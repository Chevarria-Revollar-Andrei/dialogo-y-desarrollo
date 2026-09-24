<?php 
// 1. CONEXIÓN A LA BASE DE DATOS
require_once 'admin/config/conexion.php'; 

// Función auxiliar para fechas en español
function formatearFecha($fecha) {
    $meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
    $timestamp = strtotime($fecha);
    return $meses[date('n', $timestamp) - 1] . ' ' . date('d, Y', $timestamp);
}

// Función auxiliar para obtener la miniatura de YouTube desde la URL
function getYoutubeThumb($url) {
    preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $url, $match);
    $video_id = $match[1] ?? '';
    return $video_id ? "https://img.youtube.com/vi/{$video_id}/hqdefault.jpg" : 'assets/images/podcast.png';
}

// Función auxiliar para convertir URLs a formato embed para reproducción en sitio
if (!function_exists('obtenerEmbedUrl')) {
    function obtenerEmbedUrl($url) {
        if (empty($url)) return '';
        if (preg_match('/src=["\']([^"\']+)["\']/i', $url, $m)) {
            $url = $m[1];
        }
        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $url, $match)) {
            return "https://www.youtube.com/embed/{$match[1]}?autoplay=1";
        }
        if (strpos($url, 'spotify.com') !== false) {
            if (strpos($url, '/embed/') === false) {
                return preg_replace('#spotify\.com/(episode|track|show|playlist)/#i', 'spotify.com/embed/$1/', $url);
            }
            return $url;
        }
        if (preg_match('/vimeo\.com\/(?:video\/)?(\d+)/i', $url, $match)) {
            return "https://player.vimeo.com/video/{$match[1]}?autoplay=1";
        }
        return $url;
    }
}

// 2. CONSULTAS A LA BASE DE DATOS (Usando PDO)
// Reportajes: Traemos los 3 últimos
$sql_reportajes = "SELECT id, titulo, resumen_corto, foto_principal, fecha_publicacion FROM reportajes ORDER BY fecha_publicacion DESC, id DESC LIMIT 3";
$res_reportajes = $pdo->query($sql_reportajes);
$reportajes = $res_reportajes->fetchAll(PDO::FETCH_ASSOC);

// El principal será el más reciente (el índice 0)
$reportaje_principal = $reportajes[0] ?? null;
// Los secundarios serán los 3 que acabamos de traer (incluyendo el principal)
$reportajes_secundarios = $reportajes;

// Noticias: Traemos las 3 últimas
$sql_noticias = "SELECT id, titulo, foto, link_externo, fecha_publicacion FROM noticias ORDER BY fecha_publicacion DESC, id DESC LIMIT 3";
$res_noticias = $pdo->query($sql_noticias);

// Boletín: Traemos solo el último
$sql_boletin = "SELECT numero_boletin, resumen, foto_portada, archivo_pdf, fecha_publicacion FROM boletines ORDER BY fecha_publicacion DESC, id DESC LIMIT 1";
$res_boletin = $pdo->query($sql_boletin);
$boletin = $res_boletin->fetch(PDO::FETCH_ASSOC);

// Podcasts: Traemos los 4 últimos
$sql_podcasts = "SELECT titulo, url_embed FROM podcasts ORDER BY fecha_publicacion DESC, id DESC LIMIT 4";
$res_podcasts = $pdo->query($sql_podcasts);

// Videos (Carrusel): Traemos todos los videos
$sql_videos = "SELECT titulo, url_embed FROM videos ORDER BY fecha_publicacion DESC";
$res_videos = $pdo->query($sql_videos);

include 'includes/header.php'; 
?>

<!-- Estilos generales para íconos rojos y enlaces "Leer" -->
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
</style>

<!-- Título / Header de Sección -->
<section class="breadcrumb-area py-sm-5 py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb-contents">
                    <h2 class="title-big">Reportajes</h2>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Reportaje Principal -->
