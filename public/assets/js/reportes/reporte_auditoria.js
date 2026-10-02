document.querySelectorAll('[data-event-click="reporte_auditoria-1"]').forEach(function (elemento) {
    elemento.addEventListener('click', function (event) {
        const resultado = (function (event) {
window.print();
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
                        "detalleAuditoria"
                    );

                if (detalle) {

                    detalle.scrollIntoView({
                        behavior:"smooth",
                        block:"start"
                    });
                }
            }
        );

        