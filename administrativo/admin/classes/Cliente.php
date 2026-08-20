<?php
class Cliente
{
    public function __construct()
    {
    }

    /**
     * Obtiene todos los clientes o un cliente específico por ID
     * @param array $rqst Array con parámetros opcionales (id)
     * @return array JSON con la respuesta
     */
    public static function getAll($rqst)
    {
        $id = isset($rqst['id']) ? intval($rqst['id']) : 0;

        $db = new DbConection();
        $pdo = $db->openConect();

        $q = "SELECT * FROM " . $db->getTable('tbl_clientes');
        $params = [];

        if ($id > 0) {
            $q .= " WHERE id = :id";
            $params[':id'] = $id;
        }

        $q .= " ORDER BY id DESC";

        try {
            $stmt = $pdo->prepare($q);
            $stmt->execute($params);
            $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($arr) {
                $arrjson = array('output' => array('valid' => true, 'response' => $arr));
            } else {
                $arrjson = array('output' => array('valid' => true, 'response' => []));
            }
        } catch (PDOException $e) {
            $arrjson = Util::error_general('Al obtener los datos de clientes.');
        } finally {
            $db->closeConect();
        }
        return $arrjson;
    }

    /**
     * Guarda o actualiza un cliente
     * @param array $rqst Array con los datos del cliente
     * @return array JSON con la respuesta
     */
    public static function save($rqst)
    {
        // Sanear y obtener los datos de la solicitud
        $id = isset($rqst['id']) ? intval($rqst['id']) : 0;
        $identificacion_tipo = isset($rqst['identificacion_tipo']) ? trim($rqst['identificacion_tipo']) : '';
        $identificacion_num = isset($rqst['identificacion_num']) ? trim($rqst['identificacion_num']) : '';
        $dv = isset($rqst['dv']) ? trim($rqst['dv']) : '';
        $nombre_completo = isset($rqst['nombre_completo']) ? trim($rqst['nombre_completo']) : '';
        $direccion = isset($rqst['direccion']) ? trim($rqst['direccion']) : '';
        $ubicacion = isset($rqst['ubicacion']) ? trim($rqst['ubicacion']) : '';
        $tipo_cliente = isset($rqst['tipo_cliente']) ? trim($rqst['tipo_cliente']) : '';
        $barrio = isset($rqst['barrio']) ? trim($rqst['barrio']) : '';
        $telefono = isset($rqst['telefono']) ? trim($rqst['telefono']) : '';
        $celular = isset($rqst['celular']) ? trim($rqst['celular']) : '';
        $email = isset($rqst['email']) ? trim($rqst['email']) : '';
        $tel_contacto = isset($rqst['tel_contacto']) ? trim($rqst['tel_contacto']) : '';
        $contacto = isset($rqst['contacto']) ? trim($rqst['contacto']) : '';
        $habilitado = isset($rqst['habilitado']) ? trim($rqst['habilitado']) : 'SI';
        $cumpleanos = isset($rqst['cumpleanos']) ? trim($rqst['cumpleanos']) : null;

        // Validaciones básicas
        if (empty($identificacion_tipo)) {
            return Util::error_missing_data_description('El tipo de identificación es requerido.');
        }
        if (empty($identificacion_num)) {
            return Util::error_missing_data_description('El número de identificación es requerido.');
        }
        if (empty($nombre_completo)) {
            return Util::error_missing_data_description('El nombre completo es requerido.');
        }
        if (empty($direccion)) {
            return Util::error_missing_data_description('La dirección es requerida.');
        }

        // Validar formato de email si se proporciona
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return Util::error_missing_data_description('El formato del email no es válido.');
        }

        // Verificar que no exista otro cliente con la misma identificación
        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            // Verificar duplicados
            $q_check = "SELECT id FROM " . $db->getTable('tbl_clientes') .
                       " WHERE identificacion_tipo = :tipo AND identificacion_num = :num";
            if ($id > 0) {
                $q_check .= " AND id != :id";
            }
            $stmt_check = $pdo->prepare($q_check);
            $params_check = [
                ':tipo' => $identificacion_tipo,
                ':num' => $identificacion_num
            ];
            if ($id > 0) {
                $params_check[':id'] = $id;
            }
            $stmt_check->execute($params_check);

            if ($stmt_check->fetch()) {
                $db->closeConect();
                return Util::error_registroduplicado('Ya existe un cliente con este tipo y número de identificación.');
            }

