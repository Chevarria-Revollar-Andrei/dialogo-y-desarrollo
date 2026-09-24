<?php
// modules/autores.php
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

// Obtener autores y sus publicaciones
try {
    $sql = "SELECT a.*, COUNT(r.id) AS total_reportajes 
            FROM autores a 
            LEFT JOIN reportajes r ON a.id = r.autor_id 
            GROUP BY a.id 
            ORDER BY a.nombres ASC, a.ap_paterno ASC";
    $stmt = $pdo->query($sql);
    $autores = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total_registros = count($autores);

    // Métricas rápidas
    $con_nickname_count = 0;
    $total_publicaciones_sum = 0;
    foreach ($autores as $a) {
        if (!empty($a['es_nickname'])) $con_nickname_count++;
        $total_publicaciones_sum += (int)$a['total_reportajes'];
    }
} catch (PDOException $e) {
    $autores = []; 
    $total_registros = 0; 
    $con_nickname_count = 0;
    $total_publicaciones_sum = 0;
    $error_bd = "Error SQL: " . $e->getMessage();
}
?>

<!-- jQuery para filtro dinámico -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
/* Estilos modernizados para el módulo de Autores (Idénticos a Reportajes) */
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
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: #f1f5f9;
    color: #475569;
    font-weight: 700;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #e2e8f0;
    flex-shrink: 0;
}
.badge-count {
    background: #f8fafc;
    color: #64748b;
    border: 1px solid #e2e8f0;
    font-weight: 500;
    padding: 6px 12px;
    border-radius: 20px;
}
.badge-nickname {
    background: #eff6ff;
    color: #2563eb;
    border: 1px solid #bfdbfe;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 12px;
}
</style>

<section class="section-view active">
    <!-- Cabecera -->
    <div class="page-head d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="eyebrow text-uppercase text-muted fw-bold mb-1" style="font-size: 11px; letter-spacing: 0.5px;">Panel Editorial</div>
            <h1 class="h3 fw-bold m-0">Autores</h1>
        </div>
        <button class="btn btn-primary d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalNuevoAutor">
            <i class="bi bi-plus-lg"></i> Nuevo autor
        </button>
    </div>

    <?= $alerta ?>
    <?php if(isset($error_bd)) echo '<div class="alert alert-warning mb-4">'.$error_bd.'</div>'; ?>

    <!-- Tarjetas de Estadísticas -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stats-card">
                <div class="stats-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-people"></i></div>
                <div>
                    <h3 class="mb-0 fw-bold"><?= $total_registros ?></h3>
                    <span class="text-muted small">Total de Autores</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card">
                <div class="stats-icon bg-info bg-opacity-10 text-info"><i class="bi bi-incognito"></i></div>
                <div>
                    <h3 class="mb-0 fw-bold"><?= $con_nickname_count ?></h3>
                    <span class="text-muted small">Usan Seudónimo</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card">
                <div class="stats-icon bg-success bg-opacity-10 text-success"><i class="bi bi-journal-text"></i></div>
                <div>
                    <h3 class="mb-0 fw-bold"><?= $total_publicaciones_sum ?></h3>
                    <span class="text-muted small">Publicaciones Totales</span>
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
                <input type="text" id="inputBusqueda" class="form-control" placeholder="Buscar por nombre, apellidos o seudónimo...">
            </div>
            <div class="text-muted small">
                Mostrando <strong id="contadorVisibles"><?= $total_registros ?></strong> registros
            </div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle mb-0" id="tablaAutores" style="width: 100%;">
                <thead class="table-light">
                    <tr>
                        <th style="width: 45%; border:none;" class="ps-4">AUTOR</th>
                        <th style="width: 25%; border:none;">TIPO / SEUDÓNIMO</th>
                        <th style="width: 15%; border:none;">PUBLICACIONES</th>
                        <th style="width: 15%; border:none;" class="text-end pe-4">ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($total_registros > 0): ?>
                        <?php foreach($autores as $a): 
                            $nombre_completo = trim($a['nombres'] . ' ' . $a['ap_paterno'] . ' ' . $a['ap_materno']);
                            $mostrar_nombre = $a['es_nickname'] && !empty($a['nickname']) ? $a['nickname'] : $nombre_completo;
                            
                            if ($a['es_nickname'] && !empty($a['nickname'])) {
                                $iniciales = mb_strtoupper(mb_substr($a['nickname'], 0, 2));
                            } else {
                                $iniciales = mb_strtoupper(mb_substr($a['nombres'], 0, 1)) . mb_strtoupper(mb_substr((string)$a['ap_paterno'], 0, 1));
                            }
                        ?>
                            <tr class="fila-autor">
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-circle">
                                            <?= htmlspecialchars($iniciales) ?>
                                        </div>
                                        <div>
                                            <span class="fw-bold text-dark d-block col-nombre" style="font-size: 14px;">
                                                <?= htmlspecialchars($mostrar_nombre) ?>
                                            </span>
                                            <?php if($a['es_nickname']): ?>
                                                <span class="text-muted small d-block" style="font-size: 12px;">
                                                    Nombre real: <?= htmlspecialchars($nombre_completo) ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="col-nickname">
                                    <?php if($a['es_nickname']): ?>
                                        <span class="badge badge-nickname"><i class="bi bi-mask me-1"></i> Seudónimo</span>
                                    <?php else: ?>
                                        <span class="text-muted small">Nombre Real</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge badge-count">
                                        <i class="bi bi-file-earmark-text me-1 text-muted"></i>
                                        <?= $a['total_reportajes'] ?> <?= $a['total_reportajes'] == 1 ? 'reportaje' : 'reportajes' ?>
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-inline-flex gap-1">
                                        <!-- BOTÓN EDITAR -->
                                        <button class="btn btn-sm btn-outline-secondary" title="Editar" data-bs-toggle="modal" data-bs-target="#modalEditar_<?= $a['id'] ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>

                                        <!-- BOTÓN ELIMINAR -->
                                        <form action="actions/procesar_autor.php" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro que deseas eliminar este autor?');">
                                            <input type="hidden" name="accion" value="eliminar">
                                            <input type="hidden" name="id" value="<?= $a['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar" <?= ($a['total_reportajes'] > 0) ? 'disabled title="No se puede eliminar porque tiene reportajes asociados"' : '' ?>>
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
                                <i class="bi bi-people fs-2 d-block mb-2"></i>
                                No hay autores registrados actualmente.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- MODALES DE EDICIÓN -->
