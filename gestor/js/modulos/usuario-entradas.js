
    $(document).ready(function() {
        // Inicializar select2 en los campos de tipo de espectáculo y espectáculo
        $('#tipoEspectaculo').select2({
            placeholder: "Selecciona un tipo de espectáculo",
            allowClear: true
        });

        $('#espectaculo').select2({
            placeholder: "Selecciona un espectáculo",
            allowClear: true
        });
    });
