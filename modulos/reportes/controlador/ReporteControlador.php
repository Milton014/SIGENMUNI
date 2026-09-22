<?php

require_once __DIR__ . '/../modelo/ReporteModelo.php';


class ReporteControlador
{
    private $modelo;
    private $conexion;


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
            new ReporteModelo(
                $conexion
            );
    }


    /*
    |--------------------------------------------------------------------------
    | MENÚ PRINCIPAL DE REPORTES
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $nombreCompleto =
            $_SESSION['nombre_completo']
            ??
            $_SESSION['usuario']
            ??
            'Usuario';


        $rol =
            $_SESSION['rol']
            ??
            '';


        $rolId =
            (int)(
                $_SESSION['rol_id']
                ?? 0
            );


        /*
        |--------------------------------------------------------------------------
        | PERMISOS DEL MENÚ DE REPORTES
        |--------------------------------------------------------------------------
        |
        | - ADMIN activo: acceso total.
        | - Otros roles: solo reportes con permitido = 1.
        |
        |--------------------------------------------------------------------------
        */

        $esAdminReportes =
            false;


        $permisosReportes =
            [];


        if ($rolId > 0) {

            $stmtRol =
                $this->conexion->prepare(
                    "
                    SELECT
                        nombre,
                        activo,
                        es_admin
                    FROM rol
                    WHERE id = ?
                    LIMIT 1
                    "
                );


            if (!$stmtRol) {

                die(
                    "Error al preparar la consulta del rol: "
                    .
                    htmlspecialchars(
                        $this->conexion->error,
                        ENT_QUOTES,
                        'UTF-8'
                    )
                );
            }


            $stmtRol->bind_param(
                "i",
                $rolId
            );


            if (!$stmtRol->execute()) {

                $errorRol =
                    $stmtRol->error;

                $stmtRol->close();

                die(
                    "Error al consultar el rol: "
                    .
                    htmlspecialchars(
                        $errorRol,
                        ENT_QUOTES,
                        'UTF-8'
                    )
                );
            }


            $resultadoRol =
                $stmtRol->get_result();


            if (
                $resultadoRol
                &&
                $resultadoRol->num_rows > 0
            ) {

                $datosRol =
                    $resultadoRol->fetch_assoc();


                $nombreRol =
                    strtoupper(
                        trim(
                            (string)(
                                $datosRol['nombre']
                                ?? ''
                            )
                        )
                    );


                $esAdminReportes =
                    (
                        (int)(
                            $datosRol['activo']
                            ?? 0
                        )
                        ===
                        1
                        &&
                        (
                            (int)(
                                $datosRol['es_admin']
                                ?? 0
                            )
                            ===
                            1
                            ||
                            $nombreRol === 'ADMIN'
                        )
                    );
            }


            $stmtRol->close();


            if (!$esAdminReportes) {

                $stmtPermisos =
                    $this->conexion->prepare(
                        "
                        SELECT
                            archivo,
                            permitido
                        FROM rol_modulo_permiso
                        WHERE rol_id = ?
                        "
                    );


                if (!$stmtPermisos) {

                    die(
                        "Error al preparar la consulta de permisos: "
                        .
                        htmlspecialchars(
                            $this->conexion->error,
                            ENT_QUOTES,
                            'UTF-8'
                        )
                    );
                }


                $stmtPermisos->bind_param(
                    "i",
                    $rolId
                );


                if (!$stmtPermisos->execute()) {

                    $errorPermisos =
                        $stmtPermisos->error;

                    $stmtPermisos->close();

                    die(
                        "Error al consultar los permisos de reportes: "
                        .
                        htmlspecialchars(
                            $errorPermisos,
                            ENT_QUOTES,
                            'UTF-8'
                        )
                    );
                }


                $resultadoPermisos =
                    $stmtPermisos->get_result();


                while (
                    $fila =
                        $resultadoPermisos->fetch_assoc()
                ) {

                    $permisosReportes[
                        (string)$fila['archivo']
                    ] =
                        (int)$fila['permitido'];
                }


                $stmtPermisos->close();
            }
        }


        $puedeVerReporte =
            function($archivo)
            use (
                $esAdminReportes,
                $permisosReportes
            ) {

                if ($esAdminReportes) {

                    return true;
                }


                return (
                    isset(
                        $permisosReportes[
                            $archivo
                        ]
                    )
                    &&
                    (int)$permisosReportes[
                        $archivo
                    ]
                    ===
                    1
                );
            };


        $reportesVisibles = [

            'reporte_empleados.php' =>
                $puedeVerReporte(
                    'reporte_empleados.php'
                ),

            'reporte_historial_empleado.php' =>
                $puedeVerReporte(
                    'reporte_historial_empleado.php'
                ),

            'reporte_conceptos.php' =>
                $puedeVerReporte(
                    'reporte_conceptos.php'
                ),

            'reporte_categorias.php' =>
                $puedeVerReporte(
                    'reporte_categorias.php'
                ),

            'reporte_liquidaciones.php' =>
                $puedeVerReporte(
                    'reporte_liquidaciones.php'
                ),

            'estadisticas.php' =>
                $puedeVerReporte(
                    'estadisticas.php'
                ),

            'reporte_auditoria.php' =>
                $puedeVerReporte(
                    'reporte_auditoria.php'
                )
        ];


        $totalReportesVisibles =
            count(
                array_filter(
                    $reportesVisibles
                )
            );


        require __DIR__
            . '/../vista/reportes.php';
    }


    /*
    |--------------------------------------------------------------------------
    | REPORTE DE EMPLEADOS
    |--------------------------------------------------------------------------
    |
    | Reporte de solo consulta.
    |
    | Filtros:
    | - Búsqueda general
    | - Estado
    |
    | No incluye acciones de gestión, edición o liquidación.
    |
    |--------------------------------------------------------------------------
    */

    public function empleados()
    {
        $legajo =
            trim(
                $_GET['legajo']
                ?? ''
            );


        $busqueda =
            trim(
                $_GET['busqueda']
                ?? ''
            );


        $estado =
            trim(
                $_GET['estado']
                ?? ''
            );


        if (
            $legajo !== ''
            &&
            !ctype_digit(
                $legajo
            )
        ) {

            $legajo = '';
        }


        if (
            $estado !== ''
            &&
            $estado !== '1'
            &&
            $estado !== '0'
        ) {

            $estado = '';
        }


        $error = "";
        $empleados = [];
        $totalEmpleados = 0;


        try {

            /*
            |--------------------------------------------------------------------------
            | EL MODELO ACTUAL TIENE BÚSQUEDA GENERAL
            |--------------------------------------------------------------------------
            |
            | Si se informa legajo, primero buscamos por texto y luego forzamos
            | coincidencia exacta de nro_legajo.
            |
            |--------------------------------------------------------------------------
            */

            /*
            |--------------------------------------------------------------------------
            | FILTROS
            |--------------------------------------------------------------------------
            |
            | listarEmpleados() recibe:
            |
            | 1. legajo exacto
            | 2. búsqueda general
            | 3. estado: '' / '1' / '0'
            |
            | Es importante no desplazar estos argumentos porque '0' es un valor
            | válido para empleados inactivos.
            |
            */

            $empleados =
                $this->modelo
                    ->listarEmpleados(
                        $legajo,
                        $busqueda,
                        $estado
                    );


            $totalEmpleados =
                count(
                    $empleados
                );


        } catch (Exception $e) {

            $error =
                $e->getMessage();

            $empleados = [];
            $totalEmpleados = 0;
        }


        $queryString =
            http_build_query(
                [
                    'legajo' =>
                        $legajo,

                    'busqueda' =>
                        $busqueda,

                    'estado' =>
                        $estado
                ]
            );


        require __DIR__
            . '/../vista/reporte_empleados.php';
    }



    /*
    |--------------------------------------------------------------------------
    | REPORTE DE EMPLEADOS - IMPRESIÓN / PDF
    |--------------------------------------------------------------------------
    */

    public function empleadosPdf()
    {
        $legajo =
            trim(
                (string)(
                    $_GET['legajo']
                    ?? ''
                )
            );


        $busqueda =
            trim(
                (string)(
                    $_GET['busqueda']
                    ?? ''
                )
            );


        $estado =
            trim(
                (string)(
                    $_GET['estado']
                    ?? ''
                )
            );


        if (
            $legajo !== ''
            &&
            !ctype_digit(
                $legajo
            )
        ) {

            $legajo = '';
        }


        if (
            $estado !== ''
            &&
            $estado !== '1'
            &&
            $estado !== '0'
        ) {

            $estado = '';
        }


        try {

            $empleados =
                $this->modelo
                    ->listarEmpleados(
                        $legajo,
                        $busqueda,
                        $estado
                    );


        } catch (Exception $e) {

            die(
                "Error al generar el reporte: "
                .
                htmlspecialchars(
                    $e->getMessage(),
                    ENT_QUOTES,
                    'UTF-8'
                )
            );
        }


        $totalEmpleados =
            count(
                $empleados
            );


        require __DIR__
            . '/../vista/reporte_empleados_pdf.php';
    }



    /*
    |--------------------------------------------------------------------------
    | REPORTE DE EMPLEADOS - EXPORTAR EXCEL
    |--------------------------------------------------------------------------
    */

    public function empleadosExcel()
    {
        $legajo =
            trim(
                (string)(
                    $_GET['legajo']
                    ?? ''
                )
            );


        $busqueda =
            trim(
                (string)(
                    $_GET['busqueda']
                    ?? ''
                )
            );


        $estado =
            trim(
                (string)(
                    $_GET['estado']
                    ?? ''
                )
            );


        if (
            $legajo !== ''
            &&
            !ctype_digit(
                $legajo
            )
        ) {

            $legajo = '';
        }


        if (
            $estado !== ''
            &&
            $estado !== '1'
            &&
            $estado !== '0'
        ) {

            $estado = '';
        }


        try {

            $empleados =
                $this->modelo
                    ->listarEmpleados(
                        $legajo,
                        $busqueda,
                        $estado
                    );


        } catch (Exception $e) {

            die(
                "Error al generar el reporte Excel: "
                .
                htmlspecialchars(
                    $e->getMessage(),
                    ENT_QUOTES,
                    'UTF-8'
                )
            );
        }


        $totalEmpleados =
            count(
                $empleados
            );


        require __DIR__
            . '/../vista/reporte_empleados_excel.php';
    }


    /*
    |--------------------------------------------------------------------------
    | HISTORIAL POR EMPLEADO
    |--------------------------------------------------------------------------
    */

    public function historialEmpleado()
    {
        $empleadoId =
            isset($_GET['empleado_id'])
                ?
                (int)$_GET['empleado_id']
                :
                0;


        $legajo =
            trim(
                $_GET['legajo']
                ?? ''
            );


        $busqueda =
            trim(
                $_GET['busqueda']
                ?? ''
            );


        $estado =
            trim(
                $_GET['estado']
                ?? ''
            );


        /*
        |--------------------------------------------------------------------------
        | BÚSQUEDA SOLICITADA
        |--------------------------------------------------------------------------
        |
        | La pantalla inicial no lista empleados automáticamente.
        |
        | Cuando el usuario presiona Buscar, la vista envía filtrar=1.
        | De esta forma:
        |
        | - primera entrada: no consulta;
        | - Todos + Buscar: consulta todos;
        | - Activos + Buscar: filtra activo = 1;
        | - Inactivos + Buscar: filtra activo = 0.
        |
        | Se mantiene compatibilidad con URLs anteriores que ya tengan alguno
        | de los filtros informados aunque no incluyan filtrar=1.
        |
        */

        $filtrar =
            (
                isset($_GET['filtrar'])
                &&
                (string)$_GET['filtrar'] === '1'
            )
            ||
            $legajo !== ''
            ||
            $busqueda !== ''
            ||
            $estado !== '';


        if (
            $legajo !== ''
            &&
            !ctype_digit(
                $legajo
            )
        ) {

            $legajo = '';
        }


        if (
            $estado !== ''
            &&
            $estado !== '1'
            &&
            $estado !== '0'
        ) {

            $estado = '';
        }


        $error = "";
        $empleados = [];
        $empleado = null;

        /*
        |--------------------------------------------------------------------------
        | HISTORIAL LABORAL
        |--------------------------------------------------------------------------
        */

        $historialLaboral = [];

        $resumenHistorialLaboral = [
            'cantidad_periodos' => 0,
            'periodos_finalizados' => 0,
            'tiene_periodo_abierto' => 0,
            'ultima_incorporacion' => null,
            'ultima_inactivacion' => null,
            'total_dias_registrados' => 0
        ];


        /*
        |--------------------------------------------------------------------------
        | HISTORIAL DE LIQUIDACIONES
        |--------------------------------------------------------------------------
        */

        $historial = [];
        $queryStringHistorial = "";


        try {

            /*
            |--------------------------------------------------------------------------
            | MODO HISTORIAL DE UN EMPLEADO
            |--------------------------------------------------------------------------
            */

            if ($empleadoId > 0) {

                $empleado =
                    $this->modelo
                        ->obtenerEmpleadoPorId(
                            $empleadoId
                        );


                if (!$empleado) {

                    throw new Exception(
                        "El empleado seleccionado no existe."
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | HISTORIAL LABORAL
                |--------------------------------------------------------------------------
                */

                $historialLaboral =
                    $this->modelo
                        ->obtenerHistorialLaboralEmpleado(
                            $empleadoId
                        );


                $resumenHistorialLaboral =
                    $this->modelo
                        ->obtenerResumenHistorialLaboralEmpleado(
                            $empleadoId
                        );


                /*
                |--------------------------------------------------------------------------
                | HISTORIAL DE LIQUIDACIONES
                |--------------------------------------------------------------------------
                */

                $historial =
                    $this->modelo
                        ->obtenerHistorialEmpleado(
                            $empleadoId
                        );


                $queryStringHistorial =
                    http_build_query(
                        [
                            'empleado_id' =>
                                $empleadoId
                        ]
                    );


            /*
            |--------------------------------------------------------------------------
            | MODO BUSCADOR
            |--------------------------------------------------------------------------
            */

            } elseif ($filtrar) {

                /*
                |--------------------------------------------------------------------------
                | BUSCADOR DE HISTORIAL
                |--------------------------------------------------------------------------
                |
                | Se utiliza el método específico del modelo para evitar desplazar
                | parámetros. El estado '0' se conserva como filtro válido.
                |
                | Si los tres filtros están vacíos pero filtrar=1, el modelo recibe
                | ('', '', '') y devuelve todos los empleados.
                |
                */

                $empleados =
                    $this->modelo
                        ->buscarEmpleadosHistorial(
                            $legajo,
                            $busqueda,
                            $estado
                        );
            }


        } catch (Exception $e) {

            $error =
                $e->getMessage();


            if ($empleadoId <= 0) {

                $empleados = [];

            } else {

                $historialLaboral = [];

                $resumenHistorialLaboral = [
                    'cantidad_periodos' => 0,
                    'periodos_finalizados' => 0,
                    'tiene_periodo_abierto' => 0,
                    'ultima_incorporacion' => null,
                    'ultima_inactivacion' => null,
                    'total_dias_registrados' => 0
                ];

                $historial = [];
            }
        }


        require __DIR__
            . '/../vista/reporte_historial_empleado.php';
    }



    /*
    |--------------------------------------------------------------------------
    | HISTORIAL POR EMPLEADO - IMPRESIÓN / PDF
    |--------------------------------------------------------------------------
    */

    public function historialEmpleadoPdf()
    {
        $empleadoId =
            isset($_GET['empleado_id'])
                ? (int)$_GET['empleado_id']
                : 0;


        if ($empleadoId <= 0) {

            die(
                "Empleado inválido."
            );
        }


        try {

            $empleado =
                $this->modelo
                    ->obtenerEmpleadoPorId(
                        $empleadoId
                    );


            if (!$empleado) {

                die(
                    "No se encontró el empleado solicitado."
                );
            }


            $historial =
                $this->modelo
                    ->obtenerHistorialEmpleado(
                        $empleadoId
                    );


        } catch (Exception $e) {

            die(
                "Error al generar el historial: "
                .
                htmlspecialchars(
                    $e->getMessage(),
                    ENT_QUOTES,
                    'UTF-8'
                )
            );
        }


        $totalLiquidaciones =
            count(
                $historial
            );


        require __DIR__
            . '/../vista/reporte_historial_empleado_pdf.php';
    }



    /*
    |--------------------------------------------------------------------------
    | HISTORIAL POR EMPLEADO - EXPORTAR EXCEL
    |--------------------------------------------------------------------------
    */

    public function historialEmpleadoExcel()
    {
        $empleadoId =
            isset($_GET['empleado_id'])
                ? (int)$_GET['empleado_id']
                : 0;


        if ($empleadoId <= 0) {

            die(
                "Empleado inválido."
            );
        }


        try {

            $empleado =
                $this->modelo
                    ->obtenerEmpleadoPorId(
                        $empleadoId
                    );


            if (!$empleado) {

                die(
                    "No se encontró el empleado solicitado."
                );
            }


            $historial =
                $this->modelo
                    ->obtenerHistorialEmpleado(
                        $empleadoId
                    );


        } catch (Exception $e) {

            die(
                "Error al generar el historial Excel: "
                .
                htmlspecialchars(
                    $e->getMessage(),
                    ENT_QUOTES,
                    'UTF-8'
                )
            );
        }


        $totalLiquidaciones =
            count(
                $historial
            );


        require __DIR__
            . '/../vista/reporte_historial_empleado_excel.php';
    }


    /*
    |--------------------------------------------------------------------------
    | REPORTE DE CONCEPTOS
    |--------------------------------------------------------------------------
    */

    public function conceptos()
    {
        /*
        |--------------------------------------------------------------------------
        | BÚSQUEDA GENERAL
        |--------------------------------------------------------------------------
        */

        $buscar =
            trim(
                $_GET['buscar']
                ?? ''
            );


        /*
        |--------------------------------------------------------------------------
        | TIPO DE CONCEPTO
        |--------------------------------------------------------------------------
        |
        | La columna física de la base continúa llamándose concepto.categoria.
        |
        | En la interfaz usamos "Tipo de concepto" para diferenciar claramente:
        |
        | - la categoría laboral del empleado;
        | - el tipo funcional del concepto.
        |
        | Se mantiene compatibilidad con URLs antiguas que todavía envíen
        | categoria=...
        |
        |--------------------------------------------------------------------------
        */

        $tipoConcepto =
            strtoupper(
                trim(
                    $_GET['tipo_concepto']
                    ??
                    $_GET['categoria']
                    ??
                    ''
                )
            );


        $tiposConcepto = [

            'REMUNERATIVO' =>
                'Remunerativo',

            'NO_REMUNERATIVO' =>
                'No remunerativo',

            'ASIGNACION_FAMILIAR' =>
                'Asignación familiar',

            'DESCUENTO' =>
                'Descuento',

            'APORTE_PATRONAL' =>
                'Aporte patronal'
        ];


        if (
            $tipoConcepto !== ''
            &&
            !array_key_exists(
                $tipoConcepto,
                $tiposConcepto
            )
        ) {

            $tipoConcepto = '';
        }


        /*
        |--------------------------------------------------------------------------
        | ESTADO
        |--------------------------------------------------------------------------
        |
        | El modelo recibe:
        |
        | - ''  = todos;
        | - '1' = activos;
        | - '0' = inactivos.
        |
        | También aceptamos estado=ACTIVO/INACTIVO por compatibilidad con URLs
        | anteriores.
        |
        |--------------------------------------------------------------------------
        */

        $activo =
            trim(
                $_GET['activo']
                ?? ''
            );


        $estado =
            strtoupper(
                trim(
                    $_GET['estado']
                    ?? ''
                )
            );


        if (
            $activo === ''
            &&
            (
                $estado === 'ACTIVO'
                ||
                $estado === 'INACTIVO'
            )
        ) {

            $activo =
                $estado === 'ACTIVO'
                    ? '1'
                    : '0';
        }


        if (
            $activo !== ''
            &&
            $activo !== '1'
            &&
            $activo !== '0'
        ) {

            $activo = '';
        }


        /*
        |--------------------------------------------------------------------------
        | CONSULTA
        |--------------------------------------------------------------------------
        */

        $error = "";
        $conceptos = [];
        $totalConceptos = 0;


        try {

            $conceptos =
                $this->modelo
                    ->listarConceptos(
                        $buscar,
                        $tipoConcepto,
                        $activo
                    );


            $totalConceptos =
                count(
                    $conceptos
                );


        } catch (Exception $e) {

            $error =
                $e->getMessage();

            $conceptos = [];
            $totalConceptos = 0;
        }


        /*
        |--------------------------------------------------------------------------
        | QUERY PARA PDF / EXCEL
        |--------------------------------------------------------------------------
        */

        $queryString =
            http_build_query(
                [
                    'buscar' =>
                        $buscar,

                    'tipo_concepto' =>
                        $tipoConcepto,

                    'activo' =>
                        $activo
                ]
            );


        require __DIR__
            . '/../vista/reporte_conceptos.php';
    }

    /*
    |--------------------------------------------------------------------------
    | REPORTE DE CONCEPTOS - IMPRESIÓN / PDF
    |--------------------------------------------------------------------------
    */

    public function conceptosPdf()
    {
        $buscar =
            trim(
                $_GET['buscar']
                ?? ''
            );


        $tipoConcepto =
            strtoupper(
                trim(
                    $_GET['tipo_concepto']
                    ??
                    $_GET['categoria']
                    ??
                    ''
                )
            );


        $tiposConceptoValidos = [
            'REMUNERATIVO',
            'NO_REMUNERATIVO',
            'ASIGNACION_FAMILIAR',
            'DESCUENTO',
            'APORTE_PATRONAL'
        ];


        if (
            $tipoConcepto !== ''
            &&
            !in_array(
                $tipoConcepto,
                $tiposConceptoValidos,
                true
            )
        ) {

            $tipoConcepto = '';
        }


        $activo =
            trim(
                $_GET['activo']
                ?? ''
            );


        $estado =
            strtoupper(
                trim(
                    $_GET['estado']
                    ?? ''
                )
            );


        if (
            $activo === ''
            &&
            (
                $estado === 'ACTIVO'
                ||
                $estado === 'INACTIVO'
            )
        ) {

            $activo =
                $estado === 'ACTIVO'
                    ? '1'
                    : '0';
        }


        if (
            $activo !== ''
            &&
            $activo !== '1'
            &&
            $activo !== '0'
        ) {

            $activo = '';
        }


        try {

            $conceptos =
                $this->modelo
                    ->listarConceptos(
                        $buscar,
                        $tipoConcepto,
                        $activo
                    );


        } catch (Exception $e) {

            die(
                "Error al generar el reporte: "
                .
                htmlspecialchars(
                    $e->getMessage(),
                    ENT_QUOTES,
                    'UTF-8'
                )
            );
        }


        $totalConceptos =
            count(
                $conceptos
            );


        require __DIR__
            . '/../vista/reporte_conceptos_pdf.php';
    }


    /*
    |--------------------------------------------------------------------------
    | REPORTE DE CONCEPTOS - EXPORTAR EXCEL
    |--------------------------------------------------------------------------
    */

    public function conceptosExcel()
    {
        $buscar =
            trim(
                $_GET['buscar']
                ?? ''
            );


        $tipoConcepto =
            strtoupper(
                trim(
                    $_GET['tipo_concepto']
                    ??
                    $_GET['categoria']
                    ??
                    ''
                )
            );


        $tiposConceptoValidos = [
            'REMUNERATIVO',
            'NO_REMUNERATIVO',
            'ASIGNACION_FAMILIAR',
            'DESCUENTO',
            'APORTE_PATRONAL'
        ];


        if (
            $tipoConcepto !== ''
            &&
            !in_array(
                $tipoConcepto,
                $tiposConceptoValidos,
                true
            )
        ) {

            $tipoConcepto = '';
        }


        $activo =
            trim(
                $_GET['activo']
                ?? ''
            );


        $estado =
            strtoupper(
                trim(
                    $_GET['estado']
                    ?? ''
                )
            );


        if (
            $activo === ''
            &&
            (
                $estado === 'ACTIVO'
                ||
                $estado === 'INACTIVO'
            )
        ) {

            $activo =
                $estado === 'ACTIVO'
                    ? '1'
                    : '0';
        }


        if (
            $activo !== ''
            &&
            $activo !== '1'
            &&
            $activo !== '0'
        ) {

            $activo = '';
        }


        try {

            $conceptos =
                $this->modelo
                    ->listarConceptos(
                        $buscar,
                        $tipoConcepto,
                        $activo
                    );


        } catch (Exception $e) {

            die(
                "Error al generar el reporte Excel: "
                .
                htmlspecialchars(
                    $e->getMessage(),
                    ENT_QUOTES,
                    'UTF-8'
                )
            );
        }


        $totalConceptos =
            count(
                $conceptos
            );


        require __DIR__
            . '/../vista/reporte_conceptos_excel.php';
    }


    /*
    |--------------------------------------------------------------------------
    | REPORTE DE CATEGORÍAS
    |--------------------------------------------------------------------------
    */

    public function categorias()
    {
        $buscar =
            trim(
                $_GET['buscar']
                ?? ''
            );


        $activo =
            trim(
                $_GET['activo']
                ??
                $_GET['estado']
                ??
                ''
            );


        if (
            $activo !== ''
            &&
            $activo !== '1'
            &&
            $activo !== '0'
        ) {

            $activo = '';
        }


        $error = "";
        $categorias = [];
        $totalCategorias = 0;


        try {

            $categorias =
                $this->modelo
                    ->listarCategorias(
                        $buscar,
                        $activo
                    );


            $totalCategorias =
                count(
                    $categorias
                );


        } catch (Exception $e) {

            $error =
                $e->getMessage();

            $categorias = [];
            $totalCategorias = 0;
        }


        $queryString =
            http_build_query(
                [
                    'buscar' =>
                        $buscar,

                    'activo' =>
                        $activo
                ]
            );


        require __DIR__
            . '/../vista/reporte_categorias.php';
    }


    /*
    |--------------------------------------------------------------------------
    | REPORTE DE CATEGORÍAS - IMPRESIÓN / PDF
    |--------------------------------------------------------------------------
    */

    public function categoriasPdf()
    {
        $buscar =
            trim(
                $_GET['buscar']
                ?? ''
            );


        $activo =
            trim(
                $_GET['activo']
                ??
                $_GET['estado']
                ??
                ''
            );


        if (
            $activo !== ''
            &&
            $activo !== '1'
            &&
            $activo !== '0'
        ) {

            $activo = '';
        }


        try {

            $categorias =
                $this->modelo
                    ->listarCategorias(
                        $buscar,
                        $activo
                    );


        } catch (Exception $e) {

            die(
                "Error al generar el reporte: "
                .
                htmlspecialchars(
                    $e->getMessage(),
                    ENT_QUOTES,
                    'UTF-8'
                )
            );
        }


        $totalCategorias =
            count(
                $categorias
            );


        require __DIR__
            . '/../vista/reporte_categorias_pdf.php';
    }


    /*
    |--------------------------------------------------------------------------
    | REPORTE DE CATEGORÍAS - EXPORTAR EXCEL
    |--------------------------------------------------------------------------
    */

    public function categoriasExcel()
    {
        $buscar =
            trim(
                $_GET['buscar']
                ?? ''
            );


        $activo =
            trim(
                $_GET['activo']
                ??
                $_GET['estado']
                ??
                ''
            );


        if (
            $activo !== ''
            &&
            $activo !== '1'
            &&
            $activo !== '0'
        ) {

            $activo = '';
        }


        try {

            $categorias =
                $this->modelo
                    ->listarCategorias(
                        $buscar,
                        $activo
                    );


        } catch (Exception $e) {

            die(
                "Error al generar el reporte Excel: "
                .
                htmlspecialchars(
                    $e->getMessage(),
                    ENT_QUOTES,
                    'UTF-8'
                )
            );
        }


        $totalCategorias =
            count(
                $categorias
            );


        require __DIR__
            . '/../vista/reporte_categorias_excel.php';
    }


    /*
    |--------------------------------------------------------------------------
    | REPORTE DE LIQUIDACIONES
    |--------------------------------------------------------------------------
    */

    public function liquidaciones()
    {
        /*
        |--------------------------------------------------------------------------
        | FILTROS
        |--------------------------------------------------------------------------
        */

        $periodo =
            trim(
                $_GET['periodo']
                ?? ''
            );


        $tipo =
            strtoupper(
                trim(
                    $_GET['tipo']
                    ??
                    $_GET['tipo_liquidacion']
                    ??
                    ''
                )
            );


        $estado =
            strtoupper(
                trim(
                    $_GET['estado']
                    ?? ''
                )
            );


        /*
        |--------------------------------------------------------------------------
        | ESTADOS DISPONIBLES
        |--------------------------------------------------------------------------
        |
        | Se incluye PROCESADA por compatibilidad histórica. Si no existen
        | liquidaciones con ese estado, simplemente no devolverá resultados.
        |
        |--------------------------------------------------------------------------
        */

        $estados = [

            'BORRADOR',

            'PROCESADA',

            'CERRADA',

            'ANULADA'
        ];


        if (
            $estado !== ''
            &&
            !in_array(
                $estado,
                $estados,
                true
            )
        ) {

            $estado = '';
        }


        /*
        |--------------------------------------------------------------------------
        | VARIABLES POR DEFECTO
        |--------------------------------------------------------------------------
        */

        $error = "";
        $liquidaciones = [];
        $totalLiquidaciones = 0;
        $tiposLiquidacion = [];


        /*
        |--------------------------------------------------------------------------
        | VALIDAR PERÍODO
        |--------------------------------------------------------------------------
        */

        if (
            $periodo !== ''
            &&
            !$this->periodoValido(
                $periodo
            )
        ) {

            $error =
                "El período ingresado no es válido. Utilizá el formato AAAA-MM.";

            $periodo = '';
        }


        try {

            /*
            |--------------------------------------------------------------------------
            | TIPOS DE LIQUIDACIÓN
            |--------------------------------------------------------------------------
            */

            $tiposLiquidacion =
                $this->modelo
                    ->obtenerTiposLiquidacion();


            /*
            |--------------------------------------------------------------------------
            | VALIDAR TIPO SELECCIONADO
            |--------------------------------------------------------------------------
            */

            if ($tipo !== '') {

                $tiposNormalizados =
                    array_map(
                        function($valor) {

                            return strtoupper(
                                trim(
                                    (string)$valor
                                )
                            );
                        },
                        $tiposLiquidacion
                    );


                if (
                    !in_array(
                        $tipo,
                        $tiposNormalizados,
                        true
                    )
                ) {

                    $tipo = '';
                }
            }


            /*
            |--------------------------------------------------------------------------
            | CONSULTAR
            |--------------------------------------------------------------------------
            */

            if ($error === '') {

                $liquidaciones =
                    $this->modelo
                        ->listarLiquidaciones(
                            $periodo,
                            $tipo,
                            $estado
                        );


                $totalLiquidaciones =
                    count(
                        $liquidaciones
                    );
            }


        } catch (Exception $e) {

            $error =
                $e->getMessage();

            $liquidaciones = [];
            $totalLiquidaciones = 0;
        }


        /*
        |--------------------------------------------------------------------------
        | QUERY PARA PDF / EXCEL
        |--------------------------------------------------------------------------
        */

        $queryString =
            http_build_query(
                [
                    'periodo' =>
                        $periodo,

                    'tipo' =>
                        $tipo,

                    'estado' =>
                        $estado
                ]
            );


        require __DIR__
            . '/../vista/reporte_liquidaciones.php';
    }


    /*
    |--------------------------------------------------------------------------
    | REPORTE DE LIQUIDACIONES - IMPRESIÓN / PDF
    |--------------------------------------------------------------------------
    */

    public function liquidacionesPdf()
    {
        $periodo =
            trim(
                $_GET['periodo']
                ?? ''
            );


        $tipo =
            strtoupper(
                trim(
                    $_GET['tipo']
                    ??
                    $_GET['tipo_liquidacion']
                    ??
                    ''
                )
            );


        $estado =
            strtoupper(
                trim(
                    $_GET['estado']
                    ?? ''
                )
            );


        $estados = [

            'BORRADOR',

            'PROCESADA',

            'CERRADA',

            'ANULADA'
        ];


        if (
            $estado !== ''
            &&
            !in_array(
                $estado,
                $estados,
                true
            )
        ) {

            $estado = '';
        }


        if (
            $periodo !== ''
            &&
            !$this->periodoValido(
                $periodo
            )
        ) {

            $periodo = '';
        }


        try {

            $tiposLiquidacion =
                $this->modelo
                    ->obtenerTiposLiquidacion();


            if ($tipo !== '') {

                $tiposNormalizados =
                    array_map(
                        function($valor) {

                            return strtoupper(
                                trim(
                                    (string)$valor
                                )
                            );
                        },
                        $tiposLiquidacion
                    );


                if (
                    !in_array(
                        $tipo,
                        $tiposNormalizados,
                        true
                    )
                ) {

                    $tipo = '';
                }
            }


            $liquidaciones =
                $this->modelo
                    ->listarLiquidaciones(
                        $periodo,
                        $tipo,
                        $estado
                    );


        } catch (Exception $e) {

            die(
                "Error al generar el reporte: "
                .
                htmlspecialchars(
                    $e->getMessage(),
                    ENT_QUOTES,
                    'UTF-8'
                )
            );
        }


        $totalLiquidaciones =
            count(
                $liquidaciones
            );


        require __DIR__
            . '/../vista/reporte_liquidaciones_pdf.php';
    }


    /*
    |--------------------------------------------------------------------------
    | REPORTE DE LIQUIDACIONES - EXPORTAR EXCEL
    |--------------------------------------------------------------------------
    */

    public function liquidacionesExcel()
    {
        $periodo =
            trim(
                $_GET['periodo']
                ?? ''
            );


        $tipo =
            strtoupper(
                trim(
                    $_GET['tipo']
                    ??
                    $_GET['tipo_liquidacion']
                    ??
                    ''
                )
            );


        $estado =
            strtoupper(
                trim(
                    $_GET['estado']
                    ?? ''
                )
            );


        $estados = [

            'BORRADOR',

            'PROCESADA',

            'CERRADA',

            'ANULADA'
        ];


        if (
            $estado !== ''
            &&
            !in_array(
                $estado,
                $estados,
                true
            )
        ) {

            $estado = '';
        }


        if (
            $periodo !== ''
            &&
            !$this->periodoValido(
                $periodo
            )
        ) {

            $periodo = '';
        }


        try {

            $tiposLiquidacion =
                $this->modelo
                    ->obtenerTiposLiquidacion();


            if ($tipo !== '') {

                $tiposNormalizados =
                    array_map(
                        function($valor) {

                            return strtoupper(
                                trim(
                                    (string)$valor
                                )
                            );
                        },
                        $tiposLiquidacion
                    );


                if (
                    !in_array(
                        $tipo,
                        $tiposNormalizados,
                        true
                    )
                ) {

                    $tipo = '';
                }
            }


            $liquidaciones =
                $this->modelo
                    ->listarLiquidaciones(
                        $periodo,
                        $tipo,
                        $estado
                    );


        } catch (Exception $e) {

            die(
                "Error al generar el reporte Excel: "
                .
                htmlspecialchars(
                    $e->getMessage(),
                    ENT_QUOTES,
                    'UTF-8'
                )
            );
        }


        $totalLiquidaciones =
            count(
                $liquidaciones
            );


        require __DIR__
            . '/../vista/reporte_liquidaciones_excel.php';
    }


    /*
    |--------------------------------------------------------------------------
    | DETALLE DE LIQUIDACIÓN POR EMPLEADO
    |--------------------------------------------------------------------------
    */

    public function detalleLiquidacionEmpleado()
    {
        $liquidacionId =
            isset($_GET['liquidacion_id'])
                ?
                (int)$_GET['liquidacion_id']
                :
                0;


        $empleadoId =
            isset($_GET['empleado_id'])
                ?
                (int)$_GET['empleado_id']
                :
                0;


        if (
            $liquidacionId <= 0
            ||
            $empleadoId <= 0
        ) {

            die(
                "Parámetros inválidos."
            );
        }


        try {

            $empleado =
                $this->modelo
                    ->obtenerEmpleadoPorId(
                        $empleadoId
                    );


            if (!$empleado) {

                throw new Exception(
                    "El empleado seleccionado no existe."
                );
            }


            $detalle =
                $this->modelo
                    ->obtenerDetalleLiquidacionEmpleado(
                        $liquidacionId,
                        $empleadoId
                    );


            require __DIR__
                . '/../vista/reporte_detalle_liquidacion_empleado.php';


        } catch (Exception $e) {

            die(
                "Error al consultar el detalle: "
                . htmlspecialchars(
                    $e->getMessage(),
                    ENT_QUOTES,
                    'UTF-8'
                )
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | REPORTE DE AUDITORÍA
    |--------------------------------------------------------------------------
    |
    | Filtros:
    |
    | - Fecha desde
    | - Fecha hasta
    | - Usuario
    | - Rol
    | - Módulo
    | - Acción
    | - Entidad
    |
    | Además permite recuperar un registro puntual para mostrar
    | datos anteriores y nuevos mediante detalle_id.
    |
    |--------------------------------------------------------------------------
    */

    public function auditoria()
    {
        $fechaDesde =
            trim(
                $_GET['fecha_desde']
                ?? ''
            );


        $fechaHasta =
            trim(
                $_GET['fecha_hasta']
                ?? ''
            );


        $usuario =
            trim(
                $_GET['usuario']
                ?? ''
            );


        $rol =
            trim(
                $_GET['rol']
                ?? ''
            );


        $modulo =
            trim(
                $_GET['modulo']
                ?? ''
            );


        $accion =
            strtoupper(
                trim(
                    $_GET['accion']
                    ?? ''
                )
            );


        $entidad =
            strtoupper(
                trim(
                    $_GET['entidad']
                    ?? ''
                )
            );


        $detalleId =
            isset($_GET['detalle_id'])
                ?
                (int)$_GET['detalle_id']
                :
                0;


        $error = "";


        /*
        |--------------------------------------------------------------------------
        | VALIDAR FECHA DESDE
        |--------------------------------------------------------------------------
        */

        if (
            $fechaDesde !== ''
            &&
            !$this->fechaValidaAuditoria(
                $fechaDesde
            )
        ) {

            $error =
                "La fecha Desde no es válida.";

            $fechaDesde = '';
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDAR FECHA HASTA
        |--------------------------------------------------------------------------
        */

        if (
            $fechaHasta !== ''
            &&
            !$this->fechaValidaAuditoria(
                $fechaHasta
            )
        ) {

            $error =
                "La fecha Hasta no es válida.";

            $fechaHasta = '';
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDAR RANGO
        |--------------------------------------------------------------------------
        */

        if (
            $error === ''
            &&
            $fechaDesde !== ''
            &&
            $fechaHasta !== ''
            &&
            $fechaDesde > $fechaHasta
        ) {

            $error =
                "La fecha Desde no puede ser posterior a la fecha Hasta.";
        }


        /*
        |--------------------------------------------------------------------------
        | VARIABLES POR DEFECTO
        |--------------------------------------------------------------------------
        */

        $auditorias = [];
        $totalAuditorias = 0;

        $usuariosAuditoria = [];
        $rolesAuditoria = [];
        $modulosAuditoria = [];
        $accionesAuditoria = [];
        $entidadesAuditoria = [];

        $auditoriaDetalle = null;


        try {

            /*
            |--------------------------------------------------------------------------
            | COMBOS
            |--------------------------------------------------------------------------
            */

            $usuariosAuditoria =
                $this->modelo
                    ->obtenerUsuariosAuditoria();


            $rolesAuditoria =
                $this->modelo
                    ->obtenerRolesAuditoria();


            $modulosAuditoria =
                $this->modelo
                    ->obtenerModulosAuditoria();


            $accionesAuditoria =
                $this->modelo
                    ->obtenerAccionesAuditoria();


            $entidadesAuditoria =
                $this->modelo
                    ->obtenerEntidadesAuditoria();


            /*
            |--------------------------------------------------------------------------
            | LISTADO
            |--------------------------------------------------------------------------
            */

            if ($error === '') {

                $auditorias =
                    $this->modelo
                        ->listarAuditoria(
                            $fechaDesde,
                            $fechaHasta,
                            $usuario,
                            $rol,
                            $modulo,
                            $accion,
                            $entidad
                        );


                $totalAuditorias =
                    count(
                        $auditorias
                    );
            }


            /*
            |--------------------------------------------------------------------------
            | DETALLE DE UN REGISTRO
            |--------------------------------------------------------------------------
            */

            if ($detalleId > 0) {

                $auditoriaDetalle =
                    $this->modelo
                        ->obtenerAuditoriaPorId(
                            $detalleId
                        );


                if (!$auditoriaDetalle) {

                    $error =
                        "El registro de auditoría solicitado no existe.";
                }
            }


        } catch (Exception $e) {

            $error =
                $e->getMessage();

            $auditorias = [];
            $totalAuditorias = 0;

            $usuariosAuditoria = [];
            $rolesAuditoria = [];
            $modulosAuditoria = [];
            $accionesAuditoria = [];
            $entidadesAuditoria = [];

            $auditoriaDetalle = null;
        }


        /*
        |--------------------------------------------------------------------------
        | QUERY PARA IMPRESIÓN / PDF
        |--------------------------------------------------------------------------
        */

        $queryString =
            http_build_query(
                [
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
                ]
            );


        require __DIR__
            . '/../vista/reporte_auditoria.php';
    }


    /*
    |--------------------------------------------------------------------------
    | REPORTE DE AUDITORÍA - EXPORTAR PDF
    |--------------------------------------------------------------------------
    */

    public function auditoriaPdf()
    {
        $fechaDesde =
            trim(
                $_GET['fecha_desde']
                ?? ''
            );


        $fechaHasta =
            trim(
                $_GET['fecha_hasta']
                ?? ''
            );


        $usuario =
            trim(
                $_GET['usuario']
                ?? ''
            );


        $rol =
            trim(
                $_GET['rol']
                ?? ''
            );


        $modulo =
            trim(
                $_GET['modulo']
                ?? ''
            );


        $accion =
            strtoupper(
                trim(
                    $_GET['accion']
                    ?? ''
                )
            );


        $entidad =
            strtoupper(
                trim(
                    $_GET['entidad']
                    ?? ''
                )
            );


        /*
        |--------------------------------------------------------------------------
        | VALIDAR FECHAS
        |--------------------------------------------------------------------------
        */

        if (
            !$this->fechaValidaAuditoria(
                $fechaDesde
            )
        ) {

            die(
                "La fecha Desde no es válida."
            );
        }


        if (
            !$this->fechaValidaAuditoria(
                $fechaHasta
            )
        ) {

            die(
                "La fecha Hasta no es válida."
            );
        }


        if (
            $fechaDesde !== ''
            &&
            $fechaHasta !== ''
            &&
            $fechaDesde > $fechaHasta
        ) {

            die(
                "La fecha Desde no puede ser posterior a la fecha Hasta."
            );
        }


        /*
        |--------------------------------------------------------------------------
        | CONSULTAR AUDITORÍA
        |--------------------------------------------------------------------------
        */

        try {

            $auditorias =
                $this->modelo
                    ->listarAuditoria(
                        $fechaDesde,
                        $fechaHasta,
                        $usuario,
                        $rol,
                        $modulo,
                        $accion,
                        $entidad
                    );


        } catch (Exception $e) {

            die(
                "Error al generar el Reporte de Auditoría: "
                .
                htmlspecialchars(
                    $e->getMessage(),
                    ENT_QUOTES,
                    'UTF-8'
                )
            );
        }


        $totalAuditorias =
            count(
                $auditorias
            );


        /*
        |--------------------------------------------------------------------------
        | DATOS DEL USUARIO GENERADOR
        |--------------------------------------------------------------------------
        */

        $usuarioGenerador =
            $_SESSION['nombre_completo']
            ??
            $_SESSION['usuario']
            ??
            'Usuario';


        $rolGenerador =
            $_SESSION['rol']
            ??
            '';


        require __DIR__
            . '/../vista/reporte_auditoria_pdf.php';
    }




    /*
    |--------------------------------------------------------------------------
    | ESTADÍSTICAS
    |--------------------------------------------------------------------------
    */

    public function estadisticas()
    {
        $periodoDesde =
            trim(
                $_GET['desde']
                ?? ''
            );


        $periodoHasta =
            trim(
                $_GET['hasta']
                ?? ''
            );


        /*
        |--------------------------------------------------------------------------
        | TIPO DE LIQUIDACIÓN
        |--------------------------------------------------------------------------
        |
        | Se admite tanto "tipo" como "tipo_liquidacion" para mantener
        | compatibilidad con otros reportes y enlaces existentes.
        |
        */

        $tipoLiquidacion =
            strtoupper(
                trim(
                    $_GET['tipo']
                    ??
                    $_GET['tipo_liquidacion']
                    ??
                    ''
                )
            );


        $error = "";


        /*
        |--------------------------------------------------------------------------
        | VALIDAR PERÍODO DESDE
        |--------------------------------------------------------------------------
        */

        if (
            $periodoDesde !== ''
            &&
            !$this->periodoValido(
                $periodoDesde
            )
        ) {

            $error =
                "El período Desde no es válido. Utilizá el formato AAAA-MM.";

            $periodoDesde = '';
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDAR PERÍODO HASTA
        |--------------------------------------------------------------------------
        */

        if (
            $periodoHasta !== ''
            &&
            !$this->periodoValido(
                $periodoHasta
            )
        ) {

            $error =
                "El período Hasta no es válido. Utilizá el formato AAAA-MM.";

            $periodoHasta = '';
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDAR RANGO
        |--------------------------------------------------------------------------
        */

        if (
            $error === ''
            &&
            $periodoDesde !== ''
            &&
            $periodoHasta !== ''
            &&
            $periodoDesde > $periodoHasta
        ) {

            $error =
                "El período Desde no puede ser posterior al período Hasta.";
        }


        /*
        |--------------------------------------------------------------------------
        | VARIABLES POR DEFECTO
        |--------------------------------------------------------------------------
        */

        $resumen = [

            'empleados_activos' =>
                0,

            'liquidaciones_cerradas' =>
                0,

            'total_remunerativo' =>
                0,

            'total_no_remunerativo' =>
                0,

            'total_asignaciones' =>
                0,

            'total_descuentos' =>
                0,

            'total_neto' =>
                0,

            'neto_promedio' =>
                0
        ];


        $tiposLiquidacion = [];

        $empleadosPorCategoria = [];
        $netoPorPeriodo = [];
        $conceptosPorCategoria = [];
        $principalesDescuentos = [];


        /*
        |--------------------------------------------------------------------------
        | ARRAYS PARA CHART.JS
        |--------------------------------------------------------------------------
        */

        $graficoEmpleadosCategoria = [
            'labels' => [],
            'valores' => []
        ];

        $graficoNetoPeriodo = [
            'labels' => [],
            'valores' => []
        ];

        $graficoConceptos = [
            'labels' => [],
            'valores' => []
        ];

        $graficoDescuentos = [
            'labels' => [],
            'valores' => []
        ];


        try {

            /*
            |--------------------------------------------------------------------------
            | TIPOS DISPONIBLES
            |--------------------------------------------------------------------------
            |
            | Se obtienen de la propia tabla liquidacion para evitar mantener una
            | lista duplicada en el controlador.
            |
            */

            $tiposLiquidacion =
                $this->modelo
                    ->obtenerTiposLiquidacion();


            /*
            |--------------------------------------------------------------------------
            | VALIDAR TIPO SELECCIONADO
            |--------------------------------------------------------------------------
            */

            if ($tipoLiquidacion !== '') {

                $tiposNormalizados =
                    array_map(
                        function($valor) {

                            return strtoupper(
                                trim(
                                    (string)$valor
                                )
                            );
                        },
                        $tiposLiquidacion
                    );


                if (
                    !in_array(
                        $tipoLiquidacion,
                        $tiposNormalizados,
                        true
                    )
                ) {

                    if ($error === '') {

                        $error =
                            "El tipo de liquidación seleccionado no es válido.";
                    }

                    $tipoLiquidacion = '';
                }
            }


            if ($error === '') {

                /*
                |--------------------------------------------------------------------------
                | TARJETAS
                |--------------------------------------------------------------------------
                |
                | Las métricas económicas consideran solamente liquidaciones CERRADAS.
                | empleados_activos continúa representando el padrón ACTUAL.
                |
                */

                $resumen =
                    $this->modelo
                        ->obtenerResumenEstadisticas(
                            $periodoDesde,
                            $periodoHasta,
                            $tipoLiquidacion
                        );


                /*
                |--------------------------------------------------------------------------
                | EMPLEADOS ACTIVOS ACTUALES POR CATEGORÍA
                |--------------------------------------------------------------------------
                |
                | Este gráfico representa el padrón actual. No se aplica período ni tipo
                | porque no existe historial de categoría para reconstrucción histórica.
                |
                */

                $empleadosPorCategoria =
                    $this->modelo
                        ->contarEmpleadosPorCategoria();


                /*
                |--------------------------------------------------------------------------
                | NETO POR PERÍODO
                |--------------------------------------------------------------------------
                */

                $netoPorPeriodo =
                    $this->modelo
                        ->obtenerNetoPorPeriodo(
                            $periodoDesde,
                            $periodoHasta,
                            $tipoLiquidacion
                        );


                /*
                |--------------------------------------------------------------------------
                | COMPOSICIÓN POR TIPO DE CONCEPTO
                |--------------------------------------------------------------------------
                */

                $conceptosPorCategoria =
                    $this->modelo
                        ->obtenerConceptosPorCategoria(
                            $periodoDesde,
                            $periodoHasta,
                            $tipoLiquidacion
                        );


                /*
                |--------------------------------------------------------------------------
                | PRINCIPALES DESCUENTOS
                |--------------------------------------------------------------------------
                |
                | El nuevo parámetro tipo se pasa en cuarta posición para mantener
                | compatibilidad con la firma existente del modelo.
                |
                */

                $principalesDescuentos =
                    $this->modelo
                        ->obtenerPrincipalesDescuentos(
                            $periodoDesde,
                            $periodoHasta,
                            10,
                            $tipoLiquidacion
                        );
            }


            /*
            |--------------------------------------------------------------------------
            | PREPARAR DATOS PARA CHART.JS
            |--------------------------------------------------------------------------
            */

            $graficoEmpleadosCategoria =
                $this->prepararGrafico(
                    $empleadosPorCategoria,
                    'categoria',
                    'total'
                );


            $graficoNetoPeriodo =
                $this->prepararGrafico(
                    $netoPorPeriodo,
                    'periodo',
                    'total_neto'
                );


            $graficoConceptos =
                $this->prepararGrafico(
                    $conceptosPorCategoria,
                    'tipo',
                    'total'
                );


            $graficoDescuentos =
                $this->prepararGrafico(
                    $principalesDescuentos,
                    'concepto',
                    'total'
                );


        } catch (Exception $e) {

            $error =
                $e->getMessage();

            $resumen = [

                'empleados_activos' =>
                    0,

                'liquidaciones_cerradas' =>
                    0,

                'total_remunerativo' =>
                    0,

                'total_no_remunerativo' =>
                    0,

                'total_asignaciones' =>
                    0,

                'total_descuentos' =>
                    0,

                'total_neto' =>
                    0,

                'neto_promedio' =>
                    0
            ];


            $tiposLiquidacion = [];
            $empleadosPorCategoria = [];
            $netoPorPeriodo = [];
            $conceptosPorCategoria = [];
            $principalesDescuentos = [];


            $graficoEmpleadosCategoria = [
                'labels' => [],
                'valores' => []
            ];

            $graficoNetoPeriodo = [
                'labels' => [],
                'valores' => []
            ];

            $graficoConceptos = [
                'labels' => [],
                'valores' => []
            ];

            $graficoDescuentos = [
                'labels' => [],
                'valores' => []
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | QUERY PARA PDF / EXCEL
        |--------------------------------------------------------------------------
        |
        | Los exportadores deben recibir exactamente los mismos filtros que la
        | pantalla para que los resultados sean conciliables.
        |
        */

        $queryString =
            http_build_query(
                [
                    'desde' =>
                        $periodoDesde,

                    'hasta' =>
                        $periodoHasta,

                    'tipo' =>
                        $tipoLiquidacion
                ]
            );


        require __DIR__
            . '/../vista/estadisticas.php';
    }




    /*
    |--------------------------------------------------------------------------
    | ESTADÍSTICAS - EXPORTAR PDF
    |--------------------------------------------------------------------------
    */

    public function estadisticasPdf()
    {
        $periodoDesde =
            trim(
                $_GET['desde']
                ?? ''
            );


        $periodoHasta =
            trim(
                $_GET['hasta']
                ?? ''
            );


        $tipoLiquidacion =
            strtoupper(
                trim(
                    $_GET['tipo']
                    ??
                    $_GET['tipo_liquidacion']
                    ??
                    ''
                )
            );


        /*
        |--------------------------------------------------------------------------
        | VALIDAR FILTROS
        |--------------------------------------------------------------------------
        */

        if (
            !$this->periodoValido(
                $periodoDesde
            )
        ) {

            $periodoDesde = '';
        }


        if (
            !$this->periodoValido(
                $periodoHasta
            )
        ) {

            $periodoHasta = '';
        }


        if (
            $periodoDesde !== ''
            &&
            $periodoHasta !== ''
            &&
            $periodoDesde > $periodoHasta
        ) {

            $periodoDesde = '';
            $periodoHasta = '';
        }


        /*
        |--------------------------------------------------------------------------
        | USUARIO
        |--------------------------------------------------------------------------
        */

        $nombreCompleto =
            $_SESSION['nombre_completo']
            ??
            $_SESSION['usuario']
            ??
            'Usuario';


        $rol =
            $_SESSION['rol']
            ??
            'OPERADOR';


        try {

            /*
            |--------------------------------------------------------------------------
            | VALIDAR TIPO
            |--------------------------------------------------------------------------
            */

            $tiposDisponibles =
                $this->modelo
                    ->obtenerTiposLiquidacion();


            $tiposNormalizados =
                array_map(
                    function($valor) {

                        return strtoupper(
                            trim(
                                (string)$valor
                            )
                        );
                    },
                    $tiposDisponibles
                );


            if (
                $tipoLiquidacion !== ''
                &&
                !in_array(
                    $tipoLiquidacion,
                    $tiposNormalizados,
                    true
                )
            ) {

                $tipoLiquidacion = '';
            }


            /*
            |--------------------------------------------------------------------------
            | CONSULTAR DATOS
            |--------------------------------------------------------------------------
            */

            $resumen =
                $this->modelo
                    ->obtenerResumenEstadisticas(
                        $periodoDesde,
                        $periodoHasta,
                        $tipoLiquidacion
                    );


            $empleadosPorCategoria =
                $this->modelo
                    ->contarEmpleadosPorCategoria();


            $netoPorPeriodo =
                $this->modelo
                    ->obtenerNetoPorPeriodo(
                        $periodoDesde,
                        $periodoHasta,
                        $tipoLiquidacion
                    );


            $conceptosPorCategoria =
                $this->modelo
                    ->obtenerConceptosPorCategoria(
                        $periodoDesde,
                        $periodoHasta,
                        $tipoLiquidacion
                    );


            $principalesDescuentos =
                $this->modelo
                    ->obtenerPrincipalesDescuentos(
                        $periodoDesde,
                        $periodoHasta,
                        10,
                        $tipoLiquidacion
                    );


        } catch (Exception $e) {

            http_response_code(500);

            die(
                "Error al consultar las estadísticas para PDF."
            );
        }


        require __DIR__
            . '/../vista/generar_pdf_estadisticas.php';
    }


    /*
    |--------------------------------------------------------------------------
    | ESTADÍSTICAS - EXPORTAR EXCEL
    |--------------------------------------------------------------------------
    */

    public function estadisticasExcel()
    {
        $periodoDesde =
            trim(
                $_GET['desde']
                ?? ''
            );


        $periodoHasta =
            trim(
                $_GET['hasta']
                ?? ''
            );


        $tipoLiquidacion =
            strtoupper(
                trim(
                    $_GET['tipo']
                    ??
                    $_GET['tipo_liquidacion']
                    ??
                    ''
                )
            );


        /*
        |--------------------------------------------------------------------------
        | VALIDAR FILTROS
        |--------------------------------------------------------------------------
        */

        if (
            !$this->periodoValido(
                $periodoDesde
            )
        ) {

            $periodoDesde = '';
        }


        if (
            !$this->periodoValido(
                $periodoHasta
            )
        ) {

            $periodoHasta = '';
        }


        if (
            $periodoDesde !== ''
            &&
            $periodoHasta !== ''
            &&
            $periodoDesde > $periodoHasta
        ) {

            $periodoDesde = '';
            $periodoHasta = '';
        }


        try {

            /*
            |--------------------------------------------------------------------------
            | VALIDAR TIPO
            |--------------------------------------------------------------------------
            */

            $tiposDisponibles =
                $this->modelo
                    ->obtenerTiposLiquidacion();


            $tiposNormalizados =
                array_map(
                    function($valor) {

                        return strtoupper(
                            trim(
                                (string)$valor
                            )
                        );
                    },
                    $tiposDisponibles
                );


            if (
                $tipoLiquidacion !== ''
                &&
                !in_array(
                    $tipoLiquidacion,
                    $tiposNormalizados,
                    true
                )
            ) {

                $tipoLiquidacion = '';
            }


            /*
            |--------------------------------------------------------------------------
            | CONSULTAR DATOS
            |--------------------------------------------------------------------------
            */

            $resumen =
                $this->modelo
                    ->obtenerResumenEstadisticas(
                        $periodoDesde,
                        $periodoHasta,
                        $tipoLiquidacion
                    );


            $empleadosPorCategoria =
                $this->modelo
                    ->contarEmpleadosPorCategoria();


            $netoPorPeriodo =
                $this->modelo
                    ->obtenerNetoPorPeriodo(
                        $periodoDesde,
                        $periodoHasta,
                        $tipoLiquidacion
                    );


            $conceptosPorCategoria =
                $this->modelo
                    ->obtenerConceptosPorCategoria(
                        $periodoDesde,
                        $periodoHasta,
                        $tipoLiquidacion
                    );


            $principalesDescuentos =
                $this->modelo
                    ->obtenerPrincipalesDescuentos(
                        $periodoDesde,
                        $periodoHasta,
                        10,
                        $tipoLiquidacion
                    );


        } catch (Exception $e) {

            http_response_code(500);

            die(
                "Error al consultar las estadísticas para Excel: "
                .
                htmlspecialchars(
                    $e->getMessage(),
                    ENT_QUOTES,
                    'UTF-8'
                )
            );
        }


        require __DIR__
            . '/../vista/generar_excel_estadisticas.php';
    }




    /*
    |--------------------------------------------------------------------------
    | VALIDAR FECHA DE AUDITORÍA
    |--------------------------------------------------------------------------
    |
    | Formato esperado:
    |
    | YYYY-MM-DD
    |
    |--------------------------------------------------------------------------
    */

    private function fechaValidaAuditoria($fecha)
    {
        if ($fecha === '') {

            return true;
        }


        $objetoFecha =
            DateTime::createFromFormat(
                'Y-m-d',
                $fecha
            );


        return (
            $objetoFecha !== false
            &&
            $objetoFecha->format(
                'Y-m-d'
            )
            ===
            $fecha
        );
    }


    /*
    |--------------------------------------------------------------------------
    | PREPARAR DATOS PARA CHART.JS
    |--------------------------------------------------------------------------
    */

    private function prepararGrafico(
        array $datos,
        $campoLabel,
        $campoValor
    ) {
        $labels = [];
        $valores = [];


        foreach ($datos as $fila) {

            $labels[] =
                (string)(
                    $fila[$campoLabel]
                    ?? ''
                );


            $valores[] =
                (float)(
                    $fila[$campoValor]
                    ?? 0
                );
        }


        return [
            'labels' =>
                $labels,

            'valores' =>
                $valores
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDAR PERÍODO
    |--------------------------------------------------------------------------
    |
    | Formato:
    |
    | YYYY-MM
    |
    |--------------------------------------------------------------------------
    */

    private function periodoValido($periodo)
    {
        if ($periodo === '') {

            return true;
        }


        return (
            preg_match(
                '/^\d{4}-(0[1-9]|1[0-2])$/',
                $periodo
            )
            ===
            1
        );
    }
}
