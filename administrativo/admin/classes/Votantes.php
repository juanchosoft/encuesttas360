<?php
class Votantes
{
    public function __construct() {}

    public static function getAll($rqst)
    {
        $id = isset($rqst['id']) ? intval($rqst['id']) : 0;
        $db = new DbConection();
        $pdo = $db->openConect();
        $q = "SELECT
                v.*,
                CASE
                    WHEN u.tipo = 'Encuestador' THEN 'Encuestado'
                    WHEN v.tbl_usuario_id IS NULL OR v.tbl_usuario_id = 0 THEN 'Autoregistro'
                    ELSE 'Registro interno'
                END AS tipo_registro,
                u.id AS encuestador_id,
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
                END AS encuestador_nombre_completo
              FROM " . $db->getTable('tbl_votantes') . " v
              LEFT JOIN " . $db->getTable('tbl_usuarios') . " u
                ON v.tbl_usuario_id = u.id";
        $params = [];
        if ($id > 0) {
            $q .= " WHERE v.id = :id";
            $params[':id'] = $id;
        }
        $q .= " ORDER BY v.id DESC";
        try {
            $stmt = $pdo->prepare($q);
            $stmt->execute($params);
            $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $arrjson = array('output' => array('valid' => true, 'response' => $arr ? $arr : []));
        } catch (PDOException $e) {
            $arrjson = Util::error_general('Al obtener los datos de Votantes.');
        } finally {
            $db->closeConect();
        }
        return $arrjson;
    }

//    ******************* INICIO FUNCION GUARDAR*********************

public static function save($rqst)
{
    require_once __DIR__ . '/cuestionario/DefinicionCuestionario.php';
    require_once __DIR__ . '/cuestionario/MotorReglasCuestionario.php';

    $id = isset($rqst['id']) ? (int)$rqst['id'] : 0;

    $tbl_usuario_id_actual = (int)($_SESSION['session_user']['id'] ?? 0);
    $tbl_usuario_id = $tbl_usuario_id_actual;

    $nombre_completo      = isset($rqst['nombre_completo']) ? trim((string)$rqst['nombre_completo']) : '';
    $ideologia            = isset($rqst['ideologia']) ? trim((string)$rqst['ideologia']) : '';
    $rango_edad           = isset($rqst['rango_edad']) ? trim((string)$rqst['rango_edad']) : '';
    $nivel_ingresos       = isset($rqst['nivel_ingresos']) ? trim((string)$rqst['nivel_ingresos']) : '';
    $nivel_ingresos       = $nivel_ingresos !== '' ? $nivel_ingresos : null;

    $email    = !empty($rqst['email']) ? trim((string)$rqst['email']) : null;
    $username = !empty($rqst['username']) ? trim((string)$rqst['username']) : null;
    $password = !empty($rqst['password']) ? (string)$rqst['password'] : null;

    $genero             = isset($rqst['genero']) ? trim((string)$rqst['genero']) : '';
    $codigo_departamento= isset($rqst['codigo_departamento']) ? trim((string)$rqst['codigo_departamento']) : '';
    $codigo_municipio   = isset($rqst['codigo_municipio']) ? trim((string)$rqst['codigo_municipio']) : '';
    $comuna             = isset($rqst['comuna']) ? trim((string)$rqst['comuna']) : null;
    $barrio             = isset($rqst['barrio']) ? trim((string)$rqst['barrio']) : null;
    $nivel_educacion    = isset($rqst['nivel_educacion']) ? trim((string)$rqst['nivel_educacion']) : '';
    $ocupacion          = isset($rqst['ocupacion']) ? trim((string)$rqst['ocupacion']) : '';
    $estado             = isset($rqst['estado']) ? trim((string)$rqst['estado']) : 'activo';

    $cuestionario_id = isset($rqst['cuestionario_id']) ? (int)$rqst['cuestionario_id'] : 0;
    $es_prueba = !empty($rqst['es_prueba']) ? 1 : 0;
    if ($es_prueba === 1 && !SessionData::hasPermission('encuestas.preguntas.reglas')) {
        return Util::error_general('No tiene permiso para probar el cuestionario.');
    }

    $ip_registro = Util::get_real_ipaddress();
    $user_agent  = $_SERVER['HTTP_USER_AGENT'] ?? '';

    $validar = isset($rqst['validar']) ? trim((string)$rqst['validar']) : 'no';

    if ($id > 0 && $tbl_usuario_id_actual <= 0) {
        return Util::error_missing_data_description('Sesión inválida: no se detectó usuario.');
    }

    if (empty($username) && !empty($email)) {
        $username = $email;
    }

    if (empty($nombre_completo) && !empty($username)) {
        $nombre_completo = $username;
    }
    if ($nombre_completo === '' && $cuestionario_id > 0) {
        $nombre_completo = 'Encuestado';
    }

    if ($nombre_completo === '') return Util::error_missing_data_description('El campo "Nombre completo" es requerido.');
    if ($codigo_departamento === '') return Util::error_missing_data_description('El campo "Código del departamento" es requerido.');
    if ($codigo_municipio === '')    return Util::error_missing_data_description('El campo "Código del municipio" es requerido.');
    if ($estado === '')              return Util::error_missing_data_description('El campo "Estado de la cuenta" es requerido.');

    if ($cuestionario_id <= 0) {
        if ($ideologia === '')  return Util::error_missing_data_description('El campo "Ideología política" es requerido.');
        if ($rango_edad === '') return Util::error_missing_data_description('El campo "Rango de edad" es requerido.');
        if ($genero === '')     return Util::error_missing_data_description('El campo "Género" es requerido.');
    }

    if ($password !== null && strlen($password) > 2) {
        $password = Util::make_hash_pass($password);
    } else {
        $password = null;
    }

    if ($validar === 'si') {
        if (!empty($password) && empty($username)) {
            return Util::error_missing_data_description('El campo "Username" es requerido.');
        }
        if (!empty($username) && strlen($username) < 4) {
            return Util::error_missing_data_description('El campo "Username" debe tener al menos 4 caracteres.');
        }
        if (!Votantes::available(['username' => $username])) {
            return Util::error_missing_data_description('El campo "Username" ya existe.');
        }
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return Util::error_missing_data_description('El campo "Correo electrónico" es inválido.');
        }
    }

