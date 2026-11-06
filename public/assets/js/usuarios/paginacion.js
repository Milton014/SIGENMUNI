document.querySelectorAll('[data-cantidad-listado]').forEach(function (selector) {
    selector.addEventListener('change', function () {
        if (this.form) this.form.requestSubmit();
    });
});
