<?php

require_once __DIR__ . '/../../../core/Url.php';
require_once __DIR__ . '/../../../core/Csrf.php';


/*
|--------------------------------------------------------------------------
| EDITAR ASIGNACIÓN - FRONT CONTROLLER + ROUTER
|--------------------------------------------------------------------------
*/

$empleadoConceptoEditarId =
    (int)(
        $datosAsignacion['id']
        ?? $asignacion['id']
        ?? 0
    );


$empleadoConceptoEditarAccion =
    sigenmuniUrlRuta('empleado-conceptos/editar', array_merge($contextoAsignaciones, [
            'id' =>
                $empleadoConceptoEditarId
        ]));


$empleadoConceptoEditarCancelar =
    sigenmuniUrlRuta('empleado-conceptos', $contextoAsignaciones);


$empleadoConceptoEditarCsrf =
    sigenmuniCsrfToken();

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Editar Asignación de Concepto - SIGENMUNI</title>

<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/empleados/empleado_concepto_editar.css'), ENT_QUOTES, 'UTF-8') ?>">

</head>

<body>

<?php

/*
|--------------------------------------------------------------------------
| ESTADO HISTÓRICO DE LA ASIGNACIÓN
|--------------------------------------------------------------------------
|
| Se utiliza únicamente como ayuda visual.
|
| El modelo y el controlador son quienes protegen realmente la integridad:
| la vigencia editada debe quedar dentro de un período laboral válido.
|
*/

$zonaArgentina =
    new DateTimeZone(
        'America/Argentina/Buenos_Aires'
    );

$fechaHoy =
    (
        new DateTime(
            'now',
            $zonaArgentina
        )
    )->format(
        'Y-m-d'
    );


$registroActivoOriginal =
    (int)(
        $asignacion['activo']
        ?? 0
    ) === 1;


$fechaHastaOriginal =
    trim(
        (string)(
            $asignacion['fecha_hasta']
            ?? ''
        )
    );


$esRegistroHistoricoFinalizado =
    $registroActivoOriginal
    &&
    $fechaHastaOriginal !== ''
    &&
    $fechaHastaOriginal < $fechaHoy;

?>


<!-- =========================================
     HEADER
========================================= -->

<div class="header">

    <h1>
        SIGENMUNI
    </h1>

    <p>
        Edición de Conceptos por Empleado
    </p>

</div>


<!-- =========================================
     CONTENIDO
========================================= -->

