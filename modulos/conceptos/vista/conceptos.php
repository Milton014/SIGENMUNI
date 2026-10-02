<?php

/*
|--------------------------------------------------------------------------
| GESTIÓN DE CONCEPTOS - SOLO ROUTER
|--------------------------------------------------------------------------
|
| Todas las operaciones de Gestión de Conceptos y Valores de Conceptos
| ingresan mediante public/index.php y las rutas registradas del módulo.
|
| "conceptos.php" continúa existiendo únicamente como clave de permiso;
| ya no se utiliza como archivo de navegación.
|
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../../../core/Url.php';
require_once __DIR__ . '/../../../core/Csrf.php';


$conceptosUrlListado =
    sigenmuniUrlRuta(
        'conceptos'
    );


$conceptosAccionBuscar =
    sigenmuniUrlEntrada();


$conceptosUrlNuevo =
    sigenmuniUrlRuta(
        'conceptos/nuevo'
    );


$conceptosUrlValores =
    sigenmuniUrlRuta(
        'conceptos/valores'
    );


$conceptosUrlEstado =
    sigenmuniUrlRuta(
        'conceptos/estado'
    );


$conceptosCsrfToken =
    sigenmuniCsrfToken();


$conceptosUrlMenu =
    sigenmuniUrlRuta(
    'inicio'
);

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Gestión de Conceptos - SIGENMUNI</title>

<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/conceptos/conceptos.css'), ENT_QUOTES, 'UTF-8') ?>">

</head>


<body>


<!-- =========================================
     HEADER GLOBAL
========================================= -->

<?php

$sigenmuniHeaderSubtitulo =
    'Gestión de Conceptos de Liquidación';

require __DIR__
    . '/../../../componentes/header_sigenmuni.php';

?>


<!-- =========================================
     CONTENIDO
========================================= -->

<div class="contenedor">

    <div class="panel">


        <!-- =====================================
             TOPBAR
        ====================================== -->

        <div class="topbar">

            <h2>
                Gestión de Conceptos
            </h2>

            <div class="acciones-superiores">

                <a
                    href="<?php echo htmlspecialchars($conceptosUrlNuevo, ENT_QUOTES, 'UTF-8'); ?>"
                    class="btn btn-nuevo"
                >
                    + Nuevo Concepto
                </a>

                <a
                    href="<?php echo htmlspecialchars($conceptosUrlValores, ENT_QUOTES, 'UTF-8'); ?>"
                    class="btn btn-valores"
                >
                    📋 Ver todos los valores
                </a>

                <a
                    href="<?php echo htmlspecialchars($conceptosUrlMenu, ENT_QUOTES, 'UTF-8'); ?>"
                    class="btn btn-volver"
                >
                    Volver al menú
                </a>

            </div>

        </div>


        <!-- =====================================
             MENSAJES
        ====================================== -->

        <?php if (!empty($mensaje)): ?>

            <div
                class="mensaje <?php
                    echo $tipo_mensaje === 'ok'
                        ? 'mensaje-ok'
                        : 'mensaje-error';
                ?>"
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


        <!-- =====================================
             FILTROS
        ====================================== -->

        <form
            method="GET"
            action="<?php echo htmlspecialchars($conceptosAccionBuscar, ENT_QUOTES, 'UTF-8'); ?>"
            class="filtros"
        >

            <input
                type="hidden"
                name="r"
                value="conceptos"
            >

            <!-- BUSCAR -->

            <input
                type="text"
                name="buscar"
                placeholder="Buscar por código o nombre"
                value="<?php
                    echo htmlspecialchars(
                        $buscar ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
            >


            <!-- TIPO DE CONCEPTO -->

            <select name="categoria">

                <option value="">
                    -- Todos los tipos de concepto --
                </option>

                <?php foreach ($categorias as $cat): ?>

                    <option
                        value="<?php
                            echo htmlspecialchars(
                                $cat,
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                        <?php
                        echo ($categoria === $cat)
                            ? 'selected'
                            : '';
                        ?>
                    >

                        <?php
                        echo htmlspecialchars(
                            $cat,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>

                    </option>

                <?php endforeach; ?>

            </select>


            <!-- ESTADO -->

            <select name="activo">

                <option value="">
                    -- Todos los estados --
                </option>

                <option
                    value="1"
                    <?php
                    echo ($activo === '1')
                        ? 'selected'
                        : '';
                    ?>
                >
                    Activos
                </option>

                <option
                    value="0"
                    <?php
                    echo ($activo === '0')
                        ? 'selected'
                        : '';
                    ?>
                >
                    Inactivos
                </option>

            </select>


            <!-- BUSCAR -->

            <button
                type="submit"
                class="btn btn-buscar"
            >
                Buscar
            </button>


            <!-- LIMPIAR -->

            <a
                href="<?php echo htmlspecialchars($conceptosUrlListado, ENT_QUOTES, 'UTF-8'); ?>"
                class="btn btn-limpiar"
            >
                Limpiar
            </a>

        </form>


        <!-- =====================================
             TABLA
        ====================================== -->

        <?php if (!empty($conceptos)): ?>

            <div class="tabla-contenedor">

                <table>

                    <thead>

                        <tr>

                            <th>Código</th>

                            <th>Nombre</th>

                            <th>Tipo de Concepto</th>

                            <th>Forma de Cálculo</th>

                            <th>Base de Cálculo</th>

                            <th>SAC</th>

                            <th>Recibo</th>

                            <th>Estado</th>

                            <th>Vigencia</th>

                            <th>Acciones</th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php foreach ($conceptos as $fila): ?>

                        <?php

                        /*
                        |--------------------------------------------------------------------------
                        | BADGE SEGÚN TIPO DE CONCEPTO
                        |--------------------------------------------------------------------------
                        */

                        $badgeClass = '';

                        switch ($fila['categoria']) {

                            case 'REMUNERATIVO':
                                $badgeClass = 'badge-rem';
                                break;

                            case 'NO_REMUNERATIVO':
                                $badgeClass = 'badge-no-rem';
                                break;

                            case 'ASIGNACION_FAMILIAR':
                                $badgeClass = 'badge-asig';
                                break;

                            case 'DESCUENTO':
                                $badgeClass = 'badge-desc';
                                break;

                            case 'APORTE_PATRONAL':
                                $badgeClass = 'badge-aporte';
                                break;
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | VIGENCIA
                        |--------------------------------------------------------------------------
                        */

                        $fechaDesde = '-';

                        $fechaHasta = '-';

                        if (!empty($fila['fecha_desde'])) {

                            $fechaDesde = date(
                                'd/m/Y',
                                strtotime(
                                    $fila['fecha_desde']
                                )
                            );
                        }

                        if (!empty($fila['fecha_hasta'])) {

                            $fechaHasta = date(
                                'd/m/Y',
                                strtotime(
                                    $fila['fecha_hasta']
                                )
                            );
                        }

                        ?>


                        <tr>


                            <!-- CÓDIGO -->

                            <td
                                class="col-codigo"
                                data-label="Código"
                            >
                                <?php
                                echo htmlspecialchars(
                                    $fila['codigo'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>
                            </td>


                            <!-- NOMBRE -->

                            <td
                                class="col-nombre"
                                data-label="Nombre"
                            >
                                <?php
                                echo htmlspecialchars(
                                    $fila['nombre'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>
                            </td>


                            <!-- TIPO DE CONCEPTO -->

                            <td data-label="Tipo de Concepto">

                                <span
                                    class="badge <?php echo $badgeClass; ?>"
                                >

                                    <?php
                                    echo htmlspecialchars(
                                        $fila['categoria'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>

                                </span>

                            </td>


                            <!-- FORMA CÁLCULO -->

                            <td data-label="Forma de Cálculo">

                                <?php

                                $formaCalculoInterna =
                                    strtoupper(
                                        trim(
                                            (string)(
                                                $fila['forma_calculo']
                                                ?? ''
                                            )
                                        )
                                    );

                                switch ($formaCalculoInterna) {

                                    case 'FORMULA':
                                        $formaCalculoVisual =
                                            'AUTOMÁTICO';
                                        break;

                                    case 'TABLA_CATEGORIA':
                                        $formaCalculoVisual =
                                            'VALOR POR CATEGORÍA';
                                        break;

                                    case 'MANUAL':
                                        $formaCalculoVisual =
                                            'MANUAL';
                                        break;

                                    case 'PORCENTAJE':
                                        $formaCalculoVisual =
                                            'PORCENTAJE';
                                        break;

                                    case 'FIJO':
                                        /*
                                        | Compatibilidad visual con conceptos
                                        | históricos anteriores a la nueva
                                        | arquitectura.
                                        */
                                        $formaCalculoVisual =
                                            'MANUAL';
                                        break;

                                    default:
                                        $formaCalculoVisual =
                                            $formaCalculoInterna !== ''
                                                ? $formaCalculoInterna
                                                : '-';
                                        break;
                                }

                                echo htmlspecialchars(
                                    $formaCalculoVisual,
                                    ENT_QUOTES,
                                    'UTF-8'
                                );

                                ?>

                            </td>


                            <!-- BASE DE CÁLCULO -->

                            <td
                                class="col-base"
                                data-label="Base de Cálculo"
                            >

                                <?php

                                $baseCalculoInterna =
                                    strtoupper(
                                        trim(
                                            (string)(
                                                $fila['base_calculo']
                                                ?? ''
                                            )
                                        )
                                    );


                                switch ($baseCalculoInterna) {

                                    case 'BASICO':
                                        $baseCalculoVisual =
                                            'Sueldo Básico';
                                        break;

                                    case 'BASICO_MAS_DEDICACION':
                                        $baseCalculoVisual =
                                            'Básico + Dedicación';
                                        break;

                                    case 'TOTAL_REMUNERATIVO':
                                        $baseCalculoVisual =
                                            'Total Remunerativo';
                                        break;

                                    case 'TOTAL_REMUNERATIVO_MENOS_DESCUENTOS_OBLIGATORIOS':
                                        $baseCalculoVisual =
                                            'TR menos Desc. Obligatorios';
                                        break;

                                    default:
                                        $baseCalculoVisual =
                                            '-';
                                        break;
                                }


                                echo htmlspecialchars(
                                    $baseCalculoVisual,
                                    ENT_QUOTES,
                                    'UTF-8'
                                );

                                ?>

                            </td>


                            <!-- SAC -->

                            <td
                                class="col-sac"
                                data-label="SAC"
                            >

                                <?php
                                echo (
                                    (int)$fila['aplica_sac'] === 1
                                )
                                    ? 'Sí'
                                    : 'No';
                                ?>

                            </td>


                            <!-- VISIBLE RECIBO -->

                            <td
                                class="col-visible"
                                data-label="Visible Recibo"
                            >

                                <?php
                                echo (
                                    (int)$fila['visible_recibo'] === 1
                                )
                                    ? 'Sí'
                                    : 'No';
                                ?>

                            </td>


                            <!-- ESTADO -->

                            <td
                                class="col-estado"
                                data-label="Estado"
                            >

                                <?php if ((int)$fila['activo'] === 1): ?>

                                    <span class="estado-activo">
                                        Activo
                                    </span>

                                <?php else: ?>

                                    <span class="estado-inactivo">
                                        Inactivo
                                    </span>

                                <?php endif; ?>

                            </td>


                            <!-- VIGENCIA -->

                            <td
                                class="col-vigencia"
                                data-label="Vigencia"
                            >

                                <?php
                                echo htmlspecialchars(
                                    $fechaDesde
                                    . ' / '
                                    . $fechaHasta,
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </td>


                            <!-- ACCIONES -->

                            <td
                                class="col-acciones"
                                data-label="Acciones"
                            >

                                <div class="acciones">


                                    <!-- EDITAR -->

                                    <a
                                        href="<?php
                                            echo htmlspecialchars(
                                                sigenmuniUrlRuta(
                                                    'conceptos/editar',
                                                    [
                                                        'id' => (int)$fila['id']
                                                    ]
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                        ?>"
                                        class="btn-accion btn-editar"
                                    >
                                        Editar
                                    </a>


                                    <!-- ACTIVAR / INACTIVAR -->

                                    <form
                                        method="POST"
                                        action="<?php
                                            echo htmlspecialchars(
                                                $conceptosUrlEstado,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                        ?>"
                                        class="form-accion"
                                        data-event-submit="conceptos-1" data-event-value="<?php
                                            echo (
                                                (int)$fila['activo'] === 1
                                            )
                                                ? '¿Seguro que desea inactivar este concepto?'
                                                : '¿Seguro que desea activar este concepto?';
                                        ?>"
                                    >

                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?php echo (int)$fila['id']; ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="estado"
                                            value="<?php
                                                echo (
                                                    (int)$fila['activo'] === 1
                                                )
                                                    ? 0
                                                    : 1;
                                            ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="_csrf"
                                            value="<?php
                                                echo htmlspecialchars(
                                                    $conceptosCsrfToken,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                );
                                            ?>"
                                        >

                                        <button
                                            type="submit"
                                            class="btn-accion <?php
                                                echo (
                                                    (int)$fila['activo'] === 1
                                                )
                                                    ? 'btn-inactivar'
                                                    : 'btn-activar';
                                            ?>"
                                        >
                                            <?php
                                            echo (
                                                (int)$fila['activo'] === 1
                                            )
                                                ? 'Inactivar'
                                                : 'Activar';
                                            ?>
                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>


        <?php else: ?>

            <div class="sin-resultados">

                No se encontraron conceptos.

            </div>

        <?php endif; ?>


    </div>

</div>


<script src="<?= htmlspecialchars(sigenmuniUrlAsset('js/conceptos/conceptos.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
</body>

</html>