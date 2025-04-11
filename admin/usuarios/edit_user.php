<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
include('../db.php'); // Conexión a la base de datos

// Verificar si el usuario está autenticado y es admin
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header('Location: ../index.php'); // Si no es admin, redirigir al login
    exit();
}

// Verificar que se ha recibido el ID del usuario a editar
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $user_id = $_GET['id'];

    // Consultar los datos del usuario
    $sql = "SELECT * FROM usuarios WHERE id = $user_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        echo "Usuario no encontrado.";
        exit();
    }
} else {
    echo "ID de usuario no válido.";
    exit();
}

// Procesar el formulario de edición
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['nombre'], $_POST['apellido'], $_POST['email'], $_POST['rol'])) {
        // Sanitizar los valores recibidos
        $nombre = mysqli_real_escape_string($conn, $_POST['nombre']);
        $apellido = mysqli_real_escape_string($conn, $_POST['apellido']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $rol = mysqli_real_escape_string($conn, $_POST['rol']);

        // Actualizar los datos del usuario
        $sql_update = "UPDATE usuarios SET nombre = '$nombre', apellido = '$apellido', email = '$email', rol = '$rol' WHERE id = $user_id";

        if ($conn->query($sql_update) === TRUE) {
            // Redirigir al index de administración después de actualizar
            header("Location: index.php?mensaje=Usuario actualizado correctamente");
            exit();
        } else {
            echo "Error al actualizar el usuario: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>
</head>
<body>
    <h2>Editar Usuario</h2>

    <!-- Formulario para editar el usuario -->
    <form method="POST">
        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" value="<?= htmlspecialchars($user['nombre']); ?>" required><br><br>

        <label for="apellido">Apellidos:</label>
        <input type="text" name="apellido" value="<?= htmlspecialchars($user['apellido']); ?>" required><br><br>

        <label for="email">Correo Electrónico:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" required><br><br>

        <label for="rol">Rol:</label>
        <select name="rol" required>
            <option value="usuario" <?= $user['rol'] == 'usuario' ? 'selected' : ''; ?>>Usuario</option>
            <option value="admin" <?= $user['rol'] == 'admin' ? 'selected' : ''; ?>>Administrador</option>
        </select><br><br>

        <button type="submit">Guardar cambios</button>
    </form>
</body>
</html>

<?php $conn->close(); ?>
