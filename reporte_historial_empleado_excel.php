<?php
session_start();
require_once("conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$empleadoId = isset($_GET['empleado_id']) ? (int)$_GET['empleado_id'] : 0;

if ($empleadoId <= 0) {
    die("Empleado inválido.");
}

$stmtEmp = $conexion->prepare("
    SELECT 
        e.id,
        e.nro_legajo,
        e.apellido,
        e.nombre,
        e.dni,
        e.cuil,
        e.fecha_alta,
        c.nombre AS categoria,
        o.nombre AS oficina,
        s.nombre AS situacion
    FROM empleado e
    LEFT JOIN categoria c ON e.categoria_id = c.id
    LEFT JOIN oficina o ON e.oficina_id = o.id
    LEFT JOIN situacion s ON e.situacion_id = s.id
    WHERE e.id = ?
    LIMIT 1
");
$stmtEmp->bind_param("i", $empleadoId);
$stmtEmp->execute();
$empleado = $stmtEmp->get_result()->fetch_assoc();

if (!$empleado) {
    die("No se encontró el empleado solicitado.");
}

$stmtHist = $conexion->prepare("
    SELECT 
        l.id AS liquidacion_id,
        l.tipo_liquidacion,
        l.periodo,
        l.fecha_liquidacion,
        l.estado,
        l.descripcion,
        le.total_remunerativo,
        le.total_descuentos,
        le.total_no_remunerativo,
        le.total_asignaciones,
        le.neto
    FROM liquidacion_empleado le
    INNER JOIN liquidacion l ON le.liquidacion_id = l.id
    WHERE le.empleado_id = ?
    ORDER BY l.fecha_liquidacion DESC, l.id DESC
");
$stmtHist->bind_param("i", $empleadoId);
$stmtHist->execute();
$resultado = $stmtHist->get_result();

$nombreArchivo = "historial_empleado_" . $empleadoId . "_" . date("Ymd_His") . ".xls";

header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment; filename=\"$nombreArchivo\"");
header("Pragma: no-cache");
header("Expires: 0");

echo "\xEF\xBB\xBF";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial por Empleado</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            font-size: 12px;
        }
        th {
            background: #d9ead3;
            font-weight: bold;
        }
        .titulo {
            font-size: 16px;
            font-weight: bold;
        }
        .subtitulo {
            font-size: 12px;
        }
    </style>
</head>
<body>

<table>
    <tr>
        <td colspan="11" class="titulo">SIGENMUNI - Historial por Empleado</td>
    </tr>
    <tr>
        <td colspan="11" class="subtitulo">Municipalidad de Fortín Lugones</td>
    </tr>
    <tr>
        <td colspan="11" class="subtitulo">Fecha de emisión: <?php echo date("d/m/Y H:i:s"); ?></td>
    </tr>
    <tr>
        <td colspan="11" class="subtitulo">
            Empleado: <?php echo htmlspecialchars($empleado['apellido'] . ', ' . $empleado['nombre']); ?> |
            Legajo: <?php echo htmlspecialchars($empleado['nro_legajo'] ?? ''); ?> |
            DNI: <?php echo htmlspecialchars($empleado['dni'] ?? ''); ?>
        </td>
    </tr>
</table>

<br>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Tipo</th>
            <th>Período</th>
            <th>Fecha Liquidación</th>
            <th>Estado</th>
            <th>Descripción</th>
            <th>Total Remunerativo</th>
            <th>Total Descuentos</th>
            <th>No Remunerativo</th>
            <th>Asignaciones</th>
            <th>Neto</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($resultado && $resultado->num_rows > 0): ?>
            <?php while ($fila = $resultado->fetch_assoc()): ?>
                <tr>
                    <td><?php echo (int)$fila['liquidacion_id']; ?></td>
                    <td><?php echo htmlspecialchars($fila['tipo_liquidacion']); ?></td>
                    <td><?php echo htmlspecialchars($fila['periodo']); ?></td>
                    <td><?php echo !empty($fila['fecha_liquidacion']) ? date("d/m/Y", strtotime($fila['fecha_liquidacion'])) : ''; ?></td>
                    <td><?php echo htmlspecialchars($fila['estado']); ?></td>
                    <td><?php echo htmlspecialchars($fila['descripcion'] ?? ''); ?></td>
                    <td><?php echo number_format((float)$fila['total_remunerativo'], 2, ',', '.'); ?></td>
                    <td><?php echo number_format((float)$fila['total_descuentos'], 2, ',', '.'); ?></td>
                    <td><?php echo number_format((float)$fila['total_no_remunerativo'], 2, ',', '.'); ?></td>
                    <td><?php echo number_format((float)$fila['total_asignaciones'], 2, ',', '.'); ?></td>
                    <td><?php echo number_format((float)$fila['neto'], 2, ',', '.'); ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="11">Este empleado no tiene liquidaciones registradas.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>