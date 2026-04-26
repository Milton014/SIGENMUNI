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

    $sql = "
        SELECT 
            id,
            codigo,
            nombre,
            categoria,
            forma_calculo,
            porcentaje,
            monto_fijo,
            requiere_manual,
            base_calculo,
            aplica_sac,
            activo
        FROM concepto
        WHERE activo = 1
    ";
    $res = $conexion->query($sql);

    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $codigo = (string)$row['codigo'];
            $mapa[$codigo] = [
                'id' => (int)$row['id'],
                'nombre' => $row['nombre'],
                'categoria' => $row['categoria'],
                'forma_calculo' => $row['forma_calculo'],
                'porcentaje' => (float)$row['porcentaje'],
                'monto_fijo' => (float)$row['monto_fijo'],
                'requiere_manual' => (int)$row['requiere_manual'],
                'base_calculo' => $row['base_calculo'],
                'aplica_sac' => (int)$row['aplica_sac']
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

function calcularConceptoAutomatico($concepto, $bases = []) {
    if (!$concepto || !is_array($concepto)) {
        return 0;
    }

    $formaCalculo = strtoupper(trim($concepto['forma_calculo'] ?? ''));
    $baseCalculo = strtoupper(trim($concepto['base_calculo'] ?? ''));
    $porcentaje = (float)($concepto['porcentaje'] ?? 0);
    $montoFijo = (float)($concepto['monto_fijo'] ?? 0);

    if ($formaCalculo === 'FIJO') {
        return round($montoFijo, 2);
    }

    if ($formaCalculo === 'PORCENTAJE') {
        $base = 0;

        switch ($baseCalculo) {
            case 'TOTAL_REMUNERATIVO':
                $base = (float)($bases['TOTAL_REMUNERATIVO'] ?? 0);
                break;
            case 'TOTAL_SAC':
                $base = (float)($bases['TOTAL_SAC'] ?? 0);
                break;
            case 'BASICO':
                $base = (float)($bases['BASICO'] ?? 0);
                break;
            case 'BASICO_MAS_DEDICACION':
                $base = (float)($bases['BASICO_MAS_DEDICACION'] ?? 0);
                break;
            case 'BASICO_MAS_DEDICACION_MAS_SUPLEMENTO':
                $base = (float)($bases['BASICO_MAS_DEDICACION_MAS_SUPLEMENTO'] ?? 0);
                break;
            default:
                $base = 0;
                break;
        }

        return round($base * ($porcentaje / 100), 2);
    }

    return 0;
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

function obtenerConceptosFijosEmpleado($conexion, $empleadoId, $fechaLiquidacion) {
    $items = [];

    if (!tableExists($conexion, 'empleado_concepto')) {
        return $items;
    }

    $campos = [
        'empleado_id' => columnExists($conexion, 'empleado_concepto', 'empleado_id'),
        'concepto_id' => columnExists($conexion, 'empleado_concepto', 'concepto_id'),
        'monto_manual' => columnExists($conexion, 'empleado_concepto', 'monto_manual'),
        'porcentaje_manual' => columnExists($conexion, 'empleado_concepto', 'porcentaje_manual'),
        'cantidad' => columnExists($conexion, 'empleado_concepto', 'cantidad'),
        'fecha_desde' => columnExists($conexion, 'empleado_concepto', 'fecha_desde'),
        'fecha_hasta' => columnExists($conexion, 'empleado_concepto', 'fecha_hasta'),
        'activo' => columnExists($conexion, 'empleado_concepto', 'activo'),
        'observacion' => columnExists($conexion, 'empleado_concepto', 'observacion')
    ];

    if (!$campos['empleado_id'] || !$campos['concepto_id']) {
        return $items;
    }

    $sql = "
        SELECT 
            concepto_id,
            " . ($campos['monto_manual'] ? "monto_manual" : "0") . " AS monto_manual,
            " . ($campos['porcentaje_manual'] ? "porcentaje_manual" : "0") . " AS porcentaje_manual,
            " . ($campos['cantidad'] ? "cantidad" : "1") . " AS cantidad,
            " . ($campos['observacion'] ? "observacion" : "''") . " AS observacion
        FROM empleado_concepto
        WHERE empleado_id = ?
    ";

    if ($campos['activo']) {
        $sql .= " AND activo = 1";
    }

    if ($campos['fecha_desde']) {
        $sql .= " AND (fecha_desde IS NULL OR fecha_desde <= ?)";
    }

    if ($campos['fecha_hasta']) {
        $sql .= " AND (fecha_hasta IS NULL OR fecha_hasta >= ?)";
    }

    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        return $items;
    }

    if ($campos['fecha_desde'] && $campos['fecha_hasta']) {
        $stmt->bind_param("iss", $empleadoId, $fechaLiquidacion, $fechaLiquidacion);
    } elseif ($campos['fecha_desde']) {
        $stmt->bind_param("is", $empleadoId, $fechaLiquidacion);
    } elseif ($campos['fecha_hasta']) {
        $stmt->bind_param("is", $empleadoId, $fechaLiquidacion);
    } else {
        $stmt->bind_param("i", $empleadoId);
    }

    $stmt->execute();
    $res = $stmt->get_result();

    while ($row = $res->fetch_assoc()) {
        $items[] = [
            'concepto_id' => (int)$row['concepto_id'],
            'monto_manual' => (float)$row['monto_manual'],
            'porcentaje_manual' => (float)$row['porcentaje_manual'],
            'cantidad' => (float)$row['cantidad'],
            'observacion' => $row['observacion']
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

function buscarConceptoEmpleadoPorCodigo($conceptosFijos, $conceptos, $codigoBuscado) {
    if (!isset($conceptos[$codigoBuscado])) {
        return null;
    }

    $conceptoIdBuscado = (int)$conceptos[$codigoBuscado]['id'];

    foreach ($conceptosFijos as $cf) {
        if ((int)$cf['concepto_id'] === $conceptoIdBuscado) {
            return $cf;
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

$tipoLiquidacion = strtoupper(trim((string)$liquidacion['tipo_liquidacion']));
$esSAC = in_array($tipoLiquidacion, ['SAC', 'AGUINALDO'], true);

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

        if ($dedicacion <= 0 && $sueldoBasico > 0) {
            $dedicacion = $sueldoBasico;
        }

        $categoriasConJerarquica = [1, 2, 3, 4, 5, 6, 7, 8];
        $correspondeJerarquica = in_array($categoriaNumero, $categoriasConJerarquica, true);

        $adicionalJerarquico = 0;
        if ($correspondeJerarquica) {
            $adicionalJerarquico = ($sueldoBasico + $dedicacion) * 0.10;
        }

        $antiguedad = ($sueldoBasico * 0.02) * $antiguedadAnios;
        $presentismo = ($sueldoBasico + $dedicacion + $suplemento) * 0.15;

        $conceptosFijos = obtenerConceptosFijosEmpleado($conexion, $empleadoId, $liquidacion['fecha_liquidacion']);

        // Base remunerativa común para mensual y aguinaldo
        $detalleRemunerativosBase = [];
        agregarConceptoDetalle($detalleRemunerativosBase, '101', $sueldoBasico, 1, 0, 0, 'Sueldo básico');
        agregarConceptoDetalle($detalleRemunerativosBase, '102', $dedicacion, 1, 0, 0, 'Dedicación funcional');
        agregarConceptoDetalle($detalleRemunerativosBase, '103', $adicionalJerarquico, 1, 10, 0, '10% sobre básico + dedicación funcional');
        agregarConceptoDetalle($detalleRemunerativosBase, '104', $suplemento, 1, 0, 0, 'Suplemento especial');
        agregarConceptoDetalle($detalleRemunerativosBase, '108', $antiguedad, $antiguedadAnios, $antiguedadAnios * 2, 0, '2% por año de antigüedad');
        agregarConceptoDetalle($detalleRemunerativosBase, '109', $presentismo, 1, 15, 0, '15% sobre básico + dedicación + suplemento');
        agregarConceptoDetalle($detalleRemunerativosBase, '110', $titulo, 1, 0, 0, 'Título');
        agregarConceptoDetalle($detalleRemunerativosBase, '111', $otrosRemunerativos, 1, 0, 0, 'Otros remunerativos');

        foreach ($conceptosFijos as $cf) {
            $codigo = obtenerCodigoDesdeConceptoId($conceptos, $cf['concepto_id']);
            if ($codigo === null) {
                continue;
            }

            if (!isset($conceptos[$codigo])) {
                continue;
            }

            if ($codigo === '308' || $codigo === '310') {
                continue;
            }

            $categoriaConcepto = $conceptos[$codigo]['categoria'] ?? '';

            if ($categoriaConcepto !== 'REMUNERATIVO' && $categoriaConcepto !== 'NO_REMUNERATIVO') {
                continue;
            }

            $monto = (float)$cf['monto_manual'];
            $porcentaje = (float)$cf['porcentaje_manual'];
            $cantidad = ((float)$cf['cantidad'] > 0) ? (float)$cf['cantidad'] : 1;
            $observacion = !empty($cf['observacion']) ? $cf['observacion'] : 'Concepto asignado al empleado';

            if ($monto > 0) {
                agregarConceptoDetalle(
                    $detalleRemunerativosBase,
                    $codigo,
                    $monto,
                    $cantidad,
                    $porcentaje,
                    1,
                    $observacion
                );
                continue;
            }

            if ($porcentaje > 0) {
                $basePorcentaje = $sueldoBasico + $dedicacion + $suplemento;
                $montoCalculado = $basePorcentaje * ($porcentaje / 100);

                agregarConceptoDetalle(
                    $detalleRemunerativosBase,
                    $codigo,
                    $montoCalculado,
                    $cantidad,
                    $porcentaje,
                    1,
                    $observacion
                );
            }
        }

        $baseSac = 0;
        $detalleItems = [];

        foreach ($detalleRemunerativosBase as $itemBase) {
            $codigoBase = $itemBase['codigo'];
            if ($codigoBase !== '112') {
                $baseSac += (float)$itemBase['monto'];
            }
        }

        if ($esSAC) {
            $sacBruto = round($baseSac * 0.50, 2);

            agregarConceptoDetalle(
                $detalleItems,
                '150',
                $sacBruto,
                1,
                50,
                0,
                'Sueldo Anual Complementario - 50% de conceptos remunerativos'
            );

            $cajaSac = round($sacBruto * 0.11, 2); // 301
            $ips1Sac = ($categoriaNumero <= 21) ? round($sacBruto * 0.01, 2) : 0; // 306
            $ips2Sac = ($categoriaNumero <= 22) ? round($sacBruto * 0.02, 2) : 0; // 307

            $gremioSac = 0;
            $porcentajeGremioSac = 0;
            $conceptoGremioEmpleado = buscarConceptoEmpleadoPorCodigo($conceptosFijos, $conceptos, '308');

            if ($conceptoGremioEmpleado !== null && isset($conceptos['308'])) {
                if ((float)$conceptoGremioEmpleado['porcentaje_manual'] > 0) {
                    $porcentajeGremioSac = (float)$conceptoGremioEmpleado['porcentaje_manual'];
                    $gremioSac = round($sacBruto * ($porcentajeGremioSac / 100), 2);
                } elseif ((float)$conceptoGremioEmpleado['monto_manual'] > 0) {
                    $gremioSac = (float)$conceptoGremioEmpleado['monto_manual'];
                    $porcentajeGremioSac = 0;
                } else {
                    $basesSac = ['TOTAL_SAC' => $sacBruto, 'TOTAL_REMUNERATIVO' => $sacBruto];
                    $gremioSac = calcularConceptoAutomatico($conceptos['308'], $basesSac);
                    $porcentajeGremioSac = (float)$conceptos['308']['porcentaje'];
                }
            }

            $baseEmbargoSac = $sacBruto - ($cajaSac + $ips1Sac + $ips2Sac);
            if ($baseEmbargoSac < 0) {
                $baseEmbargoSac = 0;
            }

            $embargoSac = 0;
            $porcentajeEmbargoSac = 0;
            $conceptoEmbargoEmpleado = buscarConceptoEmpleadoPorCodigo($conceptosFijos, $conceptos, '310');

            if ($conceptoEmbargoEmpleado !== null) {
                if ((float)$conceptoEmbargoEmpleado['porcentaje_manual'] > 0) {
                    $porcentajeEmbargoSac = (float)$conceptoEmbargoEmpleado['porcentaje_manual'];
                    $embargoSac = round($baseEmbargoSac * ($porcentajeEmbargoSac / 100), 2);
                } elseif ((float)$conceptoEmbargoEmpleado['monto_manual'] > 0) {
                    $embargoSac = (float)$conceptoEmbargoEmpleado['monto_manual'];
                    $porcentajeEmbargoSac = 0;
                }
            }

            agregarConceptoDetalle($detalleItems, '301', $cajaSac, 1, 11, 0, '11% Caja de Previsión Social sobre aguinaldo');
            agregarConceptoDetalle($detalleItems, '306', $ips1Sac, 1, 1, 0, '1% IPS sobre aguinaldo');
            agregarConceptoDetalle($detalleItems, '307', $ips2Sac, 1, 2, 0, '2% IPS sobre aguinaldo');
            agregarConceptoDetalle($detalleItems, '308', $gremioSac, 1, $porcentajeGremioSac, 0, 'Descuento gremial sobre aguinaldo');
            agregarConceptoDetalle($detalleItems, '310', $embargoSac, 1, $porcentajeEmbargoSac, 0, 'Embargo judicial sobre aguinaldo menos aportes');

            $totalRemunerativo = $sacBruto;
            $totalNoRemunerativo = 0;
            $totalAsignaciones = 0;
            $totalDescuentos = $cajaSac + $ips1Sac + $ips2Sac + $gremioSac + $embargoSac;
            $neto = $totalRemunerativo - $totalDescuentos;
        } else {
            foreach ($detalleRemunerativosBase as $itemBase) {
                $detalleItems[] = $itemBase;
            }

            agregarConceptoDetalle($detalleItems, '112', $noRemunerativo, 1, 0, 0, 'No remunerativo');

            $totalRemunerativo = 0;
            $codigosRemunerativos = ['101','102','103','104','105','106','107','108','109','110','111'];

            foreach ($detalleItems as $item) {
                if (in_array($item['codigo'], $codigosRemunerativos, true)) {
                    $totalRemunerativo += (float)$item['monto'];
                }
            }

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

            $basesCalculo = [
                'TOTAL_REMUNERATIVO' => $totalRemunerativo,
                'BASICO' => $sueldoBasico,
                'BASICO_MAS_DEDICACION' => $sueldoBasico + $dedicacion,
                'BASICO_MAS_DEDICACION_MAS_SUPLEMENTO' => $sueldoBasico + $dedicacion + $suplemento
            ];

            $caja = $totalRemunerativo * 0.11;
            $obraSocial = $totalRemunerativo * 0.05;
            $sepelio = $totalRemunerativo * 0.01;
            $voluntario = $totalRemunerativo * 0.08;
            $ips1 = ($categoriaNumero <= 21) ? ($totalRemunerativo * 0.01) : 0;
            $ips2 = ($categoriaNumero <= 22) ? ($totalRemunerativo * 0.02) : 0;

            $gremio = 0;
            $porcentajeGremio = 0;
            $conceptoGremioEmpleado = buscarConceptoEmpleadoPorCodigo($conceptosFijos, $conceptos, '308');

            if ($conceptoGremioEmpleado !== null && isset($conceptos['308'])) {
                if ((float)$conceptoGremioEmpleado['porcentaje_manual'] > 0) {
                    $porcentajeGremio = (float)$conceptoGremioEmpleado['porcentaje_manual'];
                    $gremio = round($totalRemunerativo * ($porcentajeGremio / 100), 2);
                } elseif ((float)$conceptoGremioEmpleado['monto_manual'] > 0) {
                    $gremio = (float)$conceptoGremioEmpleado['monto_manual'];
                    $porcentajeGremio = 0;
                } else {
                    $gremio = calcularConceptoAutomatico($conceptos['308'], $basesCalculo);
                    $porcentajeGremio = (float)$conceptos['308']['porcentaje'];
                }
            }

            $totalAportesParaEmbargo = $caja + $obraSocial + $sepelio + $voluntario + $ips1 + $ips2;
            $baseEmbargo = $totalRemunerativo - $totalAportesParaEmbargo;
            if ($baseEmbargo < 0) {
                $baseEmbargo = 0;
            }

            $embargo = 0;
            $porcentajeEmbargo = 0;
            $conceptoEmbargoEmpleado = buscarConceptoEmpleadoPorCodigo($conceptosFijos, $conceptos, '310');

            if ($conceptoEmbargoEmpleado !== null) {
                if ((float)$conceptoEmbargoEmpleado['porcentaje_manual'] > 0) {
                    $porcentajeEmbargo = (float)$conceptoEmbargoEmpleado['porcentaje_manual'];
                    $embargo = round($baseEmbargo * ($porcentajeEmbargo / 100), 2);
                } elseif ((float)$conceptoEmbargoEmpleado['monto_manual'] > 0) {
                    $embargo = (float)$conceptoEmbargoEmpleado['monto_manual'];
                    $porcentajeEmbargo = 0;
                }
            }

            agregarConceptoDetalle($detalleItems, '301', $caja, 1, 11, 0, '11% Caja de Previsión Social');
            agregarConceptoDetalle($detalleItems, '302', $obraSocial, 1, 5, 0, '5% IASEP Obra Social');
            agregarConceptoDetalle($detalleItems, '303', $sepelio, 1, 1, 0, '1% IASEP Sepelio');
            agregarConceptoDetalle($detalleItems, '304', $voluntario, 1, 8, 0, '8% IASEP Voluntario');
            agregarConceptoDetalle($detalleItems, '306', $ips1, 1, 1, 0, '1% IPS para categoría 21 o inferior');
            agregarConceptoDetalle($detalleItems, '307', $ips2, 1, 2, 0, '2% IPS para categoría 22 o inferior');
            agregarConceptoDetalle($detalleItems, '308', $gremio, 1, $porcentajeGremio, 0, 'Descuento gremial sobre total remunerativo');
            agregarConceptoDetalle($detalleItems, '310', $embargo, 1, $porcentajeEmbargo, 0, 'Embargo judicial sobre remunerativo menos aportes 301 al 307');

            $patronalCaja = $totalRemunerativo * 0.16;
            $patronalObra = $totalRemunerativo * 0.04;
            $patronalIps  = $totalRemunerativo * 0.02;

            agregarConceptoDetalle($detalleItems, '401', $patronalCaja, 1, 16, 0, '16% Caja patronal');
            agregarConceptoDetalle($detalleItems, '402', $patronalObra, 1, 4, 0, '4% Obra social patronal');
            agregarConceptoDetalle($detalleItems, '403', $patronalIps, 1, 2, 0, '2% IPS patronal');

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
        }

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