<?php

/*
|--------------------------------------------------------------------------
| GENERAR EXCEL - ESTADÍSTICAS - ROUTER
|--------------------------------------------------------------------------
|
| Los filtros y los datos estadísticos son preparados por
| ReporteControlador::estadisticasExcel().
|
| Desde el navegador se reciben únicamente las imágenes de Chart.js.
|
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| ESCRITOR ZIP AUTÓNOMO
|--------------------------------------------------------------------------
|
| XLSX es un contenedor ZIP. Para evitar dependencias adicionales,
| se genera un ZIP válido directamente en PHP usando entradas sin compresión.
|
|--------------------------------------------------------------------------
*/

class ZipExcelSimple
{
    private $entradas = [];


    public function agregar(
        $nombre,
        $contenido
    ) {
        $this->entradas[] = [
            'nombre' =>
                str_replace(
                    '\\',
                    '/',
                    (string)$nombre
                ),

            'contenido' =>
                (string)$contenido
        ];
    }


    private function fechaHoraDos()
    {
        $ahora =
            getdate();


        $anio =
            max(
                1980,
                min(
                    2107,
                    (int)$ahora['year']
                )
            );


        $horaDos =
            (
                (
                    (int)$ahora['hours']
                    &
                    0x1F
                )
                <<
                11
            )
            |
            (
                (
                    (int)$ahora['minutes']
                    &
                    0x3F
                )
                <<
                5
            )
            |
            (
                intdiv(
                    (int)$ahora['seconds'],
                    2
                )
                &
                0x1F
            );


        $fechaDos =
            (
                (
                    $anio
                    -
                    1980
                )
                <<
                9
            )
            |
            (
                (
                    (int)$ahora['mon']
                    &
                    0x0F
                )
                <<
                5
            )
            |
            (
                (int)$ahora['mday']
                &
                0x1F
            );


        return [
            $horaDos,
            $fechaDos
        ];
    }


    public function guardar($ruta)
    {
        list(
            $horaDos,
            $fechaDos
        ) =
            $this->fechaHoraDos();


        $salida =
            '';


        $central =
            '';


        $offset =
            0;


        $cantidad =
            0;


        foreach (
            $this->entradas as $entrada
        ) {

            $nombre =
                $entrada['nombre'];


            $contenido =
                $entrada['contenido'];


            $largoNombre =
                strlen(
                    $nombre
                );


            $largoContenido =
                strlen(
                    $contenido
                );


            $crc =
                (int)sprintf(
                    '%u',
                    crc32(
                        $contenido
                    )
                );


            $cabeceraLocal =
                pack(
                    'VvvvvvVVVvv',
                    0x04034b50,
                    20,
                    0,
                    0,
                    $horaDos,
                    $fechaDos,
                    $crc,
                    $largoContenido,
                    $largoContenido,
                    $largoNombre,
                    0
                )
                .
                $nombre;


            $salida .=
                $cabeceraLocal
                .
                $contenido;


            $central .=
                pack(
                    'VvvvvvvVVVvvvvvVV',
                    0x02014b50,
                    20,
                    20,
                    0,
                    0,
                    $horaDos,
                    $fechaDos,
                    $crc,
                    $largoContenido,
                    $largoContenido,
                    $largoNombre,
                    0,
                    0,
                    0,
                    0,
                    0,
                    $offset
                )
                .
                $nombre;


            $offset +=
                strlen(
                    $cabeceraLocal
                )
                +
                $largoContenido;


            $cantidad++;
        }


        $inicioCentral =
            strlen(
                $salida
            );


        $tamanoCentral =
            strlen(
                $central
            );


        $salida .=
            $central;


        $salida .=
            pack(
                'VvvvvVVv',
                0x06054b50,
                0,
                0,
                $cantidad,
                $cantidad,
                $tamanoCentral,
                $inicioCentral,
                0
            );


        return (
            file_put_contents(
                $ruta,
                $salida
            )
            !==
            false
        );
    }
}




/*
|--------------------------------------------------------------------------
| DATOS DE CHART.JS
|--------------------------------------------------------------------------
*/

$entrada =
    json_decode(
        file_get_contents(
            "php://input"
        ),
        true
    );


if (!is_array($entrada)) {

    $entrada = [];
}


$graficosRecibidos =
    is_array(
        $entrada['graficos']
        ?? null
    )
        ? $entrada['graficos']
        : [];


/*
|--------------------------------------------------------------------------
| DECODIFICAR GRÁFICOS
|--------------------------------------------------------------------------
*/

