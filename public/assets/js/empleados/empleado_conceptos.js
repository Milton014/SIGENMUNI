document.querySelectorAll('[data-event-submit="empleado_conceptos-1"]').forEach(function (elemento) {
    elemento.addEventListener('submit', function (event) {
        const resultado = (function (event) {
return confirm('¿Seguro que desea desactivar este registro de asignación?');
        }).call(this, event);
        if (resultado === false) event.preventDefault();
    });
});

document.querySelectorAll('[data-event-submit="empleado_conceptos-2"]').forEach(function (elemento) {
    elemento.addEventListener('submit', function (event) {
        const resultado = (function (event) {
return confirm('¿Seguro que desea activar este registro de asignación?');
        }).call(this, event);
        if (resultado === false) event.preventDefault();
    });
});

