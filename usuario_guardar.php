<?php
session_start();
require_once("conexion.php");
require_once("seguridad.php");

verificarSesion();
soloAdmin();

$nombre   = $_POST['nombre'];
$apellido = $_POST['apellido'];
$usuario  = $_POST['usuario'];
$clave    = $_POST['clave'];
$rol      = $_POST['rol'];

$claveHash = password_hash($clave, PASSWORD_DEFAULT);

$stmt = $conexion->prepare("
    INSERT INTO usuario (nombre, apellido, nombre_usuario, clave, rol, activo, primer_ingreso)
    VALUES (?, ?, ?, ?, ?, 1, 0)
");

$stmt->bind_param("sssss", $nombre, $apellido, $usuario, $claveHash, $rol);
$stmt->execute();

header("Location: usuarios.php");
exit();