<?php
require_once("conexion.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

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

$correo = trim($_POST["correo"] ?? "");
$dni    = trim($_POST["dni"] ?? "");

if ($correo === "" || $dni === "") {
    mostrarError("Debe completar correo y DNI.");
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    mostrarError("El correo ingresado no es válido.");
}

/* VALIDAR DNI SOLO NÚMEROS */
if (!ctype_digit($dni)) {
    mostrarError("El DNI solo puede contener números.");
}

/* VALIDAR LONGITUD DEL DNI */
if (strlen($dni) < 7 || strlen($dni) > 8) {
    mostrarError("El DNI debe tener entre 7 y 8 números.");
}

/* Buscar SOLO usuario ADMIN activo */
$stmt = $conexion->prepare("
    SELECT id, nombre, apellido, email
    FROM usuario
    WHERE email = ?
      AND dni = ?
      AND rol = 'ADMIN'
      AND activo = 1
    LIMIT 1
");

$stmt->bind_param("ss", $correo, $dni);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    mostrarError("No se encontró un ADMIN activo con ese correo y DNI. La recuperación solo está disponible para administradores.");
}

$usuario = $resultado->fetch_assoc();
$idUsuario = (int)$usuario["id"];

/* Generar código */
$codigo = random_int(100000, 999999);
$expira = date("Y-m-d H:i:s", strtotime("+10 minutes"));

/* Guardar código */
$update = $conexion->prepare("
    UPDATE usuario
    SET codigo_recuperacion = ?, codigo_expira = ?
    WHERE id = ?
");

$update->bind_param("ssi", $codigo, $expira, $idUsuario);

if (!$update->execute()) {
    mostrarError("Error al generar el código de recuperación.");
}

/* Enviar correo */
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;

    // CAMBIAR ESTOS DATOS POR TU GMAIL Y TU CONTRASEÑA DE APLICACIÓN
    $mail->Username   = 'chavezmilton082@gmail.com';
    $mail->Password   = 'hemgotgudzvcepch';

    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom('chavezmilton082@gmail.com', 'SIGENMUNI');
    $mail->addAddress($correo, $usuario["nombre"] . " " . $usuario["apellido"]);

    $mail->isHTML(true);
    $mail->CharSet = 'UTF-8';

    $mail->Subject = 'Código de recuperación - SIGENMUNI';

    $mail->Body = "
        <h2>SIGENMUNI</h2>
        <p>Hola <b>{$usuario['nombre']} {$usuario['apellido']}</b>,</p>
        <p>Tu código de recuperación es:</p>
        <h1 style='color:#0f766e;'>$codigo</h1>
        <p>Este código vence en 10 minutos.</p>
        <p>Si no solicitaste esta recuperación, ignorá este mensaje.</p>
    ";

    $mail->send();

    header("Location: verificar_codigo.php?correo=" . urlencode($correo));
    exit();

} catch (Exception $e) {
    mostrarError("No se pudo enviar el correo. Detalle: " . $mail->ErrorInfo);
}
?>