

/*
|--------------------------------------------------------------------------
| CATEGORÍA / ESCALAFÓN
|--------------------------------------------------------------------------
|
| Cargos especiales (código >= 1000):
| - Escalafón no corresponde.
| - El select queda bloqueado.
|
| Categorías generales:
| - Escalafón obligatorio.
| - Se habilitan las opciones disponibles.
|
|--------------------------------------------------------------------------
*/

function actualizarEscalafonPorCategoria(){

    const categoria =
        document.getElementById("categoria_id");

    const escalafon =
        document.getElementById("escalafon_id");

    const label =
        document.getElementById("label_escalafon");

    const ayuda =
        document.getElementById("ayuda_escalafon");


    const opcion =
        categoria.options[
            categoria.selectedIndex
        ];


    const codigo =
        opcion
        ?
        parseInt(
            opcion.dataset.codigo || "0",
            10
        )
        :
        0;


    if(codigo >= 1000){

        escalafon.value = "";

        escalafon.disabled = true;

        label.textContent =
            "Escalafón";

        ayuda.textContent =
            "No corresponde para cargos o categorías especiales.";

    }else{

        escalafon.disabled = false;

        label.textContent =
            "Escalafón *";

        ayuda.textContent =
            "Obligatorio para categorías generales.";
    }
}


document
    .getElementById("categoria_id")
    .addEventListener(
        "change",
        actualizarEscalafonPorCategoria
    );


document.addEventListener(
    "DOMContentLoaded",
    actualizarEscalafonPorCategoria
);


document
    .getElementById("formEmpleado")
    .addEventListener("submit", function(e){

    const alerta =
        document.getElementById("alertaEmpleado");

    const campos = [
        "nro_legajo",
        "apellido",
        "nombre",
        "dni",
        "cuil",
        "fecha_alta",
        "email",
        "institucion_id",
        "oficina_id",
        "situacion_id",
        "escalafon_id",
        "categoria_id"
    ];


    /*
    |--------------------------------------------------------------------------
    | LIMPIAR ERRORES ANTERIORES
    |--------------------------------------------------------------------------
    */

    campos.forEach(function(id){

        const campo =
            document.getElementById(id);

        if(campo){
            campo.classList.remove("input-error");
        }

    });


    alerta.style.display = "none";

    alerta.innerHTML = "";


    /*
    |--------------------------------------------------------------------------
    | MOSTRAR ERROR
    |--------------------------------------------------------------------------
    */

    function mostrarError(mensaje, id){

        e.preventDefault();

        const campo =
            document.getElementById(id);

        alerta.innerHTML = mensaje;

        alerta.style.display = "block";

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


    /*
    |--------------------------------------------------------------------------
    | OBTENER VALORES
    |--------------------------------------------------------------------------
    */

    const nroLegajo =
        document
            .getElementById("nro_legajo")
            .value
            .trim();

    const apellido =
        document
            .getElementById("apellido")
            .value
            .trim();

    const nombre =
        document
            .getElementById("nombre")
            .value
            .trim();

    const dni =
        document
            .getElementById("dni")
            .value
            .trim();

    const cuil =
        document
            .getElementById("cuil")
            .value
            .trim();

    const fechaAlta =
        document
            .getElementById("fecha_alta")
            .value
            .trim();

    const email =
        document
            .getElementById("email")
            .value
            .trim();


    /*
    |--------------------------------------------------------------------------
    | VALIDAR LEGAJO
    |--------------------------------------------------------------------------
    */

    if(nroLegajo === ""){

        mostrarError(
            "Debe ingresar el número de legajo.",
            "nro_legajo"
        );

        return;
    }


    if(!/^[0-9]+$/.test(nroLegajo)){

        mostrarError(
            "El número de legajo debe contener solo números.",
            "nro_legajo"
        );

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDAR APELLIDO
    |--------------------------------------------------------------------------
    */

    if(apellido === ""){

        mostrarError(
            "Debe ingresar el apellido.",
            "apellido"
        );

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDAR NOMBRE
    |--------------------------------------------------------------------------
    */

    if(nombre === ""){

        mostrarError(
            "Debe ingresar el nombre.",
            "nombre"
        );

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDAR DNI
    |--------------------------------------------------------------------------
    */

    if(dni === ""){

        mostrarError(
            "Debe ingresar el DNI.",
            "dni"
        );

        return;
    }


    if(!/^[0-9]{7,8}$/.test(dni)){

        mostrarError(
            "El DNI debe tener entre 7 y 8 números.",
            "dni"
        );

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDAR CUIL
    |--------------------------------------------------------------------------
    */

    if(cuil === ""){

        mostrarError(
            "Debe ingresar el CUIL.",
            "cuil"
        );

        return;
    }


    if(!/^[0-9]{11}$/.test(cuil)){

        mostrarError(
            "El CUIL debe tener exactamente 11 números.",
            "cuil"
        );

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDAR FECHA
    |--------------------------------------------------------------------------
    */

    if(fechaAlta === ""){

        mostrarError(
            "Debe seleccionar la fecha de alta.",
            "fecha_alta"
        );

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDAR EMAIL
    |--------------------------------------------------------------------------
    */

    if(email === ""){

        mostrarError(
            "Debe ingresar el email.",
            "email"
        );

        return;
    }


    const formatoEmail =
        /^[^\s@]+@[^\s@]+\.[^\s@]+$/;


    if(!formatoEmail.test(email)){

        mostrarError(
            "Debe ingresar un email válido.",
            "email"
        );

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDAR COMBOS
    |--------------------------------------------------------------------------
    */

    if(
        document
            .getElementById("institucion_id")
            .value === ""
    ){

        mostrarError(
            "Debe seleccionar una institución.",
            "institucion_id"
        );

        return;
    }


    if(
        document
            .getElementById("oficina_id")
            .value === ""
    ){

        mostrarError(
            "Debe seleccionar una Unidad de Organización.",
            "oficina_id"
        );

        return;
    }


    if(
        document
            .getElementById("situacion_id")
            .value === ""
    ){

        mostrarError(
            "Debe seleccionar una situación.",
            "situacion_id"
        );

        return;
    }


    const categoriaSelect =
        document.getElementById("categoria_id");


    if(
        categoriaSelect.value === ""
    ){

        mostrarError(
            "Debe seleccionar una categoría.",
            "categoria_id"
        );

        return;
    }


    const opcionCategoria =
        categoriaSelect.options[
            categoriaSelect.selectedIndex
        ];


    const codigoCategoria =
        opcionCategoria
        ?
        parseInt(
            opcionCategoria.dataset.codigo || "0",
            10
        )
        :
        0;


    const escalafonSelect =
        document.getElementById("escalafon_id");


    if(
        codigoCategoria < 1000
        &&
        escalafonSelect.value === ""
    ){

        mostrarError(
            "Debe seleccionar un escalafón para la categoría.",
            "escalafon_id"
        );

        return;
    }

});

