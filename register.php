<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
include('config/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = mysqli_real_escape_string($conn, $_POST['nombre']);
    $apellido = mysqli_real_escape_string($conn, $_POST['apellido']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $rol = 'usuario'; // Por defecto, usuario
    $estado = 'activo';

    $sql = "INSERT INTO usuarios (nombre, apellido, email, password, rol, estado) 
            VALUES ('$nombre', '$apellido', '$email', '$password', '$rol', '$estado')";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: index.php?mensaje=Registro exitoso, ahora inicia sesión");
        exit();
    } else {
        echo "Error al registrar: " . $conn->error;
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - NaturoGym</title>
</head>
<body>
    <h2>Registro</h2>
    <form action="register.php" method="POST">
        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" id="nombre" required><br><br>
        <label for="apellido">Apellido:</label>
        <input type="text" name="apellido" id="apellido" required><br><br>
        <label for="email">Correo electrónico:</label>
        <input type="email" name="email" id="email" required><br><br>
        <label for="password">Contraseña:</label>
        <input type="password" name="password" id="password" required><br><br>
        <button type="submit">Registrarse</button>
    </form>
    <p>¿Ya tienes cuenta? <a href="index.php">Inicia sesión</a></p>
</body>
</html>
