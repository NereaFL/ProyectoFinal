document.addEventListener("DOMContentLoaded", function () {
    const fechaInicio = new Date(document.getElementById("fechaInicio").value);
    const fechaFin = new Date(document.getElementById("fechaFin").value);

    // Normalizar fechas
    fechaInicio.setHours(0, 0, 0, 0);
    fechaFin.setHours(0, 0, 0, 0);

    flatpickr("#datepicker", {
        inline: true,
        dateFormat: "d/m/Y",
        minDate: fechaInicio,
        maxDate: fechaFin,
        locale: "es"
    });
});
