<?php
session_start();
require_once("conexion.php");

// Verificar login
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

// Solo ADMIN puede acceder
if ($_SESSION['rol'] !== 'ADMIN') {
    die("Acceso denegado");
}

$mensaje = "";

// AGREGAR USUARIO
if (isset($_POST['guardar'])) {

    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $usuario = trim($_POST['usuario']);
    $email = trim($_POST['email']);
    $clave = trim($_POST['clave']);
    $rol = $_POST['rol'];

    if (empty($nombre) || empty($apellido) || empty($usuario) || empty($clave)) {
        $mensaje = "Complete los campos obligatorios";
    } else {
        $claveHash = password_hash($clave, PASSWORD_DEFAULT);

        $stmt = $conexion->prepare("
            INSERT INTO usuario (nombre, apellido, nombre_usuario, email, clave, rol)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param("ssssss", $nombre, $apellido, $usuario, $email, $claveHash, $rol);

        if ($stmt->execute()) {
            $mensaje = "Usuario creado correctamente";
        } else {
            $mensaje = "Error: " . $stmt->error;
        }
    }
}

// LISTAR USUARIOS
$usuarios = $conexion->query("SELECT id, nombre, apellido, nombre_usuario, rol, activo FROM usuario");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Usuarios</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        input, select { padding: 8px; margin: 5px; width: 200px; }
        button { padding: 10px; background: green; color: white; border: none; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; }
    </style>
</head>
<body>

<h2>Gestión de Usuarios</h2>

<?php if ($mensaje): ?>
    <p style="color:green;"><?php echo $mensaje; ?></p>
<?php endif; ?>

<h3>Nuevo Usuario</h3>

<form method="POST">
    <input type="text" name="nombre" placeholder="Nombre" required>
    <input type="text" name="apellido" placeholder="Apellido" required>
    <input type="text" name="usuario" placeholder="Usuario" required>
    <input type="email" name="email" placeholder="Email">
    <input type="password" name="clave" placeholder="Contraseña" required>

    <select name="rol">
        <option value="OPERADOR">Operador</option>
        <option value="ADMIN">Admin</option>
    </select>

    <button type="submit" name="guardar">Guardar</button>
</form>

<h3>Usuarios Registrados</h3>

<table>
<tr>
    <th>ID</th>
    <th>Nombre</th>
    <th>Usuario</th>
    <th>Rol</th>
    <th>Estado</th>
</tr>

<?php while ($u = $usuarios->fetch_assoc()): ?>
<tr>
    <td><?php echo $u['id']; ?></td>
    <td><?php echo $u['nombre'] . ' ' . $u['apellido']; ?></td>
    <td><?php echo $u['nombre_usuario']; ?></td>
    <td><?php echo $u['rol']; ?></td>
    <td><?php echo $u['activo'] ? 'Activo' : 'Inactivo'; ?></td>
</tr>
<?php endwhile; ?>
</table>

</body>
</html>