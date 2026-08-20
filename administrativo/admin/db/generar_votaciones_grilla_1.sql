-- =====================================================================
-- Script para generar votaciones simuladas realistas para la grilla ID 1
-- =====================================================================
-- Grilla: Estudio Grilla para PRESIDENTES
-- Candidatos: ALVARO URIBE (8), GUSTAVO BOLIVAR (15), GUSTAVO PETRO (6), VICKY DAVILA (14)
-- Votantes: 50 votantes registrados (IDs del 2 al 51)
-- =====================================================================

USE estadisticas_db;

-- Preguntas:
-- ID 57: ¿Conoce al candidato? (conoce) - respuestas: si, no
-- ID 37: Que Imagen Tiene de él(ella): (imagen) - respuestas: favorable, desfavorable
-- ID 55: ¿Votaría por él o por ella? (votaria) - respuestas: si, no

DELIMITER $$

DROP PROCEDURE IF EXISTS generar_votaciones_grilla_1$$

CREATE PROCEDURE generar_votaciones_grilla_1()
BEGIN
    DECLARE v_votante_id INT;
    DECLARE v_candidato_id INT;
    DECLARE v_sesion_id INT;
    DECLARE v_conoce VARCHAR(10);
    DECLARE v_imagen VARCHAR(20);
    DECLARE v_votaria VARCHAR(10);
    DECLARE v_rand_conoce DECIMAL(3,2);
    DECLARE v_rand_imagen DECIMAL(3,2);
    DECLARE v_rand_votaria DECIMAL(3,2);
    DECLARE v_contador_votante INT DEFAULT 2;
    DECLARE v_contador_candidato INT;

    -- Candidatos: ALVARO URIBE (8), GUSTAVO PETRO (6), VICKY DAVILA (14), GUSTAVO BOLIVAR (15)

    -- Limpiar votaciones existentes
    SET FOREIGN_KEY_CHECKS = 0;
    TRUNCATE TABLE tbl_grilla_respuestas;
    TRUNCATE TABLE tbl_grilla_sesion_votacion;
    SET FOREIGN_KEY_CHECKS = 1;

    -- Iterar por cada votante (IDs 2-51 = 50 votantes)
    WHILE v_contador_votante <= 51 DO
        -- Crear sesión de votación para este votante
        INSERT INTO tbl_grilla_sesion_votacion (tbl_grilla_id, tbl_votante_id, dtcreate)
        VALUES (1, v_contador_votante, DATE_SUB(NOW(), INTERVAL FLOOR(RAND() * 30) DAY));

        SET v_sesion_id = LAST_INSERT_ID();

        -- Votar por cada candidato
        -- GUSTAVO PETRO (ID 6) - Candidato con mayor popularidad
        SET v_rand_conoce = RAND();
        SET v_conoce = IF(v_rand_conoce < 0.85, 'si', 'no'); -- 85% lo conocen

        INSERT INTO tbl_grilla_respuestas (tbl_sesion_votacion_id, tbl_pregunta_id, tbl_participante_id, respuesta, dtcreate)
        VALUES (v_sesion_id, 57, 6, v_conoce, NOW());

        IF v_conoce = 'si' THEN
            SET v_rand_imagen = RAND();
            SET v_imagen = IF(v_rand_imagen < 0.55, 'favorable', 'desfavorable'); -- 55% imagen favorable

            INSERT INTO tbl_grilla_respuestas (tbl_sesion_votacion_id, tbl_pregunta_id, tbl_participante_id, respuesta, dtcreate)
            VALUES (v_sesion_id, 37, 6, v_imagen, NOW());

            -- Si tiene imagen favorable, 75% votaría por él; si es desfavorable, solo 15%
            SET v_rand_votaria = RAND();
            IF v_imagen = 'favorable' THEN
                SET v_votaria = IF(v_rand_votaria < 0.75, 'si', 'no');
            ELSE
                SET v_votaria = IF(v_rand_votaria < 0.15, 'si', 'no');
            END IF;

            INSERT INTO tbl_grilla_respuestas (tbl_sesion_votacion_id, tbl_pregunta_id, tbl_participante_id, respuesta, dtcreate)
            VALUES (v_sesion_id, 55, 6, v_votaria, NOW());
        END IF;

        -- ALVARO URIBE (ID 8) - Candidato polarizante
        SET v_rand_conoce = RAND();
        SET v_conoce = IF(v_rand_conoce < 0.90, 'si', 'no'); -- 90% lo conocen (muy conocido)

        INSERT INTO tbl_grilla_respuestas (tbl_sesion_votacion_id, tbl_pregunta_id, tbl_participante_id, respuesta, dtcreate)
        VALUES (v_sesion_id, 57, 8, v_conoce, NOW());

        IF v_conoce = 'si' THEN
            SET v_rand_imagen = RAND();
            SET v_imagen = IF(v_rand_imagen < 0.45, 'favorable', 'desfavorable'); -- 45% imagen favorable, 55% desfavorable (polarizante)

            INSERT INTO tbl_grilla_respuestas (tbl_sesion_votacion_id, tbl_pregunta_id, tbl_participante_id, respuesta, dtcreate)
            VALUES (v_sesion_id, 37, 8, v_imagen, NOW());

            SET v_rand_votaria = RAND();
            IF v_imagen = 'favorable' THEN
                SET v_votaria = IF(v_rand_votaria < 0.80, 'si', 'no'); -- Base fiel
            ELSE
                SET v_votaria = IF(v_rand_votaria < 0.10, 'si', 'no');
            END IF;

            INSERT INTO tbl_grilla_respuestas (tbl_sesion_votacion_id, tbl_pregunta_id, tbl_participante_id, respuesta, dtcreate)
            VALUES (v_sesion_id, 55, 8, v_votaria, NOW());
        END IF;

        -- VICKY DAVILA (ID 14) - Candidata con reconocimiento medio
        SET v_rand_conoce = RAND();
        SET v_conoce = IF(v_rand_conoce < 0.70, 'si', 'no'); -- 70% la conocen

        INSERT INTO tbl_grilla_respuestas (tbl_sesion_votacion_id, tbl_pregunta_id, tbl_participante_id, respuesta, dtcreate)
        VALUES (v_sesion_id, 57, 14, v_conoce, NOW());

        IF v_conoce = 'si' THEN
            SET v_rand_imagen = RAND();
            SET v_imagen = IF(v_rand_imagen < 0.50, 'favorable', 'desfavorable'); -- 50-50 imagen

            INSERT INTO tbl_grilla_respuestas (tbl_sesion_votacion_id, tbl_pregunta_id, tbl_participante_id, respuesta, dtcreate)
            VALUES (v_sesion_id, 37, 14, v_imagen, NOW());

            SET v_rand_votaria = RAND();
            IF v_imagen = 'favorable' THEN
                SET v_votaria = IF(v_rand_votaria < 0.60, 'si', 'no');
            ELSE
                SET v_votaria = IF(v_rand_votaria < 0.20, 'si', 'no');
            END IF;

            INSERT INTO tbl_grilla_respuestas (tbl_sesion_votacion_id, tbl_pregunta_id, tbl_participante_id, respuesta, dtcreate)
            VALUES (v_sesion_id, 55, 14, v_votaria, NOW());
        END IF;

        -- GUSTAVO BOLIVAR (ID 15) - Candidato con menor reconocimiento
        SET v_rand_conoce = RAND();
        SET v_conoce = IF(v_rand_conoce < 0.60, 'si', 'no'); -- 60% lo conocen

        INSERT INTO tbl_grilla_respuestas (tbl_sesion_votacion_id, tbl_pregunta_id, tbl_participante_id, respuesta, dtcreate)
        VALUES (v_sesion_id, 57, 15, v_conoce, NOW());

        IF v_conoce = 'si' THEN
            SET v_rand_imagen = RAND();
            SET v_imagen = IF(v_rand_imagen < 0.48, 'favorable', 'desfavorable'); -- 48% imagen favorable

            INSERT INTO tbl_grilla_respuestas (tbl_sesion_votacion_id, tbl_pregunta_id, tbl_participante_id, respuesta, dtcreate)
            VALUES (v_sesion_id, 37, 15, v_imagen, NOW());

            SET v_rand_votaria = RAND();
            IF v_imagen = 'favorable' THEN
                SET v_votaria = IF(v_rand_votaria < 0.65, 'si', 'no');
            ELSE
                SET v_votaria = IF(v_rand_votaria < 0.18, 'si', 'no');
            END IF;

            INSERT INTO tbl_grilla_respuestas (tbl_sesion_votacion_id, tbl_pregunta_id, tbl_participante_id, respuesta, dtcreate)
            VALUES (v_sesion_id, 55, 15, v_votaria, NOW());
        END IF;

        SET v_contador_votante = v_contador_votante + 1;
    END WHILE;

    SELECT 'Votaciones generadas correctamente' as mensaje;

