<?php
session_start();

require_once("conexion.php");
require_once("seguridad.php");

verificarSesion();
verificarPermisoModulo("empleados.php");

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
        $fecha_baja = null;

        $telefono = trim($_POST['telefono'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $domicilio = trim($_POST['domicilio'] ?? '');
        $observaciones = trim($_POST['observaciones'] ?? '');

        $telefono = ($telefono === '') ? null : $telefono;
        $domicilio = ($domicilio === '') ? null : $domicilio;
        $observaciones = ($observaciones === '') ? null : $observaciones;

        if (
            $nro_legajo === '' || $apellido === '' || $nombre === '' ||
            $dni === '' || $cuil === '' || $fecha_alta === '' ||
            $email === '' ||
            $institucion_id <= 0 || $oficina_id <= 0 || $situacion_id <= 0 ||
            $escalafon_id <= 0 || $categoria_id <= 0
        ) {
            throw new Exception("Complete todos los campos obligatorios.");
        }

        if (!ctype_digit($nro_legajo)) {
            throw new Exception("El número de legajo debe contener solo números.");
        }

        if (!ctype_digit($dni)) {
            throw new Exception("El DNI debe contener solo números.");
        }

        if (!ctype_digit($cuil)) {
            throw new Exception("El CUIL debe contener solo números.");
        }

        if (strlen($dni) < 7 || strlen($dni) > 8) {
            throw new Exception("El DNI debe tener 7 u 8 dígitos.");
        }

        if (strlen($cuil) != 11) {
            throw new Exception("El CUIL debe tener exactamente 11 dígitos.");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("El email ingresado no tiene un formato válido.");
        }

        // VALIDAR LEGAJO

        $stmtVal = $conexion->prepare("
            SELECT id 
            FROM empleado 
            WHERE nro_legajo = ? 
            LIMIT 1
        ");

        $stmtVal->bind_param("s", $nro_legajo);
        $stmtVal->execute();

        $resVal = $stmtVal->get_result();

        if ($resVal->num_rows > 0) {
            throw new Exception("Ya existe un empleado con ese número de legajo.");
        }

        // VALIDAR DNI

        $stmtVal = $conexion->prepare("
            SELECT id 
            FROM empleado 
            WHERE dni = ? 
            LIMIT 1
        ");

        $stmtVal->bind_param("s", $dni);
        $stmtVal->execute();

        $resVal = $stmtVal->get_result();

        if ($resVal->num_rows > 0) {
            throw new Exception("Ya existe un empleado con ese DNI.");
        }

        // VALIDAR CUIL

        $stmtVal = $conexion->prepare("
            SELECT id 
            FROM empleado 
            WHERE cuil = ? 
            LIMIT 1
        ");

        $stmtVal->bind_param("s", $cuil);
        $stmtVal->execute();

        $resVal = $stmtVal->get_result();

        if ($resVal->num_rows > 0) {
            throw new Exception("Ya existe un empleado con ese CUIL.");
        }

        // VALIDAR EMAIL

        $stmtVal = $conexion->prepare("
            SELECT id 
            FROM empleado 
            WHERE email = ? 
            LIMIT 1
        ");

        $stmtVal->bind_param("s", $email);
        $stmtVal->execute();

        $resVal = $stmtVal->get_result();

        if ($resVal->num_rows > 0) {
            throw new Exception("Ya existe un empleado con ese email.");
        }

        // INSERTAR

        $stmt = $conexion->prepare("
            INSERT INTO empleado (
                institucion_id,
                oficina_id,
                situacion_id,
                escalafon_id,
                categoria_id,
                nro_legajo,
                apellido,
                nombre,
                dni,
                cuil,
                fecha_alta,
                fecha_baja,
                telefono,
                email,
                domicilio,
                observaciones,
                activo
            )
            VALUES (
                ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?, ?, 1
            )
        ");

        if (!$stmt) {
            throw new Exception("No se pudo preparar la consulta.");
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
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Nuevo Empleado - SIGENMUNI</title>

<style>

*{
    box-sizing:border-box;
}

body{
    font-family:Arial,sans-serif;
    background:#f4f7fb;
    margin:0;
    color:#1f2937;
}

/* =========================================
   HEADER
========================================= */

.header{
    background:linear-gradient(135deg,#0f766e,#14b8a6);
    color:white;
    padding:22px 30px;
    box-shadow:0 4px 14px rgba(0,0,0,.10);
}

.header h1{
    margin:0;
    font-size:30px;
}

.header p{
    margin-top:6px;
    font-size:14px;
}

/* =========================================
   CONTENEDOR
========================================= */

.contenedor{
    width:95%;
    max-width:1100px;
    margin:30px auto;
}

.panel{
    background:white;
    padding:28px;
    border-radius:18px;
    box-shadow:0 8px 20px rgba(0,0,0,.08);
}

h2{
    margin-top:0;
    margin-bottom:22px;
    color:#0f766e;
}

/* =========================================
   GRID
========================================= */

.grid{
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:18px;
}

/* =========================================
   CAMPOS
========================================= */

.campo{
    display:flex;
    flex-direction:column;
}

label{
    margin-bottom:6px;
    font-weight:bold;
    font-size:14px;
}

input,
select,
textarea{
    width:100%;
    padding:12px;
    border:1px solid #cbd5e1;
    border-radius:10px;
    outline:none;
    font-size:14px;
    transition:0.2s;
    background:white;
}

input:focus,
select:focus,
textarea:focus{
    border-color:#14b8a6;
    box-shadow:0 0 0 3px rgba(20,184,166,.15);
}

textarea{
    min-height:110px;
    resize:vertical;
}

/* =========================================
   BOTONES
========================================= */

.acciones{
    margin-top:25px;
    display:flex;
    gap:10px;
    flex-wrap:wrap;
}

.btn{
    background:#0f766e;
    color:white;
    padding:11px 16px;
    border:none;
    border-radius:10px;
    text-decoration:none;
    cursor:pointer;
    display:inline-block;
    font-weight:bold;
    font-size:14px;
    transition:0.2s;
}

.btn:hover{
    opacity:0.92;
    transform:translateY(-1px);
}

.btn-sec{
    background:#1f2937;
}

/* =========================================
   MENSAJES
========================================= */

.mensaje{
    padding:14px;
    border-radius:10px;
    margin-bottom:18px;
    font-weight:bold;
}

.error{
    background:#fee2e2;
    color:#991b1b;
    border:1px solid #fecaca;
}

.ok{
    background:#dcfce7;
    color:#166534;
    border:1px solid #86efac;
}

.input-error{
    border-color:#dc2626 !important;
    background:#fff1f2;
    box-shadow:0 0 0 3px rgba(220,38,38,.12);
}

/* =========================================
   TABLET
========================================= */

@media (max-width: 992px){

    .contenedor{
        width:96%;
    }

    .grid{
        grid-template-columns:1fr;
    }
}

/* =========================================
   CELULAR
========================================= */

@media (max-width: 768px){

    .header{
        padding:20px;
        text-align:center;
    }

    .header h1{
        font-size:24px;
    }

    .header p{
        font-size:13px;
    }

    .contenedor{
        width:94%;
        margin:20px auto;
    }

    .panel{
        padding:20px;
        border-radius:16px;
    }

    h2{
        font-size:24px;
        text-align:center;
    }

    .grid{
        grid-template-columns:1fr;
        gap:16px;
    }

    .acciones{
        flex-direction:column;
        align-items:stretch;
    }

    .btn{
        width:100%;
        text-align:center;
    }

    input,
    select,
    textarea{
        font-size:14px;
    }
}

/* =========================================
   CELULARES PEQUEÑOS
========================================= */

@media (max-width: 480px){

    .header h1{
        font-size:22px;
    }

    h2{
        font-size:22px;
    }

    .panel{
        padding:18px;
    }

    .btn{
        font-size:13px;
        padding:10px;
    }

    label{
        font-size:13px;
    }
}

</style>
</head>

<body>

<div class="header">

    <h1>SIGENMUNI</h1>

    <p>
        Alta de Empleado Municipal
    </p>

</div>

<div class="contenedor">

    <div class="panel">

        <h2>Nuevo Empleado</h2>

        <div id="alertaEmpleado"
             class="mensaje error"
             style="display:none;">
        </div>

        <?php if($mensaje): ?>

            <div class="mensaje <?php echo $tipo_mensaje === 'ok' ? 'ok' : 'error'; ?>">

                <?php echo htmlspecialchars($mensaje); ?>

            </div>

        <?php endif; ?>

        <form method="POST" id="formEmpleado" novalidate>

            <div class="grid">

                <div class="campo">
                    <label>Legajo *</label>

                    <input
                        name="nro_legajo"
                        id="nro_legajo"
                        value="<?php echo htmlspecialchars($_POST['nro_legajo'] ?? ''); ?>"
                    >
                </div>

                <div class="campo">
                    <label>Apellido *</label>

                    <input
                        name="apellido"
                        id="apellido"
                        value="<?php echo htmlspecialchars($_POST['apellido'] ?? ''); ?>"
                    >
                </div>

                <div class="campo">
                    <label>Nombre *</label>

                    <input
                        name="nombre"
                        id="nombre"
                        value="<?php echo htmlspecialchars($_POST['nombre'] ?? ''); ?>"
                    >
                </div>

                <div class="campo">
                    <label>DNI *</label>

                    <input
                        name="dni"
                        id="dni"
                        value="<?php echo htmlspecialchars($_POST['dni'] ?? ''); ?>"
                    >
                </div>

                <div class="campo">
                    <label>CUIL *</label>

                    <input
                        name="cuil"
                        id="cuil"
                        value="<?php echo htmlspecialchars($_POST['cuil'] ?? ''); ?>"
                    >
                </div>

                <div class="campo">
                    <label>Fecha Alta *</label>

                    <input
                        type="date"
                        name="fecha_alta"
                        id="fecha_alta"
                        value="<?php echo htmlspecialchars($_POST['fecha_alta'] ?? ''); ?>"
                    >
                </div>

                <div class="campo">
                    <label>Teléfono</label>

                    <input
                        name="telefono"
                        id="telefono"
                        value="<?php echo htmlspecialchars($_POST['telefono'] ?? ''); ?>"
                    >
                </div>

                <div class="campo">
                    <label>Email *</label>

                    <input
                        type="email"
                        name="email"
                        id="email"
                        value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                    >
                </div>

                <div class="campo">
                    <label>Domicilio</label>

                    <input
                        name="domicilio"
                        id="domicilio"
                        value="<?php echo htmlspecialchars($_POST['domicilio'] ?? ''); ?>"
                    >
                </div>

                <div class="campo">
                    <label>Institución *</label>

                    <select name="institucion_id" id="institucion_id">

                        <option value="">
                            Seleccione
                        </option>

                        <?php while($x=$instituciones->fetch_assoc()): ?>

                            <option
                                value="<?php echo $x['id']; ?>"
                                <?php echo (($_POST['institucion_id'] ?? '') == $x['id']) ? 'selected' : ''; ?>
                            >

                                <?php echo htmlspecialchars($x['nombre']); ?>

                            </option>

                        <?php endwhile; ?>

                    </select>
                </div>

                <div class="campo">
                    <label>Oficina *</label>

                    <select name="oficina_id" id="oficina_id">

                        <option value="">
                            Seleccione
                        </option>

                        <?php while($x=$oficinas->fetch_assoc()): ?>

                            <option
                                value="<?php echo $x['id']; ?>"
                                <?php echo (($_POST['oficina_id'] ?? '') == $x['id']) ? 'selected' : ''; ?>
                            >

                                <?php echo htmlspecialchars($x['nombre']); ?>

                            </option>

                        <?php endwhile; ?>

                    </select>
                </div>

                <div class="campo">
                    <label>Situación *</label>

                    <select name="situacion_id" id="situacion_id">

                        <option value="">
                            Seleccione
                        </option>

                        <?php while($x=$situaciones->fetch_assoc()): ?>

                            <option
                                value="<?php echo $x['id']; ?>"
                                <?php echo (($_POST['situacion_id'] ?? '') == $x['id']) ? 'selected' : ''; ?>
                            >

                                <?php echo htmlspecialchars($x['nombre']); ?>

                            </option>

                        <?php endwhile; ?>

                    </select>
                </div>

                <div class="campo">
                    <label>Escalafón *</label>

                    <select name="escalafon_id" id="escalafon_id">

                        <option value="">
                            Seleccione
                        </option>

                        <?php while($x=$escalafones->fetch_assoc()): ?>

                            <option
                                value="<?php echo $x['id']; ?>"
                                <?php echo (($_POST['escalafon_id'] ?? '') == $x['id']) ? 'selected' : ''; ?>
                            >

                                <?php echo htmlspecialchars($x['nombre']); ?>

                            </option>

                        <?php endwhile; ?>

                    </select>
                </div>

                <div class="campo">
                    <label>Categoría *</label>

                    <select name="categoria_id" id="categoria_id">

                        <option value="">
                            Seleccione
                        </option>

                        <?php while($x=$categorias->fetch_assoc()): ?>

                            <option
                                value="<?php echo $x['id']; ?>"
                                <?php echo (($_POST['categoria_id'] ?? '') == $x['id']) ? 'selected' : ''; ?>
                            >

                                <?php echo htmlspecialchars($x['nombre']); ?>

                            </option>

                        <?php endwhile; ?>

                    </select>
                </div>

                <div class="campo" style="grid-column:1/-1;">

                    <label>Observaciones</label>

                    <textarea
                        name="observaciones"
                        id="observaciones"
                    ><?php echo htmlspecialchars($_POST['observaciones'] ?? ''); ?></textarea>

                </div>

            </div>

            <div class="acciones">

                <button type="submit" class="btn">
                    Guardar Empleado
                </button>

                <a href="empleados.php" class="btn btn-sec">
                    Cancelar
                </a>

            </div>

        </form>

    </div>

</div>

<script>

document.getElementById("formEmpleado").addEventListener("submit", function(e){

    const alerta = document.getElementById("alertaEmpleado");

    const campos = [
        "nro_legajo",
        "apellido",
        "nombre",
        "dni",
        "cuil",
        "fecha_alta",
        "email",
        "institucion_id",
        "oficina_id",
        "situacion_id",
        "escalafon_id",
        "categoria_id"
    ];

    campos.forEach(function(id){
        document.getElementById(id).classList.remove("input-error");
    });

    alerta.style.display = "none";
    alerta.innerHTML = "";

    function mostrarError(mensaje, id){

        e.preventDefault();

        const campo = document.getElementById(id);

        alerta.innerHTML = mensaje;
        alerta.style.display = "block";

        campo.classList.add("input-error");
        campo.focus();

        window.scrollTo({
            top:0,
            behavior:"smooth"
        });
    }

    const nroLegajo = document.getElementById("nro_legajo").value.trim();
    const apellido = document.getElementById("apellido").value.trim();
    const nombre = document.getElementById("nombre").value.trim();
    const dni = document.getElementById("dni").value.trim();
    const cuil = document.getElementById("cuil").value.trim();
    const fechaAlta = document.getElementById("fecha_alta").value.trim();
    const email = document.getElementById("email").value.trim();

    if(nroLegajo === ""){
        mostrarError("Debe ingresar el número de legajo.","nro_legajo");
        return;
    }

    if(!/^[0-9]+$/.test(nroLegajo)){
        mostrarError("El número de legajo debe contener solo números.","nro_legajo");
        return;
    }

    if(apellido === ""){
        mostrarError("Debe ingresar el apellido.","apellido");
        return;
    }

    if(nombre === ""){
        mostrarError("Debe ingresar el nombre.","nombre");
        return;
    }

    if(dni === ""){
        mostrarError("Debe ingresar el DNI.","dni");
        return;
    }

    if(!/^[0-9]{7,8}$/.test(dni)){
        mostrarError("El DNI debe tener entre 7 y 8 números.","dni");
        return;
    }

    if(cuil === ""){
        mostrarError("Debe ingresar el CUIL.","cuil");
        return;
    }

    if(!/^[0-9]{11}$/.test(cuil)){
        mostrarError("El CUIL debe tener exactamente 11 números.","cuil");
        return;
    }

    if(fechaAlta === ""){
        mostrarError("Debe seleccionar la fecha de alta.","fecha_alta");
        return;
    }

    if(email === ""){
        mostrarError("Debe ingresar el email.","email");
        return;
    }

    const formatoEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if(!formatoEmail.test(email)){
        mostrarError("Debe ingresar un email válido.","email");
        return;
    }

    if(document.getElementById("institucion_id").value === ""){
        mostrarError("Debe seleccionar una institución.","institucion_id");
        return;
    }

    if(document.getElementById("oficina_id").value === ""){
        mostrarError("Debe seleccionar una oficina.","oficina_id");
        return;
    }

    if(document.getElementById("situacion_id").value === ""){
        mostrarError("Debe seleccionar una situación.","situacion_id");
        return;
    }

    if(document.getElementById("escalafon_id").value === ""){
        mostrarError("Debe seleccionar un escalafón.","escalafon_id");
        return;
    }

    if(document.getElementById("categoria_id").value === ""){
        mostrarError("Debe seleccionar una categoría.","categoria_id");
        return;
    }

});

</script>

</body>
</html>