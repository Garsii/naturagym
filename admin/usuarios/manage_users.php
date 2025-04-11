<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../index.php");
    exit();
}
include('../db.php');

// Obtener la lista de usuarios
$sql = "SELECT id, nombre, apellidos, email, rol FROM usuarios";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestión de Usuarios - Panel Admin</title>
</head>
<body>
  <h2>Gestión de Usuarios</h2>
  <table border="1" cellpadding="5">
    <tr>
      <th>ID</th>
      <th>Nombre</th>
      <th>Apellidos</th>
      <th>Email</th>
      <th>Rol</th>
      <th>Acciones</th>
    </tr>
    <?php while ($usuario = $result->fetch_assoc()) { ?>
    <tr>
      <td><?php echo $usuario['id']; ?></td>
      <td><?php echo $usuario['nombre']; ?></td>
      <td><?php echo $usuario['apellidos']; ?></td><td><?php echo $usuario['email']; ?></td>
      <td><?php echo $usuario['rol']; ?></td>
      <td>
        <!-- Enlaces a acciones: editar usuario, revocar acceso, asignar tarjeta NFC ->
        <a href="edit_user.php?id=<?php echo $usuario['id']; ?>">Editar</a> |
        <a href="revoke_user.php?id=<?php echo $usuario['id']; ?>" onclick="return con>
      </td>
    </tr>
    <?php } ?>
  </table>
  <p><a href="index.php">Volver al panel de administración</a></p>
</body>
</html>
<?php $conn->close(); ?>
