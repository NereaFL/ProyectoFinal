<?php
// Verificar si se envió el ID de la compra a eliminar
if (isset($_GET['id'])) {
    $idCompra = intval($_GET['id']); // Asegurar que el ID sea válido

    // Conexión a la base de datos
    require 'conexion.php';

    // Verificar si la compra existe antes de intentar eliminarla
    $queryCheck = $conx->prepare("SELECT * FROM compra WHERE idCompra = ?");
    $queryCheck->bind_param("i", $idCompra);
    $queryCheck->execute();
    $result = $queryCheck->get_result();

    if ($result->num_rows === 1) {
        $compra = $result->fetch_assoc();

        // Preparar la consulta para eliminar la compra
        $queryDelete = $conx->prepare("DELETE FROM compra WHERE idCompra = ?");
        $queryDelete->bind_param("i", $idCompra);

        if ($queryDelete->execute()) {
            // Mostrar alert de éxito y redirigir al listado de compras
            echo "<script>
                window.location.href = 'index.php?mod=listaCompras';
            </script>";
        } else {
            // Mostrar alert de error
            echo "<script>
                alert('Error al intentar eliminar la compra.');
                window.location.href = 'index.php?mod=listaCompras';
            </script>";
        }
    } else {
        // Mostrar alert si la compra no existe
        echo "<script>
            alert('La compra no existe en la base de datos.');
            window.location.href = 'index.php?mod=listaCompras';
        </script>";
    }
} else {
    // Mostrar alert de error si no se proporciona un ID válido
    echo "<script>
        alert('No se proporcionó un ID válido para la compra.');
        window.location.href = 'index.php?mod=listaCompras';
    </script>";
}
?>
