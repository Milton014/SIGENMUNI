<?php

/*
|--------------------------------------------------------------------------
| RUTAS DEL MÓDULO CATEGORÍAS
|--------------------------------------------------------------------------
|
| Durante la migración se conserva "categorias.php" como CLAVE DE PERMISO
| de rol_modulo_permiso, aunque las solicitudes ingresen por public/index.php.
|
| Rutas migradas hasta esta etapa:
|
| GET  categorias
| GET  categorias/nueva
| POST categorias/nueva
| GET  categorias/editar
| POST categorias/editar
| POST categorias/estado
|
|--------------------------------------------------------------------------
*/

return function (Router $router, $conexion) {

    /*
    |--------------------------------------------------------------------------
    | LISTADO
    |--------------------------------------------------------------------------
    */

    $router->get(
        'categorias',
        function () use ($conexion) {

            foreach (
                [
                    'buscar',
                    'activo',
                    'ok',
                    'error'
                ]
                as $campo
            ) {
                if (
                    isset($_GET[$campo])
                    &&
                    !is_string($_GET[$campo])
                ) {
                    http_response_code(400);

                    header(
                        'Content-Type: text/plain; charset=UTF-8'
                    );

                    echo 'Los filtros y mensajes deben enviarse como texto.';

                    return;
                }
            }

            require_once __DIR__
                . '/controlador/CategoriaControlador.php';

            $controlador =
                new CategoriaControlador(
                    $conexion
                );

            $controlador->index();
        },
        'categorias.php'
    );


    /*
    |--------------------------------------------------------------------------
    | NUEVA CATEGORÍA - GET Y POST
    |--------------------------------------------------------------------------
    |
    | El mismo método nuevo() del controlador muestra el formulario con GET
    | y procesa el alta con POST. No se duplica la lógica del caso de uso.
    |
    |--------------------------------------------------------------------------
    */

    $accionNueva =
        function () use ($conexion) {

            if (
                ($_SERVER['REQUEST_METHOD'] ?? 'GET')
                ===
                'POST'
            ) {
                foreach (
                    [
                        'codigo',
                        'nombre',
                        'activo',
                        '_csrf'
                    ]
                    as $campo
                ) {
                    if (
                        isset($_POST[$campo])
                        &&
                        !is_string($_POST[$campo])
                    ) {
                        http_response_code(400);

                        header(
                            'Content-Type: text/plain; charset=UTF-8'
                        );

                        echo 'Los datos del formulario no tienen un formato válido.';

                        return;
                    }
                }
            }

            require_once __DIR__
                . '/controlador/CategoriaControlador.php';

            $controlador =
                new CategoriaControlador(
                    $conexion
                );

            $controlador->nuevo();
        };


    $router->get(
        'categorias/nueva',
        $accionNueva,
        'categorias.php'
    );


    $router->post(
        'categorias/nueva',
        $accionNueva,
        'categorias.php'
    );


    /*
    |--------------------------------------------------------------------------
    | EDITAR CATEGORÍA - GET Y POST
    |--------------------------------------------------------------------------
    |
    | El identificador se conserva en la consulta (?id=...). editar() muestra
    | el formulario con GET y procesa la actualización con POST.
    |
    |--------------------------------------------------------------------------
    */

    $accionEditar =
        function () use ($conexion) {

            if (
                isset($_GET['id'])
                &&
                !is_string($_GET['id'])
            ) {
                http_response_code(400);

                header(
                    'Content-Type: text/plain; charset=UTF-8'
                );

                echo 'El identificador de la categoría no tiene un formato válido.';

                return;
            }

            if (
                ($_SERVER['REQUEST_METHOD'] ?? 'GET')
                ===
                'POST'
            ) {
                foreach (
                    [
                        'codigo',
                        'nombre',
                        'activo',
                        '_csrf'
                    ]
                    as $campo
                ) {
                    if (
                        isset($_POST[$campo])
                        &&
                        !is_string($_POST[$campo])
                    ) {
                        http_response_code(400);

                        header(
                            'Content-Type: text/plain; charset=UTF-8'
                        );

                        echo 'Los datos del formulario no tienen un formato válido.';

                        return;
                    }
                }
            }

            require_once __DIR__
                . '/controlador/CategoriaControlador.php';

            $controlador =
                new CategoriaControlador(
                    $conexion
                );

            $controlador->editar();
        };


    $router->get(
        'categorias/editar',
        $accionEditar,
        'categorias.php'
    );


    $router->post(
        'categorias/editar',
        $accionEditar,
        'categorias.php'
    );


    /*
    |--------------------------------------------------------------------------
    | ACTIVAR / INACTIVAR CATEGORÍA - POST
    |--------------------------------------------------------------------------
    |
    | Esta operación modifica datos, por lo tanto se expone únicamente por POST.
    | El controlador valida además el token CSRF antes de cambiar el estado.
    |
    |--------------------------------------------------------------------------
    */

    $router->post(
        'categorias/estado',
        function () use ($conexion) {

            foreach (
                [
                    'id',
                    'estado',
                    '_csrf'
                ]
                as $campo
            ) {
                if (
                    isset($_POST[$campo])
                    &&
                    !is_string($_POST[$campo])
                ) {
                    http_response_code(400);

                    header(
                        'Content-Type: text/plain; charset=UTF-8'
                    );

                    echo 'Los datos del cambio de estado no tienen un formato válido.';

                    return;
                }
            }

            require_once __DIR__
                . '/controlador/CategoriaControlador.php';

            $controlador =
                new CategoriaControlador(
                    $conexion
                );

            $controlador->estado();
        },
        'categorias.php'
    );

};
