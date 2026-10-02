

/*
|--------------------------------------------------------------------------
| VALIDACIÓN DEL FORMULARIO
|--------------------------------------------------------------------------
*/

document
    .getElementById(
        'formCategoria'
    )
    .addEventListener(
        'submit',
        function(e)
        {
            const alerta =
                document
                    .getElementById(
                        'alertaCategoria'
                    );


            const codigo =
                document
                    .getElementById(
                        'codigo'
                    );


            const nombre =
                document
                    .getElementById(
                        'nombre'
                    );


            /*
            |--------------------------------------------------------------------------
            | LIMPIAR ESTADO
            |--------------------------------------------------------------------------
            */

            alerta.style.display =
                'none';


            alerta.innerHTML =
                '';


            codigo.classList.remove(
                'input-error'
            );


            nombre.classList.remove(
                'input-error'
            );


            /*
            |--------------------------------------------------------------------------
            | FUNCIÓN DE ERROR
            |--------------------------------------------------------------------------
            */

            function mostrarError(
                mensaje,
                campo
            ) {
                e.preventDefault();


                alerta.innerHTML =
                    mensaje;


                alerta.style.display =
                    'block';


                campo.classList.add(
                    'input-error'
                );


                campo.focus();


                window.scrollTo({
                    top:0,
                    behavior:'smooth'
                });
            }


            /*
            |--------------------------------------------------------------------------
            | CÓDIGO
            |--------------------------------------------------------------------------
            */

            const codigoValor =
                codigo.value.trim();


            if (
                codigoValor
                ===
                ''
            ) {

                mostrarError(
                    'Debe ingresar el código de la categoría.',
                    codigo
                );

                return;
            }


            if (
                !/^[0-9]+$/.test(
                    codigoValor
                )
            ) {

                mostrarError(
                    'El código debe contener solamente números.',
                    codigo
                );

                return;
            }


            if (
                parseInt(
                    codigoValor,
                    10
                )
                <=
                0
            ) {

                mostrarError(
                    'El código debe ser mayor a cero.',
                    codigo
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | NOMBRE
            |--------------------------------------------------------------------------
            */

            const nombreValor =
                nombre.value.trim();


            if (
                nombreValor
                ===
                ''
            ) {

                mostrarError(
                    'Debe ingresar el nombre de la categoría.',
                    nombre
                );

                return;
            }


            if (
                nombreValor.length
                >
                150
            ) {

                mostrarError(
                    'El nombre no puede superar los 150 caracteres.',
                    nombre
                );

                return;
            }
        }
    );

