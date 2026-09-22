<?php

require_once __DIR__ . '/../../../core/Url.php';
require_once __DIR__ . '/../../../core/Csrf.php';


/*
|--------------------------------------------------------------------------
| GASTOS PROTOCOLARES - ROUTER
|--------------------------------------------------------------------------
*/

$liquidacionProtocolarId =
    (int)(
        $liquidacion['id']
        ?? 0
    );


$liquidacionProtocolarAccion =
    sigenmuniUrlRuta(
        'liquidacion/protocolar',
        [
            'id' =>
                $liquidacionProtocolarId
        ]
    );


$liquidacionProtocolarVolver =
    sigenmuniUrlRuta(
        'liquidacion'
    );


$liquidacionProtocolarCsrf =
    sigenmuniCsrfToken();

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Gastos Protocolares - SIGENMUNI</title>

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
    width:96%;
    max-width:1400px;
    margin:30px auto;
}

.panel{
    background:white;
    padding:24px;
    border-radius:18px;

    box-shadow:
        0 8px 20px rgba(0,0,0,.08);
}


/* =========================================
   ENCABEZADO
========================================= */

.topbar{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    gap:16px;
    flex-wrap:wrap;
    margin-bottom:22px;
}

.titulo h2{
    margin:0;
    color:#0f766e;
    font-size:28px;
}

.titulo p{
    margin:6px 0 0;
    color:#64748b;
    font-size:14px;
    line-height:1.5;
}

.acciones-superiores{
    display:flex;
    gap:8px;
    flex-wrap:wrap;
}


/* =========================================
   BOTONES
========================================= */

