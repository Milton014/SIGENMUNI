const datosPagina = JSON.parse(document.getElementById('datos-concepto_valor_editar').textContent);

document.querySelectorAll('[data-event-change="concepto_valor_editar-1"]').forEach(function (elemento) {
    elemento.addEventListener('change', function (event) {
        const resultado = (function (event) {
actualizarFormulario()
        }).call(this, event);
        if (resultado === false) event.preventDefault();
    });
});



/*
|--------------------------------------------------------------------------
| FORMAS DE CÁLCULO
|--------------------------------------------------------------------------
*/

const formasConcepto = datosPagina.formasConcepto;


/*
|--------------------------------------------------------------------------
| BLOQUEAR SELECT
|--------------------------------------------------------------------------
*/

function bloquearSelect(
    select,
    limpiar = true
){

    if (!select) {
        return;
    }

    if (select.type === "hidden") {

        if (limpiar) {
            select.value = "";
        }

        return;
    }

    select.disabled = true;

    select.classList.add(
        "deshabilitado"
    );


    if(limpiar){

        select.value = "";
    }
}


/*
|--------------------------------------------------------------------------
| HABILITAR SELECT
|--------------------------------------------------------------------------
*/

function habilitarSelect(select){

    if (!select) {
        return;
    }

    if (select.type === "hidden") {
        return;
    }

    select.disabled = false;

    select.classList.remove(
        "deshabilitado"
    );
}


/*
|--------------------------------------------------------------------------
| BLOQUEAR INPUT
|--------------------------------------------------------------------------
*/

function bloquearInput(
    input,
    valor = ""
){

    input.readOnly = true;

    input.classList.add(
        "deshabilitado"
    );


    if(valor !== null){

        input.value = valor;
    }
}


/*
|--------------------------------------------------------------------------
| HABILITAR INPUT
|--------------------------------------------------------------------------
*/

function habilitarInput(input){

    input.readOnly = false;

    input.classList.remove(
        "deshabilitado"
    );
}


/*
|--------------------------------------------------------------------------
| ACTUALIZAR FORMULARIO
|--------------------------------------------------------------------------
*/

function actualizarFormulario(){

    const conceptoId =
        document
            .getElementById(
                "concepto_id"
            )
            .value;


    const forma =
        formasConcepto[
            conceptoId
        ] || "";


    const categoria =
        document
            .getElementById(
                "categoria_id"
            );


    const escalafon =
        document
            .getElementById(
                "escalafon_id"
            );


    const monto =
        document
            .getElementById(
                "monto"
            );


    const porcentaje =
        document
            .getElementById(
                "porcentaje"
            );


    const ayuda =
        document
            .getElementById(
                "mensaje_ayuda"
            );


    const botonGuardar =
        document
            .getElementById(
                "btnGuardar"
            );


    /*
    |--------------------------------------------------------------------------
    | ESTADO BASE
    |--------------------------------------------------------------------------
    */

    habilitarSelect(
        categoria
    );


    habilitarSelect(
        escalafon
    );


    botonGuardar.disabled =
        false;


    botonGuardar.style.opacity =
        "1";


    botonGuardar.style.cursor =
        "pointer";


    /*
    |--------------------------------------------------------------------------
    | TABLA POR CATEGORÍA
    |--------------------------------------------------------------------------
    */

    if(
        forma ===
        "TABLA_CATEGORIA"
    ){

        habilitarSelect(
            categoria
        );


        bloquearSelect(
            escalafon,
            true
        );


        habilitarInput(
            monto
        );


        bloquearInput(
            porcentaje,
            "0.00"
        );


        ayuda.innerHTML =
            "Este concepto se carga por categoría. Seleccione una categoría e ingrese el monto correspondiente.";
    }


    /*
    |--------------------------------------------------------------------------
    | PORCENTAJE
    |--------------------------------------------------------------------------
    */

    else if(
        forma ===
        "PORCENTAJE"
    ){

        habilitarSelect(
            categoria
        );


        habilitarSelect(
            escalafon
        );


        bloquearInput(
            monto,
            "0.00"
        );


        habilitarInput(
            porcentaje
        );


        ayuda.innerHTML =
            "Este concepto utiliza un porcentaje. Categoría y escalafón son opcionales.";
    }


    /*
    |--------------------------------------------------------------------------
    | FIJO
    |--------------------------------------------------------------------------
    */

    else if(
        forma ===
        "FIJO"
    ){

        habilitarSelect(
            categoria
        );


        habilitarSelect(
            escalafon
        );


        habilitarInput(
            monto
        );


        bloquearInput(
            porcentaje,
            "0.00"
        );


        ayuda.innerHTML =
            "Este concepto utiliza un monto fijo. Categoría y escalafón son opcionales.";
    }


    /*
    |--------------------------------------------------------------------------
    | MANUAL / FORMULA
    |--------------------------------------------------------------------------
    */

    else if(
        forma === "MANUAL" ||
        forma === "FORMULA"
    ){

        bloquearSelect(
            categoria,
            true
        );


        bloquearSelect(
            escalafon,
            true
        );


        bloquearInput(
            monto,
            "0.00"
        );


        bloquearInput(
            porcentaje,
            "0.00"
        );


        botonGuardar.disabled =
            true;


        botonGuardar.style.opacity =
            ".55";


        botonGuardar.style.cursor =
            "not-allowed";


        ayuda.innerHTML =
            "Este tipo de concepto no admite carga directa de valores.";
    }


    /*
    |--------------------------------------------------------------------------
    | SIN CONCEPTO
    |--------------------------------------------------------------------------
    */

    else{

        bloquearInput(
            monto,
            "0.00"
        );


        bloquearInput(
            porcentaje,
            "0.00"
        );


        ayuda.innerHTML =
            "Seleccione un concepto para continuar.";
    }
}


