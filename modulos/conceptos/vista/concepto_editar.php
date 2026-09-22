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
    sigenmuniUrlRuta(
        'conceptos/editar',
        [
            'id' => $conceptoEditarId
        ]
    );


$conceptoEditarVolver =
    sigenmuniUrlRuta(
        'conceptos'
    );


$conceptoEditarCsrf =
    sigenmuniCsrfToken();


$conceptoEditarUrlNuevoValor =
    sigenmuniUrlRuta(
        'conceptos/valores/nuevo',
        [
            'concepto_id' => $conceptoEditarId,
            'origen' => 'concepto'
        ]
    );


$conceptoEditarUrlEstadoValor =
    sigenmuniUrlRuta(
        'conceptos/valores/estado'
    );

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Editar Concepto - SIGENMUNI</title>

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

    box-shadow:
        0 4px 14px rgba(0,0,0,.10);
}

.header h1{
    margin:0;
    font-size:30px;
}

.header p{
    margin-top:6px;
    margin-bottom:0;
    font-size:14px;
}


/* =========================================
   CONTENEDOR
========================================= */

.contenedor{
    width:95%;
    max-width:1250px;
    margin:30px auto;
}

.panel{
    background:white;
    padding:28px;
    border-radius:18px;
    box-shadow:0 8px 20px rgba(0,0,0,.08);
}

h2{
    margin-top:0;
    margin-bottom:22px;
    color:#0f766e;
}

.panel-encabezado{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:16px;
    flex-wrap:wrap;
    margin-bottom:22px;
}

.panel-encabezado h2{
    margin:0;
}

.panel-encabezado .btn-volver{
    flex-shrink:0;
}


/* =========================================
   FORMULARIO
========================================= */

.fila{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:18px;
    margin-bottom:18px;
}

.fila-completa{
    margin-bottom:18px;
}

.campo{
    display:flex;
    flex-direction:column;
}

.campo label{
    margin-bottom:6px;
    font-weight:bold;
    font-size:14px;
}

.campo input,
.campo select,
.campo textarea{
    width:100%;
    padding:12px;
    border:1px solid #cbd5e1;
    border-radius:10px;
    font-size:14px;
    outline:none;
    transition:.2s;
    background:white;
}

.campo input:focus,
.campo select:focus,
.campo textarea:focus{
    border-color:#14b8a6;
    box-shadow:0 0 0 3px rgba(20,184,166,.15);
}

.campo textarea{
    min-height:100px;
    resize:vertical;
}

.ayuda{
    margin-top:6px;
    font-size:13px;
    color:#64748b;
    line-height:1.45;
}

.aviso-arquitectura{
    margin:0 0 20px;
    padding:14px 16px;
    background:#eff6ff;
    border:1px solid #bfdbfe;
    border-radius:12px;
    color:#1e40af;
    font-size:13px;
    line-height:1.55;
}


/* =========================================
   CHECKBOXES
========================================= */

.checks{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:12px;
    margin:22px 0;
}

.check-item{
    background:#f8fafc;
    border:1px solid #e5e7eb;
    border-radius:10px;
    padding:12px;
}

.check-item label{
    display:flex;
    align-items:center;
    gap:8px;
    cursor:pointer;
    font-size:14px;
}

.check-item input{
    width:auto;
}

/* =========================================
   MENSAJES
========================================= */

.mensaje{
    padding:14px 16px;
    border-radius:10px;
    margin-bottom:18px;
    font-weight:bold;
    font-size:14px;
}

.mensaje-error{
    background:#fee2e2;
    color:#991b1b;
    border:1px solid #fecaca;
}

.mensaje-ok{
    background:#dcfce7;
    color:#166534;
    border:1px solid #86efac;
}

.input-error{
    border-color:#dc2626 !important;
    background:#fff1f2 !important;
    box-shadow:0 0 0 3px rgba(220,38,38,.12) !important;
}


/* =========================================
   BOTONES
========================================= */

.acciones{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
    margin-top:22px;
}

.btn{
    display:inline-block;
    padding:11px 16px;
    border:none;
    border-radius:10px;
    text-decoration:none;
    color:white;
    cursor:pointer;
    font-size:14px;
    font-weight:bold;
    transition:.2s;
}

.btn:hover{
    opacity:.92;
    transform:translateY(-1px);
}

