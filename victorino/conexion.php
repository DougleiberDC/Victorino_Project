<?php
$host = "localhost";
$user = "root"; // Si usas otro usuario, cámbialo
$pass = ""; // Si tienes contraseña, ponla aquí
$db = "victorino1"; // Cambia esto por el nombre exacto de tu base de datos

$mysqli = new mysqli($host, $user, $pass, $db);

if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}

$mysqli->set_charset("utf8");
?>