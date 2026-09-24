<?php
// actions/procesar_video.php
session_start();
require_once '../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $accion = $_POST['accion'] ?? '';

    try {
        if ($accion === 'guardar') {
            // Guardar nuevo video
            $titulo = trim($_POST['titulo']);
            // Se asume que el campo en DB es tipo DATE o DATETIME
            $fecha_publicacion = $_POST['fecha_publicacion']; 
            $url_embed = trim($_POST['url_embed']);
            
            // Asignamos usuario de la sesión o 1 por defecto
            $usuario_id = $_SESSION['usuario_id'] ?? $_SESSION['id_usuario'] ?? 1;

            $stmt = $pdo->prepare("INSERT INTO videos (titulo, fecha_publicacion, url_embed, usuario_id) VALUES (:titulo, :fecha_publicacion, :url_embed, :usuario_id)");
            $stmt->execute([
                ':titulo' => $titulo,
                ':fecha_publicacion' => $fecha_publicacion,
                ':url_embed' => $url_embed,
                ':usuario_id' => $usuario_id
            ]);
            $_SESSION['mensaje_exito'] = "Video guardado con éxito.";

        } elseif ($accion === 'editar') {
            // Editar video existente
            $id = $_POST['id'];
            $titulo = trim($_POST['titulo']);
            $fecha_publicacion = $_POST['fecha_publicacion'];
            $url_embed = trim($_POST['url_embed']);

            $stmt = $pdo->prepare("UPDATE videos SET titulo = :titulo, fecha_publicacion = :fecha_publicacion, url_embed = :url_embed WHERE id = :id");
            $stmt->execute([
                ':titulo' => $titulo,
                ':fecha_publicacion' => $fecha_publicacion,
                ':url_embed' => $url_embed,
                ':id' => $id
            ]);
            $_SESSION['mensaje_exito'] = "Video actualizado correctamente.";

        } elseif ($accion === 'eliminar') {
            // Eliminar video
            $id = $_POST['id'];
            
            $stmt = $pdo->prepare("DELETE FROM videos WHERE id = ?");
            $stmt->execute([$id]);
            $_SESSION['mensaje_exito'] = "Video eliminado correctamente.";
        }

    } catch (Exception $e) {
        $_SESSION['mensaje_error'] = "Error en la operación: " . $e->getMessage();
    }

    // Redireccionar de vuelta al módulo de videos
    header("Location: ../panel.php?modulo=videos");
    exit;
}