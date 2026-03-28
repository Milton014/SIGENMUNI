<?php
session_start();
require_once("conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

mysqli_report(MYSQLI_REPORT_OFF);

function calcularAntiguedadAnios($fechaAlta) {
    if (empty($fechaAlta) || $fechaAlta === '0000-00-00') {
        return 0;
    }

    try {
        $inicio = new DateTime($fechaAlta);
        $hoy = new DateTime();
        return $inicio->diff($hoy)->y;
    } catch (Exception $e) {
        return 0;
    }
}

function tableExists($conexion, $tableName) {
    $tableName = $conexion->real_escape_string($tableName);
    $sql = "SHOW TABLES LIKE '{$tableName}'";
    $res = $conexion->query($sql);
    return ($res && $res->num_rows > 0);
}

function columnExists($conexion, $tableName, $columnName) {
    $tableName = $conexion->real_escape_string($tableName);
    $columnName = $conexion->real_escape_string($columnName);
    $sql = "SHOW COLUMNS FROM `{$tableName}` LIKE '{$columnName}'";
    $res = $conexion->query($sql);
    return ($res && $res->num_rows > 0);
}

function obtenerConceptosPorCodigo($conexion) {
    $mapa = [];

    $sql = "SELECT id, codigo, nombre FROM concepto";
    $res = $conexion->query($sql);

    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $codigo = (string)$row['codigo'];
            $mapa[$codigo] = [
                'id' => (int)$row['id'],
                'nombre' => $row['nombre']
            ];
        }
    }

    return $mapa;
}

function insertarDetalle($stmt, $liquidacionId, $empleadoId, $conceptoId, $cantidad, $porcentaje, $monto, $esManual, $observacion) {
    $stmt->bind_param(
        "iiidddis",
        $liquidacionId,
        $empleadoId,
        $conceptoId,
        $cantidad,
        $porcentaje,
        $monto,
        $esManual,
        $observacion
    );

    return $stmt->execute();
}

function agregarConceptoDetalle(&$items, $codigo, $monto, $cantidad = 1, $porcentaje = 0, $esManual = 0, $observacion = '') {
    if ($monto <= 0) {
        return;
    }

    $items[] = [
        'codigo' => (string)$codigo,
        'monto' => round((float)$monto, 2),
        'cantidad' => (float)$cantidad,
        'porcentaje' => (float)$porcentaje,
        'es_manual' => (int)$esManual,
        'observacion' => $observacion
    ];
}

function obtenerEscalaValores($conexion, $categoriaId, $anio, $empleado) {
    $valores = [
        'sueldo_basico' => 0,
        'dedicacion_funcional' => 0,
        'suplemento_especial' => 0,
        'titulo' => 0,
        'otros_remunerativos' => 0,
        'no_remunerativo' => 0,
        'asignacion_hijo' => 0,
        'asignacion_hijo_discapacitado' => 0,
        'prenatal' => 0,
        'ayuda_escolar' => 0,
        'ayuda_escolar_discapacidad' => 0,
        'nacimiento' => 0,
        'adopcion' => 0,
        'matrimonio' => 0,
        'otra_asignacion' => 0
    ];

    if (tableExists($conexion, 'escala_salarial')) {
        $sql = "
            SELECT *
            FROM escala_salarial
            WHERE categoria_id = ? AND anio = ? AND activo = 1
            ORDER BY id DESC
            LIMIT 1
        ";
        $stmt = $conexion->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ii", $categoriaId, $anio);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();

            if ($row) {
                foreach ($valores as $campo => $valorDefault) {
                    if (isset($row[$campo])) {
                        $valores[$campo] = (float)$row[$campo];
                    }
                }

                if (isset($row['otros_remunerativos'])) {
                    $valores['otros_remunerativos'] = (float)$row['otros_remunerativos'];
                }
                if (isset($row['otras_asignaciones'])) {
                    $valores['otra_asignacion'] = (float)$row['otras_asignaciones'];
                }
                return $valores;
            }
        }
    }

    // Fallback usando categoría / empleado
    $valores['sueldo_basico'] = isset($empleado['sueldo_basico']) ? (float)$empleado['sueldo_basico'] : 0;
    $valores['dedicacion_funcional'] = isset($empleado['dedicacion_funcional']) ? (float)$empleado['dedicacion_funcional'] : 0;
    $valores['suplemento_especial'] = isset($empleado['suplemento_especial']) ? (float)$empleado['suplemento_especial'] : 0;

    if (isset($empleado['titulo'])) {
        $valores['titulo'] = (float)$empleado['titulo'];
    }
    if (isset($empleado['otros_remunerativos'])) {
        $valores['otros_remunerativos'] = (float)$empleado['otros_remunerativos'];
    }
    if (isset($empleado['no_remunerativo'])) {
        $valores['no_remunerativo'] = (float)$empleado['no_remunerativo'];
    }

    return $valores;
}

