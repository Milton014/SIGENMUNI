<?php

/*
|--------------------------------------------------------------------------
| VISTA - GESTIÓN DE CATEGORÍAS
|--------------------------------------------------------------------------
|
| Esta vista se ejecuta mediante la entrada única public/index.php
| y las rutas registradas en modulos/categorias/rutas.php.
|
| Las operaciones que modifican datos utilizan POST + CSRF.
|
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../../../core/Url.php';
require_once __DIR__ . '/../../../core/Csrf.php';


$categoriasUrlListado =
    sigenmuniUrlRuta(
        'categorias'
    );


$categoriasAccionBuscar =
    sigenmuniUrlEntrada();


$categoriasUrlNueva =
    sigenmuniUrlRuta(
        'categorias/nueva'
    );


$categoriasUrlEstado =
    sigenmuniUrlRuta(
        'categorias/estado'
    );


$categoriasCsrfToken =
    sigenmuniCsrfToken();

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>
    Gestión de Categorías - SIGENMUNI
</title>


<style>

/* =========================================
   GENERAL
========================================= */

*{
    box-sizing:border-box;
}

body{
    font-family:Arial,sans-serif;
    margin:0;
    background:#f4f7fb;
    color:#1f2937;
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
    box-shadow:
        0 8px 20px rgba(0,0,0,.08);
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
    margin-bottom:24px;
}

.topbar h2{
    margin:0;
    color:#6d28d9;
    font-size:30px;
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
    padding:11px 15px;
    border:none;
    border-radius:10px;
    text-decoration:none;
    color:white;
    font-size:14px;
    font-weight:bold;
    cursor:pointer;
    transition:.2s;
    text-align:center;
}

.btn:hover{
    opacity:.92;
    transform:translateY(-1px);
}

.btn-nuevo{
    background:#7c3aed;
}

.btn-volver{
    background:#1f2937;
}

.btn-buscar{
    background:#7c3aed;
}

.btn-limpiar{
    background:#64748b;
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
    border:1px solid #86efac;
    color:#166534;
}

.mensaje-error{
    background:#fee2e2;
    border:1px solid #fecaca;
    color:#991b1b;
}


/* =========================================
   FILTROS
========================================= */

.filtros{
    background:#f8fafc;
    border:1px solid #e5e7eb;
    border-radius:14px;
    padding:18px;
    margin-bottom:24px;

    display:grid;

    grid-template-columns:
        minmax(260px, 2fr)
        minmax(180px, 1fr)
        auto
        auto;

    gap:12px;
}

.filtros input,
.filtros select{
    width:100%;
    min-height:44px;
    padding:11px 12px;
    border:1px solid #cbd5e1;
    border-radius:10px;
    background:white;
    font-size:14px;
    outline:none;
}

.filtros input:focus,
.filtros select:focus{
    border-color:#a855f7;
    box-shadow:
        0 0 0 3px rgba(168,85,247,.14);
}


/* =========================================
   RESUMEN
========================================= */

.resumen{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:12px;
    flex-wrap:wrap;
    margin-bottom:14px;
}

.total{
    display:inline-block;
    padding:9px 13px;
    border:1px solid #ddd6fe;
    background:#f5f3ff;
    color:#6d28d9;
    border-radius:10px;
    font-size:14px;
    font-weight:bold;
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
    background:white;
}

th,
td{
    padding:11px 10px;
    border-bottom:1px solid #e5e7eb;
    text-align:left;
    vertical-align:middle;
    font-size:13px;
}

th{
    background:#7c3aed;
    color:white;
    white-space:nowrap;
}

thead th:first-child{
    border-radius:10px 0 0 0;
}

thead th:last-child{
    border-radius:0 10px 0 0;
}

tbody tr:hover{
    background:#fafafa;
}


/* =========================================
   COLUMNAS
========================================= */

.col-codigo{
    width:110px;
    white-space:nowrap;
}

.col-nombre{
    min-width:190px;
}

.col-monto{
    min-width:145px;
    white-space:nowrap;
    font-weight:bold;
    color:#0f766e;
}

.sin-valor{
    display:inline-block;
    padding:5px 8px;
    border-radius:8px;
    background:#f1f5f9;
    color:#64748b;
    font-size:12px;
    font-weight:bold;
    white-space:nowrap;
}

.col-estado{
    width:120px;
    white-space:nowrap;
}

.col-acciones{
    width:1%;
    white-space:nowrap;
}


/* =========================================
   ESTADOS
========================================= */

.estado{
    display:inline-block;
    padding:5px 9px;
    border-radius:999px;
    font-size:12px;
    font-weight:bold;
}

.estado-activo{
    background:#dcfce7;
    color:#166534;
}

