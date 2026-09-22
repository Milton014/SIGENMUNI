<?php

class AyudaModelo
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function obtenerConceptosParaGuia()
    {
        $sql = "
            SELECT
                id,
                codigo,
                nombre,
                categoria,
                forma_calculo,
                activo
            FROM concepto
            WHERE codigo BETWEEN 101 AND 499
            ORDER BY
                CAST(codigo AS UNSIGNED) ASC,
                id ASC
        ";

        $resultado = $this->conexion->query($sql);

        if (!$resultado) {
            throw new Exception(
                "Error al cargar los conceptos para la guía de ayuda: "
                . $this->conexion->error
            );
        }

        $conceptos = [];

        while ($fila = $resultado->fetch_assoc()) {
            $conceptos[] = [
                'id' => (int)$fila['id'],
                'codigo' => (int)$fila['codigo'],
                'nombre' => (string)$fila['nombre'],
                'categoria' => strtoupper(trim((string)$fila['categoria'])),
                'forma_calculo' => strtoupper(trim((string)$fila['forma_calculo'])),
                'activo' => (int)$fila['activo']
            ];
        }

        return $conceptos;
    }

    public function obtenerConceptosPorRango($codigoDesde, $codigoHasta)
    {
        $codigoDesde = (int)$codigoDesde;
        $codigoHasta = (int)$codigoHasta;

        if (
            $codigoDesde <= 0
            ||
            $codigoHasta <= 0
            ||
            $codigoDesde > $codigoHasta
        ) {
            throw new Exception(
                "El rango de códigos solicitado para la guía no es válido."
            );
        }

        $sql = "
            SELECT
                id,
                codigo,
                nombre,
                categoria,
                forma_calculo,
                activo
            FROM concepto
            WHERE codigo BETWEEN ? AND ?
            ORDER BY
                CAST(codigo AS UNSIGNED) ASC,
                id ASC
        ";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                "Error al preparar la consulta de conceptos por rango: "
                . $this->conexion->error
            );
        }

        $stmt->bind_param(
            "ii",
            $codigoDesde,
            $codigoHasta
        );

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();

            throw new Exception(
                "Error al consultar los conceptos por rango: "
                . $error
            );
        }

        $resultado = $stmt->get_result();

        $conceptos = [];

        while ($fila = $resultado->fetch_assoc()) {
            $conceptos[] = [
                'id' => (int)$fila['id'],
                'codigo' => (int)$fila['codigo'],
                'nombre' => (string)$fila['nombre'],
                'categoria' => strtoupper(trim((string)$fila['categoria'])),
                'forma_calculo' => strtoupper(trim((string)$fila['forma_calculo'])),
                'activo' => (int)$fila['activo']
            ];
        }

        $stmt->close();

        return $conceptos;
    }

    public function obtenerCodigosUtilizados($codigoDesde, $codigoHasta)
    {
        $conceptos = $this->obtenerConceptosPorRango(
            $codigoDesde,
            $codigoHasta
        );

        $codigos = [];

        foreach ($conceptos as $concepto) {
            $codigos[] = (int)$concepto['codigo'];
        }

        return $codigos;
    }
}
