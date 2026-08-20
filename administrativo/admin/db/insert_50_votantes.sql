-- =====================================================================
-- Script para insertar 50 votantes con datos variados
-- =====================================================================
-- Incluye diversidad en ideología, edad, ingresos, género, educación, etc.
-- =====================================================================

USE estadisticas_db;

-- Insertar 50 votantes con datos variados
INSERT INTO tbl_votantes (
    nombre_completo, ideologia, rango_edad, nivel_ingresos, email, username, password,
    genero, codigo_departamento, codigo_municipio, comuna, barrio, nivel_educacion,
    ocupacion, estado, tbl_usuario_id
) VALUES
-- Votantes 1-10: Variedad de ideologías y rangos de edad
('María Fernández López', 'centro', '26-35', '3-5_salarios', 'maria.fernandez@email.com', 'mfernandez', '$2y$10$abcdefghijklmnopqrstuv', 'femenino', '86', '86001', 'Comuna 1', 'Centro', 'universitario_completo', 'Profesora', 'activo', 1),
('Carlos Andrés Rodríguez', 'centro_derecha', '36-45', '6-10_salarios', 'carlos.rodriguez@email.com', 'carodriguez', '$2y$10$abcdefghijklmnopqrstuv', 'masculino', '86', '86001', 'Comuna 2', 'El Bosque', 'posgrado', 'Ingeniero', 'activo', 1),
('Ana Lucía Martínez', 'izquierda', '18-25', '1-2_salarios', 'ana.martinez@email.com', 'amartinez', '$2y$10$abcdefghijklmnopqrstuv', 'femenino', '86', '86001', 'Comuna 1', 'San Rafael', 'universitario_incompleto', 'Estudiante', 'activo', 1),
('Juan Pablo Gómez', 'derecha', '46-55', 'mas_10_salarios', 'juan.gomez@email.com', 'jpgomez', '$2y$10$abcdefghijklmnopqrstuv', 'masculino', '86', '86001', 'Comuna 3', 'Los Pinos', 'universitario_completo', 'Empresario', 'activo', 1),
('Sofía Hernández Castro', 'centro_izquierda', '26-35', '3-5_salarios', 'sofia.hernandez@email.com', 'shernandez', '$2y$10$abcdefghijklmnopqrstuv', 'femenino', '86', '86001', 'Comuna 2', 'La Esperanza', 'tecnologo', 'Contadora', 'activo', 1),
('Diego Fernando Sánchez', 'independiente', '36-45', '3-5_salarios', 'diego.sanchez@email.com', 'dfsanchez', '$2y$10$abcdefghijklmnopqrstuv', 'masculino', '86', '86001', 'Comuna 1', 'Villa Nueva', 'universitario_completo', 'Abogado', 'activo', 1),
('Laura Patricia Ramírez', 'sin_definir', '18-25', 'menos_1_salario', 'laura.ramirez@email.com', 'lramirez', '$2y$10$abcdefghijklmnopqrstuv', 'femenino', '86', '86001', 'Comuna 4', 'El Carmen', 'secundaria_completa', 'Comerciante', 'activo', 1),
('Roberto Carlos Díaz', 'centro', '56-65', '6-10_salarios', 'roberto.diaz@email.com', 'rcdiaz', '$2y$10$abcdefghijklmnopqrstuv', 'masculino', '86', '86001', 'Comuna 3', 'Las Palmas', 'universitario_completo', 'Médico', 'activo', 1),
('Gabriela Torres Muñoz', 'izquierda', '26-35', '1-2_salarios', 'gabriela.torres@email.com', 'gtorres', '$2y$10$abcdefghijklmnopqrstuv', 'femenino', '86', '86001', 'Comuna 1', 'La Paz', 'tecnico', 'Enfermera', 'activo', 1),
('Andrés Felipe Vargas', 'derecha', '46-55', 'mas_10_salarios', 'andres.vargas@email.com', 'afvargas', '$2y$10$abcdefghijklmnopqrstuv', 'masculino', '86', '86001', 'Comuna 2', 'El Country', 'posgrado', 'Economista', 'activo', 1),

