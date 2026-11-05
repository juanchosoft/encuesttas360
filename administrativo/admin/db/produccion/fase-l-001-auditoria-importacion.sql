SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS tbl_auditoria_preguntas (
  id INT(11) NOT NULL AUTO_INCREMENT,
  tbl_usuario_id INT(11) DEFAULT NULL,
  archivo_nombre VARCHAR(255) DEFAULT NULL,
  cantidad_preguntas INT(11) NOT NULL DEFAULT 0,
  cantidad_opciones INT(11) NOT NULL DEFAULT 0,
  fecha_subida DATETIME DEFAULT NULL,
  ip_address VARCHAR(45) DEFAULT NULL,
  navegador VARCHAR(255) DEFAULT NULL,
  estado VARCHAR(20) DEFAULT NULL,
  mensaje_error TEXT,
  tbl_ficha_tecnica_encuesta_id INT(11) DEFAULT NULL,
  tbl_encuesta_id INT(11) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY idx_aud_preg_ficha (tbl_ficha_tecnica_encuesta_id),
  KEY idx_aud_preg_usuario (tbl_usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET @aud_col = (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tbl_auditoria_preguntas' AND COLUMN_NAME = 'tbl_ficha_tecnica_encuesta_id'
);
SET @ddl_aud_col = IF(@aud_col = 0,
  "ALTER TABLE tbl_auditoria_preguntas ADD COLUMN tbl_ficha_tecnica_encuesta_id INT(11) DEFAULT NULL",
  'DO 0');
PREPARE stmt_aud_col FROM @ddl_aud_col;
EXECUTE stmt_aud_col;
DEALLOCATE PREPARE stmt_aud_col;

INSERT INTO tbl_migraciones_ejecutadas (nombre_migracion, dtejecutada)
SELECT 'fase-l-001-auditoria-importacion', NOW()
WHERE NOT EXISTS (
  SELECT 1 FROM tbl_migraciones_ejecutadas
  WHERE nombre_migracion = 'fase-l-001-auditoria-importacion'
);
