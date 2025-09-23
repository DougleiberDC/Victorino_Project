<?php
require_once 'conexion.php';
require_once 'includes/funciones.php';
session_start();

if($_POST) {
    $email = limpiar($_POST['email']);
    $password = $_POST['password'];
    
    $query = "SELECT * FROM usuarios WHERE email = '$email'";
    $result = $mysqli->query($query);
    
    if($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
        if(password_verify($password, $usuario['password'])) {
            $_SESSION['usuario_id'] = $usuario['id_usuario'];
            $_SESSION['nombre'] = $usuario['nombre_usua'];
            $_SESSION['rol'] = $usuario['rol'];
            header("Location: index.php");
            exit();
        }
    }
    
    $_SESSION['mensaje'] = "Credenciales incorrectas";
    $_SESSION['tipo'] = "danger";
}

require_once 'includes/header.php';
?>

<div class="form-container">
    <h2>Iniciar Sesión</h2>
    
    <form method="post" action="">
        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="email" required>
        </div>
        
        <div class="form-group">
            <label>Contraseña:</label>
            <input type="password" name="password" required>
        </div>
        
        <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
    </form>
    
    <p>¿No tienes cuenta? <a href="registrar.php">Regístrate aquí</a></p>
</div>

<?php require_once 'includes/footer.php'; ?>