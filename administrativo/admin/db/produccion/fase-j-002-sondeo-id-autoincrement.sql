SET NAMES utf8mb4;

SET @sondeo_pk = (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tbl_sondeo' AND CONSTRAINT_TYPE = 'PRIMARY KEY'
);
SET @ddl_sondeo_pk = IF(@sondeo_pk = 0,
  "ALTER TABLE tbl_sondeo ADD PRIMARY KEY (id)",
  'DO 0');
PREPARE stmt_sondeo_pk FROM @ddl_sondeo_pk;
EXECUTE stmt_sondeo_pk;
DEALLOCATE PREPARE stmt_sondeo_pk;

SET @sondeo_auto = (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tbl_sondeo' AND COLUMN_NAME = 'id' AND EXTRA LIKE '%auto_increment%'
);
SET @ddl_sondeo_auto = IF(@sondeo_auto = 0,
  "ALTER TABLE tbl_sondeo MODIFY id INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador único'",
  'DO 0');
PREPARE stmt_sondeo_auto FROM @ddl_sondeo_auto;
EXECUTE stmt_sondeo_auto;
DEALLOCATE PREPARE stmt_sondeo_auto;

INSERT INTO tbl_migraciones_ejecutadas (nombre_migracion, dtejecutada)
SELECT 'fase-j-002-sondeo-id-autoincrement', NOW()
WHERE NOT EXISTS (
  SELECT 1 FROM tbl_migraciones_ejecutadas
  WHERE nombre_migracion = 'fase-j-002-sondeo-id-autoincrement'
);
