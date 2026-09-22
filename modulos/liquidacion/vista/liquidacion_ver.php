<?php

require_once __DIR__ . '/../../../core/Url.php';
require_once __DIR__ . '/../../../core/Csrf.php';


/*
|--------------------------------------------------------------------------
| VER LIQUIDACIÓN - ROUTER
|--------------------------------------------------------------------------
*/

$liquidacionVerId =
    (int)(
        $liquidacion['id']
        ?? $liquidacionId
        ?? 0
    );


$liquidacionVerVolver =
    sigenmuniUrlRuta(
        'liquidacion'
    );


$liquidacionVerCerrarDetalle =
    sigenmuniUrlRuta(
        'liquidacion/ver',
        [
            'id' =>
                $liquidacionVerId
        ]
    );


$liquidacionVerProcesar =
    sigenmuniUrlRuta(
        'liquidacion/procesar'
    );


$liquidacionVerRecibosTodos =
    sigenmuniUrlRuta(
        'liquidacion/recibos',
        [
            'liquidacion_id' =>
                $liquidacionVerId
        ]
    );


$liquidacionVerCsrf =
    sigenmuniCsrfToken();


$liquidacionVerUrlDetalle =
    function (
        $empleadoId
    ) use (
        $liquidacionVerId
    ) {

        $parametros = [
            'id' =>
                $liquidacionVerId,

            'empleado_id' =>
                (int)$empleadoId
        ];


        return
            sigenmuniUrlRuta(
                'liquidacion/ver',
                $parametros
            );
    };


$liquidacionVerUrlRecibo =
    function (
        $empleadoId
    ) use (
        $liquidacionVerId
    ) {

        return
            sigenmuniUrlRuta(
                'liquidacion/recibo',
                [
                    'liquidacion_id' =>
                        $liquidacionVerId,

                    'empleado_id' =>
                        (int)$empleadoId
                ]
            );
    };

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Ver Liquidación - SIGENMUNI</title>

<style>

/* =========================================
   GENERAL
========================================= */

*{
    box-sizing:border-box;
}

body{
    margin:0;
    font-family:Arial,sans-serif;
    background:#f4f7fb;
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
        0 4px 14px rgba(0,0,0,.10);
}

.header h1{
    margin:0;
    font-size:30px;
}

.header p{
    margin:6px 0 0;
    font-size:14px;
}


/* =========================================
   CONTENEDOR
========================================= */

.contenedor{
    width:96%;
    max-width:1350px;
    margin:30px auto;
}


/* =========================================
   PANEL
========================================= */

.panel{
    background:white;
    padding:25px;
    border-radius:18px;
    box-shadow:0 8px 20px rgba(0,0,0,.08);
}


/* =========================================
   TÍTULOS
========================================= */

h2,
h3{
    color:#0f766e;
}

h2{
    margin-top:0;
    margin-bottom:20px;
    font-size:28px;
}

h3{
    margin-top:28px;
    margin-bottom:15px;
    font-size:21px;
}


/* =========================================
   BOTONES
========================================= */

.acciones-superiores{
    display:flex;
    gap:9px;
    flex-wrap:wrap;
    margin-bottom:24px;
}

.form-accion-inline{
    margin:0;
    padding:0;
}

.btn{
    display:inline-block;
    padding:10px 14px;
    text-decoration:none;
    border:none;
    border-radius:9px;
    cursor:pointer;
    color:white;
    font-size:13px;
    font-weight:bold;
    text-align:center;
    transition:.2s;
}

.btn:hover{
    opacity:.92;
    transform:translateY(-1px);
}

.btn-volver{
    background:#1f2937;
}

.btn-procesar{
    background:#16a34a;
}

.btn-imprimir-todos{
    background:#ea580c;
}

.btn-detalle{
    background:#2563eb;
}

.btn-recibo{
    background:#0f766e;
}

.btn-cerrar-detalle{
    background:#64748b;
}


/* =========================================
   CABECERA LIQUIDACIÓN
========================================= */

.cabecera{
    display:grid;
    grid-template-columns:
        repeat(
            auto-fit,
            minmax(190px,1fr)
        );

    gap:12px;
    margin-bottom:24px;
}

.card{
    background:#f8fafc;
    border:1px solid #e2e8f0;
    border-radius:12px;
    padding:15px;
    word-break:break-word;
}

