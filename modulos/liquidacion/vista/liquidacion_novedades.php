<?php

require_once __DIR__ . '/../../../core/Url.php';
require_once __DIR__ . '/../../../core/Csrf.php';


/*
|--------------------------------------------------------------------------
| NOVEDADES MENSUALES - ROUTER
|--------------------------------------------------------------------------
*/

$liquidacionNovedadesId =
    (int)(
        $liquidacion['id']
        ?? 0
    );


$liquidacionNovedadesAccion =
    sigenmuniUrlRuta(
        'liquidacion/novedades',
        [
            'id' =>
                $liquidacionNovedadesId
        ]
    );


$liquidacionNovedadesVolver =
    sigenmuniUrlRuta(
        'liquidacion'
    );


$liquidacionNovedadesCsrf =
    sigenmuniCsrfToken();

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Novedades de Liquidación - SIGENMUNI</title>

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
    margin:6px 0 0;
    font-size:14px;
}


/* =========================================
   CONTENEDOR
========================================= */

.contenedor{
    width:96%;
    max-width:1450px;
    margin:30px auto;
}

.panel{
    background:white;
    padding:24px;
    border-radius:18px;
    box-shadow:0 8px 20px rgba(0,0,0,.08);
}


/* =========================================
   ENCABEZADO DEL PANEL
========================================= */

.topbar{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    gap:18px;
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
    opacity:.90;
    transform:translateY(-1px);
}

.btn-guardar{
    background:#0f766e;
}

.btn-volver{
    background:#1f2937;
}

.btn-todos-30{
    background:#2563eb;
}

.btn-todos-presentismo{
    background:#16a34a;
}

