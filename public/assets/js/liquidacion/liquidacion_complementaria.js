document.querySelectorAll('[data-event-click="liquidacion_complementaria-1"]').forEach(function (elemento) {
    elemento.addEventListener('click', function (event) {
        const resultado = (function (event) {
seleccionarVisibles(true);
        }).call(this, event);
        if (resultado === false) event.preventDefault();
    });
});

document.querySelectorAll('[data-event-click="liquidacion_complementaria-2"]').forEach(function (elemento) {
    elemento.addEventListener('click', function (event) {
        const resultado = (function (event) {
seleccionarVisibles(false);
        }).call(this, event);
        if (resultado === false) event.preventDefault();
    });
});

document.querySelectorAll('[data-event-click="liquidacion_complementaria-3"]').forEach(function (elemento) {
    elemento.addEventListener('click', function (event) {
        const resultado = (function (event) {
usarDiasHabilitadosSeleccionados();
        }).call(this, event);
        if (resultado === false) event.preventDefault();
    });
});

document.querySelectorAll('[data-event-change="liquidacion_complementaria-4"]').forEach(function (elemento) {
    elemento.addEventListener('change', function (event) {
        const resultado = (function (event) {
actualizarFila(this);
        }).call(this, event);
        if (resultado === false) event.preventDefault();
    });
});

document.querySelectorAll('[data-event-change="liquidacion_complementaria-5"]').forEach(function (elemento) {
    elemento.addEventListener('change', function (event) {
        const resultado = (function (event) {
actualizarTextoPresentismo(this);
        }).call(this, event);
        if (resultado === false) event.preventDefault();
    });
});



const buscador =
    document.getElementById(
        "buscarEmpleado"
    );


if(buscador){

    buscador.addEventListener(
        "input",
        function(){

            const texto =
                this.value
                    .trim()
                    .toLowerCase();

            document
                .querySelectorAll(
                    "#cuerpoEmpleados tr"
                )
                .forEach(function(fila){

                    const contenido =
                        (
                            fila.dataset.busqueda
                            || ""
                        ).toLowerCase();

                    fila.style.display =
                        contenido.includes(texto)
                            ? ""
                            : "none";
                });
        }
    );
}


function actualizarFila(check){

    const fila =
        check.closest("tr");

    if(!fila){
        return;
    }

    const seleccionado =
        check.checked;

    fila.classList.toggle(
        "fila-seleccionada",
        seleccionado
    );

    fila
        .querySelectorAll(
            ".campo-novedad"
        )
        .forEach(function(campo){

            campo.disabled =
                !seleccionado;
        });

    actualizarContador();
}


function actualizarTextoPresentismo(input){

    const contenedor =
        input.closest(
            ".switch-wrap"
        );

    if(!contenedor){
        return;
    }

    const texto =
        contenedor.querySelector(
            ".estado-presentismo"
        );

    if(!texto){
        return;
    }

    if(input.checked){

        texto.textContent =
            "Sí";

        texto.classList.add(
            "estado-si"
        );

        texto.classList.remove(
            "estado-no"
        );

    }else{

        texto.textContent =
            "No";

        texto.classList.add(
            "estado-no"
        );

        texto.classList.remove(
            "estado-si"
        );
    }
}


function seleccionarVisibles(estado){

    document
        .querySelectorAll(
            "#cuerpoEmpleados tr"
        )
        .forEach(function(fila){

            if(fila.style.display === "none"){
                return;
            }

            const check =
                fila.querySelector(
                    ".check-incluir"
                );

            if(!check){
                return;
            }

            check.checked =
                estado;

            actualizarFila(
                check
            );
        });
}


function usarDiasHabilitadosSeleccionados(){

    document
        .querySelectorAll(
            "#cuerpoEmpleados tr"
        )
        .forEach(function(fila){

            const check =
                fila.querySelector(
                    ".check-incluir"
                );

            if(
                !check
                ||
                !check.checked
            ){
                return;
            }

            const input =
                fila.querySelector(
                    ".input-dias"
                );


            if(input){

                const maximo =
                    parseInt(
                        input.dataset.maxHabilitado
                        || fila.dataset.diasHabilitados
                        || input.max
                        || "30",
                        10
                    );


                input.value =
                    isNaN(maximo)
                        ? 1
                        : maximo;
            }
        });
}


function actualizarContador(){

    const contador =
        document.getElementById(
            "contadorSeleccionados"
        );

    if(!contador){
        return;
    }

    const total =
        document.querySelectorAll(
            ".check-incluir:checked"
        ).length;

    contador.textContent =
        total;
}


const formulario =
    document.getElementById(
        "formComplementaria"
    );


if(formulario){

    formulario.addEventListener(
        "submit",
        function(e){

            const seleccionados =
                document.querySelectorAll(
                    ".check-incluir:checked"
                );


            if(seleccionados.length === 0){

                e.preventDefault();

                alert(
                    "Debe seleccionar al menos un empleado para la liquidación complementaria."
                );

                return;
            }


            for(
                const check
                of
                seleccionados
            ){

                const fila =
                    check.closest("tr");

                const inputDias =
                    fila.querySelector(
                        ".input-dias"
                    );

                const dias =
                    parseInt(
                        inputDias.value,
                        10
                    );


                const maximo =
                    parseInt(
                        inputDias.dataset.maxHabilitado
                        || fila.dataset.diasHabilitados
                        || inputDias.max
                        || "30",
                        10
                    );


                if(
                    isNaN(dias)
                    ||
                    dias < 1
                    ||
                    isNaN(maximo)
                    ||
                    dias > maximo
                ){

                    e.preventDefault();

                    alert(
                        "Los días a liquidar deben estar entre 1 y "
                        + (
                            isNaN(maximo)
                                ? 30
                                : maximo
                        )
                        + " para el empleado seleccionado."
                    );

                    inputDias.focus();

                    return;
                }
            }
        }
    );
}


document.addEventListener(
    "DOMContentLoaded",
    function(){

        document
            .querySelectorAll(
                ".input-presentismo"
            )
            .forEach(
                actualizarTextoPresentismo
            );

        actualizarContador();
    }
);

