<header class="header_area">
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light">
            <a class="navbar-brand logo_h" href="index.php"><img src="image/Logo.png" alt="" style="width: 300px; height: 70px;"></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

            <div class="collapse navbar-collapse offset" id="navbarSupportedContent">
                <ul class="nav navbar-nav menu_nav ml-auto">
                    <li class="nav-item <?php echo (!isset($_GET['mod']) || $_GET['mod'] === 'inicio') ? 'active' : ''; ?>">
                        <a class="nav-link" href="index.php">Inicio</a>
                    </li>
                    <li class="nav-item <?php echo (isset($_GET['mod']) && $_GET['mod'] === 'sobre-nosotros') ? 'active' : ''; ?>">
                        <a class="nav-link" href="index.php?mod=sobre-nosotros">Sobre nosotros</a>
                    </li>
                    <li class="nav-item <?php echo (isset($_GET['mod']) && $_GET['mod'] === 'listaEspectaculos') ? 'active' : ''; ?>">
                        <a class="nav-link" href="index.php?mod=listaEspectaculos">Espectaculos</a>
                    </li>
                    <li class="nav-item <?php echo (isset($_GET['mod']) && $_GET['mod'] === 'galeria') ? 'active' : ''; ?>">
                        <a class="nav-link" href="index.php?mod=galeria">Galería de fotos</a>
                    </li>
                    <li class="nav-item <?php echo (isset($_GET['mod']) && $_GET['mod'] === 'contacto') ? 'active' : ''; ?>">
                        <a class="nav-link" href="index.php?mod=contacto">Contacto</a>
                    </li>

                   <?php
$nombreUsuario = '';

if (isset($_SESSION['usuario'])) {
    $idUsuario = intval($_SESSION['usuario']); // Asegura que sea un número
    $query = "SELECT nombre FROM usuario WHERE idUsuario = $idUsuario";
    $resultado = mysqli_query($conx, $query);

    if ($fila = mysqli_fetch_assoc($resultado)) {
        $nombreUsuario = $fila['nombre'];
    }
}
?>

<li class="nav-item submenu dropdown">
    <a href="#" class="nav-link dropdown-toggle d-flex align-items-center gap-2" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
        <img src="image/pic-2.png" alt="Menu" class="rounded-circle" style="height: 30px;">
        <span class="d-inline-block">
            <?php echo isset($nombreUsuario) && !empty($nombreUsuario) ? htmlspecialchars($nombreUsuario) : '&nbsp;'; ?>
        </span>
    </a>
    <ul class="dropdown-menu">
        <?php
        if (!isset($_SESSION['usuario'])) {
            echo '
                <li class="nav-item"><a class="nav-link" href="login.php">Iniciar sesión</a></li>
                <li class="nav-item"><a class="nav-link" href="registro.php">Registrarse</a></li>';
        } else {
            echo '
                <li class="nav-item"><a class="nav-link" href="index.php?mod=usuario-panel">Panel personal</a></li>';

            if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'ADMIN') {
                echo '
                <li class="nav-item"><a class="nav-link" href="gestor" target="_blank">Gestión</a></li>';
            }

            echo '
                <li class="nav-item"><a class="nav-link" href="logout.php">Cerrar sesión</a></li>';
        }
        ?>
    </ul>
</li>


                </ul>
            </div>
        </nav>
    </div>
</header>