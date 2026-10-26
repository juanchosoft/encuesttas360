-- Opciones sin pregunta quedan marcadas como eliminadas (no se borra nada).

UPDATE tbl_opciones_respuesta o
LEFT JOIN tbl_preguntas p ON p.id = o.tbl_pregunta_id
SET o.eliminado = 1
WHERE p.id IS NULL AND o.eliminado = 0;

INSERT INTO tbl_migraciones_ejecutadas (nombre_migracion, dtejecutada)
SELECT 'fase-i-008-huerfanas', NOW()
WHERE NOT EXISTS (
  SELECT 1 FROM tbl_migraciones_ejecutadas WHERE nombre_migracion = 'fase-i-008-huerfanas'
);
