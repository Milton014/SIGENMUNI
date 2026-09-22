<?php

/*
|--------------------------------------------------------------------------
| GESTIÓN DE CONCEPTOS - SOLO ROUTER
|--------------------------------------------------------------------------
|
| Todas las operaciones de Gestión de Conceptos y Valores de Conceptos
| ingresan mediante public/index.php y las rutas registradas del módulo.
|
| "conceptos.php" continúa existiendo únicamente como clave de permiso;
| ya no se utiliza como archivo de navegación.
|
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../../../core/Url.php';
require_once __DIR__ . '/../../../core/Csrf.php';


$conceptosUrlListado =
    sigenmuniUrlRuta(
        'conceptos'
    );


$conceptosAccionBuscar =
    sigenmuniUrlEntrada();


$conceptosUrlNuevo =
    sigenmuniUrlRuta(
        'conceptos/nuevo'
    );


$conceptosUrlValores =
    sigenmuniUrlRuta(
        'conceptos/valores'
    );


$conceptosUrlEstado =
    sigenmuniUrlRuta(
        'conceptos/estado'
    );


$conceptosCsrfToken =
    sigenmuniCsrfToken();


$conceptosUrlMenu =
    sigenmuniUrlRuta(
    'inicio'
);

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Gestión de Conceptos - SIGENMUNI</title>

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
   CONTENEDOR
========================================= */

.contenedor{
    width:97%;
    max-width:1500px;
    margin:30px auto;
}

.panel{
    background:white;

    padding:24px;

    border-radius:18px;

    box-shadow:
        0 8px 20px rgba(0,0,0,0.08);
}


/* =========================================
   TOPBAR
========================================= */

.topbar{
    display:flex;
    justify-content:space-between;
    align-items:center;

    gap:15px;

    flex-wrap:wrap;

    margin-bottom:25px;
}

.topbar h2{
    margin:0;

    color:#0f766e;

    font-size:30px;
}

.acciones-superiores{
    display:flex;

    gap:8px;

    flex-wrap:wrap;
}


/* =========================================
   BOTONES GENERALES
========================================= */

.btn{
    display:inline-block;

    padding:11px 14px;

    border:none;

    border-radius:10px;

    text-decoration:none;

    color:white;

    cursor:pointer;

    font-size:14px;

    font-weight:bold;

    transition:0.2s;

    text-align:center;
}

.btn:hover{
    opacity:0.90;

    transform:translateY(-1px);
}

.btn-nuevo{
    background:#0f766e;
}

.btn-valores{
    background:#0891b2;
}

.btn-volver{
    background:#1f2937;
}

.btn-buscar{
    background:#2563eb;
}

.btn-limpiar{
    background:#475569;
}


/* =========================================
   MENSAJES
========================================= */

