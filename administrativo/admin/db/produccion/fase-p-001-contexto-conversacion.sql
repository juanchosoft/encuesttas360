SET NAMES utf8mb4;

SET @col_ia_ctx = (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tbl_ia_conversaciones' AND COLUMN_NAME = 'contexto_json'
);
SET @ddl_ia_ctx = IF(@col_ia_ctx = 0,
  "ALTER TABLE tbl_ia_conversaciones ADD COLUMN contexto_json TEXT NULL",
  'DO 0'
);
PREPARE stmt_ia_ctx FROM @ddl_ia_ctx;
EXECUTE stmt_ia_ctx;
DEALLOCATE PREPARE stmt_ia_ctx;

INSERT INTO tbl_migraciones_ejecutadas (nombre_migracion, dtejecutada)
SELECT 'fase-p-001-contexto-conversacion', NOW()
WHERE NOT EXISTS (
  SELECT 1 FROM tbl_migraciones_ejecutadas WHERE nombre_migracion = 'fase-p-001-contexto-conversacion'
);
