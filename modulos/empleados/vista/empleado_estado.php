<?php

require_once __DIR__ . '/../../../core/Url.php';
require_once __DIR__ . '/../../../core/Csrf.php';


/*
|--------------------------------------------------------------------------
| ESTADO DE EMPLEADO - SOLO ROUTER
|--------------------------------------------------------------------------
*/

$empleadoEstadoId =
    (int)(
        $empleado['id']
        ?? 0
    );


$empleadoEstadoAccion =
    sigenmuniUrlRuta(
        'empleados/estado',
        [
            'id' => $empleadoEstadoId,
            'accion' => (string)(
                $accion
                ?? ''
            )
        ]
    );


$empleadoEstadoCancelar =
    sigenmuniUrlRuta(
        'empleados'
    );


$empleadoEstadoCsrf =
    sigenmuniCsrfToken();

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>
    <?php echo htmlspecialchars($tituloAccion ?? 'Cambiar Estado', ENT_QUOTES, 'UTF-8'); ?>
    - SIGENMUNI
</title>

<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/empleados/empleado_estado.css'), ENT_QUOTES, 'UTF-8') ?>">

</head>

<body>

<div class="header">
    <h1>SIGENMUNI</h1>
    <p>Gestión del estado e historial laboral del empleado</p>
</div>

