<?php

require_once __DIR__ . '/../../../core/Url.php';


if (!function_exists('textoEstadoFiltroReporteEmpleados')) {

    function textoEstadoFiltroReporteEmpleados($estado)
    {
        if ($estado === '1') {
            return 'Activos';
        }

        if ($estado === '0') {
            return 'Inactivos';
        }

        return 'Todos';
    }
}


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


$reporteEmpleadosPdfUrlVolver =
    sigenmuniUrlRuta(
        'reportes/empleados',
        [
            'legajo' =>
                $legajo,

            'busqueda' =>
                $busqueda,

            'estado' =>
                $estado
        ]
    );

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
    Reporte de Empleados - PDF
</title>


<style>

*{
    box-sizing:border-box;
}


body{
    font-family:
        Arial,
        Helvetica,
        sans-serif;

    margin:0;

    background:#f4f7fb;

    color:#1f2937;
}


/* =========================================
   CONTENEDOR
========================================= */

.contenedor{
    width:95%;

    max-width:1400px;

    margin:20px auto;

    background:#fff;

    padding:20px;

    border-radius:12px;

    box-shadow:
        0 4px 14px
        rgba(0,0,0,.08);
}


/* =========================================
   ACCIONES
========================================= */

.acciones{
    display:flex;

    gap:10px;

    flex-wrap:wrap;

    margin-bottom:18px;
}


.btn{
    display:inline-block;

    text-decoration:none;

    border:none;

    border-radius:8px;

    padding:
        10px 14px;

    font-size:14px;

    font-weight:bold;

    cursor:pointer;

    color:white;
}


.btn-volver{
    background:#6b7280;
}


.btn-imprimir{
    background:#dc2626;
}



/* =========================================
   ENCABEZADO
========================================= */

.encabezado{
    border:
        2px solid #d1d5db;

    border-radius:10px;

    padding:18px;

    margin-bottom:18px;
}


.encabezado h1{
    margin:
        0 0 6px 0;

    font-size:26px;

    color:#0f766e;
}


.encabezado p{
    margin:
        4px 0;

    font-size:14px;
}


/* =========================================
   RESUMEN DE FILTROS
========================================= */

.resumen{
    display:grid;

    grid-template-columns:
        repeat(
            auto-fit,
            minmax(200px,1fr)
        );

    gap:10px;

    margin-top:15px;
}


.info-box{
    border:
        1px solid #e5e7eb;

    background:#f9fafb;

    border-radius:8px;

    padding:
        10px 12px;
}


.info-box strong{
    display:block;

    margin-bottom:5px;

    font-size:13px;

    color:#374151;
}


/* =========================================
   TABLA
========================================= */

.tabla-contenedor{
    overflow-x:auto;
}


table{
    width:100%;

    min-width:1200px;

    border-collapse:collapse;
}


th,
td{
    border:
        1px solid #d1d5db;

    padding:8px;

    font-size:12px;

    text-align:left;

    vertical-align:top;
}


th{
    background:#0f766e;

    color:white;

    white-space:nowrap;
}


/* =========================================
   ESTADO
========================================= */

/* =========================================
   UNIDAD DE ORGANIZACIÓN
========================================= */

.unidad-organizacion{
    min-width:145px;
    line-height:1.35;
}


.unidad-organizacion .nombre-unidad{
    font-weight:bold;
}


.unidad-organizacion .cuit-unidad{
    margin-top:3px;
    color:#64748b;
    font-size:10px;
    white-space:nowrap;
}


.estado-activo{
    font-weight:bold;

    color:#166534;
}


.estado-inactivo{
    font-weight:bold;

    color:#991b1b;
}


/* =========================================
   SIN REGISTROS
========================================= */

.sin-registros{
    text-align:center;

    padding:20px;

    color:#6b7280;

    border:
        1px solid #d1d5db;

    border-radius:8px;

    background:#fafafa;
}


/* =========================================
   IMPRESIÓN A4 HORIZONTAL
========================================= */

