-- =====================================================================
-- Cambio de tbl_usuario_id a tbl_votante_id en grilla
-- =====================================================================
-- Este cambio permite que los votantes registrados voten en las grillas
-- en lugar de los usuarios del sistema administrativo
-- =====================================================================

USE estadisticas_db;

-- Paso 1: Limpiar datos existentes de sesiones de votación y respuestas
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE tbl_grilla_respuestas;
TRUNCATE TABLE tbl_grilla_sesion_votacion;
SET FOREIGN_KEY_CHECKS = 1;

-- Paso 2: Modificar tbl_grilla_sesion_votacion
ALTER TABLE tbl_grilla_sesion_votacion
    DROP INDEX `unique_sesion_diaria`,
    DROP INDEX `idx_usuario`;

-- Paso 3: Cambiar nombre de columna tbl_usuario_id a tbl_votante_id
ALTER TABLE tbl_grilla_sesion_votacion
    CHANGE COLUMN `tbl_usuario_id` `tbl_votante_id` INT(11) NOT NULL COMMENT 'Votante que participa en la grilla';

-- Paso 4: Recrear índices y constraints con tbl_votante_id
ALTER TABLE tbl_grilla_sesion_votacion
    ADD UNIQUE KEY `unique_sesion_diaria` (`tbl_grilla_id`, `tbl_votante_id`, `dtcreate_date`),
    ADD KEY `idx_votante` (`tbl_votante_id`),
    ADD CONSTRAINT `fk_sesion_votante` FOREIGN KEY (`tbl_votante_id`) REFERENCES `tbl_votantes` (`id`) ON DELETE CASCADE;

-- Paso 5: Actualizar comentario de la tabla
ALTER TABLE tbl_grilla_sesion_votacion
    COMMENT = 'Sesión de votación: registra que un votante participó en una grilla';

-- Verificar estructura actualizada
SHOW CREATE TABLE tbl_grilla_sesion_votacion;

SELECT 'Estructura actualizada correctamente' as mensaje;
