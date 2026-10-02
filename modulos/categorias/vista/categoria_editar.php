<?php

/*
|--------------------------------------------------------------------------
| RUTAS Y PROTECCIÓN CSRF
|--------------------------------------------------------------------------
|
| Esta vista trabaja exclusivamente mediante el Router.
|
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../../../core/Url.php';
require_once __DIR__ . '/../../../core/Csrf.php';

$categoriaEditarId =
    (int)(
        $categoria['id']
        ?? 0
    );

$categoriaEditarAccion =
    sigenmuniUrlRuta(
        'categorias/editar',
        [
            'id' => $categoriaEditarId
        ]
    );

$categoriaEditarCancelar =
    sigenmuniUrlRuta(
        'categorias'
    );

$categoriaEditarCsrf =
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
    Editar Categoría - SIGENMUNI
</title>


<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/categorias/categoria_editar.css'), ENT_QUOTES, 'UTF-8') ?>">

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
        Edición de Categoría Municipal
    </p>

</div>


<!-- =========================================
     CONTENIDO
========================================= -->

<div class="contenedor">

    <div class="panel">

        <h2>
            Editar Categoría
        </h2>

        <p class="descripcion">
            Modifique los datos generales de la categoría.
        </p>


        <!-- =====================================
             MENSAJE JAVASCRIPT
        ====================================== -->

        <div
            id="alertaCategoria"
            class="mensaje mensaje-error"
            data-estilo="categoria_editar-1"
        >
        </div>


        <!-- =====================================
             MENSAJE DEL CONTROLADOR
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


        <?php if (!empty($categoria)): ?>


            <!-- =====================================
                 RESUMEN DE ASOCIACIONES
            ====================================== -->

            <div class="resumen">

                <div class="resumen-card">

                    <span class="titulo">
                        Empleados asociados
                    </span>

                    <span class="numero">
                        <?php
                        echo (int)(
                            $empleadosAsociados
                            ?? 0
                        );
                        ?>
                    </span>

                </div>


                <div class="resumen-card">

                    <span class="titulo">
                        Empleados activos
                    </span>

                    <span class="numero">
                        <?php
                        echo (int)(
                            $empleadosActivosAsociados
                            ?? 0
                        );
                        ?>
                    </span>

                </div>


                <div class="resumen-card">

                    <span class="titulo">
                        Valores asociados
                    </span>

                    <span class="numero">
                        <?php
                        echo (int)(
                            $valoresAsociados
                            ?? 0
                        );
                        ?>
                    </span>

                </div>

            </div>


            <!-- =====================================
                 AVISO
            ====================================== -->

            <div class="aviso">

                Los importes de Sueldo Básico, Dedicación Funcional
                y Suplemento Especial se administran desde los valores
                de los conceptos 101, 102 y 104. Esta pantalla modifica
                solamente el código, nombre y estado de la categoría.

            </div>


            <!-- =====================================
                 FORMULARIO
            ====================================== -->

            <form
                action="<?php
                    echo htmlspecialchars(
                        $categoriaEditarAccion,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                method="POST"
                id="formCategoria"
                novalidate
            >

                <input
                    type="hidden"
                    name="_csrf"
                    value="<?php
                        echo htmlspecialchars(
                            $categoriaEditarCsrf,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                >


                <!-- =================================
                     CÓDIGO
                ================================== -->

                <div class="grupo">

                    <label for="codigo">
                        Código *
                    </label>

                    <input
                        type="number"
                        name="codigo"
                        id="codigo"
                        min="1"
                        step="1"
                        autocomplete="off"
                        value="<?php
                            echo htmlspecialchars(
                                (string)(
                                    $categoria['codigo']
                                    ?? ''
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                    >

                    <div class="ayuda">
                        El código debe ser numérico y único.
                        Las categorías tradicionales pueden conservar
                        los códigos 1 a 24 y los cargos especiales
                        pueden utilizar 1001 en adelante.
                    </div>

                </div>


                <!-- =================================
                     NOMBRE
                ================================== -->

                <div class="grupo">

                    <label for="nombre">
                        Nombre *
                    </label>

                    <input
                        type="text"
                        name="nombre"
                        id="nombre"
                        maxlength="150"
                        autocomplete="off"
                        value="<?php
                            echo htmlspecialchars(
                                $categoria['nombre']
                                ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                    >

                    <div class="ayuda">
                        El nombre de la categoría tampoco puede repetirse.
                    </div>

                </div>


                <!-- =================================
                     ESTADO
                ================================== -->

                <div class="grupo">

                    <div class="estado-box">

                        <input
                            type="checkbox"
                            name="activo"
                            id="activo"
                            value="1"
                            <?php
                            echo (
                                (int)(
                                    $categoria['activo']
                                    ?? 0
                                )
                                ===
                                1
                            )
                                ?
                                'checked'
                                :
                                '';
                            ?>
                        >

                        <label for="activo">
                            Categoría activa
                        </label>

                    </div>

                    <div class="ayuda">

                        Si la categoría se inactiva, dejará de estar disponible
                        para nuevas selecciones. Los empleados y registros
                        históricos asociados no se eliminan.

                    </div>

                </div>


                <!-- =================================
                     BOTONES
                ================================== -->

                <div class="acciones">

                    <button
                        type="submit"
                        class="btn btn-guardar"
                    >
                        Actualizar Categoría
                    </button>


                    <a
                        href="<?php
                        echo htmlspecialchars(
                            $categoriaEditarCancelar,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                        class="btn btn-cancelar"
                    >
                        Cancelar
                    </a>

                </div>

            </form>


        <?php else: ?>

            <div class="mensaje mensaje-error">
                No se pudo cargar la categoría solicitada.
            </div>


            <div class="acciones">

                <a
                    href="<?php
                    echo htmlspecialchars(
                        $categoriaEditarCancelar,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                    class="btn btn-cancelar"
                >
                    Volver a Categorías
                </a>

            </div>

        <?php endif; ?>

    </div>

</div>


<script src="<?= htmlspecialchars(sigenmuniUrlAsset('js/categorias/categoria_editar.js'), ENT_QUOTES, 'UTF-8') ?>"></script>


</body>

</html>