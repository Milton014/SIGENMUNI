<?php
session_start();
require_once("conexion.php");
require_once("seguridad.php");

verificarSesion();
soloAdmin();

$id = (int)$_GET['id'];

$conexion->query("
    UPDATE usuario 
    SET activo = IF(activo = 1, 0, 1)
    WHERE id = $id
");

header("Location: usuarios.php");
exit();