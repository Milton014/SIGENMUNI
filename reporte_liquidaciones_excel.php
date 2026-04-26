<?php
session_start();
require_once("conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$periodo = trim($_GET['periodo'] ?? "");
$tipo    = trim($_GET['tipo'] ?? "");
$estado  = trim($_GET['estado'] ?? "");

$sql = "
    SELECT 
        l.id,
        l.tipo_liquidacion,
        l.periodo,
        l.fecha_liquidacion,
        l.descripcion,
        l.estado,
        l.created_at,
        COUNT(le.id) AS cantidad_empleados,
        COALESCE(SUM(le.total_remunerativo), 0) AS total_remunerativo,
        COALESCE(SUM(le.total_descuentos), 0) AS total_descuentos,
        COALESCE(SUM(le.total_no_remunerativo), 0) AS total_no_remunerativo,
        COALESCE(SUM(le.total_asignaciones), 0) AS total_asignaciones,
        COALESCE(SUM(le.neto), 0) AS total_neto
    FROM liquidacion l
    LEFT JOIN liquidacion_empleado le ON le.liquidacion_id = l.id
    WHERE 1=1
";

$params = [];
$types = "";

if ($periodo !== "") {
    $sql .= " AND l.periodo LIKE ?";
    $params[] = "%" . $periodo . "%";
    $types .= "s";
}

if ($tipo !== "") {
    $sql .= " AND l.tipo_liquidacion = ?";
    $params[] = $tipo;
    $types .= "s";
}

if ($estado !== "") {
    $sql .= " AND l.estado = ?";
    $params[] = $estado;
    $types .= "s";
}

$sql .= "
    GROUP BY 
        l.id,
        l.tipo_liquidacion,
        l.periodo,
        l.fecha_liquidacion,
        l.descripcion,
        l.estado,
        l.created_at
    ORDER BY l.fecha_liquidacion DESC, l.id DESC
";

$stmt = $conexion->prepare($sql);

if (!$stmt) {
    die("Error al preparar la consulta: " . $conexion->error);
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$resultado = $stmt->get_result();

$nombreArchivo = "reporte_liquidaciones_" . date("Ymd_His") . ".xls";

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
    <title>Reporte de Liquidaciones</title>
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
            background: #dcfce7;
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
        <td colspan="13" class="titulo">SIGENMUNI - Reporte de Liquidaciones</td>
    </tr>
    <tr>
        <td colspan="13" class="subtitulo">Municipalidad de Fortín Lugones</td>
    </tr>
    <tr>
        <td colspan="13" class="subtitulo">Fecha de emisión: <?php echo date("d/m/Y H:i:s"); ?></td>
    </tr>
    <tr>
        <td colspan="13" class="subtitulo">
            Filtros:
            Período = <?php echo htmlspecialchars($periodo ?: "Todos"); ?> |
            Tipo = <?php echo htmlspecialchars($tipo ?: "Todos"); ?> |
            Estado =
            <?php echo htmlspecialchars($estado ?: "Todos"); ?>
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
            <th>Descripción</th>
            <th>Estado</th>
            <th>Empleados</th>
            <th>Total Remunerativo</th>
            <th>Total Descuentos</th>
            <th>Total No Remunerativo</th>
            <th>Total Asignaciones</th>
            <th>Total Neto</th>
            <th>Creada</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($resultado && $resultado->num_rows > 0): ?>
            <?php while ($fila = $resultado->fetch_assoc()): ?>
                <tr>
                    <td><?php echo (int)$fila['id']; ?></td>
                    <td><?php echo htmlspecialchars($fila['tipo_liquidacion']); ?></td>
                    <td><?php echo htmlspecialchars($fila['periodo']); ?></td>
                    <td>
                        <?php echo !empty($fila['fecha_liquidacion']) ? date("d/m/Y", strtotime($fila['fecha_liquidacion'])) : ''; ?>
                    </td>
                    <td><?php echo htmlspecialchars($fila['descripcion'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($fila['estado']); ?></td>
                    <td><?php echo (int)$fila['cantidad_empleados']; ?></td>
                    <td><?php echo number_format((float)$fila['total_remunerativo'], 2, ',', '.'); ?></td>
                    <td><?php echo number_format((float)$fila['total_descuentos'], 2, ',', '.'); ?></td>
                    <td><?php echo number_format((float)$fila['total_no_remunerativo'], 2, ',', '.'); ?></td>
                    <td><?php echo number_format((float)$fila['total_asignaciones'], 2, ',', '.'); ?></td>
                    <td><?php echo number_format((float)$fila['total_neto'], 2, ',', '.'); ?></td>
                    <td>
                        <?php echo !empty($fila['created_at']) ? date("d/m/Y H:i", strtotime($fila['created_at'])) : ''; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="13">No se encontraron liquidaciones con los filtros seleccionados.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>