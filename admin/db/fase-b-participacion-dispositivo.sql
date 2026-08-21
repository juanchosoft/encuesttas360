
CREATE TABLE IF NOT EXISTS tbl_participacion_dispositivo (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  device_uuid VARCHAR(80) NOT NULL,
  device_fingerprint VARCHAR(128) NULL,
  tipo_instrumento VARCHAR(20) NOT NULL,
  instrumento_id INT UNSIGNED NOT NULL,
  tbl_votante_id INT UNSIGNED NOT NULL,
  dtcreate DATETIME NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_device_instrumento (device_uuid, tipo_instrumento, instrumento_id),
  KEY idx_device (device_uuid),
  KEY idx_votante (tbl_votante_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
