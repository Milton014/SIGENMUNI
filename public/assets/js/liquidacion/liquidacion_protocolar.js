

/*
|--------------------------------------------------------------------------
| HABILITAR / DESHABILITAR FILA
|--------------------------------------------------------------------------
*/

function actualizarFila(checkbox){

    const fila =
        checkbox.closest("tr");

    if(!fila){
        return;
    }


    const importe =
        fila.querySelector(
            ".input-importe"
        );

    const prevision =
        fila.querySelector(
            ".select-prevision"
        );


    if(checkbox.checked){

        importe.disabled = false;

        prevision.disabled = false;

        if(
            importe.value.trim()
            ===
            ""
        ){
            importe.focus();
        }

    }else{

        importe.disabled = true;

        prevision.disabled = true;

        importe.classList.remove(
            "input-error"
        );
    }


    actualizarContador();
}


/*
|--------------------------------------------------------------------------
| CONTADOR
|--------------------------------------------------------------------------
*/

function actualizarContador(){

    const total =
        document.querySelectorAll(
            ".chk-incluir:checked"
        ).length;

    const contador =
        document.getElementById(
            "contadorIncluidos"
        );

    if(contador){
        contador.textContent = total;
    }
}


/*
|--------------------------------------------------------------------------
| EVENTOS CHECKBOX
|--------------------------------------------------------------------------
*/

document
    .querySelectorAll(
        ".chk-incluir"
    )
    .forEach(
        function(checkbox){

            checkbox.addEventListener(
                "change",
                function(){

                    actualizarFila(
                        checkbox
                    );
                }
            );
        }
    );


/*
|--------------------------------------------------------------------------
| VALIDACIÓN
|--------------------------------------------------------------------------
*/

document
    .getElementById(
        "formProtocolar"
    )
    ?.addEventListener(
        "submit",
        function(e){

            const seleccionados =
                document.querySelectorAll(
                    ".chk-incluir:checked"
                );


            if(
                seleccionados.length
                ===
                0
            ){

                e.preventDefault();

                alert(
                    "Debe incluir al menos un empleado en Gastos Protocolares."
                );

                return;
            }


            let valido = true;


            seleccionados.forEach(
                function(checkbox){

                    if(!valido){
                        return;
                    }


                    const fila =
                        checkbox.closest("tr");

                    const importe =
                        fila.querySelector(
                            ".input-importe"
                        );


                    importe.classList.remove(
                        "input-error"
                    );


                    const valor =
                        parseFloat(
                            importe.value
                        );


                    if(
                        importe.value.trim()
                        ===
                        ""
                        ||
                        isNaN(valor)
                        ||
                        valor <= 0
                    ){

                        valido = false;

                        importe.classList.add(
                            "input-error"
                        );

                        importe.focus();
                    }
                }
            );


            if(!valido){

                e.preventDefault();

                alert(
                    "Todos los empleados incluidos deben tener un importe mayor a cero."
                );
            }
        }
    );


/*
|--------------------------------------------------------------------------
| ESTADO INICIAL
|--------------------------------------------------------------------------
*/

document.addEventListener(
    "DOMContentLoaded",
    function(){

        document
            .querySelectorAll(
                ".chk-incluir"
            )
            .forEach(
                function(checkbox){

                    actualizarFila(
                        checkbox
                    );
                }
            );

        actualizarContador();
    }
);

