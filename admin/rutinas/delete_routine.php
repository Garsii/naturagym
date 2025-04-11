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
    $sql = "DELETE FROM rutinas WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        header('Location: view_routines.php');
        exit();
    } else {
        echo "Error al eliminar la rutina: " . $conn->error;
    }
}

$conn->close();
?>
