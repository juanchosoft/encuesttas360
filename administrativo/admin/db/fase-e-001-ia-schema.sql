CREATE TABLE IF NOT EXISTS tbl_ia_conversaciones (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  tbl_usuario_id INT UNSIGNED NOT NULL,
  titulo VARCHAR(180) NULL,
  activa TINYINT(1) NOT NULL DEFAULT 1,
  dt_create DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  dt_update DATETIME NULL,
  KEY idx_ia_conv_usuario (tbl_usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tbl_ia_mensajes (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  tbl_ia_conversacion_id INT UNSIGNED NOT NULL,
  rol ENUM('user','assistant') NOT NULL,
  contenido TEXT NULL,
  contenido_api LONGTEXT NOT NULL,
  tokens_entrada INT UNSIGNED NULL,
  tokens_salida INT UNSIGNED NULL,
  origen ENUM('texto') NOT NULL DEFAULT 'texto',
  dt_create DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_ia_msg_conversacion (tbl_ia_conversacion_id),
  CONSTRAINT fk_ia_mensajes_conversacion FOREIGN KEY (tbl_ia_conversacion_id)
    REFERENCES tbl_ia_conversaciones(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tbl_ia_tool_logs (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  tbl_usuario_id INT UNSIGNED NOT NULL,
  tool_nombre VARCHAR(80) NOT NULL,
  tool_input TEXT NULL,
  filas_devueltas INT UNSIGNED NULL,
  duracion_ms INT UNSIGNED NULL,
  exito TINYINT(1) NOT NULL DEFAULT 1,
  error TEXT NULL,
  dt_create DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_ia_log_usuario (tbl_usuario_id),
  KEY idx_ia_log_tool (tool_nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tbl_ia_informes (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  tbl_usuario_id INT UNSIGNED NOT NULL,
  titulo VARCHAR(180) NOT NULL,
  contenido_html LONGTEXT NOT NULL,
  archivo_pdf VARCHAR(120) NULL,
  dt_create DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_ia_informe_usuario (tbl_usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO tbl_migraciones_ejecutadas (nombre_migracion, dtejecutada)
SELECT 'fase-e-001-ia-schema', NOW()
WHERE NOT EXISTS (
  SELECT 1 FROM tbl_migraciones_ejecutadas WHERE nombre_migracion = 'fase-e-001-ia-schema'
);
