<?php
$servername = "localhost";
$username = "naturagym";
$password = "admin1357";
$database = "naturagym";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