-- Votantes 11-20: Diversidad de género y nivel educativo
('Valentina Rojas Silva', 'centro_izquierda', '18-25', '1-2_salarios', 'valentina.rojas@email.com', 'vrojas', '$2y$10$abcdefghijklmnopqrstuv', 'femenino', '86', '86001', 'Comuna 1', 'San José', 'universitario_incompleto', 'Estudiante', 'activo', 1),
('Miguel Ángel Castillo', 'independiente', '36-45', '3-5_salarios', 'miguel.castillo@email.com', 'macastillo', '$2y$10$abcdefghijklmnopqrstuv', 'masculino', '86', '86001', 'Comuna 3', 'La Aurora', 'tecnologo', 'Diseñador', 'activo', 1),
('Carolina Jiménez Pérez', 'centro', '26-35', '3-5_salarios', 'carolina.jimenez@email.com', 'cjimenez', '$2y$10$abcdefghijklmnopqrstuv', 'femenino', '86', '86001', 'Comuna 2', 'Las Acacias', 'universitario_completo', 'Arquitecta', 'activo', 1),
('Pedro José Morales', 'sin_definir', '66+', '1-2_salarios', 'pedro.morales@email.com', 'pjmorales', '$2y$10$abcdefghijklmnopqrstuv', 'masculino', '86', '86001', 'Comuna 4', 'Villa Hermosa', 'primaria_completa', 'Pensionado', 'activo', 1),
('Isabella Ruiz Ortega', 'izquierda', '18-25', 'menos_1_salario', 'isabella.ruiz@email.com', 'iruiz', '$2y$10$abcdefghijklmnopqrstuv', 'femenino', '86', '86001', 'Comuna 1', 'La Victoria', 'secundaria_incompleta', 'Auxiliar', 'activo', 1),
('Sebastián Herrera López', 'centro_derecha', '26-35', '6-10_salarios', 'sebastian.herrera@email.com', 'sherrera', '$2y$10$abcdefghijklmnopqrstuv', 'masculino', '86', '86001', 'Comuna 3', 'Los Rosales', 'universitario_completo', 'Gerente', 'activo', 1),
('Camila Andrea Cruz', 'centro', '36-45', '3-5_salarios', 'camila.cruz@email.com', 'cacruz', '$2y$10$abcdefghijklmnopqrstuv', 'femenino', '86', '86001', 'Comuna 2', 'San Antonio', 'tecnologo', 'Administradora', 'activo', 1),
('Fernando Patiño Ríos', 'derecha', '46-55', '6-10_salarios', 'fernando.patino@email.com', 'fpatino', '$2y$10$abcdefghijklmnopqrstuv', 'masculino', '86', '86001', 'Comuna 3', 'El Poblado', 'posgrado', 'Consultor', 'activo', 1),
('Daniela Mendoza Silva', 'independiente', '26-35', '3-5_salarios', 'daniela.mendoza@email.com', 'dmendoza', '$2y$10$abcdefghijklmnopqrstuv', 'femenino', '86', '86001', 'Comuna 1', 'La Floresta', 'universitario_completo', 'Periodista', 'activo', 1),
('Luis Eduardo Ortiz', 'centro_izquierda', '56-65', '3-5_salarios', 'luis.ortiz@email.com', 'leortiz', '$2y$10$abcdefghijklmnopqrstuv', 'masculino', '86', '86001', 'Comuna 2', 'El Prado', 'universitario_completo', 'Docente', 'activo', 1),

-- Votantes 21-30: Variedad de ocupaciones y estados
('Paula Andrea Reyes', 'izquierda', '18-25', '1-2_salarios', 'paula.reyes@email.com', 'pareyes', '$2y$10$abcdefghijklmnopqrstuv', 'femenino', '86', '86001', 'Comuna 4', 'Brisas del Norte', 'universitario_incompleto', 'Practicante', 'activo', 1),
('Javier Alejandro Gil', 'sin_definir', '36-45', '3-5_salarios', 'javier.gil@email.com', 'jagil', '$2y$10$abcdefghijklmnopqrstuv', 'masculino', '86', '86001', 'Comuna 2', 'Los Alpes', 'tecnico', 'Técnico IT', 'activo', 1),
('Natalia Garzón Romero', 'centro', '26-35', '6-10_salarios', 'natalia.garzon@email.com', 'ngarzon', '$2y$10$abcdefghijklmnopqrstuv', 'femenino', '86', '86001', 'Comuna 3', 'Santa Bárbara', 'posgrado', 'Investigadora', 'activo', 1),
('Ricardo Montes Castro', 'derecha', '46-55', 'mas_10_salarios', 'ricardo.montes@email.com', 'rmontes', '$2y$10$abcdefghijklmnopqrstuv', 'masculino', '86', '86001', 'Comuna 3', 'Club Campestre', 'universitario_completo', 'Empresario', 'inactivo', 1),
('Liliana Soto Vargas', 'centro_derecha', '36-45', '3-5_salarios', 'liliana.soto@email.com', 'lsoto', '$2y$10$abcdefghijklmnopqrstuv', 'femenino', '86', '86001', 'Comuna 1', 'San Martín', 'universitario_completo', 'Psicóloga', 'activo', 1),
('Oscar Mauricio Peña', 'independiente', '26-35', '3-5_salarios', 'oscar.pena@email.com', 'ompena', '$2y$10$abcdefghijklmnopqrstuv', 'masculino', '86', '86001', 'Comuna 2', 'La Campiña', 'tecnologo', 'Contador', 'activo', 1),
('Marcela Castro Pinzón', 'izquierda', '18-25', '1-2_salarios', 'marcela.castro@email.com', 'mcastro', '$2y$10$abcdefghijklmnopqrstuv', 'femenino', '86', '86001', 'Comuna 4', 'Villa del Río', 'secundaria_completa', 'Dependiente', 'activo', 1),
('Héctor Fabio Lara', 'centro', '56-65', '6-10_salarios', 'hector.lara@email.com', 'hflara', '$2y$10$abcdefghijklmnopqrstuv', 'masculino', '86', '86001', 'Comuna 3', 'Los Cerezos', 'universitario_completo', 'Odontólogo', 'activo', 1),
('Sandra Milena Arias', 'centro_izquierda', '36-45', '3-5_salarios', 'sandra.arias@email.com', 'smarias', '$2y$10$abcdefghijklmnopqrstuv', 'femenino', '86', '86001', 'Comuna 2', 'La Riviera', 'universitario_completo', 'Trabajadora Social', 'activo', 1),
('Álvaro José Castaño', 'derecha', '46-55', '6-10_salarios', 'alvaro.castano@email.com', 'ajcastano', '$2y$10$abcdefghijklmnopqrstuv', 'masculino', '86', '86001', 'Comuna 3', 'Los Naranjos', 'posgrado', 'Auditor', 'activo', 1),

