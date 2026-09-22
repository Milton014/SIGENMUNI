<?php

require_once __DIR__ . '/../../../core/Url.php';


/*
|--------------------------------------------------------------------------
| REPORTE DE CATEGORÍAS - ROUTER
|--------------------------------------------------------------------------
*/

$reporteCategoriasUrlReportes =
    sigenmuniUrlRuta(
        'reportes'
    );


$reporteCategoriasUrlMenu =
    sigenmuniUrlArchivo(
        'index.php'
    );


$reporteCategoriasUrlEntrada =
    sigenmuniUrlEntrada();


$reporteCategoriasUrlLimpiar =
    sigenmuniUrlRuta(
        'reportes/categorias'
    );


$reporteCategoriasFiltros = [
    'buscar' =>
        $buscar,

    'activo' =>
        $activo
];


$reporteCategoriasUrlPdf =
    sigenmuniUrlRuta(
        'reportes/categorias/pdf',
        $reporteCategoriasFiltros
    );


$reporteCategoriasUrlExcel =
    sigenmuniUrlRuta(
        'reportes/categorias/excel',
        $reporteCategoriasFiltros
    );

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Reporte de Categorías - SIGENMUNI</title>

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

.contenedor{
    width:95%;
    max-width:1400px;
    margin:30px auto;
}


/* =========================================
   CABECERA
========================================= */

