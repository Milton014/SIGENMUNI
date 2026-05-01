<?php
session_start();
require_once("seguridad.php");

verificarSesion();
soloAdmin();
?>

<h2>Nuevo Usuario</h2>

<form action="usuario_guardar.php" method="POST">

<label>Nombre</label>
<input type="text" name="nombre" required><br><br>

<label>Apellido</label>
<input type="text" name="apellido" required><br><br>

<label>Usuario</label>
<input type="text" name="usuario" required><br><br>

<label>Contraseña</label>
<input type="password" name="clave" required><br><br>

<label>Rol</label>
<select name="rol">
    <option value="ADMIN">ADMIN</option>
    <option value="OPERADOR">OPERADOR</option>
</select><br><br>

<button type="submit">Guardar</button>

</form>

<br>
<a href="usuarios.php">⬅ Volver</a>