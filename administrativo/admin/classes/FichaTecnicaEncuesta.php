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
     * Confianza (%) + margen de error (%) deben sumar exactamente 100.
     */
    public static function validarConfianzaMargen($valorZ, $margenError)
    {
        $confianza = round((float)self::confianzaDesdeZ($valorZ), 2);
        $margen = round((float)$margenError, 2);
        if ($confianza <= 0 && $margen <= 0) {
            return true;
        }
        if ($confianza <= 0 || $confianza >= 100 || $margen <= 0 || $margen >= 100) {
            return Util::error_missing_data_description('El nivel de confiabilidad y el margen de error deben ser mayores que 0 y menores que 100.');
        }
        $suma = round($confianza + $margen, 2);
        if (abs($suma - 100) > 0.005) {
            return Util::error_missing_data_description(
                'Nivel de confiabilidad (' . $confianza . '%) y margen de error (' . $margen
                . '%) deben sumar exactamente 100%. Hoy suman ' . $suma . '%.'
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
                    . " WHERE tbl_ficha_tecnica_encuesta_id = :id AND (eliminado = 0 OR eliminado IS NULL) AND (habilitado = 'si' OR habilitado IS NULL)",
                "SELECT COUNT(*) FROM " . $db->getTable('tbl_cuestionario_intentos')
                    . " WHERE tbl_ficha_tecnica_encuesta_id = :id AND (es_prueba = 0 OR es_prueba IS NULL)",
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
               AND (p.eliminado = 0 OR p.eliminado IS NULL)
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
            error_log('FichaTecnicaEncuesta::save error: ' . $e->getMessage());
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $arrjson = Util::error_general('Guardando datos en FichaTecnicaEncuesta');
        } finally {
            $db->closeConect();
        }
        return $arrjson;
    }

    private static function insertarFilaCopia(PDO $pdo, string $tabla, array $fila, array $sobrescribir = []): int
    {
        unset($fila['id']);
        foreach ($sobrescribir as $col => $val) {
            $fila[$col] = $val;
        }
        $cols = array_keys($fila);
        $sql = "INSERT INTO " . $tabla . " (`" . implode('`,`', $cols) . "`) VALUES (:" . implode(',:', $cols) . ")";
        $pdo->prepare($sql)->execute($fila);
        return (int)$pdo->lastInsertId();
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
            $tFicha = $db->getTable('tbl_ficha_tecnica_encuestas');
            $tPreg = $db->getTable('tbl_preguntas');
            $tOpc = $db->getTable('tbl_opciones_respuesta');
            $tExcl = $db->getTable('tbl_preguntas_lista_exclusiones');
            $tVar = $db->getTable('tbl_preguntas_grilla_variantes');
            $tVarReg = $db->getTable('tbl_preguntas_grilla_variantes_region');
            $tAmbito = $db->getTable('tbl_preguntas_ambito');
            $tEquiv = $db->getTable('tbl_equivalencias_votante');
            $tZonas = $db->getTable('tbl_zonas_muestreo');

            $stmt = $pdo->prepare("SELECT * FROM {$tFicha} WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $orig = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$orig) {
                return Util::error_no_result();
            }

            $usuarioId = isset($_SESSION['session_user']['id']) ? intval($_SESSION['session_user']['id']) : 1;
            $ahora = date('Y-m-d H:i:s');
            $advertencias = [];
            $mapaPreg = [];
            $mapaOpc = [];

            $pdo->beginTransaction();

            $nuevaFichaId = self::insertarFilaCopia($pdo, $tFicha, $orig, [
                'realizada_por_o_encomendada_por' => 'COPIA - ' . $orig['realizada_por_o_encomendada_por'],
                'habilitado' => 'no',
                'eliminado' => 'no',
                'dtcreate' => $ahora,
                'dtupdate' => $ahora,
                'tbl_usuario_id' => $usuarioId,
                'pregunta_destacada_id' => null,
            ]);

            $stP = $pdo->prepare("SELECT * FROM {$tPreg} WHERE tbl_ficha_tecnica_encuesta_id = :f AND eliminado = 0 ORDER BY orden, id");
            $stP->execute([':f' => $id]);
            $preguntas = $stP->fetchAll(PDO::FETCH_ASSOC);

            $stO = $pdo->prepare("SELECT * FROM {$tOpc} WHERE tbl_pregunta_id = :p AND eliminado = 0 ORDER BY orden, id");
            $saltosOrigen = [];
            foreach ($preguntas as $p) {
                $nuevoP = self::insertarFilaCopia($pdo, $tPreg, $p, [
                    'tbl_ficha_tecnica_encuesta_id' => $nuevaFichaId,
                    'tbl_usuario_id' => $usuarioId,
                    'dtcreate' => $ahora,
                    'visible_si_pregunta_id' => null,
                    'visible_si_opcion_id' => null,
                    'lista_origen_pregunta_id' => null,
                ]);
                $mapaPreg[(int)$p['id']] = $nuevoP;

                $stO->execute([':p' => $p['id']]);
                foreach ($stO->fetchAll(PDO::FETCH_ASSOC) as $o) {
                    $nuevoO = self::insertarFilaCopia($pdo, $tOpc, $o, [
                        'tbl_pregunta_id' => $nuevoP,
                        'dtcreate' => $ahora,
                        'salto_destino_pregunta_id' => null,
                    ]);
                    $mapaOpc[(int)$o['id']] = $nuevoO;
                    if ($o['salto_destino_pregunta_id'] !== null) {
                        $saltosOrigen[$nuevoO] = (int)$o['salto_destino_pregunta_id'];
                    }
                }
            }

            $stUpdP = $pdo->prepare("UPDATE {$tPreg} SET visible_si_pregunta_id = :vp, visible_si_opcion_id = :vo, lista_origen_pregunta_id = :lp WHERE id = :id");
            foreach ($preguntas as $p) {
                $vp = $p['visible_si_pregunta_id'] !== null ? ($mapaPreg[(int)$p['visible_si_pregunta_id']] ?? null) : null;
                $vo = $p['visible_si_opcion_id'] !== null ? ($mapaOpc[(int)$p['visible_si_opcion_id']] ?? null) : null;
                $lp = $p['lista_origen_pregunta_id'] !== null ? ($mapaPreg[(int)$p['lista_origen_pregunta_id']] ?? null) : null;
                if (($p['visible_si_pregunta_id'] !== null && $vp === null) || ($p['visible_si_opcion_id'] !== null && $vo === null)) {
                    $vp = null;
                    $vo = null;
                    $advertencias[] = 'La pregunta ' . $p['id'] . ' dependía de una pregunta u opción eliminada: quedó siempre visible.';
                }
                if ($p['lista_origen_pregunta_id'] !== null && $lp === null) {
                    $advertencias[] = 'La pregunta ' . $p['id'] . ' tomaba su lista de una pregunta eliminada: revise su configuración.';
                }
                if ($vp !== null || $vo !== null || $lp !== null) {
                    $stUpdP->execute([':vp' => $vp, ':vo' => $vo, ':lp' => $lp, ':id' => $mapaPreg[(int)$p['id']]]);
                }
            }

            $stSalto = $pdo->prepare("UPDATE {$tOpc} SET salto_destino_pregunta_id = :d WHERE id = :id");
            $stSinSalto = $pdo->prepare("UPDATE {$tOpc} SET accion = 'continuar' WHERE id = :id AND accion = 'saltar'");
            foreach ($saltosOrigen as $nuevoO => $destViejo) {
                if (isset($mapaPreg[$destViejo])) {
                    $stSalto->execute([':d' => $mapaPreg[$destViejo], ':id' => $nuevoO]);
                } else {
                    $stSinSalto->execute([':id' => $nuevoO]);
                    $advertencias[] = 'Una opción saltaba a una pregunta eliminada: ahora continúa con la siguiente.';
                }
            }

            if (!empty($orig['pregunta_destacada_id']) && isset($mapaPreg[(int)$orig['pregunta_destacada_id']])) {
                $pdo->prepare("UPDATE {$tFicha} SET pregunta_destacada_id = :p WHERE id = :f")
                    ->execute([':p' => $mapaPreg[(int)$orig['pregunta_destacada_id']], ':f' => $nuevaFichaId]);
            }

            $totalExcl = 0;
            $totalVar = 0;
            if (!empty($mapaPreg)) {
                $in = implode(',', array_map('intval', array_keys($mapaPreg)));
                $stExcl = $pdo->prepare("INSERT INTO {$tExcl} (tbl_pregunta_id, tbl_pregunta_excluida_id, dtcreate) VALUES (:a, :b, :d)");
                foreach ($pdo->query("SELECT tbl_pregunta_id, tbl_pregunta_excluida_id FROM {$tExcl} WHERE tbl_pregunta_id IN ({$in})")->fetchAll(PDO::FETCH_ASSOC) as $e) {
                    if (isset($mapaPreg[(int)$e['tbl_pregunta_excluida_id']])) {
                        $stExcl->execute([':a' => $mapaPreg[(int)$e['tbl_pregunta_id']], ':b' => $mapaPreg[(int)$e['tbl_pregunta_excluida_id']], ':d' => $ahora]);
                        $totalExcl++;
                    }
                }
                foreach ($pdo->query("SELECT * FROM {$tVar} WHERE tbl_pregunta_id IN ({$in})")->fetchAll(PDO::FETCH_ASSOC) as $v) {
                    self::insertarFilaCopia($pdo, $tVar, $v, [
                        'tbl_pregunta_id' => $mapaPreg[(int)$v['tbl_pregunta_id']],
                        'dtcreate' => $ahora,
                    ]);
                    $totalVar++;
                }
                foreach ($pdo->query("SELECT * FROM {$tAmbito} WHERE tbl_pregunta_id IN ({$in})")->fetchAll(PDO::FETCH_ASSOC) as $v) {
                    self::insertarFilaCopia($pdo, $tAmbito, $v, [
                        'tbl_pregunta_id' => $mapaPreg[(int)$v['tbl_pregunta_id']],
                        'dtcreate' => $ahora,
                    ]);
                }
                foreach ($pdo->query("SELECT * FROM {$tVarReg} WHERE tbl_pregunta_id IN ({$in})")->fetchAll(PDO::FETCH_ASSOC) as $v) {
                    self::insertarFilaCopia($pdo, $tVarReg, $v, [
                        'tbl_pregunta_id' => $mapaPreg[(int)$v['tbl_pregunta_id']],
                        'dtcreate' => $ahora,
                    ]);
                    $totalVar++;
                }
            }

            $stEq = $pdo->prepare("SELECT * FROM {$tEquiv} WHERE tbl_ficha_tecnica_encuesta_id = :f");
            $stEq->execute([':f' => $id]);
            $totalEq = 0;
            foreach ($stEq->fetchAll(PDO::FETCH_ASSOC) as $eq) {
                if (!isset($mapaPreg[(int)$eq['tbl_pregunta_id']]) || !isset($mapaOpc[(int)$eq['tbl_opcion_respuesta_id']])) {
                    continue;
                }
                self::insertarFilaCopia($pdo, $tEquiv, $eq, [
                    'tbl_ficha_tecnica_encuesta_id' => $nuevaFichaId,
                    'tbl_pregunta_id' => $mapaPreg[(int)$eq['tbl_pregunta_id']],
                    'tbl_opcion_respuesta_id' => $mapaOpc[(int)$eq['tbl_opcion_respuesta_id']],
                    'dtcreate' => $ahora,
                ]);
                $totalEq++;
            }

            $stZ = $pdo->prepare("SELECT * FROM {$tZonas} WHERE tbl_ficha_tecnica_encuesta_id = :f AND eliminado = 0 ORDER BY id");
            $stZ->execute([':f' => $id]);
            $totalZonas = 0;
            foreach ($stZ->fetchAll(PDO::FETCH_ASSOC) as $z) {
                self::insertarFilaCopia($pdo, $tZonas, $z, [
                    'tbl_ficha_tecnica_encuesta_id' => $nuevaFichaId,
                    'dtcreate' => $ahora,
                    'dtupdate' => $ahora,
                ]);
                $totalZonas++;
            }

            $pdo->commit();
            $arrjson = ['output' => ['valid' => true, 'response' => [
                'id' => $nuevaFichaId,
                'preguntas' => count($preguntas),
                'opciones' => count($mapaOpc),
                'exclusiones' => $totalExcl,
                'variantes' => $totalVar,
                'equivalencias' => $totalEq,
                'zonas' => $totalZonas,
                'advertencias' => array_values(array_unique($advertencias)),
            ]]];
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log('FichaTecnicaEncuesta::duplicate error: ' . $e->getMessage());
            $arrjson = Util::error_general('Error al copiar la encuesta. No se creó ningún registro.');
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
