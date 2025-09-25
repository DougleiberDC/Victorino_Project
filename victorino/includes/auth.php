<?php
session_start();
if(!isset($_SESSION['usuario_id'])) {
    $_SESSION['mensaje'] = "Debes iniciar sesión";
    $_SESSION['tipo'] = "warning";
    header("Location: login.php");
    exit();
}

function requireAdmin() {
    if($_SESSION['rol'] !== 'admin') {
        $_SESSION['mensaje'] = "Acceso no autorizado";
        $_SESSION['tipo'] = "danger";
        header("Location: index.php");
        exit();
    }
}
?>