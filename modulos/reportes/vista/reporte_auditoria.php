<?php

require_once __DIR__ . '/../../../core/Url.php';


/*
|--------------------------------------------------------------------------
| REPORTE DE AUDITORÍA - ROUTER
|--------------------------------------------------------------------------
*/

$auditoriaUrlReportes =
    sigenmuniUrlRuta(
        'reportes'
    );


$auditoriaUrlEntrada =
    sigenmuniUrlEntrada();


$auditoriaUrlLimpiar =
    sigenmuniUrlRuta(
        'reportes/auditoria'
    );


$auditoriaFiltros = [
    'fecha_desde' =>
        $fechaDesde,

    'fecha_hasta' =>
        $fechaHasta,

    'usuario' =>
        $usuario,

    'rol' =>
        $rol,

    'modulo' =>
        $modulo,

    'accion' =>
        $accion,

    'entidad' =>
        $entidad
];


$auditoriaUrlPdf =
    sigenmuniUrlRuta(
        'reportes/auditoria/pdf',
        $auditoriaFiltros
    );



/*
|--------------------------------------------------------------------------
| VISTA - REPORTE DE AUDITORÍA
|--------------------------------------------------------------------------
|
| Variables recibidas desde ReporteControlador::auditoria():
|
| $fechaDesde
| $fechaHasta
| $usuario
| $rol
| $modulo
| $accion
| $entidad
| $error
| $auditorias
| $totalAuditorias
| $usuariosAuditoria
| $rolesAuditoria
| $modulosAuditoria
| $accionesAuditoria
| $entidadesAuditoria
| $auditoriaDetalle
| $queryString
|
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| FUNCIONES DE APOYO DE LA VISTA
|--------------------------------------------------------------------------
*/

if (!function_exists('auditoriaEscapar')) {

    function auditoriaEscapar($valor)
    {
        return htmlspecialchars(
            (string)$valor,
            ENT_QUOTES,
            'UTF-8'
        );
    }
}


if (!function_exists('auditoriaFechaHora')) {

    function auditoriaFechaHora($fechaHora)
    {
        if (empty($fechaHora)) {

            return '-';
        }


        $timestamp =
            strtotime(
                $fechaHora
            );


        if ($timestamp === false) {

            return $fechaHora;
        }


        return date(
            'd/m/Y H:i:s',
            $timestamp
        );
    }
}


if (!function_exists('auditoriaEtiquetaCampo')) {

    function auditoriaEtiquetaCampo($campo)
    {
        $etiquetas = [

            'nro_legajo' =>
                'N.º de legajo',

            'apellido' =>
                'Apellido',

            'nombre' =>
                'Nombre',

            'institucion_id' =>
                'Institución ID',

            'institucion' =>
                'Institución',

            'oficina_id' =>
                'Unidad de Organización ID',

            'unidad_organizacion' =>
                'Unidad de Organización',

            'unidad_organizacion_cuit' =>
                'CUIT',

            'situacion_id' =>
                'Situación ID',

            'situacion' =>
                'Situación',

            'escalafon_id' =>
                'Escalafón ID',

            'escalafon' =>
                'Escalafón',

            'categoria_id' =>
                'Categoría ID',

            'categoria' =>
                'Categoría',

            'fecha_alta' =>
                'Fecha de alta',

            'fecha_baja' =>
                'Fecha de baja',

            'estado' =>
                'Estado'
        ];


        if (isset($etiquetas[$campo])) {

            return $etiquetas[$campo];
        }


        return ucfirst(
            str_replace(
                '_',
                ' ',
                (string)$campo
            )
        );
    }
}


if (!function_exists('auditoriaDecodificarDatos')) {

    function auditoriaDecodificarDatos($json)
    {
        if (
            $json === null
            ||
            trim(
                (string)$json
            )
            === ''
        ) {

            return [];
        }


        $datos =
            json_decode(
                $json,
                true
            );


        if (
            json_last_error()
            ===
            JSON_ERROR_NONE
            &&
            is_array(
                $datos
            )
        ) {

            return $datos;
        }


        return [
            'Información' =>
                $json
        ];
    }
}


