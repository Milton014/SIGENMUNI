<?php

require_once __DIR__ . '/../../../core/Url.php';
require_once __DIR__ . '/../../../core/Csrf.php';


/*
|--------------------------------------------------------------------------
| ESTADO DE EMPLEADO - SOLO ROUTER
|--------------------------------------------------------------------------
*/

$empleadoEstadoId =
    (int)(
        $empleado['id']
        ?? 0
    );


$empleadoEstadoAccion =
    sigenmuniUrlRuta(
        'empleados/estado',
        [
            'id' => $empleadoEstadoId,
            'accion' => (string)(
                $accion
                ?? ''
            )
        ]
    );


$empleadoEstadoCancelar =
    sigenmuniUrlRuta(
        'empleados'
    );


$empleadoEstadoCsrf =
    sigenmuniCsrfToken();

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>
    <?php echo htmlspecialchars($tituloAccion ?? 'Cambiar Estado', ENT_QUOTES, 'UTF-8'); ?>
    - SIGENMUNI
</title>

<style>

*{
    box-sizing:border-box;
}

body{
    margin:0;
    font-family:Arial,sans-serif;
    background:#f4f7fb;
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
    width:94%;
    max-width:820px;
    margin:30px auto;
}

.panel{
    background:white;
    padding:28px;
    border-radius:18px;
    box-shadow:0 8px 20px rgba(0,0,0,.08);
}

h2{
    margin:0 0 8px;
    color:#0f766e;
    font-size:27px;
}

.subtitulo{
    margin:0 0 22px;
    color:#64748b;
    line-height:1.5;
}

.mensaje{
    padding:14px 16px;
    border-radius:10px;
    margin-bottom:20px;
    font-weight:bold;
    font-size:14px;
}

.mensaje-error{
    background:#fee2e2;
    color:#991b1b;
    border:1px solid #fecaca;
}

.resumen{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:12px;
    margin-bottom:22px;
}

.dato{
    background:#f8fafc;
    border:1px solid #e5e7eb;
    border-radius:12px;
    padding:13px 14px;
}

.dato strong{
    display:block;
    color:#64748b;
    font-size:12px;
    margin-bottom:5px;
    text-transform:uppercase;
}

.dato span{
    font-weight:bold;
}

.estado-activo{
    color:#166534;
}

.estado-inactivo{
    color:#991b1b;
}

.aviso{
    padding:15px 16px;
    border-radius:12px;
    margin-bottom:22px;
    line-height:1.55;
    font-size:14px;
}

.aviso-inactivar{
    background:#fff7ed;
    border:1px solid #fed7aa;
    color:#9a3412;
}

.aviso-activar{
    background:#ecfdf5;
    border:1px solid #a7f3d0;
    color:#166534;
}

.campo{
    margin-bottom:18px;
}

label{
    display:block;
    margin-bottom:7px;
    font-weight:bold;
    font-size:14px;
}

input,
textarea{
    width:100%;
    padding:12px;
    border:1px solid #cbd5e1;
    border-radius:10px;
    font-size:14px;
    outline:none;
    background:white;
}

input:focus,
textarea:focus{
    border-color:#14b8a6;
    box-shadow:0 0 0 3px rgba(20,184,166,.15);
}

textarea{
    min-height:100px;
    resize:vertical;
}

.ayuda{
    margin-top:6px;
    font-size:12px;
    color:#64748b;
    line-height:1.45;
}

.historial-resumen{
    margin:22px 0;
    border-top:1px solid #e5e7eb;
    padding-top:20px;
}

.historial-resumen h3{
    margin:0 0 12px;
    color:#0f766e;
}

.tabla{
    width:100%;
    border-collapse:collapse;
}

.tabla th,
.tabla td{
    padding:10px 8px;
    border-bottom:1px solid #e5e7eb;
    font-size:12px;
    text-align:left;
}

.tabla th{
    background:#f8fafc;
    color:#475569;
}

.badge{
    display:inline-block;
    padding:5px 9px;
    border-radius:999px;
    font-size:11px;
    font-weight:bold;
}

