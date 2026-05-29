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

.input-error{
    border-color:#dc2626 !important;
    background:#fff1f2 !important;
    box-shadow:0 0 0 3px rgba(220,38,38,.12) !important;
}

.mensaje-error{
    background:#fee2e2;
    color:#991b1b;
    border:1px solid #fecaca;
    padding:12px 14px;
    border-radius:10px;
    margin-bottom:16px;
    font-size:14px;
    font-weight:bold;
    display:none;
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

        <div id="alertaUsuario" class="mensaje-error"></div>

        <form action="usuario_guardar.php" method="POST" id="formUsuario" novalidate>

            <div class="form-grid">

                <div class="campo">
                    <label for="nombre">Nombre *</label>
                    <input type="text" name="nombre" id="nombre">
                </div>

                <div class="campo">
                    <label for="apellido">Apellido *</label>
                    <input type="text" name="apellido" id="apellido">
                </div>

                <div class="campo">
                    <label for="dni">DNI *</label>
                    <input 
                        type="text"
                        name="dni"
                        id="dni"
                        minlength="7"
                        maxlength="8"
                        inputmode="numeric"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                    >
                    <div class="ayuda">Ingrese solo números, sin puntos ni espacios.</div>
                </div>

                <div class="campo">
                    <label for="usuario">Usuario *</label>
                    <input type="text" name="usuario" id="usuario" autocomplete="username">
                </div>

                <div class="campo">
                    <label for="email">Email *</label>
                    <input type="email" name="email" id="email">
                </div>

                <div class="campo">
                    <label for="clave">Contraseña *</label>
                    <input type="password" name="clave" id="clave" autocomplete="new-password">
                </div>

                <div class="campo">
                    <label for="rol">Rol *</label>

                    <select name="rol" id="rol">
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

<script>
document.getElementById("formUsuario").addEventListener("submit", function(e) {

    const alerta = document.getElementById("alertaUsuario");

    const campos = [
        "nombre",
        "apellido",
        "dni",
        "usuario",
        "email",
        "clave",
        "rol"
    ];

    campos.forEach(function(id) {
        document.getElementById(id).classList.remove("input-error");
    });

    alerta.style.display = "none";
    alerta.innerHTML = "";

    function mostrarError(mensaje, id) {
        e.preventDefault();

        const campo = document.getElementById(id);

        alerta.innerHTML = mensaje;
        alerta.style.display = "block";

        campo.classList.add("input-error");
        campo.focus();

        window.scrollTo({
            top: 0,
            behavior: "smooth"
        });
    }

    const nombre = document.getElementById("nombre").value.trim();
    const apellido = document.getElementById("apellido").value.trim();
    const dni = document.getElementById("dni").value.trim();
    const usuario = document.getElementById("usuario").value.trim();
    const email = document.getElementById("email").value.trim();
    const clave = document.getElementById("clave").value.trim();
    const rol = document.getElementById("rol").value;

    if (nombre === "") {
        mostrarError("Debe ingresar el nombre.", "nombre");
        return;
    }

    if (apellido === "") {
        mostrarError("Debe ingresar el apellido.", "apellido");
        return;
    }

    if (dni === "") {
        mostrarError("Debe ingresar el DNI.", "dni");
        return;
    }

    if (!/^[0-9]{7,8}$/.test(dni)) {
        mostrarError("El DNI debe tener entre 7 y 8 números.", "dni");
        return;
    }

    if (usuario === "") {
        mostrarError("Debe ingresar el nombre de usuario.", "usuario");
        return;
    }

    if (email === "") {
        mostrarError("Debe ingresar el email.", "email");
        return;
    }

    const formatoEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!formatoEmail.test(email)) {
        mostrarError("Debe ingresar un email válido.", "email");
        return;
    }

    if (clave === "") {
        mostrarError("Debe ingresar una contraseña.", "clave");
        return;
    }

    if (clave.length < 6) {
        mostrarError("La contraseña debe tener al menos 6 caracteres.", "clave");
        return;
    }

    if (rol === "") {
        mostrarError("Debe seleccionar un rol.", "rol");
        return;
    }

});
</script>

</body>
</html>