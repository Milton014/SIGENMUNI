<?php

require_once __DIR__
    . '/../modelo/AutenticacionModelo.php';

require_once __DIR__
    . '/../../../core/Url.php';

require_once __DIR__
    . '/../../../core/Csrf.php';


class AutenticacionControlador
{
    private $modelo;


    public function __construct($conexion)
    {
        $this->modelo =
            new AutenticacionModelo(
                $conexion
            );
    }


    /*
    |--------------------------------------------------------------------------
    | LOGIN
    |--------------------------------------------------------------------------
    */

    public function login()
    {
        $this->iniciarSesionSiHaceFalta();


        if (
            isset(
                $_SESSION['usuario']
            )
        ) {
            $this->redirigir(
                sigenmuniUrlRuta(
                    'inicio'
                )
            );
        }


        unset(
            $_SESSION['cambio_clave_usuario_id'],
            $_SESSION['cambio_clave_motivo']
        );


        $mensaje = '';
        $mensajeExito = '';
        $usuarioIngresado = '';


        $error =
            isset($_GET['error'])
            &&
            is_string($_GET['error'])
                ? $_GET['error']
                : '';


        switch ($error) {

            case 'sin_permiso':
                $mensaje =
                    'No tiene permisos para acceder a ese módulo.';
                break;

            case 'rol_inactivo':
                $mensaje =
                    'El rol asignado a su usuario está inactivo.';
                break;

            case 'rol_invalido':
                $mensaje =
                    'El usuario no tiene un rol válido asignado.';
                break;

            case 'usuario_inactivo':
                $mensaje =
                    'El usuario está inactivo. Comuníquese con el administrador.';
                break;
        }


        try {
            $existenUsuarios =
                $this->modelo->contarUsuarios()
                > 0;

        } catch (Exception $e) {
            die(
                htmlspecialchars(
                    $e->getMessage(),
                    ENT_QUOTES | ENT_SUBSTITUTE,
                    'UTF-8'
                )
            );
        }


        if (
            ($_SERVER['REQUEST_METHOD'] ?? 'GET')
            ===
            'POST'
        ) {

            if (
                isset($_POST['usuario'])
                &&
                !is_string($_POST['usuario'])
            ) {
                $mensaje =
                    'El usuario ingresado no tiene un formato válido.';

            } elseif (
                isset($_POST['contrasena'])
                &&
                !is_string($_POST['contrasena'])
            ) {
                $mensaje =
                    'La contraseña ingresada no tiene un formato válido.';

            } else {

                $usuarioIngresado =
                    trim(
                        $_POST['usuario']
                        ?? ''
                    );

                $pass =
                    $_POST['contrasena']
                    ?? '';


                if (
                    $usuarioIngresado === ''
                    ||
                    $pass === ''
                ) {
                    $mensaje =
                        'Debe completar usuario y contraseña.';

                } else {

                    try {
                        $fila =
                            $this->modelo
                                ->buscarUsuarioPorNombre(
                                    $usuarioIngresado
                                );

                    } catch (Exception $e) {
                        die(
                            htmlspecialchars(
                                $e->getMessage(),
                                ENT_QUOTES | ENT_SUBSTITUTE,
                                'UTF-8'
                            )
                        );
                    }


                    if (!$fila) {
                        $mensaje =
                            'Usuario o contraseña incorrectos.';

                    } elseif (
                        (int)$fila['activo']
                        !==
                        1
                    ) {
                        $mensaje =
                            'El usuario está inactivo. Comuníquese con el administrador.';

                    } elseif (
                        !password_verify(
                            $pass,
                            $fila['clave']
                        )
                    ) {
                        $mensaje =
                            'Usuario o contraseña incorrectos.';

                    } elseif (
                        empty($fila['rol_id'])
                        ||
                        empty($fila['rol_nombre'])
                    ) {
                        $mensaje =
                            'El usuario no tiene un rol válido asignado.';

                    } elseif (
                        (int)$fila['rol_activo']
                        !==
                        1
                    ) {
                        $mensaje =
                            'El rol asignado a este usuario está inactivo.';

                    } elseif (
                        !$this->claveCumplePolitica(
                            $pass
                        )
                    ) {
                        $mensaje =
                            'La contraseña no cumple con la política de seguridad vigente. '
                            . 'Solicite al administrador la asignación de una nueva contraseña.';

                    } else {

                        session_regenerate_id(
                            true
                        );

                        $_SESSION['id_usuario'] =
                            (int)$fila['id'];

                        $_SESSION['usuario'] =
                            $fila['nombre_usuario'];

                        $_SESSION['nombre_completo'] =
                            trim(
                                ($fila['nombre'] ?? '')
                                . ' '
                                . ($fila['apellido'] ?? '')
                            );

                        $_SESSION['rol'] =
                            $fila['rol_nombre'];

                        $_SESSION['rol_id'] =
                            (int)$fila['rol_id'];

                        $_SESSION['es_admin'] =
                            (int)$fila['es_admin'];

                        $_SESSION['sincronizar_local_storage'] =
                            true;

                        $this->redirigir(
                            sigenmuniUrlRuta(
                                'inicio'
                            )
                        );
                    }
                }
            }
        }


        $autenticacionUrlLogin =
            sigenmuniUrlRuta(
                'login'
            );

        $autenticacionUrlRecuperar =
            sigenmuniUrlRuta(
                'recuperar'
            );

        $autenticacionUrlEscudo =
            sigenmuniBaseUrl()
            . '/public/assets/img/escudo.jpg';


        require __DIR__
            . '/../vista/login.php';
    }


    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    */

