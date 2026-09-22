<?php

/*
|--------------------------------------------------------------------------
| EJEMPLO DE CONFIGURACIÓN DE BASE DE DATOS
|--------------------------------------------------------------------------
|
| Copiar este archivo como:
|
| config/conexion.php
|
| y completar los datos reales de conexión.
|
| IMPORTANTE:
| No subir config/conexion.php al repositorio.
|
|--------------------------------------------------------------------------
*/

$host = 'localhost';

$user = 'root';

$pass = 'TU_CONTRASENA_MYSQL';

$db = 'sigenmuni4';


$conexion =
    new mysqli(
        $host,
        $user,
        $pass,
        $db
    );


if (
    $conexion->connect_error
) {

    die(
        'Error de conexión a la base de datos: '
        .
        $conexion->connect_error
    );
}


$conexion->set_charset(
    'utf8mb4'
);