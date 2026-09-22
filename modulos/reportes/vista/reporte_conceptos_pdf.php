<?php

require_once __DIR__ . '/../../../core/Url.php';


/*
|--------------------------------------------------------------------------
| REPORTE DE CONCEPTOS - VISTA IMPRESIÓN / PDF - ROUTER
|--------------------------------------------------------------------------
*/

$reporteConceptosPdfFiltros = [
    'buscar' =>
        $buscar,

    'tipo_concepto' =>
        $tipoConcepto,

    'activo' =>
        $activo
];


$reporteConceptosPdfUrlVolver =
    sigenmuniUrlRuta(
        'reportes/conceptos',
        $reporteConceptosPdfFiltros
    );


function textoEstadoFiltro($activo)
{
    if ($activo === '1') {

        return 'Activos';
    }


    if ($activo === '0') {

        return 'Inactivos';
    }


    return 'Todos';
}


function textoTipoConceptoFiltro($tipoConcepto)
{
    $nombres = [

        'REMUNERATIVO' =>
            'Remunerativo',

        'NO_REMUNERATIVO' =>
            'No remunerativo',

        'ASIGNACION_FAMILIAR' =>
            'Asignación familiar',

        'DESCUENTO' =>
            'Descuento',

        'APORTE_PATRONAL' =>
            'Aporte patronal'
    ];


    if (
        $tipoConcepto !== ''
        &&
        isset(
            $nombres[$tipoConcepto]
        )
    ) {

        return $nombres[$tipoConcepto];
    }


    return 'Todos';
}


/*
|--------------------------------------------------------------------------
| TEXTO FORMA DE CÁLCULO
|--------------------------------------------------------------------------
*/

function textoFormaCalculo(
    $formaCalculo
) {

    $formaCalculo =
        strtoupper(
            trim(
                (string)$formaCalculo
            )
        );


    switch ($formaCalculo) {

        case 'TABLA_CATEGORIA':

            return 'VALOR POR CATEGORÍA';


        case 'MANUAL':
        case 'FIJO':

            return 'MANUAL';


        case 'PORCENTAJE':

            return 'PORCENTAJE';


        case 'FORMULA':
        case 'AUTOMATICO':
        case 'AUTOMÁTICO':

            return 'AUTOMÁTICO';


        default:

            return $formaCalculo !== ''
                ?
                $formaCalculo
                :
                '-';
    }
}

?>
<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Reporte de Conceptos - PDF</title>

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
    margin:20px auto;
    background:#fff;
    padding:20px;
    border-radius:12px;
    box-shadow:0 4px 14px rgba(0,0,0,.08);
}

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
    padding:10px 14px;
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

.encabezado{
    border:2px solid #d1d5db;
    border-radius:10px;
    padding:18px;
    margin-bottom:18px;
}

.encabezado h1{
    margin:0 0 6px 0;
    font-size:26px;
    color:#0891b2;
}

.encabezado p{
    margin:4px 0;
    font-size:14px;
}

.resumen{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
    gap:10px;
    margin-top:15px;
}

.info-box{
    border:1px solid #e5e7eb;
    background:#f9fafb;
    border-radius:8px;
    padding:10px 12px;
}

.info-box strong{
    display:block;
    margin-bottom:5px;
    font-size:13px;
    color:#374151;
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
}

th,
td{
    border:1px solid #d1d5db;
    padding:7px 5px;
    font-size:11px;
    text-align:left;
    vertical-align:middle;
    overflow-wrap:anywhere;
}

th{
    background:#0891b2;
    color:white;
    white-space:normal;
}

.col-codigo{
    width:7%;
}

.col-nombre{
    width:17%;
}

.col-categoria{
    width:14%;
}

.col-forma{
    width:16%;
}

.col-porcentaje{
    width:6%;
    text-align:center;
}

.col-monto{
    width:9%;
}

.col-base{
    width:10%;
}

.col-estado{
    width:7%;
    text-align:center;
}

.col-vigencia{
    width:14%;
}

