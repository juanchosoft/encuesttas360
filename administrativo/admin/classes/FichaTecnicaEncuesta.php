<?php
class FichaTecnicaEncuesta
{
    public function __construct() {}

    /**
     * Convierte valor Z almacenado (1.95, 1.99…) al % de confianza mostrado (95, 99…).
     */
    public static function confianzaDesdeZ($valorZ)
    {
        $map = [
            '1.95' => 95,
            '1.99' => 99,
            '1.90' => 90,
            '1.85' => 85,
        ];
        $key = number_format((float)$valorZ, 2, '.', '');
        if (isset($map[$key])) {
            return $map[$key];
        }
        foreach ($map as $z => $pct) {
            if (abs((float)$z - (float)$valorZ) < 0.001) {
                return $pct;
            }
        }
        return (float)$valorZ;
    }

    /**
     * Confianza (%) + margen de error (%) no pueden superar 100.
     */
    public static function validarConfianzaMargen($valorZ, $margenError)
    {
        $confianza = self::confianzaDesdeZ($valorZ);
        $margen = round((float)$margenError, 2);
        if ($confianza <= 0 || $margen <= 0) {
            return true;
        }
        $suma = round($confianza + $margen, 2);
        if ($suma > 100.5) {
            $maximo = round(100 - $confianza, 2);
            return Util::error_missing_data_description(
                'Nivel de confiabilidad (' . $confianza . '%) y margen de error (' . $margen
                . '%) no pueden superar 100% en conjunto. Con ' . $confianza . '% de confianza el margen máximo es '
                . $maximo . '%. Ajuste el tamaño de muestra.'
            );
        }
        return true;
    }

