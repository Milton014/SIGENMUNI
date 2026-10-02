<?php

require_once __DIR__ . '/../../../core/Url.php';


/*
|--------------------------------------------------------------------------
| REPORTE DE CATEGORÍAS - IMPRESIÓN / PDF - ROUTER
|--------------------------------------------------------------------------
|
| Los datos ($buscar, $activo, $categorias y $totalCategorias)
| son preparados por ReporteControlador::categoriasPdf().
|
|--------------------------------------------------------------------------
*/

function textoEstadoCategoria($activo)
{
    if ($activo === '1') {
        return 'Activas';
    }

    if ($activo === '0') {
        return 'Inactivas';
    }

    return 'Todas';
}


$reporteCategoriasPdfVolverUrl =
    sigenmuniUrlRuta(
        'reportes/categorias',
        [
            'buscar' =>
                $buscar,

            'activo' =>
                $activo
        ]
    );

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Reporte de Categorías - PDF</title>

<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/reportes/reporte_categorias_pdf.css'), ENT_QUOTES, 'UTF-8') ?>">

</head>

<body>

<div class="contenedor">

    <div class="acciones">

        <a
            href="<?php
                echo htmlspecialchars(
                    $reporteCategoriasPdfVolverUrl,
                    ENT_QUOTES,
                    'UTF-8'
                );
            ?>"
            class="btn btn-volver"
        >
            Volver
        </a>

        <button
            type="button"
            data-event-click="reporte_categorias_pdf-1"
            class="btn btn-imprimir"
        >
            Imprimir / Guardar PDF
        </button>

    </div>

    <div class="encabezado">

        <h1>
            Reporte de Categorías
        </h1>

        <p>
            <strong>SIGENMUNI</strong>
            - Municipalidad de Fortín Lugones
        </p>

        <p>
            Fecha de emisión:
            <?php echo date("d/m/Y H:i:s"); ?>
        </p>

        <div class="resumen">

            <div class="info-box">
                <strong>Búsqueda aplicada</strong>
                <?php
                echo htmlspecialchars(
                    $buscar !== '' ? $buscar : 'Todos',
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>
            </div>

            <div class="info-box">
                <strong>Estado</strong>
                <?php
                echo htmlspecialchars(
                    textoEstadoCategoria($activo),
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>
            </div>

            <div class="info-box">
                <strong>Total de categorías</strong>
                <?php echo (int)$totalCategorias; ?>
            </div>

        </div>

    </div>

    <?php if (!empty($categorias)): ?>

        <div class="tabla-contenedor">

            <table>

                <thead>

                    <tr>
                        <th class="col-codigo">Código</th>
                        <th class="col-nombre">Categoría</th>
                        <th class="col-basico">Sueldo Básico</th>
                        <th class="col-dedicacion">Dedicación Funcional</th>
                        <th class="col-suplemento">Suplemento Especial</th>
                        <th class="col-configuracion">Configuración</th>
                        <th class="col-estado">Estado</th>
                    </tr>

                </thead>

                <tbody>

                <?php foreach ($categorias as $fila): ?>

                    <tr>

                        <td class="col-codigo">
                            <?php
                            echo htmlspecialchars(
                                $fila['codigo'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>
                        </td>

                        <td class="col-nombre">
                            <?php
                            echo htmlspecialchars(
                                $fila['nombre'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>
                        </td>

                        <td class="col-basico">
                            <?php if (
                                array_key_exists('sueldo_basico', $fila)
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

                        <td class="col-dedicacion">
                            <?php if (
                                array_key_exists('dedicacion_funcional', $fila)
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

                        <td class="col-suplemento">
                            <?php if (
                                array_key_exists('suplemento_especial', $fila)
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

                        <td class="col-configuracion">

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

                        <td class="col-estado">

                            <?php if (
                                (int)($fila['activo'] ?? 0) === 1
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

<script src="<?= htmlspecialchars(sigenmuniUrlAsset('js/reportes/reporte_categorias_pdf.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
</body>

</html>