<?php

require_once __DIR__ . '/../../../core/Url.php';
require_once __DIR__ . '/../../../core/Csrf.php';


/*
|--------------------------------------------------------------------------
| NOVEDADES SAC - ROUTER
|--------------------------------------------------------------------------
*/

$liquidacionNovedadesSacId =
    (int)(
        $liquidacion['id']
        ?? 0
    );


$liquidacionNovedadesSacAccion =
    sigenmuniUrlRuta(
        'liquidacion/novedades-sac',
        [
            'id' =>
                $liquidacionNovedadesSacId
        ]
    );


$liquidacionNovedadesSacVolver =
    sigenmuniUrlRuta(
        'liquidacion'
    );


$liquidacionNovedadesSacCsrf =
    sigenmuniCsrfToken();

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Novedades SAC - SIGENMUNI</title>

<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/liquidacion/liquidacion_novedades_sac.css'), ENT_QUOTES, 'UTF-8') ?>">

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
        Novedades de Sueldo Anual Complementario
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
                    Novedades de SAC
                </h2>

                <p>
                    Controle los días SAC devengados, ya pagados y pendientes de cada empleado.
                </p>

            </div>


            <div class="acciones-superiores">

                <a
                    href="<?php
                        echo htmlspecialchars(
                            $liquidacionNovedadesSacVolver,
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
             MENSAJE
        ====================================== -->

        <?php if (!empty($mensaje)): ?>

            <div
                class="mensaje <?php
                    echo $tipo_mensaje === 'ok'
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


        <?php

        $periodoLiquidacion =
            (string)(
                $liquidacion['periodo']
                ?? ''
            );

        $anio =
            0;

        $mes =
            0;

        if (
            preg_match(
                '/^(\d{4})-(\d{2})$/',
                $periodoLiquidacion,
                $coincidencias
            )
        ) {

            $anio =
                (int)$coincidencias[1];

            $mes =
                (int)$coincidencias[2];
        }


        if (
            $anio > 0
            &&
            $mes >= 1
            &&
            $mes <= 6
        ) {

            $semestreTexto =
                '1.er semestre';

            $semestreDesde =
                '01/01/' . $anio;

            $semestreHasta =
                '30/06/' . $anio;

        } elseif (
            $anio > 0
            &&
            $mes >= 7
            &&
            $mes <= 12
        ) {

            $semestreTexto =
                '2.º semestre';

            $semestreDesde =
                '01/07/' . $anio;

            $semestreHasta =
                '31/12/' . $anio;

        } else {

            $semestreTexto =
                '-';

            $semestreDesde =
                '-';

            $semestreHasta =
                '-';
        }


        $fechaVisual =
            !empty(
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


        <!-- =====================================
             RESUMEN
        ====================================== -->

        <?php if (!empty($liquidacion)): ?>

            <div class="resumen-liquidacion">

                <div class="dato">

                    <strong>Liquidación</strong>

                    <span>
                        #<?php echo (int)$liquidacion['id']; ?>
                    </span>

                </div>


                <div class="dato">

                    <strong>Período</strong>

                    <span>
                        <?php
                        echo htmlspecialchars(
                            $periodoLiquidacion,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>
                    </span>

                </div>


                <div class="dato">

                    <strong>Semestre</strong>

                    <span>
                        <?php
                        echo htmlspecialchars(
                            $semestreTexto,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>
                    </span>

                </div>


                <div class="dato">

                    <strong>Rango</strong>

                    <span>
                        <?php
                        echo htmlspecialchars(
                            $semestreDesde
                            . ' al '
                            . $semestreHasta,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>
                    </span>

                </div>


                <div class="dato">

                    <strong>Fecha de liquidación</strong>

                    <span>
                        <?php
                        echo htmlspecialchars(
                            $fechaVisual,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>
                    </span>

                </div>

            </div>

        <?php endif; ?>


        <!-- =====================================
             AVISO
        ====================================== -->

        <div class="aviso">

            <strong>
                Regla de cálculo del SAC
            </strong>

            El sistema calcula automáticamente los días SAC <b>devengados</b>,
            descuenta los días <b>ya pagados</b> en liquidaciones de SAC cerradas
            del mismo semestre y propone como máximo los días <b>pendientes</b>.

            <div class="formula">
                Días pendientes = Días devengados - Días ya pagados
                <br>
                SAC = Base Remunerativa × 50% × (Días a liquidar / 180)
            </div>

        </div>


        <?php if (!empty($empleadosSac)): ?>


            <!-- =====================================
                 HERRAMIENTAS
            ====================================== -->

            <div class="herramientas">

                <div class="busqueda">

                    <input
                        type="text"
                        id="buscarEmpleado"
                        placeholder="Buscar por apellido, nombre, legajo o DNI..."
                    >

                </div>


                <div class="acciones-rapidas">

                    <button
                        type="button"
                        class="btn btn-180"
                        data-event-click="liquidacion_novedades_sac-1"
                    >
                        Todos 180 días
                    </button>

                    <button
                        type="button"
                        class="btn btn-0"
                        data-event-click="liquidacion_novedades_sac-2"
                    >
                        Todos 0 días
                    </button>

                </div>

            </div>


            <div class="resumen-sac">

                <span>
                    Empleados disponibles:
                    <strong>
                        <?php echo count($empleadosSac); ?>
                    </strong>
                </span>

                <span>
                    Con saldo pendiente:
                    <strong>
                        <?php echo (int)($cantidadSacPendiente ?? 0); ?>
                    </strong>
                </span>

                <span>
                    SAC ya liquidado:
                    <strong>
                        <?php echo (int)($cantidadSacYaLiquidado ?? 0); ?>
                    </strong>
                </span>

                <span>
                    SAC proporcional / sin SAC:
                    <strong id="contadorProporcionales">
                        <?php echo (int)($cantidadSacProporcional ?? 0); ?>
                    </strong>
                </span>

            </div>


            <!-- =====================================
                 FORMULARIO
            ====================================== -->

            <form
                method="POST"
                action="<?php
                    echo htmlspecialchars(
                        $liquidacionNovedadesSacAccion,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                id="formSac"
            >

                <input
                    type="hidden"
                    name="_csrf"
                    value="<?php
                        echo htmlspecialchars(
                            $liquidacionNovedadesSacCsrf,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                >

                <div class="tabla-contenedor">

                    <table>

                        <thead>

                            <tr>
                                <th>Legajo</th>
                                <th>Empleado</th>
                                <th>DNI</th>
                                <th>Alta</th>
                                <th>Fecha Inactivo</th>
                                <th>Situación</th>
                                <th>Devengados</th>
                                <th>Ya pagados</th>
                                <th>Pendientes</th>
                                <th>Días a liquidar</th>
                                <th>Estado SAC</th>
                            </tr>

                        </thead>


                        <tbody id="cuerpoEmpleados">

                        <?php foreach ($empleadosSac as $empleado): ?>

                            <?php

                            $empleadoId =
                                (int)(
                                    $empleado['id']
                                    ?? $empleado['empleado_id']
                                    ?? 0
                                );

                            $diasDevengados =
                                (int)(
                                    $empleado['dias_sac_devengados']
                                    ?? 0
                                );

                            $diasPagados =
                                (int)(
                                    $empleado['dias_sac_pagados']
                                    ?? 0
                                );

                            $diasPendientes =
                                (int)(
                                    $empleado['dias_sac_pendientes']
                                    ?? 0
                                );

                            $diasSac =
                                (int)(
                                    $empleado['dias_sac']
                                    ?? $diasPendientes
                                );

                            if ($diasSac < 0) {
                                $diasSac = 0;
                            }

                            if ($diasSac > $diasPendientes) {
                                $diasSac = $diasPendientes;
                            }

                            $estadoSac =
                                strtoupper(
                                    trim(
                                        (string)(
                                            $empleado['estado_sac']
                                            ?? 'PENDIENTE'
                                        )
                                    )
                                );

                            $estaActivo =
                                (int)(
                                    $empleado['activo']
                                    ?? 0
                                ) === 1;

                            $fechaAltaVisual =
                                !empty(
                                    $empleado['fecha_alta']
                                )
                                &&
                                $empleado['fecha_alta'] !== '0000-00-00'
                                    ?
                                    date(
                                        'd/m/Y',
                                        strtotime(
                                            $empleado['fecha_alta']
                                        )
                                    )
                                    :
                                    '-';

                            $fechaInactivoVisual =
                                !empty(
                                    $empleado['fecha_inactivo']
                                )
                                &&
                                $empleado['fecha_inactivo'] !== '0000-00-00'
                                    ?
                                    date(
                                        'd/m/Y',
                                        strtotime(
                                            $empleado['fecha_inactivo']
                                        )
                                    )
                                    :
                                    '-';

                            $textoBusqueda =
                                strtolower(
                                    trim(
                                        (string)(
                                            $empleado['nro_legajo']
                                            ?? ''
                                        )
                                        . ' '
                                        . (string)(
                                            $empleado['apellido']
                                            ?? ''
                                        )
                                        . ' '
                                        . (string)(
                                            $empleado['nombre']
                                            ?? ''
                                        )
                                        . ' '
                                        . (string)(
                                            $empleado['dni']
                                            ?? ''
                                        )
                                    )
                                );


                            if ($estadoSac === 'YA_LIQUIDADO') {

                                $claseFila =
                                    'fila-no-liquida';

                                $claseBadgeSac =
                                    'badge-liquidado';

                                $textoBadgeSac =
                                    'Ya liquidado';

                            } elseif ($estadoSac === 'PARCIAL') {

                                $claseFila =
                                    'fila-proporcional';

                                $claseBadgeSac =
                                    'badge-parcial';

                                $textoBadgeSac =
                                    'Parcial';

                            } elseif ($diasPendientes <= 0) {

                                $claseFila =
                                    'fila-no-liquida';

                                $claseBadgeSac =
                                    'badge-liquidado';

                                $textoBadgeSac =
                                    'Ya liquidado';

                            } elseif ($diasSac === 180) {

                                $claseFila =
                                    '';

                                $claseBadgeSac =
                                    'badge-completo';

                                $textoBadgeSac =
                                    'SAC completo';

                            } else {

                                $claseFila =
                                    'fila-proporcional';

                                $claseBadgeSac =
                                    'badge-pendiente';

                                $textoBadgeSac =
                                    'Pendiente';
                            }

                            ?>

                            <tr
                                class="<?php echo $claseFila; ?>"
                                data-busqueda="<?php
                                    echo htmlspecialchars(
                                        $textoBusqueda,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                ?>"
                                data-pendientes="<?php echo $diasPendientes; ?>"
                            >

                                <!-- LEGAJO -->

                                <td data-label="Legajo">

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

                                <td
                                    class="col-empleado"
                                    data-label="Empleado"
                                >

                                    <strong>
                                        <?php
                                        echo htmlspecialchars(
                                            trim(
                                                (string)(
                                                    $empleado['apellido']
                                                    ?? ''
                                                )
                                                . ', '
                                                . (string)(
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


                                <!-- DNI -->

                                <td data-label="DNI">

                                    <?php
                                    echo htmlspecialchars(
                                        $empleado['dni']
                                        ?? '-',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>

                                </td>


                                <!-- ALTA -->

                                <td
                                    class="col-fecha"
                                    data-label="Alta"
                                >

                                    <?php
                                    echo htmlspecialchars(
                                        $fechaAltaVisual,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>

                                </td>


                                <!-- FECHA INACTIVO -->

                                <td
                                    class="col-fecha"
                                    data-label="Fecha Inactivo"
                                >

                                    <?php
                                    echo htmlspecialchars(
                                        $fechaInactivoVisual,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>

                                </td>


                                <!-- SITUACIÓN -->

                                <td data-label="Situación">

                                    <span
                                        class="badge <?php
                                            echo $estaActivo
                                                ? 'badge-activo'
                                                : 'badge-inactivo';
                                        ?>"
                                    >
                                        <?php
                                            echo $estaActivo
                                                ? 'Activo'
                                                : 'Inactivo';
                                        ?>
                                    </span>

                                </td>


                                <!-- DEVENGADOS -->

                                <td data-label="Devengados">
                                    <strong><?php echo $diasDevengados; ?></strong>
                                </td>


                                <!-- YA PAGADOS -->

                                <td data-label="Ya pagados">
                                    <?php echo $diasPagados; ?>
                                </td>


                                <!-- PENDIENTES -->

                                <td data-label="Pendientes">
                                    <strong><?php echo $diasPendientes; ?></strong>
                                </td>


                                <!-- DÍAS A LIQUIDAR -->

                                <td
                                    class="col-dias"
                                    data-label="Días a liquidar"
                                >

                                    <input
                                        type="number"
                                        class="input-dias-sac"
                                        name="empleados[<?php echo $empleadoId; ?>][dias_sac]"
                                        value="<?php echo $diasSac; ?>"
                                        min="0"
                                        max="<?php echo max(0, $diasPendientes); ?>"
                                        step="1"
                                        required
                                        <?php echo $diasPendientes <= 0 ? 'disabled' : ''; ?>
                                        data-event-input="liquidacion_novedades_sac-3"
                                        data-event-change="liquidacion_novedades_sac-4"
                                    >

                                    <?php if ($diasPendientes <= 0): ?>
                                        <input
                                            type="hidden"
                                            name="empleados[<?php echo $empleadoId; ?>][dias_sac]"
                                            value="0"
                                        >
                                    <?php endif; ?>

                                </td>


                                <!-- ESTADO SAC -->

                                <td
                                    class="col-tipo-sac"
                                    data-label="Estado SAC"
                                >

                                    <span
                                        class="badge badge-sac <?php
                                            echo $claseBadgeSac;
                                        ?>"
                                    >
                                        <?php
                                        echo htmlspecialchars(
                                            $textoBadgeSac,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        );
                                        ?>
                                    </span>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>


                <!-- =================================
                     ACCIONES INFERIORES
                ================================== -->

                <div class="acciones-inferiores">

                    <a
                        href="<?php
                            echo htmlspecialchars(
                                $liquidacionNovedadesSacVolver,
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
                        Guardar Novedades SAC
                    </button>

                </div>

            </form>


        <?php else: ?>

            <div class="sin-resultados">
                No hay empleados disponibles para liquidar SAC en el semestre seleccionado.
            </div>

        <?php endif; ?>


    </div>

</div>


<script src="<?= htmlspecialchars(sigenmuniUrlAsset('js/liquidacion/liquidacion_novedades_sac.js'), ENT_QUOTES, 'UTF-8') ?>"></script>

</body>

</html>
