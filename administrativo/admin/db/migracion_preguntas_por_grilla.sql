-- =====================================================
-- MIGRACIÓN: Asociación de Preguntas por Grilla Específica
-- =====================================================
-- Este script permite que diferentes grillas usen diferentes
-- conjuntos de preguntas, en lugar de compartir las mismas
-- =====================================================

-- Paso 1: Agregar columna tbl_grilla_id a la tabla de preguntas
-- =====================================================

ALTER TABLE tbl_preguntas_sub_preguntas_grilla
ADD COLUMN IF NOT EXISTS tbl_grilla_id INT NULL
    COMMENT 'ID de la grilla asociada. NULL = todas las grillas, ID específico = solo esa grilla';

-- Agregar índice para mejorar performance
ALTER TABLE tbl_preguntas_sub_preguntas_grilla
ADD INDEX IF NOT EXISTS idx_grilla_id (tbl_grilla_id);

-- Agregar clave foránea (opcional, solo si quieres integridad referencial)
ALTER TABLE tbl_preguntas_sub_preguntas_grilla
ADD CONSTRAINT fk_preguntas_grilla
    FOREIGN KEY (tbl_grilla_id)
    REFERENCES tbl_grilla(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE;


-- =====================================================
-- Paso 2: Actualizar Vista para incluir información de grilla
-- =====================================================

DROP VIEW IF EXISTS vw_preguntas_grilla_completas;

CREATE OR REPLACE VIEW vw_preguntas_grilla_completas AS
SELECT
    p.id,
    p.tipo_pregunta,
    p.texto_pregunta,
    p.codigo_pregunta,
    p.orden,
    p.pregunta_padre_id,
    p.opciones_respuesta,
    p.requiere_todas_si,
    p.habilita_subpreguntas,
    p.condicion_habilitacion,
    p.habilitado,
    p.tbl_grilla_id,

    -- Datos de la pregunta padre (si existe)
    padre.codigo_pregunta AS codigo_pregunta_padre,
    padre.texto_pregunta AS texto_pregunta_padre,

    -- Datos de la grilla asociada (si existe)
    g.grilla AS nombre_grilla,
    g.descripcion_grilla,

    -- Conteo de subpreguntas (si es pregunta principal)
    (SELECT COUNT(*)
     FROM tbl_preguntas_sub_preguntas_grilla sub
     WHERE sub.pregunta_padre_id = p.id) AS total_subpreguntas

FROM tbl_preguntas_sub_preguntas_grilla p
LEFT JOIN tbl_preguntas_sub_preguntas_grilla padre ON p.pregunta_padre_id = padre.id
LEFT JOIN tbl_grilla g ON p.tbl_grilla_id = g.id
WHERE p.habilitado = TRUE
ORDER BY p.tbl_grilla_id ASC, p.tipo_pregunta ASC, p.orden ASC;


-- =====================================================
-- Paso 3: Actualizar clase PreguntaGrilla para filtrar por grilla
-- =====================================================

-- NOTA: Esto se debe hacer en el archivo PHP admin/classes/PreguntaGrilla.php
-- Modificar el método obtenerPreguntasConSubpreguntas() para incluir:

/*
public static function obtenerPreguntasConSubpreguntas($rqst = [])
{
    $db = new DbConection();
    $pdo = $db->openConect();

    $grilla_id = isset($rqst['grilla_id']) ? intval($rqst['grilla_id']) : null;

    try {
        // Filtro WHERE dinámico
        $whereGrilla = '';
        $params = [];

        if ($grilla_id !== null) {
            $whereGrilla = "AND (tbl_grilla_id = :grilla_id OR tbl_grilla_id IS NULL)";
            $params[':grilla_id'] = $grilla_id;
        }

        // Obtener PREGUNTAS PRINCIPALES
        $qPreguntas = "SELECT
                        id, tipo_pregunta, texto_pregunta, codigo_pregunta,
                        orden, opciones_respuesta, requiere_todas_si,
                        habilita_subpreguntas, condicion_habilitacion
                       FROM " . $db->getTable('tbl_preguntas_sub_preguntas_grilla') . "
                       WHERE tipo_pregunta = 'pregunta'
                         AND habilitado = TRUE
                         $whereGrilla
                       ORDER BY orden ASC";

        $stmtPreguntas = $pdo->prepare($qPreguntas);
        $stmtPreguntas->execute($params);
        $preguntas = $stmtPreguntas->fetchAll(PDO::FETCH_ASSOC);

        // Obtener SUBPREGUNTAS
        $qSubpreguntas = "SELECT
                            id, tipo_pregunta, texto_pregunta, codigo_pregunta,
                            orden, pregunta_padre_id
                          FROM " . $db->getTable('tbl_preguntas_sub_preguntas_grilla') . "
                          WHERE tipo_pregunta = 'subpregunta'
                            AND habilitado = TRUE
                            $whereGrilla
                          ORDER BY orden ASC";

        $stmtSubpreguntas = $pdo->prepare($qSubpreguntas);
        $stmtSubpreguntas->execute($params);
        $subpreguntas = $stmtSubpreguntas->fetchAll(PDO::FETCH_ASSOC);

        $arrjson = array(
            'output' => array(
                'valid' => true,
                'response' => array(
                    'preguntas' => $preguntas,
                    'subpreguntas' => $subpreguntas
                )
            )
        );

    } catch (PDOException $e) {
        $arrjson = Util::error_general('Error al obtener preguntas: ' . $e->getMessage());
    } finally {
        $db->closeConect();
    }

    return $arrjson;
}
*/


-- =====================================================
-- Paso 4: Ejemplos de Uso
-- =====================================================

-- Ejemplo 1: Crear preguntas GLOBALES (para todas las grillas)
-- Solo dejar tbl_grilla_id en NULL
INSERT INTO tbl_preguntas_sub_preguntas_grilla
(tipo_pregunta, texto_pregunta, codigo_pregunta, orden, opciones_respuesta, habilita_subpreguntas, condicion_habilitacion, tbl_grilla_id, habilitado, tbl_usuario_id)
VALUES
('pregunta', '¿Conoce al candidato?', 'conoce_global', 1, '["si", "no"]', TRUE, 'si', NULL, TRUE, 2);


-- Ejemplo 2: Crear preguntas ESPECÍFICAS para una grilla
-- Supongamos que la grilla con id=1 es "Elecciones Presidenciales 2026"
SET @grilla_elecciones_2026 = 1;

INSERT INTO tbl_preguntas_sub_preguntas_grilla
(tipo_pregunta, texto_pregunta, codigo_pregunta, orden, opciones_respuesta, habilita_subpreguntas, condicion_habilitacion, tbl_grilla_id, habilitado, tbl_usuario_id)
VALUES
('pregunta', '¿Votaría por este candidato presidencial?', 'voto_presidencial', 1, '["si", "no"]', TRUE, 'si', @grilla_elecciones_2026, TRUE, 2);


-- Ejemplo 3: Consultar preguntas de una grilla específica
-- Preguntas de la grilla con id=1 (incluye NULL = globales)
SELECT * FROM vw_preguntas_grilla_completas
WHERE tbl_grilla_id = 1 OR tbl_grilla_id IS NULL
ORDER BY orden ASC;


-- Ejemplo 4: Consultar solo preguntas globales
SELECT * FROM vw_preguntas_grilla_completas
WHERE tbl_grilla_id IS NULL;


-- Ejemplo 5: Consultar preguntas específicas de cada grilla
SELECT
    g.id AS grilla_id,
    g.grilla AS nombre_grilla,
    COUNT(p.id) AS total_preguntas
FROM tbl_grilla g
LEFT JOIN tbl_preguntas_sub_preguntas_grilla p ON (p.tbl_grilla_id = g.id OR p.tbl_grilla_id IS NULL)
WHERE p.habilitado = TRUE
GROUP BY g.id, g.grilla
ORDER BY g.id;


-- =====================================================
-- Paso 5: Actualizar candidato.php para pasar grilla_id
-- =====================================================

-- NOTA: En el archivo candidato.php, cambiar:

/*
// ANTES:
$preguntasResponse = PreguntaGrilla::obtenerPreguntasConSubpreguntas([]);

// DESPUÉS:
$grilla_id = isset($_GET['grilla_id']) ? intval($_GET['grilla_id']) : null;
$preguntasResponse = PreguntaGrilla::obtenerPreguntasConSubpreguntas(['grilla_id' => $grilla_id]);
*/


-- =====================================================
-- CONSULTAS ÚTILES
-- =====================================================

-- Ver distribución de preguntas por grilla
SELECT
    CASE
        WHEN tbl_grilla_id IS NULL THEN 'GLOBALES (todas las grillas)'
        ELSE CONCAT('Grilla ID: ', tbl_grilla_id, ' - ', (SELECT grilla FROM tbl_grilla WHERE id = tbl_grilla_id LIMIT 1))
    END AS grilla,
    tipo_pregunta,
    COUNT(*) AS total
FROM tbl_preguntas_sub_preguntas_grilla
WHERE habilitado = TRUE
GROUP BY tbl_grilla_id, tipo_pregunta
ORDER BY tbl_grilla_id ASC, tipo_pregunta ASC;


-- Ver jerarquía completa con grillas
SELECT
    CASE
        WHEN tbl_grilla_id IS NULL THEN '[GLOBAL]'
        ELSE CONCAT('[Grilla ', tbl_grilla_id, ']')
    END AS scope,
    CASE
        WHEN tipo_pregunta = 'pregunta' THEN CONCAT('¶ ', texto_pregunta)
        ELSE CONCAT('     ', texto_pregunta)
    END AS jerarquia,
    codigo_pregunta,
    orden
FROM tbl_preguntas_sub_preguntas_grilla
WHERE habilitado = TRUE
ORDER BY tbl_grilla_id ASC, tipo_pregunta ASC, orden ASC;


-- =====================================================
-- NOTAS IMPORTANTES
-- =====================================================

/*
VENTAJAS:

1. FLEXIBILIDAD:
   - Preguntas globales (NULL) se usan en todas las grillas
   - Preguntas específicas solo aparecen en su grilla asignada

2. REUTILIZACIÓN:
   - Preguntas comunes se definen una vez (NULL)
   - Preguntas especiales se crean solo cuando se necesitan

3. MANTENIMIENTO:
   - Cambiar una pregunta global afecta a todas las grillas
   - Cambiar una pregunta específica solo afecta a esa grilla

4. ESCALABILIDAD:
   - Soporta N grillas con M preguntas cada una
   - Sin duplicar código ni datos

CASOS DE USO:

1. Elecciones Presidenciales:
   - Preguntas específicas sobre candidatos presidenciales
   - Subpreguntas sobre planes de gobierno

2. Elecciones Regionales:
   - Preguntas sobre conocimiento local
   - Subpreguntas sobre proyectos regionales

3. Encuestas de Imagen:
   - Preguntas generales de conocimiento e imagen
   - Sin subpreguntas complejas

MIGRACIÓN DE DATOS EXISTENTES:

Si ya tienes preguntas en la tabla y quieres que sean globales:

UPDATE tbl_preguntas_sub_preguntas_grilla
SET tbl_grilla_id = NULL
WHERE tbl_grilla_id IS NULL OR tbl_grilla_id = 0;

Si quieres asociar preguntas existentes a una grilla específica:

UPDATE tbl_preguntas_sub_preguntas_grilla
SET tbl_grilla_id = 1  -- ID de la grilla
WHERE codigo_pregunta IN ('conoce', 'imagen', 'votaria');
*/
