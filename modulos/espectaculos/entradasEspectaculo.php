<?php
require 'conexion.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$idUsuario = $_SESSION['usuario'];
$idEspectaculo = isset($_GET['idEspectaculo']) ? (int)$_GET['idEspectaculo'] : 0;
$fecha = $_POST['fecha'] ?? null;
$hora = $_POST['hora'] ?? null;

if (!$idEspectaculo || !$fecha || !$hora) {
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

// Entradas disponibles
$sql_compras = "SELECT SUM(numeroEntradas) AS total FROM compra WHERE idEspectaculo = $idEspectaculo AND fecha = ? AND hora = ?";
$stmt = $conx->prepare($sql_compras);
$stmt->bind_param("ss", $fecha, $hora);
$stmt->execute();
$result_compras = $stmt->get_result()->fetch_assoc();
$vendidas = $result_compras['total'] ?? 0;
$disponibles = $espectaculo['capacidad'] - $vendidas;
?>

<!--================Breadcrumb Area =================-->
<section class="breadcrumb_area">
    <div class="overlay bg-parallax" data-background=""></div>
    <div class="container">
        <div class="page-cover text-start">
            <h2 class="page-cover-tittle">Compra de Entradas</h2>
            <ol class="breadcrumb">
                <li><a href="index.php">Inicio</a></li>
                <li class="active">Entradas</li>
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

<!-- Detalles de la compra -->
<section class="container my-5">
    <div class="row g-4">
        <!-- Tabla informativa -->
        <div class="col-md-7">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th colspan="2">🧾 Detalles de la Compra</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>Nombre</th>
                            <td><?php echo htmlspecialchars($user['nombre'] . ' ' . $user['apellidos']); ?></td>
                        </tr>
                        <tr>
                            <th>Correo</th>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                        </tr>
                        <tr>
                            <th>Fecha seleccionada</th>
                            <td><?php echo htmlspecialchars($fecha); ?></td>
                        </tr>
                        <tr>
                            <th>Hora seleccionada</th>
                            <td><?php echo htmlspecialchars($hora); ?></td>
                        </tr>
                        <tr>
                            <th>Sala</th>
                            <td><?php echo 'Sala ' . $espectaculo['numeroSala']; ?></td>
                        </tr>
                        <tr>
                            <th>Capacidad</th>
                            <td><?php echo $espectaculo['capacidad']; ?></td>
                        </tr>
                        <tr>
                            <th>Entradas disponibles</th>
                            <td><?php echo $disponibles; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tabla "Recibo" y formulario -->
        <div class="col-md-5">
            <div class="table-responsive mb-4">
                <table class="table table-bordered align-middle text-center" id="tabla-recibo">
                    <thead class="table-dark">
                        <tr>
                            <th colspan="2">🧾 Recibo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>Precio por entrada</th>
                            <td id="precioUnidad"><?php echo number_format($espectaculo['precio'], 2); ?> €</td>
                        </tr>
                        <tr>
                            <th>Número de entradas</th>
                            <td><span id="cantidadEntradas">1</span></td>
                        </tr>
                        <tr>
                            <th>Total a pagar</th>
                            <td><strong id="totalPagar"><?php echo number_format($espectaculo['precio'], 2); ?> €</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Formulario para el pago -->
            <div class="form-container">
                <form action="index.php?mod=pagoEspectaculo" method="POST">
                    <!-- ID del espectáculo -->
                    <input type="hidden" name="idEspectaculo" value="<?php echo $idEspectaculo; ?>">

                    <!-- Fecha y hora (ya recibidas por POST) -->
                    <input type="hidden" name="fecha" value="<?php echo htmlspecialchars($fecha); ?>">
                    <input type="hidden" name="hora" value="<?php echo htmlspecialchars($hora); ?>">

                    <!-- Número de entradas -->
                    <div class="form-group d-flex flex-column align-items-center">
                        <label for="numeroEntradas" class="form-label fw-bold text-center w-100">🎟️ Número de entradas:</label>
                        <input
                            type="number"
                            id="numeroEntradas"
                            name="numeroEntradas"
                            class="form-control text-center w-100"
                            style="max-width: 90%;"
                            min="1"
                            max="<?php echo $disponibles; ?>"
                            value="1"
                            required>
                    </div>

                    <!-- Botón de envío -->
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary btn-lg rounded-pill">Ir al pago</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<style>
    .card-espectaculo {
        display: flex;
        background: #fff;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        margin: 30px auto;
        max-width: 1000px;
    }

    .card-img {
        flex: 1;
        background-size: cover;
        background-position: center;
        height: 250px;
    }

    .card-content {
        padding: 20px;
        flex: 2;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .fade-in-up {
        animation: fadeInUp 0.8s ease forwards;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .form-container {
        margin-top: 20px;
    }
</style>