.estado-inactivo{
    background:#fee2e2;
    color:#991b1b;
}


/* =========================================
   ACCIONES
========================================= */

.acciones{
    display:flex;
    gap:7px;
    flex-wrap:wrap;
}

.form-accion{
    margin:0;
    padding:0;
}

.btn-accion{
    display:inline-block;
    padding:7px 10px;
    border-radius:8px;
    color:white;
    text-decoration:none;
    font-size:12px;
    font-weight:bold;
    white-space:nowrap;
    border:none;
    cursor:pointer;
    font-family:inherit;
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
    padding:28px;
    text-align:center;
    color:#64748b;
    border:1px dashed #cbd5e1;
    border-radius:12px;
    background:#f8fafc;
}


/* =========================================
   TABLET
========================================= */

@media (max-width:900px){

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
        padding:14px;
        box-shadow:
            0 4px 12px rgba(0,0,0,.05);
    }

    td{
        display:grid;
        grid-template-columns:120px 1fr;
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
    .col-monto,
    .col-estado,
    .col-acciones{
        width:auto;
        min-width:0;
        white-space:normal;
    }

    .acciones{
        flex-direction:column;
    }

    .btn-accion{
        width:100%;
        text-align:center;
        padding:9px;
    }
}


/* =========================================
   CELULAR PEQUEÑO
========================================= */

