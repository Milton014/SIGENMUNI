<?php

/*
|--------------------------------------------------------------------------
| NUEVO CONCEPTO - SOLO ROUTER
|--------------------------------------------------------------------------
|
| El formulario de alta de conceptos trabaja únicamente con las rutas
| registradas del módulo.
|
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../../../core/Url.php';
require_once __DIR__ . '/../../../core/Csrf.php';


$conceptoNuevoAccion =
    sigenmuniUrlRuta(
        'conceptos/nuevo'
    );


$conceptoNuevoVolver =
    sigenmuniUrlRuta(
        'conceptos'
    );


$conceptoNuevoCsrf =
    sigenmuniCsrfToken();

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Nuevo Concepto - SIGENMUNI</title>

<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/conceptos/concepto_nuevo.css'), ENT_QUOTES, 'UTF-8') ?>">

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
        Alta de Concepto de Liquidación
    </p>

</div>


<!-- =========================================
     CONTENIDO
========================================= -->

<div class="contenedor">

    <div class="panel">

        <h2>
            Nuevo Concepto
        </h2>


        <!-- =====================================
             ALERTA JAVASCRIPT
        ====================================== -->

        <div
            id="alertaConcepto"
            class="mensaje mensaje-error"
            data-estilo="concepto_nuevo-1"
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


        <div class="aviso-forma">

            
                
            </strong>

            Los importes por categoría se administran únicamente desde
            <strong data-estilo="concepto_nuevo-2">Valores del Concepto</strong>
            para Sueldo Básico (101), Dedicación Funcional (102) y
            Suplemento Especial (104).
            Los importes y porcentajes particulares de cada empleado se cargan
            desde <strong data-estilo="concepto_nuevo-3">Conceptos por Empleado</strong>.

        </div>


        <!-- =====================================
             FORMULARIO
        ====================================== -->

        <form
            action="<?php
                echo htmlspecialchars(
                    $conceptoNuevoAccion,
                    ENT_QUOTES,
                    'UTF-8'
                );
            ?>"
            method="POST"
            id="formConcepto"
            novalidate
        >

            <input
                type="hidden"
                name="_csrf"
                value="<?php
                    echo htmlspecialchars(
                        $conceptoNuevoCsrf,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
            >


            <!-- =================================
                 CAMPOS INTERNOS DE COMPATIBILIDAD
            ================================== -->

            <input type="hidden" name="porcentaje" value="0">
            <input type="hidden" name="monto_fijo" value="0">
            <input type="hidden" name="orden_calculo" value="0">


            <!-- =================================
                 CÓDIGO / NOMBRE
            ================================== -->

            <div class="fila">

                <div class="campo">

                    <label for="codigo">
                        Código *
                    </label>

                    <input
                        type="number"
                        name="codigo"
                        id="codigo"
                        min="1"
                        value="<?php
                            echo htmlspecialchars(
                                $datos['codigo'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                    >

                </div>


                <div class="campo">

                    <label for="nombre">
                        Nombre *
                    </label>

                    <input
                        type="text"
                        name="nombre"
                        id="nombre"
                        maxlength="150"
                        value="<?php
                            echo htmlspecialchars(
                                $datos['nombre'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                    >

                </div>

            </div>


            <!-- =================================
                 TIPO / FORMA DE CÁLCULO
            ================================== -->

            <div class="fila">

                <div class="campo">

                    <label for="categoria">
                        Tipo de Concepto *
                    </label>

                    <select
                        name="categoria"
                        id="categoria"
                        data-event-change="concepto_nuevo-1"
                    >

                        <option value="">
                            -- Seleccionar --
                        </option>

                        <?php foreach ($categorias as $cat): ?>

                            <option
                                value="<?php
                                    echo htmlspecialchars(
                                        $cat,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                ?>"
                                <?php
                                echo (
                                    ($datos['categoria'] ?? '')
                                    ===
                                    $cat
                                )
                                    ? 'selected'
                                    : '';
                                ?>
                            >

                                <?php
                                echo htmlspecialchars(
                                    $cat,
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <div class="campo">

                    <label for="forma_calculo">
                        Forma de Cálculo *
                    </label>

                    <?php

                    $formaSeleccionada =
                        $datos['forma_calculo']
                        ?? 'MANUAL';

                    if ($formaSeleccionada === 'FIJO') {
                        $formaSeleccionada = 'MANUAL';
                    }

                    ?>

                    <select
                        name="forma_calculo"
                        id="forma_calculo"
                        data-event-change="concepto_nuevo-2"
                    >

                        <?php foreach ($formasCalculo as $forma): ?>

                            <?php

                            /* FIJO queda fuera de la nueva interfaz. */
                            if ($forma === 'FIJO') {
                                continue;
                            }

                            ?>

                            <option
                                value="<?php
                                    echo htmlspecialchars(
                                        $forma,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                ?>"
                                <?php
                                echo (
                                    $formaSeleccionada === $forma
                                )
                                    ? 'selected'
                                    : '';
                                ?>
                            >

                                <?php

                                switch ($forma) {

                                    case 'TABLA_CATEGORIA':
                                        echo 'VALOR POR CATEGORÍA';
                                        break;

                                    case 'PORCENTAJE':
                                        echo 'PORCENTAJE';
                                        break;

                                    case 'MANUAL':
                                        echo 'MANUAL';
                                        break;

                                    case 'FORMULA':
                                        echo 'AUTOMÁTICO';
                                        break;

                                    default:
                                        echo htmlspecialchars(
                                            $forma,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        );
                                        break;
                                }

                                ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                    <div
                        id="ayudaForma"
                        class="origen-calculo"
                    >
                    </div>

                </div>

            </div>


            <!-- =================================
                 BASE DEL PORCENTAJE
                 Para cualquier concepto PORCENTAJE
            ================================== -->

            <div
                class="fila-completa"
                id="contenedorBaseCalculo"
                data-estilo="concepto_nuevo-4"
            >

                <div class="campo">

                    <label for="base_calculo">
                        Aplicar porcentaje sobre *
                    </label>

                    <select
                        name="base_calculo"
                        id="base_calculo"
                    >

                        <option value="">
                            -- Seleccionar base --
                        </option>

                        <?php foreach (($basesCalculoPorcentaje ?? []) as $base): ?>

                            <?php

                            $etiquetaBase = $base;

                            if ($base === 'BASICO') {

                                $etiquetaBase =
                                    'Sueldo Básico';

                            } elseif (
                                $base ===
                                'BASICO_MAS_DEDICACION'
                            ) {

                                $etiquetaBase =
                                    'Sueldo Básico + Dedicación Funcional';

                            } elseif (
                                $base ===
                                'TOTAL_REMUNERATIVO'
                            ) {

                                $etiquetaBase =
                                    'Total Remunerativo';

                            } elseif (
                                $base ===
                                'TOTAL_REMUNERATIVO_MENOS_DESCUENTOS_OBLIGATORIOS'
                            ) {

                                $etiquetaBase =
                                    'Total Remunerativo menos Descuentos Obligatorios';
                            }

                            ?>

                            <option
                                value="<?php
                                    echo htmlspecialchars(
                                        $base,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                ?>"
                                <?php
                                    echo (
                                        ($datos['base_calculo'] ?? '')
                                        ===
                                        $base
                                    )
                                        ? 'selected'
                                        : '';
                                ?>
                            >
                                <?php
                                    echo htmlspecialchars(
                                        $etiquetaBase,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                    <div
                        class="ayuda"
                        id="ayudaBaseCalculo"
                    >
                        Esta opción define la base sobre la que se aplicará
                        el porcentaje cargado luego en Conceptos por Empleado.
                        Por ejemplo: 103 usa Básico + Dedicación; 105 y 110 usan Básico.
                    </div>

                </div>

            </div>


            <!-- =================================
                 FECHAS
            ================================== -->

            <div class="fila">

                <div class="campo">

                    <label for="fecha_desde">
                        Fecha Desde
                    </label>

                    <input
                        type="date"
                        name="fecha_desde"
                        id="fecha_desde"
                        value="<?php
                            echo htmlspecialchars(
                                $datos['fecha_desde'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                    >

                </div>


                <div class="campo">

                    <label for="fecha_hasta">
                        Fecha Hasta
                    </label>

                    <input
                        type="date"
                        name="fecha_hasta"
                        id="fecha_hasta"
                        value="<?php
                            echo htmlspecialchars(
                                $datos['fecha_hasta'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                    >

                    <div class="ayuda">
                        Puede dejarse vacía si el concepto no tiene vencimiento.
                    </div>

                </div>

            </div>


            <!-- =================================
                 DESCRIPCIÓN
            ================================== -->

            <div class="fila-completa">

                <div class="campo">

                    <label for="descripcion">
                        Descripción
                    </label>

                    <textarea
                        name="descripcion"
                        id="descripcion"
                    ><?php
                        echo htmlspecialchars(
                            $datos['descripcion'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?></textarea>

                </div>

            </div>


            <!-- =================================
                 CHECKBOXES
            ================================== -->

            <div class="checks">


                <!-- APLICA SAC -->

                <div class="check-item">

                    <label>

                        <input
                            type="checkbox"
                            name="aplica_sac"
                            id="aplica_sac"
                            value="1"
                            <?php
                            echo (
                                (int)($datos['aplica_sac'] ?? 0)
                                === 1
                            )
                                ? 'checked'
                                : '';
                            ?>
                        >

                        Aplica SAC

                    </label>

                </div>


                <!-- VISIBLE RECIBO -->

                <div class="check-item">

                    <label>

                        <input
                            type="checkbox"
                            name="visible_recibo"
                            id="visible_recibo"
                            value="1"
                            <?php
                            echo (
                                (int)($datos['visible_recibo'] ?? 1)
                                === 1
                            )
                                ? 'checked'
                                : '';
                            ?>
                        >

                        Visible en Recibo

                    </label>

                </div>


                <!-- ACTIVO -->

                <div class="check-item">

                    <label>

                        <input
                            type="checkbox"
                            name="activo"
                            id="activo"
                            value="1"
                            <?php
                            echo (
                                (int)($datos['activo'] ?? 1)
                                === 1
                            )
                                ? 'checked'
                                : '';
                            ?>
                        >

                        Activo

                    </label>

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
                    Guardar Concepto
                </button>

                <a
                    href="<?php
                        echo htmlspecialchars(
                            $conceptoNuevoVolver,
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


<script src="<?= htmlspecialchars(sigenmuniUrlAsset('js/conceptos/concepto_nuevo.js'), ENT_QUOTES, 'UTF-8') ?>"></script>

</body>

</html>
