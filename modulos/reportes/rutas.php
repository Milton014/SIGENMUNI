<?php

/*
|--------------------------------------------------------------------------
| RUTAS DEL MÓDULO REPORTES
|--------------------------------------------------------------------------
|
| Rutas migradas:
|
| GET reportes
| GET reportes/empleados
| GET reportes/empleados/pdf
| GET reportes/empleados/excel
| GET reportes/historial-empleado
| GET reportes/historial-empleado/pdf
| GET reportes/historial-empleado/excel
| GET reportes/conceptos
| GET reportes/conceptos/pdf
| GET reportes/conceptos/excel
| GET reportes/categorias
| GET reportes/categorias/pdf
| GET reportes/categorias/excel
| GET reportes/liquidaciones
| GET reportes/liquidaciones/pdf
| GET reportes/liquidaciones/excel
| GET  reportes/estadisticas
| POST reportes/estadisticas/pdf
| POST reportes/estadisticas/excel
| GET reportes/auditoria
| GET reportes/auditoria/pdf
|
| "reportes.php" se conserva como CLAVE DE PERMISO.
|
| Reporte de Empleados, Historial por Empleado, Reporte de Conceptos,
| Reporte de Categorías, Reporte de Liquidaciones, Estadísticas y
| Reporte de Auditoría ya utilizan Router.
|
| Los demás reportes continúan temporalmente usando sus puntos de entrada
| de la raíz. Se migrarán y probarán uno por uno.
|
|--------------------------------------------------------------------------
*/

