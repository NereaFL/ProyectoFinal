<?php
$usuarioId = $_SESSION['usuario'];  // Asumiendo que el ID de usuario está en la sesión

// Obtener los datos del usuario de la base de datos
$query = "SELECT nombre, apellidos, numeroTelefono, email FROM usuario WHERE idUsuario = '$usuarioId'";
$result = mysqli_query($conx, $query);
$usuario = mysqli_fetch_assoc($result);

// Verificar si se encontraron los datos del usuario
if (!$usuario) {
    echo "Error al obtener los datos del usuario.";
    exit();
}

// Obtener las entradas de la base de datos para el usuario logueado y la fecha de hoy
$fechaHoy = date('Y-m-d'); // Fecha actual
$queryEntradas = "SELECT c.idEspectaculo, c.fecha, c.hora, c.numeroEntradas, e.nombre, e.descripcion, f.nombre as foto, f.portada, s.numeroSala , e.sala
                  FROM compra c
                  INNER JOIN espectaculo e ON c.idEspectaculo = e.idEspectaculo
                  INNER JOIN foto f ON f.idEspectaculo = c.idEspectaculo
                  INNER JOIN sala s ON s.idSala = e.sala
                  WHERE c.idUsuario = '$usuarioId' AND c.fecha = '$fechaHoy' AND f.portada = 1";

$resultEntradas = mysqli_query($conx, $queryEntradas);
$entradasHoy = mysqli_fetch_all($resultEntradas, MYSQLI_ASSOC);

// Verificar si se encontraron entradas para hoy
$noEntradasHoy = count($entradasHoy) == 0;
?>

<!--================Breadcrumb Area =================-->
<section class="breadcrumb_area">
    <div class="overlay bg-parallax" data-stellar-ratio="0.8" data-stellar-vertical-offset="0" data-background=""></div>
    <div class="container">
        <div class="page-cover text-start">
            <h2 class="page-cover-tittle">Información Personal</h2>
            <ol class="breadcrumb">
                <li><a href="index.html">Inicio</a></li>
                <li class="active">Información Personal</li>
            </ol>
        </div>
    </div>
</section>
<!--================Breadcrumb Area =================-->
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


<div class="container-fluid mt-5 px-5">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card user-profile-card shadow-lg" style="border-radius: 10px;">
                <div class="card-header text-center user-profile-card-header">
                    <h4>Información Personal</h4>
                </div>
                <div class="card-body user-profile-card-body">
                    <form id="userForm" method="POST" action="eliminar_perfil.php">
                        <!-- Fila de Nombre y Apellidos -->
                        <div class="form-row mb-3">
                            <div class="col-md-6">
                                <label for="nombre" class="font-weight-bold">Nombre</label>
                                <input type="text" id="nombre" class="form-control user-profile-input" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="apellidos" class="font-weight-bold">Apellidos</label>
                                <input type="text" id="apellidos" class="form-control user-profile-input" value="<?php echo htmlspecialchars($usuario['apellidos']); ?>" readonly>
                            </div>
                        </div>

                        <!-- Fila de Teléfono y Email -->
                        <div class="form-row mb-3">
                            <div class="col-md-6">
                                <label for="telefono" class="font-weight-bold">Teléfono</label>
                                <input type="text" id="telefono" class="form-control user-profile-input" value="<?php echo htmlspecialchars($usuario['numeroTelefono']); ?>" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="font-weight-bold">Email</label>
                                <input type="email" id="email" class="form-control user-profile-input" value="<?php echo htmlspecialchars($usuario['email']); ?>" readonly>
                            </div>
                        </div>

                        <!-- Botones debajo del formulario -->
                        <div class="form-group mt-4">
                            <div class="row g-2">
                                <div class="col-12 col-md-4">
                                    <a href="index.php?mod=usuario-contrasena" class="btn user-profile-btn-change-password w-100">Cambiar Contraseña</a>
                                </div>
                                <div class="col-12 col-md-4">
                                    <a href="index.php?mod=usuario-editar" class="btn user-profile-btn-edit-profile w-100">Editar Perfil</a>
                                </div>
                                <div class="col-12 col-md-4">
                                    <button type="button" class="btn user-profile-btn-delete-profile w-100" onclick="confirmarEliminacion()">Eliminar Perfil</button>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <!--================ Entradas Compradas Para Hoy =================-->
    <?php if (!$noEntradasHoy): ?>
        <div class="row justify-content-center mt-5">
            <div class="col-md-12">
                <div class="card shadow-lg" style="border-radius: 10px;">
                    <div class="card-header text-center" style="background-color: #007bff; color: white;">
                        <h4>Entradas Compradas para Hoy</h4>
                    </div>
                    <div class="card-body">
                        <?php foreach ($entradasHoy as $entrada): ?>
                            <div class="card mb-3">
                                <div class="row g-0">
                                    <!-- Imagen del espectáculo a la izquierda -->
                                    <div class="col-md-4">
                                        <img style="height: 100%;" src="public/<?php echo $entrada['foto']; ?>" class="img-fluid rounded-start" alt="Imagen del espectáculo">
                                    </div>
                                    <div class="col-md-8">
                                        <div class="card-body">
                                            <h5 class="card-title"><?php echo htmlspecialchars($entrada['nombre']); ?></h5>
                                            <p class="card-text"><small class="text-muted">Fecha: <?php echo date('d/m/Y', strtotime($entrada['fecha'])); ?></small></p>
                                            <p class="card-text"><small class="text-muted">Hora: <?php echo date('H:i', strtotime($entrada['hora'])); ?></small></p>
                                            <p class="card-text"><small class="text-muted">Sala: <?php echo $entrada['numeroSala']; ?></small></p>
                                            <p class="card-text"><strong>Entradas: </strong><?php echo $entrada['numeroEntradas']; ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center" role="alert">
            No tienes entradas compradas para hoy.
        </div>
    <?php endif; ?>
</div>


<!-- Script para la confirmación de eliminación -->
<script>
    function confirmarEliminacion() {
        // Mostrar un cuadro de confirmación antes de proceder
        var confirmacion = confirm("¿Estás seguro de que deseas eliminar tu perfil? Esta acción es irreversible.");

        if (confirmacion) {
            // Si el usuario confirma, enviar el formulario de eliminación
            document.getElementById("userForm").submit();
        }
    }
</script>