-- Migración: agregar columnas enunciado_pregunta, numeral y texto_adicional a tbl_preguntas
-- Fecha: 2026-04-13
-- Descripción: Nuevos campos para el modal "Configura tu cuestionario" (batch modal)
--   enunciado_pregunta → enunciado o instrucción del grupo de preguntas
--   numeral            → código alfanumérico de jerarquía del enunciado (ej: "1.1", "2.3.a")
--   texto_adicional    → contexto o instrucción extra asociada al enunciado

ALTER TABLE `tbl_preguntas`
  ADD COLUMN `enunciado_pregunta` TEXT         NULL DEFAULT NULL COMMENT 'Enunciado o instrucción del grupo de preguntas'                                                    AFTER `texto_pregunta`,
  ADD COLUMN `numeral`            VARCHAR(20)  NULL DEFAULT NULL COMMENT 'Texto que desean agregar al inicio del enunciado para indicar su jerarquía (ej: "1.1", "2.3.a")'  AFTER `enunciado_pregunta`,
  ADD COLUMN `texto_adicional`    TEXT         NULL DEFAULT NULL COMMENT 'Texto de contexto o instrucción extra del enunciado'                                               AFTER `numeral`;
