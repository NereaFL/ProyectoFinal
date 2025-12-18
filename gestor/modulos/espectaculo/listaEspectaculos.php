<?php
$query = "
SELECT 
    e.idEspectaculo,
    e.nombre AS nombreEspectaculo,
    te.tipoEspectaculo AS tipoEspectaculo,
    s.numeroSala AS numeroSala,
    e.fecha_inicio,
    e.fecha_fin,
    e.horarios,
    e.precio
FROM espectaculo e
JOIN tipoEspectaculo te ON e.tipoEspectaculo = te.idTipoEspectaculo
JOIN sala s ON e.sala = s.idSala
ORDER BY 
    CASE 
        WHEN e.fecha_inicio >= CURDATE() THEN 0  -- Espectáculos futuros o actuales tienen prioridad
        ELSE 1  -- Espectáculos pasados se ordenan después
    END,
    e.fecha_inicio ASC,  -- Ordenar fechas de inicio en orden ascendente (más reciente primero)
    e.precio ASC         -- Ordenar por precio en caso de empate de fecha
";

$result = mysqli_query($conx, $query);

// Función para formatear el precio con dos decimales y el símbolo €
function format_price($price)
{
    return number_format($price, 2, ',', '.') . ' €';
}

// Función para formatear fechas en dd/mm/yyyy
function format_date($date)
{
    return date('d/m/Y', strtotime($date));
}
?>

