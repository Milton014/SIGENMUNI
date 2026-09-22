<?php

/*
|--------------------------------------------------------------------------
| CENTRO DE AYUDA - NAVEGACIÓN ROUTER
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../../../core/Url.php';


$ayudaUrlMenu =
    sigenmuniUrlRuta(
    'inicio'
);


$ayudaUrlSeccion =
    function ($seccion) {

        return sigenmuniUrlRuta(
            'ayuda',
            [
                'seccion' =>
                    $seccion
            ]
        );
    };

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Ayuda - SIGENMUNI</title>

<style>

*{
    box-sizing:border-box;
}

html{
    scroll-behavior:smooth;
}

body{
    margin:0;
    font-family:Arial,Helvetica,sans-serif;
    background:#f4f7fb;
    color:#1f2937;
}

a{
    color:inherit;
}

.contenedor{
    width:94%;
    max-width:1280px;
    margin:30px auto 50px;
}


/* =========================================================
   CABECERA
========================================================= */

.cabecera{
    background:white;
    color:#1f2937;
    border:1px solid #e5e7eb;
    border-radius:18px;
    padding:22px;
    box-shadow:0 8px 20px rgba(0,0,0,.06);
    margin-bottom:22px;
}

.cabecera-top{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:18px;
    flex-wrap:wrap;
}

.cabecera h1{
    margin:0 0 7px;
    font-size:28px;
    color:#0f766e;
}

.cabecera p{
    margin:0;
    line-height:1.55;
    color:#64748b;
}

.acciones-superiores{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
}

.btn{
    display:inline-block;
    border:none;
    border-radius:10px;
    padding:11px 15px;
    font-size:14px;
    font-weight:bold;
    text-decoration:none;
    cursor:pointer;
    transition:.2s ease;
    text-align:center;
}

.btn:hover{
    transform:translateY(-1px);
    opacity:.94;
}

.btn-menu{
    background:#1f2937;
    color:white;
}

.btn-imprimir{
    background:#f59e0b;
    color:#111827;
}


/* =========================================================
   NAVEGACIÓN RÁPIDA
========================================================= */

.panel{
    background:white;
    border:1px solid #e5e7eb;
    border-radius:18px;
    padding:22px;
    box-shadow:0 8px 20px rgba(0,0,0,.06);
    margin-bottom:22px;
}

.panel h2{
    margin:0 0 8px;
    font-size:22px;
    color:#0f766e;
}

.panel-subtitulo{
    margin:0 0 18px;
    color:#64748b;
    line-height:1.55;
}

.menu-ayuda{
    display:grid;
    grid-template-columns:repeat(5,1fr);
    gap:12px;
}

.menu-ayuda a{
    display:flex;
    align-items:center;
    justify-content:center;
    gap:8px;
    min-height:54px;
    padding:10px;
    border-radius:12px;
    background:#f0fdfa;
    border:1px solid #99f6e4;
    color:#115e59;
    text-decoration:none;
    font-size:13px;
    font-weight:bold;
    text-align:center;
    transition:.2s ease;
}

.menu-ayuda a:hover{
    background:#ccfbf1;
    transform:translateY(-1px);
}


/* =========================================================
   BUSCADOR DE AYUDA
========================================================= */

.buscador-ayuda{
    background:white;
    border:1px solid #e5e7eb;
    border-radius:18px;
    padding:20px;
    box-shadow:0 8px 20px rgba(0,0,0,.06);
    margin-bottom:22px;
}

.buscador-ayuda h2{
    margin:0 0 8px;
    font-size:22px;
    color:#0f766e;
}

.buscador-ayuda p{
    margin:0 0 15px;
    color:#64748b;
    line-height:1.55;
}

.buscador-ayuda-grid{
    display:grid;
    grid-template-columns:1fr auto;
    gap:10px;
}

.buscador-ayuda input{
    width:100%;
    border:1px solid #cbd5e1;
    border-radius:11px;
    padding:12px 14px;
    font-size:14px;
    outline:none;
    transition:.2s ease;
}

.buscador-ayuda input:focus{
    border-color:#14b8a6;
    box-shadow:0 0 0 3px rgba(20,184,166,.14);
}

.btn-limpiar-busqueda{
    background:#64748b;
    color:white;
}

.resultado-busqueda{
    display:none;
    margin-top:14px;
    padding:12px 14px;
    border-radius:11px;
    background:#fff7ed;
    border:1px solid #fed7aa;
    color:#9a3412;
    font-weight:bold;
}

