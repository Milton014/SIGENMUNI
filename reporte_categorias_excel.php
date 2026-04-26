<?php
session_start();
require_once("conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$buscar = trim($_GET['buscar'] ?? "");
$activo = trim($_GET['activo'] ?? "");

$sql = "SELECT * FROM categoria WHERE 1=1";
$params = [];
$types = "";

if ($buscar !== "") {
    $sql .= " AND (
        CAST(codigo AS CHAR) LIKE ?
        OR nombre LIKE ?
    )";
    $like = "%" . $buscar . "%";
    $params[] = $like;
    $params[] = $like;
    $types .= "ss";
}

if ($activo !== "") {
    $sql .= " AND activo = ?";
    $params[] = (int)$activo;
    $types .= "i";
}

$sql .= " ORDER BY codigo ASC, nombre ASC";

$stmt = $conexion->prepare($sql);

if (!$stmt) {
    die("Error al preparar la consulta: " . $conexion->error);
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$resultado = $stmt->get_result();

/*
|--------------------------------------------------------------------------
| DETECTAR COLUMNAS OPCIONALES
|--------------------------------------------------------------------------
*/
$columnasCategoria = [];
$checkCols = $conexion->query("SHOW COLUMNS FROM categoria");
if ($checkCols) {
    while ($col = $checkCols->fetch_assoc()) {
        $columnasCategoria[] = $col['Field'];
    }
}

$tieneActivo       = in_array('activo', $columnasCategoria);
$tieneDescripcion  = in_array('descripcion', $columnasCategoria);
$tieneBasico       = in_array('sueldo_basico', $columnasCategoria);
$tieneDedicacion   = in_array('dedicacion_funcional', $columnasCategoria);
$tieneSuplemento   = in_array('suplemento_especial', $columnasCategoria);

$nombreArchivo = "reporte_categorias_" . date("Ymd_His") . ".xls";

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
    <title>Reporte de Categorías</title>
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
            background: #ede9fe;
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
        <td colspan="8" class="titulo">SIGENMUNI - Reporte de Categorías</td>
    </tr>
    <tr>
        <td colspan="8" class="subtitulo">Municipalidad de Fortín Lugones</td>
    </tr>
    <tr>
        <td colspan="8" class="subtitulo">Fecha de emisión: <?php echo date("d/m/Y H:i:s"); ?></td>
    </tr>
    <tr>
        <td colspan="8" class="subtitulo">
            Filtros:
            Búsqueda = <?php echo htmlspecialchars($buscar ?: "Todos"); ?> |
            Estado =
            <?php
            if ($activo === "1") {
                echo "Activas";
            } elseif ($activo === "0") {
                echo "Inactivas";
            } else {
                echo "Todas";
            }
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

            <?php if ($tieneDescripcion): ?>
                <th>Descripción</th>
            <?php endif; ?>

            <?php if ($tieneBasico): ?>
                <th>Sueldo Básico</th>
            <?php endif; ?>

            <?php if ($tieneDedicacion): ?>
                <th>Dedicación Funcional</th>
            <?php endif; ?>

            <?php if ($tieneSuplemento): ?>
                <th>Suplemento Especial</th>
            <?php endif; ?>

            <?php if ($tieneActivo): ?>
                <th>Estado</th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php if ($resultado && $resultado->num_rows > 0): ?>
            <?php while ($fila = $resultado->fetch_assoc()): ?>
                <tr>
                    <td><?php echo (int)$fila['id']; ?></td>
                    <td><?php echo htmlspecialchars($fila['codigo'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($fila['nombre'] ?? ''); ?></td>

                    <?php if ($tieneDescripcion): ?>
                        <td><?php echo htmlspecialchars($fila['descripcion'] ?? ''); ?></td>
                    <?php endif; ?>

                    <?php if ($tieneBasico): ?>
                        <td><?php echo number_format((float)($fila['sueldo_basico'] ?? 0), 2, ',', '.'); ?></td>
                    <?php endif; ?>

                    <?php if ($tieneDedicacion): ?>
                        <td><?php echo number_format((float)($fila['dedicacion_funcional'] ?? 0), 2, ',', '.'); ?></td>
                    <?php endif; ?>

                    <?php if ($tieneSuplemento): ?>
                        <td><?php echo number_format((float)($fila['suplemento_especial'] ?? 0), 2, ',', '.'); ?></td>
                    <?php endif; ?>

                    <?php if ($tieneActivo): ?>
                        <td><?php echo ((int)$fila['activo'] === 1) ? 'Activa' : 'Inactiva'; ?></td>
                    <?php endif; ?>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="8">No se encontraron categorías con los filtros seleccionados.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>