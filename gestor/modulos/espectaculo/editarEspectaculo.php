<?php
// Obtener el ID del espectáculo desde la URL
$idEspectaculo = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Variables para mantener los datos del espectáculo
$tipoEspectaculo = $sala = $nombre = $descripcion = $fechaInicio = $fechaFin = $horarios = $duracion = $precio = "";
$horas = ["", "", "", "", ""]; // Array para las cinco horas

// Conexión a la base de datos
require 'conexion.php';

// Obtener los datos del espectáculo para completar el formulario
if ($idEspectaculo > 0) {
    $query = $conx->prepare("
        SELECT tipoEspectaculo, sala, nombre, descripcion, fecha_inicio, fecha_fin, horarios, duracion, precio 
        FROM espectaculo 
        WHERE idEspectaculo = ?
    ");
    $query->bind_param("i", $idEspectaculo);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows === 1) {
        $espectaculo = $result->fetch_assoc();
        $tipoEspectaculo = $espectaculo['tipoEspectaculo'];
        $sala = $espectaculo['sala'];
        $nombre = $espectaculo['nombre'];
        $descripcion = $espectaculo['descripcion'];
        $fechaInicio = $espectaculo['fecha_inicio'];
        $fechaFin = $espectaculo['fecha_fin'];
        $horarios = $espectaculo['horarios'];
        $duracion = $espectaculo['duracion'];
        $precio = $espectaculo['precio'];

        // Convertir los horarios en un array para los campos de hora
        $horas = explode(',', $horarios);
    } else {
        echo "<div class='alert alert-danger'>Espectáculo no encontrado.</div>";
        exit;
    }
}

