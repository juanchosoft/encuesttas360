SET NAMES utf8mb4;

UPDATE tbl_opciones_respuesta o
INNER JOIN tbl_preguntas p ON p.id = o.tbl_pregunta_id
SET o.eliminado = 1
WHERE p.tipo_pregunta IN ('Grilla', 'Lista_desde_grilla')
  AND o.es_opcion_fija = 0
  AND o.eliminado = 0
  AND NOT EXISTS (
    SELECT 1 FROM tbl_cuestionario_respuestas r WHERE r.tbl_opcion_respuesta_id = o.id
  );

INSERT INTO tbl_migraciones_ejecutadas (nombre_migracion, dtejecutada)
SELECT 'fase-l-002-opciones-ajenas-lista-grilla', NOW()
WHERE NOT EXISTS (
  SELECT 1 FROM tbl_migraciones_ejecutadas
  WHERE nombre_migracion = 'fase-l-002-opciones-ajenas-lista-grilla'
);
