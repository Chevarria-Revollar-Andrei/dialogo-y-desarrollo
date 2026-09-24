<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: index.php");
    exit;
}

$modulo_actual = $_GET['modulo'] ?? 'dashboard';
$titulos_modulos = [
    'dashboard'  => 'Dashboard General',
    'reportajes' => 'Gestión de Reportajes',
    'noticias'   => 'Noticias Recientes',
    'podcasts'   => 'Biblioteca de Podcasts',
    'videos'     => 'Galería de Videos',
    'boletines'  => 'Boletines NTEP',
    'autores'    => 'Gestión de Autores',
    'usuarios'   => 'Usuarios del Sistema'
];
$nombre_modulo = $titulos_modulos[$modulo_actual] ?? 'Panel Administrativo';
?>
<!doctype html>
<html lang="es" translate="no">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Diálogo y Desarrollo — Panel Administrativo</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
:root {
    --bg: #f8fafc;
    --line: #e2e8f0;
    --text: #0f172a;
    --muted: #64748b;
    --accent-red: #e11d48;
}

* { box-sizing: border-box; }
body { margin: 0; background: var(--bg); font-family: 'Inter', system-ui, sans-serif; color: var(--text); font-size: 13px; }
.app { min-height: 100vh; }

/* ================= SIDEBAR CÁLIDO / ROJO SOBRIO ================= */
.sidebar {
    position: fixed;
    left: 0; top: 0; bottom: 0;
    width: 255px;
    background: linear-gradient(180deg, #1c1917 0%, #121011 100%);
    color: #a8a29e;
    z-index: 1040;
    display: flex;
    flex-direction: column;
    border-right: 1px solid rgba(255, 255, 255, 0.05);
}

.brand {
    height: 72px;
    padding: 16px 20px;
    display: flex;
    align-items: center;
    gap: 12px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.06);
}

