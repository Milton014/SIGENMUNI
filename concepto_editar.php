<?php
session_start();
require_once("conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    die("ID inválido.");
}

$stmt = $conexion->prepare("SELECT * FROM concepto WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    die("Concepto no encontrado.");
}

$concepto = $resultado->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Concepto</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            margin: 0;
            background: #f4f6f9;
        }

        .contenedor {
            width: 95%;
            max-width: 900px;
            margin: 30px auto;
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        h1 {
            margin-top: 0;
            color: #2c3e50;
        }

        .fila {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }

        .campo {
            display: flex;
            flex-direction: column;
        }

        .campo label {
            margin-bottom: 6px;
            font-weight: bold;
            color: #333;
        }

        .campo input,
        .campo select,
        .campo textarea {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }

        .campo textarea {
            resize: vertical;
            min-height: 80px;
        }

        .fila-completa {
            margin-bottom: 15px;
        }

        .checks {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin: 20px 0;
        }

        .check-item {
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 10px;
        }

        .acciones {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
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
            font-size: 14px;
        }

        .btn-guardar {
            background: #28a745;
        }

        .btn-volver {
            background: #6c757d;
        }

        .ayuda {
            margin-top: 6px;
            font-size: 13px;
            color: #6c757d;
        }

        @media (max-width: 768px) {
            .fila,
            .checks {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <script>
        function actualizarCampos() {
            const forma = document.getElementById("forma_calculo").value;
            const porcentaje = document.getElementById("porcentaje");
            const montoFijo = document.getElementById("monto_fijo");
            const ayuda = document.getElementById("ayuda_valores");

            porcentaje.readOnly = true;
            montoFijo.readOnly = true;

            porcentaje.style.backgroundColor = "#e9ecef";
            montoFijo.style.backgroundColor = "#e9ecef";
            ayuda.style.display = "none";

            if (forma === "PORCENTAJE") {
                porcentaje.readOnly = false;
                porcentaje.style.backgroundColor = "#fff";
                montoFijo.value = "0.00";
            } else if (forma === "FIJO") {
                montoFijo.readOnly = false;
                montoFijo.style.backgroundColor = "#fff";
                porcentaje.value = "0.0000";
            } else if (forma === "TABLA_CATEGORIA") {
                porcentaje.value = "0.0000";
                montoFijo.value = "0.00";
                ayuda.style.display = "block";
            } else if (forma === "MANUAL" || forma === "FORMULA") {
                porcentaje.value = "0.0000";
                montoFijo.value = "0.00";
            }
        }

        window.onload = actualizarCampos;
    </script>
</head>
<body>

<div class="contenedor">
    <h1>Editar Concepto</h1>

    <form action="concepto_actualizar.php" method="POST">
        <input type="hidden" name="id" value="<?php echo $concepto['id']; ?>">

        <div class="fila">
            <div class="campo">
                <label for="codigo">Código</label>
                <input type="number" name="codigo" id="codigo" required value="<?php echo htmlspecialchars($concepto['codigo']); ?>">
            </div>

            <div class="campo">
                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" id="nombre" maxlength="150" required value="<?php echo htmlspecialchars($concepto['nombre']); ?>">
            </div>
        </div>

        <div class="fila">
            <div class="campo">
                <label for="categoria">Categoría</label>
                <select name="categoria" id="categoria" required>
                    <option value="REMUNERATIVO" <?php if ($concepto['categoria'] === 'REMUNERATIVO') echo 'selected'; ?>>REMUNERATIVO</option>
                    <option value="NO_REMUNERATIVO" <?php if ($concepto['categoria'] === 'NO_REMUNERATIVO') echo 'selected'; ?>>NO_REMUNERATIVO</option>
                    <option value="ASIGNACION_FAMILIAR" <?php if ($concepto['categoria'] === 'ASIGNACION_FAMILIAR') echo 'selected'; ?>>ASIGNACION_FAMILIAR</option>
                    <option value="DESCUENTO" <?php if ($concepto['categoria'] === 'DESCUENTO') echo 'selected'; ?>>DESCUENTO</option>
                    <option value="APORTE_PATRONAL" <?php if ($concepto['categoria'] === 'APORTE_PATRONAL') echo 'selected'; ?>>APORTE_PATRONAL</option>
                </select>
            </div>

            <div class="campo">
                <label for="forma_calculo">Forma de Cálculo</label>
                <select name="forma_calculo" id="forma_calculo" onchange="actualizarCampos()" required>
                    <option value="FIJO" <?php if ($concepto['forma_calculo'] === 'FIJO') echo 'selected'; ?>>FIJO</option>
                    <option value="TABLA_CATEGORIA" <?php if ($concepto['forma_calculo'] === 'TABLA_CATEGORIA') echo 'selected'; ?>>TABLA POR CATEGORÍA</option>
                    <option value="PORCENTAJE" <?php if ($concepto['forma_calculo'] === 'PORCENTAJE') echo 'selected'; ?>>PORCENTAJE</option>
                    <option value="MANUAL" <?php if ($concepto['forma_calculo'] === 'MANUAL') echo 'selected'; ?>>MANUAL</option>
                    <option value="FORMULA" <?php if ($concepto['forma_calculo'] === 'FORMULA') echo 'selected'; ?>>FORMULA</option>
                </select>
            </div>
        </div>

        <div class="fila">
            <div class="campo">
                <label for="porcentaje">Porcentaje</label>
                <input type="number" step="0.0001" name="porcentaje" id="porcentaje" value="<?php echo htmlspecialchars($concepto['porcentaje']); ?>">
            </div>

            <div class="campo">
                <label for="monto_fijo">Monto Fijo</label>
                <input type="number" step="0.01" name="monto_fijo" id="monto_fijo" value="<?php echo htmlspecialchars($concepto['monto_fijo']); ?>">
                <div id="ayuda_valores" class="ayuda" style="display:none;">
                    Este concepto se valoriza por categoría desde Gestión de Valores.
                </div>
            </div>
        </div>

        <div class="fila">
            <div class="campo">
                <label for="base_calculo">Base de Cálculo</label>
                <input type="text" name="base_calculo" id="base_calculo" maxlength="50" value="<?php echo htmlspecialchars($concepto['base_calculo'] ?? ''); ?>">
            </div>

            <div class="campo">
                <label for="orden_calculo">Orden de Cálculo</label>
                <input type="number" name="orden_calculo" id="orden_calculo" min="0" value="<?php echo htmlspecialchars($concepto['orden_calculo']); ?>">
            </div>
        </div>

        <div class="fila">
            <div class="campo">
                <label for="fecha_desde">Fecha Desde</label>
                <input type="date" name="fecha_desde" id="fecha_desde" value="<?php echo htmlspecialchars($concepto['fecha_desde'] ?? ''); ?>">
            </div>

            <div class="campo">
                <label for="fecha_hasta">Fecha Hasta</label>
                <input type="date" name="fecha_hasta" id="fecha_hasta" value="<?php echo htmlspecialchars($concepto['fecha_hasta'] ?? ''); ?>">
            </div>
        </div>

        <div class="fila-completa">
            <div class="campo">
                <label for="descripcion">Descripción</label>
                <textarea name="descripcion" id="descripcion"><?php echo htmlspecialchars($concepto['descripcion'] ?? ''); ?></textarea>
            </div>
        </div>

        <div class="checks">
            <div class="check-item">
                <label>
                    <input type="checkbox" name="requiere_manual" value="1" <?php if ((int)$concepto['requiere_manual'] === 1) echo 'checked'; ?>>
                    Requiere Manual
                </label>
            </div>

            <div class="check-item">
                <label>
                    <input type="checkbox" name="aplica_sac" value="1" <?php if ((int)$concepto['aplica_sac'] === 1) echo 'checked'; ?>>
                    Aplica SAC
                </label>
            </div>

            <div class="check-item">
                <label>
                    <input type="checkbox" name="visible_recibo" value="1" <?php if ((int)$concepto['visible_recibo'] === 1) echo 'checked'; ?>>
                    Visible en Recibo
                </label>
            </div>

            <div class="check-item">
                <label>
                    <input type="checkbox" name="activo" value="1" <?php if ((int)$concepto['activo'] === 1) echo 'checked'; ?>>
                    Activo
                </label>
            </div>
        </div>

        <div class="acciones">
            <button type="submit" class="btn btn-guardar">Actualizar</button>
            <a href="conceptos.php" class="btn btn-volver">Volver</a>
        </div>
    </form>
</div>

</body>
</html>