<?php
$host = "localhost";
$user = "root";
$pass = "Milton014";
$db   = "sigenmuni4";

$conexion = new mysqli($host, $user, $pass, $db);

if ($conexion->connect_error) {
    die("Error de conexión a la base de datos: " . $conexion->connect_error);
}

$conexion->set_charset("utf8mb4");
?>