<div class="page-header flex-wrap">
  <h3 class="mb-0">Hola, ¡Bienvenido de nuevo!</h3>
</div>
<?php
// Consultas SQL para obtener los totales
$query_usuarios = $conx->query("SELECT COUNT(*) AS total_usuarios FROM usuario");
$total_usuarios = $query_usuarios->fetch_assoc()['total_usuarios'];

$query_espectaculos = $conx->query("SELECT COUNT(*) AS total_espectaculos FROM espectaculo");
$total_espectaculos = $query_espectaculos->fetch_assoc()['total_espectaculos'];

$query_salas = $conx->query("SELECT COUNT(*) AS total_salas FROM sala");
$total_salas = $query_salas->fetch_assoc()['total_salas'];

$query_compras = $conx->query("SELECT COUNT(*) AS total_compras FROM compra");
$total_compras = $query_compras->fetch_assoc()['total_compras'];
?>

<div class="row">
  <div class="col-xl-3 col-lg-12 stretch-card grid-margin">
    <div class="row">
      <!-- Total de Usuarios -->
      <div class="col-xl-12 col-md-6 stretch-card grid-margin grid-margin-sm-0 pb-sm-3">
        <div class="card bg-warning">
          <div class="card-body px-3 py-4">
            <div class="d-flex justify-content-between align-items-start">
              <div class="color-card">
                <p class="mb-0 color-card-head">Total Usuarios</p>
                <h2 class="text-white"><?= $total_usuarios ?></h2>
              </div>
              <i class="card-icon-indicator mdi mdi-account-circle bg-inverse-icon-warning"></i>
            </div>
            <h6 class="text-white">Registrados en el sistema</h6>
          </div>
        </div>
      </div>

      <!-- Total de Espectáculos -->
      <div class="col-xl-12 col-md-6 stretch-card grid-margin grid-margin-sm-0 pb-sm-3">
        <div class="card bg-danger">
          <div class="card-body px-3 py-4">
            <div class="d-flex justify-content-between align-items-start">
              <div class="color-card">
                <p class="mb-0 color-card-head">Total Espectáculos</p>
                <h2 class="text-white"><?= $total_espectaculos ?></h2>
              </div>
              <i class="card-icon-indicator mdi mdi-ticket bg-inverse-icon-danger"></i>
            </div>
            <h6 class="text-white">Disponibles en el sistema</h6>
          </div>
        </div>
      </div>

      <!-- Total de Salas -->
      <div class="col-xl-12 col-md-6 stretch-card grid-margin grid-margin-sm-0 pb-sm-3 pb-lg-0 pb-xl-3">
        <div class="card bg-primary">
          <div class="card-body px-3 py-4">
            <div class="d-flex justify-content-between align-items-start">
              <div class="color-card">
                <p class="mb-0 color-card-head">Total Salas</p>
                <h2 class="text-white"><?= $total_salas ?></h2>
              </div>
              <i class="card-icon-indicator mdi mdi-door-open bg-inverse-icon-primary"></i>
            </div>
            <h6 class="text-white">Disponibles en el sistema</h6>
          </div>
        </div>
      </div>

      <!-- Total de Compras -->
      <div class="col-xl-12 col-md-6 stretch-card pb-sm-3 pb-lg-0">
        <div class="card bg-success">
          <div class="card-body px-3 py-4">
            <div class="d-flex justify-content-between align-items-start">
              <div class="color-card">
                <p class="mb-0 color-card-head">Total Compras</p>
                <h2 class="text-white"><?= $total_compras ?></h2>
              </div>
              <i class="card-icon-indicator mdi mdi-cart bg-inverse-icon-success"></i>
            </div>
            <h6 class="text-white">Realizadas en el sistema</h6>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php
// Conexión a la base de datos
require 'conexion.php';

// Determinar si el usuario eligió "Mes" o "Semana"
$filtro = "mes"; // Por defecto, mostrar ventas por mes
if (isset($_GET['filtro'])) {
    $filtro = $_GET['filtro'];
}

// Consulta SQL para obtener las ventas por mes o por semana
if ($filtro === "mes") {
    $query = "
        SELECT DATE_FORMAT(c.fecha, '%Y-%m') AS periodo, COUNT(c.idCompra) AS total_ventas
        FROM compra c
        INNER JOIN espectaculo e ON c.idEspectaculo = e.idEspectaculo
        GROUP BY DATE_FORMAT(c.fecha, '%Y-%m')
        ORDER BY periodo ASC
    ";
} else {
    $query = "
        SELECT DATE_FORMAT(c.fecha, '%Y-%U') AS periodo, COUNT(c.idCompra) AS total_ventas
        FROM compra c
        INNER JOIN espectaculo e ON c.idEspectaculo = e.idEspectaculo
        GROUP BY DATE_FORMAT(c.fecha, '%Y-%U')
        ORDER BY periodo ASC
    ";
}

