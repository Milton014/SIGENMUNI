<?php

require_once __DIR__ . '/../../../lib/fpdf/fpdf.php';


/*
|--------------------------------------------------------------------------
| GENERAR PDF - ESTADÍSTICAS - ROUTER
|--------------------------------------------------------------------------
|
| Los filtros, el usuario y los datos estadísticos son preparados por
| ReporteControlador::estadisticasPdf().
|
| Desde el navegador se reciben únicamente las imágenes de Chart.js.
|
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| DATOS RECIBIDOS DESDE CHART.JS
|--------------------------------------------------------------------------
|
| La vista nueva envía:
|
| - graficos: objeto identificado por nombre.
| - imagenes: array posicional mantenido por compatibilidad.
|
| Se prioriza "graficos" porque evita desplazar títulos si falta algún gráfico.
|
|--------------------------------------------------------------------------
*/

$data =
    json_decode(
        file_get_contents(
            "php://input"
        ),
        true
    );


if (!is_array($data)) {

    $data = [];
}


$graficosRecibidos =
    is_array(
        $data['graficos']
        ?? null
    )
        ? $data['graficos']
        : [];


$imagenesCompatibles =
    is_array(
        $data['imagenes']
        ?? null
    )
        ? $data['imagenes']
        : [];


/*
|--------------------------------------------------------------------------
| NORMALIZAR IMÁGENES
|--------------------------------------------------------------------------
*/

$clavesGraficos = [
    'categoria',
    'periodo',
    'conceptos',
    'descuentos'
];


$imagenesGraficos = [];


foreach (
    $clavesGraficos as $indice => $clave
) {

    $imagen = null;


    if (
        isset(
            $graficosRecibidos[$clave]
        )
        &&
        is_string(
            $graficosRecibidos[$clave]
        )
    ) {

        $imagen =
            trim(
                $graficosRecibidos[$clave]
            );


    } elseif (
        isset(
            $imagenesCompatibles[$indice]
        )
        &&
        is_string(
            $imagenesCompatibles[$indice]
        )
    ) {

        $imagen =
            trim(
                $imagenesCompatibles[$indice]
            );
    }


    $imagenesGraficos[$clave] =
        $imagen !== ''
            ? $imagen
            : null;
}




/*
|--------------------------------------------------------------------------
| FORMATO
|--------------------------------------------------------------------------
*/

function formatoPesosPdf($numero)
{
    return '$ '
        .
        number_format(
            (float)$numero,
            2,
            ',',
            '.'
        );
}


function textoPeriodoPdf(
    $desde,
    $hasta
) {

    if (
        $desde === ''
        &&
        $hasta === ''
    ) {

        return 'Histórico completo';
    }


    if (
        $desde !== ''
        &&
        $hasta !== ''
    ) {

        return $desde
            .
            ' a '
            .
            $hasta;
    }


    if ($desde !== '') {

        return 'Desde '
            .
            $desde;
    }


    return 'Hasta '
        .
        $hasta;
}


function textoTipoLiquidacionPdf($tipo)
{
    $tipo =
        strtoupper(
            trim(
                (string)$tipo
            )
        );


    if ($tipo === '') {

        return 'Todas';
    }


    $nombres = [

        'MENSUAL' =>
            'Mensual',

        'COMPLEMENTARIA' =>
            'Complementaria',

        'AGUINALDO' =>
            'Aguinaldo',

        'COMPLEMENTARIA_SAC' =>
            'Complementaria SAC',

        'GASTOS_PROTOCOLARES' =>
            'Gastos Protocolares'
    ];


    if (
        isset(
            $nombres[$tipo]
        )
    ) {

        return $nombres[$tipo];
    }


    return ucwords(
        strtolower(
            str_replace(
                '_',
                ' ',
                $tipo
            )
        )
    );
}


function limpiarTextoPdf($texto)
{
    $texto =
        trim(
            (string)$texto
        );


    if ($texto === '') {

        return '-';
    }


    return $texto;
}


