<?php
require_once __DIR__ . '/../core/Url.php';
require_once __DIR__ . '/../core/Paginacion.php';
// El controlador define la ruta y los filtros permitidos; no se copia $_GET completo.
$paginacionClavePagina = $paginacionClavePagina ?? 'pagina';
$paginacionClaveCantidad = $paginacionClaveCantidad ?? 'por_pagina';
$paginaActual = $paginacion['pagina'];
$urlPagina = static function (int $paginaDestino) use ($paginacionRuta, $paginacionParametros, $paginacion, $paginacionClavePagina, $paginacionClaveCantidad): string {
    return htmlspecialchars(sigenmuniUrlRuta($paginacionRuta, array_merge(
        $paginacionParametros,
        [$paginacionClavePagina => $paginaDestino, $paginacionClaveCantidad => $paginacion['por_pagina']]
    )), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
};
?>
<div class="paginacion">
    <p class="paginacion-resumen">
        Mostrando <?= $paginacion['desde'] ?>–<?= $paginacion['hasta'] ?> de <?= $paginacion['total'] ?>
        <?= htmlspecialchars($paginacionEntidad ?? 'registros', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
    </p>
    <?php if ($paginacion['paginas'] > 1): ?>
        <nav class="paginacion-controles" aria-label="Páginas del listado">
            <?php if ($paginaActual > 1): ?>
                <a href="<?= $urlPagina($paginaActual - 1) ?>" rel="prev">Anterior</a>
            <?php else: ?>
                <span class="paginacion-deshabilitada" aria-disabled="true">Anterior</span>
            <?php endif; ?>
            <?php $paginaPrevia = 0; ?>
            <?php foreach (sigenmuniNumerosPaginacion($paginacion) as $numeroPagina): ?>
                <?php if ($paginaPrevia > 0 && $numeroPagina > $paginaPrevia + 1): ?>
                    <span class="paginacion-puntos" aria-hidden="true">…</span>
                <?php endif; ?>
                <?php if ($numeroPagina === $paginaActual): ?>
                    <span class="paginacion-actual" aria-current="page" aria-label="Página <?= $numeroPagina ?>"><?= $numeroPagina ?></span>
                <?php else: ?>
                    <a href="<?= $urlPagina($numeroPagina) ?>" aria-label="Página <?= $numeroPagina ?>"><?= $numeroPagina ?></a>
                <?php endif; ?>
                <?php $paginaPrevia = $numeroPagina; ?>
            <?php endforeach; ?>
            <?php if ($paginaActual < $paginacion['paginas']): ?>
                <a href="<?= $urlPagina($paginaActual + 1) ?>" rel="next">Siguiente</a>
            <?php else: ?>
                <span class="paginacion-deshabilitada" aria-disabled="true">Siguiente</span>
            <?php endif; ?>
        </nav>
    <?php endif; ?>
</div>