.card strong{
    display:block;
    margin-bottom:7px;
    color:#475569;
    font-size:13px;
}

.card .valor{
    font-size:15px;
    color:#1e293b;
}


/* =========================================
   BADGES ESTADO
========================================= */

.badge{
    display:inline-block;
    padding:6px 10px;
    border-radius:999px;
    font-size:11px;
    font-weight:bold;
}

.estado-borrador{
    background:#fef3c7;
    color:#92400e;
}

.estado-cerrada{
    background:#dcfce7;
    color:#166534;
}

.estado-anulada{
    background:#fee2e2;
    color:#991b1b;
}


/* =========================================
   TOTALES
========================================= */

.totales-grid{
    display:grid;

    grid-template-columns:
        repeat(
            auto-fit,
            minmax(180px,1fr)
        );

    gap:12px;
    margin-bottom:28px;
}

.total-box{
    background:#ecfeff;
    border:1px solid #a5f3fc;
    border-radius:12px;
    padding:15px;
}

.total-box strong{
    display:block;
    margin-bottom:7px;
    color:#0f766e;
    font-size:13px;
}

.total-box .importe{
    font-size:18px;
    font-weight:bold;
    color:#164e63;
}

.total-neto{
    background:#dcfce7;
    border-color:#86efac;
}

.total-neto strong,
.total-neto .importe{
    color:#166534;
}


/* =========================================
   TABLAS
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
    padding:11px 8px;
    border-bottom:1px solid #e5e7eb;
    font-size:13px;
    vertical-align:middle;
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
    transition:.15s;
}

tbody tr:hover{
    background:#f8fafc;
}


/* =========================================
   IMPORTES
========================================= */

.importe-positivo{
    font-weight:bold;
    color:#166534;
}

.importe-descuento{
    color:#b91c1c;
}

.importe-neto{
    font-weight:bold;
    color:#0f766e;
}


/* =========================================
   ACCIONES TABLA
========================================= */

.acciones-botones{
    display:flex;
    justify-content:center;
    gap:6px;
    flex-wrap:wrap;
}


/* =========================================
   SIN DATOS
========================================= */

.sin-datos{
    text-align:center;
    padding:24px;
    color:#64748b;
    font-weight:bold;
}


/* =========================================
   DETALLE EMPLEADO
========================================= */

.detalle-panel{
    margin-top:32px;
    padding:22px;
    background:#f8fafc;
    border:1px solid #e2e8f0;
    border-radius:16px;
}

.detalle-cabecera{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:10px;
    flex-wrap:wrap;
    margin-bottom:16px;
}

.detalle-cabecera h3{
    margin:0;
}

.empleado-info{
    margin-top:5px;
    color:#64748b;
    font-size:13px;
}


/* =========================================
   ORIGEN DEL CONCEPTO
========================================= */

.etiqueta{
    display:inline-block;
    padding:5px 8px;
    border-radius:999px;
    font-size:10px;
    font-weight:bold;
}

.etiqueta-manual{
    background:#ffedd5;
    color:#9a3412;
}

.etiqueta-auto{
    background:#dcfce7;
    color:#166534;
}

.fila-manual{
    background:#fff7ed;
}

.fila-manual:hover{
    background:#ffedd5;
}


/* =========================================
   CATEGORÍAS CONCEPTO
========================================= */

.categoria{
    display:inline-block;
    padding:5px 8px;
    border-radius:999px;
    font-size:10px;
    font-weight:bold;
}

.cat-remunerativo{
    background:#dbeafe;
    color:#1d4ed8;
}

.cat-no-remunerativo{
    background:#e0e7ff;
    color:#4338ca;
}

.cat-asignacion{
    background:#dcfce7;
    color:#166534;
}

.cat-descuento{
    background:#fee2e2;
    color:#991b1b;
}

.cat-patronal{
    background:#f3e8ff;
    color:#7e22ce;
}

.cat-otro{
    background:#e5e7eb;
    color:#374151;
}


/* =========================================
   TABLET
========================================= */

@media (max-width:1000px){

    th,
    td{
        font-size:12px;
        padding:9px 6px;
    }
}


/* =========================================
   CELULAR
========================================= */

