<?php 
include 'includes/header.php'; 
?>

<!-- Estilos específicos para la sección de Alianzas -->
<style>
.alianza-card {
    background: #ffffff;
    border: 1px solid #eef0f2;
    border-radius: 8px;
    padding: 35px 25px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 4px 12px rgba(0,0,0,0.03);
}
.alianza-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
}
.alianza-icon-box {
    width: 70px;
    height: 70px;
    background: rgba(230, 0, 0, 0.08);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
}
.badge-proximamente {
    background-color: #e60000;
    color: #ffffff;
    font-size: 0.75rem;
    font-weight: 600;
    padding: 6px 14px;
    border-radius: 20px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.banner-alianzas {
    background: #f9f9f9;
    border-left: 4px solid #e60000;
    padding: 25px 30px;
    border-radius: 0 8px 8px 0;
    margin-bottom: 40px;
}
</style>

<!-- Breadcrumb -->
<section class="breadcrumb-area py-sm-5 py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb-contents">
                    <h2 class="title-big">Alianzas Estratégicas</h2>
                    <div class="breadcrumb">
                        <ul>
                            <li><a href="index.php">Inicio</a></li>
                            <li class="active">Alianzas</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contenido Principal -->
<div class="grids-block-5 py-5">
    <section class="py-lg-4 py-md-3">
        <div class="container">
            
            <!-- Banner Informativo de Actualización -->
            <div class="banner-alianzas">
                <h3 style="font-weight: 600; color: #222;" class="mb-2">Sumando esfuerzos por el diálogo</h3>
                <p class="mb-0" style="color: #555; font-size: 1.05rem; line-height: 1.6;">
                    En <strong>Diálogo y Desarrollo Perú</strong> trabajamos en la consolidación de alianzas estratégicas con instituciones, la academia y organizaciones de la sociedad civil para visibilizar el periodismo constructivo. 
                    <em>Esta sección se encuentra en actualización y próximamente daremos a conocer a nuestras entidades aliadas.</em>
                </p>
            </div>

            <!-- Grid de Tarjetas Informativas / Placeholders -->
            <div class="row">
                
                <!-- Sector Académico -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="alianza-card text-center">
                        <div>
                            <div class="alianza-icon-box mx-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#e60000" viewBox="0 0 24 24">
                                    <path d="M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82zM12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/>
                                </svg>
                            </div>
                            <h4 style="font-weight: 700; font-size: 1.2rem; color: #222;" class="mb-2">Sector Académico</h4>
                            <p style="color: #666; font-size: 0.95rem; line-height: 1.5;" class="mb-4">
                                Convenios con universidades e institutos de investigación para el análisis continuo de la coyuntura y el diálogo social.
                            </p>
                        </div>
                        <span class="badge-proximamente">Próximamente</span>
                    </div>
                </div>

                <!-- Sociedad Civil -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="alianza-card text-center">
                        <div>
                            <div class="alianza-icon-box mx-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#e60000" viewBox="0 0 24 24">
                                    <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                                </svg>
                            </div>
                            <h4 style="font-weight: 700; font-size: 1.2rem; color: #222;" class="mb-2">Sociedad Civil</h4>
                            <p style="color: #666; font-size: 0.95rem; line-height: 1.5;" class="mb-4">
                                Coordinación con colectivos cívicos y plataformas comunitarias enfocadas en la resolución constructiva de conflictos.
                            </p>
                        </div>
                        <span class="badge-proximamente">Próximamente</span>
                    </div>
                </div>

                <!-- Red de Medios -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="alianza-card text-center">
                        <div>
                            <div class="alianza-icon-box mx-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#e60000" viewBox="0 0 24 24">
                                    <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-3 5h-2V7h2v2zm-4 0h-2V7h2v2zm-4 0H7V7h2v2zm8 4h-2v-2h2v2zm-4 0h-2v-2h2v2zm-4 0H7v-2h2v2zm8 4h-2v-2h2v2zm-4 0h-2v-2h2v2zm-4 0H7v-2h2v2z"/>
                                </svg>
                            </div>
                            <h4 style="font-weight: 700; font-size: 1.2rem; color: #222;" class="mb-2">Medios Colaborativos</h4>
                            <p style="color: #666; font-size: 0.95rem; line-height: 1.5;" class="mb-4">
                                Trabajo articulado con medios regionales e independientes para la difusión plural de investigaciones periodísticas.
                            </p>
                        </div>
                        <span class="badge-proximamente">Próximamente</span>
                    </div>
                </div>

            </div>

            <!-- Llamado a la Acción para Contacto -->
            <div class="row mt-5">
                <div class="col-12 text-center py-4" style="background: #fafafa; border-radius: 8px; border: 1px solid #eee;">
                    <h4 style="font-weight: 600; color: #222;" class="mb-2">¿Te interesa formar una alianza con nosotros?</h4>
                    <p class="mb-3" style="color: #666;">Si representas a una institución y deseas impulsar proyectos de diálogo o cobertura en conjunto, contáctanos.</p>
                    <a href="contacto.php" class="btn btn-style btn-primary">Ponte en contacto</a>
                </div>
            </div>

        </div>
    </section>
</div>

<?php include 'includes/footer.php'; ?>