-- Correcciones sobre datos de intentos y la collation del mensaje de cierre.

UPDATE tbl_cuestionario_intentos i
INNER JOIN tbl_votantes v ON v.id = i.tbl_votante_id
SET i.origen = 'publico', i.tbl_usuario_encuestador_id = NULL
WHERE COALESCE(v.tbl_usuario_id, 0) = 0 AND i.origen = 'interno' AND i.client_uuid IS NULL;

UPDATE tbl_cuestionario_intentos SET tbl_usuario_encuestador_id = NULL WHERE tbl_usuario_encuestador_id = 0;

SET @ddl_col = (SELECT IF(COLLATION_NAME <> 'utf8mb4_unicode_ci', 'ALTER TABLE tbl_ficha_tecnica_encuestas MODIFY mensaje_cierre_defecto TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL', 'DO 0')
  FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tbl_ficha_tecnica_encuestas' AND COLUMN_NAME = 'mensaje_cierre_defecto');
SET @ddl_col = IFNULL(@ddl_col, 'DO 0');
PREPARE stmt_col FROM @ddl_col;
EXECUTE stmt_col;
DEALLOCATE PREPARE stmt_col;

INSERT INTO tbl_migraciones_ejecutadas (nombre_migracion, dtejecutada)
SELECT 'fase-i-011-correcciones-revision', NOW()
WHERE NOT EXISTS (
  SELECT 1 FROM tbl_migraciones_ejecutadas WHERE nombre_migracion = 'fase-i-011-correcciones-revision'
);
