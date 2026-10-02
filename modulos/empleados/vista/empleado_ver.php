<?php

require_once __DIR__ . '/../../../core/Url.php';


/*
|--------------------------------------------------------------------------
| VER EMPLEADO - SOLO ROUTER
|--------------------------------------------------------------------------
*/

$empleadoVerVolver =
    sigenmuniUrlRuta(
        'empleados'
    );


$empleadoVerEditar =
    sigenmuniUrlRuta(
        'empleados/editar',
        [
            'id' => (int)(
                $empleado['id']
                ?? 0
            )
        ]
    );


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

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>Detalle del Empleado - SIGENMUNI</title>

<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/empleados/empleado_ver.css'), ENT_QUOTES, 'UTF-8') ?>">

</head>

<body>


<div class="header">

    <h1>SIGENMUNI</h1>

    <p>
        Ficha del Empleado Municipal
    </p>

</div>


<div class="contenedor">

    <div class="panel">

        <h2>
            Ficha del Empleado
        </h2>


        <div class="grid">


            <!-- LEGAJO -->

            <div class="item">

                <div class="titulo">
                    Legajo
                </div>

                <div class="valor">

                    <?php
                    echo htmlspecialchars(
                        $empleado['nro_legajo'],
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>

                </div>

            </div>


            <!-- APELLIDO Y NOMBRE -->

            <div class="item">

                <div class="titulo">
                    Apellido y Nombre
                </div>

                <div class="valor">

                    <?php
                    echo htmlspecialchars(
                        $empleado['apellido']
                        . ', '
                        . $empleado['nombre'],
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>

                </div>

            </div>


            <!-- DNI -->

            <div class="item">

                <div class="titulo">
                    DNI
                </div>

                <div class="valor">

                    <?php
                    echo htmlspecialchars(
                        $empleado['dni'],
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>

                </div>

            </div>


            <!-- CUIL -->

            <div class="item">

                <div class="titulo">
                    CUIL
                </div>

                <div class="valor">

                    <?php
                    echo htmlspecialchars(
                        $empleado['cuil'],
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>

                </div>

            </div>


            <!-- FECHA ALTA -->

            <div class="item">

                <div class="titulo">
                    Fecha Alta
                </div>

                <div class="valor">

                    <?php

                    $fechaAltaVisual =
                        !empty($empleado['fecha_alta'])
                        &&
                        $empleado['fecha_alta'] !== '0000-00-00'
                            ?
                            date(
                                'd/m/Y',
                                strtotime(
                                    $empleado['fecha_alta']
                                )
                            )
                            :
                            '-';

                    echo htmlspecialchars(
                        $fechaAltaVisual,
                        ENT_QUOTES,
                        'UTF-8'
                    );

                    ?>

                </div>

            </div>


            <!-- FECHA INACTIVO -->

            <div class="item">

                <div class="titulo">
                    Fecha Inactivo
                </div>

                <div class="valor">

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

                    echo htmlspecialchars(
                        $fechaInactivoVisual,
                        ENT_QUOTES,
                        'UTF-8'
                    );

                    ?>

                </div>

            </div>


            <!-- TELÉFONO -->

            <div class="item">

                <div class="titulo">
                    Teléfono
                </div>

                <div class="valor">

                    <?php
                    echo htmlspecialchars(
                        $empleado['telefono']
                            ?: '-',
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>

                </div>

            </div>


            <!-- EMAIL -->

            <div class="item">

                <div class="titulo">
                    Email
                </div>

                <div class="valor">

                    <?php
                    echo htmlspecialchars(
                        $empleado['email']
                            ?: '-',
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>

                </div>

            </div>


            <!-- DOMICILIO -->

            <div class="item">

                <div class="titulo">
                    Domicilio
                </div>

                <div class="valor">

                    <?php
                    echo htmlspecialchars(
                        $empleado['domicilio']
                            ?: '-',
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>

                </div>

            </div>


            <!-- INSTITUCIÓN -->

            <div class="item">

                <div class="titulo">
                    Institución
                </div>

                <div class="valor">

                    <?php
                    echo htmlspecialchars(
                        $empleado['institucion'],
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>

                </div>

            </div>


            <!-- UNIDAD DE ORGANIZACIÓN -->

            <div class="item">

                <div class="titulo">
                    Unidad de Organización
                </div>

                <div class="valor">

                    <?php

                    $nombreUnidadOrganizacion =
                        $empleado['oficina']
                        ?? '-';


                    $cuitUnidadOrganizacion =
                        $empleado['oficina_cuit']
                        ??
                        '';


                    echo htmlspecialchars(
                        $nombreUnidadOrganizacion,
                        ENT_QUOTES,
                        'UTF-8'
                    );


                    if ($cuitUnidadOrganizacion !== '') {

                        echo '<br>';

                        echo '<span data-estilo="empleado_ver-1">';

                        echo 'CUIT: '
                            .
                            htmlspecialchars(
                                formatearCuitUnidadOrganizacion(
                                    $cuitUnidadOrganizacion
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            );

                        echo '</span>';
                    }

                    ?>

                </div>

            </div>


            <!-- SITUACIÓN -->

            <div class="item">

                <div class="titulo">
                    Situación
                </div>

                <div class="valor">

                    <?php
                    echo htmlspecialchars(
                        $empleado['situacion'],
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>

                </div>

            </div>


            <!-- ESCALAFÓN -->

            <div class="item">

                <div class="titulo">
                    Escalafón
                </div>

                <div class="valor">

                    <?php
                    echo htmlspecialchars(
                        !empty($empleado['escalafon'])
                            ? $empleado['escalafon']
                            : 'No corresponde',
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>

                </div>

            </div>


            <!-- CATEGORÍA -->

            <div class="item">

                <div class="titulo">
                    Categoría
                </div>

                <div class="valor">

                    <?php
                    $categoriaMostrar =
                        isset($empleado['categoria_codigo'])
                            && $empleado['categoria_codigo'] !== null
                            && $empleado['categoria_codigo'] !== ''
                                ? $empleado['categoria_codigo']
                                    . ' - '
                                    . $empleado['categoria']
                                : $empleado['categoria'];

                    echo htmlspecialchars(
                        $categoriaMostrar,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>

                </div>

            </div>


            <!-- ESTADO -->

            <div class="item">

                <div class="titulo">
                    Estado
                </div>

                <div
                    class="valor <?php
                    echo (
                        (int)$empleado['activo'] === 1
                    )
                        ? 'estado-activo'
                        : 'estado-inactivo';
                    ?>"
                >

                    <?php
                    echo (
                        (int)$empleado['activo'] === 1
                    )
                        ? 'Activo'
                        : 'Inactivo';
                    ?>

                </div>

            </div>


            <!-- OBSERVACIONES -->

            <div class="item item-completo">

                <div class="titulo">
                    Observaciones
                </div>

                <div class="valor">

                    <?php
                    echo nl2br(
                        htmlspecialchars(
                            $empleado['observaciones']
                                ?: '-',
                            ENT_QUOTES,
                            'UTF-8'
                        )
                    );
                    ?>

                </div>

            </div>

        </div>


        <!-- =====================================
             ACCIONES
        ====================================== -->

        <div class="acciones">

            <a
                href="<?php
                    echo htmlspecialchars(
                        $empleadoVerEditar,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                class="btn"
            >
                Editar
            </a>


            <a
                href="<?php
                    echo htmlspecialchars(
                        $empleadoVerVolver,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                class="btn btn-sec"
            >
                Volver
            </a>

        </div>

    </div>

</div>

</body>

</html>