<?php if ($reportaje_principal): ?>
<section class="w3l-video w3l-homeblock3" id="video">
    <div class="container-fluid">
        <div class="video-grids-info row">
            <div class="video-gd-right col-lg-6 p-0">
                <div class="position-relative">
                    <a href="reportaje-detalle.php?id=<?= $reportaje_principal['id'] ?>">
                        <img src="admin/<?= htmlspecialchars($reportaje_principal['foto_principal']) ?>" alt="" class="img-fluid" style="width: 100%; height: auto; object-fit: contain;">
                    </a>
                </div>
            </div>
            <div class="video-gd-left col-lg-6 p-lg-5 p-4 align-self">
                <div class="p-xl-4 p-0 video-wrap">
                    <h5><?= formatearFecha($reportaje_principal['fecha_publicacion']) ?></h5>
                    <h3 class="title-big text-left mb-4">
                        <a href="reportaje-detalle.php?id=<?= $reportaje_principal['id'] ?>">
                            <?= htmlspecialchars($reportaje_principal['titulo']) ?>
                        </a>
                    </h3>
                    <p><?= htmlspecialchars($reportaje_principal['resumen_corto']) ?></p>
                    <a href="reportaje-detalle.php?id=<?= $reportaje_principal['id'] ?>" class="btn mt-4 p-0 btn-leer">
                        Leer 
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#e60000" viewBox="0 0 24 24">
                            <path d="M5 12h11.17l-4.88-4.88c-.39-.39-.39-1.02 0-1.41.39-.39 1.02-.39 1.41 0l6.59 6.59c.39.39.39 1.02 0 1.41l-6.59 6.59c-.39.39-1.02.39-1.41 0-.39-.39-.39-1.02 0-1.41L16.17 13H5c-.55 0-1-.45-1-1s.45-1 1-1z"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Cuadrícula de Reportajes Secundarios -->
<div class="grids-block-5 py-1">
    <section class="py-lg-4 py-md-3">
        <div class="container">
            <div class="row">
                <?php foreach($reportajes_secundarios as $rep_sec): ?>
                <div class="col-lg-4 col-md-6 grids5-info mt-5">
                    <a href="reportaje-detalle.php?id=<?= $rep_sec['id'] ?>" class="d-block">
                        <img src="admin/<?= htmlspecialchars($rep_sec['foto_principal']) ?>" alt="" class="img-fluid" style="height: 250px; object-fit: cover; width: 100%;" />
                    </a>
                    <div class="blog-info">
                        <h5><?= formatearFecha($rep_sec['fecha_publicacion']) ?></h5>
                        <h4><a href="reportaje-detalle.php?id=<?= $rep_sec['id'] ?>" class="d-block"><?= htmlspecialchars($rep_sec['titulo']) ?></a></h4>
                        <a href="reportaje-detalle.php?id=<?= $rep_sec['id'] ?>" class="btn mt-4 p-0 btn-leer">
                            Leer 
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#e60000" viewBox="0 0 24 24">
                                <path d="M5 12h11.17l-4.88-4.88c-.39-.39-.39-1.02 0-1.41.39-.39 1.02-.39 1.41 0l6.59 6.59c.39.39.39 1.02 0 1.41l-6.59 6.59c-.39.39-1.02.39-1.41 0-.39-.39-.39-1.02 0-1.41L16.17 13H5c-.55 0-1-.45-1-1s.45-1 1-1z"/>
                            </svg>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="pagination">
                <ul>
                    <li><a href="reportajes.php">Ver todos</a></li>
                </ul>
            </div>
        </div>
    </section>
</div>

<!-- SECCIÓN ACTUALIDAD (Noticias) -->
<section class="breadcrumb-area py-sm-5 py-1">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb-contents">
                    <h2 class="title-big">Noticias Recientes</h2>
                    <a class="anchor" id="actualidad"></a>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="grids-block-5 py-5">
    <section class="py-lg-4 py-md-3">
        <div class="container">
            <div class="row">
                <?php while ($noticia = $res_noticias->fetch(PDO::FETCH_ASSOC)): ?>
                <div class="col-lg-4 col-md-6 grids5-info mt-lg-0 mt-5 mb-4">
                    <a target="_blank" href="<?= htmlspecialchars($noticia['link_externo']) ?>" class="d-block">
                        <img src="admin/<?= htmlspecialchars($noticia['foto']) ?>" alt="" class="img-fluid" style="height: 250px; object-fit: cover; width: 100%;"/>
                    </a>
                    <div class="blog-info">
                        <h5><?= formatearFecha($noticia['fecha_publicacion']) ?></h5>
                        <h4><a target="_blank" href="<?= htmlspecialchars($noticia['link_externo']) ?>" class="d-block"><?= htmlspecialchars($noticia['titulo']) ?></a></h4>
                        <a target="_blank" href="<?= htmlspecialchars($noticia['link_externo']) ?>" class="btn mt-4 p-0 btn-leer">
                            Leer 
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#e60000" viewBox="0 0 24 24">
                                <path d="M5 12h11.17l-4.88-4.88c-.39-.39-.39-1.02 0-1.41.39-.39 1.02-.39 1.41 0l6.59 6.59c.39.39.39 1.02 0 1.41l-6.59 6.59c-.39.39-1.02.39-1.41 0-.39-.39-.39-1.02 0-1.41L16.17 13H5c-.55 0-1-.45-1-1s.45-1 1-1z"/>
                            </svg>
                        </a>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <div class="pagination">
                <ul>
                    <li><a target="_blank" href="https://www.facebook.com/DialogoyDesarrolloPeru">Ver todos</a></li>
                </ul>
            </div>
        </div>
    </section>
