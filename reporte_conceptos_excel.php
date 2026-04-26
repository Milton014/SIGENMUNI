<?php
session_start();
require_once("conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$buscar    = trim($_GET['buscar'] ?? "");
$categoria = trim($_GET['categoria'] ?? "");
$activo    = trim($_GET['activo'] ?? "");

$sql = "SELECT * FROM concepto WHERE 1=1";
$params = [];
$types = "";

if ($buscar !== "") {
    $sql .= " AND (CAST(codigo AS CHAR) LIKE ? OR nombre LIKE ?)";
    $like = "%" . $buscar . "%";
    $params[] = $like;
    $params[] = $like;
    $types .= "ss";
}

if ($categoria !== "") {
    $sql .= " AND categoria = ?";
    $params[] = $categoria;
    $types .= "s";
}

if ($activo !== "") {
    $sql .= " AND activo = ?";
    $params[] = (int)$activo;
    $types .= "i";
}

$sql .= " ORDER BY codigo ASC, nombre ASC";

$stmt = $conexion->prepare($sql);

if (!$stmt) {
    die("Error en la consulta: " . $conexion->error);
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$resultado = $stmt->get_result();

$nombreArchivo = "reporte_conceptos_" . date("Ymd_His") . ".xls";

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
<title>Reporte de Conceptos</title>
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
        background: #dbeafe;
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
        <td colspan="14" class="titulo">SIGENMUNI - Reporte de Conceptos</td>
    </tr>
    <tr>
        <td colspan="14" class="subtitulo">Municipalidad de Fortín Lugones</td>
    </tr>
    <tr>
        <td colspan="14" class="subtitulo">Fecha de emisión: <?php echo date("d/m/Y H:i:s"); ?></td>
    </tr>
    <tr>
        <td colspan="14" class="subtitulo">
            Filtros:
            Búsqueda = <?php echo htmlspecialchars($buscar ?: "Todos"); ?> |
            Categoría = <?php echo htmlspecialchars($categoria ?: "Todas"); ?> |
            Estado = 
            <?php
            if ($activo === "1") echo "Activos";
            elseif ($activo === "0") echo "Inactivos";
            else echo "Todos";
            ?>
        </td>
    </tr>
</table>

<br>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Código</th>
            <th>Nombre</th>
            <th>Categoría</th>
            <th>Forma Cálculo</th>
            <th>%</th>
            <th>Monto Fijo</th>
            <th>Requiere Manual</th>
            <th>Base Cálculo</th>
            <th>Orden</th>
            <th>Aplica SAC</th>
            <th>Visible Recibo</th>
            <th>Estado</th>
            <th>Vigencia</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($resultado && $resultado->num_rows > 0): ?>
            <?php while ($fila = $resultado->fetch_assoc()): ?>
                <?php
                $desde = !empty($fila['fecha_desde']) ? date("d/m/Y", strtotime($fila['fecha_desde'])) : "-";
                $hasta = !empty($fila['fecha_hasta']) ? date("d/m/Y", strtotime($fila['fecha_hasta'])) : "-";
                ?>
                <tr>
                    <td><?php echo (int)$fila['id']; ?></td>
                    <td><?php echo htmlspecialchars($fila['codigo']); ?></td>
                    <td><?php echo htmlspecialchars($fila['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($fila['categoria']); ?></td>
                    <td><?php echo htmlspecialchars($fila['forma_calculo']); ?></td>
                    <td><?php echo number_format((float)$fila['porcentaje'], 2, ',', '.'); ?></td>
                    <td><?php echo number_format((float)$fila['monto_fijo'], 2, ',', '.'); ?></td>
                    <td><?php echo ((int)$fila['requiere_manual'] === 1) ? 'Sí' : 'No'; ?></td>
                    <td><?php echo htmlspecialchars($fila['base_calculo'] ?? ''); ?></td>
                    <td><?php echo (int)$fila['orden_calculo']; ?></td>
                    <td><?php echo ((int)$fila['aplica_sac'] === 1) ? 'Sí' : 'No'; ?></td>
                    <td><?php echo ((int)$fila['visible_recibo'] === 1) ? 'Sí' : 'No'; ?></td>
                    <td><?php echo ((int)$fila['activo'] === 1) ? 'Activo' : 'Inactivo'; ?></td>
                    <td><?php echo $desde . " / " . $hasta; ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="14">No se encontraron conceptos.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>