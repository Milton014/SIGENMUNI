// Filtra únicamente las filas del resumen; conserva totales, enlaces y detalle.
(() => {
    const tabla = document.getElementById('resumenEmpleados');
    if (!tabla) return;
    const campo = document.getElementById('buscarEmpleado');
    const normalizar = texto => texto.normalize('NFD').replace(/[\u0300-\u036f]/g, '')
        .toLocaleLowerCase('es').replace(/[,\s]+/g, ' ').trim();
    // Comparar como texto evita perder precisión y admite legajos con ceros iniciales.
    const normalizarLegajo = texto => texto.trim().replace(/^0+(?=\d)/, '');
    const filas = Array.from(tabla.tBodies[0].rows, fila => ({
        fila,
        legajo: normalizarLegajo(fila.cells[0].textContent),
        texto: normalizar(fila.cells[0].textContent + ' ' + fila.cells[1].textContent)
    }));
    const filtrar = () => {
        const consulta = campo.value.trim();
        const soloNumeros = /^\d+$/.test(consulta);
        const palabras = normalizar(consulta).split(' ').filter(Boolean);
        let visibles = 0;
        filas.forEach(({fila, texto, legajo}) => {
            const coincide = soloNumeros
                ? legajo === normalizarLegajo(consulta)
                : palabras.every(palabra => texto.includes(palabra));
            fila.hidden = !coincide;
            if (coincide) visibles++;
        });
        document.getElementById('resultadoBusquedaEmpleados').textContent =
            `Mostrando ${visibles} de ${filas.length} empleados`;
        document.getElementById('sinCoincidenciasEmpleados').hidden = visibles !== 0;
    };
    campo.addEventListener('input', filtrar);
    document.getElementById('limpiarBusquedaEmpleado').addEventListener('click', () => {
        campo.value = '';
        filtrar();
        campo.focus();
    });
    document.getElementById('buscadorEmpleados').hidden = false;
    filtrar();
})();

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

            
