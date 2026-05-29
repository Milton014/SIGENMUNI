<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

require_once("conexion.php");

if (isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

$mensaje = "";

if (isset($_GET['error']) && $_GET['error'] === 'sin_permiso') {
    $mensaje = "No tiene permisos para acceder a ese módulo.";
}

$existenUsuarios = false;

$sqlUsuarios = "SELECT COUNT(*) AS total FROM usuario";
$resUsuarios = $conexion->query($sqlUsuarios);

if (!$resUsuarios) {
    die("Error al consultar la tabla usuario: " . $conexion->error);
}

$filaUsuarios = $resUsuarios->fetch_assoc();
$totalUsuarios = (int)$filaUsuarios['total'];
$existenUsuarios = ($totalUsuarios > 0);

if (isset($_POST['btnIngresar'])) {

    $usuario = trim($_POST['usuario'] ?? '');
    $pass    = trim($_POST['contrasena'] ?? '');

    if ($usuario === '' || $pass === '') {
        $mensaje = "Debe completar usuario y contraseña.";
    } else {

        $stmt = $conexion->prepare("
            SELECT id, nombre, apellido, nombre_usuario, clave, rol, activo
            FROM usuario
            WHERE nombre_usuario = ?
            LIMIT 1
        ");

        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {

            $fila = $resultado->fetch_assoc();

            if ((int)$fila['activo'] !== 1) {

                $mensaje = "El usuario está inactivo. Comuníquese con el administrador.";

            } elseif (password_verify($pass, $fila['clave'])) {

                $_SESSION['id_usuario'] = $fila['id'];
                $_SESSION['usuario'] = $fila['nombre_usuario'];
                $_SESSION['nombre_completo'] = $fila['nombre'] . ' ' . $fila['apellido'];
                $_SESSION['rol'] = $fila['rol'];

                header("Location: index.php");
                exit();

            } else {
                $mensaje = "Usuario o contraseña incorrectos.";
            }

        } else {
            $mensaje = "Usuario o contraseña incorrectos.";
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
<title>Login - SIGENMUNI</title>

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
    max-width: 420px;
}

.card {
    background: rgba(255, 255, 255, 0.97);
    border-radius: 18px;
    padding: 32px 28px;
    box-shadow: 0 20px 45px rgba(0, 0, 0, 0.18);
    border: 1px solid rgba(255, 255, 255, 0.45);
}

.logo {
    text-align: center;
    margin-bottom: 24px;
}

.logo-img {
    width: 80px;
    height: auto;
    margin-bottom: 10px;
}

.logo h1 {
    margin: 0;
    font-size: 34px;
    color: #0f766e;
    letter-spacing: 1px;
}

.subtitulo {
    margin: 8px 0 0;
    color: #6b7280;
    font-size: 15px;
}

.titulo-login {
    font-size: 20px;
    font-weight: bold;
    color: #111827;
    margin-bottom: 18px;
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

.mensaje-info {
    background: #dcfce7;
    color: #166534;
    border: 1px solid #86efac;
    padding: 12px 14px;
    border-radius: 10px;
    margin-bottom: 16px;
    font-size: 14px;
    font-weight: bold;
}

.grupo {
    margin-bottom: 16px;
}

label {
    display: block;
    margin-bottom: 7px;
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

.password-wrapper {
    position: relative;
}

.password-wrapper input {
    padding-right: 48px;
}

.toggle-pass {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    user-select: none;
    font-size: 18px;
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
    margin-top: 6px;
}

.btn:hover {
    background: #115e59;
    transform: translateY(-1px);
}

.links {
    margin-top: 18px;
    text-align: center;
}

.links p {
    margin: 10px 0 0;
    font-size: 14px;
    color: #6b7280;
}

.links a {
    color: #0f766e;
    text-decoration: none;
    font-weight: bold;
}

.links a:hover {
    text-decoration: underline;
}

.pie {
    text-align: center;
    margin-top: 16px;
    color: rgba(255,255,255,0.9);
    font-size: 13px;
}
</style>
</head>

<body>

<div class="contenedor">

    <div class="card">

        <div class="logo">
            <img src="img/escudo.jpg" alt="Escudo Municipalidad" class="logo-img">
            <h1>SIGENMUNI</h1>
            <p class="subtitulo">Sistema de Gestión Municipal</p>
        </div>

        <div class="titulo-login">Iniciar sesión</div>

        <div id="alertaLogin" style="display:none;"></div>

        <?php if (!empty($mensaje)) { ?>
            <div class="mensaje-error"><?php echo htmlspecialchars($mensaje); ?></div>
        <?php } ?>

        <?php if ($existenUsuarios === false) { ?>
            <div class="mensaje-info">No hay usuarios registrados todavía.</div>
        <?php } ?>

        <form method="POST" id="formLogin" novalidate>

            <div class="grupo">
                <label for="usuario">Usuario</label>
                <input type="text" name="usuario" id="usuario" autocomplete="username">
            </div>

            <div class="grupo">
                <label for="contrasena">Contraseña</label>

                <div class="password-wrapper">
                    <input 
                        type="password" 
                        name="contrasena" 
                        id="contrasena"
                        autocomplete="current-password"
                    >

                    <span class="toggle-pass" onclick="togglePassword()">👁</span>
                </div>
            </div>

            <button type="submit" name="btnIngresar" class="btn">Ingresar</button>

        </form>

        <div class="links">

            <p>
                <a href="recuperar.php">
                    Recuperación de acceso para administradores
                </a>
            </p>

            <?php if ($existenUsuarios === false) { ?>
                <p>
                    <a href="registro_inicial.php">
                        Registrar primer usuario
                    </a>
                </p>
            <?php } ?>

        </div>

    </div>

    <div class="pie">
        Municipalidad de Fortín Lugones
    </div>

</div>

<script>
function togglePassword() {
    const input = document.getElementById("contrasena");

    if (input.type === "password") {
        input.type = "text";
    } else {
        input.type = "password";
    }
}

document.getElementById("formLogin").addEventListener("submit", function(e) {
    const usuario = document.getElementById("usuario");
    const contrasena = document.getElementById("contrasena");
    const alerta = document.getElementById("alertaLogin");

    usuario.classList.remove("input-error");
    contrasena.classList.remove("input-error");

    alerta.style.display = "none";
    alerta.className = "";
    alerta.innerHTML = "";

    if (usuario.value.trim() === "") {
        e.preventDefault();

        alerta.className = "mensaje-error";
        alerta.innerHTML = "Debe ingresar el usuario.";
        alerta.style.display = "block";

        usuario.classList.add("input-error");
        usuario.focus();
        return;
    }

    if (contrasena.value.trim() === "") {
        e.preventDefault();

        alerta.className = "mensaje-error";
        alerta.innerHTML = "Debe ingresar la contraseña.";
        alerta.style.display = "block";

        contrasena.classList.add("input-error");
        contrasena.focus();
        return;
    }
});
</script>

</body>
</html>