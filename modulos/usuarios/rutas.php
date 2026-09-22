<?php

/*
|--------------------------------------------------------------------------
| RUTAS DEL MÓDULO USUARIOS - ETAPA 1
|--------------------------------------------------------------------------
|
| Gestión de Usuarios continúa siendo exclusiva para administradores.
|
| "usuarios.php" se conserva como CLAVE DE PERMISO del sistema aunque
| las solicitudes ingresen mediante public/index.php?r=...
|
| Rutas preparadas:
|
| GET  usuarios
| GET  usuarios/nuevo
| POST usuarios/nuevo
| GET  usuarios/editar
| POST usuarios/editar
| POST usuarios/estado
|
| GET  usuarios/roles
| GET  usuarios/roles/nuevo
| POST usuarios/roles/nuevo
| GET  usuarios/roles/editar
| POST usuarios/roles/editar
| POST usuarios/roles/estado
| GET  usuarios/roles/permisos
| POST usuarios/roles/permisos
|
| El estado de USUARIOS y de ROLES utiliza POST + CSRF.
|
|--------------------------------------------------------------------------
*/

return function (Router $router, $conexion) {

    /*
    |--------------------------------------------------------------------------
    | CREAR CONTROLADOR
    |--------------------------------------------------------------------------
    |
    | Además del permiso funcional, Gestión de Usuarios debe seguir siendo
    | exclusiva para administradores.
    |
    |--------------------------------------------------------------------------
    */

    $crearControlador =
        function () use ($conexion) {

            soloAdmin();

            require_once __DIR__
                . '/controlador/UsuarioControlador.php';


            return new UsuarioControlador(
                $conexion
            );
        };


    /*
    |--------------------------------------------------------------------------
    | LISTADO DE USUARIOS
    |--------------------------------------------------------------------------
    */

    $router->get(
        'usuarios',
        function () use ($crearControlador) {

            foreach (
                [
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

                    echo 'Los parámetros recibidos no tienen un formato válido.';

                    return;
                }
            }


            $controlador =
                $crearControlador();


            $controlador->index();
        },
        'usuarios.php'
    );


    /*
    |--------------------------------------------------------------------------
    | NUEVO USUARIO - GET Y POST
    |--------------------------------------------------------------------------
    */

    $accionNuevoUsuario =
        function () use ($crearControlador) {

            if (
                ($_SERVER['REQUEST_METHOD'] ?? 'GET')
                ===
                'POST'
            ) {

                foreach (
                    [
                        'nombre',
                        'apellido',
                        'dni',
                        'nombre_usuario',
                        'email',
                        'rol_id',
                        'clave',
                        'confirmar_clave'
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

                        echo 'Los datos del usuario no tienen un formato válido.';

                        return;
                    }
                }
            }


            $controlador =
                $crearControlador();


            $controlador->nuevo();
        };


    $router->get(
        'usuarios/nuevo',
        $accionNuevoUsuario,
        'usuarios.php'
    );


    $router->post(
        'usuarios/nuevo',
        $accionNuevoUsuario,
        'usuarios.php'
    );


    /*
    |--------------------------------------------------------------------------
    | EDITAR USUARIO - GET Y POST
    |--------------------------------------------------------------------------
    */

    $accionEditarUsuario =
        function () use ($crearControlador) {

            if (
                isset($_GET['id'])
                &&
                !is_string($_GET['id'])
            ) {

                http_response_code(400);

                header(
                    'Content-Type: text/plain; charset=UTF-8'
                );

                echo 'El identificador del usuario no tiene un formato válido.';

                return;
            }


            if (
                ($_SERVER['REQUEST_METHOD'] ?? 'GET')
                ===
                'POST'
            ) {

                foreach (
                    [
                        'nombre',
                        'apellido',
                        'dni',
                        'nombre_usuario',
                        'email',
                        'rol_id',
                        'clave',
                        'confirmar_clave'
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

                        echo 'Los datos del usuario no tienen un formato válido.';

                        return;
                    }
                }
            }


            $controlador =
                $crearControlador();


            $controlador->editar();
        };


    $router->get(
        'usuarios/editar',
        $accionEditarUsuario,
        'usuarios.php'
    );


    $router->post(
        'usuarios/editar',
        $accionEditarUsuario,
        'usuarios.php'
    );


    /*
    |--------------------------------------------------------------------------
    | CAMBIAR ESTADO DE USUARIO - SOLO POST + CSRF
    |--------------------------------------------------------------------------
    */

    $router->post(
        'usuarios/estado',
        function () use ($crearControlador) {

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

                    echo 'Los datos para cambiar el estado del usuario no tienen un formato válido.';

                    return;
                }
            }


            $controlador =
                $crearControlador();


            $controlador->cambiarEstado();
        },
        'usuarios.php'
    );


    /*
    |--------------------------------------------------------------------------
    | LISTADO DE ROLES
    |--------------------------------------------------------------------------
    */

    $router->get(
        'usuarios/roles',
        function () use ($crearControlador) {

            foreach (
                [
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

                    echo 'Los parámetros recibidos no tienen un formato válido.';

                    return;
                }
            }


            $controlador =
                $crearControlador();


            $controlador->roles();
        },
        'usuarios.php'
    );


    /*
    |--------------------------------------------------------------------------
    | NUEVO ROL - GET Y POST
    |--------------------------------------------------------------------------
    */

    $accionNuevoRol =
        function () use ($crearControlador) {

            if (
                ($_SERVER['REQUEST_METHOD'] ?? 'GET')
                ===
                'POST'
            ) {

                foreach (
                    [
                        'nombre',
                        'descripcion',
                        'es_admin'
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

                        echo 'Los datos del rol no tienen un formato válido.';

                        return;
                    }
                }
            }


            $controlador =
                $crearControlador();


            $controlador->nuevoRol();
        };


    $router->get(
        'usuarios/roles/nuevo',
        $accionNuevoRol,
        'usuarios.php'
    );


    $router->post(
        'usuarios/roles/nuevo',
        $accionNuevoRol,
        'usuarios.php'
    );


    /*
    |--------------------------------------------------------------------------
    | EDITAR ROL - GET Y POST
    |--------------------------------------------------------------------------
    */

    $accionEditarRol =
        function () use ($crearControlador) {

            if (
                isset($_GET['id'])
                &&
                !is_string($_GET['id'])
            ) {

                http_response_code(400);

                header(
                    'Content-Type: text/plain; charset=UTF-8'
                );

                echo 'El identificador del rol no tiene un formato válido.';

                return;
            }


            if (
                ($_SERVER['REQUEST_METHOD'] ?? 'GET')
                ===
                'POST'
            ) {

                foreach (
                    [
                        'nombre',
                        'descripcion',
                        'es_admin'
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

                        echo 'Los datos del rol no tienen un formato válido.';

                        return;
                    }
                }
            }


            $controlador =
                $crearControlador();


            $controlador->editarRol();
        };


    $router->get(
        'usuarios/roles/editar',
        $accionEditarRol,
        'usuarios.php'
    );


    $router->post(
        'usuarios/roles/editar',
        $accionEditarRol,
        'usuarios.php'
    );


    /*
    |--------------------------------------------------------------------------
    | CAMBIAR ESTADO DE ROL - SOLO POST + CSRF
    |--------------------------------------------------------------------------
    */

    $router->post(
        'usuarios/roles/estado',
        function () use ($crearControlador) {

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

                    echo 'Los datos para cambiar el estado del rol no tienen un formato válido.';

                    return;
                }
            }


            $controlador =
                $crearControlador();


            $controlador->cambiarEstadoRol();
        },
        'usuarios.php'
    );


    /*
    |--------------------------------------------------------------------------
    | PERMISOS DEL ROL - GET Y POST
    |--------------------------------------------------------------------------
    */

    $accionPermisosRol =
        function () use ($crearControlador) {

            if (
                isset($_GET['id'])
                &&
                !is_string($_GET['id'])
            ) {

                http_response_code(400);

                header(
                    'Content-Type: text/plain; charset=UTF-8'
                );

                echo 'El identificador del rol no tiene un formato válido.';

                return;
            }


            if (
                ($_SERVER['REQUEST_METHOD'] ?? 'GET')
                ===
                'POST'
            ) {

                if (
                    isset($_POST['_csrf'])
                    &&
                    !is_string($_POST['_csrf'])
                ) {

                    http_response_code(400);

                    header(
                        'Content-Type: text/plain; charset=UTF-8'
                    );

                    echo 'El token de seguridad no tiene un formato válido.';

                    return;
                }


                if (
                    isset($_POST['permisos'])
                    &&
                    !is_array($_POST['permisos'])
                ) {

                    http_response_code(400);

                    header(
                        'Content-Type: text/plain; charset=UTF-8'
                    );

                    echo 'Los permisos recibidos no tienen un formato válido.';

                    return;
                }


                if (
                    isset($_POST['permisos'])
                    &&
                    is_array($_POST['permisos'])
                ) {

                    foreach (
                        $_POST['permisos']
                        as $archivo
                    ) {

                        if (!is_string($archivo)) {

                            http_response_code(400);

                            header(
                                'Content-Type: text/plain; charset=UTF-8'
                            );

                            echo 'Los permisos recibidos no tienen un formato válido.';

                            return;
                        }
                    }
                }
            }


            $controlador =
                $crearControlador();


            $controlador->permisosRol();
        };


    $router->get(
        'usuarios/roles/permisos',
        $accionPermisosRol,
        'usuarios.php'
    );


    $router->post(
        'usuarios/roles/permisos',
        $accionPermisosRol,
        'usuarios.php'
    );

};
