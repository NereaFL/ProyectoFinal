<!-- JQuery (necesario si otros scripts dependen de él) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap JS con funcionalidad completa -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<!-- Otros scripts generales -->
<script src="vendors/js/vendor.bundle.base.js"></script>
<script src="vendors/chart.js/Chart.min.js"></script>
<script src="vendors/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
<script src="vendors/flot/jquery.flot.js"></script>
<script src="vendors/flot/jquery.flot.resize.js"></script>
<script src="vendors/flot/jquery.flot.categories.js"></script>
<script src="vendors/flot/jquery.flot.fillbetween.js"></script>
<script src="vendors/flot/jquery.flot.stack.js"></script>
<script src="vendors/flot/jquery.flot.pie.js"></script>

<!-- Funcionalidades específicas -->
<script src="js/off-canvas.js"></script>
<script src="js/hoverable-collapse.js"></script>
<script src="js/misc.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>


<?php
    if (isset($_GET['mod']) && ($_GET['mod'] == 'crearEspectaculo' || $_GET['mod'] == 'editarEspectaculo' || $_GET['mod'] == 'listaEspectaculos')) {
        echo "<script src='js/modulos/crearEspectaculo.js'></script>";
    }

    if (isset($_GET['mod']) && ($_GET['mod'] == 'crearUsuario' || $_GET['mod'] == 'listaUsuarios' || $_GET['mod'] == 'editarUsuario')) {
        echo "<script src='js/modulos/crearUsuario.js'></script>";
    }

    if (isset($_GET['mod']) && $_GET['mod'] == 'listaCompras') {
        echo "<script src='js/modulos/listaCompras.js'></script>";
    }

    if (isset($_GET['mod']) && $_GET['mod'] == 'listaFotos') {
        echo "<script src='js/modulos/listaFotos.js'></script>";
    }

    if (isset($_GET['mod']) && $_GET['mod'] == 'dashboard') {
        echo "<script src='js/dashboard.js'></script>";
    }
?>

