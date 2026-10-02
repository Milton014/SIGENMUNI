<?php

require_once __DIR__ . '/../../../core/Url.php';
require_once __DIR__ . '/../../../core/Csrf.php';


/*
|--------------------------------------------------------------------------
| VER LIQUIDACIÓN - ROUTER
|--------------------------------------------------------------------------
*/

$liquidacionVerId =
    (int)(
        $liquidacion['id']
        ?? $liquidacionId
        ?? 0
    );


$liquidacionVerVolver =
    sigenmuniUrlRuta(
        'liquidacion'
    );


$liquidacionVerCerrarDetalle =
    sigenmuniUrlRuta(
        'liquidacion/ver',
        [
            'id' =>
                $liquidacionVerId
        ]
    );


$liquidacionVerProcesar =
    sigenmuniUrlRuta(
        'liquidacion/procesar'
    );


$liquidacionVerRecibosTodos =
    sigenmuniUrlRuta(
        'liquidacion/recibos',
        [
            'liquidacion_id' =>
                $liquidacionVerId
        ]
    );


$liquidacionVerCsrf =
    sigenmuniCsrfToken();


$liquidacionVerUrlDetalle =
    function (
        $empleadoId
    ) use (
        $liquidacionVerId
    ) {

        $parametros = [
            'id' =>
                $liquidacionVerId,

            'empleado_id' =>
                (int)$empleadoId
        ];


        return
            sigenmuniUrlRuta(
                'liquidacion/ver',
                $parametros
            );
    };


$liquidacionVerUrlRecibo =
    function (
        $empleadoId
    ) use (
        $liquidacionVerId
    ) {

        return
            sigenmuniUrlRuta(
                'liquidacion/recibo',
                [
                    'liquidacion_id' =>
                        $liquidacionVerId,

                    'empleado_id' =>
                        (int)$empleadoId
                ]
            );
    };

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Ver Liquidación - SIGENMUNI</title>

<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/liquidacion/liquidacion_ver.css'), ENT_QUOTES, 'UTF-8') ?>">

</head>

<body>


<!-- =========================================
     HEADER
========================================= -->

<div class="header">

    <h1>
        SIGENMUNI
    </h1>

    <p>
        Detalle de Liquidación
    </p>

</div>


