<?php

/*
|--------------------------------------------------------------------------
| NUEVO ROL - NAVEGACIÓN ROUTER
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../../../core/Url.php';


$rolNuevoAccion =
    sigenmuniUrlRuta(
        'usuarios/roles/nuevo'
    );


$rolNuevoUrlVolver =
    sigenmuniUrlRuta(
        'usuarios/roles'
    );

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Nuevo Rol - SIGENMUNI</title>

<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/usuarios/rol_nuevo.css'), ENT_QUOTES, 'UTF-8') ?>">

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
        Alta de Roles
    </p>

</div>


<div class="contenedor">


    <div class="panel">


        <h2>
            Nuevo Rol
        </h2>


        <!-- =================================
             INFORMACIÓN
        ================================== -->

        <div class="info">

            Desde aquí puede crear nuevos perfiles como:

            <strong>
                RRHH, TESORERÍA, CONTADURÍA, INVITADO,
                AUDITORÍA, SECRETARÍA
            </strong>

            u otros roles que necesite el sistema.

        </div>


        <!-- =================================
             ERROR DEL CONTROLADOR
        ================================== -->

        <?php if (!empty($error)): ?>

            <div class="error">

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
            class="error"
            id="alertaFormulario"
            data-estilo="rol_nuevo-1"
        ></div>


        <!-- =================================
             FORMULARIO
        ================================== -->

        <form
            method="POST"
            action="<?php
                echo htmlspecialchars(
                    $rolNuevoAccion,
                    ENT_QUOTES,
                    'UTF-8'
                );
            ?>"
            id="formRol"
            novalidate
        >



            <!-- NOMBRE -->

            <div class="grupo">

                <label for="nombre">
                    Nombre del Rol *
                </label>


                <input
                    type="text"
                    name="nombre"
                    id="nombre"
                    maxlength="100"
                    value="<?php
                        echo htmlspecialchars(
                            $datosRol['nombre']
                            ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                    autocomplete="off"
                >

            </div>


            <!-- DESCRIPCIÓN -->

            <div class="grupo">

                <label for="descripcion">
                    Descripción
                </label>


                <textarea
                    name="descripcion"
                    id="descripcion"
                    maxlength="500"
                    placeholder="Descripción del rol..."
                ><?php
                    echo htmlspecialchars(
                        $datosRol['descripcion']
                        ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?></textarea>

            </div>


            <!-- =================================
                 ADMINISTRADOR
            ================================== -->

            <div class="check">

                <label>

                    <input
                        type="checkbox"
                        name="es_admin"
                        id="es_admin"
                        value="1"
                        <?php
                        echo (
                            (int)(
                                $datosRol['es_admin']
                                ?? 0
                            )
                            ===
                            1
                        )
                            ?
                            'checked'
                            :
                            '';
                        ?>
                    >

                    Este rol tendrá acceso total al sistema

                </label>

            </div>


            <div
                class="aviso-admin"
                id="avisoAdmin"
                data-estilo="rol_nuevo-2" data-visible="<?php
                    echo (
                        (int)(
                            $datosRol['es_admin']
                            ?? 0
                        )
                        ===
                        1
                    )
                        ?
                        'si'
                        :
                        'no';
                ?>"
            >

                Un rol administrador tendrá acceso total a los módulos
                del sistema. Sus permisos se habilitarán automáticamente.

            </div>


            <!-- =================================
                 ESTADO
            ================================== -->

            <div class="check">

                <label>

                    <input
                        type="checkbox"
                        checked
                        disabled
                    >

                    Rol activo

                </label>

            </div>


            <div class="info" data-estilo="rol_nuevo-3">

                Los roles nuevos se crean inicialmente en estado
                <strong>Activo</strong>.

                Luego podrá activarlos o inactivarlos desde
                Gestión de Roles.

            </div>


            <!-- =================================
                 ACCIONES
            ================================== -->

            <div class="acciones">


                <button
                    type="submit"
                    class="btn btn-guardar"
                >
                    Guardar Rol
                </button>


                <a
                    href="<?php
                    echo htmlspecialchars(
                        $rolNuevoUrlVolver,
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


<script src="<?= htmlspecialchars(sigenmuniUrlAsset('js/usuarios/rol_nuevo.js'), ENT_QUOTES, 'UTF-8') ?>"></script>


</body>

</html>