<?php
// modules/videos.php
require_once 'config/conexion.php';

$alerta = '';
if (isset($_SESSION['mensaje_exito'])) {
    $alerta = '<div class="alert alert-success alert-dismissible fade show mb-4" role="alert"><i class="bi bi-check-circle-fill me-2"></i>' . $_SESSION['mensaje_exito'] . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    unset($_SESSION['mensaje_exito']);
}
if (isset($_SESSION['mensaje_error'])) {
    $alerta = '<div class="alert alert-danger alert-dismissible fade show mb-4" role="alert"><i class="bi bi-exclamation-triangle-fill me-2"></i>' . $_SESSION['mensaje_error'] . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    unset($_SESSION['mensaje_error']);
}

try {
    $stmt = $pdo->query("SELECT * FROM videos ORDER BY fecha_publicacion DESC");
    $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total_registros = count($videos);

    // Contadores estadísticos
    $videos_mes = 0;
    $youtube_count = 0;
    $vimeo_count = 0;
    $mes_actual = date('Y-m');

    foreach($videos as $v) {
        if (!empty($v['fecha_publicacion']) && date('Y-m', strtotime($v['fecha_publicacion'])) === $mes_actual) {
            $videos_mes++;
        }
        $embed_lower = strtolower($v['url_embed']);
        if (strpos($embed_lower, 'youtube.com') !== false || strpos($embed_lower, 'youtu.be') !== false) {
            $youtube_count++;
        } elseif (strpos($embed_lower, 'vimeo.com') !== false) {
            $vimeo_count++;
        }
    }
} catch (PDOException $e) {
    $videos = []; 
    $total_registros = 0; 
    $videos_mes = 0;
    $youtube_count = 0;
    $vimeo_count = 0;
    $error_bd = "Error SQL: " . $e->getMessage();
}
?>

