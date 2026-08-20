-- A TODAS LAS FILAS DE LA TABLA.

-- Reemplaza 'tbl_ciudades_accion_unificada' con el nombre real de tu tabla si es diferente.

-- PASO 1: Normalización y Limpieza (Preparación)
-- ----------------------------------------------------------------------
-- 1. Convierte todo el campo 'municipio' a MINÚSCULAS.
-- 2. Usa TRIM() para quitar cualquier espacio en blanco al inicio o al final.
UPDATE tbl_ciudades_accion_unificada
SET municipio = TRIM(LOWER(municipio));


UPDATE tbl_ciudades_accion_unificada
SET municipio = REPLACE(municipio, '-', ' ');


-- 2. CAPITALIZACIÓN DE LA PRIMERA LETRA
-- Toma el primer carácter, lo pone en mayúscula (UPPER), y lo concatena con
-- el resto de la cadena (SUBSTRING desde el carácter 2).
UPDATE tbl_ciudades_accion_unificada
SET municipio = CONCAT(
    UPPER(SUBSTRING(municipio, 1, 1)),
    SUBSTRING(municipio, 2)
)
WHERE municipio IS NOT NULL AND municipio != '';

-- Resultado después del paso 2:
-- 'santa fe de antioquia' -> 'Santa fe de antioquia'
-- 'medellin' -> 'Medellin'