<div class="contenedor">

    <div class="panel">


        <!-- =====================================
             ACCIONES
        ====================================== -->

        <div class="acciones-superiores">


            <a
                href="<?php
                    echo htmlspecialchars(
                        $liquidacionVerVolver,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                class="btn btn-volver"
            >
                Volver al Listado
            </a>
            <?php if (
                $liquidacion['estado']
                ===
                'BORRADOR'
            ): ?>


                <form
                    action="<?php
                        echo htmlspecialchars(
                            $liquidacionVerProcesar,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                    method="POST"
                    class="form-accion-inline"
                    data-event-submit="liquidacion_ver-1"
                >

                    <input
                        type="hidden"
                        name="id"
                        value="<?php
                            echo (int)$liquidacionVerId;
                        ?>"
                    >

                    <input
                        type="hidden"
                        name="_csrf"
                        value="<?php
                            echo htmlspecialchars(
                                $liquidacionVerCsrf,
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                    >

                    <button
                        type="submit"
                        class="btn btn-procesar"
                    >
                        Procesar Liquidación
                    </button>

                </form>


            <?php endif; ?>


            <?php if (
                $liquidacion['estado'] === 'CERRADA'
                ||
                $liquidacion['estado'] === 'ANULADA'
            ): ?>


                <a
                    href="<?php
                        echo htmlspecialchars(
                            $liquidacionVerRecibosTodos,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                    class="btn btn-imprimir-todos"
                    target="_blank"
                    rel="noopener"
                >
                    Imprimir todos los recibos
                </a>


            <?php endif; ?>


        </div>


        <!-- =====================================
             TÍTULO
        ====================================== -->

        <h2>

            Liquidación #

            <?php
            echo (int)$liquidacion['id'];
            ?>

        </h2>


        <!-- =====================================
             CABECERA
        ====================================== -->

        <div class="cabecera">


            <!-- TIPO -->

            <div class="card">

                <strong>
                    Tipo
                </strong>

                <div class="valor">

                    <?php
                    echo htmlspecialchars(
                        $liquidacion['tipo_liquidacion'],
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>

                </div>

            </div>


            <!-- PERÍODO -->

            <div class="card">

                <strong>
                    Período
                </strong>

                <div class="valor">

                    <?php
                    echo htmlspecialchars(
                        $liquidacion['periodo'],
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>

                </div>

            </div>


            <!-- FECHA -->

            <div class="card">

                <strong>
                    Fecha de Liquidación
                </strong>

                <div class="valor">

                    <?php

                    echo !empty(
                        $liquidacion['fecha_liquidacion']
                    )
                        ?
                        date(
                            'd/m/Y',
                            strtotime(
                                $liquidacion['fecha_liquidacion']
                            )
                        )
                        :
                        '-';

                    ?>

                </div>

            </div>


            <!-- ESTADO -->

            <div class="card">

                <strong>
                    Estado
                </strong>


                <?php

                $estadoLiquidacion =
                    $liquidacion['estado'];


                $claseEstado =
                    'estado-borrador';


                if (
                    $estadoLiquidacion
                    ===
                    'CERRADA'
                ) {

                    $claseEstado =
                        'estado-cerrada';

                } elseif (
                    $estadoLiquidacion
                    ===
                    'ANULADA'
                ) {

                    $claseEstado =
                        'estado-anulada';
                }

                ?>


                <span
                    class="badge <?php
                        echo $claseEstado;
                    ?>"
                >

                    <?php
                    echo htmlspecialchars(
                        $estadoLiquidacion,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>

                </span>

            </div>


            <!-- DESCRIPCIÓN -->

            <div class="card">

                <strong>
                    Descripción
                </strong>

                <div class="valor">

                    <?php
                    echo htmlspecialchars(
                        !empty(
                            $liquidacion['descripcion']
                        )
                            ?
                            $liquidacion['descripcion']
                            :
                            '-',
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>

                </div>

            </div>


            <!-- CREADA -->

            <div class="card">

                <strong>
                    Creada
                </strong>

                <div class="valor">

                    <?php

                    echo !empty(
                        $liquidacion['created_at']
                    )
                        ?
                        date(
                            'd/m/Y H:i',
                            strtotime(
                                $liquidacion['created_at']
                            )
                        )
                        :
                        '-';

                    ?>

                </div>

            </div>


        </div>


        <!-- =====================================
             TOTALES GENERALES
        ====================================== -->

        <h3>
            Totales Generales
        </h3>


        <div class="totales-grid">


            <div class="total-box">

                <strong>
                    Empleados
                </strong>

                <div class="importe">

                    <?php
                    echo (int)(
                        $totales['cantidad_empleados']
                        ?? 0
                    );
                    ?>

                </div>

            </div>


            <div class="total-box">

                <strong>
                    Total Remunerativo
                </strong>

                <div class="importe">

                    $
                    <?php
                    echo number_format(
                        (float)(
                            $totales['total_remunerativo']
                            ?? 0
                        ),
                        2,
                        ',',
                        '.'
                    );
                    ?>

                </div>

            </div>


            <div class="total-box">

                <strong>
                    Total Descuentos
                </strong>

                <div class="importe">

                    $
                    <?php
                    echo number_format(
                        (float)(
                            $totales['total_descuentos']
                            ?? 0
                        ),
                        2,
                        ',',
                        '.'
                    );
                    ?>

                </div>

            </div>


            <div class="total-box">

                <strong>
                    Total No Remunerativo
                </strong>

                <div class="importe">

                    $
                    <?php
                    echo number_format(
                        (float)(
                            $totales['total_no_remunerativo']
                            ?? 0
                        ),
                        2,
                        ',',
                        '.'
                    );
                    ?>

                </div>

            </div>


            <div class="total-box">

                <strong>
                    Total Asignaciones
                </strong>

                <div class="importe">

                    $
                    <?php
                    echo number_format(
                        (float)(
                            $totales['total_asignaciones']
                            ?? 0
                        ),
                        2,
                        ',',
                        '.'
                    );
                    ?>

                </div>

            </div>


            <div class="total-box total-neto">

                <strong>
                    Total Neto
                </strong>

                <div class="importe">

                    $
                    <?php
                    echo number_format(
                        (float)(
                            $totales['total_neto']
                            ?? 0
                        ),
                        2,
                        ',',
                        '.'
                    );
                    ?>

                </div>

            </div>


        </div>


        <!-- =====================================
             RESUMEN POR EMPLEADO
        ====================================== -->

        <h3>
            Resumen por Empleado
        </h3>


        <?php if (
            !empty(
                $resumenEmpleados
            )
        ): ?>


            <div class="buscador-empleados" id="buscadorEmpleados" hidden>
                <label for="buscarEmpleado">Buscar por legajo, nombre o apellido</label>
                <div class="buscador-empleados-controles">
                    <input type="search" id="buscarEmpleado" placeholder="Ej.: 24 o Alexander Alarcón"
                        autocomplete="off" aria-controls="resumenEmpleados" aria-describedby="resultadoBusquedaEmpleados">
                    <button type="button" id="limpiarBusquedaEmpleado" class="btn btn-volver">Limpiar</button>
                </div>
                <p id="resultadoBusquedaEmpleados" role="status" aria-live="polite"></p>
                <p class="buscador-empleados-ayuda">La búsqueda filtra el listado. Los totales generales y la impresión de todos los recibos corresponden a la liquidación completa.</p>
            </div>
            <p id="sinCoincidenciasEmpleados" class="sin-datos" hidden>No se encontraron empleados con esa búsqueda.</p>

            <div class="tabla-contenedor">

                <table id="resumenEmpleados">

                    <thead>

                        <tr>

                            <th>Legajo</th>

                            <th>Empleado</th>

                            <th>
                                Remunerativo
                            </th>

                            <th>
                                Descuentos
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

                            <th>
                                Acciones
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php foreach (
                        $resumenEmpleados
                        as
                        $fila
                    ): ?>


                        <tr>


                            <!-- LEGAJO -->

                            <td data-label="Legajo">

                                <?php
                                echo htmlspecialchars(
                                    $fila['nro_legajo'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </td>


                            <!-- EMPLEADO -->

                            <td data-label="Empleado">

                                <?php
                                echo htmlspecialchars(
                                    $fila['apellido']
                                    . ', '
                                    . $fila['nombre'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </td>


                            <!-- REMUNERATIVO -->

                            <td
                                data-label="Remunerativo"
                                class="importe-positivo"
                            >

                                $
                                <?php
                                echo number_format(
                                    (float)$fila[
                                        'total_remunerativo'
                                    ],
                                    2,
                                    ',',
                                    '.'
                                );
                                ?>

                            </td>


                            <!-- DESCUENTOS -->

                            <td
                                data-label="Descuentos"
                                class="importe-descuento"
                            >

                                $
                                <?php
                                echo number_format(
                                    (float)$fila[
                                        'total_descuentos'
                                    ],
                                    2,
                                    ',',
                                    '.'
                                );
                                ?>

                            </td>


                            <!-- NO REMUNERATIVO -->

                            <td data-label="No Rem.">

                                $
                                <?php
                                echo number_format(
                                    (float)$fila[
                                        'total_no_remunerativo'
                                    ],
                                    2,
                                    ',',
                                    '.'
                                );
                                ?>

                            </td>


                            <!-- ASIGNACIONES -->

                            <td data-label="Asignaciones">

                                $
                                <?php
                                echo number_format(
                                    (float)$fila[
                                        'total_asignaciones'
                                    ],
                                    2,
                                    ',',
                                    '.'
                                );
                                ?>

                            </td>


                            <!-- NETO -->

                            <td
                                data-label="Neto"
                                class="importe-neto"
                            >

                                $
                                <?php
                                echo number_format(
                                    (float)$fila['neto'],
                                    2,
                                    ',',
                                    '.'
                                );
                                ?>

                            </td>


                            <!-- ACCIONES -->

                            <td data-label="Acciones">

                                <div class="acciones-botones">


                                    <a
                                        href="<?php
                                            echo htmlspecialchars(
                                                $liquidacionVerUrlDetalle(
                                                    (int)$fila[
                                                        'empleado_id'
                                                    ]
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                        ?>"
                                        class="btn btn-detalle"
                                    >
                                        Ver Detalle
                                    </a>


                                    <a
                                        href="<?php
                                            echo htmlspecialchars(
                                                $liquidacionVerUrlRecibo(
                                                    (int)$fila[
                                                        'empleado_id'
                                                    ]
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                        ?>"
                                        class="btn btn-recibo"
                                    >
                                        Ver Recibo
                                    </a>


                                </div>

                            </td>


                        </tr>


                    <?php endforeach; ?>


                    </tbody>

                </table>

            </div>


        <?php else: ?>


            <div class="sin-datos">

                Esta liquidación todavía no tiene empleados procesados.

            </div>


        <?php endif; ?>


        <!-- =====================================
             DETALLE DE CONCEPTOS
        ====================================== -->

        <?php if (
            $empleadoSeleccionado
            &&
            !empty(
                $detalleConceptos
            )
        ): ?>


            <div
                class="detalle-panel"
                id="detalleEmpleado"
            >


                <!-- CABECERA -->

                <div class="detalle-cabecera">


                    <div>

                        <h3>

                            Detalle de Conceptos

                        </h3>


                        <div class="empleado-info">

                            <?php

                            echo htmlspecialchars(
                                $empleadoSeleccionado['apellido']
                                . ', '
                                . $empleadoSeleccionado['nombre']
                                . ' - Legajo: '
                                . $empleadoSeleccionado['nro_legajo'],
                                ENT_QUOTES,
                                'UTF-8'
                            );

                            ?>

                        </div>

                    </div>


                    <a
                        href="<?php
                            echo htmlspecialchars(
                                $liquidacionVerCerrarDetalle,
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                        class="btn btn-cerrar-detalle"
                    >
                        Cerrar Detalle
                    </a>


                </div>


                <!-- TABLA -->

                <div class="tabla-contenedor">

                    <table>

                        <thead>

                            <tr>

                                <th>Código</th>

                                <th>Concepto</th>

                                <th>Categoría</th>

                                <th>Cantidad</th>

                                <th>% Aplicado</th>

                                <th>Monto</th>

                                <th>Origen</th>

                                <th>Observación</th>

                            </tr>

                        </thead>


                        <tbody>


                        <?php

                        /*
                        |--------------------------------------------------------------------------
                        | ORDEN VISUAL DE CONCEPTOS
                        |--------------------------------------------------------------------------
                        |
                        | 1. REMUNERATIVOS
                        | 2. NO REMUNERATIVOS
                        | 3. ASIGNACIONES / ASIGNACIONES FAMILIARES
                        | 4. DESCUENTOS
                        | 5. APORTES PATRONALES
                        |
                        | Dentro de cada grupo se ordena por código numérico.
                        |
                        | El código 112 se trata como NO REMUNERATIVO aunque exista
                        | algún dato histórico con una categoría distinta.
                        |
                        |--------------------------------------------------------------------------
                        */

                        $detalleConceptosOrdenados =
                            $detalleConceptos;


                        usort(
                            $detalleConceptosOrdenados,
                            function($a, $b)
                            {
                                $codigoA =
                                    (int)(
                                        $a['codigo']
                                        ?? 0
                                    );

                                $codigoB =
                                    (int)(
                                        $b['codigo']
                                        ?? 0
                                    );


                                $categoriaA =
                                    strtoupper(
                                        trim(
                                            (string)(
                                                $a['concepto_categoria']
                                                ?? ''
                                            )
                                        )
                                    );

                                $categoriaB =
                                    strtoupper(
                                        trim(
                                            (string)(
                                                $b['concepto_categoria']
                                                ?? ''
                                            )
                                        )
                                    );


                                $prioridad =
                                    function($categoria, $codigo)
                                    {
                                        /*
                                        | Código 112:
                                        | siempre se muestra como bloque
                                        | NO REMUNERATIVO.
                                        */

                                        if ($codigo === 112) {

                                            return 20;
                                        }


                                        switch ($categoria) {

                                            case 'REMUNERATIVO':
                                                return 10;

                                            case 'NO_REMUNERATIVO':
                                                return 20;

                                            case 'ASIGNACION':
                                            case 'ASIGNACION_FAMILIAR':
                                                return 30;

                                            case 'DESCUENTO':
                                                return 40;

                                            case 'APORTE_PATRONAL':
                                                return 50;

                                            default:
                                                return 90;
                                        }
                                    };


                                $prioridadA =
                                    $prioridad(
                                        $categoriaA,
                                        $codigoA
                                    );

                                $prioridadB =
                                    $prioridad(
                                        $categoriaB,
                                        $codigoB
                                    );


                                if ($prioridadA !== $prioridadB) {

                                    return
                                        $prioridadA
                                        <=>
                                        $prioridadB;
                                }


                                return
                                    $codigoA
                                    <=>
                                    $codigoB;
                            }
                        );

                        ?>


                        <?php foreach (
                            $detalleConceptosOrdenados
                            as
                            $detalle
                        ): ?>


                            <?php

                            /*
                            |--------------------------------------------------------------------------
                            | CATEGORÍA
                            |--------------------------------------------------------------------------
                            */

                            $categoriaConcepto =
                                strtoupper(
                                    trim(
                                        (string)(
                                            $detalle[
                                                'concepto_categoria'
                                            ]
                                            ?? ''
                                        )
                                    )
                                );


                            $claseCategoria =
                                'cat-otro';


                            if (
                                $categoriaConcepto
                                ===
                                'REMUNERATIVO'
                            ) {

                                $claseCategoria =
                                    'cat-remunerativo';

                            } elseif (
                                $categoriaConcepto
                                ===
                                'NO_REMUNERATIVO'
                            ) {

                                $claseCategoria =
                                    'cat-no-remunerativo';

                            } elseif (
                                $categoriaConcepto
                                ===
                                'ASIGNACION'
                            ) {

                                $claseCategoria =
                                    'cat-asignacion';

                            } elseif (
                                $categoriaConcepto
                                ===
                                'DESCUENTO'
                            ) {

                                $claseCategoria =
                                    'cat-descuento';

                            } elseif (
                                $categoriaConcepto
                                ===
                                'APORTE_PATRONAL'
                            ) {

                                $claseCategoria =
                                    'cat-patronal';
                            }

                            ?>


                            <tr
                                class="<?php
                                    echo (
                                        (int)$detalle[
                                            'es_manual'
                                        ]
                                        === 1
                                    )
                                        ?
                                        'fila-manual'
                                        :
                                        '';
                                ?>"
                            >


                                <!-- CÓDIGO -->

                                <td data-label="Código">

                                    <?php
                                    echo htmlspecialchars(
                                        $detalle['codigo'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>

                                </td>


                                <!-- CONCEPTO -->

                                <td data-label="Concepto">

                                    <?php
                                    echo htmlspecialchars(
                                        $detalle[
                                            'concepto_nombre'
                                        ],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>

                                </td>


                                <!-- CATEGORÍA -->

                                <td data-label="Categoría">

                                    <span
                                        class="categoria <?php
                                            echo $claseCategoria;
                                        ?>"
                                    >

                                        <?php
                                        echo htmlspecialchars(
                                            $categoriaConcepto
                                                !== ''
                                                ?
                                                $categoriaConcepto
                                                :
                                                '-',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        );
                                        ?>

                                    </span>

                                </td>


                                <!-- CANTIDAD -->

                                <td data-label="Cantidad">

                                    <?php
                                    echo number_format(
                                        (float)$detalle[
                                            'cantidad'
                                        ],
                                        2,
                                        ',',
                                        '.'
                                    );
                                    ?>

                                </td>


                                <!-- PORCENTAJE -->

                                <td data-label="% Aplicado">

                                    <?php
                                    echo number_format(
                                        (float)$detalle[
                                            'porcentaje_aplicado'
                                        ],
                                        2,
                                        ',',
                                        '.'
                                    );
                                    ?>%

                                </td>


                                <!-- MONTO -->

                                <td data-label="Monto">

                                    $
                                    <?php
                                    echo number_format(
                                        (float)$detalle[
                                            'monto'
                                        ],
                                        2,
                                        ',',
                                        '.'
                                    );
                                    ?>

                                </td>


                                <!-- ORIGEN -->

                                <td data-label="Origen">


                                    <?php if (
                                        (int)$detalle[
                                            'es_manual'
                                        ]
                                        === 1
                                    ): ?>


                                        <span
                                            class="etiqueta etiqueta-manual"
                                        >
                                            MANUAL
                                        </span>


                                    <?php else: ?>


                                        <span
                                            class="etiqueta etiqueta-auto"
                                        >
                                            AUTOMÁTICO
                                        </span>


                                    <?php endif; ?>


                                </td>


                                <!-- OBSERVACIÓN -->

                                <td data-label="Observación">

                                    <?php
                                    echo htmlspecialchars(
                                        !empty(
                                            $detalle[
                                                'observacion'
                                            ]
                                        )
                                            ?
                                            $detalle[
                                                'observacion'
                                            ]
                                            :
                                            '-',
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


            </div>


            <!-- =================================
                 BAJAR AUTOMÁTICAMENTE AL DETALLE
            ================================== -->



        <?php elseif (
            $empleadoSeleccionado
            &&
            empty(
                $detalleConceptos
            )
        ): ?>


            <div class="detalle-panel">

                <h3>
                    Detalle de Conceptos
                </h3>


                <div class="sin-datos">

                    No hay conceptos cargados para el empleado seleccionado.

                </div>


                <div
                    class="acciones-botones"
                    data-estilo="liquidacion_ver-1"
                >

                    <a
                        href="<?php
                            echo htmlspecialchars(
                                $liquidacionVerCerrarDetalle,
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                        class="btn btn-cerrar-detalle"
                    >
                        Cerrar Detalle
                    </a>

                </div>

            </div>


        <?php endif; ?>


    </div>

</div>


<script src="<?= htmlspecialchars(sigenmuniUrlAsset('js/liquidacion/liquidacion_ver.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
</body>

</html>
