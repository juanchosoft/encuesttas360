SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS tbl_ia_informe_versiones (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  tbl_ia_informe_id INT UNSIGNED NOT NULL,
  tbl_usuario_id INT UNSIGNED NOT NULL,
  titulo VARCHAR(180) NOT NULL,
  subtitulo VARCHAR(200) NULL,
  fuente_dsl LONGTEXT NULL,
  contenido_html LONGTEXT NULL,
  opciones_json TEXT NULL,
  dt_create DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_ia_informe_version (tbl_ia_informe_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO tbl_migraciones_ejecutadas (nombre_migracion, dtejecutada)
SELECT 'fase-n-002-versiones-informes-ia', NOW()
WHERE NOT EXISTS (
  SELECT 1 FROM tbl_migraciones_ejecutadas
  WHERE nombre_migracion = 'fase-n-002-versiones-informes-ia'
);
