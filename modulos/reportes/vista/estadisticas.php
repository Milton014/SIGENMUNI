<?php

require_once __DIR__ . '/../../../core/Url.php';


/*
|--------------------------------------------------------------------------
| ESTADÍSTICAS - ROUTER
|--------------------------------------------------------------------------
*/

$estadisticasUrlReportes =
    sigenmuniUrlRuta(
        'reportes'
    );


$estadisticasUrlMenu =
    sigenmuniUrlArchivo(
        'index.php'
    );


$estadisticasUrlEntrada =
    sigenmuniUrlEntrada();


$estadisticasUrlLimpiar =
    sigenmuniUrlRuta(
        'reportes/estadisticas'
    );


$estadisticasFiltros = [
    'desde' =>
        $periodoDesde,

    'hasta' =>
        $periodoHasta,

    'tipo' =>
        $tipoLiquidacion
];


$estadisticasUrlPdf =
    sigenmuniUrlRuta(
        'reportes/estadisticas/pdf',
        $estadisticasFiltros
    );


$estadisticasUrlExcel =
    sigenmuniUrlRuta(
        'reportes/estadisticas/excel',
        $estadisticasFiltros
    );



/*
|--------------------------------------------------------------------------
| VISTA - ESTADÍSTICAS
|--------------------------------------------------------------------------
|
| Variables esperadas desde ReporteControlador::estadisticas():
|
| - $periodoDesde
| - $periodoHasta
| - $tipoLiquidacion
| - $tiposLiquidacion
| - $error
| - $resumen
| - $empleadosPorCategoria
| - $netoPorPeriodo
| - $conceptosPorCategoria
| - $principalesDescuentos
| - $graficoEmpleadosCategoria
| - $graficoNetoPeriodo
| - $graficoConceptos
| - $graficoDescuentos
| - $queryString
|
|--------------------------------------------------------------------------
*/

if (!function_exists('formatearTipoLiquidacionEstadistica')) {

    function formatearTipoLiquidacionEstadistica($tipo)
    {
        $tipo =
            strtoupper(
                trim(
                    (string)$tipo
                )
            );

        $nombres = [
            'MENSUAL' =>
                'Mensual',

            'COMPLEMENTARIA' =>
                'Complementaria',

            'AGUINALDO' =>
                'Aguinaldo',

            'COMPLEMENTARIA_SAC' =>
                'Complementaria SAC',

            'GASTOS_PROTOCOLARES' =>
                'Gastos Protocolares'
        ];

        if (isset($nombres[$tipo])) {
            return $nombres[$tipo];
        }

        return ucwords(
            strtolower(
                str_replace(
                    '_',
                    ' ',
                    $tipo
                )
            )
        );
    }
}


$tipoLiquidacion =
    strtoupper(
        trim(
            (string)(
                $tipoLiquidacion
                ?? ''
            )
        )
    );


$periodoDesde =
    trim(
        (string)(
            $periodoDesde
            ?? ''
        )
    );


$periodoHasta =
    trim(
        (string)(
            $periodoHasta
            ?? ''
        )
    );


$tiposLiquidacion =
    is_array(
        $tiposLiquidacion
        ?? null
    )
        ? $tiposLiquidacion
        : [];


$resumen =
    is_array(
        $resumen
        ?? null
    )
        ? $resumen
        : [];


$empleadosPorCategoria =
    is_array(
        $empleadosPorCategoria
        ?? null
    )
        ? $empleadosPorCategoria
        : [];


$netoPorPeriodo =
    is_array(
        $netoPorPeriodo
        ?? null
    )
        ? $netoPorPeriodo
        : [];


$conceptosPorCategoria =
    is_array(
        $conceptosPorCategoria
        ?? null
    )
        ? $conceptosPorCategoria
        : [];


$principalesDescuentos =
    is_array(
        $principalesDescuentos
        ?? null
    )
        ? $principalesDescuentos
        : [];


/*
|--------------------------------------------------------------------------
| TOTALES AUXILIARES PARA TABLAS DE CONTROL
|--------------------------------------------------------------------------
*/

$totalComposicion =
    0.0;


foreach (
    $conceptosPorCategoria as $filaConcepto
) {

    $totalComposicion +=
        (float)(
            $filaConcepto['total']
            ?? 0
        );
}

?>
<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Estadísticas - SIGENMUNI</title>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
    background:linear-gradient(135deg,#ea580c,#fb923c);
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
    background:#7c3aed;
}

.btn-aplicar{
    background:#ea580c;
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
    color:#ea580c;
}


/* =========================================
   FILTROS
========================================= */

