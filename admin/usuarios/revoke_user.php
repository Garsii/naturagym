<?php
session_start();
include('../db.php'); // Conexión a la base de datos

// Verificar si el usuario está autenticado y es admin
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php'); // Si no es admin, redirigir al login
    exit();
}

// Verificar que se ha recibido un ID de usuario válido en la URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $user_id = $_GET['id'];

    // Liberar el UID asociado a ese usuario:
    // Se actualiza la tabla tarjetas para poner usuario_id = NULL y estado = 'disponible'
    $sql_update = "UPDATE tarjetas SET usuario_id = NULL, estado = 'disponible' WHERE usuario_id = $user_id";

    if ($conn->query($sql_update) === TRUE) {
        $mensaje = "Usuario revocado y UID liberado correctamente.";
    } else {
        $mensaje = "Error al revocar el usuario: " . $conn->error;
    }

    header("Location: index.php?mensaje=" . urlencode($mensaje));
    exit();
} else {
    echo "ID de usuario no válido.";
    exit();
}

$conn->close();
?>
