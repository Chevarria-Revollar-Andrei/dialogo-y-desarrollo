<?php
// modules/usuarios.php
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

// Obtener usuarios y métricas
try {
    $stmt = $pdo->query("SELECT * FROM usuarios ORDER BY nombres ASC, ap_paterno ASC");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total_registros = count($usuarios);

    // Contadores para las tarjetas
    $total_admins = 0;
    $total_editores_redactores = 0;
    foreach ($usuarios as $u) {
        if ($u['rol'] === 'admin') {
            $total_admins++;
        } else {
            $total_editores_redactores++;
        }
    }
} catch (PDOException $e) {
    $usuarios = []; 
    $total_registros = 0; 
    $total_admins = 0;
    $total_editores_redactores = 0;
    $error_bd = "Error SQL: " . $e->getMessage();
}

// Función para traducir y estilizar roles
function getBadgeRol($rol) {
    switch ($rol) {
        case 'admin':
            return '<span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1"><i class="bi bi-shield-lock me-1"></i> Administrador</span>';
        case 'editor':
            return '<span class="badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill px-3 py-1"><i class="bi bi-pencil-square me-1"></i> Editor</span>';
        case 'redactor':
            return '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-3 py-1"><i class="bi bi-feather me-1"></i> Redactor</span>';
        default:
            return '<span class="badge bg-light text-dark border rounded-pill px-3 py-1">' . ucfirst($rol) . '</span>';
    }
}
?>

<!-- jQuery para filtro dinámico -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
/* Estilos modernizados e idénticos al resto de los módulos */
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
    background-color: #e0e7ff;
    color: #3730a3;
    font-weight: 700;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #c7d2fe;
    flex-shrink: 0;
}
</style>

<section class="section-view active">
    <!-- Cabecera -->
    <div class="page-head d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="eyebrow text-uppercase text-muted fw-bold mb-1" style="font-size: 11px; letter-spacing: 0.5px;">Gestión de accesos</div>
            <h1 class="h3 fw-bold m-0">Usuarios</h1>
        </div>
        <button class="btn btn-primary d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalNuevoUsuario">
            <i class="bi bi-person-plus-fill"></i> Nuevo usuario
        </button>
    </div>

    <?= $alerta ?>
    <?php if(isset($error_bd)) echo '<div class="alert alert-warning mb-4">'.$error_bd.'</div>'; ?>

    <!-- Tarjetas de Estadísticas -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stats-card">
                <div class="stats-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-people-fill"></i></div>
                <div>
                    <h3 class="mb-0 fw-bold"><?= $total_registros ?></h3>
                    <span class="text-muted small">Cuentas Totales</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card">
                <div class="stats-icon bg-danger bg-opacity-10 text-danger"><i class="bi bi-shield-check"></i></div>
                <div>
                    <h3 class="mb-0 fw-bold"><?= $total_admins ?></h3>
                    <span class="text-muted small">Administradores</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card">
                <div class="stats-icon bg-info bg-opacity-10 text-info"><i class="bi bi-person-badge"></i></div>
                <div>
                    <h3 class="mb-0 fw-bold"><?= $total_editores_redactores ?></h3>
                    <span class="text-muted small">Editores / Redactores</span>
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
                <input type="text" id="inputBusqueda" class="form-control" placeholder="Buscar por usuario, correo o rol...">
            </div>
            <div class="text-muted small">
                Mostrando <strong id="contadorVisibles"><?= $total_registros ?></strong> usuarios
            </div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle mb-0" id="tablaUsuarios" style="width: 100%;">
                <thead class="table-light">
                    <tr>
                        <th style="width: 40%; border:none;" class="ps-4">USUARIO</th>
                        <th style="width: 25%; border:none;">ROL EN EL SISTEMA</th>
                        <th style="width: 20%; border:none;">ESTADO</th>
                        <th style="width: 15%; border:none;" class="text-end pe-4">ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($total_registros > 0): ?>
                        <?php foreach($usuarios as $u): 
                            $nombre_completo = trim($u['nombres'] . ' ' . $u['ap_paterno'] . ' ' . $u['ap_materno']);
                            $iniciales = mb_strtoupper(mb_substr($u['nombres'], 0, 1)) . mb_strtoupper(mb_substr((string)$u['ap_paterno'], 0, 1));
                        ?>
                            <tr class="fila-usuario">
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-circle">
                                            <?= htmlspecialchars($iniciales) ?>
                                        </div>
                                        <div>
                                            <span class="fw-bold text-dark d-block col-nombre" style="font-size: 14px;">
                                                <?= htmlspecialchars($nombre_completo) ?>
                                            </span>
                                            <span class="text-muted small d-block col-email" style="font-size: 12px;">
                                                <?= htmlspecialchars($u['email']) ?>
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="col-rol">
                                    <?= getBadgeRol($u['rol']) ?>
                                </td>
                                <td>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1" style="font-size: 12px;">
                                        <i class="bi bi-check-circle-fill me-1"></i> Activo
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-inline-flex gap-1">
                                        <!-- BOTÓN EDITAR -->
                                        <button class="btn btn-sm btn-outline-secondary" title="Editar" data-bs-toggle="modal" data-bs-target="#modalEditarUsuario_<?= $u['id'] ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>

                                        <!-- BOTÓN ELIMINAR -->
                                        <form action="actions/procesar_usuario.php" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro que deseas eliminar a este usuario?');">
                                            <input type="hidden" name="accion" value="eliminar">
                                            <input type="hidden" name="id" value="<?= $u['id'] ?>">
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
                                <i class="bi bi-people fs-2 d-block mb-2"></i>
                                No hay usuarios registrados actualmente.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- MODALES DE EDICIÓN -->
