<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require 'conexion.php';

    $email = trim($_POST['email']);

    // Consulta para verificar si el correo existe en la base de datos
    $query = $conx->prepare("SELECT idUsuario FROM usuario WHERE email = ?");
    $query->bind_param("s", $email);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows === 1) {
        // Si el correo existe, redirigir a la página de procesamiento de recuperación de contraseña
        echo "<form id='redirectForm' method='POST' action='correoContrasena.php'>
                <input type='hidden' name='email' value='" . htmlspecialchars($email) . "'>
              </form>";
        echo "<script>document.getElementById('redirectForm').submit();</script>";
        exit;
    } else {
        // Si el correo no existe, mostrar un mensaje de error
        $error = "El correo electrónico no está registrado en nuestra base de datos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="image/favicon.png" type="image/png">
    <title>Recuperar Contraseña</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
            <h4 class="mb-4">Recuperar Contraseña</h4>

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
                <button type="submit" class="btn btn-danger w-100 mt-2 rounded-pill">Recuperar Contraseña</button>
            </form>

            <a href="index.php" class="btn btn-secondary w-100 mt-2 rounded-pill">Volver al inicio</a>
        </div>
    </div>
</body>

</html>
