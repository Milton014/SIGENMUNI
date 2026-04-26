<?php
session_start();
require_once("conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$liquidacionId = isset($_GET['liquidacion_id']) ? (int)$_GET['liquidacion_id'] : 0;

if ($liquidacionId <= 0) {
    die("ID de liquidación inválido.");
}

/*
|--------------------------------------------------------------------------
| 1) DATOS DE LA LIQUIDACIÓN
|--------------------------------------------------------------------------
*/
$stmtLiq = $conexion->prepare("
    SELECT 
        id,
        tipo_liquidacion,
        periodo,
        fecha_liquidacion,
        descripcion,
        estado
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
| 2) EMPLEADOS DE LA LIQUIDACIÓN
|--------------------------------------------------------------------------
*/
$stmtEmp = $conexion->prepare("
    SELECT 
        e.id AS empleado_id,
        e.nro_legajo,
        e.apellido,
        e.nombre,
        e.fecha_alta,
        c.codigo AS categoria_codigo,
        c.nombre AS categoria_nombre
    FROM liquidacion_empleado le
    INNER JOIN empleado e ON le.empleado_id = e.id
    LEFT JOIN categoria c ON e.categoria_id = c.id
    WHERE le.liquidacion_id = ?
    ORDER BY e.apellido, e.nombre
");
$stmtEmp->bind_param("i", $liquidacionId);
$stmtEmp->execute();
$resEmpleados = $stmtEmp->get_result();

$empleados = [];
while ($emp = $resEmpleados->fetch_assoc()) {
    $empleados[] = $emp;
}

if (count($empleados) === 0) {
    die("La liquidación no tiene empleados procesados.");
}

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

/*
|--------------------------------------------------------------------------
| 3) ARMAR TODOS LOS RECIBOS
|--------------------------------------------------------------------------
*/
$recibos = [];

$stmtDetalle = $conexion->prepare("
    SELECT 
        ld.empleado_id,
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

foreach ($empleados as $emp) {
    $empleadoId = (int)$emp['empleado_id'];

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

    $recibos[] = [
        'empleado' => $emp,
        'haberesRem' => $haberesRem,
        'haberesNoRem' => $haberesNoRem,
        'asignaciones' => $asignaciones,
        'descuentos' => $descuentos,
        'aportesPatronales' => $aportesPatronales,
        'totalHaberesRem' => $totalHaberesRem,
        'totalHaberesNoRem' => $totalHaberesNoRem,
        'totalAsignaciones' => $totalAsignaciones,
        'totalDescuentos' => $totalDescuentos,
        'totalPatronales' => $totalPatronales,
        'neto' => $neto
    ];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibos de Liquidación</title>
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

        .acciones-globales {
            max-width: 1200px;
            margin: 20px auto 10px auto;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-block;
            padding: 10px 16px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            color: white;
            border: none;
            cursor: pointer;
        }

        .btn-volver {
            background: #6b7280;
        }

        .btn-reportes {
            background: #ea580c;
        }

        .btn-imprimir {
            background: #0f766e;
        }

        .contenedor-recibo {
            max-width: 1100px;
            margin: 18px auto;
            background: #fff;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 4px 14px rgba(0,0,0,0.08);
            page-break-after: always;
        }

        .contenedor-recibo:last-child {
            page-break-after: auto;
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
            padding: 9px;
            font-size: 13px;
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

            .acciones-globales {
                display: none;
            }

            .contenedor-recibo {
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

            @page {
                size: A4 portrait;
                margin: 10mm;
            }
        }
    </style>
</head>
<body>

<div class="acciones-globales">
    <a href="liquidacion_ver.php?id=<?php echo $liquidacionId; ?>" class="btn btn-volver">Volver</a>
    <a href="reportes.php" class="btn btn-reportes">Reportes</a>
    <button onclick="window.print();" class="btn btn-imprimir">Imprimir / Guardar PDF</button>
</div>

<?php foreach ($recibos as $item): ?>
    <?php
    $emp = $item['empleado'];
    ?>
    <div class="contenedor-recibo">

        <div class="encabezado">
            <div class="header-flex">
                <div class="titulo">
                    <h1>RECIBO DE SUELDO</h1>
                    <p>Municipalidad de Fortín Lugones</p>
                    <p>
                        Liquidación: <?php echo htmlspecialchars($liquidacion['tipo_liquidacion']); ?>
                        | Período: <?php echo htmlspecialchars($liquidacion['periodo']); ?>
                    </p>
                </div>

                <div class="logo">
                    <img src="img/escudo.jpg" alt="Escudo Municipal">
                </div>
            </div>

            <div class="grid-info">
                <div class="info-box">
                    <strong>Empleado</strong>
                    <?php echo htmlspecialchars($emp['apellido'] . ', ' . $emp['nombre']); ?>
                </div>

                <div class="info-box">
                    <strong>Legajo</strong>
                    <?php echo htmlspecialchars($emp['nro_legajo'] ?? '-'); ?>
                </div>

                <div class="info-box">
                    <strong>Categoría</strong>
                    <?php echo htmlspecialchars(($emp['categoria_codigo'] ?? '-') . ' - ' . ($emp['categoria_nombre'] ?? '-')); ?>
                </div>

                <div class="info-box">
                    <strong>Fecha de Alta</strong>
                    <?php echo !empty($emp['fecha_alta']) ? date("d/m/Y", strtotime($emp['fecha_alta'])) : '-'; ?>
                </div>

                <div class="info-box">
                    <strong>Antigüedad</strong>
                    <?php echo calcularAntiguedadTexto($emp['fecha_alta'] ?? null); ?>
                </div>

                <div class="info-box">
                    <strong>Fecha de Liquidación</strong>
                    <?php echo date("d/m/Y", strtotime($liquidacion['fecha_liquidacion'])); ?>
                </div>
            </div>
        </div>

        <div class="bloque">
            <h3>Haberes Remunerativos</h3>
            <?php if (count($item['haberesRem']) > 0) { ?>
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
                            <?php foreach ($item['haberesRem'] as $det): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($det['codigo']); ?></td>
                                    <td><?php echo htmlspecialchars($det['nombre']); ?></td>
                                    <td><?php echo number_format((float)$det['cantidad'], 2, ',', '.'); ?></td>
                                    <td><?php echo number_format((float)$det['porcentaje_aplicado'], 2, ',', '.'); ?></td>
                                    <td>$<?php echo number_format((float)$det['monto'], 2, ',', '.'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="total-fila">
                                <td colspan="4">Total Haberes Remunerativos</td>
                                <td>$<?php echo number_format($item['totalHaberesRem'], 2, ',', '.'); ?></td>
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
            <?php if (count($item['haberesNoRem']) > 0) { ?>
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
                            <?php foreach ($item['haberesNoRem'] as $det): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($det['codigo']); ?></td>
                                    <td><?php echo htmlspecialchars($det['nombre']); ?></td>
                                    <td><?php echo number_format((float)$det['cantidad'], 2, ',', '.'); ?></td>
                                    <td><?php echo number_format((float)$det['porcentaje_aplicado'], 2, ',', '.'); ?></td>
                                    <td>$<?php echo number_format((float)$det['monto'], 2, ',', '.'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="total-fila">
                                <td colspan="4">Total Haberes No Remunerativos</td>
                                <td>$<?php echo number_format($item['totalHaberesNoRem'], 2, ',', '.'); ?></td>
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
            <?php if (count($item['asignaciones']) > 0) { ?>
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
                            <?php foreach ($item['asignaciones'] as $det): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($det['codigo']); ?></td>
                                    <td><?php echo htmlspecialchars($det['nombre']); ?></td>
                                    <td><?php echo number_format((float)$det['cantidad'], 2, ',', '.'); ?></td>
                                    <td><?php echo number_format((float)$det['porcentaje_aplicado'], 2, ',', '.'); ?></td>
                                    <td>$<?php echo number_format((float)$det['monto'], 2, ',', '.'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="total-fila">
                                <td colspan="4">Total Asignaciones</td>
                                <td>$<?php echo number_format($item['totalAsignaciones'], 2, ',', '.'); ?></td>
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
            <?php if (count($item['descuentos']) > 0) { ?>
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
                            <?php foreach ($item['descuentos'] as $det): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($det['codigo']); ?></td>
                                    <td><?php echo htmlspecialchars($det['nombre']); ?></td>
                                    <td><?php echo number_format((float)$det['cantidad'], 2, ',', '.'); ?></td>
                                    <td><?php echo number_format((float)$det['porcentaje_aplicado'], 2, ',', '.'); ?></td>
                                    <td>$<?php echo number_format((float)$det['monto'], 2, ',', '.'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="total-fila">
                                <td colspan="4">Total Descuentos</td>
                                <td>$<?php echo number_format($item['totalDescuentos'], 2, ',', '.'); ?></td>
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
                $<?php echo number_format($item['totalHaberesRem'], 2, ',', '.'); ?>
            </div>

            <div class="resumen-box">
                <strong>Total No Remunerativo</strong>
                $<?php echo number_format($item['totalHaberesNoRem'], 2, ',', '.'); ?>
            </div>

            <div class="resumen-box">
                <strong>Total Asignaciones</strong>
                $<?php echo number_format($item['totalAsignaciones'], 2, ',', '.'); ?>
            </div>

            <div class="resumen-box">
                <strong>Total Descuentos</strong>
                $<?php echo number_format($item['totalDescuentos'], 2, ',', '.'); ?>
            </div>

            <div class="resumen-box neto">
                <strong>Neto a Cobrar</strong>
                $<?php echo number_format($item['neto'], 2, ',', '.'); ?>
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
<?php endforeach; ?>

</body>
</html>