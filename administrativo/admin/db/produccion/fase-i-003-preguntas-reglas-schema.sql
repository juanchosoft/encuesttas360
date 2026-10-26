SET @col_preg_ob = (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tbl_preguntas' AND COLUMN_NAME = 'obligatoria'
);
SET @ddl_preg_1 = IF(@col_preg_ob = 0,
  "ALTER TABLE tbl_preguntas
     ADD COLUMN obligatoria TINYINT(1) NOT NULL DEFAULT 1,
     ADD COLUMN es_filtro TINYINT(1) NOT NULL DEFAULT 0,
     ADD COLUMN libreto_encuestador TEXT NULL,
     ADD COLUMN nota_tabulacion TEXT NULL,
     ADD COLUMN tbl_grilla_plantilla_id INT NULL,
     ADD COLUMN visible_si_pregunta_id INT NULL,
     ADD COLUMN visible_si_opcion_id INT NULL,
     ADD COLUMN lista_origen_pregunta_id INT NULL,
     ADD COLUMN lista_origen_columna_id INT NULL,
     ADD COLUMN lista_origen_opcion_id INT NULL,
     ADD COLUMN si_lista_vacia ENUM('omitir','terminar') NOT NULL DEFAULT 'omitir',
     ADD COLUMN eliminado TINYINT(1) NOT NULL DEFAULT 0,
     ADD KEY idx_ficha_elim_orden (tbl_ficha_tecnica_encuesta_id, eliminado, orden)",
  'DO 0'
);
PREPARE stmt_preg_1 FROM @ddl_preg_1;
EXECUTE stmt_preg_1;
DEALLOCATE PREPARE stmt_preg_1;

SET @col_op_acc = (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tbl_opciones_respuesta' AND COLUMN_NAME = 'accion'
);
SET @ddl_op_1 = IF(@col_op_acc = 0,
  "ALTER TABLE tbl_opciones_respuesta
     ADD COLUMN valor_numerico DECIMAL(10,2) NULL,
     ADD COLUMN cuenta_como ENUM('ninguno','si','no','favorable','desfavorable') NOT NULL DEFAULT 'ninguno',
     ADD COLUMN accion ENUM('continuar','saltar','terminar') NOT NULL DEFAULT 'continuar',
     ADD COLUMN salto_destino_pregunta_id INT NULL,
     ADD COLUMN mensaje_terminar TEXT NULL,
     ADD COLUMN es_opcion_fija TINYINT(1) NOT NULL DEFAULT 0,
     ADD COLUMN eliminado TINYINT(1) NOT NULL DEFAULT 0,
     ADD KEY idx_preg_elim_orden (tbl_pregunta_id, eliminado, orden)",
  'DO 0'
);
PREPARE stmt_op_1 FROM @ddl_op_1;
EXECUTE stmt_op_1;
DEALLOCATE PREPARE stmt_op_1;

CREATE TABLE IF NOT EXISTS tbl_preguntas_lista_exclusiones (
  id INT NOT NULL AUTO_INCREMENT,
  tbl_pregunta_id INT NOT NULL,
  tbl_pregunta_excluida_id INT NOT NULL,
  dtcreate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY idx_uniq_preg_excl (tbl_pregunta_id, tbl_pregunta_excluida_id),
  KEY idx_preg_excl (tbl_pregunta_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tbl_preguntas_grilla_variantes (
  id INT NOT NULL AUTO_INCREMENT,
  tbl_pregunta_id INT NOT NULL,
  codigo_municipio VARCHAR(10) NOT NULL,
  tbl_grilla_plantilla_id INT NOT NULL,
  dtcreate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY idx_uniq_preg_muni (tbl_pregunta_id, codigo_municipio),
  KEY idx_var_plantilla (tbl_grilla_plantilla_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET @col_ficha_dest = (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tbl_ficha_tecnica_encuestas' AND COLUMN_NAME = 'pregunta_destacada_id'
);
SET @ddl_ficha_dest = IF(@col_ficha_dest = 0,
  "ALTER TABLE tbl_ficha_tecnica_encuestas
     ADD COLUMN pregunta_destacada_id INT NULL",
  'DO 0'
);
PREPARE stmt_ficha_dest FROM @ddl_ficha_dest;
EXECUTE stmt_ficha_dest;
DEALLOCATE PREPARE stmt_ficha_dest;

INSERT INTO tbl_migraciones_ejecutadas (nombre_migracion, dtejecutada)
SELECT 'fase-i-003-preguntas-reglas-schema', NOW()
WHERE NOT EXISTS (
  SELECT 1 FROM tbl_migraciones_ejecutadas
  WHERE nombre_migracion = 'fase-i-003-preguntas-reglas-schema'
);
