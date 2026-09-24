<?php
// actions/procesar_noticia.php
session_start();
require_once '../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    
    // Ruta donde se guardarán las fotos
    $dir_fotos = '../uploads/noticias/';
    if (!is_dir($dir_fotos)) {
        mkdir($dir_fotos, 0777, true);
    }

    try {
        if ($accion === 'guardar') {
            $titulo = trim($_POST['titulo']);
            $fecha_publicacion = $_POST['fecha_publicacion']; // Ahora es solo DATE
            $link_externo = trim($_POST['link_externo']);
            $usuario_id = $_SESSION['id_usuario'] ?? 1;

            $foto_ruta = null;
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $nombre_foto = time() . '_' . basename($_FILES['foto']['name']);
                if (move_uploaded_file($_FILES['foto']['tmp_name'], $dir_fotos . $nombre_foto)) {
                    $foto_ruta = 'uploads/noticias/' . $nombre_foto;
                }
            }

            // NUEVA ESTRUCTURA
            $stmt = $pdo->prepare("INSERT INTO noticias (titulo, foto, link_externo, fecha_publicacion, usuario_id) VALUES (:titulo, :foto, :link_externo, :fecha_publicacion, :usuario_id)");
            $stmt->execute([
                ':titulo' => $titulo,
                ':foto' => $foto_ruta,
                ':link_externo' => $link_externo,
                ':fecha_publicacion' => $fecha_publicacion,
                ':usuario_id' => $usuario_id
            ]);

            $_SESSION['mensaje_exito'] = "Noticia guardada con éxito.";

        } elseif ($accion === 'editar') {
            $id = $_POST['id'];
            $titulo = trim($_POST['titulo']);
            $fecha_publicacion = $_POST['fecha_publicacion'];
            $link_externo = trim($_POST['link_externo']);

            // NUEVA ESTRUCTURA
            $sql = "UPDATE noticias SET titulo = :titulo, fecha_publicacion = :fecha_publicacion, link_externo = :link_externo WHERE id = :id";
            $parametros = [
                ':titulo' => $titulo,
                ':fecha_publicacion' => $fecha_publicacion,
                ':link_externo' => $link_externo,
                ':id' => $id
            ];

            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $nombre_foto = time() . '_edit_' . basename($_FILES['foto']['name']);
                if (move_uploaded_file($_FILES['foto']['tmp_name'], $dir_fotos . $nombre_foto)) {
                    $sql = str_replace("WHERE", ", foto = :foto WHERE", $sql);
                    $parametros[':foto'] = 'uploads/noticias/' . $nombre_foto;
                }
            }

            $stmt = $pdo->prepare($sql);
            $stmt->execute($parametros);

            $_SESSION['mensaje_exito'] = "Noticia actualizada correctamente.";

        } elseif ($accion === 'eliminar') {
            $id = $_POST['id'];

            // Eliminar archivo físico de la foto si existe
            $stmt = $pdo->prepare("SELECT foto FROM noticias WHERE id = ?");
            $stmt->execute([$id]);
            $noti = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($noti && !empty($noti['foto']) && file_exists('../' . $noti['foto'])) {
                unlink('../' . $noti['foto']);
            }

            // Eliminar registro de la base de datos
            $stmt_del = $pdo->prepare("DELETE FROM noticias WHERE id = ?");
            $stmt_del->execute([$id]);

            $_SESSION['mensaje_exito'] = "Noticia eliminada correctamente.";
        }

    } catch (Exception $e) {
        $_SESSION['mensaje_error'] = "Ocurrió un error: " . $e->getMessage();
    }

    header("Location: ../panel.php?modulo=noticias");
    exit;
}