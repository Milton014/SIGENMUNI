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

<title>Guía de Empleados - SIGENMUNI</title>

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
   ÍNDICE
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
   TARJETAS
========================================================= */

.tarjetas-grid{
    display:grid;
    grid-template-columns:repeat(2,minmax(0,1fr));
    gap:12px;
    margin-top:15px;
}

.tarjeta{
    border:1px solid #e2e8f0;
    border-radius:13px;
    padding:15px;
    background:#f8fafc;
}

.tarjeta strong{
    display:block;
    margin-bottom:6px;
    color:#0f766e;
}

.tarjeta p{
    margin:0;
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

.estado-activo{
    background:#dcfce7;
    color:#166534;
}

.estado-inactivo{
    background:#fee2e2;
    color:#991b1b;
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

    .tarjetas-grid{
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
    .tarjeta,
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
    'Centro de Ayuda - Gestión de Empleados';

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
                    Guía de Gestión de Empleados
                </h1>

                <p>
                    Instrucciones para registrar, consultar, editar
                    y administrar empleados, su historial laboral
                    y su participación en las liquidaciones.
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
                Alta de empleado
            </a>

            <a href="#legajo">
                Legajo sugerido
            </a>

            <a href="#consulta">
                Consultar empleado
            </a>

            <a href="#editar">
                Editar empleado
            </a>

            <a href="#historial">
                Historial laboral
            </a>

            <a href="#estado">
                Activar / Inactivar
            </a>

            <a href="#antiguedad">
                Antigüedad efectiva
            </a>

            <a href="#liquidaciones">
                Relación con liquidaciones
            </a>

            <a href="#campos">
                Campos principales
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
                👥
            </div>

            <h2>
                Objetivo del módulo
            </h2>

        </div>

        <p>
            El módulo <strong>Gestión de Empleados</strong>
            permite administrar la información personal,
            administrativa y laboral del personal municipal.
        </p>

        <p>
            Estos datos son utilizados posteriormente
            por Conceptos por Empleado, Liquidaciones,
            Reportes y otras funciones de SIGENMUNI.
        </p>

        <div class="aviso aviso-info">

            <strong>Importante:</strong>
            la categoría, el escalafón, la situación,
            la institución y la Unidad de Organización
            forman parte de la información administrativa
            utilizada por otros módulos.

        </div>

        <div class="aviso aviso-alerta">

            El estado actual del empleado
            no reemplaza su historial laboral.
            SIGENMUNI conserva los períodos trabajados
            para poder determinar antigüedad,
            vigencias y participación en liquidaciones históricas.

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
                Registrar un nuevo empleado
            </h2>

        </div>

        <p>
            Para registrar un empleado nuevo,
            ingrese a Gestión de Empleados
            y seleccione <strong>Nuevo Empleado</strong>.
        </p>

        <div class="pasos">

            <div class="paso">

                <div class="numero">
                    1
                </div>

                <div>

                    <strong>
                        Revisar el legajo sugerido
                    </strong>

                    <p>
                        El sistema informa el último legajo utilizado
                        y propone el siguiente número disponible.
                        El valor sugerido puede revisarse
                        antes de guardar.
                    </p>

                </div>

            </div>


            <div class="paso">

                <div class="numero">
                    2
                </div>

                <div>

                    <strong>
                        Completar datos personales
                    </strong>

                    <p>
                        Ingrese apellido, nombre, DNI,
                        teléfono y correo electrónico.
                        El email es obligatorio
                        y debe tener un formato válido.
                    </p>

                </div>

            </div>


            <div class="paso">

                <div class="numero">
                    3
                </div>

                <div>

                    <strong>
                        Completar datos administrativos
                    </strong>

                    <p>
                        Seleccione institución,
                        Unidad de Organización,
                        situación, escalafón y categoría.
                    </p>

                </div>

            </div>


            <div class="paso">

                <div class="numero">
                    4
                </div>

                <div>

                    <strong>
                        Ingresar la Fecha de Alta
                    </strong>

                    <p>
                        La Fecha de Alta representa
                        el inicio del primer período laboral
                        del empleado y debe cargarse correctamente.
                    </p>

                </div>

            </div>


            <div class="paso">

                <div class="numero">
                    5
                </div>

                <div>

                    <strong>
                        Revisar datos únicos
                    </strong>

                    <p>
                        Confirme especialmente el legajo
                        y el DNI antes de guardar,
                        ya que no deben duplicarse.
                    </p>

                </div>

            </div>


            <div class="paso">

                <div class="numero">
                    6
                </div>

                <div>

                    <strong>
                        Guardar
                    </strong>

                    <p>
                        Revise los datos ingresados
                        y confirme el alta del empleado.
                    </p>

                </div>

            </div>

        </div>

        <div class="aviso aviso-ok">

            Al guardar correctamente un empleado,
            SIGENMUNI crea también
            su <strong>primer período laboral abierto</strong>
            a partir de la Fecha de Alta informada.

        </div>

    </section>


    <!-- =====================================================
         LEGAJO SUGERIDO
    ====================================================== -->

    <section
        class="seccion"
        id="legajo"
    >

        <div class="seccion-header">

            <div class="icono">
                🔢
            </div>

            <h2>
                Legajo sugerido
            </h2>

        </div>

        <p>
            Al crear un empleado,
            el sistema consulta el mayor número de legajo
            utilizado en la base, incluyendo empleados
            activos e inactivos.
        </p>

        <div class="tarjetas-grid">

            <div class="tarjeta">

                <strong>
                    Último legajo utilizado
                </strong>

                <p>
                    Muestra el mayor número de legajo
                    registrado actualmente.
                </p>

            </div>

            <div class="tarjeta">

                <strong>
                    Próximo sugerido
                </strong>

                <p>
                    Propone automáticamente el número siguiente
                    para facilitar el alta de personal.
                </p>

            </div>

        </div>

        <div class="aviso aviso-info">

            El legajo sugerido sirve como ayuda.
            Antes de guardar,
            confirme que el número corresponda
            a la numeración administrativa utilizada
            por la Municipalidad.

        </div>

    </section>


    <!-- =====================================================
         CONSULTAR
    ====================================================== -->

    <section
        class="seccion"
        id="consulta"
    >

        <div class="seccion-header">

            <div class="icono">
                🔎
            </div>

            <h2>
                Consultar un empleado
            </h2>

        </div>

        <p>
            Desde el listado principal
            puede localizar empleados mediante
            el buscador por <strong>apellido</strong>.
        </p>

        <ul>

            <li>
                Ingrese el apellido completo
                o una parte del mismo.
            </li>

            <li>
                Revise el resultado obtenido
                y confirme legajo, nombre y estado.
            </li>

            <li>
                Utilice el botón <strong>Ver</strong>
                para consultar la ficha del empleado
                sin modificar información.
            </li>

        </ul>

        <div class="aviso aviso-info">

            La opción Ver es la recomendada
            cuando solamente necesita consultar
            datos personales, administrativos
            o información histórica.

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
                Editar un empleado
            </h2>

        </div>

        <p>
            La opción Editar permite actualizar
            los datos personales y administrativos
            que continúan siendo modificables.
        </p>

        <div class="pasos">

            <div class="paso">

                <div class="numero">
                    1
                </div>

                <div>

                    <strong>
                        Buscar al empleado
                    </strong>

                    <p>
                        Localice el registro
                        desde el listado principal.
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
                        Abra el formulario
                        correspondiente al empleado.
                    </p>

                </div>

            </div>


            <div class="paso">

                <div class="numero">
                    3
                </div>

                <div>

                    <strong>
                        Modificar solo lo necesario
                    </strong>

                    <p>
                        Revise cuidadosamente los datos
                        antes de guardar.
                        El correo electrónico
                        no debe quedar vacío.
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
                        y revise el mensaje del sistema.
                    </p>

                </div>

            </div>

        </div>

        <div class="aviso aviso-importante">

            <strong>Fecha de Alta Inicial:</strong>
            después de registrar al empleado,
            la Fecha de Alta inicial queda protegida
            y se muestra como dato de solo lectura
            en la edición común.
            No debe modificarse para registrar
            una reactivación o reingreso.

        </div>

        <div class="aviso aviso-alerta">

            Antes de cambiar categoría,
            escalafón, situación
            o Unidad de Organización,
            confirme que el cambio corresponda realmente
            al empleado porque puede afectar
            consultas y cálculos posteriores.

        </div>

    </section>


    <!-- =====================================================
         HISTORIAL LABORAL
    ====================================================== -->

    <section
        class="seccion"
        id="historial"
    >

        <div class="seccion-header">

            <div class="icono">
                🗂️
            </div>

            <h2>
                Historial laboral
            </h2>

        </div>

        <p>
            SIGENMUNI conserva los períodos laborales
            del empleado para representar correctamente
            altas, interrupciones y reingresos.
        </p>

        <div class="tabla-contenedor">

            <table>

                <thead>

                    <tr>
                        <th>Dato</th>
                        <th>Significado</th>
                    </tr>

                </thead>

                <tbody>

                    <tr>
                        <td>Fecha Desde</td>
                        <td>
                            Primer día trabajado
                            dentro de ese período laboral.
                        </td>
                    </tr>

                    <tr>
                        <td>Fecha Hasta</td>
                        <td>
                            Último día trabajado.
                            Cuando está vacía,
                            el período continúa abierto.
                        </td>
                    </tr>

                    <tr>
                        <td>Motivo de inicio</td>
                        <td>
                            Identifica el origen del período,
                            por ejemplo alta inicial o reactivación.
                        </td>
                    </tr>

                    <tr>
                        <td>Motivo de fin</td>
                        <td>
                            Permite documentar el cierre
                            del período cuando corresponda.
                        </td>
                    </tr>

                </tbody>

            </table>

        </div>

        <div class="aviso aviso-info">

            Las fechas del período laboral son inclusivas.
            Por ejemplo, si Fecha Hasta es 10/09/2026,
            ese día todavía pertenece al período trabajado.

        </div>

        <div class="aviso aviso-ok">

            Un empleado puede tener varios períodos laborales
            a lo largo del tiempo.
            Los períodos anteriores se conservan
            y no se reemplazan cuando existe un reingreso.

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
                Activar o inactivar un empleado
            </h2>

        </div>

        <p>
            El estado actual indica si el empleado
            se encuentra disponible
            para las operaciones que requieren
            personal actualmente activo.
        </p>

        <h3>
            Inactivar
        </h3>

        <p>
            Cuando el empleado deja de prestar servicios,
            seleccione <strong>Inactivar</strong>
            e informe la fecha efectiva correspondiente.
        </p>

        <ul>

            <li>
                Se cierra el período laboral abierto
                utilizando esa fecha como último día trabajado.
            </li>

            <li>
                El empleado queda con estado
                <span class="estado estado-inactivo">INACTIVO</span>.
            </li>

            <li>
                Los Conceptos por Empleado ya iniciados
                que excedan esa fecha pueden cerrarse
                en la misma fecha.
            </li>

            <li>
                Las asignaciones futuras
                que todavía no comenzaron
                quedan inactivas.
            </li>

        </ul>

        <h3>
            Reactivar
        </h3>

        <p>
            Cuando un empleado vuelve a prestar servicios,
            utilice la opción de activación
            e indique la nueva fecha correspondiente.
        </p>

        <ul>

            <li>
                Se crea un <strong>nuevo período laboral abierto</strong>.
            </li>

            <li>
                El período anterior permanece cerrado
                y se conserva como historial.
            </li>

            <li>
                Los Conceptos por Empleado anteriores
                no se reabren automáticamente.
            </li>

        </ul>

        <div class="aviso aviso-importante">

            Inactivar o reactivar
            no significa borrar o reescribir
            la historia laboral.
            Cada período debe conservarse
            para mantener trazabilidad.

        </div>

    </section>


    <!-- =====================================================
         ANTIGÜEDAD EFECTIVA
    ====================================================== -->

    <section
        class="seccion"
        id="antiguedad"
    >

        <div class="seccion-header">

            <div class="icono">
                ⏳
            </div>

            <h2>
                Antigüedad efectiva
            </h2>

        </div>

        <p>
            La antigüedad no se obtiene únicamente
            contando desde la Fecha de Alta inicial.
            SIGENMUNI suma el tiempo efectivamente trabajado
            en los distintos períodos laborales
            y excluye las interrupciones.
        </p>

        <div class="tarjetas-grid">

            <div class="tarjeta">

                <strong>
                    Períodos trabajados
                </strong>

                <p>
                    Se acumula el tiempo correspondiente
                    a cada período laboral válido
                    hasta la fecha de referencia.
                </p>

            </div>

            <div class="tarjeta">

                <strong>
                    Períodos de inactividad
                </strong>

                <p>
                    El tiempo comprendido entre una baja
                    y un posterior reingreso
                    no suma antigüedad efectiva.
                </p>

            </div>

        </div>

        <div class="aviso aviso-info">

            En la liquidación,
            el concepto <strong>108 - Antigüedad</strong>
            utiliza los años completos
            de antigüedad efectiva acumulada.
            La regla vigente aplica
            <strong>2% por cada año completo</strong>
            sobre el Sueldo Básico.

        </div>

    </section>


    <!-- =====================================================
         RELACIÓN CON LIQUIDACIONES
    ====================================================== -->

    <section
        class="seccion"
        id="liquidaciones"
    >

        <div class="seccion-header">

            <div class="icono">
                💵
            </div>

            <h2>
                Relación con las liquidaciones
            </h2>

        </div>

        <p>
            Para determinar si un empleado
            puede intervenir en una liquidación,
            SIGENMUNI consulta su historial laboral.
        </p>

        <div class="tarjetas-grid">

            <div class="tarjeta">

                <strong>
                    Empleado actualmente activo
                </strong>

                <p>
                    Puede participar cuando su período laboral
                    coincide con el mes que se está liquidando.
                </p>

            </div>

            <div class="tarjeta">

                <strong>
                    Empleado actualmente inactivo
                </strong>

                <p>
                    También puede aparecer en una liquidación histórica
                    si tuvo un período laboral
                    que se superpone con ese mes.
                </p>

            </div>

        </div>

        <h3>
            Días habilitados
        </h3>

        <p>
            En las liquidaciones mensuales,
            el historial laboral determina
            la cantidad máxima de días
            que pueden liquidarse dentro del período.
        </p>

        <div class="aviso aviso-alerta">

            <strong>Importante:</strong>
            el hecho de que un empleado esté inactivo hoy
            no significa que deba desaparecer
            de una liquidación correspondiente
            a un período anterior en el que sí trabajó.

        </div>

        <div class="aviso aviso-info">

            SIGENMUNI utiliza un esquema mensual
            de hasta 30 días para la liquidación.
            Cuando el período laboral abarca solo parte del mes,
            los días habilitados se determinan
            según el tiempo efectivamente comprendido
            dentro de ese período.

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
                📋
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
                        <th>Observación</th>
                    </tr>

                </thead>

                <tbody>

                    <tr>
                        <td>Legajo</td>
                        <td>
                            Identificador administrativo del empleado.
                        </td>
                        <td>
                            Debe ser único.
                            El alta muestra un próximo legajo sugerido.
                        </td>
                    </tr>

                    <tr>
                        <td>Apellido y Nombre</td>
                        <td>
                            Identificación personal del empleado.
                        </td>
                        <td>
                            Revise la escritura antes de guardar.
                        </td>
                    </tr>

                    <tr>
                        <td>DNI</td>
                        <td>
                            Documento Nacional de Identidad.
                        </td>
                        <td>
                            No debe duplicarse entre empleados.
                        </td>
                    </tr>

                    <tr>
                        <td>Teléfono</td>
                        <td>
                            Número de contacto del empleado.
                        </td>
                        <td>
                            Manténgalo actualizado cuando corresponda.
                        </td>
                    </tr>

                    <tr>
                        <td>Email</td>
                        <td>
                            Correo electrónico del empleado.
                        </td>
                        <td>
                            Es obligatorio
                            y debe tener un formato válido.
                        </td>
                    </tr>

                    <tr>
                        <td>Fecha de Alta Inicial</td>
                        <td>
                            Inicio del primer período laboral.
                        </td>
                        <td>
                            Se carga en el alta.
                            Luego queda protegida
                            en la edición común.
                        </td>
                    </tr>

                    <tr>
                        <td>Institución</td>
                        <td>
                            Institución vinculada al empleado.
                        </td>
                        <td>
                            Seleccione la que corresponda.
                        </td>
                    </tr>

                    <tr>
                        <td>Unidad de Organización</td>
                        <td>
                            Área organizativa a la que pertenece.
                        </td>
                        <td>
                            Debe reflejar la ubicación administrativa vigente.
                        </td>
                    </tr>

                    <tr>
                        <td>Situación</td>
                        <td>
                            Situación administrativa o laboral.
                        </td>
                        <td>
                            Debe coincidir con la situación vigente.
                        </td>
                    </tr>

                    <tr>
                        <td>Escalafón</td>
                        <td>
                            Escalafón asignado al empleado.
                        </td>
                        <td>
                            Forma parte de su información administrativa.
                        </td>
                    </tr>

                    <tr>
                        <td>Categoría</td>
                        <td>
                            Categoría salarial del empleado.
                        </td>
                        <td>
                            Se utiliza para obtener
                            los Valores del Concepto
                            configurados por categoría.
                        </td>
                    </tr>

                    <tr>
                        <td>Estado</td>
                        <td>
                            Indica si actualmente
                            está activo o inactivo.
                        </td>
                        <td>
                            El historial laboral
                            conserva los períodos anteriores.
                        </td>
                    </tr>

                </tbody>

            </table>

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
                Verifique legajo y DNI
                antes de guardar un alta.
            </li>

            <li>
                Utilice el legajo sugerido
                como ayuda,
                pero confirme la numeración administrativa.
            </li>

            <li>
                Cargue correctamente
                la Fecha de Alta Inicial,
                porque crea el primer período laboral.
            </li>

            <li>
                No modifique la Fecha de Alta Inicial
                para registrar un reingreso.
                Utilice Activar para crear un nuevo período.
            </li>

            <li>
                Mantenga actualizada
                la categoría salarial del empleado.
            </li>

            <li>
                Al cambiar una categoría,
                verifique que existan
                los Valores del Concepto necesarios
                para la fecha que se liquidará.
            </li>

            <li>
                Utilice Ver antes de Editar
                cuando solamente necesite consultar.
            </li>

            <li>
                Inactive empleados cuando corresponda
                en lugar de eliminar antecedentes.
            </li>

            <li>
                Al reactivar un empleado,
                revise también sus Conceptos por Empleado.
            </li>

            <li>
                Antes de liquidar un período histórico,
                revise el Historial Laboral
                y los días habilitados.
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
                No encuentro un empleado en el listado
            </summary>

            <p>
                Revise el apellido ingresado
                en el buscador y confirme
                si el empleado se encuentra
                en el estado esperado.
            </p>

        </details>


        <details>

            <summary>
                El sistema informa que el DNI ya existe
            </summary>

            <p>
                Verifique que no exista previamente
                otro empleado registrado
                con el mismo DNI.
            </p>

        </details>


        <details>

            <summary>
                El sistema informa que el legajo ya existe
            </summary>

            <p>
                Confirme el último legajo utilizado
                y revise que el número ingresado
                no pertenezca a otro empleado,
                incluso si actualmente está inactivo.
            </p>

        </details>


        <details>

            <summary>
                El correo electrónico no es aceptado
            </summary>

            <p>
                El email es obligatorio.
                Compruebe que no esté vacío
                y que tenga un formato válido.
            </p>

        </details>


        <details>

            <summary>
                ¿Por qué no puedo modificar la Fecha de Alta Inicial?
            </summary>

            <p>
                Porque esa fecha identifica
                el comienzo del primer período laboral.
                Después del alta se protege
                para conservar la coherencia histórica.
                Los reingresos se registran mediante Activar,
                no modificando la fecha original.
            </p>

        </details>


        <details>

            <summary>
                ¿Inactivar elimina al empleado?
            </summary>

            <p>
                No.
                La inactivación cierra
                el período laboral abierto
                y conserva el registro
                y todos sus antecedentes.
            </p>

        </details>


        <details>

            <summary>
                Reactivé al empleado y sus conceptos anteriores no volvieron
            </summary>

            <p>
                Es el comportamiento esperado.
                La reactivación crea un nuevo período laboral
                y no reabre automáticamente
                los Conceptos por Empleado
                pertenecientes al período anterior.
                Si vuelven a corresponder,
                deben crearse nuevas asignaciones.
            </p>

        </details>


        <details>

            <summary>
                ¿Por qué un empleado inactivo aparece en una liquidación anterior?
            </summary>

            <p>
                Porque la participación se determina
                mediante el Historial Laboral.
                Si el empleado trabajó durante el período
                que se está liquidando,
                puede corresponder incluirlo
                aunque actualmente esté inactivo.
            </p>

        </details>


        <details>

            <summary>
                ¿Cómo se calcula la antigüedad después de un reingreso?
            </summary>

            <p>
                SIGENMUNI suma el tiempo efectivamente trabajado
                en los distintos períodos laborales
                y excluye el tiempo de inactividad.
                Se consideran años completos acumulados
                hasta la fecha de referencia.
            </p>

        </details>


        <details>

            <summary>
                Cambié la categoría del empleado. ¿Qué debo revisar?
            </summary>

            <p>
                Confirme que la nueva categoría sea correcta
                y que tenga configurados
                los Valores del Concepto vigentes
                necesarios para los conceptos salariales utilizados.
            </p>

        </details>


        <details>

            <summary>
                El empleado tiene menos días habilitados en una liquidación
            </summary>

            <p>
                Revise el Historial Laboral.
                Los días máximos habilitados
                se determinan según la parte del período
                en la que el empleado tuvo relación laboral.
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

        Guía de Gestión de Empleados

    </div>


</div>

</body>

</html>
