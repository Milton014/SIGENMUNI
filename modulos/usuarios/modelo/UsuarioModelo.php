<?php

class UsuarioModelo
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function listar()
    {
        $sql = "
            SELECT
                u.id,
                u.nombre,
                u.apellido,
                u.dni,
                u.nombre_usuario,
                u.email,
                u.rol,
                u.rol_id,
                u.activo,
                r.nombre AS rol_nombre,
                r.activo AS rol_activo,
                r.es_admin
            FROM usuario u
            LEFT JOIN rol r ON u.rol_id = r.id
            ORDER BY u.id DESC
        ";

        $resultado = $this->conexion->query($sql);

        if (!$resultado) {
            throw new Exception(
                "Error al consultar los usuarios: "
                . $this->conexion->error
            );
        }

        $usuarios = [];

        while ($fila = $resultado->fetch_assoc()) {
            $usuarios[] = [
                'id' => (int)$fila['id'],
                'nombre' => $fila['nombre'],
                'apellido' => $fila['apellido'],
                'dni' => $fila['dni'],
                'nombre_usuario' => $fila['nombre_usuario'],
                'email' => $fila['email'],
                'rol' => $fila['rol'],
                'rol_id' => $fila['rol_id'] !== null ? (int)$fila['rol_id'] : null,
                'activo' => (int)$fila['activo'],
                'rol_nombre' => $fila['rol_nombre'],
                'rol_activo' => $fila['rol_activo'] !== null ? (int)$fila['rol_activo'] : null,
                'es_admin' => $fila['es_admin'] !== null ? (int)$fila['es_admin'] : 0
            ];
        }

        return $usuarios;
    }

    public function obtenerPorId($id)
    {
        $sql = "
            SELECT
                u.id,
                u.nombre,
                u.apellido,
                u.dni,
                u.nombre_usuario,
                u.email,
                u.clave,
                u.rol,
                u.rol_id,
                u.activo,
                r.nombre AS rol_nombre,
                r.activo AS rol_activo,
                r.es_admin
            FROM usuario u
            LEFT JOIN rol r ON u.rol_id = r.id
            WHERE u.id = ?
            LIMIT 1
        ";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                "Error al preparar la consulta del usuario: "
                . $this->conexion->error
            );
        }

        $stmt->bind_param("i", $id);

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();
            throw new Exception(
                "Error al consultar el usuario: "
                . $error
            );
        }

        $resultado = $stmt->get_result();
        $usuario = $resultado->fetch_assoc();
        $stmt->close();

        return $usuario;
    }

    public function obtenerRolesActivos()
    {
        $sql = "
            SELECT
                id,
                nombre,
                descripcion,
                activo,
                es_admin,
                created_at
            FROM rol
            WHERE activo = 1
            ORDER BY nombre ASC
        ";

        $resultado = $this->conexion->query($sql);

        if (!$resultado) {
            throw new Exception(
                "Error al consultar los roles activos: "
                . $this->conexion->error
            );
        }

        $roles = [];

        while ($fila = $resultado->fetch_assoc()) {
            $roles[] = [
                'id' => (int)$fila['id'],
                'nombre' => $fila['nombre'],
                'descripcion' => $fila['descripcion'],
                'activo' => (int)$fila['activo'],
                'es_admin' => (int)$fila['es_admin'],
                'created_at' => $fila['created_at']
            ];
        }

        return $roles;
    }

    public function listarRoles()
    {
        $sql = "
            SELECT
                id,
                nombre,
                descripcion,
                activo,
                es_admin,
                created_at
            FROM rol
            ORDER BY nombre ASC
        ";

        $resultado = $this->conexion->query($sql);

        if (!$resultado) {
            throw new Exception(
                "Error al consultar los roles: "
                . $this->conexion->error
            );
        }

        $roles = [];

        while ($fila = $resultado->fetch_assoc()) {
            $roles[] = [
                'id' => (int)$fila['id'],
                'nombre' => $fila['nombre'],
                'descripcion' => $fila['descripcion'],
                'activo' => (int)$fila['activo'],
                'es_admin' => (int)$fila['es_admin'],
                'created_at' => $fila['created_at']
            ];
        }

        return $roles;
    }

    public function obtenerRolPorId($rolId)
    {
        $sql = "
            SELECT
                id,
                nombre,
                descripcion,
                activo,
                es_admin,
                created_at
            FROM rol
            WHERE id = ?
            LIMIT 1
        ";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                "Error al preparar la consulta del rol: "
                . $this->conexion->error
            );
        }

        $stmt->bind_param("i", $rolId);

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();
            throw new Exception(
                "Error al consultar el rol: "
                . $error
            );
        }

        $resultado = $stmt->get_result();
        $rol = $resultado->fetch_assoc();
        $stmt->close();

        return $rol;
    }

    public function existeNombreRol($nombre, $idExcluir = null)
    {
        $nombre = strtoupper(trim((string)$nombre));

        $sql = "
            SELECT id
            FROM rol
            WHERE UPPER(nombre) = ?
        ";

        if ($idExcluir !== null) {
            $sql .= " AND id <> ? ";
        }

        $sql .= " LIMIT 1 ";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                "Error al preparar la validación del rol: "
                . $this->conexion->error
            );
        }

        if ($idExcluir === null) {
            $stmt->bind_param("s", $nombre);
        } else {
            $stmt->bind_param("si", $nombre, $idExcluir);
        }

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();
            throw new Exception(
                "Error al validar el nombre del rol: "
                . $error
            );
        }

        $resultado = $stmt->get_result();
        $existe = $resultado->num_rows > 0;
        $stmt->close();

        return $existe;
    }

    public function guardarRol($datos)
    {
        $sql = "
            INSERT INTO rol (
                nombre,
                descripcion,
                activo,
                es_admin
            )
            VALUES (?, ?, ?, ?)
        ";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                "Error al preparar el alta del rol: "
                . $this->conexion->error
            );
        }

        $nombre = strtoupper(trim((string)$datos['nombre']));
        $descripcion = $datos['descripcion'];
        $activo = (int)$datos['activo'];
        $esAdmin = (int)$datos['es_admin'];

        $stmt->bind_param(
            "ssii",
            $nombre,
            $descripcion,
            $activo,
            $esAdmin
        );

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();
            throw new Exception(
                "Error al guardar el rol: "
                . $error
            );
        }

        $idRol = $stmt->insert_id;
        $stmt->close();

        return $idRol;
    }

    public function actualizarRol($rolId, $datos)
    {
        $sql = "
            UPDATE rol
            SET
                nombre = ?,
                descripcion = ?,
                es_admin = ?
            WHERE id = ?
        ";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                "Error al preparar la actualización del rol: "
                . $this->conexion->error
            );
        }

        $nombre = strtoupper(trim((string)$datos['nombre']));
        $descripcion = $datos['descripcion'];
        $esAdmin = (int)$datos['es_admin'];

        $stmt->bind_param(
            "ssii",
            $nombre,
            $descripcion,
            $esAdmin,
            $rolId
        );

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();
            throw new Exception(
                "Error al actualizar el rol: "
                . $error
            );
        }

        $stmt->close();

        return true;
    }

    public function cambiarEstadoRol($rolId, $nuevoEstado)
    {
        $sql = "
            UPDATE rol
            SET activo = ?
            WHERE id = ?
        ";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                "Error al preparar el cambio de estado del rol: "
                . $this->conexion->error
            );
        }

        $stmt->bind_param(
            "ii",
            $nuevoEstado,
            $rolId
        );

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();
            throw new Exception(
                "Error al cambiar el estado del rol: "
                . $error
            );
        }

        $stmt->close();

        return true;
    }

    public function contarRolesAdministradoresActivos()
    {
        $sql = "
            SELECT COUNT(*) AS total
            FROM rol
            WHERE activo = 1
              AND es_admin = 1
        ";

        $resultado = $this->conexion->query($sql);

        if (!$resultado) {
            throw new Exception(
                "Error al contar los roles administradores activos: "
                . $this->conexion->error
            );
        }

        $fila = $resultado->fetch_assoc();

        return (int)($fila['total'] ?? 0);
    }

    public function contarUsuariosActivosPorRol($rolId)
    {
        $sql = "
            SELECT COUNT(*) AS total
            FROM usuario
            WHERE rol_id = ?
              AND activo = 1
        ";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                "Error al preparar el conteo de usuarios del rol: "
                . $this->conexion->error
            );
        }

        $stmt->bind_param("i", $rolId);

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();
            throw new Exception(
                "Error al contar los usuarios del rol: "
                . $error
            );
        }

        $resultado = $stmt->get_result();
        $fila = $resultado->fetch_assoc();
        $stmt->close();

        return (int)($fila['total'] ?? 0);
    }

    public function sincronizarNombreRolUsuarios($rolId, $nombreRol)
    {
        $sql = "
            UPDATE usuario
            SET rol = ?
            WHERE rol_id = ?
        ";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                "Error al preparar la sincronización del rol en usuarios: "
                . $this->conexion->error
            );
        }

        $stmt->bind_param(
            "si",
            $nombreRol,
            $rolId
        );

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();
            throw new Exception(
                "Error al sincronizar el rol de los usuarios: "
                . $error
            );
        }

        $stmt->close();

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | CATÁLOGO FUNCIONAL DE MÓDULOS Y REPORTES
    |--------------------------------------------------------------------------
    |
    | Este catálogo define los únicos permisos configurables del sistema.
    | Los archivos auxiliares (PDF, Excel, procesadores, etc.) no aparecen
    | como permisos independientes: deben reutilizar el permiso del módulo
    | principal correspondiente mediante verificarPermisoModulo().
    |
    |--------------------------------------------------------------------------
    */

    public function obtenerCatalogoModulos()
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | GESTIÓN
            |--------------------------------------------------------------------------
            */

            [
                'modulo' =>
                    'Gestión de Empleados',

                'archivo' =>
                    'empleados.php'
            ],

            [
                'modulo' =>
                    'Conceptos por Empleado',

                'archivo' =>
                    'empleado_conceptos.php'
            ],

            [
                'modulo' =>
                    'Gestión de Conceptos',

                'archivo' =>
                    'conceptos.php'
            ],

            [
                'modulo' =>
                    'Gestión de Categorías',

                'archivo' =>
                    'categorias.php'
            ],

            [
                'modulo' =>
                    'Liquidación',

                'archivo' =>
                    'liquidacion.php'
            ],


            /*
            |--------------------------------------------------------------------------
            | CONSULTAS Y REPORTES
            |--------------------------------------------------------------------------
            */

            [
                'modulo' =>
                    'Consultas y Reportes',

                'archivo' =>
                    'reportes.php'
            ],

            [
                'modulo' =>
                    'Reporte de Empleados',

                'archivo' =>
                    'reporte_empleados.php'
            ],

            [
                'modulo' =>
                    'Historial por Empleado',

                'archivo' =>
                    'reporte_historial_empleado.php'
            ],

            [
                'modulo' =>
                    'Reporte de Conceptos',

                'archivo' =>
                    'reporte_conceptos.php'
            ],

            [
                'modulo' =>
                    'Reporte de Categorías',

                'archivo' =>
                    'reporte_categorias.php'
            ],

            [
                'modulo' =>
                    'Reporte de Liquidaciones',

                'archivo' =>
                    'reporte_liquidaciones.php'
            ],

            [
                'modulo' =>
                    'Estadísticas',

                'archivo' =>
                    'estadisticas.php'
            ],

            [
                'modulo' =>
                    'Reporte de Auditoría',

                'archivo' =>
                    'reporte_auditoria.php'
            ],


            /*
            |--------------------------------------------------------------------------
            | ADMINISTRACIÓN Y AYUDA
            |--------------------------------------------------------------------------
            */

            [
                'modulo' =>
                    'Gestión de Usuarios',

                'archivo' =>
                    'usuarios.php'
            ],

            [
                'modulo' =>
                    'Ayuda',

                'archivo' =>
                    'ayuda.php'
            ]
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | OBTENER PERMISOS DEL ROL
    |--------------------------------------------------------------------------
    |
    | Devuelve únicamente los permisos pertenecientes al catálogo funcional
    | actual. De esta manera, cualquier registro antiguo o auxiliar que haya
    | quedado en rol_modulo_permiso no vuelve a mostrarse en la pantalla.
    |
    |--------------------------------------------------------------------------
    */

    public function obtenerPermisosRol($rolId)
    {
        $catalogo =
            $this->obtenerCatalogoModulos();


        /*
        |--------------------------------------------------------------------------
        | LEER PERMISOS EXISTENTES DEL ROL
        |--------------------------------------------------------------------------
        */

        $sql = "
            SELECT
                id,
                rol_id,
                modulo,
                archivo,
                permitido
            FROM rol_modulo_permiso
            WHERE rol_id = ?
        ";


        $stmt =
            $this->conexion->prepare(
                $sql
            );


        if (!$stmt) {

            throw new Exception(
                "Error al preparar la consulta de permisos: "
                . $this->conexion->error
            );
        }


        $stmt->bind_param(
            "i",
            $rolId
        );


        if (!$stmt->execute()) {

            $error =
                $stmt->error;


            $stmt->close();


            throw new Exception(
                "Error al consultar los permisos del rol: "
                . $error
            );
        }


        $resultado =
            $stmt->get_result();


        $existentes = [];


        while (
            $fila =
                $resultado->fetch_assoc()
        ) {

            $existentes[
                (string)$fila['archivo']
            ] =
                [
                    'id' =>
                        (int)$fila['id'],

                    'rol_id' =>
                        (int)$fila['rol_id'],

                    'modulo' =>
                        $fila['modulo'],

                    'archivo' =>
                        $fila['archivo'],

                    'permitido' =>
                        (int)$fila['permitido']
                ];
        }


        $stmt->close();


        /*
        |--------------------------------------------------------------------------
        | ARMAR RESPUESTA SEGÚN CATÁLOGO ACTUAL
        |--------------------------------------------------------------------------
        */

        $permisos = [];


        foreach ($catalogo as $item) {

            $archivo =
                (string)$item['archivo'];


            $permisoExistente =
                $existentes[$archivo]
                ?? null;


            $permisos[] =
                [
                    'id' =>
                        $permisoExistente
                            ?
                            (int)$permisoExistente['id']
                            :
                            0,

                    'rol_id' =>
                        (int)$rolId,

                    'modulo' =>
                        (string)$item['modulo'],

                    'archivo' =>
                        $archivo,

                    'permitido' =>
                        $permisoExistente
                            ?
                            (int)$permisoExistente['permitido']
                            :
                            0
                ];
        }


        return $permisos;
    }


    /*
    |--------------------------------------------------------------------------
    | PERMISOS DEL ROL INDEXADOS POR ARCHIVO
    |--------------------------------------------------------------------------
    */

    public function obtenerPermisosRolPorArchivo($rolId)
    {
        $permisos =
            $this->obtenerPermisosRol(
                $rolId
            );


        $indexados = [];


        foreach ($permisos as $permiso) {

            $indexados[
                $permiso['archivo']
            ] =
                $permiso;
        }


        return $indexados;
    }


    /*
    |--------------------------------------------------------------------------
    | ASEGURAR PERMISOS BASE DEL ROL
    |--------------------------------------------------------------------------
    |
    | Crea únicamente los permisos pertenecientes al catálogo funcional
    | actual. Los registros antiguos no se eliminan aquí; simplemente dejan
    | de formar parte del catálogo visible.
    |
    |--------------------------------------------------------------------------
    */

    public function asegurarPermisosBaseRol($rolId)
    {
        $catalogo =
            $this->obtenerCatalogoModulos();


        if (empty($catalogo)) {

            return true;
        }


        $sql = "
            INSERT INTO rol_modulo_permiso (
                rol_id,
                modulo,
                archivo,
                permitido
            )
            VALUES (?, ?, ?, 0)

            ON DUPLICATE KEY UPDATE
                modulo = VALUES(modulo)
        ";


        $stmt =
            $this->conexion->prepare(
                $sql
            );


        if (!$stmt) {

            throw new Exception(
                "Error al preparar la creación de permisos base: "
                . $this->conexion->error
            );
        }


        foreach ($catalogo as $item) {

            $modulo =
                (string)$item['modulo'];


            $archivo =
                (string)$item['archivo'];


            $stmt->bind_param(
                "iss",
                $rolId,
                $modulo,
                $archivo
            );


            if (!$stmt->execute()) {

                $error =
                    $stmt->error;


                $stmt->close();


                throw new Exception(
                    "Error al crear permisos base del rol: "
                    . $error
                );
            }
        }


        $stmt->close();


        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | GUARDAR UN PERMISO
    |--------------------------------------------------------------------------
    */

    public function guardarPermisoRol(
        $rolId,
        $archivo,
        $permitido
    ) {
        $sql = "
            UPDATE rol_modulo_permiso
            SET permitido = ?
            WHERE rol_id = ?
              AND archivo = ?
        ";


        $stmt =
            $this->conexion->prepare(
                $sql
            );


        if (!$stmt) {

            throw new Exception(
                "Error al preparar la actualización del permiso: "
                . $this->conexion->error
            );
        }


        $stmt->bind_param(
            "iis",
            $permitido,
            $rolId,
            $archivo
        );


        if (!$stmt->execute()) {

            $error =
                $stmt->error;


            $stmt->close();


            throw new Exception(
                "Error al actualizar el permiso: "
                . $error
            );
        }


        $stmt->close();


        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | RESETEAR PERMISOS DEL ROL
    |--------------------------------------------------------------------------
    */

    public function resetearPermisosRol($rolId)
    {
        $sql = "
            UPDATE rol_modulo_permiso
            SET permitido = 0
            WHERE rol_id = ?
        ";


        $stmt =
            $this->conexion->prepare(
                $sql
            );


        if (!$stmt) {

            throw new Exception(
                "Error al preparar el reseteo de permisos: "
                . $this->conexion->error
            );
        }


        $stmt->bind_param(
            "i",
            $rolId
        );


        if (!$stmt->execute()) {

            $error =
                $stmt->error;


            $stmt->close();


            throw new Exception(
                "Error al resetear los permisos del rol: "
                . $error
            );
        }


        $stmt->close();


        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | ACTIVAR TODOS LOS PERMISOS DEL ROL
    |--------------------------------------------------------------------------
    |
    | Primero asegura el catálogo actual y luego activa solamente los permisos
    | funcionales vigentes.
    |
    |--------------------------------------------------------------------------
    */

    public function activarTodosPermisosRol($rolId)
    {
        $this->asegurarPermisosBaseRol(
            $rolId
        );


        $catalogo =
            $this->obtenerCatalogoModulos();


        foreach ($catalogo as $item) {

            $this->guardarPermisoRol(
                $rolId,
                (string)$item['archivo'],
                1
            );
        }


        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | ARCHIVOS HIJOS DE CONSULTAS Y REPORTES
    |--------------------------------------------------------------------------
    |
    | Estos permisos dependen del acceso al módulo padre reportes.php.
    |
    |--------------------------------------------------------------------------
    */

    private function obtenerArchivosReportesHijos()
    {
        return [

            'reporte_empleados.php',

            'reporte_historial_empleado.php',

            'reporte_conceptos.php',

            'reporte_categorias.php',

            'reporte_liquidaciones.php',

            'estadisticas.php',

            'reporte_auditoria.php'
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | GUARDAR TODOS LOS PERMISOS DEL ROL
    |--------------------------------------------------------------------------
    |
    | Reglas:
    |
    | 1. Solo se aceptan archivos pertenecientes al catálogo funcional.
    |
    | 2. Los PDF, Excel y demás auxiliares no son permisos independientes.
    |
    | 3. Si se habilita cualquier reporte hijo, también se habilita
    |    automáticamente reportes.php.
    |
    | 4. La vista será la encargada de desmarcar visualmente los hijos cuando
    |    el administrador quite reportes.php. De esa manera, el POST normal
    |    llegará sin el padre ni sus hijos.
    |
    | 5. Si un POST manipulado envía un hijo sin reportes.php, el backend
    |    restaura automáticamente el padre. Así nunca queda un reporte hijo
    |    habilitado sin acceso al módulo Consultas y Reportes.
    |
    |--------------------------------------------------------------------------
    */

    public function guardarPermisosRol(
        $rolId,
        array $archivosPermitidos
    ) {
        $catalogo =
            $this->obtenerCatalogoModulos();


        /*
        |--------------------------------------------------------------------------
        | ARCHIVOS VÁLIDOS DEL CATÁLOGO
        |--------------------------------------------------------------------------
        */

        $archivosValidos = [];


        foreach ($catalogo as $item) {

            $archivo =
                trim(
                    (string)$item['archivo']
                );


            if ($archivo !== '') {

                $archivosValidos[
                    $archivo
                ] =
                    true;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | NORMALIZAR Y FILTRAR POST
        |--------------------------------------------------------------------------
        */

        $permitidosValidos = [];


        foreach ($archivosPermitidos as $archivo) {

            $archivo =
                trim(
                    (string)$archivo
                );


            if (
                $archivo === ''
                ||
                !isset(
                    $archivosValidos[
                        $archivo
                    ]
                )
            ) {

                continue;
            }


            $permitidosValidos[
                $archivo
            ] =
                true;
        }


        /*
        |--------------------------------------------------------------------------
        | DEPENDENCIA: REPORTES HIJOS → REPORTES.PHP
        |--------------------------------------------------------------------------
        |
        | Si existe al menos un reporte específico seleccionado, el módulo
        | padre "Consultas y Reportes" debe quedar habilitado.
        |
        |--------------------------------------------------------------------------
        */

        $archivoPadreReportes =
            'reportes.php';


        $reportesHijos =
            $this->obtenerArchivosReportesHijos();


        $hayReporteHijoPermitido =
            false;


        foreach ($reportesHijos as $archivoHijo) {

            if (
                isset(
                    $permitidosValidos[
                        $archivoHijo
                    ]
                )
            ) {

                $hayReporteHijoPermitido =
                    true;

                break;
            }
        }


        if ($hayReporteHijoPermitido) {

            $permitidosValidos[
                $archivoPadreReportes
            ] =
                true;
        }


        /*
        |--------------------------------------------------------------------------
        | TRANSACCIÓN
        |--------------------------------------------------------------------------
        */

        $this->iniciarTransaccion();


        try {

            /*
            |--------------------------------------------------------------------------
            | ASEGURAR CATÁLOGO
            |--------------------------------------------------------------------------
            */

            $this->asegurarPermisosBaseRol(
                $rolId
            );


            /*
            |--------------------------------------------------------------------------
            | DESACTIVAR PERMISOS ACTUALES
            |--------------------------------------------------------------------------
            */

            $this->resetearPermisosRol(
                $rolId
            );


            /*
            |--------------------------------------------------------------------------
            | ACTIVAR LOS SELECCIONADOS
            |--------------------------------------------------------------------------
            */

            foreach (
                array_keys(
                    $permitidosValidos
                )
                as $archivo
            ) {

                $this->guardarPermisoRol(
                    $rolId,
                    $archivo,
                    1
                );
            }


            $this->confirmarTransaccion();


            return true;


        } catch (Exception $e) {

            $this->revertirTransaccion();


            throw $e;
        }
    }


    public function existeDni(
        $dni,
        $idExcluir = null
    ) {
        $sql = "
            SELECT id
            FROM usuario
            WHERE dni = ?
        ";

        if ($idExcluir !== null) {
            $sql .= " AND id <> ? ";
        }

        $sql .= " LIMIT 1 ";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                "Error al preparar la validación del DNI: "
                . $this->conexion->error
            );
        }

        if ($idExcluir === null) {
            $stmt->bind_param("s", $dni);
        } else {
            $stmt->bind_param("si", $dni, $idExcluir);
        }

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();
            throw new Exception(
                "Error al validar el DNI: "
                . $error
            );
        }

        $resultado = $stmt->get_result();
        $existe = $resultado->num_rows > 0;
        $stmt->close();

        return $existe;
    }

    public function existeNombreUsuario(
        $nombreUsuario,
        $idExcluir = null
    ) {
        $sql = "
            SELECT id
            FROM usuario
            WHERE nombre_usuario = ?
        ";

        if ($idExcluir !== null) {
            $sql .= " AND id <> ? ";
        }

        $sql .= " LIMIT 1 ";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                "Error al preparar la validación del nombre de usuario: "
                . $this->conexion->error
            );
        }

        if ($idExcluir === null) {
            $stmt->bind_param("s", $nombreUsuario);
        } else {
            $stmt->bind_param("si", $nombreUsuario, $idExcluir);
        }

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();
            throw new Exception(
                "Error al validar el nombre de usuario: "
                . $error
            );
        }

        $resultado = $stmt->get_result();
        $existe = $resultado->num_rows > 0;
        $stmt->close();

        return $existe;
    }

    public function existeEmail(
        $email,
        $idExcluir = null
    ) {
        $sql = "
            SELECT id
            FROM usuario
            WHERE email = ?
        ";

        if ($idExcluir !== null) {
            $sql .= " AND id <> ? ";
        }

        $sql .= " LIMIT 1 ";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                "Error al preparar la validación del email: "
                . $this->conexion->error
            );
        }

        if ($idExcluir === null) {
            $stmt->bind_param("s", $email);
        } else {
            $stmt->bind_param("si", $email, $idExcluir);
        }

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();
            throw new Exception(
                "Error al validar el email: "
                . $error
            );
        }

        $resultado = $stmt->get_result();
        $existe = $resultado->num_rows > 0;
        $stmt->close();

        return $existe;
    }

    public function guardar($datos)
    {
        $sql = "
            INSERT INTO usuario (
                nombre,
                apellido,
                dni,
                nombre_usuario,
                email,
                clave,
                rol,
                rol_id,
                activo
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                "Error al preparar el alta del usuario: "
                . $this->conexion->error
            );
        }

        $nombre = $datos['nombre'];
        $apellido = $datos['apellido'];
        $dni = $datos['dni'];
        $nombreUsuario = $datos['nombre_usuario'];
        $email = $datos['email'];
        $clave = $datos['clave'];
        $rol = $datos['rol'];
        $rolId = (int)$datos['rol_id'];
        $activo = (int)$datos['activo'];

        $stmt->bind_param(
            "sssssssii",
            $nombre,
            $apellido,
            $dni,
            $nombreUsuario,
            $email,
            $clave,
            $rol,
            $rolId,
            $activo
        );

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();
            throw new Exception(
                "Error al guardar el usuario: "
                . $error
            );
        }

        $idUsuario = $stmt->insert_id;
        $stmt->close();

        return $idUsuario;
    }

    public function actualizarSinClave(
        $id,
        $datos
    ) {
        $sql = "
            UPDATE usuario
            SET
                nombre = ?,
                apellido = ?,
                dni = ?,
                nombre_usuario = ?,
                email = ?,
                rol = ?,
                rol_id = ?
            WHERE id = ?
        ";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                "Error al preparar la actualización del usuario: "
                . $this->conexion->error
            );
        }

        $nombre = $datos['nombre'];
        $apellido = $datos['apellido'];
        $dni = $datos['dni'];
        $nombreUsuario = $datos['nombre_usuario'];
        $email = $datos['email'];
        $rol = $datos['rol'];
        $rolId = (int)$datos['rol_id'];

        $stmt->bind_param(
            "ssssssii",
            $nombre,
            $apellido,
            $dni,
            $nombreUsuario,
            $email,
            $rol,
            $rolId,
            $id
        );

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();
            throw new Exception(
                "Error al actualizar el usuario: "
                . $error
            );
        }

        $stmt->close();

        return true;
    }

    public function actualizarConClave(
        $id,
        $datos
    ) {
        $sql = "
            UPDATE usuario
            SET
                nombre = ?,
                apellido = ?,
                dni = ?,
                nombre_usuario = ?,
                email = ?,
                clave = ?,
                rol = ?,
                rol_id = ?
            WHERE id = ?
        ";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                "Error al preparar la actualización del usuario: "
                . $this->conexion->error
            );
        }

        $nombre = $datos['nombre'];
        $apellido = $datos['apellido'];
        $dni = $datos['dni'];
        $nombreUsuario = $datos['nombre_usuario'];
        $email = $datos['email'];
        $clave = $datos['clave'];
        $rol = $datos['rol'];
        $rolId = (int)$datos['rol_id'];

        $stmt->bind_param(
            "sssssssii",
            $nombre,
            $apellido,
            $dni,
            $nombreUsuario,
            $email,
            $clave,
            $rol,
            $rolId,
            $id
        );

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();
            throw new Exception(
                "Error al actualizar el usuario: "
                . $error
            );
        }

        $stmt->close();

        return true;
    }

    public function cambiarEstado(
        $id,
        $nuevoEstado
    ) {
        $sql = "
            UPDATE usuario
            SET activo = ?
            WHERE id = ?
        ";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                "Error al preparar el cambio de estado del usuario: "
                . $this->conexion->error
            );
        }

        $stmt->bind_param(
            "ii",
            $nuevoEstado,
            $id
        );

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();
            throw new Exception(
                "Error al cambiar el estado del usuario: "
                . $error
            );
        }

        $stmt->close();

        return true;
    }

    public function contarAdministradoresActivos()
    {
        $sql = "
            SELECT COUNT(*) AS total
            FROM usuario u
            INNER JOIN rol r ON u.rol_id = r.id
            WHERE u.activo = 1
              AND r.activo = 1
              AND r.es_admin = 1
        ";

        $resultado = $this->conexion->query($sql);

        if (!$resultado) {
            throw new Exception(
                "Error al contar los administradores activos: "
                . $this->conexion->error
            );
        }

        $fila = $resultado->fetch_assoc();

        return (int)($fila['total'] ?? 0);
    }

    public function esAdminPrincipal($idUsuario)
    {
        $sql = "
            SELECT u.id
            FROM usuario u
            INNER JOIN rol r ON u.rol_id = r.id
            WHERE u.id = ?
              AND UPPER(r.nombre) = 'ADMIN'
              AND r.es_admin = 1
            LIMIT 1
        ";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                "Error al preparar la validación del ADMIN principal: "
                . $this->conexion->error
            );
        }

        $stmt->bind_param("i", $idUsuario);

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();
            throw new Exception(
                "Error al validar el ADMIN principal: "
                . $error
            );
        }

        $resultado = $stmt->get_result();
        $esAdmin = $resultado->num_rows > 0;
        $stmt->close();

        return $esAdmin;
    }

    public function esRolAdminPrincipal($rolId)
    {
        $rol = $this->obtenerRolPorId($rolId);

        if (!$rol) {
            return false;
        }

        return (
            strtoupper(trim((string)$rol['nombre'])) === 'ADMIN'
            &&
            (int)$rol['es_admin'] === 1
        );
    }

    public function iniciarTransaccion()
    {
        if (!$this->conexion->begin_transaction()) {
            throw new Exception(
                "No se pudo iniciar la transacción."
            );
        }
    }

    public function confirmarTransaccion()
    {
        if (!$this->conexion->commit()) {
            throw new Exception(
                "No se pudo confirmar la transacción."
            );
        }
    }

    public function revertirTransaccion()
    {
        $this->conexion->rollback();
    }
}