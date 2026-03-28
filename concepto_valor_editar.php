<?php
session_start();
require_once("conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    die("ID no válido.");
}

$conceptosQuery = $conexion->query("
    SELECT id, codigo, nombre, forma_calculo
    FROM concepto
    WHERE activo = 1
    ORDER BY nombre ASC
");

$categorias = $conexion->query("SELECT id, nombre FROM categoria ORDER BY nombre ASC");
$escalafones = $conexion->query("SELECT id, nombre FROM escalafon ORDER BY nombre ASC");

$formasConcepto = [];
$opcionesConcepto = [];

while ($c = $conceptosQuery->fetch_assoc()) {
    $formasConcepto[$c['id']] = $c['forma_calculo'];
    $opcionesConcepto[] = $c;
}

$stmt = $conexion->prepare("
    SELECT id, concepto_id, categoria_id, escalafon_id, monto, porcentaje, fecha_desde, fecha_hasta, activo
    FROM concepto_valor
    WHERE id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows == 0) {
    die("Registro no encontrado.");
}

$fila = $resultado->fetch_assoc();
$concepto_id_actual = (int)$fila['concepto_id'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Valor de Concepto</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            margin: 0;
            background: #f4f6f9;
            padding: 20px;
        }

        .contenedor {
            max-width: 750px;
            margin: auto;
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.08);
        }

        h2 {
            margin-top: 0;
            color: #2c3e50;
        }

        .grupo {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
        }

        input, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 14px;
        }

        .acciones {
            margin-top: 20px;
        }

        .btn {
            display: inline-block;
            padding: 10px 16px;
            text-decoration: none;
            border-radius: 6px;
            color: white;
            border: none;
            cursor: pointer;
            margin-right: 8px;
            font-size: 14px;
        }

        .btn-guardar { background: #28a745; }
        .btn-cancelar { background: #6c757d; }

        .ayuda {
            margin-top: 6px;
            font-size: 13px;
            color: #6c757d;
            line-height: 1.4;
        }

        .deshabilitado {
            background: #e9ecef !important;
            color: #6c757d;
        }
    </style>

    <script>
        const formasConcepto = <?php echo json_encode($formasConcepto, JSON_UNESCAPED_UNICODE); ?>;

        function bloquearSelect(select, limpiar = true) {
            select.disabled = true;
            select.classList.add('deshabilitado');
            if (limpiar) {
                select.value = '';
            }
        }

        function habilitarSelect(select) {
            select.disabled = false;
            select.classList.remove('deshabilitado');
        }

        function bloquearInput(input, valor = '') {
            input.readOnly = true;
            input.classList.add('deshabilitado');
            if (valor !== null) {
                input.value = valor;
            }
        }

        function habilitarInput(input) {
            input.readOnly = false;
            input.classList.remove('deshabilitado');
        }

        function actualizarFormulario() {
            const conceptoId = document.getElementById('concepto_id').value;
            const forma = formasConcepto[conceptoId] || '';

            const categoria = document.getElementById('categoria_id');
            const escalafon = document.getElementById('escalafon_id');
            const monto = document.getElementById('monto');
            const porcentaje = document.getElementById('porcentaje');
            const ayuda = document.getElementById('mensaje_ayuda');

            habilitarSelect(categoria);
            habilitarSelect(escalafon);
            bloquearInput(monto, '0.00');
            bloquearInput(porcentaje, '0.00');

            ayuda.innerHTML = '';

            if (forma === 'TABLA_CATEGORIA') {
                habilitarSelect(categoria);
                bloquearSelect(escalafon, true);
                habilitarInput(monto);
                bloquearInput(porcentaje, '0.00');

                ayuda.innerHTML = 'Este concepto se carga por categoría. Seleccione la categoría e ingrese el monto correspondiente a la escala salarial.';
            } else if (forma === 'PORCENTAJE') {
                habilitarSelect(categoria);
                habilitarSelect(escalafon);
                bloquearInput(monto, '0.00');
                habilitarInput(porcentaje);

                ayuda.innerHTML = 'Este concepto se carga por porcentaje. Complete el porcentaje y, si corresponde, asócielo a categoría o escalafón.';
            } else if (forma === 'FIJO') {
                habilitarSelect(categoria);
                habilitarSelect(escalafon);
                habilitarInput(monto);
                bloquearInput(porcentaje, '0.00');

                ayuda.innerHTML = 'Este concepto usa un valor monetario fijo.';
            } else if (forma === 'MANUAL' || forma === 'FORMULA') {
                bloquearSelect(categoria, false);
                bloquearSelect(escalafon, false);
                bloquearInput(monto, '0.00');
                bloquearInput(porcentaje, '0.00');

                ayuda.innerHTML = 'Este concepto no admite carga directa de valores desde esta pantalla.';
            } else {
                bloquearInput(monto, '0.00');
                bloquearInput(porcentaje, '0.00');
                ayuda.innerHTML = 'Seleccione un concepto para continuar.';
            }
        }

        window.onload = actualizarFormulario;
    </script>
</head>
<body>

<div class="contenedor">
    <h2>Editar Valor de Concepto</h2>

    <form action="concepto_valor_actualizar.php" method="POST">
        <input type="hidden" name="id" value="<?php echo $fila['id']; ?>">
        <input type="hidden" name="concepto_id_original" value="<?php echo $concepto_id_actual; ?>">

        <div class="grupo">
            <label for="concepto_id">Concepto</label>
            <select name="concepto_id" id="concepto_id" onchange="actualizarFormulario()" required>
                <option value="">Seleccione</option>
                <?php foreach ($opcionesConcepto as $c) { ?>
                    <option value="<?php echo $c['id']; ?>" <?php echo ($fila['concepto_id'] == $c['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($c['codigo'] . ' - ' . $c['nombre']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="grupo">
            <label for="categoria_id">Categoría</label>
            <select name="categoria_id" id="categoria_id">
                <option value="">Seleccione</option>
                <?php while ($cat = $categorias->fetch_assoc()) { ?>
                    <option value="<?php echo $cat['id']; ?>" <?php echo ($fila['categoria_id'] == $cat['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['nombre']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="grupo">
            <label for="escalafon_id">Escalafón</label>
            <select name="escalafon_id" id="escalafon_id">
                <option value="">Seleccione</option>
                <?php while ($esc = $escalafones->fetch_assoc()) { ?>
                    <option value="<?php echo $esc['id']; ?>" <?php echo ($fila['escalafon_id'] == $esc['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($esc['nombre']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="grupo">
            <label for="monto">Monto</label>
            <input type="number" step="0.01" name="monto" id="monto" value="<?php echo htmlspecialchars($fila['monto']); ?>">
        </div>

        <div class="grupo">
            <label for="porcentaje">Porcentaje</label>
            <input type="number" step="0.01" name="porcentaje" id="porcentaje" value="<?php echo htmlspecialchars($fila['porcentaje']); ?>">
        </div>

        <div id="mensaje_ayuda" class="ayuda"></div>

        <div class="grupo">
            <label for="fecha_desde">Fecha Desde</label>
            <input type="date" name="fecha_desde" id="fecha_desde" value="<?php echo htmlspecialchars($fila['fecha_desde']); ?>" required>
        </div>

        <div class="grupo">
            <label for="fecha_hasta">Fecha Hasta</label>
            <input type="date" name="fecha_hasta" id="fecha_hasta" value="<?php echo htmlspecialchars($fila['fecha_hasta']); ?>">
        </div>

        <div class="acciones">
            <button type="submit" class="btn btn-guardar">Actualizar</button>
            <a href="concepto_valores.php?concepto_id=<?php echo $concepto_id_actual; ?>" class="btn btn-cancelar">Cancelar</a>
        </div>
    </form>
</div>

</body>
</html>