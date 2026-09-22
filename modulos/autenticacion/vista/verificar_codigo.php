<?php

/*
|--------------------------------------------------------------------------
| VISTA - VERIFICAR CÓDIGO
|--------------------------------------------------------------------------
|
| El correo y las URLs son preparados por AutenticacionControlador.
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
    Verificar Código - SIGENMUNI
</title>


<style>

*{
    box-sizing:border-box;
}


body{

    margin:0;

    min-height:100vh;

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

    display:flex;

    justify-content:center;

    align-items:center;

    padding:20px;
}


.contenedor{

    width:100%;

    max-width:470px;
}


.card{

    background:
        rgba(
            255,
            255,
            255,
            0.97
        );

    border-radius:18px;

    padding:
        32px
        28px;

    box-shadow:
        0 20px 45px
        rgba(
            0,
            0,
            0,
            0.18
        );
}


.logo{

    text-align:center;

    margin-bottom:22px;
}


.logo-img{

    width:80px;

    height:auto;

    margin-bottom:10px;
}


.logo h1{

    margin:0;

    color:#0f766e;

    font-size:32px;

    letter-spacing:1px;
}


.subtitulo{

    margin-top:6px;

    color:#6b7280;

    font-size:15px;
}


.titulo{

    font-size:20px;

    font-weight:bold;

    color:#111827;

    margin-bottom:10px;
}


.descripcion{

    font-size:14px;

    color:#6b7280;

    line-height:1.5;

    margin-bottom:20px;
}


/*
|--------------------------------------------------------------------------
| MENSAJE ERROR
|--------------------------------------------------------------------------
*/

.mensaje-error{

    background:#fee2e2;

    color:#991b1b;

    border:
        1px solid
        #fecaca;

    padding:
        12px
        14px;

    border-radius:10px;

    margin-bottom:16px;

    font-size:14px;

    font-weight:bold;

    line-height:1.45;
}


/*
|--------------------------------------------------------------------------
| FORMULARIO
|--------------------------------------------------------------------------
*/

.grupo{

    margin-bottom:16px;
}


label{

    display:block;

    margin-bottom:6px;

    font-size:14px;

    font-weight:bold;

    color:#374151;
}


input{

    width:100%;

    padding:12px;

    border:
        1px solid
        #cbd5e1;

    border-radius:10px;

    background:#f8fafc;

    font-size:14px;

    outline:none;

    transition:0.2s;
}


input:focus{

    border-color:#14b8a6;

    background:white;

    box-shadow:
        0 0 0 3px
        rgba(
            20,
            184,
            166,
            0.15
        );
}


input.input-error{

    border-color:#dc2626;

    background:#fff1f2;

    box-shadow:
        0 0 0 3px
        rgba(
            220,
            38,
            38,
            0.12
        );
}


/*
|--------------------------------------------------------------------------
| CONTRASEÑAS
|--------------------------------------------------------------------------
*/

.password-wrapper{

    position:relative;
}


.password-wrapper input{

    padding-right:48px;
}


.toggle-pass{

    position:absolute;

    right:14px;

    top:50%;

    transform:
        translateY(
            -50%
        );

    cursor:pointer;

    user-select:none;

    font-size:17px;
}


/*
|--------------------------------------------------------------------------
| REQUISITOS DE CONTRASEÑA
|--------------------------------------------------------------------------
*/

.requisitos-clave{

    margin-top:9px;

    padding:
        11px
        12px;

    background:#f0fdfa;

    border:
        1px solid
        #99f6e4;

    border-radius:10px;
}


.requisitos-titulo{

    display:block;

    color:#0f766e;

    font-size:12px;

    font-weight:bold;

    margin-bottom:7px;
}


.requisito{

    display:flex;

    align-items:center;

    gap:7px;

    margin:
        5px
        0;

    color:#64748b;

    font-size:12px;

    line-height:1.35;
}


.requisito .icono{

    width:18px;

    height:18px;

    flex:
        0
        0
        18px;

    display:flex;

    align-items:center;

    justify-content:center;

    border-radius:50%;

    background:#e5e7eb;

    color:#64748b;

    font-size:11px;

    font-weight:bold;
}


