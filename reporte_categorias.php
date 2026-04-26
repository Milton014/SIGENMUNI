<?php
session_start();
require_once("conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$buscar = trim($_GET['buscar'] ?? "");
$activo = trim($_GET['activo'] ?? "");

$sql = "SELECT * FROM categoria WHERE 1=1";
$params = [];
$types = "";

/*
|--------------------------------------------------------------------------
| FILTROS
|--------------------------------------------------------------------------
*/
if ($buscar !== "") {
    $sql .= " AND (
        CAST(codigo AS CHAR) LIKE ?
        OR nombre LIKE ?
    )";
    $like = "%" . $buscar . "%";
    $params[] = $like;
    $params[] = $like;
    $types .= "ss";
}

if ($activo !== "") {
    $sql .= " AND activo = ?";
    $params[] = (int)$activo;
    $types .= "i";
}

$sql .= " ORDER BY codigo ASC, nombre ASC";

$stmt = $conexion->prepare($sql);

if (!$stmt) {
    die("Error al preparar la consulta: " . $conexion->error);
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$resultado = $stmt->get_result();
$totalCategorias = $resultado->num_rows;

$queryString = http_build_query([
    'buscar' => $buscar,
    'activo' => $activo
]);

/*
|--------------------------------------------------------------------------
| DETECTAR COLUMNAS OPCIONALES
|--------------------------------------------------------------------------
*/
$columnasCategoria = [];
$checkCols = $conexion->query("SHOW COLUMNS FROM categoria");
if ($checkCols) {
    while ($col = $checkCols->fetch_assoc()) {
        $columnasCategoria[] = $col['Field'];
    }
}

$tieneBasico = in_array('sueldo_basico', $columnasCategoria);
$tieneDedicacion = in_array('dedicacion_funcional', $columnasCategoria);
$tieneSuplemento = in_array('suplemento_especial', $columnasCategoria);
$tieneDescripcion = in_array('descripcion', $columnasCategoria);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Categorías</title>
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
            background: linear-gradient(135deg, #7c3aed, #a855f7);
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
            background: #7c3aed;
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
            color: #7c3aed;
        }

        .filtros {
            display: grid;
            grid-template-columns: 2fr 1fr auto auto;
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
            border-color: #a855f7;
            box-shadow: 0 0 0 3px rgba(168, 85, 247, 0.15);
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
            background: #f5f3ff;
            border: 1px solid #ddd6fe;
            color: #5b21b6;
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
            min-width: 1100px;
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
            background: #7c3aed;
            color: white;
            position: sticky;
            top: 0;
        }

        tr:hover {
            background: #faf5ff;
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

        .sin-registros {
            text-align: center;
            padding: 25px;
            color: #6b7280;
            background: #fff;
            border-radius: 12px;
        }

        @media (max-width: 1000px) {
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
                <h1>Reporte de Categorías</h1>
                <p>Consulta de categorías municipales con filtros y exportación.</p>
            </div>

            <div class="acciones-superiores">
                <a href="reportes.php" class="btn btn-reportes">Volver a Reportes</a>
                <a href="index.php" class="btn btn-volver">Menú Principal</a>
            </div>
        </div>
    </div>

    <div class="panel">
        <h2>Filtros de búsqueda</h2>

        <form method="GET" action="reporte_categorias.php" class="filtros">
            <div class="campo">
                <label for="buscar">Búsqueda</label>
                <input
                    type="text"
                    id="buscar"
                    name="buscar"
                    placeholder="Buscar por código o nombre"
                    value="<?php echo htmlspecialchars($buscar); ?>"
                >
            </div>

            <div class="campo">
                <label for="activo">Estado</label>
                <select name="activo" id="activo">
                    <option value="">Todos</option>
                    <option value="1" <?php echo ($activo === "1") ? "selected" : ""; ?>>Activas</option>
                    <option value="0" <?php echo ($activo === "0") ? "selected" : ""; ?>>Inactivas</option>
                </select>
            </div>

            <button type="submit" class="btn btn-buscar">Buscar</button>
            <a href="reporte_categorias.php" class="btn btn-limpiar">Limpiar</a>
        </form>
    </div>

    <div class="panel">
        <div class="resumen">
            <div class="resumen-box">
                Total de categorías encontradas: <?php echo (int)$totalCategorias; ?>
            </div>

            <div class="acciones-exportar">
                <a href="reporte_categorias_pdf.php?<?php echo htmlspecialchars($queryString); ?>" class="btn btn-pdf">Exportar PDF</a>
                <a href="reporte_categorias_excel.php?<?php echo htmlspecialchars($queryString); ?>" class="btn btn-excel">Exportar Excel</a>
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

                            <?php if ($tieneDescripcion): ?>
                                <th>Descripción</th>
                            <?php endif; ?>

                            <?php if ($tieneBasico): ?>
                                <th>Sueldo Básico</th>
                            <?php endif; ?>

                            <?php if ($tieneDedicacion): ?>
                                <th>Dedicación Funcional</th>
                            <?php endif; ?>

                            <?php if ($tieneSuplemento): ?>
                                <th>Suplemento Especial</th>
                            <?php endif; ?>

                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($fila = $resultado->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo (int)$fila['id']; ?></td>
                                <td><?php echo htmlspecialchars($fila['codigo'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($fila['nombre'] ?? ''); ?></td>

                                <?php if ($tieneDescripcion): ?>
                                    <td><?php echo htmlspecialchars($fila['descripcion'] ?? ''); ?></td>
                                <?php endif; ?>

                                <?php if ($tieneBasico): ?>
                                    <td>$ <?php echo number_format((float)($fila['sueldo_basico'] ?? 0), 2, ',', '.'); ?></td>
                                <?php endif; ?>

                                <?php if ($tieneDedicacion): ?>
                                    <td>$ <?php echo number_format((float)($fila['dedicacion_funcional'] ?? 0), 2, ',', '.'); ?></td>
                                <?php endif; ?>

                                <?php if ($tieneSuplemento): ?>
                                    <td>$ <?php echo number_format((float)($fila['suplemento_especial'] ?? 0), 2, ',', '.'); ?></td>
                                <?php endif; ?>

                                <td>
                                    <?php if (isset($fila['activo']) && (int)$fila['activo'] === 1): ?>
                                        <span class="estado-activo">Activa</span>
                                    <?php elseif (isset($fila['activo'])): ?>
                                        <span class="estado-inactivo">Inactiva</span>
                                    <?php else: ?>
                                        <span>-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="sin-registros">
                No se encontraron categorías con los filtros seleccionados.
            </div>
        <?php endif; ?>
    </div>

</div>

</body>
</html>