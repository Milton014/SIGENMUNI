document.querySelectorAll('[data-event-input="verificar_codigo-1"]').forEach(function (elemento) {
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

document.querySelectorAll('[data-event-click="verificar_codigo-2"]').forEach(function (elemento) {
    elemento.addEventListener('click', function (event) {
        const resultado = (function (event) {
togglePassword('nueva_contra')
        }).call(this, event);
        if (resultado === false) event.preventDefault();
    });
});

document.querySelectorAll('[data-event-click="verificar_codigo-3"]').forEach(function (elemento) {
    elemento.addEventListener('click', function (event) {
        const resultado = (function (event) {
togglePassword('confirmar_contra')
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
| ELEMENTOS
|--------------------------------------------------------------------------
*/

const nuevaContra =
    document.getElementById(
        "nueva_contra"
    );


const confirmarContra =
    document.getElementById(
        "confirmar_contra"
    );


const reqLongitud =
    document.getElementById(
        "reqLongitud"
    );


const reqMayuscula =
    document.getElementById(
        "reqMayuscula"
    );


const reqEspecial =
    document.getElementById(
        "reqEspecial"
    );


/*
|--------------------------------------------------------------------------
| ACTUALIZAR INDICADORES
|--------------------------------------------------------------------------
*/

function actualizarRequisito(
    elemento,
    valido
) {

    const icono =
        elemento.querySelector(
            ".icono"
        );


    if (valido) {

        elemento.classList.add(
            "valido"
        );

        icono.textContent =
            "✓";

    } else {

        elemento.classList.remove(
            "valido"
        );

        icono.textContent =
            "•";
    }
}


function actualizarRequisitos()
{
    const clave =
        nuevaContra.value;


    /*
    |--------------------------------------------------------------------------
    | 8 CARACTERES
    |--------------------------------------------------------------------------
    */

    actualizarRequisito(
        reqLongitud,
        [...clave].length >= 8
    );


    /*
    |--------------------------------------------------------------------------
    | MAYÚSCULA
    |--------------------------------------------------------------------------
    */

    actualizarRequisito(
        reqMayuscula,
        /\p{Lu}/u.test(
            clave
        )
    );


    /*
    |--------------------------------------------------------------------------
    | CARÁCTER ESPECIAL
    |--------------------------------------------------------------------------
    */

    actualizarRequisito(
        reqEspecial,
        /[^\p{L}\p{N}\s]/u.test(
            clave
        )
    );
}


nuevaContra.addEventListener(
    "input",
    actualizarRequisitos
);


/*
|--------------------------------------------------------------------------
| VALIDAR FORMULARIO
|--------------------------------------------------------------------------
*/

document
    .getElementById(
        "formRecuperacion"
    )
    .addEventListener(
        "submit",
        function(e)
        {

            const codigo =
                document.getElementById(
                    "codigo"
                );


            const nuevoUsuario =
                document.getElementById(
                    "nuevo_usuario"
                );


            const alerta =
                document.getElementById(
                    "alertaFormulario"
                );


            /*
            |--------------------------------------------------------------------------
            | LIMPIAR ERRORES
            |--------------------------------------------------------------------------
            */

            const campos = [

                codigo,

                nuevoUsuario,

                nuevaContra,

                confirmarContra

            ];


            campos.forEach(
                function(campo)
                {
                    campo
                        .classList
                        .remove(
                            "input-error"
                        );
                }
            );


            alerta.style.display =
                "none";


            alerta.textContent =
                "";


            /*
            |--------------------------------------------------------------------------
            | FUNCIÓN ERROR
            |--------------------------------------------------------------------------
            */

            function mostrarError(
                mensaje,
                campo
            ) {

                e.preventDefault();


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
            | CÓDIGO
            |--------------------------------------------------------------------------
            */

            const valorCodigo =
                codigo.value.trim();


            if (
                valorCodigo === ""
            ) {

                mostrarError(
                    "Debe ingresar el código de verificación.",
                    codigo
                );

                return;
            }


            if (
                !/^[0-9]{6}$/.test(
                    valorCodigo
                )
            ) {

                mostrarError(
                    "El código debe contener exactamente 6 números.",
                    codigo
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | USUARIO
            |--------------------------------------------------------------------------
            */

            if (
                nuevoUsuario
                    .value
                    .trim()
                ===
                ""
            ) {

                mostrarError(
                    "Debe ingresar el nuevo nombre de usuario.",
                    nuevoUsuario
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | NUEVA CONTRASEÑA
            |--------------------------------------------------------------------------
            */

            const clave =
                nuevaContra.value;


            if (
                clave === ""
            ) {

                mostrarError(
                    "Debe ingresar la nueva contraseña.",
                    nuevaContra
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
                    nuevaContra
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
                    nuevaContra
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
                    nuevaContra
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | CONFIRMAR
            |--------------------------------------------------------------------------
            */

            if (
                confirmarContra.value
                ===
                ""
            ) {

                mostrarError(
                    "Debe confirmar la nueva contraseña.",
                    confirmarContra
                );

                return;
            }


            if (
                clave
                !==
                confirmarContra.value
            ) {

                mostrarError(
                    "Las contraseñas ingresadas no coinciden.",
                    confirmarContra
                );

                return;
            }

        }
    );


/*
|--------------------------------------------------------------------------
| INICIALIZAR
|--------------------------------------------------------------------------
*/

actualizarRequisitos();

