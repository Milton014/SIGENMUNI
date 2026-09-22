<?php

/*
|--------------------------------------------------------------------------
| REPORTE DE LIQUIDACIONES - EXPORTAR EXCEL - ROUTER
|--------------------------------------------------------------------------
|
| Los datos son preparados por ReporteControlador::liquidacionesExcel().
|
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| NOMBRE DEL ARCHIVO
|--------------------------------------------------------------------------
*/

$nombreArchivo =
    "reporte_liquidaciones_"
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
    Reporte de Liquidaciones
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
    background:#dcfce7;
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
    background:#f0fdf4;
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
            colspan="13"
            class="titulo"
        >
            SIGENMUNI - Reporte de Liquidaciones
        </td>

    </tr>


    <tr>

        <td
            colspan="13"
            class="subtitulo"
        >
            Municipalidad de Fortín Lugones
        </td>

    </tr>


    <tr>

        <td
            colspan="13"
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
            colspan="13"
            class="subtitulo"
        >

            <strong>
                Filtros:
            </strong>

            Período =

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

            |

            Tipo =

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

            |

            Estado =

            <?php
            echo htmlspecialchars(
                $estado !== ''
                    ?
                    $estado
                    :
                    'Todos',
                ENT_QUOTES,
                'UTF-8'
            );
            ?>

        </td>

    </tr>


    <tr>

        <td
            colspan="13"
            class="total"
        >

            Total de liquidaciones encontradas:

            <?php
            echo (int)$totalLiquidaciones;
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
                ID
            </th>

            <th>
                Tipo
            </th>

            <th>
                Período
            </th>

            <th>
                Fecha Liquidación
            </th>

            <th>
                Descripción
            </th>

            <th>
                Estado
            </th>

            <th>
                Empleados
            </th>

            <th>
                Total Remunerativo
            </th>

            <th>
                Total Descuentos
            </th>

            <th>
                Total No Remunerativo
            </th>

            <th>
                Total Asignaciones
            </th>

            <th>
                Total Neto
            </th>

            <th>
                Creada
            </th>

        </tr>

    </thead>


    <tbody>


    <?php if (!empty($liquidaciones)): ?>


        <?php foreach ($liquidaciones as $fila): ?>


            <tr>


                <td>

                    <?php
                    echo (int)(
                        $fila['id']
                        ?? 0
                    );
                    ?>

                </td>


                <td>

                    <?php
                    echo htmlspecialchars(
                        $fila['tipo_liquidacion']
                        ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>

                </td>


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


                <td>

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
                        '';
                    ?>

                </td>


                <td>

                    <?php
                    echo htmlspecialchars(
                        $fila['descripcion']
                        ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>

                </td>


                <td>

                    <?php
                    echo htmlspecialchars(
                        strtoupper(
                            $fila['estado']
                            ?? ''
                        ),
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>

                </td>


                <td>

                    <?php
                    echo (int)(
                        $fila['cantidad_empleados']
                        ?? 0
                    );
                    ?>

                </td>


                <td>

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


                <td>

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


                <td>

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


                <td>

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


                <td>

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


                <td>

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
                        '';
                    ?>

                </td>


            </tr>


        <?php endforeach; ?>


    <?php else: ?>


        <tr>

            <td colspan="13">

                No se encontraron liquidaciones
                con los filtros seleccionados.

            </td>

        </tr>


    <?php endif; ?>


    </tbody>


</table>


</body>

</html>