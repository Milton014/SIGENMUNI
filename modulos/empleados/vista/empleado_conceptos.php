<?php

require_once __DIR__ . '/../../../core/Url.php';
require_once __DIR__ . '/../../../core/Csrf.php';


/*
|--------------------------------------------------------------------------
| NAVEGACIÓN POR FRONT CONTROLLER + ROUTER
|--------------------------------------------------------------------------
*/

$empleadoConceptosUrlListado =
    sigenmuniUrlRuta(
        'empleado-conceptos'
    );


$empleadoConceptosAccionBuscar =
    sigenmuniUrlEntrada();


$empleadoConceptosUrlNuevo =
    sigenmuniUrlRuta('empleado-conceptos/nuevo', $contextoAsignaciones);


$empleadoConceptosUrlMenu =
    sigenmuniUrlRuta(
    'inicio'
);


$empleadoConceptosUrlEstado =
    sigenmuniUrlRuta('empleado-conceptos/estado', $contextoAsignaciones);


$empleadoConceptosCsrf =
    sigenmuniCsrfToken();

$paginacionRuta = 'empleado-conceptos';
$paginacionParametros = $contextoAsignaciones;
$paginacionEntidad = 'asignaciones';
?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Conceptos por Empleado - SIGENMUNI</title>

<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/empleados/empleado_conceptos.css'), ENT_QUOTES, 'UTF-8') ?>">

<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/compartido/paginacion.css'), ENT_QUOTES, 'UTF-8') ?>">
</head>

<body>


<!-- =========================================
     HEADER GLOBAL
========================================= -->

<?php

$sigenmuniHeaderSubtitulo =
    'Gestión de Conceptos por Empleado';

require __DIR__
    . '/../../../componentes/header_sigenmuni.php';

?>


<!-- =========================================
     CONTENIDO
========================================= -->

