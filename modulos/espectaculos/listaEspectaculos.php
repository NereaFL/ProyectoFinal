<?php
// Obtener los filtros
$nombre = isset($_GET['nombre']) ? $_GET['nombre'] : '';
$tipoEspectaculo = isset($_GET['tipoEspectaculo']) ? $_GET['tipoEspectaculo'] : '';
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : '';
$precio_max = isset($_GET['precio_max']) ? $_GET['precio_max'] : '';

// Crear la consulta base
$sql = "
    SELECT 
        e.idEspectaculo,  /* Asegúrate de seleccionar el idEspectaculo */
        e.nombre,
        te.tipoEspectaculo,
        e.fecha_inicio,
        e.fecha_fin,
        e.precio,
        f.nombre AS foto_nombre
    FROM 
        espectaculo e
    JOIN 
        tipoEspectaculo te ON e.tipoEspectaculo = te.idTipoEspectaculo
    LEFT JOIN 
        foto f ON e.idEspectaculo = f.idEspectaculo
    WHERE 
        e.fecha_fin >= CURDATE()";

// Aplicar filtros
if (!empty($nombre)) {
    $sql .= " AND e.nombre LIKE '%$nombre%'";
}

if (!empty($tipoEspectaculo)) {
    $sql .= " AND te.idTipoEspectaculo = '$tipoEspectaculo'";
}

if (!empty($fecha)) {
    $sql .= " AND '$fecha' BETWEEN e.fecha_inicio AND e.fecha_fin";
}

if (!empty($precio_max)) {
    $sql .= " AND e.precio <= '$precio_max'";
}

$sql .= " ORDER BY e.fecha_inicio";

// Ejecutar la consulta
$result = $conx->query($sql);
?>

<!--================Breadcrumb Area =================-->
<section class="breadcrumb_area">
    <div class="overlay bg-parallax" data-stellar-ratio="0.8" data-stellar-vertical-offset="0" data-background=""></div>
    <div class="container">
        <div class="page-cover text-start">
            <h2 class="page-cover-tittle">Espectáculos</h2>
            <ol class="breadcrumb">
                <li><a href="index.html">Inicio</a></li>
                <li class="active">Espectáculos</li>
            </ol>
        </div>
    </div>
</section>
<!--================Breadcrumb Area =================-->

<!--================ Accomodation Area =================-->
<section class="accomodation_area section_gap">
    <div class="container">

        <?php
        // Realizar la consulta para obtener los tipos de espectáculo
        $sql_tipo = "SELECT idTipoEspectaculo, tipoEspectaculo FROM tipoEspectaculo";
        $result_tipo = $conx->query($sql_tipo);
        ?>

<section class="filter_area mb-4">
    <div class="container">
        <div class="card p-4 rounded-3">
            <h4 class="card-title fw-bold mb-4">Filtro</h4>
            <form action="index.php" method="GET" class="filter_form">
                <input type="hidden" name="mod" value="<?php echo $_GET['mod'] ?>">
                
                <!-- Campos del filtro -->
                <div class="row g-3">
                    <!-- Nombre -->
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Buscar por nombre" value="<?php echo $nombre; ?>">
                    </div>

                    <!-- Tipo de espectáculo -->
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label for="tipoEspectaculo" class="form-label">Tipo de Espectáculo</label>
                        <select id="tipoEspectaculo" name="tipoEspectaculo" class="select2 w-100">
                            <option value="">Seleccionar tipo</option>
                            <?php
                            while ($row_tipo = $result_tipo->fetch_assoc()) {
                                $selected = ($tipoEspectaculo == $row_tipo['idTipoEspectaculo']) ? 'selected' : '';
                                echo '<option value="' . $row_tipo['idTipoEspectaculo'] . '" ' . $selected . '>' . $row_tipo['tipoEspectaculo'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Fecha -->
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label for="fecha" class="form-label">Fecha</label>
                        <input type="date" id="fecha" name="fecha" class="form-control" value="<?php echo $fecha; ?>">
                    </div>

                    <!-- Precio máximo -->
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label for="precio_max" class="form-label">Precio máximo</label>
                        <input type="number" id="precio_max" name="precio_max" class="form-control" placeholder="Precio max" value="<?php echo $precio_max; ?>">
                    </div>
                </div>

                <!-- Botones -->
                <div class="row g-3 mt-5">
                    <div class="col-12 col-md-6">
                        <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                    </div>
                    <div class="col-12 col-md-6">
                        <a href="index.php?mod=listaEspectaculos" class="btn btn-secondary w-100">Vaciar Filtro</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>



        <div class="row mb_30">
            <?php
            // Comprobar si hay resultados
            if ($result->num_rows > 0) {
                // Imprimir los resultados en HTML
                while ($row = $result->fetch_assoc()) {
                    // Asumimos que las imágenes están en la carpeta public/images/
                    $imagePath = 'public/' . $row['foto_nombre'];
                    $espectaculo_id = $row['idEspectaculo']; // Obtener el ID del espectáculo

                    echo '<div class="col-lg-3 col-sm-6">';
                    echo '  <div class="accomodation_item text-center">';
                    echo '    <div class="hotel_img" style="height: 200px; overflow: hidden;">';
                    echo '      <a href="index.php?mod=espectaculo-detalles&idEspectaculo=' . $espectaculo_id . '">'; // Enlace a los detalles
                    echo '        <img src="' . $imagePath . '" alt="' . $row['nombre'] . '" style="width: 100%; height: 100%; object-fit: cover;">';
                    echo '      </a>';
                    echo '    </div>';
                    echo '    <a href="index.php?mod=espectaculo-detalles&idEspectaculo=' . $espectaculo_id . '">'; // Enlace a los detalles
                    echo '      <h4 class="sec_h4" style="flex-grow: 1; height: 80px; overflow: hidden; text-overflow: ellipsis; 
                            word-wrap: break-word; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;">'
                        . $row['nombre'] . '</h4>';
                    echo '    </a>';
                    echo '    <p class="text-center">' . $row['tipoEspectaculo'] . '</p>';
                    echo '    <h5>' . number_format($row['precio'], 2) . '€<small>/entrada</small></h5>';
                    echo '  </div>';
                    echo '</div>';
                }
            } else {
                echo "No hay espectáculos disponibles en este momento.";
            }
            ?>
        </div>
    </div>
</section>
<!--================ Accomodation Area =================-->
