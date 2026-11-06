<?php

/*
|--------------------------------------------------------------------------
| GESTIÓN DE USUARIOS - NAVEGACIÓN ROUTER
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../../../core/Url.php';
require_once __DIR__ . '/../../../core/Csrf.php';


$usuariosUrlNuevo =
    sigenmuniUrlRuta('usuarios/nuevo', $contextoUsuarios);


$usuariosUrlRoles =
    sigenmuniUrlRuta('usuarios/roles', $contextoRoles);


$usuariosUrlMenu =
    sigenmuniUrlRuta(
    'inicio'
);


$usuariosUrlEstado =
    sigenmuniUrlRuta('usuarios/estado', $contextoUsuarios);


$usuariosCsrfToken =
    sigenmuniCsrfToken();

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Gestión de Usuarios - SIGENMUNI</title>

<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/usuarios/usuarios.css'), ENT_QUOTES, 'UTF-8') ?>">

<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/compartido/paginacion.css'), ENT_QUOTES, 'UTF-8') ?>">
<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/usuarios/paginacion.css'), ENT_QUOTES, 'UTF-8') ?>">
</head>

<body>


<!-- =========================================
     HEADER GLOBAL
========================================= -->

<?php

$sigenmuniHeaderSubtitulo =
    'Gestión de Usuarios del Sistema';

require __DIR__
    . '/../../../componentes/header_sigenmuni.php';

?>


<div class="contenedor">


    <!-- =====================================
         MENSAJE OK
    ====================================== -->

    <?php if (!empty($mensaje)): ?>

        <div class="alerta-ok">

            <?php
            echo htmlspecialchars(
                $mensaje,
                ENT_QUOTES,
                'UTF-8'
            );
            ?>

        </div>

    <?php endif; ?>


    <!-- =====================================
         MENSAJE ERROR
    ====================================== -->

    <?php if (!empty($error)): ?>

        <div class="alerta-error">

            <?php
            echo htmlspecialchars(
                $error,
                ENT_QUOTES,
                'UTF-8'
            );
            ?>

        </div>

    <?php endif; ?>


    <div class="panel">


        <!-- =================================
             TÍTULO
        ================================== -->

        <h2>
            Usuarios Registrados
        </h2>


        <!-- =================================
             INFORMACIÓN
        ================================== -->

        <div class="info">

            Desde esta sección el administrador puede crear, editar,
            activar o inactivar usuarios del sistema.

            La contraseña de cada usuario es asignada únicamente por el
            administrador y debe tener como mínimo 8 caracteres, una letra
            mayúscula y un carácter especial.

            Los roles y permisos se gestionan desde el botón

            <strong>
                Gestión de Roles
            </strong>.

        </div>


        <!-- =================================
             ACCIONES SUPERIORES
        ================================== -->

        <div class="acciones-superiores">


            <a
                href="<?php
                    echo htmlspecialchars(
                        $usuariosUrlNuevo,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                class="btn btn-nuevo"
            >
                + Nuevo usuario
            </a>


            <a
                href="<?php
                    echo htmlspecialchars(
                        $usuariosUrlRoles,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                class="btn btn-roles"
            >
                Gestión de Roles
            </a>


            <a
                href="<?php
                    echo htmlspecialchars(
                        $usuariosUrlMenu,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                class="btn btn-volver"
            >
                Volver al menú
            </a>


        </div>


        <!-- =================================
             TABLA
        ================================== -->

        <form class="filtros-listado" method="GET" action="<?= htmlspecialchars(sigenmuniUrlEntrada(), ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="r" value="usuarios">
            <label for="buscar">
                Buscar
                <input type="search" id="buscar" name="buscar" maxlength="120"
                    placeholder="Nombre, usuario, DNI o email"
                    value="<?= htmlspecialchars($contextoUsuarios['buscar'], ENT_QUOTES, 'UTF-8') ?>">
            </label>
            <label for="activo">
                Estado del usuario
                <select id="activo" name="activo">
                    <?php foreach (['' => 'Todos los estados', '1' => 'Activos', '0' => 'Inactivos'] as $valor => $texto): ?>
                        <option value="<?= $valor ?>" <?= $contextoUsuarios['activo'] === (string)$valor ? 'selected' : '' ?>><?= $texto ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label for="por_pagina">
                Por página
                <select id="por_pagina" name="por_pagina" data-cantidad-listado>
                    <?php foreach ([10, 25, 50] as $valor): ?>
                        <option value="<?= $valor ?>" <?= $paginacion['por_pagina'] === $valor ? 'selected' : '' ?>><?= $valor ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <button class="btn btn-filtrar" type="submit">Buscar</button>
            <a class="btn btn-limpiar" href="<?= htmlspecialchars(sigenmuniUrlRuta('usuarios'), ENT_QUOTES, 'UTF-8') ?>">Limpiar</a>
        </form>
        <?php
        $paginacionRuta = 'usuarios';
        $paginacionParametros = $contextoUsuarios;
        $paginacionEntidad = 'usuarios';
        $paginacionClavePagina = 'pagina';
        $paginacionClaveCantidad = 'por_pagina';
        require __DIR__ . '/../../../componentes/paginacion.php';
        ?>

        <div class="tabla-responsive">

            <?php if (!empty($usuarios)): ?>

                <table>


                    <thead>

                        <tr>

                            <th>ID</th>

                            <th>
                                Nombre completo
                            </th>

                            <th>DNI</th>

                            <th>Usuario</th>

                            <th>Email</th>

                            <th>Rol</th>

                            <th>
                                Estado usuario
                            </th>

                            <th>Acciones</th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php foreach ($usuarios as $u): ?>


                        <?php

                        /*
                        |--------------------------------------------------------------------------
                        | ADMIN PRINCIPAL
                        |--------------------------------------------------------------------------
                        */

                        $esAdminPrincipal = (

                            !empty(
                                $u['rol_nombre']
                            )

                            &&

                            strtoupper(
                                (string)$u[
                                    'rol_nombre'
                                ]
                            )
                            ===
                            'ADMIN'

                            &&

                            (int)(
                                $u['es_admin']
                                ?? 0
                            )
                            ===
                            1

                            &&

                            (int)$u['activo']
                            ===
                            1
                        );

                        ?>


                        <tr>


                            <!-- ID -->

                            <td data-label="ID">

                                <?php
                                echo (int)$u['id'];
                                ?>

                            </td>


                            <!-- NOMBRE -->

                            <td data-label="Nombre">

                                <?php
                                echo htmlspecialchars(
                                    $u['nombre']
                                    . ' '
                                    . $u['apellido'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </td>


                            <!-- DNI -->

                            <td data-label="DNI">

                                <?php
                                echo htmlspecialchars(
                                    $u['dni']
                                    ?? '',
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </td>


                            <!-- USUARIO -->

                            <td data-label="Usuario">

                                <?php
                                echo htmlspecialchars(
                                    $u[
                                        'nombre_usuario'
                                    ],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </td>


                            <!-- EMAIL -->

                            <td data-label="Email">

                                <?php
                                echo htmlspecialchars(
                                    $u['email']
                                    ?? '',
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </td>


                            <!-- ROL -->

                            <td data-label="Rol">


                                <?php if (
                                    !empty(
                                        $u['rol_nombre']
                                    )
                                ): ?>


                                    <?php
                                    echo htmlspecialchars(
                                        $u['rol_nombre'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>


                                    <?php if (
                                        (int)(
                                            $u['es_admin']
                                            ?? 0
                                        )
                                        ===
                                        1
                                    ): ?>

                                        <br>

                                        <span class="rol-admin">
                                            Administrador
                                        </span>

                                    <?php endif; ?>


                                    <?php if (
                                        (int)(
                                            $u['rol_activo']
                                            ?? 0
                                        )
                                        !==
                                        1
                                    ): ?>

                                        <br>

                                        <span class="rol-inactivo">
                                            Rol inactivo
                                        </span>

                                    <?php endif; ?>


                                <?php else: ?>


                                    <span class="rol-inactivo">
                                        Sin rol asignado
                                    </span>


                                <?php endif; ?>


                            </td>


                            <!-- ESTADO -->

                            <td data-label="Estado">


                                <?php if (
                                    (int)$u['activo']
                                    ===
                                    1
                                ): ?>


                                    <span class="estado-activo">
                                        Activo
                                    </span>


                                <?php else: ?>


                                    <span class="estado-inactivo">
                                        Inactivo
                                    </span>


                                <?php endif; ?>


                            </td>


                            <!-- ACCIONES -->

                            <td data-label="Acciones">


                                <div class="acciones-fila">


                                    <!-- EDITAR -->

                                    <a
                                        class="btn btn-editar"
                                        href="<?php
                                            echo htmlspecialchars(
                                                sigenmuniUrlRuta('usuarios/editar', array_merge($contextoUsuarios, [
                                                            'id' =>
                                                                (int)$u['id']
                                                        ])),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                        ?>"
                                    >
                                        Editar
                                    </a>


                                    <!-- ADMIN PROTEGIDO -->

                                    <?php if (
                                        $esAdminPrincipal
                                    ): ?>


                                        <span
                                            class="btn btn-protegido"
                                            title="El usuario ADMIN principal no se puede inactivar"
                                        >
                                            Protegido
                                        </span>


                                    <?php else: ?>


                                        <!-- CAMBIAR ESTADO -->

                                        

                                            <form
                                                method="POST"
                                                action="<?php
                                                    echo htmlspecialchars(
                                                        $usuariosUrlEstado,
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    );
                                                ?>"
                                                class="form-estado"
                                                data-event-submit="usuarios-1" data-event-value="<?php
                                                        echo (
                                                            (int)$u['activo']
                                                            ===
                                                            1
                                                        )
                                                            ?
                                                            '¿Está seguro de inactivar este usuario?'
                                                            :
                                                            '¿Está seguro de activar este usuario?';
                                                    ?>"
                                            >

                                                <input
                                                    type="hidden"
                                                    name="id"
                                                    value="<?php
                                                        echo (int)$u['id'];
                                                    ?>"
                                                >

                                                <input
                                                    type="hidden"
                                                    name="estado"
                                                    value="<?php
                                                        echo (
                                                            (int)$u['activo']
                                                            ===
                                                            1
                                                        )
                                                            ?
                                                            0
                                                            :
                                                            1;
                                                    ?>"
                                                >

                                                <input
                                                    type="hidden"
                                                    name="_csrf"
                                                    value="<?php
                                                        echo htmlspecialchars(
                                                            $usuariosCsrfToken,
                                                            ENT_QUOTES,
                                                            'UTF-8'
                                                        );
                                                    ?>"
                                                >

                                                <button
                                                    type="submit"
                                                    class="btn btn-estado"
                                                >
                                                    <?php
                                                    echo (
                                                        (int)$u['activo']
                                                        ===
                                                        1
                                                    )
                                                        ?
                                                        'Inactivar'
                                                        :
                                                        'Activar';
                                                    ?>
                                                </button>

                                            </form>

                                        


                                    <?php endif; ?>


                                </div>


                            </td>


                        </tr>


                    <?php endforeach; ?>


                    </tbody>


                </table>


            <?php else: ?>


                <div class="sin-datos">

                    No se encontraron usuarios con los filtros seleccionados.

                </div>


            <?php endif; ?>


        </div>


        <?php if (!empty($usuarios)): ?>
            <?php require __DIR__ . '/../../../componentes/paginacion.php'; ?>
        <?php endif; ?>

    </div>


</div>


<script src="<?= htmlspecialchars(sigenmuniUrlAsset('js/usuarios/usuarios.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
<script src="<?= htmlspecialchars(sigenmuniUrlAsset('js/usuarios/paginacion.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
</body>

</html>