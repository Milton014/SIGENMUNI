<?php
session_start();
require_once("conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    die("ID no válido.");
}

$stmt = $conexion->prepare("SELECT activo FROM empleado_concepto WHERE id = ?");
if (!$stmt) {
    die("Error en prepare: " . $conexion->error);
}

$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows == 0) {
    die("Registro no encontrado.");
}

$fila = $res->fetch_assoc();
$nuevo_estado = ((int)$fila['activo'] === 1) ? 0 : 1;

$stmt2 = $conexion->prepare("UPDATE empleado_concepto SET activo = ? WHERE id = ?");
if (!$stmt2) {
    die("Error en prepare: " . $conexion->error);
}

$stmt2->bind_param("ii", $nuevo_estado, $id);

if ($stmt2->execute()) {
    header("Location: empleado_conceptos.php?ok=3");
    exit();
} else {
    echo "Error al cambiar estado: " . $stmt2->error;
}
?>