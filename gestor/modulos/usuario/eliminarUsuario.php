<?php
// Verificar si se envió el ID del usuario a eliminar
if (isset($_GET['id'])) {
    $idUsuario = $_GET['id'];

    // Preparar la consulta para eliminar al usuario
    $query = $conx->prepare("DELETE FROM usuario WHERE idUsuario = ?");
    $query->bind_param("i", $idUsuario);

    if ($query->execute()) {
        // Mostrar alert de éxito y redirigir al listado de usuarios
        echo "<script>
            window.location.href = 'index.php?mod=listaUsuarios';
        </script>";
    } else {
        // Mostrar alert de error y redirigir al listado de usuarios
        echo "<script>
            window.location.href = 'index.php?mod=listaUsuarios';
        </script>";
    }
} else {
    // Mostrar alert de error si no se proporciona un ID válido y redirigir
    echo "<script>
        window.location.href = 'index.php?mod=listaUsuarios';
    </script>";
}
?>
