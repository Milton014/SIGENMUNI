<?php

/*
|--------------------------------------------------------------------------
| ROUTER - SIGENMUNI
|--------------------------------------------------------------------------
|
| Registra rutas explícitas por método HTTP.
|
| Tipos de acceso:
|
| - PERMISO:
|   requiere sesión + permiso del módulo.
|
| - SESIÓN:
|   requiere únicamente una sesión válida.
|
| - PÚBLICO:
|   no requiere sesión. Se utiliza para autenticación
|   y recuperación de acceso.
|
|--------------------------------------------------------------------------
*/

final class Router
{
    private $rutas = [];


    /*
    |--------------------------------------------------------------------------
    | TIPOS DE ACCESO
    |--------------------------------------------------------------------------
    */

    private const ACCESO_PERMISO =
        'permiso';

    private const ACCESO_SESION =
        'sesion';

    private const ACCESO_PUBLICO =
        'publico';


    /*
    |--------------------------------------------------------------------------
    | GET CON PERMISO DE MÓDULO
    |--------------------------------------------------------------------------
    */

    public function get(
        $ruta,
        callable $accion,
        $permiso
    ) {

        $this->registrar(
            'GET',
            $ruta,
            $accion,
            self::ACCESO_PERMISO,
            $permiso
        );
    }


    /*
    |--------------------------------------------------------------------------
    | POST CON PERMISO DE MÓDULO
    |--------------------------------------------------------------------------
    */

    public function post(
        $ruta,
        callable $accion,
        $permiso
    ) {

        $this->registrar(
            'POST',
            $ruta,
            $accion,
            self::ACCESO_PERMISO,
            $permiso
        );
    }


    /*
    |--------------------------------------------------------------------------
    | GET SOLO CON SESIÓN
    |--------------------------------------------------------------------------
    |
    | Se utiliza para pantallas generales disponibles para cualquier
    | usuario autenticado.
    |
    | Ejemplo:
    |
    | inicio
    |
    |--------------------------------------------------------------------------
    */

    public function getSesion(
        $ruta,
        callable $accion
    ) {

        $this->registrar(
            'GET',
            $ruta,
            $accion,
            self::ACCESO_SESION,
            null
        );
    }


    /*
    |--------------------------------------------------------------------------
    | POST SOLO CON SESIÓN
    |--------------------------------------------------------------------------
    |
    | Se deja disponible para acciones generales que necesiten sesión
    | pero no un permiso particular.
    |
    |--------------------------------------------------------------------------
    */

    public function postSesion(
        $ruta,
        callable $accion
    ) {

        $this->registrar(
            'POST',
            $ruta,
            $accion,
            self::ACCESO_SESION,
            null
        );
    }


    /*
    |--------------------------------------------------------------------------
    | GET PÚBLICO
    |--------------------------------------------------------------------------
    |
    | No requiere sesión.
    |
    | Ejemplos:
    |
    | login
    | recuperar
    | verificar-codigo
    |
    |--------------------------------------------------------------------------
    */

    public function getPublico(
        $ruta,
        callable $accion
    ) {

        $this->registrar(
            'GET',
            $ruta,
            $accion,
            self::ACCESO_PUBLICO,
            null
        );
    }


    /*
    |--------------------------------------------------------------------------
    | POST PÚBLICO
    |--------------------------------------------------------------------------
    |
    | No requiere sesión.
    |
    | Ejemplos:
    |
    | login
    | recuperar
    | actualizar-acceso
    |
    |--------------------------------------------------------------------------
    */

    public function postPublico(
        $ruta,
        callable $accion
    ) {

        $this->registrar(
            'POST',
            $ruta,
            $accion,
            self::ACCESO_PUBLICO,
            null
        );
    }


    /*
    |--------------------------------------------------------------------------
    | REGISTRAR RUTA
    |--------------------------------------------------------------------------
    */

