<?php

/*
|--------------------------------------------------------------------------
| REPORTE DE EMPLEADOS - VISTA EXCEL
|--------------------------------------------------------------------------
|
| Los datos y filtros son preparados por ReporteControlador->empleadosExcel().
| Esta vista conserva el formato HTML compatible con Excel (.xls).
|
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| TEXTO ESTADO
|--------------------------------------------------------------------------
*/

function textoEstadoFiltroReporteEmpleadosExcel(
    $estado
) {

    if ($estado === "1") {
        return "Activos";
    }

    if ($estado === "0") {
        return "Inactivos";
    }

    return "Todos";
}


/*
|--------------------------------------------------------------------------
| FORMATEAR CUIT DE UNIDAD DE ORGANIZACIÓN
|--------------------------------------------------------------------------
*/

function formatearCuitUnidadOrganizacionReporteEmpleadosExcel($cuit)
{
    $cuit =
        preg_replace(
            '/\D+/',
            '',
            (string)$cuit
        );


    if (strlen($cuit) !== 11) {

        return (string)$cuit;
    }


    return
        substr($cuit, 0, 2)
        . '-'
        . substr($cuit, 2, 8)
        . '-'
        . substr($cuit, 10, 1);
}


/*
|--------------------------------------------------------------------------
| NOMBRE DEL ARCHIVO
|--------------------------------------------------------------------------
*/

$nombreArchivo =
    "reporte_empleados_"
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
    Reporte de Empleados
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
    background:#d9ead3;
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
    font-weight:bold;
    background:#ecfeff;
}


.unidad-organizacion{
    min-width:190px;
}


.nombre-unidad{
    font-weight:bold;
}


.cuit-unidad{
    margin-top:3px;
    color:#475569;
    font-size:11px;
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

            SIGENMUNI - Reporte de Empleados

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


    <!-- =====================================
         FILTROS
    ====================================== -->

    <tr>

        <td
            colspan="13"
            class="subtitulo"
        >

            <strong>
                Filtros aplicados:
            </strong>

            Legajo =

            <?php

            echo htmlspecialchars(
                $legajo !== ''
                    ?
                    $legajo
                    :
                    'Todos',
                ENT_QUOTES,
                'UTF-8'
            );

            ?>

            |

            Búsqueda general =

            <?php

            echo htmlspecialchars(
                $busqueda !== ''
                    ?
                    $busqueda
                    :
                    'Sin filtro',
                ENT_QUOTES,
                'UTF-8'
            );

            ?>

            |

            Estado =

            <?php

            echo htmlspecialchars(
                textoEstadoFiltroReporteEmpleadosExcel(
                    $estado
                ),
                ENT_QUOTES,
                'UTF-8'
            );

            ?>

        </td>

    </tr>


    <!-- =====================================
         TOTAL
    ====================================== -->

    <tr>

        <td
            colspan="13"
            class="total"
        >

            Total de empleados encontrados:

            <?php
            echo (int)$totalEmpleados;
            ?>

        </td>

    </tr>


</table>


<br>


<!-- =========================================
     DATOS
========================================= -->

<table>


    <thead>


        <tr>

            <th>
                Legajo
            </th>

            <th>
                Apellido
            </th>

            <th>
                Nombre
            </th>

            <th>
                DNI
            </th>

            <th>
                CUIL
            </th>

            <th>
                Teléfono
            </th>

            <th>
                Email
            </th>

            <th>
                Fecha Alta
            </th>

            <th>
                Fecha Baja
            </th>

            <th>
                Categoría
            </th>

            <th>
                Unidad de Organización
            </th>

            <th>
                Situación
            </th>

            <th>
                Estado
            </th>

        </tr>


    </thead>


    <tbody>


    <?php if (!empty($empleados)): ?>


        <?php foreach ($empleados as $fila): ?>


            <tr>


                <!-- LEGAJO -->

                <td>

                    <?php
                    echo htmlspecialchars(
                        $fila['nro_legajo']
                        ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>

                </td>


                <!-- APELLIDO -->

                <td>

                    <?php
                    echo htmlspecialchars(
                        $fila['apellido']
                        ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>

                </td>


                <!-- NOMBRE -->

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


                <!-- DNI -->

                <td>

                    <?php
                    echo htmlspecialchars(
                        $fila['dni']
                        ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>

                </td>


                <!-- CUIL -->

                <td>

                    <?php
                    echo htmlspecialchars(
                        $fila['cuil']
                        ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>

                </td>


                <!-- TELÉFONO -->

                <td>

                    <?php
                    echo htmlspecialchars(
                        $fila['telefono']
                        ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>

                </td>


                <!-- EMAIL -->

                <td>

                    <?php
                    echo htmlspecialchars(
                        $fila['email']
                        ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>

                </td>


                <!-- FECHA ALTA -->

                <td>

                    <?php

                    echo !empty(
                        $fila['fecha_alta']
                    )
                        ?
                        date(
                            "d/m/Y",
                            strtotime(
                                $fila['fecha_alta']
                            )
                        )
                        :
                        '';

                    ?>

                </td>


                <!-- FECHA BAJA -->

                <td>

                    <?php

                    echo !empty(
                        $fila['fecha_baja']
                    )
                        ?
                        date(
                            "d/m/Y",
                            strtotime(
                                $fila['fecha_baja']
                            )
                        )
                        :
                        '';

                    ?>

                </td>


                <!-- CATEGORÍA -->

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


                <!-- UNIDAD DE ORGANIZACIÓN -->

                <td class="unidad-organizacion">

                    <div class="nombre-unidad">

                        <?php

                        echo htmlspecialchars(
                            $fila['oficina']
                            ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        );

                        ?>

                    </div>


                    <?php if (!empty($fila['oficina_cuit'])): ?>

                        <div class="cuit-unidad">

                            CUIT:
                            <?php

                            echo htmlspecialchars(
                                formatearCuitUnidadOrganizacionReporteEmpleadosExcel(
                                    $fila['oficina_cuit']
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            );

                            ?>

                        </div>

                    <?php endif; ?>

                </td>


                <!-- SITUACIÓN -->

                <td>

                    <?php
                    echo htmlspecialchars(
                        $fila['situacion']
                        ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>

                </td>


                <!-- ESTADO -->

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


            </tr>


        <?php endforeach; ?>


    <?php else: ?>


        <tr>

            <td colspan="13">

                No se encontraron empleados
                con los filtros seleccionados.

            </td>

        </tr>


    <?php endif; ?>


    </tbody>


</table>


</body>

</html>