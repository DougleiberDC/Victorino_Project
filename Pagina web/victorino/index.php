<?php


?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sistema de Biblioteca</title>
  <meta name="description" content="Sistema de gestión para biblioteca">
  <link rel="stylesheet" href="styles.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
  <!-- Iconos de Lucide -->
  <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
  <form action="" method="get">
  <div class="flex-container">
    <!-- Header -->
     <header class="bg-dark-subtle">
    <?php
    include("templates/header.php");
    ?>
    </header>

    <!-- Main Content -->
    <main class="main-content">
      <!-- Hero Section -->
      <section class="hero-section">
        <div class="container">
          <div class="hero-content">
            <div class="hero-text">
              <h1 class="hero-title">Sistema de Gestión de Biblioteca</h1>
              <p class="hero-description">
                Acceda a todas las funcionalidades del sistema de biblioteca desde este panel principal.
              </p>
            </div>

            <div class="search-container">
              <div class="search-box">
                <input type="search" name="busqueda" id="busqueda-input" placeholder="Buscar libros, usuarios..." class="search-input" value="<?php echo isset($_GET['busqueda']) ? htmlspecialchars($_GET['busqueda']) : ''; ?>">
                <select name="filtro" id="filtro">
                  <option value="titulo">Título</option>
                  <option value="autor">Autor</option>
                  <option value="ano">Año</option>
                </select>
                <button type="submit" name="enviar" class="btn btn-icon search-button">
                  <i data-lucide="search" class="icon-sm"></i>
                  <span class="sr-only">Buscar</span>
                </button>
                <?php if(isset($_GET['busqueda']) && !empty($_GET['busqueda'])): ?>
                  <a href="index.php" class="btn btn-icon clear-button">
                    <i data-lucide="x" class="icon-sm"></i>
                    <span class="sr-only">Limpiar</span>
                  </a>
                <?php endif; ?>
              </div>
            </div>
                
            <!-- Mostrar resultados -->
            <div class="m-5">
              <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-3" style="min-width: 77vw;" id="resultados-container">
                <?php include("buscador.php"); ?>
              </div>
            </div>

          </div>
        </div>
      </section>

      <div class="m-5 d-none">
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-3">
        <?php foreach($lista_libros as $libro): ?>
          <div class="col">
            <div class="card fluid ">

              <img src="imagenes/libro_1.png" class="img-thumbnail img-fluid border-secondary-subtle" alt="img">

              <div class="card-body">
                <p class=" ms-2">Autor: <i><?php echo $libro['nombre']; ?></i></p>
                <h4 class="card-title ms-2"><b><?php echo $libro['titulo']; ?></b></h4>
                <p class=" ms-2"><strong>Año: <?php echo $libro['ano']; ?></strong><br>
                <strong>Ejemplares: <?php echo $libro['ejemplares'];?></strong></p>
              </div>

            </div>
          </div>
        <?php endforeach; ?>
        </div>
      </div-->
    </main>
    </form>

    <!-- Footer -->
    <footer class="footer bg-dark-subtle">
      <?php
      include("templates/footer.php");
      ?>
    </footer>
  </div>


<script>
  document.getElementById('busqueda-input').addEventListener('input', function() {
    const busqueda = this.value;
    const filtro = document.getElementById('filtro').value;

    // Realizar petición AJAX
    const xhr = new XMLHttpRequest();
    xhr.open('GET', `buscador.php?busqueda=${encodeURIComponent(busqueda)}&filtro=${filtro}`, true);

    xhr.onload = function() {
      if (this.status === 200) {
        document.getElementById('resultados-container').innerHTML = this.responseText;
        // Re-inicializar iconos de Lucide
        lucide.createIcons();
      }
    };

    xhr.send();
  });
</script>
<script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
        integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
        crossorigin="anonymous"
></script>
</body>
</html>
