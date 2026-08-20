<?php

/**
 * Clase Pregunta
 * Gestiona las operaciones CRUD para las tablas 'tbl_preguntas' y 'tbl_opciones_respuesta'.
 */
class Pregunta
{
    public function __construct()
    {
    }

    /**
     * Obtiene preguntas, opcionalmente filtradas por ID de pregunta o ID de encuesta.
     * Incluye las opciones de respuesta asociadas a cada pregunta.
     */
    public static function getAll($rqst)
    {
        $id = isset($rqst['id']) ? intval($rqst['id']) : 0;
        $ids = isset($rqst['ids']) ? $rqst['ids'] : '';
        $tbl_ficha_tecnica_encuesta_id = isset($rqst['tbl_ficha_tecnica_encuesta_id']) ? intval($rqst['tbl_ficha_tecnica_encuesta_id']) : 0;

        $db = new DbConection();
        $pdo = $db->openConect();

        $q = "SELECT
                p.id,
                p.tbl_ficha_tecnica_encuesta_id,
                p.texto_pregunta,
                p.enunciado_pregunta,
                p.numeral,
                p.texto_adicional,
                p.capitulo,
                p.tipo_pregunta,
                p.orden,
                p.habilitado,
                p.visualizacion,
                p.tbl_usuario_id,
                p.dtcreate,
                e.temas_concretos,
                e.realizada_por_o_encomendada_por,
                e.tbl_espacio_geografico_id,
                eg.tipo_estudio AS espacio_geografico_tipo_estudio,
                eg.observaciones AS espacio_geografico_observaciones,
                GROUP_CONCAT(CONCAT_WS(':', o.id, o.texto_opcion) ORDER BY o.orden SEPARATOR '||') AS opciones_str
            FROM " . $db->getTable('tbl_preguntas') . " p
                LEFT JOIN " . $db->getTable('tbl_opciones_respuesta') . " o ON p.id = o.tbl_pregunta_id
                LEFT JOIN " . $db->getTable('tbl_ficha_tecnica_encuestas') . " e ON p.tbl_ficha_tecnica_encuesta_id = e.id
                LEFT JOIN " . $db->getTable('tbl_espacio_geografico') . " eg ON e.tbl_espacio_geografico_id = eg.id";
        
        $params = [];
        $conditions = [];

        // Normalización de parámetros para evitar el error de mezclar :id con ?
        if ($id > 0) {
            $conditions[] = "p.id = :id";
            $params[':id'] = $id;
        } elseif (!empty($ids)) {
            $idsArray = array_filter(array_map('intval', explode(',', $ids)), function($val) { return $val > 0; });
            if (!empty($idsArray)) {
                $placeholders = [];
                foreach ($idsArray as $index => $val) {
                    $key = ":id_list_" . $index;
                    $placeholders[] = $key;
                    $params[$key] = $val;
                }
                $conditions[] = "p.id IN (" . implode(',', $placeholders) . ")";
            }
        }

        if ($tbl_ficha_tecnica_encuesta_id > 0) {
            $conditions[] = "p.tbl_ficha_tecnica_encuesta_id = :encuesta_id";
            $params[':encuesta_id'] = $tbl_ficha_tecnica_encuesta_id;
        }

        if (!empty($conditions)) {
            $q .= " WHERE " . implode(' AND ', $conditions);
        }

        // ORDEN: 1. Por capítulo, 2. Por ID (orden de creación/ingreso)
        $q .= " GROUP BY p.id ORDER BY p.capitulo ASC, p.id ASC";

        try {
            $stmt = $pdo->prepare($q);
            $stmt->execute($params);
            $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($arr as &$pregunta) {
                $pregunta['opciones'] = [];
                if (!empty($pregunta['opciones_str'])) {
                    // Usamos || como separador más seguro por si el texto contiene ';'
                    $opcionesRaw = explode('||', $pregunta['opciones_str']);
                    foreach ($opcionesRaw as $opcionRaw) {
                        $parts = explode(':', $opcionRaw, 2);
                        if (count($parts) == 2) {
                            $pregunta['opciones'][] = ['id' => intval($parts[0]), 'texto' => $parts[1]];
                        }
                    }
                }
                unset($pregunta['opciones_str']); 
            }

            $arrjson = array('output' => array('valid' => true, 'response' => $arr));

        } catch (PDOException $e) {
            $arrjson = Util::error_general('Error al obtener los datos de preguntas.');
        } finally {
            $db->closeConect();
        }

        return $arrjson;
    }

