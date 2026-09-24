<?php
// panel.php
require_once 'includes/header.php';
?>

<main class="content">
    <?php
    // Sistema de rutas básico en PHP
    $modulo = isset($_GET['modulo']) ? $_GET['modulo'] : 'dashboard';
    
    // Ruta donde buscaremos los archivos visuales
    $archivo_modulo = "modules/" . $modulo . ".php";

    // Si el archivo existe, lo incluimos
    if (file_exists($archivo_modulo)) {
        include $archivo_modulo;
    } else {
        // Si no existe, mostramos un error amigable
        echo "<div class='page-head'><h1>En construcción</h1><p>El módulo '$modulo' aún no ha sido creado.</p></div>";
    }
    ?>
</main>

<?php
require_once 'includes/footer.php';
?>