<?php

function iniciarSesionSiHaceFalta() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/* Verifica que exista sesión iniciada */
function verificarSesion() {

    iniciarSesionSiHaceFalta();

    if (!isset($_SESSION['usuario'])) {
        header("Location: login.php");
        exit();
    }
}

/* Permite acceso SOLO al ADMIN */
function soloAdmin() {

    iniciarSesionSiHaceFalta();

    if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'ADMIN') {
        header("Location: index.php?error=sin_permiso");
        exit();
    }
}

/* Permite varios roles */
function permitirRoles($roles) {

    iniciarSesionSiHaceFalta();

    if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], $roles)) {
        header("Location: index.php?error=sin_permiso");
        exit();
    }
}

/* Devuelve el rol del usuario logueado */
function rolUsuario() {

    iniciarSesionSiHaceFalta();

    return $_SESSION['rol'] ?? null;
}

/* Devuelve el nombre de usuario logueado */
function usuarioLogueado() {

    iniciarSesionSiHaceFalta();

    return $_SESSION['usuario'] ?? null;
}

?>