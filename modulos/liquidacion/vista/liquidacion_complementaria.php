<?php

require_once __DIR__ . '/../../../core/Url.php';
require_once __DIR__ . '/../../../core/Csrf.php';


$liquidacionComplementariaId =
    (int)(
        $liquidacion['id']
        ?? 0
    );


$liquidacionComplementariaAccion =
    sigenmuniUrlRuta(
        'liquidacion/complementaria',
        [
            'id' =>
                $liquidacionComplementariaId
        ]
    );


$liquidacionComplementariaVolver =
    sigenmuniUrlRuta(
        'liquidacion'
    );


$liquidacionComplementariaCsrf =
    sigenmuniCsrfToken();

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Personal y Novedades - Complementaria - SIGENMUNI</title>

<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/liquidacion/liquidacion_complementaria.css'), ENT_QUOTES, 'UTF-8') ?>">

</head>

<body>

<div class="header">
    <h1>SIGENMUNI</h1>
    <p>Liquidación Complementaria de Haberes</p>
</div>

<div class="contenedor">

    <div class="panel">

        <div class="topbar">

            <div class="titulo">
                <h2>Personal y Novedades</h2>
                <p>
                    Seleccione únicamente los empleados que deben ingresar en esta liquidación complementaria.
                </p>
            </div>

            <div class="acciones-superiores">
                <a
                    href="<?php
                        echo htmlspecialchars(
                            $liquidacionComplementariaVolver,
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


        <?php if (!empty($liquidacion)): ?>

            <?php
            $fechaVisual =
                !empty($liquidacion['fecha_liquidacion'])
                    ? date(
                        'd/m/Y',
                        strtotime($liquidacion['fecha_liquidacion'])
                    )
                    : '-';
            ?>

            <div class="resumen-liquidacion">

                <div class="dato">
                    <strong>Liquidación</strong>
                    <span>#<?php echo (int)$liquidacion['id']; ?></span>
                </div>

                <div class="dato">
                    <strong>Tipo</strong>
                    <span>Complementaria</span>
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
            <strong>Importante:</strong>
            solo se procesarán los empleados que queden marcados en la columna
            <b>Incluir</b>. El sistema calcula los <b>días laborales habilitados</b>
            según el historial laboral del período. Los días a liquidar pueden
            reducirse, pero nunca superar ese máximo. El Presentismo puede
            activarse o desactivarse individualmente.
        </div>


        <?php if (!empty($empleadosComplementaria)): ?>

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
                        data-event-click="liquidacion_complementaria-1"
                    >
                        Seleccionar visibles
                    </button>

                    <button
                        type="button"
                        class="btn btn-quitar"
                        data-event-click="liquidacion_complementaria-2"
                    >
                        Quitar selección visible
                    </button>

                    <button
                        type="button"
                        class="btn btn-dias"
                        data-event-click="liquidacion_complementaria-3"
                    >
                        Usar días habilitados
                    </button>

                </div>

            </div>


            <div class="resumen-seleccion">
                Empleados seleccionados:
                <strong id="contadorSeleccionados">
                    <?php echo (int)($cantidadSeleccionados ?? 0); ?>
                </strong>
            </div>


            <form
                method="POST"
                action="<?php
                    echo htmlspecialchars(
                        $liquidacionComplementariaAccion,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                id="formComplementaria"
            >

                <input
                    type="hidden"
                    name="_csrf"
                    value="<?php
                        echo htmlspecialchars(
                            $liquidacionComplementariaCsrf,
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
                                <th>Días habilitados</th>
                                <th>Días a liquidar</th>
                                <th>Presentismo</th>
                                <th>Observación</th>
                            </tr>
                        </thead>

                        <tbody id="cuerpoEmpleados">

                        <?php foreach ($empleadosComplementaria as $empleado): ?>

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

                            $diasHabilitados =
                                (int)(
                                    $empleado['dias_laborales_habilitados']
                                    ?? $empleado['limite_dias_liquidables']
                                    ?? 30
                                );

                            if ($diasHabilitados < 1) {
                                $diasHabilitados = 1;
                            }

                            if ($diasHabilitados > 30) {
                                $diasHabilitados = 30;
                            }


                            $dias =
                                (int)(
                                    $empleado['dias_liquidados']
                                    ?? $diasHabilitados
                                );

                            if (
                                $dias < 1
                                ||
                                $dias > $diasHabilitados
                            ) {
                                $dias = $diasHabilitados;
                            }

                            $presentismo =
                                (int)(
                                    $empleado['aplica_presentismo']
                                    ?? 1
                                ) === 1;

                            $observacion =
                                (string)(
                                    $empleado['observacion_novedad']
                                    ?? ''
                                );

                            $activo =
                                (int)(
                                    $empleado['activo']
                                    ?? 0
                                ) === 1;

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
                                class="<?php
                                    echo $incluido
                                        ? 'fila-seleccionada'
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

                                <td data-label="Incluir">

                                    <input
                                        type="checkbox"
                                        class="check-incluir"
                                        name="empleados[<?php echo $empleadoId; ?>][incluir]"
                                        value="1"
                                        <?php echo $incluido ? 'checked' : ''; ?>
                                        data-event-change="liquidacion_complementaria-4"
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


                                <td data-label="Días habilitados">

                                    <span
                                        class="dias-habilitados <?php
                                            echo $diasHabilitados < 30
                                                ? 'dias-habilitados-parcial'
                                                : '';
                                        ?>"
                                        title="Máximo permitido según historial laboral"
                                    >
                                        <?php echo (int)$diasHabilitados; ?>
                                    </span>

                                </td>


                                <td data-label="Días a liquidar">

                                    <input
                                        type="number"
                                        class="input-dias campo-novedad"
                                        name="empleados[<?php echo $empleadoId; ?>][dias_liquidados]"
                                        value="<?php echo $dias; ?>"
                                        min="1"
                                        max="<?php echo (int)$diasHabilitados; ?>"
                                        data-max-habilitado="<?php echo (int)$diasHabilitados; ?>"
                                        step="1"
                                        <?php echo $incluido ? '' : 'disabled'; ?>
                                        required
                                    >

                                </td>


                                <td data-label="Presentismo">

                                    <div class="switch-wrap">

                                        <label class="switch">

                                            <input
                                                type="checkbox"
                                                class="input-presentismo campo-novedad"
                                                name="empleados[<?php echo $empleadoId; ?>][aplica_presentismo]"
                                                value="1"
                                                <?php echo $presentismo ? 'checked' : ''; ?>
                                                <?php echo $incluido ? '' : 'disabled'; ?>
                                                data-event-change="liquidacion_complementaria-5"
                                            >

                                            <span class="slider"></span>

                                        </label>

                                        <span
                                            class="estado-presentismo <?php
                                                echo $presentismo
                                                    ? 'estado-si'
                                                    : 'estado-no';
                                            ?>"
                                        >
                                            <?php echo $presentismo ? 'Sí' : 'No'; ?>
                                        </span>

                                    </div>

                                </td>


                                <td
                                    class="col-observacion"
                                    data-label="Observación"
                                >

                                    <input
                                        type="text"
                                        class="input-observacion campo-novedad"
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
                                        <?php echo $incluido ? '' : 'disabled'; ?>
                                    >

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
                                $liquidacionComplementariaVolver,
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
                        Guardar Personal y Novedades
                    </button>

                </div>

            </form>

        <?php else: ?>

            <div class="sin-resultados">
                No hay empleados disponibles para esta liquidación complementaria.
            </div>

        <?php endif; ?>

    </div>

</div>


<script src="<?= htmlspecialchars(sigenmuniUrlAsset('js/liquidacion/liquidacion_complementaria.js'), ENT_QUOTES, 'UTF-8') ?>"></script>

</body>

</html>
