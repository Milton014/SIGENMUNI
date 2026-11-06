<?php

require_once __DIR__ . '/../../../core/Url.php';


/*
|--------------------------------------------------------------------------
| REPORTE DE AUDITORÍA - ROUTER
|--------------------------------------------------------------------------
*/

$auditoriaUrlReportes =
    sigenmuniUrlRuta(
        'reportes'
    );


$auditoriaUrlEntrada =
    sigenmuniUrlEntrada();


$auditoriaUrlLimpiar =
    sigenmuniUrlRuta(
        'reportes/auditoria'
    );


$auditoriaFiltros = [
    'fecha_desde' =>
        $fechaDesde,

    'fecha_hasta' =>
        $fechaHasta,

    'usuario' =>
        $usuario,

    'rol' =>
        $rol,

    'modulo' =>
        $modulo,

    'accion' =>
        $accion,

    'entidad' =>
        $entidad
];


$auditoriaUrlPdf =
    sigenmuniUrlRuta(
        'reportes/auditoria/pdf',
        $auditoriaFiltros
    );



/*
|--------------------------------------------------------------------------
| VISTA - REPORTE DE AUDITORÍA
|--------------------------------------------------------------------------
|
| Variables recibidas desde ReporteControlador::auditoria():
|
| $fechaDesde
| $fechaHasta
| $usuario
| $rol
| $modulo
| $accion
| $entidad
| $error
| $auditorias
| $totalAuditorias
| $usuariosAuditoria
| $rolesAuditoria
| $modulosAuditoria
| $accionesAuditoria
| $entidadesAuditoria
| $auditoriaDetalle
| $queryString
|
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| FUNCIONES DE APOYO DE LA VISTA
|--------------------------------------------------------------------------
*/

if (!function_exists('auditoriaEscapar')) {

    function auditoriaEscapar($valor)
    {
        return htmlspecialchars(
            (string)$valor,
            ENT_QUOTES,
            'UTF-8'
        );
    }
}


if (!function_exists('auditoriaFechaHora')) {

    function auditoriaFechaHora($fechaHora)
    {
        if (empty($fechaHora)) {

            return '-';
        }


        $timestamp =
            strtotime(
                $fechaHora
            );


        if ($timestamp === false) {

            return $fechaHora;
        }


        return date(
            'd/m/Y H:i:s',
            $timestamp
        );
    }
}


if (!function_exists('auditoriaEtiquetaCampo')) {

    function auditoriaEtiquetaCampo($campo)
    {
        $etiquetas = [

            'nro_legajo' =>
                'N.º de legajo',

            'apellido' =>
                'Apellido',

            'nombre' =>
                'Nombre',

            'institucion_id' =>
                'Institución ID',

            'institucion' =>
                'Institución',

            'oficina_id' =>
                'Unidad de Organización ID',

            'unidad_organizacion' =>
                'Unidad de Organización',

            'unidad_organizacion_cuit' =>
                'CUIT',

            'situacion_id' =>
                'Situación ID',

            'situacion' =>
                'Situación',

            'escalafon_id' =>
                'Escalafón ID',

            'escalafon' =>
                'Escalafón',

            'categoria_id' =>
                'Categoría ID',

            'categoria' =>
                'Categoría',

            'fecha_alta' =>
                'Fecha de alta',

            'fecha_baja' =>
                'Fecha de baja',

            'estado' =>
                'Estado'
        ];


        if (isset($etiquetas[$campo])) {

            return $etiquetas[$campo];
        }


        return ucfirst(
            str_replace(
                '_',
                ' ',
                (string)$campo
            )
        );
    }
}


if (!function_exists('auditoriaDecodificarDatos')) {

    function auditoriaDecodificarDatos($json)
    {
        if (
            $json === null
            ||
            trim(
                (string)$json
            )
            === ''
        ) {

            return [];
        }


        $datos =
            json_decode(
                $json,
                true
            );


        if (
            json_last_error()
            ===
            JSON_ERROR_NONE
            &&
            is_array(
                $datos
            )
        ) {

            return $datos;
        }


        return [
            'Información' =>
                $json
        ];
    }
}


