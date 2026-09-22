<?php

require_once __DIR__ . '/../../../core/Url.php';
require_once __DIR__ . '/../../../core/Csrf.php';


/*
|--------------------------------------------------------------------------
| NAVEGACIÓN POR ROUTER
|--------------------------------------------------------------------------
|
| El listado ya funciona mediante:
|
| public/index.php?r=liquidacion
|
| Las acciones del módulo utilizan las rutas centralizadas del Router.
|
|--------------------------------------------------------------------------
*/

$liquidacionUrlListado =
    sigenmuniUrlRuta(
        'liquidacion'
    );


$liquidacionAccionFiltro =
    sigenmuniUrlEntrada();


$liquidacionUrlNueva =
    sigenmuniUrlRuta(
        'liquidacion/nueva'
    );


$liquidacionUrlMenu =
    sigenmuniUrlRuta(
    'inicio'
);


$liquidacionUrlProcesar =
    sigenmuniUrlRuta(
        'liquidacion/procesar'
    );


$liquidacionUrlEstado =
    sigenmuniUrlRuta(
        'liquidacion/estado'
    );


$liquidacionCsrf =
    sigenmuniCsrfToken();

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Liquidaciones - SIGENMUNI</title>

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
   CONTENEDOR
========================================= */

.contenedor{
    width:96%;
    max-width:1300px;
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
    align-items:center;
    gap:15px;
    flex-wrap:wrap;
    margin-bottom:20px;
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
    text-decoration:none;
    border-radius:9px;
    color:white;
    font-size:13px;
    border:none;
    cursor:pointer;
    font-weight:bold;
    transition:.2s;
    text-align:center;
}

.btn:hover{
    opacity:.90;
    transform:translateY(-1px);
}

.btn-nuevo{
    background:#0f766e;
}

.btn-volver{
    background:#1f2937;
}

.btn-filtrar{
    background:#2563eb;
}

.btn-limpiar{
    background:#64748b;
}

.btn-ver{
    background:#2563eb;
}

.btn-procesar{
    background:#16a34a;
}

.btn-protocolar{
    background:#7c3aed;
}

.btn-novedades{
    background:#0891b2;
}

.btn-novedades-sac{
    background:#7c3aed;
}

.btn-complementaria{
    background:#7c3aed;
}

.btn-complementaria-sac{
    background:#9333ea;
}

.btn-anular{
    background:#dc2626;
}

.btn-deshacer{
    background:#d97706;
}

