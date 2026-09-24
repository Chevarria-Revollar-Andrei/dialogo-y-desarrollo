<?php
// index.php
session_start();
if (isset($_SESSION['id_usuario'])) {
    header("Location: panel.php");
    exit;
}

// Manejo de mensajes de error si viene desde procesar_login.php
$error_msg = '';
if (isset($_GET['error'])) {
    if ($_GET['error'] == 'credenciales') {
        $error_msg = 'Correo o contraseña incorrectos.';
    } else if ($_GET['error'] == 'empty') {
        $error_msg = 'Por favor, completa todos los campos.';
    } else {
        $error_msg = 'Ocurrió un error al intentar iniciar sesión.';
    }
}
?>
<!doctype html>
<html lang="es" translate="no">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar Sesión — Diálogo y Desarrollo</title>
    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Font Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- SweetAlert2 (Para la alerta pro de recuperación) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        :root {
            --bg: #0f172a;
            --card-bg: #ffffff;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --accent-red: #e11d48;
            --accent-red-hover: #be123c;
            --line: #e2e8f0;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            background-color: #f8fafc;
            /* Fondo ambiental con sutil destello oscuro/carmesí */
            background-image: 
                radial-gradient(at 0% 0%, rgba(225, 29, 72, 0.05) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(15, 23, 42, 0.05) 0px, transparent 50%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
            color: var(--text-dark);
        }

        .login-wrapper {
            width: 100%;
            max-width: 420px;
        }

        .login-card {
            background: var(--card-bg);
            border: 1px solid rgba(226, 232, 240, 0.9);
            border-radius: 18px;
            box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.07);
            padding: 40px 32px;
        }

        /* Icono de Marca */
        .brand-header {
            text-align: center;
            margin-bottom: 28px;
        }

        .brand-icon {
            width: 54px;
            height: 54px;
            border-radius: 14px;
            background: linear-gradient(135deg, #e11d48 0%, #9f1239 100%);
            display: grid;
            place-items: center;
            color: #ffffff;
            font-size: 24px;
            margin: 0 auto 16px;
            box-shadow: 0 8px 20px rgba(225, 29, 72, 0.25);
        }

        .brand-header h1 {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: -0.4px;
            margin: 0 0 6px 0;
            color: var(--text-dark);
        }

        .brand-header p {
            color: var(--text-muted);
            font-size: 12px;
            margin: 0;
        }

        /* Estilos de Formulario */
        .form-label {
            font-size: 11px;
            font-weight: 700;
            color: #475569;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .input-group-custom {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-group-custom i.input-icon {
            position: absolute;
            left: 14px;
            color: #94a3b8;
            font-size: 15px;
            pointer-events: none;
            transition: color 0.2s;
        }

        .form-control {
            font-size: 13px;
            font-weight: 500;
            border-radius: 10px;
            padding: 11px 14px 11px 40px;
            background: #f8fafc;
            border: 1px solid var(--line);
            color: var(--text-dark);
            transition: all 0.2s ease;
        }

        .form-control:focus {
            background: #ffffff;
            border-color: var(--accent-red);
            box-shadow: 0 0 0 3px rgba(225, 29, 72, 0.1);
            outline: none;
        }

        .form-control:focus + i.input-icon,
        .input-group-custom:focus-within i.input-icon {
            color: var(--accent-red);
        }

        /* Botón Mostrar Contraseña */
        .btn-toggle-pass {
            position: absolute;
            right: 12px;
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 4px;
            font-size: 15px;
            display: grid;
            place-items: center;
            transition: color 0.2s;
        }

        .btn-toggle-pass:hover {
            color: var(--text-dark);
        }

        /* Enlace Olvidó Contraseña */
        .forgot-link {
            font-size: 11px;
            font-weight: 600;
            color: var(--accent-red);
            text-decoration: none;
            transition: color 0.2s;
        }

        .forgot-link:hover {
            color: var(--accent-red-hover);
            text-decoration: underline;
        }

        /* Botón Iniciar Sesión */
        .btn-primary {
            background: linear-gradient(135deg, #e11d48 0%, #be123c 100%);
            border: none;
            font-size: 13px;
            font-weight: 700;
            border-radius: 10px;
            padding: 12px;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(225, 29, 72, 0.2);
            transition: all 0.2s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #be123c 0%, #9f1239 100%);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(225, 29, 72, 0.3);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        /* Alerta de Error */
        .alert-custom {
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 500;
            background-color: #fff1f2;
            border: 1px solid #ffe4e6;
            color: #be123c;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .login-footer {
            text-align: center;
            margin-top: 24px;
            font-size: 11px;
            color: var(--text-muted);
        }
    </style>
</head>
<body>

<div class="login-wrapper">
    <div class="login-card">
        
        <!-- Cabecera -->
        <div class="brand-header">
            <div class="brand-icon">
                <i class="bi bi-chat-square-text-fill"></i>
            </div>
            <h1>Diálogo y Desarrollo</h1>
            <p>Panel de Administración General</p>
        </div>

        <!-- Alerta de Error si existiese -->
        <?php if (!empty($error_msg)): ?>
            <div class="alert-custom">
                <i class="bi bi-exclamation-triangle-fill fs-6"></i>
                <span><?= htmlspecialchars($error_msg) ?></span>
            </div>
        <?php endif; ?>

        <!-- Formulario de Login -->
        <form action="actions/procesar_login.php" method="POST">
            
            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico</label>
                <div class="input-group-custom">
                    <input type="email" id="email" name="email" class="form-control" placeholder="Correo Electronico" required autofocus>
                    <i class="bi bi-envelope input-icon"></i>
                </div>
            </div>

            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label for="password" class="form-label mb-0">Contraseña</label>
                    <a href="#" class="forgot-link" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">¿Olvidaste tu contraseña?</a>
                </div>
                <div class="input-group-custom">
                    <input type="password" id="password" name="password" class="form-control" style="padding-right: 40px;" placeholder="Contraseña" required>
                    <i class="bi bi-lock input-icon"></i>
                    <button type="button" class="btn-toggle-pass" id="togglePassword" title="Mostrar contraseña">
                        <i class="bi bi-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                Iniciar Sesión <i class="bi bi-arrow-right-short ms-1 fs-6"></i>
            </button>
        </form>

    </div>

    <!-- Footer opcional -->
    <div class="login-footer">
        &copy; <?= date('Y') ?> Diálogo y Desarrollo. Todos los derechos reservados.
    </div>
</div>

<!-- Modal para Recuperar Contraseña -->
<div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: 1px solid var(--line); box-shadow: 0 20px 40px rgba(0,0,0,0.15);">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="forgotPasswordModalLabel" style="font-size: 16px; color: var(--text-dark);">
                    <i class="bi bi-shield-lock-fill text-danger me-2"></i>Recuperar Contraseña
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <p class="text-muted" style="font-size: 12px; margin-bottom: 16px; line-height: 1.5;">
                    Ingresa el correo electrónico asociado a tu cuenta. Te enviaremos un enlace temporal con instrucciones para restablecer tu contraseña.
                </p>
                <form id="forgotForm" onsubmit="handleForgotPass(event)">
                    <div class="mb-3">
                        <label for="recoverEmail" class="form-label">Correo Electrónico</label>
                        <div class="input-group-custom">
                            <input type="email" id="recoverEmail" class="form-control" placeholder="ejemplo@correo.com" required>
                            <i class="bi bi-envelope input-icon"></i>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mt-2">
                        Enviar Enlace de Recuperación
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Scripts de Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Alternar visibilidad de contraseña
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');

    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function () {
            const isPassword = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
            eyeIcon.classList.toggle('bi-eye');
            eyeIcon.classList.toggle('bi-eye-slash');
        });
    }

    // Manejador del formulario ficticio de recuperación
    function handleForgotPass(e) {
        e.preventDefault();
        const email = document.getElementById('recoverEmail').value;
        
        // Cerrar el modal
        const modalElement = document.getElementById('forgotPasswordModal');
        const modal = bootstrap.Modal.getInstance(modalElement);
        if(modal) modal.hide();

        // Mostrar SweetAlert ficticio pero super realista
        Swal.fire({
            icon: 'success',
            title: '¡Enlace Enviado!',
            text: `Hemos enviado las instrucciones de recuperación a: ${email}`,
            confirmButtonColor: '#e11d48',
            confirmButtonText: 'Entendido',
            customClass: {
                popup: 'rounded-4'
            }
        });

        // Limpiar campo
        document.getElementById('recoverEmail').value = '';
    }
</script>

</body>
</html>