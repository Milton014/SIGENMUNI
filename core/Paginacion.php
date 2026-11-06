<?php

/** Parámetros de navegación acotados, reutilizables por los listados. */
function sigenmuniParametrosPaginacion(array $entrada): array
{
    $pagina = $entrada['pagina'] ?? 1;
    $pagina = is_string($pagina) || is_int($pagina)
        ? filter_var($pagina, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) : false;
    $cantidad = $entrada['por_pagina'] ?? 25;
    $cantidad = is_string($cantidad) || is_int($cantidad)
        ? filter_var($cantidad, FILTER_VALIDATE_INT) : false;
    return [
        'pagina' => $pagina === false ? 1 : $pagina,
        'por_pagina' => in_array($cantidad, [10, 25, 50], true) ? $cantidad : 25,
    ];
}

function sigenmuniCalcularPaginacion(int $total, $pagina = 1, $porPagina = 25): array
{
    $parametros = sigenmuniParametrosPaginacion(['pagina' => $pagina, 'por_pagina' => $porPagina]);
    $total = max(0, $total);
    $cantidad = $parametros['por_pagina'];
    $paginas = max(1, intdiv($total, $cantidad) + ($total % $cantidad > 0 ? 1 : 0));
    $pagina = min($parametros['pagina'], $paginas);
    $desplazamiento = ($pagina - 1) * $cantidad;
    return [
        'total' => $total, 'pagina' => $pagina, 'paginas' => $paginas,
        'por_pagina' => $cantidad, 'desplazamiento' => $desplazamiento,
        'desde' => $total > 0 ? $desplazamiento + 1 : 0,
        'hasta' => $desplazamiento + min($cantidad, $total - $desplazamiento),
    ];
}

/** Ventana breve: primera, última y hasta dos páginas a cada lado de la actual. */
function sigenmuniNumerosPaginacion(array $paginacion): array
{
    $numeros = [1, $paginacion['paginas']];
    for ($n = max(1, $paginacion['pagina'] - 2); $n <= min($paginacion['paginas'], $paginacion['pagina'] + 2); $n++) {
        $numeros[] = $n;
    }
    $numeros = array_values(array_unique($numeros));
    sort($numeros, SORT_NUMERIC);
    return $numeros;
}
