document.querySelectorAll('[data-event-change="concepto_nuevo-1"]').forEach(function (elemento) {
    elemento.addEventListener('change', function (event) {
        const resultado = (function (event) {
actualizarBaseCalculo()
        }).call(this, event);
        if (resultado === false) event.preventDefault();
    });
});

document.querySelectorAll('[data-event-change="concepto_nuevo-2"]').forEach(function (elemento) {
    elemento.addEventListener('change', function (event) {
        const resultado = (function (event) {
actualizarFormaCalculo(); actualizarBaseCalculo();
        }).call(this, event);
        if (resultado === false) event.preventDefault();
    });
});



/*
|--------------------------------------------------------------------------
| CONFIGURAR FORMA DE CÁLCULO
|--------------------------------------------------------------------------
*/

function actualizarFormaCalculo(){

    const forma =
        document.getElementById("forma_calculo").value;

    const codigo =
        document.getElementById("codigo").value.trim();

    const ayudaForma =
        document.getElementById("ayudaForma");


    if(forma === "TABLA_CATEGORIA"){

        ayudaForma.innerHTML =
            "El monto se administra desde <strong>Valores del Concepto</strong> por categoría y vigencia. Este concepto no se asigna individualmente desde Conceptos por Empleado.";

    } else if(forma === "MANUAL"){

        ayudaForma.innerHTML =
            "Este concepto se podrá asignar desde <strong>Conceptos por Empleado</strong>. El importe se cargará individualmente para cada empleado.";

    } else if(forma === "PORCENTAJE"){

        ayudaForma.innerHTML =
            "Este concepto se podrá asignar desde <strong>Conceptos por Empleado</strong>. El porcentaje se cargará individualmente para cada empleado y se aplicará sobre la base seleccionada debajo.";

    } else if(forma === "FORMULA"){

        ayudaForma.innerHTML =
            "Este concepto será calculado <strong>automáticamente</strong> por la lógica de liquidación y no se asigna individualmente a los empleados.";

    } else {

        ayudaForma.innerHTML =
            "Seleccione cómo se obtiene el valor del concepto.";
    }


    /*
    |------------------------------------------------------------------
    | AYUDA ESPECIAL PARA 101 / 102 / 104
    |------------------------------------------------------------------
    */

    if(
        codigo === "101" ||
        codigo === "102" ||
        codigo === "104"
    ){

        if(forma !== "TABLA_CATEGORIA"){
            ayudaForma.innerHTML +=
                "<br><strong>Importante:</strong> los conceptos 101, 102 y 104 deben utilizar VALOR POR CATEGORÍA.";
        }
    }
}


/*
|--------------------------------------------------------------------------
| CONFIGURAR BASE DEL PORCENTAJE
|--------------------------------------------------------------------------
|
| Se muestra para cualquier concepto cuya:
|
| Forma = PORCENTAJE
|
*/

function actualizarBaseCalculo(){

    const forma =
        document.getElementById("forma_calculo").value;

    const contenedor =
        document.getElementById("contenedorBaseCalculo");

    const base =
        document.getElementById("base_calculo");


    if(forma === "PORCENTAJE"){

        contenedor.style.display = "block";

    } else {

        contenedor.style.display = "none";

        if(base){
            base.value = "";
        }
    }
}


/*
|--------------------------------------------------------------------------
| VALIDACIÓN DEL FORMULARIO
|--------------------------------------------------------------------------
*/

document
    .getElementById("formConcepto")
    .addEventListener(
        "submit",
        function(e){

    const alerta =
        document.getElementById("alertaConcepto");

    const campos = [
        "codigo",
        "nombre",
        "categoria",
        "forma_calculo",
        "base_calculo",
        "fecha_desde",
        "fecha_hasta"
    ];


    campos.forEach(function(id){

        const campo =
            document.getElementById(id);

        if(campo){
            campo.classList.remove("input-error");
        }
    });


    alerta.style.display = "none";
    alerta.innerHTML = "";


    function mostrarError(mensaje,id){

        e.preventDefault();

        const campo =
            document.getElementById(id);

        alerta.innerHTML = mensaje;
        alerta.style.display = "block";

        if(campo){
            campo.classList.add("input-error");
            campo.focus();
        }

        window.scrollTo({
            top:0,
            behavior:"smooth"
        });
    }


    const codigo =
        document
            .getElementById("codigo")
            .value
            .trim();

    const nombre =
        document
            .getElementById("nombre")
            .value
            .trim();

    const categoria =
        document
            .getElementById("categoria")
            .value;

    const formaCalculo =
        document
            .getElementById("forma_calculo")
            .value;

    const baseCalculo =
        document
            .getElementById("base_calculo")
            .value;


    const fechaDesde =
        document
            .getElementById("fecha_desde")
            .value;

    const fechaHasta =
        document
            .getElementById("fecha_hasta")
            .value;


    if(codigo === ""){

        mostrarError(
            "Debe ingresar el código del concepto.",
            "codigo"
        );

        return;
    }


    if(!/^[0-9]+$/.test(codigo)){

        mostrarError(
            "El código debe contener solo números.",
            "codigo"
        );

        return;
    }


    if(parseInt(codigo,10) <= 0){

        mostrarError(
            "El código debe ser mayor a cero.",
            "codigo"
        );

        return;
    }


    if(nombre === ""){

        mostrarError(
            "Debe ingresar el nombre del concepto.",
            "nombre"
        );

        return;
    }


    if(categoria === ""){

        mostrarError(
            "Debe seleccionar un tipo de concepto.",
            "categoria"
        );

        return;
    }


    if(formaCalculo === ""){

        mostrarError(
            "Debe seleccionar una forma de cálculo.",
            "forma_calculo"
        );

        return;
    }


    if(
        (codigo === "101" || codigo === "102" || codigo === "104")
        &&
        formaCalculo !== "TABLA_CATEGORIA"
    ){

        mostrarError(
            "Los conceptos 101, 102 y 104 deben utilizar la forma VALOR POR CATEGORÍA.",
            "forma_calculo"
        );

        return;
    }


    if(
        formaCalculo === "TABLA_CATEGORIA"
        &&
        codigo !== "101"
        &&
        codigo !== "102"
        &&
        codigo !== "104"
    ){

        mostrarError(
            "En la nueva organización, VALOR POR CATEGORÍA se reserva para Sueldo Básico (101), Dedicación Funcional (102) y Suplemento Especial (104).",
            "forma_calculo"
        );

        return;
    }


    if(
        formaCalculo === "PORCENTAJE"
        &&
        baseCalculo === ""
    ){

        mostrarError(
            "Debe seleccionar sobre qué base se aplicará el porcentaje del concepto.",
            "base_calculo"
        );

        return;
    }


    if(
        formaCalculo === "PORCENTAJE"
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
            "base_calculo"
        );

        return;
    }


    if(
        fechaDesde !== "" &&
        fechaHasta !== "" &&
        fechaHasta < fechaDesde
    ){

        mostrarError(
            "La fecha hasta no puede ser anterior a la fecha desde.",
            "fecha_hasta"
        );

        return;
    }




});


/*
|--------------------------------------------------------------------------
| EVENTOS
|--------------------------------------------------------------------------
*/

document
    .getElementById("codigo")
    .addEventListener(
        "input",
        actualizarFormaCalculo
    );


document.addEventListener(
    "DOMContentLoaded",
    function(){
        actualizarFormaCalculo();
        actualizarBaseCalculo();
    }
);

