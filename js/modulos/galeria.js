
    $(document).ready(function() {
        // Destruir antes por si acaso ya estaba inicializado
        if ($('.select2').hasClass("select2-hidden-accessible")) {
            $('.select2').select2('destroy');
            $('.select2').niceSelect('destroy'); 

        }

        $('.select2').select2({
            placeholder: "Selecciona un espectáculo",
            allowClear: true,
            width: '100%'
        });
    });

