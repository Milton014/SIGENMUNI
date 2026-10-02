<?php

require_once __DIR__ . '/../../../core/Url.php';


/*
|--------------------------------------------------------------------------
| REPORTE DE CATEGORÍAS - ROUTER
|--------------------------------------------------------------------------
*/

$reporteCategoriasUrlReportes =
    sigenmuniUrlRuta(
        'reportes'
    );


$reporteCategoriasUrlMenu =
    sigenmuniUrlArchivo(
        'index.php'
    );


$reporteCategoriasUrlEntrada =
    sigenmuniUrlEntrada();


$reporteCategoriasUrlLimpiar =
    sigenmuniUrlRuta(
        'reportes/categorias'
    );


$reporteCategoriasFiltros = [
    'buscar' =>
        $buscar,

    'activo' =>
        $activo
];


$reporteCategoriasUrlPdf =
    sigenmuniUrlRuta(
        'reportes/categorias/pdf',
        $reporteCategoriasFiltros
    );


$reporteCategoriasUrlExcel =
    sigenmuniUrlRuta(
        'reportes/categorias/excel',
        $reporteCategoriasFiltros
    );

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Reporte de Categorías - SIGENMUNI</title>

<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/reportes/reporte_categorias.css'), ENT_QUOTES, 'UTF-8') ?>">

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
                    Reporte de Categorías
                </h1>

                <p>
                    Consulta de categorías municipales
                    con filtros y exportación.
                </p>

            </div>

            <div class="acciones-superiores">

                <a
                    href="<?php
                    echo htmlspecialchars(
                        $reporteCategoriasUrlReportes,
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
                        $reporteCategoriasUrlMenu,
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
                    $reporteCategoriasUrlEntrada,
                    ENT_QUOTES,
                    'UTF-8'
                );
            ?>"
            class="filtros"
        >

            <input
                type="hidden"
                name="r"
                value="reportes/categorias"
            >

            <div class="campo">

                <label for="buscar">
                    Búsqueda
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

                <label for="activo">
                    Estado
                </label>

                <select
                    name="activo"
                    id="activo"
                >

                    <option value="">
                        Todas
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
                        Activas
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
                        Inactivas
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
                        $reporteCategoriasUrlLimpiar,
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

                Total de categorías encontradas:

                <?php
                echo (int)$totalCategorias;
                ?>

            </div>


            <div class="acciones-exportar">

                <a
                    href="<?php
                        echo htmlspecialchars(
                            $reporteCategoriasUrlPdf,
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
                            $reporteCategoriasUrlExcel,
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


        <?php if (!empty($categorias)): ?>

            <div class="tabla-contenedor">

                <table>

                    <thead>

                        <tr>

                            <th class="col-codigo">
                                Código
                            </th>

                            <th class="col-nombre">
                                Categoría
                            </th>

                            <th class="col-basico">
                                Sueldo Básico
                            </th>

                            <th class="col-dedicacion">
                                Dedicación Funcional
                            </th>

                            <th class="col-suplemento">
                                Suplemento Especial
                            </th>

                            <th class="col-configuracion">
                                Configuración
                            </th>

                            <th class="col-estado">
                                Estado
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php foreach ($categorias as $fila): ?>

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
                                data-label="Categoría"
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
                                data-label="Sueldo Básico"
                                class="col-basico"
                            >

                                <?php if (
                                    array_key_exists(
                                        'sueldo_basico',
                                        $fila
                                    )
                                    &&
                                    $fila['sueldo_basico'] !== null
                                ): ?>

                                    $
                                    <?php
                                    echo number_format(
                                        (float)$fila['sueldo_basico'],
                                        2,
                                        ',',
                                        '.'
                                    );
                                    ?>

                                <?php else: ?>

                                    <span class="sin-valor">
                                        Sin valor vigente
                                    </span>

                                <?php endif; ?>

                            </td>


                            <td
                                data-label="Dedicación Funcional"
                                class="col-dedicacion"
                            >

                                <?php if (
                                    array_key_exists(
                                        'dedicacion_funcional',
                                        $fila
                                    )
                                    &&
                                    $fila['dedicacion_funcional'] !== null
                                ): ?>

                                    $
                                    <?php
                                    echo number_format(
                                        (float)$fila['dedicacion_funcional'],
                                        2,
                                        ',',
                                        '.'
                                    );
                                    ?>

                                <?php else: ?>

                                    <span class="sin-valor">
                                        Sin valor vigente
                                    </span>

                                <?php endif; ?>

                            </td>


                            <td
                                data-label="Suplemento Especial"
                                class="col-suplemento"
                            >

                                <?php if (
                                    array_key_exists(
                                        'suplemento_especial',
                                        $fila
                                    )
                                    &&
                                    $fila['suplemento_especial'] !== null
                                ): ?>

                                    $
                                    <?php
                                    echo number_format(
                                        (float)$fila['suplemento_especial'],
                                        2,
                                        ',',
                                        '.'
                                    );
                                    ?>

                                <?php else: ?>

                                    <span class="sin-valor">
                                        Sin valor vigente
                                    </span>

                                <?php endif; ?>

                            </td>


                            <td
                                data-label="Configuración"
                                class="col-configuracion"
                            >

                                <?php

                                $configuracionCompleta =
                                    isset($fila['configuracion_completa'])
                                        ?
                                        (int)$fila['configuracion_completa'] === 1
                                        :
                                        (
                                            array_key_exists('sueldo_basico', $fila)
                                            &&
                                            $fila['sueldo_basico'] !== null
                                            &&
                                            array_key_exists('dedicacion_funcional', $fila)
                                            &&
                                            $fila['dedicacion_funcional'] !== null
                                            &&
                                            array_key_exists('suplemento_especial', $fila)
                                            &&
                                            $fila['suplemento_especial'] !== null
                                        );

                                ?>

                                <?php if ($configuracionCompleta): ?>

                                    <span class="configuracion-completa">
                                        Completa
                                    </span>

                                <?php else: ?>

                                    <span class="configuracion-incompleta">
                                        Incompleta
                                    </span>

                                <?php endif; ?>

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
                                        Activa
                                    </span>

                                <?php else: ?>

                                    <span class="estado-inactivo">
                                        Inactiva
                                    </span>

                                <?php endif; ?>

                            </td>


                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php else: ?>

            <div class="sin-registros">

                No se encontraron categorías
                con los filtros seleccionados.

            </div>

        <?php endif; ?>

    </div>

</div>

</body>

</html>