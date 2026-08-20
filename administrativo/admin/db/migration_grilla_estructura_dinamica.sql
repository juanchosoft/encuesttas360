-- ================================================================================
-- MIGRACIÓN: Estructura Dinámica para Sistema de Votaciones de Grilla
-- ================================================================================
-- Fecha: 2025-10-25
-- Descripción: Migra de estructura híbrida (campos fijos + JSON) a estructura
--              100% normalizada que soporta preguntas y subpreguntas dinámicas
-- ================================================================================

-- PASO 1: Crear nuevas tablas con estructura normalizada
-- ================================================================================

-- Tabla 1: Sesión de votación (agrupa todas las respuestas de un usuario en una grilla)
CREATE TABLE IF NOT EXISTS `tbl_grilla_sesion_votacion` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `tbl_grilla_id` INT(11) NOT NULL COMMENT 'ID de la grilla',
  `tbl_usuario_id` INT(11) NOT NULL COMMENT 'Usuario que vota',
  `dtcreate` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dtupdate` DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_sesion_diaria` (`tbl_grilla_id`, `tbl_usuario_id`, DATE(`dtcreate`)),
  INDEX `idx_grilla` (`tbl_grilla_id`),
  INDEX `idx_usuario` (`tbl_usuario_id`),
  INDEX `idx_fecha` (`dtcreate`),
  CONSTRAINT `fk_sesion_grilla` FOREIGN KEY (`tbl_grilla_id`)
    REFERENCES `tbl_grilla` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Sesión de votación: registra que un usuario votó en una grilla';

-- Tabla 2: Respuestas individuales (una fila por cada respuesta a cada pregunta)
CREATE TABLE IF NOT EXISTS `tbl_grilla_respuestas` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `tbl_sesion_votacion_id` INT(11) NOT NULL COMMENT 'Sesión a la que pertenece',
  `tbl_pregunta_id` INT(11) NOT NULL COMMENT 'ID de pregunta en tbl_preguntas_sub_preguntas_grilla',
  `tbl_participante_id` INT(11) NULL COMMENT 'Candidato evaluado (NULL para subpreguntas)',
  `respuesta` VARCHAR(100) NOT NULL COMMENT 'Valor: si/no/favorable/desfavorable/no_aplica o ID de candidato',
  `dtcreate` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_respuesta` (`tbl_sesion_votacion_id`, `tbl_pregunta_id`, `tbl_participante_id`),
  INDEX `idx_sesion` (`tbl_sesion_votacion_id`),
  INDEX `idx_pregunta` (`tbl_pregunta_id`),
  INDEX `idx_participante` (`tbl_participante_id`),
  INDEX `idx_respuesta` (`respuesta`),
  CONSTRAINT `fk_respuesta_sesion` FOREIGN KEY (`tbl_sesion_votacion_id`)
    REFERENCES `tbl_grilla_sesion_votacion` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_respuesta_pregunta` FOREIGN KEY (`tbl_pregunta_id`)
    REFERENCES `tbl_preguntas_sub_preguntas_grilla` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Respuestas individuales: una fila por cada respuesta a una pregunta';

-- ================================================================================
-- PASO 2: Migrar datos existentes (si los hay)
-- ================================================================================

-- NOTA: Este paso requiere que existan preguntas configuradas en tbl_preguntas_sub_preguntas_grilla
-- Si no existen, primero hay que crearlas

-- Primero, verificar si existen preguntas configuradas para las grillas
SELECT
    COUNT(*) AS total_preguntas,
    tbl_grilla_id
FROM tbl_preguntas_sub_preguntas_grilla
WHERE tipo_pregunta = 'pregunta'
GROUP BY tbl_grilla_id;

-- Si no hay preguntas configuradas, crear las 3 preguntas básicas para cada grilla
INSERT INTO tbl_preguntas_sub_preguntas_grilla
(tipo_pregunta, texto_pregunta, codigo_pregunta, orden, pregunta_padre_id, opciones_respuesta,
 requiere_todas_si, habilita_subpreguntas, condicion_habilitacion, habilitado, tbl_usuario_id, tbl_grilla_id)
SELECT
    'pregunta',
    'Conoce Sí / NO',
    'conoce',
    1,
    NULL,
    '["si","no"]',
    0,
    1,
    'si',
    1,
    1, -- Usuario admin
    g.id
FROM tbl_grilla g
WHERE NOT EXISTS (
    SELECT 1 FROM tbl_preguntas_sub_preguntas_grilla
    WHERE tbl_grilla_id = g.id AND codigo_pregunta = 'conoce'
);

INSERT INTO tbl_preguntas_sub_preguntas_grilla
(tipo_pregunta, texto_pregunta, codigo_pregunta, orden, pregunta_padre_id, opciones_respuesta,
 requiere_todas_si, habilita_subpreguntas, condicion_habilitacion, habilitado, tbl_usuario_id, tbl_grilla_id)
