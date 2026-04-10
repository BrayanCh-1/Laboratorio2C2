<?php
session_start();
if (isset($_SESSION['usuario_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - Control de Hábitos</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <div class="container">
        <h1>Control de Hábitos Diarios</h1>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="error"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="success"><?php echo htmlspecialchars($_GET['success']); ?></div>
        <?php endif; ?>
        
        <div class="form-container">
            <h2>Registrar Nuevo Usuario</h2>
            <form action="procesar_registro.php" method="POST">
                <input type="text" name="username" placeholder="Usuario (mínimo 3 caracteres)" required>
                <input type="password" name="password" placeholder="Contraseña (mínimo 4 caracteres)" required>
                <button type="submit">Registrarse</button>
            </form>
            <p class="registro-link">¿Ya tienes cuenta? <a href="index.php">Inicia sesión aquí</a></p>
        </div>
    </div>
</body>
</html>