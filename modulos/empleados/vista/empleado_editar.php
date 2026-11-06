<?php

require_once __DIR__ . '/../../../core/Url.php';
require_once __DIR__ . '/../../../core/Csrf.php';


/*
|--------------------------------------------------------------------------
| EDITAR EMPLEADO - SOLO ROUTER
|--------------------------------------------------------------------------
*/

$empleadoEditarId =
    (int)(
        $empleado['id']
        ?? 0
    );


$empleadoEditarAccion =
    sigenmuniUrlRuta(
        'empleados/editar',
        array_merge($contextoListado ?? [], [
            'id' => $empleadoEditarId
        ])
    );


$empleadoEditarCancelar =
    sigenmuniUrlRuta(
        'empleados',
        $contextoListado ?? []
    );


$empleadoEditarCsrf =
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

<title>Editar Empleado - SIGENMUNI</title>

<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/empleados/empleado_editar.css'), ENT_QUOTES, 'UTF-8') ?>">

</head>

<body>

<div class="header">

    <h1>SIGENMUNI</h1>

    <p>
        Edición de Empleado Municipal
    </p>

</div>


<div class="contenedor">

    <div class="panel">

        <h2>
            Editar Empleado
        </h2>


        <!-- =====================================
             ALERTA JAVASCRIPT
        ====================================== -->

        <div
            id="alertaEmpleado"
            class="mensaje error"
            data-estilo="empleado_editar-1"
        >
        </div>


        <!-- =====================================
             MENSAJE DEL CONTROLADOR
        ====================================== -->

        <?php if (!empty($mensaje)): ?>

            <div class="mensaje error">

                <?php
                echo htmlspecialchars(
                    $mensaje,
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>

            </div>

        <?php endif; ?>


        <!-- =====================================
             FORMULARIO
        ====================================== -->

        <form
            method="POST"
            action="<?php
                echo htmlspecialchars(
                    $empleadoEditarAccion,
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
                        $empleadoEditarCsrf,
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
                        value="<?php echo htmlspecialchars(
                            $empleado['nro_legajo'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>"
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
                        value="<?php echo htmlspecialchars(
                            $empleado['apellido'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>"
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
                        value="<?php echo htmlspecialchars(
                            $empleado['nombre'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>"
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
                        value="<?php echo htmlspecialchars(
                            $empleado['dni'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>"
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
                        value="<?php echo htmlspecialchars(
                            $empleado['cuil'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>"
                    >

                </div>


                <!-- FECHA DE ALTA INICIAL -->

                <div class="campo">

                    <label for="fecha_alta">
                        Fecha de Alta Inicial
                    </label>

                    <input
                        type="date"
                        id="fecha_alta"
                        value="<?php echo htmlspecialchars(
                            $empleado['fecha_alta'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>"
                        readonly
                    >

                    <div class="ayuda-campo">
                        Corresponde al alta laboral inicial y está vinculada al
                        historial laboral. No se modifica desde esta pantalla.
                    </div>

                </div>


                <!-- FECHA INACTIVO -->

                <div class="campo">

                    <label for="fecha_inactivo_visual">
                        Fecha Inactivo
                    </label>

                    <?php

                    $fechaInactivoVisual =
                        !empty($empleado['fecha_inactivo'])
                        &&
                        $empleado['fecha_inactivo'] !== '0000-00-00'
                            ?
                            date(
                                'd/m/Y',
                                strtotime(
                                    $empleado['fecha_inactivo']
                                )
                            )
                            :
                            '-';

                    ?>

                    <input
                        type="text"
                        id="fecha_inactivo_visual"
                        value="<?php echo htmlspecialchars(
                            $fechaInactivoVisual,
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>"
                        readonly
                    >

                    <div class="ayuda-campo">
                        La fecha se registra automáticamente al inactivar al empleado.
                        No se modifica desde esta pantalla.
                    </div>

                    <!--
                    Se conserva fecha_baja como dato legacy para evitar que
                    una edición del empleado elimine accidentalmente un valor
                    histórico existente mientras el controlador todavía mantiene
                    compatibilidad con esa columna.
                    -->
                    <input
                        type="hidden"
                        name="fecha_baja"
                        value="<?php echo htmlspecialchars(
                            $empleado['fecha_baja'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>"
                    >

                </div>


                <!-- NORMA LEGAL DEL ALTA INICIAL -->
                <div class="campo">
                    <label for="norma_legal_alta">Norma legal de alta</label>
                    <input type="text" name="norma_legal_alta" id="norma_legal_alta"
                        maxlength="100" placeholder="Ej.: Decreto N.º 123/2026"
                        aria-describedby="ayuda_norma_legal_alta"
                        value="<?= htmlspecialchars($empleado['norma_legal_alta'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
                    <div class="ayuda-campo" id="ayuda_norma_legal_alta">
                        Indique Decreto o Resolución, número y año del alta inicial.
                    </div>
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
                        value="<?php echo htmlspecialchars(
                            $empleado['telefono'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>"
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
                        value="<?php echo htmlspecialchars(
                            $empleado['email'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>"
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
                        value="<?php echo htmlspecialchars(
                            $empleado['domicilio'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>"
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
                                    (string)($empleado['institucion_id'] ?? '')
                                    ===
                                    (string)$x['id']
                                )
                                    ? 'selected'
                                    : '';
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
                                    (string)($empleado['oficina_id'] ?? '')
                                    ===
                                    (string)$x['id']
                                )
                                    ? 'selected'
                                    : '';
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
                                    (string)($empleado['situacion_id'] ?? '')
                                    ===
                                    (string)$x['id']
                                )
                                    ? 'selected'
                                    : '';
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
                                    (string)($empleado['escalafon_id'] ?? '')
                                    ===
                                    (string)$x['id']
                                )
                                    ? 'selected'
                                    : '';
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
                                    (string)($empleado['categoria_id'] ?? '')
                                    ===
                                    (string)$x['id']
                                )
                                    ? 'selected'
                                    : '';
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
                    data-estilo="empleado_editar-2"
                >

                    <label for="observaciones">
                        Observaciones
                    </label>

                    <textarea
                        name="observaciones"
                        id="observaciones"
                    ><?php echo htmlspecialchars(
                        $empleado['observaciones'] ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?></textarea>

                </div>

            </div>


            <!-- =====================================
                 ACCIONES
            ====================================== -->

            <div class="acciones">

                <button
                    type="submit"
                    class="btn"
                >
                    Actualizar Empleado
                </button>

                <a
                    href="<?php
                        echo htmlspecialchars(
                            $empleadoEditarCancelar,
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


<script src="<?= htmlspecialchars(sigenmuniUrlAsset('js/empleados/empleado_editar.js'), ENT_QUOTES, 'UTF-8') ?>"></script>

</body>

</html>