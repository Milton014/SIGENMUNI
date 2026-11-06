<?php

/*
|--------------------------------------------------------------------------
| CONTROLADOR DE CATEGORÍAS
|--------------------------------------------------------------------------
|
| Gestiona:
|
| - listado de categorías;
| - alta;
| - edición;
| - activación / inactivación.
|
| Los importes salariales NO se administran desde este controlador.
| Los valores de Sueldo Básico, Dedicación Funcional y Suplemento Especial
| se gestionan desde concepto_valor.
|
|--------------------------------------------------------------------------
*/

require_once __DIR__
    . '/../modelo/CategoriaModelo.php';

require_once __DIR__
    . '/../../../core/Csrf.php';

require_once __DIR__
    . '/../../../core/Url.php';


class CategoriaControlador
{
    private $modelo;


    /*
    |--------------------------------------------------------------------------
    | CONSTRUCTOR
    |--------------------------------------------------------------------------
    */

    public function __construct($conexion)
    {
        $this->modelo =
            new CategoriaModelo(
                $conexion
            );
    }


    /*
    |--------------------------------------------------------------------------
    | LISTADO
    |--------------------------------------------------------------------------
    */

    /** Conserva únicamente los filtros y la página del listado. */
    private function contextoListado(): array
    {
        $buscar = is_string($_GET['buscar'] ?? null) ? trim($_GET['buscar']) : '';
        $buscar = mb_check_encoding($buscar, 'UTF-8') ? mb_substr($buscar, 0, 120, 'UTF-8') : '';
        $activo = is_string($_GET['activo'] ?? null) ? trim($_GET['activo']) : '';
        if (!in_array($activo, ['', '0', '1'], true)) $activo = '';
        return array_merge(sigenmuniParametrosPaginacion($_GET), compact('buscar', 'activo'));
    }

    public function index()
    {
        $contextoListado = $this->contextoListado();
        $buscar = $contextoListado['buscar'];
        $activo = $contextoListado['activo'];
        $paginacion = sigenmuniCalcularPaginacion(0, 1, $contextoListado['por_pagina']);

        $mensaje = '';

        $tipo_mensaje = '';


        /*
        |--------------------------------------------------------------------------
        | MENSAJES
        |--------------------------------------------------------------------------
        */

        if (
            isset(
                $_GET['ok']
            )
        ) {

            switch (
                $_GET['ok']
            ) {

                case 'nuevo':

                    $mensaje =
                        'Categoría creada correctamente.';

                    $tipo_mensaje =
                        'ok';

                    break;


                case 'editar':

                    $mensaje =
                        'Categoría actualizada correctamente.';

                    $tipo_mensaje =
                        'ok';

                    break;


                case 'estado':

                    $mensaje =
                        'Estado de la categoría actualizado correctamente.';

                    $tipo_mensaje =
                        'ok';

                    break;
            }
        }


        if (
            isset(
                $_GET['error']
            )
        ) {

            switch (
                $_GET['error']
            ) {

                case 'id':

                    $mensaje =
                        'La categoría indicada no es válida.';

                    $tipo_mensaje =
                        'error';

                    break;


                case 'no_existe':

                    $mensaje =
                        'La categoría indicada no existe.';

                    $tipo_mensaje =
                        'error';

                    break;


                case 'estado':

                    $mensaje =
                        'No se pudo actualizar el estado de la categoría.';

                    $tipo_mensaje =
                        'error';

                    break;


                case 'csrf':

                    $mensaje =
                        'La sesión de seguridad expiró. Recargue la página e inténtelo nuevamente.';

                    $tipo_mensaje =
                        'error';

                    break;
            }
        }


        try {

            $resultado = $this->modelo->listarPaginado($buscar, $activo,
                $contextoListado['pagina'], $contextoListado['por_pagina']);
            $categorias = $resultado['registros'];
            $paginacion = $resultado['paginacion'];
            $contextoListado['pagina'] = $paginacion['pagina'];

        } catch (Exception $e) {

            $categorias = [];


            $mensaje =
                $e->getMessage();


            $tipo_mensaje =
                'error';
        }


        require __DIR__
            . '/../vista/categorias.php';
    }


    /*
    |--------------------------------------------------------------------------
    | NUEVA CATEGORÍA
    |--------------------------------------------------------------------------
    */

