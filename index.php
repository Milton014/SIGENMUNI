<?php
session_start();

require_once("conexion.php");
require_once("seguridad.php");

verificarSesion();

$nombreCompleto = $_SESSION['nombre_completo'] ?? $_SESSION['usuario'];
$rol = $_SESSION['rol'] ?? 'OPERADOR';

/*
|--------------------------------------------------------------------------
| MODULOS DEL SISTEMA
|--------------------------------------------------------------------------
*/

$modulos = [

    [
        "nombre" => "Gestión de Empleados",
        "archivo" => "empleados.php",
        "clase" => "empleados",
        "icono" => "👤",
        "descripcion" => "Alta, modificación, consulta y administración del personal municipal.",
        "keywords" => "gestion de empleados empleados personal alta modificacion consulta",
        "admin" => false
    ],

    [
        "nombre" => "Conceptos por Empleado",
        "archivo" => "empleado_conceptos.php",
        "clase" => "empleados",
        "icono" => "🧾",
        "descripcion" => "Asignación de conceptos específicos a cada empleado, con montos, porcentajes, cantidades y vigencias.",
        "keywords" => "conceptos por empleado empleado conceptos asignacion adicional descuento haberes personal",
        "admin" => true
    ],

    [
        "nombre" => "Gestión de Conceptos",
        "archivo" => "conceptos.php",
        "clase" => "conceptos",
        "icono" => "📘",
        "descripcion" => "Administración de conceptos remunerativos, no remunerativos, descuentos y aportes.",
        "keywords" => "gestion de conceptos conceptos codigos haberes descuentos remunerativos",
        "admin" => true
    ],

    [
        "nombre" => "Liquidación",
        "archivo" => "liquidacion.php",
        "clase" => "liquidacion",
        "icono" => "💰",
        "descripcion" => "Generación de liquidaciones, recibos de sueldo y exportación en PDF.",
        "keywords" => "liquidacion liquidación sueldos recibos pdf haberes",
        "admin" => true
    ],

    [
        "nombre" => "Consultas y Reportes",
        "archivo" => "reportes.php",
        "clase" => "reportes",
        "icono" => "📊",
        "descripcion" => "Consultas históricas, reportes mensuales y análisis de información del sistema.",
        "keywords" => "consultas reportes historial sueldos estadisticas graficos informes",
        "admin" => false
    ],

    [
        "nombre" => "Gestión de Usuarios",
        "archivo" => "usuarios.php",
        "clase" => "usuarios",
        "icono" => "🔐",
        "descripcion" => "Administración de usuarios del sistema, roles, estados y accesos.",
        "keywords" => "gestion de usuarios usuarios seguridad accesos permisos administracion",
        "admin" => true
    ],

    [
        "nombre" => "Ayuda",
        "archivo" => "ayuda.php",
        "clase" => "ayuda",
        "icono" => "❓",
        "descripcion" => "Manual de uso, orientación general del sistema y asistencia para los módulos.",
        "keywords" => "ayuda manual soporte preguntas frecuentes informacion",
        "admin" => false
    ]
];

/*
|--------------------------------------------------------------------------
| CARGAR PERMISOS
|--------------------------------------------------------------------------
*/

$permisosOperador = [];

