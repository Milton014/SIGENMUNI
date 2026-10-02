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

    <link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/inicio/inicio.css'), ENT_QUOTES, 'UTF-8') ?>">
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

<script type="application/json" id="datos-inicio"><?= sigenmuniJsonVista([
    'idUsuario' => $idUsuario,
    'usuarioSesion' => $usuarioSesion,
    'nombreCompleto' => $nombreCompleto,
    'rol' => $rol
]) ?></script>
<script src="<?= htmlspecialchars(sigenmuniUrlAsset('js/inicio/inicio.js'), ENT_QUOTES, 'UTF-8') ?>"></script>

</body>
</html>