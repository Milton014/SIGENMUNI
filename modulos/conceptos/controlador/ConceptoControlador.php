<?php

require_once __DIR__ . '/../modelo/ConceptoModelo.php';
require_once __DIR__ . '/../../../core/Csrf.php';
require_once __DIR__ . '/../../../core/Url.php';

class ConceptoControlador
{
    private $modelo;


    /*
    |--------------------------------------------------------------------------
    | CONSTRUCTOR
    |--------------------------------------------------------------------------
    */

    public function __construct($conexion)
    {
        $this->modelo = new ConceptoModelo($conexion);
    }


    /*
    |--------------------------------------------------------------------------
    | LISTADO DE CONCEPTOS
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $buscar = trim(
            $_GET['buscar'] ?? ''
        );

        $categoria = trim(
            $_GET['categoria'] ?? ''
        );

        $activo = trim(
            $_GET['activo'] ?? ''
        );

        $mensaje = "";
        $tipo_mensaje = "";


        /*
        |--------------------------------------------------------------------------
        | MENSAJES
        |--------------------------------------------------------------------------
        */

        if (isset($_GET['ok'])) {

            switch ($_GET['ok']) {

                case '1':
                case 'nuevo':

                    $mensaje =
                        "Concepto guardado correctamente.";

                    $tipo_mensaje =
                        "ok";

                    break;


                case '2':
                case 'editar':

                    $mensaje =
                        "Concepto actualizado correctamente.";

                    $tipo_mensaje =
                        "ok";

                    break;


                case '3':
                case 'estado':

                    $mensaje =
                        "Estado del concepto actualizado correctamente.";

                    $tipo_mensaje =
                        "ok";

                    break;
            }
        }


