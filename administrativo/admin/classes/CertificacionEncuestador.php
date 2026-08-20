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
                    'message' => 'Certificación guardada correctamente',
                    'id' => $id
                ]
            ];

        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $db->closeConect();
            return Util::error_general('Error al guardar certificación: ' . $e->getMessage());
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
                  ORDER BY c.fecha_certificacion DESC";

            $stmt = $pdo->prepare($q);
            $stmt->execute();
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
            return Util::error_missing_data_description('ID de certificación requerido');
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

            $db->closeConect();

            return [
                'output' => [
                    'valid' => true,
                    'response' => $certificacion
                ]
            ];

        } catch (Exception $e) {
            $db->closeConect();
            return Util::error_general('Error al obtener detalle de certificación: ' . $e->getMessage());
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
