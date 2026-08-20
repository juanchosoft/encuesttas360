<?php

/**
 * Clase RespuestaCuestionario
 * Gestiona las respuestas de los cuestionarios (Ficha Técnica de Encuesta)
 */
class RespuestaCuestionario
{
    /**
     * Obtiene votantes disponibles (que no han contestado) para una ficha técnica
     * @param array $rqst Parámetros de búsqueda
     * @return array Resultado de la operación
     */
    public static function getVotantesDisponibles($rqst)
    {
        $fichaTecnicaId = isset($rqst['ficha_tecnica_id']) ? intval($rqst['ficha_tecnica_id']) : 0;

        if ($fichaTecnicaId === 0) {
            return Util::error_missing_data_description('ID de ficha técnica requerido');
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            // Obtener votantes que NO han contestado este cuestionario
            $q = "SELECT v.id, v.nombre_completo, v.username, v.email
                FROM " . $db->getTable('tbl_votantes') . " v
                WHERE v.estado = 'activo'
                AND v.id NOT IN (
                    SELECT DISTINCT i.tbl_votante_id
                    FROM " . $db->getTable('tbl_cuestionario_intentos') . " i
                    WHERE i.tbl_ficha_tecnica_encuesta_id = :ficha_tecnica_id
                    AND i.tbl_votante_id IS NOT NULL
                )
                ORDER BY v.nombre_completo ASC";

            $stmt = $pdo->prepare($q);
            $stmt->execute([':ficha_tecnica_id' => $fichaTecnicaId]);
            $votantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $db->closeConect();

            return array('output' => array('valid' => true, 'response' => $votantes));

        } catch (Exception $e) {
            $db->closeConect();
            return Util::error_general('Error al obtener votantes disponibles: ' . $e->getMessage());
        }
    }

