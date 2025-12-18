<?php
// Variable para mantener el dato ingresado
$tipoRol = "";

// Si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['acc']) && $_POST['acc'] == 'Crear') {
    $tipoRol = strtoupper(trim($_POST['tipoRol'])); // Convertir a mayúsculas y quitar espacios

    // Verificar si el rol ya existe
    $checkRol = $conx->prepare("SELECT idRol FROM rol WHERE tipoRol = ?");
    $checkRol->bind_param("s", $tipoRol);
    $checkRol->execute();
    $checkRol->store_result();

    if ($checkRol->num_rows > 0) {
        echo "<div class='alert alert-danger'>El rol ya existe en la base de datos.</div>";
    } else {
        // Insertar rol
        $sql = $conx->prepare("INSERT INTO rol (tipoRol) VALUES (?)");
        $sql->bind_param("s", $tipoRol);
        if ($sql->execute()) {
            echo "<div class='alert alert-success'>Rol creado correctamente, puede crear otro.</div>";
            // Limpiar el valor después de un registro exitoso
            $tipoRol = "";
        } else {
            echo "<div class='alert alert-danger'>Error al registrar el rol.</div>";
        }
    }
}
?>

<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Crear Rol</h4>
            <p class="card-description">Complete el campo para registrar un nuevo rol</p>
            <form class="forms-sample" method="POST" autocomplete="off">
                <input type="hidden" name="acc" value="Crear">
                
                <div class="form-group">
                    <label for="tipoRol">Nombre del Rol</label>
                    <input 
                        type="text" 
                        class="form-control" 
                        id="tipoRol" 
                        name="tipoRol" 
                        value="<?= htmlspecialchars($tipoRol) ?>" 
                        placeholder="Nombre del Rol" 
                        required 
                    />
                </div>

                <button type="submit" class="btn btn-primary mr-2">Registrar</button>
                <a href="index.php?mod=listaRoles" class="btn btn-danger mr-2">Volver al listado</a>
            </form>
        </div>
    </div>
</div>
