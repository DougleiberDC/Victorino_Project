<?php
require_once 'conexion.php';
require_once 'includes/funciones.php';
session_start();

if($_POST) {
    $nombre = limpiar($_POST['nombre']);
    $apellido = limpiar($_POST['apellido']);
    $cedula = limpiar($_POST['cedula']);
    $telefono = limpiar($_POST['telefono']);
    $email = limpiar($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    $query = "INSERT INTO usuarios (nombre, apellido, cedula, telefono, email, password) 
              VALUES ('$nombre', '$apellido', '$cedula', '$telefono', '$email', '$password')";
    
    if($mysqli->query($query)) {
        $_SESSION['mensaje'] = "Usuario registrado correctamente";
        $_SESSION['tipo'] = "success";
        header("Location: login.php");
    } else {
        $_SESSION['mensaje'] = "Error: " . $mysqli->error;
        $_SESSION['tipo'] = "danger";
    }
}

require_once 'includes/header.php';
?>

<div class="form-container">
    <h2>Registro de Usuario</h2>
    
    <form method="post" action="">
        <div class="form-group">
            <label>Nombre:</label>
            <input type="text" name="nombre" required>
        </div>
        
        <div class="form-group">
            <label>Apellido:</label>
            <input type="text" name="apellido" required>
        </div>
        
        <div class="form-group">
            <label>Cédula:</label>
            <input type="text" name="cedula" required>
        </div>
        
        <div class="form-group">
            <label>Teléfono:</label>
            <input type="text" name="telefono">
        </div>
        
        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="email" required>
        </div>
        
        <div class="form-group">
            <label>Contraseña:</label>
            <input type="password" name="password" required>
        </div>
        
        <button type="submit" class="btn btn-primary">Registrarse</button>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>