END$$

DELIMITER ;

-- Ejecutar el procedimiento
CALL generar_votaciones_grilla_1();

-- Mostrar estadísticas generadas
SELECT
    'RESUMEN DE VOTACIONES GENERADAS' as titulo;

SELECT
    p.nombre_completo as Candidato,
    COUNT(DISTINCT CASE WHEN gr.tbl_pregunta_id = 57 AND gr.respuesta = 'si' THEN sv.tbl_votante_id END) as Conocen,
    COUNT(DISTINCT CASE WHEN gr.tbl_pregunta_id = 57 AND gr.respuesta = 'no' THEN sv.tbl_votante_id END) as No_Conocen,
    COUNT(DISTINCT CASE WHEN gr.tbl_pregunta_id = 37 AND gr.respuesta = 'favorable' THEN sv.tbl_votante_id END) as Imagen_Favorable,
    COUNT(DISTINCT CASE WHEN gr.tbl_pregunta_id = 37 AND gr.respuesta = 'desfavorable' THEN sv.tbl_votante_id END) as Imagen_Desfavorable,
    COUNT(DISTINCT CASE WHEN gr.tbl_pregunta_id = 55 AND gr.respuesta = 'si' THEN sv.tbl_votante_id END) as Votarian_Si,
    COUNT(DISTINCT CASE WHEN gr.tbl_pregunta_id = 55 AND gr.respuesta = 'no' THEN sv.tbl_votante_id END) as Votarian_No
FROM tbl_grilla_sesion_votacion sv
INNER JOIN tbl_grilla_respuestas gr ON gr.tbl_sesion_votacion_id = sv.id
INNER JOIN tbl_participantes p ON gr.tbl_participante_id = p.id
WHERE sv.tbl_grilla_id = 1
GROUP BY p.nombre_completo, p.id
ORDER BY Votarian_Si DESC;

SELECT
    COUNT(DISTINCT tbl_votante_id) as Total_Votantes,
    COUNT(*) as Total_Respuestas
FROM tbl_grilla_sesion_votacion
WHERE tbl_grilla_id = 1;

-- Limpiar procedimiento
DROP PROCEDURE IF EXISTS generar_votaciones_grilla_1;
