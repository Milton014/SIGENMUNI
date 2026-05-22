<?php
session_start();
require_once("seguridad.php");

verificarSesion();
soloAdmin();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Nuevo Usuario - SIGENMUNI</title>

<style>

body{
    font-family: Arial, sans-serif;
    background:#f4f7fb;
    margin:0;
    color:#1f2937;
}

.header{
    background:linear-gradient(135deg,#0f766e,#14b8a6);
    color:white;
    padding:22px 30px;
}

.header h1{
    margin:0;
    font-size:28px;
}

.header p{
    margin-top:6px;
}

.contenedor{
    width:92%;
    max-width:900px;
    margin:30px auto;
}

.panel{
    background:white;
    padding:28px;
    border-radius:18px;
    box-shadow:0 8px 20px rgba(0,0,0,0.10);
}

h2{
    margin-top:0;
    margin-bottom:20px;
}

.form-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
    gap:16px;
}

.campo{
    display:flex;
    flex-direction:column;
}

label{
    font-weight:bold;
    margin-bottom:6px;
    color:#374151;
    font-size:14px;
}

input,
select{
    width:100%;
    padding:12px;
    border:1px solid #cbd5e1;
    border-radius:10px;
    font-size:14px;
    box-sizing:border-box;
    transition:0.2s;
}

input:focus,
select:focus{
    outline:none;
    border-color:#14b8a6;
    box-shadow:0 0 0 3px rgba(20,184,166,0.15);
}

.ayuda{
    margin-top:5px;
    font-size:12px;
    color:#6b7280;
}

.acciones{
    margin-top:22px;
    display:flex;
    gap:10px;
    flex-wrap:wrap;
}

button,
.btn{
    padding:12px 18px;
    border-radius:10px;
    border:none;
    text-decoration:none;
    font-weight:bold;
    cursor:pointer;
    display:inline-block;
}

.btn-guardar{
    background:#0f766e;
    color:white;
}

.btn-guardar:hover{
    background:#115e59;
}

.btn-volver{
    background:#1f2937;
    color:white;
}

.btn-volver:hover{
    background:#111827;
}

</style>
</head>

<body>

<div class="header">
    <h1>SIGENMUNI</h1>
    <p>Alta de usuarios del sistema</p>
</div>

<div class="contenedor">

    <div class="panel">

        <h2>Nuevo Usuario</h2>

        <form action="usuario_guardar.php" method="POST">

            <div class="form-grid">

                <div class="campo">
                    <label>Nombre</label>
                    <input type="text" name="nombre" required>
                </div>

                <div class="campo">
                    <label>Apellido</label>
                    <input type="text" name="apellido" required>
                </div>

                <div class="campo">
                    <label>DNI</label>
                    <input 
                        type="text"
                        name="dni"
                        required
                        minlength="7"
                        maxlength="8"
                        inputmode="numeric"
                        pattern="[0-9]{7,8}"
                        title="El DNI debe contener solo números, entre 7 y 8 dígitos."
                        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                    >
                    <div class="ayuda">Ingrese solo números, sin puntos ni espacios.</div>
                </div>

                <div class="campo">
                    <label>Usuario</label>
                    <input type="text" name="usuario" required>
                </div>

                <div class="campo">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>

                <div class="campo">
                    <label>Contraseña</label>
                    <input type="password" name="clave" required>
                </div>

                <div class="campo">
                    <label>Rol</label>

                    <select name="rol" required>
                        <option value="OPERADOR">OPERADOR</option>
                        <option value="ADMIN">ADMIN</option>
                    </select>
                </div>

            </div>

            <div class="acciones">
                <button type="submit" class="btn-guardar">
                    Guardar usuario
                </button>

                <a href="usuarios.php" class="btn btn-volver">
                    Volver
                </a>
            </div>

        </form>

    </div>

</div>

</body>
</html>