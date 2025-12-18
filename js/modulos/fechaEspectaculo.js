document.addEventListener("DOMContentLoaded", function () {
    const fechaInicio = document.getElementById("fechaInicio").value;
    const fechaFin = document.getElementById("fechaFin").value;
    const idEspectaculo = document.getElementById("idEspectaculo").value;
    const fechaHoy = new Date().toISOString().split('T')[0];
    const botonSiguiente = document.getElementById("botonSiguiente");
    let fechaSeleccionada = null;
    let horaSeleccionada = null;

    const fechaMinima = (new Date(fechaInicio) < new Date(fechaHoy)) ? fechaHoy : fechaInicio;

    // Configurar el calendario con Flatpickr
    flatpickr("#datepicker", {
        inline: true,
        dateFormat: "Y-m-d",
        minDate: fechaMinima,
        maxDate: fechaFin,
        locale: "es",
        onChange: function (selectedDates, dateStr) {
            fechaSeleccionada = dateStr;
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "getHorarios.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onload = function () {
                if (xhr.status === 200) {
                    const horariosList = document.getElementById("horarios-list");
                    horariosList.innerHTML = xhr.responseText;
                    activarSeleccionHora();
                    desactivarBoton();
                }
            };
            xhr.onerror = function () {
                console.error("Hubo un problema con la solicitud AJAX.");
            };
            xhr.send("fecha=" + dateStr + "&idEspectaculo=" + encodeURIComponent(idEspectaculo));
        }
    });

    function activarSeleccionHora() {
        const horasDisponibles = document.querySelectorAll(".hora-disponible");
        horasDisponibles.forEach(hora => {
            hora.addEventListener("click", function () {
                // Limpiar selección previa
                horasDisponibles.forEach(h => {
                    h.classList.remove("selected");
                    h.querySelector("div").style.backgroundColor = "";
                    const check = h.querySelector(".check-symbol");
                    if (check) check.remove();
                });

                // Seleccionar esta hora
                this.classList.add("selected");
                horaSeleccionada = this.dataset.hora; // Guardar hora seleccionada
                const horaDiv = this.querySelector("div");
                horaDiv.style.backgroundColor = "lightgreen";
                const checkSymbol = document.createElement("span");
                checkSymbol.classList.add("check-symbol");
                checkSymbol.textContent = " ✅";
                horaDiv.appendChild(checkSymbol);

                // Activar el botón "Siguiente"
                activarBoton();
            });
        });
    }

    function activarBoton() {
        botonSiguiente.disabled = false;
        botonSiguiente.style.backgroundColor = "green";
        botonSiguiente.style.cursor = "pointer";
    }

    function desactivarBoton() {
        botonSiguiente.disabled = true;
        botonSiguiente.style.backgroundColor = "gray";
        botonSiguiente.style.cursor = "not-allowed";
    }

    // Enviar datos seleccionados al hacer clic en "Siguiente"
    botonSiguiente.addEventListener("click", function () {
        if (fechaSeleccionada && horaSeleccionada) {
            const form = document.createElement("form");
            form.method = "POST";
            form.action = `index.php?mod=entradasEspectaculo&idEspectaculo=${encodeURIComponent(idEspectaculo)}`;
    
            const inputFecha = document.createElement("input");
            inputFecha.type = "hidden";
            inputFecha.name = "fecha";
            inputFecha.value = fechaSeleccionada;
    
            const inputHora = document.createElement("input");
            inputHora.type = "hidden";
            inputHora.name = "hora";
            inputHora.value = horaSeleccionada;
    
            form.appendChild(inputFecha);
            form.appendChild(inputHora);
    
            document.body.appendChild(form);
            form.submit();
        }
    });
    
});
