-- =============================================================================
-- MÓDULO DE CLIENTES
-- Sistema de Estadísticas de Gobierno
-- Fecha: 2025-11-13
-- Descripción: Script completo para instalación del módulo de clientes
-- =============================================================================

-- -----------------------------------------------------------------------------
-- 1. TABLA PRINCIPAL DE CLIENTES
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tbl_clientes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `identificacion_tipo` varchar(50) NOT NULL COMMENT 'Tipo de identificación: CC, NIT, CE, Pasaporte, TI',
  `identificacion_num` varchar(100) NOT NULL COMMENT 'Número de identificación',
  `dv` varchar(10) DEFAULT NULL COMMENT 'Dígito de verificación',
  `nombre_completo` varchar(155) NOT NULL COMMENT 'Nombre completo o razón social',
  `direccion` varchar(100) NOT NULL COMMENT 'Dirección física',
  `ubicacion` varchar(100) DEFAULT NULL COMMENT 'Ciudad o municipio',
  `tipo_cliente` varchar(100) DEFAULT NULL COMMENT 'Tipo: Natural, Juridica, Empresa, Gobierno',
  `barrio` varchar(60) DEFAULT NULL COMMENT 'Barrio o sector',
  `telefono` varchar(100) DEFAULT NULL COMMENT 'Teléfono fijo',
  `celular` varchar(100) DEFAULT NULL COMMENT 'Número celular',
  `email` varchar(100) DEFAULT NULL COMMENT 'Correo electrónico',
  `tel_contacto` varchar(100) DEFAULT NULL COMMENT 'Teléfono del contacto adicional',
  `contacto` varchar(100) DEFAULT NULL COMMENT 'Nombre del contacto adicional',
  `habilitado` varchar(10) NOT NULL DEFAULT 'SI' COMMENT 'Estado del cliente: SI/NO',
  `cumpleanos` date DEFAULT NULL COMMENT 'Fecha de cumpleaños',
  `dtcreate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de creación/actualización',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_identificacion` (`identificacion_tipo`, `identificacion_num`) COMMENT 'Evita duplicados de identificación',
  KEY `idx_nombre` (`nombre_completo`) COMMENT 'Búsqueda por nombre',
  KEY `idx_email` (`email`) COMMENT 'Búsqueda por email',
  KEY `idx_habilitado` (`habilitado`) COMMENT 'Filtro por estado'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de gestión de clientes';

-- -----------------------------------------------------------------------------
-- 2. PERMISOS DEL MÓDULO
-- -----------------------------------------------------------------------------
-- Crear los permisos para el módulo de clientes
-- IDs utilizados: 86 (Ver), 87 (Crear), 88 (Editar), 89 (Permisos)

INSERT INTO tbl_permisos (id, dtcreate, modulo, nombre) VALUES
(86, NOW(), 'Clientes', 'Clientes - Ver'),
(87, NOW(), 'Clientes', 'Clientes - Crear'),
(88, NOW(), 'Clientes', 'Clientes - Editar'),
(89, NOW(), 'Clientes', 'Clientes - Permisos')
ON DUPLICATE KEY UPDATE modulo = VALUES(modulo), nombre = VALUES(nombre);

-- -----------------------------------------------------------------------------
-- 3. ASIGNACIÓN DE PERMISOS A ADMINISTRADORES
-- -----------------------------------------------------------------------------
-- Asigna todos los permisos del módulo a los usuarios tipo 'Administrador'

INSERT IGNORE INTO tbl_usuarios_has_tbl_permisos (dtcreate, tbl_usuarios_id, tbl_permiso_id)
SELECT NOW(), u.id, 86 FROM tbl_usuarios u WHERE u.tipo = 'Administrador';

INSERT IGNORE INTO tbl_usuarios_has_tbl_permisos (dtcreate, tbl_usuarios_id, tbl_permiso_id)
SELECT NOW(), u.id, 87 FROM tbl_usuarios u WHERE u.tipo = 'Administrador';

INSERT IGNORE INTO tbl_usuarios_has_tbl_permisos (dtcreate, tbl_usuarios_id, tbl_permiso_id)
SELECT NOW(), u.id, 88 FROM tbl_usuarios u WHERE u.tipo = 'Administrador';

INSERT IGNORE INTO tbl_usuarios_has_tbl_permisos (dtcreate, tbl_usuarios_id, tbl_permiso_id)
SELECT NOW(), u.id, 89 FROM tbl_usuarios u WHERE u.tipo = 'Administrador';

-- -----------------------------------------------------------------------------
-- 4. DATOS DE EJEMPLO (OPCIONAL)
-- -----------------------------------------------------------------------------
-- Descomentar las siguientes líneas si desea insertar datos de prueba

/*
-- Insertar un cliente persona natural
INSERT INTO tbl_clientes
(identificacion_tipo, identificacion_num, nombre_completo, direccion, tipo_cliente, celular, email, habilitado)
VALUES
('CC', '12345678', 'Juan Pérez García', 'Calle 123 #45-67', 'Natural', '3001234567', 'juan.perez@email.com', 'SI');

-- Insertar un cliente persona jurídica
INSERT INTO tbl_clientes
(identificacion_tipo, identificacion_num, dv, nombre_completo, direccion, tipo_cliente, telefono, email, contacto, tel_contacto, habilitado)
VALUES
('NIT', '900123456', '7', 'EMPRESA EJEMPLO S.A.S.', 'Avenida Principal #100-200', 'Juridica', '6012345678', 'contacto@empresa.com', 'María López', '3109876543', 'SI');
*/

-- -----------------------------------------------------------------------------
-- 5. VERIFICACIÓN DE LA INSTALACIÓN
-- -----------------------------------------------------------------------------
-- Ejecutar estas consultas para verificar que todo se instaló correctamente

-- SELECT 'Tabla creada' AS status, COUNT(*) AS count FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'tbl_clientes';
-- SELECT 'Permisos creados' AS status, COUNT(*) AS count FROM tbl_permisos WHERE modulo = 'Clientes';
-- SELECT 'Permisos asignados' AS status, COUNT(*) AS count FROM tbl_usuarios_has_tbl_permisos WHERE tbl_permiso_id IN (86, 87, 88, 89);

-- =============================================================================
-- FIN DEL SCRIPT
-- =============================================================================
