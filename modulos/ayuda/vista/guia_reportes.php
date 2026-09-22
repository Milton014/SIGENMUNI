<?php

/*
|--------------------------------------------------------------------------
| NAVEGACIÓN DEL MÓDULO AYUDA
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../../../core/Url.php';


$ayudaUrlVolver =
    sigenmuniUrlRuta(
        'ayuda'
    );

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Guía de Reportes - SIGENMUNI</title>

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
    max-width:1180px;
    margin:28px auto 48px;
}


/* =========================================================
   CABECERA
========================================================= */

.cabecera{
    background:linear-gradient(135deg,#ea580c,#f97316);
    color:white;
    border-radius:20px;
    padding:25px;
    box-shadow:0 10px 24px rgba(234,88,12,.18);
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
    font-size:30px;
}

.cabecera p{
    margin:0;
    line-height:1.55;
    opacity:.96;
}

.acciones{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
}

.btn{
    display:inline-block;
    text-decoration:none;
    border:none;
    border-radius:10px;
    padding:11px 15px;
    font-size:14px;
    font-weight:bold;
    cursor:pointer;
    transition:.2s ease;
    text-align:center;
}

.btn:hover{
    transform:translateY(-1px);
    opacity:.94;
}

.btn-volver{
    background:#1f2937;
    color:white;
}

.btn-imprimir{
    background:#facc15;
    color:#111827;
}


/* =========================================================
   PANEL / ÍNDICE
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
    color:#ea580c;
}

.panel-subtitulo{
    margin:0 0 17px;
    color:#64748b;
    line-height:1.55;
}

.indice{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:10px;
}

.indice a{
    display:flex;
    align-items:center;
    justify-content:center;
    min-height:48px;
    padding:10px;
    border-radius:11px;
    background:#fff7ed;
    border:1px solid #fed7aa;
    color:#9a3412;
    text-decoration:none;
    font-size:13px;
    font-weight:bold;
    text-align:center;
}

.indice a:hover{
    background:#ffedd5;
}


/* =========================================================
   SECCIONES
========================================================= */

.seccion{
    background:white;
    border:1px solid #e5e7eb;
    border-radius:18px;
    padding:22px;
    box-shadow:0 8px 20px rgba(0,0,0,.06);
    margin-bottom:20px;
}

.seccion-header{
    display:flex;
    gap:12px;
    align-items:center;
    margin-bottom:14px;
}

.icono{
    width:48px;
    height:48px;
    flex:0 0 48px;
    border-radius:14px;
    display:flex;
    align-items:center;
    justify-content:center;
    background:#fff7ed;
    border:1px solid #fed7aa;
    font-size:24px;
}

.seccion h2{
    margin:0;
    color:#ea580c;
    font-size:21px;
}

.seccion h3{
    margin:20px 0 8px;
    color:#334155;
    font-size:17px;
}

.seccion p{
    margin:7px 0;
    color:#475569;
    line-height:1.65;
}

.seccion ul,
.seccion ol{
    margin:10px 0 0 22px;
    padding:0;
}

.seccion li{
    margin-bottom:8px;
    color:#475569;
    line-height:1.55;
}


/* =========================================================
   TARJETAS DE REPORTES
========================================================= */

.reportes-grid{
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:14px;
    margin-top:14px;
}

.reporte-card{
    border:1px solid #e2e8f0;
    border-radius:14px;
    padding:16px;
    background:#f8fafc;
}

.reporte-card h3{
    margin:0 0 8px;
    color:#334155;
    font-size:17px;
}

.reporte-card p{
    margin:0;
}


/* =========================================================
   PASOS
========================================================= */

.pasos{
    display:grid;
    gap:12px;
    margin-top:14px;
}

.paso{
    display:grid;
    grid-template-columns:42px 1fr;
    gap:12px;
    align-items:start;
    padding:14px;
    border:1px solid #e2e8f0;
    border-radius:13px;
    background:#f8fafc;
}

.numero{
    width:36px;
    height:36px;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    background:#ea580c;
    color:white;
    font-weight:bold;
    font-size:14px;
}

.paso strong{
    display:block;
    margin-bottom:4px;
    color:#1f2937;
}

.paso p{
    margin:0;
}


/* =========================================================
   AVISOS
========================================================= */

.aviso{
    margin-top:15px;
    border-radius:13px;
    padding:14px 16px;
    line-height:1.6;
}

.aviso-info{
    background:#eff6ff;
    border:1px solid #bfdbfe;
    color:#1e40af;
}

.aviso-ok{
    background:#ecfdf5;
    border:1px solid #a7f3d0;
    color:#065f46;
}

.aviso-alerta{
    background:#fff7ed;
    border:1px solid #fed7aa;
    color:#9a3412;
}

.aviso-importante{
    background:#fef2f2;
    border:1px solid #fecaca;
    color:#991b1b;
}


/* =========================================================
   TABLA
========================================================= */

.tabla-contenedor{
    width:100%;
    overflow-x:auto;
    margin-top:14px;
}

table{
    width:100%;
    border-collapse:collapse;
    min-width:760px;
}

th,
td{
    border-bottom:1px solid #e5e7eb;
    padding:11px 10px;
    text-align:left;
    font-size:13px;
    vertical-align:top;
}

th{
    background:#ea580c;
    color:white;
}

td{
    color:#475569;
}


/* =========================================================
   PROBLEMAS FRECUENTES
========================================================= */

details{
    border:1px solid #e2e8f0;
    border-radius:12px;
    padding:0 15px;
    margin-bottom:10px;
    background:white;
}

summary{
    cursor:pointer;
    padding:15px 0;
    font-weight:bold;
    color:#334155;
}

details p{
    margin:0;
    padding:0 0 15px;
}


/* =========================================================
   PIE
========================================================= */

.pie{
    text-align:center;
    color:#64748b;
    font-size:13px;
    padding:10px 0;
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media(max-width:900px){

    .indice{
        grid-template-columns:repeat(2,1fr);
    }

    .reportes-grid{
        grid-template-columns:1fr;
    }
}

@media(max-width:700px){

    .contenedor{
        width:97%;
        margin:12px auto 30px;
    }

    .cabecera{
        padding:19px 15px;
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

    .acciones{
        flex-direction:column;
    }

    .acciones .btn{
        width:100%;
    }

    .indice{
        grid-template-columns:1fr;
    }

    .panel,
    .seccion{
        padding:17px;
        border-radius:15px;
    }

    .seccion-header{
        align-items:flex-start;
    }

    .paso{
        grid-template-columns:36px 1fr;
        padding:12px;
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

    .acciones,
    .indice{
        display:none;
    }

    .cabecera,
    .panel,
    .seccion{
        box-shadow:none;
    }

    .cabecera{
        background:white;
        color:#111827;
        border:1px solid #d1d5db;
    }

    .cabecera h1{
        color:#ea580c;
    }

    .seccion,
    .paso,
    .reporte-card,
    details{
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
    'Centro de Ayuda - Consultas y Reportes';

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
                    Guía de Consultas y Reportes
                </h1>

                <p>
                    Instrucciones para consultar información,
                    aplicar filtros y utilizar reportes, estadísticas
                    y auditoría dentro de SIGENMUNI.
                </p>

            </div>

            <div class="acciones">

                <button
                    type="button"
                    class="btn btn-imprimir"
                    onclick="window.print()"
                >
                    Imprimir Guía
                </button>

                <a
                    href="<?php
                    echo htmlspecialchars(
                        $ayudaUrlVolver,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                    class="btn btn-volver"
                >
                    Volver a Ayuda
                </a>

            </div>

        </div>

    </div>


    <!-- =====================================================
         ÍNDICE
    ====================================================== -->

    <div class="panel">

        <h2>
            Contenido de la guía
        </h2>

        <p class="panel-subtitulo">
            Seleccione una sección para ir directamente al tema.
        </p>

        <div class="indice">

            <a href="#objetivo">
                Objetivo
            </a>

            <a href="#reportes">
                Reportes disponibles
            </a>

            <a href="#filtros">
                Uso de filtros
            </a>

            <a href="#conceptos">
                Reporte de Conceptos
            </a>

            <a href="#liquidaciones">
                Reporte de Liquidaciones
            </a>

            <a href="#categorias">
                Reporte de Categorías
            </a>

            <a href="#estadisticas">
                Estadísticas
            </a>

            <a href="#exportacion">
                PDF y Excel
            </a>

            <a href="#auditoria">
                Auditoría
            </a>

            <a href="#interpretacion">
                Cómo interpretar los datos
            </a>

            <a href="#recomendaciones">
                Recomendaciones
            </a>

            <a href="#problemas">
                Problemas frecuentes
            </a>

        </div>

    </div>


    <!-- =====================================================
         OBJETIVO
    ====================================================== -->

    <section
        class="seccion"
        id="objetivo"
    >

        <div class="seccion-header">

            <div class="icono">
                📊
            </div>

            <h2>
                Objetivo del módulo
            </h2>

        </div>

        <p>
            El módulo <strong>Consultas y Reportes</strong>
            centraliza la información de consulta
            y análisis generada por SIGENMUNI.
        </p>

        <p>
            Desde este sector pueden consultarse
            empleados, conceptos, categorías,
            liquidaciones, estadísticas,
            historial y auditoría,
            según los permisos asignados al rol.
        </p>

        <div class="aviso aviso-info">

            <strong>Importante:</strong>
            que un usuario tenga acceso
            a Consultas y Reportes
            no significa necesariamente
            que tenga habilitados todos los reportes específicos.
            El acceso depende de los permisos configurados
            para cada rol.

        </div>

    </section>


    <!-- =====================================================
         REPORTES DISPONIBLES
    ====================================================== -->

    <section
        class="seccion"
        id="reportes"
    >

        <div class="seccion-header">

            <div class="icono">
                📚
            </div>

            <h2>
                Reportes disponibles
            </h2>

        </div>

        <div class="reportes-grid">


            <div class="reporte-card">

                <h3>
                    👥 Reporte de Empleados
                </h3>

                <p>
                    Permite consultar información general
                    del personal registrado,
                    revisar su situación actual
                    y utilizar los filtros disponibles
                    para localizar empleados específicos.
                </p>

            </div>


            <div class="reporte-card">

                <h3>
                    🕘 Historial por Empleado
                </h3>

                <p>
                    Permite revisar liquidaciones
                    y antecedentes históricos asociados
                    a un empleado determinado,
                    conservando la trazabilidad de sus registros.
                </p>

            </div>


            <div class="reporte-card">

                <h3>
                    📘 Reporte de Conceptos
                </h3>

                <p>
                    Muestra los conceptos registrados
                    y permite filtrarlos por
                    <strong>Tipo de Concepto</strong>
                    y estado,
                    además de consultar su forma de cálculo.
                </p>

            </div>


            <div class="reporte-card">

                <h3>
                    🏷️ Reporte de Categorías
                </h3>

                <p>
                    Muestra las categorías salariales
                    junto con los valores vigentes
                    de 101 - Sueldo Básico,
                    102 - Dedicación Funcional
                    y 104 - Suplemento Especial.
                </p>

            </div>


            <div class="reporte-card">

                <h3>
                    💰 Reporte de Liquidaciones
                </h3>

                <p>
                    Permite consultar liquidaciones
                    por período, tipo y estado,
                    además de revisar
                    los principales totales calculados.
                </p>

            </div>


            <div class="reporte-card">

                <h3>
                    📈 Estadísticas
                </h3>

                <p>
                    Presenta indicadores, tablas y gráficos
                    para analizar información económica
                    de liquidaciones cerradas,
                    con filtros por período y tipo.
                </p>

            </div>


            <div class="reporte-card">

                <h3>
                    🕵️ Reporte de Auditoría
                </h3>

                <p>
                    Permite revisar acciones registradas
                    por los usuarios,
                    aplicar filtros
                    y abrir el detalle de cada operación.
                </p>

            </div>

        </div>

    </section>


    <!-- =====================================================
         FILTROS
    ====================================================== -->

    <section
        class="seccion"
        id="filtros"
    >

        <div class="seccion-header">

            <div class="icono">
                🔎
            </div>

            <h2>
                Uso de filtros
            </h2>

        </div>

        <p>
            Los reportes pueden incluir filtros
            para reducir los resultados
            y facilitar la búsqueda de información.
        </p>

        <div class="pasos">

            <div class="paso">

                <div class="numero">
                    1
                </div>

                <div>

                    <strong>
                        Completar uno o más filtros
                    </strong>

                    <p>
                        Ingrese los datos disponibles,
                        como código, nombre,
                        período, estado,
                        usuario o fecha,
                        según el reporte.
                    </p>

                </div>

            </div>


            <div class="paso">

                <div class="numero">
                    2
                </div>

                <div>

                    <strong>
                        Presionar Buscar
                    </strong>

                    <p>
                        El sistema actualizará
                        el listado utilizando
                        los criterios ingresados.
                    </p>

                </div>

            </div>


            <div class="paso">

                <div class="numero">
                    3
                </div>

                <div>

                    <strong>
                        Revisar resultados
                    </strong>

                    <p>
                        Verifique la cantidad
                        y el contenido de los registros
                        obtenidos.
                    </p>

                </div>

            </div>


            <div class="paso">

                <div class="numero">
                    4
                </div>

                <div>

                    <strong>
                        Limpiar cuando sea necesario
                    </strong>

                    <p>
                        Utilice la opción Limpiar
                        para volver a la consulta general.
                    </p>

                </div>

            </div>

        </div>

        <div class="aviso aviso-ok">

            Para búsquedas precisas,
            utilice los filtros más específicos
            que tenga disponibles.

        </div>

    </section>


    <!-- =====================================================
         REPORTE DE CONCEPTOS
    ====================================================== -->

    <section
        class="seccion"
        id="conceptos"
    >

        <div class="seccion-header">

            <div class="icono">
                📘
            </div>

            <h2>
                Reporte de Conceptos
            </h2>

        </div>

        <p>
            El Reporte de Conceptos permite consultar
            los conceptos registrados en SIGENMUNI
            y revisar su configuración general.
        </p>

        <h3>
            Filtros principales
        </h3>

        <div class="tabla-contenedor">

            <table>

                <thead>

                    <tr>
                        <th>Filtro</th>
                        <th>Uso</th>
                    </tr>

                </thead>

                <tbody>

                    <tr>
                        <td>Búsqueda</td>
                        <td>
                            Permite localizar conceptos
                            por código o denominación,
                            según las opciones de la pantalla.
                        </td>
                    </tr>

                    <tr>
                        <td>Tipo de Concepto</td>
                        <td>
                            Permite filtrar por
                            Remunerativo,
                            No Remunerativo,
                            Asignación Familiar,
                            Descuento
                            o Aporte Patronal.
                        </td>
                    </tr>

                    <tr>
                        <td>Estado</td>
                        <td>
                            Permite consultar conceptos
                            activos, inactivos
                            o todos los registros.
                        </td>
                    </tr>

                </tbody>

            </table>

        </div>

        <div class="aviso aviso-info">

            <strong>Tipo de Concepto</strong>
            no debe confundirse con
            la categoría salarial del empleado.
            Son clasificaciones diferentes.

        </div>

    </section>


    <!-- =====================================================
         REPORTE DE LIQUIDACIONES
    ====================================================== -->

    <section
        class="seccion"
        id="liquidaciones"
    >

        <div class="seccion-header">

            <div class="icono">
                💰
            </div>

            <h2>
                Reporte de Liquidaciones
            </h2>

        </div>

        <p>
            Este reporte permite consultar
            las liquidaciones registradas
            utilizando varios criterios de búsqueda.
        </p>

        <h3>
            Filtros principales
        </h3>

        <div class="tabla-contenedor">

            <table>

                <thead>

                    <tr>
                        <th>Filtro</th>
                        <th>Descripción</th>
                    </tr>

                </thead>

                <tbody>

                    <tr>
                        <td>Período</td>
                        <td>
                            Permite buscar una liquidación
                            por el mes y año correspondiente.
                        </td>
                    </tr>

                    <tr>
                        <td>Tipo de Liquidación</td>
                        <td>
                            Permite diferenciar
                            Mensual,
                            Complementaria de Haberes,
                            Aguinaldo,
                            Complementaria de SAC
                            y Gastos Protocolares.
                        </td>
                    </tr>

                    <tr>
                        <td>Estado</td>
                        <td>
                            Permite consultar liquidaciones
                            según el estado registrado
                            en el sistema.
                        </td>
                    </tr>

                </tbody>

            </table>

        </div>

        <div class="aviso aviso-alerta">

            Cuando analice totales,
            verifique siempre
            el período, tipo de liquidación
            y estado seleccionado.
            Comparar liquidaciones de distinto tipo
            puede producir interpretaciones incorrectas.

        </div>

    </section>


    <!-- =====================================================
         EXPORTACIÓN
    ====================================================== -->

    <section
        class="seccion"
        id="exportacion"
    >

        <div class="seccion-header">

            <div class="icono">
                📄
            </div>

            <h2>
                Exportación a PDF y Excel
            </h2>

        </div>

        <p>
            Algunos reportes permiten
            exportar o imprimir
            la información consultada.
            La exportación debe conservar
            los mismos filtros aplicados
            en la pantalla.
        </p>

        <div class="tabla-contenedor">

            <table>

                <thead>

                    <tr>
                        <th>Opción</th>
                        <th>Uso</th>
                        <th>Recomendación</th>
                    </tr>

                </thead>

                <tbody>

                    <tr>
                        <td>PDF / Imprimir</td>
                        <td>
                            Genera una presentación
                            preparada para impresión
                            o guardado en PDF.
                        </td>
                        <td>
                            Revise previamente
                            que los filtros aplicados
                            sean correctos.
                        </td>
                    </tr>

                    <tr>
                        <td>Excel (.xlsx)</td>
                        <td>
                            Exporta la información
                            en un archivo Excel
                            para análisis y trabajo posterior.
                        </td>
                        <td>
                            En Estadísticas puede incluir
                            hojas de datos y gráficos
                            asociados al análisis generado.
                        </td>
                    </tr>

                </tbody>

            </table>

        </div>

        <div class="aviso aviso-alerta">

            La exportación debe respetar
            los mismos filtros
            aplicados al reporte en pantalla.
            Antes de guardar un archivo,
            confirme que los resultados
            sean los esperados.

        </div>

    </section>


    <!-- =====================================================
         REPORTE DE CATEGORÍAS
    ====================================================== -->

    <section
        class="seccion"
        id="categorias"
    >

        <div class="seccion-header">

            <div class="icono">
                🏷️
            </div>

            <h2>
                Reporte de Categorías
            </h2>

        </div>

        <p>
            Este reporte muestra
            las categorías existentes
            junto con los valores vigentes
            de conceptos salariales principales.
        </p>

        <h3>
            Conceptos principales
        </h3>

        <ul>
            <li>
                <strong>101 - Sueldo Básico.</strong>
            </li>
            <li>
                <strong>102 - Dedicación Funcional.</strong>
            </li>
            <li>
                <strong>104 - Suplemento Especial.</strong>
            </li>
        </ul>

        <h3>
            Interpretación de resultados
        </h3>

        <div class="tabla-contenedor">

            <table>

                <thead>

                    <tr>
                        <th>Resultado</th>
                        <th>Significado</th>
                    </tr>

                </thead>

                <tbody>

                    <tr>
                        <td>
                            $ 500.000,00
                        </td>
                        <td>
                            Existe un valor activo
                            y vigente para esa categoría
                            y concepto.
                        </td>
                    </tr>

                    <tr>
                        <td>
                            Sin valor vigente
                        </td>
                        <td>
                            No existe actualmente
                            un valor activo y vigente.
                            No debe interpretarse
                            automáticamente como cero.
                        </td>
                    </tr>

                    <tr>
                        <td>
                            Completa
                        </td>
                        <td>
                            La categoría posee
                            los valores principales
                            requeridos.
                        </td>
                    </tr>

                    <tr>
                        <td>
                            Incompleta
                        </td>
                        <td>
                            Falta uno o más
                            valores vigentes
                            necesarios.
                        </td>
                    </tr>

                </tbody>

            </table>

        </div>

        <div class="aviso aviso-importante">

            <strong>Sin valor vigente</strong>
            y <strong>$ 0,00</strong>
            son situaciones diferentes.
            El primero indica ausencia
            de una configuración vigente;
            el segundo puede representar
            un valor realmente cargado en cero.

        </div>

        <div class="aviso aviso-info">

            Una categoría puede mostrarse como
            <strong>Incompleta</strong>
            si falta uno de los valores principales.
            En el caso particular de
            <strong>102 - Dedicación Funcional</strong>,
            la liquidación dispone de una regla de respaldo
            que puede utilizar el Sueldo Básico
            cuando no existe un valor vigente de 102.
            Aun así, se recomienda configurar
            el valor 102 explícitamente.

        </div>

    </section>


    <!-- =====================================================
         ESTADÍSTICAS
    ====================================================== -->

    <section
        class="seccion"
        id="estadisticas"
    >

        <div class="seccion-header">

            <div class="icono">
                📈
            </div>

            <h2>
                Estadísticas
            </h2>

        </div>

        <p>
            La sección Estadísticas
            permite analizar información económica
            mediante indicadores, tablas y gráficos.
        </p>

        <p>
            Los datos económicos se calculan
            sobre liquidaciones
            <strong>CERRADAS</strong>
            que coinciden con los filtros seleccionados.
        </p>

        <h3>
            Filtros disponibles
        </h3>

        <ul>
            <li>
                <strong>Desde:</strong>
                período inicial del análisis.
            </li>
            <li>
                <strong>Hasta:</strong>
                período final del análisis.
            </li>
            <li>
                <strong>Tipo de Liquidación:</strong>
                permite limitar el análisis
                a un tipo específico.
            </li>
        </ul>

        <h3>
            Indicadores principales
        </h3>

        <div class="tabla-contenedor">

            <table>

                <thead>

                    <tr>
                        <th>Indicador</th>
                        <th>Descripción</th>
                    </tr>

                </thead>

                <tbody>

                    <tr>
                        <td>Empleados activos actuales</td>
                        <td>
                            Cantidad de empleados
                            actualmente activos.
                            Este indicador describe
                            la situación actual del personal.
                        </td>
                    </tr>

                    <tr>
                        <td>Liquidaciones cerradas</td>
                        <td>
                            Cantidad de liquidaciones
                            incluidas en el período
                            y filtros seleccionados.
                        </td>
                    </tr>

                    <tr>
                        <td>Total Remunerativo</td>
                        <td>
                            Suma de conceptos remunerativos
                            de las liquidaciones consideradas.
                        </td>
                    </tr>

                    <tr>
                        <td>Total No Remunerativo</td>
                        <td>
                            Suma de conceptos no remunerativos.
                        </td>
                    </tr>

                    <tr>
                        <td>Total Asignaciones</td>
                        <td>
                            Suma de asignaciones liquidadas.
                        </td>
                    </tr>

                    <tr>
                        <td>Total Descuentos</td>
                        <td>
                            Suma de descuentos aplicados.
                        </td>
                    </tr>

                    <tr>
                        <td>Total Neto</td>
                        <td>
                            Suma del neto resultante
                            de los registros considerados.
                        </td>
                    </tr>

                    <tr>
                        <td>Promedio Neto</td>
                        <td>
                            Promedio de neto
                            por registro empleado-liquidación
                            dentro del conjunto filtrado.
                        </td>
                    </tr>

                </tbody>

            </table>

        </div>

        <div class="aviso aviso-info">

            Los gráficos representan
            los datos de las liquidaciones
            y filtros seleccionados.
            Antes de comparar resultados,
            confirme siempre
            Desde, Hasta y Tipo de Liquidación.

        </div>

        <div class="aviso aviso-alerta">

            El indicador de empleados activos actuales
            describe la situación actual del personal
            y no debe confundirse
            con la cantidad de empleados liquidados
            dentro de un período histórico.

        </div>

    </section>


    <!-- =====================================================
         AUDITORÍA
    ====================================================== -->

    <section
        class="seccion"
        id="auditoria"
    >

        <div class="seccion-header">

            <div class="icono">
                🕵️
            </div>

            <h2>
                Reporte de Auditoría
            </h2>

        </div>

        <p>
            La auditoría permite consultar
            acciones relevantes realizadas
            por los usuarios dentro del sistema.
        </p>

        <h3>
            Información disponible
        </h3>

        <ul>

            <li>
                Fecha y hora de la acción.
            </li>

            <li>
                Usuario que realizó la operación.
            </li>

            <li>
                Rol del usuario.
            </li>

            <li>
                Módulo.
            </li>

            <li>
                Acción realizada.
            </li>

            <li>
                Entidad afectada.
            </li>

            <li>
                Detalle de datos anteriores
                y nuevos cuando corresponda.
            </li>

        </ul>

        <h3>
            Filtros frecuentes
        </h3>

        <ul>
            <li>Fecha desde.</li>
            <li>Fecha hasta.</li>
            <li>Usuario.</li>
            <li>Rol.</li>
            <li>Módulo.</li>
            <li>Acción.</li>
            <li>Entidad.</li>
        </ul>

        <div class="aviso aviso-importante">

            La auditoría debe utilizarse
            como herramienta de trazabilidad.
            No debe incluir contraseñas,
            códigos de recuperación
            ni otra información sensible
            de autenticación.

        </div>

        <div class="aviso aviso-info">

            Para mantener una presentación más limpia,
            la dirección IP no se muestra
            en el listado general
            ni en el PDF general de Auditoría.
            Cuando ese dato se encuentra registrado
            y corresponde consultarlo,
            puede permanecer disponible
            en el detalle de la operación.

        </div>

    </section>


    <!-- =====================================================
         CÓMO INTERPRETAR LOS DATOS
    ====================================================== -->

    <section
        class="seccion"
        id="interpretacion"
    >

        <div class="seccion-header">

            <div class="icono">
                🧠
            </div>

            <h2>
                Cómo interpretar los datos
            </h2>

        </div>

        <p>
            Los reportes muestran información
            registrada en SIGENMUNI
            según los filtros y reglas
            de cada consulta.
        </p>

        <div class="aviso aviso-info">

            <strong>Información histórica:</strong>
            modificar hoy una categoría,
            un concepto o un valor vigente
            no modifica automáticamente
            los detalles de una liquidación histórica
            ya cerrada.

        </div>

        <div class="aviso aviso-ok">

            <strong>Filtros y exportaciones:</strong>
            el PDF o Excel debe utilizar
            los mismos filtros aplicados
            al reporte en pantalla.
            Si los criterios son diferentes,
            también pueden cambiar
            los resultados obtenidos.

        </div>

        <div class="aviso aviso-alerta">

            <strong>Comparaciones:</strong>
            antes de comparar totales
            de dos períodos,
            verifique que utilicen
            tipos de liquidación,
            estados y rangos equivalentes.

        </div>

    </section>


    <!-- =====================================================
         RECOMENDACIONES
    ====================================================== -->

    <section
        class="seccion"
        id="recomendaciones"
    >

        <div class="seccion-header">

            <div class="icono">
                ✅
            </div>

            <h2>
                Recomendaciones de uso
            </h2>

        </div>

        <ul>

            <li>
                Aplique filtros antes de exportar
                cuando necesite información específica.
            </li>

            <li>
                Revise el total de registros encontrados.
            </li>

            <li>
                Compare la información
                con los datos del módulo de origen
                cuando detecte diferencias.
            </li>

            <li>
                En Reporte de Categorías,
                controle configuraciones incompletas.
            </li>

            <li>
                En Reporte de Liquidaciones,
                revise período, tipo y estado
                antes de interpretar totales.
            </li>

            <li>
                En Reporte de Conceptos,
                utilice Tipo de Concepto
                y Estado para acotar resultados.
            </li>

            <li>
                En Estadísticas,
                recuerde que los indicadores económicos
                se basan en liquidaciones cerradas
                que cumplen los filtros seleccionados.
            </li>

            <li>
                Utilice Auditoría
                para rastrear acciones relevantes,
                no como reemplazo
                de los reportes funcionales.
            </li>

            <li>
                Respete los permisos asignados
                al rol del usuario.
            </li>

        </ul>

    </section>


    <!-- =====================================================
         PROBLEMAS FRECUENTES
    ====================================================== -->

    <section
        class="seccion"
        id="problemas"
    >

        <div class="seccion-header">

            <div class="icono">
                ❓
            </div>

            <h2>
                Problemas frecuentes
            </h2>

        </div>


        <details>

            <summary>
                No veo un reporte en el menú
            </summary>

            <p>
                Revise si el rol del usuario
                tiene habilitado el permiso
                correspondiente.
                El acceso a cada reporte
                puede configurarse individualmente.
            </p>

        </details>


        <details>

            <summary>
                El PDF o Excel no coincide con la pantalla
            </summary>

            <p>
                Verifique que la exportación
                reciba los mismos filtros
                utilizados en el reporte
                y que los datos se consulten
                desde la misma fuente.
            </p>

        </details>


        <details>

            <summary>
                El Reporte de Categorías muestra "Sin valor vigente"
            </summary>

            <p>
                Revise si existen valores activos
                y vigentes para los conceptos
                correspondientes a esa categoría.
            </p>

        </details>


        <details>

            <summary>
                Una categoría aparece como Incompleta
            </summary>

            <p>
                Falta uno o más valores vigentes
                requeridos para los conceptos
                salariales principales.
            </p>

        </details>


        <details>

            <summary>
                No encuentro una acción en Auditoría
            </summary>

            <p>
                Revise los filtros aplicados
                y confirme que la operación
                corresponda a una acción
                registrada por el sistema.
            </p>

        </details>


        <details>

            <summary>
                Un total estadístico parece incorrecto
            </summary>

            <p>
                Revise Desde, Hasta,
                Tipo de Liquidación
                y confirme que las liquidaciones
                incluidas se encuentren cerradas.
                Compare también los datos
                con el reporte de origen.
            </p>

        </details>


        <details>

            <summary>
                El Reporte de Liquidaciones no devuelve resultados
            </summary>

            <p>
                Revise que el Período,
                Tipo de Liquidación
                y Estado seleccionados
                correspondan a registros existentes.
                Utilice Limpiar para volver
                a una consulta más general.
            </p>

        </details>


        <details>

            <summary>
                El Reporte de Conceptos muestra menos registros de los esperados
            </summary>

            <p>
                Revise el Tipo de Concepto,
                el Estado y cualquier texto
                ingresado en la búsqueda.
                Un filtro activo puede excluir
                conceptos que sí existen.
            </p>

        </details>


        <details>

            <summary>
                El Excel de Estadísticas tiene varios elementos
            </summary>

            <p>
                Es normal.
                La exportación puede contener
                hojas de datos y gráficos
                relacionados con el análisis realizado.
            </p>

        </details>


        <details>

            <summary>
                ¿Puedo acceder a un reporte escribiendo la URL?
            </summary>

            <p>
                El sistema debe validar
                el permiso del rol
                también en el archivo de entrada.
                Si el usuario no posee permiso,
                no debería poder acceder
                aunque escriba la dirección manualmente.
            </p>

        </details>

    </section>


    <!-- =====================================================
         PIE
    ====================================================== -->

    <div class="pie">

        <?php
        echo htmlspecialchars(
            $sistema['nombre']
            ?? 'SIGENMUNI',
            ENT_QUOTES,
            'UTF-8'
        );
        ?>

        -

        Guía de Consultas y Reportes

    </div>


</div>

</body>

</html>
