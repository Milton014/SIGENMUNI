<?php
require_once("conexion.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $correo = trim($_POST["correo"] ?? "");
    $nuevo_usuario = trim($_POST["nuevo_usuario"] ?? "");
    $nueva_contra = trim($_POST["nueva_contra"] ?? "");
    $confirmar_contra = trim($_POST["confirmar_contra"] ?? "");

    if (
        empty($correo) ||
        empty($nuevo_usuario) ||
        empty($nueva_contra) ||
        empty($confirmar_contra)
    ) {
        die("<h3 style='color:red;'>Error: Debe completar todos los campos.</h3><a href='recuperar.php'>Volver</a>");
    }

    if ($nueva_contra !== $confirmar_contra) {
        die("<h3 style='color:red;'>Las contraseñas no coinciden.</h3><a href='recuperar.php'>Volver</a>");
    }

    // Buscar usuario por email
    $stmt = $conexion->prepare("SELECT id FROM usuario WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 0) {
        die("<h3 style='color:red;'>No existe un usuario registrado con ese correo.</h3><a href='recuperar.php'>Volver</a>");
    }

    $fila = $resultado->fetch_assoc();
    $idUsuario = $fila["id"];

    $claveHash = password_hash($nueva_contra, PASSWORD_DEFAULT);

    $update = $conexion->prepare("
        UPDATE usuario
        SET nombre_usuario = ?, clave = ?, primer_ingreso = 0
        WHERE id = ?
    ");
    $update->bind_param("ssi", $nuevo_usuario, $claveHash, $idUsuario);

    if ($update->execute()) {
        echo "<h2 style='color:green;'>Datos actualizados correctamente</h2>";
        echo "<p>Ya puede iniciar sesión con su nuevo usuario y contraseña.</p>";
        echo "<br><a href='login.php'>Volver al login</a>";
    } else {
        echo "<h3 style='color:red;'>Error al actualizar los datos.</h3>";
        echo "<br><a href='recuperar.php'>Volver</a>";
    }

    $stmt->close();
    $update->close();
}
?>