.cabecera{
    background:linear-gradient(135deg,#7c3aed,#a855f7);
    color:white;
    border-radius:18px;
    padding:24px;
    box-shadow:0 8px 20px rgba(0,0,0,.10);
    margin-bottom:22px;
}

.cabecera-top{
    display:flex;
    justify-content:space-between;
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
    transform:translateY(-1px);
}

.btn-volver{
    background:#374151;
}

.btn-reportes{
    background:#ea580c;
}

.btn-buscar{
    background:#7c3aed;
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
    box-shadow:0 8px 20px rgba(0,0,0,.08);
    border:1px solid #e5e7eb;
    margin-bottom:20px;
}

.panel h2{
    margin-top:0;
    margin-bottom:16px;
    font-size:20px;
    color:#7c3aed;
}


/* =========================================
   ERROR
========================================= */

.error{
    background:#fee2e2;
    color:#991b1b;
    border:1px solid #fecaca;
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
    grid-template-columns:2fr 1fr auto auto;
    gap:12px;
    align-items:end;
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
    padding:11px 12px;
    border:1px solid #cbd5e1;
    border-radius:10px;
    font-size:14px;
    outline:none;
}

.campo input:focus,
.campo select:focus{
    border-color:#a855f7;
    box-shadow:0 0 0 3px rgba(168,85,247,.15);
}


/* =========================================
   RESUMEN
========================================= */

.resumen{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:12px;
    flex-wrap:wrap;
    margin-bottom:15px;
}

.resumen-box{
    background:#f5f3ff;
    border:1px solid #ddd6fe;
    color:#5b21b6;
    padding:12px 15px;
    border-radius:12px;
    font-weight:bold;
}

.acciones-exportar{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
}


/* =========================================
   TABLA SIN BARRA HORIZONTAL
========================================= */

.tabla-contenedor{
    width:100%;
    overflow:visible;
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
    padding:12px 10px;
    border-bottom:1px solid #e5e7eb;
    text-align:left;
    font-size:14px;
    vertical-align:middle;
    overflow-wrap:anywhere;
}

th{
    background:#7c3aed;
    color:white;
    white-space:normal;
}

tr:hover{
    background:#faf5ff;
}


/* =========================================
   ANCHOS
========================================= */

.col-codigo{
    width:8%;
}

.col-nombre{
    width:18%;
}

.col-basico{
    width:16%;
}

.col-dedicacion{
    width:18%;
}

.col-suplemento{
    width:16%;
}

.col-configuracion{
    width:12%;
    text-align:center;
}

.col-estado{
    width:12%;
    text-align:center;
}


/* =========================================
   VALORES / CONFIGURACIÓN
========================================= */

.sin-valor{
    display:inline-block;
    padding:5px 9px;
    border-radius:999px;
    background:#f1f5f9;
    color:#64748b;
    font-size:11px;
    font-weight:bold;
    white-space:nowrap;
}

.configuracion-completa,
.configuracion-incompleta{
    display:inline-block;
    padding:5px 10px;
    border-radius:999px;
    font-size:11px;
    font-weight:bold;
    white-space:nowrap;
}

.configuracion-completa{
    background:#dcfce7;
    color:#166534;
}

.configuracion-incompleta{
    background:#fef3c7;
    color:#92400e;
}


/* =========================================
   ESTADO
========================================= */

.estado-activo,
.estado-inactivo{
    display:inline-block;
    font-weight:bold;
    padding:5px 10px;
    border-radius:999px;
    font-size:12px;
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
}


/* =========================================
   TABLET
========================================= */

@media(max-width:1000px){

    .filtros{
        grid-template-columns:1fr;
    }

    .filtros .btn{
        width:100%;
    }

    th,
    td{
        font-size:13px;
        padding:10px 7px;
    }
}


/* =========================================
   CELULAR
========================================= */

@media(max-width:768px){

    .contenedor{
        width:98%;
        margin:10px auto;
    }

    .cabecera{
        padding:18px 15px;
        border-radius:15px;
    }

    .cabecera-top{
        flex-direction:column;
        align-items:stretch;
        text-align:center;
    }

    .cabecera h1{
        font-size:24px;
    }

    .acciones-superiores{
        flex-direction:column;
    }

    .acciones-superiores .btn{
        width:100%;
    }

    .panel{
        padding:15px;
        border-radius:15px;
    }

    .panel h2{
        text-align:center;
        font-size:19px;
    }

    .acciones-exportar{
        width:100%;
        flex-direction:column;
    }

    .acciones-exportar .btn{
        width:100%;
    }

    .resumen{
        flex-direction:column;
        align-items:stretch;
    }

    .resumen-box{
        text-align:center;
    }


    /*
    |----------------------------------------------------------------------
    | TABLA RESPONSIVE TIPO TARJETAS
    |----------------------------------------------------------------------
    */

    table,
    thead,
    tbody,
    tr,
    th,
    td{
        display:block;
        width:100%;
    }

    thead{
        display:none;
    }

    tbody{
        display:grid;
        gap:12px;
    }

    tbody tr{
        border:1px solid #e5e7eb;
        border-radius:12px;
        padding:12px;
        background:white;
    }

    tbody tr:hover{
        background:white;
    }

    td{
        display:grid;
        grid-template-columns:150px 1fr;
        gap:10px;
        padding:8px 0;
        border-bottom:1px solid #f1f5f9;
        text-align:left !important;
        font-size:13px;
    }

    td:last-child{
        border-bottom:none;
    }

    td::before{
        content:attr(data-label);
        font-weight:bold;
        color:#475569;
    }

    .estado-activo,
    .estado-inactivo,
    .configuracion-completa,
    .configuracion-incompleta,
    .sin-valor{
        justify-self:start;
    }
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
                    Reporte de Categorías
                </h1>

                <p>
                    Consulta de categorías municipales
                    con filtros y exportación.
                </p>

            </div>

            <div class="acciones-superiores">

                <a
                    href="<?php
                    echo htmlspecialchars(
                        $reporteCategoriasUrlReportes,
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
                        $reporteCategoriasUrlMenu,
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
                    $reporteCategoriasUrlEntrada,
                    ENT_QUOTES,
                    'UTF-8'
                );
            ?>"
            class="filtros"
        >

            <input
                type="hidden"
                name="r"
                value="reportes/categorias"
            >

            <div class="campo">

                <label for="buscar">
                    Búsqueda
                </label>

                <input
                    type="text"
                    id="buscar"
                    name="buscar"
                    placeholder="Buscar por código o nombre"
                    autocomplete="off"
                    value="<?php
                        echo htmlspecialchars(
                            $buscar,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                >

            </div>


            <div class="campo">

                <label for="activo">
                    Estado
                </label>

                <select
                    name="activo"
                    id="activo"
                >

                    <option value="">
                        Todas
                    </option>

                    <option
                        value="1"
                        <?php
                        echo (
                            $activo === '1'
                        )
                            ?
                            'selected'
                            :
                            '';
                        ?>
                    >
                        Activas
                    </option>

                    <option
                        value="0"
                        <?php
                        echo (
                            $activo === '0'
                        )
                            ?
                            'selected'
                            :
                            '';
                        ?>
                    >
                        Inactivas
                    </option>

                </select>

            </div>


            <button
                type="submit"
                class="btn btn-buscar"
            >
                Buscar
            </button>


            <a
                href="<?php
                    echo htmlspecialchars(
                        $reporteCategoriasUrlLimpiar,
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

                Total de categorías encontradas:

                <?php
                echo (int)$totalCategorias;
                ?>

            </div>


            <div class="acciones-exportar">

                <a
                    href="<?php
                        echo htmlspecialchars(
                            $reporteCategoriasUrlPdf,
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
                            $reporteCategoriasUrlExcel,
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


        <?php if (!empty($categorias)): ?>

            <div class="tabla-contenedor">

                <table>

                    <thead>

                        <tr>

                            <th class="col-codigo">
                                Código
                            </th>

                            <th class="col-nombre">
                                Categoría
                            </th>

                            <th class="col-basico">
                                Sueldo Básico
                            </th>

                            <th class="col-dedicacion">
                                Dedicación Funcional
                            </th>

                            <th class="col-suplemento">
                                Suplemento Especial
                            </th>

                            <th class="col-configuracion">
                                Configuración
                            </th>

                            <th class="col-estado">
                                Estado
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php foreach ($categorias as $fila): ?>

                        <tr>


                            <td
                                data-label="Código"
                                class="col-codigo"
                            >

                                <?php
                                echo htmlspecialchars(
                                    $fila['codigo']
                                    ?? '',
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </td>


                            <td
                                data-label="Categoría"
                                class="col-nombre"
                            >

                                <?php
                                echo htmlspecialchars(
                                    $fila['nombre']
                                    ?? '',
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </td>


                            <td
                                data-label="Sueldo Básico"
                                class="col-basico"
                            >

                                <?php if (
                                    array_key_exists(
                                        'sueldo_basico',
                                        $fila
                                    )
                                    &&
                                    $fila['sueldo_basico'] !== null
                                ): ?>

                                    $
                                    <?php
                                    echo number_format(
                                        (float)$fila['sueldo_basico'],
                                        2,
                                        ',',
                                        '.'
                                    );
                                    ?>

                                <?php else: ?>

                                    <span class="sin-valor">
                                        Sin valor vigente
                                    </span>

                                <?php endif; ?>

                            </td>


                            <td
                                data-label="Dedicación Funcional"
                                class="col-dedicacion"
                            >

                                <?php if (
                                    array_key_exists(
                                        'dedicacion_funcional',
                                        $fila
                                    )
                                    &&
                                    $fila['dedicacion_funcional'] !== null
                                ): ?>

                                    $
                                    <?php
                                    echo number_format(
                                        (float)$fila['dedicacion_funcional'],
                                        2,
                                        ',',
                                        '.'
                                    );
                                    ?>

                                <?php else: ?>

                                    <span class="sin-valor">
                                        Sin valor vigente
                                    </span>

                                <?php endif; ?>

                            </td>


                            <td
                                data-label="Suplemento Especial"
                                class="col-suplemento"
                            >

                                <?php if (
                                    array_key_exists(
                                        'suplemento_especial',
                                        $fila
                                    )
                                    &&
                                    $fila['suplemento_especial'] !== null
                                ): ?>

                                    $
                                    <?php
                                    echo number_format(
                                        (float)$fila['suplemento_especial'],
                                        2,
                                        ',',
                                        '.'
                                    );
                                    ?>

                                <?php else: ?>

                                    <span class="sin-valor">
                                        Sin valor vigente
                                    </span>

                                <?php endif; ?>

                            </td>


                            <td
                                data-label="Configuración"
                                class="col-configuracion"
                            >

                                <?php

                                $configuracionCompleta =
                                    isset($fila['configuracion_completa'])
                                        ?
                                        (int)$fila['configuracion_completa'] === 1
                                        :
                                        (
                                            array_key_exists('sueldo_basico', $fila)
                                            &&
                                            $fila['sueldo_basico'] !== null
                                            &&
                                            array_key_exists('dedicacion_funcional', $fila)
                                            &&
                                            $fila['dedicacion_funcional'] !== null
                                            &&
                                            array_key_exists('suplemento_especial', $fila)
                                            &&
                                            $fila['suplemento_especial'] !== null
                                        );

                                ?>

                                <?php if ($configuracionCompleta): ?>

                                    <span class="configuracion-completa">
                                        Completa
                                    </span>

                                <?php else: ?>

                                    <span class="configuracion-incompleta">
                                        Incompleta
                                    </span>

                                <?php endif; ?>

                            </td>


                            <td
                                data-label="Estado"
                                class="col-estado"
                            >

                                <?php if (
                                    (int)(
                                        $fila['activo']
                                        ?? 0
                                    )
                                    ===
                                    1
                                ): ?>

                                    <span class="estado-activo">
                                        Activa
                                    </span>

                                <?php else: ?>

                                    <span class="estado-inactivo">
                                        Inactiva
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

                No se encontraron categorías
                con los filtros seleccionados.

            </div>

        <?php endif; ?>

    </div>

</div>

</body>

</html>