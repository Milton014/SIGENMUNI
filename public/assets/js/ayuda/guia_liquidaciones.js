document.querySelectorAll('[data-event-click="guia_liquidaciones-1"]').forEach(function (elemento) {
    elemento.addEventListener('click', function (event) {
        const resultado = (function (event) {
window.print()
        }).call(this, event);
        if (resultado === false) event.preventDefault();
    });
});