    /**
     * Indica si la ficha está en uso (preguntas, respuestas o grillas activas).
     */
    public static function estaEnUso($fichaId)
    {
        $fichaId = (int)$fichaId;
        if ($fichaId <= 0) {
            return false;
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $checks = [
                "SELECT COUNT(*) FROM " . $db->getTable('tbl_preguntas')
                    . " WHERE tbl_ficha_tecnica_encuesta_id = :id AND (habilitado = 'si' OR habilitado IS NULL)",
                "SELECT COUNT(*) FROM " . $db->getTable('tbl_cuestionario_intentos')
                    . " WHERE tbl_ficha_tecnica_encuesta_id = :id",
                "SELECT COUNT(*) FROM " . $db->getTable('tbl_grilla')
                    . " WHERE tbl_ficha_tecnica_encuesta_id = :id AND (habilitado = 'si' OR habilitado IS NULL)",
            ];

            foreach ($checks as $sql) {
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':id' => $fichaId]);
                if ((int)$stmt->fetchColumn() > 0) {
                    return true;
                }
            }
            return false;
        } catch (Exception $e) {
            return true;
        } finally {
            $db->closeConect();
        }
    }

    public static function getAll($rqst)
    {
        $rqst = is_array($rqst) ? $rqst : [];
        $id = isset($rqst['id']) ? intval($rqst['id']) : 0;
        $incluirEliminados = !empty($rqst['incluir_eliminados']);
        $soloHabilitados = !empty($rqst['solo_habilitados']);
        $db = new DbConection();
        $pdo = $db->openConect();
        $q = "SELECT * FROM " . $db->getTable('tbl_ficha_tecnica_encuestas');
        $params = [];
        $where = [];

        if ($id > 0) {
            $where[] = "id = :id";
            $params[':id'] = $id;
        }

        if (!$incluirEliminados) {
            $where[] = "(eliminado = 'no' OR eliminado IS NULL)";
        }

        if ($soloHabilitados) {
            $where[] = "(habilitado = 'si' OR habilitado IS NULL)";
        }

        if (!empty($where)) {
            $q .= " WHERE " . implode(' AND ', $where);
        }
        $q .= " ORDER BY id DESC";
        try {
            $stmt = $pdo->prepare($q);
            $stmt->execute($params);
            $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $arrjson = array('output' => array('valid' => true, 'response' => $arr ? $arr : []));
        } catch (PDOException $e) {
            $arrjson = Util::error_general('Al obtener los datos de FichaTecnicaEncuesta.');
        } finally {
            $db->closeConect();
        }
        return $arrjson;
    }

    /**
     * Listado de fichas técnicas con conteo de preguntas (hub cuestionarios).
     */
    public static function getAllConResumenPreguntas($rqst = [])
    {
        $rqst = is_array($rqst) ? $rqst : [];
        $soloHabilitados = !empty($rqst['solo_habilitados']);

        $db = new DbConection();
        $pdo = $db->openConect();

        $where = ["(f.eliminado = 'no' OR f.eliminado IS NULL)"];
        if ($soloHabilitados) {
            $where[] = "(f.habilitado = 'si' OR f.habilitado IS NULL)";
        }

        $q = "SELECT f.*,
                COUNT(p.id) AS total_preguntas,
                SUM(CASE WHEN p.habilitado = 'si' THEN 1 ELSE 0 END) AS preguntas_activas
              FROM " . $db->getTable('tbl_ficha_tecnica_encuestas') . " f
              LEFT JOIN " . $db->getTable('tbl_preguntas') . " p
                ON p.tbl_ficha_tecnica_encuesta_id = f.id
              WHERE " . implode(' AND ', $where) . "
              GROUP BY f.id
              ORDER BY f.id DESC";

        try {
            $stmt = $pdo->prepare($q);
            $stmt->execute();
            $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return ['output' => ['valid' => true, 'response' => $arr ? $arr : []]];
        } catch (PDOException $e) {
            return Util::error_general('Al obtener el listado de cuestionarios.');
        } finally {
            $db->closeConect();
        }
    }

    public static function save($rqst)
    {
        $id = isset($rqst['id']) ? intval($rqst['id']) : 0;
        $realizada_por_o_encomendada_por = isset($rqst['realizada_por_o_encomendada_por']) ? trim($rqst['realizada_por_o_encomendada_por']) : '';
        $fuente_financiacion = isset($rqst['fuente_financiacion']) ? trim($rqst['fuente_financiacion']) : '';
        $tipo_tamano_muestra_y_procedimiento_utilizado = isset($rqst['tipo_tamano_muestra_y_procedimiento_utilizado']) ? trim($rqst['tipo_tamano_muestra_y_procedimiento_utilizado']) : '';
        $temas_concretos = isset($rqst['temas_concretos']) ? trim($rqst['temas_concretos']) : '';
        $texto_literal_de_la_encuesta_o_preguntas = isset($rqst['texto_literal_de_la_encuesta_o_preguntas']) ? trim($rqst['texto_literal_de_la_encuesta_o_preguntas']) : '';
        $candidatos_personas_instituciones_indagados = isset($rqst['candidatos_personas_instituciones_indagados']) ? trim($rqst['candidatos_personas_instituciones_indagados']) : '';
        $espacio_geografico_fecha_o_periodo_que_se_realizo = isset($rqst['espacio_geografico_fecha_o_periodo_que_se_realizo']) ? trim($rqst['espacio_geografico_fecha_o_periodo_que_se_realizo']) : '';
        $margen_error_porcentaje = isset($rqst['margen_error_porcentaje']) ? floatval($rqst['margen_error_porcentaje']) : 0.0;
        $tipo_estudio = isset($rqst['tipo_estudio']) ? trim($rqst['tipo_estudio']) : 'na';
        $proposito_del_estudio = isset($rqst['proposito_del_estudio']) ? trim($rqst['proposito_del_estudio']) : '';
        $universo_representado = isset($rqst['universo_representado']) ? trim($rqst['universo_representado']) : '';
        $metodo_recoleccion = isset($rqst['metodo_recoleccion']) ? trim($rqst['metodo_recoleccion']) : '';
        $nivel_confiabilidad_porcentaje = isset($rqst['nivel_confiabilidad_porcentaje']) ? floatval($rqst['nivel_confiabilidad_porcentaje']) : 0.0;
        $estadisticos_responsables = isset($rqst['estadisticos_responsables']) ? trim($rqst['estadisticos_responsables']) : '';
        $declaracion = isset($rqst['declaracion']) ? trim($rqst['declaracion']) : '';
        $avisos = isset($rqst['avisos']) ? trim($rqst['avisos']) : '';
        $habilitado = isset($rqst['habilitado']) ? ($rqst['habilitado']) : '';

        // Campos adicionales
        $tipo_estudio_descripcion = isset($rqst['tipo_estudio_descripcion']) ? ($rqst['tipo_estudio_descripcion']) : '';
        $tipo_tamano_muestra_y_procedimiento_utilizado_descripcion = isset($rqst['tipo_tamano_muestra_y_procedimiento_utilizado_descripcion']) ? ($rqst['tipo_tamano_muestra_y_procedimiento_utilizado_descripcion']) : '';
        $tamano_muestra = isset($rqst['tamano_muestra']) ? intval($rqst['tamano_muestra']) : 0;
        $procedimiento_utilizado = isset($rqst['procedimiento_utilizado']) ? ($rqst['procedimiento_utilizado']) : '';
        $espacio_geografico_fecha = isset($rqst['espacio_geografico_fecha']) ? ($rqst['espacio_geografico_fecha']) : '';
        $espacio_geografico_fecha_estado = isset($rqst['espacio_geografico_fecha_estado']) ? ($rqst['espacio_geografico_fecha_estado']) : '';
        $tipo_encuesta = isset($rqst['tipo_encuesta']) ? ($rqst['tipo_encuesta']) : '';
        $tbl_espacio_geografico_id = isset($rqst['tbl_espacio_geografico_id']) ? intval($rqst['tbl_espacio_geografico_id']) : 0;
        $poblacion_objetivo = isset($rqst['poblacion_objetivo']) ? ($rqst['poblacion_objetivo']) : 'habitantes';

        $tbl_usuario_id =  intval($_SESSION['session_user']['id']);

        if (empty($realizada_por_o_encomendada_por)) {
            return Util::error_missing_data_description('El campo "Realizada por o encomendada por" es requerido.');
        }

        $validacionConfMargen = self::validarConfianzaMargen($nivel_confiabilidad_porcentaje, $margen_error_porcentaje);
        if ($validacionConfMargen !== true) {
            return $validacionConfMargen;
        }

        if ($habilitado === '') {
            $habilitado = 'si';
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $pdo->beginTransaction();

            if ($id > 0) {
                $table = $db->getTable('tbl_ficha_tecnica_encuestas');
                $arrfieldscomma = [
                    'realizada_por_o_encomendada_por' => $realizada_por_o_encomendada_por,
                    'fuente_financiacion' => $fuente_financiacion,
                    'tipo_tamano_muestra_y_procedimiento_utilizado' => $tipo_tamano_muestra_y_procedimiento_utilizado,
                    'temas_concretos' => $temas_concretos,
                    'texto_literal_de_la_encuesta_o_preguntas' => $texto_literal_de_la_encuesta_o_preguntas,
                    'candidatos_personas_instituciones_indagados' => $candidatos_personas_instituciones_indagados,
                    'espacio_geografico_fecha_o_periodo_que_se_realizo' => $espacio_geografico_fecha_o_periodo_que_se_realizo,
                    'margen_error_porcentaje' => $margen_error_porcentaje,
                    'tipo_estudio' => $tipo_estudio,
                    'proposito_del_estudio' => $proposito_del_estudio,
                    'universo_representado' => $universo_representado,
                    'metodo_recoleccion' => $metodo_recoleccion,
                    'nivel_confiabilidad_porcentaje' => $nivel_confiabilidad_porcentaje,
                    'estadisticos_responsables' => $estadisticos_responsables,
                    'declaracion' => $declaracion,
                    'avisos' => $avisos,
                    'tbl_usuario_id' => $tbl_usuario_id,
                    'habilitado' => $habilitado,
                    // Campos adicionales
                    'tipo_estudio_descripcion' => $tipo_estudio_descripcion,
                    'tipo_tamano_muestra_y_procedimiento_utilizado_descripcion' => $tipo_tamano_muestra_y_procedimiento_utilizado_descripcion,
                    'tamano_muestra' => $tamano_muestra,
                    'procedimiento_utilizado' => $procedimiento_utilizado,
                    'espacio_geografico_fecha' => $espacio_geografico_fecha,
                    'espacio_geografico_fecha_estado' => $espacio_geografico_fecha_estado,
                    'tipo_encuesta' => $tipo_encuesta,
                    'tbl_espacio_geografico_id' => $tbl_espacio_geografico_id,
                    'poblacion_objetivo' => $poblacion_objetivo
                ];
                $arrfieldsnocomma = array('dtupdate' => Util::date_now_server());
                $q_update = Util::make_query_update($table, "id = '$id'", $arrfieldscomma, $arrfieldsnocomma);
                $pdo->query($q_update);
                $arrjson = array('output' => array('valid' => true, 'id' => $id));
            } else {

               $q = "INSERT INTO " . $db->getTable('tbl_ficha_tecnica_encuestas') . " 
    (realizada_por_o_encomendada_por, fuente_financiacion, tipo_tamano_muestra_y_procedimiento_utilizado, temas_concretos, texto_literal_de_la_encuesta_o_preguntas, candidatos_personas_instituciones_indagados, espacio_geografico_fecha_o_periodo_que_se_realizo, margen_error_porcentaje, tipo_estudio, proposito_del_estudio, universo_representado, metodo_recoleccion, nivel_confiabilidad_porcentaje, estadisticos_responsables, declaracion, avisos, dtcreate, tbl_usuario_id, habilitado, tipo_estudio_descripcion, tipo_tamano_muestra_y_procedimiento_utilizado_descripcion, tamano_muestra, procedimiento_utilizado, espacio_geografico_fecha, espacio_geografico_fecha_estado, tipo_encuesta, tbl_espacio_geografico_id, poblacion_objetivo)
    VALUES 
    (:realizada_por_o_encomendada_por, :fuente_financiacion, :tipo_tamano_muestra_y_procedimiento_utilizado, :temas_concretos, :texto_literal_de_la_encuesta_o_preguntas, :candidatos_personas_instituciones_indagados, :espacio_geografico_fecha_o_periodo_que_se_realizo, :margen_error_porcentaje, :tipo_estudio, :proposito_del_estudio, :universo_representado, :metodo_recoleccion, :nivel_confiabilidad_porcentaje, :estadisticos_responsables, :declaracion, :avisos, :dtcreate, :tbl_usuario_id, :habilitado, :tipo_estudio_descripcion, :tipo_tamano_muestra_y_procedimiento_utilizado_descripcion, :tamano_muestra, :procedimiento_utilizado, :espacio_geografico_fecha, :espacio_geografico_fecha_estado, :tipo_encuesta, :tbl_espacio_geografico_id, :poblacion_objetivo)";
                $stmt = $pdo->prepare($q);
                $arrparam = [
                    ':realizada_por_o_encomendada_por' => $realizada_por_o_encomendada_por,
                    ':fuente_financiacion' => $fuente_financiacion,
                    ':tipo_tamano_muestra_y_procedimiento_utilizado' => $tipo_tamano_muestra_y_procedimiento_utilizado,
                    ':temas_concretos' => $temas_concretos,
                    ':texto_literal_de_la_encuesta_o_preguntas' => $texto_literal_de_la_encuesta_o_preguntas,
                    ':candidatos_personas_instituciones_indagados' => $candidatos_personas_instituciones_indagados,
                    ':espacio_geografico_fecha_o_periodo_que_se_realizo' => $espacio_geografico_fecha_o_periodo_que_se_realizo,
                    ':margen_error_porcentaje' => $margen_error_porcentaje,
                    ':tipo_estudio' => $tipo_estudio,
                    ':proposito_del_estudio' => $proposito_del_estudio,
                    ':universo_representado' => $universo_representado,
                    ':metodo_recoleccion' => $metodo_recoleccion,
                    ':nivel_confiabilidad_porcentaje' => $nivel_confiabilidad_porcentaje,
                    ':estadisticos_responsables' => $estadisticos_responsables,
                    ':declaracion' => $declaracion,
                    ':avisos' => $avisos,
                    ':dtcreate' => Util::date(),
                    ':tbl_usuario_id' => $tbl_usuario_id,
                    ':habilitado' => $habilitado,
                    // Campos adicionales
                    ':tipo_estudio_descripcion' => $tipo_estudio_descripcion,
                    ':tipo_tamano_muestra_y_procedimiento_utilizado_descripcion' => $tipo_tamano_muestra_y_procedimiento_utilizado_descripcion,
                    ':tamano_muestra' => $tamano_muestra,
                    ':procedimiento_utilizado' => $procedimiento_utilizado,
                    ':espacio_geografico_fecha' => $espacio_geografico_fecha,
                    ':espacio_geografico_fecha_estado' => $espacio_geografico_fecha_estado,
                    ':tipo_encuesta' => $tipo_encuesta,
                    ':tbl_espacio_geografico_id' => $tbl_espacio_geografico_id,
                    ':poblacion_objetivo' => $poblacion_objetivo
                ];

                $stmt->execute($arrparam);
                $arrjson = array('output' => array('valid' => true, 'response' => $pdo->lastInsertId()));
            }

            $pdo->commit();
        } catch (PDOException $e) {
            print_r($e);
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $arrjson = Util::error_general('Guardando datos en FichaTecnicaEncuesta');
        } finally {
            $db->closeConect();
        }
        return $arrjson;
    }

    public static function duplicate($rqst)
    {
        $id = isset($rqst['id']) ? intval($rqst['id']) : 0;
        if ($id <= 0) {
            return Util::error_missing_data_description('ID inválido para duplicar.');
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $stmt = $pdo->prepare("SELECT * FROM " . $db->getTable('tbl_ficha_tecnica_encuestas') . " WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $orig = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$orig) {
                return Util::error_no_result();
            }

            $tbl_usuario_id = intval($_SESSION['session_user']['id']);
            $q = "INSERT INTO " . $db->getTable('tbl_ficha_tecnica_encuestas') . "
                  (realizada_por_o_encomendada_por, fuente_financiacion, tipo_tamano_muestra_y_procedimiento_utilizado,
                   temas_concretos, texto_literal_de_la_encuesta_o_preguntas, candidatos_personas_instituciones_indagados,
                   espacio_geografico_fecha_o_periodo_que_se_realizo, margen_error_porcentaje, tipo_estudio,
                   proposito_del_estudio, universo_representado, metodo_recoleccion, nivel_confiabilidad_porcentaje,
                   estadisticos_responsables, declaracion, avisos, dtcreate, tbl_usuario_id, habilitado,
                   tipo_estudio_descripcion, tipo_tamano_muestra_y_procedimiento_utilizado_descripcion,
                   tamano_muestra, procedimiento_utilizado, espacio_geografico_fecha, espacio_geografico_fecha_estado,
                   tipo_encuesta, tbl_espacio_geografico_id, poblacion_objetivo)
                  VALUES
                  (:realizada_por_o_encomendada_por, :fuente_financiacion, :tipo_tamano_muestra_y_procedimiento_utilizado,
                   :temas_concretos, :texto_literal_de_la_encuesta_o_preguntas, :candidatos_personas_instituciones_indagados,
                   :espacio_geografico_fecha_o_periodo_que_se_realizo, :margen_error_porcentaje, :tipo_estudio,
                   :proposito_del_estudio, :universo_representado, :metodo_recoleccion, :nivel_confiabilidad_porcentaje,
                   :estadisticos_responsables, :declaracion, :avisos, :dtcreate, :tbl_usuario_id, :habilitado,
                   :tipo_estudio_descripcion, :tipo_tamano_muestra_y_procedimiento_utilizado_descripcion,
                   :tamano_muestra, :procedimiento_utilizado, :espacio_geografico_fecha, :espacio_geografico_fecha_estado,
                   :tipo_encuesta, :tbl_espacio_geografico_id, :poblacion_objetivo)";

            $stmt = $pdo->prepare($q);
            $stmt->execute([
                ':realizada_por_o_encomendada_por'                          => 'COPIA - ' . $orig['realizada_por_o_encomendada_por'],
                ':fuente_financiacion'                                       => $orig['fuente_financiacion'],
                ':tipo_tamano_muestra_y_procedimiento_utilizado'             => $orig['tipo_tamano_muestra_y_procedimiento_utilizado'],
                ':temas_concretos'                                           => $orig['temas_concretos'],
                ':texto_literal_de_la_encuesta_o_preguntas'                  => $orig['texto_literal_de_la_encuesta_o_preguntas'],
                ':candidatos_personas_instituciones_indagados'               => $orig['candidatos_personas_instituciones_indagados'],
                ':espacio_geografico_fecha_o_periodo_que_se_realizo'         => $orig['espacio_geografico_fecha_o_periodo_que_se_realizo'],
                ':margen_error_porcentaje'                                   => $orig['margen_error_porcentaje'],
                ':tipo_estudio'                                              => $orig['tipo_estudio'],
                ':proposito_del_estudio'                                     => $orig['proposito_del_estudio'],
                ':universo_representado'                                     => $orig['universo_representado'],
                ':metodo_recoleccion'                                        => $orig['metodo_recoleccion'],
                ':nivel_confiabilidad_porcentaje'                            => $orig['nivel_confiabilidad_porcentaje'],
                ':estadisticos_responsables'                                 => $orig['estadisticos_responsables'],
                ':declaracion'                                               => $orig['declaracion'],
                ':avisos'                                                    => $orig['avisos'],
                ':dtcreate'                                                  => Util::date(),
                ':tbl_usuario_id'                                            => $tbl_usuario_id,
                ':habilitado'                                                => 'si',
                ':tipo_estudio_descripcion'                                  => $orig['tipo_estudio_descripcion'],
                ':tipo_tamano_muestra_y_procedimiento_utilizado_descripcion' => $orig['tipo_tamano_muestra_y_procedimiento_utilizado_descripcion'],
                ':tamano_muestra'                                            => $orig['tamano_muestra'],
                ':procedimiento_utilizado'                                   => $orig['procedimiento_utilizado'],
                ':espacio_geografico_fecha'                                  => $orig['espacio_geografico_fecha'],
                ':espacio_geografico_fecha_estado'                           => $orig['espacio_geografico_fecha_estado'],
                ':tipo_encuesta'                                             => $orig['tipo_encuesta'],
                ':tbl_espacio_geografico_id'                                 => $orig['tbl_espacio_geografico_id'],
                ':poblacion_objetivo'                                        => $orig['poblacion_objetivo'],
            ]);

            $arrjson = array('output' => array('valid' => true, 'response' => $pdo->lastInsertId()));
        } catch (PDOException $e) {
            $arrjson = Util::error_general('Error al duplicar la ficha técnica.');
        } finally {
            $db->closeConect();
        }
        return $arrjson;
    }

    public static function delete($rqst)
    {
        $id = isset($rqst['id']) ? intval($rqst['id']) : 0;
        if ($id <= 0) {
            return Util::error_missing_data();
        }

        if (self::estaEnUso($id)) {
            return Util::error_general(
                'No se puede eliminar: la ficha técnica tiene preguntas, respuestas o grillas asociadas.'
            );
        }

        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $stmt = $pdo->prepare(
                "UPDATE " . $db->getTable('tbl_ficha_tecnica_encuestas') . "
                 SET eliminado = 'si'
                 WHERE id = :id AND (eliminado = 'no' OR eliminado IS NULL)"
            );
            $stmt->execute([':id' => $id]);

            if ($stmt->rowCount() === 0) {
                return Util::error_no_result();
            }

            return ['output' => ['valid' => true, 'response' => $id]];
        } catch (PDOException $e) {
            return Util::error_general('Error al eliminar la ficha técnica.');
        } finally {
            $db->closeConect();
        }
    }

    public static function updateTemas($rqst)
    {
        $id            = isset($rqst['id']) ? intval($rqst['id']) : 0;
        $temas_concretos = isset($rqst['temas_concretos']) ? trim($rqst['temas_concretos']) : '';

        if ($id <= 0 || $temas_concretos === '') {
            return Util::error_missing_data();
        }

        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $q = "UPDATE " . $db->getTable('tbl_ficha_tecnica_encuestas') . "
                  SET temas_concretos = :temas_concretos
                  WHERE id = :id";
            $stmt = $pdo->prepare($q);
            $stmt->execute([':temas_concretos' => $temas_concretos, ':id' => $id]);
            $arrjson = array('output' => array('valid' => true, 'response' => 'Actualizado correctamente.'));
        } catch (PDOException $e) {
            $arrjson = Util::error_general('Error al actualizar los temas concretos.');
        } finally {
            $db->closeConect();
        }
        return $arrjson;
    }
}
