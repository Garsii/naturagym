<?php
include('../config/session_check.php');
if ($_SESSION['role'] !== 'usuario') {
    header("Location: ../index.php");
    exit();
}
include('../templates/header_user.php');
?>
<main>
    <h2>Bienvenido, <?= htmlspecialchars($_SESSION['nombre'] . " " . $_SESSION['apellido']); ?></h2>
    <p>Aquí verás tus módulos: rutinas, dietas, productos y reservas.</p>
    <!-- Agrega enlaces o secciones adicionales según las funcionalidades -->
</main>
<?php include('../templates/footer.php'); ?>
