-- Ejecutar UNA VEZ antes de instalar los tres PHP de esta actualización.
ALTER TABLE usuario
    ADD COLUMN version_acceso INT UNSIGNED NOT NULL DEFAULT 0;
