<?php

/*
|--------------------------------------------------------------------------
| PROTECCIÓN CSRF - SIGENMUNI
|--------------------------------------------------------------------------
|
| Genera un token asociado a la sesión y permite validarlo en operaciones
| que modifican datos mediante POST.
|
|--------------------------------------------------------------------------
*/

function sigenmuniCsrfToken()
{
    if (
        session_status()
        ===
        PHP_SESSION_NONE
    ) {
        session_start();
    }

    if (
        !isset(
            $_SESSION['csrf_token']
        )
        ||
        !is_string(
            $_SESSION['csrf_token']
        )
        ||
        strlen(
            $_SESSION['csrf_token']
        ) < 32
    ) {
        $_SESSION['csrf_token'] =
            bin2hex(
                random_bytes(32)
            );
    }

    return $_SESSION['csrf_token'];
}


function sigenmuniCsrfValido($tokenRecibido)
{
    if (
        session_status()
        ===
        PHP_SESSION_NONE
    ) {
        session_start();
    }

    if (
        !is_string($tokenRecibido)
        ||
        $tokenRecibido === ''
    ) {
        return false;
    }

    $tokenSesion =
        $_SESSION['csrf_token']
        ?? '';

    if (
        !is_string($tokenSesion)
        ||
        $tokenSesion === ''
    ) {
        return false;
    }

    return hash_equals(
        $tokenSesion,
        $tokenRecibido
    );
}
