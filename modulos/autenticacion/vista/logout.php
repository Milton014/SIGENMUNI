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

<style>

body{
    margin:0;
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    font-family:Arial,sans-serif;
    background:#f4f7fb;
    color:#1f2937;
}

.card{
    width:90%;
    max-width:420px;
    background:white;
    padding:30px;
    border-radius:16px;
    box-shadow:0 8px 20px rgba(0,0,0,.10);
    text-align:center;
}

h1{
    margin-top:0;
    color:#0f766e;
    font-size:24px;
}

p{
    line-height:1.5;
}

a{
    color:#0f766e;
    font-weight:bold;
    text-decoration:none;
}

a:hover{
    text-decoration:underline;
}

</style>

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


<script>

/*
|--------------------------------------------------------------------------
| ELIMINAR CACHÉ LOCAL DE LA SESIÓN
|--------------------------------------------------------------------------
*/

try {

    localStorage.removeItem(
        "sigenmuni_sesion"
    );

} catch (error) {

    console.warn(
        "No fue posible limpiar localStorage.",
        error
    );
}


/*
|--------------------------------------------------------------------------
| REDIRIGIR AL LOGIN DEL ROUTER
|--------------------------------------------------------------------------
*/

window.location.replace(
    <?php
    echo json_encode(
        $autenticacionUrlLogin,
        JSON_UNESCAPED_SLASHES
        |
        JSON_UNESCAPED_UNICODE
    );
    ?>
);

</script>

</body>

</html>
