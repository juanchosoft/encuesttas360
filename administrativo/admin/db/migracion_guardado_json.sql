-- =====================================================
-- MIGRACIÓN: Estructura Flexible con JSON para Respuestas
-- =====================================================
-- Modifica las tablas para usar JSON y soportar N preguntas dinámicas
-- =====================================================

-- Paso 1: Modificar tabla de respuestas principales para usar JSON
-- =====================================================

ALTER TABLE tbl_grilla_candidato_respuestas
ADD COLUMN IF NOT EXISTS respuestas_json JSON NULL
    COMMENT 'Respuestas en formato JSON flexible: {"conoce":"si","imagen":"favorable","votaria":"si","nueva_pregunta":"valor"}';

-- Paso 2: Modificar tabla de subpreguntas para usar JSON
-- =====================================================

ALTER TABLE tbl_grilla_preguntas_adicionales
ADD COLUMN IF NOT EXISTS subpreguntas_json JSON NULL
    COMMENT 'Subpreguntas en formato JSON flexible: {"pa":6,"pb":8,"pc":6,"pd":10}';

-- Paso 3: Migrar datos existentes a JSON (si existen)
-- =====================================================

-- Migrar respuestas principales
UPDATE tbl_grilla_candidato_respuestas
SET respuestas_json = JSON_OBJECT(
    'conoce', conoce_candidato,
    'imagen', imagen_candidato,
    'votaria', votaria_por_candidato
)
WHERE respuestas_json IS NULL;

-- Migrar subpreguntas
UPDATE tbl_grilla_preguntas_adicionales
SET subpreguntas_json = JSON_OBJECT(
    'pa', pregunta_pa,
    'pb', pregunta_pb,
    'pc', pregunta_pc
)
WHERE subpreguntas_json IS NULL;

-- Paso 4: Crear índices para búsqueda en JSON (MySQL 5.7+ / MariaDB 10.2+)
-- =====================================================

-- Nota: Estos índices funcionan en MySQL 5.7+ con JSON
-- Comentar si la versión de MariaDB no soporta índices virtuales en JSON

-- ALTER TABLE tbl_grilla_candidato_respuestas
-- ADD INDEX idx_respuesta_conoce ((CAST(respuestas_json->'$.conoce' AS CHAR(20))));

-- Paso 5: Verificar datos migrados
-- =====================================================

-- Ver respuestas principales en JSON
SELECT
    id,
    tbl_grilla_id,
    tbl_participante_id,
    tbl_usuario_id,
    respuestas_json,
    -- Campos antiguos (mantener por compatibilidad temporal)
    conoce_candidato,
    imagen_candidato,
    votaria_por_candidato,
    dtcreate
FROM tbl_grilla_candidato_respuestas
LIMIT 10;

-- Ver subpreguntas en JSON
SELECT
    id,
    tbl_grilla_id,
    tbl_usuario_id,
    subpreguntas_json,
    -- Campos antiguos (mantener por compatibilidad temporal)
    pregunta_pa,
    pregunta_pb,
    pregunta_pc,
    dtcreate
FROM tbl_grilla_preguntas_adicionales
LIMIT 10;

-- Paso 6: Ejemplos de consultas con JSON
-- =====================================================

-- Buscar candidatos donde conoce = 'si'
SELECT *
FROM tbl_grilla_candidato_respuestas
WHERE JSON_UNQUOTE(JSON_EXTRACT(respuestas_json, '$.conoce')) = 'si';

-- Buscar candidatos aprobados (todas las respuestas positivas)
SELECT *
FROM tbl_grilla_candidato_respuestas
WHERE JSON_UNQUOTE(JSON_EXTRACT(respuestas_json, '$.conoce')) = 'si'
  AND JSON_UNQUOTE(JSON_EXTRACT(respuestas_json, '$.imagen')) = 'favorable'
  AND JSON_UNQUOTE(JSON_EXTRACT(respuestas_json, '$.votaria')) = 'si';

-- Extraer valores de subpreguntas
SELECT
    id,
    tbl_usuario_id,
    JSON_UNQUOTE(JSON_EXTRACT(subpreguntas_json, '$.pa')) AS respuesta_pa,
    JSON_UNQUOTE(JSON_EXTRACT(subpreguntas_json, '$.pb')) AS respuesta_pb,
    JSON_UNQUOTE(JSON_EXTRACT(subpreguntas_json, '$.pc')) AS respuesta_pc
FROM tbl_grilla_preguntas_adicionales;

-- =====================================================
-- NOTAS IMPORTANTES
-- =====================================================

/*
VENTAJAS DE USAR JSON:

1. FLEXIBILIDAD TOTAL:
   - Agregar nuevas preguntas sin modificar estructura de tabla
   - No requiere ALTER TABLE cada vez que se agrega una pregunta
   - Escalable a N preguntas

2. COMPATIBILIDAD:
   - Los campos antiguos (conoce_candidato, imagen_candidato, etc.) se mantienen
   - Migración gradual sin romper funcionalidad existente
   - Puede usarse JSON para nuevas preguntas y campos individuales para las 3 básicas

3. CONSULTAS:
   - JSON_EXTRACT para obtener valores
   - JSON_UNQUOTE para quitar comillas
   - WHERE con JSON_EXTRACT para filtrar

4. ESTRUCTURA EJEMPLO:

   respuestas_json:
   {
       "conoce": "si",
       "imagen": "favorable",
       "votaria": "si",
       "confia": "mucho",           // Nueva pregunta agregada
       "experiencia": "suficiente"   // Otra nueva pregunta
   }

   subpreguntas_json:
   {
       "pa": 6,
       "pb": 8,
       "pc": 6,
       "pd": 10,    // Nueva subpregunta agregada
       "pe": 6      // Otra nueva subpregunta
   }

5. RENDIMIENTO:
   - Para consultas frecuentes, mantener también campos individuales
   - Usar índices virtuales en columnas generadas (MySQL 5.7+)
   - JSON es más lento que columnas nativas, pero más flexible

RECOMENDACIÓN:
- Usar respuestas_json para preguntas dinámicas/futuras
- Mantener campos individuales para las 3 preguntas básicas (conoce, imagen, votaria)
- Esto da lo mejor de ambos mundos: rendimiento + flexibilidad
*/

-- =====================================================
-- ROLLBACK (si es necesario)
-- =====================================================

-- Para eliminar las columnas JSON agregadas:
-- ALTER TABLE tbl_grilla_candidato_respuestas DROP COLUMN respuestas_json;
-- ALTER TABLE tbl_grilla_preguntas_adicionales DROP COLUMN subpreguntas_json;
