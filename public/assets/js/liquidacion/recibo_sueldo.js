document.querySelectorAll('[data-event-click="recibo_sueldo-1"]').forEach(function (elemento) {
    elemento.addEventListener('click', function (event) {
        const resultado = (function (event) {
window.print(); return false;
        }).call(this, event);
        if (resultado === false) event.preventDefault();
    });
});

