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

<title>Guía de Liquidaciones - SIGENMUNI</title>

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
    background:linear-gradient(135deg,#16a34a,#22c55e);
    color:white;
    border-radius:20px;
    padding:25px;
    box-shadow:0 10px 24px rgba(22,163,74,.18);
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
    background:#f59e0b;
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
    color:#16a34a;
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
    background:#f0fdf4;
    border:1px solid #bbf7d0;
    color:#166534;
    text-decoration:none;
    font-size:13px;
    font-weight:bold;
    text-align:center;
}

.indice a:hover{
    background:#dcfce7;
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
    background:#f0fdf4;
    border:1px solid #bbf7d0;
    font-size:24px;
}

.seccion h2{
    margin:0;
    color:#16a34a;
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
    background:#16a34a;
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
    background:#16a34a;
    color:white;
}

td{
    color:#475569;
}

.estado{
    display:inline-block;
    padding:5px 9px;
    border-radius:999px;
    font-size:11px;
    font-weight:bold;
    white-space:nowrap;
}

.estado-borrador{
    background:#fef3c7;
    color:#92400e;
}

.estado-cerrada{
    background:#dcfce7;
    color:#166534;
}

.estado-anulada{
    background:#fee2e2;
    color:#991b1b;
}


/* =========================================================
   TIPOS DE LIQUIDACIÓN
========================================================= */

.tipos-grid{
    display:grid;
    grid-template-columns:repeat(2,minmax(0,1fr));
    gap:15px;
    margin-top:16px;
}

.tipo-liquidacion-card{
    position:relative;
    overflow:hidden;
    border:1px solid #e2e8f0;
    border-radius:15px;
    background:#fff;
    padding:18px;
}

.tipo-liquidacion-card::before{
    content:"";
    position:absolute;
    left:0;
    top:0;
    bottom:0;
    width:6px;
    background:#16a34a;
}

.tipo-liquidacion-card h3{
    margin:0 0 9px;
    padding-left:5px;
    color:#1f2937;
    font-size:18px;
}

.tipo-liquidacion-card p{
    margin:0 0 12px;
    padding-left:5px;
}

.tipo-liquidacion-card .antes-procesar{
    margin-top:12px;
    padding:11px 12px;
    border-radius:11px;
    background:#f8fafc;
    border:1px solid #e2e8f0;
    color:#475569;
    font-size:13px;
    line-height:1.5;
}

.tipo-liquidacion-card .antes-procesar strong{
    color:#1f2937;
}

.tipo-mensual::before{
    background:#16a34a;
}

.tipo-complementaria::before{
    background:#d97706;
}

.tipo-aguinaldo::before{
    background:#7c3aed;
}

.tipo-complementaria-sac::before{
    background:#9333ea;
}

.tipo-protocolares::before{
    background:#0891b2;
}

.tipo-protocolares{
    grid-column:1 / -1;
}

