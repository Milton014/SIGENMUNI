<?php

require_once __DIR__ . '/../../../core/Url.php';
require_once __DIR__ . '/../../../core/Csrf.php';


/*
|--------------------------------------------------------------------------
| EDITAR EMPLEADO - SOLO ROUTER
|--------------------------------------------------------------------------
*/

$empleadoEditarId =
    (int)(
        $empleado['id']
        ?? 0
    );


$empleadoEditarAccion =
    sigenmuniUrlRuta(
        'empleados/editar',
        [
            'id' => $empleadoEditarId
        ]
    );


$empleadoEditarCancelar =
    sigenmuniUrlRuta(
        'empleados'
    );


$empleadoEditarCsrf =
    sigenmuniCsrfToken();


if (!function_exists('formatearCuitUnidadOrganizacion')) {

    function formatearCuitUnidadOrganizacion($cuit)
    {
        $cuit =
            preg_replace(
                '/\D+/',
                '',
                (string)$cuit
            );


        if (strlen($cuit) !== 11) {

            return (string)$cuit;
        }


        return
            substr($cuit, 0, 2)
            . '-'
            . substr($cuit, 2, 8)
            . '-'
            . substr($cuit, 10, 1);
    }
}

?>
<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Editar Empleado - SIGENMUNI</title>

<style>

*{
    box-sizing:border-box;
}

body{
    font-family:Arial,sans-serif;
    background:#f4f7fb;
    margin:0;
    color:#1f2937;
}

/* =========================================
   HEADER
========================================= */

