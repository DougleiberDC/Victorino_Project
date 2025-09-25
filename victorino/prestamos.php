<?php
session_start();
require_once 'conexion.php';

// Si quieres exigir login, descomenta:
// if (!isset($_SESSION['usuario_id'])) { header("Location: login.php"); exit(); }

$mensajeError = "";
$mensajeOk = "";

// -------------------------
// 1) ACCIONES (POST)
// -------------------------
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $accion = $_POST['accion'] ?? '';

    // Registrar un nuevo préstamo
    if ($accion === 'registrar') {
        // Recogemos los datos y los asignamos a variables simples
        $idUsuario       = $_POST['id_usuario'] ?? 0;
        $idLibro         = $_POST['id_libro'] ?? 0;
        $fechaPrestamo   = $_POST['fecha_prestamo'] ?? '';
        $fechaDevolucion = $_POST['fecha_devolucion'] ?? '';
        $tipoPrestamo    = $_POST['tipo'] ?? '';

        // Validación básica (solo verificar que no estén vacíos)
        if (!$idUsuario || !$idLibro || !$fechaPrestamo || !$fechaDevolucion || !$tipoPrestamo) {
            $mensajeError = "Faltan datos importantes para el registro.";
        } elseif ($fechaDevolucion < $fechaPrestamo) {
            $mensajeError = "La fecha de devolución no puede ser anterior al préstamo.";
        } else {
            // ESTADO INICIAL: 1 = Activo. (Usamos concatenación directa - PELIGROSO SI NO ES ASÍ)
            $estadoInicial = 1;

            // Insert en tabla 'prestamo' (CONCATENACIÓN DIRECTA - PELIGROSO)
            $sqlPrestamo = "INSERT INTO prestamo (id_usuario, fecha_prestamo, fecha_devolucion, estado_prestamo_id) 
                            VALUES ('$idUsuario', '$fechaPrestamo', '$fechaDevolucion', '$estadoInicial')";
            
            if ($mysqli->query($sqlPrestamo)) {
                $nuevoIdPrestamo = $mysqli->insert_id;

                // Insert en tabla 'detalle_prestamo' (CONCATENACIÓN DIRECTA - PELIGROSO)
                $sqlDetalle = "INSERT INTO detalle_prestamo (id_prestamo, id_libro, tipo) 
                               VALUES ('$nuevoIdPrestamo', '$idLibro', '$tipoPrestamo')";
                
                if ($mysqli->query($sqlDetalle)) {
                    $mensajeOk = "Préstamo registrado correctamente.";
                } else {
                    $mensajeError = "Error al guardar el detalle del préstamo. Error: " . $mysqli->error;
                }
            } else {
                $mensajeError = "Error al registrar el préstamo principal. Error: " . $mysqli->error;
            }
        }
    }

    // Cambiar estado del préstamo
    if ($accion === 'actualizar_estado') {
        $idPrestamo = $_POST['id_prestamo'];
        $nuevoEstado = $_POST['estado'];

        // Consulta SQL simple (CONCATENACIÓN DIRECTA - PELIGROSO)
        $sql = "UPDATE prestamo SET estado_prestamo_id = '$nuevoEstado' WHERE id_prestamo = '$idPrestamo'";
        
        if ($mysqli->query($sql)) {
            $mensajeOk = "Estado actualizado.";
        } else {
            $mensajeError = "No se pudo actualizar el estado.";
        }
    }

    // Eliminar préstamo
    if ($accion === 'eliminar') {
        $idPrestamo = $_POST['id_prestamo'];

        // Borra el detalle primero
        $sqlDelDet = "DELETE FROM detalle_prestamo WHERE id_prestamo = '$idPrestamo'";
        $mysqli->query($sqlDelDet);

        // Borra el préstamo
        $sqlDelPrest = "DELETE FROM prestamo WHERE id_prestamo = '$idPrestamo'";
        if ($mysqli->query($sqlDelPrest)) {
            $mensajeOk = "Préstamo eliminado.";
        } else {
            $mensajeError = "Error al eliminar el préstamo.";
        }
    }
}

// -------------------------
// 2) LISTADO
// -------------------------
// Consulta SQL (CONCATENACIÓN DIRECTA - PELIGROSO)
$sqlListado = "
SELECT 
    p.id_prestamo, u.nombre, u.apellido, l.titulo, p.fecha_prestamo, p.fecha_devolucion, ep.estado, dp.tipo
