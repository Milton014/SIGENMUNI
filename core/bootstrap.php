<?php

use Dotenv\Dotenv;

/*
|--------------------------------------------------------------------------
| INICIALIZACIÓN DE LA ENTRADA ÚNICA
|--------------------------------------------------------------------------
|
| Este archivo centraliza el arranque general de SIGENMUNI.
|
| Aquí se realizan, en orden:
|
| 1. Carga automática de dependencias mediante Composer.
| 2. Carga de variables de entorno mediante vlucas/phpdotenv.
| 3. Validación de variables de entorno obligatorias.
| 4. Configuración general de la aplicación.
| 5. Carga de utilidades de URL y seguridad.
| 6. Inicio de sesión.
| 7. Conexión con la base de datos.
| 8. Creación del Router.
| 9. Registro de las rutas de cada módulo.
|
| No debe producir HTML antes de iniciar la sesión o verificar permisos.
|
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| RAÍZ DEL PROYECTO
|--------------------------------------------------------------------------
|
| bootstrap.php se encuentra dentro de /core.
|
| dirname(__DIR__) permite obtener la carpeta raíz del proyecto:
|
| SIGENMUNI/
|
|--------------------------------------------------------------------------
*/

$raizProyecto =
    dirname(__DIR__);


/*
|--------------------------------------------------------------------------
| AUTOLOAD DE COMPOSER
|--------------------------------------------------------------------------
|
| Composer genera:
|
| vendor/autoload.php
|
| Este archivo permite cargar automáticamente las clases de las librerías
| instaladas mediante Composer.
|
| Actualmente SIGENMUNI utiliza, entre otras dependencias:
|
| vlucas/phpdotenv
|
|--------------------------------------------------------------------------
*/

$autoloadComposer =
    $raizProyecto
    . '/vendor/autoload.php';


if (!is_file($autoloadComposer)) {

    throw new RuntimeException(
        'No se encontró vendor/autoload.php. '
        . 'Ejecute "composer install" desde la raíz del proyecto.'
    );
}


require_once $autoloadComposer;


/*
|--------------------------------------------------------------------------
| VARIABLES DE ENTORNO
|--------------------------------------------------------------------------
|
| vlucas/phpdotenv lee el archivo:
|
| SIGENMUNI/.env
|
| y carga sus variables para que puedan ser utilizadas mediante:
|
| $_ENV['NOMBRE_VARIABLE']
|
| Ejemplos:
|
| $_ENV['DB_HOST']
| $_ENV['DB_DATABASE']
| $_ENV['MAIL_USERNAME']
|
| createImmutable() evita sobrescribir variables de entorno que ya hayan
| sido definidas externamente por el servidor.
|
| safeLoad() permite continuar si no existe físicamente un archivo .env,
| siempre que las variables necesarias hayan sido configuradas directamente
| en el entorno del servidor.
|
|--------------------------------------------------------------------------
*/

$dotenv =
    Dotenv::createImmutable(
        $raizProyecto
    );


$dotenv->safeLoad();


/*
|--------------------------------------------------------------------------
| VALIDAR VARIABLES DE ENTORNO OBLIGATORIAS
|--------------------------------------------------------------------------
|
| Antes de continuar con el arranque de SIGENMUNI se verifica que exista
| la configuración mínima necesaria.
|
| Si falta alguna variable crítica, phpdotenv genera una excepción y evita
| que el sistema se ejecute con una configuración incompleta.
|
| DB_PASSWORD no se valida como no vacía porque algunos entornos locales
| de MySQL pueden utilizar un usuario sin contraseña.
|
|--------------------------------------------------------------------------
*/

$dotenv
    ->required([
        'APP_NAME',
        'APP_ENV',
        'APP_DEBUG',
        'APP_TIMEZONE',

        'DB_HOST',
        'DB_PORT',
        'DB_DATABASE',
        'DB_USERNAME',

        'MAIL_HOST',
        'MAIL_PORT',
        'MAIL_USERNAME',
        'MAIL_PASSWORD'
    ])
    ->notEmpty();


/*
|--------------------------------------------------------------------------
| VALIDAR TIPOS DE VARIABLES
|--------------------------------------------------------------------------
|
| Además de comprobar su existencia, se validan algunos valores que tienen
| un tipo específico.
|
|--------------------------------------------------------------------------
*/

$dotenv
    ->required([
        'DB_PORT',
        'MAIL_PORT'
    ])
    ->isInteger();


$dotenv
    ->required(
        'APP_DEBUG'
    )
    ->isBoolean();


/*
|--------------------------------------------------------------------------
| VALIDAR ENTORNO DE LA APLICACIÓN
|--------------------------------------------------------------------------
|
| APP_ENV solamente puede utilizar uno de los valores permitidos.
|
|--------------------------------------------------------------------------
*/

$dotenv
    ->required(
        'APP_ENV'
    )
    ->allowedValues([
        'development',
        'testing',
        'production'
    ]);


/*
|--------------------------------------------------------------------------
| ZONA HORARIA
|--------------------------------------------------------------------------
|
| La zona horaria se obtiene desde APP_TIMEZONE.
|
| Se mantiene un valor por defecto correspondiente a Argentina como medida
| adicional de seguridad.
|
|--------------------------------------------------------------------------
*/

