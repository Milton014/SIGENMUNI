<?php

require_once __DIR__ . '/../../../core/Url.php';
require_once __DIR__ . '/../../../core/Csrf.php';


$origen =
    trim(
        $_GET['origen']
        ?? ''
    );


$modoIntegrado =
    (
        $origen === 'concepto'
        &&
        !empty(
            $datosValor['concepto_id']
        )
    );


$conceptoFijo =
    null;


if ($modoIntegrado) {

    foreach (
        $conceptosDisponibles
        as $conceptoDisponible
    ) {

        if (
            (int)$conceptoDisponible['id']
            ===
            (int)$datosValor['concepto_id']
        ) {

            $conceptoFijo =
                $conceptoDisponible;

            break;
        }
    }


    if ($conceptoFijo === null) {

        $modoIntegrado =
            false;
    }
}


$formaActual =
    $formasConcepto[
        (int)(
            $datosValor['concepto_id']
            ?? 0
        )
    ]
    ?? '';


/*
|--------------------------------------------------------------------------
| URLS Y CSRF - SOLO ROUTER
|--------------------------------------------------------------------------
*/

$valorEditarId =
    (int)(
        $datosValor['id']
        ?? 0
    );


$retornoValores =
    (
        isset($retornoValores)
        &&
        in_array(
            $retornoValores,
            ['todos', 'filtrado'],
            true
        )
    )
        ? $retornoValores
        : (
            !empty($conceptoIdContextoValores)
                ? 'filtrado'
                : 'todos'
        );


$valorEditarParametros = [
    'id' => $valorEditarId
];

if ($modoIntegrado) {

    $valorEditarParametros['origen'] =
        'concepto';

} else {

    $valorEditarParametros['retorno'] =
        $retornoValores;

    if (
        $retornoValores === 'filtrado'
        &&
        !empty($conceptoIdContextoValores)
    ) {
        $valorEditarParametros['concepto_id'] =
            (int)$conceptoIdContextoValores;
    }
}


$valorEditarAccion =
    sigenmuniUrlRuta(
        'conceptos/valores/editar',
        $valorEditarParametros
    );


if ($modoIntegrado) {

    $valorEditarCancelar =
        sigenmuniUrlRuta(
            'conceptos/editar',
            [
                'id' => (int)$conceptoIdOriginal
            ]
        );

} else {

    $parametrosCancelar = [];

    if ($retornoValores === 'filtrado') {

        $conceptoIdCancelar =
            !empty($conceptoIdContextoValores)
                ? (int)$conceptoIdContextoValores
                : (int)$conceptoIdOriginal;

        $parametrosCancelar['concepto_id'] =
            $conceptoIdCancelar;
    }

    $valorEditarCancelar =
        sigenmuniUrlRuta(
            'conceptos/valores',
            $parametrosCancelar
        );
}


$valorEditarCsrf =
    sigenmuniCsrfToken();

?>
<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Editar Valor de Concepto - SIGENMUNI</title>

<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/conceptos/concepto_valor_editar.css'), ENT_QUOTES, 'UTF-8') ?>">

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
        Edición de Valor de Concepto
    </p>

</div>


<!-- =========================================
     CONTENIDO
========================================= -->

