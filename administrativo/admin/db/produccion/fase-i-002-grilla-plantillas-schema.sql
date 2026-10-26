CREATE TABLE IF NOT EXISTS tbl_grilla_plantillas (
  id INT NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(255) NOT NULL,
  descripcion TEXT NULL,
  libreto_encuestador TEXT NULL,
  nota_tabulacion TEXT NULL,
  tipo ENUM('candidatos','escala','libre') NOT NULL DEFAULT 'candidatos',
  origen_filas ENUM('manual','participantes') NOT NULL DEFAULT 'manual',
  tbl_cargo_publico_id INT NULL,
  codigo_departamento VARCHAR(10) NULL,
  codigo_municipio VARCHAR(10) NULL,
  presentacion ENUM('tarjetas','tabla','auto') NOT NULL DEFAULT 'tarjetas',
  habilitado ENUM('si','no') NOT NULL DEFAULT 'si',
  eliminado TINYINT(1) NOT NULL DEFAULT 0,
  tbl_usuario_id INT UNSIGNED NULL,
  dtcreate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  dtupdate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_plantilla_estado (eliminado, habilitado),
  KEY idx_plantilla_muni (codigo_municipio)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tbl_grilla_filas (
  id INT NOT NULL AUTO_INCREMENT,
  tbl_grilla_plantilla_id INT NOT NULL,
  codigo VARCHAR(64) NULL,
  etiqueta VARCHAR(255) NOT NULL,
  subtitulo VARCHAR(255) NULL,
  tbl_participante_id INT NULL,
  foto VARCHAR(255) NULL,
  orden INT NOT NULL DEFAULT 1,
  eliminado TINYINT(1) NOT NULL DEFAULT 0,
  dtcreate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_fila_plantilla_orden (tbl_grilla_plantilla_id, eliminado, orden),
  CONSTRAINT fk_grilla_filas_plantilla FOREIGN KEY (tbl_grilla_plantilla_id)
    REFERENCES tbl_grilla_plantillas (id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tbl_grilla_columnas (
  id INT NOT NULL AUTO_INCREMENT,
  tbl_grilla_plantilla_id INT NOT NULL,
  codigo VARCHAR(64) NULL,
  titulo VARCHAR(500) NOT NULL,
  libreto_encuestador TEXT NULL,
  nota_tabulacion TEXT NULL,
  tipo_respuesta ENUM('unica','multiple') NOT NULL DEFAULT 'unica',
  limite_multiple INT NOT NULL DEFAULT 1,
  obligatoria TINYINT(1) NOT NULL DEFAULT 1,
  condicion_columna_id INT NULL,
  condicion_opcion_id INT NULL,
  mensaje_bloqueo VARCHAR(255) NULL DEFAULT 'No lo conoce, pase al siguiente',
  rol_resultado ENUM('ninguno','conocimiento','imagen','intencion','ideologia','asociacion_positiva','asociacion_negativa','escala','otro') NOT NULL DEFAULT 'ninguno',
  orden INT NOT NULL DEFAULT 1,
  eliminado TINYINT(1) NOT NULL DEFAULT 0,
  dtcreate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_col_plantilla_orden (tbl_grilla_plantilla_id, eliminado, orden),
  CONSTRAINT fk_grilla_columnas_plantilla FOREIGN KEY (tbl_grilla_plantilla_id)
    REFERENCES tbl_grilla_plantillas (id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tbl_grilla_columna_opciones (
  id INT NOT NULL AUTO_INCREMENT,
  tbl_grilla_columna_id INT NOT NULL,
  codigo VARCHAR(64) NULL,
  texto VARCHAR(500) NOT NULL,
  valor_numerico DECIMAL(10,2) NULL,
  cuenta_como ENUM('ninguno','si','no','favorable','desfavorable') NOT NULL DEFAULT 'ninguno',
  orden INT NOT NULL DEFAULT 1,
  eliminado TINYINT(1) NOT NULL DEFAULT 0,
  dtcreate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_col_op_orden (tbl_grilla_columna_id, eliminado, orden),
  CONSTRAINT fk_grilla_col_opciones_columna FOREIGN KEY (tbl_grilla_columna_id)
    REFERENCES tbl_grilla_columnas (id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO tbl_migraciones_ejecutadas (nombre_migracion, dtejecutada)
SELECT 'fase-i-002-grilla-plantillas-schema', NOW()
WHERE NOT EXISTS (
  SELECT 1 FROM tbl_migraciones_ejecutadas
  WHERE nombre_migracion = 'fase-i-002-grilla-plantillas-schema'
);