function obtenerConceptosFijosEmpleado($conexion, $empleadoId) {
    $items = [];

    if (!tableExists($conexion, 'empleado_concepto')) {
        return $items;
    }

    $campos = [
        'empleado_id' => columnExists($conexion, 'empleado_concepto', 'empleado_id'),
        'concepto_id' => columnExists($conexion, 'empleado_concepto', 'concepto_id'),
        'importe' => columnExists($conexion, 'empleado_concepto', 'importe'),
        'cantidad' => columnExists($conexion, 'empleado_concepto', 'cantidad'),
        'activo' => columnExists($conexion, 'empleado_concepto', 'activo')
    ];

    if (!$campos['empleado_id'] || !$campos['concepto_id'] || !$campos['importe']) {
        return $items;
    }

    $sql = "
        SELECT 
            concepto_id,
            importe" . ($campos['cantidad'] ? ", cantidad" : ", 1 AS cantidad") . "
        FROM empleado_concepto
        WHERE empleado_id = ?" . ($campos['activo'] ? " AND activo = 1" : "");

    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        return $items;
    }

    $stmt->bind_param("i", $empleadoId);
    $stmt->execute();
    $res = $stmt->get_result();

    while ($row = $res->fetch_assoc()) {
        $items[] = [
            'concepto_id' => (int)$row['concepto_id'],
            'importe' => (float)$row['importe'],
            'cantidad' => (float)$row['cantidad']
        ];
    }

    return $items;
}

function obtenerAsignacionesFamiliares($conexion, $empleadoId) {
    $items = [];

    if (!tableExists($conexion, 'empleado_asignacion_familiar')) {
        return $items;
    }

    $campos = [
        'empleado_id' => columnExists($conexion, 'empleado_asignacion_familiar', 'empleado_id'),
        'concepto_id' => columnExists($conexion, 'empleado_asignacion_familiar', 'concepto_id'),
        'cantidad' => columnExists($conexion, 'empleado_asignacion_familiar', 'cantidad'),
        'activo' => columnExists($conexion, 'empleado_asignacion_familiar', 'activo')
    ];

    if (!$campos['empleado_id'] || !$campos['concepto_id']) {
        return $items;
    }

    $sql = "
        SELECT 
            concepto_id,
            " . ($campos['cantidad'] ? "cantidad" : "1 AS cantidad") . "
        FROM empleado_asignacion_familiar
        WHERE empleado_id = ?" . ($campos['activo'] ? " AND activo = 1" : "");

    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        return $items;
    }

    $stmt->bind_param("i", $empleadoId);
    $stmt->execute();
    $res = $stmt->get_result();

    while ($row = $res->fetch_assoc()) {
        $items[] = [
            'concepto_id' => (int)$row['concepto_id'],
            'cantidad' => (float)$row['cantidad']
        ];
    }

    return $items;
}

function obtenerCodigoDesdeConceptoId($conceptos, $conceptoId) {
    foreach ($conceptos as $codigo => $data) {
        if ((int)$data['id'] === (int)$conceptoId) {
            return (string)$codigo;
        }
    }
    return null;
}

$liquidacionId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$error = "";

if ($liquidacionId <= 0) {
    die("ID de liquidación inválido.");
}

