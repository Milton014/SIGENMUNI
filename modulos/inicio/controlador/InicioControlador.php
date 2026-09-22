<?php

require_once __DIR__
    . '/../modelo/InicioModelo.php';

require_once __DIR__
    . '/../../../core/Url.php';

require_once __DIR__
    . '/../../../core/Csrf.php';


/*
|--------------------------------------------------------------------------
| CONTROLADOR - INICIO
|--------------------------------------------------------------------------
*/

class InicioControlador
{
    private $modelo;


    public function __construct($conexion)
    {
        $this->modelo =
            new InicioModelo(
                $conexion
            );
    }


    /*
    |--------------------------------------------------------------------------
    | MENÚ PRINCIPAL
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | SESIÓN
        |--------------------------------------------------------------------------
        |
        | La ruta "inicio" es una ruta de sesión: cualquier usuario autenticado
        | puede entrar al menú. Los permisos específicos se aplican después al
        | decidir qué tarjetas se muestran.
        |
        */

        verificarSesion();


        $usuarioSesion =
            $_SESSION['usuario']
            ??
            '';


        $nombreCompleto =
            $_SESSION['nombre_completo']
            ??
            $usuarioSesion;


        $rol =
            $_SESSION['rol']
            ??
            '';


        $rolId =
            (int)(
                $_SESSION['rol_id']
                ??
                0
            );


        $idUsuario =
            (int)(
                $_SESSION['id_usuario']
                ??
                0
            );


        /*
        |--------------------------------------------------------------------------
        | VALIDAR ROL ACTUAL
        |--------------------------------------------------------------------------
        */

        $datosRol =
            $this->modelo
                ->obtenerRolPorId(
                    $rolId
                );


        if (!$datosRol) {

            $this->redirigirLogin(
                'sin_permiso'
            );
        }


        if (
            (int)(
                $datosRol['activo']
                ??
                0
            )
            !==
            1
        ) {

            $this->redirigirLogin(
                'rol_inactivo'
            );
        }


        $esAdmin =
            (
                (int)(
                    $datosRol['es_admin']
                    ??
                    0
                )
                ===
                1
                ||
                strtoupper(
                    trim(
                        (string)$rol
                    )
                )
                ===
                'ADMIN'
            );


        /*
        |--------------------------------------------------------------------------
        | MÓDULOS DEL SISTEMA
        |--------------------------------------------------------------------------
        |
        | "archivo" se conserva únicamente como CLAVE DE PERMISO de
        | rol_modulo_permiso. La navegación usa siempre "url" por Router.
        |
        */