<div class="contenedor">

    <div class="panel">

        <h2>
            Editar Valor de Concepto
        </h2>


        <!-- ALERTA JAVASCRIPT -->

        <div
            id="alertaValor"
            class="mensaje mensaje-error"
            data-estilo="concepto_valor_editar-1"
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
                    $valorEditarAccion,
                    ENT_QUOTES,
                    'UTF-8'
                );
            ?>"
            method="POST"
            id="formValor"
            novalidate
        >

            <input
                type="hidden"
                name="_csrf"
                value="<?php
                    echo htmlspecialchars(
                        $valorEditarCsrf,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
            >

            <?php if ($modoIntegrado): ?>

                <input
                    type="hidden"
                    name="origen"
                    value="concepto"
                >

            <?php else: ?>

                <input
                    type="hidden"
                    name="retorno"
                    value="<?php
                        echo htmlspecialchars(
                            $retornoValores,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                >

                <input
                    type="hidden"
                    name="concepto_id_contexto"
                    value="<?php
                        echo $retornoValores === 'filtrado'
                            ? (int)(
                                $conceptoIdContextoValores
                                ?: $conceptoIdOriginal
                            )
                            : 0;
                    ?>"
                >

            <?php endif; ?>


            <!-- CONCEPTO -->

            <div class="grupo">

                <label for="concepto_id">
                    Concepto *
                </label>


                <?php if ($modoIntegrado): ?>

                    <input
                        type="hidden"
                        name="concepto_id"
                        id="concepto_id"
                        value="<?php
                            echo (int)$datosValor['concepto_id'];
                        ?>"
                    >


                    <input
                        type="text"
                        class="deshabilitado"
                        value="<?php
                            echo htmlspecialchars(
                                $conceptoFijo['codigo']
                                . ' - '
                                . $conceptoFijo['nombre'],
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                        readonly
                    >


                    <div class="ayuda">
                        El concepto está fijado porque este valor
                        se está editando desde la pantalla del concepto.
                    </div>

                <?php else: ?>

                    <select
                        name="concepto_id"
                        id="concepto_id"
                        data-event-change="concepto_valor_editar-1"
                    >

                        <option value="">
                            Seleccione
                        </option>

                        <?php foreach ($conceptosDisponibles as $c): ?>

                            <option
                                value="<?php echo (int)$c['id']; ?>"
                                <?php
                                echo (
                                    (string)(
                                        $datosValor['concepto_id']
                                        ?? ''
                                    )
                                    ===
                                    (string)$c['id']
                                )
                                    ?
                                    'selected'
                                    :
                                    '';
                                ?>
                            >

                                <?php
                                echo htmlspecialchars(
                                    $c['codigo']
                                    . ' - '
                                    . $c['nombre'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                <?php endif; ?>

            </div>


            <!-- CATEGORÍA -->

            <div class="grupo">

                <label for="categoria_id">
                    Categoría
                </label>

                <select
                    name="categoria_id"
                    id="categoria_id"
                >

                    <option value="">
                        Seleccione
                    </option>

                    <?php foreach ($categoriasValores as $cat): ?>

                        <option
                            value="<?php echo (int)$cat['id']; ?>"
                            <?php
                            echo (
                                (string)($datosValor['categoria_id'] ?? '')
                                ===
                                (string)$cat['id']
                            )
                                ? 'selected'
                                : '';
                            ?>
                        >

                            <?php
                            echo htmlspecialchars(
                                $cat['nombre'],
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <?php if ($formaActual !== 'TABLA_CATEGORIA'): ?>

            <!-- ESCALAFÓN -->

            <div class="grupo">

                <label for="escalafon_id">
                    Escalafón
                </label>

                <select
                    name="escalafon_id"
                    id="escalafon_id"
                >

                    <option value="">
                        Seleccione
                    </option>

                    <?php foreach ($escalafonesValores as $esc): ?>

                        <option
                            value="<?php echo (int)$esc['id']; ?>"
                            <?php
                            echo (
                                (string)($datosValor['escalafon_id'] ?? '')
                                ===
                                (string)$esc['id']
                            )
                                ? 'selected'
                                : '';
                            ?>
                        >

                            <?php
                            echo htmlspecialchars(
                                $esc['nombre'],
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <?php else: ?>

                <input
                    type="hidden"
                    name="escalafon_id"
                    id="escalafon_id"
                    value=""
                >

            <?php endif; ?>


            <!-- MONTO -->

            <div class="grupo">

                <label for="monto">
                    Monto
                </label>

                <input
                    type="number"
                    step="0.01"
                    min="0"
                    name="monto"
                    id="monto"
                    value="<?php
                        echo htmlspecialchars(
                            $datosValor['monto'] ?? '0.00',
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                >

            </div>


            <!-- PORCENTAJE -->

            <div class="grupo">

                <label for="porcentaje">
                    Porcentaje
                </label>

                <input
                    type="number"
                    step="0.01"
                    min="0"
                    name="porcentaje"
                    id="porcentaje"
                    value="<?php
                        echo htmlspecialchars(
                            $datosValor['porcentaje'] ?? '0.00',
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                >

            </div>


            <!-- AYUDA -->

            <div
                id="mensaje_ayuda"
                class="ayuda"
            >
                Seleccione un concepto para continuar.
            </div>


            <!-- FECHA DESDE -->

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
                            $datosValor['fecha_desde'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                >

            </div>


            <!-- FECHA HASTA -->

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
                            $datosValor['fecha_hasta'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                >

            </div>


            <!-- BOTONES -->

            <div class="acciones">

                <button
                    type="submit"
                    class="btn btn-guardar"
                    id="btnGuardar"
                >
                    Actualizar Valor
                </button>


                <a
                    href="<?php
                        echo htmlspecialchars(
                            $valorEditarCancelar,
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


<script type="application/json" id="datos-concepto_valor_editar"><?= sigenmuniJsonVista([
    'formasConcepto' => $formasConcepto
]) ?></script>
<script src="<?= htmlspecialchars(sigenmuniUrlAsset('js/conceptos/concepto_valor_editar.js'), ENT_QUOTES, 'UTF-8') ?>"></script>


</body>

</html>