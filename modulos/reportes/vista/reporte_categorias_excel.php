<?php

/*
|--------------------------------------------------------------------------
| REPORTE DE CATEGORÍAS - EXPORTAR EXCEL - ROUTER
|--------------------------------------------------------------------------
|
| Los datos ($buscar, $activo, $categorias y $totalCategorias)
| son preparados por ReporteControlador::categoriasExcel().
|
|--------------------------------------------------------------------------
*/


function textoEstadoCategoria(
    $activo
) {

    if ($activo === '1') {

        return 'Activas';
    }


    if ($activo === '0') {

        return 'Inactivas';
    }


    return 'Todas';
}


/*
|--------------------------------------------------------------------------
| NOMBRE DEL ARCHIVO
|--------------------------------------------------------------------------
*/

$nombreArchivo =
    "reporte_categorias_"
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
    Reporte de Categorías
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
    background:#ede9fe;
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
    background:#f5f3ff;
    font-weight:bold;
}


.sin-valor{
    background:#f1f5f9;
    color:#64748b;
    font-weight:bold;
}


.configuracion-completa{
    background:#dcfce7;
    color:#166534;
    font-weight:bold;
}


.configuracion-incompleta{
    background:#fef3c7;
    color:#92400e;
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
            colspan="7"
            class="titulo"
        >

            SIGENMUNI - Reporte de Categorías

        </td>

    </tr>


    <tr>

        <td
            colspan="7"
            class="subtitulo"
        >

            Municipalidad de Fortín Lugones

        </td>

    </tr>


    <tr>

        <td
            colspan="7"
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
            colspan="7"
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

            Estado =

            <?php
            echo htmlspecialchars(
                textoEstadoCategoria(
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
            colspan="7"
            class="total"
        >

            Total de categorías encontradas:

            <?php
            echo (int)$totalCategorias;
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
                Categoría
            </th>

            <th>
                Sueldo Básico
            </th>

            <th>
                Dedicación Funcional
            </th>

            <th>
                Suplemento Especial
            </th>

            <th>
                Configuración
            </th>

            <th>
                Estado
            </th>

        </tr>

    </thead>


    <tbody>


    <?php if (!empty($categorias)): ?>


        <?php foreach ($categorias as $fila): ?>


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


                <td
                    class="<?php
                        echo (
                            array_key_exists(
                                'sueldo_basico',
                                $fila
                            )
                            &&
                            $fila['sueldo_basico'] !== null
                        )
                            ?
                            ''
                            :
                            'sin-valor';
                    ?>"
                >

                    <?php if (
                        array_key_exists(
                            'sueldo_basico',
                            $fila
                        )
                        &&
                        $fila['sueldo_basico'] !== null
                    ): ?>

                        $ <?php
                        echo number_format(
                            (float)$fila['sueldo_basico'],
                            2,
                            ',',
                            '.'
                        );
                        ?>

                    <?php else: ?>

                        Sin valor vigente

                    <?php endif; ?>

                </td>


                <td
                    class="<?php
                        echo (
                            array_key_exists(
                                'dedicacion_funcional',
                                $fila
                            )
                            &&
                            $fila['dedicacion_funcional'] !== null
                        )
                            ?
                            ''
                            :
                            'sin-valor';
                    ?>"
                >

                    <?php if (
                        array_key_exists(
                            'dedicacion_funcional',
                            $fila
                        )
                        &&
                        $fila['dedicacion_funcional'] !== null
                    ): ?>

                        $ <?php
                        echo number_format(
                            (float)$fila['dedicacion_funcional'],
                            2,
                            ',',
                            '.'
                        );
                        ?>

                    <?php else: ?>

                        Sin valor vigente

                    <?php endif; ?>

                </td>


                <td
                    class="<?php
                        echo (
                            array_key_exists(
                                'suplemento_especial',
                                $fila
                            )
                            &&
                            $fila['suplemento_especial'] !== null
                        )
                            ?
                            ''
                            :
                            'sin-valor';
                    ?>"
                >

                    <?php if (
                        array_key_exists(
                            'suplemento_especial',
                            $fila
                        )
                        &&
                        $fila['suplemento_especial'] !== null
                    ): ?>

                        $ <?php
                        echo number_format(
                            (float)$fila['suplemento_especial'],
                            2,
                            ',',
                            '.'
                        );
                        ?>

                    <?php else: ?>

                        Sin valor vigente

                    <?php endif; ?>

                </td>


                <?php

                $configuracionCompleta =
                    isset(
                        $fila['configuracion_completa']
                    )
                        ?
                        (int)$fila['configuracion_completa'] === 1
                        :
                        (
                            array_key_exists(
                                'sueldo_basico',
                                $fila
                            )
                            &&
                            $fila['sueldo_basico'] !== null
                            &&
                            array_key_exists(
                                'dedicacion_funcional',
                                $fila
                            )
                            &&
                            $fila['dedicacion_funcional'] !== null
                            &&
                            array_key_exists(
                                'suplemento_especial',
                                $fila
                            )
                            &&
                            $fila['suplemento_especial'] !== null
                        );

                ?>


                <td
                    class="<?php
                        echo $configuracionCompleta
                            ?
                            'configuracion-completa'
                            :
                            'configuracion-incompleta';
                    ?>"
                >

                    <?php
                    echo $configuracionCompleta
                        ?
                        'Completa'
                        :
                        'Incompleta';
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
                        'Activa'
                        :
                        'Inactiva';
                    ?>

                </td>


            </tr>


        <?php endforeach; ?>


    <?php else: ?>


        <tr>

            <td colspan="7">

                No se encontraron categorías
                con los filtros seleccionados.

            </td>

        </tr>


    <?php endif; ?>


    </tbody>


</table>


</body>

</html>
