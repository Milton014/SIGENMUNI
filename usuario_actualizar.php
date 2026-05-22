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

            <a href='usuarios.php' class='btn'>
                Volver
            </a>
        </div>
    </body>
    </html>
    ");
}

$id             = (int)($_POST['id'] ?? 0);
$nombre         = trim($_POST['nombre'] ?? '');
$apellido       = trim($_POST['apellido'] ?? '');
$dni            = trim($_POST['dni'] ?? '');
$email          = trim($_POST['email'] ?? '');
$nombre_usuario = trim($_POST['nombre_usuario'] ?? '');
$rol            = $_POST['rol'] ?? 'OPERADOR';
$clave          = trim($_POST['clave'] ?? '');

if (
    $id <= 0 ||
    $nombre === '' ||
    $apellido === '' ||
    $dni === '' ||
    $email === '' ||
    $nombre_usuario === ''
) {
    mostrarError("Faltan datos obligatorios.");
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
    mostrarError("Rol no válido.");
}

/* Buscar usuario actual */
$stmt = $conexion->prepare("
    SELECT id, rol
    FROM usuario
    WHERE id = ?
    LIMIT 1
");

$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();
$usuarioActual = $resultado->fetch_assoc();

if (!$usuarioActual) {
    mostrarError("El usuario no existe.");
}

/* Evitar que el único ADMIN sea cambiado a OPERADOR */
if ($usuarioActual['rol'] === 'ADMIN' && $rol === 'OPERADOR') {

    $stmt = $conexion->prepare("
        SELECT COUNT(*) AS total_admin
        FROM usuario
        WHERE rol = 'ADMIN'
          AND activo = 1
    ");

    $stmt->execute();

    $resAdmin = $stmt->get_result();
    $dataAdmin = $resAdmin->fetch_assoc();

    if ((int)$dataAdmin['total_admin'] <= 1) {
        mostrarError("No podés cambiar el rol del único ADMIN activo.");
    }
}

/* Validar DNI repetido en otro usuario */
$stmt = $conexion->prepare("
    SELECT id
    FROM usuario
    WHERE dni = ?
      AND id <> ?
    LIMIT 1
");

$stmt->bind_param("si", $dni, $id);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    mostrarError("El DNI ya está registrado con otro usuario.");
}

/* Validar email repetido en otro usuario */
$stmt = $conexion->prepare("
    SELECT id
    FROM usuario
    WHERE email = ?
      AND id <> ?
    LIMIT 1
");

$stmt->bind_param("si", $email, $id);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    mostrarError("El email ya está registrado con otro usuario.");
}

/* Validar nombre de usuario repetido en otro usuario */
$stmt = $conexion->prepare("
    SELECT id
    FROM usuario
    WHERE nombre_usuario = ?
      AND id <> ?
    LIMIT 1
");

$stmt->bind_param("si", $nombre_usuario, $id);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    mostrarError("El nombre de usuario ya está registrado con otro usuario.");
}

/* Actualizar con o sin cambio de contraseña */
if (!empty($clave)) {

    $claveHash = password_hash($clave, PASSWORD_DEFAULT);

    $stmt = $conexion->prepare("
        UPDATE usuario
        SET
            nombre = ?,
            apellido = ?,
            dni = ?,
            email = ?,
            nombre_usuario = ?,
            rol = ?,
            clave = ?
        WHERE id = ?
    ");

    $stmt->bind_param(
        "sssssssi",
        $nombre,
        $apellido,
        $dni,
        $email,
        $nombre_usuario,
        $rol,
        $claveHash,
        $id
    );

} else {

    $stmt = $conexion->prepare("
        UPDATE usuario
        SET
            nombre = ?,
            apellido = ?,
            dni = ?,
            email = ?,
            nombre_usuario = ?,
            rol = ?
        WHERE id = ?
    ");

    $stmt->bind_param(
        "ssssssi",
        $nombre,
        $apellido,
        $dni,
        $email,
        $nombre_usuario,
        $rol,
        $id
    );
}

if ($stmt->execute()) {

    header("Location: usuarios.php");
    exit();

} else {

    mostrarError("Error al actualizar el usuario: " . $stmt->error);
}
?>