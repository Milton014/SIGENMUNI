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


<style>

*{
    box-sizing:border-box;
}


body{
    font-family:
        Arial,
        Helvetica,
        sans-serif;

    margin:0;

    background:#f4f7fb;

    color:#1f2937;
}


/* =========================================
   CONTENEDOR
========================================= */

.contenedor{
    width:95%;

    max-width:1400px;

    margin:20px auto;

    background:#fff;

    padding:20px;

    border-radius:12px;

    box-shadow:
        0 4px 14px
        rgba(0,0,0,.08);
}


/* =========================================
   ACCIONES
========================================= */

.acciones{
    display:flex;

    gap:10px;

    flex-wrap:wrap;

    margin-bottom:18px;
}


.btn{
    display:inline-block;

    text-decoration:none;

    border:none;

    border-radius:8px;

    padding:
        10px 14px;

    font-size:14px;

    font-weight:bold;

    cursor:pointer;

    color:white;
}


.btn-volver{
    background:#6b7280;
}


.btn-imprimir{
    background:#dc2626;
}


/* =========================================
   ENCABEZADO
========================================= */

.encabezado{
    border:
        2px solid #d1d5db;

    border-radius:10px;

    padding:18px;

    margin-bottom:18px;
}


.encabezado h1{
    margin:
        0 0 6px 0;

    font-size:26px;

    color:#0f766e;
}


.encabezado p{
    margin:
        4px 0;

    font-size:14px;
}


/* =========================================
   DATOS DEL EMPLEADO
========================================= */

.datos-empleado{
    display:grid;

    grid-template-columns:
        repeat(
            auto-fit,
            minmax(200px,1fr)
        );

    gap:10px;

    margin-top:15px;
}


.info-box{
    border:
        1px solid #e5e7eb;

    background:#f9fafb;

    border-radius:8px;

    padding:
        10px 12px;
}


.info-box strong{
    display:block;

    margin-bottom:5px;

    font-size:13px;

    color:#374151;
}


.cuit-unidad{
    margin-top:4px;
    color:#64748b;
    font-size:11px;
    line-height:1.35;
}


/* =========================================
   TABLA
========================================= */

.tabla-contenedor{
    overflow-x:auto;
}


table{
    width:100%;

    min-width:1200px;

    border-collapse:collapse;
}


th,
td{
    border:
        1px solid #d1d5db;

    padding:8px;

    font-size:12px;

    text-align:left;

    vertical-align:middle;
}


th{
    background:#0f766e;

    color:white;

    white-space:nowrap;
}


/* =========================================
   ESTADOS
========================================= */

.estado-badge{
    display:inline-block;

    padding:
        4px 8px;

    border-radius:999px;

    font-size:11px;

    font-weight:bold;
}


.estado-borrador{
    background:#fef3c7;

    color:#92400e;
}


.estado-cerrada{
    background:#dcfce7;

    color:#166534;
}


.estado-anulada{
    background:#fee2e2;

    color:#991b1b;
}


/* =========================================
   SIN REGISTROS
========================================= */

.sin-registros{
    text-align:center;

    padding:20px;

    color:#6b7280;

    border:
        1px solid #d1d5db;

    border-radius:8px;

    background:#fafafa;
}


/* =========================================
   IMPRESIÓN
========================================= */

@media print{

    body{
        background:#fff;
    }


    .acciones{
        display:none;
    }


    .contenedor{
        box-shadow:none;

        margin:0;

        max-width:100%;

        width:100%;

        border-radius:0;

        padding:0;
    }


    table{
        min-width:0;

        width:100%;
    }


    th,
    td{
        font-size:9px;

        padding:4px;
    }


    .cuit-unidad{
        font-size:8px;
    }


    tr{
        page-break-inside:avoid;
    }


    @page{
        size:A4 landscape;

        margin:8mm;
    }
}


/* =========================================
   CELULAR
========================================= */

@media(max-width:768px){

    .contenedor{
        width:98%;

        margin:
            10px auto;

        padding:14px;
    }


    .acciones{
        flex-direction:column;
    }


    .btn{
        width:100%;

        text-align:center;
    }


    .encabezado h1{
        font-size:22px;
    }

}

</style>

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
            onclick="window.print()"
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


</body>

</html>