<?php

class EmpleadoModelo
{
    private $conexion;


    /*
    |--------------------------------------------------------------------------
    | CONSTRUCTOR
    |--------------------------------------------------------------------------
    */

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }


    /*
    |--------------------------------------------------------------------------
    | LISTADO DE EMPLEADOS
    |--------------------------------------------------------------------------
    */

    public function listar($busqueda = "")
    {
        $busqueda =
            trim(
                (string)$busqueda
            );


        $sql = "
            SELECT
                e.id,
                e.nro_legajo,
                e.apellido,
                e.nombre,
                e.dni,
                e.cuil,
                e.telefono,
                e.email,
                e.fecha_alta,
                e.fecha_baja,
                e.fecha_inactivo,
                e.activo,

                c.nombre AS categoria,

                o.nombre AS oficina,
                o.cuit AS oficina_cuit,

                s.nombre AS situacion

            FROM empleado e

            INNER JOIN categoria c
                ON e.categoria_id = c.id

            INNER JOIN oficina o
                ON e.oficina_id = o.id

            INNER JOIN situacion s
                ON e.situacion_id = s.id

            WHERE 1 = 1
        ";


        $tipos =
            "";


        $parametros =
            [];


        /*
        |--------------------------------------------------------------------------
        | BÚSQUEDA
        |--------------------------------------------------------------------------
        |
        | Si el usuario escribe solamente números:
        |
        | - El legajo se busca SIEMPRE de forma exacta.
        | - Si tiene 7 u 8 dígitos, también puede coincidir con DNI exacto.
        | - Si tiene 11 dígitos, también puede coincidir con CUIL exacto.
        |
        | Esto evita que buscar legajo "1" devuelva, por ejemplo:
        |
        | 11
        | 12
        | 21
        |
        | Si se escribe texto:
        |
        | - apellido
        | - nombre
        | - apellido + nombre
        | - nombre + apellido
        | - email
        |
        | se buscan por coincidencia parcial.
        |
        |--------------------------------------------------------------------------
        */

        if ($busqueda !== "") {

            if (ctype_digit($busqueda)) {

                /*
                |--------------------------------------------------------------------------
                | BÚSQUEDA NUMÉRICA
                |--------------------------------------------------------------------------
                */

                $longitud =
                    strlen(
                        $busqueda
                    );


                if (
                    $longitud === 7
                    ||
                    $longitud === 8
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | LEGAJO EXACTO O DNI EXACTO
                    |--------------------------------------------------------------------------
                    */

                    $sql .= "
                        AND (
                            CAST(e.nro_legajo AS CHAR) = ?
                            OR e.dni = ?
                        )
                    ";


                    $tipos .=
                        "ss";


                    $parametros[] =
                        $busqueda;


                    $parametros[] =
                        $busqueda;


                } elseif ($longitud === 11) {

                    /*
                    |--------------------------------------------------------------------------
                    | LEGAJO EXACTO O CUIL EXACTO
                    |--------------------------------------------------------------------------
                    */

                    $sql .= "
                        AND (
                            CAST(e.nro_legajo AS CHAR) = ?
                            OR e.cuil = ?
                        )
                    ";


                    $tipos .=
                        "ss";


                    $parametros[] =
                        $busqueda;


                    $parametros[] =
                        $busqueda;


                } else {

                    /*
                    |--------------------------------------------------------------------------
                    | LEGAJO EXACTO
                    |--------------------------------------------------------------------------
                    */

                    $sql .= "
                        AND CAST(e.nro_legajo AS CHAR) = ?
                    ";


                    $tipos .=
                        "s";


                    $parametros[] =
                        $busqueda;
                }


            } else {

                /*
                |--------------------------------------------------------------------------
                | BÚSQUEDA POR TEXTO
                |--------------------------------------------------------------------------
                */

                $sql .= "
                    AND (
                        e.apellido LIKE ?
                        OR e.nombre LIKE ?
                        OR CONCAT(
                            e.apellido,
                            ' ',
                            e.nombre
                        ) LIKE ?
                        OR CONCAT(
                            e.nombre,
                            ' ',
                            e.apellido
                        ) LIKE ?
                        OR e.email LIKE ?
                    )
                ";


                $like =
                    "%"
                    .
                    $busqueda
                    .
                    "%";


                $tipos .=
                    "sssss";


                $parametros[] =
                    $like;

                $parametros[] =
                    $like;

                $parametros[] =
                    $like;

                $parametros[] =
                    $like;

                $parametros[] =
                    $like;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | ORDEN
        |--------------------------------------------------------------------------
        */

        $sql .= "
            ORDER BY
                e.apellido ASC,
                e.nombre ASC
        ";


        /*
        |--------------------------------------------------------------------------
        | PREPARAR
        |--------------------------------------------------------------------------
        */

        $stmt =
            $this->conexion
                ->prepare(
                    $sql
                );


        if (!$stmt) {

            throw new Exception(
                "Error al preparar la consulta de empleados: "
                .
                $this->conexion->error
            );
        }


        /*
        |--------------------------------------------------------------------------
        | PARÁMETROS
        |--------------------------------------------------------------------------
        */

        if (!empty($parametros)) {

            $stmt->bind_param(
                $tipos,
                ...$parametros
            );
        }


        /*
        |--------------------------------------------------------------------------
        | EJECUTAR
        |--------------------------------------------------------------------------
        */

        if (!$stmt->execute()) {

            $error =
                $stmt->error;


            $stmt->close();


            throw new Exception(
                "Error al consultar empleados: "
                .
                $error
            );
        }


        /*
        |--------------------------------------------------------------------------
        | RESULTADOS
        |--------------------------------------------------------------------------
        */

        $resultado =
            $stmt->get_result();


        $empleados =
            [];


        while (
            $fila =
                $resultado->fetch_assoc()
        ) {

            $empleados[] =
                $fila;
        }


        $stmt->close();


        return $empleados;
    }


    /*
    |--------------------------------------------------------------------------
    | OBTENER EMPLEADO POR ID
    |--------------------------------------------------------------------------
    */

    public function obtenerPorId($id)
    {
        $sql = "
            SELECT
                e.*,
                i.nombre AS institucion,
                o.nombre AS oficina,
                o.cuit AS oficina_cuit,
                s.nombre AS situacion,
                es.nombre AS escalafon,
                c.codigo AS categoria_codigo,
                c.nombre AS categoria

            FROM empleado e

            INNER JOIN institucion i
                ON e.institucion_id = i.id

            INNER JOIN oficina o
                ON e.oficina_id = o.id

            INNER JOIN situacion s
                ON e.situacion_id = s.id

            LEFT JOIN escalafon es
                ON e.escalafon_id = es.id

            INNER JOIN categoria c
                ON e.categoria_id = c.id

            WHERE e.id = ?

            LIMIT 1
        ";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {

            throw new Exception(
                "Error al preparar la consulta del empleado: "
                . $this->conexion->error
            );
        }

        $stmt->bind_param(
            "i",
            $id
        );

        if (!$stmt->execute()) {

            $error = $stmt->error;

            $stmt->close();

            throw new Exception(
                "Error al consultar el empleado: "
                . $error
            );
        }

        $resultado = $stmt->get_result();

        $empleado = $resultado->fetch_assoc();

        $stmt->close();

        return $empleado;
    }


    /*
    |--------------------------------------------------------------------------
    | DATOS PARA COMBOS
    |--------------------------------------------------------------------------
    */

    public function obtenerInstituciones()
    {
        return $this->obtenerListadoSimple(
            "institucion"
        );
    }


    public function obtenerOficinas()
    {
        /*
        |--------------------------------------------------------------------------
        | UNIDADES DE ORGANIZACIÓN
        |--------------------------------------------------------------------------
        |
        | La tabla física continúa llamándose "oficina" y el campo relacionado
        | en empleado continúa siendo oficina_id para no romper relaciones
        | existentes. Visualmente, en el sistema se mostrará como
        | "Unidad de Organización".
        |
        | Se incluye el CUIT para poder mostrar en los formularios:
        |
        | Poder Ejecutivo - CUIT 30-67138210-9
        | Honorable Concejo Deliberante - CUIT 30-71225994-5
        |
        |--------------------------------------------------------------------------
        */

        $sql = "
            SELECT
                id,
                nombre,
                cuit
            FROM oficina
            ORDER BY nombre ASC
        ";


        $resultado =
            $this->conexion->query(
                $sql
            );


        if (!$resultado) {

            throw new Exception(
                "Error al cargar las Unidades de Organización: "
                . $this->conexion->error
            );
        }


        $unidadesOrganizacion =
            [];


        while (
            $fila =
                $resultado->fetch_assoc()
        ) {

            $unidadesOrganizacion[] = [

                'id' =>
                    (int)$fila['id'],

                'nombre' =>
                    $fila['nombre'],

                'cuit' =>
                    $fila['cuit']
            ];
        }


        return $unidadesOrganizacion;
    }


    public function obtenerSituaciones()
    {
        return $this->obtenerListadoSimple(
            "situacion"
        );
    }


    public function obtenerEscalafones()
    {
        /*
        |--------------------------------------------------------------------------
        | ESCALAFONES VÁLIDOS PARA CATEGORÍAS GENERALES
        |--------------------------------------------------------------------------
        |
        | Los cargos especiales (código 1000+) no utilizan escalafón.
        |
        | Para categorías generales se permiten únicamente:
        |
        | 8  - Administrativo y Técnico
        | 9  - Obrero y Maestranza
        | 10 - Jornalizado
        |
        |--------------------------------------------------------------------------
        */

        $sql = "
            SELECT
                id,
                nombre
            FROM escalafon
            WHERE id IN (8, 9, 10)
            ORDER BY
                CASE id
                    WHEN 8 THEN 1
                    WHEN 9 THEN 2
                    WHEN 10 THEN 3
                    ELSE 4
                END,
                nombre ASC
        ";


        $resultado =
            $this->conexion->query(
                $sql
            );


        if (!$resultado) {

            throw new Exception(
                "Error al cargar los escalafones: "
                . $this->conexion->error
            );
        }


        $escalafones = [];


        while (
            $fila =
                $resultado->fetch_assoc()
        ) {

            $escalafones[] = [

                'id' =>
                    (int)$fila['id'],

                'nombre' =>
                    $fila['nombre']
            ];
        }


        return $escalafones;
    }


    public function obtenerCategorias()
    {
        /*
        |--------------------------------------------------------------------------
        | CATEGORÍAS ACTIVAS PARA EMPLEADOS
        |--------------------------------------------------------------------------
        |
        | Orden funcional:
        |
        | 1. Cargos / categorías especiales (código 1000 en adelante).
        | 2. Categorías generales.
        |
        | Solo se ofrecen categorías activas para nuevas selecciones.
        |
        |--------------------------------------------------------------------------
        */

        $sql = "
            SELECT
                id,
                codigo,
                nombre
            FROM categoria
            WHERE activo = 1
            ORDER BY
                CASE
                    WHEN codigo >= 1000 THEN 1
                    ELSE 2
                END,
                codigo ASC,
                nombre ASC
        ";


        $resultado =
            $this->conexion->query(
                $sql
            );


        if (!$resultado) {

            throw new Exception(
                "Error al cargar las categorías: "
                . $this->conexion->error
            );
        }


        $categorias = [];


        while (
            $fila =
                $resultado->fetch_assoc()
        ) {

            $categorias[] = [

                'id' =>
                    (int)$fila['id'],

                'codigo' =>
                    (int)$fila['codigo'],

                'nombre' =>
                    $fila['nombre']
            ];
        }


        return $categorias;
    }


    /*
    |--------------------------------------------------------------------------
    | OBTENER CATEGORÍA POR ID PARA VALIDACIONES
    |--------------------------------------------------------------------------
    */

    public function obtenerCategoriaPorId($categoriaId)
    {
        $categoriaId =
            (int)$categoriaId;


        if ($categoriaId <= 0) {

            return null;
        }


        $sql = "
            SELECT
                id,
                codigo,
                nombre,
                activo
            FROM categoria
            WHERE id = ?
            LIMIT 1
        ";


        $stmt =
            $this->conexion->prepare(
                $sql
            );


        if (!$stmt) {

            throw new Exception(
                "Error al preparar la consulta de categoría: "
                . $this->conexion->error
            );
        }


        $stmt->bind_param(
            "i",
            $categoriaId
        );


        if (!$stmt->execute()) {

            $error =
                $stmt->error;


            $stmt->close();


            throw new Exception(
                "Error al consultar la categoría: "
                . $error
            );
        }


        $resultado =
            $stmt->get_result();


        $categoria =
            $resultado->fetch_assoc();


        $stmt->close();


        if (!$categoria) {

            return null;
        }


        return [

            'id' =>
                (int)$categoria['id'],

            'codigo' =>
                (int)$categoria['codigo'],

            'nombre' =>
                $categoria['nombre'],

            'activo' =>
                (int)$categoria['activo']
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDAR ESCALAFÓN
    |--------------------------------------------------------------------------
    */

    public function escalafonValido($escalafonId)
    {
        $escalafonId =
            (int)$escalafonId;


        return in_array(
            $escalafonId,
            [8, 9, 10],
            true
        );
    }


    /*
    |--------------------------------------------------------------------------
    | NORMALIZAR ESCALAFÓN SEGÚN CATEGORÍA
    |--------------------------------------------------------------------------
    |
    | Categoría especial (código >= 1000):
    |     escalafon_id = NULL.
    |
    | Categoría general:
    |     escalafon_id obligatorio y debe ser 8, 9 o 10.
    |
    |--------------------------------------------------------------------------
    */

    public function normalizarEscalafon(
        $categoriaId,
        $escalafonId
    ) {
        $categoria =
            $this->obtenerCategoriaPorId(
                $categoriaId
            );


        if (!$categoria) {

            throw new Exception(
                "La categoría seleccionada no existe."
            );
        }


        if (
            (int)$categoria['activo']
            !==
            1
        ) {

            throw new Exception(
                "La categoría seleccionada está inactiva."
            );
        }


        /*
        |--------------------------------------------------------------------------
        | CARGO ESPECIAL
        |--------------------------------------------------------------------------
        */

        if (
            (int)$categoria['codigo']
            >=
            1000
        ) {

            return null;
        }


        /*
        |--------------------------------------------------------------------------
        | CATEGORÍA GENERAL
        |--------------------------------------------------------------------------
        */

        $escalafonId =
            (int)$escalafonId;


        if (
            !$this->escalafonValido(
                $escalafonId
            )
        ) {

            throw new Exception(
                "Debe seleccionar un escalafón válido para la categoría."
            );
        }


        return $escalafonId;
    }


    /*
    |--------------------------------------------------------------------------
    | MÉTODO INTERNO PARA LISTADOS SIMPLES
    |--------------------------------------------------------------------------
    */

    private function obtenerListadoSimple($tabla)
    {
        $tablasPermitidas = [
            'institucion',
            'oficina',
            'situacion',
            'escalafon',
            'categoria'
        ];

        if (!in_array($tabla, $tablasPermitidas, true)) {

            throw new Exception(
                "Tabla no permitida."
            );
        }

        $sql = "
            SELECT
                id,
                nombre
            FROM {$tabla}
            ORDER BY nombre
        ";

        $resultado = $this->conexion->query($sql);

        if (!$resultado) {

            throw new Exception(
                "Error al cargar los datos de {$tabla}: "
                . $this->conexion->error
            );
        }

        $datos = [];

        while ($fila = $resultado->fetch_assoc()) {

            $datos[] = $fila;
        }

        return $datos;
    }


    /*
    |--------------------------------------------------------------------------
    | REFERENCIA DE LEGAJOS
    |--------------------------------------------------------------------------
    |
    | Devuelve:
    |
    | - ultimo_utilizado: mayor número de legajo registrado;
    | - proximo_sugerido: ultimo_utilizado + 1.
    |
    | Se consideran TODOS los empleados, activos e inactivos, porque un número de
    | legajo ya utilizado forma parte del historial y no debe sugerirse nuevamente.
    |
    | La sugerencia es solamente una ayuda visual. La validación de duplicados
    | continúa siendo obligatoria al guardar.
    |
    */

    public function obtenerReferenciaLegajos()
    {
        $sql = "
            SELECT
                COALESCE(
                    MAX(
                        CAST(
                            nro_legajo AS UNSIGNED
                        )
                    ),
                    0
                ) AS ultimo_utilizado

            FROM empleado
        ";


        $resultado =
            $this->conexion->query(
                $sql
            );


        if (!$resultado) {

            throw new Exception(
                "No se pudo obtener la referencia de legajos: "
                . $this->conexion->error
            );
        }


        $fila =
            $resultado->fetch_assoc();


        $ultimoUtilizado =
            max(
                0,
                (int)(
                    $fila['ultimo_utilizado']
                    ?? 0
                )
            );


        return [
            'ultimo_utilizado' =>
                $ultimoUtilizado,

            'proximo_sugerido' =>
                $ultimoUtilizado + 1
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDAR LEGAJO
    |--------------------------------------------------------------------------
    */

    public function existeLegajo(
        $nroLegajo,
        $idExcluir = null
    ) {
        return $this->existeCampo(
            'nro_legajo',
            $nroLegajo,
            $idExcluir
        );
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDAR DNI
    |--------------------------------------------------------------------------
    */

    public function existeDni(
        $dni,
        $idExcluir = null
    ) {
        return $this->existeCampo(
            'dni',
            $dni,
            $idExcluir
        );
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDAR CUIL
    |--------------------------------------------------------------------------
    */

    public function existeCuil(
        $cuil,
        $idExcluir = null
    ) {
        return $this->existeCampo(
            'cuil',
            $cuil,
            $idExcluir
        );
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDAR EMAIL
    |--------------------------------------------------------------------------
    */

    public function existeEmail(
        $email,
        $idExcluir = null
    ) {
        return $this->existeCampo(
            'email',
            $email,
            $idExcluir
        );
    }


    /*
    |--------------------------------------------------------------------------
    | MÉTODO INTERNO PARA VALIDAR DUPLICADOS
    |--------------------------------------------------------------------------
    */

    private function existeCampo(
        $campo,
        $valor,
        $idExcluir = null
    ) {
        $camposPermitidos = [
            'nro_legajo',
            'dni',
            'cuil',
            'email'
        ];

        if (!in_array($campo, $camposPermitidos, true)) {

            throw new Exception(
                "Campo de validación no permitido."
            );
        }


        if ($idExcluir === null) {

            $sql = "
                SELECT id
                FROM empleado
                WHERE {$campo} = ?
                LIMIT 1
            ";

            $stmt = $this->conexion->prepare(
                $sql
            );

            if (!$stmt) {

                throw new Exception(
                    "Error al preparar la validación: "
                    . $this->conexion->error
                );
            }

            $stmt->bind_param(
                "s",
                $valor
            );

        } else {

            $sql = "
                SELECT id
                FROM empleado
                WHERE {$campo} = ?
                  AND id <> ?
                LIMIT 1
            ";

            $stmt = $this->conexion->prepare(
                $sql
            );

            if (!$stmt) {

                throw new Exception(
                    "Error al preparar la validación: "
                    . $this->conexion->error
                );
            }

            $stmt->bind_param(
                "si",
                $valor,
                $idExcluir
            );
        }


        if (!$stmt->execute()) {

            $error = $stmt->error;

            $stmt->close();

            throw new Exception(
                "Error al validar los datos: "
                . $error
            );
        }

        $resultado = $stmt->get_result();

        $existe =
            $resultado->num_rows > 0;

        $stmt->close();

        return $existe;
    }


    /*
    |--------------------------------------------------------------------------
    | GUARDAR EMPLEADO
    |--------------------------------------------------------------------------
    */

    public function guardar($datos)
    {
        /*
        |--------------------------------------------------------------------------
        | ALTA DE EMPLEADO + PERÍODO LABORAL INICIAL
        |--------------------------------------------------------------------------
        |
        | El empleado y su primer período laboral se guardan dentro de la misma
        | transacción. Si falla cualquiera de las dos operaciones, no queda un
        | alta incompleta.
        |
        */

        $this->conexion->begin_transaction();

        try {

            $sql = "
                INSERT INTO empleado (
                    institucion_id,
                    oficina_id,
                    situacion_id,
                    escalafon_id,
                    categoria_id,
                    nro_legajo,
                    apellido,
                    nombre,
                    dni,
                    cuil,
                    fecha_alta,
                    fecha_baja,
                    telefono,
                    email,
                    domicilio,
                    observaciones,
                    activo
                )
                VALUES (
                    ?, ?, ?, ?, ?,
                    ?, ?, ?, ?, ?,
                    ?, ?, ?, ?, ?, ?, 1
                )
            ";


            $stmt =
                $this->conexion->prepare(
                    $sql
                );


            if (!$stmt) {

                throw new Exception(
                    "No se pudo preparar la consulta para guardar el empleado: "
                    . $this->conexion->error
                );
            }


            $institucionId =
                $datos['institucion_id'];

            $oficinaId =
                $datos['oficina_id'];

            $situacionId =
                $datos['situacion_id'];

            $categoriaId =
                (int)$datos['categoria_id'];


            $escalafonId =
                $this->normalizarEscalafon(
                    $categoriaId,
                    $datos['escalafon_id']
                    ?? null
                );


            $nroLegajo =
                $datos['nro_legajo'];

            $apellido =
                $datos['apellido'];

            $nombre =
                $datos['nombre'];

            $dni =
                $datos['dni'];

            $cuil =
                $datos['cuil'];

            $fechaAlta =
                trim(
                    (string)(
                        $datos['fecha_alta']
                        ?? ''
                    )
                );

            $fechaBaja =
                $datos['fecha_baja']
                ?? null;

            $telefono =
                $datos['telefono'];

            $email =
                $datos['email'];

            $domicilio =
                $datos['domicilio'];

            $observaciones =
                $datos['observaciones'];


            if (!$this->fechaLaboralValida($fechaAlta)) {

                throw new Exception(
                    "La fecha de alta del empleado no es válida."
                );
            }


            $stmt->bind_param(
                "iiiiisssssssssss",
                $institucionId,
                $oficinaId,
                $situacionId,
                $escalafonId,
                $categoriaId,
                $nroLegajo,
                $apellido,
                $nombre,
                $dni,
                $cuil,
                $fechaAlta,
                $fechaBaja,
                $telefono,
                $email,
                $domicilio,
                $observaciones
            );


            if (!$stmt->execute()) {

                $error =
                    $stmt->error;

                $stmt->close();

                throw new Exception(
                    "No se pudo guardar el empleado: "
                    . $error
                );
            }


            $idEmpleado =
                (int)$stmt->insert_id;


            $stmt->close();


            if ($idEmpleado <= 0) {

                throw new Exception(
                    "No se pudo obtener el ID del empleado creado."
                );
            }


            /*
            |--------------------------------------------------------------------------
            | PRIMER PERÍODO LABORAL
            |--------------------------------------------------------------------------
            */

            $this->abrirPeriodoLaboral(
                $idEmpleado,
                $fechaAlta,
                'Alta inicial',
                'Período creado automáticamente con el alta del empleado.',
                null
            );


            $this->conexion->commit();


            return $idEmpleado;

        } catch (Exception $e) {

            $this->conexion->rollback();

            throw $e;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | ACTUALIZAR EMPLEADO
    |--------------------------------------------------------------------------
    */

    public function actualizar(
        $id,
        $datos
    ) {
        $sql = "
            UPDATE empleado
            SET
                institucion_id = ?,
                oficina_id = ?,
                situacion_id = ?,
                escalafon_id = ?,
                categoria_id = ?,
                nro_legajo = ?,
                apellido = ?,
                nombre = ?,
                dni = ?,
                cuil = ?,
                fecha_alta = ?,
                fecha_baja = ?,
                telefono = ?,
                email = ?,
                domicilio = ?,
                observaciones = ?
            WHERE id = ?
        ";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {

            throw new Exception(
                "No se pudo preparar la actualización del empleado: "
                . $this->conexion->error
            );
        }


        $institucionId =
            $datos['institucion_id'];

        $oficinaId =
            $datos['oficina_id'];

        $situacionId =
            $datos['situacion_id'];

        $categoriaId =
            (int)$datos['categoria_id'];


        $escalafonId =
            $this->normalizarEscalafon(
                $categoriaId,
                $datos['escalafon_id']
                ?? null
            );

        $nroLegajo =
            $datos['nro_legajo'];

        $apellido =
            $datos['apellido'];

        $nombre =
            $datos['nombre'];

        $dni =
            $datos['dni'];

        $cuil =
            $datos['cuil'];

        $fechaAlta =
            $datos['fecha_alta'];

        $fechaBaja =
            $datos['fecha_baja'];

        $telefono =
            $datos['telefono'];

        $email =
            $datos['email'];

        $domicilio =
            $datos['domicilio'];

        $observaciones =
            $datos['observaciones'];


        $stmt->bind_param(
            "iiiiisssssssssssi",
            $institucionId,
            $oficinaId,
            $situacionId,
            $escalafonId,
            $categoriaId,
            $nroLegajo,
            $apellido,
            $nombre,
            $dni,
            $cuil,
            $fechaAlta,
            $fechaBaja,
            $telefono,
            $email,
            $domicilio,
            $observaciones,
            $id
        );


        if (!$stmt->execute()) {

            $error = $stmt->error;

            $stmt->close();

            throw new Exception(
                "No se pudo actualizar el empleado: "
                . $error
            );
        }


        $stmt->close();

        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | CAMBIAR ESTADO DEL EMPLEADO
    |--------------------------------------------------------------------------
    */

    public function cambiarEstado(
        $id,
        $activo,
        $fechaEfectiva = null,
        $observacion = null,
        $usuarioId = null
    ) {
        $id =
            (int)$id;

        $activo =
            (int)$activo;

        $usuarioId =
            $usuarioId !== null
                ? (int)$usuarioId
                : null;


        if (
            $id <= 0
            ||
            (
                $activo !== 0
                &&
                $activo !== 1
            )
        ) {

            throw new Exception(
                "Estado de empleado no válido."
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FECHA EFECTIVA
        |--------------------------------------------------------------------------
        |
        | Si todavía no se informa desde la interfaz, se usa la fecha actual
        | de Argentina. Más adelante el controlador podrá enviar una fecha
        | elegida por el operador.
        |
        */

        $fechaEfectiva =
            trim(
                (string)(
                    $fechaEfectiva
                    ?? ''
                )
            );


        if ($fechaEfectiva === '') {

            $fechaEfectiva =
                $this->fechaActualArgentina();
        }


        if (!$this->fechaLaboralValida($fechaEfectiva)) {

            throw new Exception(
                "La fecha efectiva del cambio de estado no es válida."
            );
        }


        $observacion =
            trim(
                (string)(
                    $observacion
                    ?? ''
                )
            );


        if (strlen($observacion) > 255) {

            throw new Exception(
                "La observación del cambio de estado no puede superar los 255 caracteres."
            );
        }


        $observacionBD =
            $observacion === ''
                ? null
                : $observacion;


        $this->conexion->begin_transaction();


        try {

            $empleado =
                $this->obtenerPorId(
                    $id
                );


            if (!$empleado) {

                throw new Exception(
                    "El empleado seleccionado no existe."
                );
            }


            $estadoActual =
                (int)(
                    $empleado['activo']
                    ?? 0
                );


            if ($estadoActual === $activo) {

                /*
                | No se duplica ni se cierra ningún período si el empleado
                | ya se encuentra en el estado solicitado.
                */

                $this->conexion->commit();

                return true;
            }


            /*
            |--------------------------------------------------------------------------
            | INACTIVAR
            |--------------------------------------------------------------------------
            */

            if ($activo === 0) {

                $this->cerrarPeriodoLaboral(
                    $id,
                    $fechaEfectiva,
                    'Inactivación',
                    $observacionBD,
                    $usuarioId
                );


                /*
                |--------------------------------------------------------------------------
                | CERRAR CONCEPTOS VIGENTES
                |--------------------------------------------------------------------------
                |
                | Al finalizar la relación laboral:
                |
                | - las asignaciones que ya comenzaron y continúan vigentes se
                |   cierran con la misma fecha efectiva;
                | - las asignaciones futuras que todavía no comenzaron se desactivan;
                | - al reincorporar al empleado NO se reabren automáticamente.
                |
                | Todo ocurre dentro de la misma transacción del cambio de estado.
                |
                */

                $this->cerrarConceptosPorInactivacion(
                    $id,
                    $fechaEfectiva
                );


                $sql = "
                    UPDATE empleado
                    SET
                        activo = 0,
                        fecha_inactivo = ?
                    WHERE id = ?
                ";


                $stmt =
                    $this->conexion->prepare(
                        $sql
                    );


                if (!$stmt) {

                    throw new Exception(
                        "No se pudo preparar la inactivación del empleado: "
                        . $this->conexion->error
                    );
                }


                $stmt->bind_param(
                    "si",
                    $fechaEfectiva,
                    $id
                );

            /*
            |--------------------------------------------------------------------------
            | REACTIVAR
            |--------------------------------------------------------------------------
            */

            } else {

                $this->abrirPeriodoLaboral(
                    $id,
                    $fechaEfectiva,
                    'Reincorporación',
                    $observacionBD,
                    $usuarioId
                );


                $sql = "
                    UPDATE empleado
                    SET
                        activo = 1,
                        fecha_inactivo = NULL
                    WHERE id = ?
                ";


                $stmt =
                    $this->conexion->prepare(
                        $sql
                    );


                if (!$stmt) {

                    throw new Exception(
                        "No se pudo preparar la activación del empleado: "
                        . $this->conexion->error
                    );
                }


                $stmt->bind_param(
                    "i",
                    $id
                );
            }


            if (!$stmt->execute()) {

                $error =
                    $stmt->error;

                $stmt->close();

                throw new Exception(
                    "No se pudo actualizar el estado del empleado: "
                    . $error
                );
            }


            $stmt->close();


            $this->conexion->commit();


            return true;

        } catch (Exception $e) {

            $this->conexion->rollback();

            throw $e;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | HISTORIAL LABORAL DEL EMPLEADO
    |--------------------------------------------------------------------------
    */


    /*
    |--------------------------------------------------------------------------
    | OBTENER TODOS LOS PERÍODOS LABORALES
    |--------------------------------------------------------------------------
    */

    public function obtenerPeriodosLaborales(
        $empleadoId
    ) {
        $empleadoId =
            (int)$empleadoId;


        if ($empleadoId <= 0) {

            return [];
        }


        $sql = "
            SELECT
                pl.id,
                pl.empleado_id,
                pl.fecha_desde,
                pl.fecha_hasta,
                pl.motivo_inicio,
                pl.motivo_fin,
                pl.observacion,
                pl.usuario_alta_id,
                pl.usuario_cierre_id,
                pl.created_at,
                pl.updated_at,

                CASE
                    WHEN pl.fecha_hasta IS NULL
                    THEN 'ACTUAL'
                    ELSE 'FINALIZADO'
                END AS estado_periodo,

                DATEDIFF(
                    COALESCE(
                        pl.fecha_hasta,
                        CURDATE()
                    ),
                    pl.fecha_desde
                ) + 1 AS dias_periodo

            FROM empleado_periodo_laboral pl

            WHERE pl.empleado_id = ?

            ORDER BY
                pl.fecha_desde ASC,
                pl.id ASC
        ";


        $stmt =
            $this->conexion->prepare(
                $sql
            );


        if (!$stmt) {

            throw new Exception(
                "No se pudo preparar el historial laboral del empleado: "
                . $this->conexion->error
            );
        }


        $stmt->bind_param(
            "i",
            $empleadoId
        );


        if (!$stmt->execute()) {

            $error =
                $stmt->error;

            $stmt->close();

            throw new Exception(
                "No se pudo consultar el historial laboral del empleado: "
                . $error
            );
        }


        $resultado =
            $stmt->get_result();


        $periodos =
            [];


        while (
            $fila =
                $resultado->fetch_assoc()
        ) {

            $fila['id'] =
                (int)$fila['id'];

            $fila['empleado_id'] =
                (int)$fila['empleado_id'];

            $fila['dias_periodo'] =
                max(
                    0,
                    (int)(
                        $fila['dias_periodo']
                        ?? 0
                    )
                );

            $fila['usuario_alta_id'] =
                $fila['usuario_alta_id'] !== null
                    ? (int)$fila['usuario_alta_id']
                    : null;

            $fila['usuario_cierre_id'] =
                $fila['usuario_cierre_id'] !== null
                    ? (int)$fila['usuario_cierre_id']
                    : null;


            $periodos[] =
                $fila;
        }


        $stmt->close();


        return $periodos;
    }


    /*
    |--------------------------------------------------------------------------
    | OBTENER PERÍODO ABIERTO
    |--------------------------------------------------------------------------
    */

    public function obtenerPeriodoLaboralAbierto(
        $empleadoId
    ) {
        $empleadoId =
            (int)$empleadoId;


        if ($empleadoId <= 0) {

            return null;
        }


        $sql = "
            SELECT
                id,
                empleado_id,
                fecha_desde,
                fecha_hasta,
                motivo_inicio,
                motivo_fin,
                observacion,
                usuario_alta_id,
                usuario_cierre_id,
                created_at,
                updated_at

            FROM empleado_periodo_laboral

            WHERE empleado_id = ?
              AND fecha_hasta IS NULL

            ORDER BY
                fecha_desde DESC,
                id DESC

            LIMIT 1
        ";


        $stmt =
            $this->conexion->prepare(
                $sql
            );


        if (!$stmt) {

            throw new Exception(
                "No se pudo preparar la consulta del período laboral abierto: "
                . $this->conexion->error
            );
        }


        $stmt->bind_param(
            "i",
            $empleadoId
        );


        if (!$stmt->execute()) {

            $error =
                $stmt->error;

            $stmt->close();

            throw new Exception(
                "No se pudo consultar el período laboral abierto: "
                . $error
            );
        }


        $periodo =
            $stmt
                ->get_result()
                ->fetch_assoc();


        $stmt->close();


        return $periodo
            ?: null;
    }


    /*
    |--------------------------------------------------------------------------
    | CONTAR PERÍODOS LABORALES
    |--------------------------------------------------------------------------
    */

    public function contarPeriodosLaborales(
        $empleadoId
    ) {
        $empleadoId =
            (int)$empleadoId;


        if ($empleadoId <= 0) {

            return 0;
        }


        $sql = "
            SELECT COUNT(*) AS total
            FROM empleado_periodo_laboral
            WHERE empleado_id = ?
        ";


        $stmt =
            $this->conexion->prepare(
                $sql
            );


        if (!$stmt) {

            throw new Exception(
                "No se pudo preparar el conteo de períodos laborales: "
                . $this->conexion->error
            );
        }


        $stmt->bind_param(
            "i",
            $empleadoId
        );


        if (!$stmt->execute()) {

            $error =
                $stmt->error;

            $stmt->close();

            throw new Exception(
                "No se pudieron contar los períodos laborales: "
                . $error
            );
        }


        $fila =
            $stmt
                ->get_result()
                ->fetch_assoc();


        $stmt->close();


        return (int)(
            $fila['total']
            ?? 0
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ABRIR NUEVO PERÍODO LABORAL
    |--------------------------------------------------------------------------
    */

    public function abrirPeriodoLaboral(
        $empleadoId,
        $fechaDesde,
        $motivoInicio = 'Reincorporación',
        $observacion = null,
        $usuarioAltaId = null
    ) {
        $empleadoId =
            (int)$empleadoId;

        $fechaDesde =
            trim(
                (string)$fechaDesde
            );

        $motivoInicio =
            trim(
                (string)(
                    $motivoInicio
                    ?? ''
                )
            );

        $observacion =
            trim(
                (string)(
                    $observacion
                    ?? ''
                )
            );

        $usuarioAltaId =
            $usuarioAltaId !== null
                ? (int)$usuarioAltaId
                : null;


        if (
            $empleadoId <= 0
            ||
            !$this->fechaLaboralValida(
                $fechaDesde
            )
        ) {

            throw new Exception(
                "Datos inválidos para abrir el período laboral."
            );
        }


        if (strlen($motivoInicio) > 150) {

            throw new Exception(
                "El motivo de inicio no puede superar los 150 caracteres."
            );
        }


        if (strlen($observacion) > 255) {

            throw new Exception(
                "La observación no puede superar los 255 caracteres."
            );
        }


        /*
        |--------------------------------------------------------------------------
        | NO PUEDE EXISTIR OTRO PERÍODO ABIERTO
        |--------------------------------------------------------------------------
        */

        if (
            $this->obtenerPeriodoLaboralAbierto(
                $empleadoId
            )
        ) {

            throw new Exception(
                "El empleado ya posee un período laboral abierto."
            );
        }


        /*
        |--------------------------------------------------------------------------
        | EVITAR SOLAPAMIENTOS
        |--------------------------------------------------------------------------
        */

        $sqlSolapamiento = "
            SELECT id
            FROM empleado_periodo_laboral
            WHERE empleado_id = ?
              AND fecha_desde <= ?
              AND COALESCE(
                    fecha_hasta,
                    '9999-12-31'
                  ) >= ?
            LIMIT 1
        ";


        $stmtSolapamiento =
            $this->conexion->prepare(
                $sqlSolapamiento
            );


        if (!$stmtSolapamiento) {

            throw new Exception(
                "No se pudo preparar la validación de períodos laborales: "
                . $this->conexion->error
            );
        }


        $stmtSolapamiento->bind_param(
            "iss",
            $empleadoId,
            $fechaDesde,
            $fechaDesde
        );


        if (!$stmtSolapamiento->execute()) {

            $error =
                $stmtSolapamiento->error;

            $stmtSolapamiento->close();

            throw new Exception(
                "No se pudo validar el nuevo período laboral: "
                . $error
            );
        }


        $existeSolapamiento =
            $stmtSolapamiento
                ->get_result()
                ->num_rows > 0;


        $stmtSolapamiento->close();


        if ($existeSolapamiento) {

            throw new Exception(
                "La fecha de reincorporación se superpone con un período laboral existente."
            );
        }


        /*
        |--------------------------------------------------------------------------
        | INSERTAR
        |--------------------------------------------------------------------------
        */

        $motivoInicioBD =
            $motivoInicio === ''
                ? null
                : $motivoInicio;

        $observacionBD =
            $observacion === ''
                ? null
                : $observacion;


        $sql = "
            INSERT INTO empleado_periodo_laboral (
                empleado_id,
                fecha_desde,
                fecha_hasta,
                motivo_inicio,
                motivo_fin,
                observacion,
                usuario_alta_id,
                usuario_cierre_id
            )
            VALUES (
                ?, ?, NULL, ?, NULL, ?, ?, NULL
            )
        ";


        $stmt =
            $this->conexion->prepare(
                $sql
            );


        if (!$stmt) {

            throw new Exception(
                "No se pudo preparar la apertura del período laboral: "
                . $this->conexion->error
            );
        }


        $stmt->bind_param(
            "isssi",
            $empleadoId,
            $fechaDesde,
            $motivoInicioBD,
            $observacionBD,
            $usuarioAltaId
        );


        if (!$stmt->execute()) {

            $error =
                $stmt->error;

            $stmt->close();

            throw new Exception(
                "No se pudo abrir el período laboral: "
                . $error
            );
        }


        $idPeriodo =
            (int)$stmt->insert_id;


        $stmt->close();


        return $idPeriodo;
    }


    /*
    |--------------------------------------------------------------------------
    | CERRAR PERÍODO LABORAL ABIERTO
    |--------------------------------------------------------------------------
    */

    public function cerrarPeriodoLaboral(
        $empleadoId,
        $fechaHasta,
        $motivoFin = 'Inactivación',
        $observacion = null,
        $usuarioCierreId = null
    ) {
        $empleadoId =
            (int)$empleadoId;

        $fechaHasta =
            trim(
                (string)$fechaHasta
            );

        $motivoFin =
            trim(
                (string)(
                    $motivoFin
                    ?? ''
                )
            );

        $observacion =
            trim(
                (string)(
                    $observacion
                    ?? ''
                )
            );

        $usuarioCierreId =
            $usuarioCierreId !== null
                ? (int)$usuarioCierreId
                : null;


        if (
            $empleadoId <= 0
            ||
            !$this->fechaLaboralValida(
                $fechaHasta
            )
        ) {

            throw new Exception(
                "Datos inválidos para cerrar el período laboral."
            );
        }


        if (strlen($motivoFin) > 150) {

            throw new Exception(
                "El motivo de finalización no puede superar los 150 caracteres."
            );
        }


        if (strlen($observacion) > 255) {

            throw new Exception(
                "La observación no puede superar los 255 caracteres."
            );
        }


        $periodo =
            $this->obtenerPeriodoLaboralAbierto(
                $empleadoId
            );


        if (!$periodo) {

            throw new Exception(
                "El empleado no posee un período laboral abierto para cerrar."
            );
        }


        $fechaDesde =
            trim(
                (string)(
                    $periodo['fecha_desde']
                    ?? ''
                )
            );


        if (
            !$this->fechaLaboralValida(
                $fechaDesde
            )
            ||
            $fechaHasta < $fechaDesde
        ) {

            throw new Exception(
                "La fecha de inactivación no puede ser anterior al inicio del período laboral."
            );
        }


        $motivoFinBD =
            $motivoFin === ''
                ? null
                : $motivoFin;

        $observacionBD =
            $observacion === ''
                ? null
                : $observacion;


        $sql = "
            UPDATE empleado_periodo_laboral
            SET
                fecha_hasta = ?,
                motivo_fin = ?,
                observacion =
                    CASE
                        WHEN ? IS NULL
                        THEN observacion
                        ELSE ?
                    END,
                usuario_cierre_id = ?
            WHERE id = ?
              AND fecha_hasta IS NULL
        ";


        $stmt =
            $this->conexion->prepare(
                $sql
            );


        if (!$stmt) {

            throw new Exception(
                "No se pudo preparar el cierre del período laboral: "
                . $this->conexion->error
            );
        }


        $periodoId =
            (int)$periodo['id'];


        $stmt->bind_param(
            "ssssii",
            $fechaHasta,
            $motivoFinBD,
            $observacionBD,
            $observacionBD,
            $usuarioCierreId,
            $periodoId
        );


        if (!$stmt->execute()) {

            $error =
                $stmt->error;

            $stmt->close();

            throw new Exception(
                "No se pudo cerrar el período laboral: "
                . $error
            );
        }


        $stmt->close();


        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDAR FECHA LABORAL
    |--------------------------------------------------------------------------
    */

    private function fechaLaboralValida(
        $fecha
    ) {
        $fecha =
            trim(
                (string)(
                    $fecha
                    ?? ''
                )
            );


        if ($fecha === '') {

            return false;
        }


        $objeto =
            DateTime::createFromFormat(
                'Y-m-d',
                $fecha
            );


        return (
            $objeto !== false
            &&
            $objeto->format(
                'Y-m-d'
            ) === $fecha
        );
    }


    /*
    |--------------------------------------------------------------------------
    | FECHA ACTUAL ARGENTINA
    |--------------------------------------------------------------------------
    */

    private function fechaActualArgentina()
    {
        $zona =
            new DateTimeZone(
                'America/Argentina/Buenos_Aires'
            );


        return (
            new DateTime(
                'now',
                $zona
            )
        )->format(
            'Y-m-d'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | VIGENCIA DE CONCEPTO DENTRO DE PERÍODO LABORAL
    |--------------------------------------------------------------------------
    |
    | Una asignación de concepto debe quedar completamente contenida dentro de
    | un único período laboral continuo del empleado.
    |
    | Reglas:
    |
    | - fecha_desde debe pertenecer a un período laboral;
    | - si fecha_hasta tiene valor, también debe quedar dentro del mismo período;
    | - si fecha_hasta es NULL, la asignación solo puede pertenecer a un período
    |   laboral actualmente abierto;
    | - no se permite que una asignación atraviese un intervalo de inactividad.
    |
    */

    public function validarVigenciaConceptoEnPeriodoLaboral(
        $empleadoId,
        $fechaDesde,
        $fechaHasta = null
    ) {
        $empleadoId =
            (int)$empleadoId;

        $fechaDesde =
            trim(
                (string)$fechaDesde
            );

        $fechaHasta =
            $fechaHasta === null
                ? null
                : trim(
                    (string)$fechaHasta
                );


        if (
            $empleadoId <= 0
            ||
            !$this->fechaLaboralValida(
                $fechaDesde
            )
        ) {

            throw new Exception(
                "La fecha desde de la asignación no es válida."
            );
        }


        if ($fechaHasta === '') {

            $fechaHasta =
                null;
        }


        if (
            $fechaHasta !== null
            &&
            !$this->fechaLaboralValida(
                $fechaHasta
            )
        ) {

            throw new Exception(
                "La fecha hasta de la asignación no es válida."
            );
        }


        if (
            $fechaHasta !== null
            &&
            $fechaHasta < $fechaDesde
        ) {

            throw new Exception(
                "La fecha hasta de la asignación no puede ser anterior a la fecha desde."
            );
        }


        /*
        |--------------------------------------------------------------------------
        | VIGENCIA ABIERTA
        |--------------------------------------------------------------------------
        |
        | Una asignación sin fecha_hasta debe comenzar dentro del período laboral
        | abierto del empleado.
        |
        */

        if ($fechaHasta === null) {

            $sql = "
                SELECT
                    id,
                    empleado_id,
                    fecha_desde,
                    fecha_hasta

                FROM empleado_periodo_laboral

                WHERE empleado_id = ?
                  AND fecha_desde <= ?
                  AND fecha_hasta IS NULL

                ORDER BY
                    fecha_desde DESC,
                    id DESC

                LIMIT 1
            ";


            $stmt =
                $this->conexion->prepare(
                    $sql
                );


            if (!$stmt) {

                throw new Exception(
                    "No se pudo preparar la validación de la vigencia laboral del concepto: "
                    . $this->conexion->error
                );
            }


            $stmt->bind_param(
                "is",
                $empleadoId,
                $fechaDesde
            );


        /*
        |--------------------------------------------------------------------------
        | VIGENCIA CERRADA
        |--------------------------------------------------------------------------
        |
        | Ambas fechas deben quedar dentro del mismo período laboral.
        |
        */

        } else {

            $sql = "
                SELECT
                    id,
                    empleado_id,
                    fecha_desde,
                    fecha_hasta

                FROM empleado_periodo_laboral

                WHERE empleado_id = ?
                  AND fecha_desde <= ?
                  AND (
                        fecha_hasta IS NULL
                        OR fecha_hasta >= ?
                  )

                ORDER BY
                    fecha_desde DESC,
                    id DESC

                LIMIT 1
            ";


            $stmt =
                $this->conexion->prepare(
                    $sql
                );


            if (!$stmt) {

                throw new Exception(
                    "No se pudo preparar la validación de la vigencia laboral del concepto: "
                    . $this->conexion->error
                );
            }


            $stmt->bind_param(
                "iss",
                $empleadoId,
                $fechaDesde,
                $fechaHasta
            );
        }


        if (!$stmt->execute()) {

            $error =
                $stmt->error;

            $stmt->close();

            throw new Exception(
                "No se pudo validar la vigencia del concepto contra el historial laboral: "
                . $error
            );
        }


        $periodo =
            $stmt
                ->get_result()
                ->fetch_assoc();


        $stmt->close();


        if (!$periodo) {

            if ($fechaHasta === null) {

                throw new Exception(
                    "La asignación sin fecha hasta debe comenzar dentro del período laboral actual del empleado. "
                    . "No puede iniciarse en un período laboral ya finalizado ni atravesar una inactividad."
                );
            }


            throw new Exception(
                "La vigencia del concepto debe quedar completamente contenida dentro de un único período laboral del empleado. "
                . "No puede comenzar o finalizar durante un período de inactividad ni atravesar una interrupción laboral."
            );
        }


        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | CERRAR CONCEPTOS AL INACTIVAR EMPLEADO
    |--------------------------------------------------------------------------
    |
    | fecha_hasta es inclusiva, igual que empleado_periodo_laboral.fecha_hasta.
    |
    | 1. Asignaciones ya iniciadas:
    |       se cierran con la fecha efectiva de inactivación.
    |
    | 2. Asignaciones futuras:
    |       se desactivan porque ya no existe relación laboral que las respalde.
    |
    | La reincorporación posterior NO modifica estos registros.
    |
    */

    private function cerrarConceptosPorInactivacion(
        $empleadoId,
        $fechaEfectiva
    ) {
        $empleadoId =
            (int)$empleadoId;

        $fechaEfectiva =
            trim(
                (string)$fechaEfectiva
            );


        if (
            $empleadoId <= 0
            ||
            !$this->fechaLaboralValida(
                $fechaEfectiva
            )
        ) {

            throw new Exception(
                "Datos inválidos para cerrar las asignaciones del empleado."
            );
        }


        /*
        |--------------------------------------------------------------------------
        | CERRAR ASIGNACIONES QUE YA COMENZARON
        |--------------------------------------------------------------------------
        */

        $sqlCerrar = "
            UPDATE empleado_concepto

            SET fecha_hasta = ?

            WHERE empleado_id = ?
              AND activo = 1
              AND fecha_desde <= ?
              AND (
                    fecha_hasta IS NULL
                    OR fecha_hasta > ?
              )
        ";


        $stmtCerrar =
            $this->conexion->prepare(
                $sqlCerrar
            );


        if (!$stmtCerrar) {

            throw new Exception(
                "No se pudo preparar el cierre automático de conceptos del empleado: "
                . $this->conexion->error
            );
        }


        $stmtCerrar->bind_param(
            "siss",
            $fechaEfectiva,
            $empleadoId,
            $fechaEfectiva,
            $fechaEfectiva
        );


        if (!$stmtCerrar->execute()) {

            $error =
                $stmtCerrar->error;

            $stmtCerrar->close();

            throw new Exception(
                "No se pudieron cerrar automáticamente los conceptos vigentes del empleado: "
                . $error
            );
        }


        $stmtCerrar->close();


        /*
        |--------------------------------------------------------------------------
        | CANCELAR ASIGNACIONES FUTURAS
        |--------------------------------------------------------------------------
        |
        | No se puede colocar fecha_hasta = fechaEfectiva porque quedaría antes
        | de fecha_desde. Por eso se conservan las fechas y se desactiva el registro.
        |
        */

        $sqlFuturas = "
            UPDATE empleado_concepto

            SET activo = 0

            WHERE empleado_id = ?
              AND activo = 1
              AND fecha_desde > ?
        ";


        $stmtFuturas =
            $this->conexion->prepare(
                $sqlFuturas
            );


        if (!$stmtFuturas) {

            throw new Exception(
                "No se pudo preparar la cancelación de conceptos futuros del empleado: "
                . $this->conexion->error
            );
        }


        $stmtFuturas->bind_param(
            "is",
            $empleadoId,
            $fechaEfectiva
        );


        if (!$stmtFuturas->execute()) {

            $error =
                $stmtFuturas->error;

            $stmtFuturas->close();

            throw new Exception(
                "No se pudieron desactivar los conceptos futuros del empleado: "
                . $error
            );
        }


        $stmtFuturas->close();


        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | CONCEPTOS POR EMPLEADO
    |--------------------------------------------------------------------------
    */


    /*
    |--------------------------------------------------------------------------
    | LISTAR CONCEPTOS ASIGNADOS
    |--------------------------------------------------------------------------
    */

    public function listarConceptosAsignados(
        $buscarEmpleado = "",
        $conceptoId = 0,
        $estado = ""
    ) {
        $sql = "
            SELECT
                ec.id,
                ec.empleado_id,
                ec.concepto_id,
                ec.monto_manual,
                ec.porcentaje_manual,
                ec.cantidad,
                ec.fecha_desde,
                ec.fecha_hasta,
                ec.activo,
                ec.observacion,

                e.nro_legajo,
                e.apellido,
                e.nombre,

                c.codigo,
                c.nombre AS concepto,
                c.categoria AS concepto_categoria,
                c.forma_calculo,
                c.asignable_empleado

            FROM empleado_concepto ec

            INNER JOIN empleado e
                ON ec.empleado_id = e.id

            INNER JOIN concepto c
                ON ec.concepto_id = c.id

            WHERE 1 = 1
        ";


        $params = [];
        $types = "";


        /*
        |--------------------------------------------------------------------------
        | FILTRO POR EMPLEADO
        |--------------------------------------------------------------------------
        |
        | Si el usuario escribe solamente números, se interpreta como LEGAJO
        | y se exige coincidencia exacta.
        |
        | Si contiene letras, la búsqueda continúa siendo parcial por nombre
        | y apellido.
        |
        |--------------------------------------------------------------------------
        */

        if ($buscarEmpleado !== "") {

            if (ctype_digit($buscarEmpleado)) {

                $sql .= "
                    AND CAST(
                        e.nro_legajo
                        AS CHAR
                    ) = ?
                ";


                $params[] =
                    $buscarEmpleado;


                $types .=
                    "s";

            } else {

                $sql .= "
                    AND (
                        e.apellido LIKE ?
                        OR e.nombre LIKE ?
                        OR CONCAT(
                            e.apellido,
                            ' ',
                            e.nombre
                        ) LIKE ?
                        OR CONCAT(
                            e.nombre,
                            ' ',
                            e.apellido
                        ) LIKE ?
                    )
                ";


                $like =
                    "%" . $buscarEmpleado . "%";


                $params[] = $like;
                $params[] = $like;
                $params[] = $like;
                $params[] = $like;


                $types .=
                    "ssss";
            }
        }


        /*
        |--------------------------------------------------------------------------
        | FILTRO POR CONCEPTO
        |--------------------------------------------------------------------------
        */

        if ($conceptoId > 0) {

            $sql .= "
                AND ec.concepto_id = ?
            ";


            $params[] =
                $conceptoId;


            $types .=
                "i";
        }


        /*
        |--------------------------------------------------------------------------
        | FILTRO POR ESTADO / VIGENCIA
        |--------------------------------------------------------------------------
        */

        if ($estado !== "") {

            $fechaHoy =
                $this->fechaActualArgentina();


            switch ($estado) {

                case 'vigente':

                    $sql .= "
                        AND ec.activo = 1
                        AND ec.fecha_desde <= ?
                        AND (
                            ec.fecha_hasta IS NULL
                            OR ec.fecha_hasta >= ?
                        )
                    ";


                    $params[] =
                        $fechaHoy;

                    $params[] =
                        $fechaHoy;


                    $types .=
                        "ss";

                    break;


                case 'programado':

                    $sql .= "
                        AND ec.activo = 1
                        AND ec.fecha_desde > ?
                    ";


                    $params[] =
                        $fechaHoy;


                    $types .=
                        "s";

                    break;


                case 'finalizado':

                    $sql .= "
                        AND ec.activo = 1
                        AND ec.fecha_hasta IS NOT NULL
                        AND ec.fecha_hasta < ?
                    ";


                    $params[] =
                        $fechaHoy;


                    $types .=
                        "s";

                    break;


                case 'inactivo':

                    $sql .= "
                        AND ec.activo = 0
                    ";

                    break;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | ORDEN
        |--------------------------------------------------------------------------
        */

        $sql .= "
            ORDER BY
                e.apellido ASC,
                e.nombre ASC,
                ec.fecha_desde DESC,
                ec.id DESC
        ";


        $stmt =
            $this->conexion->prepare(
                $sql
            );


        if (!$stmt) {

            throw new Exception(
                "Error al preparar el listado de conceptos por empleado: "
                . $this->conexion->error
            );
        }


        if (!empty($params)) {

            $stmt->bind_param(
                $types,
                ...$params
            );
        }


        if (!$stmt->execute()) {

            $error =
                $stmt->error;


            $stmt->close();


            throw new Exception(
                "Error al consultar los conceptos asignados a empleados: "
                . $error
            );
        }


        $resultado =
            $stmt->get_result();


        $asignaciones = [];


        while (
            $fila =
                $resultado->fetch_assoc()
        ) {

            $asignaciones[] =
                $fila;
        }


        $stmt->close();


        return $asignaciones;
    }


    /*
    |--------------------------------------------------------------------------
    | OBTENER ASIGNACIÓN POR ID
    |--------------------------------------------------------------------------
    */

    public function obtenerConceptoEmpleadoPorId(
        $id
    ) {
        $sql = "
            SELECT
                ec.id,
                ec.empleado_id,
                ec.concepto_id,
                ec.monto_manual,
                ec.porcentaje_manual,
                ec.cantidad,
                ec.fecha_desde,
                ec.fecha_hasta,
                ec.activo,
                ec.observacion,

                e.nro_legajo AS empleado_legajo,
                e.apellido AS empleado_apellido,
                e.nombre AS empleado_nombre,
                e.activo AS empleado_activo,

                c.codigo AS concepto_codigo,
                c.nombre AS concepto_nombre,
                c.categoria AS concepto_categoria,
                c.forma_calculo,
                c.asignable_empleado AS concepto_asignable_empleado,
                c.activo AS concepto_activo

            FROM empleado_concepto ec

            INNER JOIN empleado e
                ON ec.empleado_id = e.id

            INNER JOIN concepto c
                ON ec.concepto_id = c.id

            WHERE ec.id = ?

            LIMIT 1
        ";


        $stmt =
            $this->conexion->prepare(
                $sql
            );


        if (!$stmt) {

            throw new Exception(
                "Error al preparar la consulta de la asignación: "
                . $this->conexion->error
            );
        }


        $stmt->bind_param(
            "i",
            $id
        );


        if (!$stmt->execute()) {

            $error =
                $stmt->error;


            $stmt->close();


            throw new Exception(
                "Error al consultar la asignación: "
                . $error
            );
        }


        $resultado =
            $stmt->get_result();


        $asignacion =
            $resultado->fetch_assoc();


        $stmt->close();


        return $asignacion;
    }


    /*
    |--------------------------------------------------------------------------
    | OBTENER CONCEPTOS ASIGNABLES ACTIVOS
    |--------------------------------------------------------------------------
    |
    | Solo se muestran en Conceptos por Empleado los conceptos que:
    |
    | - están activos;
    | - tienen asignable_empleado = 1.
    |
    | Los conceptos automáticos quedan ocultos del selector.
    |
    |--------------------------------------------------------------------------
    */

    public function obtenerConceptosActivos()
    {
        $sql = "
            SELECT
                id,
                codigo,
                nombre,
                categoria,
                forma_calculo,
                asignable_empleado
            FROM concepto
            WHERE activo = 1
              AND asignable_empleado = 1

              AND codigo NOT IN (101, 102, 104)

              AND (
                    categoria = 'ASIGNACION_FAMILIAR'
                    OR forma_calculo IN (
                        'MANUAL',
                        'PORCENTAJE',
                        'FIJO'
                    )
              )

            ORDER BY
                CAST(codigo AS UNSIGNED) ASC,
                codigo ASC,
                nombre ASC
        ";


        $resultado =
            $this->conexion->query(
                $sql
            );


        if (!$resultado) {

            throw new Exception(
                "Error al cargar los conceptos activos: "
                . $this->conexion->error
            );
        }


        $conceptos = [];


        while (
            $fila =
                $resultado->fetch_assoc()
        ) {

            $conceptos[] =
                $fila;
        }


        return $conceptos;
    }


    /*
    |--------------------------------------------------------------------------
    | OBTENER EMPLEADOS ACTIVOS PARA CONCEPTOS
    |--------------------------------------------------------------------------
    */

    public function obtenerEmpleadosActivosParaConceptos()
    {
        $sql = "
            SELECT
                id,
                nro_legajo,
                apellido,
                nombre
            FROM empleado
            WHERE activo = 1
            ORDER BY
                apellido ASC,
                nombre ASC
        ";


        $resultado =
            $this->conexion->query(
                $sql
            );


        if (!$resultado) {

            throw new Exception(
                "Error al cargar los empleados activos: "
                . $this->conexion->error
            );
        }


        $empleados = [];


        while (
            $fila =
                $resultado->fetch_assoc()
        ) {

            $empleados[] =
                $fila;
        }


        return $empleados;
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDAR EMPLEADO ACTIVO
    |--------------------------------------------------------------------------
    */

    public function empleadoActivoExiste(
        $empleadoId
    ) {
        $sql = "
            SELECT id
            FROM empleado
            WHERE id = ?
              AND activo = 1
            LIMIT 1
        ";


        $stmt =
            $this->conexion->prepare(
                $sql
            );


        if (!$stmt) {

            throw new Exception(
                "Error al preparar la validación del empleado: "
                . $this->conexion->error
            );
        }


        $stmt->bind_param(
            "i",
            $empleadoId
        );


        if (!$stmt->execute()) {

            $error =
                $stmt->error;


            $stmt->close();


            throw new Exception(
                "Error al validar el empleado: "
                . $error
            );
        }


        $resultado =
            $stmt->get_result();


        $existe =
            $resultado->num_rows > 0;


        $stmt->close();


        return $existe;
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDAR CONCEPTO ACTIVO Y ASIGNABLE
    |--------------------------------------------------------------------------
    |
    | Evita que un concepto automático sea asignado manualmente modificando
    | la URL o el request.
    |
    |--------------------------------------------------------------------------
    */

    public function conceptoActivoExiste(
        $conceptoId
    ) {
        $sql = "
            SELECT id
            FROM concepto
            WHERE id = ?
              AND activo = 1
              AND asignable_empleado = 1

              AND codigo NOT IN (101, 102, 104)

              AND (
                    categoria = 'ASIGNACION_FAMILIAR'
                    OR forma_calculo IN (
                        'MANUAL',
                        'PORCENTAJE',
                        'FIJO'
                    )
              )

            LIMIT 1
        ";


        $stmt =
            $this->conexion->prepare(
                $sql
            );


        if (!$stmt) {

            throw new Exception(
                "Error al preparar la validación del concepto: "
                . $this->conexion->error
            );
        }


        $stmt->bind_param(
            "i",
            $conceptoId
        );


        if (!$stmt->execute()) {

            $error =
                $stmt->error;


            $stmt->close();


            throw new Exception(
                "Error al validar el concepto: "
                . $error
            );
        }


        $resultado =
            $stmt->get_result();


        $existe =
            $resultado->num_rows > 0;


        $stmt->close();


        return $existe;
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDAR ASIGNACIÓN DUPLICADA
    |--------------------------------------------------------------------------
    */

    public function existeAsignacionConceptoDuplicada(
        $empleadoId,
        $conceptoId,
        $fechaDesde,
        $idExcluir = null
    ) {
        $sql = "
            SELECT id
            FROM empleado_concepto

            WHERE empleado_id = ?
              AND concepto_id = ?
              AND fecha_desde = ?
              AND activo = 1
        ";


        if ($idExcluir !== null) {

            $sql .= "
                AND id <> ?
            ";
        }


        $sql .= "
            LIMIT 1
        ";


        $stmt =
            $this->conexion->prepare(
                $sql
            );


        if (!$stmt) {

            throw new Exception(
                "Error al preparar la validación de asignaciones duplicadas: "
                . $this->conexion->error
            );
        }


        if ($idExcluir === null) {

            $stmt->bind_param(
                "iis",
                $empleadoId,
                $conceptoId,
                $fechaDesde
            );

        } else {

            $stmt->bind_param(
                "iisi",
                $empleadoId,
                $conceptoId,
                $fechaDesde,
                $idExcluir
            );
        }


        if (!$stmt->execute()) {

            $error =
                $stmt->error;


            $stmt->close();


            throw new Exception(
                "Error al validar la asignación: "
                . $error
            );
        }


        $resultado =
            $stmt->get_result();


        $existe =
            $resultado->num_rows > 0;


        $stmt->close();


        return $existe;
    }



    /*
    |--------------------------------------------------------------------------
    | VALIDAR SOLAPAMIENTO DE VIGENCIAS
    |--------------------------------------------------------------------------
    |
    | Regla:
    |
    | Un empleado no puede tener dos asignaciones ACTIVAS del mismo concepto
    | cuyos períodos de vigencia se superpongan.
    |
    | Ejemplos:
    |
    | 01/08/2026 -> NULL
    | 01/09/2026 -> NULL
    |     BLOQUEADO
    |
    | 01/08/2026 -> 31/08/2026
    | 01/09/2026 -> NULL
    |     PERMITIDO
    |
    | fecha_hasta NULL representa vigencia indefinida.
    |
    |--------------------------------------------------------------------------
    */

    public function existeAsignacionConceptoSolapada(
        $empleadoId,
        $conceptoId,
        $fechaDesde,
        $fechaHasta = null,
        $idExcluir = null
    ) {
        $empleadoId =
            (int)$empleadoId;

        $conceptoId =
            (int)$conceptoId;

        $fechaDesde =
            trim(
                (string)$fechaDesde
            );

        $fechaHastaParametro =
            $fechaHasta === null
                ? ''
                : trim(
                    (string)$fechaHasta
                );


        if (
            $empleadoId <= 0
            ||
            $conceptoId <= 0
            ||
            $fechaDesde === ''
        ) {

            return false;
        }


        /*
        |--------------------------------------------------------------------------
        | CONDICIÓN DE SOLAPAMIENTO
        |--------------------------------------------------------------------------
        |
        | existente.desde <= nueva.hasta
        | Y
        | existente.hasta >= nueva.desde
        |
        | Cuando alguna fecha_hasta es NULL se considera 9999-12-31.
        |
        */

        $sql = "
            SELECT
                id

            FROM empleado_concepto

            WHERE empleado_id = ?
              AND concepto_id = ?
              AND activo = 1

              AND fecha_desde
                  <=
                  COALESCE(
                      NULLIF(?, ''),
                      '9999-12-31'
                  )

              AND COALESCE(
                      fecha_hasta,
                      '9999-12-31'
                  )
                  >=
                  ?
        ";


        if ($idExcluir !== null) {

            $sql .= "
              AND id <> ?
            ";
        }


        $sql .= "
            LIMIT 1
        ";


        $stmt =
            $this->conexion
                ->prepare(
                    $sql
                );


        if (!$stmt) {

            throw new Exception(
                "Error al preparar la validación de vigencias de conceptos por empleado: "
                . $this->conexion->error
            );
        }


        if ($idExcluir === null) {

            $stmt->bind_param(
                "iiss",
                $empleadoId,
                $conceptoId,
                $fechaHastaParametro,
                $fechaDesde
            );

        } else {

            $idExcluir =
                (int)$idExcluir;


            $stmt->bind_param(
                "iissi",
                $empleadoId,
                $conceptoId,
                $fechaHastaParametro,
                $fechaDesde,
                $idExcluir
            );
        }


        if (!$stmt->execute()) {

            $error =
                $stmt->error;


            $stmt->close();


            throw new Exception(
                "Error al validar el solapamiento de vigencias: "
                . $error
            );
        }


        $resultado =
            $stmt->get_result();


        $existe =
            $resultado->num_rows > 0;


        $stmt->close();


        return $existe;
    }


    /*
    |--------------------------------------------------------------------------
    | GUARDAR CONCEPTO POR EMPLEADO
    |--------------------------------------------------------------------------
    */

    public function guardarConceptoEmpleado(
        $datos
    ) {
        $empleadoId =
            (int)(
                $datos['empleado_id']
                ?? 0
            );


        $conceptoId =
            (int)(
                $datos['concepto_id']
                ?? 0
            );


        $montoManual =
            (float)(
                $datos['monto_manual']
                ?? 0
            );


        $porcentajeManual =
            (float)(
                $datos['porcentaje_manual']
                ?? 0
            );


        $cantidad =
            (float)(
                $datos['cantidad']
                ?? 1
            );


        $fechaDesde =
            trim(
                (string)(
                    $datos['fecha_desde']
                    ?? ''
                )
            );


        $fechaHasta =
            $datos['fecha_hasta']
            ?? null;


        if ($fechaHasta !== null) {

            $fechaHasta =
                trim(
                    (string)$fechaHasta
                );


            if ($fechaHasta === '') {

                $fechaHasta =
                    null;
            }
        }


        $activo =
            (int)(
                $datos['activo']
                ?? 1
            );


        $observacion =
            $datos['observacion']
            ?? null;


        /*
        |--------------------------------------------------------------------------
        | INTEGRIDAD CON HISTORIAL LABORAL
        |--------------------------------------------------------------------------
        */

        if (!$this->empleadoActivoExiste($empleadoId)) {

            throw new Exception(
                "No se puede crear la asignación porque el empleado no existe o se encuentra inactivo."
            );
        }


        $this->validarVigenciaConceptoEnPeriodoLaboral(
            $empleadoId,
            $fechaDesde,
            $fechaHasta
        );


        /*
        |--------------------------------------------------------------------------
        | DEFENSA CONTRA SOLAPAMIENTOS
        |--------------------------------------------------------------------------
        */

        if (
            $activo === 1
            &&
            $this->existeAsignacionConceptoSolapada(
                $empleadoId,
                $conceptoId,
                $fechaDesde,
                $fechaHasta
            )
        ) {

            throw new Exception(
                "El empleado ya posee una asignación activa del mismo concepto con una vigencia superpuesta."
            );
        }


        $sql = "
            INSERT INTO empleado_concepto (
                empleado_id,
                concepto_id,
                monto_manual,
                porcentaje_manual,
                cantidad,
                fecha_desde,
                fecha_hasta,
                activo,
                observacion
            )
            VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?
            )
        ";


        $stmt =
            $this->conexion->prepare(
                $sql
            );


        if (!$stmt) {

            throw new Exception(
                "No se pudo preparar el alta de la asignación: "
                . $this->conexion->error
            );
        }


        $stmt->bind_param(
            "iidddssis",
            $empleadoId,
            $conceptoId,
            $montoManual,
            $porcentajeManual,
            $cantidad,
            $fechaDesde,
            $fechaHasta,
            $activo,
            $observacion
        );


        if (!$stmt->execute()) {

            $error =
                $stmt->error;


            $stmt->close();


            throw new Exception(
                "No se pudo guardar la asignación del concepto: "
                . $error
            );
        }


        $idAsignacion =
            (int)$stmt->insert_id;


        $stmt->close();


        return $idAsignacion;
    }


    /*
    |--------------------------------------------------------------------------
    | ACTUALIZAR CONCEPTO POR EMPLEADO
    |--------------------------------------------------------------------------
    */

    public function actualizarConceptoEmpleado(
        $id,
        $datos
    ) {
        $id =
            (int)$id;


        $empleadoId =
            (int)(
                $datos['empleado_id']
                ?? 0
            );


        $conceptoId =
            (int)(
                $datos['concepto_id']
                ?? 0
            );


        $montoManual =
            (float)(
                $datos['monto_manual']
                ?? 0
            );


        $porcentajeManual =
            (float)(
                $datos['porcentaje_manual']
                ?? 0
            );


        $cantidad =
            (float)(
                $datos['cantidad']
                ?? 1
            );


        $fechaDesde =
            trim(
                (string)(
                    $datos['fecha_desde']
                    ?? ''
                )
            );


        $fechaHasta =
            $datos['fecha_hasta']
            ?? null;


        if ($fechaHasta !== null) {

            $fechaHasta =
                trim(
                    (string)$fechaHasta
                );


            if ($fechaHasta === '') {

                $fechaHasta =
                    null;
            }
        }


        $observacion =
            $datos['observacion']
            ?? null;


        if (
            $id <= 0
            ||
            $empleadoId <= 0
            ||
            $conceptoId <= 0
        ) {

            throw new Exception(
                "Datos inválidos para actualizar la asignación del concepto."
            );
        }


        /*
        |--------------------------------------------------------------------------
        | INTEGRIDAD CON HISTORIAL LABORAL
        |--------------------------------------------------------------------------
        |
        | La edición de una asignación histórica puede realizarse aunque el
        | empleado actualmente esté inactivo. Lo importante es que su vigencia
        | quede contenida dentro de un período laboral real.
        |
        */

        $this->validarVigenciaConceptoEnPeriodoLaboral(
            $empleadoId,
            $fechaDesde,
            $fechaHasta
        );


        /*
        |--------------------------------------------------------------------------
        | DEFENSA CONTRA SOLAPAMIENTOS
        |--------------------------------------------------------------------------
        */

        $asignacionActual =
            $this->obtenerConceptoEmpleadoPorId(
                $id
            );


        if (!$asignacionActual) {

            throw new Exception(
                "La asignación que intenta actualizar no existe."
            );
        }


        if (
            (int)(
                $asignacionActual['activo']
                ?? 0
            ) === 1
            &&
            $this->existeAsignacionConceptoSolapada(
                $empleadoId,
                $conceptoId,
                $fechaDesde,
                $fechaHasta,
                $id
            )
        ) {

            throw new Exception(
                "Ya existe otra asignación activa del mismo concepto con una vigencia superpuesta."
            );
        }


        $sql = "
            UPDATE empleado_concepto

            SET
                empleado_id = ?,
                concepto_id = ?,
                monto_manual = ?,
                porcentaje_manual = ?,
                cantidad = ?,
                fecha_desde = ?,
                fecha_hasta = ?,
                observacion = ?

            WHERE id = ?
        ";


        $stmt =
            $this->conexion->prepare(
                $sql
            );


        if (!$stmt) {

            throw new Exception(
                "No se pudo preparar la actualización de la asignación: "
                . $this->conexion->error
            );
        }


        $stmt->bind_param(
            "iidddsssi",
            $empleadoId,
            $conceptoId,
            $montoManual,
            $porcentajeManual,
            $cantidad,
            $fechaDesde,
            $fechaHasta,
            $observacion,
            $id
        );


        if (!$stmt->execute()) {

            $error =
                $stmt->error;


            $stmt->close();


            throw new Exception(
                "No se pudo actualizar la asignación del concepto: "
                . $error
            );
        }


        $stmt->close();


        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | CAMBIAR ESTADO DE CONCEPTO POR EMPLEADO
    |--------------------------------------------------------------------------
    */

    public function cambiarEstadoConceptoEmpleado(
        $id,
        $activo
    ) {
        $id =
            (int)$id;

        $activo =
            (int)$activo;


        /*
        |--------------------------------------------------------------------------
        | VALIDAR ESTADO
        |--------------------------------------------------------------------------
        */

        if (
            $id <= 0
            ||
            (
                $activo !== 0
                &&
                $activo !== 1
            )
        ) {

            throw new Exception(
                "Estado de asignación no válido."
            );
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDACIONES AL ACTIVAR
        |--------------------------------------------------------------------------
        */

        if ($activo === 1) {

            $asignacion =
                $this->obtenerConceptoEmpleadoPorId(
                    $id
                );


            if (!$asignacion) {

                throw new Exception(
                    "La asignación seleccionada no existe."
                );
            }


            $empleadoId =
                (int)(
                    $asignacion['empleado_id']
                    ?? 0
                );

            $conceptoId =
                (int)(
                    $asignacion['concepto_id']
                    ?? 0
                );

            $fechaDesde =
                trim(
                    (string)(
                        $asignacion['fecha_desde']
                        ?? ''
                    )
                );

            $fechaHasta =
                $asignacion['fecha_hasta']
                ?? null;


            if (!$this->empleadoActivoExiste($empleadoId)) {

                throw new Exception(
                    "No se puede activar la asignación porque el empleado se encuentra inactivo."
                );
            }


            if (!$this->conceptoActivoExiste($conceptoId)) {

                throw new Exception(
                    "No se puede activar la asignación porque el concepto está inactivo o ya no está habilitado para asignación a empleados."
                );
            }


            $this->validarVigenciaConceptoEnPeriodoLaboral(
                $empleadoId,
                $fechaDesde,
                $fechaHasta
            );


            if (
                $this->existeAsignacionConceptoSolapada(
                    $empleadoId,
                    $conceptoId,
                    $fechaDesde,
                    $fechaHasta,
                    $id
                )
            ) {

                throw new Exception(
                    "No se puede activar esta asignación porque ya existe otra asignación activa del mismo concepto con una vigencia superpuesta."
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | ACTUALIZAR
        |--------------------------------------------------------------------------
        */

        $sql = "
            UPDATE empleado_concepto
            SET activo = ?
            WHERE id = ?
        ";


        $stmt =
            $this->conexion->prepare(
                $sql
            );


        if (!$stmt) {

            throw new Exception(
                "No se pudo preparar el cambio de estado de la asignación: "
                . $this->conexion->error
            );
        }


        $stmt->bind_param(
            "ii",
            $activo,
            $id
        );


        if (!$stmt->execute()) {

            $error =
                $stmt->error;


            $stmt->close();


            throw new Exception(
                "No se pudo actualizar el estado de la asignación: "
                . $error
            );
        }


        $stmt->close();


        return true;
    }

}