<?php
session_start();
require_once("conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$buscar    = isset($_GET['buscar']) ? trim($_GET['buscar']) : "";
$categoria = isset($_GET['categoria']) ? trim($_GET['categoria']) : "";
$activo    = isset($_GET['activo']) ? trim($_GET['activo']) : "";

$sql = "SELECT * FROM concepto WHERE 1=1";
$params = [];
$types = "";

if ($buscar !== "") {
    $sql .= " AND (CAST(codigo AS CHAR) LIKE ? OR nombre LIKE ?)";
    $busquedaLike = "%" . $buscar . "%";
    $params[] = $busquedaLike;
    $params[] = $busquedaLike;
    $types .= "ss";
}

if ($categoria !== "") {
    $sql .= " AND categoria = ?";
    $params[] = $categoria;
    $types .= "s";
}

if ($activo !== "") {
    $sql .= " AND activo = ?";
    $params[] = (int)$activo;
    $types .= "i";
}

$sql .= " ORDER BY codigo ASC, nombre ASC";

$stmt = $conexion->prepare($sql);

if (!$stmt) {
    die("Error en la consulta: " . $conexion->error);
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$resultado = $stmt->get_result();
$totalConceptos = $resultado->num_rows;

$categorias = [
    "REMUNERATIVO",
    "NO_REMUNERATIVO",
    "ASIGNACION_FAMILIAR",
    "DESCUENTO",
    "APORTE_PATRONAL"
];

$queryString = http_build_query([
    'buscar'    => $buscar,
    'categoria' => $categoria,
    'activo'    => $activo
]);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Conceptos</title>
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
            background: linear-gradient(135deg, #0891b2, #06b6d4);
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
            background: #0891b2;
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
            color: #0891b2;
        }

        .filtros {
            display: grid;
            grid-template-columns: 2fr 1.5fr 1fr auto auto auto;
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
            border-color: #06b6d4;
            box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.15);
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
            min-width: 1500px;
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
            background: #0891b2;
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

        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: bold;
            color: white;
            white-space: nowrap;
        }

        .badge-rem { background: #2563eb; }
        .badge-no-rem { background: #7c3aed; }
        .badge-asig { background: #059669; }
        .badge-desc { background: #dc2626; }
        .badge-aporte { background: #ea580c; }

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

        .btn-editar {
            background: #f59e0b;
        }

        .btn-valores {
            background: #0891b2;
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
                <h1>Reporte de Conceptos</h1>
                <p>Consulta de conceptos del sistema con filtros y exportación.</p>
            </div>

            <div class="acciones-superiores">
                <a href="reportes.php" class="btn btn-reportes">Volver a Reportes</a>
                <a href="index.php" class="btn btn-volver">Menú Principal</a>
            </div>
        </div>
    </div>

    <div class="panel">
        <h2>Filtros de búsqueda</h2>

        <form method="GET" action="reporte_conceptos.php" class="filtros">
            <div class="campo">
                <label for="buscar">Búsqueda general</label>
                <input
                    type="text"
                    id="buscar"
                    name="buscar"
                    placeholder="Buscar por código o nombre"
                    value="<?php echo htmlspecialchars($buscar); ?>"
                >
            </div>

            <div class="campo">
                <label for="categoria">Categoría</label>
                <select name="categoria" id="categoria">
                    <option value="">Todas</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?php echo $cat; ?>" <?php echo ($categoria === $cat) ? "selected" : ""; ?>>
                            <?php echo $cat; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="campo">
                <label for="activo">Estado</label>
                <select name="activo" id="activo">
                    <option value="">Todos</option>
                    <option value="1" <?php echo ($activo === "1") ? "selected" : ""; ?>>Activos</option>
                    <option value="0" <?php echo ($activo === "0") ? "selected" : ""; ?>>Inactivos</option>
                </select>
            </div>

            <button type="submit" class="btn btn-buscar">Buscar</button>
            <a href="reporte_conceptos.php" class="btn btn-limpiar">Limpiar</a>
            <a href="conceptos.php" class="btn btn-volver">Ir a Gestión</a>
        </form>
    </div>

    <div class="panel">
        <div class="resumen">
            <div class="resumen-box">
                Total de conceptos encontrados: <?php echo (int)$totalConceptos; ?>
            </div>

            <div class="acciones-exportar">
                <a href="reporte_conceptos_pdf.php?<?php echo htmlspecialchars($queryString); ?>" class="btn btn-pdf">Exportar PDF</a>
                <a href="reporte_conceptos_excel.php?<?php echo htmlspecialchars($queryString); ?>" class="btn btn-excel">Exportar Excel</a>
            </div>
        </div>

        <?php if ($resultado && $resultado->num_rows > 0): ?>
            <div class="tabla-contenedor">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th>Forma Cálculo</th>
                            <th>%</th>
                            <th>Monto Fijo</th>
                            <th>Req. Manual</th>
                            <th>Base Cálculo</th>
                            <th>Orden</th>
                            <th>Aplica SAC</th>
                            <th>Visible Recibo</th>
                            <th>Activo</th>
                            <th>Vigencia</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($fila = $resultado->fetch_assoc()): ?>
                            <?php
                            $badgeClass = "";
                            switch ($fila['categoria']) {
                                case 'REMUNERATIVO':
                                    $badgeClass = 'badge-rem';
                                    break;
                                case 'NO_REMUNERATIVO':
                                    $badgeClass = 'badge-no-rem';
                                    break;
                                case 'ASIGNACION_FAMILIAR':
                                    $badgeClass = 'badge-asig';
                                    break;
                                case 'DESCUENTO':
                                    $badgeClass = 'badge-desc';
                                    break;
                                case 'APORTE_PATRONAL':
                                    $badgeClass = 'badge-aporte';
                                    break;
                            }

                            $desde = !empty($fila['fecha_desde']) ? date("d/m/Y", strtotime($fila['fecha_desde'])) : "-";
                            $hasta = !empty($fila['fecha_hasta']) ? date("d/m/Y", strtotime($fila['fecha_hasta'])) : "-";
                            ?>
                            <tr>
                                <td><?php echo (int)$fila['id']; ?></td>
                                <td><?php echo htmlspecialchars($fila['codigo']); ?></td>
                                <td><?php echo htmlspecialchars($fila['nombre']); ?></td>
                                <td>
                                    <span class="badge <?php echo $badgeClass; ?>">
                                        <?php echo htmlspecialchars($fila['categoria']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($fila['forma_calculo']); ?></td>
                                <td><?php echo number_format((float)$fila['porcentaje'], 2, ',', '.'); ?></td>
                                <td>$ <?php echo number_format((float)$fila['monto_fijo'], 2, ',', '.'); ?></td>
                                <td><?php echo ((int)$fila['requiere_manual'] === 1) ? 'Sí' : 'No'; ?></td>
                                <td><?php echo htmlspecialchars($fila['base_calculo'] ?? ''); ?></td>
                                <td><?php echo (int)$fila['orden_calculo']; ?></td>
                                <td><?php echo ((int)$fila['aplica_sac'] === 1) ? 'Sí' : 'No'; ?></td>
                                <td><?php echo ((int)$fila['visible_recibo'] === 1) ? 'Sí' : 'No'; ?></td>
                                <td>
                                    <?php if ((int)$fila['activo'] === 1): ?>
                                        <span class="estado-activo">Activo</span>
                                    <?php else: ?>
                                        <span class="estado-inactivo">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $desde . " / " . $hasta; ?></td>
                                <td>
                                    <div class="acciones-tabla">
                                        <a href="concepto_editar.php?id=<?php echo $fila['id']; ?>" class="btn-mini btn-editar">Editar</a>
                                        <a href="concepto_valores.php?concepto_id=<?php echo $fila['id']; ?>" class="btn-mini btn-valores">Valores</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="sin-registros">
                No se encontraron conceptos con los filtros seleccionados.
            </div>
        <?php endif; ?>
    </div>

</div>

</body>
</html>