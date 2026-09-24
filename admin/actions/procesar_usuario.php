<?php
// actions/procesar_usuario.php
session_start();
require_once '../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $accion = $_POST['accion'] ?? '';

    try {
        if ($accion === 'guardar') {
            $nombres = trim($_POST['nombres']);
            $ap_paterno = trim($_POST['ap_paterno']);
            $ap_materno = trim($_POST['ap_materno']) !== '' ? trim($_POST['ap_materno']) : null;
            $email = trim($_POST['email']);
            $rol = $_POST['rol'];
            $password = $_POST['password'];

            // Verificar si el email ya existe
            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                throw new Exception("El correo electrónico ya está registrado en otro usuario.");
            }

            // Hashear contraseña por seguridad
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("INSERT INTO usuarios (nombres, ap_paterno, ap_materno, email, password_hash, rol) VALUES (:nombres, :ap_paterno, :ap_materno, :email, :password_hash, :rol)");
            $stmt->execute([
                ':nombres' => $nombres,
                ':ap_paterno' => $ap_paterno,
                ':ap_materno' => $ap_materno,
                ':email' => $email,
                ':password_hash' => $password_hash,
                ':rol' => $rol
            ]);

            $_SESSION['mensaje_exito'] = "Usuario creado con éxito.";

        } elseif ($accion === 'editar') {
            $id = $_POST['id'];
            $nombres = trim($_POST['nombres']);
            $ap_paterno = trim($_POST['ap_paterno']);
            $ap_materno = trim($_POST['ap_materno']) !== '' ? trim($_POST['ap_materno']) : null;
            $email = trim($_POST['email']);
            $rol = $_POST['rol'];
            $password = $_POST['password'];

            // Verificar si el email existe en otro usuario distinto al que estamos editando
            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
            $stmt->execute([$email, $id]);
            if ($stmt->fetch()) {
                throw new Exception("El correo electrónico ya está siendo usado por otra persona.");
            }

            if (!empty($password)) {
                // Si escribió una nueva contraseña, la hasheamos y la actualizamos
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE usuarios SET nombres = :nombres, ap_paterno = :ap_paterno, ap_materno = :ap_materno, email = :email, rol = :rol, password_hash = :password_hash WHERE id = :id");
                $stmt->execute([
                    ':nombres' => $nombres,
                    ':ap_paterno' => $ap_paterno,
                    ':ap_materno' => $ap_materno,
                    ':email' => $email,
                    ':rol' => $rol,
                    ':password_hash' => $password_hash,
                    ':id' => $id
                ]);
            } else {
                // Si dejó la contraseña en blanco, no la actualizamos
                $stmt = $pdo->prepare("UPDATE usuarios SET nombres = :nombres, ap_paterno = :ap_paterno, ap_materno = :ap_materno, email = :email, rol = :rol WHERE id = :id");
                $stmt->execute([
                    ':nombres' => $nombres,
                    ':ap_paterno' => $ap_paterno,
                    ':ap_materno' => $ap_materno,
                    ':email' => $email,
                    ':rol' => $rol,
                    ':id' => $id
                ]);
            }

            $_SESSION['mensaje_exito'] = "Usuario actualizado correctamente.";

        } elseif ($accion === 'eliminar') {
            $id = $_POST['id'];
            $usuario_actual = $_SESSION['usuario_id'] ?? 0;

            // Evitar que el usuario se elimine a sí mismo (opcional pero recomendado)
            if ($id == $usuario_actual) {
                throw new Exception("No puedes eliminar tu propia cuenta mientras tienes sesión iniciada.");
            }

            try {
                $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
                $stmt->execute([$id]);
                $_SESSION['mensaje_exito'] = "Usuario eliminado correctamente.";
            } catch (PDOException $e) {
                // Si el usuario tiene reportajes/noticias asociados, saltará error por clave foránea (RESTRICT)
                if ($e->getCode() == '23000') {
                    throw new Exception("No se puede eliminar este usuario porque tiene publicaciones (noticias, reportajes) asociadas a su nombre.");
                } else {
                    throw $e;
                }
            }
        }

    } catch (Exception $e) {
        $_SESSION['mensaje_error'] = $e->getMessage();
    }

    header("Location: ../panel.php?modulo=usuarios");
    exit;
}