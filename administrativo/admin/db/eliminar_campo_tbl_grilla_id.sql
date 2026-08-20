-- ============================================================================
-- LIMPIEZA: Eliminar campo obsoleto tbl_grilla_id
-- Fecha: 2025-10-27
-- Descripción: Elimina el campo tbl_grilla_id de tbl_preguntas_sub_preguntas_grilla
--              ya que ahora usamos la tabla intermedia tbl_grilla_x_preguntas
-- ============================================================================
-- IMPORTANTE: Ejecutar este script DESPUÉS de migracion_preguntas_multiples_grillas.sql
-- ============================================================================

USE estadisticas_db;

-- ============================================================================
-- VERIFICACIONES PREVIAS
-- ============================================================================

-- 1. Verificar que la tabla intermedia existe y tiene datos
SELECT
    'Verificación 1: Tabla intermedia' AS verificacion,
    COUNT(*) AS total_asociaciones
FROM tbl_grilla_x_preguntas;

-- 2. Verificar que todas las preguntas con tbl_grilla_id tienen sus asociaciones en la tabla intermedia
SELECT
    'Verificación 2: Preguntas con tbl_grilla_id que NO están en tabla intermedia' AS verificacion,
    COUNT(*) AS total_sin_migrar
FROM tbl_preguntas_sub_preguntas_grilla p
WHERE p.tbl_grilla_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM tbl_grilla_x_preguntas gxp
      WHERE gxp.tbl_pregunta_id = p.id
        AND gxp.tbl_grilla_id = p.tbl_grilla_id
  );
-- Resultado esperado: 0 (todas ya están migradas)

-- 3. Mostrar estructura actual de la tabla
DESCRIBE tbl_preguntas_sub_preguntas_grilla;

-- ============================================================================
-- PASO 1: Verificar y eliminar foreign keys relacionadas con tbl_grilla_id
-- ============================================================================

-- Consultar foreign keys existentes
SELECT
    CONSTRAINT_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = 'estadisticas_db'
  AND TABLE_NAME = 'tbl_preguntas_sub_preguntas_grilla'
  AND COLUMN_NAME = 'tbl_grilla_id'
  AND REFERENCED_TABLE_NAME IS NOT NULL;

-- Si existe alguna foreign key, eliminarla primero
-- Ejemplo (ajustar según el nombre real):
-- ALTER TABLE tbl_preguntas_sub_preguntas_grilla DROP FOREIGN KEY fk_pregunta_grilla;

-- ============================================================================
-- PASO 2: Eliminar índices relacionados con tbl_grilla_id
-- ============================================================================

-- Consultar índices existentes
SELECT
    INDEX_NAME
FROM INFORMATION_SCHEMA.STATISTICS
WHERE TABLE_SCHEMA = 'estadisticas_db'
  AND TABLE_NAME = 'tbl_preguntas_sub_preguntas_grilla'
  AND COLUMN_NAME = 'tbl_grilla_id'
  AND INDEX_NAME != 'PRIMARY';

-- Si existe algún índice, eliminarlos (ajustar según los nombres reales):
-- ALTER TABLE tbl_preguntas_sub_preguntas_grilla DROP INDEX idx_tbl_grilla_id;

-- ============================================================================
-- PASO 3: Eliminar el campo tbl_grilla_id
-- ============================================================================

-- BACKUP RECOMENDADO: Antes de ejecutar esto, hacer backup de la base de datos
-- mysqldump -u root -p estadisticas_db > backup_antes_eliminar_campo.sql

ALTER TABLE tbl_preguntas_sub_preguntas_grilla
DROP COLUMN tbl_grilla_id;

-- ============================================================================
-- VERIFICACIÓN POST-ELIMINACIÓN
-- ============================================================================

-- Verificar que el campo ya no existe
DESCRIBE tbl_preguntas_sub_preguntas_grilla;

-- Verificar que la tabla intermedia sigue funcionando correctamente
SELECT
    'Verificación final: Preguntas con grillas asociadas' AS verificacion,
    COUNT(DISTINCT p.id) AS total_preguntas,
    COUNT(gxp.id) AS total_asociaciones
FROM tbl_preguntas_sub_preguntas_grilla p
LEFT JOIN tbl_grilla_x_preguntas gxp ON p.id = gxp.tbl_pregunta_id;

-- Verificar vista funcional
SELECT * FROM vw_preguntas_con_grillas LIMIT 5;

-- ============================================================================
-- ROLLBACK (solo en caso de emergencia)
-- ============================================================================

/*
-- Si necesitas restaurar el campo (NO RECOMENDADO):

-- 1. Agregar el campo nuevamente
ALTER TABLE tbl_preguntas_sub_preguntas_grilla
ADD COLUMN tbl_grilla_id INT NULL AFTER texto_pregunta;

-- 2. Restaurar datos desde la tabla intermedia (solo la primera grilla de cada pregunta)
UPDATE tbl_preguntas_sub_preguntas_grilla p
INNER JOIN (
    SELECT tbl_pregunta_id, MIN(tbl_grilla_id) AS grilla_id
    FROM tbl_grilla_x_preguntas
    GROUP BY tbl_pregunta_id
) AS gxp ON p.id = gxp.tbl_pregunta_id
SET p.tbl_grilla_id = gxp.grilla_id;

-- 3. Recrear foreign key si era necesaria
-- ALTER TABLE tbl_preguntas_sub_preguntas_grilla
-- ADD CONSTRAINT fk_pregunta_grilla
-- FOREIGN KEY (tbl_grilla_id) REFERENCES tbl_grilla(id);

-- NOTA: Este rollback NO es recomendado porque volverías a la relación 1:1
-- en lugar de N:M, perdiendo la funcionalidad de múltiples grillas.
*/

-- ============================================================================
-- CONFIRMACIÓN
-- ============================================================================

SELECT '¡Campo tbl_grilla_id eliminado exitosamente!' AS resultado,
       'El sistema ahora usa exclusivamente tbl_grilla_x_preguntas para asociaciones N:M' AS nota;