<div class="contenedor">

    <div class="panel">

        <h2>
            <?php
            echo htmlspecialchars(
                $tituloAccion ?? 'Cambiar Estado',
                ENT_QUOTES,
                'UTF-8'
            );
            ?>
        </h2>

        <p class="subtitulo">
            Ingrese la fecha efectiva del cambio. El sistema actualizará el estado
            actual y el período correspondiente del historial laboral.
        </p>


        <?php if (!empty($mensaje)): ?>

            <div class="mensaje mensaje-error">
                <?php
                echo htmlspecialchars(
                    $mensaje,
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>
            </div>

        <?php endif; ?>


        <div class="resumen">

            <div class="dato">
                <strong>Empleado</strong>
                <span>
                    <?php
                    echo htmlspecialchars(
                        trim(
                            ($empleado['apellido'] ?? '')
                            . ', '
                            . ($empleado['nombre'] ?? '')
                        ),
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>
                </span>
            </div>

            <div class="dato">
                <strong>Legajo</strong>
                <span>
                    <?php
                    echo htmlspecialchars(
                        $empleado['nro_legajo'] ?? '-',
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>
                </span>
            </div>

            <div class="dato">
                <strong>Estado actual</strong>
                <span
                    class="<?php
                    echo (int)($empleado['activo'] ?? 0) === 1
                        ? 'estado-activo'
                        : 'estado-inactivo';
                    ?>"
                >
                    <?php
                    echo (int)($empleado['activo'] ?? 0) === 1
                        ? 'Activo'
                        : 'Inactivo';
                    ?>
                </span>
            </div>

            <div class="dato">
                <strong>Períodos registrados</strong>
                <span>
                    <?php echo count($periodosLaborales ?? []); ?>
                </span>
            </div>

        </div>


        <?php if (($accion ?? '') === 'inactivar'): ?>

            <div class="aviso aviso-inactivar">
                <strong>Inactivación:</strong>
                se cerrará el período laboral actualmente abierto con la fecha
                efectiva que indique.
            </div>

        <?php else: ?>

            <div class="aviso aviso-activar">
                <strong>Reincorporación:</strong>
                se abrirá un nuevo período laboral desde la fecha efectiva
                indicada y el empleado volverá a quedar activo.
            </div>

        <?php endif; ?>


        <form
            method="POST"
            action="<?php
                echo htmlspecialchars(
                    $empleadoEstadoAccion,
                    ENT_QUOTES,
                    'UTF-8'
                );
            ?>"
        >

            <input
                type="hidden"
                name="_csrf"
                value="<?php
                    echo htmlspecialchars(
                        $empleadoEstadoCsrf,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
            >

            <div class="campo">

                <label for="fecha_efectiva">
                    <?php
                    echo ($accion ?? '') === 'inactivar'
                        ? 'Fecha efectiva de inactivación *'
                        : 'Fecha efectiva de reincorporación *';
                    ?>
                </label>

                <input
                    type="date"
                    id="fecha_efectiva"
                    name="fecha_efectiva"
                    value="<?php
                    echo htmlspecialchars(
                        $fechaEfectiva ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>"
                    max="<?php echo htmlspecialchars($fechaHoy ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                    required
                >

                <div class="ayuda">

                    <?php if (($accion ?? '') === 'inactivar' && !empty($periodoActual['fecha_desde'])): ?>

                        El período laboral actual comenzó el
                        <strong>
                            <?php
                            echo date(
                                'd/m/Y',
                                strtotime(
                                    $periodoActual['fecha_desde']
                                )
                            );
                            ?>
                        </strong>.

                    <?php elseif (($accion ?? '') === 'activar' && !empty($periodosLaborales)): ?>

                        La reincorporación debe ser posterior al último período
                        laboral finalizado.

                    <?php else: ?>

                        Puede utilizar la fecha actual o una fecha anterior válida.

                    <?php endif; ?>

                </div>

            </div>


            <div class="campo">

                <label for="observacion">
                    Motivo / Observación
                </label>

                <textarea
                    id="observacion"
                    name="observacion"
                    maxlength="255"
                    placeholder="<?php
                    echo ($accion ?? '') === 'inactivar'
                        ? 'Ej.: Renuncia voluntaria, finalización de contrato, licencia sin goce...'
                        : 'Ej.: Reincorporación al personal...';
                    ?>"
                ><?php echo htmlspecialchars($observacion ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>

                <div class="ayuda">
                    Opcional. Máximo 255 caracteres.
                </div>

            </div>


            <?php if (!empty($periodosLaborales)): ?>

                <div class="historial-resumen">

                    <h3>Últimos períodos laborales</h3>

                    <div class="tabla-contenedor">

                        <table class="tabla">

                            <thead>
                                <tr>
                                    <th>Desde</th>
                                    <th>Hasta</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>

                            <tbody>

                            <?php
                            $ultimosPeriodos =
                                array_slice(
                                    $periodosLaborales,
                                    -3
                                );
                            ?>

                            <?php foreach ($ultimosPeriodos as $periodo): ?>

                                <tr>

                                    <td>
                                        <?php
                                        echo !empty($periodo['fecha_desde'])
                                            ? date(
                                                'd/m/Y',
                                                strtotime(
                                                    $periodo['fecha_desde']
                                                )
                                            )
                                            : '-';
                                        ?>
                                    </td>

                                    <td>
                                        <?php
                                        echo !empty($periodo['fecha_hasta'])
                                            ? date(
                                                'd/m/Y',
                                                strtotime(
                                                    $periodo['fecha_hasta']
                                                )
                                            )
                                            : 'Actual';
                                        ?>
                                    </td>

                                    <td>

                                        <?php if (empty($periodo['fecha_hasta'])): ?>

                                            <span class="badge badge-actual">
                                                Actual
                                            </span>

                                        <?php else: ?>

                                            <span class="badge badge-finalizado">
                                                Finalizado
                                            </span>

                                        <?php endif; ?>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>

                </div>

            <?php endif; ?>


            <div class="acciones">

                <a
                    href="<?php
                        echo htmlspecialchars(
                            $empleadoEstadoCancelar,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                    class="btn btn-cancelar"
                >
                    Cancelar
                </a>

                <button
                    type="submit"
                    class="btn <?php
                    echo ($accion ?? '') === 'inactivar'
                        ? 'btn-inactivar'
                        : 'btn-activar';
                    ?>"
                >
                    <?php
                    echo ($accion ?? '') === 'inactivar'
                        ? 'Confirmar Inactivación'
                        : 'Confirmar Reincorporación';
                    ?>
                </button>

            </div>

        </form>

    </div>

</div>

</body>

</html>
