<?php

/*
|--------------------------------------------------------------------------
| VALORES DE CONCEPTOS - SOLO ROUTER
|--------------------------------------------------------------------------
|
| El listado y todas las operaciones de Valores de Conceptos utilizan
| únicamente las rutas registradas del módulo.
|
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../../../core/Url.php';
require_once __DIR__ . '/../../../core/Csrf.php';


$conceptoValoresUrlTodos =
    sigenmuniUrlRuta(
        'conceptos/valores'
    );


$conceptoValoresUrlConceptos =
    sigenmuniUrlRuta(
        'conceptos'
    );


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
            (int)($conceptoId ?? 0) > 0
                ? 'filtrado'
                : 'todos'
        );


$conceptoValoresParametrosNuevo = [
    'retorno' => $retornoValores
];

if (
    isset($conceptoId)
    &&
    (int)$conceptoId > 0
) {
    $conceptoValoresParametrosNuevo['concepto_id'] =
        (int)$conceptoId;
}


$conceptoValoresUrlNuevo =
    sigenmuniUrlRuta(
        'conceptos/valores/nuevo',
        $conceptoValoresParametrosNuevo
    );


$conceptoValoresUrlEstado =
    sigenmuniUrlRuta(
        'conceptos/valores/estado'
    );


$conceptoValoresCsrf =
    sigenmuniCsrfToken();

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Valores de Conceptos - SIGENMUNI</title>

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
    width:97%;
    max-width:1400px;
    margin:30px auto;
}

.panel{
    background:white;
    padding:24px;
    border-radius:18px;
    box-shadow:0 8px 20px rgba(0,0,0,.08);
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
    margin-bottom:22px;
}

.titulo-seccion h2{
    margin:0;
    color:#0f766e;
    font-size:28px;
}

.titulo-seccion p{
    margin:6px 0 0;
    color:#64748b;
    font-size:14px;
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

.btn-nuevo{
    background:#0f766e;
}

.btn-todos{
    background:#0891b2;
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
}

th,
td{
    padding:10px 7px;
    border-bottom:1px solid #e5e7eb;
    text-align:center;
    vertical-align:middle;
    font-size:12px;
}

th{
    background:#0f766e;
    color:white;
    white-space:nowrap;
    font-size:12px;
}

thead th:first-child{
    border-radius:10px 0 0 0;
}

thead th:last-child{
    border-radius:0 10px 0 0;
}

tbody tr{
    transition:background .15s;
}

tbody tr:hover{
    background:#f8fafc;
}


/* =========================================
   COLUMNAS
========================================= */

.col-id,
.col-codigo,
.col-monto,
.col-porcentaje,
.col-fecha,
.col-estado,
.col-acciones{
    white-space:nowrap;
}

.col-concepto{
    min-width:150px;
    text-align:left;
}

.col-categoria,
.col-escalafon{
    min-width:90px;
}


/* =========================================
   ESTADO
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

.acciones-tabla{
    display:flex;
    justify-content:center;
    gap:6px;
    flex-wrap:wrap;
}

.form-accion-valor{
    margin:0;
    padding:0;
}

.btn-accion{
    text-decoration:none;
    color:white;
    padding:7px 9px;
    border-radius:7px;
    font-size:11px;
    font-weight:bold;
    white-space:nowrap;
    transition:.2s;
    border:none;
    cursor:pointer;
    font-family:inherit;
}

.btn-accion:hover{
    opacity:.86;
    transform:translateY(-1px);
}

.btn-editar{
    background:#2563eb;
}

.btn-inactivar{
    background:#dc2626;
}

.btn-activar{
    background:#16a34a;
}


/* =========================================
   SIN DATOS
========================================= */

.sin-datos{
    text-align:center;
    padding:25px;
    color:#64748b;
    font-weight:bold;
}


/* =========================================
   PANTALLAS MEDIANAS
========================================= */

