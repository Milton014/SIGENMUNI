<?php

require_once __DIR__ . '/../../../core/Url.php';


/*
|--------------------------------------------------------------------------
| REPORTE DE CONCEPTOS - VISTA IMPRESIÓN / PDF - ROUTER
|--------------------------------------------------------------------------
*/

$reporteConceptosPdfFiltros = [
    'buscar' =>
        $buscar,

    'tipo_concepto' =>
        $tipoConcepto,

    'activo' =>
        $activo
];


$reporteConceptosPdfUrlVolver =
    sigenmuniUrlRuta(
        'reportes/conceptos',
        $reporteConceptosPdfFiltros
    );


function textoEstadoFiltro($activo)
{
    if ($activo === '1') {

        return 'Activos';
    }


    if ($activo === '0') {

        return 'Inactivos';
    }


    return 'Todos';
}


function textoTipoConceptoFiltro($tipoConcepto)
{
    $nombres = [

        'REMUNERATIVO' =>
            'Remunerativo',

        'NO_REMUNERATIVO' =>
            'No remunerativo',

        'ASIGNACION_FAMILIAR' =>
            'Asignación familiar',

        'DESCUENTO' =>
            'Descuento',

        'APORTE_PATRONAL' =>
            'Aporte patronal'
    ];


    if (
        $tipoConcepto !== ''
        &&
        isset(
            $nombres[$tipoConcepto]
        )
    ) {

        return $nombres[$tipoConcepto];
    }


    return 'Todos';
}


/*
|--------------------------------------------------------------------------
| TEXTO FORMA DE CÁLCULO
|--------------------------------------------------------------------------
*/

function textoFormaCalculo(
    $formaCalculo
) {

    $formaCalculo =
        strtoupper(
            trim(
                (string)$formaCalculo
            )
        );


    switch ($formaCalculo) {

        case 'TABLA_CATEGORIA':

            return 'VALOR POR CATEGORÍA';


        case 'MANUAL':
        case 'FIJO':

            return 'MANUAL';


        case 'PORCENTAJE':

            return 'PORCENTAJE';


        case 'FORMULA':
        case 'AUTOMATICO':
        case 'AUTOMÁTICO':

            return 'AUTOMÁTICO';


        default:

            return $formaCalculo !== ''
                ?
                $formaCalculo
                :
                '-';
    }
}

?>
<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Reporte de Conceptos - PDF</title>