    $db  = new DbConection();
    $pdo = $db->openConect();

    try {
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $client_uuid = isset($rqst['client_uuid']) ? trim((string)$rqst['client_uuid']) : '';
        if ($client_uuid !== '' && !preg_match('/^[a-fA-F0-9\-]{8,36}$/', $client_uuid)) {
            $client_uuid = '';
        }

        if ($id === 0 && $client_uuid !== '') {
            $previo = self::respuestaIdempotente($pdo, $db, $client_uuid, $cuestionario_id, $tbl_usuario_id_actual);
            if ($previo !== null) {
                return $previo;
            }
        }

        $defCuestionario = null;
        $valMotor = null;

        if ($id === 0 && $cuestionario_id > 0) {
            $defCuestionario = DefinicionCuestionario::cargar($pdo, $cuestionario_id, $db->getDbName());
            if (!$defCuestionario) {
                return Util::error_missing_data_description('No se encontró el cuestionario seleccionado.');
            }

            $versionCliente = isset($rqst['definicion_hash'])
                ? trim((string)$rqst['definicion_hash'])
                : (isset($rqst['version']) ? trim((string)$rqst['version']) : '');
            if ($versionCliente !== (string)$defCuestionario['version']) {
                return Util::error_missing_data_description(
                    'El cuestionario fue actualizado recientemente. Por favor recargue la página para cargar la versión vigente.'
                );
            }

            $respuestas_json = $rqst['cuestionario_respuestas'] ?? '';
            $respuestasRaw = is_array($respuestas_json)
                ? $respuestas_json
                : (is_string($respuestas_json) && $respuestas_json !== '' ? json_decode($respuestas_json, true) : []);
            if (!is_array($respuestasRaw)) {
                return Util::error_missing_data_description('El formato de las respuestas del cuestionario no es válido.');
            }

            $valMotor = MotorReglasCuestionario::validar($defCuestionario, $respuestasRaw, [
                'codigo_municipio' => $codigo_municipio,
                'codigo_departamento' => $codigo_departamento,
            ]);

            if (empty($valMotor['valido'])) {
                $primerError = $valMotor['errores'][0]['mensaje'] ?? 'Faltan respuestas obligatorias en el cuestionario.';
                return [
                    'output' => [
                        'valid' => false,
                        'errores' => $valMotor['errores'],
                        'response' => [
                            'content' => $primerError
                        ]
                    ]
                ];
            }

            $camposEquiv = self::extraerEquivalenciasVotante($defCuestionario, $valMotor['respuestas_normalizadas']);
            if (!empty($camposEquiv['genero']))          $genero = $camposEquiv['genero'];
            if (!empty($camposEquiv['rango_edad']))      $rango_edad = $camposEquiv['rango_edad'];
            if (!empty($camposEquiv['ideologia']))       $ideologia = $camposEquiv['ideologia'];
            if (!empty($camposEquiv['nivel_ingresos']))  $nivel_ingresos = $camposEquiv['nivel_ingresos'];
            if (!empty($camposEquiv['nivel_educacion'])) $nivel_educacion = $camposEquiv['nivel_educacion'];
            if (!empty($camposEquiv['ocupacion']))       $ocupacion = $camposEquiv['ocupacion'];

            if ($es_prueba === 1) {
                return [
                    'output' => [
                        'valid' => true,
                        'es_prueba' => true,
                        'simulado' => true,
                        'estado_intento' => $valMotor['estado_intento'],
                        'valido_para_pago' => $valMotor['valido_para_pago'],
                        'filas_normalizadas' => count($valMotor['filas_bd']),
                        'response' => 0,
                        'intento_id' => 0
                    ]
                ];
            }
        }

        if ($id === 0 && $defCuestionario && $valMotor
            && (int)($defCuestionario['ficha']['bloquear_cuota_completa'] ?? 0) === 1
            && ($valMotor['estado_intento'] ?? '') !== 'terminado_filtro'
            && !empty($rqst['tbl_zona_muestreo_id'])
            && self::zonaConMetaCumplida($pdo, $db, $cuestionario_id, (int)$rqst['tbl_zona_muestreo_id'])) {
            return Util::error_missing_data_description('La meta de esta zona ya se cumplió. Escoja otra zona.');
        }

        $ideologiasValidas = ['izquierda', 'centro_izquierda', 'centro', 'centro_derecha', 'derecha', 'independiente', 'sin_definir'];
        if (!in_array($ideologia, $ideologiasValidas, true)) {
            $ideologia = null;
        }
        $edadesValidas = ['18-25', '26-35', '36-45', '46-55', '56-65', '66+'];
        if (!in_array($rango_edad, $edadesValidas, true)) {
            $rango_edad = null;
        }
        $generosValidos = ['masculino', 'femenino', 'otro', 'prefiero_no_decir'];
        if (!in_array($genero, $generosValidos, true)) {
            $genero = null;
        }
        $ingresosValidos = ['menos_1_salario', '1-2_salarios', '3-5_salarios', '6-10_salarios', 'mas_10_salarios'];
        if ($nivel_ingresos !== null && !in_array($nivel_ingresos, $ingresosValidos, true)) {
            $nivel_ingresos = null;
        }
        $educacionValida = ['primaria_incompleta', 'primaria_completa', 'secundaria_incompleta', 'secundaria_completa', 'tecnico', 'tecnologo', 'universitario_incompleto', 'universitario_completo', 'posgrado'];
        if ($nivel_educacion !== '' && !in_array($nivel_educacion, $educacionValida, true)) {
            $nivel_educacion = null;
        } elseif ($nivel_educacion === '') {
            $nivel_educacion = null;
        }

        if (!empty($rqst['tbl_zona_muestreo_id']) && ($comuna === null || $comuna === '')) {
            $stZona = $pdo->prepare("SELECT nombre FROM " . $db->getTable('tbl_zonas_muestreo') . " WHERE id = :zid LIMIT 1");
            $stZona->execute([':zid' => (int)$rqst['tbl_zona_muestreo_id']]);
            $nomZona = $stZona->fetchColumn();
            if ($nomZona) {
                $comuna = (string)$nomZona;
            }
        }

        $pdo->beginTransaction();

        $table = $db->getTable('tbl_votantes');

        if ($id > 0) {
            $stmtVotanteActual = $pdo->prepare("SELECT tbl_usuario_id FROM {$table} WHERE id = :id LIMIT 1");
            $stmtVotanteActual->execute([':id' => $id]);
            $votanteActual = $stmtVotanteActual->fetch(PDO::FETCH_ASSOC);

            if (!$votanteActual) {
                throw new Exception("No existe el registro con id={$id} en tbl_votantes.");
            }

            $tbl_usuario_id_existente = isset($votanteActual['tbl_usuario_id']) ? (int)$votanteActual['tbl_usuario_id'] : 0;
            if ($tbl_usuario_id_existente > 0) {
                $tbl_usuario_id = $tbl_usuario_id_existente;
            }
        }

        if ($id > 0) {
            $sql = "
                UPDATE {$table} SET
                    tbl_usuario_id       = :tbl_usuario_id,
                    nombre_completo      = :nombre_completo,
                    ideologia            = :ideologia,
                    rango_edad           = :rango_edad,
                    nivel_ingresos       = :nivel_ingresos,
                    email                = :email,
                    username             = :username,
                    genero               = :genero,
                    codigo_departamento  = :codigo_departamento,
                    codigo_municipio     = :codigo_municipio,
                    comuna               = :comuna,
                    barrio               = :barrio,
                    nivel_educacion      = :nivel_educacion,
                    ocupacion            = :ocupacion,
                    estado               = :estado,
                    dtupdate             = :dtupdate
                    " . ($password !== null ? ", password = :password" : "") . "
                WHERE id = :id
            ";

            $stmt = $pdo->prepare($sql);

            $params = [
                ':id'                 => $id,
                ':tbl_usuario_id'     => $tbl_usuario_id,
                ':nombre_completo'    => $nombre_completo,
                ':ideologia'          => $ideologia,
                ':rango_edad'         => $rango_edad,
                ':nivel_ingresos'     => $nivel_ingresos,
                ':email'              => $email,
                ':username'           => $username,
                ':genero'             => $genero,
                ':codigo_departamento'=> $codigo_departamento,
                ':codigo_municipio'   => $codigo_municipio,
                ':comuna'             => $comuna,
                ':barrio'             => $barrio,
                ':nivel_educacion'    => $nivel_educacion,
                ':ocupacion'          => $ocupacion,
                ':estado'             => $estado,
                ':dtupdate'           => Util::date(),
            ];

            if ($password !== null) $params[':password'] = $password;

            $stmt->execute($params);

            if ($stmt->rowCount() === 0) {
                $chk = $pdo->prepare("SELECT id FROM {$table} WHERE id = :id LIMIT 1");
                $chk->execute([':id' => $id]);
                if (!$chk->fetchColumn()) {
                    throw new Exception("No existe el registro con id={$id} en tbl_votantes.");
                }
            }

            $arrjson = ['output' => ['valid' => true, 'id' => $id]];
        } else {
            $sql = "
                INSERT INTO {$table}
                (dtcreate, tbl_usuario_id, nombre_completo, ideologia, rango_edad, nivel_ingresos, email, username, password, genero, codigo_departamento, codigo_municipio, comuna, barrio, nivel_educacion, ocupacion, estado, ip_registro, user_agent)
                VALUES
                (:dtcreate, :tbl_usuario_id, :nombre_completo, :ideologia, :rango_edad, :nivel_ingresos, :email, :username, :password, :genero, :codigo_departamento, :codigo_municipio, :comuna, :barrio, :nivel_educacion, :ocupacion, :estado, :ip_registro, :user_agent)
            ";

            $stmt = $pdo->prepare($sql);

            $params = [
                ':dtcreate'            => Util::date(),
                ':tbl_usuario_id'      => $tbl_usuario_id > 0 ? $tbl_usuario_id : null,
                ':nombre_completo'     => $nombre_completo,
                ':ideologia'           => $ideologia,
                ':rango_edad'          => $rango_edad,
                ':nivel_ingresos'      => $nivel_ingresos,
                ':email'               => $email,
                ':username'            => $username,
                ':password'            => $password,
                ':genero'              => $genero,
                ':codigo_departamento' => $codigo_departamento,
                ':codigo_municipio'    => $codigo_municipio,
                ':comuna'              => $comuna,
                ':barrio'              => $barrio,
                ':nivel_educacion'     => $nivel_educacion,
                ':ocupacion'           => $ocupacion !== '' ? $ocupacion : null,
                ':estado'              => $estado,
                ':ip_registro'         => $ip_registro,
                ':user_agent'          => $user_agent,
            ];

            $stmt->execute($params);

            if ($stmt->rowCount() <= 0) {
                throw new Exception("INSERT no afectó filas (rowCount=0). Revisa constraints/triggers/tabla real.");
            }

            $id = (int)$pdo->lastInsertId();
            if ($id <= 0) {
                throw new Exception("INSERT ejecutó pero lastInsertId() retornó vacío. Revisa motor/PK autoincrement.");
            }

            $intentoId = 0;
            if (SessionData::encuestador() || !empty($rqst['cuestionario_id']) || !empty($rqst['sondeo_id'])) {
                Votantes::guardarRespuestaSondeo($rqst, $id, $pdo, $db);
                $intentoId = (int)Votantes::guardarRespuestasCuestionario($rqst, $id, $pdo, $db, $defCuestionario, $valMotor);
            }
            $arrjson = [
                'output' => [
                    'valid' => true,
                    'response' => $id,
                    'intento_id' => $intentoId > 0 ? $intentoId : null,
                    'estado_intento' => $valMotor ? $valMotor['estado_intento'] : null,
                    'valido_para_pago' => $valMotor ? (int)$valMotor['valido_para_pago'] : null,
                ]
            ];
        }

        $pdo->commit();
        return $arrjson;

    } catch (Throwable $e) {
        if ($pdo && $pdo->inTransaction()) $pdo->rollBack();

        $msgTecnico = $e->getMessage();
        error_log('Votantes::save ' . $msgTecnico);
        $msg = 'No fue posible guardar la encuesta. Intente de nuevo; si el problema continúa, avise al administrador.';
        if (strpos($msgTecnico, '1062') !== false || strpos($msgTecnico, 'Duplicate entry') !== false) {
            if (strpos($msgTecnico, 'client_uuid') !== false && !empty($client_uuid)) {
                $previo = self::respuestaIdempotente($pdo, $db, $client_uuid, $cuestionario_id, $tbl_usuario_id_actual);
                if ($previo !== null) {
                    return $previo;
                }
            }
            if (strpos($msgTecnico, 'email') !== false) {
                $msg = 'El correo electrónico ya está registrado. Usa uno diferente.';
            } elseif (strpos($msgTecnico, 'username') !== false) {
                $msg = 'El nombre de usuario ya está en uso. Elige otro.';
            } else {
                $msg = 'Ya existe un registro con esos datos. Verifica el correo o usuario.';
            }
        }

        return [
            'output' => [
                'valid' => false,
                'response' => [
                    'content' => $msg
                ]
            ]
        ];
    } finally {
        $db->closeConect();
    }
}

