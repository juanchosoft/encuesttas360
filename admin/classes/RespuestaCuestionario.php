<?php

/**
 * Clase RespuestaCuestionario
 * Gestiona las respuestas de los cuestionarios (Ficha Técnica de Encuesta)
 */
class RespuestaCuestionario
{
    private $db;
    
    public function __construct($dbConnection = null) {
        if ($dbConnection) {
            $this->db = $dbConnection;
        } else {
            $this->db = new DbConection();
        }
    }
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
                    AND (i.es_prueba = 0 OR i.es_prueba IS NULL)
                )
                ORDER BY v.nombre_completo ASC";

            $stmt = $pdo->prepare($q);
            $stmt->execute([':ficha_tecnica_id' => $fichaTecnicaId]);
            $votantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $db->closeConect();

            return array('output' => array('valid' => true, 'response' => $votantes));

        } catch (Exception $e) {
            $db->closeConect();
            error_log('RespuestaCuestionario: ' . $e->getMessage());
            return Util::error_general('Error al obtener votantes disponibles.');
        }
    }

    /**
     * Guarda las respuestas de un cuestionario
     * @param array $rqst Datos de la solicitud
     * @return array Resultado de la operación
     */
    public static function save($rqst)
    {
        require_once __DIR__ . '/cuestionario/DefinicionCuestionario.php';
        require_once __DIR__ . '/cuestionario/MotorReglasCuestionario.php';
        require_once __DIR__ . '/cuestionario/EquivalenciasVotante.php';

        if (!isset($rqst['data']) || empty($rqst['data'])) {
            return Util::error_missing_data();
        }

        $data = is_array($rqst['data']) ? $rqst['data'] : json_decode($rqst['data'], true);
        if (!$data || !is_array($data)) {
            return Util::error_missing_data_description('Los datos no son válidos');
        }

        $votanteId = (int) SessionData::getUserId();
        if ($votanteId <= 0) {
            return Util::error_missing_data_description('Sesión inválida: no se detectó votante activo');
        }

        $fichaId = isset($data['ficha_tecnica_id']) ? (int)$data['ficha_tecnica_id'] : 0;
        if ($fichaId <= 0) {
            return Util::error_missing_data_description('Faltan datos obligatorios (ficha técnica)');
        }

        $respuestasRaw = [];
        if (isset($data['respuestas']) && is_array($data['respuestas'])) {
            $respuestasRaw = $data['respuestas'];
        } elseif (isset($data['preguntas']) && is_array($data['preguntas'])) {
            $respuestasRaw = $data['preguntas'];
        } else {
            return Util::error_missing_data_description('No se enviaron respuestas de preguntas');
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $clientUuid = isset($data['client_uuid']) ? trim((string)$data['client_uuid']) : '';
            if ($clientUuid !== '' && !preg_match('/^[a-fA-F0-9\-]{8,36}$/', $clientUuid)) {
                $clientUuid = '';
            }
            if ($clientUuid !== '') {
                $previo = self::respuestaIdempotente($pdo, $db, $clientUuid, $fichaId, $votanteId);
                if ($previo !== null) {
                    $db->closeConect();
                    return $previo;
                }
            }

            $stVer = $pdo->prepare("SELECT id FROM " . $db->getTable('tbl_cuestionario_intentos') . "
                WHERE tbl_ficha_tecnica_encuesta_id = :fid AND tbl_votante_id = :vid AND (es_prueba = 0 OR es_prueba IS NULL) LIMIT 1");
            $stVer->execute([':fid' => $fichaId, ':vid' => $votanteId]);
            if ($stVer->fetch()) {
                $db->closeConect();
                return Util::error_general('Este votante ya ha respondido este cuestionario');
            }

            $def = DefinicionCuestionario::cargar($pdo, $fichaId, $db->getDbName());
            if (!$def) {
                $db->closeConect();
                return Util::error_missing_data_description('No se encontró la definición del cuestionario.');
            }

            $versionCliente = isset($data['definicion_hash']) ? trim((string)$data['definicion_hash']) : '';
            if ($versionCliente !== (string)$def['version']) {
                $db->closeConect();
                return Util::error_missing_data_description(
                    'El cuestionario fue actualizado recientemente. Por favor recargue la página para cargar la versión vigente.'
                );
            }

            $codigoDepto = isset($data['codigo_departamento']) ? trim((string)$data['codigo_departamento']) : '';
            $codigoMpio = isset($data['codigo_municipio']) ? trim((string)$data['codigo_municipio']) : '';
            $zonaId = !empty($data['tbl_zona_muestreo_id']) ? (int)$data['tbl_zona_muestreo_id'] : null;
            $zonaTexto = '';

            if ($zonaId !== null) {
                $stZ = $pdo->prepare("SELECT nombre, cuota FROM " . $db->getTable('tbl_zonas_muestreo') . "
                    WHERE id = :zid AND tbl_ficha_tecnica_encuesta_id = :fid AND codigo_municipio = :mpio AND eliminado = 0 AND habilitado = 'si' LIMIT 1");
                $stZ->execute([':zid' => $zonaId, ':fid' => $fichaId, ':mpio' => $codigoMpio]);
                $zRow = $stZ->fetch(PDO::FETCH_ASSOC);
                if (!$zRow) {
                    $zonaId = null;
                } else {
                    $zonaTexto = (string)$zRow['nombre'];
                    if ((int)($def['ficha']['bloquear_cuota_completa'] ?? 0) === 1 && (int)$zRow['cuota'] > 0) {
                        $stC = $pdo->prepare("SELECT COUNT(*) FROM " . $db->getTable('tbl_cuestionario_intentos') . "
                            WHERE tbl_ficha_tecnica_encuesta_id = :fid AND tbl_zona_muestreo_id = :zid AND es_prueba = 0 AND estado IN ('completo','pendiente_audio')");
                        $stC->execute([':fid' => $fichaId, ':zid' => $zonaId]);
                        if ((int)$stC->fetchColumn() >= (int)$zRow['cuota']) {
                            $db->closeConect();
                            return Util::error_missing_data_description('La meta de esta zona ya se cumplió. Escoja otra zona.');
                        }
                    }
                }
            }

            $val = MotorReglasCuestionario::validar($def, $respuestasRaw, [
                'codigo_municipio' => $codigoMpio,
                'codigo_departamento' => $codigoDepto,
            ]);
            if (empty($val['valido'])) {
                $db->closeConect();
                $primerError = $val['errores'][0]['mensaje'] ?? 'Faltan respuestas obligatorias en el cuestionario.';
                return [
                    'output' => [
                        'valid' => false,
                        'message' => $primerError,
                        'errores' => $val['errores'],
                        'response' => ['content' => $primerError],
                    ],
                ];
            }

            $estadoIntento = $val['estado_intento'] === 'terminado_filtro' ? 'terminado_filtro' : 'completo';
            $validoPago = $estadoIntento === 'completo' ? 1 : 0;

            $pdo->beginTransaction();

            $stmtIntento = $pdo->prepare("INSERT INTO " . $db->getTable('tbl_cuestionario_intentos') . "
                (tbl_ficha_tecnica_encuesta_id, tbl_votante_id, estado, terminado_en_pregunta_id, terminado_opcion_id,
                 valido_para_pago, origen, codigo_departamento, codigo_municipio, tbl_zona_muestreo_id, zona_texto,
                 client_uuid, motor_version, definicion_hash, es_prueba, fecha_respuesta, dtcreate)
                VALUES (:fid, :vid, :estado, :term_preg, :term_opt, :pago, 'publico', :depto, :mpio, :zona, :zona_txt,
                        :uuid, :motor, :hash, 0, NOW(), NOW())");
            $stmtIntento->execute([
                ':fid' => $fichaId,
                ':vid' => $votanteId,
                ':estado' => $estadoIntento,
                ':term_preg' => $val['terminado_en_pregunta_id'] ?? null,
                ':term_opt' => $val['terminado_opcion_id'] ?? null,
                ':pago' => $validoPago,
                ':depto' => $codigoDepto !== '' ? $codigoDepto : null,
                ':mpio' => $codigoMpio !== '' ? $codigoMpio : null,
                ':zona' => $zonaId,
                ':zona_txt' => $zonaTexto !== '' ? $zonaTexto : null,
                ':uuid' => $clientUuid !== '' ? $clientUuid : null,
                ':motor' => $def['motor_version'] ?? MotorReglasCuestionario::MOTOR_VERSION,
                ':hash' => $def['version'] ?? null,
            ]);
            $intentoId = (int)$pdo->lastInsertId();

            $stmtRespuesta = $pdo->prepare("INSERT INTO " . $db->getTable('tbl_cuestionario_respuestas') . "
                (tbl_intento_id, tbl_pregunta_id, tbl_opcion_respuesta_id,
                 tbl_grilla_fila_id, tbl_grilla_columna_id, tbl_grilla_columna_opcion_id,
                 valor_numerico, respuesta_texto, texto_snapshot, dtcreate)
                VALUES (:intento_id, :pregunta_id, :opcion_id, :fila_id, :col_id, :col_opt_id, :val_num, :texto, :snapshot, NOW())");
            foreach ($val['filas_bd'] ?? [] as $fila) {
                $stmtRespuesta->execute([
                    ':intento_id' => $intentoId,
                    ':pregunta_id' => (int)$fila['tbl_pregunta_id'],
                    ':opcion_id' => $fila['tbl_opcion_respuesta_id'] !== null ? (int)$fila['tbl_opcion_respuesta_id'] : null,
                    ':fila_id' => $fila['tbl_grilla_fila_id'] !== null ? (int)$fila['tbl_grilla_fila_id'] : null,
                    ':col_id' => $fila['tbl_grilla_columna_id'] !== null ? (int)$fila['tbl_grilla_columna_id'] : null,
                    ':col_opt_id' => $fila['tbl_grilla_columna_opcion_id'] !== null ? (int)$fila['tbl_grilla_columna_opcion_id'] : null,
                    ':val_num' => $fila['valor_numerico'] !== null ? (float)$fila['valor_numerico'] : null,
                    ':texto' => $fila['respuesta_texto'],
                    ':snapshot' => $fila['texto_snapshot'] ?? null,
                ]);
            }

            $sets = ['codigo_departamento = :depto', 'codigo_municipio = :mpio'];
            $paramsUpd = [':vid' => $votanteId, ':depto' => $codigoDepto !== '' ? $codigoDepto : '00', ':mpio' => $codigoMpio !== '' ? $codigoMpio : '000'];
            if ($zonaTexto !== '') {
                $sets[] = 'comuna = :comuna';
                $paramsUpd[':comuna'] = $zonaTexto;
            }
            foreach (EquivalenciasVotante::extraer($def, $val['respuestas_normalizadas']) as $col => $valorVot) {
                if (in_array($col, ['genero', 'rango_edad', 'ideologia', 'nivel_ingresos', 'nivel_educacion', 'ocupacion'], true)) {
                    $sets[] = "{$col} = :{$col}";
                    $paramsUpd[":{$col}"] = $valorVot;
                }
            }
            $pdo->prepare("UPDATE " . $db->getTable('tbl_votantes') . " SET " . implode(', ', $sets) . ", dtupdate = NOW() WHERE id = :vid")
                ->execute($paramsUpd);

            $pdo->commit();
            $db->closeConect();

            return [
                'output' => [
                    'valid' => true,
                    'message' => 'Respuestas guardadas correctamente',
                    'intento_id' => $intentoId,
                    'estado_intento' => $estadoIntento,
                ],
            ];
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log('RespuestaCuestionario::save error: ' . $e->getMessage());
            if ($clientUuid !== '' && strpos($e->getMessage(), '1062') !== false && strpos($e->getMessage(), 'client_uuid') !== false) {
                $previo = self::respuestaIdempotente($pdo, $db, $clientUuid, $fichaId, $votanteId);
                if ($previo !== null) {
                    $db->closeConect();
                    return $previo;
                }
            }
            $db->closeConect();
            return Util::error_general('No fue posible guardar las respuestas. Intente de nuevo.');
        }
    }

    private static function respuestaIdempotente($pdo, $db, string $clientUuid, int $fichaId, int $votanteId): ?array
    {
        $st = $pdo->prepare("SELECT id, tbl_votante_id, tbl_ficha_tecnica_encuesta_id, estado FROM " . $db->getTable('tbl_cuestionario_intentos') . " WHERE client_uuid = :uuid LIMIT 1");
        $st->execute([':uuid' => $clientUuid]);
        $prev = $st->fetch(PDO::FETCH_ASSOC);
        if (!$prev) {
            return null;
        }
        if ((int)$prev['tbl_ficha_tecnica_encuesta_id'] !== $fichaId || (int)$prev['tbl_votante_id'] !== $votanteId) {
            return Util::error_general('Este envío ya fue registrado. Recargue la página e intente de nuevo.');
        }
        return [
            'output' => [
                'valid' => true,
                'message' => 'Respuestas guardadas correctamente',
                'intento_id' => (int)$prev['id'],
                'estado_intento' => $prev['estado'],
                'idempotent' => true,
            ],
        ];
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
                WHERE (i.es_prueba = 0 OR i.es_prueba IS NULL)";

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
            error_log('RespuestaCuestionario: ' . $e->getMessage());
            return Util::error_general('Error al obtener respuestas.');
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
                    r.tbl_grilla_fila_id,
                    r.tbl_grilla_columna_id,
                    r.tbl_grilla_columna_opcion_id,
                    r.respuesta_texto,
                    p.texto_pregunta,
                    p.tipo_pregunta,
                    o.texto_opcion,
                    gf.etiqueta AS fila_etiqueta,
                    gc.titulo AS columna_etiqueta,
                    gco.texto AS celda_opcion_etiqueta
                FROM " . $db->getTable('tbl_cuestionario_respuestas') . " r
                INNER JOIN " . $db->getTable('tbl_preguntas') . " p ON r.tbl_pregunta_id = p.id
                LEFT JOIN " . $db->getTable('tbl_opciones_respuesta') . " o ON r.tbl_opcion_respuesta_id = o.id
                LEFT JOIN " . $db->getTable('tbl_grilla_filas') . " gf ON r.tbl_grilla_fila_id = gf.id
                LEFT JOIN " . $db->getTable('tbl_grilla_columnas') . " gc ON r.tbl_grilla_columna_id = gc.id
                LEFT JOIN " . $db->getTable('tbl_grilla_columna_opciones') . " gco ON r.tbl_grilla_columna_opcion_id = gco.id
                WHERE r.tbl_intento_id = :intento_id
                ORDER BY COALESCE(p.orden, 999999) ASC, p.id ASC, COALESCE(gf.orden, 999999) ASC, COALESCE(gc.orden, 999999) ASC, r.id ASC";

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
            error_log('RespuestaCuestionario: ' . $e->getMessage());
            return Util::error_general('Error al obtener detalle de respuestas.');
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
                AND tbl_votante_id IS NOT NULL
                AND (es_prueba = 0 OR es_prueba IS NULL)";
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
                    v.nombre_completo,
                    v.email,
                    COUNT(DISTINCT r.tbl_pregunta_id) as preguntas_respondidas
                FROM " . $db->getTable('tbl_cuestionario_intentos') . " i
                INNER JOIN " . $db->getTable('tbl_votantes') . " v ON i.tbl_votante_id = v.id
                LEFT JOIN " . $db->getTable('tbl_cuestionario_respuestas') . " r ON i.id = r.tbl_intento_id
                WHERE i.tbl_ficha_tecnica_encuesta_id = :ficha_tecnica_id
                AND i.tbl_votante_id IS NOT NULL
                AND (i.es_prueba = 0 OR i.es_prueba IS NULL)
                GROUP BY i.id
                ORDER BY i.fecha_respuesta DESC
                LIMIT 10";
            $stmtUltimas = $pdo->prepare($qUltimas);
            $stmtUltimas->execute([':ficha_tecnica_id' => $fichaTecnicaId]);
            $ultimasRespuestas = $stmtUltimas->fetchAll(PDO::FETCH_ASSOC);

            $db->closeConect();

            return [
                'output' => [
                    'valid' => true,
                    'response' => [
                        'total_votantes' => $totalVotantes,
                        'total_respondieron' => $totalRespondieron,
                        'total_no_respondieron' => $totalNoRespondieron,
                        'porcentaje_respuestas' => $porcentajeRespuestas,
                        'ultimas_respuestas' => $ultimasRespuestas
                    ]
                ]
            ];

        } catch (Exception $e) {
            $db->closeConect();
            error_log('RespuestaCuestionario: ' . $e->getMessage());
            return Util::error_general('Error al obtener estadísticas.');
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
                    v.nombre_completo,
                    v.email,
                    v.username,
                    v.genero,
                    v.rango_edad,
                    v.ideologia,
                    i.fecha_respuesta,
                    i.id as intento_id,
                    COUNT(DISTINCT r.tbl_pregunta_id) as preguntas_respondidas
                FROM " . $db->getTable('tbl_votantes') . " v
                INNER JOIN " . $db->getTable('tbl_cuestionario_intentos') . " i ON v.id = i.tbl_votante_id
                LEFT JOIN " . $db->getTable('tbl_cuestionario_respuestas') . " r ON i.id = r.tbl_intento_id
                WHERE i.tbl_ficha_tecnica_encuesta_id = :ficha_tecnica_id
                AND (i.es_prueba = 0 OR i.es_prueba IS NULL)
                AND v.estado = 'activo'
                GROUP BY v.id, i.id
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
            error_log('RespuestaCuestionario: ' . $e->getMessage());
            return Util::error_general('Error al obtener votantes que respondieron.');
        }
    }

    /**
     * Verifica si un votante específico ya contestó una ficha técnica
     * @param array $rqst ['ficha_tecnica_id' => int, 'votante_id' => int]
     * @return array ['contestada' => bool]
     */
    public static function verificarSiYaContesto($rqst)
    {
        $fichaTecnicaId = isset($rqst['ficha_tecnica_id']) ? intval($rqst['ficha_tecnica_id']) : 0;
        $votanteId = isset($rqst['votante_id']) ? intval($rqst['votante_id']) : 0;

        if ($votanteId <= 0) {
            $votanteId = (int) SessionData::getUserId();
        }

        if ($fichaTecnicaId === 0 || $votanteId === 0) {
            return ['output' => ['valid' => false, 'contestada' => false]];
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $q = "SELECT id FROM " . $db->getTable('tbl_cuestionario_intentos') . "
                  WHERE tbl_ficha_tecnica_encuesta_id = :ficha_tecnica_id
                  AND tbl_votante_id = :votante_id
                  AND (es_prueba = 0 OR es_prueba IS NULL)
                  LIMIT 1";

            $stmt = $pdo->prepare($q);
            $stmt->execute([
                ':ficha_tecnica_id' => $fichaTecnicaId,
                ':votante_id' => $votanteId
            ]);

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            $contestada = !empty($resultado);

            $db->closeConect();

            return [
                'output' => [
                    'valid' => true,
                    'contestada' => $contestada
                ]
            ];

        } catch (Exception $e) {
            $db->closeConect();
            error_log('RespuestaCuestionario: ' . $e->getMessage());
            return [
                'output' => [
                    'valid' => false,
                    'contestada' => false,
                    'error' => 'No fue posible verificar la participación.'
                ]
            ];
        }
    }

    /**
     * Obtiene las respuestas de un votante específico para una encuesta
     * @param array $rqst ['ficha_tecnica_id' => int, 'votante_id' => int]
     * @return array Resultado con las respuestas del votante
     */
    public static function getRespuestasVotante($rqst)
    {
        $fichaTecnicaId = isset($rqst['ficha_tecnica_id']) ? intval($rqst['ficha_tecnica_id']) : 0;
        $votanteId = isset($rqst['votante_id']) ? intval($rqst['votante_id']) : 0;

        if ($fichaTecnicaId === 0 || $votanteId === 0) {
            return Util::error_missing_data_description('Faltan parámetros requeridos');
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            // Obtener el intento del votante
            $qIntento = "SELECT id, fecha_respuesta
                        FROM " . $db->getTable('tbl_cuestionario_intentos') . "
                        WHERE tbl_ficha_tecnica_encuesta_id = :ficha_tecnica_id
                        AND tbl_votante_id = :votante_id
                        AND (es_prueba = 0 OR es_prueba IS NULL)
                        LIMIT 1";
            // print($qIntento);
            $stmtIntento = $pdo->prepare($qIntento);
            $stmtIntento->execute([
                ':ficha_tecnica_id' => $fichaTecnicaId,
                ':votante_id' => $votanteId
            ]);

            $intento = $stmtIntento->fetch(PDO::FETCH_ASSOC);

            if (!$intento) {
                $db->closeConect();
                return Util::error_general('No se encontraron respuestas para este votante');
            }

            // Obtener las respuestas con sus preguntas y opciones
            $qRespuestas = "SELECT
                                p.id as pregunta_id,
                                p.texto_pregunta,
                                p.tipo_pregunta,
                                p.orden,
                                GROUP_CONCAT(o.texto_opcion SEPARATOR '|') as opciones_seleccionadas,
                                MAX(r.respuesta_texto) as respuesta_texto
                            FROM " . $db->getTable('tbl_cuestionario_respuestas') . " r
                            INNER JOIN " . $db->getTable('tbl_preguntas') . " p ON r.tbl_pregunta_id = p.id
                            LEFT JOIN " . $db->getTable('tbl_opciones_respuesta') . " o ON r.tbl_opcion_respuesta_id = o.id
                            WHERE r.tbl_intento_id = :intento_id
                              AND (p.eliminado = 0 OR p.eliminado IS NULL)
                            GROUP BY p.id, p.texto_pregunta, p.tipo_pregunta, p.orden
                            ORDER BY COALESCE(p.orden, 999999) ASC, p.id ASC";

            $stmtRespuestas = $pdo->prepare($qRespuestas);
            $stmtRespuestas->execute([':intento_id' => $intento['id']]);
            $respuestas = $stmtRespuestas->fetchAll(PDO::FETCH_ASSOC);

            // Formatear respuestas
            $respuestasFormateadas = [];
            foreach ($respuestas as $respuesta) {
                $respuestasFormateadas[] = [
                    'pregunta_id' => $respuesta['pregunta_id'],
                    'texto_pregunta' => $respuesta['texto_pregunta'],
                    'tipo_pregunta' => $respuesta['tipo_pregunta'],
                    'orden' => $respuesta['orden'],
                    'opciones_seleccionadas' => !empty($respuesta['opciones_seleccionadas'])
                        ? explode('|', $respuesta['opciones_seleccionadas'])
                        : [],
                    'respuesta_texto' => $respuesta['respuesta_texto']
                ];
            }

            $db->closeConect();

            return [
                'output' => [
                    'valid' => true,
                    'response' => [
                        'fecha_respuesta' => $intento['fecha_respuesta'],
                        'respuestas' => $respuestasFormateadas
                    ]
                ]
            ];

        } catch (Exception $e) {
            $db->closeConect();
            error_log('RespuestaCuestionario: ' . $e->getMessage());
            return Util::error_general('Error al obtener respuestas.');
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
                    v.nombre_completo,
                    v.email,
                    v.username,
                    v.genero,
                    v.rango_edad,
                    v.ideologia
                FROM " . $db->getTable('tbl_votantes') . " v
                WHERE v.estado = 'activo'
                AND v.id NOT IN (
                    SELECT DISTINCT tbl_votante_id
                    FROM " . $db->getTable('tbl_cuestionario_intentos') . "
                    WHERE tbl_ficha_tecnica_encuesta_id = :ficha_tecnica_id
                    AND tbl_votante_id IS NOT NULL
                    AND (es_prueba = 0 OR es_prueba IS NULL)
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
            error_log('RespuestaCuestionario: ' . $e->getMessage());
            return Util::error_general('Error al obtener votantes que no respondieron.');
        }
    }

    /**
     * Obtiene las preguntas de opción múltiple del cuestionario activo
     * para mostrar en el selector del index.php
     */

    /** Expresión SQL: tipo_registro (alineada al listado del dashboard admin) */
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

    /**
     * Filtros del listado del dashboard (tipo / encuestador / fechas).
     */
    private static function parseFiltrosListado($rqst)
    {
        $rqst = is_array($rqst) ? $rqst : [];
        return [
            'tipo' => isset($rqst['filtro_tipo']) ? trim((string)$rqst['filtro_tipo']) : '',
            'encuestador' => isset($rqst['filtro_encuestador']) ? trim((string)$rqst['filtro_encuestador']) : '',
            'fecha_desde' => isset($rqst['fecha_desde']) ? trim((string)$rqst['fecha_desde']) : '',
            'fecha_hasta' => isset($rqst['fecha_hasta']) ? trim((string)$rqst['fecha_hasta']) : '',
        ];
    }

    private static function hasFiltrosListado($filtros)
    {
        return ($filtros['tipo'] ?? '') !== ''
            || ($filtros['encuestador'] ?? '') !== ''
            || ($filtros['fecha_desde'] ?? '') !== ''
            || ($filtros['fecha_hasta'] ?? '') !== '';
    }

    /**
     * Fragmento SQL para filtrar intentos/votantes como el listado.
     * Requiere aliases i (intentos) y v (votantes). Une usuarios si hace falta.
     * @return array{join_usuario:string,and_sql:string,params:array}
     */
    private static function buildFiltrosListadoSql($filtros, $db)
    {
        $ands = [];
        $params = [];
        $joinUsuario = '';

        if (($filtros['fecha_desde'] ?? '') !== '') {
            $ands[] = 'DATE(i.fecha_respuesta) >= :filtro_fecha_desde';
            $params[':filtro_fecha_desde'] = $filtros['fecha_desde'];
        }
        if (($filtros['fecha_hasta'] ?? '') !== '') {
            $ands[] = 'DATE(i.fecha_respuesta) <= :filtro_fecha_hasta';
            $params[':filtro_fecha_hasta'] = $filtros['fecha_hasta'];
        }
        if (($filtros['tipo'] ?? '') !== '' || ($filtros['encuestador'] ?? '') !== '') {
            $joinUsuario = ' LEFT JOIN ' . $db->getTable('tbl_usuarios') . ' u ON v.tbl_usuario_id = u.id ';
        }
        if (($filtros['tipo'] ?? '') !== '') {
            $ands[] = '(' . self::sqlTipoRegistro() . ') = :filtro_tipo';
            $params[':filtro_tipo'] = $filtros['tipo'];
        }
        if (($filtros['encuestador'] ?? '') !== '') {
            $ands[] = '(' . self::sqlEncuestadorNombre() . ') = :filtro_enc';
            $params[':filtro_enc'] = $filtros['encuestador'];
        }

        return [
            'join_usuario' => $joinUsuario,
            'and_sql' => count($ands) ? (' AND ' . implode(' AND ', $ands)) : '',
            'params' => $params,
        ];
    }

    /**
     * Resuelve la ficha del mapa: prioriza ficha_tecnica_id del request (selector dashboard).
     */
    private static function resolveFichaIndex($pdo, $db, $rqst = [])
    {
        $rqst = is_array($rqst) ? $rqst : [];
        $fichaId = 0;
        if (!empty($rqst['ficha_tecnica_id'])) {
            $fichaId = intval($rqst['ficha_tecnica_id']);
        } elseif (!empty($rqst['id'])) {
            $fichaId = intval($rqst['id']);
        }

        if ($fichaId > 0) {
            $q = "SELECT id, realizada_por_o_encomendada_por as nombre, pregunta_destacada_id
                  FROM " . $db->getTable('tbl_ficha_tecnica_encuestas') . "
                  WHERE id = :id
                  LIMIT 1";
            $stmt = $pdo->prepare($q);
            $stmt->execute([':id' => $fichaId]);
            $ficha = $stmt->fetch(PDO::FETCH_ASSOC);
            return $ficha ?: null;
        }

        $qFicha = "SELECT id, realizada_por_o_encomendada_por as nombre, pregunta_destacada_id
                   FROM " . $db->getTable('tbl_ficha_tecnica_encuestas') . "
                   WHERE habilitado = 'si'
                     AND (eliminado = 'no' OR eliminado IS NULL)
                     AND EXISTS (
                       SELECT 1 FROM " . $db->getTable('tbl_preguntas') . " p
                       WHERE p.tbl_ficha_tecnica_encuesta_id = tbl_ficha_tecnica_encuestas.id
                         AND p.habilitado = 'si'
                         AND (p.eliminado = 0 OR p.eliminado IS NULL)
                     )
                   ORDER BY dtcreate DESC
                   LIMIT 1";
        $stmt = $pdo->prepare($qFicha);
        $stmt->execute();
        $ficha = $stmt->fetch(PDO::FETCH_ASSOC);
        return $ficha ?: null;
    }

    private static function resolvePreguntaMapaIndex($pdo, $db, array $ficha, $preguntaIdParam = 0)
    {
        $fichaId = (int)$ficha['id'];
        $preguntaIdParam = (int)$preguntaIdParam;
        $destacadaId = !empty($ficha['pregunta_destacada_id']) ? (int)$ficha['pregunta_destacada_id'] : 0;

        if ($preguntaIdParam > 0) {
            $qPregunta = "SELECT p.id, p.texto_pregunta
                          FROM " . $db->getTable('tbl_preguntas') . " p
                          WHERE p.id = :pregunta_id
                            AND p.tbl_ficha_tecnica_encuesta_id = :ficha_id
                            AND (p.eliminado = 0 OR p.eliminado IS NULL)";
            $stmt = $pdo->prepare($qPregunta);
            $stmt->execute([":pregunta_id" => $preguntaIdParam, ":ficha_id" => $fichaId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                return $row;
            }
        }

        if ($destacadaId > 0) {
            $qDest = "SELECT p.id, p.texto_pregunta
                      FROM " . $db->getTable('tbl_preguntas') . " p
                      WHERE p.id = :pregunta_id
                        AND p.tbl_ficha_tecnica_encuesta_id = :ficha_id
                        AND (p.eliminado = 0 OR p.eliminado IS NULL)
                        AND (p.habilitado = 'si' OR p.habilitado IS NULL)";
            $stmt = $pdo->prepare($qDest);
            $stmt->execute([":pregunta_id" => $destacadaId, ":ficha_id" => $fichaId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                return $row;
            }
        }

        $qPregunta = "SELECT p.id, p.texto_pregunta
                      FROM " . $db->getTable('tbl_preguntas') . " p
                      WHERE p.tbl_ficha_tecnica_encuesta_id = :ficha_id
                        AND (p.eliminado = 0 OR p.eliminado IS NULL)
                        AND (p.habilitado = 'si' OR p.habilitado IS NULL)
                        AND (p.es_filtro = 0 OR p.es_filtro IS NULL)
                        AND p.tipo_pregunta IN ('radio', 'checkbox', 'Seleccion_Multiple_unica_respuesta', 'Seleccion_Multiple_multiple_respuesta', 'Dicotomica', 'Preguntas_Cardinales', 'Preguntas_Ordinales')
                      ORDER BY COALESCE(p.orden, 999999) ASC, p.id ASC
                      LIMIT 1";
        $stmt = $pdo->prepare($qPregunta);
        $stmt->execute([":ficha_id" => $fichaId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public static function obtenerPreguntasCuestionarioActivo($rqst)
    {
        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            // Obtener la ficha técnica habilitada
            $ficha = self::resolveFichaIndex($pdo, $db, $rqst);

            if (!$ficha) {
                $db->closeConect();
                return ["success" => false, "message" => "No hay encuestas habilitadas"];
            }

            // Obtener todas las preguntas de opción múltiple (excluyendo eliminadas y filtros)
            $qPreguntas = "SELECT p.id, p.texto_pregunta, p.orden, p.capitulo, p.enunciado_pregunta, p.numeral, p.texto_adicional
                          FROM " . $db->getTable('tbl_preguntas') . " p
                          WHERE p.tbl_ficha_tecnica_encuesta_id = :ficha_id
                          AND (p.eliminado = 0 OR p.eliminado IS NULL)
                          AND (p.habilitado = 'si' OR p.habilitado IS NULL)
                          AND (p.es_filtro = 0 OR p.es_filtro IS NULL)
                          AND p.tipo_pregunta IN ('radio', 'checkbox', 'Seleccion_Multiple_unica_respuesta', 'Seleccion_Multiple_multiple_respuesta', 'Dicotomica', 'Preguntas_Cardinales', 'Preguntas_Ordinales')
                          ORDER BY COALESCE(p.orden, 999999) ASC, p.id ASC";
            $stmt = $pdo->prepare($qPreguntas);
            $stmt->execute([":ficha_id" => $ficha['id']]);
            $preguntas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $db->closeConect();

            return [
                "success" => true,
                "ficha" => [
                    "id" => $ficha['id'],
                    "nombre" => $ficha['nombre'],
                    "pregunta_destacada_id" => !empty($ficha['pregunta_destacada_id']) ? (int)$ficha['pregunta_destacada_id'] : null
                ],
                "preguntas" => $preguntas
            ];

        } catch (Exception $e) {
            $db->closeConect();
            error_log('RespuestaCuestionario: ' . $e->getMessage());
            return ["success" => false, "message" => "Ocurrió un error al procesar la solicitud. Intente de nuevo."];
        }
    }

    /**
     * Obtiene resultados generales de encuestas para mostrar en el index (gráfico general)
     * Similar a obtenerSondeoGeneral pero para encuestas
     * Acepta pregunta_id para filtrar por pregunta específica
     */
    public static function obtenerEncuestaGeneralIndex($rqst)
    {
        $preguntaId = isset($rqst['pregunta_id']) ? intval($rqst['pregunta_id']) : 0;
        $depClick = $rqst['departamento_click'] ?? null;
        $muniClick = $rqst['municipio_click'] ?? null;
        if ($depClick !== null && $depClick !== '') {
            $depClick = Util::normalizeCodigoDepartamento($depClick);
        }
        if ($muniClick !== null && $muniClick !== '') {
            $muniClick = Util::normalizeCodigoMunicipio($muniClick);
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $ficha = self::resolveFichaIndex($pdo, $db, $rqst);

            if (!$ficha) {
                return ["success" => false, "message" => "No hay encuestas habilitadas"];
            }

            $pregunta = self::resolvePreguntaMapaIndex($pdo, $db, $ficha, $preguntaId);

            if (!$pregunta) {
                return ["success" => false, "message" => "No hay preguntas de opción múltiple"];
            }

            $filtros = self::parseFiltrosListado($rqst);
            $filtroSql = self::buildFiltrosListadoSql($filtros, $db);
            $params = [":pregunta_id" => $pregunta['id']];
            $params = array_merge($params, $filtroSql['params']);

            $respJoin = " LEFT JOIN " . $db->getTable('tbl_cuestionario_respuestas') . " r
                          ON r.tbl_opcion_respuesta_id = o.id
                          AND r.tbl_grilla_fila_id IS NULL
                          AND r.tbl_grilla_columna_id IS NULL
                       LEFT JOIN " . $db->getTable('tbl_cuestionario_intentos') . " i
                          ON r.tbl_intento_id = i.id
                          AND (i.es_prueba = 0 OR i.es_prueba IS NULL)
                       LEFT JOIN " . $db->getTable('tbl_votantes') . " v ON i.tbl_votante_id = v.id "
                       . $filtroSql['join_usuario'];

            $extraAnd = "";
            if (!empty($muniClick)) {
                $extraAnd .= " AND LPAD(CAST(v.codigo_municipio AS UNSIGNED), 5, '0') = :muni ";
                $params[":muni"] = $muniClick;
            } elseif (!empty($depClick)) {
                $extraAnd .= " AND LPAD(CAST(v.codigo_departamento AS UNSIGNED), 2, '0') = :dep ";
                $params[":dep"] = $depClick;
            }

            $caseCond = "r.id IS NOT NULL AND i.id IS NOT NULL";
            if (self::hasFiltrosListado($filtros) || !empty($muniClick) || !empty($depClick)) {
                if (self::hasFiltrosListado($filtros)) {
                    $caseCond .= $filtroSql['and_sql'];
                }
                if ($extraAnd !== '') {
                    $caseCond .= $extraAnd;
                }
            }

            $qVotos = "SELECT
                          o.id AS candidato_id,
                          o.texto_opcion AS nombre_completo,
                          '' AS foto,
                          'img/option_default.png' AS foto_url,
                          COUNT(CASE WHEN ($caseCond) THEN r.id END) AS total
                       FROM " . $db->getTable('tbl_opciones_respuesta') . " o
                       $respJoin
                       WHERE o.tbl_pregunta_id = :pregunta_id
                         AND (o.eliminado = 0 OR o.eliminado IS NULL)
                       GROUP BY o.id, o.texto_opcion
                       ORDER BY total DESC";

            $stmt = $pdo->prepare($qVotos);
            $stmt->execute($params);
            $votos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $db->closeConect();

            return [
                "success" => true,
                "sondeo" => [
                    "id" => $ficha['id'],
                    "sondeo" => $ficha['nombre'],
                    "descripcion_sondeo" => $pregunta['texto_pregunta'],
                    "pregunta_id" => $pregunta['id']
                ],
                "votos" => $votos
            ];

        } catch (Exception $e) {
            $db->closeConect();
            error_log('RespuestaCuestionario: ' . $e->getMessage());
            return ["success" => false, "message" => "Ocurrió un error al procesar la solicitud. Intente de nuevo."];
        }
    }

    /**
     * Obtiene resultados de encuesta filtrados por departamento (para el mapa del index)
     * Acepta pregunta_id para filtrar por pregunta específica
     */
    public static function obtenerEncuestaMapaIndex($rqst)
    {
        $depClick = $rqst['departamento_click'] ?? null;
        $muniClick = $rqst['municipio_click'] ?? null;
        $preguntaId = isset($rqst['pregunta_id']) ? intval($rqst['pregunta_id']) : 0;
        if ($depClick !== null && $depClick !== '') {
            $depClick = Util::normalizeCodigoDepartamento($depClick);
        }
        if ($muniClick !== null && $muniClick !== '') {
            $muniClick = Util::normalizeCodigoMunicipio($muniClick);
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $ficha = self::resolveFichaIndex($pdo, $db, $rqst);

            if (!$ficha) {
                return ["success" => false, "message" => "No hay encuestas habilitadas"];
            }

            $pregunta = self::resolvePreguntaMapaIndex($pdo, $db, $ficha, $preguntaId);

            if (!$pregunta) {
                return ["success" => false, "message" => "No hay preguntas de opción múltiple"];
            }

            $filtros = self::parseFiltrosListado($rqst);
            $filtroSql = self::buildFiltrosListadoSql($filtros, $db);
            $params = [":pregunta_id" => $pregunta['id']];
            $params = array_merge($params, $filtroSql['params']);

            $caseCond = "r.id IS NOT NULL AND i.id IS NOT NULL";
            if (!empty($muniClick)) {
                $caseCond .= " AND LPAD(CAST(v.codigo_municipio AS UNSIGNED), 5, '0') = :muni ";
                $params[":muni"] = $muniClick;
            } elseif (!empty($depClick)) {
                $caseCond .= " AND LPAD(CAST(v.codigo_departamento AS UNSIGNED), 2, '0') = :dep ";
                $params[":dep"] = $depClick;
            }
            if (self::hasFiltrosListado($filtros)) {
                $caseCond .= $filtroSql['and_sql'];
            }

            $qVotos = "SELECT
                          o.id AS candidato_id,
                          o.texto_opcion AS nombre_completo,
                          '' AS foto,
                          'img/option_default.png' AS foto_url,
                          COUNT(CASE WHEN ($caseCond) THEN r.id END) AS total
                       FROM " . $db->getTable('tbl_opciones_respuesta') . " o
                       LEFT JOIN " . $db->getTable('tbl_cuestionario_respuestas') . " r
                          ON r.tbl_opcion_respuesta_id = o.id
                          AND r.tbl_grilla_fila_id IS NULL
                          AND r.tbl_grilla_columna_id IS NULL
                       LEFT JOIN " . $db->getTable('tbl_cuestionario_intentos') . " i
                          ON r.tbl_intento_id = i.id
                          AND (i.es_prueba = 0 OR i.es_prueba IS NULL)
                       LEFT JOIN " . $db->getTable('tbl_votantes') . " v
                          ON i.tbl_votante_id = v.id
                       " . $filtroSql['join_usuario'] . "
                       WHERE o.tbl_pregunta_id = :pregunta_id
                         AND (o.eliminado = 0 OR o.eliminado IS NULL)
                       GROUP BY o.id, o.texto_opcion
                       ORDER BY total DESC";

            $stmt = $pdo->prepare($qVotos);
            $stmt->execute($params);
            $votos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $db->closeConect();

            return [
                "success" => true,
                "sondeo" => [
                    "id" => $ficha['id'],
                    "sondeo" => $ficha['nombre'],
                    "descripcion_sondeo" => $pregunta['texto_pregunta'],
                    "pregunta_id" => $pregunta['id']
                ],
                "votos" => $votos
            ];

        } catch (Exception $e) {
            $db->closeConect();
            error_log('RespuestaCuestionario: ' . $e->getMessage());
            return ["success" => false, "message" => "Ocurrió un error al procesar la solicitud. Intente de nuevo."];
        }
    }

    /**
     * Obtiene el ganador por cada departamento para el cuestionario activo (para colorear el mapa)
     * Similar a Sondeo::ganadorPorTodosLosDepartamentos()
     * Acepta pregunta_id opcional para filtrar por pregunta específica
     * @param int $preguntaIdParam ID de la pregunta (opcional, si es 0 usa la primera)
     * @return array ['codigo_departamento' => ['ganador' => opcion_id, 'empate' => bool], ...]
     */
    public static function ganadorPorTodosLosDepartamentosCuestionario($preguntaIdParam = 0, $rqst = [])
    {
        $db = new DbConection();
        $pdo = $db->openConect();
        $rqst = is_array($rqst) ? $rqst : [];

        try {
            $ficha = self::resolveFichaIndex($pdo, $db, $rqst);

            if (!$ficha) {
                $db->closeConect();
                return [];
            }

            $pregunta = self::resolvePreguntaMapaIndex($pdo, $db, $ficha, $preguntaIdParam);

            if (!$pregunta) {
                $db->closeConect();
                return [];
            }

            $filtros = self::parseFiltrosListado($rqst);
            $filtroSql = self::buildFiltrosListadoSql($filtros, $db);
            $paramsVotos = [":pregunta_id" => $pregunta['id']];
            $paramsVotos = array_merge($paramsVotos, $filtroSql['params']);

            // Obtener votos por opción agrupados por departamento
            $qVotos = "SELECT
                          v.codigo_departamento,
                          r.tbl_opcion_respuesta_id as opcion_id,
                          COUNT(r.id) as total
                       FROM " . $db->getTable('tbl_cuestionario_respuestas') . " r
                       INNER JOIN " . $db->getTable('tbl_cuestionario_intentos') . " i
                          ON r.tbl_intento_id = i.id
                          AND (i.es_prueba = 0 OR i.es_prueba IS NULL)
                       INNER JOIN " . $db->getTable('tbl_votantes') . " v ON i.tbl_votante_id = v.id
                       " . $filtroSql['join_usuario'] . "
                       INNER JOIN " . $db->getTable('tbl_opciones_respuesta') . " o
                          ON r.tbl_opcion_respuesta_id = o.id
                          AND (o.eliminado = 0 OR o.eliminado IS NULL)
                       WHERE o.tbl_pregunta_id = :pregunta_id
                       AND r.tbl_grilla_fila_id IS NULL
                       AND r.tbl_grilla_columna_id IS NULL
                       AND v.codigo_departamento IS NOT NULL
                       AND v.codigo_departamento != ''
                       " . $filtroSql['and_sql'] . "
                       GROUP BY v.codigo_departamento, r.tbl_opcion_respuesta_id
                       ORDER BY v.codigo_departamento, total DESC";

            $stmt = $pdo->prepare($qVotos);
            $stmt->execute($paramsVotos);
            $votos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $db->closeConect();

            // Procesar resultados por departamento
            $resultado = [];
            $votosPorDepartamento = [];

            // Agrupar votos por departamento
            foreach ($votos as $voto) {
                $dep = Util::normalizeCodigoDepartamento($voto['codigo_departamento']);
                if ($dep === '') {
                    continue;
                }
                if (!isset($votosPorDepartamento[$dep])) {
                    $votosPorDepartamento[$dep] = [];
                }
                $votosPorDepartamento[$dep][] = [
                    'opcion_id' => $voto['opcion_id'],
                    'total' => (int)$voto['total']
                ];
            }

            // Determinar ganador o empate por departamento
            foreach ($votosPorDepartamento as $dep => $votosDepto) {
                if (empty($votosDepto)) {
                    continue;
                }

                // Ordenar por total descendente
                usort($votosDepto, function($a, $b) {
                    return $b['total'] - $a['total'];
                });

                $maxVotos = $votosDepto[0]['total'];
                $ganador = $votosDepto[0]['opcion_id'];

                // Verificar si hay empate
                $empate = false;
                if (count($votosDepto) > 1 && $votosDepto[1]['total'] == $maxVotos) {
                    $empate = true;
                }

                $resultado[$dep] = [
                    'ganador' => $ganador,
                    'empate' => $empate
                ];
            }

            return $resultado;

        } catch (Exception $e) {
            $db->closeConect();
            return [];
        }
    }

    /**
     * Totales de respuestas por departamento (todas las regiones a la vez).
     * Para el panel "Detalle territorial" sin requerir clic en el mapa.
     */
    public static function obtenerTotalesPorDepartamentoIndex($rqst)
    {
        $preguntaId = isset($rqst['pregunta_id']) ? intval($rqst['pregunta_id']) : 0;
        $paletaColores = [
            "#1f77b4", "#ff7f0e", "#2ca02c", "#d62728", "#9467bd",
            "#8c564b", "#e377c2", "#7f7f7f", "#bcbd22", "#17becf"
        ];

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $ficha = self::resolveFichaIndex($pdo, $db, $rqst);

            if (!$ficha) {
                $db->closeConect();
                return ["success" => false, "message" => "No hay encuestas habilitadas", "departamentos" => []];
            }

            $pregunta = self::resolvePreguntaMapaIndex($pdo, $db, $ficha, $preguntaId);

            if (!$pregunta) {
                $db->closeConect();
                return ["success" => false, "message" => "No hay preguntas de opción múltiple", "departamentos" => []];
            }

            $qOpciones = "SELECT id, texto_opcion FROM " . $db->getTable('tbl_opciones_respuesta') . "
                          WHERE tbl_pregunta_id = :pregunta_id
                            AND (eliminado = 0 OR eliminado IS NULL)
                          ORDER BY COALESCE(orden, 999999) ASC, id ASC";
            $stmt = $pdo->prepare($qOpciones);
            $stmt->execute([":pregunta_id" => $pregunta['id']]);
            $opciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $coloresOpciones = [];
            $nombresOpciones = [];
            foreach ($opciones as $index => $opc) {
                $coloresOpciones[(int)$opc['id']] = $paletaColores[$index % count($paletaColores)];
                $nombresOpciones[(int)$opc['id']] = $opc['texto_opcion'];
            }

            $filtros = self::parseFiltrosListado($rqst);
            $filtroSql = self::buildFiltrosListadoSql($filtros, $db);
            $paramsTotales = [":pregunta_id" => $pregunta['id']];
            $paramsTotales = array_merge($paramsTotales, $filtroSql['params']);

            $qTotales = "SELECT
                            v.codigo_departamento AS codigo,
                            COALESCE(MAX(d.departamento), CONCAT('Cód. ', v.codigo_departamento)) AS nombre,
                            COUNT(r.id) AS total
                         FROM " . $db->getTable('tbl_cuestionario_respuestas') . " r
                         INNER JOIN " . $db->getTable('tbl_cuestionario_intentos') . " i
                            ON r.tbl_intento_id = i.id
                            AND (i.es_prueba = 0 OR i.es_prueba IS NULL)
                         INNER JOIN " . $db->getTable('tbl_votantes') . " v ON i.tbl_votante_id = v.id
                         " . $filtroSql['join_usuario'] . "
                         INNER JOIN " . $db->getTable('tbl_opciones_respuesta') . " o
                            ON r.tbl_opcion_respuesta_id = o.id
                            AND (o.eliminado = 0 OR o.eliminado IS NULL)
                         LEFT JOIN " . $db->getTable('tbl_departamentos') . " d
                            ON d.codigo_departamento = v.codigo_departamento
                         WHERE o.tbl_pregunta_id = :pregunta_id
                           AND r.tbl_grilla_fila_id IS NULL
                           AND r.tbl_grilla_columna_id IS NULL
                           AND v.codigo_departamento IS NOT NULL
                           AND v.codigo_departamento != ''
                           " . $filtroSql['and_sql'] . "
                         GROUP BY v.codigo_departamento
                         ORDER BY total DESC, nombre ASC";
            $stmt = $pdo->prepare($qTotales);
            $stmt->execute($paramsTotales);
            $totales = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $db->closeConect();

            $ganadores = self::ganadorPorTodosLosDepartamentosCuestionario((int)$pregunta['id'], $rqst);

            $departamentos = [];
            foreach ($totales as $row) {
                $codigo = (string)$row['codigo'];
                $ganadorMeta = $ganadores[$codigo] ?? null;
                $ganadorId = ($ganadorMeta && empty($ganadorMeta['empate'])) ? (int)$ganadorMeta['ganador'] : null;
                $empate = !empty($ganadorMeta['empate']);

                $departamentos[] = [
                    "codigo" => $codigo,
                    "nombre" => $row['nombre'],
                    "total" => (int)$row['total'],
                    "ganador_id" => $ganadorId,
                    "ganador_nombre" => ($ganadorId && isset($nombresOpciones[$ganadorId]))
                        ? $nombresOpciones[$ganadorId]
                        : ($empate ? "Empate" : null),
                    "empate" => $empate,
                    "color" => ($ganadorId && isset($coloresOpciones[$ganadorId]))
                        ? $coloresOpciones[$ganadorId]
                        : ($empate ? "#94a3b8" : "#20427F"),
                ];
            }

            return [
                "success" => true,
                "pregunta" => [
                    "id" => (int)$pregunta['id'],
                    "texto" => $pregunta['texto_pregunta'],
                ],
                "departamentos" => $departamentos,
            ];
        } catch (Exception $e) {
            $db->closeConect();
            error_log('RespuestaCuestionario: ' . $e->getMessage());
            return ["success" => false, "message" => "Ocurrió un error al procesar la solicitud. Intente de nuevo.", "departamentos" => []];
        }
    }

    /**
     * Totales por municipio dentro de un departamento (Fase C — cuestionario).
     */
    public static function obtenerTotalesPorMunicipioIndex($rqst)
    {
        $dep = Util::normalizeCodigoDepartamento($rqst['departamento_click'] ?? $rqst['codigo_departamento'] ?? '');
        $preguntaId = isset($rqst['pregunta_id']) ? intval($rqst['pregunta_id']) : 0;
        $paletaColores = [
            "#1f77b4", "#ff7f0e", "#2ca02c", "#d62728", "#9467bd",
            "#8c564b", "#e377c2", "#7f7f7f", "#bcbd22", "#17becf"
        ];

        if (!Util::isMapaMunicipalHabilitado($dep)) {
            return [
                "success" => false,
                "message" => Util::mensajeMapaMunicipalNoDisponible($dep),
                "municipios" => [],
            ];
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $ficha = self::resolveFichaIndex($pdo, $db, $rqst);
            if (!$ficha) {
                $db->closeConect();
                return ["success" => false, "message" => "No hay encuestas habilitadas", "municipios" => []];
            }

            $pregunta = self::resolvePreguntaMapaIndex($pdo, $db, $ficha, $preguntaId);
            if (!$pregunta) {
                $db->closeConect();
                return ["success" => false, "message" => "Sin pregunta", "municipios" => []];
            }

            $qOpciones = "SELECT id, texto_opcion FROM " . $db->getTable('tbl_opciones_respuesta') . "
                          WHERE tbl_pregunta_id = :pregunta_id
                            AND (eliminado = 0 OR eliminado IS NULL)
                          ORDER BY COALESCE(orden, 999999) ASC, id ASC";
            $stmt = $pdo->prepare($qOpciones);
            $stmt->execute([":pregunta_id" => $pregunta['id']]);
            $opciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $coloresOpciones = [];
            $nombresOpciones = [];
            foreach ($opciones as $index => $opc) {
                $coloresOpciones[(int)$opc['id']] = $paletaColores[$index % count($paletaColores)];
                $nombresOpciones[(int)$opc['id']] = $opc['texto_opcion'];
            }

            $filtros = self::parseFiltrosListado($rqst);
            $filtroSql = self::buildFiltrosListadoSql($filtros, $db);
            $paramsMuni = [":pregunta_id" => $pregunta['id'], ":dep" => $dep];
            $paramsMuni = array_merge($paramsMuni, $filtroSql['params']);

            $qTotales = "SELECT
                            LPAD(CAST(v.codigo_municipio AS UNSIGNED), 5, '0') AS codigo,
                            COALESCE(MAX(c.municipio), CONCAT('Cód. ', v.codigo_municipio)) AS nombre,
                            COUNT(r.id) AS total
                         FROM " . $db->getTable('tbl_cuestionario_respuestas') . " r
                         INNER JOIN " . $db->getTable('tbl_cuestionario_intentos') . " i
                            ON r.tbl_intento_id = i.id
                            AND (i.es_prueba = 0 OR i.es_prueba IS NULL)
                         INNER JOIN " . $db->getTable('tbl_votantes') . " v ON i.tbl_votante_id = v.id
                         " . $filtroSql['join_usuario'] . "
                         INNER JOIN " . $db->getTable('tbl_opciones_respuesta') . " o
                            ON r.tbl_opcion_respuesta_id = o.id
                            AND (o.eliminado = 0 OR o.eliminado IS NULL)
                         LEFT JOIN " . $db->getTable('tbl_ciudades_accion_unificada') . " c
                            ON LPAD(CAST(c.codigo_muncipio AS UNSIGNED), 5, '0') = LPAD(CAST(v.codigo_municipio AS UNSIGNED), 5, '0')
                         WHERE o.tbl_pregunta_id = :pregunta_id
                           AND r.tbl_grilla_fila_id IS NULL
                           AND r.tbl_grilla_columna_id IS NULL
                           AND LPAD(CAST(v.codigo_departamento AS UNSIGNED), 2, '0') = :dep
                           AND v.codigo_municipio IS NOT NULL AND v.codigo_municipio != ''
                           " . $filtroSql['and_sql'] . "
                         GROUP BY LPAD(CAST(v.codigo_municipio AS UNSIGNED), 5, '0')
                         ORDER BY total DESC, nombre ASC";
            $stmt = $pdo->prepare($qTotales);
            $stmt->execute($paramsMuni);
            $totales = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $qWin = "SELECT
                        LPAD(CAST(v.codigo_municipio AS UNSIGNED), 5, '0') AS codigo_municipio,
                        r.tbl_opcion_respuesta_id AS opcion_id,
                        COUNT(r.id) AS total
                     FROM " . $db->getTable('tbl_cuestionario_respuestas') . " r
                     INNER JOIN " . $db->getTable('tbl_cuestionario_intentos') . " i
                        ON r.tbl_intento_id = i.id
                        AND (i.es_prueba = 0 OR i.es_prueba IS NULL)
                     INNER JOIN " . $db->getTable('tbl_votantes') . " v ON i.tbl_votante_id = v.id
                     " . $filtroSql['join_usuario'] . "
                     INNER JOIN " . $db->getTable('tbl_opciones_respuesta') . " o
                        ON r.tbl_opcion_respuesta_id = o.id
                        AND (o.eliminado = 0 OR o.eliminado IS NULL)
                     WHERE o.tbl_pregunta_id = :pregunta_id
                       AND r.tbl_grilla_fila_id IS NULL
                       AND r.tbl_grilla_columna_id IS NULL
                       AND LPAD(CAST(v.codigo_departamento AS UNSIGNED), 2, '0') = :dep
                       AND v.codigo_municipio IS NOT NULL AND v.codigo_municipio != ''
                       " . $filtroSql['and_sql'] . "
                     GROUP BY LPAD(CAST(v.codigo_municipio AS UNSIGNED), 5, '0'), r.tbl_opcion_respuesta_id
                     ORDER BY codigo_municipio, total DESC";
            $stmt = $pdo->prepare($qWin);
            $stmt->execute($paramsMuni);
            $winRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $db->closeConect();

            $ganadores = [];
            foreach ($winRows as $row) {
                $m = (string)$row['codigo_municipio'];
                $totalRow = (int)$row['total'];
                if (!isset($ganadores[$m])) {
                    $ganadores[$m] = ["empate" => false, "ganador" => (int)$row['opcion_id'], "max_votos" => $totalRow];
                } elseif ($totalRow == $ganadores[$m]['max_votos']) {
                    $ganadores[$m]['empate'] = true;
                    $ganadores[$m]['ganador'] = null;
                }
            }

            $municipios = [];
            foreach ($totales as $row) {
                $codigo = (string)$row['codigo'];
                $g = $ganadores[$codigo] ?? null;
                $ganadorId = ($g && empty($g['empate']) && !empty($g['ganador'])) ? (int)$g['ganador'] : null;
                $empate = !empty($g['empate']);
                $municipios[] = [
                    "codigo" => $codigo,
                    "nombre" => $row['nombre'],
                    "total" => (int)$row['total'],
                    "ganador_id" => $ganadorId,
                    "ganador_nombre" => ($ganadorId && isset($nombresOpciones[$ganadorId]))
                        ? $nombresOpciones[$ganadorId]
                        : ($empate ? "Empate" : null),
                    "empate" => $empate,
                    "color" => ($ganadorId && isset($coloresOpciones[$ganadorId]))
                        ? $coloresOpciones[$ganadorId]
                        : ($empate ? "#94a3b8" : "#20427F"),
                ];
            }

            return [
                "success" => true,
                "departamento" => $dep,
                "pregunta" => ["id" => (int)$pregunta['id'], "texto" => $pregunta['texto_pregunta']],
                "municipios" => $municipios,
            ];
        } catch (Exception $e) {
            $db->closeConect();
            error_log('RespuestaCuestionario: ' . $e->getMessage());
            return ["success" => false, "message" => "Ocurrió un error al procesar la solicitud. Intente de nuevo.", "municipios" => []];
        }
    }

    public static function obtenerColoresMapaMunicipios($rqst)
    {
        $dep = Util::normalizeCodigoDepartamento($rqst['departamento_click'] ?? $rqst['codigo_departamento'] ?? '');
        $tot = self::obtenerTotalesPorMunicipioIndex($rqst);
        $colores = [];
        $ganadores = [];
        if (!empty($tot['municipios'])) {
            foreach ($tot['municipios'] as $m) {
                if (!empty($m['ganador_id'])) {
                    $colores[(int)$m['ganador_id']] = $m['color'];
                }
                $ganadores[$m['codigo']] = [
                    'ganador' => $m['ganador_id'],
                    'empate' => !empty($m['empate']),
                ];
            }
        }
        return [
            'success' => !empty($tot['success']),
            'departamento' => $dep,
            'colores' => $colores,
            'ganadores' => $ganadores,
        ];
    }

    /**
     * Obtiene las opciones de la primera pregunta del cuestionario activo con sus IDs
     * (para asignar colores en el mapa)
     * @return array [['id' => opcion_id, 'texto' => texto_opcion], ...]
     */
    public static function obtenerOpcionesCuestionarioActivo($rqst = [])
    {
        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            // Obtener la ficha técnica habilitada
            $ficha = self::resolveFichaIndex($pdo, $db, is_array($rqst) ? $rqst : []);

            if (!$ficha) {
                $db->closeConect();
                return [];
            }

            $pregunta = self::resolvePreguntaMapaIndex($pdo, $db, $ficha, 0);

            if (!$pregunta) {
                $db->closeConect();
                return [];
            }

            // Obtener opciones de la pregunta
            $qOpciones = "SELECT id, texto_opcion as texto
                          FROM " . $db->getTable('tbl_opciones_respuesta') . "
                          WHERE tbl_pregunta_id = :pregunta_id
                            AND (eliminado = 0 OR eliminado IS NULL)
                          ORDER BY COALESCE(orden, 999999) ASC, id ASC";
            $stmt = $pdo->prepare($qOpciones);
            $stmt->execute([":pregunta_id" => $pregunta['id']]);
            $opciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $db->closeConect();
            return $opciones;

        } catch (Exception $e) {
            $db->closeConect();
            return [];
        }
    }

    /**
     * Obtiene los colores para el mapa según una pregunta específica
     * Devuelve datos para actualizar el mapa dinámicamente vía AJAX
     * @param array $rqst Con pregunta_id
     * @return array Con ganadores por departamento y colores de opciones
     */
    public static function obtenerColoresMapaCuestionario($rqst)
    {
        $preguntaId = isset($rqst['pregunta_id']) ? intval($rqst['pregunta_id']) : 0;

        // Paleta de colores
        $paletaColores = [
            "#1f77b4", "#ff7f0e", "#2ca02c", "#d62728", "#9467bd",
            "#8c564b", "#e377c2", "#7f7f7f", "#bcbd22", "#17becf"
        ];

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            // Obtener ficha técnica habilitada
            $ficha = self::resolveFichaIndex($pdo, $db, $rqst);

            if (!$ficha) {
                $db->closeConect();
                return ["success" => false, "message" => "No hay cuestionario activo"];
            }

            $pregunta = self::resolvePreguntaMapaIndex($pdo, $db, $ficha, $preguntaId);

            if (!$pregunta) {
                $db->closeConect();
                return ["success" => false, "message" => "Pregunta no encontrada"];
            }

            // Obtener opciones y asignar colores
            $qOpciones = "SELECT id FROM " . $db->getTable('tbl_opciones_respuesta') . "
                          WHERE tbl_pregunta_id = :pregunta_id
                            AND (eliminado = 0 OR eliminado IS NULL)
                          ORDER BY COALESCE(orden, 999999) ASC, id ASC";
            $stmt = $pdo->prepare($qOpciones);
            $stmt->execute([":pregunta_id" => $pregunta['id']]);
            $opciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $coloresOpciones = [];
            foreach ($opciones as $index => $opc) {
                $coloresOpciones[(string)$opc['id']] = $paletaColores[$index % count($paletaColores)];
            }

            $db->closeConect();

            // Obtener ganadores por departamento
            $ganadores = self::ganadorPorTodosLosDepartamentosCuestionario($pregunta['id'], $rqst);

            // Normalizar claves de departamento a 2 dígitos para el SVG
            $ganadoresNorm = [];
            foreach ($ganadores as $cod => $info) {
                $key = Util::normalizeCodigoDepartamento($cod);
                if ($key === '') {
                    continue;
                }
                $ganadoresNorm[$key] = [
                    'ganador' => isset($info['ganador']) ? (string)$info['ganador'] : null,
                    'empate' => !empty($info['empate']),
                ];
            }

            return [
                "success" => true,
                "colores" => $coloresOpciones,
                "ganadores" => $ganadoresNorm
            ];

        } catch (Exception $e) {
            $db->closeConect();
            error_log('RespuestaCuestionario: ' . $e->getMessage());
            return ["success" => false, "message" => "Ocurrió un error al procesar la solicitud. Intente de nuevo."];
        }
    }

    /**
     * Verificar si el usuario ya ha respondido algún cuestionario activo
     */
    public function verificarSiUsuarioRespondio($usuarioId)
    {
        $pdo = $this->db->openConect();
        
        try {
            // Verificar si el usuario tiene algún intento en la tabla
            $q = "SELECT COUNT(*) as count FROM " . $this->db->getTable('tbl_cuestionario_intentos') . " 
                  WHERE tbl_votante_id = ? AND (es_prueba = 0 OR es_prueba IS NULL)";
            $stmt = $pdo->prepare($q);
            $stmt->execute([$usuarioId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $tieneIntentos = $result['count'] > 0;
            error_log("DEBUG: Usuario $usuarioId tiene $result[count] intentos de cuestionario");
            
            // Si tiene intentos, verificamos si hay algún cuestionario activo
            if ($tieneIntentos) {
                // Temporalmente devolvemos true si tiene intentos, sin importar si está activo
                error_log("DEBUG: Usuario tiene intentos, permitiendo ver registros");
                return true;
                
                // $q = "SELECT COUNT(*) as count FROM " . $this->db->getTable('tbl_ficha_tecnica_encuestas') . " 
                //       WHERE habilitado = 'si'";
                // $stmt = $pdo->prepare($q);
                // $stmt->execute();
                // $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // $hayActivos = $result['count'] > 0;
                // error_log("DEBUG: Hay $result[count] cuestionarios activos");
                
                // return $hayActivos; // Puede ver registros si tiene intentos Y hay cuestionarios activos
            }
            
            return false;
            
        } catch (Exception $e) {
            error_log("Error verificando respuesta del usuario: " . $e->getMessage());
            return false;
        } finally {
            $this->db->closeConect();
        }
    }
}
