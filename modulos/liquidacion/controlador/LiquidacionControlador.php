<?php
require_once __DIR__ . '/../modelo/LiquidacionModelo.php';
require_once __DIR__ . '/../servicio/CalculadoraLiquidacion.php';
require_once __DIR__ . '/../servicio/GeneradorReciboPDF.php';

require_once __DIR__ . '/../../../core/Url.php';
require_once __DIR__ . '/../../../core/Csrf.php';

require_once __DIR__ . '/../../../lib/phpmailer/src/Exception.php';
require_once __DIR__ . '/../../../lib/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../../../lib/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;

class LiquidacionControlador
{
    private $modelo;

    public function __construct($conexion)
    {
        $this->modelo = new LiquidacionModelo($conexion);
    }

    public function index()
    {
        $periodo = trim($_GET['periodo'] ?? '');
        $tipo = trim($_GET['tipo_liquidacion'] ?? '');
        $estado = trim($_GET['estado'] ?? '');

        $mensaje = "";
        $tipo_mensaje = "";


        /*
        |--------------------------------------------------------------------------
        | MENSAJE FLASH
        |--------------------------------------------------------------------------
        */

        if (
            isset($_SESSION['liquidacion_flash'])
            &&
            is_array($_SESSION['liquidacion_flash'])
        ) {
            $mensaje =
                trim(
                    (string)(
                        $_SESSION['liquidacion_flash']['mensaje']
                        ?? ''
                    )
                );

            $tipo_mensaje =
                (
                    (
                        $_SESSION['liquidacion_flash']['tipo']
                        ?? ''
                    )
                    ===
                    'ok'
                )
                    ? 'ok'
                    : 'error';


            unset(
                $_SESSION['liquidacion_flash']
            );
        }

        /*
        |--------------------------------------------------------------------------
        | USUARIO ADMINISTRADOR
        |--------------------------------------------------------------------------
        |
        | Se utiliza para mostrar acciones administrativas en la vista.
        | La autorización real también se vuelve a validar en estado().
        |
        |--------------------------------------------------------------------------
        */

        $esAdminUsuario =
            (
                (int)($_SESSION['es_admin'] ?? 0) === 1
                ||
                strtoupper(
                    trim(
                        (string)($_SESSION['rol'] ?? '')
                    )
                ) === 'ADMIN'
            );

        if (isset($_GET['ok'])) {
            switch ($_GET['ok']) {
                case '1':
                case 'nuevo':
                    $mensaje = "Liquidación creada correctamente.";
                    $tipo_mensaje = "ok";
                    break;

                case '2':
                case 'procesada':
                    $mensaje = "Liquidación procesada correctamente.";
                    $tipo_mensaje = "ok";
                    break;
            }
        }

        if (isset($_GET['msg'])) {
            switch ($_GET['msg']) {
                case 'anulada':
                    $mensaje = "Liquidación anulada correctamente.";
                    $tipo_mensaje = "ok";
                    break;

                case 'ya_anulada':
                    $mensaje = "La liquidación ya se encuentra anulada.";
                    $tipo_mensaje = "error";
                    break;

                case 'no_existe':
                    $mensaje = "La liquidación seleccionada no existe.";
                    $tipo_mensaje = "error";
                    break;

                case 'parametros_invalidos':
                    $mensaje = "Los parámetros recibidos no son válidos.";
                    $tipo_mensaje = "error";
                    break;

                case 'accion_invalida':
                    $mensaje = "La acción solicitada no está permitida.";
                    $tipo_mensaje = "error";
                    break;

                case 'error_estado':
                    $mensaje = "No se pudo actualizar el estado de la liquidación.";
                    $tipo_mensaje = "error";
                    break;

                case 'deshecha':
                    $mensaje = "Procesamiento deshecho correctamente. La liquidación volvió a estado BORRADOR.";
                    $tipo_mensaje = "ok";
                    break;

                case 'error_deshacer':
                    $mensaje = "No se pudo deshacer el procesamiento de la liquidación. No se realizaron cambios parciales.";
                    $tipo_mensaje = "error";
                    break;

                case 'deshacer_no_permitido':
                    $mensaje = "Solo se puede deshacer el procesamiento de una liquidación CERRADA.";
                    $tipo_mensaje = "error";
                    break;

                case 'sin_permiso_deshacer':
                    $mensaje = "Solo un administrador puede deshacer el procesamiento de una liquidación.";
                    $tipo_mensaje = "error";
                    break;
            }
        }

        try {
            if ($periodo !== '' && !$this->modelo->periodoValido($periodo)) {
                $periodo = '';
                $mensaje = "El período ingresado no es válido.";
                $tipo_mensaje = "error";
            }

            if ($tipo !== '' && !$this->modelo->tipoValido($tipo)) {
                $tipo = '';
                $mensaje = "El tipo de liquidación seleccionado no es válido.";
                $tipo_mensaje = "error";
            }

            if ($estado !== '' && !$this->modelo->estadoValido($estado)) {
                $estado = '';
                $mensaje = "El estado seleccionado no es válido.";
                $tipo_mensaje = "error";
            }

            $tipos = $this->modelo->obtenerTipos();
            $estados = $this->modelo->obtenerEstados();

            $liquidaciones = $this->modelo->listar(
                $periodo,
                $tipo,
                $estado
            );


            /*
            |--------------------------------------------------------------------------
            | ESTADO DE CARGA - GASTOS PROTOCOLARES
            |--------------------------------------------------------------------------
            |
            | Para las liquidaciones protocolares en BORRADOR se consulta si ya
            | existe al menos una carga guardada. La vista utiliza este dato para
            | mostrar el botón Procesar solamente cuando corresponde.
            |
            |--------------------------------------------------------------------------
            */

            foreach (
                $liquidaciones
                as
                &$liquidacionItem
            ) {

                $liquidacionItem[
                    'cantidad_cargados_protocolar'
                ] = 0;


                $tipoItem =
                    strtoupper(
                        trim(
                            (string)(
                                $liquidacionItem[
                                    'tipo_liquidacion'
                                ]
                                ?? ''
                            )
                        )
                    );


                $estadoItem =
                    strtoupper(
                        trim(
                            (string)(
                                $liquidacionItem[
                                    'estado'
                                ]
                                ?? ''
                            )
                        )
                    );


                if (
                    $tipoItem
                    ===
                    'GASTOS_PROTOCOLARES'
                    &&
                    $estadoItem
                    ===
                    'BORRADOR'
                ) {

                    $liquidacionItem[
                        'cantidad_cargados_protocolar'
                    ] =
                        $this->modelo
                            ->contarCargasProtocolar(
                                (int)(
                                    $liquidacionItem[
                                        'id'
                                    ]
                                    ?? 0
                                )
                            );
                }
            }


            unset(
                $liquidacionItem
            );


            require __DIR__ . '/../vista/liquidacion.php';

        } catch (Exception $e) {
            die(
                "Error al cargar las liquidaciones: "
                . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8')
            );
        }
    }

    public function nueva()
    {
        $mensaje = "";
        $tipo_mensaje = "error";

        $datosLiquidacion = [
            'tipo_liquidacion' => '',
            'periodo' => '',
            'fecha_liquidacion' => date('Y-m-d'),
            'descripcion' => ''
        ];

        try {
            $tipos = $this->modelo->obtenerTipos();

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
                        "La sesión del formulario no es válida o expiró. "
                        . "Recargue la página e inténtelo nuevamente."
                    );
                }


                $datosLiquidacion =
                    $this->recibirDatosLiquidacion();

                $datosGuardar = $this->validarYNormalizarLiquidacion(
                    $datosLiquidacion
                );

                /*
                |--------------------------------------------------------------------------
                | CONTROL DE DUPLICADOS
                |--------------------------------------------------------------------------
                |
                | MENSUAL / AGUINALDO / GASTOS_PROTOCOLARES:
                |     se mantiene una sola liquidación activa/cerrada por período.
                |
                | COMPLEMENTARIA / COMPLEMENTARIA_SAC:
                |     se permiten varias liquidaciones del mismo período, porque
                |     pueden corresponder a distintos grupos de empleados.
                |
                */

                $tipoNuevo =
                    strtoupper(
                        trim(
                            (string)$datosGuardar['tipo_liquidacion']
                        )
                    );


                $permiteVariasMismoPeriodo =
                    in_array(
                        $tipoNuevo,
                        [
                            'COMPLEMENTARIA',
                            'COMPLEMENTARIA_SAC'
                        ],
                        true
                    );


                if (
                    !$permiteVariasMismoPeriodo
                    &&
                    $this->modelo->existePorPeriodoYTipo(
                        $datosGuardar['periodo'],
                        $datosGuardar['tipo_liquidacion']
                    )
                ) {

                    throw new Exception(
                        "Ya existe una liquidación activa o cerrada del mismo tipo para el período seleccionado."
                    );
                }

                $datosGuardar['estado'] = 'BORRADOR';

                $idLiquidacion = $this->modelo->guardar($datosGuardar);

                if ($idLiquidacion <= 0) {
                    throw new Exception("No se pudo guardar la liquidación.");
                }

                $urlDestino =
                    sigenmuniUrlRuta(
                            'liquidacion',
                            [
                                'ok' => 'nuevo'
                            ]
                        );


                header(
                    'Location: ' . $urlDestino
                );

