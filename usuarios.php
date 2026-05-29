<?php
session_start();
require_once("conexion.php");
require_once("seguridad.php");

verificarSesion();
soloAdmin();

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['guardar_permisos'])) {

    $conexion->query("UPDATE modulo_permiso SET permitido_operador = 0");

    if (!empty($_POST['modulos']) && is_array($_POST['modulos'])) {

        foreach ($_POST['modulos'] as $archivo) {

            if ($archivo === "usuarios.php") {
                continue;
            }

            $stmt = $conexion->prepare("
                UPDATE modulo_permiso
                SET permitido_operador = 1
                WHERE archivo_principal = ?
            ");

            $stmt->bind_param("s", $archivo);
            $stmt->execute();
        }
    }

    header("Location: usuarios.php?ok=permisos");
    exit();
}

if (isset($_GET['ok']) && $_GET['ok'] === 'permisos') {
    $mensaje = "Permisos del OPERADOR actualizados correctamente.";
}

$usuarios = $conexion->query("
    SELECT id, nombre, apellido, dni, nombre_usuario, email, rol, activo
    FROM usuario
    ORDER BY id DESC
");

$modulos = $conexion->query("
    SELECT id, modulo, archivo_principal, permitido_operador
    FROM modulo_permiso
    ORDER BY id ASC
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gestión de Usuarios - SIGENMUNI</title>

<style>

*{
    box-sizing:border-box;
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
}

.contenedor {
    width: 96%;
    max-width: 1250px;
    margin: 30px auto;
}

.panel {
    background: white;
    padding: 24px;
    border-radius: 18px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.10);
    margin-bottom: 25px;
}

.acciones-superiores {
    margin-bottom: 18px;
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.btn {
    padding: 10px 14px;
    border-radius: 10px;
    border: none;
    text-decoration: none;
    font-weight: bold;
    cursor: pointer;
    display: inline-block;
    font-size: 14px;
    transition: 0.2s;
}

.btn:hover{
    opacity:0.92;
    transform:translateY(-1px);
}

.btn-nuevo {
    background: #0f766e;
    color: white;
}

.btn-volver {
    background: #1f2937;
    color: white;
}

.btn-editar {
    background: #2563eb;
    color: white;
}

.btn-estado {
    background: #ea580c;
    color: white;
}

.btn-guardar {
    background: #0f766e;
    color: white;
    margin-top: 18px;
}

.tabla-responsive {
    width: 100%;
    overflow-x: auto;
}

.tabla-responsive table {
    min-width: 850px;
}

table {
    border-collapse: collapse;
    width: 100%;
    background: white;
    overflow: hidden;
    border-radius: 14px;
    margin-top: 15px;
}

th, td {
    border-bottom: 1px solid #e5e7eb;
    padding: 12px;
    text-align: left;
    font-size: 14px;
}

th {
    background: #0f766e;
    color: white;
}

.estado-activo {
    color: #166534;
    font-weight: bold;
}

.estado-inactivo {
    color: #991b1b;
    font-weight: bold;
}

.alerta-ok {
    background: #dcfce7;
    color: #166534;
    border: 1px solid #86efac;
    padding: 14px;
    border-radius: 12px;
    margin-bottom: 18px;
    font-weight: bold;
}

.info {
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    color: #1e40af;
    padding: 14px;
    border-radius: 12px;
    margin-bottom: 18px;
    line-height: 1.5;
}

.switch {
    position: relative;
    display: inline-block;
    width: 54px;
    height: 28px;
}

.switch input {
    display: none;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: #cbd5e1;
    transition: .3s;
    border-radius: 999px;
}

.slider:before {
    position: absolute;
    content: "";
    height: 22px;
    width: 22px;
    left: 3px;
    bottom: 3px;
    background: white;
    transition: .3s;
    border-radius: 50%;
}

.switch input:checked + .slider {
    background: #0f766e;
}

.switch input:checked + .slider:before {
    transform: translateX(26px);
}

.bloqueado {
    color: #991b1b;
    font-weight: bold;
}

.habilitado {
    color: #166534;
    font-weight: bold;
}

.deshabilitado {
    color: #92400e;
    font-weight: bold;
}

/* =========================================
   RESPONSIVE
========================================= */

@media (max-width: 768px) {

    .header {
        padding: 18px;
        text-align: center;
    }

    .header h1 {
        font-size: 24px;
    }

    .header p {
        font-size: 14px;
    }

    .contenedor {
        width: 94%;
        margin: 20px auto;
    }

    .panel {
        padding: 18px;
        border-radius: 14px;
    }

    .panel h2 {
        font-size: 22px;
        margin-bottom: 15px;
    }

    .acciones-superiores {
        flex-direction: column;
        align-items: stretch;
    }

    .btn {
        width: 100%;
        text-align: center;
    }

    table th,
    table td {
        font-size: 13px;
        padding: 10px;
    }

    .switch {
        transform: scale(0.90);
    }

    .alerta-ok,
    .info {
        font-size: 14px;
    }
}

@media (max-width: 480px) {

    .panel h2 {
        font-size: 19px;
    }

    .header h1 {
        font-size: 22px;
    }

    .btn {
        font-size: 13px;
        padding: 10px;
    }

    table th,
    table td {
        font-size: 12px;
    }
}

</style>
</head>

<body>

<div class="header">
    <h1>SIGENMUNI</h1>
    <p>Gestión de Usuarios del Sistema</p>
</div>

<div class="contenedor">

    <?php if (!empty($mensaje)): ?>
        <div class="alerta-ok">
            <?php echo htmlspecialchars($mensaje); ?>
        </div>
    <?php endif; ?>

    <!-- ================================= -->
    <!-- USUARIOS REGISTRADOS -->
    <!-- ================================= -->

    <div class="panel">

        <h2>Usuarios Registrados</h2>

        <div class="acciones-superiores">

            <a href="usuario_nuevo.php" class="btn btn-nuevo">
                + Nuevo usuario
            </a>

            <a href="index.php" class="btn btn-volver">
                Volver al menú
            </a>

        </div>

        <div class="tabla-responsive">

            <table>

                <tr>
                    <th>ID</th>
                    <th>Nombre completo</th>
                    <th>DNI</th>
                    <th>Usuario</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>

                <?php while ($u = $usuarios->fetch_assoc()): ?>

                    <tr>

                        <td><?php echo (int)$u['id']; ?></td>

                        <td>
                            <?php echo htmlspecialchars($u['nombre'] . ' ' . $u['apellido']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($u['dni'] ?? ''); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($u['nombre_usuario']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($u['email'] ?? ''); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($u['rol']); ?>
                        </td>

                        <td>

                            <?php if ((int)$u['activo'] === 1): ?>

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

                            <a
                                class="btn btn-editar"
                                href="usuario_editar.php?id=<?php echo (int)$u['id']; ?>"
                            >
                                Editar
                            </a>

                            <a
                                class="btn btn-estado"
                                href="usuario_estado.php?id=<?php echo (int)$u['id']; ?>"
                                onclick="return confirm('¿Está seguro de cambiar el estado de este usuario?');"
                            >
                                <?php echo ((int)$u['activo'] === 1) ? 'Inactivar' : 'Activar'; ?>
                            </a>

                        </td>

                    </tr>

                <?php endwhile; ?>

            </table>

        </div>

    </div>

    <!-- ================================= -->
    <!-- PERMISOS DE MODULOS -->
    <!-- ================================= -->

    <div class="panel">

        <h2>Permisos de Módulos para OPERADOR</h2>

        

        <form method="POST">

            <div class="tabla-responsive">

                <table>

                    <tr>
                        <th>Módulo</th>
                        <th>Archivo principal</th>
                        <th>Estado</th>
                        <th>Permiso</th>
                    </tr>

                    <?php while ($m = $modulos->fetch_assoc()): ?>

                        <?php
                        $archivo = $m['archivo_principal'];
                        $permitido = (int)$m['permitido_operador'];
                        $esUsuarios = ($archivo === "usuarios.php");
                        ?>

                        <tr>

                            <td>
                                <?php echo htmlspecialchars($m['modulo']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($archivo); ?>
                            </td>

                            <td>

                                <?php if ($esUsuarios): ?>

                                    <span class="bloqueado">
                                        Solo ADMIN
                                    </span>

                                <?php elseif ($permitido === 1): ?>

                                    <span class="habilitado">
                                        Habilitado
                                    </span>

                                <?php else: ?>

                                    <span class="deshabilitado">
                                        Deshabilitado
                                    </span>

                                <?php endif; ?>

                            </td>

                            <td>

                                <?php if ($esUsuarios): ?>

                                    <span class="bloqueado">
                                        Bloqueado
                                    </span>

                                <?php else: ?>

                                    <label class="switch">

                                        <input
                                            type="checkbox"
                                            name="modulos[]"
                                            value="<?php echo htmlspecialchars($archivo); ?>"
                                            <?php echo ($permitido === 1) ? 'checked' : ''; ?>
                                        >

                                        <span class="slider"></span>

                                    </label>

                                <?php endif; ?>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                </table>

            </div>

            <button
                type="submit"
                name="guardar_permisos"
                class="btn btn-guardar"
            >
                Guardar permisos del OPERADOR
            </button>

        </form>

    </div>

</div>

</body>
</html>