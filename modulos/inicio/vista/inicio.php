<?php

/*
|--------------------------------------------------------------------------
| VISTA - INICIO / MENÚ PRINCIPAL
|--------------------------------------------------------------------------
|
| Las variables de sesión, rol, permisos y módulos visibles son preparadas
| por InicioControlador.
|
|--------------------------------------------------------------------------
*/

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGENMUNI - Menú Principal</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --color-principal: #0f766e;
            --color-principal-hover: #115e59;
            --color-secundario: #14b8a6;
            --color-fondo: #f4f7fb;
            --color-texto: #1f2937;
            --color-blanco: #ffffff;
            --color-borde: #e5e7eb;
            --color-sombra: rgba(0, 0, 0, 0.10);
            --color-header: linear-gradient(135deg, #0f766e, #14b8a6);
            --color-gris: #6b7280;
            --color-ayuda: #2563eb;
            --color-usuarios: #7c3aed;
            --color-reportes: #ea580c;
            --color-conceptos: #0891b2;
            --color-categorias: #7c3aed;
            --color-liquidacion: #16a34a;
            --color-empleados: #0f766e;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: var(--color-fondo);
            color: var(--color-texto);
        }

        .contenedor {
            width: 92%;
            max-width: 1250px;
            margin: 30px auto;
        }

        .panel-superior {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 25px;
        }

        .bienvenida h2 {
            font-size: 28px;
            margin-bottom: 6px;
        }

        .bienvenida p {
            color: var(--color-gris);
            font-size: 15px;
        }

        .acciones-superiores {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-top {
            text-decoration: none;
            padding: 12px 18px;
            border-radius: 12px;
            border: none;
            color: white;
            font-family: inherit;
            font-size: inherit;
            font-weight: bold;
            cursor: pointer;
            transition: 0.25s ease;
            box-shadow: 0 6px 14px var(--color-sombra);
        }

        .form-logout {
            margin: 0;
        }

        .btn-inicio {
            background: var(--color-principal);
        }

        .btn-inicio:hover {
            background: var(--color-principal-hover);
            transform: translateY(-2px);
        }

        .btn-logout {
            background: #1f2937;
        }

        .btn-logout:hover {
            background: #111827;
            transform: translateY(-2px);
        }

        .buscador-box {
            background: var(--color-blanco);
            padding: 18px;
            border-radius: 18px;
            box-shadow: 0 8px 20px var(--color-sombra);
            margin-bottom: 28px;
            border: 1px solid var(--color-borde);
        }

        .buscador-box label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .buscador-box input {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            font-size: 15px;
            outline: none;
            transition: 0.2s;
        }

        .buscador-box input:focus {
            border-color: var(--color-secundario);
            box-shadow: 0 0 0 3px rgba(20, 184, 166, 0.15);
        }

        .grid-modulos {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 22px;
        }

        .card-modulo {
            position: relative;
            background: var(--color-blanco);
            border-radius: 22px;
            padding: 24px 22px;
            box-shadow: 0 10px 24px var(--color-sombra);
            border: 1px solid var(--color-borde);
            transition: 0.25s ease;
            overflow: hidden;
            min-height: 220px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .card-modulo:hover {
            transform: translateY(-7px);
            box-shadow: 0 18px 32px rgba(0,0,0,0.14);
        }

        .card-modulo.oculto {
            display: none;
        }

        .franja {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 8px;
        }

        .empleados .franja { background: var(--color-empleados); }
        .conceptos .franja { background: var(--color-conceptos); }
        .categorias .franja { background: var(--color-categorias); }
        .liquidacion .franja { background: var(--color-liquidacion); }
        .reportes .franja { background: var(--color-reportes); }
        .usuarios .franja { background: var(--color-usuarios); }
        .ayuda .franja { background: var(--color-ayuda); }

        .icono {
            width: 58px;
            height: 58px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-bottom: 18px;
            color: white;
        }

        .empleados .icono { background: var(--color-empleados); }
        .conceptos .icono { background: var(--color-conceptos); }
        .categorias .icono { background: var(--color-categorias); }
        .liquidacion .icono { background: var(--color-liquidacion); }
        .reportes .icono { background: var(--color-reportes); }
        .usuarios .icono { background: var(--color-usuarios); }
        .ayuda .icono { background: var(--color-ayuda); }

        .card-modulo h3 {
            font-size: 21px;
            margin-bottom: 10px;
        }

        .card-modulo p {
            color: var(--color-gris);
            line-height: 1.45;
            font-size: 14px;
            margin-bottom: 18px;
        }

        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            margin-top: 8px;
        }

        .estado {
            font-size: 12px;
            font-weight: bold;
            background: #ecfdf5;
            color: #166534;
            padding: 7px 10px;
            border-radius: 999px;
        }

        .estado-admin {
            background: #fef3c7;
            color: #92400e;
        }

        .btn-modulo {
            text-decoration: none;
            color: white;
            font-weight: bold;
            padding: 10px 14px;
            border-radius: 10px;
            transition: 0.2s;
            display: inline-block;
        }

        .empleados .btn-modulo { background: var(--color-empleados); }
        .conceptos .btn-modulo { background: var(--color-conceptos); }
        .categorias .btn-modulo { background: var(--color-categorias); }
        .liquidacion .btn-modulo { background: var(--color-liquidacion); }
        .reportes .btn-modulo { background: var(--color-reportes); }
        .usuarios .btn-modulo { background: var(--color-usuarios); }
        .ayuda .btn-modulo { background: var(--color-ayuda); }

        .btn-modulo:hover {
            opacity: 0.92;
            transform: scale(1.03);
        }

        .sin-resultados {
            display: none;
            background: white;
            padding: 18px;
            border-radius: 16px;
            margin-top: 20px;
            text-align: center;
            color: var(--color-gris);
            box-shadow: 0 8px 20px var(--color-sombra);
        }

        .sin-modulos {
            background: white;
            padding: 34px 24px;
            border-radius: 20px;
            text-align: center;
            color: var(--color-gris);
            box-shadow: 0 10px 24px var(--color-sombra);
            border: 1px solid var(--color-borde);
        }

        .sin-modulos .icono-vacio {
            width: 64px;
            height: 64px;
            margin: 0 auto 16px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            background: #f3f4f6;
        }

        .sin-modulos h3 {
            color: var(--color-texto);
            font-size: 22px;
            margin-bottom: 10px;
        }

        .sin-modulos p {
            line-height: 1.6;
            margin-bottom: 18px;
        }

        .sin-modulos .btn-salir {
            display: inline-block;
            text-decoration: none;
            background: #1f2937;
            color: white;
            padding: 11px 16px;
            border-radius: 10px;
            font-weight: bold;
        }

        .sin-modulos .btn-salir:hover {
            background: #111827;
        }

        .footer {
            text-align: center;
            padding: 30px 20px 40px;
            color: var(--color-gris);
            font-size: 13px;
        }

        @media (max-width: 992px) {
            .contenedor {
                width: 95%;
            }

            .grid-modulos {
                grid-template-columns: repeat(2, 1fr);
            }

        }

        @media (max-width: 768px) {

            .contenedor {
                width: 94%;
                margin: 20px auto;
            }

            .panel-superior {
                flex-direction: column;
                align-items: stretch;
                gap: 20px;
            }

            .bienvenida h2 {
                font-size: 24px;
            }

            .bienvenida p {
                font-size: 14px;
                line-height: 1.5;
            }

            .acciones-superiores {
                flex-direction: column;
                align-items: stretch;
            }

            .btn-top {
                width: 100%;
                text-align: center;
            }

            .buscador-box {
                padding: 16px;
                border-radius: 16px;
            }

            .buscador-box input {
                font-size: 14px;
                padding: 13px;
            }

            .grid-modulos {
                grid-template-columns: 1fr;
                gap: 18px;
            }

            .card-modulo {
                min-height: auto;
                padding: 22px 18px;
                border-radius: 18px;
            }

            .icono {
                width: 52px;
                height: 52px;
                font-size: 24px;
                margin-bottom: 14px;
            }

            .card-modulo h3 {
                font-size: 20px;
            }

            .card-modulo p {
                font-size: 14px;
                line-height: 1.5;
            }

            .card-footer {
                flex-direction: column;
                align-items: stretch;
                gap: 12px;
            }

            .estado,
            .estado-admin {
                text-align: center;
            }

            .btn-modulo {
                width: 100%;
                text-align: center;
            }

            .footer {
                padding: 25px 15px 35px;
                font-size: 12px;
            }
        }

        @media (max-width: 480px) {

            .bienvenida h2 {
                font-size: 22px;
            }

            .buscador-box {
                padding: 14px;
            }

            .card-modulo {
                padding: 18px 16px;
            }

            .card-modulo h3 {
                font-size: 18px;
            }

            .card-modulo p {
                font-size: 13px;
            }

            .btn-top,
            .btn-modulo {
                font-size: 13px;
                padding: 11px;
            }

            .estado,
            .estado-admin {
                font-size: 11px;
            }
        }
    </style>
</head>

<body>

<?php

/*
|--------------------------------------------------------------------------
| HEADER GLOBAL
|--------------------------------------------------------------------------
*/

$sigenmuniHeaderSubtitulo =
    'Sistema de Gestión Municipal - Municipalidad de Fortín Lugones';

require __DIR__
    . '/../../../componentes/header_sigenmuni.php';

?>

<div class="contenedor">

    <div class="panel-superior">

        <div class="bienvenida">
            <h2>Menú Principal</h2>

            <?php if ($totalModulosVisibles > 0): ?>

                <p>
                    Seleccioná un módulo habilitado para comenzar a trabajar en el sistema.
                </p>

            <?php else: ?>

                <p>
                    Actualmente no tenés módulos habilitados para este rol.
                </p>

            <?php endif; ?>
        </div>

        <div class="acciones-superiores">

            <form
                method="POST"
                action="<?php
                    echo htmlspecialchars(
                        $inicioUrlLogout,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                class="form-logout"
            >

                <input
                    type="hidden"
                    name="_csrf"
                    value="<?php
                        echo htmlspecialchars(
                            $inicioCsrfLogout,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                >

                <button
                    type="submit"
                    class="btn-top btn-logout"
                >
                    Cerrar sesión
                </button>

            </form>

        </div>

    </div>

    <?php if ($totalModulosVisibles > 0): ?>

        <div class="buscador-box">

            <label for="buscadorModulos">
                Buscar módulo
            </label>

            <input
                type="text"
                id="buscadorModulos"
                placeholder="Escribí por ejemplo: empleados, reportes, ayuda..."
                autocomplete="off"
            >

        </div>

        <div class="grid-modulos" id="gridModulos">

            <?php foreach ($modulosVisibles as $modulo): ?>

                <div
                    class="card-modulo <?php
                        echo htmlspecialchars(
                            $modulo['clase'],
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                    data-nombre="<?php
                        echo htmlspecialchars(
                            $modulo['keywords'],
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                >

                    <div class="franja"></div>

                    <div>

                        <div class="icono">
                            <?php
                            echo $modulo['icono'];
                            ?>
                        </div>

                        <h3>
                            <?php
                            echo htmlspecialchars(
                                $modulo['nombre'],
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>
                        </h3>

                        <p>
                            <?php
                            echo htmlspecialchars(
                                $modulo['descripcion'],
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>
                        </p>

                    </div>

                    <div class="card-footer">

                        <?php if ($modulo['admin']): ?>

                            <span class="estado estado-admin">
                                Configurable
                            </span>

                        <?php else: ?>

                            <span class="estado">
                                Disponible
                            </span>

                        <?php endif; ?>

                        <a
                            href="<?php
                                echo htmlspecialchars(
                                    $modulo['url'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                            ?>"
                            class="btn-modulo"
                        >
                            Ingresar
                        </a>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

        <div class="sin-resultados" id="sinResultados">
            No se encontraron módulos con ese nombre.
        </div>

    <?php else: ?>

        <div class="sin-modulos">

            <div class="icono-vacio">
                🔒
            </div>

            <h3>
                No tenés módulos habilitados
            </h3>

            <p>
                El rol
                <strong>
                    <?php
                    echo htmlspecialchars(
                        $rol,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>
                </strong>
                no posee permisos de acceso a ningún módulo actualmente.
                Consultá con un administrador del sistema si necesitás acceso.
            </p>


        </div>

    <?php endif; ?>

</div>

<div class="footer">
    SIGENMUNI · Sistema de Gestión Municipal
</div>

<script>

/*
|--------------------------------------------------------------------------
| CACHÉ VISUAL DE LA SESIÓN CON LOCALSTORAGE
|--------------------------------------------------------------------------
|
| IMPORTANTE:
| localStorage NO reemplaza la sesión PHP.
|
| La sesión PHP sigue siendo la fuente real de autenticación, permisos
| y seguridad. En localStorage solo guardamos información visual no sensible
| para mostrar el usuario autenticado en la interfaz.
|
|--------------------------------------------------------------------------
*/

    const CLAVE_CACHE_SESION =
        'sigenmuni_sesion';


    const sesionServidor = {

        id_usuario:
            <?php
                echo json_encode(
                    $idUsuario
                );
            ?>,

        usuario:
            <?php
                echo json_encode(
                    $usuarioSesion,
                    JSON_UNESCAPED_UNICODE
                );
            ?>,

        nombre_completo:
            <?php
                echo json_encode(
                    $nombreCompleto,
                    JSON_UNESCAPED_UNICODE
                );
            ?>,

        rol:
            <?php
                echo json_encode(
                    $rol,
                    JSON_UNESCAPED_UNICODE
                );
            ?>,

        actualizado_en:
            new Date().toISOString()
    };


    /*
    |--------------------------------------------------------------------------
    | GUARDAR / ACTUALIZAR CACHÉ
    |--------------------------------------------------------------------------
    */

    try {

        localStorage.setItem(
            CLAVE_CACHE_SESION,
            JSON.stringify(
                sesionServidor
            )
        );

    } catch (error) {

        console.warn(
            'No fue posible guardar la caché de sesión en localStorage.',
            error
        );
    }


    /*
    |--------------------------------------------------------------------------
    | LEER CACHÉ Y MOSTRAR NOMBRE + ROL
    |--------------------------------------------------------------------------
    */

    try {

        const cacheGuardada =
            localStorage.getItem(
                CLAVE_CACHE_SESION
            );


        if (cacheGuardada) {

            const datosCache =
                JSON.parse(
                    cacheGuardada
                );


            const nombreUsuarioHeader =
                document.getElementById(
                    'nombreUsuarioHeader'
                );


            const rolUsuarioHeader =
                document.getElementById(
                    'rolUsuarioHeader'
                );


            if (
                nombreUsuarioHeader
                &&
                datosCache.nombre_completo
            ) {

                nombreUsuarioHeader.textContent =
                    datosCache.nombre_completo;
            }


            if (
                rolUsuarioHeader
                &&
                datosCache.rol
            ) {

                rolUsuarioHeader.textContent =
                    datosCache.rol;
            }
        }

    } catch (error) {

        /*
        | Si el contenido de la caché estuviera corrupto,
        | se elimina. La página sigue mostrando los datos
        | renderizados por PHP.
        */

        localStorage.removeItem(
            CLAVE_CACHE_SESION
        );

        console.warn(
            'La caché de sesión era inválida y fue eliminada.',
            error
        );
    }


/*
|--------------------------------------------------------------------------
| BUSCADOR DE MÓDULOS
|--------------------------------------------------------------------------
*/

    const buscador =
        document.getElementById(
            'buscadorModulos'
        );

    const tarjetas =
        document.querySelectorAll(
            '.card-modulo'
        );

    const sinResultados =
        document.getElementById(
            'sinResultados'
        );


    if (
        buscador
        &&
        sinResultados
    ) {

        buscador.addEventListener(
            'input',
            function ()
            {

                const texto =
                    this.value
                        .toLowerCase()
                        .trim();

                let visibles =
                    0;


                tarjetas.forEach(
                    function(tarjeta)
                    {

                        const nombre =
                            (
                                tarjeta.getAttribute(
                                    'data-nombre'
                                )
                                ??
                                ''
                            )
                            .toLowerCase();


                        if (
                            nombre.includes(
                                texto
                            )
                        ) {

                            tarjeta
                                .classList
                                .remove(
                                    'oculto'
                                );

                            visibles++;

                        } else {

                            tarjeta
                                .classList
                                .add(
                                    'oculto'
                                );
                        }
                    }
                );


                sinResultados.style.display =
                    visibles === 0
                        ?
                        'block'
                        :
                        'none';
            }
        );
    }
</script>

</body>
</html>