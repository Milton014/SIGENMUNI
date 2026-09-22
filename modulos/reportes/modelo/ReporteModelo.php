<?php

class ReporteModelo
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
    | EMPLEADOS ACTIVOS
    |--------------------------------------------------------------------------
    */

    public function obtenerEmpleadosActivos()
    {
        $sql = "
            SELECT
                id,
                nro_legajo,
                apellido,
                nombre,
                dni,
                cuil,
                telefono,
                email,
                activo
            FROM empleado
            WHERE activo = 1
            ORDER BY apellido ASC, nombre ASC
        ";

        return $this->ejecutarConsultaSimple($sql);
    }


    /*
    |--------------------------------------------------------------------------
    | OBTENER EMPLEADO POR ID
    |--------------------------------------------------------------------------
    */

    public function obtenerEmpleadoPorId($empleadoId)
    {
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

                i.nombre AS institucion,
                o.nombre AS oficina,
                o.cuit AS oficina_cuit,
                s.nombre AS situacion,
                es.nombre AS escalafon,
                c.nombre AS categoria

            FROM empleado e

            LEFT JOIN institucion i
                ON e.institucion_id = i.id

            LEFT JOIN oficina o
                ON e.oficina_id = o.id

            LEFT JOIN situacion s
                ON e.situacion_id = s.id

            LEFT JOIN escalafon es
                ON e.escalafon_id = es.id

            LEFT JOIN categoria c
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

        $stmt->bind_param("i", $empleadoId);

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
    | REPORTE GENERAL DE EMPLEADOS
    |--------------------------------------------------------------------------
    */

    public function listarEmpleados(
        $legajo = '',
        $busqueda = '',
        $estado = ''
    ) {
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

        $tipos = "";
        $parametros = [];


        if ($legajo !== '') {

            $sql .= "
                AND CAST(e.nro_legajo AS CHAR) = ?
            ";

            $tipos .= "s";
            $parametros[] = $legajo;
        }


        if ($busqueda !== '') {

            $sql .= "
                AND (
                    e.apellido LIKE ?
                    OR e.nombre LIKE ?
                    OR e.dni LIKE ?
                    OR e.cuil LIKE ?
                    OR e.email LIKE ?
                )
            ";

            $like = "%" . $busqueda . "%";

            $tipos .= "sssss";

            $parametros[] = $like;
            $parametros[] = $like;
            $parametros[] = $like;
            $parametros[] = $like;
            $parametros[] = $like;
        }


        if (
            $estado === "1"
            ||
            $estado === "0"
        ) {

            $sql .= "
                AND e.activo = ?
            ";

            $tipos .= "i";
            $parametros[] = (int)$estado;
        }


        $sql .= "
            ORDER BY
                e.apellido ASC,
                e.nombre ASC
        ";

        return $this->ejecutarConsultaPreparada(
            $sql,
            $tipos,
            $parametros
        );
    }


    /*
    |--------------------------------------------------------------------------
    | BUSCAR EMPLEADOS PARA HISTORIAL
    |--------------------------------------------------------------------------
    */

    public function buscarEmpleadosHistorial(
        $legajo = '',
        $busqueda = '',
        $estado = ''
    ) {
        $sql = "
            SELECT
                e.id,
                e.nro_legajo,
                e.apellido,
                e.nombre,
                e.dni,
                e.cuil,
                e.fecha_inactivo,
                e.activo,
                c.nombre AS categoria

            FROM empleado e

            LEFT JOIN categoria c
                ON e.categoria_id = c.id

            WHERE 1 = 1
        ";

        $tipos = "";
        $parametros = [];


        if ($legajo !== '') {

            $sql .= "
                AND CAST(e.nro_legajo AS CHAR) = ?
            ";

            $tipos .= "s";
            $parametros[] = $legajo;
        }


        if ($busqueda !== '') {

            $sql .= "
                AND (
                    e.apellido LIKE ?
                    OR e.nombre LIKE ?
                    OR e.dni LIKE ?
                    OR e.cuil LIKE ?
                )
            ";

            $like = "%" . $busqueda . "%";

            $tipos .= "ssss";

            $parametros[] = $like;
            $parametros[] = $like;
            $parametros[] = $like;
            $parametros[] = $like;
        }


        if (
            $estado === "1"
            ||
            $estado === "0"
        ) {

            $sql .= "
                AND e.activo = ?
            ";

            $tipos .= "i";
            $parametros[] = (int)$estado;
        }


        $sql .= "
            ORDER BY
                e.apellido ASC,
                e.nombre ASC
            LIMIT 50
        ";

        return $this->ejecutarConsultaPreparada(
            $sql,
            $tipos,
            $parametros
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CATEGORÍAS - LISTADO GENERAL
    |--------------------------------------------------------------------------
    |
    | Los importes económicos se obtienen desde concepto_valor:
    | 101 = Sueldo Básico
    | 102 = Dedicación Funcional
    | 104 = Suplemento Especial
    |
    | Si no existe valor activo y vigente para una categoría, se devuelve NULL.
    |
    |--------------------------------------------------------------------------
    */

    public function obtenerCategorias()
    {
        $sql = "
            SELECT
                cat.id,
                cat.codigo,
                cat.nombre,
                cat.activo,

                (
                    SELECT cv.monto
                    FROM concepto_valor cv
                    INNER JOIN concepto con
                        ON con.id = cv.concepto_id
                    WHERE cv.categoria_id = cat.id
                      AND con.codigo = 101
                      AND con.activo = 1
                      AND cv.activo = 1
                      AND (cv.fecha_desde IS NULL OR cv.fecha_desde <= CURDATE())
                      AND (cv.fecha_hasta IS NULL OR cv.fecha_hasta >= CURDATE())
                    ORDER BY
                        cv.fecha_desde DESC,
                        cv.id DESC
                    LIMIT 1
                ) AS sueldo_basico,

                (
                    SELECT cv.monto
                    FROM concepto_valor cv
                    INNER JOIN concepto con
                        ON con.id = cv.concepto_id
                    WHERE cv.categoria_id = cat.id
                      AND con.codigo = 102
                      AND con.activo = 1
                      AND cv.activo = 1
                      AND (cv.fecha_desde IS NULL OR cv.fecha_desde <= CURDATE())
                      AND (cv.fecha_hasta IS NULL OR cv.fecha_hasta >= CURDATE())
                    ORDER BY
                        cv.fecha_desde DESC,
                        cv.id DESC
                    LIMIT 1
                ) AS dedicacion_funcional,

                (
                    SELECT cv.monto
                    FROM concepto_valor cv
                    INNER JOIN concepto con
                        ON con.id = cv.concepto_id
                    WHERE cv.categoria_id = cat.id
                      AND con.codigo = 104
                      AND con.activo = 1
                      AND cv.activo = 1
                      AND (cv.fecha_desde IS NULL OR cv.fecha_desde <= CURDATE())
                      AND (cv.fecha_hasta IS NULL OR cv.fecha_hasta >= CURDATE())
                    ORDER BY
                        cv.fecha_desde DESC,
                        cv.id DESC
                    LIMIT 1
                ) AS suplemento_especial

            FROM categoria cat

            ORDER BY
                CASE
                    WHEN CAST(cat.codigo AS UNSIGNED) >= 1000 THEN 1
                    ELSE 2
                END,
                CAST(cat.codigo AS UNSIGNED) ASC,
                cat.nombre ASC
        ";

        $categorias =
            $this->ejecutarConsultaSimple(
                $sql
            );

        return
            $this->prepararEstadoConfiguracionCategorias(
                $categorias
            );
    }


    /*
    |--------------------------------------------------------------------------
    | REPORTE DE CATEGORÍAS
    |--------------------------------------------------------------------------
    |
    | Fuente exclusiva para importes: concepto_valor.
    |
    | La categoría siempre aparece aunque no tenga valores cargados.
    | Un valor NULL significa "Sin valor vigente".
    |
    |--------------------------------------------------------------------------
    */

    public function listarCategorias(
        $buscar = '',
        $activo = ''
    ) {
        $sql = "
            SELECT
                cat.id,
                cat.codigo,
                cat.nombre,
                cat.activo,

                (
                    SELECT cv.monto
                    FROM concepto_valor cv
                    INNER JOIN concepto con
                        ON con.id = cv.concepto_id
                    WHERE cv.categoria_id = cat.id
                      AND con.codigo = 101
                      AND con.activo = 1
                      AND cv.activo = 1
                      AND (cv.fecha_desde IS NULL OR cv.fecha_desde <= CURDATE())
                      AND (cv.fecha_hasta IS NULL OR cv.fecha_hasta >= CURDATE())
                    ORDER BY
                        cv.fecha_desde DESC,
                        cv.id DESC
                    LIMIT 1
                ) AS sueldo_basico,

                (
                    SELECT cv.monto
                    FROM concepto_valor cv
                    INNER JOIN concepto con
                        ON con.id = cv.concepto_id
                    WHERE cv.categoria_id = cat.id
                      AND con.codigo = 102
                      AND con.activo = 1
                      AND cv.activo = 1
                      AND (cv.fecha_desde IS NULL OR cv.fecha_desde <= CURDATE())
                      AND (cv.fecha_hasta IS NULL OR cv.fecha_hasta >= CURDATE())
                    ORDER BY
                        cv.fecha_desde DESC,
                        cv.id DESC
                    LIMIT 1
                ) AS dedicacion_funcional,

                (
                    SELECT cv.monto
                    FROM concepto_valor cv
                    INNER JOIN concepto con
                        ON con.id = cv.concepto_id
                    WHERE cv.categoria_id = cat.id
                      AND con.codigo = 104
                      AND con.activo = 1
                      AND cv.activo = 1
                      AND (cv.fecha_desde IS NULL OR cv.fecha_desde <= CURDATE())
                      AND (cv.fecha_hasta IS NULL OR cv.fecha_hasta >= CURDATE())
                    ORDER BY
                        cv.fecha_desde DESC,
                        cv.id DESC
                    LIMIT 1
                ) AS suplemento_especial

            FROM categoria cat

            WHERE 1 = 1
        ";

        $tipos = "";
        $parametros = [];


        if ($buscar !== '') {

            if (ctype_digit($buscar)) {

                $sql .= "
                    AND CAST(cat.codigo AS CHAR) = ?
                ";

                $tipos .= "s";
                $parametros[] = $buscar;

            } else {

                $sql .= "
                    AND cat.nombre LIKE ?
                ";

                $like = "%" . $buscar . "%";

                $tipos .= "s";
                $parametros[] = $like;
            }
        }


        if (
            $activo === "1"
            ||
            $activo === "0"
        ) {

            $sql .= "
                AND cat.activo = ?
            ";

            $tipos .= "i";
            $parametros[] = (int)$activo;
        }


        $sql .= "
            ORDER BY
                CASE
                    WHEN CAST(cat.codigo AS UNSIGNED) >= 1000 THEN 1
                    ELSE 2
                END,
                CAST(cat.codigo AS UNSIGNED) ASC,
                cat.nombre ASC
        ";


        $categorias =
            $this->ejecutarConsultaPreparada(
                $sql,
                $tipos,
                $parametros
            );

        return
            $this->prepararEstadoConfiguracionCategorias(
                $categorias
            );
    }


    /*
    |--------------------------------------------------------------------------
    | ESTADO DE CONFIGURACIÓN DE CATEGORÍAS
    |--------------------------------------------------------------------------
    |
    | NULL no se transforma en 0:
    | - NULL  = no existe valor vigente.
    | - 0.00  = existe un valor configurado realmente en cero.
    |
    |--------------------------------------------------------------------------
    */

    private function prepararEstadoConfiguracionCategorias(
        array $categorias
    ) {
        foreach ($categorias as &$fila) {

            $completa =
                array_key_exists('sueldo_basico', $fila)
                &&
                $fila['sueldo_basico'] !== null
                &&
                array_key_exists('dedicacion_funcional', $fila)
                &&
                $fila['dedicacion_funcional'] !== null
                &&
                array_key_exists('suplemento_especial', $fila)
                &&
                $fila['suplemento_especial'] !== null;


            $fila['configuracion_completa'] =
                $completa
                    ? 1
                    : 0;


            $fila['estado_configuracion'] =
                $completa
                    ? 'COMPLETA'
                    : 'INCOMPLETA';
        }

        unset($fila);

        return $categorias;
    }


    /*
    |--------------------------------------------------------------------------
    | INSTITUCIONES
    |--------------------------------------------------------------------------
    */

    public function obtenerInstituciones()
    {
        $sql = "
            SELECT
                id,
                nombre,
                activo
            FROM institucion
            ORDER BY nombre ASC
        ";

        return $this->ejecutarConsultaSimple($sql);
    }


    /*
    |--------------------------------------------------------------------------
    | REPORTE DE CONCEPTOS
    |--------------------------------------------------------------------------
    */

    public function listarConceptos(
        $buscar = '',
        $categoria = '',
        $activo = ''
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
                activo,
                fecha_desde,
                fecha_hasta,
                orden_calculo,
                aplica_sac,
                visible_recibo
            FROM concepto
            WHERE 1 = 1
        ";

        $tipos = "";
        $parametros = [];


        if ($buscar !== '') {

            $sql .= "
                AND (
                    CAST(codigo AS CHAR) LIKE ?
                    OR nombre LIKE ?
                )
            ";

            $like = "%" . $buscar . "%";

            $tipos .= "ss";

            $parametros[] = $like;
            $parametros[] = $like;
        }


        if ($categoria !== '') {

            $sql .= "
                AND categoria = ?
            ";

            $tipos .= "s";
            $parametros[] = $categoria;
        }


        if (
            $activo === "1"
            ||
            $activo === "0"
        ) {

            $sql .= "
                AND activo = ?
            ";

            $tipos .= "i";
            $parametros[] = (int)$activo;
        }


        $sql .= "
            ORDER BY
                codigo ASC,
                nombre ASC
        ";


        return $this->ejecutarConsultaPreparada(
            $sql,
            $tipos,
            $parametros
        );
    }


    /*
    |--------------------------------------------------------------------------
    | TIPOS DE LIQUIDACIÓN
    |--------------------------------------------------------------------------
    */

    public function obtenerTiposLiquidacion()
    {
        $sql = "
            SELECT DISTINCT
                tipo_liquidacion
            FROM liquidacion
            WHERE tipo_liquidacion IS NOT NULL
              AND TRIM(tipo_liquidacion) <> ''
            ORDER BY tipo_liquidacion ASC
        ";

        $resultado =
            $this->ejecutarConsultaSimple(
                $sql
            );

        $tipos = [];

        foreach ($resultado as $fila) {

            if (!empty($fila['tipo_liquidacion'])) {

                $tipos[] =
                    $fila['tipo_liquidacion'];
            }
        }

        return $tipos;
    }


    /*
    |--------------------------------------------------------------------------
    | REPORTE DE LIQUIDACIONES
    |--------------------------------------------------------------------------
    */

    public function listarLiquidaciones(
        $periodo = '',
        $tipo = '',
        $estado = ''
    ) {
        $sql = "
            SELECT
                l.id,
                l.tipo_liquidacion,
                l.periodo,
                l.fecha_liquidacion,
                l.descripcion,
                l.estado,
                l.created_at,

                COUNT(le.id) AS cantidad_empleados,

                COALESCE(
                    SUM(le.total_remunerativo),
                    0
                ) AS total_remunerativo,

                COALESCE(
                    SUM(le.total_descuentos),
                    0
                ) AS total_descuentos,

                COALESCE(
                    SUM(le.total_no_remunerativo),
                    0
                ) AS total_no_remunerativo,

                COALESCE(
                    SUM(le.total_asignaciones),
                    0
                ) AS total_asignaciones,

                COALESCE(
                    SUM(le.neto),
                    0
                ) AS total_neto

            FROM liquidacion l

            LEFT JOIN liquidacion_empleado le
                ON le.liquidacion_id = l.id

            WHERE 1 = 1
        ";

        $tipos = "";
        $parametros = [];


        if ($periodo !== '') {

            $sql .= "
                AND l.periodo = ?
            ";

            $tipos .= "s";
            $parametros[] = $periodo;
        }


        if ($tipo !== '') {

            $sql .= "
                AND l.tipo_liquidacion = ?
            ";

            $tipos .= "s";
            $parametros[] = $tipo;
        }


        if ($estado !== '') {

            $sql .= "
                AND l.estado = ?
            ";

            $tipos .= "s";
            $parametros[] = $estado;
        }


        $sql .= "
            GROUP BY
                l.id,
                l.tipo_liquidacion,
                l.periodo,
                l.fecha_liquidacion,
                l.descripcion,
                l.estado,
                l.created_at

            ORDER BY
                l.fecha_liquidacion DESC,
                l.id DESC
        ";


        return $this->ejecutarConsultaPreparada(
            $sql,
            $tipos,
            $parametros
        );
    }


    /*
    |--------------------------------------------------------------------------
    | EMPLEADOS DE UNA LIQUIDACIÓN
    |--------------------------------------------------------------------------
    */

    public function obtenerEmpleadosLiquidacion($liquidacionId)
    {
        $sql = "
            SELECT
                le.id,
                le.liquidacion_id,
                le.empleado_id,
                le.total_remunerativo,
                le.total_descuentos,
                le.total_no_remunerativo,
                le.total_asignaciones,
                le.neto,

                e.nro_legajo,
                e.apellido,
                e.nombre,
                e.dni,
                e.cuil

            FROM liquidacion_empleado le

            INNER JOIN empleado e
                ON le.empleado_id = e.id

            WHERE le.liquidacion_id = ?

            ORDER BY
                e.apellido ASC,
                e.nombre ASC
        ";

        return $this->ejecutarConsultaPreparada(
            $sql,
            "i",
            [
                $liquidacionId
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | HISTORIAL DE LIQUIDACIONES POR EMPLEADO
    |--------------------------------------------------------------------------
    */

    public function obtenerHistorialEmpleado($empleadoId)
    {
        $sql = "
            SELECT
                l.id AS liquidacion_id,
                l.tipo_liquidacion,
                l.periodo,
                l.fecha_liquidacion,
                l.descripcion,
                l.estado,

                le.total_remunerativo,
                le.total_descuentos,
                le.total_no_remunerativo,
                le.total_asignaciones,
                le.neto

            FROM liquidacion_empleado le

            INNER JOIN liquidacion l
                ON le.liquidacion_id = l.id

            WHERE le.empleado_id = ?

            ORDER BY
                l.fecha_liquidacion DESC,
                l.id DESC
        ";

        return $this->ejecutarConsultaPreparada(
            $sql,
            "i",
            [
                $empleadoId
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | HISTORIAL LABORAL POR EMPLEADO
    |--------------------------------------------------------------------------
    |
    | Devuelve todos los períodos registrados en empleado_periodo_laboral.
    |
    | - fecha_hasta NULL = período actualmente abierto.
    | - dias_periodo se calcula de forma inclusiva.
    | - Se incluyen los datos del usuario que abrió/cerró el período cuando
    |   estén disponibles.
    |
    |--------------------------------------------------------------------------
    */

    public function obtenerHistorialLaboralEmpleado(
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

                GREATEST(
                    0,
                    DATEDIFF(
                        COALESCE(
                            pl.fecha_hasta,
                            CURDATE()
                        ),
                        pl.fecha_desde
                    ) + 1
                ) AS dias_periodo,

                ua.nombre_usuario AS usuario_alta_login,
                ua.nombre AS usuario_alta_nombre,
                ua.apellido AS usuario_alta_apellido,

                uc.nombre_usuario AS usuario_cierre_login,
                uc.nombre AS usuario_cierre_nombre,
                uc.apellido AS usuario_cierre_apellido

            FROM empleado_periodo_laboral pl

            LEFT JOIN usuario ua
                ON ua.id = pl.usuario_alta_id

            LEFT JOIN usuario uc
                ON uc.id = pl.usuario_cierre_id

            WHERE pl.empleado_id = ?

            ORDER BY
                pl.fecha_desde ASC,
                pl.id ASC
        ";


        $datos =
            $this->ejecutarConsultaPreparada(
                $sql,
                "i",
                [
                    $empleadoId
                ]
            );


        foreach ($datos as &$fila) {

            $fila['id'] =
                (int)(
                    $fila['id']
                    ?? 0
                );


            $fila['empleado_id'] =
                (int)(
                    $fila['empleado_id']
                    ?? 0
                );


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


            $fila['estado_periodo'] =
                strtoupper(
                    trim(
                        (string)(
                            $fila['estado_periodo']
                            ?? ''
                        )
                    )
                );


            $fila['usuario_alta'] =
                trim(
                    (string)(
                        ($fila['usuario_alta_apellido'] ?? '')
                        . (
                            !empty($fila['usuario_alta_apellido'])
                            &&
                            !empty($fila['usuario_alta_nombre'])
                                ? ', '
                                : ''
                        )
                        . ($fila['usuario_alta_nombre'] ?? '')
                    )
                );


            if ($fila['usuario_alta'] === '') {

                $fila['usuario_alta'] =
                    !empty($fila['usuario_alta_login'])
                        ? $fila['usuario_alta_login']
                        : null;
            }


            $fila['usuario_cierre'] =
                trim(
                    (string)(
                        ($fila['usuario_cierre_apellido'] ?? '')
                        . (
                            !empty($fila['usuario_cierre_apellido'])
                            &&
                            !empty($fila['usuario_cierre_nombre'])
                                ? ', '
                                : ''
                        )
                        . ($fila['usuario_cierre_nombre'] ?? '')
                    )
                );


            if ($fila['usuario_cierre'] === '') {

                $fila['usuario_cierre'] =
                    !empty($fila['usuario_cierre_login'])
                        ? $fila['usuario_cierre_login']
                        : null;
            }
        }


        unset($fila);


        return $datos;
    }


    /*
    |--------------------------------------------------------------------------
    | RESUMEN DE HISTORIAL LABORAL
    |--------------------------------------------------------------------------
    |
    | Devuelve:
    |
    | - cantidad total de períodos;
    | - cantidad de períodos finalizados;
    | - si existe un período abierto;
    | - fecha de la última incorporación;
    | - fecha de la última inactivación;
    | - total de días registrados.
    |
    |--------------------------------------------------------------------------
    */

    public function obtenerResumenHistorialLaboralEmpleado(
        $empleadoId
    ) {
        $empleadoId =
            (int)$empleadoId;


        if ($empleadoId <= 0) {

            return [
                'cantidad_periodos' => 0,
                'periodos_finalizados' => 0,
                'tiene_periodo_abierto' => 0,
                'ultima_incorporacion' => null,
                'ultima_inactivacion' => null,
                'total_dias_registrados' => 0
            ];
        }


        $sql = "
            SELECT
                COUNT(*) AS cantidad_periodos,

                SUM(
                    CASE
                        WHEN fecha_hasta IS NOT NULL
                        THEN 1
                        ELSE 0
                    END
                ) AS periodos_finalizados,

                MAX(
                    CASE
                        WHEN fecha_hasta IS NULL
                        THEN 1
                        ELSE 0
                    END
                ) AS tiene_periodo_abierto,

                MAX(fecha_desde) AS ultima_incorporacion,

                MAX(fecha_hasta) AS ultima_inactivacion,

                COALESCE(
                    SUM(
                        GREATEST(
                            0,
                            DATEDIFF(
                                COALESCE(
                                    fecha_hasta,
                                    CURDATE()
                                ),
                                fecha_desde
                            ) + 1
                        )
                    ),
                    0
                ) AS total_dias_registrados

            FROM empleado_periodo_laboral

            WHERE empleado_id = ?
        ";


        $datos =
            $this->ejecutarConsultaPreparada(
                $sql,
                "i",
                [
                    $empleadoId
                ]
            );


        if (empty($datos)) {

            return [
                'cantidad_periodos' => 0,
                'periodos_finalizados' => 0,
                'tiene_periodo_abierto' => 0,
                'ultima_incorporacion' => null,
                'ultima_inactivacion' => null,
                'total_dias_registrados' => 0
            ];
        }


        $fila =
            $datos[0];


        return [
            'cantidad_periodos' =>
                (int)(
                    $fila['cantidad_periodos']
                    ?? 0
                ),

            'periodos_finalizados' =>
                (int)(
                    $fila['periodos_finalizados']
                    ?? 0
                ),

            'tiene_periodo_abierto' =>
                (int)(
                    $fila['tiene_periodo_abierto']
                    ?? 0
                ),

            'ultima_incorporacion' =>
                $fila['ultima_incorporacion']
                ?? null,

            'ultima_inactivacion' =>
                $fila['ultima_inactivacion']
                ?? null,

            'total_dias_registrados' =>
                (int)(
                    $fila['total_dias_registrados']
                    ?? 0
                )
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | DETALLE DE LIQUIDACIÓN POR EMPLEADO
    |--------------------------------------------------------------------------
    */

    public function obtenerDetalleLiquidacionEmpleado(
        $liquidacionId,
        $empleadoId
    ) {
        $sql = "
            SELECT
                ld.id,
                ld.liquidacion_id,
                ld.empleado_id,
                ld.concepto_id,
                ld.cantidad,
                ld.porcentaje_aplicado,
                ld.monto,
                ld.es_manual,
                ld.observacion,

                c.codigo,
                c.nombre,
                c.categoria

            FROM liquidacion_detalle ld

            INNER JOIN concepto c
                ON ld.concepto_id = c.id

            WHERE ld.liquidacion_id = ?
              AND ld.empleado_id = ?

            ORDER BY c.codigo ASC
        ";

        return $this->ejecutarConsultaPreparada(
            $sql,
            "ii",
            [
                $liquidacionId,
                $empleadoId
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ESTADÍSTICAS - RESUMEN GENERAL
    |--------------------------------------------------------------------------
    |
    | Todas las métricas económicas se calculan únicamente con liquidaciones
    | CERRADAS.
    |
    | Filtros económicos disponibles:
    | - período desde;
    | - período hasta;
    | - tipo de liquidación.
    |
    | IMPORTANTE:
    | empleados_activos representa el padrón ACTUAL y no depende del período
    | ni del tipo de liquidación seleccionado.
    |
    |--------------------------------------------------------------------------
    */

    public function obtenerResumenEstadisticas(
        $periodoDesde = '',
        $periodoHasta = '',
        $tipoLiquidacion = ''
    ) {
        $periodoDesde =
            trim(
                (string)$periodoDesde
            );

        $periodoHasta =
            trim(
                (string)$periodoHasta
            );

        $tipoLiquidacion =
            strtoupper(
                trim(
                    (string)$tipoLiquidacion
                )
            );


        $filtroLiquidacion = "
            WHERE l.estado = 'CERRADA'
        ";

        $tipos = "";
        $parametros = [];


        if ($periodoDesde !== '') {

            $filtroLiquidacion .= "
                AND l.periodo >= ?
            ";

            $tipos .= "s";
            $parametros[] = $periodoDesde;
        }


        if ($periodoHasta !== '') {

            $filtroLiquidacion .= "
                AND l.periodo <= ?
            ";

            $tipos .= "s";
            $parametros[] = $periodoHasta;
        }


        if ($tipoLiquidacion !== '') {

            $filtroLiquidacion .= "
                AND l.tipo_liquidacion = ?
            ";

            $tipos .= "s";
            $parametros[] = $tipoLiquidacion;
        }


        $sql = "
            SELECT

                (
                    SELECT COUNT(*)
                    FROM empleado
                    WHERE activo = 1
                ) AS empleados_activos,

                COUNT(DISTINCT l.id) AS liquidaciones_cerradas,

                COALESCE(
                    SUM(le.total_remunerativo),
                    0
                ) AS total_remunerativo,

                COALESCE(
                    SUM(le.total_no_remunerativo),
                    0
                ) AS total_no_remunerativo,

                COALESCE(
                    SUM(le.total_asignaciones),
                    0
                ) AS total_asignaciones,

                COALESCE(
                    SUM(le.total_descuentos),
                    0
                ) AS total_descuentos,

                COALESCE(
                    SUM(le.neto),
                    0
                ) AS total_neto,

                COALESCE(
                    AVG(le.neto),
                    0
                ) AS neto_promedio

            FROM liquidacion l

            LEFT JOIN liquidacion_empleado le
                ON le.liquidacion_id = l.id

            $filtroLiquidacion
        ";


        $datos =
            $this->ejecutarConsultaPreparada(
                $sql,
                $tipos,
                $parametros
            );


        if (empty($datos)) {

            return [
                'empleados_activos' => 0,
                'liquidaciones_cerradas' => 0,
                'total_remunerativo' => 0,
                'total_no_remunerativo' => 0,
                'total_asignaciones' => 0,
                'total_descuentos' => 0,
                'total_neto' => 0,
                'neto_promedio' => 0
            ];
        }


        return $datos[0];
    }


    /*
    |--------------------------------------------------------------------------
    | ESTADÍSTICAS - EMPLEADOS POR CATEGORÍA
    |--------------------------------------------------------------------------
    |
    | Este gráfico representa el padrón ACTUAL de empleados activos.
    |
    | No se filtra por período ni por tipo de liquidación porque no existe un
    | historial de cambios de categoría que permita reconstruir con precisión
    | la categoría que tenía cada empleado en un período histórico.
    |
    |--------------------------------------------------------------------------
    */

    public function contarEmpleadosPorCategoria()
    {
        $sql = "
            SELECT
                c.codigo AS codigo,

                COALESCE(
                    c.nombre,
                    'Sin categoría'
                ) AS categoria,

                COUNT(e.id) AS total

            FROM empleado e

            LEFT JOIN categoria c
                ON e.categoria_id = c.id

            WHERE e.activo = 1

            GROUP BY
                c.id,
                c.codigo,
                c.nombre

            ORDER BY
                CASE
                    WHEN CAST(c.codigo AS UNSIGNED) >= 1000 THEN 1
                    ELSE 2
                END,
                CAST(c.codigo AS UNSIGNED) ASC,
                c.nombre ASC
        ";

        return $this->ejecutarConsultaSimple($sql);
    }


    /*
    |--------------------------------------------------------------------------
    | ESTADÍSTICAS - EMPLEADOS POR ESTADO
    |--------------------------------------------------------------------------
    */

    public function contarEmpleadosPorEstado()
    {
        $sql = "
            SELECT
                CASE
                    WHEN activo = 1 THEN 'ACTIVOS'
                    ELSE 'INACTIVOS'
                END AS estado,

                COUNT(*) AS total

            FROM empleado

            GROUP BY activo

            ORDER BY activo DESC
        ";

        return $this->ejecutarConsultaSimple($sql);
    }


    /*
    |--------------------------------------------------------------------------
    | ESTADÍSTICAS - NETO POR PERÍODO
    |--------------------------------------------------------------------------
    |
    | Suma el neto de todas las liquidaciones CERRADAS del período.
    |
    | Cuando se informa tipo de liquidación, solamente se acumulan registros
    | de ese tipo. Esto permite analizar, por ejemplo, únicamente MENSUAL sin
    | que AGUINALDO o COMPLEMENTARIA modifiquen la tendencia.
    |
    |--------------------------------------------------------------------------
    */

    public function obtenerNetoPorPeriodo(
        $periodoDesde = '',
        $periodoHasta = '',
        $tipoLiquidacion = ''
    ) {
        $periodoDesde =
            trim(
                (string)$periodoDesde
            );

        $periodoHasta =
            trim(
                (string)$periodoHasta
            );

        $tipoLiquidacion =
            strtoupper(
                trim(
                    (string)$tipoLiquidacion
                )
            );


        $sql = "
            SELECT
                l.periodo,

                COALESCE(
                    SUM(le.neto),
                    0
                ) AS total_neto

            FROM liquidacion l

            INNER JOIN liquidacion_empleado le
                ON le.liquidacion_id = l.id

            WHERE l.estado = 'CERRADA'
        ";

        $tipos = "";
        $parametros = [];


        if ($periodoDesde !== '') {

            $sql .= "
                AND l.periodo >= ?
            ";

            $tipos .= "s";
            $parametros[] = $periodoDesde;
        }


        if ($periodoHasta !== '') {

            $sql .= "
                AND l.periodo <= ?
            ";

            $tipos .= "s";
            $parametros[] = $periodoHasta;
        }


        if ($tipoLiquidacion !== '') {

            $sql .= "
                AND l.tipo_liquidacion = ?
            ";

            $tipos .= "s";
            $parametros[] = $tipoLiquidacion;
        }


        $sql .= "
            GROUP BY l.periodo
            ORDER BY l.periodo ASC
        ";


        return $this->ejecutarConsultaPreparada(
            $sql,
            $tipos,
            $parametros
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ESTADÍSTICAS - COMPOSICIÓN POR TIPO DE CONCEPTO
    |--------------------------------------------------------------------------
    |
    | Agrupa los importes reales guardados en liquidacion_detalle según la
    | categoría actual del concepto:
    |
    | - REMUNERATIVO
    | - NO_REMUNERATIVO
    | - ASIGNACION / ASIGNACION_FAMILIAR, según exista en la base
    | - DESCUENTO
    | - APORTE_PATRONAL
    |
    | Se conservan los valores reales de concepto.categoria para no asumir
    | nombres distintos a los existentes en la base.
    |
    | IMPORTANTE:
    | Esta consulta muestra composición de importes. Su suma general no debe
    | interpretarse como neto porque puede incluir descuentos y patronales.
    |
    |--------------------------------------------------------------------------
    */

    public function obtenerConceptosPorCategoria(
        $periodoDesde = '',
        $periodoHasta = '',
        $tipoLiquidacion = ''
    ) {
        $periodoDesde =
            trim(
                (string)$periodoDesde
            );

        $periodoHasta =
            trim(
                (string)$periodoHasta
            );

        $tipoLiquidacion =
            strtoupper(
                trim(
                    (string)$tipoLiquidacion
                )
            );


        $sql = "
            SELECT
                c.categoria AS tipo,

                COALESCE(
                    SUM(ld.monto),
                    0
                ) AS total

            FROM liquidacion_detalle ld

            INNER JOIN liquidacion l
                ON l.id = ld.liquidacion_id

            INNER JOIN concepto c
                ON c.id = ld.concepto_id

            WHERE l.estado = 'CERRADA'
        ";

        $tipos = "";
        $parametros = [];


        if ($periodoDesde !== '') {

            $sql .= "
                AND l.periodo >= ?
            ";

            $tipos .= "s";
            $parametros[] = $periodoDesde;
        }


        if ($periodoHasta !== '') {

            $sql .= "
                AND l.periodo <= ?
            ";

            $tipos .= "s";
            $parametros[] = $periodoHasta;
        }


        if ($tipoLiquidacion !== '') {

            $sql .= "
                AND l.tipo_liquidacion = ?
            ";

            $tipos .= "s";
            $parametros[] = $tipoLiquidacion;
        }


        $sql .= "
            GROUP BY c.categoria
            ORDER BY c.categoria ASC
        ";


        return $this->ejecutarConsultaPreparada(
            $sql,
            $tipos,
            $parametros
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ESTADÍSTICAS - PRINCIPALES DESCUENTOS
    |--------------------------------------------------------------------------
    |
    | Devuelve los descuentos de mayor importe acumulado dentro de
    | liquidaciones CERRADAS.
    |
    | El parámetro $tipoLiquidacion se agrega AL FINAL para mantener
    | compatibilidad con llamadas anteriores del tipo:
    |
    | obtenerPrincipalesDescuentos($desde, $hasta, 10)
    |
    |--------------------------------------------------------------------------
    */

    public function obtenerPrincipalesDescuentos(
        $periodoDesde = '',
        $periodoHasta = '',
        $limite = 10,
        $tipoLiquidacion = ''
    ) {
        $periodoDesde =
            trim(
                (string)$periodoDesde
            );

        $periodoHasta =
            trim(
                (string)$periodoHasta
            );

        $tipoLiquidacion =
            strtoupper(
                trim(
                    (string)$tipoLiquidacion
                )
            );


        $limite = (int)$limite;

        if ($limite <= 0) {
            $limite = 10;
        }

        if ($limite > 50) {
            $limite = 50;
        }


        $sql = "
            SELECT
                c.nombre AS concepto,

                COALESCE(
                    SUM(ld.monto),
                    0
                ) AS total

            FROM liquidacion_detalle ld

            INNER JOIN liquidacion l
                ON l.id = ld.liquidacion_id

            INNER JOIN concepto c
                ON c.id = ld.concepto_id

            WHERE l.estado = 'CERRADA'
              AND c.categoria = 'DESCUENTO'
        ";

        $tipos = "";
        $parametros = [];


        if ($periodoDesde !== '') {

            $sql .= "
                AND l.periodo >= ?
            ";

            $tipos .= "s";
            $parametros[] = $periodoDesde;
        }


        if ($periodoHasta !== '') {

            $sql .= "
                AND l.periodo <= ?
            ";

            $tipos .= "s";
            $parametros[] = $periodoHasta;
        }


        if ($tipoLiquidacion !== '') {

            $sql .= "
                AND l.tipo_liquidacion = ?
            ";

            $tipos .= "s";
            $parametros[] = $tipoLiquidacion;
        }


        $sql .= "
            GROUP BY
                c.id,
                c.nombre

            ORDER BY
                total DESC,
                c.nombre ASC

            LIMIT $limite
        ";


        return $this->ejecutarConsultaPreparada(
            $sql,
            $tipos,
            $parametros
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ESTADÍSTICAS - TOTALES DE LIQUIDACIONES POR PERÍODO
    |--------------------------------------------------------------------------
    |
    | Método mantenido por compatibilidad con otras vistas.
    |
    | Se amplía con filtros opcionales para que pueda reutilizarse sin romper
    | las llamadas anteriores que lo invocan sin parámetros.
    |
    |--------------------------------------------------------------------------
    */

    public function obtenerTotalesLiquidacionesPorPeriodo(
        $periodoDesde = '',
        $periodoHasta = '',
        $tipoLiquidacion = ''
    ) {
        $periodoDesde =
            trim(
                (string)$periodoDesde
            );

        $periodoHasta =
            trim(
                (string)$periodoHasta
            );

        $tipoLiquidacion =
            strtoupper(
                trim(
                    (string)$tipoLiquidacion
                )
            );


        $sql = "
            SELECT
                l.periodo,

                ROUND(
                    SUM(le.total_remunerativo),
                    2
                ) AS total_remunerativo,

                ROUND(
                    SUM(le.total_descuentos),
                    2
                ) AS total_descuentos,

                ROUND(
                    SUM(le.total_no_remunerativo),
                    2
                ) AS total_no_remunerativo,

                ROUND(
                    SUM(le.total_asignaciones),
                    2
                ) AS total_asignaciones,

                ROUND(
                    SUM(le.neto),
                    2
                ) AS total_neto

            FROM liquidacion l

            INNER JOIN liquidacion_empleado le
                ON l.id = le.liquidacion_id

            WHERE l.estado = 'CERRADA'
        ";

        $tipos = "";
        $parametros = [];


        if ($periodoDesde !== '') {

            $sql .= "
                AND l.periodo >= ?
            ";

            $tipos .= "s";
            $parametros[] = $periodoDesde;
        }


        if ($periodoHasta !== '') {

            $sql .= "
                AND l.periodo <= ?
            ";

            $tipos .= "s";
            $parametros[] = $periodoHasta;
        }


        if ($tipoLiquidacion !== '') {

            $sql .= "
                AND l.tipo_liquidacion = ?
            ";

            $tipos .= "s";
            $parametros[] = $tipoLiquidacion;
        }


        $sql .= "
            GROUP BY l.periodo
            ORDER BY l.periodo ASC
        ";


        return $this->ejecutarConsultaPreparada(
            $sql,
            $tipos,
            $parametros
        );
    }



    /*
    |--------------------------------------------------------------------------
    | REPORTE DE AUDITORÍA
    |--------------------------------------------------------------------------
    |
    | Filtros disponibles:
    |
    | - Fecha desde
    | - Fecha hasta
    | - Usuario
    | - Rol
    | - Módulo
    | - Acción
    | - Entidad
    |
    |--------------------------------------------------------------------------
    */

    public function listarAuditoria(
        $fechaDesde = '',
        $fechaHasta = '',
        $usuario = '',
        $rol = '',
        $modulo = '',
        $accion = '',
        $entidad = ''
    ) {
        $sql = "
            SELECT
                a.id,
                a.usuario_id,
                a.usuario_login,
                a.usuario_nombre,
                a.rol,
                a.modulo,
                a.accion,
                a.entidad,
                a.entidad_id,
                a.entidad_descripcion,
                a.detalle,
                a.datos_anteriores,
                a.datos_nuevos,
                a.ip,
                a.fecha_hora

            FROM auditoria a

            WHERE 1 = 1
        ";


        $tipos = "";
        $parametros = [];


        /*
        |--------------------------------------------------------------------------
        | FECHA DESDE
        |--------------------------------------------------------------------------
        */

        if ($fechaDesde !== '') {

            $sql .= "
                AND DATE(a.fecha_hora) >= ?
            ";

            $tipos .= "s";
            $parametros[] = $fechaDesde;
        }


        /*
        |--------------------------------------------------------------------------
        | FECHA HASTA
        |--------------------------------------------------------------------------
        */

        if ($fechaHasta !== '') {

            $sql .= "
                AND DATE(a.fecha_hora) <= ?
            ";

            $tipos .= "s";
            $parametros[] = $fechaHasta;
        }


        /*
        |--------------------------------------------------------------------------
        | USUARIO
        |--------------------------------------------------------------------------
        */

        if ($usuario !== '') {

            $sql .= "
                AND a.usuario_login = ?
            ";

            $tipos .= "s";
            $parametros[] = $usuario;
        }


        /*
        |--------------------------------------------------------------------------
        | ROL
        |--------------------------------------------------------------------------
        */

        if ($rol !== '') {

            $sql .= "
                AND a.rol = ?
            ";

            $tipos .= "s";
            $parametros[] = $rol;
        }


        /*
        |--------------------------------------------------------------------------
        | MÓDULO
        |--------------------------------------------------------------------------
        */

        if ($modulo !== '') {

            $sql .= "
                AND a.modulo = ?
            ";

            $tipos .= "s";
            $parametros[] = $modulo;
        }


        /*
        |--------------------------------------------------------------------------
        | ACCIÓN
        |--------------------------------------------------------------------------
        */

        if ($accion !== '') {

            $sql .= "
                AND a.accion = ?
            ";

            $tipos .= "s";
            $parametros[] = $accion;
        }


        /*
        |--------------------------------------------------------------------------
        | ENTIDAD
        |--------------------------------------------------------------------------
        */

        if ($entidad !== '') {

            $sql .= "
                AND a.entidad = ?
            ";

            $tipos .= "s";
            $parametros[] = $entidad;
        }


        /*
        |--------------------------------------------------------------------------
        | ORDEN
        |--------------------------------------------------------------------------
        */

        $sql .= "
            ORDER BY
                a.fecha_hora DESC,
                a.id DESC
        ";


        return $this->ejecutarConsultaPreparada(
            $sql,
            $tipos,
            $parametros
        );
    }


    /*
    |--------------------------------------------------------------------------
    | OBTENER REGISTRO DE AUDITORÍA POR ID
    |--------------------------------------------------------------------------
    |
    | Se utiliza para mostrar el detalle de una acción sin cargar los JSON
    | completos dentro de la tabla principal.
    |
    |--------------------------------------------------------------------------
    */

    public function obtenerAuditoriaPorId($id)
    {
        $id = (int)$id;


        if ($id <= 0) {

            return null;
        }


        $datos =
            $this->ejecutarConsultaPreparada(
                "
                SELECT
                    a.id,
                    a.usuario_id,
                    a.usuario_login,
                    a.usuario_nombre,
                    a.rol,
                    a.modulo,
                    a.accion,
                    a.entidad,
                    a.entidad_id,
                    a.entidad_descripcion,
                    a.detalle,
                    a.datos_anteriores,
                    a.datos_nuevos,
                    a.ip,
                    a.fecha_hora

                FROM auditoria a

                WHERE a.id = ?

                LIMIT 1
                ",
                "i",
                [
                    $id
                ]
            );


        if (empty($datos)) {

            return null;
        }


        return $datos[0];
    }


    /*
    |--------------------------------------------------------------------------
    | USUARIOS DISPONIBLES EN AUDITORÍA
    |--------------------------------------------------------------------------
    */

    public function obtenerUsuariosAuditoria()
    {
        $sql = "
            SELECT DISTINCT
                usuario_login,
                usuario_nombre

            FROM auditoria

            WHERE usuario_login IS NOT NULL
              AND TRIM(usuario_login) <> ''

            ORDER BY
                usuario_nombre ASC,
                usuario_login ASC
        ";


        return $this->ejecutarConsultaSimple(
            $sql
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ROLES DISPONIBLES EN AUDITORÍA
    |--------------------------------------------------------------------------
    */

    public function obtenerRolesAuditoria()
    {
        $sql = "
            SELECT DISTINCT
                rol

            FROM auditoria

            WHERE rol IS NOT NULL
              AND TRIM(rol) <> ''

            ORDER BY
                rol ASC
        ";


        return $this->ejecutarConsultaSimple(
            $sql
        );
    }


    /*
    |--------------------------------------------------------------------------
    | MÓDULOS DISPONIBLES EN AUDITORÍA
    |--------------------------------------------------------------------------
    */

    public function obtenerModulosAuditoria()
    {
        $sql = "
            SELECT DISTINCT
                modulo

            FROM auditoria

            WHERE modulo IS NOT NULL
              AND TRIM(modulo) <> ''

            ORDER BY
                modulo ASC
        ";


        return $this->ejecutarConsultaSimple(
            $sql
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ACCIONES DISPONIBLES EN AUDITORÍA
    |--------------------------------------------------------------------------
    */

    public function obtenerAccionesAuditoria()
    {
        $sql = "
            SELECT DISTINCT
                accion

            FROM auditoria

            WHERE accion IS NOT NULL
              AND TRIM(accion) <> ''

            ORDER BY
                accion ASC
        ";


        return $this->ejecutarConsultaSimple(
            $sql
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ENTIDADES DISPONIBLES EN AUDITORÍA
    |--------------------------------------------------------------------------
    */

    public function obtenerEntidadesAuditoria()
    {
        $sql = "
            SELECT DISTINCT
                entidad

            FROM auditoria

            WHERE entidad IS NOT NULL
              AND TRIM(entidad) <> ''

            ORDER BY
                entidad ASC
        ";


        return $this->ejecutarConsultaSimple(
            $sql
        );
    }


    /*
    |--------------------------------------------------------------------------
    | RESUMEN GENERAL
    |--------------------------------------------------------------------------
    |
    | Método mantenido por compatibilidad con otras partes del proyecto.
    |
    |--------------------------------------------------------------------------
    */

    public function obtenerResumenGeneral()
    {
        $sql = "
            SELECT
                (
                    SELECT COUNT(*)
                    FROM empleado
                    WHERE activo = 1
                ) AS empleados_activos,

                (
                    SELECT COUNT(*)
                    FROM concepto
                    WHERE activo = 1
                ) AS conceptos_activos,

                (
                    SELECT COUNT(*)
                    FROM liquidacion
                    WHERE estado = 'CERRADA'
                ) AS liquidaciones_cerradas,

                (
                    SELECT COUNT(*)
                    FROM liquidacion
                    WHERE estado = 'ANULADA'
                ) AS liquidaciones_anuladas
        ";

        $resultado =
            $this->conexion->query(
                $sql
            );

        if (!$resultado) {

            throw new Exception(
                "Error al consultar el resumen general: "
                . $this->conexion->error
            );
        }

        return $resultado->fetch_assoc();
    }


    /*
    |--------------------------------------------------------------------------
    | CONSULTA SIMPLE
    |--------------------------------------------------------------------------
    */

    private function ejecutarConsultaSimple($sql)
    {
        $resultado =
            $this->conexion->query(
                $sql
            );

        if (!$resultado) {

            throw new Exception(
                "Error al ejecutar la consulta: "
                . $this->conexion->error
            );
        }


        $datos = [];


        while (
            $fila =
                $resultado->fetch_assoc()
        ) {

            $datos[] =
                $fila;
        }


        return $datos;
    }


    /*
    |--------------------------------------------------------------------------
    | CONSULTA PREPARADA
    |--------------------------------------------------------------------------
    */

    private function ejecutarConsultaPreparada(
        $sql,
        $tipos = '',
        array $parametros = []
    ) {
        $stmt =
            $this->conexion->prepare(
                $sql
            );


        if (!$stmt) {

            throw new Exception(
                "Error al preparar la consulta: "
                . $this->conexion->error
            );
        }


        if (
            $tipos !== ''
            &&
            !empty($parametros)
        ) {

            $referencias = [];


            foreach (
                $parametros as $indice => $valor
            ) {

                $referencias[$indice] =
                    &$parametros[$indice];
            }


            array_unshift(
                $referencias,
                $tipos
            );


            call_user_func_array(
                [
                    $stmt,
                    'bind_param'
                ],
                $referencias
            );
        }


        if (!$stmt->execute()) {

            $error =
                $stmt->error;


            $stmt->close();


            throw new Exception(
                "Error al ejecutar la consulta: "
                . $error
            );
        }


        $resultado =
            $stmt->get_result();


        $datos = [];


        while (
            $fila =
                $resultado->fetch_assoc()
        ) {

            $datos[] =
                $fila;
        }


        $stmt->close();


        return $datos;
    }
}