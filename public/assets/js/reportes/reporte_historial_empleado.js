document.querySelectorAll('[data-event-input="reporte_historial_empleado-1"]').forEach(function (elemento) {
    elemento.addEventListener('input', function (event) {
        const resultado = (function (event) {
this.value=this.value.replace(/[^0-9]/g,'');
        }).call(this, event);
        if (resultado === false) event.preventDefault();
    });
});