@media (max-width:1100px){

    .contenedor{
        width:98%;
    }

    .panel{
        padding:18px;
    }

    th,
    td{
        padding:8px 5px;
        font-size:11px;
    }

    .btn-accion{
        font-size:10px;
        padding:6px 7px;
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
        padding:18px;
        border-radius:16px;
    }

    .topbar{
        flex-direction:column;
        align-items:stretch;
    }

    .titulo-seccion h2{
        text-align:center;
        font-size:24px;
    }

    .titulo-seccion p{
        text-align:center;
    }

    .acciones-superiores{
        flex-direction:column;
    }

    .acciones-superiores .btn{
        width:100%;
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
        box-shadow:0 4px 12px rgba(0,0,0,.05);
    }

    tbody tr:hover{
        background:white;
    }

    td{
        display:grid;
        grid-template-columns:130px 1fr;
        gap:10px;
        padding:8px 0;
        border-bottom:1px solid #f1f5f9;
        text-align:left;
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

    .col-id,
    .col-codigo,
    .col-concepto,
    .col-categoria,
    .col-escalafon,
    .col-monto,
    .col-porcentaje,
    .col-fecha,
    .col-estado,
    .col-acciones{
        white-space:normal;
        min-width:0;
        width:auto;
        text-align:left;
    }

    .acciones-tabla{
        flex-direction:column;
        width:100%;
    }

    .btn-accion{
        width:100%;
        text-align:center;
        padding:9px;
        font-size:12px;
    }
}


/* =========================================
   CELULARES PEQUEÑOS
========================================= */

@media (max-width:480px){

    .header h1{
        font-size:22px;
    }

    .titulo-seccion h2{
        font-size:22px;
    }

    td{
        grid-template-columns:110px 1fr;
        font-size:12px;
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
        Gestión de Valores de Conceptos
    </p>

</div>


<!-- =========================================
     CONTENIDO
========================================= -->

<div class="contenedor">

    <div class="panel">


        <!-- =====================================
             TOPBAR
        ====================================== -->

        <div class="topbar">

            <div class="titulo-seccion">

                <h2>
                    Valores de Conceptos
                </h2>

                <?php if (!empty($nombreConcepto)): ?>

                    <p>
                        Concepto:
                        <strong>
                            <?php
                            echo htmlspecialchars(
                                $nombreConcepto,
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>
                        </strong>
                    </p>

                <?php else: ?>

                    <p>
                        Todos los valores registrados
                    </p>

                <?php endif; ?>

            </div>


            <div class="acciones-superiores">


                <!-- NUEVO VALOR -->

                <a
                    href="<?php
                        echo htmlspecialchars(
                            $conceptoValoresUrlNuevo,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                    class="btn btn-nuevo"
                >
                    + Nuevo Valor
                </a>


                <!-- VER TODOS -->

                <?php if ($conceptoId > 0): ?>

                    <a
                        href="<?php
                            echo htmlspecialchars(
                                $conceptoValoresUrlTodos,
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                        class="btn btn-todos"
                    >
                        Ver Todos
                    </a>

                <?php endif; ?>


                <!-- VOLVER -->

                <a
                    href="<?php
                        echo htmlspecialchars(
                            $conceptoValoresUrlConceptos,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                    class="btn btn-volver"
                >
                    Volver a Conceptos
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
             TABLA
        ====================================== -->

        <?php if (!empty($valores)): ?>

            <div class="tabla-contenedor">

                <table>

                    <thead>

                        <tr>

                            <th>ID</th>

                            <th>Código</th>

                            <th>Concepto</th>

                            <th>Categoría</th>

                            <th>Escalafón</th>

                            <th>Monto</th>

                            <th>Porcentaje</th>

                            <th>Fecha Desde</th>

                            <th>Fecha Hasta</th>

                            <th>Estado</th>

                            <th>Acciones</th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php foreach ($valores as $fila): ?>

                        <tr>


                            <!-- ID -->

                            <td
                                class="col-id"
                                data-label="ID"
                            >

                                <?php
                                echo (int)$fila['id'];
                                ?>

                            </td>


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


                            <!-- CONCEPTO -->

                            <td
                                class="col-concepto"
                                data-label="Concepto"
                            >

                                <?php
                                echo htmlspecialchars(
                                    $fila['concepto'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </td>


                            <!-- CATEGORÍA -->

                            <td
                                class="col-categoria"
                                data-label="Categoría"
                            >

                                <?php
                                echo htmlspecialchars(
                                    !empty($fila['categoria'])
                                        ? $fila['categoria']
                                        : '-',
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </td>


                            <!-- ESCALAFÓN -->

                            <td
                                class="col-escalafon"
                                data-label="Escalafón"
                            >

                                <?php
                                echo htmlspecialchars(
                                    !empty($fila['escalafon'])
                                        ? $fila['escalafon']
                                        : '-',
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </td>


                            <!-- MONTO -->

                            <td
                                class="col-monto"
                                data-label="Monto"
                            >

                                $
                                <?php
                                echo number_format(
                                    (float)$fila['monto'],
                                    2,
                                    ',',
                                    '.'
                                );
                                ?>

                            </td>


                            <!-- PORCENTAJE -->

                            <td
                                class="col-porcentaje"
                                data-label="Porcentaje"
                            >

                                <?php
                                echo number_format(
                                    (float)$fila['porcentaje'],
                                    2,
                                    ',',
                                    '.'
                                );
                                ?>%

                            </td>


                            <!-- FECHA DESDE -->

                            <td
                                class="col-fecha"
                                data-label="Fecha Desde"
                            >

                                <?php

                                if (!empty($fila['fecha_desde'])) {

                                    echo htmlspecialchars(
                                        date(
                                            'd/m/Y',
                                            strtotime(
                                                $fila['fecha_desde']
                                            )
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );

                                } else {

                                    echo '-';
                                }

                                ?>

                            </td>


                            <!-- FECHA HASTA -->

                            <td
                                class="col-fecha"
                                data-label="Fecha Hasta"
                            >

                                <?php

                                if (!empty($fila['fecha_hasta'])) {

                                    echo htmlspecialchars(
                                        date(
                                            'd/m/Y',
                                            strtotime(
                                                $fila['fecha_hasta']
                                            )
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );

                                } else {

                                    echo '-';
                                }

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


                            <!-- ACCIONES -->

                            <td
                                class="col-acciones"
                                data-label="Acciones"
                            >

                                <div class="acciones-tabla">


                                    <!-- EDITAR -->

                                    <a
                                        href="<?php
                                            echo htmlspecialchars(
                                                sigenmuniUrlRuta(
                                                    'conceptos/valores/editar',
                                                    array_filter(
                                                        [
                                                            'id' => (int)$fila['id'],
                                                            'retorno' => $retornoValores,
                                                            'concepto_id' =>
                                                                $retornoValores === 'filtrado'
                                                                    ? (int)$conceptoId
                                                                    : null
                                                        ],
                                                        static function ($valor) {
                                                            return $valor !== null;
                                                        }
                                                    )
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
                                                $conceptoValoresUrlEstado,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                        ?>"
                                        class="form-accion-valor"
                                        onsubmit="return confirm('<?php
                                            echo (int)$fila['activo'] === 1
                                                ? '¿Seguro que desea inactivar este valor?'
                                                : '¿Seguro que desea activar este valor?';
                                        ?>');"
                                    >

                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?php echo (int)$fila['id']; ?>"
                                        >

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

                                        <input
                                            type="hidden"
                                            name="concepto_id_contexto"
                                            value="<?php
                                                echo $retornoValores === 'filtrado'
                                                    ? (int)$conceptoId
                                                    : 0;
                                            ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="_csrf"
                                            value="<?php
                                                echo htmlspecialchars(
                                                    $conceptoValoresCsrf,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                );
                                            ?>"
                                        >

                                        <button
                                            type="submit"
                                            class="btn-accion <?php
                                                echo (int)$fila['activo'] === 1
                                                    ? 'btn-inactivar'
                                                    : 'btn-activar';
                                            ?>"
                                        >
                                            <?php
                                            echo (int)$fila['activo'] === 1
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

            <div class="sin-datos">

                No hay valores cargados.

            </div>

        <?php endif; ?>


    </div>

</div>


</body>

</html>