FROM prestamo p
JOIN usuarios u        ON p.id_usuario = u.usuario_id
JOIN detalle_prestamo dp ON p.id_prestamo = dp.id_prestamo
JOIN libros l          ON dp.id_libro = l.id_libro
JOIN estado_prestamo ep ON p.estado_prestamo_id = ep.id_estado
ORDER BY p.fecha_prestamo DESC
";
$listado = $mysqli->query($sqlListado);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Gestión de Préstamos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
  <h1 class="mb-3">Gestión de Préstamos</h1>
  <a href="index.php" class="btn btn-secondary mb-3">Volver al inicio</a>

  <?php if ($mensajeError): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($mensajeError) ?></div>
  <?php endif; ?>
  <?php if ($mensajeOk): ?>
    <div class="alert alert-success"><?= htmlspecialchars($mensajeOk) ?></div>
  <?php endif; ?>

  <!-- Formulario de Registro -->
  <h3>Registrar Nuevo Préstamo</h3>
  <form method="POST" class="row g-3 mb-4 border p-3 rounded">
    <input type="hidden" name="accion" value="registrar">

    <div class="col-md-3">
      <label class="form-label">Usuario ID</label>
      <input type="number" name="id_usuario" required class="form-control" value="1">
    </div>
    <div class="col-md-3">
      <label class="form-label">Libro ID</label>
      <input type="number" name="id_libro" required class="form-control" value="1">
    </div>
    <div class="col-md-3">
      <label class="form-label">Fecha Préstamo</label>
      <input type="date" name="fecha_prestamo" required class="form-control">
    </div>
    <div class="col-md-3">
      <label class="form-label">Fecha Devolución</label>
      <input type="date" name="fecha_devolucion" required class="form-control">
    </div>
    <div class="col-md-3">
      <label class="form-label">Tipo</label>
      <select name="tipo" class="form-select" required>
        <option value="Domiciliario">Domiciliario</option>
        <option value="Sala">Sala</option>
      </select>
    </div>
    <div class="col-12">
      <button type="submit" class="btn btn-primary">Registrar Préstamo</button>
    </div>
  </form>

  <!-- Lista de préstamos -->
  <h3 class="mt-5">Lista de Préstamos</h3>
  <table class="table table-striped">
    <thead>
      <tr>
        <th>ID</th>
        <th>Usuario</th>
        <th>Libro</th>
        <th>F. Préstamo</th>
        <th>F. Devolución</th>
        <th>Estado</th>
        <th>Tipo</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
    <?php if ($listado && $listado->num_rows): ?>
      <?php while ($fila = $listado->fetch_assoc()): ?>
        <tr>
          <td><?= $fila['id_prestamo'] ?></td>
          <td><?= htmlspecialchars($fila['nombre'] . ' ' . $fila['apellido']) ?></td>
          <td><?= htmlspecialchars($fila['titulo']) ?></td>
          <td><?= $fila['fecha_prestamo'] ?></td>
          <td><?= $fila['fecha_devolucion'] ?></td>
          <td><?= $fila['estado'] ?></td>
          <td><?= $fila['tipo'] ?></td>
          <td class="d-flex gap-2">
            <!-- Cambiar estado -->
            <form method="POST" class="d-flex gap-2">
              <input type="hidden" name="accion" value="actualizar_estado">
              <input type="hidden" name="id_prestamo" value="<?= $fila['id_prestamo'] ?>">
              <select name="estado" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Cambiar...</option>
                <option value="1">Activo</option>
                <option value="2">Vencido</option>
                <option value="3">Devuelto</option>
                <option value="4">Perdido</option>
              </select>
            </form>
            <!-- Eliminar -->
            <form method="POST" onsubmit="return confirm('¿Seguro que desea eliminar este préstamo?');">
              <input type="hidden" name="accion" value="eliminar">
              <input type="hidden" name="id_prestamo" value="<?= $fila['id_prestamo'] ?>">
              <button class="btn btn-danger btn-sm">Eliminar</button>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr><td colspan="8">Sin préstamos registrados.</td></tr>
    <?php endif; ?>
    </tbody>
  </table>
</div>
<?php $mysqli->close(); ?>
</body>
</html>