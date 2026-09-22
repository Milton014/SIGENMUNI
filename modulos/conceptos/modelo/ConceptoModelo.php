<?php

class ConceptoModelo
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
    | LISTAR CONCEPTOS
    |--------------------------------------------------------------------------
    */

    public function listar(
        $buscar = "",
        $categoria = "",
        $activo = ""
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
                asignable_empleado,
                base_calculo,
                orden_calculo,
                aplica_sac,
                visible_recibo,
                activo,
                descripcion,
                fecha_desde,
                fecha_hasta
            FROM concepto
            WHERE 1 = 1
        ";

        $params = [];
        $types = "";

        if ($buscar !== "") {

            $sql .= "
                AND (
                    CAST(codigo AS CHAR) LIKE ?
                    OR nombre LIKE ?
                )
            ";

            $busquedaLike = "%" . $buscar . "%";

            $params[] = $busquedaLike;
            $params[] = $busquedaLike;

            $types .= "ss";
        }

        if ($categoria !== "") {

            $sql .= "
                AND categoria = ?
            ";

            $params[] = $categoria;
            $types .= "s";
        }

        if ($activo !== "") {

            $sql .= "
                AND activo = ?
            ";

            $params[] = (int)$activo;
            $types .= "i";
        }

        $sql .= "
            ORDER BY
                codigo ASC,
                nombre ASC
        ";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                "Error al preparar la consulta de conceptos: "
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

            $error = $stmt->error;
            $stmt->close();

            throw new Exception(
                "Error al consultar los conceptos: "
                . $error
            );
        }

        $resultado = $stmt->get_result();

        $conceptos = [];

        while ($fila = $resultado->fetch_assoc()) {
            $conceptos[] = $fila;
        }

        $stmt->close();

        return $conceptos;
    }


    /*
    |--------------------------------------------------------------------------
    | OBTENER CONCEPTO POR ID
    |--------------------------------------------------------------------------
    */

    public function obtenerPorId($id)
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
                asignable_empleado,
                base_calculo,
                orden_calculo,
                aplica_sac,
                visible_recibo,
                activo,
                descripcion,
                fecha_desde,
                fecha_hasta
            FROM concepto
            WHERE id = ?
            LIMIT 1
        ";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                "Error al preparar la consulta del concepto: "
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
                "Error al consultar el concepto: "
                . $error
            );
        }

        $resultado = $stmt->get_result();

        $concepto = $resultado->fetch_assoc();

        $stmt->close();

        return $concepto;
    }


    /*
    |--------------------------------------------------------------------------
    | OBTENER NOMBRE DEL CONCEPTO
    |--------------------------------------------------------------------------
    */

    public function obtenerNombreConcepto($conceptoId)
    {
        $sql = "
            SELECT nombre
            FROM concepto
            WHERE id = ?
            LIMIT 1
        ";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                "Error al preparar la consulta del concepto: "
                . $this->conexion->error
            );
        }

        $stmt->bind_param(
            "i",
            $conceptoId
        );

        if (!$stmt->execute()) {

            $error = $stmt->error;
            $stmt->close();

            throw new Exception(
                "Error al consultar el concepto: "
                . $error
            );
        }

        $resultado = $stmt->get_result();

        $fila = $resultado->fetch_assoc();

        $stmt->close();

        return $fila
            ? $fila['nombre']
            : null;
    }


    /*
    |--------------------------------------------------------------------------
    | CATEGORÍAS PERMITIDAS
    |--------------------------------------------------------------------------
    */

    public function obtenerCategorias()
    {
        return [
            'REMUNERATIVO',
            'NO_REMUNERATIVO',
            'ASIGNACION_FAMILIAR',
            'DESCUENTO',
            'APORTE_PATRONAL'
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDAR CATEGORÍA
    |--------------------------------------------------------------------------
    */

    public function categoriaValida($categoria)
    {
        return in_array(
            $categoria,
            $this->obtenerCategorias(),
            true
        );
    }


    /*
    |--------------------------------------------------------------------------
    | FORMAS DE CÁLCULO
    |--------------------------------------------------------------------------
    */

    public function obtenerFormasCalculo()
    {
        /*
        |--------------------------------------------------------------------------
        | FORMAS DISPONIBLES EN LA NUEVA ARQUITECTURA
        |--------------------------------------------------------------------------
        |
        | TABLA_CATEGORIA : solo 101, 102 y 104.
        | MANUAL          : importe por empleado.
        | PORCENTAJE      : porcentaje por empleado.
        | FORMULA         : concepto automático calculado por el motor.
        |
        | FIJO se conserva únicamente como dato histórico en la base, pero ya no
        | se ofrece para nuevas altas o modificaciones.
        |
        */

        return [
            'TABLA_CATEGORIA',
            'MANUAL',
            'PORCENTAJE',
            'FORMULA'
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDAR FORMA DE CÁLCULO
    |--------------------------------------------------------------------------
    */

    public function formaCalculoValida($formaCalculo)
    {
        return in_array(
            $formaCalculo,
            $this->obtenerFormasCalculo(),
            true
        );
    }



    /*
    |--------------------------------------------------------------------------
    | BASES DE CÁLCULO PARA CONCEPTOS PORCENTUALES
    |--------------------------------------------------------------------------
    |
    | La base_calculo se utiliza para cualquier concepto configurado como
    | PORCENTAJE. El porcentaje concreto se carga por empleado desde
    | Conceptos por Empleado.
    |
    | Bases disponibles:
    |
    | BASICO
    |     -> 101 Sueldo Básico
    |
    | BASICO_MAS_DEDICACION
    |     -> 101 Sueldo Básico + 102 Dedicación Funcional
    |
    | TOTAL_REMUNERATIVO
    |     -> Total Remunerativo
    |
    | TOTAL_REMUNERATIVO_MENOS_DESCUENTOS_OBLIGATORIOS
    |     -> Total Remunerativo menos 301, 302, 303, 304, 306 y 307
    |
    */

    public function obtenerBasesCalculoPorcentaje()
    {
        return [
            'BASICO',
            'BASICO_MAS_DEDICACION',
            'TOTAL_REMUNERATIVO',
            'TOTAL_REMUNERATIVO_MENOS_DESCUENTOS_OBLIGATORIOS'
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDAR BASE DE CÁLCULO PORCENTUAL
    |--------------------------------------------------------------------------
    */

    public function baseCalculoPorcentajeValida($baseCalculo)
    {
        $baseCalculo =
            strtoupper(
                trim(
                    (string)$baseCalculo
                )
            );


        return in_array(
            $baseCalculo,
            $this->obtenerBasesCalculoPorcentaje(),
            true
        );
    }


    /*
    |--------------------------------------------------------------------------
    | NORMALIZAR BASE DE CÁLCULO DEL CONCEPTO
    |--------------------------------------------------------------------------
    |
    | Todo concepto PORCENTAJE debe conservar una base_calculo válida.
    |
    | Para MANUAL, TABLA_CATEGORIA y FORMULA:
    |
    | base_calculo = NULL
    |
    */

    private function normalizarBaseCalculoConcepto(
        $categoria,
        $formaCalculo,
        $baseCalculo
    ) {
        $formaCalculo =
            strtoupper(
                trim(
                    (string)$formaCalculo
                )
            );


        if ($formaCalculo !== 'PORCENTAJE') {

            return null;
        }


        $baseCalculo =
            strtoupper(
                trim(
                    (string)$baseCalculo
                )
            );


        if (
            !$this->baseCalculoPorcentajeValida(
                $baseCalculo
            )
        ) {

            throw new Exception(
                "Debe seleccionar una base de cálculo válida para el concepto porcentual."
            );
        }


        return $baseCalculo;
    }


    /*
    |--------------------------------------------------------------------------
    | NORMALIZAR REQUIERE MANUAL
    |--------------------------------------------------------------------------
    |
    | Regla de negocio:
    |
    | - TABLA_CATEGORIA nunca requiere carga manual por empleado.
    | - MANUAL sí requiere carga manual.
    | - PORCENTAJE y FORMULA no requieren monto manual.
    |
    | Además, los conceptos 101, 102 y 104 quedan blindados explícitamente
    | porque sus importes se administran por categoría.
    |
    |--------------------------------------------------------------------------
    */

    private function normalizarRequiereManualConcepto(
        $codigo,
        $formaCalculo
    ) {
        $codigo =
            (int)$codigo;


        $formaCalculo =
            strtoupper(
                trim(
                    (string)$formaCalculo
                )
            );


        if (
            in_array(
                $codigo,
                [
                    101,
                    102,
                    104
                ],
                true
            )
        ) {

            return 0;
        }


        return
            $formaCalculo === 'MANUAL'
                ? 1
                : 0;
    }


    /*
    |--------------------------------------------------------------------------
    | NORMALIZAR ASIGNABLE A EMPLEADO
    |--------------------------------------------------------------------------
    |
    | MANUAL y PORCENTAJE pueden asignarse individualmente.
    | TABLA_CATEGORIA y FORMULA son automáticos.
    |
    |--------------------------------------------------------------------------
    */

    private function normalizarAsignableEmpleadoConcepto(
        $formaCalculo
    ) {
        $formaCalculo =
            strtoupper(
                trim(
                    (string)$formaCalculo
                )
            );


        return
            (
                $formaCalculo === 'MANUAL'
                ||
                $formaCalculo === 'PORCENTAJE'
            )
                ? 1
                : 0;
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDAR CÓDIGO DUPLICADO
    |--------------------------------------------------------------------------
    */

    public function existeCodigo(
        $codigo,
        $idExcluir = null
    ) {
        if ($idExcluir === null) {

            $sql = "
                SELECT id
                FROM concepto
                WHERE codigo = ?
                LIMIT 1
            ";

            $stmt = $this->conexion->prepare($sql);

            if (!$stmt) {
                throw new Exception(
                    "Error al preparar la validación del código: "
                    . $this->conexion->error
                );
            }

            $stmt->bind_param(
                "i",
                $codigo
            );

        } else {

            $sql = "
                SELECT id
                FROM concepto
                WHERE codigo = ?
                  AND id <> ?
                LIMIT 1
            ";

            $stmt = $this->conexion->prepare($sql);

            if (!$stmt) {
                throw new Exception(
                    "Error al preparar la validación del código: "
                    . $this->conexion->error
                );
            }

            $stmt->bind_param(
                "ii",
                $codigo,
                $idExcluir
            );
        }

        if (!$stmt->execute()) {

            $error = $stmt->error;
            $stmt->close();

            throw new Exception(
                "Error al validar el código del concepto: "
                . $error
            );
        }

        $resultado = $stmt->get_result();

        $existe = $resultado->num_rows > 0;

        $stmt->close();

        return $existe;
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDAR NOMBRE DUPLICADO
    |--------------------------------------------------------------------------
    */

    public function existeNombre(
        $nombre,
        $idExcluir = null
    ) {
        if ($idExcluir === null) {

            $sql = "
                SELECT id
                FROM concepto
                WHERE nombre = ?
                LIMIT 1
            ";

            $stmt = $this->conexion->prepare($sql);

            if (!$stmt) {
                throw new Exception(
                    "Error al preparar la validación del nombre: "
                    . $this->conexion->error
                );
            }

            $stmt->bind_param(
                "s",
                $nombre
            );

        } else {

            $sql = "
                SELECT id
                FROM concepto
                WHERE nombre = ?
                  AND id <> ?
                LIMIT 1
            ";

            $stmt = $this->conexion->prepare($sql);

            if (!$stmt) {
                throw new Exception(
                    "Error al preparar la validación del nombre: "
                    . $this->conexion->error
                );
            }

            $stmt->bind_param(
                "si",
                $nombre,
                $idExcluir
            );
        }

        if (!$stmt->execute()) {

            $error = $stmt->error;
            $stmt->close();

            throw new Exception(
                "Error al validar el nombre del concepto: "
                . $error
            );
        }

        $resultado = $stmt->get_result();

        $existe = $resultado->num_rows > 0;

        $stmt->close();

        return $existe;
    }


    /*
    |--------------------------------------------------------------------------
    | GUARDAR CONCEPTO
    |--------------------------------------------------------------------------
    */

    public function guardar($datos)
    {
        $sql = "
            INSERT INTO concepto (
                codigo,
                nombre,
                categoria,
                forma_calculo,
                porcentaje,
                monto_fijo,
                requiere_manual,
                asignable_empleado,
                base_calculo,
                orden_calculo,
                aplica_sac,
                visible_recibo,
                activo,
                descripcion,
                fecha_desde,
                fecha_hasta
            )
            VALUES (
                ?, ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?, ?,
                ?, ?, ?, ?
            )
        ";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                "No se pudo preparar el alta del concepto: "
                . $this->conexion->error
            );
        }

        $codigo = $datos['codigo'];
        $nombre = $datos['nombre'];
        $categoria = $datos['categoria'];
        $formaCalculo = $datos['forma_calculo'];

        /*
        | porcentaje, monto_fijo y orden_calculo quedan en la tabla únicamente
        | por compatibilidad histórica.
        |
        | base_calculo se utiliza para cualquier concepto PORCENTAJE.
        */
        $porcentaje = 0.0;
        $montoFijo = 0.0;
        $ordenCalculo = 0;

        $baseCalculo =
            $this->normalizarBaseCalculoConcepto(
                $categoria,
                $formaCalculo,
                $datos['base_calculo'] ?? null
            );

        /*
        |--------------------------------------------------------------------------
        | REGLAS DERIVADAS DE LA FORMA DE CÁLCULO
        |--------------------------------------------------------------------------
        |
        | No confiamos en valores externos para estos dos campos.
        |
        | Especialmente:
        | 101, 102 y 104 = TABLA_CATEGORIA
        | requiere_manual = 0
        | asignable_empleado = 0
        |
        |--------------------------------------------------------------------------
        */

        $requiereManual =
            $this->normalizarRequiereManualConcepto(
                $codigo,
                $formaCalculo
            );


        $asignableEmpleado =
            $this->normalizarAsignableEmpleadoConcepto(
                $formaCalculo
            );

        $aplicaSac = (int)($datos['aplica_sac'] ?? 0);
        $visibleRecibo = (int)($datos['visible_recibo'] ?? 0);
        $activo = (int)($datos['activo'] ?? 0);
        $descripcion = $datos['descripcion'] ?? null;
        $fechaDesde = $datos['fecha_desde'] ?? null;
        $fechaHasta = $datos['fecha_hasta'] ?? null;

        $stmt->bind_param(
            "isssddiisiiiisss",
            $codigo,
            $nombre,
            $categoria,
            $formaCalculo,
            $porcentaje,
            $montoFijo,
            $requiereManual,
            $asignableEmpleado,
            $baseCalculo,
            $ordenCalculo,
            $aplicaSac,
            $visibleRecibo,
            $activo,
            $descripcion,
            $fechaDesde,
            $fechaHasta
        );

        if (!$stmt->execute()) {

            $error = $stmt->error;
            $stmt->close();

            throw new Exception(
                "No se pudo guardar el concepto: "
                . $error
            );
        }

        $idConcepto = $stmt->insert_id;

        $stmt->close();

        return $idConcepto;
    }


    /*
    |--------------------------------------------------------------------------
    | ACTUALIZAR CONCEPTO
    |--------------------------------------------------------------------------
    */

    public function actualizar(
        $id,
        $datos
    ) {
        /*
        |--------------------------------------------------------------------------
        | ACTUALIZACIÓN EN LA NUEVA ARQUITECTURA
        |--------------------------------------------------------------------------
        |
        | porcentaje, monto_fijo y orden_calculo permanecen fuera del UPDATE.
        |
        | base_calculo se actualiza para cualquier concepto PORCENTAJE.
        |
        | En MANUAL, TABLA_CATEGORIA y FORMULA se normaliza a NULL.
        |
        */

        $sql = "
            UPDATE concepto
            SET
                codigo = ?,
                nombre = ?,
                categoria = ?,
                forma_calculo = ?,
                requiere_manual = ?,
                asignable_empleado = ?,
                base_calculo = ?,
                aplica_sac = ?,
                visible_recibo = ?,
                activo = ?,
                descripcion = ?,
                fecha_desde = ?,
                fecha_hasta = ?
            WHERE id = ?
        ";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                "No se pudo preparar la actualización del concepto: "
                . $this->conexion->error
            );
        }

        $codigo = (int)$datos['codigo'];
        $nombre = $datos['nombre'];
        $categoria = $datos['categoria'];
        $formaCalculo = $datos['forma_calculo'];
        /*
        |--------------------------------------------------------------------------
        | REGLAS DERIVADAS DE LA FORMA DE CÁLCULO
        |--------------------------------------------------------------------------
        */

        $requiereManual =
            $this->normalizarRequiereManualConcepto(
                $codigo,
                $formaCalculo
            );


        $asignableEmpleado =
            $this->normalizarAsignableEmpleadoConcepto(
                $formaCalculo
            );


        $baseCalculo =
            $this->normalizarBaseCalculoConcepto(
                $categoria,
                $formaCalculo,
                $datos['base_calculo'] ?? null
            );

        $aplicaSac = (int)($datos['aplica_sac'] ?? 0);
        $visibleRecibo = (int)($datos['visible_recibo'] ?? 0);
        $activo = (int)($datos['activo'] ?? 0);
        $descripcion = $datos['descripcion'] ?? null;
        $fechaDesde = $datos['fecha_desde'] ?? null;
        $fechaHasta = $datos['fecha_hasta'] ?? null;
        $id = (int)$id;

        $stmt->bind_param(
            "isssiisiiisssi",
            $codigo,
            $nombre,
            $categoria,
            $formaCalculo,
            $requiereManual,
            $asignableEmpleado,
            $baseCalculo,
            $aplicaSac,
            $visibleRecibo,
            $activo,
            $descripcion,
            $fechaDesde,
            $fechaHasta,
            $id
        );

        if (!$stmt->execute()) {

            $error = $stmt->error;
            $stmt->close();

            throw new Exception(
                "No se pudo actualizar el concepto: "
                . $error
            );
        }

        $stmt->close();

        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | CAMBIAR ESTADO DEL CONCEPTO
    |--------------------------------------------------------------------------
    */

    public function cambiarEstado(
        $id,
        $activo
    ) {
        if (
            $activo !== 0 &&
            $activo !== 1
        ) {
            throw new Exception(
                "Estado de concepto no válido."
            );
        }

        $sql = "
            UPDATE concepto
            SET activo = ?
            WHERE id = ?
        ";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                "No se pudo preparar el cambio de estado: "
                . $this->conexion->error
            );
        }

        $stmt->bind_param(
            "ii",
            $activo,
            $id
        );

        if (!$stmt->execute()) {

            $error = $stmt->error;
            $stmt->close();

            throw new Exception(
                "No se pudo actualizar el estado del concepto: "
                . $error
            );
        }

        $stmt->close();

        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | LISTAR VALORES DE CONCEPTOS
    |--------------------------------------------------------------------------
    */

    public function listarValores(
        $conceptoId = 0
    ) {
        $sql = "
            SELECT
                cv.id,
                cv.concepto_id,
                cv.categoria_id,
                cv.escalafon_id,

                c.codigo,
                c.nombre AS concepto,

                ca.nombre AS categoria,

                e.nombre AS escalafon,

                cv.monto,
                cv.porcentaje,
                cv.fecha_desde,
                cv.fecha_hasta,
                cv.activo

            FROM concepto_valor cv

            INNER JOIN concepto c
                ON cv.concepto_id = c.id

            LEFT JOIN categoria ca
                ON cv.categoria_id = ca.id

            LEFT JOIN escalafon e
                ON cv.escalafon_id = e.id
        ";

        if ($conceptoId > 0) {
            $sql .= "
                WHERE cv.concepto_id = ?
            ";
        }

        $sql .= "
            ORDER BY
                c.nombre ASC,
                CASE
                    WHEN ca.codigo >= 1000 THEN 1
                    ELSE 2
                END,
                ca.codigo ASC,
                ca.nombre ASC,
                cv.fecha_desde DESC,
                cv.id DESC
        ";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                "Error al preparar la consulta de valores: "
                . $this->conexion->error
            );
        }

        if ($conceptoId > 0) {
            $stmt->bind_param(
                "i",
                $conceptoId
            );
        }

        if (!$stmt->execute()) {

            $error = $stmt->error;
            $stmt->close();

            throw new Exception(
                "Error al consultar los valores de conceptos: "
                . $error
            );
        }

        $resultado = $stmt->get_result();

        $valores = [];

        while ($fila = $resultado->fetch_assoc()) {
            $valores[] = $fila;
        }

        $stmt->close();

        return $valores;
    }


    /*
    |--------------------------------------------------------------------------
    | OBTENER VALOR POR ID
    |--------------------------------------------------------------------------
    */

    public function obtenerValorPorId($id)
    {
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

                c.codigo AS concepto_codigo,
                c.nombre AS concepto_nombre,
                c.forma_calculo,
                c.activo AS concepto_activo

            FROM concepto_valor cv

            INNER JOIN concepto c
                ON cv.concepto_id = c.id

            WHERE cv.id = ?

            LIMIT 1
        ";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                "Error al preparar la consulta del valor: "
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
                "Error al consultar el valor del concepto: "
                . $error
            );
        }

        $resultado = $stmt->get_result();

        $valor = $resultado->fetch_assoc();

        $stmt->close();

        return $valor;
    }


    /*
    |--------------------------------------------------------------------------
    | CONCEPTOS ACTIVOS PARA VALORES
    |--------------------------------------------------------------------------
    */

    public function obtenerConceptosActivosParaValores()
    {
        /*
        |--------------------------------------------------------------------------
        | SOLO CONCEPTOS SALARIALES POR CATEGORÍA
        |--------------------------------------------------------------------------
        |
        | La tabla concepto_valor queda reservada para:
        | 101 - Sueldo Básico
        | 102 - Dedicación Funcional
        | 104 - Suplemento Especial
        |
        */

        $sql = "
            SELECT
                id,
                codigo,
                nombre,
                forma_calculo
            FROM concepto
            WHERE activo = 1
              AND codigo IN (101, 102, 104)
              AND forma_calculo = 'TABLA_CATEGORIA'
            ORDER BY codigo ASC
        ";

        $resultado = $this->conexion->query($sql);

        if (!$resultado) {
            throw new Exception(
                "Error al cargar los conceptos salariales por categoría: "
                . $this->conexion->error
            );
        }

        $conceptos = [];

        while ($fila = $resultado->fetch_assoc()) {
            $conceptos[] = $fila;
        }

        return $conceptos;
    }


    /*
    |--------------------------------------------------------------------------
    | CATEGORÍAS PARA VALORES
    |--------------------------------------------------------------------------
    */

    public function obtenerCategoriasParaValores()
    {
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

        $resultado = $this->conexion->query($sql);

        if (!$resultado) {
            throw new Exception(
                "Error al cargar las categorías: "
                . $this->conexion->error
            );
        }

        $categorias = [];

        while ($fila = $resultado->fetch_assoc()) {
            $categorias[] = $fila;
        }

        return $categorias;
    }


    /*
    |--------------------------------------------------------------------------
    | ESCALAFONES PARA VALORES
    |--------------------------------------------------------------------------
    */

    public function obtenerEscalafonesParaValores()
    {
        $sql = "
            SELECT
                id,
                nombre
            FROM escalafon
            ORDER BY nombre ASC
        ";

        $resultado = $this->conexion->query($sql);

        if (!$resultado) {
            throw new Exception(
                "Error al cargar los escalafones: "
                . $this->conexion->error
            );
        }

        $escalafones = [];

        while ($fila = $resultado->fetch_assoc()) {
            $escalafones[] = $fila;
        }

        return $escalafones;
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDAR CATEGORÍA POR ID
    |--------------------------------------------------------------------------
    */

    public function existeCategoriaId($categoriaId)
    {
        $sql = "
            SELECT id
            FROM categoria
            WHERE id = ?
            LIMIT 1
        ";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                "Error al preparar la validación de categoría: "
                . $this->conexion->error
            );
        }

        $stmt->bind_param(
            "i",
            $categoriaId
        );

        if (!$stmt->execute()) {

            $error = $stmt->error;
            $stmt->close();

            throw new Exception(
                "Error al validar la categoría: "
                . $error
            );
        }

        $resultado = $stmt->get_result();

        $existe = $resultado->num_rows > 0;

        $stmt->close();

        return $existe;
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDAR ESCALAFÓN POR ID
    |--------------------------------------------------------------------------
    */

    public function existeEscalafonId($escalafonId)
    {
        $sql = "
            SELECT id
            FROM escalafon
            WHERE id = ?
            LIMIT 1
        ";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                "Error al preparar la validación de escalafón: "
                . $this->conexion->error
            );
        }

        $stmt->bind_param(
            "i",
            $escalafonId
        );

        if (!$stmt->execute()) {

            $error = $stmt->error;
            $stmt->close();

            throw new Exception(
                "Error al validar el escalafón: "
                . $error
            );
        }

        $resultado = $stmt->get_result();

        $existe = $resultado->num_rows > 0;

        $stmt->close();

        return $existe;
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDAR VALOR DUPLICADO
    |--------------------------------------------------------------------------
    */

    public function existeValorDuplicado(
        $conceptoId,
        $categoriaId,
        $escalafonId,
        $fechaDesde,
        $idExcluir = null
    ) {
        /*
        |--------------------------------------------------------------------------
        | NUEVA REGLA DE UNICIDAD
        |--------------------------------------------------------------------------
        |
        | El escalafón ya no participa. Se conserva el parámetro únicamente para
        | mantener compatibilidad con llamadas existentes del controlador.
        |
        */

        $sql = "
            SELECT id
            FROM concepto_valor
            WHERE concepto_id = ?
              AND categoria_id = ?
              AND fecha_desde = ?
              AND activo = 1
        ";

        if ($idExcluir !== null) {
            $sql .= " AND id <> ? ";
        }

        $sql .= " LIMIT 1 ";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                "Error al preparar la validación de valores duplicados: "
                . $this->conexion->error
            );
        }

        $conceptoId = (int)$conceptoId;
        $categoriaId = (int)$categoriaId;

        if ($idExcluir === null) {
            $stmt->bind_param(
                "iis",
                $conceptoId,
                $categoriaId,
                $fechaDesde
            );
        } else {
            $idExcluir = (int)$idExcluir;
            $stmt->bind_param(
                "iisi",
                $conceptoId,
                $categoriaId,
                $fechaDesde,
                $idExcluir
            );
        }

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();

            throw new Exception(
                "Error al validar valores duplicados: "
                . $error
            );
        }

        $resultado = $stmt->get_result();
        $existe = $resultado->num_rows > 0;

        $stmt->close();

        return $existe;
    }


    /*
    |--------------------------------------------------------------------------
    | GUARDAR VALOR
    |--------------------------------------------------------------------------
    */

    public function guardarValor($datos)
    {
        $this->validarConceptoParaValorCategoria(
            (int)$datos['concepto_id']
        );

        $sql = "
            INSERT INTO concepto_valor (
                concepto_id,
                categoria_id,
                escalafon_id,
                monto,
                porcentaje,
                fecha_desde,
                fecha_hasta,
                activo
            )
            VALUES (
                ?, ?, NULL, ?, 0, ?, ?, ?
            )
        ";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                "No se pudo preparar el alta del valor: "
                . $this->conexion->error
            );
        }

        $conceptoId = (int)$datos['concepto_id'];
        $categoriaId = (int)$datos['categoria_id'];
        $monto = (float)$datos['monto'];
        $fechaDesde = $datos['fecha_desde'];
        $fechaHasta = $datos['fecha_hasta'];
        $activo = (int)$datos['activo'];

        $stmt->bind_param(
            "iidssi",
            $conceptoId,
            $categoriaId,
            $monto,
            $fechaDesde,
            $fechaHasta,
            $activo
        );

        if (!$stmt->execute()) {

            $error = $stmt->error;
            $stmt->close();

            throw new Exception(
                "No se pudo guardar el valor del concepto: "
                . $error
            );
        }

        $idValor = $stmt->insert_id;

        $stmt->close();

        return $idValor;
    }


    /*
    |--------------------------------------------------------------------------
    | ACTUALIZAR VALOR
    |--------------------------------------------------------------------------
    */

    public function actualizarValor(
        $id,
        $datos
    ) {
        $this->validarConceptoParaValorCategoria(
            (int)$datos['concepto_id']
        );

        $sql = "
            UPDATE concepto_valor
            SET
                concepto_id = ?,
                categoria_id = ?,
                escalafon_id = NULL,
                monto = ?,
                porcentaje = 0,
                fecha_desde = ?,
                fecha_hasta = ?
            WHERE id = ?
        ";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                "No se pudo preparar la actualización del valor: "
                . $this->conexion->error
            );
        }

        $conceptoId = (int)$datos['concepto_id'];
        $categoriaId = (int)$datos['categoria_id'];
        $monto = (float)$datos['monto'];
        $fechaDesde = $datos['fecha_desde'];
        $fechaHasta = $datos['fecha_hasta'];
        $id = (int)$id;

        $stmt->bind_param(
            "iidssi",
            $conceptoId,
            $categoriaId,
            $monto,
            $fechaDesde,
            $fechaHasta,
            $id
        );

        if (!$stmt->execute()) {

            $error = $stmt->error;
            $stmt->close();

            throw new Exception(
                "No se pudo actualizar el valor del concepto: "
                . $error
            );
        }

        $stmt->close();

        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDAR CONCEPTO PARA VALOR POR CATEGORÍA
    |--------------------------------------------------------------------------
    */

    private function validarConceptoParaValorCategoria($conceptoId)
    {
        $sql = "
            SELECT id
            FROM concepto
            WHERE id = ?
              AND activo = 1
              AND codigo IN (101, 102, 104)
              AND forma_calculo = 'TABLA_CATEGORIA'
            LIMIT 1
        ";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                "Error al validar el concepto salarial: "
                . $this->conexion->error
            );
        }

        $conceptoId = (int)$conceptoId;

        $stmt->bind_param(
            "i",
            $conceptoId
        );

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();

            throw new Exception(
                "Error al validar el concepto salarial: "
                . $error
            );
        }

        $resultado = $stmt->get_result();
        $valido = $resultado->num_rows > 0;

        $stmt->close();

        if (!$valido) {
            throw new Exception(
                "Solo los conceptos 101, 102 y 104 pueden tener valores por categoría."
            );
        }

        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | CAMBIAR ESTADO DE VALOR
    |--------------------------------------------------------------------------
    */

    public function cambiarEstadoValor(
        $id,
        $activo
    ) {
        /*
        |--------------------------------------------------------------------------
        | VALIDAR ESTADO
        |--------------------------------------------------------------------------
        */

        if (
            $activo !== 0 &&
            $activo !== 1
        ) {
            throw new Exception(
                "Estado de valor no válido."
            );
        }


        /*
        |--------------------------------------------------------------------------
        | ACTUALIZAR
        |--------------------------------------------------------------------------
        */

        $sql = "
            UPDATE concepto_valor
            SET activo = ?
            WHERE id = ?
        ";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                "No se pudo preparar el cambio de estado del valor: "
                . $this->conexion->error
            );
        }

        $stmt->bind_param(
            "ii",
            $activo,
            $id
        );

        if (!$stmt->execute()) {

            $error = $stmt->error;

            $stmt->close();

            throw new Exception(
                "No se pudo actualizar el estado del valor: "
                . $error
            );
        }

        $stmt->close();

        return true;
    }
}