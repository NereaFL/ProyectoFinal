<nav class="navbar col-lg-12 col-12 p-lg-0 fixed-top d-flex flex-row">
  <div class="navbar-menu-wrapper d-flex align-items-stretch justify-content-between">
    <a class="navbar-brand brand-logo-mini align-self-center d-lg-none" href="index.php"><img src="images/logo.png" style="width: 100px; height: auto;" alt="logo" /></a>
    <button class="navbar-toggler navbar-toggler align-self-center mr-2" type="button" data-toggle="minimize">
      <i class="mdi mdi-menu"></i>
    </button>

    <ul class="navbar-nav">
      <li class="nav-item dropdown d-none d-sm-flex">
        <a class="nav-link count-indicator dropdown-toggle" href="index.php?mod=contacto">
          <i class="mdi mdi-email-outline"></i>
        </a>
      </li>
    </ul>

    <ul class="navbar-nav navbar-nav-right ml-lg-auto">

      <li class="nav-item  nav-profile dropdown border-0">
        <a class="nav-link dropdown-toggle" id="profileDropdown" href="#" data-toggle="dropdown">
          <img class="nav-profile-img mr-2" alt="" src="images/pic-2.png">
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

          <span class="profile-name"><?= htmlspecialchars($nombreCompleto) ?></span>
        </a>
        <div class="dropdown-menu navbar-dropdown w-100" aria-labelledby="profileDropdown">
          <a class="dropdown-item" href="../login.php">
            <i class="mdi mdi-logout mr-2 text-primary"></i> Cerrar sesión </a>
        </div>
      </li>
    </ul>
    <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
      <span class="mdi mdi-menu"></span>
    </button>
  </div>
</nav>