.mensaje{
    padding:14px 16px;

    border-radius:10px;

    margin-bottom:20px;

    font-weight:bold;

    font-size:14px;
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
   FILTROS
========================================= */

.filtros{
    background:#f8fafc;

    padding:18px;

    border-radius:14px;

    border:1px solid #e5e7eb;

    display:grid;

    grid-template-columns:
        minmax(230px, 2fr)
        minmax(190px, 1.3fr)
        minmax(160px, 1fr)
        auto
        auto;

    gap:12px;

    margin-bottom:24px;
}

.filtros input,
.filtros select{
    width:100%;

    padding:12px;

    border:1px solid #cbd5e1;

    border-radius:10px;

    font-size:14px;

    outline:none;

    background:white;
}

.filtros input:focus,
.filtros select:focus{
    border-color:#14b8a6;

    box-shadow:
        0 0 0 3px rgba(20,184,166,0.15);
}


/* =========================================
   TABLA
========================================= */

.tabla-contenedor{
    width:100%;
    overflow:visible;
}

table{
    width:100%;

    border-collapse:collapse;

    table-layout:auto;

    background:white;
}

th,
td{
    padding:9px 6px;

    border-bottom:1px solid #e5e7eb;

    text-align:left;

    vertical-align:middle;

    font-size:11px;
}

th{
    background:#0f766e;

    color:white;

    font-size:11px;

    white-space:nowrap;
}

thead th:first-child{
    border-radius:10px 0 0 0;
}

thead th:last-child{
    border-radius:0 10px 0 0;
}

tbody tr{
    transition:background 0.15s;
}

tbody tr:hover{
    background:#f8fafc;
}


/* =========================================
   COLUMNAS
========================================= */

.col-id,
.col-codigo,
.col-sac,
.col-visible,
.col-estado,
.col-vigencia,
.col-acciones{
    white-space:nowrap;
}

.col-nombre{
    min-width:120px;
}

.col-base{
    max-width:170px;

    word-break:break-word;
}

.col-acciones{
    width:1%;
}


/* =========================================
   BADGES DE TIPO DE CONCEPTO
========================================= */

.badge{
    display:inline-block;

    padding:5px 8px;

    border-radius:20px;

    font-size:10px;

    font-weight:bold;

    color:white;

    white-space:nowrap;
}

.badge-rem{
    background:#2563eb;
}

.badge-no-rem{
    background:#7c3aed;
}

.badge-asig{
    background:#059669;
}

.badge-desc{
    background:#dc2626;
}

.badge-aporte{
    background:#ea580c;
}


/* =========================================
   ESTADOS
========================================= */

.estado-activo{
    color:#166534;

    font-weight:bold;
}

.estado-inactivo{
    color:#991b1b;

    font-weight:bold;
}


/* =========================================
   ACCIONES
========================================= */

.acciones{
    display:flex;

    gap:5px;

    flex-wrap:wrap;

    align-items:center;
}

.form-accion{
    margin:0;
    padding:0;
}

.acciones .btn-accion{
    text-decoration:none;

    color:white;

    border:none;

    cursor:pointer;

    font-family:inherit;

    font-size:10px;

    font-weight:bold;

    border-radius:7px;

    padding:7px 8px;

    transition:0.2s;

    white-space:nowrap;
}

.acciones .btn-accion:hover{
    opacity:0.86;

    transform:translateY(-1px);
}

.btn-editar{
    background:#d97706;
}

.btn-inactivar{
    background:#dc2626;
}

.btn-activar{
    background:#16a34a;
}


/* =========================================
   SIN RESULTADOS
========================================= */

.sin-resultados{
    text-align:center;

    padding:25px;

    color:#64748b;

    font-weight:bold;
}


/* =========================================
   PANTALLAS MEDIANAS
========================================= */

@media (max-width:1200px){

    .contenedor{
        width:98%;
    }

    .panel{
        padding:18px;
    }

    th,
    td{
        padding:7px 4px;

        font-size:10px;
    }

    th{
        font-size:10px;
    }

    .badge{
        font-size:9px;
        padding:4px 6px;
    }

    .acciones .btn-accion{
        font-size:9px;
        padding:6px;
    }
}


/* =========================================
   TABLET
========================================= */

@media (max-width:992px){

    .filtros{
        grid-template-columns:1fr 1fr;
    }

    .filtros .btn{
        width:100%;
    }
}


/* =========================================
   CELULAR
========================================= */

@media (max-width:768px){

    .contenedor{
        width:94%;

        margin:20px auto;
    }

    .panel{
        padding:18px;

        border-radius:16px;
    }

    .topbar{
        flex-direction:column;

        align-items:stretch;
    }

    .topbar h2{
        text-align:center;

        font-size:24px;
    }

    .acciones-superiores{
        flex-direction:column;
    }

    .acciones-superiores .btn{
        width:100%;
    }

    .filtros{
        grid-template-columns:1fr;

        padding:16px;
    }

    .filtros input,
    .filtros select,
    .filtros .btn{
        width:100%;

        min-height:44px;
    }


    /*
    |--------------------------------------------------------------------------
    | TABLA COMO TARJETAS
    |--------------------------------------------------------------------------
    */

    table,
    thead,
    tbody,
    tr,
    th,
    td{
        display:block;

        width:100%;
    }

    thead{
        display:none;
    }

    tbody{
        display:grid;

        gap:16px;
    }

    tbody tr{
        background:white;

        border:1px solid #e5e7eb;

        border-radius:14px;

        padding:14px;

        box-shadow:
            0 4px 12px rgba(0,0,0,0.05);
    }

    tbody tr:hover{
        background:white;
    }

    td{
        display:grid;

        grid-template-columns:145px 1fr;

        gap:10px;

        padding:8px 0;

        border-bottom:1px solid #f1f5f9;

        font-size:13px;

        white-space:normal;
    }

    td:last-child{
        border-bottom:none;
    }

    td::before{
        content:attr(data-label);

        font-weight:bold;

        color:#475569;
    }

    .col-codigo,
    .col-nombre,
    .col-base,
    .col-sac,
    .col-visible,
    .col-estado,
    .col-vigencia,
    .col-acciones{
        white-space:normal;

        max-width:none;

        width:auto;
    }

    .acciones{
        display:flex;

        flex-direction:column;

        gap:7px;

        width:100%;
    }

    .acciones .btn-accion{
        width:100%;

        text-align:center;

        font-size:12px;

        padding:9px;
    }
}


/* =========================================
   CELULARES PEQUEÑOS
========================================= */

@media (max-width:480px){

    .topbar h2{
        font-size:22px;
    }

    td{
        grid-template-columns:115px 1fr;

        font-size:12px;
    }
}

</style>

</head>


<body>


<!-- =========================================
     HEADER GLOBAL
========================================= -->

<?php

$sigenmuniHeaderSubtitulo =
    'Gestión de Conceptos de Liquidación';

require __DIR__
    . '/../../../componentes/header_sigenmuni.php';

?>


<!-- =========================================
     CONTENIDO
========================================= -->

<div class="contenedor">

    <div class="panel">


        <!-- =====================================
             TOPBAR
        ====================================== -->

        <div class="topbar">

            <h2>
                Gestión de Conceptos
            </h2>

            <div class="acciones-superiores">

                <a
                    href="<?php echo htmlspecialchars($conceptosUrlNuevo, ENT_QUOTES, 'UTF-8'); ?>"
                    class="btn btn-nuevo"
                >
                    + Nuevo Concepto
                </a>

                <a
                    href="<?php echo htmlspecialchars($conceptosUrlValores, ENT_QUOTES, 'UTF-8'); ?>"
                    class="btn btn-valores"
                >
                    📋 Ver todos los valores
                </a>

                <a
                    href="<?php echo htmlspecialchars($conceptosUrlMenu, ENT_QUOTES, 'UTF-8'); ?>"
                    class="btn btn-volver"
                >
                    Volver al menú
                </a>

            </div>

        </div>


        <!-- =====================================
             MENSAJES
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
             FILTROS
        ====================================== -->

        <form
            method="GET"
            action="<?php echo htmlspecialchars($conceptosAccionBuscar, ENT_QUOTES, 'UTF-8'); ?>"
            class="filtros"
        >

            <input
                type="hidden"
                name="r"
                value="conceptos"
            >

            <!-- BUSCAR -->

            <input
                type="text"
                name="buscar"
                placeholder="Buscar por código o nombre"
                value="<?php
                    echo htmlspecialchars(
                        $buscar ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
            >


            <!-- TIPO DE CONCEPTO -->

            <select name="categoria">

                <option value="">
                    -- Todos los tipos de concepto --
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
                        echo ($categoria === $cat)
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


            <!-- ESTADO -->

            <select name="activo">

                <option value="">
                    -- Todos los estados --
                </option>

                <option
                    value="1"
                    <?php
                    echo ($activo === '1')
                        ? 'selected'
                        : '';
                    ?>
                >
                    Activos
                </option>

                <option
                    value="0"
                    <?php
                    echo ($activo === '0')
                        ? 'selected'
                        : '';
                    ?>
                >
                    Inactivos
                </option>

            </select>


            <!-- BUSCAR -->

            <button
                type="submit"
                class="btn btn-buscar"
            >
                Buscar
            </button>


            <!-- LIMPIAR -->

            <a
                href="<?php echo htmlspecialchars($conceptosUrlListado, ENT_QUOTES, 'UTF-8'); ?>"
                class="btn btn-limpiar"
            >
                Limpiar
            </a>

        </form>


        <!-- =====================================
             TABLA
        ====================================== -->

        <?php if (!empty($conceptos)): ?>

            <div class="tabla-contenedor">

                <table>

                    <thead>

                        <tr>

                            <th>Código</th>

                            <th>Nombre</th>

                            <th>Tipo de Concepto</th>

                            <th>Forma de Cálculo</th>

                            <th>Base de Cálculo</th>

                            <th>SAC</th>

                            <th>Recibo</th>

                            <th>Estado</th>

                            <th>Vigencia</th>

                            <th>Acciones</th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php foreach ($conceptos as $fila): ?>

                        <?php

                        /*
                        |--------------------------------------------------------------------------
                        | BADGE SEGÚN TIPO DE CONCEPTO
                        |--------------------------------------------------------------------------
                        */

                        $badgeClass = '';

                        switch ($fila['categoria']) {

                            case 'REMUNERATIVO':
                                $badgeClass = 'badge-rem';
                                break;

                            case 'NO_REMUNERATIVO':
                                $badgeClass = 'badge-no-rem';
                                break;

                            case 'ASIGNACION_FAMILIAR':
                                $badgeClass = 'badge-asig';
                                break;

                            case 'DESCUENTO':
                                $badgeClass = 'badge-desc';
                                break;

                            case 'APORTE_PATRONAL':
                                $badgeClass = 'badge-aporte';
                                break;
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | VIGENCIA
                        |--------------------------------------------------------------------------
                        */

                        $fechaDesde = '-';

                        $fechaHasta = '-';

                        if (!empty($fila['fecha_desde'])) {

                            $fechaDesde = date(
                                'd/m/Y',
                                strtotime(
                                    $fila['fecha_desde']
                                )
                            );
                        }

                        if (!empty($fila['fecha_hasta'])) {

                            $fechaHasta = date(
                                'd/m/Y',
                                strtotime(
                                    $fila['fecha_hasta']
                                )
                            );
                        }

                        ?>


                        <tr>


                            <!-- CÓDIGO -->

                            <td
                                class="col-codigo"
                                data-label="Código"
                            >
                                <?php
                                echo htmlspecialchars(
                                    $fila['codigo'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>
                            </td>


                            <!-- NOMBRE -->

                            <td
                                class="col-nombre"
                                data-label="Nombre"
                            >
                                <?php
                                echo htmlspecialchars(
                                    $fila['nombre'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>
                            </td>


                            <!-- TIPO DE CONCEPTO -->

                            <td data-label="Tipo de Concepto">

                                <span
                                    class="badge <?php echo $badgeClass; ?>"
                                >

                                    <?php
                                    echo htmlspecialchars(
                                        $fila['categoria'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>

                                </span>

                            </td>


                            <!-- FORMA CÁLCULO -->

                            <td data-label="Forma de Cálculo">

                                <?php

                                $formaCalculoInterna =
                                    strtoupper(
                                        trim(
                                            (string)(
                                                $fila['forma_calculo']
                                                ?? ''
                                            )
                                        )
                                    );

                                switch ($formaCalculoInterna) {

                                    case 'FORMULA':
                                        $formaCalculoVisual =
                                            'AUTOMÁTICO';
                                        break;

                                    case 'TABLA_CATEGORIA':
                                        $formaCalculoVisual =
                                            'VALOR POR CATEGORÍA';
                                        break;

                                    case 'MANUAL':
                                        $formaCalculoVisual =
                                            'MANUAL';
                                        break;

                                    case 'PORCENTAJE':
                                        $formaCalculoVisual =
                                            'PORCENTAJE';
                                        break;

                                    case 'FIJO':
                                        /*
                                        | Compatibilidad visual con conceptos
                                        | históricos anteriores a la nueva
                                        | arquitectura.
                                        */
                                        $formaCalculoVisual =
                                            'MANUAL';
                                        break;

                                    default:
                                        $formaCalculoVisual =
                                            $formaCalculoInterna !== ''
                                                ? $formaCalculoInterna
                                                : '-';
                                        break;
                                }

                                echo htmlspecialchars(
                                    $formaCalculoVisual,
                                    ENT_QUOTES,
                                    'UTF-8'
                                );

                                ?>

                            </td>


                            <!-- BASE DE CÁLCULO -->

                            <td
                                class="col-base"
                                data-label="Base de Cálculo"
                            >

                                <?php

                                $baseCalculoInterna =
                                    strtoupper(
                                        trim(
                                            (string)(
                                                $fila['base_calculo']
                                                ?? ''
                                            )
                                        )
                                    );


                                switch ($baseCalculoInterna) {

                                    case 'BASICO':
                                        $baseCalculoVisual =
                                            'Sueldo Básico';
                                        break;

                                    case 'BASICO_MAS_DEDICACION':
                                        $baseCalculoVisual =
                                            'Básico + Dedicación';
                                        break;

                                    case 'TOTAL_REMUNERATIVO':
                                        $baseCalculoVisual =
                                            'Total Remunerativo';
                                        break;

                                    case 'TOTAL_REMUNERATIVO_MENOS_DESCUENTOS_OBLIGATORIOS':
                                        $baseCalculoVisual =
                                            'TR menos Desc. Obligatorios';
                                        break;

                                    default:
                                        $baseCalculoVisual =
                                            '-';
                                        break;
                                }


                                echo htmlspecialchars(
                                    $baseCalculoVisual,
                                    ENT_QUOTES,
                                    'UTF-8'
                                );

                                ?>

                            </td>


                            <!-- SAC -->

                            <td
                                class="col-sac"
                                data-label="SAC"
                            >

                                <?php
                                echo (
                                    (int)$fila['aplica_sac'] === 1
                                )
                                    ? 'Sí'
                                    : 'No';
                                ?>

                            </td>


                            <!-- VISIBLE RECIBO -->

                            <td
                                class="col-visible"
                                data-label="Visible Recibo"
                            >

                                <?php
                                echo (
                                    (int)$fila['visible_recibo'] === 1
                                )
                                    ? 'Sí'
                                    : 'No';
                                ?>

                            </td>


                            <!-- ESTADO -->

                            <td
                                class="col-estado"
                                data-label="Estado"
                            >

                                <?php if ((int)$fila['activo'] === 1): ?>

                                    <span class="estado-activo">
                                        Activo
                                    </span>

                                <?php else: ?>

                                    <span class="estado-inactivo">
                                        Inactivo
                                    </span>

                                <?php endif; ?>

                            </td>


                            <!-- VIGENCIA -->

                            <td
                                class="col-vigencia"
                                data-label="Vigencia"
                            >

                                <?php
                                echo htmlspecialchars(
                                    $fechaDesde
                                    . ' / '
                                    . $fechaHasta,
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </td>


                            <!-- ACCIONES -->

                            <td
                                class="col-acciones"
                                data-label="Acciones"
                            >

                                <div class="acciones">


                                    <!-- EDITAR -->

                                    <a
                                        href="<?php
                                            echo htmlspecialchars(
                                                sigenmuniUrlRuta(
                                                    'conceptos/editar',
                                                    [
                                                        'id' => (int)$fila['id']
                                                    ]
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                        ?>"
                                        class="btn-accion btn-editar"
                                    >
                                        Editar
                                    </a>


                                    <!-- ACTIVAR / INACTIVAR -->

                                    <form
                                        method="POST"
                                        action="<?php
                                            echo htmlspecialchars(
                                                $conceptosUrlEstado,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                        ?>"
                                        class="form-accion"
                                        onsubmit="return confirm('<?php
                                            echo (
                                                (int)$fila['activo'] === 1
                                            )
                                                ? '¿Seguro que desea inactivar este concepto?'
                                                : '¿Seguro que desea activar este concepto?';
                                        ?>');"
                                    >

                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?php echo (int)$fila['id']; ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="estado"
                                            value="<?php
                                                echo (
                                                    (int)$fila['activo'] === 1
                                                )
                                                    ? 0
                                                    : 1;
                                            ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="_csrf"
                                            value="<?php
                                                echo htmlspecialchars(
                                                    $conceptosCsrfToken,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                );
                                            ?>"
                                        >

                                        <button
                                            type="submit"
                                            class="btn-accion <?php
                                                echo (
                                                    (int)$fila['activo'] === 1
                                                )
                                                    ? 'btn-inactivar'
                                                    : 'btn-activar';
                                            ?>"
                                        >
                                            <?php
                                            echo (
                                                (int)$fila['activo'] === 1
                                            )
                                                ? 'Inactivar'
                                                : 'Activar';
                                            ?>
                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>


        <?php else: ?>

            <div class="sin-resultados">

                No se encontraron conceptos.

            </div>

        <?php endif; ?>


    </div>

</div>


</body>

</html>