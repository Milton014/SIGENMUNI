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

<style>

*{
    box-sizing:border-box;
}

body{
    margin:0;
    font-family:Arial,Helvetica,sans-serif;
    background:#f4f7fb;
    color:#1f2937;
}

.contenedor{
    width:95%;
    max-width:1450px;
    margin:20px auto;
    background:#fff;
    padding:20px;
    border-radius:12px;
    box-shadow:0 4px 14px rgba(0,0,0,.08);
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
    padding:10px 14px;
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
    border:2px solid #d1d5db;
    border-radius:10px;
    padding:18px;
    margin-bottom:18px;
}

.encabezado h1{
    margin:0 0 6px 0;
    font-size:26px;
    color:#16a34a;
}

.encabezado p{
    margin:4px 0;
    font-size:14px;
}

.resumen{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
    gap:10px;
    margin-top:15px;
}

.info-box{
    border:1px solid #e5e7eb;
    background:#f9fafb;
    border-radius:8px;
    padding:10px 12px;
}

.info-box strong{
    display:block;
    margin-bottom:5px;
    font-size:13px;
    color:#374151;
}


/* =========================================
   TABLA
========================================= */

.tabla-contenedor{
    width:100%;
    overflow:visible;
}

table{
    width:100%;
    min-width:0;
    table-layout:fixed;
    border-collapse:collapse;
}

th,
td{
    border:1px solid #d1d5db;
    padding:6px 5px;
    font-size:10px;
    text-align:left;
    vertical-align:middle;
    overflow-wrap:anywhere;
}

th{
    background:#16a34a;
    color:white;
    white-space:normal;
    line-height:1.15;
}


/* =========================================
   ANCHOS
========================================= */

.col-id{
    width:4%;
}

.col-tipo{
    width:8%;
}

.col-periodo{
    width:7%;
}

.col-fecha{
    width:8%;
}

.col-descripcion{
    width:12%;
}

.col-estado{
    width:8%;
    text-align:center;
}

.col-empleados{
    width:6%;
    text-align:center;
}

.col-rem{
    width:9%;
}

.col-desc{
    width:9%;
}

.col-no-rem{
    width:9%;
}

.col-asig{
    width:8%;
}

.col-neto{
    width:9%;
}

.col-creada{
    width:10%;
}


/* =========================================
   ESTADOS
========================================= */

.estado-badge{
    display:inline-block;
    padding:4px 7px;
    border-radius:999px;
    font-size:9px;
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

.neto{
    font-weight:bold;
}

.sin-registros{
    text-align:center;
    padding:20px;
    color:#6b7280;
    border:1px solid #d1d5db;
    border-radius:8px;
    background:#fafafa;
}


/* =========================================
   IMPRESIÓN A4 HORIZONTAL
========================================= */

@media print{

    @page{
        size:A4 landscape;
        margin:7mm;
    }

    body{
        background:#fff;
    }

    .acciones{
        display:none;
    }

    .contenedor{
        width:100%;
        max-width:100%;
        margin:0;
        padding:0;
        border-radius:0;
        box-shadow:none;
    }

    .encabezado{
        padding:9px;
        margin-bottom:8px;
        page-break-inside:avoid;
    }

    .encabezado h1{
        font-size:18px;
    }

    .encabezado p{
        font-size:9px;
    }

    .resumen{
        gap:4px;
        margin-top:7px;
    }

    .info-box{
        padding:5px;
        font-size:8px;
    }

    .info-box strong{
        font-size:8px;
    }

    table{
        width:100%;
        min-width:0;
        table-layout:fixed;
    }

    th,
    td{
        padding:3px 2px;
        font-size:7px;
    }

    .estado-badge{
        font-size:6px;
        padding:2px 4px;
    }

    tr{
        page-break-inside:avoid;
    }
}

@media(max-width:768px){

    .contenedor{
        width:98%;
        margin:10px auto;
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

</body>

</html>