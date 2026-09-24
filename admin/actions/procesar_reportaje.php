<?php
// actions/procesar_reportaje.php
session_start();
require_once '../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    
    // Rutas de subida
    $dir_fotos = '../uploads/reportajes/fotos/';
    $dir_pdfs = '../uploads/reportajes/pdfs/';
    if(!is_dir($dir_fotos)) mkdir($dir_fotos, 0777, true);
    if(!is_dir($dir_pdfs)) mkdir($dir_pdfs, 0777, true);

    try {
        $pdo->beginTransaction();

        // 1. Recibir datos comunes
        $id = $_POST['id'] ?? null;
        $titulo = trim($_POST['titulo'] ?? '');
        $resumen_corto = trim($_POST['resumen_corto'] ?? '');
        $desarrollo = trim($_POST['desarrollo'] ?? '');
        $fecha_publicacion = $_POST['fecha_publicacion'] ?? date('Y-m-d'); 
        $autor_id = $_POST['autor_id'] ?? null;
        $es_destacado = isset($_POST['es_destacado']) ? 1 : 0;
        
        $fotos_adicionales = []; // Aquí guardaremos TODAS las fotos secundarias (galería + texto)

        // =========================================================
        // MAGIA: PROCESAR IMÁGENES DENTRO DEL TEXTO (SUMMERNOTE)
        // =========================================================
        if (!empty($desarrollo)) {
            $dom = new DOMDocument();
            // Evitamos warnings por etiquetas HTML5 y forzamos codificación UTF-8
            libxml_use_internal_errors(true);
            $dom->loadHTML(mb_convert_encoding($desarrollo, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            libxml_clear_errors();

            $imagenes = $dom->getElementsByTagName('img');
            foreach ($imagenes as $img) {
                $src = $img->getAttribute('src');
                
                // Si la imagen es un dato crudo (fue insertada en el editor)
                if (preg_match('/^data:image\/(\w+);base64,/', $src, $tipo)) {
                    $data = substr($src, strpos($src, ',') + 1);
                    $extension = strtolower($tipo[1]);
                    $data = base64_decode($data);
                    
                    // Generar un nombre único para evitar colisiones
                    $nombre_inline = time() . '_' . uniqid() . '_inline.' . $extension;
                    $ruta_fisica = $dir_fotos . $nombre_inline;
                    $ruta_bd_inline = 'uploads/reportajes/fotos/' . $nombre_inline;
                    
                    // 1. Guardamos el archivo físico
                    file_put_contents($ruta_fisica, $data);
                    
                    // 2. Actualizamos el HTML para que use la ruta real del archivo
                    // Ponemos '../' para que el visor del editor en admin la pueda cargar. 
                    // Al front-end web no le afectará, bajará a la raíz correctamente.
                    $img->setAttribute('src', '../' . $ruta_bd_inline); 
                    
                    // 3. Añadimos a la lista para registrar en 'reportajes_fotos'
                    $fotos_adicionales[] = $ruta_bd_inline;
                }
            }
            // Sobrescribimos la variable con el HTML limpio y ligero
            $desarrollo = $dom->saveHTML();
        }
        // =========================================================

        if ($accion === 'guardar') {
            $usuario_id = $_SESSION['id_usuario'] ?? 1;

            // Manejo del PDF
            $ruta_pdf = null;
            if (isset($_FILES['pdf_adjunto']) && $_FILES['pdf_adjunto']['error'] === UPLOAD_ERR_OK) {
                $nombre_pdf = time() . '_' . basename($_FILES['pdf_adjunto']['name']);
                if (move_uploaded_file($_FILES['pdf_adjunto']['tmp_name'], $dir_pdfs . $nombre_pdf)) {
                    $ruta_pdf = 'uploads/reportajes/pdfs/' . $nombre_pdf;
                }
            }

            // Manejo de Fotos Galería
            $foto_principal = '';
            if (isset($_FILES['fotos']) && $_FILES['fotos']['error'][0] === UPLOAD_ERR_OK) {
                foreach ($_FILES['fotos']['tmp_name'] as $key => $tmp_name) {
                    $nombre_foto = time() . '_' . $key . '_' . basename($_FILES['fotos']['name'][$key]);
                    if (move_uploaded_file($tmp_name, $dir_fotos . $nombre_foto)) {
                        $ruta_bd = 'uploads/reportajes/fotos/' . $nombre_foto;
                        if ($key === 0) {
                            $foto_principal = $ruta_bd; // La primera de la galería es la principal
                        }
                        $fotos_adicionales[] = $ruta_bd; // Añadir a la lista general
                    }
                }
            }

            // Insertar Reportaje
            $stmt = $pdo->prepare("INSERT INTO reportajes (titulo, resumen_corto, desarrollo, foto_principal, pdf_adjunto, fecha_publicacion, es_destacado, autor_id, usuario_id) 
                                   VALUES (:titulo, :resumen, :desarrollo, :foto, :pdf, :fecha, :destacado, :autor, :usuario)");
            $stmt->execute([
                ':titulo' => $titulo,
                ':resumen' => $resumen_corto,
                ':desarrollo' => $desarrollo,
                ':foto' => $foto_principal,
                ':pdf' => $ruta_pdf,
                ':fecha' => $fecha_publicacion,
                ':destacado' => $es_destacado,
                ':autor' => $autor_id,
                ':usuario' => $usuario_id
            ]);

            $id_nuevo = $pdo->lastInsertId();

            // Insertar TODAS las fotos (las del texto y las de la galería)
            if (count($fotos_adicionales) > 0) {
                $stmt_fotos = $pdo->prepare("INSERT INTO reportajes_fotos (reportaje_id, url_foto, orden) VALUES (:id_rep, :url, :orden)");
                foreach ($fotos_adicionales as $index => $ruta_foto) {
                    $stmt_fotos->execute([
                        ':id_rep' => $id_nuevo,
                        ':url' => $ruta_foto,
                        ':orden' => $index + 1
                    ]);
                }
            }
            $_SESSION['mensaje_exito'] = "Reportaje publicado correctamente.";

        } elseif ($accion === 'editar') {
            $sql = "UPDATE reportajes SET titulo = :titulo, resumen_corto = :resumen, desarrollo = :desarrollo, 
                    autor_id = :autor, fecha_publicacion = :fecha, es_destacado = :destacado WHERE id = :id";
            
            $parametros = [
                ':titulo' => $titulo, ':resumen' => $resumen_corto, ':desarrollo' => $desarrollo,
                ':autor' => $autor_id, ':fecha' => $fecha_publicacion, ':destacado' => $es_destacado, ':id' => $id
            ];

            if (isset($_FILES['foto_principal']) && $_FILES['foto_principal']['error'] === UPLOAD_ERR_OK) {
                $nombre_foto = time() . '_edit_' . basename($_FILES['foto_principal']['name']);
                if (move_uploaded_file($_FILES['foto_principal']['tmp_name'], $dir_fotos . $nombre_foto)) {
                    $sql = str_replace("WHERE", ", foto_principal = :foto WHERE", $sql);
                    $parametros[':foto'] = 'uploads/reportajes/fotos/' . $nombre_foto;
                }
            }

            if (isset($_FILES['pdf_adjunto']) && $_FILES['pdf_adjunto']['error'] === UPLOAD_ERR_OK) {
                $nombre_pdf = time() . '_edit_' . basename($_FILES['pdf_adjunto']['name']);
                if (move_uploaded_file($_FILES['pdf_adjunto']['tmp_name'], $dir_pdfs . $nombre_pdf)) {
                    $sql = str_replace("WHERE", ", pdf_adjunto = :pdf WHERE", $sql);
                    $parametros[':pdf'] = 'uploads/reportajes/pdfs/' . $nombre_pdf;
                }
            }

            $stmt = $pdo->prepare($sql);
            $stmt->execute($parametros);

            // Registrar las imágenes NUEVAS que se hayan añadido al texto durante la edición
            if (count($fotos_adicionales) > 0) {
                // Sacamos el último orden para que sigan la secuencia correcta
                $stmt_orden = $pdo->prepare("SELECT MAX(orden) FROM reportajes_fotos WHERE reportaje_id = ?");
                $stmt_orden->execute([$id]);
                $ultimo_orden = $stmt_orden->fetchColumn() ?: 0;

                $stmt_fotos = $pdo->prepare("INSERT INTO reportajes_fotos (reportaje_id, url_foto, orden) VALUES (:id_rep, :url, :orden)");
                foreach ($fotos_adicionales as $index => $ruta_foto) {
                    $stmt_fotos->execute([
                        ':id_rep' => $id,
                        ':url' => $ruta_foto,
                        ':orden' => $ultimo_orden + $index + 1
                    ]);
                }
            }

            $_SESSION['mensaje_exito'] = "Reportaje actualizado correctamente.";

        } elseif ($accion === 'eliminar') {
            // Esto se encarga de barrer tanto fotos de galería como del texto, gracias a la tabla reportajes_fotos.
            $stmt_galeria = $pdo->prepare("SELECT url_foto FROM reportajes_fotos WHERE reportaje_id = ?");
            $stmt_galeria->execute([$id]);
            while ($foto = $stmt_galeria->fetch(PDO::FETCH_ASSOC)) {
                if (file_exists('../' . $foto['url_foto'])) unlink('../' . $foto['url_foto']);
            }

            $stmt = $pdo->prepare("SELECT foto_principal, pdf_adjunto FROM reportajes WHERE id = ?");
            $stmt->execute([$id]);
            $archivos = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($archivos) {
                if (!empty($archivos['foto_principal']) && file_exists('../' . $archivos['foto_principal'])) unlink('../' . $archivos['foto_principal']);
                if (!empty($archivos['pdf_adjunto']) && file_exists('../' . $archivos['pdf_adjunto'])) unlink('../' . $archivos['pdf_adjunto']);
            }

            $stmt_del = $pdo->prepare("DELETE FROM reportajes WHERE id = ?");
            $stmt_del->execute([$id]);

            $_SESSION['mensaje_exito'] = "El reportaje fue eliminado de forma permanente.";
        }

        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['mensaje_error'] = "Error del sistema: " . $e->getMessage();
    }

    header("Location: ../panel.php?modulo=reportajes");
    exit;
}