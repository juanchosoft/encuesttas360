-- =====================================================================
-- Script para eliminar duplicados y prevenir futuros duplicados
-- en las respuestas de cuestionarios
-- =====================================================================

USE estadisticas_db;

-- 1. Identificar y eliminar registros duplicados
-- Mantener solo la respuesta más reciente de cada votante por ficha técnica

-- Crear tabla temporal con los IDs a mantener (los más recientes)
CREATE TEMPORARY TABLE IF NOT EXISTS temp_intentos_a_mantener AS
SELECT MAX(i.id) as id_mantener
FROM tbl_cuestionario_intentos i
WHERE i.tbl_votante_id IS NOT NULL
GROUP BY i.tbl_ficha_tecnica_encuesta_id, i.tbl_votante_id;

-- Mostrar los registros que se van a eliminar (para revisión)
SELECT
    i.id,
    i.tbl_ficha_tecnica_encuesta_id,
    i.tbl_votante_id,
    v.nombre_completo,
    i.fecha_respuesta
FROM tbl_cuestionario_intentos i
LEFT JOIN tbl_votantes v ON i.tbl_votante_id = v.id
WHERE i.tbl_votante_id IS NOT NULL
AND i.id NOT IN (SELECT id_mantener FROM temp_intentos_a_mantener)
ORDER BY i.tbl_ficha_tecnica_encuesta_id, i.tbl_votante_id, i.fecha_respuesta;

-- Eliminar las respuestas asociadas a los intentos duplicados
DELETE r FROM tbl_cuestionario_respuestas r
WHERE r.tbl_intento_id IN (
    SELECT i.id
    FROM tbl_cuestionario_intentos i
    WHERE i.tbl_votante_id IS NOT NULL
    AND i.id NOT IN (SELECT id_mantener FROM temp_intentos_a_mantener)
);

-- Eliminar los intentos duplicados
DELETE FROM tbl_cuestionario_intentos
WHERE tbl_votante_id IS NOT NULL
AND id NOT IN (SELECT id_mantener FROM temp_intentos_a_mantener);

-- Limpiar tabla temporal
DROP TEMPORARY TABLE IF EXISTS temp_intentos_a_mantener;

-- 2. Agregar índice único para prevenir futuros duplicados
-- Esto asegura que un votante solo pueda responder una vez cada cuestionario

-- Primero eliminar el índice si existe
ALTER TABLE tbl_cuestionario_intentos
DROP INDEX IF EXISTS idx_unique_votante_ficha;

-- Crear índice único
ALTER TABLE tbl_cuestionario_intentos
ADD UNIQUE INDEX idx_unique_votante_ficha (tbl_ficha_tecnica_encuesta_id, tbl_votante_id);

-- 3. Verificar resultado
SELECT
    'Total de intentos después de limpieza' as descripcion,
    COUNT(*) as total
FROM tbl_cuestionario_intentos
WHERE tbl_votante_id IS NOT NULL

UNION ALL

SELECT
    'Votantes únicos por ficha técnica' as descripcion,
    COUNT(DISTINCT CONCAT(tbl_ficha_tecnica_encuesta_id, '-', tbl_votante_id)) as total
FROM tbl_cuestionario_intentos
WHERE tbl_votante_id IS NOT NULL;

-- =====================================================================
-- Resultado esperado:
-- - Eliminados todos los duplicados
-- - Solo queda el intento más reciente de cada votante
-- - Constraint único previene futuros duplicados
-- =====================================================================
