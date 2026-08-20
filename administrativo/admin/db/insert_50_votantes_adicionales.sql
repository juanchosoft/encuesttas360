-- =====================================================
-- Script para insertar 50 VOTANTES DUMMY ADICIONALES
-- IDs: 52-101 (los primeros 50 son IDs 2-51)
-- =====================================================
-- Fecha: 2025-01-07
-- Descripción: Votantes con datos diversos para simulación de votaciones
-- =====================================================

USE estadisticas_db;

-- Desactivar verificación de claves foráneas temporalmente
SET FOREIGN_KEY_CHECKS = 0;

-- BLOQUE 1: IDs 52-61 (10 votantes)
INSERT INTO tbl_votantes (id, nombre_completo, ideologia, rango_edad, nivel_ingresos, genero, codigo_departamento, codigo_municipio, comuna, barrio, nivel_educacion, ocupacion, estado, dtcreate) VALUES
(52, 'Daniela Gómez Torres', 'centro', '26-35', '3-5_salarios', 'femenino', '86', '86001', 'Comuna 3', 'La Esperanza', 'universitario_completo', 'Ingeniera', 'activo', NOW()),
(53, 'Ricardo Patiño Jiménez', 'derecha', '36-45', '6-10_salarios', 'masculino', '86', '86001', 'Comuna 1', 'El Centro', 'posgrado', 'Abogado', 'activo', NOW()),
(54, 'Liliana Castro Morales', 'izquierda', '46-55', '3-5_salarios', 'femenino', '86', '86001', 'Comuna 2', 'San Rafael', 'universitario_completo', 'Docente', 'activo', NOW()),
(55, 'Fernando López García', 'independiente', '18-25', '1-2_salarios', 'masculino', '86', '86001', 'Comuna 4', 'Villa Nueva', 'secundaria_completa', 'Estudiante', 'activo', NOW()),
(56, 'Patricia Rojas Mendoza', 'centro_derecha', '26-35', '6-10_salarios', 'femenino', '86', '86001', 'Comuna 1', 'Centro Histórico', 'posgrado', 'Contadora', 'activo', NOW()),
(57, 'Andrés Silva Ramírez', 'centro_izquierda', '36-45', '3-5_salarios', 'masculino', '86', '86001', 'Comuna 3', 'Los Pinos', 'universitario_incompleto', 'Comerciante', 'activo', NOW()),
(58, 'Mónica Vargas Ortiz', 'izquierda', '26-35', '1-2_salarios', 'femenino', '86', '86001', 'Comuna 5', 'Primavera', 'tecnico', 'Auxiliar administrativa', 'activo', NOW()),
(59, 'Diego Herrera Vásquez', 'derecha', '46-55', 'mas_10_salarios', 'masculino', '86', '86001', 'Comuna 1', 'La Colina', 'posgrado', 'Empresario', 'activo', NOW()),
(60, 'Carolina Mejía Sánchez', 'centro', '36-45', '3-5_salarios', 'femenino', '86', '86001', 'Comuna 2', 'Los Álamos', 'universitario_completo', 'Enfermera', 'activo', NOW()),
(61, 'Pablo Gutiérrez Cruz', 'independiente', '26-35', '3-5_salarios', 'masculino', '86', '86001', 'Comuna 4', 'El Bosque', 'universitario_completo', 'Diseñador', 'activo', NOW());

