<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <div class="text-center sidebar-brand-wrapper d-flex align-items-center">
    <a class="sidebar-brand img-fluid rounded-start" href="index.php"><img src="images/logo.png" alt="logo" /></a>
  </div>
  <ul class="nav">
    <li class="nav-item nav-profile">
      <a href="#" class="nav-link">
        <div class="nav-profile-image">
          <img src="images/pic-2.png" alt="profile" />
          <span class="login-status online"></span>
        </div>
        <?php

        if (isset($_SESSION['usuario'])) {

          $idUsuario = $_SESSION['usuario'];

          $query = "SELECT nombre, apellidos FROM usuario WHERE idUsuario = ?";
          $stmt = $conx->prepare($query);
          $stmt->bind_param("i", $idUsuario);
          $stmt->execute();
          $result = $stmt->get_result();

          if ($result->num_rows === 1) {
            $usuario = $result->fetch_assoc();
            $nombreCompleto = $usuario['nombre'] . ' ' . $usuario['apellidos'];
          } else {
            $nombreCompleto = "Usuario no encontrado";
          }
        } else {
          $nombreCompleto = "No has iniciado sesión";
        }
        ?>

        <div class="nav-profile-text d-flex flex-column pr-3">
          <span class="font-weight-medium mb-2"><?= htmlspecialchars($nombreCompleto) ?></span>
        </div>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="index.php?mod=dashboard">
        <i class="mdi mdi-home menu-icon"></i>
        <span class="menu-title">Inicio</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="index.php?mod=listaUsuarios">
        <i class="mdi mdi-account-circle menu-icon"></i>
        <span class="menu-title">Usuarios</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="index.php?mod=listaRoles">
        <i class="mdi mdi-account-multiple menu-icon"></i>
        <span class="menu-title">Roles de usuarios</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="index.php?mod=listaEspectaculos">
        <i class="mdi mdi-auto-fix menu-icon"></i>
        <span class="menu-title">Espectáculos</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="index.php?mod=listaTipoEspectaculos">
        <i class="mdi mdi-auto-fix menu-icon"></i>
        <span class="menu-title">Tipos de Espectáculos</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="index.php?mod=listaSalas">
        <i class="mdi mdi-sofa menu-icon"></i>
        <span class="menu-title">Salas</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="index.php?mod=listaCompras">
        <i class="mdi mdi-tag-text-outline menu-icon"></i>
        <span class="menu-title">Compras</span>
      </a>
    </li>
  </ul>
</nav>