<div class="contenedor">

    <div class="panel">

        <h2>
            Editar Asignación de Concepto
        </h2>


        <?php if ($esRegistroHistoricoFinalizado): ?>

            <div class="mensaje mensaje-advertencia-historica">

                <strong>
                    Registro histórico finalizado
                </strong>

                <div data-estilo="empleado_concepto_editar-1">
                    Esta asignación ya finalizó por fecha.
                    Puede editarse para corregir información histórica, pero los cambios
                    deben conservar coherencia con el período laboral al que pertenece.
                </div>

                <ul>
                    <li>
                        Las modificaciones afectan información histórica del empleado.
                    </li>
                    <li>
                        Empleado y Concepto quedan bloqueados para conservar la
                        identidad histórica de la asignación.
                    </li>
                    <li>
                        La vigencia no puede atravesar un período de inactividad.
                    </li>
                    <li>
                        El sistema validará nuevamente las fechas antes de guardar.
                    </li>
                </ul>

            </div>

        <?php endif; ?>


        <!-- ALERTA JAVASCRIPT -->

        <div
            id="alertaFormulario"
            class="mensaje mensaje-error"
            data-estilo="empleado_concepto_editar-2"
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
                    $empleadoConceptoEditarAccion,
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
                        $empleadoConceptoEditarCsrf,
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

                <?php

                $empleadoIdVista =
                    $esRegistroHistoricoFinalizado
                        ? (int)($asignacion['empleado_id'] ?? 0)
                        : (int)($datosAsignacion['empleado_id'] ?? 0);

                ?>

                <?php if ($esRegistroHistoricoFinalizado): ?>

                    <input
                        type="hidden"
                        name="empleado_id"
                        value="<?php
                            echo (int)(
                                $asignacion['empleado_id']
                                ?? 0
                            );
                        ?>"
                    >

                <?php endif; ?>

                <select
                    <?php if (!$esRegistroHistoricoFinalizado): ?>
                        name="empleado_id"
                    <?php endif; ?>
                    id="empleado_id"
                    <?php
                    echo $esRegistroHistoricoFinalizado
                        ? 'disabled'
                        : '';
                    ?>
                >

                    <option value="">
                        Seleccione un empleado
                    </option>

                    <?php foreach ($empleadosDisponibles as $empleado): ?>

                        <option
                            value="<?php echo (int)$empleado['id']; ?>"
                            <?php
                            echo (
                                $empleadoIdVista
                                ===
                                (int)$empleado['id']
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

                <?php if ($esRegistroHistoricoFinalizado): ?>

                    <div class="ayuda">
                        El empleado no puede modificarse porque esta asignación
                        pertenece a un registro histórico finalizado.
                    </div>

                <?php endif; ?>

            </div>


            <!-- CONCEPTO -->

            <div class="grupo">

                <label for="concepto_id">
                    Concepto *
                </label>

                <?php

                $conceptoIdVista =
                    $esRegistroHistoricoFinalizado
                        ? (int)($asignacion['concepto_id'] ?? 0)
                        : (int)($datosAsignacion['concepto_id'] ?? 0);

                ?>

                <?php if ($esRegistroHistoricoFinalizado): ?>

                    <input
                        type="hidden"
                        name="concepto_id"
                        value="<?php
                            echo (int)(
                                $asignacion['concepto_id']
                                ?? 0
                            );
                        ?>"
                    >

                <?php endif; ?>

                <select
                    <?php if (!$esRegistroHistoricoFinalizado): ?>
                        name="concepto_id"
                    <?php endif; ?>
                    id="concepto_id"
                    data-event-change="empleado_concepto_editar-1"
                    <?php
                    echo $esRegistroHistoricoFinalizado
                        ? 'disabled'
                        : '';
                    ?>
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

                        /*
                        |--------------------------------------------------------------------------
                        | COMPATIBILIDAD HISTÓRICA
                        |--------------------------------------------------------------------------
                        |
                        | FIJO ya no se utiliza en la nueva arquitectura.
                        | Si existe una asignación histórica, se trata visualmente
                        | como MANUAL.
                        |
                        */

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
                                $conceptoIdVista
                                ===
                                (int)$concepto['id']
                            )
                                ? 'selected'
                                : '';
                            ?>
                        >

                            <?php
                            echo htmlspecialchars(
                                $codigoConcepto
                                . ' - '
                                . ($concepto['nombre'] ?? ''),
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>

                        </option>

                    <?php endforeach; ?>

                </select>

                <?php if ($esRegistroHistoricoFinalizado): ?>

                    <div class="ayuda">
                        El concepto no puede modificarse porque forma parte del
                        registro histórico original de esta asignación.
                    </div>

                <?php endif; ?>

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

                    <div class="grupo" data-estilo="empleado_concepto_editar-3">

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

                    <div class="grupo" data-estilo="empleado_concepto_editar-4">

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
                        Para asignaciones familiares, el monto es unitario y
                        se multiplica por esta cantidad.
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
                                $datosAsignacion['fecha_desde']
                                ?? '',
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
                                $datosAsignacion['fecha_hasta']
                                ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                    >

                    <div class="ayuda">

                        <?php if ($esRegistroHistoricoFinalizado): ?>

                            Esta asignación es histórica.
                            Si modifica la fecha, debe continuar dentro del mismo período
                            laboral del empleado.

                        <?php else: ?>

                            Puede dejarse vacía cuando la asignación no tiene vencimiento.

                        <?php endif; ?>

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
                        $datosAsignacion['observacion']
                        ?? '',
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
                    <?php
                    echo $esRegistroHistoricoFinalizado
                        ? 'Actualizar Registro Histórico'
                        : 'Actualizar Asignación';
                    ?>
                </button>


                <a
                    href="<?php
                        echo htmlspecialchars(
                            $empleadoConceptoEditarCancelar,
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


<script src="<?= htmlspecialchars(sigenmuniUrlAsset('js/empleados/empleado_concepto_editar.js'), ENT_QUOTES, 'UTF-8') ?>"></script>


</body>

</html>