    /**
     * Guarda las respuestas de un cuestionario
     * @param array $rqst Datos de la solicitud
     * @return array Resultado de la operación
     */
    public static function save($rqst)
    {
        // Validar que venga el JSON con los datos
        if (!isset($rqst['data']) || empty($rqst['data'])) {
            return Util::error_missing_data();
        }

        $data = json_decode($rqst['data'], true);

        if (!$data) {
            return Util::error_missing_data_description('Los datos no son válidos');
        }

        // Validar campos requeridos
        if (empty($data['ficha_tecnica_id']) || empty($data['tbl_votante_id'])) {
            return Util::error_missing_data_description('Faltan datos obligatorios (ficha técnica o votante)');
        }

        if (empty($data['preguntas']) || !is_array($data['preguntas'])) {
            return Util::error_missing_data_description('No se enviaron respuestas de preguntas');
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            // Verificar si el votante ya respondió este cuestionario
            $qVerificar = "SELECT id FROM " . $db->getTable('tbl_cuestionario_intentos') . "
                WHERE tbl_ficha_tecnica_encuesta_id = :ficha_tecnica_id
                AND tbl_votante_id = :tbl_votante_id
                LIMIT 1";

            $stmtVerificar = $pdo->prepare($qVerificar);
            $stmtVerificar->execute([
                ':ficha_tecnica_id' => $data['ficha_tecnica_id'],
                ':tbl_votante_id' => $data['tbl_votante_id']
            ]);

            if ($stmtVerificar->fetch()) {
                $db->closeConect();
                return Util::error_missing_data_description('Este votante ya ha respondido este cuestionario');
            }

            // Iniciar transacción
            $pdo->beginTransaction();

            // 1. Insertar el intento de respuesta (cabecera)
            $qIntento = "INSERT INTO " . $db->getTable('tbl_cuestionario_intentos') . "
                (tbl_ficha_tecnica_encuesta_id, tbl_votante_id, fecha_respuesta, dtcreate)
                VALUES (:ficha_tecnica_id, :tbl_votante_id, NOW(), NOW())";

            $stmtIntento = $pdo->prepare($qIntento);
            $stmtIntento->execute([
                ':ficha_tecnica_id' => $data['ficha_tecnica_id'],
                ':tbl_votante_id' => $data['tbl_votante_id']
            ]);

            $intentoId = $pdo->lastInsertId();

            // 2. Insertar cada respuesta de pregunta
            $qRespuesta = "INSERT INTO " . $db->getTable('tbl_cuestionario_respuestas') . "
                (tbl_intento_id, tbl_pregunta_id, tbl_opcion_respuesta_id, respuesta_texto, dtcreate)
                VALUES (:intento_id, :pregunta_id, :opcion_id, :texto, NOW())";

            $stmtRespuesta = $pdo->prepare($qRespuesta);

            foreach ($data['preguntas'] as $pregunta) {
                $preguntaId = $pregunta['pregunta_id'];

                // Si hay opciones seleccionadas (radio/checkbox)
                if (!empty($pregunta['opciones']) && is_array($pregunta['opciones'])) {
                    foreach ($pregunta['opciones'] as $opcionId) {
                        $stmtRespuesta->execute([
                            ':intento_id' => $intentoId,
                            ':pregunta_id' => $preguntaId,
                            ':opcion_id' => $opcionId,
                            ':texto' => null
                        ]);
                    }
                }

                // Si hay respuesta de texto (textarea)
                if (!empty($pregunta['texto'])) {
                    $stmtRespuesta->execute([
                        ':intento_id' => $intentoId,
                        ':pregunta_id' => $preguntaId,
                        ':opcion_id' => null,
                        ':texto' => $pregunta['texto']
                    ]);
                }
            }

            // Commit de la transacción
            $pdo->commit();
            $db->closeConect();

            return [
                'output' => [
                    'valid' => true,
                    'message' => 'Respuestas guardadas correctamente',
                    'intento_id' => $intentoId
                ]
            ];

        } catch (Exception $e) {
            // Rollback en caso de error
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $db->closeConect();

            return Util::error_general('Error al guardar respuestas: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene todas las respuestas de una ficha técnica
     * @param array $rqst Parámetros de búsqueda
     * @return array Resultado de la operación
     */
    public static function getAll($rqst)
    {
        $fichaTecnicaId = isset($rqst['ficha_tecnica_id']) ? intval($rqst['ficha_tecnica_id']) : 0;
        $intentoId = isset($rqst['intento_id']) ? intval($rqst['intento_id']) : 0;

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $q = "SELECT
                    i.id as intento_id,
                    i.tbl_ficha_tecnica_encuesta_id,
                    i.nombre_respondiente,
                    i.identificacion_respondiente,
                    i.email_respondiente,
                    i.telefono_respondiente,
                    i.fecha_respuesta,
                    i.dtcreate,
                    COUNT(DISTINCT r.tbl_pregunta_id) as total_preguntas_respondidas
                FROM " . $db->getTable('tbl_cuestionario_intentos') . " i
                LEFT JOIN " . $db->getTable('tbl_cuestionario_respuestas') . " r ON i.id = r.tbl_intento_id
                WHERE 1=1";

            $params = [];

            if ($intentoId > 0) {
                $q .= " AND i.id = :intento_id";
                $params[':intento_id'] = $intentoId;
            } elseif ($fichaTecnicaId > 0) {
                $q .= " AND i.tbl_ficha_tecnica_encuesta_id = :ficha_tecnica_id";
                $params[':ficha_tecnica_id'] = $fichaTecnicaId;
            }

            $q .= " GROUP BY i.id ORDER BY i.dtcreate DESC";

            $stmt = $pdo->prepare($q);
            $stmt->execute($params);
            $intentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $db->closeConect();

            return [
                'output' => [
                    'valid' => true,
                    'response' => $intentos
                ]
            ];

        } catch (Exception $e) {
            $db->closeConect();
            return Util::error_general('Error al obtener respuestas: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene el detalle de respuestas de un intento específico
     * @param array $rqst Parámetros de búsqueda
     * @return array Resultado de la operación
     */
    public static function getDetalle($rqst)
    {
        $intentoId = isset($rqst['intento_id']) ? intval($rqst['intento_id']) : 0;

        if ($intentoId === 0) {
            return Util::error_missing_data_description('ID de intento requerido');
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $q = "SELECT
                    r.id,
                    r.tbl_pregunta_id,
                    r.tbl_opcion_respuesta_id,
                    r.respuesta_texto,
                    p.texto_pregunta,
                    p.tipo_pregunta,
                    o.texto_opcion
                FROM " . $db->getTable('tbl_cuestionario_respuestas') . " r
                INNER JOIN " . $db->getTable('tbl_preguntas') . " p ON r.tbl_pregunta_id = p.id
                LEFT JOIN " . $db->getTable('tbl_opciones_respuesta') . " o ON r.tbl_opcion_respuesta_id = o.id
                WHERE r.tbl_intento_id = :intento_id
                ORDER BY p.orden ASC";

            $stmt = $pdo->prepare($q);
            $stmt->execute([':intento_id' => $intentoId]);
            $respuestas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $db->closeConect();

            return [
                'output' => [
                    'valid' => true,
                    'response' => $respuestas
                ]
            ];

        } catch (Exception $e) {
            $db->closeConect();
            return Util::error_general('Error al obtener detalle de respuestas: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene estadísticas generales de una ficha técnica
     * @param array $rqst Parámetros de búsqueda
     * @return array Resultado de la operación
     */
    public static function getEstadisticas($rqst)
    {
        $fichaTecnicaId = isset($rqst['ficha_tecnica_id']) ? intval($rqst['ficha_tecnica_id']) : 0;

        if ($fichaTecnicaId === 0) {
            return Util::error_missing_data_description('ID de ficha técnica requerido');
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            // Total de votantes activos
            $qTotal = "SELECT COUNT(*) as total
                FROM " . $db->getTable('tbl_votantes') . "
                WHERE estado = 'activo'";
            $stmtTotal = $pdo->prepare($qTotal);
            $stmtTotal->execute();
            $totalVotantes = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'];

            // Votantes que han respondido
            $qRespondieron = "SELECT COUNT(DISTINCT tbl_votante_id) as total
                FROM " . $db->getTable('tbl_cuestionario_intentos') . "
                WHERE tbl_ficha_tecnica_encuesta_id = :ficha_tecnica_id
                AND tbl_votante_id IS NOT NULL";
            $stmtRespondieron = $pdo->prepare($qRespondieron);
            $stmtRespondieron->execute([':ficha_tecnica_id' => $fichaTecnicaId]);
            $totalRespondieron = $stmtRespondieron->fetch(PDO::FETCH_ASSOC)['total'];

            // Votantes que NO han respondido
            $totalNoRespondieron = $totalVotantes - $totalRespondieron;

            // Porcentaje de respuestas
            $porcentajeRespuestas = $totalVotantes > 0 ? round(($totalRespondieron / $totalVotantes) * 100, 2) : 0;

            // Últimas 10 respuestas
            $qUltimas = "SELECT
                    i.id,
                    i.fecha_respuesta,
                    CASE
                        WHEN u.tipo = 'Encuestador' THEN 'Encuestado'
                        WHEN v.tbl_usuario_id IS NULL OR v.tbl_usuario_id = 0 THEN 'Autoregistro'
                        ELSE 'Registro interno'
                    END as tipo_registro,
                    v.nombre_completo,
                    v.email,
                    CASE
                        WHEN u.tipo = 'Encuestador' THEN COALESCE(
                            NULLIF(TRIM(CONCAT(COALESCE(u.nombre, ''), ' ', COALESCE(u.apellido, ''))), ''),
                            NULLIF(TRIM(COALESCE(u.nickname, '')), ''),
                            'Sin asignar'
                        )
                        WHEN v.tbl_usuario_id IS NULL OR v.tbl_usuario_id = 0 THEN 'No aplica'
                        ELSE COALESCE(
                            NULLIF(TRIM(CONCAT(COALESCE(u.nombre, ''), ' ', COALESCE(u.apellido, ''))), ''),
                            NULLIF(TRIM(COALESCE(u.nickname, '')), ''),
                            'Sin asignar'
                        )
                    END as encuestador_nombre_completo,
                    COUNT(DISTINCT r.tbl_pregunta_id) as preguntas_respondidas
                FROM " . $db->getTable('tbl_cuestionario_intentos') . " i
                INNER JOIN " . $db->getTable('tbl_votantes') . " v ON i.tbl_votante_id = v.id
                LEFT JOIN " . $db->getTable('tbl_usuarios') . " u ON v.tbl_usuario_id = u.id
                LEFT JOIN " . $db->getTable('tbl_cuestionario_respuestas') . " r ON i.id = r.tbl_intento_id
                WHERE i.tbl_ficha_tecnica_encuesta_id = :ficha_tecnica_id
                AND i.tbl_votante_id IS NOT NULL
                GROUP BY i.id, i.fecha_respuesta, v.nombre_completo, v.email, u.nombre, u.apellido, u.nickname
                ORDER BY i.fecha_respuesta DESC
                LIMIT 10";
            $stmtUltimas = $pdo->prepare($qUltimas);
            $stmtUltimas->execute([':ficha_tecnica_id' => $fichaTecnicaId]);
            $ultimasRespuestas = $stmtUltimas->fetchAll(PDO::FETCH_ASSOC);

            // Distribución demográfica de quienes respondieron
            $qIdeologia = "SELECT v.ideologia, COUNT(DISTINCT v.id) as cantidad
                FROM " . $db->getTable('tbl_votantes') . " v
                INNER JOIN " . $db->getTable('tbl_cuestionario_intentos') . " i ON v.id = i.tbl_votante_id
                WHERE i.tbl_ficha_tecnica_encuesta_id = :ficha_tecnica_id
                AND v.estado = 'activo'
                GROUP BY v.ideologia ORDER BY cantidad DESC";
            $stmtI = $pdo->prepare($qIdeologia);
            $stmtI->execute([':ficha_tecnica_id' => $fichaTecnicaId]);
            $ideologia = $stmtI->fetchAll(PDO::FETCH_ASSOC);

            $qGenero = "SELECT v.genero, COUNT(DISTINCT v.id) as cantidad
                FROM " . $db->getTable('tbl_votantes') . " v
                INNER JOIN " . $db->getTable('tbl_cuestionario_intentos') . " i ON v.id = i.tbl_votante_id
                WHERE i.tbl_ficha_tecnica_encuesta_id = :ficha_tecnica_id
                AND v.estado = 'activo'
                GROUP BY v.genero ORDER BY cantidad DESC";
            $stmtG = $pdo->prepare($qGenero);
            $stmtG->execute([':ficha_tecnica_id' => $fichaTecnicaId]);
            $genero = $stmtG->fetchAll(PDO::FETCH_ASSOC);

            $qEdad = "SELECT v.rango_edad, COUNT(DISTINCT v.id) as cantidad
                FROM " . $db->getTable('tbl_votantes') . " v
                INNER JOIN " . $db->getTable('tbl_cuestionario_intentos') . " i ON v.id = i.tbl_votante_id
                WHERE i.tbl_ficha_tecnica_encuesta_id = :ficha_tecnica_id
                AND v.estado = 'activo'
                GROUP BY v.rango_edad ORDER BY cantidad DESC";
            $stmtE = $pdo->prepare($qEdad);
            $stmtE->execute([':ficha_tecnica_id' => $fichaTecnicaId]);
            $edad = $stmtE->fetchAll(PDO::FETCH_ASSOC);

            $qIngresos = "SELECT v.nivel_ingresos, COUNT(DISTINCT v.id) as cantidad
                FROM " . $db->getTable('tbl_votantes') . " v
                INNER JOIN " . $db->getTable('tbl_cuestionario_intentos') . " i ON v.id = i.tbl_votante_id
                WHERE i.tbl_ficha_tecnica_encuesta_id = :ficha_tecnica_id
                AND v.estado = 'activo'
                GROUP BY v.nivel_ingresos ORDER BY cantidad DESC";
            $stmtIn = $pdo->prepare($qIngresos);
            $stmtIn->execute([':ficha_tecnica_id' => $fichaTecnicaId]);
            $ingresos = $stmtIn->fetchAll(PDO::FETCH_ASSOC);

            $qEducacion = "SELECT v.nivel_educacion, COUNT(DISTINCT v.id) as cantidad
                FROM " . $db->getTable('tbl_votantes') . " v
                INNER JOIN " . $db->getTable('tbl_cuestionario_intentos') . " i ON v.id = i.tbl_votante_id
                WHERE i.tbl_ficha_tecnica_encuesta_id = :ficha_tecnica_id
                AND v.estado = 'activo'
                GROUP BY v.nivel_educacion ORDER BY cantidad DESC";
            $stmtEd = $pdo->prepare($qEducacion);
            $stmtEd->execute([':ficha_tecnica_id' => $fichaTecnicaId]);
            $educacion = $stmtEd->fetchAll(PDO::FETCH_ASSOC);

            $qDepartamento = "SELECT
                    v.codigo_departamento,
                    COALESCE(d.departamento, v.codigo_departamento) as departamento,
                    COUNT(DISTINCT v.id) as cantidad
                FROM " . $db->getTable('tbl_votantes') . " v
                INNER JOIN " . $db->getTable('tbl_cuestionario_intentos') . " i ON v.id = i.tbl_votante_id
                LEFT JOIN " . $db->getTable('tbl_departamentos') . " d ON v.codigo_departamento = d.codigo_departamento
                WHERE i.tbl_ficha_tecnica_encuesta_id = :ficha_tecnica_id
                AND v.estado = 'activo'
                GROUP BY v.codigo_departamento, d.departamento
                ORDER BY cantidad DESC";
            $stmtDp = $pdo->prepare($qDepartamento);
            $stmtDp->execute([':ficha_tecnica_id' => $fichaTecnicaId]);
            $departamento = $stmtDp->fetchAll(PDO::FETCH_ASSOC);

            $qMunicipio = "SELECT
                    v.codigo_departamento,
                    v.codigo_municipio,
                    COALESCE(c.municipio, v.codigo_municipio) as municipio,
                    COALESCE(d.departamento, v.codigo_departamento) as departamento,
                    COUNT(DISTINCT v.id) as cantidad
                FROM " . $db->getTable('tbl_votantes') . " v
                INNER JOIN " . $db->getTable('tbl_cuestionario_intentos') . " i ON v.id = i.tbl_votante_id
                LEFT JOIN " . $db->getTable('tbl_ciudades') . " c ON v.codigo_municipio = c.codigo_muncipio
                    AND v.codigo_departamento = c.codigo_departamento
                LEFT JOIN " . $db->getTable('tbl_departamentos') . " d ON v.codigo_departamento = d.codigo_departamento
                WHERE i.tbl_ficha_tecnica_encuesta_id = :ficha_tecnica_id
                AND v.estado = 'activo'
                GROUP BY v.codigo_departamento, v.codigo_municipio, c.municipio, d.departamento
                ORDER BY cantidad DESC
                LIMIT 10";
            $stmtMu = $pdo->prepare($qMunicipio);
            $stmtMu->execute([':ficha_tecnica_id' => $fichaTecnicaId]);
            $municipio = $stmtMu->fetchAll(PDO::FETCH_ASSOC);

            $db->closeConect();

            return [
                'output' => [
                    'valid' => true,
                    'response' => [
                        'total_votantes' => $totalVotantes,
                        'total_respondieron' => $totalRespondieron,
                        'total_no_respondieron' => $totalNoRespondieron,
                        'porcentaje_respuestas' => $porcentajeRespuestas,
                        'ultimas_respuestas' => $ultimasRespuestas,
                        'ideologia'   => $ideologia,
                        'genero'      => $genero,
                        'edad'        => $edad,
                        'ingresos'    => $ingresos,
                        'educacion'   => $educacion,
                        'departamento'=> $departamento,
                        'municipio'   => $municipio,
                    ]
                ]
            ];

        } catch (Exception $e) {
            $db->closeConect();
            return Util::error_general('Error al obtener estadísticas: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene lista de votantes que respondieron
     * @param array $rqst Parámetros de búsqueda
     * @return array Resultado de la operación
     */
    public static function getVotantesQueRespondieron($rqst)
    {
        $fichaTecnicaId = isset($rqst['ficha_tecnica_id']) ? intval($rqst['ficha_tecnica_id']) : 0;

        if ($fichaTecnicaId === 0) {
            return Util::error_missing_data_description('ID de ficha técnica requerido');
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $q = "SELECT
                    v.id,
                    CASE
                        WHEN u.tipo = 'Encuestador' THEN 'Encuestado'
                        WHEN v.tbl_usuario_id IS NULL OR v.tbl_usuario_id = 0 THEN 'Autoregistro'
                        ELSE 'Registro interno'
                    END as tipo_registro,
                    v.nombre_completo,
                    v.email,
                    v.username,
                    v.genero,
                    v.rango_edad,
                    v.ideologia,
                    CASE
                        WHEN u.tipo = 'Encuestador' THEN COALESCE(
                            NULLIF(TRIM(CONCAT(COALESCE(u.nombre, ''), ' ', COALESCE(u.apellido, ''))), ''),
                            NULLIF(TRIM(COALESCE(u.nickname, '')), ''),
                            'Sin asignar'
                        )
                        WHEN v.tbl_usuario_id IS NULL OR v.tbl_usuario_id = 0 THEN 'No aplica'
                        ELSE COALESCE(
                            NULLIF(TRIM(CONCAT(COALESCE(u.nombre, ''), ' ', COALESCE(u.apellido, ''))), ''),
                            NULLIF(TRIM(COALESCE(u.nickname, '')), ''),
                            'Sin asignar'
                        )
                    END as encuestador_nombre_completo,
                    i.fecha_respuesta,
                    i.id as intento_id,
                    COUNT(DISTINCT r.tbl_pregunta_id) as preguntas_respondidas
                FROM " . $db->getTable('tbl_votantes') . " v
                INNER JOIN " . $db->getTable('tbl_cuestionario_intentos') . " i ON v.id = i.tbl_votante_id
                LEFT JOIN " . $db->getTable('tbl_usuarios') . " u ON v.tbl_usuario_id = u.id
                LEFT JOIN " . $db->getTable('tbl_cuestionario_respuestas') . " r ON i.id = r.tbl_intento_id
                WHERE i.tbl_ficha_tecnica_encuesta_id = :ficha_tecnica_id
                AND v.estado = 'activo'
                GROUP BY v.id, i.id, u.nombre, u.apellido, u.nickname
                ORDER BY i.fecha_respuesta DESC";

            $stmt = $pdo->prepare($q);
            $stmt->execute([':ficha_tecnica_id' => $fichaTecnicaId]);
            $votantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $db->closeConect();

            return [
                'output' => [
                    'valid' => true,
                    'response' => $votantes
                ]
            ];

        } catch (Exception $e) {
            $db->closeConect();
            return Util::error_general('Error al obtener votantes que respondieron: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene lista de votantes que NO han respondido
     * @param array $rqst Parámetros de búsqueda
     * @return array Resultado de la operación
     */
    public static function getVotantesQueNoRespondieron($rqst)
    {
        $fichaTecnicaId = isset($rqst['ficha_tecnica_id']) ? intval($rqst['ficha_tecnica_id']) : 0;

        if ($fichaTecnicaId === 0) {
            return Util::error_missing_data_description('ID de ficha técnica requerido');
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $q = "SELECT
                    v.id,
                    CASE
                        WHEN u.tipo = 'Encuestador' THEN 'Encuestado'
                        WHEN v.tbl_usuario_id IS NULL OR v.tbl_usuario_id = 0 THEN 'Autoregistro'
                        ELSE 'Registro interno'
                    END as tipo_registro,
                    v.nombre_completo,
                    v.email,
                    v.username,
                    v.genero,
                    v.rango_edad,
                    v.ideologia,
                    CASE
                        WHEN u.tipo = 'Encuestador' THEN COALESCE(
                            NULLIF(TRIM(CONCAT(COALESCE(u.nombre, ''), ' ', COALESCE(u.apellido, ''))), ''),
                            NULLIF(TRIM(COALESCE(u.nickname, '')), ''),
                            'Sin asignar'
                        )
                        WHEN v.tbl_usuario_id IS NULL OR v.tbl_usuario_id = 0 THEN 'No aplica'
                        ELSE COALESCE(
                            NULLIF(TRIM(CONCAT(COALESCE(u.nombre, ''), ' ', COALESCE(u.apellido, ''))), ''),
                            NULLIF(TRIM(COALESCE(u.nickname, '')), ''),
                            'Sin asignar'
                        )
                    END as encuestador_nombre_completo
                FROM " . $db->getTable('tbl_votantes') . " v
                LEFT JOIN " . $db->getTable('tbl_usuarios') . " u ON v.tbl_usuario_id = u.id
                WHERE v.estado = 'activo'
                AND v.id NOT IN (
                    SELECT DISTINCT tbl_votante_id
                    FROM " . $db->getTable('tbl_cuestionario_intentos') . "
                    WHERE tbl_ficha_tecnica_encuesta_id = :ficha_tecnica_id
                    AND tbl_votante_id IS NOT NULL
                )
                ORDER BY v.nombre_completo ASC";

            $stmt = $pdo->prepare($q);
            $stmt->execute([':ficha_tecnica_id' => $fichaTecnicaId]);
            $votantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $db->closeConect();

            return [
                'output' => [
                    'valid' => true,
                    'response' => $votantes
                ]
            ];

        } catch (Exception $e) {
            $db->closeConect();
            return Util::error_general('Error al obtener votantes que no respondieron: ' . $e->getMessage());
        }
    }
}
