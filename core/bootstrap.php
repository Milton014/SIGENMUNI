<?php

/*
|--------------------------------------------------------------------------
| INICIALIZACIÓN DE LA ENTRADA ÚNICA
|--------------------------------------------------------------------------
|
| La conexión ya se centraliza en config/conexion.php.
| La seguridad general se centraliza en core/seguridad.php.
| Las clases se cargan con require_once; aún no se incorpora Composer.
| No debe producir HTML antes de iniciar la sesión o verificar permisos.
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/Url.php';
require_once __DIR__ . '/seguridad.php';

iniciarSesionSiHaceFalta();

// Resolver la base antes de generar cualquier respuesta.
sigenmuniBaseUrl();

// La conexión se centraliza en config/conexion.php.
global $conexion;
require_once __DIR__ . '/../config/conexion.php';

if (!isset($conexion) || !$conexion) {
    throw new RuntimeException('No se obtuvo la conexión del sistema.');
}

require_once __DIR__ . '/Router.php';

$router = new Router();
$modulos = require __DIR__ . '/../config/modulos.php';

if (!is_array($modulos)) {
    throw new RuntimeException('config/modulos.php debe devolver un arreglo.');
}

foreach ($modulos as $modulo) {
    if (!is_string($modulo) || !preg_match('/\A[a-z][a-z0-9_]*\z/', $modulo)) {
        throw new RuntimeException('Nombre de módulo no válido en config/modulos.php.');
    }

    // La lista proviene de configuración interna, nunca de la solicitud.
    $archivoRutas = __DIR__ . '/../modulos/' . $modulo . '/rutas.php';

    if (!is_file($archivoRutas)) {
        throw new RuntimeException('Falta rutas.php para un módulo habilitado.');
    }

    $registrarRutas = require $archivoRutas;

    if (!is_callable($registrarRutas)) {
        throw new RuntimeException('Cada rutas.php debe devolver una función registradora.');
    }

    $registrarRutas($router, $conexion);
}

return $router;
