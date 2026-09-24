<?php 
include 'includes/header.php'; 
?>

<!-- Estilos específicos para la sección Sobre Nosotros -->
<style>
.about-hero-box {
    background: #f9f9f9;
    border-left: 4px solid #e60000;
    padding: 35px 30px;
    border-radius: 0 8px 8px 0;
    margin-bottom: 40px;
}
.about-card {
    background: #ffffff;
    border: 1px solid #eef0f2;
    border-radius: 8px;
    padding: 35px 25px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    height: 100%;
    box-shadow: 0 4px 12px rgba(0,0,0,0.03);
}
.about-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
}
.about-icon-box {
    width: 65px;
    height: 65px;
    background: rgba(230, 0, 0, 0.08);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
}
.feature-pillar {
    display: flex;
    align-items: flex-start;
    gap: 18px;
    margin-bottom: 25px;
}
.feature-pillar-icon {
    min-width: 48px;
    height: 48px;
    background: #e60000;
    color: #ffffff;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>

<!-- Breadcrumb -->
<section class="breadcrumb-area py-sm-5 py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb-contents">
                    <h2 class="title-big">Sobre Nosotros</h2>
                    <div class="breadcrumb">
                        <ul>
                            <li><a href="index.php">Inicio</a></li>
                            <li class="active">Quiénes Somos</li>
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
            
            <!-- Banner Introductorio -->
            <div class="about-hero-box">
                <h3 style="font-weight: 700; color: #222;" class="mb-3">Periodismo independiente al servicio del encuentro y el desarrollo</h3>
                <p class="mb-0" style="color: #555; font-size: 1.08rem; line-height: 1.7;">
                    <strong>Diálogo y Desarrollo Perú</strong> es un espacio de comunicación e investigación enfocado en visibilizar las iniciativas de concertación, prevención de conflictos y proyectos constructivos que impulsan el crecimiento sostenible en nuestro país. Creemos en un periodismo ético, analítico y plural.
                </p>
            </div>

            <!-- Tríptico: Misión, Visión y Compromiso -->
            <div class="row mb-5">
                
                <!-- Misión -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="about-card">
                        <div class="about-icon-box">
                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="#e60000" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
                            </svg>
                        </div>
                        <h4 style="font-weight: 700; font-size: 1.25rem; color: #222;" class="mb-2">Nuestra Misión</h4>
                        <p style="color: #666; font-size: 0.98rem; line-height: 1.6;" class="mb-0">
                            Informar con rigor e independencia, dando voz a los procesos de diálogo que contribuyen al entendimiento nacional y a la solución de los grandes retos del Perú.
                        </p>
                    </div>
                </div>

                <!-- Visión -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="about-card">
                        <div class="about-icon-box">
                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="#e60000" viewBox="0 0 24 24">
                                <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                            </svg>
                        </div>
                        <h4 style="font-weight: 700; font-size: 1.25rem; color: #222;" class="mb-2">Nuestra Visión</h4>
                        <p style="color: #666; font-size: 0.98rem; line-height: 1.6;" class="mb-0">
                            Ser la plataforma digital de referencia en periodismo constructivo, reconocida por su neutralidad, calidad investigativa e impacto social.
                        </p>
                    </div>
                </div>

                <!-- Compromiso -->
                <div class="col-lg-4 col-md-12 mb-4">
                    <div class="about-card">
                        <div class="about-icon-box">
                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="#e60000" viewBox="0 0 24 24">
                                <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z"/>
                            </svg>
                        </div>
                        <h4 style="font-weight: 700; font-size: 1.25rem; color: #222;" class="mb-2">Nuestro Compromiso</h4>
                        <p style="color: #666; font-size: 0.98rem; line-height: 1.6;" class="mb-0">
                            Mantener un espacio abierto a todas las visiones, promoviendo el debate alturado, el respeto a la verdad y la pluralidad democrática.
                        </p>
                    </div>
                </div>

            </div>

            <!-- Pilares del Periodismo Constructivo -->
            <div class="row pt-4 align-items-center">
                <div class="col-lg-5 mb-lg-0 mb-4">
                    <h3 style="font-weight: 700; color: #222;" class="mb-3">¿Qué caracteriza a nuestro trabajo?</h3>
                    <p style="color: #666; line-height: 1.7;" class="mb-4">
                        Frente al periodismo centrado únicamente en la confrontación, aportamos un enfoque donde las soluciones, los consensos y la voz de las regiones son los verdaderos protagonistas.
                    </p>
                    <a href="reportajes.php" class="btn btn-style btn-primary">Explorar Reportajes</a>
                </div>

                <div class="col-lg-7">
                    <div class="ps-lg-4">
                        
                        <div class="feature-pillar">
                            <div class="feature-pillar-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                                </svg>
                            </div>
                            <div>
                                <h5 style="font-weight: 700; color: #222;" class="mb-1">Investigación y Veracidad</h5>
                                <p style="color: #666; font-size: 0.95rem; margin: 0;">Contenidos contrastados con fuentes de primera mano, alejados del sensacionalismo.</p>
                            </div>
                        </div>

                        <div class="feature-pillar">
                            <div class="feature-pillar-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                                </svg>
                            </div>
                            <div>
                                <h5 style="font-weight: 700; color: #222;" class="mb-1">Mirada Descentralizada</h5>
                                <p style="color: #666; font-size: 0.95rem; margin: 0;">Atención prioritaria a las realidades, voces y propuestas provenientes de las regiones del Perú.</p>
                            </div>
                        </div>

                        <div class="feature-pillar">
                            <div class="feature-pillar-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M21 6h-2v9H6v2c0 .55.45 1 1 1h11l4 4V7c0-.55-.45-1-1-1zm-4-4H3c-.55 0-1 .45-1 1v14l4-4h11c.55 0 1-.45 1-1V3c0-.55-.45-1-1-1z"/>
                                </svg>
                            </div>
                            <div>
                                <h5 style="font-weight: 700; color: #222;" class="mb-1">Cultura de Diálogo</h5>
                                <p style="color: #666; font-size: 0.95rem; margin: 0;">Fomento del debate informado que permita tender puentes entre la ciudadanía, la sociedad civil y las autoridades.</p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Seccion CTA Contacto -->
            <div class="row mt-5">
                <div class="col-12 text-center py-4" style="background: #fafafa; border-radius: 8px; border: 1px solid #eee;">
                    <h4 style="font-weight: 600; color: #222;" class="mb-2">¿Quieres saber más sobre nuestro trabajo?</h4>
                    <p class="mb-3" style="color: #666;">Si tienes sugerencias, comentarios o deseas ponerte en contacto con nuestro equipo editorial, escríbenos.</p>
                    <a href="contacto.php" class="btn btn-style btn-primary">Contáctanos aquí</a>
                </div>
            </div>

        </div>
    </section>
</div>

<?php include 'includes/footer.php'; ?>