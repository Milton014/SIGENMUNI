<?php

/*
|--------------------------------------------------------------------------
| CONFIGURACIÓN DE CORREO - SIGENMUNI
|--------------------------------------------------------------------------
|
| La configuración SMTP ya no contiene credenciales escritas directamente.
|
| Los valores se obtienen desde las variables de entorno cargadas previamente
| por vlucas/phpdotenv en core/bootstrap.php.
|
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| SERVIDOR SMTP
|--------------------------------------------------------------------------
*/

define(
    'SMTP_HOST',
    $_ENV['MAIL_HOST']
    ?? 'smtp.gmail.com'
);


/*
|--------------------------------------------------------------------------
| PUERTO SMTP
|--------------------------------------------------------------------------
|
| Gmail + STARTTLS utiliza normalmente el puerto 587.
|
|--------------------------------------------------------------------------
*/

define(
    'SMTP_PORT',
    (int)(
        $_ENV['MAIL_PORT']
        ?? 587
    )
);


/*
|--------------------------------------------------------------------------
| CUENTA SMTP
|--------------------------------------------------------------------------
*/

define(
    'SMTP_USER',
    $_ENV['MAIL_USERNAME']
    ?? ''
);


/*
|--------------------------------------------------------------------------
| CONTRASEÑA DE APLICACIÓN
|--------------------------------------------------------------------------
|
| Para Gmail se debe utilizar una contraseña de aplicación.
| Nunca debe utilizarse la contraseña normal de la cuenta.
|
|--------------------------------------------------------------------------
*/

define(
    'SMTP_PASS',
    $_ENV['MAIL_PASSWORD']
    ?? ''
);


/*
|--------------------------------------------------------------------------
| CORREO REMITENTE
|--------------------------------------------------------------------------
|
| Si MAIL_FROM_ADDRESS no está definido, se utiliza la misma cuenta SMTP.
|
|--------------------------------------------------------------------------
*/

define(
    'SMTP_FROM',
    $_ENV['MAIL_FROM_ADDRESS']
    ??
    (
        $_ENV['MAIL_USERNAME']
        ?? ''
    )
);


/*
|--------------------------------------------------------------------------
| NOMBRE DEL REMITENTE
|--------------------------------------------------------------------------
*/

define(
    'SMTP_FROM_NAME',
    $_ENV['MAIL_FROM_NAME']
    ?? 'Municipalidad de Fortín Lugones'
);