    public static function save($rqst)
    {
        $id = isset($rqst['id']) ? intval($rqst['id']) : 0;
        $tbl_ficha_tecnica_encuesta_id = isset($rqst['tbl_ficha_tecnica_encuesta_id']) ? intval($rqst['tbl_ficha_tecnica_encuesta_id']) : 0;
        $texto_pregunta = isset($rqst['texto_pregunta']) ? trim($rqst['texto_pregunta']) : '';
        $enunciado_pregunta = isset($rqst['enunciado_pregunta']) ? trim($rqst['enunciado_pregunta']) : null;
        $tipo_pregunta = isset($rqst['tipo_pregunta']) ? trim($rqst['tipo_pregunta']) : null;
        $orden = isset($rqst['orden']) ? intval($rqst['orden']) : null;
        $limite_respuesta_multiple = isset($rqst['limite_respuesta_multiple']) ? intval($rqst['limite_respuesta_multiple']) : null;
        $habilitado = isset($rqst['habilitado']) ? trim($rqst['habilitado']) : 'si';
        $visualizacion = isset($rqst['visualizacion']) ? trim($rqst['visualizacion']) : 'si';
        $tbl_usuario_id = isset($_SESSION['session_user']['id']) ? intval($_SESSION['session_user']['id']) : 1; // Asume ID 1 si no hay sesión

        // Opciones de respuesta como un array de strings (ej. ['Opcion A', 'Opcion B'])
        // Si vienen con IDs (para actualizar), se procesarán en la lógica de actualización.
        $opciones_respuesta = isset($rqst['opciones']) && is_array($rqst['opciones']) ? $rqst['opciones'] : [];

        // Validaciones
        if ($tbl_ficha_tecnica_encuesta_id == 0) {
            return Util::error_missing_data_description('La encuesta es requerida.');
        }
        if (empty($texto_pregunta)) {
            return Util::error_missing_data_description('El texto de la pregunta es requerido.');
        }
        // Validar que haya opciones si el tipo de pregunta no es 'Texto Libre' (ejemplo)
        if (empty($opciones_respuesta)) {
            return Util::error_missing_data_description('Se requiere al menos una opción de respuesta.');
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        // Validar que la encuesta existe antes de continuar
        try {
            $check_q = "SELECT id FROM " . $db->getTable('tbl_ficha_tecnica_encuestas') . " WHERE id = :id LIMIT 1";
            $check_stmt = $pdo->prepare($check_q);
            $check_stmt->bindValue(':id', $tbl_ficha_tecnica_encuesta_id, PDO::PARAM_INT);
            $check_stmt->execute();

            if ($check_stmt->rowCount() == 0) {
                $db->closeConect();
                return Util::error_missing_data_description('La encuesta seleccionada (ID: ' . $tbl_ficha_tecnica_encuesta_id . ') no existe en la base de datos. Por favor, seleccione una encuesta válida.');
            }
        } catch (PDOException $e) {
            $db->closeConect();
            return Util::error_general('Error al validar la encuesta: ' . $e->getMessage());
        }

        $pdo->beginTransaction();

        try {
            $pregunta_id = $id;

            if ($id > 0) {
                // Actualizar pregunta existente
                $q = "UPDATE " . $db->getTable('tbl_preguntas') . "
                      SET tbl_ficha_tecnica_encuesta_id = :tbl_ficha_tecnica_encuesta_id,
                          texto_pregunta = :texto_pregunta,
                          enunciado_pregunta = :enunciado_pregunta,
                          tipo_pregunta = :tipo_pregunta,
                          orden = :orden,
                          tbl_usuario_id = :tbl_usuario_id,
                          limite_respuesta_multiple = :limite_respuesta_multiple,
                          habilitado = :habilitado,
                          visualizacion = :visualizacion
                      WHERE id = :id";
                $stmt = $pdo->prepare($q);
                $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            } else {
                // Insertar nueva pregunta
                $q = "INSERT INTO " . $db->getTable('tbl_preguntas') . "
                      (tbl_ficha_tecnica_encuesta_id, texto_pregunta, enunciado_pregunta, tipo_pregunta, orden, tbl_usuario_id, dtcreate, limite_respuesta_multiple, habilitado, visualizacion)
                      VALUES (:tbl_ficha_tecnica_encuesta_id, :texto_pregunta, :enunciado_pregunta, :tipo_pregunta, :orden, :tbl_usuario_id, NOW(), :limite_respuesta_multiple, :habilitado, :visualizacion)";
                $stmt = $pdo->prepare($q);
            }

            $stmt->bindValue(':tbl_ficha_tecnica_encuesta_id', $tbl_ficha_tecnica_encuesta_id, PDO::PARAM_INT);
            $stmt->bindValue(':texto_pregunta', $texto_pregunta, PDO::PARAM_STR);
            $stmt->bindValue(':enunciado_pregunta', $enunciado_pregunta, is_null($enunciado_pregunta) ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':tipo_pregunta', $tipo_pregunta, is_null($tipo_pregunta) ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':orden', $orden, is_null($orden) ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindValue(':tbl_usuario_id', $tbl_usuario_id, PDO::PARAM_INT);
            $stmt->bindValue(':limite_respuesta_multiple', $limite_respuesta_multiple, is_null($limite_respuesta_multiple) ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindValue(':habilitado', $habilitado, PDO::PARAM_STR);
            $stmt->bindValue(':visualizacion', $visualizacion, PDO::PARAM_STR);

            if (!$stmt->execute()) {
                throw new Exception('Error al guardar la pregunta principal.');
            }

            if ($id === 0) { // Si es una nueva pregunta
                $pregunta_id = $pdo->lastInsertId();
            }

            // Gestionar opciones de respuesta
            // 1. Eliminar opciones antiguas
            $delete_opciones_q = "DELETE FROM " . $db->getTable('tbl_opciones_respuesta') . " WHERE tbl_pregunta_id = :tbl_pregunta_id";
            $delete_opciones_stmt = $pdo->prepare($delete_opciones_q);
            $delete_opciones_stmt->bindValue(':tbl_pregunta_id', $pregunta_id, PDO::PARAM_INT);
            if (!$delete_opciones_stmt->execute()) {
                throw new Exception('Error al eliminar opciones de respuesta antiguas.');
            }

            // 2. Insertar nuevas opciones
            if (!empty($opciones_respuesta)) {
                $insert_opcion_q = "INSERT INTO " . $db->getTable('tbl_opciones_respuesta') . " 
                                    (tbl_pregunta_id, texto_opcion, orden, dtcreate)
                                    VALUES (:tbl_pregunta_id, :texto_opcion, :orden, NOW())";
                $insert_opcion_stmt = $pdo->prepare($insert_opcion_q);
                $opcion_orden_counter = 1;
                foreach ($opciones_respuesta as $opcionTexto) {
                    $insert_opcion_stmt->bindValue(':tbl_pregunta_id', $pregunta_id, PDO::PARAM_INT);
                    $insert_opcion_stmt->bindValue(':texto_opcion', $opcionTexto, PDO::PARAM_STR);
                    $insert_opcion_stmt->bindValue(':orden', $opcion_orden_counter++, PDO::PARAM_INT);
                    if (!$insert_opcion_stmt->execute()) {
                        throw new Exception('Error al insertar opción de respuesta: ' . $opcionTexto);
                    }
                }
            }

            $pdo->commit();
            $arrjson = array('output' => array('valid' => true, 'id' => $pregunta_id));

        } catch (Exception $e) {

            print_r($e);
            $pdo->rollBack();
            $arrjson = Util::error_general('Error al guardar la pregunta y sus opciones: ' . $e->getMessage());
        } finally {
            $db->closeConect();
        }

        return $arrjson;
    }

    /**
     * Actualiza solo el campo habilitado de una pregunta
     */
    public static function saveHabilitado($rqst)
    {
        $id = isset($rqst['id']) ? intval($rqst['id']) : 0;
        $habilitado = isset($rqst['habilitado']) && $rqst['habilitado'] === 'si' ? 'si' : 'no';

        if ($id <= 0) return Util::error_missing_data_description('ID requerido');

        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $stmt = $pdo->prepare("UPDATE " . $db->getTable('tbl_preguntas') . " SET habilitado = :habilitado WHERE id = :id");
            $stmt->execute([':habilitado' => $habilitado, ':id' => $id]);
            $arrjson = ['output' => ['valid' => true, 'response' => 'Estado actualizado']];
        } catch (Exception $e) {
            $arrjson = Util::error_general($e->getMessage());
        } finally {
            $db->closeConect();
        }
        return $arrjson;
    }

    /**
     * Elimina una pregunta y sus opciones de respuesta
     */
    public static function delete($rqst)
    {
        $id = isset($rqst['id']) ? intval($rqst['id']) : 0;

        if ($id <= 0) return Util::error_missing_data_description('ID requerido');

        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $pdo->beginTransaction();
            // Primero eliminar opciones
            $stmt = $pdo->prepare("DELETE FROM " . $db->getTable('tbl_opciones_respuesta') . " WHERE tbl_pregunta_id = :id");
            $stmt->execute([':id' => $id]);
            // Luego eliminar pregunta
            $stmt = $pdo->prepare("DELETE FROM " . $db->getTable('tbl_preguntas') . " WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $pdo->commit();
            $arrjson = ['output' => ['valid' => true, 'response' => 'Pregunta eliminada']];
        } catch (Exception $e) {
            $pdo->rollBack();
            $arrjson = Util::error_general($e->getMessage());
        } finally {
            $db->closeConect();
        }
        return $arrjson;
    }

    /**
     * Actualiza solo el enunciado de una pregunta existente
     */
    public static function saveEnunciado($rqst)
    {
        $id = isset($rqst['id']) ? intval($rqst['id']) : 0;
        $enunciado = isset($rqst['enunciado_pregunta']) ? trim($rqst['enunciado_pregunta']) : '';

        if ($id <= 0) {
            return Util::error_missing_data_description('ID de pregunta requerido');
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $q = "UPDATE " . $db->getTable('tbl_preguntas') . "
                  SET enunciado_pregunta = :enunciado_pregunta
                  WHERE id = :id";
            $stmt = $pdo->prepare($q);
            $stmt->execute([':enunciado_pregunta' => $enunciado ?: null, ':id' => $id]);
            $arrjson = ['output' => ['valid' => true, 'response' => 'Enunciado actualizado']];
        } catch (Exception $e) {
            $arrjson = Util::error_general('Error al actualizar enunciado: ' . $e->getMessage());
        } finally {
            $db->closeConect();
        }

        return $arrjson;
    }

    /**
     * Actualiza el enunciado de todas las preguntas de una ficha técnica
     * @param array $rqst - Request con 'ficha_id', 'enunciado_pregunta', y opcional 'solo_sin_enunciado'
     * @return array - Respuesta estándar
     */
    public static function saveEnunciadoGroup($rqst)
    {
        $fichaId = isset($rqst['ficha_id']) ? intval($rqst['ficha_id']) : 0;
        $enunciado = isset($rqst['enunciado_pregunta']) ? trim($rqst['enunciado_pregunta']) : '';
        $soloSinEnunciado = isset($rqst['solo_sin_enunciado']) && $rqst['solo_sin_enunciado'] === '1';

        if ($fichaId <= 0) {
            return Util::error_missing_data_description('ID de ficha técnica requerido');
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            if ($soloSinEnunciado) {
                $q = "UPDATE " . $db->getTable('tbl_preguntas') . "
                      SET enunciado_pregunta = :enunciado_pregunta
                      WHERE tbl_ficha_tecnica_encuesta_id = :ficha_id
                      AND (enunciado_pregunta IS NULL OR enunciado_pregunta = '')";
            } else {
                $q = "UPDATE " . $db->getTable('tbl_preguntas') . "
                      SET enunciado_pregunta = :enunciado_pregunta
                      WHERE tbl_ficha_tecnica_encuesta_id = :ficha_id";
            }
            $stmt = $pdo->prepare($q);
            $stmt->execute([':enunciado_pregunta' => $enunciado ?: null, ':ficha_id' => $fichaId]);
            $affected = $stmt->rowCount();
            $arrjson = ['output' => ['valid' => true, 'response' => "Enunciado actualizado en $affected preguntas"]];
        } catch (Exception $e) {
            $arrjson = Util::error_general('Error al actualizar enunciado del grupo: ' . $e->getMessage());
        } finally {
            $db->closeConect();
        }

        return $arrjson;
    }

    /**
     * Actualiza el enunciado de preguntas seleccionadas por IDs
     * @param array $rqst - 'ids' (comma-separated), 'enunciado_pregunta'
     */
    public static function saveEnunciadoSelected($rqst)
    {
        $ids_raw   = isset($rqst['ids'])               ? trim($rqst['ids'])               : '';
        $enunciado = isset($rqst['enunciado_pregunta']) ? trim($rqst['enunciado_pregunta']) : '';

        if (empty($ids_raw)) {
            return Util::error_missing_data_description('IDs de preguntas requeridos');
        }

        $ids = array_filter(array_map('intval', explode(',', $ids_raw)), fn($v) => $v > 0);

        if (empty($ids)) {
            return Util::error_missing_data_description('IDs de preguntas inválidos');
        }

        $db  = new DbConection();
        $pdo = $db->openConect();

        try {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $q = "UPDATE " . $db->getTable('tbl_preguntas') . "
                  SET enunciado_pregunta = ?
                  WHERE id IN ($placeholders)";
            $stmt = $pdo->prepare($q);
            $params = array_merge([$enunciado ?: null], array_values($ids));
            $stmt->execute($params);
            $affected = $stmt->rowCount();
            $arrjson  = ['output' => ['valid' => true, 'response' => "Enunciado actualizado en $affected preguntas"]];
        } catch (Exception $e) {
            $arrjson = Util::error_general('Error al actualizar enunciado: ' . $e->getMessage());
        } finally {
            $db->closeConect();
        }

        return $arrjson;
    }

    /**
     * Reasigna un grupo de preguntas a otra ficha técnica
     */
    public static function reasignar($rqst)
    {
        $ids_raw   = isset($rqst['ids'])   ? trim($rqst['ids'])   : '';
        $ficha_id  = isset($rqst['ficha_id']) ? intval($rqst['ficha_id']) : 0;

        if (empty($ids_raw) || $ficha_id <= 0) {
            return Util::error_missing_data_description('IDs de preguntas y ficha técnica destino son requeridos.');
        }

        // Validar que todos los IDs sean enteros positivos
        $ids = array_filter(array_map('intval', explode(',', $ids_raw)), fn($v) => $v > 0);
        if (empty($ids)) {
            return Util::error_missing_data_description('No se encontraron IDs válidos.');
        }

        $db  = new DbConection();
        $pdo = $db->openConect();

        try {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $params = array_merge([$ficha_id], array_values($ids));
            $stmt = $pdo->prepare(
                "UPDATE " . $db->getTable('tbl_preguntas') .
                " SET tbl_ficha_tecnica_encuesta_id = ? WHERE id IN ($placeholders)"
            );
            $stmt->execute($params);
            $arrjson = array('output' => array('valid' => true, 'response' => $stmt->rowCount() . ' pregunta(s) reasignada(s)'));
        } catch (PDOException $e) {
            $arrjson = Util::error_general('Error al reasignar preguntas.');
        } finally {
            $db->closeConect();
        }
        return $arrjson;
    }

    /**
     * Guarda múltiples preguntas en batch
     * @param array $rqst - Request con 'preguntas' como JSON string de array de preguntas
     * @return array - Respuesta estándar
     */
    public static function saveBatch($rqst)
    {
        $preguntasJson = isset($rqst['preguntas']) ? $rqst['preguntas'] : '';

        if (empty($preguntasJson)) {
            return Util::error_missing_data_description('No se recibieron preguntas para guardar');
        }

        $preguntas = json_decode($preguntasJson, true);
        if ($preguntas === null || !is_array($preguntas)) {
            return Util::error_general('Error al decodificar las preguntas: ' . json_last_error_msg());
        }

        if (count($preguntas) === 0) {
            return Util::error_missing_data_description('El array de preguntas está vacío');
        }

        $db = new DbConection();
        $pdo = $db->openConect();
        $preguntasGuardadas = 0;
        $errores = [];

        try {
            $pdo->beginTransaction();

            foreach ($preguntas as $index => $preguntaData) {
                $preguntaNum = $index + 1;

                // Validar datos requeridos
                if (empty($preguntaData['tbl_ficha_tecnica_encuesta_id']) || empty($preguntaData['texto_pregunta'])) {
                    $errores[] = "Pregunta $preguntaNum: Faltan datos requeridos";
                    continue;
                }

                $id = isset($preguntaData['id']) ? intval($preguntaData['id']) : 0;
                $tbl_ficha_tecnica_encuesta_id = intval($preguntaData['tbl_ficha_tecnica_encuesta_id']);
                $texto_pregunta = trim($preguntaData['texto_pregunta']);
                $tipo_pregunta = isset($preguntaData['tipo_pregunta']) ? trim($preguntaData['tipo_pregunta']) : null;
                $orden = isset($preguntaData['orden']) ? intval($preguntaData['orden']) : null;
                $limite_respuesta_multiple = isset($preguntaData['limite_respuesta_multiple']) ? intval($preguntaData['limite_respuesta_multiple']) : 1;
                $tbl_usuario_id = isset($_SESSION['session_user']['id']) ? intval($_SESSION['session_user']['id']) : 1;
                $opciones_respuesta = isset($preguntaData['opciones']) && is_array($preguntaData['opciones']) ? $preguntaData['opciones'] : [];

                // Guardar la pregunta
                if ($id > 0) {
                    // Actualizar pregunta existente
                    $qUpdate = "UPDATE " . $db->getTable('tbl_preguntas') . "
                               SET tbl_ficha_tecnica_encuesta_id = :tbl_ficha_tecnica_encuesta_id,
                                   texto_pregunta = :texto_pregunta,
                                   tipo_pregunta = :tipo_pregunta,
                                   orden = :orden,
                                   limite_respuesta_multiple = :limite_respuesta_multiple
                               WHERE id = :id";

                    $stmtUpdate = $pdo->prepare($qUpdate);
                    $stmtUpdate->execute([
                        ':id' => $id,
                        ':tbl_ficha_tecnica_encuesta_id' => $tbl_ficha_tecnica_encuesta_id,
                        ':texto_pregunta' => $texto_pregunta,
                        ':tipo_pregunta' => $tipo_pregunta,
                        ':orden' => $orden,
                        ':limite_respuesta_multiple' => $limite_respuesta_multiple
                    ]);

                    $pregunta_id = $id;
                } else {
                    // Insertar nueva pregunta
                    $enunciado_pregunta = isset($preguntaData['enunciado_pregunta']) ? trim($preguntaData['enunciado_pregunta']) : null;
                    $numeral            = isset($preguntaData['numeral'])         ? trim($preguntaData['numeral'])         : null;
                    $texto_adicional    = isset($preguntaData['texto_adicional']) ? trim($preguntaData['texto_adicional']) : null;
                    $capitulo           = isset($preguntaData['capitulo'])        ? trim($preguntaData['capitulo'])        : null;
                    $habilitado         = isset($preguntaData['habilitado'])      ? trim($preguntaData['habilitado'])      : 'si';
                    $visualizacion      = isset($preguntaData['visualizacion'])   ? trim($preguntaData['visualizacion'])   : 'si';

                    $qInsert = "INSERT INTO " . $db->getTable('tbl_preguntas') . "
                               (tbl_ficha_tecnica_encuesta_id, texto_pregunta, enunciado_pregunta, numeral, texto_adicional, capitulo,
                                tipo_pregunta, orden, limite_respuesta_multiple, tbl_usuario_id, dtcreate, habilitado, visualizacion)
                               VALUES (:tbl_ficha_tecnica_encuesta_id, :texto_pregunta, :enunciado_pregunta, :numeral, :texto_adicional, :capitulo,
                                       :tipo_pregunta, :orden, :limite_respuesta_multiple, :tbl_usuario_id, NOW(), :habilitado, :visualizacion)";

                    $stmtInsert = $pdo->prepare($qInsert);
                    $stmtInsert->execute([
                        ':tbl_ficha_tecnica_encuesta_id' => $tbl_ficha_tecnica_encuesta_id,
                        ':texto_pregunta' => $texto_pregunta,
                        ':enunciado_pregunta' => $enunciado_pregunta,
                        ':numeral' => $numeral !== '' ? $numeral : null,
                        ':texto_adicional' => $texto_adicional !== '' ? $texto_adicional : null,
                        ':capitulo' => $capitulo !== '' ? $capitulo : null,
                        ':tipo_pregunta' => $tipo_pregunta,
                        ':orden' => $orden,
                        ':limite_respuesta_multiple' => $limite_respuesta_multiple,
                        ':tbl_usuario_id' => $tbl_usuario_id,
                        ':habilitado' => $habilitado,
                        ':visualizacion' => $visualizacion
                    ]);

                    $pregunta_id = $pdo->lastInsertId();
                }

                // Eliminar opciones anteriores (si existe la pregunta)
                if ($id > 0) {
                    $qDeleteOpciones = "DELETE FROM " . $db->getTable('tbl_opciones_respuesta') . " WHERE tbl_pregunta_id = :pregunta_id";
                    $stmtDeleteOpciones = $pdo->prepare($qDeleteOpciones);
                    $stmtDeleteOpciones->execute([':pregunta_id' => $pregunta_id]);
                }

                // Insertar nuevas opciones
                if (!empty($opciones_respuesta)) {
                    $qOpcion = "INSERT INTO " . $db->getTable('tbl_opciones_respuesta') . "
                               (tbl_pregunta_id, texto_opcion, orden, dtcreate)
                               VALUES (:tbl_pregunta_id, :texto_opcion, :orden, NOW())";
                    $stmtOpcion = $pdo->prepare($qOpcion);

                    foreach ($opciones_respuesta as $orden_opcion => $texto_opcion) {
                        $stmtOpcion->execute([
                            ':tbl_pregunta_id' => $pregunta_id,
                            ':texto_opcion' => trim($texto_opcion),
                            ':orden' => $orden_opcion + 1
                        ]);
                    }
                }

                $preguntasGuardadas++;
            }

            $pdo->commit();

            if (count($errores) > 0) {
                $mensaje = "$preguntasGuardadas pregunta(s) guardada(s). Errores: " . implode(', ', $errores);
                $arrjson = array('output' => array('valid' => true, 'response' => $mensaje));
            } else {
                $arrjson = array('output' => array('valid' => true, 'response' => "$preguntasGuardadas pregunta(s) guardada(s) correctamente"));
            }

        } catch (Exception $e) {
            $pdo->rollBack();
            $arrjson = Util::error_general('Error al guardar las preguntas: ' . $e->getMessage());
        } finally {
            $db->closeConect();
        }

        return $arrjson;
    }

    /**
     * Actualiza numeral y texto_adicional en una o varias preguntas.
     * Payload: ids (CSV o array), numeral, texto_adicional
     */
    public static function renameCapitulo($rqst)
    {
        $fichaId      = isset($rqst['ficha_id'])       ? intval($rqst['ficha_id'])           : 0;
        $capituloViejo = isset($rqst['capitulo_viejo']) ? trim($rqst['capitulo_viejo'])       : '';
        $capituloNuevo = isset($rqst['capitulo_nuevo']) ? trim($rqst['capitulo_nuevo'])       : '';

        if (!$fichaId || $capituloViejo === '' || $capituloNuevo === '') {
            return Util::error_missing_data_description('Datos incompletos para renombrar el capítulo.');
        }

        $db  = new DbConection();
        $pdo = $db->openConect();

        try {
            $q = "UPDATE " . $db->getTable('tbl_preguntas') . "
                  SET capitulo = :nuevo
                  WHERE tbl_ficha_tecnica_encuesta_id = :ficha AND TRIM(capitulo) = TRIM(:viejo)";
            $stmt = $pdo->prepare($q);
            $stmt->execute([':nuevo' => $capituloNuevo, ':ficha' => $fichaId, ':viejo' => $capituloViejo]);
            $affected = $stmt->rowCount();
        } catch (Exception $e) {
            $db->closeConect();
            return Util::error_general('Error al renombrar: ' . $e->getMessage());
        }

        $db->closeConect();
        return ['output' => ['valid' => true, 'response' => "$affected pregunta(s) actualizada(s)."]];
    }

    public static function updateNumeralAdicional($rqst)
    {
        $idsRaw          = isset($rqst['ids'])             ? $rqst['ids']             : '';
        $numeral         = isset($rqst['numeral'])         ? trim($rqst['numeral'])   : null;
        $texto_adicional = isset($rqst['texto_adicional']) ? trim($rqst['texto_adicional']) : null;

        // Aceptar string CSV o array
        if (is_array($idsRaw)) {
            $ids = array_map('intval', $idsRaw);
        } else {
            $ids = array_filter(array_map('intval', explode(',', $idsRaw)));
        }
        $ids = array_values(array_filter($ids, fn($v) => $v > 0));

        if (empty($ids)) {
            return Util::error_missing_data_description('No se proporcionaron IDs de preguntas.');
        }

        $db  = new DbConection();
        $pdo = $db->openConect();

        try {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $q = "UPDATE " . $db->getTable('tbl_preguntas') . "
                  SET numeral = ?, texto_adicional = ?
                  WHERE id IN ($placeholders)";

            $params = [
                ($numeral         !== '' ? $numeral         : null),
                ($texto_adicional !== '' ? $texto_adicional : null),
            ];
            foreach ($ids as $id) { $params[] = $id; }

            $stmt = $pdo->prepare($q);
            $stmt->execute($params);
            $affected = $stmt->rowCount();

        } catch (Exception $e) {
            $db->closeConect();
            return Util::error_general('Error al actualizar: ' . $e->getMessage());
        }

        $db->closeConect();
        return ['output' => ['valid' => true, 'response' => "$affected pregunta(s) actualizada(s)."]];
    }
}