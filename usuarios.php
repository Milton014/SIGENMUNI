<?php
session_start();
require_once("conexion.php");
require_once("seguridad.php");

verificarSesion();
soloAdmin();

$mensaje = "";
$tipoMensaje = "ok";

// AGREGAR USUARIO
if (isset($_POST['guardar'])) {

    $nombre   = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $usuario  = trim($_POST['usuario'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $clave    = trim($_POST['clave'] ?? '');
    $rol      = $_POST['rol'] ?? 'OPERADOR';

    if (empty($nombre) || empty($apellido) || empty($usuario) || empty($clave)) {
        $mensaje = "Complete los campos obligatorios.";
        $tipoMensaje = "error";
    } else {

        // Verificar usuario repetido
        $stmtCheck = $conexion->prepare("SELECT id FROM usuario WHERE nombre_usuario = ? LIMIT 1");
        $stmtCheck->bind_param("s", $usuario);
        $stmtCheck->execute();
        $resCheck = $stmtCheck->get_result();

        if ($resCheck->num_rows > 0) {
            $mensaje = "El nombre de usuario ya existe.";
            $tipoMensaje = "error";
        } else {

            $claveHash = password_hash($clave, PASSWORD_DEFAULT);

            $stmt = $conexion->prepare("
                INSERT INTO usuario 
                (nombre, apellido, nombre_usuario, email, clave, rol, activo, primer_ingreso)
                VALUES (?, ?, ?, ?, ?, ?, 1, 0)
            ");

            $stmt->bind_param("ssssss", $nombre, $apellido, $usuario, $email, $claveHash, $rol);

            if ($stmt->execute()) {
                $mensaje = "Usuario creado correctamente.";
                $tipoMensaje = "ok";
            } else {
                $mensaje = "Error al crear usuario: " . $stmt->error;
                $tipoMensaje = "error";
            }

            $stmt->close();
        }

        $stmtCheck->close();
    }
}

// LISTAR USUARIOS
$usuarios = $conexion->query("
    SELECT id, nombre, apellido, nombre_usuario, email, rol, activo
    FROM usuario
    ORDER BY id DESC
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Gestión de Usuarios - SIGENMUNI</title>

<style>
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
        width: 92%;
        max-width: 1100px;
        margin: 30px auto;
    }

    .panel {
        background: white;
        padding: 24px;
        border-radius: 18px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.10);
        margin-bottom: 25px;
    }

    h1, h2, h3 {
        margin-bottom: 15px;
    }

    .mensaje {
        padding: 12px 15px;
        border-radius: 10px;
        margin-bottom: 18px;
        font-weight: bold;
    }

    .ok {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #86efac;
    }

    .error {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 14px;
    }

    input, select {
        width: 100%;
        padding: 12px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        font-size: 14px;
    }

    .acciones {
        margin-top: 18px;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    button, .btn {
        padding: 11px 16px;
        border-radius: 10px;
        border: none;
        text-decoration: none;
        font-weight: bold;
        cursor: pointer;
        display: inline-block;
    }

    .btn-guardar {
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

    table {
        border-collapse: collapse;
        width: 100%;
        background: white;
        overflow: hidden;
        border-radius: 14px;
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
</style>
</head>

<body>

<div class="header">
    <h1>SIGENMUNI</h1>
    <p>Gestión de Usuarios del Sistema</p>
</div>

<div class="contenedor">

    <div class="panel">
        <h2>Nuevo Usuario</h2>

        <?php if (!empty($mensaje)): ?>
            <div class="mensaje <?php echo $tipoMensaje; ?>">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-grid">
                <input type="text" name="nombre" placeholder="Nombre *" required>
                <input type="text" name="apellido" placeholder="Apellido *" required>
                <input type="text" name="usuario" placeholder="Nombre de usuario *" required>
                <input type="email" name="email" placeholder="Email">
                <input type="password" name="clave" placeholder="Contraseña *" required>

                <select name="rol" required>
                    <option value="OPERADOR">OPERADOR</option>
                    <option value="ADMIN">ADMIN</option>
                </select>
            </div>

            <div class="acciones">
                <button type="submit" name="guardar" class="btn-guardar">Guardar usuario</button>
                <a href="index.php" class="btn btn-volver">Volver al menú</a>
            </div>
        </form>
    </div>

    <div class="panel">
        <h2>Usuarios Registrados</h2>

        <table>
            <tr>
                <th>ID</th>
                <th>Nombre completo</th>
                <th>Usuario</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>

            <?php while ($u = $usuarios->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $u['id']; ?></td>
                    <td><?php echo htmlspecialchars($u['nombre'] . ' ' . $u['apellido']); ?></td>
                    <td><?php echo htmlspecialchars($u['nombre_usuario']); ?></td>
                    <td><?php echo htmlspecialchars($u['email'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($u['rol']); ?></td>
                    <td>
                        <?php if ((int)$u['activo'] === 1): ?>
                            <span class="estado-activo">Activo</span>
                        <?php else: ?>
                            <span class="estado-inactivo">Inactivo</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a class="btn btn-editar" href="usuario_editar.php?id=<?php echo $u['id']; ?>">Editar</a>
                        <a class="btn btn-estado" href="usuario_estado.php?id=<?php echo $u['id']; ?>">
                            <?php echo ((int)$u['activo'] === 1) ? 'Inactivar' : 'Activar'; ?>
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

</div>

</body>
</html>