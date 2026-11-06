<?php
require_once __DIR__ . '/../core/Url.php';
$paginacionClavePagina = $paginacionClavePagina ?? 'pagina';
$paginacionClaveCantidad = $paginacionClaveCantidad ?? 'por_pagina';
?>
<form class="tamano-reporte" method="GET" action="<?= htmlspecialchars(sigenmuniUrlEntrada(), ENT_QUOTES, 'UTF-8') ?>">
    <input type="hidden" name="r" value="<?= htmlspecialchars($paginacionRuta, ENT_QUOTES, 'UTF-8') ?>">
    <?php foreach ($paginacionParametros as $clave => $valor): ?>
        <?php if ($clave !== $paginacionClavePagina && $clave !== $paginacionClaveCantidad): ?>
            <input type="hidden" name="<?= htmlspecialchars($clave, ENT_QUOTES, 'UTF-8') ?>" value="<?= htmlspecialchars((string)$valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
        <?php endif; ?>
    <?php endforeach; ?>
    <label for="cantidad_reporte">Por página</label>
    <select id="cantidad_reporte" name="<?= htmlspecialchars($paginacionClaveCantidad, ENT_QUOTES, 'UTF-8') ?>" data-cantidad-reporte>
        <?php foreach ([10, 25, 50] as $cantidad): ?>
            <option value="<?= $cantidad ?>" <?= $paginacion['por_pagina'] === $cantidad ? 'selected' : '' ?>><?= $cantidad ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit">Aplicar</button>
</form>
<?php require __DIR__ . '/paginacion.php'; ?>
