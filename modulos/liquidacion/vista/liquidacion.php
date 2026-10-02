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

<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/liquidacion/liquidacion.css'), ENT_QUOTES, 'UTF-8') ?>">

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
                                                    data-event-submit="liquidacion-1" data-event-value="<?php
                                                            echo htmlspecialchars(
                                                                $tipoFila,
                                                                ENT_QUOTES,
                                                                'UTF-8'
                                                            );
                                                        ?>"
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
                                                data-event-submit="liquidacion-2" data-event-value="<?php
                                                        echo htmlspecialchars(
                                                            $tipoFila,
                                                            ENT_QUOTES,
                                                            'UTF-8'
                                                        );
                                                    ?>"
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
                                            data-event-submit="liquidacion-3"
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
                                                data-event-submit="liquidacion-4"
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
                                            data-event-submit="liquidacion-5"
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



<script src="<?= htmlspecialchars(sigenmuniUrlAsset('js/liquidacion/liquidacion.js'), ENT_QUOTES, 'UTF-8') ?>"></script>

</body>

</html>