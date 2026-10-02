<?php
require_once __DIR__ . '/../../../lib/fpdf/fpdf.php';

/** Presentación del recibo: usa importes preparados, sin calcular liquidaciones. */
class GeneradorReciboPDF extends FPDF
{
    private array $datosRecibo = [];
    private string $antiguedad = '';
    private ?string $tablaActual = null;
    private int $paginaInicioRecibo = 1;
    private const ANCHO = 188;
    private const LIMITE_Y = 279;
    private const ALTO_LINEA = 4.1;

    private function texto($valor): string
    {
        // Las fuentes estándar de FPDF usan Windows-1252; compatible con PHP 8.2.
        return mb_convert_encoding((string)$valor, 'Windows-1252', 'UTF-8');
    }

    private function dinero($valor): string
    {
        return '$ ' . number_format((float)$valor, 2, ',', '.');
    }

    private function fecha($valor): string
    {
        $fecha = $valor ? strtotime((string)$valor) : false;
        return $fecha === false ? '-' : date('d/m/Y', $fecha);
    }

    public function Header()
    {
        $d = $this->datosRecibo;
        $this->SetDrawColor(15, 118, 110);
        $this->SetTextColor(15, 118, 110);
        $escudo = __DIR__ . '/../../../public/assets/img/escudo.jpg';
        if (is_file($escudo)) $this->Image($escudo, 12, 10, 12, 14);
        $this->SetXY(28, 10);
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(171, 6, 'RECIBO DE SUELDO');
        $this->SetXY(28, 16);
        $this->SetFont('Arial', '', 9);
        $this->Cell(171, 5, $this->texto('Municipalidad de Fortín Lugones | SIGENMUNI'));
        $this->Line(11, 26, 199, 26);
        $this->SetXY(11, 28);
        $this->SetTextColor(31, 41, 55);
        $this->SetFont('Arial', 'B', 9);
        $this->fila(['Período: ' . ($d['periodo'] ?? '-'), 'Liquidación: ' . ($d['liquidacion_id'] ?? '-'), 'Estado: ' . ($d['estado'] ?? '-')], [63, 62, 63], [], false);
        $this->SetFont('Arial', '', 9);
        $this->fila(['Empleado: ' . trim(($d['apellido'] ?? '') . ', ' . ($d['nombre'] ?? ''), ', '), 'Legajo: ' . ($d['nro_legajo'] ?? '-')], [137, 51], [], false);
        if ($this->PageNo() === $this->paginaInicioRecibo) {
            $this->fila(['DNI: ' . ($d['dni'] ?? '-') . ' | CUIL: ' . ($d['cuil'] ?? '-'), 'Fecha liquidación: ' . $this->fecha($d['fecha_liquidacion'] ?? null)], [112, 76], [], false);
            $this->fila(['Categoría: ' . ($d['categoria_codigo'] ?? '-') . ' - ' . ($d['categoria_nombre'] ?? '-'), 'Alta: ' . $this->fecha($d['fecha_alta'] ?? null) . ' | Antigüedad: ' . $this->antiguedad], [112, 76], [], false);
            $this->fila(['Tipo: ' . str_replace('_', ' ', $d['tipo_liquidacion'] ?? '-'), 'Unidad: ' . ($d['oficina'] ?? $d['oficina_nombre'] ?? '-')], [76, 112], [], false);
            if (!empty($d['oficina_cuit'])) $this->fila(['CUIT de la unidad: ' . $d['oficina_cuit']], [188], [], false);
        } else {
            $this->SetFont('Arial', 'I', 9);
            $this->Cell(self::ANCHO, 5, $this->texto('Continuación del recibo'), 0, 1);
        }
        $this->Ln(3);
        if ($this->tablaActual !== null) $this->cabeceraDetalle();
    }

