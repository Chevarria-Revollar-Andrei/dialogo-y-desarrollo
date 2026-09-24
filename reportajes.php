<?php 
// Incluimos tu conexión a la base de datos (Usando tu variable $pdo)
require_once 'admin/config/conexion.php'; 

// Incluimos tu header modular
include 'includes/header.php'; 

// --- LÓGICA DE PAGINACIÓN ---
$por_pagina = 9; // Cuántos reportajes mostrar por página
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if($pagina < 1) $pagina = 1;
$offset = ($pagina - 1) * $por_pagina;

// Contar el total de reportajes para calcular las páginas usando PDO
$total_query = $pdo->query("SELECT COUNT(*) as total FROM reportajes");
$total_row = $total_query->fetch(PDO::FETCH_ASSOC);
$total_paginas = ceil($total_row['total'] / $por_pagina);

// --- CONSULTA A LA BASE DE DATOS (PDO) ---
// Traemos los reportajes ordenados por fecha de publicación (los más nuevos primero)
$sql = "SELECT id, titulo, foto_principal, fecha_publicacion 
        FROM reportajes 
        ORDER BY fecha_publicacion DESC 
        LIMIT $por_pagina OFFSET $offset";
$resultado = $pdo->query($sql);

// Función rápida para formatear la fecha a español (Ej: Ago 18, 2026)
function formatearFecha($fecha_bd) {
    $meses = ['01'=>'Ene', '02'=>'Feb', '03'=>'Mar', '04'=>'Abr', '05'=>'May', '06'=>'Jun', '07'=>'Jul', '08'=>'Ago', '09'=>'Sep', '10'=>'Oct', '11'=>'Nov', '12'=>'Dic'];
    $fecha = strtotime($fecha_bd);
    $mes = $meses[date('m', $fecha)];
    return $mes . " " . date('d', $fecha) . ", " . date('Y', $fecha);
}
?>

<!-- Estilos generales para íconos rojos, enlaces "Leer" y Pestaña Roja en Imágenes -->
<style>
.btn-leer {
    color: #e60000 !important;
    font-weight: bold;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
}
.btn-leer:hover {
    color: #b30000 !important;
}

/* Estilo para la pestaña roja en la esquina superior derecha */
.img-red-corner {
    position: relative;
    display: block;
    overflow: hidden;
    border-radius: 8px;
}
.img-red-corner img {
    width: 100%;
    height: auto;
    display: block;
}
.img-red-corner::after {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 65px;
    height: 65px;
    background-color: #e60000;
    border-bottom-left-radius: 100%;
    pointer-events: none;
    z-index: 2;
}
</style>

<!-- Título / Header de Sección -->
<section class="breadcrumb-area py-sm-5 py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb-contents">
                    <h2 class="title-big">Reportajes</h2>
                    <div class="breadcrumb">
                        <ul>
                            <li><a href="index.php">Inicio</a></li>
                            <li class="active">Reportajes</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="grids-block-5 py-5">
    <section class="py-lg-4 py-md-3">
        <div class="container">
            <div class="row">
                
                <?php if($resultado && $resultado->rowCount() > 0): ?>
                    <?php while($row = $resultado->fetch(PDO::FETCH_ASSOC)): ?>
                        <?php 
                            // Construir ruta de la imagen según tu estructura
                            // Cambia la ruta para incluir la carpeta "fotos/"
                            $ruta_foto = !empty($row['foto_principal']) ? 'admin/' . $row['foto_principal'] : 'assets/images/default.jpg';
                            // Construir URL dinámica (envía el ID a un archivo detalle)
                            $url_reportaje = 'reportaje_detalle.php?id=' . $row['id'];
                        ?>
                        
                        <div class="col-lg-4 col-md-6 grids5-info mt-5">
                            <a href="<?php echo $url_reportaje; ?>" class="d-block img-red-corner">
                                <img src="<?php echo $ruta_foto; ?>" alt="<?php echo htmlspecialchars($row['titulo']); ?>" class="img-fluid" />
                            </a>
                            <div class="blog-info">
                                <h5><?php echo formatearFecha($row['fecha_publicacion']); ?></h5>
                                <h4>
                                    <a href="<?php echo $url_reportaje; ?>" class="d-block">
                                        <?php echo htmlspecialchars($row['titulo']); ?>
                                    </a>
                                </h4>
                                <a href="<?php echo $url_reportaje; ?>" class="btn mt-4 p-0">Leer 
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#e60000" viewBox="0 0 24 24">
                                        <path d="M5 12h11.17l-4.88-4.88c-.39-.39-.39-1.02 0-1.41.39-.39 1.02-.39 1.41 0l6.59 6.59c.39.39.39 1.02 0 1.41l-6.59 6.59c-.39.39-1.02.39-1.41 0-.39-.39-.39-1.02 0-1.41L16.17 13H5c-.55 0-1-.45-1-1s.45-1 1-1z"/>
                                    </svg>
                                </a>
                            </div>
                        </div>

                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12 text-center mt-5">
                        <p>No hay reportajes publicados por el momento.</p>
                    </div>
                <?php endif; ?>

            </div>

            <!-- PAGINACIÓN DINÁMICA -->
            <?php if($total_paginas > 1): ?>
            <div class="pagination mt-5">
                <ul>
                    <!-- Botón Anterior -->
                    <?php if($pagina > 1): ?>
                        <li class="prev"><a href="reportajes.php?pagina=<?php echo $pagina - 1; ?>"> Ant</a></li>
                    <?php endif; ?>

                    <!-- Números de Página -->
                    <?php for($i = 1; $i <= $total_paginas; $i++): ?>
                        <li>
                            <a href="reportajes.php?pagina=<?php echo $i; ?>" class="<?php echo ($i == $pagina) ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <!-- Botón Siguiente -->
                    <?php if($pagina < $total_paginas): ?>
                        <li class="next"><a href="reportajes.php?pagina=<?php echo $pagina + 1; ?>"> Sig </a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <?php endif; ?>

        </div>
    </section>
</div>

<?php include 'includes/footer.php'; ?>