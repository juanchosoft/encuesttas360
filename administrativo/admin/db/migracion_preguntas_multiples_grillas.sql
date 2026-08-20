-- ============================================================================
-- MIGRACIÓN: Preguntas Asociadas a Múltiples Grillas
-- Fecha: 2025-10-26
-- Descripción: Cambia la relación de preguntas con grillas de 1:1 a N:M
-- ============================================================================

USE estadisticas_db;

-- ============================================================================
-- PASO 1: Crear tabla intermedia para relación N:M
-- ============================================================================

CREATE TABLE IF NOT EXISTS tbl_grilla_x_preguntas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tbl_grilla_id INT NOT NULL,
    tbl_pregunta_id INT NOT NULL,
    dtcreate DATETIME DEFAULT CURRENT_TIMESTAMP,

    -- Índices
    INDEX idx_grilla_id (tbl_grilla_id),
    INDEX idx_pregunta_id (tbl_pregunta_id),

    -- Constraint único para evitar duplicados
    UNIQUE KEY unique_grilla_pregunta (tbl_grilla_id, tbl_pregunta_id),

    -- Foreign keys
    FOREIGN KEY (tbl_grilla_id) REFERENCES tbl_grilla(id) ON DELETE CASCADE,
    FOREIGN KEY (tbl_pregunta_id) REFERENCES tbl_preguntas_sub_preguntas_grilla(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- PASO 2: Migrar datos existentes a la tabla intermedia
-- ============================================================================

-- Insertar asociaciones existentes en la nueva tabla
INSERT INTO tbl_grilla_x_preguntas (tbl_grilla_id, tbl_pregunta_id, dtcreate)
SELECT
    tbl_grilla_id,
    id,
    dtcreate
FROM tbl_preguntas_sub_preguntas_grilla
WHERE tbl_grilla_id IS NOT NULL
ON DUPLICATE KEY UPDATE tbl_grilla_x_preguntas.dtcreate = VALUES(dtcreate); -- Evitar duplicados si se ejecuta varias veces

-- ============================================================================
-- PASO 3: NO eliminamos el campo tbl_grilla_id de la tabla original
-- ============================================================================
-- IMPORTANTE: Mantenemos el campo tbl_grilla_id por compatibilidad temporal
-- Esto permite que el código antiguo siga funcionando mientras se migra
-- En una versión futura se puede eliminar con:
-- ALTER TABLE tbl_preguntas_sub_preguntas_grilla DROP COLUMN tbl_grilla_id;

-- Por ahora, solo actualizamos a NULL las preguntas que ya están en la tabla intermedia
UPDATE tbl_preguntas_sub_preguntas_grilla
SET tbl_grilla_id = NULL
WHERE id IN (SELECT tbl_pregunta_id FROM tbl_grilla_x_preguntas);

-- ============================================================================
-- PASO 4: Crear vista para facilitar consultas
-- ============================================================================

CREATE OR REPLACE VIEW vw_preguntas_con_grillas AS
SELECT
    p.id,
    p.tipo_pregunta,
    p.texto_pregunta,
    p.codigo_pregunta,
    p.orden,
    p.pregunta_padre_id,
    p.opciones_respuesta,
    p.habilita_subpreguntas,
    p.condicion_habilitacion,
    p.habilitado,
    p.tbl_usuario_id,
    p.dtcreate,
    p.dtupdate,
    GROUP_CONCAT(gxp.tbl_grilla_id ORDER BY gxp.tbl_grilla_id SEPARATOR ',') AS grillas_ids,
    COUNT(gxp.tbl_grilla_id) AS total_grillas_asociadas
FROM tbl_preguntas_sub_preguntas_grilla p
LEFT JOIN tbl_grilla_x_preguntas gxp ON p.id = gxp.tbl_pregunta_id
GROUP BY p.id, p.tipo_pregunta, p.texto_pregunta, p.codigo_pregunta,
         p.orden, p.pregunta_padre_id, p.opciones_respuesta,
         p.habilita_subpreguntas, p.condicion_habilitacion,
         p.habilitado, p.tbl_usuario_id, p.dtcreate, p.dtupdate;

-- ============================================================================
-- CONSULTAS ÚTILES PARA VERIFICACIÓN
-- ============================================================================

-- Ver todas las preguntas con sus grillas asociadas
SELECT
    p.id,
    p.codigo_pregunta,
    p.texto_pregunta,
    p.tipo_pregunta,
    g.id AS grilla_id,
    g.grilla AS nombre_grilla
FROM tbl_preguntas_sub_preguntas_grilla p
INNER JOIN tbl_grilla_x_preguntas gxp ON p.id = gxp.tbl_pregunta_id
INNER JOIN tbl_grilla g ON gxp.tbl_grilla_id = g.id
ORDER BY p.id, g.id;

-- Ver preguntas sin grillas asociadas (globales)
SELECT
    id,
    codigo_pregunta,
    texto_pregunta,
    tipo_pregunta
FROM tbl_preguntas_sub_preguntas_grilla p
WHERE NOT EXISTS (
    SELECT 1 FROM tbl_grilla_x_preguntas gxp WHERE gxp.tbl_pregunta_id = p.id
);

-- Ver cuántas grillas tiene cada pregunta
SELECT
    p.codigo_pregunta,
    p.texto_pregunta,
    COUNT(gxp.tbl_grilla_id) AS total_grillas
FROM tbl_preguntas_sub_preguntas_grilla p
LEFT JOIN tbl_grilla_x_preguntas gxp ON p.id = gxp.tbl_pregunta_id
GROUP BY p.id, p.codigo_pregunta, p.texto_pregunta;

-- ============================================================================
-- ROLLBACK (en caso de problemas)
-- ============================================================================

/*
-- Para revertir los cambios:

-- 1. Restaurar tbl_grilla_id desde la tabla intermedia
UPDATE tbl_preguntas_sub_preguntas_grilla p
INNER JOIN (
    SELECT tbl_pregunta_id, MIN(tbl_grilla_id) AS grilla_id
    FROM tbl_grilla_x_preguntas
    GROUP BY tbl_pregunta_id
) AS gxp ON p.id = gxp.tbl_pregunta_id
SET p.tbl_grilla_id = gxp.grilla_id;

-- 2. Eliminar tabla intermedia
DROP TABLE IF EXISTS tbl_grilla_x_preguntas;

-- 3. Eliminar vista
DROP VIEW IF EXISTS vw_preguntas_con_grillas;
*/
