<?php

/*
|--------------------------------------------------------------------------
| MODELO - INICIO
|--------------------------------------------------------------------------
|
| Encapsula las consultas necesarias para construir el Menú Principal:
|
| - rol actual;
| - permisos del rol.
|
|--------------------------------------------------------------------------
*/

class InicioModelo
{
    private $conexion;


    public function __construct($conexion)
    {
        $this->conexion =
            $conexion;
    }


    /*
    |--------------------------------------------------------------------------
    | OBTENER ROL
    |--------------------------------------------------------------------------
    */

    public function obtenerRolPorId($rolId)
    {
        $rolId =
            (int)$rolId;


        if ($rolId <= 0) {

            return null;
        }


        $stmt =
            $this->conexion
                ->prepare(
                    "
                    SELECT
                        id,
                        nombre,
                        es_admin,
                        activo
                    FROM rol
                    WHERE id = ?
                    LIMIT 1
                    "
                );


        if (!$stmt) {

            throw new RuntimeException(
                "No se pudo preparar la consulta del rol."
            );
        }


        $stmt->bind_param(
            "i",
            $rolId
        );


        if (!$stmt->execute()) {

            $stmt->close();

            throw new RuntimeException(
                "No se pudo consultar el rol actual."
            );
        }


        $resultado =
            $stmt->get_result();


        $rol =
            $resultado
            &&
            $resultado->num_rows > 0
                ?
                $resultado->fetch_assoc()
                :
                null;


        $stmt->close();


        return $rol;
    }


    /*
    |--------------------------------------------------------------------------
    | OBTENER PERMISOS DEL ROL
    |--------------------------------------------------------------------------
    */

    public function obtenerPermisosPorRol($rolId)
    {
        $rolId =
            (int)$rolId;


        if ($rolId <= 0) {

            return [];
        }


        $stmt =
            $this->conexion
                ->prepare(
                    "
                    SELECT
                        archivo,
                        permitido
                    FROM rol_modulo_permiso
                    WHERE rol_id = ?
                    "
                );


        if (!$stmt) {

            throw new RuntimeException(
                "No se pudo preparar la consulta de permisos."
            );
        }


        $stmt->bind_param(
            "i",
            $rolId
        );


        if (!$stmt->execute()) {

            $stmt->close();

            throw new RuntimeException(
                "No se pudieron consultar los permisos del rol."
            );
        }


        $resultado =
            $stmt->get_result();


        $permisos =
            [];


        while (
            $resultado
            &&
            $fila = $resultado->fetch_assoc()
        ) {

            $archivo =
                trim(
                    (string)(
                        $fila['archivo']
                        ??
                        ''
                    )
                );


            if ($archivo === '') {

                continue;
            }


            $permisos[$archivo] =
                (int)(
                    $fila['permitido']
                    ??
                    0
                );
        }


        $stmt->close();


        return $permisos;
    }
}
