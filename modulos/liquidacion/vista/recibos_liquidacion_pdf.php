<?php

require_once __DIR__ . '/../../../core/Url.php';


/*
|--------------------------------------------------------------------------
| RECIBOS DE LIQUIDACIÓN - ROUTER
|--------------------------------------------------------------------------
*/

$recibosLoteLiquidacionId =
    (int)(
        $liquidacionId
        ?? 0
    );


$recibosLoteUrlVolver =
    sigenmuniUrlRuta(
        'liquidacion/ver',
        [
            'id' =>
                $recibosLoteLiquidacionId
        ]
    );


/*
|--------------------------------------------------------------------------
| LOGO
|--------------------------------------------------------------------------
|
| No usamos sigenmuniUrlArchivo() porque el escudo es un recurso estático,
| no un punto de entrada PHP.
|
|--------------------------------------------------------------------------
*/

$recibosLoteUrlLogo =
    sigenmuniBaseUrl()
    . '/public/assets/img/escudo.jpg';

?>

<?php

if (!function_exists('formatearCuitReciboLote')) {

    function formatearCuitReciboLote($cuit)
    {
        $cuit =
            preg_replace(
                '/\D+/',
                '',
                (string)$cuit
            );


        if (strlen($cuit) !== 11) {

            return (string)$cuit;
        }


        return
            substr($cuit, 0, 2)
            . '-'
            . substr($cuit, 2, 8)
            . '-'
            . substr($cuit, 10, 1);
    }
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Recibos de Liquidación - SIGENMUNI</title>

<style>

*{
    box-sizing:border-box;
}

body{
    margin:0;
    font-family:Arial,sans-serif;
    background:#f4f7fb;
    color:#1f2937;
}

.acciones-globales{
    width:95%;
    max-width:1200px;
    margin:20px auto 10px;
    display:flex;
    gap:10px;
    flex-wrap:wrap;
}

.btn{
    display:inline-block;
    padding:10px 16px;
    text-decoration:none;
    border-radius:10px;
    font-weight:bold;
    color:#fff;
    border:none;
    cursor:pointer;
    font-size:14px;
}

.btn-volver{
    background:#6b7280;
}


.btn-imprimir{
    background:#0f766e;
}

.aviso-global{
    width:95%;
    max-width:1200px;
    margin:10px auto 20px;
    padding:14px 16px;
    border-radius:12px;
    text-align:center;
    font-weight:bold;
}

.aviso-cerrada{
    background:#dcfce7;
    color:#166534;
    border:1px solid #86efac;
}

.aviso-anulada{
    background:#fee2e2;
    color:#991b1b;
    border:1px solid #fecaca;
}

.contenedor-recibo{
    width:95%;
    max-width:1100px;
    margin:18px auto;
    background:#fff;
    padding:24px;
    border-radius:16px;
    box-shadow:0 4px 14px rgba(0,0,0,.08);
    page-break-after:always;
}

.contenedor-recibo:last-child{
    page-break-after:auto;
}

.encabezado{
    border:2px solid #d1d5db;
    border-radius:12px;
    padding:18px;
    margin-bottom:20px;
}

.header-flex{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:20px;
    margin-bottom:15px;
}

.titulo{
    flex:1;
    text-align:center;
}

.titulo h1{
    margin:0;
    font-size:24px;
    color:#0f766e;
}

.titulo p{
    margin:6px 0 0;
    font-size:14px;
}

.logo{
    width:110px;
    min-width:110px;
    text-align:right;
}

.logo img{
    width:100px;
    max-width:100%;
    object-fit:contain;
}

.grid-info{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:12px;
    margin-top:15px;
}

.info-box{
    border:1px solid #e5e7eb;
    border-radius:9px;
    padding:10px 12px;
    background:#f9fafb;
    word-break:break-word;
}

.info-box strong{
    display:block;
    font-size:13px;
    margin-bottom:4px;
    color:#374151;
}


.info-box-unidad{
    grid-column:span 2;
}


.unidad-nombre{
    font-weight:bold;
}


.unidad-cuit{
    margin-top:4px;
    color:#64748b;
    font-size:12px;
}

.bloque{
    margin-top:20px;
}

.bloque h3{
    margin:0 0 10px;
    color:#0f766e;
}

.tabla-contenedor{
    overflow-x:auto;
}

table{
    width:100%;
    border-collapse:collapse;
    min-width:700px;
}

th,
td{
    border:1px solid #e5e7eb;
    padding:9px;
    font-size:13px;
    text-align:left;
}

th{
    background:#f3f4f6;
}

th:last-child,
td:last-child{
    text-align:right;
}

.total-fila td{
    font-weight:bold;
    background:#f0fdfa;
    color:#0f766e;
}

.sin-datos{
    text-align:center;
    color:#6b7280;
    padding:15px;
    border:1px dashed #d1d5db;
    border-radius:8px;
    background:#fafafa;
}

.aporte-info{
    margin-bottom:10px;
    padding:10px 12px;
    background:#faf5ff;
    color:#7e22ce;
    border:1px solid #e9d5ff;
    border-radius:10px;
    font-size:12px;
}

.resumen-final{
    margin-top:25px;
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
    gap:12px;
}

.resumen-box{
    border-radius:10px;
    padding:15px;
    border:1px solid #d1d5db;
    background:#f9fafb;
    font-weight:bold;
}

.resumen-box strong{
    display:block;
    margin-bottom:6px;
    font-size:12px;
    color:#374151;
    text-transform:uppercase;
}

.neto{
    background:#dcfce7;
    border-color:#86efac;
    color:#166534;
}

.patronal-total{
    background:#faf5ff;
    border-color:#e9d5ff;
    color:#7e22ce;
}

.firmas{
    margin-top:60px;
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:30px;
    align-items:end;
}

.firma-box{
    text-align:center;
    min-height:90px;
}

.linea-firma{
    border-top:1px solid #374151;
    width:85%;
    margin:55px auto 8px;
}

.firma-box p{
    margin:0;
    font-size:13px;
    color:#374151;
}

@media (max-width:768px){

    .acciones-globales{
        flex-direction:column;
    }

    .btn{
        width:100%;
        text-align:center;
    }

    .header-flex{
        flex-direction:column-reverse;
        align-items:center;
    }

    .titulo{
        text-align:center;
    }

    .logo{
        text-align:center;
    }

    .grid-info{
        grid-template-columns:1fr;
    }


    .info-box-unidad{
        grid-column:span 1;
    }

    .tabla-contenedor{
        overflow:visible;
    }

    table,
    thead,
    tbody,
    tr,
    th,
    td{
        display:block;
        width:100%;
        min-width:0;
    }

    thead{
        display:none;
    }

    tbody{
        display:grid;
        gap:12px;
    }

    tbody tr{
        border:1px solid #e5e7eb;
        border-radius:12px;
        padding:12px;
        background:white;
    }

    td{
        display:grid;
        grid-template-columns:120px 1fr;
        gap:10px;
        border:none;
        border-bottom:1px solid #f1f5f9;
        padding:7px 0;
        text-align:left !important;
    }

    td:last-child{
        border-bottom:none;
    }

    td::before{
        content:attr(data-label);
        font-weight:bold;
        color:#475569;
    }

    .total-fila td{
        display:block;
        text-align:center !important;
    }

    .total-fila td::before{
        display:none;
    }

    .resumen-final{
        grid-template-columns:1fr;
    }

    .firmas{
        grid-template-columns:1fr;
        gap:20px;
    }
}

@media print{

    @page{
        size:A4 portrait;
        margin:5mm;
    }


    html,
    body{
        width:100%;
        margin:0;
        padding:0;
        background:#fff;
    }


    body{
        -webkit-print-color-adjust:exact;
        print-color-adjust:exact;
    }


    .acciones-globales,
    .aviso-global{
        display:none !important;
    }


    /*
    |--------------------------------------------------------------------------
    | UN RECIBO POR HOJA A4
    |--------------------------------------------------------------------------
    */

    .contenedor-recibo{
        width:100%;
        max-width:none;
        margin:0;
        padding:0;
        border-radius:0;
        box-shadow:none;

        page-break-after:always;
        break-after:page;
    }


    .contenedor-recibo:last-child{
        page-break-after:auto;
        break-after:auto;
    }


    /*
    |--------------------------------------------------------------------------
    | ENCABEZADO
    |--------------------------------------------------------------------------
    */

    .encabezado{
        margin:0 0 5px;
        padding:6px;
        border:1px solid #9ca3af;
        border-radius:5px;
        background:#fff;

        page-break-inside:avoid;
        break-inside:avoid;
    }


    .header-flex{
        margin-bottom:5px;
        gap:6px;
    }


    .titulo h1{
        font-size:16px;
        line-height:1;
    }


    .titulo p{
        margin:2px 0 0;
        font-size:8px;
        line-height:1.15;
    }


    .logo{
        width:50px;
        min-width:50px;
    }


    .logo img{
        width:42px;
        max-height:42px;
    }


    /*
    |--------------------------------------------------------------------------
    | DATOS DEL EMPLEADO
    |--------------------------------------------------------------------------
    */

    .grid-info{
        grid-template-columns:repeat(4,1fr);
        gap:3px;
        margin-top:4px;
    }


    .info-box{
        min-height:0;
        padding:4px 5px;
        border-radius:4px;
        font-size:8.2px;
        line-height:1.15;
    }


    .info-box strong{
        margin-bottom:2px;
        font-size:6.8px;
        line-height:1.05;
    }


    .info-box-unidad{
        grid-column:span 2;
    }


    .unidad-cuit{
        margin-top:2px;
        font-size:7px;
    }


    /*
    |--------------------------------------------------------------------------
    | BLOQUES
    |--------------------------------------------------------------------------
    */

    .bloque{
        margin-top:5px;

        page-break-inside:avoid;
        break-inside:avoid;
    }


    .bloque-vacio{
        display:none !important;
    }


    .bloque h3{
        margin:0 0 2px;
        font-size:9px;
        line-height:1.1;
    }


    .aporte-info{
        margin:0 0 1px;
        padding:1px 3px;
        border-radius:2px;
        font-size:5.8px;
        line-height:1;
    }


    .sin-datos{
        padding:3px;
        font-size:7px;
    }


    /*
    |--------------------------------------------------------------------------
    | TABLAS
    |--------------------------------------------------------------------------
    |
    | Se restaura explícitamente el display de tabla porque las reglas
    | responsive de celular también pueden activarse en la vista de impresión.
    |
    |--------------------------------------------------------------------------
    */

    .tabla-contenedor{
        overflow:visible !important;
        border-radius:0;
    }


    table{
        display:table !important;
        width:100% !important;
        min-width:0 !important;
        border-collapse:collapse;
        table-layout:fixed;
    }


    thead{
        display:table-header-group !important;
    }


    tbody{
        display:table-row-group !important;
    }


    tr{
        display:table-row !important;
        width:auto !important;
        padding:0 !important;
        border:0 !important;
        box-shadow:none !important;
        background:transparent !important;

        page-break-inside:avoid;
        break-inside:avoid;
    }


    th,
    td{
        display:table-cell !important;
        width:auto !important;
        min-width:0 !important;
        white-space:normal !important;

        padding:2px 3px;
        font-size:7.2px;
        line-height:1.05;

        text-align:left !important;
    }


    td::before{
        display:none !important;
        content:none !important;
    }


    th:nth-child(1),
    td:nth-child(1){
        width:10% !important;
    }


    th:nth-child(2),
    td:nth-child(2){
        width:46% !important;
    }


    th:nth-child(3),
    td:nth-child(3){
        width:12% !important;
        text-align:center !important;
    }


    th:nth-child(4),
    td:nth-child(4){
        width:12% !important;
        text-align:center !important;
    }


    th:nth-child(5),
    td:nth-child(5){
        width:20% !important;
        text-align:right !important;
    }


    .total-fila{
        display:table-row !important;
    }


    .total-fila td{
        display:table-cell !important;
        padding-top:2px;
        padding-bottom:2px;
        font-size:7.2px;
        text-align:left !important;
    }


    .total-fila td:last-child{
        text-align:right !important;
    }


    .total-fila td::before{
        display:none !important;
        content:none !important;
    }


    /*
    |--------------------------------------------------------------------------
    | RESUMEN FINAL
    |--------------------------------------------------------------------------
    */

    .resumen-final{
        margin-top:5px;
        grid-template-columns:repeat(6,1fr);
        gap:3px;

        page-break-inside:avoid;
        break-inside:avoid;
    }


    .resumen-box{
        padding:4px;
        border-radius:4px;
        font-size:8px;
        line-height:1.05;
    }


    .resumen-box strong{
        margin-bottom:2px;
        font-size:6px;
        line-height:1.05;
    }


    /*
    |--------------------------------------------------------------------------
    | FIRMAS
    |--------------------------------------------------------------------------
    */

    .firmas{
        margin-top:24mm;
        grid-template-columns:repeat(3,1fr);
        gap:12px;

        page-break-inside:avoid;
        break-inside:avoid;
    }


    .firma-box{
        min-height:32px;
    }


    .linea-firma{
        width:82%;
        margin:14px auto 3px;
        border-top:1px solid #374151;
    }


    .firma-box p{
        font-size:7px;
        line-height:1;
    }


    /*
    |--------------------------------------------------------------------------
    | RECIBOS CON MÁS CONCEPTOS
    |--------------------------------------------------------------------------
    */

    .contenedor-recibo.recibo-compacto .encabezado{
        padding:4px;
    }


    .contenedor-recibo.recibo-compacto .info-box{
        padding:3px 4px;
        font-size:7.6px;
    }


    .contenedor-recibo.recibo-compacto .bloque{
        margin-top:3px;
    }


    .contenedor-recibo.recibo-compacto th,
    .contenedor-recibo.recibo-compacto td{
        padding:1.5px 2px;
        font-size:6.7px;
    }


    .contenedor-recibo.recibo-compacto .firmas{
        margin-top:14mm;
    }


    .contenedor-recibo.recibo-muy-compacto .titulo h1{
        font-size:14px;
    }


    .contenedor-recibo.recibo-muy-compacto .logo img{
        width:36px;
        max-height:36px;
    }


    .contenedor-recibo.recibo-muy-compacto .info-box{
        padding:2px 3px;
        font-size:7px;
    }


    .contenedor-recibo.recibo-muy-compacto .info-box strong{
        font-size:6px;
    }


    .contenedor-recibo.recibo-muy-compacto .bloque{
        margin-top:2px;
    }


    .contenedor-recibo.recibo-muy-compacto .bloque h3{
        margin-bottom:1px;
        font-size:8px;
    }


    .contenedor-recibo.recibo-muy-compacto .aporte-info{
        display:none;
    }


    .contenedor-recibo.recibo-muy-compacto th,
    .contenedor-recibo.recibo-muy-compacto td{
        padding:1px 2px;
        font-size:6.2px;
    }


    .contenedor-recibo.recibo-muy-compacto .resumen-box{
        padding:3px 2px;
        font-size:7px;
    }


    .contenedor-recibo.recibo-muy-compacto .firmas{
        margin-top:8mm;
    }


    .contenedor-recibo.recibo-muy-compacto .linea-firma{
        margin-top:11px;
    }
}

</style>
</head>

<body>

<div class="acciones-globales">

    <a
        href="<?php
            echo htmlspecialchars(
                $recibosLoteUrlVolver,
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
        onclick="window.print();"
        class="btn btn-imprimir"
    >
        Imprimir todos los recibos
    </button>

</div>


<?php if ($esCerrada): ?>

    <div class="aviso-global aviso-cerrada">
        LIQUIDACIÓN CERRADA - RECIBOS DEFINITIVOS
    </div>

<?php elseif ($esAnulada): ?>

    <div class="aviso-global aviso-anulada">
        LIQUIDACIÓN ANULADA - DOCUMENTOS HISTÓRICOS SIN VIGENCIA
    </div>

<?php endif; ?>


<?php
function renderTablaLote($items, $tituloTotal, $total) {
    ?>
    <div class="tabla-contenedor">
        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Concepto</th>
                    <th>Cantidad</th>
                    <th>%</th>
                    <th>Monto</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($items as $det): ?>

                    <tr>
                        <td data-label="Código">
                            <?php
                            echo htmlspecialchars(
                                $det['codigo'],
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>
                        </td>

                        <td data-label="Concepto">
                            <?php
                            echo htmlspecialchars(
                                $det['nombre'],
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>
                        </td>

                        <td data-label="Cantidad">
                            <?php
                            echo number_format(
                                (float)$det['cantidad'],
                                2,
                                ',',
                                '.'
                            );
                            ?>
                        </td>

                        <td data-label="%">
                            <?php
                            echo number_format(
                                (float)$det['porcentaje_aplicado'],
                                2,
                                ',',
                                '.'
                            );
                            ?>%
                        </td>

                        <td data-label="Monto">
                            $
                            <?php
                            echo number_format(
                                (float)$det['monto'],
                                2,
                                ',',
                                '.'
                            );
                            ?>
                        </td>
                    </tr>

                <?php endforeach; ?>


                <tr class="total-fila">

                    <td colspan="4">
                        <?php
                        echo htmlspecialchars(
                            $tituloTotal,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>
                    </td>

                    <td>
                        $
                        <?php
                        echo number_format(
                            (float)$total,
                            2,
                            ',',
                            '.'
                        );
                        ?>
                    </td>

                </tr>

            </tbody>
        </table>
    </div>
    <?php
}
?>


<?php foreach ($recibos as $item): ?>

    <?php

    $datos =
        $item['datos'];


    $cantidadConceptosRecibo =
        count($item['haberesRem'] ?? [])
        +
        count($item['haberesNoRem'] ?? [])
        +
        count($item['asignaciones'] ?? [])
        +
        count($item['descuentos'] ?? [])
        +
        count($item['aportesPatronales'] ?? []);


    $claseRecibo =
        $cantidadConceptosRecibo >= 18
            ?
            'recibo-muy-compacto'
            :
            (
                $cantidadConceptosRecibo >= 12
                    ?
                    'recibo-compacto'
                    :
                    ''
            );

    ?>

    <div
        class="contenedor-recibo <?php
            echo htmlspecialchars(
                $claseRecibo,
                ENT_QUOTES,
                'UTF-8'
            );
        ?>"
    >

        <div class="encabezado">

            <div class="header-flex">

                <div class="titulo">

                    <h1>
                        RECIBO DE SUELDO
                    </h1>

                    <p>
                        Municipalidad de Fortín Lugones
                    </p>

                    <p>
                        Liquidación:
                        <?php
                        echo htmlspecialchars(
                            $liquidacion['tipo_liquidacion'],
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>
                        |
                        Período:
                        <?php
                        echo htmlspecialchars(
                            $liquidacion['periodo'],
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>
                    </p>

                </div>


                <div class="logo">
                    <img
                        src="<?php
                            echo htmlspecialchars(
                                $recibosLoteUrlLogo,
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                        alt="Escudo Municipal"
                    >
                </div>

            </div>


            <div class="grid-info">

                <div class="info-box">
                    <strong>Empleado</strong>
                    <?php
                    echo htmlspecialchars(
                        $datos['apellido']
                        . ', '
                        . $datos['nombre'],
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>
                </div>


                <div class="info-box">
                    <strong>DNI</strong>
                    <?php
                    echo htmlspecialchars(
                        $datos['dni'] ?? '-',
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>
                </div>


                <div class="info-box">
                    <strong>Legajo</strong>
                    <?php
                    echo htmlspecialchars(
                        $datos['nro_legajo'] ?? '-',
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>
                </div>


                <div class="info-box">
                    <strong>Categoría</strong>
                    <?php
                    echo htmlspecialchars(
                        ($datos['categoria_codigo'] ?? '-')
                        . ' - '
                        . ($datos['categoria_nombre'] ?? '-'),
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>
                </div>


                <div class="info-box">
                    <strong>Fecha de Alta</strong>
                    <?php
                    echo !empty($datos['fecha_alta'])
                        ? date(
                            'd/m/Y',
                            strtotime($datos['fecha_alta'])
                        )
                        : '-';
                    ?>
                </div>


                <div class="info-box">
                    <strong>Antigüedad</strong>
                    <?php
                    echo htmlspecialchars(
                        $item['antiguedadTexto'],
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>
                </div>


                <div class="info-box">
                    <strong>Fecha de Liquidación</strong>
                    <?php
                    echo !empty($liquidacion['fecha_liquidacion'])
                        ? date(
                            'd/m/Y',
                            strtotime($liquidacion['fecha_liquidacion'])
                        )
                        : '-';
                    ?>
                </div>


                <div class="info-box">
                    <strong>Estado</strong>
                    <?php
                    echo htmlspecialchars(
                        $liquidacion['estado'],
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>
                </div>


                <div class="info-box info-box-unidad">

                    <strong>
                        Unidad de Organización
                    </strong>

                    <div class="unidad-nombre">

                        <?php
                        echo htmlspecialchars(
                            $datos['oficina']
                            ?? '-',
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>

                    </div>


                    <?php if (!empty($datos['oficina_cuit'])): ?>

                        <div class="unidad-cuit">

                            CUIT:
                            <?php
                            echo htmlspecialchars(
                                formatearCuitReciboLote(
                                    $datos['oficina_cuit']
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>

                        </div>

                    <?php endif; ?>

                </div>

            </div>

        </div>


        <div class="bloque <?php echo empty($item['haberesRem']) ? 'bloque-vacio' : ''; ?>">

            <h3>
                Haberes Remunerativos
            </h3>

            <?php if (!empty($item['haberesRem'])): ?>

                <?php
                renderTablaLote(
                    $item['haberesRem'],
                    'Total Haberes Remunerativos',
                    $item['totalHaberesRem']
                );
                ?>

            <?php else: ?>

                <div class="sin-datos">
                    No hay haberes remunerativos.
                </div>

            <?php endif; ?>

        </div>


        <div class="bloque <?php echo empty($item['haberesNoRem']) ? 'bloque-vacio' : ''; ?>">

            <h3>
                Haberes No Remunerativos
            </h3>

            <?php if (!empty($item['haberesNoRem'])): ?>

                <?php
                renderTablaLote(
                    $item['haberesNoRem'],
                    'Total Haberes No Remunerativos',
                    $item['totalHaberesNoRem']
                );
                ?>

            <?php else: ?>

                <div class="sin-datos">
                    No hay haberes no remunerativos.
                </div>

            <?php endif; ?>

        </div>


        <div class="bloque <?php echo empty($item['asignaciones']) ? 'bloque-vacio' : ''; ?>">

            <h3>
                Asignaciones Familiares
            </h3>

            <?php if (!empty($item['asignaciones'])): ?>

                <?php
                renderTablaLote(
                    $item['asignaciones'],
                    'Total Asignaciones',
                    $item['totalAsignaciones']
                );
                ?>

            <?php else: ?>

                <div class="sin-datos">
                    No hay asignaciones familiares.
                </div>

            <?php endif; ?>

        </div>


        <div class="bloque <?php echo empty($item['descuentos']) ? 'bloque-vacio' : ''; ?>">

            <h3>
                Descuentos
            </h3>

            <?php if (!empty($item['descuentos'])): ?>

                <?php
                renderTablaLote(
                    $item['descuentos'],
                    'Total Descuentos',
                    $item['totalDescuentos']
                );
                ?>

            <?php else: ?>

                <div class="sin-datos">
                    No hay descuentos.
                </div>

            <?php endif; ?>

        </div>


        <div class="bloque <?php echo empty($item['aportesPatronales']) ? 'bloque-vacio' : ''; ?>">

            <h3>
                Aportes Patronales
            </h3>

            <div class="aporte-info">
                Los aportes patronales son obligaciones a cargo del empleador
                y no afectan el Neto a Cobrar del empleado.
            </div>

            <?php if (!empty($item['aportesPatronales'])): ?>

                <?php
                renderTablaLote(
                    $item['aportesPatronales'],
                    'Total Aportes Patronales',
                    $item['totalPatronales']
                );
                ?>

            <?php else: ?>

                <div class="sin-datos">
                    No hay aportes patronales registrados.
                </div>

            <?php endif; ?>

        </div>


        <div class="resumen-final">

            <div class="resumen-box">
                <strong>Total Remunerativo</strong>
                $
                <?php
                echo number_format(
                    $item['totalHaberesRem'],
                    2,
                    ',',
                    '.'
                );
                ?>
            </div>


            <div class="resumen-box">
                <strong>Total No Remunerativo</strong>
                $
                <?php
                echo number_format(
                    $item['totalHaberesNoRem'],
                    2,
                    ',',
                    '.'
                );
                ?>
            </div>


            <div class="resumen-box">
                <strong>Total Asignaciones</strong>
                $
                <?php
                echo number_format(
                    $item['totalAsignaciones'],
                    2,
                    ',',
                    '.'
                );
                ?>
            </div>


            <div class="resumen-box">
                <strong>Total Descuentos</strong>
                $
                <?php
                echo number_format(
                    $item['totalDescuentos'],
                    2,
                    ',',
                    '.'
                );
                ?>
            </div>


            <div class="resumen-box patronal-total">
                <strong>Aportes Patronales</strong>
                $
                <?php
                echo number_format(
                    $item['totalPatronales'],
                    2,
                    ',',
                    '.'
                );
                ?>
            </div>


            <div class="resumen-box neto">
                <strong>Neto a Cobrar</strong>
                $
                <?php
                echo number_format(
                    $item['neto'],
                    2,
                    ',',
                    '.'
                );
                ?>
            </div>

        </div>


        <div class="firmas">

            <div class="firma-box">
                <div class="linea-firma"></div>
                <p>Firma del Empleado</p>
            </div>

            <div class="firma-box">
                <div class="linea-firma"></div>
                <p>Tesorería</p>
            </div>

            <div class="firma-box">
                <div class="linea-firma"></div>
                <p>Autoridad Municipal</p>
            </div>

        </div>

    </div>

<?php endforeach; ?>

</body>
</html>