<?php

require_once __DIR__ . '/Url.php';

function iniciarSesionSiHaceFalta()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function obtenerConexionSeguridad()
{
    global $conexion;
    if (!isset($conexion)) {
        require_once __DIR__ . '/../config/conexion.php';
    }
    if (!$conexion) {
        throw new RuntimeException('No se pudo comprobar el acceso.');
    }
    return $conexion;
}

/** Cierra también sesiones antiguas para evitar un ciclo entre login e inicio. */
function sigenmuniCerrarSesionInvalida(string $error = ''): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $cookie = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires' => time() - 42000,
            'path' => $cookie['path'],
            'domain' => $cookie['domain'],
            'secure' => $cookie['secure'],
            'httponly' => $cookie['httponly'],
            'samesite' => $cookie['samesite'],
        ]);
    }
    session_destroy();
    header('Cache-Control: no-store');
    header('Location: ' . sigenmuniUrlRuta('login', $error === '' ? [] : ['error' => $error]));
    exit;
}

/** La base de datos, y no el rol guardado al ingresar, autoriza cada solicitud. */
function obtenerRolActual()
{
    iniciarSesionSiHaceFalta();
    $id = $_SESSION['id_usuario'] ?? null;
    if ((!is_int($id) && !is_string($id))
        || filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) === false) {
        sigenmuniCerrarSesionInvalida();
    }
    $id = (int)$id;
    $stmt = obtenerConexionSeguridad()->prepare(
        'SELECT u.id, u.nombre_usuario, u.nombre, u.apellido, u.activo, u.version_acceso,
                r.id AS rol_id, r.nombre AS rol_nombre, r.activo AS rol_activo, r.es_admin
         FROM usuario u LEFT JOIN rol r ON r.id = u.rol_id
         WHERE u.id = ? LIMIT 1'
    );
    if (!$stmt) {
        throw new RuntimeException('No se pudo comprobar el acceso.');
    }
    try {
        if (!$stmt->bind_param('i', $id) || !$stmt->execute()) {
            throw new RuntimeException('No se pudo comprobar el acceso.');
        }
        $resultado = $stmt->get_result();
        if (!$resultado) {
            throw new RuntimeException('No se pudo comprobar el acceso.');
        }
        $usuario = $resultado->fetch_assoc();
    } finally {
        $stmt->close();
    }
    if (!$usuario || (int)$usuario['activo'] !== 1) {
        sigenmuniCerrarSesionInvalida('usuario_inactivo');
    }
    // Las sesiones previas a esta actualización equivalen a la versión inicial 0.
    $versionSesion = $_SESSION['version_acceso'] ?? 0;
    if ((!is_int($versionSesion) && !is_string($versionSesion))
        || filter_var($versionSesion, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]) === false
        || (int)$versionSesion !== (int)$usuario['version_acceso']) {
        sigenmuniCerrarSesionInvalida('acceso_actualizado');
    }
    if ((int)($usuario['rol_id'] ?? 0) <= 0 || trim((string)$usuario['rol_nombre']) === '') {
        sigenmuniCerrarSesionInvalida('rol_invalido');
    }
    if ((int)$usuario['rol_activo'] !== 1) {
        sigenmuniCerrarSesionInvalida('rol_inactivo');
    }

    $_SESSION['id_usuario'] = (int)$usuario['id'];
    $_SESSION['usuario'] = $usuario['nombre_usuario'];
    $_SESSION['nombre_completo'] = trim(($usuario['nombre'] ?? '') . ' ' . ($usuario['apellido'] ?? ''));
    $_SESSION['rol_id'] = (int)$usuario['rol_id'];
    $_SESSION['rol'] = $usuario['rol_nombre'];
    $_SESSION['es_admin'] = (int)$usuario['es_admin'];
    header('Cache-Control: no-store');

    return [
        'id' => (int)$usuario['rol_id'],
        'nombre' => $usuario['rol_nombre'],
        'activo' => (int)$usuario['rol_activo'],
        'es_admin' => (int)$usuario['es_admin'],
    ];
}

function verificarSesion()
{
    obtenerRolActual();
}

/** Conserva la regla existente para el rol ADMIN y los roles es_admin. */
function sigenmuniRolEsAdmin(array $rol): bool
{
    return (int)$rol['es_admin'] === 1 || strtoupper(trim($rol['nombre'])) === 'ADMIN';
}

function sigenmuniDenegarPermiso(): void
{
    header('Location: ' . sigenmuniUrlRuta('inicio', ['error' => 'sin_permiso']));
    exit;
}

function soloAdmin()
{
    $rol = obtenerRolActual();
    if (!sigenmuniRolEsAdmin($rol)) {
        sigenmuniDenegarPermiso();
    }
    return true;
}

function verificarPermisoModulo($archivoModulo)
{
    $rol = obtenerRolActual();
    $archivoModulo = trim((string)$archivoModulo);
    if ($archivoModulo === '') {
        sigenmuniDenegarPermiso();
    }
    if (sigenmuniRolEsAdmin($rol)) {
        return true;
    }
    $rolId = (int)$rol['id'];
    $stmt = obtenerConexionSeguridad()->prepare(
        'SELECT permitido FROM rol_modulo_permiso WHERE rol_id = ? AND archivo = ? LIMIT 1'
    );
    if (!$stmt) {
        throw new RuntimeException('No se pudo comprobar el permiso.');
    }
    try {
        if (!$stmt->bind_param('is', $rolId, $archivoModulo) || !$stmt->execute()) {
            throw new RuntimeException('No se pudo comprobar el permiso.');
        }
        $resultado = $stmt->get_result();
        if (!$resultado) {
            throw new RuntimeException('No se pudo comprobar el permiso.');
        }
        $datos = $resultado->fetch_assoc();
    } finally {
        $stmt->close();
    }
    if ((int)($datos['permitido'] ?? 0) !== 1) {
        sigenmuniDenegarPermiso();
    }
    return true;
}
