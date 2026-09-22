<?php

/*
|--------------------------------------------------------------------------
| CONTROLADOR - MÓDULO AYUDA
|--------------------------------------------------------------------------
*/

require_once __DIR__
    . '/../modelo/AyudaModelo.php';

require_once __DIR__
    . '/../../../core/Url.php';


class AyudaControlador
{
    private $conexion;
    private $modelo;


    /*
    |--------------------------------------------------------------------------
    | CONSTRUCTOR
    |--------------------------------------------------------------------------
    */

    public function __construct($conexion)
    {
        $this->conexion =
            $conexion;

        $this->modelo =
            new AyudaModelo(
                $conexion
            );
    }


    /*
    |--------------------------------------------------------------------------
    | PANTALLA PRINCIPAL / GUÍAS / MANUAL
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | DATOS GENERALES
        |--------------------------------------------------------------------------
        */

        $datosComunes =
            $this->obtenerDatosComunes();


        $soporte =
            $datosComunes['soporte'];


        $sistema =
            $datosComunes['sistema'];


        $usuarioNombre =
            $datosComunes['usuario_nombre'];


        /*
        |--------------------------------------------------------------------------
        | SECCIÓN SOLICITADA
        |--------------------------------------------------------------------------
        |
        | Ejemplos:
        |
        | public/index.php?r=ayuda
        | public/index.php?r=ayuda&seccion=manual
        | public/index.php?r=ayuda&seccion=empleados
        | public/index.php?r=ayuda&seccion=categorias
        | public/index.php?r=ayuda&seccion=conceptos
        | public/index.php?r=ayuda&seccion=conceptos_empleado
        | public/index.php?r=ayuda&seccion=liquidaciones
        | public/index.php?r=ayuda&seccion=reportes
        | public/index.php?r=ayuda&seccion=usuarios
        |
        |--------------------------------------------------------------------------
        */

        $seccion =
            strtolower(
                trim(
                    (string)(
                        $_GET['seccion']
                        ?? ''
                    )
                )
            );


        /*
        |--------------------------------------------------------------------------
        | CATÁLOGO DE VISTAS
        |--------------------------------------------------------------------------
        */

        $guias = [

            'manual' => [
                'titulo' =>
                    'Manual de Usuario',

                'archivo' =>
                    'manual_usuario.php'
            ],

            'empleados' => [
                'titulo' =>
                    'Gestión de Empleados',

                'archivo' =>
                    'guia_empleados.php'
            ],

            'categorias' => [
                'titulo' =>
                    'Gestión de Categorías',

                'archivo' =>
                    'guia_categorias.php'
            ],

            'conceptos' => [
                'titulo' =>
                    'Gestión de Conceptos',

                'archivo' =>
                    'guia_conceptos.php'
            ],

            'conceptos_empleado' => [
                'titulo' =>
                    'Conceptos por Empleado',

                'archivo' =>
                    'guia_conceptos_empleado.php'
            ],

            'liquidaciones' => [
                'titulo' =>
                    'Gestión de Liquidaciones',

                'archivo' =>
                    'guia_liquidaciones.php'
            ],

            'reportes' => [
                'titulo' =>
                    'Consultas y Reportes',

                'archivo' =>
                    'guia_reportes.php'
            ],

            'usuarios' => [
                'titulo' =>
                    'Usuarios, Roles y Permisos',

                'archivo' =>
                    'guia_usuarios.php'
            ]
        ];


        /*
        |--------------------------------------------------------------------------
        | SIN SECCIÓN → CENTRO DE AYUDA
        |--------------------------------------------------------------------------
        */

