<?php
// Obtener el ID de la sala desde la URL
$idSala = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Variables para mantener los datos de la sala
$numeroSala = $capacidad = "";

// Conexión a la base de datos
require 'conexion.php';

// Función para cargar siempre los datos desde la base de datos
function cargarSalaDesdeBaseDeDatos($conx, $idSala) {
    $query = $conx->prepare("SELECT numeroSala, capacidad FROM sala WHERE idSala = ?");
    $query->bind_param("i", $idSala);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows === 1) {
        return $result->fetch_assoc();
    } else {
        echo "<div class='alert alert-danger'>Sala no encontrada.</div>";
        exit;
    }
}

// Cargar los datos desde la base de datos al inicio y después de cada acción
if ($idSala > 0) {
    $sala = cargarSalaDesdeBaseDeDatos($conx, $idSala);
    $numeroSala = $sala['numeroSala'];
    $capacidad = $sala['capacidad'];
}

// Si se envió el formulario para editar
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['acc']) && $_POST['acc'] == 'Editar') {
    $nuevoNumeroSala = intval(trim($_POST['numeroSala']));
    $nuevaCapacidad = intval(trim($_POST['capacidad']));

    // Verificar que los valores sean válidos
    if ($nuevoNumeroSala <= 0) {
        echo "<div class='alert alert-danger'>El número de sala debe ser mayor a 0.</div>";
    } elseif ($nuevaCapacidad <= 0) {
        echo "<div class='alert alert-danger'>La capacidad debe ser mayor a 0.</div>";
    } else {
        // Verificar si el número de sala ya existe en otra sala
        $checkSala = $conx->prepare("SELECT idSala FROM sala WHERE numeroSala = ? AND idSala != ?");
        $checkSala->bind_param("ii", $nuevoNumeroSala, $idSala);
        $checkSala->execute();
        $checkSala->store_result();

        if ($checkSala->num_rows > 0) {
            echo "<div class='alert alert-danger'>El número de sala ya existe en otra sala.</div>";
        } else {
            // Actualizar sala
            $queryUpdate = $conx->prepare("UPDATE sala SET numeroSala = ?, capacidad = ? WHERE idSala = ?");
            $queryUpdate->bind_param("iii", $nuevoNumeroSala, $nuevaCapacidad, $idSala);

            if ($queryUpdate->execute()) {
                echo "<div class='alert alert-success'>Sala editada correctamente.</div>";
            } else {
                echo "<div class='alert alert-danger'>Error al editar la sala.</div>";
            }
        }
    }

    // Recargar siempre los datos actuales desde la base de datos
    $sala = cargarSalaDesdeBaseDeDatos($conx, $idSala);
    $numeroSala = $sala['numeroSala'];
    $capacidad = $sala['capacidad'];
}
?>

<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Editar Sala</h4>
            <p class="card-description">Modifique los campos para actualizar la sala</p>
            <form class="forms-sample" method="POST">
                <input type="hidden" name="acc" value="Editar">

                <div class="form-group">
                    <label for="numeroSala">Número de Sala</label>
                    <input type="number" class="form-control" id="numeroSala" name="numeroSala" value="<?= htmlspecialchars($numeroSala) ?>" placeholder="Número de Sala" required />
                </div>

                <div class="form-group">
                    <label for="capacidad">Capacidad</label>
                    <input type="number" class="form-control" id="capacidad" name="capacidad" value="<?= htmlspecialchars($capacidad) ?>" placeholder="Capacidad" required />
                </div>

                <button type="submit" class="btn btn-primary mr-2">Guardar Cambios</button>
                <a href="index.php?mod=listaSalas" class="btn btn-danger mr-2">Volver al listado</a>
            </form>
        </div>
    </div>
</div>
