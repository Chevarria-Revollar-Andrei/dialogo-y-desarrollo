<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Detección automática del entorno
$is_local = ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_NAME'] === '127.0.0.1');

if ($is_local) {
    // Configuración Entorno Local (XAMPP)
    $host   = 'localhost';
    $dbname = 'revista_digital';
    $user   = 'root';
    $pass   = '';
} else {
    // Configuración Entorno Producción (InfinityFree)
    // IMPORTANTE: Estos datos se llenan únicamente en el servidor remoto o en una plantilla segura.
    $host   = 'sqlXXX.infinityfree.com';        // Servidor MySQL de InfinityFree
    $dbname = 'if0_XXXXXXXX_revista_digital';  // Nombre de la BD en InfinityFree
    $user   = 'if0_XXXXXXXX';                  // Usuario de la BD en InfinityFree
    $pass   = 'TU_CONTRASEÑA_INFINITYFREE';    // Contraseña de la BD en InfinityFree
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error crítico de conexión: " . $e->getMessage());
}
?>