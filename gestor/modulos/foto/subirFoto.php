<?php

$idEspectaculo = $_GET['id'] ?? null;

if (!$idEspectaculo) {
    echo "<div class='alert alert-danger'>No se ha proporcionado un ID de espectáculo.</div>";
    exit;
}

// Si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['imagen'])) {
    $errores = [];

    // Directorio donde se guardarán las imágenes
    $directorioSubida = "../public/";

    // Validar que se ha subido un archivo
    if ($_FILES['imagen']['error'] === UPLOAD_ERR_NO_FILE) {
        $errores[] = "Debe seleccionar una imagen.";
    } else {
        $imagen = $_FILES['imagen'];
        $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'gif'];

        // Obtener extensión del archivo
        $ext = strtolower(pathinfo($imagen['name'], PATHINFO_EXTENSION));

        // Validar extensión
        if (!in_array($ext, $extensionesPermitidas)) {
            $errores[] = "Formato de imagen no válido. Solo se permiten JPG, JPEG, PNG y GIF.";
        }

        // Validar tamaño (máximo 5MB)
        if ($imagen['size'] > 5 * 1024 * 1024) {
            $errores[] = "La imagen es demasiado grande. El tamaño máximo permitido es 5MB.";
        }

        // Si no hay errores, proceder con la subida
        if (empty($errores)) {
            // Generar un nombre único para la imagen
            $nombreUnico = $idEspectaculo . "_" . bin2hex(random_bytes(30)) . "." . $ext;
            $rutaDestino = $directorioSubida . $nombreUnico;

            // Mover la imagen al directorio
            if (move_uploaded_file($imagen['tmp_name'], $rutaDestino)) {
                // Insertar en la base de datos
                $query = $conx->prepare("INSERT INTO foto (idEspectaculo, nombre) VALUES (?, ?)");
                $query->bind_param("is", $idEspectaculo, $nombreUnico);

                if ($query->execute()) {
                    echo "<div class='alert alert-success'>Imagen subida correctamente.</div>";
                } else {
                    echo "<div class='alert alert-danger'>Error al guardar la imagen en la base de datos.</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>Error al mover la imagen al directorio de almacenamiento.</div>";
            }
        }
    }

    // Mostrar errores si los hay
    if (!empty($errores)) {
        foreach ($errores as $error) {
            echo "<div class='alert alert-danger'>$error</div>";
        }
    }
}
?>

<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Subir Imagen</h4>
            <p class="card-description">Seleccione una imagen para subir</p>
            <form class="forms-sample" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="imagen">Seleccionar Imagen</label>
                    <input type="file" class="form-control-file" id="imagen" name="imagen" accept="image/*" required />
                </div>
                <button type="submit" class="btn btn-primary mr-2">Subir Imagen</button>
                <a href="index.php?mod=listaFotos&id=<?= $idEspectaculo ?>" class="btn btn-danger mr-2">Volver</a>
            </form>
        </div>
    </div>
</div>
