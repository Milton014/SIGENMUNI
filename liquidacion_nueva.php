<?php
session_start();
require_once("conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$mensaje = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $tipo = trim($_POST['tipo_liquidacion']);
    $periodo = trim($_POST['periodo']);
    $fecha = $_POST['fecha_liquidacion'];
    $descripcion = trim($_POST['descripcion']);

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

<style>
body {
    font-family: Arial;
    background: #f4f7fb;
    margin: 0;
}

.contenedor {
    max-width: 600px;
    margin: 40px auto;
    background: white;
    padding: 25px;
    border-radius: 10px;
}

h2 {
    color: #0f766e;
    margin-bottom: 20px;
}

label {
    font-weight: bold;
    display: block;
    margin-top: 10px;
}

input, select, textarea {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    border-radius: 6px;
    border: 1px solid #ccc;
}

textarea {
    resize: vertical;
}

.botones {
    margin-top: 20px;
    display: flex;
    gap: 10px;
}

.btn {
    padding: 10px 15px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    color: white;
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
    text-align: center;
}

.btn-volver:hover {
    background: #4b5563;
}

.mensaje-error {
    background: #fee2e2;
    color: #991b1b;
    padding: 10px;
    border-radius: 6px;
    margin-bottom: 15px;
}
</style>

</head>
<body>

<div class="contenedor">

<h2>Nueva Liquidación</h2>

<?php if ($error) { ?>
    <div class="mensaje-error"><?php echo $error; ?></div>
<?php } ?>

<form method="POST">

    <label>Tipo de Liquidación *</label>
    <select name="tipo_liquidacion" required>
        <option value="">Seleccione</option>
        <option value="MENSUAL">Mensual</option>
        <option value="AGUINALDO">Aguinaldo</option>
        <option value="AJUSTE">Ajuste</option>
        <option value="OTRA">Otra</option>
    </select>

    <label>Período (YYYY-MM) *</label>
    <input type="month" name="periodo" required>

    <label>Fecha de Liquidación *</label>
    <input type="date" name="fecha_liquidacion" value="<?php echo date('Y-m-d'); ?>" required>

    <label>Descripción</label>
    <textarea name="descripcion" rows="3"></textarea>

    <div class="botones">
        <button type="submit" class="btn btn-guardar">Guardar</button>
        <a href="liquidacion.php" class="btn btn-volver">Volver</a>
    </div>

</form>

</div>

</body>
</html>