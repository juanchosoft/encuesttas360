-- =====================================================
-- MIGRACIÓN: Sistema de Preguntas Dinámicas para Grilla
-- =====================================================
USE estadisticas_db;

-- =====================================================
-- Paso 1: Gestión de la Tabla Existente (Backup y Recreación)
-- =====================================================

-- 1.1 Crear tabla de respaldo (solo si la tabla original existe)
-- Se recomienda ejecutar esto por separado si se manejan grandes volúmenes de datos
CREATE TABLE IF NOT EXISTS tbl_preguntas_sub_preguntas_grilla_backup AS
SELECT * FROM tbl_preguntas_sub_preguntas_grilla;

-- 1.2 Eliminar la tabla antigua para asegurar que la nueva estructura se aplique
DROP TABLE IF EXISTS tbl_preguntas_sub_preguntas_grilla;

-- 1.3 Crear tabla mejorada con nuevas columnas
CREATE TABLE tbl_preguntas_sub_preguntas_grilla (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'Identificador único de la pregunta',

    tipo_pregunta ENUM('pregunta', 'subpregunta') NOT NULL
        COMMENT 'Tipo: pregunta principal o subpregunta',

    texto_pregunta TEXT NOT NULL
        COMMENT 'Contenido de la pregunta que se muestra al usuario',

    codigo_pregunta VARCHAR(50) NOT NULL
        COMMENT 'Código único para identificar la pregunta en el código (ej: conoce, imagen, votaria, pa, pb, pc)',

    orden INT NOT NULL DEFAULT 1
        COMMENT 'Orden en el que aparece la pregunta (se ordena por este campo)',

    pregunta_padre_id INT NULL
        COMMENT 'ID de la pregunta principal (solo para subpreguntas). NULL para preguntas principales',

    opciones_respuesta JSON NULL
        COMMENT 'Array JSON con las opciones de respuesta. Ej: ["si", "no"], ["favorable", "desfavorable"]',

    requiere_todas_si BOOLEAN DEFAULT FALSE
        COMMENT 'Si TRUE, el candidato debe tener todas las respuestas en SÍ para ser aprobado',

    habilita_subpreguntas BOOLEAN DEFAULT FALSE
        COMMENT 'Si TRUE, al responder esta pregunta se habilitan las subpreguntas asociadas',

    condicion_habilitacion VARCHAR(50) NULL
        COMMENT 'Condición para habilitar subpreguntas. Ej: si=todas_si, imagen=favorable',

    habilitado BOOLEAN DEFAULT TRUE
        COMMENT 'Si FALSE, la pregunta no se muestra en la interfaz',

    tbl_usuario_id INT NOT NULL
        COMMENT 'Identificador del usuario que creó/modificó la pregunta',

    dtcreate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        COMMENT 'Fecha y hora de creación del registro',

    dtupdate DATETIME NULL ON UPDATE CURRENT_TIMESTAMP
        COMMENT 'Fecha y hora de última actualización',

    INDEX idx_tipo_pregunta (tipo_pregunta),
    INDEX idx_orden (orden),
    INDEX idx_codigo_pregunta (codigo_pregunta),
    INDEX idx_pregunta_padre (pregunta_padre_id),
    INDEX idx_tbl_usuario_id (tbl_usuario_id),
    UNIQUE KEY unique_codigo_pregunta (codigo_pregunta),

    FOREIGN KEY (pregunta_padre_id)
        REFERENCES tbl_preguntas_sub_preguntas_grilla(id)
        ON DELETE CASCADE ON UPDATE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Tabla para almacenar preguntas principales y subpreguntas de la grilla de manera dinámica';


-- =====================================================
-- Paso 2: Insertar configuración inicial (3 preguntas principales + 3 subpreguntas)
-- =====================================================

SET @usuario_admin = 2;

-- PREGUNTAS PRINCIPALES (tipo = 'pregunta')
-- ==============================================
INSERT INTO tbl_preguntas_sub_preguntas_grilla
(tipo_pregunta, texto_pregunta, codigo_pregunta, orden, pregunta_padre_id, opciones_respuesta, requiere_todas_si, habilita_subpreguntas, condicion_habilitacion, habilitado, tbl_usuario_id)
VALUES
-- Pregunta 1: CONOCE AL CANDIDATO
(
    'pregunta',
    'CONOCE O NO LO CONOCE',
    'conoce',
    1,
    NULL,
    '["si", "no"]',
    FALSE,
    TRUE,
    'si',
    TRUE,
    @usuario_admin
),

-- Pregunta 2: IMAGEN FAVORABLE O DESFAVORABLE
(
    'pregunta',
    'IMAGEN FAVORABLE O DESFAVORABLE',
    'imagen',
    2,
    NULL,
    '["favorable", "desfavorable"]',
    FALSE,
    TRUE,
    'favorable',
    TRUE,
    @usuario_admin
),

-- Pregunta 3: ¿VOTARÍA POR ÉL/ELLA?
(
    'pregunta',
    'VOTARIA POR EL O POR ELLA',
    'votaria',
    3,
    NULL,
    '["si", "no"]',
    TRUE,
    TRUE,
    'todas_si',
    TRUE,
    @usuario_admin
);


-- SUBPREGUNTAS (tipo = 'subpregunta')
-- ==============================================
-- Estas solo se muestran si el candidato es "aprobado" (todas las principales = SÍ)

-- Obtener ID de la pregunta 'votaria' para relacionar subpreguntas
SET @pregunta_votaria_id = (SELECT id FROM tbl_preguntas_sub_preguntas_grilla WHERE codigo_pregunta = 'votaria' LIMIT 1);

INSERT INTO tbl_preguntas_sub_preguntas_grilla
(tipo_pregunta, texto_pregunta, codigo_pregunta, orden, pregunta_padre_id, opciones_respuesta, requiere_todas_si, habilita_subpreguntas, condicion_habilitacion, habilitado, tbl_usuario_id)
VALUES
-- Subpregunta 1: P(A)
(
    'subpregunta',
    'SI LAS ELECCIONES FUERAN HOY UD POR QUIEN VOTARIA P(A)',
    'pa',
    1,
    @pregunta_votaria_id,
    NULL,
    FALSE,
    FALSE,
    NULL,
    TRUE,
    @usuario_admin
),

-- Subpregunta 2: P(B)
(
    'subpregunta',
    'SI SU CANDIDATO P(A) SE RETIRA POR QUIEN VOTARIA P(B)',
    'pb',
    2,
    @pregunta_votaria_id,
    NULL,
    FALSE,
    FALSE,
    NULL,
    TRUE,
    @usuario_admin
),

-- Subpregunta 3: P(C)
(
    'subpregunta',
    'SI SU CANDIDATO P(B) SE RETIRA POR QUIEN VOTARIA P(C)',
    'pc',
    3,
    @pregunta_votaria_id,
    NULL,
    FALSE,
    FALSE,
    NULL,
    TRUE,
    @usuario_admin
);


-- =====================================================
-- Paso 3: Crear vista para consultas simplificadas
-- =====================================================

CREATE OR REPLACE VIEW vw_preguntas_grilla_completas AS
SELECT
    p.id,
    p.tipo_pregunta,
    p.texto_pregunta,
    p.codigo_pregunta,
    p.orden,
    p.pregunta_padre_id,
    p.opciones_respuesta,
    p.requiere_todas_si,
    p.habilita_subpreguntas,
    p.condicion_habilitacion,
    p.habilitado,

    -- Datos de la pregunta padre (si existe)
    padre.codigo_pregunta AS codigo_pregunta_padre,
    padre.texto_pregunta AS texto_pregunta_padre,

    -- Conteo de subpreguntas (si es pregunta principal)
    (SELECT COUNT(*)
     FROM tbl_preguntas_sub_preguntas_grilla sub
     WHERE sub.pregunta_padre_id = p.id) AS total_subpreguntas

FROM tbl_preguntas_sub_preguntas_grilla p
LEFT JOIN tbl_preguntas_sub_preguntas_grilla padre ON p.pregunta_padre_id = padre.id
WHERE p.habilitado = TRUE
ORDER BY p.tipo_pregunta ASC, p.orden ASC;


-- =====================================================
-- CONSULTAS ÚTILES PARA VERIFICAR
-- =====================================================

SELECT
    id,
    tipo_pregunta,
    texto_pregunta,
    codigo_pregunta,
    orden,
    pregunta_padre_id,
    opciones_respuesta
FROM tbl_preguntas_sub_preguntas_grilla
ORDER BY tipo_pregunta ASC, orden ASC;

SELECT * FROM vw_preguntas_grilla_completas;

-- =====================================================
-- FIN DEL SCRIPT
-- =====================================================


-- =====================================================
-- NOTAS IMPORTANTES
-- =====================================================

/*
ESTRUCTURA DINÁMICA:

1. PREGUNTAS PRINCIPALES (tipo = 'pregunta'):
   - Son las preguntas que se hacen para CADA candidato
   - Ejemplos: "¿Conoce al candidato?", "¿Imagen favorable?", "¿Votaría por él?"
   - Se ordenan por el campo `orden`
   - Pueden tener opciones fijas en `opciones_respuesta` (JSON)

2. SUBPREGUNTAS (tipo = 'subpregunta'):
   - Solo se muestran si hay candidatos "aprobados"
   - Un candidato es "aprobado" si pasa TODAS las preguntas principales con respuesta positiva
   - Tienen un `pregunta_padre_id` que las relaciona con una pregunta principal
   - Ejemplos: P(A), P(B), P(C) - preguntas sobre preferencia electoral

3. CÓDIGOS DE PREGUNTA (codigo_pregunta):
   - Son identificadores únicos usados en el código
   - Ejemplos: 'conoce', 'imagen', 'votaria', 'pa', 'pb', 'pc'
   - Permite cambiar el texto visible sin romper el código

4. CONDICIONES DE HABILITACIÓN (condicion_habilitacion):
   - Define cuándo se habilitan las siguientes preguntas
   - 'si': Se habilita si responde SÍ
   - 'favorable': Se habilita si responde FAVORABLE
   - 'todas_si': Se habilita solo si todas las anteriores son positivas

5. OPCIONES DE RESPUESTA (opciones_respuesta):
   - JSON array con las opciones disponibles
   - Para preguntas principales: ["si", "no"], ["favorable", "desfavorable"]
   - Para subpreguntas: NULL (se usan candidatos aprobados dinámicamente)

6. AGREGAR NUEVAS PREGUNTAS:
   - Simplemente INSERT en esta tabla con el orden adecuado
   - No requiere modificar código PHP ni JavaScript
   - El sistema las carga automáticamente

EJEMPLO DE USO:

Para agregar una 4ta pregunta principal:

INSERT INTO tbl_preguntas_sub_preguntas_grilla
(tipo_pregunta, texto_pregunta, codigo_pregunta, orden, opciones_respuesta, habilita_subpreguntas, condicion_habilitacion, habilitado, tbl_usuario_id)
VALUES
('pregunta', '¿Confía en este candidato?', 'confia', 4, '["mucho", "poco", "nada"]', FALSE, NULL, TRUE, 2);

Para agregar una 4ta subpregunta:

INSERT INTO tbl_preguntas_sub_preguntas_grilla
(tipo_pregunta, texto_pregunta, codigo_pregunta, orden, pregunta_padre_id, habilitado, tbl_usuario_id)
VALUES
('subpregunta', 'Si los 3 anteriores se retiran, ¿por quién votaría? P(D)', 'pd', 4, @pregunta_votaria_id, TRUE, 2);

*/
