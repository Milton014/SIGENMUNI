<?php

/*
|--------------------------------------------------------------------------
| VISTA - LOGIN
|--------------------------------------------------------------------------
|
| La lógica de autenticación y las consultas SQL se resuelven en el
| Controlador y el Modelo. Esta vista solo presenta el formulario.
|
|--------------------------------------------------------------------------
*/

?>
<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>
    Login - SIGENMUNI
</title>


<style>

* {
    box-sizing: border-box;
}


body {

    margin: 0;

    min-height: 100vh;

    font-family:
        Arial,
        sans-serif;

    background:
        linear-gradient(
            135deg,
            #0f766e 0%,
            #115e59 45%,
            #e6fffb 100%
        );

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

    background:
        rgba(
            255,
            255,
            255,
            0.97
        );

    border-radius: 18px;

    padding: 32px 28px;

    box-shadow:
        0 20px 45px
        rgba(
            0,
            0,
            0,
            0.18
        );

    border:
        1px solid
        rgba(
            255,
            255,
            255,
            0.45
        );
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

    margin:
        8px
        0
        0;

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

    border:
        1px solid
        #fecaca;

    padding:
        12px
        14px;

    border-radius: 10px;

    margin-bottom: 16px;

    font-size: 14px;

    font-weight: bold;

    line-height: 1.5;
}


.mensaje-info {

    background: #dcfce7;

    color: #166534;

    border:
        1px solid
        #86efac;

    padding:
        12px
        14px;

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

    padding:
        13px
        14px;

    border:
        1px solid
        #cbd5e1;

    border-radius: 10px;

    background: #f8fafc;

    font-size: 14px;

    outline: none;

    transition:
        0.2s ease;
}


input:focus {

    border-color: #14b8a6;

    background: #ffffff;

    box-shadow:
        0 0 0 3px
        rgba(
            20,
            184,
            166,
            0.15
        );
}


input.input-error {

    border-color: #dc2626;

    background: #fff1f2;

    box-shadow:
        0 0 0 3px
        rgba(
            220,
            38,
            38,
            0.12
        );
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

    transform:
        translateY(
            -50%
        );

    cursor: pointer;

    user-select: none;

    font-size: 18px;
}


/*
|--------------------------------------------------------------------------
| REQUISITOS DE CONTRASEÑA
|--------------------------------------------------------------------------
*/

.requisitos-clave {

    margin-top: 9px;

    padding:
        10px
        12px;

    background: #f0fdfa;

    border:
        1px solid
        #99f6e4;

    border-radius: 9px;

    color: #475569;

    font-size: 12px;

    line-height: 1.5;
}


.requisitos-clave-titulo {

    display: block;

    color: #0f766e;

    font-weight: bold;

    margin-bottom: 4px;
}


.requisitos-clave ul {

    margin:
        5px
        0
        0
        18px;

    padding: 0;
}


.requisitos-clave li {

    margin:
        2px
        0;
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

    transition:
        0.25s ease;

    margin-top: 6px;
}


.btn:hover {

    background: #115e59;

    transform:
        translateY(
            -1px
        );
}


.links {

    margin-top: 18px;

    text-align: center;
}


.links p {

    margin:
        10px
        0
        0;

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

    color:
        rgba(
            255,
            255,
            255,
            0.9
        );

    font-size: 13px;
}


@media(max-width:500px) {

    .card {

        padding:
            26px
            20px;
    }


    .logo h1 {

        font-size: 30px;
    }
}

</style>

</head>


<body>


