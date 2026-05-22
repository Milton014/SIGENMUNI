<?php
session_start();
require_once("conexion.php");
require_once("seguridad.php");

verificarSesion();
soloAdmin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: usuarios.php");
    exit();
}

$stmt = $conexion->prepare("SELECT * FROM usuario WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();

if (!$usuario) {
    header("Location: usuarios.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Editar Usuario - SIGENMUNI</title>

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

.header h1 {
    margin: 0;
    font-size: 28px;
}

.header p {
    margin: 6px 0 0;
}

.contenedor {
    width: 92%;
    max-width: 900px;
    margin: 30px auto;
}

.panel {
    background: white;
    padding: 28px;
    border-radius: 18px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.10);
}

h2 {
    margin-top: 0;
    margin-bottom: 15px;
    color: #111827;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 16px;
}

.campo {
    display: flex;
    flex-direction: column;
}

label {
    font-weight: bold;
    margin-bottom: 6px;
    color: #374151;
    font-size: 14px;
}

input, select {
    width: 100%;
    padding: 12px;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    font-size: 14px;
    box-sizing: border-box;
}

input:focus, select:focus {
    outline: none;
    border-color: #14b8a6;
    box-shadow: 0 0 0 3px rgba(20,184,166,0.15);
}

.ayuda {
    font-size: 13px;
    color: #6b7280;
    margin-top: 6px;
}

.acciones {
    margin-top: 22px;
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

button, .btn {
    padding: 12px 18px;
    border-radius: 10px;
    border: none;
    text-decoration: none;
    font-weight: bold;
    cursor: pointer;
    display: inline-block;
}

.btn-actualizar {
    background: #0f766e;
    color: white;
}

.btn-actualizar:hover {
    background: #115e59;
}

.btn-volver {
    background: #1f2937;
    color: white;
}

.btn-volver:hover {
    background: #111827;
}

.info-usuario {
    background: #ecfdf5;
    color: #166534;
    border: 1px solid #86efac;
    padding: 12px 14px;
    border-radius: 10px;
    margin-bottom: 20px;
    font-weight: bold;
}
</style>
</head>

<body>

<div class="header">
    <h1>SIGENMUNI</h1>
    <p>Editar usuario del sistema</p>
</div>

<div class="contenedor">
    <div class="panel">

        <h2>Editar Usuario</h2>

        <div class="info-usuario">
            Usuario actual: <?php echo htmlspecialchars($usuario['nombre_usuario']); ?>
        </div>

        <form action="usuario_actualizar.php" method="POST">

            <input type="hidden" name="id" value="<?php echo (int)$usuario['id']; ?>">

            <div class="form-grid">

                <div class="campo">
                    <label>Nombre</label>
                    <input 
                        type="text" 
                        name="nombre" 
                        value="<?php echo htmlspecialchars($usuario['nombre'] ?? ''); ?>" 
                        required
                    >
                </div>

                <div class="campo">
                    <label>Apellido</label>
                    <input 
                        type="text" 
                        name="apellido" 
                        value="<?php echo htmlspecialchars($usuario['apellido'] ?? ''); ?>" 
                        required
                    >
                </div>

                <div class="campo">
                    <label>DNI</label>
                    <input 
                        type="text" 
                        name="dni" 
                        value="<?php echo htmlspecialchars($usuario['dni'] ?? ''); ?>" 
                        required
                        minlength="7"
                        maxlength="8"
                        inputmode="numeric"
                        pattern="[0-9]{7,8}"
                        title="El DNI debe contener solo números, entre 7 y 8 dígitos."
                        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                    >
                    <div class="ayuda">
                        Ingrese solo números, sin puntos ni espacios.
                    </div>
                </div>

                <div class="campo">
                    <label>Email</label>
                    <input 
                        type="email" 
                        name="email" 
                        value="<?php echo htmlspecialchars($usuario['email'] ?? ''); ?>" 
                        required
                    >
                </div>

                <div class="campo">
                    <label>Nombre de usuario</label>
                    <input 
                        type="text" 
                        name="nombre_usuario" 
                        value="<?php echo htmlspecialchars($usuario['nombre_usuario'] ?? ''); ?>" 
                        required
                    >
                </div>

                <div class="campo">
                    <label>Rol</label>
                    <select name="rol" required>
                        <option value="ADMIN" <?php if (($usuario['rol'] ?? '') === 'ADMIN') echo 'selected'; ?>>
                            ADMIN
                        </option>
                        <option value="OPERADOR" <?php if (($usuario['rol'] ?? '') === 'OPERADOR') echo 'selected'; ?>>
                            OPERADOR
                        </option>
                    </select>
                </div>

                <div class="campo">
                    <label>Nueva contraseña</label>
                    <input type="password" name="clave">
                    <div class="ayuda">
                        Dejá este campo vacío si no querés cambiar la contraseña.
                    </div>
                </div>

            </div>

            <div class="acciones">
                <button type="submit" class="btn-actualizar">Actualizar usuario</button>
                <a href="usuarios.php" class="btn btn-volver">Volver</a>
            </div>

        </form>

    </div>
</div>

</body>
</html>