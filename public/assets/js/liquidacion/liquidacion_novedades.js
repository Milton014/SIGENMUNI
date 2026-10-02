document.querySelectorAll('[data-event-click="liquidacion_novedades-1"]').forEach(function (elemento) {
    elemento.addEventListener('click', function (event) {
        const resultado = (function (event) {
establecerTodosDiasHabilitados();
        }).call(this, event);
        if (resultado === false) event.preventDefault();
    });
});

document.querySelectorAll('[data-event-click="liquidacion_novedades-2"]').forEach(function (elemento) {
    elemento.addEventListener('click', function (event) {
        const resultado = (function (event) {
establecerPresentismoTodos(true);
        }).call(this, event);
        if (resultado === false) event.preventDefault();
    });
});

document.querySelectorAll('[data-event-click="liquidacion_novedades-3"]').forEach(function (elemento) {
    elemento.addEventListener('click', function (event) {
        const resultado = (function (event) {
establecerPresentismoTodos(false);
        }).call(this, event);
        if (resultado === false) event.preventDefault();
    });
});

document.querySelectorAll('[data-event-change="liquidacion_novedades-4"]').forEach(function (elemento) {
    elemento.addEventListener('change', function (event) {
        const resultado = (function (event) {
actualizarFila(this);
        }).call(this, event);
        if (resultado === false) event.preventDefault();
    });
});

document.querySelectorAll('[data-event-change="liquidacion_novedades-5"]').forEach(function (elemento) {
    elemento.addEventListener('change', function (event) {
        const resultado = (function (event) {
actualizarEstadoPresentismo(this); actualizarFila(this);
        }).call(this, event);
        if (resultado === false) event.preventDefault();
    });
});

document.querySelectorAll('[data-event-input="liquidacion_novedades-6"]').forEach(function (elemento) {
    elemento.addEventListener('input', function (event) {
        const resultado = (function (event) {
actualizarFila(this);
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
    document.getElementById("buscarEmpleado");

if(buscador){

    buscador.addEventListener(
        "input",
        function(){

            const texto =
                this.value
                    .trim()
                    .toLowerCase();

            const filas =
                document.querySelectorAll(
                    "#cuerpoEmpleados tr"
                );

            filas.forEach(function(fila){

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
| USAR DÍAS LABORALES HABILITADOS
|--------------------------------------------------------------------------
*/

function establecerTodosDiasHabilitados(){

    document
        .querySelectorAll(
            ".input-dias"
        )
        .forEach(function(input){

            const maximo =
                parseInt(
                    input.dataset.maxHabilitado
                    || input.max
                    || "30",
                    10
                );


            input.value =
                isNaN(maximo)
                    ? 0
                    : maximo;


            actualizarFila(
                input
            );
        });
}


/*
|--------------------------------------------------------------------------
| PRESENTISMO MASIVO
|--------------------------------------------------------------------------
*/

function establecerPresentismoTodos(estado){

    document
        .querySelectorAll(
            ".input-presentismo"
        )
        .forEach(function(input){

            input.checked =
                estado;

            actualizarEstadoPresentismo(
                input
            );

            actualizarFila(
                input
            );
        });
}


/*
|--------------------------------------------------------------------------
| TEXTO SÍ / NO
|--------------------------------------------------------------------------
*/

function actualizarEstadoPresentismo(input){

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


/*
|--------------------------------------------------------------------------
| ACTUALIZAR ESTADO VISUAL DE LA FILA
|--------------------------------------------------------------------------
*/

function actualizarFila(elemento){

    const fila =
        elemento.closest(
            "tr"
        );

    if(!fila){
        return;
    }

    const diasInput =
        fila.querySelector(
            ".input-dias"
        );

    const presentismoInput =
        fila.querySelector(
            ".input-presentismo"
        );

    const observacionInput =
        fila.querySelector(
            ".input-observacion"
        );

    if(
        !diasInput
        ||
        !presentismoInput
        ||
        !observacionInput
    ){
        return;
    }

    let dias =
        parseInt(
            diasInput.value || "0",
            10
        );


    if(isNaN(dias)){
        dias = 0;
    }


    /*
    | Si no hay días liquidados, no corresponde presentismo.
    */

    if(dias === 0){

        presentismoInput.checked =
            false;

        actualizarEstadoPresentismo(
            presentismoInput
        );
    }


    const diasHabilitados =
        parseInt(
            fila.dataset.diasHabilitados
            || diasInput.dataset.maxHabilitado
            || diasInput.max
            || "30",
            10
        );


    const maximoLaboral =
        isNaN(diasHabilitados)
            ? 30
            : diasHabilitados;


    /*
    | Es novedad cuando el operador reduce los días respecto del máximo
    | laboral habilitado, quita presentismo o agrega una observación.
    | Tener menos de 30 días por alta/inactivación NO es, por sí solo,
    | una novedad manual.
    */

    const esNovedad =
        dias !== maximoLaboral
        ||
        !presentismoInput.checked
        ||
        observacionInput.value.trim() !== "";


    fila.classList.toggle(
        "fila-novedad",
        esNovedad
    );
}


/*
|--------------------------------------------------------------------------
| VALIDACIÓN ANTES DE ENVIAR
|--------------------------------------------------------------------------
*/

const formulario =
    document.getElementById(
        "formNovedades"
    );

if(formulario){

    formulario.addEventListener(
        "submit",
        function(e){

            const inputsDias =
                document.querySelectorAll(
                    ".input-dias"
                );


            for(
                const input
                of
                inputsDias
            ){

                const valor =
                    parseInt(
                        input.value,
                        10
                    );


                const maximo =
                    parseInt(
                        input.dataset.maxHabilitado
                        || input.max
                        || "30",
                        10
                    );


                if(
                    isNaN(valor)
                    ||
                    valor < 0
                    ||
                    isNaN(maximo)
                    ||
                    valor > maximo
                ){

                    e.preventDefault();

                    alert(
                        "Los días a liquidar deben estar entre 0 y "
                        + (
                            isNaN(maximo)
                                ? 30
                                : maximo
                        )
                        + " para este empleado."
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
| INICIALIZAR
|--------------------------------------------------------------------------
*/

document.addEventListener(
    "DOMContentLoaded",
    function(){

        document
            .querySelectorAll(
                ".input-presentismo"
            )
            .forEach(
                actualizarEstadoPresentismo
            );
    }
);