/*
|--------------------------------------------------------------------------
| VALIDAR FORMULARIO
|--------------------------------------------------------------------------
*/

document
    .getElementById(
        "formValor"
    )
    .addEventListener(
        "submit",
        function(e)
    {

        const alerta =
            document
                .getElementById(
                    "alertaValor"
                );


        const campos = [
            "concepto_id",
            "categoria_id",
            "escalafon_id",
            "monto",
            "porcentaje",
            "fecha_desde",
            "fecha_hasta"
        ];


        /*
        |--------------------------------------------------------------------------
        | LIMPIAR ERRORES
        |--------------------------------------------------------------------------
        */

        campos.forEach(
            function(id)
            {

                const campo =
                    document
                        .getElementById(id);


                if(campo){

                    campo
                        .classList
                        .remove(
                            "input-error"
                        );
                }
            }
        );


        alerta.style.display =
            "none";


        alerta.innerHTML =
            "";


        /*
        |--------------------------------------------------------------------------
        | MOSTRAR ERROR
        |--------------------------------------------------------------------------
        */

        function mostrarError(
            mensaje,
            id
        ){

            e.preventDefault();


            const campo =
                document
                    .getElementById(id);


            alerta.innerHTML =
                mensaje;


            alerta.style.display =
                "block";


            if(campo){

                campo
                    .classList
                    .add(
                        "input-error"
                    );


                campo.focus();
            }


            window.scrollTo({

                top:0,

                behavior:"smooth"

            });
        }


        /*
        |--------------------------------------------------------------------------
        | DATOS
        |--------------------------------------------------------------------------
        */

        const concepto =
            document
                .getElementById(
                    "concepto_id"
                )
                .value;


        const forma =
            formasConcepto[
                concepto
            ] || "";


        const categoria =
            document
                .getElementById(
                    "categoria_id"
                )
                .value;


        const monto =
            parseFloat(
                document
                    .getElementById(
                        "monto"
                    )
                    .value || 0
            );


        const porcentaje =
            parseFloat(
                document
                    .getElementById(
                        "porcentaje"
                    )
                    .value || 0
            );


        const fechaDesde =
            document
                .getElementById(
                    "fecha_desde"
                )
                .value;


        const fechaHasta =
            document
                .getElementById(
                    "fecha_hasta"
                )
                .value;


        /*
        |--------------------------------------------------------------------------
        | CONCEPTO
        |--------------------------------------------------------------------------
        */

        if(concepto === ""){

            mostrarError(
                "Debe seleccionar un concepto.",
                "concepto_id"
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | MANUAL / FORMULA
        |--------------------------------------------------------------------------
        */

        if(
            forma === "MANUAL" ||
            forma === "FORMULA"
        ){

            mostrarError(
                "Este tipo de concepto no admite carga directa de valores.",
                "concepto_id"
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | FECHA DESDE
        |--------------------------------------------------------------------------
        */

        if(fechaDesde === ""){

            mostrarError(
                "Debe seleccionar la fecha desde.",
                "fecha_desde"
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | RANGO DE FECHAS
        |--------------------------------------------------------------------------
        */

        if(
            fechaHasta !== "" &&
            fechaHasta < fechaDesde
        ){

            mostrarError(
                "La fecha hasta no puede ser anterior a la fecha desde.",
                "fecha_hasta"
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | TABLA POR CATEGORÍA
        |--------------------------------------------------------------------------
        */

        if(
            forma ===
            "TABLA_CATEGORIA"
        ){

            if(categoria === ""){

                mostrarError(
                    "Debe seleccionar una categoría para este concepto.",
                    "categoria_id"
                );

                return;
            }


            if(monto <= 0){

                mostrarError(
                    "Debe ingresar un monto mayor a cero.",
                    "monto"
                );

                return;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | FIJO
        |--------------------------------------------------------------------------
        */

        if(forma === "FIJO"){

            if(monto <= 0){

                mostrarError(
                    "Debe ingresar un monto mayor a cero.",
                    "monto"
                );

                return;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | PORCENTAJE
        |--------------------------------------------------------------------------
        */

        if(
            forma ===
            "PORCENTAJE"
        ){

            if(porcentaje <= 0){

                mostrarError(
                    "Debe ingresar un porcentaje mayor a cero.",
                    "porcentaje"
                );

                return;
            }
        }

    }
);


/*
|--------------------------------------------------------------------------
| CONFIGURACIÓN INICIAL
|--------------------------------------------------------------------------
*/

document.addEventListener(
    "DOMContentLoaded",
    function(){

        actualizarFormulario();

    }
);

