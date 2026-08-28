<?php

require_once __DIR__ . '/Role.php';

/**
 * Clase que contiene todas las operaciones utilizadas sobre la base de datos
 * @author SPIDERSOFTWARE
 */
class Usuario
{

    public function __construct() {}

    public static function getAllInicioSesion($rqst)
    {

    
        $id = isset($rqst['id']) ? intval($rqst['id']) : 0;

        $db = new DbConection();
        $pdo = $db->openConect();

        $q = "SELECT tbl_historial_session.*, tbl_usuarios.nickname, tbl_usuarios.nombre, tbl_usuarios.apellido  FROM " . $db->getTable('tbl_historial_session') . " 
        INNER JOIN " . $db->getTable('tbl_usuarios') . " 
        ON tbl_historial_session.tec_usuario_id = tbl_usuarios.id ";
        $result = $pdo->query($q);
        $arr = array();
        if ($result) {
            foreach ($result as $valor) {
                $arr[] = $valor;
            }
            $arrjson = array('output' => array('valid' => true, 'response' => $arr));
        } else {
            $arrjson = Util::error_no_result();
        }
        $db->closeConect();
        return $arrjson;
    }
    
    
    public static function getAll($rqst)
    {

        $id = isset($rqst['id']) ? intval($rqst['id']) : 0;
        $tipo = isset($rqst['tipo']) ? trim($rqst['tipo']) : ''; // 

        $db = new DbConection();
        $pdo = $db->openConect();

        $q = "SELECT tbl_usuarios.*, tbl_roles.role_key AS role_key
              FROM " . $db->getTable('tbl_usuarios') . "
              LEFT JOIN " . $db->getTable('tbl_roles') . " ON tbl_roles.id = tbl_usuarios.role_id";
        $params = [];

        if ($id > 0) {

            $q .= " WHERE tbl_usuarios.id = :id";
            $params[':id'] = $id;
        } elseif ($tipo != "") {

            $q .= " WHERE tbl_usuarios.tipo = :tipo AND tbl_usuarios.habilitado = 'si'";
            $params[':tipo'] = $tipo;
        } else {
            // Cuando no hay filtros, ordenamos por ID de forma descendente
            // Esto asegura que los usuarios más nuevos (con IDs más altos)
            // aparezcan en la parte superior de la tabla.
            $q .= " ORDER BY tbl_usuarios.id DESC";
        }
        
        $result = $pdo->prepare($q);

        // Se ejecuta la consulta con los parámetros
        if ($result->execute($params)) {
            $arr = $result->fetchAll(PDO::FETCH_ASSOC);
            $arrjson = array('output' => array('valid' => true, 'response' => $arr));
        } else {
            
            $arrjson = Util::error_no_result();
        }
        
        $db->closeConect();
        
        return $arrjson;
    }


    public static function getPaginated($limit, $offset)
    {
        $db = Database::getConnection(); // Conexión a la base de datos (PDO)
        $query = "SELECT SQL_CALC_FOUND_ROWS * FROM tbl_usuarios LIMIT :offset, :limit";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $total = $db->query("SELECT FOUND_ROWS()")->fetchColumn();

        return [
            'data' => $data,
            'total' => $total,
        ];
    }

    public static function available($rqst)
    {
        $nickname = isset($rqst['nickname']) ? ($rqst['nickname']) : '';
        $id = isset($rqst['id']) ? ($rqst['id']) : 0;

        $db = new DbConection();
        $pdo = $db->openConect();

        $q = "SELECT * FROM " . $db->getTable('tbl_usuarios') . " WHERE nickname = :nickname";

        if ($id > 0) {
            $q = "SELECT * FROM " . $db->getTable('tbl_usuarios') . " WHERE nickname = :nickname AND id != :id";
            $result = $pdo->prepare($q);
            $arr = array();
            $arrparam = array(":nickname" => $nickname, ":id" => $id);
        } else {
            $q = "SELECT * FROM " . $db->getTable('tbl_usuarios') . " WHERE nickname = :nickname";
            $result = $pdo->prepare($q);
            $arr = array();
            $arrparam = array(":nickname" => $nickname);
        }

        if ($result->execute($arrparam)) {
            foreach ($result as $valor) {
                $arr[] = $valor;
            }
            if (count($arr) > 0) {
                $arrjson = Util::error_general('El email de usuario ya existe');
            } else {
                $arrjson = array('output' => array('valid' => true, 'response' => 'available'));
            }
        } else {
            $arrjson = Util::error_general('');
        }
        $db->closeConect();
        return $arrjson;
    }

