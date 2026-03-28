<?php
session_start();
require_once("conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$mensaje = "";

// Mensajes
if (isset($_GET['ok'])) {
    if ($_GET['ok'] == '1') {
        $mensaje = "Liquidación creada correctamente.";
    } elseif ($_GET['ok'] == '2') {
        $mensaje = "Liquidación procesada correctamente.";
    }
}

if (isset($_GET['msg'])) {
    switch ($_GET['msg']) {
        case 'anulada': $mensaje = "Liquidación anulada correctamente."; break;
        case 'reabierta': $mensaje = "Liquidación reabierta correctamente."; break;
        case 'error_estado': $mensaje = "Error al cambiar el estado."; break;
    }
}

// Filtros
$periodo = $_GET['periodo'] ?? '';
$tipo = $_GET['tipo_liquidacion'] ?? '';
$estado = $_GET['estado'] ?? '';

$sql = "SELECT * FROM liquidacion WHERE 1=1";

if ($periodo != '') {
    $sql .= " AND periodo = '" . $conexion->real_escape_string($periodo) . "'";
}
if ($tipo != '') {
    $sql .= " AND tipo_liquidacion = '" . $conexion->real_escape_string($tipo) . "'";
}
if ($estado != '') {
    $sql .= " AND estado = '" . $conexion->real_escape_string($estado) . "'";
}

$sql .= " ORDER BY id DESC";

$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Liquidaciones</title>

<style>
body { font-family: Arial; background:#f4f7fb; margin:0 }

.contenedor {
    max-width:1200px;
    margin:30px auto;
    background:white;
    padding:25px;
    border-radius:10px;
}

h2 { color:#0f766e }

.btn {
    padding:10px 15px;
    border:none;
    border-radius:6px;
    text-decoration:none;
    color:white;
    font-size:13px;
}

.btn-nuevo { background:#0f766e }
.btn-volver { background:#6b7280 }
.btn-ver { background:#2563eb }
.btn-procesar { background:#16a34a }
.btn-reabrir { background:#d97706 }
.btn-anular { background:#dc2626 }
.btn-disabled { background:#9ca3af }

.filtros {
    margin:20px 0;
}

input, select {
    padding:8px;
    margin-right:10px;
}

table {
    width:100%;
    border-collapse:collapse;
}

th, td {
    padding:10px;
    border-bottom:1px solid #ddd;
}

.badge {
    padding:5px 10px;
    border-radius:10px;
    font-size:12px;
}

.estado-borrador { background:#fef3c7 }
.estado-cerrada { background:#d1fae5 }
.estado-anulada { background:#fee2e2 }
</style>

</head>
<body>

<div class="contenedor">

<h2>Gestión de Liquidaciones</h2>

<a href="liquidacion_nueva.php" class="btn btn-nuevo">+ Nueva</a>
<a href="index.php" class="btn btn-volver">Volver</a>

<?php if ($mensaje) { ?>
    <p style="color:green"><?php echo $mensaje; ?></p>
<?php } ?>

<!-- FILTROS -->
<div class="filtros">
<form method="GET">
    <input type="month" name="periodo" value="<?php echo $periodo; ?>">

    <select name="tipo_liquidacion">
        <option value="">Tipo</option>
        <option value="MENSUAL">Mensual</option>
        <option value="AGUINALDO">Aguinaldo</option>
    </select>

    <select name="estado">
        <option value="">Estado</option>
        <option value="BORRADOR">Borrador</option>
        <option value="CERRADA">Cerrada</option>
        <option value="ANULADA">Anulada</option>
    </select>

    <button class="btn btn-ver">Filtrar</button>
</form>
</div>

<table>
<tr>
    <th>ID</th>
    <th>Tipo</th>
    <th>Periodo</th>
    <th>Fecha</th>
    <th>Estado</th>
    <th>Acciones</th>
</tr>

<?php while($fila = $resultado->fetch_assoc()) { ?>

<tr>
    <td><?php echo $fila['id']; ?></td>
    <td><?php echo $fila['tipo_liquidacion']; ?></td>
    <td><?php echo $fila['periodo']; ?></td>
    <td><?php echo $fila['fecha_liquidacion']; ?></td>

    <td>
        <?php
        $clase = "estado-borrador";
        if ($fila['estado']=="CERRADA") $clase="estado-cerrada";
        if ($fila['estado']=="ANULADA") $clase="estado-anulada";
        ?>
        <span class="badge <?php echo $clase; ?>">
            <?php echo $fila['estado']; ?>
        </span>
    </td>

    <td>
        <a href="liquidacion_ver.php?id=<?php echo $fila['id']; ?>" class="btn btn-ver">Ver</a>

        <?php if ($fila['estado'] == 'BORRADOR') { ?>

            <a href="liquidacion_procesar.php?id=<?php echo $fila['id']; ?>" class="btn btn-procesar">
                Procesar
            </a>

            <a href="liquidacion_estado.php?id=<?php echo $fila['id']; ?>&accion=anular" class="btn btn-anular">
                Anular
            </a>

        <?php } elseif ($fila['estado'] == 'CERRADA') { ?>

            <a href="liquidacion_estado.php?id=<?php echo $fila['id']; ?>&accion=reabrir" class="btn btn-reabrir">
                Reabrir
            </a>

            <a href="liquidacion_estado.php?id=<?php echo $fila['id']; ?>&accion=anular" class="btn btn-anular">
                Anular
            </a>

        <?php } else { ?>

            <span class="btn btn-disabled">Sin acciones</span>

        <?php } ?>

    </td>
</tr>

<?php } ?>

</table>

</div>

</body>
</html>