$stmtLiq = $conexion->prepare("
    SELECT id, tipo_liquidacion, periodo, fecha_liquidacion, descripcion, estado
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

if ($liquidacion['estado'] !== 'BORRADOR') {
    die("La liquidación ya no está en estado BORRADOR.");
}

$anioLiquidacion = (int)date('Y', strtotime($liquidacion['fecha_liquidacion']));
$conceptos = obtenerConceptosPorCodigo($conexion);

$sqlEmpleados = "
    SELECT 
        e.*,
        c.codigo AS categoria_codigo,
        c.nombre AS categoria_nombre,
        c.sueldo_basico,
        c.dedicacion_funcional,
        c.suplemento_especial
    FROM empleado e
    INNER JOIN categoria c ON e.categoria_id = c.id
";

if (columnExists($conexion, 'empleado', 'activo')) {
    $sqlEmpleados .= " WHERE e.activo = 1";
}

$sqlEmpleados .= " ORDER BY e.apellido, e.nombre";

$resEmpleados = $conexion->query($sqlEmpleados);

if (!$resEmpleados || $resEmpleados->num_rows === 0) {
    die("No hay empleados para procesar.");
}

try {
    $conexion->begin_transaction();

    $stmtDeleteDetalle = $conexion->prepare("DELETE FROM liquidacion_detalle WHERE liquidacion_id = ?");
    $stmtDeleteDetalle->bind_param("i", $liquidacionId);
    $stmtDeleteDetalle->execute();

    $stmtDeleteResumen = $conexion->prepare("DELETE FROM liquidacion_empleado WHERE liquidacion_id = ?");
    $stmtDeleteResumen->bind_param("i", $liquidacionId);
    $stmtDeleteResumen->execute();

    $stmtDetalle = $conexion->prepare("
        INSERT INTO liquidacion_detalle
        (liquidacion_id, empleado_id, concepto_id, cantidad, porcentaje_aplicado, monto, es_manual, observacion)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    if (!$stmtDetalle) {
        throw new Exception("No se pudo preparar el insert de liquidacion_detalle.");
    }

    $stmtResumen = $conexion->prepare("
        INSERT INTO liquidacion_empleado
        (liquidacion_id, empleado_id, total_remunerativo, total_descuentos, total_no_remunerativo, total_asignaciones, neto)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    if (!$stmtResumen) {
        throw new Exception("No se pudo preparar el insert de liquidacion_empleado.");
    }

    while ($empleado = $resEmpleados->fetch_assoc()) {
        $empleadoId = (int)$empleado['id'];
        $categoriaNumero = (int)$empleado['categoria_codigo'];
        $antiguedadAnios = calcularAntiguedadAnios($empleado['fecha_alta'] ?? null);

        $escala = obtenerEscalaValores($conexion, (int)$empleado['categoria_id'], $anioLiquidacion, $empleado);

        $sueldoBasico = (float)$escala['sueldo_basico'];
        $dedicacion = (float)$escala['dedicacion_funcional'];
        $suplemento = (float)$escala['suplemento_especial'];
        $titulo = (float)$escala['titulo'];
        $otrosRemunerativos = (float)$escala['otros_remunerativos'];
        $noRemunerativo = (float)$escala['no_remunerativo'];

        // Fórmulas principales
        if ($dedicacion <= 0 && $sueldoBasico > 0) {
            $dedicacion = $sueldoBasico; // 102
        }

        $adicionalJerarquico = ($sueldoBasico + $dedicacion) * 0.10; // 103
        $antiguedad = ($sueldoBasico * 0.02) * $antiguedadAnios;     // 108
        $presentismo = ($sueldoBasico + $dedicacion + $suplemento) * 0.15; // 109

        $detalleItems = [];

        // Remunerativos base
        agregarConceptoDetalle($detalleItems, '101', $sueldoBasico, 1, 0, 0, 'Sueldo básico');
        agregarConceptoDetalle($detalleItems, '102', $dedicacion, 1, 0, 0, 'Dedicación funcional');
        agregarConceptoDetalle($detalleItems, '103', $adicionalJerarquico, 1, 10, 0, '10% sobre básico + dedicación funcional');
        agregarConceptoDetalle($detalleItems, '104', $suplemento, 1, 0, 0, 'Suplemento especial');
        agregarConceptoDetalle($detalleItems, '108', $antiguedad, $antiguedadAnios, $antiguedadAnios * 2, 0, '2% por año de antigüedad');
        agregarConceptoDetalle($detalleItems, '109', $presentismo, 1, 15, 0, '15% sobre básico + dedicación + suplemento');
        agregarConceptoDetalle($detalleItems, '110', $titulo, 1, 0, 0, 'Título');
        agregarConceptoDetalle($detalleItems, '111', $otrosRemunerativos, 1, 0, 0, 'Otros remunerativos');
        agregarConceptoDetalle($detalleItems, '112', $noRemunerativo, 1, 0, 0, 'No remunerativo');

        // Conceptos fijos del empleado
        $conceptosFijos = obtenerConceptosFijosEmpleado($conexion, $empleadoId);
        foreach ($conceptosFijos as $cf) {
            $codigo = obtenerCodigoDesdeConceptoId($conceptos, $cf['concepto_id']);
            if ($codigo === null) {
                continue;
            }

            agregarConceptoDetalle(
                $detalleItems,
                $codigo,
                (float)$cf['importe'],
                (float)$cf['cantidad'],
                0,
                1,
                'Concepto fijo del empleado'
            );
        }

        // Total remunerativo antes de descuentos
        $codigosRemunerativos = ['101','102','103','104','105','106','107','108','109','110','111'];
        $totalRemunerativo = 0;

        foreach ($detalleItems as $item) {
            if (in_array($item['codigo'], $codigosRemunerativos, true)) {
                $totalRemunerativo += (float)$item['monto'];
            }
        }

        // Asignaciones familiares
        $mapaAsignacionesEscala = [
            '201' => (float)$escala['asignacion_hijo'],
            '202' => (float)$escala['asignacion_hijo_discapacitado'],
            '203' => (float)$escala['prenatal'],
            '204' => (float)$escala['ayuda_escolar'],
            '205' => (float)$escala['ayuda_escolar_discapacidad'],
            '206' => (float)$escala['nacimiento'],
            '207' => (float)$escala['adopcion'],
            '208' => (float)$escala['matrimonio'],
            '209' => (float)$escala['otra_asignacion']
        ];

        $asignaciones = obtenerAsignacionesFamiliares($conexion, $empleadoId);
        foreach ($asignaciones as $asig) {
            $codigo = obtenerCodigoDesdeConceptoId($conceptos, $asig['concepto_id']);
            if ($codigo === null || !isset($mapaAsignacionesEscala[$codigo])) {
                continue;
            }

            $cantidad = max(1, (float)$asig['cantidad']);
            $montoUnitario = (float)$mapaAsignacionesEscala[$codigo];
            $montoTotal = $montoUnitario * $cantidad;

            agregarConceptoDetalle(
                $detalleItems,
                $codigo,
                $montoTotal,
                $cantidad,
                0,
                0,
                'Asignación familiar'
            );
        }

        // Descuentos personales
        $caja = $totalRemunerativo * 0.11; // 301
        $obraSocial = $totalRemunerativo * 0.05; // 302
        $sepelio = $totalRemunerativo * 0.01; // 303
        $voluntario = $totalRemunerativo * 0.08; // 304
        $ips1 = ($categoriaNumero <= 21) ? ($totalRemunerativo * 0.01) : 0; // 306
        $ips2 = ($categoriaNumero <= 22) ? ($totalRemunerativo * 0.02) : 0; // 307

        agregarConceptoDetalle($detalleItems, '301', $caja, 1, 11, 0, '11% Caja de Previsión Social');
        agregarConceptoDetalle($detalleItems, '302', $obraSocial, 1, 5, 0, '5% IASEP Obra Social');
        agregarConceptoDetalle($detalleItems, '303', $sepelio, 1, 1, 0, '1% IASEP Sepelio');
        agregarConceptoDetalle($detalleItems, '304', $voluntario, 1, 8, 0, '8% IASEP Voluntario');
        agregarConceptoDetalle($detalleItems, '306', $ips1, 1, 1, 0, '1% IPS para categoría 21 o inferior');
        agregarConceptoDetalle($detalleItems, '307', $ips2, 1, 2, 0, '2% IPS para categoría 22 o inferior');

        // Aportes patronales
        $patronalCaja = $totalRemunerativo * 0.16;  // 401
        $patronalObra = $totalRemunerativo * 0.04;  // 402
        $patronalIps  = $totalRemunerativo * 0.02;  // 403

        agregarConceptoDetalle($detalleItems, '401', $patronalCaja, 1, 16, 0, '16% Caja patronal');
        agregarConceptoDetalle($detalleItems, '402', $patronalObra, 1, 4, 0, '4% Obra social patronal');
        agregarConceptoDetalle($detalleItems, '403', $patronalIps, 1, 2, 0, '2% IPS patronal');

        // Totales
        $totalNoRemunerativo = 0;
        $totalAsignaciones = 0;
        $totalDescuentos = 0;

        foreach ($detalleItems as $item) {
            $codigo = $item['codigo'];
            $monto = (float)$item['monto'];

            if ($codigo === '112') {
                $totalNoRemunerativo += $monto;
            }

            if ((int)$codigo >= 201 && (int)$codigo <= 209) {
                $totalAsignaciones += $monto;
            }

            if ((int)$codigo >= 301 && (int)$codigo <= 399) {
                $totalDescuentos += $monto;
            }
        }

        $neto = $totalRemunerativo - $totalDescuentos + $totalNoRemunerativo + $totalAsignaciones;

        // Guardar detalle
        foreach ($detalleItems as $item) {
            if (!isset($conceptos[$item['codigo']])) {
                continue;
            }

            $conceptoId = (int)$conceptos[$item['codigo']]['id'];

            if (!insertarDetalle(
                $stmtDetalle,
                $liquidacionId,
                $empleadoId,
                $conceptoId,
                $item['cantidad'],
                $item['porcentaje'],
                $item['monto'],
                $item['es_manual'],
                $item['observacion']
            )) {
                throw new Exception("Error al insertar detalle del empleado ID {$empleadoId}, concepto {$item['codigo']}");
            }
        }

        // Guardar resumen
        $stmtResumen->bind_param(
            "iiddddd",
            $liquidacionId,
            $empleadoId,
            $totalRemunerativo,
            $totalDescuentos,
            $totalNoRemunerativo,
            $totalAsignaciones,
            $neto
        );

        if (!$stmtResumen->execute()) {
            throw new Exception("Error al guardar el resumen del empleado ID {$empleadoId}");
        }
    }

    $stmtCerrar = $conexion->prepare("
        UPDATE liquidacion
        SET estado = 'CERRADA'
        WHERE id = ?
    ");
    $stmtCerrar->bind_param("i", $liquidacionId);

    if (!$stmtCerrar->execute()) {
        throw new Exception("No se pudo cerrar la liquidación.");
    }

    $conexion->commit();
    header("Location: liquidacion.php?ok=2");
    exit();

} catch (Exception $e) {
    $conexion->rollback();
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procesar Liquidación</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f7fb;
            margin: 0;
        }
        .contenedor {
            max-width: 900px;
            margin: 40px auto;
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 14px rgba(0,0,0,0.08);
        }
        h2 {
            margin-top: 0;
            color: #0f766e;
        }
        .error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
            padding: 14px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .btn {
            display: inline-block;
            padding: 10px 16px;
            border-radius: 8px;
            text-decoration: none;
            color: white;
            background: #6b7280;
        }
        .btn:hover {
            background: #4b5563;
        }
    </style>
</head>
<body>
<div class="contenedor">
    <h2>Procesar Liquidación</h2>

    <?php if ($error != "") { ?>
        <div class="error">
            <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
        </div>
    <?php } else { ?>
        <p>Procesando liquidación...</p>
    <?php } ?>

    <a href="liquidacion.php" class="btn">Volver</a>
</div>
</body>
</html>