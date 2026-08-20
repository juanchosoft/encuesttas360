-- ========================================
-- Script de Verificación de Permisos
-- Sistema con 6 Roles Únicos
-- ========================================

-- INSTRUCCIONES:
-- Reemplaza 'email@test.com' con el email del usuario que creaste
-- Los totales esperados están documentados al final

-- ========================================
-- 1. VERIFICACIÓN BÁSICA DE USUARIO
-- ========================================

-- Ver información básica del usuario
SELECT
    id,
    nombre,
    apellido,
    email,
    tipo,
    habilitado,
    dtcreate as fecha_creacion
FROM tbl_usuarios
WHERE email = 'email@test.com';  -- ⬅️ CAMBIAR POR EL EMAIL DEL USUARIO

-- ========================================
-- 2. CONTAR PERMISOS ASIGNADOS
-- ========================================

-- Contar total de permisos del usuario
SELECT
    u.id,
    u.nombre,
    u.apellido,
    u.tipo,
    COUNT(uhp.tbl_permisos_id) as total_permisos_asignados
FROM tbl_usuarios u
LEFT JOIN tbl_usuarios_has_tbl_permisos uhp ON u.id = uhp.tbl_usuarios_id
WHERE u.email = 'email@test.com'  -- ⬅️ CAMBIAR POR EL EMAIL DEL USUARIO
GROUP BY u.id;

-- ========================================
-- 3. VER TODOS LOS PERMISOS CON DETALLES
-- ========================================

-- Ver lista completa de permisos con descripciones
SELECT
    u.nombre,
    u.apellido,
    u.tipo,
    p.id as permiso_id,
    p.descripcion as permiso_descripcion,
    uhp.dtcreate as fecha_asignacion
FROM tbl_usuarios u
INNER JOIN tbl_usuarios_has_tbl_permisos uhp ON u.id = uhp.tbl_usuarios_id
INNER JOIN tbl_permisos p ON uhp.tbl_permisos_id = p.id
WHERE u.email = 'email@test.com'  -- ⬅️ CAMBIAR POR EL EMAIL DEL USUARIO
ORDER BY p.id;

-- ========================================
-- 4. RESUMEN DE PERMISOS POR TIPO
-- ========================================

-- Ver resumen de permisos asignados por tipo de usuario
SELECT
    u.tipo,
    COUNT(uhp.tbl_permisos_id) as total_permisos,
    GROUP_CONCAT(DISTINCT u.nombre SEPARATOR ', ') as usuarios_ejemplo,
    COUNT(DISTINCT u.id) as cantidad_usuarios
FROM tbl_usuarios u
LEFT JOIN tbl_usuarios_has_tbl_permisos uhp ON u.id = uhp.tbl_usuarios_id
GROUP BY u.tipo
ORDER BY total_permisos DESC;

-- ========================================
-- 5. VERIFICACIÓN POR ROL ESPECÍFICO
-- ========================================

-- ADMINISTRADOR - Debe tener 85 permisos
SELECT
    u.nombre,
    u.tipo,
    COUNT(uhp.tbl_permisos_id) as permisos_actuales,
    85 as permisos_esperados,
    CASE
        WHEN COUNT(uhp.tbl_permisos_id) = 85 THEN '✅ CORRECTO'
        ELSE '❌ ERROR'
    END as estado
FROM tbl_usuarios u
LEFT JOIN tbl_usuarios_has_tbl_permisos uhp ON u.id = uhp.tbl_usuarios_id
WHERE u.tipo = 'Administrador'
GROUP BY u.id;

-- INVESTIGADOR - Debe tener 44 permisos
SELECT
    u.nombre,
    u.tipo,
    COUNT(uhp.tbl_permisos_id) as permisos_actuales,
    44 as permisos_esperados,
    CASE
        WHEN COUNT(uhp.tbl_permisos_id) = 44 THEN '✅ CORRECTO'
        ELSE '❌ ERROR'
    END as estado
FROM tbl_usuarios u
LEFT JOIN tbl_usuarios_has_tbl_permisos uhp ON u.id = uhp.tbl_usuarios_id
WHERE u.tipo = 'Investigador'
GROUP BY u.id;

-- VISOR - Debe tener 14 permisos
SELECT
    u.nombre,
    u.tipo,
    COUNT(uhp.tbl_permisos_id) as permisos_actuales,
    14 as permisos_esperados,
    CASE
        WHEN COUNT(uhp.tbl_permisos_id) = 14 THEN '✅ CORRECTO'
        ELSE '❌ ERROR'
    END as estado