$resultadoPermisos = $conexion->query("
    SELECT archivo_principal, permitido_operador
    FROM modulo_permiso
");

while ($fila = $resultadoPermisos->fetch_assoc()) {
    $permisosOperador[$fila['archivo_principal']] = (int)$fila['permitido_operador'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGENMUNI - Menú Principal</title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --color-principal: #0f766e;
            --color-principal-hover: #115e59;
            --color-secundario: #14b8a6;
            --color-fondo: #f4f7fb;
            --color-texto: #1f2937;
            --color-blanco: #ffffff;
            --color-borde: #e5e7eb;
            --color-sombra: rgba(0, 0, 0, 0.10);
            --color-header: linear-gradient(135deg, #0f766e, #14b8a6);
            --color-gris: #6b7280;
            --color-ayuda: #2563eb;
            --color-usuarios: #7c3aed;
            --color-reportes: #ea580c;
            --color-conceptos: #0891b2;
            --color-liquidacion: #16a34a;
            --color-empleados: #0f766e;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: var(--color-fondo);
            color: var(--color-texto);
        }

        .header {
            background: var(--color-header);
            color: white;
            padding: 22px 30px;
            box-shadow: 0 4px 14px var(--color-sombra);
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .titulo-sistema h1 {
            font-size: 30px;
            margin-bottom: 4px;
            letter-spacing: 0.5px;
        }

        .titulo-sistema p {
            font-size: 14px;
            opacity: 0.95;
        }

        .usuario-box {
            background: rgba(255,255,255,0.15);
            padding: 12px 16px;
            border-radius: 14px;
            min-width: 280px;
            backdrop-filter: blur(4px);
        }

        .usuario-box .nombre {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .usuario-box .rol {
            font-size: 13px;
            opacity: 0.95;
        }

        .contenedor {
            width: 92%;
            max-width: 1250px;
            margin: 30px auto;
        }

        .panel-superior {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 25px;
        }

        .bienvenida h2 {
            font-size: 28px;
            margin-bottom: 6px;
        }

        .bienvenida p {
            color: var(--color-gris);
            font-size: 15px;
        }

        .acciones-superiores {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-top {
            text-decoration: none;
            padding: 12px 18px;
            border-radius: 12px;
            color: white;
            font-weight: bold;
            transition: 0.25s ease;
            box-shadow: 0 6px 14px var(--color-sombra);
        }

        .btn-inicio {
            background: var(--color-principal);
        }

        .btn-inicio:hover {
            background: var(--color-principal-hover);
            transform: translateY(-2px);
        }

        .btn-logout {
            background: #1f2937;
        }

        .btn-logout:hover {
            background: #111827;
            transform: translateY(-2px);
        }

        .buscador-box {
            background: var(--color-blanco);
            padding: 18px;
            border-radius: 18px;
            box-shadow: 0 8px 20px var(--color-sombra);
            margin-bottom: 28px;
            border: 1px solid var(--color-borde);
        }

        .buscador-box label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .buscador-box input {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            font-size: 15px;
            outline: none;
            transition: 0.2s;
        }

        .buscador-box input:focus {
            border-color: var(--color-secundario);
            box-shadow: 0 0 0 3px rgba(20, 184, 166, 0.15);
        }

        .grid-modulos {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 22px;
        }

        .card-modulo {
            position: relative;
            background: var(--color-blanco);
            border-radius: 22px;
            padding: 24px 22px;
            box-shadow: 0 10px 24px var(--color-sombra);
            border: 1px solid var(--color-borde);
            transition: 0.25s ease;
            overflow: hidden;
            min-height: 220px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .card-modulo:hover {
            transform: translateY(-7px);
            box-shadow: 0 18px 32px rgba(0,0,0,0.14);
        }

        .card-modulo.oculto {
            display: none;
        }

        .franja {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 8px;
        }

        .empleados .franja { background: var(--color-empleados); }
        .conceptos .franja { background: var(--color-conceptos); }
        .liquidacion .franja { background: var(--color-liquidacion); }
        .reportes .franja { background: var(--color-reportes); }
        .usuarios .franja { background: var(--color-usuarios); }
        .ayuda .franja { background: var(--color-ayuda); }

        .icono {
            width: 58px;
            height: 58px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-bottom: 18px;
            color: white;
        }

        .empleados .icono { background: var(--color-empleados); }
        .conceptos .icono { background: var(--color-conceptos); }
        .liquidacion .icono { background: var(--color-liquidacion); }
        .reportes .icono { background: var(--color-reportes); }
        .usuarios .icono { background: var(--color-usuarios); }
        .ayuda .icono { background: var(--color-ayuda); }

        .card-modulo h3 {
            font-size: 21px;
            margin-bottom: 10px;
        }

        .card-modulo p {
            color: var(--color-gris);
            line-height: 1.45;
            font-size: 14px;
            margin-bottom: 18px;
        }

        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            margin-top: 8px;
        }

        .estado {
            font-size: 12px;
            font-weight: bold;
            background: #ecfdf5;
            color: #166534;
            padding: 7px 10px;
            border-radius: 999px;
        }

        .estado-admin {
            background: #fef3c7;
            color: #92400e;
        }

        .btn-modulo {
            text-decoration: none;
            color: white;
            font-weight: bold;
            padding: 10px 14px;
            border-radius: 10px;
            transition: 0.2s;
            display: inline-block;
        }

        .empleados .btn-modulo { background: var(--color-empleados); }
        .conceptos .btn-modulo { background: var(--color-conceptos); }
        .liquidacion .btn-modulo { background: var(--color-liquidacion); }
        .reportes .btn-modulo { background: var(--color-reportes); }
        .usuarios .btn-modulo { background: var(--color-usuarios); }
        .ayuda .btn-modulo { background: var(--color-ayuda); }

        .btn-modulo:hover {
            opacity: 0.92;
            transform: scale(1.03);
        }

        .sin-resultados {
            display: none;
            background: white;
            padding: 18px;
            border-radius: 16px;
            margin-top: 20px;
            text-align: center;
            color: var(--color-gris);
            box-shadow: 0 8px 20px var(--color-sombra);
        }

        .footer {
            text-align: center;
            padding: 30px 20px 40px;
            color: var(--color-gris);
            font-size: 13px;
        }

        /* =========================================
           RESPONSIVE TABLET
        ========================================= */

        @media (max-width: 992px) {

            .contenedor {
                width: 95%;
            }

            .grid-modulos {
                grid-template-columns: repeat(2, 1fr);
            }

            .header-top {
                flex-direction: column;
                align-items: flex-start;
            }

            .usuario-box {
                width: 100%;
            }
        }

        /* =========================================
           RESPONSIVE CELULAR
        ========================================= */

        @media (max-width: 768px) {

            .header {
                padding: 20px;
            }

            .header-top {
                flex-direction: column;
                align-items: stretch;
                gap: 15px;
            }

            .titulo-sistema h1 {
                font-size: 24px;
            }

            .titulo-sistema p {
                font-size: 13px;
                line-height: 1.5;
            }

            .usuario-box {
                width: 100%;
                min-width: auto;
                padding: 14px;
            }

            .usuario-box .nombre {
                font-size: 15px;
            }

            .usuario-box .rol {
                font-size: 12px;
            }

            .contenedor {
                width: 94%;
                margin: 20px auto;
            }

            .panel-superior {
                flex-direction: column;
                align-items: stretch;
                gap: 20px;
            }

            .bienvenida h2 {
                font-size: 24px;
            }

            .bienvenida p {
                font-size: 14px;
                line-height: 1.5;
            }

            .acciones-superiores {
                flex-direction: column;
                align-items: stretch;
            }

            .btn-top {
                width: 100%;
                text-align: center;
            }

            .buscador-box {
                padding: 16px;
                border-radius: 16px;
            }

            .buscador-box input {
                font-size: 14px;
                padding: 13px;
            }

            .grid-modulos {
                grid-template-columns: 1fr;
                gap: 18px;
            }

            .card-modulo {
                min-height: auto;
                padding: 22px 18px;
                border-radius: 18px;
            }

            .icono {
                width: 52px;
                height: 52px;
                font-size: 24px;
                margin-bottom: 14px;
            }

            .card-modulo h3 {
                font-size: 20px;
            }

            .card-modulo p {
                font-size: 14px;
                line-height: 1.5;
            }

            .card-footer {
                flex-direction: column;
                align-items: stretch;
                gap: 12px;
            }

            .estado,
            .estado-admin {
                text-align: center;
            }

            .btn-modulo {
                width: 100%;
                text-align: center;
            }

            .footer {
                padding: 25px 15px 35px;
                font-size: 12px;
            }
        }

        /* =========================================
           CELULARES PEQUEÑOS
        ========================================= */

        @media (max-width: 480px) {

            .header {
                padding: 18px 16px;
            }

            .titulo-sistema h1 {
                font-size: 22px;
            }

            .bienvenida h2 {
                font-size: 22px;
            }

            .buscador-box {
                padding: 14px;
            }

            .card-modulo {
                padding: 18px 16px;
            }

            .card-modulo h3 {
                font-size: 18px;
            }

            .card-modulo p {
                font-size: 13px;
            }

            .btn-top,
            .btn-modulo {
                font-size: 13px;
                padding: 11px;
            }

            .estado,
            .estado-admin {
                font-size: 11px;
            }
        }

    </style>
</head>

<body>

<div class="header">

    <div class="header-top">

        <div class="titulo-sistema">
            <h1>SIGENMUNI</h1>
            <p>Sistema de Gestión Municipal - Municipalidad de Fortín Lugones</p>
        </div>

        <div class="usuario-box">
            <div class="nombre">
                <?php echo htmlspecialchars($nombreCompleto); ?>
            </div>

            <div class="rol">
                Rol: <?php echo htmlspecialchars($rol); ?>
            </div>
        </div>

    </div>

</div>

<div class="contenedor">

    <div class="panel-superior">

        <div class="bienvenida">
            <h2>Menú Principal</h2>
            <p>Seleccioná un módulo para comenzar a trabajar en el sistema.</p>
        </div>

        <div class="acciones-superiores">
            <a href="index.php" class="btn-top btn-inicio">
                Actualizar menú
            </a>

            <a href="logout.php" class="btn-top btn-logout">
                Cerrar sesión
            </a>
        </div>

    </div>

    <div class="buscador-box">

        <label for="buscadorModulos">
            Buscar módulo
        </label>

        <input
            type="text"
            id="buscadorModulos"
            placeholder="Escribí por ejemplo: empleados, reportes, ayuda..."
        >

    </div>

    <div class="grid-modulos" id="gridModulos">

        <?php foreach ($modulos as $modulo) { ?>

        <?php

        $mostrar = false;

        if ($rol === 'ADMIN') {
            $mostrar = true;
        } else {
            $mostrar =
                isset($permisosOperador[$modulo['archivo']]) &&
                $permisosOperador[$modulo['archivo']] == 1;
        }

        if (!$mostrar) {
            continue;
        }

        ?>

        <div class="card-modulo <?php echo $modulo['clase']; ?>"
             data-nombre="<?php echo $modulo['keywords']; ?>">

            <div class="franja"></div>

            <div>

                <div class="icono">
                    <?php echo $modulo['icono']; ?>
                </div>

                <h3>
                    <?php echo $modulo['nombre']; ?>
                </h3>

                <p>
                    <?php echo $modulo['descripcion']; ?>
                </p>

            </div>

            <div class="card-footer">

                <?php if ($modulo['admin']) { ?>

                    <span class="estado estado-admin">
                        Configurable
                    </span>

                <?php } else { ?>

                    <span class="estado">
                        Disponible
                    </span>

                <?php } ?>

                <a href="<?php echo $modulo['archivo']; ?>"
                   class="btn-modulo">
                    Ingresar
                </a>

            </div>

        </div>

        <?php } ?>

    </div>

    <div class="sin-resultados" id="sinResultados">
        No se encontraron módulos con ese nombre.
    </div>

</div>

<div class="footer">
    SIGENMUNI · Sistema de Gestión Municipal
</div>

<script>

    const buscador = document.getElementById('buscadorModulos');
    const tarjetas = document.querySelectorAll('.card-modulo');
    const sinResultados = document.getElementById('sinResultados');

    buscador.addEventListener('input', function () {

        const texto = this.value.toLowerCase().trim();
        let visibles = 0;

        tarjetas.forEach(tarjeta => {

            const nombre = tarjeta.getAttribute('data-nombre').toLowerCase();

            if (nombre.includes(texto)) {
                tarjeta.classList.remove('oculto');
                visibles++;
            } else {
                tarjeta.classList.add('oculto');
            }
        });

        sinResultados.style.display = visibles === 0 ? 'block' : 'none';
    });

</script>

</body>
</html>