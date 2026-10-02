<?php

/*
|--------------------------------------------------------------------------
| EDITAR ROL - NAVEGACIÓN ROUTER
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../../../core/Url.php';


$rolEditarId =
    (int)(
        $rol['id']
        ??
        $_GET['id']
        ??
        0
    );


$rolEditarAccion =
    sigenmuniUrlRuta(
        'usuarios/roles/editar',
        [
            'id' =>
                $rolEditarId
        ]
    );


$rolEditarUrlVolver =
    sigenmuniUrlRuta(
        'usuarios/roles'
    );

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Editar Rol - SIGENMUNI</title>

<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/usuarios/rol_editar.css'), ENT_QUOTES, 'UTF-8') ?>">

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
        Editar Rol
    </p>

</div>


<div class="contenedor">


    <div class="panel">


        <h2>
            Editar Rol
        </h2>


        <!-- =================================
             INFORMACIÓN
        ================================== -->

        <?php if ($esAdminPrincipal): ?>


            <div class="advertencia">

                El rol ADMIN principal está protegido.

                No se puede cambiar su nombre, inactivarlo
                ni quitarle el acceso total al sistema.

            </div>


        <?php else: ?>


            <div class="info">

                Modifique los datos del rol.

                Si marca

                <strong>
                    Este rol tendrá acceso total al sistema
                </strong>,

                sus permisos se habilitarán automáticamente.

            </div>


        <?php endif; ?>


        <!-- =================================
             ERROR CONTROLADOR
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
            data-estilo="rol_editar-1"
        ></div>


        <!-- =================================
             FORMULARIO
        ================================== -->

        <form
            method="POST"
            action="<?php
                echo htmlspecialchars(
                    $rolEditarAccion,
                    ENT_QUOTES,
                    'UTF-8'
                );
            ?>"
            id="formRolEditar"
            novalidate
        >


            <!-- =================================
                 NOMBRE
            ================================== -->

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

                            $_POST['nombre']
                            ??
                            $rol['nombre']
                            ??
                            '',

                            ENT_QUOTES,
                            'UTF-8'
                        );

                    ?>"
                    <?php
                    echo $esAdminPrincipal
                        ?
                        'readonly'
                        :
                        '';
                    ?>
                >


            </div>


            <!-- =================================
                 DESCRIPCIÓN
            ================================== -->

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

                        $_POST['descripcion']
                        ??
                        $rol['descripcion']
                        ??
                        '',

                        ENT_QUOTES,
                        'UTF-8'
                    );

                ?></textarea>


            </div>


            <!-- =================================
                 ACCESO TOTAL
            ================================== -->

            <?php

            $esAdminSeleccionado =

                isset($_POST['es_admin'])

                    ?

                    1

                    :

                    (int)(
                        $rol['es_admin']
                        ?? 0
                    );

            ?>


            <div class="check">


                <label>


                    <input
                        type="checkbox"
                        name="es_admin"
                        id="es_admin"
                        value="1"

                        <?php
                        echo (
                            $esAdminSeleccionado
                            ===
                            1
                        )
                            ?
                            'checked'
                            :
                            '';
                        ?>

                        <?php
                        echo $esAdminPrincipal
                            ?
                            'disabled'
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
                data-estilo="rol_editar-2" data-visible="<?php

                    echo (
                        $esAdminSeleccionado
                        ===
                        1
                    )
                        ?
                        'si'
                        :
                        'no';

                ?>"
            >

                Los roles administradores tienen acceso total
                al sistema.

                Sus permisos se mantienen habilitados
                automáticamente.

            </div>


            <!-- =================================
                 ESTADO
            ================================== -->

            <div class="estado-info">


                <strong>
                    Estado actual:
                </strong>


                <?php if (
                    (int)(
                        $rol['activo']
                        ?? 0
                    )
                    ===
                    1
                ): ?>


                    Activo


                <?php else: ?>


                    Inactivo


                <?php endif; ?>


                <br><br>


                El estado del rol se administra desde el listado
                de

                <strong>
                    Gestión de Roles
                </strong>.


                <?php if ($esAdminPrincipal): ?>

                    El rol ADMIN principal permanece siempre activo.

                <?php endif; ?>


            </div>


            <!-- =================================
                 ACCIONES
            ================================== -->

            <div class="acciones">


                <button
                    type="submit"
                    class="btn btn-guardar"
                >
                    Guardar Cambios
                </button>


                <a
                    href="<?php
                    echo htmlspecialchars(
                        $rolEditarUrlVolver,
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


<script src="<?= htmlspecialchars(sigenmuniUrlAsset('js/usuarios/rol_editar.js'), ENT_QUOTES, 'UTF-8') ?>"></script>


</body>

</html>