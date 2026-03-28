<?php
session_start();
require_once("conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$mensaje = "";
$tipo_mensaje = "error";

$instituciones = $conexion->query("SELECT id, nombre FROM institucion ORDER BY nombre");
$oficinas = $conexion->query("SELECT id, nombre FROM oficina ORDER BY nombre");
$situaciones = $conexion->query("SELECT id, nombre FROM situacion ORDER BY nombre");
$escalafones = $conexion->query("SELECT id, nombre FROM escalafon ORDER BY nombre");
$categorias = $conexion->query("SELECT id, nombre FROM categoria ORDER BY nombre");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $institucion_id = isset($_POST['institucion_id']) ? (int)$_POST['institucion_id'] : 0;
        $oficina_id = isset($_POST['oficina_id']) ? (int)$_POST['oficina_id'] : 0;
        $situacion_id = isset($_POST['situacion_id']) ? (int)$_POST['situacion_id'] : 0;
        $escalafon_id = isset($_POST['escalafon_id']) ? (int)$_POST['escalafon_id'] : 0;
        $categoria_id = isset($_POST['categoria_id']) ? (int)$_POST['categoria_id'] : 0;

        $nro_legajo = trim($_POST['nro_legajo'] ?? '');
        $apellido = trim($_POST['apellido'] ?? '');
        $nombre = trim($_POST['nombre'] ?? '');
        $dni = trim($_POST['dni'] ?? '');
        $cuil = trim($_POST['cuil'] ?? '');
        $fecha_alta = trim($_POST['fecha_alta'] ?? '');
        $fecha_baja = trim($_POST['fecha_baja'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $domicilio = trim($_POST['domicilio'] ?? '');
        $observaciones = trim($_POST['observaciones'] ?? '');

        // Convertir vacíos a NULL donde corresponde
        $fecha_baja = ($fecha_baja === '') ? null : $fecha_baja;
        $telefono = ($telefono === '') ? null : $telefono;
        $email = ($email === '') ? null : $email;
        $domicilio = ($domicilio === '') ? null : $domicilio;
        $observaciones = ($observaciones === '') ? null : $observaciones;

        // Validaciones obligatorias
        if (
            $nro_legajo === '' || $apellido === '' || $nombre === '' ||
            $dni === '' || $cuil === '' || $fecha_alta === '' ||
            $institucion_id <= 0 || $oficina_id <= 0 || $situacion_id <= 0 ||
            $escalafon_id <= 0 || $categoria_id <= 0
        ) {
            throw new Exception("Complete todos los campos obligatorios.");
        }

        // Validación numérica
        if (!ctype_digit($nro_legajo)) {
            throw new Exception("El número de legajo debe contener solo números.");
        }

        if (!ctype_digit($dni)) {
            throw new Exception("El DNI debe contener solo números.");
        }

        if (!ctype_digit($cuil)) {
            throw new Exception("El CUIL debe contener solo números.");
        }

        // Validación de longitudes orientativas
        if (strlen($dni) < 7 || strlen($dni) > 8) {
            throw new Exception("El DNI debe tener 7 u 8 dígitos.");
        }

        if (strlen($cuil) != 11) {
            throw new Exception("El CUIL debe tener exactamente 11 dígitos.");
        }

        // Validar email solo si fue cargado
        if ($email !== null && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("El email ingresado no tiene un formato válido.");
        }

        // Validar legajo único
        $stmtVal = $conexion->prepare("SELECT id FROM empleado WHERE nro_legajo = ? LIMIT 1");
        $stmtVal->bind_param("s", $nro_legajo);
        $stmtVal->execute();
        $resVal = $stmtVal->get_result();

        if ($resVal->num_rows > 0) {
            throw new Exception("Ya existe un empleado con ese número de legajo.");
        }

        // Validar DNI único
        $stmtVal = $conexion->prepare("SELECT id FROM empleado WHERE dni = ? LIMIT 1");
        $stmtVal->bind_param("s", $dni);
        $stmtVal->execute();
        $resVal = $stmtVal->get_result();

        if ($resVal->num_rows > 0) {
            throw new Exception("Ya existe un empleado con ese DNI.");
        }

        // Validar CUIL único
        $stmtVal = $conexion->prepare("SELECT id FROM empleado WHERE cuil = ? LIMIT 1");
        $stmtVal->bind_param("s", $cuil);
        $stmtVal->execute();
        $resVal = $stmtVal->get_result();

        if ($resVal->num_rows > 0) {
            throw new Exception("Ya existe un empleado con ese CUIL.");
        }

        // Validar email único solo si fue cargado
        if ($email !== null) {
            $stmtVal = $conexion->prepare("SELECT id FROM empleado WHERE email = ? LIMIT 1");
            $stmtVal->bind_param("s", $email);
            $stmtVal->execute();
            $resVal = $stmtVal->get_result();

            if ($resVal->num_rows > 0) {
                throw new Exception("Ya existe un empleado con ese email.");
            }
        }

        // Insertar
        $stmt = $conexion->prepare("
            INSERT INTO empleado (
                institucion_id, oficina_id, situacion_id, escalafon_id, categoria_id,
                nro_legajo, apellido, nombre, dni, cuil,
                fecha_alta, fecha_baja, telefono, email, domicilio, observaciones, activo
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)
        ");

        if (!$stmt) {
            throw new Exception("No se pudo preparar la consulta de guardado.");
        }

        $stmt->bind_param(
            "iiiiisssssssssss",
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
            $observaciones
        );

        if ($stmt->execute()) {
            $mensaje = "Empleado guardado correctamente.";
            $tipo_mensaje = "ok";

            // Redirección
            header("refresh:1;url=empleados.php");
        } else {
            throw new Exception("No se pudo guardar el empleado.");
        }

    } catch (Exception $e) {
        $mensaje = $e->getMessage();
        $tipo_mensaje = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Nuevo Empleado</title>
<style>
body{
    font-family:Arial;
    background:#f4f7fb;
    margin:0;
}
.contenedor{
    max-width:1000px;
    margin:25px auto;
    background:white;
    padding:25px;
    border-radius:12px;
    box-shadow:0 8px 20px rgba(0,0,0,.08);
}
.grid{
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:15px;
}
label{
    display:block;
    margin-bottom:5px;
    font-weight:bold;
}
input,select,textarea{
    width:100%;
    padding:10px;
    border:1px solid #ccc;
    border-radius:6px;
    box-sizing:border-box;
}
textarea{
    min-height:90px;
    resize:vertical;
}
.btn{
    background:green;
    color:white;
    padding:10px 14px;
    border:none;
    border-radius:8px;
    text-decoration:none;
    cursor:pointer;
    display:inline-block;
}
.btn-sec{
    background:#333;
}
.mensaje{
    padding:12px;
    border-radius:8px;
    margin-bottom:15px;
    font-weight:bold;
}
.error{
    background:#ffe5e5;
    color:#b30000;
    border:1px solid #ffb3b3;
}
.ok{
    background:#e8f7e8;
    color:#1f7a1f;
    border:1px solid #b7e1b7;
}
</style>
</head>
<body>
<div class="contenedor">
<h2>Alta de Empleado</h2>

<?php if($mensaje): ?>
    <div class="mensaje <?php echo $tipo_mensaje === 'ok' ? 'ok' : 'error'; ?>">
        <?php echo htmlspecialchars($mensaje); ?>
    </div>
<?php endif; ?>

<form method="POST">
<div class="grid">

<div>
<label>Legajo *</label>
<input name="nro_legajo" required value="<?php echo htmlspecialchars($_POST['nro_legajo'] ?? ''); ?>">
</div>

<div>
<label>Apellido *</label>
<input name="apellido" required value="<?php echo htmlspecialchars($_POST['apellido'] ?? ''); ?>">
</div>

<div>
<label>Nombre *</label>
<input name="nombre" required value="<?php echo htmlspecialchars($_POST['nombre'] ?? ''); ?>">
</div>

<div>
<label>DNI *</label>
<input name="dni" required value="<?php echo htmlspecialchars($_POST['dni'] ?? ''); ?>">
</div>

<div>
<label>CUIL *</label>
<input name="cuil" required value="<?php echo htmlspecialchars($_POST['cuil'] ?? ''); ?>">
</div>

<div>
<label>Fecha Alta *</label>
<input type="date" name="fecha_alta" required value="<?php echo htmlspecialchars($_POST['fecha_alta'] ?? ''); ?>">
</div>

<div>
<label>Fecha Baja</label>
<input type="date" name="fecha_baja" value="<?php echo htmlspecialchars($_POST['fecha_baja'] ?? ''); ?>">
</div>

<div>
<label>Teléfono</label>
<input name="telefono" value="<?php echo htmlspecialchars($_POST['telefono'] ?? ''); ?>">
</div>

<div>
<label>Email</label>
<input type="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
</div>

<div>
<label>Domicilio</label>
<input name="domicilio" value="<?php echo htmlspecialchars($_POST['domicilio'] ?? ''); ?>">
</div>

<div>
<label>Institución *</label>
<select name="institucion_id" required>
<option value="">Seleccione</option>
<?php while($x=$instituciones->fetch_assoc()): ?>
<option value="<?php echo $x['id']; ?>" <?php echo (($_POST['institucion_id'] ?? '') == $x['id']) ? 'selected' : ''; ?>>
    <?php echo htmlspecialchars($x['nombre']); ?>
</option>
<?php endwhile; ?>
</select>
</div>

<div>
<label>Oficina *</label>
<select name="oficina_id" required>
<option value="">Seleccione</option>
<?php while($x=$oficinas->fetch_assoc()): ?>
<option value="<?php echo $x['id']; ?>" <?php echo (($_POST['oficina_id'] ?? '') == $x['id']) ? 'selected' : ''; ?>>
    <?php echo htmlspecialchars($x['nombre']); ?>
</option>
<?php endwhile; ?>
</select>
</div>

<div>
<label>Situación *</label>
<select name="situacion_id" required>
<option value="">Seleccione</option>
<?php while($x=$situaciones->fetch_assoc()): ?>
<option value="<?php echo $x['id']; ?>" <?php echo (($_POST['situacion_id'] ?? '') == $x['id']) ? 'selected' : ''; ?>>
    <?php echo htmlspecialchars($x['nombre']); ?>
</option>
<?php endwhile; ?>
</select>
</div>

<div>
<label>Escalafón *</label>
<select name="escalafon_id" required>
<option value="">Seleccione</option>
<?php while($x=$escalafones->fetch_assoc()): ?>
<option value="<?php echo $x['id']; ?>" <?php echo (($_POST['escalafon_id'] ?? '') == $x['id']) ? 'selected' : ''; ?>>
    <?php echo htmlspecialchars($x['nombre']); ?>
</option>
<?php endwhile; ?>
</select>
</div>

<div>
<label>Categoría *</label>
<select name="categoria_id" required>
<option value="">Seleccione</option>
<?php while($x=$categorias->fetch_assoc()): ?>
<option value="<?php echo $x['id']; ?>" <?php echo (($_POST['categoria_id'] ?? '') == $x['id']) ? 'selected' : ''; ?>>
    <?php echo htmlspecialchars($x['nombre']); ?>
</option>
<?php endwhile; ?>
</select>
</div>

<div style="grid-column:1/-1;">
<label>Observaciones</label>
<textarea name="observaciones"><?php echo htmlspecialchars($_POST['observaciones'] ?? ''); ?></textarea>
</div>

</div>

<br>
<button type="submit" class="btn">Guardar</button>
<a href="empleados.php" class="btn btn-sec">Cancelar</a>
</form>
</div>
</body>
</html>