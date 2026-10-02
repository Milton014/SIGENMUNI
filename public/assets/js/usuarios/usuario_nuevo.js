document.querySelectorAll('[data-event-input="usuario_nuevo-1"]').forEach(function (elemento) {
    elemento.addEventListener('input', function (event) {
        const resultado = (function (event) {

                            this.value =
                            this.value.replace(
                                /[^0-9]/g,
                                ''
                            );
                        
        }).call(this, event);
        if (resultado === false) event.preventDefault();
    });
});

document.querySelectorAll('[data-event-click="usuario_nuevo-2"]').forEach(function (elemento) {
    elemento.addEventListener('click', function (event) {
        const resultado = (function (event) {
togglePassword('clave')
        }).call(this, event);
        if (resultado === false) event.preventDefault();
    });
});

document.querySelectorAll('[data-event-click="usuario_nuevo-3"]').forEach(function (elemento) {
    elemento.addEventListener('click', function (event) {
        const resultado = (function (event) {
togglePassword('confirmar_clave')
        }).call(this, event);
        if (resultado === false) event.preventDefault();
    });
});



/*
|--------------------------------------------------------------------------
| MOSTRAR / OCULTAR CONTRASEÑA
|--------------------------------------------------------------------------
*/

function togglePassword(id)
{
    const input =
        document.getElementById(id);

    input.type =
        input.type === "password"
            ? "text"
            : "password";
}


/*
|--------------------------------------------------------------------------
| INDICADORES DE LA POLÍTICA DE CONTRASEÑA
|--------------------------------------------------------------------------
*/

const campoClave =
    document.getElementById("clave");

const reqLongitud =
    document.getElementById("reqLongitud");

const reqMayuscula =
    document.getElementById("reqMayuscula");

const reqEspecial =
    document.getElementById("reqEspecial");


function actualizarRequisito(elemento, valido)
{
    const icono =
        elemento.querySelector(".icono");

    if (valido) {

        elemento.classList.add("valido");
        icono.textContent = "✓";

    } else {

        elemento.classList.remove("valido");
        icono.textContent = "•";
    }
}


function actualizarRequisitosClave()
{
    const clave =
        campoClave.value;

    actualizarRequisito(
        reqLongitud,
        [...clave].length >= 8
    );

    actualizarRequisito(
        reqMayuscula,
        /\p{Lu}/u.test(clave)
    );

    actualizarRequisito(
        reqEspecial,
        /[^\p{L}\p{N}\s]/u.test(clave)
    );
}


campoClave.addEventListener(
    "input",
    actualizarRequisitosClave
);


/*
|--------------------------------------------------------------------------
| VALIDACIÓN DEL FORMULARIO
|--------------------------------------------------------------------------
*/

