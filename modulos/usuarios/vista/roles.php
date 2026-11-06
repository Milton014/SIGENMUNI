<?php

/*
|--------------------------------------------------------------------------
| GESTIÓN DE ROLES - NAVEGACIÓN ROUTER
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../../../core/Url.php';
require_once __DIR__ . '/../../../core/Csrf.php';


$rolesUrlNuevo =
    sigenmuniUrlRuta('usuarios/roles/nuevo', $contextoRoles);


$rolesUrlUsuarios =
    sigenmuniUrlRuta('usuarios', $contextoUsuarios);


$rolesUrlEstado =
    sigenmuniUrlRuta('usuarios/roles/estado', $contextoRoles);


$rolesCsrfToken =
    sigenmuniCsrfToken();

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Gestión de Roles - SIGENMUNI</title>

<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/usuarios/roles.css'), ENT_QUOTES, 'UTF-8') ?>">

<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/compartido/paginacion.css'), ENT_QUOTES, 'UTF-8') ?>">
<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/usuarios/paginacion.css'), ENT_QUOTES, 'UTF-8') ?>">
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
        Gestión de Roles
    </p>

</div>


<div class="contenedor">


    <!-- =====================================
         MENSAJE OK
    ====================================== -->

    <?php if (!empty($mensaje)): ?>

        <div class="alerta">

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
             CABECERA
        ================================== -->

        <div class="panel-header">


            <div>

                <h2>
                    Roles del Sistema
                </h2>

                <p>
                    Administrá los perfiles, estados y permisos de acceso del sistema.
                </p>

            </div>


            <div class="acciones">


                <a
                    href="<?php
                    echo htmlspecialchars(
                        $rolesUrlNuevo,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                    class="btn btn-nuevo"
                >
                    + Nuevo Rol
                </a>


                <a
                    href="<?php
                    echo htmlspecialchars(
                        $rolesUrlUsuarios,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                    class="btn btn-volver"
                >
                    Volver a Usuarios
                </a>


            </div>


        </div>


        <!-- =================================
             TABLA ESCRITORIO
        ================================== -->

        <form class="filtros-listado" method="GET" action="<?= htmlspecialchars(sigenmuniUrlEntrada(), ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="r" value="usuarios/roles">
            <?php foreach ($contextoUsuarios as $clave => $valor): ?>
                <input type="hidden" name="<?= htmlspecialchars($clave, ENT_QUOTES, 'UTF-8') ?>" value="<?= htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8') ?>">
            <?php endforeach; ?>
            <label for="buscar_rol">
                Buscar
                <input type="search" id="buscar_rol" name="buscar_rol" maxlength="120"
                    placeholder="Nombre o descripción del rol"
                    value="<?= htmlspecialchars($contextoRoles['buscar_rol'], ENT_QUOTES, 'UTF-8') ?>">
            </label>
            <label for="activo_rol">
                Estado del rol
                <select id="activo_rol" name="activo_rol">
                    <?php foreach (['' => 'Todos los estados', '1' => 'Activos', '0' => 'Inactivos'] as $valor => $texto): ?>
                        <option value="<?= $valor ?>" <?= $contextoRoles['activo_rol'] === (string)$valor ? 'selected' : '' ?>><?= $texto ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label for="por_pagina_roles">
                Por página
                <select id="por_pagina_roles" name="por_pagina_roles" data-cantidad-listado>
                    <?php foreach ([10, 25, 50] as $valor): ?>
                        <option value="<?= $valor ?>" <?= $paginacion['por_pagina'] === $valor ? 'selected' : '' ?>><?= $valor ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <button class="btn btn-filtrar" type="submit">Buscar</button>
            <a class="btn btn-limpiar" href="<?= htmlspecialchars(sigenmuniUrlRuta('usuarios/roles', $contextoUsuarios), ENT_QUOTES, 'UTF-8') ?>">Limpiar</a>
        </form>
        <?php
        $paginacionRuta = 'usuarios/roles';
        $paginacionParametros = $contextoRoles;
        $paginacionEntidad = 'roles';
        $paginacionClavePagina = 'pagina_roles';
        $paginacionClaveCantidad = 'por_pagina_roles';
        require __DIR__ . '/../../../componentes/paginacion.php';
        ?>

        <div class="tabla-responsive">


            <?php if (!empty($roles)): ?>


                <table>


                    <thead>

                        <tr>

                            <th>ID</th>

                            <th>Rol</th>

                            <th>Descripción</th>

                            <th>Tipo</th>

                            <th>Estado</th>

                            <th>Acciones</th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php foreach ($roles as $r): ?>


                        <?php

                        /*
                        |--------------------------------------------------------------------------
                        | ADMIN PRINCIPAL
                        |--------------------------------------------------------------------------
                        */

                        $esAdminPrincipal = (

                            strtoupper(
                                trim(
                                    (string)$r['nombre']
                                )
                            )
                            ===
                            'ADMIN'

                            &&

                            (int)$r['es_admin']
                            ===
                            1                        );


                        $usuariosActivosRol =
                            (int)(
                                $r['usuarios_activos']
                                ??
                                0
                            );

                        ?>


                        <tr>


                            <!-- ID -->

                            <td>

                                <?php
                                echo (int)$r['id'];
                                ?>

                            </td>


                            <!-- ROL -->

                            <td>

                                <strong>

                                    <?php
                                    echo htmlspecialchars(
                                        $r['nombre'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>

                                </strong>

                            </td>


                            <!-- DESCRIPCIÓN -->

                            <td>

                                <?php

                                echo htmlspecialchars(

                                    !empty(
                                        $r['descripcion']
                                    )
                                        ?
                                        $r['descripcion']
                                        :
                                        'Sin descripción cargada.',

                                    ENT_QUOTES,
                                    'UTF-8'
                                );

                                ?>

                            </td>


                            <!-- TIPO -->

                            <td>


                                <?php if (
                                    (int)$r['es_admin']
                                    ===
                                    1
                                ): ?>


                                    <span class="admin">
                                        Administrador
                                    </span>


                                <?php else: ?>


                                    <span class="normal">
                                        Personalizado
                                    </span>


                                <?php endif; ?>


                            </td>


                            <!-- ESTADO -->

                            <td>


                                <?php if (
                                    (int)$r['activo']
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

                            <td>


                                <div class="acciones-tabla">


                                    <!-- EDITAR -->

                                    <a
                                        href="<?php
                                            echo htmlspecialchars(
                                                sigenmuniUrlRuta('usuarios/roles/editar', array_merge($contextoRoles, [
                                                            'id' =>
                                                                (int)$r['id']
                                                        ])),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                        ?>"
                                        class="btn btn-editar"
                                    >
                                        Editar
                                    </a>


                                    <!-- PERMISOS -->

                                    <a
                                        href="<?php
                                            echo htmlspecialchars(
                                                sigenmuniUrlRuta('usuarios/roles/permisos', array_merge($contextoRoles, [
                                                            'id' =>
                                                                (int)$r['id']
                                                        ])),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                        ?>"
                                        class="btn btn-permisos"
                                    >
                                        Permisos
                                    </a>


                                    <!-- ADMIN PROTEGIDO -->

                                    <?php if (
                                        $esAdminPrincipal
                                    ): ?>


                                        <span
                                            class="btn btn-protegido"
                                            title="El rol ADMIN principal no se puede desactivar"
                                        >
                                            Protegido
                                        </span>


                                    <?php else: ?>


                                        <!-- CAMBIAR ESTADO -->

                                        <span
                                            class="usuarios-activos-info"
                                            title="Usuarios activos asignados a este rol"
                                        >
                                            Usuarios activos:
                                            <?php echo $usuariosActivosRol; ?>
                                        </span>


                                        <?php if (
                                            (int)$r['activo'] === 1
                                            &&
                                            $usuariosActivosRol > 0
                                        ): ?>

                                            <span
                                                class="btn btn-protegido"
                                                title="Primero reasigne o inactive los usuarios activos asignados a este rol"
                                            >
                                                No se puede inactivar
                                            </span>


                                        <?php else: ?>

                                            <form
                                                method="POST"
                                                action="<?php
                                                    echo htmlspecialchars(
                                                        $rolesUrlEstado,
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    );
                                                ?>"
                                                class="form-estado-rol"
                                                data-event-submit="roles-1" data-event-value="<?php
                                                        echo (
                                                            (int)$r['activo']
                                                            ===
                                                            1
                                                        )
                                                            ?
                                                            '¿Desea inactivar este rol?'
                                                            :
                                                            '¿Desea activar este rol?';
                                                    ?>"
                                            >

                                                <input
                                                    type="hidden"
                                                    name="id"
                                                    value="<?php echo (int)$r['id']; ?>"
                                                >

                                                <input
                                                    type="hidden"
                                                    name="estado"
                                                    value="<?php
                                                        echo (
                                                            (int)$r['activo']
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
                                                            $rolesCsrfToken,
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
                                                        (int)$r['activo']
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


                                    <?php endif; ?>


                                </div>


                            </td>


                        </tr>


                    <?php endforeach; ?>


                    </tbody>


                </table>


            <?php else: ?>


                <div class="sin-datos">
                    No se encontraron roles con los filtros seleccionados.
                </div>


            <?php endif; ?>


        </div>


        <!-- =================================
             TARJETAS CELULAR
        ================================== -->

        <div class="roles-cards">


            <?php if (!empty($roles)): ?>


                <?php foreach ($roles as $r): ?>


                    <?php

                    $esAdminPrincipal = (

                        strtoupper(
                            trim(
                                (string)$r['nombre']
                            )
                        )
                        ===
                        'ADMIN'

                        &&

                        (int)$r['es_admin']
                        ===
                        1                    );


                    $usuariosActivosRol =
                        (int)(
                            $r['usuarios_activos']
                            ??
                            0
                        );

                    ?>


                    <div class="rol-card">


                        <h3>

                            <?php
                            echo htmlspecialchars(
                                $r['nombre'],
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>

                        </h3>


                        <p>

                            <strong>
                                ID:
                            </strong>

                            <?php
                            echo (int)$r['id'];
                            ?>

                        </p>


                        <p class="descripcion">

                            <?php

                            echo htmlspecialchars(

                                !empty(
                                    $r['descripcion']
                                )
                                    ?
                                    $r['descripcion']
                                    :
                                    'Sin descripción cargada.',

                                ENT_QUOTES,
                                'UTF-8'
                            );

                            ?>

                        </p>


                        <div class="badges">


                            <?php if (
                                (int)$r['es_admin']
                                ===
                                1
                            ): ?>


                                <span class="admin">
                                    Administrador
                                </span>


                            <?php else: ?>


                                <span class="normal">
                                    Personalizado
                                </span>


                            <?php endif; ?>


                            <?php if (
                                (int)$r['activo']
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


                        </div>


                        <div class="acciones-tabla">


                            <a
                                href="<?php
                                            echo htmlspecialchars(
                                                sigenmuniUrlRuta('usuarios/roles/editar', array_merge($contextoRoles, [
                                                            'id' =>
                                                                (int)$r['id']
                                                        ])),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                        ?>"
                                class="btn btn-editar"
                            >
                                Editar
                            </a>


                            <a
                                href="<?php
                                            echo htmlspecialchars(
                                                sigenmuniUrlRuta('usuarios/roles/permisos', array_merge($contextoRoles, [
                                                            'id' =>
                                                                (int)$r['id']
                                                        ])),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                        ?>"
                                class="btn btn-permisos"
                            >
                                Permisos
                            </a>


                            <?php if (
                                $esAdminPrincipal
                            ): ?>


                                <span
                                    class="btn btn-protegido"
                                    title="El rol ADMIN principal no se puede desactivar"
                                >
                                    Protegido
                                </span>


                            <?php else: ?>


                                <span
                                    class="usuarios-activos-info"
                                    title="Usuarios activos asignados a este rol"
                                >
                                    Usuarios activos:
                                    <?php echo $usuariosActivosRol; ?>
                                </span>


                                <?php if (
                                    (int)$r['activo'] === 1
                                    &&
                                    $usuariosActivosRol > 0
                                ): ?>

                                    <span
                                        class="btn btn-protegido"
                                        title="Primero reasigne o inactive los usuarios activos asignados a este rol"
                                    >
                                        No se puede inactivar
                                    </span>


                                <?php else: ?>

                                    <form
                                        method="POST"
                                        action="<?php
                                            echo htmlspecialchars(
                                                $rolesUrlEstado,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                        ?>"
                                        class="form-estado-rol"
                                        data-event-submit="roles-2" data-event-value="<?php
                                                echo (
                                                    (int)$r['activo']
                                                    ===
                                                    1
                                                )
                                                    ?
                                                    '¿Desea inactivar este rol?'
                                                    :
                                                    '¿Desea activar este rol?';
                                            ?>"
                                    >

                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?php echo (int)$r['id']; ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="estado"
                                            value="<?php
                                                echo (
                                                    (int)$r['activo']
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
                                                    $rolesCsrfToken,
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
                                                (int)$r['activo']
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


                            <?php endif; ?>


                        </div>


                    </div>


                <?php endforeach; ?>


            <?php else: ?>


                <div class="sin-datos">
                    No se encontraron roles con los filtros seleccionados.
                </div>


            <?php endif; ?>


        </div>


        <?php if (!empty($roles)): ?>
            <?php require __DIR__ . '/../../../componentes/paginacion.php'; ?>
        <?php endif; ?>

    </div>


</div>


<script src="<?= htmlspecialchars(sigenmuniUrlAsset('js/usuarios/roles.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
<script src="<?= htmlspecialchars(sigenmuniUrlAsset('js/usuarios/paginacion.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
</body>

</html>