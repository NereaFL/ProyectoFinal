<?php
// Obtener valores de filtros
$nombreFiltro = $_GET['nombre'] ?? '';
$emailFiltro = $_GET['email'] ?? '';
$fechaFiltro = $_GET['fecha'] ?? '';
$estadoFiltro = $_GET['estado'] ?? '';

// Obtener estados
$estadoQuery = "SELECT idEstado, nombreEstado FROM estado";
$estadoResult = mysqli_query($conx, $estadoQuery);

// Query base
$query = "SELECT c.idContacto, c.nombre AS nombreCliente, c.email AS emailCliente, c.mensaje, c.fechaCreacion, e.nombreEstado, c.asunto
          FROM contacto c 
          JOIN estado e ON c.idEstado = e.idEstado 
          WHERE 1=1";

// Filtros dinámicos
if (!empty($nombreFiltro)) {
    $query .= " AND c.nombre LIKE '%$nombreFiltro%'";
}
if (!empty($emailFiltro)) {
    $query .= " AND c.email LIKE '%$emailFiltro%'";
}
if (!empty($fechaFiltro)) {
    $query .= " AND DATE(c.fechaCreacion) = '$fechaFiltro'";
}
if (!empty($estadoFiltro)) {
    $query .= " AND c.idEstado = '$estadoFiltro'";
}

$query .= " ORDER BY c.fechaCreacion DESC";

$result = mysqli_query($conx, $query);

// Función para formatear fecha
function format_date($date) {
    return date('d/m/Y H:i', strtotime($date));
}
?>

<!-- Filtros -->
<div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-center">Filtrar Mensajes</h4>
            <form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <input type="hidden" name="mod" value="contacto">
                <div class="row align-items-end">
                    <div class="col-md-3">
                        <label for="nombre">Nombre Cliente</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" value="<?php echo htmlspecialchars($nombreFiltro); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="email">Email Cliente</label>
                        <input type="text" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($emailFiltro); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="fecha">Fecha</label>
                        <input type="date" id="fecha" name="fecha" class="form-control" value="<?php echo htmlspecialchars($fechaFiltro); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="estado">Estado</label>
                        <select id="estado" name="estado" class="form-control">
                            <option value=""></option>
                            <?php while ($estado = mysqli_fetch_assoc($estadoResult)) { ?>
                                <option value="<?php echo $estado['idEstado']; ?>" <?php echo ($estadoFiltro == $estado['idEstado']) ? 'selected' : ''; ?>>
                                    <?php echo $estado['nombreEstado']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary btn-block">Filtrar</button>
                        <a href="index.php?mod=contacto" class="btn btn-secondary btn-block ml-2">Vaciar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Listado -->
<div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body text-center">
            <h4 class="card-title">Listado de Mensajes</h4>
            <div style="overflow-x: auto;">
                <table class="table table-striped text-center">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Asunto</th> <!-- Nueva columna -->
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Opciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0) { ?>
                            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                                <tr>
                                    <td><?php echo $row['idContacto']; ?></td>
                                    <td><?php echo htmlspecialchars($row['nombreCliente']); ?></td>
                                    <td><?php echo htmlspecialchars($row['emailCliente']); ?></td>
                                    <td><?php echo htmlspecialchars($row['asunto']); ?></td> <!-- Mostrar asunto -->
                                    <td><?php echo format_date($row['fechaCreacion']); ?></td>
                                    <td><?php echo $row['nombreEstado']; ?></td>
                                    <td>
                                        <a href="index.php?mod=verMensaje&id=<?php echo $row['idContacto']; ?>" class="btn btn-info btn-sm" title="Ver Mensaje">
                                            <i class="mdi mdi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="7" class="text-center">
                                    <div class="alert alert-danger mb-0">No hay contactos registrados con los filtros aplicados.</div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