FROM tbl_usuarios u
LEFT JOIN tbl_usuarios_has_tbl_permisos uhp ON u.id = uhp.tbl_usuarios_id
WHERE u.tipo = 'Visor'
GROUP BY u.id;

-- OPERATIVO - Debe tener 31 permisos
SELECT
    u.nombre,
    u.tipo,
    COUNT(uhp.tbl_permisos_id) as permisos_actuales,
    31 as permisos_esperados,
    CASE
        WHEN COUNT(uhp.tbl_permisos_id) = 31 THEN '✅ CORRECTO'
        ELSE '❌ ERROR'
    END as estado
FROM tbl_usuarios u
LEFT JOIN tbl_usuarios_has_tbl_permisos uhp ON u.id = uhp.tbl_usuarios_id
WHERE u.tipo = 'Operativo'
GROUP BY u.id;

-- ENCUESTADOR - Debe tener 4 permisos
SELECT
    u.nombre,
    u.tipo,
    COUNT(uhp.tbl_permisos_id) as permisos_actuales,
    4 as permisos_esperados,
    CASE
        WHEN COUNT(uhp.tbl_permisos_id) = 4 THEN '✅ CORRECTO'
        ELSE '❌ ERROR'
    END as estado
FROM tbl_usuarios u
LEFT JOIN tbl_usuarios_has_tbl_permisos uhp ON u.id = uhp.tbl_usuarios_id
WHERE u.tipo = 'Encuestador'
GROUP BY u.id;

-- CLIENTE - Debe tener 2 permisos
SELECT
    u.nombre,
    u.tipo,
    COUNT(uhp.tbl_permisos_id) as permisos_actuales,
    2 as permisos_esperados,
    CASE
        WHEN COUNT(uhp.tbl_permisos_id) = 2 THEN '✅ CORRECTO'
        ELSE '❌ ERROR'
    END as estado
FROM tbl_usuarios u
LEFT JOIN tbl_usuarios_has_tbl_permisos uhp ON u.id = uhp.tbl_usuarios_id
WHERE u.tipo = 'Cliente'
GROUP BY u.id;

-- ========================================
-- 6. VERIFICAR PERMISOS ESPECÍFICOS
-- ========================================

-- Verificar que CLIENTE solo tenga permisos 38 y 42
SELECT
    u.nombre,
    u.tipo,
    GROUP_CONCAT(uhp.tbl_permisos_id ORDER BY uhp.tbl_permisos_id) as permisos_asignados,
    '38,42' as permisos_esperados,
    CASE
        WHEN GROUP_CONCAT(uhp.tbl_permisos_id ORDER BY uhp.tbl_permisos_id) = '38,42'
        THEN '✅ CORRECTO'
        ELSE '❌ ERROR - Permisos incorrectos'
    END as estado
FROM tbl_usuarios u
INNER JOIN tbl_usuarios_has_tbl_permisos uhp ON u.id = uhp.tbl_usuarios_id
WHERE u.tipo = 'Cliente'
GROUP BY u.id;

-- Verificar que ENCUESTADOR solo tenga permisos 26, 27, 28, 29
SELECT
    u.nombre,
    u.tipo,
    GROUP_CONCAT(uhp.tbl_permisos_id ORDER BY uhp.tbl_permisos_id) as permisos_asignados,
    '26,27,28,29' as permisos_esperados,
    CASE
        WHEN GROUP_CONCAT(uhp.tbl_permisos_id ORDER BY uhp.tbl_permisos_id) = '26,27,28,29'
        THEN '✅ CORRECTO'
        ELSE '❌ ERROR - Permisos incorrectos'
    END as estado
FROM tbl_usuarios u
INNER JOIN tbl_usuarios_has_tbl_permisos uhp ON u.id = uhp.tbl_usuarios_id
WHERE u.tipo = 'Encuestador'
GROUP BY u.id;

-- ========================================
-- 7. VERIFICAR PERMISOS DE ANÁLISIS
-- ========================================

-- Verificar que solo INVESTIGADOR y ADMINISTRADOR tengan acceso a Análisis de Estudio
SELECT
    u.nombre,
    u.tipo,
    CASE
        WHEN uhp.tbl_permisos_id = 50 THEN '✅ Tiene Análisis - Ver'
        WHEN uhp.tbl_permisos_id = 51 THEN '✅ Tiene Análisis - Crear'
        WHEN uhp.tbl_permisos_id = 52 THEN '✅ Tiene Análisis - Editar'
        WHEN uhp.tbl_permisos_id = 53 THEN '✅ Tiene Análisis - Eliminar'
        ELSE 'Sin acceso'
    END as estado_analisis
