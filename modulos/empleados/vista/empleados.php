<?php

require_once __DIR__ . '/../../../core/Url.php';


/*
|--------------------------------------------------------------------------
| GESTIÓN DE EMPLEADOS - SOLO ROUTER
|--------------------------------------------------------------------------
|
| "empleados.php" se conserva únicamente como clave de permiso.
| Toda la navegación del módulo utiliza las rutas registradas.
|
|--------------------------------------------------------------------------
*/

$empleadosUrlListado =
    sigenmuniUrlRuta(
        'empleados',
        ['por_pagina' => $paginacion['por_pagina']]
    );


$empleadosAccionBuscar =
    sigenmuniUrlEntrada();


$empleadosUrlNuevo =
    sigenmuniUrlRuta(
        'empleados/nuevo',
        $contextoListado
    );


$empleadosUrlMenu =
    sigenmuniUrlRuta(
    'inicio'
);


if (!function_exists('formatearCuitUnidadOrganizacion')) {

    function formatearCuitUnidadOrganizacion($cuit)
    {
        $cuit =
            preg_replace(
                '/\D+/',
                '',
                (string)$cuit
            );

        if (strlen($cuit) !== 11) {
            return (string)$cuit;
        }

        return
            substr($cuit, 0, 2)
            . '-'
            . substr($cuit, 2, 8)
            . '-'
            . substr($cuit, 10, 1);
    }
}

?>
<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/compartido/paginacion.css'), ENT_QUOTES, 'UTF-8') ?>">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Gestión de Personal - SIGENMUNI</title>

<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/empleados/empleados.css'), ENT_QUOTES, 'UTF-8') ?>">

</head>

<body>


<!-- =========================================
     HEADER GLOBAL
========================================= -->

<?php

$sigenmuniHeaderSubtitulo =
    'Gestión de Empleados Municipales';

require __DIR__
    . '/../../../componentes/header_sigenmuni.php';

?>


<!-- =========================================
     CONTENIDO
========================================= -->

