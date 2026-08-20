<?php
class Sondeo
{
    public function __construct() {}

    public static function getAll($rqst)
    {
        $id = isset($rqst['id']) ? intval($rqst['id']) : 0;
        $db = new DbConection();
        $pdo = $db->openConect();
        $q = "SELECT * FROM " . $db->getTable('tbl_sondeo');
        $params = [];
        if ($id > 0) {
            $q .= " WHERE id = :id";
            $params[':id'] = $id;
        }
        try {
            $stmt = $pdo->prepare($q);
            $stmt->execute($params);
            $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Informacion de los candidatos a postular
            foreach ($arr as $key => $value) {
                $qSondeoCandidato = "SELECT * FROM " . $db->getTable('tbl_sondeo_x_tbl_participantes') . "
                WHERE tbl_sondeo_id = :id";

                $qSondeoCandidato = "SELECT
                    p.id, p.tbl_cargo_publico_id, p.nombre_completo, p.codigo_departamento, p.codigo_municipio, p.dtcreate, p.foto, p.habilitado,
                    cp.nombre AS cargo_publico, cp.sigla AS sigla_cargo,
                    d.departamento AS nombre_departamento,
                    c.municipio AS nombre_municipio,
                    p.habilitado,
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
                $params[':id'] = $value['id'];
                $stmtSondeoCandidato = $pdo->prepare($qSondeoCandidato);
                $stmtSondeoCandidato->execute($params);
                $arrSondeoCandidato = $stmtSondeoCandidato->fetchAll(PDO::FETCH_ASSOC);
                $arr[$key]['candidatos'] = $arrSondeoCandidato;

                // Opciones del sondeo
                $qSondeOpciones = "SELECT id, opcion FROM " . $db->getTable('tbl_sondeo_x_opciones') . " WHERE tbl_sondeo_id = :id";
                $params[':id'] = $value['id'];
                $stmtSondeOpciones = $pdo->prepare($qSondeOpciones);
                $stmtSondeOpciones->execute($params);
                $arrSondeOpciones = $stmtSondeOpciones->fetchAll(PDO::FETCH_ASSOC);
                $arr[$key]['opciones'] = $arrSondeOpciones;

                // Calcular si el sondeo está vigente
                $arr[$key]['vigente'] = Sondeo::isVigente($value['fecha_inicio'], $value['fecha_fin']);
            }

            $arrjson = array('output' => array('valid' => true, 'response' => $arr ? $arr : []));
        } catch (PDOException $e) {
            $arrjson = Util::error_general('Al obtener los datos de Sondeo.');
        } finally {
            $db->closeConect();
        }
        return $arrjson;
    }

