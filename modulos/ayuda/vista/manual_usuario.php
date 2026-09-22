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

<title>Manual de Usuario - SIGENMUNI</title>

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
    max-width:1200px;
    margin:28px auto 50px;
}


/* =========================================================
   CABECERA / PORTADA
========================================================= */

.portada{
    background:linear-gradient(135deg,#0f766e,#14b8a6);
    color:white;
    border-radius:22px;
    padding:34px;
    box-shadow:0 12px 28px rgba(15,118,110,.18);
    margin-bottom:22px;
}

.portada-top{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    gap:22px;
    flex-wrap:wrap;
}

.portada h1{
    margin:0 0 8px;
    font-size:36px;
}

.portada h2{
    margin:0 0 10px;
    font-size:21px;
    font-weight:normal;
    opacity:.97;
}

.portada p{
    margin:5px 0;
    line-height:1.55;
}

.logo-manual{
    width:90px;
    height:90px;
    border-radius:24px;
    background:rgba(255,255,255,.18);
    border:1px solid rgba(255,255,255,.30);
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:42px;
    font-weight:bold;
    flex:0 0 90px;
}

.acciones-superiores{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
    margin-top:20px;
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

.btn-imprimir{
    background:#f59e0b;
    color:#111827;
}

.btn-volver{
    background:#1f2937;
    color:white;
}


/* =========================================================
   PANEL
========================================================= */

.panel,
.seccion{
    background:white;
    border:1px solid #e5e7eb;
    border-radius:18px;
    padding:22px;
    box-shadow:0 8px 20px rgba(0,0,0,.06);
    margin-bottom:22px;
}

.panel h2,
.seccion h2{
    margin:0 0 8px;
    color:#0f766e;
}

.panel h2{
    font-size:23px;
}

.seccion h2{
    font-size:22px;
}

.seccion h3{
    margin:21px 0 8px;
    color:#334155;
    font-size:18px;
}

.seccion h4{
    margin:17px 0 7px;
    color:#475569;
    font-size:15px;
}

.seccion p,
.panel p{
    margin:7px 0;
    color:#475569;
    line-height:1.65;
}

.seccion ul,
.seccion ol{
    margin:10px 0 0 23px;
    padding:0;
}

.seccion li{
    margin-bottom:8px;
    color:#475569;
    line-height:1.55;
}


/* =========================================================
   ÍNDICE
========================================================= */

.indice{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:10px;
    margin-top:16px;
}

.indice a{
    min-height:52px;
    display:flex;
    align-items:center;
    padding:11px 13px;
    border-radius:11px;
    background:#f0fdfa;
    border:1px solid #99f6e4;
    color:#115e59;
    text-decoration:none;
    font-size:13px;
    font-weight:bold;
    line-height:1.35;
}

.indice a:hover{
    background:#ccfbf1;
}


/* =========================================================
   ENCABEZADO DE SECCIÓN
========================================================= */

.seccion-header{
    display:flex;
    gap:13px;
    align-items:center;
    margin-bottom:15px;
}

.icono{
    width:50px;
    height:50px;
    flex:0 0 50px;
    display:flex;
    align-items:center;
    justify-content:center;
    border-radius:14px;
    background:#f0fdfa;
    border:1px solid #99f6e4;
    font-size:25px;
}


/* =========================================================
   PASOS
========================================================= */

.pasos{
    display:grid;
    gap:11px;
    margin-top:14px;
}

.paso{
    display:grid;
    grid-template-columns:42px 1fr;
    gap:12px;
    align-items:start;
    padding:14px;
    background:#f8fafc;
    border:1px solid #e2e8f0;
    border-radius:13px;
}

.numero{
    width:36px;
    height:36px;
    display:flex;
    align-items:center;
    justify-content:center;
    border-radius:50%;
    background:#0f766e;
    color:white;
    font-weight:bold;
}

.paso strong{
    display:block;
    margin-bottom:4px;
}

.paso p{
    margin:0;
}


/* =========================================================
   AVISOS
========================================================= */

.aviso{
    margin-top:15px;
    padding:14px 16px;
    border-radius:12px;
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
    font-size:13px;
    text-align:left;
    vertical-align:top;
}

th{
    background:#0f766e;
    color:white;
}

td{
    color:#475569;
}

.codigo{
    font-family:Consolas,Monaco,monospace;
    color:#0f766e;
    font-weight:bold;
}


/* =========================================================
   TARJETAS
========================================================= */

.grid{
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:14px;
    margin-top:14px;
}

.tarjeta{
    background:#f8fafc;
    border:1px solid #e2e8f0;
    border-radius:14px;
    padding:16px;
}

.tarjeta h3{
    margin:0 0 8px;
    font-size:17px;
}

.tarjeta p{
    margin:0;
}


/* =========================================================
   FAQ
========================================================= */

details{
    background:white;
    border:1px solid #e2e8f0;
    border-radius:12px;
    padding:0 15px;
    margin-bottom:10px;
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
   CONTACTO
========================================================= */

.contacto-grid{
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:12px;
    margin-top:14px;
}

.contacto{
    padding:14px;
    border:1px solid #e2e8f0;
    border-radius:12px;
    background:#f8fafc;
}

.contacto strong{
    display:block;
    margin-bottom:5px;
    color:#64748b;
    font-size:12px;
    text-transform:uppercase;
}

.contacto span{
    font-weight:bold;
    overflow-wrap:anywhere;
}


/* =========================================================
   PIE
========================================================= */

.pie{
    text-align:center;
    color:#64748b;
    font-size:13px;
    padding:12px 0;
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media(max-width:900px){

    .indice{
        grid-template-columns:repeat(2,1fr);
    }

    .grid{
        grid-template-columns:1fr;
    }
}

@media(max-width:700px){

    .contenedor{
        width:97%;
        margin:12px auto 30px;
    }

    .portada{
        padding:20px 16px;
        border-radius:16px;
    }

    .portada-top{
        flex-direction:column-reverse;
        align-items:stretch;
    }

    .logo-manual{
        margin:0 auto;
    }

    .portada h1,
    .portada h2,
    .portada p{
        text-align:center;
    }

    .portada h1{
        font-size:27px;
    }

    .acciones-superiores{
        flex-direction:column;
    }

    .acciones-superiores .btn{
        width:100%;
    }

    .panel,
    .seccion{
        padding:17px;
        border-radius:15px;
    }

    .indice{
        grid-template-columns:1fr;
    }

    .seccion-header{
        align-items:flex-start;
    }

    .paso{
        grid-template-columns:36px 1fr;
        padding:12px;
    }

    .contacto-grid{
        grid-template-columns:1fr;
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
    .indice{
        display:none;
    }

    .portada,
    .panel,
    .seccion{
        box-shadow:none;
    }

    .portada{
        min-height:245mm;
        display:flex;
        flex-direction:column;
        justify-content:center;
        background:white;
        color:#111827;
        border:1px solid #d1d5db;
        page-break-after:always;
    }

    .portada h1{
        color:#0f766e;
    }

    .logo-manual{
        background:#f0fdfa;
        color:#0f766e;
        border:1px solid #99f6e4;
    }

    .panel{
        page-break-after:always;
    }

    .seccion{
        break-before:page;
    }

    .seccion,
    .paso,
    .tarjeta,
    details{
        break-inside:avoid;
    }

    table{
        min-width:0;
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
    'Centro de Ayuda - Manual de Usuario';

require __DIR__
    . '/../../../componentes/header_sigenmuni.php';

?>


<div class="contenedor">


    <!-- =====================================================
         PORTADA
    ====================================================== -->

    <section class="portada">

        <div class="portada-top">

            <div>

                <h1>
                    Manual de Usuario
                </h1>

                <h2>
                    <?php
                    echo htmlspecialchars(
                        $sistema['nombre']
                        ?? 'SIGENMUNI',
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>
                </h2>

                <p>
                    <?php
                    echo htmlspecialchars(
                        $sistema['descripcion']
                        ?? 'Sistema de Gestión Municipal y Liquidación de Haberes',
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>
                </p>

                <p>
                    <strong>
                        <?php
                        echo htmlspecialchars(
                            $sistema['municipio']
                            ?? 'Municipalidad de Fortín Lugones',
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>
                    </strong>
                </p>

                <p>
                    Versión:
                    <strong>
                        <?php
                        echo htmlspecialchars(
                            $sistema['version']
                            ?? '1.0',
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>
                    </strong>
                </p>

            </div>

            <div class="logo-manual">
                S
            </div>

        </div>

        <div class="acciones-superiores">

            <button
                type="button"
                class="btn btn-imprimir"
                onclick="window.print()"
            >
                Imprimir / Guardar PDF
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

    </section>


    <!-- =====================================================
         ÍNDICE GENERAL
    ====================================================== -->

    <section class="panel">

        <h2>
            Índice General
        </h2>

        <p>
            Este manual reúne las principales funciones
            y conceptos necesarios para utilizar SIGENMUNI.
        </p>

        <div class="indice">

            <a href="#introduccion">
                1. Introducción
            </a>

            <a href="#acceso">
                2. Acceso al sistema
            </a>

            <a href="#empleados">
                3. Gestión de Empleados
            </a>

            <a href="#categorias">
                4. Gestión de Categorías
            </a>

            <a href="#conceptos">
                5. Gestión de Conceptos
            </a>

            <a href="#conceptos-empleado">
                6. Conceptos por Empleado
            </a>

            <a href="#liquidaciones">
                7. Gestión de Liquidaciones
            </a>

            <a href="#reportes">
                8. Consultas y Reportes
            </a>

            <a href="#auditoria">
                9. Auditoría
            </a>

            <a href="#usuarios">
                10. Usuarios, Roles y Permisos
            </a>

            <a href="#glosario">
                11. Glosario
            </a>

            <a href="#faq">
                12. Preguntas Frecuentes
            </a>

            <a href="#contacto">
                13. Contacto y Soporte
            </a>

        </div>

    </section>


    <!-- =====================================================
         1. INTRODUCCIÓN
    ====================================================== -->

    <section
        class="seccion"
        id="introduccion"
    >

        <div class="seccion-header">

            <div class="icono">
                ℹ️
            </div>

            <h2>
                1. Introducción
            </h2>

        </div>

        <p>
            SIGENMUNI es un sistema de gestión municipal
            orientado a la administración de personal,
            conceptos salariales,
            liquidaciones de haberes,
            consultas,
            reportes,
            usuarios,
            roles y auditoría.
        </p>

        <p>
            El sistema organiza sus funciones por módulos
            y utiliza un esquema de permisos por rol
            para controlar el acceso a cada sector.
        </p>

        <div class="aviso aviso-info">

            Cada usuario debe utilizar solamente
            los módulos habilitados para su rol.
            El hecho de conocer la dirección de un archivo
            no reemplaza la validación de permisos.

        </div>

    </section>


    <!-- =====================================================
         2. ACCESO
    ====================================================== -->

    <section
        class="seccion"
        id="acceso"
    >

        <div class="seccion-header">

            <div class="icono">
                🔑
            </div>

            <h2>
                2. Acceso al sistema
            </h2>

        </div>

        <p>
            Para utilizar SIGENMUNI,
            el usuario debe iniciar sesión
            con una cuenta activa
            y tener asignado un rol activo.
        </p>

        <div class="pasos">

            <div class="paso">
                <div class="numero">1</div>
                <div>
                    <strong>Ingresar usuario</strong>
                    <p>
                        Escriba el nombre de usuario asignado.
                    </p>
                </div>
            </div>

            <div class="paso">
                <div class="numero">2</div>
                <div>
                    <strong>Ingresar contraseña</strong>
                    <p>
                        Utilice la contraseña correspondiente a la cuenta.
                    </p>
                </div>
            </div>

            <div class="paso">
                <div class="numero">3</div>
                <div>
                    <strong>Validación de acceso</strong>
                    <p>
                        SIGENMUNI verifica que el usuario esté activo,
                        que su rol esté activo
                        y que tenga autorización para ingresar.
                    </p>
                </div>
            </div>

            <div class="paso">
                <div class="numero">4</div>
                <div>
                    <strong>Primer ingreso</strong>
                    <p>
                        Si la cuenta está marcada para primer ingreso,
                        deberá completar el flujo previsto
                        antes de continuar con el uso normal del sistema.
                    </p>
                </div>
            </div>

            <div class="paso">
                <div class="numero">5</div>
                <div>
                    <strong>Menú Principal</strong>
                    <p>
                        Una vez validado el acceso,
                        se muestran los módulos habilitados
                        según el rol y sus permisos.
                    </p>
                </div>
            </div>

        </div>

        <h3>
            Política de contraseña
        </h3>

        <ul>
            <li>Mínimo 8 caracteres.</li>
            <li>Al menos una letra mayúscula.</li>
            <li>Al menos un carácter especial.</li>
        </ul>

        <div class="aviso aviso-info">

            En roles comunes,
            el permiso de acceso se valida
            también al ingresar directamente
            por la URL del archivo protegido.
            Ocultar una opción del menú
            no reemplaza el control de permisos.

        </div>

        <div class="aviso aviso-alerta">

            Si el usuario está activo
            pero el rol asignado está inactivo,
            el acceso al sistema debe permanecer bloqueado.

        </div>

        <div class="aviso aviso-importante">

            Nunca comparta contraseñas,
            códigos de recuperación
            ni credenciales de acceso.

        </div>

    </section>


    <!-- =====================================================
         3. EMPLEADOS
    ====================================================== -->

    <section
        class="seccion"
        id="empleados"
    >

        <div class="seccion-header">

            <div class="icono">
                👥
            </div>

            <h2>
                3. Gestión de Empleados
            </h2>

        </div>

        <p>
            Permite registrar, consultar, editar
            y administrar la situación actual
            e histórica del personal municipal.
        </p>

        <h3>
            Funciones principales
        </h3>

        <ul>
            <li>Alta de empleados.</li>
            <li>Consulta de ficha individual.</li>
            <li>Edición de datos personales y administrativos.</li>
            <li>Asignación de categoría, escalafón y Unidad de Organización.</li>
            <li>Activación e inactivación con historial laboral.</li>
            <li>Consulta de períodos laborales anteriores.</li>
        </ul>

        <h3>
            Alta y legajo sugerido
        </h3>

        <p>
            Al crear un empleado,
            SIGENMUNI informa el último legajo utilizado
            y propone el siguiente como referencia.
            El número debe verificarse antes de guardar.
        </p>

        <p>
            La <strong>Fecha de Alta Inicial</strong>
            crea el primer período laboral del empleado.
            Después del alta,
            esta fecha queda protegida
            y no debe utilizarse para registrar reingresos.
        </p>

        <h3>
            Historial laboral
        </h3>

        <p>
            Un empleado puede tener varios períodos laborales.
            Cada período registra una Fecha Desde
            y, cuando corresponde,
            una Fecha Hasta que representa
            el último día trabajado.
        </p>

        <div class="grid">

            <div class="tarjeta">
                <h3>Inactivar</h3>
                <p>
                    Cierra el período laboral abierto
                    en la fecha efectiva informada
                    y conserva el antecedente histórico.
                </p>
            </div>

            <div class="tarjeta">
                <h3>Reactivar</h3>
                <p>
                    Crea un nuevo período laboral.
                    No reabre el período anterior
                    ni reactiva automáticamente
                    conceptos particulares anteriores.
                </p>
            </div>

        </div>

        <h3>
            Antigüedad efectiva
        </h3>

        <p>
            La antigüedad se calcula
            acumulando el tiempo efectivamente trabajado
            en los distintos períodos laborales.
            Los períodos de inactividad
            no suman antigüedad.
        </p>

        <p>
            En la liquidación,
            el concepto <strong>108 - Antigüedad</strong>
            utiliza años completos acumulados
            y aplica la regla vigente
            de <strong>2% por año sobre Sueldo Básico</strong>.
        </p>

        <h3>
            Relación con liquidaciones
        </h3>

        <p>
            La inclusión de un empleado en una liquidación
            se determina mediante su historial laboral.
            Un empleado actualmente inactivo
            puede aparecer correctamente en una liquidación histórica
            si trabajó durante ese período.
        </p>

        <div class="tabla-contenedor">

            <table>

                <thead>
                    <tr>
                        <th>Dato</th>
                        <th>Uso</th>
                    </tr>
                </thead>

                <tbody>

                    <tr>
                        <td>Legajo</td>
                        <td>
                            Identificador administrativo único.
                        </td>
                    </tr>

                    <tr>
                        <td>DNI</td>
                        <td>
                            Documento identificatorio único del empleado.
                        </td>
                    </tr>

                    <tr>
                        <td>Email</td>
                        <td>
                            Correo electrónico obligatorio
                            con formato válido.
                        </td>
                    </tr>

                    <tr>
                        <td>Fecha de Alta Inicial</td>
                        <td>
                            Inicio del primer período laboral.
                            Luego queda protegida en la edición común.
                        </td>
                    </tr>

                    <tr>
                        <td>Categoría</td>
                        <td>
                            Clasificación salarial utilizada
                            para obtener valores por categoría.
                        </td>
                    </tr>

                    <tr>
                        <td>Historial laboral</td>
                        <td>
                            Determina períodos efectivamente trabajados,
                            antigüedad y días habilitados
                            en liquidaciones.
                        </td>
                    </tr>

                    <tr>
                        <td>Estado</td>
                        <td>
                            Indica si actualmente
                            está activo o inactivo.
                        </td>
                    </tr>

                </tbody>

            </table>

        </div>

        <div class="aviso aviso-alerta">

            Inactivar un empleado
            no significa eliminarlo.
            El registro y sus períodos laborales
            deben conservarse
            para mantener la información histórica.

        </div>

    </section>


    <!-- =====================================================
         4. CATEGORÍAS
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
                4. Gestión de Categorías
            </h2>

        </div>

        <p>
            Permite administrar
            las categorías salariales
            utilizadas por los empleados.
        </p>

        <h3>
            Funciones principales
        </h3>

        <ul>
            <li>Crear categorías.</li>
            <li>Editar código y nombre.</li>
            <li>Activar o inactivar categorías.</li>
            <li>Consultar categorías existentes.</li>
        </ul>

        <div class="aviso aviso-importante">

            Los importes salariales principales
            no son datos propios de la categoría.
            Se administran desde
            <strong>Gestión de Conceptos → Valores del Concepto</strong>.

        </div>

        <h3>
            Conceptos principales por categoría
        </h3>

        <ul>
            <li><strong>101 - Sueldo Básico.</strong></li>
            <li><strong>102 - Dedicación Funcional.</strong></li>
            <li><strong>104 - Suplemento Especial.</strong></li>
        </ul>

        <p>
            Cada valor puede tener
            Fecha Desde, Fecha Hasta y estado.
            Por lo tanto,
            un valor existente no necesariamente
            está vigente para la fecha que se desea liquidar.
        </p>

        <div class="aviso aviso-info">

            <strong>Regla especial del concepto 102:</strong>
            si no existe un valor vigente
            de Dedicación Funcional
            y existe un Sueldo Básico válido mayor que cero,
            la liquidación puede utilizar
            el Sueldo Básico como valor de respaldo.
            Aun así, se recomienda configurar 102
            de manera explícita.

        </div>

        <h3>
            Configuración en reportes
        </h3>

        <ul>
            <li>
                <strong>Completa:</strong>
                posee los valores principales vigentes.
            </li>
            <li>
                <strong>Incompleta:</strong>
                falta uno o más valores vigentes.
            </li>
            <li>
                <strong>Sin valor vigente:</strong>
                no existe un valor activo y válido
                para ese concepto y fecha.
            </li>
        </ul>

        <div class="aviso aviso-alerta">

            <strong>Sin valor vigente</strong>
            y <strong>$ 0,00</strong>
            no significan lo mismo.
            Un importe cero puede ser un valor válido;
            “Sin valor vigente” indica falta de configuración
            para la fecha consultada.

        </div>

    </section>


    <!-- =====================================================
         5. CONCEPTOS
    ====================================================== -->

    <section
        class="seccion"
        id="conceptos"
    >

        <div class="seccion-header">

            <div class="icono">
                💰
            </div>

            <h2>
                5. Gestión de Conceptos
            </h2>

        </div>

        <p>
            Los conceptos representan
            haberes, descuentos,
            asignaciones y aportes
            utilizados por la liquidación.
        </p>

        <h3>
            Rangos de códigos
        </h3>

        <div class="tabla-contenedor">

            <table>

                <thead>
                    <tr>
                        <th>Rango</th>
                        <th>Uso</th>
                    </tr>
                </thead>

                <tbody>

                    <tr>
                        <td class="codigo">101–199</td>
                        <td>
                            Haberes y conceptos salariales.
                        </td>
                    </tr>

                    <tr>
                        <td class="codigo">201–299</td>
                        <td>
                            Asignaciones familiares.
                        </td>
                    </tr>

                    <tr>
                        <td class="codigo">301–399</td>
                        <td>
                            Descuentos.
                        </td>
                    </tr>

                    <tr>
                        <td class="codigo">401–499</td>
                        <td>
                            Aportes patronales.
                        </td>
                    </tr>

                </tbody>

            </table>

        </div>

        <div class="aviso aviso-alerta">

            Antes de crear un concepto nuevo,
            consulte la Guía de Gestión de Conceptos
            para verificar qué códigos están utilizados
            y cuáles permanecen disponibles.
            No reutilice un código histórico
            aunque el concepto esté inactivo.

        </div>

        <h3>
            Tipo de Concepto
        </h3>

        <div class="tabla-contenedor">

            <table>

                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th>Uso</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td>REMUNERATIVO</td>
                        <td>Haberes que integran el total remunerativo.</td>
                    </tr>
                    <tr>
                        <td>NO REMUNERATIVO</td>
                        <td>Haberes que no integran el total remunerativo.</td>
                    </tr>
                    <tr>
                        <td>ASIGNACIÓN FAMILIAR</td>
                        <td>Asignaciones familiares.</td>
                    </tr>
                    <tr>
                        <td>DESCUENTO</td>
                        <td>Importes que reducen el neto.</td>
                    </tr>
                    <tr>
                        <td>APORTE PATRONAL</td>
                        <td>Aportes a cargo del empleador.</td>
                    </tr>
                </tbody>

            </table>

        </div>

        <h3>
            Forma de Cálculo
        </h3>

        <ul>
            <li>FIJO / Manual según configuración.</li>
            <li>PORCENTAJE.</li>
            <li>VALOR POR CATEGORÍA.</li>
            <li>MANUAL.</li>
            <li>FÓRMULA / Automático.</li>
        </ul>

        <div class="aviso aviso-info">

            En pantalla se utiliza
            <strong>VALOR POR CATEGORÍA</strong>.
            Internamente SIGENMUNI puede conservar
            el identificador técnico
            <span class="codigo">TABLA_CATEGORIA</span>
            sin que esto represente una tabla salarial antigua.

        </div>

        <h3>
            Valores del Concepto
        </h3>

        <p>
            Los valores se administran
            en la tabla de Valores del Concepto
            y pueden asociarse a categoría,
            monto, porcentaje
            y período de vigencia.
        </p>

        <p>
            Para conservar el historial,
            cuando un importe cambia
            normalmente conviene crear
            un nuevo valor con una nueva Fecha Desde
            en lugar de sobrescribir el valor anterior.
        </p>

    </section>


    <!-- =====================================================
         6. CONCEPTOS POR EMPLEADO
    ====================================================== -->

    <section
        class="seccion"
        id="conceptos-empleado"
    >

        <div class="seccion-header">

            <div class="icono">
                📋
            </div>

            <h2>
                6. Conceptos por Empleado
            </h2>

        </div>

        <p>
            Permite asociar conceptos particulares
            a un empleado determinado
            cuando la regla necesita
            un monto, porcentaje, cantidad
            o vigencia individual.
        </p>

        <h3>
            Datos disponibles
        </h3>

        <ul>
            <li>Empleado.</li>
            <li>Concepto.</li>
            <li>Monto manual.</li>
            <li>Porcentaje manual.</li>
            <li>Cantidad.</li>
            <li>Fecha Desde.</li>
            <li>Fecha Hasta.</li>
            <li>Observación.</li>
            <li>Estado.</li>
        </ul>

        <h3>
            Estados visuales
        </h3>

        <div class="grid">

            <div class="tarjeta">
                <h3>Vigente</h3>
                <p>
                    La asignación está activa
                    y dentro de su período de vigencia.
                </p>
            </div>

            <div class="tarjeta">
                <h3>Programado</h3>
                <p>
                    Está activo,
                    pero su Fecha Desde es futura.
                </p>
            </div>

            <div class="tarjeta">
                <h3>Finalizado por fecha</h3>
                <p>
                    Su Fecha Hasta ya terminó.
                    Se conserva como historial
                    y queda disponible para consulta.
                </p>
            </div>

            <div class="tarjeta">
                <h3>Inactivo</h3>
                <p>
                    La asignación fue deshabilitada
                    pero no se elimina.
                </p>
            </div>

        </div>

        <h3>
            Relación con el historial laboral
        </h3>

        <p>
            Una asignación debe quedar
            completamente dentro de un mismo período laboral continuo.
            No puede atravesar una baja
            y un posterior reingreso.
        </p>

        <p>
            Al inactivar a un empleado,
            las asignaciones ya iniciadas
            pueden cerrarse en la misma fecha,
            y las asignaciones futuras quedan inactivas.
            Al reactivar,
            los conceptos anteriores
            no se reabren automáticamente.
        </p>

        <div class="aviso aviso-importante">

            Las asignaciones
            <strong>Finalizadas por fecha</strong>
            forman parte del historial.
            En la operatoria normal
            quedan disponibles solamente para consulta.

        </div>

        <div class="aviso aviso-alerta">

            Los conceptos manuales
            no se prorratean automáticamente
            por los días liquidados del mes.
            Debe revisarse la regla funcional
            que corresponda al concepto.

        </div>

    </section>


    <!-- =====================================================
         7. LIQUIDACIONES
    ====================================================== -->

    <section
        class="seccion"
        id="liquidaciones"
    >

        <div class="seccion-header">

            <div class="icono">
                🧾
            </div>

            <h2>
                7. Gestión de Liquidaciones
            </h2>

        </div>

        <p>
            Permite crear, preparar, procesar
            y revisar liquidaciones de haberes
            según el tipo seleccionado.
        </p>

        <h3>
            Tipos de liquidación
        </h3>

        <div class="tabla-contenedor">

            <table>

                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th>Uso principal</th>
                    </tr>
                </thead>

                <tbody>

                    <tr>
                        <td>Mensual</td>
                        <td>
                            Liquidación habitual del mes.
                            Considera historial laboral,
                            días liquidados
                            y Presentismo.
                        </td>
                    </tr>

                    <tr>
                        <td>Complementaria de Haberes</td>
                        <td>
                            Liquidación adicional
                            para personal seleccionado,
                            con novedades propias.
                        </td>
                    </tr>

                    <tr>
                        <td>Aguinaldo</td>
                        <td>
                            Sueldo Anual Complementario
                            de junio o diciembre.
                            Utiliza días SAC
                            con un máximo de 180 por semestre.
                        </td>
                    </tr>

                    <tr>
                        <td>Complementaria de SAC</td>
                        <td>
                            Permite liquidar días SAC
                            pendientes o adicionales
                            del semestre,
                            controlando lo ya pagado.
                        </td>
                    </tr>

                    <tr>
                        <td>Gastos Protocolares</td>
                        <td>
                            Liquidación especial
                            con importe manual por empleado
                            y condición previsional.
                        </td>
                    </tr>

                </tbody>

            </table>

        </div>

        <h3>
            Reglas importantes
        </h3>

        <ul>
            <li>
                La liquidación mensual utiliza
                un convenio de hasta 30 días.
            </li>
            <li>
                El historial laboral determina
                los días máximos habilitados
                para cada empleado.
            </li>
            <li>
                Presentismo puede mantenerse
                aunque se liquiden menos de 30 días,
                salvo que la novedad indique lo contrario.
            </li>
            <li>
                Las asignaciones familiares
                y los conceptos manuales
                no se prorratean automáticamente por días.
            </li>
            <li>
                La Fecha de Liquidación
                debe ser coherente con el período
                según las validaciones del tipo seleccionado.
            </li>
            <li>
                En SAC,
                los días liquidados y ya pagados
                se controlan dentro del semestre.
            </li>
        </ul>

        <h3>
            Procesamiento seguro
        </h3>

        <p>
            Si SIGENMUNI detecta un dato obligatorio faltante
            o una condición inválida,
            el procesamiento se interrumpe
            y los cambios parciales del intento se revierten.
        </p>

        <h3>
            Neto del empleado
        </h3>

        <p>
            El neto surge de considerar
            remunerativos,
            descuentos,
            no remunerativos
            y asignaciones.
        </p>

        <div class="aviso aviso-info">

            Los aportes patronales
            no disminuyen el neto
            que percibe el empleado.

        </div>

        <div class="aviso aviso-alerta">

            Antes de procesar,
            revise las novedades específicas
            del tipo de liquidación:
            días, Presentismo,
            Personal y Días SAC
            o Gastos Protocolares,
            según corresponda.

        </div>

    </section>


    <!-- =====================================================
         8. REPORTES
    ====================================================== -->

    <section
        class="seccion"
        id="reportes"
    >

        <div class="seccion-header">

            <div class="icono">
                📊
            </div>

            <h2>
                8. Consultas y Reportes
            </h2>

        </div>

        <p>
            Centraliza la información
            de consulta, control y análisis
            generada por SIGENMUNI.
        </p>

        <div class="grid">

            <div class="tarjeta">
                <h3>Reporte de Empleados</h3>
                <p>
                    Consulta general del personal.
                </p>
            </div>

            <div class="tarjeta">
                <h3>Historial por Empleado</h3>
                <p>
                    Consulta antecedentes históricos
                    asociados a una persona.
                </p>
            </div>

            <div class="tarjeta">
                <h3>Reporte de Conceptos</h3>
                <p>
                    Permite filtrar por
                    Tipo de Concepto y Estado.
                </p>
            </div>

            <div class="tarjeta">
                <h3>Reporte de Categorías</h3>
                <p>
                    Consulta categorías
                    y valores vigentes de 101, 102 y 104.
                </p>
            </div>

            <div class="tarjeta">
                <h3>Reporte de Liquidaciones</h3>
                <p>
                    Permite filtrar por
                    Período, Tipo de Liquidación y Estado.
                </p>
            </div>

            <div class="tarjeta">
                <h3>Estadísticas</h3>
                <p>
                    Indicadores, tablas y gráficos
                    basados en liquidaciones cerradas
                    que cumplen los filtros seleccionados.
                </p>
            </div>

            <div class="tarjeta">
                <h3>Reporte de Auditoría</h3>
                <p>
                    Consulta trazabilidad
                    de acciones realizadas
                    por los usuarios.
                </p>
            </div>

        </div>

        <h3>
            Estadísticas
        </h3>

        <p>
            Los indicadores económicos
            pueden filtrarse por Desde, Hasta
            y Tipo de Liquidación.
            Entre los datos principales
            se incluyen Total Remunerativo,
            No Remunerativo,
            Asignaciones, Descuentos,
            Neto y Promedio Neto.
        </p>

        <div class="aviso aviso-info">

            El indicador de empleados activos actuales
            representa la situación presente del personal
            y no debe confundirse
            con la cantidad de empleados
            liquidados en un período histórico.

        </div>

        <h3>
            Exportaciones
        </h3>

        <p>
            Según el reporte,
            puede imprimirse o guardarse en PDF
            y también exportarse a Excel
            en formato <strong>.xlsx</strong>.
            La exportación debe conservar
            los mismos filtros utilizados en pantalla.
        </p>

        <div class="aviso aviso-alerta">

            En Estadísticas,
            el archivo Excel puede incluir
            hojas de datos y gráficos
            correspondientes al análisis generado.

        </div>

    </section>


    <!-- =====================================================
         9. AUDITORÍA
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
                9. Auditoría
            </h2>

        </div>

        <p>
            La auditoría registra
            acciones relevantes realizadas
            por los usuarios
            y permite mantener trazabilidad.
        </p>

        <h3>
            Datos que puede mostrar
        </h3>

        <ul>
            <li>Fecha y hora.</li>
            <li>Usuario.</li>
            <li>Rol.</li>
            <li>Módulo.</li>
            <li>Acción.</li>
            <li>Entidad o registro afectado.</li>
            <li>Descripción.</li>
            <li>Datos anteriores y nuevos, cuando corresponda.</li>
        </ul>

        <div class="aviso aviso-info">

            Para mantener una presentación más limpia,
            la dirección IP no se muestra
            en el listado general
            ni en el PDF general de Auditoría.
            Cuando está registrada
            y corresponde consultarla,
            puede permanecer disponible
            en el detalle de la operación.

        </div>

        <div class="aviso aviso-importante">

            La auditoría no debe almacenar
            contraseñas,
            tokens,
            códigos de recuperación
            ni otra información sensible
            de autenticación.

        </div>

    </section>


    <!-- =====================================================
         10. USUARIOS
    ====================================================== -->

    <section
        class="seccion"
        id="usuarios"
    >

        <div class="seccion-header">

            <div class="icono">
                🔐
            </div>

            <h2>
                10. Usuarios, Roles y Permisos
            </h2>

        </div>

        <p>
            Controla quién puede ingresar a SIGENMUNI
            y qué módulos o archivos
            puede utilizar cada cuenta.
        </p>

        <h3>
            Usuarios
        </h3>

        <ul>
            <li>Alta y edición de usuarios.</li>
            <li>DNI, nombre de usuario y email únicos.</li>
            <li>Asignación de un rol activo.</li>
            <li>Activación o inactivación de cuentas.</li>
            <li>Gestión del estado de primer ingreso.</li>
            <li>Resetear primer ingreso cuando corresponda.</li>
        </ul>

        <div class="aviso aviso-info">

            El usuario actualmente conectado
            se encuentra protegido
            para evitar que se inactive
            accidentalmente su propia cuenta.

        </div>

        <h3>
            Roles
        </h3>

        <ul>
            <li>Crear y editar roles.</li>
            <li>Activar o inactivar roles comunes.</li>
            <li>Definir si posee privilegio administrador.</li>
            <li>Configurar permisos por módulo o archivo.</li>
        </ul>

        <p>
            Si el rol asignado a un usuario
            está inactivo,
            el acceso debe permanecer bloqueado
            aunque la cuenta del usuario continúe activa.
        </p>

        <h3>
            ADMIN principal
        </h3>

        <p>
            El rol principal identificado como
            <strong>ADMIN</strong>
            y configurado con
            <strong>es_admin = 1</strong>
            se considera protegido.
        </p>

        <ul>
            <li>No debe inactivarse.</li>
            <li>No debe perder sus permisos.</li>
            <li>Conserva acceso total.</li>
            <li>No debe utilizarse como rol común de prueba.</li>
        </ul>

        <h3>
            Permisos
        </h3>

        <p>
            Los roles comunes
            pueden tener acceso permitido o bloqueado
            para los módulos y archivos configurados.
            El permiso se valida también
            al ingresar directamente por URL.
        </p>

        <div class="aviso aviso-alerta">

            Los roles administradores
            poseen acceso total
            según la lógica de seguridad
            y sus permisos se consideran protegidos.

        </div>

    </section>


    <!-- =====================================================
         11. GLOSARIO
    ====================================================== -->

    <section
        class="seccion"
        id="glosario"
    >

        <div class="seccion-header">

            <div class="icono">
                📖
            </div>

            <h2>
                11. Glosario
            </h2>

        </div>

        <div class="tabla-contenedor">

            <table>

                <thead>
                    <tr>
                        <th>Término</th>
                        <th>Definición</th>
                    </tr>
                </thead>

                <tbody>

                    <tr>
                        <td>Categoría</td>
                        <td>
                            Clasificación salarial del empleado.
                        </td>
                    </tr>

                    <tr>
                        <td>Tipo de Concepto</td>
                        <td>
                            Clasificación funcional del concepto
                            dentro de la liquidación.
                        </td>
                    </tr>

                    <tr>
                        <td>Valor por Categoría</td>
                        <td>
                            Forma de cálculo que obtiene
                            un importe según la categoría salarial.
                        </td>
                    </tr>

                    <tr>
                        <td>Valor vigente</td>
                        <td>
                            Valor activo cuya vigencia corresponde
                            a la fecha utilizada.
                        </td>
                    </tr>

                    <tr>
                        <td>Sin valor vigente</td>
                        <td>
                            No existe una configuración activa
                            y válida para la fecha consultada.
                        </td>
                    </tr>

                    <tr>
                        <td>Período laboral</td>
                        <td>
                            Intervalo de tiempo
                            durante el cual el empleado
                            mantiene relación laboral activa.
                        </td>
                    </tr>

                    <tr>
                        <td>Historial laboral</td>
                        <td>
                            Conjunto de períodos laborales
                            conservados para registrar
                            altas, interrupciones y reingresos.
                        </td>
                    </tr>

                    <tr>
                        <td>Antigüedad efectiva</td>
                        <td>
                            Tiempo efectivamente trabajado,
                            acumulado entre períodos laborales,
                            excluyendo las interrupciones.
                        </td>
                    </tr>

                    <tr>
                        <td>Liquidación Mensual</td>
                        <td>
                            Liquidación habitual
                            de haberes del mes.
                        </td>
                    </tr>

                    <tr>
                        <td>Complementaria de Haberes</td>
                        <td>
                            Liquidación adicional
                            para personal seleccionado
                            fuera de la liquidación mensual habitual.
                        </td>
                    </tr>

                    <tr>
                        <td>SAC / Aguinaldo</td>
                        <td>
                            Sueldo Anual Complementario
                            liquidado según base remunerativa
                            y días SAC devengados.
                        </td>
                    </tr>

                    <tr>
                        <td>Complementaria de SAC</td>
                        <td>
                            Liquidación destinada
                            a días SAC pendientes
                            o adicionales del semestre.
                        </td>
                    </tr>

                    <tr>
                        <td>Gastos Protocolares</td>
                        <td>
                            Liquidación especial
                            con importe manual
                            para los empleados seleccionados.
                        </td>
                    </tr>

                    <tr>
                        <td>Unidad de Organización</td>
                        <td>
                            Área organizativa asociada al empleado.
                        </td>
                    </tr>

                    <tr>
                        <td>Auditoría</td>
                        <td>
                            Registro de acciones relevantes
                            realizadas por usuarios.
                        </td>
                    </tr>

                </tbody>

            </table>

        </div>

    </section>


    <!-- =====================================================
         12. FAQ
    ====================================================== -->

    <section
        class="seccion"
        id="faq"
    >

        <div class="seccion-header">

            <div class="icono">
                ❓
            </div>

            <h2>
                12. Preguntas Frecuentes
            </h2>

        </div>


        <details>
            <summary>
                ¿Por qué no puedo ingresar a un módulo?
            </summary>
            <p>
                Revise que su usuario esté activo,
                que el rol asignado esté activo
                y que el rol común tenga
                el permiso correspondiente.
            </p>
        </details>


        <details>
            <summary>
                ¿Por qué una categoría muestra Sin valor vigente?
            </summary>
            <p>
                Porque no existe
                un valor activo y vigente
                para ese concepto,
                categoría y fecha.
            </p>
        </details>


        <details>
            <summary>
                ¿Qué significa Configuración incompleta?
            </summary>
            <p>
                Significa que falta
                uno o más valores principales.
                En el caso del concepto 102,
                la liquidación puede aplicar
                su regla de respaldo,
                aunque se recomienda configurar
                el valor explícitamente.
            </p>
        </details>


        <details>
            <summary>
                ¿Por qué un concepto por empleado aparece Finalizado por fecha?
            </summary>
            <p>
                Porque su Fecha Hasta ya terminó.
                El registro se conserva como historial
                y, en la operatoria normal,
                queda disponible solamente para consulta.
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
                las asignaciones del período anterior.
            </p>
        </details>


        <details>
            <summary>
                ¿Por qué un empleado inactivo aparece en una liquidación anterior?
            </summary>
            <p>
                Porque SIGENMUNI utiliza
                el Historial Laboral.
                Si trabajó durante ese período,
                puede corresponder incluirlo
                aunque actualmente esté inactivo.
            </p>
        </details>


        <details>
            <summary>
                ¿Cómo se calcula la antigüedad después de un reingreso?
            </summary>
            <p>
                Se suma el tiempo efectivamente trabajado
                entre los distintos períodos laborales
                y se excluyen las interrupciones.
            </p>
        </details>


        <details>
            <summary>
                ¿Por qué una liquidación no se procesa?
            </summary>
            <p>
                Revise las validaciones mostradas por el sistema,
                como período y fecha,
                selección de personal,
                días, Presentismo,
                días SAC o datos específicos
                del tipo de liquidación.
                Cuando una validación falla,
                los cambios parciales del intento se revierten.
            </p>
        </details>


        <details>
            <summary>
                ¿Por qué el Reporte de Liquidaciones no devuelve resultados?
            </summary>
            <p>
                Revise Período,
                Tipo de Liquidación
                y Estado.
                Un filtro activo puede excluir
                registros que sí existen.
            </p>
        </details>


        <details>
            <summary>
                ¿Inactivar elimina información?
            </summary>
            <p>
                No.
                La inactivación conserva
                la información histórica.
            </p>
        </details>


        <details>
            <summary>
                ¿Por qué no puedo inactivar mi propio usuario?
            </summary>
            <p>
                Es una protección del sistema
                para evitar que el usuario conectado
                deshabilite accidentalmente
                su propia cuenta.
            </p>
        </details>


        <details>
            <summary>
                ¿Por qué no puedo modificar o inactivar el ADMIN principal?
            </summary>
            <p>
                Porque el rol ADMIN principal
                con es_admin = 1
                se encuentra protegido
                y debe conservar acceso total.
            </p>
        </details>


        <details>
            <summary>
                ¿Puedo acceder escribiendo directamente la URL?
            </summary>
            <p>
                El sistema valida
                los permisos también
                en los archivos de entrada.
                Conocer la dirección
                no otorga autorización.
            </p>
        </details>


        <details>
            <summary>
                ¿Qué debo informar al solicitar soporte?
            </summary>
            <p>
                Usuario,
                módulo,
                operación realizada,
                mensaje de error
                y captura de pantalla,
                si corresponde.
                Nunca envíe su contraseña
                ni códigos de recuperación.
            </p>
        </details>

    </section>


    <!-- =====================================================
         13. CONTACTO
    ====================================================== -->

    <section
        class="seccion"
        id="contacto"
    >

        <div class="seccion-header">

            <div class="icono">
                ☎️
            </div>

            <h2>
                13. Contacto y Soporte
            </h2>

        </div>

        <p>
            Para asistencia sobre el uso del sistema,
            utilice los datos institucionales
            definidos para soporte.
        </p>

        <div class="contacto-grid">

            <div class="contacto">
                <strong>Responsable</strong>
                <span>
                    <?php
                    echo htmlspecialchars(
                        $soporte['responsable']
                        ?? 'Completar',
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>
                </span>
            </div>

            <div class="contacto">
                <strong>Área</strong>
                <span>
                    <?php
                    echo htmlspecialchars(
                        $soporte['area']
                        ?? 'Completar',
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>
                </span>
            </div>

            <div class="contacto">
                <strong>Correo electrónico</strong>
                <span>
                    <?php
                    echo htmlspecialchars(
                        $soporte['email']
                        ?? 'Completar',
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>
                </span>
            </div>

            <div class="contacto">
                <strong>Teléfono / Interno</strong>
                <span>
                    <?php
                    echo htmlspecialchars(
                        $soporte['telefono']
                        ?? 'Completar',
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>
                </span>
            </div>

            <div class="contacto">
                <strong>Horario</strong>
                <span>
                    <?php
                    echo htmlspecialchars(
                        $soporte['horario']
                        ?? 'Completar',
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>
                </span>
            </div>

            <div class="contacto">
                <strong>Sistema</strong>
                <span>
                    <?php
                    echo htmlspecialchars(
                        $sistema['nombre']
                        ?? 'SIGENMUNI',
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>
                    -
                    Versión
                    <?php
                    echo htmlspecialchars(
                        $sistema['version']
                        ?? '1.0',
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>
                </span>
            </div>

        </div>

        <div class="aviso aviso-importante">

            No comparta contraseñas,
            códigos de recuperación,
            tokens
            ni otros datos de acceso
            cuando solicite asistencia.

        </div>

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

        Manual de Usuario

    </div>


</div>

</body>

</html>
