<?php

$idEspectaculo = isset($_GET['idEspectaculo']) ? (int)$_GET['idEspectaculo'] : 0;

if ($idEspectaculo > 0) {
    $sql = "
        SELECT 
            e.idEspectaculo,
            e.nombre,
            e.fecha_inicio,
            e.fecha_fin,
            e.horarios,
            te.tipoEspectaculo,
            s.capacidad,
            s.numeroSala
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
                <li class="active">Detalles de Espectáculo</li>
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
        </div>
    </div>
</section>

<!-- Información secundaria -->
<section class="espectaculo-info-cards">
    <div class="container">
        <div class="row">
            <!-- Calendario -->
            <div class="col-md-6">
                <div class="info-card fade-in-up">
                    <h4>📅 Selecciona una fecha</h4>
                    <!-- Campos ocultos -->
                    <input type="hidden" id="idEspectaculo" value="<?php echo $idEspectaculo; ?>">
                    <input type="hidden" id="fechaInicio" value="<?php echo htmlspecialchars($espectaculo['fecha_inicio']); ?>">
                    <input type="hidden" id="fechaFin" value="<?php echo htmlspecialchars($espectaculo['fecha_fin']); ?>">

                    <!-- Calendario -->
                    <div id="datepicker"></div>
                </div>
            </div>

            <!-- Horarios disponibles -->
            <div class="col-md-6">
                <div class="info-card fade-in-up">
                    <h4>🕒 Horarios disponibles</h4>
                    <ul style="list-style: none;"  id="horarios-list"></ul>
                    <!-- Botón "Siguiente" -->
                    <button id="botonSiguiente" disabled style="margin-top: 20px; padding: 10px 20px; background-color: gray; color: white; border: none; border-radius: 5px; cursor: not-allowed;">
                        Siguiente
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

