<?php
session_start();
require_once("conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$concepto_id = isset($_GET['concepto_id']) ? (int)$_GET['concepto_id'] : 0;
$nombre_concepto = "";

if ($concepto_id > 0) {
    $stmtConcepto = $conexion->prepare("SELECT nombre FROM concepto WHERE id = ?");
    if ($stmtConcepto) {
        $stmtConcepto->bind_param("i", $concepto_id);
        $stmtConcepto->execute();
        $resConcepto = $stmtConcepto->get_result();

        if ($resConcepto->num_rows > 0) {
            $datoConcepto = $resConcepto->fetch_assoc();
            $nombre_concepto = $datoConcepto['nombre'];
        }

        $stmtConcepto->close();
    }
}

$sql = "SELECT 
            cv.id,
            c.codigo,
            c.nombre AS concepto,
            ca.nombre AS categoria,
            e.nombre AS escalafon,
            cv.monto,
            cv.porcentaje,
            cv.fecha_desde,
            cv.fecha_hasta,
            cv.activo
        FROM concepto_valor cv
        INNER JOIN concepto c ON cv.concepto_id = c.id
        LEFT JOIN categoria ca ON cv.categoria_id = ca.id
        LEFT JOIN escalafon e ON cv.escalafon_id = e.id";

if ($concepto_id > 0) {
    $sql .= " WHERE cv.concepto_id = " . $concepto_id;
}

$sql .= " ORDER BY c.nombre ASC, cv.fecha_desde DESC";

$resultado = $conexion->query($sql);

if (!$resultado) {
    die("Error en la consulta: " . $conexion->error);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Valores de Conceptos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 20px;
        }

        .contenedor {
            max-width: 1300px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.08);
        }

        h2 {
            margin-bottom: 20px;
        }

        .acciones-top {
            margin-bottom: 15px;
        }

        .btn {
            display: inline-block;
            padding: 8px 14px;
            text-decoration: none;
            border-radius: 6px;
            color: white;
            font-size: 14px;
            margin-right: 5px;
            margin-bottom: 5px;
        }

        .btn-nuevo { background: #28a745; }
        .btn-volver { background: #6c757d; }
        .btn-editar { background: #007bff; }
        .btn-estado { background: #fd7e14; }
        .btn-todos { background: #17a2b8; }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
            font-size: 14px;
        }

        table th {
            background: #343a40;
            color: white;
        }

        .activo {
            color: green;
            font-weight: bold;
        }

        .inactivo {
            color: red;
            font-weight: bold;
        }

        .mensaje {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 6px;
            background: #d4edda;
            color: #155724;
        }
    </style>
</head>
<body>

<div class="contenedor">
    <h2>
        Gestión de Valores de Conceptos
        <?php if (!empty($nombre_concepto)) { ?>
            - <?php echo htmlspecialchars($nombre_concepto); ?>
        <?php } ?>
    </h2>

    <?php if (isset($_GET['ok'])) { ?>
        <div class="mensaje">Operación realizada correctamente.</div>
    <?php } ?>

    <div class="acciones-top">
        <a href="concepto_valor_nuevo.php<?php echo ($concepto_id > 0) ? '?concepto_id=' . $concepto_id : ''; ?>" class="btn btn-nuevo">
            + Nuevo Valor
        </a>

        <?php if ($concepto_id > 0) { ?>
            <a href="concepto_valores.php" class="btn btn-todos">Ver Todos</a>
        <?php } ?>

        <a href="conceptos.php" class="btn btn-volver">Volver a Conceptos</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Código</th>
                <th>Concepto</th>
                <th>Categoría</th>
                <th>Escalafón</th>
                <th>Monto</th>
                <th>Porcentaje</th>
                <th>Fecha Desde</th>
                <th>Fecha Hasta</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($resultado->num_rows > 0) { ?>
            <?php while ($fila = $resultado->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $fila['id']; ?></td>
                    <td><?php echo htmlspecialchars($fila['codigo']); ?></td>
                    <td><?php echo htmlspecialchars($fila['concepto']); ?></td>
                    <td><?php echo htmlspecialchars($fila['categoria'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($fila['escalafon'] ?? '-'); ?></td>
                    <td><?php echo number_format((float)$fila['monto'], 2, ',', '.'); ?></td>
                    <td><?php echo number_format((float)$fila['porcentaje'], 2, ',', '.') . '%'; ?></td>
                    <td><?php echo !empty($fila['fecha_desde']) ? date("d/m/Y", strtotime($fila['fecha_desde'])) : '-'; ?></td>
                    <td><?php echo !empty($fila['fecha_hasta']) ? date("d/m/Y", strtotime($fila['fecha_hasta'])) : '-'; ?></td>
                    <td>
                        <?php if ($fila['activo'] == 1) { ?>
                            <span class="activo">Activo</span>
                        <?php } else { ?>
                            <span class="inactivo">Inactivo</span>
                        <?php } ?>
                    </td>
                    <td>
                        <a href="concepto_valor_editar.php?id=<?php echo $fila['id']; ?>&concepto_id=<?php echo $concepto_id; ?>" class="btn btn-editar">Editar</a>
                        <a href="concepto_valor_estado.php?id=<?php echo $fila['id']; ?>&concepto_id=<?php echo $concepto_id; ?>" class="btn btn-estado">
                            <?php echo ($fila['activo'] == 1) ? 'Inactivar' : 'Activar'; ?>
                        </a>
                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>
                <td colspan="11">No hay valores cargados.</td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>