-- BLOQUE 2: IDs 62-71 (10 votantes)
INSERT INTO tbl_votantes (id, nombre_completo, ideologia, rango_edad, nivel_ingresos, genero, codigo_departamento, codigo_municipio, comuna, barrio, nivel_educacion, ocupacion, estado, dtcreate) VALUES
(62, 'Valeria Ochoa Peña', 'centro_izquierda', '18-25', '1-2_salarios', 'femenino', '86', '86001', 'Comuna 5', 'La Aurora', 'secundaria_completa', 'Estudiante universitaria', 'activo', NOW()),
(63, 'Javier Rincón Medina', 'derecha', '56-65', 'mas_10_salarios', 'masculino', '86', '86001', 'Comuna 1', 'Altos del Norte', 'posgrado', 'Médico', 'activo', NOW()),
(64, 'Sandra Quintero Ruiz', 'izquierda', '36-45', '3-5_salarios', 'femenino', '86', '86001', 'Comuna 3', 'Vista Hermosa', 'tecnico', 'Trabajadora social', 'activo', NOW()),
(65, 'Mauricio Navarro León', 'centro', '26-35', '6-10_salarios', 'masculino', '86', '86001', 'Comuna 2', 'San Martín', 'universitario_completo', 'Arquitecto', 'activo', NOW()),
(66, 'Adriana Salazar Torres', 'independiente', '46-55', '3-5_salarios', 'femenino', '86', '86001', 'Comuna 4', 'Los Andes', 'universitario_incompleto', 'Comerciante', 'activo', NOW()),
(67, 'Hector Duarte Castillo', 'centro_derecha', '36-45', 'mas_10_salarios', 'masculino', '86', '86001', 'Comuna 1', 'La Esmeralda', 'posgrado', 'Gerente', 'activo', NOW()),
(68, 'Isabel Cardenas Gómez', 'izquierda', '26-35', '1-2_salarios', 'femenino', '86', '86001', 'Comuna 5', 'Bellavista', 'tecnico', 'Secretaria', 'activo', NOW()),
(69, 'Esteban Acosta Ríos', 'derecha', '36-45', '6-10_salarios', 'masculino', '86', '86001', 'Comuna 2', 'Santa Lucía', 'universitario_completo', 'Ingeniero civil', 'activo', NOW()),
(70, 'Natalia Suárez Hernández', 'centro', '18-25', '1-2_salarios', 'femenino', '86', '86001', 'Comuna 3', 'La Pradera', 'secundaria_completa', 'Estudiante', 'activo', NOW()),
(71, 'Roberto Mora Pineda', 'independiente', '56-65', '3-5_salarios', 'masculino', '86', '86001', 'Comuna 4', 'El Refugio', 'secundaria_completa', 'Pensionado', 'activo', NOW());

-- BLOQUE 3: IDs 72-81 (10 votantes)
INSERT INTO tbl_votantes (id, nombre_completo, ideologia, rango_edad, nivel_ingresos, genero, codigo_departamento, codigo_municipio, comuna, barrio, nivel_educacion, ocupacion, estado, dtcreate) VALUES
(72, 'Gloria Paredes Muñoz', 'centro_izquierda', '46-55', '3-5_salarios', 'femenino', '86', '86001', 'Comuna 1', 'Centro', 'universitario_completo', 'Psicóloga', 'activo', NOW()),
(73, 'Camilo Fuentes Aguilar', 'izquierda', '26-35', '3-5_salarios', 'masculino', '86', '86001', 'Comuna 5', 'Nuevo Horizonte', 'universitario_completo', 'Sociólogo', 'activo', NOW()),
(74, 'Beatriz Osorio Vega', 'derecha', '36-45', '6-10_salarios', 'femenino', '86', '86001', 'Comuna 2', 'Las Palmas', 'posgrado', 'Economista', 'activo', NOW()),
(75, 'Gustavo Parra Reyes', 'centro', '46-55', '6-10_salarios', 'masculino', '86', '86001', 'Comuna 1', 'La Florida', 'universitario_completo', 'Administrador', 'activo', NOW()),
(76, 'Elena Molina Díaz', 'independiente', '26-35', '1-2_salarios', 'femenino', '86', '86001', 'Comuna 3', 'Villa Rosa', 'tecnico', 'Asistente', 'activo', NOW()),
(77, 'Sergio Cortés Bautista', 'centro_derecha', '36-45', 'mas_10_salarios', 'masculino', '86', '86001', 'Comuna 1', 'Lomas del Sur', 'posgrado', 'Consultor', 'activo', NOW()),
(78, 'Claudia Márquez Rojas', 'izquierda', '18-25', '1-2_salarios', 'femenino', '86', '86001', 'Comuna 4', 'La Victoria', 'universitario_incompleto', 'Estudiante', 'activo', NOW()),
(79, 'Alfonso Delgado Rueda', 'derecha', '56-65', 'mas_10_salarios', 'masculino', '86', '86001', 'Comuna 2', 'Alto Prado', 'posgrado', 'Ingeniero', 'activo', NOW()),
(80, 'Pilar Soto Arias', 'centro', '36-45', '3-5_salarios', 'femenino', '86', '86001', 'Comuna 3', 'San José', 'universitario_completo', 'Nutricionista', 'activo', NOW()),
(81, 'Joaquín Ramírez Luna', 'independiente', '26-35', '3-5_salarios', 'masculino', '86', '86001', 'Comuna 5', 'El Paraíso', 'tecnico', 'Electricista', 'activo', NOW());

