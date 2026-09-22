<?php

/*
|--------------------------------------------------------------------------
| SEGURIDAD GENERAL - SIGENMUNI (CORE)
|--------------------------------------------------------------------------
|
| Funciones:
|
| - iniciarSesionSiHaceFalta()
| - verificarSesion()
| - obtenerConexionSeguridad()
| - obtenerRolActual()
| - soloAdmin()
| - verificarPermisoModulo()
|
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| URL COMPARTIDAS
|--------------------------------------------------------------------------
|
| El Login y el Menú Principal funcionan mediante las rutas "login" e
| "inicio" del Router.
|
| Las consultas y las reglas de autorización de este archivo no cambian.
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/Url.php';


/*
|--------------------------------------------------------------------------
| INICIAR SESIÓN SI HACE FALTA
|--------------------------------------------------------------------------
*/

function iniciarSesionSiHaceFalta()
{
    if (session_status() === PHP_SESSION_NONE) {

        session_start();
    }
}


/*
|--------------------------------------------------------------------------
| VERIFICAR SESIÓN
|--------------------------------------------------------------------------
*/

function verificarSesion()
{
    iniciarSesionSiHaceFalta();


    if (!isset($_SESSION['usuario'])) {

        header(
            'Location: ' . sigenmuniUrlRuta(
                'login'
            )
        );

        exit();
    }
}


/*
|--------------------------------------------------------------------------
| OBTENER CONEXIÓN
|--------------------------------------------------------------------------
|
| Centraliza la inclusión de config/conexion.php y devuelve
| la conexión activa del sistema.
|
|--------------------------------------------------------------------------
*/

function obtenerConexionSeguridad()
{
    global $conexion;


    require_once __DIR__ . '/../config/conexion.php';


    if (
        !isset($conexion)
        ||
        !$conexion
    ) {

        die(
            "No se pudo establecer la conexión con la base de datos."
        );
    }


    return $conexion;
}


/*
|--------------------------------------------------------------------------
| OBTENER DATOS DEL ROL ACTUAL
|--------------------------------------------------------------------------
|
| Devuelve:
|
| - id
| - nombre
| - activo
| - es_admin
|
|--------------------------------------------------------------------------
*/

