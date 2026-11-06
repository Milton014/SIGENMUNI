<?php

/*
|--------------------------------------------------------------------------
| EDITAR CONCEPTO - SOLO ROUTER
|--------------------------------------------------------------------------
|
| La edición del concepto y la administración integrada de sus valores
| utilizan únicamente las rutas registradas del módulo.
|
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../../../core/Url.php';
require_once __DIR__ . '/../../../core/Csrf.php';


$conceptoEditarId =
    (int)(
        $concepto['id']
        ?? 0
    );


$conceptoEditarAccion =
    sigenmuniUrlRuta('conceptos/editar', array_merge($contextoValores, [
            'id' => $conceptoEditarId
        ]));


$conceptoEditarVolver =
    sigenmuniUrlRuta('conceptos', $contextoListado);


$conceptoEditarCsrf =
    sigenmuniCsrfToken();


$conceptoEditarUrlNuevoValor =
    sigenmuniUrlRuta('conceptos/valores/nuevo', array_merge($contextoValores, [
            'concepto_id' => $conceptoEditarId,
            'origen' => 'concepto'
        ]));


$conceptoEditarUrlEstadoValor =
    sigenmuniUrlRuta('conceptos/valores/estado', $contextoValores);

$paginacion = $paginacionValores ?? sigenmuniCalcularPaginacion(0);
$paginacionRuta = 'conceptos/editar';
$paginacionParametros = array_merge($contextoValores, ['id' => $conceptoEditarId]);
$paginacionEntidad = 'valores';
$paginacionClavePagina = 'pagina_valores';
$paginacionClaveCantidad = 'por_pagina_valores';
?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Editar Concepto - SIGENMUNI</title>

<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/conceptos/concepto_editar.css'), ENT_QUOTES, 'UTF-8') ?>">

<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/compartido/paginacion.css'), ENT_QUOTES, 'UTF-8') ?>">
<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/conceptos/paginacion.css'), ENT_QUOTES, 'UTF-8') ?>">
</head>

<body>

<?php

/*
|--------------------------------------------------------------------------
| NORMALIZACIÓN VISUAL DE FORMA DE CÁLCULO
|--------------------------------------------------------------------------
|
| FIJO queda fuera de la nueva arquitectura.
| Si existe un concepto histórico FIJO, se presenta como MANUAL para que
| pueda migrarse al esquema de Conceptos por Empleado.
|
*/

$codigoActual = (int)($concepto['codigo'] ?? 0);

$formaActual = strtoupper(
    trim(
        (string)($concepto['forma_calculo'] ?? '')
    )
);

if ($formaActual === 'FIJO') {
    $formaActual = 'MANUAL';
}

$codigosConValoresCategoria = [101, 102, 104];

$esConceptoConValoresCategoria =
    in_array(
        $codigoActual,
        $codigosConValoresCategoria,
        true
    );

?>

<div class="header">

    <h1>SIGENMUNI</h1>

    <p>
        Edición de Concepto de Liquidación
    </p>

</div>