.requisito.valido{

    color:#166534;
}


.requisito.valido .icono{

    background:#dcfce7;

    color:#166534;
}


/*
|--------------------------------------------------------------------------
| AYUDA
|--------------------------------------------------------------------------
*/

.ayuda{

    margin-top:6px;

    color:#6b7280;

    font-size:12px;

    line-height:1.4;
}


/*
|--------------------------------------------------------------------------
| BOTÓN
|--------------------------------------------------------------------------
*/

.btn{

    width:100%;

    padding:14px;

    background:#0f766e;

    border:none;

    border-radius:12px;

    color:white;

    font-size:15px;

    font-weight:bold;

    cursor:pointer;

    transition:0.25s;

    margin-top:8px;
}


.btn:hover{

    background:#115e59;

    transform:
        translateY(
            -1px
        );
}


/*
|--------------------------------------------------------------------------
| VOLVER
|--------------------------------------------------------------------------
*/

.volver{

    margin-top:16px;

    text-align:center;
}


.volver a{

    text-decoration:none;

    color:#0f766e;

    font-weight:bold;
}


.volver a:hover{

    text-decoration:underline;
}


.pie{

    text-align:center;

    margin-top:15px;

    color:
        rgba(
            255,
            255,
            255,
            0.9
        );

    font-size:13px;
}


/*
|--------------------------------------------------------------------------
| RESPONSIVE
|--------------------------------------------------------------------------
*/

@media(max-width:500px){

    .card{

        padding:
            26px
            20px;
    }


    .logo h1{

        font-size:28px;
    }

}

</style>

</head>


<body>


