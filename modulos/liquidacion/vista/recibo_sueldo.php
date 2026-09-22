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

<style>

/* =========================================
   GENERAL
========================================= */

*{
    box-sizing:border-box;
}

body{
    margin:0;
    font-family:Arial,sans-serif;
    background:#eef2f7;
    color:#1f2937;
}


/* =========================================
   CONTENEDOR
========================================= */

.contenedor{
    width:95%;
    max-width:1200px;
    margin:25px auto;
    background:#fff;
    padding:28px;
    border-radius:22px;
    box-shadow:0 12px 35px rgba(0,0,0,.08);
}


/* =========================================
   MENSAJES DEL RECIBO
========================================= */

.mensaje-recibo{
    margin-bottom:20px;
    padding:14px 16px;
    border-radius:12px;
    font-size:14px;
    font-weight:bold;
    text-align:center;
}

.mensaje-recibo-ok{
    background:#dcfce7;
    color:#166534;
    border:1px solid #86efac;
}

.mensaje-recibo-error{
    background:#fee2e2;
    color:#991b1b;
    border:1px solid #fecaca;
}


/* =========================================
   ACCIONES
========================================= */

.acciones{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
    margin-bottom:22px;
}

.btn{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    padding:11px 18px;
    text-decoration:none;
    border-radius:12px;
    font-weight:bold;
    color:#fff;
    transition:.2s;
    font-size:14px;
    border:none;
    cursor:pointer;
    text-align:center;
}

.btn:hover{
    transform:translateY(-1px);
    opacity:.92;
}

.btn-volver{
    background:#6b7280;
}

.btn-imprimir{
    background:#0f766e;
}

.btn-email{
    background:#2563eb;
}


.form-email{
    margin:0;
    padding:0;
}


/* =========================================
   AVISO DE ESTADO
========================================= */

.aviso-estado{
    margin-bottom:20px;
    padding:14px 16px;
    border-radius:12px;
    font-weight:bold;
    text-align:center;
    border:1px solid transparent;
}

.aviso-borrador{
    background:#fef3c7;
    color:#92400e;
    border-color:#fde68a;
}

.aviso-anulada{
    background:#fee2e2;
    color:#991b1b;
    border-color:#fecaca;
}

.aviso-cerrada{
    background:#dcfce7;
    color:#166534;
    border-color:#86efac;
}


/* =========================================
   ENCABEZADO
========================================= */

