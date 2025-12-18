<?php
$usuarioId = $_SESSION['usuario'];  // Asumiendo que el ID de usuario está en la sesión

// Obtener los tipos de espectáculos y espectáculos disponibles
$tiposEspectaculoQuery = "SELECT idTipoEspectaculo, tipoEspectaculo FROM tipoEspectaculo";
$tiposEspectaculoResult = mysqli_query($conx, $tiposEspectaculoQuery);

$espectaculosQuery = "SELECT idEspectaculo, nombre FROM espectaculo";
$espectaculosResult = mysqli_query($conx, $espectaculosQuery);

// Obtener filtros de la URL
$telefonoFiltro = $_GET['telefono'] ?? '';
$tipoEspectaculoFiltro = $_GET['tipoEspectaculo'] ?? '';
$espectaculoFiltro = $_GET['espectaculo'] ?? '';
$fechaFiltro = $_GET['fecha'] ?? '';

// Obtener la fecha actual
$fechaActual = date('Y-m-d');

// Consulta de compras con filtros
$query = "
    SELECT c.idCompra, c.numeroEntradas, c.importeTotal, u.email AS emailUsuario, u.numeroTelefono, te.tipoEspectaculo, 
    e.nombre AS nombreEspectaculo, c.fecha, c.hora, f.nombre as foto, f.portada
    FROM compra c
    JOIN usuario u ON c.idUsuario = u.idUsuario
    JOIN espectaculo e ON c.idEspectaculo = e.idEspectaculo
    JOIN tipoEspectaculo te ON e.tipoEspectaculo = te.idTipoEspectaculo
    JOIN foto f ON c.idEspectaculo = f.idEspectaculo
    WHERE c.idUsuario = '$usuarioId' AND f.portada = 1 AND c.fecha >= '$fechaActual'";

// Aplicar filtros dinámicamente
if (!empty($tipoEspectaculoFiltro)) {
    $query .= " AND te.idTipoEspectaculo = '$tipoEspectaculoFiltro'";
}
if (!empty($espectaculoFiltro)) {
    $query .= " AND e.idEspectaculo = '$espectaculoFiltro'";
}
if (!empty($fechaFiltro)) {
    $query .= " AND c.fecha = '$fechaFiltro'";
}

$query .= " ORDER BY c.fecha ASC, c.hora ASC";


$result = mysqli_query($conx, $query);

function format_price($price) {
    // Asumiendo que se desea mostrar el precio en formato monetario, por ejemplo, en dólares.
    return number_format($price, 2, '.', ',') . '€';
}
?>

<!--================Breadcrumb Area =================-->
<section class="breadcrumb_area">
    <div class="overlay bg-parallax" data-stellar-ratio="0.8" data-stellar-vertical-offset="0" data-background=""></div>
    <div class="container">
        <div class="page-cover text-start">
            <h2 class="page-cover-tittle">Tus Compras</h2>
            <ol class="breadcrumb">
                <li><a href="index.html">Inicio</a></li>
                <li class="active">Tus Compras</li>
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


<!--================ Filtros =================-->
<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-sm" style="border-radius: 10px;">
                <div class="card-header text-center" style="background-color: #007bff; color: white;">
                    <h4>Filtrar Compras</h4>
                </div>
                <div class="card-body">
                    <form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        <input type="hidden" name="mod" value="usuario-espectaculos">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="tipoEspectaculo">Tipo de Espectáculo</label>
                                <select id="tipoEspectaculo" name="tipoEspectaculo" class="form-control select2">
                                    <option value=""></option>
                                    <?php while ($tipo = mysqli_fetch_assoc($tiposEspectaculoResult)) { ?>
                                        <option value="<?php echo $tipo['idTipoEspectaculo']; ?>" <?php echo ($tipoEspectaculoFiltro == $tipo['idTipoEspectaculo']) ? 'selected' : ''; ?>>
                                            <?php echo $tipo['tipoEspectaculo']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="espectaculo">Espectáculo</label>
                                <select id="espectaculo" name="espectaculo" class="form-control select2">
                                    <option value=""></option>
                                    <?php while ($espectaculo = mysqli_fetch_assoc($espectaculosResult)) { ?>
                                        <option value="<?php echo $espectaculo['idEspectaculo']; ?>" <?php echo ($espectaculoFiltro == $espectaculo['idEspectaculo']) ? 'selected' : ''; ?>>
                                            <?php echo $espectaculo['nombre']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="fecha">Fecha</label>
                                <input type="date" id="fecha" name="fecha" class="form-control" value="<?php echo htmlspecialchars($fechaFiltro); ?>">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-primary">Filtrar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!--================ Filtros =================-->

<!--================ Compras Realizadas =================-->
<?php if (mysqli_num_rows($result) > 0): ?>
    <div class="container my-4">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow-lg" style="border-radius: 10px;">
                    <div class="card-header text-center" style="background-color: #007bff; color: white;">
                        <h4>Tus Compras</h4>
                    </div>
                    <div class="card-body">
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <div class="card mb-3">
                                <div class="row g-0">
                                    <div class="col-md-4">
                                        <img style="height: 100%;" src="public/<?php echo $row['foto']; ?>" class="img-fluid rounded-start" alt="Imagen del espectáculo">
                                    </div>
                                    <div class="col-md-8">
                                        <div class="card-body">
                                            <h5 class="card-title"><?php echo htmlspecialchars($row['nombreEspectaculo']); ?></h5>
                                            <p class="card-text"><small class="text-muted">Fecha: <?php echo date('d/m/Y', strtotime($row['fecha'])); ?></small></p>
                                            <p class="card-text"><small class="text-muted">Hora: <?php echo date('H:i', strtotime($row['hora'])); ?></small></p>
                                            <p class="card-text"><strong>Entradas: </strong><?php echo $row['numeroEntradas']; ?></p>
                                            <p class="card-text"><strong>Total: </strong><?php echo format_price($row['importeTotal']); ?></p>

                                            <!-- Botón para generar PDF -->
                                            <a href="pdf.php?idCompra=<?php echo $row['idCompra']; ?>" class="btn btn-primary" target="_blank">Generar PDF</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-info text-center" role="alert">
        No tienes compras registradas con los filtros seleccionados.
    </div>
<?php endif; ?>
