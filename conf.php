<?php

//Archivo de configuracion

//Título
define('TITLE', 'Gestión de entradas para eventos');

// Módulos y carpetas po defecto
define('MODULO_DEFECTO', 'inicio');
define('LAYOUT_DEFECTO', 'plantilla.php');
define('MODULO_PATH', realpath('./modulos/'));
define('LAYOUT_PATH', realpath('./templates/'));

// Datos de cada modulo

/*********************** */
/* INICIO                */
/*********************** */

$conf['inicio'] = array(
    'archivo' => 'inicio.php',
    'dir' => '.',
    'privilegios' => 'priv_general',
    'layout' => 'plantilla.php'
);

$conf['politicasPrivacidad'] = array(
    'archivo' => 'politicasPrivacidad.php',
    'dir' => '.',
    'privilegios' => 'priv_general',
    'layout' => 'plantilla.php'
);

$conf['terminosUso'] = array(
    'archivo' => 'terminosUso.php',
    'dir' => '.',
    'privilegios' => 'priv_general',
    'layout' => 'plantilla.php'
);

$conf['contacto'] = array(
    'archivo' => 'contacto.php',
    'dir' => '.',
    'privilegios' => 'priv_general',
    'layout' => 'plantilla.php'
);

$conf['galeria'] = array(
    'archivo' => 'galeria.php',
    'dir' => '.',
    'privilegios' => 'priv_general',
    'layout' => 'plantilla.php'
);

$conf['sobre-nosotros'] = array(
    'archivo' => 'sobre-nosotros.php',
    'dir' => '.',
    'privilegios' => 'priv_general',
    'layout' => 'plantilla.php'
);

/******************************** */
/* ESPECTÁCULOS                   */
/******************************** */
$conf['usuario-panel'] = array(
    'archivo' => 'usuario-panel.php',
    'dir' => 'usuario',
    'privilegios' => 'priv_general',
    'layout' => 'plantilla.php'
);

$conf['usuario-editar'] = array(
    'archivo' => 'usuario-editar.php',
    'dir' => 'usuario',
    'privilegios' => 'priv_general',
    'layout' => 'plantilla.php'
);

$conf['usuario-contrasena'] = array(
    'archivo' => 'usuario-contrasena.php',
    'dir' => 'usuario',
    'privilegios' => 'priv_general',
    'layout' => 'plantilla.php'
);

$conf['usuario-espectaculos'] = array(
    'archivo' => 'usuario-entradas.php',
    'dir' => 'usuario',
    'privilegios' => 'priv_general',
    'layout' => 'plantilla.php'
);

/******************************** */
/* ESPECTÁCULOS                   */
/******************************** */
$conf['listaEspectaculos'] = array(
    'archivo' => 'listaEspectaculos.php',
    'dir' => 'espectaculos',
    'privilegios' => 'priv_general',
    'layout' => 'plantilla.php'
);

$conf['espectaculo-detalles'] = array(
    'archivo' => 'detalleEspectaculo.php',
    'dir' => 'espectaculos',
    'privilegios' => 'priv_general',
    'layout' => 'plantilla.php'
);

$conf['compraEspectaculo'] = array(
    'archivo' => 'compraEspectaculo.php',
    'dir' => 'espectaculos',
    'privilegios' => 'priv_general',
    'layout' => 'plantilla.php'
);

$conf['espectaculo-fecha'] = array(
    'archivo' => 'fechaEspectaculo.php',
    'dir' => 'espectaculos',
    'privilegios' => 'priv_general',
    'layout' => 'plantilla.php'
);

$conf['entradasEspectaculo'] = array(
    'archivo' => 'entradasEspectaculo.php',
    'dir' => 'espectaculos',
    'privilegios' => 'priv_general',
    'layout' => 'plantilla.php'
);

$conf['pagoEspectaculo'] = array(
    'archivo' => 'pagoEspectaculo.php',
    'dir' => 'espectaculos',
    'privilegios' => 'priv_general',
    'layout' => 'plantilla.php'
);

$conf['graciasCompra'] = array(
    'archivo' => 'graciasCompra.php',
    'dir' => 'espectaculos',
    'privilegios' => 'priv_general',
    'layout' => 'plantilla.php'
);

?>