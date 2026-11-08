SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS tbl_grilla_columna_excepciones (
  id INT NOT NULL AUTO_INCREMENT,
  tbl_grilla_columna_id INT NOT NULL,
  tbl_grilla_fila_id INT NOT NULL,
  condicion_columna_id INT NULL,
  condicion_opcion_id INT NULL,
  dtcreate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY idx_uniq_col_fila (tbl_grilla_columna_id, tbl_grilla_fila_id),
  KEY idx_exc_fila (tbl_grilla_fila_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO tbl_migraciones_ejecutadas (nombre_migracion, dtejecutada)
SELECT 'fase-m-004-grilla-excepciones-fila', NOW()
WHERE NOT EXISTS (
  SELECT 1 FROM tbl_migraciones_ejecutadas
  WHERE nombre_migracion = 'fase-m-004-grilla-excepciones-fila'
);