.btn-disabled{
    background:#9ca3af;
    cursor:not-allowed;
    pointer-events:none;
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
   FILTROS
========================================= */

.filtros{
    background:#f8fafc;
    padding:16px;
    border-radius:14px;
    border:1px solid #e5e7eb;
    margin-bottom:24px;
}

.form-filtros{
    display:grid;
    grid-template-columns:
        1fr
        1fr
        1fr
        auto
        auto;

    gap:10px;
}

.form-filtros input,
.form-filtros select{
    width:100%;
    padding:11px 12px;
    border:1px solid #cbd5e1;
    border-radius:9px;
    font-size:13px;
    outline:none;
    background:white;
}

.form-filtros input:focus,
.form-filtros select:focus{
    border-color:#14b8a6;
    box-shadow:0 0 0 3px rgba(20,184,166,.15);
}


/* =========================================
   TABLA
========================================= */

.tabla-contenedor{
    width:100%;
}

table{
    width:100%;
    border-collapse:collapse;
}

th,
td{
    padding:11px 8px;
    border-bottom:1px solid #e5e7eb;
    font-size:13px;
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

thead th:first-child{
    border-radius:10px 0 0 0;
}

thead th:last-child{
    border-radius:0 10px 0 0;
}

tbody tr{
    transition:background .15s;
}

tbody tr:hover{
    background:#f8fafc;
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

.estado-borrador{
    background:#fef3c7;
    color:#92400e;
}

.estado-cerrada{
    background:#d1fae5;
    color:#166534;
}

.estado-anulada{
    background:#fee2e2;
    color:#991b1b;
}


/* =========================================
   ACCIONES TABLA
========================================= */

.acciones-tabla{
    display:flex;
    gap:6px;
    justify-content:center;
    flex-wrap:wrap;
}


.form-accion-inline{
    margin:0;
    padding:0;
}


/* =========================================
   SIN RESULTADOS
========================================= */

.sin-resultados{
    text-align:center;
    padding:25px;
    color:#64748b;
    font-weight:bold;
}


/* =========================================
   TABLET
========================================= */

@media (max-width:1000px){

    .form-filtros{
        grid-template-columns:1fr 1fr;
    }

    th,
    td{
        font-size:12px;
        padding:9px 6px;
    }
}


/* =========================================
   CELULAR
========================================= */

@media (max-width:768px){

    .contenedor{
        width:94%;
        margin:20px auto;
    }

    .panel{
        padding:18px;
        border-radius:16px;
    }

    .topbar{
        flex-direction:column;
        align-items:stretch;
    }

    .titulo-seccion h2{
        text-align:center;
        font-size:24px;
    }

    .titulo-seccion p{
        text-align:center;
    }

    .acciones-superiores{
        flex-direction:column;
    }

    .acciones-superiores .btn{
        width:100%;
    }

    .form-filtros{
        grid-template-columns:1fr;
    }

    .form-filtros .btn{
        width:100%;
    }


    /*
    |--------------------------------------------------------------------------
    | TABLA COMO TARJETAS
    |--------------------------------------------------------------------------
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
        gap:16px;
    }

    tbody tr{
        background:white;
        border:1px solid #e5e7eb;
        border-radius:14px;
        padding:14px;
        box-shadow:0 4px 12px rgba(0,0,0,.05);
    }

    tbody tr:hover{
        background:white;
    }

    td{
        display:grid;
        grid-template-columns:120px 1fr;
        gap:10px;
        padding:8px 0;
        border-bottom:1px solid #f1f5f9;
        text-align:left;
        font-size:13px;
    }

    td:last-child{
        border-bottom:none;
    }

    td::before{
        content:attr(data-label);
        font-weight:bold;
        color:#475569;
    }

    .acciones-tabla{
        flex-direction:column;
        width:100%;
    }

    .acciones-tabla .btn{
        width:100%;
    }
}


/* =========================================
   CELULAR PEQUEÑO
========================================= */

@media (max-width:480px){

    .titulo-seccion h2{
        font-size:22px;
    }

    td{
        grid-template-columns:105px 1fr;
        font-size:12px;
    }
}

</style>

</head>

<body>


<!-- =========================================
     HEADER GLOBAL
========================================= -->

<?php

$sigenmuniHeaderSubtitulo =
    'Gestión de Liquidaciones';

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
                    Liquidaciones
                </h2>

                <p>
                    Administración de períodos y procesos de liquidación.
                </p>

            </div>


            <div class="acciones-superiores">

                <a
                    href="<?php
                    echo htmlspecialchars(
                        $liquidacionUrlNueva,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                    class="btn btn-nuevo"
                >
                    + Nueva Liquidación
                </a>


                <a
                    href="<?php
                    echo htmlspecialchars(
                        $liquidacionUrlMenu,
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

        <div class="filtros">

            <form
                method="GET"
                action="<?php
                    echo htmlspecialchars(
                        $liquidacionAccionFiltro,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                class="form-filtros"
            >

                <input
                    type="hidden"
                    name="r"
                    value="liquidacion"
                >


                <!-- PERÍODO -->

                <input
                    type="month"
                    name="periodo"
                    value="<?php
                        echo htmlspecialchars(
                            $periodo ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                >


                <!-- TIPO -->

                <select name="tipo_liquidacion">

                    <option value="">
                        -- Todos los tipos --
                    </option>


                    <?php foreach ($tipos as $tipoItem): ?>

                        <option
                            value="<?php
                                echo htmlspecialchars(
                                    $tipoItem,
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                            ?>"
                            <?php
                            echo (
                                ($tipo ?? '')
                                ===
                                $tipoItem
                            )
                                ? 'selected'
                                : '';
                            ?>
                        >

                            <?php

                            switch ($tipoItem) {

                                case 'MENSUAL':

                                    echo 'Mensual';

                                    break;


                                case 'AGUINALDO':

                                    echo 'Aguinaldo';

                                    break;


                                case 'COMPLEMENTARIA':

                                    echo 'Complementaria';

                                    break;


                                case 'COMPLEMENTARIA_SAC':

                                    echo 'Complementaria de SAC';

                                    break;


                                case 'GASTOS_PROTOCOLARES':

                                    echo 'Gastos Protocolares';

                                    break;


                                default:

                                    echo htmlspecialchars(
                                        $tipoItem,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );

                                    break;
                            }

                            ?>

                        </option>

                    <?php endforeach; ?>

                </select>


                <!-- ESTADO -->

                <select name="estado">

                    <option value="">
                        -- Todos los estados --
                    </option>


                    <?php foreach ($estados as $estadoItem): ?>

                        <option
                            value="<?php
                                echo htmlspecialchars(
                                    $estadoItem,
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                            ?>"
                            <?php
                            echo (
                                ($estado ?? '')
                                ===
                                $estadoItem
                            )
                                ? 'selected'
                                : '';
                            ?>
                        >

                            <?php
                            echo ucfirst(
                                strtolower(
                                    $estadoItem
                                )
                            );
                            ?>

                        </option>

                    <?php endforeach; ?>

                </select>


                <!-- FILTRAR -->

                <button
                    type="submit"
                    class="btn btn-filtrar"
                >
                    Filtrar
                </button>


                <!-- LIMPIAR -->

                <a
                    href="<?php
                    echo htmlspecialchars(
                        $liquidacionUrlListado,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                    class="btn btn-limpiar"
                >
                    Limpiar
                </a>

            </form>

        </div>


        <!-- =====================================
             LISTADO
        ====================================== -->

        <?php if (!empty($liquidaciones)): ?>

            <div class="tabla-contenedor">

                <table>

                    <thead>

                        <tr>

                            <th>ID</th>
                            <th>Tipo</th>
                            <th>Período</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Acciones</th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php foreach ($liquidaciones as $fila): ?>


                        <?php

                        /*
                        |--------------------------------------------------------------------------
                        | ESTADO
                        |--------------------------------------------------------------------------
                        */

                        $estadoFila =
                            strtoupper(
                                trim(
                                    (string)$fila['estado']
                                )
                            );


                        $claseEstado =
                            'estado-borrador';


                        if (
                            $estadoFila
                            ===
                            'CERRADA'
                        ) {

                            $claseEstado =
                                'estado-cerrada';

                        } elseif (
                            $estadoFila
                            ===
                            'ANULADA'
                        ) {

                            $claseEstado =
                                'estado-anulada';
                        }

                        ?>


                        <tr>


                            <!-- ID -->

                            <td data-label="ID">

                                <?php
                                echo (int)$fila['id'];
                                ?>

                            </td>


                            <!-- TIPO -->

                            <td data-label="Tipo">

                                <?php

                                $tipoFila =
                                    strtoupper(
                                        trim(
                                            (string)$fila[
                                                'tipo_liquidacion'
                                            ]
                                        )
                                    );


                                switch ($tipoFila) {

                                    case 'MENSUAL':

                                        echo 'Mensual';

                                        break;


                                    case 'AGUINALDO':

                                        echo 'Aguinaldo';

                                        break;


                                    case 'COMPLEMENTARIA':

                                        echo 'Complementaria';

                                        break;


                                    case 'COMPLEMENTARIA_SAC':

                                        echo 'Complementaria de SAC';

                                        break;


                                    case 'GASTOS_PROTOCOLARES':

                                        echo 'Gastos Protocolares';

                                        break;


                                    default:

                                        echo htmlspecialchars(
                                            $tipoFila,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        );

                                        break;
                                }

                                ?>

                            </td>


                            <!-- PERÍODO -->

                            <td data-label="Período">

                                <?php
                                echo htmlspecialchars(
                                    $fila['periodo'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </td>


                            <!-- FECHA -->

                            <td data-label="Fecha">

                                <?php

                                if (
                                    !empty(
                                        $fila[
                                            'fecha_liquidacion'
                                        ]
                                    )
                                ) {

                                    echo date(
                                        'd/m/Y',
                                        strtotime(
                                            $fila[
                                                'fecha_liquidacion'
                                            ]
                                        )
                                    );

                                } else {

                                    echo '-';
                                }

                                ?>

                            </td>


                            <!-- ESTADO -->

                            <td data-label="Estado">

                                <span
                                    class="badge <?php
                                        echo $claseEstado;
                                    ?>"
                                >

                                    <?php
                                    echo htmlspecialchars(
                                        $estadoFila,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>

                                </span>

                            </td>


                            <!-- ACCIONES -->

                            <td data-label="Acciones">

                                <div class="acciones-tabla">


                                    <!-- =================================
                                         VER
                                    ================================== -->

                                    <a
                                        href="<?php
                                                echo htmlspecialchars(
                                                    sigenmuniUrlRuta(
                                                        'liquidacion/ver',
                                                        [
                                                            'id' => (int)$fila['id']
                                                        ]
                                                    ),
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                );
                                            ?>"
                                        class="btn btn-ver"
                                    >
                                        Ver
                                    </a>


                                    <!-- =================================
                                         BORRADOR
                                    ================================== -->

                                    <?php if (
                                        $estadoFila
                                        ===
                                        'BORRADOR'
                                    ): ?>


                                        <?php if (
                                            $tipoFila
                                            ===
                                            'GASTOS_PROTOCOLARES'
                                        ): ?>

                                            <!-- CARGAR GASTOS PROTOCOLARES -->

                                            <a
                                                href="<?php
                                                echo htmlspecialchars(
                                                    sigenmuniUrlRuta(
                                                        'liquidacion/protocolar',
                                                        [
                                                            'id' => (int)$fila['id']
                                                        ]
                                                    ),
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                );
                                            ?>"
                                                class="btn btn-protocolar"
                                            >
                                                Cargar Gastos Protocolares
                                            </a>


                                            <?php if (
                                                (int)(
                                                    $fila[
                                                        'cantidad_cargados_protocolar'
                                                    ]
                                                    ?? 0
                                                )
                                                >
                                                0
                                            ): ?>

                                                <!-- PROCESAR GASTOS PROTOCOLARES -->

                                                <form
                                                    action="<?php
                                                        echo htmlspecialchars(
                                                            $liquidacionUrlProcesar,
                                                            ENT_QUOTES,
                                                            'UTF-8'
                                                        );
                                                    ?>"
                                                    method="POST"
                                                    class="form-accion-inline"
                                                    onsubmit="return confirmarProcesamiento(
                                                        '<?php
                                                            echo htmlspecialchars(
                                                                $tipoFila,
                                                                ENT_QUOTES,
                                                                'UTF-8'
                                                            );
                                                        ?>'
                                                    );"
                                                >

                                                    <input
                                                        type="hidden"
                                                        name="id"
                                                        value="<?php
                                                            echo (int)$fila['id'];
                                                        ?>"
                                                    >

                                                    <input
                                                        type="hidden"
                                                        name="_csrf"
                                                        value="<?php
                                                            echo htmlspecialchars(
                                                                $liquidacionCsrf,
                                                                ENT_QUOTES,
                                                                'UTF-8'
                                                            );
                                                        ?>"
                                                    >

                                                    <button
                                                        type="submit"
                                                        class="btn btn-procesar"
                                                    >
                                                        Procesar
                                                    </button>

                                                </form>

                                            <?php endif; ?>

                                        <?php else: ?>


                                            <?php if (
                                                $tipoFila === 'MENSUAL'
                                            ): ?>

                                                <!-- NOVEDADES MENSUALES -->

                                                <a
                                                    href="<?php
                                                echo htmlspecialchars(
                                                    sigenmuniUrlRuta(
                                                        'liquidacion/novedades',
                                                        [
                                                            'id' => (int)$fila['id']
                                                        ]
                                                    ),
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                );
                                            ?>"
                                                    class="btn btn-novedades"
                                                >
                                                    Novedades
                                                </a>


                                            <?php elseif (
                                                $tipoFila === 'COMPLEMENTARIA'
                                            ): ?>

                                                <!-- PERSONAL Y NOVEDADES COMPLEMENTARIA -->

                                                <a
                                                    href="<?php
                                                echo htmlspecialchars(
                                                    sigenmuniUrlRuta(
                                                        'liquidacion/complementaria',
                                                        [
                                                            'id' => (int)$fila['id']
                                                        ]
                                                    ),
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                );
                                            ?>"
                                                    class="btn btn-complementaria"
                                                >
                                                    Personal y Novedades
                                                </a>


                                            <?php elseif (
                                                $tipoFila === 'AGUINALDO'
                                            ): ?>

                                                <!-- NOVEDADES SAC -->

                                                <a
                                                    href="<?php
                                                echo htmlspecialchars(
                                                    sigenmuniUrlRuta(
                                                        'liquidacion/novedades-sac',
                                                        [
                                                            'id' => (int)$fila['id']
                                                        ]
                                                    ),
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                );
                                            ?>"
                                                    class="btn btn-novedades-sac"
                                                >
                                                    Novedades SAC
                                                </a>


                                            <?php elseif (
                                                $tipoFila === 'COMPLEMENTARIA_SAC'
                                            ): ?>

                                                <!-- PERSONAL Y DÍAS SAC -->

                                                <a
                                                    href="<?php
                                                echo htmlspecialchars(
                                                    sigenmuniUrlRuta(
                                                        'liquidacion/complementaria-sac',
                                                        [
                                                            'id' => (int)$fila['id']
                                                        ]
                                                    ),
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                );
                                            ?>"
                                                    class="btn btn-complementaria-sac"
                                                >
                                                    Personal y Días SAC
                                                </a>

                                            <?php endif; ?>


                                            <!-- PROCESAR LIQUIDACIÓN -->

                                            <form
                                                action="<?php
                                                    echo htmlspecialchars(
                                                        $liquidacionUrlProcesar,
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    );
                                                ?>"
                                                method="POST"
                                                class="form-accion-inline"
                                                onsubmit="return confirmarProcesamiento(
                                                    '<?php
                                                        echo htmlspecialchars(
                                                            $tipoFila,
                                                            ENT_QUOTES,
                                                            'UTF-8'
                                                        );
                                                    ?>'
                                                );"
                                            >

                                                <input
                                                    type="hidden"
                                                    name="id"
                                                    value="<?php
                                                        echo (int)$fila['id'];
                                                    ?>"
                                                >

                                                <input
                                                    type="hidden"
                                                    name="_csrf"
                                                    value="<?php
                                                        echo htmlspecialchars(
                                                            $liquidacionCsrf,
                                                            ENT_QUOTES,
                                                            'UTF-8'
                                                        );
                                                    ?>"
                                                >

                                                <button
                                                    type="submit"
                                                    class="btn btn-procesar"
                                                >
                                                    Procesar
                                                </button>

                                            </form>

                                        <?php endif; ?>


                                        <!-- ANULAR -->

                                        <form
                                            action="<?php
                                                echo htmlspecialchars(
                                                    $liquidacionUrlEstado,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                );
                                            ?>"
                                            method="POST"
                                            class="form-accion-inline"
                                            onsubmit="return confirm(
                                                '¿Seguro que desea anular esta liquidación? La liquidación quedará registrada como ANULADA.'
                                            );"
                                        >

                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?php
                                                    echo (int)$fila['id'];
                                                ?>"
                                            >

                                            <input
                                                type="hidden"
                                                name="accion"
                                                value="anular"
                                            >

                                            <input
                                                type="hidden"
                                                name="_csrf"
                                                value="<?php
                                                    echo htmlspecialchars(
                                                        $liquidacionCsrf,
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    );
                                                ?>"
                                            >

                                            <button
                                                type="submit"
                                                class="btn btn-anular"
                                            >
                                                Anular
                                            </button>

                                        </form>


                                    <!-- =================================
                                         CERRADA
                                    ================================== -->

                                    <?php elseif (
                                        $estadoFila
                                        ===
                                        'CERRADA'
                                    ): ?>


                                        <!-- =================================
                                             DESHACER PROCESAMIENTO
                                             SOLO ADMIN
                                        ================================== -->

                                        <?php if (!empty($esAdminUsuario)): ?>

                                            <form
                                                action="<?php
                                                    echo htmlspecialchars(
                                                        $liquidacionUrlEstado,
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    );
                                                ?>"
                                                method="POST"
                                                class="form-accion-inline"
                                                onsubmit="return confirm(
                                                    '¿Seguro que desea DESHACER el procesamiento de esta liquidación? Se eliminarán los detalles y resúmenes generados, y la liquidación volverá a estado BORRADOR. Esta acción no elimina la cabecera de la liquidación.'
                                                );"
                                            >

                                                <input
                                                    type="hidden"
                                                    name="id"
                                                    value="<?php
                                                        echo (int)$fila['id'];
                                                    ?>"
                                                >

                                                <input
                                                    type="hidden"
                                                    name="accion"
                                                    value="deshacer"
                                                >

                                                <input
                                                    type="hidden"
                                                    name="_csrf"
                                                    value="<?php
                                                        echo htmlspecialchars(
                                                            $liquidacionCsrf,
                                                            ENT_QUOTES,
                                                            'UTF-8'
                                                        );
                                                    ?>"
                                                >

                                                <button
                                                    type="submit"
                                                    class="btn btn-deshacer"
                                                >
                                                    ↶ Deshacer procesamiento
                                                </button>

                                            </form>

                                        <?php endif; ?>


                                        <!-- =================================
                                             ANULAR
                                        ================================== -->

                                        <form
                                            action="<?php
                                                echo htmlspecialchars(
                                                    $liquidacionUrlEstado,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                );
                                            ?>"
                                            method="POST"
                                            class="form-accion-inline"
                                            onsubmit="return confirm(
                                                '¿Seguro que desea anular esta liquidación cerrada? Los datos quedarán conservados como historial.'
                                            );"
                                        >

                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?php
                                                    echo (int)$fila['id'];
                                                ?>"
                                            >

                                            <input
                                                type="hidden"
                                                name="accion"
                                                value="anular"
                                            >

                                            <input
                                                type="hidden"
                                                name="_csrf"
                                                value="<?php
                                                    echo htmlspecialchars(
                                                        $liquidacionCsrf,
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    );
                                                ?>"
                                            >

                                            <button
                                                type="submit"
                                                class="btn btn-anular"
                                            >
                                                Anular
                                            </button>

                                        </form>


                                    <!-- =================================
                                         ANULADA
                                    ================================== -->

                                    <?php elseif (
                                        $estadoFila
                                        ===
                                        'ANULADA'
                                    ): ?>


                                        <!--
                                        |----------------------------------------------------------
                                        | SOLO LECTURA
                                        |----------------------------------------------------------
                                        |
                                        | Ya tiene disponible el botón VER.
                                        | No se muestran otras acciones.
                                        |
                                        -->


                                    <?php endif; ?>


                                </div>

                            </td>


                        </tr>


                    <?php endforeach; ?>


                    </tbody>

                </table>

            </div>


        <?php else: ?>


            <div class="sin-resultados">

                No se encontraron liquidaciones.

            </div>


        <?php endif; ?>


    </div>

</div>



<script>

function confirmarProcesamiento(tipoLiquidacion){

    tipoLiquidacion =
        String(
            tipoLiquidacion || ''
        ).toUpperCase();


    if(tipoLiquidacion === 'AGUINALDO'){

        return confirm(
            '¿Seguro que desea procesar esta liquidación de AGUINALDO? '
            + 'Verifique antes las Novedades SAC, los días devengados, ya pagados y pendientes de cada empleado. '
            + 'Al finalizar quedará CERRADA. Solo un administrador podrá deshacer el procesamiento si fuera necesario.'
        );
    }


    if(tipoLiquidacion === 'COMPLEMENTARIA_SAC'){

        return confirm(
            '¿Seguro que desea procesar esta COMPLEMENTARIA DE SAC? '
            + 'Verifique antes el personal seleccionado y los días SAC pendientes que se liquidarán ahora. '
            + 'Solo se procesarán los empleados seleccionados. '
            + 'Al finalizar quedará CERRADA. Solo un administrador podrá deshacer el procesamiento si fuera necesario.'
        );
    }


    if(tipoLiquidacion === 'COMPLEMENTARIA'){

        return confirm(
            '¿Seguro que desea procesar esta liquidación COMPLEMENTARIA? '
            + 'Verifique antes el personal seleccionado, los días liquidados y el presentismo. '
            + 'Solo se procesarán los empleados seleccionados. '
            + 'Al finalizar quedará CERRADA. Solo un administrador podrá deshacer el procesamiento si fuera necesario.'
        );
    }


    if(tipoLiquidacion === 'MENSUAL'){

        return confirm(
            '¿Seguro que desea procesar esta liquidación? '
            + 'Verifique antes las novedades de días liquidados y presentismo. '
            + 'Al finalizar quedará CERRADA. Solo un administrador podrá deshacer el procesamiento si fuera necesario.'
        );
    }


    return confirm(
        '¿Seguro que desea procesar esta liquidación? '
        + 'Al finalizar quedará CERRADA. Solo un administrador podrá deshacer el procesamiento si fuera necesario.'
    );
}

</script>

</body>

</html>