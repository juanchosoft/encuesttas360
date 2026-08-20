<?php

/**
 * Clase AnalisisEstudio
 * Gestiona el análisis de indicadores/fórmulas para candidatos en grillas
 * Módulo exclusivo para investigadores
 */
class AnalisisEstudio
{
    /**
     * Obtiene todos los análisis registrados
     * Puede filtrar por grilla_id, participante_id o formula_id
     */
    public static function getAll($rqst)
    {
        $id = isset($rqst['id']) ? intval($rqst['id']) : 0;
        $tbl_grilla_id = isset($rqst['tbl_grilla_id']) ? intval($rqst['tbl_grilla_id']) : 0;
        $tbl_participante_id = isset($rqst['tbl_participante_id']) ? intval($rqst['tbl_participante_id']) : 0;
        $tbl_formula_id = isset($rqst['tbl_formula_id']) ? intval($rqst['tbl_formula_id']) : 0;

        $db = new DbConection();
        $pdo = $db->openConect();

        $q = "SELECT
                a.id,
                a.tbl_grilla_id,
                a.tbl_participante_id,
                a.tbl_formula_id,
                a.resultado_calculado,
                a.texto_resultado_calculado,
                a.observaciones,
                a.tbl_usuario_id,
                a.dtcreate,
                a.dtupdate,
                g.grilla as nombre_grilla,
                g.descripcion_grilla,
                p.nombre_completo as nombre_candidato,
                f.indicador as nombre_indicador,
                f.sigla as sigla_indicador,
                f.tipo_indicador,
                u.nombre as nombre_investigador,
                u.apellido as apellido_investigador
            FROM " . $db->getTable('tbl_analisis_estudio') . " a
            LEFT JOIN " . $db->getTable('tbl_grilla') . " g ON a.tbl_grilla_id = g.id
            LEFT JOIN " . $db->getTable('tbl_participantes') . " p ON a.tbl_participante_id = p.id
            LEFT JOIN " . $db->getTable('tbl_formulas') . " f ON a.tbl_formula_id = f.id
            LEFT JOIN " . $db->getTable('tbl_usuarios') . " u ON a.tbl_usuario_id = u.id";

        $params = [];
        $conditions = [];

        if ($id > 0) {
            $conditions[] = "a.id = :id";
            $params[':id'] = $id;
        }
        if ($tbl_grilla_id > 0) {
            $conditions[] = "a.tbl_grilla_id = :tbl_grilla_id";
            $params[':tbl_grilla_id'] = $tbl_grilla_id;
        }
        if ($tbl_participante_id > 0) {
            $conditions[] = "a.tbl_participante_id = :tbl_participante_id";
            $params[':tbl_participante_id'] = $tbl_participante_id;
        }
        if ($tbl_formula_id > 0) {
            $conditions[] = "a.tbl_formula_id = :tbl_formula_id";
            $params[':tbl_formula_id'] = $tbl_formula_id;
        }

        if (!empty($conditions)) {
            $q .= " WHERE " . implode(' AND ', $conditions);
        }

        $q .= " ORDER BY a.dtcreate DESC";

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
            $arrjson = Util::error_general('Error al obtener los análisis: ' . $e->getMessage());
        } finally {
            $db->closeConect();
        }

