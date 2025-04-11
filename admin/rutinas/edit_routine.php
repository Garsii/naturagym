<?php
session_start();
include('../../config/db.php');

// Verificar que el usuario está autenticado y que es admin
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') {
    header('Location: ../../index.php');
    exit();
}

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $sql = "SELECT * FROM rutinas WHERE id = $id";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $rutina = $result->fetch_assoc();
    } else {
        echo "Rutina no encontrada.";
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $titulo = mysqli_real_escape_string($conn, $_POST['titulo']);
        $descripcion = mysqli_real_escape_string($conn, $_POST['descripcion']);
        $duracion = (int) $_POST['duracion'];
        $nivel = mysqli_real_escape_string($conn, $_POST['nivel']);
        $url_video = mysqli_real_escape_string($conn, $_POST['url_video']);

        if (empty($titulo) || empty($nivel)) {
            $message = "Por favor, completa todos los campos requeridos.";
        } else {
            $sql_update = "UPDATE rutinas SET titulo = '$titulo', descripcion = '$descripcion', duracion = $duracion, nivel = '$nivel', url_video = '$url_video' WHERE id = $id";
            if ($conn->query($sql_update) === TRUE) {
                $message = "Rutina actualizada correctamente.";
            } else {
                $message = "Error al actualizar la rutina: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Rutina</title>
    <link rel="stylesheet" type="text/css" href="../../public/css/style.css">
</head>
<body>
    <?php include('../../templates/header_admin.php'); ?>

    <main>
        <h2>Editar Rutina</h2>

        <?php if (!empty($message)): ?>
            <p><?= htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="titulo">Título de la Rutina:</label>
            <input type="text" name="titulo" id="titulo" value="<?= htmlspecialchars($rutina['titulo']); ?>" required><br><br>

            <label for="descripcion">Descripción:</label>
            <textarea name="descripcion" id="descripcion"><?= htmlspecialchars($rutina['descripcion']); ?></textarea><br><br>

            <label for="duracion">Duración (en minutos):</label>
            <input type="number" name="duracion" id="duracion" value="<?= $rutina['duracion']; ?>"><br><br>

            <label for="nivel">Nivel de la Rutina:</label>
            <select name="nivel" id="nivel" required>
                <option value="principiante" <?= $rutina['nivel'] == 'principiante' ? 'selected' : ''; ?>>Principiante</option>
                <option value="intermedio" <?= $rutina['nivel'] == 'intermedio' ? 'selected' : ''; ?>>Intermedio</option>
                <option value="avanzado" <?= $rutina['nivel'] == 'avanzado' ? 'selected' : ''; ?>>Avanzado</option>
            </select><br><br>

            <label for="url_video">URL del Video:</label>
            <input type="text" name="url_video" id="url_video" value="<?= htmlspecialchars($rutina['url_video']); ?>"><br><br>

            <button type="submit">Actualizar Rutina</button>
        </form>
    </main>

    <?php include('../../templates/footer.php'); ?>
</body>
</html>

<?php
$conn->close();
?>
