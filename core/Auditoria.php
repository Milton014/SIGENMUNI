<?php

/*
|--------------------------------------------------------------------------
| AUDITORÍA DEL SISTEMA - SIGENMUNI
|--------------------------------------------------------------------------
|
| Registra acciones relevantes realizadas por los usuarios del sistema.
|
| Ejemplos de acciones:
| - ALTA
| - EDICION
| - ACTIVACION
| - INACTIVACION
| - PROCESAR
| - ANULAR
|
| IMPORTANTE:
| - Nunca registra contraseñas, hashes ni códigos de recuperación.
| - Si no puede obtener el ID del usuario desde la sesión, intenta
|   resolverlo mediante el nombre de usuario.
|
|--------------------------------------------------------------------------
*/

class Auditoria
{
    /*
    |--------------------------------------------------------------------------
    | REGISTRAR EVENTO
    |--------------------------------------------------------------------------
    |
    | Uso:
    |
    | Auditoria::registrar(
    |     $conexion,
    |     [
    |         'modulo' => 'Gestión de Empleados',
    |         'accion' => 'ALTA',
    |         'entidad' => 'EMPLEADO',
    |         'entidad_id' => 15,
    |         'entidad_descripcion' => 'Pérez Juan - Legajo 145',
    |         'detalle' => 'Se dio de alta un nuevo empleado.',
    |         'datos_nuevos' => $datosEmpleado
    |     ]
    | );
    |
    |--------------------------------------------------------------------------
    */