        $modulos = [

            [
                "nombre" =>
                    "Gestión de Empleados",

                "archivo" =>
                    "empleados.php",

                "url" =>
                    sigenmuniUrlRuta(
                        "empleados"
                    ),

                "clase" =>
                    "empleados",

                "icono" =>
                    "👤",

                "descripcion" =>
                    "Alta, modificación, consulta y administración del personal municipal.",

                "keywords" =>
                    "gestion de empleados empleados personal alta modificacion consulta",

                "admin" =>
                    false
            ],

            [
                "nombre" =>
                    "Conceptos por Empleado",

                "archivo" =>
                    "empleado_conceptos.php",

                "url" =>
                    sigenmuniUrlRuta(
                        "empleado-conceptos"
                    ),

                "clase" =>
                    "empleados",

                "icono" =>
                    "🧾",

                "descripcion" =>
                    "Asignación de conceptos específicos a cada empleado, con montos, porcentajes, cantidades y vigencias.",

                "keywords" =>
                    "conceptos por empleado empleado conceptos asignacion adicional descuento haberes personal",

                "admin" =>
                    true
            ],

            [
                "nombre" =>
                    "Gestión de Conceptos",

                "archivo" =>
                    "conceptos.php",

                "url" =>
                    sigenmuniUrlRuta(
                        "conceptos"
                    ),

                "clase" =>
                    "conceptos",

                "icono" =>
                    "📘",

                "descripcion" =>
                    "Administración de conceptos remunerativos, no remunerativos, descuentos y aportes.",

                "keywords" =>
                    "gestion de conceptos conceptos codigos haberes descuentos remunerativos",

                "admin" =>
                    true
            ],

            [
                "nombre" =>
                    "Gestión de Categorías",

                "archivo" =>
                    "categorias.php",

                "url" =>
                    sigenmuniUrlRuta(
                        "categorias"
                    ),

                "clase" =>
                    "categorias",

                "icono" =>
                    "🏷️",

                "descripcion" =>
                    "Alta, edición y administración de categorías y cargos municipales.",

                "keywords" =>
                    "gestion de categorias categorías categoria cargos intendente secretario concejal",

                "admin" =>
                    true
            ],

            [
                "nombre" =>
                    "Liquidación",

                "archivo" =>
                    "liquidacion.php",

                "url" =>
                    sigenmuniUrlRuta(
                        "liquidacion"
                    ),

                "clase" =>
                    "liquidacion",

                "icono" =>
                    "💰",

                "descripcion" =>
                    "Generación de liquidaciones, recibos de sueldo y exportación en PDF.",

                "keywords" =>
                    "liquidacion liquidación sueldos recibos pdf haberes",

                "admin" =>
                    true
            ],

            [
                "nombre" =>
                    "Consultas y Reportes",

                "archivo" =>
                    "reportes.php",

                "url" =>
                    sigenmuniUrlRuta(
                        "reportes"
                    ),

                "clase" =>
                    "reportes",

                "icono" =>
                    "📊",

                "descripcion" =>
                    "Consultas históricas, reportes mensuales y análisis de información del sistema.",

                "keywords" =>
                    "consultas reportes historial sueldos estadisticas graficos informes",

                "admin" =>
                    false
            ],

            [
                "nombre" =>
                    "Gestión de Usuarios",

                "archivo" =>
                    "usuarios.php",

                "url" =>
                    sigenmuniUrlRuta(
                        "usuarios"
                    ),

                "clase" =>
                    "usuarios",

                "icono" =>
                    "🔐",

                "descripcion" =>
                    "Administración de usuarios del sistema, roles, estados y accesos.",

                "keywords" =>
                    "gestion de usuarios usuarios seguridad accesos permisos administracion",

                "admin" =>
                    true
            ],

            [
                "nombre" =>
                    "Ayuda",

                "archivo" =>
                    "ayuda.php",

                "url" =>
                    sigenmuniUrlRuta(
                        "ayuda"
                    ),

                "clase" =>
                    "ayuda",

                "icono" =>
                    "❓",

                "descripcion" =>
                    "Manual de uso, orientación general del sistema y asistencia para los módulos.",

                "keywords" =>
                    "ayuda manual soporte preguntas frecuentes informacion",

                "admin" =>
                    false
            ]
        ];


        /*
        |--------------------------------------------------------------------------
        | PERMISOS DEL ROL
        |--------------------------------------------------------------------------
        */

        $permisosRol =
            [];


        if (!$esAdmin) {

            $permisosRol =
                $this->modelo
                    ->obtenerPermisosPorRol(
                        $rolId
                    );
        }


        /*
        |--------------------------------------------------------------------------
        | MÓDULOS VISIBLES
        |--------------------------------------------------------------------------
        */

        $modulosVisibles =
            [];


        foreach (
            $modulos
            as
            $modulo
        ) {

            if ($esAdmin) {

                $modulosVisibles[] =
                    $modulo;

                continue;
            }


            $permitido =
                $permisosRol[
                    $modulo['archivo']
                ]
                ??
                0;


            if (
                (int)$permitido
                ===
                1
            ) {

                $modulosVisibles[] =
                    $modulo;
            }
        }


        $totalModulosVisibles =
            count(
                $modulosVisibles
            );


        /*
        |--------------------------------------------------------------------------
        | LOGOUT POR ROUTER
        |--------------------------------------------------------------------------
        */

        $inicioUrlLogout =
            sigenmuniUrlRuta(
                'logout'
            );


        $inicioCsrfLogout =
            sigenmuniCsrfToken();


        /*
        |--------------------------------------------------------------------------
        | VISTA
        |--------------------------------------------------------------------------
        */

        require __DIR__
            . '/../vista/inicio.php';
    }


    /*
    |--------------------------------------------------------------------------
    | REDIRECCIÓN AL LOGIN
    |--------------------------------------------------------------------------
    */

    private function redirigirLogin($error)
    {
        $url =
            sigenmuniUrlRuta(
                'login',
                [
                    'error' =>
                        (string)$error
                ]
            );


        header(
            'Location: '
            .
            $url
        );

        exit();
    }
}
