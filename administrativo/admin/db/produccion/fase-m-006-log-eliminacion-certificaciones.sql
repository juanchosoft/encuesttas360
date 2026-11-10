SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS tbl_certificacion_eliminadas_log (
  id INT NOT NULL AUTO_INCREMENT,
  tbl_certificacion_id INT NOT NULL,
  origen_tipo VARCHAR(30) NULL,
  tbl_ficha_tecnica_encuesta_id INT NULL,
  tbl_sondeo_id INT NULL,
  tbl_intento_id INT NULL,
  tbl_usuario_encuestador_id INT NULL,
  tbl_votante_id INT NULL,
  tbl_usuario_id INT NOT NULL,
  motivo TEXT NOT NULL,
  estado_revision VARCHAR(30) NULL,
  audio_ruta VARCHAR(255) NULL,
  respuestas_borradas INT NOT NULL DEFAULT 0,
  copia_json LONGTEXT NULL,
  dtcreate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_elim_cert (tbl_certificacion_id),
  KEY idx_elim_ficha (tbl_ficha_tecnica_encuesta_id),
  KEY idx_elim_usuario (tbl_usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO tbl_migraciones_ejecutadas (nombre_migracion, dtejecutada)
SELECT 'fase-m-006-log-eliminacion-certificaciones', NOW()
WHERE NOT EXISTS (
  SELECT 1 FROM tbl_migraciones_ejecutadas
  WHERE nombre_migracion = 'fase-m-006-log-eliminacion-certificaciones'
);