    public function logout()
    {
        $this->iniciarSesionSiHaceFalta();


        $token =
            $_POST['_csrf']
            ?? '';


        if (
            !sigenmuniCsrfValido(
                $token
            )
        ) {

            http_response_code(
                403
            );


            if (
                !headers_sent()
            ) {

                header(
                    'Content-Type: text/html; charset=UTF-8'
                );
            }


            $urlInicio =
                sigenmuniUrlRuta(
                    'inicio'
                );


            echo '<!DOCTYPE html>';
            echo '<html lang="es">';
            echo '<head>';
            echo '<meta charset="UTF-8">';
            echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
            echo '<title>Solicitud no válida - SIGENMUNI</title>';
            echo '</head>';
            echo '<body>';
            echo '<h1>Solicitud no válida</h1>';
            echo '<p>La sesión del formulario no es válida o expiró.</p>';
            echo '<p><a href="';
            echo htmlspecialchars(
                $urlInicio,
                ENT_QUOTES | ENT_SUBSTITUTE,
                'UTF-8'
            );
            echo '">Volver al menú</a></p>';
            echo '</body>';
            echo '</html>';

            return;
        }


        $_SESSION = [];

        session_unset();


        if (
            ini_get(
                'session.use_cookies'
            )
        ) {

            $parametros =
                session_get_cookie_params();


            setcookie(
                session_name(),
                '',
                time() - 42000,
                $parametros['path'],
                $parametros['domain'],
                $parametros['secure'],
                $parametros['httponly']
            );
        }


        session_destroy();


        if (
            !headers_sent()
        ) {

            header(
                'Cache-Control: no-store, no-cache, must-revalidate, max-age=0'
            );

            header(
                'Pragma: no-cache'
            );
        }


        $autenticacionUrlLogin =
            sigenmuniUrlRuta(
                'login'
            );


        require __DIR__
            . '/../vista/logout.php';
    }


    /*
    |--------------------------------------------------------------------------
    | RECUPERAR ACCESO
    |--------------------------------------------------------------------------
    */

