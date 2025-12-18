<?php
// Verificar si se envió el ID del espectáculo a eliminar
if (isset($_GET['id'])) {
    $idEspectaculo = $_GET['id'];

    // Preparar la consulta para eliminar el espectáculo
    $query = $conx->prepare("DELETE FROM espectaculo WHERE idEspectaculo = ?");
    $query->bind_param("i", $idEspectaculo);

    if ($query->execute()) {
        // Mostrar alert de éxito y redirigir al listado de espectáculos
        echo "<script>
            window.location.href = 'index.php?mod=listaEspectaculos';
        </script>";
    } else {
        // Mostrar alert de error y redirigir al listado de espectáculos
        echo "<script>
            window.location.href = 'index.php?mod=listaEspectaculos';
        </script>";
    }
} else {
    // Mostrar alert de error si no se proporciona un ID válido y redirigir
    echo "<script>
        window.location.href = 'index.php?mod=listaEspectaculos';
    </script>";
}
?>
