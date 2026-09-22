<?php

require_once __DIR__ . '/../../../core/Url.php';


/*
|--------------------------------------------------------------------------
| GESTIÓN DE EMPLEADOS - SOLO ROUTER
|--------------------------------------------------------------------------
|
| "empleados.php" se conserva únicamente como clave de permiso.
| Toda la navegación del módulo utiliza las rutas registradas.
|
|--------------------------------------------------------------------------
*/

$empleadosUrlListado =
    sigenmuniUrlRuta(
        'empleados'
    );


$empleadosAccionBuscar =
    sigenmuniUrlEntrada();


$empleadosUrlNuevo =
    sigenmuniUrlRuta(
        'empleados/nuevo'
    );


$empleadosUrlMenu =
    sigenmuniUrlRuta(
    'inicio'
);


if (!function_exists('formatearCuitUnidadOrganizacion')) {

    function formatearCuitUnidadOrganizacion($cuit)
    {
        $cuit =
            preg_replace(
                '/\D+/',
                '',
                (string)$cuit
            );

        if (strlen($cuit) !== 11) {
            return (string)$cuit;
        }

        return
            substr($cuit, 0, 2)
            . '-'
            . substr($cuit, 2, 8)
            . '-'
            . substr($cuit, 10, 1);
    }
}

?>
<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Gestión de Personal - SIGENMUNI</title>

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
    width:97%;
    max-width:1500px;
    margin:30px auto;
}

.panel{
    background:white;

    padding:24px;

    border-radius:18px;

    box-shadow:
        0 8px 20px rgba(0,0,0,0.08);
}


/* =========================================
   TOPBAR
========================================= */

.topbar{
    display:flex;
    justify-content:space-between;
    align-items:center;

    margin-bottom:25px;

    gap:15px;

    flex-wrap:wrap;
}

.topbar h2{
    margin:0;

    color:#0f766e;

    font-size:30px;
}

.acciones-superiores{
    display:flex;
    gap:8px;
    flex-wrap:wrap;
}


/* =========================================
   BOTONES GENERALES
========================================= */

.btn{
    text-decoration:none;

    color:white;

    padding:11px 14px;

    border-radius:10px;

    display:inline-block;

    border:none;

    cursor:pointer;

    font-size:14px;

    font-weight:bold;

    transition:0.2s;
}

.btn:hover{
    opacity:0.92;
    transform:translateY(-1px);
}

.btn-principal{
    background:#0f766e;
}

.btn-sec{
    background:#1f2937;
}

.btn-buscar{
    background:#2563eb;
}


/* =========================================
   MENSAJES
========================================= */

.mensaje{
    padding:14px 16px;

    border-radius:10px;

    margin-bottom:20px;

    font-weight:bold;

    font-size:14px;
}

.mensaje-ok{
    background:#dcfce7;

    color:#166534;

    border:1px solid #86efac;
}

.mensaje-error{
    background:#fee2e2;

    color:#991b1b;

    border:1px solid #fecaca;
}


/* =========================================
   BUSCADOR
========================================= */

.buscador{
    background:#f8fafc;

    padding:18px;

    border-radius:14px;

    margin-bottom:24px;

    border:1px solid #e5e7eb;
}

.buscador form{
    display:flex;

    gap:12px;

    flex-wrap:wrap;
}

input[type="text"]{
    flex:1;

    min-width:250px;

    padding:12px;

    border:1px solid #cbd5e1;

    border-radius:10px;

    font-size:14px;

    outline:none;
}

input[type="text"]:focus{
    border-color:#14b8a6;

    box-shadow:
        0 0 0 3px rgba(20,184,166,0.15);
}


/* =========================================
   TABLA
========================================= */

.tabla-responsive{
    width:100%;

    overflow:visible;
}

table{
    width:100%;

    border-collapse:collapse;

    table-layout:auto;

    background:white;
}

th,
td{
    padding:10px 7px;

    border-bottom:1px solid #e5e7eb;

    text-align:left;

    font-size:12px;

    vertical-align:top;
}

th{
    background:#0f766e;

    color:white;

    font-size:12px;

    white-space:nowrap;
}

thead th:first-child{
    border-radius:10px 0 0 0;
}

thead th:last-child{
    border-radius:0 10px 0 0;
}

tbody tr{
    transition:background 0.15s;
}

tbody tr:hover{
    background:#f8fafc;
}


/* =========================================
   COLUMNAS
========================================= */

.col-legajo{
    white-space:nowrap;
}

.col-nombre{
    min-width:105px;
}

.col-dni{
    white-space:nowrap;
}

