document.querySelectorAll('[data-event-click="login-1"]').forEach(function (elemento) {
    elemento.addEventListener('click', function (event) {
        const resultado = (function (event) {
togglePassword()
        }).call(this, event);
        if (resultado === false) event.preventDefault();
    });
});



/*
|--------------------------------------------------------------------------
| LIMPIAR CACHE LOCAL DE UNA SESIÓN ANTERIOR
|--------------------------------------------------------------------------
|
| Si llegamos a login.php sin una sesión PHP válida, cualquier dato visual
| guardado en localStorage debe considerarse obsoleto.
|
| Nunca se guardan contraseñas, hashes ni permisos sensibles en localStorage.
|
|--------------------------------------------------------------------------
*/

try {
    localStorage.removeItem("sigenmuni_sesion");
} catch (error) {
    console.warn("No se pudo limpiar el cache local de SIGENMUNI.", error);
}


/*
|--------------------------------------------------------------------------
| MOSTRAR / OCULTAR CONTRASEÑA
|--------------------------------------------------------------------------
*/

function togglePassword() {

    const input =
        document.getElementById(
            "contrasena"
        );


    if (
        input.type
        ===
        "password"
    ) {

        input.type =
            "text";

    } else {

        input.type =
            "password";
    }
}


/*
|--------------------------------------------------------------------------
| VALIDACIÓN BÁSICA DEL FORMULARIO
|--------------------------------------------------------------------------
|
| No hacemos la validación de complejidad aquí.
|
| Esa validación se realiza del lado del servidor DESPUÉS de comprobar
| mediante password_verify() que la contraseña ingresada realmente
| pertenece al usuario.
|
|--------------------------------------------------------------------------
*/

document
    .getElementById(
        "formLogin"
    )
    .addEventListener(
        "submit",
        function(e) {

            const usuario =
                document.getElementById(
                    "usuario"
                );


            const contrasena =
                document.getElementById(
                    "contrasena"
                );


            const alerta =
                document.getElementById(
                    "alertaLogin"
                );


            /*
            |--------------------------------------------------------------------------
            | LIMPIAR ERRORES
            |--------------------------------------------------------------------------
            */

            usuario.classList.remove(
                "input-error"
            );


            contrasena.classList.remove(
                "input-error"
            );


            alerta.style.display =
                "none";


            alerta.className =
                "";


            alerta.textContent =
                "";


            /*
            |--------------------------------------------------------------------------
            | VALIDAR USUARIO
            |--------------------------------------------------------------------------
            */

            if (
                usuario.value.trim()
                ===
                ""
            ) {

                e.preventDefault();


                alerta.className =
                    "mensaje-error";


                alerta.textContent =
                    "Debe ingresar el usuario.";


                alerta.style.display =
                    "block";


                usuario.classList.add(
                    "input-error"
                );


                usuario.focus();


                return;
            }


            /*
            |--------------------------------------------------------------------------
            | VALIDAR CONTRASEÑA VACÍA
            |--------------------------------------------------------------------------
            */

            if (
                contrasena.value
                ===
                ""
            ) {

                e.preventDefault();


                alerta.className =
                    "mensaje-error";


                alerta.textContent =
                    "Debe ingresar la contraseña.";


                alerta.style.display =
                    "block";


                contrasena.classList.add(
                    "input-error"
                );


                contrasena.focus();


                return;
            }

        }
    );

