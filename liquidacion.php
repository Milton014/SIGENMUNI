<?php
session_start();

require_once("conexion.php");
require_once("seguridad.php");

verificarSesion();
verificarPermisoModulo("liquidacion.php");

$mensaje = "";

// Mensajes
if (isset($_GET['ok'])) {

    if ($_GET['ok'] == '1') {
        $mensaje = "Liquidación creada correctamente.";
    }

    elseif ($_GET['ok'] == '2') {
        $mensaje = "Liquidación procesada correctamente.";
    }
}

if (isset($_GET['msg'])) {

    switch ($_GET['msg']) {

        case 'anulada':
            $mensaje = "Liquidación anulada correctamente.";
        break;

        case 'reabierta':
            $mensaje = "Liquidación reabierta correctamente.";
        break;

        case 'error_estado':
            $mensaje = "Error al cambiar el estado.";
        break;
    }
}

// Filtros
$periodo = $_GET['periodo'] ?? '';
$tipo = $_GET['tipo_liquidacion'] ?? '';
$estado = $_GET['estado'] ?? '';

$sql = "SELECT * FROM liquidacion WHERE 1=1";

if ($periodo != '') {

    $sql .= " AND periodo = '" .
        $conexion->real_escape_string($periodo) . "'";
}

if ($tipo != '') {

    $sql .= " AND tipo_liquidacion = '" .
        $conexion->real_escape_string($tipo) . "'";
}

if ($estado != '') {

    $sql .= " AND estado = '" .
        $conexion->real_escape_string($estado) . "'";
}

$sql .= " ORDER BY id DESC";

$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Liquidaciones - SIGENMUNI</title>

<style>

*{
    box-sizing:border-box;
}

body{
    font-family:Arial, sans-serif;
    background:#f4f7fb;
    margin:0;
    color:#1f2937;
}

