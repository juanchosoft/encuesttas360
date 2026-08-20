-- Insertar indicadores econométricos (Electoral, Imagen, Voto)
USE estadisticas_db;

-- ============================================
-- INDICADOR ELECTORAL ECONOMETRICO (7)
-- ============================================

INSERT INTO tbl_formulas (indicador, sigla, tipo_indicador, formula, explicacion, observaciones, enunciado_antes, enunciado_ahora, notas_adicionales, habilitado, tbl_usuario_id, dtcreate) VALUES

('Tasa electoral proyectada', 'TEP', 'Indicador Electoral Econométrico', '% DE CONOCIMIENTO ***PERSONAS QUE DICEN SI CONOCERLO (*)  TC', 'ES EL VALOR QUE SE REPLICA EN LA LINEA DEL TIEMPO EN VOTOS EN LA MIDA QUE EL CONOCIMIENTO DEL CANDIDATO CREZCA A MAYOR CRECIMIENTO DEL CONOCIMIENTO DEBEN REFLEJAR MAS VOTOS', 'Los cálculos son consistentes, no obstante, revisar los comentarios sobre la formulación y cálculo de la tasa de conversión.', 'N/A', 'N/A', NULL, 'si', 1, NOW()),

('Espacio de crecimiento del candidato', 'CRE*', 'Indicador Electoral Econométrico', '% PERSONAS QUE DICEN NO CONOCERLO * TC', 'ES LA CANTIDAD PORCENTUAL DE VOTANTES EN DONDE REPLICARA LA CONVERCIÓN Y LA TEP EN LA MEDIDA QUE EL PORCENTAJE DE DESCONOCIMIENTO BAJE', 'Los cálculos son consistentes, no obstante, revisar los comentarios sobre la formulación y cálculo de la tasa de conversión.', 'N/A', 'N/A', NULL, 'si', 1, NOW()),

('Tasa de conversión a votos', 'TC', 'Indicador Electoral Econométrico', '[(#PERSONAS QUE DICEN CONOCERLA - #PERSONAS CON IMAGEN FAVORABLE DEL CANDIDATO)+ (#PERSONAS CON IMAGEN FAVORABLE DEL CANDIDATO  - #PERSONAS QUE DICEN QUE SI VOTARIAN POR ESE CANDIDATO)]/#PERSONAS QUE DICEN CONOCER AL CANDIDATO', 'ES LA DIFERENCIA POSITIVA DE MERMAS ENTRE LOS QUE DICEN CONOCERLO Y LOS QUE DICEN VOTAR POR EL CON LA RESTRICCION DE LOS QUE DICEN TENER IMAGEN FAVORABLE ESTE VALOR ES QUE REPLICA EN PERIODO Y EN EL ESPACIO DE CRECIMIENTO PARA MEJORAR LOS INDICADORES DE ELECTORALES', 'Repetido. Cálculo que más me genera dudas y se usa para calcular otras cosas. Ver comentario previamente dado sobre esta tasa', 'N/A', 'N/A', NULL, 'si', 1, NOW()),

('Tasa electoral del candidato en esta grilla', 'TEG', 'Indicador Electoral Econométrico', 'CRE* / # CANDIDATOS EN LA GRILLA', 'DEL ESPACIO QUE TIENE EL CANDIDATO PARA CRECER LO COMPARTE CON LOS OTROS CANDIDATOS MEDIDOS EN LA GRILLA Y ES LA PORCION QUE A HOY LE CORRESPONDE DE ESE ESPACIO', 'Los cálculos son consistentes, no obstante, revisar los comentarios sobre la formulación y cálculo de la tasa de conversión.', 'N/A', 'N/A', NULL, 'si', 1, NOW()),

