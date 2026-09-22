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

<style>

/*
|--------------------------------------------------------------------------
| HEADER GLOBAL SIGENMUNI
|--------------------------------------------------------------------------
|
| Las clases llevan el prefijo "sigenmuni-global-" para evitar conflictos
| con estilos propios de cada módulo.
|
|--------------------------------------------------------------------------
*/

.sigenmuni-global-header{
    background:
        linear-gradient(
            135deg,
            #0f766e,
            #14b8a6
        );

    color:white;

    padding:
        22px 30px;

    box-shadow:
        0 4px 14px
        rgba(0,0,0,.10);

    font-family:
        Arial,
        Helvetica,
        sans-serif;
}


.sigenmuni-global-header-top{
    display:flex;

    justify-content:
        space-between;

    align-items:center;

    gap:20px;

    flex-wrap:wrap;
}


.sigenmuni-global-titulo h1{
    margin:0 0 4px 0;

    font-size:30px;

    line-height:1.15;

    letter-spacing:.5px;
}


.sigenmuni-global-titulo p{
    margin:0;

    font-size:14px;

    line-height:1.45;

    opacity:.95;
}


.sigenmuni-global-usuario{
    background:
        rgba(255,255,255,.15);

    padding:
        12px 16px;

    border-radius:14px;

    min-width:280px;

    backdrop-filter:
        blur(4px);
}


.sigenmuni-global-usuario-nombre{
    font-size:16px;

    font-weight:bold;

    margin-bottom:4px;

    line-height:1.35;
}


.sigenmuni-global-usuario-rol{
    font-size:13px;

    line-height:1.35;

    opacity:.95;
}


@media(max-width:992px){

    .sigenmuni-global-header-top{
        flex-direction:column;

        align-items:flex-start;
    }


    .sigenmuni-global-usuario{
        width:100%;

        min-width:0;
    }
}


@media(max-width:768px){

    .sigenmuni-global-header{
        padding:20px;
    }


    .sigenmuni-global-header-top{
        flex-direction:column;

        align-items:stretch;

        gap:15px;
    }


    .sigenmuni-global-titulo h1{
        font-size:24px;
    }


    .sigenmuni-global-titulo p{
        font-size:13px;

        line-height:1.5;
    }


    .sigenmuni-global-usuario{
        width:100%;

        min-width:0;

        padding:14px;
    }


    .sigenmuni-global-usuario-nombre{
        font-size:15px;
    }


    .sigenmuni-global-usuario-rol{
        font-size:12px;
    }
}


@media(max-width:480px){

    .sigenmuni-global-header{
        padding:
            18px 16px;
    }


    .sigenmuni-global-titulo h1{
        font-size:22px;
    }
}

</style>


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
