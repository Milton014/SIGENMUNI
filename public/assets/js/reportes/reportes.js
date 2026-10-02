

/*
|--------------------------------------------------------------------------
| BUSCADOR DE REPORTES
|--------------------------------------------------------------------------
*/

const buscador =
    document.getElementById(
        "buscadorModulos"
    );


const tarjetas =
    document.querySelectorAll(
        ".card-modulo"
    );


const sinResultados =
    document.getElementById(
        "sinResultados"
    );


if (
    buscador
    &&
    sinResultados
) {

buscador.addEventListener(
    "input",
    function()
    {

        const texto =
            this.value
                .toLowerCase()
                .trim();


        let visibles =
            0;


        tarjetas.forEach(
            function(tarjeta)
            {

                const nombre =
                    tarjeta
                        .getAttribute(
                            "data-nombre"
                        )
                        .toLowerCase();


                if (
                    nombre.includes(
                        texto
                    )
                ) {

                    tarjeta
                        .classList
                        .remove(
                            "oculto"
                        );


                    visibles++;

                } else {

                    tarjeta
                        .classList
                        .add(
                            "oculto"
                        );
                }

            }
        );


        sinResultados.style.display =
            visibles === 0
                ?
                "block"
                :
                "none";

    }
);

}

