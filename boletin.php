<?php 
// 1. Conexión a la base de datos
require_once 'admin/config/conexion.php'; 

// 2. Configuración de Paginación (3 columnas x 2 filas = 6 boletines por página)
$por_pagina = 6;
$pagina_actual = isset($_GET['p']) && (int)$_GET['p'] > 0 ? (int)$_GET['p'] : 1;
$offset = ($pagina_actual - 1) * $por_pagina;

// Obtener el total de boletines para calcular las páginas
$sql_total = "SELECT COUNT(*) FROM boletines";
$total_boletines = (int)$pdo->query($sql_total)->fetchColumn();
$total_paginas = ceil($total_boletines / $por_pagina);

// 3. Consulta de boletines con límite para paginación
$sql = "SELECT * FROM boletines ORDER BY fecha_publicacion DESC LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':limit', $por_pagina, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$boletines = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 4. Función para resolver rutas según la subcarpeta ('fotos' o 'pdfs')
function resolverRuta($archivo, $subcarpeta = 'fotos') {
    if (empty($archivo)) return '#';
    if (strpos($archivo, 'admin/') === 0) return $archivo;
    if (strpos($archivo, 'uploads/') === 0) return 'admin/' . $archivo;
    return 'admin/uploads/boletines/' . $subcarpeta . '/' . ltrim($archivo, '/');
}

// 5. Función para formatear la fecha
function formatearFechaBoletin($fecha_bd) {
    if (empty($fecha_bd)) return '';
    $meses = ['01'=>'Ene', '02'=>'Feb', '03'=>'Mar', '04'=>'Abr', '05'=>'May', '06'=>'Jun', '07'=>'Jul', '08'=>'Ago', '09'=>'Sep', '10'=>'Oct', '11'=>'Nov', '12'=>'Dic'];
    $fecha = strtotime($fecha_bd);
    $mes = $meses[date('m', $fecha)];
    return $mes . " " . date('d', $fecha) . ", " . date('Y', $fecha);
}

include 'includes/header.php'; 
?>

<!-- Breadcrumb -->
<section class="breadcrumb-area py-sm-5 py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb-contents">
                    <h2 class="title-big">Boletines NTEP</h2>
                    <div class="breadcrumb">
                        <ul>
                            <li><a href="index.php">Inicio</a></li>
                            <li class="active">Boletines</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Listado de Boletines (Grid 3x2) -->
<div class="grids-block-5 py-5">
    <section class="py-lg-4 py-md-3">
        <div class="container">
            <div class="row">
                
                <?php if (!empty($boletines)): ?>
                    <?php foreach ($boletines as $boletin): 
                        // Ruta al PDF dentro de 'admin/uploads/boletines/pdfs/'
                        $ruta_pdf = resolverRuta($boletin['archivo_pdf'], 'pdfs');
                        
                        // Ruta a la Foto dentro de 'admin/uploads/boletines/fotos/'
                        $ruta_foto = !empty($boletin['foto_portada']) 
                            ? resolverRuta($boletin['foto_portada'], 'fotos') 
                            : 'assets/images/default.png';
                    ?>
                    
                    <div class="col-lg-4 col-md-6 grids5-info mb-5">
                        <a target="_blank" href="<?php echo htmlspecialchars($ruta_pdf); ?>" class="d-block">
                            <!-- Estándar de tamaño forzado con height y object-fit -->
                            <img src="<?php echo htmlspecialchars($ruta_foto); ?>" 
                                 alt="Boletín" 
                                 class="img-fluid w-100 radius-image" 
                                 style="height: 380px; object-fit: cover; object-position: top; border: 1px solid #e2e2e2; box-shadow: 0 4px 6px rgba(0,0,0,0.08);" />
                        </a>
                        
                        <div class="blog-info">
                            <h5 class="mt-3"><?php echo formatearFechaBoletin($boletin['fecha_publicacion']); ?></h5>
                            
                            <a target="_blank" href="<?php echo htmlspecialchars($ruta_pdf); ?>" class="btn mt-3 p-0">
                                Ver boletín 
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#e60000" viewBox="0 0 24 24">
                            <path d="M5 12h11.17l-4.88-4.88c-.39-.39-.39-1.02 0-1.41.39-.39 1.02-.39 1.41 0l6.59 6.59c.39.39.39 1.02 0 1.41l-6.59 6.59c-.39.39-1.02.39-1.41 0-.39-.39-.39-1.02 0-1.41L16.17 13H5c-.55 0-1-.45-1-1s.45-1 1-1z"/>
                        </svg></span> 
                            </a>
                        </div>
                    </div>
                    
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <p>No hay boletines disponibles por el momento.</p>
                    </div>
                <?php endif; ?>

            </div>

            <!-- Paginación Dinámica -->
            <?php if ($total_paginas > 1): ?>
            <div class="pagination mt-4 text-center">
                <ul>
                    <?php if ($pagina_actual > 1): ?>
                        <li class="prev"><a href="boletin.php?p=<?php echo $pagina_actual - 1; ?>">Ant</a></li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                        <li>
                            <a href="boletin.php?p=<?php echo $i; ?>" class="<?php echo ($i == $pagina_actual) ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($pagina_actual < $total_paginas): ?>
                        <li class="next"><a href="boletin.php?p=<?php echo $pagina_actual + 1; ?>">Sig</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <?php endif; ?>

        </div>
    </section>
</div>

<?php include 'includes/footer.php'; ?>