function obtenerRolActual()
{
    iniciarSesionSiHaceFalta();


    /*
    |--------------------------------------------------------------------------
    | VALIDAR DATOS DE SESIÓN
    |--------------------------------------------------------------------------
    */

    if (
        !isset($_SESSION['rol_id'])
        ||
        !isset($_SESSION['rol'])
    ) {

        return null;
    }


    $rolId =
        (int)$_SESSION['rol_id'];


    if ($rolId <= 0) {

        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | CONEXIÓN
    |--------------------------------------------------------------------------
    */

    $conexion =
        obtenerConexionSeguridad();


    /*
    |--------------------------------------------------------------------------
    | CONSULTAR ROL
    |--------------------------------------------------------------------------
    */

    $stmt =
        $conexion->prepare(
            "
            SELECT
                id,
                nombre,
                activo,
                es_admin
            FROM rol
            WHERE id = ?
            LIMIT 1
            "
        );


    if (!$stmt) {

        die(
            "Error al preparar la consulta de seguridad: "
            .
            $conexion->error
        );
    }


    $stmt->bind_param(
        "i",
        $rolId
    );


    /*
    |--------------------------------------------------------------------------
    | EJECUTAR
    |--------------------------------------------------------------------------
    */

    if (!$stmt->execute()) {

        $error =
            $stmt->error;


        $stmt->close();


        die(
            "Error al consultar el rol del usuario: "
            .
            $error
        );
    }


    $resultado =
        $stmt->get_result();


    /*
    |--------------------------------------------------------------------------
    | VALIDAR RESULTADO
    |--------------------------------------------------------------------------
    */

    if (
        !$resultado
        ||
        $resultado->num_rows === 0
    ) {

        $stmt->close();

        return null;
    }


    $rol =
        $resultado->fetch_assoc();


    $stmt->close();


    return $rol;
}


/*
|--------------------------------------------------------------------------
| SOLO ADMINISTRADORES
|--------------------------------------------------------------------------
|
| Permite:
|
| - ADMIN principal
| - cualquier rol activo con es_admin = 1
|
|--------------------------------------------------------------------------
*/

function soloAdmin()
{
    verificarSesion();


    /*
    |--------------------------------------------------------------------------
    | OBTENER ROL
    |--------------------------------------------------------------------------
    */

    $rol =
        obtenerRolActual();


    if (!$rol) {

        header(
            'Location: ' . sigenmuniUrlRuta(
                'inicio',
                ['error' => 'sin_permiso']
            )
        );

        exit();
    }


    /*
    |--------------------------------------------------------------------------
    | ROL INACTIVO
    |--------------------------------------------------------------------------
    */

    if (
        (int)$rol['activo'] !== 1
    ) {

        header(
            'Location: ' . sigenmuniUrlRuta(
                'inicio',
                ['error' => 'rol_inactivo']
            )
        );

        exit();
    }


    /*
    |--------------------------------------------------------------------------
    | VERIFICAR ADMINISTRADOR
    |--------------------------------------------------------------------------
    */

    $nombreRol =
        strtoupper(
            trim(
                $rol['nombre']
                ?? ''
            )
        );


    $esAdmin =
        (
            (int)(
                $rol['es_admin']
                ?? 0
            )
            ===
            1
            ||
            $nombreRol === 'ADMIN'
        );


    if (!$esAdmin) {

        header(
            'Location: ' . sigenmuniUrlRuta(
                'inicio',
                ['error' => 'sin_permiso']
            )
        );

        exit();
    }


    return true;
}


/*
|--------------------------------------------------------------------------
| VERIFICAR PERMISO POR MÓDULO
|--------------------------------------------------------------------------
|
| Reglas:
|
| 1. Debe existir sesión.
| 2. Debe existir rol_id y rol.
| 3. El rol debe existir en la base.
| 4. El rol debe estar activo.
| 5. ADMIN o es_admin = 1 tiene acceso total.
| 6. Para roles comunes debe existir una fila en rol_modulo_permiso.
| 7. permitido debe ser = 1.
|
|--------------------------------------------------------------------------
*/

function verificarPermisoModulo($archivoModulo)
{
    verificarSesion();


    /*
    |--------------------------------------------------------------------------
    | NORMALIZAR ARCHIVO
    |--------------------------------------------------------------------------
    */

    $archivoModulo =
        trim(
            (string)$archivoModulo
        );


    if ($archivoModulo === '') {

        header(
            'Location: ' . sigenmuniUrlRuta(
                'inicio',
                ['error' => 'sin_permiso']
            )
        );

        exit();
    }


    /*
    |--------------------------------------------------------------------------
    | OBTENER ROL
    |--------------------------------------------------------------------------
    */

    $rol =
        obtenerRolActual();


    if (!$rol) {

        header(
            'Location: ' . sigenmuniUrlRuta(
                'inicio',
                ['error' => 'sin_permiso']
            )
        );

        exit();
    }


    /*
    |--------------------------------------------------------------------------
    | ROL INACTIVO
    |--------------------------------------------------------------------------
    */

    if (
        (int)$rol['activo'] !== 1
    ) {

        header(
            'Location: ' . sigenmuniUrlRuta(
                'inicio',
                ['error' => 'rol_inactivo']
            )
        );

        exit();
    }


    /*
    |--------------------------------------------------------------------------
    | ADMINISTRADOR
    |--------------------------------------------------------------------------
    |
    | ADMIN principal o cualquier rol con es_admin = 1
    | tiene acceso completo.
    |
    |--------------------------------------------------------------------------
    */

    $nombreRol =
        strtoupper(
            trim(
                $rol['nombre']
                ?? ''
            )
        );


    if (
        (int)(
            $rol['es_admin']
            ?? 0
        )
        ===
        1
        ||
        $nombreRol === 'ADMIN'
    ) {

        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | ROL COMÚN - VERIFICAR PERMISO
    |--------------------------------------------------------------------------
    */

    $conexion =
        obtenerConexionSeguridad();


    $rolId =
        (int)$rol['id'];


    /*
    |--------------------------------------------------------------------------
    | CONSULTAR PERMISO
    |--------------------------------------------------------------------------
    */

    $stmt =
        $conexion->prepare(
            "
            SELECT
                permitido
            FROM rol_modulo_permiso
            WHERE rol_id = ?
              AND archivo = ?
            LIMIT 1
            "
        );


    if (!$stmt) {

        die(
            "Error al preparar la consulta de permisos: "
            .
            $conexion->error
        );
    }


    $stmt->bind_param(
        "is",
        $rolId,
        $archivoModulo
    );


    /*
    |--------------------------------------------------------------------------
    | EJECUTAR
    |--------------------------------------------------------------------------
    */

    if (!$stmt->execute()) {

        $error =
            $stmt->error;


        $stmt->close();


        die(
            "Error al verificar el permiso del módulo: "
            .
            $error
        );
    }


    $resultado =
        $stmt->get_result();


    /*
    |--------------------------------------------------------------------------
    | SI NO EXISTE EL PERMISO, SE DENIEGA
    |--------------------------------------------------------------------------
    */

    if (
        !$resultado
        ||
        $resultado->num_rows === 0
    ) {

        $stmt->close();


        header(
            'Location: ' . sigenmuniUrlRuta(
                'inicio',
                ['error' => 'sin_permiso']
            )
        );

        exit();
    }


    $datos =
        $resultado->fetch_assoc();


    $stmt->close();


    /*
    |--------------------------------------------------------------------------
    | PERMISO DESACTIVADO
    |--------------------------------------------------------------------------
    */

    if (
        (int)(
            $datos['permitido']
            ?? 0
        )
        !==
        1
    ) {

        header(
            'Location: ' . sigenmuniUrlRuta(
                'inicio',
                ['error' => 'sin_permiso']
            )
        );

        exit();
    }


    return true;
}
