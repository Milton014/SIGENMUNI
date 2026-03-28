<?php
session_start();
require_once("conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$id = (int)($_GET['id'] ?? 0);
$accion = $_GET['accion'] ?? '';

if ($id > 0) {
    if ($accion === 'activar') {
        $activo = 1;
    } elseif ($accion === 'inactivar') {
        $activo = 0;
    } else {
        header("Location: empleados.php");
        exit();
    }

    $stmt = $conexion->prepare("UPDATE empleado SET activo = ? WHERE id = ?");
    $stmt->bind_param("ii", $activo, $id);
    $stmt->execute();
    $stmt->close();
}

header("Location: empleados.php");
exit();