<?php

require_once __DIR__ . '/../../../core/Url.php';


if (!function_exists('estadoClaseHistorialPdf')) {

    function estadoClaseHistorialPdf($estado)
    {
        $estado =
            strtoupper(
                trim(
                    (string)$estado
                )
            );


        if ($estado === 'CERRADA') {
            return 'estado-cerrada';
        }


        if ($estado === 'ANULADA') {
            return 'estado-anulada';
        }


        return 'estado-borrador';
    }
}


if (!function_exists('formatearCuitUnidadOrganizacionHistorialPdf')) {

    function formatearCuitUnidadOrganizacionHistorialPdf($cuit)
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


$historialPdfUrlVolver =
    sigenmuniUrlRuta(
        'reportes/historial-empleado',
        [
            'empleado_id' =>
                (int)$empleadoId
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
    Historial por Empleado - PDF
</title>


<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/reportes/reporte_historial_empleado_pdf.css'), ENT_QUOTES, 'UTF-8') ?>">

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
                    $historialPdfUrlVolver,
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
            data-event-click="reporte_historial_empleado_pdf-1"
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
            Historial por Empleado
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


        <div class="datos-empleado">


            <div class="info-box">

                <strong>
                    Empleado
                </strong>

                <?php
                echo htmlspecialchars(
                    (
                        $empleado['apellido']
                        ?? ''
                    )
                    .
                    ', '
                    .
                    (
                        $empleado['nombre']
                        ?? ''
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>

            </div>


            <div class="info-box">

                <strong>
                    Legajo
                </strong>

                <?php
                echo htmlspecialchars(
                    $empleado['nro_legajo']
                    ?? '',
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>

            </div>


            <div class="info-box">

                <strong>
                    DNI
                </strong>

                <?php
                echo htmlspecialchars(
                    $empleado['dni']
                    ?? '',
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>

            </div>


            <div class="info-box">

                <strong>
                    CUIL
                </strong>

                <?php
                echo htmlspecialchars(
                    $empleado['cuil']
                    ?? '',
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>

            </div>


            <div class="info-box">

                <strong>
                    Categoría
                </strong>

                <?php
                echo htmlspecialchars(
                    $empleado['categoria']
                    ?? '-',
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>

            </div>


            <div class="info-box">

                <strong>
                    Unidad de Organización
                </strong>

                <?php
                echo htmlspecialchars(
                    $empleado['oficina']
                    ?? '-',
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>


                <?php if (!empty($empleado['oficina_cuit'])): ?>

                    <div class="cuit-unidad">

                        CUIT:
                        <?php
                        echo htmlspecialchars(
                            formatearCuitUnidadOrganizacionHistorialPdf(
                                $empleado['oficina_cuit']
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>

                    </div>

                <?php endif; ?>

            </div>


            <div class="info-box">

                <strong>
                    Situación
                </strong>

                <?php
                echo htmlspecialchars(
                    $empleado['situacion']
                    ?? '-',
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>

            </div>


            <div class="info-box">

                <strong>
                    Fecha Alta
                </strong>

                <?php

                echo !empty(
                    $empleado['fecha_alta']
                )
                    ?
                    date(
                        "d/m/Y",
                        strtotime(
                            $empleado['fecha_alta']
                        )
                    )
                    :
                    '-';

                ?>

            </div>


            <div class="info-box">

                <strong>
                    Fecha Baja
                </strong>

                <?php

                echo !empty(
                    $empleado['fecha_baja']
                )
                    ?
                    date(
                        "d/m/Y",
                        strtotime(
                            $empleado['fecha_baja']
                        )
                    )
                    :
                    '-';

                ?>

            </div>


            <div class="info-box">

                <strong>
                    Estado del Empleado
                </strong>

                <?php

                echo (
                    (int)(
                        $empleado['activo']
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

            </div>


            <div class="info-box">

                <strong>
                    Total Liquidaciones
                </strong>

                <?php
                echo (int)$totalLiquidaciones;
                ?>

            </div>


        </div>


    </div>


    <!-- =====================================
         HISTORIAL
    ====================================== -->

    <?php if (!empty($historial)): ?>


        <div class="tabla-contenedor">


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
                            Estado
                        </th>

                        <th>
                            Descripción
                        </th>

                        <th>
                            Total Remunerativo
                        </th>

                        <th>
                            Total Descuentos
                        </th>

                        <th>
                            No Remunerativo
                        </th>

                        <th>
                            Asignaciones
                        </th>

                        <th>
                            Neto
                        </th>

                    </tr>


                </thead>


                <tbody>


                <?php foreach ($historial as $fila): ?>


                    <tr>


                        <td>

                            <?php
                            echo (int)(
                                $fila['liquidacion_id']
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
                                        $fila[
                                            'fecha_liquidacion'
                                        ]
                                    )
                                )
                                :
                                '-';

                            ?>

                        </td>


                        <td>

                            <span
                                class="estado-badge <?php
                                    echo estadoClaseHistorialPdf(
                                        $fila['estado']
                                        ?? ''
                                    );
                                ?>"
                            >

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

                            </span>

                        </td>


                        <td>

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


                        <td>

                            $
                            <?php
                            echo number_format(
                                (float)(
                                    $fila[
                                        'total_remunerativo'
                                    ]
                                    ?? 0
                                ),
                                2,
                                ',',
                                '.'
                            );
                            ?>

                        </td>


                        <td>

                            $
                            <?php
                            echo number_format(
                                (float)(
                                    $fila[
                                        'total_descuentos'
                                    ]
                                    ?? 0
                                ),
                                2,
                                ',',
                                '.'
                            );
                            ?>

                        </td>


                        <td>

                            $
                            <?php
                            echo number_format(
                                (float)(
                                    $fila[
                                        'total_no_remunerativo'
                                    ]
                                    ?? 0
                                ),
                                2,
                                ',',
                                '.'
                            );
                            ?>

                        </td>


                        <td>

                            $
                            <?php
                            echo number_format(
                                (float)(
                                    $fila[
                                        'total_asignaciones'
                                    ]
                                    ?? 0
                                ),
                                2,
                                ',',
                                '.'
                            );
                            ?>

                        </td>


                        <td>

                            <strong>

                                $
                                <?php
                                echo number_format(
                                    (float)(
                                        $fila['neto']
                                        ?? 0
                                    ),
                                    2,
                                    ',',
                                    '.'
                                );
                                ?>

                            </strong>

                        </td>


                    </tr>


                <?php endforeach; ?>


                </tbody>


            </table>


        </div>


    <?php else: ?>


        <div class="sin-registros">

            Este empleado todavía no tiene
            liquidaciones registradas.

        </div>


    <?php endif; ?>


</div>


<script src="<?= htmlspecialchars(sigenmuniUrlAsset('js/reportes/reporte_historial_empleado_pdf.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
</body>

</html>