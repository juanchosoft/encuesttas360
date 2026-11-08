SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS tbl_preguntas_ambito (
  id INT NOT NULL AUTO_INCREMENT,
  tbl_pregunta_id INT NOT NULL,
  tipo ENUM('region','municipio') NOT NULL,
  valor VARCHAR(50) NOT NULL,
  dtcreate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY idx_uniq_preg_ambito (tbl_pregunta_id, tipo, valor)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO tbl_migraciones_ejecutadas (nombre_migracion, dtejecutada)
SELECT 'fase-m-003-ambito-preguntas', NOW()
WHERE NOT EXISTS (
  SELECT 1 FROM tbl_migraciones_ejecutadas
  WHERE nombre_migracion = 'fase-m-003-ambito-preguntas'
);
