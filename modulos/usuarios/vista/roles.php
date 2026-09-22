<?php

/*
|--------------------------------------------------------------------------
| GESTIÓN DE ROLES - NAVEGACIÓN ROUTER
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../../../core/Url.php';
require_once __DIR__ . '/../../../core/Csrf.php';


$rolesUrlNuevo =
    sigenmuniUrlRuta(
        'usuarios/roles/nuevo'
    );


$rolesUrlUsuarios =
    sigenmuniUrlRuta(
        'usuarios'
    );


$rolesUrlEstado =
    sigenmuniUrlRuta(
        'usuarios/roles/estado'
    );


$rolesCsrfToken =
    sigenmuniCsrfToken();

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Gestión de Roles - SIGENMUNI</title>

<style>

/* =========================================
   GENERAL
========================================= */

*{
    box-sizing:border-box;
}

body{
    font-family:Arial,sans-serif;
    background:#f4f7fb;
    margin:0;
    color:#1f2937;
}


/* =========================================
   HEADER
========================================= */

.header{
    background:linear-gradient(
        135deg,
        #0f766e,
        #14b8a6
    );

    color:white;
    padding:22px 30px;

    box-shadow:
        0 4px 14px rgba(0,0,0,.12);
}

.header h1{
    margin:0 0 5px 0;
    font-size:30px;
}

.header p{
    margin:0;
    font-size:15px;
    opacity:.95;
}


/* =========================================
   CONTENEDOR
========================================= */

.contenedor{
    width:96%;
    max-width:1200px;
    margin:30px auto;
}


/* =========================================
   PANEL
========================================= */

.panel{
    background:white;
    border-radius:18px;
    padding:24px;

    box-shadow:
        0 8px 20px rgba(0,0,0,.10);
}


/* =========================================
   CABECERA DEL PANEL
========================================= */

.panel-header{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    gap:15px;
    flex-wrap:wrap;
    margin-bottom:20px;
}

.panel-header h2{
    margin:0 0 6px 0;
    color:#0f172a;
}

.panel-header p{
    margin:0;
    color:#6b7280;
    font-size:14px;
}


/* =========================================
   ACCIONES
========================================= */

.acciones{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
}


/* =========================================
   BOTONES
========================================= */

.btn{
    text-decoration:none;
    padding:10px 14px;
    border-radius:10px;
    font-weight:bold;
    color:white;
    transition:.2s;
    display:inline-block;
    border:none;
    cursor:pointer;
    font-size:14px;
    text-align:center;
}

.btn:hover{
    opacity:.92;
    transform:translateY(-1px);
}

.btn-nuevo{
    background:#0f766e;
}

.btn-volver{
    background:#1f2937;
}

.btn-editar{
    background:#2563eb;
}

.btn-permisos{
    background:#7c3aed;
}

.btn-estado{
    background:#ea580c;
}

.btn-protegido{
    background:#6b7280;
    cursor:not-allowed;
}

.btn-protegido:hover{
    opacity:1;
    transform:none;
}


.usuarios-activos-info{
    display:inline-block;
    padding:8px 10px;
    border-radius:10px;
    background:#fff7ed;
    color:#9a3412;
    border:1px solid #fed7aa;
    font-size:12px;
    font-weight:bold;
    white-space:nowrap;
}


/* =========================================
   ALERTAS
========================================= */

.alerta{
    background:#dcfce7;
    border:1px solid #86efac;
    color:#166534;
    padding:14px;
    border-radius:12px;
    margin-bottom:20px;
    font-weight:bold;
}

.alerta-error{
    background:#fee2e2;
    border:1px solid #fecaca;
    color:#991b1b;
    padding:14px;
    border-radius:12px;
    margin-bottom:20px;
    font-weight:bold;
}


/* =========================================
   TABLA
========================================= */

.tabla-responsive{
    overflow-x:auto;
    border-radius:14px;
    border:1px solid #e5e7eb;
}

table{
    width:100%;
    border-collapse:collapse;
    min-width:850px;
}

th{
    background:#0f766e;
    color:white;
    font-size:14px;
}

th,
td{
    padding:12px;
    border-bottom:1px solid #e5e7eb;
    text-align:left;
    vertical-align:middle;
}

tr:hover td{
    background:#f9fafb;
}


/* =========================================
   ESTADOS
========================================= */

.estado-activo{
    background:#dcfce7;
    color:#166534;
    font-weight:bold;
    padding:6px 10px;
    border-radius:999px;
    display:inline-block;
    font-size:13px;
}

