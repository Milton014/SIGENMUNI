<?php

require_once __DIR__ . '/../../../core/Url.php';
require_once __DIR__ . '/../../../core/Csrf.php';


/*
|--------------------------------------------------------------------------
| GASTOS PROTOCOLARES - ROUTER
|--------------------------------------------------------------------------
*/

$liquidacionProtocolarId =
    (int)(
        $liquidacion['id']
        ?? 0
    );


$liquidacionProtocolarAccion =
    sigenmuniUrlRuta(
        'liquidacion/protocolar',
        [
            'id' =>
                $liquidacionProtocolarId
        ]
    );


$liquidacionProtocolarVolver =
    sigenmuniUrlRuta(
        'liquidacion'
    );


$liquidacionProtocolarCsrf =
    sigenmuniCsrfToken();

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Gastos Protocolares - SIGENMUNI</title>

<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/liquidacion/liquidacion_protocolar.css'), ENT_QUOTES, 'UTF-8') ?>">

</head>

<body>


<div class="header">

    <h1>
        SIGENMUNI
    </h1>

    <p>
        Carga de Gastos Protocolares
    </p>

</div>


<div class="contenedor">

    <div class="panel">


        <!-- =====================================
             ENCABEZADO
        ====================================== -->

        <div class="topbar">

            <div class="titulo">

                <h2>
                    Gastos Protocolares
                </h2>

                <p>
                    Seleccione los empleados, ingrese el importe
                    y defina su condición previsional.
                </p>

            </div>


            <div class="acciones-superiores">

                <a
                    href="<?php
                        echo htmlspecialchars(
                            $liquidacionProtocolarVolver,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                    class="btn btn-volver"
                >
                    Volver a Liquidaciones
                </a>

            </div>

        </div>


        <!-- =====================================
             MENSAJES
        ====================================== -->

        <?php if (!empty($mensaje)): ?>

            <div
                class="mensaje <?php
                    echo ($tipo_mensaje ?? '') === 'ok'
                        ? 'mensaje-ok'
                        : 'mensaje-error';
                ?>"
            >

                <?php
                echo htmlspecialchars(
                    $mensaje,
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>

            </div>

        <?php endif; ?>


        <?php if (!empty($liquidacion)): ?>


            <!-- =====================================
                 RESUMEN DE LA LIQUIDACIÓN
            ====================================== -->

            <div class="resumen">

                <div class="resumen-item">

                    <span class="etiqueta">
                        Tipo
                    </span>

                    <span class="valor">
                        Gastos Protocolares
                    </span>

                </div>


                <div class="resumen-item">

                    <span class="etiqueta">
                        Período
                    </span>

                    <span class="valor">
                        <?php
                        echo htmlspecialchars(
                            $liquidacion['periodo'] ?? '-',
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>
                    </span>

                </div>


                <div class="resumen-item">

                    <span class="etiqueta">
                        Fecha
                    </span>

                    <span class="valor">

                        <?php
                        echo !empty(
                            $liquidacion['fecha_liquidacion']
                        )
                            ? date(
                                'd/m/Y',
                                strtotime(
                                    $liquidacion[
                                        'fecha_liquidacion'
                                    ]
                                )
                            )
                            : '-';
                        ?>

                    </span>

                </div>


                <div class="resumen-item">

                    <span class="etiqueta">
                        Estado
                    </span>

                    <span class="valor">
                        <?php
                        echo htmlspecialchars(
                            $liquidacion['estado'] ?? '-',
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>
                    </span>

                </div>

            </div>


            <!-- =====================================
                 AVISO
            ====================================== -->

            <div class="aviso">

                <strong>
                    Regla previsional:
                </strong>

                <br>

                <strong>Con aportes</strong>:
                se aplicará automáticamente
                Caja de Previsión Social 11%
                y Aporte Patronal 16%.

                <br>

                <strong>Jubilado / Exento</strong>:
                no se aplicará ninguno de los dos conceptos.

            </div>


            <!-- =====================================
                 FORMULARIO
            ====================================== -->

            <form
                action="<?php
                    echo htmlspecialchars(
                        $liquidacionProtocolarAccion,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                method="POST"
                id="formProtocolar"
                novalidate
            >

                <input
                    type="hidden"
                    name="_csrf"
                    value="<?php
                        echo htmlspecialchars(
                            $liquidacionProtocolarCsrf,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                >


                <?php if (!empty($empleadosProtocolar)): ?>


                    <div class="tabla-contenedor">

                        <table>

                            <thead>

                                <tr>

                                    <th>
                                        Incluir
                                    </th>

                                    <th>
                                        Legajo
                                    </th>

                                    <th>
                                        Empleado
                                    </th>

                                    <th>
                                        Importe
                                    </th>

                                    <th>
                                        Condición Previsional
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                <?php foreach ($empleadosProtocolar as $empleado): ?>

                                    <?php

                                    $empleadoId =
                                        (int)(
                                            $empleado['empleado_id']
                                            ?? 0
                                        );

                                    $incluido =
                                        (int)(
                                            $empleado['incluido']
                                            ?? 0
                                        )
                                        ===
                                        1;

                                    $aplicaPrevision =
                                        (int)(
                                            $empleado['aplica_prevision']
                                            ?? 1
                                        )
                                        ===
                                        1;

                                    $importe =
                                        (float)(
                                            $empleado['importe']
                                            ?? 0
                                        );

                                    ?>


                                    <tr
                                        data-empleado-id="<?php
                                            echo $empleadoId;
                                        ?>"
                                    >


                                        <!-- INCLUIR -->

                                        <td>

                                            <input
                                                type="checkbox"
                                                class="chk-incluir"
                                                name="empleados[<?php
                                                    echo $empleadoId;
                                                ?>][incluir]"
                                                value="1"
                                                <?php
                                                echo $incluido
                                                    ? 'checked'
                                                    : '';
                                                ?>
                                            >

                                        </td>


                                        <!-- LEGAJO -->

                                        <td>

                                            <?php
                                            echo htmlspecialchars(
                                                $empleado['nro_legajo']
                                                ?? '-',
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                            ?>

                                        </td>


                                        <!-- EMPLEADO -->

                                        <td class="empleado">

                                            <strong>

                                                <?php
                                                echo htmlspecialchars(
                                                    trim(
                                                        (
                                                            $empleado['apellido']
                                                            ?? ''
                                                        )
                                                        . ', '
                                                        . (
                                                            $empleado['nombre']
                                                            ?? ''
                                                        )
                                                    ),
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                );
                                                ?>

                                            </strong>

                                        </td>


                                        <!-- IMPORTE -->

                                        <td>

                                            <input
                                                type="number"
                                                class="input-importe"
                                                name="empleados[<?php
                                                    echo $empleadoId;
                                                ?>][importe]"
                                                step="0.01"
                                                min="0"
                                                value="<?php
                                                    echo $incluido
                                                        ? htmlspecialchars(
                                                            number_format(
                                                                $importe,
                                                                2,
                                                                '.',
                                                                ''
                                                            ),
                                                            ENT_QUOTES,
                                                            'UTF-8'
                                                        )
                                                        : '';
                                                ?>"
                                                <?php
                                                echo !$incluido
                                                    ? 'disabled'
                                                    : '';
                                                ?>
                                            >

                                        </td>


                                        <!-- PREVISIÓN -->

                                        <td>

                                            <select
                                                class="select-prevision"
                                                name="empleados[<?php
                                                    echo $empleadoId;
                                                ?>][aplica_prevision]"
                                                <?php
                                                echo !$incluido
                                                    ? 'disabled'
                                                    : '';
                                                ?>
                                            >

                                                <option
                                                    value="1"
                                                    <?php
                                                    echo $aplicaPrevision
                                                        ? 'selected'
                                                        : '';
                                                    ?>
                                                >
                                                    Con aportes
                                                </option>

                                                <option
                                                    value="0"
                                                    <?php
                                                    echo !$aplicaPrevision
                                                        ? 'selected'
                                                        : '';
                                                    ?>
                                                >
                                                    Jubilado / Exento
                                                </option>

                                            </select>

                                        </td>


                                    </tr>

                                <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>


                    <!-- =====================================
                         PIE
                    ====================================== -->

                    <div class="pie">

                        <div class="contador">

                            Empleados cargados:

                            <strong id="contadorIncluidos">
                                <?php
                                echo (int)(
                                    $cantidadCargados
                                    ?? 0
                                );
                                ?>
                            </strong>

                        </div>


                        <div class="acciones-pie">

                            <a
                                href="<?php
                                    echo htmlspecialchars(
                                        $liquidacionProtocolarVolver,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                ?>"
                                class="btn btn-volver"
                            >
                                Cancelar
                            </a>


                            <button
                                type="submit"
                                class="btn btn-guardar"
                            >
                                Guardar Carga
                            </button>

                        </div>

                    </div>


                <?php else: ?>


                    <div class="sin-resultados">

                        No hay empleados con relación laboral en este período
                        disponibles para cargar Gastos Protocolares.

                    </div>


                <?php endif; ?>


            </form>


        <?php endif; ?>


    </div>

</div>


<script src="<?= htmlspecialchars(sigenmuniUrlAsset('js/liquidacion/liquidacion_protocolar.js'), ENT_QUOTES, 'UTF-8') ?>"></script>


</body>

</html>