<style>
/* Estilos modernizados para el módulo de Videos */
.stats-card {
    background: #ffffff;
    border: 1px solid #eef2f6;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.02);
    display: flex;
    align-items: center;
    gap: 16px;
}
.stats-icon {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}
.search-box-custom {
    position: relative;
    max-width: 380px;
    width: 100%;
}
.search-box-custom input {
    padding-left: 40px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    height: 42px;
}
.search-box-custom i {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 16px;
}
.video-icon-box {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    background: #f1f5f9;
    color: #475569;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    border: 1px solid #e2e8f0;
    flex-shrink: 0;
}
.badge-platform {
    font-size: 11px;
    font-weight: 600;
    padding: 6px 12px;
    border-radius: 20px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
</style>

<section class="section-view active">
    <!-- Cabecera -->
    <div class="page-head d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="eyebrow text-uppercase text-muted fw-bold mb-1" style="font-size: 11px; letter-spacing: 0.5px;">Panel Multimedia</div>
            <h1 class="h3 fw-bold m-0">Videos & Clips</h1>
        </div>
        <button class="btn btn-primary d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalNuevoVideo">
            <i class="bi bi-plus-lg"></i> Nuevo video
        </button>
    </div>

    <?= $alerta ?>
    <?php if(isset($error_bd)) echo '<div class="alert alert-warning mb-4">'.$error_bd.'</div>'; ?>

    <!-- Tarjetas de Estadísticas -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stats-card">
                <div class="stats-icon bg-danger bg-opacity-10 text-danger"><i class="bi bi-camera-video-fill"></i></div>
                <div>
                    <h3 class="mb-0 fw-bold"><?= $total_registros ?></h3>
                    <span class="text-muted small">Total Videos</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card">
                <div class="stats-icon bg-success bg-opacity-10 text-success"><i class="bi bi-calendar-check"></i></div>
                <div>
                    <h3 class="mb-0 fw-bold"><?= $videos_mes ?></h3>
                    <span class="text-muted small">Publicados este Mes</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card">
                <div class="stats-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-play-btn-fill"></i></div>
                <div>
                    <h3 class="mb-0 fw-bold"><?= $youtube_count + $vimeo_count ?></h3>
                    <span class="text-muted small">Plataformas Streaming</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenedor Principal -->
    <div class="card border-0 shadow-sm rounded-3">
        <!-- Barra de herramientas (Buscador) -->
        <div class="card-header bg-white border-0 py-3 px-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div class="search-box-custom">
                <i class="bi bi-search"></i>
                <input type="text" id="inputBusquedaVideos" class="form-control" placeholder="Buscar video por título o enlace...">
            </div>
            <div class="text-muted small">
                Mostrando <strong id="contadorVisibles"><?= $total_registros ?></strong> registros
            </div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle mb-0" id="tablaVideos" style="width: 100%;">
                <thead class="table-light">
                    <tr>
                        <th style="width: 45%; border:none;" class="ps-4">VIDEO / TÍTULO</th>
                        <th style="width: 20%; border:none;">FECHA</th>
                        <th style="width: 22%; border:none;">PLATAFORMA / EMBED</th>
                        <th style="width: 13%; border:none;" class="text-end pe-4">ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($total_registros > 0): ?>
                        <?php foreach($videos as $vid): 
                            $embed_url = strtolower($vid['url_embed']);
                        ?>
                            <tr class="fila-video">
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="video-icon-box">
                                            <i class="bi bi-film"></i>
                                        </div>
                                        <span class="fw-bold text-dark d-block col-titulo" style="font-size: 14px;">
                                            <?= htmlspecialchars($vid['titulo']) ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="text-secondary" style="font-size: 13px;">
                                    <i class="bi bi-calendar3 me-1 text-muted"></i>
                                    <?= date('d/m/Y', strtotime($vid['fecha_publicacion'])) ?>
                                </td>
                                <td>
                                    <?php if(strpos($embed_url, 'youtube.com') !== false || strpos($embed_url, 'youtu.be') !== false): ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 badge-platform">
                                            <i class="bi bi-youtube"></i> YouTube
                                        </span>
                                    <?php elseif(strpos($embed_url, 'vimeo.com') !== false): ?>
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 badge-platform">
                                            <i class="bi bi-vimeo"></i> Vimeo
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 badge-platform">
                                            <i class="bi bi-code-slash"></i> Embed / Enlace
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-inline-flex gap-1">
                                        <a href="<?= htmlspecialchars($vid['url_embed']) ?>" target="_blank" class="btn btn-sm btn-outline-primary col-link" title="Ver enlace externo">
                                            <i class="bi bi-box-arrow-up-right"></i>
                                        </a>
                                        <button class="btn btn-sm btn-outline-secondary" title="Editar" data-bs-toggle="modal" data-bs-target="#modalEditarVideo_<?= $vid['id'] ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="actions/procesar_video.php" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro que deseas eliminar este video de forma permanente?');">
                                            <input type="hidden" name="accion" value="eliminar">
                                            <input type="hidden" name="id" value="<?= $vid['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr id="sinResultados">
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="bi bi-camera-video fs-2 d-block mb-2"></i>
                                No hay videos registrados actualmente.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- MODALES DE EDICIÓN (Fuera de la tabla) -->
<?php foreach($videos as $vid): ?>
<div class="modal fade" id="modalEditarVideo_<?= $vid['id'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="actions/procesar_video.php" method="POST">
                <input type="hidden" name="accion" value="editar">
                <input type="hidden" name="id" value="<?= $vid['id'] ?>">
                
                <div class="modal-header border-bottom py-3 px-4">
                    <h5 class="modal-title fw-bold fs-5">Editar video</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Título *</label>
                        <input type="text" name="titulo" class="form-control" value="<?= htmlspecialchars($vid['titulo']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Fecha de publicación</label>
                        <input type="date" name="fecha_publicacion" class="form-control" value="<?= date('Y-m-d', strtotime($vid['fecha_publicacion'])) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">URL / Embed *</label>
                        <input type="text" name="url_embed" class="form-control" value="<?= htmlspecialchars($vid['url_embed']) ?>" placeholder="https://..." required>
                        <div class="form-text">Enlace de YouTube, Vimeo o código Iframe.</div>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light px-4 py-3">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<!-- MODAL NUEVO VIDEO -->
<div class="modal fade" id="modalNuevoVideo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="actions/procesar_video.php" method="POST">
                <input type="hidden" name="accion" value="guardar">
                
                <div class="modal-header border-bottom py-3 px-4">
                    <h5 class="modal-title fw-bold fs-5">Nuevo video</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Título *</label>
                        <input type="text" name="titulo" class="form-control" placeholder="Escribe el título del video..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Fecha de publicación</label>
                        <input type="date" name="fecha_publicacion" class="form-control" value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">URL / Embed *</label>
                        <input type="text" name="url_embed" class="form-control" placeholder="https://www.youtube.com/watch?v=..." required>
                        <div class="form-text">Pega la URL del video o el código Iframe embed.</div>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light px-4 py-3">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4">Guardar video</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- SCRIPT BUSCADOR EN TIEMPO REAL -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#inputBusquedaVideos').on('keyup', function() {
        var valor = $(this).val().toLowerCase().trim();
        var visibles = 0;

        $('.fila-video').each(function() {
            var titulo = $(this).find('.col-titulo').text().toLowerCase();
            var link = $(this).find('.col-link').attr('href') ? $(this).find('.col-link').attr('href').toLowerCase() : '';

            if (titulo.indexOf(valor) !== -1 || link.indexOf(valor) !== -1) {
                $(this).show();
                visibles++;
            } else {
                $(this).hide();
            }
        });

        $('#contadorVisibles').text(visibles);
    });
});
</script>