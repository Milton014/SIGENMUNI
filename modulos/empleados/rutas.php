<?php

/*
|--------------------------------------------------------------------------
| RUTAS DEL MÓDULO EMPLEADOS
|--------------------------------------------------------------------------
|
| Rutas principales de Gestión de Empleados:
|
| GET  empleados
| GET  empleados/nuevo
| POST empleados/nuevo
| GET  empleados/ver
| GET  empleados/editar
| POST empleados/editar
| GET  empleados/estado
| POST empleados/estado
|
| GET  empleado-conceptos
| GET  empleado-conceptos/nuevo
| POST empleado-conceptos/nuevo
| GET  empleado-conceptos/ver
| GET  empleado-conceptos/editar
| POST empleado-conceptos/editar
| POST empleado-conceptos/estado
|
| "empleados.php" se conserva únicamente como CLAVE DE PERMISO
| de rol_modulo_permiso. La navegación real utiliza public/index.php.
|
|--------------------------------------------------------------------------
*/

return function (Router $router, $conexion) {

    $router->get(
        'empleados',
        function () use ($conexion) {

            /*
            |------------------------------------------------------------------
            | VALIDAR PARÁMETROS DEL LISTADO
            |------------------------------------------------------------------
            */

            foreach (
                [
                    'busqueda',
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
                . '/controlador/EmpleadoControlador.php';


            $controlador =
                new EmpleadoControlador(
                    $conexion
                );


            $controlador->index();
        },
        'empleados.php'
    );

    /*
    |--------------------------------------------------------------------------
    | NUEVO EMPLEADO - GET Y POST
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
                        'institucion_id',
                        'oficina_id',
                        'situacion_id',
                        'escalafon_id',
                        'categoria_id',
                        'nro_legajo',
                        'apellido',
                        'nombre',
                        'dni',
                        'cuil',
                        'fecha_alta',
                        'telefono',
                        'email',
                        'domicilio',
                        'observaciones',
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
                . '/controlador/EmpleadoControlador.php';


            $controlador =
                new EmpleadoControlador(
                    $conexion
                );


            $controlador->nuevo();
        };


    $router->get(
        'empleados/nuevo',
        $accionNuevo,
        'empleados.php'
    );


    $router->post(
        'empleados/nuevo',
        $accionNuevo,
        'empleados.php'
    );


    /*
    |--------------------------------------------------------------------------
    | VER EMPLEADO
    |--------------------------------------------------------------------------
    */

    $router->get(
        'empleados/ver',
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

                echo 'El identificador del empleado no tiene un formato válido.';

                return;
            }


            require_once __DIR__
                . '/controlador/EmpleadoControlador.php';


            $controlador =
                new EmpleadoControlador(
                    $conexion
                );


            $controlador->ver();
        },
        'empleados.php'
    );


    /*
    |--------------------------------------------------------------------------
    | EDITAR EMPLEADO - GET Y POST
    |--------------------------------------------------------------------------
    */

    $accionEditar =
        function () use ($conexion) {

            /*
            |------------------------------------------------------------------
            | VALIDAR ID
            |------------------------------------------------------------------
            */

            if (
                isset($_GET['id'])
                &&
                !is_string($_GET['id'])
            ) {
                http_response_code(400);

                header(
                    'Content-Type: text/plain; charset=UTF-8'
                );

                echo 'El identificador del empleado no tiene un formato válido.';

                return;
            }


            /*
            |------------------------------------------------------------------
            | VALIDAR DATOS POST
            |------------------------------------------------------------------
            */

            if (
                ($_SERVER['REQUEST_METHOD'] ?? 'GET')
                ===
                'POST'
            ) {
                foreach (
                    [
                        'institucion_id',
                        'oficina_id',
                        'situacion_id',
                        'escalafon_id',
                        'categoria_id',
                        'nro_legajo',
                        'apellido',
                        'nombre',
                        'dni',
                        'cuil',
                        'fecha_baja',
                        'telefono',
                        'email',
                        'domicilio',
                        'observaciones',
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


            /*
            |------------------------------------------------------------------
            | CONTROLADOR
            |------------------------------------------------------------------
            */

            require_once __DIR__
                . '/controlador/EmpleadoControlador.php';


            $controlador =
                new EmpleadoControlador(
                    $conexion
                );


            $controlador->editar();
        };


    $router->get(
        'empleados/editar',
        $accionEditar,
        'empleados.php'
    );


    $router->post(
        'empleados/editar',
        $accionEditar,
        'empleados.php'
    );


    /*
    |--------------------------------------------------------------------------
    | ESTADO EMPLEADO - GET Y POST
    |--------------------------------------------------------------------------
    |
    | GET:
    |   muestra la pantalla de confirmación con historial laboral.
    |
    | POST:
    |   confirma la activación / inactivación con fecha efectiva,
    |   observación y protección CSRF.
    |
    |--------------------------------------------------------------------------
    */

    $accionEstado =
        function () use ($conexion) {

            /*
            |------------------------------------------------------------------
            | VALIDAR PARÁMETROS GET
            |------------------------------------------------------------------
            */

            foreach (
                [
                    'id',
                    'accion'
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

                    echo 'Los parámetros del cambio de estado no tienen un formato válido.';

                    return;
                }
            }


            /*
            |------------------------------------------------------------------
            | VALIDAR DATOS POST
            |------------------------------------------------------------------
            */

            if (
                ($_SERVER['REQUEST_METHOD'] ?? 'GET')
                ===
                'POST'
            ) {
                foreach (
                    [
                        'fecha_efectiva',
                        'observacion',
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
            }


            /*
            |------------------------------------------------------------------
            | CONTROLADOR
            |------------------------------------------------------------------
            */

            require_once __DIR__
                . '/controlador/EmpleadoControlador.php';


            $controlador =
                new EmpleadoControlador(
                    $conexion
                );


            $controlador->estado();
        };


    $router->get(
        'empleados/estado',
        $accionEstado,
        'empleados.php'
    );


    $router->post(
        'empleados/estado',
        $accionEstado,
        'empleados.php'
    );


    /*
    |--------------------------------------------------------------------------
    | CONCEPTOS POR EMPLEADO - LISTADO
    |--------------------------------------------------------------------------
    |
    | El submódulo Conceptos por Empleado utiliza exclusivamente
    | Front Controller + Router para navegación y operaciones.
    |
    | "empleado_conceptos.php" se conserva únicamente como CLAVE DE PERMISO.
    |
    |--------------------------------------------------------------------------
    */

    $router->get(
        'empleado-conceptos',
        function () use ($conexion) {

            /*
            |------------------------------------------------------------------
            | VALIDAR PARÁMETROS DEL LISTADO
            |------------------------------------------------------------------
            */

            foreach (
                [
                    'buscar_empleado',
                    'concepto_id',
                    'estado',
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

                    echo 'Los filtros del listado no tienen un formato válido.';

                    return;
                }
            }


            /*
            |------------------------------------------------------------------
            | CONTROLADOR
            |------------------------------------------------------------------
            */

            require_once __DIR__
                . '/controlador/EmpleadoControlador.php';


            $controlador =
                new EmpleadoControlador(
                    $conexion
                );


            $controlador->conceptos();
        },
        'empleado_conceptos.php'
    );


    /*
    |--------------------------------------------------------------------------
    | CONCEPTOS POR EMPLEADO - NUEVA ASIGNACIÓN
    |--------------------------------------------------------------------------
    */

    $accionNuevoConceptoEmpleado =
        function () use ($conexion) {

            /*
            |------------------------------------------------------------------
            | VALIDAR DATOS POST
            |------------------------------------------------------------------
            */

            if (
                ($_SERVER['REQUEST_METHOD'] ?? 'GET')
                ===
                'POST'
            ) {
                foreach (
                    [
                        'empleado_id',
                        'concepto_id',
                        'monto_manual',
                        'porcentaje_manual',
                        'cantidad',
                        'fecha_desde',
                        'fecha_hasta',
                        'observacion',
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


            /*
            |------------------------------------------------------------------
            | CONTROLADOR
            |------------------------------------------------------------------
            */

            require_once __DIR__
                . '/controlador/EmpleadoControlador.php';


            $controlador =
                new EmpleadoControlador(
                    $conexion
                );


            $controlador->nuevoConcepto();
        };


    $router->get(
        'empleado-conceptos/nuevo',
        $accionNuevoConceptoEmpleado,
        'empleado_conceptos.php'
    );


    $router->post(
        'empleado-conceptos/nuevo',
        $accionNuevoConceptoEmpleado,
        'empleado_conceptos.php'
    );


    /*
    |--------------------------------------------------------------------------
    | CONCEPTOS POR EMPLEADO - VER ASIGNACIÓN
    |--------------------------------------------------------------------------
    */

    $router->get(
        'empleado-conceptos/ver',
        function () use ($conexion) {

            /*
            |------------------------------------------------------------------
            | VALIDAR ID
            |------------------------------------------------------------------
            */

            if (
                isset($_GET['id'])
                &&
                !is_string($_GET['id'])
            ) {
                http_response_code(400);

                header(
                    'Content-Type: text/plain; charset=UTF-8'
                );

                echo 'El identificador de la asignación no tiene un formato válido.';

                return;
            }


            /*
            |------------------------------------------------------------------
            | CONTROLADOR
            |------------------------------------------------------------------
            */

            require_once __DIR__
                . '/controlador/EmpleadoControlador.php';


            $controlador =
                new EmpleadoControlador(
                    $conexion
                );


            $controlador->verConcepto();
        },
        'empleado_conceptos.php'
    );


    /*
    |--------------------------------------------------------------------------
    | CONCEPTOS POR EMPLEADO - EDITAR ASIGNACIÓN
    |--------------------------------------------------------------------------
    */

    $accionEditarConceptoEmpleado =
        function () use ($conexion) {

            /*
            |------------------------------------------------------------------
            | VALIDAR ID
            |------------------------------------------------------------------
            */

            if (
                isset($_GET['id'])
                &&
                !is_string($_GET['id'])
            ) {
                http_response_code(400);

                header(
                    'Content-Type: text/plain; charset=UTF-8'
                );

                echo 'El identificador de la asignación no tiene un formato válido.';

                return;
            }


            /*
            |------------------------------------------------------------------
            | VALIDAR DATOS POST
            |------------------------------------------------------------------
            */

            if (
                ($_SERVER['REQUEST_METHOD'] ?? 'GET')
                ===
                'POST'
            ) {
                foreach (
                    [
                        'empleado_id',
                        'concepto_id',
                        'monto_manual',
                        'porcentaje_manual',
                        'cantidad',
                        'fecha_desde',
                        'fecha_hasta',
                        'observacion',
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


            /*
            |------------------------------------------------------------------
            | CONTROLADOR
            |------------------------------------------------------------------
            */

            require_once __DIR__
                . '/controlador/EmpleadoControlador.php';


            $controlador =
                new EmpleadoControlador(
                    $conexion
                );


            $controlador->editarConcepto();
        };


    $router->get(
        'empleado-conceptos/editar',
        $accionEditarConceptoEmpleado,
        'empleado_conceptos.php'
    );


    $router->post(
        'empleado-conceptos/editar',
        $accionEditarConceptoEmpleado,
        'empleado_conceptos.php'
    );


    /*
    |--------------------------------------------------------------------------
    | CONCEPTOS POR EMPLEADO - ACTIVAR / DESACTIVAR
    |--------------------------------------------------------------------------
    |
    | El cambio de estado es una operación de escritura.
    | Por eso se permite únicamente mediante POST + CSRF.
    |
    |--------------------------------------------------------------------------
    */

    $router->post(
        'empleado-conceptos/estado',
        function () use ($conexion) {

            /*
            |------------------------------------------------------------------
            | VALIDAR FORMATO DE DATOS
            |------------------------------------------------------------------
            */

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


            /*
            |------------------------------------------------------------------
            | CONTROLADOR
            |------------------------------------------------------------------
            */

            require_once __DIR__
                . '/controlador/EmpleadoControlador.php';


            $controlador =
                new EmpleadoControlador(
                    $conexion
                );


            $controlador->estadoConcepto();
        },
        'empleado_conceptos.php'
    );

};
