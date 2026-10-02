<?php
/*
|--------------------------------------------------------------------------
| VISTA - ERROR DE RECUPERACIÓN
|--------------------------------------------------------------------------
*/
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Error - SIGENMUNI</title>
<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/autenticacion/error.css'), ENT_QUOTES, 'UTF-8') ?>">
</head>
<body>
<div class="mensaje">
    <h2>Error</h2>
    <p><?php echo htmlspecialchars($mensaje, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></p>
    <div class="acciones">
        <a href="<?php echo htmlspecialchars($autenticacionUrlVolver, ENT_QUOTES, 'UTF-8'); ?>" class="btn">
            Volver a recuperar acceso
        </a>
        <a href="<?php echo htmlspecialchars($autenticacionUrlLogin, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-sec">
            Ir al login
        </a>
    </div>
</div>
</body>
</html>
