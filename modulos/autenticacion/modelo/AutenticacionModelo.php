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

    public function buscarUsuarioPorNombre($nombreUsuario)
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
                "
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
        $stmt =
            $this->conexion->prepare(
                "
                UPDATE usuario
                SET
                    codigo_recuperacion = ?,
                    codigo_expira = ?
                WHERE id = ?
                "
            );

        if (!$stmt) {
            throw new Exception(
                'No se pudo preparar el código de recuperación: '
                . $this->conexion->error
            );
        }

        $stmt->bind_param(
            'ssi',
            $codigo,
            $expira,
            $usuarioId
        );

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();

            throw new Exception(
                'No se pudo guardar el código de recuperación: '
                . $error
            );
        }

        $stmt->close();

        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | BUSCAR ADMINISTRADOR POR CORREO PARA VALIDAR EL CÓDIGO
    |--------------------------------------------------------------------------
    */

    public function buscarAdministradorPorCorreo($correo)
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
                "
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

    public function actualizarAcceso($usuarioId, $nuevoUsuario, $claveHash)
    {
        $stmt =
            $this->conexion->prepare(
                "
                UPDATE usuario
                SET
                    nombre_usuario = ?,
                    clave = ?,
                    codigo_recuperacion = NULL,
                    codigo_expira = NULL
                WHERE id = ?
                "
            );

        if (!$stmt) {
            throw new Exception(
                'No se pudo preparar la actualización del acceso: '
                . $this->conexion->error
            );
        }

        $stmt->bind_param(
            'ssi',
            $nuevoUsuario,
            $claveHash,
            $usuarioId
        );

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();

            throw new Exception(
                'No se pudo actualizar el acceso: '
                . $error
            );
        }

        $stmt->close();

        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | INVALIDAR CÓDIGO DE RECUPERACIÓN
    |--------------------------------------------------------------------------
    */

    public function limpiarCodigoRecuperacion($usuarioId)
    {
        $stmt =
            $this->conexion->prepare(
                "
                UPDATE usuario
                SET
                    codigo_recuperacion = NULL,
                    codigo_expira = NULL
                WHERE id = ?
                "
            );

        if (!$stmt) {
            throw new Exception(
                'No se pudo preparar la invalidación del código: '
                . $this->conexion->error
            );
        }

        $stmt->bind_param(
            'i',
            $usuarioId
        );

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();

            throw new Exception(
                'No se pudo invalidar el código de recuperación: '
                . $error
            );
        }

        $stmt->close();

        return true;
    }
}
