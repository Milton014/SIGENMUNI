<?php

/*
|--------------------------------------------------------------------------
| PERMISOS DEL ROL - NAVEGACIÓN ROUTER
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../../../core/Url.php';
require_once __DIR__ . '/../../../core/Csrf.php';


$rolPermisosId =
    (int)(
        $rol['id']
        ??
        $_GET['id']
        ??
        0
    );


$rolPermisosAccion =
    sigenmuniUrlRuta(
        'usuarios/roles/permisos',
        [
            'id' =>
                $rolPermisosId
        ]
    );


$rolPermisosUrlVolver =
    sigenmuniUrlRuta(
        'usuarios/roles'
    );


$rolPermisosCsrfToken =
    sigenmuniCsrfToken();

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Permisos del Rol - SIGENMUNI</title>

<style>

*{
    box-sizing:border-box;
}

body{
    font-family:Arial,sans-serif;
    background:#f4f7fb;
    margin:0;
    color:#1f2937;
}


/* =========================================
   HEADER
========================================= */

.header{
    background:linear-gradient(
        135deg,
        #0f766e,
        #14b8a6
    );

    color:white;
    padding:22px 30px;

    box-shadow:
        0 4px 14px rgba(0,0,0,.12);
}

.header h1{
    margin:0 0 5px 0;
    font-size:30px;
}

.header p{
    margin:0;
    font-size:15px;
    opacity:.95;
}


/* =========================================
   CONTENEDOR
========================================= */

.contenedor{
    width:95%;
    max-width:1100px;
    margin:30px auto;
}


/* =========================================
   PANEL
========================================= */

.panel{
    background:white;
    border-radius:18px;
    padding:25px;

    box-shadow:
        0 8px 20px rgba(0,0,0,.10);
}

.panel-header{
    margin-bottom:20px;
}

.panel-header h2{
    margin:0 0 8px 0;
    color:#0f172a;
}

.panel-header p{
    margin:0;
    color:#6b7280;
    font-size:14px;
    line-height:1.5;
}


/* =========================================
   ROL
========================================= */

.nombre-rol{
    background:#eff6ff;
    border:1px solid #bfdbfe;
    color:#1e40af;

    padding:14px;

    border-radius:12px;

    margin-bottom:20px;

    line-height:1.5;
}

.nombre-rol strong{
    color:#1e3a8a;
}


/* =========================================
   ADVERTENCIA
========================================= */

.advertencia{
    background:#fef3c7;
    color:#92400e;

    border:
        1px solid #fcd34d;

    padding:14px;

    border-radius:12px;

    margin-bottom:20px;

    line-height:1.5;

    font-weight:bold;
}


/* =========================================
   ERROR
========================================= */

.error{
    background:#fee2e2;
    color:#991b1b;

    border:
        1px solid #fecaca;

    padding:14px;

    border-radius:12px;

    margin-bottom:20px;

    font-weight:bold;
}


/* =========================================
   HERRAMIENTAS
========================================= */

.herramientas{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
    margin-bottom:18px;
}

.btn-herramienta{
    border:none;
    padding:9px 13px;
    border-radius:9px;
    cursor:pointer;
    font-weight:bold;
    font-size:13px;
}

.btn-todos{
    background:#dcfce7;
    color:#166534;
}

.btn-ninguno{
    background:#fee2e2;
    color:#991b1b;
}


/* =========================================
   TABLA
========================================= */

.tabla-responsive{
    width:100%;
    overflow-x:auto;

    border:
        1px solid #e5e7eb;

    border-radius:14px;
}

table{
    width:100%;
    min-width:620px;
    border-collapse:collapse;
}

th{
    background:#0f766e;
    color:white;
    font-size:14px;
}

th,
td{
    padding:12px;

    border-bottom:
        1px solid #e5e7eb;

    text-align:left;

    vertical-align:middle;
}

tr:hover td{
    background:#f9fafb;
}


/* =========================================
   ESTADO
========================================= */

.estado-permitido{
    background:#dcfce7;
    color:#166534;

    font-weight:bold;

    padding:6px 10px;

    border-radius:999px;

    display:inline-block;

    font-size:13px;
}

.estado-bloqueado{
    background:#fee2e2;
    color:#991b1b;

    font-weight:bold;

    padding:6px 10px;

    border-radius:999px;

    display:inline-block;

    font-size:13px;
}


/* =========================================
   CHECKBOX
========================================= */

input[type="checkbox"]{
    width:20px;
    height:20px;

    cursor:pointer;

    accent-color:#0f766e;
}

input[type="checkbox"]:disabled{
    cursor:not-allowed;
}