    public static function login($rqst)
    {
        // Obtención de parámetros de entrada
        $nickname = isset($rqst['nickname']) ? $rqst['nickname'] : '';
        $hashpass = isset($rqst['hashpass']) ? $rqst['hashpass'] : '';

        $db = new DbConection();
        $pdo = $db->openConect();

        // Si la contraseña tiene más de 2 caracteres, se realiza el hash
        if (strlen($hashpass) > 2) {
            $hashpass = Util::make_hash_pass($hashpass);
        }

        // Consulta para verificar usuario y contraseña
        $q = "SELECT * FROM " . $db->getTable('tbl_usuarios') . " WHERE nickname = :nickname AND hashpass = :hashpass AND habilitado='si'";
        $arrparam = [":nickname" => $nickname, ":hashpass" => $hashpass];

        $result = $pdo->prepare($q);
        if ($result->execute($arrparam)) {
            $arr = $result->fetchAll(PDO::FETCH_ASSOC);

            if (count($arr) > 0) {
                $user = $arr[0]; // Obtener el primer usuario encontrado
                $user['application'][] = Util::get_app_id();

                // Consultar perfiles asignados
                $q1 = "SELECT tbl_permiso_id FROM " . $db->getTable('tbl_usuarios_has_tbl_permisos') . " WHERE tbl_usuarios_id = :id ORDER BY tbl_permiso_id ASC";
                $result1 = $pdo->prepare($q1);
                $result1->execute([":id" => $user['id']]);
                $arrassigned = $result1->fetchAll(PDO::FETCH_COLUMN);


                // Consultar configuracion
                $qConfiguracion = "SELECT * FROM " . $db->getTable('tbl_configuracion') . " LIMIT 1";
                $resultConfiguracion = $pdo->prepare($qConfiguracion);
                $resultConfiguracion->execute();
                $configuracion = $resultConfiguracion->fetchAll(PDO::FETCH_ASSOC);

                $user['permisos'] = $arrassigned;

                // Resuelve permission_keys desde el rol del usuario
                // (tbl_usuarios.role_id), no desde el pivote legacy. Si el
                // usuario no tiene role_id, queda con permission_keys vacío
                // y solo el bypass de SuperAdministrador (por tipo) sigue
                // funcionando.
                require_once __DIR__ . '/Authorization.php';
                $permissionKeys = Authorization::loadPermissionKeys($pdo, $db, (int) $user['id']);
                $user['permission_keys'] = $permissionKeys;

                $arrjson = [
                    'output' => [
                        'valid' => true,
                        'response' => [$user],
                        'permisos' => $arrassigned,
                        'permission_keys' => $permissionKeys,
                        'configuracion' => $configuracion[0] ?? []
                    ]
                ];

                // Guardar información en la sesión
                Util::trace_session_user(['usuarioId' => $user['id']]);
            } else {
                $arrjson = Util::error_wrong_data_login();
            }
        } else {
            $arrjson = Util::error_wrong_data_login();
        }

        $db->closeConect();
        return $arrjson;
    }

