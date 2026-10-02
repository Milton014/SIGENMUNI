<?php

/*
|--------------------------------------------------------------------------
| NUEVO USUARIO - NAVEGACIÓN ROUTER
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../../../core/Url.php';


$usuarioNuevoAccion =
    sigenmuniUrlRuta(
        'usuarios/nuevo'
    );


$usuarioNuevoUrlVolver =
    sigenmuniUrlRuta(
        'usuarios'
    );

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Nuevo Usuario - SIGENMUNI</title>

<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/usuarios/usuario_nuevo.css'), ENT_QUOTES, 'UTF-8') ?>">

</head>


<body>


<!-- =========================================
     HEADER
========================================= -->

<div class="header">

    <h1>
        SIGENMUNI
    </h1>

    <p>
        Alta de usuarios del sistema
    </p>

</div>


<div class="contenedor">


    <div class="panel">


        <h2>
            Nuevo Usuario
        </h2>


        <!-- =================================
             ERROR DEL CONTROLADOR
        ================================== -->

        <?php if (!empty($error)): ?>

            <div class="mensaje-error">

                <?php
                echo htmlspecialchars(
                    $error,
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>

            </div>

        <?php endif; ?>


        <!-- =================================
             ERROR JAVASCRIPT
        ================================== -->

        <div
            id="alertaUsuario"
            class="mensaje-error"
            data-estilo="usuario_nuevo-1"
        ></div>


        <!-- =================================
             FORMULARIO
        ================================== -->

        <form
            method="POST"
            action="<?php
                echo htmlspecialchars(
                    $usuarioNuevoAccion,
                    ENT_QUOTES,
                    'UTF-8'
                );
            ?>"
            id="formUsuario"
            novalidate
        >


            <div class="form-grid">


                <!-- NOMBRE -->

                <div class="campo">

                    <label for="nombre">
                        Nombre *
                    </label>

                    <input
                        type="text"
                        name="nombre"
                        id="nombre"
                        maxlength="100"
                        value="<?php
                            echo htmlspecialchars(
                                $datosUsuario['nombre']
                                ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                        autocomplete="given-name"
                    >

                </div>


                <!-- APELLIDO -->

                <div class="campo">

                    <label for="apellido">
                        Apellido *
                    </label>

                    <input
                        type="text"
                        name="apellido"
                        id="apellido"
                        maxlength="100"
                        value="<?php
                            echo htmlspecialchars(
                                $datosUsuario['apellido']
                                ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                        autocomplete="family-name"
                    >

                </div>


                <!-- DNI -->

                <div class="campo">

                    <label for="dni">
                        DNI *
                    </label>

                    <input
                        type="text"
                        name="dni"
                        id="dni"
                        minlength="7"
                        maxlength="8"
                        inputmode="numeric"
                        value="<?php
                            echo htmlspecialchars(
                                $datosUsuario['dni']
                                ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                        data-event-input="usuario_nuevo-1"
                    >

                    <div class="ayuda">
                        Ingrese solo números, sin puntos ni espacios.
                    </div>

                </div>


                <!-- NOMBRE DE USUARIO -->

                <div class="campo">

                    <label for="nombre_usuario">
                        Usuario *
                    </label>

                    <input
                        type="text"
                        name="nombre_usuario"
                        id="nombre_usuario"
                        maxlength="100"
                        value="<?php
                            echo htmlspecialchars(
                                $datosUsuario['nombre_usuario']
                                ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                        autocomplete="username"
                    >

                </div>


                <!-- EMAIL -->

                <div class="campo">

                    <label for="email">
                        Email *
                    </label>

                    <input
                        type="email"
                        name="email"
                        id="email"
                        maxlength="150"
                        value="<?php
                            echo htmlspecialchars(
                                $datosUsuario['email']
                                ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                        autocomplete="email"
                    >

                </div>


                <!-- CONTRASEÑA -->

                <div class="campo">

                    <label for="clave">
                        Contraseña *
                    </label>

                    <div class="password-wrapper">

                        <input
                            type="password"
                            name="clave"
                            id="clave"
                            minlength="8"
                            autocomplete="new-password"
                        >

                        <span
                            class="toggle-pass"
                            data-event-click="usuario_nuevo-2"
                            title="Mostrar u ocultar contraseña"
                        >
                            👁
                        </span>

                    </div>

                    <div class="requisitos-clave">

                        <span class="requisitos-titulo">
                            🔒 Requisitos de contraseña
                        </span>

                        <div
                            class="requisito"
                            id="reqLongitud"
                        >
                            <span class="icono">•</span>
                            Mínimo 8 caracteres
                        </div>

                        <div
                            class="requisito"
                            id="reqMayuscula"
                        >
                            <span class="icono">•</span>
                            Al menos una letra mayúscula
                        </div>

                        <div
                            class="requisito"
                            id="reqEspecial"
                        >
                            <span class="icono">•</span>
                            Al menos un carácter especial
                            (@, #, $, %, !, etc.)
                        </div>

                    </div>

                </div>


                <!-- CONFIRMAR CONTRASEÑA -->

                <div class="campo">

                    <label for="confirmar_clave">
                        Confirmar Contraseña *
                    </label>

                    <div class="password-wrapper">

                        <input
                            type="password"
                            name="confirmar_clave"
                            id="confirmar_clave"
                            minlength="8"
                            autocomplete="new-password"
                        >

                        <span
                            class="toggle-pass"
                            data-event-click="usuario_nuevo-3"
                            title="Mostrar u ocultar contraseña"
                        >
                            👁
                        </span>

                    </div>

                    <div class="ayuda">
                        Ingrese nuevamente la contraseña asignada por el administrador.
                    </div>

                </div>


                <!-- ROL -->

                <div class="campo">

                    <label for="rol_id">
                        Rol *
                    </label>


                    <select
                        name="rol_id"
                        id="rol_id"
                    >

                        <option value="">
                            Seleccione un rol
                        </option>


                        <?php foreach ($roles as $r): ?>


                            <option
                                value="<?php
                                    echo (int)$r['id'];
                                ?>"
                                <?php
                                echo (
                                    (int)(
                                        $datosUsuario['rol_id']
                                        ?? 0
                                    )
                                    ===
                                    (int)$r['id']
                                )
                                    ?
                                    'selected'
                                    :
                                    '';
                                ?>
                            >

                                <?php
                                echo htmlspecialchars(
                                    $r['nombre'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </option>


                        <?php endforeach; ?>


                    </select>


                    <div class="ayuda">

                        Los roles y permisos se administran desde
                        Gestión de Roles.

                    </div>


                </div>


            </div>


            <!-- =================================
                 ACCIONES
            ================================== -->

            <div class="acciones">


                <button
                    type="submit"
                    class="btn-guardar"
                >
                    Guardar usuario
                </button>


                <a
                    href="<?php
                    echo htmlspecialchars(
                        $usuarioNuevoUrlVolver,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                    class="btn btn-volver"
                >
                    Volver
                </a>


            </div>


        </form>


    </div>


</div>


<script src="<?= htmlspecialchars(sigenmuniUrlAsset('js/usuarios/usuario_nuevo.js'), ENT_QUOTES, 'UTF-8') ?>"></script>


</body>

</html>