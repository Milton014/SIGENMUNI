<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Recuperar Acceso - SIGENMUNI</title>

<style>
* { box-sizing: border-box; }

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
    font-size: 32px;
    color: #0f766e;
}

.subtitulo {
    margin: 6px 0 0;
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
    margin-bottom: 18px;
    line-height: 1.5;
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
    display: none;
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
    background: #fff;
    box-shadow: 0 0 0 3px rgba(20, 184, 166, 0.15);
}

input.input-error {
    border-color: #dc2626;
    background: #fff1f2;
    box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.12);
}

.ayuda {
    margin-top: 5px;
    font-size: 12px;
    color: #6b7280;
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
    transition: 0.25s;
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
            <p class="subtitulo">Recuperación segura de acceso</p>
        </div>

        <div class="titulo">Recuperar acceso de ADMIN</div>

        <p class="descripcion">
            Esta opción está disponible solo para usuarios con rol ADMIN.
            Se enviará un código de verificación al correo registrado.
        </p>

        <div id="alertaRecuperar" class="mensaje-error"></div>

        <form action="recuperar_procesar.php" method="POST" id="formRecuperar" novalidate>

            <div class="grupo">
                <label for="correo">Correo registrado</label>
                <input type="email" name="correo" id="correo">
            </div>

            <div class="grupo">
                <label for="dni">DNI del administrador</label>
                <input 
                    type="text"
                    name="dni"
                    id="dni"
                    minlength="7"
                    maxlength="8"
                    inputmode="numeric"
                    pattern="[0-9]{7,8}"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                >
                <div class="ayuda">Ingrese solo números, sin puntos ni espacios.</div>
            </div>

            <button type="submit" class="btn">Enviar código</button>

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
document.getElementById("formRecuperar").addEventListener("submit", function(e) {
    const correo = document.getElementById("correo");
    const dni = document.getElementById("dni");
    const alerta = document.getElementById("alertaRecuperar");

    correo.classList.remove("input-error");
    dni.classList.remove("input-error");

    alerta.style.display = "none";
    alerta.innerHTML = "";

    if (correo.value.trim() === "") {
        e.preventDefault();
        alerta.innerHTML = "Debe ingresar el correo registrado.";
        alerta.style.display = "block";
        correo.classList.add("input-error");
        correo.focus();
        return;
    }

    const formatoCorreo = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!formatoCorreo.test(correo.value.trim())) {
        e.preventDefault();
        alerta.innerHTML = "Debe ingresar un correo válido.";
        alerta.style.display = "block";
        correo.classList.add("input-error");
        correo.focus();
        return;
    }

    if (dni.value.trim() === "") {
        e.preventDefault();
        alerta.innerHTML = "Debe ingresar el DNI del administrador.";
        alerta.style.display = "block";
        dni.classList.add("input-error");
        dni.focus();
        return;
    }

    if (dni.value.trim().length < 7 || dni.value.trim().length > 8) {
        e.preventDefault();
        alerta.innerHTML = "El DNI debe tener entre 7 y 8 números.";
        alerta.style.display = "block";
        dni.classList.add("input-error");
        dni.focus();
        return;
    }
});
</script>

</body>
</html>