    public function recuperar()
    {
        $this->iniciarSesionSiHaceFalta();


        if (
            isset(
                $_SESSION['usuario']
            )
        ) {
            $this->redirigir(
                sigenmuniUrlRuta(
                    'inicio'
                )
            );
        }


        $mensaje = '';

        $correoIngresado =
            '';

        $dniIngresado =
            '';


        if (
            ($_SERVER['REQUEST_METHOD'] ?? 'GET')
            ===
            'POST'
        ) {

            if (
                !sigenmuniCsrfValido(
                    $_POST['_csrf']
                    ?? ''
                )
            ) {
                $mensaje =
                    'La sesión del formulario no es válida o expiró. Recargue la página e inténtelo nuevamente.';

            } elseif (
                isset($_POST['correo'])
                &&
                !is_string($_POST['correo'])
            ) {
                $mensaje =
                    'El correo ingresado no tiene un formato válido.';

            } elseif (
                isset($_POST['dni'])
                &&
                !is_string($_POST['dni'])
            ) {
                $mensaje =
                    'El DNI ingresado no tiene un formato válido.';

            } else {

                $correoIngresado =
                    trim(
                        $_POST['correo']
                        ?? ''
                    );

                $dniIngresado =
                    trim(
                        $_POST['dni']
                        ?? ''
                    );


                if (
                    $correoIngresado === ''
                    ||
                    $dniIngresado === ''
                ) {
                    $mensaje =
                        'Debe completar correo y DNI.';

                } elseif (
                    !filter_var(
                        $correoIngresado,
                        FILTER_VALIDATE_EMAIL
                    )
                ) {
                    $mensaje =
                        'El correo ingresado no es válido.';

                } elseif (
                    !ctype_digit(
                        $dniIngresado
                    )
                ) {
                    $mensaje =
                        'El DNI solo puede contener números.';

                } elseif (
                    strlen($dniIngresado) < 7
                    ||
                    strlen($dniIngresado) > 8
                ) {
                    $mensaje =
                        'El DNI debe tener entre 7 y 8 números.';

                } else {

                    try {
                        $usuario =
                            $this->modelo
                                ->buscarAdministradorRecuperacion(
                                    $correoIngresado,
                                    $dniIngresado
                                );

                        if (!$usuario) {
                            $mensaje =
                                'No se encontró un administrador activo con ese correo y DNI. '
                                . 'La recuperación solo está disponible para administradores.';

                        } else {

                            $codigo =
                                (string)random_int(
                                    100000,
                                    999999
                                );

                            $expira =
                                date(
                                    'Y-m-d H:i:s',
                                    strtotime(
                                        '+10 minutes'
                                    )
                                );


                            $this->modelo
                                ->guardarCodigoRecuperacion(
                                    (int)$usuario['id'],
                                    $codigo,
                                    $expira
                                );


                            try {
                                $this->enviarCodigoRecuperacion(
                                    $usuario,
                                    $codigo
                                );

                            } catch (Exception $e) {

                                try {
                                    $this->modelo
                                        ->limpiarCodigoRecuperacion(
                                            (int)$usuario['id']
                                        );
                                } catch (Exception $ignorado) {
                                    error_log(
                                        '[SIGENMUNI - recuperación] No se pudo invalidar el código luego de fallar el envío: '
                                        . $ignorado->getMessage()
                                    );
                                }


                                error_log(
                                    '[SIGENMUNI - recuperación] '
                                    . $e->getMessage()
                                );


                                throw new Exception(
                                    'No se pudo enviar el correo de recuperación. Revise la configuración SMTP.'
                                );
                            }


                            $this->redirigir(
                                sigenmuniUrlRuta(
                                    'verificar-codigo',
                                    [
                                        'correo' =>
                                            $correoIngresado
                                    ]
                                )
                            );
                        }

                    } catch (Exception $e) {
                        $mensaje =
                            $e->getMessage();
                    }
                }
            }
        }


        $autenticacionUrlRecuperar =
            sigenmuniUrlRuta(
                'recuperar'
            );

        $autenticacionUrlLogin =
            sigenmuniUrlRuta(
                'login'
            );

        $autenticacionUrlEscudo =
            sigenmuniBaseUrl()
            . '/public/assets/img/escudo.jpg';

        $autenticacionCsrfToken =
            sigenmuniCsrfToken();


        require __DIR__
            . '/../vista/recuperar.php';
    }


    /*
    |--------------------------------------------------------------------------
    | VERIFICAR CÓDIGO - FORMULARIO
    |--------------------------------------------------------------------------
    */

    public function verificarCodigo()
    {
        $this->iniciarSesionSiHaceFalta();


        if (
            isset(
                $_SESSION['usuario']
            )
        ) {
            $this->redirigir(
                sigenmuniUrlRuta(
                    'inicio'
                )
            );
        }


        $correo =
            isset($_GET['correo'])
            &&
            is_string($_GET['correo'])
                ? trim($_GET['correo'])
                : '';


        if (
            $correo === ''
            ||
            !filter_var(
                $correo,
                FILTER_VALIDATE_EMAIL
            )
        ) {
            $this->redirigir(
                sigenmuniUrlRuta(
                    'recuperar'
                )
            );
        }


        try {
            $usuario =
                $this->modelo
                    ->buscarAdministradorPorCorreo(
                        $correo
                    );

        } catch (Exception $e) {
            $usuario =
                null;
        }


        if (
            !$usuario
            ||
            empty($usuario['codigo_recuperacion'])
            ||
            empty($usuario['codigo_expira'])
        ) {
            $this->redirigir(
                sigenmuniUrlRuta(
                    'recuperar'
                )
            );
        }


        $autenticacionUrlActualizarAcceso =
            sigenmuniUrlRuta(
                'actualizar-acceso'
            );

        $autenticacionUrlLogin =
            sigenmuniUrlRuta(
                'login'
            );

        $autenticacionUrlEscudo =
            sigenmuniBaseUrl()
            . '/public/assets/img/escudo.jpg';

        $autenticacionCsrfToken =
            sigenmuniCsrfToken();


        require __DIR__
            . '/../vista/verificar_codigo.php';
    }