    public static function save($rqst)
    {
        $id = isset($rqst['id']) ? intval($rqst['id']) : 0;
        $tbl_secretarias_id = isset($rqst['tbl_secretarias_id']) ? intval($rqst['tbl_secretarias_id']) : 0;
        $nickname = isset($rqst['nickname']) ? ($rqst['nickname']) : '';
        $hashpass = isset($rqst['hashpass']) ? ($rqst['hashpass']) : '';
        $nombre = isset($rqst['nombre']) ? ($rqst['nombre']) : '';
        $apellido = isset($rqst['apellido']) ? ($rqst['apellido']) : '';
        // El campo "tipo" del formulario envía el role_key del rol elegido
        // (poblado por Role::buildUsuarioRoleOptionsHtml()), no un string de
        // tipo fijo. Se resuelve aquí el role_id real y el valor legacy de
        // "tipo" (compatibilidad con SessionData::administrador(), etc., que
        // siguen comparando por ese string para los 7 roles de sistema). El
        // permiso real del usuario se resuelve en el login a partir de su
        // role_id (Authorization::loadPermissionKeys()), no aquí.
        $roleKeySubmitted = isset($rqst['tipo']) ? trim($rqst['tipo']) : '';
        $habilitado = isset($rqst['habilitado']) ? ($rqst['habilitado']) : '';
        $img = isset($_SESSION['file']['nombrearchivo']) ? ($_SESSION['file']['nombrearchivo']) : '';
        $tbl_departamento_id = isset($rqst['departamentoId']) ? ($rqst['departamentoId']) : Util::getDepartamentoPrincipal();
        $tbl_municipio_id = isset($rqst['tbl_municipio_id']) ? ($rqst['tbl_municipio_id']) : '';

        $db = new DbConection();
        $pdo = $db->openConect();

        $roleId = null;
        $tipo = $roleKeySubmitted;
        if ($roleKeySubmitted !== '') {
            $stmtRole = $pdo->prepare("SELECT id, role_key, name FROM " . $db->getTable('tbl_roles') . " WHERE role_key = :role_key");
            $stmtRole->execute([':role_key' => $roleKeySubmitted]);
            $roleRow = $stmtRole->fetch(PDO::FETCH_ASSOC);
            if (!$roleRow) {
                $db->closeConect();
                return Util::error_general('El rol seleccionado no existe.');
            }
            $roleId = (int) $roleRow['id'];
            $tipo = Role::legacyTipoForRoleKey($roleRow['role_key'], $roleRow['name']);
        }

        if (strlen($hashpass) > 2) {
            $hashpass = Util::make_hash_pass($hashpass);
        }
        if ($tbl_departamento_id == 0) {
            return  Util::error_general(' Identificador del departamento no está presente.');
        }
        if ($id > 0) {
            //actualiza la informacion
            $q0 = "SELECT id, img FROM " . $db->getTable('tbl_usuarios') . " WHERE id = " . $id;
            $result0 = $pdo->query($q0);
            if ($result0) {
                $table = $db->getTable('tbl_usuarios');
                $arrfieldscomma = array(
                    'nickname' => $nickname,
                    'hashpass' => $hashpass,
                    'nombre' => $nombre,
                    'apellido' => $apellido,
                    'tipo' => $tipo,
                    'role_id' => $roleId,
                    'img' => $img,
                    'tbl_secretarias_id' => $tbl_secretarias_id,
                    'habilitado' => $habilitado,
                    'tbl_departamento_id' => $tbl_departamento_id,
                    'tbl_municipio_id' => $tbl_municipio_id
                );
                $arrfieldsnocomma = array('dtcreate' => Util::date_now_server());
                $q = Util::make_query_update($table, "id = '$id'", $arrfieldscomma, $arrfieldsnocomma);
                $result = $pdo->query($q);

                // Obtemos el valor de la imagen del producto
                $file = "";
                foreach ($result0 as $valor0) {
                    $file = $valor0['img'];
                }

                if (!$result) {
                    $arrjson = Util::error_general('Actualizando los datos del usuario');
                } else {
                    $arrjson = array('output' => array('valid' => true, 'id' => $id, 'img' => $file));
                    // Eliminamos el archivo anterior siempre y cuando se halla actualizado la imagen
                    if ($file != "" && file_exists("../../assets/img/admin/usuarios/" . $file)) {
                        unlink("../../assets/img/admin/usuarios/" . $file);
                    }

                    // tbl_usuarios_has_tbl_permisos (tabla legacy) ya no se
                    // escribe: el permiso real se resuelve en el login a
                    // partir de role_id.
                }
            } else {
                $arrjson = Util::error_general();
            }
        } else {
            if ($nombre != "" && $apellido != "" && $tipo != "" && $tbl_departamento_id > 0 && $tbl_municipio_id > 0) {
                $q = "INSERT INTO " . $db->getTable('tbl_usuarios') . " (dtcreate, nickname, hashpass, nombre, apellido, tipo, role_id, img,  habilitado, tbl_departamento_id, tbl_municipio_id )
                VALUES ( " . Util::date_now_server() . ", :nickname, :hashpass, :nombre, :apellido, :tipo, :role_id, :img,  :habilitado, :tbl_departamento_id, :tbl_municipio_id)";
                $result = $pdo->prepare($q);
                $arrparam = array(
                    ':nickname' => $nickname,
                    ':hashpass' => $hashpass,
                    ':nombre' => $nombre,
                    ':apellido' => $apellido,
                    ':tipo' => $tipo,
                    ':role_id' => $roleId,
                    ':img' => $img,
                    ':habilitado' => $habilitado,
                    ':tbl_departamento_id' => $tbl_departamento_id,
                    ':tbl_municipio_id' => $tbl_municipio_id
                );
                if ($result->execute($arrparam)) {

                    $lastInsertId = $pdo->lastInsertId();

                    // tbl_usuarios_has_tbl_permisos (tabla legacy) ya no se
                    // escribe: el permiso real se resuelve en el login a
                    // partir de role_id.
                    $arrjson = ['output' => ['valid' => true, 'response' => $pdo->lastInsertId()]];
                } else {
                    $arrjson = Util::error_general('Ingresando los datos del usurario');
                }
            } else {
                $arrjson = Util::error_missing_data();
            }
        }
        $db->closeConect();
        return $arrjson;
    }

public static function actualizarPerfil($rqst)
    {
        try {
            $id = isset($rqst['id']) ? intval($rqst['id']) : 0;
            $nickname = isset($rqst['nickname']) ? trim($rqst['nickname']) : '';
            $hashpass = isset($rqst['hashpass']) ? trim($rqst['hashpass']) : '';
            $nombre = isset($rqst['nombre']) ? trim($rqst['nombre']) : '';
            $apellido = isset($rqst['apellido']) ? trim($rqst['apellido']) : '';
            $img = isset($_SESSION['file']['nombrearchivo']) ? ($_SESSION['file']['nombrearchivo']) : null;
    
            $db = new DbConection();
            $pdo = $db->openConect();

            if ($id <= 0) {
                return Util::error_general('ID de usuario no válido');
            }
    
            $q = "UPDATE " . $db->getTable('tbl_usuarios') . " SET 
                nickname = :nickname,
                nombre = :nombre,
                apellido = :apellido,
                img = :img";
            
            $params = [
                ':nickname' => $nickname,
                ':nombre' => $nombre,
                ':apellido' => $apellido,
                ':img' => $img
            ];
    
            if (!empty($hashpass) && strlen($hashpass) > 2) {
                $q .= ", hashpass = :hashpass";
                $params[':hashpass'] = Util::make_hash_pass($hashpass);
            }
    
            $q .= " WHERE id = :id";
            $params[':id'] = $id;
    
            $stmt = $pdo->prepare($q);

            $q0 = "SELECT img FROM " . $db->getTable('tbl_usuarios') . " WHERE id = :id";
            $stmt0 = $pdo->prepare($q0);
            $stmt0->execute([':id' => $id]);
            $oldImage = $stmt0->fetchColumn();

            if ($stmt->execute($params)) {

                if ($oldImage != "" && $img != $oldImage && file_exists("../../assets/img/admin/usuarios/" . $oldImage)) {
                    unlink("../../assets/img/admin/usuarios/" . $oldImage);
                }
    
                $arrjson = ['output' => ['valid' => true, 'response' => 'Perfil actualizado con éxito.']];
            } else {

                $arrjson = Util::error_general('Error al actualizar los datos del usuario.');
            }
    
            $db->closeConect();
            return $arrjson;
    
        } catch (PDOException $e) {

            return Util::error_general('Error de la base de datos: ' . $e->getMessage());
        }
    }
    

    public static function search($rqst){

        $search = isset($rqst['search']) ? ($rqst['search']) : '';

        $db = new DbConection();
        $pdo = $db->openConect();

        $q = "SELECT * FROM " . $db->getTable('tbl_usuarios') . " 
        WHERE nombre  LIKE '%$search%'  OR
            apellido  LIKE '%$search%' OR
            tipo  LIKE '%$search%' OR
            nickname  LIKE '%$search%' LIMIT 200 ";
        $result = $pdo->query($q);

        $arr = array();

        if ($result) {
            foreach ($result as $valor) {
                $arr[] = $valor;
            }
            $arrjson = array('output' => array('valid' => true, 'response' => $arr));
        } else {
            $arrjson = Util::error_no_result();
        }
        $db->closeConect();

        return $arrjson;

    }
}