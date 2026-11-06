document.querySelectorAll('[data-event-submit="liquidacion_ver-1"]').forEach(function (elemento) {
    elemento.addEventListener('submit', function (event) {
        const resultado = (function (event) {
return confirm(
                        '¿Seguro que desea procesar esta liquidación? Una vez cerrada no podrá modificarse.'
                    );
        }).call(this, event);
        if (resultado === false) event.preventDefault();
    });
});



            document.addEventListener(
                "DOMContentLoaded",
                function()
                {

                    const detalle =
                        document.getElementById(
                            "detalleEmpleado"
                        );


                    if(detalle){

                        detalle.scrollIntoView({
                            behavior:"smooth",
                            block:"start"
                        });
                    }
                }
            );

            

// Cambiar cantidad vuelve a la primera página conservando los filtros del formulario.
document.querySelectorAll('[data-paginacion-cantidad]').forEach(campo => {
    campo.addEventListener('change', () => campo.form.requestSubmit());
});
