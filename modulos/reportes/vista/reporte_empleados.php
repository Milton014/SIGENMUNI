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

<style>

*{
    box-sizing:border-box;
}

body{
    margin:0;
    font-family:Arial,Helvetica,sans-serif;
    background:#f4f7fb;
    color:#1f2937;
}


/* =========================================
   CONTENEDOR
========================================= */

.contenedor{
    width:95%;
    max-width:1400px;
    margin:30px auto;
}


/* =========================================
   CABECERA
========================================= */

.cabecera{
    background:linear-gradient(
        135deg,
        #0f766e,
        #14b8a6
    );

    color:white;

    border-radius:18px;

    padding:24px;

    box-shadow:
        0 8px 20px rgba(0,0,0,.10);

    margin-bottom:22px;
}


.cabecera-top{
    display:flex;

    justify-content:
        space-between;

    align-items:center;

    gap:15px;

    flex-wrap:wrap;
}


.cabecera h1{
    margin:0 0 6px 0;
    font-size:30px;
}


.cabecera p{
    margin:0;
    opacity:.95;
}


/* =========================================
   ACCIONES SUPERIORES
========================================= */

.acciones-superiores{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
}


/* =========================================
   BOTONES
========================================= */

.btn{
    display:inline-block;

    text-decoration:none;

    border:none;

    border-radius:10px;

    padding:11px 16px;

    font-size:14px;

    font-weight:bold;

    cursor:pointer;

    color:white;

    transition:.2s ease;

    text-align:center;
}


.btn:hover{
    opacity:.93;

    transform:
        translateY(-1px);
}


.btn-volver{
    background:#374151;
}


.btn-reportes{
    background:#ea580c;
}


.btn-buscar{
    background:#0f766e;
}


.btn-limpiar{
    background:#6b7280;
}


.btn-pdf{
    background:#dc2626;
}


.btn-excel{
    background:#16a34a;
}


/* =========================================
   PANEL
========================================= */

.panel{
    background:white;

    border-radius:18px;

    padding:20px;

    box-shadow:
        0 8px 20px rgba(0,0,0,.08);

    border:
        1px solid #e5e7eb;

    margin-bottom:20px;
}


.panel h2{
    margin-top:0;

    margin-bottom:16px;

    font-size:20px;

    color:#0f766e;
}


/* =========================================
   ERROR
========================================= */

.error{
    background:#fee2e2;

    color:#991b1b;

    border:
        1px solid #fecaca;

    padding:14px;

    border-radius:12px;

    margin-bottom:20px;

    font-weight:bold;
}


/* =========================================
   FILTROS
========================================= */

.filtros{
    display:grid;

    grid-template-columns:
        minmax(150px,1fr)
        minmax(260px,2fr)
        minmax(180px,1fr)
        auto
        auto;

    gap:12px;

    align-items:start;
}


.campo{
    display:flex;
    flex-direction:column;
    min-width:0;
}


.filtros > .btn{
    margin-top:24px;
    min-height:42px;
    display:flex;
    align-items:center;
    justify-content:center;
}


.campo label{
    display:block;

    margin-bottom:6px;

    font-size:14px;

    font-weight:bold;

    color:#374151;
}


.campo input,
.campo select{
    width:100%;

    padding:
        11px 12px;

    border:
        1px solid #cbd5e1;

    border-radius:10px;

    font-size:14px;

    outline:none;
}


.campo input:focus,
.campo select:focus{
    border-color:#14b8a6;

    box-shadow:
        0 0 0 3px
        rgba(20,184,166,.15);
}


/* =========================================
   AYUDA DE FILTROS
========================================= */

.ayuda{
    margin-top:6px;

    font-size:12px;

    color:#6b7280;

    line-height:1.4;
}


/* =========================================
   RESUMEN
========================================= */

.resumen{
    display:flex;

    justify-content:
        space-between;

    align-items:center;

    gap:12px;

    flex-wrap:wrap;

    margin-bottom:15px;
}


.resumen-box{
    background:#ecfeff;

    border:
        1px solid #a5f3fc;

    color:#155e75;

    padding:
        12px 15px;

    border-radius:12px;

    font-weight:bold;
}


/* =========================================
   EXPORTACIONES
========================================= */

.acciones-exportar{
    display:flex;

    gap:10px;

    flex-wrap:wrap;
}


.unidad-organizacion{
    min-width:0;
    line-height:1.35;
}

.unidad-organizacion .nombre-unidad{
    font-weight:bold;
}

.unidad-organizacion .cuit-unidad{
    margin-top:4px;
    color:#64748b;
    font-size:11px;
    white-space:normal;
    overflow-wrap:anywhere;
}


/* =========================================
   TABLA
========================================= */

.tabla-contenedor{
    width:100%;

    overflow:visible;

    border-radius:12px;

    border:
        1px solid #e5e7eb;
}


table{
    width:100%;

    min-width:0;

    table-layout:fixed;

    border-collapse:collapse;

    background:white;
}


th,
td{
    padding:
        9px 6px;

    border-bottom:
        1px solid #e5e7eb;

    text-align:left;

    font-size:12px;

    line-height:1.35;

    vertical-align:top;

    white-space:normal;

    overflow-wrap:anywhere;

    word-break:normal;
}


