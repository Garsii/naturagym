<?php
// admin/index.php
session_start();
ob_start(); // inicio del buffer para evitar problemas con header()

error_reporting(E_ALL);
ini_set('display_errors', 1);

include('../config/db.php');

// verificar que el usuario está autenticado y es admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

$message = "";

// procesar el formulario
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['usuario_id'], $_POST['uid'])) {
    $usuario_id = (int) $_POST['usuario_id'];
    $nuevo_uid = trim($_POST['uid']);

    try {
        if (empty($nuevo_uid) || $usuario_id <= 0) {
            throw new Exception("Por favor, completa todos los campos correctamente.");
        }

        $conn->begin_transaction();

        // liberar cualquier UID que ya tenga el usuario
        $stmt = $conn->prepare("UPDATE tarjetas SET usuario_id = NULL, estado = 'disponible' WHERE usuario_id = ?");
        $stmt->bind_param('i', $usuario_id);
        $stmt->execute();

        // comprobar si el UID ya existe
        $stmt = $conn->prepare("SELECT id, estado FROM tarjetas WHERE uid = ? FOR UPDATE");
        $stmt->bind_param('s', $nuevo_uid);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $tarjeta = $result->fetch_assoc();

            if ($tarjeta['estado'] !== 'disponible') {
                throw new Exception("El UID ya está asignado a otro usuario.");
            }

            $stmt = $conn->prepare("UPDATE tarjetas SET usuario_id = ?, estado = 'asignado' WHERE id = ?");
            $stmt->bind_param('ii', $usuario_id, $tarjeta['id']);
        } else {
            $stmt = $conn->prepare("INSERT INTO tarjetas (uid, usuario_id, estado) VALUES (?, ?, 'asignado')");
            $stmt->bind_param('si', $nuevo_uid, $usuario_id);
        }

        if (!$stmt->execute()) {
            throw new Exception("Error al asignar UID: " . $stmt->error);
        }

        $conn->commit();
        $message = "UID asignado correctamente.";
    } catch (Exception $e) {
        $conn->rollback();
        $message = $e->getMessage();
    }

    header("Location: index.php?mensaje=" . urlencode($message));
    exit();
}

// obtener datos para los formularios
try {
    $stmt = $conn->prepare("SELECT id, nombre, apellido FROM usuarios WHERE rol = 'usuario'");
    $stmt->execute();
    $usuarios = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $stmt = $conn->prepare("SELECT uid FROM tarjetas WHERE estado = 'disponible'");
    $stmt->execute();
    $uids = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $stmt = $conn->prepare("
        SELECT u.id, u.nombre, u.apellido, u.email, u.rol, u.estado, t.uid, t.estado AS estado_tarjeta 
        FROM usuarios u 
        LEFT JOIN tarjetas t ON u.id = t.usuario_id
    ");
    $stmt->execute();
    $registros = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    die("Error al obtener datos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración - Asignar UID</title>
    <link rel="stylesheet" type="text/css" href="/public/css/style.css">
</head>
<body>
    <?php include('../templates/header_admin.php'); ?>

    <main>
        <h2>Panel de Administración</h2>

        <?php if (isset($_GET['mensaje'])): ?>
            <p><?= htmlspecialchars($_GET['mensaje']); ?></p>
        <?php endif; ?>

        <h3>Asignar UID a un Usuario</h3>

        <?php if (!empty($usuarios)): ?>
            <form method="POST" action="">
                <label for="usuario_id">Seleccionar Usuario:</label>
                <select name="usuario_id" required>
                    <?php foreach ($usuarios as $user): ?>
                        <option value="<?= $user['id']; ?>">
                            <?= htmlspecialchars($user['nombre'] . " " . $user['apellido']); ?>
                        </option>
                    <?php endforeach; ?>
                </select><br><br>

                <label for="uid">Seleccionar UID disponible:</label>
                <select name="uid" required>
                    <?php if (!empty($uids)): ?>
                        <?php foreach ($uids as $uid): ?>
                            <option value="<?= htmlspecialchars($uid['uid']); ?>">
                                <?= htmlspecialchars($uid['uid']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="">No hay UID disponibles</option>
                    <?php endif; ?>
                </select><br><br>

                <button type="submit">Asignar UID</button>
            </form>
        <?php else: ?>
            <p>No hay usuarios registrados para asignar un UID.</p>
        <?php endif; ?>

        <h3>Lista de Usuarios y Tarjetas</h3>
        <?php if (!empty($registros)): ?>
            <table border="1" cellpadding="5">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Estado Usuario</th>
                        <th>UID</th>
                        <th>Estado de la Tarjeta</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($registros as $row): ?>
                        <tr>
                            <td><?= $row['id']; ?></td>
                            <td><?= htmlspecialchars($row['nombre'] . " " . $row['apellido']); ?></td>
                            <td><?= htmlspecialchars($row['email']); ?></td>
                            <td><?= htmlspecialchars($row['rol']); ?></td>
                            <td><?= htmlspecialchars($row['estado']); ?></td>
                            <td><?= !empty($row['uid']) ? htmlspecialchars($row['uid']) : 'Sin UID'; ?></td>
                            <td><?= isset($row['estado_tarjeta']) ? htmlspecialchars($row['estado_tarjeta']) : 'N/A'; ?></td>
                            <td>
                                <a href="usuario/edit_user.php?id=<?= $row['id']; ?>">Editar</a> | 
                                <a href="usuario/revoke_user.php?id=<?= $row['id']; ?>" onclick="return confirm('¿Seguro que deseas revocar o reactivar este usuario?');">
                                    <?= ($row['estado'] == 'revocado' || empty($row['uid'])) ? 'Activar' : 'Revocar'; ?>
                                </a> | 
                                <a href="usuario/ver_registros.php?id=<?= $row['id']; ?>">Ver registros</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No hay usuarios registrados.</p>
        <?php endif; ?>
    </main>

    <?php include('../templates/footer.php'); ?>
</body>
</html>

<?php
$conn->close();
ob_end_flush(); // finaliza el buffer y envía todo
?>
