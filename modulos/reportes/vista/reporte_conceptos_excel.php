<?php

/*
|--------------------------------------------------------------------------
| REPORTE DE CONCEPTOS - VISTA EXPORTAR EXCEL - ROUTER
|--------------------------------------------------------------------------
*/

function textoEstadoFiltro(
    $activo
) {

    if ($activo === '1') {

        return 'Activos';
    }


    if ($activo === '0') {

        return 'Inactivos';
    }


    return 'Todos';
}


function textoTipoConceptoFiltro(
    $tipoConcepto
) {

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


/*
|--------------------------------------------------------------------------
| NOMBRE DEL ARCHIVO
|--------------------------------------------------------------------------
*/

$nombreArchivo =
    "reporte_conceptos_"
    .
    date(
        "Ymd_His"
    )
    .
    ".xls";


/*
|--------------------------------------------------------------------------
| HEADERS EXCEL
|--------------------------------------------------------------------------
*/

header(
    "Content-Type: application/vnd.ms-excel; charset=UTF-8"
);

header(
    'Content-Disposition: attachment; filename="'
    .
    $nombreArchivo
    .
    '"'
);

header(
    "Pragma: no-cache"
);

header(
    "Expires: 0"
);


/*
|--------------------------------------------------------------------------
| BOM UTF-8
|--------------------------------------------------------------------------
*/

echo "\xEF\xBB\xBF";

?>
<!DOCTYPE html>

<html lang="es">

<head>

<meta charset="UTF-8">

<title>
    Reporte de Conceptos
</title>

<style>

table{
    border-collapse:collapse;
    width:100%;
}


th,
td{
    border:1px solid #000;
    padding:6px;
    font-size:12px;
    vertical-align:top;
}


th{
    background:#dbeafe;
    font-weight:bold;
}


.titulo{
    font-size:16px;
    font-weight:bold;
}


.subtitulo{
    font-size:12px;
}


.total{
    background:#ecfeff;
    font-weight:bold;
}

</style>

</head>


<body>


<!-- =========================================
     ENCABEZADO
========================================= -->

<table>


    <tr>

        <td
            colspan="9"
            class="titulo"
        >
            SIGENMUNI - Reporte de Conceptos
        </td>

    </tr>


    <tr>

        <td
            colspan="9"
            class="subtitulo"
        >
            Municipalidad de Fortín Lugones
        </td>

    </tr>


    <tr>

        <td
            colspan="9"
            class="subtitulo"
        >

            Fecha de emisión:

            <?php
            echo date(
                "d/m/Y H:i:s"
            );
            ?>

        </td>

    </tr>


    <tr>

        <td
            colspan="9"
            class="subtitulo"
        >

            <strong>
                Filtros:
            </strong>

            Búsqueda =

            <?php
            echo htmlspecialchars(
                $buscar !== ''
                    ?
                    $buscar
                    :
                    'Todos',
                ENT_QUOTES,
                'UTF-8'
            );
            ?>

            |

            Tipo de concepto =

            <?php
            echo htmlspecialchars(
                textoTipoConceptoFiltro(
                    $tipoConcepto
                ),
                ENT_QUOTES,
                'UTF-8'
            );
            ?>

            |

            Estado =

            <?php
            echo htmlspecialchars(
                textoEstadoFiltro(
                    $activo
                ),
                ENT_QUOTES,
                'UTF-8'
            );
            ?>

        </td>

    </tr>


    <tr>

        <td
            colspan="9"
            class="total"
        >

            Total de conceptos encontrados:

            <?php
            echo (int)$totalConceptos;
            ?>

        </td>

    </tr>


</table>


<br>


<!-- =========================================
     TABLA
========================================= -->

<table>


    <thead>

        <tr>

            <th>
                Código
            </th>

            <th>
                Nombre
            </th>

            <th>
                Tipo de concepto
            </th>

            <th>
                Forma Cálculo
            </th>

            <th>
                %
            </th>

            <th>
                Monto Fijo
            </th>
<th>
                Base Cálculo
            </th>

            <th>
                Estado
            </th>

            <th>
                Vigencia
            </th>

        </tr>

    </thead>


    <tbody>


    <?php if (!empty($conceptos)): ?>


        <?php foreach ($conceptos as $fila): ?>


            <?php

            /*
            |--------------------------------------------------------------------------
            | VIGENCIA
            |--------------------------------------------------------------------------
            */

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
                    '';


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
                    '';


            /*
            |--------------------------------------------------------------------------
            | PORCENTAJE
            |--------------------------------------------------------------------------
            */

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
                    :
                    '';


            /*
            |--------------------------------------------------------------------------
            | MONTO FIJO
            |--------------------------------------------------------------------------
            */

            $montoFijo =
                (float)(
                    $fila['monto_fijo']
                    ?? 0
                );


            $montoFijoTexto =
                $montoFijo != 0
                    ?
                    number_format(
                        $montoFijo,
                        2,
                        ',',
                        '.'
                    )
                    :
                    '';


            /*
            |--------------------------------------------------------------------------
            | BASE DE CÁLCULO
            |--------------------------------------------------------------------------
            */

            $baseCalculoTexto =
                trim(
                    $fila['base_calculo']
                    ?? ''
                );

            ?>


            <tr>


                <td>

                    <?php
                    echo htmlspecialchars(
                        $fila['codigo']
                        ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>

                </td>


                <td>

                    <?php
                    echo htmlspecialchars(
                        $fila['nombre']
                        ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>

                </td>


                <td>

                    <?php
                    echo htmlspecialchars(
                        $fila['categoria']
                        ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>

                </td>


                <td>

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


                <td>

                    <?php
                    echo htmlspecialchars(
                        $porcentajeTexto,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>

                </td>


                <td>

                    <?php
                    echo htmlspecialchars(
                        $montoFijoTexto,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>

                </td>
<td>

                    <?php
                    echo htmlspecialchars(
                        $baseCalculoTexto,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>

                </td>


                <td>

                    <?php
                    echo (
                        (int)(
                            $fila['activo']
                            ?? 0
                        )
                        ===
                        1
                    )
                        ?
                        'Activo'
                        :
                        'Inactivo';
                    ?>

                </td>


                <td>

                    <?php

                    if (
                        $desde !== ''
                        ||
                        $hasta !== ''
                    ) {

                        echo htmlspecialchars(
                            (
                                $desde !== ''
                                    ?
                                    $desde
                                    :
                                    '-'
                            )
                            .
                            ' / '
                            .
                            (
                                $hasta !== ''
                                    ?
                                    $hasta
                                    :
                                    '-'
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    }

                    ?>

                </td>


            </tr>


        <?php endforeach; ?>


    <?php else: ?>


        <tr>

            <td colspan="9">

                No se encontraron conceptos
                con los filtros seleccionados.

            </td>

        </tr>


    <?php endif; ?>


    </tbody>


</table>


</body>

</html>
