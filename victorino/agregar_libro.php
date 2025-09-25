<?php
session_start();
require_once 'conexion.php';

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Datos del libro
    $titulo           = $_POST['titulo'] ?? '';
    $editorial        = $_POST['editorial'] ?? '';
    $ciudad_editorial = $_POST['ciudad_editorial'] ?? '';
    $ano              = $_POST['ano'] ?? 0;
    $ISBN             = $_POST['ISBN'] ?? '';
    $id_especialidad  = $_POST['id_especialidad'] ?? 0;
    $contenido        = $_POST['contenido'] ?? '';
    $ejemplares       = $_POST['ejemplares'] ?? 0;
    $id_estado        = $_POST['id_estado'] ?? 0; // Recogemos el ID del select de estado (tabla 'estado')

    // --- Validar que el ID de especialidad exista ---
    $sqlValidateEspecialidad = "SELECT id_especialidad FROM especialidad WHERE id_especialidad = $id_especialidad";
    $resultValidateEspecialidad = $mysqli->query($sqlValidateEspecialidad);
    
    if (!$resultValidateEspecialidad || $resultValidateEspecialidad->num_rows === 0) {
        $error_message = "La especialidad seleccionada no existe en la base de datos.";
    } 
    
    // --- NUEVA VALIDACIÓN: Verificar que el ID de estado del libro exista en la tabla 'estado' ---
    $sqlValidateEstado = "SELECT id_estado FROM estado WHERE id_estado = $id_estado"; // ✅ Ahora referencia a la tabla 'estado'
    $resultValidateEstado = $mysqli->query($sqlValidateEstado);

    if (!$resultValidateEstado || $resultValidateEstado->num_rows === 0) {
        if (!empty($error_message)) { $error_message .= "<br>"; } // Añadir salto de línea si ya hay un error
        $error_message .= "El estado del libro seleccionado no existe en la base de datos (tabla 'estado').";
    }
    
    // Si no hay errores de validación, procedemos
    if (empty($error_message)) {
        // 1. Insertar el libro
        $sql = "INSERT INTO libros (titulo, editorial, ciudad_editorial, ano, ISBN, id_especialidad, contenido, ejemplares, id_estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("sssisiiii", 
                $titulo, 
                $editorial, 
                $ciudad_editorial, 
                $ano, 
                $ISBN, 
                $id_especialidad, 
                $contenido, 
                $ejemplares, 
                $id_estado // Usamos id_estado aquí
            );
            
            if ($stmt->execute()) {
                $id_libro_nuevo = $mysqli->insert_id;
                
                // 2. Insertar autores en libro_autor
                if (isset($_POST['autores']) && is_array($_POST['autores'])) {
                    $sql_autor = "INSERT INTO libro_autor (id_libro, id_autor) VALUES (?, ?)";
                    $stmt_autor = $mysqli->prepare($sql_autor);
                    
                    if ($stmt_autor) { 
                        foreach ($_POST['autores'] as $id_autor) {
                            $stmt_autor->bind_param("ii", $id_libro_nuevo, $id_autor);
                            $stmt_autor->execute();
                        }
                        $stmt_autor->close();
                    } else {
                        $error_message = "Error en la preparación de autores: " . $mysqli->error;
                    }
                }
                
                $_SESSION['mensaje'] = "Libro añadido exitosamente.";
                header("Location: gestion_libros.php");
                exit();
            } else {
                $error_message = "Error al añadir libro: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error_message = "Error en la preparación: " . $mysqli->error;
        }
    }
}

// Obtener especialidades para el select (para el HTML)
$especialidades = [];
$sqlEspecialidad = "SELECT id_especialidad, especialidad FROM especialidad ORDER BY especialidad ASC";
$resultEspecialidad = $mysqli->query($sqlEspecialidad);
if ($resultEspecialidad) {
    while ($row = $resultEspecialidad->fetch_assoc()) {
        $especialidades[] = $row;
    }
}

// Obtener estados de libro para el select (para el HTML)
$estados_libro = [];
// ✅ Corregido: Ahora obtenemos de la tabla 'estado'
$sqlEstado = "SELECT id_estado, estado FROM estado ORDER BY estado ASC"; 
$resultEstado = $mysqli->query($sqlEstado);
if ($resultEstado) {
    while ($row = $resultEstado->fetch_assoc()) {
        $estados_libro[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Agregar Libro</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h1 class="mb-4">Agregar Libro</h1>
  
  <?php if(!empty($error_message)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
  <?php endif; ?>
  
  <form method="POST">
    <!-- Campos del libro -->
    <div class="mb-3">
      <label class="form-label">Título</label>
      <input type="text" name="titulo" required class="form-control">
    </div>
    <div class="mb-3">
      <label class="form-label">Editorial</label>
      <input type="text" name="editorial" required class="form-control">
    </div>
    <div class="mb-3">
      <label class="form-label">Ciudad Editorial</label>
      <input type="text" name="ciudad_editorial" required class="form-control">
    </div>
    <div class="mb-3">
      <label class="form-label">Año</label>
      <input type="number" name="ano" required class="form-control">
    </div>
    <div class="mb-3">
      <label class="form-label">ISBN</label>
      <input type="text" name="ISBN" required class="form-control">
    </div>
    <!-- Select de Especialidad -->
    <div class="mb-3">
      <label class="form-label">Especialidad</label>
      <select name="id_especialidad" required class="form-control">
        <option value="">Seleccionar...</option>
        <?php foreach ($especialidades as $esp): ?>
          <option value="<?= $esp['id_especialidad'] ?>">
            <?= htmlspecialchars($esp['especialidad']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="mb-3">
      <label class="form-label">Contenido</label>
      <textarea name="contenido" class="form-control"></textarea>
    </div>
    <div class="mb-3">
      <label class="form-label">Ejemplares</label>
      <input type="number" name="ejemplares" required class="form-control">
    </div>
    <!-- Select de Estado del Libro -->
    <div class="mb-3">
      <label class="form-label">Estado del Libro</label>
      <select name="id_estado" required class="form-control"> <!-- ✅ Corregido: name="id_estado" -->
        <option value="">Seleccionar...</option>
        <?php foreach ($estados_libro as $estado_lib): ?>
          <option value="<?= $estado_lib['id_estado'] ?>">
            <?= htmlspecialchars($estado_lib['estado']) ?> <!-- ✅ Corregido: $estado_lib['estado'] -->
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <!-- Campo de Autores -->
    <div class="mb-3">
      <label class="form-label">Autores</label>
      <select name="autores[]" multiple class="form-control" required>
        <?php
        // Obtener autores para el select
        $sqlAutores = "SELECT id_autor, nombre FROM autor ORDER BY nombre ASC";
        $resultAutores = $mysqli->query($sqlAutores);
        while ($row = $resultAutores->fetch_assoc()):
        ?>
          <option value="<?= $row['id_autor'] ?>">
            <?= htmlspecialchars($row['nombre']) ?>
          </option>
        <?php endwhile; ?>
      </select>
      <small class="form-text">Mantén presionada Ctrl para seleccionar múltiples autores</small>
    </div>

    <button type="submit" class="btn btn-primary">Agregar Libro</button>
    <a href="gestion_libros.php" class="btn btn-secondary ms-2">Volver a Gestión</a>
  </form>
</div>
</body>
</html>