document.querySelectorAll('[data-cantidad-reporte]').forEach(function (selector) {
    selector.addEventListener('change', function () { if (this.form) this.form.requestSubmit(); });
});
