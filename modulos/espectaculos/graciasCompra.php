<?php
$idCompra = $_GET['idCompra'] ?? null;

if (!$idCompra) {
    echo "Compra no encontrada.";
    exit;
}

// Consulta para obtener todos los datos
$sql = "
    SELECT 
        c.idCompra, c.fecha, c.hora, c.numeroEntradas, c.importeTotal,
        u.nombre AS nombreUsuario, u.apellidos, u.email, u.numeroTelefono,
        e.nombre AS nombreEspectaculo, e.duracion,
        s.numeroSala
    FROM compra c
    JOIN usuario u ON c.idUsuario = u.idUsuario
    JOIN espectaculo e ON c.idEspectaculo = e.idEspectaculo
    JOIN sala s ON e.sala = s.idSala
    WHERE c.idCompra = ?
";

$stmt = $conx->prepare($sql);
$stmt->bind_param("i", $idCompra);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Compra no encontrada.";
    exit;
}

$compra = $result->fetch_assoc();

// 🔄 Formatear fecha y duración
$fechaFormateada = date("d-m-Y", strtotime($compra['fecha']));

// Convertir duración de minutos a horas y minutos
$duracion = (int) $compra['duracion'];
$horas = floor($duracion / 60);
$minutos = $duracion % 60;
$duracionFormateada = "{$horas}h {$minutos}m";
?>

<!--================Breadcrumb Area =================-->
<section class="breadcrumb_area">
    <div class="overlay bg-parallax" data-background=""></div>
    <div class="container">
        <div class="page-cover text-start">
            <h2 class="page-cover-tittle">Gracias por su compra</h2>
            <ol class="breadcrumb">
                <li><a href="index.php">Inicio</a></li>
                <li class="active">Gracias por su compra</li>
            </ol>
        </div>
    </div>
</section>

<!--================ Cabecera de Agradecimiento ================-->
<section class="text-center mt-5">
    <h1 class="display-4 fw-bold">🎉 ¡Gracias por su compra!</h1>
    <p class="lead">A continuación encontrará el resumen de su compra:</p>
</section>

<!--================ Tabla con los datos =================-->
<section class="container my-5">
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th colspan="2" class="text-center">📋 Detalles de la Compra</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>Nombre del Usuario</th>
                    <td><?= htmlspecialchars($compra['nombreUsuario'] . ' ' . $compra['apellidos']) ?></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><?= htmlspecialchars($compra['email']) ?></td>
                </tr>
                <tr>
                    <th>Teléfono</th>
                    <td><?= htmlspecialchars($compra['numeroTelefono']) ?></td>
                </tr>
                <tr>
                    <th>Espectáculo</th>
                    <td><?= htmlspecialchars($compra['nombreEspectaculo']) ?></td>
                </tr>
                <tr>
                    <th>Sala</th>
                    <td>Sala <?= htmlspecialchars($compra['numeroSala']) ?></td>
                </tr>
                <tr>
                    <th>Duración</th>
                    <td><?= $duracionFormateada ?></td>
                </tr>
                <tr>
                    <th>Fecha</th>
                    <td><?= $fechaFormateada ?></td>
                </tr>

                <tr>
                    <th>Hora</th>
                    <td><?= htmlspecialchars($compra['hora']) ?></td>
                </tr>
                <tr>
                    <th>Número de Entradas</th>
                    <td><?= htmlspecialchars($compra['numeroEntradas']) ?></td>
                </tr>
                <tr>
                    <th>Importe Total</th>
                    <td><strong><?= number_format($compra['importeTotal'], 2) ?> €</strong></td>
                </tr>
            </tbody>
        </table>

        <!--================ Botones de acción =================-->
        <div class="text-center mt-4 d-flex justify-content-center gap-3 flex-wrap">
            <a href="pdf.php?idCompra=<?= urlencode($idCompra) ?>" target="_blank" class="btn btn-primary btn-lg">
                📄 Ver PDF de la Entrada
            </a>
            <a href="index.php?mod=usuario-espectaculos" class="btn btn-success btn-lg">
                🎟️ Ir a Mis Entradas
            </a>
        </div>

    </div>
</section>
