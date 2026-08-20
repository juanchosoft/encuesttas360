-- Agregar campos para identificar el origen de la certificación
-- (sondeo o cuestionario)

ALTER TABLE `tbl_certificacion_encuestador`
ADD COLUMN `origen_tipo` ENUM('sondeo', 'cuestionario', 'registro_simple') DEFAULT 'registro_simple' COMMENT 'Tipo de origen de la certificación' AFTER `tbl_usuario_id`,
ADD COLUMN `tbl_sondeo_id` int(11) DEFAULT NULL COMMENT 'FK al sondeo si aplica' AFTER `origen_tipo`,
ADD COLUMN `tbl_ficha_tecnica_encuesta_id` int(11) DEFAULT NULL COMMENT 'FK al cuestionario si aplica' AFTER `tbl_sondeo_id`,
ADD KEY `idx_origen_tipo` (`origen_tipo`),
ADD KEY `idx_sondeo` (`tbl_sondeo_id`),
ADD KEY `idx_cuestionario` (`tbl_ficha_tecnica_encuesta_id`);
