<?php  ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Biblioteca</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Biblioteca Hospital Victorino Santaella</h1>
            <nav>
                <ul>
                    <li><a href="index.php">Inicio</a></li>
                    <?php if(isset($_SESSION['usuario_id'])): ?>
                        <li><a href="libros.php">Libros</a></li>
                        <li><a href="prestamos.php">Préstamos</a></li>
                        <?php if($_SESSION['rol'] == 'admin'): ?>
                            <li><a href="usuarios.php">Usuarios</a></li>
                            <li><a href="especialidades.php">Especialidades</a></li>
                        <?php endif; ?>
                        <li><a href="logout.php">Salir</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="registrar.php">Registrarse</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    <main class="container">
        <?php if(isset($_SESSION['mensaje'])): ?>
            <div class="alert alert-<?php echo $_SESSION['tipo']; ?>">
                <?php echo $_SESSION['mensaje']; unset($_SESSION['mensaje']); unset($_SESSION['tipo']); ?>
            </div>
        <?php endif; ?>