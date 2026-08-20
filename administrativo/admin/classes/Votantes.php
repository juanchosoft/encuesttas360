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

    $ip_registro = Util::get_real_ipaddress();
    $user_agent  = $_SERVER['HTTP_USER_AGENT'] ?? '';

    $validar = isset($rqst['validar']) ? trim((string)$rqst['validar']) : 'no';

    // =======================
    // Validaciones mínimas
    // =======================
    if ($id > 0 && $tbl_usuario_id_actual <= 0) {
        return Util::error_missing_data_description('Sesión inválida: no se detectó usuario.');
    }

    if (empty($username) && !empty($email)) {
        $username = $email;
    }

    if (empty($nombre_completo) && !empty($username)) {
        $nombre_completo = $username;
    }

    if ($nombre_completo === '') return Util::error_missing_data_description('El campo "Nombre completo" es requerido.');
    if ($ideologia === '')       return Util::error_missing_data_description('El campo "Ideología política" es requerido.');
    if ($rango_edad === '')      return Util::error_missing_data_description('El campo "Rango de edad" es requerido.');

    if ($genero === '')              return Util::error_missing_data_description('El campo "Género" es requerido.');
    if ($codigo_departamento === '') return Util::error_missing_data_description('El campo "Código del departamento" es requerido.');
    if ($codigo_municipio === '')    return Util::error_missing_data_description('El campo "Código del municipio" es requerido.');
    if ($estado === '')              return Util::error_missing_data_description('El campo "Estado de la cuenta" es requerido.');

    // Hash pass si viene
    if ($password !== null && strlen($password) > 2) {
        $password = Util::make_hash_pass($password);
    } else {
        // si viene vacío, lo dejamos null para que no rompa insert/update
        $password = null;
    }

    // Validaciones extra si "validar=si"
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
        // ✅ obliga a lanzar excepción si algo falla
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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
            // =======================
            // UPDATE (robusto)
            // =======================
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

            // ✅ Si no afectó filas, te lo reporto (para que no “mienta”)
            if ($stmt->rowCount() === 0) {
                // Puede ser: id no existe o datos idénticos. Confirmamos existencia.
                $chk = $pdo->prepare("SELECT id FROM {$table} WHERE id = :id LIMIT 1");
                $chk->execute([':id' => $id]);
                if (!$chk->fetchColumn()) {
                    throw new Exception("No existe el registro con id={$id} en tbl_votantes.");
                }
            }

            $arrjson = ['output' => ['valid' => true, 'id' => $id]];
        } else {
            // =======================
            // INSERT (robusto)
            // =======================
            $sql = "
                INSERT INTO {$table}
                (dtcreate, tbl_usuario_id, nombre_completo, ideologia, rango_edad, nivel_ingresos, email, username, password, genero, codigo_departamento, codigo_municipio, comuna, barrio, nivel_educacion, ocupacion, estado, ip_registro, user_agent)
                VALUES
                (:dtcreate, :tbl_usuario_id, :nombre_completo, :ideologia, :rango_edad, :nivel_ingresos, :email, :username, :password, :genero, :codigo_departamento, :codigo_municipio, :comuna, :barrio, :nivel_educacion, :ocupacion, :estado, :ip_registro, :user_agent)
            ";

            $stmt = $pdo->prepare($sql);

            $params = [
                ':dtcreate'            => Util::date(), // o Util::date_now_server() si lo manejas en SQL
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
                ':ocupacion'           => $ocupacion,
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

            // Si encuestador => guarda extras
            if (SessionData::encuestador()) {
                Votantes::guardarRespuestaSondeo($rqst, $id, $pdo, $db);
                Votantes::guardarRespuestasCuestionario($rqst, $id, $pdo, $db);
            }
            $arrjson = ['output' => ['valid' => true, 'response' => $id]];
        }

        $pdo->commit();
        return $arrjson;

    } catch (Throwable $e) {
        if ($pdo && $pdo->inTransaction()) $pdo->rollBack();

        // Mensaje amigable para errores conocidos
        $msg = $e->getMessage();
        if (strpos($msg, '1062') !== false || strpos($msg, 'Duplicate entry') !== false) {
            if (strpos($msg, 'email') !== false) {
                $msg = 'El correo electrónico ya está registrado. Usa uno diferente.';
            } elseif (strpos($msg, 'username') !== false) {
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








//    ********************FIN FUNCION GUARDAR*************************
    /**
     * Guarda la respuesta del sondeo activo junto con el votante
     * Adaptado de la lógica de estadisticaweb/admin/classes/Sondeo.php::registrarVoto (líneas 90-170)
     */
    private static function guardarRespuestaSondeo($rqst, $votanteId, $pdo, $db)
    {
        $sondeo_id = isset($rqst['sondeo_id']) ? intval($rqst['sondeo_id']) : 0;
        $sondeo_opcion_id = isset($rqst['sondeo_opcion_id']) ? intval($rqst['sondeo_opcion_id']) : 0;
        $sondeo_candidato_id = isset($rqst['sondeo_candidato_id']) ? intval($rqst['sondeo_candidato_id']) : 0;

        $sondeo_tipo = null; // 'si' = por candidato, 'no' = por opción

        // Si no hay sondeo seleccionado, no hacer nada
        if ($sondeo_id <= 0) {
            return;
        }

        if($sondeo_opcion_id > 0){
            $sondeo_tipo = 'no';
        }

        if($sondeo_candidato_id > 0){
            $sondeo_tipo = 'si';
        }

        // Variables que se insertarán en la base de datos
        $candidato_id_insert = null;
        $opcion_idsert = null;
        $valor = null; // Este es el valor que va en tbl_sondeo_x_opciones_id

        // Determinar si es respuesta por candidato u opción
        if ($sondeo_tipo === 'si') {
            // Sondeo de candidatos
            $candidato_id = isset($rqst['sondeo_candidato_id']) ? intval($rqst['sondeo_candidato_id']) : 0;

            // Si no seleccionó candidato, no guardar respuesta (es opcional)
            if ($candidato_id <= 0) {
                return;
            }

            $candidato_id_insert = $candidato_id;
            $valor = $candidato_id; // El ID del candidato va también en tbl_sondeo_x_opciones_id
        } else {
            // Sondeo de opciones (Sí/No u otras)
            // Recibimos directamente el ID de la opción desde el frontend
            $opcion_id = isset($rqst['sondeo_opcion_id']) ? intval($rqst['sondeo_opcion_id']) : 0;

            $valor = $opcion_id;
            // Si no seleccionó opción, no guardar respuesta (es opcional)
            if ($opcion_id <= 0) {
                return;
            }

        }

        // Obtener departamento y municipio del votante
        $codigo_departamento = isset($rqst['codigo_departamento']) ? trim($rqst['codigo_departamento']) : '';
        $codigo_municipio = isset($rqst['codigo_municipio']) ? trim($rqst['codigo_municipio']) : '';

        // Insertar la respuesta del sondeo
        // Estructura idéntica a estadisticaweb línea 148-164
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

    /**
     * Guarda las respuestas del cuestionario activo junto con el votante
     * Utiliza la misma estructura que RespuestaCuestionario::save()
     * Tablas: tbl_cuestionario_intentos (cabecera) y tbl_cuestionario_respuestas (detalle)
     */
    private static function guardarRespuestasCuestionario($rqst, $votanteId, $pdo, $db)
    {
        $cuestionario_id = isset($rqst['cuestionario_id']) ? intval($rqst['cuestionario_id']) : 0;
        $respuestas_json = isset($rqst['cuestionario_respuestas']) ? $rqst['cuestionario_respuestas'] : '';

        // Si no hay cuestionario seleccionado, no hacer nada
        if ($cuestionario_id <= 0 || empty($respuestas_json)) {
            return;
        }

        // Decodificar JSON de respuestas
        $respuestas = json_decode($respuestas_json, true);
        if (!is_array($respuestas)) {
            return;
        }

        // 1. Insertar el intento de respuesta (cabecera) en tbl_cuestionario_intentos
        $qIntento = "INSERT INTO " . $db->getTable('tbl_cuestionario_intentos') . "
                     (tbl_ficha_tecnica_encuesta_id, tbl_votante_id, fecha_respuesta, dtcreate)
                     VALUES (:ficha_tecnica_id, :tbl_votante_id, NOW(), NOW())";

        $stmtIntento = $pdo->prepare($qIntento);
        $stmtIntento->execute([
            ':ficha_tecnica_id' => $cuestionario_id,
            ':tbl_votante_id' => $votanteId
        ]);

        $intentoId = $pdo->lastInsertId();

        // 2. Insertar cada respuesta de pregunta en tbl_cuestionario_respuestas
        $qRespuesta = "INSERT INTO " . $db->getTable('tbl_cuestionario_respuestas') . "
                       (tbl_intento_id, tbl_pregunta_id, tbl_opcion_respuesta_id, respuesta_texto, dtcreate)
                       VALUES (:intento_id, :pregunta_id, :opcion_id, :texto, NOW())";

        $stmtRespuesta = $pdo->prepare($qRespuesta);

        foreach ($respuestas as $respuesta) {
            $pregunta_id = isset($respuesta['pregunta_id']) ? intval($respuesta['pregunta_id']) : 0;
            $opciones = isset($respuesta['opciones']) && is_array($respuesta['opciones']) ? $respuesta['opciones'] : [];
            $texto = isset($respuesta['texto']) ? trim($respuesta['texto']) : '';

            if ($pregunta_id <= 0) {
                continue;
            }

            // Si hay opciones seleccionadas (radio/checkbox)
            if (!empty($opciones)) {
                foreach ($opciones as $opcion_id) {
                    $opcion_id = intval($opcion_id);
                    if ($opcion_id <= 0) {
                        continue;
                    }

                    $stmtRespuesta->execute([
                        ':intento_id' => $intentoId,
                        ':pregunta_id' => $pregunta_id,
                        ':opcion_id' => $opcion_id,
                        ':texto' => null
                    ]);
                }
            }

            // Si hay respuesta de texto (textarea)
            if (!empty($texto)) {
                $stmtRespuesta->execute([
                    ':intento_id' => $intentoId,
                    ':pregunta_id' => $pregunta_id,
                    ':opcion_id' => null,
                    ':texto' => $texto
                ]);
            }
        }
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
