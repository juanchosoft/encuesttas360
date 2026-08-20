-- Tabla para guardar los cálculos del investigador en Análisis de Estudio
-- Permite documentar las operaciones aritméticas utilizadas para obtener el resultado final

CREATE TABLE IF NOT EXISTS `tbl_analisis_calculos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tbl_analisis_estudio_id` int(11) NOT NULL COMMENT 'Relación con el análisis de estudio',
  `orden` int(11) NOT NULL DEFAULT 1 COMMENT 'Orden de la operación en la secuencia de cálculos',
  `descripcion` varchar(255) DEFAULT NULL COMMENT 'Descripción de qué representa este cálculo',
  `valor1` decimal(15,4) NOT NULL COMMENT 'Primer valor de la operación',
  `operador` enum('+','-','*','/','%') NOT NULL COMMENT 'Operador aritmético: suma, resta, multiplicación, división, módulo',
  `valor2` decimal(15,4) NOT NULL COMMENT 'Segundo valor de la operación',
  `resultado` decimal(15,4) NOT NULL COMMENT 'Resultado de la operación',
  `dtcreate` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha de creación',
  PRIMARY KEY (`id`),
  KEY `idx_analisis_estudio` (`tbl_analisis_estudio_id`),
  KEY `idx_orden` (`orden`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Cálculos aritméticos del investigador para análisis de estudio';
