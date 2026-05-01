<?php
function verificarSesion() {
    if (!isset($_SESSION['usuario'])) {
        header("Location: login.php");
        exit();
    }
}

function soloAdmin() {
    if ($_SESSION['rol'] !== 'ADMIN') {
        header("Location: index.php?error=sin_permiso");
        exit();
    }
}

function permitirRoles($roles) {
    if (!in_array($_SESSION['rol'], $roles)) {
        header("Location: index.php?error=sin_permiso");
        exit();
    }
}
?>