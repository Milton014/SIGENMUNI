<?php

/*
|--------------------------------------------------------------------------
| GESTIÓN DE USUARIOS - NAVEGACIÓN ROUTER
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../../../core/Url.php';
require_once __DIR__ . '/../../../core/Csrf.php';


$usuariosUrlNuevo =
    sigenmuniUrlRuta(
        'usuarios/nuevo'
    );


$usuariosUrlRoles =
    sigenmuniUrlRuta(
        'usuarios/roles'
    );


$usuariosUrlMenu =
    sigenmuniUrlRuta(
    'inicio'
);


$usuariosUrlEstado =
    sigenmuniUrlRuta(
        'usuarios/estado'
    );


$usuariosCsrfToken =
    sigenmuniCsrfToken();

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Gestión de Usuarios - SIGENMUNI</title>

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
   CONTENEDOR
========================================= */

.contenedor{
    width:96%;
    max-width:1250px;
    margin:30px auto;
}


/* =========================================
   PANEL
========================================= */

.panel{
    background:white;
    padding:24px;
    border-radius:18px;

    box-shadow:
        0 8px 20px rgba(0,0,0,.10);

    margin-bottom:25px;
}

.panel h2{
    margin-top:0;
    margin-bottom:14px;
    color:#111827;
}


/* =========================================
   ACCIONES SUPERIORES
========================================= */

.acciones-superiores{
    margin-bottom:18px;

    display:flex;
    gap:10px;
    flex-wrap:wrap;
}


/* =========================================
   BOTONES
========================================= */

.btn{
    padding:10px 14px;
    border-radius:10px;
    border:none;
    text-decoration:none;
    font-weight:bold;
    cursor:pointer;
    display:inline-block;
    font-size:14px;
    transition:.2s;
}

.btn:hover{
    opacity:.92;
    transform:translateY(-1px);
}

.btn-nuevo{
    background:#0f766e;
    color:white;
}

.btn-volver{
    background:#1f2937;
    color:white;
}

.btn-editar{
    background:#2563eb;
    color:white;
}

.btn-estado{
    background:#ea580c;
    color:white;
}

.btn-roles{
    background:#7c3aed;
    color:white;
}

.btn-protegido{
    background:#6b7280;
    color:white;
    cursor:not-allowed;
}

.btn-protegido:hover{
    opacity:1;
    transform:none;
}


/* =========================================
   ALERTAS
========================================= */

.alerta-ok{
    background:#dcfce7;
    color:#166534;
    border:1px solid #86efac;
    padding:14px;
    border-radius:12px;
    margin-bottom:18px;
    font-weight:bold;
}

.alerta-error{
    background:#fee2e2;
    color:#991b1b;
    border:1px solid #fecaca;
    padding:14px;
    border-radius:12px;
    margin-bottom:18px;
    font-weight:bold;
}


/* =========================================
   INFO
========================================= */

.info{
    background:#eff6ff;
    border:1px solid #bfdbfe;
    color:#1e40af;
    padding:14px;
    border-radius:12px;
    margin-bottom:18px;
    line-height:1.5;
}


/* =========================================
   TABLA
========================================= */

.tabla-responsive{
    width:100%;
    overflow-x:auto;
}

.tabla-responsive table{
    min-width:950px;
}

table{
    border-collapse:collapse;
    width:100%;
    background:white;
    overflow:hidden;
    border-radius:14px;
    margin-top:15px;
}

th,
td{
    border-bottom:1px solid #e5e7eb;
    padding:12px;
    text-align:left;
    font-size:14px;
    vertical-align:middle;
}

th{
    background:#0f766e;
    color:white;
    white-space:nowrap;
}

tbody tr{
    transition:.15s;
}

tbody tr:hover{
    background:#f8fafc;
}


/* =========================================
   ESTADOS
========================================= */

.estado-activo{
    color:#166534;
    font-weight:bold;
}

.estado-inactivo{
    color:#991b1b;
    font-weight:bold;
}

.rol-inactivo{
    color:#92400e;
    font-weight:bold;
}


/* =========================================
   ROL ADMIN
========================================= */

.rol-admin{
    display:inline-block;

    background:#dcfce7;
    color:#166534;

    padding:4px 8px;
    border-radius:999px;

    font-size:11px;
    font-weight:bold;
}


/* =========================================
   ACCIONES DE FILA
========================================= */

.acciones-fila{
    display:flex;
    gap:6px;
    flex-wrap:wrap;
}


.form-estado{
    margin:0;
    display:inline-block;
}

.form-estado .btn{
    font-family:inherit;
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
   TABLET
========================================= */

@media (max-width:1000px){

    th,
    td{
        font-size:12px;
        padding:10px 7px;
    }
}


/* =========================================
   CELULAR
========================================= */

@media (max-width:768px){

    .contenedor{
        width:94%;
        margin:20px auto;
    }

    .panel{
        padding:18px;
        border-radius:14px;
    }

    .panel h2{
        font-size:22px;
        margin-bottom:15px;
        text-align:center;
    }

    .acciones-superiores{
        flex-direction:column;
        align-items:stretch;
    }

    .acciones-superiores .btn{
        width:100%;
        text-align:center;
    }

    .alerta-ok,
    .alerta-error,
    .info{
        font-size:14px;
    }


    /*
    =========================================
    TABLA COMO TARJETAS
    =========================================
    */

    .tabla-responsive{
        overflow:visible;
    }

    .tabla-responsive table{
        min-width:0;
    }

    table,
    thead,
    tbody,
    tr,
    th,
    td{
        display:block;
        width:100%;
    }

    thead{
        display:none;
    }

    tbody{
        display:grid;
        gap:14px;
    }

    tbody tr{
        border:1px solid #e2e8f0;
        border-radius:14px;
        padding:14px;
        background:#fff;

        box-shadow:
            0 4px 10px rgba(0,0,0,.04);
    }

    td{
        display:grid;
        grid-template-columns:130px 1fr;
        gap:10px;
        padding:8px 0;
        border-bottom:1px solid #f1f5f9;
        text-align:left;
    }

    td:last-child{
        border-bottom:none;
    }

    td::before{
        content:attr(data-label);
        font-weight:bold;
        color:#475569;
    }

    .acciones-fila{
        flex-direction:column;
    }

    .acciones-fila .btn{
        width:100%;
        text-align:center;
    }


    .acciones-fila .form-estado{
        width:100%;
    }

    .acciones-fila .form-estado .btn{
        width:100%;
    }
}


/* =========================================
   CELULAR PEQUEÑO
========================================= */

@media (max-width:480px){

    .panel h2{
        font-size:19px;
    }

    .btn{
        font-size:13px;
        padding:10px;
    }

    td{
        grid-template-columns:105px 1fr;
        font-size:12px;
    }
}

</style>

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
                                                sigenmuniUrlRuta(
                                                        'usuarios/editar',
                                                        [
                                                            'id' =>
                                                                (int)$u['id']
                                                        ]
                                                    ),
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
                                                onsubmit="return confirm(
                                                    '<?php
                                                        echo (
                                                            (int)$u['activo']
                                                            ===
                                                            1
                                                        )
                                                            ?
                                                            '¿Está seguro de inactivar este usuario?'
                                                            :
                                                            '¿Está seguro de activar este usuario?';
                                                    ?>'
                                                );"
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

                    No hay usuarios registrados.

                </div>


            <?php endif; ?>


        </div>


    </div>


</div>


</body>

</html>