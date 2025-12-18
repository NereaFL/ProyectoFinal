<?php
// Obtener el ID del tipoEspectaculo desde la URL
$idTipoEspectaculo = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Variable para mantener los datos del tipoEspectaculo
$tipoEspectaculo = "";

// Conexión a la base de datos
require 'conexion.php';

// Función para cargar siempre los datos desde la base de datos
function cargarTipoEspectaculoDesdeBaseDeDatos($conx, $idTipoEspectaculo) {
    $query = $conx->prepare("SELECT tipoEspectaculo FROM tipoEspectaculo WHERE idTipoEspectaculo = ?");
    $query->bind_param("i", $idTipoEspectaculo);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows === 1) {
        return $result->fetch_assoc()['tipoEspectaculo'];
    } else {
        echo "<div class='alert alert-danger'>Tipo de espectáculo no encontrado.</div>";
        exit;
    }
}

// Cargar los datos desde la base de datos al inicio y después de cada acción
if ($idTipoEspectaculo > 0) {
    $tipoEspectaculo = cargarTipoEspectaculoDesdeBaseDeDatos($conx, $idTipoEspectaculo);
}

// Si se envió el formulario para editar
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['acc']) && $_POST['acc'] == 'Editar') {
    $nuevoTipoEspectaculo = strtoupper(trim($_POST['tipoEspectaculo'])); // Convertir a mayúsculas

    // Verificar si ya existe otro tipoEspectaculo con el mismo nombre
    $checkTipoEspectaculo = $conx->prepare("SELECT idTipoEspectaculo FROM tipoEspectaculo WHERE tipoEspectaculo = ? AND idTipoEspectaculo != ?");
    $checkTipoEspectaculo->bind_param("si", $nuevoTipoEspectaculo, $idTipoEspectaculo);
    $checkTipoEspectaculo->execute();
    $checkTipoEspectaculo->store_result();

    if ($checkTipoEspectaculo->num_rows > 0) {
        echo "<div class='alert alert-danger'>Ya existe otro tipo de espectáculo con el mismo nombre.</div>";
    } else {
        // Actualizar tipoEspectaculo
        $queryUpdate = $conx->prepare("UPDATE tipoEspectaculo SET tipoEspectaculo = ? WHERE idTipoEspectaculo = ?");
        $queryUpdate->bind_param("si", $nuevoTipoEspectaculo, $idTipoEspectaculo);

        if ($queryUpdate->execute()) {
            echo "<div class='alert alert-success'>Tipo de espectáculo editado correctamente.</div>";
        } else {
            echo "<div class='alert alert-danger'>Error al editar el tipo de espectáculo.</div>";
        }
    }

    // Recargar siempre los datos actuales desde la base de datos
    $tipoEspectaculo = cargarTipoEspectaculoDesdeBaseDeDatos($conx, $idTipoEspectaculo);
}
?>

<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Editar Tipo de Espectáculo</h4>
            <p class="card-description">Modifique el campo para actualizar el tipo de espectáculo</p>
            <form class="forms-sample" method="POST">
                <input type="hidden" name="acc" value="Editar">

                <div class="form-group">
                    <label for="tipoEspectaculo">Nombre del Tipo de Espectáculo</label>
                    <!-- Siempre cargar lo que esté en la base de datos -->
                    <input type="text" class="form-control" id="tipoEspectaculo" name="tipoEspectaculo" value="<?= htmlspecialchars($tipoEspectaculo) ?>" placeholder="Nombre del Tipo de Espectáculo" required />
                </div>

                <button type="submit" class="btn btn-primary mr-2">Guardar Cambios</button>
                <a href="index.php?mod=listaTipoEspectaculos" class="btn btn-danger mr-2">Volver al listado</a>
            </form>
        </div>
    </div>
</div>
