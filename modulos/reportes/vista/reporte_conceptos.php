<?php

require_once __DIR__ . '/../../../core/Url.php';


/*
|--------------------------------------------------------------------------
| REPORTE DE CONCEPTOS - ROUTER
|--------------------------------------------------------------------------
*/

$reporteConceptosUrlReportes =
    sigenmuniUrlRuta(
        'reportes'
    );


$reporteConceptosUrlMenu =
    sigenmuniUrlArchivo(
        'index.php'
    );


$reporteConceptosUrlEntrada =
    sigenmuniUrlEntrada();


$reporteConceptosUrlLimpiar =
    sigenmuniUrlRuta(
        'reportes/conceptos'
    );


$reporteConceptosFiltros = [
    'buscar' =>
        $buscar,

    'tipo_concepto' =>
        $tipoConcepto,

    'activo' =>
        $activo
];


$reporteConceptosUrlPdf =
    sigenmuniUrlRuta(
        'reportes/conceptos/pdf',
        $reporteConceptosFiltros
    );


$reporteConceptosUrlExcel =
    sigenmuniUrlRuta(
        'reportes/conceptos/excel',
        $reporteConceptosFiltros
    );

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Reporte de Conceptos - SIGENMUNI</title>

<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/reportes/reporte_conceptos.css'), ENT_QUOTES, 'UTF-8') ?>">

</head>

<body>

