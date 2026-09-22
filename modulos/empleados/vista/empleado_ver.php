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

<style>

*{
    box-sizing:border-box;
}

body{
    font-family:Arial,sans-serif;
    background:#f4f7fb;
    margin:0;
    color:#1f2937;
}

/* =========================================
   HEADER
========================================= */

.header{
    background:linear-gradient(
        135deg,
        #0f766e,
        #14b8a6
    );

    color:white;

    padding:22px 30px;

    box-shadow:
        0 4px 14px rgba(0,0,0,.10);
}

.header h1{
    margin:0;
    font-size:30px;
}

.header p{
    margin-top:6px;
    font-size:14px;
}

/* =========================================
   CONTENEDOR
========================================= */

.contenedor{
    width:95%;
    max-width:1050px;
    margin:30px auto;
}

.panel{
    background:white;

    padding:28px;

    border-radius:18px;

    box-shadow:
        0 8px 20px rgba(0,0,0,.08);
}

h2{
    margin-top:0;
    margin-bottom:22px;
    color:#0f766e;
}

/* =========================================
   GRID
========================================= */

.grid{
    display:grid;

    grid-template-columns:
        repeat(2,1fr);

    gap:14px;
}

.item{
    background:#f9fafb;

    padding:14px;

    border-radius:10px;

    border:
        1px solid #e5e7eb;
}

.item-completo{
    grid-column:1/-1;
}

.titulo{
    font-size:12px;

    color:#6b7280;

    margin-bottom:5px;

    font-weight:bold;

    text-transform:uppercase;
}

.valor{
    font-weight:bold;
    word-break:break-word;
}

.estado-activo{
    color:#166534;
}

.estado-inactivo{
    color:#991b1b;
}

/* =========================================
   ACCIONES
========================================= */

.acciones{
    margin-top:25px;

    display:flex;

    gap:10px;

    flex-wrap:wrap;
}

.btn{
    background:#0f766e;

    color:white;

    padding:11px 16px;

    border:none;

    border-radius:10px;

    text-decoration:none;

    cursor:pointer;

    display:inline-block;

    font-weight:bold;

    font-size:14px;

    transition:.2s;
}

.btn:hover{
    opacity:.92;
    transform:translateY(-1px);
}


.btn-sec{
    background:#1f2937;
}

/* =========================================
   CELULAR
========================================= */

@media (max-width:768px){

    .header{
        padding:20px;
        text-align:center;
    }

    .header h1{
        font-size:24px;
    }

    .header p{
        font-size:13px;
    }

    .contenedor{
        width:94%;
        margin:20px auto;
    }

    .panel{
        padding:20px;
        border-radius:16px;
    }

    h2{
        font-size:24px;
        text-align:center;
    }

    .grid{
        grid-template-columns:1fr;
    }

    .item-completo{
        grid-column:auto;
    }

    .acciones{
        flex-direction:column;
        align-items:stretch;
    }

    .btn{
        width:100%;
        text-align:center;
    }
}

/* =========================================
   CELULARES PEQUEÑOS
========================================= */

@media (max-width:480px){

    .header h1{
        font-size:22px;
    }

    h2{
        font-size:22px;
    }

    .panel{
        padding:18px;
    }

    .btn{
        font-size:13px;
        padding:10px;
    }
}

</style>

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

                        echo '<span style="font-size:12px;color:#64748b;">';

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