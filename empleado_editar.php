<?php
session_start();
require_once("conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: empleados.php");
    exit();
}

$mensaje = "";

// Buscar empleado
$stmt = $conexion->prepare("SELECT * FROM empleado WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$empleado = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$empleado) {
    die("Empleado no encontrado.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $institucion_id = (int)$_POST['institucion_id'];
    $oficina_id = (int)$_POST['oficina_id'];
    $situacion_id = (int)$_POST['situacion_id'];
    $escalafon_id = (int)$_POST['escalafon_id'];
    $categoria_id = (int)$_POST['categoria_id'];

    $nro_legajo = trim($_POST['nro_legajo']);
    $apellido = trim($_POST['apellido']);
    $nombre = trim($_POST['nombre']);
    $dni = trim($_POST['dni']);
    $cuil = trim($_POST['cuil']);
    $fecha_alta = $_POST['fecha_alta'];
    $fecha_baja = !empty($_POST['fecha_baja']) ? $_POST['fecha_baja'] : null;
    $telefono = trim($_POST['telefono']);
    $email = trim($_POST['email']);
    $domicilio = trim($_POST['domicilio']);
    $observaciones = trim($_POST['observaciones']);

    if (empty($nro_legajo) || empty($apellido) || empty($nombre) || empty($dni) || empty($cuil) || empty($fecha_alta)) {
        $mensaje = "Complete los campos obligatorios.";
    } else {
        // Validar legajo único
        $stmtVal = $conexion->prepare("SELECT id FROM empleado WHERE nro_legajo = ? AND id <> ? LIMIT 1");
        $stmtVal->bind_param("si", $nro_legajo, $id);
        $stmtVal->execute();
        $resVal = $stmtVal->get_result();

        if ($resVal->num_rows > 0) {
            $mensaje = "Ya existe otro empleado con ese legajo.";
        } else {
            // Validar DNI único
            $stmtVal = $conexion->prepare("SELECT id FROM empleado WHERE dni = ? AND id <> ? LIMIT 1");
            $stmtVal->bind_param("si", $dni, $id);
            $stmtVal->execute();
            $resVal = $stmtVal->get_result();

            if ($resVal->num_rows > 0) {
                $mensaje = "Ya existe otro empleado con ese DNI.";
            } else {
                // Validar CUIL único
                $stmtVal = $conexion->prepare("SELECT id FROM empleado WHERE cuil = ? AND id <> ? LIMIT 1");
                $stmtVal->bind_param("si", $cuil, $id);
                $stmtVal->execute();
                $resVal = $stmtVal->get_result();

                if ($resVal->num_rows > 0) {
                    $mensaje = "Ya existe otro empleado con ese CUIL.";
                } else {
                    $stmt = $conexion->prepare("
                        UPDATE empleado SET
                            institucion_id = ?,
                            oficina_id = ?,
                            situacion_id = ?,
                            escalafon_id = ?,
                            categoria_id = ?,
                            nro_legajo = ?,
                            apellido = ?,
                            nombre = ?,
                            dni = ?,
                            cuil = ?,
                            fecha_alta = ?,
                            fecha_baja = ?,
                            telefono = ?,
                            email = ?,
                            domicilio = ?,
                            observaciones = ?
                        WHERE id = ?
                    ");

                    $stmt->bind_param(
                        "iiiiisssssssssssi",
                        $institucion_id,
                        $oficina_id,
                        $situacion_id,
                        $escalafon_id,
                        $categoria_id,
                        $nro_legajo,
                        $apellido,
                        $nombre,
                        $dni,
                        $cuil,
                        $fecha_alta,
                        $fecha_baja,
                        $telefono,
                        $email,
                        $domicilio,
                        $observaciones,
                        $id
                    );

                    if ($stmt->execute()) {
                        header("Location: empleados.php");
                        exit();
                    } else {
                        $mensaje = "Error al actualizar: " . $stmt->error;
                    }

                    $stmt->close();
                }
            }
        }
    }

    $empleado = array_merge($empleado, $_POST);
}

// Combos sin WHERE activo=1
$instituciones = $conexion->query("SELECT id, nombre FROM institucion ORDER BY nombre");
$oficinas = $conexion->query("SELECT id, nombre FROM oficina ORDER BY nombre");
$situaciones = $conexion->query("SELECT id, nombre FROM situacion ORDER BY nombre");
$escalafones = $conexion->query("SELECT id, nombre FROM escalafon ORDER BY nombre");
$categorias = $conexion->query("SELECT id, nombre FROM categoria ORDER BY nombre");
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Editar Empleado</title>
<style>
body{font-family:Arial;background:#f4f7fb;margin:0}
.contenedor{max-width:1000px;margin:25px auto;background:white;padding:25px;border-radius:12px;box-shadow:0 8px 20px rgba(0,0,0,.08)}
.grid{display:grid;grid-template-columns:repeat(2,1fr);gap:15px}
label{display:block;margin-bottom:5px;font-weight:bold}
input,select,textarea{width:100%;padding:10px;border:1px solid #ccc;border-radius:6px;box-sizing:border-box}
textarea{min-height:90px;resize:vertical}
.btn{background:green;color:white;padding:10px 14px;border:none;border-radius:8px;text-decoration:none;cursor:pointer}
.btn-sec{background:#333}
.error{color:red;margin-bottom:15px}
</style>
</head>
<body>
<div class="contenedor">
<h2>Editar Empleado</h2>

<?php if($mensaje): ?>
    <p class="error"><?php echo htmlspecialchars($mensaje); ?></p>
<?php endif; ?>

<form method="POST">
<div class="grid">

<div>
<label>Legajo</label>
<input name="nro_legajo" required value="<?php echo htmlspecialchars($empleado['nro_legajo'] ?? ''); ?>">
</div>

<div>
<label>Apellido</label>
<input name="apellido" required value="<?php echo htmlspecialchars($empleado['apellido'] ?? ''); ?>">
</div>

<div>
<label>Nombre</label>
<input name="nombre" required value="<?php echo htmlspecialchars($empleado['nombre'] ?? ''); ?>">
</div>

<div>
<label>DNI</label>
<input name="dni" required value="<?php echo htmlspecialchars($empleado['dni'] ?? ''); ?>">
</div>

<div>
<label>CUIL</label>
<input name="cuil" required value="<?php echo htmlspecialchars($empleado['cuil'] ?? ''); ?>">
</div>

<div>
<label>Fecha Alta</label>
<input type="date" name="fecha_alta" required value="<?php echo htmlspecialchars($empleado['fecha_alta'] ?? ''); ?>">
</div>

<div>
<label>Fecha Baja</label>
<input type="date" name="fecha_baja" value="<?php echo htmlspecialchars($empleado['fecha_baja'] ?? ''); ?>">
</div>

<div>
<label>Teléfono</label>
<input name="telefono" value="<?php echo htmlspecialchars($empleado['telefono'] ?? ''); ?>">
</div>

<div>
<label>Email</label>
<input type="email" name="email" value="<?php echo htmlspecialchars($empleado['email'] ?? ''); ?>">
</div>

<div>
<label>Domicilio</label>
<input name="domicilio" value="<?php echo htmlspecialchars($empleado['domicilio'] ?? ''); ?>">
</div>

<div>
<label>Institución</label>
<select name="institucion_id" required>
<option value="">Seleccione</option>
<?php while($x = $instituciones->fetch_assoc()): ?>
<option value="<?php echo $x['id']; ?>" <?php echo (($empleado['institucion_id'] ?? '') == $x['id']) ? 'selected' : ''; ?>>
    <?php echo htmlspecialchars($x['nombre']); ?>
</option>
<?php endwhile; ?>
</select>
</div>

<div>
<label>Oficina</label>
<select name="oficina_id" required>
<option value="">Seleccione</option>
<?php while($x = $oficinas->fetch_assoc()): ?>
<option value="<?php echo $x['id']; ?>" <?php echo (($empleado['oficina_id'] ?? '') == $x['id']) ? 'selected' : ''; ?>>
    <?php echo htmlspecialchars($x['nombre']); ?>
</option>
<?php endwhile; ?>
</select>
</div>

<div>
<label>Situación</label>
<select name="situacion_id" required>
<option value="">Seleccione</option>
<?php while($x = $situaciones->fetch_assoc()): ?>
<option value="<?php echo $x['id']; ?>" <?php echo (($empleado['situacion_id'] ?? '') == $x['id']) ? 'selected' : ''; ?>>
    <?php echo htmlspecialchars($x['nombre']); ?>
</option>
<?php endwhile; ?>
</select>
</div>

<div>
<label>Escalafón</label>
<select name="escalafon_id" required>
<option value="">Seleccione</option>
<?php while($x = $escalafones->fetch_assoc()): ?>
<option value="<?php echo $x['id']; ?>" <?php echo (($empleado['escalafon_id'] ?? '') == $x['id']) ? 'selected' : ''; ?>>
    <?php echo htmlspecialchars($x['nombre']); ?>
</option>
<?php endwhile; ?>
</select>
</div>

<div>
<label>Categoría</label>
<select name="categoria_id" required>
<option value="">Seleccione</option>
<?php while($x = $categorias->fetch_assoc()): ?>
<option value="<?php echo $x['id']; ?>" <?php echo (($empleado['categoria_id'] ?? '') == $x['id']) ? 'selected' : ''; ?>>
    <?php echo htmlspecialchars($x['nombre']); ?>
</option>
<?php endwhile; ?>
</select>
</div>

<div style="grid-column:1/-1;">
<label>Observaciones</label>
<textarea name="observaciones"><?php echo htmlspecialchars($empleado['observaciones'] ?? ''); ?></textarea>
</div>

</div>

<br>
<button class="btn">Actualizar</button>
<a href="empleados.php" class="btn btn-sec">Cancelar</a>
</form>
</div>
</body>
</html>