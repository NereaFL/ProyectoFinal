<?php
$idEspectaculo = $_GET['id'] ?? null;

if (!$idEspectaculo) {
    echo "<div class='alert alert-danger'>No se ha proporcionado un ID de espectáculo.</div>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['accion']) && $_POST['accion'] == 'CambiarPortada') {
    $idNuevaPortada = $_POST['idFoto'];

    // Buscar la foto actual que tiene portada = 1 para este espectáculo
    $consultaPortadaActual = "SELECT idFoto FROM foto WHERE portada = 1 AND idEspectaculo = ?";
    $stmtPortadaActual = mysqli_prepare($conx, $consultaPortadaActual);
    mysqli_stmt_bind_param($stmtPortadaActual, "i", $idEspectaculo);
    mysqli_stmt_execute($stmtPortadaActual);
    $resultadoPortadaActual = mysqli_stmt_get_result($stmtPortadaActual);

    if ($fotoActual = mysqli_fetch_assoc($resultadoPortadaActual)) {
        $idFotoActual = $fotoActual['idFoto'];

        // Quitar la portada actual
        $consultaQuitarPortada = "UPDATE foto SET portada = 0 WHERE idFoto = ?";
        $stmtQuitarPortada = mysqli_prepare($conx, $consultaQuitarPortada);
        mysqli_stmt_bind_param($stmtQuitarPortada, "i", $idFotoActual);
        mysqli_stmt_execute($stmtQuitarPortada);
    }

    // Establecer la nueva foto como portada
    $consultaNuevaPortada = "UPDATE foto SET portada = 1 WHERE idFoto = ?";
    $stmtNuevaPortada = mysqli_prepare($conx, $consultaNuevaPortada);
    mysqli_stmt_bind_param($stmtNuevaPortada, "i", $idNuevaPortada);
    mysqli_stmt_execute($stmtNuevaPortada);
}

// Consultar todas las fotos del espectáculo
$consultaFotos = "SELECT idFoto, nombre, portada FROM foto WHERE idEspectaculo = ? ";
$stmtFotos = mysqli_prepare($conx, $consultaFotos);
mysqli_stmt_bind_param($stmtFotos, "i", $idEspectaculo);
mysqli_stmt_execute($stmtFotos);
$resultadoFotos = mysqli_stmt_get_result($stmtFotos);
?>

<div class="page-header flex-wrap">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body text-center">
                <!-- Botón de volver al listado de espectáculos alineado a la izquierda y en estilo danger -->
                <div class="d-flex justify-content-start">
                    <a href="index.php?mod=listaEspectaculos" class="btn btn-danger mb-3">Volver al Listado de Espectáculos</a>
                </div>

                <h4 class="card-title">Listado de Fotos del Espectáculo</h4>

                <a href="index.php?mod=subirFoto&id=<?php echo $idEspectaculo; ?>" class="btn btn-success mb-3">Subir Nueva Foto</a>

                <div style="overflow-x: auto; position: relative;">
                    <!-- Barra de scroll horizontal superior -->
                    <div style="overflow-x: auto; height: 20px; position: absolute; top: 0; left: 0; right: 0;"></div>

                    <table class="table table-striped text-center" style="margin-top: 20px;">
                        <thead>
                            <tr>
                                <th class="text-center">ID</th>
                                <th class="text-center">Nombre</th>
                                <th class="text-center">Vista Previa</th>
                                <th class="text-center">Portada</th>
                                <th class="text-center">Opciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($resultadoFotos) > 0) { ?>
                                <?php while ($foto = mysqli_fetch_assoc($resultadoFotos)) { ?>
                                    <tr>
                                        <td class="align-middle"><?php echo $foto['idFoto']; ?></td>
                                        <td class="align-middle"><?php echo $foto['nombre']; ?></td>
                                        <td class="align-middle">
                                            <button class="btn btn-info btn-sm preview-btn"
                                                data-toggle="modal"
                                                data-target="#vistaPreviaModal"
                                                data-img="../public/<?php echo $foto['nombre']; ?>">
                                                <i class="mdi mdi-camera"></i>
                                            </button>
                                        </td>
                                        <td class="align-middle">
                                            <form method="POST">
                                                <input type="hidden" name="accion" value="CambiarPortada">
                                                <input type="hidden" name="idFoto" value="<?php echo $foto['idFoto']; ?>">
                                                <button type="submit" class="btn btn-link p-0 border-0" onclick="return confirm('¿Estás seguro de que quieres cambiar la portada?');">
                                                    <i class="mdi <?php echo ($foto['portada'] == 1) ? 'mdi-star' : 'mdi-star-outline'; ?>"
                                                        style="font-size: 24px; color: #FFA500;">
                                                    </i>
                                                </button>
                                            </form>
                                        </td>
                                        <td class="align-middle">
                                            <a href="index.php?mod=eliminarFoto&idFoto=<?php echo $foto['idFoto']; ?>&idEspectaculo=<?php echo $idEspectaculo; ?>"
                                                class="btn btn-danger btn-sm"
                                                onclick="return confirm('¿Estás seguro de eliminar esta foto?');">
                                                <i class="mdi mdi-delete-forever"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php } else { ?>
                                <tr>
                                    <td colspan="5" class="align-middle text-center">
                                        <div class="alert alert-danger mb-0">No hay fotos registradas para este espectáculo.</div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>

                    <!-- Barra de scroll horizontal inferior -->
                    <div style="overflow-x: auto; height: 20px;"></div>
                </div>
            </div>

            <!-- Modal de vista previa -->
            <div class="modal fade" id="vistaPreviaModal" tabindex="-1" aria-labelledby="vistaPreviaModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="vistaPreviaModalLabel">Vista Previa</h5>
                            <!-- Botón de cerrar (cruz) -->
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                            <!-- Imagen de previsualización -->
                            <img id="vistaPreviaImagen" src="" alt="Vista Previa" class="img-fluid">
                        </div>
                        <div class="modal-footer">
                            <!-- Botón "Cerrar" -->
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>