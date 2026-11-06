document.querySelectorAll('[data-event-submit="categorias-1"]').forEach(function (elemento) {
    elemento.addEventListener('submit', function (event) {
        const resultado = (function (event) {
return confirm('¿Seguro que desea inactivar esta categoría?');
        }).call(this, event);
        if (resultado === false) event.preventDefault();
    });
});

document.querySelectorAll('[data-event-submit="categorias-2"]').forEach(function (elemento) {
    elemento.addEventListener('submit', function (event) {
        const resultado = (function (event) {
return confirm('¿Seguro que desea activar esta categoría?');
        }).call(this, event);
        if (resultado === false) event.preventDefault();
    });
});


const cantidadCategorias = document.getElementById('por_pagina');
if (cantidadCategorias) {
    cantidadCategorias.addEventListener('change', function () {
        const formulario = document.getElementById('filtrosCategorias');
        if (formulario) formulario.requestSubmit();
    });
}
