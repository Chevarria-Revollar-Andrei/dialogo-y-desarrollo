<?php 
// 1. Conexión y captura del ID
require_once 'admin/config/conexion.php'; 

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id_reportaje = (int)$_GET['id'];

// 2. Consulta del reportaje principal
$sql = "SELECT r.*, a.nombres, a.ap_paterno 
        FROM reportajes r 
        LEFT JOIN autores a ON r.autor_id = a.id 
        WHERE r.id = :id LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $id_reportaje]);
$reportaje = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$reportaje) {
    header("Location: reportajes.php");
    exit;
}

// 3. Función para formatear fechas
function formatearFecha($fecha_bd) {
    $meses = ['01'=>'Ene', '02'=>'Feb', '03'=>'Mar', '04'=>'Abr', '05'=>'May', '06'=>'Jun', '07'=>'Jul', '08'=>'Ago', '09'=>'Sep', '10'=>'Oct', '11'=>'Nov', '12'=>'Dic'];
    $fecha = strtotime($fecha_bd);
    $mes = $meses[date('m', $fecha)];
    return $mes . " " . date('d', $fecha) . ", " . date('Y', $fecha);
}

// 4. Traer las 3 últimas noticias
$sql_ultimos = "SELECT id, titulo, fecha_publicacion FROM reportajes WHERE id != :id ORDER BY fecha_publicacion DESC LIMIT 3";
$stmt_ultimos = $pdo->prepare($sql_ultimos);
$stmt_ultimos->execute(['id' => $id_reportaje]);
$ultimos_reportajes = $stmt_ultimos->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php'; 
?>

<!-- Breadcrumb -->
<section class="breadcrumb-area py-sm-5 py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb-contents">
                    <h2 class="title-big">Reportajes</h2>
                    <div class="breadcrumb">
                        <ul>
                            <li><a href="index.php">Inicio</a></li>
                            <li class="active"><a href="reportajes.php">Reportajes</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contenido del Reportaje -->
<section class="w3l-blog mt-lg-5">
    <div class="text-element-9 py-5 mt-lg-5">
        <div class="container py-lg-3">
            <div class="row grid-text-9">
                <div class="col-lg-8">
                    <div class="blog-single-post">
                        
                        <!-- TÍTULO -->
                        <div class="post-content">
                            <h2 class="title-single mb-3"><?php echo htmlspecialchars($reportaje['titulo']); ?></h2>
                        </div>
                        
                        <!-- IMAGEN PRINCIPAL Y PDF (Solo sale aquí, una sola vez) -->
                        <div class="single-post-image mb-4 text-center">
                            <?php 
                                $ruta_foto = !empty($reportaje['foto_principal']) ? 'admin/' . $reportaje['foto_principal'] : 'assets/images/default.jpg';
                                
                                if (!empty($reportaje['pdf_adjunto'])): 
                                    $ruta_pdf = 'admin/' . $reportaje['pdf_adjunto'];
                            ?>
                                <a target="_blank" href="<?php echo $ruta_pdf; ?>">
                                    <img src="<?php echo $ruta_foto; ?>" class="img-fluid w-100 radius-image" alt="Portada" />
                                    <br />Clic en la imagen para ver la infografía completa
                                </a>
                            <?php else: ?>
                                <img src="<?php echo $ruta_foto; ?>" class="img-fluid w-100 radius-image" alt="Portada" />
                            <?php endif; ?>
                        </div>

                        <!-- DESARROLLO DEL TEXTO -->
                        <div class="single-post-content">
                            
                            <?php if(!empty($reportaje['resumen_corto'])): ?>
                            <blockquote class="blockquote my-5">
                                <q class="mb-3 d-block"><?php echo htmlspecialchars($reportaje['resumen_corto']); ?></q>
                            </blockquote>
                            <?php endif; ?>

                            <div class="contenido-reportaje mb-4">
                                <?php 
                                    // Aquí está la magia para arreglar las fotitos rotas dentro de tu texto
                                    $contenido = $reportaje['desarrollo'];
                                    
                                    // Reemplazamos la ruta rota por la correcta que incluye "admin/"
                                    $contenido = str_replace('src="uploads/', 'src="admin/uploads/', $contenido);
                                    $contenido = str_replace('src="../uploads/', 'src="admin/uploads/', $contenido);
                                    
                                    echo $contenido; 
                                ?>
                            </div>

                        </div>

                        <nav class="post-navigation row mb-5 py-4">
                            <div class="post-prev col-md-6 pr-sm-5">
                                <span class="nav-title"><span class="fa fa-arrow-left mr-2"></span> <a href="reportajes.php">Volver a Reportajes </a></span>
                            </div>
                        </nav>

                    </div>
                </div>

                <!-- SIDEBAR -->
                <div class="col-lg-4 left-text-9 mt-lg-0 mt-5 pl-lg-4">
                    <div class="left-top-9 mt-5 pt-sm-3">
                        <h6 class="heading-small-text-9 mb-3">Últimas noticias</h6>
                        
                        <?php foreach($ultimos_reportajes as $ultimo): ?>
                        <a href="reportaje_detalle.php?id=<?php echo $ultimo['id']; ?>" class="p-post d-block py-2">
                            <h6 class="text-left-inner-9"><?php echo htmlspecialchars($ultimo['titulo']); ?></h6>
                            <span class="sub-inner-text-9"><?php echo formatearFecha($ultimo['fecha_publicacion']); ?></span>
                        </a>
                        <?php endforeach; ?>

                    </div>
                    
                    <div class="categories mt-5 pt-sm-3">
                        <h6 class="heading-small-text-9">Archivos</h6>
                        <ul>
                            <li><a href="#"> Julio 2026</a></li>
                            <li><a href="#"> Junio 2026</a></li>
                            <li><a href="#"> Mayo 2026</a></li>
                        </ul>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>