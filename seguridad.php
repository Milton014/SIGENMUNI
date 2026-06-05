<?php

function iniciarSesionSiHaceFalta() {// Verifica si la sesión ya está iniciada, si no, la inicia
    if (session_status() === PHP_SESSION_NONE) { // Verifica si el estado de la sesión es PHP_SESSION_NONE, lo que significa que no se ha iniciado una sesión
        session_start(); // Si no se ha iniciado una sesión, la inicia con session_start()
    }
}

/* Verifica que exista sesión iniciada */
function verificarSesion() {//Función para verificar que el usuario haya iniciado sesión

    iniciarSesionSiHaceFalta(); 

    if (!isset($_SESSION['usuario'])) {//Pregunta si no existe un usuario guardado en sesión.
        header("Location: login.php"); //Si no hay usuario, lo manda al login y corta la ejecución
        exit();
    }
}

/* Permite acceso SOLO al ADMIN */
function soloAdmin() {

    iniciarSesionSiHaceFalta(); // Verifica si la sesión ya está iniciada, si no, la inicia

    if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'ADMIN') { // Verifica si el rol del usuario no es ADMIN
        header("Location: index.php?error=sin_permiso"); // Si no es ADMIN, lo redirige a la página principal con un mensaje de error
        exit();
    }
}

/* Permite varios roles */
function permitirRoles($roles) { // Función para permitir acceso a varios roles, recibe un array con los roles permitidos

    iniciarSesionSiHaceFalta(); // Verifica si la sesión ya está iniciada, si no, la inicia

    if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], $roles)) {// Verifica si el rol del usuario no está en el array de roles permitidos
        header("Location: index.php?error=sin_permiso"); // Si el rol no está permitido, lo redirige a la página principal (menu) con un mensaje de error 
        exit();
    }
}

/* funcion que Verifica si el usuario tiene permiso para ingresar a un módulo */
function verificarPermisoModulo($archivoModulo) {

    iniciarSesionSiHaceFalta(); // Verifica si la sesión ya está iniciada, si no, la inicia

    if (!isset($_SESSION['usuario'])) { // Verifica si no hay usuario logueado
        header("Location: login.php"); // Si no hay usuario, lo manda al login y corta la ejecución
        exit();
    }

    if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'ADMIN') { // Si el usuario es ADMIN, tiene acceso a todos los módulos
        return true; // Si el rol es ADMIN, se le permite el acceso al módulo sin hacer más verificaciones, ya que el ADMIN tiene acceso a todos los módulos
    }

    if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'OPERADOR') { // Si el rol no es OPERADOR, no tiene acceso a ningún módulo (excepto ADMIN que ya se verificó antes)
        header("Location: index.php?error=sin_permiso"); // Si el rol no es OPERADOR, lo redirige a la página principal (menu) con un mensaje de error
        exit();
    }

    require_once("conexion.php"); // Incluye el archivo de conexión a la base de datos

    global $conexion; //Permite usar la variable $conexion dentro de la función.

    // Consulta para verificar si el módulo al que se quiere acceder tiene permitido el acceso para el rol OPERADOR
    // Se limita a 1 resultado porque cada módulo debería tener una sola configuración de permisos
    // Busca en la tabla modulo_permitido el archivo principal del módulo y devuelve si está permitido para el rol OPERADOR
    $stmt = $conexion->prepare(" 
        SELECT permitido_operador 
        FROM modulo_permiso
        WHERE archivo_principal = ?
        LIMIT 1
    ");

    $stmt->bind_param("s", $archivoModulo); // Vincula el parámetro del archivo del módulo a la consulta.Reemplaza el ? de la consulta por el nombre del archivo
    $stmt->execute(); // Ejecuta la consulta

    $resultado = $stmt->get_result(); // Obtiene el resultado de la consulta. En este caso, debería ser un solo registro con el campo permitido_operador que indica si el rol OPERADOR tiene acceso a ese módulo

    if ($resultado->num_rows === 0) { // Si no se encuentra el módulo en la tabla de permisos, se asume que no tiene permiso para ningún rol (excepto ADMIN que ya se verificó antes)
        header("Location: index.php?error=sin_permiso"); // Si no se encuentra el módulo, lo redirige a la página principal (menu) con un mensaje de error
        exit(); // Corta la ejecución para evitar que el usuario pueda acceder al módulo aunque no tenga permiso
    }

    $datos = $resultado->fetch_assoc(); // Covierte el resultado de la consulta en un arreglo asociativo. En este caso, el array tendrá una clave 'permitido_operador' que indica si el rol OPERADOR tiene acceso a ese módulo

    if ((int)$datos['permitido_operador'] !== 1) { // Si el campo permitido_operador no es 1, significa que el rol OPERADOR no tiene permiso para ese módulo
        header("Location: index.php?error=sin_permiso"); // Si el rol OPERADOR no tiene permiso, lo redirige a la página principal (menu) con un mensaje de error
        exit();
    }

    return true; // Si se llega a este punto, significa que el usuario tiene permiso para acceder al módulo. si paso todos los controles, se le permite el acceso al módulo
}

/* Devuelve el rol del usuario logueado */
function rolUsuario() {

    iniciarSesionSiHaceFalta(); // Verifica si la sesión ya está iniciada, si no, la inicia

    return $_SESSION['rol'] ?? null; // Devuelve el rol del usuario logueado, o null si no hay usuario logueado. El operador ?? devuelve el valor de $_SESSION['rol'] si existe y no es null, o devuelve null si $_SESSION['rol'] no está definido o es null
}

/* Devuelve el nombre de usuario logueado */
function usuarioLogueado() {

    iniciarSesionSiHaceFalta();// Verifica si la sesión ya está iniciada, si no, la inicia

    return $_SESSION['usuario'] ?? null; // Devuelve el nombre de usuario logueado, o null si no hay usuario logueado. El operador ?? devuelve el valor de $_SESSION['usuario'] si existe y no es null, o devuelve null si $_SESSION['usuario'] no está definido o es null
}

?>