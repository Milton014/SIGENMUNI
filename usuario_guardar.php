<?php
session_start();

require_once("conexion.php");
require_once("seguridad.php");

verificarSesion();
soloAdmin();

/* FUNCIÓN PARA MOSTRAR ERRORES CON ESTILO */
function mostrarError($mensaje) {

    die("
    <!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <title>Error - SIGENMUNI</title>

        <style>

        body{
            font-family:Arial,sans-serif;
            background:#f4f7fb;
            display:flex;
            justify-content:center;
            align-items:center;
            min-height:100vh;
            margin:0;
        }

        .mensaje{
            background:#fee2e2;
            border:2px solid #dc2626;
            color:#991b1b;
            padding:30px;
            border-radius:16px;
            max-width:500px;
            width:90%;
            text-align:center;
            box-shadow:0 8px 20px rgba(0,0,0,0.12);
        }

        .mensaje h2{
            margin-top:0;
            font-size:30px;
        }

        .mensaje p{
            font-size:18px;
            margin:18px 0;
        }

        .btn{
            display:inline-block;
            margin-top:15px;
            background:#dc2626;
            color:white;
            padding:12px 20px;
            border-radius:10px;
            text-decoration:none;
            font-weight:bold;
        }

        .btn:hover{
            background:#b91c1c;
        }

        </style>
    </head>

    <body>

        <div class='mensaje'>
            <h2>Error</h2>
            <p>$mensaje</p>

            <a href='usuario_nuevo.php' class='btn'>
                Volver
            </a>
        </div>

    </body>
    </html>
    ");
}

$nombre   = trim($_POST['nombre'] ?? '');
$apellido = trim($_POST['apellido'] ?? '');
$dni      = trim($_POST['dni'] ?? '');
$usuario  = trim($_POST['usuario'] ?? '');
$email    = trim($_POST['email'] ?? '');
$clave    = $_POST['clave'] ?? '';
$rol      = $_POST['rol'] ?? '';

/* VALIDAR CAMPOS */
if (
    $nombre === '' ||
    $apellido === '' ||
    $dni === '' ||
    $usuario === '' ||
    $email === '' ||
    $clave === '' ||
    $rol === ''
) {
    mostrarError("Debe completar todos los campos obligatorios.");
}

/* VALIDAR EMAIL */
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    mostrarError("El email ingresado no es válido.");
}

/* VALIDAR DNI SOLO NÚMEROS */
if (!ctype_digit($dni)) {
    mostrarError("El DNI solo puede contener números.");
}

/* VALIDAR LONGITUD DEL DNI */
if (strlen($dni) < 7 || strlen($dni) > 8) {
    mostrarError("El DNI debe tener entre 7 y 8 números.");
}

/* VALIDAR ROL */
if ($rol !== 'ADMIN' && $rol !== 'OPERADOR') {
    mostrarError("Rol inválido.");
}

/* VALIDAR DNI REPETIDO */
$stmt = $conexion->prepare("
    SELECT id
    FROM usuario
    WHERE dni = ?
    LIMIT 1
");

$stmt->bind_param("s", $dni);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    mostrarError("El DNI ya está registrado con otro usuario.");
}

/* VALIDAR EMAIL REPETIDO */
$stmt = $conexion->prepare("
    SELECT id
    FROM usuario
    WHERE email = ?
    LIMIT 1
");

$stmt->bind_param("s", $email);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    mostrarError("El email ya está registrado con otro usuario.");
}

/* VALIDAR USUARIO REPETIDO */
$stmt = $conexion->prepare("
    SELECT id
    FROM usuario
    WHERE nombre_usuario = ?
    LIMIT 1
");

$stmt->bind_param("s", $usuario);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    mostrarError("El nombre de usuario ya está registrado.");
}

/* ENCRIPTAR CONTRASEÑA */
$claveHash = password_hash($clave, PASSWORD_DEFAULT);

/* INSERTAR USUARIO */
$stmt = $conexion->prepare("
    INSERT INTO usuario
    (
        nombre,
        apellido,
        dni,
        nombre_usuario,
        email,
        clave,
        rol,
        activo,
        primer_ingreso
    )
    VALUES
    (?, ?, ?, ?, ?, ?, ?, 1, 0)
");

$stmt->bind_param(
    "sssssss",
    $nombre,
    $apellido,
    $dni,
    $usuario,
    $email,
    $claveHash,
    $rol
);

if ($stmt->execute()) {

    header("Location: usuarios.php");
    exit();

} else {

    mostrarError("Error al guardar el usuario: " . $stmt->error);
}
?>