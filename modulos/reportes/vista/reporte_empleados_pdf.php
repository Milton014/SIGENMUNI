<?php

require_once __DIR__ . '/../../../core/Url.php';


if (!function_exists('textoEstadoFiltroReporteEmpleados')) {

    function textoEstadoFiltroReporteEmpleados($estado)
    {
        if ($estado === '1') {
            return 'Activos';
        }

        if ($estado === '0') {
            return 'Inactivos';
        }

        return 'Todos';
    }
}


if (!function_exists('formatearCuitUnidadOrganizacion')) {

    function formatearCuitUnidadOrganizacion($cuit)
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
}


$reporteEmpleadosPdfUrlVolver =
    sigenmuniUrlRuta(
        'reportes/empleados',
        [
            'legajo' =>
                $legajo,

            'busqueda' =>
                $busqueda,

            'estado' =>
                $estado
        ]
    );

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>
    Reporte de Empleados - PDF
</title>


<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/reportes/reporte_empleados_pdf.css'), ENT_QUOTES, 'UTF-8') ?>">

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
                    $reporteEmpleadosPdfUrlVolver,
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
            data-event-click="reporte_empleados_pdf-1"
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
            Reporte de Empleados
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


        <!-- =================================
             FILTROS APLICADOS
        ================================== -->

        <div class="resumen">


            <!-- LEGAJO -->

            <div class="info-box">

                <strong>
                    Legajo
                </strong>


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

            </div>


            <!-- BÚSQUEDA GENERAL -->

            <div class="info-box">

                <strong>
                    Búsqueda general
                </strong>


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

            </div>


            <!-- ESTADO -->

            <div class="info-box">

                <strong>
                    Estado
                </strong>


                <?php
                echo htmlspecialchars(
                    textoEstadoFiltroReporteEmpleados(
                        $estado
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>

            </div>


            <!-- TOTAL -->

            <div class="info-box">

                <strong>
                    Total de empleados
                </strong>


                <?php
                echo (int)$totalEmpleados;
                ?>

            </div>


        </div>


    </div>


    <!-- =====================================
         RESULTADOS
    ====================================== -->

    <?php if (!empty($empleados)): ?>


        <div class="tabla-contenedor">


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
                                !empty(
                                    $fila['telefono']
                                )
                                    ?
                                    $fila['telefono']
                                    :
                                    '-',
                                ENT_QUOTES,
                                'UTF-8'
                            );

                            ?>

                        </td>


                        <!-- EMAIL -->

                        <td>

                            <?php

                            echo htmlspecialchars(
                                !empty(
                                    $fila['email']
                                )
                                    ?
                                    $fila['email']
                                    :
                                    '-',
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
                                        $fila[
                                            'fecha_alta'
                                        ]
                                    )
                                )
                                :
                                '-';

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
                                        $fila[
                                            'fecha_baja'
                                        ]
                                    )
                                )
                                :
                                '-';

                            ?>

                        </td>


                        <!-- CATEGORÍA -->

                        <td>

                            <?php

                            echo htmlspecialchars(
                                !empty(
                                    $fila['categoria']
                                )
                                    ?
                                    $fila['categoria']
                                    :
                                    '-',
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
                                    !empty(
                                        $fila['oficina']
                                    )
                                        ?
                                        $fila['oficina']
                                        :
                                        '-',
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
                                        formatearCuitUnidadOrganizacion(
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
                                !empty(
                                    $fila['situacion']
                                )
                                    ?
                                    $fila['situacion']
                                    :
                                    '-',
                                ENT_QUOTES,
                                'UTF-8'
                            );

                            ?>

                        </td>


                        <!-- ESTADO -->

                        <td>


                            <?php if (
                                (int)(
                                    $fila['activo']
                                    ?? 0
                                )
                                ===
                                1
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


                    </tr>


                <?php endforeach; ?>


                </tbody>


            </table>


        </div>


    <?php else: ?>


        <div class="sin-registros">

            No se encontraron empleados
            con los filtros seleccionados.

        </div>


    <?php endif; ?>


</div>


<script src="<?= htmlspecialchars(sigenmuniUrlAsset('js/reportes/reporte_empleados_pdf.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
</body>

</html>