.header{
    background:linear-gradient(135deg,#0f766e,#14b8a6);
    color:white;
    padding:22px 30px;
    box-shadow:0 4px 14px rgba(0,0,0,.10);
}

.header h1{
    margin:0;
    font-size:30px;
}

.header p{
    margin-top:6px;
    font-size:14px;
}

/* =========================================
   CONTENEDOR
========================================= */

.contenedor{
    width:95%;
    max-width:1100px;
    margin:30px auto;
}

.panel{
    background:white;
    padding:28px;
    border-radius:18px;
    box-shadow:0 8px 20px rgba(0,0,0,.08);
}

h2{
    margin-top:0;
    margin-bottom:22px;
    color:#0f766e;
}

/* =========================================
   GRID
========================================= */

.grid{
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:18px;
}

/* =========================================
   CAMPOS
========================================= */

.campo{
    display:flex;
    flex-direction:column;
}

label{
    margin-bottom:6px;
    font-weight:bold;
    font-size:14px;
}

input,
select,
textarea{
    width:100%;
    padding:12px;
    border:1px solid #cbd5e1;
    border-radius:10px;
    outline:none;
    font-size:14px;
    transition:0.2s;
    background:white;
}

input:focus,
select:focus,
textarea:focus{
    border-color:#14b8a6;
    box-shadow:0 0 0 3px rgba(20,184,166,.15);
}

textarea{
    min-height:110px;
    resize:vertical;
}

/* =========================================
   BOTONES
========================================= */

.acciones{
    margin-top:25px;
    display:flex;
    gap:10px;
    flex-wrap:wrap;
}

.btn{
    background:#0f766e;
    color:white;
    padding:11px 16px;
    border:none;
    border-radius:10px;
    text-decoration:none;
    cursor:pointer;
    display:inline-block;
    font-weight:bold;
    font-size:14px;
    transition:0.2s;
}

.btn:hover{
    opacity:0.92;
    transform:translateY(-1px);
}

.btn-sec{
    background:#1f2937;
}

/* =========================================
   MENSAJES
========================================= */

.mensaje{
    padding:14px;
    border-radius:10px;
    margin-bottom:18px;
    font-weight:bold;
}

.error{
    background:#fee2e2;
    color:#991b1b;
    border:1px solid #fecaca;
}

.input-error{
    border-color:#dc2626 !important;
    background:#fff1f2;
    box-shadow:0 0 0 3px rgba(220,38,38,.12);
}

.ayuda-campo{
    margin-top:6px;
    font-size:12px;
    color:#64748b;
    line-height:1.4;
}

select:disabled{
    background:#e9ecef;
    color:#64748b;
    cursor:not-allowed;
}

input[readonly]{
    background:#f1f5f9;
    color:#475569;
    cursor:not-allowed;
}

/* =========================================
   TABLET
========================================= */

@media (max-width:992px){

    .contenedor{
        width:96%;
    }

    .grid{
        grid-template-columns:1fr;
    }
}

/* =========================================
   CELULAR
========================================= */

@media (max-width:768px){

    .header{
        padding:20px;
        text-align:center;
    }

    .header h1{
        font-size:24px;
    }

    .header p{
        font-size:13px;
    }

    .contenedor{
        width:94%;
        margin:20px auto;
    }

    .panel{
        padding:20px;
        border-radius:16px;
    }

    h2{
        font-size:24px;
        text-align:center;
    }

    .grid{
        grid-template-columns:1fr;
        gap:16px;
    }

    .acciones{
        flex-direction:column;
        align-items:stretch;
    }

    .btn{
        width:100%;
        text-align:center;
    }
}

/* =========================================
   CELULARES PEQUEÑOS
========================================= */

@media (max-width:480px){

    .header h1{
        font-size:22px;
    }

    h2{
        font-size:22px;
    }

    .panel{
        padding:18px;
    }

    .btn{
        font-size:13px;
        padding:10px;
    }

    label{
        font-size:13px;
    }
}

</style>

</head>

<body>

<div class="header">

    <h1>SIGENMUNI</h1>

    <p>
        Edición de Empleado Municipal
    </p>

</div>


<div class="contenedor">

    <div class="panel">

        <h2>
            Editar Empleado
        </h2>


        <!-- =====================================
             ALERTA JAVASCRIPT
        ====================================== -->

        <div
            id="alertaEmpleado"
            class="mensaje error"
            style="display:none;"
        >
        </div>


        <!-- =====================================
             MENSAJE DEL CONTROLADOR
        ====================================== -->

        <?php if (!empty($mensaje)): ?>

            <div class="mensaje error">

                <?php
                echo htmlspecialchars(
                    $mensaje,
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>

            </div>

        <?php endif; ?>


        <!-- =====================================
             FORMULARIO
        ====================================== -->

        <form
            method="POST"
            action="<?php
                echo htmlspecialchars(
                    $empleadoEditarAccion,
                    ENT_QUOTES,
                    'UTF-8'
                );
            ?>"
            id="formEmpleado"
            novalidate
        >

            <input
                type="hidden"
                name="_csrf"
                value="<?php
                    echo htmlspecialchars(
                        $empleadoEditarCsrf,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
            >

            <div class="grid">


                <!-- LEGAJO -->

                <div class="campo">

                    <label for="nro_legajo">
                        Legajo *
                    </label>

                    <input
                        type="text"
                        name="nro_legajo"
                        id="nro_legajo"
                        value="<?php echo htmlspecialchars(
                            $empleado['nro_legajo'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>"
                    >

                </div>


                <!-- APELLIDO -->

                <div class="campo">

                    <label for="apellido">
                        Apellido *
                    </label>

                    <input
                        type="text"
                        name="apellido"
                        id="apellido"
                        value="<?php echo htmlspecialchars(
                            $empleado['apellido'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>"
                    >

                </div>


                <!-- NOMBRE -->

                <div class="campo">

                    <label for="nombre">
                        Nombre *
                    </label>

                    <input
                        type="text"
                        name="nombre"
                        id="nombre"
                        value="<?php echo htmlspecialchars(
                            $empleado['nombre'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>"
                    >

                </div>


                <!-- DNI -->

                <div class="campo">

                    <label for="dni">
                        DNI *
                    </label>

                    <input
                        type="text"
                        name="dni"
                        id="dni"
                        value="<?php echo htmlspecialchars(
                            $empleado['dni'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>"
                    >

                </div>


                <!-- CUIL -->

                <div class="campo">

                    <label for="cuil">
                        CUIL *
                    </label>

                    <input
                        type="text"
                        name="cuil"
                        id="cuil"
                        value="<?php echo htmlspecialchars(
                            $empleado['cuil'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>"
                    >

                </div>


                <!-- FECHA DE ALTA INICIAL -->

                <div class="campo">

                    <label for="fecha_alta">
                        Fecha de Alta Inicial
                    </label>

                    <input
                        type="date"
                        id="fecha_alta"
                        value="<?php echo htmlspecialchars(
                            $empleado['fecha_alta'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>"
                        readonly
                    >

                    <div class="ayuda-campo">
                        Corresponde al alta laboral inicial y está vinculada al
                        historial laboral. No se modifica desde esta pantalla.
                    </div>

                </div>


                <!-- FECHA INACTIVO -->

                <div class="campo">

                    <label for="fecha_inactivo_visual">
                        Fecha Inactivo
                    </label>

                    <?php

                    $fechaInactivoVisual =
                        !empty($empleado['fecha_inactivo'])
                        &&
                        $empleado['fecha_inactivo'] !== '0000-00-00'
                            ?
                            date(
                                'd/m/Y',
                                strtotime(
                                    $empleado['fecha_inactivo']
                                )
                            )
                            :
                            '-';

                    ?>

                    <input
                        type="text"
                        id="fecha_inactivo_visual"
                        value="<?php echo htmlspecialchars(
                            $fechaInactivoVisual,
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>"
                        readonly
                    >

                    <div class="ayuda-campo">
                        La fecha se registra automáticamente al inactivar al empleado.
                        No se modifica desde esta pantalla.
                    </div>

                    <!--
                    Se conserva fecha_baja como dato legacy para evitar que
                    una edición del empleado elimine accidentalmente un valor
                    histórico existente mientras el controlador todavía mantiene
                    compatibilidad con esa columna.
                    -->
                    <input
                        type="hidden"
                        name="fecha_baja"
                        value="<?php echo htmlspecialchars(
                            $empleado['fecha_baja'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>"
                    >

                </div>


                <!-- TELÉFONO -->

                <div class="campo">

                    <label for="telefono">
                        Teléfono
                    </label>

                    <input
                        type="text"
                        name="telefono"
                        id="telefono"
                        value="<?php echo htmlspecialchars(
                            $empleado['telefono'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>"
                    >

                </div>


                <!-- EMAIL -->

                <div class="campo">

                    <label for="email">
                        Email *
                    </label>

                    <input
                        type="email"
                        name="email"
                        id="email"
                        value="<?php echo htmlspecialchars(
                            $empleado['email'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>"
                    >

                </div>


                <!-- DOMICILIO -->

                <div class="campo">

                    <label for="domicilio">
                        Domicilio
                    </label>

                    <input
                        type="text"
                        name="domicilio"
                        id="domicilio"
                        value="<?php echo htmlspecialchars(
                            $empleado['domicilio'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>"
                    >

                </div>


                <!-- INSTITUCIÓN -->

                <div class="campo">

                    <label for="institucion_id">
                        Institución *
                    </label>

                    <select
                        name="institucion_id"
                        id="institucion_id"
                    >

                        <option value="">
                            Seleccione
                        </option>

                        <?php foreach ($instituciones as $x): ?>

                            <option
                                value="<?php echo (int)$x['id']; ?>"
                                <?php
                                echo (
                                    (string)($empleado['institucion_id'] ?? '')
                                    ===
                                    (string)$x['id']
                                )
                                    ? 'selected'
                                    : '';
                                ?>
                            >

                                <?php
                                echo htmlspecialchars(
                                    $x['nombre'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <!-- UNIDAD DE ORGANIZACIÓN -->

                <div class="campo">

                    <label for="oficina_id">
                        Unidad de Organización *
                    </label>

                    <select
                        name="oficina_id"
                        id="oficina_id"
                    >

                        <option value="">
                            Seleccione
                        </option>

                        <?php foreach ($oficinas as $x): ?>

                            <option
                                value="<?php echo (int)$x['id']; ?>"
                                <?php
                                echo (
                                    (string)($empleado['oficina_id'] ?? '')
                                    ===
                                    (string)$x['id']
                                )
                                    ? 'selected'
                                    : '';
                                ?>
                            >

                                <?php
                                $nombreUnidad =
                                    $x['nombre']
                                    ?? '';

                                $cuitUnidad =
                                    $x['cuit']
                                    ?? '';

                                $textoUnidad =
                                    $nombreUnidad;

                                if ($cuitUnidad !== '') {

                                    $textoUnidad .=
                                        ' - CUIT '
                                        . formatearCuitUnidadOrganizacion(
                                            $cuitUnidad
                                        );
                                }

                                echo htmlspecialchars(
                                    $textoUnidad,
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                    <div class="ayuda-campo">
                        Seleccione la Unidad de Organización correspondiente.
                        El CUIT se muestra junto al nombre.
                    </div>

                </div>


                <!-- SITUACIÓN -->

                <div class="campo">

                    <label for="situacion_id">
                        Situación *
                    </label>

                    <select
                        name="situacion_id"
                        id="situacion_id"
                    >

                        <option value="">
                            Seleccione
                        </option>

                        <?php foreach ($situaciones as $x): ?>

                            <option
                                value="<?php echo (int)$x['id']; ?>"
                                <?php
                                echo (
                                    (string)($empleado['situacion_id'] ?? '')
                                    ===
                                    (string)$x['id']
                                )
                                    ? 'selected'
                                    : '';
                                ?>
                            >

                                <?php
                                echo htmlspecialchars(
                                    $x['nombre'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <!-- ESCALAFÓN -->

                <div class="campo">

                    <label
                        for="escalafon_id"
                        id="label_escalafon"
                    >
                        Escalafón *
                    </label>

                    <select
                        name="escalafon_id"
                        id="escalafon_id"
                    >

                        <option value="">
                            Seleccione
                        </option>

                        <?php foreach ($escalafones as $x): ?>

                            <option
                                value="<?php echo (int)$x['id']; ?>"
                                <?php
                                echo (
                                    (string)($empleado['escalafon_id'] ?? '')
                                    ===
                                    (string)$x['id']
                                )
                                    ? 'selected'
                                    : '';
                                ?>
                            >

                                <?php
                                echo htmlspecialchars(
                                    $x['nombre'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                    <div
                        class="ayuda-campo"
                        id="ayuda_escalafon"
                    >
                        Obligatorio para categorías generales.
                    </div>

                </div>


                <!-- CATEGORÍA -->

                <div class="campo">

                    <label for="categoria_id">
                        Categoría *
                    </label>

                    <select
                        name="categoria_id"
                        id="categoria_id"
                    >

                        <option value="">
                            Seleccione
                        </option>

                        <?php foreach ($categorias as $x): ?>

                            <option
                                value="<?php echo (int)$x['id']; ?>"
                                data-codigo="<?php echo (int)($x['codigo'] ?? 0); ?>"
                                <?php
                                echo (
                                    (string)($empleado['categoria_id'] ?? '')
                                    ===
                                    (string)$x['id']
                                )
                                    ? 'selected'
                                    : '';
                                ?>
                            >

                                <?php
                                echo htmlspecialchars(
                                    (
                                        isset($x['codigo'])
                                        ?
                                        $x['codigo'] . ' - '
                                        :
                                        ''
                                    )
                                    .
                                    $x['nombre'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <!-- OBSERVACIONES -->

                <div
                    class="campo"
                    style="grid-column:1/-1;"
                >

                    <label for="observaciones">
                        Observaciones
                    </label>

                    <textarea
                        name="observaciones"
                        id="observaciones"
                    ><?php echo htmlspecialchars(
                        $empleado['observaciones'] ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?></textarea>

                </div>

            </div>


            <!-- =====================================
                 ACCIONES
            ====================================== -->

            <div class="acciones">

                <button
                    type="submit"
                    class="btn"
                >
                    Actualizar Empleado
                </button>

                <a
                    href="<?php
                        echo htmlspecialchars(
                            $empleadoEditarCancelar,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                    class="btn btn-sec"
                >
                    Cancelar
                </a>

            </div>

        </form>

    </div>

</div>


<script>

/*
|--------------------------------------------------------------------------
| CATEGORÍA / ESCALAFÓN
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
    | LIMPIAR ERRORES
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
            "Debe ingresar el legajo.",
            "nro_legajo"
        );

        return;
    }


    if(!/^[0-9]+$/.test(nroLegajo)){

        mostrarError(
            "El legajo debe contener solo números.",
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
    | VALIDAR FECHA DE ALTA INICIAL
    |--------------------------------------------------------------------------
    |
    | El campo es solo lectura. Esta validación comprueba únicamente que el
    | registro histórico continúe teniendo una fecha válida cargada.
    |
    */

    if(fechaAlta === ""){

        mostrarError(
            "El empleado no posee una Fecha de Alta Inicial válida. Revise su historial laboral.",
            "fecha_alta"
        );

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDAR EMAIL OBLIGATORIO
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
            "Seleccione una institución.",
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
            "Seleccione una Unidad de Organización.",
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
            "Seleccione una situación.",
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
            "Seleccione una categoría.",
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
            "Seleccione un escalafón para la categoría.",
            "escalafon_id"
        );

        return;
    }

});

</script>

</body>

</html>