        if ($seccion === '') {

            require __DIR__
                . '/../vista/ayuda.php';

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDAR SECCIÓN
        |--------------------------------------------------------------------------
        */

        if (
            !isset(
                $guias[$seccion]
            )
        ) {

            $urlAyuda =
                sigenmuniUrlRuta(
                    'ayuda'
                );


            header(
                'Location: '
                .
                $urlAyuda
            );

            exit();
        }


        $guia =
            $guias[$seccion];


        $tituloGuia =
            $guia['titulo'];


        $archivoVista =
            __DIR__
            . '/../vista/'
            . $guia['archivo'];


        /*
        |--------------------------------------------------------------------------
        | VALIDAR QUE EXISTA LA VISTA
        |--------------------------------------------------------------------------
        */

        if (
            !is_file(
                $archivoVista
            )
        ) {

            $guiaPendiente =
                $tituloGuia;


            require __DIR__
                . '/../vista/ayuda.php';

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | DATOS ESPECÍFICOS - GUÍA DE CONCEPTOS
        |--------------------------------------------------------------------------
        |
        | La guía de conceptos obtiene los códigos directamente desde la tabla
        | concepto. De esta manera la documentación queda sincronizada con los
        | conceptos realmente existentes en SIGENMUNI.
        |
        | IMPORTANTE:
        |
        | Un código existente se considera OCUPADO aunque el concepto esté
        | inactivo. Los códigos históricos no deben reutilizarse.
        |
        |--------------------------------------------------------------------------
        */

        $rangosConceptos =
            [];

        $totalConceptosGuia =
            0;

        $errorGuiaConceptos =
            '';


        if ($seccion === 'conceptos') {

            try {

                $datosGuiaConceptos =
                    $this->prepararDatosGuiaConceptos();


                $rangosConceptos =
                    $datosGuiaConceptos['rangos'];


                $totalConceptosGuia =
                    $datosGuiaConceptos['total_conceptos'];


            } catch (Exception $e) {

                $errorGuiaConceptos =
                    $e->getMessage();


                /*
                |--------------------------------------------------------------
                | La vista continúa disponible aunque falle la consulta.
                |--------------------------------------------------------------
                */

                $rangosConceptos =
                    $this->obtenerEstructuraRangosConceptos();
            }
        }


        /*
        |--------------------------------------------------------------------------
        | CARGAR VISTA
        |--------------------------------------------------------------------------
        */

        require $archivoVista;
    }


    /*
    |--------------------------------------------------------------------------
    | PREPARAR DATOS PARA GUÍA DE CONCEPTOS
    |--------------------------------------------------------------------------
    */

    private function prepararDatosGuiaConceptos()
    {
        $conceptos =
            $this->modelo
                ->obtenerConceptosParaGuia();


        $rangos =
            $this->obtenerEstructuraRangosConceptos();


        /*
        |--------------------------------------------------------------------------
        | DISTRIBUIR CONCEPTOS SEGÚN CÓDIGO
        |--------------------------------------------------------------------------
        */

        foreach (
            $conceptos
            as
            $concepto
        ) {

            $codigo =
                (int)(
                    $concepto['codigo']
                    ?? 0
                );


            foreach (
                $rangos
                as
                $clave => $rango
            ) {

                if (
                    $codigo >= $rango['desde']
                    &&
                    $codigo <= $rango['hasta']
                ) {

                    $rangos[$clave]['conceptos'][] =
                        $concepto;


                    $rangos[$clave]['codigos_utilizados'][] =
                        $codigo;


                    break;
                }
            }
        }


        /*
        |--------------------------------------------------------------------------
        | CÓDIGOS DISPONIBLES
        |--------------------------------------------------------------------------
        |
        | Un código está disponible únicamente si NO existe en la tabla concepto.
        | No importa si un concepto existente está activo o inactivo.
        |
        |--------------------------------------------------------------------------
        */

        foreach (
            $rangos
            as
            $clave => $rango
        ) {

            $utilizados =
                array_values(
                    array_unique(
                        array_map(
                            'intval',
                            $rango['codigos_utilizados']
                        )
                    )
                );


            sort(
                $utilizados,
                SORT_NUMERIC
            );


            $ocupados =
                array_fill_keys(
                    $utilizados,
                    true
                );


            $disponibles =
                [];


            for (
                $codigo = $rango['desde'];
                $codigo <= $rango['hasta'];
                $codigo++
            ) {

                if (
                    !isset(
                        $ocupados[$codigo]
                    )
                ) {

                    $disponibles[] =
                        $codigo;
                }
            }


            $rangos[$clave]['codigos_utilizados'] =
                $utilizados;


            $rangos[$clave]['codigos_disponibles'] =
                $disponibles;


            $rangos[$clave]['cantidad_utilizados'] =
                count(
                    $utilizados
                );


            $rangos[$clave]['cantidad_disponibles'] =
                count(
                    $disponibles
                );


            $rangos[$clave]['proximo_codigo_disponible'] =
                !empty(
                    $disponibles
                )
                    ?
                    (int)$disponibles[0]
                    :
                    null;
        }


        return [

            'rangos' =>
                $rangos,

            'total_conceptos' =>
                count(
                    $conceptos
                )
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | ESTRUCTURA DE RANGOS DE CÓDIGOS
    |--------------------------------------------------------------------------
    |
    | 101 - 199 : Haberes y conceptos salariales
    | 201 - 299 : Asignaciones familiares
    | 301 - 399 : Descuentos
    | 401 - 499 : Aportes patronales
    |
    | El primer rango no se denomina únicamente "Remunerativos", porque dentro
    | de ese bloque pueden existir conceptos con otro Tipo de Concepto, como el
    | concepto 112 - No Remunerativo.
    |
    |--------------------------------------------------------------------------
    */

    private function obtenerEstructuraRangosConceptos()
    {
        return [

            'haberes' => [

                'titulo' =>
                    'Haberes y conceptos salariales',

                'desde' =>
                    101,

                'hasta' =>
                    199,

                'descripcion' =>
                    'Rango reservado para conceptos salariales, remunerativos y otros haberes definidos por el sistema.',

                'conceptos' =>
                    [],

                'codigos_utilizados' =>
                    [],

                'codigos_disponibles' =>
                    [],

                'cantidad_utilizados' =>
                    0,

                'cantidad_disponibles' =>
                    99,

                'proximo_codigo_disponible' =>
                    101
            ],


            'asignaciones' => [

                'titulo' =>
                    'Asignaciones familiares',

                'desde' =>
                    201,

                'hasta' =>
                    299,

                'descripcion' =>
                    'Rango reservado para conceptos correspondientes a asignaciones familiares.',

                'conceptos' =>
                    [],

                'codigos_utilizados' =>
                    [],

                'codigos_disponibles' =>
                    [],

                'cantidad_utilizados' =>
                    0,

                'cantidad_disponibles' =>
                    99,

                'proximo_codigo_disponible' =>
                    201
            ],


            'descuentos' => [

                'titulo' =>
                    'Descuentos',

                'desde' =>
                    301,

                'hasta' =>
                    399,

                'descripcion' =>
                    'Rango reservado para aportes personales, descuentos, retenciones y deducciones aplicadas al empleado.',

                'conceptos' =>
                    [],

                'codigos_utilizados' =>
                    [],

                'codigos_disponibles' =>
                    [],

                'cantidad_utilizados' =>
                    0,

                'cantidad_disponibles' =>
                    99,

                'proximo_codigo_disponible' =>
                    301
            ],


            'patronales' => [

                'titulo' =>
                    'Aportes patronales',

                'desde' =>
                    401,

                'hasta' =>
                    499,

                'descripcion' =>
                    'Rango reservado para contribuciones y aportes a cargo del empleador.',

                'conceptos' =>
                    [],

                'codigos_utilizados' =>
                    [],

                'codigos_disponibles' =>
                    [],

                'cantidad_utilizados' =>
                    0,

                'cantidad_disponibles' =>
                    99,

                'proximo_codigo_disponible' =>
                    401
            ]
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | DATOS COMUNES DEL MÓDULO
    |--------------------------------------------------------------------------
    */

    private function obtenerDatosComunes()
    {
        /*
        |--------------------------------------------------------------------------
        | DATOS DE CONTACTO Y SOPORTE
        |--------------------------------------------------------------------------
        */

        $soporte = [

            'responsable' =>
                'Chavez Milton',

            'area' =>
                'Sistema Contable',

            'email' =>
                'chavezmilton082@gmail.com',

            'telefono' =>
                '3704-304781',

            'horario' =>
                '08:00 a 12:00 y 16:00 a 20:00'
        ];


        /*
        |--------------------------------------------------------------------------
        | INFORMACIÓN DEL SISTEMA
        |--------------------------------------------------------------------------
        */

        $sistema = [

            'nombre' =>
                'SIGENMUNI',

            'descripcion' =>
                'Sistema de Gestión Municipal y Liquidación de Haberes',

            'municipio' =>
                'Municipalidad de Fortín Lugones',

            'version' =>
                '1.0'
        ];


        /*
        |--------------------------------------------------------------------------
        | USUARIO ACTUAL
        |--------------------------------------------------------------------------
        */

        $usuarioNombre =
            $_SESSION['nombre_completo']
            ??
            $_SESSION['usuario']
            ??
            'Usuario';


        return [

            'soporte' =>
                $soporte,

            'sistema' =>
                $sistema,

            'usuario_nombre' =>
                $usuarioNombre
        ];
    }
}
