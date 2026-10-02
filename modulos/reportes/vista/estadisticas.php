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

<script src="<?= htmlspecialchars(sigenmuniUrlAsset('js/vendor/chartjs/chart-4-5-1.js'), ENT_QUOTES, 'UTF-8') ?>"></script>

<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/reportes/estadisticas.css'), ENT_QUOTES, 'UTF-8') ?>">

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
            data-event-click="estadisticas-1"
        >
            Exportar PDF
        </button>


        <button
            type="button"
            class="btn btn-excel"
            data-event-click="estadisticas-2"
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


<script type="application/json" id="datos-estadisticas"><?= sigenmuniJsonVista([
    'graficoEmpleadosCategoria' => $graficoEmpleadosCategoria ?? ['labels' => [], 'valores' => []],
    'graficoNetoPeriodo' => $graficoNetoPeriodo ?? ['labels' => [], 'valores' => []],
    'graficoConceptos' => $graficoConceptos ?? ['labels' => [], 'valores' => []],
    'graficoDescuentos' => $graficoDescuentos ?? ['labels' => [], 'valores' => []],
    'estadisticasUrlPdf' => $estadisticasUrlPdf,
    'estadisticasUrlExcel' => $estadisticasUrlExcel
]) ?></script>
<script src="<?= htmlspecialchars(sigenmuniUrlAsset('js/reportes/estadisticas.js'), ENT_QUOTES, 'UTF-8') ?>"></script>

</body>

</html>
