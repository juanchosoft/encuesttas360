-- Insertar datos de fórmulas e indicadores estadísticos
-- Basado en formulas_template.csv

USE estadisticas_db;

-- Limpiar tabla (opcional - comentar si ya hay datos que quieres mantener)
-- TRUNCATE TABLE tbl_formulas;

-- Insertar las fórmulas
INSERT INTO tbl_formulas (indicador, sigla, tipo_indicador, formula, explicacion, observaciones, enunciado_antes, enunciado_ahora, notas_adicionales, habilitado, tbl_usuario_id, dtcreate) VALUES
('Índice de Favorabilidad', 'IF', 'Electoral', 'IF = (Respuestas Positivas / Total Respuestas) * 100', 'Mide el porcentaje de respuestas favorables sobre el total de respuestas obtenidas', 'Se utiliza para medir la aceptación de candidatos o políticas', '¿Qué tan favorable es su opinión?', '¿Cuál es su nivel de favorabilidad?', 'Este índice es fundamental para análisis de tendencias políticas', 'si', 1, NOW()),

('Margen de Error', 'ME', 'Estadístico', 'ME = Z * √(p * (1-p) / n)', 'Calcula el margen de error estadístico donde Z es el nivel de confianza, p la proporción y n el tamaño de muestra', 'Típicamente se usa un nivel de confianza del 95% (Z=1.96)', 'Calcular margen estadístico', 'Determinar el margen de error de la muestra', 'A mayor tamaño de muestra, menor margen de error', 'si', 1, NOW()),

('Intención de Voto', 'IV', 'Electoral', 'IV = (Votantes del Candidato / Total Votantes Decididos) * 100', 'Porcentaje de intención de voto para un candidato específico', 'No incluye indecisos ni votos en blanco', '¿Por quién votaría?', '¿Cuál es su intención de voto?', 'Se debe considerar el margen de error en la interpretación', 'si', 1, NOW()),

('Índice de Imagen Positiva', 'IIP', 'Electoral', 'IIP = (Imagen Positiva / Total Respuestas) * 100', 'Porcentaje de personas con imagen positiva del candidato', 'Excluye neutral y no sabe/no responde', '¿Qué imagen tiene del candidato?', '¿Cómo calificaría su imagen del candidato?', 'Complementar con favorabilidad', 'si', 1, NOW()),

('Índice de Conocimiento', 'IC', 'Electoral', 'IC = (Conoce / Total Encuestados) * 100', 'Porcentaje de personas que conocen al candidato o propuesta', 'Base fundamental para otros indicadores', '¿Lo conoce o no lo conoce?', '¿Ha escuchado hablar sobre este candidato?', 'Necesario tener alto conocimiento para buena votación', 'si', 1, NOW()),

('Tasa de Rechazo', 'TR', 'Electoral', 'TR = (Respuestas Negativas / Total Respuestas) * 100', 'Porcentaje de respuestas desfavorables o de rechazo', 'Opuesto al índice de favorabilidad', '¿Lo rechaza?', '¿Qué tan desfavorable es su opinión?', 'Alto rechazo puede ser difícil de revertir', 'si', 1, NOW()),

('Índice de Decisión Electoral', 'IDE', 'Electoral', 'IDE = (Votantes Decididos / Total Encuestados) * 100', 'Porcentaje de votantes que ya tienen decisión tomada', 'No incluye indecisos, votos en blanco o nulos', '¿Ya decidió su voto?', '¿Ya tiene su voto decidido?', 'A mayor cercanía de elecciones, mayor índice', 'si', 1, NOW()),

('Nivel de Certeza de Voto', 'NCV', 'Electoral', 'NCV = (Totalmente Seguro / Decididos) * 100', 'De los decididos, cuántos están totalmente seguros de su voto', 'Indicador de firmeza electoral', '¿Qué tan seguro está?', '¿Del 1 al 10, qué tan seguro está de su voto?', 'Voto firme es menos vulnerable a cambios', 'si', 1, NOW());

-- Verificar la inserción
SELECT COUNT(*) AS total_formulas FROM tbl_formulas;
SELECT sigla, indicador, tipo_indicador FROM tbl_formulas ORDER BY tipo_indicador, indicador;