    public static function registrar(
        $conexion,
        array $evento
    ) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }


        /*
        |--------------------------------------------------------------------------
        | DATOS OBLIGATORIOS DEL EVENTO
        |--------------------------------------------------------------------------
        */

        $modulo =
            trim(
                (string)(
                    $evento['modulo']
                    ?? ''
                )
            );


        $accion =
            strtoupper(
                trim(
                    (string)(
                        $evento['accion']
                        ?? ''
                    )
                )
            );


        $entidad =
            strtoupper(
                trim(
                    (string)(
                        $evento['entidad']
                        ?? ''
                    )
                )
            );


        if (
            $modulo === ''
            ||
            $accion === ''
            ||
            $entidad === ''
        ) {

            error_log(
                'SIGENMUNI Auditoría: faltan datos obligatorios del evento.'
            );

            return false;
        }


        /*
        |--------------------------------------------------------------------------
        | DATOS DEL REGISTRO AFECTADO
        |--------------------------------------------------------------------------
        */

        $entidadId =
            isset(
                $evento['entidad_id']
            )
            &&
            $evento['entidad_id'] !== ''
                ?
                (int)$evento['entidad_id']
                :
                null;


        $entidadDescripcion =
            self::normalizarTextoNullable(
                $evento['entidad_descripcion']
                ?? null
            );


        $detalle =
            self::normalizarTextoNullable(
                $evento['detalle']
                ?? null
            );


        /*
        |--------------------------------------------------------------------------
        | USUARIO LOGUEADO
        |--------------------------------------------------------------------------
        */

        $usuarioId =
            isset(
                $_SESSION['id_usuario']
            )
            ?
            (int)$_SESSION['id_usuario']
            :
            0;


        $usuarioLogin =
            trim(
                (string)(
                    $_SESSION['usuario']
                    ?? ''
                )
            );


        $usuarioNombre =
            trim(
                (string)(
                    $_SESSION['nombre_completo']
                    ?? ''
                )
            );


        $rol =
            trim(
                (string)(
                    $_SESSION['rol']
                    ?? ''
                )
            );


        /*
        |--------------------------------------------------------------------------
        | COMPLETAR DATOS DEL USUARIO DESDE MYSQL
        |--------------------------------------------------------------------------
        |
        | Esto permite que la auditoría siga funcionando aunque una sesión
        | antigua no tenga id_usuario o nombre_completo.
        |
        |--------------------------------------------------------------------------
        */

        try {

            $datosUsuario =
                self::obtenerDatosUsuario(
                    $conexion,
                    $usuarioId,
                    $usuarioLogin
                );


            if ($datosUsuario) {

                if ($usuarioId <= 0) {

                    $usuarioId =
                        (int)(
                            $datosUsuario['id']
                            ?? 0
                        );
                }


                if ($usuarioLogin === '') {

                    $usuarioLogin =
                        trim(
                            (string)(
                                $datosUsuario['nombre_usuario']
                                ?? ''
                            )
                        );
                }


                if ($usuarioNombre === '') {

                    $nombre =
                        trim(
                            (string)(
                                $datosUsuario['nombre']
                                ?? ''
                            )
                        );


                    $apellido =
                        trim(
                            (string)(
                                $datosUsuario['apellido']
                                ?? ''
                            )
                        );


                    $usuarioNombre =
                        trim(
                            $nombre
                            .
                            ' '
                            .
                            $apellido
                        );
                }


                if ($rol === '') {

                    $rol =
                        trim(
                            (string)(
                                $datosUsuario['rol_nombre']
                                ??
                                $datosUsuario['rol']
                                ??
                                ''
                            )
                        );
                }
            }

        } catch (Throwable $e) {

            /*
            |--------------------------------------------------------------------------
            | NO INTERRUMPIR LA OPERACIÓN PRINCIPAL
            |--------------------------------------------------------------------------
            */

            error_log(
                'SIGENMUNI Auditoría - error al resolver usuario: '
                .
                $e->getMessage()
            );
        }


        /*
        |--------------------------------------------------------------------------
        | VALORES MÍNIMOS PARA COLUMNAS NOT NULL
        |--------------------------------------------------------------------------
        */

        if ($usuarioLogin === '') {

            $usuarioLogin =
                'DESCONOCIDO';
        }


        if ($usuarioNombre === '') {

            $usuarioNombre =
                $usuarioLogin;
        }


        /*
        |--------------------------------------------------------------------------
        | DATOS ANTERIORES / NUEVOS
        |--------------------------------------------------------------------------
        */

        $datosAnteriores =
            self::prepararDatos(
                $evento['datos_anteriores']
                ?? null
            );


        $datosNuevos =
            self::prepararDatos(
                $evento['datos_nuevos']
                ?? null
            );


        /*
        |--------------------------------------------------------------------------
        | DIRECCIÓN IP
        |--------------------------------------------------------------------------
        */

        $ip =
            self::obtenerIp();


        /*
        |--------------------------------------------------------------------------
        | INSERTAR AUDITORÍA
        |--------------------------------------------------------------------------
        */

        $sql = "
            INSERT INTO auditoria
            (
                usuario_id,
                usuario_login,
                usuario_nombre,
                rol,
                modulo,
                accion,
                entidad,
                entidad_id,
                entidad_descripcion,
                detalle,
                datos_anteriores,
                datos_nuevos,
                ip
            )
            VALUES
            (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
            )
        ";


        $stmt =
            $conexion->prepare(
                $sql
            );


        if (!$stmt) {

            error_log(
                'SIGENMUNI Auditoría - error al preparar INSERT: '
                .
                $conexion->error
            );

            return false;
        }


        /*
        |--------------------------------------------------------------------------
        | MYSQLI PERMITE NULL EN LOS PARÁMETROS
        |--------------------------------------------------------------------------
        */

        $usuarioIdBind =
            $usuarioId > 0
                ?
                $usuarioId
                :
                null;


        $entidadIdBind =
            $entidadId !== null
                &&
                $entidadId > 0
                ?
                $entidadId
                :
                null;


        $stmt->bind_param(
            "issssssisssss",
            $usuarioIdBind,
            $usuarioLogin,
            $usuarioNombre,
            $rol,
            $modulo,
            $accion,
            $entidad,
            $entidadIdBind,
            $entidadDescripcion,
            $detalle,
            $datosAnteriores,
            $datosNuevos,
            $ip
        );


        if (!$stmt->execute()) {

            $error =
                $stmt->error;


            $stmt->close();


            error_log(
                'SIGENMUNI Auditoría - error al registrar evento: '
                .
                $error
            );


            return false;
        }


        $stmt->close();


        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | OBTENER DATOS DEL USUARIO
    |--------------------------------------------------------------------------
    */

    private static function obtenerDatosUsuario(
        $conexion,
        $usuarioId,
        $usuarioLogin
    ) {
        if ($usuarioId > 0) {

            $stmt =
                $conexion->prepare(
                    "
                    SELECT
                        u.id,
                        u.nombre,
                        u.apellido,
                        u.nombre_usuario,
                        u.rol,
                        r.nombre AS rol_nombre
                    FROM usuario u
                    LEFT JOIN rol r
                        ON r.id = u.rol_id
                    WHERE u.id = ?
                    LIMIT 1
                    "
                );


            if (!$stmt) {

                return null;
            }


            $stmt->bind_param(
                "i",
                $usuarioId
            );

        } elseif ($usuarioLogin !== '') {

            $stmt =
                $conexion->prepare(
                    "
                    SELECT
                        u.id,
                        u.nombre,
                        u.apellido,
                        u.nombre_usuario,
                        u.rol,
                        r.nombre AS rol_nombre
                    FROM usuario u
                    LEFT JOIN rol r
                        ON r.id = u.rol_id
                    WHERE u.nombre_usuario = ?
                    LIMIT 1
                    "
                );


            if (!$stmt) {

                return null;
            }


            $stmt->bind_param(
                "s",
                $usuarioLogin
            );

        } else {

            return null;
        }


        if (!$stmt->execute()) {

            $stmt->close();

            return null;
        }


        $resultado =
            $stmt->get_result();


        $datos =
            $resultado
            ?
            $resultado->fetch_assoc()
            :
            null;


        $stmt->close();


        return $datos;
    }


    /*
    |--------------------------------------------------------------------------
    | PREPARAR DATOS JSON
    |--------------------------------------------------------------------------
    */

    private static function prepararDatos($datos)
    {
        if ($datos === null || $datos === '') {

            return null;
        }


        if (is_string($datos)) {

            return $datos;
        }


        $datosLimpios =
            self::limpiarDatosSensibles(
                $datos
            );


        $json =
            json_encode(
                $datosLimpios,
                JSON_UNESCAPED_UNICODE
                |
                JSON_UNESCAPED_SLASHES
            );


        return $json !== false
            ?
            $json
            :
            null;
    }


    /*
    |--------------------------------------------------------------------------
    | ELIMINAR INFORMACIÓN SENSIBLE
    |--------------------------------------------------------------------------
    */

    private static function limpiarDatosSensibles($datos)
    {
        if (!is_array($datos)) {

            return $datos;
        }


        $clavesSensibles = [
            'clave',
            'password',
            'contrasena',
            'contraseña',
            'clave_hash',
            'password_hash',
            'codigo_recuperacion',
            'codigo_expira',
            'token',
            'token_recuperacion'
        ];


        $resultado = [];


        foreach (
            $datos as $clave => $valor
        ) {

            $claveNormalizada =
                strtolower(
                    trim(
                        (string)$clave
                    )
                );


            if (
                in_array(
                    $claveNormalizada,
                    $clavesSensibles,
                    true
                )
            ) {

                continue;
            }


            if (is_array($valor)) {

                $valor =
                    self::limpiarDatosSensibles(
                        $valor
                    );
            }


            $resultado[$clave] =
                $valor;
        }


        return $resultado;
    }


    /*
    |--------------------------------------------------------------------------
    | OBTENER IP
    |--------------------------------------------------------------------------
    */

    private static function obtenerIp()
    {
        $ip =
            trim(
                (string)(
                    $_SERVER['REMOTE_ADDR']
                    ?? ''
                )
            );


        if ($ip === '') {

            return null;
        }


        return substr(
            $ip,
            0,
            45
        );
    }


    /*
    |--------------------------------------------------------------------------
    | TEXTO NULLABLE
    |--------------------------------------------------------------------------
    */

    private static function normalizarTextoNullable($valor)
    {
        if ($valor === null) {

            return null;
        }


        $texto =
            trim(
                (string)$valor
            );


        return $texto !== ''
            ?
            $texto
            :
            null;
    }
}
