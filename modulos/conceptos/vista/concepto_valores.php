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

<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/conceptos/concepto_valores.css'), ENT_QUOTES, 'UTF-8') ?>">

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
                                        data-event-submit="concepto_valores-1" data-event-value="<?php
                                            echo (int)$fila['activo'] === 1
                                                ? '¿Seguro que desea inactivar este valor?'
                                                : '¿Seguro que desea activar este valor?';
                                        ?>"
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


<script src="<?= htmlspecialchars(sigenmuniUrlAsset('js/conceptos/concepto_valores.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
</body>

</html>