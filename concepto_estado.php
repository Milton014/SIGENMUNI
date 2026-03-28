<?php
session_start();
require_once("conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$estado = isset($_GET['estado']) ? (int)$_GET['estado'] : -1;

if ($id <= 0 || ($estado !== 0 && $estado !== 1)) {
    die("Parámetros inválidos.");
}

$stmt = $conexion->prepare("UPDATE concepto SET activo = ? WHERE id = ?");
$stmt->bind_param("ii", $estado, $id);

if ($stmt->execute()) {
    header("Location: conceptos.php?ok=3");
    exit();
} else {
    die("Error al cambiar estado: " . $stmt->error);
}
?>