</div>

<!-- Sección Boletín -->
<?php if ($boletin): ?>
<section class="w3l-homeblock5 py-0">
    <div class="container py-lg-5 py-4">
        <div class="row">
            <div class="col-lg-8 align-self">
                <h3 class="title-big mb-4">Boletín NTEP Año 2026</h3>
                
                <p><?= nl2br(htmlspecialchars($boletin['resumen'])) ?></p>

                <div class="row mt-sm-4 mt-2 px-3">
                    <div class="col-6 p-0">
                        <span>Nº <span style="color: red;"><?= htmlspecialchars($boletin['numero_boletin']) ?></span></span>
                        <h4><?= formatearFecha($boletin['fecha_publicacion']) ?></h4>
                    </div>
                    <div class="col-6 p-0">
                        <!-- Botón para descargar/ver PDF con icono SVG vectorial -->
                        <span>
                            <a target="_blank" href="admin/uploads/boletines/pdfs/<?= htmlspecialchars(basename($boletin['archivo_pdf'])) ?>" style="text-decoration: none;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="78" height="78" viewBox="0 0 24 24">
                                    <path fill="#e60000" d="M12 16l4-5h-3V4h-2v7H8l4 5zm-7 2h14v2H5v-2z"/>
                                </svg>
                            </a>
                        </span>
                        <h4>Ver Boletin</h4>
                    </div>
                    <div class="col-12 text-center">
                        <a href="boletines.php" class="btn btn-style btn-primary mt-md-5 mt-4">Ver todos</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mt-lg-0 mt-4 text-center">
                <img src="admin/uploads/boletines/fotos/<?= htmlspecialchars(basename($boletin['foto_portada'])) ?>" class="img-fluid radius-image" alt="Portada Boletín" style="width: 100%; height: auto; object-fit: contain; max-height: 500px;">
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php
// Función para obtener la miniatura de Podcasts en el Index (YouTube, Spotify, Apple Podcasts, iVoox, etc.)
if (!function_exists('obtenerMiniaturaPodcast')) {
    function obtenerMiniaturaPodcast($url) {
        if (empty($url)) {
            return 'assets/images/default-video.png';
        }

        // 1. YouTube: Lógica rápida e intacta
        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $url, $match)) {
            return "https://img.youtube.com/vi/{$match[1]}/hqdefault.jpg";
        }

        // Contexto HTTP con User-Agent y timeout para evitar retardos
        $contexto = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36\r\n",
                'timeout' => 2
            ]
        ]);

        // 2. Spotify: API oEmbed oficial
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

        // 3. Genérico (Meta Tags): Para Apple Podcasts, iVoox, SoundCloud, etc.
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

        // 4. Imagen predeterminada si no se puede extraer la portada
        return 'assets/images/default-video.png';
    }
}
?>

<!-- Estilos para la Sección Podcast (Alineación, Aspecto Proporcional 16/9 y Botón de Play) -->
<style>
.podcast-card-thumb {
    overflow: hidden;
    border-radius: 8px;
    width: 100%;
    position: relative;
    aspect-ratio: 16 / 9;
    background: #000;
}

.podcast-card-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
    transition: transform 0.4s ease;
    display: block;
}

.podcast-card-thumb:hover img {
    transform: scale(1.05);
}

.play-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(0, 0, 0, 0.25);
    transition: background 0.3s ease;
    cursor: pointer;
}

.play-overlay:hover {
    background: rgba(0, 0, 0, 0.4);
}

.play-btn-icon {
    width: 48px;
    height: 48px;
    background: #e60000;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(0,0,0,0.4);
    transition: transform 0.3s ease, background 0.3s ease;
}

.play-overlay:hover .play-btn-icon {
    transform: scale(1.15);
    background: #ff1a1a;
}
</style>