<div class="contenedor">


    <div class="card">


        <!-- LOGO -->

        <div class="logo">

            <img
                src="<?php echo htmlspecialchars($autenticacionUrlEscudo, ENT_QUOTES, 'UTF-8'); ?>"
                class="logo-img"
                alt="Escudo Municipalidad"
            >

            <h1>
                SIGENMUNI
            </h1>

            <p class="subtitulo">
                Verificación de seguridad
            </p>

        </div>


        <!-- TÍTULO -->

        <div class="titulo">
            Ingresar código recibido
        </div>


        <div class="descripcion">

            Se envió un código de recuperación al correo electrónico
            registrado del administrador.

        </div>


        <!-- ERROR JAVASCRIPT -->

        <div
            id="alertaFormulario"
            class="mensaje-error"
            style="display:none;"
        ></div>


        <!-- FORMULARIO -->

        <form
            action="<?php echo htmlspecialchars($autenticacionUrlActualizarAcceso, ENT_QUOTES, 'UTF-8'); ?>"
            method="POST"
            id="formRecuperacion"
            novalidate
        >

            <input
                type="hidden"
                name="_csrf"
                value="<?php echo htmlspecialchars($autenticacionCsrfToken, ENT_QUOTES, 'UTF-8'); ?>"
            >


            <!-- CORREO -->

            <input
                type="hidden"
                name="correo"
                value="<?php
                    echo htmlspecialchars(
                        $correo,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
            >


            <!-- CÓDIGO -->

            <div class="grupo">

                <label for="codigo">
                    Código de verificación
                </label>

                <input
                    type="text"
                    name="codigo"
                    id="codigo"
                    maxlength="6"
                    inputmode="numeric"
                    autocomplete="one-time-code"
                    oninput="
                        this.value =
                        this.value.replace(
                            /[^0-9]/g,
                            ''
                        );
                    "
                >

                <div class="ayuda">
                    Ingrese los 6 números recibidos por correo electrónico.
                </div>

            </div>


            <!-- NUEVO USUARIO -->

            <div class="grupo">

                <label for="nuevo_usuario">
                    Nuevo nombre de usuario
                </label>

                <input
                    type="text"
                    name="nuevo_usuario"
                    id="nuevo_usuario"
                    maxlength="50"
                    autocomplete="username"
                >

            </div>


            <!-- NUEVA CONTRASEÑA -->

            <div class="grupo">

                <label for="nueva_contra">
                    Nueva contraseña
                </label>


                <div class="password-wrapper">

                    <input
                        type="password"
                        name="nueva_contra"
                        id="nueva_contra"
                        minlength="8"
                        autocomplete="new-password"
                    >


                    <span
                        class="toggle-pass"
                        onclick="togglePassword('nueva_contra')"
                        title="Mostrar u ocultar contraseña"
                    >
                        👁
                    </span>

                </div>


                <!-- REQUISITOS -->

                <div class="requisitos-clave">

                    <span class="requisitos-titulo">
                        🔒 Requisitos de contraseña
                    </span>


                    <div
                        class="requisito"
                        id="reqLongitud"
                    >

                        <span class="icono">
                            •
                        </span>

                        Mínimo 8 caracteres

                    </div>


                    <div
                        class="requisito"
                        id="reqMayuscula"
                    >

                        <span class="icono">
                            •
                        </span>

                        Al menos una letra mayúscula

                    </div>


                    <div
                        class="requisito"
                        id="reqEspecial"
                    >

                        <span class="icono">
                            •
                        </span>

                        Al menos un carácter especial
                        (@, #, $, %, !, etc.)

                    </div>

                </div>

            </div>


            <!-- CONFIRMAR CONTRASEÑA -->

            <div class="grupo">

                <label for="confirmar_contra">
                    Confirmar contraseña
                </label>


                <div class="password-wrapper">

                    <input
                        type="password"
                        name="confirmar_contra"
                        id="confirmar_contra"
                        minlength="8"
                        autocomplete="new-password"
                    >


                    <span
                        class="toggle-pass"
                        onclick="togglePassword('confirmar_contra')"
                        title="Mostrar u ocultar contraseña"
                    >
                        👁
                    </span>

                </div>


                <div class="ayuda">
                    Ingrese nuevamente la nueva contraseña.
                </div>

            </div>


            <!-- BOTÓN -->

            <button
                type="submit"
                class="btn"
            >

                Actualizar acceso

            </button>


        </form>


        <!-- VOLVER -->

        <div class="volver">

            <a href="<?php echo htmlspecialchars($autenticacionUrlLogin, ENT_QUOTES, 'UTF-8'); ?>">
                ← Volver al login
            </a>

        </div>


    </div>


    <div class="pie">

        Municipalidad de Fortín Lugones

    </div>


</div>


<script>

/*
|--------------------------------------------------------------------------
| MOSTRAR / OCULTAR CONTRASEÑA
|--------------------------------------------------------------------------
*/

function togglePassword(id)
{
    const input =
        document.getElementById(id);


    input.type =
        input.type === "password"
            ? "text"
            : "password";
}


/*
|--------------------------------------------------------------------------
| ELEMENTOS
|--------------------------------------------------------------------------
*/

const nuevaContra =
    document.getElementById(
        "nueva_contra"
    );


const confirmarContra =
    document.getElementById(
        "confirmar_contra"
    );


const reqLongitud =
    document.getElementById(
        "reqLongitud"
    );


const reqMayuscula =
    document.getElementById(
        "reqMayuscula"
    );


const reqEspecial =
    document.getElementById(
        "reqEspecial"
    );


/*
|--------------------------------------------------------------------------
| ACTUALIZAR INDICADORES
|--------------------------------------------------------------------------
*/

function actualizarRequisito(
    elemento,
    valido
) {

    const icono =
        elemento.querySelector(
            ".icono"
        );


    if (valido) {

        elemento.classList.add(
            "valido"
        );

        icono.textContent =
            "✓";

    } else {

        elemento.classList.remove(
            "valido"
        );

        icono.textContent =
            "•";
    }
}


function actualizarRequisitos()
{
    const clave =
        nuevaContra.value;


    /*
    |--------------------------------------------------------------------------
    | 8 CARACTERES
    |--------------------------------------------------------------------------
    */

    actualizarRequisito(
        reqLongitud,
        [...clave].length >= 8
    );


    /*
    |--------------------------------------------------------------------------
    | MAYÚSCULA
    |--------------------------------------------------------------------------
    */

    actualizarRequisito(
        reqMayuscula,
        /\p{Lu}/u.test(
            clave
        )
    );


    /*
    |--------------------------------------------------------------------------
    | CARÁCTER ESPECIAL
    |--------------------------------------------------------------------------
    */

    actualizarRequisito(
        reqEspecial,
        /[^\p{L}\p{N}\s]/u.test(
            clave
        )
    );
}


nuevaContra.addEventListener(
    "input",
    actualizarRequisitos
);


/*
|--------------------------------------------------------------------------
| VALIDAR FORMULARIO
|--------------------------------------------------------------------------
*/

document
    .getElementById(
        "formRecuperacion"
    )
    .addEventListener(
        "submit",
        function(e)
        {

            const codigo =
                document.getElementById(
                    "codigo"
                );


            const nuevoUsuario =
                document.getElementById(
                    "nuevo_usuario"
                );


            const alerta =
                document.getElementById(
                    "alertaFormulario"
                );


            /*
            |--------------------------------------------------------------------------
            | LIMPIAR ERRORES
            |--------------------------------------------------------------------------
            */

            const campos = [

                codigo,

                nuevoUsuario,

                nuevaContra,

                confirmarContra

            ];


            campos.forEach(
                function(campo)
                {
                    campo
                        .classList
                        .remove(
                            "input-error"
                        );
                }
            );


            alerta.style.display =
                "none";


            alerta.textContent =
                "";


            /*
            |--------------------------------------------------------------------------
            | FUNCIÓN ERROR
            |--------------------------------------------------------------------------
            */

            function mostrarError(
                mensaje,
                campo
            ) {

                e.preventDefault();


                alerta.textContent =
                    mensaje;


                alerta.style.display =
                    "block";


                campo
                    .classList
                    .add(
                        "input-error"
                    );


                campo.focus();


                window.scrollTo({

                    top:0,

                    behavior:"smooth"

                });
            }


            /*
            |--------------------------------------------------------------------------
            | CÓDIGO
            |--------------------------------------------------------------------------
            */

            const valorCodigo =
                codigo.value.trim();


            if (
                valorCodigo === ""
            ) {

                mostrarError(
                    "Debe ingresar el código de verificación.",
                    codigo
                );

                return;
            }


            if (
                !/^[0-9]{6}$/.test(
                    valorCodigo
                )
            ) {

                mostrarError(
                    "El código debe contener exactamente 6 números.",
                    codigo
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | USUARIO
            |--------------------------------------------------------------------------
            */

            if (
                nuevoUsuario
                    .value
                    .trim()
                ===
                ""
            ) {

                mostrarError(
                    "Debe ingresar el nuevo nombre de usuario.",
                    nuevoUsuario
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | NUEVA CONTRASEÑA
            |--------------------------------------------------------------------------
            */

            const clave =
                nuevaContra.value;


            if (
                clave === ""
            ) {

                mostrarError(
                    "Debe ingresar la nueva contraseña.",
                    nuevaContra
                );

                return;
            }


            if (
                [...clave].length
                <
                8
            ) {

                mostrarError(
                    "La contraseña debe tener al menos 8 caracteres.",
                    nuevaContra
                );

                return;
            }


            if (
                !/\p{Lu}/u.test(
                    clave
                )
            ) {

                mostrarError(
                    "La contraseña debe contener al menos una letra mayúscula.",
                    nuevaContra
                );

                return;
            }


            if (
                !/[^\p{L}\p{N}\s]/u.test(
                    clave
                )
            ) {

                mostrarError(
                    "La contraseña debe contener al menos un carácter especial.",
                    nuevaContra
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | CONFIRMAR
            |--------------------------------------------------------------------------
            */

            if (
                confirmarContra.value
                ===
                ""
            ) {

                mostrarError(
                    "Debe confirmar la nueva contraseña.",
                    confirmarContra
                );

                return;
            }


            if (
                clave
                !==
                confirmarContra.value
            ) {

                mostrarError(
                    "Las contraseñas ingresadas no coinciden.",
                    confirmarContra
                );

                return;
            }

        }
    );


/*
|--------------------------------------------------------------------------
| INICIALIZAR
|--------------------------------------------------------------------------
*/

actualizarRequisitos();

</script>


</body>

</html>