<?php
session_start();
require_once("conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: empleados.php");
    exit();
}

$stmt = $conexion->prepare("
    SELECT 
        e.*,
        i.nombre AS institucion,
        o.nombre AS oficina,
        s.nombre AS situacion,
        es.nombre AS escalafon,
        c.nombre AS categoria
    FROM empleado e
    INNER JOIN institucion i ON e.institucion_id = i.id
    INNER JOIN oficina o ON e.oficina_id = o.id
    INNER JOIN situacion s ON e.situacion_id = s.id
    INNER JOIN escalafon es ON e.escalafon_id = es.id
    INNER JOIN categoria c ON e.categoria_id = c.id
    WHERE e.id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$empleado = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$empleado) {
    die("Empleado no encontrado.");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Detalle del Empleado</title>
<style>
body{font-family:Arial;background:#f4f7fb;margin:0}
.contenedor{max-width:950px;margin:25px auto;background:white;padding:25px;border-radius:12px;box-shadow:0 8px 20px rgba(0,0,0,.08)}
.grid{display:grid;grid-template-columns:repeat(2,1fr);gap:14px}
.item{background:#f9fafb;padding:12px;border-radius:8px;border:1px solid #e5e7eb}
.titulo{font-size:12px;color:#666;margin-bottom:5px}
.valor{font-weight:bold}
.acciones{margin-top:20px}
.btn{background:green;color:white;padding:10px 14px;border:none;border-radius:8px;text-decoration:none}
.btn-sec{background:#333}
</style>
</head>
<body>
<div class="contenedor">
    <h2>Ficha del Empleado</h2>

    <div class="grid">
        <div class="item"><div class="titulo">Legajo</div><div class="valor"><?php echo htmlspecialchars($empleado['nro_legajo']); ?></div></div>
        <div class="item"><div class="titulo">Apellido y Nombre</div><div class="valor"><?php echo htmlspecialchars($empleado['apellido'] . ', ' . $empleado['nombre']); ?></div></div>
        <div class="item"><div class="titulo">DNI</div><div class="valor"><?php echo htmlspecialchars($empleado['dni']); ?></div></div>
        <div class="item"><div class="titulo">CUIL</div><div class="valor"><?php echo htmlspecialchars($empleado['cuil']); ?></div></div>
        <div class="item"><div class="titulo">Fecha Alta</div><div class="valor"><?php echo htmlspecialchars($empleado['fecha_alta']); ?></div></div>
        <div class="item"><div class="titulo">Fecha Baja</div><div class="valor"><?php echo htmlspecialchars($empleado['fecha_baja'] ?: '-'); ?></div></div>
        <div class="item"><div class="titulo">Teléfono</div><div class="valor"><?php echo htmlspecialchars($empleado['telefono'] ?: '-'); ?></div></div>
        <div class="item"><div class="titulo">Email</div><div class="valor"><?php echo htmlspecialchars($empleado['email'] ?: '-'); ?></div></div>
        <div class="item"><div class="titulo">Domicilio</div><div class="valor"><?php echo htmlspecialchars($empleado['domicilio'] ?: '-'); ?></div></div>
        <div class="item"><div class="titulo">Institución</div><div class="valor"><?php echo htmlspecialchars($empleado['institucion']); ?></div></div>
        <div class="item"><div class="titulo">Oficina</div><div class="valor"><?php echo htmlspecialchars($empleado['oficina']); ?></div></div>
        <div class="item"><div class="titulo">Situación</div><div class="valor"><?php echo htmlspecialchars($empleado['situacion']); ?></div></div>
        <div class="item"><div class="titulo">Escalafón</div><div class="valor"><?php echo htmlspecialchars($empleado['escalafon']); ?></div></div>
        <div class="item"><div class="titulo">Categoría</div><div class="valor"><?php echo htmlspecialchars($empleado['categoria']); ?></div></div>
        <div class="item"><div class="titulo">Estado</div><div class="valor"><?php echo $empleado['activo'] ? 'Activo' : 'Inactivo'; ?></div></div>
        <div class="item" style="grid-column:1/-1;"><div class="titulo">Observaciones</div><div class="valor"><?php echo nl2br(htmlspecialchars($empleado['observaciones'] ?: '-')); ?></div></div>
    </div>

    <div class="acciones">
        <a href="empleado_editar.php?id=<?php echo $empleado['id']; ?>" class="btn">Editar</a>
        <a href="liquidacion.php?empleado_id=<?php echo $empleado['id']; ?>" class="btn">Liquidar</a>
        <a href="empleados.php" class="btn btn-sec">Volver</a>
    </div>
</div>
</body>
</html>