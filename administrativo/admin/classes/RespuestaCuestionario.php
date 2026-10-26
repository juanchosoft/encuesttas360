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
            $q = "SELECT v.id, v.nombre_completo, v.username, v.email, v.codigo_departamento, v.codigo_municipio, v.comuna
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
        require_once __DIR__ . '/Votantes.php';

        if (!isset($rqst['data']) || empty($rqst['data'])) {
            return Util::error_missing_data();
        }

        $data = is_array($rqst['data']) ? $rqst['data'] : json_decode($rqst['data'], true);

        if (!$data || !is_array($data)) {
            return Util::error_missing_data_description('Los datos no son válidos');
        }

        $fichaId = isset($data['ficha_tecnica_id']) ? (int)$data['ficha_tecnica_id'] : 0;
        $votanteId = isset($data['tbl_votante_id']) ? (int)$data['tbl_votante_id'] : 0;
        $esPrueba = !empty($data['es_prueba']) ? 1 : 0;
        if ($esPrueba === 1 && !SessionData::hasPermission('encuestas.preguntas.reglas')) {
            return Util::error_general('No tiene permiso para probar el cuestionario.');
        }

        if ($fichaId <= 0 || ($votanteId <= 0 && $esPrueba === 0)) {
            return Util::error_missing_data_description('Faltan datos obligatorios (ficha técnica o votante)');
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
                $previo = self::respuestaIdempotente($pdo, $db, $clientUuid, $fichaId);
                if ($previo !== null) {
                    $db->closeConect();
                    return $previo;
                }
            }

            if ($votanteId > 0 && $esPrueba === 0) {
                $qVerificar = "SELECT id FROM " . $db->getTable('tbl_cuestionario_intentos') . "
                    WHERE tbl_ficha_tecnica_encuesta_id = :ficha_tecnica_id
                    AND tbl_votante_id = :tbl_votante_id
                    AND (es_prueba = 0 OR es_prueba IS NULL)
                    LIMIT 1";

                $stmtVerificar = $pdo->prepare($qVerificar);
                $stmtVerificar->execute([
                    ':ficha_tecnica_id' => $fichaId,
                    ':tbl_votante_id' => $votanteId
                ]);

                if ($stmtVerificar->fetch()) {
                    $db->closeConect();
                    return Util::error_missing_data_description('Este votante ya ha respondido este cuestionario');
                }
            }

            $def = DefinicionCuestionario::cargar($pdo, $fichaId, $db->getDbName());
            if (!$def) {
                $db->closeConect();
                return Util::error_missing_data_description('No se encontró la definición del cuestionario.');
            }

            $versionCliente = isset($data['definicion_hash'])
                ? trim((string)$data['definicion_hash'])
                : (isset($data['version']) ? trim((string)$data['version']) : '');
            if ($versionCliente !== (string)$def['version']) {
                $db->closeConect();
                return Util::error_missing_data_description(
                    'El cuestionario fue actualizado recientemente. Por favor recargue la página para cargar la versión vigente.'
                );
            }

            $codigoDepto = isset($data['codigo_departamento']) ? trim((string)$data['codigo_departamento']) : '';
            $codigoMpio = isset($data['codigo_municipio']) ? trim((string)$data['codigo_municipio']) : '';
            $zonaId = !empty($data['tbl_zona_muestreo_id']) ? (int)$data['tbl_zona_muestreo_id'] : null;
            $zonaTexto = isset($data['zona_texto']) ? trim((string)$data['zona_texto']) : '';

            if ($votanteId > 0 && ($codigoMpio === '' || $codigoDepto === '')) {
                $stVot = $pdo->prepare("SELECT codigo_departamento, codigo_municipio, comuna FROM " . $db->getTable('tbl_votantes') . " WHERE id = :vid LIMIT 1");
                $stVot->execute([':vid' => $votanteId]);
                $vRow = $stVot->fetch(PDO::FETCH_ASSOC);
                if ($vRow) {
                    if ($codigoDepto === '') $codigoDepto = trim((string)($vRow['codigo_departamento'] ?? ''));
                    if ($codigoMpio === '')  $codigoMpio  = trim((string)($vRow['codigo_municipio'] ?? ''));
                    if ($zonaTexto === '')   $zonaTexto   = trim((string)($vRow['comuna'] ?? ''));
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
                        'response' => [
                            'content' => $primerError
                        ]
                    ]
                ];
            }

            if ($esPrueba === 1) {
                $db->closeConect();
                return [
                    'output' => [
                        'valid' => true,
                        'es_prueba' => true,
                        'simulado' => true,
                        'message' => 'Prueba correcta: las respuestas cumplen las reglas del cuestionario (no se guardó nada).',
                        'intento_id' => 0,
                        'estado_intento' => $val['estado_intento'],
                        'valido_para_pago' => (int)$val['valido_para_pago']
                    ]
                ];
            }

            $pdo->beginTransaction();

            $encuestadorId = (int)($_SESSION['session_user']['id'] ?? 0);

            $qIntento = "INSERT INTO " . $db->getTable('tbl_cuestionario_intentos') . "
                (tbl_ficha_tecnica_encuesta_id, tbl_votante_id, estado, terminado_en_pregunta_id, terminado_opcion_id,
                 valido_para_pago, origen, codigo_departamento, codigo_municipio, tbl_zona_muestreo_id, zona_texto,
                 tbl_usuario_encuestador_id, client_uuid, motor_version, definicion_hash, es_prueba, fecha_respuesta, dtcreate)
                VALUES (:ficha_tecnica_id, :tbl_votante_id, :estado, :term_preg_id, :term_opt_id,
                        :valido_pago, 'contestar', :depto, :mpio, :zona_id, :zona_texto,
                        :encuestador_id, :client_uuid, :motor_ver, :def_hash, :es_prueba, NOW(), NOW())";

            $stmtIntento = $pdo->prepare($qIntento);
            $stmtIntento->execute([
                ':ficha_tecnica_id' => $fichaId,
                ':tbl_votante_id' => $votanteId > 0 ? $votanteId : null,
                ':estado' => $val['estado_intento'],
                ':term_preg_id' => $val['terminado_en_pregunta_id'] ?? null,
                ':term_opt_id' => $val['terminado_opcion_id'] ?? null,
                ':valido_pago' => (int)$val['valido_para_pago'],
                ':depto' => $codigoDepto !== '' ? $codigoDepto : null,
                ':mpio' => $codigoMpio !== '' ? $codigoMpio : null,
                ':zona_id' => $zonaId,
                ':zona_texto' => $zonaTexto !== '' ? $zonaTexto : null,
                ':encuestador_id' => $encuestadorId > 0 ? $encuestadorId : null,
                ':client_uuid' => $clientUuid !== '' ? $clientUuid : null,
                ':motor_ver' => $def['motor_version'] ?? MotorReglasCuestionario::MOTOR_VERSION,
                ':def_hash' => $def['version'] ?? null,
                ':es_prueba' => $esPrueba,
            ]);

            $intentoId = (int)$pdo->lastInsertId();

            $qRespuesta = "INSERT INTO " . $db->getTable('tbl_cuestionario_respuestas') . "
                (tbl_intento_id, tbl_pregunta_id, tbl_opcion_respuesta_id,
                 tbl_grilla_fila_id, tbl_grilla_columna_id, tbl_grilla_columna_opcion_id,
                 valor_numerico, respuesta_texto, texto_snapshot, dtcreate)
                VALUES (:intento_id, :pregunta_id, :opcion_id,
                        :fila_id, :col_id, :col_opt_id,
                        :val_num, :texto, :snapshot, NOW())";

            $stmtRespuesta = $pdo->prepare($qRespuesta);

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

            if ($votanteId > 0) {
                $camposEquiv = Votantes::extraerEquivalenciasVotante($def, $val['respuestas_normalizadas']);
                if (!empty($camposEquiv)) {
                    $sets = [];
                    $paramsUpd = [':vid' => $votanteId];
                    foreach ($camposEquiv as $col => $valorVot) {
                        if (in_array($col, ['genero', 'rango_edad', 'ideologia', 'nivel_ingresos', 'nivel_educacion', 'ocupacion'], true)) {
                            $sets[] = "{$col} = :{$col}";
                            $paramsUpd[":{$col}"] = $valorVot;
                        }
                    }
                    if (!empty($sets)) {
                        $pdo->prepare("UPDATE " . $db->getTable('tbl_votantes') . " SET " . implode(', ', $sets) . ", dtupdate = NOW() WHERE id = :vid")
                            ->execute($paramsUpd);
                    }
                }
            }

            $pdo->commit();
            $db->closeConect();

            return [
                'output' => [
                    'valid' => true,
                    'message' => 'Respuestas guardadas correctamente',
                    'intento_id' => $intentoId,
                    'estado_intento' => $val['estado_intento'],
                    'valido_para_pago' => (int)$val['valido_para_pago']
                ]
            ];

        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log('RespuestaCuestionario::save error: ' . $e->getMessage());
            if (!empty($clientUuid) && strpos($e->getMessage(), '1062') !== false && strpos($e->getMessage(), 'client_uuid') !== false) {
                $previo = self::respuestaIdempotente($pdo, $db, $clientUuid, $fichaId);
                if ($previo !== null) {
                    $db->closeConect();
                    return $previo;
                }
            }
            $db->closeConect();
            return Util::error_general('No fue posible guardar las respuestas. Intente de nuevo; si el problema continúa, avise al administrador.');
        }
    }

    private static function respuestaIdempotente($pdo, $db, string $clientUuid, int $fichaId): ?array
    {
        $st = $pdo->prepare("SELECT id, tbl_ficha_tecnica_encuesta_id, tbl_usuario_encuestador_id, estado, valido_para_pago FROM " . $db->getTable('tbl_cuestionario_intentos') . " WHERE client_uuid = :uuid LIMIT 1");
        $st->execute([':uuid' => $clientUuid]);
        $prev = $st->fetch(PDO::FETCH_ASSOC);
        if (!$prev) {
            return null;
        }
        $usuario = (int)($_SESSION['session_user']['id'] ?? 0);
        if ((int)$prev['tbl_ficha_tecnica_encuesta_id'] !== $fichaId || (int)($prev['tbl_usuario_encuestador_id'] ?? 0) !== $usuario) {
            return Util::error_general('Este envío ya fue registrado por otra encuesta. Recargue la página e intente de nuevo.');
        }
        return [
            'output' => [
                'valid' => true,
                'message' => 'Respuestas guardadas correctamente',
                'intento_id' => (int)$prev['id'],
                'estado_intento' => $prev['estado'],
                'valido_para_pago' => (int)$prev['valido_para_pago'],
                'idempotent' => true
            ]
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
    public static function getDetalleLegible($rqst)
    {
        $intentoId = isset($rqst['intento_id']) ? intval($rqst['intento_id']) : 0;
        if ($intentoId <= 0) {
            return Util::error_missing_data_description('ID de intento requerido');
        }
        require_once __DIR__ . '/cuestionario/DetalleIntento.php';
        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $detalle = DetalleIntento::cargar($pdo, $db, $intentoId);
            $db->closeConect();
            if (!$detalle) {
                return Util::error_no_result();
            }
            return ['output' => ['valid' => true, 'response' => $detalle]];
        } catch (Exception $e) {
            $db->closeConect();
            error_log('RespuestaCuestionario::getDetalleLegible error: ' . $e->getMessage());
            return Util::error_general('Error al obtener el detalle de respuestas.');
        }
    }

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
                AND (i.es_prueba = 0 OR i.es_prueba IS NULL)
                AND v.estado = 'activo'" . $geoV;
            $stmtRespondieron = $pdo->prepare($qRespondieron);
            $stmtRespondieron->execute($paramsBase);
            $totalRespondieron = $stmtRespondieron->fetch(PDO::FETCH_ASSOC)['total'];

            $totalNoRespondieron = max(0, $totalVotantes - $totalRespondieron);
            $porcentajeRespuestas = $totalVotantes > 0 ? round(($totalRespondieron / $totalVotantes) * 100, 2) : 0;
            $ultimasRespuestas = [];

            $demoWhere = "i.tbl_ficha_tecnica_encuesta_id = :ficha_tecnica_id AND (i.es_prueba = 0 OR i.es_prueba IS NULL) AND v.estado = 'activo'" . $geoV;

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
                AND (i.es_prueba = 0 OR i.es_prueba IS NULL)
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
            error_log('RespuestaCuestionario: ' . $e->getMessage());
            return Util::error_general('Error al obtener votantes que respondieron.');
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
            'estado' => (isset($rqst['filtro_estado']) && in_array($rqst['filtro_estado'], ['completo', 'terminado_filtro', 'pendiente_audio'], true)) ? $rqst['filtro_estado'] : '',
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
            $tipos = ['Encuestador', 'Autoregistro'];

            $qEnc = "SELECT DISTINCT enc AS encuestador FROM (
                    SELECT {$encSql} AS enc
                    FROM " . $db->getTable('tbl_cuestionario_intentos') . " i
                    INNER JOIN " . $db->getTable('tbl_votantes') . " v ON i.tbl_votante_id = v.id
                    LEFT JOIN " . $db->getTable('tbl_usuarios') . " u ON v.tbl_usuario_id = u.id
                    WHERE i.tbl_ficha_tecnica_encuesta_id = :ficha
                    AND i.tbl_votante_id IS NOT NULL
                    AND (i.es_prueba = 0 OR i.es_prueba IS NULL)
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
            error_log('RespuestaCuestionario: ' . $e->getMessage());
            return Util::error_general('Error al obtener filtros.');
        }
    }

    /**
     * KPIs ejecutivos para el listado del dashboard (total encuestas + encuestadores)
     * Respeta los mismos filtros del listado: tipo, encuestador, fechas.
     */
    public static function getKpisListadoDashboard($rqst)
    {
        $p = self::parseDtRequest($rqst);
        if ($p['ficha'] === 0) {
            return Util::error_missing_data_description('ID de ficha técnica requerido');
        }

        $db = new DbConection();
        $pdo = $db->openConect();
        $tipoSql = self::sqlTipoRegistro();
        $encSql = self::sqlEncuestadorNombre();

        try {
            $base = " FROM " . $db->getTable('tbl_cuestionario_intentos') . " i
                INNER JOIN " . $db->getTable('tbl_votantes') . " v ON i.tbl_votante_id = v.id
                LEFT JOIN " . $db->getTable('tbl_usuarios') . " u ON v.tbl_usuario_id = u.id
                WHERE i.tbl_ficha_tecnica_encuesta_id = :ficha
                AND i.tbl_votante_id IS NOT NULL
                AND (i.es_prueba = 0 OR i.es_prueba IS NULL)";

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

            if (count($whereExtra)) {
                $base .= ' AND ' . implode(' AND ', $whereExtra);
            }

            $stmt = $pdo->prepare("SELECT COUNT(*) AS total {$base}");
            $stmt->execute($params);
            $totalRespuestas = (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];

            $stmt = $pdo->prepare(
                "SELECT COUNT(DISTINCT u.id) AS total {$base}
                 AND u.tipo = 'Encuestador' AND u.id IS NOT NULL"
            );
            $stmt->execute($params);
            $totalEncuestadores = (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];

            $db->closeConect();
            return [
                'output' => [
                    'valid' => true,
                    'response' => [
                        'total_respuestas' => $totalRespuestas,
                        'total_encuestadores' => $totalEncuestadores,
                    ],
                ],
            ];
        } catch (Exception $e) {
            $db->closeConect();
            error_log('RespuestaCuestionario: ' . $e->getMessage());
            return Util::error_general('Error al obtener KPIs.');
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
                AND i.tbl_votante_id IS NOT NULL
                AND (i.es_prueba = 0 OR i.es_prueba IS NULL)";

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
            error_log('RespuestaCuestionario: ' . $e->getMessage());
            return Util::error_general('Error al listar respuestas.');
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
                AND (i.es_prueba = 0 OR i.es_prueba IS NULL)
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
            if ($p['estado'] !== '') {
                $whereExtra[] = "i.estado = :filtro_estado";
                $params[':filtro_estado'] = $p['estado'];
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
                    i.estado AS estado_intento,
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
            error_log('RespuestaCuestionario: ' . $e->getMessage());
            return Util::error_general('Error al listar respondieron.');
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
                    AND (es_prueba = 0 OR es_prueba IS NULL)
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
            error_log('RespuestaCuestionario: ' . $e->getMessage());
            return Util::error_general('Error al listar pendientes.');
        }
    }
}
