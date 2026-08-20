<?php

/**
 * Clase que contiene todas las operaciones utilizadas sobre la base de datos
 * @author SPIDERSOFTWARE
 */
class Pilar {

    public function __construct(){}


    public static function getAll($rqst)
{
    $id = isset($rqst['id']) ? intval($rqst['id']) : 0;
    $ejeId = isset($rqst['ejeId']) ? intval($rqst['ejeId']) : 0;

    $db = new DbConection();
    $pdo = $db->openConect();

    // Construir la consulta base
    $q = "SELECT * FROM " . $db->getTable('tbl_pilar');

    if ($id > 0) {
        $q .= " WHERE id = " . $id;
    } elseif ($ejeId > 0) {
        $q .= " WHERE tbl_ejes_id = " . $ejeId;
    }

   
    $q .= " ORDER BY nombre ASC"; 
    // Ejecutar la consulta
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
