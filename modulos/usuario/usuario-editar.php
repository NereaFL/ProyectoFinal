<?php
session_start(); // Asegúrate de tener esto arriba si no está
$usuarioId = $_SESSION['usuario']; // ID del usuario en sesión

// Mostrar errores (desactiva en producción)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Validar conexión
if (!$conx) {
    die("Error en la conexión a la base de datos: " . mysqli_connect_error());
}

// Si se envió el formulario de edición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = mysqli_real_escape_string($conx, $_POST['nombre']);
    $apellidos = mysqli_real_escape_string($conx, $_POST['apellidos']);
    $telefono = mysqli_real_escape_string($conx, $_POST['telefono']);
    $email = mysqli_real_escape_string($conx, $_POST['email']);

    // Comprobar si el email ya está en uso por otro usuario
    $queryCheckEmail = "SELECT idUsuario FROM usuario WHERE email = '$email' AND idUsuario != '$usuarioId'";
    $resultCheck = mysqli_query($conx, $queryCheckEmail);

    if (mysqli_num_rows($resultCheck) > 0) {
        $error = "Este email ya está en uso por otro usuario.";
    } else {
        // Actualizar información del usuario
        $queryUpdate = "UPDATE usuario SET nombre = '$nombre', apellidos = '$apellidos', numeroTelefono = '$telefono', email = '$email' WHERE idUsuario = '$usuarioId'";
        if (mysqli_query($conx, $queryUpdate)) {
            $success = "Datos actualizados correctamente.";
        } else {
            $error = "Error al actualizar los datos: " . mysqli_error($conx);
        }
    }
}

// Obtener datos actualizados
$query = "SELECT nombre, apellidos, numeroTelefono, email FROM usuario WHERE idUsuario = '$usuarioId'";
$result = mysqli_query($conx, $query);
$usuario = mysqli_fetch_assoc($result);

// Verificación por si falla el SELECT
if (!$usuario) {
    echo "Error al obtener los datos del usuario.";
    exit();
}
?>

<!--================ Breadcrumb =================-->
<section class="breadcrumb_area">
    <div class="overlay bg-parallax"></div>
    <div class="container">
        <div class="page-cover text-start">
            <h2 class="page-cover-tittle">Editar Información Personal</h2>
            <ol class="breadcrumb">
                <li><a href="index.html">Inicio</a></li>
                <li class="active">Editar Perfil</li>
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

<!--================ Formulario de Edición =================-->
<div class="container-fluid mt-5 px-5">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-lg" style="border-radius: 10px;">
                <div class="card-header text-center bg-primary text-white">
                    <h4>Editar Información Personal</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <?php if (isset($success)): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <!-- Nombre y Apellidos -->
                        <div class="form-row mb-3">
                            <div class="col-md-6">
                                <label for="nombre" class="font-weight-bold">Nombre</label>
                                <input type="text" name="nombre" id="nombre" class="form-control" required value="<?php echo htmlspecialchars($usuario['nombre']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="apellidos" class="font-weight-bold">Apellidos</label>
                                <input type="text" name="apellidos" id="apellidos" class="form-control" required value="<?php echo htmlspecialchars($usuario['apellidos']); ?>">
                            </div>
                        </div>

                        <!-- Teléfono y Email -->
                        <div class="form-row mb-3">
                            <div class="col-md-6">
                                <label for="telefono" class="font-weight-bold">Teléfono</label>
                                <input type="text" name="telefono" id="telefono" class="form-control" required value="<?php echo htmlspecialchars($usuario['numeroTelefono']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="font-weight-bold">Email</label>
                                <input type="email" name="email" id="email" class="form-control" required value="<?php echo htmlspecialchars($usuario['email']); ?>">
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="form-group mt-4">
                            <div class="row g-3">
                                <!-- Botón Cancelar -->
                                <div class="col-12 col-md-6">
                                    <a href="index.php?mod=usuario-panel" class="btn btn-secondary w-100">Cancelar</a>
                                </div>
                                <!-- Botón Guardar Cambios -->
                                <div class="col-12 col-md-6">
                                    <button type="submit" class="btn btn-success w-100">Guardar Cambios</button>
                                </div>
                            </div>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
