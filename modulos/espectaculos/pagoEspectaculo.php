<?php
require 'conexion.php';
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$idUsuario = $_SESSION['usuario'];
$idEspectaculo = $_POST['idEspectaculo'] ?? null;
$fecha = $_POST['fecha'] ?? null;
$hora = $_POST['hora'] ?? null;
$numeroEntradas = $_POST['numeroEntradas'] ?? 1;

if (!$idEspectaculo || !$fecha || !$hora || !$numeroEntradas) {
    echo "Faltan datos de la compra.";
    exit;
}

// Obtener datos del espectáculo
$sql = "
    SELECT 
        e.idEspectaculo, e.nombre, e.fecha_inicio, e.fecha_fin, e.horarios,
        te.tipoEspectaculo, s.capacidad, s.numeroSala, s.idSala, e.precio
    FROM espectaculo e
    JOIN tipoEspectaculo te ON e.tipoEspectaculo = te.idTipoEspectaculo
    JOIN sala s ON e.sala = s.idSala
    WHERE e.idEspectaculo = $idEspectaculo
";
$result = mysqli_query($conx, $sql);
$espectaculo = mysqli_fetch_assoc($result);

// Imagen
$sql_imagen = "
    SELECT f.nombre FROM foto f
    WHERE f.idEspectaculo = $idEspectaculo AND f.portada = 1 LIMIT 1
";
$img_result = mysqli_query($conx, $sql_imagen);
$imagePath = 'public/default.jpg';
if ($img_result && mysqli_num_rows($img_result) > 0) {
    $img = mysqli_fetch_assoc($img_result);
    $imagePath = 'public/' . $img['nombre'];
}

// Usuario
$sql_user = "SELECT nombre, apellidos, email FROM usuario WHERE idUsuario = ?";
$stmt = $conx->prepare($sql_user);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Cálculo de importe total
$precioUnitario = $espectaculo['precio'];
$importeTotal = $numeroEntradas * $precioUnitario;
?>

<!--================Breadcrumb Area =================-->
<section class="breadcrumb_area">
    <div class="overlay bg-parallax" data-background=""></div>
    <div class="container">
        <div class="page-cover text-start">
            <h2 class="page-cover-tittle">Confirmar Compra</h2>
            <ol class="breadcrumb">
                <li><a href="index.php">Inicio</a></li>
                <li class="active">Confirmación</li>
            </ol>
        </div>
    </div>
</section>

<!-- Cabecera del espectáculo -->
<section class="espectaculo-card">
    <div class="card-espectaculo fade-in-up">
        <div class="card-img" style="background-image: url('<?php echo $imagePath; ?>');"></div>
        <div class="card-content">
            <h2 class="espectaculo-nombre"><?php echo htmlspecialchars($espectaculo['nombre']); ?></h2>
            <p class="espectaculo-tipo"><?php echo htmlspecialchars($espectaculo['tipoEspectaculo']); ?></p>
        </div>
    </div>
</section>

<!-- Detalles de la confirmación -->
<section class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th colspan="2">📄 Confirmación de Compra</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>Usuario</th>
                            <td><?php echo htmlspecialchars($user['nombre'] . ' ' . $user['apellidos']); ?></td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                        </tr>
                        <tr>
                            <th>Espectáculo</th>
                            <td><?php echo htmlspecialchars($espectaculo['nombre']); ?></td>
                        </tr>
                        <tr>
                            <th>Fecha</th>
                            <td><?php echo htmlspecialchars($fecha); ?></td>
                        </tr>
                        <tr>
                            <th>Hora</th>
                            <td><?php echo htmlspecialchars($hora); ?></td>
                        </tr>
                        <tr>
                            <th>Número de entradas</th>
                            <td><?php echo htmlspecialchars($numeroEntradas); ?></td>
                        </tr>
                        <tr>
                            <th>Precio por entrada</th>
                            <td><?php echo number_format($precioUnitario, 2); ?> €</td>
                        </tr>
                        <tr>
                            <th>Importe total</th>
                            <td><strong><?php echo number_format($importeTotal, 2); ?> €</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Formulario para redirigir a procesarPago.php -->
            <form action="procesarPago.php" method="POST" class="text-center mt-4">
                <input type="hidden" name="idEspectaculo" value="<?php echo $idEspectaculo; ?>">
                <input type="hidden" name="fecha" value="<?php echo htmlspecialchars($fecha); ?>">
                <input type="hidden" name="hora" value="<?php echo htmlspecialchars($hora); ?>">
                <input type="hidden" name="numeroEntradas" value="<?php echo htmlspecialchars($numeroEntradas); ?>">
                <input type="hidden" name="importeTotal" value="<?php echo htmlspecialchars($importeTotal); ?>">
                <button type="button" class="btn btn-success btn-lg rounded-pill" data-bs-toggle="modal" data-bs-target="#paypalModal">
                    💳 Pagar con PayPal
                </button>

            </form>
        </div>
    </div>
</section>

<!-- Modal -->
<div class="modal fade" id="paypalModal" tabindex="-1" aria-labelledby="paypalModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content p-4">
      <div class="modal-header">
        <h5 class="modal-title" id="paypalModalLabel">Confirmar y Pagar con PayPal</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <p>Vas a pagar <strong><?php echo number_format($importeTotal, 2); ?> €</strong> por <strong><?php echo htmlspecialchars($numeroEntradas); ?></strong> entradas.</p>
        <div id="paypal-button-container"></div>
      </div>
    </div>
  </div>
</div>

<!-- SDK de PayPal (usa tu client-id sandbox o live según entorno) -->
<script src="https://www.paypal.com/sdk/js?client-id=AdcjPmQnjCXJZT7Ye5bBYA6ib4Eh87K0jOPXZk4p2ChexfMvdJ3lsV-OmobiHpFsA3zEz6cJ-O2GaNJ6&currency=EUR"></script>

<script>
    paypal.Buttons({
        style: {
            layout: 'vertical',
            color: 'blue',
            shape: 'rect',
            label: 'paypal'
        },
        createOrder: function(data, actions) {
            return actions.order.create({
                purchase_units: [{
                    amount: {
                        value: '<?php echo number_format($importeTotal, 2, '.', ''); ?>'
                    },
                    description: '<?php echo addslashes($espectaculo['nombre']); ?>'
                }]
            });
        },
        onApprove: function(data, actions) {
            return actions.order.capture().then(function(details) {
                // Puedes mostrar un mensaje de éxito si deseas
                console.log('Pago aprobado por: ' + details.payer.name.given_name);

                // Crear y enviar el formulario oculto a procesarPago.php
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'procesarPago.php';

                const fields = {
                    idEspectaculo: '<?php echo $idEspectaculo; ?>',
                    fecha: '<?php echo htmlspecialchars($fecha); ?>',
                    hora: '<?php echo htmlspecialchars($hora); ?>',
                    numeroEntradas: '<?php echo htmlspecialchars($numeroEntradas); ?>',
                    importeTotal: '<?php echo number_format($importeTotal, 2, '.', ''); ?>',
                    paypalTransactionId: details.id
                };

                for (let key in fields) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = fields[key];
                    form.appendChild(input);
                }

                document.body.appendChild(form);
                form.submit();
            });
        },
        onCancel: function(data) {
            alert("Pago cancelado.");
        },
        onError: function(err) {
            console.error("Error en el pago:", err);
            alert("Ocurrió un error al procesar el pago.");
        }
    }).render('#paypal-button-container');
</script>
