<?php
/*
|--------------------------------------------------------------------------
| VISTA - ACCESO ACTUALIZADO
|--------------------------------------------------------------------------
*/
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Acceso actualizado - SIGENMUNI</title>
<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/autenticacion/acceso_actualizado.css'), ENT_QUOTES, 'UTF-8') ?>">
</head>
<body>
<div class="card">
    <h2>Datos actualizados correctamente</h2>
    <p>La nueva contraseña cumple con los requisitos de seguridad.</p>
    <p>Ya podés iniciar sesión con tu nuevo usuario y contraseña.</p>
    <a href="<?php echo htmlspecialchars($autenticacionUrlLogin, ENT_QUOTES, 'UTF-8'); ?>">
        Volver al login
    </a>
</div>
</body>
</html>