    /*
    |--------------------------------------------------------------------------
    | ACTUALIZAR ACCESO
    |--------------------------------------------------------------------------
    */

    public function actualizarAcceso()
    {
        $this->iniciarSesionSiHaceFalta();


        if (
            isset(
                $_SESSION['usuario']
            )
        ) {
            $this->redirigir(
                sigenmuniUrlRuta(
                    'inicio'
                )
            );
        }


        if (
            !sigenmuniCsrfValido(
                $_POST['_csrf']
                ?? ''
            )
        ) {
            $this->mostrarError(
                'La sesión del formulario no es válida o expiró. Solicitá un nuevo código.'
            );
        }


        foreach (
            [
                'correo',
                'codigo',
                'nuevo_usuario',
                'nueva_contra',
                'confirmar_contra'
            ]
            as $campo
        ) {
            if (
                isset($_POST[$campo])
                &&
                !is_string($_POST[$campo])
            ) {
                $this->mostrarError(
                    'Los datos recibidos no tienen un formato válido.'
                );
            }
        }


        $correo =
            trim(
                $_POST['correo']
                ?? ''
            );

        $codigo =
            trim(
                $_POST['codigo']
                ?? ''
            );

        $nuevoUsuario =
            trim(
                $_POST['nuevo_usuario']
                ?? ''
            );

        $nuevaContra =
            $_POST['nueva_contra']
            ?? '';

        $confirmarContra =
            $_POST['confirmar_contra']
            ?? '';


        if (
            $correo === ''
            ||
            $codigo === ''
            ||
            $nuevoUsuario === ''
            ||
            $nuevaContra === ''
            ||
            $confirmarContra === ''
        ) {
            $this->mostrarError(
                'Debe completar todos los campos.'
            );
        }


        if (
            !filter_var(
                $correo,
                FILTER_VALIDATE_EMAIL
            )
        ) {
            $this->mostrarError(
                'El correo ingresado no es válido.'
            );
        }


        if (
            !ctype_digit(
                $codigo
            )
            ||
            strlen($codigo) !== 6
        ) {
            $this->mostrarError(
                'El código debe contener exactamente 6 números.'
            );
        }


        $longitudUsuario =
            function_exists('mb_strlen')
                ? mb_strlen(
                    $nuevoUsuario,
                    'UTF-8'
                )
                : strlen(
                    $nuevoUsuario
                );


        if (
            $longitudUsuario
            >
            50
        ) {
            $this->mostrarError(
                'El nombre de usuario no puede superar los 50 caracteres.'
            );
        }


        if (
            $nuevaContra
            !==
            $confirmarContra
        ) {
            $this->mostrarError(
                'Las contraseñas no coinciden.'
            );
        }


        $errorClave =
            $this->validarPoliticaClave(
                $nuevaContra
            );


        if (
            $errorClave
            !==
            null
        ) {
            $this->mostrarError(
                $errorClave
            );
        }


        try {
            $usuario =
                $this->modelo
                    ->buscarAdministradorPorCorreo(
                        $correo
                    );

        } catch (Exception $e) {
            $this->mostrarError(
                'No se pudo validar el usuario administrador.'
            );
        }


        if (!$usuario) {
            $this->mostrarError(
                'No se encontró un administrador activo para actualizar el acceso.'
            );
        }


        $usuarioId =
            (int)$usuario['id'];


        if (
            empty($usuario['codigo_recuperacion'])
            ||
            empty($usuario['codigo_expira'])
        ) {
            $this->mostrarError(
                'No hay un código de recuperación activo. Solicitá uno nuevo.'
            );
        }


        if (
            !hash_equals(
                (string)$usuario['codigo_recuperacion'],
                (string)$codigo
            )
        ) {
            $this->mostrarError(
                'Código incorrecto.'
            );
        }


        $ahora =
            date(
                'Y-m-d H:i:s'
            );


        if (
            $usuario['codigo_expira']
            <
            $ahora
        ) {

            try {
                $this->modelo
                    ->limpiarCodigoRecuperacion(
                        $usuarioId
                    );
            } catch (Exception $ignorado) {
                error_log(
                    '[SIGENMUNI - recuperación] No se pudo limpiar un código vencido: '
                    . $ignorado->getMessage()
                );
            }


            $this->mostrarError(
                'El código venció. Solicitá uno nuevo.'
            );
        }


        try {
            if (
                $this->modelo
                    ->existeNombreUsuarioEnOtroUsuario(
                        $nuevoUsuario,
                        $usuarioId
                    )
            ) {
                $this->mostrarError(
                    'El nombre de usuario ya está registrado.'
                );
            }

        } catch (Exception $e) {
            $this->mostrarError(
                'No se pudo validar el nombre de usuario.'
            );
        }


        $claveHash =
            password_hash(
                $nuevaContra,
                PASSWORD_DEFAULT
            );


        if (
            $claveHash
            ===
            false
        ) {
            $this->mostrarError(
                'No se pudo generar la nueva contraseña.'
            );
        }


        try {
            $this->modelo
                ->actualizarAcceso(
                    $usuarioId,
                    $nuevoUsuario,
                    $claveHash
                );

        } catch (Exception $e) {
            $this->mostrarError(
                'Error al actualizar los datos de acceso.'
            );
        }


        $autenticacionUrlLogin =
            sigenmuniUrlRuta(
                'login'
            );


        require __DIR__
            . '/../vista/acceso_actualizado.php';
    }


