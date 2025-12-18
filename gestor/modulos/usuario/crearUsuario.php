<?php
// Variables para mantener los datos ingresados (excepto email y contraseña)
$rol = $nombre = $apellidos = $telefono = "";
$email = "";

// Si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['acc']) && $_POST['acc'] == 'Crear') {
    $rol = $_POST['rol'];
    $nombre = trim($_POST['nombre']);
    $apellidos = trim($_POST['apellidos']);
    $telefono = trim($_POST['telefono']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validar que las contraseñas coincidan
    if ($password !== $confirm_password) {
        echo "<div class='alert alert-danger'>Las contraseñas no coinciden.</div>";
    } else {
        $password_hashed = password_hash($password, PASSWORD_BCRYPT);

        // Verificar si el email ya existe
        $checkEmail = $conx->prepare("SELECT idUsuario FROM usuario WHERE email = ?");
        $checkEmail->bind_param("s", $email);
        $checkEmail->execute();
        $checkEmail->store_result();

        if ($checkEmail->num_rows > 0) {
            echo "<div class='alert alert-danger'>El correo ya está registrado.</div>";
        } else {
            // Insertar usuario
            $sql = $conx->prepare("INSERT INTO usuario (rol, nombre, apellidos, numeroTelefono, email, contrasena) VALUES (?, ?, ?, ?, ?, ?)");
            $sql->bind_param("isssss", $rol, $nombre, $apellidos, $telefono, $email, $password_hashed);
            if ($sql->execute()) {
                echo "<div class='alert alert-success'>Usuario creado correctamente, puede crear otro.</div>";
                // Limpiar valores tras éxito
                $rol = $nombre = $apellidos = $telefono = $email = "";
            } else {
                echo "<div class='alert alert-danger'>Error al registrar usuario.</div>";
            }
        }
    }
}
?>

<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Crear Usuario</h4>
            <p class="card-description">Complete los campos para registrar un usuario</p>
            <form class="forms-sample" method="POST" autocomplete="off">
                <input type="hidden" name="acc" value="Crear">

                <div class="form-group">
                    <label for="rol">Rol</label>
                    <select class="form-control select2" id="rol" name="rol" required>
                        <option value=""></option>
                        <?php
                        $sql = "SELECT idRol, tipoRol FROM rol";
                        $result = $conx->query($sql);
                        while ($row = $result->fetch_assoc()) {
                            $selected = ($rol == $row['idRol']) ? "selected" : "";
                            echo "<option value='" . $row['idRol'] . "' $selected>" . htmlspecialchars($row['tipoRol']) . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="<?= htmlspecialchars($nombre) ?>" placeholder="Nombre" required />
                </div>

                <div class="form-group">
                    <label for="apellidos">Apellidos</label>
                    <input type="text" class="form-control" id="apellidos" name="apellidos" value="<?= htmlspecialchars($apellidos) ?>" placeholder="Apellidos" required />
                </div>

                <div class="form-group">
                    <label for="telefono">Número de Teléfono</label>
                    <input type="tel" class="form-control" id="telefono" name="telefono" value="<?= htmlspecialchars($telefono) ?>" placeholder="Teléfono" required />
                </div>

                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($email) ?>" placeholder="Email" required />
                </div>

                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña" required />
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirmar Contraseña</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirmar Contraseña" required />
                </div>

                <button type="submit" class="btn btn-primary mr-2">Registrar</button>
                <a href="index.php?mod=listaUsuarios" class="btn btn-danger mr-2">Volver al listado</a>
            </form>
        </div>
    </div>
</div>
