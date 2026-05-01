<?php
session_start();
require_once("fpdf/fpdf.php");

if (!isset($_SESSION['usuario'])) {
    die("Acceso no autorizado");
}

$nombreCompleto = $_SESSION['nombre_completo'] ?? $_SESSION['usuario'];
$rol = $_SESSION['rol'] ?? 'OPERADOR';

$data = json_decode(file_get_contents("php://input"), true);
$imagenes = $data['imagenes'] ?? [];

if (empty($imagenes)) {
    die("No se recibieron gráficos.");
}

class PDF extends FPDF
{
    public $usuario;
    public $rol;

    function Header()
    {
        $this->SetFillColor(234, 88, 12);
        $this->Rect(0, 0, 210, 28, 'F');

        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 10, 'SIGENMUNI - Reporte de Estadisticas', 0, 1, 'C');

        $this->SetFont('Arial', '', 9);
        $this->Cell(0, 7, 'Sistema de Gestion Municipal - Liquidacion de Sueldos', 0, 1, 'C');

        $this->Ln(12);
    }

    function Footer()
    {
        $this->SetY(-18);
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(0, 5, 'Generado por: ' . $this->usuario . ' | Rol: ' . $this->rol, 0, 1, 'L');
        $this->Cell(0, 5, 'Fecha: ' . date('d/m/Y H:i') . ' | Pagina ' . $this->PageNo() . '/{nb}', 0, 0, 'L');
    }

    function TituloSeccion($titulo, $descripcion)
    {
        $this->SetTextColor(31, 41, 55);
        $this->SetFont('Arial', 'B', 13);
        $this->Cell(0, 8, utf8_decode($titulo), 0, 1, 'L');

        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(90, 90, 90);
        $this->MultiCell(0, 5, utf8_decode($descripcion));

        $this->Ln(8);
    }
}

$pdf = new PDF();
$pdf->usuario = $nombreCompleto;
$pdf->rol = $rol;
$pdf->AliasNbPages();
$pdf->SetMargins(12, 35, 12);
$pdf->SetAutoPageBreak(true, 22);

$titulos = [
    "Empleados por categoria",
    "Total por periodo",
    "Conceptos",
    "Descuentos"
];

$descripciones = [
    "Representa la cantidad de empleados activos agrupados segun su categoria laboral dentro de la municipalidad.",
    "Muestra el total de haberes netos pagados en cada periodo de liquidacion, permitiendo analizar la evolucion del gasto salarial.",
    "Distribucion de los importes liquidados segun el tipo de concepto: remunerativo, no remunerativo, asignaciones, descuentos y aportes.",
    "Ranking de los principales descuentos aplicados a los empleados, ordenados por el total acumulado."
];

for ($i = 0; $i < count($imagenes); $i++) {

    $pdf->AddPage();

    $pdf->TituloSeccion($titulos[$i] ?? "Grafico", $descripciones[$i] ?? "");

    $imgBase64 = str_replace('data:image/png;base64,', '', $imagenes[$i]);
    $imgBase64 = str_replace(' ', '+', $imgBase64);

    $archivoTemporal = tempnam(sys_get_temp_dir(), 'grafico_') . '.png';
    file_put_contents($archivoTemporal, base64_decode($imgBase64));

    $yImagen = $pdf->GetY() + 5;

    if (file_exists($archivoTemporal)) {
        $pdf->Image($archivoTemporal, 15, $yImagen, 180);
        unlink($archivoTemporal);
    }
}

$pdf->Output('I', 'estadisticas_sigenmuni.pdf');
exit;