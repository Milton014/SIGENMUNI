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

<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/reportes/reporte_liquidaciones.css'), ENT_QUOTES, 'UTF-8') ?>">

<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/compartido/paginacion.css'), ENT_QUOTES, 'UTF-8') ?>">
<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/reportes/paginacion.css'), ENT_QUOTES, 'UTF-8') ?>">
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

            <input type="hidden" name="por_pagina" value="<?= $paginacion['por_pagina'] ?>">
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


        <?php
        $paginacionRuta = 'reportes/liquidaciones';
        $paginacionParametros = $reporteLiquidacionesFiltros;
        $paginacionEntidad = 'liquidaciones';
        $paginacionClavePagina = 'pagina'; $paginacionClaveCantidad = 'por_pagina';
        require __DIR__ . '/../../../componentes/paginacion_reporte.php';
        ?>

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
            <?php require __DIR__ . '/../../../componentes/paginacion.php'; ?>

        <?php else: ?>

            <div class="sin-registros">

                No se encontraron liquidaciones
                con los filtros seleccionados.

            </div>

        <?php endif; ?>

    </div>

</div>

<script src="<?= htmlspecialchars(sigenmuniUrlAsset('js/reportes/paginacion.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
</body>

</html>