<?php

/*
|--------------------------------------------------------------------------
| EJEMPLO DE CONFIGURACIÓN SMTP - SIGENMUNI
|--------------------------------------------------------------------------
|
| Copiar este archivo como:
|
| config/config_correo.php
|
| y completar los datos reales del correo utilizado por el sistema.
|
| IMPORTANTE:
| No subir config/config_correo.php al repositorio.
|
|--------------------------------------------------------------------------
*/


define(
    'SMTP_HOST',
    'smtp.gmail.com'
);


define(
    'SMTP_PORT',
    587
);


define(
    'SMTP_USER',
    'correo@gmail.com'
);


define(
    'SMTP_PASS',
    'CONTRASENA_DE_APLICACION'
);


define(
    'SMTP_FROM',
    'correo@gmail.com'
);


define(
    'SMTP_FROM_NAME',
    'Municipalidad de Fortín Lugones'
);