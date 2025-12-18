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

// Redirección si el usuario ya inició sesión
if (isset($_SESSION['usuario'])) {
    echo "
    <script>
        window.location.href = 'index.php?mod=espectaculo-fecha&idEspectaculo=$idEspectaculo';
    </script>
    ";
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
            <p class="espectaculo-precio">Precio: <?php echo number_format($espectaculo['precio'], 2); ?>€/entrada</p>
        </div>
    </div>
</section>

<!-- Información secundaria -->
<section class="espectaculo-info-cards">
    <div class="container">
        <div class="row">
            <!-- Mensaje explicativo en texto negro -->
            <div class="col-12">
                <p style="color: black; font-size: 18px; text-align: center; margin-bottom: 20px;">
                    Para seguir con el proceso de compra necesita haber iniciado sesión
                </p>
            </div>

            <!-- Card para Iniciar sesión -->
            <div class="col-md-6">
                <div class="info-card fade-in-up info-card-green">
                    <h4>
                        <a href="login.php?idEspectaculo=<?php echo $idEspectaculo; ?>" style="text-decoration: none; color: #28a745;">Iniciar sesión</a>
                    </h4>
                    <a href="login.php?idEspectaculo=<?php echo $idEspectaculo; ?>" style="color: #28a745; font-size: 24px;">➡️</a>
                </div>
            </div>

            <!-- Card para Registrarse -->
            <div class="col-md-6">
                <div class="info-card fade-in-up info-card-blue">
                    <h4>
                        <a href="registro.php?idEspectaculo=<?php echo $idEspectaculo; ?>" style="text-decoration: none; color: #007bff;">Registrarse</a>
                    </h4>
                    <a href="registro.php?idEspectaculo=<?php echo $idEspectaculo; ?>" style="color: #007bff; font-size: 24px;">➡️</a>
                </div>
            </div>

        </div>
    </div>
</section>