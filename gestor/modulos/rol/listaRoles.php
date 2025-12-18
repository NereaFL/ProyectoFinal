<?php
$query = "SELECT idRol, tipoRol FROM rol";
$result = mysqli_query($conx, $query);
?>

<div class="page-header flex-wrap">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body text-center">
                <h4 class="card-title">Listado de Roles</h4>
                <a href="index.php?mod=crearRol" class="btn btn-success mb-3">Crear Rol</a>

                <div style="overflow-x: auto; position: relative;">
                    <!-- Barra de scroll horizontal superior -->
                    <div style="overflow-x: auto; height: 20px; position: absolute; top: 0; left: 0; right: 0;"></div>

                    <table class="table table-striped text-center" style="margin-top: 20px;">
                        <thead>
                            <tr>
                                <th class="text-center">ID</th>
                                <th class="text-center">Tipo de Rol</th>
                                <th class="text-center" colspan="2">Opciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result) > 0) { ?>
                                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                                    <tr>
                                        <td class="align-middle"><?php echo $row['idRol']; ?></td>
                                        <td class="align-middle"><?php echo $row['tipoRol']; ?></td>
                                        <td class="align-middle">
                                            <a href="index.php?mod=editarRol&id=<?php echo $row['idRol']; ?>" class="btn btn-warning btn-sm"><i class="mdi mdi-pencil"></i></a>
                                        </td>
                                        <td class="align-middle">
                                            <a href="index.php?mod=eliminarRol&id=<?php echo $row['idRol']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este rol?');"><i class="mdi mdi-delete-forever"></i></a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php } else { ?>
                                <tr>
                                    <td colspan="4" class="align-middle text-center">
                                        <div class="alert alert-danger mb-0">No hay roles registrados en el sistema.</div>
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
