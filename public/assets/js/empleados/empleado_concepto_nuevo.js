document.querySelectorAll('[data-event-change="empleado_concepto_nuevo-1"]').forEach(function (elemento) {
    elemento.addEventListener('change', function (event) {
        const resultado = (function (event) {
actualizarCamposConcepto()
        }).call(this, event);
        if (resultado === false) event.preventDefault();
    });
});



/*
|--------------------------------------------------------------------------
| DATOS DEL CONCEPTO SELECCIONADO
|--------------------------------------------------------------------------
*/

function obtenerConceptoSeleccionado(){

    const select =
        document.getElementById(
            "concepto_id"
        );

    const option =
        select.options[
            select.selectedIndex
        ];

    return {

        id:
            select.value,

        codigo:
            option
                ? (option.dataset.codigo || "")
                : "",

        categoria:
            option
                ? (option.dataset.categoria || "")
                : "",

        forma:
            option
                ? (option.dataset.forma || "")
                : ""
    };
}


/*
|--------------------------------------------------------------------------
| MOSTRAR / OCULTAR CAMPOS
|--------------------------------------------------------------------------
*/

function mostrarGrupo(
    id,
    mostrar
){

    const grupo =
        document.getElementById(id);

    if(!grupo){
        return;
    }

    if(mostrar){

        grupo.classList.add(
            "visible"
        );

    }else{

        grupo.classList.remove(
            "visible"
        );
    }
}


/*
|--------------------------------------------------------------------------
| CONFIGURAR FORMULARIO SEGÚN CONCEPTO
|--------------------------------------------------------------------------
|
| REGLAS DE LA NUEVA ARQUITECTURA:
|
| MANUAL
|   -> Monto
|
| PORCENTAJE
|   -> Porcentaje
|
| ASIGNACION_FAMILIAR
|   -> Monto por unidad + Cantidad
|
| AUTOMÁTICOS
|   -> No deberían aparecer en este selector.
|
*/

