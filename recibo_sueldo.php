<?php
session_start();
require_once("conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$liquidacionId = isset($_GET['liquidacion_id']) ? (int)$_GET['liquidacion_id'] : 0;
$empleadoId    = isset($_GET['empleado_id']) ? (int)$_GET['empleado_id'] : 0;

if ($liquidacionId <= 0 || $empleadoId <= 0) {
    die("Parámetros inválidos.");
}

/*
|--------------------------------------------------------------------------
| 1) DATOS CABECERA LIQUIDACIÓN + EMPLEADO
|--------------------------------------------------------------------------
*/
$stmt = $conexion->prepare("
    SELECT 
        l.id AS liquidacion_id,
        l.tipo_liquidacion,
        l.periodo,
        l.fecha_liquidacion,
        l.estado,
        e.id AS empleado_id,
        e.nro_legajo,
        e.apellido,
        e.nombre,
        e.fecha_alta,
        c.codigo AS categoria_codigo,
        c.nombre AS categoria_nombre
    FROM liquidacion l
    INNER JOIN liquidacion_empleado le ON le.liquidacion_id = l.id
    INNER JOIN empleado e ON le.empleado_id = e.id
    LEFT JOIN categoria c ON e.categoria_id = c.id
    WHERE l.id = ? AND e.id = ?
    LIMIT 1
");
$stmt->bind_param("ii", $liquidacionId, $empleadoId);
$stmt->execute();
$datos = $stmt->get_result()->fetch_assoc();

if (!$datos) {
    die("No se encontró el recibo solicitado.");
}

/*
|--------------------------------------------------------------------------
| 2) DETALLE DE CONCEPTOS
|--------------------------------------------------------------------------
*/
$stmtDetalle = $conexion->prepare("
    SELECT 
        ld.cantidad,
        ld.porcentaje_aplicado,
        ld.monto,
        ld.es_manual,
        ld.observacion,
        c.codigo,
        c.nombre
    FROM liquidacion_detalle ld
    INNER JOIN concepto c ON ld.concepto_id = c.id
    WHERE ld.liquidacion_id = ? AND ld.empleado_id = ?
    ORDER BY CAST(c.codigo AS UNSIGNED) ASC
");
$stmtDetalle->bind_param("ii", $liquidacionId, $empleadoId);
$stmtDetalle->execute();
$resDetalle = $stmtDetalle->get_result();

$haberesRem = [];
$haberesNoRem = [];
$asignaciones = [];
$descuentos = [];
$aportesPatronales = [];

$totalHaberesRem = 0;
$totalHaberesNoRem = 0;
$totalAsignaciones = 0;
$totalDescuentos = 0;
$totalPatronales = 0;

while ($row = $resDetalle->fetch_assoc()) {
    $codigo = (int)$row['codigo'];
    $monto = (float)$row['monto'];

    if ($codigo >= 101 && $codigo <= 199) {
        if ($codigo == 112) {
            $haberesNoRem[] = $row;
            $totalHaberesNoRem += $monto;
        } else {
            $haberesRem[] = $row;
            $totalHaberesRem += $monto;
        }
    } elseif ($codigo >= 201 && $codigo <= 299) {
        $asignaciones[] = $row;
        $totalAsignaciones += $monto;
    } elseif ($codigo >= 301 && $codigo <= 399) {
        $descuentos[] = $row;
        $totalDescuentos += $monto;
    } elseif ($codigo >= 401 && $codigo <= 499) {
        $aportesPatronales[] = $row;
        $totalPatronales += $monto;
    }
}

$neto = ($totalHaberesRem + $totalHaberesNoRem + $totalAsignaciones) - $totalDescuentos;

function calcularAntiguedadTexto($fechaAlta) {
    if (empty($fechaAlta) || $fechaAlta === '0000-00-00') {
        return "0 años";
    }

    try {
        $inicio = new DateTime($fechaAlta);
        $hoy = new DateTime();
        $diff = $inicio->diff($hoy);
        return $diff->y . " años";
    } catch (Exception $e) {
        return "0 años";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo de Sueldo</title>
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
            max-width: 1100px;
            margin: 25px auto;
            background: #fff;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 4px 14px rgba(0,0,0,0.08);
        }

        .acciones {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .btn {
            display: inline-block;
            padding: 10px 16px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            color: #fff;
        }

        .btn-volver {
            background: #6b7280;
        }

        .btn-volver:hover {
            background: #4b5563;
        }

        .btn-imprimir {
            background: #0f766e;
        }

        .btn-imprimir:hover {
            background: #115e59;
        }

        .encabezado {
            border: 2px solid #d1d5db;
            border-radius: 10px;
            padding: 18px;
            margin-bottom: 20px;
        }

        .header-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            margin-bottom: 15px;
        }

        .titulo {
            flex: 1;
            text-align: center;
        }

        .titulo h1 {
            margin: 0;
            font-size: 24px;
            color: #0f766e;
        }

        .titulo p {
            margin: 6px 0 0;
            font-size: 14px;
        }

        .logo {
            width: 110px;
            min-width: 110px;
            text-align: right;
        }

        .logo img {
            width: 100px;
            height: auto;
            object-fit: contain;
        }

        .grid-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 12px;
            margin-top: 15px;
        }

        .info-box {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 10px 12px;
            background: #f9fafb;
        }

        .info-box strong {
            display: block;
            font-size: 13px;
            margin-bottom: 4px;
            color: #374151;
        }

        .bloque {
            margin-top: 20px;
        }

        .bloque h3 {
            margin: 0 0 10px;
            color: #0f766e;
        }

        .tabla-contenedor {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 700px;
        }

        th, td {
            border: 1px solid #e5e7eb;
            padding: 10px;
            font-size: 14px;
            text-align: left;
        }

        th {
            background: #f3f4f6;
        }

        .total-fila td {
            font-weight: bold;
            background: #f9fafb;
        }

        .resumen-final {
            margin-top: 25px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 12px;
        }

        .resumen-box {
            border-radius: 10px;
            padding: 15px;
            border: 1px solid #d1d5db;
            background: #f9fafb;
        }

        .resumen-box strong {
            display: block;
            margin-bottom: 6px;
        }

        .neto {
            background: #dcfce7;
            border-color: #86efac;
        }

        .sin-datos {
            text-align: center;
            color: #6b7280;
            padding: 15px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            background: #fafafa;
        }

        .firmas {
            margin-top: 60px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            align-items: end;
        }

        .firma-box {
            text-align: center;
            min-height: 90px;
        }

        .linea-firma {
            border-top: 1px solid #374151;
            width: 85%;
            margin: 55px auto 8px;
        }

        .firma-box p {
            margin: 0;
            font-size: 13px;
            color: #374151;
        }

        @media (max-width: 768px) {
            .header-flex {
                flex-direction: column-reverse;
                align-items: center;
            }

            .titulo {
                text-align: center;
            }

            .logo {
                text-align: center;
            }

            .firmas {
                grid-template-columns: 1fr;
                gap: 20px;
            }
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

            .logo img {
                width: 80px;
            }

            .firmas {
                margin-top: 45px;
            }
        }
    </style>
</head>
<body>

<div class="contenedor">

    <div class="acciones">
        <a href="liquidacion_ver.php?id=<?php echo $liquidacionId; ?>" class="btn btn-volver">Volver</a>
        <a href="#" onclick="window.print();" class="btn btn-imprimir">Imprimir</a>
    </div>

    <div class="encabezado">

        <div class="header-flex">
            <div class="titulo">
                <h1>RECIBO DE SUELDO</h1>
                <p>Municipalidad de Fortín Lugones</p>
                <p>Liquidación: <?php echo htmlspecialchars($datos['tipo_liquidacion']); ?> | Período: <?php echo htmlspecialchars($datos['periodo']); ?></p>
            </div>

            <div class="logo">
                <img src="img/escudo.jpg" alt="Escudo Municipal">
            </div>
        </div>

        <div class="grid-info">
            <div class="info-box">
                <strong>Empleado</strong>
                <?php echo htmlspecialchars($datos['apellido'] . ', ' . $datos['nombre']); ?>
            </div>

            <div class="info-box">
                <strong>Legajo</strong>
                <?php echo htmlspecialchars($datos['nro_legajo'] ?? '-'); ?>
            </div>

            <div class="info-box">
                <strong>Categoría</strong>
                <?php echo htmlspecialchars(($datos['categoria_codigo'] ?? '-') . ' - ' . ($datos['categoria_nombre'] ?? '-')); ?>
            </div>

            <div class="info-box">
                <strong>Fecha de Alta</strong>
                <?php echo !empty($datos['fecha_alta']) ? date("d/m/Y", strtotime($datos['fecha_alta'])) : '-'; ?>
            </div>

            <div class="info-box">
                <strong>Antigüedad</strong>
                <?php echo calcularAntiguedadTexto($datos['fecha_alta'] ?? null); ?>
            </div>

            <div class="info-box">
                <strong>Fecha de Liquidación</strong>
                <?php echo date("d/m/Y", strtotime($datos['fecha_liquidacion'])); ?>
            </div>
        </div>
    </div>

    <div class="bloque">
        <h3>Haberes Remunerativos</h3>
        <?php if (count($haberesRem) > 0) { ?>
            <div class="tabla-contenedor">
                <table>
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Concepto</th>
                            <th>Cantidad</th>
                            <th>%</th>
                            <th>Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($haberesRem as $item) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['codigo']); ?></td>
                                <td><?php echo htmlspecialchars($item['nombre']); ?></td>
                                <td><?php echo number_format((float)$item['cantidad'], 2, ',', '.'); ?></td>
                                <td><?php echo number_format((float)$item['porcentaje_aplicado'], 2, ',', '.'); ?></td>
                                <td>$<?php echo number_format((float)$item['monto'], 2, ',', '.'); ?></td>
                            </tr>
                        <?php } ?>
                        <tr class="total-fila">
                            <td colspan="4">Total Haberes Remunerativos</td>
                            <td>$<?php echo number_format($totalHaberesRem, 2, ',', '.'); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <div class="sin-datos">No hay haberes remunerativos.</div>
        <?php } ?>
    </div>

    <div class="bloque">
        <h3>Haberes No Remunerativos</h3>
        <?php if (count($haberesNoRem) > 0) { ?>
            <div class="tabla-contenedor">
                <table>
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Concepto</th>
                            <th>Cantidad</th>
                            <th>%</th>
                            <th>Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($haberesNoRem as $item) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['codigo']); ?></td>
                                <td><?php echo htmlspecialchars($item['nombre']); ?></td>
                                <td><?php echo number_format((float)$item['cantidad'], 2, ',', '.'); ?></td>
                                <td><?php echo number_format((float)$item['porcentaje_aplicado'], 2, ',', '.'); ?></td>
                                <td>$<?php echo number_format((float)$item['monto'], 2, ',', '.'); ?></td>
                            </tr>
                        <?php } ?>
                        <tr class="total-fila">
                            <td colspan="4">Total Haberes No Remunerativos</td>
                            <td>$<?php echo number_format($totalHaberesNoRem, 2, ',', '.'); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <div class="sin-datos">No hay haberes no remunerativos.</div>
        <?php } ?>
    </div>

    <div class="bloque">
        <h3>Asignaciones Familiares</h3>
        <?php if (count($asignaciones) > 0) { ?>
            <div class="tabla-contenedor">
                <table>
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Concepto</th>
                            <th>Cantidad</th>
                            <th>%</th>
                            <th>Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($asignaciones as $item) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['codigo']); ?></td>
                                <td><?php echo htmlspecialchars($item['nombre']); ?></td>
                                <td><?php echo number_format((float)$item['cantidad'], 2, ',', '.'); ?></td>
                                <td><?php echo number_format((float)$item['porcentaje_aplicado'], 2, ',', '.'); ?></td>
                                <td>$<?php echo number_format((float)$item['monto'], 2, ',', '.'); ?></td>
                            </tr>
                        <?php } ?>
                        <tr class="total-fila">
                            <td colspan="4">Total Asignaciones</td>
                            <td>$<?php echo number_format($totalAsignaciones, 2, ',', '.'); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <div class="sin-datos">No hay asignaciones familiares.</div>
        <?php } ?>
    </div>

    <div class="bloque">
        <h3>Descuentos</h3>
        <?php if (count($descuentos) > 0) { ?>
            <div class="tabla-contenedor">
                <table>
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Concepto</th>
                            <th>Cantidad</th>
                            <th>%</th>
                            <th>Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($descuentos as $item) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['codigo']); ?></td>
                                <td><?php echo htmlspecialchars($item['nombre']); ?></td>
                                <td><?php echo number_format((float)$item['cantidad'], 2, ',', '.'); ?></td>
                                <td><?php echo number_format((float)$item['porcentaje_aplicado'], 2, ',', '.'); ?></td>
                                <td>$<?php echo number_format((float)$item['monto'], 2, ',', '.'); ?></td>
                            </tr>
                        <?php } ?>
                        <tr class="total-fila">
                            <td colspan="4">Total Descuentos</td>
                            <td>$<?php echo number_format($totalDescuentos, 2, ',', '.'); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <div class="sin-datos">No hay descuentos.</div>
        <?php } ?>
    </div>

    <div class="resumen-final">
        <div class="resumen-box">
            <strong>Total Remunerativo</strong>
            $<?php echo number_format($totalHaberesRem, 2, ',', '.'); ?>
        </div>

        <div class="resumen-box">
            <strong>Total No Remunerativo</strong>
            $<?php echo number_format($totalHaberesNoRem, 2, ',', '.'); ?>
        </div>

        <div class="resumen-box">
            <strong>Total Asignaciones</strong>
            $<?php echo number_format($totalAsignaciones, 2, ',', '.'); ?>
        </div>

        <div class="resumen-box">
            <strong>Total Descuentos</strong>
            $<?php echo number_format($totalDescuentos, 2, ',', '.'); ?>
        </div>

        <div class="resumen-box neto">
            <strong>Neto a Cobrar</strong>
            $<?php echo number_format($neto, 2, ',', '.'); ?>
        </div>
    </div>

    <div class="firmas">
        <div class="firma-box">
            <div class="linea-firma"></div>
            <p>Firma del Empleado</p>
        </div>

        <div class="firma-box">
            <div class="linea-firma"></div>
            <p>Tesorería</p>
        </div>

        <div class="firma-box">
            <div class="linea-firma"></div>
            <p>Autoridad Municipal</p>
        </div>
    </div>

</div>

</body>
</html>