<div class="contenedor">

    <div class="panel">


        <!-- =====================================
             TOPBAR
        ====================================== -->

        <div class="topbar">

            <h2>
                Gestión de Personal
            </h2>


            <div class="acciones-superiores">

                <a
                    href="<?php
                    echo htmlspecialchars(
                        $empleadosUrlNuevo,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                    class="btn btn-principal"
                >
                    + Nuevo Empleado
                </a>


                <a
                    href="<?php
                    echo htmlspecialchars(
                        $empleadosUrlMenu,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                    class="btn btn-sec"
                >
                    Volver al menú
                </a>

            </div>

        </div>


        <!-- =====================================
             MENSAJES
        ====================================== -->

        <?php if (!empty($mensaje)): ?>

            <div
                class="mensaje <?php
                    echo $tipo_mensaje === 'ok'
                        ? 'mensaje-ok'
                        : 'mensaje-error';
                ?>"
            >

                <?php
                echo htmlspecialchars(
                    $mensaje,
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>

            </div>

        <?php endif; ?>


        <!-- =====================================
             BUSCADOR
        ====================================== -->

        <div class="buscador">

            <form
                method="GET"
                action="<?php
                    echo htmlspecialchars(
                        $empleadosAccionBuscar,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
            >

                <input
                    type="hidden"
                    name="r"
                    value="empleados"
                >

                <input
                    type="text"
                    name="busqueda"
                    aria-label="Buscar empleados"
                    placeholder="Buscar por legajo, apellido, nombre, DNI, CUIL o email"
                    value="<?php
                        echo htmlspecialchars(
                            $busqueda ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                >


                <div class="paginacion-tamano">
                    <label for="por_pagina">Registros por página</label>
                    <select name="por_pagina" id="por_pagina">
                        <?php foreach ([10, 25, 50] as $cantidad): ?>
                            <option value="<?= $cantidad ?>" <?= $paginacion['por_pagina'] === $cantidad ? 'selected' : '' ?>><?= $cantidad ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button
                    type="submit"
                    class="btn btn-buscar"
                >
                    Buscar
                </button>


                <a
                    href="<?php
                    echo htmlspecialchars(
                        $empleadosUrlListado,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                    class="btn btn-sec"
                >
                    Limpiar
                </a>

            </form>

        </div>


        <!-- =====================================
             TABLA DE EMPLEADOS
        ====================================== -->

        <?php
        $paginacionRuta = 'empleados';
        $paginacionParametros = $contextoListado;
        $paginacionEntidad = 'empleados';
        require __DIR__ . '/../../../componentes/paginacion.php';
        ?>

        <?php if (!empty($empleados)): ?>

            <div class="tabla-responsive">

                <table>

                    <thead>

                        <tr>

                            <th>
                                Legajo
                            </th>

                            <th>
                                Apellido y Nombre
                            </th>

                            <th>
                                DNI
                            </th>

                            <th>
                                CUIL
                            </th>

                            <th>
                                Teléfono
                            </th>

                            <th>
                                Email
                            </th>

                            <th>
                                Fecha Alta
                            </th>

                            <th>
                                Fecha Inactivo
                            </th>

                            <th>
                                Categoría
                            </th>

                            <th>
                                Unidad de Organización
                            </th>

                            <th>
                                Situación
                            </th>

                            <th>
                                Estado
                            </th>

                            <th>
                                Acciones
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php foreach ($empleados as $fila): ?>

                        <tr>


                            <!-- =================================
                                 LEGAJO
                            ================================== -->

                            <td
                                class="col-legajo"
                                data-label="Legajo"
                            >

                                <?php
                                echo htmlspecialchars(
                                    $fila['nro_legajo'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </td>


                            <!-- =================================
                                 APELLIDO Y NOMBRE
                            ================================== -->

                            <td
                                class="col-nombre"
                                data-label="Apellido y Nombre"
                            >

                                <?php
                                echo htmlspecialchars(
                                    $fila['apellido']
                                    . ', '
                                    . $fila['nombre'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </td>


                            <!-- =================================
                                 DNI
                            ================================== -->

                            <td
                                class="col-dni"
                                data-label="DNI"
                            >

                                <?php
                                echo htmlspecialchars(
                                    $fila['dni'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </td>


                            <!-- =================================
                                 CUIL
                            ================================== -->

                            <td
                                class="col-cuil"
                                data-label="CUIL"
                            >

                                <?php
                                echo htmlspecialchars(
                                    $fila['cuil'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </td>


                            <!-- =================================
                                 TELÉFONO
                            ================================== -->

                            <td data-label="Teléfono">

                                <?php
                                echo htmlspecialchars(
                                    !empty($fila['telefono'])
                                        ? $fila['telefono']
                                        : '-',
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </td>


                            <!-- =================================
                                 EMAIL
                            ================================== -->

                            <td
                                class="col-email"
                                data-label="Email"
                            >

                                <?php
                                echo htmlspecialchars(
                                    !empty($fila['email'])
                                        ? $fila['email']
                                        : '-',
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </td>


                            <!-- =================================
                                 FECHA ALTA
                            ================================== -->

                            <td
                                class="col-fecha"
                                data-label="Fecha Alta"
                            >

                                <?php

                                if (!empty($fila['fecha_alta'])) {

                                    echo htmlspecialchars(
                                        date(
                                            'd/m/Y',
                                            strtotime(
                                                $fila['fecha_alta']
                                            )
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );

                                } else {

                                    echo '-';
                                }

                                ?>

                            </td>


                            <!-- =================================
                                 FECHA INACTIVO
                            ================================== -->

                            <td
                                class="col-fecha"
                                data-label="Fecha Inactivo"
                            >

                                <?php

                                if (!empty($fila['fecha_inactivo'])) {

                                    echo htmlspecialchars(
                                        date(
                                            'd/m/Y',
                                            strtotime(
                                                $fila['fecha_inactivo']
                                            )
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );

                                } else {

                                    echo '-';
                                }

                                ?>

                            </td>


                            <!-- =================================
                                 CATEGORÍA
                            ================================== -->

                            <td data-label="Categoría">

                                <?php
                                echo htmlspecialchars(
                                    $fila['categoria'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </td>


                            <!-- =================================
                                 UNIDAD DE ORGANIZACIÓN
                            ================================== -->

                            <td
                                class="unidad-organizacion"
                                data-label="Unidad de Organización"
                            >

                                <div class="nombre-unidad">

                                    <?php
                                    echo htmlspecialchars(
                                        $fila['oficina']
                                            ?? '-',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>

                                </div>

                                <?php if (!empty($fila['oficina_cuit'])): ?>

                                    <div class="cuit-unidad">

                                        CUIT:
                                        <?php
                                        echo htmlspecialchars(
                                            formatearCuitUnidadOrganizacion(
                                                $fila['oficina_cuit']
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        );
                                        ?>

                                    </div>

                                <?php endif; ?>

                            </td>


                            <!-- =================================
                                 SITUACIÓN
                            ================================== -->

                            <td data-label="Situación">

                                <?php
                                echo htmlspecialchars(
                                    $fila['situacion'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </td>


                            <!-- =================================
                                 ESTADO
                            ================================== -->

                            <td
                                class="col-estado"
                                data-label="Estado"
                            >

                                <?php if ((int)$fila['activo'] === 1): ?>

                                    <span class="estado-activo">
                                        Activo
                                    </span>

                                <?php else: ?>

                                    <span class="estado-inactivo">
                                        Inactivo
                                    </span>

                                <?php endif; ?>

                            </td>


                            <!-- =================================
                                 ACCIONES
                            ================================== -->

                            <td
                                class="col-acciones"
                                data-label="Acciones"
                            >

                                <div class="acciones">


                                    <!-- VER -->

                                    <a
                                        href="<?php
                                            echo htmlspecialchars(
                                                sigenmuniUrlRuta(
                                                    'empleados/ver',
                                                    [
                                                        ...$contextoListado,
                                                        'id' => (int)$fila['id']
                                                    ]
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                        ?>"
                                        class="btn-ver"
                                    >
                                        Ver
                                    </a>


                                    <!-- EDITAR -->

                                    <a
                                        href="<?php
                                            echo htmlspecialchars(
                                                sigenmuniUrlRuta(
                                                    'empleados/editar',
                                                    [
                                                        ...$contextoListado,
                                                        'id' => (int)$fila['id']
                                                    ]
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                        ?>"
                                        class="btn-editar"
                                    >
                                        Editar
                                    </a>


                                    <!-- ACTIVAR / INACTIVAR -->

                                    <?php if ((int)$fila['activo'] === 1): ?>

                                        <a
                                            href="<?php
                                                echo htmlspecialchars(
                                                    sigenmuniUrlRuta(
                                                        'empleados/estado',
                                                        [
                                                            ...$contextoListado,
                                                            'id' => (int)$fila['id'],
                                                            'accion' => 'inactivar'
                                                        ]
                                                    ),
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                );
                                            ?>"
                                            class="btn-inactivar"
                                            data-event-click="empleados-1"
                                        >
                                            Inactivar
                                        </a>

                                    <?php else: ?>

                                        <a
                                            href="<?php
                                                echo htmlspecialchars(
                                                    sigenmuniUrlRuta(
                                                        'empleados/estado',
                                                        [
                                                            ...$contextoListado,
                                                            'id' => (int)$fila['id'],
                                                            'accion' => 'activar'
                                                        ]
                                                    ),
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                );
                                            ?>"
                                            class="btn-activar"
                                            data-event-click="empleados-2"
                                        >
                                            Activar
                                        </a>

                                    <?php endif; ?>

                                </div>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>


        <?php else: ?>


            <!-- =================================
                 SIN EMPLEADOS
            ================================== -->

            <div class="sin-registros">

                No se encontraron empleados cargados.

            </div>


        <?php endif; ?>

        <?php if ($paginacion['paginas'] > 1): ?>
            <?php require __DIR__ . '/../../../componentes/paginacion.php'; ?>
        <?php endif; ?>


    </div>

</div>


<script src="<?= htmlspecialchars(sigenmuniUrlAsset('js/empleados/empleados.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
</body>

</html>