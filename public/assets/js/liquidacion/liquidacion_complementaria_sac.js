document.querySelectorAll('[data-event-click="liquidacion_complementaria_sac-1"]').forEach(function (elemento) {
    elemento.addEventListener('click', function (event) {
        const resultado = (function (event) {
seleccionarConSaldoVisibles();
        }).call(this, event);
        if (resultado === false) event.preventDefault();
    });
});

document.querySelectorAll('[data-event-click="liquidacion_complementaria_sac-2"]').forEach(function (elemento) {
    elemento.addEventListener('click', function (event) {
        const resultado = (function (event) {
quitarSeleccionVisible();
        }).call(this, event);
        if (resultado === false) event.preventDefault();
    });
});

document.querySelectorAll('[data-event-click="liquidacion_complementaria_sac-3"]').forEach(function (elemento) {
    elemento.addEventListener('click', function (event) {
        const resultado = (function (event) {
usarSaldoPendiente();
        }).call(this, event);
        if (resultado === false) event.preventDefault();
    });
});

document.querySelectorAll('[data-event-change="liquidacion_complementaria_sac-4"]').forEach(function (elemento) {
    elemento.addEventListener('change', function (event) {
        const resultado = (function (event) {
actualizarFila(this);
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

    const pendientes =
        parseInt(
            fila.dataset.pendientes || "0",
            10
        );

    const input =
        fila.querySelector(
            ".input-dias-sac"
        );


    if(check.checked && pendientes > 0){

        fila.classList.add(
            "fila-seleccionada"
        );

        if(input){

            input.disabled =
                false;

            if(
                parseInt(input.value,10) <= 0
                ||
                parseInt(input.value,10) > pendientes
            ){
                input.value =
                    pendientes;
            }
        }

    }else{

        fila.classList.remove(
            "fila-seleccionada"
        );

        if(input){

            input.disabled =
                true;
        }
    }


    actualizarContador();
}


function seleccionarConSaldoVisibles(){

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

            if(
                !check
                ||
                check.disabled
            ){
                return;
            }

            check.checked =
                true;

            actualizarFila(
                check
            );
        });
}


function quitarSeleccionVisible(){

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

            if(
                !check
                ||
                check.disabled
            ){
                return;
            }

            check.checked =
                false;

            actualizarFila(
                check
            );
        });
}


function usarSaldoPendiente(){

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

            const pendientes =
                parseInt(
                    fila.dataset.pendientes || "0",
                    10
                );

            const input =
                fila.querySelector(
                    ".input-dias-sac"
                );

            if(
                input
                &&
                pendientes > 0
            ){
                input.value =
                    pendientes;
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
        "formComplementariaSac"
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
                    "Debe seleccionar al menos un empleado con saldo SAC pendiente."
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

                const pendientes =
                    parseInt(
                        fila.dataset.pendientes || "0",
                        10
                    );

                const input =
                    fila.querySelector(
                        ".input-dias-sac"
                    );

                const dias =
                    parseInt(
                        input.value,
                        10
                    );


                if(
                    isNaN(dias)
                    ||
                    dias < 1
                    ||
                    dias > pendientes
                ){

                    e.preventDefault();

                    alert(
                        "Los días SAC a liquidar deben estar entre 1 y el saldo pendiente del empleado."
                    );

                    input.focus();

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
                ".check-incluir"
            )
            .forEach(function(check){

                if(!check.disabled){
                    actualizarFila(check);
                }
            });

        actualizarContador();
    }
);