SELECT
    'pregunta',
    'Imagen Favorable o Desfavorable',
    'imagen',
    2,
    NULL,
    '["favorable","desfavorable","no_aplica"]',
    0,
    1,
    'favorable',
    1,
    1,
    g.id
FROM tbl_grilla g
WHERE NOT EXISTS (
    SELECT 1 FROM tbl_preguntas_sub_preguntas_grilla
    WHERE tbl_grilla_id = g.id AND codigo_pregunta = 'imagen'
);

INSERT INTO tbl_preguntas_sub_preguntas_grilla
(tipo_pregunta, texto_pregunta, codigo_pregunta, orden, pregunta_padre_id, opciones_respuesta,
 requiere_todas_si, habilita_subpreguntas, condicion_habilitacion, habilitado, tbl_usuario_id, tbl_grilla_id)
SELECT
    'pregunta',
    'Votaría por el o por ella',
    'votaria',
    3,
    NULL,
    '["si","no","no_aplica"]',
    0,
    0,
    NULL,
    1,
    1,
    g.id
FROM tbl_grilla g
WHERE NOT EXISTS (
    SELECT 1 FROM tbl_preguntas_sub_preguntas_grilla
    WHERE tbl_grilla_id = g.id AND codigo_pregunta = 'votaria'
);

-- Ahora migrar datos de tbl_grilla_candidato_respuestas a la nueva estructura
-- Solo migrar datos que no tengan respuestas_json (datos antiguos)

-- Primero crear sesiones de votación
INSERT INTO tbl_grilla_sesion_votacion (tbl_grilla_id, tbl_usuario_id, dtcreate)
SELECT DISTINCT
    tbl_grilla_id,
    tbl_usuario_id,
    MIN(dtcreate) as dtcreate
FROM tbl_grilla_candidato_respuestas
GROUP BY tbl_grilla_id, tbl_usuario_id, DATE(dtcreate)
ON DUPLICATE KEY UPDATE dtupdate = NOW();

-- Luego migrar respuestas de "conoce_candidato"
INSERT INTO tbl_grilla_respuestas (tbl_sesion_votacion_id, tbl_pregunta_id, tbl_participante_id, respuesta, dtcreate)
SELECT
    sv.id,
    (SELECT id FROM tbl_preguntas_sub_preguntas_grilla
     WHERE codigo_pregunta = 'conoce' AND tbl_grilla_id = gcr.tbl_grilla_id LIMIT 1),
    gcr.tbl_participante_id,
    CASE
        WHEN gcr.conoce_candidato = '' THEN 'no_aplica'
        ELSE gcr.conoce_candidato
    END,
    gcr.dtcreate
FROM tbl_grilla_candidato_respuestas gcr
INNER JOIN tbl_grilla_sesion_votacion sv
    ON sv.tbl_grilla_id = gcr.tbl_grilla_id
    AND sv.tbl_usuario_id = gcr.tbl_usuario_id
    AND DATE(sv.dtcreate) = DATE(gcr.dtcreate)
WHERE gcr.conoce_candidato IS NOT NULL
  AND gcr.conoce_candidato != ''
ON DUPLICATE KEY UPDATE respuesta = VALUES(respuesta);

-- Migrar respuestas de "imagen_candidato"
INSERT INTO tbl_grilla_respuestas (tbl_sesion_votacion_id, tbl_pregunta_id, tbl_participante_id, respuesta, dtcreate)
SELECT
    sv.id,
    (SELECT id FROM tbl_preguntas_sub_preguntas_grilla
     WHERE codigo_pregunta = 'imagen' AND tbl_grilla_id = gcr.tbl_grilla_id LIMIT 1),
    gcr.tbl_participante_id,
    CASE
        WHEN gcr.imagen_candidato = '' THEN 'no_aplica'
        ELSE gcr.imagen_candidato
    END,
    gcr.dtcreate
FROM tbl_grilla_candidato_respuestas gcr
INNER JOIN tbl_grilla_sesion_votacion sv
    ON sv.tbl_grilla_id = gcr.tbl_grilla_id
    AND sv.tbl_usuario_id = gcr.tbl_usuario_id
    AND DATE(sv.dtcreate) = DATE(gcr.dtcreate)
WHERE gcr.imagen_candidato IS NOT NULL
  AND gcr.imagen_candidato != ''
ON DUPLICATE KEY UPDATE respuesta = VALUES(respuesta);

-- Migrar respuestas de "votaria_por_candidato"
INSERT INTO tbl_grilla_respuestas (tbl_sesion_votacion_id, tbl_pregunta_id, tbl_participante_id, respuesta, dtcreate)
SELECT
    sv.id,
    (SELECT id FROM tbl_preguntas_sub_preguntas_grilla
     WHERE codigo_pregunta = 'votaria' AND tbl_grilla_id = gcr.tbl_grilla_id LIMIT 1),
    gcr.tbl_participante_id,
    CASE
        WHEN gcr.votaria_por_candidato = '' THEN 'no_aplica'
        ELSE gcr.votaria_por_candidato
    END,
    gcr.dtcreate
