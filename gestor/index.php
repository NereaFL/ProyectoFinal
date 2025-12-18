<?php 


session_start();

if (empty($_SESSION['usuario']) || empty($_SESSION['rol']) || $_SESSION['rol'] !== "ADMIN") {
    header('Location: ../login.php');
    exit;
}
    

include('conf.php');
include('conexion.php');

if(!empty($_GET['mod'])) {
    $modulo = $_GET['mod'];
} else {
    $modulo = MODULO_DEFECTO;
}

if(empty($conf[$modulo])) {
    $modulo = MODULO_DEFECTO;
}

if(empty($conf[$modulo]['layout'])) {
    $conf[$modulo]['layout'] = LAYOUT_DEFECTO;
}

$path_layout = LAYOUT_PATH . '/' . $conf[$modulo]['layout'];
$path_modulo = MODULO_PATH . '/' . $conf[$modulo]['dir'] . '/' . $conf[$modulo]['archivo'];

if(file_exists($path_layout)) {
    include($path_layout);
} else {
    if(file_exists($path_modulo)) {
        include($path_modulo);
    } else {
        echo '<div>Error al cargar el módulo <b>' . $modulo . '</b>. No existe el archivo <b>' . $conf[$modulo]['archivo'] .'</b>';
        }
}

?>