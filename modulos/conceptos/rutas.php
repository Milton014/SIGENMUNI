<?php

/*
|--------------------------------------------------------------------------
| RUTAS DEL MÓDULO CONCEPTOS - ETAPA 2
|--------------------------------------------------------------------------
|
| Rutas migradas hasta esta etapa:
|
| GET  conceptos
| GET  conceptos/nuevo
| POST conceptos/nuevo
| GET  conceptos/editar
| POST conceptos/editar
| POST conceptos/estado
| GET  conceptos/valores
| GET  conceptos/valores/nuevo
| POST conceptos/valores/nuevo
| GET  conceptos/valores/editar
| POST conceptos/valores/editar
| POST conceptos/valores/estado
|
| "conceptos.php" se conserva como CLAVE DE PERMISO de rol_modulo_permiso,
| aunque la solicitud ingrese por public/index.php.
|
| Activar/Inactivar usa POST + CSRF cuando la pantalla se abre por Router.
| La entrada antigua se conserva temporalmente como compatibilidad durante
| esta etapa. El LISTADO de valores ya puede abrirse por Router.
| Nuevo Valor ya funciona por Router con GET/POST + CSRF.
| Editar Valor ya funciona por Router con GET/POST + CSRF.
| Estado de Valor usa POST + CSRF por Router; la entrada antigua se conserva
| temporalmente para comprobar compatibilidad antes de retirarla.
|
|--------------------------------------------------------------------------
*/