@media (max-width:768px){

    .header{
        padding:20px;
        text-align:center;
    }

    .header h1{
        font-size:24px;
    }

    .contenedor{
        width:94%;
        margin:20px auto;
    }

    .panel{
        padding:18px;
        border-radius:16px;
    }

    h2{
        font-size:24px;
        text-align:center;
    }

    h3{
        font-size:20px;
        text-align:center;
    }

    .acciones-superiores{
        flex-direction:column;
    }

    .acciones-superiores .btn{
        width:100%;
    }

    .acciones-superiores .form-accion-inline{
        width:100%;
    }

    .cabecera{
        grid-template-columns:1fr;
    }

    .card{
        text-align:center;
    }

    .totales-grid{
        grid-template-columns:1fr;
    }

    .total-box{
        text-align:center;
    }

    /*
    =========================================
    TABLAS COMO TARJETAS
    =========================================
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
        gap:15px;
    }

    tbody tr{
        background:white;
        border:1px solid #e2e8f0;
        border-radius:14px;
        padding:13px;
        box-shadow:0 4px 10px rgba(0,0,0,.04);
    }

    tbody tr:hover{
        background:white;
    }

    td{
        display:grid;
        grid-template-columns:125px 1fr;
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

    .acciones-botones{
        flex-direction:column;
    }

    .acciones-botones .btn{
        width:100%;
    }

    .detalle-panel{
        padding:15px;
    }

    .detalle-cabecera{
        flex-direction:column;
    }

    .detalle-cabecera .btn{
        width:100%;
    }

    .empleado-info{
        text-align:center;
    }
}


/* =========================================
   CELULAR PEQUEÑO
========================================= */

@media (max-width:480px){

    .header h1{
        font-size:22px;
    }

    h2{
        font-size:22px;
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
     HEADER
========================================= -->

<div class="header">

    <h1>
        SIGENMUNI
    </h1>

    <p>
        Detalle de Liquidación
    </p>

</div>


<div class="contenedor">

    <div class="panel">


        <!-- =====================================
             ACCIONES
        ====================================== -->

        <div class="acciones-superiores">


            <a
                href="<?php
                    echo htmlspecialchars(
                        $liquidacionVerVolver,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                class="btn btn-volver"
            >
                Volver al Listado
            </a>
            <?php if (
                $liquidacion['estado']
                ===
                'BORRADOR'
            ): ?>


                <form
                    action="<?php
                        echo htmlspecialchars(
                            $liquidacionVerProcesar,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                    method="POST"
                    class="form-accion-inline"
                    onsubmit="return confirm(
                        '¿Seguro que desea procesar esta liquidación? Una vez cerrada no podrá modificarse.'
                    );"
                >

                    <input
                        type="hidden"
                        name="id"
                        value="<?php
                            echo (int)$liquidacionVerId;
                        ?>"
                    >

                    <input
                        type="hidden"
                        name="_csrf"
                        value="<?php
                            echo htmlspecialchars(
                                $liquidacionVerCsrf,
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                    >

                    <button
                        type="submit"
                        class="btn btn-procesar"
                    >
                        Procesar Liquidación
                    </button>

                </form>


            <?php endif; ?>


            <?php if (
                $liquidacion['estado'] === 'CERRADA'
                ||
                $liquidacion['estado'] === 'ANULADA'
            ): ?>


                <a
                    href="<?php
                        echo htmlspecialchars(
                            $liquidacionVerRecibosTodos,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                    class="btn btn-imprimir-todos"
                >
                    Imprimir todos los recibos
                </a>


            <?php endif; ?>


        </div>


        <!-- =====================================
             TÍTULO
        ====================================== -->

        <h2>

            Liquidación #

            <?php
            echo (int)$liquidacion['id'];
            ?>

        </h2>


        <!-- =====================================
             CABECERA
        ====================================== -->

        <div class="cabecera">


            <!-- TIPO -->

            <div class="card">

                <strong>
                    Tipo
                </strong>

                <div class="valor">

                    <?php
                    echo htmlspecialchars(
                        $liquidacion['tipo_liquidacion'],
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>

                </div>

            </div>


            <!-- PERÍODO -->

            <div class="card">

                <strong>
                    Período
                </strong>

                <div class="valor">

                    <?php
                    echo htmlspecialchars(
                        $liquidacion['periodo'],
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>

                </div>

            </div>


            <!-- FECHA -->

            <div class="card">

                <strong>
                    Fecha de Liquidación
                </strong>

                <div class="valor">

                    <?php

                    echo !empty(
                        $liquidacion['fecha_liquidacion']
                    )
                        ?
                        date(
                            'd/m/Y',
                            strtotime(
                                $liquidacion['fecha_liquidacion']
                            )
                        )
                        :
                        '-';

                    ?>

                </div>

            </div>


            <!-- ESTADO -->

            <div class="card">

                <strong>
                    Estado
                </strong>


                <?php

                $estadoLiquidacion =
                    $liquidacion['estado'];


                $claseEstado =
                    'estado-borrador';


                if (
                    $estadoLiquidacion
                    ===
                    'CERRADA'
                ) {

                    $claseEstado =
                        'estado-cerrada';

                } elseif (
                    $estadoLiquidacion
                    ===
                    'ANULADA'
                ) {

                    $claseEstado =
                        'estado-anulada';
                }

                ?>


                <span
                    class="badge <?php
                        echo $claseEstado;
                    ?>"
                >

                    <?php
                    echo htmlspecialchars(
                        $estadoLiquidacion,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>

                </span>

            </div>


            <!-- DESCRIPCIÓN -->

            <div class="card">

                <strong>
                    Descripción
                </strong>

                <div class="valor">

                    <?php
                    echo htmlspecialchars(
                        !empty(
                            $liquidacion['descripcion']
                        )
                            ?
                            $liquidacion['descripcion']
                            :
                            '-',
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>

                </div>

            </div>


            <!-- CREADA -->

            <div class="card">

                <strong>
                    Creada
                </strong>

                <div class="valor">

                    <?php

                    echo !empty(
                        $liquidacion['created_at']
                    )
                        ?
                        date(
                            'd/m/Y H:i',
                            strtotime(
                                $liquidacion['created_at']
                            )
                        )
                        :
                        '-';

                    ?>

                </div>

            </div>


        </div>


        <!-- =====================================
             TOTALES GENERALES
        ====================================== -->

        <h3>
            Totales Generales
        </h3>


        <div class="totales-grid">


            <div class="total-box">

                <strong>
                    Empleados
                </strong>

                <div class="importe">

                    <?php
                    echo (int)(
                        $totales['cantidad_empleados']
                        ?? 0
                    );
                    ?>

                </div>

            </div>


            <div class="total-box">

                <strong>
                    Total Remunerativo
                </strong>

                <div class="importe">

                    $
                    <?php
                    echo number_format(
                        (float)(
                            $totales['total_remunerativo']
                            ?? 0
                        ),
                        2,
                        ',',
                        '.'
                    );
                    ?>

                </div>

            </div>


            <div class="total-box">

                <strong>
                    Total Descuentos
                </strong>

                <div class="importe">

                    $
                    <?php
                    echo number_format(
                        (float)(
                            $totales['total_descuentos']
                            ?? 0
                        ),
                        2,
                        ',',
                        '.'
                    );
                    ?>

                </div>

            </div>


            <div class="total-box">

                <strong>
                    Total No Remunerativo
                </strong>

                <div class="importe">

                    $
                    <?php
                    echo number_format(
                        (float)(
                            $totales['total_no_remunerativo']
                            ?? 0
                        ),
                        2,
                        ',',
                        '.'
                    );
                    ?>

                </div>

            </div>


            <div class="total-box">

                <strong>
                    Total Asignaciones
                </strong>

                <div class="importe">

                    $
                    <?php
                    echo number_format(
                        (float)(
                            $totales['total_asignaciones']
                            ?? 0
                        ),
                        2,
                        ',',
                        '.'
                    );
                    ?>

                </div>

            </div>


            <div class="total-box total-neto">

                <strong>
                    Total Neto
                </strong>

                <div class="importe">

                    $
                    <?php
                    echo number_format(
                        (float)(
                            $totales['total_neto']
                            ?? 0
                        ),
                        2,
                        ',',
                        '.'
                    );
                    ?>

                </div>

            </div>


        </div>


        <!-- =====================================
             RESUMEN POR EMPLEADO
        ====================================== -->

        <h3>
            Resumen por Empleado
        </h3>


        <?php if (
            !empty(
                $resumenEmpleados
            )
        ): ?>


            <div class="tabla-contenedor">

                <table>

                    <thead>

                        <tr>

                            <th>Legajo</th>

                            <th>Empleado</th>

                            <th>
                                Remunerativo
                            </th>

                            <th>
                                Descuentos
                            </th>

                            <th>
                                No Remunerativo
                            </th>

                            <th>
                                Asignaciones
                            </th>

                            <th>
                                Neto
                            </th>

                            <th>
                                Acciones
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php foreach (
                        $resumenEmpleados
                        as
                        $fila
                    ): ?>


                        <tr>


                            <!-- LEGAJO -->

                            <td data-label="Legajo">

                                <?php
                                echo htmlspecialchars(
                                    $fila['nro_legajo'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </td>


                            <!-- EMPLEADO -->

                            <td data-label="Empleado">

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


                            <!-- REMUNERATIVO -->

                            <td
                                data-label="Remunerativo"
                                class="importe-positivo"
                            >

                                $
                                <?php
                                echo number_format(
                                    (float)$fila[
                                        'total_remunerativo'
                                    ],
                                    2,
                                    ',',
                                    '.'
                                );
                                ?>

                            </td>


                            <!-- DESCUENTOS -->

                            <td
                                data-label="Descuentos"
                                class="importe-descuento"
                            >

                                $
                                <?php
                                echo number_format(
                                    (float)$fila[
                                        'total_descuentos'
                                    ],
                                    2,
                                    ',',
                                    '.'
                                );
                                ?>

                            </td>


                            <!-- NO REMUNERATIVO -->

                            <td data-label="No Rem.">

                                $
                                <?php
                                echo number_format(
                                    (float)$fila[
                                        'total_no_remunerativo'
                                    ],
                                    2,
                                    ',',
                                    '.'
                                );
                                ?>

                            </td>


                            <!-- ASIGNACIONES -->

                            <td data-label="Asignaciones">

                                $
                                <?php
                                echo number_format(
                                    (float)$fila[
                                        'total_asignaciones'
                                    ],
                                    2,
                                    ',',
                                    '.'
                                );
                                ?>

                            </td>


                            <!-- NETO -->

                            <td
                                data-label="Neto"
                                class="importe-neto"
                            >

                                $
                                <?php
                                echo number_format(
                                    (float)$fila['neto'],
                                    2,
                                    ',',
                                    '.'
                                );
                                ?>

                            </td>


                            <!-- ACCIONES -->

                            <td data-label="Acciones">

                                <div class="acciones-botones">


                                    <a
                                        href="<?php
                                            echo htmlspecialchars(
                                                $liquidacionVerUrlDetalle(
                                                    (int)$fila[
                                                        'empleado_id'
                                                    ]
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                        ?>"
                                        class="btn btn-detalle"
                                    >
                                        Ver Detalle
                                    </a>


                                    <a
                                        href="<?php
                                            echo htmlspecialchars(
                                                $liquidacionVerUrlRecibo(
                                                    (int)$fila[
                                                        'empleado_id'
                                                    ]
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                        ?>"
                                        class="btn btn-recibo"
                                    >
                                        Ver Recibo
                                    </a>


                                </div>

                            </td>


                        </tr>


                    <?php endforeach; ?>


                    </tbody>

                </table>

            </div>


        <?php else: ?>


            <div class="sin-datos">

                Esta liquidación todavía no tiene empleados procesados.

            </div>


        <?php endif; ?>


        <!-- =====================================
             DETALLE DE CONCEPTOS
        ====================================== -->

        <?php if (
            $empleadoSeleccionado
            &&
            !empty(
                $detalleConceptos
            )
        ): ?>


            <div
                class="detalle-panel"
                id="detalleEmpleado"
            >


                <!-- CABECERA -->

                <div class="detalle-cabecera">


                    <div>

                        <h3>

                            Detalle de Conceptos

                        </h3>


                        <div class="empleado-info">

                            <?php

                            echo htmlspecialchars(
                                $empleadoSeleccionado['apellido']
                                . ', '
                                . $empleadoSeleccionado['nombre']
                                . ' - Legajo: '
                                . $empleadoSeleccionado['nro_legajo'],
                                ENT_QUOTES,
                                'UTF-8'
                            );

                            ?>

                        </div>

                    </div>


                    <a
                        href="<?php
                            echo htmlspecialchars(
                                $liquidacionVerCerrarDetalle,
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                        class="btn btn-cerrar-detalle"
                    >
                        Cerrar Detalle
                    </a>


                </div>


                <!-- TABLA -->

                <div class="tabla-contenedor">

                    <table>

                        <thead>

                            <tr>

                                <th>Código</th>

                                <th>Concepto</th>

                                <th>Categoría</th>

                                <th>Cantidad</th>

                                <th>% Aplicado</th>

                                <th>Monto</th>

                                <th>Origen</th>

                                <th>Observación</th>

                            </tr>

                        </thead>


                        <tbody>


                        <?php

                        /*
                        |--------------------------------------------------------------------------
                        | ORDEN VISUAL DE CONCEPTOS
                        |--------------------------------------------------------------------------
                        |
                        | 1. REMUNERATIVOS
                        | 2. NO REMUNERATIVOS
                        | 3. ASIGNACIONES / ASIGNACIONES FAMILIARES
                        | 4. DESCUENTOS
                        | 5. APORTES PATRONALES
                        |
                        | Dentro de cada grupo se ordena por código numérico.
                        |
                        | El código 112 se trata como NO REMUNERATIVO aunque exista
                        | algún dato histórico con una categoría distinta.
                        |
                        |--------------------------------------------------------------------------
                        */

                        $detalleConceptosOrdenados =
                            $detalleConceptos;


                        usort(
                            $detalleConceptosOrdenados,
                            function($a, $b)
                            {
                                $codigoA =
                                    (int)(
                                        $a['codigo']
                                        ?? 0
                                    );

                                $codigoB =
                                    (int)(
                                        $b['codigo']
                                        ?? 0
                                    );


                                $categoriaA =
                                    strtoupper(
                                        trim(
                                            (string)(
                                                $a['concepto_categoria']
                                                ?? ''
                                            )
                                        )
                                    );

                                $categoriaB =
                                    strtoupper(
                                        trim(
                                            (string)(
                                                $b['concepto_categoria']
                                                ?? ''
                                            )
                                        )
                                    );


                                $prioridad =
                                    function($categoria, $codigo)
                                    {
                                        /*
                                        | Código 112:
                                        | siempre se muestra como bloque
                                        | NO REMUNERATIVO.
                                        */

                                        if ($codigo === 112) {

                                            return 20;
                                        }


                                        switch ($categoria) {

                                            case 'REMUNERATIVO':
                                                return 10;

                                            case 'NO_REMUNERATIVO':
                                                return 20;

                                            case 'ASIGNACION':
                                            case 'ASIGNACION_FAMILIAR':
                                                return 30;

                                            case 'DESCUENTO':
                                                return 40;

                                            case 'APORTE_PATRONAL':
                                                return 50;

                                            default:
                                                return 90;
                                        }
                                    };


                                $prioridadA =
                                    $prioridad(
                                        $categoriaA,
                                        $codigoA
                                    );

                                $prioridadB =
                                    $prioridad(
                                        $categoriaB,
                                        $codigoB
                                    );


                                if ($prioridadA !== $prioridadB) {

                                    return
                                        $prioridadA
                                        <=>
                                        $prioridadB;
                                }


                                return
                                    $codigoA
                                    <=>
                                    $codigoB;
                            }
                        );

                        ?>


                        <?php foreach (
                            $detalleConceptosOrdenados
                            as
                            $detalle
                        ): ?>


                            <?php

                            /*
                            |--------------------------------------------------------------------------
                            | CATEGORÍA
                            |--------------------------------------------------------------------------
                            */

                            $categoriaConcepto =
                                strtoupper(
                                    trim(
                                        (string)(
                                            $detalle[
                                                'concepto_categoria'
                                            ]
                                            ?? ''
                                        )
                                    )
                                );


                            $claseCategoria =
                                'cat-otro';


                            if (
                                $categoriaConcepto
                                ===
                                'REMUNERATIVO'
                            ) {

                                $claseCategoria =
                                    'cat-remunerativo';

                            } elseif (
                                $categoriaConcepto
                                ===
                                'NO_REMUNERATIVO'
                            ) {

                                $claseCategoria =
                                    'cat-no-remunerativo';

                            } elseif (
                                $categoriaConcepto
                                ===
                                'ASIGNACION'
                            ) {

                                $claseCategoria =
                                    'cat-asignacion';

                            } elseif (
                                $categoriaConcepto
                                ===
                                'DESCUENTO'
                            ) {

                                $claseCategoria =
                                    'cat-descuento';

                            } elseif (
                                $categoriaConcepto
                                ===
                                'APORTE_PATRONAL'
                            ) {

                                $claseCategoria =
                                    'cat-patronal';
                            }

                            ?>


                            <tr
                                class="<?php
                                    echo (
                                        (int)$detalle[
                                            'es_manual'
                                        ]
                                        === 1
                                    )
                                        ?
                                        'fila-manual'
                                        :
                                        '';
                                ?>"
                            >


                                <!-- CÓDIGO -->

                                <td data-label="Código">

                                    <?php
                                    echo htmlspecialchars(
                                        $detalle['codigo'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>

                                </td>


                                <!-- CONCEPTO -->

                                <td data-label="Concepto">

                                    <?php
                                    echo htmlspecialchars(
                                        $detalle[
                                            'concepto_nombre'
                                        ],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>

                                </td>


                                <!-- CATEGORÍA -->

                                <td data-label="Categoría">

                                    <span
                                        class="categoria <?php
                                            echo $claseCategoria;
                                        ?>"
                                    >

                                        <?php
                                        echo htmlspecialchars(
                                            $categoriaConcepto
                                                !== ''
                                                ?
                                                $categoriaConcepto
                                                :
                                                '-',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        );
                                        ?>

                                    </span>

                                </td>


                                <!-- CANTIDAD -->

                                <td data-label="Cantidad">

                                    <?php
                                    echo number_format(
                                        (float)$detalle[
                                            'cantidad'
                                        ],
                                        2,
                                        ',',
                                        '.'
                                    );
                                    ?>

                                </td>


                                <!-- PORCENTAJE -->

                                <td data-label="% Aplicado">

                                    <?php
                                    echo number_format(
                                        (float)$detalle[
                                            'porcentaje_aplicado'
                                        ],
                                        2,
                                        ',',
                                        '.'
                                    );
                                    ?>%

                                </td>


                                <!-- MONTO -->

                                <td data-label="Monto">

                                    $
                                    <?php
                                    echo number_format(
                                        (float)$detalle[
                                            'monto'
                                        ],
                                        2,
                                        ',',
                                        '.'
                                    );
                                    ?>

                                </td>


                                <!-- ORIGEN -->

                                <td data-label="Origen">


                                    <?php if (
                                        (int)$detalle[
                                            'es_manual'
                                        ]
                                        === 1
                                    ): ?>


                                        <span
                                            class="etiqueta etiqueta-manual"
                                        >
                                            MANUAL
                                        </span>


                                    <?php else: ?>


                                        <span
                                            class="etiqueta etiqueta-auto"
                                        >
                                            AUTOMÁTICO
                                        </span>


                                    <?php endif; ?>


                                </td>


                                <!-- OBSERVACIÓN -->

                                <td data-label="Observación">

                                    <?php
                                    echo htmlspecialchars(
                                        !empty(
                                            $detalle[
                                                'observacion'
                                            ]
                                        )
                                            ?
                                            $detalle[
                                                'observacion'
                                            ]
                                            :
                                            '-',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>

                                </td>


                            </tr>


                        <?php endforeach; ?>


                        </tbody>

                    </table>

                </div>


            </div>


            <!-- =================================
                 BAJAR AUTOMÁTICAMENTE AL DETALLE
            ================================== -->

            <script>

            document.addEventListener(
                "DOMContentLoaded",
                function()
                {

                    const detalle =
                        document.getElementById(
                            "detalleEmpleado"
                        );


                    if(detalle){

                        detalle.scrollIntoView({
                            behavior:"smooth",
                            block:"start"
                        });
                    }
                }
            );

            </script>


        <?php elseif (
            $empleadoSeleccionado
            &&
            empty(
                $detalleConceptos
            )
        ): ?>


            <div class="detalle-panel">

                <h3>
                    Detalle de Conceptos
                </h3>


                <div class="sin-datos">

                    No hay conceptos cargados para el empleado seleccionado.

                </div>


                <div
                    class="acciones-botones"
                    style="margin-top:15px;"
                >

                    <a
                        href="<?php
                            echo htmlspecialchars(
                                $liquidacionVerCerrarDetalle,
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                        class="btn btn-cerrar-detalle"
                    >
                        Cerrar Detalle
                    </a>

                </div>

            </div>


        <?php endif; ?>


    </div>

</div>


</body>

</html>