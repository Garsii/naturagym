<?php
session_start();
include('../../config/db.php');

// Verificar que el usuario está autenticado y que es admin
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') {
    header('Location: ../../index.php');
    exit();
}

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = mysqli_real_escape_string($conn, $_POST['titulo']);
    $descripcion = mysqli_real_escape_string($conn, $_POST['descripcion']);
    $duracion = (int) $_POST['duracion'];
    $nivel = mysqli_real_escape_string($conn, $_POST['nivel']);
    $url_video = mysqli_real_escape_string($conn, $_POST['url_video']);

    if (empty($titulo) || empty($nivel)) {
        $message = "Por favor, completa todos los campos requeridos.";
    } else {
        $sql = "INSERT INTO rutinas (titulo, descripcion, duracion, nivel, url_video) VALUES ('$titulo', '$descripcion', $duracion, '$nivel', '$url_video')";
        if ($conn->query($sql) === TRUE) {
            $message = "Rutina creada correctamente.";
        } else {
            $message = "Error al crear la rutina: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Rutina</title>
    <link rel="stylesheet" type="text/css" href="../../public/css/style.css">
</head>
<body>
    <?php include('../../templates/header_admin.php'); ?>

    <main>
        <h2>Crear Nueva Rutina</h2>

        <?php if (!empty($message)): ?>
            <p><?= htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="titulo">Título de la Rutina:</label>
            <input type="text" name="titulo" id="titulo" required><br><br>

            <label for="descripcion">Descripción:</label>
            <textarea name="descripcion" id="descripcion"></textarea><br><br>

            <label for="duracion">Duración (en minutos):</label>
            <input type="number" name="duracion" id="duracion"><br><br>

            <label for="nivel">Nivel de la Rutina:</label>
            <select name="nivel" id="nivel" required>
                <option value="principiante">Principiante</option>
                <option value="intermedio">Intermedio</option>
                <option value="avanzado">Avanzado</option>
            </select><br><br>

            <label for="url_video">URL del Video:</label>
            <input type="text" name="url_video" id="url_video"><br><br>

            <button type="submit">Crear Rutina</button>
        </form>
    </main>

    <?php include('../../templates/footer.php'); ?>
</body>
</html>

<?php
$conn->close();
?>