.filtros{
    display:grid;
    grid-template-columns:1fr 1fr 1.25fr auto auto;
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
    padding:11px 12px;
    border:1px solid #cbd5e1;
    border-radius:10px;
    font-size:14px;
    outline:none;
    background:white;
}

.campo input:focus,
.campo select:focus{
    border-color:#fb923c;
    box-shadow:0 0 0 3px rgba(251,146,60,.18);
}

.ayuda{
    margin-top:6px;
    font-size:12px;
    color:#6b7280;
    line-height:1.4;
}

.alcance{
    margin-top:16px;
    display:flex;
    gap:8px;
    flex-wrap:wrap;
    align-items:center;
}

.alcance-item{
    display:inline-flex;
    align-items:center;
    min-height:30px;
    padding:6px 10px;
    border-radius:999px;
    background:#fff7ed;
    border:1px solid #fed7aa;
    color:#9a3412;
    font-size:12px;
    font-weight:bold;
}


/* =========================================
   TARJETAS
========================================= */

.tarjetas{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(210px,1fr));
    gap:15px;
    margin-bottom:20px;
}

.card{
    background:white;
    border:1px solid #e5e7eb;
    border-radius:16px;
    padding:18px;
    box-shadow:0 6px 16px rgba(0,0,0,.06);
}

.card h3{
    margin:0 0 8px 0;
    font-size:13px;
    color:#6b7280;
    line-height:1.35;
}

.card .num{
    font-size:23px;
    font-weight:bold;
    color:#ea580c;
    overflow-wrap:anywhere;
}

.card .detalle{
    margin-top:8px;
    font-size:11px;
    color:#94a3b8;
    line-height:1.4;
}


/* =========================================
   EXPORTAR
========================================= */

.acciones-estadisticas{
    display:flex;
    justify-content:flex-end;
    gap:10px;
    flex-wrap:wrap;
    margin-bottom:20px;
}


/* =========================================
   GRÁFICOS
========================================= */

.graficos{
    display:grid;
    grid-template-columns:repeat(2,minmax(0,1fr));
    gap:20px;
}

.grafico{
    background:white;
    border:1px solid #e5e7eb;
    border-radius:18px;
    padding:20px;
    box-shadow:0 8px 20px rgba(0,0,0,.06);
    min-width:0;
}

.grafico h3{
    margin:0 0 6px 0;
    font-size:18px;
    color:#111827;
}

.grafico p{
    margin:0 0 15px 0;
    font-size:13px;
    color:#6b7280;
    line-height:1.45;
}

.canvas-wrap{
    position:relative;
    width:100%;
    min-height:320px;
}

canvas{
    max-width:100%;
}


/* =========================================
   TABLAS DE CONTROL
========================================= */

.datos-grafico{
    margin-top:18px;
    padding-top:15px;
    border-top:1px solid #e5e7eb;
}

.datos-grafico h4{
    margin:0 0 10px 0;
    font-size:13px;
    color:#475569;
}

.tabla-datos{
    width:100%;
    border-collapse:collapse;
    table-layout:fixed;
}

.tabla-datos th,
.tabla-datos td{
    padding:8px 9px;
    border-bottom:1px solid #f1f5f9;
    font-size:12px;
    text-align:left;
    overflow-wrap:anywhere;
}

.tabla-datos th{
    background:#fff7ed;
    color:#9a3412;
}

.tabla-datos .numero{
    text-align:right;
    white-space:nowrap;
}

.tabla-datos .total-fila{
    font-weight:bold;
    background:#fafafa;
}


/* =========================================
   SIN DATOS
========================================= */

