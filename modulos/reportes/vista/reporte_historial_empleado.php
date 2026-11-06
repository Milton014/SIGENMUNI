<?php

require_once __DIR__ . '/../../../core/Url.php';


/*
|--------------------------------------------------------------------------
| HISTORIAL POR EMPLEADO - ROUTER
|--------------------------------------------------------------------------
*/

$historialUrlReportes =
    sigenmuniUrlRuta(
        'reportes'
    );


$historialUrlMenu =
    sigenmuniUrlArchivo(
        'index.php'
    );


$historialUrlEntrada =
    sigenmuniUrlEntrada();


$historialUrlLimpiar =
    sigenmuniUrlRuta(
        'reportes/historial-empleado'
    );


$historialUrlPdf =
    sigenmuniUrlRuta(
        'reportes/historial-empleado/pdf',
        [
            'empleado_id' =>
                (int)(
                    $empleadoId
                    ?? 0
                )
        ]
    );


$historialUrlExcel =
    sigenmuniUrlRuta(
        'reportes/historial-empleado/excel',
        [
            'empleado_id' =>
                (int)(
                    $empleadoId
                    ?? 0
                )
        ]
    );


if (!function_exists('formatearCuitUnidadOrganizacion')) {

    function formatearCuitUnidadOrganizacion($cuit)
    {
        $cuit =
            preg_replace(
                '/\D+/',
                '',
                (string)$cuit
            );

        if (strlen($cuit) !== 11) {
            return (string)$cuit;
        }

        return
            substr($cuit, 0, 2)
            . '-'
            . substr($cuit, 2, 8)
            . '-'
            . substr($cuit, 10, 1);
    }
}

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Historial por Empleado - SIGENMUNI</title>

<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/reportes/reporte_historial_empleado.css'), ENT_QUOTES, 'UTF-8') ?>">

<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/compartido/paginacion.css'), ENT_QUOTES, 'UTF-8') ?>">
<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/reportes/paginacion.css'), ENT_QUOTES, 'UTF-8') ?>">
</head>

<body>