<div class="contenedor">

    <div class="panel">

        <div class="panel-encabezado">

            <h2>
                Editar Concepto
            </h2>

            <a
                href="<?php
                    echo htmlspecialchars(
                        $conceptoEditarVolver,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                class="btn btn-volver"
            >
                Volver
            </a>

        </div>


        <div
            id="alertaConcepto"
            class="mensaje mensaje-error"
            data-estilo="concepto_editar-1"
        >
        </div>


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


        <div class="aviso-arquitectura">

            Los conceptos <strong>MANUAL</strong> y <strong>PORCENTAJE</strong>
            se parametrizan por empleado desde <strong>Conceptos por Empleado</strong>.
            Solo los conceptos <strong>101, 102 y 104</strong> administran montos
            por categoría desde esta pantalla.

        </div>


        <form
            action="<?php
                echo htmlspecialchars(
                    $conceptoEditarAccion,
                    ENT_QUOTES,
                    'UTF-8'
                );
            ?>"
            method="POST"
            id="formConcepto" data-cambios-pendientes="<?= ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' ? '1' : '0' ?>"
            novalidate
        >

            <input
                type="hidden"
                name="_csrf"
                value="<?php
                    echo htmlspecialchars(
                        $conceptoEditarCsrf,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
            >


            <!-- CAMPOS HISTÓRICOS NEUTRALIZADOS -->

            <input
                type="hidden"
                name="porcentaje"
                value="0"
            >

            <input
                type="hidden"
                name="monto_fijo"
                value="0"
            >

            <input
                type="hidden"
                name="orden_calculo"
                value="0"
            >


            <!-- CÓDIGO / NOMBRE -->

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
                                $concepto['codigo'] ?? '',
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
                                $concepto['nombre'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                    >

                </div>

            </div>


            <!-- TIPO / FORMA -->

            <div class="fila">

                <div class="campo">

                    <label for="categoria">
                        Tipo de Concepto *
                    </label>

                    <select
                        name="categoria"
                        id="categoria"
                        data-event-change="concepto_editar-1"
                    >

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
                                    ($concepto['categoria'] ?? '')
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

                    <select
                        name="forma_calculo"
                        id="forma_calculo"
                        data-event-change="concepto_editar-2"
                    >

                        <?php foreach ($formasCalculo as $forma): ?>

                            <?php

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
                                    $formaActual
                                    ===
                                    $forma
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
                        id="ayuda_forma"
                        class="ayuda"
                    >
                    </div>

                </div>

            </div>


            <!-- BASE DEL PORCENTAJE
                 Para cualquier concepto PORCENTAJE -->

            <div
                class="fila-completa"
                id="contenedorBaseCalculo"
                data-estilo="concepto_editar-2"
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
                                        ($concepto['base_calculo'] ?? '')
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
                        Define la base sobre la que se aplicará el porcentaje
                        cargado para cada empleado. Por ejemplo: 103 usa
                        Básico + Dedicación; 105 y 110 usan Básico.
                    </div>

                </div>

            </div>


            <!-- FECHAS -->

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
                                $concepto['fecha_desde'] ?? '',
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
                                $concepto['fecha_hasta'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                    >

                </div>

            </div>


            <!-- DESCRIPCIÓN -->

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
                            $concepto['descripcion'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?></textarea>

                </div>

            </div>


            <!-- CHECKBOXES -->

            <div class="checks">


                <div class="check-item">

                    <label>

                        <input
                            type="checkbox"
                            name="aplica_sac"
                            id="aplica_sac"
                            value="1"
                            <?php
                            echo (
                                (int)($concepto['aplica_sac'] ?? 0)
                                === 1
                            )
                                ? 'checked'
                                : '';
                            ?>
                        >

                        Aplica SAC

                    </label>

                </div>


                <div class="check-item">

                    <label>

                        <input
                            type="checkbox"
                            name="visible_recibo"
                            id="visible_recibo"
                            value="1"
                            <?php
                            echo (
                                (int)($concepto['visible_recibo'] ?? 0)
                                === 1
                            )
                                ? 'checked'
                                : '';
                            ?>
                        >

                        Visible en Recibo

                    </label>

                </div>


                <div class="check-item">

                    <label>

                        <input
                            type="checkbox"
                            name="activo"
                            id="activo"
                            value="1"
                            <?php
                            echo (
                                (int)($concepto['activo'] ?? 0)
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


            <!-- BOTÓN -->

            <div class="acciones">

                <button
                    type="submit"
                    class="btn btn-guardar"
                >
                    Actualizar Concepto
                </button>

            </div>

        </form>

    </div>


    <!-- =========================================
         VALORES POR CATEGORÍA
         SOLO 101 / 102 / 104
    ========================================== -->

    <?php if ($esConceptoConValoresCategoria): ?>

        <div class="panel panel-valores" data-valores-integrados>

            <div class="valores-header">

                <div>

                    <h2>
                        Valores por Categoría
                    </h2>

                    <p>
                        Administre el monto vigente de este concepto
                        para cada categoría salarial.
                    </p>

                </div>


                <a
                    href="<?php
                        echo htmlspecialchars(
                            $conceptoEditarUrlNuevoValor,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                    class="btn btn-nuevo-valor"
                >
                    + Agregar Valor
                </a>

            </div>


<form method="get" action="<?= htmlspecialchars(sigenmuniUrlEntrada(), ENT_QUOTES, 'UTF-8') ?>" class="filtro-paginacion-valores" data-form-paginacion>
                <input type="hidden" name="r" value="conceptos/editar">
                <input type="hidden" name="id" value="<?= (int)$conceptoEditarId ?>">
                <?php foreach ($contextoListado as $claveFiltro => $valorFiltro): ?>
                    <input type="hidden" name="<?= htmlspecialchars($claveFiltro, ENT_QUOTES, 'UTF-8') ?>" value="<?= htmlspecialchars((string)$valorFiltro, ENT_QUOTES, 'UTF-8') ?>">
                <?php endforeach; ?>
                <label class="paginacion-tamano" for="por_pagina_valores">Por página
                <select id="por_pagina_valores" name="por_pagina_valores" data-paginacion-cantidad>
                    <?php foreach ([10, 25, 50] as $cantidadPagina): ?>
                        <option value="<?= $cantidadPagina ?>" <?= $paginacion['por_pagina'] === $cantidadPagina ? 'selected' : '' ?>><?= $cantidadPagina ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
                <button type="submit" class="paginacion-aplicar">Aplicar</button>
            </form>
            <?php require __DIR__ . '/../../../componentes/paginacion.php'; ?>

            <?php if (!empty($valoresConcepto)): ?>

                <div class="tabla-valores-contenedor">

                    <table class="tabla-valores">

                        <thead>

                            <tr>

                                <th>
                                    Categoría
                                </th>

                                <th>
                                    Monto
                                </th>

                                <th>
                                    Desde
                                </th>

                                <th>
                                    Hasta
                                </th>

                                <th>
                                    Estado
                                </th>

                                <th>
                                    Acciones
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <?php foreach ($valoresConcepto as $valor): ?>

                                <tr>

                                    <td>

                                        <?php
                                        echo htmlspecialchars(
                                            !empty($valor['categoria'])
                                                ? $valor['categoria']
                                                : 'General',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        );
                                        ?>

                                    </td>


                                    <td class="valor-monto">

                                        $ <?php
                                        echo number_format(
                                            (float)(
                                                $valor['monto']
                                                ?? 0
                                            ),
                                            2,
                                            ',',
                                            '.'
                                        );
                                        ?>

                                    </td>


                                    <td>

                                        <?php
                                        echo !empty(
                                            $valor['fecha_desde']
                                        )
                                            ? date(
                                                'd/m/Y',
                                                strtotime(
                                                    $valor['fecha_desde']
                                                )
                                            )
                                            : '-';
                                        ?>

                                    </td>


                                    <td>

                                        <?php
                                        echo !empty(
                                            $valor['fecha_hasta']
                                        )
                                            ? date(
                                                'd/m/Y',
                                                strtotime(
                                                    $valor['fecha_hasta']
                                                )
                                            )
                                            : 'Vigente';
                                        ?>

                                    </td>


                                    <td>

                                        <?php if (
                                            (int)(
                                                $valor['activo']
                                                ?? 0
                                            )
                                            === 1
                                        ): ?>

                                            <span class="estado-valor estado-activo">
                                                Activo
                                            </span>

                                        <?php else: ?>

                                            <span class="estado-valor estado-inactivo">
                                                Inactivo
                                            </span>

                                        <?php endif; ?>

                                    </td>


                                    <td>

                                        <div class="acciones-valor">

                                            <a
                                                href="<?php
                                                    echo htmlspecialchars(
                                                        sigenmuniUrlRuta('conceptos/valores/editar', array_merge($contextoValores, [
                                                                'id' => (int)$valor['id'],
                                                                'origen' => 'concepto'
                                                            ])),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    );
                                                ?>"
                                                class="btn-valor btn-editar-valor"
                                            >
                                                Editar
                                            </a>


                                            <form
                                                    method="POST"
                                                    action="<?php
                                                        echo htmlspecialchars(
                                                            $conceptoEditarUrlEstadoValor,
                                                            ENT_QUOTES,
                                                            'UTF-8'
                                                        );
                                                    ?>"
                                                    class="form-accion-valor"
                                                    data-event-submit="concepto_editar-3" data-event-value="<?php
                                                        echo (
                                                            (int)($valor['activo'] ?? 0)
                                                            === 1
                                                        )
                                                            ? '¿Desea inactivar este valor?'
                                                            : '¿Desea activar este valor?';
                                                    ?>"
                                                >

                                                    <input
                                                        type="hidden"
                                                        name="id"
                                                        value="<?php echo (int)$valor['id']; ?>"
                                                    >

                                                    <input
                                                        type="hidden"
                                                        name="origen"
                                                        value="concepto"
                                                    >

                                                    <input
                                                        type="hidden"
                                                        name="_csrf"
                                                        value="<?php
                                                            echo htmlspecialchars(
                                                                $conceptoEditarCsrf,
                                                                ENT_QUOTES,
                                                                'UTF-8'
                                                            );
                                                        ?>"
                                                    >

                                                    <button
                                                        type="submit"
                                                        class="btn-valor <?php
                                                            echo (
                                                                (int)($valor['activo'] ?? 0)
                                                                === 1
                                                            )
                                                                ? 'btn-estado-valor'
                                                                : 'btn-activar-valor';
                                                        ?>"
                                                    >
                                                        <?php
                                                        echo (
                                                            (int)($valor['activo'] ?? 0)
                                                            === 1
                                                        )
                                                            ? 'Inactivar'
                                                            : 'Activar';
                                                        ?>
                                                    </button>

                                                </form>

                                        </div>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>
            <?php require __DIR__ . '/../../../componentes/paginacion.php'; ?>

            <?php else: ?>

                <div class="sin-valores">

                    Este concepto todavía no tiene valores por categoría registrados.
                    <br>
                    Agregue un monto vigente para cada categoría que corresponda.

                </div>

            <?php endif; ?>

        </div>

    <?php endif; ?>

</div>


<script src="<?= htmlspecialchars(sigenmuniUrlAsset('js/conceptos/concepto_editar.js'), ENT_QUOTES, 'UTF-8') ?>"></script>

<script src="<?= htmlspecialchars(sigenmuniUrlAsset('js/conceptos/paginacion.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
</body>

</html>
