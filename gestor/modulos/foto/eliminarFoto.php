<?php
// Obtener los IDs enviados por la URL
$idEspectaculo = $_GET['idEspectaculo'] ?? null;
$idFoto = $_GET['idFoto'] ?? null;

if (!$idEspectaculo || !$idFoto) {
    echo "<script>
        alert('Error: No se han proporcionado los IDs necesarios.');
        window.location.href = 'index.php?mod=listaFotos&id=' + $idEspectaculo;
    </script>";
    exit;
}

// Buscar la foto en la base de datos para obtener su nombre
$consultaFoto = "SELECT nombre FROM foto WHERE idFoto = ? AND idEspectaculo = ?";
$stmtFoto = mysqli_prepare($conx, $consultaFoto);
mysqli_stmt_bind_param($stmtFoto, "ii", $idFoto, $idEspectaculo);
mysqli_stmt_execute($stmtFoto);
$resultadoFoto = mysqli_stmt_get_result($stmtFoto);

if ($foto = mysqli_fetch_assoc($resultadoFoto)) {
    $rutaArchivo = "../public/" . $foto['nombre'];

    // Eliminar el archivo físicamente si existe
    if (file_exists($rutaArchivo)) {
        unlink($rutaArchivo);
    }

    // Eliminar la entrada de la base de datos
    $consultaEliminar = "DELETE FROM foto WHERE idFoto = ?";
    $stmtEliminar = mysqli_prepare($conx, $consultaEliminar);
    mysqli_stmt_bind_param($stmtEliminar, "i", $idFoto);
    mysqli_stmt_execute($stmtEliminar);

    // Redirigir al listado con un mensaje de éxito
    echo "<script>
        window.location.href = 'index.php?mod=listaFotos&id=' + $idEspectaculo;
    </script>";
    exit;
} else {
    // Redirigir al listado con un mensaje de error si la foto no se encuentra
    echo "<script>
        alert('Error: No se pudo encontrar la foto.');
        window.location.href = 'index.php?mod=listaFotos&id=' + $idEspectaculo;
    </script>";
    exit;
}
?>
