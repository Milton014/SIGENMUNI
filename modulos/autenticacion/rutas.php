<?php

/*
|--------------------------------------------------------------------------
| RUTAS DEL MÓDULO AUTENTICACIÓN - ETAPA 3
|--------------------------------------------------------------------------
|
| Rutas migradas:
|
| GET  login
| POST login
| POST logout
| GET  recuperar
| POST recuperar
| GET  verificar-codigo
| POST actualizar-acceso
|
| Login y recuperación son rutas públicas.
| Logout requiere una sesión válida y se ejecuta por POST.
|
|--------------------------------------------------------------------------
*/

return function (Router $router, $conexion) {

    /*
    |--------------------------------------------------------------------------
    | LOGIN
    |--------------------------------------------------------------------------
    */

    $accionLogin =
        function () use ($conexion) {

            require_once __DIR__
                . '/controlador/AutenticacionControlador.php';

            $controlador =
                new AutenticacionControlador(
                    $conexion
                );

            $controlador->login();
        };


    $router->getPublico(
        'login',
        $accionLogin
    );


    $router->postPublico(
        'login',
        $accionLogin
    );


    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    */

    $router->postSesion(
        'logout',
        function () use ($conexion) {

            require_once __DIR__
                . '/controlador/AutenticacionControlador.php';

            $controlador =
                new AutenticacionControlador(
                    $conexion
                );

            $controlador->logout();
        }
    );


    /*
    |--------------------------------------------------------------------------
    | RECUPERAR ACCESO
    |--------------------------------------------------------------------------
    */

    $accionRecuperar =
        function () use ($conexion) {

            require_once __DIR__
                . '/controlador/AutenticacionControlador.php';

            $controlador =
                new AutenticacionControlador(
                    $conexion
                );

            $controlador->recuperar();
        };


    $router->getPublico(
        'recuperar',
        $accionRecuperar
    );


    $router->postPublico(
        'recuperar',
        $accionRecuperar
    );


    /*
    |--------------------------------------------------------------------------
    | VERIFICAR CÓDIGO
    |--------------------------------------------------------------------------
    */

    $router->getPublico(
        'verificar-codigo',
        function () use ($conexion) {

            require_once __DIR__
                . '/controlador/AutenticacionControlador.php';

            $controlador =
                new AutenticacionControlador(
                    $conexion
                );

            $controlador->verificarCodigo();
        }
    );


    /*
    |--------------------------------------------------------------------------
    | ACTUALIZAR ACCESO
    |--------------------------------------------------------------------------
    */

    $router->postPublico(
        'actualizar-acceso',
        function () use ($conexion) {

            require_once __DIR__
                . '/controlador/AutenticacionControlador.php';

            $controlador =
                new AutenticacionControlador(
                    $conexion
                );

            $controlador->actualizarAcceso();
        }
    );
};
