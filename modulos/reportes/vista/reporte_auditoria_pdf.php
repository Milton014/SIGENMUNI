<?php

date_default_timezone_set(
    'America/Argentina/Buenos_Aires'
);

require_once __DIR__ . '/../../../lib/fpdf/fpdf.php';


/*
|--------------------------------------------------------------------------
| REPORTE DE AUDITORÍA - PDF - ROUTER
|--------------------------------------------------------------------------
|
| Los filtros, los registros y los datos del usuario generador son preparados
| por ReporteControlador::auditoriaPdf().
|
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| FUNCIONES AUXILIARES
|--------------------------------------------------------------------------
*/

function auditoriaPdfFechaValida($fecha)
{
    if ($fecha === '') {

        return true;
    }


    $objetoFecha =
        DateTime::createFromFormat(
            'Y-m-d',
            $fecha
        );


    return (
        $objetoFecha !== false
        &&
        $objetoFecha->format(
            'Y-m-d'
        )
        ===
        $fecha
    );
}


function auditoriaPdfTexto($valor)
{
    $texto =
        (string)$valor;


    $texto =
        str_replace(
            [
                "\r\n",
                "\r",
                "\t"
            ],
            [
                "\n",
                "\n",
                " "
            ],
            $texto
        );


    if (
        function_exists(
            'iconv'
        )
    ) {

        $convertido =
            @iconv(
                'UTF-8',
                'Windows-1252//TRANSLIT//IGNORE',
                $texto
            );


        if ($convertido !== false) {

            return $convertido;
        }
    }


    if (
        function_exists(
            'mb_convert_encoding'
        )
    ) {

        return mb_convert_encoding(
            $texto,
            'Windows-1252',
            'UTF-8'
        );
    }


    return preg_replace(
        '/[^\x20-\x7E\n]/',
        '?',
        $texto
    );
}


function auditoriaPdfFechaHora($fechaHora)
{
    if (
        $fechaHora === null
        ||
        trim(
            (string)$fechaHora
        )
        === ''
    ) {

        return '-';
    }


    $timestamp =
        strtotime(
            $fechaHora
        );


    if ($timestamp === false) {

        return (string)$fechaHora;
    }


    return date(
        'd/m/Y H:i:s',
        $timestamp
    );
}


function auditoriaPdfFechaCorta($fecha)
{
    if ($fecha === '') {

        return 'Todas';
    }


    $timestamp =
        strtotime(
            $fecha
        );


    if ($timestamp === false) {

        return $fecha;
    }


    return date(
        'd/m/Y',
        $timestamp
    );
}


function auditoriaPdfFiltroTexto($valor)
{
    $valor =
        trim(
            (string)$valor
        );


    return $valor === ''
        ?
        'Todos'
        :
        $valor;
}




/*
|--------------------------------------------------------------------------
| CLASE PDF
|--------------------------------------------------------------------------
*/

class AuditoriaPDF extends FPDF
{
    /*
    |--------------------------------------------------------------------------
    | ANCHOS DE COLUMNAS - SIN IP
    |--------------------------------------------------------------------------
    |
    | La IP se conserva en la base de datos y en el detalle de auditoría de la
    | pantalla, pero se oculta del PDF general para mejorar la legibilidad.
    |
    | Ancho total: 281 mm.
    |
    |--------------------------------------------------------------------------
    */

    private $anchos = [
        29,
        30,
        20,
        37,
        27,
        62,
        76
    ];


    private $titulos = [
        'Fecha / Hora',
        'Usuario',
        'Rol',
        'Modulo',
        'Accion',
        'Registro afectado',
        'Detalle'
    ];


    /*
    |--------------------------------------------------------------------------
    | HEADER GENERAL
    |--------------------------------------------------------------------------
    */

    public function Header()
    {
        $this->SetFillColor(
            67,
            56,
            202
        );

        $this->Rect(
            0,
            0,
            297,
            19,
            'F'
        );


        $this->SetTextColor(
            255,
            255,
            255
        );

        $this->SetFont(
            'Arial',
            'B',
            15
        );

        $this->SetXY(
            8,
            5
        );

        $this->Cell(
            130,
            6,
            auditoriaPdfTexto(
                'SIGENMUNI'
            ),
            0,
            0,
            'L'
        );


        $this->SetFont(
            'Arial',
            '',
            8
        );

        $this->SetXY(
            145,
            5
        );

        $this->Cell(
            144,
            5,
            auditoriaPdfTexto(
                'Sistema de Gestión Municipal'
            ),
            0,
            1,
            'R'
        );


        $this->SetXY(
            145,
            10
        );

        $this->Cell(
            144,
            5,
            auditoriaPdfTexto(
                'Reporte de Auditoría'
            ),
            0,
            1,
            'R'
        );


        $this->SetTextColor(
            31,
            41,
            55
        );

        $this->SetY(
            23
        );
    }


