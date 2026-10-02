document.querySelectorAll('[data-event-click="liquidacion_novedades_sac-1"]').forEach(function (elemento) {
    elemento.addEventListener('click', function (event) {
        const resultado = (function (event) {
establecerTodosDias(180);
        }).call(this, event);
        if (resultado === false) event.preventDefault();
    });
});

document.querySelectorAll('[data-event-click="liquidacion_novedades_sac-2"]').forEach(function (elemento) {
    elemento.addEventListener('click', function (event) {
        const resultado = (function (event) {
establecerTodosDias(0);
        }).call(this, event);
        if (resultado === false) event.preventDefault();
    });
});

document.querySelectorAll('[data-event-input="liquidacion_novedades_sac-3"]').forEach(function (elemento) {
    elemento.addEventListener('input', function (event) {
        const resultado = (function (event) {
actualizarFilaSac(this);
        }).call(this, event);
        if (resultado === false) event.preventDefault();
    });
});

document.querySelectorAll('[data-event-change="liquidacion_novedades_sac-4"]').forEach(function (elemento) {
    elemento.addEventListener('change', function (event) {
        const resultado = (function (event) {
actualizarFilaSac(this);
        }).call(this, event);
        if (resultado === false) event.preventDefault();
    });
});



/*
|--------------------------------------------------------------------------
| BUSCADOR
|--------------------------------------------------------------------------
*/

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


/*
|--------------------------------------------------------------------------
| ESTABLECER DÍAS EN TODOS
|--------------------------------------------------------------------------
*/

function establecerTodosDias(dias){

    document
        .querySelectorAll(
            ".input-dias-sac"
        )
        .forEach(function(input){

            if(input.disabled){
                return;
            }

            const fila =
                input.closest("tr");

            const pendientes =
                fila
                    ? parseInt(
                        fila.dataset.pendientes || "0",
                        10
                    )
                    : 0;

            let valor =
                dias;

            if(valor > pendientes){
                valor = pendientes;
            }

            if(valor < 0){
                valor = 0;
            }

            input.value =
                valor;

            actualizarFilaSac(
                input
            );
        });


    actualizarContador();
}


/*
|--------------------------------------------------------------------------
| ACTUALIZAR ESTADO VISUAL
|--------------------------------------------------------------------------
*/

function actualizarFilaSac(input){

    const fila =
        input.closest(
            "tr"
        );

    if(!fila){
        return;
    }


    let dias =
        parseInt(
            input.value,
            10
        );


    if(isNaN(dias)){
        dias = 0;
    }


    if(dias < 0){
        dias = 0;
    }

    if(dias > 180){
        dias = 180;
    }


    const badge =
        fila.querySelector(
            ".badge-sac"
        );


    fila.classList.remove(
        "fila-proporcional",
        "fila-no-liquida"
    );


    if(badge){

        badge.classList.remove(
            "badge-completo",
            "badge-proporcional",
            "badge-no-liquida",
            "badge-pendiente",
            "badge-parcial",
            "badge-liquidado"
        );
    }


    const pendientes =
        parseInt(
            fila.dataset.pendientes || "0",
            10
        );


    if(pendientes <= 0){

        fila.classList.add(
            "fila-no-liquida"
        );

        if(badge){

            badge.textContent =
                "Ya liquidado";

            badge.classList.add(
                "badge-liquidado"
            );
        }

    }else if(dias === 180){

        if(badge){

            badge.textContent =
                "SAC completo";

            badge.classList.add(
                "badge-completo"
            );
        }

    }else if(dias === 0){

        fila.classList.add(
            "fila-no-liquida"
        );

        if(badge){

            badge.textContent =
                "No liquida ahora";

            badge.classList.add(
                "badge-no-liquida"
            );
        }

    }else if(dias < pendientes){

        fila.classList.add(
            "fila-proporcional"
        );

        if(badge){

            badge.textContent =
                "Parcial";

            badge.classList.add(
                "badge-parcial"
            );
        }

    }else{

        fila.classList.add(
            "fila-proporcional"
        );

        if(badge){

            badge.textContent =
                "Pendiente";

            badge.classList.add(
                "badge-pendiente"
            );
        }
    }


    actualizarContador();
}


/*
|--------------------------------------------------------------------------
| CONTADOR
|--------------------------------------------------------------------------
*/

function actualizarContador(){

    const contador =
        document.getElementById(
            "contadorProporcionales"
        );

    if(!contador){
        return;
    }


    let total =
        0;


    document
        .querySelectorAll(
            ".input-dias-sac"
        )
        .forEach(function(input){

            if(input.disabled){
                return;
            }

            const dias =
                parseInt(
                    input.value,
                    10
                );

            const fila =
                input.closest("tr");

            const pendientes =
                fila
                    ? parseInt(
                        fila.dataset.pendientes || "0",
                        10
                    )
                    : 0;


            if(
                !isNaN(dias)
                &&
                (
                    dias === 0
                    ||
                    dias < pendientes
                    ||
                    pendientes < 180
                )
            ){

                total++;
            }
        });


    contador.textContent =
        total;
}


/*
|--------------------------------------------------------------------------
| VALIDACIÓN
|--------------------------------------------------------------------------
*/

const formulario =
    document.getElementById(
        "formSac"
    );

if(formulario){

    formulario.addEventListener(
        "submit",
        function(e){

            const inputs =
                document.querySelectorAll(
                    ".input-dias-sac"
                );


            for(
                const input
                of
                inputs
            ){

                if(input.disabled){
                    continue;
                }

                const texto =
                    input.value.trim();

                const valor =
                    Number(texto);

                const fila =
                    input.closest("tr");

                const pendientes =
                    fila
                        ? parseInt(
                            fila.dataset.pendientes || "0",
                            10
                        )
                        : 0;


                if(
                    texto === ""
                    ||
                    !Number.isInteger(valor)
                    ||
                    valor < 0
                    ||
                    valor > pendientes
                ){

                    e.preventDefault();

                    alert(
                        "Los días de SAC deben ser un número entero entre 0 y los días pendientes del empleado."
                    );

                    input.focus();

                    return;
                }
            }
        }
    );
}


/*
|--------------------------------------------------------------------------
| INICIALIZACIÓN
|--------------------------------------------------------------------------
*/

document.addEventListener(
    "DOMContentLoaded",
    function(){

        document
            .querySelectorAll(
                ".input-dias-sac"
            )
            .forEach(function(input){

                actualizarFilaSac(
                    input
                );
            });


        actualizarContador();
    }
);

