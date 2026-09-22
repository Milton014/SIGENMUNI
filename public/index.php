<?php

/*
|--------------------------------------------------------------------------
| FRONT CONTROLLER - SIGENMUNI
|--------------------------------------------------------------------------
|
| Punto de entrada de los módulos gestionados por el Router.
|
| Ejemplos:
|
| public/index.php?r=inicio
| public/index.php?r=empleados
| public/index.php?r=liquidacion
|
| Durante esta etapa el index.php de la raíz continúa existiendo como respaldo.
| Si no se informa una ruta mediante "r", se abre el Menú Principal.
|
|--------------------------------------------------------------------------
*/

try {

    /*
    |--------------------------------------------------------------------------
    | INICIALIZAR APLICACIÓN Y ROUTER
    |--------------------------------------------------------------------------
    */

    $router =
        require __DIR__
        . '/../core/bootstrap.php';


    /*
    |--------------------------------------------------------------------------
    | DESPACHAR SOLICITUD
    |--------------------------------------------------------------------------
    */

    $router->despachar(
        $_SERVER['REQUEST_METHOD']
        ??
        'GET',

        $_GET['r']
        ??
        'inicio'
    );


} catch (Throwable $e) {

    /*
    |--------------------------------------------------------------------------
    | REGISTRO INTERNO DEL ERROR
    |--------------------------------------------------------------------------
    */

    error_log(
        '[SIGENMUNI - public/index.php] '
        . $e->getMessage()
    );


    /*
    |--------------------------------------------------------------------------
    | RESPUESTA SEGURA
    |--------------------------------------------------------------------------
    */

    http_response_code(
        500
    );


    if (
        !headers_sent()
    ) {

        header(
            'Content-Type: text/html; charset=UTF-8'
        );
    }


    echo '<!DOCTYPE html>';
    echo '<html lang="es">';
    echo '<head>';
    echo '<meta charset="UTF-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
    echo '<title>Error - SIGENMUNI</title>';
    echo '</head>';
    echo '<body>';
    echo '<h1>No se pudo completar la solicitud.</h1>';
    echo '<p>';
    echo 'Revisá el registro de errores de PHP/Apache. ';
    echo 'Si el problema está relacionado con la URL base, revisá config/app.php.';
    echo '</p>';
    echo '</body>';
    echo '</html>';
}