<div class="contenedor">


    <!-- =====================================
         CABECERA
    ====================================== -->

    <div class="cabecera">

        <div class="cabecera-top">

            <div>

                <h1>
                    Reporte de Conceptos
                </h1>

                <p>
                    Consulta de conceptos del sistema
                    con filtros y exportación.
                </p>

            </div>

            <div class="acciones-superiores">

                <a
                    href="<?php
                    echo htmlspecialchars(
                        $reporteConceptosUrlReportes,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                    class="btn btn-reportes"
                >
                    Volver a Reportes
                </a>

                <a
                    href="<?php
                    echo htmlspecialchars(
                        $reporteConceptosUrlMenu,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                    class="btn btn-volver"
                >
                    Menú Principal
                </a>

            </div>

        </div>

    </div>


    <!-- =====================================
         ERROR
    ====================================== -->

    <?php if (!empty($error)): ?>

        <div class="error">

            <?php
            echo htmlspecialchars(
                $error,
                ENT_QUOTES,
                'UTF-8'
            );
            ?>

        </div>

    <?php endif; ?>


    <!-- =====================================
         FILTROS
    ====================================== -->

    <div class="panel">

        <h2>
            Filtros de búsqueda
        </h2>

        <form
            method="GET"
            action="<?php
                echo htmlspecialchars(
                    $reporteConceptosUrlEntrada,
                    ENT_QUOTES,
                    'UTF-8'
                );
            ?>"
            class="filtros"
        >

            <input
                type="hidden"
                name="r"
                value="reportes/conceptos"
            >

            <div class="campo">

                <label for="buscar">
                    Búsqueda general
                </label>

                <input
                    type="text"
                    id="buscar"
                    name="buscar"
                    placeholder="Buscar por código o nombre"
                    autocomplete="off"
                    value="<?php
                        echo htmlspecialchars(
                            $buscar,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                >

            </div>


            <div class="campo">

                <label for="tipo_concepto">
                    Tipo de concepto
                </label>

                <select
                    name="tipo_concepto"
                    id="tipo_concepto"
                >

                    <option value="">
                        Todos los tipos
                    </option>

                    <?php foreach ($tiposConcepto as $valorTipo => $textoTipo): ?>

                        <option
                            value="<?php
                                echo htmlspecialchars(
                                    $valorTipo,
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                            ?>"
                            <?php
                            echo (
                                $tipoConcepto === $valorTipo
                            )
                                ?
                                'selected'
                                :
                                '';
                            ?>
                        >
                            <?php
                            echo htmlspecialchars(
                                $textoTipo,
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <div class="campo">

                <label for="activo">
                    Estado
                </label>

                <select
                    name="activo"
                    id="activo"
                >

                    <option value="">
                        Todos
                    </option>

                    <option
                        value="1"
                        <?php
                        echo (
                            $activo === '1'
                        )
                            ?
                            'selected'
                            :
                            '';
                        ?>
                    >
                        Activos
                    </option>

                    <option
                        value="0"
                        <?php
                        echo (
                            $activo === '0'
                        )
                            ?
                            'selected'
                            :
                            '';
                        ?>
                    >
                        Inactivos
                    </option>

                </select>

            </div>


            <button
                type="submit"
                class="btn btn-buscar"
            >
                Buscar
            </button>


            <a
                href="<?php
                    echo htmlspecialchars(
                        $reporteConceptosUrlLimpiar,
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
         RESULTADOS
    ====================================== -->

    <div class="panel">

        <div class="resumen">

            <div class="resumen-box">

                Total de conceptos encontrados:

                <?php
                echo (int)$totalConceptos;
                ?>

            </div>


            <div class="acciones-exportar">

                <a
                    href="<?php
                        echo htmlspecialchars(
                            $reporteConceptosUrlPdf,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                    class="btn btn-pdf"
                >
                    Exportar PDF
                </a>

                <a
                    href="<?php
                        echo htmlspecialchars(
                            $reporteConceptosUrlExcel,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                    class="btn btn-excel"
                >
                    Exportar Excel
                </a>

            </div>

        </div>


        <?php if (!empty($conceptos)): ?>

            <div class="tabla-contenedor">

                <table>

                    <thead>

                        <tr>

                            <th class="col-codigo">
                                Código
                            </th>

                            <th class="col-nombre">
                                Nombre
                            </th>

                            <th class="col-categoria">
                                Tipo de concepto
                            </th>

                            <th class="col-forma">
                                Forma Cálculo
                            </th>

                            <th class="col-porcentaje">
                                %
                            </th>

                            <th class="col-monto">
                                Monto Fijo
                            </th>

                            <th class="col-base">
                                Base Cálculo
                            </th>

                            <th class="col-estado">
                                Estado
                            </th>

                            <th class="col-vigencia">
                                Vigencia
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php foreach ($conceptos as $fila): ?>

                        <?php

                        /*
                        |--------------------------------------------------------------------------
                        | BADGE DE TIPO DE CONCEPTO
                        |--------------------------------------------------------------------------
                        */

                        $badgeClass =
                            'badge-default';


                        switch (
                            $fila['categoria']
                            ?? ''
                        ) {

                            case 'REMUNERATIVO':

                                $badgeClass =
                                    'badge-rem';

                                break;


                            case 'NO_REMUNERATIVO':

                                $badgeClass =
                                    'badge-no-rem';

                                break;


                            case 'ASIGNACION_FAMILIAR':

                                $badgeClass =
                                    'badge-asig';

                                break;


                            case 'DESCUENTO':

                                $badgeClass =
                                    'badge-desc';

                                break;


                            case 'APORTE_PATRONAL':

                                $badgeClass =
                                    'badge-aporte';

                                break;
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | VIGENCIA
                        |--------------------------------------------------------------------------
                        */

                        $desde =
                            !empty(
                                $fila['fecha_desde']
                            )
                                ?
                                date(
                                    "d/m/Y",
                                    strtotime(
                                        $fila['fecha_desde']
                                    )
                                )
                                :
                                '-';


                        $hasta =
                            !empty(
                                $fila['fecha_hasta']
                            )
                                ?
                                date(
                                    "d/m/Y",
                                    strtotime(
                                        $fila['fecha_hasta']
                                    )
                                )
                                :
                                '-';


                        /*
                        |--------------------------------------------------------------------------
                        | FORMA DE CÁLCULO
                        |--------------------------------------------------------------------------
                        |
                        | Se conserva el valor técnico en la base de datos, pero
                        | se muestra una descripción más clara para el usuario.
                        |
                        |--------------------------------------------------------------------------
                        */

                        $formaCalculo =
                            trim(
                                $fila['forma_calculo']
                                ?? ''
                            );


                        switch ($formaCalculo) {

                            case 'TABLA_CATEGORIA':

                                $formaCalculoTexto =
                                    'VALOR POR CATEGORÍA';

                                break;


                            case 'MANUAL':
                            case 'FIJO':

                                $formaCalculoTexto =
                                    'MANUAL';

                                break;


                            case 'PORCENTAJE':

                                $formaCalculoTexto =
                                    'PORCENTAJE';

                                break;


                            case 'FORMULA':

                                $formaCalculoTexto =
                                    'AUTOMÁTICO';

                                break;


                            default:

                                $formaCalculoTexto =
                                    $formaCalculo !== ''
                                        ?
                                        $formaCalculo
                                        :
                                        '-';

                                break;
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | PORCENTAJE
                        |--------------------------------------------------------------------------
                        |
                        | Se muestra únicamente cuando tiene un valor real.
                        |
                        |--------------------------------------------------------------------------
                        */

                        $porcentaje =
                            (float)(
                                $fila['porcentaje']
                                ?? 0
                            );


                        $porcentajeTexto =
                            $porcentaje != 0
                                ?
                                number_format(
                                    $porcentaje,
                                    2,
                                    ',',
                                    '.'
                                )
                                . ' %'
                                :
                                '-';


                        /*
                        |--------------------------------------------------------------------------
                        | MONTO FIJO
                        |--------------------------------------------------------------------------
                        */

                        $montoFijo =
                            (float)(
                                $fila['monto_fijo']
                                ?? 0
                            );


                        $montoFijoTexto =
                            $montoFijo != 0
                                ?
                                '$ '
                                .
                                number_format(
                                    $montoFijo,
                                    2,
                                    ',',
                                    '.'
                                )
                                :
                                '-';


                        /*
                        |--------------------------------------------------------------------------
                        | BASE DE CÁLCULO
                        |--------------------------------------------------------------------------
                        */

                        $baseCalculo =
                            trim(
                                $fila['base_calculo']
                                ?? ''
                            );


                        $baseCalculoTexto =
                            $baseCalculo !== ''
                                ?
                                $baseCalculo
                                :
                                '-';

                        ?>

                        <tr>


                            <td
                                data-label="Código"
                                class="col-codigo"
                            >

                                <?php
                                echo htmlspecialchars(
                                    $fila['codigo']
                                    ?? '',
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </td>


                            <td
                                data-label="Nombre"
                                class="col-nombre"
                            >

                                <?php
                                echo htmlspecialchars(
                                    $fila['nombre']
                                    ?? '',
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </td>


                            <td
                                data-label="Tipo de concepto"
                                class="col-categoria"
                            >

                                <span
                                    class="badge <?php
                                        echo $badgeClass;
                                    ?>"
                                >

                                    <?php
                                    echo htmlspecialchars(
                                        $fila['categoria']
                                        ?? '',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>

                                </span>

                            </td>


                            <td
                                data-label="Forma Cálculo"
                                class="col-forma"
                            >

                                <?php
                                echo htmlspecialchars(
                                    $formaCalculoTexto,
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </td>


                            <td
                                data-label="%"
                                class="col-porcentaje"
                            >

                                <?php
                                echo htmlspecialchars(
                                    $porcentajeTexto,
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </td>


                            <td
                                data-label="Monto Fijo"
                                class="col-monto"
                            >

                                <?php
                                echo htmlspecialchars(
                                    $montoFijoTexto,
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </td>


                            <td
                                data-label="Base Cálculo"
                                class="col-base"
                            >

                                <?php
                                echo htmlspecialchars(
                                    $baseCalculoTexto,
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </td>


                            <td
                                data-label="Estado"
                                class="col-estado"
                            >

                                <?php if (
                                    (int)(
                                        $fila['activo']
                                        ?? 0
                                    )
                                    ===
                                    1
                                ): ?>

                                    <span class="estado-activo">
                                        Activo
                                    </span>

                                <?php else: ?>

                                    <span class="estado-inactivo">
                                        Inactivo
                                    </span>

                                <?php endif; ?>

                            </td>


                            <td
                                data-label="Vigencia"
                                class="col-vigencia"
                            >

                                <?php
                                echo htmlspecialchars(
                                    $desde
                                    .
                                    ' / '
                                    .
                                    $hasta,
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </td>


                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php else: ?>

            <div class="sin-registros">

                No se encontraron conceptos
                con los filtros seleccionados.

            </div>

        <?php endif; ?>

    </div>

</div>

</body>

</html>
