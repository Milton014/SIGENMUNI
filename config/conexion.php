<?php

/*
|--------------------------------------------------------------------------
| CONEXIÓN A LA BASE DE DATOS
|--------------------------------------------------------------------------
|
| Los datos de conexión ya no se encuentran escritos directamente
| en este archivo.
|
| Las variables son cargadas desde el archivo .env mediante
| vlucas/phpdotenv en core/bootstrap.php.
|
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| OBTENER VARIABLES DE ENTORNO
|--------------------------------------------------------------------------
*/

$host =
    $_ENV['DB_HOST']
    ?? '';

$puerto =
    (int)(
        $_ENV['DB_PORT']
        ?? 3306
    );

$user =
    $_ENV['DB_USERNAME']
    ?? '';

$pass =
    $_ENV['DB_PASSWORD']
    ?? '';

$db =
    $_ENV['DB_DATABASE']
    ?? '';


/*
|--------------------------------------------------------------------------
| VALIDAR CONFIGURACIÓN
|--------------------------------------------------------------------------
|
| La contraseña puede estar vacía en algunos entornos locales,
| por eso DB_PASSWORD no se valida como obligatoria.
|
|--------------------------------------------------------------------------
*/

if (
    $host === ''
    ||
    $user === ''
    ||
    $db === ''
) {

    throw new RuntimeException(
        'La configuración de la base de datos está incompleta.'
    );
}


/*
|--------------------------------------------------------------------------
| CREAR CONEXIÓN
|--------------------------------------------------------------------------
*/

$conexion =
    new mysqli(
        $host,
        $user,
        $pass,
        $db,
        $puerto
    );


/*
|--------------------------------------------------------------------------
| VALIDAR CONEXIÓN
|--------------------------------------------------------------------------
*/

if ($conexion->connect_error) {

    throw new RuntimeException(
        'No fue posible conectar con la base de datos.'
    );
}


/*
|--------------------------------------------------------------------------
| CODIFICACIÓN
|--------------------------------------------------------------------------
*/

if (
    !$conexion->set_charset(
        'utf8mb4'
    )
) {

    throw new RuntimeException(
        'No fue posible configurar la codificación de la base de datos.'
    );
}