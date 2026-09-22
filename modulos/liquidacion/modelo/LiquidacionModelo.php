<?php

class LiquidacionModelo
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
    | LISTAR LIQUIDACIONES
    |--------------------------------------------------------------------------
    */

    public function listar(
        $periodo = "",
        $tipo = "",
        $estado = ""
    ) {
        $sql = "
            SELECT
                id,
                tipo_liquidacion,
                periodo,
                fecha_liquidacion,
                descripcion,
                estado,
                created_at
            FROM liquidacion
            WHERE 1 = 1
        ";

        $params = [];
        $types = "";


        if ($periodo !== "") {

            $sql .= "
                AND periodo = ?
            ";

            $params[] = $periodo;
            $types .= "s";
        }


        if ($tipo !== "") {

            $sql .= "
                AND tipo_liquidacion = ?
            ";

            $params[] = $tipo;
            $types .= "s";
        }


        if ($estado !== "") {

            $sql .= "
                AND estado = ?
            ";

            $params[] = $estado;
            $types .= "s";
        }


        $sql .= "
            ORDER BY id DESC
        ";


        $stmt =
            $this->conexion->prepare(
                $sql
            );


        if (!$stmt) {

            throw new Exception(
                "Error al preparar la consulta de liquidaciones: "
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
                "Error al consultar las liquidaciones: "
                . $error
            );
        }


        $resultado =
            $stmt->get_result();


        $liquidaciones = [];


        while (
            $fila =
                $resultado->fetch_assoc()
        ) {

            $liquidaciones[] =
                $fila;
        }


        $stmt->close();


        return $liquidaciones;
    }


    /*
    |--------------------------------------------------------------------------
    | OBTENER LIQUIDACIÓN POR ID
    |--------------------------------------------------------------------------
    */

    public function obtenerPorId($id)
    {
        $sql = "
            SELECT
                id,
                tipo_liquidacion,
                periodo,
                fecha_liquidacion,
                descripcion,
                estado,
                created_at
            FROM liquidacion
            WHERE id = ?
            LIMIT 1
        ";


        $stmt =
            $this->conexion->prepare(
                $sql
            );


        if (!$stmt) {

            throw new Exception(
                "Error al preparar la consulta de la liquidación: "
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
                "Error al consultar la liquidación: "
                . $error
            );
        }


        $resultado =
            $stmt->get_result();


        $liquidacion =
            $resultado->fetch_assoc();


        $stmt->close();


        return $liquidacion;
    }


    /*
    |--------------------------------------------------------------------------
    | TIPOS DE LIQUIDACIÓN
    |--------------------------------------------------------------------------
    */

    public function obtenerTipos()
    {
        return [
            'MENSUAL',
            'AGUINALDO',
            'COMPLEMENTARIA',
            'COMPLEMENTARIA_SAC',
            'GASTOS_PROTOCOLARES'
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDAR TIPO
    |--------------------------------------------------------------------------
    */

    public function tipoValido($tipo)
    {
        return in_array(
            $tipo,
            $this->obtenerTipos(),
            true
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ESTADOS
    |--------------------------------------------------------------------------
    */

    public function obtenerEstados()
    {
        return [
            'BORRADOR',
            'CERRADA',
            'ANULADA'
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDAR ESTADO
    |--------------------------------------------------------------------------
    */

    public function estadoValido($estado)
    {
        return in_array(
            $estado,
            $this->obtenerEstados(),
            true
        );
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDAR PERÍODO
    |--------------------------------------------------------------------------
    */

    public function periodoValido($periodo)
    {
        if ($periodo === "") {

            return false;
        }


        return (
            preg_match(
                '/^\d{4}-(0[1-9]|1[0-2])$/',
                $periodo
            ) === 1
        );
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDAR FECHA
    |--------------------------------------------------------------------------
    */

    public function fechaValida($fecha)
    {
        if ($fecha === "") {

            return false;
        }


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


    /*
    |--------------------------------------------------------------------------
    | EXISTE LIQUIDACIÓN POR PERÍODO Y TIPO
    |--------------------------------------------------------------------------
    |
    | Las liquidaciones ANULADAS no bloquean una nueva del mismo
    | tipo y período.
    |
    |--------------------------------------------------------------------------
    */

    public function existePorPeriodoYTipo(
        $periodo,
        $tipo,
        $idExcluir = null
    ) {
        $sql = "
            SELECT id
            FROM liquidacion
            WHERE periodo = ?
              AND tipo_liquidacion = ?
              AND estado <> 'ANULADA'
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
                "Error al preparar la validación de liquidación duplicada: "
                . $this->conexion->error
            );
        }


        if ($idExcluir === null) {

            $stmt->bind_param(
                "ss",
                $periodo,
                $tipo
            );

        } else {

            $stmt->bind_param(
                "ssi",
                $periodo,
                $tipo,
                $idExcluir
            );
        }


        if (!$stmt->execute()) {

            $error =
                $stmt->error;

            $stmt->close();

            throw new Exception(
                "Error al validar la liquidación: "
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
    | GUARDAR LIQUIDACIÓN
    |--------------------------------------------------------------------------
    */

    public function guardar($datos)
    {
        $sql = "
            INSERT INTO liquidacion (
                tipo_liquidacion,
                periodo,
                fecha_liquidacion,
                descripcion,
                estado
            )
            VALUES (
                ?, ?, ?, ?, ?
            )
        ";


        $stmt =
            $this->conexion->prepare(
                $sql
            );


        if (!$stmt) {

            throw new Exception(
                "No se pudo preparar el alta de la liquidación: "
                . $this->conexion->error
            );
        }


        $tipo =
            $datos['tipo_liquidacion'];

        $periodo =
            $datos['periodo'];

        $fecha =
            $datos['fecha_liquidacion'];

        $descripcion =
            $datos['descripcion'];

        $estado =
            $datos['estado'];


        $stmt->bind_param(
            "sssss",
            $tipo,
            $periodo,
            $fecha,
            $descripcion,
            $estado
        );


        if (!$stmt->execute()) {

            $error =
                $stmt->error;

            $stmt->close();

            throw new Exception(
                "No se pudo guardar la liquidación: "
                . $error
            );
        }


        $idLiquidacion =
            $stmt->insert_id;


        $stmt->close();


        return $idLiquidacion;
    }


    /*
    |--------------------------------------------------------------------------
    | CONCEPTOS ACTIVOS INDEXADOS POR CÓDIGO
    |--------------------------------------------------------------------------
    */

    public function obtenerConceptosActivosPorCodigo()
    {
        $sql = "
            SELECT
                id,
                codigo,
                nombre,
                categoria,
                forma_calculo,
                porcentaje,
                monto_fijo,
                requiere_manual,
                base_calculo,
                orden_calculo,
                aplica_sac,
                visible_recibo,
                activo,
                descripcion,
                fecha_desde,
                fecha_hasta

            FROM concepto

            WHERE activo = 1

            ORDER BY
                orden_calculo ASC,
                codigo ASC
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

            $codigo =
                (string)$fila['codigo'];


            $conceptos[$codigo] = [

                'id' =>
                    (int)$fila['id'],

                'codigo' =>
                    $codigo,

                'nombre' =>
                    $fila['nombre'],

                'categoria' =>
                    $fila['categoria'],

                'forma_calculo' =>
                    $fila['forma_calculo'],

                'porcentaje' =>
                    (float)$fila['porcentaje'],

                'monto_fijo' =>
                    (float)$fila['monto_fijo'],

                'requiere_manual' =>
                    (int)$fila['requiere_manual'],

                'base_calculo' =>
                    $fila['base_calculo'],

                'orden_calculo' =>
                    (int)$fila['orden_calculo'],

                'aplica_sac' =>
                    (int)$fila['aplica_sac'],

                'visible_recibo' =>
                    (int)$fila['visible_recibo'],

                'descripcion' =>
                    $fila['descripcion'],

                'fecha_desde' =>
                    $fila['fecha_desde'],

                'fecha_hasta' =>
                    $fila['fecha_hasta']
            ];
        }


        return $conceptos;
    }


    
    /*
    |--------------------------------------------------------------------------
    | EMPLEADOS PARA LIQUIDACIÓN MENSUAL
    |--------------------------------------------------------------------------
    |
    | Fuente de elegibilidad:
    |
    | empleado_periodo_laboral
    |
    | Ya no se utiliza empleado.activo para decidir quién participa.
    |
    | Reglas:
    |
    | - debe existir relación laboral dentro del mes de la liquidación;
    | - los días habilitados se calculan con convenio mensual de 30 días;
    | - una novedad puede reducir los días, pero nunca superar el máximo laboral;
    | - una persona actualmente inactiva puede liquidarse si trabajó en el período;
    | - una persona actualmente activa queda excluida si no tenía relación laboral
    |   durante el período.
    |
    */

    public function obtenerEmpleadosParaMensual(
        $liquidacionId
    ) {
        $liquidacionId =
            (int)$liquidacionId;


        if ($liquidacionId <= 0) {

            throw new Exception(
                "ID de liquidación inválido para obtener empleados de la liquidación mensual."
            );
        }


        $liquidacion =
            $this->obtenerPorId(
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


        if ($tipoLiquidacion !== 'MENSUAL') {

            throw new Exception(
                "La liquidación seleccionada no corresponde a una liquidación MENSUAL."
            );
        }


        $periodo =
            trim(
                (string)(
                    $liquidacion['periodo']
                    ?? ''
                )
            );


        $rangoMes =
            $this->obtenerRangoMes(
                $periodo
            );


        $desdeMes =
            $rangoMes['desde'];

        $hastaMes =
            $rangoMes['hasta'];


        $sql = "
            SELECT
                e.*,

                c.codigo AS categoria_codigo,
                c.nombre AS categoria_nombre,

                ln.id AS novedad_id,
                ln.dias_liquidados,
                ln.aplica_presentismo,

                COALESCE(
                    ln.dias_sac,
                    180
                ) AS dias_sac,

                COALESCE(
                    ln.observacion,
                    ''
                ) AS observacion_novedad

            FROM empleado e

            INNER JOIN categoria c
                ON e.categoria_id = c.id

            LEFT JOIN liquidacion_novedad ln
                ON ln.empleado_id = e.id
               AND ln.liquidacion_id = ?

            WHERE EXISTS (
                SELECT 1

                FROM empleado_periodo_laboral epl

                WHERE epl.empleado_id = e.id

                  AND epl.fecha_desde <= ?

                  AND (
                        epl.fecha_hasta IS NULL
                        OR epl.fecha_hasta >= ?
                  )
            )

            ORDER BY
                e.apellido ASC,
                e.nombre ASC
        ";


        $stmt =
            $this->conexion->prepare(
                $sql
            );


        if (!$stmt) {

            throw new Exception(
                "Error al preparar los empleados de la liquidación mensual: "
                . $this->conexion->error
            );
        }


        $stmt->bind_param(
            "iss",
            $liquidacionId,
            $hastaMes,
            $desdeMes
        );


        if (!$stmt->execute()) {

            $error =
                $stmt->error;

            $stmt->close();

            throw new Exception(
                "Error al consultar los empleados de la liquidación mensual: "
                . $error
            );
        }


        $resultado =
            $stmt->get_result();


        $empleados =
            [];


        while (
            $fila =
                $resultado->fetch_assoc()
        ) {

            $empleadoId =
                (int)(
                    $fila['id']
                    ?? 0
                );


            if ($empleadoId <= 0) {

                continue;
            }


            $diasHabilitados =
                $this->calcularDiasLaboralesHabilitadosMes(
                    $empleadoId,
                    $periodo
                );


            if ($diasHabilitados <= 0) {

                continue;
            }


            $tieneNovedad =
                !empty(
                    $fila['novedad_id']
                );


            $diasLiquidados =
                $tieneNovedad
                    ? (int)(
                        $fila['dias_liquidados']
                        ?? $diasHabilitados
                    )
                    : $diasHabilitados;


            /*
            |--------------------------------------------------------------------------
            | NORMALIZAR DÍAS
            |--------------------------------------------------------------------------
            |
            | Protege también novedades históricas que pudieran contener un valor
            | mayor al máximo laboral actual.
            |
            */

            if ($diasLiquidados < 0) {

                $diasLiquidados =
                    0;
            }


            if ($diasLiquidados > $diasHabilitados) {

                $diasLiquidados =
                    $diasHabilitados;
            }


            $aplicaPresentismo =
                $tieneNovedad
                    ? (int)(
                        $fila['aplica_presentismo']
                        ?? 1
                    )
                    : 1;


            if ($diasLiquidados === 0) {

                $aplicaPresentismo =
                    0;
            }


            $fila['dias_liquidados'] =
                $diasLiquidados;

            $fila['dias_laborales_habilitados'] =
                $diasHabilitados;

            $fila['limite_dias_liquidables'] =
                $diasHabilitados;

            $fila['aplica_presentismo'] =
                $aplicaPresentismo === 1
                    ? 1
                    : 0;

            $fila['observacion_novedad'] =
                (string)(
                    $fila['observacion_novedad']
                    ?? ''
                );

            $fila['novedad_id'] =
                $tieneNovedad
                    ? (int)$fila['novedad_id']
                    : null;


            $empleados[] =
                $fila;
        }


        $stmt->close();


        return $empleados;
    }


    /*
    |--------------------------------------------------------------------------
    | EMPLEADOS PARA NOVEDADES DE LIQUIDACIÓN
    |--------------------------------------------------------------------------
    |
    | Para MENSUAL devuelve solamente empleados con relación laboral dentro
    | del período. Los días por defecto surgen del historial laboral y no pueden
    | superar el máximo habilitado del mes.
    |
    */

    public function obtenerEmpleadosParaNovedades(
        $liquidacionId
    ) {
        $liquidacionId =
            (int)$liquidacionId;


        if ($liquidacionId <= 0) {

            throw new Exception(
                "ID de liquidación inválido para cargar novedades."
            );
        }


        return
            $this->obtenerEmpleadosParaMensual(
                $liquidacionId
            );
    }


    /*
    |--------------------------------------------------------------------------
    | OBTENER NOVEDAD DE UN EMPLEADO
    |--------------------------------------------------------------------------
    */

    public function obtenerNovedad(
        $liquidacionId,
        $empleadoId
    ) {
        $liquidacionId =
            (int)$liquidacionId;

        $empleadoId =
            (int)$empleadoId;


        if (
            $liquidacionId <= 0
            ||
            $empleadoId <= 0
        ) {

            return null;
        }


        $sql = "
            SELECT
                id,
                liquidacion_id,
                empleado_id,
                dias_liquidados,
                aplica_presentismo,
                dias_sac,
                observacion,
                created_at,
                updated_at

            FROM liquidacion_novedad

            WHERE liquidacion_id = ?
              AND empleado_id = ?

            LIMIT 1
        ";


        $stmt =
            $this->conexion->prepare(
                $sql
            );


        if (!$stmt) {

            throw new Exception(
                "Error al preparar la consulta de la novedad: "
                . $this->conexion->error
            );
        }


        $stmt->bind_param(
            "ii",
            $liquidacionId,
            $empleadoId
        );


        if (!$stmt->execute()) {

            $error =
                $stmt->error;

            $stmt->close();

            throw new Exception(
                "Error al consultar la novedad del empleado: "
                . $error
            );
        }


        $fila =
            $stmt
                ->get_result()
                ->fetch_assoc();


        $stmt->close();


        if (!$fila) {

            return null;
        }


        return [
            'id' =>
                (int)$fila['id'],

            'liquidacion_id' =>
                (int)$fila['liquidacion_id'],

            'empleado_id' =>
                (int)$fila['empleado_id'],

            'dias_liquidados' =>
                (int)$fila['dias_liquidados'],

            'aplica_presentismo' =>
                (int)$fila['aplica_presentismo'],

            'dias_sac' =>
                $fila['dias_sac'] !== null
                    ? (int)$fila['dias_sac']
                    : null,

            'observacion' =>
                $fila['observacion'],

            'created_at' =>
                $fila['created_at'],

            'updated_at' =>
                $fila['updated_at']
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | GUARDAR / ACTUALIZAR NOVEDAD
    |--------------------------------------------------------------------------
    |
    | Se utiliza UPSERT porque cada empleado puede tener una sola novedad
    | dentro de una liquidación.
    |
    */

    public function guardarNovedad(
        $liquidacionId,
        $empleadoId,
        $diasLiquidados,
        $aplicaPresentismo,
        $observacion = null
    ) {
        $liquidacionId =
            (int)$liquidacionId;

        $empleadoId =
            (int)$empleadoId;

        $diasLiquidados =
            (int)$diasLiquidados;

        $aplicaPresentismo =
            (int)$aplicaPresentismo === 1
                ? 1
                : 0;

        $observacion =
            trim(
                (string)($observacion ?? '')
            );


        if (
            $liquidacionId <= 0
            ||
            $empleadoId <= 0
        ) {

            throw new Exception(
                "Datos inválidos para guardar la novedad de liquidación."
            );
        }


        if (
            $diasLiquidados < 0
            ||
            $diasLiquidados > 30
        ) {

            throw new Exception(
                "Los días liquidados deben estar entre 0 y 30."
            );
        }


        /*
        |--------------------------------------------------------------------------
        | MÁXIMO LABORAL PARA LIQUIDACIÓN MENSUAL
        |--------------------------------------------------------------------------
        |
        | No alcanza con validar 0..30. Un request manipulado tampoco puede
        | liquidar más días que los realmente habilitados por el historial laboral.
        |
        */

        $liquidacion =
            $this->obtenerPorId(
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
            $tipoLiquidacion === 'MENSUAL'
            ||
            $tipoLiquidacion === 'COMPLEMENTARIA'
        ) {

            $diasHabilitados =
                $this->calcularDiasLaboralesHabilitadosMes(
                    $empleadoId,
                    $liquidacion['periodo']
                    ?? ''
                );


            if ($diasHabilitados <= 0) {

                throw new Exception(
                    "El empleado no posee un período laboral habilitado dentro del mes de esta liquidación."
                );
            }


            if (
                $tipoLiquidacion === 'COMPLEMENTARIA'
                &&
                $diasLiquidados < 1
            ) {

                throw new Exception(
                    "Un empleado incluido en una COMPLEMENTARIA debe tener al menos 1 día a liquidar."
                );
            }


            if ($diasLiquidados > $diasHabilitados) {

                throw new Exception(
                    "El empleado tiene un máximo de "
                    . $diasHabilitados
                    . " días laborales habilitados para el período "
                    . ($liquidacion['periodo'] ?? '')
                    . "."
                );
            }
        }


        if (
            strlen($observacion) > 255
        ) {

            throw new Exception(
                "La observación de la novedad no puede superar los 255 caracteres."
            );
        }


        if (
            $diasLiquidados === 0
        ) {

            $aplicaPresentismo = 0;
        }


        $observacionBD =
            $observacion === ''
                ? null
                : $observacion;


        $sql = "
            INSERT INTO liquidacion_novedad (
                liquidacion_id,
                empleado_id,
                dias_liquidados,
                aplica_presentismo,
                observacion
            )
            VALUES (
                ?, ?, ?, ?, ?
            )

            ON DUPLICATE KEY UPDATE
                dias_liquidados =
                    VALUES(dias_liquidados),

                aplica_presentismo =
                    VALUES(aplica_presentismo),

                observacion =
                    VALUES(observacion),

                updated_at =
                    CURRENT_TIMESTAMP
        ";


        $stmt =
            $this->conexion->prepare(
                $sql
            );


        if (!$stmt) {

            throw new Exception(
                "Error al preparar la novedad de liquidación: "
                . $this->conexion->error
            );
        }


        $stmt->bind_param(
            "iiiis",
            $liquidacionId,
            $empleadoId,
            $diasLiquidados,
            $aplicaPresentismo,
            $observacionBD
        );


        if (!$stmt->execute()) {

            $error =
                $stmt->error;

            $stmt->close();

            throw new Exception(
                "No se pudo guardar la novedad de liquidación: "
                . $error
            );
        }


        $stmt->close();


        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | ELIMINAR NOVEDAD
    |--------------------------------------------------------------------------
    |
    | Se utiliza cuando el empleado vuelve a los valores predeterminados:
    |
    | 30 días + Presentismo Sí + Sin observación.
    |
    | Así la tabla guarda únicamente excepciones.
    |
    */

    public function eliminarNovedad(
        $liquidacionId,
        $empleadoId
    ) {
        $liquidacionId =
            (int)$liquidacionId;

        $empleadoId =
            (int)$empleadoId;


        if (
            $liquidacionId <= 0
            ||
            $empleadoId <= 0
        ) {

            return false;
        }


        $sql = "
            DELETE FROM liquidacion_novedad

            WHERE liquidacion_id = ?
              AND empleado_id = ?
        ";


        $stmt =
            $this->conexion->prepare(
                $sql
            );


        if (!$stmt) {

            throw new Exception(
                "Error al preparar la eliminación de la novedad: "
                . $this->conexion->error
            );
        }


        $stmt->bind_param(
            "ii",
            $liquidacionId,
            $empleadoId
        );


        if (!$stmt->execute()) {

            $error =
                $stmt->error;

            $stmt->close();

            throw new Exception(
                "No se pudo eliminar la novedad de liquidación: "
                . $error
            );
        }


        $stmt->close();


        return true;
    }




    /*
    |--------------------------------------------------------------------------
    | PARTICIPANTES DE LIQUIDACIONES COMPLEMENTARIAS
    |--------------------------------------------------------------------------
    |
    | liquidacion_participante define qué empleados entran en:
    |
    | - COMPLEMENTARIA
    | - COMPLEMENTARIA_SAC
    |
    | Los días y demás novedades siguen en liquidacion_novedad.
    |
    */

    public function guardarParticipante(
        $liquidacionId,
        $empleadoId
    ) {
        $liquidacionId = (int)$liquidacionId;
        $empleadoId = (int)$empleadoId;

        if ($liquidacionId <= 0 || $empleadoId <= 0) {
            throw new Exception(
                "Datos inválidos para seleccionar al empleado."
            );
        }

        $sql = "
            INSERT INTO liquidacion_participante (
                liquidacion_id,
                empleado_id
            )
            VALUES (?, ?)

            ON DUPLICATE KEY UPDATE
                empleado_id = VALUES(empleado_id)
        ";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                "Error al preparar la selección del empleado: "
                . $this->conexion->error
            );
        }

        $stmt->bind_param("ii", $liquidacionId, $empleadoId);

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();

            throw new Exception(
                "No se pudo seleccionar al empleado: "
                . $error
            );
        }

        $stmt->close();

        return true;
    }


    public function eliminarParticipante(
        $liquidacionId,
        $empleadoId
    ) {
        $liquidacionId = (int)$liquidacionId;
        $empleadoId = (int)$empleadoId;

        if ($liquidacionId <= 0 || $empleadoId <= 0) {
            return false;
        }

        $sql = "
            DELETE FROM liquidacion_participante
            WHERE liquidacion_id = ?
              AND empleado_id = ?
        ";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                "Error al preparar la eliminación del participante: "
                . $this->conexion->error
            );
        }

        $stmt->bind_param("ii", $liquidacionId, $empleadoId);

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();

            throw new Exception(
                "No se pudo quitar al empleado de la liquidación: "
                . $error
            );
        }

        $stmt->close();

        return true;
    }


    public function contarParticipantes($liquidacionId)
    {
        $liquidacionId = (int)$liquidacionId;

        $sql = "
            SELECT COUNT(*) AS total
            FROM liquidacion_participante
            WHERE liquidacion_id = ?
        ";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                "Error al preparar el conteo de participantes: "
                . $this->conexion->error
            );
        }

        $stmt->bind_param("i", $liquidacionId);

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();

            throw new Exception(
                "Error al contar los participantes: "
                . $error
            );
        }

        $fila = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return (int)($fila['total'] ?? 0);
    }


    /*
    |--------------------------------------------------------------------------
    | PERSONAL PARA COMPLEMENTARIA DE HABERES
    |--------------------------------------------------------------------------
    |
    | Se muestran empleados que tengan al menos un período laboral que se
    | superponga con el mes de la liquidación, aunque actualmente estén inactivos.
    |
    | La fuente de vigencia es empleado_periodo_laboral.
    |
    */

    public function obtenerEmpleadosParaComplementaria($liquidacionId)
    {
        $liquidacionId =
            (int)$liquidacionId;


        if ($liquidacionId <= 0) {

            throw new Exception(
                "ID de liquidación inválido para seleccionar personal."
            );
        }


        $liquidacion =
            $this->obtenerPorId(
                $liquidacionId
            );


        if (!$liquidacion) {

            throw new Exception(
                "La liquidación seleccionada no existe."
            );
        }


        $tipo =
            strtoupper(
                trim(
                    (string)(
                        $liquidacion['tipo_liquidacion']
                        ?? ''
                    )
                )
            );


        if ($tipo !== 'COMPLEMENTARIA') {

            throw new Exception(
                "La liquidación seleccionada no corresponde a una COMPLEMENTARIA de haberes."
            );
        }


        $periodo =
            trim(
                (string)(
                    $liquidacion['periodo']
                    ?? ''
                )
            );


        $rango =
            $this->obtenerRangoMes(
                $periodo
            );


        $desde =
            $rango['desde'];

        $hasta =
            $rango['hasta'];


        /*
        |--------------------------------------------------------------------------
        | EMPLEADOS CON RELACIÓN LABORAL EN EL MES
        |--------------------------------------------------------------------------
        |
        | La elegibilidad se determina exclusivamente con empleado_periodo_laboral.
        | El estado actual del empleado no excluye a quien sí trabajó dentro del
        | período de la complementaria.
        |
        */

        $sql = "
            SELECT
                e.*,

                c.codigo AS categoria_codigo,
                c.nombre AS categoria_nombre,

                lp.id AS participante_id,

                ln.id AS novedad_id,
                ln.dias_liquidados,
                ln.aplica_presentismo,

                COALESCE(
                    ln.observacion,
                    ''
                ) AS observacion_novedad

            FROM empleado e

            INNER JOIN categoria c
                ON e.categoria_id = c.id

            LEFT JOIN liquidacion_participante lp
                ON lp.empleado_id = e.id
               AND lp.liquidacion_id = ?

            LEFT JOIN liquidacion_novedad ln
                ON ln.empleado_id = e.id
               AND ln.liquidacion_id = ?

            WHERE EXISTS (
                SELECT 1

                FROM empleado_periodo_laboral epl

                WHERE epl.empleado_id = e.id

                  AND epl.fecha_desde <= ?

                  AND (
                        epl.fecha_hasta IS NULL
                        OR epl.fecha_hasta >= ?
                  )
            )

            ORDER BY
                e.apellido ASC,
                e.nombre ASC
        ";


        $stmt =
            $this->conexion->prepare(
                $sql
            );


        if (!$stmt) {

            throw new Exception(
                "Error al preparar el personal de la complementaria: "
                . $this->conexion->error
            );
        }


        $stmt->bind_param(
            "iiss",
            $liquidacionId,
            $liquidacionId,
            $hasta,
            $desde
        );


        if (!$stmt->execute()) {

            $error =
                $stmt->error;

            $stmt->close();

            throw new Exception(
                "Error al consultar el personal de la complementaria: "
                . $error
            );
        }


        $resultado =
            $stmt->get_result();


        $empleados =
            [];


        while (
            $fila =
                $resultado->fetch_assoc()
        ) {

            $empleadoId =
                (int)(
                    $fila['id']
                    ?? 0
                );


            $diasHabilitados =
                $this->calcularDiasLaboralesHabilitadosMes(
                    $empleadoId,
                    $periodo
                );


            if ($diasHabilitados <= 0) {

                continue;
            }


            $incluido =
                !empty(
                    $fila['participante_id']
                );


            $tieneNovedad =
                !empty(
                    $fila['novedad_id']
                );


            /*
            |--------------------------------------------------------------------------
            | DÍAS A LIQUIDAR
            |--------------------------------------------------------------------------
            |
            | Si no existe una novedad guardada, el valor predeterminado ya no es
            | necesariamente 30: es el máximo laboral habilitado para ese empleado.
            |
            */

            $diasLiquidados =
                $tieneNovedad
                    ? (int)(
                        $fila['dias_liquidados']
                        ?? $diasHabilitados
                    )
                    : $diasHabilitados;


            if (
                $diasLiquidados < 1
                ||
                $diasLiquidados > $diasHabilitados
            ) {

                $diasLiquidados =
                    $diasHabilitados;
            }


            $aplicaPresentismo =
                $tieneNovedad
                    ? (int)(
                        $fila['aplica_presentismo']
                        ?? 1
                    )
                    : 1;


            $fila['incluido'] =
                $incluido
                    ? 1
                    : 0;

            $fila['participante_id'] =
                $incluido
                    ? (int)$fila['participante_id']
                    : null;

            $fila['novedad_id'] =
                $tieneNovedad
                    ? (int)$fila['novedad_id']
                    : null;

            $fila['dias_laborales_habilitados'] =
                $diasHabilitados;

            $fila['limite_dias_liquidables'] =
                $diasHabilitados;

            $fila['dias_liquidados'] =
                $diasLiquidados;

            $fila['aplica_presentismo'] =
                $aplicaPresentismo === 1
                    ? 1
                    : 0;

            $fila['observacion_novedad'] =
                (string)(
                    $fila['observacion_novedad']
                    ?? ''
                );


            $empleados[] =
                $fila;
        }


        $stmt->close();


        return $empleados;
    }


    /*
    |--------------------------------------------------------------------------
    | PARTICIPANTES LISTOS PARA PROCESAR
    |--------------------------------------------------------------------------
    */

    public function obtenerParticipantesParaLiquidar($liquidacionId)
    {
        $liquidacionId =
            (int)$liquidacionId;


        if ($liquidacionId <= 0) {

            throw new Exception(
                "ID de liquidación inválido para obtener participantes."
            );
        }


        $liquidacion =
            $this->obtenerPorId(
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


        $periodo =
            trim(
                (string)(
                    $liquidacion['periodo']
                    ?? ''
                )
            );


        $sql = "
            SELECT
                e.*,

                c.codigo AS categoria_codigo,
                c.nombre AS categoria_nombre,

                lp.id AS participante_id,

                ln.id AS novedad_id,
                ln.dias_liquidados,
                ln.aplica_presentismo,

                COALESCE(
                    ln.dias_sac,
                    180
                ) AS dias_sac,

                COALESCE(
                    ln.observacion,
                    ''
                ) AS observacion_novedad

            FROM liquidacion_participante lp

            INNER JOIN empleado e
                ON lp.empleado_id = e.id

            INNER JOIN categoria c
                ON e.categoria_id = c.id

            LEFT JOIN liquidacion_novedad ln
                ON ln.empleado_id = e.id
               AND ln.liquidacion_id = lp.liquidacion_id

            WHERE lp.liquidacion_id = ?

            ORDER BY
                e.apellido ASC,
                e.nombre ASC
        ";


        $stmt =
            $this->conexion->prepare(
                $sql
            );


        if (!$stmt) {

            throw new Exception(
                "Error al preparar los participantes: "
                . $this->conexion->error
            );
        }


        $stmt->bind_param(
            "i",
            $liquidacionId
        );


        if (!$stmt->execute()) {

            $error =
                $stmt->error;

            $stmt->close();

            throw new Exception(
                "Error al consultar los participantes: "
                . $error
            );
        }


        $resultado =
            $stmt->get_result();


        $empleados =
            [];


        while (
            $fila =
                $resultado->fetch_assoc()
        ) {

            $empleadoId =
                (int)(
                    $fila['id']
                    ?? 0
                );


            $fila['participante_id'] =
                (int)$fila['participante_id'];


            /*
            |--------------------------------------------------------------------------
            | COMPLEMENTARIA DE HABERES
            |--------------------------------------------------------------------------
            |
            | Se vuelve a validar el historial laboral justo antes de procesar.
            | Esto evita que un participante antiguo o manipulado pueda liquidarse
            | fuera del período laboral o por más días de los permitidos.
            |
            */

            if ($tipoLiquidacion === 'COMPLEMENTARIA') {

                $diasHabilitados =
                    $this->calcularDiasLaboralesHabilitadosMes(
                        $empleadoId,
                        $periodo
                    );


                if ($diasHabilitados <= 0) {

                    $stmt->close();

                    throw new Exception(
                        "El empleado "
                        . trim(
                            (string)($fila['apellido'] ?? '')
                            . ", "
                            . (string)($fila['nombre'] ?? '')
                        )
                        . " no posee relación laboral habilitada en el período "
                        . $periodo
                        . "."
                    );
                }


                $tieneNovedad =
                    !empty(
                        $fila['novedad_id']
                    );


                $diasLiquidados =
                    $tieneNovedad
                        ? (int)(
                            $fila['dias_liquidados']
                            ?? $diasHabilitados
                        )
                        : $diasHabilitados;


                if (
                    $diasLiquidados < 1
                    ||
                    $diasLiquidados > $diasHabilitados
                ) {

                    $stmt->close();

                    throw new Exception(
                        "El empleado "
                        . trim(
                            (string)($fila['apellido'] ?? '')
                            . ", "
                            . (string)($fila['nombre'] ?? '')
                        )
                        . " tiene un máximo de "
                        . $diasHabilitados
                        . " días laborales habilitados para el período "
                        . $periodo
                        . "."
                    );
                }


                $fila['dias_laborales_habilitados'] =
                    $diasHabilitados;

                $fila['limite_dias_liquidables'] =
                    $diasHabilitados;

                $fila['dias_liquidados'] =
                    $diasLiquidados;

                $fila['aplica_presentismo'] =
                    $tieneNovedad
                        ? (
                            (int)(
                                $fila['aplica_presentismo']
                                ?? 1
                            ) === 1
                                ? 1
                                : 0
                        )
                        : 1;

            } else {

                /*
                | COMPLEMENTARIA_SAC y otros usos de participantes:
                | conservan sus valores de días mensuales porque su cálculo
                | específico utiliza dias_sac.
                */

                $fila['dias_laborales_habilitados'] =
                    30;

                $fila['limite_dias_liquidables'] =
                    30;

                $fila['dias_liquidados'] =
                    $fila['dias_liquidados'] !== null
                        ? (int)$fila['dias_liquidados']
                        : 30;

                $fila['aplica_presentismo'] =
                    $fila['aplica_presentismo'] !== null
                        ? (
                            (int)$fila['aplica_presentismo'] === 1
                                ? 1
                                : 0
                        )
                        : 1;
            }


            $fila['dias_sac'] =
                (int)(
                    $fila['dias_sac']
                    ?? 180
                );

            $fila['observacion_novedad'] =
                (string)(
                    $fila['observacion_novedad']
                    ?? ''
                );


            $empleados[] =
                $fila;
        }


        $stmt->close();


        return $empleados;
    }


    /*
    |--------------------------------------------------------------------------
    | EMPLEADOS PARA NOVEDADES DE SAC
    |--------------------------------------------------------------------------
    |
    | Incluye empleados que tengan al menos un período registrado en
    | empleado_periodo_laboral que se superponga con el semestre.
    |
    | El estado actual del empleado no determina por sí solo si corresponde SAC.
    |
    | Valor predeterminado: dias_sac = 180.
    |
    */

    public function obtenerEmpleadosParaSac($liquidacionId)
    {
        $liquidacionId = (int)$liquidacionId;

        if ($liquidacionId <= 0) {
            throw new Exception(
                "ID de liquidación inválido para cargar novedades de SAC."
            );
        }

        $liquidacion = $this->obtenerPorId($liquidacionId);

        if (!$liquidacion) {
            throw new Exception(
                "La liquidación seleccionada no existe."
            );
        }

        $tipo = strtoupper(
            trim((string)($liquidacion['tipo_liquidacion'] ?? ''))
        );

        if (
            $tipo !== 'AGUINALDO'
            &&
            $tipo !== 'COMPLEMENTARIA_SAC'
        ) {
            throw new Exception(
                "La liquidación seleccionada no corresponde a SAC."
            );
        }

        $rango = $this->obtenerRangoSemestreSac(
            $liquidacion['periodo'] ?? ''
        );

        $desde = $rango['desde'];
        $hastaSemestre = $rango['hasta'];

        $hastaDevengoGeneral = $hastaSemestre;

        /*
        |--------------------------------------------------------------------------
        | COMPLEMENTARIA_SAC
        |--------------------------------------------------------------------------
        |
        | Permite liquidar antes de junio/diciembre.
        | Ejemplo: en marzo se devenga solamente hasta la fecha de liquidación.
        |
        */

        if ($tipo === 'COMPLEMENTARIA_SAC') {
            $fechaLiquidacion =
                trim(
                    (string)($liquidacion['fecha_liquidacion'] ?? '')
                );

            if (!$this->fechaValida($fechaLiquidacion)) {
                throw new Exception(
                    "La fecha de la liquidación de SAC no es válida."
                );
            }

            if ($fechaLiquidacion < $desde) {
                throw new Exception(
                    "La fecha de la liquidación es anterior al semestre correspondiente."
                );
            }

            if ($fechaLiquidacion < $hastaDevengoGeneral) {
                $hastaDevengoGeneral = $fechaLiquidacion;
            }
        }

        $sql = "
            SELECT
                e.*,

                c.codigo AS categoria_codigo,
                c.nombre AS categoria_nombre,

                lp.id AS participante_id,

                ln.id AS novedad_sac_id,
                ln.dias_sac

            FROM empleado e

            INNER JOIN categoria c
                ON e.categoria_id = c.id

            LEFT JOIN liquidacion_participante lp
                ON lp.empleado_id = e.id
               AND lp.liquidacion_id = ?

            LEFT JOIN liquidacion_novedad ln
                ON ln.empleado_id = e.id
               AND ln.liquidacion_id = ?

            WHERE EXISTS (
                SELECT 1

                FROM empleado_periodo_laboral epl

                WHERE epl.empleado_id = e.id

                  AND epl.fecha_desde <= ?

                  AND (
                        epl.fecha_hasta IS NULL
                        OR epl.fecha_hasta >= ?
                  )
            )

            ORDER BY
                e.apellido ASC,
                e.nombre ASC
        ";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                "Error al preparar los empleados para SAC: "
                . $this->conexion->error
            );
        }

        $stmt->bind_param(
            "iiss",
            $liquidacionId,
            $liquidacionId,
            $hastaDevengoGeneral,
            $desde
        );

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();

            throw new Exception(
                "Error al consultar los empleados para SAC: "
                . $error
            );
        }

        $resultado = $stmt->get_result();
        $empleados = [];

        $anio =
            (int)substr(
                (string)$liquidacion['periodo'],
                0,
                4
            );

        while ($fila = $resultado->fetch_assoc()) {
            $empleadoId =
                (int)($fila['id'] ?? 0);

            if ($empleadoId <= 0) {
                continue;
            }

            $diasDevengados =
                $this->calcularDiasSacDevengados(
                    $empleadoId,
                    $desde,
                    $hastaDevengoGeneral
                );

            $diasPagados =
                $this->obtenerDiasSacPagados(
                    $empleadoId,
                    $liquidacionId,
                    (int)$rango['semestre'],
                    $anio
                );

            $diasPendientes =
                $diasDevengados
                -
                $diasPagados;

            if ($diasPendientes < 0) {
                $diasPendientes = 0;
            }

            if ($diasPendientes > 180) {
                $diasPendientes = 180;
            }

            if ($fila['dias_sac'] !== null) {
                $diasSac = (int)$fila['dias_sac'];
            } else {
                $diasSac = $diasPendientes;
            }

            if ($diasSac < 0) {
                $diasSac = 0;
            }

            if ($diasSac > $diasPendientes) {
                $diasSac = $diasPendientes;
            }

            if ($diasPendientes <= 0) {
                $estadoSac = 'YA_LIQUIDADO';
            } elseif ($diasPagados > 0) {
                $estadoSac = 'PARCIAL';
            } else {
                $estadoSac = 'PENDIENTE';
            }

            $fila['incluido'] =
                !empty($fila['participante_id'])
                    ? 1
                    : 0;

            $fila['participante_id'] =
                !empty($fila['participante_id'])
                    ? (int)$fila['participante_id']
                    : null;

            $fila['dias_sac'] = $diasSac;

            $fila['dias_sac_devengados'] =
                $diasDevengados;

            $fila['dias_sac_pagados'] =
                $diasPagados;

            $fila['dias_sac_pendientes'] =
                $diasPendientes;

            $fila['estado_sac'] =
                $estadoSac;

            $fila['puede_liquidar_sac'] =
                $diasPendientes > 0
                    ? 1
                    : 0;

            $fila['novedad_sac_id'] =
                !empty($fila['novedad_sac_id'])
                    ? (int)$fila['novedad_sac_id']
                    : null;

            $fila['semestre_desde'] =
                $desde;

            $fila['semestre_hasta'] =
                $hastaSemestre;

            $fila['devengado_hasta'] =
                $hastaDevengoGeneral;

            /*
            |--------------------------------------------------------------------------
            | ADVERTENCIA SAC
            |--------------------------------------------------------------------------
            |
            | Los días ya se obtienen del historial laboral. Si el empleado aparece
            | en esta lista es porque existe al menos un período que se superpone
            | con el rango de devengamiento.
            |
            */

            $fila['advertencia_sac'] =
                '';

            $empleados[] = $fila;
        }

        $stmt->close();

        return $empleados;
    }


    /*
    |--------------------------------------------------------------------------
    | GUARDAR / ACTUALIZAR DÍAS DE SAC
    |--------------------------------------------------------------------------
    */

    public function guardarNovedadSac(
        $liquidacionId,
        $empleadoId,
        $diasSac
    ) {
        $liquidacionId = (int)$liquidacionId;
        $empleadoId = (int)$empleadoId;
        $diasSac = (int)$diasSac;

        if (
            $liquidacionId <= 0
            ||
            $empleadoId <= 0
        ) {
            throw new Exception(
                "Datos inválidos para guardar la novedad de SAC."
            );
        }

        if (
            $diasSac < 0
            ||
            $diasSac > 180
        ) {
            throw new Exception(
                "Los días de SAC deben estar entre 0 y 180."
            );
        }

        $sql = "
            INSERT INTO liquidacion_novedad (
                liquidacion_id,
                empleado_id,
                dias_sac
            )
            VALUES (?, ?, ?)

            ON DUPLICATE KEY UPDATE
                dias_sac = VALUES(dias_sac),
                updated_at = CURRENT_TIMESTAMP
        ";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                "Error al preparar la novedad de SAC: "
                . $this->conexion->error
            );
        }

        $stmt->bind_param(
            "iii",
            $liquidacionId,
            $empleadoId,
            $diasSac
        );

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();

            throw new Exception(
                "No se pudo guardar la novedad de SAC: "
                . $error
            );
        }

        $stmt->close();

        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | RESTABLECER DÍAS DE SAC
    |--------------------------------------------------------------------------
    |
    | NULL equivale al valor predeterminado de 180 días.
    |
    */

    public function eliminarNovedadSac(
        $liquidacionId,
        $empleadoId
    ) {
        $liquidacionId = (int)$liquidacionId;
        $empleadoId = (int)$empleadoId;

        if (
            $liquidacionId <= 0
            ||
            $empleadoId <= 0
        ) {
            return false;
        }

        $sql = "
            UPDATE liquidacion_novedad
            SET
                dias_sac = NULL,
                updated_at = CURRENT_TIMESTAMP
            WHERE liquidacion_id = ?
              AND empleado_id = ?
        ";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                "Error al preparar el restablecimiento de SAC: "
                . $this->conexion->error
            );
        }

        $stmt->bind_param(
            "ii",
            $liquidacionId,
            $empleadoId
        );

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();

            throw new Exception(
                "No se pudo restablecer la novedad de SAC: "
                . $error
            );
        }

        $stmt->close();

        $sqlLimpiar = "
            DELETE FROM liquidacion_novedad
            WHERE liquidacion_id = ?
              AND empleado_id = ?
              AND dias_sac IS NULL
              AND dias_liquidados = 30
              AND aplica_presentismo = 1
              AND (
                    observacion IS NULL
                    OR TRIM(observacion) = ''
              )
        ";

        $stmtLimpiar = $this->conexion->prepare($sqlLimpiar);

        if (!$stmtLimpiar) {
            throw new Exception(
                "Error al preparar la limpieza de la novedad de SAC: "
                . $this->conexion->error
            );
        }

        $stmtLimpiar->bind_param(
            "ii",
            $liquidacionId,
            $empleadoId
        );

        if (!$stmtLimpiar->execute()) {
            $error = $stmtLimpiar->error;
            $stmtLimpiar->close();

            throw new Exception(
                "No se pudo limpiar la novedad de SAC: "
                . $error
            );
        }

        $stmtLimpiar->close();

        return true;
    }



    /*
    |--------------------------------------------------------------------------
    | DÍAS SAC YA PAGADOS EN EL MISMO SEMESTRE
    |--------------------------------------------------------------------------
    */

    private function obtenerDiasSacPagados(
        $empleadoId,
        $liquidacionIdExcluir,
        $semestre,
        $anio
    ) {
        $empleadoId = (int)$empleadoId;
        $liquidacionIdExcluir = (int)$liquidacionIdExcluir;
        $semestre = (int)$semestre;
        $anio = (int)$anio;

        if (
            $empleadoId <= 0
            ||
            $anio <= 0
            ||
            !in_array($semestre, [1, 2], true)
        ) {
            return 0;
        }

        if ($semestre === 1) {
            $periodoDesde = sprintf('%04d-01', $anio);
            $periodoHasta = sprintf('%04d-06', $anio);
        } else {
            $periodoDesde = sprintf('%04d-07', $anio);
            $periodoHasta = sprintf('%04d-12', $anio);
        }

        $sql = "
            SELECT
                COALESCE(
                    SUM(
                        CASE
                            WHEN ln.dias_sac IS NULL
                            THEN 180
                            ELSE ln.dias_sac
                        END
                    ),
                    0
                ) AS dias_pagados

            FROM liquidacion l

            INNER JOIN liquidacion_empleado le
                ON le.liquidacion_id = l.id
               AND le.empleado_id = ?

            LEFT JOIN liquidacion_novedad ln
                ON ln.liquidacion_id = l.id
               AND ln.empleado_id = le.empleado_id

            WHERE l.estado = 'CERRADA'
              AND l.tipo_liquidacion IN (
                    'AGUINALDO',
                    'COMPLEMENTARIA_SAC'
              )
              AND l.periodo >= ?
              AND l.periodo <= ?
              AND l.id <> ?
        ";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                "Error al preparar el control de SAC ya pagado: "
                . $this->conexion->error
            );
        }

        $stmt->bind_param(
            "issi",
            $empleadoId,
            $periodoDesde,
            $periodoHasta,
            $liquidacionIdExcluir
        );

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();

            throw new Exception(
                "Error al consultar los días de SAC ya pagados: "
                . $error
            );
        }

        $fila = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        $dias = (int)($fila['dias_pagados'] ?? 0);

        if ($dias < 0) {
            $dias = 0;
        }

        if ($dias > 180) {
            $dias = 180;
        }

        return $dias;
    }


    /*
    |--------------------------------------------------------------------------
    | DÍAS SAC DEVENGADOS
    |--------------------------------------------------------------------------
    |
    | Suma los días calendario inclusivos de todos los períodos laborales
    | que se superponen con el rango del SAC.
    |
    | Los intervalos se unifican para evitar doble conteo ante cualquier
    | solapamiento histórico accidental.
    |
    | El máximo por semestre continúa siendo 180 días.
    |
    */

    private function calcularDiasSacDevengados(
        $empleadoId,
        $desdeSemestre,
        $hastaDevengo
    ) {
        $empleadoId =
            (int)$empleadoId;


        $desdeSemestre =
            trim(
                (string)$desdeSemestre
            );


        $hastaDevengo =
            trim(
                (string)$hastaDevengo
            );


        if (
            $empleadoId <= 0
            ||
            !$this->fechaValida($desdeSemestre)
            ||
            !$this->fechaValida($hastaDevengo)
            ||
            $hastaDevengo < $desdeSemestre
        ) {

            return 0;
        }


        /*
        |--------------------------------------------------------------------------
        | PERÍODOS LABORALES QUE SE SUPERPONEN CON EL SAC
        |--------------------------------------------------------------------------
        |
        | Ejemplo:
        |
        | 01/01 -> 31/03
        | 01/09 -> NULL
        |
        | Para un SAC del primer semestre se toma únicamente el primer período.
        | Para el segundo semestre se toma el segundo período desde 01/09.
        |
        | Si existen varios períodos dentro del mismo semestre, se suman.
        |
        */

        $sql = "
            SELECT
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
                fecha_desde ASC,
                id ASC
        ";


        $stmt =
            $this->conexion->prepare(
                $sql
            );


        if (!$stmt) {

            throw new Exception(
                "Error al preparar los períodos laborales para calcular SAC: "
                . $this->conexion->error
            );
        }


        $stmt->bind_param(
            "iss",
            $empleadoId,
            $hastaDevengo,
            $desdeSemestre
        );


        if (!$stmt->execute()) {

            $error =
                $stmt->error;


            $stmt->close();


            throw new Exception(
                "Error al consultar los períodos laborales para calcular SAC: "
                . $error
            );
        }


        $resultado =
            $stmt->get_result();


        $intervalos =
            [];


        while (
            $periodo =
                $resultado->fetch_assoc()
        ) {

            $fechaDesdePeriodo =
                trim(
                    (string)(
                        $periodo['fecha_desde']
                        ?? ''
                    )
                );


            $fechaHastaPeriodo =
                trim(
                    (string)(
                        $periodo['fecha_hasta']
                        ?? ''
                    )
                );


            if (
                !$this->fechaValida(
                    $fechaDesdePeriodo
                )
            ) {

                continue;
            }


            /*
            | Período abierto:
            | se corta en hastaDevengo.
            */

            if (
                $fechaHastaPeriodo === ''
                ||
                !$this->fechaValida(
                    $fechaHastaPeriodo
                )
            ) {

                $fechaHastaPeriodo =
                    $hastaDevengo;
            }


            /*
            |--------------------------------------------------------------------------
            | RECORTAR AL RANGO DEL SAC
            |--------------------------------------------------------------------------
            */

            $inicio =
                $fechaDesdePeriodo > $desdeSemestre
                    ? $fechaDesdePeriodo
                    : $desdeSemestre;


            $fin =
                $fechaHastaPeriodo < $hastaDevengo
                    ? $fechaHastaPeriodo
                    : $hastaDevengo;


            if ($fin < $inicio) {

                continue;
            }


            $intervalos[] = [
                'desde' => $inicio,
                'hasta' => $fin
            ];
        }


        $stmt->close();


        if (empty($intervalos)) {

            return 0;
        }


        /*
        |--------------------------------------------------------------------------
        | UNIFICAR INTERVALOS
        |--------------------------------------------------------------------------
        |
        | Aunque la aplicación impide períodos solapados, se unifican igualmente
        | para que un dato histórico incorrecto nunca duplique días de SAC.
        |
        */

        $intervalosUnificados =
            [];


        foreach ($intervalos as $intervalo) {

            if (empty($intervalosUnificados)) {

                $intervalosUnificados[] =
                    $intervalo;

                continue;
            }


            $ultimoIndice =
                count(
                    $intervalosUnificados
                )
                -
                1;


            $ultimo =
                $intervalosUnificados[
                    $ultimoIndice
                ];


            try {

                $finUltimo =
                    new DateTime(
                        $ultimo['hasta']
                    );


                $diaSiguiente =
                    clone $finUltimo;


                $diaSiguiente->modify(
                    '+1 day'
                );


                $fechaDiaSiguiente =
                    $diaSiguiente->format(
                        'Y-m-d'
                    );

            } catch (Exception $e) {

                $fechaDiaSiguiente =
                    $ultimo['hasta'];
            }


            /*
            | Si el nuevo período comienza antes o justo al día siguiente,
            | se fusiona con el anterior.
            */

            if (
                $intervalo['desde']
                <=
                $fechaDiaSiguiente
            ) {

                if (
                    $intervalo['hasta']
                    >
                    $intervalosUnificados[
                        $ultimoIndice
                    ]['hasta']
                ) {

                    $intervalosUnificados[
                        $ultimoIndice
                    ]['hasta'] =
                        $intervalo['hasta'];
                }

            } else {

                $intervalosUnificados[] =
                    $intervalo;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | SUMAR DÍAS INCLUSIVOS
        |--------------------------------------------------------------------------
        */

        $dias =
            0;


        foreach (
            $intervalosUnificados
            as
            $intervalo
        ) {

            try {

                $inicio =
                    new DateTime(
                        $intervalo['desde']
                    );


                $fin =
                    new DateTime(
                        $intervalo['hasta']
                    );


                if ($fin < $inicio) {

                    continue;
                }


                $dias +=
                    (int)$inicio
                        ->diff($fin)
                        ->days
                    +
                    1;

            } catch (Exception $e) {

                continue;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | TOPE DEL SISTEMA
        |--------------------------------------------------------------------------
        |
        | SIGENMUNI trabaja con divisor fijo de 180 días para SAC.
        | Por lo tanto, el máximo devengable por semestre continúa siendo 180.
        |
        */

        if ($dias < 0) {

            $dias =
                0;
        }


        if ($dias > 180) {

            $dias =
                180;
        }


        return $dias;
    }


    /*
    |--------------------------------------------------------------------------
    | ANTIGÜEDAD EFECTIVA ACUMULADA
    |--------------------------------------------------------------------------
    |
    | Calcula los AÑOS COMPLETOS de servicio efectivo de un empleado hasta una
    | fecha de referencia.
    |
    | Fuente exclusiva:
    |
    | empleado_periodo_laboral
    |
    | Regla:
    |
    | - los períodos de inactividad no generan antigüedad;
    | - se acumula el tiempo realmente trabajado en todos los períodos;
    | - los intervalos superpuestos o contiguos se unifican defensivamente;
    | - para conservar la lógica histórica de "años completos", el umbral de
    |   cada año se calcula contra los aniversarios de la primera incorporación;
    | - un período cerrado utiliza fecha_hasta como último día trabajado;
    | - un período abierto se corta en la fecha de referencia.
    |
    | Ejemplo:
    |
    | 01/01/2020 -> 31/12/2022
    | 01/01/2025 -> fecha de referencia
    |
    | El tiempo 2023-2024 no se suma a la antigüedad.
    |
    */

    public function calcularAniosAntiguedadEfectiva(
        $empleadoId,
        $fechaReferencia
    ) {
        $empleadoId =
            (int)$empleadoId;

        $fechaReferencia =
            trim(
                (string)$fechaReferencia
            );


        if (
            $empleadoId <= 0
            ||
            !$this->fechaValida(
                $fechaReferencia
            )
        ) {

            return 0;
        }


        $sql = "
            SELECT
                fecha_desde,
                fecha_hasta

            FROM empleado_periodo_laboral

            WHERE empleado_id = ?
              AND fecha_desde <= ?

            ORDER BY
                fecha_desde ASC,
                id ASC
        ";


        $stmt =
            $this->conexion->prepare(
                $sql
            );


        if (!$stmt) {

            throw new Exception(
                "Error al preparar el historial laboral para calcular antigüedad: "
                . $this->conexion->error
            );
        }


        $stmt->bind_param(
            "is",
            $empleadoId,
            $fechaReferencia
        );


        if (!$stmt->execute()) {

            $error =
                $stmt->error;

            $stmt->close();

            throw new Exception(
                "Error al consultar el historial laboral para calcular antigüedad: "
                . $error
            );
        }


        $resultado =
            $stmt->get_result();


        $intervalos =
            [];


        while (
            $fila =
                $resultado->fetch_assoc()
        ) {

            $desde =
                trim(
                    (string)(
                        $fila['fecha_desde']
                        ?? ''
                    )
                );

            $hasta =
                trim(
                    (string)(
                        $fila['fecha_hasta']
                        ?? ''
                    )
                );


            if (
                !$this->fechaValida(
                    $desde
                )
            ) {

                continue;
            }


            /*
            | Período abierto o cierre posterior a la fecha consultada:
            | se corta exactamente en fechaReferencia.
            */

            if (
                $hasta === ''
                ||
                !$this->fechaValida(
                    $hasta
                )
                ||
                $hasta > $fechaReferencia
            ) {

                $hasta =
                    $fechaReferencia;
            }


            if ($hasta < $desde) {

                continue;
            }


            $intervalos[] = [
                'desde' => $desde,
                'hasta' => $hasta
            ];
        }


        $stmt->close();


        if (empty($intervalos)) {

            return 0;
        }


        /*
        |--------------------------------------------------------------------------
        | UNIFICAR INTERVALOS
        |--------------------------------------------------------------------------
        */

        $intervalosUnificados =
            [];


        foreach ($intervalos as $intervalo) {

            if (empty($intervalosUnificados)) {

                $intervalosUnificados[] =
                    $intervalo;

                continue;
            }


            $ultimoIndice =
                count(
                    $intervalosUnificados
                ) - 1;


            $ultimoHasta =
                $intervalosUnificados[
                    $ultimoIndice
                ]['hasta'];


            $objetoHasta =
                DateTime::createFromFormat(
                    'Y-m-d',
                    $ultimoHasta
                );


            if ($objetoHasta) {

                $diaSiguiente =
                    clone $objetoHasta;

                $diaSiguiente->modify(
                    '+1 day'
                );

                $limiteUnion =
                    $diaSiguiente->format(
                        'Y-m-d'
                    );

            } else {

                $limiteUnion =
                    $ultimoHasta;
            }


            if (
                $intervalo['desde']
                <=
                $limiteUnion
            ) {

                if (
                    $intervalo['hasta']
                    >
                    $intervalosUnificados[
                        $ultimoIndice
                    ]['hasta']
                ) {

                    $intervalosUnificados[
                        $ultimoIndice
                    ]['hasta'] =
                        $intervalo['hasta'];
                }

            } else {

                $intervalosUnificados[] =
                    $intervalo;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | SUMAR TIEMPO EFECTIVAMENTE TRABAJADO
        |--------------------------------------------------------------------------
        |
        | Para un período abierto que llega hasta la fecha de referencia se usa la
        | diferencia normal entre ambas fechas. Así, un empleado que ingresó
        | 01/01/2020 cumple su primer año el 01/01/2021, igual que en la lógica
        | histórica basada en DateTime::diff().
        |
        | Para un período cerrado antes de la fecha de referencia se incluye
        | completamente fecha_hasta, porque representa el último día trabajado.
        |
        */

        $diasServicio =
            0;


        foreach (
            $intervalosUnificados
            as
            $intervalo
        ) {

            $inicio =
                DateTime::createFromFormat(
                    'Y-m-d',
                    $intervalo['desde']
                );

            $fin =
                DateTime::createFromFormat(
                    'Y-m-d',
                    $intervalo['hasta']
                );


            if (
                !$inicio
                ||
                !$fin
                ||
                $fin < $inicio
            ) {

                continue;
            }


            $finExclusivo =
                clone $fin;


            if (
                $intervalo['hasta']
                <
                $fechaReferencia
            ) {

                /*
                | fecha_hasta es inclusiva:
                | si el período ya terminó antes de la fecha consultada,
                | se incorpora completo su último día trabajado.
                */

                $finExclusivo->modify(
                    '+1 day'
                );
            }


            $diasServicio +=
                (int)$inicio
                    ->diff(
                        $finExclusivo
                    )
                    ->days;
        }


        if ($diasServicio <= 0) {

            return 0;
        }


        /*
        |--------------------------------------------------------------------------
        | CONVERTIR SERVICIO ACUMULADO EN AÑOS COMPLETOS
        |--------------------------------------------------------------------------
        |
        | Los umbrales se construyen desde la primera incorporación para respetar
        | años bisiestos y mantener el mismo criterio de aniversario que tenía
        | el cálculo histórico cuando el vínculo era continuo.
        |
        */

        $fechaPrimeraAlta =
            $intervalosUnificados[0]['desde'];


        $inicioInicial =
            DateTime::createFromFormat(
                'Y-m-d',
                $fechaPrimeraAlta
            );

        $fechaCalculo =
            DateTime::createFromFormat(
                'Y-m-d',
                $fechaReferencia
            );


        if (
            !$inicioInicial
            ||
            !$fechaCalculo
            ||
            $fechaCalculo < $inicioInicial
        ) {

            return 0;
        }


        $maximoAniosCalendario =
            (int)$inicioInicial
                ->diff(
                    $fechaCalculo
                )
                ->y;


        $aniosCompletos =
            0;


        for (
            $anio = 1;
            $anio <= $maximoAniosCalendario;
            $anio++
        ) {

            $aniversario =
                clone $inicioInicial;


            $aniversario->modify(
                '+'
                . $anio
                . ' year'
            );


            $diasNecesarios =
                (int)$inicioInicial
                    ->diff(
                        $aniversario
                    )
                    ->days;


            if (
                $diasServicio
                >=
                $diasNecesarios
            ) {

                $aniosCompletos =
                    $anio;

            } else {

                break;
            }
        }


        return
            $aniosCompletos;
    }


    /*
    |--------------------------------------------------------------------------
    | DÍAS LABORALES HABILITADOS EN UN MES
    |--------------------------------------------------------------------------
    |
    | Calcula el máximo de días que un empleado puede liquidar dentro de un
    | período mensual tomando como fuente exclusiva empleado_periodo_laboral.
    |
    | Regla de SIGENMUNI:
    |
    | - divisor mensual fijo = 30 días;
    | - un mes completo siempre equivale a 30 días;
    | - el último día calendario del mes se considera día 30;
    | - el día 31 se normaliza como día 30;
    | - si hay varios períodos laborales dentro del mismo mes, se suman;
    | - nunca se devuelven más de 30 días;
    | - los períodos superpuestos o contiguos se unifican antes de contar para
    |   evitar duplicaciones históricas.
    |
    | Ejemplos:
    |
    | 01/02 -> 28/02 = 30 días
    | 01/07 -> 31/07 = 30 días
    | 01/07 -> 15/07 = 15 días
    | 10/07 -> 31/07 = 21 días
    |
    |--------------------------------------------------------------------------
    */

    public function calcularDiasLaboralesHabilitadosMes(
        $empleadoId,
        $periodo
    ) {
        $empleadoId =
            (int)$empleadoId;

        $periodo =
            trim(
                (string)$periodo
            );


        if ($empleadoId <= 0) {

            return 0;
        }


        $rangoMes =
            $this->obtenerRangoMes(
                $periodo
            );


        $desdeMes =
            $rangoMes['desde'];

        $hastaMes =
            $rangoMes['hasta'];


        $sql = "
            SELECT
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
                fecha_desde ASC,
                id ASC
        ";


        $stmt =
            $this->conexion->prepare(
                $sql
            );


        if (!$stmt) {

            throw new Exception(
                "Error al preparar los períodos laborales del empleado: "
                . $this->conexion->error
            );
        }


        $stmt->bind_param(
            "iss",
            $empleadoId,
            $hastaMes,
            $desdeMes
        );


        if (!$stmt->execute()) {

            $error =
                $stmt->error;

            $stmt->close();

            throw new Exception(
                "Error al consultar los períodos laborales del empleado: "
                . $error
            );
        }


        $resultado =
            $stmt->get_result();


        $intervalos =
            [];


        while (
            $fila =
                $resultado->fetch_assoc()
        ) {

            $fechaDesdePeriodo =
                trim(
                    (string)(
                        $fila['fecha_desde']
                        ?? ''
                    )
                );


            $fechaHastaPeriodo =
                trim(
                    (string)(
                        $fila['fecha_hasta']
                        ?? ''
                    )
                );


            if (
                !$this->fechaValida(
                    $fechaDesdePeriodo
                )
            ) {

                continue;
            }


            if (
                $fechaHastaPeriodo === ''
                ||
                !$this->fechaValida(
                    $fechaHastaPeriodo
                )
            ) {

                $fechaHastaPeriodo =
                    $hastaMes;
            }


            /*
            |------------------------------------------------------------------
            | RECORTAR AL MES SOLICITADO
            |------------------------------------------------------------------
            */

            $inicio =
                $fechaDesdePeriodo > $desdeMes
                    ? $fechaDesdePeriodo
                    : $desdeMes;


            $fin =
                $fechaHastaPeriodo < $hastaMes
                    ? $fechaHastaPeriodo
                    : $hastaMes;


            if ($fin < $inicio) {

                continue;
            }


            $intervalos[] = [
                'desde' => $inicio,
                'hasta' => $fin
            ];
        }


        $stmt->close();


        if (empty($intervalos)) {

            return 0;
        }


        /*
        |--------------------------------------------------------------------------
        | UNIFICAR INTERVALOS SUPERPUESTOS O CONTIGUOS
        |--------------------------------------------------------------------------
        */

        $intervalosUnificados =
            [];


        foreach ($intervalos as $intervalo) {

            if (empty($intervalosUnificados)) {

                $intervalosUnificados[] =
                    $intervalo;

                continue;
            }


            $ultimoIndice =
                count(
                    $intervalosUnificados
                ) - 1;


            $ultimoHasta =
                $intervalosUnificados[
                    $ultimoIndice
                ]['hasta'];


            $diaSiguiente =
                DateTime::createFromFormat(
                    'Y-m-d',
                    $ultimoHasta
                );


            if ($diaSiguiente) {

                $diaSiguiente =
                    clone $diaSiguiente;

                $diaSiguiente->modify(
                    '+1 day'
                );

                $limiteUnion =
                    $diaSiguiente->format(
                        'Y-m-d'
                    );

            } else {

                $limiteUnion =
                    $ultimoHasta;
            }


            if (
                $intervalo['desde']
                <=
                $limiteUnion
            ) {

                if (
                    $intervalo['hasta']
                    >
                    $intervalosUnificados[
                        $ultimoIndice
                    ]['hasta']
                ) {

                    $intervalosUnificados[
                        $ultimoIndice
                    ]['hasta'] =
                        $intervalo['hasta'];
                }

            } else {

                $intervalosUnificados[] =
                    $intervalo;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | CONTAR CON CONVENIO MENSUAL DE 30 DÍAS
        |--------------------------------------------------------------------------
        */

        $diasHabilitados =
            0;


        foreach (
            $intervalosUnificados
            as
            $intervalo
        ) {

            $diasHabilitados +=
                $this->calcularDiasConvenioMensual30(
                    $intervalo['desde'],
                    $intervalo['hasta'],
                    $hastaMes
                );
        }


        if ($diasHabilitados < 0) {

            $diasHabilitados =
                0;
        }


        if ($diasHabilitados > 30) {

            $diasHabilitados =
                30;
        }


        return $diasHabilitados;
    }


    /*
    |--------------------------------------------------------------------------
    | CONVENIO MENSUAL DE 30 DÍAS
    |--------------------------------------------------------------------------
    |
    | Convierte un intervalo ya recortado al mismo mes en días de liquidación.
    |
    | El último día calendario del mes se considera día 30. Esto permite que
    | febrero completo, por ejemplo, se liquide como 30 días.
    |
    |--------------------------------------------------------------------------
    */

    private function calcularDiasConvenioMensual30(
        $fechaDesde,
        $fechaHasta,
        $ultimoDiaMes
    ) {
        if (
            !$this->fechaValida($fechaDesde)
            ||
            !$this->fechaValida($fechaHasta)
            ||
            !$this->fechaValida($ultimoDiaMes)
            ||
            $fechaHasta < $fechaDesde
        ) {

            return 0;
        }


        $inicio =
            DateTime::createFromFormat(
                'Y-m-d',
                $fechaDesde
            );


        $fin =
            DateTime::createFromFormat(
                'Y-m-d',
                $fechaHasta
            );


        if (!$inicio || !$fin) {

            return 0;
        }


        $diaDesde =
            min(
                30,
                (int)$inicio->format('d')
            );


        if ($fechaHasta === $ultimoDiaMes) {

            $diaHasta =
                30;

        } else {

            $diaHasta =
                min(
                    30,
                    (int)$fin->format('d')
                );
        }


        $dias =
            $diaHasta
            -
            $diaDesde
            +
            1;


        return max(
            0,
            $dias
        );
    }


    private function obtenerRangoMes($periodo)
    {
        $periodo =
            trim(
                (string)$periodo
            );

        if (!$this->periodoValido($periodo)) {
            throw new Exception(
                "El período de la liquidación no es válido."
            );
        }

        $inicio =
            DateTime::createFromFormat(
                'Y-m-d',
                $periodo . '-01'
            );

        if (!$inicio) {
            throw new Exception(
                "No se pudo determinar el inicio del período."
            );
        }

        $fin = clone $inicio;

        $fin->modify(
            'last day of this month'
        );

        return [
            'desde' => $inicio->format('Y-m-d'),
            'hasta' => $fin->format('Y-m-d')
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | RANGO DEL SEMESTRE PARA SAC
    |--------------------------------------------------------------------------
    */

    private function obtenerRangoSemestreSac($periodo)
    {
        $periodo = trim((string)$periodo);

        if (!$this->periodoValido($periodo)) {
            throw new Exception(
                "El período de la liquidación de SAC no es válido."
            );
        }

        $partes = explode('-', $periodo);

        $anio = (int)($partes[0] ?? 0);
        $mes = (int)($partes[1] ?? 0);

        if (
            $anio <= 0
            ||
            $mes < 1
            ||
            $mes > 12
        ) {
            throw new Exception(
                "No se pudo determinar el semestre de la liquidación de SAC."
            );
        }

        if ($mes <= 6) {
            return [
                'desde' => sprintf('%04d-01-01', $anio),
                'hasta' => sprintf('%04d-06-30', $anio),
                'semestre' => 1
            ];
        }

        return [
            'desde' => sprintf('%04d-07-01', $anio),
            'hasta' => sprintf('%04d-12-31', $anio),
            'semestre' => 2
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | EMPLEADOS PARA CARGA DE GASTOS PROTOCOLARES
    |--------------------------------------------------------------------------
    |
    | Devuelve empleados con relación laboral dentro del período y, si ya
    | existe una carga para la liquidación indicada, incorpora importe y
    | condición previsional.
    |
    |--------------------------------------------------------------------------
    */

    public function obtenerEmpleadosParaProtocolar($liquidacionId)
    {
        $liquidacionId =
            (int)$liquidacionId;


        if ($liquidacionId <= 0) {

            throw new Exception(
                "ID de liquidación inválido para Gastos Protocolares."
            );
        }


        $liquidacion =
            $this->obtenerPorId(
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


        if ($tipoLiquidacion !== 'GASTOS_PROTOCOLARES') {

            throw new Exception(
                "La liquidación seleccionada no corresponde a Gastos Protocolares."
            );
        }


        $periodo =
            trim(
                (string)(
                    $liquidacion['periodo']
                    ?? ''
                )
            );


        $rangoMes =
            $this->obtenerRangoMes(
                $periodo
            );


        $desdeMes =
            $rangoMes['desde'];

        $hastaMes =
            $rangoMes['hasta'];


        /*
        |--------------------------------------------------------------------------
        | ELEGIBILIDAD POR HISTORIAL LABORAL
        |--------------------------------------------------------------------------
        |
        | Ya no se utiliza empleado.activo para decidir quién puede recibir
        | Gastos Protocolares.
        |
        | Se muestra a todo empleado que haya tenido al menos un período laboral
        | superpuesto con el mes de la liquidación.
        |
        | Esto permite:
        |
        | - incluir a una persona hoy inactiva si trabajó en ese período;
        | - excluir a una persona actualmente activa si todavía no tenía relación
        |   laboral durante el período consultado;
        | - excluir meses completos en los que no existió relación laboral.
        |
        */

        $sql = "
            SELECT
                e.id AS empleado_id,
                e.nro_legajo,
                e.apellido,
                e.nombre,

                c.codigo AS categoria_codigo,
                c.nombre AS categoria_nombre,

                lp.id AS protocolar_id,
                lp.importe,
                lp.aplica_prevision

            FROM empleado e

            LEFT JOIN categoria c
                ON e.categoria_id = c.id

            LEFT JOIN liquidacion_protocolar lp
                ON lp.empleado_id = e.id
               AND lp.liquidacion_id = ?

            WHERE EXISTS (
                SELECT 1

                FROM empleado_periodo_laboral epl

                WHERE epl.empleado_id = e.id

                  AND epl.fecha_desde <= ?

                  AND (
                        epl.fecha_hasta IS NULL
                        OR epl.fecha_hasta >= ?
                  )
            )

            ORDER BY
                e.apellido ASC,
                e.nombre ASC
        ";


        $stmt =
            $this->conexion->prepare(
                $sql
            );


        if (!$stmt) {

            throw new Exception(
                "Error al preparar los empleados para Gastos Protocolares: "
                . $this->conexion->error
            );
        }


        $stmt->bind_param(
            "iss",
            $liquidacionId,
            $hastaMes,
            $desdeMes
        );


        if (!$stmt->execute()) {

            $error =
                $stmt->error;

            $stmt->close();

            throw new Exception(
                "Error al consultar los empleados para Gastos Protocolares: "
                . $error
            );
        }


        $resultado =
            $stmt->get_result();


        $empleados =
            [];


        while (
            $fila =
                $resultado->fetch_assoc()
        ) {

            $empleadoId =
                (int)(
                    $fila['empleado_id']
                    ?? 0
                );


            $diasHabilitados =
                $this->calcularDiasLaboralesHabilitadosMes(
                    $empleadoId,
                    $periodo
                );


            /*
            | Defensa adicional:
            | si por cualquier inconsistencia histórica el EXISTS devolviera una
            | fila sin días habilitados, no se muestra al empleado.
            */

            if ($diasHabilitados <= 0) {

                continue;
            }


            $empleados[] = [

                'empleado_id' =>
                    $empleadoId,

                'nro_legajo' =>
                    $fila['nro_legajo'],

                'apellido' =>
                    $fila['apellido'],

                'nombre' =>
                    $fila['nombre'],

                'categoria_codigo' =>
                    $fila['categoria_codigo'],

                'categoria_nombre' =>
                    $fila['categoria_nombre'],

                'dias_laborales_habilitados' =>
                    $diasHabilitados,

                'incluido' =>
                    !empty(
                        $fila['protocolar_id']
                    )
                        ? 1
                        : 0,

                'protocolar_id' =>
                    !empty(
                        $fila['protocolar_id']
                    )
                        ? (int)$fila['protocolar_id']
                        : null,

                'importe' =>
                    $fila['importe'] !== null
                        ? (float)$fila['importe']
                        : 0,

                'aplica_prevision' =>
                    $fila['aplica_prevision'] !== null
                        ? (int)$fila['aplica_prevision']
                        : 1
            ];
        }


        $stmt->close();


        return $empleados;
    }


    /*
    |--------------------------------------------------------------------------
    | OBTENER CARGAS DE GASTOS PROTOCOLARES
    |--------------------------------------------------------------------------
    */

    public function obtenerCargasProtocolar($liquidacionId)
    {
        $liquidacionId =
            (int)$liquidacionId;


        if ($liquidacionId <= 0) {

            throw new Exception(
                "ID de liquidación inválido para consultar Gastos Protocolares."
            );
        }


        $liquidacion =
            $this->obtenerPorId(
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


        if ($tipoLiquidacion !== 'GASTOS_PROTOCOLARES') {

            throw new Exception(
                "La liquidación seleccionada no corresponde a Gastos Protocolares."
            );
        }


        $periodo =
            trim(
                (string)(
                    $liquidacion['periodo']
                    ?? ''
                )
            );


        $sql = "
            SELECT
                lp.id,
                lp.liquidacion_id,
                lp.empleado_id,
                lp.importe,
                lp.aplica_prevision,

                e.nro_legajo,
                e.apellido,
                e.nombre,

                c.codigo AS categoria_codigo,
                c.nombre AS categoria_nombre

            FROM liquidacion_protocolar lp

            INNER JOIN empleado e
                ON lp.empleado_id = e.id

            LEFT JOIN categoria c
                ON e.categoria_id = c.id

            WHERE lp.liquidacion_id = ?

            ORDER BY
                e.apellido ASC,
                e.nombre ASC
        ";


        $stmt =
            $this->conexion->prepare(
                $sql
            );


        if (!$stmt) {

            throw new Exception(
                "Error al preparar las cargas de Gastos Protocolares: "
                . $this->conexion->error
            );
        }


        $stmt->bind_param(
            "i",
            $liquidacionId
        );


        if (!$stmt->execute()) {

            $error =
                $stmt->error;

            $stmt->close();

            throw new Exception(
                "Error al consultar las cargas de Gastos Protocolares: "
                . $error
            );
        }


        $resultado =
            $stmt->get_result();


        $cargas =
            [];


        while (
            $fila =
                $resultado->fetch_assoc()
        ) {

            $empleadoId =
                (int)(
                    $fila['empleado_id']
                    ?? 0
                );


            $diasHabilitados =
                $this->calcularDiasLaboralesHabilitadosMes(
                    $empleadoId,
                    $periodo
                );


            /*
            |--------------------------------------------------------------------------
            | REVALIDACIÓN ANTES DE PROCESAR
            |--------------------------------------------------------------------------
            |
            | Una carga vieja o manipulada no debe procesarse si el empleado no
            | tenía relación laboral en el período de la liquidación.
            |
            */

            if ($diasHabilitados <= 0) {

                $stmt->close();

                throw new Exception(
                    "El empleado "
                    . trim(
                        (string)(
                            $fila['apellido']
                            ?? ''
                        )
                        . ", "
                        . (string)(
                            $fila['nombre']
                            ?? ''
                        )
                    )
                    . " no posee relación laboral habilitada para Gastos Protocolares en el período "
                    . $periodo
                    . "."
                );
            }


            $cargas[] = [

                'id' =>
                    (int)$fila['id'],

                'liquidacion_id' =>
                    (int)$fila['liquidacion_id'],

                'empleado_id' =>
                    $empleadoId,

                'importe' =>
                    (float)$fila['importe'],

                'aplica_prevision' =>
                    (int)$fila['aplica_prevision'],

                'nro_legajo' =>
                    $fila['nro_legajo'],

                'apellido' =>
                    $fila['apellido'],

                'nombre' =>
                    $fila['nombre'],

                'categoria_codigo' =>
                    $fila['categoria_codigo'],

                'categoria_nombre' =>
                    $fila['categoria_nombre'],

                'dias_laborales_habilitados' =>
                    $diasHabilitados
            ];
        }


        $stmt->close();


        return $cargas;
    }


    /*
    |--------------------------------------------------------------------------
    | GUARDAR / ACTUALIZAR GASTO PROTOCOLAR
    |--------------------------------------------------------------------------
    */

    public function guardarProtocolar(
        $liquidacionId,
        $empleadoId,
        $importe,
        $aplicaPrevision
    ) {
        $liquidacionId =
            (int)$liquidacionId;

        $empleadoId =
            (int)$empleadoId;

        $importe =
            round(
                (float)$importe,
                2
            );

        $aplicaPrevision =
            (int)$aplicaPrevision === 1
                ? 1
                : 0;


        if (
            $liquidacionId <= 0
            ||
            $empleadoId <= 0
        ) {

            throw new Exception(
                "Datos inválidos para guardar el Gasto Protocolar."
            );
        }


        if ($importe <= 0) {

            throw new Exception(
                "El importe del Gasto Protocolar debe ser mayor a cero."
            );
        }


        $liquidacion =
            $this->obtenerPorId(
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


        if ($tipoLiquidacion !== 'GASTOS_PROTOCOLARES') {

            throw new Exception(
                "La liquidación seleccionada no corresponde a Gastos Protocolares."
            );
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDAR RELACIÓN LABORAL EN EL PERÍODO
        |--------------------------------------------------------------------------
        |
        | No basta con que el empleado exista o esté activo actualmente.
        | Debe haber tenido relación laboral dentro del mes de la liquidación.
        |
        */

        $diasHabilitados =
            $this->calcularDiasLaboralesHabilitadosMes(
                $empleadoId,
                $liquidacion['periodo']
                ?? ''
            );


        if ($diasHabilitados <= 0) {

            throw new Exception(
                "El empleado seleccionado no posee relación laboral habilitada para Gastos Protocolares en el período "
                . ($liquidacion['periodo'] ?? '')
                . "."
            );
        }


        $sql = "
            INSERT INTO liquidacion_protocolar (
                liquidacion_id,
                empleado_id,
                importe,
                aplica_prevision
            )
            VALUES (?, ?, ?, ?)

            ON DUPLICATE KEY UPDATE
                importe = VALUES(importe),
                aplica_prevision = VALUES(aplica_prevision)
        ";


        $stmt =
            $this->conexion->prepare(
                $sql
            );


        if (!$stmt) {

            throw new Exception(
                "Error al preparar el Gasto Protocolar: "
                . $this->conexion->error
            );
        }


        $stmt->bind_param(
            "iidi",
            $liquidacionId,
            $empleadoId,
            $importe,
            $aplicaPrevision
        );


        if (!$stmt->execute()) {

            $error =
                $stmt->error;

            $stmt->close();

            throw new Exception(
                "No se pudo guardar el Gasto Protocolar: "
                . $error
            );
        }


        $stmt->close();


        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | ELIMINAR GASTO PROTOCOLAR DE UN EMPLEADO
    |--------------------------------------------------------------------------
    */

    public function eliminarProtocolar(
        $liquidacionId,
        $empleadoId
    ) {
        $liquidacionId = (int)$liquidacionId;
        $empleadoId = (int)$empleadoId;

        $sql = "
            DELETE FROM liquidacion_protocolar
            WHERE liquidacion_id = ?
              AND empleado_id = ?
        ";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                "Error al preparar la eliminación del Gasto Protocolar: "
                . $this->conexion->error
            );
        }

        $stmt->bind_param(
            "ii",
            $liquidacionId,
            $empleadoId
        );

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();

            throw new Exception(
                "No se pudo eliminar el Gasto Protocolar: "
                . $error
            );
        }

        $stmt->close();

        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | CONTAR CARGAS DE GASTOS PROTOCOLARES
    |--------------------------------------------------------------------------
    */

    public function contarCargasProtocolar($liquidacionId)
    {
        $liquidacionId = (int)$liquidacionId;

        $sql = "
            SELECT COUNT(*) AS total
            FROM liquidacion_protocolar
            WHERE liquidacion_id = ?
        ";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                "Error al preparar el conteo de Gastos Protocolares: "
                . $this->conexion->error
            );
        }

        $stmt->bind_param("i", $liquidacionId);

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();

            throw new Exception(
                "Error al contar los Gastos Protocolares: "
                . $error
            );
        }

        $fila = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return (int)($fila['total'] ?? 0);
    }


    /*
    |--------------------------------------------------------------------------
    | OBTENER VALOR VIGENTE DE UN CONCEPTO POR CATEGORÍA
    |--------------------------------------------------------------------------
    |
    | Fuente principal para conceptos TABLA_CATEGORIA.
    |
    | Busca el valor en concepto_valor teniendo en cuenta:
    |
    | - código del concepto;
    | - categoría del empleado;
    | - concepto activo;
    | - valor activo;
    | - fecha desde;
    | - fecha hasta.
    |
    | Si existen varios registros vigentes, se prioriza el de fecha_desde
    | más reciente y luego el ID más alto.
    |
    |--------------------------------------------------------------------------
    */

    public function obtenerValorConceptoVigente(
        $codigoConcepto,
        $categoriaId,
        $fechaLiquidacion
    ) {
        $codigoConcepto =
            trim(
                (string)$codigoConcepto
            );


        $categoriaId =
            (int)$categoriaId;


        $fechaLiquidacion =
            trim(
                (string)$fechaLiquidacion
            );


        if (
            $codigoConcepto === ''
            ||
            $categoriaId <= 0
            ||
            !$this->fechaValida(
                $fechaLiquidacion
            )
        ) {

            return null;
        }


        $sql = "
            SELECT
                cv.id,
                cv.concepto_id,
                cv.categoria_id,
                cv.escalafon_id,
                cv.monto,
                cv.porcentaje,
                cv.fecha_desde,
                cv.fecha_hasta,
                cv.activo,

                c.codigo,
                c.nombre,
                c.forma_calculo

            FROM concepto_valor cv

            INNER JOIN concepto c
                ON cv.concepto_id = c.id

            WHERE c.codigo = ?

              AND c.activo = 1

              AND cv.categoria_id = ?

              AND cv.activo = 1

              AND (
                    cv.fecha_desde IS NULL
                    OR cv.fecha_desde <= ?
              )

              AND (
                    cv.fecha_hasta IS NULL
                    OR cv.fecha_hasta >= ?
              )

            ORDER BY
                CASE
                    WHEN cv.fecha_desde IS NULL
                    THEN 1
                    ELSE 0
                END ASC,

                cv.fecha_desde DESC,

                cv.id DESC

            LIMIT 1
        ";


        $stmt =
            $this->conexion->prepare(
                $sql
            );


        if (!$stmt) {

            throw new Exception(
                "Error al preparar la consulta del valor vigente del concepto "
                . $codigoConcepto
                . ": "
                . $this->conexion->error
            );
        }


        $stmt->bind_param(
            "siss",
            $codigoConcepto,
            $categoriaId,
            $fechaLiquidacion,
            $fechaLiquidacion
        );


        if (!$stmt->execute()) {

            $error =
                $stmt->error;


            $stmt->close();


            throw new Exception(
                "Error al consultar el valor vigente del concepto "
                . $codigoConcepto
                . ": "
                . $error
            );
        }


        $resultado =
            $stmt->get_result();


        $fila =
            $resultado->fetch_assoc();


        $stmt->close();


        if (!$fila) {

            return null;
        }


        return [

            'id' =>
                (int)$fila['id'],

            'concepto_id' =>
                (int)$fila['concepto_id'],

            'categoria_id' =>
                (int)$fila['categoria_id'],

            'escalafon_id' =>
                !empty(
                    $fila['escalafon_id']
                )
                    ?
                    (int)$fila['escalafon_id']
                    :
                    null,

            'codigo' =>
                (string)$fila['codigo'],

            'nombre' =>
                $fila['nombre'],

            'forma_calculo' =>
                $fila['forma_calculo'],

            'monto' =>
                (float)$fila['monto'],

            'porcentaje' =>
                (float)$fila['porcentaje'],

            'fecha_desde' =>
                $fila['fecha_desde'],

            'fecha_hasta' =>
                $fila['fecha_hasta'],

            'activo' =>
                (int)$fila['activo']
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | CONCEPTOS VIGENTES DEL EMPLEADO
    |--------------------------------------------------------------------------
    */

    public function obtenerConceptosEmpleadoVigentes(
        $empleadoId,
        $fechaLiquidacion
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

                c.codigo,
                c.nombre AS concepto_nombre,
                c.categoria AS concepto_categoria,
                c.forma_calculo,
                c.porcentaje AS concepto_porcentaje,
                c.monto_fijo AS concepto_monto_fijo,
                c.base_calculo,
                c.aplica_sac,
                c.requiere_manual

            FROM empleado_concepto ec

            INNER JOIN concepto c
                ON ec.concepto_id = c.id

            WHERE ec.empleado_id = ?

              AND ec.activo = 1

              AND c.activo = 1

              AND (
                    ec.fecha_desde IS NULL
                    OR ec.fecha_desde <= ?
              )

              AND (
                    ec.fecha_hasta IS NULL
                    OR ec.fecha_hasta >= ?
              )

            ORDER BY
                c.codigo ASC,
                ec.id ASC
        ";


        $stmt =
            $this->conexion->prepare(
                $sql
            );


        if (!$stmt) {

            throw new Exception(
                "Error al preparar los conceptos del empleado: "
                . $this->conexion->error
            );
        }


        $stmt->bind_param(
            "iss",
            $empleadoId,
            $fechaLiquidacion,
            $fechaLiquidacion
        );


        if (!$stmt->execute()) {

            $error =
                $stmt->error;

            $stmt->close();

            throw new Exception(
                "Error al consultar los conceptos del empleado: "
                . $error
            );
        }


        $resultado =
            $stmt->get_result();


        $conceptosEmpleado = [];


        while (
            $fila =
                $resultado->fetch_assoc()
        ) {

            $conceptosEmpleado[] = [

                'id' =>
                    (int)$fila['id'],

                'empleado_id' =>
                    (int)$fila['empleado_id'],

                'concepto_id' =>
                    (int)$fila['concepto_id'],

                'codigo' =>
                    (string)$fila['codigo'],

                'concepto_nombre' =>
                    $fila['concepto_nombre'],

                'concepto_categoria' =>
                    $fila['concepto_categoria'],

                'forma_calculo' =>
                    $fila['forma_calculo'],

                'monto_manual' =>
                    (float)$fila['monto_manual'],

                'porcentaje_manual' =>
                    (float)$fila['porcentaje_manual'],

                'cantidad' =>
                    (float)$fila['cantidad'],

                'fecha_desde' =>
                    $fila['fecha_desde'],

                'fecha_hasta' =>
                    $fila['fecha_hasta'],

                'observacion' =>
                    $fila['observacion'],

                'concepto_porcentaje' =>
                    (float)$fila['concepto_porcentaje'],

                'concepto_monto_fijo' =>
                    (float)$fila['concepto_monto_fijo'],

                'base_calculo' =>
                    $fila['base_calculo'],

                'aplica_sac' =>
                    (int)$fila['aplica_sac'],

                'requiere_manual' =>
                    (int)$fila['requiere_manual']
            ];
        }


        $stmt->close();


        return $conceptosEmpleado;
    }


    /*
    |--------------------------------------------------------------------------
    | INICIAR TRANSACCIÓN
    |--------------------------------------------------------------------------
    */

    public function iniciarTransaccion()
    {
        if (
            !$this->conexion
                ->begin_transaction()
        ) {

            throw new Exception(
                "No se pudo iniciar la transacción de la liquidación."
            );
        }


        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | CONFIRMAR TRANSACCIÓN
    |--------------------------------------------------------------------------
    */

    public function confirmarTransaccion()
    {
        if (
            !$this->conexion
                ->commit()
        ) {

            throw new Exception(
                "No se pudo confirmar la liquidación."
            );
        }


        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | REVERTIR TRANSACCIÓN
    |--------------------------------------------------------------------------
    */

    public function revertirTransaccion()
    {
        $this->conexion
            ->rollback();


        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | LIMPIAR PROCESAMIENTO
    |--------------------------------------------------------------------------
    */

    public function limpiarProcesamiento(
        $liquidacionId
    ) {
        /*
        |--------------------------------------------------------------------------
        | DETALLE
        |--------------------------------------------------------------------------
        */

        $stmtDetalle =
            $this->conexion->prepare(
                "
                DELETE FROM liquidacion_detalle
                WHERE liquidacion_id = ?
                "
            );


        if (!$stmtDetalle) {

            throw new Exception(
                "No se pudo preparar la limpieza del detalle: "
                . $this->conexion->error
            );
        }


        $stmtDetalle->bind_param(
            "i",
            $liquidacionId
        );


        if (!$stmtDetalle->execute()) {

            $error =
                $stmtDetalle->error;

            $stmtDetalle->close();

            throw new Exception(
                "No se pudo limpiar el detalle anterior: "
                . $error
            );
        }


        $stmtDetalle->close();


        /*
        |--------------------------------------------------------------------------
        | RESUMEN
        |--------------------------------------------------------------------------
        */

        $stmtResumen =
            $this->conexion->prepare(
                "
                DELETE FROM liquidacion_empleado
                WHERE liquidacion_id = ?
                "
            );


        if (!$stmtResumen) {

            throw new Exception(
                "No se pudo preparar la limpieza del resumen: "
                . $this->conexion->error
            );
        }


        $stmtResumen->bind_param(
            "i",
            $liquidacionId
        );


        if (!$stmtResumen->execute()) {

            $error =
                $stmtResumen->error;

            $stmtResumen->close();

            throw new Exception(
                "No se pudo limpiar el resumen anterior: "
                . $error
            );
        }


        $stmtResumen->close();


        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | GUARDAR DETALLE
    |--------------------------------------------------------------------------
    */

    public function guardarDetalle(
        $liquidacionId,
        $empleadoId,
        $conceptoId,
        $cantidad,
        $porcentaje,
        $monto,
        $esManual,
        $observacion
    ) {
        $sql = "
            INSERT INTO liquidacion_detalle (
                liquidacion_id,
                empleado_id,
                concepto_id,
                cantidad,
                porcentaje_aplicado,
                monto,
                es_manual,
                observacion
            )
            VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?
            )
        ";


        $stmt =
            $this->conexion->prepare(
                $sql
            );


        if (!$stmt) {

            throw new Exception(
                "No se pudo preparar el detalle de liquidación: "
                . $this->conexion->error
            );
        }


        $stmt->bind_param(
            "iiidddis",
            $liquidacionId,
            $empleadoId,
            $conceptoId,
            $cantidad,
            $porcentaje,
            $monto,
            $esManual,
            $observacion
        );


        if (!$stmt->execute()) {

            $error =
                $stmt->error;

            $stmt->close();

            throw new Exception(
                "No se pudo guardar un detalle de liquidación: "
                . $error
            );
        }


        $stmt->close();


        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | GUARDAR RESUMEN DEL EMPLEADO
    |--------------------------------------------------------------------------
    */

    public function guardarResumenEmpleado(
        $liquidacionId,
        $empleadoId,
        $totalRemunerativo,
        $totalDescuentos,
        $totalNoRemunerativo,
        $totalAsignaciones,
        $neto,
        $aplicaPrevision = 1
    ) {
        $sql = "
            INSERT INTO liquidacion_empleado (
                liquidacion_id,
                empleado_id,
                aplica_prevision,
                total_remunerativo,
                total_descuentos,
                total_no_remunerativo,
                total_asignaciones,
                neto
            )
            VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?
            )
        ";


        $stmt =
            $this->conexion->prepare(
                $sql
            );


        if (!$stmt) {

            throw new Exception(
                "No se pudo preparar el resumen del empleado: "
                . $this->conexion->error
            );
        }


        $aplicaPrevision =
            (int)$aplicaPrevision === 1 ? 1 : 0;

        $stmt->bind_param(
            "iiiddddd",
            $liquidacionId,
            $empleadoId,
            $aplicaPrevision,
            $totalRemunerativo,
            $totalDescuentos,
            $totalNoRemunerativo,
            $totalAsignaciones,
            $neto
        );


        if (!$stmt->execute()) {

            $error =
                $stmt->error;

            $stmt->close();

            throw new Exception(
                "No se pudo guardar el resumen del empleado ID "
                . $empleadoId
                . ": "
                . $error
            );
        }


        $stmt->close();


        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | CAMBIAR ESTADO
    |--------------------------------------------------------------------------
    */

    public function cambiarEstado(
        $liquidacionId,
        $estado
    ) {
        if (
            !$this->estadoValido(
                $estado
            )
        ) {

            throw new Exception(
                "Estado de liquidación no válido."
            );
        }


        $sql = "
            UPDATE liquidacion
            SET estado = ?
            WHERE id = ?
        ";


        $stmt =
            $this->conexion->prepare(
                $sql
            );


        if (!$stmt) {

            throw new Exception(
                "No se pudo preparar el cambio de estado: "
                . $this->conexion->error
            );
        }


        $stmt->bind_param(
            "si",
            $estado,
            $liquidacionId
        );


        if (!$stmt->execute()) {

            $error =
                $stmt->error;

            $stmt->close();

            throw new Exception(
                "No se pudo cambiar el estado de la liquidación: "
                . $error
            );
        }


        $stmt->close();


        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | CERRAR LIQUIDACIÓN
    |--------------------------------------------------------------------------
    */

    public function cerrar(
        $liquidacionId
    ) {
        return $this->cambiarEstado(
            $liquidacionId,
            'CERRADA'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ANULAR LIQUIDACIÓN
    |--------------------------------------------------------------------------
    */

    public function anular(
        $liquidacionId
    ) {
        return $this->cambiarEstado(
            $liquidacionId,
            'ANULADA'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | OBTENER CONCEPTO ACTIVO POR CÓDIGO
    |--------------------------------------------------------------------------
    */

    public function obtenerConceptoActivoPorCodigo(
        $codigo
    ) {
        $sql = "
            SELECT
                id,
                codigo,
                nombre,
                categoria,
                forma_calculo,
                porcentaje,
                monto_fijo,
                requiere_manual,
                base_calculo,
                orden_calculo,
                aplica_sac,
                visible_recibo

            FROM concepto

            WHERE codigo = ?
              AND activo = 1

            LIMIT 1
        ";


        $stmt =
            $this->conexion->prepare(
                $sql
            );


        if (!$stmt) {

            throw new Exception(
                "Error al preparar la consulta del concepto: "
                . $this->conexion->error
            );
        }


        $codigoTexto =
            (string)$codigo;


        $stmt->bind_param(
            "s",
            $codigoTexto
        );


        if (!$stmt->execute()) {

            $error =
                $stmt->error;

            $stmt->close();

            throw new Exception(
                "Error al consultar el concepto "
                . $codigoTexto
                . ": "
                . $error
            );
        }


        $resultado =
            $stmt->get_result();


        $concepto =
            $resultado->fetch_assoc();


        $stmt->close();


        return $concepto;
    }


    /*
    |--------------------------------------------------------------------------
    | RESUMEN DE EMPLEADOS
    |--------------------------------------------------------------------------
    */

    public function obtenerResumenEmpleados(
        $liquidacionId
    ) {
        $sql = "
            SELECT
                le.id,
                le.empleado_id,
                le.aplica_prevision,

                e.nro_legajo,
                e.apellido,
                e.nombre,

                le.total_remunerativo,
                le.total_descuentos,
                le.total_no_remunerativo,
                le.total_asignaciones,
                le.neto

            FROM liquidacion_empleado le

            INNER JOIN empleado e
                ON le.empleado_id = e.id

            WHERE le.liquidacion_id = ?

            ORDER BY
                e.apellido ASC,
                e.nombre ASC
        ";


        $stmt =
            $this->conexion->prepare(
                $sql
            );


        if (!$stmt) {

            throw new Exception(
                "Error al preparar el resumen de empleados: "
                . $this->conexion->error
            );
        }


        $stmt->bind_param(
            "i",
            $liquidacionId
        );


        if (!$stmt->execute()) {

            $error =
                $stmt->error;

            $stmt->close();

            throw new Exception(
                "Error al consultar el resumen de empleados: "
                . $error
            );
        }


        $resultado =
            $stmt->get_result();


        $empleados = [];


        while (
            $fila =
                $resultado->fetch_assoc()
        ) {

            $empleados[] = [

                'id' =>
                    (int)$fila['id'],

                'empleado_id' =>
                    (int)$fila['empleado_id'],

                'aplica_prevision' =>
                    (int)$fila['aplica_prevision'],

                'nro_legajo' =>
                    $fila['nro_legajo'],

                'apellido' =>
                    $fila['apellido'],

                'nombre' =>
                    $fila['nombre'],

                'total_remunerativo' =>
                    (float)$fila['total_remunerativo'],

                'total_descuentos' =>
                    (float)$fila['total_descuentos'],

                'total_no_remunerativo' =>
                    (float)$fila['total_no_remunerativo'],

                'total_asignaciones' =>
                    (float)$fila['total_asignaciones'],

                'neto' =>
                    (float)$fila['neto']
            ];
        }


        $stmt->close();


        return $empleados;
    }


    /*
    |--------------------------------------------------------------------------
    | TOTALES GENERALES
    |--------------------------------------------------------------------------
    */

    public function obtenerTotales(
        $liquidacionId
    ) {
        $sql = "
            SELECT

                COUNT(*) AS cantidad_empleados,

                COALESCE(
                    SUM(total_remunerativo),
                    0
                ) AS total_remunerativo,

                COALESCE(
                    SUM(total_descuentos),
                    0
                ) AS total_descuentos,

                COALESCE(
                    SUM(total_no_remunerativo),
                    0
                ) AS total_no_remunerativo,

                COALESCE(
                    SUM(total_asignaciones),
                    0
                ) AS total_asignaciones,

                COALESCE(
                    SUM(neto),
                    0
                ) AS total_neto

            FROM liquidacion_empleado

            WHERE liquidacion_id = ?
        ";


        $stmt =
            $this->conexion->prepare(
                $sql
            );


        if (!$stmt) {

            throw new Exception(
                "Error al preparar los totales de la liquidación: "
                . $this->conexion->error
            );
        }


        $stmt->bind_param(
            "i",
            $liquidacionId
        );


        if (!$stmt->execute()) {

            $error =
                $stmt->error;

            $stmt->close();

            throw new Exception(
                "Error al consultar los totales de la liquidación: "
                . $error
            );
        }


        $resultado =
            $stmt->get_result();


        $fila =
            $resultado->fetch_assoc();


        $stmt->close();


        return [

            'cantidad_empleados' =>
                (int)(
                    $fila['cantidad_empleados']
                    ?? 0
                ),

            'total_remunerativo' =>
                (float)(
                    $fila['total_remunerativo']
                    ?? 0
                ),

            'total_descuentos' =>
                (float)(
                    $fila['total_descuentos']
                    ?? 0
                ),

            'total_no_remunerativo' =>
                (float)(
                    $fila['total_no_remunerativo']
                    ?? 0
                ),

            'total_asignaciones' =>
                (float)(
                    $fila['total_asignaciones']
                    ?? 0
                ),

            'total_neto' =>
                (float)(
                    $fila['total_neto']
                    ?? 0
                )
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | OBTENER EMPLEADO DE UNA LIQUIDACIÓN
    |--------------------------------------------------------------------------
    */

    public function obtenerEmpleadoLiquidacion(
        $liquidacionId,
        $empleadoId
    ) {
        $sql = "
            SELECT
                e.id,
                e.nro_legajo,
                e.apellido,
                e.nombre,

                le.aplica_prevision,
                le.total_remunerativo,
                le.total_descuentos,
                le.total_no_remunerativo,
                le.total_asignaciones,
                le.neto

            FROM liquidacion_empleado le

            INNER JOIN empleado e
                ON le.empleado_id = e.id

            WHERE le.liquidacion_id = ?
              AND le.empleado_id = ?

            LIMIT 1
        ";


        $stmt =
            $this->conexion->prepare(
                $sql
            );


        if (!$stmt) {

            throw new Exception(
                "Error al preparar la consulta del empleado de la liquidación: "
                . $this->conexion->error
            );
        }


        $stmt->bind_param(
            "ii",
            $liquidacionId,
            $empleadoId
        );


        if (!$stmt->execute()) {

            $error =
                $stmt->error;

            $stmt->close();

            throw new Exception(
                "Error al consultar el empleado de la liquidación: "
                . $error
            );
        }


        $resultado =
            $stmt->get_result();


        $empleado =
            $resultado->fetch_assoc();


        $stmt->close();


        return $empleado;
    }


    /*
    |--------------------------------------------------------------------------
    | DETALLE DE CONCEPTOS DE UN EMPLEADO
    |--------------------------------------------------------------------------
    */

    public function obtenerDetalleEmpleado(
        $liquidacionId,
        $empleadoId
    ) {
        $sql = "
            SELECT
                ld.id,
                ld.cantidad,
                ld.porcentaje_aplicado,
                ld.monto,
                ld.es_manual,
                ld.observacion,

                c.id AS concepto_id,
                c.codigo,
                c.nombre AS concepto_nombre,
                c.categoria AS concepto_categoria,
                c.orden_calculo,
                c.visible_recibo

            FROM liquidacion_detalle ld

            INNER JOIN concepto c
                ON ld.concepto_id = c.id

            WHERE ld.liquidacion_id = ?
              AND ld.empleado_id = ?

            ORDER BY
                c.orden_calculo ASC,
                CAST(c.codigo AS UNSIGNED) ASC,
                c.codigo ASC
        ";


        $stmt =
            $this->conexion->prepare(
                $sql
            );


        if (!$stmt) {

            throw new Exception(
                "Error al preparar el detalle del empleado: "
                . $this->conexion->error
            );
        }


        $stmt->bind_param(
            "ii",
            $liquidacionId,
            $empleadoId
        );


        if (!$stmt->execute()) {

            $error =
                $stmt->error;

            $stmt->close();

            throw new Exception(
                "Error al consultar el detalle del empleado: "
                . $error
            );
        }


        $resultado =
            $stmt->get_result();


        $detalle = [];


        while (
            $fila =
                $resultado->fetch_assoc()
        ) {

            $detalle[] = [

                'id' =>
                    (int)$fila['id'],

                'concepto_id' =>
                    (int)$fila['concepto_id'],

                'codigo' =>
                    (string)$fila['codigo'],

                'concepto_nombre' =>
                    $fila['concepto_nombre'],

                'concepto_categoria' =>
                    $fila['concepto_categoria'],

                'cantidad' =>
                    (float)$fila['cantidad'],

                'porcentaje_aplicado' =>
                    (float)$fila['porcentaje_aplicado'],

                'monto' =>
                    (float)$fila['monto'],

                'es_manual' =>
                    (int)$fila['es_manual'],

                'observacion' =>
                    $fila['observacion'],

                'orden_calculo' =>
                    (int)$fila['orden_calculo'],

                'visible_recibo' =>
                    (int)$fila['visible_recibo']
            ];
        }


        $stmt->close();


        return $detalle;
    }


    /*
    |--------------------------------------------------------------------------
    | DATOS GENERALES DEL RECIBO
    |--------------------------------------------------------------------------
    |
    | Comprueba que el empleado realmente pertenezca a la liquidación.
    |
    |--------------------------------------------------------------------------
    */

    public function obtenerDatosRecibo(
        $liquidacionId,
        $empleadoId
    ) {
        $sql = "
            SELECT
                l.id AS liquidacion_id,
                l.tipo_liquidacion,
                l.periodo,
                l.fecha_liquidacion,
                l.descripcion,
                l.estado,
                l.created_at,

                e.id AS empleado_id,
                e.nro_legajo,
                e.apellido,
                e.nombre,
                e.dni,
                e.fecha_alta,
                e.fecha_baja,
                e.fecha_inactivo,
                e.email,

                o.nombre AS oficina,
                o.cuit AS oficina_cuit,

                c.id AS categoria_id,
                c.codigo AS categoria_codigo,
                c.nombre AS categoria_nombre,

                le.aplica_prevision,
                le.total_remunerativo,
                le.total_descuentos,
                le.total_no_remunerativo,
                le.total_asignaciones,
                le.neto,

                COALESCE(
                    ln.dias_liquidados,
                    30
                ) AS dias_liquidados,

                COALESCE(
                    ln.aplica_presentismo,
                    1
                ) AS aplica_presentismo,

                COALESCE(
                    ln.dias_sac,
                    180
                ) AS dias_sac,

                COALESCE(
                    ln.observacion,
                    ''
                ) AS observacion_novedad

            FROM liquidacion l

            INNER JOIN liquidacion_empleado le
                ON le.liquidacion_id = l.id

            INNER JOIN empleado e
                ON le.empleado_id = e.id

            LEFT JOIN oficina o
                ON e.oficina_id = o.id

            LEFT JOIN categoria c
                ON e.categoria_id = c.id

            LEFT JOIN liquidacion_novedad ln
                ON ln.liquidacion_id = l.id
               AND ln.empleado_id = e.id

            WHERE l.id = ?
              AND e.id = ?

            LIMIT 1
        ";


        $stmt =
            $this->conexion->prepare(
                $sql
            );


        if (!$stmt) {

            throw new Exception(
                "Error al preparar los datos del recibo: "
                . $this->conexion->error
            );
        }


        $stmt->bind_param(
            "ii",
            $liquidacionId,
            $empleadoId
        );


        if (!$stmt->execute()) {

            $error =
                $stmt->error;

            $stmt->close();

            throw new Exception(
                "Error al consultar los datos del recibo: "
                . $error
            );
        }


        $resultado =
            $stmt->get_result();


        $datos =
            $resultado->fetch_assoc();


        $stmt->close();


        return $datos;
    }


    /*
    |--------------------------------------------------------------------------
    | DETALLE DEL RECIBO
    |--------------------------------------------------------------------------
    |
    | La categoría del concepto se devuelve explícitamente.
    |
    | Esto nos permite dejar de clasificar únicamente por rangos:
    |
    | 101-199
    | 201-299
    | 301-399
    | etc.
    |
    |--------------------------------------------------------------------------
    */

    public function obtenerDetalleRecibo(
        $liquidacionId,
        $empleadoId
    ) {
        $sql = "
            SELECT
                ld.id,
                ld.cantidad,
                ld.porcentaje_aplicado,
                ld.monto,
                ld.es_manual,
                ld.observacion,

                c.id AS concepto_id,
                c.codigo,
                c.nombre AS concepto_nombre,
                c.categoria AS concepto_categoria,
                c.orden_calculo,
                c.visible_recibo

            FROM liquidacion_detalle ld

            INNER JOIN concepto c
                ON ld.concepto_id = c.id

            WHERE ld.liquidacion_id = ?
              AND ld.empleado_id = ?

            ORDER BY
                c.orden_calculo ASC,
                CAST(c.codigo AS UNSIGNED) ASC,
                c.codigo ASC
        ";


        $stmt =
            $this->conexion->prepare(
                $sql
            );


        if (!$stmt) {

            throw new Exception(
                "Error al preparar el detalle del recibo: "
                . $this->conexion->error
            );
        }


        $stmt->bind_param(
            "ii",
            $liquidacionId,
            $empleadoId
        );


        if (!$stmt->execute()) {

            $error =
                $stmt->error;

            $stmt->close();

            throw new Exception(
                "Error al consultar el detalle del recibo: "
                . $error
            );
        }


        $resultado =
            $stmt->get_result();


        $detalle = [];


        while (
            $fila =
                $resultado->fetch_assoc()
        ) {

            $detalle[] = [

                'id' =>
                    (int)$fila['id'],

                'concepto_id' =>
                    (int)$fila['concepto_id'],

                'codigo' =>
                    (string)$fila['codigo'],

                'nombre' =>
                    $fila['concepto_nombre'],

                'categoria' =>
                    strtoupper(
                        trim(
                            (string)$fila['concepto_categoria']
                        )
                    ),

                'cantidad' =>
                    (float)$fila['cantidad'],

                'porcentaje_aplicado' =>
                    (float)$fila['porcentaje_aplicado'],

                'monto' =>
                    (float)$fila['monto'],

                'es_manual' =>
                    (int)$fila['es_manual'],

                'observacion' =>
                    $fila['observacion'],

                'orden_calculo' =>
                    (int)$fila['orden_calculo'],

                'visible_recibo' =>
                    (int)$fila['visible_recibo']
            ];
        }


        $stmt->close();


        return $detalle;
    }
}