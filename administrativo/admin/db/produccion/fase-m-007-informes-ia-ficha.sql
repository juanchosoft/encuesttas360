SET NAMES utf8mb4;

SET @col_inf_ficha = (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tbl_ia_informes' AND COLUMN_NAME = 'ficha_tecnica_id'
);
SET @ddl_inf_1 = IF(@col_inf_ficha = 0,
  "ALTER TABLE tbl_ia_informes
     ADD COLUMN ficha_tecnica_id INT NULL,
     ADD COLUMN subtitulo VARCHAR(200) NULL",
  'DO 0'
);
PREPARE stmt_inf_1 FROM @ddl_inf_1;
EXECUTE stmt_inf_1;
DEALLOCATE PREPARE stmt_inf_1;

INSERT INTO tbl_migraciones_ejecutadas (nombre_migracion, dtejecutada)
SELECT 'fase-m-007-informes-ia-ficha', NOW()
WHERE NOT EXISTS (
  SELECT 1 FROM tbl_migraciones_ejecutadas
  WHERE nombre_migracion = 'fase-m-007-informes-ia-ficha'
);
