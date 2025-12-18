<?php

//Archivo de configuracion

//Título
define('TITLE', 'Gestión de entradas para eventos');

// Módulos y carpetas po defecto
define('MODULO_DEFECTO', 'dashboard');
define('LAYOUT_DEFECTO', 'plantilla.php');
define('MODULO_PATH', realpath('./modulos/'));
define('LAYOUT_PATH', realpath('./templates/'));

// Datos de cada modulo

/*********************** */
/* INICIO                */
/*********************** */

$conf['dashboard'] = array(
    'archivo' => 'dashboard.php',
    'dir' => '.',
    'privilegios' => 'priv_general',
    'layout' => 'plantilla.php'
);

$conf['contacto'] = array(
    'archivo' => 'listaMensajes.php',
    'dir' => 'contacto',
    'privilegios' => 'priv_general',
    'layout' => 'plantilla.php'
);

$conf['verMensaje'] = array(
    'archivo' => 'verMensaje.php',
    'dir' => 'contacto',
    'privilegios' => 'priv_general',
    'layout' => 'plantilla.php'
);

/*********************** */
/* ROLES                 */
/*********************** */

$conf['listaRoles'] = array(
    'archivo' => 'listaRoles.php',
    'dir' => 'rol',
    'privilegios' => 'priv_general',
    'layout' => 'plantilla.php'
);

$conf['crearRol'] = array(
    'archivo' => 'crearRol.php',
    'dir' => 'rol',
    'privilegios' => 'priv_general',
    'layout' => 'plantilla.php'
);

$conf['editarRol'] = array(
    'archivo' => 'editarRol.php',
    'dir' => 'rol',
    'privilegios' => 'priv_general',
    'layout' => 'plantilla.php'
);

$conf['eliminarRol'] = array(
    'archivo' => 'eliminarRol.php',
    'dir' => 'rol',
    'privilegios' => 'priv_general',
    'layout' => 'plantilla.php'
);

/*********************** */
/* USUARIOS              */
/*********************** */

$conf['listaUsuarios'] = array(
    'archivo' => 'listaUsuarios.php',
    'dir' => 'usuario',
    'privilegios' => 'priv_general',
    'layout' => 'plantilla.php'
);

$conf['editarUsuario'] = array(
    'archivo' => 'editarUsuario.php',
    'dir' => 'usuario',
    'privilegios' => 'priv_general',
    'layout' => 'plantilla.php'
);

$conf['crearUsuario'] = array(
    'archivo' => 'crearUsuario.php',
    'dir' => 'usuario',
    'privilegios' => 'priv_general',
    'layout' => 'plantilla.php'
);

$conf['eliminarUsuario'] = array(
    'archivo' => 'eliminarUsuario.php',
    'dir' => 'usuario',
    'privilegios' => 'priv_general',
    'layout' => 'plantilla.php'
);


/*********************** */
/* TIPO ESPECTACULO      */
/*********************** */

$conf['listaTipoEspectaculos'] = array(
    'archivo' => 'listaTipoEspectaculo.php',
    'dir' => 'tipoEspectaculo',
    'privilegios' => 'priv_general',
    'layout' => 'plantilla.php'
);

$conf['crearTipoEspectaculo'] = array(
    'archivo' => 'crearTipoEspectaculo.php',
    'dir' => 'tipoEspectaculo',
    'privilegios' => 'priv_general',
    'layout' => 'plantilla.php'
);

$conf['editarTipoEspectaculo'] = array(
    'archivo' => 'editarTipoEspectaculo.php',
    'dir' => 'tipoEspectaculo',
    'privilegios' => 'priv_general',
    'layout' => 'plantilla.php'
);

$conf['eliminarTipoEspectaculo'] = array(
    'archivo' => 'eliminarTipoEspectaculo.php',
    'dir' => 'tipoEspectaculo',
    'privilegios' => 'priv_general',
    'layout' => 'plantilla.php'
);

/*********************** */
/* SALAS                 */
/*********************** */

$conf['listaSalas'] = array(
    'archivo' => 'listaSalas.php',
    'dir' => 'sala',
    'privilegios' => 'priv_general',
    'layout' => 'plantilla.php'
);

$conf['crearSala'] = array(
    'archivo' => 'crearSala.php',
    'dir' => 'sala',
    'privilegios' => 'priv_general',
    'layout' => 'plantilla.php'
);

$conf['editarSala'] = array(
    'archivo' => 'editarSala.php',
    'dir' => 'sala',
    'privilegios' => 'priv_general',
    'layout' => 'plantilla.php'
);

$conf['eliminarSala'] = array(
    'archivo' => 'eliminarSala.php',
    'dir' => 'sala',
    'privilegios' => 'priv_general',
    'layout' => 'plantilla.php'
);

/*********************** */
/* ESPECTACULOS          */
/*********************** */

$conf['listaEspectaculos'] = array(
    'archivo' => 'listaEspectaculos.php',
    'dir' => 'espectaculo',
    'privilegios' => 'priv_general',
    'layout' => 'plantilla.php'
);

$conf['crearEspectaculo'] = array(
    'archivo' => 'crearEspectaculo.php',
    'dir' => 'espectaculo',
    'privilegios' => 'priv_general',
    'layout' => 'plantilla.php'
);

$conf['editarEspectaculo'] = array(
    'archivo' => 'editarEspectaculo.php',
    'dir' => 'espectaculo',
    'privilegios' => 'priv_general',
    'layout' => 'plantilla.php'
);

$conf['eliminarEspectaculo'] = array(
    'archivo' => 'eliminarEspectaculo.php',
    'dir' => 'espectaculo',
    'privilegios' => 'priv_general',
    'layout' => 'plantilla.php'
);

/*********************** */
/* FOTOS                 */
/*********************** */

$conf['listaFotos'] = array(
    'archivo' => 'listaFotos.php',
    'dir' => 'foto',
    'privilegios' => 'priv_general',
    'layout' => 'plantilla.php'
);

$conf['subirFoto'] = array(
    'archivo' => 'subirFoto.php',
    'dir' => 'foto',
    'privilegios' => 'priv_general',
    'layout' => 'plantilla.php'
);

$conf['eliminarFoto'] = array(
    'archivo' => 'eliminarFoto.php',
    'dir' => 'foto',
    'privilegios' => 'priv_general',
    'layout' => 'plantilla.php'
);

/*********************** */
/* COMPRAS               */
/*********************** */

$conf['listaCompras'] = array(
    'archivo' => 'listaCompras.php',
    'dir' => 'compra',
    'privilegios' => 'priv_general',
    'layout' => 'plantilla.php'
);

$conf['eliminarCompra'] = array(
    'archivo' => 'eliminarCompra.php',
    'dir' => 'compra',
    'privilegios' => 'priv_general',
    'layout' => 'plantilla.php'
);



?>