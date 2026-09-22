<?php

require_once __DIR__ . '/../../../lib/fpdf/fpdf.php';


class GeneradorReciboPDF extends FPDF
{
    /*
    |--------------------------------------------------------------------------
    | DINERO
    |--------------------------------------------------------------------------
    */

    private function dinero($valor)
    {
        return '$ '
            . number_format(
                (float)$valor,
                2,
                ',',
                '.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | TEXTO PARA FPDF
    |--------------------------------------------------------------------------
    |
    | FPDF estándar trabaja con ISO-8859-1.
    |
    |--------------------------------------------------------------------------
    */

    private function texto($texto)
    {
        return utf8_decode(
            (string)$texto
        );
    }


    /*
    |--------------------------------------------------------------------------
    | TÍTULO DE SECCIÓN
    |--------------------------------------------------------------------------
    */

    public function tituloSeccion($titulo)
    {
        $this->Ln(4);

        $this->SetFont(
            'Arial',
            'B',
            10
        );

        $this->SetTextColor(
            15,
            118,
            110
        );

        $this->SetFillColor(
            236,
            253,
            245
        );

        $this->SetDrawColor(
            153,
            246,
            228
        );

        $this->Cell(
            186,
            8,
            $this->texto($titulo),
            1,
            1,
            'L',
            true
        );

        $this->SetTextColor(
            31,
            41,
            55
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CABECERA TABLA
    |--------------------------------------------------------------------------
    */

    public function cabeceraTabla()
    {
        $this->SetFont(
            'Arial',
            'B',
            8
        );

        $this->SetFillColor(
            243,
            244,
            246
        );

        $this->SetDrawColor(
            229,
            231,
            235
        );


        $this->Cell(
            22,
            7,
            $this->texto('Código'),
            1,
            0,
            'C',
            true
        );


        $this->Cell(
            78,
            7,
            'Concepto',
            1,
            0,
            'C',
            true
        );


        $this->Cell(
            25,
            7,
            'Cantidad',
            1,
            0,
            'C',
            true
        );


        $this->Cell(
            25,
            7,
            '%',
            1,
            0,
            'C',
            true
        );


        $this->Cell(
            36,
            7,
            'Monto',
            1,
            1,
            'C',
            true
        );
    }


    /*
    |--------------------------------------------------------------------------
    | FILA DE TABLA
    |--------------------------------------------------------------------------
    */

    public function filaTabla(
        $codigo,
        $concepto,
        $cantidad,
        $porcentaje,
        $monto
    ) {
        $this->SetFont(
            'Arial',
            '',
            8
        );

        $this->SetDrawColor(
            229,
            231,
            235
        );


        $this->Cell(
            22,
            7,
            (string)$codigo,
            1,
            0,
            'C'
        );


        $conceptoCorto =
            mb_substr(
                (string)$concepto,
                0,
                45,
                'UTF-8'
            );


        $this->Cell(
            78,
            7,
            $this->texto(
                $conceptoCorto
            ),
            1,
            0,
            'L'
        );


        $this->Cell(
            25,
            7,
            number_format(
                (float)$cantidad,
                2,
                ',',
                '.'
            ),
            1,
            0,
            'R'
        );


        $this->Cell(
            25,
            7,
            number_format(
                (float)$porcentaje,
                2,
                ',',
                '.'
            ),
            1,
            0,
            'R'
        );


        $this->Cell(
            36,
            7,
            $this->dinero(
                $monto
            ),
            1,
            1,
            'R'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | TOTAL DE TABLA
    |--------------------------------------------------------------------------
    */

    public function totalTabla(
        $texto,
        $monto
    ) {
        $this->SetFont(
            'Arial',
            'B',
            8
        );

        $this->SetFillColor(
            240,
            253,
            250
        );

        $this->SetDrawColor(
            153,
            246,
            228
        );


        $this->Cell(
            150,
            7,
            $this->texto(
                $texto
            ),
            1,
            0,
            'R',
            true
        );


        $this->Cell(
            36,
            7,
            $this->dinero(
                $monto
            ),
            1,
            1,
            'R',
            true
        );
    }


    /*
    |--------------------------------------------------------------------------
    | GENERAR RECIBO
    |--------------------------------------------------------------------------
    |
    | Retorna la ruta absoluta del PDF generado.
    |
    |--------------------------------------------------------------------------
    */

    public static function generar(
        array $datos,
        array $haberesRem,
        array $haberesNoRem,
        array $asignaciones,
        array $descuentos,
        array $aportesPatronales,
        $totalHaberesRem,
        $totalHaberesNoRem,
        $totalAsignaciones,
        $totalDescuentos,
        $totalPatronales,
        $neto,
        $antiguedadTexto
    ) {
        /*
        |--------------------------------------------------------------------------
        | CREAR PDF
        |--------------------------------------------------------------------------
        */

        $pdf =
            new self(
                'P',
                'mm',
                'A4'
            );


        $pdf->SetMargins(
            12,
            12,
            12
        );


        $pdf->SetAutoPageBreak(
            true,
            15
        );


        $pdf->AddPage();


        /*
        |--------------------------------------------------------------------------
        | CABECERA SUPERIOR
        |--------------------------------------------------------------------------
        */

        $pdf->SetDrawColor(
            15,
            118,
            110
        );

        $pdf->SetFillColor(
            15,
            118,
            110
        );


        $pdf->Rect(
            0,
            0,
            210,
            25,
            'F'
        );


        /*
        |--------------------------------------------------------------------------
        | ESCUDO
        |--------------------------------------------------------------------------
        */

        $rutaEscudo =
            __DIR__
            . '/../../../public/assets/img/escudo.jpg';


        if (
            file_exists(
                $rutaEscudo
            )
        ) {

            $pdf->Image(
                $rutaEscudo,
                15,
                5,
                17
            );
        }


        /*
        |--------------------------------------------------------------------------
        | TÍTULO
        |--------------------------------------------------------------------------
        */

        $pdf->SetTextColor(
            255,
            255,
            255
        );


        $pdf->SetFont(
            'Arial',
            'B',
            17
        );


        $pdf->Cell(
            186,
            8,
            $pdf->texto(
                'RECIBO DE SUELDO'
            ),
            0,
            1,
            'C'
        );


        $pdf->SetFont(
            'Arial',
            '',
            10
        );


        $pdf->Cell(
            186,
            6,
            $pdf->texto(
                'Municipalidad de Fortín Lugones'
            ),
            0,
            1,
            'C'
        );


        $pdf->Ln(12);


        $pdf->SetTextColor(
            31,
            41,
            55
        );


        /*
        |--------------------------------------------------------------------------
        | ESTADO
        |--------------------------------------------------------------------------
        */

        $estado =
            strtoupper(
                trim(
                    (string)(
                        $datos['estado']
                        ?? ''
                    )
                )
            );


        /*
        |--------------------------------------------------------------------------
        | DATOS DE LIQUIDACIÓN
        |--------------------------------------------------------------------------
        */

        $pdf->SetFont(
            'Arial',
            'B',
            10
        );


        $pdf->SetFillColor(
            243,
            244,
            246
        );


        $pdf->SetDrawColor(
            209,
            213,
            219
        );


        $pdf->Cell(
            93,
            8,
            $pdf->texto(
                'Tipo Liquidación: '
                . (
                    $datos['tipo_liquidacion']
                    ?? '-'
                )
            ),
            1,
            0,
            'L',
            true
        );


        $pdf->Cell(
            93,
            8,
            $pdf->texto(
                'Período: '
                . (
                    $datos['periodo']
                    ?? '-'
                )
            ),
            1,
            1,
            'L',
            true
        );


        /*
        |--------------------------------------------------------------------------
        | FECHA LIQUIDACIÓN
        |--------------------------------------------------------------------------
        */

        $fechaLiquidacion =
            '-';


        if (
            !empty(
                $datos[
                    'fecha_liquidacion'
                ]
            )
        ) {

            $fechaLiquidacion =
                date(
                    'd/m/Y',
                    strtotime(
                        $datos[
                            'fecha_liquidacion'
                        ]
                    )
                );
        }


        $pdf->Cell(
            93,
            8,
            $pdf->texto(
                'Fecha Liquidación: '
                . $fechaLiquidacion
            ),
            1,
            0,
            'L',
            true
        );


        $pdf->Cell(
            93,
            8,
            $pdf->texto(
                'Estado: '
                . $estado
            ),
            1,
            1,
            'L',
            true
        );


        $pdf->Ln(5);


        /*
        |--------------------------------------------------------------------------
        | EMPLEADO
        |--------------------------------------------------------------------------
        */

        $pdf->SetFont(
            'Arial',
            'B',
            9
        );


        $pdf->Cell(
            93,
            8,
            'Empleado',
            1,
            0,
            'L'
        );


        $pdf->Cell(
            93,
            8,
            'Legajo',
            1,
            1,
            'L'
        );


        $pdf->SetFont(
            'Arial',
            '',
            9
        );


        $nombreEmpleado =
            (
                $datos['apellido']
                ?? ''
            )
            .
            ', '
            .
            (
                $datos['nombre']
                ?? ''
            );


        $pdf->Cell(
            93,
            8,
            $pdf->texto(
                $nombreEmpleado
            ),
            1,
            0,
            'L'
        );


        $pdf->Cell(
            93,
            8,
            $pdf->texto(
                $datos[
                    'nro_legajo'
                ]
                ?? '-'
            ),
            1,
            1,
            'L'
        );


        /*
        |--------------------------------------------------------------------------
        | CATEGORÍA / DNI
        |--------------------------------------------------------------------------
        */

        $pdf->SetFont(
            'Arial',
            'B',
            9
        );


        $pdf->Cell(
            93,
            8,
            $pdf->texto(
                'Categoría'
            ),
            1,
            0,
            'L'
        );


        $pdf->Cell(
            93,
            8,
            'DNI',
            1,
            1,
            'L'
        );


        $pdf->SetFont(
            'Arial',
            '',
            9
        );


        $categoria =
            (
                $datos[
                    'categoria_codigo'
                ]
                ?? '-'
            )
            .
            ' - '
            .
            (
                $datos[
                    'categoria_nombre'
                ]
                ?? '-'
            );


        $pdf->Cell(
            93,
            8,
            $pdf->texto(
                $categoria
            ),
            1,
            0,
            'L'
        );


        $pdf->Cell(
            93,
            8,
            $pdf->texto(
                $datos['dni']
                ?? '-'
            ),
            1,
            1,
            'L'
        );


        /*
        |--------------------------------------------------------------------------
        | FECHA ALTA / ANTIGÜEDAD
        |--------------------------------------------------------------------------
        */

        $pdf->SetFont(
            'Arial',
            'B',
            9
        );


        $pdf->Cell(
            93,
            8,
            'Fecha de Alta',
            1,
            0,
            'L'
        );


        $pdf->Cell(
            93,
            8,
            $pdf->texto(
                'Antigüedad'
            ),
            1,
            1,
            'L'
        );


        $pdf->SetFont(
            'Arial',
            '',
            9
        );


        $fechaAlta =
            '-';


        if (
            !empty(
                $datos['fecha_alta']
            )
        ) {

            $fechaAlta =
                date(
                    'd/m/Y',
                    strtotime(
                        $datos['fecha_alta']
                    )
                );
        }


        $pdf->Cell(
            93,
            8,
            $fechaAlta,
            1,
            0,
            'L'
        );


        $pdf->Cell(
            93,
            8,
            $pdf->texto(
                $antiguedadTexto
            ),
            1,
            1,
            'L'
        );


        /*
        |--------------------------------------------------------------------------
        | HABERES REMUNERATIVOS
        |--------------------------------------------------------------------------
        */

        $pdf->tituloSeccion(
            'Haberes Remunerativos'
        );


        $pdf->cabeceraTabla();


        foreach (
            $haberesRem
            as
            $item
        ) {

            $pdf->filaTabla(
                $item['codigo'],
                $item['nombre'],
                $item['cantidad'],
                $item['porcentaje_aplicado'],
                $item['monto']
            );
        }


        $pdf->totalTabla(
            'Total Haberes Remunerativos',
            $totalHaberesRem
        );


        /*
        |--------------------------------------------------------------------------
        | HABERES NO REMUNERATIVOS
        |--------------------------------------------------------------------------
        */

        $pdf->tituloSeccion(
            'Haberes No Remunerativos'
        );


        $pdf->cabeceraTabla();


        foreach (
            $haberesNoRem
            as
            $item
        ) {

            $pdf->filaTabla(
                $item['codigo'],
                $item['nombre'],
                $item['cantidad'],
                $item['porcentaje_aplicado'],
                $item['monto']
            );
        }


        $pdf->totalTabla(
            'Total Haberes No Remunerativos',
            $totalHaberesNoRem
        );


        /*
        |--------------------------------------------------------------------------
        | ASIGNACIONES
        |--------------------------------------------------------------------------
        */

        $pdf->tituloSeccion(
            'Asignaciones Familiares'
        );


        $pdf->cabeceraTabla();


        foreach (
            $asignaciones
            as
            $item
        ) {

            $pdf->filaTabla(
                $item['codigo'],
                $item['nombre'],
                $item['cantidad'],
                $item['porcentaje_aplicado'],
                $item['monto']
            );
        }


        $pdf->totalTabla(
            'Total Asignaciones',
            $totalAsignaciones
        );


        /*
        |--------------------------------------------------------------------------
        | DESCUENTOS
        |--------------------------------------------------------------------------
        */

        $pdf->tituloSeccion(
            'Descuentos'
        );


        $pdf->cabeceraTabla();


        foreach (
            $descuentos
            as
            $item
        ) {

            $pdf->filaTabla(
                $item['codigo'],
                $item['nombre'],
                $item['cantidad'],
                $item['porcentaje_aplicado'],
                $item['monto']
            );
        }


        $pdf->totalTabla(
            'Total Descuentos',
            $totalDescuentos
        );


        /*
        |--------------------------------------------------------------------------
        | APORTES PATRONALES
        |--------------------------------------------------------------------------
        */

        $pdf->tituloSeccion(
            'Aportes Patronales'
        );


        $pdf->SetFont(
            'Arial',
            '',
            7
        );


        $pdf->SetFillColor(
            250,
            245,
            255
        );


        $pdf->SetTextColor(
            107,
            33,
            168
        );


        $pdf->MultiCell(
            186,
            5,
            $pdf->texto(
                'Los aportes patronales son obligaciones a cargo del empleador y no afectan el Neto a Cobrar del empleado.'
            ),
            1,
            'L',
            true
        );


        $pdf->SetTextColor(
            31,
            41,
            55
        );


        $pdf->Ln(2);


        $pdf->cabeceraTabla();


        foreach (
            $aportesPatronales
            as
            $item
        ) {

            $pdf->filaTabla(
                $item['codigo'],
                $item['nombre'],
                $item['cantidad'],
                $item['porcentaje_aplicado'],
                $item['monto']
            );
        }


        $pdf->totalTabla(
            'Total Aportes Patronales',
            $totalPatronales
        );


        /*
        |--------------------------------------------------------------------------
        | RESUMEN
        |--------------------------------------------------------------------------
        */

        $pdf->Ln(6);


        $pdf->SetFont(
            'Arial',
            'B',
            9
        );


        $pdf->SetFillColor(
            249,
            250,
            251
        );


        $pdf->SetDrawColor(
            209,
            213,
            219
        );


        $pdf->Cell(
            93,
            8,
            'Total Remunerativo: '
            . $pdf->dinero(
                $totalHaberesRem
            ),
            1,
            0,
            'L',
            true
        );


        $pdf->Cell(
            93,
            8,
            'Total No Remunerativo: '
            . $pdf->dinero(
                $totalHaberesNoRem
            ),
            1,
            1,
            'L',
            true
        );


        $pdf->Cell(
            93,
            8,
            'Total Asignaciones: '
            . $pdf->dinero(
                $totalAsignaciones
            ),
            1,
            0,
            'L',
            true
        );


        $pdf->Cell(
            93,
            8,
            'Total Descuentos: '
            . $pdf->dinero(
                $totalDescuentos
            ),
            1,
            1,
            'L',
            true
        );


        $pdf->Cell(
            186,
            8,
            'Total Aportes Patronales: '
            . $pdf->dinero(
                $totalPatronales
            ),
            1,
            1,
            'L',
            true
        );


        /*
        |--------------------------------------------------------------------------
        | NETO
        |--------------------------------------------------------------------------
        */

        $pdf->Ln(4);


        $pdf->SetFillColor(
            220,
            252,
            231
        );


        $pdf->SetDrawColor(
            22,
            163,
            74
        );


        $pdf->SetFont(
            'Arial',
            'B',
            15
        );


        $pdf->Cell(
            186,
            14,
            $pdf->texto(
                'NETO A COBRAR: '
            )
            .
            $pdf->dinero(
                $neto
            ),
            1,
            1,
            'C',
            true
        );


        /*
        |--------------------------------------------------------------------------
        | LEYENDA
        |--------------------------------------------------------------------------
        */

        $pdf->Ln(8);


        $pdf->SetFont(
            'Arial',
            '',
            8
        );


        $pdf->SetTextColor(
            107,
            114,
            128
        );


        $pdf->MultiCell(
            186,
            5,
            $pdf->texto(
                'Documento generado automáticamente por SIGENMUNI. '
                .
                'Este recibo corresponde a la liquidación indicada y '
                .
                'se emite para constancia del empleado y la Municipalidad '
                .
                'de Fortín Lugones.'
            ),
            0,
            'C'
        );


        $pdf->SetTextColor(
            31,
            41,
            55
        );


        /*
        |--------------------------------------------------------------------------
        | FIRMAS
        |--------------------------------------------------------------------------
        */

        $pdf->Ln(15);


        $pdf->SetFont(
            'Arial',
            '',
            9
        );


        $pdf->Cell(
            62,
            8,
            '_________________________',
            0,
            0,
            'C'
        );


        $pdf->Cell(
            62,
            8,
            '_________________________',
            0,
            0,
            'C'
        );


        $pdf->Cell(
            62,
            8,
            '_________________________',
            0,
            1,
            'C'
        );


        $pdf->Cell(
            62,
            6,
            'Firma del Empleado',
            0,
            0,
            'C'
        );


        $pdf->Cell(
            62,
            6,
            $pdf->texto(
                'Tesorería'
            ),
            0,
            0,
            'C'
        );


        $pdf->Cell(
            62,
            6,
            'Autoridad Municipal',
            0,
            1,
            'C'
        );


        /*
        |--------------------------------------------------------------------------
        | CARPETA TEMPORAL
        |--------------------------------------------------------------------------
        */

        $carpetaTemp =
            __DIR__
            . '/../../../storage/temp_recibos';


        if (
            !is_dir(
                $carpetaTemp
            )
        ) {

            if (
                !mkdir(
                    $carpetaTemp,
                    0777,
                    true
                )
                &&
                !is_dir(
                    $carpetaTemp
                )
            ) {

                throw new Exception(
                    'No se pudo crear la carpeta temporal de recibos.'
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | NOMBRE DEL ARCHIVO
        |--------------------------------------------------------------------------
        */

        $legajo =
            preg_replace(
                '/[^a-zA-Z0-9_-]/',
                '',
                (string)(
                    $datos[
                        'nro_legajo'
                    ]
                    ?? 'empleado'
                )
            );


        $periodo =
            preg_replace(
                '/[^0-9-]/',
                '',
                (string)(
                    $datos['periodo']
                    ?? 'periodo'
                )
            );


        $liquidacionId =
            (int)(
                $datos[
                    'liquidacion_id'
                ]
                ?? 0
            );


        $nombreArchivo =
            'recibo_'
            . $legajo
            . '_'
            . $periodo
            . '_liq'
            . $liquidacionId
            . '.pdf';


        $rutaPDF =
            $carpetaTemp
            . '/'
            . $nombreArchivo;


        /*
        |--------------------------------------------------------------------------
        | GUARDAR
        |--------------------------------------------------------------------------
        */

        $pdf->Output(
            'F',
            $rutaPDF
        );


        if (
            !file_exists(
                $rutaPDF
            )
        ) {

            throw new Exception(
                'No se pudo generar el archivo PDF del recibo.'
            );
        }


        return [

            'ruta' =>
                $rutaPDF,

            'nombre' =>
                $nombreArchivo
        ];
    }
}