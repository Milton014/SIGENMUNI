document.querySelectorAll('[data-event-click="ayuda-1"]').forEach(function (elemento) {
    elemento.addEventListener('click', function (event) {
        const resultado = (function (event) {
window.print()
        }).call(this, event);
        if (resultado === false) event.preventDefault();
    });
});



document.addEventListener(
    "DOMContentLoaded",
    function()
    {
        const input =
            document.getElementById(
                "buscarAyuda"
            );

        const botonLimpiar =
            document.getElementById(
                "limpiarBusquedaAyuda"
            );

        const avisoSinResultados =
            document.getElementById(
                "sinResultadosAyuda"
            );

        const elementos =
            Array.from(
                document.querySelectorAll(
                    ".buscable-ayuda"
                )
            );

        const seccionesFiltrables =
            Array.from(
                document.querySelectorAll(
                    ".seccion-filtrable"
                )
            );


        function normalizarTexto(
            texto
        ) {
            return (texto ?? "")
                .toString()
                .normalize("NFD")
                .replace(
                    /[\u0300-\u036f]/g,
                    ""
                )
                .toLowerCase()
                .trim();
        }


        function filtrarAyuda()
        {
            if (!input) {
                return;
            }

            const termino =
                normalizarTexto(
                    input.value
                );

            let visibles =
                0;


            elementos.forEach(
                function(elemento)
                {
                    const contenido =
                        normalizarTexto(
                            (
                                elemento.dataset.busqueda
                                ??
                                ""
                            )
                            +
                            " "
                            +
                            elemento.textContent
                        );


                    const coincide =
                        termino === ""
                        ||
                        contenido.includes(
                            termino
                        );


                    elemento.classList.toggle(
                        "oculto-busqueda",
                        !coincide
                    );


                    if (coincide) {
                        visibles++;
                    }
                }
            );


            seccionesFiltrables.forEach(
                function(seccion)
                {
                    const hijos =
                        seccion.querySelectorAll(
                            ".buscable-ayuda"
                        );

                    let tieneVisible =
                        false;


                    hijos.forEach(
                        function(hijo)
                        {
                            if (
                                !hijo.classList.contains(
                                    "oculto-busqueda"
                                )
                            ) {
                                tieneVisible =
                                    true;
                            }
                        }
                    );


                    seccion.classList.toggle(
                        "oculto-busqueda",
                        termino !== ""
                        &&
                        !tieneVisible
                    );
                }
            );


            if (avisoSinResultados) {

                avisoSinResultados.style.display =
                    termino !== ""
                    &&
                    visibles === 0
                        ?
                        "block"
                        :
                        "none";
            }
        }


        if (input) {

            input.addEventListener(
                "input",
                filtrarAyuda
            );
        }


        if (botonLimpiar) {

            botonLimpiar.addEventListener(
                "click",
                function()
                {
                    if (!input) {
                        return;
                    }

                    input.value =
                        "";

                    filtrarAyuda();

                    input.focus();
                }
            );
        }
    }
);