.encabezado{
    border:1px solid #d1d5db;
    border-radius:20px;
    padding:24px;
    margin-bottom:25px;
    background:linear-gradient(135deg,#ffffff,#f9fafb);
}

.header-flex{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:20px;
    margin-bottom:20px;
}

.titulo{
    flex:1;
    text-align:center;
}

.titulo h1{
    margin:0;
    font-size:32px;
    color:#0f766e;
    letter-spacing:1px;
}

.titulo p{
    margin:6px 0 0;
    font-size:14px;
    color:#4b5563;
}

.logo{
    width:120px;
    min-width:120px;
    text-align:right;
}

.logo img{
    width:105px;
    max-width:100%;
    object-fit:contain;
}


/* =========================================
   DATOS DEL EMPLEADO
========================================= */

.grid-info{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:14px;
}

.info-box{
    border-radius:14px;
    padding:14px;
    background:#f9fafb;
    border:1px solid #e5e7eb;
    transition:.2s;
    word-break:break-word;
}

.info-box strong{
    display:block;
    font-size:12px;
    margin-bottom:6px;
    color:#6b7280;
    text-transform:uppercase;
    letter-spacing:.5px;
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


/* =========================================
   BLOQUES
========================================= */

.bloque{
    margin-top:28px;
}

.bloque h3{
    margin:0 0 12px;
    color:#0f766e;
    font-size:20px;
}


/* =========================================
   TABLAS
========================================= */

.tabla-contenedor{
    width:100%;
    border-radius:16px;
    border:1px solid #e5e7eb;
    overflow:hidden;
}

table{
    width:100%;
    border-collapse:collapse;
    background:white;
}

th,
td{
    padding:13px 12px;
    border-bottom:1px solid #e5e7eb;
    text-align:left;
    font-size:14px;
    vertical-align:middle;
}

th{
    background:#f9fafb;
    color:#374151;
}

tbody tr:hover{
    background:#f8fafc;
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


/* =========================================
   SIN DATOS
========================================= */

.sin-datos{
    text-align:center;
    color:#6b7280;
    padding:18px;
    border:1px dashed #d1d5db;
    border-radius:12px;
    background:#fafafa;
}


/* =========================================
   APORTES PATRONALES
========================================= */

.aporte-info{
    margin-bottom:12px;
    padding:12px 14px;
    background:#f3e8ff;
    color:#6b21a8;
    border:1px solid #e9d5ff;
    border-radius:12px;
    font-size:13px;
    line-height:1.5;
}


/* =========================================
   RESUMEN FINAL
========================================= */

.resumen-final{
    margin-top:30px;
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(190px,1fr));
    gap:14px;
}

.resumen-box{
    border-radius:16px;
    padding:18px;
    border:1px solid #d1d5db;
    background:#f9fafb;
    font-size:22px;
    font-weight:bold;
    word-break:break-word;
}

.resumen-box strong{
    display:block;
    margin-bottom:8px;
    font-size:12px;
    color:#374151;
    text-transform:uppercase;
}

.neto{
    background:linear-gradient(135deg,#dcfce7,#f0fdf4);
    border-color:#86efac;
    color:#166534;
}

.patronal-total{
    background:#faf5ff;
    border-color:#e9d5ff;
    color:#7e22ce;
}


/* =========================================
   FIRMAS
========================================= */

.firmas{
    margin-top:65px;
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:30px;
    align-items:end;
}

.firma-box{
    text-align:center;
    min-height:100px;
}

.linea-firma{
    border-top:2px solid #374151;
    width:85%;
    margin:60px auto 10px;
}

.firma-box p{
    margin:0;
    font-size:13px;
    color:#374151;
    font-weight:bold;
}


/* =========================================
   CELULAR
========================================= */

@media (max-width:768px){

    .contenedor{
        width:98%;
        margin:10px auto;
        padding:15px;
        border-radius:16px;
    }

    .acciones{
        flex-direction:column;
    }

    .btn{
        width:100%;
        padding:12px;
    }

    .form-email{
        width:100%;
    }

    .encabezado{
        padding:15px;
        border-radius:16px;
    }

    .header-flex{
        flex-direction:column-reverse;
        align-items:center;
        gap:12px;
    }

    .titulo{
        text-align:center;
    }

    .titulo h1{
        font-size:24px;
        line-height:1.2;
    }

    .titulo p{
        font-size:13px;
        line-height:1.4;
    }

    .logo{
        width:100%;
        min-width:auto;
        text-align:center;
    }

    .logo img{
        width:85px;
    }

    .grid-info{
        grid-template-columns:1fr;
    }

    .info-box{
        text-align:center;
        padding:12px;
    }

    .bloque h3{
        text-align:center;
        font-size:18px;
    }

    .tabla-contenedor{
        border:none;
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
        background:#fff;
    }

    tbody tr.total-fila{
        background:#f0fdfa;
    }

    td{
        display:grid;
        grid-template-columns:120px 1fr;
        gap:10px;
        text-align:left !important;
        padding:7px 0;
        border-bottom:1px solid #f1f5f9;
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

    .resumen-box{
        text-align:center;
        font-size:21px;
        padding:15px;
    }

    .firmas{
        grid-template-columns:1fr;
        gap:25px;
        margin-top:45px;
    }

    .linea-firma{
        margin-top:45px;
    }
}


/* =========================================
   IMPRESIÓN A4
========================================= */

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


    .acciones,
    .mensaje-recibo{
        display:none !important;
    }


    /*
    |--------------------------------------------------------------------------
    | CONTENEDOR GENERAL
    |--------------------------------------------------------------------------
    */

    .contenedor{
        width:100%;
        max-width:none;
        margin:0;
        padding:0;
        border-radius:0;
        box-shadow:none;
    }


    /*
    |--------------------------------------------------------------------------
    | ESTADO
    |--------------------------------------------------------------------------
    */

    .aviso-estado{
        margin:0 0 4px;
        padding:4px 6px;
        border-radius:4px;
        font-size:8px;
        line-height:1.15;
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
        break-inside:avoid;
        page-break-inside:avoid;
    }


    .header-flex{
        min-height:0;
        margin-bottom:5px;
        gap:6px;
    }


    .titulo h1{
        font-size:16px;
        line-height:1;
        letter-spacing:.5px;
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
        letter-spacing:.15px;
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
        break-inside:avoid;
        page-break-inside:avoid;
    }


    /*
    | En pantalla mostramos "No hay...".
    | En impresión omitimos secciones vacías para ahorrar espacio.
    */

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
    | IMPORTANTE:
    | La hoja impresa puede tener un ancho CSS que también activa el
    | @media (max-width:768px). Ese bloque convierte la tabla en tarjetas
    | verticales. Aquí restauramos explícitamente el comportamiento normal
    | de tabla para impresión A4.
    |
    |--------------------------------------------------------------------------
    */

    .tabla-contenedor{
        overflow:visible;
        border-radius:0;
        border:1px solid #d1d5db;
    }


    table{
        display:table !important;
        width:100% !important;
        border-collapse:collapse;
        table-layout:fixed;
        page-break-inside:auto;
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
    }


    th,
    td{
        display:table-cell !important;
        width:auto !important;
        min-width:0 !important;
        white-space:normal !important;
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


    th,
    td{
        padding:2px 3px;
        border-bottom:1px solid #d1d5db;
        font-size:7.2px;
        line-height:1.05;
    }


    th{
        font-size:6.8px;
    }


    tr{
        page-break-inside:avoid;
        break-inside:avoid;
        page-break-after:auto;
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
        break-inside:avoid;
        page-break-inside:avoid;
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
        break-inside:avoid;
        page-break-inside:avoid;
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
    | RECIBOS CON MUCHOS CONCEPTOS
    |--------------------------------------------------------------------------
    */

    body.recibo-compacto .encabezado{
        padding:4px;
    }


    body.recibo-compacto .info-box{
        padding:3px 4px;
        font-size:7.6px;
    }


    body.recibo-compacto .bloque{
        margin-top:3px;
    }


    body.recibo-compacto th,
    body.recibo-compacto td{
        padding:1.5px 2px;
        font-size:6.7px;
    }


    body.recibo-compacto .resumen-final{
        margin-top:3px;
    }


    body.recibo-compacto .firmas{
        margin-top:14mm;
    }


    body.recibo-muy-compacto .titulo h1{
        font-size:14px;
    }


    body.recibo-muy-compacto .logo img{
        width:36px;
        max-height:36px;
    }


    body.recibo-muy-compacto .info-box{
        padding:2px 3px;
        font-size:7px;
    }


    body.recibo-muy-compacto .info-box strong{
        font-size:6px;
    }


    body.recibo-muy-compacto .bloque{
        margin-top:2px;
    }


    body.recibo-muy-compacto .bloque h3{
        font-size:8px;
        margin-bottom:1px;
    }


    body.recibo-muy-compacto .aporte-info{
        display:none;
    }


    body.recibo-muy-compacto th,
    body.recibo-muy-compacto td{
        padding:1px 2px;
        font-size:6.2px;
    }


    body.recibo-muy-compacto .resumen-box{
        padding:3px 2px;
        font-size:7px;
    }


    body.recibo-muy-compacto .firmas{
        margin-top:8mm;
    }


    body.recibo-muy-compacto .linea-firma{
        margin-top:14px;
    }
}

</style>

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


        <a
            href="#"
            onclick="window.print(); return false;"
            class="btn btn-imprimir"
        >
            Imprimir
        </a>


        <?php if ($esCerrada): ?>

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

</body>

</html>