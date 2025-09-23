<?php
include("database.php");

// Verificar si la conexión se estableció correctamente
if (!$con || $con->connect_error) {
    die("Error de conexión: " . ($con ? $con->connect_error : "Conexión no disponible"));
}

// Obtener parámetro de búsqueda
$busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';

// Consulta base para obtener libros
if(!empty($busqueda)) {
    // Si hay búsqueda, filtrar por título
    $consulta = $con->prepare("SELECT libros.*, autor.nombre as autor_nombre 
                              FROM libros
                              LEFT JOIN libro_autor ON libro_autor.id_libro = libros.id
                              LEFT JOIN autor ON autor.id = libro_autor.id_autor
                              WHERE titulo LIKE ?");
    $paramBusqueda = "%$busqueda%";
    $consulta->bind_param("s", $paramBusqueda);
} else {
    // Si no hay búsqueda, mostrar todos los libros
    $consulta = $con->prepare("SELECT libros.*, autor.nombre as autor_nombre 
                              FROM libros
                              LEFT JOIN libro_autor ON libro_autor.id_libro = libros.id
                              LEFT JOIN autor ON autor.id = libro_autor.id_autor
                              LIMIT 20");
}

// Verificar si la preparación de la consulta fue exitosa
if (!$consulta) {
    die("Error en la consulta: " . $con->error);
}

$consulta->execute();
$resultado = $consulta->get_result();

if($resultado->num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        echo '<div class="col">';
        echo '  <div class="card fluid" style="max-height: 530px;">';
        echo '    <img src="imagenes/libro_1.png" class="img-thumbnail img-fluid border-secondary-subtle" alt="img">';
        echo '    <div class="card-body">';
        echo '      <p class="ms-2">Autor: <i>'.($row['autor_nombre'] ?? 'Desconocido').'</i></p>';
        echo '      <h4 class="card-title ms-2"><b>'.$row['titulo'].'</b></h4>';
        echo '      <p class="ms-2"><strong>Año: '.$row['ano'].'</strong><br>';
        echo '      <strong>Ejemplares: '.$row['ejemplares'].'</strong></p>';
        echo '    </div>';
        echo '  </div>';
        echo '</div>';
    }
} else {
    echo '<div class="col"><p class="text-center">No se encontraron resultados</p></div>';
}

$consulta->close();
?>