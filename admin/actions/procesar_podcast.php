<?php
// actions/procesar_podcast.php
session_start();
require_once '../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    try {
        if ($accion === 'guardar') {
            $titulo = trim($_POST['titulo']);
            $fecha_publicacion = $_POST['fecha_publicacion'];
            $url_embed = trim($_POST['url_embed']);
            
            // Toma el ID de la sesión actual (o 1 por defecto)
            $usuario_id = $_SESSION['usuario_id'] ?? $_SESSION['id_usuario'] ?? 1;

            $stmt = $pdo->prepare("INSERT INTO podcasts (titulo, url_embed, fecha_publicacion, usuario_id) VALUES (:titulo, :url_embed, :fecha_publicacion, :usuario_id)");
            $stmt->execute([
                ':titulo' => $titulo,
                ':url_embed' => $url_embed,
                ':fecha_publicacion' => $fecha_publicacion,
                ':usuario_id' => $usuario_id
            ]);
            $_SESSION['mensaje_exito'] = "Podcast guardado con éxito.";

        } elseif ($accion === 'editar') {
            $id = $_POST['id'];
            $titulo = trim($_POST['titulo']);
            $fecha_publicacion = $_POST['fecha_publicacion'];
            $url_embed = trim($_POST['url_embed']);

            $stmt = $pdo->prepare("UPDATE podcasts SET titulo = :titulo, fecha_publicacion = :fecha_publicacion, url_embed = :url_embed WHERE id = :id");
            $stmt->execute([
                ':titulo' => $titulo,
                ':fecha_publicacion' => $fecha_publicacion,
                ':url_embed' => $url_embed,
                ':id' => $id
            ]);
            $_SESSION['mensaje_exito'] = "Podcast actualizado correctamente.";

        } elseif ($accion === 'eliminar') {
            $id = $_POST['id'];
            
            $stmt = $pdo->prepare("DELETE FROM podcasts WHERE id = ?");
            $stmt->execute([$id]);
            $_SESSION['mensaje_exito'] = "Podcast eliminado correctamente.";
        }

    } catch (Exception $e) {
        $_SESSION['mensaje_error'] = "Error en la operación: " . $e->getMessage();
    }

    header("Location: ../panel.php?modulo=podcasts");
    exit;
}