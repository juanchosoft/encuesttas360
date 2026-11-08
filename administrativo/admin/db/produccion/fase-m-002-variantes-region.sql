SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS tbl_preguntas_grilla_variantes_region (
  id INT NOT NULL AUTO_INCREMENT,
  tbl_pregunta_id INT NOT NULL,
  region VARCHAR(50) NOT NULL,
  tbl_grilla_plantilla_id INT NOT NULL,
  dtcreate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY idx_uniq_preg_region (tbl_pregunta_id, region),
  KEY idx_var_reg_plantilla (tbl_grilla_plantilla_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO tbl_migraciones_ejecutadas (nombre_migracion, dtejecutada)
SELECT 'fase-m-002-variantes-region', NOW()
WHERE NOT EXISTS (
  SELECT 1 FROM tbl_migraciones_ejecutadas
  WHERE nombre_migracion = 'fase-m-002-variantes-region'
);
