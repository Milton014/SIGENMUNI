-- Ejecutar UNA VEZ en la base SIGENMUNI antes de copiar los archivos PHP.
-- No modifica contraseñas, cuentas ni permisos.
ALTER TABLE usuario
    ADD COLUMN recuperacion_intentos TINYINT UNSIGNED NOT NULL DEFAULT 0;
