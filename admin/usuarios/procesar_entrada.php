<?php
include('../db.php');

$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (isset($data['uid']) && isset($data['punto_id'])) {
    $uid = mysqli_real_escape_string($conn, $data['uid']);
    $punto_id = intval($data['punto_id']);

    // Buscar el usuario asociado a la tarjeta
    $sql = "SELECT u.id, u.estado FROM usuarios u 
            JOIN tarjetas t ON u.id = t.usuario_id
            WHERE t.uid = '$uid' LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        if ($user['estado'] === 'activo') {
            // Registrar acceso permitido
            $sql_insert = "INSERT INTO registros (uid, acceso, punto_id) 
                           VALUES ('$uid', 'permitido', '$punto_id')";
            $conn->query($sql_insert);
            echo json_encode(["acceso" => "permitido"]);
        } else {
            // Usuario revocado
            echo json_encode(["acceso" => "denegado"]);
        }
    } else {
        // UID no registrado
        echo json_encode(["acceso" => "denegado"]);
    }
} else {
    echo json_encode(["error" => "Datos incompletos"]);
}

$conn->close();
?>
