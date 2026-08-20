-- =====================================================================
-- Permisos para el módulo de Análisis de Estudio
-- =====================================================================
-- Este módulo es exclusivo para usuarios tipo Investigador
-- Permite calcular y registrar indicadores electorales para candidatos
-- =====================================================================

USE estadisticas_db;

-- Insertar permisos para el módulo Análisis de Estudio (IDs 50-53)
INSERT INTO tbl_permisos (id, modulo, nombre, dtcreate) VALUES
(50, 'Análisis de Estudio', 'Ver Análisis de Estudio', NOW()),
(51, 'Análisis de Estudio', 'Crear Análisis de Estudio', NOW()),
(52, 'Análisis de Estudio', 'Editar Análisis de Estudio', NOW()),
(53, 'Análisis de Estudio', 'Permisos Análisis de Estudio', NOW());

-- Asignar todos los permisos al usuario Investigador (id 2)
INSERT INTO tbl_usuarios_has_tbl_permisos (tbl_usuarios_id, tbl_permiso_id, dtcreate) VALUES
(2, 50, NOW()), -- Ver
(2, 51, NOW()), -- Crear
(2, 52, NOW()), -- Editar
(2, 53, NOW()); -- Permisos

-- Verificar inserción de permisos
SELECT * FROM tbl_permisos WHERE id BETWEEN 50 AND 53;

-- Verificar asignación de permisos al usuario
SELECT
    u.id AS usuario_id,
    u.nombre,
    u.apellido,
    u.tipo,
    p.id AS permiso_id,
    p.modulo,
    p.nombre AS permiso_nombre
FROM tbl_usuarios_has_tbl_permisos up
INNER JOIN tbl_usuarios u ON up.tbl_usuarios_id = u.id
INNER JOIN tbl_permisos p ON up.tbl_permiso_id = p.id
WHERE u.id = 2 AND p.id BETWEEN 50 AND 53
ORDER BY p.id;
