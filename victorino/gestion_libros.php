<?php
require_once 'conexion.php';
session_start();

// Verificar login (comenta esto si quieres probar sin login)
// if (!isset($_SESSION['usuario_id'])) {
//     header("Location: login.php");
//     exit();
// }

// ==========================================
// BLOQUE 1: PROCESAR ACCIONES (INSERTAR, ELIMINAR, ACTUALIZAR)
// Esto se ejecuta solo si se envía un formulario POST
// ==========================================
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['accion'])) {
    $accion = $_POST['accion'];

    // --------------------------------------
    // Sub-bloque: Insertar (Añadir) Libro
    // --------------------------------------
    if ($accion == 'agregar') {
        $titulo = $_POST['titulo'] ?? '';
        $editorial = $_POST['editorial'] ?? '';
        $ciudad_editorial = $_POST['ciudad_editorial'] ?? '';
        $ano = $_POST['ano'] ?? 0;
        $ISBN = $_POST['ISBN'] ?? '';
        $id_especialidad = $_POST['id_especialidad'] ?? 0;
        $contenido = $_POST['contenido'] ?? '';
        $ejemplares = $_POST['ejemplares'] ?? 0;
        $estado_id = $_POST['estado_id'] ?? 0;

        $sql = "INSERT INTO libros (titulo, editorial, ciudad_editorial, ano, ISBN, id_especialidad, contenido, ejemplares, estado_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("sssisissi", $titulo, $editorial, $ciudad_editorial, $ano, $ISBN, $id_especialidad, $contenido, $ejemplares, $estado_id);
            if ($stmt->execute()) {
                echo "<p style='color: green;'>Libro añadido exitosamente.</p>";
            } else {
                echo "<p style='color: red;'>Error al añadir libro: " . $stmt->error . "</p>";
            }
            $stmt->close();
        } else {
            echo "<p style='color: red;'>Error en la preparación: " . $mysqli->error . "</p>";
        }
    }

    // --------------------------------------
    // Sub-bloque: Eliminar Libro
    // --------------------------------------
    elseif ($accion == 'eliminar') {
        $id_libro = $_POST['id_libro'] ?? 0;
        $sql = "DELETE FROM libros WHERE id_libro = ?";
        $stmt = $mysqli->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("i", $id_libro);
            if ($stmt->execute()) {
                echo "<p style='color: green;'>Libro eliminado exitosamente.</p>";
            } else {
                echo "<p style='color: red;'>Error al eliminar libro: " . $stmt->error . "</p>";
            }
            $stmt->close();
        } else {
            echo "<p style='color: red;'>Error en la preparación: " . $mysqli->error . "</p>";
        }
    }

    // --------------------------------------
    // Sub-bloque: Actualizar Libro
    // --------------------------------------
    elseif ($accion == 'actualizar') {
        $id_libro = $_POST['id_libro'] ?? 0;
        $titulo = $_POST['titulo'] ?? '';
        $editorial = $_POST['editorial'] ?? '';
        $ciudad_editorial = $_POST['ciudad_editorial'] ?? '';
        $ano = $_POST['ano'] ?? 0;
        $ISBN = $_POST['ISBN'] ?? '';
        $id_especialidad = $_POST['id_especialidad'] ?? 0;
        $contenido = $_POST['contenido'] ?? '';
        $ejemplares = $_POST['ejemplares'] ?? 0;
        $estado_id = $_POST['estado_id'] ?? 0;

        $sql = "UPDATE libros SET titulo = ?, editorial = ?, ciudad_editorial = ?, ano = ?, ISBN = ?, id_especialidad = ?, contenido = ?, ejemplares = ?, estado_id = ? 
                WHERE id_libro = ?";
        $stmt = $mysqli->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("sssisissii", $titulo, $editorial, $ciudad_editorial, $ano, $ISBN, $id_especialidad, $contenido, $ejemplares, $estado_id, $id_libro);
            if ($stmt->execute()) {
                echo "<p style='color: green;'>Libro actualizado exitosamente.</p>";
            } else {
                echo "<p style='color: red;'>Error al actualizar libro: " . $stmt->error . "</p>";
            }
            $stmt->close();
        } else {
            echo "<p style='color: red;'>Error en la preparación: " . $mysqli->error . "</p>";
        }
    }
}

