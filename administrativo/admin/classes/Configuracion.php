<?php

/**
 * Clase que contiene todas las operaciones utilizadas sobre la base de datos
 * @author SPIDERSOFTWARE
 */
class Configuracion
{

    public function __construct() {}


    public static function getAll($rqst)
    {
        $id = isset($rqst['id']) ? intval($rqst['id']) : 0;

        $db = new DbConection();
        $pdo = $db->openConect();

        $q = "SELECT * FROM " . $db->getTable('tbl_configuracion') . " ORDER BY id DESC LIMIT 1";

        if ($id > 0) {
            $q = "SELECT * FROM " . $db->getTable('tbl_configuracion') . " WHERE id = " . $id . " LIMIT 1";
        }
        $result = Util::sb_db_get($q);
        $arr = array();
        if (!empty($result)) {
            foreach ($result as $valor) {
                $arr[] = $valor;
            }
            $arrjson = array('output' => array('valid' => true, 'response' => [$result]));
        } else {
            $arrjson = Util::error_no_result();
        }
        $db->closeConect();

        return $arrjson;
    }
    public static function getCodigoDepartamentoConfiguracion($rqst)
    {
        $db = new DbConection();
        $pdo = $db->openConect();

        $q = "SELECT codigo_departamento FROM " . $db->getTable('tbl_configuracion') . " ORDER BY id DESC LIMIT 1";
        $result = Util::sb_db_get($q);

        $db->closeConect();

        if (!empty($result) && isset($result[0]['codigo_departamento'])) {
            return $result[0]['codigo_departamento'];
        } else {
            return null;
        }
    }

    public static function save($rqst)
    {
        $id = isset($rqst['id']) ? intval($rqst['id']) : 0;
        $nombre_proyecto = isset($rqst['nombre_proyecto']) ? ($rqst['nombre_proyecto']) : '';
        $codigo_departamento = isset($rqst['codigo_departamento']) ? ($rqst['codigo_departamento']) : 0;
        $codigo_municipio = isset($rqst['codigo_municipio']) ? ($rqst['codigo_municipio']) : 0;
        $tbl_vereda_id = isset($rqst['tbl_vereda_id']) ? intval($rqst['tbl_vereda_id']) : 0;
        $logo = $rqst['logo'] ?? '';
        $opcion_activa_web = isset($rqst['opcion_activa_web']) ? ($rqst['opcion_activa_web']) : 'sondeo';

        $tipo_configuracion_colores = isset($rqst['tipo_configuracion_colores']) ? ($rqst['tipo_configuracion_colores']) : '';
        $comentarios = isset($rqst['comentarios']) ? ($rqst['comentarios']) : '';

/*         if($tipo_configuracion_colores == ""){
            $arrjson = Util::error_general('El campo Tipo Configuración Colores es obligatorio');
            return $arrjson;
        } */

        if($nombre_proyecto == ""){
            $arrjson = Util::error_general(' Nombre del proyecto');
            return $arrjson;
        }
        
        if($opcion_activa_web == ""){
            $arrjson = Util::error_general('Opción activa web es obligatorio');
            return $arrjson;
        }

        if($codigo_departamento == 0){
            $arrjson = Util::error_general('Departamento es obligatorio');
            return $arrjson;
        }
        if($codigo_municipio == 0){
            $arrjson = Util::error_general('Municipio es obligatorio');
            return $arrjson;
        }
        if($tbl_vereda_id == 0){
            $arrjson = Util::error_general('Vereda es obligatorio');
            return $arrjson;
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        if ($id > 0) {
            $q = "SELECT id  FROM " . $db->getTable('tbl_configuracion') . " WHERE id = " . $id;
            $result = $pdo->query($q);
            if ($result) {
                $table = $db->getTable('tbl_configuracion');
                $arrfieldscomma = array(
                    'tipo_configuracion_colores' => $tipo_configuracion_colores,
                    'comentarios' => $comentarios,
                    'codigo_departamento' => $codigo_departamento,
                    'codigo_municipio' => $codigo_municipio,
                    'tbl_vereda_id' => $tbl_vereda_id,
                    'logo' => $logo,
                    'nombre_proyecto' => $nombre_proyecto,
                    'opcion_activa_web' => $opcion_activa_web
                );
                $arrfieldsnocomma = array('dtcreate' => Util::date_now_server());
                $q = Util::make_query_update($table, "id = '$id'", $arrfieldscomma, $arrfieldsnocomma);

                $result = $pdo->query($q);
                if (!$pdo->query($q)) {
                    $arrjson = Util::error_general('Actualizando las Configuraciones del sistema');
                } else {
                    $arrjson = array('output' => array('valid' => true, 'id' => $id));
                }
            }
            $db->closeConect();
            return $arrjson;
        }else{
            return  Util::error_missing_data();
        }

    }
}