@media print{

    body{
        background:#fff;
    }


    .acciones{
        display:none;
    }


    .contenedor{
        box-shadow:none;

        margin:0;

        max-width:100%;

        width:100%;

        border-radius:0;

        padding:0;
    }


    .encabezado{
        page-break-inside:avoid;
    }


    table{
        min-width:0;

        width:100%;
    }


    th,
    td{
        font-size:8px;

        padding:4px 3px;

        overflow-wrap:anywhere;
    }


    .unidad-organizacion{
        min-width:0;
    }


    .unidad-organizacion .cuit-unidad{
        font-size:7.5px;
        white-space:normal;
    }


    tr{
        page-break-inside:avoid;
    }


    @page{
        size:A4 landscape;

        margin:8mm;
    }
}


/* =========================================
   CELULAR
========================================= */

@media(max-width:768px){

    .contenedor{
        width:98%;

        margin:
            10px auto;

        padding:14px;
    }


    .acciones{
        flex-direction:column;
    }


    .btn{
        width:100%;

        text-align:center;
    }


    .encabezado h1{
        font-size:22px;
    }

}

</style>

</head>


<body>


<div class="contenedor">


    <!-- =====================================
         ACCIONES
    ====================================== -->

    <div class="acciones">


        <a
            href="<?php
                echo htmlspecialchars(
                    $reporteEmpleadosPdfUrlVolver,
                    ENT_QUOTES,
                    'UTF-8'
                );
            ?>"
            class="btn btn-volver"
        >
            Volver
        </a>



        <button
            type="button"
            onclick="window.print()"
            class="btn btn-imprimir"
        >
            Imprimir / Guardar PDF
        </button>


    </div>


    <!-- =====================================
         ENCABEZADO
    ====================================== -->

    <div class="encabezado">


        <h1>
            Reporte de Empleados
        </h1>


        <p>

            <strong>
                SIGENMUNI
            </strong>

            - Municipalidad de Fortín Lugones

        </p>


        <p>

            Fecha de emisión:

            <?php
            echo date(
                "d/m/Y H:i:s"
            );
            ?>

        </p>


        <!-- =================================
             FILTROS APLICADOS
        ================================== -->

        <div class="resumen">


            <!-- LEGAJO -->

            <div class="info-box">

                <strong>
                    Legajo
                </strong>


                <?php

                echo htmlspecialchars(
                    $legajo !== ''
                        ?
                        $legajo
                        :
                        'Todos',
                    ENT_QUOTES,
                    'UTF-8'
                );

                ?>

            </div>


            <!-- BÚSQUEDA GENERAL -->

            <div class="info-box">

                <strong>
                    Búsqueda general
                </strong>


                <?php

                echo htmlspecialchars(
                    $busqueda !== ''
                        ?
                        $busqueda
                        :
                        'Sin filtro',
                    ENT_QUOTES,
                    'UTF-8'
                );

                ?>

            </div>


            <!-- ESTADO -->

            <div class="info-box">

                <strong>
                    Estado
                </strong>


                <?php
                echo htmlspecialchars(
                    textoEstadoFiltroReporteEmpleados(
                        $estado
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>

            </div>


            <!-- TOTAL -->

            <div class="info-box">

                <strong>
                    Total de empleados
                </strong>


                <?php
                echo (int)$totalEmpleados;
                ?>

            </div>


        </div>


    </div>


    <!-- =====================================
         RESULTADOS
    ====================================== -->

    <?php if (!empty($empleados)): ?>


        <div class="tabla-contenedor">


            <table>


                <thead>


                    <tr>

                        <th>
                            Legajo
                        </th>

                        <th>
                            Apellido
                        </th>

                        <th>
                            Nombre
                        </th>

                        <th>
                            DNI
                        </th>

                        <th>
                            CUIL
                        </th>

                        <th>
                            Teléfono
                        </th>

                        <th>
                            Email
                        </th>

                        <th>
                            Fecha Alta
                        </th>

                        <th>
                            Fecha Baja
                        </th>

                        <th>
                            Categoría
                        </th>

                        <th>
                            Unidad de Organización
                        </th>

                        <th>
                            Situación
                        </th>

                        <th>
                            Estado
                        </th>

                    </tr>


                </thead>


                <tbody>


                <?php foreach ($empleados as $fila): ?>


                    <tr>


                        <!-- LEGAJO -->

                        <td>

                            <?php
                            echo htmlspecialchars(
                                $fila['nro_legajo']
                                ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>

                        </td>


                        <!-- APELLIDO -->

                        <td>

                            <?php
                            echo htmlspecialchars(
                                $fila['apellido']
                                ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>

                        </td>


                        <!-- NOMBRE -->

                        <td>

                            <?php
                            echo htmlspecialchars(
                                $fila['nombre']
                                ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>

                        </td>


                        <!-- DNI -->

                        <td>

                            <?php
                            echo htmlspecialchars(
                                $fila['dni']
                                ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>

                        </td>


                        <!-- CUIL -->

                        <td>

                            <?php
                            echo htmlspecialchars(
                                $fila['cuil']
                                ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>

                        </td>


                        <!-- TELÉFONO -->

                        <td>

                            <?php

                            echo htmlspecialchars(
                                !empty(
                                    $fila['telefono']
                                )
                                    ?
                                    $fila['telefono']
                                    :
                                    '-',
                                ENT_QUOTES,
                                'UTF-8'
                            );

                            ?>

                        </td>


                        <!-- EMAIL -->

                        <td>

                            <?php

                            echo htmlspecialchars(
                                !empty(
                                    $fila['email']
                                )
                                    ?
                                    $fila['email']
                                    :
                                    '-',
                                ENT_QUOTES,
                                'UTF-8'
                            );

                            ?>

                        </td>


                        <!-- FECHA ALTA -->

                        <td>

                            <?php

                            echo !empty(
                                $fila['fecha_alta']
                            )
                                ?
                                date(
                                    "d/m/Y",
                                    strtotime(
                                        $fila[
                                            'fecha_alta'
                                        ]
                                    )
                                )
                                :
                                '-';

                            ?>

                        </td>


                        <!-- FECHA BAJA -->

                        <td>

                            <?php

                            echo !empty(
                                $fila['fecha_baja']
                            )
                                ?
                                date(
                                    "d/m/Y",
                                    strtotime(
                                        $fila[
                                            'fecha_baja'
                                        ]
                                    )
                                )
                                :
                                '-';

                            ?>

                        </td>


                        <!-- CATEGORÍA -->

                        <td>

                            <?php

                            echo htmlspecialchars(
                                !empty(
                                    $fila['categoria']
                                )
                                    ?
                                    $fila['categoria']
                                    :
                                    '-',
                                ENT_QUOTES,
                                'UTF-8'
                            );

                            ?>

                        </td>


                        <!-- UNIDAD DE ORGANIZACIÓN -->

                        <td class="unidad-organizacion">

                            <div class="nombre-unidad">

                                <?php

                                echo htmlspecialchars(
                                    !empty(
                                        $fila['oficina']
                                    )
                                        ?
                                        $fila['oficina']
                                        :
                                        '-',
                                    ENT_QUOTES,
                                    'UTF-8'
                                );

                                ?>

                            </div>


                            <?php if (!empty($fila['oficina_cuit'])): ?>

                                <div class="cuit-unidad">

                                    CUIT:
                                    <?php

                                    echo htmlspecialchars(
                                        formatearCuitUnidadOrganizacion(
                                            $fila['oficina_cuit']
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );

                                    ?>

                                </div>

                            <?php endif; ?>

                        </td>


                        <!-- SITUACIÓN -->

                        <td>

                            <?php

                            echo htmlspecialchars(
                                !empty(
                                    $fila['situacion']
                                )
                                    ?
                                    $fila['situacion']
                                    :
                                    '-',
                                ENT_QUOTES,
                                'UTF-8'
                            );

                            ?>

                        </td>


                        <!-- ESTADO -->

                        <td>


                            <?php if (
                                (int)(
                                    $fila['activo']
                                    ?? 0
                                )
                                ===
                                1
                            ): ?>


                                <span class="estado-activo">
                                    Activo
                                </span>


                            <?php else: ?>


                                <span class="estado-inactivo">
                                    Inactivo
                                </span>


                            <?php endif; ?>


                        </td>


                    </tr>


                <?php endforeach; ?>


                </tbody>


            </table>


        </div>


    <?php else: ?>


        <div class="sin-registros">

            No se encontraron empleados
            con los filtros seleccionados.

        </div>


    <?php endif; ?>


</div>


</body>

</html>