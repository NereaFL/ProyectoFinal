<?php
// Obtener valores de los filtros si están establecidos
$rolFiltro = $_GET['rol'] ?? '';
$nombreFiltro = $_GET['nombre'] ?? '';
$apellidosFiltro = $_GET['apellidos'] ?? '';
$telefonoFiltro = $_GET['telefono'] ?? '';
$emailFiltro = $_GET['email'] ?? '';

// Obtener listado de roles
$rolesQuery = "SELECT idRol, tipoRol FROM rol";
$rolesResult = mysqli_query($conx, $rolesQuery);

$query = "SELECT u.idUsuario, r.tipoRol, u.nombre, u.apellidos, u.numeroTelefono, u.email 
          FROM usuario u INNER JOIN rol r ON u.rol = r.idRol 
          WHERE 1=1";

// Aplicar filtros dinámicamente
if (!empty($rolFiltro)) {
    $query .= " AND r.idRol = '$rolFiltro'";
}
if (!empty($nombreFiltro)) {
    $query .= " AND u.nombre LIKE '%$nombreFiltro%'";
}
if (!empty($apellidosFiltro)) {
    $query .= " AND u.apellidos LIKE '%$apellidosFiltro%'";
}
if (!empty($telefonoFiltro)) {
    $query .= " AND u.numeroTelefono LIKE '%$telefonoFiltro%'";
}
if (!empty($emailFiltro)) {
    $query .= " AND u.email LIKE '%$emailFiltro%'";
}

$result = mysqli_query($conx, $query);
?>

<div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-center">Filtrar Usuarios</h4>
            <form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <input type="hidden" name="mod" value="listaUsuarios">
                <div class="row">
                    <div class="col-md-4">
                        <label for="rol">Rol</label>
                        <select id="rol" name="rol" class="form-control select2">
                            <option value=""></option>
                            <?php while ($rol = mysqli_fetch_assoc($rolesResult)) { ?>
                                <option value="<?php echo $rol['idRol']; ?>" <?php echo ($rolFiltro == $rol['idRol']) ? 'selected' : ''; ?>>
                                    <?php echo $rol['tipoRol']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="nombre">Nombre</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Nombre" value="<?php echo htmlspecialchars($nombreFiltro); ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="apellidos">Apellidos</label>
                        <input type="text" id="apellidos" name="apellidos" class="form-control" placeholder="Apellidos" value="<?php echo htmlspecialchars($apellidosFiltro); ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="telefono">Teléfono</label>
                        <input type="text" id="telefono" name="telefono" class="form-control" placeholder="Teléfono" value="<?php echo htmlspecialchars($telefonoFiltro); ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="email">Email</label>
                        <input type="text" id="email" name="email" class="form-control" placeholder="Email" value="<?php echo htmlspecialchars($emailFiltro); ?>">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary btn-block">Filtrar</button>
                        <a href="index.php?mod=listaUsuarios" class="btn btn-secondary btn-block ml-2">Vaciar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="page-header flex-wrap">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body text-center">
                <h4 class="card-title">Listado de Usuarios</h4>
                <a href="index.php?mod=crearUsuario" class="btn btn-success mb-3">Crear Usuario</a>

                <div style="overflow-x: auto; position: relative;">
                    <!-- Barra de scroll horizontal superior -->
                    <div style="overflow-x: auto; height: 20px; position: absolute; top: 0; left: 0; right: 0;"></div>

                    <table class="table table-striped text-center" style="margin-top: 20px;">
                        <thead>
                            <tr>
                                <th class="text-center">ID</th>
                                <th class="text-center">Rol</th>
                                <th class="text-center">Nombre</th>
                                <th class="text-center">Apellidos</th>
                                <th class="text-center">Teléfono</th>
                                <th class="text-center">Email</th>
                                <th class="text-center" colspan="2">Opciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                                    <tr>
                                        <td class="align-middle"><?php echo $row['idUsuario']; ?></td>
                                        <td class="align-middle"><?php echo $row['tipoRol']; ?></td>
                                        <td class="align-middle"><?php echo $row['nombre']; ?></td>
                                        <td class="align-middle"><?php echo $row['apellidos']; ?></td>
                                        <td class="align-middle"><?php echo $row['numeroTelefono']; ?></td>
                                        <td class="align-middle"><?php echo $row['email']; ?></td>
                                        <td class="align-middle">
                                            <a href="index.php?mod=editarUsuario&id=<?php echo $row['idUsuario']; ?>" class="btn btn-warning btn-sm"><i class="mdi mdi-pencil"></i></a>
                                        </td>
                                        <td class="align-middle">
                                            <a href="index.php?mod=eliminarUsuario&id=<?php echo $row['idUsuario']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este usuario?');"><i class="mdi mdi-delete-forever"></i></a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php else: ?>
                                <tr>
                                    <td class="text-center text-danger" colspan="8">
                                        No se encontraron usuarios con los filtros aplicados.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <!-- Barra de scroll horizontal inferior -->
                    <div style="overflow-x: auto; height: 20px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
