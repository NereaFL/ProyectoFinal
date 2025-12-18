<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="js/popper.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="vendors/owl-carousel/owl.carousel.min.js"></script>
<script src="js/jquery.ajaxchimp.min.js"></script>
<script src="js/mail-script.js"></script>
<script src="vendors/bootstrap-datepicker/bootstrap-datetimepicker.min.js"></script>
<script src="js/mail-script.js"></script>
<script src="js/stellar.js"></script>
<script src="vendors/lightbox/simpleLightbox.min.js"></script>
<script src="js/custom.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<!-- Flatpickr -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>



<?php

if (isset($_GET['mod']) && $_GET['mod'] == 'galeria') {
    echo '<script src="js/modulos/galeria.js"></script>';
}

if (isset($_GET['mod']) && $_GET['mod'] == 'listaEspectaculos') {
    echo '<script src="js/modulos/listaEspectaculos.js"></script>';
}

if (isset($_GET['mod']) && $_GET['mod'] == 'espectaculo-detalles') {
    echo '<script src="js/modulos/detallesEspectaculo.js"></script>';
}

if (isset($_GET['mod']) && $_GET['mod'] == 'espectaculo-fecha') {
    echo '<script src="js/modulos/fechaEspectaculo.js"></script>';
}

if (isset($_GET['mod']) && $_GET['mod'] == 'entradasEspectaculo') {
    echo '<script src="js/modulos/entradasEspectaculo.js"></script>';
}

if (isset($_GET['mod']) && $_GET['mod'] == 'usuario-espectaculos') {
    echo '<script src="js/modulos/usuario-entradas.js"></script>';
}

if (isset($_GET['mod']) && $_GET['mod'] == 'pagoEspectaculo') {
    echo '<script src="https://js.stripe.com/v3/"></script>';
    echo '<script src="js/modulos/stripe.js"></script>';
}

?>