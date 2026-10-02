<?php

/*
|--------------------------------------------------------------------------
| VISTA - RECUPERAR ACCESO
|--------------------------------------------------------------------------
|
| La validación, generación del código y envío de correo se resuelven en
| AutenticacionControlador y AutenticacionModelo.
|
|--------------------------------------------------------------------------
*/

?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Recuperar Acceso - SIGENMUNI</title>

<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/autenticacion/recuperar.css'), ENT_QUOTES, 'UTF-8') ?>">
</head>

<body>

<div class="contenedor">

    <div class="card">

        <div class="logo">
            <img src="<?php echo htmlspecialchars($autenticacionUrlEscudo, ENT_QUOTES, 'UTF-8'); ?>" class="logo-img" alt="Escudo">
            <h1>SIGENMUNI</h1>
            <p class="subtitulo">Recuperación segura de acceso</p>
        </div>

        <div class="titulo">Recuperar acceso de ADMIN</div>

        <p class="descripcion">
            Esta opción está disponible solo para usuarios con rol ADMIN.
            Se enviará un código de verificación al correo registrado.
        </p>

        <?php if (!empty($mensaje)): ?>
            <div class="mensaje-error" data-estilo="recuperar-1">
                <?php echo htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <div id="alertaRecuperar" class="mensaje-error"></div>

        <form action="<?php echo htmlspecialchars($autenticacionUrlRecuperar, ENT_QUOTES, 'UTF-8'); ?>" method="POST" id="formRecuperar" novalidate>

            <input
                type="hidden"
                name="_csrf"
                value="<?php echo htmlspecialchars($autenticacionCsrfToken, ENT_QUOTES, 'UTF-8'); ?>"
            >

            <div class="grupo">
                <label for="correo">Correo registrado</label>
                <input
                    type="email"
                    name="correo"
                    id="correo"
                    value="<?php echo htmlspecialchars($correoIngresado ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                >
            </div>

            <div class="grupo">
                <label for="dni">DNI del administrador</label>
                <input 
                    type="text"
                    name="dni"
                    id="dni"
                    minlength="7"
                    maxlength="8"
                    inputmode="numeric"
                    pattern="[0-9]{7,8}"
                    data-event-input="recuperar-1"
                    value="<?php echo htmlspecialchars($dniIngresado ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                >
                <div class="ayuda">Ingrese solo números, sin puntos ni espacios.</div>
            </div>

            <button type="submit" class="btn">Enviar código</button>

        </form>

        <div class="volver">
            <a href="<?php echo htmlspecialchars($autenticacionUrlLogin, ENT_QUOTES, 'UTF-8'); ?>">← Volver al login</a>
        </div>

    </div>

    <div class="pie">
        Municipalidad de Fortín Lugones
    </div>

</div>

<script src="<?= htmlspecialchars(sigenmuniUrlAsset('js/autenticacion/recuperar.js'), ENT_QUOTES, 'UTF-8') ?>"></script>

</body>
</html>