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

<title>Guía de Categorías - SIGENMUNI</title>

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
    background:linear-gradient(135deg,#7c3aed,#a855f7);
    color:white;
    border-radius:20px;
    padding:25px;
    box-shadow:0 10px 24px rgba(124,58,237,.18);
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

.btn-modulo{
    background:#2563eb;
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
    color:#7c3aed;
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
    background:#f5f3ff;
    border:1px solid #ddd6fe;
    color:#5b21b6;
    text-decoration:none;
    font-size:13px;
    font-weight:bold;
    text-align:center;
}

.indice a:hover{
    background:#ede9fe;
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
    background:#f5f3ff;
    border:1px solid #ddd6fe;
    font-size:24px;
}

.seccion h2{
    margin:0;
    color:#7c3aed;
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
    background:#7c3aed;
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
    background:#7c3aed;
    color:white;
}

td{
    color:#475569;
}


/* =========================================================
   ESTADOS VISUALES
========================================================= */

.estado-ejemplo{
    display:inline-block;
    padding:5px 9px;
    border-radius:999px;
    font-size:11px;
    font-weight:bold;
    white-space:nowrap;
}

.estado-completa{
    background:#dcfce7;
    color:#166534;
}

.estado-incompleta{
    background:#fef3c7;
    color:#92400e;
}

.estado-sin-valor{
    background:#f1f5f9;
    color:#64748b;
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
        color:#7c3aed;
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
    'Centro de Ayuda - Gestión de Categorías';

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
                    Guía de Gestión de Categorías
                </h1>

                <p>
                    Instrucciones para registrar, editar, consultar
                    y administrar categorías salariales en SIGENMUNI.
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
                Alta de categoría
            </a>

            <a href="#despues-alta">
                Después del alta
            </a>

            <a href="#editar">
                Editar categoría
            </a>

            <a href="#estado">
                Activar / Inactivar
            </a>

            <a href="#relacion-valores">
                Relación con conceptos
            </a>

            <a href="#vigencia-valores">
                Vigencia de valores
            </a>

            <a href="#reporte">
                Reporte de categorías
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
                🏷️
            </div>

            <h2>
                Objetivo del módulo
            </h2>

        </div>

        <p>
            El módulo <strong>Gestión de Categorías</strong>
            permite administrar las categorías salariales
            utilizadas por los empleados y por los valores
            configurados en los conceptos de liquidación.
        </p>

        <p>
            Cada categoría posee principalmente un código,
            un nombre y un estado.
        </p>

        <div class="aviso aviso-importante">

            <strong>Importante:</strong>
            los importes de
            <strong>101 - Sueldo Básico</strong>,
            <strong>102 - Dedicación Funcional</strong>
            y
            <strong>104 - Suplemento Especial</strong>
            se administran desde
            <strong>Gestión de Conceptos → Valores del Concepto</strong>.
            No son importes propios del registro de la categoría.

        </div>

        <div class="aviso aviso-info">

            La categoría identifica el grupo salarial del empleado.
            Los valores monetarios se mantienen por separado,
            con su propia vigencia.
            Esta separación permite actualizar importes
            sin modificar la categoría ni perder información histórica.

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
                Registrar una nueva categoría
            </h2>

        </div>

        <p>
            Para registrar una categoría nueva,
            ingrese al módulo Gestión de Categorías
            y utilice la opción correspondiente a nueva categoría.
        </p>

        <div class="pasos">

            <div class="paso">

                <div class="numero">
                    1
                </div>

                <div>

                    <strong>
                        Ingresar a Gestión de Categorías
                    </strong>

                    <p>
                        Desde el Menú Principal
                        seleccione el módulo correspondiente.
                    </p>

                </div>

            </div>


            <div class="paso">

                <div class="numero">
                    2
                </div>

                <div>

                    <strong>
                        Seleccionar Nueva Categoría
                    </strong>

                    <p>
                        Utilice el botón de alta disponible
                        en el listado principal.
                    </p>

                </div>

            </div>


            <div class="paso">

                <div class="numero">
                    3
                </div>

                <div>

                    <strong>
                        Ingresar el código
                    </strong>

                    <p>
                        El código identifica a la categoría
                        dentro del sistema.
                    </p>

                </div>

            </div>


            <div class="paso">

                <div class="numero">
                    4
                </div>

                <div>

                    <strong>
                        Ingresar el nombre
                    </strong>

                    <p>
                        Utilice una denominación clara,
                        por ejemplo: Categoría 19,
                        Intendente o Presidente HCD.
                    </p>

                </div>

            </div>


            <div class="paso">

                <div class="numero">
                    5
                </div>

                <div>

                    <strong>
                        Guardar la categoría
                    </strong>

                    <p>
                        Revise la información ingresada
                        antes de confirmar el alta.
                    </p>

                </div>

            </div>

        </div>

        <div class="aviso aviso-ok">

            Crear una categoría no implica que ya tenga
            valores salariales configurados.
            Después del alta debe revisarse
            <strong>Gestión de Conceptos → Valores del Concepto</strong>
            para completar los importes que correspondan.

        </div>

        <div class="aviso aviso-alerta">

            Antes de asignar una categoría nueva a empleados
            que vayan a ser liquidados,
            conviene verificar que tenga configurados
            los valores necesarios para la fecha de liquidación.

        </div>

    </section>


    <!-- =====================================================
         DESPUÉS DEL ALTA
    ====================================================== -->

    <section
        class="seccion"
        id="despues-alta"
    >

        <div class="seccion-header">

            <div class="icono">
                ✅
            </div>

            <h2>
                Qué hacer después de crear una categoría
            </h2>

        </div>

        <p>
            Una categoría nueva queda registrada en el sistema,
            pero sus valores salariales no se generan automáticamente.
            Antes de utilizarla en una liquidación,
            conviene completar y revisar su parametrización.
        </p>

        <div class="pasos">

            <div class="paso">

                <div class="numero">
                    1
                </div>

                <div>

                    <strong>
                        Revisar 101 - Sueldo Básico
                    </strong>

                    <p>
                        Ingrese a Gestión de Conceptos,
                        abra Valores del Concepto 101
                        y cargue un valor para la nueva categoría
                        con la vigencia correspondiente.
                    </p>

                </div>

            </div>


            <div class="paso">

                <div class="numero">
                    2
                </div>

                <div>

                    <strong>
                        Revisar 102 - Dedicación Funcional
                    </strong>

                    <p>
                        Configure explícitamente el valor
                        que corresponda para la categoría.
                        Aunque SIGENMUNI posee una regla de respaldo
                        para este concepto, la configuración explícita
                        es la opción recomendada.
                    </p>

                </div>

            </div>


            <div class="paso">

                <div class="numero">
                    3
                </div>

                <div>

                    <strong>
                        Revisar 104 - Suplemento Especial
                    </strong>

                    <p>
                        Cargue el valor correspondiente
                        cuando esa categoría deba utilizar
                        este concepto.
                    </p>

                </div>

            </div>


            <div class="paso">

                <div class="numero">
                    4
                </div>

                <div>

                    <strong>
                        Verificar las fechas de vigencia
                    </strong>

                    <p>
                        Controle Fecha Desde,
                        Fecha Hasta y estado activo
                        de cada valor antes de utilizar
                        la categoría en una liquidación.
                    </p>

                </div>

            </div>


            <div class="paso">

                <div class="numero">
                    5
                </div>

                <div>

                    <strong>
                        Revisar el Reporte de Categorías
                    </strong>

                    <p>
                        Utilice el reporte para comprobar
                        si la categoría aparece Completa
                        o si todavía falta algún valor principal.
                    </p>

                </div>

            </div>

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
                Editar una categoría
            </h2>

        </div>

        <p>
            La edición permite modificar
            la identificación de la categoría,
            como su código o nombre,
            según las opciones habilitadas en el sistema.
        </p>

        <p>
            Si la categoría ya fue utilizada por empleados,
            valores o liquidaciones,
            evite cambiar su código sin una necesidad administrativa real.
            Un cambio innecesario puede generar confusión
            al consultar información histórica.
        </p>

        <div class="pasos">

            <div class="paso">

                <div class="numero">
                    1
                </div>

                <div>

                    <strong>
                        Localizar la categoría
                    </strong>

                    <p>
                        Utilice el listado o el buscador
                        para encontrar el registro.
                    </p>

                </div>

            </div>


            <div class="paso">

                <div class="numero">
                    2
                </div>

                <div>

                    <strong>
                        Seleccionar Editar
                    </strong>

                    <p>
                        Abra el formulario de edición
                        correspondiente.
                    </p>

                </div>

            </div>


            <div class="paso">

                <div class="numero">
                    3
                </div>

                <div>

                    <strong>
                        Modificar solamente lo necesario
                    </strong>

                    <p>
                        Revise especialmente el código
                        y el nombre de la categoría.
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
                        y verifique el mensaje del sistema.
                    </p>

                </div>

            </div>

        </div>

        <div class="aviso aviso-alerta">

            Si una categoría ya se encuentra asignada
            a empleados o vinculada a Valores del Concepto,
            modifique sus datos con cuidado.
            Siempre que sea posible,
            conserve el mismo código y limite los cambios
            a correcciones realmente necesarias.

        </div>

    </section>


    <!-- =====================================================
         ESTADO
    ====================================================== -->

    <section
        class="seccion"
        id="estado"
    >

        <div class="seccion-header">

            <div class="icono">
                🔄
            </div>

            <h2>
                Activar o inactivar una categoría
            </h2>

        </div>

        <p>
            El estado permite controlar
            si una categoría se encuentra disponible
            para su utilización en el sistema.
        </p>

        <h3>
            Inactivar
        </h3>

        <p>
            Utilice esta opción cuando una categoría
            ya no deba utilizarse en nuevas operaciones.
        </p>

        <h3>
            Activar
        </h3>

        <p>
            Permite volver a habilitar
            una categoría previamente inactivada.
        </p>

        <div class="aviso aviso-alerta">

            Inactivar una categoría no elimina
            sus referencias históricas.
            Los empleados, valores y liquidaciones anteriores
            deben conservar su información.

        </div>

    </section>


    <!-- =====================================================
         RELACIÓN CON VALORES
    ====================================================== -->

    <section
        class="seccion"
        id="relacion-valores"
    >

        <div class="seccion-header">

            <div class="icono">
                💰
            </div>

            <h2>
                Relación con Valores del Concepto
            </h2>

        </div>

        <p>
            La categoría define
            <strong>a qué grupo salarial corresponde el empleado</strong>.
            El importe de determinados conceptos se configura
            de manera independiente.
        </p>

        <p>
            Para los conceptos con forma de cálculo
            <strong>VALOR POR CATEGORÍA</strong>,
            el sistema busca el valor correspondiente
            a la categoría salarial del empleado.
        </p>

        <h3>
            Conceptos salariales principales
        </h3>

        <div class="tabla-contenedor">

            <table>

                <thead>

                    <tr>
                        <th>Código</th>
                        <th>Concepto</th>
                        <th>Fuente del importe</th>
                    </tr>

                </thead>

                <tbody>

                    <tr>
                        <td>101</td>
                        <td>Sueldo Básico</td>
                        <td>
                            Valor vigente cargado
                            en Valores del Concepto
                            para la categoría correspondiente.
                        </td>
                    </tr>

                    <tr>
                        <td>102</td>
                        <td>Dedicación Funcional</td>
                        <td>
                            Valor vigente cargado
                            en Valores del Concepto
                            para la categoría correspondiente.
                        </td>
                    </tr>

                    <tr>
                        <td>104</td>
                        <td>Suplemento Especial</td>
                        <td>
                            Valor vigente cargado
                            en Valores del Concepto
                            para la categoría correspondiente.
                        </td>
                    </tr>

                </tbody>

            </table>

        </div>

        <div class="aviso aviso-info">

            Ejemplo:
            si el empleado pertenece a
            <strong>Categoría 19</strong>,
            SIGENMUNI busca los valores vigentes
            de los conceptos 101, 102 y 104
            asociados a esa categoría
            para la fecha utilizada en la liquidación.

        </div>

        <div class="aviso aviso-alerta">

            <strong>Regla especial del concepto 102:</strong>
            si no existe un valor vigente de
            <strong>Dedicación Funcional</strong>
            y existe un Sueldo Básico válido mayor que cero,
            la lógica actual de liquidación utiliza
            el Sueldo Básico como valor de respaldo para 102.
            Esta regla protege el cálculo,
            pero no reemplaza la recomendación
            de configurar el valor 102 de manera explícita.

        </div>

    </section>


    <!-- =====================================================
         VIGENCIA DE VALORES
    ====================================================== -->

    <section
        class="seccion"
        id="vigencia-valores"
    >

        <div class="seccion-header">

            <div class="icono">
                📅
            </div>

            <h2>
                Vigencia de los valores por categoría
            </h2>

        </div>

        <p>
            Que exista un valor cargado para una categoría
            no significa que necesariamente corresponda
            a la fecha que se está liquidando.
            El sistema debe encontrar un valor activo y vigente.
        </p>

        <div class="tabla-contenedor">

            <table>

                <thead>

                    <tr>
                        <th>Dato</th>
                        <th>Significado</th>
                        <th>Qué revisar</th>
                    </tr>

                </thead>

                <tbody>

                    <tr>
                        <td>Fecha Desde</td>
                        <td>
                            Primer día a partir del cual
                            el valor puede utilizarse.
                        </td>
                        <td>
                            Debe ser igual o anterior
                            a la fecha de referencia de la liquidación.
                        </td>
                    </tr>

                    <tr>
                        <td>Fecha Hasta</td>
                        <td>
                            Último día de vigencia del valor.
                        </td>
                        <td>
                            Puede quedar vacía cuando el valor
                            continúa vigente.
                        </td>
                    </tr>

                    <tr>
                        <td>Estado</td>
                        <td>
                            Indica si el valor está activo.
                        </td>
                        <td>
                            Un valor inactivo no debe utilizarse
                            en nuevos cálculos.
                        </td>
                    </tr>

                </tbody>

            </table>

        </div>

        <div class="aviso aviso-importante">

            Si existen varios valores históricos
            para una misma categoría y concepto,
            las fechas de vigencia permiten determinar
            cuál corresponde utilizar.
            No sobrescriba valores anteriores
            cuando el cambio debe comenzar en una fecha nueva.

        </div>

        <div class="aviso aviso-ok">

            Para actualizar un importe salarial,
            normalmente conviene conservar el registro anterior
            y crear un nuevo Valor del Concepto
            con la nueva Fecha Desde.
            De esta manera se mantiene el historial.

        </div>

    </section>


    <!-- =====================================================
         REPORTE
    ====================================================== -->

    <section
        class="seccion"
        id="reporte"
    >

        <div class="seccion-header">

            <div class="icono">
                📊
            </div>

            <h2>
                Reporte de Categorías
            </h2>

        </div>

        <p>
            El Reporte de Categorías muestra
            las categorías existentes junto con
            los valores vigentes de los conceptos
            salariales principales.
        </p>

        <h3>
            Estados posibles
        </h3>

        <div class="tabla-contenedor">

            <table>

                <thead>

                    <tr>
                        <th>Situación</th>
                        <th>Cómo se muestra</th>
                        <th>Significado</th>
                    </tr>

                </thead>

                <tbody>

                    <tr>
                        <td>
                            Existe un valor vigente
                        </td>
                        <td>
                            $ 500.000,00
                        </td>
                        <td>
                            La categoría posee un importe
                            activo y vigente para ese concepto.
                        </td>
                    </tr>

                    <tr>
                        <td>
                            No existe valor vigente
                        </td>
                        <td>
                            <span class="estado-ejemplo estado-sin-valor">
                                Sin valor vigente
                            </span>
                        </td>
                        <td>
                            No hay un registro activo y vigente
                            para ese concepto y categoría.
                        </td>
                    </tr>

                    <tr>
                        <td>
                            Tiene todos los valores principales
                        </td>
                        <td>
                            <span class="estado-ejemplo estado-completa">
                                Completa
                            </span>
                        </td>
                        <td>
                            La categoría posee
                            los valores vigentes requeridos
                            para los conceptos principales.
                        </td>
                    </tr>

                    <tr>
                        <td>
                            Falta uno o más valores
                        </td>
                        <td>
                            <span class="estado-ejemplo estado-incompleta">
                                Incompleta
                            </span>
                        </td>
                        <td>
                            La categoría necesita
                            completar su parametrización.
                        </td>
                    </tr>

                </tbody>

            </table>

        </div>

        <div class="aviso aviso-importante">

            <strong>Sin valor vigente</strong>
            no debe confundirse con
            <strong>$ 0,00</strong>.
            Si existe un registro cuyo monto es realmente cero,
            debe mostrarse como $ 0,00.
            Si no existe configuración vigente,
            debe mostrarse como Sin valor vigente.

        </div>

        <div class="aviso aviso-info">

            Una categoría marcada como
            <strong>Incompleta</strong>
            indica que falta uno o más valores principales
            en su parametrización.
            Este estado sirve como advertencia de configuración.
            En el caso particular del concepto 102,
            la liquidación puede aplicar la regla de respaldo
            explicada anteriormente,
            pero aun así es recomendable completar
            el valor explícito de Dedicación Funcional.

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
                Utilice códigos y nombres claros
                para identificar cada categoría.
            </li>

            <li>
                Evite crear categorías duplicadas.
            </li>

            <li>
                Antes de asignar una categoría a empleados,
                verifique que corresponda administrativamente.
            </li>

            <li>
                Después de crear una nueva categoría,
                revise los valores de los conceptos
                101, 102 y 104
                antes de utilizarla en liquidaciones.
            </li>

            <li>
                Verifique Fecha Desde,
                Fecha Hasta y estado activo
                de los Valores del Concepto.
            </li>

            <li>
                Aunque exista la regla de respaldo
                para 102 - Dedicación Funcional,
                configure su valor explícitamente
                siempre que corresponda.
            </li>

            <li>
                Controle el Reporte de Categorías
                para detectar configuraciones incompletas.
            </li>

            <li>
                No utilice $ 0,00
                para representar falta de configuración
                cuando en realidad no existe un valor vigente.
            </li>

            <li>
                Inactive categorías que ya no deban utilizarse,
                en lugar de eliminar referencias históricas.
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
                La categoría aparece pero dice "Sin valor vigente"
            </summary>

            <p>
                La categoría existe,
                pero no tiene un valor activo y vigente
                cargado para ese concepto.
                Revise Gestión de Conceptos
                y la sección Valores del Concepto.
            </p>

        </details>


        <details>

            <summary>
                La categoría aparece como "Incompleta"
            </summary>

            <p>
                Falta uno o más valores vigentes
                de los conceptos salariales principales.
                Revise Sueldo Básico,
                Dedicación Funcional
                y Suplemento Especial.
            </p>

        </details>


        <details>

            <summary>
                Creé una categoría pero no aparecen importes
            </summary>

            <p>
                Crear la categoría no crea automáticamente
                los Valores del Concepto.
                Los importes deben cargarse
                desde Gestión de Conceptos.
            </p>

        </details>


        <details>

            <summary>
                El reporte muestra $ 0,00
            </summary>

            <p>
                Verifique si realmente existe
                un valor vigente cuyo monto es cero.
                Si no existe ningún valor,
                el reporte debería mostrar
                "Sin valor vigente".
            </p>

        </details>


        <details>

            <summary>
                No puedo seleccionar una categoría para un empleado
            </summary>

            <p>
                Revise si la categoría se encuentra activa
                y disponible dentro de Gestión de Categorías.
            </p>

        </details>


        <details>

            <summary>
                Creé una categoría y la liquidación no toma los importes esperados
            </summary>

            <p>
                Revise que existan valores activos y vigentes
                para los conceptos principales,
                especialmente 101 - Sueldo Básico,
                102 - Dedicación Funcional
                y 104 - Suplemento Especial.
                Controle también que las fechas correspondan
                al período que está liquidando.
            </p>

        </details>


        <details>

            <summary>
                Falta el valor 102 pero aparece Dedicación Funcional
            </summary>

            <p>
                SIGENMUNI posee una regla de respaldo:
                cuando no existe un valor vigente de 102
                y existe un Sueldo Básico válido mayor que cero,
                puede utilizar el Sueldo Básico como Dedicación Funcional.
                De todos modos, se recomienda configurar
                el valor 102 explícitamente.
            </p>

        </details>


        <details>

            <summary>
                El valor está cargado pero no corresponde a la liquidación
            </summary>

            <p>
                Revise Fecha Desde, Fecha Hasta
                y el estado del Valor del Concepto.
                Un registro puede existir en la base
                y no estar vigente para la fecha utilizada
                por la liquidación.
            </p>

        </details>


        <details>

            <summary>
                ¿Puedo inactivar una categoría que ya fue utilizada?
            </summary>

            <p>
                Sí, siempre que corresponda funcionalmente.
                La inactivación debe conservar
                las referencias históricas existentes.
                No elimine categorías que ya tengan antecedentes
                si la inactivación resuelve la necesidad.
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

        Guía de Gestión de Categorías

    </div>


</div>

</body>

</html>