<!-- Sección Podcast -->
<section class="w3l-homeblock3 py-5">
    <div class="container py-lg-5 py-md-4">
        <h3 class="title-big mb-5 text-center">Podcast</h3>
        <div class="row">
            <?php while ($podcast = $res_podcasts->fetch(PDO::FETCH_ASSOC)): 
                $embed_url = htmlspecialchars(obtenerEmbedUrl($podcast['url_embed']));
                $miniatura = obtenerMiniaturaPodcast($podcast['url_embed']);
            ?>
            <div class="col-lg-3 col-sm-6 mt-sm-0 mt-5 mb-4">
                <div class="area-box">
                    <div class="podcast-card-thumb mb-3" data-embed="<?= $embed_url ?>">
                        <div class="play-overlay" onclick="reproducirMediaInline(this)">
                            <img src="<?= $miniatura ?>" alt="<?= htmlspecialchars($podcast['titulo']) ?>" class="img-fluid rounded">
                            <div class="play-btn-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#ffffff" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <p><?= htmlspecialchars($podcast['titulo']) ?></p>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <div class="text-center">
            <a href="podcast.php" class="btn btn-style btn-primary mt-md-5 mt-4">Ver todos</a>
        </div>
    </div>
</section>

<?php
// Función para obtener la miniatura de videos/especiales (YouTube, Spotify, Vimeo, TikTok, etc.)
if (!function_exists('obtenerMiniaturaVideo')) {
    function obtenerMiniaturaVideo($url) {
        if (empty($url)) {
            return 'assets/images/default-video.png';
        }

        // 1. YouTube: Lógica intacta e instantánea
        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $url, $match)) {
            return "https://img.youtube.com/vi/{$match[1]}/hqdefault.jpg";
        }

        // Contexto HTTP optimizado (timeout corto de 2s para no ralentizar la carga)
        $contexto = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36\r\n",
                'timeout' => 2
            ]
        ]);

        // 2. Spotify: API oEmbed oficial
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

        // 3. Vimeo: API oEmbed oficial
        if (strpos($url, 'vimeo.com') !== false) {
            $oembedUrl = 'https://vimeo.com/api/oembed.json?url=' . urlencode($url);
            $json = @file_get_contents($oembedUrl, false, $contexto);
            if ($json) {
                $data = json_decode($json, true);
                if (!empty($data['thumbnail_url'])) {
                    return $data['thumbnail_url'];
                }
            }
        }

        // 4. Genérico (OpenGraph / Meta Tags): Para TikTok, Dailymotion, Facebook, iVoox, etc.
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

        // 5. Imagen por defecto si no se puede extraer portada
        return 'assets/images/default-video.png';
    }
}
?>

<!-- Estilos para la Sección de Videos (Zoom + Flechas del Carrusel + Proporción 16/9) -->
<style>
.video-card-thumb {
    overflow: hidden;
    border-radius: 8px;
    position: relative;
    width: 100%;
    aspect-ratio: 16 / 9;
    background: #000;
}

.video-card-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
    transition: transform 0.4s ease;
    display: block;
}

.video-card-thumb:hover img {
    transform: scale(1.08);
}

.teams1-content {
    position: relative;
}

.teams1-content .owl-nav {
    position: absolute;
    top: 40%;
    width: 100%;
    display: flex;
    justify-content: space-between;
    pointer-events: none;
    transform: translateY(-50%);
    z-index: 10;
}

.teams1-content .owl-nav button.owl-prev,
.teams1-content .owl-nav button.owl-next {
    pointer-events: auto;
    background: #000 !important;
    color: #fff !important;
    width: 40px;
    height: 40px;
    border-radius: 50% !important;
    display: flex !important;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    border: none !important;
    font-size: 20px !important;
    box-shadow: 0px 4px 10px rgba(0,0,0,0.3);
}

.teams1-content .owl-nav button.owl-prev:hover,
.teams1-content .owl-nav button.owl-next:hover {
    background: #e60000 !important;
    color: #fff !important;
}

.teams1-content .owl-nav button.owl-prev {
    margin-left: -20px;
}

.teams1-content .owl-nav button.owl-next {
    margin-right: -20px;
}
</style>

