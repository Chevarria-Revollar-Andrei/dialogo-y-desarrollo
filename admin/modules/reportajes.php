<?php
// modules/reportajes.php
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
    $stmt = $pdo->query("SELECT r.*, CONCAT(a.nombres, ' ', IFNULL(a.ap_paterno, '')) AS autor_nombre 
                         FROM reportajes r 
                         LEFT JOIN autores a ON r.autor_id = a.id 
                         ORDER BY r.fecha_publicacion DESC");
    $reportajes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total_registros = count($reportajes);

    // Contadores para métricas rápidas
    $destacados_count = 0;
    foreach($reportajes as $r) {
        if (!empty($r['es_destacado'])) $destacados_count++;
    }
} catch (PDOException $e) {
    $reportajes = []; $total_registros = 0; $destacados_count = 0; $error_bd = "Error SQL: " . $e->getMessage();
}

try {
    $stmt_autores = $pdo->query("SELECT id, CONCAT(nombres, ' ', IFNULL(ap_paterno, '')) AS nombre FROM autores ORDER BY nombres ASC");
    $autores = $stmt_autores->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { $autores = []; }
?>

<!-- Summernote Assets -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

<style>
/* Estilos modernizados para el módulo de Reportajes */
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
.rep-thumb {
    width: 60px;
    height: 44px;
    border-radius: 6px;
    object-fit: cover;
    border: 1px solid #e2e8f0;
}
.badge-destacado {
    background: #ecfdf5;
    color: #059669;
    border: 1px solid #a7f3d0;
    font-weight: 600;
    padding: 6px 10px;
    border-radius: 20px;
}
.badge-publicado {
    background: #f8fafc;
    color: #64748b;
    border: 1px solid #e2e8f0;
    font-weight: 500;
    padding: 6px 10px;
    border-radius: 20px;
}
.badge-borrador {
    background: #fff7ed;
    color: #c2410c;
    border: 1px solid #ffedd5;
    font-weight: 600;
    padding: 6px 10px;
    border-radius: 20px;
}
.note-editor.note-frame { border-radius: 8px; border: 1px solid #cbd5e1; }
.note-editor .note-toolbar { background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; border-radius: 8px 8px 0 0; }
</style>

<section class="section-view active">
    <!-- Cabecera -->
    <div class="page-head d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="eyebrow text-uppercase text-muted fw-bold mb-1" style="font-size: 11px; letter-spacing: 0.5px;">Panel Editorial</div>
            <h1 class="h3 fw-bold m-0">Reportajes</h1>
        </div>
        <button class="btn btn-primary d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalNuevoReportaje">
            <i class="bi bi-plus-lg"></i> Nuevo reportaje
        </button>
    </div>

    <?= $alerta ?>
    <?php if(isset($error_bd)) echo '<div class="alert alert-warning mb-4">'.$error_bd.'</div>'; ?>

    <!-- Tarjetas de Estadísticas -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stats-card">
                <div class="stats-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-journal-text"></i></div>
                <div>
                    <h3 class="mb-0 fw-bold"><?= $total_registros ?></h3>
                    <span class="text-muted small">Total de Reportajes</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card">
                <div class="stats-icon bg-success bg-opacity-10 text-success"><i class="bi bi-star-fill"></i></div>
                <div>
                    <h3 class="mb-0 fw-bold"><?= $destacados_count ?></h3>
                    <span class="text-muted small">Destacados en Portada</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card">
                <div class="stats-icon bg-info bg-opacity-10 text-info"><i class="bi bi-person-badge"></i></div>
                <div>
                    <h3 class="mb-0 fw-bold"><?= count($autores) ?></h3>
                    <span class="text-muted small">Autores Registrados</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenedor Principal de Tabla y Búsqueda -->
    <div class="card border-0 shadow-sm rounded-3">
        <!-- Barra de herramientas (Buscador) -->
        <div class="card-header bg-white border-0 py-3 px-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div class="search-box-custom">
                <i class="bi bi-search"></i>
                <input type="text" id="inputBusqueda" class="form-control" placeholder="Buscar por título, autor o resumen...">
            </div>
            <div class="text-muted small">
                Mostrando <strong id="contadorVisibles"><?= $total_registros ?></strong> registros
            </div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle mb-0" id="tablaReportajes" style="width: 100%;">
                <thead class="table-light">
                    <tr>
                        <th style="width: 45%; border:none;" class="ps-4">REPORTAJE</th>
                        <th style="width: 20%; border:none;">AUTOR</th>
                        <th style="width: 12%; border:none;">FECHA</th>
                        <th style="width: 13%; border:none;">ESTADO</th>
                        <th style="width: 10%; border:none;" class="text-end pe-4">ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($total_registros > 0): ?>
                        <?php foreach($reportajes as $rep): ?>
                            <tr class="fila-reportaje">
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="<?= htmlspecialchars($rep['foto_principal'] ?: 'assets/img/placeholder.jpg') ?>" class="rep-thumb" alt="Miniatura">
                                        <div>
                                            <a href="#" class="fw-bold text-dark text-decoration-none d-block mb-1 col-titulo" style="font-size: 14px;">
                                                <?= htmlspecialchars($rep['titulo']) ?>
                                            </a>
                                            <span class="text-muted small d-block text-truncate col-resumen" style="max-width: 320px;">
                                                <?= htmlspecialchars($rep['resumen_corto']) ?>
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="col-autor fw-semibold text-secondary" style="font-size: 13px;">
                                    <i class="bi bi-person me-1"></i><?= htmlspecialchars($rep['autor_nombre'] ?: 'Sin autor') ?>
                                </td>
                                <td class="text-secondary" style="font-size: 13px;">
                                    <?= date('d/m/Y', strtotime($rep['fecha_publicacion'])) ?>
                                </td>
                                <td>
                                    <?php if(($rep['estado'] ?? '') === 'borrador' || !empty($rep['es_borrador'])): ?>
                                        <span class="badge badge-borrador"><i class="bi bi-file-earmark-medical me-1"></i> Borrador</span>
                                    <?php elseif($rep['es_destacado']): ?>
                                        <span class="badge badge-destacado"><i class="bi bi-star-fill me-1"></i> Destacado</span>
                                    <?php else: ?>
                                        <span class="badge badge-publicado">Publicado</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-inline-flex gap-1">
                                        <button class="btn btn-sm btn-outline-secondary" title="Editar" data-bs-toggle="modal" data-bs-target="#modalEditar_<?= $rep['id'] ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="actions/procesar_reportaje.php" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro que deseas eliminar este reportaje de forma permanente?');">
                                            <input type="hidden" name="accion" value="eliminar">
                                            <input type="hidden" name="id" value="<?= $rep['id'] ?>">
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
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                No hay reportajes registrados actualmente.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- MODALES DE EDICIÓN (Separados del Loop Principal para optimizar el HTML) -->
<?php foreach($reportajes as $rep): ?>
<div class="modal fade" id="modalEditar_<?= $rep['id'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <form action="actions/procesar_reportaje.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="accion" value="editar">
                <input type="hidden" name="id" value="<?= $rep['id'] ?>">
                
                <div class="modal-header border-bottom py-3 px-4">
                    <h5 class="modal-title fw-bold fs-5">Editar reportaje</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Título *</label>
                        <input type="text" name="titulo" class="form-control" value="<?= htmlspecialchars($rep['titulo']) ?>" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Autor *</label>
                            <select name="autor_id" class="form-select" required>
                                <?php foreach($autores as $autor): ?>
                                    <option value="<?= $autor['id'] ?>" <?= ($autor['id'] == $rep['autor_id']) ? 'selected' : '' ?>><?= htmlspecialchars($autor['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Fecha de Publicación</label>
                            <input type="date" name="fecha_publicacion" class="form-control" value="<?= date('Y-m-d', strtotime($rep['fecha_publicacion'])) ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Resumen Corto *</label>
                        <textarea name="resumen_corto" class="form-control" rows="2" required><?= htmlspecialchars($rep['resumen_corto']) ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Desarrollo (Contenido completo) *</label>
                        <textarea name="desarrollo" class="form-control editor-html" required><?= htmlspecialchars($rep['desarrollo']) ?></textarea>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Reemplazar Foto Principal</label>
                            <input type="file" name="foto_principal" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Reemplazar PDF</label>
                            <input type="file" name="pdf_adjunto" class="form-control" accept="application/pdf">
                        </div>
                    </div>
                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" name="es_destacado" value="1" id="checkDest_<?= $rep['id'] ?>" <?= $rep['es_destacado'] ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="checkDest_<?= $rep['id'] ?>">Marcar como reportaje destacado</label>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light px-4 py-3 d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <div class="d-flex gap-2">
                        <button type="submit" name="estado" value="borrador" formnovalidate class="btn btn-warning text-dark"><i class="bi bi-file-earmark-medical me-1"></i> Guardar como borrador</button>
                        <button type="submit" name="estado" value="publicado" class="btn btn-primary px-4">Guardar cambios</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<!-- MODAL NUEVO REPORTAJE -->
<div class="modal fade" id="modalNuevoReportaje" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <form action="actions/procesar_reportaje.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="accion" value="guardar">
                
                <div class="modal-header border-bottom py-3 px-4">
                    <h5 class="modal-title fw-bold fs-5">Nuevo reportaje</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Título *</label>
                        <input type="text" name="titulo" class="form-control" placeholder="Escribe el título aquí..." required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Autor *</label>
                            <select name="autor_id" class="form-select" required>
                                <option value="">Selecciona un autor...</option>
                                <?php foreach($autores as $autor): ?>
                                    <option value="<?= $autor['id'] ?>"><?= htmlspecialchars($autor['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Fecha de Publicación</label>
                            <input type="date" name="fecha_publicacion" class="form-control" value="<?= date('Y-m-d') ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Resumen Corto *</label>
                        <textarea name="resumen_corto" class="form-control" rows="2" placeholder="Breve introducción..." required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Desarrollo (Contenido completo) *</label>
                        <textarea name="desarrollo" class="form-control editor-html" required></textarea>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Fotos de Galería *</label>
                            <input type="file" name="fotos[]" class="form-control" accept="image/*" multiple required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">PDF adjunto</label>
                            <input type="file" name="pdf_adjunto" class="form-control" accept="application/pdf">
                        </div>
                    </div>
                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" name="es_destacado" value="1" id="checkNuevoDestacado">
                        <label class="form-check-label fw-semibold" for="checkNuevoDestacado">Marcar como reportaje destacado</label>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light px-4 py-3 d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <div class="d-flex gap-2">
                        <button type="submit" name="estado" value="borrador" formnovalidate class="btn btn-warning text-dark"><i class="bi bi-file-earmark-medical me-1"></i> Guardar como borrador</button>
                        <button type="submit" name="estado" value="publicado" class="btn btn-primary px-4">Guardar reportaje</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- SCRIPTS DE INTERACCIÓN Y SUMMERNOTE -->
<script>
$(document).ready(function() {
    // Inicialización del editor Summernote
    $('.modal').on('shown.bs.modal', function() {
        $(this).find('.editor-html').summernote({
            height: 260,
            placeholder: 'Escribe el desarrollo del reportaje aquí...',
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview']]
            ]
        });
    });

    $('.modal').on('hidden.bs.modal', function() {
        $(this).find('.editor-html').summernote('destroy');
    });

    // Lógica del Buscador en Tiempo Real
    $('#inputBusqueda').on('keyup', function() {
        var valor = $(this).val().toLowerCase().trim();
        var visibles = 0;

        $('.fila-reportaje').each(function() {
            var titulo = $(this).find('.col-titulo').text().toLowerCase();
            var resumen = $(this).find('.col-resumen').text().toLowerCase();
            var autor = $(this).find('.col-autor').text().toLowerCase();

            if (titulo.indexOf(valor) !== -1 || resumen.indexOf(valor) !== -1 || autor.indexOf(valor) !== -1) {
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