-- ============================================
-- SCRIPT: Crear tabla de respuestas de grilla
-- Base de datos: estadisticas_db
-- ============================================

USE estadisticas_db;

-- Tabla principal de respuestas de votaciones
CREATE TABLE IF NOT EXISTS tbl_grilla_candidato_respuestas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tbl_grilla_id INT NOT NULL COMMENT 'ID de la grilla',
    tbl_participante_id INT NOT NULL COMMENT 'ID del participante/candidato',
    tbl_usuario_id INT NOT NULL COMMENT 'ID del usuario que responde',

    -- Preguntas principales
    conoce_candidato VARCHAR(20) NOT NULL COMMENT 'si/no',
    imagen_candidato VARCHAR(20) NOT NULL COMMENT 'favorable/desfavorable/no_aplica',
    votaria_por_candidato VARCHAR(20) NOT NULL COMMENT 'si/no/no_aplica',

    -- Metadata
    dtcreate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    dtupdate DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,

    -- Índices
    INDEX idx_grilla (tbl_grilla_id),
    INDEX idx_participante (tbl_participante_id),
    INDEX idx_usuario (tbl_usuario_id),
    INDEX idx_fecha (dtcreate),

    -- Clave única
    UNIQUE KEY unique_respuesta (tbl_grilla_id, tbl_participante_id, tbl_usuario_id)

) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Respuestas del estudio de votaciones (preguntas 1-3)';

-- Tabla de preguntas adicionales (P(A), P(B), P(C))
CREATE TABLE IF NOT EXISTS tbl_grilla_preguntas_adicionales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tbl_grilla_id INT NOT NULL COMMENT 'ID de la grilla',
    tbl_usuario_id INT NOT NULL COMMENT 'ID del usuario que responde',

    -- Preguntas adicionales - cada una almacena el ID del candidato seleccionado
    pregunta_pa INT NULL COMMENT 'Si las elecciones fueran hoy, ¿por quién votaría? P(A)',
    pregunta_pb INT NULL COMMENT 'Si su candidato P(A) se retira, ¿por quién votaría? P(B)',
    pregunta_pc INT NULL COMMENT 'Si su candidato P(B) se retira, ¿por quién votaría? P(C)',

    -- Metadata
    dtcreate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    dtupdate DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,

    -- Índices
    INDEX idx_grilla (tbl_grilla_id),
    INDEX idx_usuario (tbl_usuario_id),
    INDEX idx_fecha (dtcreate),

    -- Un usuario solo puede responder una vez
    UNIQUE KEY unique_respuesta_adicional (tbl_grilla_id, tbl_usuario_id)

) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Preguntas adicionales del estudio de votaciones (P(A), P(B), P(C))';

-- ============================================
-- VISTAS ÚTILES PARA REPORTES
-- ============================================

