document.querySelectorAll('[data-event-change="concepto_editar-1"]').forEach(function (elemento) {
    elemento.addEventListener('change', function (event) {
        const resultado = (function (event) {
actualizarConfiguracion(); actualizarBaseCalculo();
        }).call(this, event);
        if (resultado === false) event.preventDefault();
    });
});

document.querySelectorAll('[data-event-change="concepto_editar-2"]').forEach(function (elemento) {
    elemento.addEventListener('change', function (event) {
        const resultado = (function (event) {
actualizarConfiguracion(); actualizarBaseCalculo();
        }).call(this, event);
        if (resultado === false) event.preventDefault();
    });
});

document.querySelectorAll('[data-event-submit="concepto_editar-3"]').forEach(function (elemento) {
    elemento.addEventListener('submit', function (event) {
        const resultado = (function (event) {
return confirm(this.dataset.eventValue);
        }).call(this, event);
        if (resultado === false) event.preventDefault();
    });
});



/*
|--------------------------------------------------------------------------
| CONFIGURAR FORMA SEGÚN LA NUEVA ARQUITECTURA
|--------------------------------------------------------------------------
*/

function actualizarConfiguracion(){

    const codigoInput =
        document.getElementById("codigo");

    const formaSelect =
        document.getElementById("forma_calculo");

    const ayudaForma =
        document.getElementById("ayuda_forma");

    const codigo =
        parseInt(
            codigoInput.value || "0",
            10
        );

    const codigosCategoria =
        [101, 102, 104];

    const usaValoresCategoria =
        codigosCategoria.includes(codigo);


    /*
    |--------------------------------------------------------------------------
    | 101 / 102 / 104
    |--------------------------------------------------------------------------
    */

    if(usaValoresCategoria){

        formaSelect.value =
            "TABLA_CATEGORIA";

        ayudaForma.innerHTML =
            "El monto se administra por categoría en la sección <strong>Valores por Categoría</strong>. Este concepto no se asigna individualmente desde Conceptos por Empleado.";

        actualizarBaseCalculo();

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | RESTO DE CONCEPTOS
    |--------------------------------------------------------------------------
    */

    if(formaSelect.value === "TABLA_CATEGORIA"){

        if(
            Array.from(
                formaSelect.options
            ).some(
                option =>
                    option.value === "MANUAL"
            )
        ){
            formaSelect.value =
                "MANUAL";
        }
    }


    const forma =
        formaSelect.value;


    if(forma === "MANUAL"){

        ayudaForma.innerHTML =
            "Este concepto se podrá asignar desde <strong>Conceptos por Empleado</strong>. El importe se cargará individualmente para cada empleado.";

    }else if(forma === "PORCENTAJE"){

        ayudaForma.innerHTML =
            "Este concepto se podrá asignar desde <strong>Conceptos por Empleado</strong>. El porcentaje se cargará individualmente para cada empleado y se aplicará sobre la base seleccionada debajo.";

    }else if(forma === "FORMULA"){

        ayudaForma.innerHTML =
            "Este concepto será calculado <strong>automáticamente</strong> por la lógica de liquidación y no se asigna individualmente a los empleados.";

    }else{

        ayudaForma.innerHTML =
            "Seleccione cómo se obtiene el valor del concepto.";
    }
}


/*
|--------------------------------------------------------------------------
| CONFIGURAR BASE DEL PORCENTAJE
|--------------------------------------------------------------------------
|
| Corresponde para cualquier concepto cuya:
|
| Forma = PORCENTAJE
|
*/

function actualizarBaseCalculo(){

    const forma =
        document
            .getElementById("forma_calculo")
            .value;

    const contenedor =
        document
            .getElementById("contenedorBaseCalculo");

    const base =
        document
            .getElementById("base_calculo");


    if(forma === "PORCENTAJE"){

        contenedor.style.display =
            "block";

    }else{

        contenedor.style.display =
            "none";

        if(base){
            base.value = "";
        }
    }
}


/*
|--------------------------------------------------------------------------
| VALIDACIÓN
|--------------------------------------------------------------------------
*/

document
    .getElementById("formConcepto")
    .addEventListener(
        "submit",
        function(e){

    const alerta =
        document.getElementById(
            "alertaConcepto"
        );

    const codigoCampo =
        document.getElementById(
            "codigo"
        );

    const nombreCampo =
        document.getElementById(
            "nombre"
        );

    const categoriaCampo =
        document.getElementById(
            "categoria"
        );

    const formaCampo =
        document.getElementById(
            "forma_calculo"
        );

    const baseCalculoCampo =
        document.getElementById(
            "base_calculo"
        );

    const fechaDesdeCampo =
        document.getElementById(
            "fecha_desde"
        );

    const fechaHastaCampo =
        document.getElementById(
            "fecha_hasta"
        );


    [
        codigoCampo,
        nombreCampo,
        categoriaCampo,
        formaCampo,
        baseCalculoCampo,
        fechaDesdeCampo,
        fechaHastaCampo
    ].forEach(function(campo){

        if(campo){
            campo.classList.remove(
                "input-error"
            );
        }
    });


    alerta.style.display =
        "none";

    alerta.innerHTML =
        "";


    function mostrarError(
        mensaje,
        campo
    ){

        e.preventDefault();

        alerta.innerHTML =
            mensaje;

        alerta.style.display =
            "block";

        if(campo){

            campo.classList.add(
                "input-error"
            );

            campo.focus();
        }

        window.scrollTo({
            top:0,
            behavior:"smooth"
        });
    }


    const codigo =
        codigoCampo
            .value
            .trim();

    const nombre =
        nombreCampo
            .value
            .trim();

    const categoria =
        categoriaCampo
            .value;

    const forma =
        formaCampo
            .value;

    const baseCalculo =
        baseCalculoCampo
            ? baseCalculoCampo.value
            : "";

    const fechaDesde =
        fechaDesdeCampo
            .value;

    const fechaHasta =
        fechaHastaCampo
            .value;


    if(codigo === ""){

        mostrarError(
            "Debe ingresar el código.",
            codigoCampo
        );

        return;
    }


    if(!/^[0-9]+$/.test(codigo)){

        mostrarError(
            "El código debe contener solo números.",
            codigoCampo
        );

        return;
    }


    if(parseInt(codigo,10) <= 0){

        mostrarError(
            "El código debe ser mayor a cero.",
            codigoCampo
        );

        return;
    }


    if(nombre === ""){

        mostrarError(
            "Debe ingresar el nombre del concepto.",
            nombreCampo
        );

        return;
    }


    if(categoria === ""){

        mostrarError(
            "Debe seleccionar un tipo de concepto.",
            categoriaCampo
        );

        return;
    }


    if(forma === ""){

        mostrarError(
            "Debe seleccionar una forma de cálculo.",
            formaCampo
        );

        return;
    }


    const codigoNumerico =
        parseInt(
            codigo,
            10
        );

    const codigosCategoria =
        [101, 102, 104];


    if(
        codigosCategoria.includes(
            codigoNumerico
        )
        &&
        forma !== "TABLA_CATEGORIA"
    ){

        mostrarError(
            "Los conceptos 101, 102 y 104 deben utilizar Valor por Categoría.",
            formaCampo
        );

        return;
    }


    if(
        !codigosCategoria.includes(
            codigoNumerico
        )
        &&
        forma === "TABLA_CATEGORIA"
    ){

        mostrarError(
            "Valor por Categoría está reservado para los conceptos 101, 102 y 104.",
            formaCampo
        );

        return;
    }


    if(
        forma === "PORCENTAJE"
        &&
        baseCalculo === ""
    ){

        mostrarError(
            "Debe seleccionar sobre qué base se aplicará el porcentaje del concepto.",
            baseCalculoCampo
        );

        return;
    }


    if(
        forma === "PORCENTAJE"
        &&
        baseCalculo !== "BASICO"
        &&
        baseCalculo !== "BASICO_MAS_DEDICACION"
        &&
        baseCalculo !== "TOTAL_REMUNERATIVO"
        &&
        baseCalculo !== "TOTAL_REMUNERATIVO_MENOS_DESCUENTOS_OBLIGATORIOS"
    ){

        mostrarError(
            "La base seleccionada para el concepto porcentual no es válida.",
            baseCalculoCampo
        );

        return;
    }


    if(
        fechaDesde !== ""
        &&
        fechaHasta !== ""
        &&
        fechaHasta < fechaDesde
    ){

        mostrarError(
            "La fecha hasta no puede ser anterior a la fecha desde.",
            fechaHastaCampo
        );

        return;
    }

});


/*
|--------------------------------------------------------------------------
| EVENTOS
|--------------------------------------------------------------------------
*/

document.addEventListener(
    "DOMContentLoaded",
    function(){

        actualizarConfiguracion();
        actualizarBaseCalculo();

        document
            .getElementById("codigo")
            .addEventListener(
                "input",
                function(){
                    actualizarConfiguracion();
                    actualizarBaseCalculo();
                }
            );

        document
            .getElementById("forma_calculo")
            .addEventListener(
                "change",
                actualizarBaseCalculo
            );
    }
);

