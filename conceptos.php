<?php
session_start();
require_once("conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$buscar = isset($_GET['buscar']) ? trim($_GET['buscar']) : "";
$categoria = isset($_GET['categoria']) ? trim($_GET['categoria']) : "";
$activo = isset($_GET['activo']) ? trim($_GET['activo']) : "";

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

$categorias = [
    "REMUNERATIVO",
    "NO_REMUNERATIVO",
    "ASIGNACION_FAMILIAR",
    "DESCUENTO",
    "APORTE_PATRONAL"
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Conceptos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            margin: 0;
            background: #f4f6f9;
        }

        .contenedor {
            width: 95%;
            max-width: 1300px;
            margin: 30px auto;
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        h1 {
            margin-top: 0;
            color: #2c3e50;
        }

        .acciones-superiores {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }

        .btn {
            display: inline-block;
            padding: 10px 16px;
            text-decoration: none;
            border-radius: 6px;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-nuevo {
            background: #28a745;
        }

        .btn-buscar {
            background: #007bff;
        }

        .btn-editar {
            background: #f0ad4e;
            color: #fff;
            padding: 7px 12px;
            font-size: 13px;
        }

        .btn-valores {
            background: #17a2b8;
            color: #fff;
            padding: 7px 12px;
            font-size: 13px;
        }

        .btn-estado {
            background: #6c757d;
            color: #fff;
            padding: 7px 12px;
            font-size: 13px;
        }

        .btn-volver {
            background: #343a40;
        }

        .filtros {
            display: grid;
            grid-template-columns: 2fr 1.5fr 1fr auto;
            gap: 12px;
            margin-bottom: 20px;
        }

        .filtros input,
        .filtros select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        .tabla-contenedor {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1200px;
        }

        th, td {
            padding: 10px 8px;
            border-bottom: 1px solid #ddd;
            text-align: left;
            vertical-align: middle;
            font-size: 14px;
        }

        th {
            background: #f8f9fa;
            color: #333;
        }

        .estado-activo {
            color: #28a745;
            font-weight: bold;
        }

        .estado-inactivo {
            color: #dc3545;
            font-weight: bold;
        }

        .acciones {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            color: white;
            white-space: nowrap;
        }

        .badge-rem { background: #007bff; }
        .badge-no-rem { background: #6f42c1; }
        .badge-asig { background: #20c997; }
        .badge-desc { background: #dc3545; }
        .badge-aporte { background: #fd7e14; }

        .mensaje {
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 6px;
            background: #d4edda;
            color: #155724;
        }

        .sin-resultados {
            text-align: center;
            padding: 20px;
            color: #666;
        }

        @media (max-width: 900px) {
            .filtros {
                grid-template-columns: 1fr;
            }

            .acciones-superiores {
                flex-direction: column;
                align-items: stretch;
            }
        }
    </style>
</head>
<body>

<div class="contenedor">
    <div class="acciones-superiores">
        <h1>Gestión de Conceptos</h1>
        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <a href="concepto_nuevo.php" class="btn btn-nuevo">+ Nuevo Concepto</a>
            <a href="index.php" class="btn btn-volver">Volver</a>
        </div>
    </div>

    <?php if (isset($_GET['ok'])): ?>
        <div class="mensaje">
            <?php
            switch ($_GET['ok']) {
                case '1':
                    echo "Concepto guardado correctamente.";
                    break;
                case '2':
                    echo "Concepto actualizado correctamente.";
                    break;
                case '3':
                    echo "Estado del concepto actualizado correctamente.";
                    break;
                default:
                    echo "Operación realizada correctamente.";
                    break;
            }
            ?>
        </div>
    <?php endif; ?>

    <form method="GET" action="conceptos.php" class="filtros">
        <input type="text" name="buscar" placeholder="Buscar por código o nombre" value="<?php echo htmlspecialchars($buscar); ?>">

        <select name="categoria">
            <option value="">-- Todas las categorías --</option>
            <?php foreach ($categorias as $cat): ?>
                <option value="<?php echo $cat; ?>" <?php echo ($categoria === $cat) ? 'selected' : ''; ?>>
                    <?php echo $cat; ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="activo">
            <option value="">-- Todos los estados --</option>
            <option value="1" <?php echo ($activo === "1") ? 'selected' : ''; ?>>Activos</option>
            <option value="0" <?php echo ($activo === "0") ? 'selected' : ''; ?>>Inactivos</option>
        </select>

        <button type="submit" class="btn btn-buscar">Buscar</button>
    </form>

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
                    <th>SAC</th>
                    <th>Visible Recibo</th>
                    <th>Activo</th>
                    <th>Vigencia</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($resultado->num_rows > 0): ?>
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
                        ?>
                        <tr>
                            <td><?php echo $fila['id']; ?></td>
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
                            <td>
                                <?php
                                $desde = !empty($fila['fecha_desde']) ? date("d/m/Y", strtotime($fila['fecha_desde'])) : "-";
                                $hasta = !empty($fila['fecha_hasta']) ? date("d/m/Y", strtotime($fila['fecha_hasta'])) : "-";
                                echo $desde . " / " . $hasta;
                                ?>
                            </td>
                            <td>
                                <div class="acciones">
                                    <a class="btn btn-editar" href="concepto_editar.php?id=<?php echo $fila['id']; ?>">Editar</a>

                                    <a class="btn btn-valores" href="concepto_valores.php?concepto_id=<?php echo $fila['id']; ?>">
                                        Valores
                                    </a>

                                    <?php if ((int)$fila['activo'] === 1): ?>
                                        <a class="btn btn-estado"
                                           href="concepto_estado.php?id=<?php echo $fila['id']; ?>&estado=0"
                                           onclick="return confirm('¿Seguro que desea inactivar este concepto?');">
                                           Inactivar
                                        </a>
                                    <?php else: ?>
                                        <a class="btn btn-estado"
                                           href="concepto_estado.php?id=<?php echo $fila['id']; ?>&estado=1"
                                           onclick="return confirm('¿Seguro que desea activar este concepto?');">
                                           Activar
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="15" class="sin-resultados">No se encontraron conceptos.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>