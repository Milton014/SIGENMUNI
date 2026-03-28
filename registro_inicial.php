<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once("conexion.php");

// Verificar si ya existe un usuario
$sql = "SELECT COUNT(*) AS total FROM usuario";
$resultado = $conexion->query($sql);

if (!$resultado) {
    die("Error al consultar usuarios: " . $conexion->error);
}

$fila = $resultado->fetch_assoc();

if ((int)$fila['total'] > 0) {
    header("Location: login.php");
    exit();
}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST["nombre"] ?? "");
    $apellido = trim($_POST["apellido"] ?? "");
    $nombre_usuario = trim($_POST["nombre_usuario"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $clave = trim($_POST["clave"] ?? "");
    $confirmar_clave = trim($_POST["confirmar_clave"] ?? "");

    if (
        empty($nombre) ||
        empty($apellido) ||
        empty($nombre_usuario) ||
        empty($clave) ||
        empty($confirmar_clave)
    ) {
        $mensaje = "Debe completar todos los campos obligatorios.";
    } elseif ($clave !== $confirmar_clave) {
        $mensaje = "Las contraseñas no coinciden.";
    } else {
        $claveHash = password_hash($clave, PASSWORD_DEFAULT);

        $stmt = $conexion->prepare("
            INSERT INTO usuario (nombre, apellido, nombre_usuario, email, clave, rol, activo, primer_ingreso)
            VALUES (?, ?, ?, ?, ?, 'ADMIN', 1, 0)
        ");

        if (!$stmt) {
            die("Error en prepare: " . $conexion->error);
        }

        $stmt->bind_param("sssss", $nombre, $apellido, $nombre_usuario, $email, $claveHash);

        if ($stmt->execute()) {
            header("Location: login.php");
            exit();
        } else {
            $mensaje = "Error al registrar el usuario: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro Inicial - SIGENMUNI</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .contenedor {
            width: 400px;
            text-align: center;
        }

        h1 {
            font-size: 34px;
            margin-bottom: 0;
        }

        .subtitulo {
            color: #555;
            margin-bottom: 30px;
        }

        .titulo {
            font-size: 18px;
            text-align: left;
            margin-bottom: 10px;
            font-weight: bold;
        }

        label {
            text-align: left;
            display: block;
            margin: 8px 0 4px 0;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #999;
            border-radius: 6px;
            margin-bottom: 12px;
            background: #f5f5f5;
            font-size: 14px;
            box-sizing: border-box;
        }

        .btn {
            width: 100%;
            padding: 14px;
            background: green;
            border-radius: 40px;
            border: none;
            color: white;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
        }

        .btn:hover {
            background: #0b6b0b;
        }

        .error {
            color: red;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<div class="contenedor">
    <h1>SIGENMUNI</h1>
    <p class="subtitulo">Registro del primer usuario</p>

    <?php if (!empty($mensaje)) { ?>
        <p class="error"><?php echo htmlspecialchars($mensaje); ?></p>
    <?php } ?>

    <div class="titulo">Crear usuario administrador</div>

    <form method="POST">
        <label>Nombre</label>
        <input type="text" name="nombre" required>

        <label>Apellido</label>
        <input type="text" name="apellido" required>

        <label>Nombre de usuario</label>
        <input type="text" name="nombre_usuario" required>

        <label>Email</label>
        <input type="email" name="email">

        <label>Contraseña</label>
        <input type="password" name="clave" required>

        <label>Confirmar contraseña</label>
        <input type="password" name="confirmar_clave" required>

        <button type="submit" class="btn">REGISTRAR USUARIO</button>
    </form>
</div>

</body>
</html>