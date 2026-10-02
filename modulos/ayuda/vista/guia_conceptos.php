<?php

/*
|--------------------------------------------------------------------------
| NAVEGACIÓN DEL MÓDULO AYUDA
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../../../core/Url.php';


$ayudaUrlVolver =
    sigenmuniUrlRuta(
        'ayuda'
    );

?>
<?php

/*
|--------------------------------------------------------------------------
| FUNCIONES AUXILIARES - GUÍA DE CONCEPTOS
|--------------------------------------------------------------------------
*/

if (!function_exists('guiaConceptosEscapar')) {

    function guiaConceptosEscapar($valor)
    {
        return htmlspecialchars(
            (string)$valor,
            ENT_QUOTES,
            'UTF-8'
        );
    }
}


if (!function_exists('guiaConceptosTipoVisual')) {

    function guiaConceptosTipoVisual($tipo)
    {
        $tipo =
            strtoupper(
                trim(
                    (string)$tipo
                )
            );

        $mapa = [
            'REMUNERATIVO' =>
                'Remunerativo',

            'NO_REMUNERATIVO' =>
                'No remunerativo',

            'ASIGNACION_FAMILIAR' =>
                'Asignación familiar',

            'DESCUENTO' =>
                'Descuento',

            'APORTE_PATRONAL' =>
                'Aporte patronal'
        ];

        return
            $mapa[$tipo]
            ??
            (
                $tipo !== ''
                    ? str_replace('_', ' ', $tipo)
                    : '-'
            );
    }
}


if (!function_exists('guiaConceptosClaseTipo')) {

    function guiaConceptosClaseTipo($tipo)
    {
        $tipo =
            strtoupper(
                trim(
                    (string)$tipo
                )
            );

        $mapa = [
            'REMUNERATIVO' =>
                'etiqueta-remunerativo',

            'NO_REMUNERATIVO' =>
                'etiqueta-no-remunerativo',

            'ASIGNACION_FAMILIAR' =>
                'etiqueta-asignacion',

            'DESCUENTO' =>
                'etiqueta-descuento',

            'APORTE_PATRONAL' =>
                'etiqueta-patronal'
        ];

        return
            $mapa[$tipo]
            ??
            'etiqueta-neutra';
    }
}


if (!function_exists('guiaConceptosFormaVisual')) {

    function guiaConceptosFormaVisual($forma)
    {
        $forma =
            strtoupper(
                trim(
                    (string)$forma
                )
            );

        $mapa = [
            'FORMULA' =>
                'Automático',

            'TABLA_CATEGORIA' =>
                'Valor por categoría',

            'PORCENTAJE' =>
                'Porcentaje',

            'MANUAL' =>
                'Manual',

            'FIJO' =>
                'Manual'
        ];

        return
            $mapa[$forma]
            ??
            (
                $forma !== ''
                    ? str_replace('_', ' ', $forma)
                    : '-'
            );
    }
}


if (!function_exists('guiaConceptosCompactarCodigos')) {

    function guiaConceptosCompactarCodigos(array $codigos)
    {
        $codigos =
            array_values(
                array_unique(
                    array_map(
                        'intval',
                        $codigos
                    )
                )
            );

        sort(
            $codigos,
            SORT_NUMERIC
        );


        if (empty($codigos)) {

            return [];
        }


        $rangos = [];

        $inicio =
            $codigos[0];

        $anterior =
            $codigos[0];


        $cantidad =
            count(
                $codigos
            );


        for (
            $i = 1;
            $i < $cantidad;
            $i++
        ) {

            $actual =
                $codigos[$i];


            if ($actual === $anterior + 1) {

                $anterior =
                    $actual;

                continue;
            }


            $rangos[] =
                $inicio === $anterior
                    ? (string)$inicio
                    : $inicio . '–' . $anterior;


            $inicio =
                $actual;

            $anterior =
                $actual;
        }


        $rangos[] =
            $inicio === $anterior
                ? (string)$inicio
                : $inicio . '–' . $anterior;


        return $rangos;
    }
}

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Guía de Conceptos - SIGENMUNI</title>