('Intención de voto convertida', 'IVC', 'Indicador Electoral Econométrico', '# PERSONAS QUE DICEN QUE SI VOTRIAN POR LE CANDIDATO / CANTIDAD DE PERSONAS QUE LE DIJERON QUE SI VOTARIA POR Y LO DEMAS CANDITOS DE GRILLA', 'ES LA CONVERSION DE GRILLA A INTENCION DE VOTO REFLEJANDO EL PORCENTAJE DE PERSONAS QUE DIJERON QUE SI VOTARIAN POR UN CANDIDATO ENTRE LOS OTROS CANDIDATOS QUE DIJERON QUE SI VOTARIAN TAMBIEN POR ELLOS', 'Los cálculos son consistentes, no obstante, revisar los comentarios sobre la formulación y cálculo de la tasa de conversión.', 'N/A', 'N/A', NULL, 'si', 1, NOW()),

('Intención de voto convertido en la grilla específica', 'IVG', 'Indicador Electoral Econométrico', 'IVC - TEG', 'ES LA INTENCION DE VOTO AJUSTADA AL MARGEN DE ERROR DEL ESTUDIO CON LOS NOMBRES ESTUDIADOS EN GRILLA ESPECIFICA UN NOMBRE DIFERENTE CAMBIARIA ESTE CONCEPTO', 'Los cálculos son consistentes, no obstante, revisar los comentarios sobre la formulación y cálculo de la tasa de conversión.', 'N/A', 'N/A', NULL, 'si', 1, NOW()),

('Tasa de conversión de la grilla', 'TCG', 'Indicador Electoral Econométrico', '{(TC/ # CANDIDATOS DE LA GRILLA) +  (TEG/ # CANDIDATOS DE LA GRILLA)', 'ES EL AJUSTE DEL ESPACIO DE CRECIMIENTO DEL CANDIDATO EN SUS INDICADORES A LA GRILLA ESPECIFICA CON EL MARGEN DE ERROR DEL ESTUDIO', 'Los cálculos son consistentes, no obstante, revisar los comentarios sobre la formulación y cálculo de la tasa de conversión.', 'N/A', 'N/A', NULL, 'si', 1, NOW());


-- ============================================
-- INDICADOR ECONOMETRICO DE IMAGEN (7)
-- ============================================

INSERT INTO tbl_formulas (indicador, sigla, tipo_indicador, formula, explicacion, observaciones, enunciado_antes, enunciado_ahora, notas_adicionales, habilitado, tbl_usuario_id, dtcreate) VALUES

('Tasa electoral de imagen favorable', 'TCI', 'Indicador Econométrico de Imagen', '(# PERSONAS QUE DICEN CONOCERLO - # QUE TIENE LA IMAGEN FAVORABLE) / MUESTRA', 'ES LA CIFRA QUE EL CANDIDATO OCUPA EN EL ESTUDIO CON LA COBERTURA EN MOMENTO ES LA CAPACIDAD DE CONVERTIR SU CONOCIMIENTO EN IMAGEN FAVORABLE', 'La fórmula y los cálculos son consistentes.', 'N/A', 'N/A', NULL, 'si', 1, NOW()),

('Índice de favorabilidad electoral', 'IFE', 'Indicador Econométrico de Imagen', '(+) % PERSONAS QUE DICEN CONOCER AL CANDIDATO  - PORCENTAJE DE PERSONAS QUE DICEN TENER IMAGEN FAVORABLE DE EL', 'ESTA DIFERENCIA POSITIVA DE PORCENTAJES DE CONOCIMIENTO Y FAVORABILIDAD... ES LA CIFRA DE REPLICACION EN LA LINEA DEL TIEMPO Y EL ESPACIO QUE TIENE EL CANDIDATO PARA SU CRECIMIENTO DE IMAGEN FAVORABLE, EN LA MEDIDA QUE ESTE INDICE AUMENTE AUMENTA LA FAVORABILIDAD DEL CANDIDATO Y POR TANTO SUS CONVERSIONES A VOTOS', 'La fórmula y los cálculos son consistentes.', 'N/A', 'N/A', NULL, 'si', 1, NOW()),

