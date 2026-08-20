<?php
class Votantes
{
    public function __construct() {}

    public static function getAll($rqst)
    {
        $id = isset($rqst['id']) ? intval($rqst['id']) : 0;
        $db = new DbConection();
        $pdo = $db->openConect();
        $q = "SELECT * FROM " . $db->getTable('tbl_votantes');
        $params = [];

        if ($id > 0) {
            $q .= " WHERE id = :id";
            $params[':id'] = $id;
        }

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

    public static function save($rqst)
    {
        $id = isset($rqst['id']) ? (int)$rqst['id'] : 0;

        $tbl_usuario_id = isset($_SESSION['session_user']['id'])
            ? (int)$_SESSION['session_user']['id']
            : 0;

        $nombre_completo      = isset($rqst['nombre_completo']) ? trim((string)$rqst['nombre_completo']) : '';
        $ideologia            = isset($rqst['ideologia']) ? trim((string)$rqst['ideologia']) : '';
        $rango_edad           = isset($rqst['rango_edad']) ? trim((string)$rqst['rango_edad']) : '';
        $nivel_ingresos       = (isset($rqst['nivel_ingresos']) && trim((string)$rqst['nivel_ingresos']) !== '') ? trim((string)$rqst['nivel_ingresos']) : null;
        $email                = isset($rqst['email']) ? trim((string)$rqst['email']) : '';
        $username             = isset($rqst['username']) ? trim((string)$rqst['username']) : '';
        $password             = isset($rqst['password']) ? trim((string)$rqst['password']) : '';
        $genero               = isset($rqst['genero']) ? trim((string)$rqst['genero']) : '';
        $codigo_departamento  = isset($rqst['codigo_departamento']) ? trim((string)$rqst['codigo_departamento']) : '';
        $codigo_municipio     = isset($rqst['codigo_municipio']) ? trim((string)$rqst['codigo_municipio']) : '';
        $comuna               = isset($rqst['comuna']) ? trim((string)$rqst['comuna']) : '';
        $barrio               = isset($rqst['barrio']) ? trim((string)$rqst['barrio']) : '';
        $nivel_educacion      = isset($rqst['nivel_educacion']) ? trim((string)$rqst['nivel_educacion']) : '';
        $ocupacion            = isset($rqst['ocupacion']) ? trim((string)$rqst['ocupacion']) : '';
        $estado               = isset($rqst['estado']) ? trim((string)$rqst['estado']) : '';

        // NUEVOS CAMPOS DE DISPOSITIVO
        $device_token         = isset($rqst['device_token']) ? trim((string)$rqst['device_token']) : '';
        $device_fingerprint   = isset($rqst['device_fingerprint']) ? trim((string)$rqst['device_fingerprint']) : '';
        $device_user_agent    = isset($rqst['device_user_agent']) ? trim((string)$rqst['device_user_agent']) : '';
        $device_platform      = isset($rqst['device_platform']) ? trim((string)$rqst['device_platform']) : '';
        $device_language      = isset($rqst['device_language']) ? trim((string)$rqst['device_language']) : '';
        $device_timezone      = isset($rqst['device_timezone']) ? trim((string)$rqst['device_timezone']) : '';

        // CAMPOS DE SERVIDOR
        $ip_registro = Util::get_real_ipaddress();
        $user_agent  = isset($_SERVER['HTTP_USER_AGENT']) ? (string)$_SERVER['HTTP_USER_AGENT'] : '';

        // =========================
        // VALIDACIONES
        // =========================
        if ($ideologia === '') {
            return array('output' => array(
                'valid' => false,
                'response' => array('code' => 'VALIDATION', 'content' => 'El campo "Ideología política" es requerido.')
            ));
        }

        if ($rango_edad === '') {
            return array('output' => array(
                'valid' => false,
                'response' => array('code' => 'VALIDATION', 'content' => 'El campo "Rango de edad" es requerido.')
            ));
        }

        // if ($nivel_ingresos === '') {
        //     return array('output' => array(
        //         'valid' => false,
        //         'response' => array('code' => 'VALIDATION', 'content' => 'El campo "Nivel socioeconómico" es requerido.')
        //     ));
        // }

        if ($email === '') {
            return array('output' => array(
                'valid' => false,
                'response' => array('code' => 'VALIDATION', 'content' => 'El campo "Correo electrónico" es requerido.')
            ));
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return array('output' => array(
                'valid' => false,
                'response' => array('code' => 'EMAIL_INVALID', 'content' => 'El campo "Correo electrónico" es inválido.')
            ));
        }

        if ($username === '') {
            $username = $email;
        }

        if ($nombre_completo === '') {
            $nombre_completo = $username;
        }

        if ($username === '') {
            return array('output' => array(
                'valid' => false,
                'response' => array('code' => 'VALIDATION', 'content' => 'El campo "Username" es requerido.')
            ));
        }

        if (strlen($username) < 4) {
            return array('output' => array(
                'valid' => false,
                'response' => array('code' => 'USERNAME_INVALID', 'content' => 'El campo "Username" debe tener al menos 4 caracteres.')
            ));
        }

        if (filter_var($username, FILTER_VALIDATE_EMAIL) === false) {
            $validation = self::validateUsername($username);
            if (!isset($validation['valid']) || !$validation['valid']) {
                return array('output' => array(
                    'valid' => false,
                    'response' => array(
                        'code' => 'USERNAME_INVALID',
                        'content' => isset($validation['response']['content'])
                            ? $validation['response']['content']
                            : 'El username no es válido.'
                    )
                ));
            }
        }

        if ($id <= 0 && $password === '') {
            return array('output' => array(
                'valid' => false,
                'response' => array('code' => 'PASSWORD_REQUIRED', 'content' => 'La contraseña es requerida para crear la cuenta.')
            ));
        }

        if ($password !== '' && strlen($password) <= 2) {
            return array('output' => array(
                'valid' => false,
                'response' => array('code' => 'PASSWORD_SHORT', 'content' => 'La contraseña es demasiado corta.')
            ));
        }

        if ($genero === '') {
            return array('output' => array(
                'valid' => false,
                'response' => array('code' => 'VALIDATION', 'content' => 'El campo "Género" es requerido.')
            ));
        }

        if ($codigo_departamento === '') {
            return array('output' => array(
                'valid' => false,
                'response' => array('code' => 'VALIDATION', 'content' => 'El campo "Código del departamento" es requerido.')
            ));
        }

        if ($codigo_municipio === '') {
            return array('output' => array(
                'valid' => false,
                'response' => array('code' => 'VALIDATION', 'content' => 'El campo "Código del municipio" es requerido.')
            ));
        }

        if ($ocupacion === '') {
            return array('output' => array(
                'valid' => false,
                'response' => array('code' => 'VALIDATION', 'content' => 'El campo "Ocupación" es requerido.')
            ));
        }

        if ($estado === '') {
            return array('output' => array(
                'valid' => false,
                'response' => array('code' => 'VALIDATION', 'content' => 'El campo "Estado de la cuenta" es requerido.')
            ));
        }

        // Solo exigir identificación del dispositivo en creación
        if ($id <= 0) {
            if ($device_token === '' || $device_fingerprint === '') {
                return array('output' => array(
                    'valid' => false,
                    'response' => array(
                        'code' => 'DEVICE_REQUIRED',
                        'content' => 'No fue posible validar el dispositivo.'
                    )
                ));
            }
        }

        $db  = new DbConection();
        $pdo = $db->openConect();
        $table = $db->getTable('tbl_votantes');

        try {
            $pdo->beginTransaction();

            // =========================
            // VALIDAR EMAIL ÚNICO
            // =========================
            $sqlEmail = "SELECT id FROM {$table} WHERE email = :email";
            $paramsEmail = array(':email' => $email);

            if ($id > 0) {
                $sqlEmail .= " AND id != :id";
                $paramsEmail[':id'] = $id;
            }

            $stmtEmail = $pdo->prepare($sqlEmail);
            $stmtEmail->execute($paramsEmail);

            if ($stmtEmail->fetch(PDO::FETCH_ASSOC)) {
                $pdo->rollBack();
                return array('output' => array(
                    'valid' => false,
                    'response' => array(
                        'code' => 'EMAIL_EXISTS',
                        'content' => 'Este correo ya se encuentra registrado.'
                    )
                ));
            }

            // =========================
            // VALIDAR USERNAME ÚNICO
            // =========================
            $sqlUser = "SELECT id FROM {$table} WHERE username = :username";
            $paramsUser = array(':username' => $username);

            if ($id > 0) {
                $sqlUser .= " AND id != :id";
                $paramsUser[':id'] = $id;
            }

            $stmtUser = $pdo->prepare($sqlUser);
            $stmtUser->execute($paramsUser);

            if ($stmtUser->fetch(PDO::FETCH_ASSOC)) {
                $pdo->rollBack();
                return array('output' => array(
                    'valid' => false,
                    'response' => array(
                        'code' => 'USERNAME_EXISTS',
                        'content' => 'Este username ya se encuentra registrado.'
                    )
                ));
            }

            // =========================
            // VALIDAR DISPOSITIVO SOLO EN CREACIÓN
            // =========================
            if ($id <= 0) {
                $sqlDevice = "SELECT id, email, username
                              FROM {$table}
                              WHERE device_token = :device_token
                                 OR device_fingerprint = :device_fingerprint
                              LIMIT 1";
                $stmtDevice = $pdo->prepare($sqlDevice);
                $stmtDevice->execute(array(
                    ':device_token' => $device_token,
                    ':device_fingerprint' => $device_fingerprint
                ));

                if ($stmtDevice->fetch(PDO::FETCH_ASSOC)) {
                    $pdo->rollBack();
                    return array('output' => array(
                        'valid' => false,
                        'response' => array(
                            'code' => 'DEVICE_EXISTS',
                            'content' => 'Este equipo ya fue utilizado para crear una cuenta. No es posible registrarse nuevamente desde este dispositivo.'
                        )
                    ));
                }
            }

            // =========================
            // PROCESAR PASSWORD
            // =========================
            $passwordToSave = '';
            if ($password !== '') {
                $passwordToSave = Util::make_hash_pass($password);
            }

            // =========================
            // UPDATE
            // =========================
            if ($id > 0) {
                $fields = array(
                    'tbl_usuario_id'      => $tbl_usuario_id,
                    'nombre_completo'     => $nombre_completo,
                    'ideologia'           => $ideologia,
                    'rango_edad'          => $rango_edad,
                    'nivel_ingresos'      => $nivel_ingresos,
                    'email'               => $email,
                    'username'            => $username,
                    'genero'              => $genero,
                    'codigo_departamento' => $codigo_departamento,
                    'codigo_municipio'    => $codigo_municipio,
                    'comuna'              => $comuna,
                    'barrio'              => $barrio,
                    'nivel_educacion'     => $nivel_educacion,
                    'ocupacion'           => $ocupacion,
                    'estado'              => $estado,
                    'dtupdate'            => Util::date()
                );

                // Solo actualizar password si enviaron nueva
                if ($passwordToSave !== '') {
                    $fields['password'] = $passwordToSave;
                }

                // Si quieres también actualizar información técnica del dispositivo al editar:
                if ($device_token !== '')       $fields['device_token'] = $device_token;
                if ($device_fingerprint !== '') $fields['device_fingerprint'] = $device_fingerprint;
                if ($device_user_agent !== '')  $fields['device_user_agent'] = $device_user_agent;
                if ($device_platform !== '')    $fields['device_platform'] = $device_platform;
                if ($device_language !== '')    $fields['device_language'] = $device_language;
                if ($device_timezone !== '')    $fields['device_timezone'] = $device_timezone;

                $fields['ip_registro'] = $ip_registro;
                $fields['user_agent']  = $user_agent;

                $setParts = array();
                $params = array(':id' => $id);

                foreach ($fields as $col => $val) {
                    $ph = ':' . $col;
                    $setParts[] = "{$col} = {$ph}";
                    $params[$ph] = $val;
                }

                $sql = "UPDATE {$table} SET " . implode(', ', $setParts) . " WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);

                $pdo->commit();
                return array('output' => array('valid' => true, 'id' => $id));
            }

            // =========================
            // INSERT
            // =========================
            $sql = "INSERT INTO {$table}
                (
                    dtcreate,
                    tbl_usuario_id,
                    nombre_completo,
                    ideologia,
                    rango_edad,
                    nivel_ingresos,
                    email,
                    username,
                    password,
                    genero,
                    codigo_departamento,
                    codigo_municipio,
                    comuna,
                    barrio,
                    nivel_educacion,
                    ocupacion,
                    estado,
                    habilitado,
                    ip_registro,
                    user_agent,
                    device_token,
                    device_fingerprint,
                    device_user_agent,
                    device_platform,
                    device_language,
                    device_timezone
                )
                VALUES
                (
                    :dtcreate,
                    :tbl_usuario_id,
                    :nombre_completo,
                    :ideologia,
                    :rango_edad,
                    :nivel_ingresos,
                    :email,
                    :username,
                    :password,
                    :genero,
                    :codigo_departamento,
                    :codigo_municipio,
                    :comuna,
                    :barrio,
                    :nivel_educacion,
                    :ocupacion,
                    :estado,
                    :habilitado,
                    :ip_registro,
                    :user_agent,
                    :device_token,
                    :device_fingerprint,
                    :device_user_agent,
                    :device_platform,
                    :device_language,
                    :device_timezone
                )";

            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(
                ':dtcreate'            => Util::date(),
                ':tbl_usuario_id'      => $tbl_usuario_id,
                ':nombre_completo'     => $nombre_completo,
                ':ideologia'           => $ideologia,
                ':rango_edad'          => $rango_edad,
                ':nivel_ingresos'      => $nivel_ingresos,
                ':email'               => $email,
                ':username'            => $username,
                ':password'            => $passwordToSave,
                ':genero'              => $genero,
                ':codigo_departamento' => $codigo_departamento,
                ':codigo_municipio'    => $codigo_municipio,
                ':comuna'              => $comuna,
                ':barrio'              => $barrio,
                ':nivel_educacion'     => $nivel_educacion,
                ':ocupacion'           => $ocupacion,
                ':estado'              => $estado,
                ':habilitado'          => 'si',
                ':ip_registro'         => $ip_registro,
                ':user_agent'          => $user_agent,
                ':device_token'        => $device_token,
                ':device_fingerprint'  => $device_fingerprint,
                ':device_user_agent'   => $device_user_agent,
                ':device_platform'     => $device_platform,
                ':device_language'     => $device_language,
                ':device_timezone'     => $device_timezone
            ));

            $newId = (int)$pdo->lastInsertId();
            $pdo->commit();

            return array('output' => array('valid' => true, 'response' => $newId));

        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $errorCode = (string)$e->getCode();
            $errorMsg  = $e->getMessage();

            if ($errorCode === '23000') {
                if (stripos($errorMsg, 'device_token') !== false || stripos($errorMsg, 'device_fingerprint') !== false) {
                    return array('output' => array(
                        'valid' => false,
                        'response' => array(
                            'code' => 'DEVICE_EXISTS',
                            'content' => 'Este equipo ya fue utilizado para crear una cuenta. No es posible registrarse nuevamente desde este dispositivo.'
                        )
                    ));
                }

                if (stripos($errorMsg, 'email') !== false) {
                    return array('output' => array(
                        'valid' => false,
                        'response' => array(
                            'code' => 'EMAIL_EXISTS',
                            'content' => 'Este correo ya se encuentra registrado.'
                        )
                    ));
                }

                if (stripos($errorMsg, 'username') !== false) {
                    return array('output' => array(
                        'valid' => false,
                        'response' => array(
                            'code' => 'USERNAME_EXISTS',
                            'content' => 'Este username ya se encuentra registrado.'
                        )
                    ));
                }
            }

            return array('output' => array(
                'valid' => false,
                'response' => array(
                    'code' => 'SQL',
                    'content' => '[' . $errorCode . '] ' . $errorMsg
                )
            ));
        } finally {
            $db->closeConect();
        }
    }

