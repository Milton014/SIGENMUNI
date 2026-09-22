<?php

require_once __DIR__ . '/../../../core/Url.php';
require_once __DIR__ . '/../../../core/Csrf.php';


/*
|--------------------------------------------------------------------------
| NAVEGACIÓN POR FRONT CONTROLLER + ROUTER
|--------------------------------------------------------------------------
*/

$empleadoConceptosUrlListado =
    sigenmuniUrlRuta(
        'empleado-conceptos'
    );


$empleadoConceptosAccionBuscar =
    sigenmuniUrlEntrada();


$empleadoConceptosUrlNuevo =
    sigenmuniUrlRuta(
        'empleado-conceptos/nuevo'
    );


$empleadoConceptosUrlMenu =
    sigenmuniUrlRuta(
    'inicio'
);


$empleadoConceptosUrlEstado =
    sigenmuniUrlRuta(
        'empleado-conceptos/estado'
    );


$empleadoConceptosCsrf =
    sigenmuniCsrfToken();

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Conceptos por Empleado - SIGENMUNI</title>

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
    max-width:1450px;
    margin:30px auto;
}

.panel{
    background:white;
    padding:24px;
    border-radius:18px;
    box-shadow:0 8px 20px rgba(0,0,0,.08);
}


/* =========================================
   TOPBAR
========================================= */

.topbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:15px;
    flex-wrap:wrap;
    margin-bottom:20px;
}

.titulo-seccion h2{
    margin:0;
    color:#0f766e;
    font-size:28px;
}

.titulo-seccion p{
    margin:6px 0 0;
    color:#64748b;
    font-size:14px;
}

.acciones-superiores{
    display:flex;
    gap:8px;
    flex-wrap:wrap;
}


/* =========================================
   BOTONES
========================================= */

.btn{
    display:inline-block;
    padding:10px 14px;
    border:none;
    border-radius:9px;
    text-decoration:none;
    color:white;
    cursor:pointer;
    font-size:13px;
    font-weight:bold;
    transition:.2s;
    text-align:center;
}

.btn:hover{
    opacity:.90;
    transform:translateY(-1px);
}

.btn-nuevo{
    background:#0f766e;
}

.btn-volver{
    background:#1f2937;
}

.btn-buscar{
    background:#2563eb;
}

.btn-limpiar{
    background:#64748b;
}


/* =========================================
   FILTROS
========================================= */

.filtros{
    display:grid;
    grid-template-columns:2fr 1.5fr 1fr auto auto;
    gap:10px;
    margin-bottom:22px;
}

.filtros input,
.filtros select{
    width:100%;
    padding:11px 12px;
    border:1px solid #cbd5e1;
    border-radius:9px;
    font-size:13px;
    outline:none;
    background:white;
}

.filtros input:focus,
.filtros select:focus{
    border-color:#14b8a6;
    box-shadow:0 0 0 3px rgba(20,184,166,.15);
}


/* =========================================
   MENSAJES
========================================= */

