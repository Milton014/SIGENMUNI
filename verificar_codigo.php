<?php
require_once("conexion.php");

$correo = trim($_GET['correo'] ?? '');

if ($correo === '') {
    header("Location: recuperar.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Verificar Código - SIGENMUNI</title>

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
    max-width: 450px;
}

.card {
    background: rgba(255,255,255,0.97);
    border-radius: 18px;
    padding: 32px 28px;
    box-shadow: 0 20px 45px rgba(0,0,0,0.18);
}

.logo {
    text-align: center;
    margin-bottom: 22px;
}

.logo-img {
    width: 80px;
    margin-bottom: 10px;
}

.logo h1 {
    margin: 0;
    color: #0f766e;
    font-size: 32px;
}

.subtitulo {
    margin-top: 6px;
    color: #6b7280;
    font-size: 15px;
}

.titulo {
    font-size: 20px;
    font-weight: bold;
    color: #111827;
    margin-bottom: 10px;
}

.descripcion {
    font-size: 14px;
    color: #6b7280;
    line-height: 1.5;
    margin-bottom: 20px;
}

.grupo {
    margin-bottom: 15px;
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
    padding: 12px;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    background: #f8fafc;
    font-size: 14px;
    outline: none;
    transition: 0.2s;
}

input:focus {
    border-color: #14b8a6;
    background: white;
    box-shadow: 0 0 0 3px rgba(20,184,166,0.15);
}

.btn {
    width: 100%;
    padding: 14px;
    background: #0f766e;
    border: none;
    border-radius: 12px;
    color: white;
    font-size: 15px;
    font-weight: bold;
    cursor: pointer;
    transition: 0.25s;
    margin-top: 8px;
}

.btn:hover {
    background: #115e59;
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
            <p class="subtitulo">Verificación de seguridad</p>
        </div>

        <div class="titulo">Ingresar código recibido</div>

        <div class="descripcion">
            Se envió un código de recuperación al correo electrónico registrado.
        </div>

        <form action="actualizar_acceso.php" method="POST">

            <input type="hidden" name="correo" value="<?php echo htmlspecialchars($correo); ?>">

            <div class="grupo">
                <label>Código de verificación</label>
                <input 
                    type="text" 
                    name="codigo" 
                    required 
                    maxlength="6"
                >
            </div>

            <div class="grupo">
                <label>Nuevo nombre de usuario</label>
                <input 
                    type="text" 
                    name="nuevo_usuario" 
                    required
                >
            </div>

            <div class="grupo">
                <label>Nueva contraseña</label>
                <input 
                    type="password" 
                    name="nueva_contra" 
                    required
                >
            </div>

            <div class="grupo">
                <label>Confirmar contraseña</label>
                <input 
                    type="password" 
                    name="confirmar_contra" 
                    required
                >
            </div>

            <button type="submit" class="btn">
                Actualizar acceso
            </button>

        </form>

        <div class="volver">
            <a href="login.php">← Volver al login</a>
        </div>

    </div>

    <div class="pie">
        Municipalidad de Fortín Lugones
    </div>

</div>

</body>
</html>