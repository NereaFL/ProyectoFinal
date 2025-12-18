<?php
// Verificar si se envió el ID de la sala a eliminar
if (isset($_GET['id'])) {
    $idSala = intval($_GET['id']); // Asegurar que el ID sea válido

    // Conexión a la base de datos
    require 'conexion.php';

    // Verificar si la sala existe antes de intentar eliminarla
    $queryCheck = $conx->prepare("SELECT numeroSala, capacidad FROM sala WHERE idSala = ?");
    $queryCheck->bind_param("i", $idSala);
    $queryCheck->execute();
    $result = $queryCheck->get_result();

    if ($result->num_rows === 1) {
        $sala = $result->fetch_assoc();

        // Preparar la consulta para eliminar la sala
        $queryDelete = $conx->prepare("DELETE FROM sala WHERE idSala = ?");
        $queryDelete->bind_param("i", $idSala);

        if ($queryDelete->execute()) {
            // Mostrar alert de éxito y redirigir al listado de salas
            echo "<script>
                window.location.href = 'index.php?mod=listaSalas';
            </script>";
        } else {
            // Mostrar alert de error
            echo "<script>
                alert('Error al intentar eliminar la sala.');
                window.location.href = 'index.php?mod=listaSalas';
            </script>";
        }
    } else {
        // Mostrar alert si la sala no existe
        echo "<script>
            alert('La sala no existe en la base de datos.');
            window.location.href = 'index.php?mod=listaSalas';
        </script>";
    }
} else {
    // Mostrar alert de error si no se proporciona un ID válido
    echo "<script>
        alert('No se proporcionó un ID válido para la sala.');
        window.location.href = 'index.php?mod=listaSalas';
    </script>";
}
?>