('Tasa de conversión a votos', 'TC', 'Indicador Econométrico de Imagen', '[(#PERSONAS QUE DICEN CONOCERLA - #PERSONAS CON IMAGEN FAVORABLE DEL CANDIDATO)+ (#PERSONAS CON IMAGEN FAVORABLE DEL CANDIDATO  - #PERSONAS QUE DICEN QUE SI VOTARIAN POR ESE CANDIDATO)]/#PERSONAS QUE DICEN CONOCER AL CANDIDATO', 'ES LA DIFERENCIA POSITIVA DE MERMAS ENTRE LOS QUE DICEN CONOCERLO Y LOS QUE DICEN VOTAR POR EL CON LA RESTRICCION DE LOS QUE DICEN TENER IMAGEN FAVORABLE ESTE VALOR ES QUE REPLICA EN PERIODO Y EN EL ESPACIO DE CRECIMIENTO PARA MEJORAR LOS INDICADORES DE ELECTORALES', 'Repetido. Cálculo que más me genera dudas y se usa para calcular otras cosas. Ver comentario previamente dado sobre esta tasa', 'N/A', 'N/A', NULL, 'si', 1, NOW()),

('Tasa de favorabilidad proyectada', 'TFP', 'Indicador Econométrico de Imagen', '1-TC', 'ES LA FAVORABILIDAD AJUSTADA AL MARGEN DE ERROR DEL ESTUDIO EN UNA GRILLA COMPARTIDA CON ESOS NOMBRES ESPECIFICOS SIRVE PARA CALCULAR LA TRANSFERENCIA O ENDOSO DE LA FAVORABILIDAD', 'Los cálculos son consistentes, no obstante, revisar los comentarios sobre la formulación y cálculo de la tasa de conversión.', 'N/A', 'N/A', NULL, 'si', 1, NOW()),

('Tasa de favorabilidad convertida', 'TFC', 'Indicador Econométrico de Imagen', 'TC * TFP', 'ES LA CONVERSION DE LA GRILLA A FAVORABILIDAD ASI COMO EL IVC ES A VOTOS EL TFC ES A FAVORABILIDAD DEL CANDIDATO ENTRE LOS OTROS CANDIDATOS', 'Los cálculos son consistentes, no obstante, revisar los comentarios sobre la formulación y cálculo de la tasa de conversión.', 'N/A', 'N/A', NULL, 'si', 1, NOW()),

('Tasa de desfavorabilidad proyectada', 'TDFP', 'Indicador Econométrico de Imagen', '1-TFP', 'ES LA DESFAVORABILIDAD AJUSTADA AL MARGEN DE ERROR DEL ESTUDIO EN UNA GRILLA COMPARTIDA CON ESOS NOMBRES ESPECIFICOS SIRVE PARA CALCULAR LA TRANSFERENCIA O ENDOSO DE LA FAVORABILIDAD', 'Los cálculos son consistentes, no obstante, revisar los comentarios sobre la formulación y cálculo de la tasa de conversión.', 'N/A', 'N/A', NULL, 'si', 1, NOW()),

('Resta de desfavorabilidad a votos', '(-)IVC', 'Indicador Econométrico de Imagen', '% PORCENTAJE DE DESFAVORABLE * TC', 'ES EL PORCENTAJE DE VOTOS PERDIDOS POR LA IMAGEN DESFAVORABLE DEL CANDIDATO', 'Los cálculos son consistentes, no obstante, revisar los comentarios sobre la formulación y cálculo de la tasa de conversión.', 'N/A', 'N/A', NULL, 'si', 1, NOW());


-- ============================================
-- INDICADOR ECONOMETRICO DE VOTO (8)
-- ============================================

