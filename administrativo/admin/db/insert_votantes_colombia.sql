-- Script para poblar votantes de todos los departamentos de Colombia
-- Genera 500 votantes distribuidos por todos los departamentos

-- Configuración de variables
SET @nombres_masculinos = 'Juan,Carlos,Pedro,Luis,Miguel,Andrés,Jorge,Fernando,Roberto,Diego,Javier,Manuel,Ricardo,Santiago,David,Felipe,Oscar,Sergio,Daniel,Alejandro,Mario,Francisco,Alberto,Rafael,Antonio,Gustavo,Hernán,Eduardo,Germán,Pablo,Julio,Mauricio,Enrique,Camilo,Sebastián,Cristian,Iván,César,Fabián,Leonardo,Tomás,Ignacio,Rodrigo,Arturo,Raúl';
SET @nombres_femeninos = 'María,Ana,Laura,Carolina,Diana,Andrea,Claudia,Paola,Sandra,Patricia,Liliana,Gloria,Martha,Beatriz,Adriana,Marcela,Natalia,Paula,Catalina,Lucía,Alejandra,Sofía,Valentina,Gabriela,Isabella,Daniela,Camila,Juliana,Mariana,Angela,Mónica,Tatiana,Viviana,Luisa,Silvia,Carmen,Elena,Rosa,Pilar,Teresa';
SET @apellidos = 'García,Rodríguez,Martínez,López,González,Pérez,Sánchez,Ramírez,Torres,Flores,Rivera,Gómez,Díaz,Cruz,Morales,Jiménez,Hernández,Castillo,Reyes,Vargas,Romero,Álvarez,Mendoza,Gutiérrez,Ruiz,Ortiz,Muñoz,Castro,Silva,Rojas,Vega,Medina,Moreno,Salazar,Guerrero,Aguilar,Suárez,Valencia,Parra,Molina,Espinosa,Serrano,León,Ríos,Contreras,Cortés,Navarro,Herrera,Estrada,Mejía,Ramos,Rubio,Lara,Santana,Carrillo,Ávila,Soto,Campos,Téllez,Pedraza,Cano,Vera,Acosta,Quintero,Delgado,Patiño,Montoya,Cárdenas,Zúñiga,Ochoa,Giraldo,Henao,Vélez,Zapata';

-- Eliminar votantes simulados previos si existen
DELETE FROM tbl_respuestas_sondeos WHERE tbl_votante_id IN (
    SELECT id FROM tbl_votantes WHERE nombre_completo LIKE 'Votante%'
);
DELETE FROM tbl_votantes WHERE nombre_completo LIKE 'Votante%';

-- Insertar votantes distribuidos por todos los departamentos
-- Departamento 05 - Antioquia (40 votantes)
INSERT INTO tbl_votantes (nombre_completo, ideologia, rango_edad, nivel_ingresos, genero, codigo_departamento, codigo_municipio, nivel_educacion, ocupacion, estado, dtcreate)
SELECT
    CONCAT(
        SUBSTRING_INDEX(SUBSTRING_INDEX(IF(n % 2 = 0, @nombres_masculinos, @nombres_femeninos), ',', (n % 20) + 1), ',', -1),
        ' ',
        SUBSTRING_INDEX(SUBSTRING_INDEX(@apellidos, ',', ((n * 3) % 70) + 1), ',', -1),
        ' ',
        SUBSTRING_INDEX(SUBSTRING_INDEX(@apellidos, ',', ((n * 7) % 70) + 1), ',', -1)
    ) as nombre_completo,
    CASE (n % 7)
        WHEN 0 THEN 'izquierda'
        WHEN 1 THEN 'centro_izquierda'
        WHEN 2 THEN 'centro'
        WHEN 3 THEN 'centro_derecha'
        WHEN 4 THEN 'derecha'
        WHEN 5 THEN 'independiente'
        ELSE 'sin_definir'
    END as ideologia,
    CASE (n % 6)
        WHEN 0 THEN '18-25'
        WHEN 1 THEN '26-35'
        WHEN 2 THEN '36-45'
        WHEN 3 THEN '46-55'
        WHEN 4 THEN '56-65'
        ELSE '66+'
    END as rango_edad,
    CASE (n % 5)
        WHEN 0 THEN 'menos_1_salario'
        WHEN 1 THEN '1-2_salarios'
        WHEN 2 THEN '3-5_salarios'
        WHEN 3 THEN '6-10_salarios'
        ELSE 'mas_10_salarios'
    END as nivel_ingresos,
    IF(n % 2 = 0, 'masculino', 'femenino') as genero,
    '05' as codigo_departamento,
    (SELECT codigo_muncipio FROM tbl_ciudades_accion_unificada WHERE codigo_departamento = '05' ORDER BY RAND() LIMIT 1) as codigo_municipio,
    CASE (n % 9)
        WHEN 0 THEN 'primaria_incompleta'
        WHEN 1 THEN 'primaria_completa'
        WHEN 2 THEN 'secundaria_incompleta'
        WHEN 3 THEN 'secundaria_completa'
        WHEN 4 THEN 'tecnico'
        WHEN 5 THEN 'tecnologo'
        WHEN 6 THEN 'universitario_incompleto'
        WHEN 7 THEN 'universitario_completo'
        ELSE 'posgrado'
    END as nivel_educacion,
    'Ciudadano' as ocupacion,
    'activo' as estado,
    NOW() as dtcreate
