// Las páginas de valores son independientes de las páginas del listado principal.
document.addEventListener('DOMContentLoaded', () => {
    const formulario = document.getElementById('formConcepto');
    const panel = document.querySelector('[data-valores-integrados]');
    const datos = () => formulario ? JSON.stringify(Array.from(new FormData(formulario).entries())) : '';
    const inicial = datos();
    const pendientes = formulario?.dataset.cambiosPendientes === '1';
    const confirmarSalida = () => (!pendientes && datos() === inicial) || confirm('Hay cambios del concepto sin guardar. ¿Desea continuar y descartarlos?');
    if (panel && formulario) {
        panel.querySelectorAll('.paginacion a').forEach(enlace => enlace.addEventListener('click', event => {
            if (!confirmarSalida()) event.preventDefault();
        }));
        panel.querySelector('[data-form-paginacion]').addEventListener('submit', event => {
            if (!confirmarSalida()) {
                event.preventDefault();
                const cantidad = event.target.querySelector('[data-paginacion-cantidad]');
                cantidad.value = cantidad.querySelector('option[selected]').value;
            }
        });
    }
    document.querySelectorAll('[data-paginacion-cantidad]').forEach(cantidad => {
        cantidad.addEventListener('change', () => cantidad.form.requestSubmit());
    });
});
