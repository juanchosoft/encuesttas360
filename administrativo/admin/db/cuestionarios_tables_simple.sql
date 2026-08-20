-- Tabla para almacenar los intentos de respuesta a cuestionarios
CREATE TABLE IF NOT EXISTS tbl_cuestionario_intentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tbl_ficha_tecnica_encuesta_id INT NOT NULL,
    tbl_votante_id INT NULL,
    nombre_respondiente VARCHAR(255) NULL COMMENT 'Nombre del respondiente (si no es votante registrado)',
    identificacion_respondiente VARCHAR(100) NULL COMMENT 'Identificación del respondiente (si no es votante registrado)',
    email_respondiente VARCHAR(255) NULL,
    telefono_respondiente VARCHAR(50) NULL,
    fecha_respuesta DATETIME NOT NULL,
    dtcreate DATETIME NOT NULL,
    INDEX idx_ficha_tecnica (tbl_ficha_tecnica_encuesta_id),
    INDEX idx_votante (tbl_votante_id),
    INDEX idx_fecha_respuesta (fecha_respuesta)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Almacena los intentos de respuesta a cuestionarios. Cada registro representa una sesión de respuesta completa.';

-- Tabla para almacenar las respuestas individuales de cada pregunta
CREATE TABLE IF NOT EXISTS tbl_cuestionario_respuestas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tbl_intento_id INT NOT NULL,
    tbl_pregunta_id INT NOT NULL,
    tbl_opcion_respuesta_id INT NULL COMMENT 'ID de la opción seleccionada (para radio/checkbox)',
    respuesta_texto TEXT NULL COMMENT 'Respuesta en texto libre (para preguntas abiertas)',
    dtcreate DATETIME NOT NULL,
    INDEX idx_intento (tbl_intento_id),
    INDEX idx_pregunta (tbl_pregunta_id),
    INDEX idx_opcion (tbl_opcion_respuesta_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Almacena cada respuesta individual. Un intento puede tener múltiples respuestas.';

-- Índice compuesto para consultas de análisis
CREATE INDEX idx_intento_pregunta_opcion
ON tbl_cuestionario_respuestas(tbl_intento_id, tbl_pregunta_id, tbl_opcion_respuesta_id);