public static function catalogoCamposEquivalencia(): array
{
    return [
        'genero' => ['etiqueta' => 'Género', 'valores' => [
            'masculino' => 'Masculino', 'femenino' => 'Femenino', 'otro' => 'Otro', 'prefiero_no_decir' => 'Prefiere no decir',
        ]],
        'rango_edad' => ['etiqueta' => 'Rango de edad', 'valores' => [
            '18-25' => '18 a 25 años', '26-35' => '26 a 35 años', '36-45' => '36 a 45 años',
            '46-55' => '46 a 55 años', '56-65' => '56 a 65 años', '66+' => '66 años o más',
        ]],
        'ideologia' => ['etiqueta' => 'Ideología', 'valores' => [
            'izquierda' => 'Izquierda', 'centro_izquierda' => 'Centro izquierda', 'centro' => 'Centro',
            'centro_derecha' => 'Centro derecha', 'derecha' => 'Derecha', 'independiente' => 'Independiente', 'sin_definir' => 'Sin definir',
        ]],
        'nivel_ingresos' => ['etiqueta' => 'Nivel de ingresos', 'valores' => [
            'menos_1_salario' => 'Menos de 1 salario mínimo', '1-2_salarios' => '1 a 2 salarios mínimos', '3-5_salarios' => '3 a 5 salarios mínimos',
            '6-10_salarios' => '6 a 10 salarios mínimos', 'mas_10_salarios' => 'Más de 10 salarios mínimos',
        ]],
        'nivel_educacion' => ['etiqueta' => 'Nivel educativo', 'valores' => [
            'primaria_incompleta' => 'Primaria incompleta', 'primaria_completa' => 'Primaria completa',
            'secundaria_incompleta' => 'Bachillerato incompleto', 'secundaria_completa' => 'Bachillerato completo',
            'tecnico' => 'Técnico', 'tecnologo' => 'Tecnólogo', 'universitario_incompleto' => 'Universitario incompleto',
            'universitario_completo' => 'Universitario completo', 'posgrado' => 'Posgrado',
        ]],
        'ocupacion' => ['etiqueta' => 'Ocupación (copia el texto de la respuesta)', 'valores' => []],
    ];
}