    public static function available($rqst)
    {
        // Compatible con el flujo viejo y el nuevo
        $field = isset($rqst['field']) ? trim((string)$rqst['field']) : 'username';
        $value = '';

        if (isset($rqst['value'])) {
            $value = trim((string)$rqst['value']);
        } elseif (isset($rqst['fieldValue'])) {
            $value = trim((string)$rqst['fieldValue']);
        }

        $id = isset($rqst['id']) ? intval($rqst['id']) : 0;

        if ($value === '') {
            return array('output' => array(
                'valid' => false,
                'response' => array(
                    'code' => 'VALIDATION',
                    'content' => 'El valor a validar es requerido.'
                )
            ));
        }

        $allowedFields = array('username', 'email');
        if (!in_array($field, $allowedFields, true)) {
            $field = 'username';
        }

        if ($field === 'username') {
            $validation = self::validateUsername($value);
            if (!isset($validation['valid']) || !$validation['valid']) {
                return array('output' => array(
                    'valid' => false,
                    'response' => array(
                        'code' => 'USERNAME_INVALID',
                        'content' => isset($validation['response']['content'])
                            ? $validation['response']['content']
                            : 'El nombre de usuario no es válido.'
                    )
                ));
            }
        }

        if ($field === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return array('output' => array(
                'valid' => false,
                'response' => array(
                    'code' => 'EMAIL_INVALID',
                    'content' => 'El correo electrónico no es válido.'
                )
            ));
        }

        $db = new DbConection();
        $pdo = $db->openConect();
        $table = $db->getTable('tbl_votantes');

        try {
            $sql = "SELECT id FROM {$table} WHERE {$field} = :value";
            $params = array(':value' => $value);

            if ($id > 0) {
                $sql .= " AND id != :id";
                $params[':id'] = $id;
            }

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            if ($stmt->fetch(PDO::FETCH_ASSOC)) {
                $message = ($field === 'email')
                    ? 'El correo electrónico "' . $value . '" ya existe.'
                    : 'El nombre de usuario "' . $value . '" ya existe.';

                return array('output' => array(
                    'valid' => false,
                    'response' => array(
                        'code' => strtoupper($field) . '_EXISTS',
                        'content' => $message
                    )
                ));
            }

            return array('output' => array(
                'valid' => true,
                'response' => 'available'
            ));
        } catch (PDOException $e) {
            return array('output' => array(
                'valid' => false,
                'response' => array(
                    'code' => 'SQL',
                    'content' => 'Error al verificar la disponibilidad.'
                )
            ));
        } finally {
            $db->closeConect();
        }
    }

