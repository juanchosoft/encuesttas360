<?php
/**
 * Fase B — Participación pública sin login.
 * Geo (encuestas), device UUID, borrador y commit.
 */
class ParticipacionPublica
{
    const DRAFT_TTL_SECONDS = 7200;
    const TABLE_DEVICE = 'tbl_participacion_dispositivo';

    public static function ensureSchema()
    {
        static $done = false;
        if ($done) {
            return;
        }
        $done = true;

        try {
            $db = new DbConection();
            $pdo = $db->openConect();
            $tbl = $db->getTable(self::TABLE_DEVICE);
            $pdo->exec(
                "CREATE TABLE IF NOT EXISTS $tbl (
                    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                    device_uuid VARCHAR(80) NOT NULL,
                    device_fingerprint VARCHAR(128) NULL,
                    tipo_instrumento VARCHAR(20) NOT NULL,
                    instrumento_id INT UNSIGNED NOT NULL,
                    tbl_votante_id INT UNSIGNED NOT NULL,
                    dtcreate DATETIME NOT NULL,
                    PRIMARY KEY (id),
                    UNIQUE KEY uq_device_instrumento (device_uuid, tipo_instrumento, instrumento_id),
                    KEY idx_device (device_uuid),
                    KEY idx_votante (tbl_votante_id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
            );
            $db->closeConect();
        } catch (Exception $e) {
            // Continuar: el lookup por tbl_votantes.device_token sigue disponible
        }
    }

    public static function normalizeDeviceUuid($uuid)
    {
        $uuid = trim((string)$uuid);
        if ($uuid === '' || strlen($uuid) > 80) {
            return '';
        }
        if (!preg_match('/^[a-zA-Z0-9_\-\.]+$/', $uuid)) {
            return '';
        }
        return $uuid;
    }

    public static function getGeoFromSession()
    {
        $g = $_SESSION['geo_participacion'] ?? null;
        if (!is_array($g)) {
            return ['codigo_departamento' => '', 'codigo_municipio' => '', 'ok' => false];
        }
        return [
            'codigo_departamento' => (string)($g['codigo_departamento'] ?? ''),
            'codigo_municipio' => (string)($g['codigo_municipio'] ?? ''),
            'departamento' => (string)($g['departamento'] ?? ''),
            'municipio' => (string)($g['municipio'] ?? ''),
            'ok' => !empty($g['codigo_departamento']) || !empty($g['codigo_municipio']),
        ];
    }

    public static function setGeoSession(array $geo)
    {
        $_SESSION['geo_participacion'] = [
            'codigo_departamento' => (string)($geo['codigo_departamento'] ?? ''),
            'codigo_municipio' => (string)($geo['codigo_municipio'] ?? ''),
            'departamento' => (string)($geo['departamento'] ?? ''),
            'municipio' => (string)($geo['municipio'] ?? ''),
            'lat' => $geo['lat'] ?? null,
            'lng' => $geo['lng'] ?? null,
            'ts' => time(),
        ];
    }

    /**
     * Resolver lat/lng → códigos DANE locales vía Nominatim + match BD.
     */
    public static function resolveGeo($rqst)
    {
        $lat = isset($rqst['lat']) ? floatval($rqst['lat']) : null;
        $lng = isset($rqst['lng']) ? floatval($rqst['lng']) : null;

        if ($lat === null || $lng === null || abs($lat) > 90 || abs($lng) > 180) {
            return Util::error_missing_data_description('Coordenadas inválidas');
        }

        $cityName = self::reverseGeocodeCity($lat, $lng);
        if ($cityName === '') {
            return Util::error_general('No se pudo detectar el municipio desde tu ubicación.');
        }

        $match = self::matchCiudadLocal($cityName);
        if (!$match) {
            return Util::error_general('Tu ubicación (' . $cityName . ') no coincide con un municipio en nuestro catálogo.');
        }

        self::setGeoSession([
            'codigo_departamento' => $match['codigo_departamento'],
            'codigo_municipio' => $match['codigo_municipio'],
            'departamento' => $match['departamento'],
            'municipio' => $match['municipio'],
            'lat' => $lat,
            'lng' => $lng,
        ]);

        return [
            'output' => [
                'valid' => true,
                'response' => $match + ['city_detected' => $cityName],
            ],
        ];
    }

    private static function reverseGeocodeCity($lat, $lng)
    {
        $url = 'https://nominatim.openstreetmap.org/reverse?format=json&lat='
            . rawurlencode((string)$lat) . '&lon=' . rawurlencode((string)$lng)
            . '&accept-language=es';

        $ctx = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => "User-Agent: Encuestas360/1.0 (participacion-publica)\r\nAccept: application/json\r\n",
                'timeout' => 8,
            ],
        ]);

        $raw = @file_get_contents($url, false, $ctx);
        if ($raw === false) {
            return '';
        }

        $json = json_decode($raw, true);
        if (!is_array($json) || empty($json['address']) || !is_array($json['address'])) {
            return '';
        }

        $a = $json['address'];
        $city = $a['city'] ?? $a['town'] ?? $a['municipality'] ?? $a['village'] ?? $a['county'] ?? '';
        return trim((string)$city);
    }

    private static function normalizeName($s)
    {
        $s = mb_strtolower(trim((string)$s), 'UTF-8');
        $s = preg_replace('/\s+/', ' ', $s);
        if (class_exists('Normalizer')) {
            $s = Normalizer::normalize($s, Normalizer::FORM_D);
            $s = preg_replace('/\p{Mn}/u', '', $s);
        } else {
            $s = strtr($s, [
                'á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ñ'=>'n',
                'ü'=>'u','Á'=>'a','É'=>'e','Í'=>'i','Ó'=>'o','Ú'=>'u','Ñ'=>'n',
            ]);
        }
        return $s;
    }

    private static function matchCiudadLocal($cityName)
    {
        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $q = "SELECT c.codigo_muncipio AS codigo_municipio, c.municipio,
                         c.codigo_departamento, d.departamento
                  FROM " . $db->getTable('tbl_ciudades') . " c
                  LEFT JOIN " . $db->getTable('tbl_departamentos') . " d
                    ON c.codigo_departamento = d.codigo_departamento";
            $rows = $pdo->query($q)->fetchAll(PDO::FETCH_ASSOC);
            $db->closeConect();

            $needle = self::normalizeName($cityName);
            $best = null;

            foreach ($rows as $row) {
                $mun = self::normalizeName($row['municipio'] ?? '');
                if ($mun === '') {
                    continue;
                }
                if ($mun === $needle) {
                    return [
                        'codigo_municipio' => (string)$row['codigo_municipio'],
                        'municipio' => (string)$row['municipio'],
                        'codigo_departamento' => (string)$row['codigo_departamento'],
                        'departamento' => (string)($row['departamento'] ?? ''),
                    ];
                }
                if ($best === null && (strpos($mun, $needle) !== false || strpos($needle, $mun) !== false)) {
                    $best = $row;
                }
            }

            if ($best) {
                return [
                    'codigo_municipio' => (string)$best['codigo_municipio'],
                    'municipio' => (string)$best['municipio'],
                    'codigo_departamento' => (string)$best['codigo_departamento'],
                    'departamento' => (string)($best['departamento'] ?? ''),
                ];
            }

            return null;
        } catch (Exception $e) {
            $db->closeConect();
            return null;
        }
    }

    public static function fichaVisible($fichaId, $codigoDepto, $codigoMunicipio)
    {
        $fichaId = (int)$fichaId;
        if ($fichaId <= 0) {
            return false;
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        $tblEG = $db->getTable('tbl_espacio_geografico');
        $tblFTE = $db->getTable('tbl_ficha_tecnica_encuestas');
        $tblXD = $db->getTable('tbl_espacio_geografico_x_departamentos_x_ciudades');

        try {
            $stmtEg = $pdo->prepare(
                "SELECT eg.tipo_estudio FROM $tblEG eg
                 JOIN $tblFTE fte ON fte.tbl_espacio_geografico_id = eg.id
                 WHERE fte.id = :ficha_id LIMIT 1"
            );
            $stmtEg->execute([':ficha_id' => $fichaId]);
            $eg = $stmtEg->fetch(PDO::FETCH_ASSOC);

            if (!$eg) {
                $db->closeConect();
                return true;
            }

            $tipo = strtolower((string)$eg['tipo_estudio']);
            if ($tipo === 'nacional') {
                $db->closeConect();
                return true;
            }

            if ($tipo === 'departamental') {
                $stmt = $pdo->prepare(
                    "SELECT COUNT(*) FROM $tblXD xd
                     JOIN $tblFTE fte ON fte.tbl_espacio_geografico_id = xd.tbl_espacio_geografico_id
                     WHERE fte.id = :ficha_id
                     AND CAST(xd.codigo_departamento AS UNSIGNED) = CAST(:depto AS UNSIGNED)"
                );
                $stmt->execute([':ficha_id' => $fichaId, ':depto' => $codigoDepto]);
                $visible = (int)$stmt->fetchColumn() > 0;
            } else {
                $stmt = $pdo->prepare(
                    "SELECT COUNT(*) FROM $tblXD xd
                     JOIN $tblFTE fte ON fte.tbl_espacio_geografico_id = xd.tbl_espacio_geografico_id
                     WHERE fte.id = :ficha_id
                     AND CAST(xd.codigo_ciudad AS UNSIGNED) = CAST(:municipio AS UNSIGNED)"
                );
                $stmt->execute([':ficha_id' => $fichaId, ':municipio' => $codigoMunicipio]);
                $visible = (int)$stmt->fetchColumn() > 0;
            }

            $db->closeConect();
            return $visible;
        } catch (Exception $e) {
            $db->closeConect();
            return false;
        }
    }

    public static function findVotanteByDevice($deviceUuid)
    {
        $deviceUuid = self::normalizeDeviceUuid($deviceUuid);
        if ($deviceUuid === '') {
            return null;
        }

        self::ensureSchema();
        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $stmt = $pdo->prepare(
                "SELECT id, email, username, codigo_departamento, codigo_municipio, device_token, device_fingerprint
                 FROM " . $db->getTable('tbl_votantes') . "
                 WHERE device_token = :du
                 ORDER BY id DESC LIMIT 1"
            );
            $stmt->execute([':du' => $deviceUuid]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $db->closeConect();
                return $row;
            }

            $tbl = $db->getTable(self::TABLE_DEVICE);
            $stmt2 = $pdo->prepare(
                "SELECT v.id, v.email, v.username, v.codigo_departamento, v.codigo_municipio
                 FROM $tbl p
                 JOIN " . $db->getTable('tbl_votantes') . " v ON v.id = p.tbl_votante_id
                 WHERE p.device_uuid = :du
                 ORDER BY p.id DESC LIMIT 1"
            );
            $stmt2->execute([':du' => $deviceUuid]);
            $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
            $db->closeConect();
            return $row2 ?: null;
        } catch (Exception $e) {
            $db->closeConect();
            return null;
        }
    }

    public static function deviceAlreadyParticipated($tipo, $instrumentoId, $deviceUuid)
    {
        $deviceUuid = self::normalizeDeviceUuid($deviceUuid);
        $instrumentoId = (int)$instrumentoId;
        $tipo = ($tipo === 'sondeo') ? 'sondeo' : 'encuesta';
        if ($deviceUuid === '' || $instrumentoId <= 0) {
            return false;
        }

        self::ensureSchema();
        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $tbl = $db->getTable(self::TABLE_DEVICE);
            $stmt = $pdo->prepare(
                "SELECT id FROM $tbl
                 WHERE device_uuid = :du AND tipo_instrumento = :tipo AND instrumento_id = :iid
                 LIMIT 1"
            );
            $stmt->execute([':du' => $deviceUuid, ':tipo' => $tipo, ':iid' => $instrumentoId]);
            if ($stmt->fetch()) {
                $db->closeConect();
                return true;
            }

            $votante = self::findVotanteByDevice($deviceUuid);
            if (!$votante) {
                $db->closeConect();
                return false;
            }
            $vid = (int)$votante['id'];

            if ($tipo === 'sondeo') {
                $st = $pdo->prepare(
                    "SELECT id FROM " . $db->getTable('tbl_respuestas_sondeos') . "
                     WHERE tbl_votante_id = :v AND tbl_sondeo_id = :s LIMIT 1"
                );
                $st->execute([':v' => $vid, ':s' => $instrumentoId]);
            } else {
                $st = $pdo->prepare(
                    "SELECT id FROM " . $db->getTable('tbl_cuestionario_intentos') . "
                     WHERE tbl_votante_id = :v AND tbl_ficha_tecnica_encuesta_id = :f LIMIT 1"
                );
                $st->execute([':v' => $vid, ':f' => $instrumentoId]);
            }
            $ok = (bool)$st->fetch();
            $db->closeConect();
            return $ok;
        } catch (Exception $e) {
            $db->closeConect();
            return false;
        }
    }

    public static function markDeviceParticipation($tipo, $instrumentoId, $deviceUuid, $votanteId, $fingerprint = '')
    {
        self::ensureSchema();
        $deviceUuid = self::normalizeDeviceUuid($deviceUuid);
        $instrumentoId = (int)$instrumentoId;
        $votanteId = (int)$votanteId;
        $tipo = ($tipo === 'sondeo') ? 'sondeo' : 'encuesta';
        if ($deviceUuid === '' || $instrumentoId <= 0 || $votanteId <= 0) {
            return;
        }

        try {
            $db = new DbConection();
            $pdo = $db->openConect();
            $tbl = $db->getTable(self::TABLE_DEVICE);
            $stmt = $pdo->prepare(
                "INSERT IGNORE INTO $tbl
                 (device_uuid, device_fingerprint, tipo_instrumento, instrumento_id, tbl_votante_id, dtcreate)
                 VALUES (:du, :fp, :tipo, :iid, :vid, NOW())"
            );
            $stmt->execute([
                ':du' => $deviceUuid,
                ':fp' => substr((string)$fingerprint, 0, 128),
                ':tipo' => $tipo,
                ':iid' => $instrumentoId,
                ':vid' => $votanteId,
            ]);
            $db->closeConect();
        } catch (Exception $e) {
            // no bloquear commit
        }
    }

    public static function saveDraft($rqst)
    {
        $tipo = isset($rqst['tipo']) ? strtolower(trim((string)$rqst['tipo'])) : '';
        if ($tipo !== 'encuesta' && $tipo !== 'sondeo') {
            return Util::error_missing_data_description('Tipo de participación inválido');
        }

        $deviceUuid = self::normalizeDeviceUuid($rqst['device_uuid'] ?? '');
        $fingerprint = trim((string)($rqst['device_fingerprint'] ?? ''));
        if ($deviceUuid === '') {
            return Util::error_missing_data_description('Dispositivo no identificado');
        }

        $instrumentoId = 0;
        $payload = null;

        if ($tipo === 'encuesta') {
            $dataRaw = $rqst['data'] ?? '';
            $payload = is_string($dataRaw) ? json_decode($dataRaw, true) : $dataRaw;
            if (!is_array($payload) || empty($payload['ficha_tecnica_id']) || empty($payload['preguntas'])) {
                return Util::error_missing_data_description('Respuestas de encuesta incompletas');
            }
            $instrumentoId = (int)$payload['ficha_tecnica_id'];
        } else {
            $instrumentoId = (int)($rqst['sondeo_id'] ?? 0);
            $valor = $rqst['valor'] ?? null;
            $tipoSondeo = trim((string)($rqst['tipo_sondeo'] ?? $rqst['tipo_voto'] ?? ''));
            if ($instrumentoId <= 0 || $valor === null || $valor === '') {
                return Util::error_missing_data_description('Datos de sondeo incompletos');
            }
            $payload = [
                'sondeo_id' => $instrumentoId,
                'valor' => $valor,
                'tipo' => $tipoSondeo !== '' ? $tipoSondeo : trim((string)($rqst['voto_tipo'] ?? '')),
                'pregunta_id' => $rqst['pregunta_id'] ?? null,
            ];
            if ($payload['tipo'] === '') {
                $payload['tipo'] = 'opciones';
            }
        }

        if (self::deviceAlreadyParticipated($tipo, $instrumentoId, $deviceUuid)) {
            return [
                'output' => [
                    'valid' => false,
                    'response' => [
                        'code' => 'ALREADY_PARTICIPATED',
                        'content' => 'Este dispositivo ya participó en este formulario.',
                    ],
                ],
            ];
        }

        $token = bin2hex(random_bytes(16));
        $_SESSION['participacion_draft'] = [
            'token' => $token,
            'tipo' => $tipo,
            'instrumento_id' => $instrumentoId,
            'payload' => $payload,
            'device_uuid' => $deviceUuid,
            'device_fingerprint' => $fingerprint,
            'device_user_agent' => substr((string)($rqst['device_user_agent'] ?? ($_SERVER['HTTP_USER_AGENT'] ?? '')), 0, 255),
            'device_platform' => substr((string)($rqst['device_platform'] ?? ''), 0, 80),
            'device_language' => substr((string)($rqst['device_language'] ?? ''), 0, 40),
            'device_timezone' => substr((string)($rqst['device_timezone'] ?? ''), 0, 60),
            'expires' => time() + self::DRAFT_TTL_SECONDS,
        ];

        $existing = self::findVotanteByDevice($deviceUuid);
        if ($existing) {
            $commit = self::commit([
                'token' => $token,
                'skip_profile' => 1,
            ]);
            if (!empty($commit['output']['valid'])) {
                return [
                    'output' => [
                        'valid' => true,
                        'needs_profile' => false,
                        'redirect' => Util::getPostParticipacionUrl(),
                        'message' => 'Participación guardada',
                    ],
                ];
            }
            return $commit;
        }

        return [
            'output' => [
                'valid' => true,
                'needs_profile' => true,
                'token' => $token,
                'redirect' => 'registro.php?token=' . urlencode($token),
            ],
        ];
    }

    public static function getDraft($token = null)
    {
        $draft = $_SESSION['participacion_draft'] ?? null;
        if (!is_array($draft)) {
            return null;
        }
        if (($draft['expires'] ?? 0) < time()) {
            unset($_SESSION['participacion_draft']);
            return null;
        }
        if ($token !== null && $token !== '' && ($draft['token'] ?? '') !== $token) {
            return null;
        }
        return $draft;
    }

    public static function commit($rqst)
    {
        $token = trim((string)($rqst['token'] ?? ''));
        $draft = self::getDraft($token !== '' ? $token : null);
        if (!$draft) {
            return Util::error_general('No hay una participación pendiente o expiró. Vuelve a responder el formulario.');
        }

        $deviceUuid = self::normalizeDeviceUuid($draft['device_uuid'] ?? '');
        $tipo = $draft['tipo'];
        $instrumentoId = (int)$draft['instrumento_id'];

        if (self::deviceAlreadyParticipated($tipo, $instrumentoId, $deviceUuid)) {
            unset($_SESSION['participacion_draft']);
            return Util::error_general('Este dispositivo ya participó en este formulario.');
        }

        $votante = self::findVotanteByDevice($deviceUuid);
        $skipProfile = !empty($rqst['skip_profile']) || $votante;

        if (!$votante) {
            $created = self::createSyntheticVotante($rqst, $draft);
            if (empty($created['output']['valid'])) {
                return $created;
            }
            $votanteId = (int)$created['output']['votante_id'];
        } else {
            $votanteId = (int)$votante['id'];
        }

        // Aplicar respuestas con sesión temporal de votante
        $prevSession = $_SESSION['session_user'] ?? null;
        $_SESSION['session_user'] = [
            'id' => $votanteId,
            'codigo_departamento' => $rqst['codigo_departamento']
                ?? ($draft['payload']['codigo_departamento'] ?? null)
                ?? (self::getGeoFromSession()['codigo_departamento'] ?? null)
                ?? ($votante['codigo_departamento'] ?? null),
            'codigo_municipio' => $rqst['codigo_municipio']
                ?? (self::getGeoFromSession()['codigo_municipio'] ?? null)
                ?? ($votante['codigo_municipio'] ?? null),
        ];
        // aliases usados por SessionData
        $_SESSION['session_user']['codigo_departamento'] = (string)$_SESSION['session_user']['codigo_departamento'];
        $_SESSION['session_user']['codigo_municipio'] = (string)$_SESSION['session_user']['codigo_municipio'];

        try {
            if ($tipo === 'encuesta') {
                $saveRqst = ['data' => json_encode($draft['payload'], JSON_UNESCAPED_UNICODE)];
                $result = RespuestaCuestionario::save($saveRqst);
                if (empty($result['output']['valid'])) {
                    throw new Exception($result['output']['response']['content'] ?? $result['output']['message'] ?? 'Error al guardar encuesta');
                }
            } else {
                $p = $draft['payload'];
                $result = Sondeo::registrarVoto([
                    'sondeo_id' => $p['sondeo_id'],
                    'valor' => $p['valor'],
                    'tipo' => $p['tipo'],
                    'pregunta_id' => $p['pregunta_id'] ?? null,
                ]);
                if (empty($result['output']['valid']) || ($result['status'] ?? '') === 'error') {
                    throw new Exception($result['message'] ?? 'Error al guardar sondeo');
                }
            }

            self::markDeviceParticipation(
                $tipo,
                $instrumentoId,
                $deviceUuid,
                $votanteId,
                $draft['device_fingerprint'] ?? ''
            );

            unset($_SESSION['participacion_draft']);

            // No dejar sesión de “cuenta”; solo marcar participación ok
            if ($prevSession === null) {
                unset($_SESSION['session_user']);
            } else {
                $_SESSION['session_user'] = $prevSession;
            }
            $_SESSION['participacion_gracias'] = [
                'ts' => time(),
                'tipo' => $tipo,
            ];

            return [
                'output' => [
                    'valid' => true,
                    'redirect' => Util::getPostParticipacionUrl(),
                    'message' => 'Participación guardada',
                    'skipped_profile' => (bool)$skipProfile,
                ],
            ];
        } catch (Exception $e) {
            if ($prevSession === null) {
                unset($_SESSION['session_user']);
            } else {
                $_SESSION['session_user'] = $prevSession;
            }
            return Util::error_general($e->getMessage());
        }
    }

    private static function createSyntheticVotante($rqst, $draft)
    {
        $deviceUuid = self::normalizeDeviceUuid($draft['device_uuid']);
        $geo = self::getGeoFromSession();

        $codigoDepto = trim((string)($rqst['codigo_departamento'] ?? $rqst['tbl_departamento_id'] ?? $geo['codigo_departamento'] ?? ''));
        $codigoMun = trim((string)($rqst['codigo_municipio'] ?? $rqst['tbl_municipio_id'] ?? $geo['codigo_municipio'] ?? ''));

        $ideologia = trim((string)($rqst['ideologia'] ?? ''));
        $rangoEdad = trim((string)($rqst['rango_edad'] ?? ''));
        $genero = trim((string)($rqst['genero'] ?? ''));
        $ocupacion = trim((string)($rqst['ocupacion'] ?? ''));
        $nivelEdu = trim((string)($rqst['nivel_educacion'] ?? ''));
        $barrio = trim((string)($rqst['barrio'] ?? ''));

        if ($ideologia === '' || $rangoEdad === '' || $genero === '' || $ocupacion === '') {
            return Util::error_missing_data_description('Completa los datos demográficos obligatorios.');
        }

        if ($codigoDepto === '') {
            $codigoDepto = '00';
        }
        if ($codigoMun === '') {
            $codigoMun = '000';
        }

        $suffix = substr(hash('sha256', $deviceUuid . microtime(true)), 0, 12);
        $email = 'anon_' . $suffix . '@no-reply.local';
        $passwordPlain = bin2hex(random_bytes(16));
        $passwordHash = Util::make_hash_pass(md5($passwordPlain));

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $q = "INSERT INTO " . $db->getTable('tbl_votantes') . "
                (email, username, nombre_completo, password, ideologia, rango_edad, genero,
                 codigo_departamento, codigo_municipio, barrio, nivel_educacion, ocupacion,
                 estado, habilitado, dtcreate, ip_registro, user_agent,
                 device_token, device_fingerprint, device_user_agent, device_platform, device_language, device_timezone)
                VALUES
                (:email, :username, :nombre, :password, :ideologia, :rango_edad, :genero,
                 :depto, :mun, :barrio, :nivel_edu, :ocupacion,
                 'activo', 'si', NOW(), :ip, :ua,
                 :device_token, :device_fp, :device_ua, :device_plat, :device_lang, :device_tz)";

            $stmt = $pdo->prepare($q);
            $stmt->execute([
                ':email' => $email,
                ':username' => $email,
                ':nombre' => 'Participante',
                ':password' => $passwordHash,
                ':ideologia' => $ideologia,
                ':rango_edad' => $rangoEdad,
                ':genero' => $genero,
                ':depto' => $codigoDepto,
                ':mun' => $codigoMun,
                ':barrio' => $barrio,
                ':nivel_edu' => $nivelEdu !== '' ? $nivelEdu : null,
                ':ocupacion' => $ocupacion,
                ':ip' => $_SERVER['REMOTE_ADDR'] ?? '',
                ':ua' => substr((string)($draft['device_user_agent'] ?? ''), 0, 255),
                ':device_token' => $deviceUuid,
                ':device_fp' => substr((string)($draft['device_fingerprint'] ?? ''), 0, 128),
                ':device_ua' => substr((string)($draft['device_user_agent'] ?? ''), 0, 255),
                ':device_plat' => substr((string)($draft['device_platform'] ?? ''), 0, 80),
                ':device_lang' => substr((string)($draft['device_language'] ?? ''), 0, 40),
                ':device_tz' => substr((string)($draft['device_timezone'] ?? ''), 0, 60),
            ]);

            $id = (int)$pdo->lastInsertId();
            $db->closeConect();

            return ['output' => ['valid' => true, 'votante_id' => $id, 'email' => $email]];
        } catch (Exception $e) {
            $db->closeConect();
            $msg = $e->getMessage();
            if (stripos($msg, 'device_token') !== false || stripos($msg, 'Duplicate') !== false) {
                $existing = self::findVotanteByDevice($deviceUuid);
                if ($existing) {
                    return ['output' => ['valid' => true, 'votante_id' => (int)$existing['id']]];
                }
            }
            return Util::error_general('No se pudieron guardar tus datos: ' . $msg);
        }
    }

    /**
     * Sondeos activos sin filtro geográfico (D7).
     */
    public static function listSondeosPublicos()
    {
        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $q = "SELECT s.*,
                        cp.nombre as nombre_cargo_publico
                FROM " . $db->getTable('tbl_sondeo') . " s
                LEFT JOIN " . $db->getTable('tbl_cargos_publicos') . " cp
                    ON s.tbl_cargo_publico_id = cp.id
                WHERE s.habilitado = 'si'
                ORDER BY s.dtcreate DESC";
            $arr = $pdo->query($q)->fetchAll(PDO::FETCH_ASSOC);
            $arr = array_values(array_filter($arr, function ($sondeo) {
                return Sondeo::isVigente($sondeo['fecha_inicio'] ?? null, $sondeo['fecha_fin'] ?? null);
            }));

            foreach ($arr as $key => $value) {
                $qSondeoCandidato = "SELECT 
                    p.id, p.tbl_cargo_publico_id, p.nombre_completo, p.codigo_departamento, p.codigo_municipio, p.dtcreate, p.foto, p.habilitado,
                    cp.nombre AS cargo_publico, cp.sigla AS sigla_cargo,
                    d.departamento AS nombre_departamento,
                    c.municipio AS nombre_municipio,
                    GROUP_CONCAT(pxp.tbl_partido_politico_id) AS partidoPoliticoIds,
                    GROUP_CONCAT(pp.nombre_partido SEPARATOR ', ') AS nombres_partidos
                FROM " . $db->getTable('tbl_participantes') . " p
                INNER JOIN " . $db->getTable('tbl_sondeo_x_tbl_participantes') . " sxp
                    ON p.id = sxp.tbl_participante_id
                LEFT JOIN " . $db->getTable('tbl_participantes_x_partidos_politicos') . " pxp
                    ON p.id = pxp.tbl_participante_id
                LEFT JOIN " . $db->getTable('tbl_partidos_politicos') . " pp
                    ON pxp.tbl_partido_politico_id = pp.id
                LEFT JOIN " . $db->getTable('tbl_cargos_publicos') . " cp
                    ON p.tbl_cargo_publico_id = cp.id
                LEFT JOIN " . $db->getTable('tbl_departamentos') . " d
                    ON p.codigo_departamento = d.codigo_departamento
                LEFT JOIN " . $db->getTable('tbl_ciudades') . " c
                    ON p.codigo_municipio = c.codigo_muncipio
                WHERE sxp.tbl_sondeo_id = :id
                GROUP BY p.id";

                $stmtSondeoCandidato = $pdo->prepare($qSondeoCandidato);
                $stmtSondeoCandidato->execute([':id' => $value['id']]);
                $arr[$key]['candidatos'] = $stmtSondeoCandidato->fetchAll(PDO::FETCH_ASSOC);

                // Opciones si/no si aplica
                $qOpc = "SELECT * FROM " . $db->getTable('tbl_sondeo_x_opciones') . " WHERE tbl_sondeo_id = :id";
                try {
                    $stO = $pdo->prepare($qOpc);
                    $stO->execute([':id' => $value['id']]);
                    $arr[$key]['opciones'] = $stO->fetchAll(PDO::FETCH_ASSOC);
                } catch (Exception $e) {
                    $arr[$key]['opciones'] = [];
                }
            }

            $db->closeConect();
            return $arr;
        } catch (Exception $e) {
            $db->closeConect();
            return [];
        }
    }

    public static function checkDeviceStatus($rqst)
    {
        $deviceUuid = self::normalizeDeviceUuid($rqst['device_uuid'] ?? '');
        if ($deviceUuid === '') {
            return Util::error_missing_data_description('Dispositivo no identificado');
        }

        $votante = self::findVotanteByDevice($deviceUuid);
        $doneEncuesta = [];
        $doneSondeo = [];

        self::ensureSchema();
        try {
            $db = new DbConection();
            $pdo = $db->openConect();
            $tbl = $db->getTable(self::TABLE_DEVICE);
            $stmt = $pdo->prepare(
                "SELECT tipo_instrumento, instrumento_id FROM $tbl WHERE device_uuid = :du"
            );
            $stmt->execute([':du' => $deviceUuid]);
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                if ($row['tipo_instrumento'] === 'sondeo') {
                    $doneSondeo[] = (int)$row['instrumento_id'];
                } else {
                    $doneEncuesta[] = (int)$row['instrumento_id'];
                }
            }

            if ($votante) {
                $vid = (int)$votante['id'];
                $st = $pdo->prepare(
                    "SELECT DISTINCT tbl_sondeo_id FROM " . $db->getTable('tbl_respuestas_sondeos') . " WHERE tbl_votante_id = ?"
                );
                $st->execute([$vid]);
                $doneSondeo = array_values(array_unique(array_merge($doneSondeo, array_map('intval', $st->fetchAll(PDO::FETCH_COLUMN)))));

                $st2 = $pdo->prepare(
                    "SELECT DISTINCT tbl_ficha_tecnica_encuesta_id FROM " . $db->getTable('tbl_cuestionario_intentos') . " WHERE tbl_votante_id = ?"
                );
                $st2->execute([$vid]);
                $doneEncuesta = array_values(array_unique(array_merge($doneEncuesta, array_map('intval', $st2->fetchAll(PDO::FETCH_COLUMN)))));
            }
            $db->closeConect();
        } catch (Exception $e) {
            // ignore
        }

        return [
            'output' => [
                'valid' => true,
                'has_profile' => (bool)$votante,
                'votante_id' => $votante ? (int)$votante['id'] : 0,
                'done_encuesta' => $doneEncuesta,
                'done_sondeo' => $doneSondeo,
                'geo' => self::getGeoFromSession(),
            ],
        ];
    }
}
