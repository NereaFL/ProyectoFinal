<?php
$idEspectaculo = isset($_GET['idEspectaculo']) ? (int)$_GET['idEspectaculo'] : 0;

if ($idEspectaculo > 0) {
    $sql = "
        SELECT 
            e.idEspectaculo,
            e.nombre,
            e.descripcion,
            e.fecha_inicio,
            e.fecha_fin,
            e.horarios,
            e.duracion,
            e.precio,
            te.tipoEspectaculo,
            s.numeroSala,
            s.capacidad
        FROM espectaculo e
        JOIN tipoEspectaculo te ON e.tipoEspectaculo = te.idTipoEspectaculo
        JOIN sala s ON e.sala = s.idSala
        WHERE e.idEspectaculo = $idEspectaculo
    ";

    $sql_result = mysqli_query($conx, $sql);

    if ($sql_result && mysqli_num_rows($sql_result) > 0) {
        $espectaculo = mysqli_fetch_assoc($sql_result);
    } else {
        echo "Espectáculo no encontrado.";
        exit;
    }

    $sql_imagen = "
        SELECT f.portada, f.nombre
        FROM foto f
        WHERE f.idEspectaculo = $idEspectaculo AND f.portada = 1
        LIMIT 1
    ";
    $result_imagen = mysqli_query($conx, $sql_imagen);

    $imagePath = 'public/default.jpg';

    if ($result_imagen && mysqli_num_rows($result_imagen) > 0) {
        $imagen = mysqli_fetch_assoc($result_imagen);
        $imagePath = 'public/' . $imagen['nombre'];
    }
} else {
    echo "ID de espectáculo no válido.";
    exit;
}
?>

<!--================Breadcrumb Area =================-->
<section class="breadcrumb_area">
    <div class="overlay bg-parallax" data-stellar-ratio="0.8" data-stellar-vertical-offset="0" data-background=""></div>
    <div class="container">
        <div class="page-cover text-start">
            <h2 class="page-cover-tittle">Detalles de Espectáculo</h2>
            <ol class="breadcrumb">
                <li><a href="index.html">Inicio</a></li>
                <li class="active">Deralles de Espectáculo</li>
            </ol>
        </div>
    </div>
</section>
<!--================Breadcrumb Area =================-->

<!-- Card principal -->
<section class="espectaculo-card">
    <div class="card-espectaculo fade-in-up">
        <div class="card-img" style="background-image: url('<?php echo $imagePath; ?>');"></div>
        <div class="card-content">
            <h2 class="espectaculo-nombre"><?php echo htmlspecialchars($espectaculo['nombre']); ?></h2>
            <p class="espectaculo-tipo"><?php echo htmlspecialchars($espectaculo['tipoEspectaculo']); ?></p>
            <p class="espectaculo-precio">Precio: <?php echo number_format($espectaculo['precio'], 2); ?>€/entrada</p>
            <a href="index.php?mod=compraEspectaculo&idEspectaculo=<?php echo $idEspectaculo; ?>" class="btn-comprar fade-in-up-delay">🎟️ Comprar Entradas</a>
        </div>
    </div>
</section>

<!-- Información secundaria -->
<section class="espectaculo-info-cards">
    <div class="container">
        <div class="row">
            <!-- Lado izquierdo -->
            <div class="col-md-6">
                <div class="info-card fade-in-up">
                    <h4>📝 Descripción</h4>
                    <p><?php echo nl2br(htmlspecialchars($espectaculo['descripcion'])); ?></p>
                </div>
                <div class="info-card fade-in-up">
                    <h4>⏱️ Duración</h4>
                    <p><?php echo htmlspecialchars($espectaculo['duracion']); ?> minutos</p>
                </div>
                <div class="info-card fade-in-up">
                    <h4>🏛️ Sala</h4>
                    <p>Sala <?php echo htmlspecialchars($espectaculo['numeroSala']); ?> (Capacidad: <?php echo htmlspecialchars($espectaculo['capacidad']); ?> personas)</p>
                </div>
            </div>

            <!-- Lado derecho -->
            <div class="col-md-6">
                <div class="info-card fade-in-up">
                    <h4>🕒 Horarios</h4>
                    <?php
                    $horarios_array = explode(',', $espectaculo['horarios']);
                    ?>

                    <ul class="horarios-list">
                        <?php foreach ($horarios_array as $hora): ?>
                            <li><span>🕒</span> <?php echo htmlspecialchars(trim($hora)); ?></li>
                        <?php endforeach; ?>
                    </ul>

                </div>
                <!-- Campo para el calendario -->
                <div class="info-card fade-in-up">
                    <h4>📅 Calendario</h4>
                    <!-- Campos ocultos para las fechas -->
                    <input type="hidden" id="fechaInicio" value="<?php echo htmlspecialchars($espectaculo['fecha_inicio']); ?>">
                    <input type="hidden" id="fechaFin" value="<?php echo htmlspecialchars($espectaculo['fecha_fin']); ?>">

                    <!-- Calendario -->
                    <div id="datepicker"></div>

                </div>

            </div>
        </div>
    </div>
</section>