$zonaHoraria =
    $_ENV['APP_TIMEZONE']
    ??
    'America/Argentina/Buenos_Aires';


if (
    !in_array(
        $zonaHoraria,
        timezone_identifiers_list(),
        true
    )
) {

    throw new RuntimeException(
        'La zona horaria configurada en APP_TIMEZONE no es válida.'
    );
}


date_default_timezone_set(
    $zonaHoraria
);


/*
|--------------------------------------------------------------------------
| ARCHIVOS GENERALES DEL SISTEMA
|--------------------------------------------------------------------------
|
| Url.php:
|   contiene las funciones relacionadas con las URLs del sistema.
|
| seguridad.php:
|   contiene las funciones generales de sesión, autenticación y permisos.
|
|--------------------------------------------------------------------------
*/

require_once
    __DIR__
    . '/Url.php';


require_once
    __DIR__
    . '/seguridad.php';


/*
|--------------------------------------------------------------------------
| SESIÓN
|--------------------------------------------------------------------------
|
| Se inicia la sesión únicamente cuando todavía no existe una sesión activa.
|
|--------------------------------------------------------------------------
*/

iniciarSesionSiHaceFalta();


/*
|--------------------------------------------------------------------------
| URL BASE
|--------------------------------------------------------------------------
|
| Se resuelve la URL base de SIGENMUNI antes de generar cualquier respuesta.
|
|--------------------------------------------------------------------------
*/

sigenmuniBaseUrl();


/*
|--------------------------------------------------------------------------
| CONEXIÓN A LA BASE DE DATOS
|--------------------------------------------------------------------------
|
| config/conexion.php obtiene actualmente la configuración desde las
| variables de entorno:
|
| DB_HOST
| DB_PORT
| DB_DATABASE
| DB_USERNAME
| DB_PASSWORD
|
| Por lo tanto, las credenciales reales ya no se encuentran escritas
| directamente dentro del código fuente.
|
|--------------------------------------------------------------------------
*/

global $conexion;


require_once
    $raizProyecto
    . '/config/conexion.php';


if (
    !isset($conexion)
    ||
    !($conexion instanceof mysqli)
) {

    throw new RuntimeException(
        'No se obtuvo una conexión válida con la base de datos.'
    );
}


/*
|--------------------------------------------------------------------------
| ROUTER
|--------------------------------------------------------------------------
|
| Se carga el Router principal utilizado por la entrada única del sistema.
|
|--------------------------------------------------------------------------
*/

require_once
    __DIR__
    . '/Router.php';


$router =
    new Router();


/*
|--------------------------------------------------------------------------
| MÓDULOS HABILITADOS
|--------------------------------------------------------------------------
|
| config/modulos.php contiene la lista de módulos habilitados que serán
| registrados dinámicamente en el Router.
|
|--------------------------------------------------------------------------
*/

$modulos =
    require
        $raizProyecto
        . '/config/modulos.php';


if (!is_array($modulos)) {

    throw new RuntimeException(
        'config/modulos.php debe devolver un arreglo.'
    );
}


/*
|--------------------------------------------------------------------------
| REGISTRO DE RUTAS DE LOS MÓDULOS
|--------------------------------------------------------------------------
|
| Cada módulo habilitado debe contener:
|
| modulos/NOMBRE_MODULO/rutas.php
|
| Cada rutas.php debe devolver una función encargada de registrar las rutas
| correspondientes dentro del Router general.
|
|--------------------------------------------------------------------------
*/

foreach ($modulos as $modulo) {

    /*
    |--------------------------------------------------------------------------
    | VALIDAR NOMBRE DEL MÓDULO
    |--------------------------------------------------------------------------
    |
    | Solamente se permiten nombres internos compuestos por:
    |
    | - letras minúsculas
    | - números
    | - guion bajo
    |
    | El primer carácter debe ser una letra.
    |
    |--------------------------------------------------------------------------
    */

    if (
        !is_string($modulo)
        ||
        !preg_match(
            '/\A[a-z][a-z0-9_]*\z/',
            $modulo
        )
    ) {

        throw new RuntimeException(
            'Nombre de módulo no válido en config/modulos.php.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ARCHIVO DE RUTAS DEL MÓDULO
    |--------------------------------------------------------------------------
    */

    $archivoRutas =
        $raizProyecto
        . '/modulos/'
        . $modulo
        . '/rutas.php';


    if (!is_file($archivoRutas)) {

        throw new RuntimeException(
            'Falta rutas.php para el módulo habilitado: '
            . $modulo
            . '.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CARGAR REGISTRADOR DE RUTAS
    |--------------------------------------------------------------------------
    */

    $registrarRutas =
        require $archivoRutas;


    if (!is_callable($registrarRutas)) {

        throw new RuntimeException(
            'El archivo rutas.php del módulo '
            . $modulo
            . ' debe devolver una función registradora.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | REGISTRAR RUTAS
    |--------------------------------------------------------------------------
    */

    $registrarRutas(
        $router,
        $conexion
    );
}


/*
|--------------------------------------------------------------------------
| DEVOLVER ROUTER
|--------------------------------------------------------------------------
|
| public/index.php recibe esta instancia y continúa con el procesamiento
| de la solicitud HTTP.
|
|--------------------------------------------------------------------------
*/

return $router;