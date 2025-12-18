document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("vistaPreviaModal");
    const modalImagen = document.getElementById("vistaPreviaImagen");
    const closeButtons = modal.querySelectorAll("[data-bs-dismiss='modal']");

    // Configuración de la previsualización en el modal
    document.querySelectorAll(".preview-btn").forEach(function (boton) {
        boton.addEventListener("click", function () {
            const rutaImagen = this.getAttribute("data-img");
            modalImagen.src = rutaImagen;
        });
    });

    // Eliminar posibles conflictos con aria-hidden
    modal.addEventListener("shown.bs.modal", function () {
        modal.setAttribute("aria-hidden", "false");
        modal.removeAttribute("inert"); // El modal está activo
    });

    modal.addEventListener("hidden.bs.modal", function () {
        modal.setAttribute("aria-hidden", "true");
        modal.setAttribute("inert", ""); // El modal está inactivo
    });

    // Asegurar que los botones de cierre funcionen correctamente
    closeButtons.forEach(button => {
        button.addEventListener("click", function () {
            modal.classList.remove("show"); // Elimina la clase "show"
            modal.style.display = "none";  // Oculta el modal
            document.body.classList.remove("modal-open"); // Restaura el estado del body
            const backdrop = document.querySelector(".modal-backdrop");
            if (backdrop) backdrop.remove(); // Elimina el fondo del modal
        });
    });
});