FROM (
    SELECT (@row := @row + 1) as n
    FROM (SELECT 0 UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t1,
         (SELECT 0 UNION SELECT 1 UNION SELECT 2 UNION SELECT 3) t2,
         (SELECT @row := 0) r
    LIMIT 40
) numbers;

-- Departamento 11 - Bogotá (50 votantes)
INSERT INTO tbl_votantes (nombre_completo, ideologia, rango_edad, nivel_ingresos, genero, codigo_departamento, codigo_municipio, nivel_educacion, ocupacion, estado, dtcreate)
SELECT
    CONCAT(
        SUBSTRING_INDEX(SUBSTRING_INDEX(IF((n + 100) % 2 = 0, @nombres_masculinos, @nombres_femeninos), ',', ((n + 100) % 20) + 1), ',', -1),
        ' ',
        SUBSTRING_INDEX(SUBSTRING_INDEX(@apellidos, ',', (((n + 100) * 3) % 70) + 1), ',', -1),
        ' ',
        SUBSTRING_INDEX(SUBSTRING_INDEX(@apellidos, ',', (((n + 100) * 7) % 70) + 1), ',', -1)
    ),
    CASE ((n + 100) % 7)
        WHEN 0 THEN 'izquierda'
        WHEN 1 THEN 'centro_izquierda'
        WHEN 2 THEN 'centro'
        WHEN 3 THEN 'centro_derecha'
        WHEN 4 THEN 'derecha'
        WHEN 5 THEN 'independiente'
        ELSE 'sin_definir'
    END,
    CASE ((n + 100) % 6)
        WHEN 0 THEN '18-25'
        WHEN 1 THEN '26-35'
        WHEN 2 THEN '36-45'
        WHEN 3 THEN '46-55'
        WHEN 4 THEN '56-65'
        ELSE '66+'
    END,
    CASE ((n + 100) % 5)
        WHEN 0 THEN 'menos_1_salario'
        WHEN 1 THEN '1-2_salarios'
        WHEN 2 THEN '3-5_salarios'
        WHEN 3 THEN '6-10_salarios'
        ELSE 'mas_10_salarios'
    END,
    IF((n + 100) % 2 = 0, 'masculino', 'femenino'),
    '11',
    '11001',
    CASE ((n + 100) % 9)
        WHEN 0 THEN 'primaria_incompleta'
        WHEN 1 THEN 'primaria_completa'
        WHEN 2 THEN 'secundaria_incompleta'
        WHEN 3 THEN 'secundaria_completa'
        WHEN 4 THEN 'tecnico'
        WHEN 5 THEN 'tecnologo'
        WHEN 6 THEN 'universitario_incompleto'
        WHEN 7 THEN 'universitario_completo'
        ELSE 'posgrado'
    END,
    'Ciudadano',
    'activo',
    NOW()
FROM (
    SELECT (@row2 := @row2 + 1) as n
    FROM (SELECT 0 UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t1,
         (SELECT 0 UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4) t2,
         (SELECT @row2 := 0) r
    LIMIT 50
) numbers;

-- Departamento 76 - Valle del Cauca (35 votantes)
INSERT INTO tbl_votantes (nombre_completo, ideologia, rango_edad, nivel_ingresos, genero, codigo_departamento, codigo_municipio, nivel_educacion, ocupacion, estado, dtcreate)
SELECT
    CONCAT(
        SUBSTRING_INDEX(SUBSTRING_INDEX(IF((n + 200) % 2 = 0, @nombres_masculinos, @nombres_femeninos), ',', ((n + 200) % 20) + 1), ',', -1),
        ' ',
        SUBSTRING_INDEX(SUBSTRING_INDEX(@apellidos, ',', (((n + 200) * 3) % 70) + 1), ',', -1),
        ' ',
        SUBSTRING_INDEX(SUBSTRING_INDEX(@apellidos, ',', (((n + 200) * 7) % 70) + 1), ',', -1)
    ),
    CASE ((n + 200) % 7)
        WHEN 0 THEN 'izquierda'
        WHEN 1 THEN 'centro_izquierda'
        WHEN 2 THEN 'centro'
        WHEN 3 THEN 'centro_derecha'
        WHEN 4 THEN 'derecha'
        WHEN 5 THEN 'independiente'
        ELSE 'sin_definir'
    END,
    CASE ((n + 200) % 6)
        WHEN 0 THEN '18-25'
        WHEN 1 THEN '26-35'
        WHEN 2 THEN '36-45'
        WHEN 3 THEN '46-55'
        WHEN 4 THEN '56-65'
        ELSE '66+'
    END,
    CASE ((n + 200) % 5)
        WHEN 0 THEN 'menos_1_salario'
        WHEN 1 THEN '1-2_salarios'
        WHEN 2 THEN '3-5_salarios'
        WHEN 3 THEN '6-10_salarios'
        ELSE 'mas_10_salarios'
    END,
    IF((n + 200) % 2 = 0, 'masculino', 'femenino'),
    '76',
    (SELECT codigo_muncipio FROM tbl_ciudades_accion_unificada WHERE codigo_departamento = '76' ORDER BY RAND() LIMIT 1),
    CASE ((n + 200) % 9)
        WHEN 0 THEN 'primaria_incompleta'
        WHEN 1 THEN 'primaria_completa'
        WHEN 2 THEN 'secundaria_incompleta'
        WHEN 3 THEN 'secundaria_completa'
        WHEN 4 THEN 'tecnico'
        WHEN 5 THEN 'tecnologo'
        WHEN 6 THEN 'universitario_incompleto'
        WHEN 7 THEN 'universitario_completo'
        ELSE 'posgrado'
    END,
    'Ciudadano',
    'activo',
    NOW()
FROM (
    SELECT (@row3 := @row3 + 1) as n
    FROM (SELECT 0 UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t1,
         (SELECT 0 UNION SELECT 1 UNION SELECT 2 UNION SELECT 3) t2,
         (SELECT @row3 := 0) r
    LIMIT 35
) numbers;

-- Departamento 68 - Santander (30 votantes)
INSERT INTO tbl_votantes (nombre_completo, ideologia, rango_edad, nivel_ingresos, genero, codigo_departamento, codigo_municipio, nivel_educacion, ocupacion, estado, dtcreate)
SELECT
    CONCAT(
        SUBSTRING_INDEX(SUBSTRING_INDEX(IF((n + 300) % 2 = 0, @nombres_masculinos, @nombres_femeninos), ',', ((n + 300) % 20) + 1), ',', -1),
        ' ',
        SUBSTRING_INDEX(SUBSTRING_INDEX(@apellidos, ',', (((n + 300) * 3) % 70) + 1), ',', -1),
        ' ',
        SUBSTRING_INDEX(SUBSTRING_INDEX(@apellidos, ',', (((n + 300) * 7) % 70) + 1), ',', -1)
    ),
    CASE ((n + 300) % 7)
        WHEN 0 THEN 'izquierda'
        WHEN 1 THEN 'centro_izquierda'
        WHEN 2 THEN 'centro'
        WHEN 3 THEN 'centro_derecha'
        WHEN 4 THEN 'derecha'
        WHEN 5 THEN 'independiente'
        ELSE 'sin_definir'
    END,
    CASE ((n + 300) % 6)
        WHEN 0 THEN '18-25'
        WHEN 1 THEN '26-35'
        WHEN 2 THEN '36-45'
        WHEN 3 THEN '46-55'
        WHEN 4 THEN '56-65'
        ELSE '66+'
    END,
    CASE ((n + 300) % 5)
        WHEN 0 THEN 'menos_1_salario'
        WHEN 1 THEN '1-2_salarios'
        WHEN 2 THEN '3-5_salarios'
        WHEN 3 THEN '6-10_salarios'
        ELSE 'mas_10_salarios'
    END,
    IF((n + 300) % 2 = 0, 'masculino', 'femenino'),
    '68',
    (SELECT codigo_muncipio FROM tbl_ciudades_accion_unificada WHERE codigo_departamento = '68' ORDER BY RAND() LIMIT 1),
    CASE ((n + 300) % 9)
        WHEN 0 THEN 'primaria_incompleta'
        WHEN 1 THEN 'primaria_completa'
        WHEN 2 THEN 'secundaria_incompleta'
        WHEN 3 THEN 'secundaria_completa'
        WHEN 4 THEN 'tecnico'
        WHEN 5 THEN 'tecnologo'
        WHEN 6 THEN 'universitario_incompleto'
        WHEN 7 THEN 'universitario_completo'
        ELSE 'posgrado'
    END,
    'Ciudadano',
    'activo',
    NOW()
FROM (
    SELECT (@row4 := @row4 + 1) as n
    FROM (SELECT 0 UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t1,
         (SELECT 0 UNION SELECT 1 UNION SELECT 2) t2,
         (SELECT @row4 := 0) r
    LIMIT 30
) numbers;

-- Resto de departamentos (25 votantes cada uno)
-- 08 - Atlántico
INSERT INTO tbl_votantes (nombre_completo, ideologia, rango_edad, nivel_ingresos, genero, codigo_departamento, codigo_municipio, nivel_educacion, ocupacion, estado, dtcreate)
SELECT
    CONCAT(
        SUBSTRING_INDEX(SUBSTRING_INDEX(IF((n + 400) % 2 = 0, @nombres_masculinos, @nombres_femeninos), ',', ((n + 400) % 20) + 1), ',', -1),
        ' ',
        SUBSTRING_INDEX(SUBSTRING_INDEX(@apellidos, ',', (((n + 400) * 3) % 70) + 1), ',', -1)
    ),
    CASE ((n + 400) % 7)
        WHEN 0 THEN 'izquierda' WHEN 1 THEN 'centro_izquierda' WHEN 2 THEN 'centro'
        WHEN 3 THEN 'centro_derecha' WHEN 4 THEN 'derecha' WHEN 5 THEN 'independiente'
        ELSE 'sin_definir'
    END,
    CASE ((n + 400) % 6)
        WHEN 0 THEN '18-25' WHEN 1 THEN '26-35' WHEN 2 THEN '36-45'
        WHEN 3 THEN '46-55' WHEN 4 THEN '56-65' ELSE '66+'
    END,
    CASE ((n + 400) % 5)
        WHEN 0 THEN 'menos_1_salario' WHEN 1 THEN '1-2_salarios' WHEN 2 THEN '3-5_salarios'
        WHEN 3 THEN '6-10_salarios' ELSE 'mas_10_salarios'
    END,
    IF((n + 400) % 3 = 0, 'masculino', IF((n + 400) % 3 = 1, 'femenino', 'otro')),
    '08',
    (SELECT codigo_muncipio FROM tbl_ciudades_accion_unificada WHERE codigo_departamento = '08' ORDER BY RAND() LIMIT 1),
    CASE ((n + 400) % 9)
        WHEN 0 THEN 'primaria_completa' WHEN 1 THEN 'secundaria_completa'
        WHEN 2 THEN 'tecnico' WHEN 3 THEN 'tecnologo'
        ELSE 'universitario_completo'
    END,
    'Ciudadano', 'activo', NOW()
FROM (
    SELECT (@row5 := @row5 + 1) as n
    FROM (SELECT 0 UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t1,
         (SELECT 0 UNION SELECT 1 UNION SELECT 2) t2,
         (SELECT @row5 := 0) r
    LIMIT 25
) numbers;

-- Crear votantes para los departamentos restantes (adaptado de forma similar)
-- 13 - Bolívar, 15 - Boyacá, 17 - Caldas, 19 - Cauca, 20 - Cesar, 25 - Cundinamarca
-- 41 - Huila, 47 - Magdalena, 50 - Meta, 52 - Nariño, 54 - N. Santander
-- 63 - Quindío, 66 - Risaralda, 70 - Sucre, 73 - Tolima, 86 - Putumayo

-- Insertar 15 votantes por cada uno de los departamentos restantes
INSERT INTO tbl_votantes (nombre_completo, ideologia, rango_edad, nivel_ingresos, genero, codigo_departamento, codigo_municipio, nivel_educacion, ocupacion, estado, dtcreate)
SELECT
    CONCAT(
        SUBSTRING_INDEX(SUBSTRING_INDEX(IF(combo.n % 2 = 0, @nombres_masculinos, @nombres_femeninos), ',', (combo.n % 20) + 1), ',', -1),
        ' ',
        SUBSTRING_INDEX(SUBSTRING_INDEX(@apellidos, ',', ((combo.n * 3 + combo.dept_offset) % 70) + 1), ',', -1)
    ),
    CASE (combo.n % 7)
        WHEN 0 THEN 'izquierda' WHEN 1 THEN 'centro_izquierda' WHEN 2 THEN 'centro'
        WHEN 3 THEN 'centro_derecha' WHEN 4 THEN 'derecha' WHEN 5 THEN 'independiente'
        ELSE 'sin_definir'
    END,
    CASE (combo.n % 6)
        WHEN 0 THEN '18-25' WHEN 1 THEN '26-35' WHEN 2 THEN '36-45'
        WHEN 3 THEN '46-55' WHEN 4 THEN '56-65' ELSE '66+'
    END,
    CASE (combo.n % 5)
        WHEN 0 THEN 'menos_1_salario' WHEN 1 THEN '1-2_salarios' WHEN 2 THEN '3-5_salarios'
        WHEN 3 THEN '6-10_salarios' ELSE 'mas_10_salarios'
    END,
    IF(combo.n % 2 = 0, 'masculino', 'femenino'),
    combo.codigo_dep,
    (SELECT codigo_muncipio FROM tbl_ciudades_accion_unificada WHERE codigo_departamento = combo.codigo_dep ORDER BY RAND() LIMIT 1),
    CASE (combo.n % 9)
        WHEN 0 THEN 'primaria_incompleta' WHEN 1 THEN 'primaria_completa'
        WHEN 2 THEN 'secundaria_incompleta' WHEN 3 THEN 'secundaria_completa'
        WHEN 4 THEN 'tecnico' WHEN 5 THEN 'tecnologo'
        WHEN 6 THEN 'universitario_incompleto' WHEN 7 THEN 'universitario_completo'
        ELSE 'posgrado'
    END,
    'Ciudadano', 'activo', NOW()
FROM (
    SELECT n, codigo_dep,
           CASE codigo_dep
               WHEN '13' THEN 1000
               WHEN '15' THEN 2000
               WHEN '17' THEN 3000
               WHEN '19' THEN 4000
               WHEN '20' THEN 5000
               WHEN '25' THEN 6000
               WHEN '41' THEN 7000
               WHEN '47' THEN 8000
               WHEN '50' THEN 9000
               WHEN '52' THEN 10000
               WHEN '54' THEN 11000
               WHEN '63' THEN 12000
               WHEN '66' THEN 13000
               WHEN '70' THEN 14000
               WHEN '73' THEN 15000
               WHEN '86' THEN 16000
           END as dept_offset
    FROM (
        SELECT (@row6 := @row6 + 1) as n
        FROM (SELECT 0 UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t1,
             (SELECT 0 UNION SELECT 1) t2,
             (SELECT @row6 := 0) r
        LIMIT 15
    ) nums
    CROSS JOIN (
        SELECT '13' as codigo_dep UNION SELECT '15' UNION SELECT '17' UNION SELECT '19'
        UNION SELECT '20' UNION SELECT '25' UNION SELECT '41' UNION SELECT '47'
        UNION SELECT '50' UNION SELECT '52' UNION SELECT '54' UNION SELECT '63'
        UNION SELECT '66' UNION SELECT '70' UNION SELECT '73' UNION SELECT '86'
    ) depts
) combo;

-- Verificar la distribución de votantes por departamento
SELECT
    d.codigo_departamento,
    d.departamento,
    COUNT(v.id) as total_votantes
FROM tbl_departamentos d
LEFT JOIN tbl_votantes v ON d.codigo_departamento = v.codigo_departamento
WHERE d.habilitado = 'si'
GROUP BY d.codigo_departamento, d.departamento
ORDER BY d.codigo_departamento;
