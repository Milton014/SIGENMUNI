<?php

require_once __DIR__ . '/../../../core/Url.php';
require_once __DIR__ . '/../../../core/Csrf.php';


/*
|--------------------------------------------------------------------------
| NOVEDADES SAC - ROUTER
|--------------------------------------------------------------------------
*/

$liquidacionNovedadesSacId =
    (int)(
        $liquidacion['id']
        ?? 0
    );


$liquidacionNovedadesSacAccion =
    sigenmuniUrlRuta(
        'liquidacion/novedades-sac',
        [
            'id' =>
                $liquidacionNovedadesSacId
        ]
    );


$liquidacionNovedadesSacVolver =
    sigenmuniUrlRuta(
        'liquidacion'
    );


$liquidacionNovedadesSacCsrf =
    sigenmuniCsrfToken();

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Novedades SAC - SIGENMUNI</title>

<style>

/* =========================================
   GENERAL
========================================= */

*{
    box-sizing:border-box;
}

body{
    font-family:Arial,sans-serif;
    background:#f4f7fb;
    margin:0;
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
    width:96%;
    max-width:1450px;
    margin:30px auto;
}

.panel{
    background:white;
    padding:24px;
    border-radius:18px;
    box-shadow:0 8px 20px rgba(0,0,0,.08);
}


/* =========================================
   TOPBAR
========================================= */

.topbar{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    gap:18px;
    flex-wrap:wrap;
    margin-bottom:22px;
}

.titulo h2{
    margin:0;
    color:#0f766e;
    font-size:28px;
}

.titulo p{
    margin:6px 0 0;
    color:#64748b;
    font-size:14px;
    line-height:1.5;
}

.acciones-superiores{
    display:flex;
    gap:8px;
    flex-wrap:wrap;
}


/* =========================================
   BOTONES
========================================= */

.btn{
    display:inline-block;
    padding:10px 14px;
    border:none;
    border-radius:9px;
    text-decoration:none;
    color:white;
    cursor:pointer;
    font-size:13px;
    font-weight:bold;
    transition:.2s;
    text-align:center;
}

.btn:hover{
    opacity:.90;
    transform:translateY(-1px);
}

.btn-guardar{
    background:#0f766e;
}

.btn-volver{
    background:#1f2937;
}

.btn-180{
    background:#2563eb;
}

.btn-0{
    background:#dc2626;
}


/* =========================================
   MENSAJES
========================================= */

.mensaje{
    padding:14px 16px;
    border-radius:10px;
    margin-bottom:20px;
    font-size:14px;
    font-weight:bold;
}

.mensaje-ok{
    background:#dcfce7;
    color:#166534;
    border:1px solid #86efac;
}

.mensaje-error{
    background:#fee2e2;
    color:#991b1b;
    border:1px solid #fecaca;
}


/* =========================================
   RESUMEN DE LIQUIDACIÓN
========================================= */

.resumen-liquidacion{
    display:grid;
    grid-template-columns:
        repeat(5, minmax(140px,1fr));

    gap:12px;
    margin-bottom:20px;
}

.dato{
    background:#f8fafc;
    border:1px solid #e5e7eb;
    border-radius:12px;
    padding:12px 14px;
}

.dato strong{
    display:block;
    margin-bottom:4px;
    color:#475569;
    font-size:12px;
}

.dato span{
    font-size:14px;
    font-weight:bold;
    color:#111827;
}


/* =========================================
   AVISO / FÓRMULA
========================================= */

.aviso{
    background:#eff6ff;
    border:1px solid #bfdbfe;
    color:#1e3a8a;
    padding:15px 17px;
    border-radius:12px;
    margin-bottom:20px;
    font-size:13px;
    line-height:1.55;
}

.aviso strong{
    display:block;
    margin-bottom:5px;
}

.formula{
    margin-top:8px;
    padding:10px 12px;
    background:white;
    border:1px solid #dbeafe;
    border-radius:9px;
    color:#1e40af;
    font-weight:bold;
}


/* =========================================
   HERRAMIENTAS
========================================= */

.herramientas{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:12px;
    flex-wrap:wrap;
    margin-bottom:18px;
}

.busqueda{
    flex:1;
    min-width:260px;
}

.busqueda input{
    width:100%;
    padding:11px 12px;
    border:1px solid #cbd5e1;
    border-radius:9px;
    font-size:13px;
    outline:none;
}

