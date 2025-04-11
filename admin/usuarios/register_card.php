<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include('db.php');  // Conexión a la base de datos

// Verificar que se recibió un UID en la URL
if (isset($_GET['uid'])) {
    $uid = mysqli_real_escape_string($conn, $_GET['uid']);

    // Verificar si el UID ya está registrado
    $sql = "SELECT id FROM tarjetas WHERE uid = '$uid'";
    $result = $conn->query($sql);
    
    if ($result->num_rows == 0) {
        // Insertar la tarjeta, sin asignar usuario (usuario_id = NULL)
        $sql_insert = "INSERT INTO tarjetas (uid, usuario_id) VALUES ('$uid', NULL)";
        if ($conn->query($sql_insert) === TRUE) {
            echo "Tarjeta registrada correctamente";
        } else {
            echo "Error al registrar la tarjeta: " . $conn->error;
        }
    } else {
        echo "La tarjeta ya está registrada";
    }
} else {
    echo "No se proporcionó UID";
}
$conn->close();
?>
