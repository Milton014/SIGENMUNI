<?php

require_once __DIR__ . '/../../../core/Url.php';


/*
|--------------------------------------------------------------------------
| REPORTE DE LIQUIDACIONES - ROUTER
|--------------------------------------------------------------------------
*/

$reporteLiquidacionesUrlReportes =
    sigenmuniUrlRuta(
        'reportes'
    );


$reporteLiquidacionesUrlMenu =
    sigenmuniUrlArchivo(
        'index.php'
    );


$reporteLiquidacionesUrlEntrada =
    sigenmuniUrlEntrada();


$reporteLiquidacionesUrlLimpiar =
    sigenmuniUrlRuta(
        'reportes/liquidaciones'
    );


$reporteLiquidacionesFiltros = [
    'periodo' =>
        $periodo,

    'tipo' =>
        $tipo,

    'estado' =>
        $estado
];


$reporteLiquidacionesUrlPdf =
    sigenmuniUrlRuta(
        'reportes/liquidaciones/pdf',
        $reporteLiquidacionesFiltros
    );


$reporteLiquidacionesUrlExcel =
    sigenmuniUrlRuta(
        'reportes/liquidaciones/excel',
        $reporteLiquidacionesFiltros
    );

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Reporte de Liquidaciones - SIGENMUNI</title>

<style>

*{
    box-sizing:border-box;
}

body{
    margin:0;
    font-family:Arial,Helvetica,sans-serif;
    background:#f4f7fb;
    color:#1f2937;
}

.contenedor{
    width:95%;
    max-width:1450px;
    margin:30px auto;
}


/* =========================================
   CABECERA
========================================= */

