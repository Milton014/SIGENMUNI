<?php
session_start();
require_once("conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    die("ID no válido.");
}

$empleados = $conexion->query("
    SELECT id, nro_legajo, apellido, nombre
    FROM empleado
    WHERE activo = 1
    ORDER BY apellido ASC, nombre ASC
");

$conceptos = $conexion->query("
    SELECT id, codigo, nombre
    FROM concepto
    WHERE activo = 1
    ORDER BY nombre ASC
");

if (!$empleados) {
    die("Error al cargar empleados: " . $conexion->error);
}

if (!$conceptos) {
    die("Error al cargar conceptos: " . $conexion->error);
}

$stmt = $conexion->prepare("
    SELECT id, empleado_id, concepto_id, monto_manual, porcentaje_manual, cantidad, fecha_desde, fecha_hasta, activo, observacion
    FROM empleado_concepto
    WHERE id = ?
");

if (!$stmt) {
    die("Error en prepare: " . $conexion->error);
}

$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows == 0) {
    die("Registro no encontrado.");
}

$fila = $resultado->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Asignación de Concepto</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            margin: 0;
            background: #f4f6f9;
            padding: 20px;
        }

        .contenedor {
            max-width: 800px;
            margin: auto;
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        h2 {
            margin-top: 0;
            color: #2c3e50;
            margin-bottom: 20px;
        }

        .grupo {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
            color: #333;
        }

        input, select, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }

        textarea {
            resize: vertical;
            min-height: 90px;
        }

        .acciones {
            margin-top: 20px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
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

        .btn-guardar {
            background: #28a745;
        }

        .btn-cancelar {
            background: #6c757d;
        }
    </style>
</head>
<body>

<div class="contenedor">
    <h2>Editar Asignación de Concepto</h2>

    <form action="empleado_concepto_actualizar.php" method="POST">
        <input type="hidden" name="id" value="<?php echo $fila['id']; ?>">

        <div class="grupo">
            <label for="empleado_id">Empleado</label>
            <select name="empleado_id" id="empleado_id" required>
                <option value="">Seleccione un empleado</option>
                <?php while ($emp = $empleados->fetch_assoc()) { ?>
                    <option value="<?php echo $emp['id']; ?>" <?php echo ($fila['empleado_id'] == $emp['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($emp['apellido'] . ', ' . $emp['nombre'] . ' - Legajo: ' . $emp['nro_legajo']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="grupo">
            <label for="concepto_id">Concepto</label>
            <select name="concepto_id" id="concepto_id" required>
                <option value="">Seleccione un concepto</option>
                <?php while ($con = $conceptos->fetch_assoc()) { ?>
                    <option value="<?php echo $con['id']; ?>" <?php echo ($fila['concepto_id'] == $con['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($con['codigo'] . ' - ' . $con['nombre']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="grupo">
            <label for="monto_manual">Monto Manual</label>
            <input type="number" step="0.01" name="monto_manual" id="monto_manual" value="<?php echo $fila['monto_manual']; ?>">
        </div>

        <div class="grupo">
            <label for="porcentaje_manual">Porcentaje Manual</label>
            <input type="number" step="0.01" name="porcentaje_manual" id="porcentaje_manual" value="<?php echo $fila['porcentaje_manual']; ?>">
        </div>

        <div class="grupo">
            <label for="cantidad">Cantidad</label>
            <input type="number" step="0.01" name="cantidad" id="cantidad" value="<?php echo $fila['cantidad']; ?>">
        </div>

        <div class="grupo">
            <label for="fecha_desde">Fecha Desde</label>
            <input type="date" name="fecha_desde" id="fecha_desde" value="<?php echo $fila['fecha_desde']; ?>" required>
        </div>

        <div class="grupo">
            <label for="fecha_hasta">Fecha Hasta</label>
            <input type="date" name="fecha_hasta" id="fecha_hasta" value="<?php echo $fila['fecha_hasta']; ?>">
        </div>

        <div class="grupo">
            <label for="observacion">Observación</label>
            <textarea name="observacion" id="observacion"><?php echo htmlspecialchars($fila['observacion'] ?? ''); ?></textarea>
        </div>

        <div class="acciones">
            <button type="submit" class="btn btn-guardar">Actualizar</button>
            <a href="empleado_conceptos.php" class="btn btn-cancelar">Cancelar</a>
        </div>
    </form>
</div>

</body>
</html>