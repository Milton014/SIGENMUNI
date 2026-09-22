<?php

/*
|--------------------------------------------------------------------------
| HISTORIAL POR EMPLEADO - VISTA EXCEL
|--------------------------------------------------------------------------
|
| Los datos son preparados por:
|
| ReporteControlador->historialEmpleadoExcel()
|
| Se conserva el formato HTML compatible con Excel (.xls).
|
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| FORMATEAR CUIT DE UNIDAD DE ORGANIZACIÓN
|--------------------------------------------------------------------------
*/

function formatearCuitUnidadOrganizacionHistorialExcel($cuit)
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


/*
|--------------------------------------------------------------------------
| NOMBRE DEL ARCHIVO
|--------------------------------------------------------------------------
*/

$nombreArchivo =
    "historial_empleado_"
    .
    $empleadoId
    .
    "_"
    .
    date(
        "Ymd_His"
    )
    .
    ".xls";


/*
|--------------------------------------------------------------------------
| HEADERS EXCEL
|--------------------------------------------------------------------------
*/

header(
    "Content-Type: application/vnd.ms-excel; charset=UTF-8"
);

header(
    'Content-Disposition: attachment; filename="'
    .
    $nombreArchivo
    .
    '"'
);

header(
    "Pragma: no-cache"
);

header(
    "Expires: 0"
);


/*
|--------------------------------------------------------------------------
| BOM UTF-8
|--------------------------------------------------------------------------
*/

echo "\xEF\xBB\xBF";

?>
<!DOCTYPE html>

<html lang="es">

<head>

<meta charset="UTF-8">

<title>
    Historial por Empleado
</title>

<style>

table{
    border-collapse:collapse;
    width:100%;
}


th,
td{
    border:1px solid #000;
    padding:6px;
    font-size:12px;
    vertical-align:top;
}


th{
    background:#d9ead3;
    font-weight:bold;
}


.titulo{
    font-size:16px;
    font-weight:bold;
}


.subtitulo{
    font-size:12px;
}


.total{
    background:#ecfeff;
    font-weight:bold;
}

</style>

</head>


<body>


<!-- =========================================
     ENCABEZADO
========================================= -->