.col-cuil{
    white-space:nowrap;
}

.col-email{
    word-break:break-word;
}

.col-fecha{
    white-space:nowrap;
}

.col-estado{
    white-space:nowrap;
}

.col-acciones{
    width:1%;
    white-space:nowrap;
}

.unidad-organizacion{
    min-width:150px;
    line-height:1.35;
}

.unidad-organizacion .nombre-unidad{
    font-weight:bold;
}

.unidad-organizacion .cuit-unidad{
    margin-top:3px;
    color:#64748b;
    font-size:10px;
    white-space:nowrap;
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


/* =========================================
   BOTONES DE ACCIÓN
========================================= */

.acciones{
    display:flex;

    gap:5px;

    flex-wrap:wrap;

    align-items:center;
}

.acciones a{
    text-decoration:none;

    color:white;

    font-weight:bold;

    font-size:11px;

    padding:7px 9px;

    border-radius:7px;

    transition:0.2s;

    white-space:nowrap;
}

.acciones a:hover{
    opacity:0.85;

    transform:translateY(-1px);
}


/* VER */

.btn-ver{
    background:#2563eb;
}


/* EDITAR */

.btn-editar{
    background:#d97706;
}


/* INACTIVAR */

.btn-inactivar{
    background:#dc2626;
}


/* ACTIVAR */

.btn-activar{
    background:#16a34a;
}


/* =========================================
   SIN REGISTROS
========================================= */

.sin-registros{
    background:white;

    padding:20px;

    border-radius:12px;

    color:#666;

    box-shadow:
        0 6px 14px rgba(0,0,0,0.06);
}


/* =========================================
   PANTALLAS MEDIANAS
========================================= */

@media (max-width:1200px){

    .contenedor{
        width:98%;
    }

    .panel{
        padding:18px;
    }

    th,
    td{
        padding:8px 5px;
        font-size:11px;
    }

    th{
        font-size:11px;
    }

    .acciones a{
        font-size:10px;
        padding:6px 7px;
    }
}


/* =========================================
   TABLET
========================================= */

@media (max-width:992px){

    .contenedor{
        width:98%;
    }

    .buscador form{
        flex-direction:column;
        align-items:stretch;
    }

    input[type="text"]{
        width:100%;
    }

    .buscador .btn{
        width:100%;
        text-align:center;
    }

    th,
    td{
        font-size:10px;
        padding:7px 4px;
    }

    .acciones{
        flex-direction:column;
        align-items:stretch;
    }

    .acciones a{
        text-align:center;
        width:100%;
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
        border-radius:16px;
    }

    .topbar{
        flex-direction:column;
        align-items:stretch;
    }

    .topbar h2{
        font-size:24px;
        text-align:center;
    }

    .acciones-superiores{
        flex-direction:column;
    }

    .btn{
        width:100%;
        text-align:center;
    }

    .buscador{
        padding:16px;
    }

    .buscador form{
        flex-direction:column;
        align-items:stretch;
    }

    input[type="text"]{
        width:100%;
        min-width:auto;
    }


    /*
    |--------------------------------------------------------------------------
    | TABLA COMO TARJETAS
    |--------------------------------------------------------------------------
    */

    .tabla-responsive{
        overflow:visible;
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
        gap:16px;
    }

    tbody tr{
        background:white;

        border:1px solid #e5e7eb;

        border-radius:14px;

        padding:14px;

        box-shadow:
            0 4px 12px rgba(0,0,0,0.05);
    }

    tbody tr:hover{
        background:white;
    }

    td{
        display:grid;

        grid-template-columns:130px 1fr;

        gap:10px;

        padding:8px 0;

        border-bottom:1px solid #f1f5f9;

        font-size:13px;

        white-space:normal;
    }

    td:last-child{
        border-bottom:none;
    }

    td::before{
        content:attr(data-label);

        font-weight:bold;

        color:#475569;
    }

    .col-legajo,
    .col-nombre,
    .col-dni,
    .col-cuil,
    .col-email,
    .col-fecha,
    .col-estado,
    .col-acciones{
        width:auto;
        min-width:0;
        white-space:normal;
    }

    .acciones{
        display:flex;

        flex-direction:column;

        gap:7px;

        width:100%;
    }

    .acciones a{
        width:100%;

        text-align:center;

        font-size:12px;

        padding:9px;
    }
}


/* =========================================
   CELULARES PEQUEÑOS
========================================= */

@media (max-width:480px){

    .topbar h2{
        font-size:22px;
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
    'Gestión de Empleados Municipales';

require __DIR__
    . '/../../../componentes/header_sigenmuni.php';

?>


<!-- =========================================
     CONTENIDO
========================================= -->

<div class="contenedor">

    <div class="panel">


        <!-- =====================================
             TOPBAR
        ====================================== -->

        <div class="topbar">

            <h2>
                Gestión de Personal
            </h2>


            <div class="acciones-superiores">

                <a
                    href="<?php
                    echo htmlspecialchars(
                        $empleadosUrlNuevo,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                    class="btn btn-principal"
                >
                    + Nuevo Empleado
                </a>


                <a
                    href="<?php
                    echo htmlspecialchars(
                        $empleadosUrlMenu,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                    class="btn btn-sec"
                >
                    Volver al menú
                </a>

            </div>

        </div>


        <!-- =====================================
             MENSAJES
        ====================================== -->

        <?php if (!empty($mensaje)): ?>

            <div
                class="mensaje <?php
                    echo $tipo_mensaje === 'ok'
                        ? 'mensaje-ok'
                        : 'mensaje-error';
                ?>"
            >

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
             BUSCADOR
        ====================================== -->

        <div class="buscador">

            <form
                method="GET"
                action="<?php
                    echo htmlspecialchars(
                        $empleadosAccionBuscar,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
            >

                <input
                    type="hidden"
                    name="r"
                    value="empleados"
                >

                <input
                    type="text"
                    name="busqueda"
                    placeholder="Buscar por legajo, apellido, nombre, DNI, CUIL o email"
                    value="<?php
                        echo htmlspecialchars(
                            $busqueda ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                >


                <button
                    type="submit"
                    class="btn btn-buscar"
                >
                    Buscar
                </button>


                <a
                    href="<?php
                    echo htmlspecialchars(
                        $empleadosUrlListado,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                    class="btn btn-sec"
                >
                    Limpiar
                </a>

            </form>

        </div>


        <!-- =====================================
             TABLA DE EMPLEADOS
        ====================================== -->

        <?php if (!empty($empleados)): ?>

            <div class="tabla-responsive">

                <table>

                    <thead>

                        <tr>

                            <th>
                                Legajo
                            </th>

                            <th>
                                Apellido y Nombre
                            </th>

                            <th>
                                DNI
                            </th>

                            <th>
                                CUIL
                            </th>

                            <th>
                                Teléfono
                            </th>

                            <th>
                                Email
                            </th>

                            <th>
                                Fecha Alta
                            </th>

                            <th>
                                Fecha Inactivo
                            </th>

                            <th>
                                Categoría
                            </th>

                            <th>
                                Unidad de Organización
                            </th>

                            <th>
                                Situación
                            </th>

                            <th>
                                Estado
                            </th>

                            <th>
                                Acciones
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php foreach ($empleados as $fila): ?>

                        <tr>


                            <!-- =================================
                                 LEGAJO
                            ================================== -->

                            <td
                                class="col-legajo"
                                data-label="Legajo"
                            >

                                <?php
                                echo htmlspecialchars(
                                    $fila['nro_legajo'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </td>


                            <!-- =================================
                                 APELLIDO Y NOMBRE
                            ================================== -->

                            <td
                                class="col-nombre"
                                data-label="Apellido y Nombre"
                            >

                                <?php
                                echo htmlspecialchars(
                                    $fila['apellido']
                                    . ', '
                                    . $fila['nombre'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </td>


                            <!-- =================================
                                 DNI
                            ================================== -->

                            <td
                                class="col-dni"
                                data-label="DNI"
                            >

                                <?php
                                echo htmlspecialchars(
                                    $fila['dni'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </td>


                            <!-- =================================
                                 CUIL
                            ================================== -->

                            <td
                                class="col-cuil"
                                data-label="CUIL"
                            >

                                <?php
                                echo htmlspecialchars(
                                    $fila['cuil'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </td>


                            <!-- =================================
                                 TELÉFONO
                            ================================== -->

                            <td data-label="Teléfono">

                                <?php
                                echo htmlspecialchars(
                                    !empty($fila['telefono'])
                                        ? $fila['telefono']
                                        : '-',
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </td>


                            <!-- =================================
                                 EMAIL
                            ================================== -->

                            <td
                                class="col-email"
                                data-label="Email"
                            >

                                <?php
                                echo htmlspecialchars(
                                    !empty($fila['email'])
                                        ? $fila['email']
                                        : '-',
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </td>


                            <!-- =================================
                                 FECHA ALTA
                            ================================== -->

                            <td
                                class="col-fecha"
                                data-label="Fecha Alta"
                            >

                                <?php

                                if (!empty($fila['fecha_alta'])) {

                                    echo htmlspecialchars(
                                        date(
                                            'd/m/Y',
                                            strtotime(
                                                $fila['fecha_alta']
                                            )
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );

                                } else {

                                    echo '-';
                                }

                                ?>

                            </td>


                            <!-- =================================
                                 FECHA INACTIVO
                            ================================== -->

                            <td
                                class="col-fecha"
                                data-label="Fecha Inactivo"
                            >

                                <?php

                                if (!empty($fila['fecha_inactivo'])) {

                                    echo htmlspecialchars(
                                        date(
                                            'd/m/Y',
                                            strtotime(
                                                $fila['fecha_inactivo']
                                            )
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );

                                } else {

                                    echo '-';
                                }

                                ?>

                            </td>


                            <!-- =================================
                                 CATEGORÍA
                            ================================== -->

                            <td data-label="Categoría">

                                <?php
                                echo htmlspecialchars(
                                    $fila['categoria'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </td>


                            <!-- =================================
                                 UNIDAD DE ORGANIZACIÓN
                            ================================== -->

                            <td
                                class="unidad-organizacion"
                                data-label="Unidad de Organización"
                            >

                                <div class="nombre-unidad">

                                    <?php
                                    echo htmlspecialchars(
                                        $fila['oficina']
                                            ?? '-',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>

                                </div>

                                <?php if (!empty($fila['oficina_cuit'])): ?>

                                    <div class="cuit-unidad">

                                        CUIT:
                                        <?php
                                        echo htmlspecialchars(
                                            formatearCuitUnidadOrganizacion(
                                                $fila['oficina_cuit']
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        );
                                        ?>

                                    </div>

                                <?php endif; ?>

                            </td>


                            <!-- =================================
                                 SITUACIÓN
                            ================================== -->

                            <td data-label="Situación">

                                <?php
                                echo htmlspecialchars(
                                    $fila['situacion'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </td>


                            <!-- =================================
                                 ESTADO
                            ================================== -->

                            <td
                                class="col-estado"
                                data-label="Estado"
                            >

                                <?php if ((int)$fila['activo'] === 1): ?>

                                    <span class="estado-activo">
                                        Activo
                                    </span>

                                <?php else: ?>

                                    <span class="estado-inactivo">
                                        Inactivo
                                    </span>

                                <?php endif; ?>

                            </td>


                            <!-- =================================
                                 ACCIONES
                            ================================== -->

                            <td
                                class="col-acciones"
                                data-label="Acciones"
                            >

                                <div class="acciones">


                                    <!-- VER -->

                                    <a
                                        href="<?php
                                            echo htmlspecialchars(
                                                sigenmuniUrlRuta(
                                                    'empleados/ver',
                                                    [
                                                        'id' => (int)$fila['id']
                                                    ]
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                        ?>"
                                        class="btn-ver"
                                    >
                                        Ver
                                    </a>


                                    <!-- EDITAR -->

                                    <a
                                        href="<?php
                                            echo htmlspecialchars(
                                                sigenmuniUrlRuta(
                                                    'empleados/editar',
                                                    [
                                                        'id' => (int)$fila['id']
                                                    ]
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                        ?>"
                                        class="btn-editar"
                                    >
                                        Editar
                                    </a>


                                    <!-- ACTIVAR / INACTIVAR -->

                                    <?php if ((int)$fila['activo'] === 1): ?>

                                        <a
                                            href="<?php
                                                echo htmlspecialchars(
                                                    sigenmuniUrlRuta(
                                                        'empleados/estado',
                                                        [
                                                            'id' => (int)$fila['id'],
                                                            'accion' => 'inactivar'
                                                        ]
                                                    ),
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                );
                                            ?>"
                                            class="btn-inactivar"
                                            onclick="return confirm('¿Desea inactivar este empleado?');"
                                        >
                                            Inactivar
                                        </a>

                                    <?php else: ?>

                                        <a
                                            href="<?php
                                                echo htmlspecialchars(
                                                    sigenmuniUrlRuta(
                                                        'empleados/estado',
                                                        [
                                                            'id' => (int)$fila['id'],
                                                            'accion' => 'activar'
                                                        ]
                                                    ),
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                );
                                            ?>"
                                            class="btn-activar"
                                            onclick="return confirm('¿Desea activar este empleado?');"
                                        >
                                            Activar
                                        </a>

                                    <?php endif; ?>

                                </div>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>


        <?php else: ?>


            <!-- =================================
                 SIN EMPLEADOS
            ================================== -->

            <div class="sin-registros">

                No se encontraron empleados cargados.

            </div>


        <?php endif; ?>


    </div>

</div>


</body>

</html>