<div class="page-header flex-wrap">
    <?php
    // Obtener valores de los filtros si están establecidos
    $tipoEspectaculoFiltro = $_GET['tipoEspectaculo'] ?? '';
    $salaFiltro = $_GET['sala'] ?? '';
    $nombreFiltro = $_GET['nombre'] ?? '';
    $fechaFiltro = $_GET['fecha'] ?? '';

    // Obtener listado de tipos de espectáculos
    $tipoEspectaculoQuery = "SELECT idTipoEspectaculo, tipoEspectaculo FROM tipoEspectaculo";
    $tipoEspectaculoResult = mysqli_query($conx, $tipoEspectaculoQuery);

    // Obtener listado de salas
    $salaQuery = "SELECT idSala, numeroSala FROM sala";
    $salaResult = mysqli_query($conx, $salaQuery);

    $query = "SELECT e.idEspectaculo, e.nombre AS nombreEspectaculo, te.tipoEspectaculo, s.numeroSala, e.fecha_inicio, e.fecha_fin, e.horarios, e.precio 
          FROM espectaculo e 
          JOIN tipoEspectaculo te ON e.tipoEspectaculo = te.idTipoEspectaculo 
          JOIN sala s ON e.sala = s.idSala 
          WHERE 1=1";

    // Aplicar filtros dinámicamente
    if (!empty($tipoEspectaculoFiltro)) {
        $query .= " AND te.idTipoEspectaculo = '$tipoEspectaculoFiltro'";
    }
    if (!empty($salaFiltro)) {
        $query .= " AND s.idSala = '$salaFiltro'";
    }
    if (!empty($nombreFiltro)) {
        $query .= " AND e.nombre LIKE '%$nombreFiltro%'";
    }
    if (!empty($fechaFiltro)) {
        $query .= " AND ('$fechaFiltro' BETWEEN e.fecha_inicio AND e.fecha_fin)";
    }

    $result = mysqli_query($conx, $query);
    ?>

    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title text-center">Filtrar Espectáculos</h4>
                <form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <input type="hidden" name="mod" value="listaEspectaculos">
                    <div class="row align-items-end">
                        <div class="col-md-3">
                            <label for="tipoEspectaculo">Tipo de Espectáculo</label>
                            <select id="tipoEspectaculo" name="tipoEspectaculo" class="form-control">
                                <option value=""></option>
                                <?php while ($tipo = mysqli_fetch_assoc($tipoEspectaculoResult)) { ?>
                                    <option value="<?php echo $tipo['idTipoEspectaculo']; ?>" <?php echo ($tipoEspectaculoFiltro == $tipo['idTipoEspectaculo']) ? 'selected' : ''; ?>>
                                        <?php echo $tipo['tipoEspectaculo']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="sala">Sala</label>
                            <select id="sala" name="sala" class="form-control">
                                <option value=""></option>
                                <?php while ($sala = mysqli_fetch_assoc($salaResult)) { ?>
                                    <option value="<?php echo $sala['idSala']; ?>" <?php echo ($salaFiltro == $sala['idSala']) ? 'selected' : ''; ?>>
                                        <?php echo $sala['numeroSala']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="nombre">Nombre</label>
                            <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Nombre" value="<?php echo htmlspecialchars($nombreFiltro); ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="fecha">Fecha</label>
                            <input type="date" id="fecha" name="fecha" class="form-control" value="<?php echo htmlspecialchars($fechaFiltro); ?>">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary btn-block">Filtrar</button>
                            <a href="index.php?mod=listaEspectaculos" class="btn btn-secondary btn-block ml-2">Vaciar</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body text-center">
                <h4 class="card-title">Listado de Espectáculos</h4>
                <a href="index.php?mod=crearEspectaculo" class="btn btn-success mb-3">Crear Espectáculo</a>
                <div style="overflow-x: auto; position: relative;">
                    <!-- Scroll horizontal arriba -->
                    <div class="scroll-bar-top" style="overflow-x: auto; height: 20px; position: absolute; top: 0; left: 0; right: 0;"></div>
                    <table class="table table-striped text-center" style="margin-top: 20px;">
                        <thead>
                            <tr>
                                <th class="text-center">ID</th>
                                <th class="text-center">Nombre</th>
                                <th class="text-center">Tipo</th>
                                <th class="text-center">Sala</th>
                                <th class="text-center">Fecha Inicio</th>
                                <th class="text-center">Fecha Fin</th>
                                <th class="text-center">Horarios</th>
                                <th class="text-center">Precio</th>
                                <th class="text-center" colspan="2" style="width: 120px;">Opciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result) > 0) { ?>
                                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                                    <tr>
                                        <td><?php echo $row['idEspectaculo']; ?></td>
                                        <td><?php echo $row['nombreEspectaculo']; ?></td>
                                        <td><?php echo $row['tipoEspectaculo']; ?></td>
                                        <td><?php echo $row['numeroSala']; ?></td>
                                        <td><?php echo format_date($row['fecha_inicio']); ?></td>
                                        <td><?php echo !empty($row['fecha_fin']) ? format_date($row['fecha_fin']) : 'No existe'; ?></td>
                                        <td><?php echo htmlspecialchars($row['horarios']); ?></td>
                                        <td><?php echo format_price($row['precio']); ?></td>
                                        <td>
                                            <a href="index.php?mod=listaFotos&id=<?php echo $row['idEspectaculo']; ?>" class="btn btn-primary btn-sm"><i class="mdi mdi-image-multiple"></i></a>
                                        </td>
                                        <td>
                                            <a href="index.php?mod=editarEspectaculo&id=<?php echo $row['idEspectaculo']; ?>" class="btn btn-warning btn-sm"><i class="mdi mdi-pencil"></i></a>
                                        </td>
                                        <td>
                                            <a href="index.php?mod=eliminarEspectaculo&id=<?php echo $row['idEspectaculo']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este espectáculo?');"><i class="mdi mdi-delete-forever"></i></a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php } else { ?>
                                <tr>
                                    <td colspan="10" class="text-center">
                                        <div class="alert alert-danger mb-0">No hay espectáculos registrados en el sistema.</div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <!-- Scroll horizontal abajo -->
                    <div class="scroll-bar-bottom" style="overflow-x: auto; height: 20px;"></div>
                </div>


            </div>
        </div>
    </div>