                exit();
            }

        } catch (Exception $e) {
            $mensaje = $e->getMessage();
            $tipo_mensaje = "error";
        }

        require __DIR__ . '/../vista/liquidacion_nueva.php';
    }


    /*
    |--------------------------------------------------------------------------
    | NOVEDADES DE LIQUIDACIÓN
    |--------------------------------------------------------------------------
    |
    | Permite definir, por empleado y por liquidación:
    |
    | - días liquidados (0 a 30);
    | - si corresponde Presentismo;
    | - una observación opcional.
    |
    | Solo se guardan excepciones. El valor predeterminado de días es el máximo
    | laboral habilitado para cada empleado, acompañado de Presentismo Sí y sin
    | observación.
    |
    | Si el empleado queda exactamente en esos valores, se elimina cualquier
    | novedad previa.
    |
    */

    public function novedades()
    {
        $liquidacionId =
            isset($_GET['id'])
                ? (int)$_GET['id']
                : 0;

        $mensaje = "";
        $tipo_mensaje = "";

        $transaccionIniciada =
            false;


        if ($liquidacionId <= 0) {

            $urlDestino =
                sigenmuniUrlRuta(
                        'liquidacion',
                        [
                            'msg' => 'parametros_invalidos'
                        ]
                    );


            header(
                'Location: ' . $urlDestino
            );

            exit();
        }


        try {

            /*
            |--------------------------------------------------------------------------
            | LIQUIDACIÓN
            |--------------------------------------------------------------------------
            */

            $liquidacion =
                $this->modelo->obtenerPorId(
                    $liquidacionId
                );


            if (!$liquidacion) {

                $urlDestino =
                    sigenmuniUrlRuta(
                            'liquidacion',
                            [
                                'msg' => 'no_existe'
                            ]
                        );


                header(
                    'Location: ' . $urlDestino
                );

                exit();
            }


            $estadoLiquidacion =
                strtoupper(
                    trim(
                        (string)(
                            $liquidacion['estado']
                            ?? ''
                        )
                    )
                );


            if ($estadoLiquidacion !== 'BORRADOR') {

                throw new Exception(
                    "Las novedades solo pueden modificarse mientras la liquidación se encuentre en estado BORRADOR."
                );
            }


            $tipoLiquidacion =
                strtoupper(
                    trim(
                        (string)(
                            $liquidacion['tipo_liquidacion']
                            ?? ''
                        )
                    )
                );


            /*
            |--------------------------------------------------------------------------
            | TIPOS QUE UTILIZAN NOVEDADES DE DÍAS / PRESENTISMO
            |--------------------------------------------------------------------------
            |
            | MENSUAL:
            |     uso normal.
            |
            | COMPLEMENTARIA:
            |     utiliza su propia pantalla de Personal y Novedades.
            |
            | AGUINALDO:
            |     conserva su lógica propia.
            |
            | GASTOS_PROTOCOLARES:
            |     conserva su pantalla y cálculo independiente.
            |
            */

            $tiposConNovedades = [
                'MENSUAL'
            ];


            if (
                !in_array(
                    $tipoLiquidacion,
                    $tiposConNovedades,
                    true
                )
            ) {

                throw new Exception(
                    "Este tipo de liquidación no utiliza novedades de días liquidados y presentismo."
                );
            }


            /*
            |--------------------------------------------------------------------------
            | EMPLEADOS
            |--------------------------------------------------------------------------
            */

            $empleadosNovedades =
                $this->modelo
                    ->obtenerEmpleadosParaNovedades(
                        $liquidacionId
                    );


            if (empty($empleadosNovedades)) {

                throw new Exception(
                    "No hay empleados con relación laboral en el período disponibles para cargar novedades."
                );
            }


            /*
            |--------------------------------------------------------------------------
            | GUARDAR NOVEDADES
            |--------------------------------------------------------------------------
            */

            if (
                $_SERVER['REQUEST_METHOD']
                ===
                'POST'
            ) {

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
                        "La sesión del formulario no es válida o expiró. "
                        . "Recargue la página e inténtelo nuevamente."
                    );
                }


                $empleadosPost =
                    $_POST['empleados']
                    ?? [];


                if (!is_array($empleadosPost)) {

                    throw new Exception(
                        "Los datos de novedades recibidos no son válidos."
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | IDS VÁLIDOS
                |--------------------------------------------------------------------------
                |
                | Solo se aceptan empleados obtenidos desde el modelo.
                |
                */

                $empleadosValidos = [];


                foreach (
                    $empleadosNovedades
                    as
                    $empleado
                ) {

                    $empleadoId =
                        (int)(
                            $empleado['id']
                            ?? $empleado['empleado_id']
                            ?? 0
                        );


                    if ($empleadoId > 0) {

                        $empleadosValidos[
                            $empleadoId
                        ] =
                            $empleado;
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | TRANSACCIÓN
                |--------------------------------------------------------------------------
                */

                $this->modelo
                    ->iniciarTransaccion();

                $transaccionIniciada =
                    true;


                foreach (
                    $empleadosValidos
                    as
                    $empleadoId => $empleadoDisponible
                ) {

                    $fila =
                        $empleadosPost[
                            $empleadoId
                        ]
                        ?? [];


                    /*
                    |--------------------------------------------------------------------------
                    | DÍAS LABORALES HABILITADOS
                    |--------------------------------------------------------------------------
                    */

                    $diasHabilitados =
                        (int)(
                            $empleadoDisponible[
                                'dias_laborales_habilitados'
                            ]
                            ?? $empleadoDisponible[
                                'limite_dias_liquidables'
                            ]
                            ?? 30
                        );


                    if ($diasHabilitados < 0) {
                        $diasHabilitados = 0;
                    }


                    if ($diasHabilitados > 30) {
                        $diasHabilitados = 30;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | DÍAS A LIQUIDAR
                    |--------------------------------------------------------------------------
                    */

                    $diasTexto =
                        trim(
                            (string)(
                                $fila['dias_liquidados']
                                ?? $diasHabilitados
                            )
                        );


                    if (
                        $diasTexto === ''
                        ||
                        !ctype_digit(
                            $diasTexto
                        )
                    ) {

                        throw new Exception(
                            "Los días liquidados deben ser un número entero entre 0 y "
                            . $diasHabilitados
                            . "."
                        );
                    }


                    $diasLiquidados =
                        (int)$diasTexto;


                    if (
                        $diasLiquidados < 0
                        ||
                        $diasLiquidados > $diasHabilitados
                    ) {

                        throw new Exception(
                            "El empleado "
                            . trim(
                                (string)(
                                    $empleadoDisponible['apellido']
                                    ?? ''
                                )
                                . ", "
                                . (string)(
                                    $empleadoDisponible['nombre']
                                    ?? ''
                                )
                            )
                            . " tiene un máximo de "
                            . $diasHabilitados
                            . " días laborales habilitados para el período."
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | PRESENTISMO
                    |--------------------------------------------------------------------------
                    */

                    $aplicaPresentismo =
                        isset(
                            $fila['aplica_presentismo']
                        )
                        &&
                        (string)$fila[
                            'aplica_presentismo'
                        ]
                        ===
                        '1'
                            ? 1
                            : 0;


                    /*
                    |--------------------------------------------------------------------------
                    | SI NO HAY DÍAS, NO PUEDE HABER PRESENTISMO
                    |--------------------------------------------------------------------------
                    */

                    if ($diasLiquidados === 0) {

                        $aplicaPresentismo =
                            0;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | OBSERVACIÓN
                    |--------------------------------------------------------------------------
                    */

                    $observacion =
                        trim(
                            (string)(
                                $fila['observacion']
                                ?? ''
                            )
                        );


                    if (
                        strlen(
                            $observacion
                        )
                        >
                        255
                    ) {

                        throw new Exception(
                            "La observación de una novedad no puede superar los 255 caracteres."
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | GUARDAR SOLO EXCEPCIONES
                    |--------------------------------------------------------------------------
                    */

                    $esValorPredeterminado =
                        (
                            $diasLiquidados === $diasHabilitados
                            &&
                            $aplicaPresentismo === 1
                            &&
                            $observacion === ''
                        );


                    if ($esValorPredeterminado) {

                        $this->modelo
                            ->eliminarNovedad(
                                $liquidacionId,
                                $empleadoId
                            );

                    } else {

                        $this->modelo
                            ->guardarNovedad(
                                $liquidacionId,
                                $empleadoId,
                                $diasLiquidados,
                                $aplicaPresentismo,
                                $observacion
                            );
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | COMMIT
                |--------------------------------------------------------------------------
                */

                $this->modelo
                    ->confirmarTransaccion();

                $transaccionIniciada =
                    false;


                $urlDestino =
                    sigenmuniUrlRuta(
                            'liquidacion/novedades',
                            [
                                'id' =>
                                    $liquidacionId,

                                'ok' =>
                                    'guardado'
                            ]
                        );


                header(
                    'Location: ' . $urlDestino
                );

                exit();
            }


            /*
            |--------------------------------------------------------------------------
            | MENSAJE DE ÉXITO
            |--------------------------------------------------------------------------
            */

            if (
                isset($_GET['ok'])
                &&
                $_GET['ok'] === 'guardado'
            ) {

                $mensaje =
                    "Novedades de liquidación guardadas correctamente.";

                $tipo_mensaje =
                    "ok";
            }


            /*
            |--------------------------------------------------------------------------
            | CONTAR EXCEPCIONES
            |--------------------------------------------------------------------------
            */

            $cantidadNovedades =
                0;


            foreach (
                $empleadosNovedades
                as
                $empleado
            ) {

                if (
                    !empty(
                        $empleado['novedad_id']
                    )
                ) {

                    $cantidadNovedades++;
                }
            }


        } catch (Throwable $e) {

            /*
            |--------------------------------------------------------------------------
            | ROLLBACK
            |--------------------------------------------------------------------------
            */

            if ($transaccionIniciada) {

                try {

                    $this->modelo
                        ->revertirTransaccion();

                } catch (Throwable $rollbackException) {

                    // Se conserva el error principal.
                }


                $transaccionIniciada =
                    false;
            }


            $mensaje =
                $e->getMessage();

            $tipo_mensaje =
                "error";


            /*
            |--------------------------------------------------------------------------
            | RECUPERAR DATOS PARA VOLVER A MOSTRAR LA VISTA
            |--------------------------------------------------------------------------
            */

            try {

                $empleadosNovedades =
                    isset($liquidacion)
                    &&
                    $liquidacion
                        ?
                        $this->modelo
                            ->obtenerEmpleadosParaNovedades(
                                $liquidacionId
                            )
                        :
                        [];

            } catch (Throwable $consultaException) {

                $empleadosNovedades =
                    [];
            }


            $cantidadNovedades =
                0;


            foreach (
                $empleadosNovedades
                as
                $empleado
            ) {

                if (
                    !empty(
                        $empleado['novedad_id']
                    )
                ) {

                    $cantidadNovedades++;
                }
            }
        }


        /*
        |--------------------------------------------------------------------------
        | VISTA
        |--------------------------------------------------------------------------
        */

        require __DIR__
            . '/../vista/liquidacion_novedades.php';
    }




    /*
    |--------------------------------------------------------------------------
    | PERSONAL Y NOVEDADES - COMPLEMENTARIA DE HABERES
    |--------------------------------------------------------------------------
    |
    | Permite:
    |
    | - seleccionar exactamente qué empleados participan;
    | - definir días liquidados entre 1 y 30;
    | - indicar si corresponde Presentismo;
    | - agregar una observación.
    |
    | La selección se guarda en liquidacion_participante.
    | Las novedades se guardan en liquidacion_novedad.
    |
    */

    public function complementaria()
    {
        $liquidacionId =
            isset($_GET['id'])
                ? (int)$_GET['id']
                : 0;

        $mensaje = "";
        $tipo_mensaje = "";
        $transaccionIniciada = false;


        if ($liquidacionId <= 0) {

            $urlDestino =
                sigenmuniUrlRuta(
                        'liquidacion',
                        [
                            'msg' => 'parametros_invalidos'
                        ]
                    );


            header(
                'Location: ' . $urlDestino
            );

            exit();
        }


        try {

            $liquidacion =
                $this->modelo->obtenerPorId(
                    $liquidacionId
                );


            if (!$liquidacion) {

                $urlDestino =
                    sigenmuniUrlRuta(
                            'liquidacion',
                            [
                                'msg' => 'no_existe'
                            ]
                        );


                header(
                    'Location: ' . $urlDestino
                );

                exit();
            }


            $estadoLiquidacion =
                strtoupper(
                    trim(
                        (string)(
                            $liquidacion['estado']
                            ?? ''
                        )
                    )
                );


            if ($estadoLiquidacion !== 'BORRADOR') {

                throw new Exception(
                    "El personal de una liquidación COMPLEMENTARIA solo puede modificarse mientras se encuentre en estado BORRADOR."
                );
            }


            $tipoLiquidacion =
                strtoupper(
                    trim(
                        (string)(
                            $liquidacion['tipo_liquidacion']
                            ?? ''
                        )
                    )
                );


            if ($tipoLiquidacion !== 'COMPLEMENTARIA') {

                throw new Exception(
                    "La liquidación seleccionada no corresponde a una COMPLEMENTARIA de haberes."
                );
            }


            $empleadosComplementaria =
                $this->modelo
                    ->obtenerEmpleadosParaComplementaria(
                        $liquidacionId
                    );


            if (empty($empleadosComplementaria)) {

                throw new Exception(
                    "No hay empleados disponibles para esta liquidación complementaria."
                );
            }


            if (
                $_SERVER['REQUEST_METHOD']
                ===
                'POST'
            ) {

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
                        "La sesión del formulario no es válida o expiró. "
                        . "Recargue la página e inténtelo nuevamente."
                    );
                }


                $empleadosPost =
                    $_POST['empleados']
                    ?? [];


                if (!is_array($empleadosPost)) {

                    throw new Exception(
                        "Los datos de empleados recibidos no son válidos."
                    );
                }


                $empleadosValidos = [];


                foreach (
                    $empleadosComplementaria
                    as
                    $empleado
                ) {

                    $empleadoId =
                        (int)(
                            $empleado['id']
                            ?? $empleado['empleado_id']
                            ?? 0
                        );


                    if ($empleadoId > 0) {

                        $empleadosValidos[
                            $empleadoId
                        ] =
                            $empleado;
                    }
                }


                $this->modelo
                    ->iniciarTransaccion();

                $transaccionIniciada = true;


                foreach (
                    $empleadosValidos
                    as
                    $empleadoId => $empleadoDisponible
                ) {

                    $fila =
                        $empleadosPost[
                            $empleadoId
                        ]
                        ?? [];


                    $incluido =
                        isset(
                            $fila['incluir']
                        )
                        &&
                        (string)$fila['incluir']
                        ===
                        '1';


                    /*
                    |--------------------------------------------------------------------------
                    | NO INCLUIDO
                    |--------------------------------------------------------------------------
                    */

                    if (!$incluido) {

                        $this->modelo
                            ->eliminarParticipante(
                                $liquidacionId,
                                $empleadoId
                            );


                        /*
                        | Si deja de participar, tampoco debe conservar novedades
                        | propias de esta liquidación complementaria.
                        */

                        $this->modelo
                            ->eliminarNovedad(
                                $liquidacionId,
                                $empleadoId
                            );


                        continue;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | DÍAS
                    |--------------------------------------------------------------------------
                    */

                    $diasHabilitados =
                        (int)(
                            $empleadoDisponible[
                                'dias_laborales_habilitados'
                            ]
                            ?? $empleadoDisponible[
                                'limite_dias_liquidables'
                            ]
                            ?? 30
                        );


                    if ($diasHabilitados < 1) {

                        throw new Exception(
                            "El empleado seleccionado no posee días laborales habilitados en el período."
                        );
                    }


                    $diasTexto =
                        trim(
                            (string)(
                                $fila['dias_liquidados']
                                ?? $diasHabilitados
                            )
                        );


                    if (
                        $diasTexto === ''
                        ||
                        !ctype_digit(
                            $diasTexto
                        )
                    ) {

                        throw new Exception(
                            "Los días liquidados deben ser un número entero entre 1 y "
                            . $diasHabilitados
                            . "."
                        );
                    }


                    $diasLiquidados =
                        (int)$diasTexto;


                    if (
                        $diasLiquidados < 1
                        ||
                        $diasLiquidados > $diasHabilitados
                    ) {

                        throw new Exception(
                            "El empleado "
                            . trim(
                                (string)(
                                    $empleadoDisponible['apellido']
                                    ?? ''
                                )
                                . ", "
                                . (string)(
                                    $empleadoDisponible['nombre']
                                    ?? ''
                                )
                            )
                            . " tiene un máximo de "
                            . $diasHabilitados
                            . " días laborales habilitados para esta complementaria."
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | PRESENTISMO
                    |--------------------------------------------------------------------------
                    */

                    $aplicaPresentismo =
                        isset(
                            $fila['aplica_presentismo']
                        )
                        &&
                        (string)$fila['aplica_presentismo']
                        ===
                        '1'
                            ? 1
                            : 0;


                    /*
                    |--------------------------------------------------------------------------
                    | OBSERVACIÓN
                    |--------------------------------------------------------------------------
                    */

                    $observacion =
                        trim(
                            (string)(
                                $fila['observacion']
                                ?? ''
                            )
                        );


                    if (
                        strlen(
                            $observacion
                        )
                        >
                        255
                    ) {

                        throw new Exception(
                            "La observación no puede superar los 255 caracteres."
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | PARTICIPANTE
                    |--------------------------------------------------------------------------
                    */

                    $this->modelo
                        ->guardarParticipante(
                            $liquidacionId,
                            $empleadoId
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | NOVEDAD
                    |--------------------------------------------------------------------------
                    */

                    $esValorPredeterminado =
                        (
                            $diasLiquidados === $diasHabilitados
                            &&
                            $aplicaPresentismo === 1
                            &&
                            $observacion === ''
                        );


                    if ($esValorPredeterminado) {

                        $this->modelo
                            ->eliminarNovedad(
                                $liquidacionId,
                                $empleadoId
                            );

                    } else {

                        $this->modelo
                            ->guardarNovedad(
                                $liquidacionId,
                                $empleadoId,
                                $diasLiquidados,
                                $aplicaPresentismo,
                                $observacion
                            );
                    }
                }


                if (
                    $this->modelo
                        ->contarParticipantes(
                            $liquidacionId
                        )
                    <=
                    0
                ) {

                    throw new Exception(
                        "Debe seleccionar al menos un empleado para la liquidación COMPLEMENTARIA."
                    );
                }


                $this->modelo
                    ->confirmarTransaccion();

                $transaccionIniciada = false;


                $urlDestino =
                    sigenmuniUrlRuta(
                            'liquidacion/complementaria',
                            [
                                'id' =>
                                    $liquidacionId,

                                'ok' =>
                                    'guardado'
                            ]
                        );


                header(
                    'Location: ' . $urlDestino
                );

                exit();
            }


            if (
                isset($_GET['ok'])
                &&
                $_GET['ok'] === 'guardado'
            ) {

                $mensaje =
                    "Personal y novedades de la complementaria guardados correctamente.";

                $tipo_mensaje =
                    "ok";
            }


            $cantidadSeleccionados =
                $this->modelo
                    ->contarParticipantes(
                        $liquidacionId
                    );


        } catch (Throwable $e) {

            if ($transaccionIniciada) {

                try {

                    $this->modelo
                        ->revertirTransaccion();

                } catch (Throwable $rollbackException) {

                    // Se conserva el error principal.
                }


                $transaccionIniciada =
                    false;
            }


            $mensaje =
                $e->getMessage();

            $tipo_mensaje =
                "error";


            try {

                $empleadosComplementaria =
                    isset($liquidacion)
                    &&
                    $liquidacion
                        ?
                        $this->modelo
                            ->obtenerEmpleadosParaComplementaria(
                                $liquidacionId
                            )
                        :
                        [];


                $cantidadSeleccionados =
                    isset($liquidacion)
                    &&
                    $liquidacion
                        ?
                        $this->modelo
                            ->contarParticipantes(
                                $liquidacionId
                            )
                        :
                        0;

            } catch (Throwable $consultaException) {

                $empleadosComplementaria =
                    [];

                $cantidadSeleccionados =
                    0;
            }
        }


        require __DIR__
            . '/../vista/liquidacion_complementaria.php';
    }



    /*
    |--------------------------------------------------------------------------
    | NOVEDADES DE SAC
    |--------------------------------------------------------------------------
    |
    | Permite definir los días computables de SAC por empleado.
    |
    | 180 días = SAC completo del semestre.
    | 1 a 179  = SAC proporcional.
    | 0        = no genera SAC.
    |
    | Solo se modifica mientras la liquidación AGUINALDO esté en BORRADOR.
    |
    */

    public function novedadesSac()
    {
        $liquidacionId =
            isset($_GET['id'])
                ? (int)$_GET['id']
                : 0;

        $mensaje = "";
        $tipo_mensaje = "";
        $transaccionIniciada = false;


        if ($liquidacionId <= 0) {

            $urlDestino =
                sigenmuniUrlRuta(
                        'liquidacion',
                        [
                            'msg' => 'parametros_invalidos'
                        ]
                    );


            header(
                'Location: ' . $urlDestino
            );

            exit();
        }


        try {

            $liquidacion =
                $this->modelo->obtenerPorId(
                    $liquidacionId
                );


            if (!$liquidacion) {

                $urlDestino =
                    sigenmuniUrlRuta(
                            'liquidacion',
                            [
                                'msg' => 'no_existe'
                            ]
                        );


                header(
                    'Location: ' . $urlDestino
                );

                exit();
            }


            $estadoLiquidacion =
                strtoupper(
                    trim(
                        (string)(
                            $liquidacion['estado']
                            ?? ''
                        )
                    )
                );


            if ($estadoLiquidacion !== 'BORRADOR') {

                throw new Exception(
                    "Los días de SAC solo pueden modificarse mientras la liquidación se encuentre en estado BORRADOR."
                );
            }


            $tipoLiquidacion =
                strtoupper(
                    trim(
                        (string)(
                            $liquidacion['tipo_liquidacion']
                            ?? ''
                        )
                    )
                );


            if ($tipoLiquidacion !== 'AGUINALDO') {

                throw new Exception(
                    "La liquidación seleccionada no corresponde a AGUINALDO."
                );
            }


            $empleadosSac =
                $this->modelo
                    ->obtenerEmpleadosParaSac(
                        $liquidacionId
                    );


            if (empty($empleadosSac)) {

                throw new Exception(
                    "No hay empleados con relación laboral dentro del semestre para liquidar SAC."
                );
            }


            if (
                $_SERVER['REQUEST_METHOD']
                ===
                'POST'
            ) {

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
                        "La sesión del formulario no es válida o expiró. "
                        . "Recargue la página e inténtelo nuevamente."
                    );
                }


                $empleadosPost =
                    $_POST['empleados']
                    ?? [];


                if (!is_array($empleadosPost)) {

                    throw new Exception(
                        "Los datos de SAC recibidos no son válidos."
                    );
                }


                $empleadosPorId = [];


                foreach (
                    $empleadosSac
                    as
                    $empleado
                ) {

                    $empleadoId =
                        (int)(
                            $empleado['id']
                            ?? $empleado['empleado_id']
                            ?? 0
                        );


                    if ($empleadoId > 0) {

                        $empleadosPorId[
                            $empleadoId
                        ] = $empleado;
                    }
                }


                $this->modelo
                    ->iniciarTransaccion();

                $transaccionIniciada = true;


                foreach (
                    $empleadosPorId
                    as
                    $empleadoId => $empleadoSac
                ) {

                    $diasPendientes =
                        (int)(
                            $empleadoSac[
                                'dias_sac_pendientes'
                            ]
                            ?? 0
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | YA LIQUIDADO
                    |--------------------------------------------------------------------------
                    */

                    if ($diasPendientes <= 0) {

                        $this->modelo
                            ->eliminarNovedadSac(
                                $liquidacionId,
                                $empleadoId
                            );

                        continue;
                    }


                    $fila =
                        $empleadosPost[
                            $empleadoId
                        ]
                        ?? [];


                    $diasTexto =
                        trim(
                            (string)(
                                $fila['dias_sac']
                                ?? $diasPendientes
                            )
                        );


                    if (
                        $diasTexto === ''
                        ||
                        !ctype_digit(
                            $diasTexto
                        )
                    ) {

                        throw new Exception(
                            "Los días de SAC deben ser un número entero válido."
                        );
                    }


                    $diasSac =
                        (int)$diasTexto;


                    if (
                        $diasSac < 0
                        ||
                        $diasSac > $diasPendientes
                    ) {

                        throw new Exception(
                            "Los días de SAC del empleado ID "
                            . $empleadoId
                            . " no pueden superar los "
                            . $diasPendientes
                            . " días pendientes."
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | 0 = NO LIQUIDAR EN ESTA LIQUIDACIÓN
                    |--------------------------------------------------------------------------
                    */

                    if ($diasSac === 0) {

                        $this->modelo
                            ->guardarNovedadSac(
                                $liquidacionId,
                                $empleadoId,
                                0
                            );

                        continue;
                    }


                    $this->modelo
                        ->guardarNovedadSac(
                            $liquidacionId,
                            $empleadoId,
                            $diasSac
                        );
                }


                $this->modelo
                    ->confirmarTransaccion();

                $transaccionIniciada = false;


                $urlDestino =
                    sigenmuniUrlRuta(
                            'liquidacion/novedades-sac',
                            [
                                'id' =>
                                    $liquidacionId,

                                'ok' =>
                                    'guardado'
                            ]
                        );


                header(
                    'Location: ' . $urlDestino
                );

                exit();
            }


            if (
                isset($_GET['ok'])
                &&
                $_GET['ok'] === 'guardado'
            ) {

                $mensaje =
                    "Novedades de SAC guardadas correctamente.";

                $tipo_mensaje =
                    "ok";
            }


            $cantidadSacProporcional = 0;
            $cantidadSacYaLiquidado = 0;
            $cantidadSacPendiente = 0;


            foreach (
                $empleadosSac
                as
                $empleado
            ) {

                $diasPendientes =
                    (int)(
                        $empleado['dias_sac_pendientes']
                        ?? 0
                    );


                $diasPagados =
                    (int)(
                        $empleado['dias_sac_pagados']
                        ?? 0
                    );


                $diasSac =
                    (int)(
                        $empleado['dias_sac']
                        ?? 0
                    );


                if ($diasPendientes <= 0) {

                    $cantidadSacYaLiquidado++;

                } else {

                    $cantidadSacPendiente++;
                }


                if (
                    $diasSac > 0
                    &&
                    $diasSac < 180
                ) {

                    $cantidadSacProporcional++;
                }
            }


        } catch (Throwable $e) {

            if ($transaccionIniciada) {

                try {

                    $this->modelo
                        ->revertirTransaccion();

                } catch (Throwable $rollbackException) {

                    // Se conserva el error principal.
                }


                $transaccionIniciada =
                    false;
            }


            $mensaje =
                $e->getMessage();

            $tipo_mensaje =
                "error";


            try {

                $empleadosSac =
                    isset($liquidacion)
                    &&
                    $liquidacion
                        ?
                        $this->modelo
                            ->obtenerEmpleadosParaSac(
                                $liquidacionId
                            )
                        :
                        [];

            } catch (Throwable $consultaException) {

                $empleadosSac =
                    [];
            }


            $cantidadSacProporcional = 0;
            $cantidadSacYaLiquidado = 0;
            $cantidadSacPendiente = 0;


            foreach (
                $empleadosSac
                as
                $empleado
            ) {

                $diasPendientes =
                    (int)(
                        $empleado['dias_sac_pendientes']
                        ?? 0
                    );


                $diasSac =
                    (int)(
                        $empleado['dias_sac']
                        ?? 0
                    );


                if ($diasPendientes <= 0) {

                    $cantidadSacYaLiquidado++;

                } else {

                    $cantidadSacPendiente++;
                }


                if (
                    $diasSac > 0
                    &&
                    $diasSac < 180
                ) {

                    $cantidadSacProporcional++;
                }
            }
        }


        require __DIR__
            . '/../vista/liquidacion_novedades_sac.php';
    }



    /*
    |--------------------------------------------------------------------------
    | PERSONAL Y DÍAS SAC - COMPLEMENTARIA DE SAC
    |--------------------------------------------------------------------------
    |
    | Permite seleccionar empleados y liquidar únicamente días SAC pendientes.
    |
    | El sistema impide superar:
    |
    | días devengados - días ya pagados.
    |
    */

    public function complementariaSac()
    {
        $liquidacionId =
            isset($_GET['id'])
                ? (int)$_GET['id']
                : 0;

        $mensaje = "";
        $tipo_mensaje = "";
        $transaccionIniciada = false;


        if ($liquidacionId <= 0) {

            $urlDestino =
                sigenmuniUrlRuta(
                        'liquidacion',
                        [
                            'msg' => 'parametros_invalidos'
                        ]
                    );


            header(
                'Location: ' . $urlDestino
            );

            exit();
        }


        try {

            $liquidacion =
                $this->modelo->obtenerPorId(
                    $liquidacionId
                );


            if (!$liquidacion) {

                $urlDestino =
                    sigenmuniUrlRuta(
                            'liquidacion',
                            [
                                'msg' => 'no_existe'
                            ]
                        );


                header(
                    'Location: ' . $urlDestino
                );

                exit();
            }


            $estadoLiquidacion =
                strtoupper(
                    trim(
                        (string)(
                            $liquidacion['estado']
                            ?? ''
                        )
                    )
                );


            if ($estadoLiquidacion !== 'BORRADOR') {

                throw new Exception(
                    "El personal de una COMPLEMENTARIA DE SAC solo puede modificarse mientras se encuentre en estado BORRADOR."
                );
            }


            $tipoLiquidacion =
                strtoupper(
                    trim(
                        (string)(
                            $liquidacion['tipo_liquidacion']
                            ?? ''
                        )
                    )
                );


            if ($tipoLiquidacion !== 'COMPLEMENTARIA_SAC') {

                throw new Exception(
                    "La liquidación seleccionada no corresponde a una COMPLEMENTARIA DE SAC."
                );
            }


            $empleadosSac =
                $this->modelo
                    ->obtenerEmpleadosParaSac(
                        $liquidacionId
                    );


            if (empty($empleadosSac)) {

                throw new Exception(
                    "No hay empleados disponibles para una complementaria de SAC en este período."
                );
            }


            if (
                $_SERVER['REQUEST_METHOD']
                ===
                'POST'
            ) {

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
                        "La sesión del formulario no es válida o expiró. "
                        . "Recargue la página e inténtelo nuevamente."
                    );
                }


                $empleadosPost =
                    $_POST['empleados']
                    ?? [];


                if (!is_array($empleadosPost)) {

                    throw new Exception(
                        "Los datos de empleados recibidos no son válidos."
                    );
                }


                $empleadosPorId = [];


                foreach (
                    $empleadosSac
                    as
                    $empleado
                ) {

                    $empleadoId =
                        (int)(
                            $empleado['id']
                            ?? $empleado['empleado_id']
                            ?? 0
                        );


                    if ($empleadoId > 0) {

                        $empleadosPorId[
                            $empleadoId
                        ] = $empleado;
                    }
                }


                $this->modelo
                    ->iniciarTransaccion();

                $transaccionIniciada = true;


                foreach (
                    $empleadosPorId
                    as
                    $empleadoId => $empleadoSac
                ) {

                    $fila =
                        $empleadosPost[
                            $empleadoId
                        ]
                        ?? [];


                    $incluido =
                        isset(
                            $fila['incluir']
                        )
                        &&
                        (string)$fila['incluir']
                        ===
                        '1';


                    $diasPendientes =
                        (int)(
                            $empleadoSac[
                                'dias_sac_pendientes'
                            ]
                            ?? 0
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | SIN SALDO O NO SELECCIONADO
                    |--------------------------------------------------------------------------
                    */

                    if (
                        !$incluido
                        ||
                        $diasPendientes <= 0
                    ) {

                        $this->modelo
                            ->eliminarParticipante(
                                $liquidacionId,
                                $empleadoId
                            );


                        $this->modelo
                            ->eliminarNovedadSac(
                                $liquidacionId,
                                $empleadoId
                            );


                        continue;
                    }


                    $diasTexto =
                        trim(
                            (string)(
                                $fila['dias_sac']
                                ?? $diasPendientes
                            )
                        );


                    if (
                        $diasTexto === ''
                        ||
                        !ctype_digit(
                            $diasTexto
                        )
                    ) {

                        throw new Exception(
                            "Los días SAC deben ser un número entero válido."
                        );
                    }


                    $diasSac =
                        (int)$diasTexto;


                    if (
                        $diasSac < 1
                        ||
                        $diasSac > $diasPendientes
                    ) {

                        throw new Exception(
                            "El empleado ID "
                            . $empleadoId
                            . " puede liquidar entre 1 y "
                            . $diasPendientes
                            . " días SAC pendientes."
                        );
                    }


                    $this->modelo
                        ->guardarParticipante(
                            $liquidacionId,
                            $empleadoId
                        );


                    $this->modelo
                        ->guardarNovedadSac(
                            $liquidacionId,
                            $empleadoId,
                            $diasSac
                        );
                }


                if (
                    $this->modelo
                        ->contarParticipantes(
                            $liquidacionId
                        )
                    <=
                    0
                ) {

                    throw new Exception(
                        "Debe seleccionar al menos un empleado con días SAC pendientes."
                    );
                }


                $this->modelo
                    ->confirmarTransaccion();

                $transaccionIniciada = false;


                $urlDestino =
                    sigenmuniUrlRuta(
                            'liquidacion/complementaria-sac',
                            [
                                'id' =>
                                    $liquidacionId,

                                'ok' =>
                                    'guardado'
                            ]
                        );


                header(
                    'Location: ' . $urlDestino
                );

                exit();
            }


            if (
                isset($_GET['ok'])
                &&
                $_GET['ok'] === 'guardado'
            ) {

                $mensaje =
                    "Personal y días de la complementaria de SAC guardados correctamente.";

                $tipo_mensaje =
                    "ok";
            }


            $cantidadSeleccionados =
                $this->modelo
                    ->contarParticipantes(
                        $liquidacionId
                    );


            $cantidadSacYaLiquidado = 0;
            $cantidadSacPendiente = 0;


            foreach (
                $empleadosSac
                as
                $empleado
            ) {

                if (
                    (int)(
                        $empleado['dias_sac_pendientes']
                        ?? 0
                    )
                    <=
                    0
                ) {

                    $cantidadSacYaLiquidado++;

                } else {

                    $cantidadSacPendiente++;
                }
            }


        } catch (Throwable $e) {

            if ($transaccionIniciada) {

                try {

                    $this->modelo
                        ->revertirTransaccion();

                } catch (Throwable $rollbackException) {

                    // Se conserva el error principal.
                }


                $transaccionIniciada =
                    false;
            }


            $mensaje =
                $e->getMessage();

            $tipo_mensaje =
                "error";


            try {

                $empleadosSac =
                    isset($liquidacion)
                    &&
                    $liquidacion
                        ?
                        $this->modelo
                            ->obtenerEmpleadosParaSac(
                                $liquidacionId
                            )
                        :
                        [];


                $cantidadSeleccionados =
                    isset($liquidacion)
                    &&
                    $liquidacion
                        ?
                        $this->modelo
                            ->contarParticipantes(
                                $liquidacionId
                            )
                        :
                        0;

            } catch (Throwable $consultaException) {

                $empleadosSac =
                    [];

                $cantidadSeleccionados =
                    0;
            }


            $cantidadSacYaLiquidado = 0;
            $cantidadSacPendiente = 0;


            foreach (
                $empleadosSac
                as
                $empleado
            ) {

                if (
                    (int)(
                        $empleado['dias_sac_pendientes']
                        ?? 0
                    )
                    <=
                    0
                ) {

                    $cantidadSacYaLiquidado++;

                } else {

                    $cantidadSacPendiente++;
                }
            }
        }


        require __DIR__
            . '/../vista/liquidacion_complementaria_sac.php';
    }



    /*
    |--------------------------------------------------------------------------
    | CARGA DE GASTOS PROTOCOLARES
    |--------------------------------------------------------------------------
    */

    public function protocolar()
    {
        $liquidacionId =
            isset($_GET['id'])
                ? (int)$_GET['id']
                : 0;

        $mensaje = "";
        $tipo_mensaje = "";

        /*
        |--------------------------------------------------------------------------
        | CONTROL DE TRANSACCIÓN
        |--------------------------------------------------------------------------
        |
        | La carga de Gastos Protocolares modifica varios registros.
        | Si alguna operación falla, se ejecutará ROLLBACK.
        |
        |--------------------------------------------------------------------------
        */

        $transaccionIniciada = false;


        if ($liquidacionId <= 0) {

            $urlDestino =
                sigenmuniUrlRuta(
                        'liquidacion',
                        [
                            'msg' => 'parametros_invalidos'
                        ]
                    );


            header(
                'Location: ' . $urlDestino
            );

            exit();
        }


        try {

            $liquidacion =
                $this->modelo->obtenerPorId(
                    $liquidacionId
                );


            if (!$liquidacion) {

                throw new Exception(
                    "La liquidación seleccionada no existe."
                );
            }


            $tipoLiquidacion =
                strtoupper(
                    trim(
                        (string)(
                            $liquidacion['tipo_liquidacion']
                            ?? ''
                        )
                    )
                );


            if (
                $tipoLiquidacion
                !==
                'GASTOS_PROTOCOLARES'
            ) {

                throw new Exception(
                    "La liquidación seleccionada no corresponde a Gastos Protocolares."
                );
            }


            if (
                strtoupper(
                    trim(
                        (string)(
                            $liquidacion['estado']
                            ?? ''
                        )
                    )
                )
                !==
                'BORRADOR'
            ) {

                throw new Exception(
                    "Solo se pueden cargar Gastos Protocolares en una liquidación BORRADOR."
                );
            }


            /*
            |--------------------------------------------------------------------------
            | GUARDAR CARGA
            |--------------------------------------------------------------------------
            */

            if (
                $_SERVER['REQUEST_METHOD']
                ===
                'POST'
            ) {

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
                        "La sesión del formulario no es válida o expiró. "
                        . "Recargue la página e inténtelo nuevamente."
                    );
                }


                $empleadosPost =
                    $_POST['empleados']
                    ?? [];


                if (
                    !is_array(
                        $empleadosPost
                    )
                ) {

                    throw new Exception(
                        "Los datos de empleados recibidos no son válidos."
                    );
                }


                $empleadosDisponibles =
                    $this->modelo
                        ->obtenerEmpleadosParaProtocolar(
                            $liquidacionId
                        );


                $idsValidos = [];


                foreach (
                    $empleadosDisponibles
                    as
                    $empleadoDisponible
                ) {

                    $empleadoIdDisponible =
                        (int)(
                            $empleadoDisponible['empleado_id']
                            ?? 0
                        );


                    if ($empleadoIdDisponible > 0) {

                        $idsValidos[
                            $empleadoIdDisponible
                        ] = true;
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | INICIAR TRANSACCIÓN
                |--------------------------------------------------------------------------
                */

                $this->modelo
                    ->iniciarTransaccion();

                $transaccionIniciada =
                    true;


                foreach (
                    $idsValidos
                    as
                    $empleadoId => $ignorar
                ) {

                    $fila =
                        $empleadosPost[
                            $empleadoId
                        ]
                        ?? [];


                    $incluido =
                        isset(
                            $fila['incluir']
                        )
                        &&
                        (string)$fila['incluir']
                        ===
                        '1';


                    if (!$incluido) {

                        $this->modelo
                            ->eliminarProtocolar(
                                $liquidacionId,
                                $empleadoId
                            );

                        continue;
                    }


                    $importeTexto =
                        trim(
                            (string)(
                                $fila['importe']
                                ?? ''
                            )
                        );


                    $importeTexto =
                        str_replace(
                            ',',
                            '.',
                            $importeTexto
                        );


                    if (
                        $importeTexto === ''
                        ||
                        !is_numeric(
                            $importeTexto
                        )
                    ) {

                        throw new Exception(
                            "Debe ingresar un importe válido para todos los empleados incluidos."
                        );
                    }


                    $importe =
                        round(
                            (float)$importeTexto,
                            2
                        );


                    if ($importe <= 0) {

                        throw new Exception(
                            "El importe de Gastos Protocolares debe ser mayor a cero para todos los empleados incluidos."
                        );
                    }


                    $aplicaPrevision =
                        isset(
                            $fila[
                                'aplica_prevision'
                            ]
                        )
                        &&
                        (string)$fila[
                            'aplica_prevision'
                        ]
                        ===
                        '0'
                            ? 0
                            : 1;


                    $this->modelo
                        ->guardarProtocolar(
                            $liquidacionId,
                            $empleadoId,
                            $importe,
                            $aplicaPrevision
                        );
                }


                if (
                    $this->modelo
                        ->contarCargasProtocolar(
                            $liquidacionId
                        )
                    <=
                    0
                ) {

                    throw new Exception(
                        "Debe incluir al menos un empleado en la liquidación de Gastos Protocolares."
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | CONFIRMAR TRANSACCIÓN
                |--------------------------------------------------------------------------
                */

                $this->modelo
                    ->confirmarTransaccion();

                $transaccionIniciada =
                    false;


                $urlDestino =
                    sigenmuniUrlRuta(
                            'liquidacion/protocolar',
                            [
                                'id' =>
                                    $liquidacionId,

                                'ok' =>
                                    'guardado'
                            ]
                        );


                header(
                    'Location: ' . $urlDestino
                );

                exit();
            }


            /*
            |--------------------------------------------------------------------------
            | MENSAJE
            |--------------------------------------------------------------------------
            */

            if (
                isset($_GET['ok'])
                &&
                $_GET['ok']
                ===
                'guardado'
            ) {

                $mensaje =
                    "Gastos Protocolares guardados correctamente.";

                $tipo_mensaje =
                    "ok";
            }


            /*
            |--------------------------------------------------------------------------
            | DATOS PARA LA VISTA
            |--------------------------------------------------------------------------
            */

            $empleadosProtocolar =
                $this->modelo
                    ->obtenerEmpleadosParaProtocolar(
                        $liquidacionId
                    );


            $cantidadCargados =
                $this->modelo
                    ->contarCargasProtocolar(
                        $liquidacionId
                    );


            require __DIR__
                . '/../vista/liquidacion_protocolar.php';


        } catch (Throwable $e) {

            /*
            |--------------------------------------------------------------------------
            | ROLLBACK
            |--------------------------------------------------------------------------
            */

            if ($transaccionIniciada) {

                try {

                    $this->modelo
                        ->revertirTransaccion();

                } catch (Throwable $rollbackException) {

                    // Se conserva el error principal.
                }

                $transaccionIniciada =
                    false;
            }


            $mensaje =
                $e->getMessage();

            $tipo_mensaje =
                "error";


            try {

                $empleadosProtocolar =
                    isset($liquidacion)
                    &&
                    $liquidacion
                    ?
                    $this->modelo
                        ->obtenerEmpleadosParaProtocolar(
                            $liquidacionId
                        )
                    :
                    [];

                $cantidadCargados =
                    isset($liquidacion)
                    &&
                    $liquidacion
                    ?
                    $this->modelo
                        ->contarCargasProtocolar(
                            $liquidacionId
                        )
                    :
                    0;

            } catch (Throwable $consultaException) {

                $empleadosProtocolar = [];
                $cantidadCargados = 0;
            }


            require __DIR__
                . '/../vista/liquidacion_protocolar.php';
        }
    }


    public function procesar()
    {
        $transaccionIniciada = false;


        try {

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
                throw new Exception(
                    "El procesamiento de una liquidación solo puede realizarse mediante una solicitud POST."
                );
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
                throw new Exception(
                    "La sesión del formulario no es válida o expiró. "
                    . "Recargue la página e inténtelo nuevamente."
                );
            }


            /*
            |--------------------------------------------------------------------------
            | ID DE LIQUIDACIÓN
            |--------------------------------------------------------------------------
            */

            $liquidacionId =
                isset($_POST['id'])
                    ? (int)$_POST['id']
                    : 0;


            if ($liquidacionId <= 0) {
                throw new Exception(
                    "El identificador de la liquidación no es válido."
                );
            }


            $liquidacion =
                $this->modelo->obtenerPorId(
                    $liquidacionId
                );

            if (!$liquidacion) {
                throw new Exception("La liquidación seleccionada no existe.");
            }

            if ($liquidacion['estado'] !== 'BORRADOR') {
                throw new Exception(
                    "Solo se pueden procesar liquidaciones en estado BORRADOR."
                );
            }

            if (!$this->modelo->tipoValido($liquidacion['tipo_liquidacion'])) {
                throw new Exception("El tipo de liquidación no es válido.");
            }

            if (!$this->modelo->fechaValida($liquidacion['fecha_liquidacion'])) {
                throw new Exception("La fecha de liquidación no es válida.");
            }

            $tipoLiquidacion = strtoupper(
                trim((string)$liquidacion['tipo_liquidacion'])
            );


            /*
            |--------------------------------------------------------------------------
            | VALIDAR PERÍODO SEGÚN TIPO
            |--------------------------------------------------------------------------
            |
            | Defensa adicional:
            | aunque una liquidación haya sido creada por otra vía, un AGUINALDO
            | normal solo puede procesarse para junio o diciembre.
            |
            */

            $this->validarPeriodoSegunTipo(
                $tipoLiquidacion,
                $liquidacion['periodo'] ?? ''
            );


            /*
            |--------------------------------------------------------------------------
            | COHERENCIA PERÍODO / FECHA PARA SAC
            |--------------------------------------------------------------------------
            |
            | También se valida al procesar para proteger liquidaciones antiguas
            | o creadas por otra vía.
            |
            */

            $this->validarCoherenciaPeriodoFechaSegunTipo(
                $tipoLiquidacion,
                $liquidacion['periodo'] ?? '',
                $liquidacion['fecha_liquidacion'] ?? ''
            );


            if ($tipoLiquidacion === 'GASTOS_PROTOCOLARES') {

                $empleados =
                    $this->modelo->obtenerCargasProtocolar(
                        $liquidacionId
                    );

                if (empty($empleados)) {

                    throw new Exception(
                        "Debe cargar al menos un empleado en Gastos Protocolares antes de procesar la liquidación."
                    );
                }


            } elseif ($tipoLiquidacion === 'COMPLEMENTARIA') {

                /*
                |--------------------------------------------------------------------------
                | COMPLEMENTARIA DE HABERES
                |--------------------------------------------------------------------------
                |
                | Solo se procesan los empleados seleccionados.
                |
                */

                $empleados =
                    $this->modelo
                        ->obtenerParticipantesParaLiquidar(
                            $liquidacionId
                        );


                if (empty($empleados)) {

                    throw new Exception(
                        "Debe seleccionar al menos un empleado en Personal y Novedades antes de procesar la COMPLEMENTARIA."
                    );
                }


            } elseif ($tipoLiquidacion === 'COMPLEMENTARIA_SAC') {

                /*
                |--------------------------------------------------------------------------
                | COMPLEMENTARIA DE SAC
                |--------------------------------------------------------------------------
                |
                | Solo se procesan participantes y se vuelve a validar el saldo
                | SAC pendiente antes de calcular.
                |
                */

                $participantes =
                    $this->modelo
                        ->obtenerParticipantesParaLiquidar(
                            $liquidacionId
                        );


                if (empty($participantes)) {

                    throw new Exception(
                        "Debe seleccionar al menos un empleado en Personal y Días SAC antes de procesar la COMPLEMENTARIA DE SAC."
                    );
                }


                $estadoSacActual =
                    $this->modelo
                        ->obtenerEmpleadosParaSac(
                            $liquidacionId
                        );


                $sacPorEmpleado = [];


                foreach (
                    $estadoSacActual
                    as
                    $empleadoSac
                ) {

                    $idSac =
                        (int)(
                            $empleadoSac['id']
                            ?? $empleadoSac['empleado_id']
                            ?? 0
                        );


                    if ($idSac > 0) {

                        $sacPorEmpleado[
                            $idSac
                        ] = $empleadoSac;
                    }
                }


                $empleados = [];


                foreach (
                    $participantes
                    as
                    $participante
                ) {

                    $empleadoIdParticipante =
                        (int)(
                            $participante['id']
                            ?? $participante['empleado_id']
                            ?? 0
                        );


                    if (
                        $empleadoIdParticipante <= 0
                        ||
                        !isset(
                            $sacPorEmpleado[
                                $empleadoIdParticipante
                            ]
                        )
                    ) {

                        continue;
                    }


                    $estadoEmpleadoSac =
                        $sacPorEmpleado[
                            $empleadoIdParticipante
                        ];


                    $diasPendientes =
                        (int)(
                            $estadoEmpleadoSac[
                                'dias_sac_pendientes'
                            ]
                            ?? 0
                        );


                    $diasSac =
                        (int)(
                            $participante['dias_sac']
                            ?? 0
                        );


                    if ($diasPendientes <= 0) {

                        throw new Exception(
                            "El empleado ID "
                            . $empleadoIdParticipante
                            . " ya tiene totalmente liquidado el SAC devengado."
                        );
                    }


                    if (
                        $diasSac < 1
                        ||
                        $diasSac > $diasPendientes
                    ) {

                        throw new Exception(
                            "El empleado ID "
                            . $empleadoIdParticipante
                            . " tiene "
                            . $diasPendientes
                            . " días SAC pendientes y no puede liquidar "
                            . $diasSac
                            . " días."
                        );
                    }


                    $participante['dias_sac'] =
                        $diasSac;

                    $participante['dias_sac_devengados'] =
                        (int)(
                            $estadoEmpleadoSac[
                                'dias_sac_devengados'
                            ]
                            ?? 0
                        );

                    $participante['dias_sac_pagados'] =
                        (int)(
                            $estadoEmpleadoSac[
                                'dias_sac_pagados'
                            ]
                            ?? 0
                        );

                    $participante['dias_sac_pendientes'] =
                        $diasPendientes;


                    $empleados[] =
                        $participante;
                }


                if (empty($empleados)) {

                    throw new Exception(
                        "No hay participantes con días SAC pendientes para procesar."
                    );
                }


            } elseif ($tipoLiquidacion === 'AGUINALDO') {

                /*
                |--------------------------------------------------------------------------
                | AGUINALDO GENERAL
                |--------------------------------------------------------------------------
                |
                | Se muestran todos los empleados del semestre, pero al procesar
                | se excluyen automáticamente quienes ya tienen 0 días pendientes.
                |
                */

                $empleadosSac =
                    $this->modelo
                        ->obtenerEmpleadosParaSac(
                            $liquidacionId
                        );


                $empleados = [];


                foreach (
                    $empleadosSac
                    as
                    $empleadoSac
                ) {

                    $diasPendientes =
                        (int)(
                            $empleadoSac[
                                'dias_sac_pendientes'
                            ]
                            ?? 0
                        );


                    $diasSac =
                        (int)(
                            $empleadoSac['dias_sac']
                            ?? $diasPendientes
                        );


                    if (
                        $diasPendientes <= 0
                        ||
                        $diasSac <= 0
                    ) {

                        continue;
                    }


                    if ($diasSac > $diasPendientes) {

                        throw new Exception(
                            "Un empleado tiene más días SAC cargados que días pendientes."
                        );
                    }


                    $empleados[] =
                        $empleadoSac;
                }


                if (empty($empleados)) {

                    throw new Exception(
                        "No hay empleados con días SAC pendientes para procesar."
                    );
                }


            } elseif ($tipoLiquidacion === 'MENSUAL') {

                /*
                |--------------------------------------------------------------------------
                | LIQUIDACIÓN MENSUAL
                |--------------------------------------------------------------------------
                |
                | La elegibilidad se determina mediante empleado_periodo_laboral.
                | El estado actual del empleado no decide su participación.
                |
                */

                $empleados =
                    $this->modelo
                        ->obtenerEmpleadosParaMensual(
                            $liquidacionId
                        );


                if (empty($empleados)) {

                    throw new Exception(
                        "No hay empleados con relación laboral en el período para procesar la liquidación MENSUAL."
                    );
                }


            } else {

                throw new Exception(
                    "El tipo de liquidación seleccionado no tiene un flujo de procesamiento implementado."
                );
            }


            $conceptos = $this->modelo->obtenerConceptosActivosPorCodigo();

            if (empty($conceptos)) {
                throw new Exception(
                    "No existen conceptos activos para procesar la liquidación."
                );
            }

            /*
            |--------------------------------------------------------------------------
            | CONCEPTOS OBLIGATORIOS SEGÚN TIPO
            |--------------------------------------------------------------------------
            |
            | GASTOS_PROTOCOLARES tiene una lógica independiente:
            |
            | 113 = Gastos Protocolares
            | 301 = Caja de Previsión Social 11%
            | 401 = Aporte Patronal Caja de Previsión Social 16%
            |
            | Para jubilados / exentos, 301 y 401 no se aplicarán.
            |
            |--------------------------------------------------------------------------
            */

            if ($tipoLiquidacion === 'GASTOS_PROTOCOLARES') {

                $conceptosObligatorios = [
                    '113',
                    '301',
                    '401'
                ];

            } elseif (
                $tipoLiquidacion === 'AGUINALDO'
                ||
                $tipoLiquidacion === 'COMPLEMENTARIA_SAC'
            ) {

                /*
                |--------------------------------------------------------------------------
                | CONCEPTOS OBLIGATORIOS DEL SAC
                |--------------------------------------------------------------------------
                |
                | El SAC utiliza:
                |
                | 150 - Sueldo Anual Complementario
                |
                | Descuentos automáticos:
                | 301 - Caja 11%
                | 306 - IPS 1%
                | 307 - IPS 2%
                |
                | Aportes patronales:
                | 401 - Caja Patronal 16%
                | 403 - IPS Patronal 2%
                |
                | 402 NO corresponde en SAC.
                |
                | Los conceptos remunerativos que integran la base se validan
                | normalmente desde la configuración activa.
                |
                */

                $conceptosObligatorios = [
                    '101',
                    '102',
                    '104',
                    '108',
                    '109',
                    '150',
                    '301',
                    '306',
                    '307',
                    '401',
                    '403'
                ];

            } else {

                $conceptosObligatorios = [
                    '101', '102', '103', '104', '108', '109',
                    '301', '302', '303', '304', '306', '307',
                    '401', '402', '403'
                ];
            }

            foreach ($conceptosObligatorios as $codigo) {
                if (!isset($conceptos[$codigo])) {
                    throw new Exception(
                        "No se puede procesar la liquidación porque falta o está inactivo el concepto "
                        . $codigo
                        . "."
                    );
                }
            }

            $calculadora = new CalculadoraLiquidacion($this->modelo);

            $this->modelo->iniciarTransaccion();
            $transaccionIniciada = true;

            $this->modelo->limpiarProcesamiento($liquidacionId);

            foreach ($empleados as $empleado) {
                $empleadoId =
                    $tipoLiquidacion === 'GASTOS_PROTOCOLARES'
                        ? (int)($empleado['empleado_id'] ?? 0)
                        : (int)($empleado['id'] ?? 0);

                if ($empleadoId <= 0) {
                    throw new Exception(
                        "Se encontró un empleado con ID inválido durante el procesamiento."
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | NOVEDADES DEL PERÍODO
                |--------------------------------------------------------------------------
                */

                if (
                    $tipoLiquidacion === 'MENSUAL'
                    ||
                    $tipoLiquidacion === 'COMPLEMENTARIA'
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | DÍAS MENSUALES / PRESENTISMO
                    |--------------------------------------------------------------------------
                    */

                    $diasLiquidados =
                        (int)(
                            $empleado['dias_liquidados']
                            ?? 30
                        );


                    if (
                        $diasLiquidados < 0
                        ||
                        $diasLiquidados > 30
                    ) {

                        throw new Exception(
                            "Los días liquidados del empleado ID "
                            . $empleadoId
                            . " deben estar entre 0 y 30."
                        );
                    }


                    $empleado['dias_liquidados'] =
                        $diasLiquidados;


                    $empleado['aplica_presentismo'] =
                        (int)(
                            $empleado['aplica_presentismo']
                            ?? 1
                        )
                        ===
                        1
                            ? 1
                            : 0;


                    if ($diasLiquidados === 0) {

                        $empleado['aplica_presentismo'] =
                            0;
                    }


                    $empleado['observacion_novedad'] =
                        trim(
                            (string)(
                                $empleado['observacion_novedad']
                                ?? ''
                            )
                        );


                } elseif (
                    $tipoLiquidacion === 'AGUINALDO'
                    ||
                    $tipoLiquidacion === 'COMPLEMENTARIA_SAC'
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | DÍAS DE SAC
                    |--------------------------------------------------------------------------
                    |
                    | 180 = SAC completo.
                    | 0-179 = SAC proporcional.
                    |
                    */

                    $diasSac =
                        (int)(
                            $empleado['dias_sac']
                            ?? 180
                        );


                    if (
                        $diasSac < 0
                        ||
                        $diasSac > 180
                    ) {

                        throw new Exception(
                            "Los días de SAC del empleado ID "
                            . $empleadoId
                            . " deben estar entre 0 y 180."
                        );
                    }


                    $empleado['dias_sac'] =
                        $diasSac;


                    /*
                    |--------------------------------------------------------------------------
                    | EL SAC NO UTILIZA LAS NOVEDADES MENSUALES
                    |--------------------------------------------------------------------------
                    */

                    $empleado['dias_liquidados'] =
                        30;

                    $empleado['aplica_presentismo'] =
                        1;

                    $empleado['observacion_novedad'] =
                        '';
                }


                $resultado = $calculadora->calcularEmpleado(
                    $empleado,
                    $liquidacion,
                    $conceptos
                );

                if (!is_array($resultado)) {
                    throw new Exception(
                        "La calculadora devolvió un resultado inválido para el empleado ID "
                        . $empleadoId
                        . "."
                    );
                }

                if (
                    !isset(
                        $resultado['detalle'],
                        $resultado['total_remunerativo'],
                        $resultado['total_descuentos'],
                        $resultado['total_no_remunerativo'],
                        $resultado['total_asignaciones'],
                        $resultado['neto']
                    )
                ) {
                    throw new Exception(
                        "El cálculo del empleado ID "
                        . $empleadoId
                        . " está incompleto."
                    );
                }

                foreach ($resultado['detalle'] as $item) {
                    $codigo = (string)($item['codigo'] ?? '');

                    if ($codigo === '' || !isset($conceptos[$codigo])) {
                        throw new Exception(
                            "El concepto "
                            . ($codigo !== '' ? $codigo : '(sin código)')
                            . " generado para el empleado ID "
                            . $empleadoId
                            . " no existe o está inactivo."
                        );
                    }

                    $conceptoId = (int)$conceptos[$codigo]['id'];

                    if ($conceptoId <= 0) {
                        throw new Exception(
                            "El concepto "
                            . $codigo
                            . " tiene un ID inválido."
                        );
                    }

                    $cantidad = (float)($item['cantidad'] ?? 1);

                    if ($cantidad <= 0) {
                        $cantidad = 1;
                    }

                    $porcentaje = (float)($item['porcentaje'] ?? 0);
                    $monto = round((float)($item['monto'] ?? 0), 2);
                    $esManual = (int)($item['es_manual'] ?? 0);

                    $observacion = trim(
                        (string)($item['observacion'] ?? '')
                    );

                    if ($monto <= 0) {
                        continue;
                    }

                    $this->modelo->guardarDetalle(
                        $liquidacionId,
                        $empleadoId,
                        $conceptoId,
                        $cantidad,
                        $porcentaje,
                        $monto,
                        $esManual,
                        $observacion
                    );
                }

                $totalRemunerativo = round(
                    (float)$resultado['total_remunerativo'],
                    2
                );

                $totalDescuentos = round(
                    (float)$resultado['total_descuentos'],
                    2
                );

                $totalNoRemunerativo = round(
                    (float)$resultado['total_no_remunerativo'],
                    2
                );

                $totalAsignaciones = round(
                    (float)$resultado['total_asignaciones'],
                    2
                );

                $neto = round((float)$resultado['neto'], 2);

                $netoEsperado = round(
                    $totalRemunerativo
                    - $totalDescuentos
                    + $totalNoRemunerativo
                    + $totalAsignaciones,
                    2
                );

                if (abs($neto - $netoEsperado) > 0.01) {
                    throw new Exception(
                        "Se detectó una diferencia en el cálculo del neto para el empleado ID "
                        . $empleadoId
                        . "."
                    );
                }

                $aplicaPrevision =
                    isset($resultado['aplica_prevision'])
                        ? (
                            (int)$resultado['aplica_prevision'] === 1
                                ? 1
                                : 0
                        )
                        : 1;


                $this->modelo->guardarResumenEmpleado(
                    $liquidacionId,
                    $empleadoId,
                    $totalRemunerativo,
                    $totalDescuentos,
                    $totalNoRemunerativo,
                    $totalAsignaciones,
                    $neto,
                    $aplicaPrevision
                );
            }

            $this->modelo->cerrar($liquidacionId);
            $this->modelo->confirmarTransaccion();

            $transaccionIniciada = false;

            $urlDestino =
                sigenmuniUrlRuta(
                        'liquidacion',
                        [
                            'ok' => 'procesada'
                        ]
                    );


            header(
                'Location: ' . $urlDestino
            );

            exit();

        } catch (Throwable $e) {
            if ($transaccionIniciada) {
                try {
                    $this->modelo->revertirTransaccion();
                } catch (Throwable $rollbackException) {
                    // Se conserva el error principal.
                }

                $transaccionIniciada = false;
            }

            $mensaje =
                $e->getMessage();

            $tipo_mensaje =
                "error";


            /*
            |--------------------------------------------------------------------------
            | ERROR DESDE ROUTER
            |--------------------------------------------------------------------------
            */

            $_SESSION['liquidacion_flash'] = [
                'mensaje' =>
                    $mensaje,

                'tipo' =>
                    'error'
            ];


            header(
                'Location: '
                . sigenmuniUrlRuta(
                    'liquidacion'
                )
            );

            exit();
        }
    }

    public function ver()
    {
        $liquidacionId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        $empleadoDetalleId = isset($_GET['empleado_id'])
            ? (int)$_GET['empleado_id']
            : 0;

        if ($liquidacionId <= 0) {

            $urlDestino =
                sigenmuniUrlRuta(
                        'liquidacion'
                    );


            header(
                'Location: ' . $urlDestino
            );

            exit();
        }

        try {
            $liquidacion = $this->modelo->obtenerPorId($liquidacionId);

            if (!$liquidacion) {

                $urlDestino =
                    sigenmuniUrlRuta(
                            'liquidacion'
                        );


                header(
                    'Location: ' . $urlDestino
                );

                exit();
            }

            $resumenEmpleados = $this->modelo->obtenerResumenEmpleados(
                $liquidacionId
            );

            $totales = $this->modelo->obtenerTotales($liquidacionId);

            $empleadoSeleccionado = null;
            $detalleConceptos = [];

            if ($empleadoDetalleId > 0) {
                $empleadoSeleccionado =
                    $this->modelo->obtenerEmpleadoLiquidacion(
                        $liquidacionId,
                        $empleadoDetalleId
                    );

                if (!$empleadoSeleccionado) {

                    $urlDestino =
                        sigenmuniUrlRuta(
                                'liquidacion/ver',
                                [
                                    'id' =>
                                        $liquidacionId
                                ]
                            );


                    header(
                        'Location: ' . $urlDestino
                    );

                    exit();
                }

                $detalleConceptos =
                    $this->modelo->obtenerDetalleEmpleado(
                        $liquidacionId,
                        $empleadoDetalleId
                    );
            }

            require __DIR__ . '/../vista/liquidacion_ver.php';

        } catch (Exception $e) {
            die(
                "Error al consultar la liquidación: "
                . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8')
            );
        }
    }

    public function estado()
    {
        /*
        |--------------------------------------------------------------------------
        | FUNCIÓN LOCAL DE REDIRECCIÓN
        |--------------------------------------------------------------------------
        |
        | Centraliza el regreso al listado de Liquidaciones mediante el Router.
        |
        |--------------------------------------------------------------------------
        */

        $redirigir =
            function (
                $mensaje = ''
            ) {

                $parametros = [];


                if ($mensaje !== '') {

                    $parametros[
                        'msg'
                    ] = $mensaje;
                }


                $urlDestino =
                    sigenmuniUrlRuta(
                            'liquidacion',
                            $parametros
                        );


                header(
                    'Location: ' . $urlDestino
                );

                exit();
            };


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

            $redirigir(
                'accion_invalida'
            );
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

            $redirigir(
                'accion_invalida'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | PARÁMETROS
        |--------------------------------------------------------------------------
        */

        $liquidacionId =
            isset($_POST['id'])
                ? (int)$_POST['id']
                : 0;


        $accion =
            strtolower(
                trim(
                    (string)(
                        $_POST['accion']
                        ?? ''
                    )
                )
            );


        if (
            $liquidacionId <= 0
            ||
            $accion === ''
        ) {

            $redirigir(
                'parametros_invalidos'
            );
        }


        if (
            $accion !== 'anular'
            &&
            $accion !== 'deshacer'
        ) {

            $redirigir(
                'accion_invalida'
            );
        }


        try {

            $liquidacion =
                $this->modelo->obtenerPorId(
                    $liquidacionId
                );


            if (!$liquidacion) {

                $redirigir(
                    'no_existe'
                );
            }


            $estadoActual =
                strtoupper(
                    trim(
                        (string)(
                            $liquidacion['estado']
                            ?? ''
                        )
                    )
                );


            /*
            |--------------------------------------------------------------------------
            | DESHACER PROCESAMIENTO
            |--------------------------------------------------------------------------
            |
            | IMPORTANTE:
            |
            | Esto NO intenta ejecutar ROLLBACK sobre un COMMIT anterior.
            |
            | Se inicia una NUEVA transacción que:
            |
            | 1. elimina liquidacion_detalle;
            | 2. elimina liquidacion_empleado;
            | 3. vuelve la cabecera a BORRADOR.
            |
            | Si cualquiera de esas operaciones falla, se ejecuta ROLLBACK y
            | la liquidación permanece exactamente como estaba.
            |
            |--------------------------------------------------------------------------
            */

            if ($accion === 'deshacer') {

                $esAdminUsuario =
                    (
                        (int)(
                            $_SESSION['es_admin']
                            ?? 0
                        ) === 1
                        ||
                        strtoupper(
                            trim(
                                (string)(
                                    $_SESSION['rol']
                                    ?? ''
                                )
                            )
                        ) === 'ADMIN'
                    );


                if (!$esAdminUsuario) {

                    $redirigir(
                        'sin_permiso_deshacer'
                    );
                }


                if (
                    $estadoActual
                    !==
                    'CERRADA'
                ) {

                    $redirigir(
                        'deshacer_no_permitido'
                    );
                }


                $transaccionIniciada =
                    false;


                try {

                    /*
                    |--------------------------------------------------------------------------
                    | BEGIN
                    |--------------------------------------------------------------------------
                    */

                    $this->modelo
                        ->iniciarTransaccion();


                    $transaccionIniciada =
                        true;


                    /*
                    |--------------------------------------------------------------------------
                    | ELIMINAR RESULTADOS GENERADOS
                    |--------------------------------------------------------------------------
                    |
                    | No se elimina la cabecera de liquidación.
                    |
                    | En Gastos Protocolares tampoco se eliminan las cargas
                    | originales de liquidacion_protocolar, para que puedan
                    | revisarse y procesarse nuevamente.
                    |
                    |--------------------------------------------------------------------------
                    */

                    $this->modelo
                        ->limpiarProcesamiento(
                            $liquidacionId
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | VOLVER A BORRADOR
                    |--------------------------------------------------------------------------
                    */

                    $this->modelo
                        ->cambiarEstado(
                            $liquidacionId,
                            'BORRADOR'
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | COMMIT
                    |--------------------------------------------------------------------------
                    */

                    $this->modelo
                        ->confirmarTransaccion();


                    $transaccionIniciada =
                        false;


                } catch (Throwable $e) {

                    /*
                    |--------------------------------------------------------------------------
                    | ROLLBACK
                    |--------------------------------------------------------------------------
                    */

                    if ($transaccionIniciada) {

                        try {

                            $this->modelo
                                ->revertirTransaccion();

                        } catch (
                            Throwable
                            $rollbackException
                        ) {

                            // Se conserva el error original.
                        }


                        $transaccionIniciada =
                            false;
                    }


                    $redirigir(
                        'error_deshacer'
                    );
                }


                $redirigir(
                    'deshecha'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | ANULAR
            |--------------------------------------------------------------------------
            */

            if (
                $estadoActual
                ===
                'ANULADA'
            ) {

                $redirigir(
                    'ya_anulada'
                );
            }


            if (
                $estadoActual !== 'BORRADOR'
                &&
                $estadoActual !== 'CERRADA'
            ) {

                $redirigir(
                    'accion_invalida'
                );
            }


            $actualizada =
                $this->modelo->anular(
                    $liquidacionId
                );


            if (!$actualizada) {

                throw new Exception(
                    "No se pudo anular la liquidación."
                );
            }


            $redirigir(
                'anulada'
            );


        } catch (Throwable $e) {

            $redirigir(
                'error_estado'
            );
        }
    }


    public function recibo()
    {
        $liquidacionId = isset($_GET['liquidacion_id'])
            ? (int)$_GET['liquidacion_id']
            : 0;

        $empleadoId = isset($_GET['empleado_id'])
            ? (int)$_GET['empleado_id']
            : 0;

        if ($liquidacionId <= 0 || $empleadoId <= 0) {
            die("Parámetros inválidos.");
        }

        try {
            $recibo = $this->prepararRecibo(
                $liquidacionId,
                $empleadoId
            );

            $datos = $recibo['datos'];

            $haberesRem = $recibo['haberesRem'];
            $haberesNoRem = $recibo['haberesNoRem'];
            $asignaciones = $recibo['asignaciones'];
            $descuentos = $recibo['descuentos'];
            $aportesPatronales = $recibo['aportesPatronales'];

            $totalHaberesRem = $recibo['totalHaberesRem'];
            $totalHaberesNoRem = $recibo['totalHaberesNoRem'];
            $totalAsignaciones = $recibo['totalAsignaciones'];
            $totalDescuentos = $recibo['totalDescuentos'];
            $totalPatronales = $recibo['totalPatronales'];

            $neto = $recibo['neto'];
            $antiguedadTexto = $recibo['antiguedadTexto'];

            $esBorrador = $recibo['esBorrador'];
            $esCerrada = $recibo['esCerrada'];
            $esAnulada = $recibo['esAnulada'];

            $mensajeRecibo = $_SESSION['mensaje_recibo'] ?? '';
            $tipoMensajeRecibo = $_SESSION['tipo_mensaje_recibo'] ?? '';

            unset(
                $_SESSION['mensaje_recibo'],
                $_SESSION['tipo_mensaje_recibo']
            );

            require __DIR__ . '/../vista/recibo_sueldo.php';

        } catch (Exception $e) {
            die(
                "Error al cargar el recibo de sueldo: "
                . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8')
            );
        }
    }

    public function recibosLiquidacion()
    {
        $liquidacionId = isset($_GET['liquidacion_id'])
            ? (int)$_GET['liquidacion_id']
            : 0;

        if ($liquidacionId <= 0) {
            die("ID de liquidación inválido.");
        }

        try {
            $liquidacion = $this->modelo->obtenerPorId($liquidacionId);

            if (!$liquidacion) {
                throw new Exception(
                    "La liquidación solicitada no existe."
                );
            }

            $estadoLiquidacion = strtoupper(
                trim((string)($liquidacion['estado'] ?? ''))
            );

            if ($estadoLiquidacion === 'BORRADOR') {
                throw new Exception(
                    "No se pueden imprimir todos los recibos de una liquidación en estado BORRADOR. Primero debe procesarse."
                );
            }

            $resumenEmpleados =
                $this->modelo->obtenerResumenEmpleados(
                    $liquidacionId
                );

            if (empty($resumenEmpleados)) {
                throw new Exception(
                    "La liquidación no tiene empleados procesados."
                );
            }

            $recibos = [];

            foreach ($resumenEmpleados as $empleadoResumen) {
                $empleadoId = (int)(
                    $empleadoResumen['empleado_id']
                    ?? 0
                );

                if ($empleadoId <= 0) {
                    continue;
                }

                $recibo = $this->prepararRecibo(
                    $liquidacionId,
                    $empleadoId
                );

                $recibo['resumenEmpleado'] =
                    $empleadoResumen;

                $recibos[] =
                    $recibo;
            }

            if (empty($recibos)) {
                throw new Exception(
                    "No se pudieron preparar los recibos de la liquidación."
                );
            }

            $esCerrada = $estadoLiquidacion === 'CERRADA';
            $esAnulada = $estadoLiquidacion === 'ANULADA';

            require __DIR__
                . '/../vista/recibos_liquidacion_pdf.php';

        } catch (Exception $e) {
            die(
                "Error al cargar los recibos de la liquidación: "
                . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8')
            );
        }
    }

    public function enviarRecibo()
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

            $_SESSION['liquidacion_flash'] = [
                'mensaje' =>
                    'El envío del recibo solo puede realizarse mediante una solicitud POST.',

                'tipo' =>
                    'error'
            ];


            header(
                'Location: '
                . sigenmuniUrlRuta(
                    'liquidacion'
                )
            );

            exit();
        }


        /*
        |--------------------------------------------------------------------------
        | PARÁMETROS
        |--------------------------------------------------------------------------
        */

        $liquidacionId =
            isset($_POST['liquidacion_id'])
                ? (int)$_POST['liquidacion_id']
                : 0;


        $empleadoId =
            isset($_POST['empleado_id'])
                ? (int)$_POST['empleado_id']
                : 0;


        $origen =
            trim(
                (string)(
                    $_POST['origen']
                    ?? ''
                )
            );


        if ($origen !== 'historial') {

            $origen = '';
        }


        /*
        |--------------------------------------------------------------------------
        | REDIRECCIÓN AL RECIBO
        |--------------------------------------------------------------------------
        */

        $redirigirRecibo =
            function () use (
                $liquidacionId,
                $empleadoId,
                $origen
            ) {

                $parametros = [
                    'liquidacion_id' =>
                        $liquidacionId,

                    'empleado_id' =>
                        $empleadoId
                ];


                if ($origen === 'historial') {

                    $parametros[
                        'origen'
                    ] = 'historial';
                }


                $urlDestino =
                    sigenmuniUrlRuta(
                            'liquidacion/recibo',
                            $parametros
                        );


                header(
                    'Location: ' . $urlDestino
                );

                exit();
            };


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

            $_SESSION['mensaje_recibo'] =
                "La sesión del formulario no es válida o expiró. "
                . "Recargue el recibo e inténtelo nuevamente.";


            $_SESSION['tipo_mensaje_recibo'] =
                "error";


            if (
                $liquidacionId > 0
                &&
                $empleadoId > 0
            ) {

                $redirigirRecibo();
            }


            $_SESSION['liquidacion_flash'] = [
                'mensaje' =>
                    $_SESSION['mensaje_recibo'],

                'tipo' =>
                    'error'
            ];


            header(
                'Location: '
                . sigenmuniUrlRuta(
                    'liquidacion'
                )
            );

            exit();
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDAR IDENTIFICADORES
        |--------------------------------------------------------------------------
        */

        if (
            $liquidacionId <= 0
            ||
            $empleadoId <= 0
        ) {

            $_SESSION['liquidacion_flash'] = [
                'mensaje' =>
                    "Los parámetros del recibo no son válidos.",

                'tipo' =>
                    'error'
            ];


            $urlDestino =
                sigenmuniUrlRuta(
                        'liquidacion'
                    );


            header(
                'Location: ' . $urlDestino
            );

            exit();
        }


        $rutaPDF =
            null;


        try {

            $recibo =
                $this->prepararRecibo(
                    $liquidacionId,
                    $empleadoId
                );


            $datos =
                $recibo['datos'];


            /*
            |--------------------------------------------------------------------------
            | SOLO LIQUIDACIONES CERRADAS
            |--------------------------------------------------------------------------
            */

            if (
                strtoupper(
                    trim(
                        (string)$datos['estado']
                    )
                )
                !==
                'CERRADA'
            ) {

                throw new Exception(
                    "Solo se pueden enviar por email recibos de liquidaciones CERRADAS."
                );
            }


            /*
            |--------------------------------------------------------------------------
            | EMAIL DEL EMPLEADO
            |--------------------------------------------------------------------------
            */

            $emailEmpleado =
                trim(
                    (string)(
                        $datos['email']
                        ?? ''
                    )
                );


            if ($emailEmpleado === '') {

                throw new Exception(
                    "El empleado no tiene un correo electrónico cargado."
                );
            }


            if (
                !filter_var(
                    $emailEmpleado,
                    FILTER_VALIDATE_EMAIL
                )
            ) {

                throw new Exception(
                    "El correo electrónico del empleado no es válido."
                );
            }


            /*
            |--------------------------------------------------------------------------
            | GENERAR PDF TEMPORAL
            |--------------------------------------------------------------------------
            */

            $archivoPDF =
                GeneradorReciboPDF::generar(
                    $datos,
                    $recibo['haberesRem'],
                    $recibo['haberesNoRem'],
                    $recibo['asignaciones'],
                    $recibo['descuentos'],
                    $recibo['aportesPatronales'],
                    $recibo['totalHaberesRem'],
                    $recibo['totalHaberesNoRem'],
                    $recibo['totalAsignaciones'],
                    $recibo['totalDescuentos'],
                    $recibo['totalPatronales'],
                    $recibo['neto'],
                    $recibo['antiguedadTexto']
                );


            if (
                empty(
                    $archivoPDF['ruta']
                )
                ||
                !file_exists(
                    $archivoPDF['ruta']
                )
            ) {

                throw new Exception(
                    "No se pudo generar el PDF del recibo."
                );
            }


            $rutaPDF =
                $archivoPDF['ruta'];


            $nombreArchivo =
                $archivoPDF['nombre']
                ?? basename(
                    $rutaPDF
                );


            /*
            |--------------------------------------------------------------------------
            | CONFIGURACIÓN DE CORREO
            |--------------------------------------------------------------------------
            */

            $configCorreo =
                $this->obtenerConfiguracionCorreo();


            $mail =
                new PHPMailer(
                    true
                );


            $mail->isSMTP();

            $mail->Host =
                $configCorreo['host'];

            $mail->SMTPAuth =
                true;

            $mail->Username =
                $configCorreo['usuario'];

            $mail->Password =
                $configCorreo['clave'];

            $mail->SMTPSecure =
                PHPMailer::ENCRYPTION_STARTTLS;

            $mail->Port =
                $configCorreo['puerto'];

            $mail->CharSet =
                'UTF-8';


            $mail->setFrom(
                $configCorreo[
                    'remitente_email'
                ],
                $configCorreo[
                    'remitente_nombre'
                ]
            );


            $nombreEmpleado =
                trim(
                    (string)(
                        $datos['nombre']
                        ?? ''
                    )
                    . ' '
                    . (string)(
                        $datos['apellido']
                        ?? ''
                    )
                );


            $mail->addAddress(
                $emailEmpleado,
                $nombreEmpleado
            );


            $mail->isHTML(
                true
            );


            /*
            |--------------------------------------------------------------------------
            | MENSAJE
            |--------------------------------------------------------------------------
            */

            $periodoSeguro =
                htmlspecialchars(
                    (string)$datos['periodo'],
                    ENT_QUOTES,
                    'UTF-8'
                );


            $nombreSeguro =
                htmlspecialchars(
                    (string)$datos['nombre'],
                    ENT_QUOTES,
                    'UTF-8'
                );


            $apellidoSeguro =
                htmlspecialchars(
                    (string)$datos['apellido'],
                    ENT_QUOTES,
                    'UTF-8'
                );


            $mail->Subject =
                'Recibo de Sueldo - '
                . $datos['periodo'];


            $mail->Body =
                "
                <p>
                    Estimado/a
                    <strong>{$nombreSeguro} {$apellidoSeguro}</strong>:
                </p>
                <p>
                    Se adjunta su recibo de sueldo correspondiente
                    al período <strong>{$periodoSeguro}</strong>.
                </p>
                <p>Saludos cordiales.</p>
                <p><strong>Municipalidad de Fortín Lugones</strong></p>
                ";


            $mail->AltBody =
                "Se adjunta su recibo de sueldo correspondiente al período "
                . $datos['periodo']
                . ".";


            $mail->addAttachment(
                $rutaPDF,
                $nombreArchivo
            );


            /*
            |--------------------------------------------------------------------------
            | ENVIAR
            |--------------------------------------------------------------------------
            */

            $mail->send();


            /*
            |--------------------------------------------------------------------------
            | ELIMINAR PDF TEMPORAL
            |--------------------------------------------------------------------------
            */

            if (
                $rutaPDF
                &&
                file_exists(
                    $rutaPDF
                )
            ) {

                unlink(
                    $rutaPDF
                );
            }


            $rutaPDF =
                null;


            /*
            |--------------------------------------------------------------------------
            | MENSAJE DE ÉXITO
            |--------------------------------------------------------------------------
            */

            $_SESSION['mensaje_recibo'] =
                "Recibo enviado correctamente al correo del empleado.";


            $_SESSION['tipo_mensaje_recibo'] =
                "ok";


            $redirigirRecibo();


        } catch (Throwable $e) {

            /*
            |--------------------------------------------------------------------------
            | LIMPIAR PDF TEMPORAL SI EXISTE
            |--------------------------------------------------------------------------
            */

            if (
                $rutaPDF
                &&
                file_exists(
                    $rutaPDF
                )
            ) {

                unlink(
                    $rutaPDF
                );
            }


            $_SESSION['mensaje_recibo'] =
                $e->getMessage();


            $_SESSION['tipo_mensaje_recibo'] =
                "error";


            $redirigirRecibo();
        }
    }


    private function prepararRecibo(
        $liquidacionId,
        $empleadoId
    ) {
        $datos = $this->modelo->obtenerDatosRecibo(
            $liquidacionId,
            $empleadoId
        );

        if (!$datos) {
            throw new Exception(
                "No se encontró el recibo solicitado."
            );
        }

        $detalle = $this->modelo->obtenerDetalleRecibo(
            $liquidacionId,
            $empleadoId
        );

        $haberesRem = [];
        $haberesNoRem = [];
        $asignaciones = [];
        $descuentos = [];
        $aportesPatronales = [];

        $totalHaberesRem = 0;
        $totalHaberesNoRem = 0;
        $totalAsignaciones = 0;
        $totalDescuentos = 0;
        $totalPatronales = 0;

        foreach ($detalle as $item) {
            $categoria = strtoupper(
                trim((string)($item['categoria'] ?? ''))
            );

            $monto = round(
                (float)($item['monto'] ?? 0),
                2
            );

            switch ($categoria) {
                case 'REMUNERATIVO':
                    $haberesRem[] = $item;
                    $totalHaberesRem += $monto;
                    break;

                case 'NO_REMUNERATIVO':
                    $haberesNoRem[] = $item;
                    $totalHaberesNoRem += $monto;
                    break;

                case 'ASIGNACION':
                case 'ASIGNACION_FAMILIAR':
                    $asignaciones[] = $item;
                    $totalAsignaciones += $monto;
                    break;

                case 'DESCUENTO':
                    $descuentos[] = $item;
                    $totalDescuentos += $monto;
                    break;

                case 'APORTE_PATRONAL':
                    $aportesPatronales[] = $item;
                    $totalPatronales += $monto;
                    break;
            }
        }

        $totalHaberesRem = round($totalHaberesRem, 2);
        $totalHaberesNoRem = round($totalHaberesNoRem, 2);
        $totalAsignaciones = round($totalAsignaciones, 2);
        $totalDescuentos = round($totalDescuentos, 2);
        $totalPatronales = round($totalPatronales, 2);

        $netoCalculado = round(
            $totalHaberesRem
            - $totalDescuentos
            + $totalHaberesNoRem
            + $totalAsignaciones,
            2
        );

        $neto = round(
            (float)($datos['neto'] ?? $netoCalculado),
            2
        );

        /*
        |--------------------------------------------------------------------------
        | ANTIGÜEDAD EFECTIVA DEL RECIBO
        |--------------------------------------------------------------------------
        |
        | Debe coincidir exactamente con la antigüedad utilizada para calcular
        | el concepto 108.
        |
        */

        $aniosAntiguedad =
            $this->modelo
                ->calcularAniosAntiguedadEfectiva(
                    (int)(
                        $datos['empleado_id']
                        ?? 0
                    ),
                    $datos['fecha_liquidacion']
                    ?? ''
                );


        $antiguedadTexto =
            $aniosAntiguedad
            . (
                $aniosAntiguedad === 1
                    ? ' año'
                    : ' años'
            );

        $estadoRecibo = strtoupper(
            trim((string)($datos['estado'] ?? ''))
        );

        return [
            'datos' => $datos,
            'detalle' => $detalle,
            'haberesRem' => $haberesRem,
            'haberesNoRem' => $haberesNoRem,
            'asignaciones' => $asignaciones,
            'descuentos' => $descuentos,
            'aportesPatronales' => $aportesPatronales,
            'totalHaberesRem' => $totalHaberesRem,
            'totalHaberesNoRem' => $totalHaberesNoRem,
            'totalAsignaciones' => $totalAsignaciones,
            'totalDescuentos' => $totalDescuentos,
            'totalPatronales' => $totalPatronales,
            'neto' => $neto,
            'antiguedadTexto' => $antiguedadTexto,
            'esBorrador' => $estadoRecibo === 'BORRADOR',
            'esCerrada' => $estadoRecibo === 'CERRADA',
            'esAnulada' => $estadoRecibo === 'ANULADA'
        ];
    }

    private function obtenerConfiguracionCorreo()
    {
        $archivoConfig =
            __DIR__
            . '/../../../config/config_correo.php';

        if (file_exists($archivoConfig)) {
            require_once $archivoConfig;
        }

        $host = defined('SMTP_HOST')
            ? SMTP_HOST
            : (getenv('SIGENMUNI_SMTP_HOST') ?: 'smtp.gmail.com');

        $puerto = defined('SMTP_PORT')
            ? (int)SMTP_PORT
            : (int)(getenv('SIGENMUNI_SMTP_PORT') ?: 587);

        $usuario = defined('SMTP_USER')
            ? SMTP_USER
            : (getenv('SIGENMUNI_SMTP_USER') ?: '');

        $clave = defined('SMTP_PASS')
            ? SMTP_PASS
            : (getenv('SIGENMUNI_SMTP_PASS') ?: '');

        $remitenteEmail = defined('SMTP_FROM')
            ? SMTP_FROM
            : (getenv('SIGENMUNI_SMTP_FROM') ?: $usuario);

        $remitenteNombre = defined('SMTP_FROM_NAME')
            ? SMTP_FROM_NAME
            : (
                getenv('SIGENMUNI_SMTP_FROM_NAME')
                ?: 'Municipalidad de Fortín Lugones'
            );

        if (
            trim((string)$usuario) === ''
            ||
            trim((string)$clave) === ''
        ) {
            throw new Exception(
                "La configuración SMTP no está completa. Debe definir el usuario y la contraseña de aplicación del correo."
            );
        }

        if (!filter_var($remitenteEmail, FILTER_VALIDATE_EMAIL)) {
            throw new Exception(
                "El correo remitente configurado no es válido."
            );
        }

        return [
            'host' => $host,
            'puerto' => $puerto,
            'usuario' => $usuario,
            'clave' => $clave,
            'remitente_email' => $remitenteEmail,
            'remitente_nombre' => $remitenteNombre
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDAR PERÍODO SEGÚN TIPO DE LIQUIDACIÓN
    |--------------------------------------------------------------------------
    |
    | AGUINALDO:
    |     solamente junio (06) o diciembre (12).
    |
    | COMPLEMENTARIA_SAC:
    |     puede realizarse en cualquier mes.
    |
    */

    private function validarPeriodoSegunTipo(
        $tipo,
        $periodo
    ) {
        $tipo =
            strtoupper(
                trim(
                    (string)$tipo
                )
            );

        $periodo =
            trim(
                (string)$periodo
            );


        if (!$this->modelo->periodoValido($periodo)) {

            throw new Exception(
                "El período de liquidación no tiene un formato válido."
            );
        }


        if ($tipo !== 'AGUINALDO') {

            return true;
        }


        $partes =
            explode(
                '-',
                $periodo
            );


        $mes =
            (int)(
                $partes[1]
                ?? 0
            );


        if (
            $mes !== 6
            &&
            $mes !== 12
        ) {

            throw new Exception(
                "La liquidación de AGUINALDO solo puede corresponder a los períodos de junio o diciembre. "
                . "Para liquidar SAC en otro mes utilice el tipo COMPLEMENTARIA DE SAC."
            );
        }


        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDAR COHERENCIA ENTRE PERÍODO Y FECHA
    |--------------------------------------------------------------------------
    |
    | Para liquidaciones que determinan días de SAC mediante la fecha de
    | liquidación, el mes/año de la fecha debe coincidir con el período.
    |
    | Se aplica a:
    |
    | - AGUINALDO
    | - COMPLEMENTARIA_SAC
    |
    | No se fuerza esta regla sobre MENSUAL o COMPLEMENTARIA de haberes porque
    | una liquidación de haberes puede generarse administrativamente en una
    | fecha posterior al período que está corrigiendo.
    |
    |--------------------------------------------------------------------------
    */

    private function validarCoherenciaPeriodoFechaSegunTipo(
        $tipo,
        $periodo,
        $fecha
    ) {
        $tipo =
            strtoupper(
                trim(
                    (string)$tipo
                )
            );


        $periodo =
            trim(
                (string)$periodo
            );


        $fecha =
            trim(
                (string)$fecha
            );


        if (!$this->modelo->periodoValido($periodo)) {

            throw new Exception(
                "El período de liquidación no tiene un formato válido."
            );
        }


        if (!$this->modelo->fechaValida($fecha)) {

            throw new Exception(
                "La fecha de liquidación no es válida."
            );
        }


        if (
            $tipo !== 'AGUINALDO'
            &&
            $tipo !== 'COMPLEMENTARIA_SAC'
        ) {

            return true;
        }


        $periodoFecha =
            substr(
                $fecha,
                0,
                7
            );


        if ($periodoFecha !== $periodo) {

            $nombreTipo =
                $tipo === 'AGUINALDO'
                    ? 'AGUINALDO'
                    : 'COMPLEMENTARIA DE SAC';


            throw new Exception(
                "Para una liquidación de "
                . $nombreTipo
                . ", la Fecha de Liquidación debe pertenecer al mismo mes y año del período seleccionado. "
                . "Período: "
                . $periodo
                . " - Fecha ingresada: "
                . date(
                    'd/m/Y',
                    strtotime($fecha)
                )
                . "."
            );
        }


        return true;
    }


    private function recibirDatosLiquidacion()
    {
        return [
            'tipo_liquidacion' => trim(
                $_POST['tipo_liquidacion'] ?? ''
            ),
            'periodo' => trim(
                $_POST['periodo'] ?? ''
            ),
            'fecha_liquidacion' => trim(
                $_POST['fecha_liquidacion'] ?? ''
            ),
            'descripcion' => trim(
                $_POST['descripcion'] ?? ''
            )
        ];
    }

    private function validarYNormalizarLiquidacion($datos)
    {
        $tipo = $datos['tipo_liquidacion'];

        if ($tipo === '') {
            throw new Exception(
                "Debe seleccionar el tipo de liquidación."
            );
        }

        if (!$this->modelo->tipoValido($tipo)) {
            throw new Exception(
                "El tipo de liquidación seleccionado no es válido."
            );
        }

        $periodo = $datos['periodo'];

        if ($periodo === '') {
            throw new Exception(
                "Debe seleccionar el período de liquidación."
            );
        }

        if (!$this->modelo->periodoValido($periodo)) {
            throw new Exception(
                "El período de liquidación no tiene un formato válido."
            );
        }


        /*
        |--------------------------------------------------------------------------
        | REGLAS DEL PERÍODO SEGÚN TIPO
        |--------------------------------------------------------------------------
        */

        $this->validarPeriodoSegunTipo(
            $tipo,
            $periodo
        );


        $fecha = $datos['fecha_liquidacion'];

        if ($fecha === '') {
            throw new Exception(
                "Debe seleccionar la fecha de liquidación."
            );
        }

        if (!$this->modelo->fechaValida($fecha)) {
            throw new Exception(
                "La fecha de liquidación no es válida."
            );
        }


        /*
        |--------------------------------------------------------------------------
        | COHERENCIA PERÍODO / FECHA PARA SAC
        |--------------------------------------------------------------------------
        */

        $this->validarCoherenciaPeriodoFechaSegunTipo(
            $tipo,
            $periodo,
            $fecha
        );


        $descripcion = trim($datos['descripcion']);

        if (strlen($descripcion) > 500) {
            throw new Exception(
                "La descripción no puede superar los 500 caracteres."
            );
        }

        return [
            'tipo_liquidacion' => $tipo,
            'periodo' => $periodo,
            'fecha_liquidacion' => $fecha,
            'descripcion' => $descripcion === ''
                ? null
                : $descripcion
        ];
    }
}