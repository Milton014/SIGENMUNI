<?php

require_once __DIR__ . '/../../../core/Url.php';
require_once __DIR__ . '/../../../core/Csrf.php';


/*
|--------------------------------------------------------------------------
| NUEVA LIQUIDACIÓN - ROUTER
|--------------------------------------------------------------------------
*/

$liquidacionNuevaAccion =
    sigenmuniUrlRuta(
        'liquidacion/nueva'
    );


$liquidacionNuevaVolver =
    sigenmuniUrlRuta(
        'liquidacion'
    );


$liquidacionNuevaCsrf =
    sigenmuniCsrfToken();

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Nueva Liquidación - SIGENMUNI</title>

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
    max-width:700px;
    margin:30px auto;
}

.panel{
    background:white;
    padding:28px;
    border-radius:18px;
    box-shadow:0 8px 20px rgba(0,0,0,.08);
}


/* =========================================
   TÍTULO
========================================= */

h2{
    margin-top:0;
    margin-bottom:6px;
    color:#0f766e;
    font-size:28px;
}

.subtitulo{
    margin-top:0;
    margin-bottom:24px;
    color:#64748b;
    font-size:14px;
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
    color:#334155;
}

.campo-obligatorio{
    color:#dc2626;
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
    color:#1f2937;
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
   AYUDA
========================================= */

.ayuda{
    margin-top:6px;
    color:#64748b;
    font-size:12px;
    line-height:1.4;
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
   ESTADO INICIAL
========================================= */

.estado-inicial{
    background:#fef3c7;
    color:#92400e;
    border:1px solid #fde68a;
    border-radius:10px;
    padding:12px 14px;
    margin-bottom:20px;
    font-size:13px;
}

.estado-inicial strong{
    font-weight:bold;
}

.aviso-tipo{
    display:none;
    border-radius:10px;
    padding:12px 14px;
    margin-top:10px;
    font-size:13px;
    line-height:1.5;
}

.aviso-tipo strong{
    font-weight:bold;
}

.aviso-protocolar{
    background:#eff6ff;
    color:#1e40af;
    border:1px solid #bfdbfe;
}

.aviso-complementaria{
    background:#fff7ed;
    color:#9a3412;
    border:1px solid #fed7aa;
}

.aviso-complementaria-sac{
    background:#faf5ff;
    color:#7e22ce;
    border:1px solid #e9d5ff;
}

.aviso-aguinaldo{
    background:#ecfdf5;
    color:#166534;
    border:1px solid #bbf7d0;
}


/* =========================================
   MENSAJES
========================================= */

.mensaje{
    padding:14px 16px;
    border-radius:10px;
    margin-bottom:20px;
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
    margin-top:24px;
}

.btn{
    display:inline-block;
    padding:11px 17px;
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

.btn-volver{
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
        text-align:center;
        font-size:24px;
    }

    .subtitulo{
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
        min-height:44px;
    }

    .acciones{
        flex-direction:column;
    }

    .acciones .btn{
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
        Gestión de Liquidaciones
    </p>

</div>


<!-- =========================================
     CONTENIDO
========================================= -->

<div class="contenedor">

    <div class="panel">


        <!-- =====================================
             TÍTULO
        ====================================== -->

        <h2>
            Nueva Liquidación
        </h2>

        <p class="subtitulo">
            Cree la cabecera de una nueva liquidación de haberes.
        </p>


        <!-- =====================================
             ESTADO INICIAL
        ====================================== -->

        <div class="estado-inicial">

            La nueva liquidación será creada con estado

            <strong>
                BORRADOR
            </strong>.

        </div>


        <!-- =====================================
             ALERTA JAVASCRIPT
        ====================================== -->

        <div
            id="alertaLiquidacion"
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
                    $liquidacionNuevaAccion,
                    ENT_QUOTES,
                    'UTF-8'
                );
            ?>"
            method="POST"
            id="formLiquidacion"
            novalidate
        >

            <input
                type="hidden"
                name="_csrf"
                value="<?php
                    echo htmlspecialchars(
                        $liquidacionNuevaCsrf,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
            >


            <!-- =================================
                 TIPO DE LIQUIDACIÓN
            ================================== -->

            <div class="grupo">

                <label for="tipo_liquidacion">

                    Tipo de Liquidación

                    <span class="campo-obligatorio">
                        *
                    </span>

                </label>


                <select
                    name="tipo_liquidacion"
                    id="tipo_liquidacion"
                >

                    <option value="">
                        Seleccione un tipo
                    </option>


                    <?php foreach ($tipos as $tipoItem): ?>

                        <option
                            value="<?php
                                echo htmlspecialchars(
                                    $tipoItem,
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                            ?>"
                            <?php
                            echo (
                                ($datosLiquidacion['tipo_liquidacion'] ?? '')
                                ===
                                $tipoItem
                            )
                                ? 'selected'
                                : '';
                            ?>
                        >

                            <?php

                            switch ($tipoItem) {

                                case 'MENSUAL':

                                    echo 'Mensual';

                                    break;


                                case 'AGUINALDO':

                                    echo 'Aguinaldo';

                                    break;


                                case 'COMPLEMENTARIA':

                                    echo 'Complementaria';

                                    break;


                                case 'COMPLEMENTARIA_SAC':

                                    echo 'Complementaria de SAC';

                                    break;


                                case 'GASTOS_PROTOCOLARES':

                                    echo 'Gastos Protocolares';

                                    break;


                                default:

                                    echo htmlspecialchars(
                                        $tipoItem,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );

                                    break;
                            }

                            ?>

                        </option>

                    <?php endforeach; ?>

                </select>


                <div class="ayuda">
                    Seleccione el tipo de liquidación que desea generar.
                </div>

                <div
                    id="avisoAguinaldo"
                    class="aviso-tipo aviso-aguinaldo"
                >
                    <strong>Aguinaldo:</strong>
                    la liquidación normal de SAC solo puede crearse para los
                    períodos de <b>junio</b> o <b>diciembre</b>.
                    Si necesita liquidar SAC en otro mes, utilice
                    <b>Complementaria de SAC</b>.
                </div>


                <div
                    id="avisoComplementaria"
                    class="aviso-tipo aviso-complementaria"
                >
                    <strong>Complementaria de haberes:</strong>
                    después de crear la liquidación podrá ingresar a
                    <b>Personal y Novedades</b> para seleccionar únicamente
                    los empleados que deben participar, indicar de 1 a 30 días
                    liquidados y definir si corresponde Presentismo.
                </div>


                <div
                    id="avisoComplementariaSac"
                    class="aviso-tipo aviso-complementaria-sac"
                >
                    <strong>Complementaria de SAC:</strong>
                    después de crear la liquidación podrá ingresar a
                    <b>Personal y Días SAC</b>. El sistema mostrará por empleado
                    los días SAC devengados, ya pagados y pendientes, y evitará
                    superar el saldo disponible del semestre.
                </div>


                <div
                    id="avisoProtocolar"
                    class="aviso-tipo aviso-protocolar"
                >
                    <strong>Gastos Protocolares:</strong>
                    esta liquidación tendrá una carga específica por empleado.
                    Se podrá indicar el importe y si corresponde aplicar
                    Caja de Previsión 11% y aporte patronal 16%, o marcar
                    al empleado como Jubilado / Exento.
                </div>

            </div>


            <!-- =================================
                 PERÍODO / FECHA
            ================================== -->

            <div class="grid-doble">


                <!-- PERÍODO -->

                <div class="grupo">

                    <label for="periodo">

                        Período

                        <span class="campo-obligatorio">
                            *
                        </span>

                    </label>


                    <input
                        type="month"
                        name="periodo"
                        id="periodo"
                        value="<?php
                            echo htmlspecialchars(
                                $datosLiquidacion['periodo']
                                ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                    >


                    <div class="ayuda">
                        Mes y año correspondiente a la liquidación.
                    </div>

                </div>


                <!-- FECHA -->

                <div class="grupo">

                    <label for="fecha_liquidacion">

                        Fecha de Liquidación

                        <span class="campo-obligatorio">
                            *
                        </span>

                    </label>


                    <input
                        type="date"
                        name="fecha_liquidacion"
                        id="fecha_liquidacion"
                        value="<?php
                            echo htmlspecialchars(
                                $datosLiquidacion['fecha_liquidacion']
                                ?? date('Y-m-d'),
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                    >


                    <div
                        class="ayuda"
                        id="ayudaFechaLiquidacion"
                    >
                        Fecha en la que se genera la liquidación.
                    </div>

                </div>

            </div>


            <!-- =================================
                 DESCRIPCIÓN
            ================================== -->

            <div class="grupo">

                <label for="descripcion">
                    Descripción
                </label>


                <textarea
                    name="descripcion"
                    id="descripcion"
                    maxlength="500"
                    placeholder="Ingrese una descripción opcional"
                ><?php
                    echo htmlspecialchars(
                        $datosLiquidacion['descripcion']
                        ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?></textarea>


                <div class="ayuda">
                    Campo opcional. Máximo 500 caracteres.
                </div>

            </div>


            <!-- =================================
                 BOTONES
            ================================== -->

            <div class="acciones">

                <button
                    type="submit"
                    class="btn btn-guardar"
                >
                    Guardar Liquidación
                </button>


                <a
                    href="<?php
                        echo htmlspecialchars(
                            $liquidacionNuevaVolver,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                    class="btn btn-volver"
                >
                    Volver
                </a>

            </div>

        </form>

    </div>

</div>


<script>

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

</script>


</body>

</html>