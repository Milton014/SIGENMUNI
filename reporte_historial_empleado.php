<?php
session_start();
require_once("conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$empleadoId = isset($_GET['empleado_id']) ? (int)$_GET['empleado_id'] : 0;
$busqueda   = trim($_GET['busqueda'] ?? "");

$empleado = null;
$resultado = null;

if ($empleadoId > 0) {
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

    if ($empleado) {
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
    }
} else {
    $sqlBusqueda = "
        SELECT 
            e.id,
            e.nro_legajo,
            e.apellido,
            e.nombre,
            e.dni,
            c.nombre AS categoria
        FROM empleado e
        LEFT JOIN categoria c ON e.categoria_id = c.id
        WHERE 1=1
    ";

    $params = [];
    $types = "";

    if ($busqueda !== "") {
        $sqlBusqueda .= " AND (
            e.nro_legajo LIKE ?
            OR e.apellido LIKE ?
            OR e.nombre LIKE ?
            OR e.dni LIKE ?
            OR e.cuil LIKE ?
        )";
        $like = "%" . $busqueda . "%";
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
        $types .= "sssss";
    }

    $sqlBusqueda .= " ORDER BY e.apellido, e.nombre LIMIT 50";

    $stmtBus = $conexion->prepare($sqlBusqueda);
    if (!$stmtBus) {
        die("Error al preparar búsqueda de empleados: " . $conexion->error);
    }

    if (!empty($params)) {
        $stmtBus->bind_param($types, ...$params);
    }

    $stmtBus->execute();
    $resultadoBusqueda = $stmtBus->get_result();
}

