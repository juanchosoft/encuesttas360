SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS tbl_banco_preguntas (
  id INT(11) NOT NULL AUTO_INCREMENT,
  uso ENUM('encuesta','sondeo') NOT NULL DEFAULT 'encuesta',
  categoria VARCHAR(120) NULL,
  texto_pregunta TEXT NOT NULL,
  enunciado_pregunta TEXT NULL,
  capitulo VARCHAR(255) NULL,
  numeral VARCHAR(60) NULL,
  tipo_pregunta VARCHAR(50) NULL,
  limite_respuesta_multiple VARCHAR(2) NOT NULL DEFAULT '1',
  obligatoria TINYINT(1) NOT NULL DEFAULT 1,
  es_filtro TINYINT(1) NOT NULL DEFAULT 0,
  libreto_encuestador TEXT NULL,
  nota_tabulacion TEXT NULL,
  es_variable_cruce TINYINT(1) NOT NULL DEFAULT 0,
  etiqueta_cruce VARCHAR(120) NULL,
  copiar_a_votante_campo VARCHAR(64) NULL,
  rol_resultado VARCHAR(40) NOT NULL DEFAULT 'ninguno',
  tbl_grilla_plantilla_id INT(11) NULL,
  descripcion_sondeo VARCHAR(255) NULL,
  tipo_sondeo VARCHAR(30) NULL,
  tipo_inferenciales VARCHAR(30) NULL,
  es_trivia VARCHAR(2) NULL,
  hash_contenido CHAR(40) NOT NULL,
  tbl_usuario_id INT(11) NULL,
  dtcreate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  dtupdate DATETIME NULL,
  eliminado TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  KEY idx_banco_uso_eliminado (uso, eliminado),
  KEY idx_banco_hash (hash_contenido),
  KEY idx_banco_categoria (categoria)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tbl_banco_pregunta_opciones (
  id INT(11) NOT NULL AUTO_INCREMENT,
  tbl_banco_pregunta_id INT(11) NOT NULL,
  texto_opcion TEXT NOT NULL,
  orden INT(11) NULL,
  valor_numerico DECIMAL(10,2) NULL,
  cuenta_como ENUM('ninguno','si','no','favorable','desfavorable') NOT NULL DEFAULT 'ninguno',
  accion ENUM('continuar','terminar') NOT NULL DEFAULT 'continuar',
  mensaje_terminar TEXT NULL,
  es_opcion_fija TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  KEY idx_banco_opc_pregunta (tbl_banco_pregunta_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO tbl_permissions (permission_key, module, action, name, description, legacy_id, dt_create, is_active) VALUES
  ('encuestas.banco_preguntas.view', 'encuestas.banco_preguntas', 'view', 'Banco de preguntas - Ver', NULL, NULL, NOW(), 1),
  ('encuestas.banco_preguntas.create', 'encuestas.banco_preguntas', 'create', 'Banco de preguntas - Crear', NULL, NULL, NOW(), 1),
  ('encuestas.banco_preguntas.update', 'encuestas.banco_preguntas', 'update', 'Banco de preguntas - Editar', NULL, NULL, NOW(), 1),
  ('encuestas.banco_preguntas.manage', 'encuestas.banco_preguntas', 'manage', 'Banco de preguntas - Permisos', NULL, NULL, NOW(), 1),
  ('encuestas.banco_preguntas.delete', 'encuestas.banco_preguntas', 'delete', 'Banco de preguntas - Eliminar', NULL, NULL, NOW(), 1)
ON DUPLICATE KEY UPDATE module = VALUES(module), action = VALUES(action), name = VALUES(name), is_active = 1;

INSERT IGNORE INTO tbl_role_has_permissions (role_id, permission_id, dt_create)
SELECT r.id, p.id, NOW()
FROM tbl_roles r
JOIN tbl_permissions p ON p.permission_key IN (
  'encuestas.banco_preguntas.view', 'encuestas.banco_preguntas.create', 'encuestas.banco_preguntas.update',
  'encuestas.banco_preguntas.manage', 'encuestas.banco_preguntas.delete')
WHERE r.role_key IN ('super_administrador', 'administrador')
  AND NOT EXISTS (SELECT 1 FROM tbl_migraciones_ejecutadas WHERE nombre_migracion = 'fase-j-001-banco-preguntas');

INSERT IGNORE INTO tbl_role_has_permissions (role_id, permission_id, dt_create)
SELECT r.id, p.id, NOW()
FROM tbl_roles r
JOIN tbl_permissions p ON p.permission_key IN (
  'encuestas.banco_preguntas.view', 'encuestas.banco_preguntas.create', 'encuestas.banco_preguntas.update')
WHERE r.role_key = 'investigador'
  AND NOT EXISTS (SELECT 1 FROM tbl_migraciones_ejecutadas WHERE nombre_migracion = 'fase-j-001-banco-preguntas');

INSERT INTO tbl_migraciones_ejecutadas (nombre_migracion, dtejecutada)
SELECT 'fase-j-001-banco-preguntas', NOW()
WHERE NOT EXISTS (
  SELECT 1 FROM tbl_migraciones_ejecutadas
  WHERE nombre_migracion = 'fase-j-001-banco-preguntas'
);
