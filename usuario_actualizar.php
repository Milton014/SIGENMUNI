<?php
session_start();
require_once("conexion.php");
require_once("seguridad.php");

verificarSesion();
soloAdmin();

$id       = (int)($_POST['id'] ?? 0);
$nombre   = trim($_POST['nombre'] ?? '');
$apellido = trim($_POST['apellido'] ?? '');
$email    = trim($_POST['email'] ?? '');
$rol      = $_POST['rol'] ?? 'OPERADOR';
$clave    = trim($_POST['clave'] ?? '');

if ($id <= 0 || empty($nombre) || empty($apellido)) {
    header("Location: usuarios.php");
    exit();
}

if (!empty($clave)) {

    $claveHash = password_hash($clave, PASSWORD_DEFAULT);

    $stmt = $conexion->prepare("
        UPDATE usuario 
        SET nombre = ?, apellido = ?, email = ?, rol = ?, clave = ?
        WHERE id = ?
    ");

    $stmt->bind_param("sssssi", $nombre, $apellido, $email, $rol, $claveHash, $id);

} else {

    $stmt = $conexion->prepare("
        UPDATE usuario 
        SET nombre = ?, apellido = ?, email = ?, rol = ?
        WHERE id = ?
    ");

    $stmt->bind_param("ssssi", $nombre, $apellido, $email, $rol, $id);
}

$stmt->execute();
$stmt->close();

header("Location: usuarios.php");
exit();
?>