<table>


    <tr>

        <td
            colspan="11"
            class="titulo"
        >
            SIGENMUNI - Historial por Empleado
        </td>

    </tr>


    <tr>

        <td
            colspan="11"
            class="subtitulo"
        >
            Municipalidad de Fortín Lugones
        </td>

    </tr>


    <tr>

        <td
            colspan="11"
            class="subtitulo"
        >

            Fecha de emisión:

            <?php
            echo date(
                "d/m/Y H:i:s"
            );
            ?>

        </td>

    </tr>


    <tr>

        <td
            colspan="11"
            class="subtitulo"
        >

            <strong>
                Empleado:
            </strong>

            <?php
            echo htmlspecialchars(
                ($empleado['apellido'] ?? '')
                . ', '
                . ($empleado['nombre'] ?? ''),
                ENT_QUOTES,
                'UTF-8'
            );
            ?>

            |

            <strong>
                Legajo:
            </strong>

            <?php
            echo htmlspecialchars(
                $empleado['nro_legajo']
                ?? '',
                ENT_QUOTES,
                'UTF-8'
            );
            ?>

            |

            <strong>
                DNI:
            </strong>

            <?php
            echo htmlspecialchars(
                $empleado['dni']
                ?? '',
                ENT_QUOTES,
                'UTF-8'
            );
            ?>

            |

            <strong>
                CUIL:
            </strong>

            <?php
            echo htmlspecialchars(
                $empleado['cuil']
                ?? '',
                ENT_QUOTES,
                'UTF-8'
            );
            ?>

        </td>

    </tr>


    <tr>

        <td
            colspan="11"
            class="subtitulo"
        >

            <strong>
                Categoría:
            </strong>

            <?php
            echo htmlspecialchars(
                $empleado['categoria']
                ?? '-',
                ENT_QUOTES,
                'UTF-8'
            );
            ?>

            |

            <strong>
                Unidad de Organización:
            </strong>

            <?php
            echo htmlspecialchars(
                $empleado['oficina']
                ?? '-',
                ENT_QUOTES,
                'UTF-8'
            );
            ?>


            <?php if (!empty($empleado['oficina_cuit'])): ?>

                (
                CUIT:
                <?php
                echo htmlspecialchars(
                    formatearCuitUnidadOrganizacionHistorialExcel(
                        $empleado['oficina_cuit']
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>
                )

            <?php endif; ?>

            |

            <strong>
                Situación:
            </strong>

            <?php
            echo htmlspecialchars(
                $empleado['situacion']
                ?? '-',
                ENT_QUOTES,
                'UTF-8'
            );
            ?>

        </td>

    </tr>


    <tr>

        <td
            colspan="11"
            class="subtitulo"
        >

            <strong>
                Fecha Alta:
            </strong>

            <?php
            echo !empty(
                $empleado['fecha_alta']
            )
                ?
                date(
                    "d/m/Y",
                    strtotime(
                        $empleado['fecha_alta']
                    )
                )
                :
                '';
            ?>

            |

            <strong>
                Fecha Baja:
            </strong>

            <?php
            echo !empty(
                $empleado['fecha_baja']
            )
                ?
                date(
                    "d/m/Y",
                    strtotime(
                        $empleado['fecha_baja']
                    )
                )
                :
                '';
            ?>

            |

            <strong>
                Estado del Empleado:
            </strong>

            <?php
            echo (
                (int)(
                    $empleado['activo']
                    ?? 0
                )
                === 1
            )
                ?
                'Activo'
                :
                'Inactivo';
            ?>

        </td>

    </tr>


    <tr>

        <td
            colspan="11"
            class="total"
        >

            Total de liquidaciones:

            <?php
            echo (int)$totalLiquidaciones;
            ?>

        </td>

    </tr>


</table>


<br>


<!-- =========================================
     HISTORIAL
========================================= -->

<table>


    <thead>

        <tr>

            <th>
                ID
            </th>

            <th>
                Tipo
            </th>

            <th>
                Período
            </th>

            <th>
                Fecha Liquidación
            </th>

            <th>
                Estado
            </th>

            <th>
                Descripción
            </th>

            <th>
                Total Remunerativo
            </th>

            <th>
                Total Descuentos
            </th>

            <th>
                No Remunerativo
            </th>

            <th>
                Asignaciones
            </th>

            <th>
                Neto
            </th>

        </tr>

    </thead>


    <tbody>


    <?php if (!empty($historial)): ?>


        <?php foreach ($historial as $fila): ?>


            <tr>


                <td>

                    <?php
                    echo (int)(
                        $fila['liquidacion_id']
                        ?? 0
                    );
                    ?>

                </td>


                <td>

                    <?php
                    echo htmlspecialchars(
                        $fila['tipo_liquidacion']
                        ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>

                </td>


                <td>

                    <?php
                    echo htmlspecialchars(
                        $fila['periodo']
                        ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>

                </td>


                <td>

                    <?php
                    echo !empty(
                        $fila['fecha_liquidacion']
                    )
                        ?
                        date(
                            "d/m/Y",
                            strtotime(
                                $fila['fecha_liquidacion']
                            )
                        )
                        :
                        '';
                    ?>

                </td>


                <td>

                    <?php
                    echo htmlspecialchars(
                        strtoupper(
                            $fila['estado']
                            ?? ''
                        ),
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>

                </td>


                <td>

                    <?php
                    echo htmlspecialchars(
                        $fila['descripcion']
                        ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>

                </td>


                <td>

                    <?php
                    echo number_format(
                        (float)(
                            $fila['total_remunerativo']
                            ?? 0
                        ),
                        2,
                        ',',
                        '.'
                    );
                    ?>

                </td>


                <td>

                    <?php
                    echo number_format(
                        (float)(
                            $fila['total_descuentos']
                            ?? 0
                        ),
                        2,
                        ',',
                        '.'
                    );
                    ?>

                </td>


                <td>

                    <?php
                    echo number_format(
                        (float)(
                            $fila['total_no_remunerativo']
                            ?? 0
                        ),
                        2,
                        ',',
                        '.'
                    );
                    ?>

                </td>


                <td>

                    <?php
                    echo number_format(
                        (float)(
                            $fila['total_asignaciones']
                            ?? 0
                        ),
                        2,
                        ',',
                        '.'
                    );
                    ?>

                </td>


                <td>

                    <?php
                    echo number_format(
                        (float)(
                            $fila['neto']
                            ?? 0
                        ),
                        2,
                        ',',
                        '.'
                    );
                    ?>

                </td>


            </tr>


        <?php endforeach; ?>


    <?php else: ?>


        <tr>

            <td colspan="11">

                Este empleado no tiene
                liquidaciones registradas.

            </td>

        </tr>


    <?php endif; ?>


    </tbody>


</table>


</body>

</html>