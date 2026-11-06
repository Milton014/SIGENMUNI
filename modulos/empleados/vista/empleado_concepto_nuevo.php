<?php

require_once __DIR__ . '/../../../core/Url.php';
require_once __DIR__ . '/../../../core/Csrf.php';


/*
|--------------------------------------------------------------------------
| NUEVA ASIGNACIÓN - FRONT CONTROLLER + ROUTER
|--------------------------------------------------------------------------
*/

$empleadoConceptoNuevoAccion =
    sigenmuniUrlRuta('empleado-conceptos/nuevo', $contextoAsignaciones);


$empleadoConceptoNuevoCancelar =
    sigenmuniUrlRuta('empleado-conceptos', $contextoAsignaciones);


$empleadoConceptoNuevoCsrf =
    sigenmuniCsrfToken();

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Nueva Asignación de Concepto - SIGENMUNI</title>

<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/empleados/empleado_concepto_nuevo.css'), ENT_QUOTES, 'UTF-8') ?>">

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
        Asignación de Conceptos al Personal
    </p>

</div>


<!-- =========================================
     CONTENIDO
========================================= -->

<div class="contenedor">

    <div class="panel">

        <h2>
            Nueva Asignación de Concepto
        </h2>


        <!-- ALERTA JAVASCRIPT -->

        <div
            id="alertaFormulario"
            class="mensaje mensaje-error"
            data-estilo="empleado_concepto_nuevo-1"
        >
        </div>


        <!-- MENSAJE DEL CONTROLADOR -->

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


        <!-- FORMULARIO -->

        <form
            action="<?php
                echo htmlspecialchars(
                    $empleadoConceptoNuevoAccion,
                    ENT_QUOTES,
                    'UTF-8'
                );
            ?>"
            method="POST"
            id="formAsignacion"
            novalidate
        >

            <input
                type="hidden"
                name="_csrf"
                value="<?php
                    echo htmlspecialchars(
                        $empleadoConceptoNuevoCsrf,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
            >


            <!-- EMPLEADO -->

            <div class="grupo">

                <label for="empleado_id">
                    Empleado *
                </label>

                <select
                    name="empleado_id"
                    id="empleado_id"
                >

                    <option value="">
                        Seleccione un empleado
                    </option>

                    <?php foreach ($empleadosDisponibles as $empleado): ?>

                        <option
                            value="<?php echo (int)$empleado['id']; ?>"
                            <?php
                            echo (
                                (string)($datosAsignacion['empleado_id'] ?? '')
                                ===
                                (string)$empleado['id']
                            )
                                ? 'selected'
                                : '';
                            ?>
                        >

                            <?php
                            echo htmlspecialchars(
                                $empleado['apellido']
                                . ', '
                                . $empleado['nombre']
                                . ' - Legajo: '
                                . $empleado['nro_legajo'],
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <!-- CONCEPTO -->

            <div class="grupo">

                <label for="concepto_id">
                    Concepto *
                </label>

                <select
                    name="concepto_id"
                    id="concepto_id"
                    data-event-change="empleado_concepto_nuevo-1"
                >

                    <option
                        value=""
                        data-codigo=""
                        data-categoria=""
                        data-forma=""
                    >
                        Seleccione un concepto
                    </option>

                    <?php foreach ($conceptosDisponibles as $concepto): ?>

                        <?php

                        $codigoConcepto =
                            (string)($concepto['codigo'] ?? '');

                        $categoriaConcepto =
                            strtoupper(
                                trim(
                                    (string)($concepto['categoria'] ?? '')
                                )
                            );

                        $formaConcepto =
                            strtoupper(
                                trim(
                                    (string)($concepto['forma_calculo'] ?? '')
                                )
                            );

                        if ($formaConcepto === 'FIJO') {
                            $formaConcepto = 'MANUAL';
                        }

                        ?>

                        <option
                            value="<?php echo (int)$concepto['id']; ?>"
                            data-codigo="<?php
                                echo htmlspecialchars(
                                    $codigoConcepto,
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                            ?>"
                            data-categoria="<?php
                                echo htmlspecialchars(
                                    $categoriaConcepto,
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                            ?>"
                            data-forma="<?php
                                echo htmlspecialchars(
                                    $formaConcepto,
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                            ?>"
                            <?php
                            echo (
                                (string)($datosAsignacion['concepto_id'] ?? '')
                                ===
                                (string)$concepto['id']
                            )
                                ? 'selected'
                                : '';
                            ?>
                        >

                            <?php
                            echo htmlspecialchars(
                                $codigoConcepto
                                . ' - '
                                . $concepto['nombre'],
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <!-- INFORMACIÓN DEL CONCEPTO -->

            <div
                id="conceptoInfo"
                class="concepto-info"
            >

                <div id="conceptoInfoTexto">
                </div>

            </div>


            <!-- =====================================
                 MONTO
            ====================================== -->

            <div
                id="grupoMonto"
                class="campo-dinamico"
            >

                <div class="campo-resaltado">

                    <div class="grupo" data-estilo="empleado_concepto_nuevo-2">

                        <label
                            for="monto_manual"
                            id="labelMonto"
                        >
                            Monto *
                        </label>

                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            name="monto_manual"
                            id="monto_manual"
                            value="<?php
                                echo htmlspecialchars(
                                    $datosAsignacion['monto_manual']
                                    ?? '0.00',
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                            ?>"
                        >

                        <div
                            class="ayuda"
                            id="ayudaMonto"
                        >
                            Ingrese el importe correspondiente a este empleado.
                        </div>

                    </div>

                </div>

            </div>


            <!-- =====================================
                 PORCENTAJE
            ====================================== -->

            <div
                id="grupoPorcentaje"
                class="campo-dinamico"
            >

                <div class="campo-resaltado">

                    <div class="grupo" data-estilo="empleado_concepto_nuevo-3">

                        <label for="porcentaje_manual">
                            Porcentaje *
                        </label>

                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            name="porcentaje_manual"
                            id="porcentaje_manual"
                            value="<?php
                                echo htmlspecialchars(
                                    $datosAsignacion['porcentaje_manual']
                                    ?? '0.00',
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                            ?>"
                        >

                        <div class="ayuda">
                            Ingrese el porcentaje particular que corresponde a este empleado.
                        </div>

                    </div>

                </div>

            </div>


            <!-- =====================================
                 CANTIDAD
                 SOLO ASIGNACIONES FAMILIARES
            ====================================== -->

            <div
                id="grupoCantidad"
                class="campo-dinamico"
            >

                <div class="grupo">

                    <label for="cantidad">
                        Cantidad *
                    </label>

                    <input
                        type="number"
                        step="1"
                        min="1"
                        name="cantidad"
                        id="cantidad"
                        value="<?php
                            echo htmlspecialchars(
                                $datosAsignacion['cantidad']
                                ?? '1.00',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                    >

                    <div class="ayuda">
                        Para asignaciones familiares, el monto ingresado se toma
                        como importe por unidad y se multiplica por esta cantidad.
                    </div>

                </div>

            </div>


            <!-- FECHAS -->

            <div class="grid-doble">


                <div class="grupo">

                    <label for="fecha_desde">
                        Fecha Desde *
                    </label>

                    <input
                        type="date"
                        name="fecha_desde"
                        id="fecha_desde"
                        value="<?php
                            echo htmlspecialchars(
                                $datosAsignacion['fecha_desde'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                    >

                </div>


                <div class="grupo">

                    <label for="fecha_hasta">
                        Fecha Hasta
                    </label>

                    <input
                        type="date"
                        name="fecha_hasta"
                        id="fecha_hasta"
                        value="<?php
                            echo htmlspecialchars(
                                $datosAsignacion['fecha_hasta'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                    >

                    <div class="ayuda">
                        Puede dejarse vacía si la asignación no tiene vencimiento.
                    </div>

                </div>

            </div>


            <!-- OBSERVACIÓN -->

            <div class="grupo">

                <label for="observacion">
                    Observación
                </label>

                <textarea
                    name="observacion"
                    id="observacion"
                    placeholder="Ingrese una observación opcional"
                ><?php
                    echo htmlspecialchars(
                        $datosAsignacion['observacion'] ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?></textarea>

            </div>


            <!-- BOTONES -->

            <div class="acciones">

                <button
                    type="submit"
                    class="btn btn-guardar"
                >
                    Guardar Asignación
                </button>


                <a
                    href="<?php
                        echo htmlspecialchars(
                            $empleadoConceptoNuevoCancelar,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                    class="btn btn-cancelar"
                >
                    Cancelar
                </a>

            </div>

        </form>

    </div>

</div>


<script src="<?= htmlspecialchars(sigenmuniUrlAsset('js/empleados/empleado_concepto_nuevo.js'), ENT_QUOTES, 'UTF-8') ?>"></script>


</body>

</html>
