<?php

/*
|--------------------------------------------------------------------------
| NUEVO CONCEPTO - SOLO ROUTER
|--------------------------------------------------------------------------
|
| El formulario de alta de conceptos trabaja únicamente con las rutas
| registradas del módulo.
|
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../../../core/Url.php';
require_once __DIR__ . '/../../../core/Csrf.php';


$conceptoNuevoAccion =
    sigenmuniUrlRuta(
        'conceptos/nuevo'
    );


$conceptoNuevoVolver =
    sigenmuniUrlRuta(
        'conceptos'
    );


$conceptoNuevoCsrf =
    sigenmuniCsrfToken();

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Nuevo Concepto - SIGENMUNI</title>

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
    box-shadow:0 4px 14px rgba(0,0,0,.10);
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
    max-width:950px;
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

.fila{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:18px;
    margin-bottom:18px;
}

.fila-completa{
    margin-bottom:18px;
}

.campo{
    display:flex;
    flex-direction:column;
}

.campo label{
    margin-bottom:6px;
    font-weight:bold;
    font-size:14px;
}

.campo input,
.campo select,
.campo textarea{
    width:100%;
    padding:12px;
    border:1px solid #cbd5e1;
    border-radius:10px;
    font-size:14px;
    outline:none;
    transition:.2s;
    background:white;
}

.campo input:focus,
.campo select:focus,
.campo textarea:focus{
    border-color:#14b8a6;
    box-shadow:0 0 0 3px rgba(20,184,166,.15);
}

.campo textarea{
    min-height:100px;
    resize:vertical;
}


/* =========================================
   AYUDAS
========================================= */

.ayuda{
    margin-top:6px;
    font-size:13px;
    color:#64748b;
    line-height:1.45;
}

.aviso-forma{
    margin-bottom:20px;
    padding:14px 16px;
    border-radius:12px;
    background:#eff6ff;
    border:1px solid #bfdbfe;
    color:#1e3a8a;
    font-size:13px;
    line-height:1.55;
}

.aviso-forma strong{
    display:block;
    margin-bottom:5px;
}

.origen-calculo{
    margin-top:8px;
    padding:10px 12px;
    border-radius:10px;
    background:#f8fafc;
    border:1px solid #e2e8f0;
    color:#475569;
    font-size:13px;
    line-height:1.45;
}


/* =========================================
   CHECKBOXES
========================================= */

.checks{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:12px;
    margin:22px 0;
}

.check-item{
    background:#f8fafc;
    border:1px solid #e5e7eb;
    border-radius:10px;
    padding:12px;
}

.check-item label{
    display:flex;
    align-items:center;
    gap:8px;
    cursor:pointer;
    font-size:14px;
}

