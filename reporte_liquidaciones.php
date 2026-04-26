<?php
session_start();
require_once("conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$periodo = trim($_GET['periodo'] ?? "");
$tipo    = trim($_GET['tipo'] ?? "");
$estado  = trim($_GET['estado'] ?? "");

$sql = "
    SELECT 
        l.id,
        l.tipo_liquidacion,
        l.periodo,
        l.fecha_liquidacion,
        l.descripcion,
        l.estado,
        l.created_at,
        COUNT(le.id) AS cantidad_empleados,
        COALESCE(SUM(le.total_remunerativo), 0) AS total_remunerativo,
        COALESCE(SUM(le.total_descuentos), 0) AS total_descuentos,
        COALESCE(SUM(le.total_no_remunerativo), 0) AS total_no_remunerativo,
        COALESCE(SUM(le.total_asignaciones), 0) AS total_asignaciones,
        COALESCE(SUM(le.neto), 0) AS total_neto
    FROM liquidacion l
    LEFT JOIN liquidacion_empleado le ON le.liquidacion_id = l.id
    WHERE 1=1
";

$params = [];
$types = "";

if ($periodo !== "") {
    $sql .= " AND l.periodo LIKE ?";
    $params[] = "%" . $periodo . "%";
    $types .= "s";
}

if ($tipo !== "") {
    $sql .= " AND l.tipo_liquidacion = ?";
    $params[] = $tipo;
    $types .= "s";
}

if ($estado !== "") {
    $sql .= " AND l.estado = ?";
    $params[] = $estado;
    $types .= "s";
}

$sql .= "
    GROUP BY 
        l.id,
        l.tipo_liquidacion,
        l.periodo,
        l.fecha_liquidacion,
        l.descripcion,
        l.estado,
        l.created_at
    ORDER BY l.fecha_liquidacion DESC, l.id DESC
";

$stmt = $conexion->prepare($sql);

if (!$stmt) {
    die("Error al preparar la consulta: " . $conexion->error);
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$resultado = $stmt->get_result();

$totalLiquidaciones = $resultado->num_rows;

$tiposLiquidacion = [];
$resTipos = $conexion->query("SELECT DISTINCT tipo_liquidacion FROM liquidacion ORDER BY tipo_liquidacion ASC");
if ($resTipos) {
    while ($filaTipo = $resTipos->fetch_assoc()) {
        if (!empty($filaTipo['tipo_liquidacion'])) {
            $tiposLiquidacion[] = $filaTipo['tipo_liquidacion'];
        }
    }
}

$estados = ['BORRADOR', 'CERRADA', 'ANULADA'];

$queryString = http_build_query([
    'periodo' => $periodo,
    'tipo'    => $tipo,
    'estado'  => $estado
]);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Liquidaciones</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #f4f7fb;
            color: #1f2937;
        }

        .contenedor {
            width: 95%;
            max-width: 1450px;
            margin: 30px auto;
        }

        .cabecera {
            background: linear-gradient(135deg, #16a34a, #22c55e);
            color: white;
            border-radius: 18px;
            padding: 24px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.10);
            margin-bottom: 22px;
        }

        .cabecera-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .cabecera h1 {
            margin: 0 0 6px 0;
            font-size: 30px;
        }

        .cabecera p {
            margin: 0;
            opacity: 0.95;
        }

        .acciones-superiores {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-block;
            text-decoration: none;
            border: none;
            border-radius: 10px;
            padding: 11px 16px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            color: white;
            transition: 0.2s ease;
        }

        .btn:hover {
            opacity: 0.93;
            transform: translateY(-1px);
        }

        .btn-volver {
            background: #374151;
        }

        .btn-reportes {
            background: #ea580c;
        }

        .btn-buscar {
            background: #16a34a;
        }

        .btn-limpiar {
            background: #6b7280;
        }

        .btn-pdf {
            background: #dc2626;
        }

        .btn-excel {
            background: #15803d;
        }

        .panel {
            background: white;
            border-radius: 18px;
            padding: 20px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
            border: 1px solid #e5e7eb;
            margin-bottom: 20px;
        }

        .panel h2 {
            margin-top: 0;
            margin-bottom: 16px;
            font-size: 20px;
            color: #16a34a;
        }

        .filtros {
            display: grid;
            grid-template-columns: 1.5fr 1.2fr 1fr auto auto;
            gap: 12px;
            align-items: end;
        }

        .campo label {
            display: block;
            margin-bottom: 6px;
            font-size: 14px;
            font-weight: bold;
            color: #374151;
        }

        .campo input,
        .campo select {
            width: 100%;
            padding: 11px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            font-size: 14px;
            outline: none;
        }

        .campo input:focus,
        .campo select:focus {
            border-color: #22c55e;
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.15);
        }

        .resumen {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 15px;
        }

        .resumen-box {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
            padding: 12px 15px;
            border-radius: 12px;
            font-weight: bold;
        }

        .acciones-exportar {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .tabla-contenedor {
            overflow-x: auto;
        }

        table {
            width: 100%;
            min-width: 1600px;
            border-collapse: collapse;
            background: white;
        }

        th, td {
            padding: 12px 10px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
            font-size: 14px;
            vertical-align: middle;
        }

        th {
            background: #16a34a;
            color: white;
            position: sticky;
            top: 0;
        }

        tr:hover {
            background: #f8fafc;
        }

        .estado-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 999px;
            font-size: 12px;
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

        .acciones-tabla {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }

        .btn-mini {
            display: inline-block;
            text-decoration: none;
            font-size: 12px;
            font-weight: bold;
            padding: 7px 9px;
            border-radius: 8px;
            color: white;
        }

        .btn-ver {
            background: #2563eb;
        }

        .btn-modulo {
            background: #16a34a;
        }

        .btn-recibos {
            background: #0f766e;
        }

        .sin-registros {
            text-align: center;
            padding: 25px;
            color: #6b7280;
            background: #fff;
            border-radius: 12px;
        }

        @media (max-width: 1200px) {
            .filtros {
                grid-template-columns: 1fr;
            }

            .acciones-superiores,
            .acciones-exportar {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="contenedor">

    <div class="cabecera">
        <div class="cabecera-top">
            <div>
                <h1>Reporte de Liquidaciones</h1>
                <p>Consulta histórica de liquidaciones con totales generales y acceso al detalle.</p>
            </div>

            <div class="acciones-superiores">
                <a href="reportes.php" class="btn btn-reportes">Volver a Reportes</a>
                <a href="index.php" class="btn btn-volver">Menú Principal</a>
            </div>
        </div>
    </div>

    <div class="panel">
        <h2>Filtros de búsqueda</h2>

        <form method="GET" action="reporte_liquidaciones.php" class="filtros">
            <div class="campo">
                <label for="periodo">Período</label>
                <input
                    type="text"
                    id="periodo"
                    name="periodo"
                    placeholder="Ejemplo: 2026-03 o marzo 2026"
                    value="<?php echo htmlspecialchars($periodo); ?>"
                >
            </div>

            <div class="campo">
                <label for="tipo">Tipo de liquidación</label>
                <select name="tipo" id="tipo">
                    <option value="">Todos</option>
                    <?php foreach ($tiposLiquidacion as $t): ?>
                        <option value="<?php echo htmlspecialchars($t); ?>" <?php echo ($tipo === $t) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($t); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="campo">
                <label for="estado">Estado</label>
                <select name="estado" id="estado">
                    <option value="">Todos</option>
                    <?php foreach ($estados as $e): ?>
                        <option value="<?php echo $e; ?>" <?php echo ($estado === $e) ? 'selected' : ''; ?>>
                            <?php echo $e; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-buscar">Buscar</button>
            <a href="reporte_liquidaciones.php" class="btn btn-limpiar">Limpiar</a>
        </form>
    </div>

    <div class="panel">
        <div class="resumen">
            <div class="resumen-box">
                Total de liquidaciones encontradas: <?php echo (int)$totalLiquidaciones; ?>
            </div>

            <div class="acciones-exportar">
                <a href="reporte_liquidaciones_pdf.php?<?php echo htmlspecialchars($queryString); ?>" class="btn btn-pdf">Exportar PDF</a>
                <a href="reporte_liquidaciones_excel.php?<?php echo htmlspecialchars($queryString); ?>" class="btn btn-excel">Exportar Excel</a>
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
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th>Empleados</th>
                            <th>Total Remunerativo</th>
                            <th>Total Descuentos</th>
                            <th>Total No Remunerativo</th>
                            <th>Total Asignaciones</th>
                            <th>Total Neto</th>
                            <th>Creada</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($fila = $resultado->fetch_assoc()): ?>
                            <?php
                            $claseEstado = 'estado-borrador';
                            if ($fila['estado'] === 'CERRADA') {
                                $claseEstado = 'estado-cerrada';
                            } elseif ($fila['estado'] === 'ANULADA') {
                                $claseEstado = 'estado-anulada';
                            }
                            ?>
                            <tr>
                                <td><?php echo (int)$fila['id']; ?></td>
                                <td><?php echo htmlspecialchars($fila['tipo_liquidacion']); ?></td>
                                <td><?php echo htmlspecialchars($fila['periodo']); ?></td>
                                <td>
                                    <?php echo !empty($fila['fecha_liquidacion']) ? date("d/m/Y", strtotime($fila['fecha_liquidacion'])) : ''; ?>
                                </td>
                                <td><?php echo htmlspecialchars($fila['descripcion'] ?? ''); ?></td>
                                <td>
                                    <span class="estado-badge <?php echo $claseEstado; ?>">
                                        <?php echo htmlspecialchars($fila['estado']); ?>
                                    </span>
                                </td>
                                <td><?php echo (int)$fila['cantidad_empleados']; ?></td>
                                <td>$<?php echo number_format((float)$fila['total_remunerativo'], 2, ',', '.'); ?></td>
                                <td>$<?php echo number_format((float)$fila['total_descuentos'], 2, ',', '.'); ?></td>
                                <td>$<?php echo number_format((float)$fila['total_no_remunerativo'], 2, ',', '.'); ?></td>
                                <td>$<?php echo number_format((float)$fila['total_asignaciones'], 2, ',', '.'); ?></td>
                                <td><strong>$<?php echo number_format((float)$fila['total_neto'], 2, ',', '.'); ?></strong></td>
                                <td>
                                    <?php echo !empty($fila['created_at']) ? date("d/m/Y H:i", strtotime($fila['created_at'])) : ''; ?>
                                </td>
                                <td>
                                    <div class="acciones-tabla">
                                        <a href="liquidacion_ver.php?id=<?php echo $fila['id']; ?>" class="btn-mini btn-ver">Ver Detalle</a>
                                        <a href="recibos_liquidacion_pdf.php?liquidacion_id=<?php echo $fila['id']; ?>" class="btn-mini btn-recibos">Todos los Recibos</a>
                                        <a href="liquidacion.php" class="btn-mini btn-modulo">Ir a Liquidación</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="sin-registros">
                No se encontraron liquidaciones con los filtros seleccionados.
            </div>
        <?php endif; ?>
    </div>

</div>

</body>
</html>