    /*
    |--------------------------------------------------------------------------
    | ENVIAR CÓDIGO POR CORREO
    |--------------------------------------------------------------------------
    */

    private function enviarCodigoRecuperacion($usuario, $codigo)
    {
        /*
        |--------------------------------------------------------------------------
        | CARGAR PHPMAILER
        |--------------------------------------------------------------------------
        |
        | La librería se encuentra centralizada en:
        |
        | lib/phpmailer/
        |
        |--------------------------------------------------------------------------
        */

        $directorioPhpMailer =
            __DIR__
            . '/../../../lib/phpmailer/src';


        $archivoException =
            $directorioPhpMailer
            . '/Exception.php';

        $archivoPhpMailer =
            $directorioPhpMailer
            . '/PHPMailer.php';

        $archivoSmtp =
            $directorioPhpMailer
            . '/SMTP.php';


        if (
            !is_file($archivoException)
            ||
            !is_file($archivoPhpMailer)
            ||
            !is_file($archivoSmtp)
        ) {
            throw new Exception(
                'No se encontraron todos los archivos requeridos de PHPMailer.'
            );
        }


        require_once $archivoException;
        require_once $archivoPhpMailer;
        require_once $archivoSmtp;


        $config =
            $this->obtenerConfiguracionCorreo();


        $mail =
            new \PHPMailer\PHPMailer\PHPMailer(
                true
            );


        try {
            $mail->isSMTP();

            $mail->Host =
                $config['host'];

            $mail->SMTPAuth =
                true;

            $mail->Username =
                $config['usuario'];

            $mail->Password =
                $config['clave'];

            $mail->SMTPSecure =
                \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;

            $mail->Port =
                $config['puerto'];

            $mail->CharSet =
                'UTF-8';


            $mail->setFrom(
                $config['remitente_email'],
                $config['remitente_nombre']
            );


            $nombreCompleto =
                trim(
                    ($usuario['nombre'] ?? '')
                    . ' '
                    . ($usuario['apellido'] ?? '')
                );


            $mail->addAddress(
                $usuario['email'],
                $nombreCompleto
            );


            $mail->isHTML(
                true
            );

            $mail->Subject =
                'Código de recuperación - SIGENMUNI';


            $nombreSeguro =
                htmlspecialchars(
                    $nombreCompleto,
                    ENT_QUOTES | ENT_SUBSTITUTE,
                    'UTF-8'
                );

            $codigoSeguro =
                htmlspecialchars(
                    (string)$codigo,
                    ENT_QUOTES | ENT_SUBSTITUTE,
                    'UTF-8'
                );


            $mail->Body =
                '<h2>SIGENMUNI</h2>'
                . '<p>Hola <b>' . $nombreSeguro . '</b>,</p>'
                . '<p>Tu código de recuperación es:</p>'
                . '<h1 style="color:#0f766e;">' . $codigoSeguro . '</h1>'
                . '<p>Este código vence en 10 minutos.</p>'
                . '<p>Si no solicitaste esta recuperación, ignorá este mensaje.</p>';


            $mail->AltBody =
                'SIGENMUNI - Tu código de recuperación es: '
                . $codigo
                . '. Vence en 10 minutos.';


            $mail->send();

        } catch (\PHPMailer\PHPMailer\Exception $e) {
            throw new Exception(
                'No se pudo enviar el correo de recuperación.'
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | CONFIGURACIÓN SMTP
    |--------------------------------------------------------------------------
    */

    private function obtenerConfiguracionCorreo()
    {
        $archivoConfig =
            __DIR__
            . '/../../../config/config_correo.php';


        if (
            file_exists(
                $archivoConfig
            )
        ) {
            require_once $archivoConfig;
        }


        $host =
            defined('SMTP_HOST')
                ? SMTP_HOST
                : (
                    getenv('SIGENMUNI_SMTP_HOST')
                    ?: 'smtp.gmail.com'
                );

        $puerto =
            defined('SMTP_PORT')
                ? (int)SMTP_PORT
                : (int)(
                    getenv('SIGENMUNI_SMTP_PORT')
                    ?: 587
                );

        $usuario =
            defined('SMTP_USER')
                ? SMTP_USER
                : (
                    getenv('SIGENMUNI_SMTP_USER')
                    ?: ''
                );

        $clave =
            defined('SMTP_PASS')
                ? SMTP_PASS
                : (
                    getenv('SIGENMUNI_SMTP_PASS')
                    ?: ''
                );

        $remitenteEmail =
            defined('SMTP_FROM')
                ? SMTP_FROM
                : (
                    getenv('SIGENMUNI_SMTP_FROM')
                    ?: $usuario
                );

        $remitenteNombre =
            defined('SMTP_FROM_NAME')
                ? SMTP_FROM_NAME
                : (
                    getenv('SIGENMUNI_SMTP_FROM_NAME')
                    ?: 'SIGENMUNI'
                );


        if (
            trim(
                (string)$usuario
            ) === ''
            ||
            trim(
                (string)$clave
            ) === ''
        ) {
            throw new Exception(
                'La configuración SMTP no está completa.'
            );
        }


        if (
            !filter_var(
                $remitenteEmail,
                FILTER_VALIDATE_EMAIL
            )
        ) {
            throw new Exception(
                'El correo remitente configurado no es válido.'
            );
        }


        return [
            'host' =>
                $host,

            'puerto' =>
                $puerto,

            'usuario' =>
                $usuario,

            'clave' =>
                $clave,

            'remitente_email' =>
                $remitenteEmail,

            'remitente_nombre' =>
                $remitenteNombre
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | POLÍTICA DE CONTRASEÑA
    |--------------------------------------------------------------------------
    */

    private function claveCumplePolitica($clave)
    {
        return
            $this->validarPoliticaClave(
                $clave
            )
            ===
            null;
    }


    private function validarPoliticaClave($clave)
    {
        $longitud =
            function_exists('mb_strlen')
                ? mb_strlen(
                    $clave,
                    'UTF-8'
                )
                : strlen(
                    $clave
                );


        if (
            $longitud
            <
            8
        ) {
            return
                'La contraseña debe tener al menos 8 caracteres.';
        }


        if (
            !preg_match(
                '/\p{Lu}/u',
                $clave
            )
        ) {
            return
                'La contraseña debe contener al menos una letra mayúscula.';
        }


        if (
            !preg_match(
                '/[^\p{L}\p{N}\s]/u',
                $clave
            )
        ) {
            return
                'La contraseña debe contener al menos un carácter especial, por ejemplo: @, #, $, %, !.';
        }


        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | VISTA DE ERROR
    |--------------------------------------------------------------------------
    */

    private function mostrarError($mensaje)
    {
        $autenticacionUrlVolver =
            sigenmuniUrlRuta(
                'recuperar'
            );

        $autenticacionUrlLogin =
            sigenmuniUrlRuta(
                'login'
            );


        require __DIR__
            . '/../vista/error.php';

        exit();
    }


    private function iniciarSesionSiHaceFalta()
    {
        if (
            session_status()
            ===
            PHP_SESSION_NONE
        ) {
            session_start();
        }
    }


    private function redirigir($url)
    {
        header(
            'Location: '
            . $url
        );

        exit();
    }
}
