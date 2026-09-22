<?php

/*
|--------------------------------------------------------------------------
| RUTAS DEL MÓDULO AYUDA
|--------------------------------------------------------------------------
|
| Ruta principal:
|
| GET ayuda
|
| Las distintas guías se seleccionan mediante el parámetro GET "seccion":
|
| public/index.php?r=ayuda&seccion=manual
| public/index.php?r=ayuda&seccion=empleados
| public/index.php?r=ayuda&seccion=categorias
| public/index.php?r=ayuda&seccion=conceptos
| public/index.php?r=ayuda&seccion=conceptos_empleado
| public/index.php?r=ayuda&seccion=liquidaciones
| public/index.php?r=ayuda&seccion=reportes
| public/index.php?r=ayuda&seccion=usuarios
|
| "ayuda.php" se conserva como CLAVE DE PERMISO del sistema aunque
| la solicitud ingrese mediante public/index.php.
|
|--------------------------------------------------------------------------
*/

return function (Router $router, $conexion) {

    $router->get(
        'ayuda',
        function () use ($conexion) {

            if (
                isset($_GET['seccion'])
                &&
                !is_string($_GET['seccion'])
            ) {

                http_response_code(400);

                header(
                    'Content-Type: text/plain; charset=UTF-8'
                );

                echo 'La sección de ayuda no tiene un formato válido.';

                return;
            }


            require_once __DIR__
                . '/controlador/AyudaControlador.php';


            $controlador =
                new AyudaControlador(
                    $conexion
                );


            $controlador->index();
        },
        'ayuda.php'
    );

};
