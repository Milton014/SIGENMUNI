<?php
session_start();

require_once("conexion.php");
require_once("seguridad.php");

verificarSesion();
verificarPermisoModulo("empleados.php");

$busqueda = trim($_GET['busqueda'] ?? "");
$mensaje = "";

$sql = "
    SELECT 
        e.id,
        e.nro_legajo,
        e.apellido,
        e.nombre,
        e.dni,
        e.cuil,
        e.telefono,
        e.email,
        e.fecha_alta,
        e.fecha_baja,
        e.activo,
        c.nombre AS categoria,
        o.nombre AS oficina,
        s.nombre AS situacion
    FROM empleado e
    INNER JOIN categoria c ON e.categoria_id = c.id
    INNER JOIN oficina o ON e.oficina_id = o.id
    INNER JOIN situacion s ON e.situacion_id = s.id
";

if ($busqueda !== "") {

    $sql .= "
        WHERE e.nro_legajo LIKE ?
        OR e.apellido LIKE ?
        OR e.nombre LIKE ?
        OR e.dni LIKE ?
        OR e.cuil LIKE ?
        OR e.email LIKE ?
    ";
}

$sql .= " ORDER BY e.apellido, e.nombre";

$stmt = $conexion->prepare($sql);

if ($busqueda !== "") {

    $like = "%{$busqueda}%";

    $stmt->bind_param(
        "ssssss",
        $like,
        $like,
        $like,
        $like,
        $like,
        $like
    );
}

$stmt->execute();

$resultado = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Gestión de Personal - SIGENMUNI</title>

<style>

*{
    box-sizing:border-box;
}

body{
    font-family:Arial,sans-serif;
    background:#f4f7fb;
    margin:0;
    color:#1f2937;
}

/* =========================================
   HEADER
========================================= */

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

/* =========================================
   CONTENEDOR
========================================= */

.contenedor{
    width:95%;
    max-width:1350px;
    margin:30px auto;
}

.panel{
    background:white;
    padding:24px;
    border-radius:18px;
    box-shadow:0 8px 20px rgba(0,0,0,0.08);
}

/* =========================================
   TOPBAR
========================================= */

.topbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:25px;
    gap:15px;
    flex-wrap:wrap;
}

.topbar h2{
    margin:0;
    color:#0f766e;
    font-size:30px;
}

/* =========================================
   BOTONES
========================================= */

.btn{
    text-decoration:none;
    color:white;
    padding:11px 14px;
    border-radius:10px;
    display:inline-block;
    border:none;
    cursor:pointer;
    font-size:14px;
    font-weight:bold;
    transition:0.2s;
}

.btn:hover{
    opacity:0.92;
    transform:translateY(-1px);
}

.btn-principal{
    background:#0f766e;
}

.btn-sec{
    background:#1f2937;
}

.btn-buscar{
    background:#2563eb;
}

/* =========================================
   BUSCADOR
========================================= */

.buscador{
    background:#f8fafc;
    padding:18px;
    border-radius:14px;
    margin-bottom:24px;
    border:1px solid #e5e7eb;
}

.buscador form{
    display:flex;
    gap:12px;
    flex-wrap:wrap;
}

input[type="text"]{
    flex:1;
    min-width:250px;
    padding:12px;
    border:1px solid #cbd5e1;
    border-radius:10px;
    font-size:14px;
    outline:none;
}

input[type="text"]:focus{
    border-color:#14b8a6;
    box-shadow:0 0 0 3px rgba(20,184,166,0.15);
}

/* =========================================
   TABLA
========================================= */

.tabla-responsive{
    width:100%;
    overflow-x:auto;
}

.tabla-responsive table{
    min-width:1350px;
}

table{
    width:100%;
    border-collapse:collapse;
    background:white;
    border-radius:14px;
    overflow:hidden;
}

th,
td{
    padding:12px;
    border-bottom:1px solid #eee;
    text-align:left;
    font-size:14px;
    vertical-align:top;
}

th{
    background:#0f766e;
    color:white;
}

.estado-activo{
    color:#166534;
    font-weight:bold;
}

.estado-inactivo{
    color:#991b1b;
    font-weight:bold;
}

.acciones{
    display:flex;
    gap:6px;
    flex-wrap:wrap;
}

.acciones a{
    text-decoration:none;
    font-weight:bold;
    font-size:13px;
    padding:7px 9px;
    border-radius:8px;
    background:#eef2f7;
    color:#1f2937;
    transition:0.2s;
}

.acciones a:hover{
    background:#dbe4ee;
}

.sin-registros{
    background:white;
    padding:20px;
    border-radius:12px;
    color:#666;
    box-shadow:0 6px 14px rgba(0,0,0,0.06);
}

/* =========================================
   TABLET
========================================= */