    /*
    |--------------------------------------------------------------------------
    | FOOTER
    |--------------------------------------------------------------------------
    */

    public function Footer()
    {
        $this->SetY(
            -10
        );


        $this->SetDrawColor(
            209,
            213,
            219
        );

        $this->Line(
            8,
            $this->GetY(),
            289,
            $this->GetY()
        );


        $this->SetY(
            -8
        );

        $this->SetFont(
            'Arial',
            '',
            7
        );

        $this->SetTextColor(
            107,
            114,
            128
        );


        $this->Cell(
            140,
            4,
            auditoriaPdfTexto(
                'SIGENMUNI - Reporte de Auditoría - Solo consulta'
            ),
            0,
            0,
            'L'
        );


        $this->Cell(
            141,
            4,
            auditoriaPdfTexto(
                'Página '
                .
                $this->PageNo()
                .
                ' de {nb}'
            ),
            0,
            0,
            'R'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CABECERA DE TABLA
    |--------------------------------------------------------------------------
    */

    public function cabeceraTabla()
    {
        $this->SetFillColor(
            79,
            70,
            229
        );

        $this->SetTextColor(
            255,
            255,
            255
        );

        $this->SetDrawColor(
            199,
            210,
            254
        );

        $this->SetFont(
            'Arial',
            'B',
            6.5
        );


        foreach (
            $this->titulos
            as
            $indice => $titulo
        ) {

            $this->Cell(
                $this->anchos[$indice],
                8,
                auditoriaPdfTexto(
                    $titulo
                ),
                1,
                0,
                'C',
                true
            );
        }


        $this->Ln();


        $this->SetTextColor(
            31,
            41,
            55
        );

        $this->SetFont(
            'Arial',
            '',
            6.5
        );
    }


    /*
    |--------------------------------------------------------------------------
    | FILA MULTILÍNEA
    |--------------------------------------------------------------------------
    */

    public function fila(array $datos)
    {
        $numeroLineas =
            1;


        for (
            $i = 0;
            $i < count(
                $this->anchos
            );
            $i++
        ) {

            $numeroLineas =
                max(
                    $numeroLineas,
                    $this->numeroLineas(
                        $this->anchos[$i],
                        auditoriaPdfTexto(
                            $datos[$i]
                            ??
                            ''
                        )
                    )
                );
        }


        $altoLinea =
            3.8;


        $altoFila =
            $altoLinea
            *
            $numeroLineas;


        if (
            $this->GetY()
            +
            $altoFila
            >
            194
        ) {

            $this->AddPage(
                'L'
            );

            $this->cabeceraTabla();
        }


        $x =
            $this->GetX();


        $y =
            $this->GetY();


        for (
            $i = 0;
            $i < count(
                $this->anchos
            );
            $i++
        ) {

            $ancho =
                $this->anchos[$i];


            $texto =
                auditoriaPdfTexto(
                    $datos[$i]
                    ??
                    ''
                );


            $this->Rect(
                $x,
                $y,
                $ancho,
                $altoFila
            );


            $this->SetXY(
                $x,
                $y
            );


            $alineacion =
                $i === 0
                ||
                $i === 2
                ||
                $i === 4
                    ?
                    'C'
                    :
                    'L';


            $this->MultiCell(
                $ancho,
                $altoLinea,
                $texto,
                0,
                $alineacion
            );


            $x +=
                $ancho;
        }


        $this->SetXY(
            8,
            $y
            +
            $altoFila
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CALCULAR CANTIDAD DE LÍNEAS
    |--------------------------------------------------------------------------
    |
    | Adaptado al comportamiento estándar de MultiCell de FPDF.
    |
    |--------------------------------------------------------------------------
    */

    private function numeroLineas(
        $ancho,
        $texto
    ) {
        $cw =
            $this->CurrentFont['cw'];


        if ($ancho === 0) {

            $ancho =
                $this->w
                -
                $this->rMargin
                -
                $this->x;
        }


        $anchoMaximo =
            (
                $ancho
                -
                2
                *
                $this->cMargin
            )
            *
            1000
            /
            $this->FontSize;


        $texto =
            str_replace(
                "\r",
                '',
                $texto
            );


        $longitud =
            strlen(
                $texto
            );


        if (
            $longitud > 0
            &&
            $texto[
                $longitud
                -
                1
            ]
            ===
            "\n"
        ) {

            $longitud--;
        }


        $separador =
            -1;

        $i =
            0;

        $j =
            0;

        $anchoActual =
            0;

        $lineas =
            1;


        while (
            $i < $longitud
        ) {

            $caracter =
                $texto[$i];


            if (
                $caracter
                ===
                "\n"
            ) {

                $i++;

                $separador =
                    -1;

                $j =
                    $i;

                $anchoActual =
                    0;

                $lineas++;

                continue;
            }


            if (
                $caracter
                ===
                ' '
            ) {

                $separador =
                    $i;
            }


            $anchoActual +=
                $cw[$caracter]
                ??
                0;


            if (
                $anchoActual
                >
                $anchoMaximo
            ) {

                if (
                    $separador
                    ===
                    -1
                ) {

                    if (
                        $i
                        ===
                        $j
                    ) {

                        $i++;
                    }

                } else {

                    $i =
                        $separador
                        +
                        1;
                }


                $separador =
                    -1;

                $j =
                    $i;

                $anchoActual =
                    0;

                $lineas++;

            } else {

                $i++;
            }
        }


        return $lineas;
    }
}


/*
|--------------------------------------------------------------------------
| CREAR PDF
|--------------------------------------------------------------------------
*/

$pdf =
    new AuditoriaPDF(
        'L',
        'mm',
        'A4'
    );


$pdf->AliasNbPages();

$pdf->SetMargins(
    8,
    8,
    8
);

$pdf->SetAutoPageBreak(
    true,
    13
);

$pdf->AddPage();


/*
|--------------------------------------------------------------------------
| TÍTULO DEL REPORTE
|--------------------------------------------------------------------------
*/

$pdf->SetFont(
    'Arial',
    'B',
    16
);

$pdf->SetTextColor(
    49,
    46,
    129
);

$pdf->Cell(
    0,
    7,
    auditoriaPdfTexto(
        'Reporte de Auditoría'
    ),
    0,
    1,
    'L'
);


$pdf->SetFont(
    'Arial',
    '',
    8
);

$pdf->SetTextColor(
    75,
    85,
    99
);

$pdf->Cell(
    0,
    5,
    auditoriaPdfTexto(
        'Historial de acciones realizadas por los usuarios del sistema.'
    ),
    0,
    1,
    'L'
);


$pdf->Ln(
    2
);


/*
|--------------------------------------------------------------------------
| INFORMACIÓN GENERAL
|--------------------------------------------------------------------------
*/

$pdf->SetFillColor(
    238,
    242,
    255
);

$pdf->SetDrawColor(
    199,
    210,
    254
);

$pdf->SetTextColor(
    49,
    46,
    129
);

$pdf->SetFont(
    'Arial',
    'B',
    7.5
);


$pdf->Cell(
    70,
    7,
    auditoriaPdfTexto(
        'Generado por: '
        .
        $usuarioGenerador
    ),
    1,
    0,
    'L',
    true
);


$pdf->Cell(
    55,
    7,
    auditoriaPdfTexto(
        'Rol: '
        .
        auditoriaPdfFiltroTexto(
            $rolGenerador
        )
    ),
    1,
    0,
    'L',
    true
);


$pdf->Cell(
    75,
    7,
    auditoriaPdfTexto(
        'Fecha de generación: '
        .
        date(
            'd/m/Y H:i:s'
        )
    ),
    1,
    0,
    'L',
    true
);


$pdf->Cell(
    81,
    7,
    auditoriaPdfTexto(
        'Total de movimientos: '
        .
        $totalAuditorias
    ),
    1,
    1,
    'L',
    true
);


$pdf->Ln(
    3
);


/*
|--------------------------------------------------------------------------
| FILTROS APLICADOS
|--------------------------------------------------------------------------
*/

$pdf->SetTextColor(
    31,
    41,
    55
);

$pdf->SetFont(
    'Arial',
    'B',
    8
);

$pdf->Cell(
    0,
    5,
    auditoriaPdfTexto(
        'Filtros aplicados'
    ),
    0,
    1,
    'L'
);


$pdf->SetFont(
    'Arial',
    '',
    7.2
);


$filtroLinea1 =
    'Desde: '
    .
    auditoriaPdfFechaCorta(
        $fechaDesde
    )
    .
    '   |   Hasta: '
    .
    auditoriaPdfFechaCorta(
        $fechaHasta
    )
    .
    '   |   Usuario: '
    .
    auditoriaPdfFiltroTexto(
        $usuario
    )
    .
    '   |   Rol: '
    .
    auditoriaPdfFiltroTexto(
        $rol
    );


$filtroLinea2 =
    'Módulo: '
    .
    auditoriaPdfFiltroTexto(
        $modulo
    )
    .
    '   |   Acción: '
    .
    auditoriaPdfFiltroTexto(
        $accion
    )
    .
    '   |   Entidad: '
    .
    auditoriaPdfFiltroTexto(
        $entidad
    );


$pdf->MultiCell(
    0,
    4,
    auditoriaPdfTexto(
        $filtroLinea1
    ),
    0,
    'L'
);


$pdf->MultiCell(
    0,
    4,
    auditoriaPdfTexto(
        $filtroLinea2
    ),
    0,
    'L'
);


$pdf->Ln(
    3
);


/*
|--------------------------------------------------------------------------
| TABLA
|--------------------------------------------------------------------------
*/

$pdf->cabeceraTabla();


if (
    empty(
        $auditorias
    )
) {

    $pdf->SetFont(
        'Arial',
        'I',
        9
    );

    $pdf->SetTextColor(
        107,
        114,
        128
    );

    $pdf->Cell(
        281,
        12,
        auditoriaPdfTexto(
            'No se encontraron registros de auditoría para los filtros seleccionados.'
        ),
        1,
        1,
        'C'
    );


} else {

    $pdf->SetFont(
        'Arial',
        '',
        6.5
    );


    $pdf->SetTextColor(
        31,
        41,
        55
    );


    $pdf->SetDrawColor(
        209,
        213,
        219
    );


    foreach (
        $auditorias
        as
        $fila
    ) {

        $usuarioPdf =
            trim(
                (string)(
                    $fila['usuario_login']
                    ??
                    ''
                )
            );


        $nombreUsuarioPdf =
            trim(
                (string)(
                    $fila['usuario_nombre']
                    ??
                    ''
                )
            );


        if (
            $nombreUsuarioPdf !== ''
            &&
            strcasecmp(
                $usuarioPdf,
                $nombreUsuarioPdf
            )
            !==
            0
        ) {

            $usuarioPdf .=
                "\n"
                .
                $nombreUsuarioPdf;
        }


        if (
            $usuarioPdf === ''
        ) {

            $usuarioPdf =
                '-';
        }


        $registroPdf =
            trim(
                (string)(
                    $fila['entidad_descripcion']
                    ??
                    ''
                )
            );


        if (
            $registroPdf === ''
        ) {

            $registroPdf =
                trim(
                    (string)(
                        $fila['entidad']
                        ??
                        ''
                    )
                );
        }


        if (
            !empty(
                $fila['entidad_id']
            )
        ) {

            $registroPdf .=
                "\nID "
                .
                (int)$fila['entidad_id'];
        }


        if (
            $registroPdf === ''
        ) {

            $registroPdf =
                '-';
        }


        $pdf->fila(
            [
                auditoriaPdfFechaHora(
                    $fila['fecha_hora']
                    ??
                    ''
                ),

                $usuarioPdf,

                $fila['rol']
                ??
                '-',

                $fila['modulo']
                ??
                '-',

                $fila['accion']
                ??
                '-',

                $registroPdf,

                $fila['detalle']
                ??
                '-'
            ]
        );
    }
}


/*
|--------------------------------------------------------------------------
| SALIDA
|--------------------------------------------------------------------------
*/

$nombreArchivo =
    'reporte_auditoria_'
    .
    date(
        'Ymd_His'
    )
    .
    '.pdf';


$pdf->Output(
    'D',
    $nombreArchivo
);

exit();