.cabecera{
    background:linear-gradient(135deg,#16a34a,#22c55e);
    color:white;
    border-radius:18px;
    padding:24px;
    box-shadow:0 8px 20px rgba(0,0,0,.10);
    margin-bottom:22px;
}

.cabecera-top{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:15px;
    flex-wrap:wrap;
}

.cabecera h1{
    margin:0 0 6px 0;
    font-size:30px;
}

.cabecera p{
    margin:0;
    opacity:.95;
}

.acciones-superiores{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
}


/* =========================================
   BOTONES
========================================= */

.btn{
    display:inline-block;
    text-decoration:none;
    border:none;
    border-radius:10px;
    padding:11px 16px;
    font-size:14px;
    font-weight:bold;
    cursor:pointer;
    color:white;
    transition:.2s ease;
    text-align:center;
}

.btn:hover{
    opacity:.93;
    transform:translateY(-1px);
}

.btn-volver{
    background:#374151;
}

.btn-reportes{
    background:#ea580c;
}

.btn-buscar{
    background:#16a34a;
}

.btn-limpiar{
    background:#6b7280;
}

.btn-pdf{
    background:#dc2626;
}

.btn-excel{
    background:#15803d;
}


/* =========================================
   PANEL
========================================= */

.panel{
    background:white;
    border-radius:18px;
    padding:20px;
    box-shadow:0 8px 20px rgba(0,0,0,.08);
    border:1px solid #e5e7eb;
    margin-bottom:20px;
}

.panel h2{
    margin-top:0;
    margin-bottom:16px;
    font-size:20px;
    color:#16a34a;
}


/* =========================================
   ERROR
========================================= */

.error{
    background:#fee2e2;
    color:#991b1b;
    border:1px solid #fecaca;
    padding:14px;
    border-radius:12px;
    margin-bottom:20px;
    font-weight:bold;
}


/* =========================================
   FILTROS
========================================= */

.filtros{
    display:grid;
    grid-template-columns:repeat(3,minmax(0,1fr)) auto auto;
    gap:12px;
    align-items:end;
}

.campo label{
    display:block;
    margin-bottom:6px;
    font-size:14px;
    font-weight:bold;
    color:#374151;
}

.campo input,
.campo select{
    width:100%;
    height:44px;
    padding:11px 12px;
    border:1px solid #cbd5e1;
    border-radius:10px;
    font-size:14px;
    outline:none;
    background:white;
}

.campo input:focus,
.campo select:focus{
    border-color:#22c55e;
    box-shadow:0 0 0 3px rgba(34,197,94,.15);
}

.ayuda{
    margin-top:6px;
    font-size:12px;
    color:#6b7280;
}


/* =========================================
   RESUMEN
========================================= */

.resumen{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:12px;
    flex-wrap:wrap;
    margin-bottom:15px;
}

.resumen-box{
    background:#f0fdf4;
    border:1px solid #bbf7d0;
    color:#166534;
    padding:12px 15px;
    border-radius:12px;
    font-weight:bold;
}

.acciones-exportar{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
}


/* =========================================
   TABLA SIN BARRA HORIZONTAL
========================================= */

.tabla-contenedor{
    width:100%;
    overflow:visible;
}

table{
    width:100%;
    min-width:0;
    table-layout:fixed;
    border-collapse:collapse;
    background:white;
}

th,
td{
    padding:10px 6px;
    border-bottom:1px solid #e5e7eb;
    text-align:left;
    font-size:12px;
    vertical-align:middle;
    overflow-wrap:anywhere;
}

th{
    background:#16a34a;
    color:white;
    white-space:normal;
    line-height:1.15;
}

tr:hover{
    background:#f8fafc;
}


/* =========================================
   ANCHOS
========================================= */

.col-tipo{
    width:10%;
}

.col-periodo{
    width:9%;
}

.col-fecha{
    width:10%;
}

.col-estado{
    width:9%;
    text-align:center;
}

.col-empleados{
    width:7%;
    text-align:center;
}

.col-remunerativo{
    width:12%;
}

.col-descuentos{
    width:11%;
}

.col-no-remunerativo{
    width:12%;
}

.col-asignaciones{
    width:10%;
}

.col-neto{
    width:10%;
}


/* =========================================
   ESTADOS
========================================= */

.estado-badge{
    display:inline-block;
    padding:5px 9px;
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
   NETO
========================================= */

.neto{
    font-weight:bold;
    color:#111827;
}


/* =========================================
   SIN REGISTROS
========================================= */

.sin-registros{
    text-align:center;
    padding:25px;
    color:#6b7280;
    background:#fff;
    border-radius:12px;
}


/* =========================================
   TABLET
========================================= */

@media(max-width:1100px){

    .filtros{
        grid-template-columns:1fr 1fr;
    }

    .filtros .btn{
        width:100%;
    }

    th,
    td{
        font-size:11px;
        padding:9px 5px;
    }
}


/* =========================================
   CELULAR
========================================= */

@media(max-width:768px){

    .contenedor{
        width:98%;
        margin:10px auto;
    }

    .cabecera{
        padding:18px 15px;
        border-radius:15px;
    }

    .cabecera-top{
        flex-direction:column;
        align-items:stretch;
        text-align:center;
    }

    .cabecera h1{
        font-size:24px;
    }

    .acciones-superiores{
        flex-direction:column;
    }

    .acciones-superiores .btn{
        width:100%;
    }

    .panel{
        padding:15px;
        border-radius:15px;
    }

    .panel h2{
        text-align:center;
        font-size:19px;
    }

    .filtros{
        grid-template-columns:1fr;
    }

    .campo input,
    .campo select{
        min-height:44px;
        font-size:16px;
    }

    .acciones-exportar{
        width:100%;
        flex-direction:column;
    }

    .acciones-exportar .btn{
        width:100%;
    }

    .resumen{
        flex-direction:column;
        align-items:stretch;
    }

    .resumen-box{
        text-align:center;
    }


    /*
    |----------------------------------------------------------------------
    | TABLA RESPONSIVE TIPO TARJETAS
    |----------------------------------------------------------------------
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
        gap:12px;
    }

    tbody tr{
        border:1px solid #e5e7eb;
        border-radius:12px;
        padding:12px;
        background:white;
    }

    tbody tr:hover{
        background:white;
    }

    td{
        display:grid;
        grid-template-columns:155px 1fr;
        gap:10px;
        padding:8px 0;
        border-bottom:1px solid #f1f5f9;
        text-align:left !important;
        font-size:13px;
    }

    td:last-child{
        border-bottom:none;
    }

    td::before{
        content:attr(data-label);
        font-weight:bold;
        color:#475569;
    }

    .estado-badge{
        justify-self:start;
    }
}

</style>

</head>

<body>

<div class="contenedor">


    <!-- =====================================
         CABECERA
    ====================================== -->

    <div class="cabecera">

        <div class="cabecera-top">

            <div>

                <h1>
                    Reporte de Liquidaciones
                </h1>

                <p>
                    Consulta histórica de liquidaciones
                    con filtros y totales generales.
                </p>

            </div>

            <div class="acciones-superiores">

                <a
                    href="<?php
                    echo htmlspecialchars(
                        $reporteLiquidacionesUrlReportes,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                    class="btn btn-reportes"
                >
                    Volver a Reportes
                </a>

                <a
                    href="<?php
                    echo htmlspecialchars(
                        $reporteLiquidacionesUrlMenu,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                    class="btn btn-volver"
                >
                    Menú Principal
                </a>

            </div>

        </div>

    </div>


    <!-- =====================================
         ERROR
    ====================================== -->

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


    <!-- =====================================
         FILTROS
    ====================================== -->

    <div class="panel">

        <h2>
            Filtros de búsqueda
        </h2>

        <form
            method="GET"
            action="<?php
                echo htmlspecialchars(
                    $reporteLiquidacionesUrlEntrada,
                    ENT_QUOTES,
                    'UTF-8'
                );
            ?>"
            class="filtros"
        >

            <input
                type="hidden"
                name="r"
                value="reportes/liquidaciones"
            >

            <div class="campo">

                <label for="periodo">
                    Período
                </label>

                <input
                    type="month"
                    id="periodo"
                    name="periodo"
                    value="<?php
                        echo htmlspecialchars(
                            $periodo,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                >

            </div>


            <div class="campo">

                <label for="tipo">
                    Tipo de liquidación
                </label>

                <select
                    name="tipo"
                    id="tipo"
                >

                    <option value="">
                        Todos
                    </option>

                    <?php foreach ($tiposLiquidacion as $t): ?>

                        <option
                            value="<?php
                                echo htmlspecialchars(
                                    $t,
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                            ?>"
                            <?php
                            echo (
                                $tipo
                                ===
                                strtoupper(
                                    trim(
                                        (string)$t
                                    )
                                )
                            )
                                ?
                                'selected'
                                :
                                '';
                            ?>
                        >
                            <?php
                            echo htmlspecialchars(
                                ucwords(
                                    strtolower(
                                        str_replace(
                                            '_',
                                            ' ',
                                            $t
                                        )
                                    )
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <div class="campo">

                <label for="estado">
                    Estado
                </label>

                <select
                    name="estado"
                    id="estado"
                >

                    <option value="">
                        Todos
                    </option>

                    <?php foreach ($estados as $e): ?>

                        <option
                            value="<?php
                                echo htmlspecialchars(
                                    $e,
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                            ?>"
                            <?php
                            echo (
                                $estado === $e
                            )
                                ?
                                'selected'
                                :
                                '';
                            ?>
                        >
                            <?php
                            echo htmlspecialchars(
                                $e,
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <button
                type="submit"
                class="btn btn-buscar"
            >
                Buscar
            </button>


            <a
                href="<?php
                    echo htmlspecialchars(
                        $reporteLiquidacionesUrlLimpiar,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                class="btn btn-limpiar"
            >
                Limpiar
            </a>

        </form>

    </div>


    <!-- =====================================
         RESULTADOS
    ====================================== -->

    <div class="panel">

        <div class="resumen">

            <div class="resumen-box">

                Total de liquidaciones encontradas:

                <?php
                echo (int)$totalLiquidaciones;
                ?>

            </div>


            <div class="acciones-exportar">

                <a
                    href="<?php
                        echo htmlspecialchars(
                            $reporteLiquidacionesUrlPdf,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                    class="btn btn-pdf"
                >
                    Exportar PDF
                </a>


                <a
                    href="<?php
                        echo htmlspecialchars(
                            $reporteLiquidacionesUrlExcel,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                    class="btn btn-excel"
                >
                    Exportar Excel
                </a>

            </div>

        </div>


        <?php if (!empty($liquidaciones)): ?>

            <div class="tabla-contenedor">

                <table>

                    <thead>

                        <tr>

                            <th class="col-tipo">
                                Tipo
                            </th>

                            <th class="col-periodo">
                                Período
                            </th>

                            <th class="col-fecha">
                                Fecha Liquidación
                            </th>

                            <th class="col-estado">
                                Estado
                            </th>

                            <th class="col-empleados">
                                Empleados
                            </th>

                            <th class="col-remunerativo">
                                Total Remunerativo
                            </th>

                            <th class="col-descuentos">
                                Total Descuentos
                            </th>

                            <th class="col-no-remunerativo">
                                No Remunerativo
                            </th>

                            <th class="col-asignaciones">
                                Asignaciones
                            </th>

                            <th class="col-neto">
                                Neto
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php foreach ($liquidaciones as $fila): ?>

                        <?php

                        $estadoLiquidacion =
                            strtoupper(
                                trim(
                                    $fila['estado']
                                    ?? ''
                                )
                            );


                        switch ($estadoLiquidacion) {

                            case 'CERRADA':
                                $claseEstado = 'estado-cerrada';
                                break;

                            case 'ANULADA':
                                $claseEstado = 'estado-anulada';
                                break;

                            default:
                                $claseEstado = 'estado-borrador';
                                break;
                        }

                        ?>

                        <tr>


                            <td
                                data-label="Tipo"
                                class="col-tipo"
                            >

                                <?php
                                echo htmlspecialchars(
                                    $fila['tipo_liquidacion']
                                    ?? '',
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </td>


                            <td
                                data-label="Período"
                                class="col-periodo"
                            >

                                <?php
                                echo htmlspecialchars(
                                    $fila['periodo']
                                    ?? '',
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </td>


                            <td
                                data-label="Fecha Liquidación"
                                class="col-fecha"
                            >

                                <?php
                                echo !empty(
                                    $fila['fecha_liquidacion']
                                )
                                    ?
                                    date(
                                        "d/m/Y",
                                        strtotime(
                                            $fila['fecha_liquidacion']
                                        )
                                    )
                                    :
                                    '-';
                                ?>

                            </td>


                            <td
                                data-label="Estado"
                                class="col-estado"
                            >

                                <span
                                    class="estado-badge <?php
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

                            </td>


                            <td
                                data-label="Empleados"
                                class="col-empleados"
                            >

                                <?php
                                echo (int)(
                                    $fila['cantidad_empleados']
                                    ?? 0
                                );
                                ?>

                            </td>


                            <td
                                data-label="Total Remunerativo"
                                class="col-remunerativo"
                            >

                                $
                                <?php
                                echo number_format(
                                    (float)(
                                        $fila['total_remunerativo']
                                        ?? 0
                                    ),
                                    2,
                                    ',',
                                    '.'
                                );
                                ?>

                            </td>


                            <td
                                data-label="Total Descuentos"
                                class="col-descuentos"
                            >

                                $
                                <?php
                                echo number_format(
                                    (float)(
                                        $fila['total_descuentos']
                                        ?? 0
                                    ),
                                    2,
                                    ',',
                                    '.'
                                );
                                ?>

                            </td>


                            <td
                                data-label="No Remunerativo"
                                class="col-no-remunerativo"
                            >

                                $
                                <?php
                                echo number_format(
                                    (float)(
                                        $fila['total_no_remunerativo']
                                        ?? 0
                                    ),
                                    2,
                                    ',',
                                    '.'
                                );
                                ?>

                            </td>


                            <td
                                data-label="Asignaciones"
                                class="col-asignaciones"
                            >

                                $
                                <?php
                                echo number_format(
                                    (float)(
                                        $fila['total_asignaciones']
                                        ?? 0
                                    ),
                                    2,
                                    ',',
                                    '.'
                                );
                                ?>

                            </td>


                            <td
                                data-label="Neto"
                                class="col-neto"
                            >

                                <span class="neto">

                                    $
                                    <?php
                                    echo number_format(
                                        (float)(
                                            $fila['total_neto']
                                            ?? 0
                                        ),
                                        2,
                                        ',',
                                        '.'
                                    );
                                    ?>

                                </span>

                            </td>


                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php else: ?>

            <div class="sin-registros">

                No se encontraron liquidaciones
                con los filtros seleccionados.

            </div>

        <?php endif; ?>

    </div>

</div>

</body>

</html>