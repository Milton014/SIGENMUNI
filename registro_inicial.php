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
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Registro Inicial - SIGENMUNI</title>

<style>
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    min-height: 100vh;
    font-family: Arial, sans-serif;
    background: linear-gradient(135deg, #0f766e 0%, #115e59 45%, #e6fffb 100%);
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
}

.contenedor {
    width: 100%;
    max-width: 440px;
}

.card {
    background: rgba(255, 255, 255, 0.97);
    border-radius: 18px;
    padding: 32px 28px;
    box-shadow: 0 20px 45px rgba(0, 0, 0, 0.18);
}

.logo {
    text-align: center;
    margin-bottom: 24px;
}

.logo-img {
    width: 80px;
    margin-bottom: 10px;
}

.logo h1 {
    margin: 0;
    font-size: 34px;
    color: #0f766e;
}

.subtitulo {
    margin: 8px 0 0;
    color: #6b7280;
    font-size: 15px;
}

.titulo {
    font-size: 20px;
    font-weight: bold;
    color: #111827;
    margin-bottom: 16px;
}

.mensaje-error {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fecaca;
    padding: 12px 14px;
    border-radius: 10px;
    margin-bottom: 16px;
    font-size: 14px;
    font-weight: bold;
}

.grupo {
    margin-bottom: 14px;
}

label {
    display: block;
    margin-bottom: 6px;
    font-size: 14px;
    font-weight: bold;
    color: #374151;
}

input {
    width: 100%;
    padding: 13px 14px;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    background: #f8fafc;
    font-size: 14px;
    outline: none;
    transition: 0.2s ease;
}

input:focus {
    border-color: #14b8a6;
    background: #ffffff;
    box-shadow: 0 0 0 3px rgba(20, 184, 166, 0.15);
}

input.input-error {
    border-color: #dc2626;
    background: #fff1f2;
    box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.12);
}

.btn {
    width: 100%;
    padding: 14px;
    background: #0f766e;
    border-radius: 12px;
    border: none;
    color: white;
    font-size: 15px;
    font-weight: bold;
    cursor: pointer;
    transition: 0.25s ease;
    margin-top: 8px;
}

.btn:hover {
    background: #115e59;
    transform: translateY(-1px);
}

.volver {
    margin-top: 16px;
    text-align: center;
}

.volver a {
    text-decoration: none;
    color: #0f766e;
    font-weight: bold;
}

.volver a:hover {
    text-decoration: underline;
}

.pie {
    text-align: center;
    margin-top: 15px;
    color: rgba(255,255,255,0.9);
    font-size: 13px;
}
</style>
</head>

<body>

<div class="contenedor">

    <div class="card">

        <div class="logo">
            <img src="img/escudo.jpg" class="logo-img" alt="Escudo">
            <h1>SIGENMUNI</h1>
            <p class="subtitulo">Registro del primer usuario</p>
        </div>

        <div class="titulo">Crear usuario administrador</div>

        <div id="alertaRegistro" class="mensaje-error" style="display:none;"></div>

        <?php if (!empty($mensaje)) { ?>
            <div class="mensaje-error"><?php echo htmlspecialchars($mensaje); ?></div>
        <?php } ?>

        <form method="POST" id="formRegistro" novalidate>

            <div class="grupo">
                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" id="nombre">
            </div>

            <div class="grupo">
                <label for="apellido">Apellido</label>
                <input type="text" name="apellido" id="apellido">
            </div>

            <div class="grupo">
                <label for="nombre_usuario">Nombre de usuario</label>
                <input type="text" name="nombre_usuario" id="nombre_usuario" autocomplete="username">
            </div>

            <div class="grupo">
                <label for="email">Email</label>
                <input type="email" name="email" id="email">
            </div>

            <div class="grupo">
                <label for="clave">Contraseña</label>
                <input type="password" name="clave" id="clave" autocomplete="new-password">
            </div>

            <div class="grupo">
                <label for="confirmar_clave">Confirmar contraseña</label>
                <input type="password" name="confirmar_clave" id="confirmar_clave" autocomplete="new-password">
            </div>

            <button type="submit" class="btn">REGISTRAR USUARIO</button>

        </form>

        <div class="volver">
            <a href="login.php">← Volver al login</a>
        </div>

    </div>

    <div class="pie">
        Municipalidad de Fortín Lugones
    </div>

</div>

<script>
document.getElementById("formRegistro").addEventListener("submit", function(e) {
    const nombre = document.getElementById("nombre");
    const apellido = document.getElementById("apellido");
    const nombreUsuario = document.getElementById("nombre_usuario");
    const email = document.getElementById("email");
    const clave = document.getElementById("clave");
    const confirmarClave = document.getElementById("confirmar_clave");
    const alerta = document.getElementById("alertaRegistro");

    const campos = [nombre, apellido, nombreUsuario, email, clave, confirmarClave];

    campos.forEach(function(campo) {
        campo.classList.remove("input-error");
    });

    alerta.style.display = "none";
    alerta.innerHTML = "";

    function mostrarError(mensaje, campo) {
        e.preventDefault();
        alerta.innerHTML = mensaje;
        alerta.style.display = "block";
        campo.classList.add("input-error");
        campo.focus();
    }

    if (nombre.value.trim() === "") {
        mostrarError("Debe ingresar el nombre.", nombre);
        return;
    }

    if (apellido.value.trim() === "") {
        mostrarError("Debe ingresar el apellido.", apellido);
        return;
    }

    if (nombreUsuario.value.trim() === "") {
        mostrarError("Debe ingresar el nombre de usuario.", nombreUsuario);
        return;
    }

    if (email.value.trim() !== "") {
        const formatoCorreo = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!formatoCorreo.test(email.value.trim())) {
            mostrarError("Debe ingresar un email válido.", email);
            return;
        }
    }

    if (clave.value.trim() === "") {
        mostrarError("Debe ingresar una contraseña.", clave);
        return;
    }

    if (confirmarClave.value.trim() === "") {
        mostrarError("Debe confirmar la contraseña.", confirmarClave);
        return;
    }

    if (clave.value.trim().length < 6) {
        mostrarError("La contraseña debe tener al menos 6 caracteres.", clave);
        return;
    }

    if (clave.value.trim() !== confirmarClave.value.trim()) {
        mostrarError("Las contraseñas no coinciden.", confirmarClave);
        return;
    }
});
</script>

</body>
</html>