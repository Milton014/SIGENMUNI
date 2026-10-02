-- Ejecutar UNA VEZ antes de instalar los PHP de esta actualización.
ALTER TABLE usuario
    ADD COLUMN login_intentos TINYINT UNSIGNED NOT NULL DEFAULT 0,
    ADD COLUMN login_bloqueado_hasta DATETIME NULL DEFAULT NULL;
