<?php
session_start();
require_once("conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$busqueda = trim($_GET['busqueda'] ?? "");
$estado   = trim($_GET['estado'] ?? "");

$sql = "
    SELECT 
        e.id,
        e.nro_legajo,
        e.apellido,
        e.nombre,
        e.dni,
        e.cuil,
        e.telefono,
        e.email,
        e.fecha_alta,
        e.fecha_baja,
        e.activo,
        c.nombre AS categoria,
        o.nombre AS oficina,
        s.nombre AS situacion
    FROM empleado e
    INNER JOIN categoria c ON e.categoria_id = c.id
    INNER JOIN oficina o ON e.oficina_id = o.id
    INNER JOIN situacion s ON e.situacion_id = s.id
    WHERE 1=1
";

$params = [];
$types = "";

if ($busqueda !== "") {
    $sql .= " 
        AND (
            e.nro_legajo LIKE ?
            OR e.apellido LIKE ?
            OR e.nombre LIKE ?
            OR e.dni LIKE ?
            OR e.cuil LIKE ?
            OR e.email LIKE ?
        )
    ";
    $like = "%{$busqueda}%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $types .= "ssssss";
}

if ($estado !== "") {
    $sql .= " AND e.activo = ?";
    $params[] = (int)$estado;
    $types .= "i";
}

$sql .= " ORDER BY e.apellido, e.nombre";

$stmt = $conexion->prepare($sql);

if (!$stmt) {
    die("Error al preparar la consulta: " . $conexion->error);
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$resultado = $stmt->get_result();

$totalEmpleados = $resultado->num_rows;

$queryString = http_build_query([
    'busqueda' => $busqueda,
    'estado'   => $estado
]);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Empleados</title>
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
            max-width: 1400px;
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

        .btn-pdf {
            background: #dc2626;
        }

        .btn-excel {
            background: #16a34a;
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

        .filtros {
            display: grid;
            grid-template-columns: 2fr 1fr auto auto auto;
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
            border-color: #14b8a6;
            box-shadow: 0 0 0 3px rgba(20, 184, 166, 0.15);
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
            background: #ecfeff;
            border: 1px solid #a5f3fc;
            color: #155e75;
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
            min-width: 1250px;
            border-collapse: collapse;
            background: white;
        }

        th, td {
            padding: 12px 10px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
            font-size: 14px;
            vertical-align: top;
        }

        th {
            background: #0f766e;
            color: white;
            position: sticky;
            top: 0;
        }

        tr:hover {
            background: #f8fafc;
        }

        .estado-activo {
            display: inline-block;
            background: #dcfce7;
            color: #166534;
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 999px;
            font-size: 12px;
        }

        .estado-inactivo {
            display: inline-block;
            background: #fee2e2;
            color: #991b1b;
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 999px;
            font-size: 12px;
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

        .btn-editar {
            background: #f59e0b;
        }

        .btn-liquidar {
            background: #16a34a;
        }

        .sin-registros {
            text-align: center;
            padding: 25px;
            color: #6b7280;
            background: #fff;
            border-radius: 12px;
        }

        @media (max-width: 1100px) {
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
                <h1>Reporte de Empleados</h1>
                <p>Consulta general del personal municipal con filtros y exportación.</p>
            </div>

            <div class="acciones-superiores">
                <a href="reportes.php" class="btn btn-reportes">Volver a Reportes</a>
                <a href="index.php" class="btn btn-volver">Menú Principal</a>
            </div>
        </div>
    </div>

    <div class="panel">
        <h2>Filtros de búsqueda</h2>

        <form method="GET" action="reporte_empleados.php" class="filtros">
            <div class="campo">
                <label for="busqueda">Búsqueda general</label>
                <input
                    type="text"
                    id="busqueda"
                    name="busqueda"
                    placeholder="Legajo, apellido, nombre, DNI, CUIL o email"
                    value="<?php echo htmlspecialchars($busqueda); ?>"
                >
            </div>

            <div class="campo">
                <label for="estado">Estado</label>
                <select name="estado" id="estado">
                    <option value="">Todos</option>
                    <option value="1" <?php echo ($estado === "1") ? "selected" : ""; ?>>Activos</option>
                    <option value="0" <?php echo ($estado === "0") ? "selected" : ""; ?>>Inactivos</option>
                </select>
            </div>

            <button type="submit" class="btn btn-buscar">Buscar</button>
            <a href="reporte_empleados.php" class="btn btn-limpiar">Limpiar</a>
            <a href="empleados.php" class="btn btn-volver">Ir a Gestión</a>
        </form>
    </div>

    <div class="panel">
        <div class="resumen">
            <div class="resumen-box">
                Total de empleados encontrados: <?php echo (int)$totalEmpleados; ?>
            </div>

            <div class="acciones-exportar">
                <a href="reporte_empleados_pdf.php?<?php echo htmlspecialchars($queryString); ?>" class="btn btn-pdf">Exportar PDF</a>
                <a href="reporte_empleados_excel.php?<?php echo htmlspecialchars($queryString); ?>" class="btn btn-excel">Exportar Excel</a>
            </div>
        </div>

        <?php if ($resultado && $resultado->num_rows > 0): ?>
            <div class="tabla-contenedor">
                <table>
                    <thead>
                        <tr>
                            <th>Legajo</th>
                            <th>Apellido y Nombre</th>
                            <th>DNI</th>
                            <th>CUIL</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>Fecha Alta</th>
                            <th>Fecha Baja</th>
                            <th>Categoría</th>
                            <th>Oficina</th>
                            <th>Situación</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while ($fila = $resultado->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($fila['nro_legajo']); ?></td>
                            <td><?php echo htmlspecialchars($fila['apellido'] . ', ' . $fila['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($fila['dni']); ?></td>
                            <td><?php echo htmlspecialchars($fila['cuil']); ?></td>
                            <td><?php echo htmlspecialchars($fila['telefono'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($fila['email'] ?? ''); ?></td>
                            <td>
                                <?php echo !empty($fila['fecha_alta']) ? date("d/m/Y", strtotime($fila['fecha_alta'])) : ''; ?>
                            </td>
                            <td>
                                <?php echo !empty($fila['fecha_baja']) ? date("d/m/Y", strtotime($fila['fecha_baja'])) : ''; ?>
                            </td>
                            <td><?php echo htmlspecialchars($fila['categoria']); ?></td>
                            <td><?php echo htmlspecialchars($fila['oficina']); ?></td>
                            <td><?php echo htmlspecialchars($fila['situacion']); ?></td>
                            <td>
                                <?php if ((int)$fila['activo'] === 1): ?>
                                    <span class="estado-activo">Activo</span>
                                <?php else: ?>
                                    <span class="estado-inactivo">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="acciones-tabla">
                                    <a href="empleado_ver.php?id=<?php echo $fila['id']; ?>" class="btn-mini btn-ver">Ver</a>
                                    <a href="empleado_editar.php?id=<?php echo $fila['id']; ?>" class="btn-mini btn-editar">Editar</a>
                                    <a href="liquidacion.php?empleado_id=<?php echo $fila['id']; ?>" class="btn-mini btn-liquidar">Liquidar</a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="sin-registros">
                No se encontraron empleados con los filtros seleccionados.
            </div>
        <?php endif; ?>
    </div>

</div>

</body>
</html>