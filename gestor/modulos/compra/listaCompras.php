<?php
$query = "
    SELECT 
        c.idCompra,  
        c.numeroEntradas,
        c.importeTotal,
        u.email AS emailUsuario, 
        u.numeroTelefono, 
        te.tipoEspectaculo AS tipoEspectaculo, 
        e.nombre AS nombreEspectaculo, 
        c.fecha, c.hora,
        e.precio
    FROM compra c
    JOIN usuario u ON c.idUsuario = u.idUsuario
    JOIN espectaculo e ON c.idEspectaculo = e.idEspectaculo
    JOIN tipoEspectaculo te ON e.tipoEspectaculo = te.idTipoEspectaculo
";
$result = mysqli_query($conx, $query);

// Función para formatear el precio con dos decimales y el símbolo €
function format_price($price) {
    return number_format($price, 2, ',', '.') . ' €';
}
?>

<div class="page-header flex-wrap">
    <?php
    // Obtener valores de los filtros si están establecidos
    $usuarioFiltro = $_GET['usuario'] ?? '';
    $telefonoFiltro = $_GET['telefono'] ?? '';
    $tipoEspectaculoFiltro = $_GET['tipoEspectaculo'] ?? '';
    $espectaculoFiltro = $_GET['espectaculo'] ?? '';

    // Obtener listado de tipos de espectáculos
    $tiposEspectaculoQuery = "SELECT idTipoEspectaculo, tipoEspectaculo FROM tipoEspectaculo";
    $tiposEspectaculoResult = mysqli_query($conx, $tiposEspectaculoQuery);

    // Obtener listado de espectáculos
    $espectaculosQuery = "SELECT idEspectaculo, nombre FROM espectaculo";
    $espectaculosResult = mysqli_query($conx, $espectaculosQuery);

    $query = "SELECT c.idCompra, c.numeroEntradas, c.importeTotal, u.email AS emailUsuario, c.numeroEntradas, c.fecha, c.hora, u.numeroTelefono, te.tipoEspectaculo, e.nombre AS nombreEspectaculo 
              FROM compra c
              JOIN usuario u ON c.idUsuario = u.idUsuario
              JOIN espectaculo e ON c.idEspectaculo = e.idEspectaculo
              JOIN tipoEspectaculo te ON e.tipoEspectaculo = te.idTipoEspectaculo
              WHERE 1=1";

    // Aplicar filtros dinámicamente
    if (!empty($usuarioFiltro)) {
        $query .= " AND u.email LIKE '%$usuarioFiltro%'";
    }
    if (!empty($telefonoFiltro)) {
        $query .= " AND u.numeroTelefono LIKE '%$telefonoFiltro%'";
    }
    if (!empty($tipoEspectaculoFiltro)) {
        $query .= " AND te.idTipoEspectaculo = '$tipoEspectaculoFiltro'";
    }
    if (!empty($espectaculoFiltro)) {
        $query .= " AND e.idEspectaculo = '$espectaculoFiltro'";
    }

    $result = mysqli_query($conx, $query);
    ?>

    <!-- Formulario de filtros -->
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title text-center">Filtrar Compras</h4>
                <form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <input type="hidden" name="mod" value="listaCompras">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="usuario">Usuario (Email)</label>
                            <input type="text" id="usuario" name="usuario" class="form-control" placeholder="Email Usuario" value="<?php echo htmlspecialchars($usuarioFiltro); ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="telefono">Teléfono</label>
                            <input type="text" id="telefono" name="telefono" class="form-control" placeholder="Teléfono" value="<?php echo htmlspecialchars($telefonoFiltro); ?>">
                        </div>
                        <div class="col-md-3">
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
                        <div class="col-md-3">
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
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">Filtrar</button>
                            <a href="index.php?mod=listaCompras" class="btn btn-secondary">Vaciar</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Tabla de compras -->
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body text-center">
                <h4 class="card-title">Listado de Compras</h4>
                
                <!-- Contenedor con scroll horizontal para la tabla -->
                <div style="overflow-x: auto; position: relative;">
                    <!-- Barra de scroll horizontal superior -->
                    <div style="overflow-x: auto; height: 20px; position: absolute; top: 0; left: 0; right: 0;"></div>

                    <table class="table table-striped table-bordered text-center" style="margin-top: 20px;">
                        <thead>
                            <tr>
                                <th class="text-center">ID</th>
                                <th class="text-center">Email Usuario</th>
                                <th class="text-center">Número Teléfono</th>
                                <th class="text-center">Espectáculo</th>
                                <th class="text-center">Fecha</th>
                                <th class="text-center">Hora</th>
                                <th class="text-center">Personas</th>
                                <th class="text-center">Pago</th>
                                <th class="text-center" colspan="2" style="width: 120px;">Opciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result) > 0) { ?>
                                <?php while ($row = mysqli_fetch_assoc($result)) { 
                                    // Formatear la fecha y la hora
                                    $fechaFormateada = date("d-m-Y", strtotime($row['fecha']));
                                    $horaFormateada = date("H:i", strtotime($row['hora'])); ?>
                                    <tr>
                                        <td class="align-middle"><?php echo $row['idCompra']; ?></td>
                                        <td class="align-middle"><?php echo $row['emailUsuario']; ?></td>
                                        <td class="align-middle"><?php echo $row['numeroTelefono']; ?></td>
                                        <td class="align-middle"><?php echo $row['nombreEspectaculo']; ?></td>
                                        <td class="align-middle"><?php echo $fechaFormateada; ?></td>
                                        <td class="align-middle"><?php echo $horaFormateada; ?></td>
                                        <td class="align-middle"><?php echo $row['numeroEntradas']; ?></td>
                                        <td class="align-middle"><?php echo format_price($row['importeTotal']); ?></td>
                                        <td class="align-middle">
                                            <a href="../pdf.php?idCompra=<?php echo $row['idCompra']; ?>" class="btn btn-primary btn-sm" target="_blank"><i class="mdi mdi-file-pdf"></i></a>
                                        </td>
                                        <td class="align-middle">
                                            <a href="index.php?mod=eliminarCompra&id=<?php echo $row['idCompra']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar esta compra?');"><i class="mdi mdi-delete-forever"></i></a>
                                        </td>

                                    </tr>
                                <?php } ?>
                            <?php } else { ?>
                                <tr>
                                    <td colspan="11" class="align-middle text-center">
                                        <div class="alert alert-danger mb-0">No hay compras registradas en el sistema.</div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>

                    <!-- Barra de scroll horizontal inferior -->
                    <div style="overflow-x: auto; height: 20px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
