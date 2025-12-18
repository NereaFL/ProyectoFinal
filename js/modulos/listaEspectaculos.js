$(document).ready(function() {
    // Destruir cualquier instancia previa de select2 antes de inicializarla
    if ($('.select2').hasClass("select2-hidden-accessible")) {
        $('.select2').select2('destroy');
    }

    // Inicializar select2
    $('.select2').select2({
        placeholder: "Selecciona un espectáculo",
        allowClear: true,
        width: '100%'
    });
});
