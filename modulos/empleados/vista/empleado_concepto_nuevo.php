<?php

require_once __DIR__ . '/../../../core/Url.php';
require_once __DIR__ . '/../../../core/Csrf.php';


/*
|--------------------------------------------------------------------------
| NUEVA ASIGNACIÓN - FRONT CONTROLLER + ROUTER
|--------------------------------------------------------------------------
*/

$empleadoConceptoNuevoAccion =
    sigenmuniUrlRuta(
        'empleado-conceptos/nuevo'
    );


$empleadoConceptoNuevoCancelar =
    sigenmuniUrlRuta(
        'empleado-conceptos'
    );


$empleadoConceptoNuevoCsrf =
    sigenmuniCsrfToken();

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Nueva Asignación de Concepto - SIGENMUNI</title>

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
    max-width:850px;
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
select,
textarea{
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
select:focus,
textarea:focus{
    border-color:#14b8a6;
    box-shadow:0 0 0 3px rgba(20,184,166,.15);
}

textarea{
    min-height:100px;
    resize:vertical;
}


/* =========================================
   GRID
========================================= */

.grid-doble{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:16px;
}


/* =========================================
   AYUDA
========================================= */

.ayuda{
    margin-top:7px;
    font-size:12px;
    color:#64748b;
    line-height:1.45;
}


/* =========================================
   INFORMACIÓN DEL CONCEPTO
========================================= */

.concepto-info{
    display:none;
    margin-bottom:20px;
    padding:14px 16px;
    border:1px solid #bfdbfe;
    border-radius:12px;
    background:#eff6ff;
    color:#1e40af;
    font-size:13px;
    line-height:1.55;
}

.concepto-info strong{
    color:#1e3a8a;
}

.etiqueta{
    display:inline-block;
    margin:4px 6px 0 0;
    padding:4px 8px;
    border-radius:999px;
    background:#dbeafe;
    color:#1e40af;
    font-size:11px;
    font-weight:bold;
}


/* =========================================
   CAMPOS DINÁMICOS
========================================= */

.campo-dinamico{
    display:none;
}

.campo-dinamico.visible{
    display:block;
}

.campo-resaltado{
    background:#f8fafc;
    border:1px solid #e2e8f0;
    border-radius:12px;
    padding:16px;
    margin-bottom:18px;
}


/* =========================================
   MENSAJES
========================================= */

.mensaje{
    padding:14px 16px;
    border-radius:10px;
    margin-bottom:18px;
    font-size:14px;
    font-weight:bold;
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

    .grid-doble{
        grid-template-columns:1fr;
        gap:0;
    }

    input,
    select,
    textarea{
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
        Asignación de Conceptos al Personal
    </p>

</div>


<!-- =========================================
     CONTENIDO
========================================= -->

<div class="contenedor">

    <div class="panel">

        <h2>
            Nueva Asignación de Concepto
        </h2>


        <!-- ALERTA JAVASCRIPT -->

        <div
            id="alertaFormulario"
            class="mensaje mensaje-error"
            style="display:none;"
        >
        </div>


        <!-- MENSAJE DEL CONTROLADOR -->

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


        <!-- FORMULARIO -->

        <form
            action="<?php
                echo htmlspecialchars(
                    $empleadoConceptoNuevoAccion,
                    ENT_QUOTES,
                    'UTF-8'
                );
            ?>"
            method="POST"
            id="formAsignacion"
            novalidate
        >

            <input
                type="hidden"
                name="_csrf"
                value="<?php
                    echo htmlspecialchars(
                        $empleadoConceptoNuevoCsrf,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
            >


            <!-- EMPLEADO -->

            <div class="grupo">

                <label for="empleado_id">
                    Empleado *
                </label>

                <select
                    name="empleado_id"
                    id="empleado_id"
                >

                    <option value="">
                        Seleccione un empleado
                    </option>

                    <?php foreach ($empleadosDisponibles as $empleado): ?>

                        <option
                            value="<?php echo (int)$empleado['id']; ?>"
                            <?php
                            echo (
                                (string)($datosAsignacion['empleado_id'] ?? '')
                                ===
                                (string)$empleado['id']
                            )
                                ? 'selected'
                                : '';
                            ?>
                        >

                            <?php
                            echo htmlspecialchars(
                                $empleado['apellido']
                                . ', '
                                . $empleado['nombre']
                                . ' - Legajo: '
                                . $empleado['nro_legajo'],
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <!-- CONCEPTO -->

            <div class="grupo">

                <label for="concepto_id">
                    Concepto *
                </label>

                <select
                    name="concepto_id"
                    id="concepto_id"
                    onchange="actualizarCamposConcepto()"
                >

                    <option
                        value=""
                        data-codigo=""
                        data-categoria=""
                        data-forma=""
                    >
                        Seleccione un concepto
                    </option>

                    <?php foreach ($conceptosDisponibles as $concepto): ?>

                        <?php

                        $codigoConcepto =
                            (string)($concepto['codigo'] ?? '');

                        $categoriaConcepto =
                            strtoupper(
                                trim(
                                    (string)($concepto['categoria'] ?? '')
                                )
                            );

                        $formaConcepto =
                            strtoupper(
                                trim(
                                    (string)($concepto['forma_calculo'] ?? '')
                                )
                            );

                        if ($formaConcepto === 'FIJO') {
                            $formaConcepto = 'MANUAL';
                        }

                        ?>

                        <option
                            value="<?php echo (int)$concepto['id']; ?>"
                            data-codigo="<?php
                                echo htmlspecialchars(
                                    $codigoConcepto,
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                            ?>"
                            data-categoria="<?php
                                echo htmlspecialchars(
                                    $categoriaConcepto,
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                            ?>"
                            data-forma="<?php
                                echo htmlspecialchars(
                                    $formaConcepto,
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                            ?>"
                            <?php
                            echo (
                                (string)($datosAsignacion['concepto_id'] ?? '')
                                ===
                                (string)$concepto['id']
                            )
                                ? 'selected'
                                : '';
                            ?>
                        >

                            <?php
                            echo htmlspecialchars(
                                $codigoConcepto
                                . ' - '
                                . $concepto['nombre'],
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <!-- INFORMACIÓN DEL CONCEPTO -->

            <div
                id="conceptoInfo"
                class="concepto-info"
            >

                <div id="conceptoInfoTexto">
                </div>

            </div>


            <!-- =====================================
                 MONTO
            ====================================== -->

            <div
                id="grupoMonto"
                class="campo-dinamico"
            >

                <div class="campo-resaltado">

                    <div class="grupo" style="margin-bottom:0;">

                        <label
                            for="monto_manual"
                            id="labelMonto"
                        >
                            Monto *
                        </label>

                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            name="monto_manual"
                            id="monto_manual"
                            value="<?php
                                echo htmlspecialchars(
                                    $datosAsignacion['monto_manual']
                                    ?? '0.00',
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                            ?>"
                        >

                        <div
                            class="ayuda"
                            id="ayudaMonto"
                        >
                            Ingrese el importe correspondiente a este empleado.
                        </div>

                    </div>

                </div>

            </div>


            <!-- =====================================
                 PORCENTAJE
            ====================================== -->

            <div
                id="grupoPorcentaje"
                class="campo-dinamico"
            >

                <div class="campo-resaltado">

                    <div class="grupo" style="margin-bottom:0;">

                        <label for="porcentaje_manual">
                            Porcentaje *
                        </label>

                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            name="porcentaje_manual"
                            id="porcentaje_manual"
                            value="<?php
                                echo htmlspecialchars(
                                    $datosAsignacion['porcentaje_manual']
                                    ?? '0.00',
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                            ?>"
                        >

                        <div class="ayuda">
                            Ingrese el porcentaje particular que corresponde a este empleado.
                        </div>

                    </div>

                </div>

            </div>


            <!-- =====================================
                 CANTIDAD
                 SOLO ASIGNACIONES FAMILIARES
            ====================================== -->

            <div
                id="grupoCantidad"
                class="campo-dinamico"
            >

                <div class="grupo">

                    <label for="cantidad">
                        Cantidad *
                    </label>

                    <input
                        type="number"
                        step="1"
                        min="1"
                        name="cantidad"
                        id="cantidad"
                        value="<?php
                            echo htmlspecialchars(
                                $datosAsignacion['cantidad']
                                ?? '1.00',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                    >

                    <div class="ayuda">
                        Para asignaciones familiares, el monto ingresado se toma
                        como importe por unidad y se multiplica por esta cantidad.
                    </div>

                </div>

            </div>


            <!-- FECHAS -->

            <div class="grid-doble">


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
                                $datosAsignacion['fecha_desde'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                    >

                </div>


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
                                $datosAsignacion['fecha_hasta'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                    >

                    <div class="ayuda">
                        Puede dejarse vacía si la asignación no tiene vencimiento.
                    </div>

                </div>

            </div>


            <!-- OBSERVACIÓN -->

            <div class="grupo">

                <label for="observacion">
                    Observación
                </label>

                <textarea
                    name="observacion"
                    id="observacion"
                    placeholder="Ingrese una observación opcional"
                ><?php
                    echo htmlspecialchars(
                        $datosAsignacion['observacion'] ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?></textarea>

            </div>


            <!-- BOTONES -->

            <div class="acciones">

                <button
                    type="submit"
                    class="btn btn-guardar"
                >
                    Guardar Asignación
                </button>


                <a
                    href="<?php
                        echo htmlspecialchars(
                            $empleadoConceptoNuevoCancelar,
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

</script>


</body>

</html>