-- BLOQUE 4: IDs 82-91 (10 votantes)
INSERT INTO tbl_votantes (id, nombre_completo, ideologia, rango_edad, nivel_ingresos, genero, codigo_departamento, codigo_municipio, comuna, barrio, nivel_educacion, ocupacion, estado, dtcreate) VALUES
(82, 'Verónica Castro Suárez', 'centro_izquierda', '36-45', '3-5_salarios', 'femenino', '86', '86001', 'Comuna 2', 'Los Cerezos', 'universitario_completo', 'Periodista', 'activo', NOW()),
(83, 'Hugo Zambrano Gil', 'izquierda', '46-55', '3-5_salarios', 'masculino', '86', '86001', 'Comuna 4', 'Santa Elena', 'universitario_incompleto', 'Artista', 'activo', NOW()),
(84, 'Lorena Campos Martínez', 'derecha', '26-35', '6-10_salarios', 'femenino', '86', '86001', 'Comuna 1', 'Versalles', 'posgrado', 'Ingeniera industrial', 'activo', NOW()),
(85, 'Raúl Figueroa Cardona', 'centro', '56-65', '6-10_salarios', 'masculino', '86', '86001', 'Comuna 3', 'El Lago', 'universitario_completo', 'Contador', 'activo', NOW()),
(86, 'Teresa Nieto Ponce', 'independiente', '18-25', '1-2_salarios', 'femenino', '86', '86001', 'Comuna 5', 'Nueva Esperanza', 'secundaria_completa', 'Vendedora', 'activo', NOW()),
(87, 'Vicente Blanco Ortega', 'centro_derecha', '36-45', 'mas_10_salarios', 'masculino', '86', '86001', 'Comuna 1', 'Country Club', 'posgrado', 'Director', 'activo', NOW()),
(88, 'Rosa Espinosa Zapata', 'izquierda', '26-35', '1-2_salarios', 'femenino', '86', '86001', 'Comuna 4', 'Pueblo Nuevo', 'tecnico', 'Auxiliar de enfermería', 'activo', NOW()),
(89, 'Arturo Gil Romero', 'derecha', '46-55', 'mas_10_salarios', 'masculino', '86', '86001', 'Comuna 2', 'La Campiña', 'posgrado', 'Odontólogo', 'activo', NOW()),
(90, 'Silvia Montenegro Salas', 'centro', '36-45', '3-5_salarios', 'femenino', '86', '86001', 'Comuna 3', 'Brisas del Sur', 'universitario_completo', 'Bióloga', 'activo', NOW()),
(91, 'Marcos Peña Varela', 'independiente', '26-35', '3-5_salarios', 'masculino', '86', '86001', 'Comuna 5', 'Los Laureles', 'universitario_completo', 'Programador', 'activo', NOW());

