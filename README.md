# Diálogo y Desarrollo — Revista Digital

[![PHP](https://img.shields.io/badge/PHP-7.4%20%7C%208.x-777BB4?style=flat-square&logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?style=flat-square&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Hosting](https://img.shields.io/badge/Hosting-InfinityFree-green?style=flat-square)](https://www.infinityfree.com/)

## 📝 Descripción

**Diálogo y Desarrollo** es una plataforma web tipo revista digital orientada a la difusión de contenidos informativos y académicos. Ofrece una interfaz pública para la lectura de reportajes, visualización de noticias, boletines descargables en PDF, podcasts, alianzas institucionales y vídeos.

Cuenta con un módulo administrativo privado (`admin/`) protegido con autenticación de usuarios que permite realizar operaciones **CRUD** (Crear, Leer, Actualizar, Eliminar) sobre todas las entidades de la aplicación.

---

## 🛠️ Tecnologías Utilizadas

* **Lenguaje Backend:** PHP (PDO con preparado de consultas)
* **Base de Datos:** MySQL / MariaDB
* **Frontend:** HTML5, CSS3, JavaScript, jQuery, OwlCarousel, EasyResponsiveTabs
* **Servidor Local:** XAMPP (Apache + MySQL)
* **Control de Versiones:** Git & GitHub
* **Hosting en Producción:** InfinityFree

---

## 📂 Estructura del Proyecto

```text
dialogoydesarrollo/
│
├── admin/                         # Panel de administración de la plataforma
│   ├── actions/                   # Procesadores backend de formularios
│   │   ├── logout.php             # Cierre de sesión de usuario
│   │   ├── procesar_autor.php     # CRUD de autores
│   │   ├── procesar_boletin.php   # CRUD de boletines y carga de PDFs/fotos
│   │   ├── procesar_login.php     # Autenticación al panel
│   │   ├── procesar_noticia.php   # CRUD de noticias
│   │   ├── procesar_podcast.php   # CRUD de podcasts
│   │   ├── procesar_reportaje.php # CRUD de reportajes y adjuntos
│   │   ├── procesar_usuario.php   # Gestión de usuarios del sistema
│   │   └── procesar_video.php     # CRUD de vídeos
│   │
│   ├── assets/                    # Hojas de estilo y scripts del área admin
│   ├── config/
│   │   └── conexion.php           # Configuración dinámica PDO (Local / Producción)
│   │
│   ├── includes/                  # Componentes repetitivos del panel
│   │   ├── footer.php
│   │   └── header.php
│   │
│   ├── modules/                   # Módulos del CRUD administrativo
│   │   ├── autores.php
│   │   ├── boletines.php
│   │   ├── dashboard.php
│   │   ├── noticias.php
│   │   ├── podcasts.php
│   │   ├── reportajes.php
│   │   ├── usuarios.php
│   │   └── videos.php
│   │
│   ├── uploads/                   # Archivos cargados dinámicamente
│   │   ├── boletines/             # Portadas y documentos PDF
│   │   ├── noticias/              # Imágenes de noticias
│   │   └── reportajes/            # Fotos y archivos PDF adjuntos
│   │
│   ├── index.php                  # Pantalla de Login al Panel
│   └── panel.php                  # Dashboard administrativo
│
├── assets/                        # Recursos estáticos del sitio público
│   ├── css/
│   │   └── style-starter.css
│   ├── images/
│   │   ├── bannerimg.jpg
│   │   └── logo.png
│   └── js/
│       ├── easyResponsiveTabs.js
│       ├── owl.carousel.js
│       └── theme-chenge.js
│
├── includes/                      # Header y Footer de la vista pública
│   ├── footer.php
│   └── header.php
│
├── .gitignore                     # Exclusión de archivos sensibles y temporales
├── alianzas.php                   # Sección de alianzas institucionales
├── boletin.php                    # Publicaciones y boletines informativos
├── contacto.php                   # Formulario de contacto
├── database.sql                   # Estructura y datos de la Base de Datos
├── index.php                      # Página principal (Portada)
├── podcast.php                    # Reproductor y listado de podcasts
├── README.md                      # Documentación del proyecto
├── reportaje_detalle.php          # Vista de lectura de reportaje
├── reportajes.php                 # Catálogo de reportajes
└── sobre-dd.php                   # Información institucional sobre la revista
```

---

## 💻 Instalación Local (XAMPP)

1. **Clonar el repositorio:**
   Ubícate en el directorio `htdocs` de XAMPP y ejecuta:
   ```bash
   git clone https://github.com/Chevarria-Revollar-Andrei/dialogo-y-desarrollo
   ```

2. **Iniciar servicios:**
   Abre el panel de **XAMPP Control Panel** e inicia **Apache** y **MySQL**.

3. **Importar la Base de Datos:**
   * Entra a `http://localhost/phpmyadmin`.
   * Crea una base de datos nombrada `revista_digital`.
   * Selecciona la base de datos, entra en la pestaña **Importar** y selecciona el archivo `database.sql` de la raíz del proyecto.

4. **Acceder a la aplicación:**
   * **Sitio Web Público:** `http://localhost/dialogoydesarrollo/index.php`
   * **Panel de Administración:** `http://localhost/dialogoydesarrollo/admin/index.php`

---

## 🗄️ Migración de Base de Datos

La migración a producción se realiza de forma independiente al control de versiones de Git:

1. Exportación desde phpMyAdmin local a formato `database.sql`.
2. Creación de la base de datos MySQL mediante el panel vPanel de **InfinityFree**.
3. Importación del script `.sql` en el phpMyAdmin remoto de InfinityFree.

---

## 🚀 Despliegue en Producción (InfinityFree)

1. **Configuración de Entorno:**
   El archivo `admin/config/conexion.php` cuenta con detección automática de entorno (`$_SERVER['SERVER_NAME']`):
   * En entorno local (`localhost` / `127.0.0.1`), conecta a XAMPP.
   * En entorno remoto, utiliza las credenciales asignadas por InfinityFree.

2. **Buenas Prácticas de Seguridad:**
   * Las credenciales reales de la base de datos en producción no se almacenan en el repositorio público de GitHub.
   * La configuración definitiva de la conexión remota se realiza directamente en el archivo ubicado en el servidor web.

3. **Carga de Archivos:**
   El código completo del proyecto se transfiere a la carpeta raíz pública (`htdocs/`) del servidor hosting.

---

## 🔗 URL Pública del Proyecto

* 🚀 **Sitio en vivo:** `http://dialogoydesarrollochevarria.fwh.is` *(Actualizar con la URL final)*

---

## 📸 Evidencias del Despliegue

| N° | Evidencia Requerida | Captura / Referencia |
| :---: | :--- | :--- |
| **1** | Repositorio en GitHub | `assets/images/evidencias/github.png` |
| **2** | README.md Profesional | `assets/images/evidencias/readme.png` |
| **3** | Panel del Hosting InfinityFree | `assets/images/evidencias/hosting.png` |
| **4** | Base de Datos en phpMyAdmin Remoto | `assets/images/evidencias/phpmyadmin.png` |
| **5** | Aplicación Web Funcionando en Vivo | `assets/images/evidencias/sitio_web.png` |
| **6** | Panel Administrativo (`admin/`) en Vivo | `assets/images/evidencias/admin_panel.png` |
| **7** | Funcionalidad Dinámica MySQL (CRUD) | `assets/images/evidencias/crud_demo.png` |

---
*Práctica desarrollada para la asignatura Plataformas para el Desarrollo de Aplicaciones — Escuela Profesional de Ingeniería de Sistemas (Semestre 2026-II).*