private static function respuestaIdempotente($pdo, $db, string $clientUuid, int $fichaId, int $usuarioId): ?array
{
    $st = $pdo->prepare(
        "SELECT id, tbl_votante_id, tbl_ficha_tecnica_encuesta_id, tbl_usuario_encuestador_id, estado, valido_para_pago FROM "
        . $db->getTable('tbl_cuestionario_intentos') . " WHERE client_uuid = :uuid LIMIT 1"
    );
    $st->execute([':uuid' => $clientUuid]);
    $previo = $st->fetch(PDO::FETCH_ASSOC);
    if (!$previo) {
        return null;
    }
    $mismaFicha = (int)$previo['tbl_ficha_tecnica_encuesta_id'] === $fichaId;
    $mismoUsuario = (int)($previo['tbl_usuario_encuestador_id'] ?? 0) === $usuarioId;
    if (!$mismaFicha || !$mismoUsuario) {
        return Util::error_general('Este envío ya fue registrado por otra encuesta. Recargue la página e intente de nuevo.');
    }
    return [
        'output' => [
            'valid' => true,
            'response' => (int)$previo['tbl_votante_id'],
            'intento_id' => (int)$previo['id'],
            'estado_intento' => $previo['estado'],
            'valido_para_pago' => (int)$previo['valido_para_pago'],
            'idempotent' => true
        ]
    ];
}

