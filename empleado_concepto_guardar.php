<?php
session_start();
require_once("conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $empleado_id        = (int)($_POST['empleado_id'] ?? 0);
    $concepto_id        = (int)($_POST['concepto_id'] ?? 0);
    $monto_manual       = (float)($_POST['monto_manual'] ?? 0);
    $porcentaje_manual  = (float)($_POST['porcentaje_manual'] ?? 0);
    $cantidad           = (float)($_POST['cantidad'] ?? 1);
    $fecha_desde        = $_POST['fecha_desde'] ?? null;
    $fecha_hasta        = !empty($_POST['fecha_hasta']) ? $_POST['fecha_hasta'] : null;
    $observacion        = trim($_POST['observacion'] ?? "");
    $activo             = 1;

    if ($empleado_id <= 0 || $concepto_id <= 0 || empty($fecha_desde)) {
        die("Faltan datos obligatorios.");
    }

    $stmt = $conexion->prepare("
        INSERT INTO empleado_concepto
        (empleado_id, concepto_id, monto_manual, porcentaje_manual, cantidad, fecha_desde, fecha_hasta, activo, observacion)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    if (!$stmt) {
        die("Error en prepare: " . $conexion->error);
    }

    $stmt->bind_param(
        "iidddssis",
        $empleado_id,
        $concepto_id,
        $monto_manual,
        $porcentaje_manual,
        $cantidad,
        $fecha_desde,
        $fecha_hasta,
        $activo,
        $observacion
    );

    if ($stmt->execute()) {
        header("Location: empleado_conceptos.php?ok=1");
        exit();
    } else {
        echo "Error al guardar: " . $stmt->error;
    }

    $stmt->close();

} else {
    header("Location: empleado_conceptos.php");
    exit();
}
?>