/* =========================================
   BOTONES
========================================= */

.btn{
    border:none;

    padding:12px 16px;

    border-radius:10px;

    font-weight:bold;

    cursor:pointer;

    text-decoration:none;

    display:inline-block;

    transition:.2s;

    font-size:14px;

    text-align:center;
}

.btn:hover{
    opacity:.92;
    transform:translateY(-1px);
}

.btn-guardar{
    background:#0f766e;
    color:white;
}

.btn-volver{
    background:#1f2937;
    color:white;
}

.acciones{
    margin-top:20px;

    display:flex;
    gap:10px;
    flex-wrap:wrap;
}



/* =========================================
   GRUPOS DE PERMISOS
========================================= */

.grupo-permisos{
    margin-bottom:22px;
}

.grupo-permisos:last-child{
    margin-bottom:0;
}

.grupo-titulo{
    display:flex;
    align-items:center;
    gap:10px;
    margin:18px 0 10px;
    padding:10px 12px;
    border-radius:10px;
    background:#f8fafc;
    border:1px solid #e5e7eb;
    font-weight:bold;
    color:#0f172a;
}

.grupo-titulo:first-child{
    margin-top:0;
}

.grupo-titulo .emoji{
    font-size:18px;
}

.fila-grupo td{
    background:#f8fafc !important;
    color:#0f172a;
    font-weight:bold;
    border-top:2px solid #e2e8f0;
}

.fila-grupo td:first-child{
    border-left:4px solid #0f766e;
}

.descripcion-grupo{
    color:#64748b;
    font-size:12px;
    font-weight:normal;
    margin-top:3px;
}

/* =========================================
   TARJETAS CELULAR
========================================= */

.permisos-cards{
    display:none;
}

.permiso-card{
    background:white;

    border:
        1px solid #e5e7eb;

    border-radius:16px;

    padding:16px;

    margin-bottom:14px;

    box-shadow:
        0 6px 14px rgba(0,0,0,.08);
}

.permiso-card h3{
    margin:0 0 8px 0;

    color:#0f172a;

    font-size:18px;
}

.permiso-card p{
    margin:8px 0;

    color:#374151;

    font-size:14px;

    line-height:1.4;
}

.permiso-card-footer{
    display:flex;

    justify-content:
        space-between;

    align-items:center;

    gap:12px;

    margin-top:14px;

    padding-top:12px;

    border-top:
        1px solid #e5e7eb;
}

.check-movil{
    display:flex;

    align-items:center;

    gap:10px;

    font-weight:bold;

    color:#0f172a;
}


/* =========================================
   CELULAR
========================================= */

@media(max-width:768px){

    .header{
        text-align:center;
        padding:18px 16px;
    }

    .header h1{
        font-size:25px;
    }

    .contenedor{
        width:94%;
        margin:20px auto;
    }

    .panel{
        padding:18px;
        border-radius:16px;
    }

    .panel-header{
        text-align:center;
    }

    .nombre-rol,
    .advertencia,
    .error{
        font-size:14px;
        text-align:center;
    }

    .herramientas{
        flex-direction:column;
    }

    .btn-herramienta{
        width:100%;
        padding:12px;
    }

    .tabla-responsive{
        display:none;
    }

    .permisos-cards{
        display:block;
    }

    .acciones{
        flex-direction:column;
    }

    .btn{
        width:100%;
        padding:13px;
    }

    .permiso-card-footer{
        flex-direction:column;
        align-items:stretch;
    }

    .check-movil{
        justify-content:space-between;

        background:#f9fafb;

        padding:12px;

        border-radius:12px;

        border:
            1px solid #e5e7eb;
    }

    input[type="checkbox"]{
        width:24px;
        height:24px;
    }
}

</style>

</head>


<body>


<!-- =========================================
     HEADER
========================================= -->

<div class="header">

    <h1>
        SIGENMUNI
    </h1>

    <p>
        Permisos del Rol
    </p>

</div>


