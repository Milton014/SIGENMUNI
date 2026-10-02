<?php

require_once __DIR__ . '/../../../core/Url.php';


/*
|--------------------------------------------------------------------------
| REPORTE DE LIQUIDACIONES - IMPRESIÓN / PDF - ROUTER
|--------------------------------------------------------------------------
|
| Los datos son preparados por ReporteControlador::liquidacionesPdf().
|
|--------------------------------------------------------------------------
*/

function textoEstadoLiquidacion(
    $estado
) {

    if ($estado === '') {

        return 'Todos';
    }


    return $estado;
}


$reporteLiquidacionesPdfVolverUrl =
    sigenmuniUrlRuta(
        'reportes/liquidaciones',
        [
            'periodo' =>
                $periodo,

            'tipo' =>
                $tipo,

            'estado' =>
                $estado
        ]
    );

?>

<!DOCTYPE html>

<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>
    Reporte de Liquidaciones - PDF
</title>

<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/reportes/reporte_liquidaciones_pdf.css'), ENT_QUOTES, 'UTF-8') ?>">

</head>

<body>

<div class="contenedor">


    <!-- =====================================
         ACCIONES
    ====================================== -->

    <div class="acciones">

        <a
            href="<?php
                echo htmlspecialchars(
                    $reporteLiquidacionesPdfVolverUrl,
                    ENT_QUOTES,
                    'UTF-8'
                );
            ?>"
            class="btn btn-volver"
        >
            Volver
        </a>

        <button
            type="button"
            data-event-click="reporte_liquidaciones_pdf-1"
            class="btn btn-imprimir"
        >
            Imprimir / Guardar PDF
        </button>

    </div>


    <!-- =====================================
         ENCABEZADO
    ====================================== -->

    <div class="encabezado">

        <h1>
            Reporte de Liquidaciones
        </h1>

        <p>
            <strong>
                SIGENMUNI
            </strong>
            - Municipalidad de Fortín Lugones
        </p>

        <p>
            Fecha de emisión:
            <?php
            echo date(
                "d/m/Y H:i:s"
            );
            ?>
        </p>


        <div class="resumen">

            <div class="info-box">

                <strong>
                    Período
                </strong>

                <?php
                echo htmlspecialchars(
                    $periodo !== ''
                        ?
                        $periodo
                        :
                        'Todos',
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>

            </div>


            <div class="info-box">

                <strong>
                    Tipo
                </strong>

                <?php
                echo htmlspecialchars(
                    $tipo !== ''
                        ?
                        $tipo
                        :
                        'Todos',
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>

            </div>


            <div class="info-box">

                <strong>
                    Estado
                </strong>

                <?php
                echo htmlspecialchars(
                    textoEstadoLiquidacion(
                        $estado
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>

            </div>


            <div class="info-box">

                <strong>
                    Total de liquidaciones
                </strong>

                <?php
                echo (int)$totalLiquidaciones;
                ?>

            </div>

        </div>

    </div>


    <!-- =====================================
         TABLA
    ====================================== -->

    <?php if (!empty($liquidaciones)): ?>

        <div class="tabla-contenedor">

            <table>

                <thead>

                    <tr>

                        <th class="col-id">
                            ID
                        </th>

                        <th class="col-tipo">
                            Tipo
                        </th>

                        <th class="col-periodo">
                            Período
                        </th>

                        <th class="col-fecha">
                            Fecha Liquidación
                        </th>

                        <th class="col-descripcion">
                            Descripción
                        </th>

                        <th class="col-estado">
                            Estado
                        </th>

                        <th class="col-empleados">
                            Empleados
                        </th>

                        <th class="col-rem">
                            Remunerativo
                        </th>

                        <th class="col-desc">
                            Descuentos
                        </th>

                        <th class="col-no-rem">
                            No Remunerativo
                        </th>

                        <th class="col-asig">
                            Asignaciones
                        </th>

                        <th class="col-neto">
                            Neto
                        </th>

                        <th class="col-creada">
                            Creada
                        </th>

                    </tr>

                </thead>


                <tbody>

                <?php foreach ($liquidaciones as $fila): ?>

                    <?php

                    $estadoFila =
                        strtoupper(
                            trim(
                                $fila['estado']
                                ?? ''
                            )
                        );


                    switch ($estadoFila) {

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

                        <td class="col-id">

                            <?php
                            echo (int)(
                                $fila['id']
                                ?? 0
                            );
                            ?>

                        </td>


                        <td class="col-tipo">

                            <?php
                            echo htmlspecialchars(
                                $fila['tipo_liquidacion']
                                ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>

                        </td>


                        <td class="col-periodo">

                            <?php
                            echo htmlspecialchars(
                                $fila['periodo']
                                ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>

                        </td>


                        <td class="col-fecha">

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


                        <td class="col-descripcion">

                            <?php
                            echo htmlspecialchars(
                                !empty(
                                    $fila['descripcion']
                                )
                                    ?
                                    $fila['descripcion']
                                    :
                                    '-',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>

                        </td>


                        <td class="col-estado">

                            <span
                                class="estado-badge <?php
                                    echo $claseEstado;
                                ?>"
                            >

                                <?php
                                echo htmlspecialchars(
                                    $estadoFila,
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </span>

                        </td>


                        <td class="col-empleados">

                            <?php
                            echo (int)(
                                $fila['cantidad_empleados']
                                ?? 0
                            );
                            ?>

                        </td>


                        <td class="col-rem">

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


                        <td class="col-desc">

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


                        <td class="col-no-rem">

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


                        <td class="col-asig">

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


                        <td class="col-neto">

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


                        <td class="col-creada">

                            <?php
                            echo !empty(
                                $fila['created_at']
                            )
                                ?
                                date(
                                    "d/m/Y H:i",
                                    strtotime(
                                        $fila['created_at']
                                    )
                                )
                                :
                                '-';
                            ?>

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

<script src="<?= htmlspecialchars(sigenmuniUrlAsset('js/reportes/reporte_liquidaciones_pdf.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
</body>

</html>