-- Votantes 31-40: Más variedad en nivel de ingresos y educación
('Andrea Carolina Vega', 'sin_definir', '18-25', 'menos_1_salario', 'andrea.vega@email.com', 'acvega', '$2y$10$abcdefghijklmnopqrstuv', 'femenino', '86', '86001', 'Comuna 4', 'El Mirador', 'secundaria_incompleta', 'Mesera', 'activo', 1),
('Julián David Rojas', 'centro', '26-35', '3-5_salarios', 'julian.rojas@email.com', 'jdrojas', '$2y$10$abcdefghijklmnopqrstuv', 'masculino', '86', '86001', 'Comuna 2', 'La Colina', 'tecnico', 'Mecánico', 'activo', 1),
('Paola Alejandra Mejía', 'centro_derecha', '36-45', '6-10_salarios', 'paola.mejia@email.com', 'pamejia', '$2y$10$abcdefghijklmnopqrstuv', 'femenino', '86', '86001', 'Comuna 3', 'San Fernando', 'universitario_completo', 'Directora', 'activo', 1),
('Esteban Moreno Díaz', 'izquierda', '18-25', '1-2_salarios', 'esteban.moreno@email.com', 'emoreno', '$2y$10$abcdefghijklmnopqrstuv', 'masculino', '86', '86001', 'Comuna 1', 'La Unión', 'universitario_incompleto', 'Activista', 'activo', 1),
('Lorena Patricia Duque', 'independiente', '46-55', '3-5_salarios', 'lorena.duque@email.com', 'lpduque', '$2y$10$abcdefghijklmnopqrstuv', 'femenino', '86', '86001', 'Comuna 2', 'Las Delicias', 'tecnologo', 'Secretaria', 'activo', 1),
('Mauricio Andrés Cano', 'centro', '26-35', 'mas_10_salarios', 'mauricio.cano@email.com', 'macano', '$2y$10$abcdefghijklmnopqrstuv', 'masculino', '86', '86001', 'Comuna 3', 'Los Olivos', 'posgrado', 'Director Financiero', 'activo', 1),
('Claudia Isabel Salazar', 'derecha', '56-65', '6-10_salarios', 'claudia.salazar@email.com', 'cisalazar', '$2y$10$abcdefghijklmnopqrstuv', 'femenino', '86', '86001', 'Comuna 3', 'Santa Mónica', 'universitario_completo', 'Abogada', 'activo', 1),
('Jairo Alberto Muñoz', 'centro_izquierda', '36-45', '3-5_salarios', 'jairo.munoz@email.com', 'jamunoz', '$2y$10$abcdefghijklmnopqrstuv', 'masculino', '86', '86001', 'Comuna 1', 'El Recreo', 'universitario_completo', 'Ingeniero Civil', 'activo', 1),
('Viviana Marcela León', 'sin_definir', '26-35', '1-2_salarios', 'viviana.leon@email.com', 'vmleon', '$2y$10$abcdefghijklmnopqrstuv', 'femenino', '86', '86001', 'Comuna 4', 'Villa María', 'secundaria_completa', 'Ama de Casa', 'activo', 1),
('Jorge Luis Ospina', 'izquierda', '18-25', 'menos_1_salario', 'jorge.ospina@email.com', 'jlospina', '$2y$10$abcdefghijklmnopqrstuv', 'masculino', '86', '86001', 'Comuna 1', 'El Porvenir', 'universitario_incompleto', 'Vendedor', 'suspendido', 1),