if (!function_exists('auditoriaPrepararDatosDetalle')) {

    function auditoriaPrepararDatosDetalle(array $datos)
    {
        /*
        |--------------------------------------------------------------------------
        | OCULTAR IDs CUANDO EXISTE UNA DESCRIPCIÓN
        |--------------------------------------------------------------------------
        |
        | Los registros nuevos de auditoría guardan tanto el ID técnico como
        | una fotografía descriptiva del dato. Para que el reporte sea legible,
        | si existe el nombre correspondiente ocultamos el ID.
        |
        | Los registros antiguos siguen siendo compatibles: si no existe la
        | descripción, el ID se conserva y continúa mostrándose.
        |
        |--------------------------------------------------------------------------
        */

        $relaciones = [

            'institucion_id' =>
                'institucion',

            'oficina_id' =>
                'unidad_organizacion',

            'situacion_id' =>
                'situacion',

            'escalafon_id' =>
                'escalafon',

            'categoria_id' =>
                'categoria'
        ];


        foreach (
            $relaciones
            as
            $campoId => $campoDescripcion
        ) {

            if (
                array_key_exists(
                    $campoDescripcion,
                    $datos
                )
                &&
                $datos[$campoDescripcion] !== null
                &&
                $datos[$campoDescripcion] !== ''
            ) {

                unset(
                    $datos[$campoId]
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | ORDEN DE PRESENTACIÓN
        |--------------------------------------------------------------------------
        */

        $orden = [

            'nro_legajo',
            'apellido',
            'nombre',

            'institucion',
            'institucion_id',

            'unidad_organizacion',
            'unidad_organizacion_cuit',
            'oficina_id',

            'situacion',
            'situacion_id',

            'escalafon',
            'escalafon_id',

            'categoria',
            'categoria_id',

            'fecha_alta',
            'fecha_baja',

            'estado'
        ];


        $ordenados = [];


        foreach ($orden as $campo) {

            if (
                array_key_exists(
                    $campo,
                    $datos
                )
            ) {

                $ordenados[$campo] =
                    $datos[$campo];
            }
        }


        /*
        |--------------------------------------------------------------------------
        | CONSERVAR CAMPOS ADICIONALES
        |--------------------------------------------------------------------------
        */

        foreach (
            $datos
            as
            $campo => $valor
        ) {

            if (
                !array_key_exists(
                    $campo,
                    $ordenados
                )
            ) {

                $ordenados[$campo] =
                    $valor;
            }
        }


        return $ordenados;
    }
}


if (!function_exists('auditoriaValorMostrar')) {

    function auditoriaValorMostrar($valor)
    {
        if ($valor === null) {

            return '—';
        }


        if ($valor === '') {

            return '—';
        }


        if (is_bool($valor)) {

            return $valor
                ?
                'Sí'
                :
                'No';
        }


        if (is_array($valor)) {

            return json_encode(
                $valor,
                JSON_UNESCAPED_UNICODE
                |
                JSON_UNESCAPED_SLASHES
            );
        }


        return (string)$valor;
    }
}


if (!function_exists('auditoriaClaseAccion')) {

    function auditoriaClaseAccion($accion)
    {
        $accion =
            strtoupper(
                trim(
                    (string)$accion
                )
            );


        switch ($accion) {

            case 'ALTA':
                return 'accion-alta';

            case 'EDICION':
                return 'accion-edicion';

            case 'ACTIVACION':
                return 'accion-activacion';

            case 'INACTIVACION':
                return 'accion-inactivacion';

            case 'BAJA':
                return 'accion-baja';

            default:
                return 'accion-otra';
        }
    }
}


/*
|--------------------------------------------------------------------------
| URL DEL DETALLE
|--------------------------------------------------------------------------
*/

$filtrosDetalle = [

    'fecha_desde' =>
        $fechaDesde,

    'fecha_hasta' =>
        $fechaHasta,

    'usuario' =>
        $usuario,

    'rol' =>
        $rol,

    'modulo' =>
        $modulo,

    'accion' =>
        $accion,

    'entidad' =>
        $entidad
];


$filtrosDetalleLimpios = [];


foreach (
    $filtrosDetalle
    as
    $clave => $valor
) {

    if ($valor !== '') {

        $filtrosDetalleLimpios[$clave] =
            $valor;
    }
}


$filtrosDetalleLimpios = array_merge($filtrosDetalleLimpios, [
    'pagina' => $paginacion['pagina'], 'por_pagina' => $paginacion['por_pagina'],
]);

$queryFiltros =
    http_build_query(
        $filtrosDetalleLimpios
    );


$urlCerrarDetalle =
    sigenmuniUrlRuta(
        'reportes/auditoria',
        $filtrosDetalleLimpios
    );


$datosAnterioresDetalle = [];
$datosNuevosDetalle = [];


if ($auditoriaDetalle) {

    $datosAnterioresDetalle =
        auditoriaDecodificarDatos(
            $auditoriaDetalle['datos_anteriores']
            ??
            null
        );


    $datosNuevosDetalle =
        auditoriaDecodificarDatos(
            $auditoriaDetalle['datos_nuevos']
            ??
            null
        );


    $datosAnterioresDetalle =
        auditoriaPrepararDatosDetalle(
            $datosAnterioresDetalle
        );


    $datosNuevosDetalle =
        auditoriaPrepararDatosDetalle(
            $datosNuevosDetalle
        );
}

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
    SIGENMUNI - Reporte de Auditoría
</title>


<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/reportes/reporte_auditoria.css'), ENT_QUOTES, 'UTF-8') ?>">

<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/compartido/paginacion.css'), ENT_QUOTES, 'UTF-8') ?>">
<link rel="stylesheet" href="<?= htmlspecialchars(sigenmuniUrlAsset('css/reportes/paginacion.css'), ENT_QUOTES, 'UTF-8') ?>">
</head>


<body>


<!-- =========================================
     HEADER GLOBAL
========================================= -->

<?php

$sigenmuniHeaderSubtitulo =
    'Sistema de Gestión Municipal - Reporte de Auditoría';

require __DIR__
    . '/../../../componentes/header_sigenmuni.php';

?>


<div class="contenedor">


    <!-- =====================================
         CABECERA
    ====================================== -->

    <div class="panel-superior">

        <div class="titulo-reporte">

            <h2>
                🛡️ Reporte de Auditoría
            </h2>

            <p>
                Historial de acciones realizadas por los usuarios del sistema.
            </p>

        </div>


        <div class="acciones-superiores">

            <a
                href="<?php
                    echo auditoriaEscapar(
                        $auditoriaUrlReportes
                    );
                ?>"
                class="btn btn-volver"
            >
                Volver a reportes
            </a>


            <button
                type="button"
                class="btn btn-imprimir"
                data-event-click="reporte_auditoria-1"
            >
                Imprimir
            </button>


            <a
                href="<?php
                    echo auditoriaEscapar(
                        $auditoriaUrlPdf
                    );
                ?>"
                class="btn btn-pdf"
            >
                Exportar PDF
            </a>

        </div>

    </div>


    <!-- =====================================
         ERROR
    ====================================== -->

    <?php if (!empty($error)): ?>

        <div class="mensaje-error">

            <?php
                echo auditoriaEscapar(
                    $error
                );
            ?>

        </div>

    <?php endif; ?>


    <!-- =====================================
         FILTROS
    ====================================== -->

    <div class="panel panel-filtros">

        <div class="panel-titulo">

            <div class="icono-panel">
                🔎
            </div>

            <h3>
                Filtros de búsqueda
            </h3>

        </div>


        <form
            method="GET"
            action="<?php
                echo auditoriaEscapar(
                    $auditoriaUrlEntrada
                );
            ?>"
        >

            <input
                type="hidden"
                name="r"
                value="reportes/auditoria"
            >

            <div class="filtros-grid">


                <div class="campo">

                    <label for="fecha_desde">
                        Fecha desde
                    </label>

                    <input
                        type="date"
                        id="fecha_desde"
                        name="fecha_desde"
                        value="<?php
                            echo auditoriaEscapar(
                                $fechaDesde
                            );
                        ?>"
                    >

                </div>


                <div class="campo">

                    <label for="fecha_hasta">
                        Fecha hasta
                    </label>

                    <input
                        type="date"
                        id="fecha_hasta"
                        name="fecha_hasta"
                        value="<?php
                            echo auditoriaEscapar(
                                $fechaHasta
                            );
                        ?>"
                    >

                </div>


                <div class="campo">

                    <label for="usuario">
                        Usuario
                    </label>

                    <select
                        id="usuario"
                        name="usuario"
                    >

                        <option value="">
                            Todos
                        </option>

                        <?php foreach ($usuariosAuditoria as $item): ?>

                            <?php

                            $usuarioItem =
                                $item['usuario_login']
                                ??
                                '';

                            $nombreItem =
                                $item['usuario_nombre']
                                ??
                                '';

                            ?>

                            <option
                                value="<?php
                                    echo auditoriaEscapar(
                                        $usuarioItem
                                    );
                                ?>"
                                <?php
                                    echo (
                                        $usuario === $usuarioItem
                                    )
                                        ?
                                        'selected'
                                        :
                                        '';
                                ?>
                            >

                                <?php

                                echo auditoriaEscapar(
                                    $usuarioItem
                                    .
                                    (
                                        $nombreItem !== ''
                                            ?
                                            ' - '
                                            . $nombreItem
                                            :
                                            ''
                                    )
                                );

                                ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <div class="campo">

                    <label for="rol">
                        Rol
                    </label>

                    <select
                        id="rol"
                        name="rol"
                    >

                        <option value="">
                            Todos
                        </option>

                        <?php foreach ($rolesAuditoria as $item): ?>

                            <?php
                                $rolItem =
                                    $item['rol']
                                    ??
                                    '';
                            ?>

                            <option
                                value="<?php
                                    echo auditoriaEscapar(
                                        $rolItem
                                    );
                                ?>"
                                <?php
                                    echo (
                                        $rol === $rolItem
                                    )
                                        ?
                                        'selected'
                                        :
                                        '';
                                ?>
                            >
                                <?php
                                    echo auditoriaEscapar(
                                        $rolItem
                                    );
                                ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <div class="campo">

                    <label for="modulo">
                        Módulo
                    </label>

                    <select
                        id="modulo"
                        name="modulo"
                    >

                        <option value="">
                            Todos
                        </option>

                        <?php foreach ($modulosAuditoria as $item): ?>

                            <?php
                                $moduloItem =
                                    $item['modulo']
                                    ??
                                    '';
                            ?>

                            <option
                                value="<?php
                                    echo auditoriaEscapar(
                                        $moduloItem
                                    );
                                ?>"
                                <?php
                                    echo (
                                        $modulo === $moduloItem
                                    )
                                        ?
                                        'selected'
                                        :
                                        '';
                                ?>
                            >
                                <?php
                                    echo auditoriaEscapar(
                                        $moduloItem
                                    );
                                ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <div class="campo">

                    <label for="accion">
                        Acción
                    </label>

                    <select
                        id="accion"
                        name="accion"
                    >

                        <option value="">
                            Todas
                        </option>

                        <?php foreach ($accionesAuditoria as $item): ?>

                            <?php
                                $accionItem =
                                    strtoupper(
                                        $item['accion']
                                        ??
                                        ''
                                    );
                            ?>

                            <option
                                value="<?php
                                    echo auditoriaEscapar(
                                        $accionItem
                                    );
                                ?>"
                                <?php
                                    echo (
                                        $accion === $accionItem
                                    )
                                        ?
                                        'selected'
                                        :
                                        '';
                                ?>
                            >
                                <?php
                                    echo auditoriaEscapar(
                                        $accionItem
                                    );
                                ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <div class="campo">

                    <label for="entidad">
                        Entidad
                    </label>

                    <select
                        id="entidad"
                        name="entidad"
                    >

                        <option value="">
                            Todas
                        </option>

                        <?php foreach ($entidadesAuditoria as $item): ?>

                            <?php
                                $entidadItem =
                                    strtoupper(
                                        $item['entidad']
                                        ??
                                        ''
                                    );
                            ?>

                            <option
                                value="<?php
                                    echo auditoriaEscapar(
                                        $entidadItem
                                    );
                                ?>"
                                <?php
                                    echo (
                                        $entidad === $entidadItem
                                    )
                                        ?
                                        'selected'
                                        :
                                        '';
                                ?>
                            >
                                <?php
                                    echo auditoriaEscapar(
                                        $entidadItem
                                    );
                                ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


            </div>


            <div class="acciones-filtros">

                <button
                    type="submit"
                    class="btn btn-buscar"
                >
                    Buscar
                </button>


                <a
                    href="<?php
                        echo auditoriaEscapar(
                            $auditoriaUrlLimpiar
                        );
                    ?>"
                    class="btn btn-limpiar"
                >
                    Limpiar filtros
                </a>

            </div>

            <input type="hidden" name="por_pagina" value="<?= $paginacion['por_pagina'] ?>">
        </form>

    </div>


    <!-- =====================================
         LISTADO
    ====================================== -->

    <div class="panel">

        <div class="resumen">

            <div class="total-box">

                Total de registros:

                <?php
                    echo (int)$totalAuditorias;
                ?>

            </div>


            <div class="leyenda">
                Los registros se muestran del más reciente al más antiguo.
            </div>

        </div>


        <?php
        $paginacionRuta = 'reportes/auditoria';
        $paginacionParametros = $auditoriaFiltros;
        $paginacionEntidad = 'registros';
        $paginacionClavePagina = 'pagina'; $paginacionClaveCantidad = 'por_pagina';
        require __DIR__ . '/../../../componentes/paginacion_reporte.php';
        ?>

<?php if (!empty($auditorias)): ?>


            <div class="tabla-contenedor tabla-auditoria-paginada">

                <table>

                    <thead>

                        <tr>

                            <th>
                                Fecha / Hora
                            </th>

                            <th>
                                Usuario
                            </th>

                            <th>
                                Rol
                            </th>

                            <th>
                                Módulo
                            </th>

                            <th>
                                Acción
                            </th>

                            <th>
                                Registro afectado
                            </th>

                            <th>
                                Detalle
                            </th>

                            <th class="col-accion">
                                Ver
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php foreach ($auditorias as $fila): ?>

                        <?php

                        $idAuditoria =
                            (int)(
                                $fila['id']
                                ??
                                0
                            );


                        $urlDetalle =
                            sigenmuniUrlRuta(
                                'reportes/auditoria',
                                array_merge(
                                    $filtrosDetalleLimpios,
                                    [
                                        'detalle_id' =>
                                            $idAuditoria
                                    ]
                                )
                            );

                        ?>

                        <tr>

                            <td>

                                <?php
                                    echo auditoriaEscapar(
                                        auditoriaFechaHora(
                                            $fila['fecha_hora']
                                            ??
                                            ''
                                        )
                                    );
                                ?>

                            </td>


                            <td>

                                <div class="usuario-principal">

                                    <?php
                                        echo auditoriaEscapar(
                                            $fila['usuario_login']
                                            ??
                                            '-'
                                        );
                                    ?>

                                </div>


                                <div class="usuario-secundario">

                                    <?php
                                        echo auditoriaEscapar(
                                            $fila['usuario_nombre']
                                            ??
                                            '-'
                                        );
                                    ?>

                                </div>

                            </td>


                            <td>

                                <?php
                                    echo auditoriaEscapar(
                                        $fila['rol']
                                        ??
                                        '-'
                                    );
                                ?>

                            </td>


                            <td>

                                <?php
                                    echo auditoriaEscapar(
                                        $fila['modulo']
                                        ??
                                        '-'
                                    );
                                ?>

                            </td>


                            <td>

                                <span
                                    class="badge <?php
                                        echo auditoriaEscapar(
                                            auditoriaClaseAccion(
                                                $fila['accion']
                                                ??
                                                ''
                                            )
                                        );
                                    ?>"
                                >

                                    <?php
                                        echo auditoriaEscapar(
                                            $fila['accion']
                                            ??
                                            '-'
                                        );
                                    ?>

                                </span>

                            </td>


                            <td>

                                <div class="registro-principal">

                                    <?php
                                        echo auditoriaEscapar(
                                            $fila['entidad_descripcion']
                                            ??
                                            (
                                                $fila['entidad']
                                                ??
                                                '-'
                                            )
                                        );
                                    ?>

                                </div>


                                <div class="registro-secundario">

                                    <?php

                                    echo auditoriaEscapar(
                                        (
                                            $fila['entidad']
                                            ??
                                            '-'
                                        )
                                        .
                                        (
                                            !empty(
                                                $fila['entidad_id']
                                            )
                                                ?
                                                ' · ID '
                                                . $fila['entidad_id']
                                                :
                                                ''
                                        )
                                    );

                                    ?>

                                </div>

                            </td>


                            <td>

                                <div class="detalle-texto">

                                    <?php
                                        echo auditoriaEscapar(
                                            $fila['detalle']
                                            ??
                                            '-'
                                        );
                                    ?>

                                </div>

                            </td>


                            <td class="col-accion">

                                <a
                                    href="<?php
                                        echo auditoriaEscapar(
                                            $urlDetalle
                                        );
                                    ?>"
                                    class="btn-detalle"
                                >
                                    Ver detalle
                                </a>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>
            <?php require __DIR__ . '/../../../componentes/paginacion.php'; ?>


        <?php else: ?>


            <div class="sin-registros">

                No se encontraron registros de auditoría
                para los filtros seleccionados.

            </div>


        <?php endif; ?>

    </div>


    <!-- =====================================
         DETALLE
    ====================================== -->

    <?php if ($auditoriaDetalle): ?>

        <div
            class="panel detalle-auditoria"
            id="detalleAuditoria"
        >

            <div class="detalle-cabecera">

                <div>

                    <h3>
                        Detalle de Auditoría
                    </h3>

                    <p>

                        Registro N.º

                        <?php
                            echo (int)(
                                $auditoriaDetalle['id']
                                ??
                                0
                            );
                        ?>

                    </p>

                </div>


                <a
                    href="<?php
                        echo auditoriaEscapar(
                            $urlCerrarDetalle
                        );
                    ?>"
                    class="btn btn-cerrar"
                >
                    Cerrar detalle
                </a>

            </div>


            <div class="detalle-meta">


                <div class="meta-item">

                    <div class="titulo">
                        Fecha / Hora
                    </div>

                    <div class="valor">

                        <?php
                            echo auditoriaEscapar(
                                auditoriaFechaHora(
                                    $auditoriaDetalle['fecha_hora']
                                    ??
                                    ''
                                )
                            );
                        ?>

                    </div>

                </div>


                <div class="meta-item">

                    <div class="titulo">
                        Usuario
                    </div>

                    <div class="valor">

                        <?php

                        echo auditoriaEscapar(
                            (
                                $auditoriaDetalle['usuario_login']
                                ??
                                '-'
                            )
                            .
                            ' - '
                            .
                            (
                                $auditoriaDetalle['usuario_nombre']
                                ??
                                '-'
                            )
                        );

                        ?>

                    </div>

                </div>


                <div class="meta-item">

                    <div class="titulo">
                        Rol
                    </div>

                    <div class="valor">

                        <?php
                            echo auditoriaEscapar(
                                $auditoriaDetalle['rol']
                                ??
                                '-'
                            );
                        ?>

                    </div>

                </div>


                <div class="meta-item">

                    <div class="titulo">
                        IP
                    </div>

                    <div class="valor">

                        <?php
                            echo auditoriaEscapar(
                                $auditoriaDetalle['ip']
                                ??
                                '-'
                            );
                        ?>

                    </div>

                </div>


                <div class="meta-item">

                    <div class="titulo">
                        Módulo
                    </div>

                    <div class="valor">

                        <?php
                            echo auditoriaEscapar(
                                $auditoriaDetalle['modulo']
                                ??
                                '-'
                            );
                        ?>

                    </div>

                </div>


                <div class="meta-item">

                    <div class="titulo">
                        Acción
                    </div>

                    <div class="valor">

                        <span
                            class="badge <?php
                                echo auditoriaEscapar(
                                    auditoriaClaseAccion(
                                        $auditoriaDetalle['accion']
                                        ??
                                        ''
                                    )
                                );
                            ?>"
                        >

                            <?php
                                echo auditoriaEscapar(
                                    $auditoriaDetalle['accion']
                                    ??
                                    '-'
                                );
                            ?>

                        </span>

                    </div>

                </div>


                <div class="meta-item">

                    <div class="titulo">
                        Entidad
                    </div>

                    <div class="valor">

                        <?php
                            echo auditoriaEscapar(
                                $auditoriaDetalle['entidad']
                                ??
                                '-'
                            );
                        ?>

                    </div>

                </div>


                <div class="meta-item">

                    <div class="titulo">
                        ID afectado
                    </div>

                    <div class="valor">

                        <?php

                        echo auditoriaEscapar(
                            $auditoriaDetalle['entidad_id']
                            ??
                            '-'
                        );

                        ?>

                    </div>

                </div>


            </div>


            <div class="detalle-descripcion">

                <strong>
                    Registro:
                </strong>

                <?php
                    echo auditoriaEscapar(
                        $auditoriaDetalle['entidad_descripcion']
                        ??
                        '-'
                    );
                ?>

                <br><br>

                <strong>
                    Detalle:
                </strong>

                <?php
                    echo auditoriaEscapar(
                        $auditoriaDetalle['detalle']
                        ??
                        '-'
                    );
                ?>

            </div>


            <div class="comparacion-grid">


                <!-- DATOS ANTERIORES -->

                <div class="bloque-datos">

                    <h4>
                        Datos anteriores
                    </h4>


                    <?php if (!empty($datosAnterioresDetalle)): ?>

                        <table>

                            <tbody>

                            <?php foreach ($datosAnterioresDetalle as $campo => $valor): ?>

                                <tr>

                                    <th>

                                        <?php
                                            echo auditoriaEscapar(
                                                auditoriaEtiquetaCampo(
                                                    $campo
                                                )
                                            );
                                        ?>

                                    </th>


                                    <td>

                                        <?php
                                            echo auditoriaEscapar(
                                                auditoriaValorMostrar(
                                                    $valor
                                                )
                                            );
                                        ?>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                            </tbody>

                        </table>


                    <?php else: ?>


                        <div class="vacio-datos">
                            No hay datos anteriores registrados.
                        </div>


                    <?php endif; ?>

                </div>


                <!-- DATOS NUEVOS -->

                <div class="bloque-datos">

                    <h4>
                        Datos nuevos
                    </h4>


                    <?php if (!empty($datosNuevosDetalle)): ?>

                        <table>

                            <tbody>

                            <?php foreach ($datosNuevosDetalle as $campo => $valor): ?>

                                <tr>

                                    <th>

                                        <?php
                                            echo auditoriaEscapar(
                                                auditoriaEtiquetaCampo(
                                                    $campo
                                                )
                                            );
                                        ?>

                                    </th>


                                    <td>

                                        <?php
                                            echo auditoriaEscapar(
                                                auditoriaValorMostrar(
                                                    $valor
                                                )
                                            );
                                        ?>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                            </tbody>

                        </table>


                    <?php else: ?>


                        <div class="vacio-datos">
                            No hay datos nuevos registrados.
                        </div>


                    <?php endif; ?>

                </div>


            </div>

        </div>


        <script src="<?= htmlspecialchars(sigenmuniUrlAsset('js/reportes/reporte_auditoria.js'), ENT_QUOTES, 'UTF-8') ?>"></script>

    <?php endif; ?>


</div>


<div class="footer">

    SIGENMUNI · Reporte de Auditoría · Solo consulta

</div>


<script src="<?= htmlspecialchars(sigenmuniUrlAsset('js/reportes/paginacion.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
</body>

</html>