<?php foreach($usuarios as $u): ?>
<div class="modal fade" id="modalEditarUsuario_<?= $u['id'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="actions/procesar_usuario.php" method="POST">
                <input type="hidden" name="accion" value="editar">
                <input type="hidden" name="id" value="<?= $u['id'] ?>">
                
                <div class="modal-header border-bottom py-3 px-4">
                    <h5 class="modal-title fw-bold fs-5">Editar usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombres *</label>
                        <input type="text" name="nombres" class="form-control" value="<?= htmlspecialchars($u['nombres']) ?>" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Apellido Paterno *</label>
                            <input type="text" name="ap_paterno" class="form-control" value="<?= htmlspecialchars($u['ap_paterno']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Apellido Materno</label>
                            <input type="text" name="ap_materno" class="form-control" value="<?= htmlspecialchars($u['ap_materno']) ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Correo Electrónico *</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($u['email']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Rol en el sistema *</label>
                        <select name="rol" class="form-select" required>
                            <option value="admin" <?= $u['rol'] == 'admin' ? 'selected' : '' ?>>Administrador</option>
                            <option value="editor" <?= $u['rol'] == 'editor' ? 'selected' : '' ?>>Editor</option>
                            <option value="redactor" <?= $u['rol'] == 'redactor' ? 'selected' : '' ?>>Redactor</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nueva Contraseña</label>
                        <input type="password" name="password" class="form-control" placeholder="Dejar en blanco para conservar la actual">
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

<!-- MODAL NUEVO USUARIO -->
<div class="modal fade" id="modalNuevoUsuario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="actions/procesar_usuario.php" method="POST">
                <input type="hidden" name="accion" value="guardar">
                
                <div class="modal-header border-bottom py-3 px-4">
                    <h5 class="modal-title fw-bold fs-5">Nuevo usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombres *</label>
                        <input type="text" name="nombres" class="form-control" placeholder="Ej. Carlos Eduardo" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Apellido Paterno *</label>
                            <input type="text" name="ap_paterno" class="form-control" placeholder="Pérez" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Apellido Materno</label>
                            <input type="text" name="ap_materno" class="form-control" placeholder="Gómez">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Correo Electrónico *</label>
                        <input type="email" name="email" class="form-control" placeholder="ejemplo@dialogoydesarrollo.com.pe" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Rol en el sistema *</label>
                        <select name="rol" class="form-select" required>
                            <option value="redactor" selected>Redactor</option>
                            <option value="editor">Editor</option>
                            <option value="admin">Administrador</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Contraseña Temporal *</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light px-4 py-3">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4">Crear usuario</button>
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

        $('.fila-usuario').each(function() {
            var nombre = $(this).find('.col-nombre').text().toLowerCase();
            var email = $(this).find('.col-email').text().toLowerCase();
            var rol = $(this).find('.col-rol').text().toLowerCase();

            if (nombre.indexOf(valor) !== -1 || email.indexOf(valor) !== -1 || rol.indexOf(valor) !== -1) {
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