// ==========================================
// BLOQUE 2: LISTAR LIBROS (Consulta y muestra la tabla)
// Esto se ejecuta siempre para mostrar la lista
// ==========================================
$sql = "SELECT * FROM libros";
$result = $mysqli->query($sql);
if (!$result) {
    echo "<p style='color: red;'>Error al listar libros: " . $mysqli->error . "</p>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Gestión de Libros</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Gestión de Libros</h1>
        <a href="index.php">Volver</a>

        <!-- ========================================== -->
        <!-- BLOQUE HTML: Formulario para Añadir (Insertar) -->
        <!-- ========================================== -->
        <h2>Añadir Libro</h2>
        <form method="POST">
            <input type="hidden" name="accion" value="agregar">
            <input type="text" name="titulo" placeholder="Título" required>
            <input type="text" name="editorial" placeholder="Editorial" required>
            <input type="text" name="ciudad_editorial" placeholder="Ciudad Editorial" required>
            <input type="number" name="ano" placeholder="Año" required>
            <input type="text" name="ISBN" placeholder="ISBN" required>
            <input type="number" name="id_especialidad" placeholder="ID Especialidad" required>
            <textarea name="contenido" placeholder="Contenido"></textarea>
            <input type="number" name="ejemplares" placeholder="Ejemplares" required>
            <input type="number" name="estado_id" placeholder="Estado ID" required>
            <button type="submit">Añadir</button>
        </form>

        <!-- ========================================== -->
        <!-- BLOQUE HTML: Lista de Libros (con opciones para Eliminar y Actualizar) -->
        <!-- ========================================== -->
        <h2>Lista de Libros</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>ID Libro</th>
                    <th>Título</th>
                    <th>Editorial</th>
                    <th>Año</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id_libro']); ?></td>
                            <td><?php echo htmlspecialchars($row['titulo']); ?></td>
                            <td><?php echo htmlspecialchars($row['editorial']); ?></td>
                            <td><?php echo htmlspecialchars($row['ano']); ?></td>
                            <td>
                                <!-- Sub-bloque: Eliminar -->
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="accion" value="eliminar">
                                    <input type="hidden" name="id_libro" value="<?php echo $row['id_libro']; ?>">
                                    <button type="submit">Eliminar</button>
                                </form>
                                <!-- Sub-bloque: Actualizar (muestra formulario editable) -->
                                <button type="button" onclick="document.getElementById('edit-<?php echo $row['id_libro']; ?>').style.display='block'">Editar</button>
                                <div id="edit-<?php echo $row['id_libro']; ?>" style="display:none;">
                                    <form method="POST">
                                        <input type="hidden" name="accion" value="actualizar">
                                        <input type="hidden" name="id_libro" value="<?php echo $row['id_libro']; ?>">
                                        <input type="text" name="titulo" value="<?php echo htmlspecialchars($row['titulo']); ?>" required>
                                        <input type="text" name="editorial" value="<?php echo htmlspecialchars($row['editorial']); ?>" required>
                                        <input type="text" name="ciudad_editorial" value="<?php echo htmlspecialchars($row['ciudad_editorial']); ?>" required>
                                        <input type="number" name="ano" value="<?php echo htmlspecialchars($row['ano']); ?>" required>
                                        <input type="text" name="ISBN" value="<?php echo htmlspecialchars($row['ISBN']); ?>" required>
                                        <input type="number" name="id_especialidad" value="<?php echo htmlspecialchars($row['id_especialidad']); ?>" required>
                                        <textarea name="contenido"><?php echo htmlspecialchars($row['contenido']); ?></textarea>
                                        <input type="number" name="ejemplares" value="<?php echo htmlspecialchars($row['ejemplares']); ?>" required>
                                        <input type="number" name="estado_id" value="<?php echo htmlspecialchars($row['estado_id']); ?>" required>
                                        <button type="submit">Actualizar</button>
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
<?php
// Cerrar conexión (opcional, pero buena práctica)
$mysqli->close();
?>
</body>
</html>