        return $arrjson;
    }

    /**
     * Obtiene un análisis completo por ID (incluye cálculos)
     * Para uso en edición
     */
    public static function getById($rqst)
    {
        $id = isset($rqst['id']) ? intval($rqst['id']) : 0;

        if ($id == 0) {
            return Util::error_missing_data_description('El ID del análisis es requerido.');
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $q = "SELECT
                    a.id,
                    a.tbl_grilla_id,
                    a.tbl_participante_id,
                    a.tbl_formula_id,
                    a.resultado_calculado,
                    a.texto_resultado_calculado,
                    a.observaciones,
                    a.dtcreate,
                    g.grilla as nombre_grilla,
                    p.nombre_completo as nombre_candidato,
                    p.foto as foto_candidato,
                    f.indicador as nombre_indicador,
                    f.sigla as sigla_indicador,
                    f.tipo_indicador,
                    f.formula
                FROM " . $db->getTable('tbl_analisis_estudio') . " a
                LEFT JOIN " . $db->getTable('tbl_grilla') . " g ON a.tbl_grilla_id = g.id
                LEFT JOIN " . $db->getTable('tbl_participantes') . " p ON a.tbl_participante_id = p.id
                LEFT JOIN " . $db->getTable('tbl_formulas') . " f ON a.tbl_formula_id = f.id
                WHERE a.id = :id
                LIMIT 1";

            $stmt = $pdo->prepare($q);
            $stmt->execute([':id' => $id]);
            $analisis = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($analisis) {
                // Cargar cálculos
                require_once 'AnalisisCalculos.php';
                $analisis['calculos'] = AnalisisCalculos::obtenerCalculos($id);

                $arrjson = array('output' => array('valid' => true, 'response' => $analisis));
            } else {
                $arrjson = Util::error_no_result();
            }
        } catch (PDOException $e) {
            $arrjson = Util::error_general('Error al obtener el análisis: ' . $e->getMessage());
        } finally {
            $db->closeConect();
        }

        return $arrjson;
    }

    /**
     * Guarda un nuevo análisis o actualiza uno existente
     */
    public static function save($rqst)
    {
        $id = isset($rqst['id']) ? intval($rqst['id']) : 0;
        $tbl_grilla_id = isset($rqst['tbl_grilla_id']) ? intval($rqst['tbl_grilla_id']) : 0;
        $tbl_participante_id = isset($rqst['tbl_participante_id']) ? intval($rqst['tbl_participante_id']) : 0;
        $tbl_formula_id = isset($rqst['tbl_formula_id']) ? intval($rqst['tbl_formula_id']) : 0;
        $resultado_calculado = isset($rqst['resultado_calculado']) ? trim($rqst['resultado_calculado']) : '';
        $texto_resultado_calculado = isset($rqst['texto_resultado_calculado']) ? trim($rqst['texto_resultado_calculado']) : null;
        $observaciones = isset($rqst['observaciones']) ? trim($rqst['observaciones']) : null;
        $tbl_usuario_id = isset($_SESSION['session_user']['id']) ? intval($_SESSION['session_user']['id']) : 1;

        // Procesar cálculos de la calculadora
        $calculos = [];
        if (isset($rqst['calculos']) && !empty($rqst['calculos'])) {
            $calculos = json_decode($rqst['calculos'], true);
            if (!is_array($calculos)) {
                $calculos = [];
            }
        }

        // Validaciones
        if ($tbl_grilla_id == 0) {
            return Util::error_missing_data_description('La grilla es requerida.');
        }
        if ($tbl_participante_id == 0) {
            return Util::error_missing_data_description('El candidato es requerido.');
        }
        if ($tbl_formula_id == 0) {
            return Util::error_missing_data_description('El indicador/fórmula es requerido.');
        }
        if (empty($resultado_calculado)) {
            return Util::error_missing_data_description('El resultado calculado es requerido.');
        }
        if (!empty($texto_resultado_calculado) && mb_strlen($texto_resultado_calculado) > 20) {
            return Util::error_missing_data_description('El texto descriptivo del resultado no puede exceder 20 caracteres.');
        }

        $db = new DbConection();
        $pdo = $db->openConect();
        $pdo->beginTransaction();

        try {
            if ($id > 0) {
                // Actualizar análisis existente por ID
                $table = $db->getTable('tbl_analisis_estudio');
                $arrfieldscomma = [
                    'tbl_grilla_id' => $tbl_grilla_id,
                    'tbl_participante_id' => $tbl_participante_id,
                    'tbl_formula_id' => $tbl_formula_id,
                    'resultado_calculado' => $resultado_calculado,
                    'texto_resultado_calculado' => $texto_resultado_calculado,
                    'observaciones' => $observaciones,
                    'tbl_usuario_id' => $tbl_usuario_id,
                ];
                $arrfieldsnocomma = array();
                $q_update = Util::make_query_update($table, "id = '$id'", $arrfieldscomma, $arrfieldsnocomma);

                $pdo->query($q_update);
                $arrjson = array('output' => array('valid' => true, 'id' => $id));
            } else {
                // Verificar si ya existe un análisis con la misma combinación grilla+candidato+fórmula
                $qCheck = "SELECT id FROM " . $db->getTable('tbl_analisis_estudio') . "
                          WHERE tbl_grilla_id = :tbl_grilla_id
                          AND tbl_participante_id = :tbl_participante_id
                          AND tbl_formula_id = :tbl_formula_id
                          LIMIT 1";

                $stmtCheck = $pdo->prepare($qCheck);
                $stmtCheck->execute([
                    ':tbl_grilla_id' => $tbl_grilla_id,
                    ':tbl_participante_id' => $tbl_participante_id,
                    ':tbl_formula_id' => $tbl_formula_id
                ]);
                $existente = $stmtCheck->fetch(PDO::FETCH_ASSOC);

                if ($existente) {
                    // Ya existe, actualizar el registro existente
                    $id = $existente['id'];
                    $qUpdate = "UPDATE " . $db->getTable('tbl_analisis_estudio') . "
                              SET resultado_calculado = :resultado_calculado,
                                  texto_resultado_calculado = :texto_resultado_calculado,
                                  observaciones = :observaciones,
                                  tbl_usuario_id = :tbl_usuario_id,
                                  dtupdate = NOW()
                              WHERE id = :id";

                    $stmtUpdate = $pdo->prepare($qUpdate);
                    $stmtUpdate->execute([
                        ':resultado_calculado' => $resultado_calculado,
                        ':texto_resultado_calculado' => $texto_resultado_calculado,
                        ':observaciones' => $observaciones,
                        ':tbl_usuario_id' => $tbl_usuario_id,
                        ':id' => $id
                    ]);

                    $arrjson = array('output' => array('valid' => true, 'response' => $id, 'action' => 'updated'));
                } else {
                    // No existe, insertar nuevo análisis
                    $q = "INSERT INTO " . $db->getTable('tbl_analisis_estudio') . "
                    (tbl_grilla_id, tbl_participante_id, tbl_formula_id, resultado_calculado, texto_resultado_calculado, observaciones, tbl_usuario_id, dtcreate)
                    VALUES (:tbl_grilla_id, :tbl_participante_id, :tbl_formula_id, :resultado_calculado, :texto_resultado_calculado, :observaciones, :tbl_usuario_id, NOW())";

                    $stmt = $pdo->prepare($q);
                    $arrparam = [
                        ':tbl_grilla_id' => $tbl_grilla_id,
                        ':tbl_participante_id' => $tbl_participante_id,
                        ':tbl_formula_id' => $tbl_formula_id,
                        ':resultado_calculado' => $resultado_calculado,
                        ':texto_resultado_calculado' => $texto_resultado_calculado,
                        ':observaciones' => $observaciones,
                        ':tbl_usuario_id' => $tbl_usuario_id,
                    ];

                    $stmt->execute($arrparam);
                    $id = $pdo->lastInsertId();
                    $arrjson = array('output' => array('valid' => true, 'response' => $id, 'action' => 'created'));
                }
            }

            $pdo->commit();

            // Guardar cálculos de la calculadora (después del commit)
            if (!empty($calculos) && $id > 0) {
                require_once 'AnalisisCalculos.php';
                AnalisisCalculos::guardarCalculos($id, $calculos);
            }

        } catch (PDOException $e) {
            $pdo->rollBack();
            $arrjson = Util::error_general('Error al guardar el análisis: ' . $e->getMessage());
        } finally {
            $db->closeConect();
        }

        return $arrjson;
    }

    /**
     * Elimina un análisis
     */
    public static function delete($rqst)
    {
        $id = isset($rqst['id']) ? intval($rqst['id']) : 0;

        if ($id == 0) {
            return Util::error_missing_data_description('El ID del análisis es requerido.');
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            // Eliminar cálculos asociados
            require_once 'AnalisisCalculos.php';
            AnalisisCalculos::eliminarCalculos($id);

            // Eliminar análisis
            $q = "DELETE FROM " . $db->getTable('tbl_analisis_estudio') . " WHERE id = :id";
            $stmt = $pdo->prepare($q);
            $stmt->execute([':id' => $id]);

            $arrjson = array('output' => array('valid' => true, 'response' => 'Análisis eliminado correctamente'));
        } catch (PDOException $e) {
            $arrjson = Util::error_general('Error al eliminar el análisis: ' . $e->getMessage());
        } finally {
            $db->closeConect();
        }

        return $arrjson;
    }

    /**
     * Obtiene los candidatos asociados a una grilla específica
     */
    public static function getCandidatosByGrilla($rqst)
    {
        $tbl_grilla_id = isset($rqst['tbl_grilla_id']) ? intval($rqst['tbl_grilla_id']) : 0;

        if ($tbl_grilla_id == 0) {
            return Util::error_missing_data_description('El ID de la grilla es requerido.');
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $q = "SELECT DISTINCT
                    p.id,
                    p.nombre_completo,
                    p.codigo_departamento,
                    p.codigo_municipio,
                    p.tbl_cargo_publico_id,
                    p.foto,
                    cp.nombre as cargo_nombre
                FROM " . $db->getTable('tbl_grilla_x_tbl_participantes') . " gp
                INNER JOIN " . $db->getTable('tbl_participantes') . " p ON gp.tbl_participante_id = p.id
                LEFT JOIN " . $db->getTable('tbl_cargos_publicos') . " cp ON p.tbl_cargo_publico_id = cp.id
                WHERE gp.tbl_grilla_id = :tbl_grilla_id
                AND p.habilitado = 'si'
                ORDER BY p.nombre_completo ASC";

            $stmt = $pdo->prepare($q);
            $stmt->execute([':tbl_grilla_id' => $tbl_grilla_id]);
            $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($arr) {
                $arrjson = array('output' => array('valid' => true, 'response' => $arr));
            } else {
                $arrjson = array('output' => array('valid' => true, 'response' => []));
            }
        } catch (PDOException $e) {
            $arrjson = Util::error_general('Error al obtener candidatos: ' . $e->getMessage());
        } finally {
            $db->closeConect();
        }

        return $arrjson;
    }

    /**
     * Obtiene los resultados de votación de una grilla para un candidato específico
     * (datos de conocimiento, imagen, votaría por él, etc.)
     */
    public static function getResultadosGrillaCandidato($rqst)
    {
        $tbl_grilla_id = isset($rqst['tbl_grilla_id']) ? intval($rqst['tbl_grilla_id']) : 0;
        $tbl_participante_id = isset($rqst['tbl_participante_id']) ? intval($rqst['tbl_participante_id']) : 0;

        if ($tbl_grilla_id == 0 || $tbl_participante_id == 0) {
            return Util::error_missing_data_description('Se requiere ID de grilla y candidato.');
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            // Obtener resultados de la grilla para el candidato usando tbl_grilla_respuestas
            $q = "SELECT
                    COUNT(DISTINCT CASE WHEN p.codigo_pregunta = 'conoce' AND gr.respuesta = 'si' THEN sv.tbl_votante_id END) as conoce_si,
                    COUNT(DISTINCT CASE WHEN p.codigo_pregunta = 'conoce' AND gr.respuesta = 'no' THEN sv.tbl_votante_id END) as conoce_no,
                    COUNT(DISTINCT CASE WHEN p.codigo_pregunta = 'imagen' AND gr.respuesta = 'favorable' THEN sv.tbl_votante_id END) as imagen_favorable,
                    COUNT(DISTINCT CASE WHEN p.codigo_pregunta = 'imagen' AND gr.respuesta = 'desfavorable' THEN sv.tbl_votante_id END) as imagen_desfavorable,
                    COUNT(DISTINCT CASE WHEN p.codigo_pregunta = 'votaria' AND gr.respuesta = 'si' THEN sv.tbl_votante_id END) as votaria_si,
                    COUNT(DISTINCT CASE WHEN p.codigo_pregunta = 'votaria' AND gr.respuesta = 'no' THEN sv.tbl_votante_id END) as votaria_no,
                    COUNT(DISTINCT sv.tbl_votante_id) as total_votantes
                FROM " . $db->getTable('tbl_grilla_sesion_votacion') . " sv
                INNER JOIN " . $db->getTable('tbl_grilla_respuestas') . " gr ON gr.tbl_sesion_votacion_id = sv.id
                INNER JOIN " . $db->getTable('tbl_preguntas_sub_preguntas_grilla') . " p ON gr.tbl_pregunta_id = p.id
                WHERE sv.tbl_grilla_id = :tbl_grilla_id
                AND gr.tbl_participante_id = :tbl_participante_id
                AND p.codigo_pregunta IN ('conoce', 'imagen', 'votaria')";

            $stmt = $pdo->prepare($q);
            $stmt->execute([
                ':tbl_grilla_id' => $tbl_grilla_id,
                ':tbl_participante_id' => $tbl_participante_id
            ]);
            $resultados = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($resultados) {
                $arrjson = array('output' => array('valid' => true, 'response' => $resultados));
            } else {
                // Retornar estructura con ceros si no hay datos
                $arrjson = array('output' => array('valid' => true, 'response' => [
                    'conoce_si' => 0,
                    'conoce_no' => 0,
                    'imagen_favorable' => 0,
                    'imagen_desfavorable' => 0,
                    'votaria_si' => 0,
                    'votaria_no' => 0,
                    'total_votantes' => 0
                ]));
            }
        } catch (PDOException $e) {
            $arrjson = Util::error_general('Error al obtener resultados: ' . $e->getMessage());
        } finally {
            $db->closeConect();
        }

        return $arrjson;
    }

    /**
     * Busca un análisis existente por filtros (grilla + candidato + fórmula)
     * y retorna el ID del análisis con sus operaciones guardadas
     */
    public static function buscarAnalisisExistente($rqst)
    {
        $tbl_grilla_id = isset($rqst['tbl_grilla_id']) ? intval($rqst['tbl_grilla_id']) : 0;
        $tbl_participante_id = isset($rqst['tbl_participante_id']) ? intval($rqst['tbl_participante_id']) : 0;
        $tbl_formula_id = isset($rqst['tbl_formula_id']) ? intval($rqst['tbl_formula_id']) : 0;

        if ($tbl_grilla_id == 0 || $tbl_participante_id == 0 || $tbl_formula_id == 0) {
            return array('output' => array('valid' => true, 'response' => null));
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $q = "SELECT id, resultado_calculado, texto_resultado_calculado, observaciones
                  FROM " . $db->getTable('tbl_analisis_estudio') . "
                  WHERE tbl_grilla_id = :tbl_grilla_id
                    AND tbl_participante_id = :tbl_participante_id
                    AND tbl_formula_id = :tbl_formula_id
                  LIMIT 1";

            $stmt = $pdo->prepare($q);
            $stmt->execute([
                ':tbl_grilla_id' => $tbl_grilla_id,
                ':tbl_participante_id' => $tbl_participante_id,
                ':tbl_formula_id' => $tbl_formula_id
            ]);
            $analisis = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($analisis) {
                // Cargar operaciones del análisis
                require_once 'AnalisisCalculos.php';
                $calculos = AnalisisCalculos::obtenerCalculos($analisis['id']);

                $arrjson = array('output' => array('valid' => true, 'response' => [
                    'id' => $analisis['id'],
                    'resultado_calculado' => $analisis['resultado_calculado'],
                    'texto_resultado_calculado' => $analisis['texto_resultado_calculado'],
                    'observaciones' => $analisis['observaciones'],
                    'calculos' => $calculos
                ]));
            } else {
                $arrjson = array('output' => array('valid' => true, 'response' => null));
            }
        } catch (PDOException $e) {
            $arrjson = Util::error_general('Error al buscar análisis: ' . $e->getMessage());
        } finally {
            $db->closeConect();
        }

        return $arrjson;
    }
}
