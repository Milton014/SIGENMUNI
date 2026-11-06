<?php
require_once __DIR__ . '/../../../core/Paginacion.php';

/** Contexto acotado para volver del historial y de sus recibos. */
function sigenmuniContextoHistorialReporte(array $entrada): array
{
    $texto = static function (string $clave) use ($entrada): string {
        $v = is_string($entrada[$clave] ?? null) ? trim($entrada[$clave]) : '';
        return mb_check_encoding($v, 'UTF-8') ? mb_substr($v, 0, 120, 'UTF-8') : '';
    };
    $legajo = $texto('legajo'); if ($legajo !== '' && !ctype_digit($legajo)) $legajo = '';
    $estado = $texto('estado'); if (!in_array($estado, ['', '0', '1'], true)) $estado = '';
    $historial = sigenmuniParametrosPaginacion([
        'pagina' => $entrada['pagina_historial'] ?? 1, 'por_pagina' => $entrada['por_pagina_historial'] ?? 25,
    ]);
    return array_merge(sigenmuniParametrosPaginacion($entrada), [
        'legajo' => $legajo, 'busqueda' => $texto('busqueda'), 'estado' => $estado, 'filtrar' => '1',
        'pagina_historial' => $historial['pagina'], 'por_pagina_historial' => $historial['por_pagina'],
    ]);
}
