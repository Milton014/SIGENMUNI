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
.mensaje{
    background:#fee2e2;
    border:2px solid #dc2626;
    color:#991b1b;
    padding:30px;
    border-radius:16px;
    max-width:520px;
    width:100%;
    text-align:center;
    box-shadow:0 8px 20px rgba(0,0,0,.12);
}
.mensaje h2{margin-top:0;font-size:30px;}
.mensaje p{font-size:18px;margin:18px 0;line-height:1.5;}
.acciones{display:flex;gap:10px;justify-content:center;flex-wrap:wrap;}
.btn{
    display:inline-block;
    margin-top:15px;
    background:#dc2626;
    color:white;
    padding:12px 20px;
    border-radius:10px;
    text-decoration:none;
    font-weight:bold;
}
.btn-sec{background:#1f2937;}
.btn:hover{opacity:.9;}
</style>
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