-- BLOQUE 5: IDs 92-101 (10 votantes)
INSERT INTO tbl_votantes (id, nombre_completo, ideologia, rango_edad, nivel_ingresos, genero, codigo_departamento, codigo_municipio, comuna, barrio, nivel_educacion, ocupacion, estado, dtcreate) VALUES
(92, 'Angela Guerrero Téllez', 'centro_izquierda', '18-25', '1-2_salarios', 'femenino', '86', '86001', 'Comuna 4', 'La Paz', 'universitario_incompleto', 'Estudiante', 'activo', NOW()),
(93, 'Ignacio Carrillo Vera', 'izquierda', '36-45', '3-5_salarios', 'masculino', '86', '86001', 'Comuna 5', 'El Porvenir', 'tecnico', 'Trabajador comunitario', 'activo', NOW()),
(94, 'Margarita Cortés Ramos', 'derecha', '46-55', '6-10_salarios', 'femenino', '86', '86001', 'Comuna 1', 'Los Rosales', 'universitario_completo', 'Auditora', 'activo', NOW()),
(95, 'Tomás Restrepo Cano', 'centro', '26-35', '6-10_salarios', 'masculino', '86', '86001', 'Comuna 2', 'San Antonio', 'posgrado', 'Ingeniero de sistemas', 'activo', NOW()),
(96, 'Lucía Mendez Parra', 'independiente', '36-45', '3-5_salarios', 'femenino', '86', '86001', 'Comuna 3', 'Villa Hermosa', 'universitario_completo', 'Veterinaria', 'activo', NOW()),
(97, 'Fabián Lara Estrada', 'centro_derecha', '46-55', 'mas_10_salarios', 'masculino', '86', '86001', 'Comuna 1', 'El Poblado', 'posgrado', 'Ejecutivo', 'activo', NOW()),
(98, 'Paola Ávila Santos', 'izquierda', '26-35', '1-2_salarios', 'femenino', '86', '86001', 'Comuna 4', 'Los Comuneros', 'tecnico', 'Operaria', 'activo', NOW()),
(99, 'Leonardo Sánchez Gómez', 'derecha', '36-45', 'mas_10_salarios', 'masculino', '86', '86001', 'Comuna 2', 'La Castellana', 'posgrado', 'Financiero', 'activo', NOW()),
(100, 'Diana Rubio Pedraza', 'centro', '18-25', '1-2_salarios', 'femenino', '86', '86001', 'Comuna 5', 'Las Acacias', 'secundaria_completa', 'Empleada', 'activo', NOW()),
(101, 'Samuel Rojas Contreras', 'sin_definir', '26-35', '3-5_salarios', 'otro', '86', '86001', 'Comuna 3', 'El Mirador', 'universitario_completo', 'Artista visual', 'activo', NOW());

-- Reactivar verificación de claves foráneas
SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================
-- RESUMEN DE DISTRIBUCIÓN (50 votantes adicionales)
-- =====================================================
-- GÉNERO:
-- - Femenino: 25 (50%)
-- - Masculino: 24 (48%)
-- - Otro: 1 (2%)
--
-- IDEOLOGÍA:
-- - Centro: 10 (20%)
-- - Izquierda: 8 (16%)
-- - Derecha: 8 (16%)
-- - Independiente: 8 (16%)
-- - Centro-izquierda: 6 (12%)
-- - Centro-derecha: 6 (12%)
-- - Sin definir: 4 (8%)
--
-- RANGO DE EDAD:
-- - 18-25: 9 (18%)
-- - 26-35: 14 (28%)
-- - 36-45: 15 (30%)
-- - 46-55: 9 (18%)
-- - 56-65: 3 (6%)
--
-- NIVEL DE INGRESOS:
-- - 1-2_salarios: 11 (22%)
-- - 3-5_salarios: 19 (38%)
-- - 6-10_salarios: 11 (22%)
-- - mas_10_salarios: 9 (18%)
--
-- NIVEL EDUCACIÓN:
-- - secundaria_completa: 6 (12%)
-- - tecnico: 8 (16%)
-- - universitario_incompleto: 4 (8%)
-- - universitario_completo: 19 (38%)
-- - posgrado: 13 (26%)
--
-- ESTADO: Todos activos (100%)
-- DEPARTAMENTO: Putumayo (86) - 100%
-- =====================================================

SELECT 'Script ejecutado exitosamente. 50 votantes adicionales insertados (IDs 52-101)' as resultado;
