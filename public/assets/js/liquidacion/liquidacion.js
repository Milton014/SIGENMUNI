document.querySelectorAll('[data-event-submit="liquidacion-1"]').forEach(function (elemento) {
    elemento.addEventListener('submit', function (event) {
        const resultado = (function (event) {
return confirmarProcesamiento(this.dataset.eventValue);
        }).call(this, event);
        if (resultado === false) event.preventDefault();
    });
});

document.querySelectorAll('[data-event-submit="liquidacion-2"]').forEach(function (elemento) {
    elemento.addEventListener('submit', function (event) {
        const resultado = (function (event) {
return confirmarProcesamiento(this.dataset.eventValue);
        }).call(this, event);
        if (resultado === false) event.preventDefault();
    });
});

document.querySelectorAll('[data-event-submit="liquidacion-3"]').forEach(function (elemento) {
    elemento.addEventListener('submit', function (event) {
        const resultado = (function (event) {
return confirm(
                                                '¿Seguro que desea anular esta liquidación? La liquidación quedará registrada como ANULADA.'
                                            );
        }).call(this, event);
        if (resultado === false) event.preventDefault();
    });
});

document.querySelectorAll('[data-event-submit="liquidacion-4"]').forEach(function (elemento) {
    elemento.addEventListener('submit', function (event) {
        const resultado = (function (event) {
return confirm(
                                                    '¿Seguro que desea DESHACER el procesamiento de esta liquidación? Se eliminarán los detalles y resúmenes generados, y la liquidación volverá a estado BORRADOR. Esta acción no elimina la cabecera de la liquidación.'
                                                );
        }).call(this, event);
        if (resultado === false) event.preventDefault();
    });
});

document.querySelectorAll('[data-event-submit="liquidacion-5"]').forEach(function (elemento) {
    elemento.addEventListener('submit', function (event) {
        const resultado = (function (event) {
return confirm(
                                                '¿Seguro que desea anular esta liquidación cerrada? Los datos quedarán conservados como historial.'
                                            );
        }).call(this, event);
        if (resultado === false) event.preventDefault();
    });
});



function confirmarProcesamiento(tipoLiquidacion){

    tipoLiquidacion =
        String(
            tipoLiquidacion || ''
        ).toUpperCase();


    if(tipoLiquidacion === 'AGUINALDO'){

        return confirm(
            '¿Seguro que desea procesar esta liquidación de AGUINALDO? '
            + 'Verifique antes las Novedades SAC, los días devengados, ya pagados y pendientes de cada empleado. '
            + 'Al finalizar quedará CERRADA. Solo un administrador podrá deshacer el procesamiento si fuera necesario.'
        );
    }


    if(tipoLiquidacion === 'COMPLEMENTARIA_SAC'){

        return confirm(
            '¿Seguro que desea procesar esta COMPLEMENTARIA DE SAC? '
            + 'Verifique antes el personal seleccionado y los días SAC pendientes que se liquidarán ahora. '
            + 'Solo se procesarán los empleados seleccionados. '
            + 'Al finalizar quedará CERRADA. Solo un administrador podrá deshacer el procesamiento si fuera necesario.'
        );
    }


    if(tipoLiquidacion === 'COMPLEMENTARIA'){

        return confirm(
            '¿Seguro que desea procesar esta liquidación COMPLEMENTARIA? '
            + 'Verifique antes el personal seleccionado, los días liquidados y el presentismo. '
            + 'Solo se procesarán los empleados seleccionados. '
            + 'Al finalizar quedará CERRADA. Solo un administrador podrá deshacer el procesamiento si fuera necesario.'
        );
    }


    if(tipoLiquidacion === 'MENSUAL'){

        return confirm(
            '¿Seguro que desea procesar esta liquidación? '
            + 'Verifique antes las novedades de días liquidados y presentismo. '
            + 'Al finalizar quedará CERRADA. Solo un administrador podrá deshacer el procesamiento si fuera necesario.'
        );
    }


    return confirm(
        '¿Seguro que desea procesar esta liquidación? '
        + 'Al finalizar quedará CERRADA. Solo un administrador podrá deshacer el procesamiento si fuera necesario.'
    );
}


// Cambiar cantidad vuelve a la primera página conservando los filtros del formulario.
document.querySelectorAll('[data-paginacion-cantidad]').forEach(campo => {
    campo.addEventListener('change', () => campo.form.requestSubmit());
});
