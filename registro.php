<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require 'conexion.php';

    // Validar reCAPTCHA v3
    $recaptcha_response = $_POST['recaptcha_response'] ?? '';
    $secret_key = '6LcZ9T0rAAAAAGfMmRlaWOxwOvg9Hexw3LFe1sKR'; 

    $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret_key&response=$recaptcha_response");
    $captcha_success = json_decode($verify);

    if (!$captcha_success->success || $captcha_success->score < 0.5) {
        $error = "No se pudo verificar que eres humano. Intenta de nuevo.";
    } else {
        // Recibir y limpiar datos del formulario
        $nombre = trim($_POST['nombre']);
        $apellidos = trim($_POST['apellidos']);
        $telefono = trim($_POST['telefono']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // Validar que las contraseñas coincidan
        if ($password !== $confirm_password) {
            $error = "Las contraseñas no coinciden.";
        } else {
            // Verificar que el correo no esté ya registrado
            $check = $conx->prepare("SELECT idUsuario FROM usuario WHERE email = ?");
            $check->bind_param("s", $email);
            $check->execute();
            $check_result = $check->get_result();

            if ($check_result->num_rows > 0) {
                $error = "El correo ya está registrado.";
            } else {
                // Hash para la contraseña
                $hash = password_hash($password, PASSWORD_DEFAULT);

                // Obtener el ID del rol "CLIENTE"
                $rol_query = $conx->prepare("SELECT idRol FROM rol WHERE tipoRol = ?");
                $rol_nombre = "CLIENTE";
                $rol_query->bind_param("s", $rol_nombre);
                $rol_query->execute();
                $rol_result = $rol_query->get_result();

                if ($rol_result->num_rows > 0) {
                    $rol_row = $rol_result->fetch_assoc();
                    $rol_cliente = $rol_row['idRol'];

                    // Insertar usuario
                    $insert = $conx->prepare("INSERT INTO usuario (nombre, apellidos, numeroTelefono, email, contrasena, rol) VALUES (?, ?, ?, ?, ?, ?)");
                    $insert->bind_param("sssssi", $nombre, $apellidos, $telefono, $email, $hash, $rol_cliente);

                    if ($insert->execute()) {
                        $_SESSION['usuario'] = $insert->insert_id;
                        $_SESSION['rol'] = $rol_nombre;

                        if (isset($_GET['idEspectaculo']) && is_numeric($_GET['idEspectaculo'])) {
                            $id = (int)$_GET['idEspectaculo'];
                            echo "<script>window.location.href = 'index.php?mod=compraEspectaculo&idEspectaculo=$id';</script>";
                        } else {
                            echo "<script>window.location.href = 'index.php';</script>";
                        }
                        exit;
                    } else {
                        $error = "Error al registrar. Intenta nuevamente.";
                    }
                } else {
                    $error = "No se encontró el rol 'CLIENTE'.";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="icon" href="image/favicon.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <!-- reCAPTCHA v3 -->
    <script src="https://www.google.com/recaptcha/api.js?render=6LcZ9T0rAAAAAI28NEUKfbnQTouX7gZhNr5XyzQf"></script>
    <script>
        grecaptcha.ready(function () {
            grecaptcha.execute('6LcZ9T0rAAAAAI28NEUKfbnQTouX7gZhNr5XyzQf', { action: 'registro' }).then(function (token) {
                document.getElementById('recaptchaResponse').value = token;
            });
        });
    </script>

    <style>
        body, html { height: 100%; margin: 0; }
        .bg-cover { background-image: url('image/teatro.png'); background-size: cover; background-position: center; height: 100vh; display: flex; align-items: center; justify-content: center; position: relative; }
        .overlay { position: absolute; inset: 0; background-color: rgba(0, 0, 0, 0.4); }
        .register-card { z-index: 2; background-color: #fff; border-radius: 15px; padding: 30px; max-width: 500px; width: 90%; max-height: 90vh; overflow-y: auto; box-shadow: 0 0 20px rgba(0, 0, 0, 0.2); }
        .form-control { margin-bottom: 15px; }
        .logo { max-width: 200px; }
        .password-toggle { position: absolute; right: 15px; top: 75%; transform: translateY(-50%); cursor: pointer; color: gray; font-size: 18px; }
        .position-relative { position: relative; }
    </style>
</head>

<body>
    <div class="bg-cover">
        <div class="overlay"></div>
        <div class="register-card text-center">
            <img src="image/Logo.png" alt="Logo" class="logo mb-3">
            <h4 class="mb-4">Registro de Usuario</h4>

            <?php if (!empty($error)) : ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <input type="hidden" name="recaptcha_response" id="recaptchaResponse">

                <div class="mb-3 text-start">
                    <label class="form-label">Nombre</label>
                    <input type="text" class="form-control" name="nombre" required>
                </div>
                <div class="mb-3 text-start">
                    <label class="form-label">Apellidos</label>
                    <input type="text" class="form-control" name="apellidos" required>
                </div>
                <div class="mb-3 text-start">
                    <label class="form-label">Teléfono</label>
                    <input type="text" class="form-control" name="telefono" required>
                </div>
                <div class="mb-3 text-start">
                    <label class="form-label">Correo Electrónico</label>
                    <input type="email" class="form-control" name="email" required>
                </div>
                <div class="mb-3 text-start position-relative">
                    <label class="form-label">Contraseña</label>
                    <input type="password" class="form-control" name="password" id="password" required>
                    <i class="fa-solid fa-eye-slash password-toggle" onclick="togglePassword('password', this)"></i>
                </div>
                <div class="mb-4 text-start position-relative">
                    <label class="form-label">Confirmar Contraseña</label>
                    <input type="password" class="form-control" name="confirm_password" id="confirm_password" required>
                    <i class="fa-solid fa-eye-slash password-toggle" onclick="togglePassword('confirm_password', this)"></i>
                </div>
                <div class="form-check mb-3 text-start">
                    <input class="form-check-input" type="checkbox" name="accept_terms" id="accept_terms" required>
                    <label class="form-check-label" for="accept_terms">
                        Acepto las <a href="index.php?mod=politicasPrivacidad" class="text-primary">políticas de privacidad</a> y los <a href="index.php?mod=terminosUso" class="text-primary">términos de uso</a>.
                    </label>
                </div>
                <button type="submit" class="btn btn-danger w-100 rounded-pill">Registrarse</button>
            </form>

            <a href="index.php" class="btn btn-secondary w-100 mt-2 rounded-pill">Volver al inicio</a>
            <p class="mt-3 mb-1">¿Ya tienes una cuenta? <a href="login.php">Inicia sesión</a></p>
        </div>
    </div>

    <script>
        function togglePassword(id, icon) {
            const input = document.getElementById(id);
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            } else {
                input.type = "password";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            }
        }
    </script>
</body>
</html>
