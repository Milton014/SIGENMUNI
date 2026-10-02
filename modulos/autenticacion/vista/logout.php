<?php

/*
|--------------------------------------------------------------------------
| VISTA - LOGOUT
|--------------------------------------------------------------------------
|
| La sesión PHP ya fue destruida por AutenticacionControlador::logout().
| Esta vista limpia el dato visual almacenado en localStorage y redirige al
| Login del Router.
|
|--------------------------------------------------------------------------
*/

?>
<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>
    Cerrando sesión - SIGENMUNI
</title>

<meta
    http-equiv="refresh"
    content="2;url=<?php
        echo htmlspecialchars(
            $autenticacionUrlLogin,
            ENT_QUOTES,
            'UTF-8'
        );
    ?>"
>

<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/autenticacion/logout.css'), ENT_QUOTES, 'UTF-8') ?>">

</head>

<body>

<div class="card">

    <h1>
        Cerrando sesión
    </h1>

    <p>
        La sesión se cerró correctamente.
    </p>

    <p>
        Redirigiendo al login...
    </p>

    <p>
        <a
            href="<?php
                echo htmlspecialchars(
                    $autenticacionUrlLogin,
                    ENT_QUOTES,
                    'UTF-8'
                );
            ?>"
        >
            Ir al login
        </a>
    </p>

</div>


<script type="application/json" id="datos-logout"><?= sigenmuniJsonVista([
    'autenticacionUrlLogin' => $autenticacionUrlLogin
]) ?></script>
<script src="<?= htmlspecialchars(sigenmuniUrlAsset('js/autenticacion/logout.js'), ENT_QUOTES, 'UTF-8') ?>"></script>

</body>

</html>
