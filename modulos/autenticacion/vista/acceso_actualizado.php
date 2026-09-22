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
<style>
*{box-sizing:border-box;}
body{
    font-family:Arial,sans-serif;
    background:#f4f7fb;
    display:flex;
    justify-content:center;
    align-items:center;
    min-height:100vh;
    margin:0;
    padding:20px;
}
.card{
    background:white;
    padding:30px;
    border-radius:16px;
    box-shadow:0 8px 20px rgba(0,0,0,.12);
    text-align:center;
    max-width:460px;
    width:100%;
}
h2{color:#0f766e;margin-top:0;}
p{font-size:16px;color:#374151;line-height:1.5;}
a{
    display:inline-block;
    margin-top:15px;
    background:#0f766e;
    color:white;
    padding:12px 18px;
    border-radius:10px;
    text-decoration:none;
    font-weight:bold;
}
a:hover{background:#115e59;}
</style>
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