return function (Router $router, $conexion) {

    /*
    |--------------------------------------------------------------------------
    | MENÚ PRINCIPAL DE CONSULTAS Y REPORTES
    |--------------------------------------------------------------------------
    */

    $router->get(
        'reportes',
        function () use ($conexion) {

            require_once __DIR__
                . '/controlador/ReporteControlador.php';


            $controlador =
                new ReporteControlador(
                    $conexion
                );


            $controlador->index();
        },
        'reportes.php'
    );

    /*
    |--------------------------------------------------------------------------
    | REPORTE DE EMPLEADOS - SOLO GET
    |--------------------------------------------------------------------------
    */

    $router->get(
        'reportes/empleados',
        function () use ($conexion) {

            /*
            |------------------------------------------------------------------
            | VALIDAR FILTROS
            |------------------------------------------------------------------
            */

            foreach (
                [
                    'legajo',
                    'busqueda',
                    'estado'
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

                    echo 'Los filtros del reporte no tienen un formato válido.';

                    return;
                }
            }


            /*
            |------------------------------------------------------------------
            | CONTROLADOR
            |------------------------------------------------------------------
            */

            require_once __DIR__
                . '/controlador/ReporteControlador.php';


            $controlador =
                new ReporteControlador(
                    $conexion
                );


            $controlador->empleados();
        },
        'reporte_empleados.php'
    );


    /*
    |--------------------------------------------------------------------------
    | REPORTE DE EMPLEADOS - IMPRESIÓN / PDF - SOLO GET
    |--------------------------------------------------------------------------
    */

    $router->get(
        'reportes/empleados/pdf',
        function () use ($conexion) {

            foreach (
                [
                    'legajo',
                    'busqueda',
                    'estado'
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

                    echo 'Los filtros del reporte no tienen un formato válido.';

                    return;
                }
            }


            require_once __DIR__
                . '/controlador/ReporteControlador.php';


            $controlador =
                new ReporteControlador(
                    $conexion
                );


            $controlador->empleadosPdf();
        },
        'reporte_empleados.php'
    );


    /*
    |--------------------------------------------------------------------------
    | REPORTE DE EMPLEADOS - EXPORTAR EXCEL - SOLO GET
    |--------------------------------------------------------------------------
    |
    | Operación de solo lectura. No requiere CSRF.
    |
    |--------------------------------------------------------------------------
    */

    $router->get(
        'reportes/empleados/excel',
        function () use ($conexion) {

            foreach (
                [
                    'legajo',
                    'busqueda',
                    'estado'
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

                    echo 'Los filtros del reporte no tienen un formato válido.';

                    return;
                }
            }


            require_once __DIR__
                . '/controlador/ReporteControlador.php';


            $controlador =
                new ReporteControlador(
                    $conexion
                );


            $controlador->empleadosExcel();
        },
        'reporte_empleados.php'
    );


    /*
    |--------------------------------------------------------------------------
    | HISTORIAL POR EMPLEADO - SOLO GET
    |--------------------------------------------------------------------------
    */

    $router->get(
        'reportes/historial-empleado',
        function () use ($conexion) {

            foreach (
                [
                    'empleado_id',
                    'legajo',
                    'busqueda',
                    'estado',
                    'filtrar'
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

                    echo 'Los parámetros del historial no tienen un formato válido.';

                    return;
                }
            }


            require_once __DIR__
                . '/controlador/ReporteControlador.php';


            $controlador =
                new ReporteControlador(
                    $conexion
                );


            $controlador->historialEmpleado();
        },
        'reporte_historial_empleado.php'
    );


    /*
    |--------------------------------------------------------------------------
    | HISTORIAL POR EMPLEADO - IMPRESIÓN / PDF - SOLO GET
    |--------------------------------------------------------------------------
    */

    $router->get(
        'reportes/historial-empleado/pdf',
        function () use ($conexion) {

            if (
                isset($_GET['empleado_id'])
                &&
                !is_string($_GET['empleado_id'])
            ) {

                http_response_code(400);

                header(
                    'Content-Type: text/plain; charset=UTF-8'
                );

                echo 'El identificador del empleado no tiene un formato válido.';

                return;
            }


            require_once __DIR__
                . '/controlador/ReporteControlador.php';


            $controlador =
                new ReporteControlador(
                    $conexion
                );


            $controlador->historialEmpleadoPdf();
        },
        'reporte_historial_empleado.php'
    );


    /*
    |--------------------------------------------------------------------------
    | HISTORIAL POR EMPLEADO - EXPORTAR EXCEL - SOLO GET
    |--------------------------------------------------------------------------
    */

    $router->get(
        'reportes/historial-empleado/excel',
        function () use ($conexion) {

            if (
                isset($_GET['empleado_id'])
                &&
                !is_string($_GET['empleado_id'])
            ) {

                http_response_code(400);

                header(
                    'Content-Type: text/plain; charset=UTF-8'
                );

                echo 'El identificador del empleado no tiene un formato válido.';

                return;
            }


            require_once __DIR__
                . '/controlador/ReporteControlador.php';


            $controlador =
                new ReporteControlador(
                    $conexion
                );


            $controlador->historialEmpleadoExcel();
        },
        'reporte_historial_empleado.php'
    );


    /*
    |--------------------------------------------------------------------------
    | REPORTE DE CONCEPTOS - SOLO GET
    |--------------------------------------------------------------------------
    */

    $router->get(
        'reportes/conceptos',
        function () use ($conexion) {

            foreach (
                [
                    'buscar',
                    'tipo_concepto',
                    'categoria',
                    'activo',
                    'estado'
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

                    echo 'Los filtros del reporte no tienen un formato válido.';

                    return;
                }
            }


            require_once __DIR__
                . '/controlador/ReporteControlador.php';


            $controlador =
                new ReporteControlador(
                    $conexion
                );


            $controlador->conceptos();
        },
        'reporte_conceptos.php'
    );


    /*
    |--------------------------------------------------------------------------
    | REPORTE DE CONCEPTOS - IMPRESIÓN / PDF - SOLO GET
    |--------------------------------------------------------------------------
    */

    $router->get(
        'reportes/conceptos/pdf',
        function () use ($conexion) {

            foreach (
                [
                    'buscar',
                    'tipo_concepto',
                    'categoria',
                    'activo',
                    'estado'
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

                    echo 'Los filtros del reporte no tienen un formato válido.';

                    return;
                }
            }


            require_once __DIR__
                . '/controlador/ReporteControlador.php';


            $controlador =
                new ReporteControlador(
                    $conexion
                );


            $controlador->conceptosPdf();
        },
        'reporte_conceptos.php'
    );


    /*
    |--------------------------------------------------------------------------
    | REPORTE DE CONCEPTOS - EXPORTAR EXCEL - SOLO GET
    |--------------------------------------------------------------------------
    |
    | Operación de solo lectura. No requiere CSRF.
    |
    |--------------------------------------------------------------------------
    */

    $router->get(
        'reportes/conceptos/excel',
        function () use ($conexion) {

            foreach (
                [
                    'buscar',
                    'tipo_concepto',
                    'categoria',
                    'activo',
                    'estado'
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

                    echo 'Los filtros del reporte no tienen un formato válido.';

                    return;
                }
            }


            require_once __DIR__
                . '/controlador/ReporteControlador.php';


            $controlador =
                new ReporteControlador(
                    $conexion
                );


            $controlador->conceptosExcel();
        },
        'reporte_conceptos.php'
    );


    /*
    |--------------------------------------------------------------------------
    | REPORTE DE CATEGORÍAS - SOLO GET
    |--------------------------------------------------------------------------
    */

    $router->get(
        'reportes/categorias',
        function () use ($conexion) {

            foreach (
                [
                    'buscar',
                    'activo',
                    'estado'
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

                    echo 'Los filtros del reporte no tienen un formato válido.';

                    return;
                }
            }


            require_once __DIR__
                . '/controlador/ReporteControlador.php';


            $controlador =
                new ReporteControlador(
                    $conexion
                );


            $controlador->categorias();
        },
        'reporte_categorias.php'
    );


    /*
    |--------------------------------------------------------------------------
    | REPORTE DE CATEGORÍAS - IMPRESIÓN / PDF - SOLO GET
    |--------------------------------------------------------------------------
    */

    $router->get(
        'reportes/categorias/pdf',
        function () use ($conexion) {

            foreach (
                [
                    'buscar',
                    'activo',
                    'estado'
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

                    echo 'Los filtros del reporte no tienen un formato válido.';

                    return;
                }
            }


            require_once __DIR__
                . '/controlador/ReporteControlador.php';


            $controlador =
                new ReporteControlador(
                    $conexion
                );


            $controlador->categoriasPdf();
        },
        'reporte_categorias.php'
    );


    /*
    |--------------------------------------------------------------------------
    | REPORTE DE CATEGORÍAS - EXPORTAR EXCEL - SOLO GET
    |--------------------------------------------------------------------------
    |
    | Operación de solo lectura. No requiere CSRF.
    |
    |--------------------------------------------------------------------------
    */

    $router->get(
        'reportes/categorias/excel',
        function () use ($conexion) {

            foreach (
                [
                    'buscar',
                    'activo',
                    'estado'
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

                    echo 'Los filtros del reporte no tienen un formato válido.';

                    return;
                }
            }


            require_once __DIR__
                . '/controlador/ReporteControlador.php';


            $controlador =
                new ReporteControlador(
                    $conexion
                );


            $controlador->categoriasExcel();
        },
        'reporte_categorias.php'
    );


    /*
    |--------------------------------------------------------------------------
    | REPORTE DE LIQUIDACIONES - SOLO GET
    |--------------------------------------------------------------------------
    */

    $router->get(
        'reportes/liquidaciones',
        function () use ($conexion) {

            foreach (
                [
                    'periodo',
                    'tipo',
                    'tipo_liquidacion',
                    'estado'
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

                    echo 'Los filtros del reporte no tienen un formato válido.';

                    return;
                }
            }


            require_once __DIR__
                . '/controlador/ReporteControlador.php';


            $controlador =
                new ReporteControlador(
                    $conexion
                );


            $controlador->liquidaciones();
        },
        'reporte_liquidaciones.php'
    );


    /*
    |--------------------------------------------------------------------------
    | REPORTE DE LIQUIDACIONES - IMPRESIÓN / PDF - SOLO GET
    |--------------------------------------------------------------------------
    */

    $router->get(
        'reportes/liquidaciones/pdf',
        function () use ($conexion) {

            foreach (
                [
                    'periodo',
                    'tipo',
                    'tipo_liquidacion',
                    'estado'
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

                    echo 'Los filtros del reporte no tienen un formato válido.';

                    return;
                }
            }


            require_once __DIR__
                . '/controlador/ReporteControlador.php';


            $controlador =
                new ReporteControlador(
                    $conexion
                );


            $controlador->liquidacionesPdf();
        },
        'reporte_liquidaciones.php'
    );


    /*
    |--------------------------------------------------------------------------
    | REPORTE DE LIQUIDACIONES - EXPORTAR EXCEL - SOLO GET
    |--------------------------------------------------------------------------
    |
    | Operación de solo lectura. No requiere CSRF.
    |
    |--------------------------------------------------------------------------
    */

    $router->get(
        'reportes/liquidaciones/excel',
        function () use ($conexion) {

            foreach (
                [
                    'periodo',
                    'tipo',
                    'tipo_liquidacion',
                    'estado'
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

                    echo 'Los filtros del reporte no tienen un formato válido.';

                    return;
                }
            }


            require_once __DIR__
                . '/controlador/ReporteControlador.php';


            $controlador =
                new ReporteControlador(
                    $conexion
                );


            $controlador->liquidacionesExcel();
        },
        'reporte_liquidaciones.php'
    );


    /*
    |--------------------------------------------------------------------------
    | ESTADÍSTICAS - PANTALLA - SOLO GET
    |--------------------------------------------------------------------------
    */

    $router->get(
        'reportes/estadisticas',
        function () use ($conexion) {

            foreach (
                [
                    'desde',
                    'hasta',
                    'tipo',
                    'tipo_liquidacion'
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

                    echo 'Los filtros de estadísticas no tienen un formato válido.';

                    return;
                }
            }


            require_once __DIR__
                . '/controlador/ReporteControlador.php';


            $controlador =
                new ReporteControlador(
                    $conexion
                );


            $controlador->estadisticas();
        },
        'estadisticas.php'
    );


    /*
    |--------------------------------------------------------------------------
    | ESTADÍSTICAS - EXPORTAR PDF - SOLO POST
    |--------------------------------------------------------------------------
    |
    | Operación de solo lectura. El POST se utiliza para recibir las imágenes
    | generadas por Chart.js; no modifica datos y no requiere CSRF.
    |
    |--------------------------------------------------------------------------
    */

    $router->post(
        'reportes/estadisticas/pdf',
        function () use ($conexion) {

            foreach (
                [
                    'desde',
                    'hasta',
                    'tipo',
                    'tipo_liquidacion'
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

                    echo 'Los filtros de estadísticas no tienen un formato válido.';

                    return;
                }
            }


            require_once __DIR__
                . '/controlador/ReporteControlador.php';


            $controlador =
                new ReporteControlador(
                    $conexion
                );


            $controlador->estadisticasPdf();
        },
        'estadisticas.php'
    );


    /*
    |--------------------------------------------------------------------------
    | ESTADÍSTICAS - EXPORTAR EXCEL - SOLO POST
    |--------------------------------------------------------------------------
    |
    | Operación de solo lectura. El POST se utiliza para recibir las imágenes
    | generadas por Chart.js; no modifica datos y no requiere CSRF.
    |
    |--------------------------------------------------------------------------
    */

    $router->post(
        'reportes/estadisticas/excel',
        function () use ($conexion) {

            foreach (
                [
                    'desde',
                    'hasta',
                    'tipo',
                    'tipo_liquidacion'
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

                    echo 'Los filtros de estadísticas no tienen un formato válido.';

                    return;
                }
            }


            require_once __DIR__
                . '/controlador/ReporteControlador.php';


            $controlador =
                new ReporteControlador(
                    $conexion
                );


            $controlador->estadisticasExcel();
        },
        'estadisticas.php'
    );


    /*
    |--------------------------------------------------------------------------
    | REPORTE DE AUDITORÍA - PANTALLA - SOLO GET
    |--------------------------------------------------------------------------
    */

    $router->get(
        'reportes/auditoria',
        function () use ($conexion) {

            foreach (
                [
                    'fecha_desde',
                    'fecha_hasta',
                    'usuario',
                    'rol',
                    'modulo',
                    'accion',
                    'entidad',
                    'detalle_id'
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

                    echo 'Los filtros del Reporte de Auditoría no tienen un formato válido.';

                    return;
                }
            }


            require_once __DIR__
                . '/controlador/ReporteControlador.php';


            $controlador =
                new ReporteControlador(
                    $conexion
                );


            $controlador->auditoria();
        },
        'reporte_auditoria.php'
    );


    /*
    |--------------------------------------------------------------------------
    | REPORTE DE AUDITORÍA - EXPORTAR PDF - SOLO GET
    |--------------------------------------------------------------------------
    */

    $router->get(
        'reportes/auditoria/pdf',
        function () use ($conexion) {

            foreach (
                [
                    'fecha_desde',
                    'fecha_hasta',
                    'usuario',
                    'rol',
                    'modulo',
                    'accion',
                    'entidad'
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

                    echo 'Los filtros del Reporte de Auditoría no tienen un formato válido.';

                    return;
                }
            }


            require_once __DIR__
                . '/controlador/ReporteControlador.php';


            $controlador =
                new ReporteControlador(
                    $conexion
                );


            $controlador->auditoriaPdf();
        },
        'reporte_auditoria.php'
    );

};