.btn-guardar{
    background:#0f766e;
}

.btn-volver{
    background:#1f2937;
}


/* =========================================
   VALORES DEL CONCEPTO
========================================= */

.panel-valores{
    margin-top:26px;
}

.valores-header{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    gap:16px;
    flex-wrap:wrap;
    margin-bottom:18px;
}

.valores-header h2{
    margin:0 0 6px 0;
}

.valores-header p{
    margin:0;
    color:#64748b;
    font-size:14px;
    line-height:1.5;
}

.btn-nuevo-valor{
    background:#2563eb;
}

.tabla-valores-contenedor{
    width:100%;
    overflow-x:auto;
}

.tabla-valores{
    width:100%;
    border-collapse:collapse;
    min-width:680px;
}

.tabla-valores th,
.tabla-valores td{
    padding:11px 10px;
    border-bottom:1px solid #e5e7eb;
    text-align:left;
    font-size:13px;
    vertical-align:middle;
}

.tabla-valores th{
    background:#0f766e;
    color:white;
    white-space:nowrap;
}

.tabla-valores tbody tr:hover{
    background:#f8fafc;
}

.valor-monto{
    font-weight:bold;
    color:#0f766e;
    white-space:nowrap;
}

.estado-valor{
    display:inline-block;
    padding:5px 9px;
    border-radius:999px;
    font-size:12px;
    font-weight:bold;
    white-space:nowrap;
}

.estado-activo{
    background:#dcfce7;
    color:#166534;
}

.estado-inactivo{
    background:#fee2e2;
    color:#991b1b;
}

.acciones-valor{
    display:flex;
    gap:7px;
    flex-wrap:wrap;
}

.form-accion-valor{
    margin:0;
    padding:0;
}

.btn-valor{
    display:inline-block;
    padding:7px 10px;
    border-radius:8px;
    color:white;
    text-decoration:none;
    font-size:12px;
    font-weight:bold;
    white-space:nowrap;
    border:none;
    cursor:pointer;
    font-family:inherit;
}

.btn-editar-valor{
    background:#2563eb;
}

.btn-estado-valor{
    background:#dc2626;
}

.btn-activar-valor{
    background:#16a34a;
}

.sin-valores{
    padding:24px;
    border:1px dashed #cbd5e1;
    border-radius:12px;
    background:#f8fafc;
    text-align:center;
    color:#64748b;
    line-height:1.6;
}


/* =========================================
   TABLET
========================================= */

@media (max-width:900px){

    .checks{
        grid-template-columns:1fr 1fr;
    }
}


/* =========================================
   CELULAR
========================================= */

@media (max-width:768px){

    .header{
        padding:20px;
        text-align:center;
    }

    .header h1{
        font-size:24px;
    }

    .header p{
        font-size:13px;
    }

    .contenedor{
        width:94%;
        margin:20px auto;
    }

    .panel{
        padding:20px;
        border-radius:16px;
    }

    h2{
        font-size:24px;
        text-align:center;
    }

    .panel-encabezado{
        flex-direction:column;
        align-items:stretch;
        margin-bottom:20px;
    }

    .panel-encabezado .btn-volver{
        width:100%;
        text-align:center;
    }

    .fila{
        grid-template-columns:1fr;
        gap:16px;
    }

    .checks{
        grid-template-columns:1fr;
    }

    .acciones{
        flex-direction:column;
    }

    .btn{
        width:100%;
        text-align:center;
    }

    .campo input,
    .campo select,
    .campo textarea{
        min-height:44px;
        font-size:16px;
    }
}


/* =========================================
   CELULARES PEQUEÑOS
========================================= */

@media (max-width:480px){

    .header h1{
        font-size:22px;
    }

    h2{
        font-size:22px;
    }

    .panel{
        padding:18px;
    }
}

</style>

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
            style="display:none;"
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
            id="formConcepto"
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
                        onchange="actualizarConfiguracion(); actualizarBaseCalculo();"
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
                        onchange="actualizarConfiguracion(); actualizarBaseCalculo();"
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
                style="display:none;"
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

        <div class="panel panel-valores">

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
                                                        sigenmuniUrlRuta(
                                                            'conceptos/valores/editar',
                                                            [
                                                                'id' => (int)$valor['id'],
                                                                'origen' => 'concepto'
                                                            ]
                                                        ),
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
                                                    onsubmit="return confirm('<?php
                                                        echo (
                                                            (int)($valor['activo'] ?? 0)
                                                            === 1
                                                        )
                                                            ? '¿Desea inactivar este valor?'
                                                            : '¿Desea activar este valor?';
                                                    ?>');"
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


