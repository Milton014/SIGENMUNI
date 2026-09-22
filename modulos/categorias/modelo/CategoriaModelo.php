<?php

class CategoriaModelo
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function listar($buscar = '', $activo = '')
    {
        $sql = "
            SELECT
                c.id,
                c.codigo,
                c.nombre,
                c.activo,

                (
                    SELECT cv101.monto
                    FROM concepto_valor cv101
                    INNER JOIN concepto co101
                        ON cv101.concepto_id = co101.id
                    WHERE co101.codigo = 101
                      AND co101.activo = 1
                      AND cv101.categoria_id = c.id
                      AND cv101.activo = 1
                      AND (
                            cv101.fecha_desde IS NULL
                            OR cv101.fecha_desde <= CURDATE()
                      )
                      AND (
                            cv101.fecha_hasta IS NULL
                            OR cv101.fecha_hasta >= CURDATE()
                      )
                    ORDER BY
                        CASE
                            WHEN cv101.fecha_desde IS NULL THEN 1
                            ELSE 0
                        END,
                        cv101.fecha_desde DESC,
                        cv101.id DESC
                    LIMIT 1
                ) AS sueldo_basico_actual,

                (
                    SELECT cv102.monto
                    FROM concepto_valor cv102
                    INNER JOIN concepto co102
                        ON cv102.concepto_id = co102.id
                    WHERE co102.codigo = 102
                      AND co102.activo = 1
                      AND cv102.categoria_id = c.id
                      AND cv102.activo = 1
                      AND (
                            cv102.fecha_desde IS NULL
                            OR cv102.fecha_desde <= CURDATE()
                      )
                      AND (
                            cv102.fecha_hasta IS NULL
                            OR cv102.fecha_hasta >= CURDATE()
                      )
                    ORDER BY
                        CASE
                            WHEN cv102.fecha_desde IS NULL THEN 1
                            ELSE 0
                        END,
                        cv102.fecha_desde DESC,
                        cv102.id DESC
                    LIMIT 1
                ) AS dedicacion_funcional_actual,

                (
                    SELECT cv104.monto
                    FROM concepto_valor cv104
                    INNER JOIN concepto co104
                        ON cv104.concepto_id = co104.id
                    WHERE co104.codigo = 104
                      AND co104.activo = 1
                      AND cv104.categoria_id = c.id
                      AND cv104.activo = 1
                      AND (
                            cv104.fecha_desde IS NULL
                            OR cv104.fecha_desde <= CURDATE()
                      )
                      AND (
                            cv104.fecha_hasta IS NULL
                            OR cv104.fecha_hasta >= CURDATE()
                      )
                    ORDER BY
                        CASE
                            WHEN cv104.fecha_desde IS NULL THEN 1
                            ELSE 0
                        END,
                        cv104.fecha_desde DESC,
                        cv104.id DESC
                    LIMIT 1
                ) AS suplemento_especial_actual

            FROM categoria c
            WHERE 1 = 1
        ";

        $params = [];
        $types = '';

        $buscar = trim((string)$buscar);

        if ($buscar !== '') {

            /*
            |--------------------------------------------------------------------------
            | BÚSQUEDA NUMÉRICA
            |--------------------------------------------------------------------------
            |
            | Si el usuario ingresa un número, se busca exclusivamente por
            | código exacto.
            |
            | Ejemplo:
            |
            | 1    -> código 1
            | 19   -> código 19
            | 1001 -> código 1001
            |
            | Así evitamos que buscar "1" también encuentre nombres como
            | Categoría 18, Categoría 19 o Categoría 21.
            |
            |--------------------------------------------------------------------------
            */

            if (ctype_digit($buscar)) {

                $sql .= " AND c.codigo = ? ";

                $codigoBuscar = (int)$buscar;

                $params[] = $codigoBuscar;

                $types .= 'i';

            } else {

                /*
                |--------------------------------------------------------------------------
                | BÚSQUEDA POR NOMBRE
                |--------------------------------------------------------------------------
                |
                | Cuando el usuario escribe texto, se permite coincidencia parcial.
                |
                |--------------------------------------------------------------------------
                */

                $sql .= " AND c.nombre LIKE ? ";

                $like = '%' . $buscar . '%';

                $params[] = $like;

                $types .= 's';
            }
        }

        if ($activo !== '' && ((string)$activo === '0' || (string)$activo === '1')) {
            $sql .= " AND c.activo = ? ";
            $params[] = (int)$activo;
            $types .= 'i';
        }

        $sql .= "
            ORDER BY
                CASE
                    WHEN c.codigo >= 1000 THEN 1
                    ELSE 2
                END,
                c.codigo ASC,
                c.nombre ASC
        ";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                'Error al preparar el listado de categorías: ' .
                $this->conexion->error
            );
        }

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();
            throw new Exception('Error al consultar las categorías: ' . $error);
        }

        $resultado = $stmt->get_result();
        $categorias = [];

        while ($fila = $resultado->fetch_assoc()) {
            $categorias[] = [
                'id' => (int)$fila['id'],
                'codigo' => (int)$fila['codigo'],
                'nombre' => $fila['nombre'],

                /*
                |--------------------------------------------------------------
                | VALORES VIGENTES DESDE concepto_valor
                |--------------------------------------------------------------
                */

                'sueldo_basico_actual' =>
                    $fila['sueldo_basico_actual'] !== null
                        ? (float)$fila['sueldo_basico_actual']
                        : null,

                'dedicacion_funcional_actual' =>
                    $fila['dedicacion_funcional_actual'] !== null
                        ? (float)$fila['dedicacion_funcional_actual']
                        : null,

                'suplemento_especial_actual' =>
                    $fila['suplemento_especial_actual'] !== null
                        ? (float)$fila['suplemento_especial_actual']
                        : null,

                'activo' =>
                    (int)$fila['activo']
            ];
        }

        $stmt->close();
        return $categorias;
    }

    public function obtenerPorId($id)
    {
        $id = (int)$id;

        if ($id <= 0) {
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

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                'Error al preparar la consulta de la categoría: ' .
                $this->conexion->error
            );
        }

        $stmt->bind_param('i', $id);

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();
            throw new Exception('Error al consultar la categoría: ' . $error);
        }

        $resultado = $stmt->get_result();
        $fila = $resultado->fetch_assoc();
        $stmt->close();

        if (!$fila) {
            return null;
        }

        return [
            'id' => (int)$fila['id'],
            'codigo' => (int)$fila['codigo'],
            'nombre' => $fila['nombre'],
            'activo' => (int)$fila['activo']
        ];
    }

    public function existeCodigo($codigo, $idExcluir = null)
    {
        $codigo = (int)$codigo;

        if ($codigo <= 0) {
            return false;
        }

        $sql = "SELECT id FROM categoria WHERE codigo = ?";

        if ($idExcluir !== null) {
            $sql .= " AND id <> ?";
        }

        $sql .= " LIMIT 1";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                'Error al validar el código de la categoría: ' .
                $this->conexion->error
            );
        }

        if ($idExcluir === null) {
            $stmt->bind_param('i', $codigo);
        } else {
            $idExcluir = (int)$idExcluir;
            $stmt->bind_param('ii', $codigo, $idExcluir);
        }

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();
            throw new Exception('Error al validar el código de la categoría: ' . $error);
        }

        $resultado = $stmt->get_result();
        $existe = $resultado->num_rows > 0;
        $stmt->close();

        return $existe;
    }

    public function existeNombre($nombre, $idExcluir = null)
    {
        $nombre = trim((string)$nombre);

        if ($nombre === '') {
            return false;
        }

        $sql = "
            SELECT id
            FROM categoria
            WHERE LOWER(TRIM(nombre)) = LOWER(TRIM(?))
        ";

        if ($idExcluir !== null) {
            $sql .= " AND id <> ?";
        }

        $sql .= " LIMIT 1";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                'Error al validar el nombre de la categoría: ' .
                $this->conexion->error
            );
        }

        if ($idExcluir === null) {
            $stmt->bind_param('s', $nombre);
        } else {
            $idExcluir = (int)$idExcluir;
            $stmt->bind_param('si', $nombre, $idExcluir);
        }

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();
            throw new Exception('Error al validar el nombre de la categoría: ' . $error);
        }

        $resultado = $stmt->get_result();
        $existe = $resultado->num_rows > 0;
        $stmt->close();

        return $existe;
    }

    public function guardar($datos)
    {
        $codigo = (int)($datos['codigo'] ?? 0);
        $nombre = trim((string)($datos['nombre'] ?? ''));
        $activo = isset($datos['activo']) ? (int)$datos['activo'] : 1;

        if ($codigo <= 0) {
            throw new Exception('El código de la categoría debe ser mayor a cero.');
        }

        if ($nombre === '') {
            throw new Exception('Debe ingresar el nombre de la categoría.');
        }

        if (!in_array($activo, [0, 1], true)) {
            $activo = 1;
        }

        if ($this->existeCodigo($codigo)) {
            throw new Exception('Ya existe una categoría con ese código.');
        }

        if ($this->existeNombre($nombre)) {
            throw new Exception('Ya existe una categoría con ese nombre.');
        }

        $sql = "
            INSERT INTO categoria (
                codigo,
                nombre,
                activo
            )
            VALUES (?, ?, ?)
        ";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                'Error al preparar el alta de la categoría: ' .
                $this->conexion->error
            );
        }

        $stmt->bind_param(
            'isi',
            $codigo,
            $nombre,
            $activo
        );

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();
            throw new Exception('No se pudo guardar la categoría: ' . $error);
        }

        $id = $stmt->insert_id;
        $stmt->close();

        return $id;
    }

    public function actualizar($id, $datos)
    {
        $id = (int)$id;
        $codigo = (int)($datos['codigo'] ?? 0);
        $nombre = trim((string)($datos['nombre'] ?? ''));
        $activo = isset($datos['activo']) ? (int)$datos['activo'] : 0;

        if ($id <= 0) {
            throw new Exception('Categoría inválida.');
        }

        if (!$this->obtenerPorId($id)) {
            throw new Exception('La categoría indicada no existe.');
        }

        if ($codigo <= 0) {
            throw new Exception('El código de la categoría debe ser mayor a cero.');
        }

        if ($nombre === '') {
            throw new Exception('Debe ingresar el nombre de la categoría.');
        }

        if (!in_array($activo, [0, 1], true)) {
            $activo = 0;
        }

        if ($this->existeCodigo($codigo, $id)) {
            throw new Exception('Ya existe otra categoría con ese código.');
        }

        if ($this->existeNombre($nombre, $id)) {
            throw new Exception('Ya existe otra categoría con ese nombre.');
        }

        $sql = "
            UPDATE categoria
            SET
                codigo = ?,
                nombre = ?,
                activo = ?
            WHERE id = ?
            LIMIT 1
        ";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                'Error al preparar la actualización de la categoría: ' .
                $this->conexion->error
            );
        }

        $stmt->bind_param('isii', $codigo, $nombre, $activo, $id);

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();
            throw new Exception('No se pudo actualizar la categoría: ' . $error);
        }

        $stmt->close();
        return true;
    }

    public function cambiarEstado($id, $activo)
    {
        $id = (int)$id;
        $activo = (int)$activo;

        if ($id <= 0) {
            throw new Exception('Categoría inválida.');
        }

        if (!in_array($activo, [0, 1], true)) {
            throw new Exception('Estado de categoría inválido.');
        }

        if (!$this->obtenerPorId($id)) {
            throw new Exception('La categoría indicada no existe.');
        }

        $sql = "UPDATE categoria SET activo = ? WHERE id = ? LIMIT 1";
        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                'Error al preparar el cambio de estado de la categoría: ' .
                $this->conexion->error
            );
        }

        $stmt->bind_param('ii', $activo, $id);

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();
            throw new Exception('No se pudo cambiar el estado de la categoría: ' . $error);
        }

        $stmt->close();
        return true;
    }

    public function alternarEstado($id)
    {
        $categoria = $this->obtenerPorId($id);

        if (!$categoria) {
            throw new Exception('La categoría indicada no existe.');
        }

        $nuevoEstado = ((int)$categoria['activo'] === 1) ? 0 : 1;
        $this->cambiarEstado($id, $nuevoEstado);

        return $nuevoEstado;
    }

    public function listarActivas()
    {
        $sql = "
            SELECT id, codigo, nombre
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
                'Error al consultar las categorías activas: ' .
                $this->conexion->error
            );
        }

        $categorias = [];

        while ($fila = $resultado->fetch_assoc()) {
            $categorias[] = [
                'id' => (int)$fila['id'],
                'codigo' => (int)$fila['codigo'],
                'nombre' => $fila['nombre']
            ];
        }

        return $categorias;
    }

    public function contarEmpleadosAsociados($categoriaId, $soloActivos = false)
    {
        $categoriaId = (int)$categoriaId;

        if ($categoriaId <= 0) {
            return 0;
        }

        $sql = "SELECT COUNT(*) AS total FROM empleado WHERE categoria_id = ?";

        if ($soloActivos) {
            $sql .= " AND activo = 1";
        }

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                'Error al preparar el conteo de empleados por categoría: ' .
                $this->conexion->error
            );
        }

        $stmt->bind_param('i', $categoriaId);

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();
            throw new Exception('Error al contar empleados de la categoría: ' . $error);
        }

        $resultado = $stmt->get_result();
        $fila = $resultado->fetch_assoc();
        $stmt->close();

        return (int)($fila['total'] ?? 0);
    }

    public function contarValoresConceptosAsociados($categoriaId)
    {
        $categoriaId = (int)$categoriaId;

        if ($categoriaId <= 0) {
            return 0;
        }

        $sql = "SELECT COUNT(*) AS total FROM concepto_valor WHERE categoria_id = ?";
        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                'Error al preparar el conteo de valores de conceptos: ' .
                $this->conexion->error
            );
        }

        $stmt->bind_param('i', $categoriaId);

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();
            throw new Exception('Error al contar valores asociados a la categoría: ' . $error);
        }

        $resultado = $stmt->get_result();
        $fila = $resultado->fetch_assoc();
        $stmt->close();

        return (int)($fila['total'] ?? 0);
    }
}