/*
|--------------------------------------------------------------------------
| CLASE PDF
|--------------------------------------------------------------------------
*/

class PDFEstadisticas extends FPDF
{
    public $usuario;
    public $rol;
    public $periodoTexto;
    public $tipoTexto;


    /*
    |--------------------------------------------------------------------------
    | CABECERA
    |--------------------------------------------------------------------------
    */

    function Header()
    {
        $this->SetFillColor(
            234,
            88,
            12
        );


        $this->Rect(
            0,
            0,
            210,
            34,
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


        $this->Cell(
            0,
            8,
            utf8_decode(
                'SIGENMUNI - Reporte de Estadísticas'
            ),
            0,
            1,
            'C'
        );


        $this->SetFont(
            'Arial',
            '',
            9
        );


        $this->Cell(
            0,
            5,
            utf8_decode(
                'Municipalidad de Fortín Lugones'
            ),
            0,
            1,
            'C'
        );


        $this->Cell(
            0,
            5,
            utf8_decode(
                'Período: '
                .
                $this->periodoTexto
                .
                ' | Tipo: '
                .
                $this->tipoTexto
            ),
            0,
            1,
            'C'
        );


        $this->Cell(
            0,
            5,
            utf8_decode(
                'Estado considerado: CERRADA'
            ),
            0,
            1,
            'C'
        );


        $this->Ln(8);
    }


    /*
    |--------------------------------------------------------------------------
    | PIE
    |--------------------------------------------------------------------------
    */

    function Footer()
    {
        $this->SetY(
            -18
        );


        $this->SetDrawColor(
            220,
            220,
            220
        );


        $this->Line(
            12,
            $this->GetY(),
            198,
            $this->GetY()
        );


        $this->Ln(3);


        $this->SetFont(
            'Arial',
            '',
            8
        );


        $this->SetTextColor(
            100,
            100,
            100
        );


        $this->Cell(
            0,
            5,
            utf8_decode(
                'Generado por: '
                .
                $this->usuario
                .
                ' | Rol: '
                .
                $this->rol
            ),
            0,
            1,
            'L'
        );


        $this->Cell(
            0,
            5,
            utf8_decode(
                'Fecha: '
                .
                date(
                    'd/m/Y H:i'
                )
                .
                ' | Página '
                .
                $this->PageNo()
                .
                '/{nb}'
            ),
            0,
            0,
            'L'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | TÍTULO DE SECCIÓN
    |--------------------------------------------------------------------------
    */

    function TituloSeccion(
        $titulo,
        $descripcion = ''
    ) {

        $this->SetTextColor(
            31,
            41,
            55
        );


        $this->SetFont(
            'Arial',
            'B',
            13
        );


        $this->Cell(
            0,
            8,
            utf8_decode(
                $titulo
            ),
            0,
            1,
            'L'
        );


        if (
            trim(
                (string)$descripcion
            )
            !==
            ''
        ) {

            $this->SetFont(
                'Arial',
                '',
                9
            );


            $this->SetTextColor(
                90,
                90,
                90
            );


            $this->MultiCell(
                0,
                5,
                utf8_decode(
                    $descripcion
                )
            );
        }


        $this->Ln(4);
    }


    /*
    |--------------------------------------------------------------------------
    | TARJETA RESUMEN
    |--------------------------------------------------------------------------
    */

    function Tarjeta(
        $x,
        $y,
        $ancho,
        $alto,
        $titulo,
        $valor,
        $detalle = ''
    ) {

        $this->SetFillColor(
            255,
            247,
            237
        );


        $this->SetDrawColor(
            253,
            186,
            116
        );


        $this->Rect(
            $x,
            $y,
            $ancho,
            $alto,
            'DF'
        );


        $this->SetXY(
            $x + 4,
            $y + 3
        );


        $this->SetFont(
            'Arial',
            '',
            7.7
        );


        $this->SetTextColor(
            100,
            100,
            100
        );


        $this->MultiCell(
            $ancho - 8,
            3.8,
            utf8_decode(
                $titulo
            ),
            0,
            'L'
        );


        $this->SetXY(
            $x + 4,
            $y + 11
        );


        $this->SetFont(
            'Arial',
            'B',
            10.5
        );


        $this->SetTextColor(
            234,
            88,
            12
        );


        $this->MultiCell(
            $ancho - 8,
            4.8,
            utf8_decode(
                $valor
            ),
            0,
            'L'
        );


        if (
            trim(
                (string)$detalle
            )
            !==
            ''
        ) {

            $this->SetXY(
                $x + 4,
                $y + $alto - 8
            );


            $this->SetFont(
                'Arial',
                '',
                6.5
            );


            $this->SetTextColor(
                120,
                120,
                120
            );


            $this->MultiCell(
                $ancho - 8,
                3.2,
                utf8_decode(
                    $detalle
                ),
                0,
                'L'
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | CABECERA DE TABLA
    |--------------------------------------------------------------------------
    */

    function CabeceraTabla(
        array $titulos,
        array $anchos,
        array $alineaciones = []
    ) {

        $this->SetFillColor(
            255,
            247,
            237
        );


        $this->SetTextColor(
            154,
            52,
            18
        );


        $this->SetDrawColor(
            230,
            230,
            230
        );


        $this->SetFont(
            'Arial',
            'B',
            8
        );


        foreach (
            $titulos as $indice => $titulo
        ) {

            $alineacion =
                $alineaciones[$indice]
                ?? 'L';


            $this->Cell(
                $anchos[$indice],
                7,
                utf8_decode(
                    $titulo
                ),
                1,
                0,
                $alineacion,
                true
            );
        }


        $this->Ln();
    }


    /*
    |--------------------------------------------------------------------------
    | FILA DE TABLA
    |--------------------------------------------------------------------------
    */

    function FilaTabla(
        array $valores,
        array $anchos,
        array $alineaciones = [],
        $negrita = false
    ) {

        $this->SetTextColor(
            55,
            65,
            81
        );


        $this->SetDrawColor(
            235,
            235,
            235
        );


        $this->SetFont(
            'Arial',
            $negrita ? 'B' : '',
            7.8
        );


        foreach (
            $valores as $indice => $valor
        ) {

            $alineacion =
                $alineaciones[$indice]
                ?? 'L';


            $texto =
                limpiarTextoPdf(
                    $valor
                );


            if (
                strlen(
                    $texto
                )
                >
                55
            ) {

                $texto =
                    substr(
                        $texto,
                        0,
                        52
                    )
                    .
                    '...';
            }


            $this->Cell(
                $anchos[$indice],
                6.5,
                utf8_decode(
                    $texto
                ),
                1,
                0,
                $alineacion
            );
        }


        $this->Ln();
    }


    /*
    |--------------------------------------------------------------------------
    | CONTROL DE SALTO
    |--------------------------------------------------------------------------
    */

    function AsegurarEspacio(
        $altoNecesario
    ) {

        if (
            $this->GetY()
            +
            $altoNecesario
            >
            272
        ) {

            $this->AddPage();
        }
    }
}


/*
|--------------------------------------------------------------------------
| CREAR PDF
|--------------------------------------------------------------------------
*/

$pdf =
    new PDFEstadisticas();


$pdf->usuario =
    $nombreCompleto;


$pdf->rol =
    $rol;


$pdf->periodoTexto =
    textoPeriodoPdf(
        $periodoDesde,
        $periodoHasta
    );


$pdf->tipoTexto =
    textoTipoLiquidacionPdf(
        $tipoLiquidacion
    );


$pdf->AliasNbPages();


$pdf->SetMargins(
    12,
    40,
    12
);


$pdf->SetAutoPageBreak(
    true,
    24
);


/*
|--------------------------------------------------------------------------
| PÁGINA 1 - RESUMEN
|--------------------------------------------------------------------------
*/

$pdf->AddPage();


$pdf->TituloSeccion(
    'Resumen general',
    'Los importes económicos se calculan únicamente sobre liquidaciones cerradas. '
    .
    'El indicador de empleados activos corresponde al padrón actual.'
);


/*
|--------------------------------------------------------------------------
| 8 TARJETAS - 2 COLUMNAS x 4 FILAS
|--------------------------------------------------------------------------
*/

$anchoTarjeta =
    90;


$altoTarjeta =
    25;


$separacionHorizontal =
    6;


$separacionVertical =
    5;


$x1 =
    12;


$x2 =
    $x1
    +
    $anchoTarjeta
    +
    $separacionHorizontal;


$yInicial =
    $pdf->GetY();


$tarjetas = [

    [
        'Empleados activos actuales',
        (string)(
            (int)(
                $resumen['empleados_activos']
                ?? 0
            )
        ),
        'Padrón actual.'
    ],

    [
        'Liquidaciones cerradas',
        (string)(
            (int)(
                $resumen['liquidaciones_cerradas']
                ?? 0
            )
        ),
        'Según los filtros seleccionados.'
    ],

    [
        'Total remunerativo',
        formatoPesosPdf(
            $resumen['total_remunerativo']
            ?? 0
        ),
        ''
    ],

    [
        'Total no remunerativo',
        formatoPesosPdf(
            $resumen['total_no_remunerativo']
            ?? 0
        ),
        ''
    ],

    [
        'Total asignaciones',
        formatoPesosPdf(
            $resumen['total_asignaciones']
            ?? 0
        ),
        ''
    ],

    [
        'Total descuentos',
        formatoPesosPdf(
            $resumen['total_descuentos']
            ?? 0
        ),
        ''
    ],

    [
        'Total neto liquidado',
        formatoPesosPdf(
            $resumen['total_neto']
            ?? 0
        ),
        ''
    ],

    [
        'Neto promedio por empleado liquidado',
        formatoPesosPdf(
            $resumen['neto_promedio']
            ?? 0
        ),
        'Promedio de registros de liquidacion_empleado.'
    ]
];


foreach (
    $tarjetas as $indice => $tarjeta
) {

    $columna =
        $indice % 2;


    $fila =
        intdiv(
            $indice,
            2
        );


    $x =
        $columna === 0
            ? $x1
            : $x2;


    $y =
        $yInicial
        +
        (
            $fila
            *
            (
                $altoTarjeta
                +
                $separacionVertical
            )
        );


    $pdf->Tarjeta(
        $x,
        $y,
        $anchoTarjeta,
        $altoTarjeta,
        $tarjeta[0],
        $tarjeta[1],
        $tarjeta[2]
    );
}


$finTarjetas =
    $yInicial
    +
    (
        4
        *
        $altoTarjeta
    )
    +
    (
        3
        *
        $separacionVertical
    );


$pdf->SetY(
    $finTarjetas
    +
    8
);


$pdf->SetFont(
    'Arial',
    '',
    8.5
);


$pdf->SetTextColor(
    90,
    90,
    90
);


$pdf->MultiCell(
    0,
    5,
    utf8_decode(
        'Alcance: período '
        .
        textoPeriodoPdf(
            $periodoDesde,
            $periodoHasta
        )
        .
        ' | Tipo de liquidación: '
        .
        textoTipoLiquidacionPdf(
            $tipoLiquidacion
        )
        .
        ' | Estado: CERRADA.'
    )
);


/*
|--------------------------------------------------------------------------
| UTILIDAD PARA GUARDAR IMAGEN TEMPORAL
|--------------------------------------------------------------------------
*/

function crearArchivoTemporalGrafico(
    $imagenBase64
) {

    if (
        !is_string(
            $imagenBase64
        )
        ||
        trim(
            $imagenBase64
        )
        ===
        ''
    ) {

        return null;
    }


    $imagenBase64 =
        preg_replace(
            '#^data:image/png;base64,#',
            '',
            trim(
                $imagenBase64
            )
        );


    $imagenBase64 =
        str_replace(
            ' ',
            '+',
            $imagenBase64
        );


    /*
    |--------------------------------------------------------------------------
    | LÍMITE DEFENSIVO
    |--------------------------------------------------------------------------
    |
    | Aproximadamente 12 MB de texto Base64 por gráfico.
    |
    */

    if (
        strlen(
            $imagenBase64
        )
        >
        12 * 1024 * 1024
    ) {

        return null;
    }


    $binario =
        base64_decode(
            $imagenBase64,
            true
        );


    if ($binario === false) {

        return null;
    }


    $archivoTemporal =
        tempnam(
            sys_get_temp_dir(),
            'grafico_'
        );


    if ($archivoTemporal === false) {

        return null;
    }


    $archivoPng =
        $archivoTemporal
        .
        '.png';


    @unlink(
        $archivoTemporal
    );


    $guardado =
        file_put_contents(
            $archivoPng,
            $binario
        );


    if (
        $guardado === false
        ||
        !file_exists(
            $archivoPng
        )
    ) {

        @unlink(
            $archivoPng
        );

        return null;
    }


    return $archivoPng;
}


/*
|--------------------------------------------------------------------------
| SECCIÓN 1 - EMPLEADOS ACTIVOS ACTUALES POR CATEGORÍA
|--------------------------------------------------------------------------
*/

$pdf->AddPage();


$pdf->TituloSeccion(
    'Empleados activos actuales por categoría',
    'Distribución del padrón actualmente activo según su categoría laboral. '
    .
    'Este gráfico no depende de los filtros de período ni tipo de liquidación.'
);


$archivoGrafico =
    crearArchivoTemporalGrafico(
        $imagenesGraficos['categoria']
        ?? null
    );


if ($archivoGrafico !== null) {

    $pdf->Image(
        $archivoGrafico,
        18,
        $pdf->GetY() + 2,
        174,
        72
    );


    @unlink(
        $archivoGrafico
    );


    $pdf->Ln(78);

} else {

    $pdf->SetFont(
        'Arial',
        'I',
        9
    );

    $pdf->SetTextColor(
        110,
        110,
        110
    );

    $pdf->Cell(
        0,
        8,
        utf8_decode(
            'Gráfico no disponible.'
        ),
        0,
        1,
        'L'
    );
}


if (
    !empty(
        $empleadosPorCategoria
    )
) {

    $pdf->AsegurarEspacio(
        20
    );


    $pdf->SetFont(
        'Arial',
        'B',
        9
    );

    $pdf->SetTextColor(
        55,
        65,
        81
    );

    $pdf->Cell(
        0,
        7,
        utf8_decode(
            'Valores del gráfico'
        ),
        0,
        1,
        'L'
    );


    $anchos = [
        145,
        41
    ];


    $alineaciones = [
        'L',
        'R'
    ];


    $pdf->CabeceraTabla(
        [
            'Categoría',
            'Empleados'
        ],
        $anchos,
        $alineaciones
    );


    foreach (
        $empleadosPorCategoria as $fila
    ) {

        $pdf->AsegurarEspacio(
            7
        );


        $codigo =
            trim(
                (string)(
                    $fila['codigo']
                    ?? ''
                )
            );


        $categoria =
            trim(
                (string)(
                    $fila['categoria']
                    ?? 'Sin categoría'
                )
            );


        $textoCategoria =
            (
                $codigo !== ''
                    ? $codigo . ' - '
                    : ''
            )
            .
            $categoria;


        $pdf->FilaTabla(
            [
                $textoCategoria,
                (string)(
                    (int)(
                        $fila['total']
                        ?? 0
                    )
                )
            ],
            $anchos,
            $alineaciones
        );
    }

} else {

    $pdf->SetFont(
        'Arial',
        'I',
        9
    );

    $pdf->Cell(
        0,
        8,
        utf8_decode(
            'No hay empleados activos para representar.'
        ),
        0,
        1,
        'L'
    );
}


/*
|--------------------------------------------------------------------------
| SECCIÓN 2 - TOTAL NETO POR PERÍODO
|--------------------------------------------------------------------------
*/

$pdf->AddPage();


$pdf->TituloSeccion(
    'Total neto liquidado por período',
    'Evolución del total neto de liquidaciones cerradas según los filtros '
    .
    'de período y tipo seleccionados.'
);


$archivoGrafico =
    crearArchivoTemporalGrafico(
        $imagenesGraficos['periodo']
        ?? null
    );


if ($archivoGrafico !== null) {

    $pdf->Image(
        $archivoGrafico,
        18,
        $pdf->GetY() + 2,
        174,
        72
    );


    @unlink(
        $archivoGrafico
    );


    $pdf->Ln(78);

} else {

    $pdf->SetFont(
        'Arial',
        'I',
        9
    );

    $pdf->SetTextColor(
        110,
        110,
        110
    );

    $pdf->Cell(
        0,
        8,
        utf8_decode(
            'No hay datos económicos para este gráfico con los filtros seleccionados.'
        ),
        0,
        1,
        'L'
    );
}


if (
    !empty(
        $netoPorPeriodo
    )
) {

    $pdf->SetFont(
        'Arial',
        'B',
        9
    );

    $pdf->SetTextColor(
        55,
        65,
        81
    );

    $pdf->Cell(
        0,
        7,
        utf8_decode(
            'Valores del gráfico'
        ),
        0,
        1,
        'L'
    );


    $anchos = [
        70,
        116
    ];


    $alineaciones = [
        'L',
        'R'
    ];


    $pdf->CabeceraTabla(
        [
            'Período',
            'Total neto'
        ],
        $anchos,
        $alineaciones
    );


    foreach (
        $netoPorPeriodo as $fila
    ) {

        $pdf->AsegurarEspacio(
            7
        );


        $pdf->FilaTabla(
            [
                $fila['periodo']
                ?? '',

                formatoPesosPdf(
                    $fila['total_neto']
                    ?? 0
                )
            ],
            $anchos,
            $alineaciones
        );
    }
}


/*
|--------------------------------------------------------------------------
| SECCIÓN 3 - COMPOSICIÓN POR TIPO DE CONCEPTO
|--------------------------------------------------------------------------
*/

$pdf->AddPage();


$pdf->TituloSeccion(
    'Composición de la liquidación por tipo de concepto',
    'Distribución de los importes registrados en el detalle de las '
    .
    'liquidaciones cerradas. Puede incluir remunerativos, no remunerativos, '
    .
    'asignaciones, descuentos y aportes patronales.'
);


$archivoGrafico =
    crearArchivoTemporalGrafico(
        $imagenesGraficos['conceptos']
        ?? null
    );


if ($archivoGrafico !== null) {

    $pdf->Image(
        $archivoGrafico,
        33,
        $pdf->GetY() + 2,
        144,
        74
    );


    @unlink(
        $archivoGrafico
    );


    $pdf->Ln(80);

} else {

    $pdf->SetFont(
        'Arial',
        'I',
        9
    );

    $pdf->SetTextColor(
        110,
        110,
        110
    );

    $pdf->Cell(
        0,
        8,
        utf8_decode(
            'No hay datos de conceptos para los filtros seleccionados.'
        ),
        0,
        1,
        'L'
    );
}


if (
    !empty(
        $conceptosPorCategoria
    )
) {

    $totalComposicion =
        0.0;


    foreach (
        $conceptosPorCategoria as $fila
    ) {

        $totalComposicion +=
            (float)(
                $fila['total']
                ?? 0
            );
    }


    $pdf->SetFont(
        'Arial',
        'B',
        9
    );

    $pdf->SetTextColor(
        55,
        65,
        81
    );

    $pdf->Cell(
        0,
        7,
        utf8_decode(
            'Valores del gráfico'
        ),
        0,
        1,
        'L'
    );


    $anchos = [
        82,
        66,
        38
    ];


    $alineaciones = [
        'L',
        'R',
        'R'
    ];


    $pdf->CabeceraTabla(
        [
            'Tipo de concepto',
            'Importe',
            'Participación'
        ],
        $anchos,
        $alineaciones
    );


    foreach (
        $conceptosPorCategoria as $fila
    ) {

        $pdf->AsegurarEspacio(
            7
        );


        $importe =
            (float)(
                $fila['total']
                ?? 0
            );


        $participacion =
            $totalComposicion > 0
                ? (
                    $importe
                    /
                    $totalComposicion
                ) * 100
                : 0;


        $pdf->FilaTabla(
            [
                $fila['tipo']
                ?? '',

                formatoPesosPdf(
                    $importe
                ),

                number_format(
                    $participacion,
                    2,
                    ',',
                    '.'
                )
                .
                '%'
            ],
            $anchos,
            $alineaciones
        );
    }


    $pdf->FilaTabla(
        [
            'Total representado',

            formatoPesosPdf(
                $totalComposicion
            ),

            $totalComposicion > 0
                ? '100,00%'
                : '0,00%'
        ],
        $anchos,
        $alineaciones,
        true
    );
}


/*
|--------------------------------------------------------------------------
| SECCIÓN 4 - PRINCIPALES DESCUENTOS
|--------------------------------------------------------------------------
*/

$pdf->AddPage();


$pdf->TituloSeccion(
    'Principales descuentos',
    'Ranking de hasta 10 descuentos con mayor importe acumulado en las '
    .
    'liquidaciones cerradas seleccionadas.'
);


$archivoGrafico =
    crearArchivoTemporalGrafico(
        $imagenesGraficos['descuentos']
        ?? null
    );


if ($archivoGrafico !== null) {

    $pdf->Image(
        $archivoGrafico,
        18,
        $pdf->GetY() + 2,
        174,
        76
    );


    @unlink(
        $archivoGrafico
    );


    $pdf->Ln(82);

} else {

    $pdf->SetFont(
        'Arial',
        'I',
        9
    );

    $pdf->SetTextColor(
        110,
        110,
        110
    );

    $pdf->Cell(
        0,
        8,
        utf8_decode(
            'No hay descuentos para los filtros seleccionados.'
        ),
        0,
        1,
        'L'
    );
}


if (
    !empty(
        $principalesDescuentos
    )
) {

    $pdf->SetFont(
        'Arial',
        'B',
        9
    );

    $pdf->SetTextColor(
        55,
        65,
        81
    );

    $pdf->Cell(
        0,
        7,
        utf8_decode(
            'Valores del gráfico'
        ),
        0,
        1,
        'L'
    );


    $anchos = [
        122,
        64
    ];


    $alineaciones = [
        'L',
        'R'
    ];


    $pdf->CabeceraTabla(
        [
            'Concepto',
            'Importe acumulado'
        ],
        $anchos,
        $alineaciones
    );


    foreach (
        $principalesDescuentos as $fila
    ) {

        $pdf->AsegurarEspacio(
            7
        );


        $pdf->FilaTabla(
            [
                $fila['concepto']
                ?? '',

                formatoPesosPdf(
                    $fila['total']
                    ?? 0
                )
            ],
            $anchos,
            $alineaciones
        );
    }
}


/*
|--------------------------------------------------------------------------
| SALIDA
|--------------------------------------------------------------------------
*/

$nombreArchivo =
    'estadisticas_sigenmuni';


if ($periodoDesde !== '') {

    $nombreArchivo .=
        '_desde_'
        .
        $periodoDesde;
}


if ($periodoHasta !== '') {

    $nombreArchivo .=
        '_hasta_'
        .
        $periodoHasta;
}


if ($tipoLiquidacion !== '') {

    $nombreArchivo .=
        '_'
        .
        strtolower(
            $tipoLiquidacion
        );
}


$nombreArchivo .=
    '.pdf';


$pdf->Output(
    'I',
    $nombreArchivo
);

exit;