.badge{
    display:inline-block;
    padding:4px 6px;
    border-radius:999px;
    font-size:9px;
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

.estado-activo{
    display:inline-block;
    background:#dcfce7;
    color:#166534;
    font-weight:bold;
    padding:4px 7px;
    border-radius:999px;
    font-size:10px;
}

.estado-inactivo{
    display:inline-block;
    background:#fee2e2;
    color:#991b1b;
    font-weight:bold;
    padding:4px 7px;
    border-radius:999px;
    font-size:10px;
}

.sin-registros{
    text-align:center;
    padding:20px;
    color:#6b7280;
    border:1px solid #d1d5db;
    border-radius:8px;
    background:#fafafa;
}


/* =========================================
   IMPRESIÓN A4 HORIZONTAL
========================================= */

@media print{

    @page{
        size:A4 landscape;
        margin:8mm;
    }

    body{
        background:#fff;
    }

    .acciones{
        display:none;
    }

    .contenedor{
        width:100%;
        max-width:100%;
        margin:0;
        padding:0;
        border-radius:0;
        box-shadow:none;
    }

    .encabezado{
        padding:10px;
        margin-bottom:10px;
        page-break-inside:avoid;
    }

    .encabezado h1{
        font-size:20px;
    }

    .encabezado p{
        font-size:10px;
    }

    .resumen{
        gap:5px;
        margin-top:8px;
    }

    .info-box{
        padding:6px;
        font-size:9px;
    }

    .info-box strong{
        font-size:9px;
    }

    table{
        width:100%;
        min-width:0;
        table-layout:fixed;
    }

    th,
    td{
        padding:4px 3px;
        font-size:8px;
    }

    .badge,
    .estado-activo,
    .estado-inactivo{
        font-size:7px;
        padding:3px 4px;
    }

    tr{
        page-break-inside:avoid;
    }
}

@media(max-width:768px){

    .contenedor{
        width:98%;
        margin:10px auto;
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
                    $reporteConceptosPdfUrlVolver,
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
            Reporte de Conceptos
        </h1>

        <p>
            <strong>SIGENMUNI</strong>
            - Municipalidad de Fortín Lugones
        </p>

        <p>
            Fecha de emisión:
            <?php echo date("d/m/Y H:i:s"); ?>
        </p>


        <div class="resumen">

            <div class="info-box">

                <strong>
                    Búsqueda aplicada
                </strong>

                <?php
                echo htmlspecialchars(
                    $buscar !== ''
                        ? $buscar
                        : 'Todos',
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>

            </div>


            <div class="info-box">

                <strong>
                    Tipo de concepto
                </strong>

                <?php
                echo htmlspecialchars(
                    textoTipoConceptoFiltro(
                        $tipoConcepto
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>

            </div>


            <div class="info-box">

                <strong>
                    Estado
                </strong>

                <?php
                echo htmlspecialchars(
                    textoEstadoFiltro($activo),
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>

            </div>


            <div class="info-box">

                <strong>
                    Total de conceptos
                </strong>

                <?php
                echo (int)$totalConceptos;
                ?>

            </div>

        </div>

    </div>


    <!-- =====================================
         TABLA
    ====================================== -->

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

                    $badgeClass =
                        'badge-default';


                    switch (
                        $fila['categoria']
                        ?? ''
                    ) {

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


                        <td class="col-codigo">

                            <?php
                            echo htmlspecialchars(
                                $fila['codigo']
                                ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>

                        </td>


                        <td class="col-nombre">

                            <?php
                            echo htmlspecialchars(
                                $fila['nombre']
                                ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>

                        </td>


                        <td class="col-categoria">

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


                        <td class="col-forma">

                            <?php
                            echo htmlspecialchars(
                                textoFormaCalculo(
                                    $fila['forma_calculo']
                                    ?? ''
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>

                        </td>


                        <td class="col-porcentaje">

                            <?php
                            echo htmlspecialchars(
                                $porcentajeTexto,
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>

                        </td>


                        <td class="col-monto">

                            <?php
                            echo htmlspecialchars(
                                $montoFijoTexto,
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>

                        </td>
<td class="col-base">

                            <?php
                            echo htmlspecialchars(
                                $baseCalculoTexto,
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>

                        </td>


                        <td class="col-estado">

                            <?php if (
                                (int)(
                                    $fila['activo']
                                    ?? 0
                                )
                                === 1
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


                        <td class="col-vigencia">

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

</body>

</html>
