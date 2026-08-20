-- Script para poblar tbl_respuestas_sondeos con datos simulados
-- Asegurarse de que existan votantes y sondeos activos

-- Paso 1: Crear votantes simulados si no existen (con datos demográficos variados)
INSERT INTO tbl_votantes (nombre, apellido, cedula, genero, ideologia, rango_edad, nivel_ingresos, nivel_educacion, dtcreate)
SELECT
    CONCAT('Votante', n) as nombre,
    CONCAT('Apellido', n) as apellido,
    CONCAT('1000000', n) as cedula,
    CASE (n % 3)
        WHEN 0 THEN 'Masculino'
        WHEN 1 THEN 'Femenino'
        ELSE 'Otro'
    END as genero,
    CASE (n % 7)
        WHEN 0 THEN 'Centro'
        WHEN 1 THEN 'Derecha'
        WHEN 2 THEN 'Izquierda'
        WHEN 3 THEN 'Centro-Derecha'
        WHEN 4 THEN 'Centro-Izquierda'
        WHEN 5 THEN 'Independiente'
        ELSE 'Sin Definir'
    END as ideologia,
    CASE (n % 5)
        WHEN 0 THEN '18-25 años'
        WHEN 1 THEN '26-35 años'
        WHEN 2 THEN '36-45 años'
        WHEN 3 THEN '46-55 años'
        ELSE '56-65 años'
    END as rango_edad,
    CASE (n % 5)
        WHEN 0 THEN 'Menos de 1 salario'
        WHEN 1 THEN '1-2 salarios'
        WHEN 2 THEN '3-5 salarios'
        WHEN 3 THEN '6-10 salarios'
        ELSE 'Más de 10 salarios'
    END as nivel_ingresos,
    CASE (n % 7)
        WHEN 0 THEN 'Primaria Completa'
        WHEN 1 THEN 'Secundaria Incompleta'
        WHEN 2 THEN 'Secundaria Completa'
        WHEN 3 THEN 'Técnico'
        WHEN 4 THEN 'Tecnólogo'
        WHEN 5 THEN 'Universitario Completo'
        ELSE 'Posgrado'
    END as nivel_educacion,
    NOW() as dtcreate
FROM (
    SELECT 1 as n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION
    SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10 UNION
    SELECT 11 UNION SELECT 12 UNION SELECT 13 UNION SELECT 14 UNION SELECT 15 UNION
    SELECT 16 UNION SELECT 17 UNION SELECT 18 UNION SELECT 19 UNION SELECT 20 UNION
    SELECT 21 UNION SELECT 22 UNION SELECT 23 UNION SELECT 24 UNION SELECT 25 UNION
    SELECT 26 UNION SELECT 27 UNION SELECT 28 UNION SELECT 29 UNION SELECT 30 UNION
    SELECT 31 UNION SELECT 32 UNION SELECT 33 UNION SELECT 34 UNION SELECT 35 UNION
    SELECT 36 UNION SELECT 37 UNION SELECT 38 UNION SELECT 39 UNION SELECT 40 UNION
    SELECT 41 UNION SELECT 42 UNION SELECT 43 UNION SELECT 44 UNION SELECT 45 UNION
    SELECT 46 UNION SELECT 47 UNION SELECT 48 UNION SELECT 49 UNION SELECT 50 UNION
    SELECT 51 UNION SELECT 52 UNION SELECT 53 UNION SELECT 54 UNION SELECT 55 UNION
    SELECT 56 UNION SELECT 57 UNION SELECT 58 UNION SELECT 59 UNION SELECT 60 UNION
    SELECT 61 UNION SELECT 62 UNION SELECT 63 UNION SELECT 64 UNION SELECT 65 UNION
    SELECT 66 UNION SELECT 67 UNION SELECT 68 UNION SELECT 69 UNION SELECT 70 UNION
    SELECT 71 UNION SELECT 72 UNION SELECT 73 UNION SELECT 74 UNION SELECT 75 UNION
    SELECT 76 UNION SELECT 77 UNION SELECT 78 UNION SELECT 79 UNION SELECT 80 UNION
    SELECT 81 UNION SELECT 82 UNION SELECT 83 UNION SELECT 84 UNION SELECT 85 UNION
    SELECT 86 UNION SELECT 87 UNION SELECT 88 UNION SELECT 89 UNION SELECT 90 UNION
    SELECT 91 UNION SELECT 92 UNION SELECT 93 UNION SELECT 94 UNION SELECT 95 UNION
    SELECT 96 UNION SELECT 97 UNION SELECT 98 UNION SELECT 99 UNION SELECT 100
) AS numbers
WHERE NOT EXISTS (
    SELECT 1 FROM tbl_votantes WHERE cedula = CONCAT('1000000', n)
);

