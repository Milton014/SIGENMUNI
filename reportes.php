<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$nombreCompleto = $_SESSION['nombre_completo'] ?? $_SESSION['usuario'];
$rol = $_SESSION['rol'] ?? 'OPERADOR';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGENMUNI - Consultas y Reportes</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --color-principal: #ea580c;
            --color-principal-hover: #c2410c;
            --color-secundario: #fb923c;
            --color-fondo: #f4f7fb;
            --color-texto: #1f2937;
            --color-blanco: #ffffff;
            --color-borde: #e5e7eb;
            --color-sombra: rgba(0, 0, 0, 0.10);
            --color-header: linear-gradient(135deg, #ea580c, #fb923c);
            --color-gris: #6b7280;
            --color-empleados: #0f766e;
            --color-conceptos: #0891b2;
            --color-categorias: #7c3aed;
            --color-liquidaciones: #16a34a;
            --color-recibos: #2563eb;
            --color-historial: #0f766e;
            --color-estadisticas: #f59e0b;
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

        .btn-menu {
            background: #374151;
        }

        .btn-menu:hover {
            background: #1f2937;
            transform: translateY(-2px);
        }

        .btn-inicio {
            background: var(--color-principal);
        }

        .btn-inicio:hover {
            background: var(--color-principal-hover);
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
            box-shadow: 0 0 0 3px rgba(251, 146, 60, 0.18);
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
            min-height: 230px;
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
        .categorias .franja { background: var(--color-categorias); }
        .liquidaciones .franja { background: var(--color-liquidaciones); }
        .recibos .franja { background: var(--color-recibos); }
        .historial .franja { background: var(--color-historial); }
        .estadisticas .franja { background: var(--color-estadisticas); }

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
        .categorias .icono { background: var(--color-categorias); }
        .liquidaciones .icono { background: var(--color-liquidaciones); }
        .recibos .icono { background: var(--color-recibos); }
        .historial .icono { background: var(--color-historial); }
        .estadisticas .icono { background: var(--color-estadisticas); }

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
            background: #fff7ed;
            color: #c2410c;
            padding: 7px 10px;
            border-radius: 999px;
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
        .categorias .btn-modulo { background: var(--color-categorias); }
        .liquidaciones .btn-modulo { background: var(--color-liquidaciones); }
        .recibos .btn-modulo { background: var(--color-recibos); }
        .historial .btn-modulo { background: var(--color-historial); }
        .estadisticas .btn-modulo { background: var(--color-estadisticas); }

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

        @media (max-width: 768px) {
            .titulo-sistema h1 {
                font-size: 24px;
            }

            .bienvenida h2 {
                font-size: 22px;
            }

            .contenedor {
                width: 94%;
            }

            .card-modulo {
                min-height: 210px;
            }
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="header-top">
            <div class="titulo-sistema">
                <h1>SIGENMUNI</h1>
                <p>Sistema de Gestión Municipal - Consultas y Reportes</p>
            </div>

            <div class="usuario-box">
                <div class="nombre"><?php echo htmlspecialchars($nombreCompleto); ?></div>
                <div class="rol">Rol: <?php echo htmlspecialchars($rol); ?></div>
            </div>
        </div>
    </div>

    <div class="contenedor">
        <div class="panel-superior">
            <div class="bienvenida">
                <h2>Consultas y Reportes</h2>
                <p>Seleccioná el tipo de consulta o reporte que querés generar.</p>
            </div>

            <div class="acciones-superiores">
                <a href="reportes.php" class="btn-top btn-inicio">Actualizar</a>
                <a href="index.php" class="btn-top btn-menu">Volver al menú</a>
            </div>
        </div>

        <div class="buscador-box">
            <label for="buscadorModulos">Buscar opción</label>
            <input type="text" id="buscadorModulos" placeholder="Escribí por ejemplo: empleados, conceptos, categorías, liquidaciones, recibos, historial, estadísticas...">
        </div>

        <div class="grid-modulos" id="gridModulos">

            <div class="card-modulo empleados" data-nombre="reporte empleados personal agentes legajos dni listado excel pdf">
                <div class="franja"></div>
                <div>
                    <div class="icono">👤</div>
                    <h3>Reporte de Empleados</h3>
                    <p>Consulta general del personal municipal con filtros y opciones de exportación en PDF y Excel.</p>
                </div>
                <div class="card-footer">
                    <span class="estado">Disponible</span>
                    <a href="reporte_empleados.php" class="btn-modulo">Ingresar</a>
                </div>
            </div>

            <div class="card-modulo historial" data-nombre="historial empleado liquidaciones recibos agente personal legajo">
                <div class="franja"></div>
                <div>
                    <div class="icono">📂</div>
                    <h3>Historial por Empleado</h3>
                    <p>Consulta el historial de liquidaciones y recibos de sueldo de cada agente municipal.</p>
                </div>
                <div class="card-footer">
                    <span class="estado">Disponible</span>
                    <a href="reporte_historial_empleado.php" class="btn-modulo">Ingresar</a>
                </div>
            </div>

            <div class="card-modulo conceptos" data-nombre="reporte conceptos haberes descuentos remunerativos no remunerativos asignaciones listado excel pdf">
                <div class="franja"></div>
                <div>
                    <div class="icono">📘</div>
                    <h3>Reporte de Conceptos</h3>
                    <p>Listado de conceptos del sistema, con filtros por categoría y estado, y exportación en PDF y Excel.</p>
                </div>
                <div class="card-footer">
                    <span class="estado">Disponible</span>
                    <a href="reporte_conceptos.php" class="btn-modulo">Ingresar</a>
                </div>
            </div>

            <div class="card-modulo categorias" data-nombre="reporte categorias categoría escalas basico sueldo listado excel pdf">
                <div class="franja"></div>
                <div>
                    <div class="icono">🏷️</div>
                    <h3>Reporte de Categorías</h3>
                    <p>Consulta de categorías municipales con sus datos principales y exportación en PDF y Excel.</p>
                </div>
                <div class="card-footer">
                    <span class="estado">Disponible</span>
                    <a href="reporte_categorias.php" class="btn-modulo">Ingresar</a>
                </div>
            </div>

            <div class="card-modulo liquidaciones" data-nombre="reporte liquidaciones sueldos periodos resumen neto descuentos liquidacion">
                <div class="franja"></div>
                <div>
                    <div class="icono">💰</div>
                    <h3>Reporte de Liquidaciones</h3>
                    <p>Consulta histórica de liquidaciones realizadas, con acceso al resumen, detalle y recibos por liquidación.</p>
                </div>
                <div class="card-footer">
                    <span class="estado">Disponible</span>
                    <a href="reporte_liquidaciones.php" class="btn-modulo">Ingresar</a>
                </div>
            </div>

            <div class="card-modulo estadisticas" data-nombre="estadisticas gráficos graficos analisis análisis sueldos liquidaciones empleados categorias descuentos neto promedio">
                <div class="franja"></div>
                <div>
                    <div class="icono">📊</div>
                    <h3>Estadísticas</h3>
                    <p>Análisis gráfico de sueldos, empleados, categorías, descuentos y liquidaciones del sistema.</p>
                </div>
                <div class="card-footer">
                    <span class="estado">Nuevo</span>
                    <a href="estadisticas.php" class="btn-modulo">Ingresar</a>
                </div>
            </div>

            <div class="card-modulo recibos" data-nombre="recibos sueldo imprimir recibo empleado liquidacion reimprimir">
                <div class="franja"></div>
                <div>
                    <div class="icono">🧾</div>
                    <h3>Recibos de Sueldo</h3>
                    <p>Acceso a recibos de sueldo individuales para consulta, impresión y generación de recibos por liquidación.</p>
                </div>
                <div class="card-footer">
                    <span class="estado">Integrado</span>
                    <a href="liquidacion.php" class="btn-modulo">Ver liquidaciones</a>
                </div>
            </div>

        </div>

        <div class="sin-resultados" id="sinResultados">
            No se encontraron opciones con ese nombre.
        </div>
    </div>

    <div class="footer">
        SIGENMUNI · Módulo de Consultas y Reportes
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