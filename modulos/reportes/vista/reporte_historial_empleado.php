<?php

require_once __DIR__ . '/../../../core/Url.php';


/*
|--------------------------------------------------------------------------
| HISTORIAL POR EMPLEADO - ROUTER
|--------------------------------------------------------------------------
*/

$historialUrlReportes =
    sigenmuniUrlRuta(
        'reportes'
    );


$historialUrlMenu =
    sigenmuniUrlArchivo(
        'index.php'
    );


$historialUrlEntrada =
    sigenmuniUrlEntrada();


$historialUrlLimpiar =
    sigenmuniUrlRuta(
        'reportes/historial-empleado'
    );


$historialUrlPdf =
    sigenmuniUrlRuta(
        'reportes/historial-empleado/pdf',
        [
            'empleado_id' =>
                (int)(
                    $empleadoId
                    ?? 0
                )
        ]
    );


$historialUrlExcel =
    sigenmuniUrlRuta(
        'reportes/historial-empleado/excel',
        [
            'empleado_id' =>
                (int)(
                    $empleadoId
                    ?? 0
                )
        ]
    );


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

<title>Historial por Empleado - SIGENMUNI</title>

<style>

*{
    box-sizing:border-box;
}

body{
    margin:0;
    font-family:Arial,Helvetica,sans-serif;
    background:#f4f7fb;
    color:#1f2937;
}

.contenedor{
    width:95%;
    max-width:1450px;
    margin:30px auto;
}