-- Paso 2: Insertar respuestas para sondeos con OPCIONES (tipo dicotómica, selección múltiple, etc.)
-- Esto tomará los sondeos activos que NO son para cargos públicos
INSERT INTO tbl_respuestas_sondeos (tbl_sondeo_id, tbl_votante_id, tbl_sondeo_x_opciones_id, dtcreate)
SELECT
    s.id as tbl_sondeo_id,
    v.id as tbl_votante_id,
    (
        -- Seleccionar una opción aleatoria del sondeo
        SELECT so.id
        FROM tbl_sondeo_x_opciones so
        WHERE so.tbl_sondeo_id = s.id
        ORDER BY RAND()
        LIMIT 1
    ) as tbl_sondeo_x_opciones_id,
    DATE_ADD(s.fecha_inicio, INTERVAL FLOOR(RAND() * DATEDIFF(COALESCE(s.fecha_fin, NOW()), s.fecha_inicio)) DAY) as dtcreate
FROM tbl_sondeo s
CROSS JOIN (
    SELECT id FROM tbl_votantes ORDER BY RAND() LIMIT 50
) v
WHERE s.habilitado = 'si'
  AND s.aplica_cargos_publicos = 'no'
  AND s.tipo_sondeo != 'No Aplica'
  AND EXISTS (SELECT 1 FROM tbl_sondeo_x_opciones WHERE tbl_sondeo_id = s.id)
  AND NOT EXISTS (
      SELECT 1 FROM tbl_respuestas_sondeos rs
      WHERE rs.tbl_sondeo_id = s.id AND rs.tbl_votante_id = v.id
  );

-- Paso 3: Insertar respuestas para sondeos con CANDIDATOS (para cargos públicos)
INSERT INTO tbl_respuestas_sondeos (tbl_sondeo_id, tbl_votante_id, tbl_candidato_id, dtcreate)
SELECT
    s.id as tbl_sondeo_id,
    v.id as tbl_votante_id,
    (
        -- Seleccionar un candidato aleatorio del sondeo
        SELECT sxp.tbl_participante_id
        FROM tbl_sondeo_x_tbl_participantes sxp
        WHERE sxp.tbl_sondeo_id = s.id
        ORDER BY RAND()
        LIMIT 1
    ) as tbl_candidato_id,
    DATE_ADD(s.fecha_inicio, INTERVAL FLOOR(RAND() * DATEDIFF(COALESCE(s.fecha_fin, NOW()), s.fecha_inicio)) DAY) as dtcreate
FROM tbl_sondeo s
CROSS JOIN (
    SELECT id FROM tbl_votantes ORDER BY RAND() LIMIT 60
) v
WHERE s.habilitado = 'si'
  AND s.aplica_cargos_publicos = 'si'
  AND EXISTS (SELECT 1 FROM tbl_sondeo_x_tbl_participantes WHERE tbl_sondeo_id = s.id)
  AND NOT EXISTS (
      SELECT 1 FROM tbl_respuestas_sondeos rs
      WHERE rs.tbl_sondeo_id = s.id AND rs.tbl_votante_id = v.id
  );