FROM tbl_grilla_candidato_respuestas gcr
INNER JOIN tbl_grilla_sesion_votacion sv
    ON sv.tbl_grilla_id = gcr.tbl_grilla_id
    AND sv.tbl_usuario_id = gcr.tbl_usuario_id
    AND DATE(sv.dtcreate) = DATE(gcr.dtcreate)
WHERE gcr.votaria_por_candidato IS NOT NULL
  AND gcr.votaria_por_candidato != ''
ON DUPLICATE KEY UPDATE respuesta = VALUES(respuesta);

-- ================================================================================
-- PASO 3: Renombrar tabla antigua (no eliminar por seguridad)
-- ================================================================================

RENAME TABLE `tbl_grilla_candidato_respuestas` TO `tbl_grilla_candidato_respuestas_OLD_BACKUP`;

-- ================================================================================
-- PASO 4: Crear vistas para compatibilidad con código antiguo (opcional)
-- ================================================================================

-- Vista que simula la estructura antigua para consultas de solo lectura
CREATE OR REPLACE VIEW vw_grilla_candidato_respuestas_legacy AS
SELECT
    gr_conoce.id,
    sv.tbl_grilla_id,
    gr_conoce.tbl_participante_id,
    sv.tbl_usuario_id,
    COALESCE(gr_conoce.respuesta, '') AS conoce_candidato,
    COALESCE(gr_imagen.respuesta, '') AS imagen_candidato,
    COALESCE(gr_votaria.respuesta, '') AS votaria_por_candidato,
    sv.dtcreate
FROM tbl_grilla_sesion_votacion sv
LEFT JOIN tbl_grilla_respuestas gr_conoce
    ON gr_conoce.tbl_sesion_votacion_id = sv.id
    AND gr_conoce.tbl_pregunta_id = (
        SELECT id FROM tbl_preguntas_sub_preguntas_grilla
        WHERE codigo_pregunta = 'conoce' AND tbl_grilla_id = sv.tbl_grilla_id LIMIT 1
    )
LEFT JOIN tbl_grilla_respuestas gr_imagen
    ON gr_imagen.tbl_sesion_votacion_id = sv.id
    AND gr_imagen.tbl_participante_id = gr_conoce.tbl_participante_id
    AND gr_imagen.tbl_pregunta_id = (
        SELECT id FROM tbl_preguntas_sub_preguntas_grilla
        WHERE codigo_pregunta = 'imagen' AND tbl_grilla_id = sv.tbl_grilla_id LIMIT 1
    )
LEFT JOIN tbl_grilla_respuestas gr_votaria
    ON gr_votaria.tbl_sesion_votacion_id = sv.id
    AND gr_votaria.tbl_participante_id = gr_conoce.tbl_participante_id
    AND gr_votaria.tbl_pregunta_id = (
        SELECT id FROM tbl_preguntas_sub_preguntas_grilla
        WHERE codigo_pregunta = 'votaria' AND tbl_grilla_id = sv.tbl_grilla_id LIMIT 1
    );

-- ================================================================================
-- PASO 5: Verificar migración
-- ================================================================================

-- Contar registros en tabla antigua
SELECT
    'Registros en tabla antigua' AS descripcion,
    COUNT(*) AS total
FROM tbl_grilla_candidato_respuestas_OLD_BACKUP;

-- Contar sesiones creadas
SELECT
    'Sesiones de votación creadas' AS descripcion,
    COUNT(*) AS total
FROM tbl_grilla_sesion_votacion;

-- Contar respuestas migradas
SELECT
    'Respuestas migradas' AS descripcion,
    COUNT(*) AS total
FROM tbl_grilla_respuestas;

-- Ver distribución de respuestas por pregunta
SELECT
    p.codigo_pregunta,
    p.texto_pregunta,
    COUNT(*) AS total_respuestas
FROM tbl_grilla_respuestas gr
INNER JOIN tbl_preguntas_sub_preguntas_grilla p ON gr.tbl_pregunta_id = p.id
GROUP BY p.codigo_pregunta, p.texto_pregunta
ORDER BY p.orden;

-- ================================================================================
-- FIN DE LA MIGRACIÓN
-- ================================================================================
-- NOTAS:
-- 1. La tabla antigua se renombró a tbl_grilla_candidato_respuestas_OLD_BACKUP
-- 2. Se puede eliminar después de verificar que todo funciona correctamente
-- 3. La vista vw_grilla_candidato_respuestas_legacy simula la estructura antigua
-- 4. Actualizar el código PHP para usar las nuevas tablas
-- ================================================================================
