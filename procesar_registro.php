<?php
session_start();
include 'conexion.php';

$username = trim($_POST['username']);
$password = $_POST['password'];

if (strlen($username) < 3) {
    header("Location: registro.php?error=El usuario debe tener al menos 3 caracteres");
    exit();
}

if (strlen($password) < 4) {
    header("Location: registro.php?error=La contraseña debe tener al menos 4 caracteres");
    exit();
}

$sql = "SELECT id FROM usuarios WHERE username = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    header("Location: registro.php?error=El usuario ya existe. Elige otro nombre");
    exit();
}

$password_hash = password_hash($password, PASSWORD_DEFAULT);
$sql = "INSERT INTO usuarios (username, password) VALUES (?, ?)";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ss", $username, $password_hash);

if ($stmt->execute()) {
    header("Location: index.php?success=Registro exitoso. Ahora inicia sesión");
} else {
    header("Location: registro.php?error=Error al registrar. Intenta de nuevo");
}
exit();
?>