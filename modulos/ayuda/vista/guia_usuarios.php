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

<title>Guía de Usuarios, Roles y Permisos - SIGENMUNI</title>

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
    background:linear-gradient(135deg,#7c3aed,#8b5cf6);
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

.badge{
    display:inline-block;
    padding:5px 9px;
    border-radius:999px;
    font-size:11px;
    font-weight:bold;
    white-space:nowrap;
}

.badge-admin{
    background:#fef3c7;
    color:#92400e;
}

.badge-permitido{
    background:#dcfce7;
    color:#166534;
}

.badge-bloqueado{
    background:#fee2e2;
    color:#991b1b;
}


/* =========================================================
   FLUJO DE ACCESO
========================================================= */

.acceso-grid{
    display:grid;
    grid-template-columns:repeat(2,minmax(0,1fr));
    gap:12px;
    margin-top:14px;
}

.acceso-card{
    border:1px solid #e2e8f0;
    border-radius:13px;
    padding:15px;
    background:#f8fafc;
}

.acceso-card strong{
    display:block;
    margin-bottom:6px;
    color:#6d28d9;
}

.acceso-card p{
    margin:0;
}

@media(max-width:700px){

    .acceso-grid{
        grid-template-columns:1fr;
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
    'Centro de Ayuda - Usuarios, Roles y Permisos';

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
                    Guía de Usuarios, Roles y Permisos
                </h1>

                <p>
                    Instrucciones para administrar usuarios,
                    roles, estados y permisos de acceso en SIGENMUNI.
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

            <a href="#acceso">
                Cómo se determina el acceso
            </a>

            <a href="#usuarios">
                Gestión de Usuarios
            </a>

            <a href="#primer-ingreso">
                Primer ingreso
            </a>

            <a href="#claves">
                Contraseñas y recuperación
            </a>

            <a href="#roles">
                Gestión de Roles
            </a>

            <a href="#permisos">
                Permisos
            </a>

            <a href="#administrador">
                Rol Administrador
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
                🔐
            </div>

            <h2>
                Objetivo del módulo
            </h2>

        </div>

        <p>
            La administración de
            <strong>Usuarios, Roles y Permisos</strong>
            permite controlar quién puede ingresar al sistema
            y qué módulos puede utilizar cada usuario.
        </p>

        <p>
            Un usuario posee un rol,
            y el rol determina los permisos
            disponibles para acceder a los módulos.
        </p>

        <div class="aviso aviso-importante">

            <strong>Importante:</strong>
            las funciones administrativas
            deben utilizarse solamente
            por usuarios autorizados.
            Los permisos no deben asignarse
            sin verificar previamente
            las responsabilidades del rol.

        </div>

    </section>


    <!-- =====================================================
         CÓMO SE DETERMINA EL ACCESO
    ====================================================== -->

    <section
        class="seccion"
        id="acceso"
    >

        <div class="seccion-header">

            <div class="icono">
                🚦
            </div>

            <h2>
                Cómo se determina el acceso
            </h2>

        </div>

        <p>
            Para que un usuario pueda utilizar SIGENMUNI,
            el sistema verifica varias condiciones.
            No alcanza únicamente con conocer
            el nombre de usuario y la contraseña.
        </p>

        <div class="acceso-grid">

            <div class="acceso-card">

                <strong>
                    1. Usuario activo
                </strong>

                <p>
                    La cuenta debe encontrarse activa.
                    Un usuario inactivo no debe poder ingresar.
                </p>

            </div>


            <div class="acceso-card">

                <strong>
                    2. Rol activo
                </strong>

                <p>
                    El rol asignado también debe estar activo.
                    Si el rol está inactivo,
                    el acceso se bloquea aunque la cuenta
                    del usuario siga activa.
                </p>

            </div>


            <div class="acceso-card">

                <strong>
                    3. Rol administrador
                </strong>

                <p>
                    Si el rol posee condición de administrador,
                    se considera con acceso total
                    según las reglas de seguridad del sistema.
                </p>

            </div>


            <div class="acceso-card">

                <strong>
                    4. Rol común
                </strong>

                <p>
                    Para un rol común,
                    SIGENMUNI consulta los permisos
                    configurados para el módulo o archivo solicitado.
                </p>

            </div>


            <div class="acceso-card">

                <strong>
                    5. Validación por URL
                </strong>

                <p>
                    El permiso se vuelve a verificar
                    en el archivo de entrada.
                    Conocer o escribir una URL
                    no evita la validación de seguridad.
                </p>

            </div>


            <div class="acceso-card">

                <strong>
                    6. Primer ingreso
                </strong>

                <p>
                    Cuando corresponde,
                    la cuenta debe completar
                    el flujo previsto para primer ingreso
                    antes de continuar con el uso normal.
                </p>

            </div>

        </div>

        <div class="aviso aviso-importante">

            Ocultar opciones del menú ayuda a la experiencia de usuario,
            pero la seguridad real depende
            de validar sesión, rol y permisos
            en los archivos protegidos.

        </div>

    </section>


    <!-- =====================================================
         USUARIOS
    ====================================================== -->

    <section
        class="seccion"
        id="usuarios"
    >

        <div class="seccion-header">

            <div class="icono">
                👤
            </div>

            <h2>
                Gestión de Usuarios
            </h2>

        </div>

        <p>
            Desde Gestión de Usuarios
            se administran las cuentas
            utilizadas para iniciar sesión en SIGENMUNI.
        </p>

        <h3>
            Crear un usuario
        </h3>

        <div class="pasos">

            <div class="paso">

                <div class="numero">
                    1
                </div>

                <div>

                    <strong>
                        Completar datos personales
                    </strong>

                    <p>
                        Ingrese nombre, apellido, DNI
                        y demás información solicitada.
                    </p>

                </div>

            </div>


            <div class="paso">

                <div class="numero">
                    2
                </div>

                <div>

                    <strong>
                        Definir nombre de usuario
                    </strong>

                    <p>
                        Debe identificar claramente
                        la cuenta utilizada para ingresar.
                    </p>

                </div>

            </div>


            <div class="paso">

                <div class="numero">
                    3
                </div>

                <div>

                    <strong>
                        Ingresar correo electrónico
                    </strong>

                    <p>
                        Utilice una dirección válida
                        y correspondiente al usuario.
                    </p>

                </div>

            </div>


            <div class="paso">

                <div class="numero">
                    4
                </div>

                <div>

                    <strong>
                        Asignar un rol
                    </strong>

                    <p>
                        Seleccione un rol activo
                        acorde a las funciones
                        y responsabilidades de la cuenta.
                    </p>

                </div>

            </div>


            <div class="paso">

                <div class="numero">
                    5
                </div>

                <div>

                    <strong>
                        Definir contraseña
                    </strong>

                    <p>
                        La contraseña debe cumplir
                        la política de seguridad
                        utilizada por SIGENMUNI.
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
                        Revise todos los datos
                        antes de crear la cuenta.
                    </p>

                </div>

            </div>

        </div>


        <div class="aviso aviso-importante">

            <strong>Datos únicos:</strong>
            DNI, nombre de usuario y correo electrónico
            no deben repetirse entre cuentas.
            Si el sistema informa un duplicado,
            revise primero los usuarios ya registrados.

        </div>


        <h3>
            Editar un usuario
        </h3>

        <p>
            La edición permite actualizar
            nombre, apellido, DNI,
            nombre de usuario, correo electrónico
            y rol asignado,
            según las opciones habilitadas.
        </p>

        <div class="aviso aviso-info">

            Si no se desea cambiar la contraseña
            durante una edición,
            utilice el comportamiento previsto
            por el formulario y deje sin modificar
            el dato de clave.
            No debe reemplazarse una contraseña
            por un valor vacío.

        </div>


        <h3>
            Activar o inactivar
        </h3>

        <p>
            Un usuario inactivo
            no debe poder utilizar su cuenta
            para ingresar a SIGENMUNI.
            La inactivación conserva
            el registro y su historial.
        </p>

        <div class="aviso aviso-alerta">

            SIGENMUNI protege al usuario
            que se encuentra actualmente conectado
            para evitar que se inactive
            accidentalmente su propia cuenta
            durante la sesión administrativa.

        </div>

    </section>


    <!-- =====================================================
         PRIMER INGRESO
    ====================================================== -->

    <section
        class="seccion"
        id="primer-ingreso"
    >

        <div class="seccion-header">

            <div class="icono">
                🆕
            </div>

            <h2>
                Primer ingreso
            </h2>

        </div>

        <p>
            SIGENMUNI utiliza el estado
            <strong>primer_ingreso</strong>
            para identificar cuentas
            que deben completar el flujo previsto
            para su primer acceso.
        </p>

        <h3>
            Resetear primer ingreso
        </h3>

        <p>
            Desde Gestión de Usuarios,
            un administrador puede utilizar
            la opción de resetear primer ingreso
            cuando necesite que la cuenta
            vuelva a pasar por ese flujo.
        </p>

        <div class="aviso aviso-info">

            Esta acción no elimina el usuario
            ni modifica sus permisos.
            Se utiliza únicamente
            para restablecer la condición
            de primer ingreso prevista por el sistema.

        </div>

    </section>


    <!-- =====================================================
         CONTRASEÑAS
    ====================================================== -->

    <section
        class="seccion"
        id="claves"
    >

        <div class="seccion-header">

            <div class="icono">
                🔑
            </div>

            <h2>
                Contraseñas y recuperación de acceso
            </h2>

        </div>

        <p>
            Las contraseñas deben cumplir
            la política mínima de seguridad
            utilizada por SIGENMUNI.
        </p>

        <div class="tabla-contenedor">

            <table>

                <thead>

                    <tr>
                        <th>Requisito</th>
                        <th>Descripción</th>
                    </tr>

                </thead>

                <tbody>

                    <tr>
                        <td>Longitud mínima</td>
                        <td>
                            Al menos 8 caracteres.
                        </td>
                    </tr>

                    <tr>
                        <td>Mayúscula</td>
                        <td>
                            Debe incluir
                            al menos una letra mayúscula.
                        </td>
                    </tr>

                    <tr>
                        <td>Carácter especial</td>
                        <td>
                            Debe incluir
                            al menos un carácter especial,
                            por ejemplo @, #, $, %, !.
                        </td>
                    </tr>

                </tbody>

            </table>

        </div>

        <h3>
            Recuperación de acceso
        </h3>

        <p>
            El flujo de recuperación
            se encuentra restringido
            a las cuentas y operaciones administrativas
            habilitadas por SIGENMUNI.
        </p>

        <p>
            Cuando el sistema utilice
            códigos de verificación
            o envío por correo,
            esos datos deben mantenerse privados
            y utilizarse solamente
            durante el proceso de recuperación.
        </p>

        <div class="aviso aviso-alerta">

            No comparta contraseñas,
            códigos de recuperación,
            credenciales de correo
            ni otros datos de autenticación
            dentro de la ayuda,
            reportes, observaciones
            o auditoría.

        </div>

    </section>


    <!-- =====================================================
         ROLES
    ====================================================== -->

    <section
        class="seccion"
        id="roles"
    >

        <div class="seccion-header">

            <div class="icono">
                👥
            </div>

            <h2>
                Gestión de Roles
            </h2>

        </div>

        <p>
            Los roles permiten agrupar permisos
            y asignarlos posteriormente
            a uno o más usuarios.
        </p>

        <h3>
            Crear un rol
        </h3>

        <div class="pasos">

            <div class="paso">

                <div class="numero">
                    1
                </div>

                <div>

                    <strong>
                        Ingresar nombre
                    </strong>

                    <p>
                        Utilice una denominación
                        representativa de la función.
                    </p>

                </div>

            </div>


            <div class="paso">

                <div class="numero">
                    2
                </div>

                <div>

                    <strong>
                        Agregar descripción
                    </strong>

                    <p>
                        Indique brevemente
                        el propósito del rol.
                    </p>

                </div>

            </div>


            <div class="paso">

                <div class="numero">
                    3
                </div>

                <div>

                    <strong>
                        Definir si es administrador
                    </strong>

                    <p>
                        Esta opción debe utilizarse
                        solamente para roles
                        que realmente requieran
                        acceso total al sistema.
                    </p>

                </div>

            </div>


            <div class="paso">

                <div class="numero">
                    4
                </div>

                <div>

                    <strong>
                        Configurar permisos
                    </strong>

                    <p>
                        Para roles comunes,
                        habilite únicamente
                        los módulos necesarios.
                    </p>

                </div>

            </div>

        </div>

        <div class="aviso aviso-info">

            Los roles comunes
            pueden tener combinaciones
            diferentes de permisos
            según sus responsabilidades.
            Si un rol se encuentra inactivo,
            los usuarios asignados a ese rol
            no deben poder ingresar al sistema.

        </div>

    </section>


    <!-- =====================================================
         PERMISOS
    ====================================================== -->

    <section
        class="seccion"
        id="permisos"
    >

        <div class="seccion-header">

            <div class="icono">
                ✅
            </div>

            <h2>
                Permisos por módulo
            </h2>

        </div>

        <p>
            La pantalla de Permisos
            permite habilitar o bloquear
            el acceso de un rol común
            a los módulos y archivos funcionales
            registrados en el sistema.
        </p>

        <div class="tabla-contenedor">

            <table>

                <thead>

                    <tr>
                        <th>Estado</th>
                        <th>Significado</th>
                    </tr>

                </thead>

                <tbody>

                    <tr>
                        <td>
                            <span class="badge badge-permitido">
                                Permitido
                            </span>
                        </td>
                        <td>
                            El rol puede acceder
                            al módulo correspondiente.
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <span class="badge badge-bloqueado">
                                Bloqueado
                            </span>
                        </td>
                        <td>
                            El rol no debe acceder
                            al módulo correspondiente.
                        </td>
                    </tr>

                </tbody>

            </table>

        </div>

        <h3>
            Consultas y Reportes
        </h3>

        <p>
            Los reportes específicos
            pueden tener permisos independientes.
            Cuando un reporte hijo está habilitado,
            el acceso al módulo padre
            Consultas y Reportes
            debe permanecer disponible.
        </p>

        <h3>
            Ayuda
        </h3>

        <p>
            El módulo Ayuda también forma parte
            del sistema de permisos.
            El administrador puede habilitarlo
            o bloquearlo para cada rol común.
        </p>

        <div class="aviso aviso-importante">

            Ocultar una tarjeta en el menú
            no es suficiente como medida de seguridad.
            Cada archivo de entrada del módulo
            debe validar también
            el permiso correspondiente.
            Por eso un acceso directo por URL
            continúa sujeto al control del rol.

        </div>

    </section>


    <!-- =====================================================
         ADMINISTRADOR
    ====================================================== -->

    <section
        class="seccion"
        id="administrador"
    >

        <div class="seccion-header">

            <div class="icono">
                🛡️
            </div>

            <h2>
                Rol Administrador
            </h2>

        </div>

        <p>
            Los roles marcados como administradores
            poseen acceso total
            según la lógica de seguridad
            implementada en SIGENMUNI.
        </p>

        <div class="tabla-contenedor">

            <table>

                <thead>

                    <tr>
                        <th>Tipo</th>
                        <th>Acceso</th>
                        <th>Comportamiento</th>
                    </tr>

                </thead>

                <tbody>

                    <tr>
                        <td>
                            <span class="badge badge-admin">
                                ADMIN principal
                            </span>
                        </td>
                        <td>
                            Acceso total.
                        </td>
                        <td>
                            Rol protegido.
                            Sus permisos permanecen habilitados
                            y no debe inactivarse.
                        </td>
                    </tr>

                    <tr>
                        <td>
                            Rol con es_admin = 1
                        </td>
                        <td>
                            Acceso administrativo total
                            según la lógica del sistema.
                        </td>
                        <td>
                            Debe asignarse solamente
                            cuando realmente se requiera
                            acceso global.
                        </td>
                    </tr>

                    <tr>
                        <td>
                            Rol común
                        </td>
                        <td>
                            Acceso configurable.
                        </td>
                        <td>
                            Utiliza los permisos
                            asignados a cada módulo o archivo.
                        </td>
                    </tr>

                </tbody>

            </table>

        </div>

        <h3>
            Protección del ADMIN principal
        </h3>

        <p>
            El rol principal identificado como
            <strong>ADMIN</strong>
            y configurado con
            <strong>es_admin = 1</strong>
            se considera un rol protegido del sistema.
        </p>

        <ul>

            <li>
                No debe inactivarse.
            </li>

            <li>
                Sus permisos no deben deshabilitarse individualmente.
            </li>

            <li>
                La pantalla de permisos debe conservar
                el acceso total correspondiente.
            </li>

            <li>
                No debe utilizarse como un rol común
                para realizar pruebas de restricciones.
            </li>

        </ul>

        <div class="aviso aviso-alerta">

            La condición de administrador
            debe asignarse con precaución.
            Un rol con acceso total
            puede ingresar a funciones
            administrativas y protegidas
            que no corresponden a un usuario común.

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
                📌
            </div>

            <h2>
                Recomendaciones de uso
            </h2>

        </div>

        <ul>

            <li>
                Cree usuarios individuales
                y evite compartir cuentas.
            </li>

            <li>
                Verifique que DNI,
                nombre de usuario y email
                no correspondan a otra cuenta.
            </li>

            <li>
                Asigne solamente
                los permisos necesarios
                para cada función.
            </li>

            <li>
                Mantenga activos
                solamente los usuarios
                y roles que correspondan.
            </li>

            <li>
                Inactive cuentas
                que ya no deban utilizarse
                en lugar de eliminar antecedentes.
            </li>

            <li>
                Evite asignar
                <strong>es_admin = 1</strong>
                si no es estrictamente necesario.
            </li>

            <li>
                No intente modificar
                las protecciones del ADMIN principal.
            </li>

            <li>
                Verifique los permisos
                después de crear
                o modificar un rol común.
            </li>

            <li>
                Pruebe el acceso
                con un usuario del rol
                para confirmar
                que los permisos sean correctos.
            </li>

            <li>
                Recuerde que un rol inactivo
                bloquea el acceso
                de los usuarios que lo utilizan.
            </li>

            <li>
                No almacene contraseñas
                ni códigos de recuperación
                en texto visible,
                observaciones,
                reportes o auditoría.
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
                Mi usuario está activo pero no puedo ingresar
            </summary>

            <p>
                Revise también el rol asignado.
                Si el rol está inactivo,
                el sistema debe bloquear el acceso
                aunque la cuenta del usuario
                permanezca activa.
            </p>

        </details>


        <details>

            <summary>
                Un usuario no puede ingresar a un módulo
            </summary>

            <p>
                Revise que el usuario esté activo,
                que el rol esté activo
                y que el permiso exacto
                del módulo o archivo
                se encuentre habilitado.
            </p>

        </details>


        <details>

            <summary>
                El permiso está marcado pero la tarjeta no aparece
            </summary>

            <p>
                Revise que el archivo utilizado
                en el catálogo de permisos
                coincida exactamente
                con el archivo configurado
                en el menú principal.
            </p>

        </details>


        <details>

            <summary>
                Un usuario puede intentar entrar escribiendo la URL
            </summary>

            <p>
                El archivo de entrada
                debe ejecutar la validación
                del permiso del módulo.
                De esa manera,
                aunque el usuario conozca la URL,
                el acceso será rechazado
                si no posee autorización.
            </p>

        </details>


        <details>

            <summary>
                No puedo modificar o inactivar el ADMIN principal
            </summary>

            <p>
                Es el comportamiento esperado.
                El rol ADMIN principal
                con es_admin = 1
                se encuentra protegido
                para conservar un acceso administrativo válido.
            </p>

        </details>


        <details>

            <summary>
                El rol administrador no permite desmarcar permisos
            </summary>

            <p>
                Es el comportamiento esperado
                para un rol con acceso total.
                Sus permisos deben permanecer habilitados.
            </p>

        </details>


        <details>

            <summary>
                No puedo inactivar un rol
            </summary>

            <p>
                Revise si existen usuarios activos
                asignados a ese rol
                o si se trata de un rol administrador
                protegido por las reglas del sistema.
            </p>

        </details>


        <details>

            <summary>
                No puedo inactivar mi propio usuario
            </summary>

            <p>
                Es una protección del sistema.
                El usuario que se encuentra
                actualmente conectado
                no debe inactivar accidentalmente
                su propia cuenta.
            </p>

        </details>


        <details>

            <summary>
                ¿Qué hace Resetear primer ingreso?
            </summary>

            <p>
                Restablece la condición
                para que la cuenta vuelva
                a pasar por el flujo
                previsto para primer ingreso.
                No elimina el usuario
                ni cambia automáticamente su rol.
            </p>

        </details>


        <details>

            <summary>
                La contraseña nueva no es aceptada
            </summary>

            <p>
                Verifique que cumpla
                la longitud mínima,
                que incluya una letra mayúscula
                y al menos un carácter especial.
            </p>

        </details>


        <details>

            <summary>
                Ayuda no aparece para un rol
            </summary>

            <p>
                Revise la configuración
                de permisos del rol
                y confirme que el módulo Ayuda
                se encuentre marcado como permitido.
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

        Guía de Usuarios, Roles y Permisos

    </div>


</div>

</body>

</html>
