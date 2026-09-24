<?php
// modules/boletines.php
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
    $stmt = $pdo->query("SELECT * FROM boletines ORDER BY fecha_publicacion DESC");
    $boletines = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total_registros = count($boletines);

    // Contadores estadísticos
    $boletines_mes = 0;
    $con_pdf_count = 0;
    $mes_actual = date('Y-m');

    foreach($boletines as $b) {
        if (!empty($b['fecha_publicacion']) && date('Y-m', strtotime($b['fecha_publicacion'])) === $mes_actual) {
            $boletines_mes++;
        }
        if (!empty($b['archivo_pdf'])) {
            $con_pdf_count++;
        }
    }
} catch (PDOException $e) {
    $boletines = []; 
    $total_registros = 0; 
    $boletines_mes = 0;
    $con_pdf_count = 0;
    $error_bd = "Error SQL: " . $e->getMessage();
}
?>

<style>
/* Estilos modernizados para el módulo de Boletines */
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
.boletin-thumb {
    width: 42px;
    height: 54px;
    object-fit: cover;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    flex-shrink: 0;
}
.badge-pdf {
    font-size: 11px;
    font-weight: 700;
    padding: 6px 12px;
    border-radius: 20px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.resumen-text {
    font-size: 13px;
    color: #64748b;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

<section class="section-view active">
    <!-- Cabecera -->
    <div class="page-head d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="eyebrow text-uppercase text-muted fw-bold mb-1" style="font-size: 11px; letter-spacing: 0.5px;">Publicaciones</div>
            <h1 class="h3 fw-bold m-0">Boletines NTEP</h1>
        </div>
        <button class="btn btn-primary d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalNuevoBoletin">
            <i class="bi bi-plus-lg"></i> Nuevo boletín
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
                    <span class="text-muted small">Total Boletines</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card">
                <div class="stats-icon bg-success bg-opacity-10 text-success"><i class="bi bi-calendar-check"></i></div>
                <div>
                    <h3 class="mb-0 fw-bold"><?= $boletines_mes ?></h3>
                    <span class="text-muted small">Publicados este Mes</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card">
                <div class="stats-icon bg-danger bg-opacity-10 text-danger"><i class="bi bi-file-earmark-pdf-fill"></i></div>
                <div>
                    <h3 class="mb-0 fw-bold"><?= $con_pdf_count ?></h3>
                    <span class="text-muted small">Con Documento PDF</span>
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
                <input type="text" id="inputBusquedaBoletines" class="form-control" placeholder="Buscar por título, número o resumen...">
            </div>
            <div class="text-muted small">
                Mostrando <strong id="contadorVisibles"><?= $total_registros ?></strong> registros
            </div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle mb-0" id="tablaBoletines" style="width: 100%;">
                <thead class="table-light">
                    <tr>
                        <th style="width: 30%; border:none;" class="ps-4">BOLETÍN / PORTADA</th>
                        <th style="width: 33%; border:none;">RESUMEN</th>
                        <th style="width: 15%; border:none;">FECHA</th>
                        <th style="width: 10%; border:none;">DOCUMENTO</th>
                        <th style="width: 12%; border:none;" class="text-end pe-4">ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($total_registros > 0): ?>
                        <?php foreach($boletines as $b): 
                            $foto = !empty($b['foto_portada']) && $b['foto_portada'] !== 'default.png' ? 'uploads/boletines/fotos/' . $b['foto_portada'] : 'assets/img/placeholder.jpg';
                        ?>
                            <tr class="fila-boletin">
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="<?= htmlspecialchars($foto) ?>" alt="Portada" class="boletin-thumb">
                                        <span class="fw-bold text-dark d-block col-titulo" style="font-size: 14px;">
                                            <?= htmlspecialchars($b['numero_boletin']) ?>
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <span class="resumen-text col-resumen">
                                        <?= htmlspecialchars($b['resumen']) ?>
                                    </span>
                                </td>
                                <td class="text-secondary" style="font-size: 13px;">
                                    <i class="bi bi-calendar3 me-1 text-muted"></i>
                                    <?= date('d/m/Y', strtotime($b['fecha_publicacion'])) ?>
                                </td>
                                <td>
                                    <?php if(!empty($b['archivo_pdf'])): 
                                        $ruta_pdf = file_exists('uploads/boletines/pdfs/' . $b['archivo_pdf']) ? 'uploads/boletines/pdfs/' . $b['archivo_pdf'] : 'uploads/boletines/' . $b['archivo_pdf']; 
                                    ?>
                                        <a href="<?= htmlspecialchars($ruta_pdf) ?>" target="_blank" class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 badge-pdf text-decoration-none" title="Ver documento PDF">
                                            <i class="bi bi-file-earmark-pdf-fill"></i> PDF
                                        </a>
                                    <?php else: ?>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 badge-pdf">
                                            Sin archivo
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-inline-flex gap-1">
                                        <button class="btn btn-sm btn-outline-secondary" title="Editar" data-bs-toggle="modal" data-bs-target="#modalEditarBoletin_<?= $b['id'] ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="actions/procesar_boletin.php" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro que deseas eliminar este boletín, su foto y su archivo PDF?');">
                                            <input type="hidden" name="accion" value="eliminar">
                                            <input type="hidden" name="id" value="<?= $b['id'] ?>">
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
                                <i class="bi bi-journal-x fs-2 d-block mb-2"></i>
                                No hay boletines registrados actualmente.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- MODALES DE EDICIÓN (Fuera de la tabla) -->
<?php foreach($boletines as $b): ?>
<div class="modal fade" id="modalEditarBoletin_<?= $b['id'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <form action="actions/procesar_boletin.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="accion" value="editar">
                <input type="hidden" name="id" value="<?= $b['id'] ?>">
                
                <div class="modal-header border-bottom py-3 px-4">
                    <h5 class="modal-title fw-bold fs-5">Editar boletín</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Número / Título *</label>
                            <input type="text" name="numero_boletin" class="form-control" value="<?= htmlspecialchars($b['numero_boletin']) ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Fecha de publicación</label>
                            <input type="date" name="fecha_publicacion" class="form-control" value="<?= date('Y-m-d', strtotime($b['fecha_publicacion'])) ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Resumen *</label>
                        <textarea name="resumen" class="form-control" rows="3" required><?= htmlspecialchars($b['resumen']) ?></textarea>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Reemplazar foto de portada</label>
                            <input type="file" name="foto_portada" class="form-control" accept="image/*">
                            <div class="form-text">Dejar vacío para conservar la foto actual.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Reemplazar archivo PDF</label>
                            <input type="file" name="archivo_pdf" class="form-control" accept=".pdf">
                            <div class="form-text">Dejar vacío para conservar el PDF actual.</div>
                        </div>
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

<!-- MODAL NUEVO BOLETÍN -->
<div class="modal fade" id="modalNuevoBoletin" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <form action="actions/procesar_boletin.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="accion" value="guardar">
                
                <div class="modal-header border-bottom py-3 px-4">
                    <h5 class="modal-title fw-bold fs-5">Nuevo boletín</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Número / Título *</label>
                            <input type="text" name="numero_boletin" class="form-control" placeholder="Ej. NTEP #13" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Fecha de publicación</label>
                            <input type="date" name="fecha_publicacion" class="form-control" value="<?= date('Y-m-d') ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Resumen *</label>
                        <textarea name="resumen" class="form-control" rows="3" placeholder="Descripción breve del boletín..." required></textarea>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Foto de portada *</label>
                            <input type="file" name="foto_portada" class="form-control" accept="image/*" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Archivo PDF *</label>
                            <input type="file" name="archivo_pdf" class="form-control" accept=".pdf" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light px-4 py-3">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4">Guardar boletín</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- SCRIPT BUSCADOR EN TIEMPO REAL -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#inputBusquedaBoletines').on('keyup', function() {
        var valor = $(this).val().toLowerCase().trim();
        var visibles = 0;

        $('.fila-boletin').each(function() {
            var titulo = $(this).find('.col-titulo').text().toLowerCase();
            var resumen = $(this).find('.col-resumen').text().toLowerCase();

            if (titulo.indexOf(valor) !== -1 || resumen.indexOf(valor) !== -1) {
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