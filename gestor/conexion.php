<?php

$servername = "localhost";
$username = "root";
$password = "";
$database = "teatro";

/*
$servername = "sql200.infinityfree.com";
$username = "if0_38908334";
$password = "Nerea642004";
$database = "if0_38908334_teatro";

*/
// Crear conexión
$conx = new mysqli($servername, $username, $password, $database);

// Verificar conexión
if ($conx->connect_error) {
    die("Error de conexión: " . $conx->connect_error);
}

$conx->set_charset("utf8mb4");
?>
