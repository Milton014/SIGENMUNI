<?php

/*
|--------------------------------------------------------------------------
| HEADER GLOBAL - SIGENMUNI
|--------------------------------------------------------------------------
|
| Componente reutilizable para todas las pantallas autenticadas.
|
| Variables opcionales antes de incluirlo:
|
| $sigenmuniHeaderSubtitulo = 'Gestión de Empleados';
|
| Si no se informa subtítulo, utiliza el nombre general del sistema.
|
|--------------------------------------------------------------------------
*/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$sigenmuniHeaderNombre =
    $_SESSION['nombre_completo']
    ??
    $_SESSION['usuario']
    ??
    'Usuario';


$sigenmuniHeaderRol =
    $_SESSION['rol']
    ??
    '';


$sigenmuniHeaderSubtitulo =
    isset($sigenmuniHeaderSubtitulo)
    &&
    trim(
        (string)$sigenmuniHeaderSubtitulo
    ) !== ''
        ?
        trim(
            (string)$sigenmuniHeaderSubtitulo
        )
        :
        'Sistema de Gestión Municipal - Municipalidad de Fortín Lugones';


if (!function_exists('sigenmuniHeaderEscapar')) {

    function sigenmuniHeaderEscapar($valor)
    {
        return htmlspecialchars(
            (string)$valor,
            ENT_QUOTES,
            'UTF-8'
        );
    }
}

?>

<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/compartido/header_sigenmuni.css'), ENT_QUOTES, 'UTF-8') ?>">


<header
    class="sigenmuni-global-header"
    aria-label="Encabezado principal SIGENMUNI"
>

    <div class="sigenmuni-global-header-top">

        <div class="sigenmuni-global-titulo">

            <h1>
                SIGENMUNI
            </h1>

            <p>
                <?php
                echo sigenmuniHeaderEscapar(
                    $sigenmuniHeaderSubtitulo
                );
                ?>
            </p>

        </div>


        <div
            class="sigenmuni-global-usuario"
            aria-label="Usuario autenticado"
        >

            <div class="sigenmuni-global-usuario-nombre">

                👤

                <span id="nombreUsuarioHeader">
                    <?php
                    echo sigenmuniHeaderEscapar(
                        $sigenmuniHeaderNombre
                    );
                    ?>
                </span>

            </div>


            <div class="sigenmuni-global-usuario-rol">

                Rol:

                <span id="rolUsuarioHeader">
                    <?php
                    echo sigenmuniHeaderEscapar(
                        $sigenmuniHeaderRol
                    );
                    ?>
                </span>

            </div>

        </div>

    </div>

</header>
