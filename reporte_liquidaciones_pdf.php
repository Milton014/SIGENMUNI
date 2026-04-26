<?php
session_start();
require_once("conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$periodo = trim($_GET['periodo'] ?? "");
$estado  = trim($_GET['estado'] ?? "");

$sql = "
    SELECT 
        l.id,
        l.tipo_liquidacion,
        l.periodo,
        l.fecha_liquidacion,
        l.estado,
        COUNT(le.empleado_id) AS cantidad_empleados,
        COALESCE(SUM(le.total_remunerativo),0) AS total_remunerativo,
        COALESCE(SUM(le.total_descuentos),0) AS total_descuentos,
        COALESCE(SUM(le.total_no_remunerativo),0) AS total_no_remunerativo,
        COALESCE(SUM(le.total_asignaciones),0) AS total_asignaciones,
        COALESCE(SUM(le.neto),0) AS total_neto
    FROM liquidacion l
    LEFT JOIN liquidacion_empleado le ON l.id = le.liquidacion_id
    WHERE 1=1
";

$params = [];
$types = "";

if ($periodo !== "") {
    $sql .= " AND l.periodo LIKE ?";
    $like = "%$periodo%";
    $params[] = $like;
    $types .= "s";
}

if ($estado !== "") {
    $sql .= " AND l.estado = ?";
    $params[] = $estado;
    $types .= "s";
}

$sql .= " GROUP BY l.id ORDER BY l.fecha_liquidacion DESC";

$stmt = $conexion->prepare($sql);

if (!$stmt) {
    die("Error en la consulta: " . $conexion->error);
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$resultado = $stmt->get_result();
$totalLiquidaciones = $resultado ? $resultado->num_rows : 0;

function textoEstado($estado) {
    if ($estado === "") return "Todos";
    return $estado;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Reporte de Liquidaciones</title>

<style>
body {
    font-family: Arial;
    margin: 0;
    background: #f4f7fb;
}

.contenedor {
    width: 95%;
    max-width: 1400px;
    margin: 20px auto;
    background: #fff;
    padding: 20px;
}

.acciones {
    margin-bottom: 15px;
}

.btn {
    padding: 10px;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    margin-right: 5px;
    font-size: 14px;
}

.volver { background: #6b7280; }
.print  { background: #dc2626; }
.menu   { background: #ea580c; }

h1 {
    margin: 0;
    color: #16a34a;
}

.encabezado {
    border: 2px solid #ccc;
    padding: 15px;
    margin-bottom: 15px;
}

.resumen {
    margin-top: 10px;
}

table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12px;
}

th, td {
    border: 1px solid #ccc;
    padding: 6px;
}

th {
    background: #16a34a;
    color: white;
}

@media print {
    .acciones { display: none; }
}
</style>

</head>
<body>

<div class="contenedor">

<div class="acciones">
    <a href="reporte_liquidaciones.php" class="btn volver">Volver</a>
    <a href="reportes.php" class="btn menu">Reportes</a>
    <button onclick="window.print()" class="btn print">Imprimir / PDF</button>
</div>

<div class="encabezado">
    <h1>Reporte de Liquidaciones</h1>
    <p>Municipalidad de Fortín Lugones</p>
    <p>Fecha: <?php echo date("d/m/Y H:i"); ?></p>

    <div class="resumen">
        <strong>Periodo:</strong> <?php echo $periodo ?: "Todos"; ?> |
        <strong>Estado:</strong> <?php echo textoEstado($estado); ?> |
        <strong>Total:</strong> <?php echo $totalLiquidaciones; ?>
    </div>
</div>

<?php if ($resultado && $resultado->num_rows > 0): ?>
<table>
<thead>
<tr>
    <th>ID</th>
    <th>Tipo</th>
    <th>Periodo</th>
    <th>Fecha</th>
    <th>Estado</th>
    <th>Empleados</th>
    <th>Remunerativo</th>
    <th>No Remun.</th>
    <th>Asignaciones</th>
    <th>Descuentos</th>
    <th>Neto</th>
</tr>
</thead>
<tbody>

<?php while ($fila = $resultado->fetch_assoc()): ?>
<tr>
    <td><?php echo $fila['id']; ?></td>
    <td><?php echo $fila['tipo_liquidacion']; ?></td>
    <td><?php echo $fila['periodo']; ?></td>
    <td><?php echo date("d/m/Y", strtotime($fila['fecha_liquidacion'])); ?></td>
    <td><?php echo $fila['estado']; ?></td>
    <td><?php echo $fila['cantidad_empleados']; ?></td>
    <td>$<?php echo number_format($fila['total_remunerativo'],2,',','.'); ?></td>
    <td>$<?php echo number_format($fila['total_no_remunerativo'],2,',','.'); ?></td>
    <td>$<?php echo number_format($fila['total_asignaciones'],2,',','.'); ?></td>
    <td>$<?php echo number_format($fila['total_descuentos'],2,',','.'); ?></td>
    <td><strong>$<?php echo number_format($fila['total_neto'],2,',','.'); ?></strong></td>
</tr>
<?php endwhile; ?>

</tbody>
</table>

<?php else: ?>
<p>No hay datos.</p>
<?php endif; ?>

</div>

</body>
</html>