-- Votantes 41-50: Últimos registros con mayor diversidad
('Martha Cecilia Rincón', 'centro', '66+', '3-5_salarios', 'martha.rincon@email.com', 'mcrincon', '$2y$10$abcdefghijklmnopqrstuv', 'femenino', '86', '86001', 'Comuna 2', 'Los Sauces', 'primaria_completa', 'Pensionada', 'activo', 1),
('Felipe Augusto Parra', 'independiente', '46-55', '6-10_salarios', 'felipe.parra@email.com', 'faparra', '$2y$10$abcdefghijklmnopqrstuv', 'masculino', '86', '86001', 'Comuna 3', 'El Parque', 'universitario_completo', 'Contador Público', 'activo', 1),
('Diana Marcela Suárez', 'centro_derecha', '26-35', '3-5_salarios', 'diana.suarez@email.com', 'dmsuarez', '$2y$10$abcdefghijklmnopqrstuv', 'femenino', '86', '86001', 'Comuna 2', 'La Pradera', 'tecnologo', 'Analista', 'activo', 1),
('Raúl Eduardo Herrera', 'izquierda', '36-45', '1-2_salarios', 'raul.herrera@email.com', 'reherrera', '$2y$10$abcdefghijklmnopqrstuv', 'masculino', '86', '86001', 'Comuna 4', 'Ciudadela Norte', 'tecnico', 'Electricista', 'activo', 1),
('Beatriz Elena Acosta', 'derecha', '56-65', 'mas_10_salarios', 'beatriz.acosta@email.com', 'beacosta', '$2y$10$abcdefghijklmnopqrstuv', 'femenino', '86', '86001', 'Comuna 3', 'Los Laureles', 'posgrado', 'Empresaria', 'activo', 1),
('Nelson Javier Gutiérrez', 'sin_definir', '26-35', '3-5_salarios', 'nelson.gutierrez@email.com', 'njgutierrez', '$2y$10$abcdefghijklmnopqrstuv', 'masculino', '86', '86001', 'Comuna 1', 'San Pedro', 'universitario_completo', 'Veterinario', 'activo', 1),
('Gloria Patricia Campos', 'centro', '46-55', '3-5_salarios', 'gloria.campos@email.com', 'gpcampos', '$2y$10$abcdefghijklmnopqrstuv', 'femenino', '86', '86001', 'Comuna 2', 'Las Brisas', 'universitario_completo', 'Farmacéutica', 'activo', 1),
('Germán Darío Montoya', 'centro_izquierda', '36-45', '6-10_salarios', 'german.montoya@email.com', 'gdmontoya', '$2y$10$abcdefghijklmnopqrstuv', 'masculino', '86', '86001', 'Comuna 3', 'El Refugio', 'posgrado', 'Ingeniero de Sistemas', 'activo', 1),
('Yolanda Cristina Mesa', 'independiente', '66+', '1-2_salarios', 'yolanda.mesa@email.com', 'ycmesa', '$2y$10$abcdefghijklmnopqrstuv', 'femenino', '86', '86001', 'Comuna 4', 'Villa Esperanza', 'secundaria_completa', 'Pensionada', 'activo', 1),
('Hernán Darío Castillo', 'derecha', '46-55', 'mas_10_salarios', 'hernan.castillo@email.com', 'hdcastillo', '$2y$10$abcdefghijklmnopqrstuv', 'otro', '86', '86001', 'Comuna 3', 'Alto Prado', 'universitario_completo', 'Inversionista', 'activo', 1);

-- Verificar inserción
SELECT
    COUNT(*) as total_votantes,
    SUM(CASE WHEN genero = 'femenino' THEN 1 ELSE 0 END) as mujeres,
    SUM(CASE WHEN genero = 'masculino' THEN 1 ELSE 0 END) as hombres,
    SUM(CASE WHEN estado = 'activo' THEN 1 ELSE 0 END) as activos,
    SUM(CASE WHEN estado = 'inactivo' THEN 1 ELSE 0 END) as inactivos,
    SUM(CASE WHEN estado = 'suspendido' THEN 1 ELSE 0 END) as suspendidos
FROM tbl_votantes
WHERE email LIKE '%@email.com';

SELECT
    ideologia,
    COUNT(*) as cantidad
FROM tbl_votantes
WHERE email LIKE '%@email.com'
GROUP BY ideologia
ORDER BY cantidad DESC;

SELECT
    rango_edad,
    COUNT(*) as cantidad
FROM tbl_votantes
WHERE email LIKE '%@email.com'
GROUP BY rango_edad
ORDER BY FIELD(rango_edad, '18-25', '26-35', '36-45', '46-55', '56-65', '66+');
