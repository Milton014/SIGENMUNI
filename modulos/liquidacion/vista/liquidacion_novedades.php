<?php

require_once __DIR__ . '/../../../core/Url.php';
require_once __DIR__ . '/../../../core/Csrf.php';


/*
|--------------------------------------------------------------------------
| NOVEDADES MENSUALES - ROUTER
|--------------------------------------------------------------------------
*/

$liquidacionNovedadesId =
    (int)(
        $liquidacion['id']
        ?? 0
    );


$liquidacionNovedadesAccion =
    sigenmuniUrlRuta(
        'liquidacion/novedades',
        [
            'id' =>
                $liquidacionNovedadesId
        ]
    );


$liquidacionNovedadesVolver =
    sigenmuniUrlRuta(
        'liquidacion'
    );


$liquidacionNovedadesCsrf =
    sigenmuniCsrfToken();

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Novedades de Liquidación - SIGENMUNI</title>

<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/liquidacion/liquidacion_novedades.css'), ENT_QUOTES, 'UTF-8') ?>">

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
        Novedades de Liquidación por Empleado
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
                    Novedades de Liquidación
                </h2>

                <p>
                    Defina días liquidados y presentismo para las excepciones del período.
                </p>

            </div>


            <div class="acciones-superiores">

                <a
                    href="<?php
                        echo htmlspecialchars(
                            $liquidacionNovedadesVolver,
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


        <!-- =====================================
             DATOS DE LA LIQUIDACIÓN
        ====================================== -->

        <?php if (!empty($liquidacion)): ?>

            <?php

            $tipoVisual =
                strtoupper(
                    trim(
                        (string)(
                            $liquidacion['tipo_liquidacion']
                            ?? ''
                        )
                    )
                );

            switch ($tipoVisual) {

                case 'MENSUAL':
                    $tipoVisual = 'Mensual';
                    break;

                case 'COMPLEMENTARIA':
                    $tipoVisual = 'Complementaria';
                    break;
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

            <div class="resumen-liquidacion">

                <div class="dato">
                    <strong>Liquidación</strong>
                    <span>
                        #<?php echo (int)$liquidacion['id']; ?>
                    </span>
                </div>

                <div class="dato">
                    <strong>Tipo</strong>
                    <span>
                        <?php
                        echo htmlspecialchars(
                            $tipoVisual,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>
                    </span>
                </div>

                <div class="dato">
                    <strong>Período</strong>
                    <span>
                        <?php
                        echo htmlspecialchars(
                            $liquidacion['periodo'] ?? '-',
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
                Funcionamiento de las novedades
            </strong>

            El sistema calcula automáticamente los <strong data-estilo="liquidacion_novedades-1">días laborales habilitados</strong>
            según el historial laboral del empleado. Un mes completo equivale a 30 días.
            Los <strong data-estilo="liquidacion_novedades-2">Días a liquidar</strong> pueden reducirse por una novedad,
            pero nunca superar los días habilitados. Si no corresponde Presentismo,
            desactive la opción para ese empleado.

        </div>


        <?php if (!empty($empleadosNovedades)): ?>

            <!-- =====================================
                 HERRAMIENTAS
            ====================================== -->

            <div class="herramientas">

                <div class="busqueda">

                    <input
                        type="text"
                        id="buscarEmpleado"
                        placeholder="Buscar por apellido, nombre o legajo..."
                    >

                </div>


                <div class="acciones-rapidas">

                    <button
                        type="button"
                        class="btn btn-todos-30"
                        data-event-click="liquidacion_novedades-1"
                    >
                        Usar días habilitados
                    </button>

                    <button
                        type="button"
                        class="btn btn-todos-presentismo"
                        data-event-click="liquidacion_novedades-2"
                    >
                        Presentismo a todos
                    </button>

                    <button
                        type="button"
                        class="btn btn-quitar-presentismo"
                        data-event-click="liquidacion_novedades-3"
                    >
                        Quitar presentismo a todos
                    </button>

                </div>

            </div>


            <div class="resumen-novedades">

                Empleados con novedades guardadas:
                <strong>
                    <?php echo (int)($cantidadNovedades ?? 0); ?>
                </strong>

            </div>


            <!-- =====================================
                 FORMULARIO
            ====================================== -->

            <form
                method="POST"
                action="<?php
                    echo htmlspecialchars(
                        $liquidacionNovedadesAccion,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                id="formNovedades"
            >

                <input
                    type="hidden"
                    name="_csrf"
                    value="<?php
                        echo htmlspecialchars(
                            $liquidacionNovedadesCsrf,
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
                                <th>Días habilitados</th>
                                <th>Días a liquidar</th>
                                <th>Presentismo</th>
                                <th>Observación</th>
                            </tr>

                        </thead>


                        <tbody id="cuerpoEmpleados">

                        <?php foreach ($empleadosNovedades as $empleado): ?>

                            <?php

                            $empleadoId =
                                (int)(
                                    $empleado['id']
                                    ?? $empleado['empleado_id']
                                    ?? 0
                                );

                            $diasHabilitados =
                                (int)(
                                    $empleado['dias_laborales_habilitados']
                                    ?? $empleado['limite_dias_liquidables']
                                    ?? 30
                                );


                            if ($diasHabilitados < 0) {
                                $diasHabilitados = 0;
                            }


                            if ($diasHabilitados > 30) {
                                $diasHabilitados = 30;
                            }


                            $dias =
                                (int)(
                                    $empleado['dias_liquidados']
                                    ?? $diasHabilitados
                                );


                            if ($dias < 0) {
                                $dias = 0;
                            }


                            if ($dias > $diasHabilitados) {
                                $dias = $diasHabilitados;
                            }


                            $aplicaPresentismo =
                                (int)(
                                    $empleado['aplica_presentismo']
                                    ?? 1
                                ) === 1;

                            $observacion =
                                (string)(
                                    $empleado['observacion_novedad']
                                    ?? ''
                                );

                            $tieneNovedad =
                                !empty(
                                    $empleado['novedad_id']
                                );

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
                                    )
                                );

                            ?>

                            <tr
                                class="<?php
                                    echo $tieneNovedad
                                        ? 'fila-novedad'
                                        : '';
                                ?>"
                                data-busqueda="<?php
                                    echo htmlspecialchars(
                                        $textoBusqueda,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                ?>"
                                data-dias-habilitados="<?php
                                    echo (int)$diasHabilitados;
                                ?>"
                            >

                                <!-- LEGAJO -->

                                <td class="col-legajo" data-label="Legajo">

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


                                <!-- DÍAS HABILITADOS -->

                                <td
                                    class="col-habilitados"
                                    data-label="Días habilitados"
                                >

                                    <span
                                        class="dias-habilitados <?php
                                            echo $diasHabilitados < 30
                                                ? 'dias-habilitados-parcial'
                                                : '';
                                        ?>"
                                        title="Máximo permitido según el historial laboral del empleado"
                                    >
                                        <?php echo (int)$diasHabilitados; ?>
                                    </span>

                                </td>


                                <!-- DÍAS A LIQUIDAR -->

                                <td
                                    class="col-dias"
                                    data-label="Días a liquidar"
                                >

                                    <input
                                        type="number"
                                        class="input-dias"
                                        name="empleados[<?php echo $empleadoId; ?>][dias_liquidados]"
                                        value="<?php echo $dias; ?>"
                                        min="0"
                                        max="<?php echo (int)$diasHabilitados; ?>"
                                        data-max-habilitado="<?php echo (int)$diasHabilitados; ?>"
                                        step="1"
                                        required
                                        data-event-change="liquidacion_novedades-4"
                                    >

                                </td>


                                <!-- PRESENTISMO -->

                                <td
                                    class="col-presentismo"
                                    data-label="Presentismo"
                                >

                                    <div class="switch-wrap">

                                        <label class="switch">

                                            <input
                                                type="checkbox"
                                                class="input-presentismo"
                                                name="empleados[<?php echo $empleadoId; ?>][aplica_presentismo]"
                                                value="1"
                                                <?php
                                                    echo $aplicaPresentismo
                                                        ? 'checked'
                                                        : '';
                                                ?>
                                                data-event-change="liquidacion_novedades-5"
                                            >

                                            <span class="slider"></span>

                                        </label>

                                        <span
                                            class="estado-presentismo <?php
                                                echo $aplicaPresentismo
                                                    ? 'estado-si'
                                                    : 'estado-no';
                                            ?>"
                                        >
                                            <?php
                                                echo $aplicaPresentismo
                                                    ? 'Sí'
                                                    : 'No';
                                            ?>
                                        </span>

                                    </div>

                                </td>


                                <!-- OBSERVACIÓN -->

                                <td
                                    class="col-observacion"
                                    data-label="Observación"
                                >

                                    <input
                                        type="text"
                                        class="input-observacion"
                                        name="empleados[<?php echo $empleadoId; ?>][observacion]"
                                        value="<?php
                                            echo htmlspecialchars(
                                                $observacion,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                        ?>"
                                        maxlength="255"
                                        placeholder="Opcional"
                                        data-event-input="liquidacion_novedades-6"
                                    >

                                </td>

                            </tr>

                        <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>


                <!-- =================================
                     ACCIONES
                ================================== -->

                <div class="acciones-inferiores">

                    <a
                        href="<?php
                            echo htmlspecialchars(
                                $liquidacionNovedadesVolver,
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
                        Guardar Novedades
                    </button>

                </div>

            </form>

        <?php else: ?>

            <div class="sin-resultados">

                No hay empleados disponibles para cargar novedades.

            </div>

        <?php endif; ?>

    </div>

</div>


<script src="<?= htmlspecialchars(sigenmuniUrlAsset('js/liquidacion/liquidacion_novedades.js'), ENT_QUOTES, 'UTF-8') ?>"></script>

</body>

</html>