function decodificarGraficoExcel($valor)
{
    if (
        !is_string(
            $valor
        )
        ||
        trim(
            $valor
        )
        ===
        ''
    ) {

        return null;
    }


    $valor =
        trim(
            $valor
        );


    $valor =
        preg_replace(
            '#^data:image/png;base64,#',
            '',
            $valor
        );


    $valor =
        str_replace(
            ' ',
            '+',
            $valor
        );


    /*
    |--------------------------------------------------------------------------
    | LÍMITE DEFENSIVO
    |--------------------------------------------------------------------------
    */

    if (
        strlen(
            $valor
        )
        >
        12 * 1024 * 1024
    ) {

        return null;
    }


    $binario =
        base64_decode(
            $valor,
            true
        );


    if (
        $binario === false
        ||
        strlen(
            $binario
        )
        < 8
    ) {

        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | FIRMA PNG
    |--------------------------------------------------------------------------
    */

    if (
        substr(
            $binario,
            0,
            8
        )
        !==
        "\x89PNG\r\n\x1a\n"
    ) {

        return null;
    }


    return $binario;
}


$imagenes = [

    'categoria' =>
        decodificarGraficoExcel(
            $graficosRecibidos['categoria']
            ?? null
        ),

    'periodo' =>
        decodificarGraficoExcel(
            $graficosRecibidos['periodo']
            ?? null
        ),

    'conceptos' =>
        decodificarGraficoExcel(
            $graficosRecibidos['conceptos']
            ?? null
        ),

    'descuentos' =>
        decodificarGraficoExcel(
            $graficosRecibidos['descuentos']
            ?? null
        )
];




/*
|--------------------------------------------------------------------------
| TEXTOS
|--------------------------------------------------------------------------
*/

function textoPeriodoExcel(
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


function textoTipoLiquidacionExcel($tipo)
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


/*
|--------------------------------------------------------------------------
| HELPERS XML
|--------------------------------------------------------------------------
*/

function xmlExcel($valor)
{
    return htmlspecialchars(
        (string)$valor,
        ENT_XML1
        |
        ENT_QUOTES,
        'UTF-8'
    );
}


function columnaExcel($numero)
{
    $numero =
        (int)$numero;


    $resultado =
        '';


    while ($numero > 0) {

        $numero--;


        $resultado =
            chr(
                65
                +
                (
                    $numero
                    %
                    26
                )
            )
            .
            $resultado;


        $numero =
            intdiv(
                $numero,
                26
            );
    }


    return $resultado;
}


function numeroXmlExcel($valor)
{
    if (!is_numeric($valor)) {

        return '0';
    }


    $numero =
        number_format(
            (float)$valor,
            8,
            '.',
            ''
        );


    $numero =
        rtrim(
            rtrim(
                $numero,
                '0'
            ),
            '.'
        );


    if (
        $numero === ''
        ||
        $numero === '-0'
    ) {

        $numero = '0';
    }


    return $numero;
}


function celdaTextoExcel(
    $referencia,
    $valor,
    $estilo = 0
) {

    return
        '<c r="'
        .
        xmlExcel(
            $referencia
        )
        .
        '" t="inlineStr" s="'
        .
        (int)$estilo
        .
        '">'
        .
        '<is><t>'
        .
        xmlExcel(
            $valor
        )
        .
        '</t></is>'
        .
        '</c>';
}


function celdaNumeroExcel(
    $referencia,
    $valor,
    $estilo = 0
) {

    return
        '<c r="'
        .
        xmlExcel(
            $referencia
        )
        .
        '" s="'
        .
        (int)$estilo
        .
        '">'
        .
        '<v>'
        .
        numeroXmlExcel(
            $valor
        )
        .
        '</v>'
        .
        '</c>';
}


function filaExcel(
    $numeroFila,
    array $celdas
) {

    $xml =
        '<row r="'
        .
        (int)$numeroFila
        .
        '">';


    $indiceColumna =
        1;


    foreach (
        $celdas as $celda
    ) {

        if (
            $celda === null
        ) {

            $indiceColumna++;

            continue;
        }


        $referencia =
            columnaExcel(
                $indiceColumna
            )
            .
            (int)$numeroFila;


        $tipo =
            $celda['tipo']
            ?? 's';


        $valor =
            $celda['valor']
            ?? '';


        $estilo =
            (int)(
                $celda['estilo']
                ?? 0
            );


        if ($tipo === 'n') {

            $xml .=
                celdaNumeroExcel(
                    $referencia,
                    $valor,
                    $estilo
                );

        } else {

            $xml .=
                celdaTextoExcel(
                    $referencia,
                    $valor,
                    $estilo
                );
        }


        $indiceColumna++;
    }


    $xml .=
        '</row>';


    return $xml;
}


function crearHojaExcelXml(
    array $filas,
    array $anchos,
    array $combinaciones = [],
    $tieneDibujo = false,
    $congelarFila = 0,
    $mostrarCuadricula = true,
    $panelGrafico = false
) {

    /*
    |--------------------------------------------------------------------------
    | PANEL VISUAL DEL GRÁFICO
    |--------------------------------------------------------------------------
    |
    | Los PNG exportados desde Chart.js conservan transparencia. Para evitar
    | que la cuadrícula de Excel se mezcle visualmente con el gráfico, se crea
    | un panel gris muy claro exactamente debajo de la imagen (D2:M21).
    |
    */

    if ($panelGrafico) {

        while (
            count($filas) < 2
        ) {

            $filas[] = [];
        }


        while (
            count($filas[1]) < 4
        ) {

            $filas[1][] = null;
        }


        $filas[1][3] = [
            'valor' =>
                '',

            'estilo' =>
                8
        ];


        if (
            !in_array(
                'D2:M21',
                $combinaciones,
                true
            )
        ) {

            $combinaciones[] =
                'D2:M21';
        }
    }


    $maxColumnas =
        1;


    foreach (
        $filas as $fila
    ) {

        $maxColumnas =
            max(
                $maxColumnas,
                count(
                    $fila
                )
            );
    }


    $maxFilas =
        max(
            1,
            count(
                $filas
            )
        );


    if ($panelGrafico) {

        $maxColumnas =
            max(
                $maxColumnas,
                13
            );


        $maxFilas =
            max(
                $maxFilas,
                21
            );
    }


    $dimension =
        'A1:'
        .
        columnaExcel(
            $maxColumnas
        )
        .
        $maxFilas;


    $xml =
        '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        .
        '<worksheet '
        .
        'xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" '
        .
        'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
        .
        '<dimension ref="'
        .
        xmlExcel(
            $dimension
        )
        .
        '"/>';


    if ($congelarFila > 0) {

        $xml .=
            '<sheetViews>'
            .
            '<sheetView workbookViewId="0" showGridLines="'
            .
            (
                $mostrarCuadricula
                    ? '1'
                    : '0'
            )
            .
            '">'
            .
            '<pane ySplit="'
            .
            (int)$congelarFila
            .
            '" topLeftCell="A'
            .
            (
                (int)$congelarFila
                +
                1
            )
            .
            '" activePane="bottomLeft" state="frozen"/>'
            .
            '</sheetView>'
            .
            '</sheetViews>';

    } else {

        $xml .=
            '<sheetViews>'
            .
            '<sheetView workbookViewId="0" showGridLines="'
            .
            (
                $mostrarCuadricula
                    ? '1'
                    : '0'
            )
            .
            '"/>'
            .
            '</sheetViews>';
    }


    if (!empty($anchos)) {

        $xml .=
            '<cols>';


        foreach (
            $anchos as $indice => $ancho
        ) {

            $columna =
                (int)$indice
                +
                1;


            $xml .=
                '<col min="'
                .
                $columna
                .
                '" max="'
                .
                $columna
                .
                '" width="'
                .
                (float)$ancho
                .
                '" customWidth="1"/>';
        }


        $xml .=
            '</cols>';
    }


    $xml .=
        '<sheetData>';


    foreach (
        $filas as $indice => $fila
    ) {

        $xml .=
            filaExcel(
                $indice + 1,
                $fila
            );
    }


    $xml .=
        '</sheetData>';


    if (!empty($combinaciones)) {

        $xml .=
            '<mergeCells count="'
            .
            count(
                $combinaciones
            )
            .
            '">';


        foreach (
            $combinaciones as $combinacion
        ) {

            $xml .=
                '<mergeCell ref="'
                .
                xmlExcel(
                    $combinacion
                )
                .
                '"/>';
        }


        $xml .=
            '</mergeCells>';
    }


    /*
    |--------------------------------------------------------------------------
    | ORDEN XML DE WORKSHEET
    |--------------------------------------------------------------------------
    |
    | pageMargins debe aparecer antes de drawing según el orden del esquema
    | OOXML de una hoja de cálculo. Si drawing se escribe antes, Excel puede
    | reparar la hoja y terminar descartando su contenido.
    |
    |--------------------------------------------------------------------------
    */

    $xml .=
        '<pageMargins '
        .
        'left="0.5" right="0.5" top="0.6" bottom="0.6" '
        .
        'header="0.3" footer="0.3"/>';


    if ($tieneDibujo) {

        $xml .=
            '<drawing r:id="rId1"/>';
    }


    $xml .=
        '</worksheet>';


    return $xml;
}


function crearDibujoExcelXml(
    $nombre,
    $colDesde = 3,
    $filaDesde = 1,
    $colHasta = 12,
    $filaHasta = 20
) {

    return
        '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        .
        '<xdr:wsDr '
        .
        'xmlns:xdr="http://schemas.openxmlformats.org/drawingml/2006/spreadsheetDrawing" '
        .
        'xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" '
        .
        'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
        .
        '<xdr:twoCellAnchor editAs="oneCell">'
        .
        '<xdr:from>'
        .
        '<xdr:col>'
        .
        (int)$colDesde
        .
        '</xdr:col>'
        .
        '<xdr:colOff>0</xdr:colOff>'
        .
        '<xdr:row>'
        .
        (int)$filaDesde
        .
        '</xdr:row>'
        .
        '<xdr:rowOff>0</xdr:rowOff>'
        .
        '</xdr:from>'
        .
        '<xdr:to>'
        .
        '<xdr:col>'
        .
        (int)$colHasta
        .
        '</xdr:col>'
        .
        '<xdr:colOff>0</xdr:colOff>'
        .
        '<xdr:row>'
        .
        (int)$filaHasta
        .
        '</xdr:row>'
        .
        '<xdr:rowOff>0</xdr:rowOff>'
        .
        '</xdr:to>'
        .
        '<xdr:pic>'
        .
        '<xdr:nvPicPr>'
        .
        '<xdr:cNvPr id="1" name="'
        .
        xmlExcel(
            $nombre
        )
        .
        '"/>'
        .
        '<xdr:cNvPicPr/>'
        .
        '</xdr:nvPicPr>'
        .
        '<xdr:blipFill>'
        .
        '<a:blip r:embed="rId1"/>'
        .
        '<a:stretch><a:fillRect/></a:stretch>'
        .
        '</xdr:blipFill>'
        .
        '<xdr:spPr>'
        .
        '<a:prstGeom prst="rect"><a:avLst/></a:prstGeom>'
        .
        '</xdr:spPr>'
        .
        '</xdr:pic>'
        .
        '<xdr:clientData/>'
        .
        '</xdr:twoCellAnchor>'
        .
        '</xdr:wsDr>';
}


function relacionesHojaDibujoXml($numeroDibujo)
{
    return
        '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        .
        '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
        .
        '<Relationship '
        .
        'Id="rId1" '
        .
        'Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/drawing" '
        .
        'Target="../drawings/drawing'
        .
        (int)$numeroDibujo
        .
        '.xml"/>'
        .
        '</Relationships>';
}


function relacionesDibujoImagenXml($numeroImagen)
{
    return
        '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        .
        '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
        .
        '<Relationship '
        .
        'Id="rId1" '
        .
        'Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image" '
        .
        'Target="../media/image'
        .
        (int)$numeroImagen
        .
        '.png"/>'
        .
        '</Relationships>';
}


/*
|--------------------------------------------------------------------------
| ESTILOS XLSX
|--------------------------------------------------------------------------
|
| Índices:
|
| 0 = Normal
| 1 = Título
| 2 = Encabezado de tabla
| 3 = Entero
| 4 = Moneda
| 5 = Porcentaje
| 6 = Total / negrita
| 7 = Subtítulo / filtro
| 8 = Panel de gráfico (gris claro)
|
|--------------------------------------------------------------------------
*/

$stylesXml =
    '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
    .
    '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
    .
    '<numFmts count="2">'
    .
    '<numFmt numFmtId="164" formatCode="&quot;$&quot; #,##0.00"/>'
    .
    '<numFmt numFmtId="165" formatCode="0.00%"/>'
    .
    '</numFmts>'
    .
    '<fonts count="4">'
    .
    '<font><sz val="11"/><name val="Calibri"/><family val="2"/></font>'
    .
    '<font><b/><color rgb="FFFFFFFF"/><sz val="11"/><name val="Calibri"/></font>'
    .
    '<font><b/><color rgb="FFF97316"/><sz val="16"/><name val="Calibri"/></font>'
    .
    '<font><b/><color rgb="FF374151"/><sz val="11"/><name val="Calibri"/></font>'
    .
    '</fonts>'
    .
    '<fills count="5">'
    .
    '<fill><patternFill patternType="none"/></fill>'
    .
    '<fill><patternFill patternType="gray125"/></fill>'
    .
    '<fill><patternFill patternType="solid"><fgColor rgb="FFF97316"/><bgColor indexed="64"/></patternFill></fill>'
    .
    '<fill><patternFill patternType="solid"><fgColor rgb="FFFFF7ED"/><bgColor indexed="64"/></patternFill></fill>'
    .
    '<fill><patternFill patternType="solid"><fgColor rgb="FFF3F4F6"/><bgColor indexed="64"/></patternFill></fill>'
    .
    '</fills>'
    .
    '<borders count="2">'
    .
    '<border><left/><right/><top/><bottom/><diagonal/></border>'
    .
    '<border>'
    .
    '<left style="thin"><color rgb="FFE5E7EB"/></left>'
    .
    '<right style="thin"><color rgb="FFE5E7EB"/></right>'
    .
    '<top style="thin"><color rgb="FFE5E7EB"/></top>'
    .
    '<bottom style="thin"><color rgb="FFE5E7EB"/></bottom>'
    .
    '<diagonal/>'
    .
    '</border>'
    .
    '</borders>'
    .
    '<cellStyleXfs count="1">'
    .
    '<xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>'
    .
    '</cellStyleXfs>'
    .
    '<cellXfs count="9">'
    .
    '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
    .
    '<xf numFmtId="0" fontId="2" fillId="0" borderId="0" xfId="0"><alignment vertical="center"/></xf>'
    .
    '<xf numFmtId="0" fontId="1" fillId="2" borderId="1" xfId="0"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>'
    .
    '<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0"><alignment horizontal="right"/></xf>'
    .
    '<xf numFmtId="164" fontId="0" fillId="0" borderId="1" xfId="0" applyNumberFormat="1"><alignment horizontal="right"/></xf>'
    .
    '<xf numFmtId="165" fontId="0" fillId="0" borderId="1" xfId="0" applyNumberFormat="1"><alignment horizontal="right"/></xf>'
    .
    '<xf numFmtId="0" fontId="3" fillId="3" borderId="1" xfId="0"/>'
    .
    '<xf numFmtId="0" fontId="3" fillId="0" borderId="0" xfId="0"/>'
    .
    '<xf numFmtId="0" fontId="0" fillId="4" borderId="1" xfId="0"/>'
    .
    '</cellXfs>'
    .
    '<cellStyles count="1">'
    .
    '<cellStyle name="Normal" xfId="0" builtinId="0"/>'
    .
    '</cellStyles>'
    .
    '</styleSheet>';


/*
|--------------------------------------------------------------------------
| FILTRO DESCRIPTIVO
|--------------------------------------------------------------------------
*/

$periodoTexto =
    textoPeriodoExcel(
        $periodoDesde,
        $periodoHasta
    );


$tipoTexto =
    textoTipoLiquidacionExcel(
        $tipoLiquidacion
    );


$alcanceTexto =
    'Período: '
    .
    $periodoTexto
    .
    ' | Tipo: '
    .
    $tipoTexto
    .
    ' | Estado: CERRADA';


/*
|--------------------------------------------------------------------------
| HOJA 1 - RESUMEN
|--------------------------------------------------------------------------
*/

$filasResumen = [

    [
        [
            'valor' =>
                'SIGENMUNI - Estadísticas',
            'estilo' =>
                1
        ]
    ],

    [
        [
            'valor' =>
                'Municipalidad de Fortín Lugones',
            'estilo' =>
                7
        ]
    ],

    [],

    [
        [
            'valor' =>
                'Período de análisis',
            'estilo' =>
                6
        ],
        [
            'valor' =>
                $periodoTexto
        ]
    ],

    [
        [
            'valor' =>
                'Tipo de liquidación',
            'estilo' =>
                6
        ],
        [
            'valor' =>
                $tipoTexto
        ]
    ],

    [
        [
            'valor' =>
                'Estado',
            'estilo' =>
                6
        ],
        [
            'valor' =>
                'CERRADA'
        ]
    ],

    [],

    [
        [
            'valor' =>
                'Indicador',
            'estilo' =>
                2
        ],
        [
            'valor' =>
                'Valor',
            'estilo' =>
                2
        ]
    ],

    [
        [
            'valor' =>
                'Empleados activos actuales'
        ],
        [
            'tipo' =>
                'n',
            'valor' =>
                (int)(
                    $resumen['empleados_activos']
                    ?? 0
                ),
            'estilo' =>
                3
        ]
    ],

    [
        [
            'valor' =>
                'Liquidaciones cerradas'
        ],
        [
            'tipo' =>
                'n',
            'valor' =>
                (int)(
                    $resumen['liquidaciones_cerradas']
                    ?? 0
                ),
            'estilo' =>
                3
        ]
    ],

    [
        [
            'valor' =>
                'Total remunerativo'
        ],
        [
            'tipo' =>
                'n',
            'valor' =>
                (float)(
                    $resumen['total_remunerativo']
                    ?? 0
                ),
            'estilo' =>
                4
        ]
    ],

    [
        [
            'valor' =>
                'Total no remunerativo'
        ],
        [
            'tipo' =>
                'n',
            'valor' =>
                (float)(
                    $resumen['total_no_remunerativo']
                    ?? 0
                ),
            'estilo' =>
                4
        ]
    ],

    [
        [
            'valor' =>
                'Total asignaciones'
        ],
        [
            'tipo' =>
                'n',
            'valor' =>
                (float)(
                    $resumen['total_asignaciones']
                    ?? 0
                ),
            'estilo' =>
                4
        ]
    ],

    [
        [
            'valor' =>
                'Total descuentos'
        ],
        [
            'tipo' =>
                'n',
            'valor' =>
                (float)(
                    $resumen['total_descuentos']
                    ?? 0
                ),
            'estilo' =>
                4
        ]
    ],

    [
        [
            'valor' =>
                'Total neto liquidado'
        ],
        [
            'tipo' =>
                'n',
            'valor' =>
                (float)(
                    $resumen['total_neto']
                    ?? 0
                ),
            'estilo' =>
                4
        ]
    ],

    [
        [
            'valor' =>
                'Neto promedio por empleado liquidado'
        ],
        [
            'tipo' =>
                'n',
            'valor' =>
                (float)(
                    $resumen['neto_promedio']
                    ?? 0
                ),
            'estilo' =>
                4
        ]
    ],

    [],

    [
        [
            'valor' =>
                'Nota'
        ],
        [
            'valor' =>
                'El indicador de empleados activos corresponde al padrón actual. '
                .
                'Los importes económicos corresponden a liquidaciones cerradas.'
        ]
    ]
];


$hojaResumen =
    crearHojaExcelXml(
        $filasResumen,
        [
            42,
            24
        ],
        [
            'A1:B1',
            'A2:B2'
        ],
        false,
        0
    );


/*
|--------------------------------------------------------------------------
| HOJA 2 - EMPLEADOS POR CATEGORÍA
|--------------------------------------------------------------------------
*/

$filasCategoria = [

    [
        [
            'valor' =>
                'Empleados activos actuales por categoría',
            'estilo' =>
                1
        ]
    ],

    [
        [
            'valor' =>
                'Padrón actual. No depende del período ni del tipo de liquidación.',
            'estilo' =>
                7
        ]
    ],

    [],

    [
        [
            'valor' =>
                'Categoría',
            'estilo' =>
                2
        ],
        [
            'valor' =>
                'Empleados',
            'estilo' =>
                2
        ]
    ]
];


foreach (
    $empleadosPorCategoria as $fila
) {

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


    $filasCategoria[] = [

        [
            'valor' =>
                $textoCategoria
        ],

        [
            'tipo' =>
                'n',
            'valor' =>
                (int)(
                    $fila['total']
                    ?? 0
                ),
            'estilo' =>
                3
        ]
    ];
}


$hojaCategoria =
    crearHojaExcelXml(
        $filasCategoria,
        [
            32,
            14,
            3,
            12,
            12,
            12,
            12,
            12,
            12,
            12,
            12,
            12,
            12
        ],
        [
            'A1:B1',
            'A2:B2'
        ],
        $imagenes['categoria'] !== null,
        4,
        false,
        false
    );


/*
|--------------------------------------------------------------------------
| HOJA 3 - NETO POR PERÍODO
|--------------------------------------------------------------------------
*/

$filasNeto = [

    [
        [
            'valor' =>
                'Total neto liquidado por período',
            'estilo' =>
                1
        ]
    ],

    [
        [
            'valor' =>
                $alcanceTexto,
            'estilo' =>
                7
        ]
    ],

    [],

    [
        [
            'valor' =>
                'Período',
            'estilo' =>
                2
        ],
        [
            'valor' =>
                'Total neto',
            'estilo' =>
                2
        ]
    ]
];


foreach (
    $netoPorPeriodo as $fila
) {

    $filasNeto[] = [

        [
            'valor' =>
                $fila['periodo']
                ?? ''
        ],

        [
            'tipo' =>
                'n',
            'valor' =>
                (float)(
                    $fila['total_neto']
                    ?? 0
                ),
            'estilo' =>
                4
        ]
    ];
}


$hojaNeto =
    crearHojaExcelXml(
        $filasNeto,
        [
            18,
            22,
            3,
            12,
            12,
            12,
            12,
            12,
            12,
            12,
            12,
            12,
            12
        ],
        [
            'A1:B1',
            'A2:B2'
        ],
        $imagenes['periodo'] !== null,
        4,
        false,
        false
    );


/*
|--------------------------------------------------------------------------
| HOJA 4 - COMPOSICIÓN
|--------------------------------------------------------------------------
*/

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


$filasComposicion = [

    [
        [
            'valor' =>
                'Composición de la liquidación por tipo de concepto',
            'estilo' =>
                1
        ]
    ],

    [
        [
            'valor' =>
                $alcanceTexto,
            'estilo' =>
                7
        ]
    ],

    [],

    [
        [
            'valor' =>
                'Tipo de concepto',
            'estilo' =>
                2
        ],
        [
            'valor' =>
                'Importe',
            'estilo' =>
                2
        ],
        [
            'valor' =>
                'Participación',
            'estilo' =>
                2
        ]
    ]
];


foreach (
    $conceptosPorCategoria as $fila
) {

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
            )
            : 0;


    $filasComposicion[] = [

        [
            'valor' =>
                $fila['tipo']
                ?? ''
        ],

        [
            'tipo' =>
                'n',
            'valor' =>
                $importe,
            'estilo' =>
                4
        ],

        [
            'tipo' =>
                'n',
            'valor' =>
                $participacion,
            'estilo' =>
                5
        ]
    ];
}


$filasComposicion[] = [

    [
        'valor' =>
            'Total representado',
        'estilo' =>
            6
    ],

    [
        'tipo' =>
            'n',
        'valor' =>
            $totalComposicion,
        'estilo' =>
            4
    ],

    [
        'tipo' =>
            'n',
        'valor' =>
            $totalComposicion > 0
                ? 1
                : 0,
        'estilo' =>
            5
    ]
];


$hojaComposicion =
    crearHojaExcelXml(
        $filasComposicion,
        [
            28,
            22,
            18,
            3,
            12,
            12,
            12,
            12,
            12,
            12,
            12,
            12,
            12
        ],
        [
            'A1:C1',
            'A2:C2'
        ],
        $imagenes['conceptos'] !== null,
        4,
        false,
        false
    );


/*
|--------------------------------------------------------------------------
| HOJA 5 - DESCUENTOS
|--------------------------------------------------------------------------
*/

$filasDescuentos = [

    [
        [
            'valor' =>
                'Principales descuentos',
            'estilo' =>
                1
        ]
    ],

    [
        [
            'valor' =>
                $alcanceTexto,
            'estilo' =>
                7
        ]
    ],

    [],

    [
        [
            'valor' =>
                'Concepto',
            'estilo' =>
                2
        ],
        [
            'valor' =>
                'Importe acumulado',
            'estilo' =>
                2
        ]
    ]
];


foreach (
    $principalesDescuentos as $fila
) {

    $filasDescuentos[] = [

        [
            'valor' =>
                $fila['concepto']
                ?? ''
        ],

        [
            'tipo' =>
                'n',
            'valor' =>
                (float)(
                    $fila['total']
                    ?? 0
                ),
            'estilo' =>
                4
        ]
    ];
}


$hojaDescuentos =
    crearHojaExcelXml(
        $filasDescuentos,
        [
            38,
            22,
            3,
            12,
            12,
            12,
            12,
            12,
            12,
            12,
            12,
            12,
            12
        ],
        [
            'A1:B1',
            'A2:B2'
        ],
        $imagenes['descuentos'] !== null,
        4,
        false,
        false
    );


/*
|--------------------------------------------------------------------------
| LIBRO
|--------------------------------------------------------------------------
*/

$nombresHojas = [

    'Resumen',

    'Empleados por categoría',

    'Neto por período',

    'Composición',

    'Descuentos'
];


$workbookXml =
    '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
    .
    '<workbook '
    .
    'xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" '
    .
    'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
    .
    '<bookViews><workbookView/></bookViews>'
    .
    '<sheets>';


foreach (
    $nombresHojas as $indice => $nombreHoja
) {

    $workbookXml .=
        '<sheet name="'
        .
        xmlExcel(
            $nombreHoja
        )
        .
        '" sheetId="'
        .
        (
            $indice
            +
            1
        )
        .
        '" r:id="rId'
        .
        (
            $indice
            +
            2
        )
        .
        '"/>';
}


$workbookXml .=
    '</sheets>'
    .
    '<calcPr calcId="0"/>'
    .
    '</workbook>';


$workbookRelsXml =
    '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
    .
    '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
    .
    '<Relationship '
    .
    'Id="rId1" '
    .
    'Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" '
    .
    'Target="styles.xml"/>';


for (
    $i = 1;
    $i <= 5;
    $i++
) {

    $workbookRelsXml .=
        '<Relationship '
        .
        'Id="rId'
        .
        (
            $i
            +
            1
        )
        .
        '" '
        .
        'Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" '
        .
        'Target="worksheets/sheet'
        .
        $i
        .
        '.xml"/>';
}


$workbookRelsXml .=
    '</Relationships>';


/*
|--------------------------------------------------------------------------
| RELACIÓN PRINCIPAL
|--------------------------------------------------------------------------
*/

$relsPrincipalXml =
    '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
    .
    '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
    .
    '<Relationship '
    .
    'Id="rId1" '
    .
    'Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" '
    .
    'Target="xl/workbook.xml"/>'
    .
    '</Relationships>';


/*
|--------------------------------------------------------------------------
| GRÁFICOS / DIBUJOS
|--------------------------------------------------------------------------
*/

$mapaDibujos = [

    2 => [
        'clave' =>
            'categoria',
        'nombre' =>
            'Empleados por categoría'
    ],

    3 => [
        'clave' =>
            'periodo',
        'nombre' =>
            'Neto por período'
    ],

    4 => [
        'clave' =>
            'conceptos',
        'nombre' =>
            'Composición'
    ],

    5 => [
        'clave' =>
            'descuentos',
        'nombre' =>
            'Principales descuentos'
    ]
];


$dibujosActivos = [];

$numeroDibujo =
    0;


foreach (
    $mapaDibujos as $numeroHoja => $configuracion
) {

    $clave =
        $configuracion['clave'];


    if (
        $imagenes[$clave]
        ===
        null
    ) {

        continue;
    }


    $numeroDibujo++;


    $dibujosActivos[$numeroHoja] = [

        'numero_dibujo' =>
            $numeroDibujo,

        'numero_imagen' =>
            $numeroDibujo,

        'clave' =>
            $clave,

        'nombre' =>
            $configuracion['nombre']
    ];
}


/*
|--------------------------------------------------------------------------
| CONTENT TYPES
|--------------------------------------------------------------------------
*/

$contentTypesXml =
    '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
    .
    '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
    .
    '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
    .
    '<Default Extension="xml" ContentType="application/xml"/>';


if (!empty($dibujosActivos)) {

    $contentTypesXml .=
        '<Default Extension="png" ContentType="image/png"/>';
}


$contentTypesXml .=
    '<Override PartName="/xl/workbook.xml" '
    .
    'ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
    .
    '<Override PartName="/xl/styles.xml" '
    .
    'ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>';


for (
    $i = 1;
    $i <= 5;
    $i++
) {

    $contentTypesXml .=
        '<Override PartName="/xl/worksheets/sheet'
        .
        $i
        .
        '.xml" '
        .
        'ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>';
}


foreach (
    $dibujosActivos as $configuracion
) {

    $contentTypesXml .=
        '<Override PartName="/xl/drawings/drawing'
        .
        (int)$configuracion['numero_dibujo']
        .
        '.xml" '
        .
        'ContentType="application/vnd.openxmlformats-officedocument.drawing+xml"/>';
}


$contentTypesXml .=
    '</Types>';


/*
|--------------------------------------------------------------------------
| CREAR XLSX
|--------------------------------------------------------------------------
*/

$archivoTemporal =
    tempnam(
        sys_get_temp_dir(),
        'estadisticas_xlsx_'
    );


if ($archivoTemporal === false) {

    http_response_code(500);

    die(
        "No se pudo crear el archivo temporal de Excel."
    );
}


@unlink(
    $archivoTemporal
);


$archivoTemporal .=
    '.xlsx';


$zip =
    new ZipExcelSimple();


/*
|--------------------------------------------------------------------------
| ARCHIVOS BASE
|--------------------------------------------------------------------------
*/

$zip->agregar(
    '[Content_Types].xml',
    $contentTypesXml
);


$zip->agregar(
    '_rels/.rels',
    $relsPrincipalXml
);


$zip->agregar(
    'xl/workbook.xml',
    $workbookXml
);


$zip->agregar(
    'xl/_rels/workbook.xml.rels',
    $workbookRelsXml
);


$zip->agregar(
    'xl/styles.xml',
    $stylesXml
);


/*
|--------------------------------------------------------------------------
| HOJAS
|--------------------------------------------------------------------------
*/

$hojasXml = [

    1 =>
        $hojaResumen,

    2 =>
        $hojaCategoria,

    3 =>
        $hojaNeto,

    4 =>
        $hojaComposicion,

    5 =>
        $hojaDescuentos
];


foreach (
    $hojasXml as $numeroHoja => $xmlHoja
) {

    $zip->agregar(
        'xl/worksheets/sheet'
        .
        $numeroHoja
        .
        '.xml',
        $xmlHoja
    );
}


/*
|--------------------------------------------------------------------------
| DIBUJOS E IMÁGENES
|--------------------------------------------------------------------------
*/

foreach (
    $dibujosActivos as $numeroHoja => $configuracion
) {

    $numeroDibujoActual =
        (int)$configuracion[
            'numero_dibujo'
        ];


    $numeroImagenActual =
        (int)$configuracion[
            'numero_imagen'
        ];


    $clave =
        $configuracion[
            'clave'
        ];


    $zip->agregar(
        'xl/worksheets/_rels/sheet'
        .
        $numeroHoja
        .
        '.xml.rels',
        relacionesHojaDibujoXml(
            $numeroDibujoActual
        )
    );


    $zip->agregar(
        'xl/drawings/drawing'
        .
        $numeroDibujoActual
        .
        '.xml',
        crearDibujoExcelXml(
            $configuracion[
                'nombre'
            ],
            3,
            1,
            12,
            20
        )
    );


    $zip->agregar(
        'xl/drawings/_rels/drawing'
        .
        $numeroDibujoActual
        .
        '.xml.rels',
        relacionesDibujoImagenXml(
            $numeroImagenActual
        )
    );


    $zip->agregar(
        'xl/media/image'
        .
        $numeroImagenActual
        .
        '.png',
        $imagenes[$clave]
    );
}


if (
    !$zip->guardar(
        $archivoTemporal
    )
) {

    http_response_code(500);

    die(
        "No se pudo escribir el archivo XLSX."
    );
}


/*
|--------------------------------------------------------------------------
| NOMBRE DE ARCHIVO
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
    '.xlsx';


/*
|--------------------------------------------------------------------------
| SALIDA
|--------------------------------------------------------------------------
*/

header(
    'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
);


header(
    'Content-Disposition: attachment; filename="'
    .
    $nombreArchivo
    .
    '"'
);


header(
    'Content-Length: '
    .
    filesize(
        $archivoTemporal
    )
);


header(
    'Cache-Control: no-store, no-cache, must-revalidate'
);


header(
    'Pragma: no-cache'
);


readfile(
    $archivoTemporal
);


@unlink(
    $archivoTemporal
);


exit;