<script>

/*
|--------------------------------------------------------------------------
| CONFIGURAR FORMA SEGÚN LA NUEVA ARQUITECTURA
|--------------------------------------------------------------------------
*/

function actualizarConfiguracion(){

    const codigoInput =
        document.getElementById("codigo");

    const formaSelect =
        document.getElementById("forma_calculo");

    const ayudaForma =
        document.getElementById("ayuda_forma");

    const codigo =
        parseInt(
            codigoInput.value || "0",
            10
        );

    const codigosCategoria =
        [101, 102, 104];

    const usaValoresCategoria =
        codigosCategoria.includes(codigo);


    /*
    |--------------------------------------------------------------------------
    | 101 / 102 / 104
    |--------------------------------------------------------------------------
    */

    if(usaValoresCategoria){

        formaSelect.value =
            "TABLA_CATEGORIA";

        ayudaForma.innerHTML =
            "El monto se administra por categoría en la sección <strong>Valores por Categoría</strong>. Este concepto no se asigna individualmente desde Conceptos por Empleado.";

        actualizarBaseCalculo();

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | RESTO DE CONCEPTOS
    |--------------------------------------------------------------------------
    */

    if(formaSelect.value === "TABLA_CATEGORIA"){

        if(
            Array.from(
                formaSelect.options
            ).some(
                option =>
                    option.value === "MANUAL"
            )
        ){
            formaSelect.value =
                "MANUAL";
        }
    }


    const forma =
        formaSelect.value;


    if(forma === "MANUAL"){

        ayudaForma.innerHTML =
            "Este concepto se podrá asignar desde <strong>Conceptos por Empleado</strong>. El importe se cargará individualmente para cada empleado.";

    }else if(forma === "PORCENTAJE"){

        ayudaForma.innerHTML =
            "Este concepto se podrá asignar desde <strong>Conceptos por Empleado</strong>. El porcentaje se cargará individualmente para cada empleado y se aplicará sobre la base seleccionada debajo.";

    }else if(forma === "FORMULA"){

        ayudaForma.innerHTML =
            "Este concepto será calculado <strong>automáticamente</strong> por la lógica de liquidación y no se asigna individualmente a los empleados.";

    }else{

        ayudaForma.innerHTML =
            "Seleccione cómo se obtiene el valor del concepto.";
    }
}


/*
|--------------------------------------------------------------------------
| CONFIGURAR BASE DEL PORCENTAJE
|--------------------------------------------------------------------------
|
| Corresponde para cualquier concepto cuya:
|
| Forma = PORCENTAJE
|
*/

function actualizarBaseCalculo(){

    const forma =
        document
            .getElementById("forma_calculo")
            .value;

    const contenedor =
        document
            .getElementById("contenedorBaseCalculo");

    const base =
        document
            .getElementById("base_calculo");


    if(forma === "PORCENTAJE"){

        contenedor.style.display =
            "block";

    }else{

        contenedor.style.display =
            "none";

        if(base){
            base.value = "";
        }
    }
}


/*
|--------------------------------------------------------------------------
| VALIDACIÓN
|--------------------------------------------------------------------------
*/

document
    .getElementById("formConcepto")
    .addEventListener(
        "submit",
        function(e){

    const alerta =
        document.getElementById(
            "alertaConcepto"
        );

    const codigoCampo =
        document.getElementById(
            "codigo"
        );

    const nombreCampo =
        document.getElementById(
            "nombre"
        );

    const categoriaCampo =
        document.getElementById(
            "categoria"
        );

    const formaCampo =
        document.getElementById(
            "forma_calculo"
        );

    const baseCalculoCampo =
        document.getElementById(
            "base_calculo"
        );

    const fechaDesdeCampo =
        document.getElementById(
            "fecha_desde"
        );

    const fechaHastaCampo =
        document.getElementById(
            "fecha_hasta"
        );


    [
        codigoCampo,
        nombreCampo,
        categoriaCampo,
        formaCampo,
        baseCalculoCampo,
        fechaDesdeCampo,
        fechaHastaCampo
    ].forEach(function(campo){

        if(campo){
            campo.classList.remove(
                "input-error"
            );
        }
    });


    alerta.style.display =
        "none";

    alerta.innerHTML =
        "";


    function mostrarError(
        mensaje,
        campo
    ){

        e.preventDefault();

        alerta.innerHTML =
            mensaje;

        alerta.style.display =
            "block";

        if(campo){

            campo.classList.add(
                "input-error"
            );

            campo.focus();
        }

        window.scrollTo({
            top:0,
            behavior:"smooth"
        });
    }


    const codigo =
        codigoCampo
            .value
            .trim();

    const nombre =
        nombreCampo
            .value
            .trim();

    const categoria =
        categoriaCampo
            .value;

    const forma =
        formaCampo
            .value;

    const baseCalculo =
        baseCalculoCampo
            ? baseCalculoCampo.value
            : "";

    const fechaDesde =
        fechaDesdeCampo
            .value;

    const fechaHasta =
        fechaHastaCampo
            .value;


    if(codigo === ""){

        mostrarError(
            "Debe ingresar el código.",
            codigoCampo
        );

        return;
    }


    if(!/^[0-9]+$/.test(codigo)){

        mostrarError(
            "El código debe contener solo números.",
            codigoCampo
        );

        return;
    }


    if(parseInt(codigo,10) <= 0){

        mostrarError(
            "El código debe ser mayor a cero.",
            codigoCampo
        );

        return;
    }


    if(nombre === ""){

        mostrarError(
            "Debe ingresar el nombre del concepto.",
            nombreCampo
        );

        return;
    }


    if(categoria === ""){

        mostrarError(
            "Debe seleccionar un tipo de concepto.",
            categoriaCampo
        );

        return;
    }


    if(forma === ""){

        mostrarError(
            "Debe seleccionar una forma de cálculo.",
            formaCampo
        );

        return;
    }


    const codigoNumerico =
        parseInt(
            codigo,
            10
        );

    const codigosCategoria =
        [101, 102, 104];


    if(
        codigosCategoria.includes(
            codigoNumerico
        )
        &&
        forma !== "TABLA_CATEGORIA"
    ){

        mostrarError(
            "Los conceptos 101, 102 y 104 deben utilizar Valor por Categoría.",
            formaCampo
        );

        return;
    }


    if(
        !codigosCategoria.includes(
            codigoNumerico
        )
        &&
        forma === "TABLA_CATEGORIA"
    ){

        mostrarError(
            "Valor por Categoría está reservado para los conceptos 101, 102 y 104.",
            formaCampo
        );

        return;
    }


    if(
        forma === "PORCENTAJE"
        &&
        baseCalculo === ""
    ){

        mostrarError(
            "Debe seleccionar sobre qué base se aplicará el porcentaje del concepto.",
            baseCalculoCampo
        );

        return;
    }


    if(
        forma === "PORCENTAJE"
        &&
        baseCalculo !== "BASICO"
        &&
        baseCalculo !== "BASICO_MAS_DEDICACION"
        &&
        baseCalculo !== "TOTAL_REMUNERATIVO"
        &&
        baseCalculo !== "TOTAL_REMUNERATIVO_MENOS_DESCUENTOS_OBLIGATORIOS"
    ){

        mostrarError(
            "La base seleccionada para el concepto porcentual no es válida.",
            baseCalculoCampo
        );

        return;
    }


    if(
        fechaDesde !== ""
        &&
        fechaHasta !== ""
        &&
        fechaHasta < fechaDesde
    ){

        mostrarError(
            "La fecha hasta no puede ser anterior a la fecha desde.",
            fechaHastaCampo
        );

        return;
    }

});


/*
|--------------------------------------------------------------------------
| EVENTOS
|--------------------------------------------------------------------------
*/

document.addEventListener(
    "DOMContentLoaded",
    function(){

        actualizarConfiguracion();
        actualizarBaseCalculo();

        document
            .getElementById("codigo")
            .addEventListener(
                "input",
                function(){
                    actualizarConfiguracion();
                    actualizarBaseCalculo();
                }
            );

        document
            .getElementById("forma_calculo")
            .addEventListener(
                "change",
                actualizarBaseCalculo
            );
    }
);

</script>

</body>

</html>
