<?php

/**
 * Clase CertificacionEncuestador
 * Gestiona las certificaciones de encuestadores con audio y geolocalización
 */
class CertificacionEncuestador
{
    /**
     * Guarda una certificación de encuestador
     * @param array $rqst Datos de la solicitud
     * @return array Resultado de la operación
     */
    public static function save($rqst)
    {
        $tbl_votante_id = isset($rqst['tbl_votante_id']) ? intval($rqst['tbl_votante_id']) : 0;
        $tbl_usuario_id = intval($_SESSION['session_user']['id']);

        // Validar campos requeridos
        if ($tbl_votante_id <= 0) {
            return Util::error_missing_data_description('ID de votante requerido');
        }

        // Origen de la certificación (sondeo, cuestionario o registro_simple)
        $origen_tipo = isset($rqst['origen_tipo']) ? trim($rqst['origen_tipo']) : 'registro_simple';
        $tbl_sondeo_id = isset($rqst['tbl_sondeo_id']) ? intval($rqst['tbl_sondeo_id']) : null;
        $tbl_ficha_tecnica_encuesta_id = isset($rqst['tbl_ficha_tecnica_encuesta_id']) ? intval($rqst['tbl_ficha_tecnica_encuesta_id']) : null;

        // Datos de geolocalización
        $latitud = isset($rqst['latitud']) ? floatval($rqst['latitud']) : null;
        $longitud = isset($rqst['longitud']) ? floatval($rqst['longitud']) : null;
        $precision_metros = isset($rqst['precision_metros']) ? floatval($rqst['precision_metros']) : null;
        $altitud = isset($rqst['altitud']) ? floatval($rqst['altitud']) : null;

        // Datos de audio
        $audio_base64 = isset($rqst['audio_base64']) ? $rqst['audio_base64'] : null;
        $audio_duracion_segundos = isset($rqst['audio_duracion_segundos']) ? intval($rqst['audio_duracion_segundos']) : null;
        $audio_formato = isset($rqst['audio_formato']) ? trim($rqst['audio_formato']) : null;

        // Metadata del dispositivo
        $dispositivo_info = isset($rqst['dispositivo_info']) ? $rqst['dispositivo_info'] : null;
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
        $ip_address = self::getClientIP();

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $pdo->beginTransaction();

            $q = "INSERT INTO " . $db->getTable('tbl_certificacion_encuestador') . "
                  (tbl_votante_id, tbl_usuario_id,
                   origen_tipo, tbl_sondeo_id, tbl_ficha_tecnica_encuesta_id,
                   latitud, longitud, precision_metros, altitud,
                   audio_base64, audio_duracion_segundos, audio_formato,
                   dispositivo_info, user_agent, ip_address,
                   fecha_certificacion, dtcreate)
                  VALUES
                  (:tbl_votante_id, :tbl_usuario_id,
                   :origen_tipo, :tbl_sondeo_id, :tbl_ficha_tecnica_encuesta_id,
                   :latitud, :longitud, :precision_metros, :altitud,
                   :audio_base64, :audio_duracion_segundos, :audio_formato,
                   :dispositivo_info, :user_agent, :ip_address,
                   NOW(), NOW())";

            $stmt = $pdo->prepare($q);
            $stmt->execute([
                ':tbl_votante_id' => $tbl_votante_id,
                ':tbl_usuario_id' => $tbl_usuario_id,
                ':origen_tipo' => $origen_tipo,
                ':tbl_sondeo_id' => $tbl_sondeo_id,
                ':tbl_ficha_tecnica_encuesta_id' => $tbl_ficha_tecnica_encuesta_id,
                ':latitud' => $latitud,
                ':longitud' => $longitud,
                ':precision_metros' => $precision_metros,
                ':altitud' => $altitud,
                ':audio_base64' => $audio_base64,
                ':audio_duracion_segundos' => $audio_duracion_segundos,
                ':audio_formato' => $audio_formato,
                ':dispositivo_info' => $dispositivo_info,
                ':user_agent' => $user_agent,
                ':ip_address' => $ip_address
            ]);

            $id = $pdo->lastInsertId();
            $pdo->commit();
            $db->closeConect();

            return [
                'output' => [
                    'valid' => true,
                    'message' => 'Validación guardada correctamente',
                    'id' => $id
                ]
            ];

        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $db->closeConect();
            return Util::error_general('Error al guardar la validación: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene certificaciones por votante
     * @param array $rqst Parámetros de búsqueda
     * @return array Resultado de la operación
     */
    public static function getByVotante($rqst)
    {
        $tbl_votante_id = isset($rqst['tbl_votante_id']) ? intval($rqst['tbl_votante_id']) : 0;

        if ($tbl_votante_id <= 0) {
            return Util::error_missing_data_description('ID de votante requerido');
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $q = "SELECT
                    c.*,
                    u.nombre as encuestador_nombre,
                    u.apellido as encuestador_apellido,
                    v.nombre_completo as votante_nombre,
                    s.sondeo as sondeo_nombre,
                    f.tema as cuestionario_nombre
                  FROM " . $db->getTable('tbl_certificacion_encuestador') . " c
                  INNER JOIN " . $db->getTable('tbl_usuarios') . " u ON c.tbl_usuario_id = u.id
                  INNER JOIN " . $db->getTable('tbl_votantes') . " v ON c.tbl_votante_id = v.id
                  LEFT JOIN " . $db->getTable('tbl_sondeo') . " s ON c.tbl_sondeo_id = s.id
                  LEFT JOIN " . $db->getTable('tbl_ficha_tecnica_encuestas') . " f ON c.tbl_ficha_tecnica_encuesta_id = f.id
                  WHERE c.tbl_votante_id = :tbl_votante_id
                  ORDER BY c.fecha_certificacion DESC";

            $stmt = $pdo->prepare($q);
            $stmt->execute([':tbl_votante_id' => $tbl_votante_id]);
            $certificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $db->closeConect();

            return [
                'output' => [
                    'valid' => true,
                    'response' => $certificaciones
                ]
            ];

        } catch (Exception $e) {
            $db->closeConect();
            return Util::error_general('Error al obtener certificaciones: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene todas las certificaciones con filtros
     * @param array $rqst Parámetros de búsqueda
     * @return array Resultado de la operación
     */
    public static function getAll($rqst)
    {
        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $q = "SELECT
                    c.id,
                    c.tbl_votante_id,
                    c.tbl_usuario_id,
                    c.origen_tipo,
                    c.tbl_sondeo_id,
                    c.tbl_ficha_tecnica_encuesta_id,
                    c.latitud,
                    c.longitud,
                    c.precision_metros,
                    c.audio_duracion_segundos,
                    c.fecha_certificacion,
                    c.dtcreate,
                    c.estado_revision,
                    c.revision_metodo,
                    c.revision_fecha,
                    c.revision_usuario_id,
                    u.nombre as encuestador_nombre,
                    u.apellido as encuestador_apellido,
                    v.nombre_completo as votante_nombre,
                    v.email as votante_email,
                    s.sondeo as sondeo_nombre,
                    f.realizada_por_o_encomendada_por as cuestionario_nombre
                  FROM " . $db->getTable('tbl_certificacion_encuestador') . " c
                  INNER JOIN " . $db->getTable('tbl_usuarios') . " u ON c.tbl_usuario_id = u.id
                  INNER JOIN " . $db->getTable('tbl_votantes') . " v ON c.tbl_votante_id = v.id
                  LEFT JOIN " . $db->getTable('tbl_sondeo') . " s ON c.tbl_sondeo_id = s.id
                  LEFT JOIN " . $db->getTable('tbl_ficha_tecnica_encuestas') . " f ON c.tbl_ficha_tecnica_encuesta_id = f.id
                  WHERE 1=1";

            $params = [];
            if (!empty($rqst['estado_revision'])) {
                $q .= " AND c.estado_revision = :estado_revision";
                $params[':estado_revision'] = trim((string)$rqst['estado_revision']);
            }
            if (!empty($rqst['origen_tipo'])) {
                $q .= " AND c.origen_tipo = :origen_tipo";
                $params[':origen_tipo'] = trim((string)$rqst['origen_tipo']);
            }
            if (!empty($rqst['tbl_sondeo_id'])) {
                $q .= " AND c.tbl_sondeo_id = :tbl_sondeo_id";
                $params[':tbl_sondeo_id'] = intval($rqst['tbl_sondeo_id']);
            }
            if (!empty($rqst['tbl_ficha_tecnica_encuesta_id'])) {
                $q .= " AND c.tbl_ficha_tecnica_encuesta_id = :tbl_ficha_tecnica_encuesta_id";
                $params[':tbl_ficha_tecnica_encuesta_id'] = intval($rqst['tbl_ficha_tecnica_encuesta_id']);
            }
            if (!empty($rqst['tbl_usuario_id'])) {
                $q .= " AND c.tbl_usuario_id = :tbl_usuario_id";
                $params[':tbl_usuario_id'] = intval($rqst['tbl_usuario_id']);
            }
            if (!empty($rqst['fecha_desde'])) {
                $q .= " AND DATE(c.fecha_certificacion) >= :fecha_desde";
                $params[':fecha_desde'] = trim((string)$rqst['fecha_desde']);
            }
            if (!empty($rqst['fecha_hasta'])) {
                $q .= " AND DATE(c.fecha_certificacion) <= :fecha_hasta";
                $params[':fecha_hasta'] = trim((string)$rqst['fecha_hasta']);
            }
            if (isset($rqst['con_audio']) && $rqst['con_audio'] !== '') {
                if (intval($rqst['con_audio']) === 1) {
                    $q .= " AND c.audio_duracion_segundos IS NOT NULL AND c.audio_duracion_segundos > 0";
                } else {
                    $q .= " AND (c.audio_duracion_segundos IS NULL OR c.audio_duracion_segundos <= 0)";
                }
            }
            if (!empty($rqst['revision_metodo'])) {
                $q .= " AND c.revision_metodo = :revision_metodo";
                $params[':revision_metodo'] = trim((string)$rqst['revision_metodo']);
            }

            $q .= " ORDER BY c.fecha_certificacion DESC";

            $stmt = $pdo->prepare($q);
            $stmt->execute($params);
            $certificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $db->closeConect();

            return [
                'output' => [
                    'valid' => true,
                    'response' => $certificaciones
                ]
            ];

        } catch (Exception $e) {
            $db->closeConect();
            return Util::error_general('Error al obtener certificaciones: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene detalle completo de una certificación (incluyendo audio)
     * @param array $rqst Parámetros de búsqueda
     * @return array Resultado de la operación
     */
    public static function getDetalle($rqst)
    {
        $id = isset($rqst['id']) ? intval($rqst['id']) : 0;

        if ($id <= 0) {
            return Util::error_missing_data_description('ID de validación requerido');
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $q = "SELECT
                    c.*,
                    u.nombre as encuestador_nombre,
                    u.apellido as encuestador_apellido,
                    u.email as encuestador_email,
                    v.nombre_completo as votante_nombre,
                    v.email as votante_email,
                    v.genero as votante_genero,
                    v.rango_edad as votante_rango_edad,
                    s.sondeo as sondeo_nombre,
                    s.descripcion_sondeo as sondeo_descripcion,
                    f.realizada_por_o_encomendada_por as cuestionario_nombre,
                    f.realizada_por_o_encomendada_por as cuestionario_realizada_por
                  FROM " . $db->getTable('tbl_certificacion_encuestador') . " c
                  INNER JOIN " . $db->getTable('tbl_usuarios') . " u ON c.tbl_usuario_id = u.id
                  INNER JOIN " . $db->getTable('tbl_votantes') . " v ON c.tbl_votante_id = v.id
                  LEFT JOIN " . $db->getTable('tbl_sondeo') . " s ON c.tbl_sondeo_id = s.id
                  LEFT JOIN " . $db->getTable('tbl_ficha_tecnica_encuestas') . " f ON c.tbl_ficha_tecnica_encuesta_id = f.id
                  WHERE c.id = :id";

            $stmt = $pdo->prepare($q);
            $stmt->execute([':id' => $id]);
            $certificacion = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$certificacion) {
                $db->closeConect();
                return Util::error_no_result();
            }

            // Obtener respuestas del sondeo si aplica
            $certificacion['respuestas_sondeo'] = [];
            if ($certificacion['origen_tipo'] === 'sondeo' && $certificacion['tbl_votante_id']) {
                $qSondeo = "SELECT
                        rs.id,
                        rs.tbl_sondeo_id,
                        rs.tbl_candidato_id,
                        rs.tbl_sondeo_x_opciones_id,
                        rs.dtcreate as fecha_respuesta,
                        p.nombre_completo as candidato_nombre,
                        p.foto as candidato_foto,
                        cp.nombre as candidato_cargo,
                        so.opcion as opcion_texto
                    FROM " . $db->getTable('tbl_respuestas_sondeos') . " rs
                    LEFT JOIN " . $db->getTable('tbl_participantes') . " p ON rs.tbl_candidato_id = p.id
                    LEFT JOIN " . $db->getTable('tbl_cargos_publicos') . " cp ON p.tbl_cargo_publico_id = cp.id
                    LEFT JOIN " . $db->getTable('tbl_sondeo_x_opciones') . " so ON rs.tbl_sondeo_x_opciones_id = so.id
                    WHERE rs.tbl_votante_id = :votante_id
                    AND rs.tbl_sondeo_id = :sondeo_id
                    ORDER BY rs.dtcreate DESC";

                $stmtSondeo = $pdo->prepare($qSondeo);
                $stmtSondeo->execute([
                    ':votante_id' => $certificacion['tbl_votante_id'],
                    ':sondeo_id' => $certificacion['tbl_sondeo_id']
                ]);
                $certificacion['respuestas_sondeo'] = $stmtSondeo->fetchAll(PDO::FETCH_ASSOC);
            }

            // Obtener respuestas del cuestionario si aplica
            $certificacion['respuestas_cuestionario'] = [];
            if ($certificacion['origen_tipo'] === 'cuestionario' && $certificacion['tbl_votante_id']) {
                // Primero obtener el intento
                $qIntento = "SELECT id FROM " . $db->getTable('tbl_cuestionario_intentos') . "
                    WHERE tbl_ficha_tecnica_encuesta_id = :ficha_id
                    AND tbl_votante_id = :votante_id
                    ORDER BY fecha_respuesta DESC LIMIT 1";

                $stmtIntento = $pdo->prepare($qIntento);
                $stmtIntento->execute([
                    ':ficha_id' => $certificacion['tbl_ficha_tecnica_encuesta_id'],
                    ':votante_id' => $certificacion['tbl_votante_id']
                ]);
                $intento = $stmtIntento->fetch(PDO::FETCH_ASSOC);

                if ($intento) {
                    $qCuestionario = "SELECT
                            r.id,
                            r.tbl_pregunta_id,
                            r.tbl_opcion_respuesta_id,
                            r.respuesta_texto,
                            p.texto_pregunta,
                            p.tipo_pregunta,
                            p.orden,
                            o.texto_opcion
                        FROM " . $db->getTable('tbl_cuestionario_respuestas') . " r
                        INNER JOIN " . $db->getTable('tbl_preguntas') . " p ON r.tbl_pregunta_id = p.id
                        LEFT JOIN " . $db->getTable('tbl_opciones_respuesta') . " o ON r.tbl_opcion_respuesta_id = o.id
                        WHERE r.tbl_intento_id = :intento_id
                        ORDER BY p.orden ASC, r.id ASC";

                    $stmtCuestionario = $pdo->prepare($qCuestionario);
                    $stmtCuestionario->execute([':intento_id' => $intento['id']]);
                    $respuestas = $stmtCuestionario->fetchAll(PDO::FETCH_ASSOC);

                    // Agrupar respuestas por pregunta
                    $preguntasAgrupadas = [];
                    foreach ($respuestas as $resp) {
                        $preguntaId = $resp['tbl_pregunta_id'];
                        if (!isset($preguntasAgrupadas[$preguntaId])) {
                            $preguntasAgrupadas[$preguntaId] = [
                                'pregunta_id' => $preguntaId,
                                'texto_pregunta' => $resp['texto_pregunta'],
                                'tipo_pregunta' => $resp['tipo_pregunta'],
                                'orden' => $resp['orden'],
                                'respuestas' => []
                            ];
                        }
                        if ($resp['texto_opcion']) {
                            $preguntasAgrupadas[$preguntaId]['respuestas'][] = $resp['texto_opcion'];
                        } elseif ($resp['respuesta_texto']) {
                            $preguntasAgrupadas[$preguntaId]['respuestas'][] = $resp['respuesta_texto'];
                        }
                    }
                    $certificacion['respuestas_cuestionario'] = array_values($preguntasAgrupadas);
                }
            }

            $certificacion['tiene_audio'] = !empty($certificacion['audio_base64'])
                || intval($certificacion['audio_duracion_segundos'] ?? 0) > 0;
            // No enviar base64 completo al listado de historial embebido; el detalle lo necesita el player.
            $certificacion['historial_count'] = 0;
            try {
                $qh = "SELECT COUNT(*) AS total FROM " . $db->getTable('tbl_certificacion_revision_historial')
                    . " WHERE tbl_certificacion_id = :id";
                $sth = $pdo->prepare($qh);
                $sth->execute([':id' => $id]);
                $certificacion['historial_count'] = intval(($sth->fetch(PDO::FETCH_ASSOC)['total'] ?? 0));
            } catch (Exception $eHist) {
                $certificacion['historial_count'] = 0;
            }

            $db->closeConect();

            return [
                'output' => [
                    'valid' => true,
                    'response' => $certificacion
                ]
            ];

        } catch (Exception $e) {
            $db->closeConect();
            return Util::error_general('Error al obtener detalle de validación: ' . $e->getMessage());
        }
    }

    public static function guardarRevision($rqst)
    {
        $id = isset($rqst['id']) ? intval($rqst['id']) : 0;
        $estado = isset($rqst['estado_revision']) ? trim((string)$rqst['estado_revision']) : '';
        $comentario = isset($rqst['revision_comentario']) ? trim((string)$rqst['revision_comentario']) : '';
        $metodo = isset($rqst['revision_metodo']) ? trim((string)$rqst['revision_metodo']) : 'manual';
        $transcripcion = array_key_exists('revision_transcripcion', $rqst)
            ? (string)$rqst['revision_transcripcion']
            : null;
        $iaJson = array_key_exists('revision_ia_json', $rqst)
            ? (is_string($rqst['revision_ia_json']) ? $rqst['revision_ia_json'] : json_encode($rqst['revision_ia_json'], JSON_UNESCAPED_UNICODE))
            : null;
        $usuarioId = intval($_SESSION['session_user']['id'] ?? 0);

        $estadosOk = ['pendiente', 'bien', 'mal', 'en_revision', 'anulada'];
        if ($id <= 0 || !in_array($estado, $estadosOk, true)) {
            return Util::error_missing_data_description('ID y estado_revision válidos son requeridos');
        }
        if (!in_array($metodo, ['manual', 'ia'], true)) {
            $metodo = 'manual';
        }
        if ($estado === 'mal' && $comentario === '') {
            return Util::error_missing_data_description('El comentario es obligatorio cuando el estado es Mal');
        }
        if ($usuarioId <= 0) {
            return Util::error_general('Sesión de usuario inválida');
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $pdo->beginTransaction();
            $qActual = "SELECT id, estado_revision, revision_metodo FROM "
                . $db->getTable('tbl_certificacion_encuestador') . " WHERE id = :id FOR UPDATE";
            $stmt = $pdo->prepare($qActual);
            $stmt->execute([':id' => $id]);
            $actual = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$actual) {
                $pdo->rollBack();
                $db->closeConect();
                return Util::error_no_result();
            }

            $estadoAnterior = (string)($actual['estado_revision'] ?? 'pendiente');
            $esRevalidacion = ($estadoAnterior !== 'pendiente');
            if ($esRevalidacion && !SessionData::hasPermission('certificaciones.revalidar')
                && !SessionData::superAdministrador()) {
                $pdo->rollBack();
                $db->closeConect();
                return [
                    'output' => [
                        'valid' => false,
                        'error' => 'permiso_denegado',
                        'response' => ['content' => 'No tienes permiso para revalidar una validación ya revisada.']
                    ]
                ];
            }

            $qUp = "UPDATE " . $db->getTable('tbl_certificacion_encuestador') . "
                    SET estado_revision = :estado,
                        revision_metodo = :metodo,
                        revision_comentario = :comentario,
                        revision_transcripcion = COALESCE(:transcripcion, revision_transcripcion),
                        revision_ia_json = COALESCE(:ia_json, revision_ia_json),
                        revision_usuario_id = :uid,
                        revision_fecha = NOW(),
                        dtupdate = NOW()
                    WHERE id = :id";
            $stmtUp = $pdo->prepare($qUp);
            $stmtUp->execute([
                ':estado' => $estado,
                ':metodo' => $metodo,
                ':comentario' => $comentario !== '' ? $comentario : null,
                ':transcripcion' => ($transcripcion !== null && $transcripcion !== '') ? $transcripcion : null,
                ':ia_json' => ($iaJson !== null && $iaJson !== '') ? $iaJson : null,
                ':uid' => $usuarioId,
                ':id' => $id,
            ]);

            $qHist = "INSERT INTO " . $db->getTable('tbl_certificacion_revision_historial') . "
                      (tbl_certificacion_id, estado_anterior, estado_nuevo, metodo, comentario, transcripcion, ia_json, tbl_usuario_id, dtcreate)
                      VALUES (:cid, :ant, :nuevo, :metodo, :comentario, :transcripcion, :ia_json, :uid, NOW())";
            $stmtHist = $pdo->prepare($qHist);
            $stmtHist->execute([
                ':cid' => $id,
                ':ant' => $estadoAnterior,
                ':nuevo' => $estado,
                ':metodo' => $metodo,
                ':comentario' => $comentario !== '' ? $comentario : null,
                ':transcripcion' => ($transcripcion !== null && $transcripcion !== '') ? $transcripcion : null,
                ':ia_json' => ($iaJson !== null && $iaJson !== '') ? $iaJson : null,
                ':uid' => $usuarioId,
            ]);

            $pdo->commit();
            $db->closeConect();
            return [
                'output' => [
                    'valid' => true,
                    'message' => 'Revisión guardada',
                    'response' => [
                        'id' => $id,
                        'estado_revision' => $estado,
                        'revision_metodo' => $metodo,
                        'revalidacion' => $esRevalidacion,
                    ]
                ]
            ];
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $db->closeConect();
            return Util::error_general('Error al guardar revisión: ' . $e->getMessage());
        }
    }

    public static function validarConIa($rqst)
    {
        $id = isset($rqst['id']) ? intval($rqst['id']) : 0;
        if ($id <= 0) {
            return Util::error_missing_data_description('ID de validación requerido');
        }

        $detalle = self::getDetalle(['id' => $id]);
        if (!($detalle['output']['valid'] ?? false)) {
            return $detalle;
        }
        $cert = $detalle['output']['response'];

        require_once __DIR__ . '/ia/CertificacionValidadorIA.php';
        $preview = CertificacionValidadorIA::validarPreview($cert);

        if (!($preview['valid'] ?? false)) {
            return [
                'output' => [
                    'valid' => false,
                    'error' => $preview['error'] ?? 'validacion_ia',
                    'response' => ['content' => $preview['message'] ?? 'Error al validar con IA']
                ]
            ];
        }

        $nota = $preview['nota_calidad'] ?? [];
        $comentarioCompuesto = trim((string)($preview['comentario'] ?? ''));
        $extras = [];
        foreach (['lenguaje' => 'Lenguaje', 'gps' => 'GPS', 'manipulacion' => 'Manipulación'] as $k => $label) {
            $v = trim((string)($nota[$k] ?? ''));
            if ($v !== '' && strtolower($v) !== 'ok') {
                $extras[] = $label . ': ' . $v;
            }
        }
        if ($extras) {
            $comentarioCompuesto .= ($comentarioCompuesto !== '' ? "\n" : '') . implode(' · ', $extras);
        }

        return [
            'output' => [
                'valid' => true,
                'response' => [
                    'id' => $id,
                    'veredicto' => $preview['veredicto'],
                    'comentario' => $comentarioCompuesto,
                    'transcripcion' => $preview['transcripcion'],
                    'nota_calidad' => $nota,
                    'coincidencias' => $preview['coincidencias'],
                    'confianza' => $preview['confianza'],
                    'ia_json' => $preview['ia_json'],
                    'persistido' => false,
                ]
            ]
        ];
    }

    public static function getHistorial($rqst)
    {
        $id = isset($rqst['id']) ? intval($rqst['id']) : 0;
        if ($id <= 0) {
            return Util::error_missing_data_description('ID de validación requerido');
        }

        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $q = "SELECT h.*, u.nombre AS revisor_nombre, u.apellido AS revisor_apellido
                  FROM " . $db->getTable('tbl_certificacion_revision_historial') . " h
                  LEFT JOIN " . $db->getTable('tbl_usuarios') . " u ON h.tbl_usuario_id = u.id
                  WHERE h.tbl_certificacion_id = :id
                  ORDER BY h.dtcreate DESC, h.id DESC";
            $stmt = $pdo->prepare($q);
            $stmt->execute([':id' => $id]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $db->closeConect();
            return ['output' => ['valid' => true, 'response' => $rows]];
        } catch (Exception $e) {
            $db->closeConect();
            return Util::error_general('Error al obtener historial: ' . $e->getMessage());
        }
    }

    public static function getEncuestasVinculadas($rqst = [])
    {
        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $items = [];

            $qCq = "SELECT
                        c.id AS certificacion_id,
                        c.estado_revision,
                        c.fecha_certificacion,
                        c.tbl_usuario_id,
                        c.tbl_votante_id,
                        c.tbl_ficha_tecnica_encuesta_id AS encuesta_id,
                        'cuestionario' AS tipo,
                        f.realizada_por_o_encomendada_por AS encuesta_nombre,
                        i.id AS intento_id,
                        i.fecha_respuesta,
                        u.nombre AS encuestador_nombre,
                        u.apellido AS encuestador_apellido,
                        v.nombre_completo AS votante_nombre
                    FROM " . $db->getTable('tbl_certificacion_encuestador') . " c
                    INNER JOIN " . $db->getTable('tbl_usuarios') . " u ON c.tbl_usuario_id = u.id
                    INNER JOIN " . $db->getTable('tbl_votantes') . " v ON c.tbl_votante_id = v.id
                    LEFT JOIN " . $db->getTable('tbl_ficha_tecnica_encuestas') . " f ON c.tbl_ficha_tecnica_encuesta_id = f.id
                    LEFT JOIN " . $db->getTable('tbl_cuestionario_intentos') . " i
                        ON i.id = (
                            SELECT i2.id FROM " . $db->getTable('tbl_cuestionario_intentos') . " i2
                            WHERE i2.tbl_ficha_tecnica_encuesta_id = c.tbl_ficha_tecnica_encuesta_id
                              AND i2.tbl_votante_id = c.tbl_votante_id
                            ORDER BY i2.fecha_respuesta DESC LIMIT 1
                        )
                    WHERE c.origen_tipo = 'cuestionario'
                    ORDER BY c.fecha_certificacion DESC";
            foreach ($pdo->query($qCq) as $row) {
                $items[] = $row;
            }

            $qSn = "SELECT
                        c.id AS certificacion_id,
                        c.estado_revision,
                        c.fecha_certificacion,
                        c.tbl_usuario_id,
                        c.tbl_votante_id,
                        c.tbl_sondeo_id AS encuesta_id,
                        'sondeo' AS tipo,
                        s.sondeo AS encuesta_nombre,
                        NULL AS intento_id,
                        c.fecha_certificacion AS fecha_respuesta,
                        u.nombre AS encuestador_nombre,
                        u.apellido AS encuestador_apellido,
                        v.nombre_completo AS votante_nombre
                    FROM " . $db->getTable('tbl_certificacion_encuestador') . " c
                    INNER JOIN " . $db->getTable('tbl_usuarios') . " u ON c.tbl_usuario_id = u.id
                    INNER JOIN " . $db->getTable('tbl_votantes') . " v ON c.tbl_votante_id = v.id
                    LEFT JOIN " . $db->getTable('tbl_sondeo') . " s ON c.tbl_sondeo_id = s.id
                    WHERE c.origen_tipo = 'sondeo'
                    ORDER BY c.fecha_certificacion DESC";
            foreach ($pdo->query($qSn) as $row) {
                $items[] = $row;
            }

            $db->closeConect();
            return ['output' => ['valid' => true, 'response' => $items]];
        } catch (Exception $e) {
            $db->closeConect();
            return Util::error_general('Error al listar encuestas vinculadas: ' . $e->getMessage());
        }
    }

    public static function getDashboardKpis($rqst = [])
    {
        $filtros = self::buildDashboardWhere($rqst);
        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            // Mismos INNER JOIN que getAll/getDashboardList para que total = filas visibles.
            $base = "FROM " . $db->getTable('tbl_certificacion_encuestador') . " c
                     INNER JOIN " . $db->getTable('tbl_usuarios') . " u ON c.tbl_usuario_id = u.id
                     INNER JOIN " . $db->getTable('tbl_votantes') . " v ON c.tbl_votante_id = v.id
                     WHERE 1=1 " . $filtros['sql'];

            $stmt = $pdo->prepare("SELECT COUNT(*) AS total $base");
            $stmt->execute($filtros['params']);
            $total = (int)($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

            $porEstado = [];
            foreach (['pendiente', 'bien', 'mal', 'en_revision', 'anulada'] as $est) {
                $stmtE = $pdo->prepare("SELECT COUNT(*) AS total $base AND c.estado_revision = :est");
                $paramsE = $filtros['params'];
                $paramsE[':est'] = $est;
                $stmtE->execute($paramsE);
                $porEstado[$est] = (int)($stmtE->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);
            }

            $stmtAudio = $pdo->prepare("SELECT COUNT(*) AS total $base AND c.audio_duracion_segundos IS NOT NULL AND c.audio_duracion_segundos > 0");
            $stmtAudio->execute($filtros['params']);
            $conAudio = (int)($stmtAudio->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

            $stmtIa = $pdo->prepare("SELECT COUNT(*) AS total $base AND c.revision_metodo = 'ia'");
            $stmtIa->execute($filtros['params']);
            $conIa = (int)($stmtIa->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

            $stmtMan = $pdo->prepare("SELECT COUNT(*) AS total $base AND c.revision_metodo = 'manual'");
            $stmtMan->execute($filtros['params']);
            $conManual = (int)($stmtMan->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

            $porOrigen = ['cuestionario' => 0, 'sondeo' => 0];
            $stmtOr = $pdo->prepare(
                "SELECT COALESCE(NULLIF(TRIM(c.origen_tipo), ''), '') AS origen, COUNT(*) AS total
                 $base
                 AND COALESCE(NULLIF(TRIM(c.origen_tipo), ''), '') IN ('cuestionario', 'sondeo')
                 GROUP BY COALESCE(NULLIF(TRIM(c.origen_tipo), ''), '')"
            );
            $stmtOr->execute($filtros['params']);
            foreach ($stmtOr->fetchAll(PDO::FETCH_ASSOC) as $rowOr) {
                $key = (string)($rowOr['origen'] ?? '');
                if ($key === 'cuestionario' || $key === 'sondeo') {
                    $porOrigen[$key] = (int)($rowOr['total'] ?? 0);
                }
            }

            $stmtTop = $pdo->prepare(
                "SELECT c.tbl_usuario_id AS uid,
                        TRIM(CONCAT(COALESCE(u.nombre,''), ' ', COALESCE(u.apellido,''))) AS nombre,
                        COUNT(*) AS total
                 $base
                 GROUP BY c.tbl_usuario_id, u.nombre, u.apellido
                 ORDER BY total DESC
                 LIMIT 8"
            );
            $stmtTop->execute($filtros['params']);
            $topEncuestadores = [];
            foreach ($stmtTop->fetchAll(PDO::FETCH_ASSOC) as $rowTop) {
                $nombre = trim((string)($rowTop['nombre'] ?? ''));
                if ($nombre === '') {
                    $nombre = 'Encuestador #' . (int)($rowTop['uid'] ?? 0);
                }
                $topEncuestadores[] = [
                    'uid' => (int)($rowTop['uid'] ?? 0),
                    'nombre' => $nombre,
                    'total' => (int)($rowTop['total'] ?? 0),
                ];
            }

            $stmtGps = $pdo->prepare(
                "SELECT COUNT(*) AS total $base
                 AND c.latitud IS NOT NULL AND c.longitud IS NOT NULL
                 AND TRIM(CAST(c.latitud AS CHAR)) <> '' AND TRIM(CAST(c.longitud AS CHAR)) <> ''"
            );
            $stmtGps->execute($filtros['params']);
            $conGps = (int)($stmtGps->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

            $revisadas = $porEstado['bien'] + $porEstado['mal'] + $porEstado['en_revision'] + $porEstado['anulada'];
            $pctBien = $revisadas > 0 ? round(($porEstado['bien'] / $revisadas) * 100, 1) : 0.0;

            $db->closeConect();
            return [
                'output' => [
                    'valid' => true,
                    'response' => [
                        'total' => $total,
                        'por_estado' => $porEstado,
                        'por_origen' => $porOrigen,
                        'top_encuestadores' => $topEncuestadores,
                        'con_audio' => $conAudio,
                        'sin_audio' => max(0, $total - $conAudio),
                        'con_gps' => $conGps,
                        'metodo_ia' => $conIa,
                        'metodo_manual' => $conManual,
                        'pct_bien_sobre_revisadas' => $pctBien,
                        'revisadas' => $revisadas,
                    ]
                ]
            ];
        } catch (Exception $e) {
            $db->closeConect();
            return Util::error_general('Error KPIs: ' . $e->getMessage());
        }
    }

    public static function getDashboardList($rqst = [])
    {
        return self::getAll($rqst);
    }

    /**
     * Resumen de certificación por encuestador (para asistente IA y reportes).
     * Busca por id o por nombre/apellido parcial.
     */
    public static function getResumenPorEncuestador($rqst = [])
    {
        $usuarioId = isset($rqst['tbl_usuario_id']) ? intval($rqst['tbl_usuario_id']) : 0;
        $nombre = trim((string)($rqst['encuestador_nombre'] ?? $rqst['nombre'] ?? ''));
        $filtrosExtra = self::buildDashboardWhere(array_diff_key($rqst, array_flip([
            'tbl_usuario_id', 'encuestador_nombre', 'nombre', 'limite',
        ])));

        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $params = $filtrosExtra['params'];
            $whereUsr = '';
            if ($usuarioId > 0) {
                $whereUsr = ' AND c.tbl_usuario_id = :uid_exact';
                $params[':uid_exact'] = $usuarioId;
            } elseif ($nombre !== '') {
                $whereUsr = ' AND (
                    LOWER(CONCAT(COALESCE(u.nombre,\'\'), \' \', COALESCE(u.apellido,\'\'))) LIKE :nombre_like
                    OR LOWER(COALESCE(u.nombre,\'\')) LIKE :nombre_like
                    OR LOWER(COALESCE(u.apellido,\'\')) LIKE :nombre_like
                )';
                $params[':nombre_like'] = '%' . mb_strtolower($nombre, 'UTF-8') . '%';
            }

            $limite = isset($rqst['limite']) ? max(1, min(50, intval($rqst['limite']))) : 20;

            $q = "SELECT
                    c.tbl_usuario_id AS uid,
                    TRIM(CONCAT(COALESCE(u.nombre,''), ' ', COALESCE(u.apellido,''))) AS nombre,
                    COUNT(*) AS total,
                    SUM(CASE WHEN c.estado_revision = 'pendiente' THEN 1 ELSE 0 END) AS pendiente,
                    SUM(CASE WHEN c.estado_revision = 'bien' THEN 1 ELSE 0 END) AS bien,
                    SUM(CASE WHEN c.estado_revision = 'mal' THEN 1 ELSE 0 END) AS mal,
                    SUM(CASE WHEN c.estado_revision = 'en_revision' THEN 1 ELSE 0 END) AS en_revision,
                    SUM(CASE WHEN c.estado_revision = 'anulada' THEN 1 ELSE 0 END) AS anulada,
                    SUM(CASE WHEN c.audio_duracion_segundos IS NOT NULL AND c.audio_duracion_segundos > 0 THEN 1 ELSE 0 END) AS con_audio,
                    SUM(CASE WHEN c.revision_metodo = 'ia' THEN 1 ELSE 0 END) AS metodo_ia,
                    SUM(CASE WHEN c.revision_metodo = 'manual' THEN 1 ELSE 0 END) AS metodo_manual,
                    SUM(CASE WHEN c.origen_tipo = 'sondeo' THEN 1 ELSE 0 END) AS sondeos,
                    SUM(CASE WHEN c.origen_tipo = 'cuestionario' THEN 1 ELSE 0 END) AS encuestas,
                    MAX(c.fecha_certificacion) AS ultima_fecha
                  FROM " . $db->getTable('tbl_certificacion_encuestador') . " c
                  INNER JOIN " . $db->getTable('tbl_usuarios') . " u ON c.tbl_usuario_id = u.id
                  INNER JOIN " . $db->getTable('tbl_votantes') . " v ON c.tbl_votante_id = v.id
                  WHERE 1=1 " . $filtrosExtra['sql'] . $whereUsr . "
                  GROUP BY c.tbl_usuario_id, u.nombre, u.apellido
                  ORDER BY total DESC
                  LIMIT " . (int)$limite;

            $stmt = $pdo->prepare($q);
            $stmt->execute($params);
            $rows = [];
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
                $total = (int)($r['total'] ?? 0);
                $bien = (int)($r['bien'] ?? 0);
                $mal = (int)($r['mal'] ?? 0);
                $revisadas = $bien + $mal + (int)($r['en_revision'] ?? 0) + (int)($r['anulada'] ?? 0);
                $nombreRow = trim((string)($r['nombre'] ?? ''));
                if ($nombreRow === '') {
                    $nombreRow = 'Encuestador #' . (int)($r['uid'] ?? 0);
                }
                $rows[] = [
                    'uid' => (int)($r['uid'] ?? 0),
                    'nombre' => $nombreRow,
                    'total' => $total,
                    'pendiente' => (int)($r['pendiente'] ?? 0),
                    'bien' => $bien,
                    'mal' => $mal,
                    'en_revision' => (int)($r['en_revision'] ?? 0),
                    'anulada' => (int)($r['anulada'] ?? 0),
                    'con_audio' => (int)($r['con_audio'] ?? 0),
                    'metodo_ia' => (int)($r['metodo_ia'] ?? 0),
                    'metodo_manual' => (int)($r['metodo_manual'] ?? 0),
                    'sondeos' => (int)($r['sondeos'] ?? 0),
                    'encuestas' => (int)($r['encuestas'] ?? 0),
                    'revisadas' => $revisadas,
                    'pct_bien_sobre_revisadas' => $revisadas > 0 ? round(($bien / $revisadas) * 100, 1) : 0.0,
                    'ultima_fecha' => $r['ultima_fecha'] ?? null,
                ];
            }

            $db->closeConect();
            return [
                'output' => [
                    'valid' => true,
                    'response' => [
                        'encuestadores' => $rows,
                        'coincidencias' => count($rows),
                    ],
                ],
            ];
        } catch (Exception $e) {
            $db->closeConect();
            return Util::error_general('Error resumen encuestador: ' . $e->getMessage());
        }
    }

    public static function exportDashboardExcel($rqst = [])
    {
        $list = self::getAll($rqst);
        if (!($list['output']['valid'] ?? false)) {
            return $list;
        }
        $rows = $list['output']['response'] ?? [];

        $headers = [
            'ID', 'Fecha', 'Estado', 'Método', 'Encuestador', 'Encuestado', 'Origen',
            'Sondeo/Cuestionario', 'Audio_seg', 'GPS_lat', 'GPS_lng', 'Fecha_revision', 'Comentario'
        ];

        $db = new DbConection();
        $pdo = $db->openConect();
        $comentarios = [];
        try {
            $ids = array_map(function ($r) { return (int)$r['id']; }, $rows);
            if ($ids) {
                $in = implode(',', array_fill(0, count($ids), '?'));
                $stmt = $pdo->prepare(
                    "SELECT id, revision_comentario FROM " . $db->getTable('tbl_certificacion_encuestador')
                    . " WHERE id IN ($in)"
                );
                $stmt->execute($ids);
                foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $c) {
                    $comentarios[(int)$c['id']] = (string)($c['revision_comentario'] ?? '');
                }
            }
        } catch (Exception $e) {
            // export without comments if column missing
        }
        $db->closeConect();

        $fh = fopen('php://temp', 'r+');
        fprintf($fh, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($fh, $headers, ';');
        foreach ($rows as $r) {
            $origen = $r['origen_tipo'] ?? '';
            $nombreOrigen = $origen === 'sondeo'
                ? ($r['sondeo_nombre'] ?? '')
                : ($origen === 'cuestionario' ? ($r['cuestionario_nombre'] ?? '') : '');
            fputcsv($fh, [
                $r['id'] ?? '',
                $r['fecha_certificacion'] ?? '',
                $r['estado_revision'] ?? 'pendiente',
                $r['revision_metodo'] ?? 'ninguno',
                trim(($r['encuestador_nombre'] ?? '') . ' ' . ($r['encuestador_apellido'] ?? '')),
                $r['votante_nombre'] ?? '',
                $origen,
                $nombreOrigen,
                $r['audio_duracion_segundos'] ?? '',
                $r['latitud'] ?? '',
                $r['longitud'] ?? '',
                $r['revision_fecha'] ?? '',
                $comentarios[(int)($r['id'] ?? 0)] ?? '',
            ], ';');
        }
        rewind($fh);
        $csv = stream_get_contents($fh);
        fclose($fh);

        return [
            'output' => [
                'valid' => true,
                'response' => [
                    'filename' => 'certificaciones_' . date('Ymd_His') . '.csv',
                    'mime' => 'text/csv; charset=UTF-8',
                    'content_base64' => base64_encode($csv),
                ]
            ]
        ];
    }

    private static function buildDashboardWhere($rqst): array
    {
        $sql = '';
        $params = [];
        if (!empty($rqst['estado_revision'])) {
            $sql .= ' AND c.estado_revision = :estado_revision';
            $params[':estado_revision'] = trim((string)$rqst['estado_revision']);
        }
        if (!empty($rqst['origen_tipo'])) {
            $sql .= ' AND c.origen_tipo = :origen_tipo';
            $params[':origen_tipo'] = trim((string)$rqst['origen_tipo']);
        }
        if (!empty($rqst['tbl_sondeo_id'])) {
            $sql .= ' AND c.tbl_sondeo_id = :tbl_sondeo_id';
            $params[':tbl_sondeo_id'] = intval($rqst['tbl_sondeo_id']);
        }
        if (!empty($rqst['tbl_ficha_tecnica_encuesta_id'])) {
            $sql .= ' AND c.tbl_ficha_tecnica_encuesta_id = :tbl_ficha_tecnica_encuesta_id';
            $params[':tbl_ficha_tecnica_encuesta_id'] = intval($rqst['tbl_ficha_tecnica_encuesta_id']);
        }
        if (!empty($rqst['tbl_usuario_id'])) {
            $sql .= ' AND c.tbl_usuario_id = :tbl_usuario_id';
            $params[':tbl_usuario_id'] = intval($rqst['tbl_usuario_id']);
        }
        if (!empty($rqst['fecha_desde'])) {
            $sql .= ' AND DATE(c.fecha_certificacion) >= :fecha_desde';
            $params[':fecha_desde'] = trim((string)$rqst['fecha_desde']);
        }
        if (!empty($rqst['fecha_hasta'])) {
            $sql .= ' AND DATE(c.fecha_certificacion) <= :fecha_hasta';
            $params[':fecha_hasta'] = trim((string)$rqst['fecha_hasta']);
        }
        if (!empty($rqst['revision_metodo'])) {
            $sql .= ' AND c.revision_metodo = :revision_metodo';
            $params[':revision_metodo'] = trim((string)$rqst['revision_metodo']);
        }
        if (isset($rqst['con_audio']) && $rqst['con_audio'] !== '') {
            if (intval($rqst['con_audio']) === 1) {
                $sql .= ' AND c.audio_duracion_segundos IS NOT NULL AND c.audio_duracion_segundos > 0';
            } else {
                $sql .= ' AND (c.audio_duracion_segundos IS NULL OR c.audio_duracion_segundos <= 0)';
            }
        }
        return ['sql' => $sql, 'params' => $params];
    }

    /**
     * Cuenta certificaciones del encuestador logueado para la encuesta/sondeo actual.
     * Prioridad: ficha técnica (cuestionario); si no, sondeo.
     * @param array $rqst
     * @return int
     */
    public static function countByUsuarioEncuestaActual($rqst = [])
    {
        $tbl_usuario_id = isset($rqst['tbl_usuario_id'])
            ? intval($rqst['tbl_usuario_id'])
            : intval($_SESSION['session_user']['id'] ?? 0);
        $fichaId = isset($rqst['tbl_ficha_tecnica_encuesta_id']) ? intval($rqst['tbl_ficha_tecnica_encuesta_id']) : 0;
        $sondeoId = isset($rqst['tbl_sondeo_id']) ? intval($rqst['tbl_sondeo_id']) : 0;

        if ($tbl_usuario_id <= 0 || ($fichaId <= 0 && $sondeoId <= 0)) {
            return 0;
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            if ($fichaId > 0) {
                $q = "SELECT COUNT(*) AS total
                      FROM " . $db->getTable('tbl_certificacion_encuestador') . "
                      WHERE tbl_usuario_id = :uid
                        AND tbl_ficha_tecnica_encuesta_id = :ficha_id";
                $stmt = $pdo->prepare($q);
                $stmt->execute([':uid' => $tbl_usuario_id, ':ficha_id' => $fichaId]);
            } else {
                $q = "SELECT COUNT(*) AS total
                      FROM " . $db->getTable('tbl_certificacion_encuestador') . "
                      WHERE tbl_usuario_id = :uid
                        AND tbl_sondeo_id = :sondeo_id";
                $stmt = $pdo->prepare($q);
                $stmt->execute([':uid' => $tbl_usuario_id, ':sondeo_id' => $sondeoId]);
            }

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return intval($row['total'] ?? 0);
        } catch (Exception $e) {
            return 0;
        } finally {
            $db->closeConect();
        }
    }

    /**
     * Obtiene la IP real del cliente
     * @return string IP del cliente
     */
    private static function getClientIP()
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if (isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }
}
