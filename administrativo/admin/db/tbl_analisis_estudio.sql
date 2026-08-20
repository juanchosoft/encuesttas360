-- Tabla para almacenar análisis de indicadores por candidato en cada grilla
-- Módulo: Análisis de Estudio (Solo para Investigadores)

CREATE TABLE IF NOT EXISTS tbl_analisis_estudio (
    id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    tbl_grilla_id INT(11) NOT NULL COMMENT 'ID de la grilla analizada',
    tbl_participante_id INT(11) NOT NULL COMMENT 'ID del candidato analizado',
    tbl_formula_id INT(11) UNSIGNED NOT NULL COMMENT 'ID del indicador/fórmula aplicada',
    resultado_calculado VARCHAR(255) DEFAULT NULL COMMENT 'Valor calculado del indicador',
    observaciones TEXT DEFAULT NULL COMMENT 'Notas y observaciones del investigador',
    tbl_usuario_id INT(11) DEFAULT NULL COMMENT 'ID del investigador que realizó el análisis',
    dtcreate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    dtupdate DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    INDEX idx_grilla (tbl_grilla_id),
    INDEX idx_participante (tbl_participante_id),
    INDEX idx_formula (tbl_formula_id),
    INDEX idx_usuario (tbl_usuario_id),
    INDEX idx_grilla_participante (tbl_grilla_id, tbl_participante_id),

    UNIQUE KEY unique_analisis (tbl_grilla_id, tbl_participante_id, tbl_formula_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Almacena los resultados de análisis de indicadores por candidato';

-- Insertar datos de ejemplo (opcional)
-- INSERT INTO tbl_analisis_estudio (tbl_grilla_id, tbl_participante_id, tbl_formula_id, resultado_calculado, observaciones, tbl_usuario_id)
-- VALUES (1, 1, 1, '45.5%', 'Indicador de conocimiento calculado con base en 1000 encuestas', 2);
