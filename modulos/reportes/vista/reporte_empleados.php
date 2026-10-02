<?php

require_once __DIR__ . '/../../../core/Url.php';


/*
|--------------------------------------------------------------------------
| REPORTE DE EMPLEADOS - ROUTER + EXPORTACIONES LEGACY TEMPORALES
|--------------------------------------------------------------------------
*/

$reporteEmpleadosUrlReportes =
    sigenmuniUrlRuta(
        'reportes'
    );


$reporteEmpleadosUrlMenu =
    sigenmuniUrlArchivo(
        'index.php'
    );


$reporteEmpleadosUrlEntrada =
    sigenmuniUrlEntrada();


$reporteEmpleadosUrlLimpiar =
    sigenmuniUrlRuta(
        'reportes/empleados'
    );


$reporteEmpleadosFiltros = [
    'legajo' =>
        $legajo,

    'busqueda' =>
        $busqueda,

    'estado' =>
        $estado
];


$reporteEmpleadosUrlPdf =
    sigenmuniUrlRuta(
        'reportes/empleados/pdf',
        $reporteEmpleadosFiltros
    );


$reporteEmpleadosUrlExcel =
    sigenmuniUrlRuta(
        'reportes/empleados/excel',
        $reporteEmpleadosFiltros
    );

?>

<?php

if (!function_exists('formatearCuitUnidadOrganizacion')) {

    function formatearCuitUnidadOrganizacion($cuit)
    {
        $cuit = preg_replace('/\D+/', '', (string)$cuit);

        if (strlen($cuit) !== 11) {
            return (string)$cuit;
        }

        return substr($cuit, 0, 2)
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

<title>Reporte de Empleados - SIGENMUNI</title>

<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/reportes/reporte_empleados.css'), ENT_QUOTES, 'UTF-8') ?>">

</head>


<body>


<div class="contenedor">


    <!-- =====================================
         CABECERA
    ====================================== -->

    <div class="cabecera">


        <div class="cabecera-top">


            <div>

                <h1>
                    Reporte de Empleados
                </h1>

                <p>
                    Consulta general del personal municipal
                    con filtros y exportación.
                </p>

            </div>


            <div class="acciones-superiores">


                <a
                    href="<?php
                        echo htmlspecialchars(
                            $reporteEmpleadosUrlReportes,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                    class="btn btn-reportes"
                >
                    Volver a Reportes
                </a>


                <a
                    href="<?php
                        echo htmlspecialchars(
                            $reporteEmpleadosUrlMenu,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                    class="btn btn-volver"
                >
                    Menú Principal
                </a>


            </div>


        </div>


    </div>


    <!-- =====================================
         ERROR
    ====================================== -->

    <?php if (!empty($error)): ?>

        <div class="error">

            <?php
            echo htmlspecialchars(
                $error,
                ENT_QUOTES,
                'UTF-8'
            );
            ?>

        </div>

    <?php endif; ?>


    <!-- =====================================
         FILTROS
    ====================================== -->

    <div class="panel">


        <h2>
            Filtros de búsqueda
        </h2>


        <form
            method="GET"
            action="<?php
                echo htmlspecialchars(
                    $reporteEmpleadosUrlEntrada,
                    ENT_QUOTES,
                    'UTF-8'
                );
            ?>"
            class="filtros"
        >

            <input
                type="hidden"
                name="r"
                value="reportes/empleados"
            >


            <!-- =================================
                 LEGAJO
            ================================== -->

            <div class="campo">


                <label for="legajo">
                    Legajo
                </label>


                <input
                    type="text"
                    id="legajo"
                    name="legajo"
                    inputmode="numeric"
                    autocomplete="off"
                    placeholder="Ej.: 11"
                    value="<?php
                        echo htmlspecialchars(
                            $legajo,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                    data-event-input="reporte_empleados-1"
                >


                <div class="ayuda">
                    La búsqueda por legajo es exacta.
                </div>


            </div>


            <!-- =================================
                 BÚSQUEDA GENERAL
            ================================== -->

            <div class="campo">


                <label for="busqueda">
                    Búsqueda general
                </label>


                <input
                    type="text"
                    id="busqueda"
                    name="busqueda"
                    placeholder="Apellido, nombre, DNI, CUIL o email"
                    value="<?php
                        echo htmlspecialchars(
                            $busqueda,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                    autocomplete="off"
                >


                <div class="ayuda">
                    Permite coincidencias parciales.
                </div>


            </div>


            <!-- =================================
                 ESTADO
            ================================== -->

            <div class="campo">


                <label for="estado">
                    Estado
                </label>


                <select
                    name="estado"
                    id="estado"
                >


                    <option value="">
                        Todos
                    </option>


                    <option
                        value="1"
                        <?php
                        echo (
                            $estado === "1"
                        )
                            ?
                            "selected"
                            :
                            "";
                        ?>
                    >
                        Activos
                    </option>


                    <option
                        value="0"
                        <?php
                        echo (
                            $estado === "0"
                        )
                            ?
                            "selected"
                            :
                            "";
                        ?>
                    >
                        Inactivos
                    </option>


                </select>


            </div>


            <!-- =================================
                 BUSCAR
            ================================== -->

            <button
                type="submit"
                class="btn btn-buscar"
            >
                Buscar
            </button>


            <!-- =================================
                 LIMPIAR
            ================================== -->

            <a
                href="<?php
                    echo htmlspecialchars(
                        $reporteEmpleadosUrlLimpiar,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                class="btn btn-limpiar"
            >
                Limpiar
            </a>


        </form>


    </div>


    <!-- =====================================
         RESULTADOS
    ====================================== -->

    <div class="panel">


        <div class="resumen">


            <div class="resumen-box">

                Total de empleados encontrados:

                <?php
                echo (int)$totalEmpleados;
                ?>

            </div>


            <!-- =================================
                 EXPORTACIONES
            ================================== -->

            <div class="acciones-exportar">


                <a
                    href="<?php
                        echo htmlspecialchars(
                            $reporteEmpleadosUrlPdf,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                    class="btn btn-pdf"
                >
                    Exportar PDF
                </a>


                <a
                    href="<?php
                        echo htmlspecialchars(
                            $reporteEmpleadosUrlExcel,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                    class="btn btn-excel"
                >
                    Exportar Excel
                </a>


            </div>


        </div>


        <!-- =====================================
             TABLA
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
                                Apellido y Nombre
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


                            <!-- APELLIDO Y NOMBRE -->

                            <td>

                                <?php

                                echo htmlspecialchars(

                                    (
                                        $fila['apellido']
                                        ?? ''
                                    )
                                    .
                                    ', '
                                    .
                                    (
                                        $fila['nombre']
                                        ?? ''
                                    ),

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


</div>


<script src="<?= htmlspecialchars(sigenmuniUrlAsset('js/reportes/reporte_empleados.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
</body>

</html>