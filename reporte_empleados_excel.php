<?php
session_start();
require_once("conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$busqueda = trim($_GET['busqueda'] ?? "");
$estado   = trim($_GET['estado'] ?? "");

$sql = "
    SELECT 
        e.nro_legajo,
        e.apellido,
        e.nombre,
        e.dni,
        e.cuil,
        e.telefono,
        e.email,
        e.fecha_alta,
        e.fecha_baja,
        e.activo,
        c.nombre AS categoria,
        o.nombre AS oficina,
        s.nombre AS situacion
    FROM empleado e
    INNER JOIN categoria c ON e.categoria_id = c.id
    INNER JOIN oficina o ON e.oficina_id = o.id
    INNER JOIN situacion s ON e.situacion_id = s.id
    WHERE 1=1
";

$params = [];
$types = "";

if ($busqueda !== "") {
    $sql .= " 
        AND (
            e.nro_legajo LIKE ?
            OR e.apellido LIKE ?
            OR e.nombre LIKE ?
            OR e.dni LIKE ?
            OR e.cuil LIKE ?
            OR e.email LIKE ?
        )
    ";
    $like = "%{$busqueda}%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $types .= "ssssss";
}

if ($estado !== "") {
    $sql .= " AND e.activo = ?";
    $params[] = (int)$estado;
    $types .= "i";
}

$sql .= " ORDER BY e.apellido, e.nombre";

$stmt = $conexion->prepare($sql);

if (!$stmt) {
    die("Error al preparar la consulta: " . $conexion->error);
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$resultado = $stmt->get_result();

$nombreArchivo = "reporte_empleados_" . date("Ymd_His") . ".xls";

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
    <title>Reporte de Empleados</title>
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
            <td colspan="12" class="titulo">SIGENMUNI - Reporte de Empleados</td>
        </tr>
        <tr>
            <td colspan="12" class="subtitulo">Municipalidad de Fortín Lugones</td>
        </tr>
        <tr>
            <td colspan="12" class="subtitulo">Fecha de emisión: <?php echo date("d/m/Y H:i:s"); ?></td>
        </tr>
        <tr>
            <td colspan="12" class="subtitulo">
                Filtros aplicados:
                Búsqueda = <?php echo htmlspecialchars($busqueda !== "" ? $busqueda : "Todos"); ?> |
                Estado = 
                <?php
                if ($estado === "1") {
                    echo "Activos";
                } elseif ($estado === "0") {
                    echo "Inactivos";
                } else {
                    echo "Todos";
                }
                ?>
            </td>
        </tr>
    </table>

    <br>

    <table>
        <thead>
            <tr>
                <th>Legajo</th>
                <th>Apellido</th>
                <th>Nombre</th>
                <th>DNI</th>
                <th>CUIL</th>
                <th>Teléfono</th>
                <th>Email</th>
                <th>Fecha Alta</th>
                <th>Fecha Baja</th>
                <th>Categoría</th>
                <th>Oficina</th>
                <th>Situación</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($resultado && $resultado->num_rows > 0): ?>
                <?php while ($fila = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($fila['nro_legajo']); ?></td>
                        <td><?php echo htmlspecialchars($fila['apellido']); ?></td>
                        <td><?php echo htmlspecialchars($fila['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($fila['dni']); ?></td>
                        <td><?php echo htmlspecialchars($fila['cuil']); ?></td>
                        <td><?php echo htmlspecialchars($fila['telefono'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($fila['email'] ?? ''); ?></td>
                        <td>
                            <?php echo !empty($fila['fecha_alta']) ? date("d/m/Y", strtotime($fila['fecha_alta'])) : ''; ?>
                        </td>
                        <td>
                            <?php echo !empty($fila['fecha_baja']) ? date("d/m/Y", strtotime($fila['fecha_baja'])) : ''; ?>
                        </td>
                        <td><?php echo htmlspecialchars($fila['categoria']); ?></td>
                        <td><?php echo htmlspecialchars($fila['oficina']); ?></td>
                        <td><?php echo htmlspecialchars($fila['situacion']); ?></td>
                        <td><?php echo ((int)$fila['activo'] === 1) ? 'Activo' : 'Inactivo'; ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="13">No se encontraron empleados con los filtros seleccionados.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>