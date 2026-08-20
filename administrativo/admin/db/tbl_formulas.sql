-- =============================================
-- Script: Creación de tabla tbl_formulas
-- Descripción: Tabla para almacenar fórmulas e indicadores estadísticos
-- Base de datos: estadisticas_db
-- Fecha: 2025-01-05
-- =============================================

USE estadisticas_db;

-- Eliminar tabla si existe (para desarrollo, comentar en producción)
-- DROP TABLE IF EXISTS tbl_formulas;

-- Crear tabla tbl_formulas
CREATE TABLE IF NOT EXISTS tbl_formulas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    indicador VARCHAR(500) NOT NULL COMMENT 'Nombre del indicador',
    sigla VARCHAR(100) NOT NULL COMMENT 'Sigla o abreviatura del indicador',
    formula TEXT NOT NULL COMMENT 'Fórmula matemática del indicador',
    explicacion TEXT COMMENT 'Explicación detallada de la fórmula',
    observaciones TEXT COMMENT 'Observaciones sobre el uso del indicador',
    enunciado_antes TEXT COMMENT 'Enunciado anterior del indicador',
    enunciado_ahora TEXT COMMENT 'Enunciado actual del indicador',
    notas_adicionales TEXT COMMENT 'Notas adicionales relevantes',
    habilitado ENUM('si', 'no') DEFAULT 'si' COMMENT 'Estado del indicador',
    tbl_usuario_id INT NOT NULL COMMENT 'Usuario que creó el registro',
    dtcreate TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de creación',
    dtupdate TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha de última actualización',
    INDEX idx_sigla (sigla),
    INDEX idx_indicador (indicador(255)),
    INDEX idx_habilitado (habilitado),
    INDEX idx_usuario (tbl_usuario_id),
    FOREIGN KEY (tbl_usuario_id) REFERENCES tbl_usuarios(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Fórmulas e indicadores estadísticos';

-- Insertar algunos registros de ejemplo
INSERT INTO tbl_formulas (
    indicador,
    sigla,
    formula,
    explicacion,
    observaciones,
    enunciado_antes,
    enunciado_ahora,
    notas_adicionales,
    tbl_usuario_id
) VALUES
(
    'Índice de Favorabilidad',
    'IF',
    'IF = (Respuestas Positivas / Total Respuestas) * 100',
    'Mide el porcentaje de respuestas favorables sobre el total de respuestas obtenidas',
    'Se utiliza para medir la aceptación de candidatos o políticas',
    '¿Qué tan favorable es su opinión?',
    '¿Cuál es su nivel de favorabilidad?',
    'Este índice es fundamental para análisis de tendencias políticas',
    1
),
(
    'Margen de Error',
    'ME',
    'ME = Z * √(p * (1-p) / n)',
    'Calcula el margen de error estadístico donde Z es el nivel de confianza, p la proporción y n el tamaño de muestra',
    'Típicamente se usa un nivel de confianza del 95% (Z=1.96)',
    'Calcular margen estadístico',
    'Determinar el margen de error de la muestra',
    'A mayor tamaño de muestra, menor margen de error',
    1
),
(
    'Intención de Voto',
    'IV',
    'IV = (Votantes del Candidato / Total Votantes Decididos) * 100',
    'Porcentaje de intención de voto para un candidato específico',
    'No incluye indecisos ni votos en blanco',
    '¿Por quién votaría?',
    '¿Cuál es su intención de voto?',
    'Se debe considerar el margen de error en la interpretación',
    1
);

-- Verificar la creación
SELECT 'Tabla tbl_formulas creada exitosamente' AS mensaje;
SELECT COUNT(*) AS total_registros FROM tbl_formulas;
