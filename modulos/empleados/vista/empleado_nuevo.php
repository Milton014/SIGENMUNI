<?php

require_once __DIR__ . '/../../../core/Url.php';
require_once __DIR__ . '/../../../core/Csrf.php';


/*
|--------------------------------------------------------------------------
| NUEVO EMPLEADO - SOLO ROUTER
|--------------------------------------------------------------------------
*/

$empleadoNuevoAccion =
    sigenmuniUrlRuta(
        'empleados/nuevo'
    );


$empleadoNuevoVolver =
    sigenmuniUrlRuta(
        'empleados'
    );


$empleadoNuevoCsrf =
    sigenmuniCsrfToken();


if (!function_exists('formatearCuitUnidadOrganizacion')) {

    function formatearCuitUnidadOrganizacion($cuit)
    {
        $cuit =
            preg_replace(
                '/\D+/',
                '',
                (string)$cuit
            );


        if (strlen($cuit) !== 11) {

            return (string)$cuit;
        }


        return
            substr($cuit, 0, 2)
            . '-'
            . substr($cuit, 2, 8)
            . '-'
            . substr($cuit, 10, 1);
    }
}

?>
<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Nuevo Empleado - SIGENMUNI</title>

<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/empleados/empleado_nuevo.css'), ENT_QUOTES, 'UTF-8') ?>">

</head>

<body>

<div class="header">

    <h1>SIGENMUNI</h1>

    <p>
        Alta de Empleado Municipal
    </p>

</div>

