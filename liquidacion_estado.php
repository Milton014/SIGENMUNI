<?php
session_start();
require_once("conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$accion = isset($_GET['accion']) ? trim($_GET['accion']) : '';

if ($id <= 0 || $accion === '') {
    header("Location: liquidacion.php?msg=parametros_invalidos");
    exit();
}

$stmt = $conexion->prepare("
    SELECT id, estado
    FROM liquidacion
    WHERE id = ?
    LIMIT 1
");
$stmt->bind_param("i", $id);
$stmt->execute();
$liquidacion = $stmt->get_result()->fetch_assoc();

if (!$liquidacion) {
    header("Location: liquidacion.php?msg=no_existe");
    exit();
}

$nuevoEstado = null;

if ($accion === 'anular') {
    if ($liquidacion['estado'] === 'ANULADA') {
        header("Location: liquidacion.php?msg=ya_anulada");
        exit();
    }
    $nuevoEstado = 'ANULADA';
}

if ($accion === 'reabrir') {
    if ($liquidacion['estado'] !== 'CERRADA') {
        header("Location: liquidacion.php?msg=no_reabrible");
        exit();
    }
    $nuevoEstado = 'BORRADOR';
}

if ($nuevoEstado === null) {
    header("Location: liquidacion.php?msg=accion_invalida");
    exit();
}

$stmtUpdate = $conexion->prepare("
    UPDATE liquidacion
    SET estado = ?
    WHERE id = ?
");
$stmtUpdate->bind_param("si", $nuevoEstado, $id);

if ($stmtUpdate->execute()) {
    if ($accion === 'anular') {
        header("Location: liquidacion.php?msg=anulada");
    } else {
        header("Location: liquidacion.php?msg=reabierta");
    }
    exit();
} else {
    header("Location: liquidacion.php?msg=error_estado");
    exit();
}