.busqueda input:focus{
    border-color:#14b8a6;
    box-shadow:0 0 0 3px rgba(20,184,166,.15);
}

.acciones-rapidas{
    display:flex;
    gap:8px;
    flex-wrap:wrap;
}


/* =========================================
   CONTADORES
========================================= */

.resumen-sac{
    display:flex;
    gap:18px;
    flex-wrap:wrap;
    margin-bottom:18px;
    font-size:13px;
    color:#475569;
}

.resumen-sac strong{
    color:#0f766e;
}


/* =========================================
   TABLA
========================================= */

.tabla-contenedor{
    width:100%;
    overflow-x:visible;
}

table{
    width:100%;
    border-collapse:collapse;
    table-layout:auto;
}

th,
td{
    padding:10px 6px;
    border-bottom:1px solid #e5e7eb;
    font-size:12px;
    vertical-align:middle;
}

th{
    background:#0f766e;
    color:white;
    text-align:center;
    white-space:nowrap;
}

td{
    text-align:center;
}

tbody tr:hover{
    background:#f8fafc;
}

.col-empleado{
    text-align:left;
    min-width:175px;
}

.col-fecha{
    width:82px;
    white-space:nowrap;
}

.col-dias{
    width:105px;
}

.col-tipo-sac{
    min-width:105px;
}


/* =========================================
   INPUT DÍAS
========================================= */

.input-dias-sac{
    width:90px;
    padding:8px 7px;
    border:1px solid #cbd5e1;
    border-radius:8px;
    text-align:center;
    font-size:13px;
}

.input-dias-sac:focus{
    outline:none;
    border-color:#14b8a6;
    box-shadow:0 0 0 3px rgba(20,184,166,.12);
}


/* =========================================
   BADGES
========================================= */

.badge{
    display:inline-block;
    padding:6px 10px;
    border-radius:999px;
    font-size:11px;
    font-weight:bold;
    white-space:nowrap;
}

.badge-completo{
    background:#dcfce7;
    color:#166534;
}

.badge-proporcional{
    background:#fef3c7;
    color:#92400e;
}

.badge-no-liquida{
    background:#fee2e2;
    color:#991b1b;
}

.badge-pendiente{
    background:#dbeafe;
    color:#1d4ed8;
}

.badge-parcial{
    background:#fef3c7;
    color:#92400e;
}

.badge-liquidado{
    background:#dcfce7;
    color:#166534;
}

.badge-activo{
    background:#dbeafe;
    color:#1d4ed8;
}

.badge-inactivo{
    background:#f1f5f9;
    color:#475569;
}


/* =========================================
   FILAS PROPORCIONALES
========================================= */

.fila-proporcional{
    background:#fffbeb;
}

.fila-no-liquida{
    background:#fef2f2;
}


/* =========================================
   ACCIONES INFERIORES
========================================= */

.acciones-inferiores{
    display:flex;
    justify-content:flex-end;
    gap:10px;
    flex-wrap:wrap;
    margin-top:22px;
}


/* =========================================
   SIN RESULTADOS
========================================= */

.sin-resultados{
    padding:28px;
    text-align:center;
    color:#64748b;
    font-weight:bold;
}


/* =========================================
   RESPONSIVE
========================================= */

@media (max-width:1100px){

    .resumen-liquidacion{
        grid-template-columns:1fr 1fr;
    }

    /*
    |--------------------------------------------------------------------------
    | TABLA COMO TARJETAS
    |--------------------------------------------------------------------------
    |
    | Evita la barra de desplazamiento horizontal en pantallas más angostas.
    |
    */

    table,
    thead,
    tbody,
    tr,
    th,
    td{
        display:block;
        width:100%;
    }

    thead{
        display:none;
    }

    tbody{
        display:grid;
        gap:14px;
    }

    tbody tr{
        border:1px solid #e5e7eb;
        border-radius:14px;
        padding:12px 14px;
        background:white;
        box-shadow:0 4px 12px rgba(0,0,0,.05);
    }

    tbody tr:hover{
        background:white;
    }

    tbody tr.fila-proporcional{
        background:#fffbeb;
    }

    tbody tr.fila-no-liquida{
        background:#fef2f2;
    }

    td{
        display:grid;
        grid-template-columns:145px minmax(0,1fr);
        gap:12px;
        align-items:center;
        text-align:left;
        padding:8px 0;
        border-bottom:1px solid #f1f5f9;
    }

    td:last-child{
        border-bottom:none;
    }

    td::before{
        content:attr(data-label);
        font-weight:bold;
        color:#475569;
    }

    .col-empleado,
    .col-fecha,
    .col-dias,
    .col-tipo-sac{
        min-width:0;
        width:100%;
    }

    .input-dias-sac{
        width:110px;
    }
}