<div class="contenedor">

    <div class="panel">

        <h2>Nuevo Empleado</h2>

        <!-- REFERENCIA DE LEGAJOS -->

        <div class="legajos-referencia">

            <div class="legajo-dato">

                <span class="legajo-etiqueta">
                    Último legajo utilizado
                </span>

                <span class="legajo-valor">
                    <?php
                    echo (int)(
                        $ultimoLegajoUtilizado
                        ?? 0
                    ) > 0
                        ? (int)$ultimoLegajoUtilizado
                        : '-';
                    ?>
                </span>

            </div>


            <div class="legajo-dato">

                <span class="legajo-etiqueta">
                    Próximo legajo sugerido
                </span>

                <span class="legajo-valor">
                    <?php
                    echo (int)(
                        $proximoLegajoSugerido
                        ?? 1
                    );
                    ?>
                </span>

            </div>


            <div class="legajos-ayuda">
                El próximo legajo es una sugerencia basada en el mayor número
                registrado. Puede modificarlo si corresponde; el sistema seguirá
                controlando que no exista un legajo duplicado.
            </div>

        </div>

        <!-- ALERTA JAVASCRIPT -->
        <div
            id="alertaEmpleado"
            class="mensaje error"
            data-estilo="empleado_nuevo-1"
        >
        </div>

        <!-- MENSAJE DEL CONTROLADOR -->
        <?php if (!empty($mensaje)): ?>

            <div
                class="mensaje <?php echo $tipo_mensaje === 'ok' ? 'ok' : 'error'; ?>"
            >
                <?php
                echo htmlspecialchars(
                    $mensaje,
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>
            </div>

        <?php endif; ?>

        <form
            method="POST"
            action="<?php
                echo htmlspecialchars(
                    $empleadoNuevoAccion,
                    ENT_QUOTES,
                    'UTF-8'
                );
            ?>"
            id="formEmpleado"
            novalidate
        >

            <input
                type="hidden"
                name="_csrf"
                value="<?php
                    echo htmlspecialchars(
                        $empleadoNuevoCsrf,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
            >

            <div class="grid">

                <!-- LEGAJO -->

                <div class="campo">

                    <label for="nro_legajo">
                        Legajo *
                    </label>

                    <input
                        type="text"
                        name="nro_legajo"
                        id="nro_legajo"
                        value="<?php echo htmlspecialchars($datos['nro_legajo'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                    >

                </div>


                <!-- APELLIDO -->

                <div class="campo">

                    <label for="apellido">
                        Apellido *
                    </label>

                    <input
                        type="text"
                        name="apellido"
                        id="apellido"
                        value="<?php echo htmlspecialchars($datos['apellido'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                    >

                </div>


                <!-- NOMBRE -->

                <div class="campo">

                    <label for="nombre">
                        Nombre *
                    </label>

                    <input
                        type="text"
                        name="nombre"
                        id="nombre"
                        value="<?php echo htmlspecialchars($datos['nombre'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                    >

                </div>


                <!-- DNI -->

                <div class="campo">

                    <label for="dni">
                        DNI *
                    </label>

                    <input
                        type="text"
                        name="dni"
                        id="dni"
                        value="<?php echo htmlspecialchars($datos['dni'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                    >

                </div>


                <!-- CUIL -->

                <div class="campo">

                    <label for="cuil">
                        CUIL *
                    </label>

                    <input
                        type="text"
                        name="cuil"
                        id="cuil"
                        value="<?php echo htmlspecialchars($datos['cuil'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                    >

                </div>


                <!-- FECHA ALTA -->

                <div class="campo">

                    <label for="fecha_alta">
                        Fecha Alta *
                    </label>

                    <input
                        type="date"
                        name="fecha_alta"
                        id="fecha_alta"
                        value="<?php echo htmlspecialchars($datos['fecha_alta'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                    >

                </div>


                <!-- TELÉFONO -->

                <div class="campo">

                    <label for="telefono">
                        Teléfono
                    </label>

                    <input
                        type="text"
                        name="telefono"
                        id="telefono"
                        value="<?php echo htmlspecialchars($datos['telefono'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                    >

                </div>


                <!-- EMAIL -->

                <div class="campo">

                    <label for="email">
                        Email *
                    </label>

                    <input
                        type="email"
                        name="email"
                        id="email"
                        value="<?php echo htmlspecialchars($datos['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                    >

                </div>


                <!-- DOMICILIO -->

                <div class="campo">

                    <label for="domicilio">
                        Domicilio
                    </label>

                    <input
                        type="text"
                        name="domicilio"
                        id="domicilio"
                        value="<?php echo htmlspecialchars($datos['domicilio'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                    >

                </div>


                <!-- INSTITUCIÓN -->

                <div class="campo">

                    <label for="institucion_id">
                        Institución *
                    </label>

                    <select
                        name="institucion_id"
                        id="institucion_id"
                    >

                        <option value="">
                            Seleccione
                        </option>

                        <?php foreach ($instituciones as $x): ?>

                            <option
                                value="<?php echo (int)$x['id']; ?>"
                                <?php
                                echo (
                                    (string)($datos['institucion_id'] ?? '')
                                    ===
                                    (string)$x['id']
                                ) ? 'selected' : '';
                                ?>
                            >
                                <?php
                                echo htmlspecialchars(
                                    $x['nombre'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <!-- UNIDAD DE ORGANIZACIÓN -->

                <div class="campo">

                    <label for="oficina_id">
                        Unidad de Organización *
                    </label>

                    <select
                        name="oficina_id"
                        id="oficina_id"
                    >

                        <option value="">
                            Seleccione
                        </option>

                        <?php foreach ($oficinas as $x): ?>

                            <option
                                value="<?php echo (int)$x['id']; ?>"
                                <?php
                                echo (
                                    (string)($datos['oficina_id'] ?? '')
                                    ===
                                    (string)$x['id']
                                ) ? 'selected' : '';
                                ?>
                            >
                                <?php
                                $nombreUnidad =
                                    $x['nombre']
                                    ?? '';

                                $cuitUnidad =
                                    $x['cuit']
                                    ?? '';

                                $textoUnidad =
                                    $nombreUnidad;

                                if ($cuitUnidad !== '') {

                                    $textoUnidad .=
                                        ' - CUIT '
                                        . formatearCuitUnidadOrganizacion(
                                            $cuitUnidad
                                        );
                                }

                                echo htmlspecialchars(
                                    $textoUnidad,
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                    <div class="ayuda-campo">
                        Seleccione la Unidad de Organización correspondiente.
                        El CUIT se muestra junto al nombre.
                    </div>

                </div>


                <!-- SITUACIÓN -->

                <div class="campo">

                    <label for="situacion_id">
                        Situación *
                    </label>

                    <select
                        name="situacion_id"
                        id="situacion_id"
                    >

                        <option value="">
                            Seleccione
                        </option>

                        <?php foreach ($situaciones as $x): ?>

                            <option
                                value="<?php echo (int)$x['id']; ?>"
                                <?php
                                echo (
                                    (string)($datos['situacion_id'] ?? '')
                                    ===
                                    (string)$x['id']
                                ) ? 'selected' : '';
                                ?>
                            >
                                <?php
                                echo htmlspecialchars(
                                    $x['nombre'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <!-- ESCALAFÓN -->

                <div class="campo">

                    <label
                        for="escalafon_id"
                        id="label_escalafon"
                    >
                        Escalafón *
                    </label>

                    <select
                        name="escalafon_id"
                        id="escalafon_id"
                    >

                        <option value="">
                            Seleccione
                        </option>

                        <?php foreach ($escalafones as $x): ?>

                            <option
                                value="<?php echo (int)$x['id']; ?>"
                                <?php
                                echo (
                                    (string)($datos['escalafon_id'] ?? '')
                                    ===
                                    (string)$x['id']
                                ) ? 'selected' : '';
                                ?>
                            >
                                <?php
                                echo htmlspecialchars(
                                    $x['nombre'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                    <div
                        class="ayuda-campo"
                        id="ayuda_escalafon"
                    >
                        Obligatorio para categorías generales.
                    </div>

                </div>


                <!-- CATEGORÍA -->

                <div class="campo">

                    <label for="categoria_id">
                        Categoría *
                    </label>

                    <select
                        name="categoria_id"
                        id="categoria_id"
                    >

                        <option value="">
                            Seleccione
                        </option>

                        <?php foreach ($categorias as $x): ?>

                            <option
                                value="<?php echo (int)$x['id']; ?>"
                                data-codigo="<?php echo (int)($x['codigo'] ?? 0); ?>"
                                <?php
                                echo (
                                    (string)($datos['categoria_id'] ?? '')
                                    ===
                                    (string)$x['id']
                                ) ? 'selected' : '';
                                ?>
                            >
                                <?php
                                echo htmlspecialchars(
                                    (
                                        isset($x['codigo'])
                                        ?
                                        $x['codigo'] . ' - '
                                        :
                                        ''
                                    )
                                    .
                                    $x['nombre'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <!-- OBSERVACIONES -->

                <div
                    class="campo"
                    data-estilo="empleado_nuevo-2"
                >

                    <label for="observaciones">
                        Observaciones
                    </label>

                    <textarea
                        name="observaciones"
                        id="observaciones"
                    ><?php echo htmlspecialchars($datos['observaciones'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>

                </div>

            </div>


            <!-- BOTONES -->

            <div class="acciones">

                <button
                    type="submit"
                    class="btn"
                >
                    Guardar Empleado
                </button>

                <a
                    href="<?php
                        echo htmlspecialchars(
                            $empleadoNuevoVolver,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                    class="btn btn-sec"
                >
                    Cancelar
                </a>

            </div>

        </form>

    </div>

</div>


<script src="<?= htmlspecialchars(sigenmuniUrlAsset('js/empleados/empleado_nuevo.js'), ENT_QUOTES, 'UTF-8') ?>"></script>

</body>

</html>