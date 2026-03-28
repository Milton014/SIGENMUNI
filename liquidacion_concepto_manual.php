<?php
session_start();
require_once("conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$liquidacionId = isset($_GET['liquidacion_id']) ? (int)$_GET['liquidacion_id'] : (int)($_POST['liquidacion_id'] ?? 0);
$detalleEditarId = isset($_GET['editar_id']) ? (int)$_GET['editar_id'] : (int)($_POST['detalle_id'] ?? 0);

$mensaje = "";
$error = "";

if ($liquidacionId <= 0) {
    die("Liquidación inválida.");
}

function recalcularResumenEmpleado($conexion, $liquidacionId, $empleadoId) {
    $sql = "
        SELECT c.codigo, ld.monto
        FROM liquidacion_detalle ld
        INNER JOIN concepto c ON ld.concepto_id = c.id
        WHERE ld.liquidacion_id = ? AND ld.empleado_id = ?
    ";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ii", $liquidacionId, $empleadoId);
    $stmt->execute();
    $res = $stmt->get_result();

    $totalRem = 0;
    $totalNoRem = 0;
    $totalAsig = 0;
    $totalDesc = 0;

    while ($row = $res->fetch_assoc()) {
        $codigo = (int)$row['codigo'];
        $monto = (float)$row['monto'];

        if ($codigo >= 101 && $codigo <= 199) {
            if ($codigo == 112) {
                $totalNoRem += $monto;
            } else {
                $totalRem += $monto;
            }
        } elseif ($codigo >= 201 && $codigo <= 299) {
            $totalAsig += $monto;
        } elseif ($codigo >= 301 && $codigo <= 399) {
            $totalDesc += $monto;
        }
    }

    $neto = ($totalRem + $totalNoRem + $totalAsig) - $totalDesc;

    $check = $conexion->prepare("
        SELECT id
        FROM liquidacion_empleado
        WHERE liquidacion_id = ? AND empleado_id = ?
        LIMIT 1
    ");
    $check->bind_param("ii", $liquidacionId, $empleadoId);
    $check->execute();
    $existe = $check->get_result()->fetch_assoc();

    if ($existe) {
        $upd = $conexion->prepare("
            UPDATE liquidacion_empleado
            SET total_remunerativo = ?,
                total_descuentos = ?,
                total_no_remunerativo = ?,
                total_asignaciones = ?,
                neto = ?
            WHERE liquidacion_id = ? AND empleado_id = ?
        ");
        $upd->bind_param("dddddii", $totalRem, $totalDesc, $totalNoRem, $totalAsig, $neto, $liquidacionId, $empleadoId);
        $upd->execute();
    } else {
        $ins = $conexion->prepare("
            INSERT INTO liquidacion_empleado
            (liquidacion_id, empleado_id, total_remunerativo, total_descuentos, total_no_remunerativo, total_asignaciones, neto)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $ins->bind_param("iiddddd", $liquidacionId, $empleadoId, $totalRem, $totalDesc, $totalNoRem, $totalAsig, $neto);
        $ins->execute();
    }
}

/*
|--------------------------------------------------------------------------
| DATOS DE LIQUIDACIÓN
|--------------------------------------------------------------------------
*/
$stmtLiq = $conexion->prepare("
    SELECT id, tipo_liquidacion, periodo, estado
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

$soloLectura = ($liquidacion['estado'] === 'ANULADA');

if (isset($_GET['ok'])) {
    if ($_GET['ok'] == '1') {
        $mensaje = "Concepto manual agregado correctamente.";
    } elseif ($_GET['ok'] == '2') {
        $mensaje = "Concepto manual eliminado correctamente.";
    } elseif ($_GET['ok'] == '3') {
        $mensaje = "Concepto manual actualizado correctamente.";
    }
}

if ($soloLectura) {
    $error = "La liquidación está ANULADA. No se pueden agregar, editar ni eliminar conceptos manuales.";
}

/*
|--------------------------------------------------------------------------
| DATOS PARA EDICIÓN
|--------------------------------------------------------------------------
*/
$registroEditar = [
    'id' => 0,
    'empleado_id' => '',
    'concepto_id' => '',
    'cantidad' => 1,
    'porcentaje_aplicado' => 0,
    'monto' => '',
    'observacion' => ''
];

if ($detalleEditarId > 0) {
    $stmtEdit = $conexion->prepare("
        SELECT id, empleado_id, concepto_id, cantidad, porcentaje_aplicado, monto, observacion
        FROM liquidacion_detalle
        WHERE id = ? AND liquidacion_id = ? AND es_manual = 1
        LIMIT 1
    ");
    $stmtEdit->bind_param("ii", $detalleEditarId, $liquidacionId);
    $stmtEdit->execute();
    $rowEdit = $stmtEdit->get_result()->fetch_assoc();

    if ($rowEdit) {
        $registroEditar = $rowEdit;
    }
}

/*
|--------------------------------------------------------------------------
| ELIMINAR
|--------------------------------------------------------------------------
*/
if (isset($_GET['eliminar_id']) && !$soloLectura) {
    $eliminarId = (int)$_GET['eliminar_id'];

    $stmtBuscar = $conexion->prepare("
        SELECT empleado_id
        FROM liquidacion_detalle
        WHERE id = ? AND liquidacion_id = ? AND es_manual = 1
        LIMIT 1
    ");
    $stmtBuscar->bind_param("ii", $eliminarId, $liquidacionId);
    $stmtBuscar->execute();
    $filaEliminar = $stmtBuscar->get_result()->fetch_assoc();

    if ($filaEliminar) {
        $empleadoIdEliminar = (int)$filaEliminar['empleado_id'];

        $stmtDel = $conexion->prepare("
            DELETE FROM liquidacion_detalle
            WHERE id = ? AND liquidacion_id = ? AND es_manual = 1
        ");
        $stmtDel->bind_param("ii", $eliminarId, $liquidacionId);

        if ($stmtDel->execute()) {
            recalcularResumenEmpleado($conexion, $liquidacionId, $empleadoIdEliminar);
            header("Location: liquidacion_concepto_manual.php?liquidacion_id=".$liquidacionId."&ok=2");
            exit();
        } else {
            $error = "No se pudo eliminar el concepto manual.";
        }
    }
}

/*
|--------------------------------------------------------------------------
| GUARDAR / ACTUALIZAR
|--------------------------------------------------------------------------
*/
if ($_SERVER["REQUEST_METHOD"] === "POST" && !$soloLectura) {
    $detalleId = (int)$_POST['detalle_id'];
    $empleadoId = (int)$_POST['empleado_id'];
    $conceptoId = (int)$_POST['concepto_id'];
    $cantidad = (float)$_POST['cantidad'];
    $porcentaje = (float)$_POST['porcentaje_aplicado'];
    $monto = (float)$_POST['monto'];
    $observacion = trim($_POST['observacion']);

    if ($empleadoId <= 0 || $conceptoId <= 0 || $monto <= 0) {
        $error = "Complete correctamente los datos obligatorios.";
    } else {
        if ($cantidad <= 0) {
            $cantidad = 1;
        }

        if ($detalleId > 0) {
            $stmtEmpAnterior = $conexion->prepare("
                SELECT empleado_id
                FROM liquidacion_detalle
                WHERE id = ? AND liquidacion_id = ? AND es_manual = 1
                LIMIT 1
            ");
            $stmtEmpAnterior->bind_param("ii", $detalleId, $liquidacionId);
            $stmtEmpAnterior->execute();
            $empAnterior = $stmtEmpAnterior->get_result()->fetch_assoc();
            $empleadoAnteriorId = $empAnterior ? (int)$empAnterior['empleado_id'] : 0;

            $stmtUpd = $conexion->prepare("
                UPDATE liquidacion_detalle
                SET empleado_id = ?,
                    concepto_id = ?,
                    cantidad = ?,
                    porcentaje_aplicado = ?,
                    monto = ?,
                    observacion = ?
                WHERE id = ? AND liquidacion_id = ? AND es_manual = 1
            ");
            $stmtUpd->bind_param("iiddssii", $empleadoId, $conceptoId, $cantidad, $porcentaje, $monto, $observacion, $detalleId, $liquidacionId);

            if ($stmtUpd->execute()) {
                if ($empleadoAnteriorId > 0 && $empleadoAnteriorId !== $empleadoId) {
                    recalcularResumenEmpleado($conexion, $liquidacionId, $empleadoAnteriorId);
                }
                recalcularResumenEmpleado($conexion, $liquidacionId, $empleadoId);
                header("Location: liquidacion_concepto_manual.php?liquidacion_id=".$liquidacionId."&ok=3");
                exit();
            } else {
                $error = "Error al actualizar el concepto manual.";
            }
        } else {
            $stmtIns = $conexion->prepare("
                INSERT INTO liquidacion_detalle
                (liquidacion_id, empleado_id, concepto_id, cantidad, porcentaje_aplicado, monto, es_manual, observacion)
                VALUES (?, ?, ?, ?, ?, ?, 1, ?)
            ");
            $stmtIns->bind_param("iiiddss", $liquidacionId, $empleadoId, $conceptoId, $cantidad, $porcentaje, $monto, $observacion);

            if ($stmtIns->execute()) {
                recalcularResumenEmpleado($conexion, $liquidacionId, $empleadoId);
                header("Location: liquidacion_concepto_manual.php?liquidacion_id=".$liquidacionId."&ok=1");
                exit();
            } else {
                $error = "Error al guardar el concepto manual.";
            }
        }
    }
}

/*
|--------------------------------------------------------------------------
| EMPLEADOS
|--------------------------------------------------------------------------
*/
$resEmpleados = $conexion->query("
    SELECT 
        le.empleado_id,
        e.nro_legajo,
        e.apellido,
        e.nombre
    FROM liquidacion_empleado le
    INNER JOIN empleado e ON le.empleado_id = e.id
    WHERE le.liquidacion_id = {$liquidacionId}
    ORDER BY e.apellido, e.nombre
");

/*
|--------------------------------------------------------------------------
| CONCEPTOS
|--------------------------------------------------------------------------
*/
$resConceptos = $conexion->query("
    SELECT id, codigo, nombre
    FROM concepto
    ORDER BY CAST(codigo AS UNSIGNED), nombre
");

/*
|--------------------------------------------------------------------------
| MANUALES CARGADOS
|--------------------------------------------------------------------------
*/
$resManuales = $conexion->query("
    SELECT 
        ld.id,
        ld.empleado_id,
        e.apellido,
        e.nombre,
        c.codigo,
        c.nombre AS concepto_nombre,
        ld.cantidad,
        ld.porcentaje_aplicado,
        ld.monto,
        ld.observacion
    FROM liquidacion_detalle ld
    INNER JOIN empleado e ON ld.empleado_id = e.id
    INNER JOIN concepto c ON ld.concepto_id = c.id
    WHERE ld.liquidacion_id = {$liquidacionId}
      AND ld.es_manual = 1
    ORDER BY e.apellido, e.nombre, CAST(c.codigo AS UNSIGNED)
");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Conceptos Manuales</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f7fb;
            margin: 0;
        }
        .contenedor {
            max-width: 1200px;
            margin: 30px auto;
            background: white;
            padding: 25px;
            border-radius: 12px;
        }
        h2, h3 {
            color: #0f766e;
            margin-top: 0;
        }
        .acciones {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-block;
            padding: 10px 14px;
            border-radius: 8px;
            text-decoration: none;
            border: none;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }
        .btn-volver { background: #6b7280; }
        .btn-guardar { background: #0f766e; }
        .btn-editar { background: #2563eb; padding: 7px 10px; font-size: 12px; }
        .btn-eliminar { background: #dc2626; padding: 7px 10px; font-size: 12px; }
        .btn-cancelar { background: #9ca3af; }
        .mensaje {
            background: #dcfce7;
            color: #166534;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .error {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 12px;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 6px;
        }
        input, select, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
        }
        textarea {
            resize: vertical;
        }
        .bloque {
            margin-top: 25px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }
        th, td {
            border-bottom: 1px solid #e5e7eb;
            padding: 10px;
            text-align: left;
            font-size: 14px;
        }
        th {
            background: #f9fafb;
        }
        .solo-lectura {
            background: #fff7ed;
            color: #9a3412;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 15px;
            border: 1px solid #fdba74;
        }
        .acciones-tabla {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }
    </style>
</head>
<body>

<div class="contenedor">

    <div class="acciones">
        <a href="liquidacion_ver.php?id=<?php echo $liquidacionId; ?>" class="btn btn-volver">Volver a Liquidación</a>
    </div>

    <h2>Conceptos Manuales</h2>
    <p>
        <strong>Liquidación:</strong>
        <?php echo htmlspecialchars($liquidacion['tipo_liquidacion']); ?> |
        <strong>Período:</strong>
        <?php echo htmlspecialchars($liquidacion['periodo']); ?> |
        <strong>Estado:</strong>
        <?php echo htmlspecialchars($liquidacion['estado']); ?>
    </p>

    <?php if ($mensaje) { ?>
        <div class="mensaje"><?php echo htmlspecialchars($mensaje); ?></div>
    <?php } ?>

    <?php if ($error) { ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php } ?>

    <?php if ($soloLectura) { ?>
        <div class="solo-lectura">
            Esta liquidación está en modo solo lectura porque fue anulada.
        </div>
    <?php } ?>

    <?php if (!$soloLectura) { ?>
        <div class="bloque">
            <h3><?php echo ($registroEditar['id'] > 0) ? 'Editar Concepto Manual' : 'Agregar Concepto Manual'; ?></h3>

            <form method="POST">
                <input type="hidden" name="liquidacion_id" value="<?php echo $liquidacionId; ?>">
                <input type="hidden" name="detalle_id" value="<?php echo (int)$registroEditar['id']; ?>">

                <div class="form-grid">
                    <div>
                        <label>Empleado *</label>
                        <select name="empleado_id" required>
                            <option value="">Seleccione</option>
                            <?php while ($emp = $resEmpleados->fetch_assoc()) { ?>
                                <option value="<?php echo $emp['empleado_id']; ?>" <?php echo ((int)$registroEditar['empleado_id'] === (int)$emp['empleado_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars(($emp['nro_legajo'] ?: 'S/L') . ' - ' . $emp['apellido'] . ', ' . $emp['nombre']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div>
                        <label>Concepto *</label>
                        <select name="concepto_id" required>
                            <option value="">Seleccione</option>
                            <?php while ($con = $resConceptos->fetch_assoc()) { ?>
                                <option value="<?php echo $con['id']; ?>" <?php echo ((int)$registroEditar['concepto_id'] === (int)$con['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($con['codigo'] . ' - ' . $con['nombre']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div>
                        <label>Cantidad</label>
                        <input type="number" step="0.01" name="cantidad" value="<?php echo htmlspecialchars($registroEditar['cantidad']); ?>">
                    </div>

                    <div>
                        <label>% Aplicado</label>
                        <input type="number" step="0.01" name="porcentaje_aplicado" value="<?php echo htmlspecialchars($registroEditar['porcentaje_aplicado']); ?>">
                    </div>

                    <div>
                        <label>Monto *</label>
                        <input type="number" step="0.01" name="monto" value="<?php echo htmlspecialchars($registroEditar['monto']); ?>" required>
                    </div>

                    <div>
                        <label>Observación</label>
                        <textarea name="observacion" rows="2"><?php echo htmlspecialchars($registroEditar['observacion']); ?></textarea>
                    </div>
                </div>

                <div style="margin-top:15px; display:flex; gap:10px; flex-wrap:wrap;">
                    <button type="submit" class="btn btn-guardar">
                        <?php echo ($registroEditar['id'] > 0) ? 'Actualizar Concepto Manual' : 'Guardar Concepto Manual'; ?>
                    </button>

                    <?php if ($registroEditar['id'] > 0) { ?>
                        <a href="liquidacion_concepto_manual.php?liquidacion_id=<?php echo $liquidacionId; ?>" class="btn btn-cancelar">
                            Cancelar Edición
                        </a>
                    <?php } ?>
                </div>
            </form>
        </div>
    <?php } ?>

    <div class="bloque">
        <h3>Conceptos Manuales Cargados</h3>

        <table>
            <thead>
                <tr>
                    <th>Empleado</th>
                    <th>Código</th>
                    <th>Concepto</th>
                    <th>Cantidad</th>
                    <th>%</th>
                    <th>Monto</th>
                    <th>Observación</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($resManuales && $resManuales->num_rows > 0) { ?>
                    <?php while ($m = $resManuales->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($m['apellido'] . ', ' . $m['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($m['codigo']); ?></td>
                            <td><?php echo htmlspecialchars($m['concepto_nombre']); ?></td>
                            <td><?php echo number_format((float)$m['cantidad'], 2, ',', '.'); ?></td>
                            <td><?php echo number_format((float)$m['porcentaje_aplicado'], 2, ',', '.'); ?></td>
                            <td>$<?php echo number_format((float)$m['monto'], 2, ',', '.'); ?></td>
                            <td><?php echo htmlspecialchars($m['observacion'] ?: '-'); ?></td>
                            <td>
                                <?php if (!$soloLectura) { ?>
                                    <div class="acciones-tabla">
                                        <a href="liquidacion_concepto_manual.php?liquidacion_id=<?php echo $liquidacionId; ?>&editar_id=<?php echo $m['id']; ?>" class="btn btn-editar">
                                            Editar
                                        </a>

                                        <a href="liquidacion_concepto_manual.php?liquidacion_id=<?php echo $liquidacionId; ?>&eliminar_id=<?php echo $m['id']; ?>"
                                           class="btn btn-eliminar"
                                           onclick="return confirm('¿Eliminar este concepto manual?');">
                                            Eliminar
                                        </a>
                                    </div>
                                <?php } else { ?>
                                    <span style="color:#6b7280;">Bloqueado</span>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="8">No hay conceptos manuales cargados.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>