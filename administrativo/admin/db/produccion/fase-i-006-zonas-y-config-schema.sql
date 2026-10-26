CREATE TABLE IF NOT EXISTS tbl_zonas_muestreo (
  id INT NOT NULL AUTO_INCREMENT,
  tbl_ficha_tecnica_encuesta_id INT NOT NULL,
  codigo_departamento VARCHAR(10) NULL,
  codigo_municipio VARCHAR(10) NOT NULL,
  tipo ENUM('comuna','sector','corregimiento','zona') NOT NULL DEFAULT 'comuna',
  codigo VARCHAR(50) NULL,
  nombre VARCHAR(255) NOT NULL,
  cuota INT NOT NULL DEFAULT 0,
  orden INT NOT NULL DEFAULT 1,
  habilitado ENUM('si','no') NOT NULL DEFAULT 'si',
  eliminado TINYINT(1) NOT NULL DEFAULT 0,
  dtcreate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  dtupdate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_zona_ficha_muni (tbl_ficha_tecnica_encuesta_id, codigo_municipio, eliminado, orden)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET @col_ficha_cfg = (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tbl_ficha_tecnica_encuestas' AND COLUMN_NAME = 'pedir_municipio_zona_inicio'
);
SET @ddl_ficha_cfg = IF(@col_ficha_cfg = 0,
  "ALTER TABLE tbl_ficha_tecnica_encuestas
     ADD COLUMN pedir_municipio_zona_inicio TINYINT(1) NOT NULL DEFAULT 1,
     ADD COLUMN audio_obligatorio TINYINT(1) NOT NULL DEFAULT 1,
     ADD COLUMN certificar_terminadas_filtro TINYINT(1) NOT NULL DEFAULT 1,
     ADD COLUMN mostrar_resultados_publicos TINYINT(1) NOT NULL DEFAULT 1,
     ADD COLUMN bloquear_cuota_completa TINYINT(1) NOT NULL DEFAULT 0,
     ADD COLUMN mensaje_cierre_defecto TEXT NULL",
  'DO 0'
);
PREPARE stmt_ficha_cfg FROM @ddl_ficha_cfg;
EXECUTE stmt_ficha_cfg;
DEALLOCATE PREPARE stmt_ficha_cfg;

SET @col_preg_cruce = (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tbl_preguntas' AND COLUMN_NAME = 'es_variable_cruce'
);
SET @ddl_preg_cruce = IF(@col_preg_cruce = 0,
  "ALTER TABLE tbl_preguntas
     ADD COLUMN es_variable_cruce TINYINT(1) NOT NULL DEFAULT 0,
     ADD COLUMN etiqueta_cruce VARCHAR(120) NULL,
     ADD COLUMN copiar_a_votante_campo VARCHAR(64) NULL,
     ADD COLUMN rol_resultado ENUM('ninguno','conocimiento','imagen','intencion','ideologia','asociacion_positiva','asociacion_negativa','escala','otro') NOT NULL DEFAULT 'ninguno'",
  'DO 0'
);
PREPARE stmt_preg_cruce FROM @ddl_preg_cruce;
EXECUTE stmt_preg_cruce;
DEALLOCATE PREPARE stmt_preg_cruce;

CREATE TABLE IF NOT EXISTS tbl_equivalencias_votante (
  id INT NOT NULL AUTO_INCREMENT,
  tbl_ficha_tecnica_encuesta_id INT NOT NULL,
  tbl_pregunta_id INT NOT NULL,
  tbl_opcion_respuesta_id INT NOT NULL,
  campo_votante VARCHAR(64) NOT NULL,
  valor_votante VARCHAR(150) NOT NULL,
  dtcreate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY idx_uniq_equiv (tbl_pregunta_id, tbl_opcion_respuesta_id, campo_votante),
  KEY idx_equiv_ficha (tbl_ficha_tecnica_encuesta_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO tbl_migraciones_ejecutadas (nombre_migracion, dtejecutada)
SELECT 'fase-i-006-zonas-y-config-schema', NOW()
WHERE NOT EXISTS (
  SELECT 1 FROM tbl_migraciones_ejecutadas
  WHERE nombre_migracion = 'fase-i-006-zonas-y-config-schema'
);
