<?php
session_start();
require_once("conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$nombreCompleto = $_SESSION['nombre_completo'] ?? $_SESSION['usuario'];
$rol = $_SESSION['rol'] ?? 'OPERADOR';

function obtenerDato($conexion, $sql, $campo) {
    $resultado = $conexion->query($sql);
    if ($resultado && $fila = $resultado->fetch_assoc()) {
        return $fila[$campo] ?? 0;
    }
    return 0;
}

function obtenerArray($conexion, $sql, $campoLabel, $campoValor) {
    $labels = [];
    $valores = [];

    $resultado = $conexion->query($sql);

    if ($resultado) {
        while ($fila = $resultado->fetch_assoc()) {
            $labels[] = $fila[$campoLabel];
            $valores[] = (float)$fila[$campoValor];
        }
    }

    return [
        "labels" => $labels,
        "valores" => $valores
    ];
}

/* ===== TARJETAS ===== */

$totalEmpleados = obtenerDato($conexion, "SELECT COUNT(*) total FROM empleado WHERE activo=1", "total");
$totalLiquidaciones = obtenerDato($conexion, "SELECT COUNT(*) total FROM liquidacion", "total");
$totalNeto = obtenerDato($conexion, "SELECT IFNULL(SUM(neto),0) total FROM liquidacion_empleado", "total");
$netoPromedio = obtenerDato($conexion, "SELECT IFNULL(AVG(neto),0) promedio FROM liquidacion_empleado", "promedio");

/* ===== GRÁFICOS ===== */

$empleadosPorCategoria = obtenerArray($conexion, "
    SELECT IFNULL(c.nombre,'Sin categoría') categoria, COUNT(e.id) total
    FROM empleado e
    LEFT JOIN categoria c ON e.categoria_id = c.id
    WHERE e.activo=1
    GROUP BY c.nombre
", "categoria", "total");

$totalPorPeriodo = obtenerArray($conexion, "
    SELECT l.periodo, SUM(le.neto) total
    FROM liquidacion l
    JOIN liquidacion_empleado le ON l.id = le.liquidacion_id
    GROUP BY l.periodo
", "periodo", "total");

$resumenConceptos = obtenerArray($conexion, "
    SELECT c.categoria tipo, SUM(ld.monto) total
    FROM liquidacion_detalle ld
    JOIN concepto c ON ld.concepto_id = c.id
    GROUP BY c.categoria
", "tipo", "total");

$descuentos = obtenerArray($conexion, "
    SELECT c.nombre concepto, SUM(ld.monto) total
    FROM liquidacion_detalle ld
    JOIN concepto c ON ld.concepto_id = c.id
    WHERE c.categoria='DESCUENTO'
    GROUP BY c.nombre
    LIMIT 10
", "concepto", "total");

function formatoPesos($n) {
    return "$ " . number_format($n, 2, ",", ".");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Estadísticas</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
body { font-family: Arial; background:#f4f7fb; }

.header {
    background: linear-gradient(135deg,#ea580c,#fb923c);
    color:white;
    padding:20px;
}

.contenedor { width:90%; margin:25px auto; }

/* BOTONES */
.acciones {
    display:flex;
    gap:10px;
    margin-bottom:20px;
    flex-wrap:wrap;
}

.btn-pdf {
    background:#16a34a;
    color:white;
    padding:10px 15px;
    border:none;
    border-radius:8px;
    cursor:pointer;
}

.btn-volver {
    background:#374151;
    color:white;
    padding:10px 15px;
    border-radius:8px;
    text-decoration:none;
}

.btn-volver:hover {
    background:#1f2937;
}

.tarjetas {
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
    gap:15px;
    margin-bottom:25px;
}

.card {
    background:white;
    padding:20px;
    border-radius:12px;
}

.card h3 { font-size:14px; color:#6b7280; }
.card .num { font-size:22px; color:#ea580c; font-weight:bold; }

.graficos {
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(400px,1fr));
    gap:20px;
}

.grafico {
    background:white;
    padding:20px;
    border-radius:12px;
}

.grafico h3 { margin-bottom:5px; }

.grafico p {
    font-size:13px;
    color:#6b7280;
    margin-bottom:10px;
}

canvas { height:300px !important; }
</style>
</head>

<body>

<div class="header">
<h1>SIGENMUNI - Estadísticas</h1>
<p><?php echo $nombreCompleto . " | " . $rol; ?></p>
</div>

<div class="contenedor">

<div class="acciones">
    <button class="btn-pdf" onclick="exportarPDF()">📄 Exportar PDF</button>
    <a href="reportes.php" class="btn-volver">← Volver a Reportes</a>
</div>

<div class="tarjetas">
<div class="card"><h3>Empleados activos</h3><div class="num"><?php echo $totalEmpleados; ?></div></div>
<div class="card"><h3>Liquidaciones</h3><div class="num"><?php echo $totalLiquidaciones; ?></div></div>
<div class="card"><h3>Total neto</h3><div class="num"><?php echo formatoPesos($totalNeto); ?></div></div>
<div class="card"><h3>Neto promedio</h3><div class="num"><?php echo formatoPesos($netoPromedio); ?></div></div>
</div>

<div class="graficos">

<div class="grafico">
<h3>Empleados por categoría</h3>
<p>Representa la cantidad de empleados activos agrupados según su categoría laboral dentro de la municipalidad.</p>
<canvas id="cat"></canvas>
</div>

<div class="grafico">
<h3>Total por período</h3>
<p>Muestra el total de haberes netos pagados en cada período de liquidación, permitiendo analizar la evolución del gasto salarial.</p>
<canvas id="per"></canvas>
</div>

<div class="grafico">
<h3>Conceptos</h3>
<p>Distribución de los importes liquidados según el tipo de concepto: remunerativo, no remunerativo, asignaciones, descuentos y aportes.</p>
<canvas id="con"></canvas>
</div>

<div class="grafico">
<h3>Descuentos</h3>
<p>Ranking de los principales descuentos aplicados a los empleados, ordenados por el total acumulado.</p>
<canvas id="des"></canvas>
</div>

</div>

</div>

<script>

const charts = {};

charts.cat = new Chart(document.getElementById('cat'), {
    type:'bar',
    data:{ labels: <?php echo json_encode($empleadosPorCategoria["labels"]); ?>,
           datasets:[{ label:'Cantidad de empleados', data: <?php echo json_encode($empleadosPorCategoria["valores"]); ?> }] }
});

charts.per = new Chart(document.getElementById('per'), {
    type:'line',
    data:{ labels: <?php echo json_encode($totalPorPeriodo["labels"]); ?>,
           datasets:[{ label:'Total neto', data: <?php echo json_encode($totalPorPeriodo["valores"]); ?> }] }
});

charts.con = new Chart(document.getElementById('con'), {
    type:'doughnut',
    data:{ labels: <?php echo json_encode($resumenConceptos["labels"]); ?>,
           datasets:[{ label:'Conceptos', data: <?php echo json_encode($resumenConceptos["valores"]); ?> }] }
});

charts.des = new Chart(document.getElementById('des'), {
    type:'bar',
    data:{ labels: <?php echo json_encode($descuentos["labels"]); ?>,
           datasets:[{ label:'Descuentos', data: <?php echo json_encode($descuentos["valores"]); ?> }] }
});

function exportarPDF(){
    let imgs = [];
    Object.values(charts).forEach(c => imgs.push(c.toBase64Image()));

    fetch('generar_pdf_estadisticas.php',{
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body: JSON.stringify({imagenes:imgs})
    })
    .then(r=>r.blob())
    .then(blob=>window.open(URL.createObjectURL(blob)));
}

</script>

</body>
</html>