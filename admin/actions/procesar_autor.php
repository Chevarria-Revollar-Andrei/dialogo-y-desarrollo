<?php
// actions/procesar_autor.php
session_start();
require_once '../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $accion = $_POST['accion'] ?? '';

    try {
        if ($accion === 'guardar') {
            $nombres = trim($_POST['nombres']);
            $ap_paterno = trim($_POST['ap_paterno']) !== '' ? trim($_POST['ap_paterno']) : null;
            $ap_materno = trim($_POST['ap_materno']) !== '' ? trim($_POST['ap_materno']) : null;
            $es_nickname = isset($_POST['es_nickname']) ? 1 : 0;
            $nickname = trim($_POST['nickname']) !== '' ? trim($_POST['nickname']) : null;

            if ($es_nickname && empty($nickname)) {
                throw new Exception("Si marcas usar seudónimo, debes escribir uno.");
            }

            $stmt = $pdo->prepare("INSERT INTO autores (nombres, ap_paterno, ap_materno, nickname, es_nickname) VALUES (:nombres, :ap_paterno, :ap_materno, :nickname, :es_nickname)");
            $stmt->execute([
                ':nombres' => $nombres,
                ':ap_paterno' => $ap_paterno,
                ':ap_materno' => $ap_materno,
                ':nickname' => $nickname,
                ':es_nickname' => $es_nickname
            ]);

            $_SESSION['mensaje_exito'] = "Autor registrado con éxito.";

        } elseif ($accion === 'editar') {
            $id = $_POST['id'];
            $nombres = trim($_POST['nombres']);
            $ap_paterno = trim($_POST['ap_paterno']) !== '' ? trim($_POST['ap_paterno']) : null;
            $ap_materno = trim($_POST['ap_materno']) !== '' ? trim($_POST['ap_materno']) : null;
            $es_nickname = isset($_POST['es_nickname']) ? 1 : 0;
            $nickname = trim($_POST['nickname']) !== '' ? trim($_POST['nickname']) : null;

            if ($es_nickname && empty($nickname)) {
                throw new Exception("Si marcas usar seudónimo, debes escribir uno.");
            }

            $stmt = $pdo->prepare("UPDATE autores SET nombres = :nombres, ap_paterno = :ap_paterno, ap_materno = :ap_materno, nickname = :nickname, es_nickname = :es_nickname WHERE id = :id");
            $stmt->execute([
                ':nombres' => $nombres,
                ':ap_paterno' => $ap_paterno,
                ':ap_materno' => $ap_materno,
                ':nickname' => $nickname,
                ':es_nickname' => $es_nickname,
                ':id' => $id
            ]);

            $_SESSION['mensaje_exito'] = "Autor actualizado correctamente.";

        } elseif ($accion === 'eliminar') {
            $id = $_POST['id'];

            try {
                $stmt = $pdo->prepare("DELETE FROM autores WHERE id = ?");
                $stmt->execute([$id]);
                $_SESSION['mensaje_exito'] = "Autor eliminado correctamente.";
            } catch (PDOException $e) {
                // Verificar si el error es por restricción de clave foránea (codigo 23000)
                if ($e->getCode() == '23000') {
                    throw new Exception("No puedes eliminar este autor porque ya tiene reportajes asignados.");
                } else {
                    throw $e;
                }
            }
        }

    } catch (Exception $e) {
        $_SESSION['mensaje_error'] = $e->getMessage();
    }

    header("Location: ../panel.php?modulo=autores");
    exit;
}