.btn-quitar-presentismo{
    background:#dc2626;
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
   DATOS DE LIQUIDACIÓN
========================================= */

.resumen-liquidacion{
    display:grid;
    grid-template-columns:
        repeat(4, minmax(150px,1fr));

    gap:12px;
    margin-bottom:20px;
}

.dato{
    background:#f8fafc;
    border:1px solid #e5e7eb;
    border-radius:12px;
    padding:12px 14px;
}

.dato strong{
    display:block;
    margin-bottom:4px;
    color:#475569;
    font-size:12px;
}

.dato span{
    font-size:14px;
    font-weight:bold;
    color:#111827;
}


/* =========================================
   AVISO
========================================= */

.aviso{
    background:#eff6ff;
    border:1px solid #bfdbfe;
    color:#1e3a8a;
    padding:14px 16px;
    border-radius:12px;
    margin-bottom:20px;
    font-size:13px;
    line-height:1.55;
}

.aviso strong{
    display:block;
    margin-bottom:5px;
}


/* =========================================
   HERRAMIENTAS
========================================= */

.herramientas{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:12px;
    flex-wrap:wrap;
    margin-bottom:18px;
}

.busqueda{
    flex:1;
    min-width:260px;
}

.busqueda input{
    width:100%;
    padding:11px 12px;
    border:1px solid #cbd5e1;
    border-radius:9px;
    font-size:13px;
    outline:none;
}

.busqueda input:focus{
    border-color:#14b8a6;
    box-shadow:0 0 0 3px rgba(20,184,166,.15);
}

.acciones-rapidas{
    display:flex;
    gap:8px;
    flex-wrap:wrap;
}


/* =========================================
   RESUMEN DE NOVEDADES
========================================= */

.resumen-novedades{
    margin-bottom:18px;
    font-size:13px;
    color:#475569;
}

.resumen-novedades strong{
    color:#0f766e;
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
    padding:9px 6px;
    border-bottom:1px solid #e5e7eb;
    font-size:11px;
    vertical-align:middle;
    overflow-wrap:anywhere;
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

tbody tr:hover{
    background:#f8fafc;
}

.col-empleado{
    text-align:left;
    min-width:0;
    width:24%;
}

.col-habilitados{
    width:12%;
}

.col-dias{
    width:13%;
}

.col-presentismo{
    width:14%;
}

.col-observacion{
    min-width:0;
    width:29%;
    text-align:left;
}

.col-legajo{
    width:8%;
}

.dias-habilitados{
    display:inline-block;
    min-width:42px;
    padding:6px 9px;
    border-radius:999px;
    background:#e0f2fe;
    color:#075985;
    font-weight:bold;
}

.dias-habilitados-parcial{
    background:#fef3c7;
    color:#92400e;
}


/* =========================================
   INPUTS TABLA
========================================= */

.input-dias{
    width:76px;
    max-width:100%;
    padding:8px 6px;
    border:1px solid #cbd5e1;
    border-radius:8px;
    text-align:center;
    font-size:12px;
}

.input-observacion{
    width:100%;
    min-width:0;
    padding:8px 9px;
    border:1px solid #cbd5e1;
    border-radius:8px;
    font-size:12px;
}

.input-dias:focus,
.input-observacion:focus{
    outline:none;
    border-color:#14b8a6;
    box-shadow:0 0 0 3px rgba(20,184,166,.12);
}


/* =========================================
   SWITCH PRESENTISMO
========================================= */

.switch-wrap{
    display:flex;
    align-items:center;
    justify-content:center;
    gap:8px;
}

.switch{
    position:relative;
    display:inline-block;
    width:44px;
    height:24px;
}

.switch input{
    opacity:0;
    width:0;
    height:0;
}

.slider{
    position:absolute;
    cursor:pointer;
    inset:0;
    background:#cbd5e1;
    border-radius:999px;
    transition:.2s;
}

.slider:before{
    content:"";
    position:absolute;
    width:18px;
    height:18px;
    left:3px;
    top:3px;
    background:white;
    border-radius:50%;
    transition:.2s;
    box-shadow:0 1px 3px rgba(0,0,0,.25);
}

.switch input:checked + .slider{
    background:#16a34a;
}

.switch input:checked + .slider:before{
    transform:translateX(20px);
}

.estado-presentismo{
    font-size:11px;
    font-weight:bold;
    min-width:22px;
}

.estado-si{
    color:#166534;
}

.estado-no{
    color:#991b1b;
}


/* =========================================
   FILAS CON NOVEDAD
========================================= */

.fila-novedad{
    background:#fffbeb;
}

.fila-novedad:hover{
    background:#fef3c7;
}


/* =========================================
   ACCIONES INFERIORES
========================================= */

.acciones-inferiores{
    display:flex;
    justify-content:flex-end;
    gap:10px;
    flex-wrap:wrap;
    margin-top:22px;
}


/* =========================================
   SIN RESULTADOS
========================================= */

.sin-resultados{
    padding:28px;
    text-align:center;
    color:#64748b;
    font-weight:bold;
}


/* =========================================
   TABLET
========================================= */

@media (max-width:1000px){

    .resumen-liquidacion{
        grid-template-columns:1fr 1fr;
    }
}


/* =========================================
   CELULAR
========================================= */

@media (max-width:900px){

    .tabla-contenedor{
        overflow:visible;
    }

    table,
    tbody,
    tr,
    td{
        display:block;
        width:100%;
    }

    table{
        table-layout:auto;
    }

    thead{
        display:none;
    }

    tbody tr{
        border:1px solid #e5e7eb;
        border-radius:12px;
        padding:7px 10px;
        margin-bottom:12px;
        overflow:hidden;
    }

    td,
    td:nth-child(n){
        width:100%;
        display:grid;
        grid-template-columns:145px minmax(0,1fr);
        align-items:center;
        gap:10px;
        padding:9px 4px;
        text-align:right;
        border-bottom:1px solid #eef2f7;
    }

    td:last-child{
        border-bottom:none;
    }

    td::before{
        content:attr(data-label);
        font-weight:bold;
        color:#475569;
        text-align:left;
        font-size:11px;
    }

    .col-empleado,
    .col-observacion{
        text-align:right;
        min-width:0;
    }

    .input-dias,
    .input-observacion,
    .switch-wrap,
    .dias-habilitados{
        justify-self:end;
    }

    .input-observacion{
        max-width:420px;
    }
}


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
        font-size:24px;
        text-align:center;
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

    .resumen-liquidacion{
        grid-template-columns:1fr;
    }

    .herramientas{
        flex-direction:column;
        align-items:stretch;
    }

    .busqueda{
        min-width:0;
    }

    .acciones-rapidas{
        flex-direction:column;
    }

    .acciones-rapidas .btn{
        width:100%;
    }

    .acciones-inferiores{
        flex-direction:column;
    }

    .acciones-inferiores .btn{
        width:100%;
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
        Novedades de Liquidación por Empleado
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
                    Novedades de Liquidación
                </h2>

                <p>
                    Defina días liquidados y presentismo para las excepciones del período.
                </p>

            </div>


            <div class="acciones-superiores">

                <a
                    href="<?php
                        echo htmlspecialchars(
                            $liquidacionNovedadesVolver,
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
             MENSAJE
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
             DATOS DE LA LIQUIDACIÓN
        ====================================== -->

        <?php if (!empty($liquidacion)): ?>

            <?php

            $tipoVisual =
                strtoupper(
                    trim(
                        (string)(
                            $liquidacion['tipo_liquidacion']
                            ?? ''
                        )
                    )
                );

            switch ($tipoVisual) {

                case 'MENSUAL':
                    $tipoVisual = 'Mensual';
                    break;

                case 'COMPLEMENTARIA':
                    $tipoVisual = 'Complementaria';
                    break;
            }

            $fechaVisual =
                !empty(
                    $liquidacion['fecha_liquidacion']
                )
                    ?
                    date(
                        'd/m/Y',
                        strtotime(
                            $liquidacion['fecha_liquidacion']
                        )
                    )
                    :
                    '-';

            ?>

            <div class="resumen-liquidacion">

                <div class="dato">
                    <strong>Liquidación</strong>
                    <span>
                        #<?php echo (int)$liquidacion['id']; ?>
                    </span>
                </div>

                <div class="dato">
                    <strong>Tipo</strong>
                    <span>
                        <?php
                        echo htmlspecialchars(
                            $tipoVisual,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>
                    </span>
                </div>

                <div class="dato">
                    <strong>Período</strong>
                    <span>
                        <?php
                        echo htmlspecialchars(
                            $liquidacion['periodo'] ?? '-',
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>
                    </span>
                </div>

                <div class="dato">
                    <strong>Fecha de liquidación</strong>
                    <span>
                        <?php
                        echo htmlspecialchars(
                            $fechaVisual,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>
                    </span>
                </div>

            </div>

        <?php endif; ?>


        <!-- =====================================
             AVISO
        ====================================== -->

        <div class="aviso">

            <strong>
                Funcionamiento de las novedades
            </strong>

            El sistema calcula automáticamente los <strong style="display:inline;">días laborales habilitados</strong>
            según el historial laboral del empleado. Un mes completo equivale a 30 días.
            Los <strong style="display:inline;">Días a liquidar</strong> pueden reducirse por una novedad,
            pero nunca superar los días habilitados. Si no corresponde Presentismo,
            desactive la opción para ese empleado.

        </div>


        <?php if (!empty($empleadosNovedades)): ?>

            <!-- =====================================
                 HERRAMIENTAS
            ====================================== -->

            <div class="herramientas">

                <div class="busqueda">

                    <input
                        type="text"
                        id="buscarEmpleado"
                        placeholder="Buscar por apellido, nombre o legajo..."
                    >

                </div>


                <div class="acciones-rapidas">

                    <button
                        type="button"
                        class="btn btn-todos-30"
                        onclick="establecerTodosDiasHabilitados();"
                    >
                        Usar días habilitados
                    </button>

                    <button
                        type="button"
                        class="btn btn-todos-presentismo"
                        onclick="establecerPresentismoTodos(true);"
                    >
                        Presentismo a todos
                    </button>

                    <button
                        type="button"
                        class="btn btn-quitar-presentismo"
                        onclick="establecerPresentismoTodos(false);"
                    >
                        Quitar presentismo a todos
                    </button>

                </div>

            </div>


            <div class="resumen-novedades">

                Empleados con novedades guardadas:
                <strong>
                    <?php echo (int)($cantidadNovedades ?? 0); ?>
                </strong>

            </div>


            <!-- =====================================
                 FORMULARIO
            ====================================== -->

            <form
                method="POST"
                action="<?php
                    echo htmlspecialchars(
                        $liquidacionNovedadesAccion,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                id="formNovedades"
            >

                <input
                    type="hidden"
                    name="_csrf"
                    value="<?php
                        echo htmlspecialchars(
                            $liquidacionNovedadesCsrf,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                >

                <div class="tabla-contenedor">

                    <table>

                        <thead>

                            <tr>
                                <th>Legajo</th>
                                <th>Empleado</th>
                                <th>Días habilitados</th>
                                <th>Días a liquidar</th>
                                <th>Presentismo</th>
                                <th>Observación</th>
                            </tr>

                        </thead>


                        <tbody id="cuerpoEmpleados">

                        <?php foreach ($empleadosNovedades as $empleado): ?>

                            <?php

                            $empleadoId =
                                (int)(
                                    $empleado['id']
                                    ?? $empleado['empleado_id']
                                    ?? 0
                                );

                            $diasHabilitados =
                                (int)(
                                    $empleado['dias_laborales_habilitados']
                                    ?? $empleado['limite_dias_liquidables']
                                    ?? 30
                                );


                            if ($diasHabilitados < 0) {
                                $diasHabilitados = 0;
                            }


                            if ($diasHabilitados > 30) {
                                $diasHabilitados = 30;
                            }


                            $dias =
                                (int)(
                                    $empleado['dias_liquidados']
                                    ?? $diasHabilitados
                                );


                            if ($dias < 0) {
                                $dias = 0;
                            }


                            if ($dias > $diasHabilitados) {
                                $dias = $diasHabilitados;
                            }


                            $aplicaPresentismo =
                                (int)(
                                    $empleado['aplica_presentismo']
                                    ?? 1
                                ) === 1;

                            $observacion =
                                (string)(
                                    $empleado['observacion_novedad']
                                    ?? ''
                                );

                            $tieneNovedad =
                                !empty(
                                    $empleado['novedad_id']
                                );

                            $textoBusqueda =
                                strtolower(
                                    trim(
                                        (string)(
                                            $empleado['nro_legajo']
                                            ?? ''
                                        )
                                        . ' '
                                        . (string)(
                                            $empleado['apellido']
                                            ?? ''
                                        )
                                        . ' '
                                        . (string)(
                                            $empleado['nombre']
                                            ?? ''
                                        )
                                    )
                                );

                            ?>

                            <tr
                                class="<?php
                                    echo $tieneNovedad
                                        ? 'fila-novedad'
                                        : '';
                                ?>"
                                data-busqueda="<?php
                                    echo htmlspecialchars(
                                        $textoBusqueda,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                ?>"
                                data-dias-habilitados="<?php
                                    echo (int)$diasHabilitados;
                                ?>"
                            >

                                <!-- LEGAJO -->

                                <td class="col-legajo" data-label="Legajo">

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

                                <td
                                    class="col-empleado"
                                    data-label="Empleado"
                                >

                                    <strong>
                                        <?php
                                        echo htmlspecialchars(
                                            trim(
                                                (string)(
                                                    $empleado['apellido']
                                                    ?? ''
                                                )
                                                . ', '
                                                . (string)(
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


                                <!-- DÍAS HABILITADOS -->

                                <td
                                    class="col-habilitados"
                                    data-label="Días habilitados"
                                >

                                    <span
                                        class="dias-habilitados <?php
                                            echo $diasHabilitados < 30
                                                ? 'dias-habilitados-parcial'
                                                : '';
                                        ?>"
                                        title="Máximo permitido según el historial laboral del empleado"
                                    >
                                        <?php echo (int)$diasHabilitados; ?>
                                    </span>

                                </td>


                                <!-- DÍAS A LIQUIDAR -->

                                <td
                                    class="col-dias"
                                    data-label="Días a liquidar"
                                >

                                    <input
                                        type="number"
                                        class="input-dias"
                                        name="empleados[<?php echo $empleadoId; ?>][dias_liquidados]"
                                        value="<?php echo $dias; ?>"
                                        min="0"
                                        max="<?php echo (int)$diasHabilitados; ?>"
                                        data-max-habilitado="<?php echo (int)$diasHabilitados; ?>"
                                        step="1"
                                        required
                                        onchange="actualizarFila(this);"
                                    >

                                </td>


                                <!-- PRESENTISMO -->

                                <td
                                    class="col-presentismo"
                                    data-label="Presentismo"
                                >

                                    <div class="switch-wrap">

                                        <label class="switch">

                                            <input
                                                type="checkbox"
                                                class="input-presentismo"
                                                name="empleados[<?php echo $empleadoId; ?>][aplica_presentismo]"
                                                value="1"
                                                <?php
                                                    echo $aplicaPresentismo
                                                        ? 'checked'
                                                        : '';
                                                ?>
                                                onchange="actualizarEstadoPresentismo(this); actualizarFila(this);"
                                            >

                                            <span class="slider"></span>

                                        </label>

                                        <span
                                            class="estado-presentismo <?php
                                                echo $aplicaPresentismo
                                                    ? 'estado-si'
                                                    : 'estado-no';
                                            ?>"
                                        >
                                            <?php
                                                echo $aplicaPresentismo
                                                    ? 'Sí'
                                                    : 'No';
                                            ?>
                                        </span>

                                    </div>

                                </td>


                                <!-- OBSERVACIÓN -->

                                <td
                                    class="col-observacion"
                                    data-label="Observación"
                                >

                                    <input
                                        type="text"
                                        class="input-observacion"
                                        name="empleados[<?php echo $empleadoId; ?>][observacion]"
                                        value="<?php
                                            echo htmlspecialchars(
                                                $observacion,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                        ?>"
                                        maxlength="255"
                                        placeholder="Opcional"
                                        oninput="actualizarFila(this);"
                                    >

                                </td>

                            </tr>

                        <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>


                <!-- =================================
                     ACCIONES
                ================================== -->

                <div class="acciones-inferiores">

                    <a
                        href="<?php
                            echo htmlspecialchars(
                                $liquidacionNovedadesVolver,
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
                        Guardar Novedades
                    </button>

                </div>

            </form>

        <?php else: ?>

            <div class="sin-resultados">

                No hay empleados disponibles para cargar novedades.

            </div>

        <?php endif; ?>

    </div>

</div>


<script>

/*
|--------------------------------------------------------------------------
| BUSCADOR
|--------------------------------------------------------------------------
*/

const buscador =
    document.getElementById("buscarEmpleado");

if(buscador){

    buscador.addEventListener(
        "input",
        function(){

            const texto =
                this.value
                    .trim()
                    .toLowerCase();

            const filas =
                document.querySelectorAll(
                    "#cuerpoEmpleados tr"
                );

            filas.forEach(function(fila){

                const contenido =
                    (
                        fila.dataset.busqueda
                        || ""
                    ).toLowerCase();

                fila.style.display =
                    contenido.includes(texto)
                        ? ""
                        : "none";
            });
        }
    );
}


/*
|--------------------------------------------------------------------------
| USAR DÍAS LABORALES HABILITADOS
|--------------------------------------------------------------------------
*/

function establecerTodosDiasHabilitados(){

    document
        .querySelectorAll(
            ".input-dias"
        )
        .forEach(function(input){

            const maximo =
                parseInt(
                    input.dataset.maxHabilitado
                    || input.max
                    || "30",
                    10
                );


            input.value =
                isNaN(maximo)
                    ? 0
                    : maximo;


            actualizarFila(
                input
            );
        });
}


/*
|--------------------------------------------------------------------------
| PRESENTISMO MASIVO
|--------------------------------------------------------------------------
*/

function establecerPresentismoTodos(estado){

    document
        .querySelectorAll(
            ".input-presentismo"
        )
        .forEach(function(input){

            input.checked =
                estado;

            actualizarEstadoPresentismo(
                input
            );

            actualizarFila(
                input
            );
        });
}


/*
|--------------------------------------------------------------------------
| TEXTO SÍ / NO
|--------------------------------------------------------------------------
*/

function actualizarEstadoPresentismo(input){

    const contenedor =
        input.closest(
            ".switch-wrap"
        );

    if(!contenedor){
        return;
    }

    const texto =
        contenedor.querySelector(
            ".estado-presentismo"
        );

    if(!texto){
        return;
    }

    if(input.checked){

        texto.textContent =
            "Sí";

        texto.classList.add(
            "estado-si"
        );

        texto.classList.remove(
            "estado-no"
        );

    }else{

        texto.textContent =
            "No";

        texto.classList.add(
            "estado-no"
        );

        texto.classList.remove(
            "estado-si"
        );
    }
}


/*
|--------------------------------------------------------------------------
| ACTUALIZAR ESTADO VISUAL DE LA FILA
|--------------------------------------------------------------------------
*/

function actualizarFila(elemento){

    const fila =
        elemento.closest(
            "tr"
        );

    if(!fila){
        return;
    }

    const diasInput =
        fila.querySelector(
            ".input-dias"
        );

    const presentismoInput =
        fila.querySelector(
            ".input-presentismo"
        );

    const observacionInput =
        fila.querySelector(
            ".input-observacion"
        );

    if(
        !diasInput
        ||
        !presentismoInput
        ||
        !observacionInput
    ){
        return;
    }

    let dias =
        parseInt(
            diasInput.value || "0",
            10
        );


    if(isNaN(dias)){
        dias = 0;
    }


    /*
    | Si no hay días liquidados, no corresponde presentismo.
    */

    if(dias === 0){

        presentismoInput.checked =
            false;

        actualizarEstadoPresentismo(
            presentismoInput
        );
    }


    const diasHabilitados =
        parseInt(
            fila.dataset.diasHabilitados
            || diasInput.dataset.maxHabilitado
            || diasInput.max
            || "30",
            10
        );


    const maximoLaboral =
        isNaN(diasHabilitados)
            ? 30
            : diasHabilitados;


    /*
    | Es novedad cuando el operador reduce los días respecto del máximo
    | laboral habilitado, quita presentismo o agrega una observación.
    | Tener menos de 30 días por alta/inactivación NO es, por sí solo,
    | una novedad manual.
    */

    const esNovedad =
        dias !== maximoLaboral
        ||
        !presentismoInput.checked
        ||
        observacionInput.value.trim() !== "";


    fila.classList.toggle(
        "fila-novedad",
        esNovedad
    );
}


/*
|--------------------------------------------------------------------------
| VALIDACIÓN ANTES DE ENVIAR
|--------------------------------------------------------------------------
*/

const formulario =
    document.getElementById(
        "formNovedades"
    );

if(formulario){

    formulario.addEventListener(
        "submit",
        function(e){

            const inputsDias =
                document.querySelectorAll(
                    ".input-dias"
                );


            for(
                const input
                of
                inputsDias
            ){

                const valor =
                    parseInt(
                        input.value,
                        10
                    );


                const maximo =
                    parseInt(
                        input.dataset.maxHabilitado
                        || input.max
                        || "30",
                        10
                    );


                if(
                    isNaN(valor)
                    ||
                    valor < 0
                    ||
                    isNaN(maximo)
                    ||
                    valor > maximo
                ){

                    e.preventDefault();

                    alert(
                        "Los días a liquidar deben estar entre 0 y "
                        + (
                            isNaN(maximo)
                                ? 30
                                : maximo
                        )
                        + " para este empleado."
                    );

                    input.focus();

                    return;
                }
            }
        }
    );
}


/*
|--------------------------------------------------------------------------
| INICIALIZAR
|--------------------------------------------------------------------------
*/

document.addEventListener(
    "DOMContentLoaded",
    function(){

        document
            .querySelectorAll(
                ".input-presentismo"
            )
            .forEach(
                actualizarEstadoPresentismo
            );
    }
);

</script>

</body>

</html>
