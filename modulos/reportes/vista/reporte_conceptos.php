<?php

require_once __DIR__ . '/../../../core/Url.php';


/*
|--------------------------------------------------------------------------
| REPORTE DE CONCEPTOS - ROUTER
|--------------------------------------------------------------------------
*/

$reporteConceptosUrlReportes =
    sigenmuniUrlRuta(
        'reportes'
    );


$reporteConceptosUrlMenu =
    sigenmuniUrlArchivo(
        'index.php'
    );


$reporteConceptosUrlEntrada =
    sigenmuniUrlEntrada();


$reporteConceptosUrlLimpiar =
    sigenmuniUrlRuta(
        'reportes/conceptos'
    );


$reporteConceptosFiltros = [
    'buscar' =>
        $buscar,

    'tipo_concepto' =>
        $tipoConcepto,

    'activo' =>
        $activo
];


$reporteConceptosUrlPdf =
    sigenmuniUrlRuta(
        'reportes/conceptos/pdf',
        $reporteConceptosFiltros
    );


$reporteConceptosUrlExcel =
    sigenmuniUrlRuta(
        'reportes/conceptos/excel',
        $reporteConceptosFiltros
    );

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Reporte de Conceptos - SIGENMUNI</title>

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
    max-width:1450px;
    margin:30px auto;
}


/* =========================================
   CABECERA
========================================= */