<div class="contenedor">

    <div class="panel">


        <!-- =====================================
             TOPBAR
        ====================================== -->

        <div class="topbar">

            <div class="titulo-seccion">

                <h2>
                    Asignación de Conceptos a Empleados
                </h2>

                <p>
                    Administración de conceptos particulares asignados al personal.
                </p>

            </div>


            <div class="acciones-superiores">

                <a
                    href="<?php
                    echo htmlspecialchars(
                        $empleadoConceptosUrlNuevo,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                    class="btn btn-nuevo"
                >
                    + Nueva Asignación
                </a>


                <a
                    href="<?php
                    echo htmlspecialchars(
                        $empleadoConceptosUrlMenu,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                    class="btn btn-volver"
                >
                    Volver al Menú
                </a>

            </div>

        </div>


        <!-- =====================================
             MENSAJES
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
             FILTROS
        ====================================== -->

        <form
            method="GET"
            action="<?php
                echo htmlspecialchars(
                    $empleadoConceptosAccionBuscar,
                    ENT_QUOTES,
                    'UTF-8'
                );
            ?>"
            class="filtros"
        >

            <input
                type="hidden"
                name="r"
                value="empleado-conceptos"
            >

            <input
                type="text"
                name="buscar_empleado"
                maxlength="120"
                aria-label="Buscar por apellido, nombre o legajo exacto"
                placeholder="Apellido, nombre o legajo exacto"
                value="<?php
                    echo htmlspecialchars(
                        $buscarEmpleado ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
            >


            <select name="concepto_id" aria-label="Concepto">

                <option value="">
                    -- Todos los conceptos --
                </option>

                <?php foreach ($conceptos as $concepto): ?>

                    <option
                        value="<?php echo (int)$concepto['id']; ?>"
                        <?php
                        echo (
                            (int)($conceptoId ?? 0)
                            ===
                            (int)$concepto['id']
                        )
                            ? 'selected'
                            : '';
                        ?>
                    >

                        <?php
                        echo htmlspecialchars(
                            $concepto['codigo']
                            . ' - '
                            . $concepto['nombre'],
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>

                    </option>

                <?php endforeach; ?>

            </select>


            <select name="estado" aria-label="Estado de la asignación">

                <option value="">
                    -- Todos los estados --
                </option>

                <option
                    value="vigente"
                    <?php
                    echo (($estado ?? '') === 'vigente')
                        ? 'selected'
                        : '';
                    ?>
                >
                    Vigentes
                </option>

                <option
                    value="programado"
                    <?php
                    echo (($estado ?? '') === 'programado')
                        ? 'selected'
                        : '';
                    ?>
                >
                    Programados
                </option>

                <option
                    value="finalizado"
                    <?php
                    echo (($estado ?? '') === 'finalizado')
                        ? 'selected'
                        : '';
                    ?>
                >
                    Finalizados por fecha
                </option>

                <option
                    value="inactivo"
                    <?php
                    echo (($estado ?? '') === 'inactivo')
                        ? 'selected'
                        : '';
                    ?>
                >
                    Registros inactivos
                </option>

            </select>


            <label class="paginacion-tamano" for="por_pagina">Por página
                <select id="por_pagina" name="por_pagina">
                    <?php foreach ([10, 25, 50] as $cantidadPagina): ?>
                        <option value="<?= $cantidadPagina ?>" <?= $paginacion['por_pagina'] === $cantidadPagina ? 'selected' : '' ?>><?= $cantidadPagina ?></option>
                    <?php endforeach; ?>
                </select>
            </label>

            <button
                type="submit"
                class="btn btn-buscar"
            >
                Buscar
            </button>


            <a
                href="<?php
                    echo htmlspecialchars(
                        $empleadoConceptosUrlListado,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                class="btn btn-limpiar"
            >
                Limpiar
            </a>

        </form>

        <?php require __DIR__ . '/../../../componentes/paginacion.php'; ?>


        <!-- =====================================
             TABLA / TARJETAS
             ID y Observación se conservan internamente,
             pero no se muestran en el listado principal.
        ====================================== -->

        <?php

        /*
        |--------------------------------------------------------------------------
        | FECHA DE REFERENCIA PARA VIGENCIA
        |--------------------------------------------------------------------------
        |
        | Las fechas de empleado_concepto son inclusivas.
        | Una asignación cuya fecha_hasta es hoy continúa vigente durante el día.
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

        ?>

        <?php if (!empty($asignaciones)): ?>

            <div class="tabla-contenedor">

                <table>

                    <thead>

                        <tr>

                            <th>Empleado</th>
                            <th>Legajo</th>
                            <th>Concepto</th>
                            <th>Monto Manual</th>
                            <th>% Manual</th>
                            <th>Cantidad</th>
                            <th>Fecha Desde</th>
                            <th>Fecha Hasta</th>
                            <th>Estado</th>
                            <th>Acciones</th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php foreach ($asignaciones as $fila): ?>

                        <tr>


                            <!-- EMPLEADO -->

                            <td
                                class="col-empleado"
                                data-label="Empleado"
                            >

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


                            <!-- LEGAJO -->

                            <td
                                class="col-numero"
                                data-label="Legajo"
                            >

                                <?php
                                echo htmlspecialchars(
                                    $fila['nro_legajo'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </td>


                            <!-- CONCEPTO -->

                            <td
                                class="col-concepto"
                                data-label="Concepto"
                            >

                                <?php
                                echo htmlspecialchars(
                                    $fila['codigo']
                                    . ' - '
                                    . $fila['concepto'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </td>


                            <!-- MONTO -->

                            <td
                                class="col-numero"
                                data-label="Monto Manual"
                            >

                                $
                                <?php
                                echo number_format(
                                    (float)$fila['monto_manual'],
                                    2,
                                    ',',
                                    '.'
                                );
                                ?>

                            </td>


                            <!-- PORCENTAJE -->

                            <td
                                class="col-numero"
                                data-label="% Manual"
                            >

                                <?php
                                echo number_format(
                                    (float)$fila['porcentaje_manual'],
                                    2,
                                    ',',
                                    '.'
                                );
                                ?>%

                            </td>


                            <!-- CANTIDAD -->

                            <td
                                class="col-numero"
                                data-label="Cantidad"
                            >

                                <?php
                                echo number_format(
                                    (float)$fila['cantidad'],
                                    2,
                                    ',',
                                    '.'
                                );
                                ?>

                            </td>


                            <!-- FECHA DESDE -->

                            <td
                                class="col-fecha"
                                data-label="Fecha Desde"
                            >

                                <?php

                                if (!empty($fila['fecha_desde'])) {

                                    echo date(
                                        'd/m/Y',
                                        strtotime(
                                            $fila['fecha_desde']
                                        )
                                    );

                                } else {

                                    echo '-';
                                }

                                ?>

                            </td>


                            <!-- FECHA HASTA -->

                            <td
                                class="col-fecha"
                                data-label="Fecha Hasta"
                            >

                                <?php

                                if (!empty($fila['fecha_hasta'])) {

                                    echo date(
                                        'd/m/Y',
                                        strtotime(
                                            $fila['fecha_hasta']
                                        )
                                    );

                                } else {

                                    echo '-';
                                }

                                ?>

                            </td>


                            <!-- ESTADO / VIGENCIA -->

                            <td
                                class="col-estado"
                                data-label="Estado"
                            >

                                <?php

                                $registroActivo =
                                    (int)(
                                        $fila['activo']
                                        ?? 0
                                    ) === 1;

                                $fechaDesdeEstado =
                                    trim(
                                        (string)(
                                            $fila['fecha_desde']
                                            ?? ''
                                        )
                                    );

                                $fechaHastaEstado =
                                    trim(
                                        (string)(
                                            $fila['fecha_hasta']
                                            ?? ''
                                        )
                                    );


                                /*
                                |--------------------------------------------------------------------------
                                | ESTADO VISUAL
                                |--------------------------------------------------------------------------
                                |
                                | activo = 0                     -> Inactivo
                                | activo = 1 + fecha_hasta < hoy -> Finalizado por fecha
                                | activo = 1 + fecha_desde > hoy -> Programado
                                | resto                          -> Vigente
                                |
                                */

                                if (!$registroActivo) {

                                    $estadoTexto =
                                        'Inactivo';

                                    $estadoClase =
                                        'estado-inactivo';

                                } elseif (
                                    $fechaHastaEstado !== ''
                                    &&
                                    $fechaHastaEstado < $fechaHoy
                                ) {

                                    $estadoTexto =
                                        'Finalizado por fecha';

                                    $estadoClase =
                                        'estado-finalizado';

                                } elseif (
                                    $fechaDesdeEstado !== ''
                                    &&
                                    $fechaDesdeEstado > $fechaHoy
                                ) {

                                    $estadoTexto =
                                        'Programado';

                                    $estadoClase =
                                        'estado-programado';

                                } else {

                                    $estadoTexto =
                                        'Vigente';

                                    $estadoClase =
                                        'estado-vigente';
                                }

                                ?>

                                <span
                                    class="estado-badge <?php
                                        echo $estadoClase;
                                    ?>"
                                >
                                    <?php
                                    echo htmlspecialchars(
                                        $estadoTexto,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>
                                </span>

                            </td>


                            <!-- ACCIONES -->

                            <td
                                class="col-acciones"
                                data-label="Acciones"
                            >

                                <div class="acciones-tabla">


                                    <!-- VER -->

                                    <a
                                        href="<?php
                                            echo htmlspecialchars(
                                                sigenmuniUrlRuta('empleado-conceptos/ver', array_merge($contextoAsignaciones, [
                                                        'id' => (int)$fila['id']
                                                    ])),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                        ?>"
                                        class="btn-accion btn-ver"
                                    >
                                        Ver
                                    </a>


                                    <!-- EDITAR -->

                                    <?php if ($estadoTexto !== 'Finalizado por fecha'): ?>

                                        <a
                                            href="<?php
                                                echo htmlspecialchars(
                                                    sigenmuniUrlRuta('empleado-conceptos/editar', array_merge($contextoAsignaciones, [
                                                            'id' => (int)$fila['id']
                                                        ])),
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                );
                                            ?>"
                                            class="btn-accion btn-editar"
                                        >
                                            Editar
                                        </a>

                                    <?php endif; ?>


                                    <!--
                                    =========================================
                                    ACTIVAR / DESACTIVAR REGISTRO
                                    =========================================
                                    El botón modifica activo/inactivo del registro.
                                    La vigencia cronológica se muestra en Estado.
                                    -->

                                    <?php

                                    /*
                                    |--------------------------------------------------------------------------
                                    | ACTIVAR / DESACTIVAR REGISTRO
                                    |--------------------------------------------------------------------------
                                    |
                                    | Si la asignación está FINALIZADA POR FECHA se considera
                                    | histórica e inmutable desde la gestión común.
                                    |
                                    | En ese caso queda disponible solamente:
                                    |
                                    | - Ver
                                    |
                                    | Si el registro está inactivo técnicamente, se conserva
                                    | la opción "Activar registro"; el modelo volverá a validar
                                    | empleado, concepto, vigencia laboral y solapamientos.
                                    |
                                    */

                                    $esFinalizadoPorFecha =
                                        $estadoTexto === 'Finalizado por fecha';

                                    ?>


                                    <?php if (
                                        (int)$fila['activo'] === 1
                                        &&
                                        !$esFinalizadoPorFecha
                                    ): ?>

                                        <form
                                            method="POST"
                                            action="<?php
                                                echo htmlspecialchars(
                                                    $empleadoConceptosUrlEstado,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                );
                                            ?>"
                                            data-event-submit="empleado_conceptos-1"
                                        >

                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?php echo (int)$fila['id']; ?>"
                                            >

                                            <input
                                                type="hidden"
                                                name="estado"
                                                value="0"
                                            >

                                            <input
                                                type="hidden"
                                                name="_csrf"
                                                value="<?php
                                                    echo htmlspecialchars(
                                                        $empleadoConceptosCsrf,
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    );
                                                ?>"
                                            >

                                            <button
                                                type="submit"
                                                class="btn-accion btn-inactivar"
                                            >
                                                Desactivar registro
                                            </button>

                                        </form>

                                    <?php elseif ((int)$fila['activo'] === 0): ?>

                                        <form
                                            method="POST"
                                            action="<?php
                                                echo htmlspecialchars(
                                                    $empleadoConceptosUrlEstado,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                );
                                            ?>"
                                            data-event-submit="empleado_conceptos-2"
                                        >

                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?php echo (int)$fila['id']; ?>"
                                            >

                                            <input
                                                type="hidden"
                                                name="estado"
                                                value="1"
                                            >

                                            <input
                                                type="hidden"
                                                name="_csrf"
                                                value="<?php
                                                    echo htmlspecialchars(
                                                        $empleadoConceptosCsrf,
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    );
                                                ?>"
                                            >

                                            <button
                                                type="submit"
                                                class="btn-accion btn-activar"
                                            >
                                                Activar registro
                                            </button>

                                        </form>

                                    <?php endif; ?>


                                </div>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>
            <?php require __DIR__ . '/../../../componentes/paginacion.php'; ?>


        <?php else: ?>

            <div class="sin-resultados">
                No se encontraron asignaciones.
            </div>

        <?php endif; ?>


    </div>

</div>

<script src="<?= htmlspecialchars(sigenmuniUrlAsset('js/empleados/empleado_conceptos.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
</body>

</html>