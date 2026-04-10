<?php
session_start();
include 'conexion.php';

$username = $_POST['username'];
$password = $_POST['password'];

$sql = "SELECT id, username, password FROM usuarios WHERE username = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows == 1) {
    $usuario = $resultado->fetch_assoc();
    if (password_verify($password, $usuario['password'])) {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nombre'] = $usuario['username'];
        header("Location: dashboard.php");
        exit();
    } else {
        header("Location: index.php?error=Contraseña incorrecta");
        exit();
    }
} else {
    header("Location: index.php?error=Usuario no encontrado");
    exit();
}
?>