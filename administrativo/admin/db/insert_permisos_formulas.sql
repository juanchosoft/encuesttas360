-- Crear permisos para el módulo de Fórmulas
-- IDs 46-49 siguiendo el patrón de los módulos anteriores

USE estadisticas_db;

-- Insertar los 4 permisos del módulo Fórmulas
INSERT INTO tbl_permisos (id, dtcreate, modulo, nombre) VALUES
(46, NOW(), 'Fórmulas', 'Ver Fórmulas'),
(47, NOW(), 'Fórmulas', 'Crear Fórmulas'),
(48, NOW(), 'Fórmulas', 'Editar Fórmulas'),
(49, NOW(), 'Fórmulas', 'Permisos Fórmulas');

-- Asignar todos los permisos de Fórmulas al usuario id 2
INSERT INTO tbl_usuarios_has_tbl_permisos (tbl_usuarios_id, tbl_permiso_id, dtcreate) VALUES
(2, 46, NOW()),
(2, 47, NOW()),
(2, 48, NOW()),
(2, 49, NOW());

-- Verificar la inserción
SELECT * FROM tbl_permisos WHERE id BETWEEN 46 AND 49;
SELECT u.id, u.nombre, u.apellido, p.id as permiso_id, p.nombre as permiso_nombre
FROM tbl_usuarios u
INNER JOIN tbl_usuarios_has_tbl_permisos up ON u.id = up.tbl_usuarios_id
INNER JOIN tbl_permisos p ON up.tbl_permiso_id = p.id
WHERE u.id = 2 AND p.id BETWEEN 46 AND 49;
