SET @nulos = (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tbl_votantes'
    AND COLUMN_NAME IN ('rango_edad', 'genero', 'ideologia') AND IS_NULLABLE = 'NO'
);
SET @ddl_votantes = IF(@nulos > 0,
  "ALTER TABLE tbl_votantes
     MODIFY COLUMN ideologia ENUM('izquierda','centro_izquierda','centro','centro_derecha','derecha','independiente','sin_definir') NULL DEFAULT NULL,
     MODIFY COLUMN rango_edad ENUM('18-25','26-35','36-45','46-55','56-65','66+') NULL DEFAULT NULL,
     MODIFY COLUMN genero ENUM('masculino','femenino','otro','prefiero_no_decir') NULL DEFAULT NULL",
  'DO 0');
PREPARE stmt_votantes FROM @ddl_votantes;
EXECUTE stmt_votantes;
DEALLOCATE PREPARE stmt_votantes;

INSERT INTO tbl_migraciones_ejecutadas (nombre_migracion, dtejecutada)
SELECT 'fase-i-012-votantes-datos-opcionales', NOW()
WHERE NOT EXISTS (
  SELECT 1 FROM tbl_migraciones_ejecutadas
  WHERE nombre_migracion = 'fase-i-012-votantes-datos-opcionales'
);
