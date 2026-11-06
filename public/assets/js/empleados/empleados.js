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


// El formulario no envía una página, por lo que cambiar la cantidad vuelve al inicio.
document.getElementById('por_pagina')?.addEventListener('change', function () {
    this.form.requestSubmit();
});
