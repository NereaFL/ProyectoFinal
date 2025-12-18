<?php
// Obtener el ID del rol desde la URL
$idRol = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Variable para mantener los datos del rol
$tipoRol = "";

// Conexión a la base de datos
require 'conexion.php';

// Función para cargar siempre el rol desde la base de datos
function cargarRolDesdeBaseDeDatos($conx, $idRol) {
    $query = $conx->prepare("SELECT tipoRol FROM rol WHERE idRol = ?");
    $query->bind_param("i", $idRol);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows === 1) {
        return $result->fetch_assoc()['tipoRol'];
    } else {
        echo "<div class='alert alert-danger'>Rol no encontrado.</div>";
        exit;
    }
}

// Cargar los datos del rol al inicio y después de cada acción
if ($idRol > 0) {
    $tipoRol = cargarRolDesdeBaseDeDatos($conx, $idRol);
}

// Si se envió el formulario para editar
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['acc']) && $_POST['acc'] == 'Editar') {
    $nuevoTipoRol = strtoupper(trim($_POST['tipoRol'])); // Convertir a mayúsculas

    // Verificar si ya existe otro rol con el mismo nombre
    $checkRol = $conx->prepare("SELECT idRol FROM rol WHERE tipoRol = ? AND idRol != ?");
    $checkRol->bind_param("si", $nuevoTipoRol, $idRol);
    $checkRol->execute();
    $checkRol->store_result();

    if ($checkRol->num_rows > 0) {
        echo "<div class='alert alert-danger'>Ya existe otro rol con el mismo nombre.</div>";
    } else {
        // Actualizar el rol en la base de datos
        $queryUpdate = $conx->prepare("UPDATE rol SET tipoRol = ? WHERE idRol = ?");
        $queryUpdate->bind_param("si", $nuevoTipoRol, $idRol);

        if ($queryUpdate->execute()) {
            echo "<div class='alert alert-success'>Rol editado correctamente.</div>";
        } else {
            echo "<div class='alert alert-danger'>Error al editar el rol.</div>";
        }
    }

    // Recargar siempre los datos actuales desde la base de datos
    $tipoRol = cargarRolDesdeBaseDeDatos($conx, $idRol);
}
?>

<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Editar Rol</h4>
            <p class="card-description">Modifique el campo para actualizar el rol</p>
            <form class="forms-sample" method="POST">
                <input type="hidden" name="acc" value="Editar">

                <div class="form-group">
                    <label for="tipoRol">Nombre del Rol</label>
                    <!-- Siempre cargar lo que esté en la base de datos -->
                    <input type="text" class="form-control" id="tipoRol" name="tipoRol" value="<?= htmlspecialchars($tipoRol) ?>" placeholder="Nombre del Rol" required />
                </div>

                <button type="submit" class="btn btn-primary mr-2">Guardar Cambios</button>
                <a href="index.php?mod=listaRoles" class="btn btn-danger mr-2">Volver al listado</a>
            </form>
        </div>
    </div>
</div>
