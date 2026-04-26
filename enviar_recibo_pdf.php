<?php
session_start();
require_once("conexion.php");
require_once("fpdf/fpdf.php");

// PHPMailer sin Composer
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$liquidacionId = isset($_GET['liquidacion_id']) ? (int)$_GET['liquidacion_id'] : 0;
$empleadoId    = isset($_GET['empleado_id']) ? (int)$_GET['empleado_id'] : 0;

if ($liquidacionId <= 0 || $empleadoId <= 0) {
    die("Parámetros inválidos.");
}

function dinero($valor) {
    return "$ " . number_format((float)$valor, 2, ',', '.');
}

function antiguedadTexto($fechaAlta) {
    if (empty($fechaAlta) || $fechaAlta === '0000-00-00') {
        return "0 años";
    }

    try {
        $inicio = new DateTime($fechaAlta);
        $hoy = new DateTime();
        $diff = $inicio->diff($hoy);
        return $diff->y . " años";
    } catch (Exception $e) {
        return "0 años";
    }
}

$stmt = $conexion->prepare("
    SELECT 
        l.id AS liquidacion_id,
        l.tipo_liquidacion,
        l.periodo,
        l.fecha_liquidacion,
        e.id AS empleado_id,
        e.nro_legajo,
        e.apellido,
        e.nombre,
        e.email,
        e.fecha_alta,
        c.codigo AS categoria_codigo,
        c.nombre AS categoria_nombre
    FROM liquidacion l
    INNER JOIN liquidacion_empleado le ON le.liquidacion_id = l.id
    INNER JOIN empleado e ON le.empleado_id = e.id
    LEFT JOIN categoria c ON e.categoria_id = c.id
    WHERE l.id = ? AND e.id = ?
    LIMIT 1
");
$stmt->bind_param("ii", $liquidacionId, $empleadoId);
$stmt->execute();
$datos = $stmt->get_result()->fetch_assoc();

if (!$datos) {
    die("No se encontró el recibo.");
}

if (empty($datos['email'])) {
    die("El empleado no tiene email cargado.");
}

$stmtDetalle = $conexion->prepare("
    SELECT 
        ld.cantidad,
        ld.porcentaje_aplicado,
        ld.monto,
        c.codigo,
        c.nombre
    FROM liquidacion_detalle ld
    INNER JOIN concepto c ON ld.concepto_id = c.id
    WHERE ld.liquidacion_id = ? AND ld.empleado_id = ?
    ORDER BY CAST(c.codigo AS UNSIGNED) ASC
");
$stmtDetalle->bind_param("ii", $liquidacionId, $empleadoId);
$stmtDetalle->execute();
$resDetalle = $stmtDetalle->get_result();

$haberesRem = [];
$haberesNoRem = [];
$asignaciones = [];
$descuentos = [];

$totalHaberesRem = 0;
$totalHaberesNoRem = 0;
$totalAsignaciones = 0;
$totalDescuentos = 0;

while ($row = $resDetalle->fetch_assoc()) {
    $codigo = (int)$row['codigo'];
    $monto = (float)$row['monto'];

    if ($codigo >= 101 && $codigo <= 199) {
        if ($codigo == 112) {
            $haberesNoRem[] = $row;
            $totalHaberesNoRem += $monto;
        } else {
            $haberesRem[] = $row;
            $totalHaberesRem += $monto;
        }
    } elseif ($codigo >= 201 && $codigo <= 299) {
        $asignaciones[] = $row;
        $totalAsignaciones += $monto;
    } elseif ($codigo >= 301 && $codigo <= 399) {
        $descuentos[] = $row;
        $totalDescuentos += $monto;
    }
}

$neto = ($totalHaberesRem + $totalHaberesNoRem + $totalAsignaciones) - $totalDescuentos;

class PDFRecibo extends FPDF {
    function tituloSeccion($titulo) {
        $this->Ln(4);
        $this->SetFont('Arial', 'B', 11);
        $this->SetFillColor(230, 245, 242);
        $this->Cell(190, 8, utf8_decode($titulo), 1, 1, 'L', true);
    }

    function filaTabla($codigo, $concepto, $cantidad, $porcentaje, $monto) {
        $this->SetFont('Arial', '', 9);
        $this->Cell(22, 7, $codigo, 1, 0, 'C');
        $this->Cell(78, 7, utf8_decode($concepto), 1, 0, 'L');
        $this->Cell(25, 7, number_format((float)$cantidad, 2, ',', '.'), 1, 0, 'R');
        $this->Cell(25, 7, number_format((float)$porcentaje, 2, ',', '.'), 1, 0, 'R');
        $this->Cell(40, 7, dinero($monto), 1, 1, 'R');
    }

    function totalTabla($texto, $monto) {
        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(245, 245, 245);
        $this->Cell(150, 7, utf8_decode($texto), 1, 0, 'R', true);
        $this->Cell(40, 7, dinero($monto), 1, 1, 'R', true);
    }

    function cabeceraTabla() {
        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(243, 244, 246);
        $this->Cell(22, 7, utf8_decode('Código'), 1, 0, 'C', true);
        $this->Cell(78, 7, 'Concepto', 1, 0, 'C', true);
        $this->Cell(25, 7, 'Cantidad', 1, 0, 'C', true);
        $this->Cell(25, 7, '%', 1, 0, 'C', true);
        $this->Cell(40, 7, 'Monto', 1, 1, 'C', true);
    }
}

$pdf = new PDFRecibo();
$pdf->AddPage();
$pdf->SetMargins(10, 10, 10);

if (file_exists("img/escudo.jpg")) {
    $pdf->Image("img/escudo.jpg", 170, 12, 25);
}

$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(190, 8, 'RECIBO DE SUELDO', 0, 1, 'C');

$pdf->SetFont('Arial', '', 11);
$pdf->Cell(190, 6, utf8_decode('Municipalidad de Fortín Lugones'), 0, 1, 'C');
$pdf->Cell(
    190,
    6,
    utf8_decode('Liquidación: ' . $datos['tipo_liquidacion'] . ' | Período: ' . $datos['periodo']),
    0,
    1,
    'C'
);

$pdf->Ln(8);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(95, 8, 'Empleado', 1, 0, 'L');
$pdf->Cell(95, 8, 'Legajo', 1, 1, 'L');

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(95, 8, utf8_decode($datos['apellido'] . ', ' . $datos['nombre']), 1, 0, 'L');
$pdf->Cell(95, 8, utf8_decode($datos['nro_legajo']), 1, 1, 'L');

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(95, 8, utf8_decode('Categoría'), 1, 0, 'L');
$pdf->Cell(95, 8, 'Fecha de Alta', 1, 1, 'L');

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(95, 8, utf8_decode(($datos['categoria_codigo'] ?? '-') . ' - ' . ($datos['categoria_nombre'] ?? '-')), 1, 0, 'L');
$pdf->Cell(95, 8, date("d/m/Y", strtotime($datos['fecha_alta'])), 1, 1, 'L');

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(95, 8, utf8_decode('Antigüedad'), 1, 0, 'L');
$pdf->Cell(95, 8, utf8_decode('Fecha de Liquidación'), 1, 1, 'L');

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(95, 8, utf8_decode(antiguedadTexto($datos['fecha_alta'])), 1, 0, 'L');
$pdf->Cell(95, 8, date("d/m/Y", strtotime($datos['fecha_liquidacion'])), 1, 1, 'L');

$pdf->tituloSeccion('Haberes Remunerativos');
$pdf->cabeceraTabla();
if (count($haberesRem) > 0) {
    foreach ($haberesRem as $item) {
        $pdf->filaTabla($item['codigo'], $item['nombre'], $item['cantidad'], $item['porcentaje_aplicado'], $item['monto']);
    }
}
$pdf->totalTabla('Total Haberes Remunerativos', $totalHaberesRem);

$pdf->tituloSeccion('Haberes No Remunerativos');
$pdf->cabeceraTabla();
if (count($haberesNoRem) > 0) {
    foreach ($haberesNoRem as $item) {
        $pdf->filaTabla($item['codigo'], $item['nombre'], $item['cantidad'], $item['porcentaje_aplicado'], $item['monto']);
    }
}
$pdf->totalTabla('Total Haberes No Remunerativos', $totalHaberesNoRem);

$pdf->tituloSeccion('Asignaciones Familiares');
$pdf->cabeceraTabla();
if (count($asignaciones) > 0) {
    foreach ($asignaciones as $item) {
        $pdf->filaTabla($item['codigo'], $item['nombre'], $item['cantidad'], $item['porcentaje_aplicado'], $item['monto']);
    }
}
$pdf->totalTabla('Total Asignaciones', $totalAsignaciones);

$pdf->tituloSeccion('Descuentos');
$pdf->cabeceraTabla();
if (count($descuentos) > 0) {
    foreach ($descuentos as $item) {
        $pdf->filaTabla($item['codigo'], $item['nombre'], $item['cantidad'], $item['porcentaje_aplicado'], $item['monto']);
    }
}
$pdf->totalTabla('Total Descuentos', $totalDescuentos);

$pdf->Ln(6);
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(220, 252, 231);

$pdf->Cell(95, 8, 'Total Remunerativo: ' . dinero($totalHaberesRem), 1, 0, 'L');
$pdf->Cell(95, 8, 'Total No Remunerativo: ' . dinero($totalHaberesNoRem), 1, 1, 'L');

$pdf->Cell(95, 8, 'Total Asignaciones: ' . dinero($totalAsignaciones), 1, 0, 'L');
$pdf->Cell(95, 8, 'Total Descuentos: ' . dinero($totalDescuentos), 1, 1, 'L');

$pdf->Cell(190, 10, 'NETO A COBRAR: ' . dinero($neto), 1, 1, 'C', true);

$pdf->Ln(20);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(63, 8, '_________________________', 0, 0, 'C');
$pdf->Cell(63, 8, '_________________________', 0, 0, 'C');
$pdf->Cell(63, 8, '_________________________', 0, 1, 'C');

$pdf->Cell(63, 6, 'Firma del Empleado', 0, 0, 'C');
$pdf->Cell(63, 6, utf8_decode('Tesorería'), 0, 0, 'C');
$pdf->Cell(63, 6, 'Autoridad Municipal', 0, 1, 'C');

$carpetaTemp = "temp_recibos";
if (!is_dir($carpetaTemp)) {
    mkdir($carpetaTemp, 0777, true);
}

$nombreArchivo = "recibo_" . $datos['nro_legajo'] . "_" . $datos['periodo'] . ".pdf";
$rutaPDF = $carpetaTemp . "/" . $nombreArchivo;

$pdf->Output("F", $rutaPDF);

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;

    // CAMBIAR ESTOS DATOS
    $mail->Username   = 'chavezmilton082@gmail.com';
    $mail->Password   = 'hemgotgudzvcepch'; // la que generaste

    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->CharSet = 'UTF-8';

    $mail->setFrom('chavezmilton082@gmail.com', 'Municipalidad de Fortín Lugones');
    $mail->addAddress($datos['email'], $datos['apellido'] . ' ' . $datos['nombre']);

    $mail->isHTML(true);
    $mail->Subject = 'Recibo de Sueldo - ' . $datos['periodo'];

    $mail->Body = "
        <p>Estimado/a <strong>{$datos['nombre']} {$datos['apellido']}</strong>:</p>
        <p>Se adjunta su recibo de sueldo correspondiente al período <strong>{$datos['periodo']}</strong>.</p>
        <p>Saludos cordiales.</p>
        <p><strong>Municipalidad de Fortín Lugones</strong></p>
    ";

    $mail->AltBody = "Se adjunta su recibo de sueldo correspondiente al período " . $datos['periodo'];

    $mail->addAttachment($rutaPDF, $nombreArchivo);
    $mail->send();

    if (file_exists($rutaPDF)) {
        unlink($rutaPDF);
    }

    echo "<script>
        alert('Recibo enviado correctamente al email del empleado.');
        window.location.href='recibo_sueldo.php?liquidacion_id=$liquidacionId&empleado_id=$empleadoId';
    </script>";

} catch (Exception $e) {
    if (file_exists($rutaPDF)) {
        unlink($rutaPDF);
    }

    echo 'Error al enviar el correo: ' . $mail->ErrorInfo;
}
?>