if (!function_exists('auditoriaPrepararDatosDetalle')) {

    function auditoriaPrepararDatosDetalle(array $datos)
    {
        /*
        |--------------------------------------------------------------------------
        | OCULTAR IDs CUANDO EXISTE UNA DESCRIPCIÓN
        |--------------------------------------------------------------------------
        |
        | Los registros nuevos de auditoría guardan tanto el ID técnico como
        | una fotografía descriptiva del dato. Para que el reporte sea legible,
        | si existe el nombre correspondiente ocultamos el ID.
        |
        | Los registros antiguos siguen siendo compatibles: si no existe la
        | descripción, el ID se conserva y continúa mostrándose.
        |
        |--------------------------------------------------------------------------
        */

        $relaciones = [

            'institucion_id' =>
                'institucion',

            'oficina_id' =>
                'unidad_organizacion',

            'situacion_id' =>
                'situacion',

            'escalafon_id' =>
                'escalafon',

            'categoria_id' =>
                'categoria'
        ];


        foreach (
            $relaciones
            as
            $campoId => $campoDescripcion
        ) {

            if (
                array_key_exists(
                    $campoDescripcion,
                    $datos
                )
                &&
                $datos[$campoDescripcion] !== null
                &&
                $datos[$campoDescripcion] !== ''
            ) {

                unset(
                    $datos[$campoId]
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | ORDEN DE PRESENTACIÓN
        |--------------------------------------------------------------------------
        */

        $orden = [

            'nro_legajo',
            'apellido',
            'nombre',

            'institucion',
            'institucion_id',

            'unidad_organizacion',
            'unidad_organizacion_cuit',
            'oficina_id',

            'situacion',
            'situacion_id',

            'escalafon',
            'escalafon_id',

            'categoria',
            'categoria_id',

            'fecha_alta',
            'fecha_baja',

            'estado'
        ];


        $ordenados = [];


        foreach ($orden as $campo) {

            if (
                array_key_exists(
                    $campo,
                    $datos
                )
            ) {

                $ordenados[$campo] =
                    $datos[$campo];
            }
        }


        /*
        |--------------------------------------------------------------------------
        | CONSERVAR CAMPOS ADICIONALES
        |--------------------------------------------------------------------------
        */

        foreach (
            $datos
            as
            $campo => $valor
        ) {

            if (
                !array_key_exists(
                    $campo,
                    $ordenados
                )
            ) {

                $ordenados[$campo] =
                    $valor;
            }
        }


        return $ordenados;
    }
}


if (!function_exists('auditoriaValorMostrar')) {

    function auditoriaValorMostrar($valor)
    {
        if ($valor === null) {

            return '—';
        }


        if ($valor === '') {

            return '—';
        }


        if (is_bool($valor)) {

            return $valor
                ?
                'Sí'
                :
                'No';
        }


        if (is_array($valor)) {

            return json_encode(
                $valor,
                JSON_UNESCAPED_UNICODE
                |
                JSON_UNESCAPED_SLASHES
            );
        }


        return (string)$valor;
    }
}


if (!function_exists('auditoriaClaseAccion')) {

    function auditoriaClaseAccion($accion)
    {
        $accion =
            strtoupper(
                trim(
                    (string)$accion
                )
            );


        switch ($accion) {

            case 'ALTA':
                return 'accion-alta';

            case 'EDICION':
                return 'accion-edicion';

            case 'ACTIVACION':
                return 'accion-activacion';

            case 'INACTIVACION':
                return 'accion-inactivacion';

            case 'BAJA':
                return 'accion-baja';

            default:
                return 'accion-otra';
        }
    }
}


/*
|--------------------------------------------------------------------------
| URL DEL DETALLE
|--------------------------------------------------------------------------
*/

$filtrosDetalle = [

    'fecha_desde' =>
        $fechaDesde,

    'fecha_hasta' =>
        $fechaHasta,

    'usuario' =>
        $usuario,

    'rol' =>
        $rol,

    'modulo' =>
        $modulo,

    'accion' =>
        $accion,

    'entidad' =>
        $entidad
];


$filtrosDetalleLimpios = [];


foreach (
    $filtrosDetalle
    as
    $clave => $valor
) {

    if ($valor !== '') {

        $filtrosDetalleLimpios[$clave] =
            $valor;
    }
}


$queryFiltros =
    http_build_query(
        $filtrosDetalleLimpios
    );


$urlCerrarDetalle =
    sigenmuniUrlRuta(
        'reportes/auditoria',
        $filtrosDetalleLimpios
    );


$datosAnterioresDetalle = [];
$datosNuevosDetalle = [];


if ($auditoriaDetalle) {

    $datosAnterioresDetalle =
        auditoriaDecodificarDatos(
            $auditoriaDetalle['datos_anteriores']
            ??
            null
        );


    $datosNuevosDetalle =
        auditoriaDecodificarDatos(
            $auditoriaDetalle['datos_nuevos']
            ??
            null
        );


    $datosAnterioresDetalle =
        auditoriaPrepararDatosDetalle(
            $datosAnterioresDetalle
        );


    $datosNuevosDetalle =
        auditoriaPrepararDatosDetalle(
            $datosNuevosDetalle
        );
}

?>
<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>
    SIGENMUNI - Reporte de Auditoría
</title>


<style>

*{
    box-sizing:border-box;
    margin:0;
    padding:0;
}


:root{
    --principal:#4f46e5;
    --principal-hover:#4338ca;
    --secundario:#6366f1;
    --fondo:#f4f7fb;
    --blanco:#ffffff;
    --texto:#1f2937;
    --gris:#6b7280;
    --borde:#e5e7eb;
    --sombra:rgba(0,0,0,.10);

    --verde:#16a34a;
    --verde-suave:#dcfce7;

    --azul:#2563eb;
    --azul-suave:#dbeafe;

    --naranja:#ea580c;
    --naranja-suave:#ffedd5;

    --rojo:#dc2626;
    --rojo-suave:#fee2e2;

    --gris-accion:#475569;
    --gris-accion-suave:#e2e8f0;
}


body{
    font-family:
        Arial,
        Helvetica,
        sans-serif;

    background:
        var(--fondo);

    color:
        var(--texto);

    min-height:100vh;
}


/* =========================================
   CONTENEDOR
========================================= */

.contenedor{
    width:94%;

    max-width:1500px;

    margin:
        28px auto 40px;
}


/* =========================================
   CABECERA DEL REPORTE
========================================= */

.panel-superior{
    display:flex;

    justify-content:
        space-between;

    align-items:center;

    gap:16px;

    flex-wrap:wrap;

    margin-bottom:22px;
}


.titulo-reporte h2{
    font-size:28px;

    margin-bottom:6px;
}


.titulo-reporte p{
    color:
        var(--gris);

    line-height:1.5;
}


.acciones-superiores{
    display:flex;

    gap:10px;

    flex-wrap:wrap;
}


.btn{
    display:inline-flex;

    align-items:center;

    justify-content:center;

    min-height:42px;

    padding:
        10px 16px;

    border:0;

    border-radius:10px;

    text-decoration:none;

    font-weight:bold;

    cursor:pointer;

    transition:.2s ease;

    font-size:14px;

    font-family:inherit;
}


.btn:hover{
    transform:
        translateY(-1px);
}


.btn-volver{
    background:#374151;

    color:white;
}


.btn-volver:hover{
    background:#1f2937;
}


.btn-imprimir{
    background:
        var(--principal);

    color:white;
}


.btn-imprimir:hover{
    background:
        var(--principal-hover);
}


.btn-pdf{
    background:#dc2626;

    color:white;
}


.btn-pdf:hover{
    background:#b91c1c;
}


/* =========================================
   FILTROS
========================================= */

.panel{
    background:
        var(--blanco);

    border:
        1px solid
        var(--borde);

    border-radius:18px;

    box-shadow:
        0 8px 20px
        var(--sombra);

    padding:22px;

    margin-bottom:22px;
}


.panel-titulo{
    display:flex;

    align-items:center;

    gap:10px;

    margin-bottom:18px;
}


.panel-titulo .icono-panel{
    width:40px;
    height:40px;

    border-radius:12px;

    display:flex;

    align-items:center;

    justify-content:center;

    background:#eef2ff;

    font-size:20px;
}


.panel-titulo h3{
    font-size:19px;
}


.filtros-grid{
    display:grid;

    grid-template-columns:
        repeat(
            4,
            minmax(180px,1fr)
        );

    gap:16px;
}


.campo label{
    display:block;

    margin-bottom:7px;

    font-weight:bold;

    font-size:13px;
}


.campo input,
.campo select{
    width:100%;

    min-height:44px;

    padding:
        10px 12px;

    border:
        1px solid #cbd5e1;

    border-radius:10px;

    background:white;

    color:
        var(--texto);

    font-size:14px;

    outline:none;
}


.campo input:focus,
.campo select:focus{
    border-color:
        var(--secundario);

    box-shadow:
        0 0 0 3px
        rgba(99,102,241,.14);
}


.acciones-filtros{
    display:flex;

    gap:10px;

    flex-wrap:wrap;

    margin-top:18px;
}


.btn-buscar{
    background:
        var(--principal);

    color:white;
}


.btn-buscar:hover{
    background:
        var(--principal-hover);
}


.btn-limpiar{
    background:#e5e7eb;

    color:#374151;
}


.btn-limpiar:hover{
    background:#d1d5db;
}


/* =========================================
   MENSAJES
========================================= */

.mensaje-error{
    background:#fee2e2;

    color:#991b1b;

    border:
        1px solid #fecaca;

    padding:14px 16px;

    border-radius:12px;

    margin-bottom:20px;

    font-weight:bold;
}


/* =========================================
   RESUMEN
========================================= */

.resumen{
    display:flex;

    justify-content:
        space-between;

    align-items:center;

    gap:16px;

    flex-wrap:wrap;

    margin-bottom:16px;
}


.total-box{
    background:#eef2ff;

    color:#3730a3;

    border:
        1px solid #c7d2fe;

    padding:
        10px 14px;

    border-radius:12px;

    font-weight:bold;
}


.leyenda{
    color:
        var(--gris);

    font-size:13px;
}


/* =========================================
   TABLA
========================================= */

.tabla-contenedor{
    width:100%;

    overflow:hidden;

    border:
        1px solid
        var(--borde);

    border-radius:14px;
}


table{
    width:100%;

    border-collapse:
        collapse;

    table-layout:fixed;

    min-width:0;

    background:white;
}


thead th{
    background:#eef2ff;

    color:#312e81;

    font-size:12px;

    text-transform:uppercase;

    letter-spacing:.3px;

    padding:
        12px 10px;

    border-bottom:
        1px solid
        #c7d2fe;

    text-align:left;

    white-space:normal;

    overflow-wrap:anywhere;

    word-break:break-word;
}


tbody td{
    padding:
        12px 10px;

    border-bottom:
        1px solid
        var(--borde);

    font-size:12px;

    vertical-align:top;

    overflow-wrap:anywhere;

    word-break:break-word;
}


tbody tr:hover{
    background:#fafafa;
}


tbody tr:last-child td{
    border-bottom:0;
}


.usuario-principal{
    font-weight:bold;

    color:#111827;

    margin-bottom:3px;
}


.usuario-secundario{
    color:
        var(--gris);

    font-size:12px;
}


.registro-principal{
    font-weight:bold;

    margin-bottom:3px;
}


.registro-secundario{
    color:
        var(--gris);

    font-size:12px;
}


.detalle-texto{
    max-width:none;

    line-height:1.4;

    overflow-wrap:anywhere;

    word-break:break-word;
}


.badge{
    display:inline-block;

    padding:
        6px 9px;

    border-radius:999px;

    font-size:11px;

    font-weight:bold;

    white-space:nowrap;
}


.accion-alta{
    background:
        var(--verde-suave);

    color:#166534;
}


.accion-edicion{
    background:
        var(--azul-suave);

    color:#1d4ed8;
}


.accion-activacion{
    background:#d1fae5;

    color:#047857;
}


.accion-inactivacion,
.accion-baja{
    background:
        var(--rojo-suave);

    color:#991b1b;
}


.accion-otra{
    background:
        var(--gris-accion-suave);

    color:
        var(--gris-accion);
}


.btn-detalle{
    background:
        var(--principal);

    color:white;

    width:100%;

    padding:
        7px 5px;

    font-size:11px;

    border-radius:8px;

    text-decoration:none;

    display:inline-block;

    text-align:center;

    white-space:normal;

    overflow-wrap:anywhere;
}


.btn-detalle:hover{
    background:
        var(--principal-hover);
}



/* =========================================
   ANCHOS DE COLUMNAS - SIN SCROLL HORIZONTAL
========================================= */

/*
|--------------------------------------------------------------------------
| LISTADO PRINCIPAL SIN IP
|--------------------------------------------------------------------------
|
| La dirección IP se conserva en el registro de auditoría y continúa visible
| dentro de "Ver detalle", pero no se muestra en la tabla principal.
|
| Esto libera espacio para Registro afectado y Detalle.
|
|--------------------------------------------------------------------------
*/

table th:nth-child(1),
table td:nth-child(1){
    width:11%;
}

table th:nth-child(2),
table td:nth-child(2){
    width:12%;
}

table th:nth-child(3),
table td:nth-child(3){
    width:7%;
}

table th:nth-child(4),
table td:nth-child(4){
    width:13%;
}

table th:nth-child(5),
table td:nth-child(5){
    width:9%;
}

table th:nth-child(6),
table td:nth-child(6){
    width:20%;
}

table th:nth-child(7),
table td:nth-child(7){
    width:22%;
}

table th:nth-child(8),
table td:nth-child(8){
    width:6%;
}


.sin-registros{
    text-align:center;

    padding:
        35px 20px;

    color:
        var(--gris);

    background:#fafafa;

    border:
        1px dashed #cbd5e1;

    border-radius:14px;
}


/* =========================================
   DETALLE DE AUDITORÍA
========================================= */

.detalle-auditoria{
    margin-top:22px;

    border:
        2px solid
        #c7d2fe;
}


.detalle-cabecera{
    display:flex;

    justify-content:
        space-between;

    align-items:flex-start;

    gap:15px;

    flex-wrap:wrap;

    padding-bottom:18px;

    margin-bottom:18px;

    border-bottom:
        1px solid
        var(--borde);
}


.detalle-cabecera h3{
    font-size:21px;

    margin-bottom:5px;
}


.detalle-cabecera p{
    color:
        var(--gris);
}


.btn-cerrar{
    background:#e5e7eb;

    color:#374151;
}


.detalle-meta{
    display:grid;

    grid-template-columns:
        repeat(
            4,
            minmax(170px,1fr)
        );

    gap:12px;

    margin-bottom:20px;
}


.meta-item{
    background:#f8fafc;

    border:
        1px solid
        var(--borde);

    border-radius:12px;

    padding:12px;
}


.meta-item .titulo{
    color:
        var(--gris);

    font-size:11px;

    font-weight:bold;

    text-transform:uppercase;

    margin-bottom:5px;
}


.meta-item .valor{
    font-size:13px;

    font-weight:bold;

    overflow-wrap:anywhere;
}


.detalle-descripcion{
    background:#f8fafc;

    border:
        1px solid
        var(--borde);

    border-radius:12px;

    padding:14px;

    margin-bottom:20px;

    line-height:1.5;
}


.comparacion-grid{
    display:grid;

    grid-template-columns:
        repeat(
            2,
            minmax(280px,1fr)
        );

    gap:18px;
}


.bloque-datos{
    border:
        1px solid
        var(--borde);

    border-radius:14px;

    overflow:hidden;
}


.bloque-datos h4{
    padding:
        12px 14px;

    background:#f8fafc;

    border-bottom:
        1px solid
        var(--borde);

    font-size:15px;
}


.bloque-datos table{
    min-width:0;

    table-layout:fixed;
}


.bloque-datos th{
    background:white;

    color:
        var(--gris);

    text-transform:none;

    letter-spacing:0;

    width:42%;
}


.bloque-datos th,
.bloque-datos td{
    padding:
        10px 12px;

    border-bottom:
        1px solid
        var(--borde);

    font-size:13px;

    overflow-wrap:anywhere;
}


.bloque-datos td{
    color:#111827;
}


.bloque-datos tr:last-child th,
.bloque-datos tr:last-child td{
    border-bottom:0;
}


.vacio-datos{
    padding:20px;

    text-align:center;

    color:
        var(--gris);

    font-size:13px;
}


/* =========================================
   FOOTER
========================================= */

.footer{
    text-align:center;

    color:
        var(--gris);

    font-size:12px;

    padding:
        8px 20px 32px;
}


/* =========================================
   IMPRESIÓN
========================================= */

@media print{

    @page{
        size:A4 landscape;
        margin:8mm;
    }


    body{
        background:white;
    }


    .sigenmuni-global-header,
    .acciones-superiores,
    .panel-filtros,
    .col-accion,
    .detalle-auditoria,
    .footer{
        display:none !important;
    }


    .contenedor{
        width:100%;

        max-width:none;

        margin:0;
    }


    .panel{
        box-shadow:none;

        border:0;

        padding:0;
    }


    .panel-superior{
        margin-bottom:12px;
    }


    .titulo-reporte h2{
        font-size:20px;
    }


    .titulo-reporte p{
        font-size:10px;
    }


    .tabla-contenedor{
        overflow:visible;

        border:1px solid #d1d5db;
    }


    table{
        min-width:0;

        width:100%;

        table-layout:fixed;
    }


    thead th,
    tbody td{
        font-size:8px;

        padding:5px 4px;

        overflow-wrap:anywhere;
    }


    .detalle-texto{
        max-width:none;
    }


    tr{
        page-break-inside:avoid;
    }
}


/* =========================================
   TABLET
========================================= */

@media(max-width:1100px){

    .tabla-contenedor{
        overflow:hidden;
    }


    table{
        min-width:0;
        table-layout:fixed;
    }


    thead th,
    tbody td{
        padding:9px 7px;
        font-size:11px;
    }


    .filtros-grid{
        grid-template-columns:
            repeat(
                2,
                minmax(180px,1fr)
            );
    }


    .detalle-meta{
        grid-template-columns:
            repeat(
                2,
                minmax(170px,1fr)
            );
    }
}


/* =========================================
   CELULAR
========================================= */

@media(max-width:700px){

    table th:nth-child(3),
    table td:nth-child(3),
    table th:nth-child(4),
    table td:nth-child(4){
        display:none;
    }


    thead th,
    tbody td{
        padding:7px 4px;
        font-size:10px;
    }


    .btn-detalle{
        font-size:9px;
        padding:6px 3px;
    }


    .contenedor{
        width:96%;

        margin:
            18px auto;
    }


    .panel-superior{
        flex-direction:column;

        align-items:stretch;

        text-align:center;
    }


    .acciones-superiores{
        flex-direction:column;
    }


    .acciones-superiores .btn{
        width:100%;
    }


    .filtros-grid{
        grid-template-columns:1fr;
    }


    .acciones-filtros{
        flex-direction:column;
    }


    .acciones-filtros .btn{
        width:100%;
    }


    .detalle-meta{
        grid-template-columns:1fr;
    }


    .comparacion-grid{
        grid-template-columns:1fr;
    }


    .detalle-cabecera{
        flex-direction:column;
    }


    .btn-cerrar{
        width:100%;
    }
}

</style>

</head>


<body>


<!-- =========================================
     HEADER GLOBAL
========================================= -->

<?php

$sigenmuniHeaderSubtitulo =
    'Sistema de Gestión Municipal - Reporte de Auditoría';

require __DIR__
    . '/../../../componentes/header_sigenmuni.php';

?>


<div class="contenedor">


    <!-- =====================================
         CABECERA
    ====================================== -->

    <div class="panel-superior">

        <div class="titulo-reporte">

            <h2>
                🛡️ Reporte de Auditoría
            </h2>

            <p>
                Historial de acciones realizadas por los usuarios del sistema.
            </p>

        </div>


        <div class="acciones-superiores">

            <a
                href="<?php
                    echo auditoriaEscapar(
                        $auditoriaUrlReportes
                    );
                ?>"
                class="btn btn-volver"
            >
                Volver a reportes
            </a>


            <button
                type="button"
                class="btn btn-imprimir"
                onclick="window.print();"
            >
                Imprimir
            </button>


            <a
                href="<?php
                    echo auditoriaEscapar(
                        $auditoriaUrlPdf
                    );
                ?>"
                class="btn btn-pdf"
            >
                Exportar PDF
            </a>

        </div>

    </div>


    <!-- =====================================
         ERROR
    ====================================== -->

    <?php if (!empty($error)): ?>

        <div class="mensaje-error">

            <?php
                echo auditoriaEscapar(
                    $error
                );
            ?>

        </div>

    <?php endif; ?>


    <!-- =====================================
         FILTROS
    ====================================== -->

    <div class="panel panel-filtros">

        <div class="panel-titulo">

            <div class="icono-panel">
                🔎
            </div>

            <h3>
                Filtros de búsqueda
            </h3>

        </div>


        <form
            method="GET"
            action="<?php
                echo auditoriaEscapar(
                    $auditoriaUrlEntrada
                );
            ?>"
        >

            <input
                type="hidden"
                name="r"
                value="reportes/auditoria"
            >

            <div class="filtros-grid">


                <div class="campo">

                    <label for="fecha_desde">
                        Fecha desde
                    </label>

                    <input
                        type="date"
                        id="fecha_desde"
                        name="fecha_desde"
                        value="<?php
                            echo auditoriaEscapar(
                                $fechaDesde
                            );
                        ?>"
                    >

                </div>


                <div class="campo">

                    <label for="fecha_hasta">
                        Fecha hasta
                    </label>

                    <input
                        type="date"
                        id="fecha_hasta"
                        name="fecha_hasta"
                        value="<?php
                            echo auditoriaEscapar(
                                $fechaHasta
                            );
                        ?>"
                    >

                </div>


                <div class="campo">

                    <label for="usuario">
                        Usuario
                    </label>

                    <select
                        id="usuario"
                        name="usuario"
                    >

                        <option value="">
                            Todos
                        </option>

                        <?php foreach ($usuariosAuditoria as $item): ?>

                            <?php

                            $usuarioItem =
                                $item['usuario_login']
                                ??
                                '';

                            $nombreItem =
                                $item['usuario_nombre']
                                ??
                                '';

                            ?>

                            <option
                                value="<?php
                                    echo auditoriaEscapar(
                                        $usuarioItem
                                    );
                                ?>"
                                <?php
                                    echo (
                                        $usuario === $usuarioItem
                                    )
                                        ?
                                        'selected'
                                        :
                                        '';
                                ?>
                            >

                                <?php

                                echo auditoriaEscapar(
                                    $usuarioItem
                                    .
                                    (
                                        $nombreItem !== ''
                                            ?
                                            ' - '
                                            . $nombreItem
                                            :
                                            ''
                                    )
                                );

                                ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <div class="campo">

                    <label for="rol">
                        Rol
                    </label>

                    <select
                        id="rol"
                        name="rol"
                    >

                        <option value="">
                            Todos
                        </option>

                        <?php foreach ($rolesAuditoria as $item): ?>

                            <?php
                                $rolItem =
                                    $item['rol']
                                    ??
                                    '';
                            ?>

                            <option
                                value="<?php
                                    echo auditoriaEscapar(
                                        $rolItem
                                    );
                                ?>"
                                <?php
                                    echo (
                                        $rol === $rolItem
                                    )
                                        ?
                                        'selected'
                                        :
                                        '';
                                ?>
                            >
                                <?php
                                    echo auditoriaEscapar(
                                        $rolItem
                                    );
                                ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <div class="campo">

                    <label for="modulo">
                        Módulo
                    </label>

                    <select
                        id="modulo"
                        name="modulo"
                    >

                        <option value="">
                            Todos
                        </option>

                        <?php foreach ($modulosAuditoria as $item): ?>

                            <?php
                                $moduloItem =
                                    $item['modulo']
                                    ??
                                    '';
                            ?>

                            <option
                                value="<?php
                                    echo auditoriaEscapar(
                                        $moduloItem
                                    );
                                ?>"
                                <?php
                                    echo (
                                        $modulo === $moduloItem
                                    )
                                        ?
                                        'selected'
                                        :
                                        '';
                                ?>
                            >
                                <?php
                                    echo auditoriaEscapar(
                                        $moduloItem
                                    );
                                ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <div class="campo">

                    <label for="accion">
                        Acción
                    </label>

                    <select
                        id="accion"
                        name="accion"
                    >

                        <option value="">
                            Todas
                        </option>

                        <?php foreach ($accionesAuditoria as $item): ?>

                            <?php
                                $accionItem =
                                    strtoupper(
                                        $item['accion']
                                        ??
                                        ''
                                    );
                            ?>

                            <option
                                value="<?php
                                    echo auditoriaEscapar(
                                        $accionItem
                                    );
                                ?>"
                                <?php
                                    echo (
                                        $accion === $accionItem
                                    )
                                        ?
                                        'selected'
                                        :
                                        '';
                                ?>
                            >
                                <?php
                                    echo auditoriaEscapar(
                                        $accionItem
                                    );
                                ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <div class="campo">

                    <label for="entidad">
                        Entidad
                    </label>

                    <select
                        id="entidad"
                        name="entidad"
                    >

                        <option value="">
                            Todas
                        </option>

                        <?php foreach ($entidadesAuditoria as $item): ?>

                            <?php
                                $entidadItem =
                                    strtoupper(
                                        $item['entidad']
                                        ??
                                        ''
                                    );
                            ?>

                            <option
                                value="<?php
                                    echo auditoriaEscapar(
                                        $entidadItem
                                    );
                                ?>"
                                <?php
                                    echo (
                                        $entidad === $entidadItem
                                    )
                                        ?
                                        'selected'
                                        :
                                        '';
                                ?>
                            >
                                <?php
                                    echo auditoriaEscapar(
                                        $entidadItem
                                    );
                                ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


            </div>


            <div class="acciones-filtros">

                <button
                    type="submit"
                    class="btn btn-buscar"
                >
                    Buscar
                </button>


                <a
                    href="<?php
                        echo auditoriaEscapar(
                            $auditoriaUrlLimpiar
                        );
                    ?>"
                    class="btn btn-limpiar"
                >
                    Limpiar filtros
                </a>

            </div>

        </form>

    </div>


    <!-- =====================================
         LISTADO
    ====================================== -->

    <div class="panel">

        <div class="resumen">

            <div class="total-box">

                Total de registros:

                <?php
                    echo (int)$totalAuditorias;
                ?>

            </div>


            <div class="leyenda">
                Los registros se muestran del más reciente al más antiguo.
            </div>

        </div>


        <?php if (!empty($auditorias)): ?>


            <div class="tabla-contenedor">

                <table>

                    <thead>

                        <tr>

                            <th>
                                Fecha / Hora
                            </th>

                            <th>
                                Usuario
                            </th>

                            <th>
                                Rol
                            </th>

                            <th>
                                Módulo
                            </th>

                            <th>
                                Acción
                            </th>

                            <th>
                                Registro afectado
                            </th>

                            <th>
                                Detalle
                            </th>

                            <th class="col-accion">
                                Ver
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php foreach ($auditorias as $fila): ?>

                        <?php

                        $idAuditoria =
                            (int)(
                                $fila['id']
                                ??
                                0
                            );


                        $urlDetalle =
                            sigenmuniUrlRuta(
                                'reportes/auditoria',
                                array_merge(
                                    $filtrosDetalleLimpios,
                                    [
                                        'detalle_id' =>
                                            $idAuditoria
                                    ]
                                )
                            );

                        ?>

                        <tr>

                            <td>

                                <?php
                                    echo auditoriaEscapar(
                                        auditoriaFechaHora(
                                            $fila['fecha_hora']
                                            ??
                                            ''
                                        )
                                    );
                                ?>

                            </td>


                            <td>

                                <div class="usuario-principal">

                                    <?php
                                        echo auditoriaEscapar(
                                            $fila['usuario_login']
                                            ??
                                            '-'
                                        );
                                    ?>

                                </div>


                                <div class="usuario-secundario">

                                    <?php
                                        echo auditoriaEscapar(
                                            $fila['usuario_nombre']
                                            ??
                                            '-'
                                        );
                                    ?>

                                </div>

                            </td>


                            <td>

                                <?php
                                    echo auditoriaEscapar(
                                        $fila['rol']
                                        ??
                                        '-'
                                    );
                                ?>

                            </td>


                            <td>

                                <?php
                                    echo auditoriaEscapar(
                                        $fila['modulo']
                                        ??
                                        '-'
                                    );
                                ?>

                            </td>


                            <td>

                                <span
                                    class="badge <?php
                                        echo auditoriaEscapar(
                                            auditoriaClaseAccion(
                                                $fila['accion']
                                                ??
                                                ''
                                            )
                                        );
                                    ?>"
                                >

                                    <?php
                                        echo auditoriaEscapar(
                                            $fila['accion']
                                            ??
                                            '-'
                                        );
                                    ?>

                                </span>

                            </td>


                            <td>

                                <div class="registro-principal">

                                    <?php
                                        echo auditoriaEscapar(
                                            $fila['entidad_descripcion']
                                            ??
                                            (
                                                $fila['entidad']
                                                ??
                                                '-'
                                            )
                                        );
                                    ?>

                                </div>


                                <div class="registro-secundario">

                                    <?php

                                    echo auditoriaEscapar(
                                        (
                                            $fila['entidad']
                                            ??
                                            '-'
                                        )
                                        .
                                        (
                                            !empty(
                                                $fila['entidad_id']
                                            )
                                                ?
                                                ' · ID '
                                                . $fila['entidad_id']
                                                :
                                                ''
                                        )
                                    );

                                    ?>

                                </div>

                            </td>


                            <td>

                                <div class="detalle-texto">

                                    <?php
                                        echo auditoriaEscapar(
                                            $fila['detalle']
                                            ??
                                            '-'
                                        );
                                    ?>

                                </div>

                            </td>


                            <td class="col-accion">

                                <a
                                    href="<?php
                                        echo auditoriaEscapar(
                                            $urlDetalle
                                        );
                                    ?>"
                                    class="btn-detalle"
                                >
                                    Ver detalle
                                </a>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>


        <?php else: ?>


            <div class="sin-registros">

                No se encontraron registros de auditoría
                para los filtros seleccionados.

            </div>


        <?php endif; ?>

    </div>


    <!-- =====================================
         DETALLE
    ====================================== -->

    <?php if ($auditoriaDetalle): ?>

        <div
            class="panel detalle-auditoria"
            id="detalleAuditoria"
        >

            <div class="detalle-cabecera">

                <div>

                    <h3>
                        Detalle de Auditoría
                    </h3>

                    <p>

                        Registro N.º

                        <?php
                            echo (int)(
                                $auditoriaDetalle['id']
                                ??
                                0
                            );
                        ?>

                    </p>

                </div>


                <a
                    href="<?php
                        echo auditoriaEscapar(
                            $urlCerrarDetalle
                        );
                    ?>"
                    class="btn btn-cerrar"
                >
                    Cerrar detalle
                </a>

            </div>


            <div class="detalle-meta">


                <div class="meta-item">

                    <div class="titulo">
                        Fecha / Hora
                    </div>

                    <div class="valor">

                        <?php
                            echo auditoriaEscapar(
                                auditoriaFechaHora(
                                    $auditoriaDetalle['fecha_hora']
                                    ??
                                    ''
                                )
                            );
                        ?>

                    </div>

                </div>


                <div class="meta-item">

                    <div class="titulo">
                        Usuario
                    </div>

                    <div class="valor">

                        <?php

                        echo auditoriaEscapar(
                            (
                                $auditoriaDetalle['usuario_login']
                                ??
                                '-'
                            )
                            .
                            ' - '
                            .
                            (
                                $auditoriaDetalle['usuario_nombre']
                                ??
                                '-'
                            )
                        );

                        ?>

                    </div>

                </div>


                <div class="meta-item">

                    <div class="titulo">
                        Rol
                    </div>

                    <div class="valor">

                        <?php
                            echo auditoriaEscapar(
                                $auditoriaDetalle['rol']
                                ??
                                '-'
                            );
                        ?>

                    </div>

                </div>


                <div class="meta-item">

                    <div class="titulo">
                        IP
                    </div>

                    <div class="valor">

                        <?php
                            echo auditoriaEscapar(
                                $auditoriaDetalle['ip']
                                ??
                                '-'
                            );
                        ?>

                    </div>

                </div>


                <div class="meta-item">

                    <div class="titulo">
                        Módulo
                    </div>

                    <div class="valor">

                        <?php
                            echo auditoriaEscapar(
                                $auditoriaDetalle['modulo']
                                ??
                                '-'
                            );
                        ?>

                    </div>

                </div>


                <div class="meta-item">

                    <div class="titulo">
                        Acción
                    </div>

                    <div class="valor">

                        <span
                            class="badge <?php
                                echo auditoriaEscapar(
                                    auditoriaClaseAccion(
                                        $auditoriaDetalle['accion']
                                        ??
                                        ''
                                    )
                                );
                            ?>"
                        >

                            <?php
                                echo auditoriaEscapar(
                                    $auditoriaDetalle['accion']
                                    ??
                                    '-'
                                );
                            ?>

                        </span>

                    </div>

                </div>


                <div class="meta-item">

                    <div class="titulo">
                        Entidad
                    </div>

                    <div class="valor">

                        <?php
                            echo auditoriaEscapar(
                                $auditoriaDetalle['entidad']
                                ??
                                '-'
                            );
                        ?>

                    </div>

                </div>


                <div class="meta-item">

                    <div class="titulo">
                        ID afectado
                    </div>

                    <div class="valor">

                        <?php

                        echo auditoriaEscapar(
                            $auditoriaDetalle['entidad_id']
                            ??
                            '-'
                        );

                        ?>

                    </div>

                </div>


            </div>


            <div class="detalle-descripcion">

                <strong>
                    Registro:
                </strong>

                <?php
                    echo auditoriaEscapar(
                        $auditoriaDetalle['entidad_descripcion']
                        ??
                        '-'
                    );
                ?>

                <br><br>

                <strong>
                    Detalle:
                </strong>

                <?php
                    echo auditoriaEscapar(
                        $auditoriaDetalle['detalle']
                        ??
                        '-'
                    );
                ?>

            </div>


            <div class="comparacion-grid">


                <!-- DATOS ANTERIORES -->

                <div class="bloque-datos">

                    <h4>
                        Datos anteriores
                    </h4>


                    <?php if (!empty($datosAnterioresDetalle)): ?>

                        <table>

                            <tbody>

                            <?php foreach ($datosAnterioresDetalle as $campo => $valor): ?>

                                <tr>

                                    <th>

                                        <?php
                                            echo auditoriaEscapar(
                                                auditoriaEtiquetaCampo(
                                                    $campo
                                                )
                                            );
                                        ?>

                                    </th>


                                    <td>

                                        <?php
                                            echo auditoriaEscapar(
                                                auditoriaValorMostrar(
                                                    $valor
                                                )
                                            );
                                        ?>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                            </tbody>

                        </table>


                    <?php else: ?>


                        <div class="vacio-datos">
                            No hay datos anteriores registrados.
                        </div>


                    <?php endif; ?>

                </div>


                <!-- DATOS NUEVOS -->

                <div class="bloque-datos">

                    <h4>
                        Datos nuevos
                    </h4>


                    <?php if (!empty($datosNuevosDetalle)): ?>

                        <table>

                            <tbody>

                            <?php foreach ($datosNuevosDetalle as $campo => $valor): ?>

                                <tr>

                                    <th>

                                        <?php
                                            echo auditoriaEscapar(
                                                auditoriaEtiquetaCampo(
                                                    $campo
                                                )
                                            );
                                        ?>

                                    </th>


                                    <td>

                                        <?php
                                            echo auditoriaEscapar(
                                                auditoriaValorMostrar(
                                                    $valor
                                                )
                                            );
                                        ?>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                            </tbody>

                        </table>


                    <?php else: ?>


                        <div class="vacio-datos">
                            No hay datos nuevos registrados.
                        </div>


                    <?php endif; ?>

                </div>


            </div>

        </div>


        <script>

        document.addEventListener(
            "DOMContentLoaded",
            function()
            {
                const detalle =
                    document.getElementById(
                        "detalleAuditoria"
                    );

                if (detalle) {

                    detalle.scrollIntoView({
                        behavior:"smooth",
                        block:"start"
                    });
                }
            }
        );

        </script>

    <?php endif; ?>


</div>


<div class="footer">

    SIGENMUNI · Reporte de Auditoría · Solo consulta

</div>


</body>

</html>
