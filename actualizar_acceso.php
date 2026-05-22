<?php
require_once("conexion.php");

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
            max-width:520px;
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
            <a href='recuperar.php' class='btn'>Volver</a>
        </div>
    </body>
    </html>
    ");
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: recuperar.php");
    exit();
}

$correo           = trim($_POST["correo"] ?? "");
$codigo           = trim($_POST["codigo"] ?? "");
$nuevo_usuario    = trim($_POST["nuevo_usuario"] ?? "");
$nueva_contra     = trim($_POST["nueva_contra"] ?? "");
$confirmar_contra = trim($_POST["confirmar_contra"] ?? "");

if (
    $correo === "" ||
    $codigo === "" ||
    $nuevo_usuario === "" ||
    $nueva_contra === "" ||
    $confirmar_contra === ""
) {
    mostrarError("Debe completar todos los campos.");
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    mostrarError("El correo ingresado no es válido.");
}

/* Validar código numérico de 6 dígitos */
if (!ctype_digit($codigo) || strlen($codigo) !== 6) {
    mostrarError("El código debe contener exactamente 6 números.");
}

if ($nueva_contra !== $confirmar_contra) {
    mostrarError("Las contraseñas no coinciden.");
}

/* Validación mínima de contraseña */
if (strlen($nueva_contra) < 6) {
    mostrarError("La nueva contraseña debe tener al menos 6 caracteres.");
}

/* Buscar SOLO ADMIN activo */
$stmt = $conexion->prepare("
    SELECT id, nombre_usuario, rol, codigo_recuperacion, codigo_expira
    FROM usuario
    WHERE email = ?
      AND rol = 'ADMIN'
      AND activo = 1
    LIMIT 1
");

$stmt->bind_param("s", $correo);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    mostrarError("No se encontró un ADMIN activo para actualizar el acceso. La recuperación no está disponible para usuarios OPERADOR.");
}

$usuario = $resultado->fetch_assoc();
$idUsuario = (int)$usuario["id"];

if (empty($usuario["codigo_recuperacion"]) || empty($usuario["codigo_expira"])) {
    mostrarError("No hay un código de recuperación activo. Solicitá uno nuevo.");
}

if ($usuario["codigo_recuperacion"] !== $codigo) {
    mostrarError("Código incorrecto.");
}

$ahora = date("Y-m-d H:i:s");

if ($usuario["codigo_expira"] < $ahora) {
    mostrarError("El código venció. Solicitá uno nuevo.");
}

/* Validar que el nuevo nombre de usuario no exista en otro usuario */
$stmt = $conexion->prepare("
    SELECT id
    FROM usuario
    WHERE nombre_usuario = ?
      AND id <> ?
    LIMIT 1
");

$stmt->bind_param("si", $nuevo_usuario, $idUsuario);
$stmt->execute();
$resUsuario = $stmt->get_result();

if ($resUsuario->num_rows > 0) {
    mostrarError("El nombre de usuario ya está registrado.");
}

/* Actualizar acceso */
$claveHash = password_hash($nueva_contra, PASSWORD_DEFAULT);

$update = $conexion->prepare("
    UPDATE usuario
    SET 
        nombre_usuario = ?,
        clave = ?,
        codigo_recuperacion = NULL,
        codigo_expira = NULL,
        primer_ingreso = 0
    WHERE id = ?
      AND rol = 'ADMIN'
");

$update->bind_param("ssi", $nuevo_usuario, $claveHash, $idUsuario);

if ($update->execute()) {
    echo "
    <!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <title>Acceso actualizado</title>

        <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f7fb;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
            text-align: center;
            max-width: 420px;
            width: 90%;
        }

        h2 {
            color: #0f766e;
            margin-top: 0;
        }

        p {
            font-size: 16px;
            color: #374151;
        }

        a {
            display: inline-block;
            margin-top: 15px;
            background: #0f766e;
            color: white;
            padding: 12px 18px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            background: #115e59;
        }
        </style>
    </head>

    <body>
        <div class='card'>
            <h2>Datos actualizados correctamente</h2>
            <p>Ya podés iniciar sesión con tu nuevo usuario y contraseña.</p>
            <a href='login.php'>Volver al login</a>
        </div>
    </body>
    </html>
    ";
} else {
    mostrarError("Error al actualizar los datos.");
}
?>