.header{
    background:linear-gradient(135deg,#0f766e,#14b8a6);
    color:white;
    padding:22px 30px;
    box-shadow:0 4px 14px rgba(0,0,0,0.10);
}

.header h1{
    margin:0;
    font-size:30px;
}

.header p{
    margin-top:6px;
    font-size:14px;
}

.contenedor{
    width:95%;
    max-width:1250px;
    margin:30px auto;
}

.panel{
    background:white;
    padding:24px;
    border-radius:18px;
    box-shadow:0 8px 20px rgba(0,0,0,0.10);
}

h2{
    color:#0f766e;
    margin-top:0;
    margin-bottom:20px;
}

.acciones-superiores{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
    margin-bottom:20px;
}

.btn{
    padding:10px 14px;
    border:none;
    border-radius:10px;
    text-decoration:none;
    color:white;
    font-size:14px;
    font-weight:bold;
    display:inline-block;
    cursor:pointer;
    transition:0.2s;
}

.btn:hover{
    opacity:0.92;
    transform:translateY(-1px);
}

.btn-nuevo{
    background:#0f766e;
}

.btn-volver{
    background:#1f2937;
}

.btn-ver{
    background:#2563eb;
}

.btn-procesar{
    background:#16a34a;
}

.btn-reabrir{
    background:#d97706;
}

.btn-anular{
    background:#dc2626;
}

.btn-disabled{
    background:#9ca3af;
}

.alerta-ok{
    background:#dcfce7;
    color:#166534;
    border:1px solid #86efac;
    padding:14px;
    border-radius:12px;
    margin-bottom:18px;
    font-weight:bold;
}

.filtros{
    background:#f8fafc;
    padding:18px;
    border-radius:14px;
    margin-bottom:25px;
    border:1px solid #e5e7eb;
}

.form-filtros{
    display:flex;
    gap:12px;
    flex-wrap:wrap;
    align-items:center;
}

input,
select{
    padding:10px 12px;
    border:1px solid #cbd5e1;
    border-radius:10px;
    font-size:14px;
    outline:none;
    min-width:180px;
}

input:focus,
select:focus{
    border-color:#14b8a6;
    box-shadow:0 0 0 3px rgba(20,184,166,0.15);
}

.tabla-responsive{
    width:100%;
    overflow-x:auto;
}

.tabla-responsive table{
    min-width:950px;
}

table{
    width:100%;
    border-collapse:collapse;
}

th,
td{
    padding:12px;
    border-bottom:1px solid #e5e7eb;
    text-align:left;
    font-size:14px;
}

th{
    background:#0f766e;
    color:white;
}

.badge{
    padding:6px 10px;
    border-radius:999px;
    font-size:12px;
    font-weight:bold;
}

.estado-borrador{
    background:#fef3c7;
    color:#92400e;
}

.estado-cerrada{
    background:#d1fae5;
    color:#166534;
}

.estado-anulada{
    background:#fee2e2;
    color:#991b1b;
}

.acciones-tabla{
    display:flex;
    gap:8px;
    flex-wrap:wrap;
}

/* =========================================
   TABLET
========================================= */

@media (max-width: 992px){

    .contenedor{
        width:96%;
    }

    .form-filtros{
        flex-direction:column;
        align-items:stretch;
    }

    input,
    select,
    .form-filtros button{
        width:100%;
    }
}

/* =========================================
   CELULAR
========================================= */

@media (max-width: 768px){

    .header{
        padding:20px;
        text-align:center;
    }

    .header h1{
        font-size:24px;
    }

    .header p{
        font-size:13px;
    }

    .contenedor{
        width:94%;
        margin:20px auto;
    }

    .panel{
        padding:18px;
        border-radius:16px;
    }

    h2{
        font-size:24px;
    }

    .acciones-superiores{
        flex-direction:column;
        align-items:stretch;
    }

    .btn{
        width:100%;
        text-align:center;
    }

    .filtros{
        padding:16px;
    }

    .form-filtros{
        flex-direction:column;
        align-items:stretch;
    }

    input,
    select,
    .form-filtros button{
        width:100%;
    }

    table th,
    table td{
        font-size:13px;
        padding:10px;
    }

    .acciones-tabla{
        flex-direction:column;
    }

    .acciones-tabla .btn{
        width:100%;
    }
}

/* =========================================
   CELULARES PEQUEÑOS
========================================= */

@media (max-width: 480px){

    h2{
        font-size:22px;
    }

    .header h1{
        font-size:22px;
    }

    .btn{
        font-size:13px;
        padding:10px;
    }

    table th,
    table td{
        font-size:12px;
    }
}

</style>
</head>

<body>

<div class="header">

    <h1>SIGENMUNI</h1>

    <p>
        Gestión de Liquidaciones
    </p>

</div>

<div class="contenedor">

    <div class="panel">

        <h2>Liquidaciones</h2>

        <div class="acciones-superiores">

            <a href="liquidacion_nueva.php" class="btn btn-nuevo">
                + Nueva liquidación
            </a>

            <a href="index.php" class="btn btn-volver">
                Volver al menú
            </a>

        </div>

        <?php if ($mensaje) { ?>

            <div class="alerta-ok">
                <?php echo $mensaje; ?>
            </div>

        <?php } ?>

        <!-- FILTROS -->

        <div class="filtros">

            <form method="GET" class="form-filtros">

                <input
                    type="month"
                    name="periodo"
                    value="<?php echo htmlspecialchars($periodo); ?>"
                >

                <select name="tipo_liquidacion">

                    <option value="">
                        Tipo
                    </option>

                    <option value="MENSUAL">
                        Mensual
                    </option>

                    <option value="AGUINALDO">
                        Aguinaldo
                    </option>

                </select>

                <select name="estado">

                    <option value="">
                        Estado
                    </option>

                    <option value="BORRADOR">
                        Borrador
                    </option>

                    <option value="CERRADA">
                        Cerrada
                    </option>

                    <option value="ANULADA">
                        Anulada
                    </option>

                </select>

                <button class="btn btn-ver">
                    Filtrar
                </button>

            </form>

        </div>

        <div class="tabla-responsive">

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

                    <td>
                        <?php echo $fila['id']; ?>
                    </td>

                    <td>
                        <?php echo $fila['tipo_liquidacion']; ?>
                    </td>

                    <td>
                        <?php echo $fila['periodo']; ?>
                    </td>

                    <td>
                        <?php echo $fila['fecha_liquidacion']; ?>
                    </td>

                    <td>

                        <?php

                        $clase = "estado-borrador";

                        if ($fila['estado']=="CERRADA") {
                            $clase="estado-cerrada";
                        }

                        if ($fila['estado']=="ANULADA") {
                            $clase="estado-anulada";
                        }

                        ?>

                        <span class="badge <?php echo $clase; ?>">
                            <?php echo $fila['estado']; ?>
                        </span>

                    </td>

                    <td>

                        <div class="acciones-tabla">

                            <a
                                href="liquidacion_ver.php?id=<?php echo $fila['id']; ?>"
                                class="btn btn-ver"
                            >
                                Ver
                            </a>

                            <?php if ($fila['estado'] == 'BORRADOR') { ?>

                                <a
                                    href="liquidacion_procesar.php?id=<?php echo $fila['id']; ?>"
                                    class="btn btn-procesar"
                                >
                                    Procesar
                                </a>

                                <a
                                    href="liquidacion_estado.php?id=<?php echo $fila['id']; ?>&accion=anular"
                                    class="btn btn-anular"
                                >
                                    Anular
                                </a>

                            <?php } elseif ($fila['estado'] == 'CERRADA') { ?>

                                <a
                                    href="liquidacion_estado.php?id=<?php echo $fila['id']; ?>&accion=reabrir"
                                    class="btn btn-reabrir"
                                >
                                    Reabrir
                                </a>

                                <a
                                    href="liquidacion_estado.php?id=<?php echo $fila['id']; ?>&accion=anular"
                                    class="btn btn-anular"
                                >
                                    Anular
                                </a>

                            <?php } else { ?>

                                <span class="btn btn-disabled">
                                    Sin acciones
                                </span>

                            <?php } ?>

                        </div>

                    </td>

                </tr>

                <?php } ?>

            </table>

        </div>

    </div>

</div>

</body>
</html>