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

        $dep = '';
        $muni = '';
        if (!empty($rqst['municipio_click'])) {
            $muni = method_exists('Util', 'normalizeCodigoMunicipio')
                ? Util::normalizeCodigoMunicipio($rqst['municipio_click'])
                : str_pad((string)intval($rqst['municipio_click']), 5, '0', STR_PAD_LEFT);
        }
        if (!empty($rqst['departamento_click'])) {
            $dep = method_exists('Util', 'normalizeCodigoDepartamento')
                ? Util::normalizeCodigoDepartamento($rqst['departamento_click'])
                : str_pad((string)intval($rqst['departamento_click']), 2, '0', STR_PAD_LEFT);
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $paramsBase = [':ficha_tecnica_id' => $fichaTecnicaId];
            $geoV = '';
            if ($muni !== '') {
                $geoV = " AND LPAD(CAST(v.codigo_municipio AS UNSIGNED), 5, '0') = :geo_muni ";
                $paramsBase[':geo_muni'] = $muni;
            } elseif ($dep !== '') {
                $geoV = " AND LPAD(CAST(v.codigo_departamento AS UNSIGNED), 2, '0') = :geo_dep ";
                $paramsBase[':geo_dep'] = $dep;
            }

            // Universo de votantes (filtrado por territorio si aplica)
            $qTotal = "SELECT COUNT(*) as total
                FROM " . $db->getTable('tbl_votantes') . " v
                WHERE v.estado = 'activo'" . $geoV;
            $stmtTotal = $pdo->prepare($qTotal);
            $paramsUniverse = [];
            if ($muni !== '') { $paramsUniverse[':geo_muni'] = $muni; }
            elseif ($dep !== '') { $paramsUniverse[':geo_dep'] = $dep; }
            $stmtTotal->execute($paramsUniverse);
            $totalVotantes = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'];

            $qRespondieron = "SELECT COUNT(DISTINCT i.tbl_votante_id) as total
                FROM " . $db->getTable('tbl_cuestionario_intentos') . " i
                INNER JOIN " . $db->getTable('tbl_votantes') . " v ON v.id = i.tbl_votante_id
                WHERE i.tbl_ficha_tecnica_encuesta_id = :ficha_tecnica_id
                AND i.tbl_votante_id IS NOT NULL
                AND v.estado = 'activo'" . $geoV;
            $stmtRespondieron = $pdo->prepare($qRespondieron);
            $stmtRespondieron->execute($paramsBase);
            $totalRespondieron = $stmtRespondieron->fetch(PDO::FETCH_ASSOC)['total'];

            $totalNoRespondieron = max(0, $totalVotantes - $totalRespondieron);
            $porcentajeRespuestas = $totalVotantes > 0 ? round(($totalRespondieron / $totalVotantes) * 100, 2) : 0;
            $ultimasRespuestas = [];

            $demoWhere = "i.tbl_ficha_tecnica_encuesta_id = :ficha_tecnica_id AND v.estado = 'activo'" . $geoV;

            $qIdeologia = "SELECT v.ideologia, COUNT(DISTINCT v.id) as cantidad
                FROM " . $db->getTable('tbl_votantes') . " v
                INNER JOIN " . $db->getTable('tbl_cuestionario_intentos') . " i ON v.id = i.tbl_votante_id
                WHERE {$demoWhere}
                GROUP BY v.ideologia ORDER BY cantidad DESC";
            $stmtI = $pdo->prepare($qIdeologia);
            $stmtI->execute($paramsBase);
            $ideologia = $stmtI->fetchAll(PDO::FETCH_ASSOC);

            $qGenero = "SELECT v.genero, COUNT(DISTINCT v.id) as cantidad
                FROM " . $db->getTable('tbl_votantes') . " v
                INNER JOIN " . $db->getTable('tbl_cuestionario_intentos') . " i ON v.id = i.tbl_votante_id
                WHERE {$demoWhere}
                GROUP BY v.genero ORDER BY cantidad DESC";
            $stmtG = $pdo->prepare($qGenero);
            $stmtG->execute($paramsBase);
            $genero = $stmtG->fetchAll(PDO::FETCH_ASSOC);

            $qEdad = "SELECT v.rango_edad, COUNT(DISTINCT v.id) as cantidad
                FROM " . $db->getTable('tbl_votantes') . " v
                INNER JOIN " . $db->getTable('tbl_cuestionario_intentos') . " i ON v.id = i.tbl_votante_id
                WHERE {$demoWhere}
                GROUP BY v.rango_edad ORDER BY cantidad DESC";
            $stmtE = $pdo->prepare($qEdad);
            $stmtE->execute($paramsBase);
            $edad = $stmtE->fetchAll(PDO::FETCH_ASSOC);

            $qIngresos = "SELECT v.nivel_ingresos, COUNT(DISTINCT v.id) as cantidad
                FROM " . $db->getTable('tbl_votantes') . " v
                INNER JOIN " . $db->getTable('tbl_cuestionario_intentos') . " i ON v.id = i.tbl_votante_id
                WHERE {$demoWhere}
                GROUP BY v.nivel_ingresos ORDER BY cantidad DESC";
            $stmtIn = $pdo->prepare($qIngresos);
            $stmtIn->execute($paramsBase);
            $ingresos = $stmtIn->fetchAll(PDO::FETCH_ASSOC);

            $qEducacion = "SELECT v.nivel_educacion, COUNT(DISTINCT v.id) as cantidad
                FROM " . $db->getTable('tbl_votantes') . " v
                INNER JOIN " . $db->getTable('tbl_cuestionario_intentos') . " i ON v.id = i.tbl_votante_id
                WHERE {$demoWhere}
                GROUP BY v.nivel_educacion ORDER BY cantidad DESC";
            $stmtEd = $pdo->prepare($qEducacion);
            $stmtEd->execute($paramsBase);
            $educacion = $stmtEd->fetchAll(PDO::FETCH_ASSOC);

            $qDepartamento = "SELECT
                    v.codigo_departamento,
                    COALESCE(d.departamento, v.codigo_departamento) as departamento,
                    COUNT(DISTINCT v.id) as cantidad
                FROM " . $db->getTable('tbl_votantes') . " v
                INNER JOIN " . $db->getTable('tbl_cuestionario_intentos') . " i ON v.id = i.tbl_votante_id
                LEFT JOIN " . $db->getTable('tbl_departamentos') . " d ON v.codigo_departamento = d.codigo_departamento
                WHERE {$demoWhere}
                GROUP BY v.codigo_departamento, d.departamento
                ORDER BY cantidad DESC";
            $stmtDp = $pdo->prepare($qDepartamento);
            $stmtDp->execute($paramsBase);
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
                WHERE {$demoWhere}
                GROUP BY v.codigo_departamento, v.codigo_municipio, c.municipio, d.departamento
                ORDER BY cantidad DESC
                LIMIT 10";
            $stmtMu = $pdo->prepare($qMunicipio);
            $stmtMu->execute($paramsBase);
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
                        'territorio'  => [
                            'departamento' => $dep,
                            'municipio' => $muni,
                        ],
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
                        WHEN u.tipo = 'Encuestador' THEN 'Encuestador'
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
                        WHEN u.tipo = 'Encuestador' THEN 'Encuestador'
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

    /** Expresión SQL: tipo_registro (misma lógica de las tablas actuales) */
    private static function sqlTipoRegistro($usuarioAlias = 'u', $votanteAlias = 'v')
    {
        return "CASE
            WHEN {$usuarioAlias}.tipo = 'Encuestador' THEN 'Encuestador'
            WHEN {$votanteAlias}.tbl_usuario_id IS NULL OR {$votanteAlias}.tbl_usuario_id = 0 THEN 'Autoregistro'
            ELSE 'Registro interno'
        END";
    }

    /** Expresión SQL: nombre de encuestador / creador */
    private static function sqlEncuestadorNombre($usuarioAlias = 'u', $votanteAlias = 'v')
    {
        return "CASE
            WHEN {$usuarioAlias}.tipo = 'Encuestador' THEN COALESCE(
                NULLIF(TRIM(CONCAT(COALESCE({$usuarioAlias}.nombre, ''), ' ', COALESCE({$usuarioAlias}.apellido, ''))), ''),
                NULLIF(TRIM(COALESCE({$usuarioAlias}.nickname, '')), ''),
                'Sin asignar'
            )
            WHEN {$votanteAlias}.tbl_usuario_id IS NULL OR {$votanteAlias}.tbl_usuario_id = 0 THEN 'No aplica'
            ELSE COALESCE(
                NULLIF(TRIM(CONCAT(COALESCE({$usuarioAlias}.nombre, ''), ' ', COALESCE({$usuarioAlias}.apellido, ''))), ''),
                NULLIF(TRIM(COALESCE({$usuarioAlias}.nickname, '')), ''),
                'Sin asignar'
            )
        END";
    }

    private static function parseDtRequest($rqst)
    {
        return [
            'ficha' => isset($rqst['ficha_tecnica_id']) ? intval($rqst['ficha_tecnica_id']) : 0,
            'draw' => isset($rqst['draw']) ? intval($rqst['draw']) : 1,
            'start' => isset($rqst['start']) ? max(0, intval($rqst['start'])) : 0,
            'length' => isset($rqst['length']) ? intval($rqst['length']) : 25,
            'tipo' => isset($rqst['filtro_tipo']) ? trim((string)$rqst['filtro_tipo']) : '',
            'encuestador' => isset($rqst['filtro_encuestador']) ? trim((string)$rqst['filtro_encuestador']) : '',
            'fecha_desde' => isset($rqst['fecha_desde']) ? trim((string)$rqst['fecha_desde']) : '',
            'fecha_hasta' => isset($rqst['fecha_hasta']) ? trim((string)$rqst['fecha_hasta']) : '',
            'search' => isset($rqst['search']['value']) ? trim((string)$rqst['search']['value']) : (isset($rqst['search_value']) ? trim((string)$rqst['search_value']) : ''),
        ];
    }

    private static function dtResponse($draw, $recordsTotal, $recordsFiltered, $data)
    {
        return [
            'output' => [
                'valid' => true,
                'response' => [
                    'draw' => $draw,
                    'recordsTotal' => (int)$recordsTotal,
                    'recordsFiltered' => (int)$recordsFiltered,
                    'data' => $data,
                ],
            ],
        ];
    }

    /**
     * Catálogo de filtros para las tablas del dashboard
     */
    public static function getFiltrosDashboard($rqst)
    {
        $fichaTecnicaId = isset($rqst['ficha_tecnica_id']) ? intval($rqst['ficha_tecnica_id']) : 0;
        if ($fichaTecnicaId === 0) {
            return Util::error_missing_data_description('ID de ficha técnica requerido');
        }

        $db = new DbConection();
        $pdo = $db->openConect();
        $tipoSql = self::sqlTipoRegistro();
        $encSql = self::sqlEncuestadorNombre();

        try {
            $tipos = ['Encuestador', 'Autoregistro', 'Registro interno'];

            $qEnc = "SELECT DISTINCT enc AS encuestador FROM (
                    SELECT {$encSql} AS enc
                    FROM " . $db->getTable('tbl_cuestionario_intentos') . " i
                    INNER JOIN " . $db->getTable('tbl_votantes') . " v ON i.tbl_votante_id = v.id
                    LEFT JOIN " . $db->getTable('tbl_usuarios') . " u ON v.tbl_usuario_id = u.id
                    WHERE i.tbl_ficha_tecnica_encuesta_id = :ficha
                    AND i.tbl_votante_id IS NOT NULL
                ) t
                WHERE enc IS NOT NULL AND enc <> ''
                ORDER BY enc ASC";
            $stmt = $pdo->prepare($qEnc);
            $stmt->execute([':ficha' => $fichaTecnicaId]);
            $encuestadores = array_values(array_filter(array_map(static function ($r) {
                return $r['encuestador'] ?? '';
            }, $stmt->fetchAll(PDO::FETCH_ASSOC))));

            $db->closeConect();
            return [
                'output' => [
                    'valid' => true,
                    'response' => [
                        'tipos' => $tipos,
                        'encuestadores' => $encuestadores,
                    ],
                ],
            ];
        } catch (Exception $e) {
            $db->closeConect();
            return Util::error_general('Error al obtener filtros: ' . $e->getMessage());
        }
    }

    /**
     * KPIs ejecutivos para el listado del dashboard (6 métricas)
     */
    public static function getKpisListadoDashboard($rqst)
    {
        $fichaTecnicaId = isset($rqst['ficha_tecnica_id']) ? intval($rqst['ficha_tecnica_id']) : 0;
        if ($fichaTecnicaId === 0) {
            return Util::error_missing_data_description('ID de ficha técnica requerido');
        }

        $db = new DbConection();
        $pdo = $db->openConect();
        $tipoSql = self::sqlTipoRegistro();

        try {
            $base = " FROM " . $db->getTable('tbl_cuestionario_intentos') . " i
                INNER JOIN " . $db->getTable('tbl_votantes') . " v ON i.tbl_votante_id = v.id
                LEFT JOIN " . $db->getTable('tbl_usuarios') . " u ON v.tbl_usuario_id = u.id
                WHERE i.tbl_ficha_tecnica_encuesta_id = :ficha
                AND i.tbl_votante_id IS NOT NULL";

            $stmt = $pdo->prepare("SELECT COUNT(*) AS total {$base}");
            $stmt->execute([':ficha' => $fichaTecnicaId]);
            $totalRespuestas = (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];

            $stmt = $pdo->prepare(
                "SELECT COUNT(DISTINCT u.id) AS total {$base}
                 AND u.tipo = 'Encuestador' AND u.id IS NOT NULL"
            );
            $stmt->execute([':ficha' => $fichaTecnicaId]);
            $totalEncuestadores = (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];

            $stmt = $pdo->prepare(
                "SELECT tipo_registro, COUNT(*) AS total FROM (
                    SELECT i.id, {$tipoSql} AS tipo_registro {$base}
                    GROUP BY i.id, u.tipo, v.tbl_usuario_id
                 ) t GROUP BY tipo_registro"
            );
            $stmt->execute([':ficha' => $fichaTecnicaId]);
            $byTipo = ['Encuestador' => 0, 'Autoregistro' => 0, 'Registro interno' => 0];
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $k = $row['tipo_registro'] ?? '';
                if (isset($byTipo[$k])) {
                    $byTipo[$k] = (int)$row['total'];
                }
            }

            $stmt = $pdo->prepare(
                "SELECT COUNT(DISTINCT i.id) AS total {$base}
                 AND EXISTS (
                    SELECT 1 FROM " . $db->getTable('tbl_certificacion_encuestador') . " c
                    WHERE c.tbl_votante_id = v.id
                      AND c.tbl_ficha_tecnica_encuesta_id = i.tbl_ficha_tecnica_encuesta_id
                      AND c.origen_tipo = 'cuestionario'
                 )"
            );
            $stmt->execute([':ficha' => $fichaTecnicaId]);
            $totalCertificadas = (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];

            $db->closeConect();
            return [
                'output' => [
                    'valid' => true,
                    'response' => [
                        'total_respuestas' => $totalRespuestas,
                        'total_encuestadores' => $totalEncuestadores,
                        'tipo_encuestador' => $byTipo['Encuestador'],
                        'tipo_autoregistro' => $byTipo['Autoregistro'],
                        'tipo_registro_interno' => $byTipo['Registro interno'],
                        'total_certificadas' => $totalCertificadas,
                    ],
                ],
            ];
        } catch (Exception $e) {
            $db->closeConect();
            return Util::error_general('Error al obtener KPIs: ' . $e->getMessage());
        }
    }

    /**
     * DataTables server-side: últimas / todas las respuestas (fecha DESC)
     */
    public static function getUltimasRespuestasDt($rqst)
    {
        $p = self::parseDtRequest($rqst);
        if ($p['ficha'] === 0) {
            return Util::error_missing_data_description('ID de ficha técnica requerido');
        }

        $length = $p['length'] <= 0 ? 25 : min($p['length'], 200);
        $db = new DbConection();
        $pdo = $db->openConect();
        $tipoSql = self::sqlTipoRegistro();
        $encSql = self::sqlEncuestadorNombre();

        try {
            $baseFrom = " FROM " . $db->getTable('tbl_cuestionario_intentos') . " i
                INNER JOIN " . $db->getTable('tbl_votantes') . " v ON i.tbl_votante_id = v.id
                LEFT JOIN " . $db->getTable('tbl_usuarios') . " u ON v.tbl_usuario_id = u.id
                LEFT JOIN " . $db->getTable('tbl_cuestionario_respuestas') . " r ON i.id = r.tbl_intento_id
                WHERE i.tbl_ficha_tecnica_encuesta_id = :ficha
                AND i.tbl_votante_id IS NOT NULL";

            $params = [':ficha' => $p['ficha']];
            $having = [];
            $whereExtra = [];

            if ($p['fecha_desde'] !== '') {
                $whereExtra[] = "DATE(i.fecha_respuesta) >= :fecha_desde";
                $params[':fecha_desde'] = $p['fecha_desde'];
            }
            if ($p['fecha_hasta'] !== '') {
                $whereExtra[] = "DATE(i.fecha_respuesta) <= :fecha_hasta";
                $params[':fecha_hasta'] = $p['fecha_hasta'];
            }
            if ($p['tipo'] !== '') {
                $having[] = "tipo_registro = :filtro_tipo";
                $params[':filtro_tipo'] = $p['tipo'];
            }
            if ($p['encuestador'] !== '') {
                $having[] = "encuestador_nombre_completo = :filtro_enc";
                $params[':filtro_enc'] = $p['encuestador'];
            }
            if ($p['search'] !== '') {
                $having[] = "(nombre_completo LIKE :q OR email LIKE :q OR encuestador_nombre_completo LIKE :q OR tipo_registro LIKE :q)";
                $params[':q'] = '%' . $p['search'] . '%';
            }

            $whereSql = $baseFrom . (count($whereExtra) ? ' AND ' . implode(' AND ', $whereExtra) : '');

            $qTotal = "SELECT COUNT(*) AS total FROM (
                    SELECT i.id {$whereSql} GROUP BY i.id
                ) x";
            $stmtTotal = $pdo->prepare($qTotal);
            $stmtTotal->execute([':ficha' => $p['ficha']]);
            $recordsTotal = (int)$stmtTotal->fetch(PDO::FETCH_ASSOC)['total'];

            $selectInner = "SELECT
                    i.id,
                    i.fecha_respuesta,
                    i.tbl_votante_id,
                    {$tipoSql} AS tipo_registro,
                    v.nombre_completo,
                    v.email,
                    {$encSql} AS encuestador_nombre_completo,
                    COUNT(DISTINCT r.tbl_pregunta_id) AS preguntas_respondidas,
                    (
                        SELECT c.id FROM " . $db->getTable('tbl_certificacion_encuestador') . " c
                        WHERE c.tbl_votante_id = v.id
                          AND c.tbl_ficha_tecnica_encuesta_id = i.tbl_ficha_tecnica_encuesta_id
                          AND c.origen_tipo = 'cuestionario'
                        ORDER BY c.id DESC LIMIT 1
                    ) AS certificacion_id,
                    (
                        SELECT c.latitud FROM " . $db->getTable('tbl_certificacion_encuestador') . " c
                        WHERE c.tbl_votante_id = v.id
                          AND c.tbl_ficha_tecnica_encuesta_id = i.tbl_ficha_tecnica_encuesta_id
                          AND c.origen_tipo = 'cuestionario'
                        ORDER BY c.id DESC LIMIT 1
                    ) AS cert_latitud,
                    (
                        SELECT c.longitud FROM " . $db->getTable('tbl_certificacion_encuestador') . " c
                        WHERE c.tbl_votante_id = v.id
                          AND c.tbl_ficha_tecnica_encuesta_id = i.tbl_ficha_tecnica_encuesta_id
                          AND c.origen_tipo = 'cuestionario'
                        ORDER BY c.id DESC LIMIT 1
                    ) AS cert_longitud,
                    (
                        SELECT c.audio_duracion_segundos FROM " . $db->getTable('tbl_certificacion_encuestador') . " c
                        WHERE c.tbl_votante_id = v.id
                          AND c.tbl_ficha_tecnica_encuesta_id = i.tbl_ficha_tecnica_encuesta_id
                          AND c.origen_tipo = 'cuestionario'
                        ORDER BY c.id DESC LIMIT 1
                    ) AS cert_audio_segundos
                {$whereSql}
                GROUP BY i.id, i.fecha_respuesta, i.tbl_votante_id, v.id, v.nombre_completo, v.email, u.tipo, u.nombre, u.apellido, u.nickname, v.tbl_usuario_id, i.tbl_ficha_tecnica_encuesta_id";

            $havingSql = count($having) ? ' HAVING ' . implode(' AND ', $having) : '';

            $qFiltered = "SELECT COUNT(*) AS total FROM ({$selectInner}{$havingSql}) x";
            $stmtFiltered = $pdo->prepare($qFiltered);
            $stmtFiltered->execute($params);
            $recordsFiltered = (int)$stmtFiltered->fetch(PDO::FETCH_ASSOC)['total'];

            $qData = "{$selectInner}{$havingSql} ORDER BY i.fecha_respuesta DESC LIMIT {$length} OFFSET {$p['start']}";
            $stmtData = $pdo->prepare($qData);
            $stmtData->execute($params);
            $rows = $stmtData->fetchAll(PDO::FETCH_ASSOC);

            $db->closeConect();
            return self::dtResponse($p['draw'], $recordsTotal, $recordsFiltered, $rows);
        } catch (Exception $e) {
            $db->closeConect();
            return Util::error_general('Error al listar respuestas: ' . $e->getMessage());
        }
    }

    /**
     * DataTables server-side: votantes que respondieron
     */
    public static function getVotantesQueRespondieronDt($rqst)
    {
        $p = self::parseDtRequest($rqst);
        if ($p['ficha'] === 0) {
            return Util::error_missing_data_description('ID de ficha técnica requerido');
        }

        $length = $p['length'] <= 0 ? 25 : min($p['length'], 200);
        $db = new DbConection();
        $pdo = $db->openConect();
        $tipoSql = self::sqlTipoRegistro();
        $encSql = self::sqlEncuestadorNombre();
        $tblRespuestas = $db->getTable('tbl_cuestionario_respuestas');

        try {
            $baseFrom = " FROM " . $db->getTable('tbl_votantes') . " v
                INNER JOIN " . $db->getTable('tbl_cuestionario_intentos') . " i ON v.id = i.tbl_votante_id
                LEFT JOIN " . $db->getTable('tbl_usuarios') . " u ON v.tbl_usuario_id = u.id
                WHERE i.tbl_ficha_tecnica_encuesta_id = :ficha
                AND v.estado = 'activo'";

            $params = [':ficha' => $p['ficha']];
            $whereExtra = [];

            if ($p['fecha_desde'] !== '') {
                $whereExtra[] = "DATE(i.fecha_respuesta) >= :fecha_desde";
                $params[':fecha_desde'] = $p['fecha_desde'];
            }
            if ($p['fecha_hasta'] !== '') {
                $whereExtra[] = "DATE(i.fecha_respuesta) <= :fecha_hasta";
                $params[':fecha_hasta'] = $p['fecha_hasta'];
            }
            if ($p['tipo'] !== '') {
                $whereExtra[] = "({$tipoSql}) = :filtro_tipo";
                $params[':filtro_tipo'] = $p['tipo'];
            }
            if ($p['encuestador'] !== '') {
                $whereExtra[] = "({$encSql}) = :filtro_enc";
                $params[':filtro_enc'] = $p['encuestador'];
            }
            if ($p['search'] !== '') {
                $whereExtra[] = "(v.nombre_completo LIKE :q OR v.email LIKE :q OR ({$encSql}) LIKE :q OR ({$tipoSql}) LIKE :q)";
                $params[':q'] = '%' . $p['search'] . '%';
            }

            $whereSql = $baseFrom . (count($whereExtra) ? ' AND ' . implode(' AND ', $whereExtra) : '');

            $qTotal = "SELECT COUNT(*) AS total {$baseFrom}";
            $stmtTotal = $pdo->prepare($qTotal);
            $stmtTotal->execute([':ficha' => $p['ficha']]);
            $recordsTotal = (int)$stmtTotal->fetch(PDO::FETCH_ASSOC)['total'];

            $qFiltered = "SELECT COUNT(*) AS total {$whereSql}";
            $stmtFiltered = $pdo->prepare($qFiltered);
            $stmtFiltered->execute($params);
            $recordsFiltered = (int)$stmtFiltered->fetch(PDO::FETCH_ASSOC)['total'];

            $qData = "SELECT
                    v.id,
                    {$tipoSql} AS tipo_registro,
                    v.nombre_completo,
                    v.email,
                    v.username,
                    v.genero,
                    v.rango_edad,
                    v.ideologia,
                    {$encSql} AS encuestador_nombre_completo,
                    i.fecha_respuesta,
                    i.id AS intento_id,
                    (
                        SELECT COUNT(DISTINCT r.tbl_pregunta_id)
                        FROM {$tblRespuestas} r
                        WHERE r.tbl_intento_id = i.id
                    ) AS preguntas_respondidas
                {$whereSql}
                ORDER BY i.fecha_respuesta DESC
                LIMIT {$length} OFFSET {$p['start']}";
            $stmtData = $pdo->prepare($qData);
            $stmtData->execute($params);
            $rows = $stmtData->fetchAll(PDO::FETCH_ASSOC);

            $db->closeConect();
            return self::dtResponse($p['draw'], $recordsTotal, $recordsFiltered, $rows);
        } catch (Exception $e) {
            $db->closeConect();
            return Util::error_general('Error al listar respondieron: ' . $e->getMessage());
        }
    }

    /**
     * DataTables server-side: votantes pendientes
     * Filtro fecha aplica sobre v.dtcreate cuando existe
     */
    public static function getVotantesQueNoRespondieronDt($rqst)
    {
        $p = self::parseDtRequest($rqst);
        if ($p['ficha'] === 0) {
            return Util::error_missing_data_description('ID de ficha técnica requerido');
        }

        $length = $p['length'] <= 0 ? 25 : min($p['length'], 200);
        $db = new DbConection();
        $pdo = $db->openConect();
        $tipoSql = self::sqlTipoRegistro();
        $encSql = self::sqlEncuestadorNombre();

        try {
            $baseFrom = " FROM " . $db->getTable('tbl_votantes') . " v
                LEFT JOIN " . $db->getTable('tbl_usuarios') . " u ON v.tbl_usuario_id = u.id
                WHERE v.estado = 'activo'
                AND v.id NOT IN (
                    SELECT DISTINCT tbl_votante_id
                    FROM " . $db->getTable('tbl_cuestionario_intentos') . "
                    WHERE tbl_ficha_tecnica_encuesta_id = :ficha
                    AND tbl_votante_id IS NOT NULL
                )";

            $params = [':ficha' => $p['ficha']];
            $whereExtra = [];

            if ($p['fecha_desde'] !== '') {
                $whereExtra[] = "DATE(v.dtcreate) >= :fecha_desde";
                $params[':fecha_desde'] = $p['fecha_desde'];
            }
            if ($p['fecha_hasta'] !== '') {
                $whereExtra[] = "DATE(v.dtcreate) <= :fecha_hasta";
                $params[':fecha_hasta'] = $p['fecha_hasta'];
            }
            if ($p['tipo'] !== '') {
                $whereExtra[] = "({$tipoSql}) = :filtro_tipo";
                $params[':filtro_tipo'] = $p['tipo'];
            }
            if ($p['encuestador'] !== '') {
                $whereExtra[] = "({$encSql}) = :filtro_enc";
                $params[':filtro_enc'] = $p['encuestador'];
            }
            if ($p['search'] !== '') {
                $whereExtra[] = "(v.nombre_completo LIKE :q OR v.email LIKE :q OR v.username LIKE :q OR ({$encSql}) LIKE :q OR ({$tipoSql}) LIKE :q)";
                $params[':q'] = '%' . $p['search'] . '%';
            }

            $whereSql = $baseFrom . (count($whereExtra) ? ' AND ' . implode(' AND ', $whereExtra) : '');

            $qTotal = "SELECT COUNT(*) AS total {$baseFrom}";
            $stmtTotal = $pdo->prepare($qTotal);
            $stmtTotal->execute([':ficha' => $p['ficha']]);
            $recordsTotal = (int)$stmtTotal->fetch(PDO::FETCH_ASSOC)['total'];

            $qFiltered = "SELECT COUNT(*) AS total {$whereSql}";
            $stmtFiltered = $pdo->prepare($qFiltered);
            $stmtFiltered->execute($params);
            $recordsFiltered = (int)$stmtFiltered->fetch(PDO::FETCH_ASSOC)['total'];

            $qData = "SELECT
                    v.id,
                    {$tipoSql} AS tipo_registro,
                    v.nombre_completo,
                    v.email,
                    v.username,
                    v.genero,
                    v.rango_edad,
                    v.ideologia,
                    {$encSql} AS encuestador_nombre_completo,
                    v.dtcreate
                {$whereSql}
                ORDER BY v.nombre_completo ASC
                LIMIT {$length} OFFSET {$p['start']}";
            $stmtData = $pdo->prepare($qData);
            $stmtData->execute($params);
            $rows = $stmtData->fetchAll(PDO::FETCH_ASSOC);

            $db->closeConect();
            return self::dtResponse($p['draw'], $recordsTotal, $recordsFiltered, $rows);
        } catch (Exception $e) {
            $db->closeConect();
            return Util::error_general('Error al listar pendientes: ' . $e->getMessage());
        }
    }
}