.sin-datos{
    display:flex;
    align-items:center;
    justify-content:center;
    min-height:260px;
    text-align:center;
    color:#6b7280;
    font-size:14px;
    background:#f9fafb;
    border:1px dashed #d1d5db;
    border-radius:12px;
    padding:20px;
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

    .graficos{
        grid-template-columns:1fr;
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

    .filtros{
        grid-template-columns:1fr;
    }

    .campo input,
    .campo select{
        min-height:44px;
        font-size:16px;
    }

    .acciones-estadisticas{
        flex-direction:column;
    }

    .acciones-estadisticas .btn{
        width:100%;
    }

    .tarjetas{
        grid-template-columns:1fr;
    }

    .grafico{
        padding:15px;
        border-radius:15px;
    }

    .canvas-wrap{
        min-height:280px;
    }

    .tabla-datos th,
    .tabla-datos td{
        font-size:11px;
        padding:7px 6px;
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
                    Estadísticas
                </h1>

                <p>
                    Indicadores generales y análisis
                    de liquidaciones cerradas.
                </p>

            </div>


            <div class="acciones-superiores">

                <a
                    href="<?php
                    echo htmlspecialchars(
                        $estadisticasUrlReportes,
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
                        $estadisticasUrlMenu,
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
            Período de análisis
        </h2>

        <form
            method="GET"
            action="<?php
                echo htmlspecialchars(
                    $estadisticasUrlEntrada,
                    ENT_QUOTES,
                    'UTF-8'
                );
            ?>"
            class="filtros"
        >

            <input
                type="hidden"
                name="r"
                value="reportes/estadisticas"
            >

            <div class="campo">

                <label for="desde">
                    Desde
                </label>

                <input
                    type="month"
                    id="desde"
                    name="desde"
                    value="<?php
                        echo htmlspecialchars(
                            $periodoDesde,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                >

                <div class="ayuda">
                    Período inicial opcional.
                </div>

            </div>


            <div class="campo">

                <label for="hasta">
                    Hasta
                </label>

                <input
                    type="month"
                    id="hasta"
                    name="hasta"
                    value="<?php
                        echo htmlspecialchars(
                            $periodoHasta,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                >

                <div class="ayuda">
                    Período final opcional.
                </div>

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
                        Todas
                    </option>

                    <?php foreach ($tiposLiquidacion as $tipoDisponible): ?>

                        <?php
                        $tipoDisponibleNormalizado =
                            strtoupper(
                                trim(
                                    (string)$tipoDisponible
                                )
                            );
                        ?>

                        <option
                            value="<?php
                                echo htmlspecialchars(
                                    $tipoDisponibleNormalizado,
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                            ?>"
                            <?php
                            echo (
                                $tipoLiquidacion
                                ===
                                $tipoDisponibleNormalizado
                            )
                                ? 'selected'
                                : '';
                            ?>
                        >
                            <?php
                            echo htmlspecialchars(
                                formatearTipoLiquidacionEstadistica(
                                    $tipoDisponibleNormalizado
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>
                        </option>

                    <?php endforeach; ?>

                </select>

                <div class="ayuda">
                    Filtra las estadísticas económicas.
                </div>

            </div>


            <button
                type="submit"
                class="btn btn-aplicar"
            >
                Aplicar
            </button>


            <a
                href="<?php
                    echo htmlspecialchars(
                        $estadisticasUrlLimpiar,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                class="btn btn-limpiar"
            >
                Limpiar
            </a>

        </form>


        <div class="alcance">

            <span class="alcance-item">
                Estado: CERRADA
            </span>

            <span class="alcance-item">
                Tipo:
                <?php
                echo htmlspecialchars(
                    $tipoLiquidacion !== ''
                        ? formatearTipoLiquidacionEstadistica(
                            $tipoLiquidacion
                        )
                        : 'Todas',
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>
            </span>

            <span class="alcance-item">
                Empleados por categoría:
                padrón actual
            </span>

        </div>

    </div>


    <!-- =====================================
         TARJETAS
    ====================================== -->

    <div class="tarjetas">


        <div class="card">

            <h3>
                Empleados activos actuales
            </h3>

            <div class="num">

                <?php
                echo (int)(
                    $resumen['empleados_activos']
                    ?? 0
                );
                ?>

            </div>

            <div class="detalle">
                Corresponde al padrón actual y no al período histórico seleccionado.
            </div>

        </div>


        <div class="card">

            <h3>
                Liquidaciones cerradas
            </h3>

            <div class="num">

                <?php
                echo (int)(
                    $resumen['liquidaciones_cerradas']
                    ?? 0
                );
                ?>

            </div>

            <div class="detalle">
                Según período y tipo de liquidación seleccionados.
            </div>

        </div>


        <div class="card">

            <h3>
                Total remunerativo
            </h3>

            <div class="num">

                $
                <?php
                echo number_format(
                    (float)(
                        $resumen['total_remunerativo']
                        ?? 0
                    ),
                    2,
                    ',',
                    '.'
                );
                ?>

            </div>

        </div>


        <div class="card">

            <h3>
                Total no remunerativo
            </h3>

            <div class="num">

                $
                <?php
                echo number_format(
                    (float)(
                        $resumen['total_no_remunerativo']
                        ?? 0
                    ),
                    2,
                    ',',
                    '.'
                );
                ?>

            </div>

        </div>


        <div class="card">

            <h3>
                Total asignaciones
            </h3>

            <div class="num">

                $
                <?php
                echo number_format(
                    (float)(
                        $resumen['total_asignaciones']
                        ?? 0
                    ),
                    2,
                    ',',
                    '.'
                );
                ?>

            </div>

        </div>


        <div class="card">

            <h3>
                Total descuentos
            </h3>

            <div class="num">

                $
                <?php
                echo number_format(
                    (float)(
                        $resumen['total_descuentos']
                        ?? 0
                    ),
                    2,
                    ',',
                    '.'
                );
                ?>

            </div>

        </div>


        <div class="card">

            <h3>
                Total neto liquidado
            </h3>

            <div class="num">

                $
                <?php
                echo number_format(
                    (float)(
                        $resumen['total_neto']
                        ?? 0
                    ),
                    2,
                    ',',
                    '.'
                );
                ?>

            </div>

        </div>


        <div class="card">

            <h3>
                Neto promedio por empleado liquidado
            </h3>

            <div class="num">

                $
                <?php
                echo number_format(
                    (float)(
                        $resumen['neto_promedio']
                        ?? 0
                    ),
                    2,
                    ',',
                    '.'
                );
                ?>

            </div>

            <div class="detalle">
                Promedio de los registros de empleados incluidos en liquidaciones cerradas.
            </div>

        </div>


    </div>


    <!-- =====================================
         EXPORTAR
    ====================================== -->

    <div class="acciones-estadisticas">

        <button
            type="button"
            class="btn btn-pdf"
            onclick="exportarPDF()"
        >
            Exportar PDF
        </button>


        <button
            type="button"
            class="btn btn-excel"
            onclick="exportarExcel()"
        >
            Exportar Excel
        </button>

    </div>


    <!-- =====================================
         GRÁFICOS
    ====================================== -->

    <div class="graficos">


        <!-- EMPLEADOS ACTIVOS ACTUALES POR CATEGORÍA -->

        <div class="grafico">

            <h3>
                Empleados activos actuales por categoría
            </h3>

            <p>
                Distribución del padrón actualmente activo según su categoría laboral.
                Este gráfico no depende de los filtros de período ni tipo.
            </p>

            <?php if (
                !empty(
                    $graficoEmpleadosCategoria['labels']
                    ?? []
                )
            ): ?>

                <div class="canvas-wrap">
                    <canvas id="graficoCategoria"></canvas>
                </div>

                <div class="datos-grafico">

                    <h4>
                        Valores del gráfico
                    </h4>

                    <table class="tabla-datos">

                        <thead>
                            <tr>
                                <th>Categoría</th>
                                <th class="numero">Empleados</th>
                            </tr>
                        </thead>

                        <tbody>

                        <?php foreach ($empleadosPorCategoria as $fila): ?>

                            <tr>

                                <td>
                                    <?php
                                    $codigoCategoria =
                                        trim(
                                            (string)(
                                                $fila['codigo']
                                                ?? ''
                                            )
                                        );

                                    $nombreCategoria =
                                        trim(
                                            (string)(
                                                $fila['categoria']
                                                ?? 'Sin categoría'
                                            )
                                        );

                                    echo htmlspecialchars(
                                        (
                                            $codigoCategoria !== ''
                                                ? $codigoCategoria . ' - '
                                                : ''
                                        )
                                        .
                                        $nombreCategoria,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>
                                </td>

                                <td class="numero">
                                    <?php
                                    echo (int)(
                                        $fila['total']
                                        ?? 0
                                    );
                                    ?>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            <?php else: ?>

                <div class="sin-datos">
                    No hay datos disponibles
                    para este gráfico.
                </div>

            <?php endif; ?>

        </div>


        <!-- TOTAL NETO POR PERÍODO -->

        <div class="grafico">

            <h3>
                Total neto liquidado por período
            </h3>

            <p>
                Evolución del total neto de liquidaciones cerradas según los filtros
                económicos seleccionados.
            </p>

            <?php if (
                !empty(
                    $graficoNetoPeriodo['labels']
                    ?? []
                )
            ): ?>

                <div class="canvas-wrap">
                    <canvas id="graficoPeriodo"></canvas>
                </div>

                <div class="datos-grafico">

                    <h4>
                        Valores del gráfico
                    </h4>

                    <table class="tabla-datos">

                        <thead>
                            <tr>
                                <th>Período</th>
                                <th class="numero">Total neto</th>
                            </tr>
                        </thead>

                        <tbody>

                        <?php foreach ($netoPorPeriodo as $fila): ?>

                            <tr>

                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        $fila['periodo']
                                        ?? '',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>
                                </td>

                                <td class="numero">
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
                                </td>

                            </tr>

                        <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            <?php else: ?>

                <div class="sin-datos">
                    No hay liquidaciones cerradas
                    para los filtros seleccionados.
                </div>

            <?php endif; ?>

        </div>


        <!-- COMPOSICIÓN DE LIQUIDACIÓN -->

        <div class="grafico">

            <h3>
                Composición de la liquidación por tipo de concepto
            </h3>

            <p>
                Distribución de los importes registrados en el detalle de las
                liquidaciones cerradas. Puede incluir remunerativos, no remunerativos,
                asignaciones, descuentos y aportes patronales.
            </p>

            <?php if (
                !empty(
                    $graficoConceptos['labels']
                    ?? []
                )
            ): ?>

                <div class="canvas-wrap">
                    <canvas id="graficoConceptos"></canvas>
                </div>

                <div class="datos-grafico">

                    <h4>
                        Valores del gráfico
                    </h4>

                    <table class="tabla-datos">

                        <thead>
                            <tr>
                                <th>Tipo de concepto</th>
                                <th class="numero">Importe</th>
                                <th class="numero">Participación</th>
                            </tr>
                        </thead>

                        <tbody>

                        <?php foreach ($conceptosPorCategoria as $fila): ?>

                            <?php
                            $importeConcepto =
                                (float)(
                                    $fila['total']
                                    ?? 0
                                );

                            $porcentajeParticipacion =
                                $totalComposicion > 0
                                    ? (
                                        $importeConcepto
                                        /
                                        $totalComposicion
                                    ) * 100
                                    : 0;
                            ?>

                            <tr>

                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        $fila['tipo']
                                        ?? '',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>
                                </td>

                                <td class="numero">
                                    $
                                    <?php
                                    echo number_format(
                                        $importeConcepto,
                                        2,
                                        ',',
                                        '.'
                                    );
                                    ?>
                                </td>

                                <td class="numero">
                                    <?php
                                    echo number_format(
                                        $porcentajeParticipacion,
                                        2,
                                        ',',
                                        '.'
                                    );
                                    ?>%
                                </td>

                            </tr>

                        <?php endforeach; ?>


                        <tr class="total-fila">

                            <td>
                                Total representado
                            </td>

                            <td class="numero">
                                $
                                <?php
                                echo number_format(
                                    $totalComposicion,
                                    2,
                                    ',',
                                    '.'
                                );
                                ?>
                            </td>

                            <td class="numero">
                                <?php
                                echo $totalComposicion > 0
                                    ? '100,00%'
                                    : '0,00%';
                                ?>
                            </td>

                        </tr>

                        </tbody>

                    </table>

                </div>

            <?php else: ?>

                <div class="sin-datos">
                    No hay conceptos liquidados
                    para los filtros seleccionados.
                </div>

            <?php endif; ?>

        </div>


        <!-- PRINCIPALES DESCUENTOS -->

        <div class="grafico">

            <h3>
                Principales descuentos
            </h3>

            <p>
                Ranking de hasta 10 descuentos con mayor importe acumulado en las
                liquidaciones cerradas seleccionadas.
            </p>

            <?php if (
                !empty(
                    $graficoDescuentos['labels']
                    ?? []
                )
            ): ?>

                <div class="canvas-wrap">
                    <canvas id="graficoDescuentos"></canvas>
                </div>

                <div class="datos-grafico">

                    <h4>
                        Valores del gráfico
                    </h4>

                    <table class="tabla-datos">

                        <thead>
                            <tr>
                                <th>Concepto</th>
                                <th class="numero">Importe acumulado</th>
                            </tr>
                        </thead>

                        <tbody>

                        <?php foreach ($principalesDescuentos as $fila): ?>

                            <tr>

                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        $fila['concepto']
                                        ?? '',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>
                                </td>

                                <td class="numero">
                                    $
                                    <?php
                                    echo number_format(
                                        (float)(
                                            $fila['total']
                                            ?? 0
                                        ),
                                        2,
                                        ',',
                                        '.'
                                    );
                                    ?>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            <?php else: ?>

                <div class="sin-datos">
                    No hay descuentos disponibles
                    para los filtros seleccionados.
                </div>

            <?php endif; ?>

        </div>


    </div>

</div>


<script>

const charts = {};


/*
|--------------------------------------------------------------------------
| FORMATO MONEDA
|--------------------------------------------------------------------------
*/

function formatoPesos(valor){

    return new Intl.NumberFormat(
        'es-AR',
        {
            style:'currency',
            currency:'ARS',
            minimumFractionDigits:2
        }
    ).format(valor);
}


/*
|--------------------------------------------------------------------------
| CONFIGURACIÓN COMÚN
|--------------------------------------------------------------------------
*/

Chart.defaults.font.family =
    'Arial, Helvetica, sans-serif';

Chart.defaults.color =
    '#374151';


/*
|--------------------------------------------------------------------------
| EMPLEADOS ACTIVOS ACTUALES POR CATEGORÍA
|--------------------------------------------------------------------------
*/

<?php if (
    !empty(
        $graficoEmpleadosCategoria['labels']
        ?? []
    )
): ?>

charts.categoria =
    new Chart(
        document.getElementById(
            'graficoCategoria'
        ),
        {
            type:'bar',

            data:{
                labels:
                    <?php
                    echo json_encode(
                        $graficoEmpleadosCategoria['labels'],
                        JSON_UNESCAPED_UNICODE
                        |
                        JSON_HEX_TAG
                        |
                        JSON_HEX_AMP
                        |
                        JSON_HEX_APOS
                        |
                        JSON_HEX_QUOT
                    );
                    ?>,

                datasets:[
                    {
                        label:'Cantidad de empleados',

                        data:
                            <?php
                            echo json_encode(
                                $graficoEmpleadosCategoria['valores']
                            );
                            ?>,

                        borderWidth:1
                    }
                ]
            },

            options:{
                responsive:true,
                maintainAspectRatio:false,

                plugins:{
                    legend:{
                        display:false
                    }
                },

                scales:{
                    y:{
                        beginAtZero:true,

                        ticks:{
                            precision:0
                        }
                    }
                }
            }
        }
    );

<?php endif; ?>


/*
|--------------------------------------------------------------------------
| TOTAL NETO POR PERÍODO
|--------------------------------------------------------------------------
*/

<?php if (
    !empty(
        $graficoNetoPeriodo['labels']
        ?? []
    )
): ?>

charts.periodo =
    new Chart(
        document.getElementById(
            'graficoPeriodo'
        ),
        {
            type:'line',

            data:{
                labels:
                    <?php
                    echo json_encode(
                        $graficoNetoPeriodo['labels'],
                        JSON_UNESCAPED_UNICODE
                        |
                        JSON_HEX_TAG
                        |
                        JSON_HEX_AMP
                        |
                        JSON_HEX_APOS
                        |
                        JSON_HEX_QUOT
                    );
                    ?>,

                datasets:[
                    {
                        label:'Total neto',

                        data:
                            <?php
                            echo json_encode(
                                $graficoNetoPeriodo['valores']
                            );
                            ?>,

                        borderWidth:2,
                        tension:.25
                    }
                ]
            },

            options:{
                responsive:true,
                maintainAspectRatio:false,

                plugins:{
                    tooltip:{
                        callbacks:{
                            label:function(context){

                                return formatoPesos(
                                    context.raw
                                );
                            }
                        }
                    }
                },

                scales:{
                    y:{
                        beginAtZero:true,

                        ticks:{
                            callback:function(valor){

                                return '$ '
                                    +
                                    Number(valor)
                                        .toLocaleString(
                                            'es-AR'
                                        );
                            }
                        }
                    }
                }
            }
        }
    );

<?php endif; ?>


/*
|--------------------------------------------------------------------------
| COMPOSICIÓN POR TIPO DE CONCEPTO
|--------------------------------------------------------------------------
*/

<?php if (
    !empty(
        $graficoConceptos['labels']
        ?? []
    )
): ?>

charts.conceptos =
    new Chart(
        document.getElementById(
            'graficoConceptos'
        ),
        {
            type:'doughnut',

            data:{
                labels:
                    <?php
                    echo json_encode(
                        $graficoConceptos['labels'],
                        JSON_UNESCAPED_UNICODE
                        |
                        JSON_HEX_TAG
                        |
                        JSON_HEX_AMP
                        |
                        JSON_HEX_APOS
                        |
                        JSON_HEX_QUOT
                    );
                    ?>,

                datasets:[
                    {
                        label:'Importe',

                        data:
                            <?php
                            echo json_encode(
                                $graficoConceptos['valores']
                            );
                            ?>,

                        borderWidth:1
                    }
                ]
            },

            options:{
                responsive:true,
                maintainAspectRatio:false,

                plugins:{
                    tooltip:{
                        callbacks:{
                            label:function(context){

                                const valores =
                                    context.dataset.data;

                                const total =
                                    valores.reduce(
                                        function(acumulado, valor){

                                            return acumulado
                                                +
                                                Number(valor || 0);
                                        },
                                        0
                                    );

                                const valorActual =
                                    Number(
                                        context.raw
                                        || 0
                                    );

                                const porcentaje =
                                    total > 0
                                        ? (
                                            valorActual
                                            /
                                            total
                                        ) * 100
                                        : 0;

                                return context.label
                                    +
                                    ': '
                                    +
                                    formatoPesos(
                                        valorActual
                                    )
                                    +
                                    ' ('
                                    +
                                    porcentaje.toLocaleString(
                                        'es-AR',
                                        {
                                            minimumFractionDigits:2,
                                            maximumFractionDigits:2
                                        }
                                    )
                                    +
                                    '%)';
                            }
                        }
                    }
                }
            }
        }
    );

<?php endif; ?>


/*
|--------------------------------------------------------------------------
| PRINCIPALES DESCUENTOS
|--------------------------------------------------------------------------
*/

<?php if (
    !empty(
        $graficoDescuentos['labels']
        ?? []
    )
): ?>

charts.descuentos =
    new Chart(
        document.getElementById(
            'graficoDescuentos'
        ),
        {
            type:'bar',

            data:{
                labels:
                    <?php
                    echo json_encode(
                        $graficoDescuentos['labels'],
                        JSON_UNESCAPED_UNICODE
                        |
                        JSON_HEX_TAG
                        |
                        JSON_HEX_AMP
                        |
                        JSON_HEX_APOS
                        |
                        JSON_HEX_QUOT
                    );
                    ?>,

                datasets:[
                    {
                        label:'Total descuentos',

                        data:
                            <?php
                            echo json_encode(
                                $graficoDescuentos['valores']
                            );
                            ?>,

                        borderWidth:1
                    }
                ]
            },

            options:{
                indexAxis:'y',
                responsive:true,
                maintainAspectRatio:false,

                plugins:{
                    legend:{
                        display:false
                    },

                    tooltip:{
                        callbacks:{
                            label:function(context){

                                return formatoPesos(
                                    context.raw
                                );
                            }
                        }
                    }
                },

                scales:{
                    x:{
                        beginAtZero:true,

                        ticks:{
                            callback:function(valor){

                                return '$ '
                                    +
                                    Number(valor)
                                        .toLocaleString(
                                            'es-AR'
                                        );
                            }
                        }
                    }
                }
            }
        }
    );

<?php endif; ?>


/*
|--------------------------------------------------------------------------
| EXPORTAR PDF
|--------------------------------------------------------------------------
|
| Se envían:
|
| - imagenes: array posicional compatible con el PDF actual;
| - graficos: objeto identificado por nombre para la próxima versión del PDF.
|
| El array posicional conserva los lugares vacíos con null. De esa manera,
| aunque un gráfico no tenga datos, los títulos no se desplazan.
|
|--------------------------------------------------------------------------
*/

function exportarPDF(){

    const ordenGraficos = [
        'categoria',
        'periodo',
        'conceptos',
        'descuentos'
    ];


    const imagenes =
        ordenGraficos.map(
            function(clave){

                return charts[clave]
                    ? charts[clave].toBase64Image()
                    : null;
            }
        );


    const graficos = {};


    ordenGraficos.forEach(
        function(clave){

            if (charts[clave]) {

                graficos[clave] =
                    imagenGraficoParaExcel(
                        charts[clave]
                    );
            }
        }
    );


    const urlPdf =
        <?php
        echo json_encode(
            $estadisticasUrlPdf,
            JSON_UNESCAPED_SLASHES
            |
            JSON_UNESCAPED_UNICODE
            |
            JSON_HEX_TAG
            |
            JSON_HEX_AMP
            |
            JSON_HEX_APOS
            |
            JSON_HEX_QUOT
        );
        ?>;


    fetch(
        urlPdf,
        {
            method:'POST',

            headers:{
                'Content-Type':
                    'application/json'
            },

            body:
                JSON.stringify(
                    {
                        imagenes:
                            imagenes,

                        graficos:
                            graficos
                    }
                )
        }
    )
    .then(
        function(respuesta){

            if (!respuesta.ok){

                return respuesta
                    .text()
                    .then(
                        function(texto){

                            throw new Error(
                                texto
                                ||
                                'No se pudo generar el PDF.'
                            );
                        }
                    );
            }

            return respuesta.blob();
        }
    )
    .then(
        function(blob){

            const url =
                URL.createObjectURL(
                    blob
                );


            window.open(
                url,
                '_blank'
            );


            setTimeout(
                function(){

                    URL.revokeObjectURL(
                        url
                    );
                },
                60000
            );
        }
    )
    .catch(
        function(error){

            alert(
                error.message
            );
        }
    );
}


/*
|--------------------------------------------------------------------------
| IMAGEN DE GRÁFICO PARA EXCEL
|--------------------------------------------------------------------------
|
| Chart.js genera el canvas con fondo transparente. Para que el fondo viaje
| JUNTO con la imagen al moverla dentro de Excel, creamos un segundo canvas:
|
| 1. dibujamos un fondo gris muy claro;
| 2. copiamos encima el gráfico original;
| 3. agregamos un borde suave;
| 4. exportamos ese canvas como PNG.
|
| Así el fondo forma parte real de la imagen y no queda pegado a las celdas.
|
|--------------------------------------------------------------------------
*/

function imagenGraficoParaExcel(chart){

    const canvasOriginal =
        chart.canvas;


    const canvasExportacion =
        document.createElement(
            'canvas'
        );


    canvasExportacion.width =
        canvasOriginal.width;


    canvasExportacion.height =
        canvasOriginal.height;


    const contexto =
        canvasExportacion.getContext(
            '2d'
        );


    /*
    |--------------------------------------------------------------------------
    | FONDO
    |--------------------------------------------------------------------------
    */

    contexto.fillStyle =
        '#F3F4F6';


    contexto.fillRect(
        0,
        0,
        canvasExportacion.width,
        canvasExportacion.height
    );


    /*
    |--------------------------------------------------------------------------
    | GRÁFICO
    |--------------------------------------------------------------------------
    */

    contexto.drawImage(
        canvasOriginal,
        0,
        0
    );


    /*
    |--------------------------------------------------------------------------
    | BORDE
    |--------------------------------------------------------------------------
    */

    const escala =
        window.devicePixelRatio
        || 1;


    const grosor =
        Math.max(
            2,
            Math.round(
                escala
            )
        );


    contexto.strokeStyle =
        '#D1D5DB';


    contexto.lineWidth =
        grosor;


    contexto.strokeRect(
        grosor / 2,
        grosor / 2,
        canvasExportacion.width - grosor,
        canvasExportacion.height - grosor
    );


    return canvasExportacion
        .toDataURL(
            'image/png'
        );
}


/*
|--------------------------------------------------------------------------
| EXPORTAR EXCEL
|--------------------------------------------------------------------------
|
| El XLSX consulta nuevamente los datos desde la base de datos.
| Desde el navegador se envían solamente las imágenes de los gráficos.
|
|--------------------------------------------------------------------------
*/

function exportarExcel(){

    const ordenGraficos = [
        'categoria',
        'periodo',
        'conceptos',
        'descuentos'
    ];


    const graficos = {};


    ordenGraficos.forEach(
        function(clave){

            if (charts[clave]) {

                graficos[clave] =
                    charts[clave]
                        .toBase64Image();
            }
        }
    );


    const urlExcel =
        <?php
        echo json_encode(
            $estadisticasUrlExcel,
            JSON_UNESCAPED_SLASHES
            |
            JSON_UNESCAPED_UNICODE
            |
            JSON_HEX_TAG
            |
            JSON_HEX_AMP
            |
            JSON_HEX_APOS
            |
            JSON_HEX_QUOT
        );
        ?>;


    fetch(
        urlExcel,
        {
            method:'POST',

            headers:{
                'Content-Type':
                    'application/json'
            },

            body:
                JSON.stringify(
                    {
                        graficos:
                            graficos
                    }
                )
        }
    )
    .then(
        function(respuesta){

            if (!respuesta.ok){

                return respuesta
                    .text()
                    .then(
                        function(texto){

                            throw new Error(
                                texto
                                ||
                                'No se pudo generar el archivo Excel.'
                            );
                        }
                    );
            }


            const disposicion =
                respuesta.headers.get(
                    'Content-Disposition'
                )
                || '';


            let nombreArchivo =
                'estadisticas_sigenmuni.xlsx';


            const coincidencia =
                disposicion.match(
                    /filename="?([^";]+)"?/i
                );


            if (
                coincidencia
                &&
                coincidencia[1]
            ) {

                nombreArchivo =
                    coincidencia[1];
            }


            return respuesta
                .blob()
                .then(
                    function(blob){

                        return {
                            blob:
                                blob,

                            nombre:
                                nombreArchivo
                        };
                    }
                );
        }
    )
    .then(
        function(resultado){

            const url =
                URL.createObjectURL(
                    resultado.blob
                );


            const enlace =
                document.createElement(
                    'a'
                );


            enlace.href =
                url;


            enlace.download =
                resultado.nombre;


            document.body.appendChild(
                enlace
            );


            enlace.click();


            enlace.remove();


            setTimeout(
                function(){

                    URL.revokeObjectURL(
                        url
                    );
                },
                60000
            );
        }
    )
    .catch(
        function(error){

            alert(
                error.message
            );
        }
    );
}

</script>

</body>

</html>
