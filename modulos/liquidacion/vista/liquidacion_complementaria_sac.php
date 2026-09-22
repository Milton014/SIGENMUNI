<?php

require_once __DIR__ . '/../../../core/Url.php';
require_once __DIR__ . '/../../../core/Csrf.php';


/*
|--------------------------------------------------------------------------
| COMPLEMENTARIA SAC - ROUTER
|--------------------------------------------------------------------------
*/

$liquidacionComplementariaSacId =
    (int)(
        $liquidacion['id']
        ?? 0
    );


$liquidacionComplementariaSacAccion =
    sigenmuniUrlRuta(
        'liquidacion/complementaria-sac',
        [
            'id' =>
                $liquidacionComplementariaSacId
        ]
    );


$liquidacionComplementariaSacVolver =
    sigenmuniUrlRuta(
        'liquidacion'
    );


$liquidacionComplementariaSacCsrf =
    sigenmuniCsrfToken();

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Personal y Días SAC - Complementaria SAC - SIGENMUNI</title>

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
    width:97%;
    max-width:1600px;
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

.btn-saldo{
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
    grid-template-columns:repeat(5,minmax(140px,1fr));
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
    padding:15px 17px;
    border-radius:12px;
    margin-bottom:20px;
    font-size:13px;
    line-height:1.55;
}

