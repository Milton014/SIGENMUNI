<?php
session_start();
require_once("conexion.php");
require_once("seguridad.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

verificarSesion();
verificarPermisoModulo("liquidacion_nueva.php");

$mensaje = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $tipo = trim($_POST['tipo_liquidacion'] ?? '');
    $periodo = trim($_POST['periodo'] ?? '');
    $fecha = trim($_POST['fecha_liquidacion'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');

    if ($tipo == "" || $periodo == "" || $fecha == "") {
        $error = "Complete todos los campos obligatorios.";
    } else {

        $stmt = $conexion->prepare("
            INSERT INTO liquidacion 
            (tipo_liquidacion, periodo, fecha_liquidacion, descripcion)
            VALUES (?, ?, ?, ?)
        ");

        $stmt->bind_param("ssss", $tipo, $periodo, $fecha, $descripcion);

        if ($stmt->execute()) {
            header("Location: liquidacion.php?ok=1");
            exit();
        } else {
            $error = "Error al guardar la liquidación.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Nueva Liquidación</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
* {
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

body {
    background: #f4f7fb;
    margin: 0;
    padding: 20px;
}

.contenedor {
    width: 100%;
    max-width: 600px;
    margin: 40px auto;
    background: white;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 8px 20px rgba(0,0,0,.08);
}

h2 {
    color: #0f766e;
    margin-top: 0;
    margin-bottom: 20px;
}

label {
    font-weight: bold;
    display: block;
    margin-top: 10px;
    color: #333;
}

input, 
select, 
textarea {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    border-radius: 6px;
    border: 1px solid #ccc;
    outline: none;
    transition: .2s;
    font-size: 14px;
}

input:focus,
select:focus,
textarea:focus {
    border-color: #14b8a6;
    box-shadow: 0 0 0 3px rgba(20,184,166,.15);
}

textarea {
    resize: vertical;
    min-height: 90px;
}

.botones {
    margin-top: 20px;
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.btn {
    padding: 10px 15px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    color: white;
    text-align: center;
    font-size: 14px;
}

.btn-guardar {
    background: #0f766e;
}

.btn-guardar:hover {
    background: #115e59;
}

.btn-volver {
    background: #6b7280;
    text-decoration: none;
    display: inline-block;
}

.btn-volver:hover {
    background: #4b5563;
}

.mensaje-error {
    background: #fee2e2;
    color: #991b1b;
    padding: 12px 14px;
    border-radius: 8px;
    margin-bottom: 15px;
    border: 1px solid #fecaca;
    font-weight: bold;
    word-break: break-word;
}

.input-error {
    border-color: #dc2626 !important;
    background: #fff1f2 !important;
    box-shadow: 0 0 0 3px rgba(220,38,38,.12) !important;
}

@media (max-width: 768px) {

    body {
        padding: 10px;
    }

    .contenedor {
        margin: 10px auto;
        padding: 15px;
    }

    h2 {
        text-align: center;
        font-size: 22px;
        line-height: 1.3;
    }

    input,
    select,
    textarea {
        min-height: 44px;
        font-size: 16px;
    }

    .botones {
        flex-direction: column;
    }

    .botones .btn,
    .botones button {
        width: 100%;
        padding: 12px;
    }
}
</style>

</head>
<body>

<div class="contenedor">

<h2>Nueva Liquidación</h2>

<div id="alertaLiquidacion" class="mensaje-error" style="display:none;"></div>

<?php if ($error) { ?>
    <div class="mensaje-error"><?php echo htmlspecialchars($error); ?></div>
<?php } ?>

<form method="POST" id="formLiquidacion" novalidate>

    <label for="tipo_liquidacion">Tipo de Liquidación *</label>
    <select name="tipo_liquidacion" id="tipo_liquidacion">
        <option value="">Seleccione</option>
        <option value="MENSUAL" <?php echo (($_POST['tipo_liquidacion'] ?? '') === 'MENSUAL') ? 'selected' : ''; ?>>Mensual</option>
        <option value="AGUINALDO" <?php echo (($_POST['tipo_liquidacion'] ?? '') === 'AGUINALDO') ? 'selected' : ''; ?>>Aguinaldo</option>
        <option value="AJUSTE" <?php echo (($_POST['tipo_liquidacion'] ?? '') === 'AJUSTE') ? 'selected' : ''; ?>>Ajuste</option>
        <option value="OTRA" <?php echo (($_POST['tipo_liquidacion'] ?? '') === 'OTRA') ? 'selected' : ''; ?>>Otra</option>
    </select>

    <label for="periodo">Período (YYYY-MM) *</label>
    <input 
        type="month" 
        name="periodo" 
        id="periodo"
        value="<?php echo htmlspecialchars($_POST['periodo'] ?? ''); ?>"
    >

    <label for="fecha_liquidacion">Fecha de Liquidación *</label>
    <input 
        type="date" 
        name="fecha_liquidacion" 
        id="fecha_liquidacion"
        value="<?php echo htmlspecialchars($_POST['fecha_liquidacion'] ?? date('Y-m-d')); ?>"
    >

    <label for="descripcion">Descripción</label>
    <textarea name="descripcion" id="descripcion" rows="3"><?php echo htmlspecialchars($_POST['descripcion'] ?? ''); ?></textarea>

    <div class="botones">
        <button type="submit" class="btn btn-guardar">Guardar</button>
        <a href="liquidacion.php" class="btn btn-volver">Volver</a>
    </div>

</form>

</div>

<script>
document.getElementById("formLiquidacion").addEventListener("submit", function(e) {

    const alerta = document.getElementById("alertaLiquidacion");

    const campos = [
        "tipo_liquidacion",
        "periodo",
        "fecha_liquidacion"
    ];

    campos.forEach(function(id) {
        document.getElementById(id).classList.remove("input-error");
    });

    alerta.style.display = "none";
    alerta.innerHTML = "";

    function mostrarError(mensaje, id) {
        e.preventDefault();

        const campo = document.getElementById(id);

        alerta.innerHTML = mensaje;
        alerta.style.display = "block";

        campo.classList.add("input-error");
        campo.focus();

        window.scrollTo({
            top: 0,
            behavior: "smooth"
        });
    }

    const tipo = document.getElementById("tipo_liquidacion").value;
    const periodo = document.getElementById("periodo").value;
    const fecha = document.getElementById("fecha_liquidacion").value;

    if (tipo === "") {
        mostrarError("Debe seleccionar el tipo de liquidación.", "tipo_liquidacion");
        return;
    }

    if (periodo === "") {
        mostrarError("Debe seleccionar el período de liquidación.", "periodo");
        return;
    }

    if (fecha === "") {
        mostrarError("Debe seleccionar la fecha de liquidación.", "fecha_liquidacion");
        return;
    }

});
</script>

</body>
</html>