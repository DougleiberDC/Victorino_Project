<?php
session_start();
require_once 'conexion.php';

// Procesar acciones POST: eliminar y actualizar
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['accion'])) {
    $accion = $_POST['accion'];

    if ($accion == 'eliminar') {
        $id_libro = (int)($_POST['id_libro'] ?? 0);
        if ($id_libro > 0) {
            // Eliminar relaciones libro_autor primero
            $mysqli->query("DELETE FROM libro_autor WHERE id_libro = $id_libro");
            // Luego eliminar libro
            $mysqli->query("DELETE FROM libros WHERE id_libro = $id_libro");
            $_SESSION['mensaje'] = "Libro eliminado exitosamente.";
        }
    } elseif ($accion == 'actualizar') {
        $id_libro = (int)($_POST['id_libro'] ?? 0);
        $titulo = $_POST['titulo'] ?? '';
        $editorial = $_POST['editorial'] ?? '';
        $ciudad_editorial = $_POST['ciudad_editorial'] ?? '';
        $ano = (int)($_POST['ano'] ?? 0);
        $ISBN = $_POST['ISBN'] ?? '';
        $id_especialidad = (int)($_POST['id_especialidad'] ?? 0);
        $contenido = $_POST['contenido'] ?? '';
        $ejemplares = (int)($_POST['ejemplares'] ?? 0);
        $estado_id = (int)($_POST['estado_id'] ?? 0);
        $autores = $_POST['autores'] ?? [];

        if ($id_libro > 0 && $titulo != '') {
            // Actualizar libro
            $sql = "UPDATE libros SET 
                titulo = '$titulo',
                editorial = '$editorial',
                ciudad_editorial = '$ciudad_editorial',
                ano = $ano,
                ISBN = '$ISBN',
                id_especialidad = $id_especialidad,
                contenido = '$contenido',
                ejemplares = $ejemplares,
                estado_id = $estado_id
                WHERE id_libro = $id_libro";
            $mysqli->query($sql);

            // Actualizar autores: eliminar antiguos y agregar nuevos
            $mysqli->query("DELETE FROM libro_autor WHERE id_libro = $id_libro");
            foreach ($autores as $id_autor) {
                $id_autor = (int)$id_autor;
                $mysqli->query("INSERT INTO libro_autor (id_libro, id_autor) VALUES ($id_libro, $id_autor)");
            }

            $_SESSION['mensaje'] = "Libro actualizado exitosamente.";
        }
    }
    header("Location: gestion_libros.php");
    exit();
}

// Obtener lista de libros con autores concatenados
$sql = "
SELECT l.*, GROUP_CONCAT(a.nombre SEPARATOR ', ') AS autores
FROM libros l
LEFT JOIN libro_autor la ON l.id_libro = la.id_libro
LEFT JOIN autor a ON la.id_autor = a.id_autor
GROUP BY l.id_libro
ORDER BY l.titulo ASC
";
$result = $mysqli->query($sql);