th{
    background:#0f766e;

    color:white;

    position:sticky;

    top:0;

    white-space:normal;
}


tr:hover{
    background:#f8fafc;
}


/* ANCHOS DE COLUMNAS */

th:nth-child(1),
td:nth-child(1){ width:6%; }

th:nth-child(2),
td:nth-child(2){ width:12%; }

th:nth-child(3),
td:nth-child(3){ width:7%; }

th:nth-child(4),
td:nth-child(4){ width:9%; }

th:nth-child(5),
td:nth-child(5){ width:7%; }

th:nth-child(6),
td:nth-child(6){ width:13%; }

th:nth-child(7),
td:nth-child(7){ width:7%; }

th:nth-child(8),
td:nth-child(8){ width:7%; }

th:nth-child(9),
td:nth-child(9){ width:8%; }

th:nth-child(10),
td:nth-child(10){ width:10%; }

th:nth-child(11),
td:nth-child(11){ width:8%; }

th:nth-child(12),
td:nth-child(12){
    width:6%;
    text-align:center;
}


/* =========================================
   ESTADO
========================================= */

.estado-activo,
.estado-inactivo{
    display:inline-flex;

    align-items:center;

    justify-content:center;

    font-weight:bold;

    padding:
        5px 10px;

    border-radius:999px;

    font-size:12px;

    white-space:nowrap;

    word-break:normal;

    overflow-wrap:normal;
}


.estado-activo{
    background:#dcfce7;
    color:#166534;
}


.estado-inactivo{
    background:#fee2e2;
    color:#991b1b;
}


/* =========================================
   SIN REGISTROS
========================================= */

.sin-registros{
    text-align:center;

    padding:25px;

    color:#6b7280;

    background:#fff;

    border-radius:12px;

    font-weight:bold;
}


/* =========================================
   TABLET
========================================= */

@media(max-width:1100px){

    .filtros{
        grid-template-columns:
            1fr
            1fr;
    }


    .filtros > .btn{
        width:100%;
        margin-top:0;
    }


    .acciones-superiores,
    .acciones-exportar{
        width:100%;
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
    }


    .cabecera{
        padding:
            18px 15px;

        border-radius:15px;
    }


    .cabecera-top{
        flex-direction:column;

        text-align:center;

        align-items:stretch;
    }


    .cabecera h1{
        font-size:24px;
    }


    .cabecera p{
        font-size:14px;

        line-height:1.4;
    }


    .acciones-superiores{
        flex-direction:column;
    }


    .acciones-superiores .btn{
        width:100%;

        padding:12px;
    }


    .panel{
        padding:15px;

        border-radius:15px;
    }


    .panel h2{
        text-align:center;

        font-size:19px;
    }


    .filtros{
        grid-template-columns:1fr;
    }


    .campo input,
    .campo select{
        min-height:44px;

        font-size:16px;
    }


    .filtros > .btn,
    .filtros button{
        width:100%;

        margin-top:0;

        padding:12px;
    }


    .resumen{
        flex-direction:column;

        align-items:stretch;

        text-align:center;
    }


    .resumen-box{
        width:100%;
    }


    .acciones-exportar{
        flex-direction:column;
    }


    .acciones-exportar .btn{
        width:100%;

        padding:12px;
    }


    /*
    =========================================
    TABLA COMO TARJETAS - SIN SCROLL
    =========================================
    */

    .tabla-contenedor{
        border:none;
        overflow:visible;
        border-radius:0;
    }


    table,
    thead,
    tbody,
    tr,
    th,
    td{
        display:block;
        width:100%;
    }


    table{
        table-layout:auto;
        min-width:0;
    }


    thead{
        display:none;
    }


    tbody{
        display:grid;
        gap:14px;
    }


    tbody tr{
        border:1px solid #e5e7eb;
        border-radius:14px;
        padding:12px;
        background:white;
        box-shadow:0 4px 12px rgba(0,0,0,.05);
    }


    tbody tr:hover{
        background:white;
    }


    td{
        display:grid;

        grid-template-columns:
            125px
            minmax(0,1fr);

        gap:10px;

        width:100% !important;

        padding:8px 0;

        border-bottom:1px solid #f1f5f9;

        font-size:13px;

        text-align:left;

        overflow-wrap:anywhere;
    }


    td:last-child{
        border-bottom:none;
    }


    td::before{
        font-weight:bold;
        color:#475569;
    }


    td:nth-child(1)::before{ content:"Legajo"; }
    td:nth-child(2)::before{ content:"Apellido y Nombre"; }
    td:nth-child(3)::before{ content:"DNI"; }
    td:nth-child(4)::before{ content:"CUIL"; }
    td:nth-child(5)::before{ content:"Teléfono"; }
    td:nth-child(6)::before{ content:"Email"; }
    td:nth-child(7)::before{ content:"Fecha Alta"; }
    td:nth-child(8)::before{ content:"Fecha Baja"; }
    td:nth-child(9)::before{ content:"Categoría"; }
    td:nth-child(10)::before{ content:"Unidad de Organización"; }
    td:nth-child(11)::before{ content:"Situación"; }
    td:nth-child(12)::before{ content:"Estado"; }

}

</style>

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
                    oninput="
                        this.value =
                        this.value.replace(
                            /[^0-9]/g,
                            ''
                        );
                    "
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


</body>

</html>