document.querySelectorAll('[data-event-click="empleados-1"]').forEach(function (elemento) {
    elemento.addEventListener('click', function (event) {
        const resultado = (function (event) {
return confirm('¿Desea inactivar este empleado?');
        }).call(this, event);
        if (resultado === false) event.preventDefault();
    });
});

document.querySelectorAll('[data-event-click="empleados-2"]').forEach(function (elemento) {
    elemento.addEventListener('click', function (event) {
        const resultado = (function (event) {
return confirm('¿Desea activar este empleado?');
        }).call(this, event);
        if (resultado === false) event.preventDefault();
    });
});

