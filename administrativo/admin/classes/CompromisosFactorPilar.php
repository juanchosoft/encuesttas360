<?php

/**
 * Clase que contiene todas las operaciones utilizadas sobre la base de datos
 * @author SPIDERSOFTWARE
 */
class CompromisosFactorPilar
{

    public function __construct() {}


    public static function guardarCompromiso($rqst)
    {
        // Validar datos obligatorios
        if (empty($rqst['factorId']) || empty($rqst['cantidad']) || empty($rqst['actor']) || empty($rqst['codigo_departamento']) || empty($rqst['codigo_municipio']) || empty($rqst['tbl_vereda_id'])) {
            return Util::info_general('❌ Faltan datos obligatorios');
        }

        $codigo_departamento = intval($rqst['codigo_departamento']);
        $codigo_municipio = intval($rqst['codigo_municipio']);
        $tbl_vereda_id = intval($rqst['tbl_vereda_id']);
        $factorId = intval($rqst['factorId']);
        $cantidad = intval($rqst['cantidad']);
        $cantidadInstante = intval($rqst['cantidadActual']);
        $tbl_actor_id = intval($rqst['actor']);
        $observaciones = isset($rqst['observaciones']) ? trim($rqst['observaciones']) : '';
        $tec_usuario_id =  intval($_SESSION['session_user']['id']);

        if ($cantidad > $cantidadInstante) {
            return Util::info_general('❌ La cantidad de compromiso no puede ser mayor a la actual.');
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {

            $query = "INSERT INTO " . $db->getTable('tbl_compromisos_pilares_factores') . "  (dtcreate, tbl_factor_id, cantidad_instante, cantidad, observaciones, tbl_actor_id, codigo_departamento, codigo_municipio, tbl_vereda_id, tec_usuario_id) 
                    VALUES (" . Util::date_now_server() . ", :tbl_factor_id, :cantidad_instante, :cantidad, :observaciones, :tbl_actor_id, :codigo_departamento, :codigo_municipio, :tbl_vereda_id, :tec_usuario_id)";

            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':tbl_factor_id' => $factorId,
                ':cantidad_instante' => $cantidadInstante,
                ':cantidad' => $cantidad,
                ':observaciones' => $observaciones,
                ':tbl_actor_id' => $tbl_actor_id,
                ':codigo_departamento' => $codigo_departamento,
                ':codigo_municipio' => $codigo_municipio,
                ':tbl_vereda_id' => $tbl_vereda_id,
                ':tec_usuario_id' => $tec_usuario_id
            ]);
            return  array('output' => array('valid' => true, 'response' => $pdo->lastInsertId()));
        } catch (Exception $e) {
            return Util::error_general(' ❌ Guardando los datos de compromisos');
        } finally {
            $db->closeConect();
        }
    }




    public static function getCompromisosFactores($rqst)
    {

        $pilarId = intval($rqst['pilarId']);
        $veredaId = intval($rqst['veredaId']);

        $db = new DbConection();
        $pdo = $db->openConect();

        $q = "SELECT tbl_compromisos_pilares_factores.*, tbl_actores_mapa.nombre as actor,  tbl_factores.tipo as factor 
            FROM " . $db->getTable('tbl_compromisos_pilares_factores') .
            "," . $db->getTable('tbl_actores_mapa') .
            "," . $db->getTable('tbl_factores') . " 
            WHERE tbl_compromisos_pilares_factores.tbl_actor_id = tbl_actores_mapa.id AND
            tbl_compromisos_pilares_factores.tbl_factor_id = tbl_factores.id AND
            tbl_factores.tec_pilar_id = $pilarId AND tbl_compromisos_pilares_factores.tbl_vereda_id = $veredaId";

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
