<?php
session_start();

// Depuración: mostrar el estado de la sesión al cargar index.php
echo "<pre>DEBUG INDEX: Estado de sesión AL INICIAR index.php: ";
var_dump($_SESSION);
echo "</pre>";

require_once 'conexion.php';
require_once 'includes/funciones.php';

if(isset($_SESSION['usuario_id'])) {
    require_once 'includes/header.php';
    ?>
    
    <div class="container">
        <h2>Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre'] . ' ' . htmlspecialchars($_SESSION['apellido'] ?? '')); ?></h2>
        <p>Este es el sistema de gestión de biblioteca del Hospital Victorino Santaella.</p>
        
        <div class="dashboard">
            <div class="card">
                <h3>Libros</h3>
                <p>Gestiona el catálogo de libros</p>
                <a href="gestion_libros.php" class="btn btn-primary">Ir a Libros</a>
            </div>
            
            <div class="card">
                <h3>Préstamos</h3>
                <p>Gestiona los préstamos activos</p>
                <a href="prestamos.php" class="btn btn-primary">Ir a Préstamos</a>
            </div>
            
            <!-- ELIMINAMOS LA VERIFICACIÓN DE ROL ADMIN YA QUE NO EXISTE EN TU TABLA -->
            <div class="card">
                <h3>Usuarios</h3>
                <p>Gestiona los usuarios del sistema</p>
                <a href="usuarios.php" class="btn btn-primary">Ir a Usuarios</a>
            </div>
            
            <div class="card">
                <h3>Especialidades</h3>
                <p>Gestiona las especialidades médicas</p>
                <a href="especialidades.php" class="btn btn-primary">Ir a Especialidades</a>
            </div>
        </div>
    </div>
    
    <?php
    require_once 'includes/footer.php';
} else {
    header("Location: login.php");
    exit();
}
?>