INSERT INTO tbl_formulas (indicador, sigla, tipo_indicador, formula, explicacion, observaciones, enunciado_antes, enunciado_ahora, notas_adicionales, habilitado, tbl_usuario_id, dtcreate) VALUES

('Votos por conocimiento', 'VC', 'Indicador Econométrico de Voto', '(+) % QUE DICEN CONOCER AL CANDIDATO - % DE DE PERSONAS QUE DICEN QUE SI VOTARIAN POR EL CANDIDATO', 'ES EL PORCENTAJE DE PERSONAS QUE DICEN QUE VOTARIAN POR EL CANDIDATO POR EL CONOCIMIENTO INDEPENDIENTE DE LA IMAGEN FAVORABLE QUE ESTE TENGA', 'La fórmula de cálculo no es clara.', 'N/A', 'N/A', NULL, 'si', 1, NOW()),

('Crecimiento proyectado en intención de voto', 'CRE', 'Indicador Econométrico de Voto', 'TEG  + TCG', 'ES LA META ESPERADA DE CRECIMIENTO PORCENTUAL EN INTENCION DE VOTO EN TRACKING TRIMESTRALES EN UNA LINEA INFERIOR A 12 MESES CON EXACTAMENTE LOS MISMOS CANDIDATOS DEL ESTUDIO ANTERIOR AL SEGUIMIENTO', 'Los cálculos son consistentes, no obstante, revisar los comentarios sobre el cálculo de la tasa de conversión. De otro lado, no es una fórmula que se pueda sostener en el tiempo por la entrada y salida de candidatos.', 'N/A', 'N/A', NULL, 'si', 1, NOW()),

('Tasa de conversión a votos', 'TC', 'Indicador Econométrico de Voto', '[(#PERSONAS QUE DICEN CONOCERLA - #PERSONAS CON IMAGEN FAVORABLE DEL CANDIDATO)+ (#PERSONAS CON IMAGEN FAVORABLE DEL CANDIDATO  - #PERSONAS QUE DICEN QUE SI VOTARIAN POR ESE CANDIDATO)]/#PERSONAS QUE DICEN CONOCER AL CANDIDATO', 'ES LA DIFERENCIA POSITIVA DE MERMAS ENTRE LOS QUE DICEN CONOCERLO Y LOS QUE DICEN VOTAR POR EL CON LA RESTRICCION DE LOS QUE DICEN TENER IMAGEN FAVORABLE ESTE VALOR ES QUE REPLICA EN PERIODO Y EN EL ESPACIO DE CRECIMIENTO PARA MEJORAR LOS INDICADORES DE ELECTORALES', 'Repetido. Cálculo que más me genera dudas y se usa para calcular otras cosas. Ver comentario previamente dado sobre esta tasa', 'N/A', 'N/A', NULL, 'si', 1, NOW()),

('Vector de crecimiento electoral mensual', 'CREM', 'Indicador Econométrico de Voto', 'CRE / LINEA DEL TIEMPO AL MOMENTO ELECTORAL', 'ES LA META DE CRECIMIENTO MENSUAL PORCENTUAL DE LA INTENCION DE VOTO DENTRO DE LA LINEA DE TIEMPO A PARTIR DEL ESTUDIO VACE. ES LA LINEA DE AVANCE DE LA INTENCION DE VOTO CON LOS MISMO CANDIDATOS DE GRILLA DE LA LINEA BASE', 'Los cálculos son consistentes, no obstante, revisar los comentarios sobre la formulación y cálculo de la tasa de conversión. De otro lado, más que una meta que se fija la campaña es una meta indicativa, no es lo que me propongo sino en teoría debería crecer, no obstante, la línea base debería tener una actualización, no puede ser estática, sugiero actualizarsa con los candidatos en contienda cada 3 meses.', 'N/A', 'N/A', NULL, 'si', 1, NOW()),

