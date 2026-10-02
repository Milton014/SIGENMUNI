<?php

/*
|--------------------------------------------------------------------------
| MODELO - AUTENTICACIÓN
|--------------------------------------------------------------------------
*/

class AutenticacionModelo
{
    private $conexion;


    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }


    /*
    |--------------------------------------------------------------------------
    | CONTAR USUARIOS
    |--------------------------------------------------------------------------
    */

    public function contarUsuarios()
    {
        $resultado =
            $this->conexion->query(
                "
                SELECT COUNT(*) AS total
                FROM usuario
                "
            );

        if (!$resultado) {
            throw new Exception(
                'Error al consultar la tabla usuario: '
                . $this->conexion->error
            );
        }

        $fila =
            $resultado->fetch_assoc();

        return (int)(
            $fila['total']
            ?? 0
        );
    }


    /*
    |--------------------------------------------------------------------------
    | BUSCAR USUARIO PARA LOGIN
    |--------------------------------------------------------------------------
    */

    /** Serializa los intentos de una cuenta entre navegadores y solicitudes. */
    public function autenticarUsuario(string $nombre, string $clave): array
    {
        if (!$this->conexion->begin_transaction()) throw new RuntimeException('No se pudo verificar el acceso.');
        try {
            $u = $this->buscarUsuarioPorNombre($nombre, true);
            $mensaje = 'Usuario o contraseña incorrectos.';
            $autorizado = null;
            $ahora = time();
            $bloqueadoHasta = $u && !empty($u['login_bloqueado_hasta'])
                ? strtotime($u['login_bloqueado_hasta']) : 0;
            if ($u && $bloqueadoHasta > $ahora) {
                $minutos = (int)ceil(($bloqueadoHasta - $ahora) / 60);
                $mensaje = 'Por seguridad, el acceso está bloqueado temporalmente. Intentá nuevamente en '
                    . $minutos . ($minutos === 1 ? ' minuto.' : ' minutos.');
            } elseif ($u && !password_verify($clave, $u['clave'])) {
                // El vencimiento abre un nuevo ciclo. Consultar durante el bloqueo no lo prolonga.
                $intentos = ($bloqueadoHasta ? 0 : (int)$u['login_intentos']) + 1;
                $hasta = $intentos >= 5 ? date('Y-m-d H:i:s', $ahora + 600) : null;
                $this->ejecutarRecuperacion(
                    'UPDATE usuario SET login_intentos = ?, login_bloqueado_hasta = ? WHERE id = ?',
                    'isi', [$intentos, $hasta, (int)$u['id']]
                );
                $restantes = 5 - $intentos;
                $mensaje = $intentos >= 5
                    ? 'Por seguridad, el acceso está bloqueado temporalmente. Intentá nuevamente en 10 minutos.'
                    : 'Usuario o contraseña incorrectos. Te '
                        . ($restantes === 1 ? 'queda 1 intento.' : 'quedan ' . $restantes . ' intentos.');
            } elseif ($u) {
                $this->ejecutarRecuperacion(
                    'UPDATE usuario SET login_intentos = 0, login_bloqueado_hasta = NULL WHERE id = ?',
                    'i', [(int)$u['id']]
                );
                $autorizado = $u;
                $mensaje = null;
            }
            if (!$this->conexion->commit()) throw new RuntimeException('No se pudo confirmar el acceso.');
            return ['usuario' => $autorizado, 'mensaje' => $mensaje];
        } catch (Throwable $e) {
            try { $this->conexion->rollback(); } catch (Throwable $ignorado) {}
            throw $e;
        }
    }

    public function buscarUsuarioPorNombre($nombreUsuario, bool $bloquear = false)
    {
        $stmt =
            $this->conexion->prepare(
                "
                SELECT
                    u.id,
                    u.nombre,
                    u.apellido,
                    u.nombre_usuario,
                    u.clave,
                    u.version_acceso,
                    u.login_intentos,
                    u.login_bloqueado_hasta,
                    u.rol_id,
                    u.activo,
                    r.nombre AS rol_nombre,
                    r.activo AS rol_activo,
                    r.es_admin
                FROM usuario u
                LEFT JOIN rol r
                    ON u.rol_id = r.id
                WHERE u.nombre_usuario = ?
                LIMIT 1
                " . ($bloquear ? ' FOR UPDATE' : '')
            );

        if (!$stmt) {
            throw new Exception(
                'Error al preparar la consulta de acceso: '
                . $this->conexion->error
            );
        }

        $stmt->bind_param(
            's',
            $nombreUsuario
        );

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();

            throw new Exception(
                'Error al ejecutar la consulta de acceso: '
                . $error
            );
        }

        $resultado =
            $stmt->get_result();

        $fila =
            $resultado->num_rows > 0
                ? $resultado->fetch_assoc()
                : null;

        $stmt->close();

        return $fila;
    }


    /*
    |--------------------------------------------------------------------------
    | BUSCAR ADMINISTRADOR PARA SOLICITAR RECUPERACIÓN
    |--------------------------------------------------------------------------
    |
    | La condición de administrador se resuelve mediante rol_id + tabla rol.
    | No se depende del campo histórico usuario.rol.
    |
    |--------------------------------------------------------------------------
    */

    public function buscarAdministradorRecuperacion($correo, $dni)
    {
        $stmt =
            $this->conexion->prepare(
                "
                SELECT
                    u.id,
                    u.nombre,
                    u.apellido,
                    u.email,
                    u.dni,
                    u.activo,
                    u.rol_id,
                    r.nombre AS rol_nombre,
                    r.es_admin,
                    r.activo AS rol_activo
                FROM usuario u
                INNER JOIN rol r
                    ON u.rol_id = r.id
                WHERE u.email = ?
                  AND u.dni = ?
                  AND u.activo = 1
                  AND r.activo = 1
                  AND (
                        r.es_admin = 1
                        OR UPPER(r.nombre) = 'ADMIN'
                      )
                LIMIT 1
                "
            );

        if (!$stmt) {
            throw new Exception(
                'No se pudo preparar la consulta del administrador: '
                . $this->conexion->error
            );
        }

        $stmt->bind_param(
            'ss',
            $correo,
            $dni
        );

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();

            throw new Exception(
                'No se pudo validar el administrador: '
                . $error
            );
        }

        $resultado =
            $stmt->get_result();

        $fila =
            $resultado->num_rows > 0
                ? $resultado->fetch_assoc()
                : null;

        $stmt->close();

        return $fila;
    }


    /*
    |--------------------------------------------------------------------------
    | GUARDAR CÓDIGO DE RECUPERACIÓN
    |--------------------------------------------------------------------------
    */

    public function guardarCodigoRecuperacion($usuarioId, $codigo, $expira)
    {
        // No se puede reiniciar el límite solicitando otro código dentro de la ventana.
        return $this->ejecutarRecuperacion(
            'UPDATE usuario SET codigo_recuperacion = ?, codigo_expira = ?, recuperacion_intentos = 0
             WHERE id = ? AND (codigo_expira IS NULL OR codigo_expira <= ?)',
            'ssis', [$codigo, $expira, $usuarioId, date('Y-m-d H:i:s')]
        ) === 1;
    }

    public function buscarAdministradorPorCorreo($correo, bool $bloquear = false)
    {
        $stmt =
            $this->conexion->prepare(
                "
                SELECT
                    u.id,
                    u.nombre,
                    u.apellido,
                    u.nombre_usuario,
                    u.email,
                    u.codigo_recuperacion,
                    u.codigo_expira,
                    u.recuperacion_intentos,
                    u.activo,
                    u.rol_id,
                    r.nombre AS rol_nombre,
                    r.es_admin,
                    r.activo AS rol_activo
                FROM usuario u
                INNER JOIN rol r
                    ON u.rol_id = r.id
                WHERE u.email = ?
                  AND u.activo = 1
                  AND r.activo = 1
                  AND (
                        r.es_admin = 1
                        OR UPPER(r.nombre) = 'ADMIN'
                      )
                LIMIT 1
                " . ($bloquear ? ' FOR UPDATE' : '')
            );

        if (!$stmt) {
            throw new Exception(
                'No se pudo preparar la consulta del usuario administrador: '
                . $this->conexion->error
            );
        }

        $stmt->bind_param(
            's',
            $correo
        );

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();

            throw new Exception(
                'No se pudo validar el usuario administrador: '
                . $error
            );
        }

        $resultado =
            $stmt->get_result();

        $fila =
            $resultado->num_rows > 0
                ? $resultado->fetch_assoc()
                : null;

        $stmt->close();

        return $fila;
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDAR NOMBRE DE USUARIO DUPLICADO
    |--------------------------------------------------------------------------
    */

    public function existeNombreUsuarioEnOtroUsuario($nombreUsuario, $usuarioId)
    {
        $stmt =
            $this->conexion->prepare(
                "
                SELECT id
                FROM usuario
                WHERE nombre_usuario = ?
                  AND id <> ?
                LIMIT 1
                "
            );

        if (!$stmt) {
            throw new Exception(
                'No se pudo validar el nombre de usuario: '
                . $this->conexion->error
            );
        }

        $stmt->bind_param(
            'si',
            $nombreUsuario,
            $usuarioId
        );

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();

            throw new Exception(
                'No se pudo validar el nombre de usuario: '
                . $error
            );
        }

        $resultado =
            $stmt->get_result();

        $existe =
            $resultado->num_rows > 0;

        $stmt->close();

        return $existe;
    }


    /*
    |--------------------------------------------------------------------------
    | ACTUALIZAR DATOS DE ACCESO
    |--------------------------------------------------------------------------
    */

    /** Comprueba y consume el código bajo el mismo bloqueo que cambia la contraseña. */
    public function recuperarAcceso($correo, $codigo, $nuevoUsuario, $claveHash): ?string
    {
        if (!$this->conexion->begin_transaction()) {
            throw new RuntimeException('No se pudo iniciar la recuperación.');
        }
        try {
            $u = $this->buscarAdministradorPorCorreo($correo, true);
            $error = null;
            if (!$u || empty($u['codigo_expira']) || $u['codigo_expira'] <= date('Y-m-d H:i:s')) {
                $error = 'El código no está activo o venció. Solicitá uno nuevo.';
            } elseif ((int)$u['recuperacion_intentos'] >= 5) {
                $error = 'Se alcanzó el límite de cinco intentos. Esperá a que termine el plazo de diez minutos antes de solicitar otro código.';
            } elseif (empty($u['codigo_recuperacion'])) {
                $error = 'El código no está activo. Solicitá uno nuevo.';
            } elseif (!hash_equals((string)$u['codigo_recuperacion'], (string)$codigo)) {
                $intentos = (int)$u['recuperacion_intentos'] + 1;
                $this->ejecutarRecuperacion(
                    'UPDATE usuario SET recuperacion_intentos = ? WHERE id = ?', 'ii', [$intentos, (int)$u['id']]
                );
                $error = $intentos >= 5
                    ? 'Se alcanzó el límite de cinco intentos. Esperá a que venza el código para solicitar otro.'
                    : 'Código incorrecto. Intentos restantes: ' . (5 - $intentos) . '.';
            } elseif ($this->existeNombreUsuarioEnOtroUsuario($nuevoUsuario, (int)$u['id'])) {
                $error = 'El nombre de usuario ya está registrado.';
            } else {
                $filas = $this->ejecutarRecuperacion(
                    'UPDATE usuario SET nombre_usuario = ?, clave = ?, version_acceso = version_acceso + 1, codigo_recuperacion = NULL,
                     codigo_expira = NULL, recuperacion_intentos = 0 WHERE id = ?',
                    'ssi', [$nuevoUsuario, $claveHash, (int)$u['id']]
                );
                if ($filas !== 1) throw new RuntimeException('No se pudo actualizar el acceso.');
            }
            // Los intentos incorrectos también deben persistir.
            if (!$this->conexion->commit()) throw new RuntimeException('No se pudo confirmar la recuperación.');
            return $error;
        } catch (Throwable $e) {
            try { $this->conexion->rollback(); } catch (Throwable $ignorado) {}
            throw $e;
        }
    }

    public function limpiarCodigoRecuperacion($usuarioId, $codigo)
    {
        // Un envío fallido no debe borrar un código posterior de otra solicitud.
        $this->ejecutarRecuperacion(
            'UPDATE usuario SET codigo_recuperacion = NULL, codigo_expira = NULL,
             recuperacion_intentos = 0 WHERE id = ? AND codigo_recuperacion = ?',
            'is', [$usuarioId, $codigo]
        );
    }

    private function ejecutarRecuperacion(string $sql, string $tipos, array $valores): int
    {
        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) throw new RuntimeException('No se pudo preparar la recuperación.');
        try {
            if (!$stmt->bind_param($tipos, ...$valores) || !$stmt->execute()) {
                throw new RuntimeException('No se pudo guardar la recuperación.');
            }
            return $stmt->affected_rows;
        } finally {
            $stmt->close();
        }
    }
}