<!-- Sección Videos (Carrusel) -->
<section class="w3l-team" id="team">
    <div class="teams1 py-5 mb-3">
        <div class="container py-lg-3 pb-lg-5 pb-4">
            <div class="teams1-content">
                <h3 class="title-big text-center mb-5">Especiales</h3>
                <div class="owl-carousel owl-theme text-center">
                    <?php while ($video = $res_videos->fetch(PDO::FETCH_ASSOC)): 
                        $embed_url = htmlspecialchars(obtenerEmbedUrl($video['url_embed']));
                        $miniatura = obtenerMiniaturaVideo($video['url_embed']);
                    ?>
                    <div class="item">
                        <div class="area-box p-0 mx-1">
                            <div class="video-card-thumb mb-3" data-embed="<?= $embed_url ?>">
                                <div class="play-overlay" onclick="reproducirMediaInline(this)">
                                    <img src="<?= $miniatura ?>" alt="<?= htmlspecialchars($video['titulo']) ?>" class="img-fluid rounded">
                                    <div class="play-btn-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#ffffff" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <p class="text-center"><?= htmlspecialchars($video['titulo']) ?></p>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Sobre Nosotros Banner -->
<section class="w3l-banner py-0" id="work">
    <div class="midd-w3 py-lg-4 py-md-3">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 mt-lg-0 mt-lg-5 about-right-faq align-self">
                    <h5 class="title-small mb-2">DDP Noticias</h5>
                    <h3 class="title-banner">Diálogo y Desarrollo Perú</h3>
                    <p class="mt-4">Somos un espacio de periodismo independiente que busca visibilizar las acciones de diálogo en el país desde una mirada constructiva.</p>
                    <a href="sobre-dd.php" class="btn btn-style btn-primary mt-md-5 mt-4">Nosotros</a>
                </div>
                <div class="col-md-6 left-wthree-img mt-lg-0 mt-4">
                    <div class="position-relative">
                        <img src="assets/images/bannerimg.jpg" alt="" class="img-fluid">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Estilos para la sección Redes Sociales (Imagen de Fondo e Íconos SVG Rojos) -->
<style>
.redes-sociales-section {
    background: url('assets/images/bg-redes.jpg') no-repeat center center;
    background-size: cover;
    position: relative;
}

.redes-sociales-section .main-social-footer-29 {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 25px;
}

.redes-sociales-section .main-social-footer-29 a {
    display: inline-block;
    transition: transform 0.3s ease;
}

.redes-sociales-section .main-social-footer-29 a:hover {
    transform: scale(1.2);
}

.redes-sociales-section .main-social-footer-29 svg {
    fill: #e60000;
}
</style>

<!-- Redes Sociales -->
<div class="middle py-5 redes-sociales-section">
    <div class="container py-xl-5 py-lg-3">
        <div class="welcome-left text-center py-md-5 py-3">
            <h3 class="title-big">Síguenos en nuestras Redes Sociales</h3>
            <div class="main-social-footer-29 mt-4">
                <!-- Facebook -->
                <a target="_blank" href="https://www.facebook.com/DialogoyDesarrolloPeru" title="Facebook">
                    <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 24 24">
                        <path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3h1.8l.3-2.1h-2.1V9.8c0-.6.2-1 1.1-1h1.1V6.9c-.2 0-.9-.1-1.7-.1-1.7 0-2.9 1-2.9 3v1.9h-1.8v2.1h1.8v5.3h2.4z"/>
                    </svg>
                </a>
                <!-- TikTok -->
                <a target="_blank" href="https://www.tiktok.com/@dialogo.y.desarrollo" title="TikTok">
                    <svg xmlns="http://www.w3.org/2000/svg" width="34" height="34" viewBox="0 0 24 24">
                        <path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.98-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/>
                    </svg>
                </a>
                <!-- Instagram -->
                <a target="_blank" href="https://www.instagram.com/dialogo.y.desarrollo/" title="Instagram">
                    <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 24 24">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script>
  function reproducirMediaInline(elem) {
    var container = elem.closest('[data-embed]');
    if (!container) return;
    var embedUrl = container.getAttribute('data-embed');
    if (!embedUrl) return;
    
    container.innerHTML = '<iframe src="' + embedUrl + '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen style="width:100%; height:100%; border:0; border-radius:8px;"></iframe>';
  }

  $(document).ready(function () {
    if($('.owl-carousel').length){
        $('.owl-carousel').owlCarousel({
          loop: true,
          margin: 20,
          nav: true, 
          dots: false, 
          navText: [
            '&#10094;',
            '&#10095;'
          ],
          responsiveClass: true,
          responsive: {
            0: { items: 1, nav: true },
            568: { items: 2, nav: true },
            768: { items: 3, nav: true },
            1000: { items: 4, nav: true, loop: true } 
          }
        });
    }
  });
</script>
<?php include 'includes/footer.php'; ?>