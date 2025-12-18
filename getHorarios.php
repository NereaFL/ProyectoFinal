<?php
file_put_contents('debug.log', print_r($_POST, true), FILE_APPEND); // Para depurar POST

include("conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha = mysqli_real_escape_string($conx, $_POST['fecha']); // Fecha seleccionada
    $idEspectaculo = (int)$_POST['idEspectaculo']; // ID del espectáculo

    // Consulta para obtener la capacidad de la sala y los horarios del espectáculo
    $sql = "
        SELECT e.horarios, s.capacidad
        FROM espectaculo e
        JOIN sala s ON e.sala = s.idSala
        WHERE e.idEspectaculo = $idEspectaculo
    ";

    $result = mysqli_query($conx, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        $horarios = explode(",", $data['horarios']); // Convertir horarios a array
        $capacidad = (int)$data['capacidad']; // Capacidad de la sala

        $response = ""; // HTML de respuesta

        // Iterar sobre los horarios para calcular la disponibilidad
        foreach ($horarios as $hora) {
            $hora = htmlspecialchars(trim($hora)); // Limpiar y formatear la hora

            // Consulta para calcular cuántas entradas ya se han comprado para esa fecha y hora específica
            $sql_hora = "
                SELECT SUM(c.numeroEntradas) AS entradas_compradas_hora
                FROM compra c
                WHERE c.idEspectaculo = $idEspectaculo AND c.fecha = '$fecha' AND c.hora = '$hora'
            ";

            $result_hora = mysqli_query($conx, $sql_hora);
            $entradasCompradasHora = 0;

            if ($result_hora && mysqli_num_rows($result_hora) > 0) {
                $row_hora = mysqli_fetch_assoc($result_hora);
                $entradasCompradasHora = (int)($row_hora['entradas_compradas_hora'] ?? 0);
            }

            // Calcular cuántas entradas quedan disponibles para esa hora
            $entradasDisponiblesHora = $capacidad - $entradasCompradasHora;

            // Crear el HTML para mostrar las horas disponibles o no
            if ($entradasDisponiblesHora > 0) {
                $response .= "
                    <li class='hora-disponible' data-hora='$hora' style='margin-bottom: 10px; cursor: pointer;'>
                        <div style='border: 1px solid green; padding: 10px; border-radius: 5px; text-align: center;'>
                            🕒 $hora <br>
                            <span style='color: green;'>Entradas disponibles: $entradasDisponiblesHora</span>
                        </div>
                    </li>";
            } else {
                $response .= "
                    <li class='hora-no-disponible' style='margin-bottom: 10px; cursor: not-allowed;'>
                        <div style='border: 1px solid red; padding: 10px; border-radius: 5px; text-align: center; background-color: #f8d7da;'>
                            🕒 $hora <br>
                            <span style='color: red;'>No disponible</span>
                        </div>
                    </li>";
            }
        }

        echo $response; // Devolver la respuesta HTML al cliente
    } else {
        // No hay horarios disponibles
        echo "<p style='color: red;'>No hay horarios disponibles para esta fecha.</p>";
    }
}
?>
