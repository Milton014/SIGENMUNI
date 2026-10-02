<?php

require_once __DIR__ . '/../../../core/Url.php';
require_once __DIR__ . '/../../../core/Csrf.php';


/*
|--------------------------------------------------------------------------
| RECIBO DE SUELDO - ROUTER
|--------------------------------------------------------------------------
*/

$reciboLiquidacionId =
    (int)(
        $liquidacionId
        ?? 0
    );


$reciboEmpleadoId =
    (int)(
        $empleadoId
        ?? 0
    );


$reciboOrigen =
    trim(
        (string)(
            $_GET['origen']
            ?? ''
        )
    );


/*
|--------------------------------------------------------------------------
| URL VOLVER
|--------------------------------------------------------------------------
*/

if ($reciboOrigen === 'historial') {

    $reciboUrlVolver =
        sigenmuniUrlRuta(
            'reportes/historial-empleado',
            [
                'empleado_id' =>
                    $reciboEmpleadoId
            ]
        );

} else {

    $reciboUrlVolver =
        sigenmuniUrlRuta(
            'liquidacion/ver',
            [
                'id' =>
                    $reciboLiquidacionId
            ]
        );
}


/*
|--------------------------------------------------------------------------
| ENVÍO POR EMAIL - POST + CSRF
|--------------------------------------------------------------------------
*/

$reciboUrlEmail =
    sigenmuniUrlRuta(
        'liquidacion/recibo/enviar'
    );


$reciboCsrf =
    sigenmuniCsrfToken();

$reciboUrlPdf = sigenmuniUrlRuta('liquidacion/recibo/pdf', [
    'liquidacion_id' => $reciboLiquidacionId,
    'empleado_id' => $reciboEmpleadoId
]);

$reciboUrlImprimir = sigenmuniUrlRuta('liquidacion/recibo/pdf', [
    'liquidacion_id' => $reciboLiquidacionId,
    'empleado_id' => $reciboEmpleadoId,
    'modo' => 'imprimir'
]);


/*
|--------------------------------------------------------------------------
| LOGO
|--------------------------------------------------------------------------
|
| Al abrir el recibo desde /public/index.php una ruta relativa "public/assets/img/escudo.jpg..."
| apuntaría a /public/img. Generamos la URL desde la raíz real del proyecto.
|
|--------------------------------------------------------------------------
*/

$reciboUrlLogo =
    sigenmuniBaseUrl()
    . '/public/assets/img/escudo.jpg';

?>

<?php

