SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS tbl_certificacion_respuestas_log (
  id INT NOT NULL AUTO_INCREMENT,
  tbl_certificacion_id INT NOT NULL,
  tbl_intento_id INT NOT NULL,
  tbl_usuario_id INT NOT NULL,
  motivo TEXT NOT NULL,
  estado_antes VARCHAR(30) NULL,
  estado_despues VARCHAR(30) NULL,
  texto_antes LONGTEXT NULL,
  texto_despues LONGTEXT NULL,
  respuestas_antes LONGTEXT NULL,
  respuestas_despues LONGTEXT NULL,
  dtcreate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_log_resp_cert (tbl_certificacion_id),
  KEY idx_log_resp_intento (tbl_intento_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO tbl_migraciones_ejecutadas (nombre_migracion, dtejecutada)
SELECT 'fase-m-005-log-edicion-respuestas', NOW()
WHERE NOT EXISTS (
  SELECT 1 FROM tbl_migraciones_ejecutadas
  WHERE nombre_migracion = 'fase-m-005-log-edicion-respuestas'
);
