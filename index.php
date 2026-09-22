<?php

/*
|--------------------------------------------------------------------------
| ENTRADA INICIAL - SIGENMUNI
|--------------------------------------------------------------------------
|
| Este archivo no contiene lógica del sistema.
|
| Su única función es redirigir la entrada principal del proyecto
| hacia el Login gestionado por el Front Controller.
|
|--------------------------------------------------------------------------
*/

require_once __DIR__
    . '/core/Url.php';


header(
    'Location: '
    .
    sigenmuniUrlRuta(
        'login'
    )
);

exit();