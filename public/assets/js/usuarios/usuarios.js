document.querySelectorAll('[data-event-submit="usuarios-1"]').forEach(function (elemento) {
    elemento.addEventListener('submit', function (event) {
        const resultado = (function (event) {
return confirm(this.dataset.eventValue);
        }).call(this, event);
        if (resultado === false) event.preventDefault();
    });
});

