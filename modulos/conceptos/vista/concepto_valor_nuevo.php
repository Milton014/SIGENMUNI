<?php

require_once __DIR__ . '/../../../core/Url.php';
require_once __DIR__ . '/../../../core/Csrf.php';


$origen =
    trim(
        $_GET['origen']
        ?? ''
    );


$modoIntegrado =
    (
        $origen === 'concepto'
        &&
        !empty(
            $datosValor['concepto_id']
        )
    );


$conceptoFijo =
    null;


if ($modoIntegrado) {

    foreach (
        $conceptosDisponibles
        as $conceptoDisponible
    ) {

        if (
            (int)$conceptoDisponible['id']
            ===
            (int)$datosValor['concepto_id']
        ) {

            $conceptoFijo =
                $conceptoDisponible;

            break;
        }
    }


    if ($conceptoFijo === null) {

        $modoIntegrado =
            false;
    }
}


/*
|--------------------------------------------------------------------------
| URLS Y CSRF - SOLO ROUTER
|--------------------------------------------------------------------------
*/

$retornoValores =
    (
        isset($retornoValores)
        &&
        in_array(
            $retornoValores,
            ['todos', 'filtrado'],
            true
        )
    )
        ? $retornoValores
        : (
            !empty($conceptoIdContextoValores)
                ? 'filtrado'
                : 'todos'
        );


$valorNuevoParametros = [];

if (!$modoIntegrado) {
    $valorNuevoParametros['retorno'] =
        $retornoValores;
}

if (!empty($datosValor['concepto_id'])) {
    $valorNuevoParametros['concepto_id'] =
        (int)$datosValor['concepto_id'];
}

if ($modoIntegrado) {
    $valorNuevoParametros['origen'] =
        'concepto';
}


$valorNuevoAccion =
    sigenmuniUrlRuta(
        'conceptos/valores/nuevo',
        $valorNuevoParametros
    );


if ($modoIntegrado) {

    $valorNuevoCancelar =
        sigenmuniUrlRuta(
            'conceptos/editar',
            [
                'id' => (int)$datosValor['concepto_id']
            ]
        );

} else {

    $parametrosListado = [];

    if (
        $retornoValores === 'filtrado'
        &&
        !empty($conceptoIdContextoValores)
    ) {
        $parametrosListado['concepto_id'] =
            (int)$conceptoIdContextoValores;
    }

    $valorNuevoCancelar =
        sigenmuniUrlRuta(
            'conceptos/valores',
            $parametrosListado
        );
}


$valorNuevoCsrf =
    sigenmuniCsrfToken();

?>
<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Nuevo Valor de Concepto - SIGENMUNI</title>

<style>

/* =========================================
   GENERAL
========================================= */

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
    background:linear-gradient(
        135deg,
        #0f766e,
        #14b8a6
    );

    color:white;

    padding:22px 30px;

    box-shadow:
        0 4px 14px rgba(0,0,0,.10);
}

.header h1{
    margin:0;
    font-size:30px;
}

.header p{
    margin-top:6px;
    margin-bottom:0;
    font-size:14px;
}


/* =========================================
   CONTENEDOR
========================================= */

