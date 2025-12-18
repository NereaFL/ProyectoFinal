<?php
// Variables para mantener los datos ingresados
$tipoEspectaculo = $sala = $nombre = $descripcion = $precio = $duracion = "";
$horas = ["", "", "", "", ""]; // Array para las cinco horas

// Si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['acc']) && $_POST['acc'] == 'Crear') {
    $tipoEspectaculo = $_POST['tipoEspectaculo'];
    $sala = $_POST['sala'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $fechaInicio = $_POST['fecha_inicio'];
    $fechaFin = empty($_POST['fecha_fin']) ? null : $_POST['fecha_fin']; // NULL si no se introduce fecha_fin
    $precio = floatval($_POST['precio']);
    $duracion = intval($_POST['duracion']);

    // Validar que la fecha de inicio no sea menor a hoy
    $hoy = date("Y-m-d");
    if ($fechaInicio < $hoy) {
        echo "<div class='alert alert-danger'>La fecha de inicio no puede ser anterior a hoy.</div>";
    } else {
        // Recoger las horas y construir la cadena separada por comas
        $horas = [$_POST['hora1'], $_POST['hora2'], $_POST['hora3'], $_POST['hora4'], $_POST['hora5']];
        $horarios = implode(',', array_filter($horas, fn($h) => !empty($h)));

        // Verificar conflicto de horarios considerando la duración + 15 minutos adicionales
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
        AND ((fecha_inicio <= ? AND (fecha_fin IS NULL OR fecha_fin >= ?)))
    ");
            $queryCheck->bind_param("sss", $sala, $fechaInicio, $fechaInicio);
            $queryCheck->execute();
            $resultCheck = $queryCheck->get_result();

            while ($row = $resultCheck->fetch_assoc()) {
                $existentes = explode(',', $row['horarios']);
                foreach ($existentes as $horaExistente) {
                    $horaExistente = trim($horaExistente);
                    $horaExistenteFin = date("H:i:s", strtotime($horaExistente . " + $duracionConMargen minutes"));

                    if (
                        ($hora >= $horaExistente && $hora < $horaExistenteFin) || 
                        ($horaFin > $horaExistente && $horaFin <= $horaExistenteFin) ||
                        ($horaExistente >= $hora && $horaExistente < $horaFin)
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
            // Insertar el nuevo espectáculo
            $queryInsert = $conx->prepare("
                INSERT INTO espectaculo (tipoEspectaculo, sala, nombre, descripcion, fecha_inicio, fecha_fin, horarios, duracion, precio) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $queryInsert->bind_param(
                "iisssssds",
                $tipoEspectaculo,
                $sala,
                $nombre,
                $descripcion,
                $fechaInicio,
                $fechaFin,
                $horarios,
                $duracion,
                $precio
            );

            if ($queryInsert->execute()) {
                echo "<div class='alert alert-success'>Espectáculo registrado correctamente.</div>";
                $tipoEspectaculo = $sala = $nombre = $descripcion = $precio = $duracion = "";
                $horas = ["", "", "", "", ""];
            } else {
                echo "<div class='alert alert-danger'>Error al registrar el espectáculo.</div>";
            }
        }
    }
}
?>
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Crear Espectáculo</h4>
            <p class="card-description">Complete los campos para registrar un espectáculo</p>
            <form class="forms-sample" method="POST">
                <input type="hidden" name="acc" value="Crear">

                <div class="form-group">
                    <label for="tipoEspectaculo">Tipo de Espectáculo</label>
                    <select class="form-control select2" id="tipoEspectaculo" name="tipoEspectaculo" required>
                        <option value=""></option>
                        <?php
                        $sql = "SELECT idTipoEspectaculo, tipoEspectaculo FROM tipoEspectaculo";
                        $result = $conx->query($sql);
                        while ($row = $result->fetch_assoc()) {
                            $selected = ($row['idTipoEspectaculo'] == $tipoEspectaculo) ? "selected" : "";
                            echo "<option value='" . $row['idTipoEspectaculo'] . "' $selected>" . htmlspecialchars($row['tipoEspectaculo']) . "</option>";
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
                            $selected = ($row['idSala'] == $sala) ? "selected" : "";
                            echo "<option value='" . $row['idSala'] . "' $selected>Sala " . htmlspecialchars($row['numeroSala']) . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" required value="<?= htmlspecialchars($nombre) ?>">
                </div>

                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea class="form-control" id="descripcion" name="descripcion" rows="4" required><?= htmlspecialchars($descripcion) ?></textarea>
                </div>

                <div class="form-group">
                    <label for="fecha_inicio">Fecha Inicio</label>
                    <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required min="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($fechaInicio ?? '') ?>" />
                </div>

                <div class="form-group">
                    <label for="fecha_fin">Fecha Fin</label>
                    <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" value="<?= htmlspecialchars($fechaFin ?? '') ?>" />
                </div>

                <div class="form-group">
                    <label for="duracion">Duración (minutos)</label>
                    <input type="number" class="form-control" id="duracion" name="duracion" placeholder="Duración en minutos" required value="<?= htmlspecialchars($duracion) ?>" />
                </div>

                <div class="form-group">
                    <label for="horas">Horarios</label>
                    <input type="time" class="form-control mb-2" id="hora1" name="hora1" required value="<?= $horas[0] ?>" />
                    <input type="time" class="form-control mb-2" id="hora2" name="hora2" value="<?= $horas[1] ?>" />
                    <input type="time" class="form-control mb-2" id="hora3" name="hora3" value="<?= $horas[2] ?>" />
                    <input type="time" class="form-control mb-2" id="hora4" name="hora4" value="<?= $horas[3] ?>" />
                    <input type="time" class="form-control mb-2" id="hora5" name="hora5" value="<?= $horas[4] ?>" />
                </div>

                <div class="form-group">
                    <label for="precio">Precio</label>
                    <input type="number" class="form-control" id="precio" name="precio" step="0.01" placeholder="Precio de la entrada" required value="<?= htmlspecialchars($precio) ?>" />
                </div>

                <button type="submit" class="btn btn-primary mr-2">Registrar</button>
                <a href="index.php?mod=listaEspectaculos" class="btn btn-danger">Volver al listado</a>
            </form>
        </div>
    </div>
</div>