@media(max-width:700px){

    .tipos-grid{
        grid-template-columns:1fr;
    }

    .tipo-protocolares{
        grid-column:auto;
    }
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
        color:#16a34a;
    }

    .seccion,
    .paso,
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
    'Centro de Ayuda - Gestión de Liquidaciones';

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
                    Guía de Gestión de Liquidaciones
                </h1>

                <p>
                    Instrucciones para crear, procesar, revisar
                    y administrar liquidaciones de haberes en SIGENMUNI.
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
                Objetivo del módulo
            </a>

            <a href="#tipos">
                Tipos de liquidación
            </a>

            <a href="#nueva">
                Nueva liquidación
            </a>

            <a href="#procesar">
                Procesar liquidación
            </a>

            <a href="#revision">
                Revisar resultados
            </a>

            <a href="#recibos">
                Recibos de sueldo
            </a>

            <a href="#estados">
                Estados
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
                🧾
            </div>

            <h2>
                Objetivo del módulo
            </h2>

        </div>

        <p>
            El módulo <strong>Gestión de Liquidaciones</strong>
            permite calcular los haberes,
            descuentos, asignaciones,
            aportes y netos
            correspondientes a los empleados.
        </p>

        <p>
            La liquidación utiliza la información
            configurada previamente en empleados,
            categorías, conceptos
            y conceptos por empleado.
        </p>

        <div class="aviso aviso-importante">

            <strong>Importante:</strong>
            antes de procesar una liquidación
            conviene revisar que los datos base
            estén correctamente configurados,
            especialmente categorías,
            valores vigentes
            y conceptos activos.

        </div>

    </section>


    <!-- =====================================================
         TIPOS DE LIQUIDACIÓN
    ====================================================== -->

    <section
        class="seccion"
        id="tipos"
    >

        <div class="seccion-header">

            <div class="icono">
                📋
            </div>

            <h2>
                Tipos de liquidación
            </h2>

        </div>


        <p>
            SIGENMUNI permite trabajar con distintos tipos de liquidación.
            Cada uno tiene un objetivo y una preparación previa diferente.
        </p>


        <div class="tipos-grid">


            <!-- MENSUAL -->

            <article class="tipo-liquidacion-card tipo-mensual">

                <h3>
                    🟢 Mensual
                </h3>

                <p>
                    Es la liquidación habitual de haberes del mes.
                    Incluye al personal que posee relación laboral dentro
                    del período correspondiente.
                </p>

                <p>
                    Permite indicar días liquidados y si corresponde
                    aplicar Presentismo. Cuando existen días parciales,
                    los conceptos salariales que corresponden se calculan
                    sobre el convenio mensual de 30 días.
                </p>

                <div class="antes-procesar">
                    <strong>Antes de procesar:</strong>
                    revise Novedades, días liquidados, Presentismo
                    y los valores vigentes de los conceptos.
                </div>

            </article>


            <!-- COMPLEMENTARIA DE HABERES -->

            <article class="tipo-liquidacion-card tipo-complementaria">

                <h3>
                    🟠 Complementaria de Haberes
                </h3>

                <p>
                    Se utiliza para realizar una liquidación adicional
                    de haberes fuera de la liquidación mensual habitual.
                </p>

                <p>
                    El personal debe seleccionarse previamente.
                    Solo se procesan los empleados incluidos en la
                    complementaria y pueden cargarse días liquidados
                    y Presentismo para cada caso.
                </p>

                <div class="antes-procesar">
                    <strong>Antes de procesar:</strong>
                    ingrese a Personal y Novedades, seleccione
                    los empleados y controle sus días y Presentismo.
                </div>

            </article>


            <!-- AGUINALDO -->

            <article class="tipo-liquidacion-card tipo-aguinaldo">

                <h3>
                    🟣 Aguinaldo
                </h3>

                <p>
                    Corresponde al Sueldo Anual Complementario (SAC)
                    y se utiliza para los períodos de junio y diciembre.
                </p>

                <p>
                    El sistema calcula el importe a partir de la base
                    remunerativa correspondiente y de los días SAC
                    devengados, con un máximo de 180 días por semestre.
                </p>

                <div class="antes-procesar">
                    <strong>Antes de procesar:</strong>
                    revise Novedades SAC, los días devengados,
                    los días ya pagados y los días pendientes.
                </div>

            </article>


            <!-- COMPLEMENTARIA SAC -->

            <article class="tipo-liquidacion-card tipo-complementaria-sac">

                <h3>
                    🟪 Complementaria de SAC
                </h3>

                <p>
                    Permite liquidar una parte pendiente o adicional
                    del Sueldo Anual Complementario dentro del semestre.
                </p>

                <p>
                    Debe seleccionarse previamente el personal y los días
                    SAC a liquidar. El sistema controla los días ya pagados
                    en otras liquidaciones de SAC cerradas del mismo semestre.
                </p>

                <div class="antes-procesar">
                    <strong>Antes de procesar:</strong>
                    ingrese a Personal y Días SAC, seleccione
                    al menos un empleado y verifique los días pendientes.
                </div>

            </article>


            <!-- GASTOS PROTOCOLARES -->

            <article class="tipo-liquidacion-card tipo-protocolares">

                <h3>
                    🔵 Gastos Protocolares
                </h3>

                <p>
                    Es una liquidación especial e independiente de la
                    liquidación mensual. El importe se carga manualmente
                    para cada empleado que corresponda.
                </p>

                <p>
                    También puede indicarse si corresponde aplicar previsión
                    social. Cuando corresponde, el sistema calcula los
                    descuentos y aportes previsionales definidos para este tipo.
                </p>

                <div class="antes-procesar">
                    <strong>Antes de procesar:</strong>
                    cargue los Gastos Protocolares de cada empleado
                    y verifique el importe y la condición previsional.
                </div>

            </article>


        </div>


        <div class="aviso aviso-info">

            <strong>Período y Fecha de Liquidación:</strong>
            la fecha de liquidación debe corresponder al mes y año
            del período seleccionado. Por ejemplo, una liquidación
            de Aguinaldo del período <strong>2026-12</strong>
            debe utilizar una fecha dentro de diciembre de 2026.

        </div>


        <div class="aviso aviso-ok">

            <strong>Procesamiento seguro:</strong>
            si SIGENMUNI detecta una condición inválida o un dato obligatorio
            faltante, la liquidación no se cierra y los cambios realizados
            durante ese intento se revierten para evitar datos parciales
            o inconsistentes.

        </div>

    </section>


    <!-- =====================================================
         NUEVA LIQUIDACIÓN
    ====================================================== -->

    <section
        class="seccion"
        id="nueva"
    >

        <div class="seccion-header">

            <div class="icono">
                ➕
            </div>

            <h2>
                Crear una nueva liquidación
            </h2>

        </div>

        <p>
            Para comenzar una liquidación,
            debe crearse primero su cabecera
            con los datos generales del proceso.
        </p>

        <div class="pasos">

            <div class="paso">

                <div class="numero">
                    1
                </div>

                <div>

                    <strong>
                        Seleccionar Nueva Liquidación
                    </strong>

                    <p>
                        Inicie una nueva liquidación
                        desde la opción disponible
                        en el módulo.
                    </p>

                </div>

            </div>


            <div class="paso">

                <div class="numero">
                    2
                </div>

                <div>

                    <strong>
                        Seleccionar el tipo
                    </strong>

                    <p>
                        Elija el tipo de liquidación que corresponda.
                        Consulte la sección
                        <strong>Tipos de liquidación</strong>
                        para identificar el objetivo y la preparación
                        previa de cada alternativa.
                    </p>

                </div>

            </div>


            <div class="paso">

                <div class="numero">
                    3
                </div>

                <div>

                    <strong>
                        Definir el período
                    </strong>

                    <p>
                        Complete el período
                        al que corresponde
                        la liquidación.
                    </p>

                </div>

            </div>


            <div class="paso">

                <div class="numero">
                    4
                </div>

                <div>

                    <strong>
                        Indicar fecha y descripción
                    </strong>

                    <p>
                        Complete los datos complementarios
                        solicitados por el sistema.
                    </p>

                </div>

            </div>


            <div class="paso">

                <div class="numero">
                    5
                </div>

                <div>

                    <strong>
                        Guardar la cabecera
                    </strong>

                    <p>
                        Revise los datos
                        antes de continuar
                        con el procesamiento.
                    </p>

                </div>

            </div>

        </div>

        <div class="aviso aviso-info">

            Una liquidación recién creada
            debe considerarse una instancia
            previa al cálculo definitivo.
            Revise siempre sus datos
            antes de procesarla.

        </div>

    </section>


    <!-- =====================================================
         PROCESAR
    ====================================================== -->

    <section
        class="seccion"
        id="procesar"
    >

        <div class="seccion-header">

            <div class="icono">
                ⚙️
            </div>

            <h2>
                Procesar la liquidación
            </h2>

        </div>

        <p>
            Al procesar,
            SIGENMUNI calcula los importes
            correspondientes a cada empleado incluido.
        </p>

        <h3>
            Información utilizada
        </h3>

        <ul>

            <li>
                Datos del empleado y su categoría salarial.
            </li>

            <li>
                Valores vigentes de los conceptos.
            </li>

            <li>
                Conceptos particulares
                asignados al empleado.
            </li>

            <li>
                Antigüedad y demás reglas
                definidas por la lógica de cálculo.
            </li>

            <li>
                Descuentos y aportes
                configurados en el sistema.
            </li>

        </ul>

        <div class="aviso aviso-alerta">

            Si falta información obligatoria,
            existe una configuración incorrecta
            o no se completó una preparación previa,
            SIGENMUNI interrumpe el procesamiento.
            La liquidación no se cierra y los cambios
            realizados durante ese intento se revierten.

        </div>

    </section>


    <!-- =====================================================
         REVISIÓN
    ====================================================== -->

    <section
        class="seccion"
        id="revision"
    >

        <div class="seccion-header">

            <div class="icono">
                🔎
            </div>

            <h2>
                Revisar los resultados
            </h2>

        </div>

        <p>
            Una vez procesada la liquidación,
            revise los importes calculados
            antes de considerarla definitiva.
        </p>

        <h3>
            Totales principales
        </h3>

        <div class="tabla-contenedor">

            <table>

                <thead>

                    <tr>
                        <th>Total</th>
                        <th>Descripción</th>
                    </tr>

                </thead>

                <tbody>

                    <tr>
                        <td>Total Remunerativo</td>
                        <td>
                            Suma de conceptos remunerativos
                            aplicados al empleado.
                        </td>
                    </tr>

                    <tr>
                        <td>Total No Remunerativo</td>
                        <td>
                            Suma de conceptos
                            no remunerativos.
                        </td>
                    </tr>

                    <tr>
                        <td>Total Asignaciones</td>
                        <td>
                            Suma de asignaciones
                            que corresponden al empleado.
                        </td>
                    </tr>

                    <tr>
                        <td>Total Descuentos</td>
                        <td>
                            Suma de descuentos aplicados.
                        </td>
                    </tr>

                    <tr>
                        <td>Neto</td>
                        <td>
                            Resultado final
                            de la liquidación del empleado.
                        </td>
                    </tr>

                </tbody>

            </table>

        </div>

        <div class="aviso aviso-ok">

            Antes de cerrar una liquidación,
            compare los resultados
            con la configuración vigente
            y revise los casos particulares
            que correspondan.

        </div>

    </section>


    <!-- =====================================================
         RECIBOS
    ====================================================== -->

    <section
        class="seccion"
        id="recibos"
    >

        <div class="seccion-header">

            <div class="icono">
                📄
            </div>

            <h2>
                Recibos de sueldo
            </h2>

        </div>

        <p>
            El recibo muestra
            los conceptos liquidados
            para un empleado
            y los totales resultantes.
        </p>

        <h3>
            Orden general de conceptos
        </h3>

        <ol>

            <li>
                Conceptos remunerativos.
            </li>

            <li>
                Conceptos no remunerativos.
            </li>

            <li>
                Asignaciones.
            </li>

            <li>
                Descuentos.
            </li>

            <li>
                Aportes patronales,
                cuando corresponda su visualización.
            </li>

        </ol>

        <p>
            Los recibos pueden visualizarse
            e imprimirse en formato A4
            según las opciones disponibles.
        </p>

        <div class="aviso aviso-info">

            Los aportes patronales
            no forman parte del neto
            que percibe el empleado.
            Se informan separadamente
            cuando corresponde.

        </div>

    </section>


    <!-- =====================================================
         ESTADOS
    ====================================================== -->

    <section
        class="seccion"
        id="estados"
    >

        <div class="seccion-header">

            <div class="icono">
                🔄
            </div>

            <h2>
                Estados de una liquidación
            </h2>

        </div>

        <div class="tabla-contenedor">

            <table>

                <thead>

                    <tr>
                        <th>Estado</th>
                        <th>Descripción</th>
                        <th>Recomendación</th>
                    </tr>

                </thead>

                <tbody>

                    <tr>
                        <td>
                            <span class="estado estado-borrador">
                                BORRADOR
                            </span>
                        </td>
                        <td>
                            Liquidación creada
                            y todavía susceptible
                            de revisión o preparación.
                        </td>
                        <td>
                            Verifique datos
                            antes de avanzar.
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <span class="estado estado-cerrada">
                                CERRADA
                            </span>
                        </td>
                        <td>
                            El procesamiento finalizó correctamente
                            y la liquidación quedó cerrada.
                        </td>
                        <td>
                            Revise resultados y recibos.
                            Solo un administrador debe deshacer
                            el procesamiento cuando sea necesario.
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <span class="estado estado-anulada">
                                ANULADA
                            </span>
                        </td>
                        <td>
                            Liquidación que dejó
                            de ser válida operativamente.
                        </td>
                        <td>
                            Utilice la anulación
                            solo cuando corresponda.
                        </td>
                    </tr>

                </tbody>

            </table>

        </div>

        <div class="aviso aviso-importante">

            Los estados disponibles
            pueden depender del flujo
            implementado en SIGENMUNI.
            Antes de cambiar el estado,
            verifique que la operación
            sea la correcta.

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
                Verifique el período
                y el tipo de liquidación
                antes de procesar.
            </li>

            <li>
                Controle que los empleados
                que deban intervenir
                estén correctamente configurados.
            </li>

            <li>
                Revise que las categorías
                tengan valores vigentes
                para los conceptos necesarios.
            </li>

            <li>
                Controle los conceptos
                particulares por empleado
                antes del procesamiento.
            </li>

            <li>
                Revise los totales
                de cada empleado
                y los totales generales.
            </li>

            <li>
                No cierre una liquidación
                sin revisar primero
                los recibos y resultados.
            </li>

            <li>
                Utilice los reportes
                para validar información
                cuando sea necesario.
            </li>

            <li>
                Conserve el historial
                y utilice la anulación
                en lugar de eliminar
                información relevante.
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
                Un empleado no aparece en la liquidación
            </summary>

            <p>
                Revise el estado del empleado,
                su configuración administrativa
                y las reglas utilizadas
                para incluir personal
                en el proceso correspondiente.
            </p>

        </details>


        <details>

            <summary>
                Un concepto aparece con importe incorrecto
            </summary>

            <p>
                Revise la Forma de Cálculo,
                el valor vigente,
                la categoría del empleado,
                la base de cálculo
                y cualquier concepto particular
                asociado al empleado.
            </p>

        </details>


        <details>

            <summary>
                Falta Sueldo Básico, Dedicación o Suplemento
            </summary>

            <p>
                Verifique que existan
                valores activos y vigentes
                para los conceptos 101, 102 y 104
                en la categoría correspondiente.
            </p>

        </details>


        <details>

            <summary>
                El neto no coincide con lo esperado
            </summary>

            <p>
                Revise los totales remunerativos,
                descuentos,
                no remunerativos
                y asignaciones.
                Controle también los conceptos
                particulares del empleado.
            </p>

        </details>


        <details>

            <summary>
                Modifiqué un valor después de procesar
            </summary>

            <p>
                La liquidación ya procesada
                puede conservar los detalles
                calculados en ese momento.
                Revise el flujo correspondiente
                antes de reprocesar
                o modificar una liquidación existente.
            </p>

        </details>


        <details>

            <summary>
                ¿Los aportes patronales modifican el neto?
            </summary>

            <p>
                No. Los aportes patronales
                corresponden al empleador
                y no deben restarse
                del neto que percibe el empleado.
            </p>

        </details>


        <details>

            <summary>
                ¿Qué debo revisar antes de cerrar?
            </summary>

            <p>
                Controle empleados,
                conceptos,
                vigencias,
                descuentos,
                asignaciones,
                totales,
                recibos
                y cualquier caso particular
                antes de confirmar el cierre.
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

        Guía de Gestión de Liquidaciones

    </div>


</div>

</body>

</html>
