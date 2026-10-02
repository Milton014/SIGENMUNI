document.querySelectorAll('[data-event-input="recuperar-1"]').forEach(function (elemento) {
    elemento.addEventListener('input', function (event) {
        const resultado = (function (event) {
this.value = this.value.replace(/[^0-9]/g, '')
        }).call(this, event);
        if (resultado === false) event.preventDefault();
    });
});


document.getElementById("formRecuperar").addEventListener("submit", function(e) {
    const correo = document.getElementById("correo");
    const dni = document.getElementById("dni");
    const alerta = document.getElementById("alertaRecuperar");

    correo.classList.remove("input-error");
    dni.classList.remove("input-error");

    alerta.style.display = "none";
    alerta.innerHTML = "";

    if (correo.value.trim() === "") {
        e.preventDefault();
        alerta.innerHTML = "Debe ingresar el correo registrado.";
        alerta.style.display = "block";
        correo.classList.add("input-error");
        correo.focus();
        return;
    }

    const formatoCorreo = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!formatoCorreo.test(correo.value.trim())) {
        e.preventDefault();
        alerta.innerHTML = "Debe ingresar un correo válido.";
        alerta.style.display = "block";
        correo.classList.add("input-error");
        correo.focus();
        return;
    }

    if (dni.value.trim() === "") {
        e.preventDefault();
        alerta.innerHTML = "Debe ingresar el DNI del administrador.";
        alerta.style.display = "block";
        dni.classList.add("input-error");
        dni.focus();
        return;
    }

    if (dni.value.trim().length < 7 || dni.value.trim().length > 8) {
        e.preventDefault();
        alerta.innerHTML = "El DNI debe tener entre 7 y 8 números.";
        alerta.style.display = "block";
        dni.classList.add("input-error");
        dni.focus();
        return;
    }
});
