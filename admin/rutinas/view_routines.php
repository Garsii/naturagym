<?php
session_start();
include('../../config/db.php');

// Verificar que el usuario está autenticado y que es admin
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') {
    header('Location: ../../index.php');
    exit();
}

$sql = "SELECT * FROM rutinas";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ver Rutinas</title>
    <link rel="stylesheet" type="text/css" href="../../public/css/style.css">
</head>
<body>
    <?php include('../../templates/header_admin.php'); ?>

    <main>
        <h2>Rutinas Disponibles</h2>

        <?php if ($result && $result->num_rows > 0): ?>
            <table border="1" cellpadding="5">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Descripción</th>
                        <th>Duración (min)</th>
                        <th>Nivel</th>
                        <th>URL Video</th>
                        <th>Fecha de Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id']; ?></td>
                            <td><?= htmlspecialchars($row['titulo']); ?></td>
                            <td><?= htmlspecialchars($row['descripcion']); ?></td>
                            <td><?= $row['duracion']; ?></td>
                            <td><?= htmlspecialchars($row['nivel']); ?></td>
                            <td><?= htmlspecialchars($row['url_video']); ?></td>
                            <td><?= $row['fecha_registro']; ?></td>
                            <td>
                                <a href="edit_routine.php?id=<?= $row['id']; ?>">Editar</a> | 
                                <a href="delete_routine.php?id=<?= $row['id']; ?>" onclick="return confirm('¿Seguro que deseas eliminar esta rutina?');">Eliminar</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No hay rutinas disponibles.</p>
        <?php endif; ?>
    </main>

    <?php include('../../templates/footer.php'); ?>
</body>
</html>

<?php
$conn->close();
?>
