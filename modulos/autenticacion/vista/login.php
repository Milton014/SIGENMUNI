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


<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/autenticacion/login.css'), ENT_QUOTES, 'UTF-8') ?>">

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
            data-estilo="login-1"
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
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars(sigenmuniCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">


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
                        data-event-click="login-1"
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


<script src="<?= htmlspecialchars(sigenmuniUrlAsset('js/autenticacion/login.js'), ENT_QUOTES, 'UTF-8') ?>"></script>


</body>

</html>
