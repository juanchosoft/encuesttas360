-- ============================================================================
-- Script de Permisos para Módulos de Configuración Estadísticas
-- ============================================================================
-- Este script crea los permisos necesarios para todos los módulos de la
-- sección "Configuración Estadísticas" y los asigna al usuario ID 2
-- ============================================================================

-- 1. PARTIDOS POLÍTICOS (IDs: 10-13)
INSERT INTO tbl_permisos (id, modulo, nombre, dtcreate) VALUES
(10, 'Partidos Políticos', 'Partidos Políticos - Ver', NOW()),
(11, 'Partidos Políticos', 'Partidos Políticos - Crear', NOW()),
(12, 'Partidos Políticos', 'Partidos Políticos - Editar', NOW()),
(13, 'Partidos Políticos', 'Partidos Políticos - Permisos', NOW());

-- 2. ESPACIO GEOGRÁFICO (IDs: 14-17)
INSERT INTO tbl_permisos (id, modulo, nombre, dtcreate) VALUES
(14, 'Espacio Geográfico', 'Espacio Geográfico - Ver', NOW()),
(15, 'Espacio Geográfico', 'Espacio Geográfico - Crear', NOW()),
(16, 'Espacio Geográfico', 'Espacio Geográfico - Editar', NOW()),
(17, 'Espacio Geográfico', 'Espacio Geográfico - Permisos', NOW());

-- 3. FICHA TÉCNICA ENCUESTA (IDs: 18-21)
INSERT INTO tbl_permisos (id, modulo, nombre, dtcreate) VALUES
(18, 'Ficha Técnica Encuesta', 'Ficha Técnica Encuesta - Ver', NOW()),
(19, 'Ficha Técnica Encuesta', 'Ficha Técnica Encuesta - Crear', NOW()),
(20, 'Ficha Técnica Encuesta', 'Ficha Técnica Encuesta - Editar', NOW()),
(21, 'Ficha Técnica Encuesta', 'Ficha Técnica Encuesta - Permisos', NOW());

-- 4. PERSONAL POLÍTICO / PARTICIPANTES (IDs: 22-25)
INSERT INTO tbl_permisos (id, modulo, nombre, dtcreate) VALUES
(22, 'Personal Político', 'Personal Político - Ver', NOW()),
(23, 'Personal Político', 'Personal Político - Crear', NOW()),
(24, 'Personal Político', 'Personal Político - Editar', NOW()),
(25, 'Personal Político', 'Personal Político - Permisos', NOW());

-- 5. VOTANTES (IDs: 26-29)
INSERT INTO tbl_permisos (id, modulo, nombre, dtcreate) VALUES
(26, 'Votantes', 'Votantes - Ver', NOW()),
(27, 'Votantes', 'Votantes - Crear', NOW()),
(28, 'Votantes', 'Votantes - Editar', NOW()),
(29, 'Votantes', 'Votantes - Permisos', NOW());

-- 6. PREGUNTAS (IDs: 30-33)
INSERT INTO tbl_permisos (id, modulo, nombre, dtcreate) VALUES
(30, 'Preguntas', 'Preguntas - Ver', NOW()),
(31, 'Preguntas', 'Preguntas - Crear', NOW()),
(32, 'Preguntas', 'Preguntas - Editar', NOW()),
(33, 'Preguntas', 'Preguntas - Permisos', NOW());

-- 7. SONDEOS (IDs: 34-37)
INSERT INTO tbl_permisos (id, modulo, nombre, dtcreate) VALUES
(34, 'Sondeos', 'Sondeos - Ver', NOW()),
(35, 'Sondeos', 'Sondeos - Crear', NOW()),
(36, 'Sondeos', 'Sondeos - Editar', NOW()),
(37, 'Sondeos', 'Sondeos - Permisos', NOW());

-- 8. PREGUNTAS GRILLA (IDs: 38-41)
INSERT INTO tbl_permisos (id, modulo, nombre, dtcreate) VALUES
(38, 'Preguntas Grilla', 'Preguntas Grilla - Ver', NOW()),
(39, 'Preguntas Grilla', 'Preguntas Grilla - Crear', NOW()),
(40, 'Preguntas Grilla', 'Preguntas Grilla - Editar', NOW()),
(41, 'Preguntas Grilla', 'Preguntas Grilla - Permisos', NOW());

-- 9. GRILLA (IDs: 42-45)
INSERT INTO tbl_permisos (id, modulo, nombre, dtcreate) VALUES
(42, 'Grilla', 'Grilla - Ver', NOW()),
(43, 'Grilla', 'Grilla - Crear', NOW()),
(44, 'Grilla', 'Grilla - Editar', NOW()),
(45, 'Grilla', 'Grilla - Permisos', NOW());


-- ============================================================================
-- RESUMEN DE PERMISOS
-- ============================================================================
-- Partidos Políticos:         10 (Ver), 11 (Crear), 12 (Editar), 13 (Permisos)
-- Espacio Geográfico:          14 (Ver), 15 (Crear), 16 (Editar), 17 (Permisos)
-- Ficha Técnica Encuesta:      18 (Ver), 19 (Crear), 20 (Editar), 21 (Permisos)
-- Personal Político:           22 (Ver), 23 (Crear), 24 (Editar), 25 (Permisos)
-- Votantes:                    26 (Ver), 27 (Crear), 28 (Editar), 29 (Permisos)
-- Preguntas:                   30 (Ver), 31 (Crear), 32 (Editar), 33 (Permisos)
-- Sondeos:                     34 (Ver), 35 (Crear), 36 (Editar), 37 (Permisos)
-- Preguntas Grilla:            38 (Ver), 39 (Crear), 40 (Editar), 41 (Permisos)
-- Grilla:                      42 (Ver), 43 (Crear), 44 (Editar), 45 (Permisos)
-- ============================================================================
