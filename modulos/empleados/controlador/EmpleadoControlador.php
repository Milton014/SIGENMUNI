<?php

require_once __DIR__ . '/../modelo/EmpleadoModelo.php';
require_once __DIR__ . '/../../../core/Auditoria.php';
require_once __DIR__ . '/../../../core/Csrf.php';
require_once __DIR__ . '/../../../core/Url.php';


class EmpleadoControlador
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
        $this->conexion = $conexion;
        $this->modelo = new EmpleadoModelo($conexion);
    }


    /*
    |--------------------------------------------------------------------------
    | LISTADO DE EMPLEADOS
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $busqueda = trim(
            $_GET['busqueda'] ?? ''
        );

        $mensaje = "";
        $tipo_mensaje = "";


        if (isset($_GET['ok'])) {

            switch ($_GET['ok']) {

                case 'nuevo':

                    $mensaje =
                        "Empleado guardado correctamente.";

                    $tipo_mensaje =
                        "ok";

                    break;


                case 'editar':

                    $mensaje =
                        "Empleado actualizado correctamente.";

                    $tipo_mensaje =
                        "ok";

                    break;


                case 'estado':

                    $mensaje =
                        "Estado del empleado actualizado correctamente.";

                    $tipo_mensaje =
                        "ok";

                    break;
            }
        }


        try {

            $empleados =
                $this->modelo->listar(
                    $busqueda
                );


            require __DIR__
                . '/../vista/empleados.php';

        } catch (Exception $e) {

            die(
                "Error al cargar los empleados: "
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
    | NUEVO EMPLEADO
    |--------------------------------------------------------------------------
    */

    public function nuevo()
    {
        $mensaje = "";
        $tipo_mensaje = "error";


        $datos = [

            'institucion_id' => '',
            'oficina_id' => '',
            'situacion_id' => '',
            'escalafon_id' => '',
            'categoria_id' => '',

            'nro_legajo' => '',
            'apellido' => '',
            'nombre' => '',
            'dni' => '',
            'cuil' => '',
            'fecha_alta' => '',

            'telefono' => '',
            'email' => '',
            'domicilio' => '',
            'observaciones' => ''
        ];


        try {

            /*
            |--------------------------------------------------------------------------
            | COMBOS
            |--------------------------------------------------------------------------
            */

            $instituciones =
                $this->modelo->obtenerInstituciones();

            $oficinas =
                $this->modelo->obtenerOficinas();

            $situaciones =
                $this->modelo->obtenerSituaciones();

            $escalafones =
                $this->modelo->obtenerEscalafones();

            $categorias =
                $this->modelo->obtenerCategorias();


            /*
            |--------------------------------------------------------------------------
            | REFERENCIA DE LEGAJOS
            |--------------------------------------------------------------------------
            |
            | Se muestra el último legajo registrado y el próximo sugerido.
            |
            | En una apertura normal del formulario se precarga el próximo número.
            | Si el POST vuelve por un error de validación, se conserva exactamente
            | el legajo que había ingresado el usuario.
            |
            */

            $referenciaLegajos =
                $this->modelo
                    ->obtenerReferenciaLegajos();


            $ultimoLegajoUtilizado =
                (int)(
                    $referenciaLegajos[
                        'ultimo_utilizado'
                    ]
                    ?? 0
                );


            $proximoLegajoSugerido =
                (int)(
                    $referenciaLegajos[
                        'proximo_sugerido'
                    ]
                    ?? (
                        $ultimoLegajoUtilizado
                        + 1
                    )
                );


            if (
                $_SERVER['REQUEST_METHOD'] !== 'POST'
                &&
                trim(
                    (string)(
                        $datos['nro_legajo']
                        ?? ''
                    )
                ) === ''
            ) {

                $datos['nro_legajo'] =
                    (string)$proximoLegajoSugerido;
            }


            /*
            |--------------------------------------------------------------------------
            | POST
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


                $datos['institucion_id'] =
                    isset($_POST['institucion_id'])
                        ? (int) $_POST['institucion_id']
                        : 0;

                $datos['oficina_id'] =
                    isset($_POST['oficina_id'])
                        ? (int) $_POST['oficina_id']
                        : 0;

                $datos['situacion_id'] =
                    isset($_POST['situacion_id'])
                        ? (int) $_POST['situacion_id']
                        : 0;

                $datos['escalafon_id'] =
                    isset($_POST['escalafon_id'])
                        ? (int) $_POST['escalafon_id']
                        : 0;

                $datos['categoria_id'] =
                    isset($_POST['categoria_id'])
                        ? (int) $_POST['categoria_id']
                        : 0;


                $datos['nro_legajo'] =
                    trim($_POST['nro_legajo'] ?? '');

                $datos['apellido'] =
                    trim($_POST['apellido'] ?? '');

                $datos['nombre'] =
                    trim($_POST['nombre'] ?? '');

                $datos['dni'] =
                    trim($_POST['dni'] ?? '');

                $datos['cuil'] =
                    trim($_POST['cuil'] ?? '');

                $datos['fecha_alta'] =
                    trim($_POST['fecha_alta'] ?? '');

                $datos['telefono'] =
                    trim($_POST['telefono'] ?? '');

                $datos['email'] =
                    trim($_POST['email'] ?? '');

                $datos['domicilio'] =
                    trim($_POST['domicilio'] ?? '');

                $datos['observaciones'] =
                    trim($_POST['observaciones'] ?? '');


                /*
                |--------------------------------------------------------------------------
                | OBLIGATORIOS
                |--------------------------------------------------------------------------
                */

                if (
                    $datos['nro_legajo'] === '' ||
                    $datos['apellido'] === '' ||
                    $datos['nombre'] === '' ||
                    $datos['dni'] === '' ||
                    $datos['cuil'] === '' ||
                    $datos['fecha_alta'] === '' ||
                    $datos['email'] === '' ||
                    $datos['institucion_id'] <= 0 ||
                    $datos['oficina_id'] <= 0 ||
                    $datos['situacion_id'] <= 0 ||
                    $datos['categoria_id'] <= 0
                ) {

                    throw new Exception(
                        "Complete todos los campos obligatorios."
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | LEGAJO
                |--------------------------------------------------------------------------
                */

                if (!ctype_digit($datos['nro_legajo'])) {

                    throw new Exception(
                        "El número de legajo debe contener solo números."
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | DNI
                |--------------------------------------------------------------------------
                */

                if (!ctype_digit($datos['dni'])) {

                    throw new Exception(
                        "El DNI debe contener solo números."
                    );
                }


                if (
                    strlen($datos['dni']) < 7 ||
                    strlen($datos['dni']) > 8
                ) {

                    throw new Exception(
                        "El DNI debe tener 7 u 8 dígitos."
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | CUIL
                |--------------------------------------------------------------------------
                */

                if (!ctype_digit($datos['cuil'])) {

                    throw new Exception(
                        "El CUIL debe contener solo números."
                    );
                }


                if (strlen($datos['cuil']) !== 11) {

                    throw new Exception(
                        "El CUIL debe tener exactamente 11 dígitos."
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | EMAIL
                |--------------------------------------------------------------------------
                */

                if (
                    !filter_var(
                        $datos['email'],
                        FILTER_VALIDATE_EMAIL
                    )
                ) {

                    throw new Exception(
                        "El email ingresado no tiene un formato válido."
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | CATEGORÍA / ESCALAFÓN
                |--------------------------------------------------------------------------
                |
                | Cargos especiales (código >= 1000):
                |     escalafon_id = NULL.
                |
                | Categorías generales:
                |     escalafón obligatorio (8, 9 o 10).
                |
                |--------------------------------------------------------------------------
                */

                $datos['escalafon_id'] =
                    $this->modelo->normalizarEscalafon(
                        $datos['categoria_id'],
                        $datos['escalafon_id']
                    );


                /*
                |--------------------------------------------------------------------------
                | DUPLICADOS
                |--------------------------------------------------------------------------
                */

                if (
                    $this->modelo->existeLegajo(
                        $datos['nro_legajo']
                    )
                ) {

                    throw new Exception(
                        "Ya existe un empleado con ese número de legajo."
                    );
                }


                if (
                    $this->modelo->existeDni(
                        $datos['dni']
                    )
                ) {

                    throw new Exception(
                        "Ya existe un empleado con ese DNI."
                    );
                }


                if (
                    $this->modelo->existeCuil(
                        $datos['cuil']
                    )
                ) {

                    throw new Exception(
                        "Ya existe un empleado con ese CUIL."
                    );
                }


                if (
                    $this->modelo->existeEmail(
                        $datos['email']
                    )
                ) {

                    throw new Exception(
                        "Ya existe un empleado con ese email."
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | NORMALIZAR
                |--------------------------------------------------------------------------
                */

                $datosGuardar = $datos;

                $datosGuardar['fecha_baja'] =
                    null;

                $datosGuardar['telefono'] =
                    $datos['telefono'] === ''
                        ? null
                        : $datos['telefono'];

                $datosGuardar['domicilio'] =
                    $datos['domicilio'] === ''
                        ? null
                        : $datos['domicilio'];

                $datosGuardar['observaciones'] =
                    $datos['observaciones'] === ''
                        ? null
                        : $datos['observaciones'];


                /*
                |--------------------------------------------------------------------------
                | GUARDAR
                |--------------------------------------------------------------------------
                */

                $idEmpleado =
                    $this->modelo->guardar(
                        $datosGuardar
                    );


                if ($idEmpleado <= 0) {

                    throw new Exception(
                        "No se pudo guardar el empleado."
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | AUDITORÍA - ALTA DE EMPLEADO
                |--------------------------------------------------------------------------
                */

                $descripcionEmpleado =
                    trim(
                        $datosGuardar['apellido']
                        . ", "
                        . $datosGuardar['nombre']
                        . " - Legajo "
                        . $datosGuardar['nro_legajo']
                    );


                $datosAdministrativosAuditoria =
                    $this->armarDatosAdministrativosAuditoria(
                        $datosGuardar['institucion_id'],
                        $datosGuardar['oficina_id'],
                        $datosGuardar['situacion_id'],
                        $datosGuardar['escalafon_id'],
                        $datosGuardar['categoria_id'],
                        $instituciones,
                        $oficinas,
                        $situaciones,
                        $escalafones,
                        $categorias
                    );


                $auditoriaRegistrada =
                    Auditoria::registrar(
                        $this->conexion,
                        [
                            'modulo' =>
                                'Gestión de Empleados',

                            'accion' =>
                                'ALTA',

                            'entidad' =>
                                'EMPLEADO',

                            'entidad_id' =>
                                $idEmpleado,

                            'entidad_descripcion' =>
                                $descripcionEmpleado,

                            'detalle' =>
                                'Se dio de alta un nuevo empleado.',

                            'datos_nuevos' =>
                                array_merge(
                                    [
                                        'nro_legajo' =>
                                            $datosGuardar['nro_legajo'],

                                        'apellido' =>
                                            $datosGuardar['apellido'],

                                        'nombre' =>
                                            $datosGuardar['nombre']
                                    ],
                                    $datosAdministrativosAuditoria,
                                    [
                                        'fecha_alta' =>
                                            $datosGuardar['fecha_alta']
                                    ]
                                )
                        ]
                    );


                if (!$auditoriaRegistrada) {

                    error_log(
                        "SIGENMUNI: el empleado ID "
                        . $idEmpleado
                        . " fue creado, pero no se pudo registrar su auditoría."
                    );
                }


                $urlListado =
                    sigenmuniUrlRuta(
                        'empleados',
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


        require __DIR__
            . '/../vista/empleado_nuevo.php';
    }


    /*
    |--------------------------------------------------------------------------
    | VER EMPLEADO
    |--------------------------------------------------------------------------
    */

    public function ver()
    {
        $id =
            isset($_GET['id'])
                ? (int) $_GET['id']
                : 0;


        /*
        |--------------------------------------------------------------------------
        | URL DE RETORNO
        |--------------------------------------------------------------------------
        */

        $urlListado =
            sigenmuniUrlRuta(
                'empleados'
            );


        /*
        |--------------------------------------------------------------------------
        | VALIDAR ID
        |--------------------------------------------------------------------------
        */

        if ($id <= 0) {

            header(
                'Location: ' . $urlListado
            );

            exit();
        }


        try {

            $empleado =
                $this->modelo->obtenerPorId(
                    $id
                );


            if (!$empleado) {

                header(
                    'Location: ' . $urlListado
                );

                exit();
            }


            require __DIR__
                . '/../vista/empleado_ver.php';


        } catch (Exception $e) {

            die(
                "Error al cargar el empleado: "
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
    | EDITAR EMPLEADO
    |--------------------------------------------------------------------------
    */

    public function editar()
    {
        $id =
            isset($_GET['id'])
                ? (int) $_GET['id']
                : 0;


        $mensaje = "";
        $tipo_mensaje = "error";


        /*
        |--------------------------------------------------------------------------
        | URL DE RETORNO
        |--------------------------------------------------------------------------
        */

        $urlListado =
            sigenmuniUrlRuta(
                'empleados'
            );


        if ($id <= 0) {

            header(
                'Location: ' . $urlListado
            );

            exit();
        }


        try {

            /*
            |--------------------------------------------------------------------------
            | EMPLEADO
            |--------------------------------------------------------------------------
            */

            $empleado =
                $this->modelo->obtenerPorId(
                    $id
                );


            if (!$empleado) {

                header(
                    'Location: ' . $urlListado
                );

                exit();
            }


            /*
            |--------------------------------------------------------------------------
            | COMBOS
            |--------------------------------------------------------------------------
            */

            $instituciones =
                $this->modelo->obtenerInstituciones();

            $oficinas =
                $this->modelo->obtenerOficinas();

            $situaciones =
                $this->modelo->obtenerSituaciones();

            $escalafones =
                $this->modelo->obtenerEscalafones();

            $categorias =
                $this->modelo->obtenerCategorias();


            /*
            |--------------------------------------------------------------------------
            | POST
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


                $datos = [];


                $datos['institucion_id'] =
                    isset($_POST['institucion_id'])
                        ? (int) $_POST['institucion_id']
                        : 0;

                $datos['oficina_id'] =
                    isset($_POST['oficina_id'])
                        ? (int) $_POST['oficina_id']
                        : 0;

                $datos['situacion_id'] =
                    isset($_POST['situacion_id'])
                        ? (int) $_POST['situacion_id']
                        : 0;

                $datos['escalafon_id'] =
                    isset($_POST['escalafon_id'])
                        ? (int) $_POST['escalafon_id']
                        : 0;

                $datos['categoria_id'] =
                    isset($_POST['categoria_id'])
                        ? (int) $_POST['categoria_id']
                        : 0;


                $datos['nro_legajo'] =
                    trim($_POST['nro_legajo'] ?? '');

                $datos['apellido'] =
                    trim($_POST['apellido'] ?? '');

                $datos['nombre'] =
                    trim($_POST['nombre'] ?? '');

                $datos['dni'] =
                    trim($_POST['dni'] ?? '');

                $datos['cuil'] =
                    trim($_POST['cuil'] ?? '');

                /*
                |--------------------------------------------------------------------------
                | FECHA DE ALTA - PROTEGIDA POR HISTORIAL LABORAL
                |--------------------------------------------------------------------------
                |
                | fecha_alta representa la incorporación inicial del empleado y está
                | vinculada al primer período de empleado_periodo_laboral.
                |
                | Por integridad histórica NO se acepta una fecha_alta enviada por
                | POST desde la edición general. Aunque el formulario fuera
                | manipulado manualmente, se conserva siempre el valor ya registrado.
                |
                | Las reincorporaciones y las inactivaciones se administran
                | exclusivamente desde Activar / Inactivar y su historial laboral.
                |
                */

                $datos['fecha_alta'] =
                    $empleado['fecha_alta']
                    ?? null;

                /*
                |--------------------------------------------------------------------------
                | FECHA BAJA - LEGACY
                |--------------------------------------------------------------------------
                |
                | fecha_baja ya no se edita desde Gestión de Personal.
                | Se conserva únicamente para compatibilidad histórica con la base
                | y con código anterior que todavía espera esa clave.
                |
                | La fecha operativa del estado del empleado es fecha_inactivo,
                | administrada exclusivamente desde Activar / Inactivar.
                |
                */

                $datos['fecha_baja'] =
                    $empleado['fecha_baja']
                    ?? null;

                $datos['telefono'] =
                    trim($_POST['telefono'] ?? '');

                $datos['email'] =
                    trim($_POST['email'] ?? '');

                $datos['domicilio'] =
                    trim($_POST['domicilio'] ?? '');

                $datos['observaciones'] =
                    trim($_POST['observaciones'] ?? '');


                /*
                |--------------------------------------------------------------------------
                | OBLIGATORIOS
                |--------------------------------------------------------------------------
                |
                | fecha_alta continúa siendo obligatoria como dato histórico, pero
                | en edición se conserva desde el registro existente y no desde POST.
                |
                */

                if (
                    $datos['nro_legajo'] === '' ||
                    $datos['apellido'] === '' ||
                    $datos['nombre'] === '' ||
                    $datos['dni'] === '' ||
                    $datos['cuil'] === '' ||
                    $datos['fecha_alta'] === '' ||
                    $datos['email'] === '' ||
                    $datos['institucion_id'] <= 0 ||
                    $datos['oficina_id'] <= 0 ||
                    $datos['situacion_id'] <= 0 ||
                    $datos['categoria_id'] <= 0
                ) {

                    throw new Exception(
                        "Complete todos los campos obligatorios."
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | LEGAJO
                |--------------------------------------------------------------------------
                */

                if (!ctype_digit($datos['nro_legajo'])) {

                    throw new Exception(
                        "El legajo debe contener solo números."
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | DNI
                |--------------------------------------------------------------------------
                */

                if (
                    !ctype_digit($datos['dni']) ||
                    strlen($datos['dni']) < 7 ||
                    strlen($datos['dni']) > 8
                ) {

                    throw new Exception(
                        "El DNI debe tener entre 7 y 8 números."
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | CUIL
                |--------------------------------------------------------------------------
                */

                if (
                    !ctype_digit($datos['cuil']) ||
                    strlen($datos['cuil']) !== 11
                ) {

                    throw new Exception(
                        "El CUIL debe tener exactamente 11 números."
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | EMAIL
                |--------------------------------------------------------------------------
                */

                if (
                    !filter_var(
                        $datos['email'],
                        FILTER_VALIDATE_EMAIL
                    )
                ) {

                    throw new Exception(
                        "El email ingresado no tiene un formato válido."
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | CATEGORÍA / ESCALAFÓN
                |--------------------------------------------------------------------------
                |
                | Cargos especiales (código >= 1000):
                |     escalafon_id = NULL.
                |
                | Categorías generales:
                |     escalafón obligatorio (8, 9 o 10).
                |
                |--------------------------------------------------------------------------
                */

                $datos['escalafon_id'] =
                    $this->modelo->normalizarEscalafon(
                        $datos['categoria_id'],
                        $datos['escalafon_id']
                    );


                /*
                |--------------------------------------------------------------------------
                | DUPLICADOS
                |--------------------------------------------------------------------------
                */

                if (
                    $this->modelo->existeLegajo(
                        $datos['nro_legajo'],
                        $id
                    )
                ) {

                    throw new Exception(
                        "Ya existe otro empleado con ese legajo."
                    );
                }


                if (
                    $this->modelo->existeDni(
                        $datos['dni'],
                        $id
                    )
                ) {

                    throw new Exception(
                        "Ya existe otro empleado con ese DNI."
                    );
                }


                if (
                    $this->modelo->existeCuil(
                        $datos['cuil'],
                        $id
                    )
                ) {

                    throw new Exception(
                        "Ya existe otro empleado con ese CUIL."
                    );
                }


                if (
                    $this->modelo->existeEmail(
                        $datos['email'],
                        $id
                    )
                ) {

                    throw new Exception(
                        "Ya existe otro empleado con ese email."
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | NORMALIZAR
                |--------------------------------------------------------------------------
                */

                $datos['telefono'] =
                    $datos['telefono'] === ''
                        ? null
                        : $datos['telefono'];

                $datos['domicilio'] =
                    $datos['domicilio'] === ''
                        ? null
                        : $datos['domicilio'];

                $datos['observaciones'] =
                    $datos['observaciones'] === ''
                        ? null
                        : $datos['observaciones'];


                /*
                |--------------------------------------------------------------------------
                | ACTUALIZAR
                |--------------------------------------------------------------------------
                */

                $actualizado =
                    $this->modelo->actualizar(
                        $id,
                        $datos
                    );


                if (!$actualizado) {

                    throw new Exception(
                        "No se pudo actualizar el empleado."
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | AUDITORÍA - EDICIÓN DE EMPLEADO
                |--------------------------------------------------------------------------
                |
                | Guardamos una fotografía administrativa anterior y posterior
                | de los datos relevantes. No duplicamos DNI, CUIL, email,
                | teléfono ni domicilio dentro de la auditoría.
                |
                |--------------------------------------------------------------------------
                */

                $descripcionEmpleado =
                    trim(
                        $datos['apellido']
                        . ", "
                        . $datos['nombre']
                        . " - Legajo "
                        . $datos['nro_legajo']
                    );


                $datosAdministrativosAnteriores =
                    $this->armarDatosAdministrativosDesdeEmpleado(
                        $empleado
                    );


                $datosAdministrativosNuevos =
                    $this->armarDatosAdministrativosAuditoria(
                        $datos['institucion_id'],
                        $datos['oficina_id'],
                        $datos['situacion_id'],
                        $datos['escalafon_id'],
                        $datos['categoria_id'],
                        $instituciones,
                        $oficinas,
                        $situaciones,
                        $escalafones,
                        $categorias
                    );


                $datosAnterioresAuditoria =
                    array_merge(
                        [
                            'nro_legajo' =>
                                $empleado['nro_legajo']
                                ?? null,

                            'apellido' =>
                                $empleado['apellido']
                                ?? null,

                            'nombre' =>
                                $empleado['nombre']
                                ?? null
                        ],
                        $datosAdministrativosAnteriores,
                        [
                            'fecha_alta' =>
                                $empleado['fecha_alta']
                                ?? null,

                            'estado' =>
                                (int)($empleado['activo'] ?? 0) === 1
                                    ? 'ACTIVO'
                                    : 'INACTIVO',

                            'fecha_inactivo' =>
                                $empleado['fecha_inactivo']
                                ?? null
                        ]
                    );


                $datosNuevosAuditoria =
                    array_merge(
                        [
                            'nro_legajo' =>
                                $datos['nro_legajo'],

                            'apellido' =>
                                $datos['apellido'],

                            'nombre' =>
                                $datos['nombre']
                        ],
                        $datosAdministrativosNuevos,
                        [
                            /*
                            | fecha_alta no es modificable desde la edición general.
                            | Se conserva como fotografía histórica del alta inicial.
                            */
                            'fecha_alta' =>
                                $datos['fecha_alta'],

                            /*
                            | La edición general tampoco modifica el estado ni la
                            | fecha_inactivo. Se conservan como fotografía
                            | administrativa para la auditoría.
                            */
                            'estado' =>
                                (int)($empleado['activo'] ?? 0) === 1
                                    ? 'ACTIVO'
                                    : 'INACTIVO',

                            'fecha_inactivo' =>
                                $empleado['fecha_inactivo']
                                ?? null
                        ]
                    );


                $auditoriaRegistrada =
                    Auditoria::registrar(
                        $this->conexion,
                        [
                            'modulo' =>
                                'Gestión de Empleados',

                            'accion' =>
                                'EDICION',

                            'entidad' =>
                                'EMPLEADO',

                            'entidad_id' =>
                                $id,

                            'entidad_descripcion' =>
                                $descripcionEmpleado,

                            'detalle' =>
                                'Se actualizaron los datos del empleado.',

                            'datos_anteriores' =>
                                $datosAnterioresAuditoria,

                            'datos_nuevos' =>
                                $datosNuevosAuditoria
                        ]
                    );


                if (!$auditoriaRegistrada) {

                    error_log(
                        "SIGENMUNI: el empleado ID "
                        . $id
                        . " fue actualizado, pero no se pudo registrar su auditoría."
                    );
                }


                $urlDestino =
                    sigenmuniUrlRuta(
                        'empleados',
                        [
                            'ok' => 'editar'
                        ]
                    );


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


            if ($_SERVER['REQUEST_METHOD'] === 'POST') {

                $empleadoAnterior =
                    $empleado
                    ?? [];


                $empleado =
                    array_merge(
                        $empleadoAnterior,
                        $_POST
                    );


                /*
                |--------------------------------------------------------------------------
                | CONSERVAR FECHAS HISTÓRICAS / ESTADO
                |--------------------------------------------------------------------------
                |
                | fecha_alta, fecha_baja, fecha_inactivo y activo no deben quedar
                | alterados por un POST de la edición general.
                |
                | Esto es especialmente importante para fecha_alta: aunque un request
                | sea manipulado, la vista vuelve a mostrar siempre la fecha original
                | vinculada al historial laboral.
                |
                */

                $empleado['fecha_alta'] =
                    $empleadoAnterior['fecha_alta']
                    ?? null;

                $empleado['fecha_baja'] =
                    $empleadoAnterior['fecha_baja']
                    ?? null;

                $empleado['fecha_inactivo'] =
                    $empleadoAnterior['fecha_inactivo']
                    ?? null;

                $empleado['activo'] =
                    $empleadoAnterior['activo']
                    ?? 1;


                /*
                |--------------------------------------------------------------------------
                | CONSERVAR IDs NORMALIZADOS
                |--------------------------------------------------------------------------
                */

                $empleado['categoria_id'] =
                    $datos['categoria_id']
                    ?? 0;


                $empleado['escalafon_id'] =
                    $datos['escalafon_id']
                    ?? null;
            }
        }


        require __DIR__
            . '/../vista/empleado_editar.php';
    }


    /*
    |--------------------------------------------------------------------------
    | CAMBIAR ESTADO DEL EMPLEADO
    |--------------------------------------------------------------------------
    */

    public function estado()
    {
        $id =
            isset($_GET['id'])
                ? (int)$_GET['id']
                : 0;


        $accion =
            strtolower(
                trim(
                    (string)(
                        $_GET['accion']
                        ?? ''
                    )
                )
            );


        $mensaje =
            "";

        $tipo_mensaje =
            "error";


        /*
        |--------------------------------------------------------------------------
        | URL DE RETORNO
        |--------------------------------------------------------------------------
        */

        $urlListado =
            sigenmuniUrlRuta(
                'empleados'
            );


        /*
        |--------------------------------------------------------------------------
        | VALIDAR PARÁMETROS
        |--------------------------------------------------------------------------
        */

        if ($id <= 0) {

            header(
                'Location: ' . $urlListado
            );

            exit();
        }


        if ($accion === 'activar') {

            $activoNuevo =
                1;

            $tituloAccion =
                'Reactivar Empleado';

            $textoAccion =
                'reincorporar';

        } elseif ($accion === 'inactivar') {

            $activoNuevo =
                0;

            $tituloAccion =
                'Inactivar Empleado';

            $textoAccion =
                'inactivar';

        } else {

            header(
                'Location: ' . $urlListado
            );

            exit();
        }


        /*
        |--------------------------------------------------------------------------
        | FECHA PREDETERMINADA
        |--------------------------------------------------------------------------
        */

        $zona =
            new DateTimeZone(
                'America/Argentina/Buenos_Aires'
            );


        $fechaHoy =
            (
                new DateTime(
                    'now',
                    $zona
                )
            )->format(
                'Y-m-d'
            );


        $fechaEfectiva =
            $fechaHoy;


        $observacion =
            '';


        try {

            /*
            |--------------------------------------------------------------------------
            | EMPLEADO
            |--------------------------------------------------------------------------
            */

            $empleado =
                $this->modelo->obtenerPorId(
                    $id
                );


            if (!$empleado) {

                header(
                    'Location: ' . $urlListado
                );

                exit();
            }


            $estadoActual =
                (int)(
                    $empleado['activo']
                    ?? 0
                );


            /*
            |--------------------------------------------------------------------------
            | IMPEDIR ACCIÓN INNECESARIA
            |--------------------------------------------------------------------------
            */

            if (
                $accion === 'activar'
                &&
                $estadoActual === 1
            ) {

                header(
                    'Location: ' . $urlListado
                );

                exit();
            }


            if (
                $accion === 'inactivar'
                &&
                $estadoActual === 0
            ) {

                header(
                    'Location: ' . $urlListado
                );

                exit();
            }


            /*
            |--------------------------------------------------------------------------
            | HISTORIAL LABORAL PARA REFERENCIA
            |--------------------------------------------------------------------------
            */

            $periodosLaborales =
                $this->modelo->obtenerPeriodosLaborales(
                    $id
                );


            $periodoActual =
                $this->modelo->obtenerPeriodoLaboralAbierto(
                    $id
                );


            /*
            |--------------------------------------------------------------------------
            | POST - CONFIRMAR CAMBIO
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


                $fechaEfectiva =
                    trim(
                        (string)(
                            $_POST['fecha_efectiva']
                            ?? ''
                        )
                    );


                $observacion =
                    trim(
                        (string)(
                            $_POST['observacion']
                            ?? ''
                        )
                    );


                /*
                |--------------------------------------------------------------------------
                | VALIDAR FECHA
                |--------------------------------------------------------------------------
                */

                if ($fechaEfectiva === '') {

                    throw new Exception(
                        "Debe ingresar la fecha efectiva."
                    );
                }


                if (!$this->fechaValida($fechaEfectiva)) {

                    throw new Exception(
                        "La fecha efectiva ingresada no es válida."
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | NO PERMITIR FECHA FUTURA
                |--------------------------------------------------------------------------
                |
                | El cambio modifica inmediatamente el estado actual del empleado,
                | por eso la fecha efectiva puede ser hoy o una fecha anterior.
                |
                */

                if ($fechaEfectiva > $fechaHoy) {

                    throw new Exception(
                        "La fecha efectiva no puede ser posterior a la fecha actual."
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | VALIDACIONES SEGÚN ACCIÓN
                |--------------------------------------------------------------------------
                */

                if ($accion === 'inactivar') {

                    if (!$periodoActual) {

                        throw new Exception(
                            "El empleado no posee un período laboral abierto para inactivar."
                        );
                    }


                    $fechaInicioPeriodo =
                        trim(
                            (string)(
                                $periodoActual['fecha_desde']
                                ?? ''
                            )
                        );


                    if (
                        $fechaInicioPeriodo !== ''
                        &&
                        $fechaEfectiva < $fechaInicioPeriodo
                    ) {

                        throw new Exception(
                            "La fecha de inactivación no puede ser anterior al inicio del período laboral actual ("
                            . date(
                                'd/m/Y',
                                strtotime(
                                    $fechaInicioPeriodo
                                )
                            )
                            . ")."
                        );
                    }

                } else {

                    /*
                    |--------------------------------------------------------------------------
                    | REINCORPORACIÓN
                    |--------------------------------------------------------------------------
                    |
                    | Si existe un último período cerrado, la reincorporación debe
                    | ser posterior a su fecha_hasta porque los extremos son
                    | inclusivos.
                    |
                    */

                    $ultimoPeriodo =
                        null;


                    if (!empty($periodosLaborales)) {

                        $ultimoPeriodo =
                            $periodosLaborales[
                                count($periodosLaborales) - 1
                            ];
                    }


                    if (
                        $ultimoPeriodo
                        &&
                        !empty(
                            $ultimoPeriodo['fecha_hasta']
                        )
                        &&
                        $fechaEfectiva
                        <=
                        $ultimoPeriodo['fecha_hasta']
                    ) {

                        throw new Exception(
                            "La fecha de reincorporación debe ser posterior al "
                            . date(
                                'd/m/Y',
                                strtotime(
                                    $ultimoPeriodo['fecha_hasta']
                                )
                            )
                            . "."
                        );
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | OBSERVACIÓN
                |--------------------------------------------------------------------------
                */

                if (strlen($observacion) > 255) {

                    throw new Exception(
                        "La observación no puede superar los 255 caracteres."
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | USUARIO RESPONSABLE
                |--------------------------------------------------------------------------
                |
                | Algunas instalaciones pueden no guardar usuario_id en sesión.
                | La tabla permite NULL, por lo que no se fuerza un dato inexistente.
                |
                */

                $usuarioId =
                    isset($_SESSION['usuario_id'])
                        ? (int)$_SESSION['usuario_id']
                        : null;


                if (
                    $usuarioId !== null
                    &&
                    $usuarioId <= 0
                ) {

                    $usuarioId =
                        null;
                }


                /*
                |--------------------------------------------------------------------------
                | CAMBIAR ESTADO + HISTORIAL LABORAL
                |--------------------------------------------------------------------------
                |
                | EmpleadoModelo se encarga transaccionalmente de:
                |
                | INACTIVAR:
                |   - cerrar período laboral abierto;
                |   - cerrar conceptos ya iniciados en la misma fecha;
                |   - desactivar conceptos futuros todavía no iniciados;
                |   - activo = 0;
                |   - fecha_inactivo = fecha efectiva.
                |
                | REACTIVAR:
                |   - abrir nuevo período laboral;
                |   - activo = 1;
                |   - fecha_inactivo = NULL;
                |   - NO reactivar automáticamente conceptos anteriores.
                |
                */

                $actualizado =
                    $this->modelo->cambiarEstado(
                        $id,
                        $activoNuevo,
                        $fechaEfectiva,
                        $observacion,
                        $usuarioId
                    );


                if (!$actualizado) {

                    throw new Exception(
                        "No se pudo actualizar el estado del empleado."
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | AUDITORÍA
                |--------------------------------------------------------------------------
                */

                $accionAuditoria =
                    $activoNuevo === 1
                        ? 'ACTIVACION'
                        : 'INACTIVACION';


                $estadoAnteriorTexto =
                    $estadoActual === 1
                        ? 'ACTIVO'
                        : 'INACTIVO';


                $estadoNuevoTexto =
                    $activoNuevo === 1
                        ? 'ACTIVO'
                        : 'INACTIVO';


                $descripcionEmpleado =
                    trim(
                        ($empleado['apellido'] ?? '')
                        . ", "
                        . ($empleado['nombre'] ?? '')
                        . " - Legajo "
                        . ($empleado['nro_legajo'] ?? '')
                    );


                $detalleAuditoria =
                    $activoNuevo === 1
                        ?
                        'Se reincorporó el empleado y se abrió un nuevo período laboral. Los conceptos anteriores permanecen con su vigencia histórica.'
                        :
                        'Se inactivó el empleado, se cerró su período laboral actual y se ajustaron las vigencias de sus conceptos por empleado.';


                $datosAnteriores =
                    [
                        'estado' =>
                            $estadoAnteriorTexto,

                        'fecha_inactivo' =>
                            $empleado['fecha_inactivo']
                            ?? null
                    ];


                $datosNuevos =
                    [
                        'estado' =>
                            $estadoNuevoTexto,

                        'fecha_efectiva' =>
                            $fechaEfectiva,

                        'fecha_inactivo' =>
                            $activoNuevo === 0
                                ? $fechaEfectiva
                                : null,

                        'observacion' =>
                            $observacion !== ''
                                ? $observacion
                                : null
                    ];


                $auditoriaRegistrada =
                    Auditoria::registrar(
                        $this->conexion,
                        [
                            'modulo' =>
                                'Gestión de Empleados',

                            'accion' =>
                                $accionAuditoria,

                            'entidad' =>
                                'EMPLEADO',

                            'entidad_id' =>
                                $id,

                            'entidad_descripcion' =>
                                $descripcionEmpleado,

                            'detalle' =>
                                $detalleAuditoria,

                            'datos_anteriores' =>
                                $datosAnteriores,

                            'datos_nuevos' =>
                                $datosNuevos
                        ]
                    );


                if (!$auditoriaRegistrada) {

                    error_log(
                        "SIGENMUNI: el estado del empleado ID "
                        . $id
                        . " fue actualizado junto con su historial laboral, "
                        . "pero no se pudo registrar la auditoría."
                    );
                }


                $urlDestino =
                    sigenmuniUrlRuta(
                        'empleados',
                        [
                            'ok' => 'estado'
                        ]
                    );


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
        | VISTA DE CONFIRMACIÓN
        |--------------------------------------------------------------------------
        */

        require __DIR__
            . '/../vista/empleado_estado.php';
    }


    /*
    |--------------------------------------------------------------------------
    | LISTADO DE CONCEPTOS POR EMPLEADO
    |--------------------------------------------------------------------------
    */

    public function conceptos()
    {
        $buscarEmpleado =
            trim(
                $_GET['buscar_empleado']
                ?? ''
            );


        $conceptoId =
            isset($_GET['concepto_id'])
                ? (int)$_GET['concepto_id']
                : 0;


        /*
        |--------------------------------------------------------------------------
        | FILTRO DE ESTADO / VIGENCIA
        |--------------------------------------------------------------------------
        */

        $estado =
            trim(
                $_GET['estado']
                ?? ''
            );


        $estadosPermitidos = [
            '',
            'vigente',
            'programado',
            'finalizado',
            'inactivo'
        ];


        if (
            !in_array(
                $estado,
                $estadosPermitidos,
                true
            )
        ) {
            $estado = '';
        }


        $mensaje = "";
        $tipo_mensaje = "";


        /*
        |--------------------------------------------------------------------------
        | MENSAJE TEMPORAL DE SESIÓN
        |--------------------------------------------------------------------------
        |
        | Se utiliza principalmente para mostrar errores producidos al intentar
        | activar o desactivar una asignación.
        |
        | De esta manera evitamos cortar la navegación con die() y mostramos
        | el problema dentro del listado, utilizando la alerta visual existente.
        |
        |--------------------------------------------------------------------------
        */

        if (
            isset($_SESSION['empleado_conceptos_flash'])
            &&
            is_array(
                $_SESSION['empleado_conceptos_flash']
            )
        ) {

            $mensaje =
                (string)(
                    $_SESSION['empleado_conceptos_flash']['mensaje']
                    ?? ''
                );


            $tipo_mensaje =
                (string)(
                    $_SESSION['empleado_conceptos_flash']['tipo']
                    ?? 'error'
                );


            unset(
                $_SESSION['empleado_conceptos_flash']
            );
        }


        /*
        |--------------------------------------------------------------------------
        | MENSAJES DE OPERACIONES CORRECTAS
        |--------------------------------------------------------------------------
        |
        | El mensaje temporal de error tiene prioridad sobre los parámetros GET.
        |
        |--------------------------------------------------------------------------
        */

        if (
            $mensaje === ''
            &&
            isset($_GET['ok'])
        ) {

            switch ($_GET['ok']) {

                case '1':
                case 'nuevo':

                    $mensaje =
                        "Asignación guardada correctamente.";

                    $tipo_mensaje =
                        "ok";

                    break;


                case '2':
                case 'editar':

                    $mensaje =
                        "Asignación actualizada correctamente.";

                    $tipo_mensaje =
                        "ok";

                    break;


                case '3':
                case 'estado':

                    $mensaje =
                        "Estado de la asignación actualizado correctamente.";

                    $tipo_mensaje =
                        "ok";

                    break;
            }
        }


        try {

            $conceptos =
                $this->modelo
                    ->obtenerConceptosActivos();


            $asignaciones =
                $this->modelo
                    ->listarConceptosAsignados(
                        $buscarEmpleado,
                        $conceptoId,
                        $estado
                    );


            require __DIR__
                . '/../vista/empleado_conceptos.php';


        } catch (Exception $e) {

            die(
                "Error al cargar los conceptos por empleado: "
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
    | NUEVA ASIGNACIÓN DE CONCEPTO
    |--------------------------------------------------------------------------
    */

    public function nuevoConcepto()
    {
        $mensaje = "";
        $tipo_mensaje = "error";


        /*
        |--------------------------------------------------------------------------
        | DATOS INICIALES
        |--------------------------------------------------------------------------
        */

        $datosAsignacion = [

            'empleado_id' => '',
            'concepto_id' => '',

            'monto_manual' => '0.00',
            'porcentaje_manual' => '0.00',
            'cantidad' => '1.00',

            'fecha_desde' => '',
            'fecha_hasta' => '',

            'observacion' => ''
        ];


        try {

            /*
            |--------------------------------------------------------------------------
            | COMBOS
            |--------------------------------------------------------------------------
            */

            $empleadosDisponibles =
                $this->modelo
                    ->obtenerEmpleadosActivosParaConceptos();


            $conceptosDisponibles =
                $this->modelo
                    ->obtenerConceptosActivos();


            /*
            |--------------------------------------------------------------------------
            | POST
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


                $datosAsignacion =
                    $this->recibirDatosConceptoEmpleado();


                $datosGuardar =
                    $this->validarYNormalizarConceptoEmpleado(
                        $datosAsignacion,
                        null,
                        $conceptosDisponibles
                    );


                /*
                |--------------------------------------------------------------------------
                | DUPLICADO
                |--------------------------------------------------------------------------
                */

                if (
                    $this->modelo
                        ->existeAsignacionConceptoSolapada(
                            $datosGuardar['empleado_id'],
                            $datosGuardar['concepto_id'],
                            $datosGuardar['fecha_desde'],
                            $datosGuardar['fecha_hasta']
                        )
                ) {

                    throw new Exception(
                        "El empleado ya tiene una asignación activa de este concepto con un período de vigencia que se superpone. Edite la asignación existente o finalice su vigencia antes de crear una nueva."
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | GUARDAR
                |--------------------------------------------------------------------------
                */

                $idAsignacion =
                    $this->modelo
                        ->guardarConceptoEmpleado(
                            $datosGuardar
                        );


                if ($idAsignacion <= 0) {

                    throw new Exception(
                        "No se pudo guardar la asignación."
                    );
                }


                $urlDestino =
                    sigenmuniUrlRuta(
                        'empleado-conceptos',
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

            $mensaje =
                $e->getMessage();

            $tipo_mensaje =
                "error";
        }


        require __DIR__
            . '/../vista/empleado_concepto_nuevo.php';
    }


    /*
    |--------------------------------------------------------------------------
    | VER ASIGNACIÓN DE CONCEPTO
    |--------------------------------------------------------------------------
    */

    public function verConcepto()
    {
        $id =
            isset($_GET['id'])
                ? (int) $_GET['id']
                : 0;


        /*
        |--------------------------------------------------------------------------
        | VALIDAR ID
        |--------------------------------------------------------------------------
        */

        if ($id <= 0) {

            $urlDestino =
                sigenmuniUrlRuta(
                    'empleado-conceptos'
                );



            header(
                'Location: ' . $urlDestino
            );

            exit();
        }


        try {

            /*
            |--------------------------------------------------------------------------
            | OBTENER ASIGNACIÓN
            |--------------------------------------------------------------------------
            */

            $asignacion =
                $this->modelo
                    ->obtenerConceptoEmpleadoPorId(
                        $id
                    );


            if (!$asignacion) {

                $urlDestino =
                sigenmuniUrlRuta(
                    'empleado-conceptos'
                );



                header(
                    'Location: ' . $urlDestino
                );

                exit();
            }


            /*
            |--------------------------------------------------------------------------
            | VISTA
            |--------------------------------------------------------------------------
            */

            require __DIR__
                . '/../vista/empleado_concepto_ver.php';


        } catch (Exception $e) {

            die(
                "Error al cargar la asignación del concepto: "
                .
                htmlspecialchars(
                    $e->getMessage(),
                    ENT_QUOTES,
                    'UTF-8'
                )
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | EDITAR ASIGNACIÓN DE CONCEPTO
    |--------------------------------------------------------------------------
    */

    public function editarConcepto()
    {
        $id =
            isset($_GET['id'])
                ? (int) $_GET['id']
                : 0;


        $mensaje = "";
        $tipo_mensaje = "error";


        /*
        |--------------------------------------------------------------------------
        | VALIDAR ID
        |--------------------------------------------------------------------------
        */

        if ($id <= 0) {

            $urlDestino =
                sigenmuniUrlRuta(
                    'empleado-conceptos'
                );



            header(
                'Location: ' . $urlDestino
            );

            exit();
        }


        try {

            /*
            |--------------------------------------------------------------------------
            | OBTENER ASIGNACIÓN
            |--------------------------------------------------------------------------
            */

            $asignacion =
                $this->modelo
                    ->obtenerConceptoEmpleadoPorId(
                        $id
                    );


            if (!$asignacion) {

                $urlDestino =
                sigenmuniUrlRuta(
                    'empleado-conceptos'
                );



                header(
                    'Location: ' . $urlDestino
                );

                exit();
            }


            /*
            |--------------------------------------------------------------------------
            | BLOQUEAR EDICIÓN DE REGISTROS HISTÓRICOS FINALIZADOS
            |--------------------------------------------------------------------------
            |
            | Una asignación técnicamente activa cuya fecha_hasta ya pasó se
            | considera un registro histórico cerrado.
            |
            | Desde la gestión común no puede modificarse ningún dato.
            |
            | Si el mismo concepto vuelve a corresponder en un nuevo período,
            | debe crearse una NUEVA asignación.
            |
            | Esta validación también protege el acceso manual por URL.
            |
            */

            $zonaArgentina =
                new DateTimeZone(
                    'America/Argentina/Buenos_Aires'
                );


            $fechaHoy =
                (
                    new DateTime(
                        'now',
                        $zonaArgentina
                    )
                )->format(
                    'Y-m-d'
                );


            $fechaHastaAsignacion =
                trim(
                    (string)(
                        $asignacion['fecha_hasta']
                        ?? ''
                    )
                );


            $esRegistroHistoricoFinalizado =
                (int)(
                    $asignacion['activo']
                    ?? 0
                ) === 1
                &&
                $fechaHastaAsignacion !== ''
                &&
                $fechaHastaAsignacion < $fechaHoy;


            if ($esRegistroHistoricoFinalizado) {

                $urlDestino =
                    sigenmuniUrlRuta(
                        'empleado-conceptos/ver',
                        [
                            'id' => $id
                        ]
                    );



                header(
                    'Location: ' . $urlDestino
                );

                exit();
            }


            /*
            |--------------------------------------------------------------------------
            | IDs ORIGINALES
            |--------------------------------------------------------------------------
            */

            $empleadoIdOriginal =
                (int) $asignacion['empleado_id'];


            $conceptoIdOriginal =
                (int) $asignacion['concepto_id'];


            /*
            |--------------------------------------------------------------------------
            | EMPLEADOS DISPONIBLES
            |--------------------------------------------------------------------------
            |
            | El selector ofrece empleados activos para nuevas selecciones.
            | Si la asignación pertenece a un empleado actualmente inactivo, el
            | empleado original se agrega debajo para permitir conservar y editar
            | correctamente el registro histórico.
            |
            */

            $empleadosDisponibles =
                $this->modelo
                    ->obtenerEmpleadosActivosParaConceptos();


            /*
            |--------------------------------------------------------------------------
            | EMPLEADO ACTUAL INACTIVO
            |--------------------------------------------------------------------------
            */

            if (
                (int) $asignacion['empleado_activo']
                !== 1
            ) {

                $empleadosDisponibles[] = [

                    'id' =>
                        (int) $asignacion['empleado_id'],

                    'nro_legajo' =>
                        $asignacion['empleado_legajo'],

                    'apellido' =>
                        $asignacion['empleado_apellido'],

                    'nombre' =>
                        $asignacion['empleado_nombre']
                ];
            }


            /*
            |--------------------------------------------------------------------------
            | CONCEPTOS ACTIVOS
            |--------------------------------------------------------------------------
            */

            $conceptosDisponibles =
                $this->modelo
                    ->obtenerConceptosActivos();


            /*
            |--------------------------------------------------------------------------
            | CONCEPTO ACTUAL HISTÓRICO / NO DISPONIBLE
            |--------------------------------------------------------------------------
            |
            | obtenerConceptosActivos() devuelve solamente conceptos:
            |
            | - activos;
            | - asignables a empleados.
            |
            | Sin embargo, una asignación histórica puede estar asociada a un
            | concepto que actualmente está inactivo o dejó de ser asignable.
            | En ese caso lo agregamos al combo únicamente para conservar y
            | visualizar correctamente la relación existente.
            |
            |--------------------------------------------------------------------------
            */

            if (
                (int) $asignacion['concepto_activo'] !== 1
                ||
                (int) (
                    $asignacion['concepto_asignable_empleado']
                    ?? 0
                ) !== 1
            ) {

                $conceptosDisponibles[] = [

                    'id' =>
                        (int) $asignacion['concepto_id'],

                    'codigo' =>
                        $asignacion['concepto_codigo'],

                    'nombre' =>
                        $asignacion['concepto_nombre'],

                    'categoria' =>
                        $asignacion['concepto_categoria'],

                    'forma_calculo' =>
                        $asignacion['forma_calculo'],

                    'asignable_empleado' =>
                        (int) (
                            $asignacion['concepto_asignable_empleado']
                            ?? 0
                        )
                ];
            }


            /*
            |--------------------------------------------------------------------------
            | DATOS INICIALES
            |--------------------------------------------------------------------------
            */

            $datosAsignacion = [

                'id' =>
                    (int) $asignacion['id'],

                'empleado_id' =>
                    (int) $asignacion['empleado_id'],

                'concepto_id' =>
                    (int) $asignacion['concepto_id'],

                'monto_manual' =>
                    $asignacion['monto_manual'],

                'porcentaje_manual' =>
                    $asignacion['porcentaje_manual'],

                'cantidad' =>
                    $asignacion['cantidad'],

                'fecha_desde' =>
                    $asignacion['fecha_desde'],

                'fecha_hasta' =>
                    $asignacion['fecha_hasta'],

                'observacion' =>
                    $asignacion['observacion']
            ];


            /*
            |--------------------------------------------------------------------------
            | POST
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


                $datosAsignacion =
                    $this->recibirDatosConceptoEmpleado();


                $datosAsignacion['id'] =
                    $id;


                /*
                |--------------------------------------------------------------------------
                | VALIDAR
                |--------------------------------------------------------------------------
                */

                $datosActualizar =
                    $this->validarYNormalizarConceptoEmpleado(
                        $datosAsignacion,
                        $asignacion,
                        $conceptosDisponibles
                    );


                /*
                |--------------------------------------------------------------------------
                | DUPLICADO
                |--------------------------------------------------------------------------
                */

                if (
                    (int)(
                        $asignacion['activo']
                        ?? 0
                    ) === 1
                    &&
                    $this->modelo
                        ->existeAsignacionConceptoSolapada(
                            $datosActualizar['empleado_id'],
                            $datosActualizar['concepto_id'],
                            $datosActualizar['fecha_desde'],
                            $datosActualizar['fecha_hasta'],
                            $id
                        )
                ) {

                    throw new Exception(
                        "Ya existe otra asignación activa de este concepto para el empleado cuyo período de vigencia se superpone con el ingresado."
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | ACTUALIZAR
                |--------------------------------------------------------------------------
                */

                $actualizado =
                    $this->modelo
                        ->actualizarConceptoEmpleado(
                            $id,
                            $datosActualizar
                        );


                if (!$actualizado) {

                    throw new Exception(
                        "No se pudo actualizar la asignación."
                    );
                }


                $urlDestino =
                    sigenmuniUrlRuta(
                        'empleado-conceptos',
                        [
                            'ok' => 'editar'
                        ]
                    );



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

                $datosAsignacion = [

                    'id' =>
                        $id,

                    'empleado_id' =>
                        isset($_POST['empleado_id'])
                            ? (int) $_POST['empleado_id']
                            : 0,

                    'concepto_id' =>
                        isset($_POST['concepto_id'])
                            ? (int) $_POST['concepto_id']
                            : 0,

                    'monto_manual' =>
                        trim(
                            $_POST['monto_manual']
                            ?? '0.00'
                        ),

                    'porcentaje_manual' =>
                        trim(
                            $_POST['porcentaje_manual']
                            ?? '0.00'
                        ),

                    'cantidad' =>
                        trim(
                            $_POST['cantidad']
                            ?? '1.00'
                        ),

                    'fecha_desde' =>
                        trim(
                            $_POST['fecha_desde']
                            ?? ''
                        ),

                    'fecha_hasta' =>
                        trim(
                            $_POST['fecha_hasta']
                            ?? ''
                        ),

                    'observacion' =>
                        trim(
                            $_POST['observacion']
                            ?? ''
                        )
                ];
            }
        }


        /*
        |--------------------------------------------------------------------------
        | ASEGURAR VARIABLES
        |--------------------------------------------------------------------------
        */

        if (!isset($datosAsignacion)) {

            $datosAsignacion = [];
        }


        if (!isset($empleadoIdOriginal)) {

            $empleadoIdOriginal =
                isset($datosAsignacion['empleado_id'])
                    ? (int) $datosAsignacion['empleado_id']
                    : 0;
        }


        if (!isset($conceptoIdOriginal)) {

            $conceptoIdOriginal =
                isset($datosAsignacion['concepto_id'])
                    ? (int) $datosAsignacion['concepto_id']
                    : 0;
        }


        require __DIR__
            . '/../vista/empleado_concepto_editar.php';
    }


    /*
    |--------------------------------------------------------------------------
    | CAMBIAR ESTADO DE CONCEPTO POR EMPLEADO
    |--------------------------------------------------------------------------
    */

    public function estadoConcepto()
    {
        /*
        |--------------------------------------------------------------------------
        | FUNCIÓN LOCAL PARA VOLVER AL LISTADO
        |--------------------------------------------------------------------------
        |
        | Permite redirigir tanto por Router como por compatibilidad con la
        | entrada antigua mientras termina la migración del módulo.
        |
        |--------------------------------------------------------------------------
        */

        $volverAlListado =
            function (
                $mensaje = '',
                $tipo = 'error',
                $ok = ''
            ) {

                /*
                |------------------------------------------------------------------
                | MENSAJE TEMPORAL
                |------------------------------------------------------------------
                */

                if ($mensaje !== '') {

                    $_SESSION['empleado_conceptos_flash'] = [

                        'mensaje' =>
                            $mensaje,

                        'tipo' =>
                            $tipo
                    ];
                }


                /*
                |------------------------------------------------------------------
                | URL DESTINO
                |------------------------------------------------------------------
                */

                $parametros = [];


                if ($ok !== '') {

                    $parametros['ok'] =
                        $ok;
                }


                $urlDestino =
                    sigenmuniUrlRuta(
                        'empleado-conceptos',
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
            http_response_code(405);

            header(
                'Content-Type: text/plain; charset=UTF-8'
            );

            echo 'Método no permitido.';

            return;
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

            $volverAlListado(
                "La sesión del formulario no es válida o expiró. "
                . "Recargue la página e inténtelo nuevamente.",
                'error'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | DATOS
        |--------------------------------------------------------------------------
        */

        $id =
            isset($_POST['id'])
                ? (int)$_POST['id']
                : 0;


        $nuevoEstado =
            isset($_POST['estado'])
                ? (int)$_POST['estado']
                : -1;


        /*
        |--------------------------------------------------------------------------
        | VALIDAR ID Y ESTADO SOLICITADO
        |--------------------------------------------------------------------------
        */

        if (
            $id <= 0
            ||
            !in_array(
                $nuevoEstado,
                [0, 1],
                true
            )
        ) {

            $volverAlListado(
                "No se pudo procesar el cambio de estado porque los datos enviados no son válidos.",
                'error'
            );
        }


        try {

            /*
            |--------------------------------------------------------------------------
            | OBTENER ASIGNACIÓN
            |--------------------------------------------------------------------------
            */

            $asignacion =
                $this->modelo
                    ->obtenerConceptoEmpleadoPorId(
                        $id
                    );


            if (!$asignacion) {

                $volverAlListado(
                    "La asignación seleccionada no existe o ya no se encuentra disponible.",
                    'error'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | ESTADO ACTUAL
            |--------------------------------------------------------------------------
            */

            $estadoActual =
                (int)$asignacion['activo'];


            /*
            |--------------------------------------------------------------------------
            | OPERACIÓN IDEMPOTENTE
            |--------------------------------------------------------------------------
            |
            | El formulario envía explícitamente el estado deseado.
            | De esta manera un doble envío o una pantalla desactualizada no
            | invierte accidentalmente el estado del registro.
            |
            |--------------------------------------------------------------------------
            */

            if ($estadoActual === $nuevoEstado) {

                $volverAlListado(
                    '',
                    'ok',
                    'estado'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | SI SE VA A ACTIVAR
            |--------------------------------------------------------------------------
            */

            if ($nuevoEstado === 1) {

                /*
                |------------------------------------------------------------------
                | VALIDAR SOLAPAMIENTOS
                |------------------------------------------------------------------
                */

                if (
                    $this->modelo
                        ->existeAsignacionConceptoSolapada(
                            (int)$asignacion['empleado_id'],
                            (int)$asignacion['concepto_id'],
                            $asignacion['fecha_desde'],
                            $asignacion['fecha_hasta'] ?? null,
                            $id
                        )
                ) {
                    throw new Exception(
                        "Ya existe otra asignación activa del mismo concepto "
                        . "para este empleado con un período de vigencia superpuesto."
                    );
                }


                /*
                |------------------------------------------------------------------
                | VALIDAR EMPLEADO ACTIVO
                |------------------------------------------------------------------
                */

                if (
                    !$this->modelo
                        ->empleadoActivoExiste(
                            (int)$asignacion['empleado_id']
                        )
                ) {
                    throw new Exception(
                        "El empleado se encuentra inactivo."
                    );
                }


                /*
                |------------------------------------------------------------------
                | VALIDAR CONCEPTO ACTIVO
                |------------------------------------------------------------------
                */

                if (
                    !$this->modelo
                        ->conceptoActivoExiste(
                            (int)$asignacion['concepto_id']
                        )
                ) {
                    throw new Exception(
                        "El concepto se encuentra inactivo o ya no está "
                        . "habilitado para asignación a empleados."
                    );
                }
            }


            /*
            |--------------------------------------------------------------------------
            | CAMBIAR ESTADO
            |--------------------------------------------------------------------------
            |
            | El modelo realiza además las validaciones de período laboral.
            |
            |--------------------------------------------------------------------------
            */

            $actualizado =
                $this->modelo
                    ->cambiarEstadoConceptoEmpleado(
                        $id,
                        $nuevoEstado
                    );


            if (!$actualizado) {
                throw new Exception(
                    "No se pudo actualizar el estado de la asignación."
                );
            }


            /*
            |--------------------------------------------------------------------------
            | OPERACIÓN CORRECTA
            |--------------------------------------------------------------------------
            */

            $volverAlListado(
                '',
                'ok',
                'estado'
            );


        } catch (Exception $e) {

            /*
            |--------------------------------------------------------------------------
            | ERROR DE NEGOCIO
            |--------------------------------------------------------------------------
            |
            | Ya no se utiliza die().
            |
            | El mensaje se guarda temporalmente en sesión y se vuelve al listado,
            | donde se muestra dentro de la alerta roja existente.
            |
            |--------------------------------------------------------------------------
            */

            $accion =
                $nuevoEstado === 1
                    ? 'activar'
                    : 'desactivar';


            $mensajeError =
                "No se pudo "
                . $accion
                . " la asignación. "
                . trim(
                    $e->getMessage()
                );


            $volverAlListado(
                $mensajeError,
                'error'
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | RECIBIR DATOS DE CONCEPTO POR EMPLEADO
    |--------------------------------------------------------------------------
    */

    private function recibirDatosConceptoEmpleado()
    {
        return [

            'empleado_id' =>
                isset($_POST['empleado_id'])
                    ? (int) $_POST['empleado_id']
                    : 0,

            'concepto_id' =>
                isset($_POST['concepto_id'])
                    ? (int) $_POST['concepto_id']
                    : 0,

            'monto_manual' =>
                trim(
                    $_POST['monto_manual']
                    ?? '0'
                ),

            'porcentaje_manual' =>
                trim(
                    $_POST['porcentaje_manual']
                    ?? '0'
                ),

            'cantidad' =>
                trim(
                    $_POST['cantidad']
                    ?? '1'
                ),

            'fecha_desde' =>
                trim(
                    $_POST['fecha_desde']
                    ?? ''
                ),

            'fecha_hasta' =>
                trim(
                    $_POST['fecha_hasta']
                    ?? ''
                ),

            'observacion' =>
                trim(
                    $_POST['observacion']
                    ?? ''
                )
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDAR Y NORMALIZAR CONCEPTO POR EMPLEADO
    |--------------------------------------------------------------------------
    */

    private function validarYNormalizarConceptoEmpleado(
        $datos,
        $asignacionOriginal = null,
        $conceptosDisponibles = []
    ) {
        /*
        |--------------------------------------------------------------------------
        | EMPLEADO
        |--------------------------------------------------------------------------
        */

        $empleadoId =
            (int)($datos['empleado_id'] ?? 0);


        if ($empleadoId <= 0) {

            throw new Exception(
                "Debe seleccionar un empleado."
            );
        }


        /*
        |--------------------------------------------------------------------------
        | EMPLEADO ACTIVO / EMPLEADO HISTÓRICO
        |--------------------------------------------------------------------------
        |
        | NUEVA ASIGNACIÓN:
        |     el empleado debe estar actualmente activo.
        |
        | EDICIÓN:
        |     se permite conservar el empleado original aunque actualmente esté
        |     inactivo, porque puede tratarse de una asignación histórica.
        |
        |     Si durante la edición se cambia a otro empleado, ese nuevo empleado
        |     sí debe estar actualmente activo.
        |
        */

        $esEdicion =
            is_array(
                $asignacionOriginal
            );


        $esEmpleadoOriginal =
            $esEdicion
            &&
            $empleadoId
            ===
            (int)(
                $asignacionOriginal['empleado_id']
                ?? 0
            );


        if (
            !$esEmpleadoOriginal
            &&
            !$this->modelo
                ->empleadoActivoExiste(
                    $empleadoId
                )
        ) {

            throw new Exception(
                $esEdicion
                    ? "El nuevo empleado seleccionado no existe o está inactivo."
                    : "El empleado seleccionado no existe o está inactivo."
            );
        }


        /*
        |--------------------------------------------------------------------------
        | CONCEPTO
        |--------------------------------------------------------------------------
        */

        $conceptoId =
            (int)($datos['concepto_id'] ?? 0);


        if ($conceptoId <= 0) {

            throw new Exception(
                "Debe seleccionar un concepto."
            );
        }


        $conceptoAsignableActivo =
            $this->modelo
                ->conceptoActivoExiste(
                    $conceptoId
                );


        /*
        |--------------------------------------------------------------------------
        | CONCEPTO HISTÓRICO EN EDICIÓN
        |--------------------------------------------------------------------------
        |
        | Una asignación existente puede conservar su concepto original aunque
        | actualmente:
        |
        | - el concepto esté inactivo; o
        | - haya dejado de ser asignable a empleados.
        |
        | Esto permite corregir datos históricos sin obligar a reactivar una
        | definición de concepto que ya no corresponde usar.
        |
        | Si se cambia a OTRO concepto, el nuevo sí debe estar:
        |
        | - activo = 1;
        | - asignable_empleado = 1.
        |
        | Para una NUEVA asignación se mantienen esas mismas exigencias.
        |
        */

        $esConceptoOriginalPermitido =
            is_array(
                $asignacionOriginal
            )
            &&
            $conceptoId
            ===
            (int)(
                $asignacionOriginal['concepto_id']
                ?? 0
            );


        if (
            !$conceptoAsignableActivo
            &&
            !$esConceptoOriginalPermitido
        ) {

            throw new Exception(
                "El concepto seleccionado no existe, está inactivo o no está habilitado para asignación a empleados."
            );
        }


        /*
        |--------------------------------------------------------------------------
        | OBTENER DEFINICIÓN DEL CONCEPTO
        |--------------------------------------------------------------------------
        |
        | Ya tenemos esta lista cargada para los combos. La reutilizamos para
        | conocer:
        |
        | - código;
        | - categoría;
        | - forma de cálculo.
        |
        | De esta forma la validación del servidor aplica exactamente la misma
        | regla que la vista y no depende de datos enviados por JavaScript.
        |
        */

        if (
            !is_array($conceptosDisponibles)
            ||
            empty($conceptosDisponibles)
        ) {

            $conceptosDisponibles =
                $this->modelo
                    ->obtenerConceptosActivos();
        }


        $conceptoSeleccionado =
            null;


        foreach (
            $conceptosDisponibles
            as
            $conceptoDisponible
        ) {

            if (
                (int)(
                    $conceptoDisponible['id']
                    ?? 0
                )
                ===
                $conceptoId
            ) {

                $conceptoSeleccionado =
                    $conceptoDisponible;

                break;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | FALLBACK PARA CONCEPTO HISTÓRICO
        |--------------------------------------------------------------------------
        */

        if (
            $conceptoSeleccionado === null
            &&
            $esConceptoOriginalPermitido
            &&
            is_array($asignacionOriginal)
        ) {

            $conceptoSeleccionado = [

                'id' =>
                    $conceptoId,

                'codigo' =>
                    $asignacionOriginal['concepto_codigo']
                    ?? '',

                'categoria' =>
                    $asignacionOriginal['concepto_categoria']
                    ?? '',

                'forma_calculo' =>
                    $asignacionOriginal['forma_calculo']
                    ?? '',

                'asignable_empleado' =>
                    $asignacionOriginal[
                        'concepto_asignable_empleado'
                    ]
                    ?? 0
            ];
        }


        if ($conceptoSeleccionado === null) {

            throw new Exception(
                "No se pudo obtener la configuración del concepto seleccionado."
            );
        }


        $codigoConcepto =
            (string)(
                $conceptoSeleccionado['codigo']
                ?? ''
            );


        $categoriaConcepto =
            strtoupper(
                trim(
                    (string)(
                        $conceptoSeleccionado['categoria']
                        ?? ''
                    )
                )
            );


        $formaCalculo =
            strtoupper(
                trim(
                    (string)(
                        $conceptoSeleccionado['forma_calculo']
                        ?? ''
                    )
                )
            );


        /*
        |--------------------------------------------------------------------------
        | COMPATIBILIDAD CON CONCEPTOS FIJO HISTÓRICOS
        |--------------------------------------------------------------------------
        |
        | FIJO ya no forma parte de la nueva arquitectura.
        | Si todavía existe un concepto histórico FIJO, se trata como MANUAL.
        |
        */

        if ($formaCalculo === 'FIJO') {

            $formaCalculo =
                'MANUAL';
        }


        /*
        |--------------------------------------------------------------------------
        | BLOQUEAR CONCEPTOS QUE NO CORRESPONDEN A ASIGNACIÓN INDIVIDUAL
        |--------------------------------------------------------------------------
        |
        | TABLA_CATEGORIA:
        |     101, 102 y 104 se administran desde Gestión de Conceptos.
        |
        | FORMULA:
        |     se calcula automáticamente.
        |
        */

        if (
            $formaCalculo === 'TABLA_CATEGORIA'
            ||
            $formaCalculo === 'FORMULA'
        ) {

            throw new Exception(
                "El concepto "
                . (
                    $codigoConcepto !== ''
                        ? $codigoConcepto
                        : $conceptoId
                )
                . " no admite carga individual por empleado."
            );
        }


        /*
        |--------------------------------------------------------------------------
        | MONTO MANUAL
        |--------------------------------------------------------------------------
        */

        $montoTexto =
            trim(
                (string)(
                    $datos['monto_manual']
                    ?? '0'
                )
            );


        if (
            $montoTexto === ''
            ||
            !is_numeric(
                $montoTexto
            )
        ) {

            throw new Exception(
                "El monto manual debe ser un valor numérico."
            );
        }


        $montoManual =
            round(
                (float)$montoTexto,
                2
            );


        if ($montoManual < 0) {

            throw new Exception(
                "El monto manual no puede ser negativo."
            );
        }


        /*
        |--------------------------------------------------------------------------
        | PORCENTAJE MANUAL
        |--------------------------------------------------------------------------
        */

        $porcentajeTexto =
            trim(
                (string)(
                    $datos['porcentaje_manual']
                    ?? '0'
                )
            );


        if (
            $porcentajeTexto === ''
            ||
            !is_numeric(
                $porcentajeTexto
            )
        ) {

            throw new Exception(
                "El porcentaje manual debe ser un valor numérico."
            );
        }


        $porcentajeManual =
            round(
                (float)$porcentajeTexto,
                4
            );


        if ($porcentajeManual < 0) {

            throw new Exception(
                "El porcentaje manual no puede ser negativo."
            );
        }


        /*
        |--------------------------------------------------------------------------
        | CANTIDAD
        |--------------------------------------------------------------------------
        */

        $cantidadTexto =
            trim(
                (string)(
                    $datos['cantidad']
                    ?? '1'
                )
            );


        if (
            $cantidadTexto === ''
            ||
            !is_numeric(
                $cantidadTexto
            )
        ) {

            throw new Exception(
                "La cantidad debe ser un valor numérico."
            );
        }


        $cantidad =
            (float)$cantidadTexto;


        if ($cantidad <= 0) {

            throw new Exception(
                "La cantidad debe ser mayor a cero."
            );
        }


        /*
        |--------------------------------------------------------------------------
        | REGLA SEGÚN TIPO DE CONCEPTO
        |--------------------------------------------------------------------------
        |
        | PRIORIDAD:
        |
        | 1. ASIGNACION_FAMILIAR
        |       monto por unidad > 0
        |       cantidad > 0
        |       porcentaje = 0
        |
        | 2. MANUAL
        |       monto > 0
        |       porcentaje = 0
        |       cantidad = 1
        |
        | 3. PORCENTAJE
        |       porcentaje > 0
        |       monto = 0
        |       cantidad = 1
        |
        */

        if (
            $categoriaConcepto
            ===
            'ASIGNACION_FAMILIAR'
        ) {

            if ($montoManual <= 0) {

                throw new Exception(
                    "Para una asignación familiar debe ingresar un monto por unidad mayor a cero."
                );
            }


            $porcentajeManual =
                0;


        } elseif (
            $formaCalculo
            ===
            'MANUAL'
        ) {

            if ($montoManual <= 0) {

                throw new Exception(
                    "Para un concepto MANUAL debe ingresar un monto mayor a cero."
                );
            }


            $porcentajeManual =
                0;

            $cantidad =
                1;


        } elseif (
            $formaCalculo
            ===
            'PORCENTAJE'
        ) {

            if ($porcentajeManual <= 0) {

                throw new Exception(
                    "Para un concepto PORCENTAJE debe ingresar un porcentaje mayor a cero."
                );
            }

            $montoManual =
                0;

            $cantidad =
                1;


        } else {

            throw new Exception(
                "El concepto seleccionado debe estar configurado como MANUAL, PORCENTAJE o ASIGNACION_FAMILIAR para poder asignarse a un empleado."
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FECHA DESDE
        |--------------------------------------------------------------------------
        */

        if (
            empty(
                $datos['fecha_desde']
            )
        ) {

            throw new Exception(
                "Debe ingresar la fecha desde."
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

        $fechaHastaTexto =
            trim(
                (string)(
                    $datos['fecha_hasta']
                    ?? ''
                )
            );


        if (
            $fechaHastaTexto !== ''
            &&
            !$this->fechaValida(
                $fechaHastaTexto
            )
        ) {

            throw new Exception(
                "La fecha hasta no es válida."
            );
        }


        if (
            $fechaHastaTexto !== ''
            &&
            $fechaHastaTexto
            <
            $datos['fecha_desde']
        ) {

            throw new Exception(
                "La fecha hasta no puede ser anterior a la fecha desde."
            );
        }


        /*
        |--------------------------------------------------------------------------
        | NORMALIZAR
        |--------------------------------------------------------------------------
        */

        $fechaHasta =
            $fechaHastaTexto === ''
                ? null
                : $fechaHastaTexto;


        /*
        |--------------------------------------------------------------------------
        | VIGENCIA DENTRO DEL HISTORIAL LABORAL
        |--------------------------------------------------------------------------
        |
        | La asignación debe quedar completamente contenida dentro de un único
        | período laboral continuo del empleado.
        |
        | Esta validación se repite también en EmpleadoModelo como defensa del
        | servidor frente a requests manipulados o llamadas directas al modelo.
        |
        */

        $this->modelo
            ->validarVigenciaConceptoEnPeriodoLaboral(
                $empleadoId,
                $datos['fecha_desde'],
                $fechaHasta
            );


        $observacionTexto =
            trim(
                (string)(
                    $datos['observacion']
                    ?? ''
                )
            );


        $observacion =
            $observacionTexto === ''
                ? null
                : $observacionTexto;


        return [

            'empleado_id' =>
                $empleadoId,

            'concepto_id' =>
                $conceptoId,

            'monto_manual' =>
                $montoManual,

            'porcentaje_manual' =>
                $porcentajeManual,

            'cantidad' =>
                $cantidad,

            'fecha_desde' =>
                $datos['fecha_desde'],

            'fecha_hasta' =>
                $fechaHasta,

            'activo' =>
                1,

            'observacion' =>
                $observacion
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | DATOS ADMINISTRATIVOS PARA AUDITORÍA
    |--------------------------------------------------------------------------
    |
    | Además de los IDs internos se guarda una fotografía descriptiva de los
    | catálogos seleccionados. De esta forma, el historial puede mostrar:
    |
    | - Institución: nombre
    | - Unidad de Organización: nombre + CUIT
    | - Situación: nombre
    | - Escalafón: nombre
    | - Categoría: código + nombre
    |
    | sin depender de que esos nombres continúen iguales en el futuro.
    |
    |--------------------------------------------------------------------------
    */

    private function armarDatosAdministrativosAuditoria(
        $institucionId,
        $oficinaId,
        $situacionId,
        $escalafonId,
        $categoriaId,
        array $instituciones,
        array $oficinas,
        array $situaciones,
        array $escalafones,
        array $categorias
    ) {
        $institucion =
            $this->buscarItemPorId(
                $instituciones,
                $institucionId
            );


        $oficina =
            $this->buscarItemPorId(
                $oficinas,
                $oficinaId
            );


        $situacion =
            $this->buscarItemPorId(
                $situaciones,
                $situacionId
            );


        $escalafon =
            $escalafonId !== null
                ?
                $this->buscarItemPorId(
                    $escalafones,
                    $escalafonId
                )
                :
                null;


        $categoria =
            $this->buscarItemPorId(
                $categorias,
                $categoriaId
            );


        $categoriaDescripcion =
            null;


        if ($categoria) {

            $codigoCategoria =
                $categoria['codigo']
                ?? null;


            $nombreCategoria =
                $categoria['nombre']
                ?? '';


            $categoriaDescripcion =
                (
                    $codigoCategoria !== null
                    &&
                    $codigoCategoria !== ''
                )
                    ?
                    $codigoCategoria
                    . ' - '
                    . $nombreCategoria
                    :
                    $nombreCategoria;
        }


        return [

            'institucion_id' =>
                (int)$institucionId,

            'institucion' =>
                $institucion['nombre']
                ?? null,

            'oficina_id' =>
                (int)$oficinaId,

            'unidad_organizacion' =>
                $oficina['nombre']
                ?? null,

            'unidad_organizacion_cuit' =>
                !empty(
                    $oficina['cuit']
                    ?? ''
                )
                    ?
                    $this->formatearCuitAuditoria(
                        $oficina['cuit']
                    )
                    :
                    null,

            'situacion_id' =>
                (int)$situacionId,

            'situacion' =>
                $situacion['nombre']
                ?? null,

            'escalafon_id' =>
                $escalafonId !== null
                    ?
                    (int)$escalafonId
                    :
                    null,

            'escalafon' =>
                $escalafonId !== null
                    ?
                    (
                        $escalafon['nombre']
                        ?? null
                    )
                    :
                    'No corresponde',

            'categoria_id' =>
                (int)$categoriaId,

            'categoria' =>
                $categoriaDescripcion
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | DATOS ADMINISTRATIVOS ANTERIORES
    |--------------------------------------------------------------------------
    |
    | obtenerPorId() ya devuelve los nombres relacionados del empleado antes
    | de la edición, por lo que se utilizan directamente como fotografía
    | histórica anterior.
    |
    |--------------------------------------------------------------------------
    */

    private function armarDatosAdministrativosDesdeEmpleado(
        array $empleado
    ) {
        $categoriaDescripcion =
            $empleado['categoria']
            ?? null;


        if (
            isset(
                $empleado['categoria_codigo']
            )
            &&
            $empleado['categoria_codigo'] !== null
            &&
            $empleado['categoria_codigo'] !== ''
            &&
            $categoriaDescripcion !== null
        ) {

            $categoriaDescripcion =
                $empleado['categoria_codigo']
                . ' - '
                . $categoriaDescripcion;
        }


        return [

            'institucion_id' =>
                isset(
                    $empleado['institucion_id']
                )
                    ?
                    (int)$empleado['institucion_id']
                    :
                    null,

            'institucion' =>
                $empleado['institucion']
                ?? null,

            'oficina_id' =>
                isset(
                    $empleado['oficina_id']
                )
                    ?
                    (int)$empleado['oficina_id']
                    :
                    null,

            'unidad_organizacion' =>
                $empleado['oficina']
                ?? null,

            'unidad_organizacion_cuit' =>
                !empty(
                    $empleado['oficina_cuit']
                    ?? ''
                )
                    ?
                    $this->formatearCuitAuditoria(
                        $empleado['oficina_cuit']
                    )
                    :
                    null,

            'situacion_id' =>
                isset(
                    $empleado['situacion_id']
                )
                    ?
                    (int)$empleado['situacion_id']
                    :
                    null,

            'situacion' =>
                $empleado['situacion']
                ?? null,

            'escalafon_id' =>
                isset(
                    $empleado['escalafon_id']
                )
                &&
                $empleado['escalafon_id'] !== null
                    ?
                    (int)$empleado['escalafon_id']
                    :
                    null,

            'escalafon' =>
                !empty(
                    $empleado['escalafon']
                    ?? ''
                )
                    ?
                    $empleado['escalafon']
                    :
                    'No corresponde',

            'categoria_id' =>
                isset(
                    $empleado['categoria_id']
                )
                    ?
                    (int)$empleado['categoria_id']
                    :
                    null,

            'categoria' =>
                $categoriaDescripcion
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | BUSCAR ELEMENTO DE CATÁLOGO POR ID
    |--------------------------------------------------------------------------
    */

    private function buscarItemPorId(
        array $items,
        $id
    ) {
        if ($id === null) {

            return null;
        }


        $id =
            (int)$id;


        foreach ($items as $item) {

            if (
                (int)(
                    $item['id']
                    ?? 0
                )
                ===
                $id
            ) {

                return $item;
            }
        }


        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | FORMATEAR CUIT PARA AUDITORÍA
    |--------------------------------------------------------------------------
    */

    private function formatearCuitAuditoria(
        $cuit
    ) {
        $cuit =
            preg_replace(
                '/\D+/',
                '',
                (string)$cuit
            );


        if (
            strlen(
                $cuit
            )
            !==
            11
        ) {

            return (string)$cuit;
        }


        return
            substr(
                $cuit,
                0,
                2
            )
            . '-'
            . substr(
                $cuit,
                2,
                8
            )
            . '-'
            . substr(
                $cuit,
                10,
                1
            );
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