.oculto-busqueda{
    display:none !important;
}


/* =========================================================
   BOTONES DE GUÍA
========================================================= */

.modulo{
    display:flex;
    flex-direction:column;
}

.modulo ul{
    flex:1;
}

.modulo-acciones{
    margin-top:15px;
    padding-top:13px;
    border-top:1px solid #e2e8f0;
}

.btn-guia{
    display:inline-block;
    padding:9px 13px;
    border-radius:9px;
    background:#0f766e;
    color:white;
    text-decoration:none;
    font-size:13px;
    font-weight:bold;
    transition:.2s ease;
}

.btn-guia:hover{
    background:#115e59;
    transform:translateY(-1px);
}


/* =========================================================
   MANUAL DE USUARIO DESTACADO
========================================================= */

.manual-destacado{
    display:grid;
    grid-template-columns:auto 1fr auto;
    gap:18px;
    align-items:center;
    background:linear-gradient(135deg,#ecfdf5,#f0fdfa);
    border:1px solid #99f6e4;
    border-radius:18px;
    padding:22px;
    box-shadow:0 8px 20px rgba(0,0,0,.06);
    margin-bottom:22px;
}

.manual-icono{
    width:64px;
    height:64px;
    border-radius:18px;
    display:flex;
    align-items:center;
    justify-content:center;
    background:#0f766e;
    color:white;
    font-size:31px;
    box-shadow:0 6px 14px rgba(15,118,110,.18);
}

.manual-contenido h2{
    margin:0 0 7px;
    color:#0f766e;
    font-size:22px;
}

.manual-contenido p{
    margin:0;
    color:#475569;
    line-height:1.6;
}

.btn-manual{
    background:#0f766e;
    color:white;
    white-space:nowrap;
}

.btn-manual:hover{
    background:#115e59;
}

.manual-etiqueta{
    display:inline-block;
    margin-top:10px;
    padding:5px 9px;
    border-radius:999px;
    background:#dcfce7;
    color:#166534;
    font-size:11px;
    font-weight:bold;
}


/* =========================================================
   TARJETAS PRINCIPALES
========================================================= */

.grid-principal{
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:18px;
    margin-bottom:22px;
}

.card{
    background:white;
    border:1px solid #e5e7eb;
    border-radius:18px;
    padding:22px;
    box-shadow:0 8px 20px rgba(0,0,0,.06);
}

.card-header{
    display:flex;
    align-items:center;
    gap:12px;
    margin-bottom:14px;
}

.icono{
    width:48px;
    height:48px;
    flex:0 0 48px;
    border-radius:14px;
    display:flex;
    justify-content:center;
    align-items:center;
    font-size:24px;
    background:#f0fdfa;
    border:1px solid #99f6e4;
}

.card h2,
.card h3{
    margin:0;
    color:#0f766e;
}

.card h2{
    font-size:21px;
}

.card h3{
    font-size:17px;
    margin-top:18px;
    margin-bottom:8px;
}

.card p{
    margin:7px 0;
    line-height:1.6;
    color:#475569;
}

.card ul,
.card ol{
    margin:10px 0 0 20px;
    padding:0;
}

.card li{
    margin-bottom:8px;
    line-height:1.55;
}


/* =========================================================
   GUÍA POR MÓDULO
========================================================= */

.modulos-grid{
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:15px;
}

.modulo{
    border:1px solid #e2e8f0;
    border-radius:14px;
    padding:17px;
    background:#fff;
}

.modulo h3{
    margin:0 0 10px;
    color:#334155;
    font-size:17px;
}

.modulo ul{
    margin:0 0 0 19px;
}

.modulo li{
    margin-bottom:7px;
    line-height:1.5;
    color:#475569;
}


/* =========================================================
   GLOSARIO
========================================================= */

.glosario{
    display:grid;
    gap:12px;
}

.termino{
    border-left:4px solid #14b8a6;
    background:#f8fafc;
    border-radius:0 12px 12px 0;
    padding:14px 16px;
}

.termino strong{
    display:block;
    margin-bottom:5px;
    color:#0f766e;
}

.termino span{
    color:#475569;
    line-height:1.55;
}


/* =========================================================
   PREGUNTAS FRECUENTES
========================================================= */

.faq details{
    border:1px solid #e2e8f0;
    border-radius:12px;
    padding:0 15px;
    background:white;
    margin-bottom:10px;
}

.faq summary{
    cursor:pointer;
    padding:15px 0;
    font-weight:bold;
    color:#334155;
}

.faq details p{
    padding:0 0 15px;
    margin:0;
    color:#475569;
    line-height:1.6;
}


/* =========================================================
   CONTACTO
========================================================= */

.contacto-grid{
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:12px;
    margin-top:15px;
}

.contacto-item{
    background:#f8fafc;
    border:1px solid #e2e8f0;
    border-radius:12px;
    padding:14px;
}

.contacto-item strong{
    display:block;
    font-size:12px;
    text-transform:uppercase;
    letter-spacing:.04em;
    color:#64748b;
    margin-bottom:6px;
}

.contacto-item span{
    color:#1f2937;
    font-weight:bold;
    overflow-wrap:anywhere;
}

.aviso{
    margin-top:16px;
    padding:14px;
    border-radius:12px;
    background:#fff7ed;
    border:1px solid #fed7aa;
    color:#9a3412;
    line-height:1.55;
}


/* =========================================================
   ACERCA DE
========================================================= */

.acerca{
    text-align:center;
}

.logo-sistema{
    width:72px;
    height:72px;
    border-radius:20px;
    margin:0 auto 12px;
    display:flex;
    justify-content:center;
    align-items:center;
    background:linear-gradient(135deg,#0f766e,#14b8a6);
    color:white;
    font-size:34px;
    box-shadow:0 8px 20px rgba(15,118,110,.2);
}

.acerca h2{
    margin-bottom:6px;
}

.acerca .version{
    display:inline-block;
    margin-top:10px;
    padding:6px 10px;
    border-radius:999px;
    background:#ecfdf5;
    color:#047857;
    font-size:12px;
    font-weight:bold;
}


/* =========================================================
   PIE
========================================================= */

.pie{
    text-align:center;
    color:#64748b;
    font-size:13px;
    padding:8px 0;
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media(max-width:1000px){

    .menu-ayuda{
        grid-template-columns:repeat(2,1fr);
    }

    .grid-principal,
    .modulos-grid{
        grid-template-columns:1fr;
    }
}

@media(max-width:700px){

    .contenedor{
        width:97%;
        margin:12px auto 30px;
    }

    .cabecera{
        padding:20px 16px;
        border-radius:16px;
    }

    .cabecera-top{
        flex-direction:column;
        align-items:stretch;
    }

    .cabecera h1{
        font-size:24px;
        text-align:center;
    }

    .cabecera p{
        text-align:center;
    }

    .acciones-superiores{
        flex-direction:column;
    }

    .acciones-superiores .btn{
        width:100%;
    }

    .panel,
    .card{
        padding:17px;
        border-radius:15px;
    }

    .manual-destacado{
        grid-template-columns:1fr;
        text-align:center;
        padding:18px;
    }

    .manual-icono{
        margin:0 auto;
    }

    .btn-manual{
        width:100%;
    }

    .menu-ayuda{
        grid-template-columns:1fr;
    }

    .contacto-grid{
        grid-template-columns:1fr;
    }

    .buscador-ayuda-grid{
        grid-template-columns:1fr;
    }

    .btn-limpiar-busqueda{
        width:100%;
    }

    .card-header{
        align-items:flex-start;
    }
}


/* =========================================================
   IMPRESIÓN
========================================================= */

@media print{

    .sigenmuni-global-header{
        display:none !important;
    }

    @page{
        size:A4 portrait;
        margin:12mm;
    }

    body{
        background:white;
    }

    .contenedor{
        width:100%;
        max-width:none;
        margin:0;
    }

    .acciones-superiores,
    .menu-ayuda,
    .buscador-ayuda,
    .btn-guia,
    .manual-destacado{
        display:none;
    }

    .cabecera,
    .panel,
    .card{
        box-shadow:none;
    }

    .cabecera{
        background:white;
        color:#111827;
        border:1px solid #d1d5db;
    }

    .cabecera h1{
        color:#0f766e;
    }

    .grid-principal,
    .modulos-grid{
        grid-template-columns:1fr;
    }

    .panel,
    .card,
    .modulo,
    .faq details{
        break-inside:avoid;
    }
}

</style>

</head>

<body>

<!-- =====================================================
     HEADER GLOBAL
====================================================== -->

<?php

$sigenmuniHeaderSubtitulo =
    'Centro de Ayuda';

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
                    Centro de Ayuda
                </h1>

                <p>
                    Guía de usuario, preguntas frecuentes,
                    conceptos importantes y datos de soporte de SIGENMUNI.
                </p>

            </div>

            <div class="acciones-superiores">

                <button
                    type="button"
                    class="btn btn-imprimir"
                    onclick="window.print()"
                >
                    Imprimir Ayuda
                </button>

                <a
                    href="<?php
                        echo htmlspecialchars(
                            $ayudaUrlMenu,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                    class="btn btn-menu"
                >
                    Volver al Menú
                </a>

            </div>

        </div>

    </div>


    <!-- =====================================================
         BUSCADOR DE AYUDA
    ====================================================== -->

    <div class="buscador-ayuda">

        <h2>
            Buscar en Ayuda
        </h2>

        <p>
            Escriba una palabra o tema, por ejemplo:
            empleados, categoría, conceptos, liquidación,
            reportes, permisos o contraseña.
        </p>

        <div class="buscador-ayuda-grid">

            <input
                type="search"
                id="buscarAyuda"
                placeholder="¿Qué necesita consultar?"
                autocomplete="off"
                aria-label="Buscar contenido en el módulo de ayuda"
            >

            <button
                type="button"
                class="btn btn-limpiar-busqueda"
                id="limpiarBusquedaAyuda"
            >
                Limpiar
            </button>

        </div>

        <div
            class="resultado-busqueda"
            id="sinResultadosAyuda"
        >
            No se encontraron contenidos relacionados con la búsqueda.
        </div>

    </div>


    <!-- =====================================================
         ACCESOS RÁPIDOS
    ====================================================== -->

    <div class="panel">

        <h2>
            Accesos rápidos
        </h2>

        <p class="panel-subtitulo">
            Seleccione una sección para ir directamente al contenido.
        </p>

        <div class="menu-ayuda">

            <a href="#guia">
                📘 Guía rápida
            </a>

            <a href="#modulos">
                🧩 Guía por módulo
            </a>

            <a href="#glosario">
                📖 Glosario
            </a>

            <a href="#preguntas">
                ❓ Preguntas frecuentes
            </a>

            <a href="#contacto">
                ☎️ Contacto y soporte
            </a>

        </div>

    </div>


    <!-- =====================================================
         MANUAL DE USUARIO
    ====================================================== -->

    <section
        class="manual-destacado buscable-ayuda"
        data-busqueda="manual usuario completo guia sistema empleados categorias conceptos liquidaciones reportes permisos soporte"
    >

        <div class="manual-icono">
            📕
        </div>

        <div class="manual-contenido">

            <h2>
                Manual de Usuario
            </h2>

            <p>
                Consulte la guía completa de SIGENMUNI:
                acceso al sistema, empleados, categorías, conceptos,
                conceptos por empleado, liquidaciones, reportes,
                auditoría, usuarios, roles, permisos y soporte.
            </p>

            <span class="manual-etiqueta">
                Guía completa del sistema
            </span>

        </div>

        <div>

            <a
                href="<?php
                    echo htmlspecialchars(
                        $ayudaUrlSeccion(
                            'manual'
                        ),
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                class="btn btn-manual"
            >
                Abrir Manual
            </a>

        </div>

    </section>


    <!-- =====================================================
         GUÍA RÁPIDA + ACERCA DE
    ====================================================== -->

    <div class="grid-principal">

        <section
            class="card buscable-ayuda"
            id="guia"
            data-busqueda="guia rápida inicio uso sistema empleados categorias conceptos liquidaciones reportes"
        >

            <div class="card-header">

                <div class="icono">
                    📘
                </div>

                <div>
                    <h2>
                        Guía rápida de uso
                    </h2>
                </div>

            </div>

            <p>
                Flujo general recomendado para utilizar el sistema:
            </p>

            <ol>

                <li>
                    Inicie sesión con su usuario habilitado.
                </li>

                <li>
                    Verifique o registre los datos del personal
                    en <strong>Gestión de Empleados</strong>.
                </li>

                <li>
                    Controle las categorías salariales
                    en <strong>Gestión de Categorías</strong>.
                </li>

                <li>
                    Configure los conceptos y sus valores vigentes
                    en <strong>Gestión de Conceptos</strong>.
                </li>

                <li>
                    Asigne conceptos particulares mediante
                    <strong>Conceptos por Empleado</strong>,
                    cuando corresponda.
                </li>

                <li>
                    Cree y procese la liquidación desde
                    <strong>Gestión de Liquidaciones</strong>.
                </li>

                <li>
                    Revise los resultados, recibos y totales
                    antes de cerrar la liquidación.
                </li>

                <li>
                    Utilice <strong>Consultas y Reportes</strong>
                    para obtener listados, PDF, Excel,
                    estadísticas y auditoría.
                </li>

            </ol>

        </section>


        <section class="card acerca buscable-ayuda" data-busqueda="sigenmuni sistema version municipalidad informacion acerca">

            <div class="logo-sistema">
                S
            </div>

            <h2>
                <?php
                echo htmlspecialchars(
                    $sistema['nombre'],
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>
            </h2>

            <p>
                <?php
                echo htmlspecialchars(
                    $sistema['descripcion'],
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>
            </p>

            <p>
                <strong>
                    <?php
                    echo htmlspecialchars(
                        $sistema['municipio'],
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>
                </strong>
            </p>

            <span class="version">
                Versión
                <?php
                echo htmlspecialchars(
                    $sistema['version'],
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>
            </span>

        </section>

    </div>


    <!-- =====================================================
         GUÍA POR MÓDULO
    ====================================================== -->

    <section
        class="panel seccion-filtrable"
        id="modulos"
    >

        <h2>
            Guía por módulo
        </h2>

        <p class="panel-subtitulo">
            Resumen de las principales funciones disponibles en SIGENMUNI.
        </p>

        <div class="modulos-grid">


            <div class="modulo buscable-ayuda" data-busqueda="empleados personal legajo alta editar consultar activar inactivar categoria escalafon situacion">

<h3>
                    👥 Gestión de Empleados
                </h3>

                <ul>
                    <li>Registrar nuevos empleados.</li>
                    <li>Consultar la ficha individual.</li>
                    <li>Editar datos personales y administrativos.</li>
                    <li>Asignar categoría, escalafón y situación.</li>
                    <li>Activar o inactivar empleados.</li>
                    <li>Buscar por legajo, apellido u otros datos disponibles.</li>
                </ul>

                <div class="modulo-acciones">

                    <a
                        href="<?php
                    echo htmlspecialchars(
                        $ayudaUrlSeccion(
                            'empleados'
                        ),
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                        class="btn-guia"
                    >
                        Ver guía
                    </a>

                </div>

            </div>


            <div class="modulo buscable-ayuda" data-busqueda="categorias categoria salarial alta editar activa inactiva valores">

<h3>
                    🏷️ Gestión de Categorías
                </h3>

                <ul>
                    <li>Registrar y editar categorías salariales.</li>
                    <li>Consultar categorías activas e inactivas.</li>
                    <li>
                        Utilizar la categoría como referencia
                        para los valores salariales configurados.
                    </li>
                </ul>

                <div class="modulo-acciones">

                    <a
                        href="<?php
                    echo htmlspecialchars(
                        $ayudaUrlSeccion(
                            'categorias'
                        ),
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                        class="btn-guia"
                    >
                        Ver guía
                    </a>

                </div>

            </div>


            <div class="modulo buscable-ayuda" data-busqueda="conceptos tipo de concepto forma de calculo valor por categoria vigencia monto porcentaje">

<h3>
                    💰 Gestión de Conceptos
                </h3>

                <ul>
                    <li>Crear y editar conceptos de liquidación.</li>
                    <li>Definir el Tipo de Concepto.</li>
                    <li>Definir la Forma de Cálculo.</li>
                    <li>Configurar porcentajes o importes, según corresponda.</li>
                    <li>
                        Administrar Valores del Concepto
                        por categoría, escalafón y vigencia.
                    </li>
                    <li>Activar o inactivar conceptos y valores.</li>
                </ul>

                <div class="modulo-acciones">

                    <a
                        href="<?php
                    echo htmlspecialchars(
                        $ayudaUrlSeccion(
                            'conceptos'
                        ),
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                        class="btn-guia"
                    >
                        Ver guía
                    </a>

                </div>

            </div>


            <div class="modulo buscable-ayuda" data-busqueda="conceptos por empleado asignacion monto manual porcentaje cantidad vigencia observacion">

<h3>
                    📋 Conceptos por Empleado
                </h3>

                <ul>
                    <li>Asignar conceptos particulares a un empleado.</li>
                    <li>Registrar monto manual o porcentaje manual.</li>
                    <li>Indicar cantidad.</li>
                    <li>Definir fecha desde y fecha hasta.</li>
                    <li>Agregar observaciones.</li>
                    <li>Activar o inactivar la asignación.</li>
                </ul>

                <div class="modulo-acciones">

                    <a
                        href="<?php
                    echo htmlspecialchars(
                        $ayudaUrlSeccion(
                            'conceptos_empleado'
                        ),
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                        class="btn-guia"
                    >
                        Ver guía
                    </a>

                </div>

            </div>


            <div class="modulo buscable-ayuda" data-busqueda="liquidacion liquidaciones procesar recibos sueldo cerrar anular haberes descuentos">

<h3>
                    🧾 Gestión de Liquidaciones
                </h3>

                <ul>
                    <li>Crear una nueva liquidación.</li>
                    <li>Procesar empleados incluidos.</li>
                    <li>Consultar importes calculados.</li>
                    <li>Revisar conceptos y descuentos.</li>
                    <li>Visualizar e imprimir recibos de sueldo.</li>
                    <li>Cerrar o anular liquidaciones según corresponda.</li>
                </ul>

                <div class="modulo-acciones">

                    <a
                        href="<?php
                    echo htmlspecialchars(
                        $ayudaUrlSeccion(
                            'liquidaciones'
                        ),
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                        class="btn-guia"
                    >
                        Ver guía
                    </a>

                </div>

            </div>


            <div class="modulo buscable-ayuda" data-busqueda="reportes consultas historial pdf excel estadisticas auditoria">

<h3>
                    📊 Consultas y Reportes
                </h3>

                <ul>
                    <li>Reporte de empleados.</li>
                    <li>Historial por empleado.</li>
                    <li>Reporte de conceptos.</li>
                    <li>Reporte de categorías.</li>
                    <li>Reporte de liquidaciones.</li>
                    <li>Estadísticas.</li>
                    <li>Reporte de auditoría.</li>
                    <li>Exportación a PDF y Excel cuando esté disponible.</li>
                </ul>

                <div class="modulo-acciones">

                    <a
                        href="<?php
                    echo htmlspecialchars(
                        $ayudaUrlSeccion(
                            'reportes'
                        ),
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                        class="btn-guia"
                    >
                        Ver guía
                    </a>

                </div>

            </div>


            <div class="modulo buscable-ayuda" data-busqueda="usuarios roles permisos seguridad accesos administrador admin">

<h3>
                    🔐 Usuarios, Roles y Permisos
                </h3>

                <ul>
                    <li>Administrar usuarios del sistema.</li>
                    <li>Asignar roles.</li>
                    <li>Activar o inactivar usuarios y roles.</li>
                    <li>Configurar permisos de acceso por módulo.</li>
                    <li>
                        Las funciones administrativas dependen
                        del rol asignado al usuario.
                    </li>
                </ul>

                <div class="modulo-acciones">

                    <a
                        href="<?php
                    echo htmlspecialchars(
                        $ayudaUrlSeccion(
                            'usuarios'
                        ),
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                        class="btn-guia"
                    >
                        Ver guía
                    </a>

                </div>

            </div>


        </div>

    </section>


    <!-- =====================================================
         GLOSARIO
    ====================================================== -->

    <section
        class="panel seccion-filtrable"
        id="glosario"
    >

        <h2>
            Glosario del sistema
        </h2>

        <p class="panel-subtitulo">
            Definiciones breves de términos utilizados en SIGENMUNI.
        </p>

        <div class="glosario">


            <div class="termino buscable-ayuda">

                <strong>
                    Categoría
                </strong>

                <span>
                    Clasificación salarial asignada al empleado.
                    Por ejemplo: Categoría 19.
                </span>

            </div>


            <div class="termino buscable-ayuda">

                <strong>
                    Tipo de Concepto
                </strong>

                <span>
                    Clasificación del concepto de liquidación,
                    por ejemplo: remunerativo, no remunerativo,
                    asignación familiar, descuento o aporte patronal.
                </span>

            </div>


            <div class="termino buscable-ayuda">

                <strong>
                    Valor por Categoría
                </strong>

                <span>
                    Forma de cálculo donde el importe del concepto
                    se obtiene desde los Valores del Concepto
                    según la categoría salarial del empleado.
                </span>

            </div>


            <div class="termino buscable-ayuda">

                <strong>
                    Valor vigente
                </strong>

                <span>
                    Valor activo cuya fecha de vigencia corresponde
                    a la fecha utilizada por el sistema.
                </span>

            </div>


            <div class="termino buscable-ayuda">

                <strong>
                    Sin valor vigente
                </strong>

                <span>
                    Indica que para esa categoría y concepto
                    no existe actualmente un valor activo y vigente.
                    No significa necesariamente que el importe sea cero.
                </span>

            </div>


            <div class="termino buscable-ayuda">

                <strong>
                    Configuración completa
                </strong>

                <span>
                    Indica que la categoría posee los valores vigentes
                    requeridos para los conceptos salariales principales
                    utilizados por el reporte correspondiente.
                </span>

            </div>


            <div class="termino buscable-ayuda">

                <strong>
                    Configuración incompleta
                </strong>

                <span>
                    Indica que falta cargar uno o más valores vigentes
                    necesarios para esa categoría.
                </span>

            </div>


            <div class="termino buscable-ayuda">

                <strong>
                    Liquidación
                </strong>

                <span>
                    Proceso mediante el cual el sistema calcula
                    los haberes, descuentos, asignaciones y neto
                    correspondiente a los empleados incluidos.
                </span>

            </div>


            <div class="termino buscable-ayuda">

                <strong>
                    Auditoría
                </strong>

                <span>
                    Registro de acciones relevantes realizadas
                    por los usuarios dentro del sistema.
                </span>

            </div>

        </div>

    </section>


    <!-- =====================================================
         PREGUNTAS FRECUENTES
    ====================================================== -->

    <section
        class="panel faq seccion-filtrable"
        id="preguntas"
    >

        <h2>
            Preguntas frecuentes
        </h2>

        <p class="panel-subtitulo">
            Respuestas rápidas a situaciones comunes.
        </p>


        <details class="buscable-ayuda">

            <summary>
                ¿Por qué una categoría muestra "Sin valor vigente"?
            </summary>

            <p>
                Porque no existe un valor activo y vigente configurado
                para ese concepto y categoría en la fecha correspondiente.
                Debe revisar los Valores del Concepto.
            </p>

        </details>


        <details class="buscable-ayuda">

            <summary>
                ¿Qué significa "Configuración incompleta"?
            </summary>

            <p>
                Significa que a la categoría le falta uno o más valores
                vigentes requeridos para los conceptos salariales principales.
            </p>

        </details>


        <details class="buscable-ayuda">

            <summary>
                ¿Por qué no puedo ingresar a un módulo?
            </summary>

            <p>
                El acceso depende del rol y de los permisos asignados
                a su usuario. Si necesita ingresar a un módulo no habilitado,
                debe comunicarse con el administrador del sistema.
            </p>

        </details>


        <details class="buscable-ayuda">

            <summary>
                ¿Por qué un concepto no aparece en una liquidación?
            </summary>

            <p>
                Verifique que el concepto esté activo,
                que su configuración sea correcta,
                que tenga vigencia para la fecha correspondiente
                y que, cuando sea necesario, exista un valor
                o una asignación válida para el empleado.
            </p>

        </details>


        <details class="buscable-ayuda">

            <summary>
                ¿Qué diferencia existe entre Categoría y Tipo de Concepto?
            </summary>

            <p>
                Categoría identifica la clasificación salarial del empleado.
                Tipo de Concepto clasifica el concepto de liquidación
                como remunerativo, descuento, asignación u otro tipo.
            </p>

        </details>


        <details class="buscable-ayuda">

            <summary>
                ¿Cómo puedo recuperar o modificar una contraseña?
            </summary>

            <p>
                La administración de credenciales se realiza según
                las políticas de seguridad definidas para SIGENMUNI.
                Si no puede acceder, comuníquese con el administrador
                o responsable autorizado.
            </p>

        </details>


        <details class="buscable-ayuda">

            <summary>
                ¿Qué información conviene enviar al solicitar soporte?
            </summary>

            <p>
                Indique su usuario, el módulo donde ocurrió el problema,
                qué operación estaba realizando, el mensaje mostrado
                y, si es posible, adjunte una captura de pantalla.
                No envíe contraseñas.
            </p>

        </details>

    </section>


    <!-- =====================================================
         CONTACTO Y SOPORTE
    ====================================================== -->

    <section
        class="panel buscable-ayuda"
        id="contacto"
        data-busqueda="contacto soporte responsable area correo telefono horario asistencia"
    >

        <h2>
            Contacto y soporte
        </h2>

        <p class="panel-subtitulo">
            Datos para solicitar asistencia sobre el uso del sistema.
        </p>

        <div class="contacto-grid">


            <div class="contacto-item">

                <strong>
                    Responsable
                </strong>

                <span>
                    <?php
                    echo htmlspecialchars(
                        $soporte['responsable'],
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>
                </span>

            </div>


            <div class="contacto-item">

                <strong>
                    Área
                </strong>

                <span>
                    <?php
                    echo htmlspecialchars(
                        $soporte['area'],
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>
                </span>

            </div>


            <div class="contacto-item">

                <strong>
                    Correo electrónico
                </strong>

                <span>
                    <?php
                    echo htmlspecialchars(
                        $soporte['email'],
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>
                </span>

            </div>


            <div class="contacto-item">

                <strong>
                    Teléfono / Interno
                </strong>

                <span>
                    <?php
                    echo htmlspecialchars(
                        $soporte['telefono'],
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>
                </span>

            </div>


            <div class="contacto-item">

                <strong>
                    Horario de atención
                </strong>

                <span>
                    <?php
                    echo htmlspecialchars(
                        $soporte['horario'],
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>
                </span>

            </div>


            <div class="contacto-item">

                <strong>
                    Sistema
                </strong>

                <span>
                    <?php
                    echo htmlspecialchars(
                        $sistema['nombre'],
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>

                    -

                    Versión

                    <?php
                    echo htmlspecialchars(
                        $sistema['version'],
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>
                </span>

            </div>

        </div>


        <div class="aviso">

            <strong>
                Importante:
            </strong>

            para solicitar soporte puede informar usuario,
            módulo, descripción del inconveniente y captura de pantalla.
            Nunca comparta su contraseña, códigos de recuperación
            ni otros datos de acceso.

        </div>

    </section>


    <!-- =====================================================
         PIE
    ====================================================== -->

    <div class="pie">

        <?php
        echo htmlspecialchars(
            $sistema['nombre'],
            ENT_QUOTES,
            'UTF-8'
        );
        ?>

        -

        <?php
        echo htmlspecialchars(
            $sistema['municipio'],
            ENT_QUOTES,
            'UTF-8'
        );
        ?>

    </div>

</div>


<script>

document.addEventListener(
    "DOMContentLoaded",
    function()
    {
        const input =
            document.getElementById(
                "buscarAyuda"
            );

        const botonLimpiar =
            document.getElementById(
                "limpiarBusquedaAyuda"
            );

        const avisoSinResultados =
            document.getElementById(
                "sinResultadosAyuda"
            );

        const elementos =
            Array.from(
                document.querySelectorAll(
                    ".buscable-ayuda"
                )
            );

        const seccionesFiltrables =
            Array.from(
                document.querySelectorAll(
                    ".seccion-filtrable"
                )
            );


        function normalizarTexto(
            texto
        ) {
            return (texto ?? "")
                .toString()
                .normalize("NFD")
                .replace(
                    /[\u0300-\u036f]/g,
                    ""
                )
                .toLowerCase()
                .trim();
        }


        function filtrarAyuda()
        {
            if (!input) {
                return;
            }

            const termino =
                normalizarTexto(
                    input.value
                );

            let visibles =
                0;


            elementos.forEach(
                function(elemento)
                {
                    const contenido =
                        normalizarTexto(
                            (
                                elemento.dataset.busqueda
                                ??
                                ""
                            )
                            +
                            " "
                            +
                            elemento.textContent
                        );


                    const coincide =
                        termino === ""
                        ||
                        contenido.includes(
                            termino
                        );


                    elemento.classList.toggle(
                        "oculto-busqueda",
                        !coincide
                    );


                    if (coincide) {
                        visibles++;
                    }
                }
            );


            seccionesFiltrables.forEach(
                function(seccion)
                {
                    const hijos =
                        seccion.querySelectorAll(
                            ".buscable-ayuda"
                        );

                    let tieneVisible =
                        false;


                    hijos.forEach(
                        function(hijo)
                        {
                            if (
                                !hijo.classList.contains(
                                    "oculto-busqueda"
                                )
                            ) {
                                tieneVisible =
                                    true;
                            }
                        }
                    );


                    seccion.classList.toggle(
                        "oculto-busqueda",
                        termino !== ""
                        &&
                        !tieneVisible
                    );
                }
            );


            if (avisoSinResultados) {

                avisoSinResultados.style.display =
                    termino !== ""
                    &&
                    visibles === 0
                        ?
                        "block"
                        :
                        "none";
            }
        }


        if (input) {

            input.addEventListener(
                "input",
                filtrarAyuda
            );
        }


        if (botonLimpiar) {

            botonLimpiar.addEventListener(
                "click",
                function()
                {
                    if (!input) {
                        return;
                    }

                    input.value =
                        "";

                    filtrarAyuda();

                    input.focus();
                }
            );
        }
    }
);

</script>

</body>

</html>
