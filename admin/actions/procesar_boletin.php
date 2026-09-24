<?php
// actions/procesar_boletin.php
session_start();
require_once '../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $accion = $_POST['accion'] ?? '';
    
    // Nuevas rutas de subida separadas
    $dir_pdfs = '../uploads/boletines/pdfs/';
    $dir_fotos = '../uploads/boletines/fotos/';

    if (!file_exists($dir_pdfs)) mkdir($dir_pdfs, 0777, true);
    if (!file_exists($dir_fotos)) mkdir($dir_fotos, 0777, true);

    try {
        if ($accion === 'guardar') {
            $numero_boletin = trim($_POST['numero_boletin']);
            $resumen = trim($_POST['resumen']);
            $fecha_publicacion = $_POST['fecha_publicacion'];
            $usuario_id = $_SESSION['usuario_id'] ?? $_SESSION['id_usuario'] ?? 1;

            // Procesar PDF
            if (isset($_FILES['archivo_pdf']) && $_FILES['archivo_pdf']['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES['archivo_pdf']['name'], PATHINFO_EXTENSION));
                if ($ext !== 'pdf') throw new Exception("El archivo debe ser en formato PDF.");

                $nombre_pdf = uniqid('boletin_') . '.pdf';
                if (!move_uploaded_file($_FILES['archivo_pdf']['tmp_name'], $dir_pdfs . $nombre_pdf)) {
                    throw new Exception("Error al guardar el archivo PDF.");
                }
            } else {
                throw new Exception("Por favor adjunta un archivo PDF válido.");
            }

            // Procesar Foto de Portada
            $nombre_foto = null;
            if (isset($_FILES['foto_portada']) && $_FILES['foto_portada']['error'] === UPLOAD_ERR_OK) {
                $ext_foto = strtolower(pathinfo($_FILES['foto_portada']['name'], PATHINFO_EXTENSION));
                $nombre_foto = uniqid('portada_') . '.' . $ext_foto;
                if (!move_uploaded_file($_FILES['foto_portada']['tmp_name'], $dir_fotos . $nombre_foto)) {
                    throw new Exception("Error al guardar la foto de portada.");
                }
            }

            $stmt = $pdo->prepare("INSERT INTO boletines (numero_boletin, resumen, fecha_publicacion, archivo_pdf, usuario_id, foto_portada) VALUES (:numero, :resumen, :fecha, :pdf, :usuario, :foto)");
            $stmt->execute([
                ':numero' => $numero_boletin,
                ':resumen' => $resumen,
                ':fecha' => $fecha_publicacion,
                ':pdf' => $nombre_pdf,
                ':usuario' => $usuario_id,
                ':foto' => $nombre_foto
            ]);

            $_SESSION['mensaje_exito'] = "Boletín guardado con éxito.";

        } elseif ($accion === 'editar') {
            $id = $_POST['id'];
            $numero_boletin = trim($_POST['numero_boletin']);
            $resumen = trim($_POST['resumen']);
            $fecha_publicacion = $_POST['fecha_publicacion'];

            // Preparar update base
            $sql = "UPDATE boletines SET numero_boletin = :numero, resumen = :resumen, fecha_publicacion = :fecha WHERE id = :id";
            $parametros = [
                ':numero' => $numero_boletin,
                ':resumen' => $resumen,
                ':fecha' => $fecha_publicacion,
                ':id' => $id
            ];

            // Si suben un nuevo PDF
            if (isset($_FILES['archivo_pdf']) && $_FILES['archivo_pdf']['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES['archivo_pdf']['name'], PATHINFO_EXTENSION));
                if ($ext !== 'pdf') throw new Exception("El archivo debe ser en formato PDF.");

                $nombre_pdf = uniqid('boletin_') . '.pdf';
                if (move_uploaded_file($_FILES['archivo_pdf']['tmp_name'], $dir_pdfs . $nombre_pdf)) {
                    
                    // Borrar PDF viejo
                    $stmt_old = $pdo->prepare("SELECT archivo_pdf FROM boletines WHERE id = ?");
                    $stmt_old->execute([$id]);
                    $old_file = $stmt_old->fetchColumn();
                    if ($old_file) {
                        if (file_exists($dir_pdfs . $old_file)) unlink($dir_pdfs . $old_file);
                        elseif (file_exists('../uploads/boletines/' . $old_file)) unlink('../uploads/boletines/' . $old_file);
                    }

                    $sql = str_replace("WHERE", ", archivo_pdf = :pdf WHERE", $sql);
                    $parametros[':pdf'] = $nombre_pdf;
                }
            }

            // Si suben una nueva Foto
            if (isset($_FILES['foto_portada']) && $_FILES['foto_portada']['error'] === UPLOAD_ERR_OK) {
                $ext_foto = strtolower(pathinfo($_FILES['foto_portada']['name'], PATHINFO_EXTENSION));
                $nombre_foto = uniqid('portada_') . '.' . $ext_foto;
                
                if (move_uploaded_file($_FILES['foto_portada']['tmp_name'], $dir_fotos . $nombre_foto)) {
                    
                    // Borrar Foto vieja
                    $stmt_old = $pdo->prepare("SELECT foto_portada FROM boletines WHERE id = ?");
                    $stmt_old->execute([$id]);
                    $old_foto = $stmt_old->fetchColumn();
                    if ($old_foto && $old_foto !== 'default.png' && file_exists($dir_fotos . $old_foto)) {
                        unlink($dir_fotos . $old_foto);
                    }

                    $sql = str_replace("WHERE", ", foto_portada = :foto WHERE", $sql);
                    $parametros[':foto'] = $nombre_foto;
                }
            }

            $stmt = $pdo->prepare($sql);
            $stmt->execute($parametros);

            $_SESSION['mensaje_exito'] = "Boletín actualizado correctamente.";

        } elseif ($accion === 'eliminar') {
            $id = $_POST['id'];

            // Obtener archivos para borrarlos físicamente
            $stmt_old = $pdo->prepare("SELECT archivo_pdf, foto_portada FROM boletines WHERE id = ?");
            $stmt_old->execute([$id]);
            $files = $stmt_old->fetch(PDO::FETCH_ASSOC);
            
            if ($files) {
                // Borrar PDF
                if (!empty($files['archivo_pdf'])) {
                    if (file_exists($dir_pdfs . $files['archivo_pdf'])) unlink($dir_pdfs . $files['archivo_pdf']);
                    elseif (file_exists('../uploads/boletines/' . $files['archivo_pdf'])) unlink('../uploads/boletines/' . $files['archivo_pdf']);
                }
                // Borrar Foto
                if (!empty($files['foto_portada']) && $files['foto_portada'] !== 'default.png') {
                    if (file_exists($dir_fotos . $files['foto_portada'])) unlink($dir_fotos . $files['foto_portada']);
                }
            }

            $stmt = $pdo->prepare("DELETE FROM boletines WHERE id = ?");
            $stmt->execute([$id]);

            $_SESSION['mensaje_exito'] = "Boletín eliminado correctamente.";
        }

    } catch (Exception $e) {
        $_SESSION['mensaje_error'] = "Error: " . $e->getMessage();
    }

    header("Location: ../panel.php?modulo=boletines");
    exit;
}