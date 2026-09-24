<?php 
include 'includes/header.php'; 
?>

<!-- Estilos específicos para la sección de Contacto -->
<style>
.contact-hero-box {
    background: #f9f9f9;
    border-left: 4px solid #e60000;
    padding: 30px 25px;
    border-radius: 0 8px 8px 0;
    margin-bottom: 40px;
}
.contact-card {
    background: #ffffff;
    border: 1px solid #eef0f2;
    border-radius: 8px;
    padding: 30px 20px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    height: 100%;
    box-shadow: 0 4px 12px rgba(0,0,0,0.03);
}
.contact-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
}
.contact-icon-box {
    width: 60px;
    height: 60px;
    background: rgba(230, 0, 0, 0.08);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 18px;
}
.form-contact-wrapper {
    background: #ffffff;
    border: 1px solid #eef0f2;
    border-radius: 8px;
    padding: 35px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.03);
}
.form-contact .form-control {
    border: 1px solid #e0e0e0;
    padding: 12px 15px;
    border-radius: 6px;
    font-size: 0.95rem;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}
.form-contact .form-control:focus {
    border-color: #e60000;
    box-shadow: 0 0 0 3px rgba(230, 0, 0, 0.1);
    outline: none;
}
.map-container {
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid #eef0f2;
    box-shadow: 0 4px 12px rgba(0,0,0,0.03);
    height: 100%;
    min-height: 380px;
}
</style>

<!-- Breadcrumb -->
<section class="breadcrumb-area py-sm-5 py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb-contents">
                    <h2 class="title-big">Contacto</h2>
                    <div class="breadcrumb">
                        <ul>
                            <li><a href="index.php">Inicio</a></li>
                            <li class="active">Contacto</li>
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
            <div class="contact-hero-box">
                <h3 style="font-weight: 700; color: #222;" class="mb-2">Ponte en contacto con nuestro equipo</h3>
                <p class="mb-0" style="color: #555; font-size: 1.05rem; line-height: 1.6;">
                    ¿Tienes alguna consulta, sugerencia de cobertura o propuesta de alianza? Escríbenos y nos pondremos en contacto contigo a la brevedad.
                </p>
            </div>

            <!-- Tarjetas de Información Rápida -->
            <div class="row mb-5">
                
                <!-- Correo Electrónico -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="contact-card text-center">
                        <div class="contact-icon-box mx-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="#e60000" viewBox="0 0 24 24">
                                <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                            </svg>
                        </div>
                        <h5 style="font-weight: 700; color: #222;" class="mb-2">Correo Electrónico</h5>
                        <p style="color: #666; font-size: 0.95rem;" class="mb-0">
                            <a href="mailto:info@dialogoydesarrollo.com.pe" style="color: inherit; text-decoration: none;">
                                info@dialogoydesarrollo.com.pe
                            </a>
                        </p>
                    </div>
                </div>

                <!-- Cobertura -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="contact-card text-center">
                        <div class="contact-icon-box mx-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="#e60000" viewBox="0 0 24 24">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                            </svg>
                        </div>
                        <h5 style="font-weight: 700; color: #222;" class="mb-2">Ubicación y Cobertura</h5>
                        <p style="color: #666; font-size: 0.95rem;" class="mb-0">
                            Lima y Regiones del Perú
                        </p>
                    </div>
                </div>

                <!-- Horario de Atención -->
                <div class="col-lg-4 col-md-12 mb-4">
                    <div class="contact-card text-center">
                        <div class="contact-icon-box mx-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="#e60000" viewBox="0 0 24 24">
                                <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                            </svg>
                        </div>
                        <h5 style="font-weight: 700; color: #222;" class="mb-2">Atención Editorial</h5>
                        <p style="color: #666; font-size: 0.95rem;" class="mb-0">
                            Lunes a Viernes: 9:00 am - 6:00 pm
                        </p>
                    </div>
                </div>

            </div>

            <!-- Formulario de Contacto y Mapa/Información Lateral -->
            <div class="row align-items-stretch">
                
                <!-- Formulario -->
                <div class="col-lg-7 mb-lg-0 mb-4">
                    <div class="form-contact-wrapper">
                        <h4 style="font-weight: 700; color: #222;" class="mb-4">Envíanos un mensaje</h4>
                        
                        <form action="#" method="post" class="form-contact">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nombre" style="font-weight: 600; font-size: 0.9rem; color: #444;" class="mb-1">Nombre completo *</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Tu nombre" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" style="font-weight: 600; font-size: 0.9rem; color: #444;" class="mb-1">Correo electrónico *</label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="ejemplo@correo.com" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="asunto" style="font-weight: 600; font-size: 0.9rem; color: #444;" class="mb-1">Asunto *</label>
                                <input type="text" class="form-control" id="asunto" name="asunto" placeholder="Motivo del mensaje" required>
                            </div>

                            <div class="mb-4">
                                <label for="mensaje" style="font-weight: 600; font-size: 0.9rem; color: #444;" class="mb-1">Mensaje *</label>
                                <textarea class="form-control" id="mensaje" name="mensaje" rows="5" placeholder="Escribe tu mensaje aquí..." required></textarea>
                            </div>

                            <button type="submit" class="btn btn-style btn-primary w-100">Enviar Mensaje</button>
                        </form>
                    </div>
                </div>

                <!-- Mapa / Ubicación -->
                <div class="col-lg-5">
                    <div class="map-container">
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d249743.72044566212!2d-77.12786364024855!3d-12.026267600858177!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x9105c5f619ee3ec7%3A0x14206707a9db2b40!2sLima%2C%20Per%C3%BA!5e0!3m2!1ses!2spe!4v1700000000000!5m2!1ses!2spe" 
                            width="100%" 
                            height="100%" 
                            style="border:0; min-height: 380px;" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                </div>

            </div>

        </div>
    </section>
</div>

<?php include 'includes/footer.php'; ?>