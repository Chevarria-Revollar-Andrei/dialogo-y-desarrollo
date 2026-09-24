<?php
// actions/procesar_login.php
session_start(); // ¡Ojo! Faltaba iniciar sesión en tu código original
require_once __DIR__ . '/../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // 1. Usamos la nueva tabla 'usuarios'
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email LIMIT 1");
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // MODO DEBUG: Si el usuario no existe, se detiene y avisa
    if (!$usuario) {
        die("ERROR DEBUG: No se encontró este correo en la base de datos: " . htmlspecialchars($email));
    }

    // MODO DEBUG: Si la contraseña no cuadra, se detiene y avisa
    if (!password_verify($password, $usuario['password_hash'])) {
        die("ERROR DEBUG: El correo sí existe, pero la contraseña no coincide. Revisa el Hash en phpMyAdmin.");
    }

    // Si todo está perfecto, guarda la sesión y avanza
    // Adaptamos las columnas a tu nueva BD (id, nombres, ap_paterno)
    $_SESSION['id_usuario'] = $usuario['id']; // Lo llamamos id_usuario en sesión para no romper el resto de tu app
    $_SESSION['nombre_completo'] = $usuario['nombres'] . ' ' . $usuario['ap_paterno'];
    $_SESSION['rol'] = $usuario['rol'];
    $_SESSION['email'] = $usuario['email'];
    
    header("Location: ../panel.php");
    exit;
} else {
    header("Location: ../index.php");
    exit;
}
?>