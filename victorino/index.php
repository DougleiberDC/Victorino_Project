<?php
require_once 'conexion.php';
require_once 'includes/funciones.php';

session_start();

if(isset($_SESSION['usuario_id'])) {
    require_once 'includes/header.php';
    ?>
    
    <div class="container">
        <h2>Bienvenido, <?php echo $_SESSION['nombre']; ?></h2>
        <p>Este es el sistema de gestión de biblioteca del Hospital Victorino Santaella.</p>
        
        <div class="dashboard">
            <div class="card">
                <h3>Libros</h3>
                <p>Gestiona el catálogo de libros</p>
                <a href="libros.php" class="btn btn-primary">Ir a Libros</a>
            </div>
            
            <div class="card">
                <h3>Préstamos</h3>
                <p>Gestiona los préstamos activos</p>
                <a href="prestamos.php" class="btn btn-primary">Ir a Préstamos</a>
            </div>
            
            <?php if($_SESSION['rol'] == 'admin'): ?>
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
            <?php endif; ?>
        </div>
    </div>
    
    <?php
    require_once 'includes/footer.php';
} else {
    header("Location: login.php");
    exit();
}
?>