    private function registrar(
        $metodo,
        $ruta,
        callable $accion,
        $tipoAcceso,
        $permiso = null
    ) {

        $metodo =
            strtoupper(
                trim(
                    (string)$metodo
                )
            );


        /*
        |--------------------------------------------------------------------------
        | VALIDAR RUTA
        |--------------------------------------------------------------------------
        */

        $this->validarRutaConfigurada(
            $ruta
        );


        /*
        |--------------------------------------------------------------------------
        | VALIDAR MÉTODO HTTP
        |--------------------------------------------------------------------------
        */

        if (
            !in_array(
                $metodo,
                [
                    'GET',
                    'POST'
                ],
                true
            )
        ) {

            throw new InvalidArgumentException(
                'Método HTTP no admitido por el Router.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDAR TIPO DE ACCESO
        |--------------------------------------------------------------------------
        */

        if (
            !in_array(
                $tipoAcceso,
                [
                    self::ACCESO_PERMISO,
                    self::ACCESO_SESION,
                    self::ACCESO_PUBLICO
                ],
                true
            )
        ) {

            throw new InvalidArgumentException(
                'El tipo de acceso de la ruta no es válido.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDAR PERMISO
        |--------------------------------------------------------------------------
        |
        | Solamente las rutas ACCESO_PERMISO deben tener clave de permiso.
        |
        |--------------------------------------------------------------------------
        */

        if (
            $tipoAcceso
            ===
            self::ACCESO_PERMISO
        ) {

            if (
                !is_string($permiso)
                ||
                trim($permiso) === ''
            ) {

                throw new InvalidArgumentException(
                    'El permiso de la ruta no es válido.'
                );
            }


            $permiso =
                trim(
                    $permiso
                );

        } else {

            $permiso =
                null;
        }


        /*
        |--------------------------------------------------------------------------
        | EVITAR RUTAS DUPLICADAS
        |--------------------------------------------------------------------------
        */

        if (
            isset(
                $this->rutas[$ruta][$metodo]
            )
        ) {

            throw new LogicException(
                'Hay una ruta duplicada para el mismo método HTTP.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | CREAR CONTENEDOR DE LA RUTA
        |--------------------------------------------------------------------------
        */

        if (
            !isset(
                $this->rutas[$ruta]
            )
        ) {

            $this->rutas[$ruta] = [];
        }


        /*
        |--------------------------------------------------------------------------
        | GUARDAR RUTA
        |--------------------------------------------------------------------------
        */

        $this->rutas[$ruta][$metodo] = [

            'accion' =>
                $accion,

            'tipo_acceso' =>
                $tipoAcceso,

            'permiso' =>
                $permiso
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | DESPACHAR SOLICITUD
    |--------------------------------------------------------------------------
    */

    public function despachar(
        $metodo,
        $ruta
    ) {

        $metodo =
            strtoupper(
                trim(
                    (string)$metodo
                )
            );


        /*
        |--------------------------------------------------------------------------
        | VALIDAR FORMATO DE RUTA
        |--------------------------------------------------------------------------
        */

        if (
            !$this->rutaValida(
                $ruta
            )
        ) {

            $this->responderError(
                400,
                'La ruta solicitada no es válida.'
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | RUTA NO REGISTRADA
        |--------------------------------------------------------------------------
        */

        if (
            !isset(
                $this->rutas[$ruta]
            )
        ) {

            $this->responderError(
                404,
                'La ruta solicitada no está registrada.'
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | MÉTODO HTTP NO PERMITIDO
        |--------------------------------------------------------------------------
        */

        if (
            !isset(
                $this->rutas[$ruta][$metodo]
            )
        ) {

            $metodosPermitidos =
                array_keys(
                    $this->rutas[$ruta]
                );


            sort(
                $metodosPermitidos
            );


            header(
                'Allow: '
                .
                implode(
                    ', ',
                    $metodosPermitidos
                )
            );


            $this->responderError(
                405,
                'El método de la solicitud no está permitido para esta ruta.'
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | OBTENER DESTINO
        |--------------------------------------------------------------------------
        */

        $destino =
            $this->rutas[$ruta][$metodo];


        /*
        |--------------------------------------------------------------------------
        | SEGURIDAD DE LA RUTA
        |--------------------------------------------------------------------------
        */

        switch (
            $destino['tipo_acceso']
        ) {


            /*
            |--------------------------------------------------------------------------
            | CON PERMISO DE MÓDULO
            |--------------------------------------------------------------------------
            */

            case self::ACCESO_PERMISO:

                verificarPermisoModulo(
                    $destino['permiso']
                );

                break;


            /*
            |--------------------------------------------------------------------------
            | SOLO SESIÓN
            |--------------------------------------------------------------------------
            */

            case self::ACCESO_SESION:

                verificarSesion();

                break;


            /*
            |--------------------------------------------------------------------------
            | PÚBLICA
            |--------------------------------------------------------------------------
            |
            | No ejecutamos ninguna validación de sesión.
            |
            |--------------------------------------------------------------------------
            */

            case self::ACCESO_PUBLICO:

                break;


            /*
            |--------------------------------------------------------------------------
            | PROTECCIÓN EXTRA
            |--------------------------------------------------------------------------
            */

            default:

                $this->responderError(
                    500,
                    'La configuración de seguridad de la ruta no es válida.'
                );

                return;
        }


        /*
        |--------------------------------------------------------------------------
        | EJECUTAR CONTROLADOR
        |--------------------------------------------------------------------------
        */

        $accion =
            $destino['accion'];


        $accion();
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDAR RUTA DE CONFIGURACIÓN
    |--------------------------------------------------------------------------
    */

    private function validarRutaConfigurada(
        $ruta
    ) {

        if (
            !$this->rutaValida(
                $ruta
            )
        ) {

            throw new InvalidArgumentException(
                'Ruta de configuración no válida.'
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | FORMATO DE RUTA
    |--------------------------------------------------------------------------
    */

    private function rutaValida(
        $ruta
    ) {

        return (

            is_string($ruta)

            &&

            preg_match(
                '/\A[a-z0-9]+(?:[\/_-][a-z0-9]+)*\z/',
                $ruta
            )

            &&

            strlen($ruta) <= 120
        );
    }


    /*
    |--------------------------------------------------------------------------
    | RESPUESTA DE ERROR
    |--------------------------------------------------------------------------
    */

    private function responderError(
        $codigo,
        $mensaje
    ) {

        http_response_code(
            $codigo
        );


        header(
            'Content-Type: text/html; charset=UTF-8'
        );


        echo '<!DOCTYPE html>';

        echo '<html lang="es">';

        echo '<head>';

        echo '<meta charset="UTF-8">';

        echo '<title>';
        echo 'Solicitud no disponible - SIGENMUNI';
        echo '</title>';

        echo '</head>';

        echo '<body>';

        echo '<h1>';
        echo (int)$codigo;
        echo '</h1>';

        echo '<p>';

        echo htmlspecialchars(
            $mensaje,
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8'
        );

        echo '</p>';

        echo '</body>';

        echo '</html>';
    }
}