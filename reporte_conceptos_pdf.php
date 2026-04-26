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
$totalConceptos = $resultado ? $resultado->num_rows : 0;

function textoEstadoFiltro($activo) {
    if ($activo === "1") return "Activos";
    if ($activo === "0") return "Inactivos";
    return "Todos";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Conceptos - PDF</title>
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
            max-width: 1450px;
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
            color: #0891b2;
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
            min-width: 1500px;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #d1d5db;
            padding: 8px;
            font-size: 12px;
            text-align: left;
            vertical-align: middle;
        }

        th {
            background: #0891b2;
            color: white;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: bold;
            color: white;
            white-space: nowrap;
        }

        .badge-rem { background: #2563eb; }
        .badge-no-rem { background: #7c3aed; }
        .badge-asig { background: #059669; }
        .badge-desc { background: #dc2626; }
        .badge-aporte { background: #ea580c; }

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
        <a href="reporte_conceptos.php?buscar=<?php echo urlencode($buscar); ?>&categoria=<?php echo urlencode($categoria); ?>&activo=<?php echo urlencode($activo); ?>" class="btn btn-volver">Volver</a>
        <a href="reportes.php" class="btn btn-reportes">Reportes</a>
        <button onclick="window.print()" class="btn btn-imprimir">Imprimir / Guardar PDF</button>
    </div>

    <div class="encabezado">
        <h1>Reporte de Conceptos</h1>
        <p><strong>SIGENMUNI</strong> - Municipalidad de Fortín Lugones</p>
        <p>Fecha de emisión: <?php echo date("d/m/Y H:i:s"); ?></p>

        <div class="resumen">
            <div class="info-box">
                <strong>Búsqueda aplicada</strong>
                <?php echo htmlspecialchars($buscar !== "" ? $buscar : "Todos"); ?>
            </div>

            <div class="info-box">
                <strong>Categoría</strong>
                <?php echo htmlspecialchars($categoria !== "" ? $categoria : "Todas"); ?>
            </div>

            <div class="info-box">
                <strong>Estado</strong>
                <?php echo textoEstadoFiltro($activo); ?>
            </div>

            <div class="info-box">
                <strong>Total de conceptos</strong>
                <?php echo (int)$totalConceptos; ?>
            </div>
        </div>
    </div>

    <?php if ($resultado && $resultado->num_rows > 0): ?>
        <div class="tabla-contenedor">
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
                        <th>Req. Manual</th>
                        <th>Base Cálculo</th>
                        <th>Orden</th>
                        <th>Aplica SAC</th>
                        <th>Visible Recibo</th>
                        <th>Estado</th>
                        <th>Vigencia</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($fila = $resultado->fetch_assoc()): ?>
                        <?php
                        $badgeClass = "";
                        switch ($fila['categoria']) {
                            case 'REMUNERATIVO':
                                $badgeClass = 'badge-rem';
                                break;
                            case 'NO_REMUNERATIVO':
                                $badgeClass = 'badge-no-rem';
                                break;
                            case 'ASIGNACION_FAMILIAR':
                                $badgeClass = 'badge-asig';
                                break;
                            case 'DESCUENTO':
                                $badgeClass = 'badge-desc';
                                break;
                            case 'APORTE_PATRONAL':
                                $badgeClass = 'badge-aporte';
                                break;
                        }

                        $desde = !empty($fila['fecha_desde']) ? date("d/m/Y", strtotime($fila['fecha_desde'])) : "-";
                        $hasta = !empty($fila['fecha_hasta']) ? date("d/m/Y", strtotime($fila['fecha_hasta'])) : "-";
                        ?>
                        <tr>
                            <td><?php echo (int)$fila['id']; ?></td>
                            <td><?php echo htmlspecialchars($fila['codigo']); ?></td>
                            <td><?php echo htmlspecialchars($fila['nombre']); ?></td>
                            <td>
                                <span class="badge <?php echo $badgeClass; ?>">
                                    <?php echo htmlspecialchars($fila['categoria']); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($fila['forma_calculo']); ?></td>
                            <td><?php echo number_format((float)$fila['porcentaje'], 2, ',', '.'); ?></td>
                            <td>$ <?php echo number_format((float)$fila['monto_fijo'], 2, ',', '.'); ?></td>
                            <td><?php echo ((int)$fila['requiere_manual'] === 1) ? 'Sí' : 'No'; ?></td>
                            <td><?php echo htmlspecialchars($fila['base_calculo'] ?? ''); ?></td>
                            <td><?php echo (int)$fila['orden_calculo']; ?></td>
                            <td><?php echo ((int)$fila['aplica_sac'] === 1) ? 'Sí' : 'No'; ?></td>
                            <td><?php echo ((int)$fila['visible_recibo'] === 1) ? 'Sí' : 'No'; ?></td>
                            <td>
                                <?php if ((int)$fila['activo'] === 1): ?>
                                    <span class="estado-activo">Activo</span>
                                <?php else: ?>
                                    <span class="estado-inactivo">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $desde . " / " . $hasta; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="sin-registros">
            No se encontraron conceptos con los filtros seleccionados.
        </div>
    <?php endif; ?>

</div>

</body>
</html>