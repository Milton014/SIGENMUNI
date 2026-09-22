<?php

/*
|--------------------------------------------------------------------------
| RUTAS DEL MÓDULO LIQUIDACIÓN
|--------------------------------------------------------------------------
|
| Rutas migradas:
|
| GET  liquidacion
| GET  liquidacion/nueva
| POST liquidacion/nueva
| GET  liquidacion/novedades
| POST liquidacion/novedades
| GET  liquidacion/complementaria
| POST liquidacion/complementaria
| GET  liquidacion/novedades-sac
| POST liquidacion/novedades-sac
| GET  liquidacion/complementaria-sac
| POST liquidacion/complementaria-sac
| GET  liquidacion/protocolar
| POST liquidacion/protocolar
| POST liquidacion/procesar
| GET  liquidacion/ver
| POST liquidacion/estado
| GET  liquidacion/recibo
| GET  liquidacion/recibos
| POST liquidacion/recibo/enviar
|
| "liquidacion.php" se conserva únicamente como CLAVE DE PERMISO.
|
| Las demás acciones pendientes continúan temporalmente mediante sus puntos
| de entrada actuales hasta migrarlos uno por uno.
|
|--------------------------------------------------------------------------
*/

return function (Router $router, $conexion) {

    /*
    |--------------------------------------------------------------------------
    | LISTADO DE LIQUIDACIONES
    |--------------------------------------------------------------------------
    */

    $router->get(
        'liquidacion',
        function () use ($conexion) {

            /*
            |------------------------------------------------------------------
            | VALIDAR PARÁMETROS DEL LISTADO
            |------------------------------------------------------------------
            */

            foreach (
                [
                    'periodo',
                    'tipo_liquidacion',
                    'estado',
                    'ok',
                    'msg'
                ]
                as $campo
            ) {
                if (
                    isset($_GET[$campo])
                    &&
                    !is_string($_GET[$campo])
                ) {
                    http_response_code(400);

                    header(
                        'Content-Type: text/plain; charset=UTF-8'
                    );

                    echo 'Los filtros y mensajes del listado no tienen un formato válido.';

                    return;
                }
            }


            /*
            |------------------------------------------------------------------
            | CONTROLADOR
            |------------------------------------------------------------------
            */

            require_once __DIR__
                . '/controlador/LiquidacionControlador.php';


            $controlador =
                new LiquidacionControlador(
                    $conexion
                );


            $controlador->index();
        },
        'liquidacion.php'
    );

    /*
    |--------------------------------------------------------------------------
    | NUEVA LIQUIDACIÓN - GET Y POST
    |--------------------------------------------------------------------------
    |
    | GET  muestra el formulario.
    | POST valida CSRF y procesa el alta mediante el mismo método nueva().
    |
    |--------------------------------------------------------------------------
    */

    $accionNuevaLiquidacion =
        function () use ($conexion) {

            /*
            |------------------------------------------------------------------
            | VALIDAR FORMATO DE DATOS POST
            |------------------------------------------------------------------
            */

            if (
                ($_SERVER['REQUEST_METHOD'] ?? 'GET')
                ===
                'POST'
            ) {
                foreach (
                    [
                        'tipo_liquidacion',
                        'periodo',
                        'fecha_liquidacion',
                        'descripcion',
                        '_csrf'
                    ]
                    as $campo
                ) {
                    if (
                        isset($_POST[$campo])
                        &&
                        !is_string($_POST[$campo])
                    ) {
                        http_response_code(400);

                        header(
                            'Content-Type: text/plain; charset=UTF-8'
                        );

                        echo 'Los datos del formulario no tienen un formato válido.';

                        return;
                    }
                }
            }


            /*
            |------------------------------------------------------------------
            | CONTROLADOR
            |------------------------------------------------------------------
            */

            require_once __DIR__
                . '/controlador/LiquidacionControlador.php';


            $controlador =
                new LiquidacionControlador(
                    $conexion
                );


            $controlador->nueva();
        };


    $router->get(
        'liquidacion/nueva',
        $accionNuevaLiquidacion,
        'liquidacion.php'
    );


    $router->post(
        'liquidacion/nueva',
        $accionNuevaLiquidacion,
        'liquidacion.php'
    );


    /*
    |--------------------------------------------------------------------------
    | NOVEDADES MENSUALES - GET Y POST
    |--------------------------------------------------------------------------
    |
    | GET  muestra las novedades por empleado.
    | POST guarda las excepciones con protección CSRF.
    |
    |--------------------------------------------------------------------------
    */

    $accionNovedadesLiquidacion =
        function () use ($conexion) {

            /*
            |------------------------------------------------------------------
            | VALIDAR ID
            |------------------------------------------------------------------
            */

            if (
                isset($_GET['id'])
                &&
                !is_string($_GET['id'])
            ) {
                http_response_code(400);

                header(
                    'Content-Type: text/plain; charset=UTF-8'
                );

                echo 'El identificador de la liquidación no tiene un formato válido.';

                return;
            }


            /*
            |------------------------------------------------------------------
            | VALIDAR FORMATO POST
            |------------------------------------------------------------------
            */

            if (
                ($_SERVER['REQUEST_METHOD'] ?? 'GET')
                ===
                'POST'
            ) {

                if (
                    isset($_POST['_csrf'])
                    &&
                    !is_string($_POST['_csrf'])
                ) {
                    http_response_code(400);

                    header(
                        'Content-Type: text/plain; charset=UTF-8'
                    );

                    echo 'El token de seguridad no tiene un formato válido.';

                    return;
                }


                if (
                    isset($_POST['empleados'])
                    &&
                    !is_array($_POST['empleados'])
                ) {
                    http_response_code(400);

                    header(
                        'Content-Type: text/plain; charset=UTF-8'
                    );

                    echo 'Los datos de novedades no tienen un formato válido.';

                    return;
                }
            }


            /*
            |------------------------------------------------------------------
            | CONTROLADOR
            |------------------------------------------------------------------
            */

            require_once __DIR__
                . '/controlador/LiquidacionControlador.php';


            $controlador =
                new LiquidacionControlador(
                    $conexion
                );


            $controlador->novedades();
        };


    $router->get(
        'liquidacion/novedades',
        $accionNovedadesLiquidacion,
        'liquidacion.php'
    );


    $router->post(
        'liquidacion/novedades',
        $accionNovedadesLiquidacion,
        'liquidacion.php'
    );


    /*
    |--------------------------------------------------------------------------
    | COMPLEMENTARIA - PERSONAL Y NOVEDADES
    |--------------------------------------------------------------------------
    */

    $accionComplementaria =
        function () use ($conexion) {

            if (
                isset($_GET['id'])
                &&
                !is_string($_GET['id'])
            ) {
                http_response_code(400);

                header(
                    'Content-Type: text/plain; charset=UTF-8'
                );

                echo 'El identificador de la liquidación no tiene un formato válido.';

                return;
            }


            if (
                ($_SERVER['REQUEST_METHOD'] ?? 'GET')
                ===
                'POST'
            ) {

                if (
                    isset($_POST['_csrf'])
                    &&
                    !is_string($_POST['_csrf'])
                ) {
                    http_response_code(400);

                    header(
                        'Content-Type: text/plain; charset=UTF-8'
                    );

                    echo 'El token de seguridad no tiene un formato válido.';

                    return;
                }


                if (
                    isset($_POST['empleados'])
                    &&
                    !is_array($_POST['empleados'])
                ) {
                    http_response_code(400);

                    header(
                        'Content-Type: text/plain; charset=UTF-8'
                    );

                    echo 'Los datos del personal no tienen un formato válido.';

                    return;
                }
            }


            require_once __DIR__
                . '/controlador/LiquidacionControlador.php';


            try {

                $controlador =
                    new LiquidacionControlador(
                        $conexion
                    );


                $controlador->complementaria();

            } catch (Throwable $e) {

                error_log(
                    'Error en ruta liquidacion/complementaria: '
                    . $e->getMessage()
                    . ' | Archivo: '
                    . $e->getFile()
                    . ' | Línea: '
                    . $e->getLine()
                );


                http_response_code(500);


                $urlVolver =
                    sigenmuniUrlRuta(
                        'liquidacion'
                    );


                echo
                    "<!DOCTYPE html>"
                    . "<html lang='es'>"
                    . "<head>"
                    . "<meta charset='UTF-8'>"
                    . "<meta name='viewport' content='width=device-width, initial-scale=1.0'>"
                    . "<title>Error - SIGENMUNI</title>"
                    . "<style>"
                    . "body{font-family:Arial,sans-serif;background:#f4f7fb;margin:0;padding:30px;color:#1f2937;}"
                    . ".mensaje{max-width:700px;margin:50px auto;background:#fee2e2;border:1px solid #fecaca;color:#991b1b;padding:24px;border-radius:14px;box-shadow:0 8px 20px rgba(0,0,0,.08);text-align:center;}"
                    . ".mensaje h2{margin-top:0;}"
                    . ".btn{display:inline-block;margin-top:16px;background:#1f2937;color:white;text-decoration:none;padding:10px 14px;border-radius:8px;font-weight:bold;}"
                    . "</style>"
                    . "</head>"
                    . "<body>"
                    . "<div class='mensaje'>"
                    . "<h2>No se pudo abrir Personal y Novedades</h2>"
                    . "<p>Ocurrió un error al cargar la liquidación complementaria. El detalle técnico fue registrado en el log del servidor.</p>"
                    . "<a class='btn' href='"
                    . htmlspecialchars(
                        $urlVolver,
                        ENT_QUOTES,
                        'UTF-8'
                    )
                    . "'>Volver a Liquidaciones</a>"
                    . "</div>"
                    . "</body>"
                    . "</html>";
            }
        };


    $router->get(
        'liquidacion/complementaria',
        $accionComplementaria,
        'liquidacion.php'
    );


    $router->post(
        'liquidacion/complementaria',
        $accionComplementaria,
        'liquidacion.php'
    );


    /*
    |--------------------------------------------------------------------------
    | NOVEDADES SAC - GET Y POST
    |--------------------------------------------------------------------------
    |
    | GET  muestra días SAC por empleado.
    | POST guarda novedades SAC con protección CSRF.
    |
    |--------------------------------------------------------------------------
    */

    $accionNovedadesSac =
        function () use ($conexion) {

            /*
            |------------------------------------------------------------------
            | VALIDAR ID
            |------------------------------------------------------------------
            */

            if (
                isset($_GET['id'])
                &&
                !is_string($_GET['id'])
            ) {
                http_response_code(400);

                header(
                    'Content-Type: text/plain; charset=UTF-8'
                );

                echo 'El identificador de la liquidación no tiene un formato válido.';

                return;
            }


            /*
            |------------------------------------------------------------------
            | VALIDAR FORMATO POST
            |------------------------------------------------------------------
            */

            if (
                ($_SERVER['REQUEST_METHOD'] ?? 'GET')
                ===
                'POST'
            ) {

                if (
                    isset($_POST['_csrf'])
                    &&
                    !is_string($_POST['_csrf'])
                ) {
                    http_response_code(400);

                    header(
                        'Content-Type: text/plain; charset=UTF-8'
                    );

                    echo 'El token de seguridad no tiene un formato válido.';

                    return;
                }


                if (
                    isset($_POST['empleados'])
                    &&
                    !is_array($_POST['empleados'])
                ) {
                    http_response_code(400);

                    header(
                        'Content-Type: text/plain; charset=UTF-8'
                    );

                    echo 'Los datos de SAC no tienen un formato válido.';

                    return;
                }
            }


            /*
            |------------------------------------------------------------------
            | CONTROLADOR
            |------------------------------------------------------------------
            */

            require_once __DIR__
                . '/controlador/LiquidacionControlador.php';


            $controlador =
                new LiquidacionControlador(
                    $conexion
                );


            $controlador->novedadesSac();
        };


    $router->get(
        'liquidacion/novedades-sac',
        $accionNovedadesSac,
        'liquidacion.php'
    );


    $router->post(
        'liquidacion/novedades-sac',
        $accionNovedadesSac,
        'liquidacion.php'
    );


    /*
    |--------------------------------------------------------------------------
    | COMPLEMENTARIA DE SAC - PERSONAL Y DÍAS SAC
    |--------------------------------------------------------------------------
    |
    | GET  muestra personal disponible y saldo SAC pendiente.
    | POST guarda participantes y días SAC con protección CSRF.
    |
    |--------------------------------------------------------------------------
    */

    $accionComplementariaSac =
        function () use ($conexion) {

            if (
                isset($_GET['id'])
                &&
                !is_string($_GET['id'])
            ) {
                http_response_code(400);

                header(
                    'Content-Type: text/plain; charset=UTF-8'
                );

                echo 'El identificador de la liquidación no tiene un formato válido.';

                return;
            }


            if (
                ($_SERVER['REQUEST_METHOD'] ?? 'GET')
                ===
                'POST'
            ) {

                if (
                    isset($_POST['_csrf'])
                    &&
                    !is_string($_POST['_csrf'])
                ) {
                    http_response_code(400);

                    header(
                        'Content-Type: text/plain; charset=UTF-8'
                    );

                    echo 'El token de seguridad no tiene un formato válido.';

                    return;
                }


                if (
                    isset($_POST['empleados'])
                    &&
                    !is_array($_POST['empleados'])
                ) {
                    http_response_code(400);

                    header(
                        'Content-Type: text/plain; charset=UTF-8'
                    );

                    echo 'Los datos de SAC no tienen un formato válido.';

                    return;
                }
            }


            require_once __DIR__
                . '/controlador/LiquidacionControlador.php';


            $controlador =
                new LiquidacionControlador(
                    $conexion
                );


            $controlador->complementariaSac();
        };


    $router->get(
        'liquidacion/complementaria-sac',
        $accionComplementariaSac,
        'liquidacion.php'
    );


    $router->post(
        'liquidacion/complementaria-sac',
        $accionComplementariaSac,
        'liquidacion.php'
    );


    /*
    |--------------------------------------------------------------------------
    | GASTOS PROTOCOLARES - GET Y POST
    |--------------------------------------------------------------------------
    |
    | GET  muestra la carga por empleado.
    | POST guarda importes y condición previsional con protección CSRF.
    |
    |--------------------------------------------------------------------------
    */

    $accionProtocolar =
        function () use ($conexion) {

            /*
            |------------------------------------------------------------------
            | VALIDAR PARÁMETROS GET
            |------------------------------------------------------------------
            */

            foreach (
                [
                    'id',
                    'ok'
                ]
                as $campo
            ) {
                if (
                    isset($_GET[$campo])
                    &&
                    !is_string($_GET[$campo])
                ) {
                    http_response_code(400);

                    header(
                        'Content-Type: text/plain; charset=UTF-8'
                    );

                    echo 'Los parámetros de Gastos Protocolares no tienen un formato válido.';

                    return;
                }
            }


            /*
            |------------------------------------------------------------------
            | VALIDAR FORMATO POST
            |------------------------------------------------------------------
            */

            if (
                ($_SERVER['REQUEST_METHOD'] ?? 'GET')
                ===
                'POST'
            ) {

                if (
                    isset($_POST['_csrf'])
                    &&
                    !is_string($_POST['_csrf'])
                ) {
                    http_response_code(400);

                    header(
                        'Content-Type: text/plain; charset=UTF-8'
                    );

                    echo 'El token de seguridad no tiene un formato válido.';

                    return;
                }


                if (
                    isset($_POST['empleados'])
                    &&
                    !is_array($_POST['empleados'])
                ) {
                    http_response_code(400);

                    header(
                        'Content-Type: text/plain; charset=UTF-8'
                    );

                    echo 'Los datos de Gastos Protocolares no tienen un formato válido.';

                    return;
                }
            }


            /*
            |------------------------------------------------------------------
            | CONTROLADOR
            |------------------------------------------------------------------
            */

            require_once __DIR__
                . '/controlador/LiquidacionControlador.php';


            $controlador =
                new LiquidacionControlador(
                    $conexion
                );


            $controlador->protocolar();
        };


    $router->get(
        'liquidacion/protocolar',
        $accionProtocolar,
        'liquidacion.php'
    );


    $router->post(
        'liquidacion/protocolar',
        $accionProtocolar,
        'liquidacion.php'
    );


    /*
    |--------------------------------------------------------------------------
    | PROCESAR LIQUIDACIÓN - SOLO POST
    |--------------------------------------------------------------------------
    */

    $router->post(
        'liquidacion/procesar',
        function () use ($conexion) {

            foreach (
                [
                    'id',
                    '_csrf'
                ]
                as $campo
            ) {
                if (
                    isset($_POST[$campo])
                    &&
                    !is_string($_POST[$campo])
                ) {
                    http_response_code(400);

                    header(
                        'Content-Type: text/plain; charset=UTF-8'
                    );

                    echo 'Los datos de procesamiento no tienen un formato válido.';

                    return;
                }
            }


            require_once __DIR__
                . '/controlador/LiquidacionControlador.php';


            $controlador =
                new LiquidacionControlador(
                    $conexion
                );


            $controlador->procesar();
        },
        'liquidacion.php'
    );


    /*
    |--------------------------------------------------------------------------
    | VER LIQUIDACIÓN - SOLO GET
    |--------------------------------------------------------------------------
    |
    | Consulta general y detalle por empleado.
    |
    |--------------------------------------------------------------------------
    */

    $router->get(
        'liquidacion/ver',
        function () use ($conexion) {

            foreach (
                [
                    'id',
                    'empleado_id'
                ]
                as $campo
            ) {
                if (
                    isset($_GET[$campo])
                    &&
                    !is_string($_GET[$campo])
                ) {
                    http_response_code(400);

                    header(
                        'Content-Type: text/plain; charset=UTF-8'
                    );

                    echo 'Los parámetros de consulta no tienen un formato válido.';

                    return;
                }
            }


            require_once __DIR__
                . '/controlador/LiquidacionControlador.php';


            $controlador =
                new LiquidacionControlador(
                    $conexion
                );


            $controlador->ver();
        },
        'liquidacion.php'
    );


    /*
    |--------------------------------------------------------------------------
    | ESTADO DE LIQUIDACIÓN - SOLO POST
    |--------------------------------------------------------------------------
    |
    | Acciones permitidas:
    |
    | - anular
    | - deshacer
    |
    | Ambas acciones modifican datos, por lo tanto se ejecutan únicamente
    | mediante POST y están protegidas con CSRF.
    |
    |--------------------------------------------------------------------------
    */

    $router->post(
        'liquidacion/estado',
        function () use ($conexion) {

            foreach (
                [
                    'id',
                    'accion',
                    '_csrf'
                ]
                as $campo
            ) {

                if (
                    isset($_POST[$campo])
                    &&
                    !is_string($_POST[$campo])
                ) {

                    http_response_code(400);

                    header(
                        'Content-Type: text/plain; charset=UTF-8'
                    );

                    echo 'Los datos enviados no tienen un formato válido.';

                    return;
                }
            }


            require_once __DIR__
                . '/controlador/LiquidacionControlador.php';


            $controlador =
                new LiquidacionControlador(
                    $conexion
                );


            $controlador->estado();
        },
        'liquidacion.php'
    );


    /*
    |--------------------------------------------------------------------------
    | VER RECIBO DE SUELDO - SOLO GET
    |--------------------------------------------------------------------------
    |
    | El recibo conserva la misma clave de permiso del punto de entrada
    | histórico:
    |
    | reporte_historial_empleado.php
    |
    |--------------------------------------------------------------------------
    */

    $router->get(
        'liquidacion/recibo',
        function () use ($conexion) {

            foreach (
                [
                    'liquidacion_id',
                    'empleado_id',
                    'origen'
                ]
                as $campo
            ) {

                if (
                    isset($_GET[$campo])
                    &&
                    !is_string($_GET[$campo])
                ) {

                    http_response_code(400);

                    header(
                        'Content-Type: text/plain; charset=UTF-8'
                    );

                    echo 'Los parámetros del recibo no tienen un formato válido.';

                    return;
                }
            }


            require_once __DIR__
                . '/controlador/LiquidacionControlador.php';


            $controlador =
                new LiquidacionControlador(
                    $conexion
                );


            $controlador->recibo();
        },
        'reporte_historial_empleado.php'
    );


    /*
    |--------------------------------------------------------------------------
    | IMPRIMIR TODOS LOS RECIBOS - SOLO GET
    |--------------------------------------------------------------------------
    |
    | Genera la vista conjunta de todos los recibos de una liquidación.
    | Es una operación de lectura y no requiere CSRF.
    |
    |--------------------------------------------------------------------------
    */

    $router->get(
        'liquidacion/recibos',
        function () use ($conexion) {

            if (
                isset($_GET['liquidacion_id'])
                &&
                !is_string($_GET['liquidacion_id'])
            ) {

                http_response_code(400);

                header(
                    'Content-Type: text/plain; charset=UTF-8'
                );

                echo 'El identificador de la liquidación no tiene un formato válido.';

                return;
            }


            require_once __DIR__
                . '/controlador/LiquidacionControlador.php';


            $controlador =
                new LiquidacionControlador(
                    $conexion
                );


            $controlador->recibosLiquidacion();
        },
        'liquidacion.php'
    );


    /*
    |--------------------------------------------------------------------------
    | ENVIAR RECIBO PDF POR EMAIL - SOLO POST
    |--------------------------------------------------------------------------
    |
    | Acción con efecto externo. Requiere POST + CSRF.
    |
    | Conserva la misma clave de permiso del punto de entrada histórico:
    |
    | liquidacion.php
    |
    |--------------------------------------------------------------------------
    */

    $router->post(
        'liquidacion/recibo/enviar',
        function () use ($conexion) {

            foreach (
                [
                    'liquidacion_id',
                    'empleado_id',
                    'origen',
                    '_csrf'
                ]
                as $campo
            ) {

                if (
                    isset($_POST[$campo])
                    &&
                    !is_string($_POST[$campo])
                ) {

                    http_response_code(400);

                    header(
                        'Content-Type: text/plain; charset=UTF-8'
                    );

                    echo 'Los datos enviados no tienen un formato válido.';

                    return;
                }
            }


            require_once __DIR__
                . '/controlador/LiquidacionControlador.php';


            $controlador =
                new LiquidacionControlador(
                    $conexion
                );


            $controlador->enviarRecibo();
        },
        'liquidacion.php'
    );

};
