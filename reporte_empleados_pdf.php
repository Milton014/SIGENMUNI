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
    $sql .= " AND (
        e.nro_legajo LIKE ?
        OR e.apellido LIKE ?
        OR e.nombre LIKE ?
        OR e.dni LIKE ?
        OR e.cuil LIKE ?
        OR e.email LIKE ?
    )";
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
$totalEmpleados = $resultado ? $resultado->num_rows : 0;

function textoEstadoFiltro($estado) {
    if ($estado === "1") return "Activos";
    if ($estado === "0") return "Inactivos";
    return "Todos";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Empleados - PDF</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            background: #f4f7fb;
            color: #1f2937;
        }

        .contenedor {
            width: 95%;
            max-width: 1400px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 14px rgba(0,0,0,0.08);
        }

        .acciones {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }

        .btn {
            display: inline-block;
            text-decoration: none;
            border: none;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            color: white;
        }

        .btn-volver {
            background: #6b7280;
        }

        .btn-imprimir {
            background: #dc2626;
        }

        .btn-reportes {
            background: #ea580c;
        }

        .encabezado {
            border: 2px solid #d1d5db;
            border-radius: 10px;
            padding: 18px;
            margin-bottom: 18px;
        }

        .encabezado h1 {
            margin: 0 0 6px 0;
            font-size: 26px;
            color: #0f766e;
        }

        .encabezado p {
            margin: 4px 0;
            font-size: 14px;
        }

        .resumen {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 10px;
            margin-top: 15px;
        }

        .info-box {
            border: 1px solid #e5e7eb;
            background: #f9fafb;
            border-radius: 8px;
            padding: 10px 12px;
        }

        .info-box strong {
            display: block;
            margin-bottom: 5px;
            font-size: 13px;
            color: #374151;
        }

        .tabla-contenedor {
            overflow-x: auto;
        }

        table {
            width: 100%;
            min-width: 1200px;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #d1d5db;
            padding: 8px;
            font-size: 12px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #0f766e;
            color: white;
        }

        .estado-activo {
            font-weight: bold;
            color: #166534;
        }

        .estado-inactivo {
            font-weight: bold;
            color: #991b1b;
        }

        .sin-registros {
            text-align: center;
            padding: 20px;
            color: #6b7280;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            background: #fafafa;
        }

        @media print {
            body {
                background: #fff;
            }

            .acciones {
                display: none;
            }

            .contenedor {
                box-shadow: none;
                margin: 0;
                max-width: 100%;
                border-radius: 0;
                padding: 10px;
            }

            @page {
                size: landscape;
                margin: 12mm;
            }
        }
    </style>
</head>
<body>

<div class="contenedor">

    <div class="acciones">
        <a href="reporte_empleados.php?busqueda=<?php echo urlencode($busqueda); ?>&estado=<?php echo urlencode($estado); ?>" class="btn btn-volver">Volver</a>
        <a href="reportes.php" class="btn btn-reportes">Reportes</a>
        <button onclick="window.print()" class="btn btn-imprimir">Imprimir / Guardar PDF</button>
    </div>

    <div class="encabezado">
        <h1>Reporte de Empleados</h1>
        <p><strong>SIGENMUNI</strong> - Municipalidad de Fortín Lugones</p>
        <p>Fecha de emisión: <?php echo date("d/m/Y H:i:s"); ?></p>

        <div class="resumen">
            <div class="info-box">
                <strong>Búsqueda aplicada</strong>
                <?php echo htmlspecialchars($busqueda !== "" ? $busqueda : "Todos"); ?>
            </div>

            <div class="info-box">
                <strong>Estado</strong>
                <?php echo textoEstadoFiltro($estado); ?>
            </div>

            <div class="info-box">
                <strong>Total de empleados</strong>
                <?php echo (int)$totalEmpleados; ?>
            </div>
        </div>
    </div>

    <?php if ($resultado && $resultado->num_rows > 0): ?>
        <div class="tabla-contenedor">
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
                            <td>
                                <?php if ((int)$fila['activo'] === 1): ?>
                                    <span class="estado-activo">Activo</span>
                                <?php else: ?>
                                    <span class="estado-inactivo">Inactivo</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="sin-registros">
            No se encontraron empleados con los filtros seleccionados.
        </div>
    <?php endif; ?>

</div>

</body>
</html>