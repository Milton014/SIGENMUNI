<?php

/*
|--------------------------------------------------------------------------
| RUTAS DEL MÓDULO INICIO
|--------------------------------------------------------------------------
|
| GET inicio
|
| Inicio no utiliza una clave de rol_modulo_permiso porque el Menú Principal
| debe estar disponible para cualquier usuario autenticado. El propio menú
| filtra las tarjetas de acuerdo con los permisos reales del rol.
|
|--------------------------------------------------------------------------
*/

return function (Router $router, $conexion) {

    $router->getSesion(
        'inicio',
        function () use ($conexion) {

            require_once __DIR__
                . '/controlador/InicioControlador.php';


            $controlador =
                new InicioControlador(
                    $conexion
                );


            $controlador->index();
        }
    );
};