-- Vista: Resumen de respuestas por candidato
CREATE OR REPLACE VIEW vw_grilla_resumen_candidatos AS
SELECT
    gcr.tbl_grilla_id,
    gcr.tbl_participante_id,
    p.nombre_completo AS candidato,
    p.foto,
    pp.nombre_partido AS partido,

    -- Contadores de respuestas
    COUNT(DISTINCT gcr.tbl_usuario_id) AS total_respuestas,
    SUM(CASE WHEN gcr.conoce_candidato = 'si' THEN 1 ELSE 0 END) AS total_conocen,
    SUM(CASE WHEN gcr.conoce_candidato = 'no' THEN 1 ELSE 0 END) AS total_no_conocen,
    SUM(CASE WHEN gcr.imagen_candidato = 'favorable' THEN 1 ELSE 0 END) AS total_imagen_favorable,
    SUM(CASE WHEN gcr.imagen_candidato = 'desfavorable' THEN 1 ELSE 0 END) AS total_imagen_desfavorable,
    SUM(CASE WHEN gcr.votaria_por_candidato = 'si' THEN 1 ELSE 0 END) AS total_votarian,
    SUM(CASE WHEN gcr.votaria_por_candidato = 'no' THEN 1 ELSE 0 END) AS total_no_votarian,

    -- Porcentajes
    ROUND((SUM(CASE WHEN gcr.conoce_candidato = 'si' THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) AS porcentaje_conocen,
    ROUND((SUM(CASE WHEN gcr.imagen_candidato = 'favorable' THEN 1 ELSE 0 END) /
           NULLIF(SUM(CASE WHEN gcr.conoce_candidato = 'si' THEN 1 ELSE 0 END), 0)) * 100, 2) AS porcentaje_imagen_favorable,
    ROUND((SUM(CASE WHEN gcr.votaria_por_candidato = 'si' THEN 1 ELSE 0 END) /
           NULLIF(SUM(CASE WHEN gcr.imagen_candidato = 'favorable' THEN 1 ELSE 0 END), 0)) * 100, 2) AS porcentaje_votarian

FROM tbl_grilla_candidato_respuestas gcr
INNER JOIN tbl_participantes p ON gcr.tbl_participante_id = p.id
LEFT JOIN tbl_participantes_x_partidos_politicos pxp ON p.id = pxp.tbl_participante_id
LEFT JOIN tbl_partidos_politicos pp ON pxp.tbl_partido_politico_id = pp.id
GROUP BY
    gcr.tbl_grilla_id,
    gcr.tbl_participante_id,
    p.nombre_completo,
    p.foto,
    pp.nombre_partido
ORDER BY total_votarian DESC;

-- Vista: Resultados de preguntas adicionales
CREATE OR REPLACE VIEW vw_grilla_preguntas_adicionales_resumen AS
SELECT
    gpa.tbl_grilla_id,
    g.grilla,

    -- P(A): Si las elecciones fueran hoy
    COALESCE(pa.id, 'N/A') AS pa_candidato_id,
    COALESCE(pa.nombre_completo, 'Voto Nulo/Blanco') AS pa_candidato,
    COUNT(DISTINCT CASE WHEN gpa.pregunta_pa = pa.id OR gpa.pregunta_pa IS NULL THEN gpa.tbl_usuario_id END) AS pa_total_votos,

    -- P(B): Si P(A) se retira
    COALESCE(pb.id, 'N/A') AS pb_candidato_id,
    COALESCE(pb.nombre_completo, 'Voto Nulo/Blanco') AS pb_candidato,
    COUNT(DISTINCT CASE WHEN gpa.pregunta_pb = pb.id OR gpa.pregunta_pb IS NULL THEN gpa.tbl_usuario_id END) AS pb_total_votos,

    -- P(C): Si P(B) se retira
    COALESCE(pc.id, 'N/A') AS pc_candidato_id,
    COALESCE(pc.nombre_completo, 'Voto Nulo/Blanco') AS pc_candidato,
    COUNT(DISTINCT CASE WHEN gpa.pregunta_pc = pc.id OR gpa.pregunta_pc IS NULL THEN gpa.tbl_usuario_id END) AS pc_total_votos

FROM tbl_grilla_preguntas_adicionales gpa
INNER JOIN tbl_grilla g ON gpa.tbl_grilla_id = g.id
-- Utilizamos LEFT JOIN para incluir los casos donde el voto es NULL (ej. No sabe/No responde)
LEFT JOIN tbl_participantes pa ON gpa.pregunta_pa = pa.id
LEFT JOIN tbl_participantes pb ON gpa.pregunta_pb = pb.id
LEFT JOIN tbl_participantes pc ON gpa.pregunta_pc = pc.id
GROUP BY
    gpa.tbl_grilla_id,
    g.grilla,
    pa.id,
    pa.nombre_completo,
    pb.id,
    pb.nombre_completo,
    pc.id,
    pc.nombre_completo;

-- ============================================
-- FIN DEL SCRIPT
-- ============================================
