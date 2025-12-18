<?php
// Variables para almacenar errores
$error_contraseña = "";
$error_email = "";
$actualizado = false;

// Procesar actualización
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['acc']) && $_POST['acc'] == 'Actualizar') {
    $idUsuario = $_POST['idUsuario'];
    $rol = $_POST['rol'];
    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];
    $contrasena_actual = $_POST['contrasena_actual'] ?? '';
    $nueva_contrasena = $_POST['nueva_contrasena'] ?? '';

    // Verificar si el email ya está en uso
    $query_email = $conx->prepare("SELECT idUsuario FROM usuario WHERE email = ? AND idUsuario != ?");
    $query_email->bind_param("si", $email, $idUsuario);
    $query_email->execute();
    $result_email = $query_email->get_result();

    if ($result_email->num_rows > 0) {
        $error_email = "El correo electrónico ya está en uso por otro usuario.";
    }

    // Validar si se ingresó una nueva contraseña
    if (!empty($nueva_contrasena)) {
        if (!empty($contrasena_actual)) {
            // Obtener la contraseña actual del usuario
            $query_contraseña = $conx->prepare("SELECT contrasena FROM usuario WHERE idUsuario = ?");
            $query_contraseña->bind_param("i", $idUsuario);
            $query_contraseña->execute();
            $result_contraseña = $query_contraseña->get_result();
            $usuario_contraseña = $result_contraseña->fetch_assoc();

            if (!password_verify($contrasena_actual, $usuario_contraseña['contrasena'])) {
                $error_contraseña = "La contraseña actual es incorrecta.";
            } else {
                $nueva_contrasena_hash = password_hash($nueva_contrasena, PASSWORD_BCRYPT);
                $sql = $conx->prepare("UPDATE usuario SET rol = ?, nombre = ?, apellidos = ?, numeroTelefono = ?, email = ?, contrasena = ? WHERE idUsuario = ?");
                $sql->bind_param("isssssi", $rol, $nombre, $apellidos, $telefono, $email, $nueva_contrasena_hash, $idUsuario);
            }
        } else {
            $error_contraseña = "Debe ingresar la contraseña actual para cambiar la contraseña.";
        }
    }

    // Si no hay errores en la contraseña ni el email, actualizar los demás campos
    if (empty($error_contraseña) && empty($error_email)) {
        if (empty($nueva_contrasena)) {
            $sql = $conx->prepare("UPDATE usuario SET rol = ?, nombre = ?, apellidos = ?, numeroTelefono = ?, email = ? WHERE idUsuario = ?");
            $sql->bind_param("issssi", $rol, $nombre, $apellidos, $telefono, $email, $idUsuario);
        }

        if ($sql->execute()) {
            echo "<div class='alert alert-success'>Usuario actualizado correctamente.</div>";
            $actualizado = true;
        } else {
            echo "<div class='alert alert-danger'>Error al actualizar usuario.</div>";
        }
    }
}

// Obtener los datos del usuario después de procesar el formulario
if (isset($_GET['id'])) {
    $idUsuario = $_GET['id'];

    function obtenerUsuario($conx, $idUsuario) {
        $query = $conx->prepare("SELECT rol, nombre, apellidos, numeroTelefono, email, contrasena FROM usuario WHERE idUsuario = ?");
        $query->bind_param("i", $idUsuario);
        $query->execute();
        $result = $query->get_result();
        return $result->fetch_assoc();
    }

    $usuario = obtenerUsuario($conx, $idUsuario);

    if (!$usuario) {
        echo "<div class='alert alert-danger'>Usuario no encontrado.</div>";
        exit;
    }
}
?>


<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Actualizar Usuario</h4>
            <p class="card-description">Modifique los campos para actualizar un usuario</p>
            <form class="forms-sample" method="POST">
                <input type="hidden" name="acc" value="Actualizar">
                <input type="hidden" name="idUsuario" value="<?= $idUsuario ?>">

                <div class="form-group">
                    <label for="rol">Rol</label>
                    <select class="form-control select2" id="rol" name="rol" required>
                        <option value=""></option>
                        <?php
                        $sql = "SELECT idRol, tipoRol FROM rol";
                        $result = $conx->query($sql);
                        while ($row = $result->fetch_assoc()) {
                            $selected = ($row['idRol'] == $usuario['rol']) ? "selected" : "";
                            echo "<option value='" . $row['idRol'] . "' $selected>" . $row['tipoRol'] . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required />
                </div>

                <div class="form-group">
                    <label for="apellidos">Apellidos</label>
                    <input type="text" class="form-control" id="apellidos" name="apellidos" value="<?= htmlspecialchars($usuario['apellidos']) ?>" required />
                </div>

                <div class="form-group">
                    <label for="telefono">Número de Teléfono</label>
                    <input type="tel" class="form-control" id="telefono" name="telefono" value="<?= htmlspecialchars($usuario['numeroTelefono']) ?>" required />
                </div>

                <?php if (!empty($error_email)) : ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error_email) ?></div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required />
                </div>

                <hr>
                <p class="card-description">Si desea cambiar la contraseña, complete los siguientes campos:</p>

                <?php if (!empty($error_contraseña)) : ?>
                    <div class="alert alert-danger"><?= $error_contraseña ?></div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="contrasena_actual">Contraseña Actual</label>
                    <input type="password" class="form-control" id="contrasena_actual" name="contrasena_actual" placeholder="Ingrese su contraseña actual" />
                </div>

                <div class="form-group">
                    <label for="nueva_contrasena">Nueva Contraseña</label>
                    <input type="password" class="form-control" id="nueva_contrasena" name="nueva_contrasena" placeholder="Ingrese su nueva contraseña" />
                </div>

                <button type="submit" class="btn btn-primary mr-2">Actualizar</button>
                <a href="index.php?mod=listaUsuarios" class="btn btn-danger mr-2">Volver al listado</a>
            </form>
        </div>
    </div>
</div>