function actualizarCamposConcepto(){

    const concepto =
        obtenerConceptoSeleccionado();

    const monto =
        document.getElementById(
            "monto_manual"
        );

    const porcentaje =
        document.getElementById(
            "porcentaje_manual"
        );

    const cantidad =
        document.getElementById(
            "cantidad"
        );

    const labelMonto =
        document.getElementById(
            "labelMonto"
        );

    const ayudaMonto =
        document.getElementById(
            "ayudaMonto"
        );

    const info =
        document.getElementById(
            "conceptoInfo"
        );

    const infoTexto =
        document.getElementById(
            "conceptoInfoTexto"
        );


    /*
    |--------------------------------------------------------------------------
    | SIN CONCEPTO
    |--------------------------------------------------------------------------
    */

    if(concepto.id === ""){

        mostrarGrupo(
            "grupoMonto",
            false
        );

        mostrarGrupo(
            "grupoPorcentaje",
            false
        );

        mostrarGrupo(
            "grupoCantidad",
            false
        );

        info.style.display =
            "none";

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | NORMALIZAR
    |--------------------------------------------------------------------------
    */

    const categoria =
        concepto.categoria.toUpperCase();

    const forma =
        concepto.forma.toUpperCase();


    /*
    |--------------------------------------------------------------------------
    | ASIGNACIÓN FAMILIAR
    |--------------------------------------------------------------------------
    |
    | El importe se administra por empleado.
    | El monto es unitario y Cantidad permite representar 1, 2, 3 hijos, etc.
    |
    */

    if(categoria === "ASIGNACION_FAMILIAR"){

        mostrarGrupo(
            "grupoMonto",
            true
        );

        mostrarGrupo(
            "grupoPorcentaje",
            false
        );

        mostrarGrupo(
            "grupoCantidad",
            true
        );

        porcentaje.value =
            "0.00";

        labelMonto.textContent =
            "Monto por Unidad *";

        ayudaMonto.textContent =
            "Ingrese el importe unitario de la asignación. El sistema lo multiplicará por la cantidad.";

        info.style.display =
            "block";

        infoTexto.innerHTML =
            "<strong>Asignación Familiar</strong><br>"
            + "Cargue el monto unitario y la cantidad que corresponden a este empleado."
            + "<br><span class='etiqueta'>"
            + concepto.codigo
            + "</span>"
            + "<span class='etiqueta'>"
            + categoria
            + "</span>";

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | MANUAL
    |--------------------------------------------------------------------------
    */

    if(forma === "MANUAL"){

        mostrarGrupo(
            "grupoMonto",
            true
        );

        mostrarGrupo(
            "grupoPorcentaje",
            false
        );

        mostrarGrupo(
            "grupoCantidad",
            false
        );

        porcentaje.value =
            "0.00";

        cantidad.value =
            "1";

        labelMonto.textContent =
            "Monto *";

        ayudaMonto.textContent =
            "Ingrese el importe particular que corresponde a este empleado.";

        info.style.display =
            "block";

        infoTexto.innerHTML =
            "<strong>Concepto Manual</strong><br>"
            + "Este concepto se carga mediante un importe individual por empleado."
            + "<br><span class='etiqueta'>"
            + concepto.codigo
            + "</span>"
            + "<span class='etiqueta'>"
            + categoria
            + "</span>";

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | PORCENTAJE
    |--------------------------------------------------------------------------
    */

    if(forma === "PORCENTAJE"){

        mostrarGrupo(
            "grupoMonto",
            false
        );

        mostrarGrupo(
            "grupoPorcentaje",
            true
        );

        mostrarGrupo(
            "grupoCantidad",
            false
        );

        monto.value =
            "0.00";

        cantidad.value =
            "1";

        info.style.display =
            "block";

        infoTexto.innerHTML =
            "<strong>Concepto Porcentual</strong><br>"
            + "Cargue el porcentaje particular que corresponde a este empleado."
            + "<br><span class='etiqueta'>"
            + concepto.codigo
            + "</span>"
            + "<span class='etiqueta'>"
            + categoria
            + "</span>";

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | OTRO TIPO
    |--------------------------------------------------------------------------
    */

    mostrarGrupo(
        "grupoMonto",
        false
    );

    mostrarGrupo(
        "grupoPorcentaje",
        false
    );

    mostrarGrupo(
        "grupoCantidad",
        false
    );

    monto.value =
        "0.00";

    porcentaje.value =
        "0.00";

    cantidad.value =
        "1";

    info.style.display =
        "block";

    infoTexto.innerHTML =
        "<strong>Concepto no editable manualmente.</strong><br>"
        + "Este concepto debería ser calculado automáticamente o administrado desde Gestión de Conceptos.";
}


/*
|--------------------------------------------------------------------------
| VALIDACIÓN DE FORMULARIO
|--------------------------------------------------------------------------
*/

document
    .getElementById(
        "formAsignacion"
    )
    .addEventListener(
        "submit",
        function(e)
        {

            const alerta =
                document.getElementById(
                    "alertaFormulario"
                );


            const campos = [
                "empleado_id",
                "concepto_id",
                "monto_manual",
                "porcentaje_manual",
                "cantidad",
                "fecha_desde",
                "fecha_hasta"
            ];


            campos.forEach(
                function(id)
                {

                    const campo =
                        document.getElementById(id);

                    if(campo){

                        campo.classList.remove(
                            "input-error"
                        );
                    }
                }
            );


            alerta.style.display =
                "none";

            alerta.innerHTML =
                "";


            function mostrarError(
                mensaje,
                id
            ){

                e.preventDefault();

                const campo =
                    document.getElementById(id);

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


            const empleadoId =
                document
                    .getElementById(
                        "empleado_id"
                    )
                    .value;


            const concepto =
                obtenerConceptoSeleccionado();


            const monto =
                parseFloat(
                    document
                        .getElementById(
                            "monto_manual"
                        )
                        .value || 0
                );


            const porcentaje =
                parseFloat(
                    document
                        .getElementById(
                            "porcentaje_manual"
                        )
                        .value || 0
                );


            const cantidad =
                parseFloat(
                    document
                        .getElementById(
                            "cantidad"
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
            | EMPLEADO
            |--------------------------------------------------------------------------
            */

            if(empleadoId === ""){

                mostrarError(
                    "Debe seleccionar un empleado.",
                    "empleado_id"
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | CONCEPTO
            |--------------------------------------------------------------------------
            */

            if(concepto.id === ""){

                mostrarError(
                    "Debe seleccionar un concepto.",
                    "concepto_id"
                );

                return;
            }


            const categoria =
                concepto.categoria
                    .toUpperCase();

            const forma =
                concepto.forma
                    .toUpperCase();


            /*
            |--------------------------------------------------------------------------
            | ASIGNACIÓN FAMILIAR
            |--------------------------------------------------------------------------
            */

            if(
                categoria
                ===
                "ASIGNACION_FAMILIAR"
            ){

                if(
                    Number.isNaN(monto)
                    ||
                    monto <= 0
                ){

                    mostrarError(
                        "Debe ingresar un monto por unidad mayor a cero para la asignación familiar.",
                        "monto_manual"
                    );

                    return;
                }


                if(
                    Number.isNaN(cantidad)
                    ||
                    cantidad <= 0
                ){

                    mostrarError(
                        "La cantidad debe ser mayor a cero.",
                        "cantidad"
                    );

                    return;
                }

            }


            /*
            |--------------------------------------------------------------------------
            | MANUAL
            |--------------------------------------------------------------------------
            */

            else if(
                forma
                ===
                "MANUAL"
            ){

                if(
                    Number.isNaN(monto)
                    ||
                    monto <= 0
                ){

                    mostrarError(
                        "Debe ingresar un monto mayor a cero para este concepto manual.",
                        "monto_manual"
                    );

                    return;
                }
            }


            /*
            |--------------------------------------------------------------------------
            | PORCENTAJE
            |--------------------------------------------------------------------------
            */

            else if(
                forma
                ===
                "PORCENTAJE"
            ){

                if(
                    Number.isNaN(porcentaje)
                    ||
                    porcentaje <= 0
                ){

                    mostrarError(
                        "Debe ingresar un porcentaje mayor a cero.",
                        "porcentaje_manual"
                    );

                    return;
                }
            }


            /*
            |--------------------------------------------------------------------------
            | FORMA NO VÁLIDA PARA ASIGNACIÓN
            |--------------------------------------------------------------------------
            */

            else{

                mostrarError(
                    "El concepto seleccionado no está configurado como MANUAL, PORCENTAJE o ASIGNACIÓN FAMILIAR.",
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
                fechaHasta !== ""
                &&
                fechaHasta < fechaDesde
            ){

                mostrarError(
                    "La fecha hasta no puede ser anterior a la fecha desde.",
                    "fecha_hasta"
                );

                return;
            }

        }
    );


/*
|--------------------------------------------------------------------------
| CARGAR CONFIGURACIÓN INICIAL
|--------------------------------------------------------------------------
*/

document.addEventListener(
    "DOMContentLoaded",
    function(){

        actualizarCamposConcepto();

    }
);

