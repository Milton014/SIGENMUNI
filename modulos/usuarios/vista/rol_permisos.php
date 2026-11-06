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
    sigenmuniUrlRuta('usuarios/roles/permisos', array_merge($contextoRoles, [
            'id' =>
                $rolPermisosId
        ]));


$rolPermisosUrlVolver =
    sigenmuniUrlRuta('usuarios/roles', $contextoRoles);


$rolPermisosCsrfToken =
    sigenmuniCsrfToken();

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Permisos del Rol - SIGENMUNI</title>

<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/usuarios/rol_permisos.css'), ENT_QUOTES, 'UTF-8') ?>">

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


<script src="<?= htmlspecialchars(sigenmuniUrlAsset('js/usuarios/rol_permisos.js'), ENT_QUOTES, 'UTF-8') ?>"></script>


</body>

</html>