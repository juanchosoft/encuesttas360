SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS tbl_ficha_encuestador_municipios (
  id INT(11) NOT NULL AUTO_INCREMENT,
  tbl_ficha_tecnica_encuesta_id INT(11) NOT NULL,
  tbl_usuario_id INT(10) UNSIGNED NOT NULL,
  codigo_departamento VARCHAR(30) NOT NULL,
  codigo_municipio VARCHAR(60) NOT NULL,
  dtcreate DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_ficha_encuestador_municipio (tbl_ficha_tecnica_encuesta_id, tbl_usuario_id, codigo_municipio),
  KEY idx_ficha_enc_mun_usuario (tbl_usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO tbl_migraciones_ejecutadas (nombre_migracion, dtejecutada)
SELECT 'fase-m-008-asignacion-encuestador-municipios', NOW()
WHERE NOT EXISTS (
  SELECT 1 FROM tbl_migraciones_ejecutadas
  WHERE nombre_migracion = 'fase-m-008-asignacion-encuestador-municipios'
);
