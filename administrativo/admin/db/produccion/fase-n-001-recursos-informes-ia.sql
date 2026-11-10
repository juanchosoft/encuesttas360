SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS tbl_ia_recursos (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  tbl_usuario_id INT UNSIGNED NOT NULL,
  nombre VARCHAR(150) NOT NULL,
  tipo VARCHAR(20) NOT NULL DEFAULT 'foto',
  mime VARCHAR(40) NOT NULL,
  ruta VARCHAR(200) NOT NULL,
  ancho INT NOT NULL DEFAULT 0,
  alto INT NOT NULL DEFAULT 0,
  tamano INT NOT NULL DEFAULT 0,
  hash_sha1 CHAR(40) NOT NULL,
  origen VARCHAR(500) NULL,
  dt_create DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_ia_recurso_usuario (tbl_usuario_id),
  KEY idx_ia_recurso_hash (tbl_usuario_id, hash_sha1)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET @col_inf_fuente = (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tbl_ia_informes' AND COLUMN_NAME = 'fuente_dsl'
);
SET @ddl_inf_n1 = IF(@col_inf_fuente = 0,
  "ALTER TABLE tbl_ia_informes
     ADD COLUMN fuente_dsl LONGTEXT NULL,
     ADD COLUMN opciones_json TEXT NULL",
  'DO 0'
);
PREPARE stmt_inf_n1 FROM @ddl_inf_n1;
EXECUTE stmt_inf_n1;
DEALLOCATE PREPARE stmt_inf_n1;

INSERT INTO tbl_migraciones_ejecutadas (nombre_migracion, dtejecutada)
SELECT 'fase-n-001-recursos-informes-ia', NOW()
WHERE NOT EXISTS (
  SELECT 1 FROM tbl_migraciones_ejecutadas
  WHERE nombre_migracion = 'fase-n-001-recursos-informes-ia'
);
