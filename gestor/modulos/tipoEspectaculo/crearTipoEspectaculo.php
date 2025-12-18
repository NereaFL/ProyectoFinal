<?php
// Variable para mantener el dato ingresado
$tipoEspectaculo = "";

// Si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['acc']) && $_POST['acc'] == 'Crear') {
    $tipoEspectaculo = strtoupper(trim($_POST['tipoEspectaculo'])); // Convertir a mayúsculas

    // Verificar si el tipoEspectaculo ya existe
    $checkTipoEspectaculo = $conx->prepare("SELECT idTipoEspectaculo FROM tipoEspectaculo WHERE tipoEspectaculo = ?");
    $checkTipoEspectaculo->bind_param("s", $tipoEspectaculo);
    $checkTipoEspectaculo->execute();
    $checkTipoEspectaculo->store_result();

    if ($checkTipoEspectaculo->num_rows > 0) {
        echo "<div class='alert alert-danger'>El tipo de espectáculo ya existe en la base de datos.</div>";
    } else {
        // Insertar tipoEspectaculo
        $sql = $conx->prepare("INSERT INTO tipoEspectaculo (tipoEspectaculo) VALUES (?)");
        $sql->bind_param("s", $tipoEspectaculo);
        if ($sql->execute()) {
            echo "<div class='alert alert-success'>Tipo de espectáculo creado correctamente, puede crear otro.</div>";
            // Limpiar el valor después de un registro exitoso
            $tipoEspectaculo = "";
        } else {
            echo "<div class='alert alert-danger'>Error al registrar el tipo de espectáculo.</div>";
        }
    }
}
?>

<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Crear Tipo de Espectáculo</h4>
            <p class="card-description">Complete el campo para registrar un nuevo tipo de espectáculo</p>
            <form class="forms-sample" method="POST">
                <input type="hidden" name="acc" value="Crear">

                <div class="form-group">
                    <label for="tipoEspectaculo">Nombre del Tipo de Espectáculo</label>
                    <input type="text" class="form-control" id="tipoEspectaculo" name="tipoEspectaculo" value="<?= htmlspecialchars($tipoEspectaculo) ?>" placeholder="Nombre del Tipo de Espectáculo" required />
                </div>

                <button type="submit" class="btn btn-primary mr-2">Registrar</button>
                <a href="index.php?mod=listaTipoEspectaculos" class="btn btn-danger mr-2">Volver al listado</a>
            </form>
        </div>
    </div>
</div>