@media (max-width:768px){

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
        padding:18px;
    }

    .topbar{
        flex-direction:column;
        align-items:stretch;
    }

    .titulo h2{
        font-size:24px;
        text-align:center;
    }

    .titulo p{
        text-align:center;
    }

    .acciones-superiores{
        flex-direction:column;
    }

    .acciones-superiores .btn{
        width:100%;
    }

    .resumen-liquidacion{
        grid-template-columns:1fr;
    }

    .herramientas{
        flex-direction:column;
        align-items:stretch;
    }

    .busqueda{
        min-width:0;
    }

    .acciones-rapidas{
        flex-direction:column;
    }

    .acciones-rapidas .btn{
        width:100%;
    }

    .acciones-inferiores{
        flex-direction:column;
    }

    .acciones-inferiores .btn{
        width:100%;
    }

    td{
        grid-template-columns:125px minmax(0,1fr);
        font-size:12px;
    }
}

@media (max-width:480px){

    td{
        grid-template-columns:110px minmax(0,1fr);
        gap:8px;
    }

    .input-dias-sac{
        width:100px;
    }
}

</style>

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
        Novedades de Sueldo Anual Complementario
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
                    Novedades de SAC
                </h2>

                <p>
                    Controle los días SAC devengados, ya pagados y pendientes de cada empleado.
                </p>

            </div>


            <div class="acciones-superiores">

                <a
                    href="<?php
                        echo htmlspecialchars(
                            $liquidacionNovedadesSacVolver,
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


        <?php

        $periodoLiquidacion =
            (string)(
                $liquidacion['periodo']
                ?? ''
            );

        $anio =
            0;

        $mes =
            0;

        if (
            preg_match(
                '/^(\d{4})-(\d{2})$/',
                $periodoLiquidacion,
                $coincidencias
            )
        ) {

            $anio =
                (int)$coincidencias[1];

            $mes =
                (int)$coincidencias[2];
        }


        if (
            $anio > 0
            &&
            $mes >= 1
            &&
            $mes <= 6
        ) {

            $semestreTexto =
                '1.er semestre';

            $semestreDesde =
                '01/01/' . $anio;

            $semestreHasta =
                '30/06/' . $anio;

        } elseif (
            $anio > 0
            &&
            $mes >= 7
            &&
            $mes <= 12
        ) {

            $semestreTexto =
                '2.º semestre';

            $semestreDesde =
                '01/07/' . $anio;

            $semestreHasta =
                '31/12/' . $anio;

        } else {

            $semestreTexto =
                '-';

            $semestreDesde =
                '-';

            $semestreHasta =
                '-';
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


        <!-- =====================================
             RESUMEN
        ====================================== -->

        <?php if (!empty($liquidacion)): ?>

            <div class="resumen-liquidacion">

                <div class="dato">

                    <strong>Liquidación</strong>

                    <span>
                        #<?php echo (int)$liquidacion['id']; ?>
                    </span>

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

                    <strong>Rango</strong>

                    <span>
                        <?php
                        echo htmlspecialchars(
                            $semestreDesde
                            . ' al '
                            . $semestreHasta,
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
                Regla de cálculo del SAC
            </strong>

            El sistema calcula automáticamente los días SAC <b>devengados</b>,
            descuenta los días <b>ya pagados</b> en liquidaciones de SAC cerradas
            del mismo semestre y propone como máximo los días <b>pendientes</b>.

            <div class="formula">
                Días pendientes = Días devengados - Días ya pagados
                <br>
                SAC = Base Remunerativa × 50% × (Días a liquidar / 180)
            </div>

        </div>


        <?php if (!empty($empleadosSac)): ?>


            <!-- =====================================
                 HERRAMIENTAS
            ====================================== -->

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
                        class="btn btn-180"
                        onclick="establecerTodosDias(180);"
                    >
                        Todos 180 días
                    </button>

                    <button
                        type="button"
                        class="btn btn-0"
                        onclick="establecerTodosDias(0);"
                    >
                        Todos 0 días
                    </button>

                </div>

            </div>


            <div class="resumen-sac">

                <span>
                    Empleados disponibles:
                    <strong>
                        <?php echo count($empleadosSac); ?>
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

                <span>
                    SAC proporcional / sin SAC:
                    <strong id="contadorProporcionales">
                        <?php echo (int)($cantidadSacProporcional ?? 0); ?>
                    </strong>
                </span>

            </div>


            <!-- =====================================
                 FORMULARIO
            ====================================== -->

            <form
                method="POST"
                action="<?php
                    echo htmlspecialchars(
                        $liquidacionNovedadesSacAccion,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                id="formSac"
            >

                <input
                    type="hidden"
                    name="_csrf"
                    value="<?php
                        echo htmlspecialchars(
                            $liquidacionNovedadesSacCsrf,
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
                                <th>DNI</th>
                                <th>Alta</th>
                                <th>Fecha Inactivo</th>
                                <th>Situación</th>
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

                            $estadoSac =
                                strtoupper(
                                    trim(
                                        (string)(
                                            $empleado['estado_sac']
                                            ?? 'PENDIENTE'
                                        )
                                    )
                                );

                            $estaActivo =
                                (int)(
                                    $empleado['activo']
                                    ?? 0
                                ) === 1;

                            $fechaAltaVisual =
                                !empty(
                                    $empleado['fecha_alta']
                                )
                                &&
                                $empleado['fecha_alta'] !== '0000-00-00'
                                    ?
                                    date(
                                        'd/m/Y',
                                        strtotime(
                                            $empleado['fecha_alta']
                                        )
                                    )
                                    :
                                    '-';

                            $fechaInactivoVisual =
                                !empty(
                                    $empleado['fecha_inactivo']
                                )
                                &&
                                $empleado['fecha_inactivo'] !== '0000-00-00'
                                    ?
                                    date(
                                        'd/m/Y',
                                        strtotime(
                                            $empleado['fecha_inactivo']
                                        )
                                    )
                                    :
                                    '-';

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
                                        . ' '
                                        . (string)(
                                            $empleado['dni']
                                            ?? ''
                                        )
                                    )
                                );


                            if ($estadoSac === 'YA_LIQUIDADO') {

                                $claseFila =
                                    'fila-no-liquida';

                                $claseBadgeSac =
                                    'badge-liquidado';

                                $textoBadgeSac =
                                    'Ya liquidado';

                            } elseif ($estadoSac === 'PARCIAL') {

                                $claseFila =
                                    'fila-proporcional';

                                $claseBadgeSac =
                                    'badge-parcial';

                                $textoBadgeSac =
                                    'Parcial';

                            } elseif ($diasPendientes <= 0) {

                                $claseFila =
                                    'fila-no-liquida';

                                $claseBadgeSac =
                                    'badge-liquidado';

                                $textoBadgeSac =
                                    'Ya liquidado';

                            } elseif ($diasSac === 180) {

                                $claseFila =
                                    '';

                                $claseBadgeSac =
                                    'badge-completo';

                                $textoBadgeSac =
                                    'SAC completo';

                            } else {

                                $claseFila =
                                    'fila-proporcional';

                                $claseBadgeSac =
                                    'badge-pendiente';

                                $textoBadgeSac =
                                    'Pendiente';
                            }

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

                                <!-- LEGAJO -->

                                <td data-label="Legajo">

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


                                <!-- DNI -->

                                <td data-label="DNI">

                                    <?php
                                    echo htmlspecialchars(
                                        $empleado['dni']
                                        ?? '-',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>

                                </td>


                                <!-- ALTA -->

                                <td
                                    class="col-fecha"
                                    data-label="Alta"
                                >

                                    <?php
                                    echo htmlspecialchars(
                                        $fechaAltaVisual,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>

                                </td>


                                <!-- FECHA INACTIVO -->

                                <td
                                    class="col-fecha"
                                    data-label="Fecha Inactivo"
                                >

                                    <?php
                                    echo htmlspecialchars(
                                        $fechaInactivoVisual,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>

                                </td>


                                <!-- SITUACIÓN -->

                                <td data-label="Situación">

                                    <span
                                        class="badge <?php
                                            echo $estaActivo
                                                ? 'badge-activo'
                                                : 'badge-inactivo';
                                        ?>"
                                    >
                                        <?php
                                            echo $estaActivo
                                                ? 'Activo'
                                                : 'Inactivo';
                                        ?>
                                    </span>

                                </td>


                                <!-- DEVENGADOS -->

                                <td data-label="Devengados">
                                    <strong><?php echo $diasDevengados; ?></strong>
                                </td>


                                <!-- YA PAGADOS -->

                                <td data-label="Ya pagados">
                                    <?php echo $diasPagados; ?>
                                </td>


                                <!-- PENDIENTES -->

                                <td data-label="Pendientes">
                                    <strong><?php echo $diasPendientes; ?></strong>
                                </td>


                                <!-- DÍAS A LIQUIDAR -->

                                <td
                                    class="col-dias"
                                    data-label="Días a liquidar"
                                >

                                    <input
                                        type="number"
                                        class="input-dias-sac"
                                        name="empleados[<?php echo $empleadoId; ?>][dias_sac]"
                                        value="<?php echo $diasSac; ?>"
                                        min="0"
                                        max="<?php echo max(0, $diasPendientes); ?>"
                                        step="1"
                                        required
                                        <?php echo $diasPendientes <= 0 ? 'disabled' : ''; ?>
                                        oninput="actualizarFilaSac(this);"
                                        onchange="actualizarFilaSac(this);"
                                    >

                                    <?php if ($diasPendientes <= 0): ?>
                                        <input
                                            type="hidden"
                                            name="empleados[<?php echo $empleadoId; ?>][dias_sac]"
                                            value="0"
                                        >
                                    <?php endif; ?>

                                </td>


                                <!-- ESTADO SAC -->

                                <td
                                    class="col-tipo-sac"
                                    data-label="Estado SAC"
                                >

                                    <span
                                        class="badge badge-sac <?php
                                            echo $claseBadgeSac;
                                        ?>"
                                    >
                                        <?php
                                        echo htmlspecialchars(
                                            $textoBadgeSac,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        );
                                        ?>
                                    </span>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>


                <!-- =================================
                     ACCIONES INFERIORES
                ================================== -->

                <div class="acciones-inferiores">

                    <a
                        href="<?php
                            echo htmlspecialchars(
                                $liquidacionNovedadesSacVolver,
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
                        Guardar Novedades SAC
                    </button>

                </div>

            </form>


        <?php else: ?>

            <div class="sin-resultados">
                No hay empleados disponibles para liquidar SAC en el semestre seleccionado.
            </div>

        <?php endif; ?>


    </div>

</div>


<script>

/*
|--------------------------------------------------------------------------
| BUSCADOR
|--------------------------------------------------------------------------
*/

const buscador =
    document.getElementById(
        "buscarEmpleado"
    );

if(buscador){

    buscador.addEventListener(
        "input",
        function(){

            const texto =
                this.value
                    .trim()
                    .toLowerCase();

            document
                .querySelectorAll(
                    "#cuerpoEmpleados tr"
                )
                .forEach(function(fila){

                    const contenido =
                        (
                            fila.dataset.busqueda
                            || ""
                        ).toLowerCase();

                    fila.style.display =
                        contenido.includes(texto)
                            ? ""
                            : "none";
                });
        }
    );
}


/*
|--------------------------------------------------------------------------
| ESTABLECER DÍAS EN TODOS
|--------------------------------------------------------------------------
*/

function establecerTodosDias(dias){

    document
        .querySelectorAll(
            ".input-dias-sac"
        )
        .forEach(function(input){

            if(input.disabled){
                return;
            }

            const fila =
                input.closest("tr");

            const pendientes =
                fila
                    ? parseInt(
                        fila.dataset.pendientes || "0",
                        10
                    )
                    : 0;

            let valor =
                dias;

            if(valor > pendientes){
                valor = pendientes;
            }

            if(valor < 0){
                valor = 0;
            }

            input.value =
                valor;

            actualizarFilaSac(
                input
            );
        });


    actualizarContador();
}


/*
|--------------------------------------------------------------------------
| ACTUALIZAR ESTADO VISUAL
|--------------------------------------------------------------------------
*/

function actualizarFilaSac(input){

    const fila =
        input.closest(
            "tr"
        );

    if(!fila){
        return;
    }


    let dias =
        parseInt(
            input.value,
            10
        );


    if(isNaN(dias)){
        dias = 0;
    }


    if(dias < 0){
        dias = 0;
    }

    if(dias > 180){
        dias = 180;
    }


    const badge =
        fila.querySelector(
            ".badge-sac"
        );


    fila.classList.remove(
        "fila-proporcional",
        "fila-no-liquida"
    );


    if(badge){

        badge.classList.remove(
            "badge-completo",
            "badge-proporcional",
            "badge-no-liquida",
            "badge-pendiente",
            "badge-parcial",
            "badge-liquidado"
        );
    }


    const pendientes =
        parseInt(
            fila.dataset.pendientes || "0",
            10
        );


    if(pendientes <= 0){

        fila.classList.add(
            "fila-no-liquida"
        );

        if(badge){

            badge.textContent =
                "Ya liquidado";

            badge.classList.add(
                "badge-liquidado"
            );
        }

    }else if(dias === 180){

        if(badge){

            badge.textContent =
                "SAC completo";

            badge.classList.add(
                "badge-completo"
            );
        }

    }else if(dias === 0){

        fila.classList.add(
            "fila-no-liquida"
        );

        if(badge){

            badge.textContent =
                "No liquida ahora";

            badge.classList.add(
                "badge-no-liquida"
            );
        }

    }else if(dias < pendientes){

        fila.classList.add(
            "fila-proporcional"
        );

        if(badge){

            badge.textContent =
                "Parcial";

            badge.classList.add(
                "badge-parcial"
            );
        }

    }else{

        fila.classList.add(
            "fila-proporcional"
        );

        if(badge){

            badge.textContent =
                "Pendiente";

            badge.classList.add(
                "badge-pendiente"
            );
        }
    }


    actualizarContador();
}


/*
|--------------------------------------------------------------------------
| CONTADOR
|--------------------------------------------------------------------------
*/

function actualizarContador(){

    const contador =
        document.getElementById(
            "contadorProporcionales"
        );

    if(!contador){
        return;
    }


    let total =
        0;


    document
        .querySelectorAll(
            ".input-dias-sac"
        )
        .forEach(function(input){

            if(input.disabled){
                return;
            }

            const dias =
                parseInt(
                    input.value,
                    10
                );

            const fila =
                input.closest("tr");

            const pendientes =
                fila
                    ? parseInt(
                        fila.dataset.pendientes || "0",
                        10
                    )
                    : 0;


            if(
                !isNaN(dias)
                &&
                (
                    dias === 0
                    ||
                    dias < pendientes
                    ||
                    pendientes < 180
                )
            ){

                total++;
            }
        });


    contador.textContent =
        total;
}


/*
|--------------------------------------------------------------------------
| VALIDACIÓN
|--------------------------------------------------------------------------
*/

const formulario =
    document.getElementById(
        "formSac"
    );

if(formulario){

    formulario.addEventListener(
        "submit",
        function(e){

            const inputs =
                document.querySelectorAll(
                    ".input-dias-sac"
                );


            for(
                const input
                of
                inputs
            ){

                if(input.disabled){
                    continue;
                }

                const texto =
                    input.value.trim();

                const valor =
                    Number(texto);

                const fila =
                    input.closest("tr");

                const pendientes =
                    fila
                        ? parseInt(
                            fila.dataset.pendientes || "0",
                            10
                        )
                        : 0;


                if(
                    texto === ""
                    ||
                    !Number.isInteger(valor)
                    ||
                    valor < 0
                    ||
                    valor > pendientes
                ){

                    e.preventDefault();

                    alert(
                        "Los días de SAC deben ser un número entero entre 0 y los días pendientes del empleado."
                    );

                    input.focus();

                    return;
                }
            }
        }
    );
}


/*
|--------------------------------------------------------------------------
| INICIALIZACIÓN
|--------------------------------------------------------------------------
*/

document.addEventListener(
    "DOMContentLoaded",
    function(){

        document
            .querySelectorAll(
                ".input-dias-sac"
            )
            .forEach(function(input){

                actualizarFilaSac(
                    input
                );
            });


        actualizarContador();
    }
);

</script>

</body>

</html>