    public function Footer()
    {
        $this->SetY(-12);
        $this->SetDrawColor(203, 213, 225);
        $this->Line(11, $this->GetY(), 199, $this->GetY());
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(100, 116, 139);
        $this->Cell(145, 6, $this->texto('SIGENMUNI | Recibo de la liquidación indicada'), 0, 0);
        $this->Cell(43, 6, $this->texto('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    /** Divide por el ancho real, incluidas palabras largas sin espacios. */
    private function lineas(string $texto, float $ancho): array
    {
        $resultado = [];
        foreach (explode("\n", str_replace("\r", '', $texto)) as $parrafo) {
            $linea = '';
            foreach (preg_split('/ +/', trim($parrafo)) as $palabra) {
                $candidata = $linea === '' ? $palabra : $linea . ' ' . $palabra;
                if ($this->GetStringWidth($candidata) <= $ancho) { $linea = $candidata; continue; }
                if ($linea !== '') $resultado[] = $linea;
                $linea = '';
                for ($i = 0; $i < strlen($palabra); $i++) {
                    if ($linea !== '' && $this->GetStringWidth($linea . $palabra[$i]) > $ancho) { $resultado[] = $linea; $linea = ''; }
                    $linea .= $palabra[$i];
                }
            }
            $resultado[] = $linea;
        }
        return $resultado ?: [''];
    }

    private function espacio(float $alto): void
    {
        if ($this->GetY() + $alto > self::LIMITE_Y) $this->AddPage();
    }

    /** Filas multilínea, con continuación en otra página si resulta necesario. */
    private function fila(array $valores, array $anchos, array $alineaciones = [], bool $paginar = true, bool $relleno = false): void
    {
        $lineas = [];
        foreach ($valores as $i => $valor) $lineas[] = $this->lineas($this->texto($valor), $anchos[$i] - 3);
        $cantidad = max(array_map('count', $lineas));
        $alto = max(5.2, $cantidad * self::ALTO_LINEA + 1);
        // Una fila más alta que una página empieza aquí y continúa en fragmentos.
        if ($paginar && $alto <= self::LIMITE_Y - 65) $this->espacio($alto);
        for ($desde = 0; $desde < $cantidad;) {
            $capacidad = $paginar ? max(1, (int)floor((self::LIMITE_Y - $this->GetY() - 1) / self::ALTO_LINEA)) : $cantidad;
            $tomar = min($cantidad - $desde, $capacidad);
            $alto = max(5.2, $tomar * self::ALTO_LINEA + 1);
            $x = 11; $y = $this->GetY();
            $this->SetDrawColor(218, 225, 232);
            foreach ($anchos as $i => $ancho) {
                $this->Rect($x, $y, $ancho, $alto, $relleno ? 'DF' : 'D');
                foreach (array_slice($lineas[$i], $desde, $tomar) as $j => $linea) {
                    $this->SetXY($x + 1.5, $y + 0.5 + $j * self::ALTO_LINEA);
                    $this->Cell($ancho - 3, self::ALTO_LINEA, $linea, 0, 0, $alineaciones[$i] ?? 'L');
                }
                $x += $ancho;
            }
            $this->SetXY(11, $y + $alto);
            $desde += $tomar;
            if ($desde < $cantidad) $this->AddPage();
        }
    }

    private function cabeceraDetalle(): void
    {
        $this->SetFont('Arial', 'B', 9);
        $this->SetTextColor(255, 255, 255);
        $this->SetFillColor(15, 118, 110);
        if ($this->tablaActual === 'patronales') $this->fila(['Código', 'Aporte patronal', 'Cant.', '%', 'Importe'], [14, 102, 16, 20, 36], [], false, true);
        else $this->fila(['Código', 'Concepto', 'Cant.', '%', 'Haberes', 'Descuentos'], [14, 78, 16, 16, 32, 32], [], false, true);
        $this->SetTextColor(31, 41, 55);
        $this->SetFont('Arial', '', 9);
    }

    private function detalle(array $filas, string $titulo, bool $descuento = false): void
    {
        if (!$filas) return;
        $this->espacio(11);
        $this->SetFillColor(239, 246, 246);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(self::ANCHO, 4.5, $this->texto($titulo), 0, 1, 'L', true);
        $this->SetFont('Arial', '', 9);
        foreach ($filas as $fila) $this->fila([
            $fila['codigo'] ?? '', $fila['nombre'] ?? '',
            number_format((float)($fila['cantidad'] ?? 0), 2, ',', '.'),
            number_format((float)($fila['porcentaje_aplicado'] ?? 0), 2, ',', '.'),
            $descuento ? '' : $this->dinero($fila['monto'] ?? 0),
            $descuento ? $this->dinero($fila['monto'] ?? 0) : ''
        ], [14, 78, 16, 16, 32, 32], ['C', 'L', 'R', 'R', 'R', 'R']);
    }

    private function contenido(array $recibo): void
    {
        $this->tablaActual = 'haberes';
        $this->cabeceraDetalle();
        $this->detalle($recibo['haberesRem'], 'Haberes remunerativos');
        $this->detalle($recibo['haberesNoRem'], 'Haberes no remunerativos');
        $this->detalle($recibo['asignaciones'], 'Asignaciones familiares');
        $this->detalle($recibo['descuentos'], 'Descuentos', true);
        $this->tablaActual = null;
        if ($recibo['aportesPatronales']) {
            $this->espacio(22);
            $this->Ln(2);
            $this->SetFont('Arial', '', 9);
            $this->Cell(self::ANCHO, 5, $this->texto('Aportes patronales: a cargo del empleador, no se descuentan del neto.'), 0, 1);
            $this->tablaActual = 'patronales';
            $this->cabeceraDetalle();
            foreach ($recibo['aportesPatronales'] as $fila) $this->fila([
                $fila['codigo'] ?? '', $fila['nombre'] ?? '',
                number_format((float)($fila['cantidad'] ?? 0), 2, ',', '.'),
                number_format((float)($fila['porcentaje_aplicado'] ?? 0), 2, ',', '.'),
                $this->dinero($fila['monto'] ?? 0)
            ], [14, 102, 16, 20, 36], ['C', 'L', 'R', 'R', 'R']);
            $this->tablaActual = null;
        }
        // Resumen y firmas juntos, sin reducir la letra para forzarlos.
        $this->espacio(49);
        $this->Ln(3);
        $this->SetFont('Arial', '', 9);
        $this->fila(['Remunerativos: ' . $this->dinero($recibo['totalHaberesRem']), 'No remunerativos: ' . $this->dinero($recibo['totalHaberesNoRem'])], [94, 94]);
        $this->fila(['Asignaciones: ' . $this->dinero($recibo['totalAsignaciones']), 'Descuentos: ' . $this->dinero($recibo['totalDescuentos'])], [94, 94]);
        $this->fila(['Aportes patronales (no afectan el neto): ' . $this->dinero($recibo['totalPatronales'])], [188]);
        $this->Ln(2);
        $this->SetFillColor(15, 118, 110);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 13);
        $this->Cell(self::ANCHO, 10, $this->texto('NETO A COBRAR: ') . $this->dinero($recibo['neto']), 0, 1, 'C', true);
        $this->SetTextColor(31, 41, 55);
        $this->Ln(9);
        $this->SetFont('Arial', '', 9);
        foreach (['Firma del empleado', 'Tesorería', 'Autoridad municipal'] as $i => $firma) {
            $x = 11 + $i * (self::ANCHO / 3);
            $this->Line($x + 5, $this->GetY(), $x + self::ANCHO / 3 - 5, $this->GetY());
            $this->Cell(self::ANCHO / 3, 6, $this->texto($firma), 0, 0, 'C');
        }
    }

    private static function nuevoDocumento(): self
    {
        $pdf = new self('P', 'mm', 'A4');
        $pdf->SetMargins(11, 10, 11);
        $pdf->SetAutoPageBreak(false);
        $pdf->AliasNbPages();
        $pdf->SetTitle('Recibo de sueldo - SIGENMUNI');
        $pdf->SetAuthor('Municipalidad de Fortin Lugones');
        return $pdf;
    }

    private function agregarRecibo(array $recibo): void
    {
        if (strtoupper(trim((string)($recibo['datos']['estado'] ?? ''))) !== 'CERRADA') {
            throw new DomainException('Solo se pueden generar PDF de liquidaciones CERRADAS.');
        }
        $this->datosRecibo = $recibo['datos'];
        $this->antiguedad = (string)$recibo['antiguedadTexto'];
        $this->tablaActual = null;
        $this->paginaInicioRecibo = $this->PageNo() + 1;
        $this->AddPage();
        $this->contenido($recibo);
    }

    /** Descarga y correo comparten este contenido binario. No escribe archivos. */
    public static function generarEnMemoria(array $recibo): array
    {
        $datos = $recibo['datos'];
        $pdf = self::nuevoDocumento();
        $pdf->agregarRecibo($recibo);
        $legajo = preg_replace('/[^a-zA-Z0-9_-]/', '', (string)($datos['nro_legajo'] ?? 'empleado')) ?: 'empleado';
        $periodo = preg_replace('/[^0-9-]/', '', (string)($datos['periodo'] ?? '')) ?: 'periodo';
        $nombre = 'recibo_' . $legajo . '_' . $periodo . '_liq' . (int)($datos['liquidacion_id'] ?? 0) . '.pdf';
        return ['contenido' => $pdf->Output('S'), 'nombre' => $nombre];
    }

    /** Un único PDF, con cada empleado empezando en una nueva página A4. */
    public static function generarLoteEnMemoria(array $recibos): array
    {
        if (!$recibos) throw new InvalidArgumentException('No hay recibos para imprimir.');
        $primero = reset($recibos);
        $liquidacionId = (int)($primero['datos']['liquidacion_id'] ?? 0);
        if ($liquidacionId <= 0) throw new InvalidArgumentException('Liquidación no válida.');
        $pdf = self::nuevoDocumento();
        foreach ($recibos as $recibo) {
            if ((int)($recibo['datos']['liquidacion_id'] ?? 0) !== $liquidacionId) {
                throw new InvalidArgumentException('Los recibos deben pertenecer a la misma liquidación.');
            }
            $pdf->agregarRecibo($recibo);
        }
        $periodo = preg_replace('/[^0-9-]/', '', (string)($primero['datos']['periodo'] ?? '')) ?: 'periodo';
        return [
            'contenido' => $pdf->Output('S'),
            'nombre' => 'recibos_' . $periodo . '_liq' . $liquidacionId . '.pdf'
        ];
    }

    /** Compatibilidad con el correo existente. Temporal exclusivo por llamada. */
    public static function generar(
        array $datos, array $haberesRem, array $haberesNoRem, array $asignaciones,
        array $descuentos, array $aportesPatronales, $totalHaberesRem,
        $totalHaberesNoRem, $totalAsignaciones, $totalDescuentos, $totalPatronales,
        $neto, $antiguedadTexto
    ) {
        $resultado = self::generarEnMemoria(compact('datos', 'haberesRem', 'haberesNoRem', 'asignaciones', 'descuentos', 'aportesPatronales', 'totalHaberesRem', 'totalHaberesNoRem', 'totalAsignaciones', 'totalDescuentos', 'totalPatronales', 'neto', 'antiguedadTexto'));
        $carpeta = __DIR__ . '/../../../storage/temp_recibos';
        if (!is_dir($carpeta) && !mkdir($carpeta, 0700, true) && !is_dir($carpeta)) throw new RuntimeException('No se pudo crear la carpeta temporal de recibos.');
        $ruta = $carpeta . '/recibo_' . bin2hex(random_bytes(16)) . '.pdf';
        $archivo = fopen($ruta, 'xb');
        if ($archivo === false) throw new RuntimeException('No se pudo crear el PDF temporal.');
        try {
            $escritos = fwrite($archivo, $resultado['contenido']);
            if ($escritos !== strlen($resultado['contenido'])) throw new RuntimeException('No se pudo guardar el PDF completo.');
        } catch (Throwable $e) {
            fclose($archivo);
            unlink($ruta);
            throw $e;
        }
        fclose($archivo);
        return ['ruta' => $ruta, 'nombre' => $resultado['nombre']];
    }
}
