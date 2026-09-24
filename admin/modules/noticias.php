<?php
// modules/noticias.php
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
    $stmt = $pdo->query("SELECT * FROM noticias ORDER BY fecha_publicacion DESC");
    $noticias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total_registros = count($noticias);

    // Contadores estadísticos
    $noticias_mes = 0;
    $mes_actual = date('Y-m');
    foreach($noticias as $n) {
        if (!empty($n['fecha_publicacion']) && date('Y-m', strtotime($n['fecha_publicacion'])) === $mes_actual) {
            $noticias_mes++;
        }
    }
} catch (PDOException $e) {
    $noticias = []; 
    $total_registros = 0; 
    $noticias_mes = 0;
    $error_bd = "Error SQL: " . $e->getMessage();
}
?>

<style>
/* Estilos modernizados para el módulo de Noticias */
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
.noti-thumb {
    width: 52px;
    height: 40px;
    border-radius: 6px;
    object-fit: cover;
    border: 1px solid #e2e8f0;
}
.noti-thumb-placeholder {
    width: 52px;
    height: 40px;
    border-radius: 6px;
    background: #f1f5f9;
    color: #94a3b8;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    border: 1px solid #e2e8f0;
}
</style>

<section class="section-view active">
    <!-- Cabecera -->
    <div class="page-head d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="eyebrow text-uppercase text-muted fw-bold mb-1" style="font-size: 11px; letter-spacing: 0.5px;">Panel Editorial</div>
            <h1 class="h3 fw-bold m-0">Noticias Recientes</h1>
        </div>
        <button class="btn btn-primary d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalNuevaNoticia">
            <i class="bi bi-plus-lg"></i> Nueva noticia
        </button>
    </div>

    <?= $alerta ?>
    <?php if(isset($error_bd)) echo '<div class="alert alert-warning mb-4">'.$error_bd.'</div>'; ?>

    <!-- Tarjetas de Estadísticas -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stats-card">
                <div class="stats-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-newspaper"></i></div>
                <div>
                    <h3 class="mb-0 fw-bold"><?= $total_registros ?></h3>
                    <span class="text-muted small">Total de Noticias</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card">
                <div class="stats-icon bg-success bg-opacity-10 text-success"><i class="bi bi-calendar-check"></i></div>
                <div>
                    <h3 class="mb-0 fw-bold"><?= $noticias_mes ?></h3>
                    <span class="text-muted small">Publicadas este Mes</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card">
                <div class="stats-icon bg-info bg-opacity-10 text-info"><i class="bi bi-link-45deg"></i></div>
                <div>
                    <h3 class="mb-0 fw-bold"><?= $total_registros ?></h3>
                    <span class="text-muted small">Enlaces Externos Activos</span>
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
                <input type="text" id="inputBusquedaNoticias" class="form-control" placeholder="Buscar noticia por título o enlace...">
            </div>
            <div class="text-muted small">
                Mostrando <strong id="contadorVisibles"><?= $total_registros ?></strong> registros
            </div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle mb-0" id="tablaNoticias" style="width: 100%;">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50%; border:none;" class="ps-4">NOTICIA</th>
                        <th style="width: 20%; border:none;">FECHA PUBLICACIÓN</th>
                        <th style="width: 18%; border:none;">ENLACE EXTERNO</th>
                        <th style="width: 12%; border:none;" class="text-end pe-4">ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($total_registros > 0): ?>
                        <?php foreach($noticias as $noti): ?>
                            <tr class="fila-noticia">
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <?php if(!empty($noti['foto'])): ?>
                                            <img src="<?= htmlspecialchars($noti['foto']) ?>" class="noti-thumb" alt="Miniatura">
                                        <?php else: ?>
                                            <div class="noti-thumb-placeholder"><i class="bi bi-image"></i></div>
                                        <?php endif; ?>
                                        
                                        <span class="fw-bold text-dark d-block col-titulo" style="font-size: 14px;">
                                            <?= htmlspecialchars($noti['titulo']) ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="text-secondary" style="font-size: 13px;">
                                    <i class="bi bi-calendar3 me-1 text-muted"></i>
                                    <?= date('d/m/Y', strtotime($noti['fecha_publicacion'])) ?>
                                </td>
                                <td>
                                    <?php if(!empty($noti['link_externo'])): ?>
                                        <a href="<?= htmlspecialchars($noti['link_externo']) ?>" target="_blank" class="btn btn-sm btn-light border text-primary d-inline-flex align-items-center gap-1 col-link" style="font-size: 12px;">
                                            <i class="bi bi-box-arrow-up-right"></i> Visitar enlace
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small">Sin enlace</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-inline-flex gap-1">
                                        <button class="btn btn-sm btn-outline-secondary" title="Editar" data-bs-toggle="modal" data-bs-target="#modalEditarNoticia_<?= $noti['id'] ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="actions/procesar_noticia.php" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro que deseas eliminar esta noticia de forma permanente?');">
                                            <input type="hidden" name="accion" value="eliminar">
                                            <input type="hidden" name="id" value="<?= $noti['id'] ?>">
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
                                <i class="bi bi-newspaper fs-2 d-block mb-2"></i>
                                No hay noticias registradas actualmente.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- MODALES DE EDICIÓN (Fuera del loop de la tabla) -->
<?php foreach($noticias as $noti): ?>
<div class="modal fade" id="modalEditarNoticia_<?= $noti['id'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="actions/procesar_noticia.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="accion" value="editar">
                <input type="hidden" name="id" value="<?= $noti['id'] ?>">
                
                <div class="modal-header border-bottom py-3 px-4">
                    <h5 class="modal-title fw-bold fs-5">Editar noticia</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Título *</label>
                        <input type="text" name="titulo" class="form-control" value="<?= htmlspecialchars($noti['titulo']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Fecha de publicación</label>
                        <input type="date" name="fecha_publicacion" class="form-control" value="<?= date('Y-m-d', strtotime($noti['fecha_publicacion'])) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">URL / Enlace externo *</label>
                        <input type="url" name="link_externo" class="form-control" value="<?= htmlspecialchars($noti['link_externo']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Cambiar Foto de Portada (Opcional)</label>
                        <input type="file" name="foto" class="form-control" accept="image/*">
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

<!-- MODAL NUEVA NOTICIA -->
<div class="modal fade" id="modalNuevaNoticia" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="actions/procesar_noticia.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="accion" value="guardar">
                
                <div class="modal-header border-bottom py-3 px-4">
                    <h5 class="modal-title fw-bold fs-5">Nueva noticia</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Título *</label>
                        <input type="text" name="titulo" class="form-control" placeholder="Escribe el título de la noticia..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Fecha de publicación</label>
                        <input type="date" name="fecha_publicacion" class="form-control" value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">URL / Enlace externo *</label>
                        <input type="url" name="link_externo" class="form-control" placeholder="https://ejemplo.com/noticia" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Foto de portada *</label>
                        <input type="file" name="foto" class="form-control" accept="image/*" required>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light px-4 py-3">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4">Guardar noticia</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- SCRIPT BUSCADOR EN TIEMPO REAL -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#inputBusquedaNoticias').on('keyup', function() {
        var valor = $(this).val().toLowerCase().trim();
        var visibles = 0;

        $('.fila-noticia').each(function() {
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