<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/reportes/reporte_conceptos_pdf.css'), ENT_QUOTES, 'UTF-8') ?>">

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
                    $reporteConceptosPdfUrlVolver,
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
            data-event-click="reporte_conceptos_pdf-1"
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
            Reporte de Conceptos
        </h1>

        <p>
            <strong>SIGENMUNI</strong>
            - Municipalidad de Fortín Lugones
        </p>

        <p>
            Fecha de emisión:
            <?php echo date("d/m/Y H:i:s"); ?>
        </p>


        <div class="resumen">

            <div class="info-box">

                <strong>
                    Búsqueda aplicada
                </strong>

                <?php
                echo htmlspecialchars(
                    $buscar !== ''
                        ? $buscar
                        : 'Todos',
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>

            </div>


            <div class="info-box">

                <strong>
                    Tipo de concepto
                </strong>

                <?php
                echo htmlspecialchars(
                    textoTipoConceptoFiltro(
                        $tipoConcepto
                    ),
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
                    textoEstadoFiltro($activo),
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>

            </div>


            <div class="info-box">

                <strong>
                    Total de conceptos
                </strong>

                <?php
                echo (int)$totalConceptos;
                ?>

            </div>

        </div>

    </div>


    <!-- =====================================
         TABLA
    ====================================== -->

    <?php if (!empty($conceptos)): ?>

        <div class="tabla-contenedor">

            <table>

                <thead>

                    <tr>

                        <th class="col-codigo">
                            Código
                        </th>

                        <th class="col-nombre">
                            Nombre
                        </th>

                        <th class="col-categoria">
                            Tipo de concepto
                        </th>

                        <th class="col-forma">
                            Forma Cálculo
                        </th>

                        <th class="col-porcentaje">
                            %
                        </th>

                        <th class="col-monto">
                            Monto Fijo
                        </th>
<th class="col-base">
                            Base Cálculo
                        </th>

                        <th class="col-estado">
                            Estado
                        </th>

                        <th class="col-vigencia">
                            Vigencia
                        </th>

                    </tr>

                </thead>


                <tbody>

                <?php foreach ($conceptos as $fila): ?>

                    <?php

                    $badgeClass =
                        'badge-default';


                    switch (
                        $fila['categoria']
                        ?? ''
                    ) {

                        case 'REMUNERATIVO':
                            $badgeClass = 'badge-rem';
                            break;

                        case 'NO_REMUNERATIVO':
                            $badgeClass = 'badge-no-rem';
                            break;

                        case 'ASIGNACION_FAMILIAR':
                            $badgeClass = 'badge-asig';
                            break;

                        case 'DESCUENTO':
                            $badgeClass = 'badge-desc';
                            break;

                        case 'APORTE_PATRONAL':
                            $badgeClass = 'badge-aporte';
                            break;
                    }


                    $desde =
                        !empty(
                            $fila['fecha_desde']
                        )
                            ?
                            date(
                                "d/m/Y",
                                strtotime(
                                    $fila['fecha_desde']
                                )
                            )
                            :
                            '-';


                    $hasta =
                        !empty(
                            $fila['fecha_hasta']
                        )
                            ?
                            date(
                                "d/m/Y",
                                strtotime(
                                    $fila['fecha_hasta']
                                )
                            )
                            :
                            '-';


                    $porcentaje =
                        (float)(
                            $fila['porcentaje']
                            ?? 0
                        );


                    $porcentajeTexto =
                        $porcentaje != 0
                            ?
                            number_format(
                                $porcentaje,
                                2,
                                ',',
                                '.'
                            )
                            . ' %'
                            :
                            '-';


                    $montoFijo =
                        (float)(
                            $fila['monto_fijo']
                            ?? 0
                        );


                    $montoFijoTexto =
                        $montoFijo != 0
                            ?
                            '$ '
                            .
                            number_format(
                                $montoFijo,
                                2,
                                ',',
                                '.'
                            )
                            :
                            '-';


                    $baseCalculo =
                        trim(
                            $fila['base_calculo']
                            ?? ''
                        );


                    $baseCalculoTexto =
                        $baseCalculo !== ''
                            ?
                            $baseCalculo
                            :
                            '-';

                    ?>


                    <tr>


                        <td class="col-codigo">

                            <?php
                            echo htmlspecialchars(
                                $fila['codigo']
                                ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>

                        </td>


                        <td class="col-nombre">

                            <?php
                            echo htmlspecialchars(
                                $fila['nombre']
                                ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>

                        </td>


                        <td class="col-categoria">

                            <span
                                class="badge <?php
                                    echo $badgeClass;
                                ?>"
                            >

                                <?php
                                echo htmlspecialchars(
                                    $fila['categoria']
                                    ?? '',
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </span>

                        </td>


                        <td class="col-forma">

                            <?php
                            echo htmlspecialchars(
                                textoFormaCalculo(
                                    $fila['forma_calculo']
                                    ?? ''
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>

                        </td>


                        <td class="col-porcentaje">

                            <?php
                            echo htmlspecialchars(
                                $porcentajeTexto,
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>

                        </td>


                        <td class="col-monto">

                            <?php
                            echo htmlspecialchars(
                                $montoFijoTexto,
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>

                        </td>
<td class="col-base">

                            <?php
                            echo htmlspecialchars(
                                $baseCalculoTexto,
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>

                        </td>


                        <td class="col-estado">

                            <?php if (
                                (int)(
                                    $fila['activo']
                                    ?? 0
                                )
                                === 1
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


                        <td class="col-vigencia">

                            <?php
                            echo htmlspecialchars(
                                $desde
                                .
                                ' / '
                                .
                                $hasta,
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

    <?php else: ?>

        <div class="sin-registros">

            No se encontraron conceptos
            con los filtros seleccionados.

        </div>

    <?php endif; ?>


</div>

<script src="<?= htmlspecialchars(sigenmuniUrlAsset('js/reportes/reporte_conceptos_pdf.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
</body>

</html>