<?php foreach($autores as $a): ?>
<div class="modal fade" id="modalEditar_<?= $a['id'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="actions/procesar_autor.php" method="POST">
                <input type="hidden" name="accion" value="editar">
                <input type="hidden" name="id" value="<?= $a['id'] ?>">
                
                <div class="modal-header border-bottom py-3 px-4">
                    <h5 class="modal-title fw-bold fs-5">Editar autor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombres *</label>
                        <input type="text" name="nombres" class="form-control" value="<?= htmlspecialchars($a['nombres']) ?>" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Apellido Paterno</label>
                            <input type="text" name="ap_paterno" class="form-control" value="<?= htmlspecialchars($a['ap_paterno']) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Apellido Materno</label>
                            <input type="text" name="ap_materno" class="form-control" value="<?= htmlspecialchars($a['ap_materno']) ?>">
                        </div>
                    </div>
                    <div class="form-check form-switch my-3">
                        <input class="form-check-input" type="checkbox" name="es_nickname" value="1" id="checkNick_<?= $a['id'] ?>" <?= $a['es_nickname'] ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="checkNick_<?= $a['id'] ?>">Usar seudónimo o nombre artístico</label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nickname / Seudónimo</label>
                        <input type="text" name="nickname" class="form-control" value="<?= htmlspecialchars($a['nickname']) ?>" placeholder="Ej. El Observador">
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

<!-- MODAL NUEVO AUTOR -->
<div class="modal fade" id="modalNuevoAutor" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="actions/procesar_autor.php" method="POST">
                <input type="hidden" name="accion" value="guardar">
                
                <div class="modal-header border-bottom py-3 px-4">
                    <h5 class="modal-title fw-bold fs-5">Nuevo autor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombres *</label>
                        <input type="text" name="nombres" class="form-control" placeholder="Ej. Juan Carlos" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Apellido Paterno</label>
                            <input type="text" name="ap_paterno" class="form-control" placeholder="Pérez">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Apellido Materno</label>
                            <input type="text" name="ap_materno" class="form-control" placeholder="Gómez">
                        </div>
                    </div>
                    <div class="form-check form-switch my-3">
                        <input class="form-check-input" type="checkbox" name="es_nickname" value="1" id="checkNuevoNick">
                        <label class="form-check-label fw-semibold" for="checkNuevoNick">Usar seudónimo o nombre artístico</label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nickname / Seudónimo</label>
                        <input type="text" name="nickname" class="form-control" placeholder="Ej. El Observador">
                    </div>
                </div>
                <div class="modal-footer border-top bg-light px-4 py-3">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4">Guardar autor</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- SCRIPTS DE INTERACCIÓN -->
<script>
$(document).ready(function() {
    // Lógica del Buscador en Tiempo Real
    $('#inputBusqueda').on('keyup', function() {
        var valor = $(this).val().toLowerCase().trim();
        var visibles = 0;

        $('.fila-autor').each(function() {
            var nombre = $(this).find('.col-nombre').text().toLowerCase();
            var nickname = $(this).find('.col-nickname').text().toLowerCase();

            if (nombre.indexOf(valor) !== -1 || nickname.indexOf(valor) !== -1) {
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