public static function extraerEquivalenciasVotante(array $def, array $respuestasNormalizadas): array
{
    require_once __DIR__ . '/cuestionario/EquivalenciasVotante.php';
    return EquivalenciasVotante::extraer($def, $respuestasNormalizadas);
}

    private static function guardarRespuestaSondeo($rqst, $votanteId, $pdo, $db)
    {
        $sondeo_id = isset($rqst['sondeo_id']) ? intval($rqst['sondeo_id']) : 0;
        $sondeo_opcion_id = isset($rqst['sondeo_opcion_id']) ? intval($rqst['sondeo_opcion_id']) : 0;
        $sondeo_candidato_id = isset($rqst['sondeo_candidato_id']) ? intval($rqst['sondeo_candidato_id']) : 0;

        $sondeo_tipo = null;

        if ($sondeo_id <= 0) {
            return;
        }

        if ($sondeo_opcion_id > 0) {
            $sondeo_tipo = 'no';
        }

        if ($sondeo_candidato_id > 0) {
            $sondeo_tipo = 'si';
        }

        $candidato_id_insert = null;
        $valor = null;

        if ($sondeo_tipo === 'si') {
            $candidato_id = isset($rqst['sondeo_candidato_id']) ? intval($rqst['sondeo_candidato_id']) : 0;
            if ($candidato_id <= 0) {
                return;
            }
            $candidato_id_insert = $candidato_id;
            $valor = $candidato_id;
        } else {
            $opcion_id = isset($rqst['sondeo_opcion_id']) ? intval($rqst['sondeo_opcion_id']) : 0;
            $valor = $opcion_id;
            if ($opcion_id <= 0) {
                return;
            }
        }

        $codigo_departamento = isset($rqst['codigo_departamento']) ? trim($rqst['codigo_departamento']) : '';
        $codigo_municipio = isset($rqst['codigo_municipio']) ? trim($rqst['codigo_municipio']) : '';

        $qRespuesta = "INSERT INTO " . $db->getTable('tbl_respuestas_sondeos') . "
                       (tbl_sondeo_id, tbl_sondeo_x_opciones_id, tbl_votante_id,
                        codigo_departamento, codigo_municipio,
                        tbl_candidato_id, tbl_respuesta_texto, dtcreate)
                       VALUES (:tbl_sondeo_id, :tbl_sondeo_x_opciones_id, :tbl_votante_id,
                               :codigo_departamento, :codigo_municipio,
                               :tbl_candidato_id, :tbl_respuesta_texto, :dtcreate)";

        $stmtRespuesta = $pdo->prepare($qRespuesta);
        $stmtRespuesta->execute([
            ':tbl_sondeo_id' => $sondeo_id,
            ':tbl_sondeo_x_opciones_id' => $valor,
            ':tbl_votante_id' => $votanteId,
            ':codigo_departamento' => $codigo_departamento,
            ':codigo_municipio' => $codigo_municipio,
            ':tbl_candidato_id' => $candidato_id_insert,
            ':tbl_respuesta_texto' => $valor,
            ':dtcreate' => Util::date()
        ]);
    }

    private static function zonaConMetaCumplida($pdo, $db, int $fichaId, int $zonaId): bool
    {
        $st = $pdo->prepare("SELECT cuota FROM " . $db->getTable('tbl_zonas_muestreo')
            . " WHERE id = :zid AND tbl_ficha_tecnica_encuesta_id = :fid AND eliminado = 0 LIMIT 1");
        $st->execute([':zid' => $zonaId, ':fid' => $fichaId]);
        $cuota = (int)$st->fetchColumn();
        if ($cuota <= 0) {
            return false;
        }
        $st = $pdo->prepare("SELECT COUNT(*) FROM " . $db->getTable('tbl_cuestionario_intentos')
            . " WHERE tbl_ficha_tecnica_encuesta_id = :fid AND tbl_zona_muestreo_id = :zid AND es_prueba = 0 AND estado IN ('completo','pendiente_audio')");
        $st->execute([':fid' => $fichaId, ':zid' => $zonaId]);
        return (int)$st->fetchColumn() >= $cuota;
    }

    private static function guardarRespuestasCuestionario($rqst, $votanteId, $pdo, $db, ?array $defCuestionario = null, ?array $valMotor = null)
    {
        $cuestionario_id = isset($rqst['cuestionario_id']) ? intval($rqst['cuestionario_id']) : 0;
        if ($cuestionario_id <= 0) {
            return 0;
        }

        if (!$defCuestionario) {
            $defCuestionario = DefinicionCuestionario::cargar($pdo, $cuestionario_id, $db->getDbName());
        }
        if (!$defCuestionario) {
            return 0;
        }

        if (!$valMotor) {
            $respuestas_json = $rqst['cuestionario_respuestas'] ?? '';
            $respuestasRaw = is_array($respuestas_json)
                ? $respuestas_json
                : (is_string($respuestas_json) && $respuestas_json !== '' ? json_decode($respuestas_json, true) : []);
            if (!is_array($respuestasRaw)) {
                return 0;
            }
            $valMotor = MotorReglasCuestionario::validar($defCuestionario, $respuestasRaw, [
                'codigo_municipio' => isset($rqst['codigo_municipio']) ? trim((string)$rqst['codigo_municipio']) : '',
                'codigo_departamento' => isset($rqst['codigo_departamento']) ? trim((string)$rqst['codigo_departamento']) : '',
            ]);
        }

        $client_uuid = isset($rqst['client_uuid']) ? trim((string)$rqst['client_uuid']) : '';
        if ($client_uuid === '' || !preg_match('/^[a-fA-F0-9\-]{8,36}$/', $client_uuid)) {
            $client_uuid = null;
        }
        $encuestadorId = (int)($_SESSION['session_user']['id'] ?? 0);
        $codigoDepto = isset($rqst['codigo_departamento']) ? trim((string)$rqst['codigo_departamento']) : null;
        $codigoMpio  = isset($rqst['codigo_municipio']) ? trim((string)$rqst['codigo_municipio']) : null;
        $zonaId      = !empty($rqst['tbl_zona_muestreo_id']) ? (int)$rqst['tbl_zona_muestreo_id'] : null;
        $zonaTexto   = isset($rqst['zona_texto'])
            ? trim((string)$rqst['zona_texto'])
            : (isset($rqst['comuna']) && trim((string)$rqst['comuna']) !== '' ? trim((string)$rqst['comuna']) : (isset($rqst['barrio']) ? trim((string)$rqst['barrio']) : null));
        $esPrueba    = !empty($rqst['es_prueba']) ? 1 : 0;

        $estadoMotor = $valMotor['estado_intento'] ?? 'completo';
        $audioObligatorio = (int)($defCuestionario['ficha']['audio_obligatorio'] ?? 1) === 1;

        if ($estadoMotor === 'terminado_filtro') {
            $estadoIntento = 'terminado_filtro';
            $validoParaPago = 0;
        } elseif ($esPrueba === 1 || !$audioObligatorio) {
            $estadoIntento = 'completo';
            $validoParaPago = 1;
        } else {
            $estadoIntento = 'pendiente_audio';
            $validoParaPago = 1;
        }

        if ($zonaId !== null) {
            $stZonaValida = $pdo->prepare(
                "SELECT COUNT(*) FROM " . $db->getTable('tbl_zonas_muestreo')
                . " WHERE id = :zid AND tbl_ficha_tecnica_encuesta_id = :fid AND (codigo_municipio = :mpio OR codigo_municipio IS NULL OR codigo_municipio = '')"
            );
            $stZonaValida->execute([':zid' => $zonaId, ':fid' => $cuestionario_id, ':mpio' => (string)$codigoMpio]);
            if ((int)$stZonaValida->fetchColumn() === 0) {
                $zonaId = null;
            }
        }

        $qIntento = "INSERT INTO " . $db->getTable('tbl_cuestionario_intentos') . "
                     (tbl_ficha_tecnica_encuesta_id, tbl_votante_id, estado, terminado_en_pregunta_id, terminado_opcion_id,
                      valido_para_pago, origen, codigo_departamento, codigo_municipio, tbl_zona_muestreo_id, zona_texto,
                      tbl_usuario_encuestador_id, client_uuid, motor_version, definicion_hash, es_prueba, fecha_respuesta, dtcreate)
                     VALUES (:ficha_tecnica_id, :tbl_votante_id, :estado, :term_preg_id, :term_opt_id,
                             :valido_pago, 'interno', :depto, :mpio, :zona_id, :zona_texto,
                             :encuestador_id, :client_uuid, :motor_ver, :def_hash, :es_prueba, NOW(), NOW())";

        $stmtIntento = $pdo->prepare($qIntento);
        $stmtIntento->execute([
            ':ficha_tecnica_id' => $cuestionario_id,
            ':tbl_votante_id' => $votanteId,
            ':estado' => $estadoIntento,
            ':term_preg_id' => $valMotor['terminado_en_pregunta_id'] ?? null,
            ':term_opt_id' => $valMotor['terminado_opcion_id'] ?? null,
            ':valido_pago' => $validoParaPago,
            ':depto' => $codigoDepto !== '' ? $codigoDepto : null,
            ':mpio' => $codigoMpio !== '' ? $codigoMpio : null,
            ':zona_id' => $zonaId,
            ':zona_texto' => $zonaTexto !== '' ? $zonaTexto : null,
            ':encuestador_id' => $encuestadorId > 0 ? $encuestadorId : null,
            ':client_uuid' => $client_uuid,
            ':motor_ver' => $defCuestionario['motor_version'] ?? MotorReglasCuestionario::MOTOR_VERSION,
            ':def_hash' => $defCuestionario['version'] ?? null,
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

        foreach ($valMotor['filas_bd'] ?? [] as $fila) {
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

        return $intentoId;
    }

    public static function available($rqst)
    {
        $fieldValue = isset($rqst['fieldValue']) ? trim($rqst['fieldValue']) : '';
        $id = isset($rqst['id']) ? intval($rqst['id']) : 0;

        if (empty($fieldValue)) {
            return Util::error_missing_data_description('El campo "Nombre de usuario" es requerido.');
        }

        $validation = self::validateUsername($fieldValue);
        if (!$validation['valid']) {
            return Util::error_general($validation['message']);
        }

        $db = new DbConection();
        $pdo = $db->openConect();
        $params = [':fieldValue' => $fieldValue];
        $q = "SELECT id FROM " . $db->getTable('tbl_votantes') . " WHERE username = :fieldValue";

        if ($id > 0) {
            $q .= " AND id != :id";
            $params[':id'] = $id;
        }

        try {
            $stmt = $pdo->prepare($q);
            $stmt->execute($params);
            if ($stmt->fetch()) {
                $arrjson = Util::error_general('El valor \"' . $fieldValue . '\" ya existe.');
            } else {
                $arrjson = array('output' => array('valid' => true, 'response' => 'available'));
            }
        } catch (PDOException $e) {
            $arrjson = Util::error_general('Error al verificar la disponibilidad.');
        } finally {
            $db->closeConect();
        }
        return $arrjson;
    }

    public static function validateUsername($username) {
        // Remover espacios en blanco
        $username = trim($username);
        
        // Validaciones básicas
        if (empty($username)) {
            return Util::error_missing_data_description('El nombre de usuario es requerido.');
        }
        
        if (strlen($username) < 3 || strlen($username) > 20) {
            return Util::error_missing_data_description('El nombre de usuario debe tener entre 3 y 20 caracteres.');
        }
        
        // Expresión regular: solo letras, números, guión bajo y punto
        // No puede empezar ni terminar con punto o guión bajo
        if (!preg_match('/^[a-zA-Z0-9][a-zA-Z0-9._]*[a-zA-Z0-9]$|^[a-zA-Z0-9]$/', $username)) {
            return Util::error_missing_data_description('El nombre de usuario solo puede contener letras, números, puntos y guiones bajos. No puede empezar ni terminar con punto o guión bajo.');
        }
        
        // Validar que no tenga puntos o guiones bajos consecutivos
        if (preg_match('/[._]{2,}/', $username)) {
            return Util::error_missing_data_description('No se permiten puntos o guiones bajos consecutivos.');
        }
        
        return ['valid' => true, 'username' => $username];
    }

    public static function delete($rqst)
    {
        $id = isset($rqst['id']) ? intval($rqst['id']) : 0;
        if ($id <= 0) {
            return Util::error_missing_data();
        }

        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $q = "DELETE FROM " . $db->getTable('tbl_votantes') . " WHERE id = :id";
            $stmt = $pdo->prepare($q);
            if ($stmt->execute([':id' => $id])) {
                $arrjson = array('output' => array('valid' => true));
            } else {
                $arrjson = Util::error_generaldelete();
            }
        } catch (PDOException $e) {
            $arrjson = Util::error_general('Error al eliminar el registro.');
        } finally {
            $db->closeConect();
        }
        return $arrjson;
    }
}