@media (max-width:480px){

    .topbar h2{
        font-size:22px;
    }

    td{
        grid-template-columns:100px 1fr;
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
    'Gestión de Categorías Municipales';

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
                Gestión de Categorías
            </h2>


            <div class="acciones-superiores">

                <a
                    href="<?php echo htmlspecialchars($categoriasUrlNueva, ENT_QUOTES, 'UTF-8'); ?>"
                    class="btn btn-nuevo"
                >
                    + Nueva Categoría
                </a>


                <a
                    href="<?php echo htmlspecialchars(sigenmuniUrlRuta('inicio'), ENT_QUOTES, 'UTF-8'); ?>"
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
            action="<?php echo htmlspecialchars($categoriasAccionBuscar, ENT_QUOTES, 'UTF-8'); ?>"
            class="filtros"
        >

            <input
                type="hidden"
                name="r"
                value="categorias"
            >

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


            <select name="activo">

                <option value="">
                    -- Todos los estados --
                </option>

                <option
                    value="1"
                    <?php
                    echo (
                        ($activo ?? '')
                        ===
                        '1'
                    )
                        ?
                        'selected'
                        :
                        '';
                    ?>
                >
                    Activas
                </option>

                <option
                    value="0"
                    <?php
                    echo (
                        ($activo ?? '')
                        ===
                        '0'
                    )
                        ?
                        'selected'
                        :
                        '';
                    ?>
                >
                    Inactivas
                </option>

            </select>


            <button
                type="submit"
                class="btn btn-buscar"
            >
                Buscar
            </button>


            <a
                href="<?php echo htmlspecialchars($categoriasUrlListado, ENT_QUOTES, 'UTF-8'); ?>"
                class="btn btn-limpiar"
            >
                Limpiar
            </a>

        </form>


        <!-- =====================================
             RESUMEN
        ====================================== -->

        <div class="resumen">

            <div class="total">

                Total de categorías encontradas:
                <?php
                echo count(
                    $categorias
                    ?? []
                );
                ?>

            </div>

        </div>


        <!-- =====================================
             LISTADO
        ====================================== -->

        <?php if (!empty($categorias)): ?>

            <div class="tabla-contenedor">

                <table>

                    <thead>

                        <tr>

                            <th>
                                Código
                            </th>

                            <th>
                                Nombre
                            </th>

                            <th>
                                Sueldo Básico
                            </th>

                            <th>
                                Dedicación Funcional
                            </th>

                            <th>
                                Suplemento Especial
                            </th>

                            <th>
                                Estado
                            </th>

                            <th>
                                Acciones
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        <?php foreach ($categorias as $categoriaFila): ?>

                            <tr>

                                <td
                                    class="col-codigo"
                                    data-label="Código"
                                >

                                    <?php
                                    echo htmlspecialchars(
                                        (string)$categoriaFila['codigo'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>

                                </td>


                                <td
                                    class="col-nombre"
                                    data-label="Nombre"
                                >

                                    <?php
                                    echo htmlspecialchars(
                                        $categoriaFila['nombre'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>

                                </td>


                                <td
                                    class="col-monto"
                                    data-label="Sueldo Básico"
                                >

                                    <?php if (
                                        $categoriaFila['sueldo_basico_actual']
                                        !==
                                        null
                                    ): ?>

                                        $ <?php
                                        echo number_format(
                                            (float)$categoriaFila['sueldo_basico_actual'],
                                            2,
                                            ',',
                                            '.'
                                        );
                                        ?>

                                    <?php else: ?>

                                        <span class="sin-valor">
                                            Sin valor
                                        </span>

                                    <?php endif; ?>

                                </td>


                                <td
                                    class="col-monto"
                                    data-label="Dedicación Funcional"
                                >

                                    <?php if (
                                        $categoriaFila['dedicacion_funcional_actual']
                                        !==
                                        null
                                    ): ?>

                                        $ <?php
                                        echo number_format(
                                            (float)$categoriaFila['dedicacion_funcional_actual'],
                                            2,
                                            ',',
                                            '.'
                                        );
                                        ?>

                                    <?php else: ?>

                                        <span class="sin-valor">
                                            Sin valor
                                        </span>

                                    <?php endif; ?>

                                </td>


                                <td
                                    class="col-monto"
                                    data-label="Suplemento Especial"
                                >

                                    <?php if (
                                        $categoriaFila['suplemento_especial_actual']
                                        !==
                                        null
                                    ): ?>

                                        $ <?php
                                        echo number_format(
                                            (float)$categoriaFila['suplemento_especial_actual'],
                                            2,
                                            ',',
                                            '.'
                                        );
                                        ?>

                                    <?php else: ?>

                                        <span class="sin-valor">
                                            Sin valor
                                        </span>

                                    <?php endif; ?>

                                </td>


                                <td
                                    class="col-estado"
                                    data-label="Estado"
                                >

                                    <?php if (
                                        (int)$categoriaFila['activo']
                                        ===
                                        1
                                    ): ?>

                                        <span class="estado estado-activo">
                                            Activa
                                        </span>

                                    <?php else: ?>

                                        <span class="estado estado-inactivo">
                                            Inactiva
                                        </span>

                                    <?php endif; ?>

                                </td>


                                <td
                                    class="col-acciones"
                                    data-label="Acciones"
                                >

                                    <div class="acciones">

                                        <a
                                            href="<?php
                                                echo htmlspecialchars(
                                                    sigenmuniUrlRuta(
                                                        'categorias/editar',
                                                        [
                                                            'id' =>
                                                                (int)$categoriaFila['id']
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


                                        <?php if (
                                            (int)$categoriaFila['activo']
                                            ===
                                            1
                                        ): ?>

                                            <form
                                                method="POST"
                                                action="<?php
                                                    echo htmlspecialchars(
                                                        $categoriasUrlEstado,
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    );
                                                ?>"
                                                class="form-accion"
                                                onsubmit="return confirm('¿Seguro que desea inactivar esta categoría?');"
                                            >

                                                <input
                                                    type="hidden"
                                                    name="id"
                                                    value="<?php echo (int)$categoriaFila['id']; ?>"
                                                >

                                                <input
                                                    type="hidden"
                                                    name="estado"
                                                    value="0"
                                                >

                                                <input
                                                    type="hidden"
                                                    name="_csrf"
                                                    value="<?php
                                                        echo htmlspecialchars(
                                                            $categoriasCsrfToken,
                                                            ENT_QUOTES,
                                                            'UTF-8'
                                                        );
                                                    ?>"
                                                >

                                                <button
                                                    type="submit"
                                                    class="btn-accion btn-inactivar"
                                                >
                                                    Inactivar
                                                </button>

                                            </form>

                                        <?php else: ?>

                                            <form
                                                method="POST"
                                                action="<?php
                                                    echo htmlspecialchars(
                                                        $categoriasUrlEstado,
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    );
                                                ?>"
                                                class="form-accion"
                                                onsubmit="return confirm('¿Seguro que desea activar esta categoría?');"
                                            >

                                                <input
                                                    type="hidden"
                                                    name="id"
                                                    value="<?php echo (int)$categoriaFila['id']; ?>"
                                                >

                                                <input
                                                    type="hidden"
                                                    name="estado"
                                                    value="1"
                                                >

                                                <input
                                                    type="hidden"
                                                    name="_csrf"
                                                    value="<?php
                                                        echo htmlspecialchars(
                                                            $categoriasCsrfToken,
                                                            ENT_QUOTES,
                                                            'UTF-8'
                                                        );
                                                    ?>"
                                                >

                                                <button
                                                    type="submit"
                                                    class="btn-accion btn-activar"
                                                >
                                                    Activar
                                                </button>

                                            </form>

                                        <?php endif; ?>

                                    </div>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php else: ?>

            <div class="sin-resultados">

                No se encontraron categorías con los filtros seleccionados.

            </div>

        <?php endif; ?>

    </div>

</div>


</body>

</html>
