<?php
session_start();
require_once("conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$liquidacionId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$empleadoDetalleId = isset($_GET['empleado_id']) ? (int)$_GET['empleado_id'] : 0;

if ($liquidacionId <= 0) {
    die("ID de liquidación inválido.");
}

/*
|--------------------------------------------------------------------------
| 1) CABECERA DE LA LIQUIDACIÓN
|--------------------------------------------------------------------------
*/
$stmtLiq = $conexion->prepare("
    SELECT 
        id,
        tipo_liquidacion,
        periodo,
        fecha_liquidacion,
        descripcion,
        estado,
        created_at
    FROM liquidacion
    WHERE id = ?
    LIMIT 1
");
$stmtLiq->bind_param("i", $liquidacionId);
$stmtLiq->execute();
$liquidacion = $stmtLiq->get_result()->fetch_assoc();

if (!$liquidacion) {
    die("La liquidación no existe.");
}

/*
|--------------------------------------------------------------------------
| 2) RESUMEN POR EMPLEADO
|--------------------------------------------------------------------------
*/
$stmtResumen = $conexion->prepare("
    SELECT 
        le.id,
        le.empleado_id,
        e.nro_legajo,
        e.apellido,
        e.nombre,
        le.total_remunerativo,
        le.total_descuentos,
        le.total_no_remunerativo,
        le.total_asignaciones,
        le.neto
    FROM liquidacion_empleado le
    INNER JOIN empleado e ON le.empleado_id = e.id
    WHERE le.liquidacion_id = ?
    ORDER BY e.apellido, e.nombre
");
$stmtResumen->bind_param("i", $liquidacionId);
$stmtResumen->execute();
$resumenEmpleados = $stmtResumen->get_result();

/*
|--------------------------------------------------------------------------
| 3) TOTALES GENERALES
|--------------------------------------------------------------------------
*/
$stmtTotales = $conexion->prepare("
    SELECT 
        COUNT(*) AS cantidad_empleados,
        COALESCE(SUM(total_remunerativo), 0) AS total_remunerativo,
        COALESCE(SUM(total_descuentos), 0) AS total_descuentos,
        COALESCE(SUM(total_no_remunerativo), 0) AS total_no_remunerativo,
        COALESCE(SUM(total_asignaciones), 0) AS total_asignaciones,
        COALESCE(SUM(neto), 0) AS total_neto
    FROM liquidacion_empleado
    WHERE liquidacion_id = ?
");
$stmtTotales->bind_param("i", $liquidacionId);
$stmtTotales->execute();
$totales = $stmtTotales->get_result()->fetch_assoc();

/*
|--------------------------------------------------------------------------
| 4) DETALLE DE CONCEPTOS DE UN EMPLEADO (OPCIONAL)
|--------------------------------------------------------------------------
*/
$detalleConceptos = null;
$empleadoSeleccionado = null;

if ($empleadoDetalleId > 0) {

    $stmtEmpleadoSel = $conexion->prepare("
        SELECT id, nro_legajo, apellido, nombre
        FROM empleado
        WHERE id = ?
        LIMIT 1
    ");
    $stmtEmpleadoSel->bind_param("i", $empleadoDetalleId);
    $stmtEmpleadoSel->execute();
    $empleadoSeleccionado = $stmtEmpleadoSel->get_result()->fetch_assoc();

    $stmtDetalle = $conexion->prepare("
        SELECT 
            ld.id,
            ld.cantidad,
            ld.porcentaje_aplicado,
            ld.monto,
            ld.es_manual,
            ld.observacion,
            c.codigo,
            c.nombre AS concepto_nombre
        FROM liquidacion_detalle ld
        INNER JOIN concepto c ON ld.concepto_id = c.id
        WHERE ld.liquidacion_id = ? AND ld.empleado_id = ?
        ORDER BY c.codigo ASC
    ");
    $stmtDetalle->bind_param("ii", $liquidacionId, $empleadoDetalleId);
    $stmtDetalle->execute();
    $detalleConceptos = $stmtDetalle->get_result();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Liquidación</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f7fb;
            color: #1f2937;
        }

        .contenedor {
            max-width: 1250px;
            margin: 30px auto;
            background: #ffffff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 14px rgba(0,0,0,0.08);
        }

        h2, h3 {
            margin-top: 0;
            color: #0f766e;
        }

        .cabecera {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 12px;
            margin-bottom: 20px;
        }

        .card {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 14px;
        }

        .card strong {
            display: block;
            margin-bottom: 6px;
            color: #374151;
        }

        .badge {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: bold;
        }

        .estado-borrador {
            background: #fef3c7;
            color: #92400e;
        }

        .estado-cerrada {
            background: #d1fae5;
            color: #065f46;
        }

        .estado-anulada {
            background: #fee2e2;
            color: #991b1b;
        }

        .acciones-superiores {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-block;
            padding: 10px 16px;
            text-decoration: none;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            color: white;
        }

        .btn-volver {
            background: #6b7280;
        }

        .btn-volver:hover {
            background: #4b5563;
        }

        .btn-procesar {
            background: #16a34a;
        }

        .btn-procesar:hover {
            background: #15803d;
        }

        .btn-manual {
            background: #d97706;
        }

        .btn-manual:hover {
            background: #b45309;
        }

        .acciones-botones {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn-detalle {
            background: #2563eb;
            padding: 8px 12px;
            font-size: 13px;
        }

        .btn-detalle:hover {
            background: #1d4ed8;
        }

        .btn-recibo {
            background: #0f766e;
            padding: 8px 12px;
            font-size: 13px;
        }

        .btn-recibo:hover {
            background: #115e59;
        }

        .tabla-contenedor {
            overflow-x: auto;
            margin-top: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 950px;
        }

        th, td {
            padding: 12px 10px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
            font-size: 14px;
            vertical-align: middle;
        }

        th {
            background: #f9fafb;
            color: #374151;
        }

        tr:hover {
            background: #f8fafc;
        }

        .sin-datos {
            text-align: center;
            padding: 25px;
            color: #6b7280;
        }

        .totales-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 12px;
            margin: 20px 0;
        }

        .total-box {
            background: #ecfeff;
            border: 1px solid #a5f3fc;
            padding: 14px;
            border-radius: 10px;
        }

        .total-box strong {
            display: block;
            margin-bottom: 5px;
            color: #0f766e;
        }

        .detalle-panel {
            margin-top: 30px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 20px;
        }

        .subtitulo {
            margin-bottom: 10px;
        }

        .manual-si {
            color: #b45309;
            font-weight: bold;
        }

        .manual-no {
            color: #065f46;
        }

        .fila-manual {
            background: #fff7ed;
        }

        .fila-manual:hover {
            background: #ffedd5 !important;
        }

        .etiqueta-manual {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: bold;
            background: #fed7aa;
            color: #9a3412;
        }

        .etiqueta-auto {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: bold;
            background: #dcfce7;
            color: #166534;
        }
    </style>
</head>
<body>

<div class="contenedor">

    <div class="acciones-superiores">
        <a href="liquidacion.php" class="btn btn-volver">Volver al Listado</a>

        <a href="liquidacion_concepto_manual.php?liquidacion_id=<?php echo $liquidacion['id']; ?>" class="btn btn-manual">
            Conceptos Manuales
        </a>

        <?php if ($liquidacion['estado'] === 'BORRADOR') { ?>
            <a href="liquidacion_procesar.php?id=<?php echo $liquidacion['id']; ?>" class="btn btn-procesar">Procesar Liquidación</a>
        <?php } ?>
    </div>

    <h2>Liquidación #<?php echo $liquidacion['id']; ?></h2>

    <div class="cabecera">
        <div class="card">
            <strong>Tipo</strong>
            <?php echo htmlspecialchars($liquidacion['tipo_liquidacion']); ?>
        </div>

        <div class="card">
            <strong>Período</strong>
            <?php echo htmlspecialchars($liquidacion['periodo']); ?>
        </div>

        <div class="card">
            <strong>Fecha de Liquidación</strong>
            <?php echo date("d/m/Y", strtotime($liquidacion['fecha_liquidacion'])); ?>
        </div>

        <div class="card">
            <strong>Estado</strong>
            <?php
            $estado = $liquidacion['estado'];
            $claseEstado = 'estado-borrador';

            if ($estado === 'CERRADA') {
                $claseEstado = 'estado-cerrada';
            } elseif ($estado === 'ANULADA') {
                $claseEstado = 'estado-anulada';
            }
            ?>
            <span class="badge <?php echo $claseEstado; ?>">
                <?php echo htmlspecialchars($estado); ?>
            </span>
        </div>

        <div class="card">
            <strong>Descripción</strong>
            <?php echo htmlspecialchars($liquidacion['descripcion'] ?: '-'); ?>
        </div>

        <div class="card">
            <strong>Creada</strong>
            <?php echo date("d/m/Y H:i", strtotime($liquidacion['created_at'])); ?>
        </div>
    </div>

    <h3>Totales Generales</h3>

    <div class="totales-grid">
        <div class="total-box">
            <strong>Empleados</strong>
            <?php echo (int)$totales['cantidad_empleados']; ?>
        </div>

        <div class="total-box">
            <strong>Total Remunerativo</strong>
            $<?php echo number_format((float)$totales['total_remunerativo'], 2, ',', '.'); ?>
        </div>

        <div class="total-box">
            <strong>Total Descuentos</strong>
            $<?php echo number_format((float)$totales['total_descuentos'], 2, ',', '.'); ?>
        </div>

        <div class="total-box">
            <strong>Total No Remunerativo</strong>
            $<?php echo number_format((float)$totales['total_no_remunerativo'], 2, ',', '.'); ?>
        </div>

        <div class="total-box">
            <strong>Total Asignaciones</strong>
            $<?php echo number_format((float)$totales['total_asignaciones'], 2, ',', '.'); ?>
        </div>

        <div class="total-box">
            <strong>Total Neto</strong>
            $<?php echo number_format((float)$totales['total_neto'], 2, ',', '.'); ?>
        </div>
    </div>

    <h3>Resumen por Empleado</h3>

    <div class="tabla-contenedor">
        <table>
            <thead>
                <tr>
                    <th>Legajo</th>
                    <th>Empleado</th>
                    <th>Total Remunerativo</th>
                    <th>Total Descuentos</th>
                    <th>No Remunerativo</th>
                    <th>Asignaciones</th>
                    <th>Neto</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($resumenEmpleados && $resumenEmpleados->num_rows > 0) { ?>
                    <?php while ($fila = $resumenEmpleados->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($fila['nro_legajo']); ?></td>
                            <td><?php echo htmlspecialchars($fila['apellido'] . ', ' . $fila['nombre']); ?></td>
                            <td>$<?php echo number_format((float)$fila['total_remunerativo'], 2, ',', '.'); ?></td>
                            <td>$<?php echo number_format((float)$fila['total_descuentos'], 2, ',', '.'); ?></td>
                            <td>$<?php echo number_format((float)$fila['total_no_remunerativo'], 2, ',', '.'); ?></td>
                            <td>$<?php echo number_format((float)$fila['total_asignaciones'], 2, ',', '.'); ?></td>
                            <td><strong>$<?php echo number_format((float)$fila['neto'], 2, ',', '.'); ?></strong></td>
                            <td>
                                <div class="acciones-botones">
                                    <a href="liquidacion_ver.php?id=<?php echo $liquidacionId; ?>&empleado_id=<?php echo $fila['empleado_id']; ?>" class="btn btn-detalle">
                                        Ver Detalle
                                    </a>

                                    <a href="recibo_sueldo.php?liquidacion_id=<?php echo $liquidacionId; ?>&empleado_id=<?php echo $fila['empleado_id']; ?>" class="btn btn-recibo">
                                        Ver Recibo
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="8" class="sin-datos">Esta liquidación todavía no tiene empleados procesados.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <?php if ($empleadoSeleccionado && $detalleConceptos) { ?>
        <div class="detalle-panel">
            <h3 class="subtitulo">
                Detalle de Conceptos - <?php echo htmlspecialchars($empleadoSeleccionado['apellido'] . ', ' . $empleadoSeleccionado['nombre']); ?>
            </h3>

            <div class="tabla-contenedor">
                <table>
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Concepto</th>
                            <th>Cantidad</th>
                            <th>% Aplicado</th>
                            <th>Monto</th>
                            <th>Origen</th>
                            <th>Observación</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($detalleConceptos->num_rows > 0) { ?>
                            <?php while ($det = $detalleConceptos->fetch_assoc()) { ?>
                                <tr class="<?php echo ((int)$det['es_manual'] === 1) ? 'fila-manual' : ''; ?>">
                                    <td><?php echo htmlspecialchars($det['codigo']); ?></td>
                                    <td><?php echo htmlspecialchars($det['concepto_nombre']); ?></td>
                                    <td><?php echo number_format((float)$det['cantidad'], 2, ',', '.'); ?></td>
                                    <td><?php echo number_format((float)$det['porcentaje_aplicado'], 2, ',', '.'); ?>%</td>
                                    <td>$<?php echo number_format((float)$det['monto'], 2, ',', '.'); ?></td>
                                    <td>
                                        <?php if ((int)$det['es_manual'] === 1) { ?>
                                            <span class="etiqueta-manual">MANUAL</span>
                                        <?php } else { ?>
                                            <span class="etiqueta-auto">AUTOMÁTICO</span>
                                        <?php } ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($det['observacion'] ?: '-'); ?></td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="7" class="sin-datos">No hay detalle cargado para este empleado.</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php } ?>

</div>

</body>
</html>