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

/* Buscar usuario */
$stmt = $conexion->prepare("
    SELECT id, rol, activo
    FROM usuario
    WHERE id = ?
    LIMIT 1
");

$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    header("Location: usuarios.php");
    exit();
}

$usuario = $resultado->fetch_assoc();

/* Si es ADMIN activo y se quiere desactivar */
if (
    $usuario['rol'] === 'ADMIN' &&
    (int)$usuario['activo'] === 1
) {

    $stmtAdmin = $conexion->prepare("
        SELECT COUNT(*) AS total
        FROM usuario
        WHERE rol = 'ADMIN'
          AND activo = 1
    ");

    $stmtAdmin->execute();

    $resAdmin = $stmtAdmin->get_result();
    $dataAdmin = $resAdmin->fetch_assoc();

    /* Evitar desactivar el último ADMIN */
    if ((int)$dataAdmin['total'] <= 1) {

        header("Location: usuarios.php?error=ultimo_admin");
        exit();
    }
}

/* Cambiar estado */
$stmtUpdate = $conexion->prepare("
    UPDATE usuario
    SET activo = IF(activo = 1, 0, 1)
    WHERE id = ?
");

$stmtUpdate->bind_param("i", $id);
$stmtUpdate->execute();

header("Location: usuarios.php");
exit();
?>