SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS tbl_ia_ejecuciones (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  token CHAR(32) NOT NULL,
  tbl_usuario_id INT UNSIGNED NOT NULL,
  tbl_ia_conversacion_id INT UNSIGNED NOT NULL,
  client_request_id VARCHAR(64) NOT NULL,
  inicio_mensaje_id INT UNSIGNED NOT NULL,
  estado VARCHAR(12) NOT NULL DEFAULT 'queued',
  fase VARCHAR(20) NOT NULL DEFAULT 'queued',
  detalle VARCHAR(200) NULL,
  iteracion INT NOT NULL DEFAULT 0,
  reintentos INT NOT NULL DEFAULT 0,
  lease_token CHAR(32) NULL,
  heartbeat_at DATETIME NULL,
  pasos_json TEXT NULL,
  adjuntos_json TEXT NULL,
  mensaje_final_id INT UNSIGNED NULL,
  respuesta LONGTEXT NULL,
  error_code VARCHAR(30) NULL,
  error_msg VARCHAR(300) NULL,
  dt_create DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  dt_end DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_ia_ejec_token (token),
  UNIQUE KEY uq_ia_ejec_req (tbl_usuario_id, client_request_id),
  KEY idx_ia_ejec_usuario (tbl_usuario_id, estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO tbl_migraciones_ejecutadas (nombre_migracion, dtejecutada)
SELECT 'fase-n-003-ejecuciones-ia', NOW()
WHERE NOT EXISTS (
  SELECT 1 FROM tbl_migraciones_ejecutadas
  WHERE nombre_migracion = 'fase-n-003-ejecuciones-ia'
);
