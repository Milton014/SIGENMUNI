

/*
|--------------------------------------------------------------------------
| AVISO PARA GASTOS PROTOCOLARES
|--------------------------------------------------------------------------
*/

function actualizarAvisoTipoLiquidacion(){

    const tipo =
        document.getElementById(
            "tipo_liquidacion"
        ).value;


    const avisos = [
        "avisoAguinaldo",
        "avisoComplementaria",
        "avisoComplementariaSac",
        "avisoProtocolar"
    ];


    avisos.forEach(function(id){

        const elemento =
            document.getElementById(id);

        if(elemento){
            elemento.style.display = "none";
        }
    });


    let idAviso = "";


    if(tipo === "AGUINALDO"){

        idAviso =
            "avisoAguinaldo";

    }else if(tipo === "COMPLEMENTARIA"){

        idAviso =
            "avisoComplementaria";

    }else if(tipo === "COMPLEMENTARIA_SAC"){

        idAviso =
            "avisoComplementariaSac";

    }else if(tipo === "GASTOS_PROTOCOLARES"){

        idAviso =
            "avisoProtocolar";
    }


    if(idAviso !== ""){

        const aviso =
            document.getElementById(
                idAviso
            );

        if(aviso){
            aviso.style.display = "block";
        }
    }
}


document
    .getElementById("tipo_liquidacion")
    .addEventListener(
        "change",
        actualizarAvisoTipoLiquidacion
    );


document.addEventListener(
    "DOMContentLoaded",
    actualizarAvisoTipoLiquidacion
);



/*
|--------------------------------------------------------------------------
| AYUDA DE COHERENCIA PERÍODO / FECHA
|--------------------------------------------------------------------------
|
| Para AGUINALDO y COMPLEMENTARIA_SAC la fecha debe pertenecer al mismo
| mes y año indicado en Período.
|
*/

function actualizarAyudaFechaLiquidacion()
{
    const tipo =
        document
            .getElementById(
                "tipo_liquidacion"
            )
            .value;


    const periodo =
        document
            .getElementById(
                "periodo"
            )
            .value;


    const ayuda =
        document
            .getElementById(
                "ayudaFechaLiquidacion"
            );


    if (!ayuda) {
        return;
    }


    if (
        tipo === "AGUINALDO"
        ||
        tipo === "COMPLEMENTARIA_SAC"
    ) {

        if (periodo !== "") {

            ayuda.innerHTML =
                "<strong>Importante:</strong> para SAC la fecha debe pertenecer al período "
                + periodo
                + ".";

        } else {

            ayuda.innerHTML =
                "<strong>Importante:</strong> para SAC la fecha debe pertenecer al mismo mes y año del período seleccionado.";
        }

    } else {

        ayuda.textContent =
            "Fecha en la que se genera la liquidación.";
    }
}


document
    .getElementById(
        "tipo_liquidacion"
    )
    .addEventListener(
        "change",
        actualizarAyudaFechaLiquidacion
    );


document
    .getElementById(
        "periodo"
    )
    .addEventListener(
        "change",
        actualizarAyudaFechaLiquidacion
    );


document.addEventListener(
    "DOMContentLoaded",
    actualizarAyudaFechaLiquidacion
);


/*
|--------------------------------------------------------------------------
| VALIDACIÓN DEL FORMULARIO
|--------------------------------------------------------------------------
*/

document
    .getElementById(
        "formLiquidacion"
    )
    .addEventListener(
        "submit",
        function(e)
        {

            const alerta =
                document.getElementById(
                    "alertaLiquidacion"
                );


            const campos = [
                "tipo_liquidacion",
                "periodo",
                "fecha_liquidacion"
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
                        document.getElementById(
                            id
                        );


                    if (campo) {

                        campo.classList.remove(
                            "input-error"
                        );
                    }
                }
            );


            alerta.style.display =
                "none";


            alerta.textContent =
                "";


            /*
            |--------------------------------------------------------------------------
            | MOSTRAR ERROR
            |--------------------------------------------------------------------------
            */

            function mostrarError(
                mensaje,
                id
            ) {

                e.preventDefault();


                const campo =
                    document.getElementById(
                        id
                    );


                alerta.textContent =
                    mensaje;


                alerta.style.display =
                    "block";


                if (campo) {

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


            /*
            |--------------------------------------------------------------------------
            | VALORES
            |--------------------------------------------------------------------------
            */

            const tipo =
                document
                    .getElementById(
                        "tipo_liquidacion"
                    )
                    .value;


            const periodo =
                document
                    .getElementById(
                        "periodo"
                    )
                    .value;


            const fecha =
                document
                    .getElementById(
                        "fecha_liquidacion"
                    )
                    .value;


            /*
            |--------------------------------------------------------------------------
            | TIPO
            |--------------------------------------------------------------------------
            */

            if (tipo === "") {

                mostrarError(
                    "Debe seleccionar el tipo de liquidación.",
                    "tipo_liquidacion"
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | PERÍODO
            |--------------------------------------------------------------------------
            */

            if (periodo === "") {

                mostrarError(
                    "Debe seleccionar el período de liquidación.",
                    "periodo"
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | AGUINALDO SOLO JUNIO / DICIEMBRE
            |--------------------------------------------------------------------------
            */

            if (tipo === "AGUINALDO") {

                const partesPeriodo =
                    periodo.split("-");

                const mesPeriodo =
                    partesPeriodo.length >= 2
                        ? parseInt(
                            partesPeriodo[1],
                            10
                        )
                        : 0;


                if (
                    mesPeriodo !== 6
                    &&
                    mesPeriodo !== 12
                ) {

                    mostrarError(
                        "La liquidación de AGUINALDO solo puede corresponder a junio o diciembre. Para liquidar SAC en otro mes utilice Complementaria de SAC.",
                        "periodo"
                    );

                    return;
                }
            }


            /*
            |--------------------------------------------------------------------------
            | FECHA
            |--------------------------------------------------------------------------
            */

            if (fecha === "") {

                mostrarError(
                    "Debe seleccionar la fecha de liquidación.",
                    "fecha_liquidacion"
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | COHERENCIA PERÍODO / FECHA PARA SAC
            |--------------------------------------------------------------------------
            */

            if (
                tipo === "AGUINALDO"
                ||
                tipo === "COMPLEMENTARIA_SAC"
            ) {

                const periodoFecha =
                    fecha.length >= 7
                        ? fecha.substring(
                            0,
                            7
                        )
                        : "";


                if (periodoFecha !== periodo) {

                    const nombreTipo =
                        tipo === "AGUINALDO"
                            ? "Aguinaldo"
                            : "Complementaria de SAC";


                    mostrarError(
                        "Para "
                        + nombreTipo
                        + ", la Fecha de Liquidación debe pertenecer al mismo mes y año del período seleccionado ("
                        + periodo
                        + ").",
                        "fecha_liquidacion"
                    );

                    return;
                }
            }

        }
    );

