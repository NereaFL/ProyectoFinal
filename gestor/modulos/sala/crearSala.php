<?php
// Variables para mantener los datos ingresados
$numeroSala = $capacidad = "";

// Si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['acc']) && $_POST['acc'] == 'Crear') {
    $numeroSala = intval(trim($_POST['numeroSala'])); // Convertir a número entero y limpiar espacios
    $capacidad = intval(trim($_POST['capacidad'])); // Convertir a número entero y limpiar espacios

    // Verificar que los valores sean válidos
    if ($numeroSala <= 0) {
        echo "<div class='alert alert-danger'>El número de sala debe ser mayor a 0.</div>";
    } elseif ($capacidad <= 0) {
        echo "<div class='alert alert-danger'>La capacidad debe ser mayor a 0.</div>";
    } else {
        // Verificar si el número de sala ya existe
        $checkSala = $conx->prepare("SELECT idSala FROM sala WHERE numeroSala = ?");
        $checkSala->bind_param("i", $numeroSala);
        $checkSala->execute();
        $checkSala->store_result();

        if ($checkSala->num_rows > 0) {
            echo "<div class='alert alert-danger'>El número de sala ya existe en la base de datos.</div>";
        } else {
            // Insertar sala
            $sql = $conx->prepare("INSERT INTO sala (numeroSala, capacidad) VALUES (?, ?)");
            $sql->bind_param("ii", $numeroSala, $capacidad);
            if ($sql->execute()) {
                echo "<div class='alert alert-success'>Sala creada correctamente, puede crear otra.</div>";
                // Limpiar los valores después de un registro exitoso
                $numeroSala = $capacidad = "";
            } else {
                echo "<div class='alert alert-danger'>Error al registrar la sala.</div>";
            }
        }
    }
}
?>

<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Crear Sala</h4>
            <p class="card-description">Complete los campos para registrar una nueva sala</p>
            <form class="forms-sample" method="POST">
                <input type="hidden" name="acc" value="Crear">

                <div class="form-group">
                    <label for="numeroSala">Número de Sala</label>
                    <input type="number" class="form-control" id="numeroSala" name="numeroSala" value="<?= htmlspecialchars($numeroSala) ?>" placeholder="Número de Sala" required />
                </div>

                <div class="form-group">
                    <label for="capacidad">Capacidad</label>
                    <input type="number" class="form-control" id="capacidad" name="capacidad" value="<?= htmlspecialchars($capacidad) ?>" placeholder="Capacidad" required />
                </div>

                <button type="submit" class="btn btn-primary mr-2">Registrar</button>
                <a href="index.php?mod=listaSalas" class="btn btn-danger mr-2">Volver al listado</a>
            </form>
        </div>
    </div>
</div>
