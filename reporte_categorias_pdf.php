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
$totalCategorias = $resultado ? $resultado->num_rows : 0;

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

$tieneActivo      = in_array('activo', $columnasCategoria);
$tieneDescripcion = in_array('descripcion', $columnasCategoria);
$tieneBasico      = in_array('sueldo_basico', $columnasCategoria);
$tieneDedicacion  = in_array('dedicacion_funcional', $columnasCategoria);
$tieneSuplemento  = in_array('suplemento_especial', $columnasCategoria);

function textoEstadoCategoria($activo)
{
    if ($activo === "1") return "Activas";
    if ($activo === "0") return "Inactivas";
    return "Todas";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Categorías - PDF</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            background: #f4f7fb;
            color: #1f2937;
        }

        .contenedor {
            width: 95%;
            max-width: 1400px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 14px rgba(0,0,0,0.08);
        }

        .acciones {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }

        .btn {
            display: inline-block;
            text-decoration: none;
            border: none;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            color: white;
        }

        .btn-volver {
            background: #6b7280;
        }

        .btn-imprimir {
            background: #dc2626;
        }

        .btn-reportes {
            background: #ea580c;
        }

        .encabezado {
            border: 2px solid #d1d5db;
            border-radius: 10px;
            padding: 18px;
            margin-bottom: 18px;
        }

        .encabezado h1 {
            margin: 0 0 6px 0;
            font-size: 26px;
            color: #7c3aed;
        }

        .encabezado p {
            margin: 4px 0;
            font-size: 14px;
        }

        .resumen {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 10px;
            margin-top: 15px;
        }

        .info-box {
            border: 1px solid #e5e7eb;
            background: #f9fafb;
            border-radius: 8px;
            padding: 10px 12px;
        }

        .info-box strong {
            display: block;
            margin-bottom: 5px;
            font-size: 13px;
            color: #374151;
        }

        .tabla-contenedor {
            overflow-x: auto;
        }

        table {
            width: 100%;
            min-width: 1000px;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #d1d5db;
            padding: 8px;
            font-size: 12px;
            text-align: left;
            vertical-align: middle;
        }

        th {
            background: #7c3aed;
            color: white;
        }

        .estado-activo {
            font-weight: bold;
            color: #166534;
        }

        .estado-inactivo {
            font-weight: bold;
            color: #991b1b;
        }

        .sin-registros {
            text-align: center;
            padding: 20px;
            color: #6b7280;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            background: #fafafa;
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

            @page {
                size: landscape;
                margin: 12mm;
            }
        }
    </style>
</head>
<body>

<div class="contenedor">

    <div class="acciones">
        <a href="reporte_categorias.php?buscar=<?php echo urlencode($buscar); ?>&activo=<?php echo urlencode($activo); ?>" class="btn btn-volver">Volver</a>
        <a href="reportes.php" class="btn btn-reportes">Reportes</a>
        <button onclick="window.print()" class="btn btn-imprimir">Imprimir / Guardar PDF</button>
    </div>

    <div class="encabezado">
        <h1>Reporte de Categorías</h1>
        <p><strong>SIGENMUNI</strong> - Municipalidad de Fortín Lugones</p>
        <p>Fecha de emisión: <?php echo date("d/m/Y H:i:s"); ?></p>

        <div class="resumen">
            <div class="info-box">
                <strong>Búsqueda aplicada</strong>
                <?php echo htmlspecialchars($buscar !== "" ? $buscar : "Todos"); ?>
            </div>

            <div class="info-box">
                <strong>Estado</strong>
                <?php echo textoEstadoCategoria($activo); ?>
            </div>

            <div class="info-box">
                <strong>Total de categorías</strong>
                <?php echo (int)$totalCategorias; ?>
            </div>
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

                        <?php if ($tieneActivo): ?>
                            <th>Estado</th>
                        <?php endif; ?>
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

                            <?php if ($tieneActivo): ?>
                                <td>
                                    <?php if ((int)$fila['activo'] === 1): ?>
                                        <span class="estado-activo">Activa</span>
                                    <?php else: ?>
                                        <span class="estado-inactivo">Inactiva</span>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
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

</body>
</html>