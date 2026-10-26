SET @col_desc = (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tbl_zonas_muestreo' AND COLUMN_NAME = 'descripcion'
);
SET @ddl_zona_desc = IF(@col_desc = 0,
  "ALTER TABLE tbl_zonas_muestreo ADD COLUMN descripcion TEXT NULL AFTER nombre",
  'DO 0');
PREPARE stmt_zona_desc FROM @ddl_zona_desc;
EXECUTE stmt_zona_desc;
DEALLOCATE PREPARE stmt_zona_desc;

INSERT INTO tbl_migraciones_ejecutadas (nombre_migracion, dtejecutada)
SELECT 'fase-i-013-zonas-descripcion', NOW()
WHERE NOT EXISTS (
  SELECT 1 FROM tbl_migraciones_ejecutadas
  WHERE nombre_migracion = 'fase-i-013-zonas-descripcion'
);
