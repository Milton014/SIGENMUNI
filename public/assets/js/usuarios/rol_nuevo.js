

const formRol =
    document.getElementById(
        "formRol"
    );

const nombre =
    document.getElementById(
        "nombre"
    );

const alertaFormulario =
    document.getElementById(
        "alertaFormulario"
    );

const esAdmin =
    document.getElementById(
        "es_admin"
    );

const avisoAdmin =
    document.getElementById(
        "avisoAdmin"
    );


/*
|--------------------------------------------------------------------------
| MOSTRAR AVISO DE ADMINISTRADOR
|--------------------------------------------------------------------------
*/

esAdmin.addEventListener(
    "change",
    function()
    {

        avisoAdmin.style.display =
            this.checked
                ?
                "block"
                :
                "none";

    }
);


/*
|--------------------------------------------------------------------------
| VALIDACIÓN
|--------------------------------------------------------------------------
*/

formRol.addEventListener(
    "submit",
    function(e)
    {

        nombre
            .classList
            .remove(
                "campo-error"
            );


        alertaFormulario.style.display =
            "none";


        alertaFormulario.textContent =
            "";


        const nombreValor =
            nombre.value.trim();


        if (
            nombreValor
            ===
            ""
        ) {

            e.preventDefault();


            nombre
                .classList
                .add(
                    "campo-error"
                );


            nombre.focus();


            alertaFormulario.textContent =
                "Debe ingresar el nombre del rol.";


            alertaFormulario.style.display =
                "block";


            window.scrollTo({

                top:0,

                behavior:"smooth"

            });


            return false;
        }


        if (
            nombreValor.length
            >
            100
        ) {

            e.preventDefault();


            nombre
                .classList
                .add(
                    "campo-error"
                );


            nombre.focus();


            alertaFormulario.textContent =
                "El nombre del rol no puede superar los 100 caracteres.";


            alertaFormulario.style.display =
                "block";


            return false;
        }

    }
);