if (!function_exists('formatearCuitRecibo')) {

    function formatearCuitRecibo($cuit)
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


/*
|--------------------------------------------------------------------------
| DENSIDAD DE IMPRESIÓN
|--------------------------------------------------------------------------
|
| Para recibos con muchos conceptos reducimos levemente tamaños y espacios.
| Esto ayuda a mantener el recibo dentro de una sola hoja A4 sin afectar
| la vista normal en pantalla.
|
|--------------------------------------------------------------------------
*/

$cantidadConceptosRecibo =
    count($haberesRem ?? [])
    +
    count($haberesNoRem ?? [])
    +
    count($asignaciones ?? [])
    +
    count($descuentos ?? [])
    +
    count($aportesPatronales ?? []);


$claseImpresionRecibo =
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
<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Recibo de Sueldo - SIGENMUNI</title>

<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/liquidacion/recibo_sueldo.css'), ENT_QUOTES, 'UTF-8') ?>">

</head>

<body class="<?php
    echo htmlspecialchars(
        $claseImpresionRecibo,
        ENT_QUOTES,
        'UTF-8'
    );
?>">

<div class="contenedor">

    <?php if (!empty($mensajeRecibo)): ?>

        <div
            class="mensaje-recibo <?php
                echo ($tipoMensajeRecibo === 'ok')
                    ? 'mensaje-recibo-ok'
                    : 'mensaje-recibo-error';
            ?>"
        >

            <?php
            echo htmlspecialchars(
                $mensajeRecibo,
                ENT_QUOTES,
                'UTF-8'
            );
            ?>

        </div>

    <?php endif; ?>


    <!-- =====================================
         ACCIONES
    ====================================== -->

    <?php

    /*
    |--------------------------------------------------------------------------
    | URL DE RETORNO
    |--------------------------------------------------------------------------
    |
    | Preparada al inicio de la vista mediante las rutas centralizadas
    | del Router.
    |
    |--------------------------------------------------------------------------
    */

    $urlVolver =
        $reciboUrlVolver;

    ?>

    <div class="acciones">

        <a
            href="<?php
                echo htmlspecialchars(
                    $urlVolver,
                    ENT_QUOTES,
                    'UTF-8'
                );
            ?>"
            class="btn btn-volver"
        >
            Volver
        </a>


        <?php if ($esCerrada): ?>
            <a
                href="<?= htmlspecialchars($reciboUrlImprimir, ENT_QUOTES, 'UTF-8') ?>"
                target="_blank"
                rel="noopener"
                class="btn btn-imprimir"
                title="Abrir el PDF A4 para imprimir desde el visor"
            >
                Imprimir
            </a>
        <?php else: ?>
            <a href="#" data-event-click="recibo_sueldo-1" class="btn btn-imprimir">
                Imprimir vista previa
            </a>
        <?php endif; ?>


        <?php if ($esCerrada): ?>

            <a href="<?= htmlspecialchars($reciboUrlPdf, ENT_QUOTES, 'UTF-8') ?>" class="btn btn-pdf">
                Descargar PDF
            </a>

            <form
                action="<?php
                    echo htmlspecialchars(
                        $reciboUrlEmail,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                method="POST"
                class="form-email"
            >

                <input
                    type="hidden"
                    name="liquidacion_id"
                    value="<?php
                        echo (int)$reciboLiquidacionId;
                    ?>"
                >

                <input
                    type="hidden"
                    name="empleado_id"
                    value="<?php
                        echo (int)$reciboEmpleadoId;
                    ?>"
                >

                <input
                    type="hidden"
                    name="origen"
                    value="<?php
                        echo htmlspecialchars(
                            $reciboOrigen,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                >

                <input
                    type="hidden"
                    name="_csrf"
                    value="<?php
                        echo htmlspecialchars(
                            $reciboCsrf,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                >

                <button
                    type="submit"
                    class="btn btn-email"
                >
                    Enviar PDF por Email
                </button>

            </form>

        <?php endif; ?>

    </div>


    <!-- =====================================
         AVISO ESTADO
    ====================================== -->

    <?php if ($esBorrador): ?>

        <div class="aviso-estado aviso-borrador">
            VISTA PREVIA - LIQUIDACIÓN EN BORRADOR
        </div>

    <?php elseif ($esAnulada): ?>

        <div class="aviso-estado aviso-anulada">
            LIQUIDACIÓN ANULADA - DOCUMENTO HISTÓRICO SIN VIGENCIA
        </div>

    <?php elseif ($esCerrada): ?>

        <div class="aviso-estado aviso-cerrada">
            LIQUIDACIÓN CERRADA
        </div>

    <?php endif; ?>


    <!-- =====================================
         ENCABEZADO
    ====================================== -->

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
                        $datos['tipo_liquidacion'],
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>
                    |
                    Período:
                    <?php
                    echo htmlspecialchars(
                        $datos['periodo'],
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
                            $reciboUrlLogo,
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

                <strong>
                    Empleado
                </strong>

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

                <strong>
                    DNI
                </strong>

                <?php
                echo htmlspecialchars(
                    $datos['dni'] ?? '-',
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>

            </div>


            <div class="info-box">

                <strong>
                    Legajo
                </strong>

                <?php
                echo htmlspecialchars(
                    $datos['nro_legajo'] ?? '-',
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>

            </div>


            <div class="info-box">

                <strong>
                    Categoría
                </strong>

                <?php

                $categoriaTexto =
                    ($datos['categoria_codigo'] ?? '-')
                    . ' - '
                    . ($datos['categoria_nombre'] ?? '-');

                echo htmlspecialchars(
                    $categoriaTexto,
                    ENT_QUOTES,
                    'UTF-8'
                );

                ?>

            </div>


            <div class="info-box">

                <strong>
                    Fecha de Alta
                </strong>

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

                <strong>
                    Antigüedad
                </strong>

                <?php
                echo htmlspecialchars(
                    $antiguedadTexto,
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>

            </div>


            <div class="info-box">

                <strong>
                    Fecha de Liquidación
                </strong>

                <?php
                echo !empty($datos['fecha_liquidacion'])
                    ? date(
                        'd/m/Y',
                        strtotime($datos['fecha_liquidacion'])
                    )
                    : '-';
                ?>

            </div>


            <div class="info-box">

                <strong>
                    Estado
                </strong>

                <?php
                echo htmlspecialchars(
                    $datos['estado'],
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
                            formatearCuitRecibo(
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


    <?php
    function renderTablaRecibo($items, $tituloTotal, $total) {
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
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td data-label="Código">
                                <?php echo htmlspecialchars($item['codigo'], ENT_QUOTES, 'UTF-8'); ?>
                            </td>

                            <td data-label="Concepto">
                                <?php echo htmlspecialchars($item['nombre'], ENT_QUOTES, 'UTF-8'); ?>
                            </td>

                            <td data-label="Cantidad">
                                <?php echo number_format((float)$item['cantidad'], 2, ',', '.'); ?>
                            </td>

                            <td data-label="%">
                                <?php echo number_format((float)$item['porcentaje_aplicado'], 2, ',', '.'); ?>%
                            </td>

                            <td data-label="Monto">
                                $ <?php echo number_format((float)$item['monto'], 2, ',', '.'); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <tr class="total-fila">
                        <td colspan="4">
                            <?php echo htmlspecialchars($tituloTotal, ENT_QUOTES, 'UTF-8'); ?>
                        </td>
                        <td>
                            $ <?php echo number_format((float)$total, 2, ',', '.'); ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php
    }
    ?>


    <!-- =====================================
         HABERES REMUNERATIVOS
    ====================================== -->

    <div class="bloque <?php echo empty($haberesRem) ? 'bloque-vacio' : ''; ?>">

        <h3>
            Haberes Remunerativos
        </h3>

        <?php if (!empty($haberesRem)): ?>

            <?php
            renderTablaRecibo(
                $haberesRem,
                'Total Haberes Remunerativos',
                $totalHaberesRem
            );
            ?>

        <?php else: ?>

            <div class="sin-datos">
                No hay haberes remunerativos.
            </div>

        <?php endif; ?>

    </div>


    <!-- =====================================
         HABERES NO REMUNERATIVOS
    ====================================== -->

    <div class="bloque <?php echo empty($haberesNoRem) ? 'bloque-vacio' : ''; ?>">

        <h3>
            Haberes No Remunerativos
        </h3>

        <?php if (!empty($haberesNoRem)): ?>

            <?php
            renderTablaRecibo(
                $haberesNoRem,
                'Total Haberes No Remunerativos',
                $totalHaberesNoRem
            );
            ?>

        <?php else: ?>

            <div class="sin-datos">
                No hay haberes no remunerativos.
            </div>

        <?php endif; ?>

    </div>


    <!-- =====================================
         ASIGNACIONES
    ====================================== -->

    <div class="bloque <?php echo empty($asignaciones) ? 'bloque-vacio' : ''; ?>">

        <h3>
            Asignaciones Familiares
        </h3>

        <?php if (!empty($asignaciones)): ?>

            <?php
            renderTablaRecibo(
                $asignaciones,
                'Total Asignaciones',
                $totalAsignaciones
            );
            ?>

        <?php else: ?>

            <div class="sin-datos">
                No hay asignaciones familiares.
            </div>

        <?php endif; ?>

    </div>


    <!-- =====================================
         DESCUENTOS
    ====================================== -->

    <div class="bloque <?php echo empty($descuentos) ? 'bloque-vacio' : ''; ?>">

        <h3>
            Descuentos
        </h3>

        <?php if (!empty($descuentos)): ?>

            <?php
            renderTablaRecibo(
                $descuentos,
                'Total Descuentos',
                $totalDescuentos
            );
            ?>

        <?php else: ?>

            <div class="sin-datos">
                No hay descuentos.
            </div>

        <?php endif; ?>

    </div>


    <!-- =====================================
         APORTES PATRONALES
    ====================================== -->

    <div class="bloque <?php echo empty($aportesPatronales) ? 'bloque-vacio' : ''; ?>">

        <h3>
            Aportes Patronales
        </h3>

        <div class="aporte-info">
            Los aportes patronales son obligaciones a cargo del empleador.
            Se muestran con carácter informativo y no se descuentan del
            Neto a Cobrar del empleado.
        </div>

        <?php if (!empty($aportesPatronales)): ?>

            <?php
            renderTablaRecibo(
                $aportesPatronales,
                'Total Aportes Patronales',
                $totalPatronales
            );
            ?>

        <?php else: ?>

            <div class="sin-datos">
                No hay aportes patronales registrados.
            </div>

        <?php endif; ?>

    </div>


    <!-- =====================================
         RESUMEN FINAL
    ====================================== -->

    <div class="resumen-final">

        <div class="resumen-box">
            <strong>Total Remunerativo</strong>
            $ <?php echo number_format($totalHaberesRem, 2, ',', '.'); ?>
        </div>

        <div class="resumen-box">
            <strong>Total No Remunerativo</strong>
            $ <?php echo number_format($totalHaberesNoRem, 2, ',', '.'); ?>
        </div>

        <div class="resumen-box">
            <strong>Total Asignaciones</strong>
            $ <?php echo number_format($totalAsignaciones, 2, ',', '.'); ?>
        </div>

        <div class="resumen-box">
            <strong>Total Descuentos</strong>
            $ <?php echo number_format($totalDescuentos, 2, ',', '.'); ?>
        </div>

        <div class="resumen-box patronal-total">
            <strong>Aportes Patronales</strong>
            $ <?php echo number_format($totalPatronales, 2, ',', '.'); ?>
        </div>

        <div class="resumen-box neto">
            <strong>Neto a Cobrar</strong>
            $ <?php echo number_format($neto, 2, ',', '.'); ?>
        </div>

    </div>


    <!-- =====================================
         FIRMAS
    ====================================== -->

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

<script src="<?= htmlspecialchars(sigenmuniUrlAsset('js/liquidacion/recibo_sueldo.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
</body>

</html>
