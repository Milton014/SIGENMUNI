<?php
session_start();
require_once("conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: concepto_valores.php");
    exit();
}

$id           = (int)($_POST['id'] ?? 0);
$concepto_id  = (int)($_POST['concepto_id'] ?? 0);
$categoria_id = !empty($_POST['categoria_id']) ? (int)$_POST['categoria_id'] : null;
$escalafon_id = !empty($_POST['escalafon_id']) ? (int)$_POST['escalafon_id'] : null;
$monto        = (float)($_POST['monto'] ?? 0);
$porcentaje   = (float)($_POST['porcentaje'] ?? 0);
$fecha_desde  = $_POST['fecha_desde'] ?? null;
$fecha_hasta  = !empty($_POST['fecha_hasta']) ? $_POST['fecha_hasta'] : null;

if ($id <= 0 || $concepto_id <= 0 || empty($fecha_desde)) {
    die("Faltan datos obligatorios.");
}

/*
|--------------------------------------------------------------------------
| Buscar forma de cálculo del concepto
|--------------------------------------------------------------------------
*/
$stmtConcepto = $conexion->prepare("
    SELECT id, nombre, forma_calculo
    FROM concepto
    WHERE id = ? AND activo = 1
");

if (!$stmtConcepto) {
    die("Error al preparar consulta del concepto: " . $conexion->error);
}

$stmtConcepto->bind_param("i", $concepto_id);
$stmtConcepto->execute();
$resConcepto = $stmtConcepto->get_result();

if ($resConcepto->num_rows === 0) {
    die("El concepto no existe o está inactivo.");
}

$concepto = $resConcepto->fetch_assoc();
$forma_calculo = $concepto['forma_calculo'];

$stmtConcepto->close();

/*
|--------------------------------------------------------------------------
| Validaciones según forma de cálculo
|--------------------------------------------------------------------------
*/
switch ($forma_calculo) {
    case 'TABLA_CATEGORIA':
        if (empty($categoria_id) || $categoria_id <= 0) {
            die("Debe seleccionar una categoría para este concepto.");
        }

        if ($monto <= 0) {
            die("Debe ingresar un monto mayor a cero.");
        }

        $porcentaje = 0;
        $escalafon_id = null;
        break;

    case 'FIJO':
        if ($monto <= 0) {
            die("Debe ingresar un monto mayor a cero.");
        }

        $porcentaje = 0;
        break;

    case 'PORCENTAJE':
        if ($porcentaje <= 0) {
            die("Debe ingresar un porcentaje mayor a cero.");
        }

        $monto = 0;
        break;

    case 'MANUAL':
    case 'FORMULA':
        die("Este tipo de concepto no admite carga de valores desde esta pantalla.");

    default:
        die("Forma de cálculo no válida.");
}

/*
|--------------------------------------------------------------------------
| Validación de fechas
|--------------------------------------------------------------------------
*/
if (!empty($fecha_hasta) && $fecha_hasta < $fecha_desde) {
    die("La fecha hasta no puede ser menor que la fecha desde.");
}

/*
|--------------------------------------------------------------------------
| Evitar duplicados exactos al actualizar
|--------------------------------------------------------------------------
*/
$stmtExiste = $conexion->prepare("
    SELECT id
    FROM concepto_valor
    WHERE concepto_id = ?
      AND ((categoria_id = ?) OR (categoria_id IS NULL AND ? IS NULL))
      AND ((escalafon_id = ?) OR (escalafon_id IS NULL AND ? IS NULL))
      AND fecha_desde = ?
      AND activo = 1
      AND id <> ?
    LIMIT 1
");

if (!$stmtExiste) {
    die("Error al validar duplicados: " . $conexion->error);
}

$stmtExiste->bind_param(
    "iiiiisi",
    $concepto_id,
    $categoria_id,
    $categoria_id,
    $escalafon_id,
    $escalafon_id,
    $fecha_desde,
    $id
);

$stmtExiste->execute();
$resExiste = $stmtExiste->get_result();

if ($resExiste->num_rows > 0) {
    die("Ya existe un valor activo para ese concepto con la misma categoría/escalafón y fecha desde.");
}

$stmtExiste->close();

/*
|--------------------------------------------------------------------------
| Actualizar
|--------------------------------------------------------------------------
*/
$stmt = $conexion->prepare("
    UPDATE concepto_valor
    SET concepto_id = ?, categoria_id = ?, escalafon_id = ?, monto = ?, porcentaje = ?, fecha_desde = ?, fecha_hasta = ?
    WHERE id = ?
");

if (!$stmt) {
    die("Error en prepare: " . $conexion->error);
}

$stmt->bind_param(
    "iiiddssi",
    $concepto_id,
    $categoria_id,
    $escalafon_id,
    $monto,
    $porcentaje,
    $fecha_desde,
    $fecha_hasta,
    $id
);

if ($stmt->execute()) {
    header("Location: concepto_valores.php?concepto_id=" . $concepto_id . "&ok=2");
    exit();
} else {
    die("Error al actualizar: " . $stmt->error);
}
?>