.btn{
    display:inline-block;
    padding:10px 14px;
    border:none;
    border-radius:9px;
    text-decoration:none;
    color:white;
    cursor:pointer;
    font-size:13px;
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
   MENSAJES
========================================= */

.mensaje{
    padding:14px 16px;
    border-radius:10px;
    margin-bottom:20px;
    font-size:14px;
    font-weight:bold;
}

.mensaje-ok{
    background:#dcfce7;
    color:#166534;
    border:1px solid #86efac;
}

.mensaje-error{
    background:#fee2e2;
    color:#991b1b;
    border:1px solid #fecaca;
}


/* =========================================
   DATOS LIQUIDACIÓN
========================================= */

.resumen{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:12px;
    margin-bottom:20px;
}

.resumen-item{
    background:#f8fafc;
    border:1px solid #e2e8f0;
    border-radius:12px;
    padding:14px;
}

.resumen-item .etiqueta{
    display:block;
    color:#64748b;
    font-size:12px;
    margin-bottom:4px;
}

.resumen-item .valor{
    font-weight:bold;
    color:#1f2937;
    font-size:14px;
}


/* =========================================
   INFORMACIÓN
========================================= */

.aviso{
    background:#eff6ff;
    border:1px solid #bfdbfe;
    color:#1e40af;
    border-radius:12px;
    padding:14px 16px;
    margin-bottom:20px;
    font-size:13px;
    line-height:1.6;
}

.aviso strong{
    font-weight:bold;
}


/* =========================================
   TABLA
========================================= */

.tabla-contenedor{
    width:100%;
    overflow-x:visible;
}

table{
    width:100%;
    border-collapse:collapse;
    table-layout:fixed;
    min-width:0;
}

th,
td{
    padding:10px 8px;
    border-bottom:1px solid #e5e7eb;
    font-size:13px;
    vertical-align:middle;
}

th{
    background:#0f766e;
    color:white;
    text-align:center;
    white-space:normal;
    line-height:1.2;
}

td{
    text-align:center;
}

td.empleado{
    text-align:left;
}

td.empleado strong{
    display:block;
    color:#1f2937;
}

td.empleado span{
    display:block;
    color:#64748b;
    font-size:12px;
    margin-top:3px;
}

/* =========================================
   DISTRIBUCIÓN DE COLUMNAS
========================================= */

th:nth-child(1),
td:nth-child(1){
    width:8%;
}

th:nth-child(2),
td:nth-child(2){
    width:10%;
}

th:nth-child(3),
td:nth-child(3){
    width:27%;
}

th:nth-child(4),
td:nth-child(4){
    width:25%;
}

th:nth-child(5),
td:nth-child(5){
    width:30%;
}

tbody tr:hover{
    background:#f8fafc;
}


/* =========================================
   CAMPOS
========================================= */

.chk-incluir{
    width:18px;
    height:18px;
    cursor:pointer;
}

.input-importe,
.select-prevision{
    width:100%;
    min-width:0;
    padding:9px 10px;
    border:1px solid #cbd5e1;
    border-radius:8px;
    font-size:13px;
    outline:none;
    background:white;
}

.input-importe:focus,
.select-prevision:focus{
    border-color:#14b8a6;
    box-shadow:0 0 0 3px rgba(20,184,166,.15);
}

.input-importe:disabled,
.select-prevision:disabled{
    background:#e9ecef;
    color:#64748b;
    cursor:not-allowed;
}

.input-error{
    border-color:#dc2626 !important;
    background:#fff1f2 !important;
    box-shadow:0 0 0 3px rgba(220,38,38,.12) !important;
}


/* =========================================
   ESTADO PREVISIONAL
========================================= */

.estado-prevision{
    display:inline-block;
    padding:5px 8px;
    border-radius:999px;
    font-size:11px;
    font-weight:bold;
}

.con-aportes{
    background:#dcfce7;
    color:#166534;
}

.exento{
    background:#fef3c7;
    color:#92400e;
}


/* =========================================
   PIE
========================================= */

.pie{
    margin-top:22px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:12px;
    flex-wrap:wrap;
}

.contador{
    color:#475569;
    font-size:13px;
}

.acciones-pie{
    display:flex;
    gap:8px;
    flex-wrap:wrap;
}


/* =========================================
   SIN EMPLEADOS
========================================= */

.sin-resultados{
    text-align:center;
    padding:30px;
    color:#64748b;
    font-weight:bold;
}


/* =========================================
   TABLET
========================================= */

@media (max-width:1000px){

    .resumen{
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

    .contenedor{
        width:94%;
        margin:20px auto;
    }

    .panel{
        padding:18px;
    }

    .topbar{
        flex-direction:column;
        align-items:stretch;
    }

    .titulo h2{
        text-align:center;
        font-size:24px;
    }

    .titulo p{
        text-align:center;
    }

    .acciones-superiores{
        flex-direction:column;
    }

    .acciones-superiores .btn{
        width:100%;
    }

    .resumen{
        grid-template-columns:1fr;
    }

    .pie{
        flex-direction:column;
        align-items:stretch;
    }

    .acciones-pie{
        flex-direction:column;
    }

    .acciones-pie .btn{
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

    .titulo h2{
        font-size:22px;
    }
}

</style>

</head>

<body>


<div class="header">

    <h1>
        SIGENMUNI
    </h1>

    <p>
        Carga de Gastos Protocolares
    </p>

</div>


<div class="contenedor">

    <div class="panel">


        <!-- =====================================
             ENCABEZADO
        ====================================== -->

        <div class="topbar">

            <div class="titulo">

                <h2>
                    Gastos Protocolares
                </h2>

                <p>
                    Seleccione los empleados, ingrese el importe
                    y defina su condición previsional.
                </p>

            </div>


            <div class="acciones-superiores">

                <a
                    href="<?php
                        echo htmlspecialchars(
                            $liquidacionProtocolarVolver,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                    class="btn btn-volver"
                >
                    Volver a Liquidaciones
                </a>

            </div>

        </div>


        <!-- =====================================
             MENSAJES
        ====================================== -->

        <?php if (!empty($mensaje)): ?>

            <div
                class="mensaje <?php
                    echo ($tipo_mensaje ?? '') === 'ok'
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


        <?php if (!empty($liquidacion)): ?>


            <!-- =====================================
                 RESUMEN DE LA LIQUIDACIÓN
            ====================================== -->

            <div class="resumen">

                <div class="resumen-item">

                    <span class="etiqueta">
                        Tipo
                    </span>

                    <span class="valor">
                        Gastos Protocolares
                    </span>

                </div>


                <div class="resumen-item">

                    <span class="etiqueta">
                        Período
                    </span>

                    <span class="valor">
                        <?php
                        echo htmlspecialchars(
                            $liquidacion['periodo'] ?? '-',
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>
                    </span>

                </div>


                <div class="resumen-item">

                    <span class="etiqueta">
                        Fecha
                    </span>

                    <span class="valor">

                        <?php
                        echo !empty(
                            $liquidacion['fecha_liquidacion']
                        )
                            ? date(
                                'd/m/Y',
                                strtotime(
                                    $liquidacion[
                                        'fecha_liquidacion'
                                    ]
                                )
                            )
                            : '-';
                        ?>

                    </span>

                </div>


                <div class="resumen-item">

                    <span class="etiqueta">
                        Estado
                    </span>

                    <span class="valor">
                        <?php
                        echo htmlspecialchars(
                            $liquidacion['estado'] ?? '-',
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>
                    </span>

                </div>

            </div>


            <!-- =====================================
                 AVISO
            ====================================== -->

            <div class="aviso">

                <strong>
                    Regla previsional:
                </strong>

                <br>

                <strong>Con aportes</strong>:
                se aplicará automáticamente
                Caja de Previsión Social 11%
                y Aporte Patronal 16%.

                <br>

                <strong>Jubilado / Exento</strong>:
                no se aplicará ninguno de los dos conceptos.

            </div>


            <!-- =====================================
                 FORMULARIO
            ====================================== -->

            <form
                action="<?php
                    echo htmlspecialchars(
                        $liquidacionProtocolarAccion,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                method="POST"
                id="formProtocolar"
                novalidate
            >

                <input
                    type="hidden"
                    name="_csrf"
                    value="<?php
                        echo htmlspecialchars(
                            $liquidacionProtocolarCsrf,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                >


                <?php if (!empty($empleadosProtocolar)): ?>


                    <div class="tabla-contenedor">

                        <table>

                            <thead>

                                <tr>

                                    <th>
                                        Incluir
                                    </th>

                                    <th>
                                        Legajo
                                    </th>

                                    <th>
                                        Empleado
                                    </th>

                                    <th>
                                        Importe
                                    </th>

                                    <th>
                                        Condición Previsional
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                <?php foreach ($empleadosProtocolar as $empleado): ?>

                                    <?php

                                    $empleadoId =
                                        (int)(
                                            $empleado['empleado_id']
                                            ?? 0
                                        );

                                    $incluido =
                                        (int)(
                                            $empleado['incluido']
                                            ?? 0
                                        )
                                        ===
                                        1;

                                    $aplicaPrevision =
                                        (int)(
                                            $empleado['aplica_prevision']
                                            ?? 1
                                        )
                                        ===
                                        1;

                                    $importe =
                                        (float)(
                                            $empleado['importe']
                                            ?? 0
                                        );

                                    ?>


                                    <tr
                                        data-empleado-id="<?php
                                            echo $empleadoId;
                                        ?>"
                                    >


                                        <!-- INCLUIR -->

                                        <td>

                                            <input
                                                type="checkbox"
                                                class="chk-incluir"
                                                name="empleados[<?php
                                                    echo $empleadoId;
                                                ?>][incluir]"
                                                value="1"
                                                <?php
                                                echo $incluido
                                                    ? 'checked'
                                                    : '';
                                                ?>
                                            >

                                        </td>


                                        <!-- LEGAJO -->

                                        <td>

                                            <?php
                                            echo htmlspecialchars(
                                                $empleado['nro_legajo']
                                                ?? '-',
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                            ?>

                                        </td>


                                        <!-- EMPLEADO -->

                                        <td class="empleado">

                                            <strong>

                                                <?php
                                                echo htmlspecialchars(
                                                    trim(
                                                        (
                                                            $empleado['apellido']
                                                            ?? ''
                                                        )
                                                        . ', '
                                                        . (
                                                            $empleado['nombre']
                                                            ?? ''
                                                        )
                                                    ),
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                );
                                                ?>

                                            </strong>

                                        </td>


                                        <!-- IMPORTE -->

                                        <td>

                                            <input
                                                type="number"
                                                class="input-importe"
                                                name="empleados[<?php
                                                    echo $empleadoId;
                                                ?>][importe]"
                                                step="0.01"
                                                min="0"
                                                value="<?php
                                                    echo $incluido
                                                        ? htmlspecialchars(
                                                            number_format(
                                                                $importe,
                                                                2,
                                                                '.',
                                                                ''
                                                            ),
                                                            ENT_QUOTES,
                                                            'UTF-8'
                                                        )
                                                        : '';
                                                ?>"
                                                <?php
                                                echo !$incluido
                                                    ? 'disabled'
                                                    : '';
                                                ?>
                                            >

                                        </td>


                                        <!-- PREVISIÓN -->

                                        <td>

                                            <select
                                                class="select-prevision"
                                                name="empleados[<?php
                                                    echo $empleadoId;
                                                ?>][aplica_prevision]"
                                                <?php
                                                echo !$incluido
                                                    ? 'disabled'
                                                    : '';
                                                ?>
                                            >

                                                <option
                                                    value="1"
                                                    <?php
                                                    echo $aplicaPrevision
                                                        ? 'selected'
                                                        : '';
                                                    ?>
                                                >
                                                    Con aportes
                                                </option>

                                                <option
                                                    value="0"
                                                    <?php
                                                    echo !$aplicaPrevision
                                                        ? 'selected'
                                                        : '';
                                                    ?>
                                                >
                                                    Jubilado / Exento
                                                </option>

                                            </select>

                                        </td>


                                    </tr>

                                <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>


                    <!-- =====================================
                         PIE
                    ====================================== -->

                    <div class="pie">

                        <div class="contador">

                            Empleados cargados:

                            <strong id="contadorIncluidos">
                                <?php
                                echo (int)(
                                    $cantidadCargados
                                    ?? 0
                                );
                                ?>
                            </strong>

                        </div>


                        <div class="acciones-pie">

                            <a
                                href="<?php
                                    echo htmlspecialchars(
                                        $liquidacionProtocolarVolver,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                ?>"
                                class="btn btn-volver"
                            >
                                Cancelar
                            </a>


                            <button
                                type="submit"
                                class="btn btn-guardar"
                            >
                                Guardar Carga
                            </button>

                        </div>

                    </div>


                <?php else: ?>


                    <div class="sin-resultados">

                        No hay empleados con relación laboral en este período
                        disponibles para cargar Gastos Protocolares.

                    </div>


                <?php endif; ?>


            </form>


        <?php endif; ?>


    </div>

</div>


<script>

/*
|--------------------------------------------------------------------------
| HABILITAR / DESHABILITAR FILA
|--------------------------------------------------------------------------
*/

function actualizarFila(checkbox){

    const fila =
        checkbox.closest("tr");

    if(!fila){
        return;
    }


    const importe =
        fila.querySelector(
            ".input-importe"
        );

    const prevision =
        fila.querySelector(
            ".select-prevision"
        );


    if(checkbox.checked){

        importe.disabled = false;

        prevision.disabled = false;

        if(
            importe.value.trim()
            ===
            ""
        ){
            importe.focus();
        }

    }else{

        importe.disabled = true;

        prevision.disabled = true;

        importe.classList.remove(
            "input-error"
        );
    }


    actualizarContador();
}


/*
|--------------------------------------------------------------------------
| CONTADOR
|--------------------------------------------------------------------------
*/

function actualizarContador(){

    const total =
        document.querySelectorAll(
            ".chk-incluir:checked"
        ).length;

    const contador =
        document.getElementById(
            "contadorIncluidos"
        );

    if(contador){
        contador.textContent = total;
    }
}


/*
|--------------------------------------------------------------------------
| EVENTOS CHECKBOX
|--------------------------------------------------------------------------
*/

document
    .querySelectorAll(
        ".chk-incluir"
    )
    .forEach(
        function(checkbox){

            checkbox.addEventListener(
                "change",
                function(){

                    actualizarFila(
                        checkbox
                    );
                }
            );
        }
    );


/*
|--------------------------------------------------------------------------
| VALIDACIÓN
|--------------------------------------------------------------------------
*/

document
    .getElementById(
        "formProtocolar"
    )
    ?.addEventListener(
        "submit",
        function(e){

            const seleccionados =
                document.querySelectorAll(
                    ".chk-incluir:checked"
                );


            if(
                seleccionados.length
                ===
                0
            ){

                e.preventDefault();

                alert(
                    "Debe incluir al menos un empleado en Gastos Protocolares."
                );

                return;
            }


            let valido = true;


            seleccionados.forEach(
                function(checkbox){

                    if(!valido){
                        return;
                    }


                    const fila =
                        checkbox.closest("tr");

                    const importe =
                        fila.querySelector(
                            ".input-importe"
                        );


                    importe.classList.remove(
                        "input-error"
                    );


                    const valor =
                        parseFloat(
                            importe.value
                        );


                    if(
                        importe.value.trim()
                        ===
                        ""
                        ||
                        isNaN(valor)
                        ||
                        valor <= 0
                    ){

                        valido = false;

                        importe.classList.add(
                            "input-error"
                        );

                        importe.focus();
                    }
                }
            );


            if(!valido){

                e.preventDefault();

                alert(
                    "Todos los empleados incluidos deben tener un importe mayor a cero."
                );
            }
        }
    );


/*
|--------------------------------------------------------------------------
| ESTADO INICIAL
|--------------------------------------------------------------------------
*/

document.addEventListener(
    "DOMContentLoaded",
    function(){

        document
            .querySelectorAll(
                ".chk-incluir"
            )
            .forEach(
                function(checkbox){

                    actualizarFila(
                        checkbox
                    );
                }
            );

        actualizarContador();
    }
);

</script>


</body>

</html>