.brand-icon {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: linear-gradient(135deg, #e11d48 0%, #9f1239 100%);
    display: grid;
    place-items: center;
    color: #fff;
    font-size: 18px;
    box-shadow: 0 4px 12px rgba(225, 29, 72, 0.25);
}

.brand strong { display: block; color: #f5f5f4; font-size: 13px; font-weight: 700; }
.brand small { display: block; color: #a8a29e; font-size: 10px; opacity: 0.8; }

.nav-area { padding: 16px 12px; overflow-y: auto; flex: 1; }
.nav-label { font-size: 9px; font-weight: 800; letter-spacing: 1.2px; color: #f43f5e; padding: 12px 12px 6px; opacity: 0.9; }

.nav-link {
    color: #d6d3d1;
    border-radius: 8px;
    margin: 2px 0;
    padding: 9px 12px;
    display: flex;
    align-items: center;
    gap: 11px;
    font-size: 12px;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s ease;
}

.nav-link i { width: 18px; text-align: center; font-size: 15px; opacity: 0.75; }
.nav-link:hover { background: rgba(255, 255, 255, 0.05); color: #fff; }
.nav-link.active {
    background: rgba(225, 29, 72, 0.12);
    color: #fff;
    font-weight: 600;
    box-shadow: inset 3px 0 0 #e11d48;
}
.nav-link.active i { opacity: 1; color: #fb7185; }

.sidebar-footer {
    margin: auto 12px 14px;
    padding: 10px 12px;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.05);
    border-radius: 8px;
    font-size: 10px;
    color: #78716c;
    display: flex;
    align-items: center;
    gap: 8px;
}

.dot { width: 7px; height: 7px; border-radius: 50%; background: #22c55e; }
.sidebar-footer b { margin-left: auto; color: #4ade80; }

/* ================= MAIN & HEADER ================= */
.main { margin-left: 255px; min-height: 100vh; }

.topbar {
    height: 68px;
    background: #ffffff;
    border-bottom: 1px solid var(--line);
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 32px;
    position: sticky;
    top: 0;
    z-index: 1000;
}

.module-indicator { display: flex; align-items: center; gap: 12px; }
.module-badge {
    background: #fff1f2;
    color: #e11d48;
    border: 1px solid #ffe4e6;
    padding: 5px 12px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 700;
}

.topbar-search {
    width: 300px;
    height: 38px;
    background: #f8fafc;
    border: 1px solid var(--line);
    border-radius: 8px;
    display: flex;
    align-items: center;
    padding: 0 12px;
    gap: 8px;
    color: #94a3b8;
}
.topbar-search input {
    border: 0;
    outline: 0;
    background: transparent;
    width: 100%;
    font-size: 11px;
    color: var(--text);
}

.top-right { display: flex; align-items: center; gap: 14px; }

.icon-btn {
    width: 36px; height: 36px;
    border: 1px solid var(--line);
    background: #fff;
    border-radius: 8px;
    position: relative;
    color: #64748b;
    display: grid;
    place-items: center;
    cursor: pointer;
}
.icon-btn:hover { background: #f8fafc; color: var(--text); }
.bell-dot {
    position: absolute;
    right: 7px; top: 7px;
    width: 6px; height: 6px;
    background: #e11d48;
    border-radius: 50%;
}

.user-profile { display: flex; align-items: center; gap: 10px; }
.user-avatar {
    width: 34px; height: 34px;
    border-radius: 50%;
    background: #ffe4e6;
    color: #be123c;
    display: grid;
    place-items: center;
    font-weight: 800;
    font-size: 11px;
}
.user-info strong { display: block; font-size: 11px; font-weight: 700; color: #0f172a; }
.user-info small { color: var(--muted); font-size: 10px; }

/* ESPACIADO DEL CONTENIDO PRINCIPAL */
.content {
    padding: 32px 32px 40px 32px;
    max-width: 1600px;
    margin: 0 auto;
}

.mobile-toggle { display: none; }

@media(max-width: 768px) {
    .sidebar { transform: translateX(-100%); transition: transform .25s ease; width: 255px; }
    .sidebar.open { transform: translateX(0); }
    .main { margin-left: 0; }
    .mobile-toggle { display: inline-grid; }
    .topbar-search { display: none; }
    .topbar { padding: 0 16px; }
    .content { padding: 20px 16px; }
}
</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
</head>

<body>
<div class="app">
<!-- BARRA LATERAL -->
<aside class="sidebar" id="sidebar">
    <div class="brand">
        <div class="brand-icon"><i class="bi bi-chat-square-text-fill"></i></div>
        <div>
            <strong>Diálogo y Desarrollo</strong>
            <small>Panel administrativo</small>
        </div>
    </div>
    
    <div class="nav-area">
        <div class="nav-label">PRINCIPAL</div>
        <a href="panel.php?modulo=dashboard" class="nav-link <?= (!isset($_GET['modulo']) || $_GET['modulo'] == 'dashboard') ? 'active' : '' ?>">
            <i class="bi bi-grid-1x2-fill"></i>Dashboard
        </a>
        
        <div class="nav-label">CONTENIDO</div>
        <a href="panel.php?modulo=reportajes" class="nav-link <?= (isset($_GET['modulo']) && $_GET['modulo'] == 'reportajes') ? 'active' : '' ?>">
            <i class="bi bi-newspaper"></i>Reportajes
        </a>
        <a href="panel.php?modulo=noticias" class="nav-link <?= (isset($_GET['modulo']) && $_GET['modulo'] == 'noticias') ? 'active' : '' ?>">
            <i class="bi bi-lightning-charge-fill"></i>Noticias recientes
        </a>
        <a href="panel.php?modulo=podcasts" class="nav-link <?= (isset($_GET['modulo']) && $_GET['modulo'] == 'podcasts') ? 'active' : '' ?>">
            <i class="bi bi-mic-fill"></i>Podcasts
        </a>
        <a href="panel.php?modulo=videos" class="nav-link <?= (isset($_GET['modulo']) && $_GET['modulo'] == 'videos') ? 'active' : '' ?>">
            <i class="bi bi-camera-video-fill"></i>Videos
        </a>
        <a href="panel.php?modulo=boletines" class="nav-link <?= (isset($_GET['modulo']) && $_GET['modulo'] == 'boletines') ? 'active' : '' ?>">
            <i class="bi bi-file-earmark-pdf-fill"></i>Boletines NTEP
        </a>
        
        <div class="nav-label">CONFIGURACIÓN</div>
        <a href="panel.php?modulo=autores" class="nav-link <?= (isset($_GET['modulo']) && $_GET['modulo'] == 'autores') ? 'active' : '' ?>">
            <i class="bi bi-people-fill"></i>Autores
        </a>
        <a href="panel.php?modulo=usuarios" class="nav-link <?= (isset($_GET['modulo']) && $_GET['modulo'] == 'usuarios') ? 'active' : '' ?>">
            <i class="bi bi-person-badge-fill"></i>Usuarios
        </a>
        
        <div class="nav-label">SESIÓN</div>
        <a href="actions/logout.php" class="nav-link text-danger"><i class="bi bi-box-arrow-right"></i>Cerrar Sesión</a>
    </div>
    
    <div class="sidebar-footer">
        <span class="dot"></span>
        <span>Sistema <b>PHP/MySQL</b></span>
    </div>
</aside>

<div class="main">
<!-- HEADER -->
<header class="topbar">
    <div class="module-indicator">
        <button class="icon-btn mobile-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')">
            <i class="bi bi-list fs-5"></i>
        </button>
        <span class="module-badge">
            <i class="bi bi-folder2-open me-1"></i> <?= htmlspecialchars($nombre_modulo) ?>
        </span>
    </div>

    <div class="topbar-search">
        <i class="bi bi-search"></i>
        <input type="text" placeholder="Buscar en el panel..." aria-label="Buscar">
        <span class="badge bg-white text-muted border px-1" style="font-size: 9px;">Ctrl+K</span>
    </div>

    <div class="top-right">
        <button class="icon-btn" title="Notificaciones" onclick="alert('No tienes nuevas notificaciones')">
            <i class="bi bi-bell"></i>
            <span class="bell-dot"></span>
        </button>
        
        <div class="user-profile">
            <div class="user-avatar">
                <?= strtoupper(substr($_SESSION['nombre_completo'] ?? 'AD', 0, 2)) ?>
            </div>
            <div class="user-info">
                <strong><?= htmlspecialchars($_SESSION['nombre_completo'] ?? 'Administrador') ?></strong>
                <small><?= htmlspecialchars($_SESSION['rol'] ?? 'SuperAdmin') ?></small>
            </div>
        </div>
    </div>
</header>

<!-- CONTENEDOR CON MARGEN CORRECTO -->
<div class="content"></div>