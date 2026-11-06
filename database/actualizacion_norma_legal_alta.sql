-- SIGENMUNI: norma legal que respalda el alta inicial del empleado.
-- Seleccionar primero la base de datos de SIGENMUNI.
-- Ejecutar UNA VEZ antes de copiar los PHP de esta actualización.
-- Los empleados existentes quedan con NULL (norma no registrada).
ALTER TABLE empleado
    ADD COLUMN norma_legal_alta VARCHAR(100) NULL DEFAULT NULL AFTER fecha_alta;

-- Comprobación de la columna creada:
SHOW COLUMNS FROM empleado LIKE 'norma_legal_alta';
