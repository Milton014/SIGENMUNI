<?php

require_once __DIR__ . '/../../../core/Url.php';


/*
|--------------------------------------------------------------------------
| URLS DE REPORTES MIGRADOS AL ROUTER
|--------------------------------------------------------------------------
*/

$reportesUrlMenu =
    sigenmuniUrlRuta(
    'inicio'
);


$reportesUrlEmpleados =
    sigenmuniUrlRuta(
        'reportes/empleados'
    );


$reportesUrlHistorialEmpleado =
    sigenmuniUrlRuta(
        'reportes/historial-empleado'
    );


$reportesUrlConceptos =
    sigenmuniUrlRuta(
        'reportes/conceptos'
    );


$reportesUrlCategorias =
    sigenmuniUrlRuta(
        'reportes/categorias'
    );



$reportesUrlLiquidaciones =
    sigenmuniUrlRuta(
        'reportes/liquidaciones'
    );



$reportesUrlEstadisticas =
    sigenmuniUrlRuta(
        'reportes/estadisticas'
    );



$reportesUrlAuditoria =
    sigenmuniUrlRuta(
        'reportes/auditoria'
    );

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>SIGENMUNI - Consultas y Reportes</title>

<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/reportes/reportes.css'), ENT_QUOTES, 'UTF-8') ?>">

</head>


<body>


<!-- =========================================
     HEADER GLOBAL
========================================= -->

<?php

$sigenmuniHeaderSubtitulo =
    'Sistema de Gestión Municipal - Consultas y Reportes';

require __DIR__
    . '/../../../componentes/header_sigenmuni.php';

?>


<!-- =========================================
     CONTENIDO
========================================= -->