.cabecera{
    background:linear-gradient(135deg,#0891b2,#06b6d4);
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
    background:#0891b2;
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
    color:#0891b2;
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
    grid-template-columns:2fr 1.4fr 1fr auto auto;
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
    border-color:#06b6d4;
    box-shadow:0 0 0 3px rgba(6,182,212,.15);
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
    background:#ecfeff;
    border:1px solid #a5f3fc;
    color:#155e75;
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
    padding:10px 7px;
    border-bottom:1px solid #e5e7eb;
    text-align:left;
    font-size:13px;
    vertical-align:middle;
    overflow-wrap:anywhere;
    word-break:normal;
}

th{
    background:#0891b2;
    color:white;
    white-space:normal;
    line-height:1.15;
}

tr:hover{
    background:#f8fafc;
}


/* ANCHOS */

.col-codigo{
    width:7%;
}

.col-nombre{
    width:19%;
}

.col-categoria{
    width:15%;
}

.col-forma{
    width:15%;
}

.col-porcentaje{
    width:8%;
    text-align:center;
}

.col-monto{
    width:11%;
}

.col-base{
    width:11%;
}

.col-estado{
    width:7%;
    text-align:center;
}

.col-vigencia{
    width:12%;
}


/* =========================================
   ESTADO
========================================= */

.estado-activo,
.estado-inactivo{
    display:inline-block;
    font-weight:bold;
    padding:5px 9px;
    border-radius:999px;
    font-size:11px;
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
   BADGES DE CATEGORÍA
========================================= */

.badge{
    display:inline-block;
    padding:5px 8px;
    border-radius:999px;
    font-size:11px;
    font-weight:bold;
    color:white;
    white-space:normal;
    text-align:center;
}

.badge-rem{
    background:#2563eb;
}

.badge-no-rem{
    background:#7c3aed;
}

.badge-asig{
    background:#059669;
}

.badge-desc{
    background:#dc2626;
}

.badge-aporte{
    background:#ea580c;
}

.badge-default{
    background:#64748b;
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

@media(max-width:1100px){

    .filtros{
        grid-template-columns:1fr 1fr;
    }

    .filtros .btn{
        width:100%;
    }

    th,
    td{
        font-size:12px;
        padding:9px 5px;
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

    .filtros{
        grid-template-columns:1fr;
    }

    .campo input,
    .campo select{
        min-height:44px;
        font-size:16px;
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
        grid-template-columns:135px 1fr;
        gap:10px;
        padding:7px 0;
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

    .badge,
    .estado-activo,
    .estado-inactivo{
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
                    Reporte de Conceptos
                </h1>

                <p>
                    Consulta de conceptos del sistema
                    con filtros y exportación.
                </p>

            </div>

            <div class="acciones-superiores">

                <a
                    href="<?php
                    echo htmlspecialchars(
                        $reporteConceptosUrlReportes,
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
                        $reporteConceptosUrlMenu,
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
                    $reporteConceptosUrlEntrada,
                    ENT_QUOTES,
                    'UTF-8'
                );
            ?>"
            class="filtros"
        >

            <input
                type="hidden"
                name="r"
                value="reportes/conceptos"
            >

            <div class="campo">

                <label for="buscar">
                    Búsqueda general
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

                <label for="tipo_concepto">
                    Tipo de concepto
                </label>

                <select
                    name="tipo_concepto"
                    id="tipo_concepto"
                >

                    <option value="">
                        Todos los tipos
                    </option>

                    <?php foreach ($tiposConcepto as $valorTipo => $textoTipo): ?>

                        <option
                            value="<?php
                                echo htmlspecialchars(
                                    $valorTipo,
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                            ?>"
                            <?php
                            echo (
                                $tipoConcepto === $valorTipo
                            )
                                ?
                                'selected'
                                :
                                '';
                            ?>
                        >
                            <?php
                            echo htmlspecialchars(
                                $textoTipo,
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>
                        </option>

                    <?php endforeach; ?>

                </select>

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
                        Todos
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
                        Activos
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
                        Inactivos
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
                        $reporteConceptosUrlLimpiar,
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

                Total de conceptos encontrados:

                <?php
                echo (int)$totalConceptos;
                ?>

            </div>


            <div class="acciones-exportar">

                <a
                    href="<?php
                        echo htmlspecialchars(
                            $reporteConceptosUrlPdf,
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
                            $reporteConceptosUrlExcel,
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


        <?php if (!empty($conceptos)): ?>

            <div class="tabla-contenedor">

                <table>

                    <thead>

                        <tr>

                            <th class="col-codigo">
                                Código
                            </th>

                            <th class="col-nombre">
                                Nombre
                            </th>

                            <th class="col-categoria">
                                Tipo de concepto
                            </th>

                            <th class="col-forma">
                                Forma Cálculo
                            </th>

                            <th class="col-porcentaje">
                                %
                            </th>

                            <th class="col-monto">
                                Monto Fijo
                            </th>

                            <th class="col-base">
                                Base Cálculo
                            </th>

                            <th class="col-estado">
                                Estado
                            </th>

                            <th class="col-vigencia">
                                Vigencia
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php foreach ($conceptos as $fila): ?>

                        <?php

                        /*
                        |--------------------------------------------------------------------------
                        | BADGE DE TIPO DE CONCEPTO
                        |--------------------------------------------------------------------------
                        */

                        $badgeClass =
                            'badge-default';


                        switch (
                            $fila['categoria']
                            ?? ''
                        ) {

                            case 'REMUNERATIVO':

                                $badgeClass =
                                    'badge-rem';

                                break;


                            case 'NO_REMUNERATIVO':

                                $badgeClass =
                                    'badge-no-rem';

                                break;


                            case 'ASIGNACION_FAMILIAR':

                                $badgeClass =
                                    'badge-asig';

                                break;


                            case 'DESCUENTO':

                                $badgeClass =
                                    'badge-desc';

                                break;


                            case 'APORTE_PATRONAL':

                                $badgeClass =
                                    'badge-aporte';

                                break;
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | VIGENCIA
                        |--------------------------------------------------------------------------
                        */

                        $desde =
                            !empty(
                                $fila['fecha_desde']
                            )
                                ?
                                date(
                                    "d/m/Y",
                                    strtotime(
                                        $fila['fecha_desde']
                                    )
                                )
                                :
                                '-';


                        $hasta =
                            !empty(
                                $fila['fecha_hasta']
                            )
                                ?
                                date(
                                    "d/m/Y",
                                    strtotime(
                                        $fila['fecha_hasta']
                                    )
                                )
                                :
                                '-';


                        /*
                        |--------------------------------------------------------------------------
                        | FORMA DE CÁLCULO
                        |--------------------------------------------------------------------------
                        |
                        | Se conserva el valor técnico en la base de datos, pero
                        | se muestra una descripción más clara para el usuario.
                        |
                        |--------------------------------------------------------------------------
                        */

                        $formaCalculo =
                            trim(
                                $fila['forma_calculo']
                                ?? ''
                            );


                        switch ($formaCalculo) {

                            case 'TABLA_CATEGORIA':

                                $formaCalculoTexto =
                                    'VALOR POR CATEGORÍA';

                                break;


                            case 'MANUAL':
                            case 'FIJO':

                                $formaCalculoTexto =
                                    'MANUAL';

                                break;


                            case 'PORCENTAJE':

                                $formaCalculoTexto =
                                    'PORCENTAJE';

                                break;


                            case 'FORMULA':

                                $formaCalculoTexto =
                                    'AUTOMÁTICO';

                                break;


                            default:

                                $formaCalculoTexto =
                                    $formaCalculo !== ''
                                        ?
                                        $formaCalculo
                                        :
                                        '-';

                                break;
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | PORCENTAJE
                        |--------------------------------------------------------------------------
                        |
                        | Se muestra únicamente cuando tiene un valor real.
                        |
                        |--------------------------------------------------------------------------
                        */

                        $porcentaje =
                            (float)(
                                $fila['porcentaje']
                                ?? 0
                            );


                        $porcentajeTexto =
                            $porcentaje != 0
                                ?
                                number_format(
                                    $porcentaje,
                                    2,
                                    ',',
                                    '.'
                                )
                                . ' %'
                                :
                                '-';


                        /*
                        |--------------------------------------------------------------------------
                        | MONTO FIJO
                        |--------------------------------------------------------------------------
                        */

                        $montoFijo =
                            (float)(
                                $fila['monto_fijo']
                                ?? 0
                            );


                        $montoFijoTexto =
                            $montoFijo != 0
                                ?
                                '$ '
                                .
                                number_format(
                                    $montoFijo,
                                    2,
                                    ',',
                                    '.'
                                )
                                :
                                '-';


                        /*
                        |--------------------------------------------------------------------------
                        | BASE DE CÁLCULO
                        |--------------------------------------------------------------------------
                        */

                        $baseCalculo =
                            trim(
                                $fila['base_calculo']
                                ?? ''
                            );


                        $baseCalculoTexto =
                            $baseCalculo !== ''
                                ?
                                $baseCalculo
                                :
                                '-';

                        ?>

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
                                data-label="Nombre"
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
                                data-label="Tipo de concepto"
                                class="col-categoria"
                            >

                                <span
                                    class="badge <?php
                                        echo $badgeClass;
                                    ?>"
                                >

                                    <?php
                                    echo htmlspecialchars(
                                        $fila['categoria']
                                        ?? '',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>

                                </span>

                            </td>


                            <td
                                data-label="Forma Cálculo"
                                class="col-forma"
                            >

                                <?php
                                echo htmlspecialchars(
                                    $formaCalculoTexto,
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </td>


                            <td
                                data-label="%"
                                class="col-porcentaje"
                            >

                                <?php
                                echo htmlspecialchars(
                                    $porcentajeTexto,
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </td>


                            <td
                                data-label="Monto Fijo"
                                class="col-monto"
                            >

                                <?php
                                echo htmlspecialchars(
                                    $montoFijoTexto,
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </td>


                            <td
                                data-label="Base Cálculo"
                                class="col-base"
                            >

                                <?php
                                echo htmlspecialchars(
                                    $baseCalculoTexto,
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

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
                                        Activo
                                    </span>

                                <?php else: ?>

                                    <span class="estado-inactivo">
                                        Inactivo
                                    </span>

                                <?php endif; ?>

                            </td>


                            <td
                                data-label="Vigencia"
                                class="col-vigencia"
                            >

                                <?php
                                echo htmlspecialchars(
                                    $desde
                                    .
                                    ' / '
                                    .
                                    $hasta,
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </td>


                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php else: ?>

            <div class="sin-registros">

                No se encontraron conceptos
                con los filtros seleccionados.

            </div>

        <?php endif; ?>

    </div>

</div>

</body>

</html>