        try {

            /*
            |--------------------------------------------------------------------------
            | VALIDAR CATEGORÍA
            |--------------------------------------------------------------------------
            */

            if (
                $categoria !== '' &&
                !$this->modelo->categoriaValida(
                    $categoria
                )
            ) {
                $categoria = '';
            }


            /*
            |--------------------------------------------------------------------------
            | VALIDAR ESTADO
            |--------------------------------------------------------------------------
            */

            if (
                $activo !== '' &&
                $activo !== '0' &&
                $activo !== '1'
            ) {
                $activo = '';
            }


            /*
            |--------------------------------------------------------------------------
            | DATOS PARA LA VISTA
            |--------------------------------------------------------------------------
            */

            $categorias =
                $this->modelo->obtenerCategorias();


            $conceptos =
                $this->modelo->listar(
                    $buscar,
                    $categoria,
                    $activo
                );


            /*
            |--------------------------------------------------------------------------
            | CARGAR VISTA
            |--------------------------------------------------------------------------
            */

            require __DIR__
                . '/../vista/conceptos.php';

        } catch (Exception $e) {

            die(
                "Error al cargar los conceptos: "
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
    | NUEVO CONCEPTO
    |--------------------------------------------------------------------------
    */

    public function nuevo()
    {
        $mensaje = "";
        $tipo_mensaje = "error";
        $basesCalculoPorcentaje = [];


        /*
        |--------------------------------------------------------------------------
        | DATOS INICIALES
        |--------------------------------------------------------------------------
        */

        $datos = [
            'codigo' => '',
            'nombre' => '',
            'categoria' => '',
            'forma_calculo' => 'MANUAL',

            'porcentaje' => '0.0000',
            'monto_fijo' => '0.00',

            'requiere_manual' => 0,
            'asignable_empleado' => 0,
            'base_calculo' => '',
            'orden_calculo' => 0,

            'aplica_sac' => 0,
            'visible_recibo' => 1,
            'activo' => 1,

            'descripcion' => '',

            'fecha_desde' => '',
            'fecha_hasta' => ''
        ];


        try {

            /*
            |--------------------------------------------------------------------------
            | DATOS PARA COMBOS
            |--------------------------------------------------------------------------
            */

            $categorias =
                $this->modelo->obtenerCategorias();


            $formasCalculo =
                $this->obtenerFormasCalculoFormulario();


            $basesCalculoPorcentaje =
                $this->modelo
                    ->obtenerBasesCalculoPorcentaje();


            /*
            |--------------------------------------------------------------------------
            | PROCESAR POST
            |--------------------------------------------------------------------------
            */

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {

                $datos =
                    $this->recibirDatosFormulario();


                /*
                |--------------------------------------------------------------------------
                | PROTECCIÓN CSRF
                |--------------------------------------------------------------------------
                */

                if (
                    !sigenmuniCsrfValido(
                        $_POST['_csrf']
                        ?? ''
                    )
                ) {
                    throw new Exception(
                        "La sesión del formulario no es válida o expiró. Recargue la página e inténtelo nuevamente."
                    );
                }


                $datosGuardar =
                    $this->validarYNormalizarDatos(
                        $datos
                    );


                /*
                |--------------------------------------------------------------------------
                | CÓDIGO DUPLICADO
                |--------------------------------------------------------------------------
                */

                if (
                    $this->modelo->existeCodigo(
                        $datosGuardar['codigo']
                    )
                ) {

                    throw new Exception(
                        "Ya existe un concepto con ese código."
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | NOMBRE DUPLICADO
                |--------------------------------------------------------------------------
                */

                if (
                    $this->modelo->existeNombre(
                        $datosGuardar['nombre']
                    )
                ) {

                    throw new Exception(
                        "Ya existe un concepto con ese nombre."
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | GUARDAR
                |--------------------------------------------------------------------------
                */

                $idConcepto =
                    $this->modelo->guardar(
                        $datosGuardar
                    );


                if ($idConcepto <= 0) {

                    throw new Exception(
                        "No se pudo guardar el concepto."
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | REDIRECCIONAR
                |--------------------------------------------------------------------------
                */

                $urlListado =
                    sigenmuniUrlRuta(
                        'conceptos',
                        [
                            'ok' => 'nuevo'
                        ]
                    );


                header(
                    'Location: ' . $urlListado
                );

                exit();
            }

        } catch (Exception $e) {

            $mensaje =
                $e->getMessage();

            $tipo_mensaje =
                "error";
        }


        /*
        |--------------------------------------------------------------------------
        | CARGAR VISTA
        |--------------------------------------------------------------------------
        */

        require __DIR__
            . '/../vista/concepto_nuevo.php';
    }


    /*
    |--------------------------------------------------------------------------
    | EDITAR CONCEPTO
    |--------------------------------------------------------------------------
    */

    public function editar()
    {
        $id = isset($_GET['id'])
            ? (int)$_GET['id']
            : 0;

        $mensaje = "";
        $tipo_mensaje = "";
        $basesCalculoPorcentaje = [];


        /*
        |--------------------------------------------------------------------------
        | VALIDAR ID
        |--------------------------------------------------------------------------
        */

        if ($id <= 0) {

            $urlListado =
                sigenmuniUrlRuta(
                    'conceptos'
                );

            header(
                'Location: ' . $urlListado
            );

            exit();
        }


        /*
        |--------------------------------------------------------------------------
        | MENSAJES DE LA PANTALLA INTEGRADA
        |--------------------------------------------------------------------------
        */

        if (isset($_GET['ok'])) {

            if ($_GET['ok'] === 'editar') {

                $mensaje =
                    "Concepto actualizado correctamente.";

                $tipo_mensaje =
                    "ok";
            }
        }


        if (isset($_GET['valor_ok'])) {

            switch ($_GET['valor_ok']) {

                case 'nuevo':

                    $mensaje =
                        "Valor del concepto guardado correctamente.";

                    $tipo_mensaje =
                        "ok";

                    break;


                case 'editar':

                    $mensaje =
                        "Valor del concepto actualizado correctamente.";

                    $tipo_mensaje =
                        "ok";

                    break;


                case 'estado':

                    $mensaje =
                        "Estado del valor actualizado correctamente.";

                    $tipo_mensaje =
                        "ok";

                    break;
            }
        }


        try {

            /*
            |--------------------------------------------------------------------------
            | OBTENER CONCEPTO
            |--------------------------------------------------------------------------
            */

            $concepto =
                $this->modelo->obtenerPorId(
                    $id
                );


            if (!$concepto) {

                $urlListado =
                    sigenmuniUrlRuta(
                        'conceptos'
                    );

                header(
                    'Location: ' . $urlListado
                );

                exit();
            }


            /*
            |--------------------------------------------------------------------------
            | COMPATIBILIDAD CON CONCEPTOS FIJO ANTERIORES
            |--------------------------------------------------------------------------
            |
            | FIJO ya no se ofrece en la interfaz. Si un concepto histórico
            | todavía está configurado como FIJO, se presenta como MANUAL para
            | que pueda migrarse al nuevo esquema al actualizarlo.
            |
            */

            if (
                strtoupper(
                    trim(
                        (string)($concepto['forma_calculo'] ?? '')
                    )
                )
                ===
                'FIJO'
            ) {
                $concepto['forma_calculo'] =
                    'MANUAL';

                $concepto['asignable_empleado'] =
                    1;

                $concepto['requiere_manual'] =
                    1;
            }


            /*
            |--------------------------------------------------------------------------
            | DATOS DEL CONCEPTO
            |--------------------------------------------------------------------------
            */

            $categorias =
                $this->modelo->obtenerCategorias();


            $formasCalculo =
                $this->obtenerFormasCalculoFormulario();


            $basesCalculoPorcentaje =
                $this->modelo
                    ->obtenerBasesCalculoPorcentaje();


            /*
            |--------------------------------------------------------------------------
            | VALORES DEL CONCEPTO
            |--------------------------------------------------------------------------
            |
            | La edición del concepto funciona ahora como pantalla principal
            | para administrar también sus valores por categoría/escalafón.
            |
            */

            $valoresConcepto =
                $this->modelo->listarValores(
                    $id
                );


            $categoriasValores =
                $this->modelo
                    ->obtenerCategoriasParaValores();


            $escalafonesValores =
                $this->modelo
                    ->obtenerEscalafonesParaValores();


            /*
            |--------------------------------------------------------------------------
            | INDICAR SI EL CONCEPTO ADMITE VALORES
            |--------------------------------------------------------------------------
            */

            $admiteValores =
                in_array(
                    (string)($concepto['codigo'] ?? ''),
                    ['101', '102', '104'],
                    true
                )
                &&
                (
                    ($concepto['forma_calculo'] ?? '')
                    ===
                    'TABLA_CATEGORIA'
                );


            /*
            |--------------------------------------------------------------------------
            | PROCESAR POST DEL CONCEPTO
            |--------------------------------------------------------------------------
            */

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {

                /*
                |--------------------------------------------------------------------------
                | PROTECCIÓN CSRF
                |--------------------------------------------------------------------------
                */

                if (
                    !sigenmuniCsrfValido(
                        $_POST['_csrf']
                        ?? ''
                    )
                ) {
                    throw new Exception(
                        "La sesión del formulario no es válida o expiró. Recargue la página e inténtelo nuevamente."
                    );
                }


                $datos =
                    $this->recibirDatosFormulario();


                $datosActualizar =
                    $this->validarYNormalizarDatos(
                        $datos
                    );


                /*
                |--------------------------------------------------------------------------
                | CÓDIGO DUPLICADO
                |--------------------------------------------------------------------------
                */

                if (
                    $this->modelo->existeCodigo(
                        $datosActualizar['codigo'],
                        $id
                    )
                ) {

                    throw new Exception(
                        "Ya existe otro concepto con ese código."
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | NOMBRE DUPLICADO
                |--------------------------------------------------------------------------
                */

                if (
                    $this->modelo->existeNombre(
                        $datosActualizar['nombre'],
                        $id
                    )
                ) {

                    throw new Exception(
                        "Ya existe otro concepto con ese nombre."
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | ACTUALIZAR
                |--------------------------------------------------------------------------
                */

                $actualizado =
                    $this->modelo->actualizar(
                        $id,
                        $datosActualizar
                    );


                if (!$actualizado) {

                    throw new Exception(
                        "No se pudo actualizar el concepto."
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | VOLVER A LA MISMA PANTALLA
                |--------------------------------------------------------------------------
                |
                | De esta forma el usuario puede continuar administrando los valores
                | del concepto sin regresar obligatoriamente al listado general.
                |
                */

                $urlEditar =
                    sigenmuniUrlRuta(
                        'conceptos/editar',
                        [
                            'id' => $id,
                            'ok' => 'editar'
                        ]
                    );

                header(
                    'Location: ' . $urlEditar
                );

                exit();
            }

        } catch (Exception $e) {

            $mensaje =
                $e->getMessage();

            $tipo_mensaje =
                "error";


            /*
            |--------------------------------------------------------------------------
            | CONSERVAR DATOS INGRESADOS
            |--------------------------------------------------------------------------
            */

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {

                $concepto = array_merge(
                    $concepto ?? [],
                    $_POST
                );


                $concepto['requiere_manual'] =
                    (
                        trim(
                            $_POST['forma_calculo']
                            ?? ''
                        )
                        ===
                        'MANUAL'
                    )
                        ? 1
                        : 0;


                $concepto['asignable_empleado'] =
                    isset($_POST['asignable_empleado'])
                        ? 1
                        : 0;


                $concepto['aplica_sac'] =
                    isset($_POST['aplica_sac'])
                        ? 1
                        : 0;


                $concepto['visible_recibo'] =
                    isset($_POST['visible_recibo'])
                        ? 1
                        : 0;


                $concepto['activo'] =
                    isset($_POST['activo'])
                        ? 1
                        : 0;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | ASEGURAR VARIABLES PARA LA VISTA
        |--------------------------------------------------------------------------
        */

        $valoresConcepto =
            $valoresConcepto
            ?? [];


        $categoriasValores =
            $categoriasValores
            ?? [];


        $escalafonesValores =
            $escalafonesValores
            ?? [];


        $admiteValores =
            $admiteValores
            ?? false;


        /*
        |--------------------------------------------------------------------------
        | CARGAR VISTA
        |--------------------------------------------------------------------------
        */

        require __DIR__
            . '/../vista/concepto_editar.php';
    }


    /*
    |--------------------------------------------------------------------------
    | CAMBIAR ESTADO DEL CONCEPTO
    |--------------------------------------------------------------------------
    */

    public function estado()
    {
        /*
        |--------------------------------------------------------------------------
        | SOLO POST
        |--------------------------------------------------------------------------
        */

        if (
            ($_SERVER['REQUEST_METHOD'] ?? 'GET')
            !==
            'POST'
        ) {
            http_response_code(405);

            die(
                "Método no permitido."
            );
        }


        /*
        |--------------------------------------------------------------------------
        | DATOS RECIBIDOS
        |--------------------------------------------------------------------------
        */

        $id =
            isset($_POST['id'])
                ? (int)$_POST['id']
                : 0;


        $estado =
            isset($_POST['estado'])
                ? (int)$_POST['estado']
                : -1;


        /*
        |--------------------------------------------------------------------------
        | PROTECCIÓN CSRF
        |--------------------------------------------------------------------------
        */

        if (
            !sigenmuniCsrfValido(
                $_POST['_csrf']
                ?? ''
            )
        ) {
            header(
                'Location: '
                .
                sigenmuniUrlRuta(
                    'conceptos'
                )
            );

            exit();
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDAR PARÁMETROS
        |--------------------------------------------------------------------------
        */

        if (
            $id <= 0 ||
            (
                $estado !== 0 &&
                $estado !== 1
            )
        ) {
            header(
                'Location: '
                .
                sigenmuniUrlRuta(
                    'conceptos'
                )
            );

            exit();
        }


        try {

            /*
            |--------------------------------------------------------------------------
            | OBTENER CONCEPTO
            |--------------------------------------------------------------------------
            */

            $concepto =
                $this->modelo->obtenerPorId(
                    $id
                );


            if (!$concepto) {
                header(
                    'Location: '
                    .
                    sigenmuniUrlRuta(
                        'conceptos'
                    )
                );

                exit();
            }


            /*
            |--------------------------------------------------------------------------
            | EVITAR UPDATE INNECESARIO
            |--------------------------------------------------------------------------
            */

            if (
                (int)$concepto['activo']
                ===
                $estado
            ) {
                header(
                    'Location: '
                    .
                    sigenmuniUrlRuta(
                        'conceptos'
                    )
                );

                exit();
            }


            /*
            |--------------------------------------------------------------------------
            | CAMBIAR ESTADO
            |--------------------------------------------------------------------------
            */

            $actualizado =
                $this->modelo->cambiarEstado(
                    $id,
                    $estado
                );


            if (!$actualizado) {
                throw new Exception(
                    "No se pudo actualizar el estado del concepto."
                );
            }


            /*
            |--------------------------------------------------------------------------
            | REDIRECCIONAR
            |--------------------------------------------------------------------------
            */

            header(
                'Location: '
                .
                sigenmuniUrlRuta(
                    'conceptos',
                    [
                        'ok' => 'estado'
                    ]
                )
            );

            exit();

        } catch (Exception $e) {

            die(
                "Error al cambiar el estado del concepto: "
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
    | LISTADO DE VALORES
    |--------------------------------------------------------------------------
    */

    public function valores()
    {
        $conceptoId = isset($_GET['concepto_id'])
            ? (int)$_GET['concepto_id']
            : 0;


        /*
        |--------------------------------------------------------------------------
        | CONTEXTO DEL LISTADO
        |--------------------------------------------------------------------------
        |
        | todos     = listado general, sin filtro por concepto.
        | filtrado  = listado de valores de un concepto específico.
        |
        */

        $retornoValores =
            $conceptoId > 0
                ? 'filtrado'
                : 'todos';


        $mensaje = "";
        $tipo_mensaje = "";


        /*
        |--------------------------------------------------------------------------
        | MENSAJES
        |--------------------------------------------------------------------------
        */

        if (isset($_GET['ok'])) {

            switch ($_GET['ok']) {

                case '1':
                case 'nuevo':

                    $mensaje =
                        "Valor del concepto guardado correctamente.";

                    $tipo_mensaje =
                        "ok";

                    break;


                case '2':
                case 'editar':

                    $mensaje =
                        "Valor del concepto actualizado correctamente.";

                    $tipo_mensaje =
                        "ok";

                    break;


                case '3':
                case 'estado':

                    $mensaje =
                        "Estado del valor actualizado correctamente.";

                    $tipo_mensaje =
                        "ok";

                    break;
            }
        }


        try {

            /*
            |--------------------------------------------------------------------------
            | NOMBRE DEL CONCEPTO
            |--------------------------------------------------------------------------
            */

            $nombreConcepto = "";


            if ($conceptoId > 0) {

                $nombreConcepto =
                    $this->modelo->obtenerNombreConcepto(
                        $conceptoId
                    );


                if ($nombreConcepto === null) {

                    $urlConceptos =
                        sigenmuniUrlRuta(
                            'conceptos'
                        );

                    header(
                        'Location: ' . $urlConceptos
                    );

                    exit();
                }
            }


            /*
            |--------------------------------------------------------------------------
            | LISTAR VALORES
            |--------------------------------------------------------------------------
            */

            $valores =
                $this->modelo->listarValores(
                    $conceptoId
                );


            /*
            |--------------------------------------------------------------------------
            | CARGAR VISTA
            |--------------------------------------------------------------------------
            */

            require __DIR__
                . '/../vista/concepto_valores.php';

        } catch (Exception $e) {

            die(
                "Error al cargar los valores de conceptos: "
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
    | NUEVO VALOR
    |--------------------------------------------------------------------------
    */

    public function nuevoValor()
    {
        $conceptoIdSeleccionado =
            isset($_GET['concepto_id'])
                ? (int)$_GET['concepto_id']
                : 0;


        $origen =
            trim(
                $_POST['origen']
                ?? $_GET['origen']
                ?? ''
            );


        $modoIntegrado =
            (
                $origen === 'concepto'
                &&
                $conceptoIdSeleccionado > 0
            );


        /*
        |--------------------------------------------------------------------------
        | CONTEXTO DE RETORNO
        |--------------------------------------------------------------------------
        */

        $retornoValores =
            trim(
                $_POST['retorno']
                ?? $_GET['retorno']
                ?? ''
            );


        if (
            $retornoValores !== 'todos'
            &&
            $retornoValores !== 'filtrado'
        ) {
            $retornoValores =
                $conceptoIdSeleccionado > 0
                    ? 'filtrado'
                    : 'todos';
        }


        $conceptoIdContextoValores =
            $conceptoIdSeleccionado;


        $mensaje = "";
        $tipo_mensaje = "error";


        /*
        |--------------------------------------------------------------------------
        | DATOS INICIALES
        |--------------------------------------------------------------------------
        */

        $datosValor = [

            'concepto_id' =>
                $conceptoIdSeleccionado > 0
                    ? $conceptoIdSeleccionado
                    : '',

            'categoria_id' =>
                '',

            'escalafon_id' =>
                '',

            'monto' =>
                '0.00',

            'porcentaje' =>
                '0.00',

            'fecha_desde' =>
                '',

            'fecha_hasta' =>
                ''
        ];


        try {

            /*
            |--------------------------------------------------------------------------
            | DATOS PARA COMBOS
            |--------------------------------------------------------------------------
            */

            $conceptosDisponibles =
                $this->filtrarConceptosConValoresPorCategoria(
                    $this->modelo
                        ->obtenerConceptosActivosParaValores()
                );


            $categoriasValores =
                $this->modelo
                    ->obtenerCategoriasParaValores();


            $escalafonesValores =
                $this->modelo
                    ->obtenerEscalafonesParaValores();


            /*
            |--------------------------------------------------------------------------
            | MAPA DE FORMAS
            |--------------------------------------------------------------------------
            */

            $formasConcepto =
                $this->crearMapaFormasConcepto(
                    $conceptosDisponibles
                );


            /*
            |--------------------------------------------------------------------------
            | VALIDAR PRESELECCIÓN
            |--------------------------------------------------------------------------
            */

            if (
                $conceptoIdSeleccionado > 0 &&
                !array_key_exists(
                    $conceptoIdSeleccionado,
                    $formasConcepto
                )
            ) {

                $conceptoIdSeleccionado = 0;

                $datosValor['concepto_id'] = '';
            }


            /*
            |--------------------------------------------------------------------------
            | PROCESAR POST
            |--------------------------------------------------------------------------
            */

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {

                /*
                |--------------------------------------------------------------------------
                | PROTECCIÓN CSRF
                |--------------------------------------------------------------------------
                */

                if (
                    !sigenmuniCsrfValido(
                        $_POST['_csrf']
                        ?? ''
                    )
                ) {
                    throw new Exception(
                        "La sesión del formulario no es válida o expiró. Recargue la página e inténtelo nuevamente."
                    );
                }


                $datosValor =
                    $this->recibirDatosValor();


                if ($modoIntegrado) {

                    $datosValor['concepto_id'] =
                        $conceptoIdSeleccionado;
                }


                /*
                |--------------------------------------------------------------------------
                | VALIDAR Y NORMALIZAR
                |--------------------------------------------------------------------------
                */

                $datosGuardar =
                    $this->validarYNormalizarValor(
                        $datosValor
                    );


                /*
                |--------------------------------------------------------------------------
                | DUPLICADO
                |--------------------------------------------------------------------------
                */

                if (
                    $this->modelo->existeValorDuplicado(
                        $datosGuardar['concepto_id'],
                        $datosGuardar['categoria_id'],
                        $datosGuardar['escalafon_id'],
                        $datosGuardar['fecha_desde']
                    )
                ) {

                    throw new Exception(
                        "Ya existe un valor activo para ese concepto con la misma categoría/escalafón y fecha desde."
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | GUARDAR
                |--------------------------------------------------------------------------
                */

                $idValor =
                    $this->modelo->guardarValor(
                        $datosGuardar
                    );


                if ($idValor <= 0) {

                    throw new Exception(
                        "No se pudo guardar el valor del concepto."
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | REDIRECCIONAR
                |--------------------------------------------------------------------------
                */

                if ($modoIntegrado) {

                    $urlDestino =
                        sigenmuniUrlRuta(
                            'conceptos/editar',
                            [
                                'id' => (int)$datosGuardar['concepto_id'],
                                'valor_ok' => 'nuevo'
                            ]
                        );

                } elseif ($retornoValores === 'filtrado') {

                    $conceptoIdRetorno =
                        $conceptoIdContextoValores > 0
                            ? $conceptoIdContextoValores
                            : (int)$datosGuardar['concepto_id'];


                    $urlDestino =
                        sigenmuniUrlRuta(
                            'conceptos/valores',
                            [
                                'concepto_id' => $conceptoIdRetorno,
                                'ok' => 'nuevo'
                            ]
                        );

                } else {

                    $urlDestino =
                        sigenmuniUrlRuta(
                            'conceptos/valores',
                            [
                                'ok' => 'nuevo'
                            ]
                        );
                }


                header(
                    'Location: ' . $urlDestino
                );

                exit();
            }

        } catch (Exception $e) {

            $mensaje =
                $e->getMessage();

            $tipo_mensaje =
                "error";
        }


        /*
        |--------------------------------------------------------------------------
        | CARGAR VISTA
        |--------------------------------------------------------------------------
        */

        require __DIR__
            . '/../vista/concepto_valor_nuevo.php';
    }


    /*
    |--------------------------------------------------------------------------
    | EDITAR VALOR
    |--------------------------------------------------------------------------
    */

    public function editarValor()
    {
        $id = isset($_GET['id'])
            ? (int)$_GET['id']
            : 0;


        $origen =
            trim(
                $_POST['origen']
                ?? $_GET['origen']
                ?? ''
            );


        $modoIntegrado =
            ($origen === 'concepto');


        /*
        |--------------------------------------------------------------------------
        | CONTEXTO DE RETORNO
        |--------------------------------------------------------------------------
        */

        $conceptoIdContextoValores =
            isset($_POST['concepto_id_contexto'])
                ? (int)$_POST['concepto_id_contexto']
                : (
                    isset($_GET['concepto_id'])
                        ? (int)$_GET['concepto_id']
                        : 0
                );


        $retornoValores =
            trim(
                $_POST['retorno']
                ?? $_GET['retorno']
                ?? ''
            );


        if (
            $retornoValores !== 'todos'
            &&
            $retornoValores !== 'filtrado'
        ) {
            $retornoValores =
                $conceptoIdContextoValores > 0
                    ? 'filtrado'
                    : 'todos';
        }


        $mensaje = "";
        $tipo_mensaje = "error";


        /*
        |--------------------------------------------------------------------------
        | VALIDAR ID
        |--------------------------------------------------------------------------
        */

        if ($id <= 0) {

            $urlValores =
                sigenmuniUrlRuta(
                    'conceptos/valores'
                );

            header(
                'Location: ' . $urlValores
            );

            exit();
        }


        try {

            /*
            |--------------------------------------------------------------------------
            | OBTENER VALOR
            |--------------------------------------------------------------------------
            */

            $valor =
                $this->modelo->obtenerValorPorId(
                    $id
                );


            if (!$valor) {

                $urlValores =
                    sigenmuniUrlRuta(
                        'conceptos/valores'
                    );

                header(
                    'Location: ' . $urlValores
                );

                exit();
            }


            /*
            |--------------------------------------------------------------------------
            | CONCEPTO ORIGINAL
            |--------------------------------------------------------------------------
            */

            $conceptoIdOriginal =
                (int)$valor['concepto_id'];


            if (
                $retornoValores === 'filtrado'
                &&
                $conceptoIdContextoValores <= 0
            ) {
                $conceptoIdContextoValores =
                    $conceptoIdOriginal;
            }


            /*
            |--------------------------------------------------------------------------
            | CONCEPTOS DISPONIBLES
            |--------------------------------------------------------------------------
            */

            $conceptosDisponibles =
                $this->filtrarConceptosConValoresPorCategoria(
                    $this->modelo
                        ->obtenerConceptosActivosParaValores()
                );


            /*
            |--------------------------------------------------------------------------
            | CONCEPTO ACTUAL INACTIVO
            |--------------------------------------------------------------------------
            |
            | Se agrega al combo para poder visualizar correctamente
            | la relación actual.
            |
            */

            if (
                (int)$valor['concepto_activo'] !== 1
            ) {

                $conceptosDisponibles[] = [

                    'id' =>
                        (int)$valor['concepto_id'],

                    'codigo' =>
                        $valor['concepto_codigo'],

                    'nombre' =>
                        $valor['concepto_nombre'],

                    'forma_calculo' =>
                        $valor['forma_calculo']
                ];
            }


            /*
            |--------------------------------------------------------------------------
            | CATEGORÍAS / ESCALAFONES
            |--------------------------------------------------------------------------
            */

            $categoriasValores =
                $this->modelo
                    ->obtenerCategoriasParaValores();


            $escalafonesValores =
                $this->modelo
                    ->obtenerEscalafonesParaValores();


            /*
            |--------------------------------------------------------------------------
            | MAPA DE FORMAS
            |--------------------------------------------------------------------------
            */

            $formasConcepto =
                $this->crearMapaFormasConcepto(
                    $conceptosDisponibles
                );


            /*
            |--------------------------------------------------------------------------
            | DATOS INICIALES PARA LA VISTA
            |--------------------------------------------------------------------------
            */

            $datosValor = [

                'id' =>
                    (int)$valor['id'],

                'concepto_id' =>
                    (int)$valor['concepto_id'],

                'categoria_id' =>
                    $valor['categoria_id'],

                'escalafon_id' =>
                    $valor['escalafon_id'],

                'monto' =>
                    $valor['monto'],

                'porcentaje' =>
                    $valor['porcentaje'],

                'fecha_desde' =>
                    $valor['fecha_desde'],

                'fecha_hasta' =>
                    $valor['fecha_hasta']
            ];


            /*
            |--------------------------------------------------------------------------
            | PROCESAR POST
            |--------------------------------------------------------------------------
            */

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {

                /*
                |--------------------------------------------------------------------------
                | PROTECCIÓN CSRF
                |--------------------------------------------------------------------------
                */

                if (
                    !sigenmuniCsrfValido(
                        $_POST['_csrf']
                        ?? ''
                    )
                ) {
                    throw new Exception(
                        "La sesión del formulario no es válida o expiró. Recargue la página e inténtelo nuevamente."
                    );
                }


                $datosValor =
                    $this->recibirDatosValor();


                if ($modoIntegrado) {

                    $datosValor['concepto_id'] =
                        $conceptoIdOriginal;
                }


                $datosValor['id'] =
                    $id;


                /*
                |--------------------------------------------------------------------------
                | VALIDAR Y NORMALIZAR
                |--------------------------------------------------------------------------
                */

                $datosActualizar =
                    $this->validarYNormalizarValor(
                        $datosValor
                    );


                /*
                |--------------------------------------------------------------------------
                | DUPLICADO
                |--------------------------------------------------------------------------
                |
                | Excluye el propio registro.
                |
                */

                if (
                    $this->modelo->existeValorDuplicado(
                        $datosActualizar['concepto_id'],
                        $datosActualizar['categoria_id'],
                        $datosActualizar['escalafon_id'],
                        $datosActualizar['fecha_desde'],
                        $id
                    )
                ) {

                    throw new Exception(
                        "Ya existe otro valor activo para ese concepto con la misma categoría/escalafón y fecha desde."
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | ACTUALIZAR
                |--------------------------------------------------------------------------
                */

                $actualizado =
                    $this->modelo->actualizarValor(
                        $id,
                        $datosActualizar
                    );


                if (!$actualizado) {

                    throw new Exception(
                        "No se pudo actualizar el valor del concepto."
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | REDIRECCIONAR
                |--------------------------------------------------------------------------
                */

                if ($modoIntegrado) {

                    $urlDestino =
                        sigenmuniUrlRuta(
                            'conceptos/editar',
                            [
                                'id' => (int)$datosActualizar['concepto_id'],
                                'valor_ok' => 'editar'
                            ]
                        );

                } elseif ($retornoValores === 'filtrado') {

                    $conceptoIdRetorno =
                        $conceptoIdContextoValores > 0
                            ? $conceptoIdContextoValores
                            : $conceptoIdOriginal;


                    $urlDestino =
                        sigenmuniUrlRuta(
                            'conceptos/valores',
                            [
                                'concepto_id' => $conceptoIdRetorno,
                                'ok' => 'editar'
                            ]
                        );

                } else {

                    $urlDestino =
                        sigenmuniUrlRuta(
                            'conceptos/valores',
                            [
                                'ok' => 'editar'
                            ]
                        );
                }


                header(
                    'Location: ' . $urlDestino
                );

                exit();
            }

        } catch (Exception $e) {

            $mensaje =
                $e->getMessage();

            $tipo_mensaje =
                "error";


            /*
            |--------------------------------------------------------------------------
            | CONSERVAR POST
            |--------------------------------------------------------------------------
            */

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {

                $datosValor = [

                    'id' =>
                        $id,

                    'concepto_id' =>
                        isset($_POST['concepto_id'])
                            ? (int)$_POST['concepto_id']
                            : 0,

                    'categoria_id' =>
                        !empty($_POST['categoria_id'])
                            ? (int)$_POST['categoria_id']
                            : '',

                    'escalafon_id' =>
                        !empty($_POST['escalafon_id'])
                            ? (int)$_POST['escalafon_id']
                            : '',

                    'monto' =>
                        trim($_POST['monto'] ?? '0.00'),

                    'porcentaje' =>
                        trim($_POST['porcentaje'] ?? '0.00'),

                    'fecha_desde' =>
                        trim($_POST['fecha_desde'] ?? ''),

                    'fecha_hasta' =>
                        trim($_POST['fecha_hasta'] ?? '')
                ];


                if ($modoIntegrado) {
                    $datosValor['concepto_id'] =
                        $conceptoIdOriginal
                        ?? 0;
                }
            }
        }


        /*
        |--------------------------------------------------------------------------
        | ASEGURAR VARIABLES DE VISTA
        |--------------------------------------------------------------------------
        */

        if (!isset($datosValor)) {
            $datosValor = [];
        }


        if (!isset($conceptoIdOriginal)) {

            $conceptoIdOriginal =
                isset($datosValor['concepto_id'])
                    ? (int)$datosValor['concepto_id']
                    : 0;
        }


        /*
        |--------------------------------------------------------------------------
        | CARGAR VISTA
        |--------------------------------------------------------------------------
        */

        require __DIR__
            . '/../vista/concepto_valor_editar.php';
    }


    /*
    |--------------------------------------------------------------------------
    | CAMBIAR ESTADO DE VALOR
    |--------------------------------------------------------------------------
    */

    public function estadoValor()
    {
        /*
        |--------------------------------------------------------------------------
        | SOLO POST
        |--------------------------------------------------------------------------
        */

        if (
            ($_SERVER['REQUEST_METHOD'] ?? 'GET')
            !==
            'POST'
        ) {
            http_response_code(405);

            die(
                "Método no permitido."
            );
        }


        /*
        |--------------------------------------------------------------------------
        | DATOS RECIBIDOS
        |--------------------------------------------------------------------------
        */

        $id =
            isset($_POST['id'])
                ? (int)$_POST['id']
                : 0;


        $origen =
            trim(
                $_POST['origen']
                ?? ''
            );


        $modoIntegrado =
            ($origen === 'concepto');


        $conceptoIdContextoValores =
            isset($_POST['concepto_id_contexto'])
                ? (int)$_POST['concepto_id_contexto']
                : 0;


        $retornoValores =
            trim(
                $_POST['retorno']
                ?? ''
            );


        if (
            $retornoValores !== 'todos'
            &&
            $retornoValores !== 'filtrado'
        ) {
            $retornoValores =
                $conceptoIdContextoValores > 0
                    ? 'filtrado'
                    : 'todos';
        }


        /*
        |--------------------------------------------------------------------------
        | PROTECCIÓN CSRF
        |--------------------------------------------------------------------------
        */

        if (
            !sigenmuniCsrfValido(
                $_POST['_csrf']
                ?? ''
            )
        ) {

            $parametrosRetorno = [];

            if (
                $retornoValores === 'filtrado'
                &&
                $conceptoIdContextoValores > 0
            ) {
                $parametrosRetorno['concepto_id'] =
                    $conceptoIdContextoValores;
            }

            header(
                'Location: '
                .
                sigenmuniUrlRuta(
                    'conceptos/valores',
                    $parametrosRetorno
                )
            );

            exit();
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDAR ID
        |--------------------------------------------------------------------------
        */

        if ($id <= 0) {
            header(
                'Location: '
                .
                sigenmuniUrlRuta(
                    'conceptos/valores'
                )
            );

            exit();
        }


        try {

            /*
            |--------------------------------------------------------------------------
            | OBTENER VALOR
            |--------------------------------------------------------------------------
            */

            $valor =
                $this->modelo->obtenerValorPorId(
                    $id
                );


            if (!$valor) {
                header(
                    'Location: '
                    .
                    sigenmuniUrlRuta(
                        'conceptos/valores'
                    )
                );

                exit();
            }


            /*
            |--------------------------------------------------------------------------
            | DATOS ACTUALES
            |--------------------------------------------------------------------------
            */

            $conceptoId =
                (int)$valor['concepto_id'];


            $estadoActual =
                (int)$valor['activo'];


            /*
            |--------------------------------------------------------------------------
            | DETERMINAR NUEVO ESTADO
            |--------------------------------------------------------------------------
            */

            $nuevoEstado =
                $estadoActual === 1
                    ? 0
                    : 1;


            /*
            |--------------------------------------------------------------------------
            | SI SE VA A ACTIVAR, VALIDAR DUPLICADO
            |--------------------------------------------------------------------------
            |
            | Si existe otro registro ACTIVO con el mismo concepto, categoría,
            | escalafón y fecha_desde, no permitimos reactivar este registro.
            |
            |--------------------------------------------------------------------------
            */

            if ($nuevoEstado === 1) {

                if (
                    $this->modelo->existeValorDuplicado(
                        (int)$valor['concepto_id'],
                        $valor['categoria_id'] !== null
                            ? (int)$valor['categoria_id']
                            : null,
                        $valor['escalafon_id'] !== null
                            ? (int)$valor['escalafon_id']
                            : null,
                        $valor['fecha_desde'],
                        $id
                    )
                ) {
                    throw new Exception(
                        "No se puede activar este valor porque ya existe otro valor activo con el mismo concepto, categoría/escalafón y fecha desde."
                    );
                }
            }


            /*
            |--------------------------------------------------------------------------
            | CAMBIAR ESTADO
            |--------------------------------------------------------------------------
            */

            $actualizado =
                $this->modelo->cambiarEstadoValor(
                    $id,
                    $nuevoEstado
                );


            if (!$actualizado) {
                throw new Exception(
                    "No se pudo actualizar el estado del valor."
                );
            }


            /*
            |--------------------------------------------------------------------------
            | REDIRECCIONAR
            |--------------------------------------------------------------------------
            */

            if ($modoIntegrado) {

                $urlDestino =
                    sigenmuniUrlRuta(
                        'conceptos/editar',
                        [
                            'id' => $conceptoId,
                            'valor_ok' => 'estado'
                        ]
                    );

            } elseif ($retornoValores === 'filtrado') {

                $conceptoIdRetorno =
                    $conceptoIdContextoValores > 0
                        ? $conceptoIdContextoValores
                        : $conceptoId;


                $urlDestino =
                    sigenmuniUrlRuta(
                        'conceptos/valores',
                        [
                            'concepto_id' => $conceptoIdRetorno,
                            'ok' => 'estado'
                        ]
                    );

            } else {

                $urlDestino =
                    sigenmuniUrlRuta(
                        'conceptos/valores',
                        [
                            'ok' => 'estado'
                        ]
                    );
            }


            header(
                'Location: ' . $urlDestino
            );

            exit();

        } catch (Exception $e) {

            die(
                "Error al cambiar el estado del valor: "
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
    | FORMAS DE CÁLCULO VISIBLES EN EL FORMULARIO
    |--------------------------------------------------------------------------
    |
    | FIJO se conserva en la base por compatibilidad, pero ya no puede
    | seleccionarse para conceptos nuevos o editados.
    |
    */

    private function obtenerFormasCalculoFormulario()
    {
        $formasModelo =
            $this->modelo->obtenerFormasCalculo();

        $formas = [];

        foreach ($formasModelo as $forma) {

            if ($forma === 'FIJO') {
                continue;
            }

            $formas[] = $forma;
        }

        return $formas;
    }


    /*
    |--------------------------------------------------------------------------
    | CONCEPTOS QUE ADMINISTRAN VALORES POR CATEGORÍA
    |--------------------------------------------------------------------------
    */

    private function filtrarConceptosConValoresPorCategoria(
        $conceptos
    ) {
        $permitidos = [
            '101',
            '102',
            '104'
        ];

        $filtrados = [];

        foreach ($conceptos as $concepto) {

            $codigo =
                (string)($concepto['codigo'] ?? '');

            $forma =
                strtoupper(
                    trim(
                        (string)($concepto['forma_calculo'] ?? '')
                    )
                );

            if (
                in_array(
                    $codigo,
                    $permitidos,
                    true
                )
                &&
                $forma === 'TABLA_CATEGORIA'
            ) {
                $filtrados[] = $concepto;
            }
        }

        return $filtrados;
    }


    /*
    |--------------------------------------------------------------------------
    | CREAR MAPA DE FORMAS DE CÁLCULO
    |--------------------------------------------------------------------------
    */

    private function crearMapaFormasConcepto(
        $conceptos
    ) {
        $formas = [];


        foreach ($conceptos as $concepto) {

            $formas[
                (int)$concepto['id']
            ] =
                $concepto['forma_calculo'];
        }


        return $formas;
    }


    /*
    |--------------------------------------------------------------------------
    | RECIBIR DATOS DEL CONCEPTO
    |--------------------------------------------------------------------------
    */

    private function recibirDatosFormulario()
    {
        return [

            'codigo' =>
                trim($_POST['codigo'] ?? ''),

            'nombre' =>
                trim($_POST['nombre'] ?? ''),

            'categoria' =>
                trim($_POST['categoria'] ?? ''),

            'forma_calculo' =>
                trim($_POST['forma_calculo'] ?? ''),

            'porcentaje' =>
                '0',

            'monto_fijo' =>
                '0',

            /*
            |--------------------------------------------------------------
            | REQUIERE MANUAL
            |--------------------------------------------------------------
            |
            | Ya no depende de un checkbox del formulario.
            | Se determina automáticamente según forma_calculo.
            |
            */

            'requiere_manual' =>
                0,

            /*
            |--------------------------------------------------------------
            | ASIGNABLE A EMPLEADO
            |--------------------------------------------------------------
            |
            | 1 = puede seleccionarse desde Conceptos por Empleado.
            | 0 = concepto automático / no asignable individualmente.
            |
            */

            'asignable_empleado' =>
                isset($_POST['asignable_empleado'])
                    ? 1
                    : 0,

            /*
            |--------------------------------------------------------------
            | BASE DE CÁLCULO
            |--------------------------------------------------------------
            |
            | Solo se conserva cuando forma_calculo = PORCENTAJE.
            |
            */

            'base_calculo' =>
                trim(
                    $_POST['base_calculo']
                    ?? ''
                ),

            'orden_calculo' =>
                0,

            'aplica_sac' =>
                isset($_POST['aplica_sac'])
                    ? 1
                    : 0,

            'visible_recibo' =>
                isset($_POST['visible_recibo'])
                    ? 1
                    : 0,

            'activo' =>
                isset($_POST['activo'])
                    ? 1
                    : 0,

            'descripcion' =>
                trim($_POST['descripcion'] ?? ''),

            'fecha_desde' =>
                trim($_POST['fecha_desde'] ?? ''),

            'fecha_hasta' =>
                trim($_POST['fecha_hasta'] ?? '')
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDAR / NORMALIZAR CONCEPTO
    |--------------------------------------------------------------------------
    */

    private function validarYNormalizarDatos(
        $datos
    ) {
        /*
        |--------------------------------------------------------------------------
        | CAMPOS OBLIGATORIOS
        |--------------------------------------------------------------------------
        */

        if (
            $datos['codigo'] === '' ||
            $datos['nombre'] === '' ||
            $datos['categoria'] === '' ||
            $datos['forma_calculo'] === ''
        ) {
            throw new Exception(
                "Complete todos los campos obligatorios."
            );
        }


        /*
        |--------------------------------------------------------------------------
        | CÓDIGO
        |--------------------------------------------------------------------------
        */

        if (!ctype_digit($datos['codigo'])) {
            throw new Exception(
                "El código debe contener solo números."
            );
        }

        $codigo = (int)$datos['codigo'];

        if ($codigo <= 0) {
            throw new Exception(
                "El código debe ser mayor a cero."
            );
        }


        /*
        |--------------------------------------------------------------------------
        | NOMBRE
        |--------------------------------------------------------------------------
        */

        if (strlen($datos['nombre']) > 150) {
            throw new Exception(
                "El nombre del concepto no puede superar los 150 caracteres."
            );
        }


        /*
        |--------------------------------------------------------------------------
        | CATEGORÍA
        |--------------------------------------------------------------------------
        */

        if (
            !$this->modelo->categoriaValida(
                $datos['categoria']
            )
        ) {
            throw new Exception(
                "La categoría seleccionada no es válida."
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FORMA DE CÁLCULO
        |--------------------------------------------------------------------------
        |
        | Nueva organización:
        |
        | TABLA_CATEGORIA -> solamente 101, 102 y 104.
        | MANUAL          -> monto por empleado.
        | PORCENTAJE      -> porcentaje por empleado.
        | FORMULA         -> cálculo automático del sistema.
        |
        | FIJO queda fuera de uso.
        |
        */

        $formaCalculo =
            strtoupper(
                trim(
                    (string)$datos['forma_calculo']
                )
            );

        if (
            !$this->modelo->formaCalculoValida(
                $formaCalculo
            )
        ) {
            throw new Exception(
                "La forma de cálculo seleccionada no es válida."
            );
        }

        if ($formaCalculo === 'FIJO') {
            throw new Exception(
                "La forma FIJO ya no se utiliza. Use MANUAL para importes por empleado."
            );
        }

        $formasPermitidas = [
            'TABLA_CATEGORIA',
            'MANUAL',
            'PORCENTAJE',
            'FORMULA'
        ];

        if (
            !in_array(
                $formaCalculo,
                $formasPermitidas,
                true
            )
        ) {
            throw new Exception(
                "La forma de cálculo seleccionada no está habilitada."
            );
        }


        /*
        |--------------------------------------------------------------------------
        | REGLA DE LOS TRES CONCEPTOS SALARIALES
        |--------------------------------------------------------------------------
        |
        | 101 - Sueldo Básico
        | 102 - Dedicación Funcional
        | 104 - Suplemento Especial
        |
        | Son los únicos conceptos cuyos montos se administran desde
        | Gestión de Conceptos mediante valores por categoría.
        |
        */

        $codigosPorCategoria = [
            101,
            102,
            104
        ];

        $esConceptoPorCategoria =
            in_array(
                $codigo,
                $codigosPorCategoria,
                true
            );

        if (
            $esConceptoPorCategoria
            &&
            $formaCalculo !== 'TABLA_CATEGORIA'
        ) {
            throw new Exception(
                "Los conceptos 101, 102 y 104 deben utilizar VALOR POR CATEGORÍA."
            );
        }

        if (
            !$esConceptoPorCategoria
            &&
            $formaCalculo === 'TABLA_CATEGORIA'
        ) {
            throw new Exception(
                "VALOR POR CATEGORÍA está reservado para Sueldo Básico, Dedicación Funcional y Suplemento Especial."
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FECHAS
        |--------------------------------------------------------------------------
        */

        if (
            $datos['fecha_desde'] !== '' &&
            !$this->fechaValida(
                $datos['fecha_desde']
            )
        ) {
            throw new Exception(
                "La fecha desde no es válida."
            );
        }

        if (
            $datos['fecha_hasta'] !== '' &&
            !$this->fechaValida(
                $datos['fecha_hasta']
            )
        ) {
            throw new Exception(
                "La fecha hasta no es válida."
            );
        }

        if (
            $datos['fecha_desde'] !== '' &&
            $datos['fecha_hasta'] !== '' &&
            $datos['fecha_hasta'] < $datos['fecha_desde']
        ) {
            throw new Exception(
                "La fecha hasta no puede ser anterior a la fecha desde."
            );
        }


        /*
        |--------------------------------------------------------------------------
        | CAMPOS ECONÓMICOS GENERALES
        |--------------------------------------------------------------------------
        |
        | Continúan fuera de uso:
        |
        | - porcentaje general;
        | - monto fijo;
        | - orden de cálculo.
        |
        | base_calculo se utiliza para cualquier concepto configurado como:
        |
        | forma_calculo = PORCENTAJE
        |
        | El porcentaje concreto continúa cargándose desde Conceptos por Empleado.
        |
        | Ejemplos:
        |
        | 103 - Adicional por Función Jerárquica
        |       base = BASICO_MAS_DEDICACION
        |
        | 105 - Responsabilidad Profesional
        |       base = BASICO
        |
        | 110 - Título
        |       base = BASICO
        |
        | 308 - Gremio
        |       base = TOTAL_REMUNERATIVO
        |
        | Embargos
        |       base = TOTAL_REMUNERATIVO_MENOS_DESCUENTOS_OBLIGATORIOS
        |
        */

        $porcentaje = 0;
        $montoFijo = 0;
        $ordenCalculo = 0;


        if ($formaCalculo === 'PORCENTAJE') {

            $baseCalculo =
                strtoupper(
                    trim(
                        (string)(
                            $datos['base_calculo']
                            ?? ''
                        )
                    )
                );


            if ($baseCalculo === '') {

                throw new Exception(
                    "Debe seleccionar sobre qué base se aplicará el porcentaje del concepto."
                );
            }


            if (
                !$this->modelo
                    ->baseCalculoPorcentajeValida(
                        $baseCalculo
                    )
            ) {

                throw new Exception(
                    "La base de cálculo seleccionada no es válida para un concepto porcentual."
                );
            }


        } else {

            $baseCalculo =
                null;
        }


        /*
        |--------------------------------------------------------------------------
        | REQUIERE MANUAL
        |--------------------------------------------------------------------------
        */

        $requiereManual =
            $formaCalculo === 'MANUAL'
                ? 1
                : 0;


        /*
        |--------------------------------------------------------------------------
        | ASIGNABLE A EMPLEADO
        |--------------------------------------------------------------------------
        |
        | MANUAL y PORCENTAJE se administran desde Conceptos por Empleado.
        | TABLA_CATEGORIA y FORMULA no se asignan individualmente.
        |
        | Esta decisión es automática y no depende de un checkbox en la vista.
        |
        */

        if (
            $formaCalculo === 'MANUAL'
            ||
            $formaCalculo === 'PORCENTAJE'
        ) {
            $asignableEmpleado = 1;
        } else {
            $asignableEmpleado = 0;
        }


        /*
        |--------------------------------------------------------------------------
        | DATOS NORMALIZADOS
        |--------------------------------------------------------------------------
        */

        return [
            'codigo' =>
                $codigo,

            'nombre' =>
                $datos['nombre'],

            'categoria' =>
                $datos['categoria'],

            'forma_calculo' =>
                $formaCalculo,

            'porcentaje' =>
                $porcentaje,

            'monto_fijo' =>
                $montoFijo,

            'requiere_manual' =>
                $requiereManual,

            'asignable_empleado' =>
                $asignableEmpleado,

            'base_calculo' =>
                $baseCalculo,

            'orden_calculo' =>
                $ordenCalculo,

            'aplica_sac' =>
                (int)($datos['aplica_sac'] ?? 0) === 1
                    ? 1
                    : 0,

            'visible_recibo' =>
                (int)($datos['visible_recibo'] ?? 0) === 1
                    ? 1
                    : 0,

            'activo' =>
                (int)($datos['activo'] ?? 0) === 1
                    ? 1
                    : 0,

            'descripcion' =>
                $datos['descripcion'] === ''
                    ? null
                    : $datos['descripcion'],

            'fecha_desde' =>
                $datos['fecha_desde'] === ''
                    ? null
                    : $datos['fecha_desde'],

            'fecha_hasta' =>
                $datos['fecha_hasta'] === ''
                    ? null
                    : $datos['fecha_hasta']
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | RECIBIR DATOS DEL VALOR
    |--------------------------------------------------------------------------
    */

    private function recibirDatosValor()
    {
        return [

            'concepto_id' =>
                isset($_POST['concepto_id'])
                    ? (int)$_POST['concepto_id']
                    : 0,

            'categoria_id' =>
                !empty($_POST['categoria_id'])
                    ? (int)$_POST['categoria_id']
                    : null,

            'escalafon_id' =>
                !empty($_POST['escalafon_id'])
                    ? (int)$_POST['escalafon_id']
                    : null,

            'monto' =>
                trim($_POST['monto'] ?? '0'),

            'porcentaje' =>
                trim($_POST['porcentaje'] ?? '0'),

            'fecha_desde' =>
                trim($_POST['fecha_desde'] ?? ''),

            'fecha_hasta' =>
                trim($_POST['fecha_hasta'] ?? '')
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDAR / NORMALIZAR VALOR
    |--------------------------------------------------------------------------
    */

    private function validarYNormalizarValor(
        $datos
    ) {
        /*
        |--------------------------------------------------------------------------
        | CONCEPTO
        |--------------------------------------------------------------------------
        */

        $conceptoId =
            (int)$datos['concepto_id'];

        if ($conceptoId <= 0) {
            throw new Exception(
                "Debe seleccionar un concepto."
            );
        }

        $concepto =
            $this->modelo->obtenerPorId(
                $conceptoId
            );

        if (!$concepto) {
            throw new Exception(
                "El concepto seleccionado no existe."
            );
        }

        if (
            (int)$concepto['activo'] !== 1
        ) {
            throw new Exception(
                "El concepto seleccionado está inactivo."
            );
        }

        $codigoConcepto =
            (string)($concepto['codigo'] ?? '');

        $conceptosConValores = [
            '101',
            '102',
            '104'
        ];

        if (
            !in_array(
                $codigoConcepto,
                $conceptosConValores,
                true
            )
        ) {
            throw new Exception(
                "Solo Sueldo Básico, Dedicación Funcional y Suplemento Especial admiten valores por categoría."
            );
        }

        $formaCalculo =
            strtoupper(
                trim(
                    (string)($concepto['forma_calculo'] ?? '')
                )
            );

        if ($formaCalculo !== 'TABLA_CATEGORIA') {
            throw new Exception(
                "Este concepto no está configurado como VALOR POR CATEGORÍA."
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FECHA DESDE
        |--------------------------------------------------------------------------
        */

        if ($datos['fecha_desde'] === '') {
            throw new Exception(
                "Debe seleccionar la fecha desde."
            );
        }

        if (
            !$this->fechaValida(
                $datos['fecha_desde']
            )
        ) {
            throw new Exception(
                "La fecha desde no es válida."
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FECHA HASTA
        |--------------------------------------------------------------------------
        */

        if (
            $datos['fecha_hasta'] !== '' &&
            !$this->fechaValida(
                $datos['fecha_hasta']
            )
        ) {
            throw new Exception(
                "La fecha hasta no es válida."
            );
        }

        if (
            $datos['fecha_hasta'] !== '' &&
            $datos['fecha_hasta'] < $datos['fecha_desde']
        ) {
            throw new Exception(
                "La fecha hasta no puede ser anterior a la fecha desde."
            );
        }


        /*
        |--------------------------------------------------------------------------
        | MONTO
        |--------------------------------------------------------------------------
        */

        if (
            $datos['monto'] === ''
            ||
            !is_numeric(
                $datos['monto']
            )
        ) {
            throw new Exception(
                "El monto debe ser un valor numérico."
            );
        }

        $monto =
            (float)$datos['monto'];

        if ($monto <= 0) {
            throw new Exception(
                "Debe ingresar un monto mayor a cero."
            );
        }


        /*
        |--------------------------------------------------------------------------
        | CATEGORÍA
        |--------------------------------------------------------------------------
        */

        $categoriaId =
            $datos['categoria_id'];

        if (
            $categoriaId === null
            ||
            $categoriaId <= 0
            ||
            !$this->modelo->existeCategoriaId(
                $categoriaId
            )
        ) {
            throw new Exception(
                "Debe seleccionar una categoría válida."
            );
        }


        /*
        |--------------------------------------------------------------------------
        | NUEVA ORGANIZACIÓN
        |--------------------------------------------------------------------------
        |
        | Los valores por categoría ya no utilizan escalafón ni porcentaje.
        |
        */

        $escalafonId = null;
        $porcentaje = 0;


        /*
        |--------------------------------------------------------------------------
        | DATOS NORMALIZADOS
        |--------------------------------------------------------------------------
        */

        return [
            'concepto_id' =>
                $conceptoId,

            'categoria_id' =>
                $categoriaId,

            'escalafon_id' =>
                $escalafonId,

            'monto' =>
                $monto,

            'porcentaje' =>
                $porcentaje,

            'fecha_desde' =>
                $datos['fecha_desde'],

            'fecha_hasta' =>
                $datos['fecha_hasta'] === ''
                    ? null
                    : $datos['fecha_hasta'],

            'activo' =>
                1
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDAR FECHA
    |--------------------------------------------------------------------------
    */

    private function fechaValida($fecha)
    {
        $objetoFecha =
            DateTime::createFromFormat(
                'Y-m-d',
                $fecha
            );


        return (
            $objetoFecha !== false &&
            $objetoFecha->format(
                'Y-m-d'
            ) === $fecha
        );
    }
}