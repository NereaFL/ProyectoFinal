<?php
$query = "SELECT idSala, numeroSala, capacidad FROM sala";
$result = mysqli_query($conx, $query);
?>

<div class="page-header flex-wrap">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body text-center">
                <h4 class="card-title">Listado de Salas</h4>
                <a href="index.php?mod=crearSala" class="btn btn-success mb-3">Crear Sala</a>

                <div style="overflow-x: auto; position: relative;">
                    <!-- Barra de scroll horizontal superior -->
                    <div style="overflow-x: auto; height: 20px; position: absolute; top: 0; left: 0; right: 0;"></div>

                    <table class="table table-striped text-center" style="margin-top: 20px;">
                        <thead>
                            <tr>
                                <th class="text-center">ID</th>
                                <th class="text-center">Sala</th>
                                <th class="text-center">Capacidad</th>
                                <th class="text-center" colspan="2">Opciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result) > 0) { ?>
                                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                                    <tr>
                                        <td class="align-middle"><?php echo $row['idSala']; ?></td>
                                        <td class="align-middle">Sala <?php echo $row['numeroSala']; ?></td>
                                        <td class="align-middle"><?php echo $row['capacidad']; ?> personas</td>
                                        <td class="align-middle">
                                            <a href="index.php?mod=editarSala&id=<?php echo $row['idSala']; ?>" class="btn btn-warning btn-sm"><i class="mdi mdi-pencil"></i></a>
                                        </td>
                                        <td class="align-middle">
                                            <a href="index.php?mod=eliminarSala&id=<?php echo $row['idSala']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar esta sala?');"><i class="mdi mdi-delete-forever"></i></a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php } else { ?>
                                <tr>
                                    <td colspan="5" class="align-middle text-center">
                                        <div class="alert alert-danger mb-0">No hay salas registradas en el sistema.</div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>

                    <!-- Barra de scroll horizontal inferior -->
                    <div style="overflow-x: auto; height: 20px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
