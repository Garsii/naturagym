<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
include('config/db.php');

// Si ya está autenticado, redirigir inmediatamente
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin/index.php");
    } else {
        header("Location: user/index.php");
    }
    exit();
}

if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    
    // Consulta para obtener el usuario activo con ese email
    $sql = "SELECT * FROM usuarios WHERE email = '$email' AND estado = 'activo'";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Establecer las variables de sesión
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['rol'];
            $_SESSION['nombre'] = $user['nombre'];
            $_SESSION['apellido'] = $user['apellido'];
            $_SESSION['puesto_id'] = $user['puesto_id'];
            
            if ($user['rol'] === 'admin') {
                header("Location: admin/index.php");
            } else {
                header("Location: user/index.php");
            }
            exit();
        } else {
            echo "Credenciales incorrectas.";
        }
    } else {
        echo "Usuario no encontrado o inactivo.";
    }
} else {
    echo "Por favor, completa todos los campos.";
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - NaturoGym</title>
</head>
<body>
    <h2>Iniciar Sesión</h2>
    <form action="auth.php" method="POST">
        <label for="email">Correo electrónico:</label>
        <input type="email" name="email" id="email" required><br><br>
        <label for="password">Contraseña:</label>
        <input type="password" name="password" id="password" required><br><br>
        <button type="submit">Ingresar</button>
    </form>
</body>
</html>
