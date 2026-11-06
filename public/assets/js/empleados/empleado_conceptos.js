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


// El formulario conserva filtros y omite pagina para volver al inicio del resultado.
document.getElementById('por_pagina')?.addEventListener('change', function () {
    this.form.requestSubmit();
});
