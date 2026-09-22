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

<style>

*{
    box-sizing:border-box;
}

body{
    margin:0;
    font-family:Arial,Helvetica,sans-serif;
    background:#f4f7fb;
    color:#1f2937;
}

.contenedor{
    width:95%;
    max-width:1400px;
    margin:20px auto;
    background:#fff;
    padding:20px;
    border-radius:12px;
    box-shadow:0 4px 14px rgba(0,0,0,.08);
}

.acciones{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
    margin-bottom:18px;
}

.btn{
    display:inline-block;
    text-decoration:none;
    border:none;
    border-radius:8px;
    padding:10px 14px;
    font-size:14px;
    font-weight:bold;
    cursor:pointer;
    color:white;
}

.btn-volver{
    background:#6b7280;
}

.btn-imprimir{
    background:#dc2626;
}

.encabezado{
    border:2px solid #d1d5db;
    border-radius:10px;
    padding:18px;
    margin-bottom:18px;
}

.encabezado h1{
    margin:0 0 6px 0;
    font-size:26px;
    color:#7c3aed;
}

.encabezado p{
    margin:4px 0;
    font-size:14px;
}

.resumen{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:10px;
    margin-top:15px;
}

.info-box{
    border:1px solid #e5e7eb;
    background:#f9fafb;
    border-radius:8px;
    padding:10px 12px;
}

.info-box strong{
    display:block;
    margin-bottom:5px;
    font-size:13px;
    color:#374151;
}

.tabla-contenedor{
    width:100%;
    overflow:visible;
}

table{
    width:100%;
    min-width:0;
    table-layout:fixed;
    border-collapse:collapse;
}

th,
td{
    border:1px solid #d1d5db;
    padding:8px 7px;
    font-size:12px;
    text-align:left;
    vertical-align:middle;
    overflow-wrap:anywhere;
}

th{
    background:#7c3aed;
    color:white;
    white-space:normal;
}

.col-codigo{
    width:8%;
}

.col-nombre{
    width:18%;
}

.col-basico{
    width:16%;
}

.col-dedicacion{
    width:18%;
}

.col-suplemento{
    width:16%;
}

.col-configuracion{
    width:12%;
    text-align:center;
}

.col-estado{
    width:12%;
    text-align:center;
}

.sin-valor{
    display:inline-block;
    padding:4px 7px;
    border-radius:999px;
    background:#f1f5f9;
    color:#64748b;
    font-size:10px;
    font-weight:bold;
    white-space:nowrap;
}

.configuracion-completa,
.configuracion-incompleta{
    display:inline-block;
    padding:4px 7px;
    border-radius:999px;
    font-size:10px;
    font-weight:bold;
    white-space:nowrap;
}

.configuracion-completa{
    background:#dcfce7;
    color:#166534;
}

.configuracion-incompleta{
    background:#fef3c7;
    color:#92400e;
}

.estado-activo,
.estado-inactivo{
    display:inline-block;
    font-weight:bold;
    padding:4px 8px;
    border-radius:999px;
    font-size:11px;
}

.estado-activo{
    background:#dcfce7;
    color:#166534;
}

.estado-inactivo{
    background:#fee2e2;
    color:#991b1b;
}

.sin-registros{
    text-align:center;
    padding:20px;
    color:#6b7280;
    border:1px solid #d1d5db;
    border-radius:8px;
    background:#fafafa;
}

@media print{

    @page{
        size:A4 landscape;
        margin:8mm;
    }

    body{
        background:#fff;
    }

    .acciones{
        display:none;
    }

    .contenedor{
        width:100%;
        max-width:100%;
        margin:0;
        padding:0;
        border-radius:0;
        box-shadow:none;
    }

    .encabezado{
        padding:10px;
        margin-bottom:10px;
        page-break-inside:avoid;
    }

    .encabezado h1{
        font-size:20px;
    }

    .encabezado p{
        font-size:10px;
    }

    .resumen{
        gap:5px;
        margin-top:8px;
    }

    .info-box{
        padding:6px;
        font-size:9px;
    }

    .info-box strong{
        font-size:9px;
    }

    table{
        width:100%;
        min-width:0;
        table-layout:fixed;
    }

    th,
    td{
        padding:5px 4px;
        font-size:9px;
    }

    .estado-activo,
    .estado-inactivo,
    .configuracion-completa,
    .configuracion-incompleta,
    .sin-valor{
        font-size:8px;
        padding:3px 5px;
    }

    tr{
        page-break-inside:avoid;
    }
}

@media(max-width:768px){

    .contenedor{
        width:98%;
        margin:10px auto;
        padding:14px;
    }

    .acciones{
        flex-direction:column;
    }

    .btn{
        width:100%;
        text-align:center;
    }

    .encabezado h1{
        font-size:22px;
    }
}

</style>

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
            onclick="window.print()"
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

</body>

</html>