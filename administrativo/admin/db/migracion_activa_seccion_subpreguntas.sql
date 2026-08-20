-- =====================================================================================
-- MIGRACIÓN: Agregar campo activa_seccion_subpreguntas
-- Versión: 2.5.0
-- Fecha: 2025-10-30
-- =====================================================================================
--
-- PROPÓSITO:
-- Agregar un nuevo campo para marcar qué pregunta activa la sección de subpreguntas.
-- Esto permite que solo una pregunta específica (ej: P3 "¿Votaría por él?") active
-- las subpreguntas (PA, PB, PC...), en lugar de requerir TODAS las respuestas positivas.
--
-- =====================================================================================

USE estadisticas_db;

-- =====================================================================================
-- PASO 1: Agregar campo activa_seccion_subpreguntas
-- =====================================================================================

ALTER TABLE tbl_preguntas_sub_preguntas_grilla
ADD COLUMN activa_seccion_subpreguntas TINYINT(1) DEFAULT 0
COMMENT 'Indica si esta pregunta activa la sección de subpreguntas (PA, PB, PC...)'
AFTER condicion_habilitacion;

-- =====================================================================================
-- PASO 2: Configurar P3 (¿Votaría por él?) para activar subpreguntas
-- =====================================================================================

UPDATE tbl_preguntas_sub_preguntas_grilla
SET activa_seccion_subpreguntas = 1
WHERE codigo_pregunta = 'votaria'
  AND tipo_pregunta = 'pregunta';

-- =====================================================================================
-- PASO 3: Verificar configuración
-- =====================================================================================

SELECT
    id,
    codigo_pregunta,
    orden,
    texto_pregunta,
    habilita_subpreguntas AS 'Habilita Siguientes',
    activa_seccion_subpreguntas AS 'Activa Subpreguntas'
FROM tbl_preguntas_sub_preguntas_grilla
WHERE tipo_pregunta = 'pregunta'
ORDER BY orden;

-- =====================================================================================
-- RESULTADO ESPERADO:
-- =====================================================================================
-- | orden | codigo_pregunta | texto_pregunta                     | Habilita Siguientes | Activa Subpreguntas |
-- |-------|-----------------|-------------------------------------|---------------------|---------------------|
-- | 1     | conoce          | ¿Conoce al candidato?              | 1                   | 0                   |
-- | 2     | imagen          | Qué Imagen Tiene de él(ella):      | 0                   | 0                   |
-- | 3     | votaria         | ¿Votaría por él o por ella?        | 0                   | 1                   |
-- =====================================================================================
--
-- INTERPRETACIÓN:
-- - P1 (conoce): Habilita las siguientes preguntas (P2 y P3)
-- - P2 (imagen): No afecta nada, solo se responde
-- - P3 (votaria): Activa la sección de subpreguntas si respuesta = SÍ
--
-- =====================================================================================
