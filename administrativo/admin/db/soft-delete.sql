
-- ---------------------------------------------------------------------------
-- 1/3 Espacio geográfico (habilitado = eliminado lógicamente)
-- ---------------------------------------------------------------------------
SET @col_hab = (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'tbl_espacio_geografico'
    AND COLUMN_NAME = 'habilitado'
);

SET @ddl_hab = IF(@col_hab = 0,
  "ALTER TABLE tbl_espacio_geografico ADD COLUMN habilitado ENUM('si','no') NOT NULL DEFAULT 'si' COMMENT 'si=activo, no=eliminado logicamente' AFTER tbl_usuario_id",
  'SELECT 1'
);
PREPARE stmt_hab FROM @ddl_hab;
EXECUTE stmt_hab;
DEALLOCATE PREPARE stmt_hab;

INSERT INTO tbl_migraciones_ejecutadas (nombre_migracion, dtejecutada)
SELECT 'espacio-geografico-soft-delete', NOW()
WHERE NOT EXISTS (
  SELECT 1 FROM tbl_migraciones_ejecutadas
  WHERE nombre_migracion = 'espacio-geografico-soft-delete'
);

-- ---------------------------------------------------------------------------
-- 2/3 Ficha técnica (eliminado separado de habilitado)
-- ---------------------------------------------------------------------------
SET @col_elim_ft = (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'tbl_ficha_tecnica_encuestas'
    AND COLUMN_NAME = 'eliminado'
);

SET @ddl_elim_ft = IF(@col_elim_ft = 0,
  "ALTER TABLE tbl_ficha_tecnica_encuestas ADD COLUMN eliminado ENUM('si','no') NOT NULL DEFAULT 'no' COMMENT 'si=eliminada logicamente, no=visible' AFTER habilitado",
  'SELECT 1'
);
PREPARE stmt_elim_ft FROM @ddl_elim_ft;
EXECUTE stmt_elim_ft;
DEALLOCATE PREPARE stmt_elim_ft;

INSERT INTO tbl_migraciones_ejecutadas (nombre_migracion, dtejecutada)
SELECT 'ficha-tecnica-soft-delete', NOW()
WHERE NOT EXISTS (
  SELECT 1 FROM tbl_migraciones_ejecutadas
  WHERE nombre_migracion = 'ficha-tecnica-soft-delete'
);

-- ---------------------------------------------------------------------------
-- 3/3 Sondeos (eliminado separado de habilitado)
-- ---------------------------------------------------------------------------
SET @col_elim_s = (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'tbl_sondeo'
    AND COLUMN_NAME = 'eliminado'
);

SET @ddl_elim_s = IF(@col_elim_s = 0,
  "ALTER TABLE tbl_sondeo ADD COLUMN eliminado ENUM('si','no') NOT NULL DEFAULT 'no' COMMENT 'si=eliminado logicamente, no=visible' AFTER habilitado",
  'SELECT 1'
);
PREPARE stmt_elim_s FROM @ddl_elim_s;
EXECUTE stmt_elim_s;
DEALLOCATE PREPARE stmt_elim_s;

INSERT INTO tbl_migraciones_ejecutadas (nombre_migracion, dtejecutada)
SELECT 'sondeos-soft-delete', NOW()
WHERE NOT EXISTS (
  SELECT 1 FROM tbl_migraciones_ejecutadas
  WHERE nombre_migracion = 'sondeos-soft-delete'
);

-- Registro del bundle completo
INSERT INTO tbl_migraciones_ejecutadas (nombre_migracion, dtejecutada)
SELECT 'fase-f-bundle-soft-delete', NOW()
WHERE NOT EXISTS (
  SELECT 1 FROM tbl_migraciones_ejecutadas
  WHERE nombre_migracion = 'fase-f-bundle-soft-delete'
);
