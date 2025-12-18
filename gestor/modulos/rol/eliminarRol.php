<?php
// Verificar si se envió el ID del rol a eliminar
if (isset($_GET['id'])) {
    $idRol = intval($_GET['id']); // Asegurar que el ID sea un número entero válido

    // Conexión a la base de datos
    require 'conexion.php';

    // Verificar si el rol existe antes de intentar eliminarlo
    $queryCheck = $conx->prepare("SELECT tipoRol FROM rol WHERE idRol = ?");
    $queryCheck->bind_param("i", $idRol);
    $queryCheck->execute();
    $result = $queryCheck->get_result();

    if ($result->num_rows === 1) {
        $rol = $result->fetch_assoc();

        // Preparar la consulta para eliminar el rol
        $queryDelete = $conx->prepare("DELETE FROM rol WHERE idRol = ?");
        $queryDelete->bind_param("i", $idRol);

        if ($queryDelete->execute()) {
            // Mostrar alert de éxito y redirigir al listado de roles
            echo "<script>
                window.location.href = 'index.php?mod=listaRoles';
            </script>";
        } else {
            // Mostrar alert de error al intentar eliminar
            echo "<script>
                alert('Error al intentar eliminar el rol.');
                window.location.href = 'index.php?mod=listaRoles';
            </script>";
        }
    } else {
        // Mostrar alert si el rol no existe
        echo "<script>
            alert('El rol no existe en la base de datos.');
            window.location.href = 'index.php?mod=listaRoles';
        </script>";
    }
} else {
    // Mostrar alert de error si no se proporciona un ID válido
    echo "<script>
        alert('No se proporcionó un ID válido para el rol.');
        window.location.href = 'index.php?mod=listaRoles';
    </script>";
}
?>
