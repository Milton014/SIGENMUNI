<?php

require_once __DIR__ . '/../../../core/Url.php';
require_once __DIR__ . '/../../../core/Csrf.php';


/*
|--------------------------------------------------------------------------
| NUEVA LIQUIDACIÓN - ROUTER
|--------------------------------------------------------------------------
*/

$liquidacionNuevaAccion =
    sigenmuniUrlRuta(
        'liquidacion/nueva'
    );


$liquidacionNuevaVolver =
    sigenmuniUrlRuta(
        'liquidacion'
    );


$liquidacionNuevaCsrf =
    sigenmuniCsrfToken();

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Nueva Liquidación - SIGENMUNI</title>

<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/liquidacion/liquidacion_nueva.css'), ENT_QUOTES, 'UTF-8') ?>">

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
        Gestión de Liquidaciones
    </p>

</div>


<!-- =========================================
     CONTENIDO
========================================= -->

<div class="contenedor">

    <div class="panel">


        <!-- =====================================
             TÍTULO
        ====================================== -->

        <h2>
            Nueva Liquidación
        </h2>

        <p class="subtitulo">
            Cree la cabecera de una nueva liquidación de haberes.
        </p>


        <!-- =====================================
             ESTADO INICIAL
        ====================================== -->

        <div class="estado-inicial">

            La nueva liquidación será creada con estado

            <strong>
                BORRADOR
            </strong>.

        </div>


        <!-- =====================================
             ALERTA JAVASCRIPT
        ====================================== -->

        <div
            id="alertaLiquidacion"
            class="mensaje mensaje-error"
            data-estilo="liquidacion_nueva-1"
        >
        </div>


        <!-- =====================================
             MENSAJE DEL CONTROLADOR
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
             FORMULARIO
        ====================================== -->

        <form
            action="<?php
                echo htmlspecialchars(
                    $liquidacionNuevaAccion,
                    ENT_QUOTES,
                    'UTF-8'
                );
            ?>"
            method="POST"
            id="formLiquidacion"
            novalidate
        >

            <input
                type="hidden"
                name="_csrf"
                value="<?php
                    echo htmlspecialchars(
                        $liquidacionNuevaCsrf,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
            >


            <!-- =================================
                 TIPO DE LIQUIDACIÓN
            ================================== -->

            <div class="grupo">

                <label for="tipo_liquidacion">

                    Tipo de Liquidación

                    <span class="campo-obligatorio">
                        *
                    </span>

                </label>


                <select
                    name="tipo_liquidacion"
                    id="tipo_liquidacion"
                >

                    <option value="">
                        Seleccione un tipo
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
                                ($datosLiquidacion['tipo_liquidacion'] ?? '')
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


                <div class="ayuda">
                    Seleccione el tipo de liquidación que desea generar.
                </div>

                <div
                    id="avisoAguinaldo"
                    class="aviso-tipo aviso-aguinaldo"
                >
                    <strong>Aguinaldo:</strong>
                    la liquidación normal de SAC solo puede crearse para los
                    períodos de <b>junio</b> o <b>diciembre</b>.
                    Si necesita liquidar SAC en otro mes, utilice
                    <b>Complementaria de SAC</b>.
                </div>


                <div
                    id="avisoComplementaria"
                    class="aviso-tipo aviso-complementaria"
                >
                    <strong>Complementaria de haberes:</strong>
                    después de crear la liquidación podrá ingresar a
                    <b>Personal y Novedades</b> para seleccionar únicamente
                    los empleados que deben participar, indicar de 1 a 30 días
                    liquidados y definir si corresponde Presentismo.
                </div>


                <div
                    id="avisoComplementariaSac"
                    class="aviso-tipo aviso-complementaria-sac"
                >
                    <strong>Complementaria de SAC:</strong>
                    después de crear la liquidación podrá ingresar a
                    <b>Personal y Días SAC</b>. El sistema mostrará por empleado
                    los días SAC devengados, ya pagados y pendientes, y evitará
                    superar el saldo disponible del semestre.
                </div>


                <div
                    id="avisoProtocolar"
                    class="aviso-tipo aviso-protocolar"
                >
                    <strong>Gastos Protocolares:</strong>
                    esta liquidación tendrá una carga específica por empleado.
                    Se podrá indicar el importe y si corresponde aplicar
                    Caja de Previsión 11% y aporte patronal 16%, o marcar
                    al empleado como Jubilado / Exento.
                </div>

            </div>


            <!-- =================================
                 PERÍODO / FECHA
            ================================== -->

            <div class="grid-doble">


                <!-- PERÍODO -->

                <div class="grupo">

                    <label for="periodo">

                        Período

                        <span class="campo-obligatorio">
                            *
                        </span>

                    </label>


                    <input
                        type="month"
                        name="periodo"
                        id="periodo"
                        value="<?php
                            echo htmlspecialchars(
                                $datosLiquidacion['periodo']
                                ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                    >


                    <div class="ayuda">
                        Mes y año correspondiente a la liquidación.
                    </div>

                </div>


                <!-- FECHA -->

                <div class="grupo">

                    <label for="fecha_liquidacion">

                        Fecha de Liquidación

                        <span class="campo-obligatorio">
                            *
                        </span>

                    </label>


                    <input
                        type="date"
                        name="fecha_liquidacion"
                        id="fecha_liquidacion"
                        value="<?php
                            echo htmlspecialchars(
                                $datosLiquidacion['fecha_liquidacion']
                                ?? date('Y-m-d'),
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                    >


                    <div
                        class="ayuda"
                        id="ayudaFechaLiquidacion"
                    >
                        Fecha en la que se genera la liquidación.
                    </div>

                </div>

            </div>


            <!-- =================================
                 DESCRIPCIÓN
            ================================== -->

            <div class="grupo">

                <label for="descripcion">
                    Descripción
                </label>


                <textarea
                    name="descripcion"
                    id="descripcion"
                    maxlength="500"
                    placeholder="Ingrese una descripción opcional"
                ><?php
                    echo htmlspecialchars(
                        $datosLiquidacion['descripcion']
                        ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?></textarea>


                <div class="ayuda">
                    Campo opcional. Máximo 500 caracteres.
                </div>

            </div>


            <!-- =================================
                 BOTONES
            ================================== -->

            <div class="acciones">

                <button
                    type="submit"
                    class="btn btn-guardar"
                >
                    Guardar Liquidación
                </button>


                <a
                    href="<?php
                        echo htmlspecialchars(
                            $liquidacionNuevaVolver,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                    class="btn btn-volver"
                >
                    Volver
                </a>

            </div>

        </form>

    </div>

</div>


<script src="<?= htmlspecialchars(sigenmuniUrlAsset('js/liquidacion/liquidacion_nueva.js'), ENT_QUOTES, 'UTF-8') ?>"></script>


</body>

</html>