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
        e.activo,
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

$totalLiquidaciones = $resultado ? $resultado->num_rows : 0;

function estadoClasePdf($estado) {
    if ($estado === 'CERRADA') return 'estado-cerrada';
    if ($estado === 'ANULADA') return 'estado-anulada';
    return 'estado-borrador';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial por Empleado - PDF</title>
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

        .datos-empleado {
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
            vertical-align: middle;
        }

        th {
            background: #0f766e;
            color: white;
        }

        .estado-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: bold;
        }

        .estado-borrador {
            background: #fef3c7;
            color: #92400e;
        }

        .estado-cerrada {
            background: #dcfce7;
            color: #166534;
        }

        .estado-anulada {
            background: #fee2e2;
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
        <a href="reporte_historial_empleado.php?empleado_id=<?php echo $empleadoId; ?>" class="btn btn-volver">Volver</a>
        <a href="reportes.php" class="btn btn-reportes">Reportes</a>
        <button onclick="window.print()" class="btn btn-imprimir">Imprimir / Guardar PDF</button>
    </div>

    <div class="encabezado">
        <h1>Historial por Empleado</h1>
        <p><strong>SIGENMUNI</strong> - Municipalidad de Fortín Lugones</p>
        <p>Fecha de emisión: <?php echo date("d/m/Y H:i:s"); ?></p>

        <div class="datos-empleado">
            <div class="info-box">
                <strong>Empleado</strong>
                <?php echo htmlspecialchars($empleado['apellido'] . ', ' . $empleado['nombre']); ?>
            </div>

            <div class="info-box">
                <strong>Legajo</strong>
                <?php echo htmlspecialchars($empleado['nro_legajo'] ?? ''); ?>
            </div>

            <div class="info-box">
                <strong>DNI</strong>
                <?php echo htmlspecialchars($empleado['dni'] ?? ''); ?>
            </div>

            <div class="info-box">
                <strong>CUIL</strong>
                <?php echo htmlspecialchars($empleado['cuil'] ?? ''); ?>
            </div>

            <div class="info-box">
                <strong>Categoría</strong>
                <?php echo htmlspecialchars($empleado['categoria'] ?? ''); ?>
            </div>

            <div class="info-box">
                <strong>Oficina</strong>
                <?php echo htmlspecialchars($empleado['oficina'] ?? ''); ?>
            </div>

            <div class="info-box">
                <strong>Situación</strong>
                <?php echo htmlspecialchars($empleado['situacion'] ?? ''); ?>
            </div>

            <div class="info-box">
                <strong>Fecha Alta</strong>
                <?php echo !empty($empleado['fecha_alta']) ? date("d/m/Y", strtotime($empleado['fecha_alta'])) : ''; ?>
            </div>

            <div class="info-box">
                <strong>Total Liquidaciones</strong>
                <?php echo (int)$totalLiquidaciones; ?>
            </div>
        </div>
    </div>

    <?php if ($resultado && $resultado->num_rows > 0): ?>
        <div class="tabla-contenedor">
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
                    <?php while ($fila = $resultado->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo (int)$fila['liquidacion_id']; ?></td>
                            <td><?php echo htmlspecialchars($fila['tipo_liquidacion']); ?></td>
                            <td><?php echo htmlspecialchars($fila['periodo']); ?></td>
                            <td>
                                <?php echo !empty($fila['fecha_liquidacion']) ? date("d/m/Y", strtotime($fila['fecha_liquidacion'])) : ''; ?>
                            </td>
                            <td>
                                <span class="estado-badge <?php echo estadoClasePdf($fila['estado']); ?>">
                                    <?php echo htmlspecialchars($fila['estado']); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($fila['descripcion'] ?? ''); ?></td>
                            <td>$<?php echo number_format((float)$fila['total_remunerativo'], 2, ',', '.'); ?></td>
                            <td>$<?php echo number_format((float)$fila['total_descuentos'], 2, ',', '.'); ?></td>
                            <td>$<?php echo number_format((float)$fila['total_no_remunerativo'], 2, ',', '.'); ?></td>
                            <td>$<?php echo number_format((float)$fila['total_asignaciones'], 2, ',', '.'); ?></td>
                            <td><strong>$<?php echo number_format((float)$fila['neto'], 2, ',', '.'); ?></strong></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="sin-registros">
            Este empleado todavía no tiene liquidaciones registradas.
        </div>
    <?php endif; ?>

</div>

</body>
</html>