// Obtener lista de autores para selects
$sqlAutores = "SELECT id_autor, nombre FROM autor ORDER BY nombre ASC";
$resultAutores = $mysqli->query($sqlAutores);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Gestión de Libros</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1>Gestión de Libros</h1>

    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert alert-info"><?= htmlspecialchars($_SESSION['mensaje']) ?></div>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>

    <a href="index.php" class="btn btn-secondary mb-3">Volver</a>
    <a href="agregar_libro.php" class="btn btn-primary mb-3">Agregar Libro</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID Libro</th>
                <th>Título y Autor(es)</th>
                <th>Editorial</th>
                <th>Año</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($libro = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $libro['id_libro'] ?></td>
                        <td>
                            <?= htmlspecialchars($libro['titulo']) ?>
                            <br>
                            <small><em><?= htmlspecialchars($libro['autores'] ?? 'Sin autor') ?></em></small>
                        </td>
                        <td><?= htmlspecialchars($libro['editorial']) ?></td>
                        <td><?= htmlspecialchars($libro['ano']) ?></td>
                        <td>
                            <!-- Eliminar -->
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="accion" value="eliminar">
                                <input type="hidden" name="id_libro" value="<?= $libro['id_libro'] ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                            </form>
                            <!-- Editar -->
                            <button type="button" class="btn btn-warning btn-sm" onclick="document.getElementById('edit-<?= $libro['id_libro'] ?>').style.display='block'">Editar</button>
                            <div id="edit-<?= $libro['id_libro'] ?>" style="display:none; margin-top:10px;">
                                <form method="POST" class="border p-3">
                                    <input type="hidden" name="accion" value="actualizar">
                                    <input type="hidden" name="id_libro" value="<?= $libro['id_libro'] ?>">

                                    <div class="mb-2">
                                        <label>Título</label>
                                        <input type="text" name="titulo" value="<?= htmlspecialchars($libro['titulo']) ?>" required class="form-control">
                                    </div>
                                    <div class="mb-2">
                                        <label>Editorial</label>
                                        <input type="text" name="editorial" value="<?= htmlspecialchars($libro['editorial']) ?>" required class="form-control">
                                    </div>
                                    <div class="mb-2">
                                        <label>Ciudad Editorial</label>
                                        <input type="text" name="ciudad_editorial" value="<?= htmlspecialchars($libro['ciudad_editorial']) ?>" required class="form-control">
                                    </div>
                                    <div class="mb-2">
                                        <label>Año</label>
                                        <input type="number" name="ano" value="<?= htmlspecialchars($libro['ano']) ?>" required class="form-control">
                                    </div>
                                    <div class="mb-2">
                                        <label>ISBN</label>
                                        <input type="text" name="ISBN" value="<?= htmlspecialchars($libro['ISBN']) ?>" required class="form-control">
                                    </div>
                                    <div class="mb-2">
                                        <label>ID Especialidad</label>
                                        <input type="number" name="id_especialidad" value="<?= htmlspecialchars($libro['id_especialidad']) ?>" required class="form-control">
                                    </div>
                                    <div class="mb-2">
                                        <label>Contenido</label>
                                        <textarea name="contenido" class="form-control"><?= htmlspecialchars($libro['contenido']) ?></textarea>
                                    </div>
                                    <div class="mb-2">
                                        <label>Ejemplares</label>
                                        <input type="number" name="ejemplares" value="<?= htmlspecialchars($libro['ejemplares']) ?>" required class="form-control">
                                    </div>
                                    <div class="mb-2">
                                        <label>Estado ID</label>
                                        <input type="number" name="estado_id" value="<?= htmlspecialchars($libro['estado_id']) ?>" required class="form-control">
                                    </div>
                                    <div class="mb-2">
                                        <label>Autores</label>
                                        <select name="autores[]" multiple size="5" class="form-select">
                                            <?php
                                            // Obtener autores seleccionados para este libro
                                            $autoresSeleccionados = [];
                                            $sqlAutoresLibro = "SELECT id_autor FROM libro_autor WHERE id_libro = " . $libro['id_libro'];
                                            $resAutoresLibro = $mysqli->query($sqlAutoresLibro);
                                            if ($resAutoresLibro) {
                                                while ($a = $resAutoresLibro->fetch_assoc()) {
                                                    $autoresSeleccionados[] = $a['id_autor'];
                                                }
                                            }
                                            // Mostrar todos los autores
                                            $resultAutores->data_seek(0); // Resetear puntero
                                            while ($autor = $resultAutores->fetch_assoc()):
                                            ?>
                                                <option value="<?= $autor['id_autor'] ?>" <?= in_array($autor['id_autor'], $autoresSeleccionados) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($autor['nombre']) ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-sm">Guardar Cambios</button>
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('edit-<?= $libro['id_libro'] ?>').style.display='none'">Cancelar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5">No hay libros registrados.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>