<?php
require_once('conexion.php'); // Usa tu archivo de conexión (con $mysqli)

// Verificar si la conexión está disponible
if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}

// Obtener el término de búsqueda
$busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';

// Consulta segura: Búsqueda por título, especialidad o contenido
// Ajustado a tus columnas exactas
$sql = "SELECT l.id_libro, l.titulo, l.contenido, e.especialidad
        FROM libros l
        LEFT JOIN especialidad e ON l.id_especialidad = e.id_especialidad
        WHERE l.titulo LIKE ? OR e.especialidad LIKE ? OR l.contenido LIKE ?";

$stmt = $mysqli->prepare($sql);
if (!$stmt) {
    die("Error en la preparación de la consulta: " . $mysqli->error); // Para depurar
}

$termino = "%" . $busqueda . "%";
$stmt->bind_param("sss", $termino, $termino, $termino);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resultados de Búsqueda</title>
    <link rel="stylesheet" href="estilos.css"> <!-- Ajusta si es necesario -->
    <!-- Opcional: Bootstrap para diseño -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Resultados para: "<?php echo htmlspecialchars($busqueda); ?>"</h1>
        <a href="index.php">Volver al inicio</a>

        <?php if ($result->num_rows > 0): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID Libro</th>
                        <th>Título</th>
                        <th>Especialidad</th>
                        <th>Contenido (resumen)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id_libro']); ?></td>
                            <td><?php echo htmlspecialchars($row['titulo']); ?></td>
                            <td><?php echo htmlspecialchars($row['especialidad'] ?? 'No asignada'); ?></td>
                            <td><?php echo htmlspecialchars(substr($row['contenido'], 0, 100) ?? ''); ?>...</td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No se encontraron resultados. Asegúrate de tener datos en la tabla 'libros'.</p>
        <?php endif; ?>
    </div>
<?php
$stmt->close();
$mysqli->close();
?>
</body>
</html>