<div class="contenedor">

    <div class="cabecera">

        <div class="cabecera-top">

            <div>
                <h1>Historial por Empleado</h1>
                <p>
                    Consulta de liquidaciones históricas
                    y recibos individuales por agente.
                </p>
            </div>

            <div class="acciones-superiores">

                <a
                    href="<?php
                    echo htmlspecialchars(
                        $historialUrlReportes,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                    class="btn btn-reportes"
                >
                    Volver a Reportes
                </a>

                <a
                    href="<?php
                    echo htmlspecialchars(
                        $historialUrlMenu,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                    class="btn btn-volver"
                >
                    Menú Principal
                </a>

            </div>

        </div>

    </div>


    <?php if (!empty($error)): ?>

        <div class="error">
            <?php
            echo htmlspecialchars(
                $error,
                ENT_QUOTES,
                'UTF-8'
            );
            ?>
        </div>

    <?php endif; ?>


    <?php if ($empleadoId <= 0): ?>

        <div class="panel">

            <h2>Buscar empleado</h2>

            <form
                method="GET"
                action="<?php
                    echo htmlspecialchars(
                        $historialUrlEntrada,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                class="busqueda-form"
            >

                <input
                    type="hidden"
                    name="r"
                    value="reportes/historial-empleado"
                >

                <input
                    type="hidden"
                    name="filtrar"
                    value="1"
                >

                <div class="campo">

                    <label for="legajo">
                        Legajo
                    </label>

                    <input
                        type="text"
                        id="legajo"
                        name="legajo"
                        inputmode="numeric"
                        autocomplete="off"
                        placeholder="Ej.: 11"
                        value="<?php
                            echo htmlspecialchars(
                                $legajo,
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                        data-event-input="reporte_historial_empleado-1"
                    >

                    <div class="ayuda">
                        La búsqueda por legajo es exacta.
                    </div>

                </div>


                <div class="campo">

                    <label for="busqueda">
                        Búsqueda general
                    </label>

                    <input
                        type="text"
                        id="busqueda"
                        name="busqueda"
                        placeholder="Apellido, nombre, DNI o CUIL"
                        autocomplete="off"
                        value="<?php
                            echo htmlspecialchars(
                                $busqueda,
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                    >

                    <div class="ayuda">
                        Permite coincidencias parciales.
                    </div>

                </div>


                <div class="campo">

                    <label for="estado">
                        Estado
                    </label>

                    <select
                        name="estado"
                        id="estado"
                    >

                        <option value="">
                            Todos
                        </option>

                        <option
                            value="1"
                            <?php echo ($estado === '1') ? 'selected' : ''; ?>
                        >
                            Activos
                        </option>

                        <option
                            value="0"
                            <?php echo ($estado === '0') ? 'selected' : ''; ?>
                        >
                            Inactivos
                        </option>

                    </select>

                    <div class="ayuda">
                        Filtre por estado actual del empleado.
                    </div>

                </div>


                <button
                    type="submit"
                    class="btn btn-buscar"
                >
                    Buscar
                </button>


                <a
                    href="<?php
                        echo htmlspecialchars(
                            $historialUrlLimpiar,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                    class="btn btn-limpiar"
                >
                    Limpiar
                </a>

                <input type="hidden" name="por_pagina" value="<?= $paginacion['por_pagina'] ?>">
            </form>

        </div>


        <div class="panel lista-empleados">

            <h2>Resultados</h2>

            <?php if ($filtrar): ?>
            <?php
        $paginacionRuta = 'reportes/historial-empleado';
        $paginacionParametros = $contextoHistorial;
        $paginacionEntidad = 'empleados';
        $paginacionClavePagina = 'pagina'; $paginacionClaveCantidad = 'por_pagina';
        require __DIR__ . '/../../../componentes/paginacion_reporte.php';
        ?>


            <?php endif; ?>
            <?php if (!empty($empleados)): ?>

                <div class="tabla-contenedor">

                    <table>

                        <thead>
                            <tr>
                                <th>Legajo</th>
                                <th>Apellido y Nombre</th>
                                <th>DNI</th>
                                <th>CUIL</th>
                                <th>Categoría</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>

                        <tbody>

                        <?php foreach ($empleados as $fila): ?>

                            <tr>

                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        $fila['nro_legajo'] ?? '',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        ($fila['apellido'] ?? '')
                                        . ', '
                                        . ($fila['nombre'] ?? ''),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        $fila['dni'] ?? '',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        $fila['cuil'] ?? '',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        $fila['categoria'] ?? '-',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>
                                </td>

                                <td>

                                    <?php if (
                                        (int)($fila['activo'] ?? 0) === 1
                                    ): ?>

                                        <span class="estado-badge estado-cerrada">
                                            Activo
                                        </span>

                                    <?php else: ?>

                                        <span class="estado-badge estado-anulada">
                                            Inactivo
                                        </span>

                                    <?php endif; ?>

                                </td>

                                <td>

                                    <a
                                        href="<?php
                                            echo htmlspecialchars(
                                                sigenmuniUrlRuta('reportes/historial-empleado', array_merge($contextoHistorial, ['pagina_historial' => 1, 
                                                        'empleado_id' =>
                                                            (int)$fila['id']
                                                    ])),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                        ?>"
                                        class="btn-mini btn-ver"
                                    >
                                        Ver Historial
                                    </a>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>
                    <?php require __DIR__ . '/../../../componentes/paginacion.php'; ?>

            <?php else: ?>

                <div class="sin-registros">

                    <?php if (!($filtrar ?? false)): ?>

                        Ingresá algún filtro para localizar un empleado
                        o seleccioná <strong>Todos</strong> y presioná Buscar.

                    <?php else: ?>

                        No se encontraron empleados
                        con los filtros seleccionados.

                    <?php endif; ?>

                </div>

            <?php endif; ?>

        </div>


    <?php else: ?>


        <?php if ($empleado): ?>

            <div class="panel">

                <h2>Datos del Empleado</h2>

                <div class="datos-empleado">

                    <div class="info-box">
                        <strong>Empleado</strong>
                        <?php
                        echo htmlspecialchars(
                            ($empleado['apellido'] ?? '')
                            . ', '
                            . ($empleado['nombre'] ?? ''),
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>
                    </div>

                    <div class="info-box">
                        <strong>Legajo</strong>
                        <?php
                        echo htmlspecialchars(
                            $empleado['nro_legajo'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>
                    </div>

                    <div class="info-box">
                        <strong>DNI</strong>
                        <?php
                        echo htmlspecialchars(
                            $empleado['dni'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>
                    </div>

                    <div class="info-box">
                        <strong>CUIL</strong>
                        <?php
                        echo htmlspecialchars(
                            $empleado['cuil'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>
                    </div>

                    <div class="info-box">
                        <strong>Categoría</strong>
                        <?php
                        echo htmlspecialchars(
                            $empleado['categoria'] ?? '-',
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>
                    </div>

                    <div class="info-box">
                        <strong>Unidad de Organización</strong>

                        <?php
                        echo htmlspecialchars(
                            $empleado['oficina'] ?? '-',
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>

                        <?php if (!empty($empleado['oficina_cuit'])): ?>

                            <div
                                data-estilo="reporte_historial_empleado-1"
                            >
                                CUIT:
                                <?php
                                echo htmlspecialchars(
                                    formatearCuitUnidadOrganizacion(
                                        $empleado['oficina_cuit']
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>
                            </div>

                        <?php endif; ?>

                    </div>

                    <div class="info-box">
                        <strong>Situación</strong>
                        <?php
                        echo htmlspecialchars(
                            $empleado['situacion'] ?? '-',
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>
                    </div>

                    <div class="info-box">
                        <strong>Fecha Alta</strong>
                        <?php
                        echo !empty($empleado['fecha_alta'])
                            ? date(
                                "d/m/Y",
                                strtotime($empleado['fecha_alta'])
                            )
                            : '-';
                        ?>
                    </div>

                    <div class="info-box">
                        <strong>Fecha Inactivo</strong>
                        <?php
                        echo !empty($empleado['fecha_inactivo'])
                            ? date(
                                "d/m/Y",
                                strtotime($empleado['fecha_inactivo'])
                            )
                            : '-';
                        ?>
                    </div>

                    <div class="info-box">
                        <strong>Estado del Empleado</strong>

                        <?php if (
                            (int)($empleado['activo'] ?? 0) === 1
                        ): ?>

                            <span class="estado-badge estado-cerrada">
                                Activo
                            </span>

                        <?php else: ?>

                            <span class="estado-badge estado-anulada">
                                Inactivo
                            </span>

                        <?php endif; ?>

                    </div>

                </div>

            </div>


            <div class="panel">

                <h2>Historia laboral del empleado</h2>
                <p>Altas, bajas y reincorporaciones según los períodos laborales registrados.</p>
                <?php if (!empty($historialLaboral)): ?>
                    <ol class="historial-laboral-linea">
                        <?php foreach ($historialLaboral as $indicePeriodo => $periodoLaboral): ?>
                            <li class="historial-laboral-evento">
                                <strong><?= $indicePeriodo === 0 ? 'Alta registrada' : 'Reincorporación registrada' ?></strong>
                                <span class="historial-laboral-fecha">
                                    <?= !empty($periodoLaboral['fecha_desde']) ? htmlspecialchars(date('d/m/Y', strtotime($periodoLaboral['fecha_desde'])), ENT_QUOTES, 'UTF-8') : 'Fecha no disponible' ?>
                                </span>
                                <?php if (!empty($periodoLaboral['motivo_inicio'])): ?>
                                    <p>Motivo: <?= htmlspecialchars($periodoLaboral['motivo_inicio'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></p>
                                <?php endif; ?>
                                <?php if (!empty($periodoLaboral['observacion'])): ?>
                                    <p><?= htmlspecialchars($periodoLaboral['observacion'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></p>
                                <?php endif; ?>
                                <?php if (empty($periodoLaboral['fecha_hasta'])): ?>
                                    <p class="historial-laboral-abierto">Período abierto · Sin fecha de baja registrada</p>
                                <?php endif; ?>
                            </li>
                            <?php if (!empty($periodoLaboral['fecha_hasta'])): ?>
                                <li class="historial-laboral-evento historial-laboral-baja">
                                    <strong>Baja o inactivación registrada</strong>
                                    <span class="historial-laboral-fecha"><?= htmlspecialchars(date('d/m/Y', strtotime($periodoLaboral['fecha_hasta'])), ENT_QUOTES, 'UTF-8') ?></span>
                                    <p class="historial-norma-baja">Norma legal de baja:
                                        <?= htmlspecialchars(($periodoLaboral['norma_legal_baja'] ?? '') !== '' ? $periodoLaboral['norma_legal_baja'] : 'Norma legal no registrada', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
                                    </p>
                                    <?php if (!empty($periodoLaboral['motivo_fin'])): ?>
                                        <p>Motivo: <?= htmlspecialchars($periodoLaboral['motivo_fin'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></p>
                                    <?php endif; ?>
                                </li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ol>
                <?php else: ?>
                    <p class="sin-datos">No hay períodos laborales registrados para este empleado.</p>
                <?php endif; ?>
            </div>

            <div class="panel">
                <h2>
                    Historial de Liquidaciones
                </h2>

                <div class="acciones-exportar">
                    <a class="btn btn-volver" href="<?= htmlspecialchars(sigenmuniUrlRuta('reportes/historial-empleado', $contextoHistorial), ENT_QUOTES, 'UTF-8') ?>">Volver a la búsqueda</a>

                    <a
                        href="<?php
                            echo htmlspecialchars(
                                $historialUrlPdf,
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                        class="btn btn-pdf"
                    >
                        Exportar PDF
                    </a>

                    <a
                        href="<?php
                            echo htmlspecialchars(
                                $historialUrlExcel,
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                        class="btn btn-excel"
                    >
                        Exportar Excel
                    </a>

                </div>


                <?php $paginacion = $paginacionHistorial; ?>
                <?php
        $paginacionRuta = 'reportes/historial-empleado';
        $paginacionParametros = array_merge($contextoHistorial, ['empleado_id' => (int)$empleadoId]);
        $paginacionEntidad = 'liquidaciones';
        $paginacionClavePagina = 'pagina_historial'; $paginacionClaveCantidad = 'por_pagina_historial';
        require __DIR__ . '/../../../componentes/paginacion_reporte.php';
        ?>


                <?php if (!empty($historial)): ?>

                    <div class="tabla-contenedor">

                        <table>

                            <thead>

                                <tr>
                                    <th>ID</th>
                                    <th>Tipo</th>
                                    <th>Período</th>
                                    <th>Fecha Liquidación</th>
                                    <th>Estado</th>
                                    <th>Total Remunerativo</th>
                                    <th>Total Descuentos</th>
                                    <th>No Remunerativo</th>
                                    <th>Asignaciones</th>
                                    <th>Neto</th>
                                    <th>Recibo</th>
                                </tr>

                            </thead>

                            <tbody>

                            <?php foreach ($historial as $fila): ?>

                                <?php

                                $estadoLiquidacion =
                                    strtoupper(
                                        trim(
                                            $fila['estado'] ?? ''
                                        )
                                    );

                                switch ($estadoLiquidacion) {

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

                                    <td>
                                        <?php
                                        echo (int)(
                                            $fila['liquidacion_id'] ?? 0
                                        );
                                        ?>
                                    </td>

                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $fila['tipo_liquidacion'] ?? '',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        );
                                        ?>
                                    </td>

                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $fila['periodo'] ?? '',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        );
                                        ?>
                                    </td>

                                    <td>
                                        <?php
                                        echo !empty($fila['fecha_liquidacion'])
                                            ? date(
                                                "d/m/Y",
                                                strtotime(
                                                    $fila['fecha_liquidacion']
                                                )
                                            )
                                            : '-';
                                        ?>
                                    </td>

                                    <td>

                                        <span
                                            class="estado-badge <?php
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

                                    </td>

                                    <td>
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

                                    <td>
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

                                    <td>
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

                                    <td>
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

                                    <td>
                                        <strong>
                                            $
                                            <?php
                                            echo number_format(
                                                (float)(
                                                    $fila['neto']
                                                    ?? 0
                                                ),
                                                2,
                                                ',',
                                                '.'
                                            );
                                            ?>
                                        </strong>
                                    </td>

                                    <td>

                                        <?php if (
                                            $estadoLiquidacion === 'CERRADA'
                                        ): ?>

                                            <a
                                                href="<?php
                                                    echo htmlspecialchars(
                                                        sigenmuniUrlRuta('liquidacion/recibo', array_merge($contextoHistorial, [
                                                                'liquidacion_id' =>
                                                                    (int)$fila[
                                                                        'liquidacion_id'
                                                                    ],

                                                                'empleado_id' =>
                                                                    (int)$empleado['id'],

                                                                'origen' =>
                                                                    'historial'
                                                            ])),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    );
                                                ?>"
                                                class="btn-mini btn-recibo"
                                            >
                                                Ver Recibo
                                            </a>

                                        <?php else: ?>

                                            <span class="no-disponible">
                                                No disponible
                                            </span>

                                        <?php endif; ?>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>
                    <?php require __DIR__ . '/../../../componentes/paginacion.php'; ?>

                <?php else: ?>

                    <div class="sin-registros">
                        Este empleado todavía no tiene
                        liquidaciones registradas.
                    </div>

                <?php endif; ?>

            </div>


        <?php else: ?>

            <div class="panel">

                <div class="sin-registros">
                    No se encontró el empleado solicitado.
                </div>

            </div>

        <?php endif; ?>


    <?php endif; ?>

</div>

<script src="<?= htmlspecialchars(sigenmuniUrlAsset('js/reportes/reporte_historial_empleado.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
<script src="<?= htmlspecialchars(sigenmuniUrlAsset('js/reportes/paginacion.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
</body>

</html>