    public static function validateUsername($username)
    {
        $username = trim($username);

        if (empty($username)) {
            return array(
                'valid' => false,
                'response' => array(
                    'content' => 'El nombre de usuario es requerido.'
                )
            );
        }

        if (strlen($username) < 3 || strlen($username) > 20) {
            return array(
                'valid' => false,
                'response' => array(
                    'content' => 'El nombre de usuario debe tener entre 3 y 20 caracteres.'
                )
            );
        }

        if (!preg_match('/^[a-zA-Z0-9][a-zA-Z0-9._]*[a-zA-Z0-9]$|^[a-zA-Z0-9]$/', $username)) {
            return array(
                'valid' => false,
                'response' => array(
                    'content' => 'El nombre de usuario solo puede contener letras, números, puntos y guiones bajos. No puede empezar ni terminar con punto o guión bajo.'
                )
            );
        }

        if (preg_match('/[._]{2,}/', $username)) {
            return array(
                'valid' => false,
                'response' => array(
                    'content' => 'No se permiten puntos o guiones bajos consecutivos.'
                )
            );
        }

        return array(
            'valid' => true,
            'username' => $username
        );
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

    private static function getPerfilPasswordHash($password)
    {
        $password = trim((string)$password);
        $passwordForHash = preg_match('/^[a-f0-9]{32}$/i', $password) ? strtolower($password) : md5($password);
        return Util::make_hash_pass($passwordForHash);
    }

    public static function actualizarPerfil($rqst)
    {
        $output = array(
            "valid" => false,
            "response" => "No se pudo actualizar el perfil"
        );

        try {
            $id = isset($rqst['idVotantes']) ? intval($rqst['idVotantes']) : 0;
            $nombre_completo = isset($rqst['nombre_completo']) ? trim($rqst['nombre_completo']) : '';
            $email = isset($rqst['email']) ? trim($rqst['email']) : '';
            $username = isset($rqst['username']) ? trim($rqst['username']) : '';
            $current_password = isset($rqst['current_password']) ? trim($rqst['current_password']) : '';
            $new_password = isset($rqst['password']) ? trim($rqst['password']) : '';

            if ($id <= 0) {
                $output['response'] = "ID de votante no válido";
                return $output;
            }

            if (empty($nombre_completo) || empty($email) || empty($username)) {
                $output['response'] = "Todos los campos marcados con * son requeridos";
                return $output;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $output['response'] = "El correo electrónico no es válido";
                return $output;
            }

            $db = new DbConection();
            $pdo = $db->openConect();

            $stmt = $pdo->prepare("SELECT id, password, username, email FROM " . $db->getTable('tbl_votantes') . " WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                $output['response'] = "Usuario no encontrado";
                $db->closeConect();
                return $output;
            }

            // Solo valida contraseña actual si se proporcionó
            if (!empty($current_password)) {
                $hashed_current_password = self::getPerfilPasswordHash($current_password);
                $legacy_current_password = Util::make_hash_pass($current_password);

                if ($user['password'] !== $hashed_current_password && $user['password'] !== $legacy_current_password) {
                    $output['response'] = "La contraseña actual es incorrecta";
                    $db->closeConect();
                    return $output;
                }
            }

            $stmt = $pdo->prepare("SELECT id FROM " . $db->getTable('tbl_votantes') . " WHERE username = :username AND id != :id");
            $stmt->execute([':username' => $username, ':id' => $id]);
            if ($stmt->fetch()) {
                $output['response'] = "El nombre de usuario '$username' ya está siendo utilizado";
                $db->closeConect();
                return $output;
            }

            $stmt = $pdo->prepare("SELECT id FROM " . $db->getTable('tbl_votantes') . " WHERE email = :email AND id != :id");
            $stmt->execute([':email' => $email, ':id' => $id]);
            if ($stmt->fetch()) {
                $output['response'] = "El correo electrónico '$email' ya está siendo utilizado";
                $db->closeConect();
                return $output;
            }

            $table = $db->getTable('tbl_votantes');
            $updates = array();
            $params = array(':id' => $id);

            $updates[] = "nombre_completo = :nombre_completo";
            $params[':nombre_completo'] = $nombre_completo;

            $updates[] = "email = :email";
            $params[':email'] = $email;

            $updates[] = "username = :username";
            $params[':username'] = $username;

            $updates[] = "dtupdate = :dtupdate";
            $params[':dtupdate'] = Util::date();

            if (!empty($new_password)) {
                $hashed_new_password = self::getPerfilPasswordHash($new_password);
                $updates[] = "password = :password";
                $params[':password'] = $hashed_new_password;
            }

            $sql = "UPDATE $table SET " . implode(', ', $updates) . " WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute($params);

            if ($result) {
                if (isset($_SESSION['session_user'])) {
                    $_SESSION['session_user']['nombre_completo'] = $nombre_completo;
                    $_SESSION['session_user']['username'] = $username;
                    $_SESSION['session_user']['usuario'] = $username;
                    $_SESSION['session_user']['email'] = $email;
                }

                $output['valid'] = true;
                $output['response'] = "Perfil actualizado correctamente";
            } else {
                $output['response'] = "No se realizaron cambios en la base de datos";
            }

            $db->closeConect();

        } catch (Exception $e) {
            $output['response'] = "Error del sistema: " . $e->getMessage();
        }

        return $output;
    }
}
