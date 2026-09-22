<?php

require_once __DIR__ . '/../../../core/Url.php';
require_once __DIR__ . '/../../../core/Csrf.php';


$liquidacionComplementariaId =
    (int)(
        $liquidacion['id']
        ?? 0
    );


$liquidacionComplementariaAccion =
    sigenmuniUrlRuta(
        'liquidacion/complementaria',
        [
            'id' =>
                $liquidacionComplementariaId
        ]
    );


$liquidacionComplementariaVolver =
    sigenmuniUrlRuta(
        'liquidacion'
    );


$liquidacionComplementariaCsrf =
    sigenmuniCsrfToken();

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Personal y Novedades - Complementaria - SIGENMUNI</title>

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
    margin:6px 0 0;
    font-size:14px;
}

.contenedor{
    width:96%;
    max-width:1500px;
    margin:30px auto;
}

.panel{
    background:white;
    padding:24px;
    border-radius:18px;
    box-shadow:0 8px 20px rgba(0,0,0,.08);
}

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

.btn-seleccionar{
    background:#2563eb;
}

.btn-quitar{
    background:#64748b;
}

.btn-dias{
    background:#7c3aed;
}

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

.resumen-liquidacion{
    display:grid;
    grid-template-columns:repeat(4,minmax(150px,1fr));
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
    min-width:280px;
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

.resumen-seleccion{
    margin-bottom:18px;
    font-size:13px;
    color:#475569;
}

.resumen-seleccion strong{
    color:#0f766e;
}

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
    padding:9px 5px;
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

.fila-seleccionada{
    background:#f0fdfa;
}

.fila-seleccionada:hover{
    background:#ccfbf1;
}

.col-empleado{
    text-align:left;
    min-width:0;
}

.col-observacion{
    min-width:0;
}

/*
|--------------------------------------------------------------------------
| DISTRIBUCIÓN DE COLUMNAS - SIN CATEGORÍA
|--------------------------------------------------------------------------
*/

th:nth-child(1),
td:nth-child(1){
    width:5%;
}

th:nth-child(2),
td:nth-child(2){
    width:6%;
}

th:nth-child(3),
td:nth-child(3){
    width:20%;
}

th:nth-child(4),
td:nth-child(4){
    width:10%;
}

th:nth-child(5),
td:nth-child(5){
    width:10%;
}

th:nth-child(6),
td:nth-child(6){
    width:11%;
}

th:nth-child(7),
td:nth-child(7){
    width:11%;
}

th:nth-child(8),
td:nth-child(8){
    width:11%;
}

th:nth-child(9),
td:nth-child(9){
    width:16%;
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

.input-dias{
    width:72px;
    max-width:100%;
    padding:8px 7px;
    border:1px solid #cbd5e1;
    border-radius:8px;
    text-align:center;
    font-size:13px;
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

input:disabled{
    background:#f1f5f9;
    color:#94a3b8;
    cursor:not-allowed;
}

.check-incluir{
    width:18px;
    height:18px;
    cursor:pointer;
}

.switch-wrap{
    display:flex;
    justify-content:center;
    align-items:center;
    gap:7px;
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
}

.estado-si{
    color:#166534;
}

.estado-no{
    color:#991b1b;
}

.badge{
    display:inline-block;
    padding:6px 10px;
    border-radius:999px;
    font-size:11px;
    font-weight:bold;
    white-space:nowrap;
}

.badge-activo{
    background:#dcfce7;
    color:#166534;
}

.badge-inactivo{
    background:#f1f5f9;
    color:#475569;
}

.acciones-inferiores{
    display:flex;
    justify-content:flex-end;
    gap:10px;
    flex-wrap:wrap;
    margin-top:22px;
}

.sin-resultados{
    padding:28px;
    text-align:center;
    color:#64748b;
    font-weight:bold;
}

@media (max-width:1000px){

    .resumen-liquidacion{
        grid-template-columns:1fr 1fr;
    }
}

@media (max-width:950px){

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
    }

    .check-incluir,
    .input-dias,
    .input-observacion,
    .switch-wrap,
    .badge,
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

    .topbar,
    .herramientas{
        flex-direction:column;
        align-items:stretch;
    }

    .titulo h2,
    .titulo p{
        text-align:center;
    }

    .resumen-liquidacion{
        grid-template-columns:1fr;
    }

    .acciones-superiores,
    .acciones-rapidas,
    .acciones-inferiores{
        flex-direction:column;
    }

    .acciones-superiores .btn,
    .acciones-rapidas .btn,
    .acciones-inferiores .btn{
        width:100%;
    }

    .busqueda{
        min-width:0;
    }
}

</style>

</head>

<body>

<div class="header">
    <h1>SIGENMUNI</h1>
    <p>Liquidación Complementaria de Haberes</p>
</div>

<div class="contenedor">

    <div class="panel">

        <div class="topbar">

            <div class="titulo">
                <h2>Personal y Novedades</h2>
                <p>
                    Seleccione únicamente los empleados que deben ingresar en esta liquidación complementaria.
                </p>
            </div>

            <div class="acciones-superiores">
                <a
                    href="<?php
                        echo htmlspecialchars(
                            $liquidacionComplementariaVolver,
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


        <?php if (!empty($liquidacion)): ?>

            <?php
            $fechaVisual =
                !empty($liquidacion['fecha_liquidacion'])
                    ? date(
                        'd/m/Y',
                        strtotime($liquidacion['fecha_liquidacion'])
                    )
                    : '-';
            ?>

            <div class="resumen-liquidacion">

                <div class="dato">
                    <strong>Liquidación</strong>
                    <span>#<?php echo (int)$liquidacion['id']; ?></span>
                </div>

                <div class="dato">
                    <strong>Tipo</strong>
                    <span>Complementaria</span>
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
                    <strong>Fecha</strong>
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


        <div class="aviso">
            <strong>Importante:</strong>
            solo se procesarán los empleados que queden marcados en la columna
            <b>Incluir</b>. El sistema calcula los <b>días laborales habilitados</b>
            según el historial laboral del período. Los días a liquidar pueden
            reducirse, pero nunca superar ese máximo. El Presentismo puede
            activarse o desactivarse individualmente.
        </div>


        <?php if (!empty($empleadosComplementaria)): ?>

            <div class="herramientas">

                <div class="busqueda">
                    <input
                        type="text"
                        id="buscarEmpleado"
                        placeholder="Buscar por apellido, nombre, legajo o DNI..."
                    >
                </div>

                <div class="acciones-rapidas">

                    <button
                        type="button"
                        class="btn btn-seleccionar"
                        onclick="seleccionarVisibles(true);"
                    >
                        Seleccionar visibles
                    </button>

                    <button
                        type="button"
                        class="btn btn-quitar"
                        onclick="seleccionarVisibles(false);"
                    >
                        Quitar selección visible
                    </button>

                    <button
                        type="button"
                        class="btn btn-dias"
                        onclick="usarDiasHabilitadosSeleccionados();"
                    >
                        Usar días habilitados
                    </button>

                </div>

            </div>


            <div class="resumen-seleccion">
                Empleados seleccionados:
                <strong id="contadorSeleccionados">
                    <?php echo (int)($cantidadSeleccionados ?? 0); ?>
                </strong>
            </div>


            <form
                method="POST"
                action="<?php
                    echo htmlspecialchars(
                        $liquidacionComplementariaAccion,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                id="formComplementaria"
            >

                <input
                    type="hidden"
                    name="_csrf"
                    value="<?php
                        echo htmlspecialchars(
                            $liquidacionComplementariaCsrf,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                >

                <div class="tabla-contenedor">

                    <table>

                        <thead>
                            <tr>
                                <th>Incluir</th>
                                <th>Legajo</th>
                                <th>Empleado</th>
                                <th>DNI</th>
                                <th>Situación</th>
                                <th>Días habilitados</th>
                                <th>Días a liquidar</th>
                                <th>Presentismo</th>
                                <th>Observación</th>
                            </tr>
                        </thead>

                        <tbody id="cuerpoEmpleados">

                        <?php foreach ($empleadosComplementaria as $empleado): ?>

                            <?php

                            $empleadoId =
                                (int)(
                                    $empleado['id']
                                    ?? $empleado['empleado_id']
                                    ?? 0
                                );

                            $incluido =
                                (int)(
                                    $empleado['incluido']
                                    ?? 0
                                ) === 1;

                            $diasHabilitados =
                                (int)(
                                    $empleado['dias_laborales_habilitados']
                                    ?? $empleado['limite_dias_liquidables']
                                    ?? 30
                                );

                            if ($diasHabilitados < 1) {
                                $diasHabilitados = 1;
                            }

                            if ($diasHabilitados > 30) {
                                $diasHabilitados = 30;
                            }


                            $dias =
                                (int)(
                                    $empleado['dias_liquidados']
                                    ?? $diasHabilitados
                                );

                            if (
                                $dias < 1
                                ||
                                $dias > $diasHabilitados
                            ) {
                                $dias = $diasHabilitados;
                            }

                            $presentismo =
                                (int)(
                                    $empleado['aplica_presentismo']
                                    ?? 1
                                ) === 1;

                            $observacion =
                                (string)(
                                    $empleado['observacion_novedad']
                                    ?? ''
                                );

                            $activo =
                                (int)(
                                    $empleado['activo']
                                    ?? 0
                                ) === 1;

                            $textoBusqueda =
                                strtolower(
                                    trim(
                                        (string)($empleado['nro_legajo'] ?? '')
                                        . ' '
                                        . (string)($empleado['apellido'] ?? '')
                                        . ' '
                                        . (string)($empleado['nombre'] ?? '')
                                        . ' '
                                        . (string)($empleado['dni'] ?? '')
                                    )
                                );

                            ?>

                            <tr
                                class="<?php
                                    echo $incluido
                                        ? 'fila-seleccionada'
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

                                <td data-label="Incluir">

                                    <input
                                        type="checkbox"
                                        class="check-incluir"
                                        name="empleados[<?php echo $empleadoId; ?>][incluir]"
                                        value="1"
                                        <?php echo $incluido ? 'checked' : ''; ?>
                                        onchange="actualizarFila(this);"
                                    >

                                </td>


                                <td data-label="Legajo">
                                    <?php
                                    echo htmlspecialchars(
                                        $empleado['nro_legajo'] ?? '-',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>
                                </td>


                                <td
                                    class="col-empleado"
                                    data-label="Empleado"
                                >
                                    <strong>
                                        <?php
                                        echo htmlspecialchars(
                                            trim(
                                                (string)($empleado['apellido'] ?? '')
                                                . ', '
                                                . (string)($empleado['nombre'] ?? '')
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        );
                                        ?>
                                    </strong>
                                </td>


                                <td data-label="DNI">
                                    <?php
                                    echo htmlspecialchars(
                                        $empleado['dni'] ?? '-',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>
                                </td>


                                <td data-label="Situación">
                                    <span
                                        class="badge <?php
                                            echo $activo
                                                ? 'badge-activo'
                                                : 'badge-inactivo';
                                        ?>"
                                    >
                                        <?php echo $activo ? 'Activo' : 'Inactivo'; ?>
                                    </span>
                                </td>


                                <td data-label="Días habilitados">

                                    <span
                                        class="dias-habilitados <?php
                                            echo $diasHabilitados < 30
                                                ? 'dias-habilitados-parcial'
                                                : '';
                                        ?>"
                                        title="Máximo permitido según historial laboral"
                                    >
                                        <?php echo (int)$diasHabilitados; ?>
                                    </span>

                                </td>


                                <td data-label="Días a liquidar">

                                    <input
                                        type="number"
                                        class="input-dias campo-novedad"
                                        name="empleados[<?php echo $empleadoId; ?>][dias_liquidados]"
                                        value="<?php echo $dias; ?>"
                                        min="1"
                                        max="<?php echo (int)$diasHabilitados; ?>"
                                        data-max-habilitado="<?php echo (int)$diasHabilitados; ?>"
                                        step="1"
                                        <?php echo $incluido ? '' : 'disabled'; ?>
                                        required
                                    >

                                </td>


                                <td data-label="Presentismo">

                                    <div class="switch-wrap">

                                        <label class="switch">

                                            <input
                                                type="checkbox"
                                                class="input-presentismo campo-novedad"
                                                name="empleados[<?php echo $empleadoId; ?>][aplica_presentismo]"
                                                value="1"
                                                <?php echo $presentismo ? 'checked' : ''; ?>
                                                <?php echo $incluido ? '' : 'disabled'; ?>
                                                onchange="actualizarTextoPresentismo(this);"
                                            >

                                            <span class="slider"></span>

                                        </label>

                                        <span
                                            class="estado-presentismo <?php
                                                echo $presentismo
                                                    ? 'estado-si'
                                                    : 'estado-no';
                                            ?>"
                                        >
                                            <?php echo $presentismo ? 'Sí' : 'No'; ?>
                                        </span>

                                    </div>

                                </td>


                                <td
                                    class="col-observacion"
                                    data-label="Observación"
                                >

                                    <input
                                        type="text"
                                        class="input-observacion campo-novedad"
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
                                        <?php echo $incluido ? '' : 'disabled'; ?>
                                    >

                                </td>

                            </tr>

                        <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>


                <div class="acciones-inferiores">

                    <a
                        href="<?php
                            echo htmlspecialchars(
                                $liquidacionComplementariaVolver,
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
                        Guardar Personal y Novedades
                    </button>

                </div>

            </form>

        <?php else: ?>

            <div class="sin-resultados">
                No hay empleados disponibles para esta liquidación complementaria.
            </div>

        <?php endif; ?>

    </div>

</div>


<script>

const buscador =
    document.getElementById(
        "buscarEmpleado"
    );


if(buscador){

    buscador.addEventListener(
        "input",
        function(){

            const texto =
                this.value
                    .trim()
                    .toLowerCase();

            document
                .querySelectorAll(
                    "#cuerpoEmpleados tr"
                )
                .forEach(function(fila){

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


function actualizarFila(check){

    const fila =
        check.closest("tr");

    if(!fila){
        return;
    }

    const seleccionado =
        check.checked;

    fila.classList.toggle(
        "fila-seleccionada",
        seleccionado
    );

    fila
        .querySelectorAll(
            ".campo-novedad"
        )
        .forEach(function(campo){

            campo.disabled =
                !seleccionado;
        });

    actualizarContador();
}


function actualizarTextoPresentismo(input){

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


function seleccionarVisibles(estado){

    document
        .querySelectorAll(
            "#cuerpoEmpleados tr"
        )
        .forEach(function(fila){

            if(fila.style.display === "none"){
                return;
            }

            const check =
                fila.querySelector(
                    ".check-incluir"
                );

            if(!check){
                return;
            }

            check.checked =
                estado;

            actualizarFila(
                check
            );
        });
}


function usarDiasHabilitadosSeleccionados(){

    document
        .querySelectorAll(
            "#cuerpoEmpleados tr"
        )
        .forEach(function(fila){

            const check =
                fila.querySelector(
                    ".check-incluir"
                );

            if(
                !check
                ||
                !check.checked
            ){
                return;
            }

            const input =
                fila.querySelector(
                    ".input-dias"
                );


            if(input){

                const maximo =
                    parseInt(
                        input.dataset.maxHabilitado
                        || fila.dataset.diasHabilitados
                        || input.max
                        || "30",
                        10
                    );


                input.value =
                    isNaN(maximo)
                        ? 1
                        : maximo;
            }
        });
}


function actualizarContador(){

    const contador =
        document.getElementById(
            "contadorSeleccionados"
        );

    if(!contador){
        return;
    }

    const total =
        document.querySelectorAll(
            ".check-incluir:checked"
        ).length;

    contador.textContent =
        total;
}


const formulario =
    document.getElementById(
        "formComplementaria"
    );


if(formulario){

    formulario.addEventListener(
        "submit",
        function(e){

            const seleccionados =
                document.querySelectorAll(
                    ".check-incluir:checked"
                );


            if(seleccionados.length === 0){

                e.preventDefault();

                alert(
                    "Debe seleccionar al menos un empleado para la liquidación complementaria."
                );

                return;
            }


            for(
                const check
                of
                seleccionados
            ){

                const fila =
                    check.closest("tr");

                const inputDias =
                    fila.querySelector(
                        ".input-dias"
                    );

                const dias =
                    parseInt(
                        inputDias.value,
                        10
                    );


                const maximo =
                    parseInt(
                        inputDias.dataset.maxHabilitado
                        || fila.dataset.diasHabilitados
                        || inputDias.max
                        || "30",
                        10
                    );


                if(
                    isNaN(dias)
                    ||
                    dias < 1
                    ||
                    isNaN(maximo)
                    ||
                    dias > maximo
                ){

                    e.preventDefault();

                    alert(
                        "Los días a liquidar deben estar entre 1 y "
                        + (
                            isNaN(maximo)
                                ? 30
                                : maximo
                        )
                        + " para el empleado seleccionado."
                    );

                    inputDias.focus();

                    return;
                }
            }
        }
    );
}


document.addEventListener(
    "DOMContentLoaded",
    function(){

        document
            .querySelectorAll(
                ".input-presentismo"
            )
            .forEach(
                actualizarTextoPresentismo
            );

        actualizarContador();
    }
);

</script>

</body>

</html>