@media (max-width: 992px){

    .contenedor{
        width:96%;
    }

    .buscador form{
        flex-direction:column;
        align-items:stretch;
    }

    input[type="text"]{
        width:100%;
    }

    .buscador .btn{
        width:100%;
        text-align:center;
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

    .topbar{
        flex-direction:column;
        align-items:stretch;
    }

    .topbar h2{
        font-size:24px;
        text-align:center;
    }

    .topbar .acciones-superiores{
        display:flex;
        flex-direction:column;
        gap:10px;
    }

    .btn{
        width:100%;
        text-align:center;
    }

    .buscador{
        padding:16px;
    }

    .buscador form{
        flex-direction:column;
        align-items:stretch;
    }

    input[type="text"]{
        width:100%;
        min-width:auto;
    }

    table th,
    table td{
        font-size:13px;
        padding:10px;
    }

    .acciones{
        flex-direction:column;
    }

    .acciones a{
        width:100%;
        text-align:center;
    }
}

/* =========================================
   CELULARES PEQUEÑOS
========================================= */

@media (max-width: 480px){

    .header h1{
        font-size:22px;
    }

    .topbar h2{
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
        Gestión de Empleados Municipales
    </p>

</div>

<div class="contenedor">

    <div class="panel">

        <!-- TOPBAR -->

        <div class="topbar">

            <h2>
                Gestión de Personal
            </h2>

            <div class="acciones-superiores">

                <a href="empleado_nuevo.php" class="btn btn-principal">
                    + Nuevo Empleado
                </a>

                <a href="index.php" class="btn btn-sec">
                    Volver al menú
                </a>

            </div>

        </div>

        <!-- BUSCADOR -->

        <div class="buscador">

            <form method="GET">

                <input
                    type="text"
                    name="busqueda"
                    placeholder="Buscar por legajo, apellido, nombre, DNI, CUIL o email"
                    value="<?php echo htmlspecialchars($busqueda); ?>"
                >

                <button type="submit" class="btn btn-buscar">
                    Buscar
                </button>

                <a href="empleados.php" class="btn btn-sec">
                    Limpiar
                </a>

            </form>

        </div>

        <!-- TABLA -->

        <?php if ($resultado->num_rows > 0): ?>

            <div class="tabla-responsive">

                <table>

                    <thead>

                        <tr>
                            <th>Legajo</th>
                            <th>Apellido y Nombre</th>
                            <th>DNI</th>
                            <th>CUIL</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>Fecha Alta</th>
                            <th>Fecha Baja</th>
                            <th>Categoría</th>
                            <th>Oficina</th>
                            <th>Situación</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>

                    </thead>

                    <tbody>

                    <?php while ($fila = $resultado->fetch_assoc()): ?>

                        <tr>

                            <td>
                                <?php echo htmlspecialchars($fila['nro_legajo']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($fila['apellido'] . ", " . $fila['nombre']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($fila['dni']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($fila['cuil']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($fila['telefono'] ?? ''); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($fila['email'] ?? ''); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($fila['fecha_alta']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($fila['fecha_baja'] ?? ''); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($fila['categoria']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($fila['oficina']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($fila['situacion']); ?>
                            </td>

                            <td>

                                <?php if ((int)$fila['activo'] === 1): ?>

                                    <span class="estado-activo">
                                        Activo
                                    </span>

                                <?php else: ?>

                                    <span class="estado-inactivo">
                                        Inactivo
                                    </span>

                                <?php endif; ?>

                            </td>

                            <td>

                                <div class="acciones">

                                    <a href="empleado_ver.php?id=<?php echo $fila['id']; ?>">
                                        Ver
                                    </a>

                                    <a href="empleado_editar.php?id=<?php echo $fila['id']; ?>">
                                        Editar
                                    </a>

                                    <a href="liquidacion.php?empleado_id=<?php echo $fila['id']; ?>">
                                        Liquidar
                                    </a>

                                    <?php if ((int)$fila['activo'] === 1): ?>

                                        <a
                                            href="empleado_estado.php?id=<?php echo $fila['id']; ?>&accion=inactivar"
                                            onclick="return confirm('¿Desea inactivar este empleado?');"
                                        >
                                            Inactivar
                                        </a>

                                    <?php else: ?>

                                        <a
                                            href="empleado_estado.php?id=<?php echo $fila['id']; ?>&accion=activar"
                                            onclick="return confirm('¿Desea activar este empleado?');"
                                        >
                                            Activar
                                        </a>

                                    <?php endif; ?>

                                </div>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                    </tbody>

                </table>

            </div>

        <?php else: ?>

            <div class="sin-registros">
                No se encontraron empleados cargados.
            </div>

        <?php endif; ?>

    </div>

</div>

</body>
</html>