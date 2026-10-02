<?php

/*
|--------------------------------------------------------------------------
| VISTA - GESTIÓN DE CATEGORÍAS
|--------------------------------------------------------------------------
|
| Esta vista se ejecuta mediante la entrada única public/index.php
| y las rutas registradas en modulos/categorias/rutas.php.
|
| Las operaciones que modifican datos utilizan POST + CSRF.
|
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../../../core/Url.php';
require_once __DIR__ . '/../../../core/Csrf.php';


$categoriasUrlListado =
    sigenmuniUrlRuta(
        'categorias'
    );


$categoriasAccionBuscar =
    sigenmuniUrlEntrada();


$categoriasUrlNueva =
    sigenmuniUrlRuta(
        'categorias/nueva'
    );


$categoriasUrlEstado =
    sigenmuniUrlRuta(
        'categorias/estado'
    );


$categoriasCsrfToken =
    sigenmuniCsrfToken();

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>
    Gestión de Categorías - SIGENMUNI
</title>


<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/categorias/categorias.css'), ENT_QUOTES, 'UTF-8') ?>">

</head>


<body>


<!-- =========================================
     HEADER GLOBAL
========================================= -->

<?php

$sigenmuniHeaderSubtitulo =
    'Gestión de Categorías Municipales';

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
                Gestión de Categorías
            </h2>


            <div class="acciones-superiores">

                <a
                    href="<?php echo htmlspecialchars($categoriasUrlNueva, ENT_QUOTES, 'UTF-8'); ?>"
                    class="btn btn-nuevo"
                >
                    + Nueva Categoría
                </a>


                <a
                    href="<?php echo htmlspecialchars(sigenmuniUrlRuta('inicio'), ENT_QUOTES, 'UTF-8'); ?>"
                    class="btn btn-volver"
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
             FILTROS
        ====================================== -->

        <form
            method="GET"
            action="<?php echo htmlspecialchars($categoriasAccionBuscar, ENT_QUOTES, 'UTF-8'); ?>"
            class="filtros"
        >

            <input
                type="hidden"
                name="r"
                value="categorias"
            >

            <input
                type="text"
                name="buscar"
                placeholder="Buscar por código o nombre"
                value="<?php
                    echo htmlspecialchars(
                        $buscar ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
            >


            <select name="activo">

                <option value="">
                    -- Todos los estados --
                </option>

                <option
                    value="1"
                    <?php
                    echo (
                        ($activo ?? '')
                        ===
                        '1'
                    )
                        ?
                        'selected'
                        :
                        '';
                    ?>
                >
                    Activas
                </option>

                <option
                    value="0"
                    <?php
                    echo (
                        ($activo ?? '')
                        ===
                        '0'
                    )
                        ?
                        'selected'
                        :
                        '';
                    ?>
                >
                    Inactivas
                </option>

            </select>


            <button
                type="submit"
                class="btn btn-buscar"
            >
                Buscar
            </button>


            <a
                href="<?php echo htmlspecialchars($categoriasUrlListado, ENT_QUOTES, 'UTF-8'); ?>"
                class="btn btn-limpiar"
            >
                Limpiar
            </a>

        </form>


        <!-- =====================================
             RESUMEN
        ====================================== -->

        <div class="resumen">

            <div class="total">

                Total de categorías encontradas:
                <?php
                echo count(
                    $categorias
                    ?? []
                );
                ?>

            </div>

        </div>


        <!-- =====================================
             LISTADO
        ====================================== -->

        <?php if (!empty($categorias)): ?>

            <div class="tabla-contenedor">

                <table>

                    <thead>

                        <tr>

                            <th>
                                Código
                            </th>

                            <th>
                                Nombre
                            </th>

                            <th>
                                Sueldo Básico
                            </th>

                            <th>
                                Dedicación Funcional
                            </th>

                            <th>
                                Suplemento Especial
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

                        <?php foreach ($categorias as $categoriaFila): ?>

                            <tr>

                                <td
                                    class="col-codigo"
                                    data-label="Código"
                                >

                                    <?php
                                    echo htmlspecialchars(
                                        (string)$categoriaFila['codigo'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>

                                </td>


                                <td
                                    class="col-nombre"
                                    data-label="Nombre"
                                >

                                    <?php
                                    echo htmlspecialchars(
                                        $categoriaFila['nombre'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>

                                </td>


                                <td
                                    class="col-monto"
                                    data-label="Sueldo Básico"
                                >

                                    <?php if (
                                        $categoriaFila['sueldo_basico_actual']
                                        !==
                                        null
                                    ): ?>

                                        $ <?php
                                        echo number_format(
                                            (float)$categoriaFila['sueldo_basico_actual'],
                                            2,
                                            ',',
                                            '.'
                                        );
                                        ?>

                                    <?php else: ?>

                                        <span class="sin-valor">
                                            Sin valor
                                        </span>

                                    <?php endif; ?>

                                </td>


                                <td
                                    class="col-monto"
                                    data-label="Dedicación Funcional"
                                >

                                    <?php if (
                                        $categoriaFila['dedicacion_funcional_actual']
                                        !==
                                        null
                                    ): ?>

                                        $ <?php
                                        echo number_format(
                                            (float)$categoriaFila['dedicacion_funcional_actual'],
                                            2,
                                            ',',
                                            '.'
                                        );
                                        ?>

                                    <?php else: ?>

                                        <span class="sin-valor">
                                            Sin valor
                                        </span>

                                    <?php endif; ?>

                                </td>


                                <td
                                    class="col-monto"
                                    data-label="Suplemento Especial"
                                >

                                    <?php if (
                                        $categoriaFila['suplemento_especial_actual']
                                        !==
                                        null
                                    ): ?>

                                        $ <?php
                                        echo number_format(
                                            (float)$categoriaFila['suplemento_especial_actual'],
                                            2,
                                            ',',
                                            '.'
                                        );
                                        ?>

                                    <?php else: ?>

                                        <span class="sin-valor">
                                            Sin valor
                                        </span>

                                    <?php endif; ?>

                                </td>


                                <td
                                    class="col-estado"
                                    data-label="Estado"
                                >

                                    <?php if (
                                        (int)$categoriaFila['activo']
                                        ===
                                        1
                                    ): ?>

                                        <span class="estado estado-activo">
                                            Activa
                                        </span>

                                    <?php else: ?>

                                        <span class="estado estado-inactivo">
                                            Inactiva
                                        </span>

                                    <?php endif; ?>

                                </td>


                                <td
                                    class="col-acciones"
                                    data-label="Acciones"
                                >

                                    <div class="acciones">

                                        <a
                                            href="<?php
                                                echo htmlspecialchars(
                                                    sigenmuniUrlRuta(
                                                        'categorias/editar',
                                                        [
                                                            'id' =>
                                                                (int)$categoriaFila['id']
                                                        ]
                                                    ),
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                );
                                            ?>"
                                            class="btn-accion btn-editar"
                                        >
                                            Editar
                                        </a>


                                        <?php if (
                                            (int)$categoriaFila['activo']
                                            ===
                                            1
                                        ): ?>

                                            <form
                                                method="POST"
                                                action="<?php
                                                    echo htmlspecialchars(
                                                        $categoriasUrlEstado,
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    );
                                                ?>"
                                                class="form-accion"
                                                data-event-submit="categorias-1"
                                            >

                                                <input
                                                    type="hidden"
                                                    name="id"
                                                    value="<?php echo (int)$categoriaFila['id']; ?>"
                                                >

                                                <input
                                                    type="hidden"
                                                    name="estado"
                                                    value="0"
                                                >

                                                <input
                                                    type="hidden"
                                                    name="_csrf"
                                                    value="<?php
                                                        echo htmlspecialchars(
                                                            $categoriasCsrfToken,
                                                            ENT_QUOTES,
                                                            'UTF-8'
                                                        );
                                                    ?>"
                                                >

                                                <button
                                                    type="submit"
                                                    class="btn-accion btn-inactivar"
                                                >
                                                    Inactivar
                                                </button>

                                            </form>

                                        <?php else: ?>

                                            <form
                                                method="POST"
                                                action="<?php
                                                    echo htmlspecialchars(
                                                        $categoriasUrlEstado,
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    );
                                                ?>"
                                                class="form-accion"
                                                data-event-submit="categorias-2"
                                            >

                                                <input
                                                    type="hidden"
                                                    name="id"
                                                    value="<?php echo (int)$categoriaFila['id']; ?>"
                                                >

                                                <input
                                                    type="hidden"
                                                    name="estado"
                                                    value="1"
                                                >

                                                <input
                                                    type="hidden"
                                                    name="_csrf"
                                                    value="<?php
                                                        echo htmlspecialchars(
                                                            $categoriasCsrfToken,
                                                            ENT_QUOTES,
                                                            'UTF-8'
                                                        );
                                                    ?>"
                                                >

                                                <button
                                                    type="submit"
                                                    class="btn-accion btn-activar"
                                                >
                                                    Activar
                                                </button>

                                            </form>

                                        <?php endif; ?>

                                    </div>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php else: ?>

            <div class="sin-resultados">

                No se encontraron categorías con los filtros seleccionados.

            </div>

        <?php endif; ?>

    </div>

</div>


<script src="<?= htmlspecialchars(sigenmuniUrlAsset('js/categorias/categorias.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
</body>

</html>
