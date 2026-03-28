<?php
session_start();
require_once("conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$busqueda = trim($_GET['busqueda'] ?? "");

$sql = "SELECT 
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
        INNER JOIN situacion s ON e.situacion_id = s.id";

if ($busqueda !== "") {
    $sql .= " WHERE e.nro_legajo LIKE ? 
              OR e.apellido LIKE ?
              OR e.nombre LIKE ?
              OR e.dni LIKE ?
              OR e.cuil LIKE ?
              OR e.email LIKE ?";
}

$sql .= " ORDER BY e.apellido, e.nombre";

$stmt = $conexion->prepare($sql);

if ($busqueda !== "") {
    $like = "%$busqueda%";
    $stmt->bind_param("ssssss", $like, $like, $like, $like, $like, $like);
}

$stmt->execute();
$resultado = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Personal</title>
    <style>
        body { font-family: Arial, sans-serif; background:#f4f7fb; margin:0; }
        .contenedor { width: 96%; max-width: 1300px; margin: 25px auto; }
        .topbar { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; gap: 10px; flex-wrap: wrap; }
        .btn {
            text-decoration:none; background:green; color:white; padding:10px 14px;
            border-radius:8px; display:inline-block; border:none; cursor:pointer;
        }
        .btn-sec { background:#333; }
        .buscador {
            background:white; padding:15px; border-radius:10px; margin-bottom:20px;
        }
        input[type="text"] {
            padding:10px; width:340px; border:1px solid #ccc; border-radius:6px;
        }
        table {
            width:100%; border-collapse:collapse; background:white; border-radius:10px; overflow:hidden;
        }
        th, td { padding:12px; border-bottom:1px solid #eee; text-align:left; font-size: 14px; }
        th { background:#0f766e; color:white; }
        .estado-activo { color:green; font-weight:bold; }
        .estado-inactivo { color:red; font-weight:bold; }
        .acciones a {
            margin-right:8px; text-decoration:none; font-weight:bold;
        }
    </style>
</head>
<body>
<div class="contenedor">

    <div class="topbar">
        <h1>Gestión de Personal</h1>
        <div>
            <a href="empleado_nuevo.php" class="btn">+ Nuevo Empleado</a>
            <a href="index.php" class="btn btn-sec">Volver al menú</a>
        </div>
    </div>

    <div class="buscador">
        <form method="GET">
            <input type="text" name="busqueda" placeholder="Buscar por legajo, apellido, nombre, DNI, CUIL o email"
                   value="<?php echo htmlspecialchars($busqueda); ?>">
            <button type="submit" class="btn">Buscar</button>
        </form>
    </div>

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
        <?php while ($fila = $resultado->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($fila['nro_legajo']); ?></td>
                <td><?php echo htmlspecialchars($fila['apellido'] . ", " . $fila['nombre']); ?></td>
                <td><?php echo htmlspecialchars($fila['dni']); ?></td>
                <td><?php echo htmlspecialchars($fila['cuil']); ?></td>
                <td><?php echo htmlspecialchars($fila['telefono'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($fila['email'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($fila['fecha_alta']); ?></td>
                <td><?php echo htmlspecialchars($fila['fecha_baja'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($fila['categoria']); ?></td>
                <td><?php echo htmlspecialchars($fila['oficina']); ?></td>
                <td><?php echo htmlspecialchars($fila['situacion']); ?></td>
                <td>
                    <?php if ($fila['activo']) { ?>
                        <span class="estado-activo">Activo</span>
                    <?php } else { ?>
                        <span class="estado-inactivo">Inactivo</span>
                    <?php } ?>
                </td>
                <td class="acciones">
                    <a href="empleado_editar.php?id=<?php echo $fila['id']; ?>">Editar</a>
                    <?php if ($fila['activo']) { ?>
                        <a href="empleado_estado.php?id=<?php echo $fila['id']; ?>&accion=inactivar"
                           onclick="return confirm('¿Desea inactivar este empleado?')">Inactivar</a>
                    <?php } else { ?>
                        <a href="empleado_estado.php?id=<?php echo $fila['id']; ?>&accion=activar"
                           onclick="return confirm('¿Desea activar este empleado?')">Activar</a>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

</div>
</body>
</html>