<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/ayuda/guia_conceptos.css'), ENT_QUOTES, 'UTF-8') ?>">

</head>

<body>

<!-- =====================================================
     HEADER GLOBAL
====================================================== -->

<?php

$sigenmuniHeaderSubtitulo =
    'Centro de Ayuda - Gestión de Conceptos';

require __DIR__
    . '/../../../componentes/header_sigenmuni.php';

?>


<div class="contenedor">


    <!-- =====================================================
         CABECERA
    ====================================================== -->

    <div class="cabecera">

        <div class="cabecera-top">

            <div>

                <h1>
                    Guía de Gestión de Conceptos
                </h1>

                <p>
                    Instrucciones para crear, editar y configurar
                    conceptos de liquidación y sus valores en SIGENMUNI.
                </p>

            </div>

            <div class="acciones">

                <button
                    type="button"
                    class="btn btn-imprimir"
                    data-event-click="guia_conceptos-1"
                >
                    Imprimir Guía
                </button>

                <a
                    href="<?php
                    echo htmlspecialchars(
                        $ayudaUrlVolver,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                    class="btn btn-volver"
                >
                    Volver a Ayuda
                </a>

            </div>

        </div>

    </div>


    <!-- =====================================================
         ÍNDICE
    ====================================================== -->

    <div class="panel">

        <h2>
            Contenido de la guía
        </h2>

        <p class="panel-subtitulo">
            Seleccione una sección para ir directamente al tema.
        </p>

        <div class="indice">

            <a href="#objetivo">
                Objetivo del módulo
            </a>

            <a href="#codigos">
                Rangos y códigos
            </a>

            <a href="#alta">
                Crear concepto
            </a>

            <a href="#tipo">
                Tipo de Concepto
            </a>

            <a href="#forma">
                Forma de Cálculo
            </a>

            <a href="#valores">
                Valores del Concepto
            </a>

            <a href="#vigencia">
                Vigencia
            </a>

            <a href="#recomendaciones">
                Recomendaciones
            </a>

            <a href="#problemas">
                Problemas frecuentes
            </a>

        </div>

    </div>


    <!-- =====================================================
         OBJETIVO
    ====================================================== -->

    <section
        class="seccion"
        id="objetivo"
    >

        <div class="seccion-header">

            <div class="icono">
                💰
            </div>

            <h2>
                Objetivo del módulo
            </h2>

        </div>

        <p>
            El módulo <strong>Gestión de Conceptos</strong>
            permite administrar los conceptos utilizados
            en las liquidaciones de haberes.
        </p>

        <p>
            Cada concepto define qué representa,
            cómo se clasifica,
            cómo se calcula
            y qué valores utiliza cuando corresponde.
        </p>

        <div class="aviso aviso-info">

            <strong>Importante:</strong>
            no debe confundirse la
            <strong>Categoría salarial del empleado</strong>
            con el <strong>Tipo de Concepto</strong>.
            Son datos diferentes y cumplen funciones distintas.

        </div>

    </section>


    <!-- =====================================================
         RANGOS Y CÓDIGOS DE CONCEPTOS
    ====================================================== -->

    <section
        class="seccion"
        id="codigos"
    >

        <div class="seccion-header">

            <div class="icono">
                🔢
            </div>

            <h2>
                Rangos y códigos de conceptos
            </h2>

        </div>


        <p>
            SIGENMUNI organiza los códigos de conceptos por rangos.
            Antes de crear un concepto nuevo, debe verificarse que el código
            pertenezca al rango correspondiente y que no se encuentre ocupado.
        </p>


        <div class="aviso aviso-importante">

            <strong>Importante:</strong>
            un código existente se considera ocupado aunque el concepto esté
            inactivo. No se recomienda reutilizar códigos históricos.

        </div>


        <?php if (!empty($errorGuiaConceptos ?? '')): ?>

            <div class="aviso aviso-importante">

                No fue posible cargar automáticamente el catálogo de conceptos:

                <strong>
                    <?php
                    echo guiaConceptosEscapar(
                        $errorGuiaConceptos
                    );
                    ?>
                </strong>

            </div>

        <?php endif; ?>


        <?php if (!empty($rangosConceptos ?? [])): ?>

            <div class="rangos-grid">

                <?php foreach ($rangosConceptos as $claveRango => $rango): ?>

                    <?php

                    $codigosDisponiblesCompactados =
                        guiaConceptosCompactarCodigos(
                            $rango['codigos_disponibles']
                            ?? []
                        );

                    ?>

                    <div class="rango-card">

                        <div class="rango-cabecera">

                            <div>

                                <h3>
                                    <?php
                                    echo guiaConceptosEscapar(
                                        $rango['titulo']
                                        ?? 'Rango de conceptos'
                                    );
                                    ?>
                                </h3>

                                <p>
                                    <?php
                                    echo guiaConceptosEscapar(
                                        $rango['descripcion']
                                        ?? ''
                                    );
                                    ?>
                                </p>

                            </div>

                            <div class="rango-codigo">

                                <?php
                                echo (int)(
                                    $rango['desde']
                                    ?? 0
                                );
                                ?>

                                –

                                <?php
                                echo (int)(
                                    $rango['hasta']
                                    ?? 0
                                );
                                ?>

                            </div>

                        </div>


                        <div class="rango-resumen">

                            <div class="rango-resumen-item">

                                <strong>
                                    Códigos utilizados
                                </strong>

                                <span>
                                    <?php
                                    echo (int)(
                                        $rango['cantidad_utilizados']
                                        ?? 0
                                    );
                                    ?>
                                </span>

                            </div>


                            <div class="rango-resumen-item">

                                <strong>
                                    Códigos disponibles
                                </strong>

                                <span>
                                    <?php
                                    echo (int)(
                                        $rango['cantidad_disponibles']
                                        ?? 0
                                    );
                                    ?>
                                </span>

                            </div>


                            <div class="rango-resumen-item">

                                <strong>
                                    Próximo código libre
                                </strong>

                                <span>

                                    <?php if (
                                        isset(
                                            $rango['proximo_codigo_disponible']
                                        )
                                        &&
                                        $rango['proximo_codigo_disponible'] !== null
                                    ): ?>

                                        <?php
                                        echo (int)$rango[
                                            'proximo_codigo_disponible'
                                        ];
                                        ?>

                                    <?php else: ?>

                                        Sin códigos libres

                                    <?php endif; ?>

                                </span>

                            </div>

                        </div>


                        <div class="rango-contenido">

                            <?php if (!empty($rango['conceptos'] ?? [])): ?>

                                <div class="tabla-contenedor">

                                    <table>

                                        <thead>

                                            <tr>
                                                <th>Código</th>
                                                <th>Nombre</th>
                                                <th>Tipo de Concepto</th>
                                                <th>Forma de Cálculo</th>
                                                <th>Estado</th>
                                            </tr>

                                        </thead>

                                        <tbody>

                                            <?php foreach ($rango['conceptos'] as $concepto): ?>

                                                <tr>

                                                    <td class="codigo">
                                                        <?php
                                                        echo (int)(
                                                            $concepto['codigo']
                                                            ?? 0
                                                        );
                                                        ?>
                                                    </td>

                                                    <td>
                                                        <?php
                                                        echo guiaConceptosEscapar(
                                                            $concepto['nombre']
                                                            ?? '-'
                                                        );
                                                        ?>
                                                    </td>

                                                    <td>

                                                        <span
                                                            class="etiqueta <?php
                                                            echo guiaConceptosEscapar(
                                                                guiaConceptosClaseTipo(
                                                                    $concepto['categoria']
                                                                    ?? ''
                                                                )
                                                            );
                                                            ?>"
                                                        >
                                                            <?php
                                                            echo guiaConceptosEscapar(
                                                                guiaConceptosTipoVisual(
                                                                    $concepto['categoria']
                                                                    ?? ''
                                                                )
                                                            );
                                                            ?>
                                                        </span>

                                                    </td>

                                                    <td>
                                                        <?php
                                                        echo guiaConceptosEscapar(
                                                            guiaConceptosFormaVisual(
                                                                $concepto['forma_calculo']
                                                                ?? ''
                                                            )
                                                        );
                                                        ?>
                                                    </td>

                                                    <td>

                                                        <?php if (
                                                            (int)(
                                                                $concepto['activo']
                                                                ?? 0
                                                            ) === 1
                                                        ): ?>

                                                            <span
                                                                class="estado-concepto estado-concepto-activo"
                                                            >
                                                                Activo
                                                            </span>

                                                        <?php else: ?>

                                                            <span
                                                                class="estado-concepto estado-concepto-inactivo"
                                                            >
                                                                Inactivo
                                                            </span>

                                                        <?php endif; ?>

                                                    </td>

                                                </tr>

                                            <?php endforeach; ?>

                                        </tbody>

                                    </table>

                                </div>

                            <?php else: ?>

                                <div class="sin-conceptos-rango">
                                    Actualmente no existen conceptos registrados
                                    dentro de este rango.
                                </div>

                            <?php endif; ?>


                            <div class="codigos-disponibles">

                                <strong>
                                    Códigos disponibles para futuros conceptos
                                </strong>

                                <div class="lista-codigos">

                                    <?php if (!empty($codigosDisponiblesCompactados)): ?>

                                        <?php
                                        echo guiaConceptosEscapar(
                                            implode(
                                                ', ',
                                                $codigosDisponiblesCompactados
                                            )
                                        );
                                        ?>

                                    <?php else: ?>

                                        No quedan códigos libres en este rango.

                                    <?php endif; ?>

                                </div>

                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php else: ?>

            <div class="aviso aviso-alerta">
                No se encontraron datos de rangos para mostrar.
            </div>

        <?php endif; ?>


        <div class="aviso aviso-info">

            <strong>Cómo elegir un código nuevo:</strong>
            primero identifique el Tipo de Concepto, luego ubique el rango
            correspondiente y seleccione uno de los códigos libres indicados.
            Después confirme que la denominación y la Forma de Cálculo sean
            coherentes con la regla que se desea incorporar.

        </div>

    </section>


    <!-- =====================================================
         CREAR CONCEPTO
    ====================================================== -->

    <section
        class="seccion"
        id="alta"
    >

        <div class="seccion-header">

            <div class="icono">
                ➕
            </div>

            <h2>
                Crear un nuevo concepto
            </h2>

        </div>

        <p>
            Para registrar un concepto,
            ingrese a Gestión de Conceptos
            y seleccione la opción de nuevo concepto.
        </p>

        <div class="pasos">

            <div class="paso">

                <div class="numero">
                    1
                </div>

                <div>

                    <strong>
                        Ingresar el código
                    </strong>

                    <p>
                        Consulte primero la sección
                        <strong>Rangos y códigos</strong>,
                        seleccione un código libre dentro del rango
                        correspondiente y evite reutilizar códigos históricos.
                    </p>

                </div>

            </div>


            <div class="paso">

                <div class="numero">
                    2
                </div>

                <div>

                    <strong>
                        Ingresar el nombre
                    </strong>

                    <p>
                        Utilice una denominación clara,
                        por ejemplo Sueldo Básico,
                        Dedicación Funcional
                        o Caja de Previsión.
                    </p>

                </div>

            </div>


            <div class="paso">

                <div class="numero">
                    3
                </div>

                <div>

                    <strong>
                        Seleccionar el Tipo de Concepto
                    </strong>

                    <p>
                        Indique si corresponde a un concepto
                        remunerativo, no remunerativo,
                        asignación familiar,
                        descuento o aporte patronal.
                    </p>

                </div>

            </div>


            <div class="paso">

                <div class="numero">
                    4
                </div>

                <div>

                    <strong>
                        Seleccionar la Forma de Cálculo
                    </strong>

                    <p>
                        Defina de qué manera
                        el sistema debe determinar el importe.
                    </p>

                </div>

            </div>


            <div class="paso">

                <div class="numero">
                    5
                </div>

                <div>

                    <strong>
                        Completar parámetros adicionales
                    </strong>

                    <p>
                        Según la forma de cálculo,
                        complete porcentaje,
                        monto fijo,
                        base de cálculo
                        u otras opciones disponibles.
                    </p>

                </div>

            </div>


            <div class="paso">

                <div class="numero">
                    6
                </div>

                <div>

                    <strong>
                        Guardar el concepto
                    </strong>

                    <p>
                        Revise la configuración
                        y confirme el alta.
                    </p>

                </div>

            </div>

        </div>

        <div class="aviso aviso-ok">

            Una vez creado el concepto,
            puede agregar valores específicos
            desde la sección
            <strong>Valores del Concepto</strong>
            cuando la forma de cálculo lo requiera.

        </div>

    </section>


    <!-- =====================================================
         TIPO DE CONCEPTO
    ====================================================== -->

    <section
        class="seccion"
        id="tipo"
    >

        <div class="seccion-header">

            <div class="icono">
                🧩
            </div>

            <h2>
                Tipo de Concepto
            </h2>

        </div>

        <p>
            El Tipo de Concepto indica
            cómo se clasifica el concepto
            dentro de la liquidación.
        </p>

        <div class="tabla-contenedor">

            <table>

                <thead>

                    <tr>
                        <th>Tipo de Concepto</th>
                        <th>Descripción</th>
                        <th>Ejemplo</th>
                    </tr>

                </thead>

                <tbody>

                    <tr>
                        <td>
                            <span class="etiqueta etiqueta-remunerativo">
                                REMUNERATIVO
                            </span>
                        </td>
                        <td>
                            Conceptos que forman parte
                            de los haberes remunerativos.
                        </td>
                        <td>
                            Sueldo Básico,
                            Dedicación Funcional,
                            Presentismo.
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <span class="etiqueta etiqueta-no-remunerativo">
                                NO REMUNERATIVO
                            </span>
                        </td>
                        <td>
                            Haberes que no integran
                            el total remunerativo.
                        </td>
                        <td>
                            Conceptos no remunerativos
                            definidos por la administración.
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <span class="etiqueta etiqueta-asignacion">
                                ASIGNACIÓN FAMILIAR
                            </span>
                        </td>
                        <td>
                            Importes relacionados
                            con asignaciones familiares.
                        </td>
                        <td>
                            Hijo,
                            Prenatal,
                            Ayuda Escolar.
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <span class="etiqueta etiqueta-descuento">
                                DESCUENTO
                            </span>
                        </td>
                        <td>
                            Conceptos que disminuyen
                            el importe neto del empleado.
                        </td>
                        <td>
                            Caja de Previsión,
                            IASEP,
                            Sepelio.
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <span class="etiqueta etiqueta-patronal">
                                APORTE PATRONAL
                            </span>
                        </td>
                        <td>
                            Aportes a cargo del empleador.
                        </td>
                        <td>
                            Aportes patronales
                            calculados sobre remuneraciones.
                        </td>
                    </tr>

                </tbody>

            </table>

        </div>

        <div class="aviso aviso-alerta">

            <strong>Tipo de Concepto</strong>
            no significa Categoría 1, 2, 19, etc.
            Esas son categorías salariales del empleado.

        </div>

    </section>


    <!-- =====================================================
         FORMA DE CÁLCULO
    ====================================================== -->

    <section
        class="seccion"
        id="forma"
    >

        <div class="seccion-header">

            <div class="icono">
                🧮
            </div>

            <h2>
                Forma de Cálculo
            </h2>

        </div>

        <p>
            La Forma de Cálculo indica
            cómo obtiene el sistema
            el importe de un concepto.
        </p>

        <div class="tabla-contenedor">

            <table>

                <thead>

                    <tr>
                        <th>Forma</th>
                        <th>Uso</th>
                        <th>Observación</th>
                    </tr>

                </thead>

                <tbody>

                    <tr>
                        <td>
                            FIJO
                        </td>
                        <td>
                            Se utiliza cuando el concepto
                            posee un importe fijo.
                        </td>
                        <td>
                            Puede utilizar monto fijo
                            o valores administrados
                            según la configuración.
                        </td>
                    </tr>

                    <tr>
                        <td>
                            PORCENTAJE
                        </td>
                        <td>
                            Calcula un porcentaje
                            sobre una base determinada.
                        </td>
                        <td>
                            Debe definirse correctamente
                            el porcentaje y su base.
                        </td>
                    </tr>

                    <tr>
                        <td>
                            VALOR POR CATEGORÍA
                        </td>
                        <td>
                            Obtiene el importe
                            desde Valores del Concepto
                            según la categoría salarial
                            del empleado.
                        </td>
                        <td>
                            Internamente el sistema puede utilizar
                            el valor técnico
                            <span class="codigo">TABLA_CATEGORIA</span>.
                        </td>
                    </tr>

                    <tr>
                        <td>
                            MANUAL
                        </td>
                        <td>
                            El importe se completa
                            de manera particular
                            cuando la operación lo requiere.
                        </td>
                        <td>
                            Debe utilizarse solamente
                            cuando la configuración del concepto
                            lo permita.
                        </td>
                    </tr>

                    <tr>
                        <td>
                            FÓRMULA
                        </td>
                        <td>
                            El importe se determina
                            mediante la lógica de cálculo
                            programada en el sistema.
                        </td>
                        <td>
                            No debe reemplazarse
                            por un valor fijo sin revisar
                            la regla correspondiente.
                        </td>
                    </tr>

                </tbody>

            </table>

        </div>

        <div class="aviso aviso-importante">

            En pantalla debe mostrarse
            <strong>VALOR POR CATEGORÍA</strong>.
            El identificador técnico
            <strong>TABLA_CATEGORIA</strong>
            se mantiene internamente
            para no alterar la lógica del sistema.

        </div>

    </section>


    <!-- =====================================================
         VALORES DEL CONCEPTO
    ====================================================== -->

    <section
        class="seccion"
        id="valores"
    >

        <div class="seccion-header">

            <div class="icono">
                💵
            </div>

            <h2>
                Valores del Concepto
            </h2>

        </div>

        <p>
            La sección Valores del Concepto
            permite administrar montos o porcentajes
            asociados a un concepto.
        </p>

        <p>
            Un valor puede estar relacionado
            con una categoría salarial,
            un escalafón
            y un período de vigencia.
        </p>

        <h3>
            Ejemplo: concepto 101 - Sueldo Básico
        </h3>

        <div class="tabla-contenedor">

            <table>

                <thead>

                    <tr>
                        <th>Categoría</th>
                        <th>Monto</th>
                        <th>Fecha desde</th>
                        <th>Fecha hasta</th>
                        <th>Estado</th>
                    </tr>

                </thead>

                <tbody>

                    <tr>
                        <td>Categoría 19</td>
                        <td>$ 500.000,00</td>
                        <td>01/01/2026</td>
                        <td>Vigente</td>
                        <td>Activo</td>
                    </tr>

                </tbody>

            </table>

        </div>

        <div class="aviso aviso-info">

            Cuando un empleado pertenece a Categoría 19
            y el concepto utiliza
            <strong>VALOR POR CATEGORÍA</strong>,
            el sistema busca el valor vigente
            correspondiente a esa categoría.

        </div>

        <h3>
            Conceptos principales configurados por categoría
        </h3>

        <ul>
            <li>
                <strong>101 - Sueldo Básico.</strong>
            </li>
            <li>
                <strong>102 - Dedicación Funcional.</strong>
            </li>
            <li>
                <strong>104 - Suplemento Especial.</strong>
            </li>
        </ul>

    </section>


    <!-- =====================================================
         VIGENCIA
    ====================================================== -->

    <section
        class="seccion"
        id="vigencia"
    >

        <div class="seccion-header">

            <div class="icono">
                📅
            </div>

            <h2>
                Vigencia de los valores
            </h2>

        </div>

        <p>
            Los valores pueden tener
            una fecha de inicio
            y una fecha de finalización.
        </p>

        <div class="pasos">

            <div class="paso">

                <div class="numero">
                    1
                </div>

                <div>

                    <strong>
                        Fecha Desde
                    </strong>

                    <p>
                        Indica a partir de qué fecha
                        el valor puede utilizarse.
                    </p>

                </div>

            </div>


            <div class="paso">

                <div class="numero">
                    2
                </div>

                <div>

                    <strong>
                        Fecha Hasta
                    </strong>

                    <p>
                        Indica hasta cuándo
                        resulta válido el valor.
                        Si no tiene fecha hasta,
                        puede permanecer vigente
                        mientras continúe activo.
                    </p>

                </div>

            </div>


            <div class="paso">

                <div class="numero">
                    3
                </div>

                <div>

                    <strong>
                        Estado Activo
                    </strong>

                    <p>
                        El valor debe estar activo
                        para poder ser considerado
                        por el sistema.
                    </p>

                </div>

            </div>

        </div>

        <div class="aviso aviso-importante">

            Que un valor exista en la base
            no significa que esté vigente.
            Debe cumplir simultáneamente
            las condiciones de estado y fechas
            definidas por el sistema.

        </div>

    </section>


    <!-- =====================================================
         EDITAR / ESTADO
    ====================================================== -->

    <section
        class="seccion"
        id="editar"
    >

        <div class="seccion-header">

            <div class="icono">
                ✏️
            </div>

            <h2>
                Editar, activar o inactivar
            </h2>

        </div>

        <p>
            Tanto los conceptos como sus valores
            pueden requerir modificaciones
            a lo largo del tiempo.
        </p>

        <h3>
            Editar concepto
        </h3>

        <p>
            Utilice Editar para actualizar
            nombre, Tipo de Concepto,
            Forma de Cálculo,
            parámetros y otras opciones disponibles.
        </p>

        <h3>
            Editar valor
        </h3>

        <p>
            Utilice la edición del valor
            para modificar monto,
            porcentaje,
            categoría,
            escalafón
            o fechas de vigencia.
        </p>

        <h3>
            Inactivar
        </h3>

        <p>
            La inactivación evita
            que el registro continúe utilizándose
            cuando ya no corresponda,
            sin eliminar su referencia histórica.
        </p>

        <div class="aviso aviso-alerta">

            Antes de modificar un valor vigente
            utilizado en liquidaciones,
            verifique si corresponde editar el registro
            o crear un nuevo valor
            con una nueva fecha de vigencia.

        </div>

    </section>


    <!-- =====================================================
         RECOMENDACIONES
    ====================================================== -->

    <section
        class="seccion"
        id="recomendaciones"
    >

        <div class="seccion-header">

            <div class="icono">
                ✅
            </div>

            <h2>
                Recomendaciones de uso
            </h2>

        </div>

        <ul>

            <li>
                Mantenga códigos de conceptos únicos
                y nombres claros.
            </li>

            <li>
                Seleccione correctamente
                el Tipo de Concepto.
            </li>

            <li>
                Verifique la Forma de Cálculo
                antes de cargar valores.
            </li>

            <li>
                Para valores por categoría,
                controle que la categoría salarial
                seleccionada sea la correcta.
            </li>

            <li>
                Revise las fechas de vigencia
                antes de procesar una liquidación.
            </li>

            <li>
                No utilice $ 0,00
                para representar falta de configuración
                si realmente no existe un valor vigente.
            </li>

            <li>
                Cuando cambien los valores salariales,
                considere crear nuevos registros
                con la fecha desde correspondiente,
                en lugar de sobrescribir información histórica.
            </li>

            <li>
                Después de modificar valores,
                revise el Reporte de Categorías
                para detectar configuraciones incompletas.
            </li>

        </ul>

    </section>


    <!-- =====================================================
         PROBLEMAS FRECUENTES
    ====================================================== -->

    <section
        class="seccion"
        id="problemas"
    >

        <div class="seccion-header">

            <div class="icono">
                ❓
            </div>

            <h2>
                Problemas frecuentes
            </h2>

        </div>


        <details>

            <summary>
                El concepto aparece pero no toma ningún valor
            </summary>

            <p>
                Revise la Forma de Cálculo,
                el estado del concepto,
                el estado del valor,
                la categoría seleccionada
                y las fechas de vigencia.
            </p>

        </details>


        <details>

            <summary>
                El reporte muestra "Sin valor vigente"
            </summary>

            <p>
                Significa que no se encontró
                un valor activo y vigente
                para ese concepto y categoría.
                Revise Valores del Concepto.
            </p>

        </details>


        <details>

            <summary>
                Confundí Categoría con Tipo de Concepto
            </summary>

            <p>
                Categoría corresponde
                a la clasificación salarial del empleado.
                Tipo de Concepto corresponde
                a la clasificación del concepto
                como remunerativo, descuento,
                asignación u otro tipo.
            </p>

        </details>


        <details>

            <summary>
                ¿Por qué se muestra VALOR POR CATEGORÍA y en el código aparece TABLA_CATEGORIA?
            </summary>

            <p>
                VALOR POR CATEGORÍA
                es la denominación visual utilizada
                para facilitar la comprensión del usuario.
                TABLA_CATEGORIA
                es el identificador técnico interno
                que utiliza la lógica del sistema.
            </p>

        </details>


        <details>

            <summary>
                Creé una nueva categoría pero el concepto no tiene importe
            </summary>

            <p>
                Crear una categoría
                no genera automáticamente
                los Valores del Concepto.
                Debe cargar el valor correspondiente
                para esa nueva categoría.
            </p>

        </details>


        <details>

            <summary>
                Hay varios valores para la misma categoría
            </summary>

            <p>
                Revise las fechas de vigencia
                y el estado de cada registro.
                La configuración debe permitir identificar
                cuál es el valor válido
                para la fecha utilizada.
            </p>

        </details>


        <details>

            <summary>
                Cambié un monto pero una liquidación anterior no cambia
            </summary>

            <p>
                Las liquidaciones ya procesadas
                conservan sus propios detalles.
                Los cambios de configuración
                deben utilizarse para las operaciones
                que correspondan según su vigencia.
            </p>

        </details>

    </section>


    <!-- =====================================================
         PIE
    ====================================================== -->

    <div class="pie">

        <?php
        echo htmlspecialchars(
            $sistema['nombre']
            ?? 'SIGENMUNI',
            ENT_QUOTES,
            'UTF-8'
        );
        ?>

        -

        Guía de Gestión de Conceptos

    </div>


</div>

<script src="<?= htmlspecialchars(sigenmuniUrlAsset('js/ayuda/guia_conceptos.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
</body>

</html>
