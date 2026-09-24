<?php 
// 1. Conexión a la base de datos
require_once 'admin/config/conexion.php'; 

// 2. Configuración de Paginación (3 columnas x 2 filas = 6 podcasts por página)
$por_pagina = 6;
$pagina_actual = isset($_GET['p']) && (int)$_GET['p'] > 0 ? (int)$_GET['p'] : 1;
$offset = ($pagina_actual - 1) * $por_pagina;

// Obtener el total de podcasts para calcular las páginas
$sql_total = "SELECT COUNT(*) FROM podcasts";
$total_podcasts = (int)$pdo->query($sql_total)->fetchColumn();
$total_paginas = ceil($total_podcasts / $por_pagina);

// 3. Consulta de podcasts con límite para paginación
$sql = "SELECT * FROM podcasts ORDER BY fecha_publicacion DESC LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':limit', $por_pagina, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$podcasts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 4. Función para extraer la miniatura/portada (YouTube, Spotify, SoundCloud, iVoox, etc.)
function obtenerMiniaturaPodcast($url) {
    if (empty($url)) {
        return 'assets/images/default-video.png';
    }

    // A. YouTube: Mantener exactamente la lógica previa intacta
    if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $url, $match)) {
        return "https://img.youtube.com/vi/{$match[1]}/hqdefault.jpg";
    }

    // Contexto con User-Agent y timeout corto de 2 segundos para evitar ralentizaciones
    $contexto = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36\r\n",
            'timeout' => 2
        ]
    ]);

    // B. Spotify: Uso de la API oEmbed oficial y gratuita de Spotify
    if (strpos($url, 'spotify.com') !== false) {
        $oembedUrl = 'https://open.spotify.com/oembed?url=' . urlencode($url);
        $json = @file_get_contents($oembedUrl, false, $contexto);
        if ($json) {
            $data = json_decode($json, true);
            if (!empty($data['thumbnail_url'])) {
                return $data['thumbnail_url'];
            }
        }
    }

    // C. Genérico (OpenGraph/Twitter Cards): Para Apple Podcasts, iVoox, SoundCloud, etc.
    $html = @file_get_contents($url, false, $contexto);
    if ($html) {
        if (preg_match('/<meta[^>]+property=["\']og:image["\'][^>]+content=["\']([^"\']+)["\']/i', $html, $m) ||
            preg_match('/<meta[^>]+content=["\']([^"\']+)["\'][^>]+property=["\']og:image["\']/i', $html, $m) ||
            preg_match('/<meta[^>]+name=["\']twitter:image["\'][^>]+content=["\']([^"\']+)["\']/i', $html, $m)) {
            if (!empty($m[1])) {
                return html_entity_decode($m[1]);
            }
        }
    }

    // D. Imagen predeterminada si no se logró obtener la portada
    return 'assets/images/default-video.png'; 
}

// 5. Función para formatear la fecha
function formatearFechaPodcast($fecha_bd) {
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
                    <h2 class="title-big">Podcasts</h2>
                    <div class="breadcrumb">
                        <ul>
                            <li><a href="index.php">Inicio</a></li>
                            <li class="active">Podcasts</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Listado de Podcasts (Grid 3x2) -->
<div class="grids-block-5 py-5">
    <section class="py-lg-4 py-md-3">
        <div class="container">
            <div class="row">
                
                <?php if (!empty($podcasts)): ?>
                    <?php foreach ($podcasts as $podcast): 
                        $url_video = htmlspecialchars($podcast['url_embed']);
                        $miniatura = obtenerMiniaturaPodcast($podcast['url_embed']);
                    ?>
                    
                    <div class="col-lg-4 col-md-6 grids5-info mb-5">
                        <!-- Vista previa del Podcast -->
                        <a target="_blank" href="<?php echo $url_video; ?>" class="d-block position-relative">
                            <img src="<?php echo $miniatura; ?>" 
                                 alt="<?php echo htmlspecialchars($podcast['titulo']); ?>" 
                                 class="img-fluid w-100 radius-image" 
                                 style="aspect-ratio: 16/9; object-fit: cover; object-position: center; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);" />
                                 
                            <!-- Botón de Play SVG Nativo -->
                            <div class="play-icon" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(0,0,0,0.7); border-radius: 50%; width: 56px; height: 56px; display: flex; align-items: center; justify-content: center;">
                                <svg width="20" height="22" viewBox="0 0 20 22" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-left: 3px;">
                                    <path d="M19 11L1 1V21L19 11Z" fill="white"/>
                                </svg>
                            </div>
                        </a>
                        
                        <div class="blog-info mt-3">
                            <!-- Título del Podcast -->
                            <h4 class="mb-2" style="font-size: 1.1rem; font-weight: bold; line-height: 1.4;">
                                <a target="_blank" href="<?php echo $url_video; ?>" style="color: inherit;">
                                    <?php echo htmlspecialchars($podcast['titulo']); ?>
                                </a>
                            </h4>
                            
                            <!-- Fecha limpia -->
                            <h5><?php echo formatearFechaPodcast($podcast['fecha_publicacion']); ?></h5>
                            
                            <!-- Botón Ver Podcast -->
                            <a target="_blank" href="<?php echo $url_video; ?>" class="btn mt-3 p-0">
                                Ver podcast 
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#e60000" viewBox="0 0 24 24">
                                    <path d="M5 12h11.17l-4.88-4.88c-.39-.39-.39-1.02 0-1.41.39-.39 1.02-.39 1.41 0l6.59 6.59c.39.39.39 1.02 0 1.41l-6.59 6.59c-.39.39-1.02.39-1.41 0-.39-.39-.39-1.02 0-1.41L16.17 13H5c-.55 0-1-.45-1-1s.45-1 1-1z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                    
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <p>No hay podcasts disponibles por el momento.</p>
                    </div>
                <?php endif; ?>

            </div>

            <!-- Paginación Dinámica -->
            <?php if ($total_paginas > 1): ?>
            <div class="pagination mt-4 text-center">
                <ul>
                    <?php if ($pagina_actual > 1): ?>
                        <li class="prev"><a href="podcast.php?p=<?php echo $pagina_actual - 1; ?>">Ant</a></li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                        <li>
                            <a href="podcast.php?p=<?php echo $i; ?>" class="<?php echo ($i == $pagina_actual) ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($pagina_actual < $total_paginas): ?>
                        <li class="next"><a href="podcast.php?p=<?php echo $pagina_actual + 1; ?>">Sig</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <?php endif; ?>

        </div>
    </section>
</div>

<?php include 'includes/footer.php'; ?>