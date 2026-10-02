<?php

require_once __DIR__ . '/../../../core/Url.php';
require_once __DIR__ . '/../../../core/Csrf.php';


/*
|--------------------------------------------------------------------------
| COMPLEMENTARIA SAC - ROUTER
|--------------------------------------------------------------------------
*/

$liquidacionComplementariaSacId =
    (int)(
        $liquidacion['id']
        ?? 0
    );


$liquidacionComplementariaSacAccion =
    sigenmuniUrlRuta(
        'liquidacion/complementaria-sac',
        [
            'id' =>
                $liquidacionComplementariaSacId
        ]
    );


$liquidacionComplementariaSacVolver =
    sigenmuniUrlRuta(
        'liquidacion'
    );


$liquidacionComplementariaSacCsrf =
    sigenmuniCsrfToken();

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Personal y Días SAC - Complementaria SAC - SIGENMUNI</title>

<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/liquidacion/liquidacion_complementaria_sac.css'), ENT_QUOTES, 'UTF-8') ?>">

</head>

<body>

<div class="header">
    <h1>SIGENMUNI</h1>
    <p>Liquidación Complementaria de SAC</p>
</div>

<div class="contenedor">

    <div class="panel">

        <div class="topbar">

            <div class="titulo">
                <h2>Personal y Días SAC</h2>
                <p>
                    Seleccione empleados con saldo SAC pendiente y defina los días que se liquidarán ahora.
                </p>
            </div>

            <div>
                <a
                    href="<?php
                        echo htmlspecialchars(
                            $liquidacionComplementariaSacVolver,
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

        $anio = 0;
        $mes = 0;

        if (
            preg_match(
                '/^(\d{4})-(\d{2})$/',
                $periodoLiquidacion,
                $coincidencias
            )
        ) {
            $anio = (int)$coincidencias[1];
            $mes = (int)$coincidencias[2];
        }

        if ($mes >= 1 && $mes <= 6) {
            $semestreTexto = '1.er semestre';
        } elseif ($mes >= 7 && $mes <= 12) {
            $semestreTexto = '2.º semestre';
        } else {
            $semestreTexto = '-';
        }

        $fechaVisual =
            !empty($liquidacion['fecha_liquidacion'])
                ? date(
                    'd/m/Y',
                    strtotime($liquidacion['fecha_liquidacion'])
                )
                : '-';

        ?>


        <?php if (!empty($liquidacion)): ?>

            <div class="resumen-liquidacion">

                <div class="dato">
                    <strong>Liquidación</strong>
                    <span>#<?php echo (int)$liquidacion['id']; ?></span>
                </div>

                <div class="dato">
                    <strong>Tipo</strong>
                    <span>Complementaria SAC</span>
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
                    <strong>Fecha</strong>
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


        <div class="aviso">

            <strong>Control automático de saldo SAC</strong><br>

            El sistema muestra los días SAC devengados, ya pagados y pendientes.
            Un empleado con saldo pendiente igual a 0 queda bloqueado y no puede
            volver a liquidarse.

            <div class="formula">
                SAC = Base Remunerativa × 50% × (Días SAC / 180)
            </div>

        </div>


        <?php if (!empty($empleadosSac)): ?>

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
                        class="btn btn-seleccionar"
                        data-event-click="liquidacion_complementaria_sac-1"
                    >
                        Seleccionar con saldo
                    </button>

                    <button
                        type="button"
                        class="btn btn-quitar"
                        data-event-click="liquidacion_complementaria_sac-2"
                    >
                        Quitar selección visible
                    </button>

                    <button
                        type="button"
                        class="btn btn-saldo"
                        data-event-click="liquidacion_complementaria_sac-3"
                    >
                        Usar saldo pendiente
                    </button>

                </div>

            </div>


            <div class="resumen-sac">

                <span>
                    Seleccionados:
                    <strong id="contadorSeleccionados">
                        <?php echo (int)($cantidadSeleccionados ?? 0); ?>
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

            </div>


            <form
                method="POST"
                action="<?php
                    echo htmlspecialchars(
                        $liquidacionComplementariaSacAccion,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                id="formComplementariaSac"
            >

                <input
                    type="hidden"
                    name="_csrf"
                    value="<?php
                        echo htmlspecialchars(
                            $liquidacionComplementariaSacCsrf,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                >

                <div class="tabla-contenedor">

                    <table>

                        <thead>

                            <tr>
                                <th>Incluir</th>
                                <th>Legajo</th>
                                <th>Empleado</th>
                                <th>DNI</th>
                                <th>Situación</th>
                                <th>Alta</th>
                                <th>Fecha Inactivo</th>
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

                            $incluido =
                                (int)(
                                    $empleado['incluido']
                                    ?? 0
                                ) === 1;

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

                            $puedeLiquidar =
                                (int)(
                                    $empleado['puede_liquidar_sac']
                                    ?? 0
                                ) === 1
                                &&
                                $diasPendientes > 0;

                            if (!$puedeLiquidar) {
                                $incluido = false;
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

                            if ($estadoSac === 'YA_LIQUIDADO') {
                                $claseEstado = 'badge-liquidado';
                                $textoEstado = 'Ya liquidado';
                                $claseFila = 'fila-liquidada';
                            } elseif ($estadoSac === 'PARCIAL') {
                                $claseEstado = 'badge-parcial';
                                $textoEstado = 'Parcial';
                                $claseFila = $incluido
                                    ? 'fila-seleccionada'
                                    : 'fila-parcial';
                            } else {
                                $claseEstado = 'badge-pendiente';
                                $textoEstado = 'Pendiente';
                                $claseFila = $incluido
                                    ? 'fila-seleccionada'
                                    : '';
                            }

                            $activo =
                                (int)(
                                    $empleado['activo']
                                    ?? 0
                                ) === 1;

                            $fechaAltaVisual =
                                !empty($empleado['fecha_alta'])
                                &&
                                strpos(
                                    (string)$empleado['fecha_alta'],
                                    '0000-00-00'
                                ) !== 0
                                    ? date(
                                        'd/m/Y',
                                        strtotime($empleado['fecha_alta'])
                                    )
                                    : '-';

                            $fechaInactivoVisual =
                                !empty($empleado['fecha_inactivo'])
                                &&
                                strpos(
                                    (string)$empleado['fecha_inactivo'],
                                    '0000-00-00'
                                ) !== 0
                                    ? date(
                                        'd/m/Y',
                                        strtotime($empleado['fecha_inactivo'])
                                    )
                                    : '-';

                            $advertencia =
                                trim(
                                    (string)(
                                        $empleado['advertencia_sac']
                                        ?? ''
                                    )
                                );

                            $textoBusqueda =
                                strtolower(
                                    trim(
                                        (string)($empleado['nro_legajo'] ?? '')
                                        . ' '
                                        . (string)($empleado['apellido'] ?? '')
                                        . ' '
                                        . (string)($empleado['nombre'] ?? '')
                                        . ' '
                                        . (string)($empleado['dni'] ?? '')
                                    )
                                );

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

                                <td data-label="Incluir">

                                    <input
                                        type="checkbox"
                                        class="check-incluir"
                                        name="empleados[<?php echo $empleadoId; ?>][incluir]"
                                        value="1"
                                        <?php echo $incluido ? 'checked' : ''; ?>
                                        <?php echo $puedeLiquidar ? '' : 'disabled'; ?>
                                        data-event-change="liquidacion_complementaria_sac-4"
                                    >

                                </td>


                                <td data-label="Legajo">
                                    <?php
                                    echo htmlspecialchars(
                                        $empleado['nro_legajo'] ?? '-',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>
                                </td>


                                <td
                                    class="col-empleado"
                                    data-label="Empleado"
                                >

                                    <strong>
                                        <?php
                                        echo htmlspecialchars(
                                            trim(
                                                (string)($empleado['apellido'] ?? '')
                                                . ', '
                                                . (string)($empleado['nombre'] ?? '')
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        );
                                        ?>
                                    </strong>

                                    <?php if ($advertencia !== ''): ?>

                                        <div class="advertencia">
                                            <?php
                                            echo htmlspecialchars(
                                                $advertencia,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                            ?>
                                        </div>

                                    <?php endif; ?>

                                </td>


                                <td data-label="DNI">
                                    <?php
                                    echo htmlspecialchars(
                                        $empleado['dni'] ?? '-',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>
                                </td>


                                <td data-label="Situación">

                                    <span
                                        class="badge <?php
                                            echo $activo
                                                ? 'badge-activo'
                                                : 'badge-inactivo';
                                        ?>"
                                    >
                                        <?php echo $activo ? 'Activo' : 'Inactivo'; ?>
                                    </span>

                                </td>


                                <td data-label="Alta">
                                    <?php echo htmlspecialchars($fechaAltaVisual, ENT_QUOTES, 'UTF-8'); ?>
                                </td>


                                <td data-label="Fecha Inactivo">
                                    <?php echo htmlspecialchars($fechaInactivoVisual, ENT_QUOTES, 'UTF-8'); ?>
                                </td>


                                <td data-label="Devengados">
                                    <?php echo $diasDevengados; ?>
                                </td>


                                <td data-label="Ya pagados">
                                    <?php echo $diasPagados; ?>
                                </td>


                                <td data-label="Pendientes">
                                    <strong><?php echo $diasPendientes; ?></strong>
                                </td>


                                <td data-label="Días a liquidar">

                                    <input
                                        type="number"
                                        class="input-dias-sac"
                                        name="empleados[<?php echo $empleadoId; ?>][dias_sac]"
                                        value="<?php echo $diasSac; ?>"
                                        min="1"
                                        max="<?php echo max(1,$diasPendientes); ?>"
                                        step="1"
                                        <?php
                                            echo (
                                                $incluido
                                                &&
                                                $puedeLiquidar
                                            )
                                                ? ''
                                                : 'disabled';
                                        ?>
                                        required
                                    >

                                </td>


                                <td data-label="Estado SAC">

                                    <span
                                        class="badge <?php echo $claseEstado; ?>"
                                    >
                                        <?php echo htmlspecialchars($textoEstado, ENT_QUOTES, 'UTF-8'); ?>
                                    </span>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>


                <div class="acciones-inferiores">

                    <a
                        href="<?php
                            echo htmlspecialchars(
                                $liquidacionComplementariaSacVolver,
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
                        Guardar Personal y Días SAC
                    </button>

                </div>

            </form>

        <?php else: ?>

            <div class="sin-resultados">
                No hay empleados disponibles para esta complementaria de SAC.
            </div>

        <?php endif; ?>

    </div>

</div>


<script src="<?= htmlspecialchars(sigenmuniUrlAsset('js/liquidacion/liquidacion_complementaria_sac.js'), ENT_QUOTES, 'UTF-8') ?>"></script>

</body>

</html>
