document.addEventListener('DOMContentLoaded', function () {
    const precioElement = document.getElementById('precioUnidad');
    const input = document.getElementById('numeroEntradas');
    const cantidad = document.getElementById('cantidadEntradas');
    const total = document.getElementById('totalPagar');

    if (!precioElement || !input || !cantidad || !total) {
        console.warn("⚠️ Elementos del recibo no encontrados.");
        return;
    }

    const precioUnitario = parseFloat(precioElement.textContent.replace(',', '.'));

    function actualizarRecibo() {
        const entradas = parseInt(input.value) || 1;
        const totalPagar = (precioUnitario * entradas).toFixed(2);
        cantidad.textContent = entradas;
        total.textContent = totalPagar + ' €';
    }

    input.addEventListener('input', actualizarRecibo);
    actualizarRecibo(); // por si hay un valor inicial
});