FROM tbl_usuarios u
LEFT JOIN tbl_usuarios_has_tbl_permisos uhp ON u.id = uhp.tbl_usuarios_id
    AND uhp.tbl_permisos_id IN (50, 51, 52, 53)
WHERE u.tipo IN ('Administrador', 'Investigador', 'Visor', 'Operativo', 'Encuestador', 'Cliente')
ORDER BY u.tipo, uhp.tbl_permisos_id;

-- ========================================
-- 8. DIAGNÓSTICO COMPLETO
-- ========================================

-- Resumen completo del sistema de permisos
SELECT
    'Total de usuarios' as metrica,
    COUNT(*) as valor
FROM tbl_usuarios
UNION ALL
SELECT
    'Usuarios Administrador',
    COUNT(*)
FROM tbl_usuarios WHERE tipo = 'Administrador'
UNION ALL
SELECT
    'Usuarios Investigador',
    COUNT(*)
FROM tbl_usuarios WHERE tipo = 'Investigador'
UNION ALL
SELECT
    'Usuarios Visor',
    COUNT(*)
FROM tbl_usuarios WHERE tipo = 'Visor'
UNION ALL
SELECT
    'Usuarios Operativo',
    COUNT(*)
FROM tbl_usuarios WHERE tipo = 'Operativo'
UNION ALL
SELECT
    'Usuarios Encuestador',
    COUNT(*)
FROM tbl_usuarios WHERE tipo = 'Encuestador'
UNION ALL
SELECT
    'Usuarios Cliente',
    COUNT(*)
FROM tbl_usuarios WHERE tipo = 'Cliente'
UNION ALL
SELECT
    'Total de permisos en sistema',
    COUNT(*)
FROM tbl_permisos
UNION ALL
SELECT
    'Total de asignaciones de permisos',
    COUNT(*)
FROM tbl_usuarios_has_tbl_permisos;

-- ========================================
-- TOTALES ESPERADOS POR ROL
-- ========================================

/*
┌─────────────────┬──────────────────────┐
│ Tipo de Usuario │ Permisos Esperados   │
├─────────────────┼──────────────────────┤
│ Administrador   │ 85 (TODOS)           │
│ Investigador    │ 44                   │
│ Visor           │ 14                   │
│ Operativo       │ 31                   │
│ Encuestador     │ 4                    │
│ Cliente         │ 2                    │
└─────────────────┴──────────────────────┘

PERMISOS ESPECÍFICOS:

✅ Administrador (85 permisos):
   - range(1, 85) = TODOS los permisos del sistema

✅ Investigador (44 permisos):
   50, 51, 52, 53 (Análisis de Estudio)
   46, 47, 48, 49 (Fórmulas)
   42, 43, 44, 45 (Grilla)
   38, 39, 40, 41 (Preguntas Grilla)
   26, 27, 28 (Votantes)
   22, 23, 24 (Personal Político)
   34, 35, 36 (Sondeos)
   10, 11, 12 (Partidos Políticos)
   30, 31, 32 (Preguntas)
   14, 15, 16 (Espacio Geográfico)
   18, 19, 20 (Ficha Técnica Encuesta)
   80, 81, 82 (Línea)
   83, 84, 85 (Estrategia)
   1 (Usuarios - Ver)

✅ Visor (14 permisos):
   1, 10, 14, 18, 22, 26, 30, 34, 38, 42, 46, 50, 80, 83
   (Solo Ver en todos los módulos)

✅ Operativo (31 permisos):
   1 (Usuarios - Ver)
   10, 11, 12 (Partidos Políticos)
   14, 15, 16 (Espacio Geográfico)
   18, 19, 20 (Ficha Técnica)
   22, 23, 24 (Personal Político)
   26, 27, 28 (Votantes)
   30, 31, 32 (Preguntas)
   34, 35, 36 (Sondeos)
   38, 39, 40 (Preguntas Grilla)
   42, 43, 44 (Grilla)
   46, 47, 48 (Fórmulas)

✅ Encuestador (4 permisos):
   26, 27, 28, 29 (Votantes completo)

✅ Cliente (2 permisos):
   38 (Preguntas Grilla - Ver)
   42 (Grilla - Ver)

*/
