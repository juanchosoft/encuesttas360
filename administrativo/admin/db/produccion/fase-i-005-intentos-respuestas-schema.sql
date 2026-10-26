SET @col_resp_fila = (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tbl_cuestionario_respuestas' AND COLUMN_NAME = 'tbl_grilla_fila_id'
);
SET @ddl_resp_1 = IF(@col_resp_fila = 0,
  "ALTER TABLE tbl_cuestionario_respuestas
     ADD COLUMN tbl_grilla_fila_id INT NULL,
     ADD COLUMN tbl_grilla_columna_id INT NULL,
     ADD COLUMN tbl_grilla_columna_opcion_id INT NULL,
     ADD COLUMN valor_numerico DECIMAL(10,2) NULL,
     ADD COLUMN texto_snapshot TEXT NULL,
     ADD KEY idx_resp_preg_fila_col (tbl_pregunta_id, tbl_grilla_fila_id, tbl_grilla_columna_id),
     ADD KEY idx_resp_intento_preg (tbl_intento_id, tbl_pregunta_id)",
  'DO 0'
);
PREPARE stmt_resp_1 FROM @ddl_resp_1;
EXECUTE stmt_resp_1;
DEALLOCATE PREPARE stmt_resp_1;

SET @col_int_estado = (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tbl_cuestionario_intentos' AND COLUMN_NAME = 'estado'
);
SET @ddl_int_1 = IF(@col_int_estado = 0,
  "ALTER TABLE tbl_cuestionario_intentos
     ADD COLUMN estado ENUM('completo','terminado_filtro','pendiente_audio') NOT NULL DEFAULT 'completo',
     ADD COLUMN terminado_en_pregunta_id INT NULL,
     ADD COLUMN terminado_opcion_id INT NULL,
     ADD COLUMN valido_para_pago TINYINT(1) NOT NULL DEFAULT 1,
     ADD COLUMN origen ENUM('interno','publico','contestar') NOT NULL DEFAULT 'interno',
     ADD COLUMN codigo_departamento VARCHAR(10) NULL,
     ADD COLUMN codigo_municipio VARCHAR(10) NULL,
     ADD COLUMN tbl_zona_muestreo_id INT NULL,
     ADD COLUMN zona_texto VARCHAR(150) NULL,
     ADD COLUMN tbl_usuario_encuestador_id INT UNSIGNED NULL,
     ADD COLUMN client_uuid CHAR(36) NULL,
     ADD COLUMN motor_version VARCHAR(32) NULL,
     ADD COLUMN definicion_hash VARCHAR(64) NULL,
     ADD COLUMN es_prueba TINYINT(1) NOT NULL DEFAULT 0,
     ADD UNIQUE KEY idx_uniq_client_uuid (client_uuid),
     ADD KEY idx_int_ficha_estado_prueba (tbl_ficha_tecnica_encuesta_id, estado, es_prueba),
     ADD KEY idx_int_muni_zona (codigo_municipio, tbl_zona_muestreo_id)",
  'DO 0'
);
PREPARE stmt_int_1 FROM @ddl_int_1;
EXECUTE stmt_int_1;
DEALLOCATE PREPARE stmt_int_1;

UPDATE tbl_cuestionario_intentos i
INNER JOIN tbl_votantes v ON v.id = i.tbl_votante_id
SET
  i.codigo_departamento = COALESCE(i.codigo_departamento, v.codigo_departamento),
  i.codigo_municipio = COALESCE(i.codigo_municipio, v.codigo_municipio),
  i.zona_texto = COALESCE(i.zona_texto, v.comuna),
  i.tbl_usuario_encuestador_id = COALESCE(i.tbl_usuario_encuestador_id, NULLIF(v.tbl_usuario_id, 0))
WHERE i.codigo_municipio IS NULL OR i.codigo_departamento IS NULL;

UPDATE tbl_cuestionario_intentos i
INNER JOIN tbl_votantes v ON v.id = i.tbl_votante_id
SET i.origen = 'publico', i.tbl_usuario_encuestador_id = NULL
WHERE COALESCE(v.tbl_usuario_id, 0) = 0 AND i.origen = 'interno' AND i.client_uuid IS NULL;

INSERT INTO tbl_migraciones_ejecutadas (nombre_migracion, dtejecutada)
SELECT 'fase-i-005-intentos-respuestas-schema', NOW()
WHERE NOT EXISTS (
  SELECT 1 FROM tbl_migraciones_ejecutadas
  WHERE nombre_migracion = 'fase-i-005-intentos-respuestas-schema'
);
