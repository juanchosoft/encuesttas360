-- Tabla para almacenar certificaciones de encuestadores
-- Guarda información de verificación: audio, geolocalización, metadata del dispositivo

CREATE TABLE IF NOT EXISTS `tbl_certificacion_encuestador` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tbl_votante_id` int(11) NOT NULL COMMENT 'FK al votante registrado',
  `tbl_usuario_id` int(11) NOT NULL COMMENT 'FK al encuestador que registró',

  -- Datos de geolocalización
  `latitud` decimal(10, 8) DEFAULT NULL COMMENT 'Latitud GPS',
  `longitud` decimal(11, 8) DEFAULT NULL COMMENT 'Longitud GPS',
  `precision_metros` decimal(10, 2) DEFAULT NULL COMMENT 'Precisión de la ubicación en metros',
  `altitud` decimal(10, 2) DEFAULT NULL COMMENT 'Altitud en metros',

  -- Datos de audio
  `audio_base64` longtext DEFAULT NULL COMMENT 'Audio grabado en formato base64',
  `audio_duracion_segundos` int(11) DEFAULT NULL COMMENT 'Duración del audio en segundos',
  `audio_formato` varchar(50) DEFAULT NULL COMMENT 'Formato del audio (webm, mp3, etc)',

  -- Metadata del dispositivo
  `dispositivo_info` text DEFAULT NULL COMMENT 'JSON con información del dispositivo',
  `user_agent` text DEFAULT NULL COMMENT 'User agent del navegador',
  `ip_address` varchar(45) DEFAULT NULL COMMENT 'Dirección IP del encuestador',

  -- Timestamps
  `fecha_certificacion` datetime DEFAULT NULL COMMENT 'Fecha y hora de la certificación',
  `dtcreate` datetime DEFAULT NULL,
  `dtupdate` datetime DEFAULT NULL,

  PRIMARY KEY (`id`),
  KEY `idx_votante` (`tbl_votante_id`),
  KEY `idx_usuario` (`tbl_usuario_id`),
  KEY `idx_fecha` (`fecha_certificacion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Certificaciones de encuestadores con audio y geolocalización';
