<?php
session_start();
require_once("conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: conceptos.php");
    exit();
}

$codigo = isset($_POST['codigo']) ? (int)$_POST['codigo'] : 0;
$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$categoria = isset($_POST['categoria']) ? trim($_POST['categoria']) : '';
$forma_calculo = isset($_POST['forma_calculo']) ? trim($_POST['forma_calculo']) : 'FIJO';

$porcentaje = isset($_POST['porcentaje']) ? (float)$_POST['porcentaje'] : 0;
$monto_fijo = isset($_POST['monto_fijo']) ? (float)$_POST['monto_fijo'] : 0;

$requiere_manual = isset($_POST['requiere_manual']) ? 1 : 0;
$base_calculo = isset($_POST['base_calculo']) && $_POST['base_calculo'] !== '' ? trim($_POST['base_calculo']) : null;
$orden_calculo = isset($_POST['orden_calculo']) ? (int)$_POST['orden_calculo'] : 0;
$aplica_sac = isset($_POST['aplica_sac']) ? 1 : 0;
$visible_recibo = isset($_POST['visible_recibo']) ? 1 : 0;
$activo = isset($_POST['activo']) ? 1 : 0;
$descripcion = isset($_POST['descripcion']) && $_POST['descripcion'] !== '' ? trim($_POST['descripcion']) : null;
$fecha_desde = isset($_POST['fecha_desde']) && $_POST['fecha_desde'] !== '' ? $_POST['fecha_desde'] : null;
$fecha_hasta = isset($_POST['fecha_hasta']) && $_POST['fecha_hasta'] !== '' ? $_POST['fecha_hasta'] : null;

if ($codigo <= 0 || $nombre === '' || $categoria === '') {
    die("Faltan datos obligatorios.");
}

/*
|--------------------------------------------------------------------------
| Reglas según forma de cálculo
|--------------------------------------------------------------------------
| FIJO            -> usa monto_fijo
| PORCENTAJE      -> usa porcentaje
| TABLA_CATEGORIA -> monto y porcentaje siempre en 0
| MANUAL          -> monto y porcentaje en 0
| FORMULA         -> monto y porcentaje en 0
*/
switch ($forma_calculo) {
    case 'FIJO':
        $porcentaje = 0;
        if ($monto_fijo < 0) {
            die("El monto fijo no puede ser negativo.");
        }
        break;

    case 'PORCENTAJE':
        $monto_fijo = 0;
        if ($porcentaje < 0) {
            die("El porcentaje no puede ser negativo.");
        }
        break;

    case 'TABLA_CATEGORIA':
        $porcentaje = 0;
        $monto_fijo = 0;
        break;

    case 'MANUAL':
    case 'FORMULA':
        $porcentaje = 0;
        $monto_fijo = 0;
        break;

    default:
        die("Forma de cálculo no válida.");
}

$sql = "INSERT INTO concepto (
            codigo, nombre, categoria, forma_calculo, porcentaje, monto_fijo,
            requiere_manual, base_calculo, orden_calculo, aplica_sac,
            visible_recibo, activo, descripcion, fecha_desde, fecha_hasta
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conexion->prepare($sql);

if (!$stmt) {
    die("Error al preparar consulta: " . $conexion->error);
}

$stmt->bind_param(
    "isssddisiiiisss",
    $codigo,
    $nombre,
    $categoria,
    $forma_calculo,
    $porcentaje,
    $monto_fijo,
    $requiere_manual,
    $base_calculo,
    $orden_calculo,
    $aplica_sac,
    $visible_recibo,
    $activo,
    $descripcion,
    $fecha_desde,
    $fecha_hasta
);

if ($stmt->execute()) {
    header("Location: conceptos.php?ok=1");
    exit();
} else {
    die("Error al guardar: " . $stmt->error);
}
?>