.formula{
    margin-top:8px;
    padding:10px 12px;
    background:white;
    border:1px solid #dbeafe;
    border-radius:9px;
    color:#1e40af;
    font-weight:bold;
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

.resumen-sac{
    display:flex;
    gap:18px;
    flex-wrap:wrap;
    margin-bottom:18px;
    font-size:13px;
    color:#475569;
}

.resumen-sac strong{
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

/*
|--------------------------------------------------------------------------
| ANCHOS DE COLUMNAS - SIN SCROLL HORIZONTAL
|--------------------------------------------------------------------------
|
| La tabla utiliza todo el ancho disponible y distribuye las 12 columnas
| proporcionalmente. Esto elimina la necesidad de una barra horizontal.
|
*/

th:nth-child(1),
td:nth-child(1){
    width:4%;
}

th:nth-child(2),
td:nth-child(2){
    width:5%;
}

th:nth-child(3),
td:nth-child(3){
    width:17%;
}

th:nth-child(4),
td:nth-child(4){
    width:8%;
}

th:nth-child(5),
td:nth-child(5){
    width:8%;
}

th:nth-child(6),
td:nth-child(6){
    width:8%;
}

th:nth-child(7),
td:nth-child(7){
    width:9%;
}

th:nth-child(8),
td:nth-child(8){
    width:8%;
}

th:nth-child(9),
td:nth-child(9){
    width:8%;
}

th:nth-child(10),
td:nth-child(10){
    width:8%;
}

th:nth-child(11),
td:nth-child(11){
    width:9%;
}

th:nth-child(12),
td:nth-child(12){
    width:8%;
}

tbody tr:hover{
    background:#f8fafc;
}

.fila-seleccionada{
    background:#f0fdfa;
}

.fila-parcial{
    background:#fffbeb;
}

.fila-liquidada{
    background:#f8fafc;
    color:#64748b;
}

.col-empleado{
    text-align:left;
    min-width:0;
}

.input-dias-sac{
    width:72px;
    max-width:100%;
    padding:8px 5px;
    border:1px solid #cbd5e1;
    border-radius:8px;
    text-align:center;
    font-size:12px;
}

.input-dias-sac:focus{
    outline:none;
    border-color:#14b8a6;
    box-shadow:0 0 0 3px rgba(20,184,166,.12);
}

.input-dias-sac:disabled{
    background:#f1f5f9;
    color:#94a3b8;
    cursor:not-allowed;
}

.check-incluir{
    width:18px;
    height:18px;
    cursor:pointer;
}

.check-incluir:disabled{
    cursor:not-allowed;
}

.badge{
    display:inline-block;
    padding:6px 10px;
    border-radius:999px;
    font-size:11px;
    font-weight:bold;
    white-space:nowrap;
}

.badge-pendiente{
    background:#dbeafe;
    color:#1d4ed8;
}

.badge-parcial{
    background:#fef3c7;
    color:#92400e;
}

.badge-liquidado{
    background:#dcfce7;
    color:#166534;
}

.badge-activo{
    background:#e0f2fe;
    color:#075985;
}

.badge-inactivo{
    background:#f1f5f9;
    color:#475569;
}

.advertencia{
    margin-top:5px;
    font-size:10px;
    color:#b45309;
    font-weight:bold;
    line-height:1.35;
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

@media (max-width:1100px){

    .resumen-liquidacion{
        grid-template-columns:1fr 1fr;
    }
}

@media (max-width:900px){

    /*
    |--------------------------------------------------------------------------
    | TABLA EN FORMATO TARJETA
    |--------------------------------------------------------------------------
    |
    | En pantallas angostas se evita comprimir las 12 columnas.
    | Cada empleado pasa a mostrarse como una tarjeta vertical y no se genera
    | desplazamiento horizontal.
    |
    */

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

    tbody tr:last-child{
        margin-bottom:0;
    }

    td,
    td:nth-child(n){
        width:100%;
        display:grid;
        grid-template-columns:135px minmax(0,1fr);
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

    .col-empleado{
        text-align:right;
        min-width:0;
    }

    .input-dias-sac{
        justify-self:end;
        width:90px;
    }

    .check-incluir{
        justify-self:end;
    }

    .badge{
        justify-self:end;
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

    .acciones-rapidas,
    .acciones-inferiores{
        flex-direction:column;
    }

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
    <p>Liquidación Complementaria de SAC</p>
</div>

<div class="contenedor">

    <div class="panel">

        <div class="topbar">

            <div class="titulo">
                <h2>Personal y Días SAC</h2>
                <p>
                    Seleccione empleados con saldo SAC pendiente y defina los días que se liquidarán ahora.
                </p>
            </div>

            <div>
                <a
                    href="<?php
                        echo htmlspecialchars(
                            $liquidacionComplementariaSacVolver,
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


        <?php

        $periodoLiquidacion =
            (string)(
                $liquidacion['periodo']
                ?? ''
            );

        $anio = 0;
        $mes = 0;

        if (
            preg_match(
                '/^(\d{4})-(\d{2})$/',
                $periodoLiquidacion,
                $coincidencias
            )
        ) {
            $anio = (int)$coincidencias[1];
            $mes = (int)$coincidencias[2];
        }

        if ($mes >= 1 && $mes <= 6) {
            $semestreTexto = '1.er semestre';
        } elseif ($mes >= 7 && $mes <= 12) {
            $semestreTexto = '2.º semestre';
        } else {
            $semestreTexto = '-';
        }

        $fechaVisual =
            !empty($liquidacion['fecha_liquidacion'])
                ? date(
                    'd/m/Y',
                    strtotime($liquidacion['fecha_liquidacion'])
                )
                : '-';

        ?>


        <?php if (!empty($liquidacion)): ?>

            <div class="resumen-liquidacion">

                <div class="dato">
                    <strong>Liquidación</strong>
                    <span>#<?php echo (int)$liquidacion['id']; ?></span>
                </div>

                <div class="dato">
                    <strong>Tipo</strong>
                    <span>Complementaria SAC</span>
                </div>

                <div class="dato">
                    <strong>Período</strong>
                    <span>
                        <?php
                        echo htmlspecialchars(
                            $periodoLiquidacion,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>
                    </span>
                </div>

                <div class="dato">
                    <strong>Semestre</strong>
                    <span>
                        <?php
                        echo htmlspecialchars(
                            $semestreTexto,
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

            <strong>Control automático de saldo SAC</strong><br>

            El sistema muestra los días SAC devengados, ya pagados y pendientes.
            Un empleado con saldo pendiente igual a 0 queda bloqueado y no puede
            volver a liquidarse.

            <div class="formula">
                SAC = Base Remunerativa × 50% × (Días SAC / 180)
            </div>

        </div>


        <?php if (!empty($empleadosSac)): ?>

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
                        onclick="seleccionarConSaldoVisibles();"
                    >
                        Seleccionar con saldo
                    </button>

                    <button
                        type="button"
                        class="btn btn-quitar"
                        onclick="quitarSeleccionVisible();"
                    >
                        Quitar selección visible
                    </button>

                    <button
                        type="button"
                        class="btn btn-saldo"
                        onclick="usarSaldoPendiente();"
                    >
                        Usar saldo pendiente
                    </button>

                </div>

            </div>


            <div class="resumen-sac">

                <span>
                    Seleccionados:
                    <strong id="contadorSeleccionados">
                        <?php echo (int)($cantidadSeleccionados ?? 0); ?>
                    </strong>
                </span>

                <span>
                    Con saldo pendiente:
                    <strong>
                        <?php echo (int)($cantidadSacPendiente ?? 0); ?>
                    </strong>
                </span>

                <span>
                    SAC ya liquidado:
                    <strong>
                        <?php echo (int)($cantidadSacYaLiquidado ?? 0); ?>
                    </strong>
                </span>

            </div>


            <form
                method="POST"
                action="<?php
                    echo htmlspecialchars(
                        $liquidacionComplementariaSacAccion,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                id="formComplementariaSac"
            >

                <input
                    type="hidden"
                    name="_csrf"
                    value="<?php
                        echo htmlspecialchars(
                            $liquidacionComplementariaSacCsrf,
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
                                <th>Alta</th>
                                <th>Fecha Inactivo</th>
                                <th>Devengados</th>
                                <th>Ya pagados</th>
                                <th>Pendientes</th>
                                <th>Días a liquidar</th>
                                <th>Estado SAC</th>
                            </tr>

                        </thead>


                        <tbody id="cuerpoEmpleados">

                        <?php foreach ($empleadosSac as $empleado): ?>

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

                            $diasDevengados =
                                (int)(
                                    $empleado['dias_sac_devengados']
                                    ?? 0
                                );

                            $diasPagados =
                                (int)(
                                    $empleado['dias_sac_pagados']
                                    ?? 0
                                );

                            $diasPendientes =
                                (int)(
                                    $empleado['dias_sac_pendientes']
                                    ?? 0
                                );

                            $diasSac =
                                (int)(
                                    $empleado['dias_sac']
                                    ?? $diasPendientes
                                );

                            if ($diasSac < 0) {
                                $diasSac = 0;
                            }

                            if ($diasSac > $diasPendientes) {
                                $diasSac = $diasPendientes;
                            }

                            $puedeLiquidar =
                                (int)(
                                    $empleado['puede_liquidar_sac']
                                    ?? 0
                                ) === 1
                                &&
                                $diasPendientes > 0;

                            if (!$puedeLiquidar) {
                                $incluido = false;
                            }

                            $estadoSac =
                                strtoupper(
                                    trim(
                                        (string)(
                                            $empleado['estado_sac']
                                            ?? 'PENDIENTE'
                                        )
                                    )
                                );

                            if ($estadoSac === 'YA_LIQUIDADO') {
                                $claseEstado = 'badge-liquidado';
                                $textoEstado = 'Ya liquidado';
                                $claseFila = 'fila-liquidada';
                            } elseif ($estadoSac === 'PARCIAL') {
                                $claseEstado = 'badge-parcial';
                                $textoEstado = 'Parcial';
                                $claseFila = $incluido
                                    ? 'fila-seleccionada'
                                    : 'fila-parcial';
                            } else {
                                $claseEstado = 'badge-pendiente';
                                $textoEstado = 'Pendiente';
                                $claseFila = $incluido
                                    ? 'fila-seleccionada'
                                    : '';
                            }

                            $activo =
                                (int)(
                                    $empleado['activo']
                                    ?? 0
                                ) === 1;

                            $fechaAltaVisual =
                                !empty($empleado['fecha_alta'])
                                &&
                                strpos(
                                    (string)$empleado['fecha_alta'],
                                    '0000-00-00'
                                ) !== 0
                                    ? date(
                                        'd/m/Y',
                                        strtotime($empleado['fecha_alta'])
                                    )
                                    : '-';

                            $fechaInactivoVisual =
                                !empty($empleado['fecha_inactivo'])
                                &&
                                strpos(
                                    (string)$empleado['fecha_inactivo'],
                                    '0000-00-00'
                                ) !== 0
                                    ? date(
                                        'd/m/Y',
                                        strtotime($empleado['fecha_inactivo'])
                                    )
                                    : '-';

                            $advertencia =
                                trim(
                                    (string)(
                                        $empleado['advertencia_sac']
                                        ?? ''
                                    )
                                );

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
                                class="<?php echo $claseFila; ?>"
                                data-busqueda="<?php
                                    echo htmlspecialchars(
                                        $textoBusqueda,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                ?>"
                                data-pendientes="<?php echo $diasPendientes; ?>"
                            >

                                <td data-label="Incluir">

                                    <input
                                        type="checkbox"
                                        class="check-incluir"
                                        name="empleados[<?php echo $empleadoId; ?>][incluir]"
                                        value="1"
                                        <?php echo $incluido ? 'checked' : ''; ?>
                                        <?php echo $puedeLiquidar ? '' : 'disabled'; ?>
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

                                    <?php if ($advertencia !== ''): ?>

                                        <div class="advertencia">
                                            <?php
                                            echo htmlspecialchars(
                                                $advertencia,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                            ?>
                                        </div>

                                    <?php endif; ?>

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


                                <td data-label="Alta">
                                    <?php echo htmlspecialchars($fechaAltaVisual, ENT_QUOTES, 'UTF-8'); ?>
                                </td>


                                <td data-label="Fecha Inactivo">
                                    <?php echo htmlspecialchars($fechaInactivoVisual, ENT_QUOTES, 'UTF-8'); ?>
                                </td>


                                <td data-label="Devengados">
                                    <?php echo $diasDevengados; ?>
                                </td>


                                <td data-label="Ya pagados">
                                    <?php echo $diasPagados; ?>
                                </td>


                                <td data-label="Pendientes">
                                    <strong><?php echo $diasPendientes; ?></strong>
                                </td>


                                <td data-label="Días a liquidar">

                                    <input
                                        type="number"
                                        class="input-dias-sac"
                                        name="empleados[<?php echo $empleadoId; ?>][dias_sac]"
                                        value="<?php echo $diasSac; ?>"
                                        min="1"
                                        max="<?php echo max(1,$diasPendientes); ?>"
                                        step="1"
                                        <?php
                                            echo (
                                                $incluido
                                                &&
                                                $puedeLiquidar
                                            )
                                                ? ''
                                                : 'disabled';
                                        ?>
                                        required
                                    >

                                </td>


                                <td data-label="Estado SAC">

                                    <span
                                        class="badge <?php echo $claseEstado; ?>"
                                    >
                                        <?php echo htmlspecialchars($textoEstado, ENT_QUOTES, 'UTF-8'); ?>
                                    </span>

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
                                $liquidacionComplementariaSacVolver,
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
                        Guardar Personal y Días SAC
                    </button>

                </div>

            </form>

        <?php else: ?>

            <div class="sin-resultados">
                No hay empleados disponibles para esta complementaria de SAC.
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

    const pendientes =
        parseInt(
            fila.dataset.pendientes || "0",
            10
        );

    const input =
        fila.querySelector(
            ".input-dias-sac"
        );


    if(check.checked && pendientes > 0){

        fila.classList.add(
            "fila-seleccionada"
        );

        if(input){

            input.disabled =
                false;

            if(
                parseInt(input.value,10) <= 0
                ||
                parseInt(input.value,10) > pendientes
            ){
                input.value =
                    pendientes;
            }
        }

    }else{

        fila.classList.remove(
            "fila-seleccionada"
        );

        if(input){

            input.disabled =
                true;
        }
    }


    actualizarContador();
}


function seleccionarConSaldoVisibles(){

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

            if(
                !check
                ||
                check.disabled
            ){
                return;
            }

            check.checked =
                true;

            actualizarFila(
                check
            );
        });
}


function quitarSeleccionVisible(){

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

            if(
                !check
                ||
                check.disabled
            ){
                return;
            }

            check.checked =
                false;

            actualizarFila(
                check
            );
        });
}


function usarSaldoPendiente(){

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

            const pendientes =
                parseInt(
                    fila.dataset.pendientes || "0",
                    10
                );

            const input =
                fila.querySelector(
                    ".input-dias-sac"
                );

            if(
                input
                &&
                pendientes > 0
            ){
                input.value =
                    pendientes;
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
        "formComplementariaSac"
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
                    "Debe seleccionar al menos un empleado con saldo SAC pendiente."
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

                const pendientes =
                    parseInt(
                        fila.dataset.pendientes || "0",
                        10
                    );

                const input =
                    fila.querySelector(
                        ".input-dias-sac"
                    );

                const dias =
                    parseInt(
                        input.value,
                        10
                    );


                if(
                    isNaN(dias)
                    ||
                    dias < 1
                    ||
                    dias > pendientes
                ){

                    e.preventDefault();

                    alert(
                        "Los días SAC a liquidar deben estar entre 1 y el saldo pendiente del empleado."
                    );

                    input.focus();

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
                ".check-incluir"
            )
            .forEach(function(check){

                if(!check.disabled){
                    actualizarFila(check);
                }
            });

        actualizarContador();
    }
);

</script>

</body>

</html>
