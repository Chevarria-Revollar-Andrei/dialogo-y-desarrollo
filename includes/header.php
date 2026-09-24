<?php
// Detecta el archivo PHP actual en el que está navegando el usuario
$pagina_actual = basename($_SERVER['PHP_SELF']);
?>
<!doctype html>
<html lang="es">
  <head>
    <!-- Meta tags requeridos -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>DDP Noticias - Diálogo y Desarrollo Perú</title>

    <!-- Google fonts -->
    <link href="https://fonts.googleapis.com/css?family=Cabin:400,500,600&amp;subset=latin-ext,vietnamese" rel="stylesheet">
    
    <!-- Template CSS -->
    <link rel="stylesheet" href="assets/css/style-starter.css">

    <!-- Estilo personalizado para resaltar el ítem activo en color rojo -->
    <style>
      .navbar-expand-lg .navbar-nav .nav-item.active .nav-link {
          color: #e60000 !important;
          font-weight: 600;
      }
    </style>
  </head>
  <body>

<!-- Header / Menú de Navegación -->
<header id="site-header" class="fixed-top">
  <div class="container">
      <nav class="navbar navbar-expand-lg stroke">
          <a class="navbar-brand" href="index.php">
              <img src="assets/images/logo.png" alt="DDP Logo" title="Diálogo y Desarrollo Perú" style="height:75px;" />
          </a> 

          <button class="navbar-toggler collapsed bg-gradient" type="button" data-toggle="collapse"
              data-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false"
              aria-label="Toggle navigation">
              <span class="navbar-toggler-icon fa icon-expand fa-bars"></span>
              <span class="navbar-toggler-icon fa icon-close fa-times"></span>
          </button>

          <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
              <ul class="navbar-nav ml-auto">
                  <li class="nav-item <?= ($pagina_actual == 'index.php') ? 'active' : '' ?>">
                      <a class="nav-link" href="index.php">Inicio</a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link" href="index.php#actualidad">Actualidad</a>
                  </li>
                  <li class="nav-item <?= ($pagina_actual == 'reportajes.php' || $pagina_actual == 'reportaje-detalle.php') ? 'active' : '' ?>">
                      <a class="nav-link" href="reportajes.php">Reportajes</a>
                  </li>
                  <li class="nav-item <?= ($pagina_actual == 'podcast.php') ? 'active' : '' ?>">
                      <a class="nav-link" href="podcast.php">Podcast</a>
                  </li>
                                    <li class="nav-item <?= ($pagina_actual == 'boletin.php' || $pagina_actual == 'boletines.php') ? 'active' : '' ?>">
                      <a class="nav-link" href="boletin.php">Boletín NTEP</a>
                  </li>
                  <li class="nav-item <?= ($pagina_actual == 'alianzas.php') ? 'active' : '' ?>">
                      <a class="nav-link" href="alianzas.php">Alianzas</a>
                  </li>
                  <li class="nav-item <?= ($pagina_actual == 'sobre-dd.php') ? 'active' : '' ?>">
                      <a class="nav-link" href="sobre-dd.php">Sobre D&D</a>
                  </li>               
                  <li class="ml-2">
                      <a href="contacto.php" class="btn btn-style btn-outline-secondary <?= ($pagina_actual == 'contacto.php') ? 'active' : '' ?>">Contacto</a>
                  </li>
              </ul>
          </div>
      </nav>
  </div>
</header>
<!-- //Header -->