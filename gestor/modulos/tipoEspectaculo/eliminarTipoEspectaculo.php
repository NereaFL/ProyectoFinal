<?php
// Verificar si se envió el ID del tipoEspectaculo a eliminar
if (isset($_GET['id'])) {
    $idTipoEspectaculo = intval($_GET['id']); // Asegurar que el ID sea válido

    // Conexión a la base de datos
    require 'conexion.php';

    // Verificar si el tipoEspectaculo existe antes de intentar eliminarlo
    $queryCheck = $conx->prepare("SELECT tipoEspectaculo FROM tipoEspectaculo WHERE idTipoEspectaculo = ?");
    $queryCheck->bind_param("i", $idTipoEspectaculo);
    $queryCheck->execute();
    $result = $queryCheck->get_result();

    if ($result->num_rows === 1) {
        $tipoEspectaculo = $result->fetch_assoc();

        // Preparar la consulta para eliminar el tipoEspectaculo
        $queryDelete = $conx->prepare("DELETE FROM tipoEspectaculo WHERE idTipoEspectaculo = ?");
        $queryDelete->bind_param("i", $idTipoEspectaculo);

        if ($queryDelete->execute()) {
            // Mostrar alert de éxito y redirigir al listado de tipoEspectaculos
            echo "<script>
                window.location.href = 'index.php?mod=listaTipoEspectaculos';
            </script>";
        } else {
            // Mostrar alert de error
            echo "<script>
                alert('Error al intentar eliminar el tipo de espectáculo.');
                window.location.href = 'index.php?mod=listaTipoEspectaculos';
            </script>";
        }
    } else {
        // Mostrar alert si el tipoEspectaculo no existe
        echo "<script>
            alert('El tipo de espectáculo no existe en la base de datos.');
            window.location.href = 'index.php?mod=listaTipoEspectaculos';
        </script>";
    }
} else {
    // Mostrar alert de error si no se proporciona un ID válido
    echo "<script>
        alert('No se proporcionó un ID válido para el tipo de espectáculo.');
        window.location.href = 'index.php?mod=listaTipoEspectaculos';
    </script>";
}
?>