-- Paso 4: Agregar más respuestas para simular popularidad desigual entre opciones/candidatos
-- Esto hace que algunas opciones tengan más votos que otras (más realista)

-- Para sondeos con opciones: agregar votos extra a las primeras 2 opciones
INSERT INTO tbl_respuestas_sondeos (tbl_sondeo_id, tbl_votante_id, tbl_sondeo_x_opciones_id, dtcreate)
SELECT
    s.id as tbl_sondeo_id,
    v.id as tbl_votante_id,
    (
        -- Seleccionar una de las 2 primeras opciones (más populares)
        SELECT so.id
        FROM tbl_sondeo_x_opciones so
        WHERE so.tbl_sondeo_id = s.id
        ORDER BY so.id ASC
        LIMIT 1 OFFSET FLOOR(RAND() * 2)
    ) as tbl_sondeo_x_opciones_id,
    DATE_ADD(s.fecha_inicio, INTERVAL FLOOR(RAND() * DATEDIFF(COALESCE(s.fecha_fin, NOW()), s.fecha_inicio)) DAY) as dtcreate
FROM tbl_sondeo s
CROSS JOIN (
    SELECT id FROM tbl_votantes ORDER BY RAND() LIMIT 30
) v
WHERE s.habilitado = 'si'
  AND s.aplica_cargos_publicos = 'no'
  AND s.tipo_sondeo != 'No Aplica'
  AND EXISTS (SELECT 1 FROM tbl_sondeo_x_opciones WHERE tbl_sondeo_id = s.id LIMIT 2)
  AND NOT EXISTS (
      SELECT 1 FROM tbl_respuestas_sondeos rs
      WHERE rs.tbl_sondeo_id = s.id AND rs.tbl_votante_id = v.id
  );

-- Para sondeos con candidatos: agregar votos extra a los primeros 2 candidatos
INSERT INTO tbl_respuestas_sondeos (tbl_sondeo_id, tbl_votante_id, tbl_candidato_id, dtcreate)
SELECT
    s.id as tbl_sondeo_id,
    v.id as tbl_votante_id,
    (
        -- Seleccionar uno de los 2 primeros candidatos (más populares)
        SELECT sxp.tbl_participante_id
        FROM tbl_sondeo_x_tbl_participantes sxp
        WHERE sxp.tbl_sondeo_id = s.id
        ORDER BY sxp.tbl_participante_id ASC
        LIMIT 1 OFFSET FLOOR(RAND() * 2)
    ) as tbl_candidato_id,
    DATE_ADD(s.fecha_inicio, INTERVAL FLOOR(RAND() * DATEDIFF(COALESCE(s.fecha_fin, NOW()), s.fecha_inicio)) DAY) as dtcreate
FROM tbl_sondeo s
CROSS JOIN (
    SELECT id FROM tbl_votantes ORDER BY RAND() LIMIT 40
) v
WHERE s.habilitado = 'si'
  AND s.aplica_cargos_publicos = 'si'
  AND EXISTS (
      SELECT 1 FROM tbl_sondeo_x_tbl_participantes
      WHERE tbl_sondeo_id = s.id
      LIMIT 2
  )
  AND NOT EXISTS (
      SELECT 1 FROM tbl_respuestas_sondeos rs
      WHERE rs.tbl_sondeo_id = s.id AND rs.tbl_votante_id = v.id
  );

-- Verificación: Mostrar resumen de respuestas insertadas
SELECT
    s.id,
    s.sondeo,
    s.tipo_sondeo,
    s.aplica_cargos_publicos,
    COUNT(rs.id) as total_respuestas,
    COUNT(DISTINCT rs.tbl_votante_id) as votantes_unicos
FROM tbl_sondeo s
LEFT JOIN tbl_respuestas_sondeos rs ON s.id = rs.tbl_sondeo_id
WHERE s.habilitado = 'si'
GROUP BY s.id
ORDER BY s.id DESC;
