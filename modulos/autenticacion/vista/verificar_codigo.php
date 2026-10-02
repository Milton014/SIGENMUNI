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


<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/autenticacion/verificar_codigo.css'), ENT_QUOTES, 'UTF-8') ?>">

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
            data-estilo="verificar_codigo-1"
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
                    data-event-input="verificar_codigo-1"
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
                        data-event-click="verificar_codigo-2"
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
                        data-event-click="verificar_codigo-3"
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


<script src="<?= htmlspecialchars(sigenmuniUrlAsset('js/autenticacion/verificar_codigo.js'), ENT_QUOTES, 'UTF-8') ?>"></script>


</body>

</html>