<?php

require_once __DIR__ . '/../../../core/Url.php';


/*
|--------------------------------------------------------------------------
| VER ASIGNACIÓN - FRONT CONTROLLER + ROUTER
|--------------------------------------------------------------------------
*/

$empleadoConceptoVerVolver =
    sigenmuniUrlRuta(
        'empleado-conceptos'
    );


$empleadoConceptoVerEditar =
    sigenmuniUrlRuta(
        'empleado-conceptos/editar',
        [
            'id' => (int)(
                $asignacion['id']
                ?? 0
            )
        ]
    );

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>
    Ver Concepto por Empleado - SIGENMUNI
</title>


<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/empleados/empleado_concepto_ver.css'), ENT_QUOTES, 'UTF-8') ?>">

</head>


<body>


<div class="header">

    <h1>
        SIGENMUNI
    </h1>

    <p>
        Gestión de Conceptos por Empleado
    </p>

</div>


<?php

/*
|--------------------------------------------------------------------------
| ESTADO / VIGENCIA DE LA ASIGNACIÓN
|--------------------------------------------------------------------------
|
| Se distingue el estado técnico del registro (activo/inactivo) de la
| vigencia cronológica definida por fecha_desde y fecha_hasta.
|
| Las fechas son inclusivas:
| una asignación cuya fecha_hasta es hoy continúa vigente durante hoy.
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


$registroActivo =
    (int)(
        $asignacion['activo']
        ?? 0
    ) === 1;


$fechaDesdeEstado =
    trim(
        (string)(
            $asignacion['fecha_desde']
            ?? ''
        )
    );


$fechaHastaEstado =
    trim(
        (string)(
            $asignacion['fecha_hasta']
            ?? ''
        )
    );


if (!$registroActivo) {

    $estadoTexto =
        'Inactivo';

    $estadoClase =
        'estado-inactivo';

    $estadoDetalle =
        'El registro fue desactivado manualmente.';

} elseif (
    $fechaHastaEstado !== ''
    &&
    $fechaHastaEstado < $fechaHoy
) {

    $estadoTexto =
        'Finalizado por fecha';

    $estadoClase =
        'estado-finalizado';

    $estadoDetalle =
        'El registro permanece activo históricamente, pero su vigencia ya finalizó.';

} elseif (
    $fechaDesdeEstado !== ''
    &&
    $fechaDesdeEstado > $fechaHoy
) {

    $estadoTexto =
        'Programado';

    $estadoClase =
        'estado-programado';

    $estadoDetalle =
        'La asignación comenzará a estar vigente en la fecha desde indicada.';

} else {

    $estadoTexto =
        'Vigente';

    $estadoClase =
        'estado-vigente';

    $estadoDetalle =
        'La asignación se encuentra vigente en la fecha actual.';
}

?>

<div class="contenedor">

    <div class="panel">


        <div class="topbar">

            <div class="titulo-seccion">

                <h2>
                    Detalle de la Asignación
                </h2>

                <p>
                    Información completa del concepto asignado al empleado.
                </p>

            </div>

        </div>


        <div class="grid">


            <!-- EMPLEADO -->

            <div class="item">

                <div class="titulo">
                    Empleado
                </div>

                <div class="valor">

                    <?php
                    echo htmlspecialchars(
                        ($asignacion['empleado_apellido'] ?? '')
                        . ', '
                        . ($asignacion['empleado_nombre'] ?? ''),
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>

                </div>

            </div>


            <!-- LEGAJO -->

            <div class="item">

                <div class="titulo">
                    Legajo
                </div>

                <div class="valor">

                    <?php
                    echo htmlspecialchars(
                        $asignacion['empleado_legajo']
                        ?? '-',
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>

                </div>

            </div>


            <!-- CONCEPTO -->

            <div class="item item-completo">

                <div class="titulo">
                    Concepto
                </div>

                <div class="valor">

                    <?php
                    echo htmlspecialchars(
                        ($asignacion['concepto_codigo'] ?? '')
                        . ' - '
                        . ($asignacion['concepto_nombre'] ?? ''),
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>

                </div>


                <div class="valor-secundario">

                    Tipo de Concepto:
                    <?php
                    echo htmlspecialchars(
                        $asignacion['concepto_categoria']
                        ?? '-',
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>

                    &nbsp;|&nbsp;

                    Forma de cálculo:
                    <?php
                    echo htmlspecialchars(
                        $asignacion['forma_calculo']
                        ?? '-',
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>

                </div>

            </div>


            <!-- MONTO MANUAL -->

            <div class="item">

                <div class="titulo">
                    Monto Manual
                </div>

                <div class="valor">

                    $
                    <?php
                    echo number_format(
                        (float)(
                            $asignacion['monto_manual']
                            ?? 0
                        ),
                        2,
                        ',',
                        '.'
                    );
                    ?>

                </div>

            </div>


            <!-- PORCENTAJE -->

            <div class="item">

                <div class="titulo">
                    % Manual
                </div>

                <div class="valor">

                    <?php
                    echo number_format(
                        (float)(
                            $asignacion['porcentaje_manual']
                            ?? 0
                        ),
                        2,
                        ',',
                        '.'
                    );
                    ?>%

                </div>

            </div>


            <!-- CANTIDAD -->

            <div class="item">

                <div class="titulo">
                    Cantidad
                </div>

                <div class="valor">

                    <?php
                    echo number_format(
                        (float)(
                            $asignacion['cantidad']
                            ?? 0
                        ),
                        2,
                        ',',
                        '.'
                    );
                    ?>

                </div>

            </div>


            <!-- ESTADO / VIGENCIA -->

            <div class="item">

                <div class="titulo">
                    Estado / Vigencia
                </div>

                <div class="valor">

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

                    <div class="valor-secundario">

                        <?php
                        echo htmlspecialchars(
                            $estadoDetalle,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>

                    </div>

                </div>

            </div>


            <!-- FECHA DESDE -->

            <div class="item">

                <div class="titulo">
                    Fecha Desde
                </div>

                <div class="valor">

                    <?php
                    echo !empty(
                        $asignacion['fecha_desde']
                    )
                        ?
                        date(
                            'd/m/Y',
                            strtotime(
                                $asignacion['fecha_desde']
                            )
                        )
                        :
                        '-';
                    ?>

                </div>

            </div>


            <!-- FECHA HASTA -->

            <div class="item">

                <div class="titulo">
                    Fecha Hasta
                </div>

                <div class="valor">

                    <?php
                    echo !empty(
                        $asignacion['fecha_hasta']
                    )
                        ?
                        date(
                            'd/m/Y',
                            strtotime(
                                $asignacion['fecha_hasta']
                            )
                        )
                        :
                        '-';
                    ?>

                </div>

            </div>


            <!-- OBSERVACIÓN -->

            <div class="item item-completo">

                <div class="titulo">
                    Observación
                </div>

                <div class="valor observacion"><?php
                    echo htmlspecialchars(
                        !empty(
                            $asignacion['observacion']
                        )
                            ?
                            $asignacion['observacion']
                            :
                            'Sin observaciones.',
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?></div>

            </div>


        </div>


        <div class="acciones">

            <?php if ($estadoTexto !== 'Finalizado por fecha'): ?>

                <a
                    href="<?php
                        echo htmlspecialchars(
                            $empleadoConceptoVerEditar,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                    class="btn btn-editar"
                >
                    Editar
                </a>

            <?php endif; ?>


            <a
                href="<?php
                    echo htmlspecialchars(
                        $empleadoConceptoVerVolver,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                class="btn btn-volver"
            >
                Volver
            </a>

        </div>


    </div>

</div>


</body>

</html>
