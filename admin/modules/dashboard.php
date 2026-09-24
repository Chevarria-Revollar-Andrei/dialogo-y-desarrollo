<?php
// modules/dashboard.php
require_once 'config/conexion.php';

// 1. Consultas para contar los registros actuales
$total_reportajes = $pdo->query("SELECT COUNT(*) FROM reportajes")->fetchColumn();
$total_noticias = $pdo->query("SELECT COUNT(*) FROM noticias")->fetchColumn();
$total_boletines = $pdo->query("SELECT COUNT(*) FROM boletines")->fetchColumn();
$total_podcasts = $pdo->query("SELECT COUNT(*) FROM podcasts")->fetchColumn();
$total_videos = $pdo->query("SELECT COUNT(*) FROM videos")->fetchColumn();
$total_multimedia = $total_podcasts + $total_videos;
$total_general = $total_reportajes + $total_noticias + $total_boletines + $total_multimedia;

// 2. Lógica para el gráfico de líneas (Rendimiento anual)
$anio_actual = date('Y');
$datos_mensuales = array_fill(1, 12, 0);

$tablas_fecha = [
    ['tabla' => 'reportajes', 'columna' => 'created_at'],
    ['tabla' => 'noticias', 'columna' => 'fecha_publicacion'],
    ['tabla' => 'boletines', 'columna' => 'fecha_publicacion'],
    ['tabla' => 'podcasts', 'columna' => 'fecha_publicacion'],
    ['tabla' => 'videos', 'columna' => 'fecha_publicacion']
];

foreach ($tablas_fecha as $t) {
    $sql = "SELECT MONTH({$t['columna']}) as mes, COUNT(*) as total 
            FROM {$t['tabla']} 
            WHERE YEAR({$t['columna']}) = :anio 
            GROUP BY MONTH({$t['columna']})";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['anio' => $anio_actual]);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $mes = (int)$row['mes'];
        $datos_mensuales[$mes] += (int)$row['total'];
    }
}
$datos_json = json_encode(array_values($datos_mensuales));

// 3. Actividad reciente
$sql_actividad = "
    SELECT 'Reportaje' as tipo, titulo as nombre, created_at as fecha, 'bi-newspaper' as icono, 'text-primary' as color, 'bg-primary' as bg_light FROM reportajes
    UNION ALL
    SELECT 'Noticia' as tipo, titulo as nombre, fecha_publicacion as fecha, 'bi-lightning-charge-fill' as icono, 'text-success' as color, 'bg-success' as bg_light FROM noticias
    UNION ALL
    SELECT 'Boletín' as tipo, numero_boletin as nombre, fecha_publicacion as fecha, 'bi-file-earmark-pdf-fill' as icono, 'text-danger' as color, 'bg-danger' as bg_light FROM boletines
    ORDER BY fecha DESC LIMIT 5
";
$actividades = $pdo->query($sql_actividad)->fetchAll(PDO::FETCH_ASSOC);

$nombre_usuario = $_SESSION['nombres'] ?? 'Administrador';
?>

