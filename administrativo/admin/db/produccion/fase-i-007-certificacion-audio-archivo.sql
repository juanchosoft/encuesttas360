SET @col_cert_int = (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tbl_certificacion_encuestador' AND COLUMN_NAME = 'tbl_cuestionario_intento_id'
);
SET @ddl_cert_int = IF(@col_cert_int = 0,
  "ALTER TABLE tbl_certificacion_encuestador
     ADD COLUMN tbl_cuestionario_intento_id INT NULL AFTER tbl_ficha_tecnica_encuesta_id,
     ADD COLUMN audio_ruta VARCHAR(255) NULL AFTER audio_base64,
     ADD COLUMN audio_bytes INT UNSIGNED NULL AFTER audio_ruta,
     ADD COLUMN audio_sha256 CHAR(64) NULL AFTER audio_bytes,
     ADD UNIQUE KEY idx_uniq_cert_intento (tbl_cuestionario_intento_id)",
  'DO 0'
);
PREPARE stmt_cert_int FROM @ddl_cert_int;
EXECUTE stmt_cert_int;
DEALLOCATE PREPARE stmt_cert_int;

INSERT INTO tbl_migraciones_ejecutadas (nombre_migracion, dtejecutada)
SELECT 'fase-i-007-certificacion-audio-archivo', NOW()
WHERE NOT EXISTS (
  SELECT 1 FROM tbl_migraciones_ejecutadas
  WHERE nombre_migracion = 'fase-i-007-certificacion-audio-archivo'
);