    public function nuevo()
    {
        $contextoListado = $this->contextoListado();
        $mensaje = '';

        $tipo_mensaje = '';


        $datos = [

            'codigo' =>
                '',

            'nombre' =>
                '',

            'activo' =>
                1
        ];


        /*
        |--------------------------------------------------------------------------
        | POST
        |--------------------------------------------------------------------------
        */

        if (
            $_SERVER['REQUEST_METHOD']
            ===
            'POST'
        ) {

            $datos = [

                'codigo' =>
                    trim(
                        $_POST['codigo']
                        ?? ''
                    ),

                'nombre' =>
                    trim(
                        $_POST['nombre']
                        ?? ''
                    ),

                'activo' =>
                    isset(
                        $_POST['activo']
                    )
                        ?
                        1
                        :
                        0
            ];


            try {

                /*
                |--------------------------------------------------------------------------
                | PROTECCIÓN CSRF
                |--------------------------------------------------------------------------
                */

                if (
                    !sigenmuniCsrfValido(
                        $_POST['_csrf']
                        ?? ''
                    )
                ) {
                    throw new Exception(
                        'La sesión del formulario no es válida o expiró. Recargue la página e inténtelo nuevamente.'
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | VALIDACIONES
                |--------------------------------------------------------------------------
                */

                if (
                    $datos['codigo']
                    ===
                    ''
                ) {

                    throw new Exception(
                        'Debe ingresar el código de la categoría.'
                    );
                }


                if (
                    !ctype_digit(
                        (string)$datos['codigo']
                    )
                ) {

                    throw new Exception(
                        'El código de la categoría debe contener solo números.'
                    );
                }


                if (
                    (int)$datos['codigo']
                    <=
                    0
                ) {

                    throw new Exception(
                        'El código de la categoría debe ser mayor a cero.'
                    );
                }


                if (
                    $datos['nombre']
                    ===
                    ''
                ) {

                    throw new Exception(
                        'Debe ingresar el nombre de la categoría.'
                    );
                }


                if (
                    mb_strlen(
                        $datos['nombre']
                    )
                    >
                    150
                ) {

                    throw new Exception(
                        'El nombre de la categoría no puede superar los 150 caracteres.'
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | GUARDAR
                |--------------------------------------------------------------------------
                */

                $this->modelo
                    ->guardar(
                        $datos
                    );


                $urlListado =
                    sigenmuniUrlRuta('categorias', array_merge($this->contextoListado(), [
                            'ok' => 'nuevo'
                        ]));


                header(
                    'Location: ' . $urlListado
                );

                exit;


            } catch (Exception $e) {

                $mensaje =
                    $e->getMessage();


                $tipo_mensaje =
                    'error';
            }
        }


        require __DIR__
            . '/../vista/categoria_nueva.php';
    }


    /*
    |--------------------------------------------------------------------------
    | EDITAR CATEGORÍA
    |--------------------------------------------------------------------------
    */

    public function editar()
    {
        $contextoListado = $this->contextoListado();
        $id =
            isset(
                $_GET['id']
            )
                ?
                (int)$_GET['id']
                :
                0;


        if (
            $id
            <=
            0
        ) {

            $urlListado =
                sigenmuniUrlRuta('categorias', array_merge($this->contextoListado(), [
                        'error' => 'id'
                    ]));


            header(
                'Location: ' . $urlListado
            );

            exit;
        }


        try {

            $categoria =
                $this->modelo
                    ->obtenerPorId(
                        $id
                    );


            if (!$categoria) {

                $urlListado =
                    sigenmuniUrlRuta('categorias', array_merge($this->contextoListado(), [
                            'error' => 'no_existe'
                        ]));


                header(
                    'Location: ' . $urlListado
                );

                exit;
            }


            $mensaje = '';

            $tipo_mensaje = '';


            /*
            |--------------------------------------------------------------------------
            | POST
            |--------------------------------------------------------------------------
            */

            if (
                $_SERVER['REQUEST_METHOD']
                ===
                'POST'
            ) {

                $datos = [

                    'codigo' =>
                        trim(
                            $_POST['codigo']
                            ?? ''
                        ),

                    'nombre' =>
                        trim(
                            $_POST['nombre']
                            ?? ''
                        ),

                    'activo' =>
                        isset(
                            $_POST['activo']
                        )
                            ?
                            1
                            :
                            0
                ];


                try {

                    /*
                    |--------------------------------------------------------------------------
                    | PROTECCIÓN CSRF
                    |--------------------------------------------------------------------------
                    */

                    if (
                        !sigenmuniCsrfValido(
                            $_POST['_csrf']
                            ?? ''
                        )
                    ) {
                        throw new Exception(
                            'La sesión del formulario no es válida o expiró. Recargue la página e inténtelo nuevamente.'
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | VALIDACIONES
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $datos['codigo']
                        ===
                        ''
                    ) {

                        throw new Exception(
                            'Debe ingresar el código de la categoría.'
                        );
                    }


                    if (
                        !ctype_digit(
                            (string)$datos['codigo']
                        )
                    ) {

                        throw new Exception(
                            'El código de la categoría debe contener solo números.'
                        );
                    }


                    if (
                        (int)$datos['codigo']
                        <=
                        0
                    ) {

                        throw new Exception(
                            'El código de la categoría debe ser mayor a cero.'
                        );
                    }


                    if (
                        $datos['nombre']
                        ===
                        ''
                    ) {

                        throw new Exception(
                            'Debe ingresar el nombre de la categoría.'
                        );
                    }


                    if (
                        mb_strlen(
                            $datos['nombre']
                        )
                        >
                        150
                    ) {

                        throw new Exception(
                            'El nombre de la categoría no puede superar los 150 caracteres.'
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | ACTUALIZAR
                    |--------------------------------------------------------------------------
                    */

                    $this->modelo
                        ->actualizar(
                            $id,
                            $datos
                        );


                    $urlListado =
                        sigenmuniUrlRuta('categorias', array_merge($this->contextoListado(), [
                                'ok' => 'editar'
                            ]));


                    header(
                        'Location: ' . $urlListado
                    );

                    exit;


                } catch (Exception $e) {

                    $mensaje =
                        $e->getMessage();


                    $tipo_mensaje =
                        'error';


                    /*
                    |--------------------------------------------------------------------------
                    | CONSERVAR DATOS INGRESADOS
                    |--------------------------------------------------------------------------
                    */

                    $categoria['codigo'] =
                        $datos['codigo'];


                    $categoria['nombre'] =
                        $datos['nombre'];


                    $categoria['activo'] =
                        $datos['activo'];
                }
            }


            /*
            |--------------------------------------------------------------------------
            | DATOS ASOCIADOS
            |--------------------------------------------------------------------------
            |
            | Se usan para informar al administrador cuántos empleados y valores
            | dependen de esta categoría.
            |
            |--------------------------------------------------------------------------
            */

            $empleadosAsociados =
                $this->modelo
                    ->contarEmpleadosAsociados(
                        $id
                    );


            $empleadosActivosAsociados =
                $this->modelo
                    ->contarEmpleadosAsociados(
                        $id,
                        true
                    );


            $valoresAsociados =
                $this->modelo
                    ->contarValoresConceptosAsociados(
                        $id
                    );


        } catch (Exception $e) {

            $mensaje =
                $e->getMessage();


            $tipo_mensaje =
                'error';


            $categoria =
                null;


            $empleadosAsociados =
                0;


            $empleadosActivosAsociados =
                0;


            $valoresAsociados =
                0;
        }


        require __DIR__
            . '/../vista/categoria_editar.php';
    }


    /*
    |--------------------------------------------------------------------------
    | CAMBIAR ESTADO
    |--------------------------------------------------------------------------
    */

    public function estado()
    {
        /*
        |--------------------------------------------------------------------------
        | SOLO POST
        |--------------------------------------------------------------------------
        |
        | Activar / inactivar modifica datos.
        | Por lo tanto esta acción solo acepta solicitudes POST.
        |
        |--------------------------------------------------------------------------
        */

        $metodo =
            strtoupper(
                $_SERVER['REQUEST_METHOD']
                ?? 'GET'
            );


        if ($metodo !== 'POST') {

            http_response_code(
                405
            );

            header(
                'Allow: POST'
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | DATOS RECIBIDOS
        |--------------------------------------------------------------------------
        */

        $id =
            isset(
                $_POST['id']
            )
                ?
                (int)$_POST['id']
                :
                0;


        $estado =
            isset(
                $_POST['estado']
            )
                ?
                (int)$_POST['estado']
                :
                -1;


        /*
        |--------------------------------------------------------------------------
        | PROTECCIÓN CSRF
        |--------------------------------------------------------------------------
        */

        if (
            !sigenmuniCsrfValido(
                $_POST['_csrf']
                ?? ''
            )
        ) {

            header(
                'Location: '
                .
                sigenmuniUrlRuta('categorias', array_merge($this->contextoListado(), [
                        'error' => 'csrf'
                    ]))
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDAR ID
        |--------------------------------------------------------------------------
        */

        if (
            $id
            <=
            0
        ) {

            header(
                'Location: '
                .
                sigenmuniUrlRuta('categorias', array_merge($this->contextoListado(), [
                        'error' => 'id'
                    ]))
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDAR ESTADO
        |--------------------------------------------------------------------------
        */

        if (
            !in_array(
                $estado,
                [
                    0,
                    1
                ],
                true
            )
        ) {

            header(
                'Location: '
                .
                sigenmuniUrlRuta('categorias', array_merge($this->contextoListado(), [
                        'error' => 'estado'
                    ]))
            );

            exit;
        }


        try {

            $categoria =
                $this->modelo
                    ->obtenerPorId(
                        $id
                    );


            if (!$categoria) {

                header(
                    'Location: '
                    .
                    sigenmuniUrlRuta('categorias', array_merge($this->contextoListado(), [
                            'error' => 'no_existe'
                        ]))
                );

                exit;
            }


            /*
            |--------------------------------------------------------------------------
            | ADVERTENCIA DE CONSISTENCIA
            |--------------------------------------------------------------------------
            |
            | No bloqueamos la inactivación aunque tenga empleados asociados.
            | El módulo simplemente deja la categoría fuera de nuevas selecciones.
            |
            | Los empleados y liquidaciones históricas conservan su referencia.
            |
            |--------------------------------------------------------------------------
            */

            $this->modelo
                ->cambiarEstado(
                    $id,
                    $estado
                );


            header(
                'Location: '
                .
                sigenmuniUrlRuta('categorias', array_merge($this->contextoListado(), [
                        'ok' => 'estado'
                    ]))
            );

            exit;


        } catch (Exception $e) {

            header(
                'Location: '
                .
                sigenmuniUrlRuta('categorias', array_merge($this->contextoListado(), [
                        'error' => 'estado'
                    ]))
            );

            exit;
        }
    }

}