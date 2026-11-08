SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS tbl_ficha_encuestadores (
  id INT(11) NOT NULL AUTO_INCREMENT,
  tbl_ficha_tecnica_encuesta_id INT(11) NOT NULL,
  tbl_usuario_id INT(10) UNSIGNED NOT NULL,
  dtcreate DATETIME DEFAULT CURRENT_TIMESTAMP,
  tbl_usuario_asigno_id INT(10) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_ficha_encuestador (tbl_ficha_tecnica_encuesta_id, tbl_usuario_id),
  KEY idx_ficha_encuestador_usuario (tbl_usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO tbl_migraciones_ejecutadas (nombre_migracion, dtejecutada)
SELECT 'fase-m-001-asignacion-encuestadores', NOW()
WHERE NOT EXISTS (
  SELECT 1 FROM tbl_migraciones_ejecutadas
  WHERE nombre_migracion = 'fase-m-001-asignacion-encuestadores'
);