            if ($id > 0) {
                // --- UPDATE ---
                $table = $db->getTable('tbl_clientes');
                $q = "SELECT id FROM $table WHERE id = :id";
                $stmt = $pdo->prepare($q);
                $stmt->execute([':id' => $id]);

                if ($stmt->fetch()) {
                    $arrfieldscomma = array(
                        'identificacion_tipo' => $identificacion_tipo,
                        'identificacion_num' => $identificacion_num,
                        'dv' => $dv,
                        'nombre_completo' => $nombre_completo,
                        'direccion' => $direccion,
                        'ubicacion' => $ubicacion,
                        'tipo_cliente' => $tipo_cliente,
                        'barrio' => $barrio,
                        'telefono' => $telefono,
                        'celular' => $celular,
                        'email' => $email,
                        'tel_contacto' => $tel_contacto,
                        'contacto' => $contacto,
                        'habilitado' => $habilitado,
                        'cumpleanos' => $cumpleanos
                    );
                    $arrfieldsnocomma = array('dtcreate' => Util::date_now_server());
                    $q_update = Util::make_query_update($table, "id = '$id'", $arrfieldscomma, $arrfieldsnocomma);
                    $result = $pdo->query($q_update);

                    if (!$result) {
                        $arrjson = Util::error_general('Actualizando los datos del cliente.');
                    } else {
                        $arrjson = array('output' => array('valid' => true, 'id' => $id));
                    }
                } else {
                    $arrjson = Util::error_general('El cliente no existe.');
                }
            } else {
                // --- INSERT ---
                $q = "INSERT INTO " . $db->getTable('tbl_clientes') . "
                      (identificacion_tipo, identificacion_num, dv, nombre_completo, direccion, ubicacion,
                       tipo_cliente, barrio, telefono, celular, email, tel_contacto, contacto, habilitado,
                       cumpleanos, dtcreate)
                      VALUES
                      (:identificacion_tipo, :identificacion_num, :dv, :nombre_completo, :direccion, :ubicacion,
                       :tipo_cliente, :barrio, :telefono, :celular, :email, :tel_contacto, :contacto, :habilitado,
                       :cumpleanos, " . Util::date_now_server() . ")";

                $stmt = $pdo->prepare($q);
                $arrparam = array(
                    ':identificacion_tipo' => $identificacion_tipo,
                    ':identificacion_num' => $identificacion_num,
                    ':dv' => $dv,
                    ':nombre_completo' => $nombre_completo,
                    ':direccion' => $direccion,
                    ':ubicacion' => $ubicacion,
                    ':tipo_cliente' => $tipo_cliente,
                    ':barrio' => $barrio,
                    ':telefono' => $telefono,
                    ':celular' => $celular,
                    ':email' => $email,
                    ':tel_contacto' => $tel_contacto,
                    ':contacto' => $contacto,
                    ':habilitado' => $habilitado,
                    ':cumpleanos' => $cumpleanos
                );

                if ($stmt->execute($arrparam)) {
                    $arrjson = array('output' => array('valid' => true, 'response' => $pdo->lastInsertId()));
                } else {
                    $arrjson = Util::error_general('Error al insertar el cliente.');
                }
            }
        } catch (PDOException $e) {
            $arrjson = Util::error_general('Guardando cliente: ' . $e->getMessage());
        } finally {
            $db->closeConect();
        }
        return $arrjson;
    }

    /**
     * Elimina un cliente (borrado lógico o físico según necesidad)
     * @param array $rqst Array con el id del cliente
     * @return array JSON con la respuesta
     */
    public static function delete($rqst)
    {
        $id = isset($rqst['id']) ? intval($rqst['id']) : 0;

        if ($id <= 0) {
            return Util::error_missing_data();
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            // Opción 1: Borrado físico (elimina el registro)
            $q = "DELETE FROM " . $db->getTable('tbl_clientes') . " WHERE id = :id";

            // Opción 2: Borrado lógico (comentar línea anterior y descomentar estas)
            // $q = "UPDATE " . $db->getTable('tbl_clientes') . " SET habilitado = 'NO' WHERE id = :id";

            $stmt = $pdo->prepare($q);
            if ($stmt->execute([':id' => $id])) {
                $arrjson = array('output' => array('valid' => true));
            } else {
                $arrjson = Util::error_general('Error al eliminar el cliente.');
            }
        } catch (PDOException $e) {
            $arrjson = Util::error_general('Error al eliminar el registro: ' . $e->getMessage());
        } finally {
            $db->closeConect();
        }
        return $arrjson;
    }

    /**
     * Busca clientes por diferentes criterios
     * @param array $rqst Array con criterios de búsqueda
     * @return array JSON con la respuesta
     */
    public static function buscar($rqst)
    {
        $criterio = isset($rqst['criterio']) ? trim($rqst['criterio']) : '';

        if (empty($criterio)) {
            return Util::error_missing_data_description('Debe proporcionar un criterio de búsqueda.');
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $q = "SELECT * FROM " . $db->getTable('tbl_clientes') .
                 " WHERE nombre_completo LIKE :criterio
                   OR identificacion_num LIKE :criterio
                   OR email LIKE :criterio
                   OR telefono LIKE :criterio
                   OR celular LIKE :criterio
                   ORDER BY nombre_completo ASC";

            $stmt = $pdo->prepare($q);
            $stmt->execute([':criterio' => '%' . $criterio . '%']);
            $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $arrjson = array('output' => array('valid' => true, 'response' => $arr ? $arr : []));
        } catch (PDOException $e) {
            $arrjson = Util::error_general('Al buscar clientes.');
        } finally {
            $db->closeConect();
        }
        return $arrjson;
    }
}
