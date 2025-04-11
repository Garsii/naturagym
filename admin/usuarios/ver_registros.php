<?php
session_start();
include('../db.php'); // Conexión a la base de datos

// Verificar si el usuario está autenticado y es admin
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php'); 
    exit();
}

// Consulta que une usuarios, tarjetas y registros (incluso si no hay UID o registros)
$sql = "SELECT 
            u.id AS user_id, 
            u.nombre, 
            u.apellido, 
            u.email, 
            t.uid, 
            r.acceso, 
            r.fecha 
        FROM usuarios u 
        LEFT JOIN tarjetas t ON u.id = t.usuario_id 
        LEFT JOIN registros r ON t.uid = r.uid 
        ORDER BY r.fecha DESC";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registros de Acceso de Todos los Usuarios</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Registros de Acceso de Todos los Usuarios</h2>
    
    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID Usuario</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>UID</th>
                    <th>Acceso</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['user_id']; ?></td>
                        <td><?= htmlspecialchars($row['nombre'] . " " . $row['apellido']); ?></td>
                        <td><?= htmlspecialchars($row['email']); ?></td>
                        <td><?= !empty($row['uid']) ? htmlspecialchars($row['uid']) : 'No asignado'; ?></td>
                        <td><?= !empty($row['acceso']) ? htmlspecialchars(ucfirst($row['acceso'])) : 'N/A'; ?></td>
                        <td><?= !empty($row['fecha']) ? htmlspecialchars($row['fecha']) : 'N/A'; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No se encontraron registros de acceso.</p>
    <?php endif; ?>
    
    <p><a href="index.php">Volver al panel de administración</a></p>
</body>
</html>
<?php $conn->close(); ?>