<div class="contenedor">


    <div class="panel">


        <!-- =================================
             CABECERA
        ================================== -->

        <div class="panel-header">

            <h2>
                Configuración de Permisos
            </h2>

            <p>
                Desde esta pantalla puede habilitar o bloquear
                el acceso del rol a cada módulo del sistema.
            </p>

        </div>


        <!-- =================================
             ROL
        ================================== -->

        <div class="nombre-rol">

            Rol seleccionado:

            <strong>

                <?php
                echo htmlspecialchars(
                    $rol['nombre']
                    ?? '',
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>

            </strong>

        </div>


        <!-- =================================
             ERROR
        ================================== -->

        <?php if (!empty($error)): ?>

            <div class="error">

                <?php
                echo htmlspecialchars(
                    $error,
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>

            </div>

        <?php endif; ?>


        <!-- =================================
             ADMIN PRINCIPAL
        ================================== -->

        <?php if ($permisosProtegidos): ?>

            <div class="advertencia">

                Este rol tiene privilegios de administrador.

                Tiene acceso total al sistema y sus permisos
                no pueden deshabilitarse individualmente.

            </div>

        <?php endif; ?>


        <form
            method="POST"
            action="<?php
                echo htmlspecialchars(
                    $rolPermisosAccion,
                    ENT_QUOTES,
                    'UTF-8'
                );
            ?>"
            id="formPermisos"
        >

            

                <input
                    type="hidden"
                    name="_csrf"
                    value="<?php
                        echo htmlspecialchars(
                            $rolPermisosCsrfToken,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                >

            


            <!-- =================================
                 SELECCIÓN RÁPIDA
            ================================== -->

            <?php if (!$permisosProtegidos): ?>

                <div class="herramientas">

                    <button
                        type="button"
                        class="btn-herramienta btn-todos"
                        id="seleccionarTodos"
                    >
                        Seleccionar todos
                    </button>


                    <button
                        type="button"
                        class="btn-herramienta btn-ninguno"
                        id="quitarTodos"
                    >
                        Quitar todos
                    </button>

                </div>

            <?php endif; ?>


            <!-- =================================
                 TABLA ESCRITORIO
            ================================== -->

            <div class="tabla-responsive">


                <table>


                    <thead>

                        <tr>

                            <th>
                                Módulo
                            </th>

                            <th>
                                Estado
                            </th>

                            <th>
                                Permitir
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php

                    $gruposPermisos = [

                        'gestion' => [
                            'titulo' => 'Gestión',
                            'icono' => '🛠️',
                            'descripcion' => 'Módulos operativos y de administración diaria.',
                            'archivos' => [
                                'empleados.php',
                                'empleado_conceptos.php',
                                'conceptos.php',
                                'categorias.php',
                                'liquidacion.php'
                            ]
                        ],

                        'reportes' => [
                            'titulo' => 'Consultas y Reportes',
                            'icono' => '📊',
                            'descripcion' => 'Accesos de consulta, reportes y estadísticas.',
                            'archivos' => [
                                'reportes.php',
                                'reporte_empleados.php',
                                'reporte_historial_empleado.php',
                                'reporte_conceptos.php',
                                'reporte_categorias.php',
                                'reporte_liquidaciones.php',
                                'estadisticas.php',
                                'reporte_auditoria.php'
                            ]
                        ],

                        'administracion' => [
                            'titulo' => 'Administración y Ayuda',
                            'icono' => '🔐',
                            'descripcion' => 'Administración del sistema y documentación de ayuda.',
                            'archivos' => [
                                'usuarios.php',
                                'ayuda.php'
                            ]
                        ]
                    ];


                    $permisosIndexados = [];

                    foreach ($permisos as $permisoItem) {

                        $permisosIndexados[
                            (string)$permisoItem['archivo']
                        ] =
                            $permisoItem;
                    }

                    ?>


                    <?php foreach ($gruposPermisos as $grupo): ?>

                        <tr class="fila-grupo">

                            <td colspan="3">

                                <?php echo $grupo['icono']; ?>

                                <?php
                                echo htmlspecialchars(
                                    $grupo['titulo'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                                <div class="descripcion-grupo">

                                    <?php
                                    echo htmlspecialchars(
                                        $grupo['descripcion'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>

                                </div>

                            </td>

                        </tr>


                        <?php foreach ($grupo['archivos'] as $archivoGrupo): ?>

                            <?php

                            if (!isset($permisosIndexados[$archivoGrupo])) {
                                continue;
                            }

                            $p =
                                $permisosIndexados[$archivoGrupo];

                            $archivo =
                                (string)$p['archivo'];

                            $permitido =
                                (int)$p['permitido'];

                            ?>

                            <tr>

                                <td>

                                    <strong>

                                        <?php
                                        echo htmlspecialchars(
                                            $p['modulo'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        );
                                        ?>

                                    </strong>

                                </td>

                                <td>

                                    <?php if ($permitido === 1): ?>

                                        <span class="estado-permitido">
                                            Permitido
                                        </span>

                                    <?php else: ?>

                                        <span class="estado-bloqueado">
                                            Bloqueado
                                        </span>

                                    <?php endif; ?>

                                </td>


                                <td>

                                    <input
                                        type="checkbox"
                                        class="check-principal permiso-check"

                                        data-archivo="<?php
                                            echo htmlspecialchars(
                                                $archivo,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                        ?>"

                                        name="permisos[]"

                                        value="<?php
                                            echo htmlspecialchars(
                                                $archivo,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                        ?>"

                                        <?php
                                        echo (
                                            $permitido === 1
                                        )
                                            ?
                                            'checked'
                                            :
                                            '';
                                        ?>

                                        <?php
                                        echo $permisosProtegidos
                                            ?
                                            'disabled'
                                            :
                                            '';
                                        ?>
                                    >

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php endforeach; ?>

                    </tbody>


                </table>


            </div>


            <!-- =================================
                 TARJETAS CELULAR
            ================================== -->

            <div class="permisos-cards">

                <?php foreach ($gruposPermisos as $grupo): ?>

                    <div class="grupo-titulo">

                        <span class="emoji">
                            <?php echo $grupo['icono']; ?>
                        </span>

                        <span>
                            <?php
                            echo htmlspecialchars(
                                $grupo['titulo'],
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>
                        </span>

                    </div>


                    <?php foreach ($grupo['archivos'] as $archivoGrupo): ?>

                        <?php

                        if (!isset($permisosIndexados[$archivoGrupo])) {
                            continue;
                        }

                        $p =
                            $permisosIndexados[$archivoGrupo];

                        $archivo =
                            (string)$p['archivo'];

                        $permitido =
                            (int)$p['permitido'];

                        ?>

                        <div class="permiso-card">

                            <h3>

                                <?php
                                echo htmlspecialchars(
                                    $p['modulo'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </h3>

                            <div class="permiso-card-footer">

                                <div>

                                    <?php if ($permitido === 1): ?>

                                        <span class="estado-permitido">
                                            Permitido
                                        </span>

                                    <?php else: ?>

                                        <span class="estado-bloqueado">
                                            Bloqueado
                                        </span>

                                    <?php endif; ?>

                                </div>


                                <label class="check-movil">

                                    Permitir acceso

                                    <input
                                        type="checkbox"
                                        class="check-secundario"

                                        data-archivo="<?php
                                            echo htmlspecialchars(
                                                $archivo,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                        ?>"

                                        <?php
                                        echo (
                                            $permitido === 1
                                        )
                                            ?
                                            'checked'
                                            :
                                            '';
                                        ?>

                                        <?php
                                        echo $permisosProtegidos
                                            ?
                                            'disabled'
                                            :
                                            '';
                                        ?>
                                    >

                                </label>

                            </div>

                        </div>

                    <?php endforeach; ?>

                <?php endforeach; ?>

            </div>

            </div>


            <!-- =================================
                 ACCIONES
            ================================== -->

            <div class="acciones">


                <?php if (!$permisosProtegidos): ?>


                    <button
                        type="submit"
                        class="btn btn-guardar"
                    >
                        Guardar Permisos
                    </button>


                <?php endif; ?>


                <a
                    href="<?php
                    echo htmlspecialchars(
                        $rolPermisosUrlVolver,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                    class="btn btn-volver"
                >
                    Volver
                </a>


            </div>


        </form>


    </div>


</div>


<script>

document.addEventListener(
    "DOMContentLoaded",
    function()
    {

        const principales =
            document.querySelectorAll(
                ".check-principal"
            );


        const secundarios =
            document.querySelectorAll(
                ".check-secundario"
            );


        /*
        |--------------------------------------------------------------------------
        | ESCRITORIO → CELULAR
        |--------------------------------------------------------------------------
        */

        principales.forEach(
            function(check)
            {

                check.addEventListener(
                    "change",
                    function()
                    {

                        const archivo =
                            this.dataset.archivo;


                        secundarios.forEach(
                            function(secundario)
                            {

                                if (
                                    secundario.dataset.archivo
                                    ===
                                    archivo
                                ) {

                                    secundario.checked =
                                        check.checked;
                                }

                            }
                        );

                    }
                );

            }
        );


        /*
        |--------------------------------------------------------------------------
        | CELULAR → ESCRITORIO
        |--------------------------------------------------------------------------
        |
        | Solo los checks principales tienen name="permisos[]".
        | Por eso sincronizamos el check móvil con el principal.
        |
        |--------------------------------------------------------------------------
        */

        secundarios.forEach(
            function(check)
            {

                check.addEventListener(
                    "change",
                    function()
                    {

                        const archivo =
                            this.dataset.archivo;


                        principales.forEach(
                            function(principal)
                            {

                                if (
                                    principal.dataset.archivo
                                    ===
                                    archivo
                                ) {

                                    principal.checked =
                                        check.checked;
                                }

                            }
                        );

                    }
                );

            }
        );


        /*
        |--------------------------------------------------------------------------
        | DEPENDENCIA CONSULTAS Y REPORTES
        |--------------------------------------------------------------------------
        |
        | Reglas visuales:
        |
        | - Si se marca cualquier reporte hijo, se marca reportes.php.
        | - Si se desmarca reportes.php, se desmarcan todos sus hijos.
        | - Si se marca reportes.php, no se marcan automáticamente los hijos:
        |   el administrador decide cuáles habilitar.
        |
        |--------------------------------------------------------------------------
        */

        const archivoPadreReportes =
            "reportes.php";


        const archivosReportesHijos = [

            "reporte_empleados.php",

            "reporte_historial_empleado.php",

            "reporte_conceptos.php",

            "reporte_categorias.php",

            "reporte_liquidaciones.php",

            "estadisticas.php",

            "reporte_auditoria.php"
        ];


        function obtenerPrincipalPorArchivo(
            archivo
        ) {

            return document.querySelector(
                '.check-principal[data-archivo="'
                +
                archivo
                +
                '"]'
            );
        }


        function obtenerSecundarioPorArchivo(
            archivo
        ) {

            return document.querySelector(
                '.check-secundario[data-archivo="'
                +
                archivo
                +
                '"]'
            );
        }


        function sincronizarCheckArchivo(
            archivo,
            checked
        ) {

            const principal =
                obtenerPrincipalPorArchivo(
                    archivo
                );


            const secundario =
                obtenerSecundarioPorArchivo(
                    archivo
                );


            if (
                principal
                &&
                !principal.disabled
            ) {

                principal.checked =
                    checked;
            }


            if (
                secundario
                &&
                !secundario.disabled
            ) {

                secundario.checked =
                    checked;
            }
        }


        function activarPadreReportes()
        {

            sincronizarCheckArchivo(
                archivoPadreReportes,
                true
            );
        }


        function desactivarHijosReportes()
        {

            archivosReportesHijos.forEach(
                function(archivo)
                {

                    sincronizarCheckArchivo(
                        archivo,
                        false
                    );
                }
            );
        }


        principales.forEach(
            function(check)
            {

                check.addEventListener(
                    "change",
                    function()
                    {

                        const archivo =
                            this.dataset.archivo;


                        if (
                            archivo
                            ===
                            archivoPadreReportes
                            &&
                            !this.checked
                        ) {

                            desactivarHijosReportes();
                        }


                        if (
                            archivosReportesHijos.includes(
                                archivo
                            )
                            &&
                            this.checked
                        ) {

                            activarPadreReportes();
                        }
                    }
                );
            }
        );


        secundarios.forEach(
            function(check)
            {

                check.addEventListener(
                    "change",
                    function()
                    {

                        const archivo =
                            this.dataset.archivo;


                        if (
                            archivo
                            ===
                            archivoPadreReportes
                            &&
                            !this.checked
                        ) {

                            desactivarHijosReportes();
                        }


                        if (
                            archivosReportesHijos.includes(
                                archivo
                            )
                            &&
                            this.checked
                        ) {

                            activarPadreReportes();
                        }
                    }
                );
            }
        );


        /*
        |--------------------------------------------------------------------------
        | SELECCIONAR TODOS
        |--------------------------------------------------------------------------
        */

        const seleccionarTodos =
            document.getElementById(
                "seleccionarTodos"
            );


        if (seleccionarTodos) {

            seleccionarTodos.addEventListener(
                "click",
                function()
                {

                    principales.forEach(
                        function(check)
                        {

                            if (!check.disabled) {
                                check.checked = true;
                            }

                        }
                    );


                    secundarios.forEach(
                        function(check)
                        {

                            if (!check.disabled) {
                                check.checked = true;
                            }

                        }
                    );

                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | QUITAR TODOS
        |--------------------------------------------------------------------------
        */

        const quitarTodos =
            document.getElementById(
                "quitarTodos"
            );


        if (quitarTodos) {

            quitarTodos.addEventListener(
                "click",
                function()
                {

                    principales.forEach(
                        function(check)
                        {

                            if (!check.disabled) {
                                check.checked = false;
                            }

                        }
                    );


                    secundarios.forEach(
                        function(check)
                        {

                            if (!check.disabled) {
                                check.checked = false;
                            }

                        }
                    );

                }
            );
        }

    }
);

</script>


</body>

</html>