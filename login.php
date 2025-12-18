<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require 'conexion.php';

    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $recaptcha_response = $_POST['recaptcha_response'];

    // Verificar reCAPTCHA v3
    $secret_key = '6LcZ9T0rAAAAAGfMmRlaWOxwOvg9Hexw3LFe1sKR';
    $verify_url = 'https://www.google.com/recaptcha/api/siteverify';

    $data = [
        'secret' => $secret_key,
        'response' => $recaptcha_response,
        'remoteip' => $_SERVER['REMOTE_ADDR']
    ];

    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        ]
    ];

    $context  = stream_context_create($options);
    $verify = file_get_contents($verify_url, false, $context);
    $captcha_success = json_decode($verify);

    if ($captcha_success->success && $captcha_success->score >= 0.5) {
        // Si pasa el reCAPTCHA, verificar usuario
        $query = $conx->prepare("
            SELECT u.idUsuario, r.tipoRol, u.contrasena 
            FROM usuario u 
            INNER JOIN rol r ON u.rol = r.idRol 
            WHERE u.email = ?
        ");
        $query->bind_param("s", $email);
        $query->execute();
        $result = $query->get_result();

        if ($result->num_rows === 1) {
            $usuario = $result->fetch_assoc();

            if (password_verify($password, $usuario['contrasena'])) {
                $_SESSION['usuario'] = $usuario['idUsuario'];
                $_SESSION['rol'] = $usuario['tipoRol'];

                // Redirección condicional
                if (isset($_GET['idEspectaculo']) && is_numeric($_GET['idEspectaculo'])) {
                    $id = (int)$_GET['idEspectaculo'];
                    header("Location: index.php?mod=compraEspectaculo&idEspectaculo=$id");
                } else {
                    header('Location: index.php');
                }
                exit;
            } else {
                $error = "La contraseña es incorrecta.";
            }
        } else {
            $error = "El correo electrónico no está registrado.";
        }
    } else {
        $error = "Falló la verificación de reCAPTCHA.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="image/favicon.png" type="image/png">
    <title>Iniciar sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://www.google.com/recaptcha/api.js?render=6LcZ9T0rAAAAAI28NEUKfbnQTouX7gZhNr5XyzQf"></script>
    <script>
        grecaptcha.ready(function() {
            grecaptcha.execute('6LcZ9T0rAAAAAI28NEUKfbnQTouX7gZhNr5XyzQf', { action: 'login' }).then(function(token) {
                document.getElementById('recaptchaResponse').value = token;
            });
        });
    </script>
    <style>
        body,
        html {
            height: 100%;
            margin: 0;
        }

        .bg-cover {
            background-image: url('image/teatro.png');
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .overlay {
            position: absolute;
            inset: 0;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .login-card {
            z-index: 2;
            background-color: #ffffff;
            border-radius: 15px;
            padding: 30px;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
        }

        .form-control {
            padding: 10px;
            margin-bottom: 10px;
        }

        .logo {
            max-width: 200px;
        }
    </style>
</head>

<body>
    <div class="bg-cover">
        <div class="overlay"></div>

        <div class="login-card text-center">
            <img src="image/Logo.png" alt="Logo" class="logo mb-3">
            <h4 class="mb-4">Iniciar Sesión</h4>

            <?php if (!empty($error)) : ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3 text-start">
                    <label for="email" class="form-label">Correo Electrónico</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3 text-start">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>

                <!-- Campo oculto reCAPTCHA -->
                <input type="hidden" name="recaptcha_response" id="recaptchaResponse">

                <p class="mt-3 mb-1">¿Has olvidado tu contraseña? <a href="recordarContraseña.php">Recuperar contraseña</a></p>
                <button type="submit" class="btn btn-danger w-100 mt-2 rounded-pill">Iniciar Sesión</button>
            </form>

            <a href="index.php" class="btn btn-secondary w-100 mt-2 rounded-pill">Volver al inicio</a>
            <p class="mt-3 mb-1">¿No tienes una cuenta? <a href="registro.php">Regístrate</a></p>
        </div>
    </div>
</body>

</html>
