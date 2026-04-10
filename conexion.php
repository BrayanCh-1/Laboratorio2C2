<?php
$host = "localhost";
$port = 3307;  // Hicimos cambio de puerto ya que el 3306 esta ocupado 
$user = "root";
$pass = "";
$base = "habitos_db";

$conexion = new mysqli($host, $user, $pass, $base, $port);

if ($conexion->connect_error) {
    die("Error: " . $conexion->connect_error);
}
?>