return function (Router $router, $conexion) {

    $router->get(
        'conceptos',
        function () use ($conexion) {

            /*
            |------------------------------------------------------------------
            | VALIDAR PARÁMETROS DEL LISTADO
            |------------------------------------------------------------------
            */

            foreach (
                [
                    'buscar',
                    'categoria',
                    'activo',
                    'ok'
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


            /*
            |------------------------------------------------------------------
            | CONTROLADOR
            |------------------------------------------------------------------
            */

            require_once __DIR__
                . '/controlador/ConceptoControlador.php';


            $controlador =
                new ConceptoControlador(
                    $conexion
                );


            $controlador->index();
        },
        'conceptos.php'
    );


    /*
    |--------------------------------------------------------------------------
    | NUEVO CONCEPTO - GET Y POST
    |--------------------------------------------------------------------------
    */

    $accionNuevo =
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
                        'categoria',
                        'forma_calculo',
                        'porcentaje',
                        'monto_fijo',
                        'base_calculo',
                        'orden_calculo',
                        'aplica_sac',
                        'visible_recibo',
                        'activo',
                        'descripcion',
                        'fecha_desde',
                        'fecha_hasta',
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
                        header('Content-Type: text/plain; charset=UTF-8');
                        echo 'Los datos del formulario no tienen un formato válido.';
                        return;
                    }
                }
            }

            require_once __DIR__
                . '/controlador/ConceptoControlador.php';

            $controlador =
                new ConceptoControlador(
                    $conexion
                );

            $controlador->nuevo();
        };


    $router->get(
        'conceptos/nuevo',
        $accionNuevo,
        'conceptos.php'
    );


    $router->post(
        'conceptos/nuevo',
        $accionNuevo,
        'conceptos.php'
    );

    /*
    |--------------------------------------------------------------------------
    | EDITAR CONCEPTO - GET Y POST
    |--------------------------------------------------------------------------
    */

    $accionEditar =
        function () use ($conexion) {

            foreach (
                [
                    'id',
                    'ok',
                    'valor_ok',
                    'ok_valor'
                ]
                as $campo
            ) {
                if (
                    isset($_GET[$campo])
                    &&
                    !is_string($_GET[$campo])
                ) {
                    http_response_code(400);
                    header('Content-Type: text/plain; charset=UTF-8');
                    echo 'Los parámetros de edición no tienen un formato válido.';
                    return;
                }
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
                        'categoria',
                        'forma_calculo',
                        'porcentaje',
                        'monto_fijo',
                        'base_calculo',
                        'orden_calculo',
                        'asignable_empleado',
                        'aplica_sac',
                        'visible_recibo',
                        'activo',
                        'descripcion',
                        'fecha_desde',
                        'fecha_hasta',
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
                        header('Content-Type: text/plain; charset=UTF-8');
                        echo 'Los datos del formulario no tienen un formato válido.';
                        return;
                    }
                }
            }

            require_once __DIR__
                . '/controlador/ConceptoControlador.php';

            $controlador =
                new ConceptoControlador(
                    $conexion
                );

            $controlador->editar();
        };


    $router->get(
        'conceptos/editar',
        $accionEditar,
        'conceptos.php'
    );


    $router->post(
        'conceptos/editar',
        $accionEditar,
        'conceptos.php'
    );


    /*
    |--------------------------------------------------------------------------
    | ESTADO DEL CONCEPTO - SOLO POST EN ROUTER
    |--------------------------------------------------------------------------
    */

    $router->post(
        'conceptos/estado',
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
                    header('Content-Type: text/plain; charset=UTF-8');
                    echo 'Los datos para cambiar el estado no tienen un formato válido.';
                    return;
                }
            }

            require_once __DIR__
                . '/controlador/ConceptoControlador.php';

            $controlador =
                new ConceptoControlador(
                    $conexion
                );

            $controlador->estado();
        },
        'conceptos.php'
    );


    /*
    |--------------------------------------------------------------------------
    | LISTADO DE VALORES DE CONCEPTOS - GET
    |--------------------------------------------------------------------------
    |
    | Puede mostrar:
    | - todos los valores;
    | - los valores de un concepto mediante concepto_id.
    |
    | Las acciones Nuevo/Editar/Estado de valores todavía permanecen en las
    | entradas antiguas durante esta etapa de transición.
    |
    |--------------------------------------------------------------------------
    */

    $router->get(
        'conceptos/valores',
        function () use ($conexion) {

            foreach (
                [
                    'concepto_id',
                    'ok'
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

                    echo 'Los parámetros de valores no tienen un formato válido.';

                    return;
                }
            }

            require_once __DIR__
                . '/controlador/ConceptoControlador.php';

            $controlador =
                new ConceptoControlador(
                    $conexion
                );

            $controlador->valores();
        },
        'conceptos.php'
    );


    /*
    |--------------------------------------------------------------------------
    | NUEVO VALOR DE CONCEPTO - GET Y POST
    |--------------------------------------------------------------------------
    |
    | Admite dos recorridos:
    |
    | - general: desde el listado de valores;
    | - integrado: desde Editar Concepto con concepto_id + origen=concepto.
    |
    |--------------------------------------------------------------------------
    */

    $accionNuevoValor =
        function () use ($conexion) {

            foreach (
                [
                    'concepto_id',
                    'origen'
                ]
                as $campo
            ) {
                if (
                    isset($_GET[$campo])
                    &&
                    !is_string($_GET[$campo])
                ) {
                    http_response_code(400);
                    header('Content-Type: text/plain; charset=UTF-8');
                    echo 'Los parámetros del nuevo valor no tienen un formato válido.';
                    return;
                }
            }


            if (
                ($_SERVER['REQUEST_METHOD'] ?? 'GET')
                ===
                'POST'
            ) {
                foreach (
                    [
                        'concepto_id',
                        'categoria_id',
                        'escalafon_id',
                        'monto',
                        'porcentaje',
                        'fecha_desde',
                        'fecha_hasta',
                        'origen',
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
                        header('Content-Type: text/plain; charset=UTF-8');
                        echo 'Los datos del nuevo valor no tienen un formato válido.';
                        return;
                    }
                }
            }


            require_once __DIR__
                . '/controlador/ConceptoControlador.php';


            $controlador =
                new ConceptoControlador(
                    $conexion
                );


            $controlador->nuevoValor();
        };


    $router->get(
        'conceptos/valores/nuevo',
        $accionNuevoValor,
        'conceptos.php'
    );


    $router->post(
        'conceptos/valores/nuevo',
        $accionNuevoValor,
        'conceptos.php'
    );


    /*
    |--------------------------------------------------------------------------
    | EDITAR VALOR DE CONCEPTO - GET Y POST
    |--------------------------------------------------------------------------
    |
    | Admite:
    | - edición desde el listado general de valores;
    | - edición integrada desde Editar Concepto mediante origen=concepto.
    |
    |--------------------------------------------------------------------------
    */

    $accionEditarValor =
        function () use ($conexion) {

            foreach (
                [
                    'id',
                    'origen',
                    'concepto_id'
                ]
                as $campo
            ) {
                if (
                    isset($_GET[$campo])
                    &&
                    !is_string($_GET[$campo])
                ) {
                    http_response_code(400);
                    header('Content-Type: text/plain; charset=UTF-8');
                    echo 'Los parámetros de edición del valor no tienen un formato válido.';
                    return;
                }
            }


            if (
                ($_SERVER['REQUEST_METHOD'] ?? 'GET')
                ===
                'POST'
            ) {
                foreach (
                    [
                        'concepto_id',
                        'categoria_id',
                        'escalafon_id',
                        'monto',
                        'porcentaje',
                        'fecha_desde',
                        'fecha_hasta',
                        'origen',
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
                        header('Content-Type: text/plain; charset=UTF-8');
                        echo 'Los datos de edición del valor no tienen un formato válido.';
                        return;
                    }
                }
            }


            require_once __DIR__
                . '/controlador/ConceptoControlador.php';


            $controlador =
                new ConceptoControlador(
                    $conexion
                );


            $controlador->editarValor();
        };


    $router->get(
        'conceptos/valores/editar',
        $accionEditarValor,
        'conceptos.php'
    );


    $router->post(
        'conceptos/valores/editar',
        $accionEditarValor,
        'conceptos.php'
    );


    /*
    |--------------------------------------------------------------------------
    | ESTADO DE VALOR DE CONCEPTO - SOLO POST EN ROUTER
    |--------------------------------------------------------------------------
    |
    | El controlador determina el nuevo estado a partir del estado actual.
    | origen=concepto conserva el regreso a la pantalla integrada.
    |
    |--------------------------------------------------------------------------
    */

    $router->post(
        'conceptos/valores/estado',
        function () use ($conexion) {

            foreach (
                [
                    'id',
                    'origen',
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
                    header('Content-Type: text/plain; charset=UTF-8');
                    echo 'Los datos para cambiar el estado del valor no tienen un formato válido.';
                    return;
                }
            }


            require_once __DIR__
                . '/controlador/ConceptoControlador.php';


            $controlador =
                new ConceptoControlador(
                    $conexion
                );


            $controlador->estadoValor();
        },
        'conceptos.php'
    );


};
