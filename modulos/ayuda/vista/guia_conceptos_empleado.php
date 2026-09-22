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

<title>Guía de Conceptos por Empleado - SIGENMUNI</title>

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
   CABECERA DE LA GUÍA
========================================================= */

.cabecera{
    background:linear-gradient(135deg,#0f766e,#14b8a6);
    color:white;
    border-radius:20px;
    padding:25px;
    box-shadow:0 10px 24px rgba(15,118,110,.18);
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
    color:#0f766e;
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
    background:#f0fdfa;
    border:1px solid #99f6e4;
    color:#115e59;
    text-decoration:none;
    font-size:13px;
    font-weight:bold;
    text-align:center;
}

.indice a:hover{
    background:#ccfbf1;
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
    background:#f0fdfa;
    border:1px solid #99f6e4;
    font-size:24px;
}

.seccion h2{
    margin:0;
    color:#0f766e;
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
    background:#0f766e;
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
   TABLAS
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
    background:#0f766e;
    color:white;
}

td{
    color:#475569;
}


/* =========================================================
   ESTADOS
========================================================= */

.estado{
    display:inline-block;
    padding:5px 9px;
    border-radius:999px;
    font-size:11px;
    font-weight:bold;
    white-space:nowrap;
}

.estado-vigente{
    background:#dcfce7;
    color:#166534;
}

.estado-programado{
    background:#dbeafe;
    color:#1e40af;
}

.estado-finalizado{
    background:#fef3c7;
    color:#92400e;
}

.estado-inactivo{
    background:#fee2e2;
    color:#991b1b;
}

.estado-grid{
    display:grid;
    grid-template-columns:repeat(2,minmax(0,1fr));
    gap:12px;
    margin-top:15px;
}

.estado-card{
    border:1px solid #e2e8f0;
    border-radius:13px;
    padding:14px;
    background:#f8fafc;
}

.estado-card p{
    margin:8px 0 0;
}


/* =========================================================
   REGLAS DE PERÍODO LABORAL
========================================================= */

.reglas-grid{
    display:grid;
    grid-template-columns:repeat(2,minmax(0,1fr));
    gap:12px;
    margin-top:14px;
}

.regla-card{
    border:1px solid #e2e8f0;
    border-radius:13px;
    padding:15px;
    background:#fff;
}

.regla-card strong{
    display:block;
    margin-bottom:6px;
    color:#0f766e;
}

.regla-card p{
    margin:0;
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

    .estado-grid,
    .reglas-grid{
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
        color:#0f766e;
    }

    .seccion,
    .paso,
    .estado-card,
    .regla-card,
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
    'Centro de Ayuda - Conceptos por Empleado';

require __DIR__
    . '/../../../componentes/header_sigenmuni.php';

?>


<div class="contenedor">


    <!-- =====================================================
         CABECERA DE LA GUÍA
    ====================================================== -->

    <div class="cabecera">

        <div class="cabecera-top">

            <div>

                <h1>
                    Guía de Conceptos por Empleado
                </h1>

                <p>
                    Instrucciones para asignar, editar y administrar
                    conceptos particulares asociados a cada empleado,
                    respetando su vigencia y su historial laboral.
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

            <a href="#alta">
                Asignar concepto
            </a>

            <a href="#campos">
                Campos principales
            </a>

            <a href="#vigencia">
                Vigencia
            </a>

            <a href="#estados">
                Estados e historial
            </a>

            <a href="#periodo-laboral">
                Período laboral
            </a>

            <a href="#editar">
                Editar asignación
            </a>

            <a href="#liquidacion">
                Uso en liquidaciones
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
                📋
            </div>

            <h2>
                Objetivo del módulo
            </h2>

        </div>

        <p>
            El módulo <strong>Conceptos por Empleado</strong>
            permite asociar conceptos específicos a una persona determinada
            cuando ese concepto necesita información particular para ese empleado.
        </p>

        <p>
            Puede utilizarse, según la configuración del concepto,
            para registrar un monto manual, un porcentaje manual,
            una cantidad, una vigencia y una observación.
        </p>

        <div class="aviso aviso-info">

            <strong>Importante:</strong>
            este módulo no reemplaza a
            <strong>Gestión de Conceptos</strong>.
            Primero debe existir el concepto general y luego,
            solamente cuando corresponda,
            puede asociarse a un empleado.

        </div>

        <div class="aviso aviso-alerta">

            Los conceptos automáticos o administrados mediante
            <strong>Valor por Categoría</strong>
            se obtienen desde su configuración general.
            No deben transformarse en conceptos particulares del empleado
            salvo que la regla funcional del concepto así lo establezca.

        </div>

    </section>


    <!-- =====================================================
         ALTA
    ====================================================== -->

    <section
        class="seccion"
        id="alta"
    >

        <div class="seccion-header">

            <div class="icono">
                ➕
            </div>

            <h2>
                Asignar un concepto a un empleado
            </h2>

        </div>

        <p>
            Para crear una nueva asignación,
            utilice la opción correspondiente dentro de
            <strong>Conceptos por Empleado</strong>.
        </p>

        <div class="pasos">

            <div class="paso">

                <div class="numero">
                    1
                </div>

                <div>

                    <strong>
                        Seleccionar el empleado
                    </strong>

                    <p>
                        Elija el empleado al que se aplicará el concepto.
                        Para crear una nueva asignación,
                        el empleado debe encontrarse actualmente activo.
                    </p>

                </div>

            </div>


            <div class="paso">

                <div class="numero">
                    2
                </div>

                <div>

                    <strong>
                        Seleccionar el concepto
                    </strong>

                    <p>
                        Elija un concepto existente, activo
                        y habilitado por el sistema
                        para ser asignado a un empleado.
                    </p>

                </div>

            </div>


            <div class="paso">

                <div class="numero">
                    3
                </div>

                <div>

                    <strong>
                        Completar monto o porcentaje
                    </strong>

                    <p>
                        Si el concepto es manual,
                        complete el monto correspondiente.
                        Si trabaja por porcentaje,
                        complete el porcentaje particular
                        que corresponda al empleado.
                    </p>

                </div>

            </div>


            <div class="paso">

                <div class="numero">
                    4
                </div>

                <div>

                    <strong>
                        Indicar cantidad
                    </strong>

                    <p>
                        Complete la cantidad solamente
                        cuando la regla del concepto necesite unidades.
                        No debe cargarse por defecto si el concepto
                        no utiliza este dato.
                    </p>

                </div>

            </div>


            <div class="paso">

                <div class="numero">
                    5
                </div>

                <div>

                    <strong>
                        Definir la vigencia
                    </strong>

                    <p>
                        Complete Fecha Desde y,
                        cuando corresponda,
                        Fecha Hasta.
                        La vigencia debe quedar comprendida
                        dentro de un mismo período laboral continuo.
                    </p>

                </div>

            </div>


            <div class="paso">

                <div class="numero">
                    6
                </div>

                <div>

                    <strong>
                        Agregar observación
                    </strong>

                    <p>
                        Utilice este campo para registrar
                        información aclaratoria útil,
                        por ejemplo el motivo o referencia
                        de la asignación.
                    </p>

                </div>

            </div>


            <div class="paso">

                <div class="numero">
                    7
                </div>

                <div>

                    <strong>
                        Guardar la asignación
                    </strong>

                    <p>
                        Revise empleado, concepto, importe o porcentaje,
                        fechas y observación antes de confirmar.
                    </p>

                </div>

            </div>

        </div>

        <div class="aviso aviso-ok">

            Una vez guardada,
            la asignación podrá ser considerada
            por las liquidaciones que correspondan
            según su estado, vigencia y período laboral.

        </div>

    </section>


    <!-- =====================================================
         CAMPOS
    ====================================================== -->

    <section
        class="seccion"
        id="campos"
    >

        <div class="seccion-header">

            <div class="icono">
                🧾
            </div>

            <h2>
                Campos principales
            </h2>

        </div>

        <div class="tabla-contenedor">

            <table>

                <thead>

                    <tr>
                        <th>Campo</th>
                        <th>Descripción</th>
                        <th>Recomendación</th>
                    </tr>

                </thead>

                <tbody>

                    <tr>
                        <td>Empleado</td>
                        <td>
                            Persona a la que se asigna el concepto.
                        </td>
                        <td>
                            Verifique apellido, nombre y legajo.
                            Para nuevas asignaciones debe estar activo.
                        </td>
                    </tr>

                    <tr>
                        <td>Concepto</td>
                        <td>
                            Concepto particular que se aplicará al empleado.
                        </td>
                        <td>
                            Confirme que sea el concepto correcto,
                            que esté activo y habilitado para asignación.
                        </td>
                    </tr>

                    <tr>
                        <td>Monto manual</td>
                        <td>
                            Importe particular asignado al empleado.
                        </td>
                        <td>
                            Utilícelo cuando la forma de cálculo
                            del concepto requiera un valor manual.
                        </td>
                    </tr>

                    <tr>
                        <td>Porcentaje manual</td>
                        <td>
                            Porcentaje particular correspondiente al empleado.
                        </td>
                        <td>
                            Utilícelo en conceptos por porcentaje
                            y respete la base de cálculo definida
                            por la lógica del concepto.
                        </td>
                    </tr>

                    <tr>
                        <td>Cantidad</td>
                        <td>
                            Número de unidades que utiliza
                            un concepto cuando su regla lo requiere.
                        </td>
                        <td>
                            Complete únicamente cuando corresponda.
                        </td>
                    </tr>

                    <tr>
                        <td>Fecha Desde</td>
                        <td>
                            Primer día de vigencia de la asignación.
                        </td>
                        <td>
                            Debe pertenecer al período laboral
                            en el que se crea la asignación.
                        </td>
                    </tr>

                    <tr>
                        <td>Fecha Hasta</td>
                        <td>
                            Último día de vigencia de la asignación.
                        </td>
                        <td>
                            Puede quedar vacía cuando continúa vigente,
                            siempre dentro del período laboral actual.
                        </td>
                    </tr>

                    <tr>
                        <td>Observación</td>
                        <td>
                            Información adicional sobre la asignación.
                        </td>
                        <td>
                            Utilice una descripción breve y clara.
                        </td>
                    </tr>

                    <tr>
                        <td>Estado</td>
                        <td>
                            Permite conservar una asignación
                            activa o inactiva técnicamente.
                        </td>
                        <td>
                            El estado visual también considera
                            las fechas de vigencia.
                        </td>
                    </tr>

                </tbody>

            </table>

        </div>

    </section>


    <!-- =====================================================
         VIGENCIA
    ====================================================== -->

    <section
        class="seccion"
        id="vigencia"
    >

        <div class="seccion-header">

            <div class="icono">
                📅
            </div>

            <h2>
                Vigencia de la asignación
            </h2>

        </div>

        <p>
            La vigencia determina durante qué período
            puede considerarse el concepto particular del empleado.
        </p>

        <h3>
            Fecha Desde
        </h3>

        <p>
            Es el <strong>primer día</strong>
            en que la asignación corresponde.
            Antes de esa fecha el concepto no se aplica
            por esta asociación.
        </p>

        <h3>
            Fecha Hasta
        </h3>

        <p>
            Es el <strong>último día</strong>
            en que la asignación corresponde.
            La fecha es inclusiva:
            si Fecha Hasta es 10/09/2026,
            el concepto todavía puede corresponder el 10/09/2026
            y deja de estar vigente a partir del 11/09/2026.
        </p>

        <p>
            Si Fecha Hasta queda vacía,
            la asignación puede permanecer vigente
            mientras esté activa y el empleado continúe
            dentro del mismo período laboral.
        </p>

        <div class="aviso aviso-importante">

            Una asignación no puede atravesar
            una interrupción laboral.
            Fecha Desde y Fecha Hasta deben quedar
            dentro de un mismo período laboral continuo.

        </div>

    </section>


    <!-- =====================================================
         ESTADOS E HISTORIAL
    ====================================================== -->

    <section
        class="seccion"
        id="estados"
    >

        <div class="seccion-header">

            <div class="icono">
                🗂️
            </div>

            <h2>
                Estados e historial de la asignación
            </h2>

        </div>

        <p>
            El listado distingue el estado actual de una asignación
            combinando su estado técnico con las fechas de vigencia.
        </p>

        <div class="estado-grid">

            <div class="estado-card">

                <span class="estado estado-vigente">
                    VIGENTE
                </span>

                <p>
                    La asignación está activa,
                    ya comenzó y todavía no finalizó.
                    Puede visualizarse, editarse
                    o desactivarse cuando corresponda.
                </p>

            </div>


            <div class="estado-card">

                <span class="estado estado-programado">
                    PROGRAMADO
                </span>

                <p>
                    La asignación está activa,
                    pero su Fecha Desde es futura.
                    Todavía no interviene en las operaciones
                    anteriores a esa fecha.
                </p>

            </div>


            <div class="estado-card">

                <span class="estado estado-finalizado">
                    FINALIZADO POR FECHA
                </span>

                <p>
                    La asignación está conservada históricamente,
                    pero su Fecha Hasta ya terminó.
                    Se mantiene disponible para consulta.
                </p>

            </div>


            <div class="estado-card">

                <span class="estado estado-inactivo">
                    INACTIVO
                </span>

                <p>
                    La asignación fue desactivada.
                    Sus datos no se eliminan
                    y se conservan para mantener trazabilidad.
                </p>

            </div>

        </div>

        <div class="aviso aviso-alerta">

            <strong>Historial protegido:</strong>
            una asignación
            <strong>Finalizada por fecha</strong>
            se considera histórica.
            En la operatoria normal queda disponible
            solamente para <strong>Ver</strong>
            y no debe editarse para cambiar información pasada.

        </div>

        <div class="aviso aviso-info">

            Una asignación inactiva solo debe volver a habilitarse
            cuando las validaciones del sistema lo permitan
            y siga perteneciendo al período laboral correspondiente.
            No debe utilizarse esta acción para revivir
            una asignación perteneciente a un período laboral anterior.

        </div>

    </section>


    <!-- =====================================================
         PERÍODO LABORAL
    ====================================================== -->

    <section
        class="seccion"
        id="periodo-laboral"
    >

        <div class="seccion-header">

            <div class="icono">
                🧑‍💼
            </div>

            <h2>
                Relación con el período laboral
            </h2>

        </div>

        <p>
            Los conceptos por empleado están vinculados
            al historial laboral de la persona.
            Por ese motivo, una asignación debe pertenecer
            completamente a un único período laboral continuo.
        </p>

        <div class="reglas-grid">

            <div class="regla-card">

                <strong>
                    Nueva asignación
                </strong>

                <p>
                    Solo puede crearse para un empleado
                    que se encuentre actualmente activo.
                </p>

            </div>


            <div class="regla-card">

                <strong>
                    Sin cruce de períodos
                </strong>

                <p>
                    La asignación no puede comenzar
                    en un período laboral y terminar
                    después de una baja y posterior reingreso.
                </p>

            </div>


            <div class="regla-card">

                <strong>
                    Al inactivar al empleado
                </strong>

                <p>
                    Las asignaciones ya iniciadas que excedan
                    la fecha de inactivación se cierran
                    en esa fecha.
                    Las asignaciones futuras quedan inactivas.
                </p>

            </div>


            <div class="regla-card">

                <strong>
                    Al reactivar al empleado
                </strong>

                <p>
                    Se abre un nuevo período laboral.
                    Los conceptos anteriores
                    no se reabren automáticamente.
                </p>

            </div>

        </div>

        <div class="aviso aviso-ok">

            Si después de un reingreso vuelve a corresponder
            un concepto particular,
            debe crearse una <strong>nueva asignación</strong>
            dentro del nuevo período laboral.

        </div>

    </section>


    <!-- =====================================================
         EDITAR
    ====================================================== -->

    <section
        class="seccion"
        id="editar"
    >

        <div class="seccion-header">

            <div class="icono">
                ✏️
            </div>

            <h2>
                Editar una asignación
            </h2>

        </div>

        <p>
            La opción Editar permite modificar
            una asignación que todavía puede administrarse
            dentro de su período vigente o programado.
        </p>

        <div class="pasos">

            <div class="paso">

                <div class="numero">
                    1
                </div>

                <div>

                    <strong>
                        Localizar la asignación
                    </strong>

                    <p>
                        Busque el registro correspondiente
                        en el listado de Conceptos por Empleado.
                    </p>

                </div>

            </div>


            <div class="paso">

                <div class="numero">
                    2
                </div>

                <div>

                    <strong>
                        Revisar su estado
                    </strong>

                    <p>
                        Confirme si está Vigente o Programado.
                        Los registros Finalizados por fecha
                        quedan solo para consulta histórica.
                    </p>

                </div>

            </div>


            <div class="paso">

                <div class="numero">
                    3
                </div>

                <div>

                    <strong>
                        Modificar los datos necesarios
                    </strong>

                    <p>
                        Revise monto, porcentaje, cantidad,
                        vigencia y observación,
                        respetando siempre el período laboral.
                    </p>

                </div>

            </div>


            <div class="paso">

                <div class="numero">
                    4
                </div>

                <div>

                    <strong>
                        Guardar cambios
                    </strong>

                    <p>
                        Confirme la actualización
                        y verifique nuevamente
                        estado y fechas en el listado.
                    </p>

                </div>

            </div>

        </div>

        <div class="aviso aviso-importante">

            Si el cambio corresponde a una nueva etapa,
            una nueva vigencia o un nuevo período laboral,
            no sobrescriba el historial anterior.
            Finalice la asignación que corresponda
            y cree una nueva cuando sea necesario.

        </div>

    </section>


    <!-- =====================================================
         USO EN LIQUIDACIONES
    ====================================================== -->

    <section
        class="seccion"
        id="liquidacion"
    >

        <div class="seccion-header">

            <div class="icono">
                💵
            </div>

            <h2>
                Cómo intervienen en las liquidaciones
            </h2>

        </div>

        <p>
            Durante una liquidación,
            SIGENMUNI evalúa si el concepto particular
            corresponde al empleado para la fecha y el período
            que se están procesando.
        </p>

        <ul>

            <li>
                La asignación debe corresponder
                al período de vigencia aplicable.
            </li>

            <li>
                Debe respetar el período laboral
                al que pertenece.
            </li>

            <li>
                Un concepto manual utiliza
                el monto particular cargado para el empleado.
            </li>

            <li>
                Un concepto por porcentaje utiliza
                el porcentaje particular y la base de cálculo
                definida por la lógica del concepto.
            </li>

            <li>
                La cantidad se utiliza únicamente
                en los conceptos cuya regla de cálculo
                requiere unidades.
            </li>

        </ul>

        <div class="aviso aviso-alerta">

            <strong>Conceptos manuales:</strong>
            el monto manual asignado al empleado
            no se prorratea automáticamente
            por los días liquidados del mes.
            Debe revisarse según la regla funcional
            que corresponda al concepto.

        </div>

        <div class="aviso aviso-info">

            Antes de procesar una liquidación,
            revise los conceptos particulares
            que deberían intervenir,
            especialmente su estado,
            Fecha Desde, Fecha Hasta,
            monto, porcentaje y observación.

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
                Verifique siempre empleado y concepto
                antes de guardar.
            </li>

            <li>
                Utilice monto manual o porcentaje manual
                solamente cuando corresponda
                a la forma de cálculo del concepto.
            </li>

            <li>
                Mantenga correctamente
                Fecha Desde y Fecha Hasta.
            </li>

            <li>
                Recuerde que Fecha Hasta
                representa el último día de vigencia.
            </li>

            <li>
                No cree una asignación
                que atraviese una baja y un reingreso.
            </li>

            <li>
                Evite superponer asignaciones
                del mismo concepto para el mismo empleado
                sin una razón funcional válida.
            </li>

            <li>
                Utilice observaciones
                para dejar aclaraciones útiles.
            </li>

            <li>
                No modifique registros
                Finalizados por fecha.
                Consérvelos como historial.
            </li>

            <li>
                Después de reactivar a un empleado,
                cree una nueva asignación
                si el concepto vuelve a corresponder.
            </li>

            <li>
                Antes de procesar una liquidación,
                revise las asignaciones
                que deban intervenir.
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
                No encuentro al empleado para seleccionarlo
            </summary>

            <p>
                Para crear una nueva asignación,
                verifique que el empleado exista
                y se encuentre actualmente activo.
            </p>

        </details>


        <details>

            <summary>
                No aparece el concepto que necesito
            </summary>

            <p>
                Verifique que el concepto exista
                en Gestión de Conceptos,
                esté activo y se encuentre habilitado
                para asignación por empleado.
            </p>

        </details>


        <details>

            <summary>
                La asignación está activa pero no se aplica
            </summary>

            <p>
                Revise Fecha Desde, Fecha Hasta,
                el período laboral del empleado,
                la configuración del concepto
                y la fecha de la operación que se está procesando.
            </p>

        </details>


        <details>

            <summary>
                ¿Qué significa Programado?
            </summary>

            <p>
                Significa que la asignación está activa
                pero su Fecha Desde todavía es futura.
                Se aplicará únicamente cuando corresponda
                según esa fecha.
            </p>

        </details>


        <details>

            <summary>
                ¿Por qué aparece Finalizado por fecha?
            </summary>

            <p>
                Porque la asignación conserva estado técnico activo,
                pero su Fecha Hasta ya terminó.
                El registro se mantiene como antecedente histórico.
            </p>

        </details>


        <details>

            <summary>
                ¿Por qué no puedo editar una asignación finalizada?
            </summary>

            <p>
                Las asignaciones Finalizadas por fecha
                se protegen para no modificar información histórica.
                En la operatoria normal quedan disponibles
                solamente para consulta.
            </p>

        </details>


        <details>

            <summary>
                La fecha ingresada no pertenece al período laboral
            </summary>

            <p>
                La asignación debe quedar completamente
                dentro de un único período laboral continuo.
                Revise Fecha Desde, Fecha Hasta
                y el historial laboral del empleado.
            </p>

        </details>


        <details>

            <summary>
                Reactivé al empleado y el concepto anterior no volvió
            </summary>

            <p>
                Es el comportamiento esperado.
                La reactivación genera un nuevo período laboral
                y no reabre automáticamente
                las asignaciones del período anterior.
                Si el concepto vuelve a corresponder,
                debe crearse una nueva asignación.
            </p>

        </details>


        <details>

            <summary>
                ¿Monto manual y porcentaje manual son obligatorios?
            </summary>

            <p>
                No necesariamente.
                Debe completarse el dato
                que corresponda a la forma de cálculo
                del concepto seleccionado.
            </p>

        </details>


        <details>

            <summary>
                ¿Puedo dejar Fecha Hasta vacía?
            </summary>

            <p>
                Sí, cuando la asignación continúa vigente
                y pertenece al período laboral actual.
                Si el empleado posteriormente es inactivado,
                el sistema ajustará las asignaciones
                según las reglas de cierre correspondientes.
            </p>

        </details>


        <details>

            <summary>
                ¿Los conceptos manuales se prorratean por días?
            </summary>

            <p>
                No de forma automática.
                El monto manual se utiliza según la regla
                definida para ese concepto.
                Si el caso requiere un importe diferente,
                debe revisarse antes de procesar la liquidación.
            </p>

        </details>


        <details>

            <summary>
                ¿Inactivar borra la observación o los datos cargados?
            </summary>

            <p>
                No.
                La inactivación conserva la asignación
                y sus datos históricos.
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

        Guía de Conceptos por Empleado

    </div>


</div>

</body>

</html>