.badge-actual{
    background:#dcfce7;
    color:#166534;
}

.badge-finalizado{
    background:#f1f5f9;
    color:#475569;
}

.acciones{
    margin-top:24px;
    display:flex;
    justify-content:flex-end;
    gap:10px;
    flex-wrap:wrap;
}

.btn{
    border:none;
    border-radius:10px;
    padding:11px 16px;
    color:white;
    font-size:14px;
    font-weight:bold;
    text-decoration:none;
    cursor:pointer;
}

.btn-cancelar{
    background:#1f2937;
}

.btn-inactivar{
    background:#dc2626;
}

.btn-activar{
    background:#16a34a;
}

@media(max-width:700px){

    .header{
        text-align:center;
        padding:20px;
    }

    .header h1{
        font-size:24px;
    }

    .contenedor{
        width:94%;
        margin:20px auto;
    }

    .panel{
        padding:20px;
    }

    .resumen{
        grid-template-columns:1fr;
    }

    .acciones{
        flex-direction:column-reverse;
    }

    .btn{
        width:100%;
        text-align:center;
    }

    .tabla-contenedor{
        overflow-x:auto;
    }

    .tabla{
        min-width:560px;
    }
}

</style>

</head>

<body>

<div class="header">
    <h1>SIGENMUNI</h1>
    <p>Gestión del estado e historial laboral del empleado</p>
</div>