.contenedor{
    width:95%;
    max-width:800px;
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
   FORMULARIO
========================================= */

.grupo{
    margin-bottom:18px;
}

label{
    display:block;
    margin-bottom:6px;
    font-weight:bold;
    font-size:14px;
}

input,
select{
    width:100%;
    padding:12px;
    border:1px solid #cbd5e1;
    border-radius:10px;
    font-size:14px;
    outline:none;
    transition:.2s;
    background:white;
}

input:focus,
select:focus{
    border-color:#14b8a6;
    box-shadow:0 0 0 3px rgba(20,184,166,.15);
}


/* =========================================
   DESHABILITADOS
========================================= */

.deshabilitado{
    background:#e9ecef !important;
    color:#6b7280;
}


/* =========================================
   AYUDA
========================================= */

.ayuda{
    margin-top:8px;
    padding:10px 12px;
    background:#f8fafc;
    border:1px solid #e5e7eb;
    border-radius:8px;
    font-size:13px;
    color:#64748b;
    line-height:1.4;
}


/* =========================================
   MENSAJES
========================================= */

.mensaje{
    padding:14px 16px;
    border-radius:10px;
    margin-bottom:18px;
    font-weight:bold;
    font-size:14px;
}

.mensaje-error{
    background:#fee2e2;
    color:#991b1b;
    border:1px solid #fecaca;
}

.mensaje-ok{
    background:#dcfce7;
    color:#166534;
    border:1px solid #86efac;
}

.input-error{
    border-color:#dc2626 !important;
    background:#fff1f2 !important;
    box-shadow:0 0 0 3px rgba(220,38,38,.12) !important;
}


/* =========================================
   BOTONES
========================================= */

.acciones{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
    margin-top:22px;
}

.btn{
    display:inline-block;
    padding:11px 16px;
    border:none;
    border-radius:10px;
    text-decoration:none;
    color:white;
    cursor:pointer;
    font-size:14px;
    font-weight:bold;
    transition:.2s;
    text-align:center;
}

.btn:hover{
    opacity:.92;
    transform:translateY(-1px);
}

.btn-guardar{
    background:#0f766e;
}

.btn-cancelar{
    background:#1f2937;
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

    input,
    select{
        min-height:44px;
        font-size:16px;
    }

    .acciones{
        flex-direction:column;
    }

    .btn{
        width:100%;
    }
}


/* =========================================
   CELULAR PEQUEÑO
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
}

</style>

</head>

<body>


<!-- =========================================
     HEADER
========================================= -->

<div class="header">

    <h1>
        SIGENMUNI
    </h1>

    <p>
        Alta de Valor de Concepto
    </p>

</div>


<!-- =========================================
     CONTENIDO
========================================= -->

<div class="contenedor">

    <div class="panel">

        <h2>
            Nuevo Valor de Concepto
        </h2>


        <!-- =====================================
             ALERTA JAVASCRIPT
        ====================================== -->

        <div
            id="alertaValor"
            class="mensaje mensaje-error"
            style="display:none;"
        >
        </div>


        <!-- =====================================
             MENSAJE DEL CONTROLADOR
        ====================================== -->

        <?php if (!empty($mensaje)): ?>

            <div
                class="mensaje <?php
                    echo $tipo_mensaje === 'ok'
                        ? 'mensaje-ok'
                        : 'mensaje-error';
                ?>"
            >

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
            action="<?php
                echo htmlspecialchars(
                    $valorNuevoAccion,
                    ENT_QUOTES,
                    'UTF-8'
                );
            ?>"
            method="POST"
            id="formValor"
            novalidate
        >

            <input
                type="hidden"
                name="_csrf"
                value="<?php
                    echo htmlspecialchars(
                        $valorNuevoCsrf,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
            >

            <?php if (!$modoIntegrado): ?>

                <input
                    type="hidden"
                    name="retorno"
                    value="<?php
                        echo htmlspecialchars(
                            $retornoValores,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                >

            <?php endif; ?>


            <!-- =================================
                 CONCEPTO
            ================================== -->

            <div class="grupo">

                <label for="concepto_id">
                    Concepto *
                </label>


                <?php if ($modoIntegrado): ?>

                    <input
                        type="hidden"
                        name="concepto_id"
                        id="concepto_id"
                        value="<?php
                            echo (int)$datosValor['concepto_id'];
                        ?>"
                    >


                    <input
                        type="text"
                        class="deshabilitado"
                        value="<?php
                            echo htmlspecialchars(
                                $conceptoFijo['codigo']
                                . ' - '
                                . $conceptoFijo['nombre'],
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                        readonly
                    >


                    <div class="ayuda">

                        El concepto está fijado porque el valor
                        se está cargando desde la edición del concepto.

                    </div>

                <?php else: ?>

                    <select
                        name="concepto_id"
                        id="concepto_id"
                        onchange="actualizarFormulario()"
                    >

                        <option value="">
                            Seleccione
                        </option>

                        <?php foreach ($conceptosDisponibles as $c): ?>

                            <option
                                value="<?php
                                    echo (int)$c['id'];
                                ?>"
                                <?php
                                echo (
                                    (string)(
                                        $datosValor['concepto_id']
                                        ?? ''
                                    )
                                    ===
                                    (string)$c['id']
                                )
                                    ?
                                    'selected'
                                    :
                                    '';
                                ?>
                            >

                                <?php
                                echo htmlspecialchars(
                                    $c['codigo']
                                    . ' - '
                                    . $c['nombre'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                <?php endif; ?>

            </div>


            <!-- =================================
                 CATEGORÍA
            ================================== -->

            <div class="grupo">

                <label for="categoria_id">
                    Categoría
                </label>

                <select
                    name="categoria_id"
                    id="categoria_id"
                >

                    <option value="">
                        Seleccione
                    </option>

                    <?php foreach ($categoriasValores as $cat): ?>

                        <option
                            value="<?php echo (int)$cat['id']; ?>"
                            <?php
                            echo (
                                (string)($datosValor['categoria_id'] ?? '')
                                ===
                                (string)$cat['id']
                            )
                                ? 'selected'
                                : '';
                            ?>
                        >

                            <?php
                            echo htmlspecialchars(
                                $cat['nombre'],
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <!-- =================================
                 ESCALAFÓN
            ================================== -->

            <div class="grupo">

                <label for="escalafon_id">
                    Escalafón
                </label>

                <select
                    name="escalafon_id"
                    id="escalafon_id"
                >

                    <option value="">
                        Seleccione
                    </option>

                    <?php foreach ($escalafonesValores as $esc): ?>

                        <option
                            value="<?php echo (int)$esc['id']; ?>"
                            <?php
                            echo (
                                (string)($datosValor['escalafon_id'] ?? '')
                                ===
                                (string)$esc['id']
                            )
                                ? 'selected'
                                : '';
                            ?>
                        >

                            <?php
                            echo htmlspecialchars(
                                $esc['nombre'],
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <!-- =================================
                 MONTO
            ================================== -->

            <div class="grupo">

                <label for="monto">
                    Monto
                </label>

                <input
                    type="number"
                    step="0.01"
                    min="0"
                    name="monto"
                    id="monto"
                    value="<?php
                        echo htmlspecialchars(
                            $datosValor['monto'] ?? '0.00',
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                >

            </div>


            <!-- =================================
                 PORCENTAJE
            ================================== -->

            <div class="grupo">

                <label for="porcentaje">
                    Porcentaje
                </label>

                <input
                    type="number"
                    step="0.01"
                    min="0"
                    name="porcentaje"
                    id="porcentaje"
                    value="<?php
                        echo htmlspecialchars(
                            $datosValor['porcentaje'] ?? '0.00',
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                >

            </div>


            <!-- =================================
                 AYUDA
            ================================== -->

            <div
                id="mensaje_ayuda"
                class="ayuda"
            >
                Seleccione un concepto para continuar.
            </div>


            <!-- =================================
                 FECHA DESDE
            ================================== -->

            <div class="grupo">

                <label for="fecha_desde">
                    Fecha Desde *
                </label>

                <input
                    type="date"
                    name="fecha_desde"
                    id="fecha_desde"
                    value="<?php
                        echo htmlspecialchars(
                            $datosValor['fecha_desde'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                >

            </div>


            <!-- =================================
                 FECHA HASTA
            ================================== -->

            <div class="grupo">

                <label for="fecha_hasta">
                    Fecha Hasta
                </label>

                <input
                    type="date"
                    name="fecha_hasta"
                    id="fecha_hasta"
                    value="<?php
                        echo htmlspecialchars(
                            $datosValor['fecha_hasta'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                >

            </div>


            <!-- =================================
                 BOTONES
            ================================== -->

            <div class="acciones">

                <button
                    type="submit"
                    class="btn btn-guardar"
                    id="btnGuardar"
                >
                    Guardar Valor
                </button>


                <a
                    href="<?php
                        echo htmlspecialchars(
                            $valorNuevoCancelar,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                    class="btn btn-cancelar"
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
| FORMAS DE CÁLCULO POR CONCEPTO
|--------------------------------------------------------------------------
*/

const formasConcepto = <?php
    echo json_encode(
        $formasConcepto,
        JSON_UNESCAPED_UNICODE |
        JSON_UNESCAPED_SLASHES
    );
?>;


/*
|--------------------------------------------------------------------------
| BLOQUEAR SELECT
|--------------------------------------------------------------------------
*/

function bloquearSelect(
    select,
    limpiar = true
){

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
            .getElementById("concepto_id")
            .value;


    const forma =
        formasConcepto[conceptoId] || "";


    const categoria =
        document
            .getElementById("categoria_id");


    const escalafon =
        document
            .getElementById("escalafon_id");


    const monto =
        document
            .getElementById("monto");


    const porcentaje =
        document
            .getElementById("porcentaje");


    const ayuda =
        document
            .getElementById("mensaje_ayuda");


    const botonGuardar =
        document
            .getElementById("btnGuardar");


    /*
    |--------------------------------------------------------------------------
    | ESTADO BASE
    |--------------------------------------------------------------------------
    */

    habilitarSelect(categoria);

    habilitarSelect(escalafon);


    bloquearInput(
        monto,
        "0.00"
    );

    bloquearInput(
        porcentaje,
        "0.00"
    );


    botonGuardar.disabled = false;

    botonGuardar.style.opacity = "1";

    botonGuardar.style.cursor = "pointer";


    /*
    |--------------------------------------------------------------------------
    | TABLA POR CATEGORÍA
    |--------------------------------------------------------------------------
    */

    if(forma === "TABLA_CATEGORIA"){

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

    else if(forma === "PORCENTAJE"){

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

    else if(forma === "FIJO"){

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


        /*
        |--------------------------------------------------------------------------
        | NO PERMITIR GUARDAR
        |--------------------------------------------------------------------------
        */

        botonGuardar.disabled = true;

        botonGuardar.style.opacity = ".55";

        botonGuardar.style.cursor = "not-allowed";


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
    .getElementById("formValor")
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

</script>


</body>

</html>