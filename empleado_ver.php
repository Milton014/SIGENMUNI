<?php
session_start();

require_once("conexion.php");
require_once("seguridad.php");

verificarSesion();
verificarPermisoModulo("empleados.php");

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
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Detalle del Empleado - SIGENMUNI</title>

<style>
* {
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    background: #f4f7fb;
    margin: 0;
    color: #1f2937;
}

.header {
    background: linear-gradient(135deg, #0f766e, #14b8a6);
    color: white;
    padding: 22px 30px;
    box-shadow: 0 4px 14px rgba(0,0,0,.10);
}

.header h1 {
    margin: 0;
    font-size: 30px;
}

.header p {
    margin-top: 6px;
    font-size: 14px;
}

.contenedor {
    width: 95%;
    max-width: 1050px;
    margin: 30px auto;
}

.panel {
    background: white;
    padding: 28px;
    border-radius: 18px;
    box-shadow: 0 8px 20px rgba(0,0,0,.08);
}

h2 {
    margin-top: 0;
    margin-bottom: 22px;
    color: #0f766e;
}

.grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 14px;
}

.item {
    background: #f9fafb;
    padding: 14px;
    border-radius: 10px;
    border: 1px solid #e5e7eb;
}

.titulo {
    font-size: 12px;
    color: #6b7280;
    margin-bottom: 5px;
    font-weight: bold;
    text-transform: uppercase;
}

.valor {
    font-weight: bold;
    word-break: break-word;
}

.estado-activo {
    color: #166534;
}

.estado-inactivo {
    color: #991b1b;
}

.acciones {
    margin-top: 25px;
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.btn {
    background: #0f766e;
    color: white;
    padding: 11px 16px;
    border: none;
    border-radius: 10px;
    text-decoration: none;
    cursor: pointer;
    display: inline-block;
    font-weight: bold;
    font-size: 14px;
    transition: 0.2s;
}

.btn:hover {
    opacity: 0.92;
    transform: translateY(-1px);
}

.btn-liquidar {
    background: #16a34a;
}

.btn-sec {
    background: #1f2937;
}

@media (max-width: 768px) {
    .header {
        padding: 20px;
        text-align: center;
    }

    .header h1 {
        font-size: 24px;
    }

    .header p {
        font-size: 13px;
    }

    .contenedor {
        width: 94%;
        margin: 20px auto;
    }

    .panel {
        padding: 20px;
        border-radius: 16px;
    }

    h2 {
        font-size: 24px;
        text-align: center;
    }

    .grid {
        grid-template-columns: 1fr;
    }

    .acciones {
        flex-direction: column;
        align-items: stretch;
    }

    .btn {
        width: 100%;
        text-align: center;
    }
}

@media (max-width: 480px) {
    .header h1 {
        font-size: 22px;
    }

    h2 {
        font-size: 22px;
    }

    .panel {
        padding: 18px;
    }

    .btn {
        font-size: 13px;
        padding: 10px;
    }
}
</style>
</head>

<body>

<div class="header">
    <h1>SIGENMUNI</h1>
    <p>Ficha del Empleado Municipal</p>
</div>

<div class="contenedor">

    <div class="panel">

        <h2>Ficha del Empleado</h2>

        <div class="grid">

            <div class="item">
                <div class="titulo">Legajo</div>
                <div class="valor"><?php echo htmlspecialchars($empleado['nro_legajo']); ?></div>
            </div>

            <div class="item">
                <div class="titulo">Apellido y Nombre</div>
                <div class="valor"><?php echo htmlspecialchars($empleado['apellido'] . ', ' . $empleado['nombre']); ?></div>
            </div>

            <div class="item">
                <div class="titulo">DNI</div>
                <div class="valor"><?php echo htmlspecialchars($empleado['dni']); ?></div>
            </div>

            <div class="item">
                <div class="titulo">CUIL</div>
                <div class="valor"><?php echo htmlspecialchars($empleado['cuil']); ?></div>
            </div>

            <div class="item">
                <div class="titulo">Fecha Alta</div>
                <div class="valor"><?php echo htmlspecialchars($empleado['fecha_alta']); ?></div>
            </div>

            <div class="item">
                <div class="titulo">Fecha Baja</div>
                <div class="valor"><?php echo htmlspecialchars($empleado['fecha_baja'] ?: '-'); ?></div>
            </div>

            <div class="item">
                <div class="titulo">Teléfono</div>
                <div class="valor"><?php echo htmlspecialchars($empleado['telefono'] ?: '-'); ?></div>
            </div>

            <div class="item">
                <div class="titulo">Email</div>
                <div class="valor"><?php echo htmlspecialchars($empleado['email'] ?: '-'); ?></div>
            </div>

            <div class="item">
                <div class="titulo">Domicilio</div>
                <div class="valor"><?php echo htmlspecialchars($empleado['domicilio'] ?: '-'); ?></div>
            </div>

            <div class="item">
                <div class="titulo">Institución</div>
                <div class="valor"><?php echo htmlspecialchars($empleado['institucion']); ?></div>
            </div>

            <div class="item">
                <div class="titulo">Oficina</div>
                <div class="valor"><?php echo htmlspecialchars($empleado['oficina']); ?></div>
            </div>

            <div class="item">
                <div class="titulo">Situación</div>
                <div class="valor"><?php echo htmlspecialchars($empleado['situacion']); ?></div>
            </div>

            <div class="item">
                <div class="titulo">Escalafón</div>
                <div class="valor"><?php echo htmlspecialchars($empleado['escalafon']); ?></div>
            </div>

            <div class="item">
                <div class="titulo">Categoría</div>
                <div class="valor"><?php echo htmlspecialchars($empleado['categoria']); ?></div>
            </div>

            <div class="item">
                <div class="titulo">Estado</div>

                <div class="valor <?php echo ((int)$empleado['activo'] === 1) ? 'estado-activo' : 'estado-inactivo'; ?>">
                    <?php echo ((int)$empleado['activo'] === 1) ? 'Activo' : 'Inactivo'; ?>
                </div>
            </div>

            <div class="item" style="grid-column:1/-1;">
                <div class="titulo">Observaciones</div>
                <div class="valor">
                    <?php echo nl2br(htmlspecialchars($empleado['observaciones'] ?: '-')); ?>
                </div>
            </div>

        </div>

        <div class="acciones">

            <a href="empleado_editar.php?id=<?php echo $empleado['id']; ?>" class="btn">
                Editar
            </a>

            <a href="liquidacion.php?empleado_id=<?php echo $empleado['id']; ?>" class="btn btn-liquidar">
                Liquidar
            </a>

            <a href="empleados.php" class="btn btn-sec">
                Volver
            </a>

        </div>

    </div>

</div>

</body>
</html>