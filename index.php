<?php
session_start();
// Si ya está autenticado, redirigir según el rol
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin/index.php");
    } else {
        header("Location: user/index.php");
    }
    exit();
}

include('templates/header_public.php');
?>
<main>
    <h2>Iniciar Sesión</h2>
    <form action="auth.php" method="POST">
        <label for="email">Correo electrónico:</label>
        <input type="email" name="email" id="email" required><br><br>
        <label for="password">Contraseña:</label>
        <input type="password" name="password" id="password" required><br><br>
        <button type="submit">Ingresar</button>
    </form>
    <p>¿No tienes cuenta? <a href="register.php">Regístrate</a></p>
</main>
<?php include('templates/footer.php'); ?>