$result = $conx->query($query);
$labels = [];
$data = [];

while ($row = $result->fetch_assoc()) {
    $labels[] = $row['periodo'];
    $data[] = $row['total_ventas'];
}
?>

<div class="col-xl-9 stretch-card grid-margin">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-sm-7">
                    <h5>Ventas</h5>
                    <p class="text-muted">
                        Mostrar ventas por:
                        <a href="?filtro=mes" class="btn btn-sm <?= $filtro === 'mes' ? 'btn-primary' : 'btn-outline-primary' ?>">Mes</a>
                        <a href="?filtro=semana" class="btn btn-sm <?= $filtro === 'semana' ? 'btn-primary' : 'btn-outline-primary' ?>">Semana</a>
                    </p>
                </div>
            </div>
            <div class="row my-3">
                <div class="col-sm-12">
                    <div class="flot-chart-wrapper">
                        <canvas id="ventasChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Incluir Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Datos para el gráfico
    const labels = <?= json_encode($labels) ?>;
    const data = <?= json_encode($data) ?>;

    // Configuración del gráfico
    const ctx = document.getElementById('ventasChart').getContext('2d');
    const ventasChart = new Chart(ctx, {
        type: 'line', // Tipo de gráfico (puedes cambiarlo a 'bar' si prefieres)
        data: {
            labels: labels,
            datasets: [{
                label: 'Ventas',
                data: data,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
<?php

// Consulta SQL para obtener los espectáculos disponibles para hoy
$query = "
    SELECT 
        e.idEspectaculo,
        e.nombre AS nombreEspectaculo,
        te.tipoEspectaculo AS tipoEspectaculo,
        s.numeroSala AS numeroSala,
        e.fecha_inicio,
        e.fecha_fin,
        e.horarios,
        e.duracion
    FROM espectaculo e
    JOIN tipoEspectaculo te ON e.tipoEspectaculo = te.idTipoEspectaculo
    JOIN sala s ON e.sala = s.idSala
    WHERE CURDATE() BETWEEN e.fecha_inicio AND IFNULL(e.fecha_fin, CURDATE())  -- Filtrar espectáculos activos hoy
    ORDER BY e.fecha_inicio ASC;  
";

$result = mysqli_query($conx, $query);

// Función para formatear la hora en HH:mm
function format_time($time) {
    return date('H:i', strtotime($time));
}

// Función para calcular la hora de finalización sumando la duración
function calcular_hora_fin($hora_inicio, $duracion) {
    return date('H:i', strtotime($hora_inicio) + ($duracion * 60));
}

?>
</div>

<!-- Tabla de Espectáculos Disponibles para Hoy -->
<div class="row">
    <div class="col-xl-12 col-sm-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body px-0" style="position: relative; overflow-x: auto;">
                <h4 class="card-title pl-4">Espectáculos Disponibles para Hoy</h4>
                
                <!-- Barra de scroll horizontal superior -->
                <div style="overflow-x: auto; height: 20px; position: absolute; top: 0; left: 0; right: 0;"></div>

                <div class="table-responsive">
                    <table class="table w-100" style="margin-top: 20px;">
                        <thead class="bg-light">
                            <tr>
                                <th>Nombre</th>
                                <th>Tipo</th>
                                <th>Sala</th>
                                <th>Hora Inicio</th>
                                <th>Hora Fin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result) > 0) { ?>
                                <?php while ($row = mysqli_fetch_assoc($result)) { 
                                    // Obtener el primer horario disponible
                                    $horarios = explode(',', $row['horarios']);
                                    $hora_inicio = isset($horarios[0]) ? trim($horarios[0]) : null;
                                    $hora_fin = $hora_inicio ? calcular_hora_fin($hora_inicio, $row['duracion']) : null;
                                ?>
                                    <tr>
                                        <td><?= $row['nombreEspectaculo'] ?></td>
                                        <td><?= $row['tipoEspectaculo'] ?></td>
                                        <td><?= $row['numeroSala'] ?></td>
                                        <td><?= $hora_inicio ? format_time($hora_inicio) : 'N/A' ?></td>
                                        <td><?= $hora_fin ? format_time($hora_fin) : 'N/A' ?></td>
                                    </tr>
                                <?php } ?>
                            <?php } else { ?>
                                <tr>
                                    <td colspan="5" class="text-center">
                                        <div class="alert alert-danger mb-0">No hay espectáculos disponibles para hoy.</div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                <!-- Barra de scroll horizontal inferior -->
                <div style="overflow-x: auto; height: 20px;"></div>

                <a class="text-black mt-3 d-block pl-4" href="index.php?mod=listaEspectaculos">
                    <span class="font-weight-medium h6">Ver todos los espectáculos</span>
                    <i class="mdi mdi-chevron-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>

  