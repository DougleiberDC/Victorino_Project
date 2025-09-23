<?php
$db_server = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "victorino";
$con = "";

try {
    $con = new mysqli($db_server, $db_user, $db_pass, $db_name);
    
    // Verificar conexión
    if ($con->connect_error) {
        throw new Exception("Connection failed: " . $con->connect_error);
    }
    
    // Establecer el charset
    $con->set_charset("utf8");
    
} catch (Exception $e) {
    echo "Couldn't Connect: " . $e->getMessage();
    $con = null; // Asegurar que $con sea null en caso de error
}
?>