<?php
session_start();
$usuarioId = $_SESSION['usuario'];

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!$conx) {
    die("Error en la conexión: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password_actual = $_POST['password_actual'];
    $nueva_password = $_POST['nueva_password'];
    $confirmar_password = $_POST['confirmar_password'];

    // Obtener el hash actual desde la BD
    $query = "SELECT contrasena FROM usuario WHERE idUsuario = '$usuarioId'";
    $result = mysqli_query($conx, $query);

    if ($row = mysqli_fetch_assoc($result)) {
        $hash_actual = $row['contrasena'];

        // Verificar la contraseña actual
        if (!password_verify($password_actual, $hash_actual)) {
            $error = "La contraseña actual es incorrecta.";
        } elseif ($nueva_password !== $confirmar_password) {
            $error = "La nueva contraseña y su confirmación no coinciden.";
        } elseif (strlen($nueva_password) < 6) {
            $error = "La nueva contraseña debe tener al menos 6 caracteres.";
        } else {
            // Hashear la nueva contraseña
            $nuevo_hash = password_hash($nueva_password, PASSWORD_DEFAULT);

            $updateQuery = "UPDATE usuario SET contrasena = '$nuevo_hash' WHERE idUsuario = '$usuarioId'";
            if (mysqli_query($conx, $updateQuery)) {
                $success = "Contraseña actualizada correctamente.";
            } else {
                $error = "Error al actualizar la contraseña: " . mysqli_error($conx);
            }
        }
    } else {
        $error = "Usuario no encontrado.";
    }
}
?>


<!--================ Breadcrumb =================-->
<section class="breadcrumb_area">
    <div class="overlay bg-parallax"></div>
    <div class="container">
        <div class="page-cover text-start">
            <h2 class="page-cover-tittle">Cambiar Contraseña</h2>
            <ol class="breadcrumb">
                <li><a href="index.html">Inicio</a></li>
                <li class="active">Cambiar Contraseña</li>
            </ol>
        </div>
    </div>
</section>

<!--================ Submenú Cuadrado =================-->
<div class="container my-4">
    <div class="row text-center justify-content-center">
        <!-- Panel Personal -->
        <div class="col-6 col-sm-3 mb-3">
            <a href="index.php?mod=usuario-panel" class="card shadow-sm h-100 text-decoration-none text-dark submenu-card">
                <div class="card-body d-flex flex-column justify-content-center">
                    <i class="fas fa-user-circle fa-2x mb-2"></i>
                    <span>Panel Personal</span>
                </div>
            </a>
        </div>
        <!-- Entradas -->
        <div class="col-6 col-sm-3 mb-3">
            <a href="index.php?mod=usuario-espectaculos" class="card shadow-sm h-100 text-decoration-none text-dark submenu-card">
                <div class="card-body d-flex flex-column justify-content-center">
                    <i class="fas fa-ticket-alt fa-2x mb-2"></i>
                    <span>Entradas</span>
                </div>
            </a>
        </div>
        <!-- Editar Perfil -->
        <div class="col-6 col-sm-3 mb-3">
            <a href="index.php?mod=usuario-editar" class="card shadow-sm h-100 text-decoration-none text-dark submenu-card">
                <div class="card-body d-flex flex-column justify-content-center">
                    <i class="fas fa-user-edit fa-2x mb-2"></i>
                    <span>Editar Perfil</span>
                </div>
            </a>
        </div>
        <!-- Cambiar Contraseña -->
        <div class="col-6 col-sm-3 mb-3">
            <a href="index.php?mod=usuario-contrasena" class="card shadow-sm h-100 text-decoration-none text-dark submenu-card">
                <div class="card-body d-flex flex-column justify-content-center">
                    <i class="fas fa-key fa-2x mb-2"></i>
                    <span>Cambiar Contraseña</span>
                </div>
            </a>
        </div>
    </div>
</div>

<!--================ Formulario Cambiar Contraseña =================-->
<div class="container-fluid mt-5 px-5">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card user-profile-card shadow-lg" style="border-radius: 10px;">
                <div class="card-header text-center user-profile-card-header">
                    <h4>Cambiar Contraseña</h4>
                </div>
                <div class="card-body user-profile-card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <?php if (isset($success)): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <!-- Contraseña actual -->
                        <div class="form-group mb-3">
                            <label for="password_actual" class="font-weight-bold">Contraseña Actual</label>
                            <input type="password" name="password_actual" id="password_actual" class="form-control user-profile-input" required>
                        </div>

                        <!-- Nueva contraseña -->
                        <div class="form-group mb-3">
                            <label for="nueva_password" class="font-weight-bold">Nueva Contraseña</label>
                            <input type="password" name="nueva_password" id="nueva_password" class="form-control user-profile-input" required minlength="6">
                        </div>

                        <!-- Confirmar nueva contraseña -->
                        <div class="form-group mb-4">
                            <label for="confirmar_password" class="font-weight-bold">Confirmar Nueva Contraseña</label>
                            <input type="password" name="confirmar_password" id="confirmar_password" class="form-control user-profile-input" required minlength="6">
                        </div>

                       <!-- Botones -->
                        <div class="form-group">
                            <div class="row g-3">
                                <!-- Botón Cancelar -->
                                <div class="col-12 col-md-6">
                                    <a href="index.php?mod=usuario-panel" class="btn user-profile-btn-edit-profile w-100">Cancelar</a>
                                </div>
                                <!-- Botón Actualizar Contraseña -->
                                <div class="col-12 col-md-6">
                                    <button type="submit" class="btn user-profile-btn-change-password w-100">Actualizar Contraseña</button>
                                </div>
                            </div>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
