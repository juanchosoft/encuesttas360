SET NAMES utf8mb4;

-- Convierte el estudio de candidatos anterior (tbl_grilla id 1) en una tabla de candidatos reutilizable.
-- Solo actúa si esa grilla existe y la tabla de ejemplo todavía no se creó.

SET @crear = IF(NOT EXISTS (SELECT 1 FROM tbl_migraciones_ejecutadas WHERE nombre_migracion = 'fase-i-009-grilla-migrar-ejemplo') AND (SELECT COUNT(*) FROM tbl_grilla WHERE id = 1) = 1 AND (SELECT COUNT(*) FROM tbl_grilla_plantillas WHERE nombre = 'Ejemplo: Conocimiento e imagen de candidatos' AND eliminado = 0) = 0, 1, 0);

INSERT INTO tbl_grilla_plantillas (nombre, descripcion, libreto_encuestador, tipo, origen_filas, tbl_cargo_publico_id, presentacion, habilitado, eliminado)
SELECT 'Ejemplo: Conocimiento e imagen de candidatos', COALESCE(g.descripcion_grilla, 'Plantilla de ejemplo migrada desde el estudio de candidatos'), 'Le voy a mencionar algunos nombres de personajes públicos para conocer su opinión.', 'candidatos', 'participantes', g.tbl_cargo_publico_id, 'tarjetas', 'si', 0
FROM tbl_grilla g WHERE g.id = 1 AND @crear = 1;

SET @pid = (SELECT id FROM tbl_grilla_plantillas WHERE nombre = 'Ejemplo: Conocimiento e imagen de candidatos' AND eliminado = 0 ORDER BY id DESC LIMIT 1);

DROP TEMPORARY TABLE IF EXISTS tmp_part_orden;
CREATE TEMPORARY TABLE tmp_part_orden (n INT NOT NULL AUTO_INCREMENT PRIMARY KEY, participante_id INT NOT NULL, nombre VARCHAR(255) NOT NULL, foto VARCHAR(255) NULL);
INSERT INTO tmp_part_orden (participante_id, nombre, foto)
SELECT p.id, p.nombre_completo, p.foto
FROM tbl_grilla_x_tbl_participantes gp
INNER JOIN tbl_participantes p ON p.id = gp.tbl_participante_id
WHERE gp.tbl_grilla_id = 1 AND @crear = 1
GROUP BY p.id, p.nombre_completo, p.foto
ORDER BY MIN(gp.id) ASC;

INSERT INTO tbl_grilla_filas (tbl_grilla_plantilla_id, codigo, etiqueta, tbl_participante_id, foto, orden, eliminado)
SELECT @pid, CONCAT('cand_', participante_id), nombre, participante_id, NULLIF(foto, ''), n, 0 FROM tmp_part_orden WHERE @crear = 1;
DROP TEMPORARY TABLE IF EXISTS tmp_part_orden;

INSERT INTO tbl_grilla_columnas (tbl_grilla_plantilla_id, codigo, titulo, tipo_respuesta, limite_multiple, obligatoria, rol_resultado, orden, eliminado)
SELECT @pid, 'conoce', '¿Lo conoce o ha oído hablar de él/ella?', 'unica', 1, 1, 'conocimiento', 1, 0 FROM DUAL WHERE @crear = 1;
SET @c_conoce = (SELECT id FROM tbl_grilla_columnas WHERE tbl_grilla_plantilla_id = @pid AND codigo = 'conoce' ORDER BY id DESC LIMIT 1);
INSERT INTO tbl_grilla_columna_opciones (tbl_grilla_columna_id, codigo, texto, valor_numerico, cuenta_como, orden, eliminado)
SELECT @c_conoce, 'si', 'Sí lo conoce', 1, 'si', 1, 0 FROM DUAL WHERE @crear = 1
UNION ALL SELECT @c_conoce, 'no', 'No lo conoce', 0, 'no', 2, 0 FROM DUAL WHERE @crear = 1;
SET @o_conoce_si = (SELECT id FROM tbl_grilla_columna_opciones WHERE tbl_grilla_columna_id = @c_conoce AND codigo = 'si' ORDER BY id DESC LIMIT 1);

INSERT INTO tbl_grilla_columnas (tbl_grilla_plantilla_id, codigo, titulo, tipo_respuesta, limite_multiple, obligatoria, condicion_columna_id, condicion_opcion_id, rol_resultado, orden, eliminado)
SELECT @pid, 'imagen', '¿Tiene una imagen favorable o desfavorable?', 'unica', 1, 1, @c_conoce, @o_conoce_si, 'imagen', 2, 0 FROM DUAL WHERE @crear = 1
UNION ALL SELECT @pid, 'votaria', '¿Votaría por él/ella?', 'unica', 1, 1, @c_conoce, @o_conoce_si, 'intencion', 3, 0 FROM DUAL WHERE @crear = 1;
SET @c_imagen = (SELECT id FROM tbl_grilla_columnas WHERE tbl_grilla_plantilla_id = @pid AND codigo = 'imagen' ORDER BY id DESC LIMIT 1);
SET @c_votaria = (SELECT id FROM tbl_grilla_columnas WHERE tbl_grilla_plantilla_id = @pid AND codigo = 'votaria' ORDER BY id DESC LIMIT 1);
INSERT INTO tbl_grilla_columna_opciones (tbl_grilla_columna_id, codigo, texto, valor_numerico, cuenta_como, orden, eliminado)
SELECT @c_imagen, 'fav', 'Favorable', 1, 'favorable', 1, 0 FROM DUAL WHERE @crear = 1
UNION ALL SELECT @c_imagen, 'desfav', 'Desfavorable', 0, 'desfavorable', 2, 0 FROM DUAL WHERE @crear = 1
UNION ALL SELECT @c_imagen, 'nsnr', 'No sabe / No responde', NULL, 'ninguno', 3, 0 FROM DUAL WHERE @crear = 1
UNION ALL SELECT @c_votaria, 'si_vota', 'Sí votaría', 1, 'si', 1, 0 FROM DUAL WHERE @crear = 1
UNION ALL SELECT @c_votaria, 'no_vota', 'Nunca votaría', 0, 'no', 2, 0 FROM DUAL WHERE @crear = 1
UNION ALL SELECT @c_votaria, 'nsnr', 'No sabe / No responde', NULL, 'ninguno', 3, 0 FROM DUAL WHERE @crear = 1;

INSERT INTO tbl_migraciones_ejecutadas (nombre_migracion, dtejecutada)
SELECT 'fase-i-009-grilla-migrar-ejemplo', NOW()
WHERE NOT EXISTS (
  SELECT 1 FROM tbl_migraciones_ejecutadas WHERE nombre_migracion = 'fase-i-009-grilla-migrar-ejemplo'
);