.mensaje{
    padding:14px 16px;
    border-radius:10px;
    margin-bottom:20px;
    font-size:14px;
    font-weight:bold;
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
   TABLA
========================================= */

.tabla-contenedor{
    width:100%;
}

table{
    width:100%;
    border-collapse:collapse;
}

th,
td{
    padding:9px 6px;
    border-bottom:1px solid #e5e7eb;
    vertical-align:middle;
    font-size:12px;
}

th{
    background:#0f766e;
    color:white;
    text-align:center;
    white-space:nowrap;
}

td{
    text-align:center;
}

thead th:first-child{
    border-radius:10px 0 0 0;
}

thead th:last-child{
    border-radius:0 10px 0 0;
}

tbody tr{
    transition:background .15s;
}

tbody tr:hover{
    background:#f8fafc;
}


/* =========================================
   COLUMNAS
========================================= */

.col-empleado,
.col-concepto{
    text-align:left;
}

.col-empleado{
    min-width:150px;
}

.col-concepto{
    min-width:160px;
}


.col-empleado{
    width:15%;
}

.col-concepto{
    width:22%;
}

.col-acciones{
    width:1%;
}

.col-numero,
.col-fecha,
.col-estado,
.col-acciones{
    white-space:nowrap;
}


/* =========================================
   ESTADOS
========================================= */

.estado-badge{
    display:inline-block;
    padding:5px 8px;
    border-radius:999px;
    font-size:11px;
    font-weight:bold;
    line-height:1.2;
    white-space:nowrap;
}

.estado-vigente{
    background:#dcfce7;
    color:#166534;
    border:1px solid #86efac;
}

.estado-finalizado{
    background:#e2e8f0;
    color:#475569;
    border:1px solid #cbd5e1;
}

.estado-inactivo{
    background:#fee2e2;
    color:#991b1b;
    border:1px solid #fecaca;
}

.estado-programado{
    background:#dbeafe;
    color:#1d4ed8;
    border:1px solid #93c5fd;
}


/* =========================================
   ACCIONES
========================================= */

.acciones-tabla{
    display:flex;
    gap:6px;
    justify-content:center;
    flex-wrap:wrap;
}

.btn-accion{
    display:inline-block;
    text-decoration:none;
    color:white;
    padding:7px 9px;
    border:none;
    border-radius:7px;
    font-family:inherit;
    font-size:11px;
    font-weight:bold;
    white-space:nowrap;
    transition:.2s;
    cursor:pointer;
}

.acciones-tabla form{
    margin:0;
}

.btn-accion:hover{
    opacity:.86;
    transform:translateY(-1px);
}

.btn-ver{
    background:#2563eb;
}

.btn-editar{
    background:#d97706;
}

.btn-inactivar{
    background:#dc2626;
}

.btn-activar{
    background:#16a34a;
}


/* =========================================
   SIN RESULTADOS
========================================= */

.sin-resultados{
    text-align:center;
    padding:25px;
    color:#64748b;
    font-weight:bold;
}


/* =========================================
   TABLET
========================================= */

@media (max-width:1100px){

    .contenedor{
        width:98%;
    }

    .panel{
        padding:18px;
    }

    th,
    td{
        font-size:11px;
        padding:8px 4px;
    }

    .btn-accion{
        font-size:10px;
        padding:6px;
    }

    .filtros{
        grid-template-columns:1fr 1fr;
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

    .titulo-seccion h2{
        text-align:center;
        font-size:24px;
    }

    .titulo-seccion p{
        text-align:center;
    }

    .acciones-superiores{
        flex-direction:column;
    }

    .acciones-superiores .btn{
        width:100%;
    }

    .filtros{
        grid-template-columns:1fr;
    }

    .filtros .btn{
        width:100%;
    }


    /*
    |--------------------------------------------------------------------------
    | TABLA COMO TARJETAS
    |--------------------------------------------------------------------------
    */

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
        box-shadow:0 4px 12px rgba(0,0,0,.05);
    }

    tbody tr:hover{
        background:white;
    }

    td{
        display:grid;
        grid-template-columns:135px 1fr;
        gap:10px;
        padding:8px 0;
        border-bottom:1px solid #f1f5f9;
        text-align:left;
        font-size:13px;
        white-space:normal;
        min-width:0;
        max-width:none;
    }

    td:last-child{
        border-bottom:none;
    }

    td::before{
        content:attr(data-label);
        font-weight:bold;
        color:#475569;
    }

    .col-empleado,
    .col-concepto,
    .col-numero,
    .col-fecha,
    .col-estado,
    .col-acciones{
        text-align:left;
        min-width:0;
        max-width:none;
        white-space:normal;
    }

    .acciones-tabla{
        flex-direction:column;
        width:100%;
    }

    .btn-accion{
        width:100%;
        padding:9px;
        text-align:center;
        font-size:12px;
    }
}


/* =========================================
   CELULARES PEQUEÑOS
========================================= */

@media (max-width:480px){

    .titulo-seccion h2{
        font-size:22px;
    }

    td{
        grid-template-columns:115px 1fr;
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
    'Gestión de Conceptos por Empleado';

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

            <div class="titulo-seccion">

                <h2>
                    Asignación de Conceptos a Empleados
                </h2>

                <p>
                    Administración de conceptos particulares asignados al personal.
                </p>

            </div>


            <div class="acciones-superiores">

                <a
                    href="<?php
                    echo htmlspecialchars(
                        $empleadoConceptosUrlNuevo,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                    class="btn btn-nuevo"
                >
                    + Nueva Asignación
                </a>


                <a
                    href="<?php
                    echo htmlspecialchars(
                        $empleadoConceptosUrlMenu,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                    class="btn btn-volver"
                >
                    Volver al Menú
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
             FILTROS
        ====================================== -->

        <form
            method="GET"
            action="<?php
                echo htmlspecialchars(
                    $empleadoConceptosAccionBuscar,
                    ENT_QUOTES,
                    'UTF-8'
                );
            ?>"
            class="filtros"
        >

            <input
                type="hidden"
                name="r"
                value="empleado-conceptos"
            >

            <input
                type="text"
                name="buscar_empleado"
                placeholder="Apellido, nombre o legajo exacto"
                value="<?php
                    echo htmlspecialchars(
                        $buscarEmpleado ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
            >


            <select name="concepto_id">

                <option value="">
                    -- Todos los conceptos --
                </option>

                <?php foreach ($conceptos as $concepto): ?>

                    <option
                        value="<?php echo (int)$concepto['id']; ?>"
                        <?php
                        echo (
                            (int)($conceptoId ?? 0)
                            ===
                            (int)$concepto['id']
                        )
                            ? 'selected'
                            : '';
                        ?>
                    >

                        <?php
                        echo htmlspecialchars(
                            $concepto['codigo']
                            . ' - '
                            . $concepto['nombre'],
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>

                    </option>

                <?php endforeach; ?>

            </select>


            <select name="estado">

                <option value="">
                    -- Todos los estados --
                </option>

                <option
                    value="vigente"
                    <?php
                    echo (($estado ?? '') === 'vigente')
                        ? 'selected'
                        : '';
                    ?>
                >
                    Vigentes
                </option>

                <option
                    value="programado"
                    <?php
                    echo (($estado ?? '') === 'programado')
                        ? 'selected'
                        : '';
                    ?>
                >
                    Programados
                </option>

                <option
                    value="finalizado"
                    <?php
                    echo (($estado ?? '') === 'finalizado')
                        ? 'selected'
                        : '';
                    ?>
                >
                    Finalizados por fecha
                </option>

                <option
                    value="inactivo"
                    <?php
                    echo (($estado ?? '') === 'inactivo')
                        ? 'selected'
                        : '';
                    ?>
                >
                    Registros inactivos
                </option>

            </select>


            <button
                type="submit"
                class="btn btn-buscar"
            >
                Buscar
            </button>


            <a
                href="<?php
                    echo htmlspecialchars(
                        $empleadoConceptosUrlListado,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                class="btn btn-limpiar"
            >
                Limpiar
            </a>

        </form>


        <!-- =====================================
             TABLA / TARJETAS
             ID y Observación se conservan internamente,
             pero no se muestran en el listado principal.
        ====================================== -->

        <?php

        /*
        |--------------------------------------------------------------------------
        | FECHA DE REFERENCIA PARA VIGENCIA
        |--------------------------------------------------------------------------
        |
        | Las fechas de empleado_concepto son inclusivas.
        | Una asignación cuya fecha_hasta es hoy continúa vigente durante el día.
        |
        */

        $zonaArgentina =
            new DateTimeZone(
                'America/Argentina/Buenos_Aires'
            );

        $fechaHoy =
            (
                new DateTime(
                    'now',
                    $zonaArgentina
                )
            )->format(
                'Y-m-d'
            );

        ?>

        <?php if (!empty($asignaciones)): ?>

            <div class="tabla-contenedor">

                <table>

                    <thead>

                        <tr>

                            <th>Empleado</th>
                            <th>Legajo</th>
                            <th>Concepto</th>
                            <th>Monto Manual</th>
                            <th>% Manual</th>
                            <th>Cantidad</th>
                            <th>Fecha Desde</th>
                            <th>Fecha Hasta</th>
                            <th>Estado</th>
                            <th>Acciones</th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php foreach ($asignaciones as $fila): ?>

                        <tr>


                            <!-- EMPLEADO -->

                            <td
                                class="col-empleado"
                                data-label="Empleado"
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


                            <!-- LEGAJO -->

                            <td
                                class="col-numero"
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


                            <!-- CONCEPTO -->

                            <td
                                class="col-concepto"
                                data-label="Concepto"
                            >

                                <?php
                                echo htmlspecialchars(
                                    $fila['codigo']
                                    . ' - '
                                    . $fila['concepto'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </td>


                            <!-- MONTO -->

                            <td
                                class="col-numero"
                                data-label="Monto Manual"
                            >

                                $
                                <?php
                                echo number_format(
                                    (float)$fila['monto_manual'],
                                    2,
                                    ',',
                                    '.'
                                );
                                ?>

                            </td>


                            <!-- PORCENTAJE -->

                            <td
                                class="col-numero"
                                data-label="% Manual"
                            >

                                <?php
                                echo number_format(
                                    (float)$fila['porcentaje_manual'],
                                    2,
                                    ',',
                                    '.'
                                );
                                ?>%

                            </td>


                            <!-- CANTIDAD -->

                            <td
                                class="col-numero"
                                data-label="Cantidad"
                            >

                                <?php
                                echo number_format(
                                    (float)$fila['cantidad'],
                                    2,
                                    ',',
                                    '.'
                                );
                                ?>

                            </td>


                            <!-- FECHA DESDE -->

                            <td
                                class="col-fecha"
                                data-label="Fecha Desde"
                            >

                                <?php

                                if (!empty($fila['fecha_desde'])) {

                                    echo date(
                                        'd/m/Y',
                                        strtotime(
                                            $fila['fecha_desde']
                                        )
                                    );

                                } else {

                                    echo '-';
                                }

                                ?>

                            </td>


                            <!-- FECHA HASTA -->

                            <td
                                class="col-fecha"
                                data-label="Fecha Hasta"
                            >

                                <?php

                                if (!empty($fila['fecha_hasta'])) {

                                    echo date(
                                        'd/m/Y',
                                        strtotime(
                                            $fila['fecha_hasta']
                                        )
                                    );

                                } else {

                                    echo '-';
                                }

                                ?>

                            </td>


                            <!-- ESTADO / VIGENCIA -->

                            <td
                                class="col-estado"
                                data-label="Estado"
                            >

                                <?php

                                $registroActivo =
                                    (int)(
                                        $fila['activo']
                                        ?? 0
                                    ) === 1;

                                $fechaDesdeEstado =
                                    trim(
                                        (string)(
                                            $fila['fecha_desde']
                                            ?? ''
                                        )
                                    );

                                $fechaHastaEstado =
                                    trim(
                                        (string)(
                                            $fila['fecha_hasta']
                                            ?? ''
                                        )
                                    );


                                /*
                                |--------------------------------------------------------------------------
                                | ESTADO VISUAL
                                |--------------------------------------------------------------------------
                                |
                                | activo = 0                     -> Inactivo
                                | activo = 1 + fecha_hasta < hoy -> Finalizado por fecha
                                | activo = 1 + fecha_desde > hoy -> Programado
                                | resto                          -> Vigente
                                |
                                */

                                if (!$registroActivo) {

                                    $estadoTexto =
                                        'Inactivo';

                                    $estadoClase =
                                        'estado-inactivo';

                                } elseif (
                                    $fechaHastaEstado !== ''
                                    &&
                                    $fechaHastaEstado < $fechaHoy
                                ) {

                                    $estadoTexto =
                                        'Finalizado por fecha';

                                    $estadoClase =
                                        'estado-finalizado';

                                } elseif (
                                    $fechaDesdeEstado !== ''
                                    &&
                                    $fechaDesdeEstado > $fechaHoy
                                ) {

                                    $estadoTexto =
                                        'Programado';

                                    $estadoClase =
                                        'estado-programado';

                                } else {

                                    $estadoTexto =
                                        'Vigente';

                                    $estadoClase =
                                        'estado-vigente';
                                }

                                ?>

                                <span
                                    class="estado-badge <?php
                                        echo $estadoClase;
                                    ?>"
                                >
                                    <?php
                                    echo htmlspecialchars(
                                        $estadoTexto,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>
                                </span>

                            </td>


                            <!-- ACCIONES -->

                            <td
                                class="col-acciones"
                                data-label="Acciones"
                            >

                                <div class="acciones-tabla">


                                    <!-- VER -->

                                    <a
                                        href="<?php
                                            echo htmlspecialchars(
                                                sigenmuniUrlRuta(
                                                    'empleado-conceptos/ver',
                                                    [
                                                        'id' => (int)$fila['id']
                                                    ]
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                        ?>"
                                        class="btn-accion btn-ver"
                                    >
                                        Ver
                                    </a>


                                    <!-- EDITAR -->

                                    <?php if ($estadoTexto !== 'Finalizado por fecha'): ?>

                                        <a
                                            href="<?php
                                                echo htmlspecialchars(
                                                    sigenmuniUrlRuta(
                                                        'empleado-conceptos/editar',
                                                        [
                                                            'id' => (int)$fila['id']
                                                        ]
                                                    ),
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                );
                                            ?>"
                                            class="btn-accion btn-editar"
                                        >
                                            Editar
                                        </a>

                                    <?php endif; ?>


                                    <!--
                                    =========================================
                                    ACTIVAR / DESACTIVAR REGISTRO
                                    =========================================
                                    El botón modifica activo/inactivo del registro.
                                    La vigencia cronológica se muestra en Estado.
                                    -->

                                    <?php

                                    /*
                                    |--------------------------------------------------------------------------
                                    | ACTIVAR / DESACTIVAR REGISTRO
                                    |--------------------------------------------------------------------------
                                    |
                                    | Si la asignación está FINALIZADA POR FECHA se considera
                                    | histórica e inmutable desde la gestión común.
                                    |
                                    | En ese caso queda disponible solamente:
                                    |
                                    | - Ver
                                    |
                                    | Si el registro está inactivo técnicamente, se conserva
                                    | la opción "Activar registro"; el modelo volverá a validar
                                    | empleado, concepto, vigencia laboral y solapamientos.
                                    |
                                    */

                                    $esFinalizadoPorFecha =
                                        $estadoTexto === 'Finalizado por fecha';

                                    ?>


                                    <?php if (
                                        (int)$fila['activo'] === 1
                                        &&
                                        !$esFinalizadoPorFecha
                                    ): ?>

                                        <form
                                            method="POST"
                                            action="<?php
                                                echo htmlspecialchars(
                                                    $empleadoConceptosUrlEstado,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                );
                                            ?>"
                                            onsubmit="return confirm('¿Seguro que desea desactivar este registro de asignación?');"
                                        >

                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?php echo (int)$fila['id']; ?>"
                                            >

                                            <input
                                                type="hidden"
                                                name="estado"
                                                value="0"
                                            >

                                            <input
                                                type="hidden"
                                                name="_csrf"
                                                value="<?php
                                                    echo htmlspecialchars(
                                                        $empleadoConceptosCsrf,
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    );
                                                ?>"
                                            >

                                            <button
                                                type="submit"
                                                class="btn-accion btn-inactivar"
                                            >
                                                Desactivar registro
                                            </button>

                                        </form>

                                    <?php elseif ((int)$fila['activo'] === 0): ?>

                                        <form
                                            method="POST"
                                            action="<?php
                                                echo htmlspecialchars(
                                                    $empleadoConceptosUrlEstado,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                );
                                            ?>"
                                            onsubmit="return confirm('¿Seguro que desea activar este registro de asignación?');"
                                        >

                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?php echo (int)$fila['id']; ?>"
                                            >

                                            <input
                                                type="hidden"
                                                name="estado"
                                                value="1"
                                            >

                                            <input
                                                type="hidden"
                                                name="_csrf"
                                                value="<?php
                                                    echo htmlspecialchars(
                                                        $empleadoConceptosCsrf,
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    );
                                                ?>"
                                            >

                                            <button
                                                type="submit"
                                                class="btn-accion btn-activar"
                                            >
                                                Activar registro
                                            </button>

                                        </form>

                                    <?php endif; ?>


                                </div>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>


        <?php else: ?>

            <div class="sin-resultados">
                No se encontraron asignaciones.
            </div>

        <?php endif; ?>


    </div>

</div>

</body>

</html>