.cabecera{
    background:linear-gradient(135deg,#0f766e,#14b8a6);
    color:white;
    border-radius:18px;
    padding:24px;
    box-shadow:0 8px 20px rgba(0,0,0,.10);
    margin-bottom:22px;
}

.cabecera-top{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:15px;
    flex-wrap:wrap;
}

.cabecera h1{
    margin:0 0 6px 0;
    font-size:30px;
}

.cabecera p{
    margin:0;
    opacity:.95;
}

.acciones-superiores{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
}

.btn{
    display:inline-block;
    text-decoration:none;
    border:none;
    border-radius:10px;
    padding:11px 16px;
    font-size:14px;
    font-weight:bold;
    cursor:pointer;
    color:white;
    transition:.2s ease;
    text-align:center;
}

.btn:hover{
    opacity:.93;
    transform:translateY(-1px);
}

.btn-volver{
    background:#374151;
}

.btn-reportes{
    background:#ea580c;
}

.btn-buscar{
    background:#0f766e;
}

.btn-limpiar{
    background:#6b7280;
}

.btn-pdf{
    background:#dc2626;
}

.btn-excel{
    background:#15803d;
}

.btn-ver{
    background:#2563eb;
}

.btn-recibo{
    background:#0f766e;
}

.panel{
    background:white;
    border-radius:18px;
    padding:20px;
    box-shadow:0 8px 20px rgba(0,0,0,.08);
    border:1px solid #e5e7eb;
    margin-bottom:20px;
}

.panel h2{
    margin-top:0;
    margin-bottom:16px;
    font-size:20px;
    color:#0f766e;
}

.error{
    background:#fee2e2;
    color:#991b1b;
    border:1px solid #fecaca;
    padding:14px;
    border-radius:12px;
    margin-bottom:20px;
    font-weight:bold;
}

.busqueda-form{
    display:grid;
    grid-template-columns:1fr 2fr 1fr auto auto;
    gap:12px;
    align-items:start;
}

.busqueda-form > .btn{
    margin-top:25px;
}

.campo{
    display:flex;
    flex-direction:column;
    min-width:0;
}

.campo label{
    display:block;
    margin-bottom:6px;
    font-size:14px;
    font-weight:bold;
    color:#374151;
}

.campo input,
.campo select{
    width:100%;
    padding:11px 12px;
    border:1px solid #cbd5e1;
    border-radius:10px;
    font-size:14px;
    outline:none;
}

.campo input:focus,
.campo select:focus{
    border-color:#14b8a6;
    box-shadow:0 0 0 3px rgba(20,184,166,.15);
}

.ayuda{
    margin-top:6px;
    font-size:12px;
    color:#6b7280;
    line-height:1.4;
}

.datos-empleado{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
    gap:12px;
}

.info-box{
    border:1px solid #e5e7eb;
    background:#f9fafb;
    border-radius:10px;
    padding:12px;
}

.info-box strong{
    display:block;
    margin-bottom:5px;
    color:#374151;
    font-size:13px;
}

.acciones-exportar{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
    margin-bottom:18px;
}

.tabla-contenedor{
    width:100%;
    overflow:visible;
}

table{
    width:100%;
    min-width:0;
    table-layout:fixed;
    border-collapse:collapse;
    background:white;
}

th,
td{
    padding:9px 6px;
    border-bottom:1px solid #e5e7eb;
    text-align:left;
    font-size:12px;
    line-height:1.35;
    vertical-align:middle;
    white-space:normal;
    overflow-wrap:anywhere;
    word-break:normal;
}

th{
    background:#0f766e;
    color:white;
    white-space:normal;
}

tr:hover{
    background:#f8fafc;
}


/* ANCHOS DE COLUMNAS - HISTORIAL */

th:nth-child(1),
td:nth-child(1){ width:5%; }

th:nth-child(2),
td:nth-child(2){ width:9%; }

th:nth-child(3),
td:nth-child(3){ width:8%; }

th:nth-child(4),
td:nth-child(4){ width:9%; }

th:nth-child(5),
td:nth-child(5){
    width:8%;
    text-align:center;
}

th:nth-child(6),
td:nth-child(6){ width:10%; }

th:nth-child(7),
td:nth-child(7){ width:10%; }

th:nth-child(8),
td:nth-child(8){ width:10%; }

th:nth-child(9),
td:nth-child(9){ width:9%; }

th:nth-child(10),
td:nth-child(10){ width:10%; }

th:nth-child(11),
td:nth-child(11){
    width:12%;
    text-align:center;
}

.estado-badge{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    padding:5px 10px;
    border-radius:999px;
    font-size:12px;
    font-weight:bold;
    white-space:nowrap;
    word-break:normal;
    overflow-wrap:normal;
}

.estado-borrador{
    background:#fef3c7;
    color:#92400e;
}

.estado-cerrada{
    background:#dcfce7;
    color:#166534;
}

.estado-anulada{
    background:#fee2e2;
    color:#991b1b;
}

.btn-mini{
    display:inline-block;
    text-decoration:none;
    font-size:12px;
    font-weight:bold;
    padding:7px 9px;
    border-radius:8px;
    color:white;
}

.sin-registros{
    text-align:center;
    padding:25px;
    color:#6b7280;
    background:#fff;
    border-radius:12px;
}

.no-disponible{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    padding:6px 9px;
    border-radius:8px;
    background:#e5e7eb;
    color:#6b7280;
    font-size:12px;
    font-weight:bold;
    white-space:nowrap;
    word-break:normal;
    overflow-wrap:normal;
}

.lista-empleados table{
    min-width:750px;
}

@media(max-width:1100px){
    .busqueda-form{
        grid-template-columns:1fr 1fr;
    }

    .busqueda-form .btn{
        width:100%;
        margin-top:0;
    }
}

@media(max-width:768px){
    .contenedor{
        width:98%;
        margin:10px auto;
    }

    .cabecera{
        padding:18px 15px;
        border-radius:15px;
    }

    .cabecera-top{
        flex-direction:column;
        align-items:stretch;
        text-align:center;
    }

    .cabecera h1{
        font-size:24px;
    }

    .acciones-superiores{
        flex-direction:column;
    }

    .acciones-superiores .btn{
        width:100%;
    }

    .panel{
        padding:15px;
        border-radius:15px;
    }

    .panel h2{
        text-align:center;
        font-size:19px;
    }

    .busqueda-form{
        grid-template-columns:1fr;
    }

    .busqueda-form > .btn{
        margin-top:0;
    }

    .campo input,
    .campo select{
        min-height:44px;
        font-size:16px;
    }

    .acciones-exportar{
        flex-direction:column;
    }

    .acciones-exportar .btn{
        width:100%;
    }

    /*
    =========================================
    TABLAS COMO TARJETAS - SIN SCROLL
    =========================================
    */

    .tabla-contenedor{
        overflow:visible;
    }

    table,
    thead,
    tbody,
    tr,
    th,
    td{
        display:block;
        width:100%;
    }

    table{
        table-layout:auto;
        min-width:0;
    }

    thead{
        display:none;
    }

    tbody{
        display:grid;
        gap:14px;
    }

    tbody tr{
        border:1px solid #e5e7eb;
        border-radius:14px;
        padding:12px;
        background:white;
        box-shadow:0 4px 12px rgba(0,0,0,.05);
    }

    tbody tr:hover{
        background:white;
    }

    td{
        display:grid;
        grid-template-columns:135px minmax(0,1fr);
        gap:10px;
        width:100% !important;
        padding:8px 0;
        border-bottom:1px solid #f1f5f9;
        font-size:13px;
        text-align:left !important;
        overflow-wrap:anywhere;
    }

    td:last-child{
        border-bottom:none;
    }

    td::before{
        font-weight:bold;
        color:#475569;
    }

    .lista-empleados td:nth-child(1)::before{ content:"Legajo"; }
    .lista-empleados td:nth-child(2)::before{ content:"Apellido y Nombre"; }
    .lista-empleados td:nth-child(3)::before{ content:"DNI"; }
    .lista-empleados td:nth-child(4)::before{ content:"CUIL"; }
    .lista-empleados td:nth-child(5)::before{ content:"Categoría"; }
    .lista-empleados td:nth-child(6)::before{ content:"Estado"; }
    .lista-empleados td:nth-child(7)::before{ content:"Acción"; }

    .panel:not(.lista-empleados) td:nth-child(1)::before{ content:"ID"; }
    .panel:not(.lista-empleados) td:nth-child(2)::before{ content:"Tipo"; }
    .panel:not(.lista-empleados) td:nth-child(3)::before{ content:"Período"; }
    .panel:not(.lista-empleados) td:nth-child(4)::before{ content:"Fecha Liquidación"; }
    .panel:not(.lista-empleados) td:nth-child(5)::before{ content:"Estado"; }
    .panel:not(.lista-empleados) td:nth-child(6)::before{ content:"Total Remunerativo"; }
    .panel:not(.lista-empleados) td:nth-child(7)::before{ content:"Total Descuentos"; }
    .panel:not(.lista-empleados) td:nth-child(8)::before{ content:"No Remunerativo"; }
    .panel:not(.lista-empleados) td:nth-child(9)::before{ content:"Asignaciones"; }
    .panel:not(.lista-empleados) td:nth-child(10)::before{ content:"Neto"; }
    .panel:not(.lista-empleados) td:nth-child(11)::before{ content:"Recibo"; }
}

</style>

</head>

<body>

<div class="contenedor">

    <div class="cabecera">

        <div class="cabecera-top">

            <div>
                <h1>Historial por Empleado</h1>
                <p>
                    Consulta de liquidaciones históricas
                    y recibos individuales por agente.
                </p>
            </div>

            <div class="acciones-superiores">

                <a
                    href="<?php
                    echo htmlspecialchars(
                        $historialUrlReportes,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                    class="btn btn-reportes"
                >
                    Volver a Reportes
                </a>

                <a
                    href="<?php
                    echo htmlspecialchars(
                        $historialUrlMenu,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                    class="btn btn-volver"
                >
                    Menú Principal
                </a>

            </div>

        </div>

    </div>


    <?php if (!empty($error)): ?>

        <div class="error">
            <?php
            echo htmlspecialchars(
                $error,
                ENT_QUOTES,
                'UTF-8'
            );
            ?>
        </div>

    <?php endif; ?>


    <?php if ($empleadoId <= 0): ?>

        <div class="panel">

            <h2>Buscar empleado</h2>

            <form
                method="GET"
                action="<?php
                    echo htmlspecialchars(
                        $historialUrlEntrada,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                class="busqueda-form"
            >

                <input
                    type="hidden"
                    name="r"
                    value="reportes/historial-empleado"
                >

                <input
                    type="hidden"
                    name="filtrar"
                    value="1"
                >

                <div class="campo">

                    <label for="legajo">
                        Legajo
                    </label>

                    <input
                        type="text"
                        id="legajo"
                        name="legajo"
                        inputmode="numeric"
                        autocomplete="off"
                        placeholder="Ej.: 11"
                        value="<?php
                            echo htmlspecialchars(
                                $legajo,
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                        oninput="this.value=this.value.replace(/[^0-9]/g,'');"
                    >

                    <div class="ayuda">
                        La búsqueda por legajo es exacta.
                    </div>

                </div>


                <div class="campo">

                    <label for="busqueda">
                        Búsqueda general
                    </label>

                    <input
                        type="text"
                        id="busqueda"
                        name="busqueda"
                        placeholder="Apellido, nombre, DNI o CUIL"
                        autocomplete="off"
                        value="<?php
                            echo htmlspecialchars(
                                $busqueda,
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                    >

                    <div class="ayuda">
                        Permite coincidencias parciales.
                    </div>

                </div>


                <div class="campo">

                    <label for="estado">
                        Estado
                    </label>

                    <select
                        name="estado"
                        id="estado"
                    >

                        <option value="">
                            Todos
                        </option>

                        <option
                            value="1"
                            <?php echo ($estado === '1') ? 'selected' : ''; ?>
                        >
                            Activos
                        </option>

                        <option
                            value="0"
                            <?php echo ($estado === '0') ? 'selected' : ''; ?>
                        >
                            Inactivos
                        </option>

                    </select>

                    <div class="ayuda">
                        Filtre por estado actual del empleado.
                    </div>

                </div>


                <button
                    type="submit"
                    class="btn btn-buscar"
                >
                    Buscar
                </button>


                <a
                    href="<?php
                        echo htmlspecialchars(
                            $historialUrlLimpiar,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                    class="btn btn-limpiar"
                >
                    Limpiar
                </a>

            </form>

        </div>


        <div class="panel lista-empleados">

            <h2>Resultados</h2>

            <?php if (!empty($empleados)): ?>

                <div class="tabla-contenedor">

                    <table>

                        <thead>
                            <tr>
                                <th>Legajo</th>
                                <th>Apellido y Nombre</th>
                                <th>DNI</th>
                                <th>CUIL</th>
                                <th>Categoría</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>

                        <tbody>

                        <?php foreach ($empleados as $fila): ?>

                            <tr>

                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        $fila['nro_legajo'] ?? '',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        ($fila['apellido'] ?? '')
                                        . ', '
                                        . ($fila['nombre'] ?? ''),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        $fila['dni'] ?? '',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        $fila['cuil'] ?? '',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        $fila['categoria'] ?? '-',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>
                                </td>

                                <td>

                                    <?php if (
                                        (int)($fila['activo'] ?? 0) === 1
                                    ): ?>

                                        <span class="estado-badge estado-cerrada">
                                            Activo
                                        </span>

                                    <?php else: ?>

                                        <span class="estado-badge estado-anulada">
                                            Inactivo
                                        </span>

                                    <?php endif; ?>

                                </td>

                                <td>

                                    <a
                                        href="<?php
                                            echo htmlspecialchars(
                                                sigenmuniUrlRuta(
                                                    'reportes/historial-empleado',
                                                    [
                                                        'empleado_id' =>
                                                            (int)$fila['id']
                                                    ]
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                        ?>"
                                        class="btn-mini btn-ver"
                                    >
                                        Ver Historial
                                    </a>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            <?php else: ?>

                <div class="sin-registros">

                    <?php if (!($filtrar ?? false)): ?>

                        Ingresá algún filtro para localizar un empleado
                        o seleccioná <strong>Todos</strong> y presioná Buscar.

                    <?php else: ?>

                        No se encontraron empleados
                        con los filtros seleccionados.

                    <?php endif; ?>

                </div>

            <?php endif; ?>

        </div>


    <?php else: ?>


        <?php if ($empleado): ?>

            <div class="panel">

                <h2>Datos del Empleado</h2>

                <div class="datos-empleado">

                    <div class="info-box">
                        <strong>Empleado</strong>
                        <?php
                        echo htmlspecialchars(
                            ($empleado['apellido'] ?? '')
                            . ', '
                            . ($empleado['nombre'] ?? ''),
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>
                    </div>

                    <div class="info-box">
                        <strong>Legajo</strong>
                        <?php
                        echo htmlspecialchars(
                            $empleado['nro_legajo'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>
                    </div>

                    <div class="info-box">
                        <strong>DNI</strong>
                        <?php
                        echo htmlspecialchars(
                            $empleado['dni'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>
                    </div>

                    <div class="info-box">
                        <strong>CUIL</strong>
                        <?php
                        echo htmlspecialchars(
                            $empleado['cuil'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>
                    </div>

                    <div class="info-box">
                        <strong>Categoría</strong>
                        <?php
                        echo htmlspecialchars(
                            $empleado['categoria'] ?? '-',
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>
                    </div>

                    <div class="info-box">
                        <strong>Unidad de Organización</strong>

                        <?php
                        echo htmlspecialchars(
                            $empleado['oficina'] ?? '-',
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>

                        <?php if (!empty($empleado['oficina_cuit'])): ?>

                            <div
                                style="
                                    margin-top:4px;
                                    font-size:12px;
                                    color:#64748b;
                                "
                            >
                                CUIT:
                                <?php
                                echo htmlspecialchars(
                                    formatearCuitUnidadOrganizacion(
                                        $empleado['oficina_cuit']
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>
                            </div>

                        <?php endif; ?>

                    </div>

                    <div class="info-box">
                        <strong>Situación</strong>
                        <?php
                        echo htmlspecialchars(
                            $empleado['situacion'] ?? '-',
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>
                    </div>

                    <div class="info-box">
                        <strong>Fecha Alta</strong>
                        <?php
                        echo !empty($empleado['fecha_alta'])
                            ? date(
                                "d/m/Y",
                                strtotime($empleado['fecha_alta'])
                            )
                            : '-';
                        ?>
                    </div>

                    <div class="info-box">
                        <strong>Fecha Inactivo</strong>
                        <?php
                        echo !empty($empleado['fecha_inactivo'])
                            ? date(
                                "d/m/Y",
                                strtotime($empleado['fecha_inactivo'])
                            )
                            : '-';
                        ?>
                    </div>

                    <div class="info-box">
                        <strong>Estado del Empleado</strong>

                        <?php if (
                            (int)($empleado['activo'] ?? 0) === 1
                        ): ?>

                            <span class="estado-badge estado-cerrada">
                                Activo
                            </span>

                        <?php else: ?>

                            <span class="estado-badge estado-anulada">
                                Inactivo
                            </span>

                        <?php endif; ?>

                    </div>

                </div>

            </div>


            <div class="panel">

                <h2>
                    Historial de Liquidaciones
                </h2>

                <div class="acciones-exportar">

                    <a
                        href="<?php
                            echo htmlspecialchars(
                                $historialUrlPdf,
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                        class="btn btn-pdf"
                    >
                        Exportar PDF
                    </a>

                    <a
                        href="<?php
                            echo htmlspecialchars(
                                $historialUrlExcel,
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                        class="btn btn-excel"
                    >
                        Exportar Excel
                    </a>

                </div>


                <?php if (!empty($historial)): ?>

                    <div class="tabla-contenedor">

                        <table>

                            <thead>

                                <tr>
                                    <th>ID</th>
                                    <th>Tipo</th>
                                    <th>Período</th>
                                    <th>Fecha Liquidación</th>
                                    <th>Estado</th>
                                    <th>Total Remunerativo</th>
                                    <th>Total Descuentos</th>
                                    <th>No Remunerativo</th>
                                    <th>Asignaciones</th>
                                    <th>Neto</th>
                                    <th>Recibo</th>
                                </tr>

                            </thead>

                            <tbody>

                            <?php foreach ($historial as $fila): ?>

                                <?php

                                $estadoLiquidacion =
                                    strtoupper(
                                        trim(
                                            $fila['estado'] ?? ''
                                        )
                                    );

                                switch ($estadoLiquidacion) {

                                    case 'CERRADA':
                                        $claseEstado = 'estado-cerrada';
                                        break;

                                    case 'ANULADA':
                                        $claseEstado = 'estado-anulada';
                                        break;

                                    default:
                                        $claseEstado = 'estado-borrador';
                                        break;
                                }

                                ?>

                                <tr>

                                    <td>
                                        <?php
                                        echo (int)(
                                            $fila['liquidacion_id'] ?? 0
                                        );
                                        ?>
                                    </td>

                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $fila['tipo_liquidacion'] ?? '',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        );
                                        ?>
                                    </td>

                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $fila['periodo'] ?? '',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        );
                                        ?>
                                    </td>

                                    <td>
                                        <?php
                                        echo !empty($fila['fecha_liquidacion'])
                                            ? date(
                                                "d/m/Y",
                                                strtotime(
                                                    $fila['fecha_liquidacion']
                                                )
                                            )
                                            : '-';
                                        ?>
                                    </td>

                                    <td>

                                        <span
                                            class="estado-badge <?php
                                                echo $claseEstado;
                                            ?>"
                                        >
                                            <?php
                                            echo htmlspecialchars(
                                                $estadoLiquidacion,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                            ?>
                                        </span>

                                    </td>

                                    <td>
                                        $
                                        <?php
                                        echo number_format(
                                            (float)(
                                                $fila['total_remunerativo']
                                                ?? 0
                                            ),
                                            2,
                                            ',',
                                            '.'
                                        );
                                        ?>
                                    </td>

                                    <td>
                                        $
                                        <?php
                                        echo number_format(
                                            (float)(
                                                $fila['total_descuentos']
                                                ?? 0
                                            ),
                                            2,
                                            ',',
                                            '.'
                                        );
                                        ?>
                                    </td>

                                    <td>
                                        $
                                        <?php
                                        echo number_format(
                                            (float)(
                                                $fila['total_no_remunerativo']
                                                ?? 0
                                            ),
                                            2,
                                            ',',
                                            '.'
                                        );
                                        ?>
                                    </td>

                                    <td>
                                        $
                                        <?php
                                        echo number_format(
                                            (float)(
                                                $fila['total_asignaciones']
                                                ?? 0
                                            ),
                                            2,
                                            ',',
                                            '.'
                                        );
                                        ?>
                                    </td>

                                    <td>
                                        <strong>
                                            $
                                            <?php
                                            echo number_format(
                                                (float)(
                                                    $fila['neto']
                                                    ?? 0
                                                ),
                                                2,
                                                ',',
                                                '.'
                                            );
                                            ?>
                                        </strong>
                                    </td>

                                    <td>

                                        <?php if (
                                            $estadoLiquidacion === 'CERRADA'
                                        ): ?>

                                            <a
                                                href="<?php
                                                    echo htmlspecialchars(
                                                        sigenmuniUrlRuta(
                                                            'liquidacion/recibo',
                                                            [
                                                                'liquidacion_id' =>
                                                                    (int)$fila[
                                                                        'liquidacion_id'
                                                                    ],

                                                                'empleado_id' =>
                                                                    (int)$empleado['id'],

                                                                'origen' =>
                                                                    'historial'
                                                            ]
                                                        ),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    );
                                                ?>"
                                                class="btn-mini btn-recibo"
                                            >
                                                Ver Recibo
                                            </a>

                                        <?php else: ?>

                                            <span class="no-disponible">
                                                No disponible
                                            </span>

                                        <?php endif; ?>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>

                <?php else: ?>

                    <div class="sin-registros">
                        Este empleado todavía no tiene
                        liquidaciones registradas.
                    </div>

                <?php endif; ?>

            </div>


        <?php else: ?>

            <div class="panel">

                <div class="sin-registros">
                    No se encontró el empleado solicitado.
                </div>

            </div>

        <?php endif; ?>


    <?php endif; ?>

</div>

</body>

</html>