SET NAMES utf8mb4;

SET @col_opc_color = (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tbl_opciones_respuesta' AND COLUMN_NAME = 'color'
);
SET @ddl_opc_color = IF(@col_opc_color = 0,
  "ALTER TABLE tbl_opciones_respuesta ADD COLUMN color VARCHAR(7) NULL",
  'DO 0'
);
PREPARE stmt_opc_color FROM @ddl_opc_color;
EXECUTE stmt_opc_color;
DEALLOCATE PREPARE stmt_opc_color;

UPDATE tbl_opciones_respuesta o
JOIN (
  SELECT o1.id,
         (SELECT COUNT(*) FROM tbl_opciones_respuesta o2
           WHERE o2.tbl_pregunta_id = o1.tbl_pregunta_id
             AND (COALESCE(o2.eliminado, 0), COALESCE(o2.orden, 999999), o2.id) < (COALESCE(o1.eliminado, 0), COALESCE(o1.orden, 999999), o1.id)) AS pos
  FROM tbl_opciones_respuesta o1
  WHERE o1.color IS NULL
  LIMIT 18446744073709551615
) x ON x.id = o.id
SET o.color = ELT((x.pos MOD 24) + 1,
  '#1F77B4', '#FF7F0E', '#2CA02C', '#D62728', '#9467BD', '#8C564B', '#E377C2', '#17BECF',
  '#BCBD22', '#393B79', '#E6AB02', '#1B9E77', '#A6761D', '#E7298A', '#66A61E', '#D95F02',
  '#7570B3', '#3182BD', '#31A354', '#CE6DBD', '#843C39', '#637939', '#0B7285', '#8E24AA')
WHERE o.color IS NULL;

INSERT INTO tbl_migraciones_ejecutadas (nombre_migracion, dtejecutada)
SELECT 'fase-o-001-color-opciones', NOW()
WHERE NOT EXISTS (
  SELECT 1 FROM tbl_migraciones_ejecutadas WHERE nombre_migracion = 'fase-o-001-color-opciones'
);