<div class="contenedor">


    <!-- =====================================
         PANEL SUPERIOR
    ====================================== -->

    <div class="panel-superior">


        <div class="bienvenida">

            <h2>
                Consultas y Reportes
            </h2>

            <p>
                Seleccione el tipo de consulta o reporte
                que desea generar.
            </p>

        </div>


        <div class="acciones-superiores">

            <a
                href="<?php
                    echo htmlspecialchars(
                        $reportesUrlMenu,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                class="btn-top btn-menu"
            >
                Volver al menú
            </a>

        </div>


    </div>


        <?php if ($totalReportesVisibles > 0): ?>

    <!-- =====================================
         BUSCADOR
    ====================================== -->

    <div class="buscador-box">


        <label for="buscadorModulos">

            Buscar opción

        </label>


        <input
            type="text"
            id="buscadorModulos"
            placeholder="Escriba por ejemplo: empleados, conceptos, categorías, liquidaciones, historial, estadísticas, auditoría..."
            autocomplete="off"
        >


    </div>


    <!-- =====================================
         TARJETAS
    ====================================== -->

    <div
        class="grid-modulos"
        id="gridModulos"
    >


        <!-- =================================
             REPORTE EMPLEADOS
        ================================== -->

        <?php if ($reportesVisibles['reporte_empleados.php']): ?>


        <div
            class="card-modulo empleados"
            data-nombre="reporte empleados personal agentes legajos dni listado excel pdf"
        >


            <div class="franja"></div>


            <div>


                <div class="icono">
                    👤
                </div>


                <h3>
                    Reporte de Empleados
                </h3>


                <p>

                    Consulta general del personal municipal
                    con filtros y opciones de exportación
                    en PDF y Excel.

                </p>


            </div>


            <div class="card-footer">


                <span class="estado">
                    Disponible
                </span>


                <a
                    href="<?php
                        echo htmlspecialchars(
                            $reportesUrlEmpleados,
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


        
        <?php endif; ?>

        <!-- =================================
             HISTORIAL POR EMPLEADO
        ================================== -->

        <?php if ($reportesVisibles['reporte_historial_empleado.php']): ?>


        <div
            class="card-modulo historial"
            data-nombre="historial empleado liquidaciones recibos agente personal legajo"
        >


            <div class="franja"></div>


            <div>


                <div class="icono">
                    📂
                </div>


                <h3>
                    Historial por Empleado
                </h3>


                <p>

                    Consulte el historial de liquidaciones
                    y recibos de sueldo de cada agente municipal.

                </p>


            </div>


            <div class="card-footer">


                <span class="estado">
                    Disponible
                </span>


                <a
                    href="<?php
                        echo htmlspecialchars(
                            $reportesUrlHistorialEmpleado,
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


        
        <?php endif; ?>

        <!-- =================================
             REPORTE CONCEPTOS
        ================================== -->

        <?php if ($reportesVisibles['reporte_conceptos.php']): ?>


        <div
            class="card-modulo conceptos"
            data-nombre="reporte conceptos haberes descuentos remunerativos no remunerativos asignaciones listado excel pdf"
        >


            <div class="franja"></div>


            <div>


                <div class="icono">
                    📘
                </div>


                <h3>
                    Reporte de Conceptos
                </h3>


                <p>

                    Listado de conceptos del sistema,
                    con filtros por categoría y estado,
                    y exportación en PDF y Excel.

                </p>


            </div>


            <div class="card-footer">


                <span class="estado">
                    Disponible
                </span>


                <a
                    href="<?php
                        echo htmlspecialchars(
                            $reportesUrlConceptos,
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


        
        <?php endif; ?>

        <!-- =================================
             REPORTE CATEGORÍAS
        ================================== -->

        <?php if ($reportesVisibles['reporte_categorias.php']): ?>


        <div
            class="card-modulo categorias"
            data-nombre="reporte categorias categoría escalas basico sueldo listado excel pdf"
        >


            <div class="franja"></div>


            <div>


                <div class="icono">
                    🏷️
                </div>


                <h3>
                    Reporte de Categorías
                </h3>


                <p>

                    Consulta de categorías municipales
                    con sus datos principales y exportación
                    en PDF y Excel.

                </p>


            </div>


            <div class="card-footer">


                <span class="estado">
                    Disponible
                </span>


                <a
                    href="<?php
                        echo htmlspecialchars(
                            $reportesUrlCategorias,
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


        
        <?php endif; ?>

        <!-- =================================
             REPORTE LIQUIDACIONES
        ================================== -->

        <?php if ($reportesVisibles['reporte_liquidaciones.php']): ?>


        <div
            class="card-modulo liquidaciones"
            data-nombre="reporte liquidaciones sueldos periodos tipo estado totales neto descuentos pdf excel liquidacion"
        >


            <div class="franja"></div>


            <div>


                <div class="icono">
                    💰
                </div>


                <h3>
                    Reporte de Liquidaciones
                </h3>


                <p>

                    Consulta histórica de liquidaciones,
                    con filtros por período, tipo y estado,
                    totales generales y exportación en PDF y Excel.

                </p>


            </div>


            <div class="card-footer">


                <span class="estado">
                    Disponible
                </span>


                <a
                    href="<?php
                        echo htmlspecialchars(
                            $reportesUrlLiquidaciones,
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


        
        <?php endif; ?>

        <!-- =================================
             ESTADÍSTICAS
        ================================== -->

        <?php if ($reportesVisibles['estadisticas.php']): ?>


        <div
            class="card-modulo estadisticas"
            data-nombre="estadisticas gráficos graficos analisis análisis sueldos liquidaciones empleados categorias descuentos neto promedio"
        >


            <div class="franja"></div>


            <div>


                <div class="icono">
                    📊
                </div>


                <h3>
                    Estadísticas
                </h3>


                <p>

                    Análisis gráfico de sueldos,
                    empleados, categorías, descuentos
                    y liquidaciones del sistema.

                </p>


            </div>


            <div class="card-footer">


                <span class="estado">
                    Disponible
                </span>


                <a
                    href="<?php
                        echo htmlspecialchars(
                            $reportesUrlEstadisticas,
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


        <?php endif; ?>

        <!-- =================================
             REPORTE DE AUDITORÍA
        ================================== -->

        <?php if ($reportesVisibles['reporte_auditoria.php'] ?? false): ?>


        <div
            class="card-modulo auditoria"
            data-nombre="reporte auditoria auditoría acciones usuarios seguridad registros historial cambios altas ediciones activaciones inactivaciones"
        >


            <div class="franja"></div>


            <div>


                <div class="icono">
                    🛡️
                </div>


                <h3>
                    Reporte de Auditoría
                </h3>


                <p>

                    Historial de acciones realizadas por los usuarios
                    del sistema, con fecha, hora, módulo, acción,
                    registro afectado y detalle.

                </p>


            </div>


            <div class="card-footer">


                <span class="estado">
                    Disponible
                </span>


                <a
                    href="<?php
                        echo htmlspecialchars(
                            $reportesUrlAuditoria,
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


        <?php endif; ?>

    </div>


    <!-- =====================================
         SIN RESULTADOS
    ====================================== -->

    <div
        class="sin-resultados"
        id="sinResultados"
    >

        No se encontraron opciones con ese nombre.

    </div>

    <?php else: ?>

        <div class="sin-reportes">

            <div class="icono-vacio">
                🔒
            </div>

            <h3>
                No tiene reportes habilitados
            </h3>

            <p>
                Su rol tiene acceso al módulo Consultas y Reportes,
                pero actualmente no posee reportes específicos habilitados.
                Consulte con un administrador del sistema si necesita acceso.
            </p>

        </div>

    <?php endif; ?>



</div>


<!-- =========================================
     FOOTER
========================================= -->

<div class="footer">

    SIGENMUNI · Módulo de Consultas y Reportes

</div>


<script src="<?= htmlspecialchars(sigenmuniUrlAsset('js/reportes/reportes.js'), ENT_QUOTES, 'UTF-8') ?>"></script>


</body>

</html>