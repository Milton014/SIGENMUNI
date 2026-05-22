<?php
session_start();
require_once("conexion.php");
require_once("seguridad.php");

verificarSesion();
soloAdmin();

$usuarios = $conexion->query("
    SELECT id, nombre, apellido, dni, nombre_usuario, email, rol, activo
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

        <h2>Usuarios Registrados</h2>

        <div class="acciones-superiores">
            <a href="usuario_nuevo.php" class="btn btn-nuevo">+ Nuevo usuario</a>
            <a href="index.php" class="btn btn-volver">Volver al menú</a>
        </div>

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
                    <td><?php echo htmlspecialchars($u['nombre'] . ' ' . $u['apellido']); ?></td>
                    <td><?php echo htmlspecialchars($u['dni'] ?? ''); ?></td>
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
                        <a class="btn btn-editar" href="usuario_editar.php?id=<?php echo (int)$u['id']; ?>">Editar</a>

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

</body>
</html>