<?php 
include("database.php");

// Verificar conexión
if (!$con || $con->connect_error) {
    die("Error de conexión: " . ($con ? $con->connect_error : "Conexión no disponible"));
}

if (isset($_POST['register'])) {
    if (strlen($_POST['usuario']) >= 1 && strlen($_POST['password']) >= 1) {
        $usuario = trim($_POST['usuario']);
        $password = trim($_POST['password']);
        $fechareg = date("d/m/y");
        
        // Usar consultas preparadas para mayor seguridad
        $consulta = $con->prepare("INSERT INTO datos(usuario, contraseña, fecha_reg) VALUES (?, ?, ?)");
        $consulta->bind_param("sss", $usuario, $password, $fechareg);
        
        if ($consulta->execute()) {
            echo '<h3 class="ok">¡Te has inscrito correctamente!</h3>';
        } else {
            echo '<h3 class="bad">¡Ups ha ocurrido un error!</h3>';
        }
        $consulta->close();
    } else {
        echo '<h3 class="bad">¡Por favor complete los campos!</h3>';
    }
}
?>