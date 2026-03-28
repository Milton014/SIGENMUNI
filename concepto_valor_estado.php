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

$stmt = $conexion->prepare("SELECT activo, concepto_id FROM concepto_valor WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows == 0) {
    die("Registro no encontrado.");
}

$fila = $res->fetch_assoc();
$nuevo_estado = ($fila['activo'] == 1) ? 0 : 1;
$concepto_id = (int)$fila['concepto_id'];

$stmt2 = $conexion->prepare("UPDATE concepto_valor SET activo = ? WHERE id = ?");
$stmt2->bind_param("ii", $nuevo_estado, $id);

if ($stmt2->execute()) {
    header("Location: concepto_valores.php?concepto_id=" . $concepto_id . "&ok=1");
    exit();
} else {
    echo "Error al cambiar estado: " . $stmt2->error;
}
?>