<!--================Banner Area =================-->
<section class="banner_area">
    <div class="booking_table d_flex align-items-center">
        <div class="overlay bg-parallax" data-stellar-ratio="0.9" data-stellar-vertical-offset="0" data-background="">
        </div>
        <div class="container">
            <div class="banner_content text-center">
                <h6>Bienvenido a un mundo de emociones</h6>
                <h2>Compra tus entradas fácilmente</h2>
                <p>Descubre espectáculos únicos de teatro, musicales, monólogos, danza, ópera, teatro infantil, etre otros muchos<br> Reserva ahora y vive momentos inolvidables.</p>
                <a href="index.php?mod=listaEspectaculos" class="btn theme_btn button_hover">Explorar espectáculos</a>
            </div>
        </div>
    </div>
</section>

<!--================Banner Area =================-->

<?php

// Ejecutar la consulta para obtener los 4 espectáculos más vendidos con sus imágenes
$sql = "
    SELECT 
        e.nombre,
        e.idEspectaculo,
        te.tipoEspectaculo,
        e.fecha_inicio,
        e.fecha_fin,
        e.precio,
        SUM(c.numeroEntradas) AS cantidad_vendida,
        f.nombre AS foto_nombre
    FROM 
        espectaculo e
    JOIN 
        tipoEspectaculo te ON e.tipoEspectaculo = te.idTipoEspectaculo
    LEFT JOIN 
        compra c ON e.idEspectaculo = c.idEspectaculo
    LEFT JOIN 
        foto f ON e.idEspectaculo = f.idEspectaculo
    WHERE 
        e.fecha_fin >= CURDATE()
    GROUP BY 
        e.idEspectaculo, te.tipoEspectaculo, e.nombre, e.precio, f.nombre
    ORDER BY 
        cantidad_vendida DESC
    LIMIT 4;
";

$result = $conx->query($sql);

?>

<!--================ Accomodation Area  =================-->
<section class="accomodation_area section_gap">
    <div class="container">
        <div class="section_title text-center">
            <h2 class="title_color">Espectáculos Más Vendidos</h2>
            <p>En el mundo del teatro, cada función es única y llena de emociones. Descubre los espectáculos más populares que están cautivando al público y no te pierdas la oportunidad de vivir una experiencia inolvidable.</p>
        </div>
        <div class="row mb_30" style="padding: 10px 0;">
            <!-- Contenedor que permite el desplazamiento horizontal -->
            <div style="display: flex; overflow-x: auto; gap: 10px; width: 100%; align-items: stretch;">
                
                <?php
                // Comprobar si hay resultados
                if ($result->num_rows > 0) {
                    // Imprimir los resultados en HTML
                    while ($row = $result->fetch_assoc()) {
                        $imagePath = 'public/' . $row['foto_nombre'];

                        echo '<div class="accomodation_item text-center" style="
                        flex-shrink: 0;
                        min-width: 220px;
                        max-width: 300px;
                        width: 90%;
                        display: flex;
                        flex-direction: column;
                        justify-content: space-between;
                        scroll-snap-align: start;
                    ">';
                    
                        echo '  <div class="hotel_img" style="height: 200px; overflow: hidden;">';
                        echo '      <img src="' . $imagePath . '" alt="' . $row['nombre'] . '" style="width: 100%; height: 100%; object-fit: cover;">'; // Aseguramos que la imagen se ajuste
                        echo '  </div>';
                        echo '  <a href="index.php?mod=espectaculo-detalles&idEspectaculo=' . $row['idEspectaculo'] . '">';
                            echo '      <h4 class="sec_h4" style="flex-grow: 1; height: 80px; overflow: hidden; text-overflow: ellipsis; word-wrap: break-word; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;">' . $row['nombre'] . '</h4>';
                            echo '  </a>';

                        echo '  <p class="text-center" style="margin-bottom: 0;">' . $row['tipoEspectaculo'] . '</p>';
                        echo '  <h5 style="margin-top: auto;">' . number_format($row['precio'], 2) . '€<small>/entrada</small></h5>';
                        echo '</div>';
                    }
                } else {
                    echo "No hay espectáculos activos o no vendidos";
                }
                ?>
            </div>
        </div>
    </div>
</section>

<!--================ Accomodation Area  =================-->


<!--================ Área de Instalaciones =================-->
<section class="facilities_area section_gap">
    <div class="overlay bg-parallax" data-stellar-ratio="0.8" data-stellar-vertical-offset="0" data-background="">
    </div>
    <div class="container">
        <div class="section_title text-center">
            <h2 class="title_w">Nuestros Servicios</h2>
            <p>Un lugar donde la magia del espectáculo cobra vida.</p>
        </div>
        <div class="row mb_30">
            <div class="col-lg-4 col-md-6">
                <div class="facilities_item">
                    <h4 class="sec_h4"><i class="lnr lnr-magic-wand"></i>Espectáculos de Magia</h4>
                    <p>Déjate sorprender por ilusionistas de renombre que transforman lo imposible en realidad.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="facilities_item">
                    <h4 class="sec_h4"><i class="lnr lnr-music-note"></i>Musicales</h4>
                    <p>Vive la intensidad de producciones musicales que llenan el escenario de vida y emoción.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="facilities_item">
                    <h4 class="sec_h4"><i class="lnr lnr-smile"></i>Teatro de Humor</h4>
                    <p>Ríete a carcajadas con comedias que harán que olvides el estrés diario.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="facilities_item">
                    <h4 class="sec_h4"><i class="lnr lnr-rocket"></i>Teatro Infantil</h4>
                    <p>Un espacio pensado para los más pequeños, lleno de imaginación y aprendizaje.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="facilities_item">
                    <h4 class="sec_h4"><i class="lnr lnr-diamond"></i>Ópera y Danza</h4>
                    <p>Disfruta de la elegancia y pasión de espectáculos de danza y ópera de primer nivel.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="facilities_item">
                    <h4 class="sec_h4"><i class="lnr lnr-diamond"></i>Teatro Clásico</h4>
                    <p>Redescubre las grandes obras de la literatura teatral en un ambiente único y lleno de ilusión.</p>
                </div>
            </div>
        </div>
    </div>
</section>
<!--================ Área de Instalaciones =================-->


<!--================ About History Area  =================-->
<section class="about_history_area section_gap">
    <div class="container">
        <div class="row">
            <div class="col-md-6 d_flex align-items-center">
                <div class="about_content ">
                    <h2 class="title title_color">Sobre Nosotros<br>Nuestra Historia<br>Misión y Visión</h2>
                    <p>
                        En nuestro espacio, llevamos años brindando emociones únicas a través de una amplia variedad de espectáculos.
                        Desde el humor y el teatro clásico hasta musicales vibrantes, espectáculos de magia, teatro infantil,
                        danza contemporánea y ópera, ofrecemos un lugar donde todos puedan encontrar algo para disfrutar.
                        Nuestra misión es inspirar, emocionar y conectar a través del arte y la creatividad, mientras construimos
                        un espacio acogedor para todos.
                    </p>
                    <a href="index.php?mod=contacto" class="button_hover theme_btn_two">Solicita Información</a>
                </div>
            </div>
            <div class="col-md-6">
                <img class="img-fluid" src="image/about_bg.jpg" alt="Imagen Sobre Nosotros">
            </div>
        </div>
    </div>
</section>
<!--================ About History Area  =================-->