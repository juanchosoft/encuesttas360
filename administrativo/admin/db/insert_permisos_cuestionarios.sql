-- =====================================================================
-- Permisos para los módulos de Cuestionarios
-- =====================================================================
-- Crea permisos para:
-- 1. Contestar Cuestionario (IDs 70-73)
-- 2. Resultados Cuestionarios (IDs 74-77)
-- =====================================================================

USE estadisticas_db;

-- 1. CONTESTAR CUESTIONARIO (IDs: 70-73)
INSERT INTO tbl_permisos (id, modulo, nombre, dtcreate) VALUES
(70, 'Contestar Cuestionario', 'Contestar Cuestionario - Ver', NOW()),
(71, 'Contestar Cuestionario', 'Contestar Cuestionario - Crear', NOW()),
(72, 'Contestar Cuestionario', 'Contestar Cuestionario - Editar', NOW()),
(73, 'Contestar Cuestionario', 'Contestar Cuestionario - Permisos', NOW());

-- 2. RESULTADOS CUESTIONARIOS (IDs: 74-77)
INSERT INTO tbl_permisos (id, modulo, nombre, dtcreate) VALUES
(74, 'Resultados Cuestionarios', 'Resultados Cuestionarios - Ver', NOW()),
(75, 'Resultados Cuestionarios', 'Resultados Cuestionarios - Crear', NOW()),
(76, 'Resultados Cuestionarios', 'Resultados Cuestionarios - Editar', NOW()),
(77, 'Resultados Cuestionarios', 'Resultados Cuestionarios - Permisos', NOW());

-- =====================================================================
-- Asignar todos los permisos al usuario ID 2
-- =====================================================================

-- Permisos de Contestar Cuestionario
INSERT INTO tbl_usuarios_has_tbl_permisos (tbl_usuarios_id, tbl_permiso_id, dtcreate) VALUES
(2, 70, NOW()), -- Ver
(2, 71, NOW()), -- Crear
(2, 72, NOW()), -- Editar
(2, 73, NOW()); -- Permisos

-- Permisos de Resultados Cuestionarios
INSERT INTO tbl_usuarios_has_tbl_permisos (tbl_usuarios_id, tbl_permiso_id, dtcreate) VALUES
(2, 74, NOW()), -- Ver
(2, 75, NOW()), -- Crear
(2, 76, NOW()), -- Editar
(2, 77, NOW()); -- Permisos

-- =====================================================================
-- VERIFICACIÓN
-- =====================================================================

-- Verificar inserción de permisos
SELECT * FROM tbl_permisos WHERE id BETWEEN 70 AND 77 ORDER BY id;

-- Verificar asignación de permisos al usuario ID 2
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
WHERE u.id = 2 AND p.id BETWEEN 70 AND 77
ORDER BY p.id;

-- =====================================================================
-- RESUMEN DE PERMISOS CREADOS
-- =====================================================================
-- Contestar Cuestionario:        70 (Ver), 71 (Crear), 72 (Editar), 73 (Permisos)
-- Resultados Cuestionarios:      74 (Ver), 75 (Crear), 76 (Editar), 77 (Permisos)
-- =====================================================================
