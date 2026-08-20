<?php

/**
 * Clase que contiene todas las operaciones utilizadas sobre la base de datos
 * @author SPIDERSOFTWARE
 */
class Linea
{

    public function __construct() {}

    public static function getAll($rqst)
    {
        $id = isset($rqst['id']) ? intval($rqst['id']) : 0;

        $db = new DbConection();
        $pdo = $db->openConect();

        $query = "SELECT * FROM " . $db->getTable('tbl_linea') . ($id > 0 ? " WHERE id = :id" : "");

        $stmt = $pdo->prepare($query);
        if ($id > 0) {
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        }
        $stmt->execute();

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $response = $data ? ['output' => ['valid' => true, 'response' => $data]] : Util::error_no_result();

        $db->closeConect();
        return $response;
    }

    public static function save($rqst)
    {
        $id = isset($rqst['id']) ? intval($rqst['id']) : 0;
        $nombre = isset($rqst['nombre']) ? trim($rqst['nombre']) : '';
        $descripcion = isset($rqst['descripcion']) ? trim($rqst['descripcion']) : '';
        $tec_usuario_id = $_SESSION['session_user']['id'] ?? 0;

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $pdo->beginTransaction();

            if ($id > 0) {
                // Verifica si el registro existe antes de actualizar
                $q = "SELECT id FROM " . $db->getTable('tbl_linea') . " WHERE id = :id";
                $stmt = $pdo->prepare($q);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    $table = $db->getTable('tbl_linea');
                    $arrfieldscomma = array(
                        'nombre' => $nombre,
                        'descripcion' => $descripcion,
                        'tec_usuario_id' => $tec_usuario_id
                    );
                    $arrfieldsnocomma = array('dtcreate' => Util::date_now_server());
                    $q = Util::make_query_update($table, "id = '$id'", $arrfieldscomma, $arrfieldsnocomma);

                    $result = $pdo->query($q);
                    if (!$result) {
                        throw new Exception('Error actualizando los datos del linea');
                    }

                    $arrjson = array('output' => array('valid' => true, 'id' => $id));
                } else {
                    throw new Exception('El registro no existe');
                }
            } else {
                if (!empty($nombre)) {
                    // Inserta un nuevo registro
                    $q = "INSERT INTO " . $db->getTable('tbl_linea') . " (dtcreate, nombre, descripcion, tec_usuario_id)
                        VALUES (:dtcreate, :nombre, :descripcion, :tec_usuario_id)";
                    $stmt = $pdo->prepare($q);
                    $stmt->execute([
                        ':dtcreate' => Util::date_now_server(),
                        ':nombre' => $nombre,
                        ':descripcion' => $descripcion,
                        ':tec_usuario_id' => $tec_usuario_id
                    ]);
                    $arrjson = array('output' => array('valid' => true, 'response' => $pdo->lastInsertId()));
                } else {
                    throw new Exception('Faltan datos obligatorios');
                }
            }

            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            $arrjson = Util::error_general($e->getMessage());
        } finally {
            $db->closeConect();
        }

        return $arrjson;
    }
}