.check-item input{
    width:auto;
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
   TABLET
========================================= */

@media (max-width:900px){

    .checks{
        grid-template-columns:1fr 1fr;
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

    .fila{
        grid-template-columns:1fr;
        gap:16px;
    }

    .checks{
        grid-template-columns:1fr;
    }

    .acciones{
        flex-direction:column;
    }

    .btn{
        width:100%;
        text-align:center;
    }

    .campo input,
    .campo select,
    .campo textarea{
        min-height:44px;
        font-size:16px;
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
        Alta de Concepto de Liquidación
    </p>

</div>


<!-- =========================================
     CONTENIDO
========================================= -->

<div class="contenedor">

    <div class="panel">

        <h2>
            Nuevo Concepto
        </h2>


        <!-- =====================================
             ALERTA JAVASCRIPT
        ====================================== -->

        <div
            id="alertaConcepto"
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


        <div class="aviso-forma">

            
                
            </strong>

            Los importes por categoría se administran únicamente desde
            <strong style="display:inline;">Valores del Concepto</strong>
            para Sueldo Básico (101), Dedicación Funcional (102) y
            Suplemento Especial (104).
            Los importes y porcentajes particulares de cada empleado se cargan
            desde <strong style="display:inline;">Conceptos por Empleado</strong>.

        </div>


        <!-- =====================================
             FORMULARIO
        ====================================== -->

        <form
            action="<?php
                echo htmlspecialchars(
                    $conceptoNuevoAccion,
                    ENT_QUOTES,
                    'UTF-8'
                );
            ?>"
            method="POST"
            id="formConcepto"
            novalidate
        >

            <input
                type="hidden"
                name="_csrf"
                value="<?php
                    echo htmlspecialchars(
                        $conceptoNuevoCsrf,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
            >


            <!-- =================================
                 CAMPOS INTERNOS DE COMPATIBILIDAD
            ================================== -->

            <input type="hidden" name="porcentaje" value="0">
            <input type="hidden" name="monto_fijo" value="0">
            <input type="hidden" name="orden_calculo" value="0">


            <!-- =================================
                 CÓDIGO / NOMBRE
            ================================== -->

            <div class="fila">

                <div class="campo">

                    <label for="codigo">
                        Código *
                    </label>

                    <input
                        type="number"
                        name="codigo"
                        id="codigo"
                        min="1"
                        value="<?php
                            echo htmlspecialchars(
                                $datos['codigo'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                    >

                </div>


                <div class="campo">

                    <label for="nombre">
                        Nombre *
                    </label>

                    <input
                        type="text"
                        name="nombre"
                        id="nombre"
                        maxlength="150"
                        value="<?php
                            echo htmlspecialchars(
                                $datos['nombre'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                    >

                </div>

            </div>


            <!-- =================================
                 TIPO / FORMA DE CÁLCULO
            ================================== -->

            <div class="fila">

                <div class="campo">

                    <label for="categoria">
                        Tipo de Concepto *
                    </label>

                    <select
                        name="categoria"
                        id="categoria"
                        onchange="actualizarBaseCalculo()"
                    >

                        <option value="">
                            -- Seleccionar --
                        </option>

                        <?php foreach ($categorias as $cat): ?>

                            <option
                                value="<?php
                                    echo htmlspecialchars(
                                        $cat,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                ?>"
                                <?php
                                echo (
                                    ($datos['categoria'] ?? '')
                                    ===
                                    $cat
                                )
                                    ? 'selected'
                                    : '';
                                ?>
                            >

                                <?php
                                echo htmlspecialchars(
                                    $cat,
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <div class="campo">

                    <label for="forma_calculo">
                        Forma de Cálculo *
                    </label>

                    <?php

                    $formaSeleccionada =
                        $datos['forma_calculo']
                        ?? 'MANUAL';

                    if ($formaSeleccionada === 'FIJO') {
                        $formaSeleccionada = 'MANUAL';
                    }

                    ?>

                    <select
                        name="forma_calculo"
                        id="forma_calculo"
                        onchange="actualizarFormaCalculo(); actualizarBaseCalculo();"
                    >

                        <?php foreach ($formasCalculo as $forma): ?>

                            <?php

                            /* FIJO queda fuera de la nueva interfaz. */
                            if ($forma === 'FIJO') {
                                continue;
                            }

                            ?>

                            <option
                                value="<?php
                                    echo htmlspecialchars(
                                        $forma,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                ?>"
                                <?php
                                echo (
                                    $formaSeleccionada === $forma
                                )
                                    ? 'selected'
                                    : '';
                                ?>
                            >

                                <?php

                                switch ($forma) {

                                    case 'TABLA_CATEGORIA':
                                        echo 'VALOR POR CATEGORÍA';
                                        break;

                                    case 'PORCENTAJE':
                                        echo 'PORCENTAJE';
                                        break;

                                    case 'MANUAL':
                                        echo 'MANUAL';
                                        break;

                                    case 'FORMULA':
                                        echo 'AUTOMÁTICO';
                                        break;

                                    default:
                                        echo htmlspecialchars(
                                            $forma,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        );
                                        break;
                                }

                                ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                    <div
                        id="ayudaForma"
                        class="origen-calculo"
                    >
                    </div>

                </div>

            </div>


            <!-- =================================
                 BASE DEL PORCENTAJE
                 Para cualquier concepto PORCENTAJE
            ================================== -->

            <div
                class="fila-completa"
                id="contenedorBaseCalculo"
                style="display:none;"
            >

                <div class="campo">

                    <label for="base_calculo">
                        Aplicar porcentaje sobre *
                    </label>

                    <select
                        name="base_calculo"
                        id="base_calculo"
                    >

                        <option value="">
                            -- Seleccionar base --
                        </option>

                        <?php foreach (($basesCalculoPorcentaje ?? []) as $base): ?>

                            <?php

                            $etiquetaBase = $base;

                            if ($base === 'BASICO') {

                                $etiquetaBase =
                                    'Sueldo Básico';

                            } elseif (
                                $base ===
                                'BASICO_MAS_DEDICACION'
                            ) {

                                $etiquetaBase =
                                    'Sueldo Básico + Dedicación Funcional';

                            } elseif (
                                $base ===
                                'TOTAL_REMUNERATIVO'
                            ) {

                                $etiquetaBase =
                                    'Total Remunerativo';

                            } elseif (
                                $base ===
                                'TOTAL_REMUNERATIVO_MENOS_DESCUENTOS_OBLIGATORIOS'
                            ) {

                                $etiquetaBase =
                                    'Total Remunerativo menos Descuentos Obligatorios';
                            }

                            ?>

                            <option
                                value="<?php
                                    echo htmlspecialchars(
                                        $base,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                ?>"
                                <?php
                                    echo (
                                        ($datos['base_calculo'] ?? '')
                                        ===
                                        $base
                                    )
                                        ? 'selected'
                                        : '';
                                ?>
                            >
                                <?php
                                    echo htmlspecialchars(
                                        $etiquetaBase,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                    <div
                        class="ayuda"
                        id="ayudaBaseCalculo"
                    >
                        Esta opción define la base sobre la que se aplicará
                        el porcentaje cargado luego en Conceptos por Empleado.
                        Por ejemplo: 103 usa Básico + Dedicación; 105 y 110 usan Básico.
                    </div>

                </div>

            </div>


            <!-- =================================
                 FECHAS
            ================================== -->

            <div class="fila">

                <div class="campo">

                    <label for="fecha_desde">
                        Fecha Desde
                    </label>

                    <input
                        type="date"
                        name="fecha_desde"
                        id="fecha_desde"
                        value="<?php
                            echo htmlspecialchars(
                                $datos['fecha_desde'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                    >

                </div>


                <div class="campo">

                    <label for="fecha_hasta">
                        Fecha Hasta
                    </label>

                    <input
                        type="date"
                        name="fecha_hasta"
                        id="fecha_hasta"
                        value="<?php
                            echo htmlspecialchars(
                                $datos['fecha_hasta'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                    >

                    <div class="ayuda">
                        Puede dejarse vacía si el concepto no tiene vencimiento.
                    </div>

                </div>

            </div>


            <!-- =================================
                 DESCRIPCIÓN
            ================================== -->

            <div class="fila-completa">

                <div class="campo">

                    <label for="descripcion">
                        Descripción
                    </label>

                    <textarea
                        name="descripcion"
                        id="descripcion"
                    ><?php
                        echo htmlspecialchars(
                            $datos['descripcion'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?></textarea>

                </div>

            </div>


            <!-- =================================
                 CHECKBOXES
            ================================== -->

            <div class="checks">


                <!-- APLICA SAC -->

                <div class="check-item">

                    <label>

                        <input
                            type="checkbox"
                            name="aplica_sac"
                            id="aplica_sac"
                            value="1"
                            <?php
                            echo (
                                (int)($datos['aplica_sac'] ?? 0)
                                === 1
                            )
                                ? 'checked'
                                : '';
                            ?>
                        >

                        Aplica SAC

                    </label>

                </div>


                <!-- VISIBLE RECIBO -->

                <div class="check-item">

                    <label>

                        <input
                            type="checkbox"
                            name="visible_recibo"
                            id="visible_recibo"
                            value="1"
                            <?php
                            echo (
                                (int)($datos['visible_recibo'] ?? 1)
                                === 1
                            )
                                ? 'checked'
                                : '';
                            ?>
                        >

                        Visible en Recibo

                    </label>

                </div>


                <!-- ACTIVO -->

                <div class="check-item">

                    <label>

                        <input
                            type="checkbox"
                            name="activo"
                            id="activo"
                            value="1"
                            <?php
                            echo (
                                (int)($datos['activo'] ?? 1)
                                === 1
                            )
                                ? 'checked'
                                : '';
                            ?>
                        >

                        Activo

                    </label>

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
                    Guardar Concepto
                </button>

                <a
                    href="<?php
                        echo htmlspecialchars(
                            $conceptoNuevoVolver,
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

</script>

</body>

</html>
