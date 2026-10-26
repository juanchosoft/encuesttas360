-- Numera de 1..n las preguntas de cada ficha (capítulo, id) y las opciones de cada pregunta. Se aplica una sola vez.

UPDATE tbl_preguntas p
INNER JOIN (
  SELECT a.id, 1 + COUNT(b.id) AS nuevo
  FROM tbl_preguntas a
  LEFT JOIN tbl_preguntas b ON b.tbl_ficha_tecnica_encuesta_id = a.tbl_ficha_tecnica_encuesta_id
    AND COALESCE(b.eliminado, 0) = 0
    AND (b.capitulo < a.capitulo OR (b.capitulo = a.capitulo AND b.id < a.id))
  WHERE COALESCE(a.eliminado, 0) = 0
  GROUP BY a.id
) x ON x.id = p.id
SET p.orden = x.nuevo
WHERE p.orden <> x.nuevo AND NOT EXISTS (SELECT 1 FROM tbl_migraciones_ejecutadas WHERE nombre_migracion = 'fase-i-004-orden-canonico');

UPDATE tbl_opciones_respuesta o
INNER JOIN (
  SELECT a.id, 1 + COUNT(b.id) AS nuevo
  FROM tbl_opciones_respuesta a
  LEFT JOIN tbl_opciones_respuesta b ON b.tbl_pregunta_id = a.tbl_pregunta_id
    AND (COALESCE(b.orden, 999999) < COALESCE(a.orden, 999999) OR (COALESCE(b.orden, 999999) = COALESCE(a.orden, 999999) AND b.id < a.id))
  GROUP BY a.id
) x ON x.id = o.id
SET o.orden = x.nuevo
WHERE (o.orden IS NULL OR o.orden <> x.nuevo) AND NOT EXISTS (SELECT 1 FROM tbl_migraciones_ejecutadas WHERE nombre_migracion = 'fase-i-004-orden-canonico');

INSERT INTO tbl_migraciones_ejecutadas (nombre_migracion, dtejecutada)
SELECT 'fase-i-004-orden-canonico', NOW()
WHERE NOT EXISTS (
  SELECT 1 FROM tbl_migraciones_ejecutadas WHERE nombre_migracion = 'fase-i-004-orden-canonico'
);
