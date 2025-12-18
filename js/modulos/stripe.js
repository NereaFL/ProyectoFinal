document.addEventListener('DOMContentLoaded', () => {
    const stripe = Stripe('pk_test_51RC3OuB9jeL4LPDywXfnbGAP1xAofUkouqMdFqpGaC1Pf6gc0PsErsx1arxRTd898MfLjcmuHdXJ1RSLRvSK0uIX002Vb3oQ3A');
    const elements = stripe.elements();

    const card = elements.create('card', { hidePostalCode: true });
    card.mount('#card-element');

    const form = document.getElementById('payment-form');
    const cardErrors = document.getElementById('card-errors');

    card.on('change', (event) => {
        if (event.error) {
            cardErrors.textContent = event.error.message;
        } else {
            cardErrors.textContent = '';
        }
    });

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        const numeroEntradas = document.getElementById('numeroEntradas').value;
        const importeTotal = numeroEntradas * parseFloat(document.getElementById('precioUnidad').textContent);

        document.getElementById('importeTotalInput').value = importeTotal.toFixed(2);

        try {
            const response = await fetch('procesarPago.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    idEspectaculo: document.querySelector('input[name="idEspectaculo"]').value,
                    fecha: document.querySelector('input[name="fecha"]').value,
                    hora: document.querySelector('input[name="hora"]').value,
                    numeroEntradas,
                    importeTotal,
                }),
            });

            const data = await response.json();

            if (data.error) {
                cardErrors.textContent = data.error;
                return;
            }

            const { error } = await stripe.confirmCardPayment(data.clientSecret, {
                payment_method: { card },
            });

            if (error) {
                cardErrors.textContent = error.message;
            } else {
                window.location.href = "index.php?mod=graciasCompra"; // Redirección al finalizar el pago
            }
        } catch (err) {
            cardErrors.textContent = 'Error de conexión con el servidor.';
        }
    });
});
