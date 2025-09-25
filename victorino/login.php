<?php
session_start();
require_once 'conexion.php';
require_once 'includes/funciones.php';

// Limpiar variables de sesión que no sean necesarias
unset($_SESSION['tipo']);
unset($_SESSION['mensaje']);

// Depuración: mostrar el estado de la sesión al inicio
echo "<pre>Estado de sesión ANTES del POST en login.php: ";
var_dump($_SESSION);
echo "</pre>";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = isset($_POST['email']) ? limpiar($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // CONSULTA CORREGIDA: Solo seleccionar campos que existen en tu tabla
    $query = "SELECT usuario_id, nombre, apellido, email, password FROM usuarios WHERE email = ?";
    $stmt = $mysqli->prepare($query);

    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $usuario = $result->fetch_assoc();
            if (password_verify($password, $usuario['password'])) {
                // Asignar solo los campos que existen
                $_SESSION['usuario_id'] = $usuario['usuario_id'];
                $_SESSION['nombre'] = $usuario['nombre'];
                $_SESSION['apellido'] = $usuario['apellido'];
                // No asignamos 'rol' porque no existe en tu tabla
                
                // Depuración: mostrar la sesión después de asignar
                echo "<pre>Sesión establecida correctamente en login.php ANTES de redirigir: ";
                var_dump($_SESSION);
                echo "</pre>";
                
                // Redirigir a index.php
                header("Location: index.php");
                exit();
            } else {
                $_SESSION['mensaje'] = "Credenciales incorrectas";
                $_SESSION['tipo'] = "danger";
            }
        } else {
            $_SESSION['mensaje'] = "Credenciales incorrectas";
            $_SESSION['tipo'] = "danger";
        }
        $stmt->close();
    } else {
        $_SESSION['mensaje'] = "Error del sistema: " . $mysqli->error;
        $_SESSION['tipo'] = "danger";
    }
}

require_once 'includes/header.php';
?>

<div class="form-container">
    <h2>Iniciar Sesión</h2>
    
    <?php if(isset($_SESSION['mensaje'])): ?>
        <div class="alert alert-<?= $_SESSION['tipo'] ?? 'info' ?>">
            <?= $_SESSION['mensaje'] ?>
        </div>
        <?php 
        unset($_SESSION['mensaje']);
        unset($_SESSION['tipo']);
        ?>
    <?php endif; ?>

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