('Piso de votos actual', 'PISO', 'Indicador Econométrico de Voto', 'VOTOS HISTORICOS DEL VPA * IVG', 'EN LA GRILLA MEDIDA ES CONVERTIR LA INTENCION DE VOTOS DE LA GRILLA EN % (IVG) EN CANTIDAD DE VOTOS (LINEA BASE DE VOTOS)', 'Los cálculos son consistentes, no obstante, revisar los comentarios sobre la formulación y cálculo de la tasa de conversión.', 'N/A', 'N/A', NULL, 'si', 1, NOW()),

('Techo de votos actual', 'TECHO', 'Indicador Econométrico de Voto', 'VOTOS HISTORICOS DEL VPA * TEP', 'EN LA LINEA DE TIEMPO PREVISTA HAY UNA TASA ELECTORAL PROYECTADA EN % Y SE CONVIERTE EN CANTIDAD DE VOTOS ES EL RANGO INTERPERCENTILICO ENTRE LA LINEA BASE O PISO HASTA EL MOMENTO ELECTORAL CON LA INTENCION DE VOTO EN EL MOMENTO DE LA MEDICION ESTE VALOR VARIA EN MEDIDA DE CRECIMIENTO O DECRECIMIENTO DE LA INTENCION DE VOTO CONVERTIDA O IVG', 'Los cálculos son consistentes, no obstante, revisar los comentarios sobre la formulación y cálculo de la tasa de conversión.', 'N/A', 'N/A', NULL, 'si', 1, NOW()),

('Meta de crecimiento mensual intención de voto', 'META', 'Indicador Econométrico de Voto', 'TEP / LINEA DEL TIEMPO EN MESES AL MOMENTO ELECTORAL', 'ES CRECIMIENTO EN % ESPERADO EN INTENCION DE VOTO DE MANERA MENSUAL SI LA TEP SE CUMPLE POR CRECIMIENTO DE LA GENTE DISPUESTA A VOTAR POR EL CANDIDATO AL SUBIR EL RECONOCIMIENTO Y LA FAVORABILIDAD Y POR SUBIR EN PRIORIZACION DE LAS TRANSFERENCIAS O ENDOSOS EN LA GRILLA', 'Los cálculos son consistentes, no obstante, revisar los comentarios sobre la formulación y cálculo de la tasa de conversión.', 'N/A', 'N/A', NULL, 'si', 1, NOW()),

('Votos a hoy según la encuesta realizada', 'AL 100%', 'Indicador Econométrico de Voto', 'ES LA SUMATORIA DE DISTRIBUCION MUESTRAL EN VOTOS REPRESENTADOS', 'CANTIDAD DE VOTOS DESGREGADOS EN LA DISTRIBUCION MUESTRAL GEOGRAFICA', 'La fórmula y los cálculos son consistentes.', 'N/A', 'N/A', NULL, 'si', 1, NOW()),

('Crecimiento mensual de votos en los que aún no conocen al candidato', 'VNC', 'Indicador Econométrico de Voto', '% PERSONAS QUE DICEN NO CONOCER AL CANDIDATO  (*)  CREM', 'EL VECTOR DE CRECIMIENTO MENSUAL SE CALCULA PARA CRECER O DECRECER CANDIDATOS EN UN TRACKING O DESK Y SE MULTIPLICA POR EL PORCENTAJE DE PERSONAS QUE DICEN NO CONOCER EL CANDIDATO AL BAJAR ESTE NUMERO BAJA EL VECTOR PERO CRECE LA IVC', 'Los cálculos son consistentes, no obstante, revisar los comentarios sobre la formulación y cálculo de la tasa de conversión.', 'N/A', 'N/A', NULL, 'si', 1, NOW());


-- Verificar inserción
SELECT COUNT(*) AS total_formulas FROM tbl_formulas;
SELECT tipo_indicador, COUNT(*) as cantidad
FROM tbl_formulas
GROUP BY tipo_indicador
ORDER BY tipo_indicador;