<div class="contenedor">

    <div class="panel">

        <h2>
            <?php
            echo htmlspecialchars(
                $tituloAccion ?? 'Cambiar Estado',
                ENT_QUOTES,
                'UTF-8'
            );
            ?>
        </h2>

        <p class="subtitulo">
            Ingrese la fecha efectiva del cambio. El sistema actualizará el estado
            actual y el período correspondiente del historial laboral.
        </p>


        <?php if (!empty($mensaje)): ?>

            <div class="mensaje mensaje-error">
                <?php
                echo htmlspecialchars(
                    $mensaje,
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>
            </div>

        <?php endif; ?>


        <div class="resumen">

            <div class="dato">
                <strong>Empleado</strong>
                <span>
                    <?php
                    echo htmlspecialchars(
                        trim(
                            ($empleado['apellido'] ?? '')
                            . ', '
                            . ($empleado['nombre'] ?? '')
                        ),
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>
                </span>
            </div>

            <div class="dato">
                <strong>Legajo</strong>
                <span>
                    <?php
                    echo htmlspecialchars(
                        $empleado['nro_legajo'] ?? '-',
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>
                </span>
            </div>

            <div class="dato">
                <strong>Estado actual</strong>
                <span
                    class="<?php
                    echo (int)($empleado['activo'] ?? 0) === 1
                        ? 'estado-activo'
                        : 'estado-inactivo';
                    ?>"
                >
                    <?php
                    echo (int)($empleado['activo'] ?? 0) === 1
                        ? 'Activo'
                        : 'Inactivo';
                    ?>
                </span>
            </div>

            <div class="dato">
                <strong>Períodos registrados</strong>
                <span>
                    <?php echo count($periodosLaborales ?? []); ?>
                </span>
            </div>

        </div>


        <?php if (($accion ?? '') === 'inactivar'): ?>

            <div class="aviso aviso-inactivar">
                <strong>Inactivación:</strong>
                se cerrará el período laboral actualmente abierto con la fecha
                efectiva que indique.
            </div>

        <?php else: ?>

            <div class="aviso aviso-activar">
                <strong>Reincorporación:</strong>
                se abrirá un nuevo período laboral desde la fecha efectiva
                indicada y el empleado volverá a quedar activo.
            </div>

        <?php endif; ?>


        <form
            method="POST"
            action="<?php
                echo htmlspecialchars(
                    $empleadoEstadoAccion,
                    ENT_QUOTES,
                    'UTF-8'
                );
            ?>"
        >

            <input
                type="hidden"
                name="_csrf"
                value="<?php
                    echo htmlspecialchars(
                        $empleadoEstadoCsrf,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
            >

            <div class="campo">

                <label for="fecha_efectiva">
                    <?php
                    echo ($accion ?? '') === 'inactivar'
                        ? 'Fecha efectiva de inactivación *'
                        : 'Fecha efectiva de reincorporación *';
                    ?>
                </label>

                <input
                    type="date"
                    id="fecha_efectiva"
                    name="fecha_efectiva"
                    value="<?php
                    echo htmlspecialchars(
                        $fechaEfectiva ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>"
                    max="<?php echo htmlspecialchars($fechaHoy ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                    required
                >

                <div class="ayuda">

                    <?php if (($accion ?? '') === 'inactivar' && !empty($periodoActual['fecha_desde'])): ?>

                        El período laboral actual comenzó el
                        <strong>
                            <?php
                            echo date(
                                'd/m/Y',
                                strtotime(
                                    $periodoActual['fecha_desde']
                                )
                            );
                            ?>
                        </strong>.

                    <?php elseif (($accion ?? '') === 'activar' && !empty($periodosLaborales)): ?>

                        La reincorporación debe ser posterior al último período
                        laboral finalizado.

                    <?php else: ?>

                        Puede utilizar la fecha actual o una fecha anterior válida.

                    <?php endif; ?>

                </div>

            </div>


            <div class="campo">

                <label for="observacion">
                    Motivo / Observación
                </label>

                <textarea
                    id="observacion"
                    name="observacion"
                    maxlength="255"
                    placeholder="<?php
                    echo ($accion ?? '') === 'inactivar'
                        ? 'Ej.: Renuncia voluntaria, finalización de contrato, licencia sin goce...'
                        : 'Ej.: Reincorporación al personal...';
                    ?>"
                ><?php echo htmlspecialchars($observacion ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>

                <div class="ayuda">
                    Opcional. Máximo 255 caracteres.
                </div>

            </div>


            <?php if (!empty($periodosLaborales)): ?>

                <div class="historial-resumen">

                    <h3>Últimos períodos laborales</h3>

                    <div class="tabla-contenedor">

                        <table class="tabla">

                            <thead>
                                <tr>
                                    <th>Desde</th>
                                    <th>Hasta</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>

                            <tbody>

                            <?php
                            $ultimosPeriodos =
                                array_slice(
                                    $periodosLaborales,
                                    -3
                                );
                            ?>

                            <?php foreach ($ultimosPeriodos as $periodo): ?>

                                <tr>

                                    <td>
                                        <?php
                                        echo !empty($periodo['fecha_desde'])
                                            ? date(
                                                'd/m/Y',
                                                strtotime(
                                                    $periodo['fecha_desde']
                                                )
                                            )
                                            : '-';
                                        ?>
                                    </td>

                                    <td>
                                        <?php
                                        echo !empty($periodo['fecha_hasta'])
                                            ? date(
                                                'd/m/Y',
                                                strtotime(
                                                    $periodo['fecha_hasta']
                                                )
                                            )
                                            : 'Actual';
                                        ?>
                                    </td>

                                    <td>

                                        <?php if (empty($periodo['fecha_hasta'])): ?>

                                            <span class="badge badge-actual">
                                                Actual
                                            </span>

                                        <?php else: ?>

                                            <span class="badge badge-finalizado">
                                                Finalizado
                                            </span>

                                        <?php endif; ?>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>

                </div>

            <?php endif; ?>


            <div class="acciones">

                <a
                    href="<?php
                        echo htmlspecialchars(
                            $empleadoEstadoCancelar,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                    class="btn btn-cancelar"
                >
                    Cancelar
                </a>

                <button
                    type="submit"
                    class="btn <?php
                    echo ($accion ?? '') === 'inactivar'
                        ? 'btn-inactivar'
                        : 'btn-activar';
                    ?>"
                >
                    <?php
                    echo ($accion ?? '') === 'inactivar'
                        ? 'Confirmar Inactivación'
                        : 'Confirmar Reincorporación';
                    ?>
                </button>

            </div>

        </form>

    </div>

</div>

</body>

</html>
