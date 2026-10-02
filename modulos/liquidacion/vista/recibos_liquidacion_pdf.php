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

<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/liquidacion/recibos_liquidacion_pdf.css'), ENT_QUOTES, 'UTF-8') ?>">
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
        data-event-click="recibos_liquidacion_pdf-1"
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

<script src="<?= htmlspecialchars(sigenmuniUrlAsset('js/liquidacion/recibos_liquidacion_pdf.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
</body>
</html>