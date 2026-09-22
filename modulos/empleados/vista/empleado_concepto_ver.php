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


<style>

*{
    box-sizing:border-box;
}


body{
    margin:0;
    font-family:Arial,sans-serif;
    background:#f4f7fb;
    color:#1f2937;
}


/* =========================================
   HEADER
========================================= */

.header{
    background:linear-gradient(
        135deg,
        #0f766e,
        #14b8a6
    );

    color:white;
    padding:22px 30px;
    box-shadow:0 4px 14px rgba(0,0,0,.10);
}


.header h1{
    margin:0;
    font-size:30px;
}


.header p{
    margin:6px 0 0;
    font-size:14px;
}


/* =========================================
   CONTENEDOR
========================================= */

.contenedor{
    width:94%;
    max-width:1000px;
    margin:30px auto;
}


.panel{
    background:white;
    padding:28px;
    border-radius:18px;
    box-shadow:0 8px 20px rgba(0,0,0,.08);
}


.topbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:15px;
    flex-wrap:wrap;
    margin-bottom:24px;
}


.titulo-seccion h2{
    margin:0;
    color:#0f766e;
    font-size:28px;
}


.titulo-seccion p{
    margin:6px 0 0;
    color:#64748b;
    font-size:14px;
}


/* =========================================
   DATOS
========================================= */

.grid{
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:14px;
}


.item{
    background:#f8fafc;
    border:1px solid #e5e7eb;
    border-radius:12px;
    padding:14px;
    min-height:72px;
}


.item-completo{
    grid-column:1/-1;
}


.titulo{
    font-size:12px;
    color:#64748b;
    text-transform:uppercase;
    font-weight:bold;
    letter-spacing:.4px;
    margin-bottom:6px;
}


.valor{
    font-size:15px;
    font-weight:bold;
    overflow-wrap:anywhere;
    line-height:1.45;
}


.valor-secundario{
    margin-top:4px;
    font-size:12px;
    color:#64748b;
    font-weight:normal;
}


.estado-badge{
    display:inline-block;
    padding:6px 10px;
    border-radius:999px;
    font-size:12px;
    font-weight:bold;
    line-height:1.2;
    white-space:nowrap;
}

.estado-vigente{
    background:#dcfce7;
    color:#166534;
    border:1px solid #86efac;
}

.estado-finalizado{
    background:#e2e8f0;
    color:#475569;
    border:1px solid #cbd5e1;
}

.estado-inactivo{
    background:#fee2e2;
    color:#991b1b;
    border:1px solid #fecaca;
}

.estado-programado{
    background:#dbeafe;
    color:#1d4ed8;
    border:1px solid #93c5fd;
}


.observacion{
    white-space:pre-wrap;
    font-weight:normal;
}


/* =========================================
   ACCIONES
========================================= */

.acciones{
    margin-top:25px;
    display:flex;
    gap:10px;
    flex-wrap:wrap;
}


.btn{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    padding:11px 16px;
    border-radius:10px;
    text-decoration:none;
    color:white;
    font-weight:bold;
    font-size:14px;
    transition:.2s;
}


.btn:hover{
    opacity:.92;
    transform:translateY(-1px);
}


.btn-editar{
    background:#d97706;
}


.btn-volver{
    background:#1f2937;
}


/* =========================================
   RESPONSIVE
========================================= */

@media(max-width:768px){

    .header{
        padding:20px;
        text-align:center;
    }


    .header h1{
        font-size:24px;
    }


    .contenedor{
        width:94%;
        margin:20px auto;
    }


    .panel{
        padding:20px;
    }


    .topbar{
        flex-direction:column;
        align-items:stretch;
        text-align:center;
    }


    .titulo-seccion h2{
        font-size:23px;
    }


    .grid{
        grid-template-columns:1fr;
    }


    .item-completo{
        grid-column:auto;
    }


    .acciones{
        flex-direction:column;
    }


    .btn{
        width:100%;
    }
}

</style>

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