// Si se envió el formulario para editar
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['acc']) && $_POST['acc'] == 'Editar') {
    $tipoEspectaculo = $_POST['tipoEspectaculo'];
    $sala = $_POST['sala'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $fechaInicio = $_POST['fecha_inicio'];
    $fechaFin = empty($_POST['fecha_fin']) ? null : $_POST['fecha_fin'];
    $duracion = intval($_POST['duracion']);
    $precio = floatval($_POST['precio']);

    // Recoger las horas y construir la cadena separada por comas
    $horas = [$_POST['hora1'], $_POST['hora2'], $_POST['hora3'], $_POST['hora4'], $_POST['hora5']];
    $horarios = implode(',', array_filter($horas, fn($h) => !empty($h))); // Filtrar y unir las horas no vacías

// Validación de conflictos considerando duración + 15 minutos
$conflicto = false;
$horariosArray = explode(',', $horarios);

foreach ($horariosArray as $hora) {
    $hora = trim($hora);

    // Añadir 15 minutos al tiempo de duración
    $duracionConMargen = $duracion + 15;
    $horaFin = date("H:i:s", strtotime($hora . " + $duracionConMargen minutes"));

    $queryCheck = $conx->prepare("
        SELECT idEspectaculo, horarios 
        FROM espectaculo 
        WHERE sala = ? 
        AND idEspectaculo != ? 
        AND (
            (fecha_inicio <= ? AND (fecha_fin IS NULL OR fecha_fin >= ?)) OR 
            (fecha_inicio <= ? AND (fecha_fin IS NULL OR fecha_fin >= ?))
        )
    ");
    $queryCheck->bind_param("iissss", $sala, $idEspectaculo, $fechaInicio, $fechaInicio, $fechaFin, $fechaFin);
    $queryCheck->execute();
    $resultCheck = $queryCheck->get_result();

    while ($row = $resultCheck->fetch_assoc()) {
        $existentes = explode(',', $row['horarios']);
        foreach ($existentes as $horaExistente) {
            $horaExistente = trim($horaExistente);
            $horaExistenteFin = date("H:i:s", strtotime($horaExistente . " + $duracionConMargen minutes"));

            if (
                ($hora >= $horaExistente && $hora < $horaExistenteFin) || // Inicio dentro de otro intervalo
                ($horaFin > $horaExistente && $horaFin <= $horaExistenteFin) || // Fin dentro de otro intervalo
                ($horaExistente >= $hora && $horaExistente < $horaFin) // Otro intervalo dentro del rango
            ) {
                $conflicto = true;
                break 2;
            }
        }
    }
}

    if ($conflicto) {
        echo "<div class='alert alert-danger'>Ya existe un espectáculo en conflicto con alguno de los horarios establecidos.</div>";
    } else {
        // Actualizar el espectáculo sin guardar hora fin
        $queryUpdate = $conx->prepare("
            UPDATE espectaculo 
            SET tipoEspectaculo = ?, sala = ?, nombre = ?, descripcion = ?, fecha_inicio = ?, fecha_fin = ?, horarios = ?, duracion = ?, precio = ? 
            WHERE idEspectaculo = ?
        ");
        $queryUpdate->bind_param(
            "iisssssisi",
            $tipoEspectaculo,
            $sala,
            $nombre,
            $descripcion,
            $fechaInicio,
            $fechaFin,
            $horarios,
            $duracion,
            $precio,
            $idEspectaculo
        );

        if ($queryUpdate->execute()) {
            echo "<div class='alert alert-success'>Espectáculo editado correctamente.</div>";
        } else {
            echo "<div class='alert alert-danger'>Error al editar el espectáculo.</div>";
        }
    }
}
?>

<!-- Formulario -->
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Editar Espectáculo</h4>
            <p class="card-description">Modifique los campos para actualizar el espectáculo</p>
            <form class="forms-sample" method="POST">
                <input type="hidden" name="acc" value="Editar">

                <div class="form-group">
                    <label for="tipoEspectaculo">Tipo de Espectáculo</label>
                    <select class="form-control select2" id="tipoEspectaculo" name="tipoEspectaculo" required>
                        <option value=""></option>
                        <?php
                        $sql = "SELECT idTipoEspectaculo, tipoEspectaculo FROM tipoEspectaculo";
                        $result = $conx->query($sql);
                        while ($row = $result->fetch_assoc()) {
                            $selected = ($tipoEspectaculo == $row['idTipoEspectaculo']) ? "selected" : "";
                            echo "<option value='" . $row['idTipoEspectaculo'] . "' $selected>" . $row['tipoEspectaculo'] . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="sala">Sala</label>
                    <select class="form-control select2" id="sala" name="sala" required>
                        <option value=""></option>
                        <?php
                        $sql = "SELECT idSala, numeroSala FROM sala";
                        $result = $conx->query($sql);
                        while ($row = $result->fetch_assoc()) {
                            $selected = ($sala == $row['idSala']) ? "selected" : "";
                            echo "<option value='" . $row['idSala'] . "' $selected>Sala " . $row['numeroSala'] . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="<?= htmlspecialchars($nombre) ?>" placeholder="Nombre del Espectáculo" required />
                </div>

                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3" placeholder="Descripción"><?= htmlspecialchars($descripcion) ?></textarea>
                </div>

                <div class="form-group">
                    <label for="fecha_inicio">Fecha Inicio</label>
                    <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" value="<?= htmlspecialchars($fechaInicio) ?>" required />
                </div>

                <div class="form-group">
                    <label for="fecha_fin">Fecha Fin</label>
                    <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" value="<?= htmlspecialchars($fechaFin) ?>" />
                </div>

                <div class="form-group">
                    <label for="horas">Horarios</label>                   
                    <input type="time" class="form-control mb-2" id="hora1" name="hora1" value="<?= $horas[0] ?? '' ?>" required />
                    <input type="time" class="form-control mb-2" id="hora2" name="hora2" value="<?= $horas[1] ?? '' ?>" />
                    <input type="time" class="form-control mb-2" id="hora3" name="hora3" value="<?= $horas[2] ?? '' ?>" />
                    <input type="time" class="form-control mb-2" id="hora4" name="hora4" value="<?= $horas[3] ?? '' ?>" />
                    <input type="time" class="form-control mb-2" id="hora5" name="hora5" value="<?= $horas[4] ?? '' ?>" />
                </div>

                <div class="form-group">
                    <label for="duracion">Duración (minutos)</label>
                    <input type="number" class="form-control" id="duracion" name="duracion" value="<?= htmlspecialchars($duracion) ?>" placeholder="Duración en minutos" required />
                </div>

                <div class="form-group">
                    <label for="precio">Precio</label>
                    <input type="number" class="form-control" id="precio" name="precio" step="0.01" value="<?= htmlspecialchars($precio) ?>" placeholder="Precio de la entrada" required />
                </div>

                <button type="submit" class="btn btn-primary mr-2">Guardar Cambios</button>
                <a href="index.php?mod=listaEspectaculos" class="btn btn-danger mr-2">Volver al listado</a>
            </form>
        </div>
    </div>
</div>