document
    .getElementById("formUsuario")
    .addEventListener(
        "submit",
        function(e)
        {

            const alerta =
                document.getElementById(
                    "alertaUsuario"
                );


            const campos = [

                "nombre",
                "apellido",
                "dni",
                "nombre_usuario",
                "email",
                "clave",
                "confirmar_clave",
                "rol_id"

            ];


            campos.forEach(
                function(id)
                {
                    document
                        .getElementById(id)
                        .classList
                        .remove(
                            "input-error"
                        );
                }
            );


            alerta.style.display =
                "none";

            alerta.innerHTML =
                "";


            /*
            |--------------------------------------------------------------------------
            | MOSTRAR ERROR
            |--------------------------------------------------------------------------
            */

            function mostrarError(
                mensaje,
                id
            ) {

                e.preventDefault();


                const campo =
                    document.getElementById(
                        id
                    );


                alerta.textContent =
                    mensaje;


                alerta.style.display =
                    "block";


                campo
                    .classList
                    .add(
                        "input-error"
                    );


                campo.focus();


                window.scrollTo({

                    top:0,

                    behavior:"smooth"

                });
            }


            /*
            |--------------------------------------------------------------------------
            | VALORES
            |--------------------------------------------------------------------------
            */

            const nombre =
                document
                    .getElementById(
                        "nombre"
                    )
                    .value
                    .trim();


            const apellido =
                document
                    .getElementById(
                        "apellido"
                    )
                    .value
                    .trim();


            const dni =
                document
                    .getElementById(
                        "dni"
                    )
                    .value
                    .trim();


            const usuario =
                document
                    .getElementById(
                        "nombre_usuario"
                    )
                    .value
                    .trim();


            const email =
                document
                    .getElementById(
                        "email"
                    )
                    .value
                    .trim();


            const clave =
                document
                    .getElementById(
                        "clave"
                    )
                    .value;


            const confirmarClave =
                document
                    .getElementById(
                        "confirmar_clave"
                    )
                    .value;


            const rolId =
                document
                    .getElementById(
                        "rol_id"
                    )
                    .value;


            /*
            |--------------------------------------------------------------------------
            | NOMBRE
            |--------------------------------------------------------------------------
            */

            if (nombre === "") {

                mostrarError(
                    "Debe ingresar el nombre.",
                    "nombre"
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | APELLIDO
            |--------------------------------------------------------------------------
            */

            if (apellido === "") {

                mostrarError(
                    "Debe ingresar el apellido.",
                    "apellido"
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | DNI
            |--------------------------------------------------------------------------
            */

            if (dni === "") {

                mostrarError(
                    "Debe ingresar el DNI.",
                    "dni"
                );

                return;
            }


            if (
                !/^[0-9]{7,8}$/
                    .test(dni)
            ) {

                mostrarError(
                    "El DNI debe tener entre 7 y 8 números.",
                    "dni"
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | USUARIO
            |--------------------------------------------------------------------------
            */

            if (usuario === "") {

                mostrarError(
                    "Debe ingresar el nombre de usuario.",
                    "nombre_usuario"
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | EMAIL
            |--------------------------------------------------------------------------
            */

            if (email === "") {

                mostrarError(
                    "Debe ingresar el email.",
                    "email"
                );

                return;
            }


            const formatoEmail =
                /^[^\s@]+@[^\s@]+\.[^\s@]+$/;


            if (
                !formatoEmail.test(
                    email
                )
            ) {

                mostrarError(
                    "Debe ingresar un email válido.",
                    "email"
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | CONTRASEÑA
            |--------------------------------------------------------------------------
            */

            if (clave === "") {

                mostrarError(
                    "Debe ingresar una contraseña.",
                    "clave"
                );

                return;
            }


            if (
                [...clave].length
                <
                8
            ) {

                mostrarError(
                    "La contraseña debe tener al menos 8 caracteres.",
                    "clave"
                );

                return;
            }


            if (
                !/\p{Lu}/u.test(
                    clave
                )
            ) {

                mostrarError(
                    "La contraseña debe contener al menos una letra mayúscula.",
                    "clave"
                );

                return;
            }


            if (
                !/[^\p{L}\p{N}\s]/u.test(
                    clave
                )
            ) {

                mostrarError(
                    "La contraseña debe contener al menos un carácter especial.",
                    "clave"
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | CONFIRMAR CONTRASEÑA
            |--------------------------------------------------------------------------
            */

            if (
                confirmarClave
                ===
                ""
            ) {

                mostrarError(
                    "Debe confirmar la contraseña.",
                    "confirmar_clave"
                );

                return;
            }


            if (
                clave
                !==
                confirmarClave
            ) {

                mostrarError(
                    "Las contraseñas ingresadas no coinciden.",
                    "confirmar_clave"
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | ROL
            |--------------------------------------------------------------------------
            */

            if (rolId === "") {

                mostrarError(
                    "Debe seleccionar un rol.",
                    "rol_id"
                );

                return;
            }

        }
    );


/*
|--------------------------------------------------------------------------
| INICIALIZAR INDICADORES
|--------------------------------------------------------------------------
*/

actualizarRequisitosClave();