function estadoClase($estado) {
    if ($estado === 'CERRADA') return 'estado-cerrada';
    if ($estado === 'ANULADA') return 'estado-anulada';
    return 'estado-borrador';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial por Empleado</title>
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
            background: linear-gradient(135deg, #0f766e, #14b8a6);
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
            background: #0f766e;
        }

        .btn-limpiar {
            background: #6b7280;
        }

        .btn-ver {
            background: #2563eb;
        }

        .btn-recibo {
            background: #0f766e;
        }

        .btn-detalle {
            background: #16a34a;
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
            color: #0f766e;
        }

        .busqueda-form {
            display: grid;
            grid-template-columns: 2fr auto auto;
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

        .campo input {
            width: 100%;
            padding: 11px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            font-size: 14px;
            outline: none;
        }

        .campo input:focus {
            border-color: #14b8a6;
            box-shadow: 0 0 0 3px rgba(20, 184, 166, 0.15);
        }

        .datos-empleado {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 12px;
        }

        .info-box {
            border: 1px solid #e5e7eb;
            background: #f9fafb;
            border-radius: 10px;
            padding: 12px;
        }

        .info-box strong {
            display: block;
            margin-bottom: 5px;
            color: #374151;
            font-size: 13px;
        }

        .acciones-exportar {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }

        .tabla-contenedor {
            overflow-x: auto;
        }

        table {
            width: 100%;
            min-width: 1200px;
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
            background: #0f766e;
            color: white;
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

        .lista-empleados table {
            min-width: 700px;
        }

        .sin-registros {
            text-align: center;
            padding: 25px;
            color: #6b7280;
            background: #fff;
            border-radius: 12px;
        }

        @media (max-width: 1000px) {
            .busqueda-form {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<div class="contenedor">

    <div class="cabecera">
        <div class="cabecera-top">
            <div>
                <h1>Historial por Empleado</h1>
                <p>Consulta de liquidaciones históricas y recibos individuales por agente.</p>
            </div>

            <div class="acciones-superiores">
                <a href="reportes.php" class="btn btn-reportes">Volver a Reportes</a>
                <a href="index.php" class="btn btn-volver">Menú Principal</a>
            </div>
        </div>
    </div>

    <?php if ($empleadoId <= 0): ?>
        <div class="panel">
            <h2>Buscar empleado</h2>

            <form method="GET" action="reporte_historial_empleado.php" class="busqueda-form">
                <div class="campo">
                    <label for="busqueda">Búsqueda</label>
                    <input
                        type="text"
                        id="busqueda"
                        name="busqueda"
                        placeholder="Buscar por legajo, apellido, nombre, DNI o CUIL"
                        value="<?php echo htmlspecialchars($busqueda); ?>"
                    >
                </div>

                <button type="submit" class="btn btn-buscar">Buscar</button>
                <a href="reporte_historial_empleado.php" class="btn btn-limpiar">Limpiar</a>
            </form>
        </div>

        <div class="panel lista-empleados">
            <h2>Resultados</h2>

            <?php if (isset($resultadoBusqueda) && $resultadoBusqueda->num_rows > 0): ?>
                <div class="tabla-contenedor">
                    <table>
                        <thead>
                            <tr>
                                <th>Legajo</th>
                                <th>Apellido y Nombre</th>
                                <th>DNI</th>
                                <th>Categoría</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($fila = $resultadoBusqueda->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($fila['nro_legajo']); ?></td>
                                    <td><?php echo htmlspecialchars($fila['apellido'] . ', ' . $fila['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($fila['dni']); ?></td>
                                    <td><?php echo htmlspecialchars($fila['categoria'] ?? ''); ?></td>
                                    <td>
                                        <a href="reporte_historial_empleado.php?empleado_id=<?php echo $fila['id']; ?>" class="btn-mini btn-ver">
                                            Ver Historial
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="sin-registros">
                    Ingresá una búsqueda para localizar un empleado.
                </div>
            <?php endif; ?>
        </div>

    <?php else: ?>

        <?php if ($empleado): ?>
            <div class="panel">
                <h2>Datos del Empleado</h2>

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
                </div>
            </div>

            <div class="panel">
                <h2>Historial de Liquidaciones</h2>

                <div class="acciones-exportar">
                    <a href="reporte_historial_empleado_pdf.php?empleado_id=<?php echo $empleado['id']; ?>" class="btn btn-pdf">Exportar PDF</a>
                    <a href="reporte_historial_empleado_excel.php?empleado_id=<?php echo $empleado['id']; ?>" class="btn btn-excel">Exportar Excel</a>
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
                                    <th>Total Remunerativo</th>
                                    <th>Total Descuentos</th>
                                    <th>No Remunerativo</th>
                                    <th>Asignaciones</th>
                                    <th>Neto</th>
                                    <th>Acciones</th>
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
                                            <span class="estado-badge <?php echo estadoClase($fila['estado']); ?>">
                                                <?php echo htmlspecialchars($fila['estado']); ?>
                                            </span>
                                        </td>
                                        <td>$<?php echo number_format((float)$fila['total_remunerativo'], 2, ',', '.'); ?></td>
                                        <td>$<?php echo number_format((float)$fila['total_descuentos'], 2, ',', '.'); ?></td>
                                        <td>$<?php echo number_format((float)$fila['total_no_remunerativo'], 2, ',', '.'); ?></td>
                                        <td>$<?php echo number_format((float)$fila['total_asignaciones'], 2, ',', '.'); ?></td>
                                        <td><strong>$<?php echo number_format((float)$fila['neto'], 2, ',', '.'); ?></strong></td>
                                        <td>
                                            <div class="acciones-tabla">
                                                <a href="liquidacion_ver.php?id=<?php echo $fila['liquidacion_id']; ?>&empleado_id=<?php echo $empleado['id']; ?>" class="btn-mini btn-detalle">
                                                    Ver Detalle
                                                </a>
                                                <a href="recibo_sueldo.php?liquidacion_id=<?php echo $fila['liquidacion_id']; ?>&empleado_id=<?php echo $empleado['id']; ?>" class="btn-mini btn-recibo">
                                                    Ver Recibo
                                                </a>
                                            </div>
                                        </td>
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
        <?php else: ?>
            <div class="panel">
                <div class="sin-registros">
                    No se encontró el empleado solicitado.
                </div>
            </div>
        <?php endif; ?>

    <?php endif; ?>

</div>

</body>
</html>