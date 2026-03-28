<?php
session_start();
require_once("conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$buscar_empleado = isset($_GET['buscar_empleado']) ? trim($_GET['buscar_empleado']) : "";
$concepto_id = isset($_GET['concepto_id']) ? (int)$_GET['concepto_id'] : 0;
$activo = isset($_GET['activo']) ? trim($_GET['activo']) : "";

$sql = "SELECT 
            ec.id,
            ec.empleado_id,
            ec.concepto_id,
            ec.monto_manual,
            ec.porcentaje_manual,
            ec.cantidad,
            ec.fecha_desde,
            ec.fecha_hasta,
            ec.activo,
            ec.observacion,
            e.nro_legajo,
            e.apellido,
            e.nombre,
            c.codigo,
            c.nombre AS concepto
        FROM empleado_concepto ec
        INNER JOIN empleado e ON ec.empleado_id = e.id
        INNER JOIN concepto c ON ec.concepto_id = c.id
        WHERE 1=1";

$params = [];
$types = "";

if ($buscar_empleado !== "") {
    $sql .= " AND (
                e.apellido LIKE ?
                OR e.nombre LIKE ?
                OR CONCAT(e.apellido, ' ', e.nombre) LIKE ?
                OR CONCAT(e.nombre, ' ', e.apellido) LIKE ?
                OR CAST(e.nro_legajo AS CHAR) LIKE ?
             )";
    $like = "%" . $buscar_empleado . "%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $types .= "sssss";
}

if ($concepto_id > 0) {
    $sql .= " AND ec.concepto_id = ?";
    $params[] = $concepto_id;
    $types .= "i";
}

if ($activo !== "") {
    $sql .= " AND ec.activo = ?";
    $params[] = (int)$activo;
    $types .= "i";
}

$sql .= " ORDER BY e.apellido ASC, e.nombre ASC, ec.fecha_desde DESC";

$stmt = $conexion->prepare($sql);

if (!$stmt) {
    die("Error en la consulta: " . $conexion->error);
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$resultado = $stmt->get_result();

$conceptos = $conexion->query("SELECT id, codigo, nombre FROM concepto WHERE activo = 1 ORDER BY nombre ASC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Empleado - Conceptos</title>
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
            max-width: 1400px;
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

        .btn-nuevo { background: #28a745; }
        .btn-buscar { background: #007bff; }
        .btn-editar {
            background: #f0ad4e;
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
        .btn-volver { background: #343a40; }

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
            min-width: 1300px;
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
        <h1>Asignación de Conceptos a Empleados</h1>
        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <a href="empleado_concepto_nuevo.php" class="btn btn-nuevo">+ Nueva Asignación</a>
            <a href="index.php" class="btn btn-volver">Volver</a>
        </div>
    </div>

    <?php if (isset($_GET['ok'])): ?>
        <div class="mensaje">
            <?php
            switch ($_GET['ok']) {
                case '1':
                    echo "Asignación guardada correctamente.";
                    break;
                case '2':
                    echo "Asignación actualizada correctamente.";
                    break;
                case '3':
                    echo "Estado de la asignación actualizado correctamente.";
                    break;
                default:
                    echo "Operación realizada correctamente.";
                    break;
            }
            ?>
        </div>
    <?php endif; ?>

    <form method="GET" action="empleado_conceptos.php" class="filtros">
        <input type="text" name="buscar_empleado" placeholder="Buscar por apellido, nombre o legajo" value="<?php echo htmlspecialchars($buscar_empleado); ?>">

        <select name="concepto_id">
            <option value="">-- Todos los conceptos --</option>
            <?php while ($con = $conceptos->fetch_assoc()): ?>
                <option value="<?php echo $con['id']; ?>" <?php echo ($concepto_id == $con['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($con['codigo'] . ' - ' . $con['nombre']); ?>
                </option>
            <?php endwhile; ?>
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
                    <th>Empleado</th>
                    <th>Legajo</th>
                    <th>Concepto</th>
                    <th>Monto Manual</th>
                    <th>% Manual</th>
                    <th>Cantidad</th>
                    <th>Fecha Desde</th>
                    <th>Fecha Hasta</th>
                    <th>Estado</th>
                    <th>Observación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($resultado->num_rows > 0): ?>
                    <?php while ($fila = $resultado->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $fila['id']; ?></td>
                            <td><?php echo htmlspecialchars($fila['apellido'] . ', ' . $fila['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($fila['nro_legajo']); ?></td>
                            <td><?php echo htmlspecialchars($fila['codigo'] . ' - ' . $fila['concepto']); ?></td>
                            <td>$ <?php echo number_format((float)$fila['monto_manual'], 2, ',', '.'); ?></td>
                            <td><?php echo number_format((float)$fila['porcentaje_manual'], 2, ',', '.'); ?></td>
                            <td><?php echo number_format((float)$fila['cantidad'], 2, ',', '.'); ?></td>
                            <td><?php echo !empty($fila['fecha_desde']) ? date("d/m/Y", strtotime($fila['fecha_desde'])) : '-'; ?></td>
                            <td><?php echo !empty($fila['fecha_hasta']) ? date("d/m/Y", strtotime($fila['fecha_hasta'])) : '-'; ?></td>
                            <td>
                                <?php if ((int)$fila['activo'] === 1): ?>
                                    <span class="estado-activo">Activo</span>
                                <?php else: ?>
                                    <span class="estado-inactivo">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($fila['observacion'] ?? ''); ?></td>
                            <td>
                                <div class="acciones">
                                    <a class="btn btn-editar" href="empleado_concepto_editar.php?id=<?php echo $fila['id']; ?>">Editar</a>

                                    <?php if ((int)$fila['activo'] === 1): ?>
                                        <a class="btn btn-estado"
                                           href="empleado_concepto_estado.php?id=<?php echo $fila['id']; ?>&estado=0"
                                           onclick="return confirm('¿Seguro que desea inactivar esta asignación?');">
                                           Inactivar
                                        </a>
                                    <?php else: ?>
                                        <a class="btn btn-estado"
                                           href="empleado_concepto_estado.php?id=<?php echo $fila['id']; ?>&estado=1"
                                           onclick="return confirm('¿Seguro que desea activar esta asignación?');">
                                           Activar
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="12" class="sin-resultados">No se encontraron asignaciones.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>