<section class="section-view active">
    <!-- CABECERA DE BIENVENIDA -->
    <div class="page-head d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-primary bg-opacity-10 text-primary font-weight-bold px-2 py-1" style="font-size: 11px; letter-spacing: 0.5px;">
                    <i class="bi bi-calendar3 me-1"></i> <?= date('d M, Y') ?>
                </span>
                <span class="eyebrow text-uppercase text-muted" style="font-size: 11px; font-weight: 700; letter-spacing: 1px;">• PANEL DE CONTROL</span>
            </div>
            <h1 class="mb-1 fw-bold" style="font-size: 26px; color: #0f172a; letter-spacing: -0.5px;">
                ¡Hola de nuevo, <?= htmlspecialchars($nombre_usuario) ?>! 👋
            </h1>
            <p class="text-muted mb-0" style="font-size: 14px;">Resumen ejecutivo y métricas en tiempo real de tu portal.</p>
        </div>
        <div class="actions d-flex gap-2">
            <button class="btn btn-white bg-white border shadow-sm px-3" style="border-radius: 10px; font-weight: 600; font-size: 14px; color: #475569;" onclick="location.reload()">
                <i class="bi bi-arrow-clockwise me-1"></i> Actualizar
            </button>
            <div class="dropdown">
                <button class="btn btn-primary shadow-sm px-3 dropdown-toggle" type="button" data-bs-toggle="dropdown" style="border-radius: 10px; font-weight: 600; font-size: 14px;">
                    <i class="bi bi-plus-lg me-1"></i> Nuevo contenido
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2" style="border-radius: 12px; min-width: 200px;">
                    <li><a class="dropdown-item py-2" href="panel.php?modulo=reportajes"><i class="bi bi-newspaper me-2 text-primary"></i> Reportaje</a></li>
                    <li><a class="dropdown-item py-2" href="panel.php?modulo=noticias"><i class="bi bi-lightning-charge me-2 text-success"></i> Noticia</a></li>
                    <li><a class="dropdown-item py-2" href="panel.php?modulo=boletines"><i class="bi bi-file-earmark-pdf me-2 text-danger"></i> Boletín</a></li>
                    <li><hr class="dropdown-divider my-1"></li>
                    <li><a class="dropdown-item py-2" href="panel.php?modulo=podcasts"><i class="bi bi-mic me-2 text-warning"></i> Podcast</a></li>
                    <li><a class="dropdown-item py-2" href="panel.php?modulo=videos"><i class="bi bi-play-circle me-2 text-info"></i> Video</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- TARJETAS DE ESTADÍSTICAS -->
    <div class="row g-3 mb-4">
        <!-- Reportajes -->
        <div class="col-md-3">
            <div class="cardx stat h-100 p-4 border-top-accent border-primary">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="stat-title">Reportajes</span>
                    <div class="icon-shape bg-primary-subtle text-primary">
                        <i class="bi bi-newspaper fs-5"></i>
                    </div>
                </div>
                <h3 class="stat-value mb-2"><?= number_format($total_reportajes) ?></h3>
                <div class="d-flex align-items-center text-success gap-1" style="font-size: 12px; font-weight: 600;">
                    <i class="bi bi-arrow-up-short fs-6"></i>
                    <span>Artículos de investigación</span>
                </div>
            </div>
        </div>
        <!-- Noticias -->
        <div class="col-md-3">
            <div class="cardx stat h-100 p-4 border-top-accent border-success">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="stat-title">Noticias</span>
                    <div class="icon-shape bg-success-subtle text-success">
                        <i class="bi bi-lightning-charge-fill fs-5"></i>
                    </div>
                </div>
                <h3 class="stat-value mb-2"><?= number_format($total_noticias) ?></h3>
                <div class="d-flex align-items-center text-success gap-1" style="font-size: 12px; font-weight: 600;">
                    <i class="bi bi-check-circle-fill" style="font-size: 10px;"></i>
                    <span>Publicaciones activas</span>
                </div>
            </div>
        </div>
        <!-- Multimedia -->
        <div class="col-md-3">
            <div class="cardx stat h-100 p-4 border-top-accent border-warning">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="stat-title">Multimedia</span>
                    <div class="icon-shape bg-warning-subtle text-warning">
                        <i class="bi bi-collection-play-fill fs-5"></i>
                    </div>
                </div>
                <h3 class="stat-value mb-2"><?= number_format($total_multimedia) ?></h3>
                <div class="d-flex align-items-center text-muted gap-2" style="font-size: 12px; font-weight: 500;">
                    <span class="badge bg-light text-dark border"><?= $total_podcasts ?> Podcasts</span>
                    <span class="badge bg-light text-dark border"><?= $total_videos ?> Videos</span>
                </div>
            </div>
        </div>
        <!-- Boletines -->
        <div class="col-md-3">
            <div class="cardx stat h-100 p-4 border-top-accent border-danger">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="stat-title">Boletines NTEP</span>
                    <div class="icon-shape bg-danger-subtle text-danger">
                        <i class="bi bi-file-earmark-pdf-fill fs-5"></i>
                    </div>
                </div>
                <h3 class="stat-value mb-2"><?= number_format($total_boletines) ?></h3>
                <div class="d-flex align-items-center text-danger gap-1" style="font-size: 12px; font-weight: 600;">
                    <i class="bi bi-file-text-fill" style="font-size: 10px;"></i>
                    <span>Documentos PDF</span>
                </div>
            </div>
        </div>
    </div>

    <!-- PANELES DE GRÁFICOS -->
    <div class="row g-4 mb-4">
        <!-- Gráfico de Líneas -->
        <div class="col-lg-8">
            <div class="cardx h-100 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="card-heading">Rendimiento de publicación (<?= $anio_actual ?>)</h2>
                        <p class="text-muted mb-0" style="font-size: 13px;">Volumen mensual acumulado de contenido generado</p>
                    </div>
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1" style="border-radius: 6px;">
                        <i class="bi bi-pulse me-1"></i> EN VIVO
                    </span>
                </div>
                <div style="height: 290px; width: 100%;">
                    <canvas id="contentChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Gráfico Circular (Distribución) -->
        <div class="col-lg-4">
            <div class="cardx h-100 p-4 position-relative">
                <div class="mb-3">
                    <h2 class="card-heading">Distribución global</h2>
                    <p class="text-muted mb-0" style="font-size: 13px;">Proporción por tipo de contenido</p>
                </div>
                <div style="height: 230px; width: 100%; position: relative;" class="my-2">
                    <canvas id="doughnutChart"></canvas>
                    <!-- Total en el centro del Doughnut -->
                    <div class="chart-center-label">
                        <span class="fw-bold fs-3 text-dark d-block leading-none"><?= $total_general ?></span>
                        <small class="text-uppercase text-muted fw-semibold" style="font-size: 10px; letter-spacing: 0.5px;">Totales</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ZONA INFERIOR: ACCESOS RÁPIDOS Y ACTIVIDAD -->
    <div class="row g-4 mb-4">
        <!-- Accesos Rápidos -->
        <div class="col-lg-7">
            <h5 class="section-subtitle mb-3">Accesos rápidos</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <a href="panel.php?modulo=reportajes" class="text-decoration-none">
                        <div class="cardx p-3 d-flex align-items-center justify-content-between action-card">
                            <div class="d-flex align-items-center">
                                <div class="action-icon bg-primary text-white me-3">
                                    <i class="bi bi-pencil-square"></i>
                                </div>
                                <div>
                                    <strong class="text-dark d-block fs-6">Crear reportaje</strong>
                                    <span class="text-muted" style="font-size: 12px;">Redacta un artículo extensible</span>
                                </div>
                            </div>
                            <i class="bi bi-chevron-right text-muted action-arrow"></i>
                        </div>
                    </a>
                </div>
                <div class="col-md-6">
                    <a href="panel.php?modulo=noticias" class="text-decoration-none">
                        <div class="cardx p-3 d-flex align-items-center justify-content-between action-card">
                            <div class="d-flex align-items-center">
                                <div class="action-icon bg-success text-white me-3">
                                    <i class="bi bi-lightning-fill"></i>
                                </div>
                                <div>
                                    <strong class="text-dark d-block fs-6">Agregar noticia</strong>
                                    <span class="text-muted" style="font-size: 12px;">Publicación de última hora</span>
                                </div>
                            </div>
                            <i class="bi bi-chevron-right text-muted action-arrow"></i>
                        </div>
                    </a>
                </div>
                <div class="col-md-6">
                    <a href="panel.php?modulo=podcasts" class="text-decoration-none">
                        <div class="cardx p-3 d-flex align-items-center justify-content-between action-card">
                            <div class="d-flex align-items-center">
                                <div class="action-icon bg-warning text-white me-3">
                                    <i class="bi bi-mic-fill"></i>
                                </div>
                                <div>
                                    <strong class="text-dark d-block fs-6">Nuevo podcast</strong>
                                    <span class="text-muted" style="font-size: 12px;">Sube episodios en audio</span>
                                </div>
                            </div>
                            <i class="bi bi-chevron-right text-muted action-arrow"></i>
                        </div>
                    </a>
                </div>
                <div class="col-md-6">
                    <a href="panel.php?modulo=videos" class="text-decoration-none">
                        <div class="cardx p-3 d-flex align-items-center justify-content-between action-card">
                            <div class="d-flex align-items-center">
                                <div class="action-icon text-white me-3" style="background: #8b5cf6;">
                                    <i class="bi bi-play-fill fs-5"></i>
                                </div>
                                <div>
                                    <strong class="text-dark d-block fs-6">Añadir video</strong>
                                    <span class="text-muted" style="font-size: 12px;">Enlaza material audiovisual</span>
                                </div>
                            </div>
                            <i class="bi bi-chevron-right text-muted action-arrow"></i>
                        </div>
                    </a>
                </div>
                <div class="col-12">
                    <a href="panel.php?modulo=boletines" class="text-decoration-none">
                        <div class="cardx p-3 d-flex align-items-center justify-content-between action-card">
                            <div class="d-flex align-items-center">
                                <div class="action-icon bg-danger text-white me-3">
                                    <i class="bi bi-file-earmark-pdf-fill"></i>
                                </div>
                                <div>
                                    <strong class="text-dark d-block fs-6">Subir boletín informativo</strong>
                                    <span class="text-muted" style="font-size: 12px;">Carga ediciones descargables en PDF para la comunidad NTEP</span>
                                </div>
                            </div>
                            <i class="bi bi-chevron-right text-muted action-arrow"></i>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Actividad Reciente (Timeline Style) -->
        <div class="col-lg-5">
            <h5 class="section-subtitle mb-3">Actividad reciente</h5>
            <div class="cardx p-4 h-100">
                <div class="timeline-feed">
                    <?php if(count($actividades) > 0): ?>
                        <?php foreach($actividades as $act): ?>
                            <div class="timeline-item d-flex pb-3 position-relative">
                                <div class="timeline-icon me-3 <?= $act['bg_light'] ?> bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px;">
                                    <i class="bi <?= $act['icono'] ?> <?= $act['color'] ?> fs-6"></i>
                                </div>
                                <div class="timeline-content overflow-hidden pe-2">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <span class="badge bg-light text-dark border" style="font-size: 10px; font-weight: 600;"><?= $act['tipo'] ?></span>
                                        <small class="text-muted" style="font-size: 11px;"><?= date('d M', strtotime($act['fecha'])) ?></small>
                                    </div>
                                    <p class="mb-0 text-dark font-medium text-truncate" style="font-size: 13px;" title="<?= htmlspecialchars($act['nombre']) ?>">
                                        <?= htmlspecialchars($act['nombre']) ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-2 d-block mb-1"></i>
                            <span style="font-size: 13px;">Sin actividad registrada.</span>
                        </div>
                    <?php endif; ?>

                    <div class="d-flex align-items-center pt-3 border-top mt-1">
                        <div class="avatar-circle me-3 bg-secondary bg-opacity-10 text-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                            <i class="bi bi-person-circle fs-5"></i>
                        </div>
                        <div>
                            <strong class="text-dark d-block" style="font-size: 13px;">Sesión iniciada</strong>
                            <span class="text-muted" style="font-size: 11px;"><?= htmlspecialchars($nombre_usuario) ?> · En línea</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    /* Estilos generales mejorados */
    .cardx {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03), 0 1px 2px rgba(15, 23, 42, 0.02);
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .border-top-accent {
        border-top-width: 4px !important;
    }

    .stat-title {
        font-size: 13px;
        font-weight: 600;
        color: #64748b;
        letter-spacing: 0.2px;
    }

    .stat-value {
        font-size: 30px;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.5px;
        line-height: 1;
    }

    .icon-shape {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .bg-primary-subtle { background-color: #eff6ff; }
    .bg-success-subtle { background-color: #f0fdf4; }
    .bg-warning-subtle { background-color: #fffbebf; }
    .bg-danger-subtle { background-color: #fef2f2; }

    .card-heading {
        font-size: 17px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 2px;
    }

    .section-subtitle {
        font-weight: 700;
        color: #0f172a;
        font-size: 16px;
    }

    /* Cards de accesos rápidos */
    .action-card {
        cursor: pointer;
    }
    .action-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 15px -3px rgba(0, 0, 0, 0.06), 0 4px 6px -2px rgba(0, 0, 0, 0.03) !important;
        border-color: #cbd5e1 !important;
    }
    .action-card:hover .action-arrow {
        transform: translateX(3px);
        color: #0f172a !important;
    }
    .action-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .action-arrow {
        transition: transform 0.2s ease, color 0.2s ease;
    }

    /* Timeline activity */
    .timeline-item:not(:last-child)::before {
        content: '';
        position: absolute;
        left: 19px;
        top: 38px;
        bottom: 0;
        width: 2px;
        background-color: #f1f5f9;
    }

    /* Total en Doughnut */
    .chart-center-label {
        position: absolute;
        top: 42%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        pointer-events: none;
    }
    .leading-none { line-height: 1; }
</style>

<script>
    // Gráfico 1: Rendimiento (Líneas)
    if(document.getElementById('contentChart')){
        const datosReales = <?= $datos_json ?>;
        const ctx = document.getElementById('contentChart').getContext('2d');
        
        // Gradiente para el área bajo la curva
        const gradient = ctx.createLinearGradient(0, 0, 0, 280);
        gradient.addColorStop(0, 'rgba(59, 130, 246, 0.2)');
        gradient.addColorStop(1, 'rgba(59, 130, 246, 0.0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'],
                datasets: [{
                    label: 'Publicaciones',
                    data: datosReales,
                    borderWidth: 3,
                    tension: 0.35,
                    fill: true,
                    backgroundColor: gradient,
                    borderColor: '#3b82f6',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#3b82f6',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0, stepSize: 1, color: '#94a3b8', font: { size: 11 } },
                        grid: { color: '#f1f5f9' },
                        border: { dash: [4, 4], display: false }
                    },
                    x: {
                        ticks: { color: '#94a3b8', font: { size: 11 } },
                        grid: { display: false },
                        border: { display: false }
                    }
                }
            }
        });
    }

    // Gráfico 2: Distribución (Doughnut)
    if(document.getElementById('doughnutChart')){
        const datosDoughnut = [<?= $total_reportajes ?>, <?= $total_noticias ?>, <?= $total_boletines ?>, <?= $total_podcasts ?>, <?= $total_videos ?>];
        
        new Chart(document.getElementById('doughnutChart'), {
            type: 'doughnut',
            data: {
                labels: ['Reportajes', 'Noticias', 'Boletines', 'Podcasts', 'Videos'],
                datasets: [{
                    data: datosDoughnut,
                    backgroundColor: ['#3b82f6', '#10b981', '#ef4444', '#f59e0b', '#8b5cf6'],
                    borderWidth: 3,
                    borderColor: '#ffffff',
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '78%',
                plugins: {
                    legend: { 
                        position: 'bottom', 
                        labels: { 
                            boxWidth: 8, 
                            usePointStyle: true,
                            padding: 12, 
                            font: { size: 11, family: 'system-ui' } 
                        } 
                    },
                    tooltip: { 
                        backgroundColor: '#0f172a', 
                        padding: 10, 
                        cornerRadius: 8,
                        bodyFont: { size: 12, weight: 'bold' } 
                    }
                }
            }
        });
    }
</script>