<div class="contenedor">


    <div class="card">


        <div class="logo">

            <img
                src="<?php echo htmlspecialchars($autenticacionUrlEscudo, ENT_QUOTES, 'UTF-8'); ?>"
                alt="Escudo Municipalidad"
                class="logo-img"
            >

            <h1>
                SIGENMUNI
            </h1>

            <p class="subtitulo">
                Sistema de Gestión Municipal
            </p>

        </div>


        <div class="titulo-login">
            Iniciar sesión
        </div>


        <div
            id="alertaLogin"
            style="display:none;"
        ></div>


        <?php if (!empty($mensaje)): ?>

            <div class="mensaje-error">

                <?php
                echo htmlspecialchars(
                    $mensaje,
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>

            </div>

        <?php endif; ?>


        <?php if (!empty($mensajeExito)): ?>

            <div class="mensaje-info">

                <?php
                echo htmlspecialchars(
                    $mensajeExito,
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>

            </div>

        <?php endif; ?>


        <?php if ($existenUsuarios === false): ?>

            <div class="mensaje-info">

                No hay usuarios registrados todavía.

            </div>

        <?php endif; ?>


        <form
            method="POST"
            action="<?php echo htmlspecialchars($autenticacionUrlLogin, ENT_QUOTES, 'UTF-8'); ?>"
            id="formLogin"
            novalidate
        >


            <!-- USUARIO -->

            <div class="grupo">

                <label for="usuario">
                    Usuario
                </label>


                <input
                    type="text"
                    name="usuario"
                    id="usuario"
                    autocomplete="username"

                    value="<?php
                        echo htmlspecialchars(
                            $usuarioIngresado
                            ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                >

            </div>


            <!-- CONTRASEÑA -->

            <div class="grupo">

                <label for="contrasena">
                    Contraseña
                </label>


                <div class="password-wrapper">

                    <input
                        type="password"
                        name="contrasena"
                        id="contrasena"
                        autocomplete="current-password"
                    >


                    <span
                        class="toggle-pass"
                        onclick="togglePassword()"
                        title="Mostrar u ocultar contraseña"
                    >
                        👁
                    </span>

                </div>


                <!-- REQUISITOS -->

                <div class="requisitos-clave">

                    <span class="requisitos-clave-titulo">
                        🔒 Requisitos de contraseña
                    </span>


                    <ul>

                        <li>
                            Mínimo 8 caracteres
                        </li>

                        <li>
                            Al menos una letra mayúscula
                        </li>

                        <li>
                            Al menos un carácter especial
                            (@, #, $, %, !, etc.)
                        </li>

                    </ul>

                </div>

            </div>


            <!-- BOTÓN -->

            <button
                type="submit"
                name="btnIngresar"
                class="btn"
            >

                Ingresar

            </button>


        </form>


        <div class="links">

            <p>

                <a href="<?php echo htmlspecialchars($autenticacionUrlRecuperar, ENT_QUOTES, 'UTF-8'); ?>">

                    Recuperación de acceso para administradores

                </a>

            </p>

        </div>


    </div>


    <div class="pie">

        Municipalidad de Fortín Lugones

    </div>


</div>


<script>

/*
|--------------------------------------------------------------------------
| LIMPIAR CACHE LOCAL DE UNA SESIÓN ANTERIOR
|--------------------------------------------------------------------------
|
| Si llegamos a login.php sin una sesión PHP válida, cualquier dato visual
| guardado en localStorage debe considerarse obsoleto.
|
| Nunca se guardan contraseñas, hashes ni permisos sensibles en localStorage.
|
|--------------------------------------------------------------------------
*/

try {
    localStorage.removeItem("sigenmuni_sesion");
} catch (error) {
    console.warn("No se pudo limpiar el cache local de SIGENMUNI.", error);
}


/*
|--------------------------------------------------------------------------
| MOSTRAR / OCULTAR CONTRASEÑA
|--------------------------------------------------------------------------
*/

function togglePassword() {

    const input =
        document.getElementById(
            "contrasena"
        );


    if (
        input.type
        ===
        "password"
    ) {

        input.type =
            "text";

    } else {

        input.type =
            "password";
    }
}


/*
|--------------------------------------------------------------------------
| VALIDACIÓN BÁSICA DEL FORMULARIO
|--------------------------------------------------------------------------
|
| No hacemos la validación de complejidad aquí.
|
| Esa validación se realiza del lado del servidor DESPUÉS de comprobar
| mediante password_verify() que la contraseña ingresada realmente
| pertenece al usuario.
|
|--------------------------------------------------------------------------
*/

document
    .getElementById(
        "formLogin"
    )
    .addEventListener(
        "submit",
        function(e) {

            const usuario =
                document.getElementById(
                    "usuario"
                );


            const contrasena =
                document.getElementById(
                    "contrasena"
                );


            const alerta =
                document.getElementById(
                    "alertaLogin"
                );


            /*
            |--------------------------------------------------------------------------
            | LIMPIAR ERRORES
            |--------------------------------------------------------------------------
            */

            usuario.classList.remove(
                "input-error"
            );


            contrasena.classList.remove(
                "input-error"
            );


            alerta.style.display =
                "none";


            alerta.className =
                "";


            alerta.textContent =
                "";


            /*
            |--------------------------------------------------------------------------
            | VALIDAR USUARIO
            |--------------------------------------------------------------------------
            */

            if (
                usuario.value.trim()
                ===
                ""
            ) {

                e.preventDefault();


                alerta.className =
                    "mensaje-error";


                alerta.textContent =
                    "Debe ingresar el usuario.";


                alerta.style.display =
                    "block";


                usuario.classList.add(
                    "input-error"
                );


                usuario.focus();


                return;
            }


            /*
            |--------------------------------------------------------------------------
            | VALIDAR CONTRASEÑA VACÍA
            |--------------------------------------------------------------------------
            */

            if (
                contrasena.value
                ===
                ""
            ) {

                e.preventDefault();


                alerta.className =
                    "mensaje-error";


                alerta.textContent =
                    "Debe ingresar la contraseña.";


                alerta.style.display =
                    "block";


                contrasena.classList.add(
                    "input-error"
                );


                contrasena.focus();


                return;
            }

        }
    );

</script>


</body>

</html>