    /**
     *  Metodo para guardar la informacion de sondeo
     * @param array $rqst
     */
    public static function save($rqst)
    {
        $id = isset($rqst['id']) ? intval($rqst['id']) : 0;
        $sondeo = isset($rqst['sondeo']) ? trim($rqst['sondeo']) : '';
        $descripcion_sondeo = isset($rqst['descripcion_sondeo']) ? trim($rqst['descripcion_sondeo']) : '';
        $tipo_sondeo = isset($rqst['tipo_sondeo']) ? trim($rqst['tipo_sondeo']) : '';
        $tipo_inferenciales = isset($rqst['tipo_inferenciales']) ? trim($rqst['tipo_inferenciales']) : '';
        $aplica_cargos_publicos = isset($rqst['aplica_cargos_publicos']) ? trim($rqst['aplica_cargos_publicos']) : '';
        $codigo_departamento = isset($rqst['codigo_departamento']) ? trim($rqst['codigo_departamento']) : '';
        $codigo_municipio = isset($rqst['codigo_municipio']) ? trim($rqst['codigo_municipio']) : '';
        $tbl_cargo_publico_id = isset($rqst['tbl_cargo_publico_id']) ? trim($rqst['tbl_cargo_publico_id']) : '';
        $habilitado = isset($rqst['habilitado']) ? trim($rqst['habilitado']) : '';
        $fecha_inicio = isset($rqst['fecha_inicio']) ? trim($rqst['fecha_inicio']) : null;
        $fecha_fin = isset($rqst['fecha_fin']) ? trim($rqst['fecha_fin']) : null;
        $es_trivia = isset($rqst['es_trivia']) ? trim($rqst['es_trivia']) : 'no';
        $candidatos = isset($rqst['candidatos']) ? $rqst['candidatos'] : [];
        $opciones = isset($rqst['opciones']) ? $rqst['opciones'] : [];

        if (empty($sondeo)) {
            return Util::error_missing_data_description('El campo "Sondeo" es requerido.');
        }
        if (empty($tipo_sondeo)) {
            return Util::error_missing_data_description('El campo "Tipo de Sondeo" es requerido.');
        }
        if (empty($tipo_inferenciales)) {
            return Util::error_missing_data_description('El campo "Tipo de Inferenciales" es requerido.');
        }
        if (empty($aplica_cargos_publicos)) {
            return Util::error_missing_data_description('El campo "Aplica a Cargos Públicos" es requerido.');
        }
        if (empty($habilitado)) {
            return Util::error_missing_data_description('El campo "Habilitado" es requerido.');
        }
        if ($aplica_cargos_publicos == 'si' && empty($tbl_cargo_publico_id)) {
            return Util::error_missing_data_description('El campo "Cargo Público" es requerido.');
        }
        if ($aplica_cargos_publicos == 'si' && empty($candidatos)) {
            return Util::error_missing_data_description('Debes seleccionar candidatos a postular para este sondeo: ' . $sondeo);
        }
        if ($tipo_sondeo != 'No Aplica') {
            if (empty($opciones)) {
                return Util::error_missing_data_description('Debes agregar opciones para este sondeo: ' . $sondeo);
            }
        }
        if ($tipo_sondeo == 'No Aplica') {
            if (empty($candidatos)) {
                return Util::error_missing_data_description('Debes seleccionar candidatos, como no aplica para opciones ' . $sondeo);
            }
        }
        if ($tipo_sondeo == 'Dicotomica') {
            if (!empty($opciones) && count($opciones) != 2) {
                return Util::error_missing_data_description('El sondeo de tipo "Dicotómica" debe tener exactamente dos opciones.');
            }
        }

        if ($aplica_cargos_publicos == 'no') {
            $codigo_departamento = null;
            $codigo_municipio = null;
        }


        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $pdo->beginTransaction();

            if ($id > 0) {
                $table = $db->getTable('tbl_sondeo');
                $arrfieldscomma = [
                    'sondeo' => $sondeo,
                    'descripcion_sondeo' => $descripcion_sondeo,
                    'tipo_sondeo' => $tipo_sondeo,
                    'tipo_inferenciales' => $tipo_inferenciales,
                    'aplica_cargos_publicos' => $aplica_cargos_publicos,
                    'codigo_departamento' => $codigo_departamento,
                    'codigo_municipio' => $codigo_municipio,
                    'tbl_cargo_publico_id' => $tbl_cargo_publico_id,
                    'habilitado' => $habilitado,
                    'fecha_inicio' => $fecha_inicio,
                    'fecha_fin' => $fecha_fin,
                    'es_trivia' => $es_trivia,
                ];
                $arrfieldsnocomma = array();
                $q_update = Util::make_query_update($table, "id = '$id'", $arrfieldscomma, $arrfieldsnocomma);

                $pdo->query($q_update);
                $arrjson = array('output' => array('valid' => true, 'id' => $id));

                Sondeo::saveParticipantes($rqst);

                Sondeo::saveSondeoOpciones($rqst);
            } else {

                $q = "INSERT INTO " . $db->getTable('tbl_sondeo') . " (sondeo, descripcion_sondeo, tipo_sondeo, tipo_inferenciales, aplica_cargos_publicos, codigo_departamento, codigo_municipio, tbl_cargo_publico_id, dtcreate, habilitado, fecha_inicio, fecha_fin, es_trivia) VALUES (:sondeo, :descripcion_sondeo, :tipo_sondeo, :tipo_inferenciales, :aplica_cargos_publicos, :codigo_departamento, :codigo_municipio, :tbl_cargo_publico_id, :dtcreate, :habilitado, :fecha_inicio, :fecha_fin, :es_trivia)";
                $stmt = $pdo->prepare($q);
                $arrparam = [
                    ':sondeo' => $sondeo,
                    ':descripcion_sondeo' => $descripcion_sondeo,
                    ':tipo_sondeo' => $tipo_sondeo,
                    ':tipo_inferenciales' => $tipo_inferenciales,
                    ':aplica_cargos_publicos' => $aplica_cargos_publicos,
                    ':codigo_departamento' => $codigo_departamento,
                    ':codigo_municipio' => $codigo_municipio,
                    ':tbl_cargo_publico_id' => $tbl_cargo_publico_id,
                    ':dtcreate' => Util::date(),
                    ':habilitado' => $habilitado,
                    ':fecha_inicio' => $fecha_inicio,
                    ':fecha_fin' => $fecha_fin,
                    ':es_trivia' => $es_trivia,
                ];

                $stmt->execute($arrparam);
                $id = $pdo->lastInsertId();

                $rqst['id'] = $id;

                Sondeo::saveParticipantes($rqst);

                if ($tipo_sondeo != 'No Aplica') {
                    Sondeo::saveSondeoOpciones($rqst);
                }
                $arrjson = array('output' => array('valid' => true, 'response' => $id));
            }
            $pdo->commit();
        } catch (PDOException $e) {
            // print_r($e);
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $arrjson = Util::error_general('Guardando datos en Sondeo');
        } finally {
            $db->closeConect();
        }
        return $arrjson;
    }

    /** 
     * Metodo para guardar los participantes del sondeo
     * @param array $rqst
     */
    public static function saveParticipantes($rqst)
    {

        $db = new DbConection();
        $pdo = $db->openConect();

        $id = isset($rqst['id']) ? intval($rqst['id']) : 0;
        $aplica_cargos_publicos = isset($rqst['aplica_cargos_publicos']) ? trim($rqst['aplica_cargos_publicos']) : '';
        $candidatos = isset($rqst['candidatos']) ? $rqst['candidatos'] : [];

        // Información de participantes del sondeo
        if ($aplica_cargos_publicos == 'si' && !empty($candidatos)) {

            $qDelete = "DELETE FROM " . $db->getTable('tbl_sondeo_x_tbl_participantes') . " WHERE tbl_sondeo_id = :tbl_sondeo_id";
            $stmtDelete = $pdo->prepare($qDelete);
            $stmtDelete->execute([':tbl_sondeo_id' => $id]);
            if (!$stmtDelete) {
                throw new Exception('Error al eliminar los candidatos del sondeo.');
            }

            $qSondeParticipantes = "INSERT INTO " . $db->getTable('tbl_sondeo_x_tbl_participantes') . " (tbl_sondeo_id, tbl_participante_id, dtcreate) VALUES (:tbl_sondeo_id, :tbl_participante_id, :dtcreate)";
            $stmtSondeParticipantes = $pdo->prepare($qSondeParticipantes);
            foreach ($candidatos as $candidato) {
                $arrparam = [
                    ':tbl_sondeo_id' => $id,
                    ':tbl_participante_id' => $candidato,
                    ':dtcreate' => Util::date(),
                ];
                $stmtSondeParticipantes->execute($arrparam);
                if (!$stmtSondeParticipantes) {
                    throw new Exception('Error al guardar los candidatos del sondeo.');
                }
            }
        }
        $db->closeConect();
    }

    /**
     * Metodo para guardar las opciones del sondeo
     * @param array $rqst
     */
    public static function saveSondeoOpciones($rqst)
    {

        $id = isset($rqst['id']) ? intval($rqst['id']) : 0;
        $opciones = isset($rqst['opciones']) ? $rqst['opciones'] : [];

        if ($id <= 0) {
            throw new Exception('El id del sondeo es requerido.');
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        // Información de opciones del sondeo
        $qDelete = "DELETE FROM " . $db->getTable('tbl_sondeo_x_opciones') . " WHERE tbl_sondeo_id = :tbl_sondeo_id";
        $stmtDelete = $pdo->prepare($qDelete);
        $stmtDelete->execute([':tbl_sondeo_id' => $id]);
        if (!$stmtDelete) {
            throw new Exception('Error al eliminar las opciones del sondeo.');
        }

        $qSondeOpciones = "INSERT INTO " . $db->getTable('tbl_sondeo_x_opciones') . " (tbl_sondeo_id, opcion, dtcreate) VALUES (:tbl_sondeo_id, :opcion, :dtcreate)";
        $stmtSondeOpciones = $pdo->prepare($qSondeOpciones);
        foreach ($opciones as $opcion) {
            if ($opcion == "") {
                throw new Exception('Ninguna opción no puede estar vacía.');
            }
            $arrparam = [
                ':tbl_sondeo_id' => $id,
                ':opcion' => $opcion,
                ':dtcreate' => Util::date(),
            ];
            $stmtSondeOpciones->execute($arrparam);
            if (!$stmtSondeOpciones) {
                throw new Exception('Error al guardar la opción del sondeo.');
            }
        }
        $db->closeConect();
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
            $q = "DELETE FROM " . $db->getTable('tbl_sondeo') . " WHERE id = :id";
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

    /**
     * Activa o desactiva un sondeo. Si se activa, desactiva todos los demás.
     * Solo puede haber UN sondeo habilitado a la vez.
     * @param array $rqst Datos de la solicitud
     * @return array Resultado de la operación
     */
    public static function toggleHabilitado($rqst)
    {
        $id = isset($rqst['id']) ? intval($rqst['id']) : 0;
        $habilitado = isset($rqst['habilitado']) ? trim($rqst['habilitado']) : 'no';

        if ($id <= 0) {
            return Util::error_missing_data_description('ID de sondeo requerido');
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $pdo->beginTransaction();

            // Si se está habilitando, primero desactivar TODOS los sondeos
            if ($habilitado === 'si') {
                $qDesactivar = "UPDATE " . $db->getTable('tbl_sondeo') . " SET habilitado = 'no'";
                $pdo->exec($qDesactivar);
            }

            // Ahora actualizar el sondeo específico
            $qUpdate = "UPDATE " . $db->getTable('tbl_sondeo') . "
                        SET habilitado = :habilitado
                        WHERE id = :id";
            $stmt = $pdo->prepare($qUpdate);
            $stmt->execute([
                ':habilitado' => $habilitado,
                ':id' => $id
            ]);

            $pdo->commit();
            $db->closeConect();

            $mensaje = $habilitado === 'si'
                ? 'Sondeo activado correctamente. Los demás sondeos han sido desactivados.'
                : 'Sondeo desactivado correctamente.';

            return [
                'output' => [
                    'valid' => true,
                    'message' => $mensaje,
                    'habilitado' => $habilitado
                ]
            ];

        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $db->closeConect();
            return Util::error_general('Error al cambiar estado del sondeo: ' . $e->getMessage());
        }
    }

    /**
     * Verifica si un sondeo está vigente según sus fechas
     * @param string|null $fecha_inicio Fecha de inicio del sondeo
     * @param string|null $fecha_fin Fecha de fin del sondeo
     * @return bool True si está vigente, false si no
     */
    public static function isVigente($fecha_inicio, $fecha_fin)
    {
        // Si no hay fechas definidas, no se puede determinar vigencia
        if (empty($fecha_inicio) && empty($fecha_fin)) {
            return false;
        }

        $fecha_actual = date('Y-m-d');

        // Si solo hay fecha de inicio
        if (!empty($fecha_inicio) && empty($fecha_fin)) {
            return $fecha_actual >= $fecha_inicio;
        }

        // Si solo hay fecha fin
        if (empty($fecha_inicio) && !empty($fecha_fin)) {
            return $fecha_actual <= $fecha_fin;
        }

        // Si hay ambas fechas, verificar que esté en el rango
        return $fecha_actual >= $fecha_inicio && $fecha_actual <= $fecha_fin;
    }
}
