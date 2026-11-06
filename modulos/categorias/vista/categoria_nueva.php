<?php

/*
|--------------------------------------------------------------------------
| URL Y CSRF - SOLO ROUTER
|--------------------------------------------------------------------------
|
| Esta vista ya no mantiene compatibilidad con las entradas antiguas.
| Todas las acciones del formulario utilizan el Front Controller.
|
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../../../core/Url.php';
require_once __DIR__ . '/../../../core/Csrf.php';

$categoriaNuevaAccion =
    sigenmuniUrlRuta('categorias/nueva', $contextoListado);

$categoriaNuevaCancelar =
    sigenmuniUrlRuta('categorias', $contextoListado);

$categoriaNuevaCsrf =
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
    Nueva Categoría - SIGENMUNI
</title>


<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/categorias/categoria_nueva.css'), ENT_QUOTES, 'UTF-8') ?>">

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
        Alta de Categoría Municipal
    </p>

</div>


<!-- =========================================
     CONTENIDO
========================================= -->

<div class="contenedor">

    <div class="panel">

        <h2>
            Nueva Categoría
        </h2>

        <p class="descripcion">
            Registre una nueva categoría o cargo municipal.
        </p>


        <!-- =====================================
             MENSAJE JAVASCRIPT
        ====================================== -->

        <div
            id="alertaCategoria"
            class="mensaje mensaje-error"
            data-estilo="categoria_nueva-1"
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


        <!-- =====================================
             AVISO
        ====================================== -->

        <div class="aviso">

            Los importes de Sueldo Básico, Dedicación Funcional
            y Suplemento Especial no se cargan en esta pantalla.
            Luego podrá asignarlos desde los valores de los conceptos
            101, 102 y 104.

        </div>


        <!-- =====================================
             FORMULARIO
        ====================================== -->

        <form
            action="<?php
                echo htmlspecialchars(
                    $categoriaNuevaAccion,
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
                        $categoriaNuevaCsrf,
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
                    placeholder="Ejemplo: 1001"
                    value="<?php
                        echo htmlspecialchars(
                            (string)(
                                $datos['codigo']
                                ?? ''
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                >

                <div class="ayuda">
                    El código debe ser numérico y no puede repetirse.
                    Puede utilizar 1 a 24 para categorías tradicionales
                    y 1001 en adelante para cargos especiales.
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
                    placeholder="Ejemplo: Intendente"
                    value="<?php
                        echo htmlspecialchars(
                            $datos['nombre']
                            ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                >

                <div class="ayuda">
                    El nombre también debe ser único.
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
                                $datos['activo']
                                ?? 1
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

            </div>


            <!-- =================================
                 BOTONES
            ================================== -->

            <div class="acciones">

                <button
                    type="submit"
                    class="btn btn-guardar"
                >
                    Guardar Categoría
                </button>


                <a
                    href="<?php
                        echo htmlspecialchars(
                            $categoriaNuevaCancelar,
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

    </div>

</div>


<script src="<?= htmlspecialchars(sigenmuniUrlAsset('js/categorias/categoria_nueva.js'), ENT_QUOTES, 'UTF-8') ?>"></script>


</body>

</html>