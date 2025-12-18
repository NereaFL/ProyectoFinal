<!--================Breadcrumb Area =================-->
<section class="breadcrumb_area">
    <div class="overlay bg-parallax"></div>
    <div class="container">
        <div class="page-cover text-start">
            <h2 class="page-cover-tittle">Galería de Espectáculos</h2>
            <ol class="breadcrumb">
                <li><a href="index.php">Inicio</a></li>
                <li class="active">Galería</li>
            </ol>
        </div>
    </div>
</section>
<!--================End Breadcrumb =================-->

<!--================ Gallery Area =================-->
<section class="gallery_area section_gap">
    <div class="container">
        <div class="section_title text-center">
            <h2 class="title_color">Galería de nuestros Espectáculos</h2>
            <p>Revive los mejores momentos a través de nuestras imágenes.</p>
        </div>

        <!-- Filtro por espectáculo -->
        <div class="row justify-content-center mb-4">
            <div class="col-md-6 col-lg-4">
                <form method="GET" class="d-flex align-items-center justify-content-center gap-2">
                    <input type="hidden" name="mod" value="<?php echo $_GET['mod'] ?>">
                    <label for="espectaculo" class="me-2">Filtrar por espectáculo:</label>
                    <select name="espectaculo" id="espectaculo" class="form-select select2" onchange="this.form.submit()">
                        <option value="">Todos</option>
                        <?php
                            $espectaculos = mysqli_query($conx, "SELECT * FROM espectaculo");
                            while ($row = mysqli_fetch_assoc($espectaculos)) {
                                $selected = (isset($_GET['espectaculo']) && $_GET['espectaculo'] == $row['idEspectaculo']) ? "selected" : "";
                                echo "<option value='{$row['idEspectaculo']}' $selected>{$row['nombre']}</option>";
                            }
                        ?>
                    </select>
                </form>
            </div>
        </div>

        <!-- Galería de imágenes -->
        <div class="row imageGallery1" id="gallery">
            <?php
                $filtro = isset($_GET['espectaculo']) ? intval($_GET['espectaculo']) : null;
                $query = "SELECT * FROM foto";
                if ($filtro) {
                    $query .= " WHERE idEspectaculo = $filtro";
                }
                $fotos = mysqli_query($conx, $query);

                while ($foto = mysqli_fetch_assoc($fotos)) {
                    $imgPath = "public/" . htmlspecialchars($foto['nombre']);
                    echo "
                    <div class='col-md-4 gallery_item'>
                        <div class='gallery_img'>
                            <img src='$imgPath' alt=''>
                            <div class='hover'>
                                <a class='light' href='$imgPath'><i class='fa fa-expand'></i></a>
                            </div>
                        </div>
                    </div>";
                }
            ?>
        </div>
    </div>
</section>
<!--================ End Gallery Area =================-->