.estado-inactivo{
    background:#fee2e2;
    color:#991b1b;
    font-weight:bold;
    padding:6px 10px;
    border-radius:999px;
    display:inline-block;
    font-size:13px;
}


/* =========================================
   TIPO DE ROL
========================================= */

.admin{
    background:#fef3c7;
    color:#92400e;
    font-weight:bold;
    padding:6px 10px;
    border-radius:999px;
    display:inline-block;
    font-size:13px;
}

.normal{
    background:#e5e7eb;
    color:#374151;
    font-weight:bold;
    padding:6px 10px;
    border-radius:999px;
    display:inline-block;
    font-size:13px;
}


/* =========================================
   ACCIONES DE TABLA
========================================= */

.acciones-tabla{
    display:flex;
    gap:8px;
    flex-wrap:wrap;
}


.form-estado-rol{
    margin:0;
    display:inline-block;
}

.form-estado-rol .btn{
    font-family:inherit;
}


/* =========================================
   TARJETAS PARA CELULAR
========================================= */

.roles-cards{
    display:none;
}

.rol-card{
    background:white;
    border:1px solid #e5e7eb;
    border-radius:16px;
    padding:16px;

    box-shadow:
        0 6px 14px rgba(0,0,0,.08);

    margin-bottom:15px;
}

.rol-card h3{
    margin:0 0 8px 0;
    color:#0f172a;
}

.rol-card p{
    margin:6px 0;
    font-size:14px;
    color:#374151;
    line-height:1.4;
}

.rol-card .descripcion{
    color:#6b7280;
    margin:10px 0;
}

.rol-card .badges{
    display:flex;
    gap:8px;
    flex-wrap:wrap;
    margin:12px 0;
}

.rol-card .acciones-tabla{
    flex-direction:column;
    margin-top:12px;
}

.rol-card .btn{
    width:100%;
    padding:12px;
}


.rol-card .form-estado-rol{
    width:100%;
}

.rol-card .form-estado-rol .btn{
    width:100%;
}


/* =========================================
   SIN DATOS
========================================= */

.sin-datos{
    padding:24px;
    text-align:center;
    color:#64748b;
    font-weight:bold;
}


/* =========================================
   CELULAR
========================================= */

@media(max-width:768px){

    .header{
        padding:18px 16px;
        text-align:center;
    }

    .header h1{
        font-size:25px;
    }

    .contenedor{
        width:94%;
        margin:20px auto;
    }

    .panel{
        padding:18px;
        border-radius:16px;
    }

    .panel-header{
        flex-direction:column;
        text-align:center;
        align-items:stretch;
    }

    .acciones{
        flex-direction:column;
        width:100%;
    }

    .btn{
        width:100%;
        padding:13px;
    }

    .tabla-responsive{
        display:none;
    }

    .roles-cards{
        display:block;
    }

    .alerta,
    .alerta-error{
        font-size:14px;
        text-align:center;
    }
}

</style>

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
                                                sigenmuniUrlRuta(
                                                        'usuarios/roles/editar',
                                                        [
                                                            'id' =>
                                                                (int)$r['id']
                                                        ]
                                                    ),
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
                                                sigenmuniUrlRuta(
                                                        'usuarios/roles/permisos',
                                                        [
                                                            'id' =>
                                                                (int)$r['id']
                                                        ]
                                                    ),
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
                                                onsubmit="return confirm(
                                                    '<?php
                                                        echo (
                                                            (int)$r['activo']
                                                            ===
                                                            1
                                                        )
                                                            ?
                                                            '¿Desea inactivar este rol?'
                                                            :
                                                            '¿Desea activar este rol?';
                                                    ?>'
                                                );"
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
                    No hay roles registrados.
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
                                                sigenmuniUrlRuta(
                                                        'usuarios/roles/editar',
                                                        [
                                                            'id' =>
                                                                (int)$r['id']
                                                        ]
                                                    ),
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
                                                sigenmuniUrlRuta(
                                                        'usuarios/roles/permisos',
                                                        [
                                                            'id' =>
                                                                (int)$r['id']
                                                        ]
                                                    ),
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
                                        onsubmit="return confirm(
                                            '<?php
                                                echo (
                                                    (int)$r['activo']
                                                    ===
                                                    1
                                                )
                                                    ?
                                                    '¿Desea inactivar este rol?'
                                                    :
                                                    '¿Desea activar este rol?';
                                            ?>'
                                        );"
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
                    No hay roles registrados.
                </div>


            <?php endif; ?>


        </div>


    </div>


</div>


</body>

</html>