<?php
class EspacioGeografico
{
    public function __construct() {}

    public static function getAll($rqst)
    {
        $id = isset($rqst['id']) ? intval($rqst['id']) : 0;

        $db = new DbConection();
        $pdo = $db->openConect();

        $q = "SELECT * FROM " . $db->getTable('tbl_espacio_geografico');
        $params = [];
        if ($id > 0) {
            $q .= " WHERE id = :id";
            $params[':id'] = $id;
        }
        try {
            $stmt = $pdo->prepare($q);
            $stmt->execute($params);
            $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Si se solicitó un id específico, traer también las relaciones departamento/ciudad
            if ($id > 0 && is_array($arr) && count($arr) > 0) {
                $tabla_rel = $db->getTable('tbl_espacio_geografico_x_departamentos_x_ciudades');
                $q_rel = "SELECT codigo_departamento, codigo_ciudad FROM " . $tabla_rel . " WHERE tbl_espacio_geografico_id = :id_rel";
                $stmt_rel = $pdo->prepare($q_rel);
                $stmt_rel->execute([':id_rel' => $id]);
                $rel_rows = $stmt_rel->fetchAll(PDO::FETCH_ASSOC);

                $arr[0]['geografias'] = $rel_rows ? $rel_rows : [];
            }

            $arrjson = array('output' => array('valid' => true, 'response' => $arr ? $arr : []));
        } catch (PDOException $e) {
            $arrjson = Util::error_general('Al obtener los datos de EspacioGeografico.');
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
            $pdo->beginTransaction();

            // Obtener registro original
            $stmt = $pdo->prepare("SELECT * FROM " . $db->getTable('tbl_espacio_geografico') . " WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $orig = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$orig) {
                return Util::error_no_result();
            }

            // Insertar copia
            $tbl_usuario_id = intval($_SESSION['session_user']['id']);
            $qInsert = "INSERT INTO " . $db->getTable('tbl_espacio_geografico') . "
                        (observaciones, tipo_estudio, numero_comunas, numero_zonas, numero_veredas,
                         cantidad_poblacion, numero_votantes, dtcreate, tbl_usuario_id)
                        VALUES (:observaciones, :tipo_estudio, :numero_comunas, :numero_zonas, :numero_veredas,
                                :cantidad_poblacion, :numero_votantes, :dtcreate, :tbl_usuario_id)";
            $stmtInsert = $pdo->prepare($qInsert);
            $stmtInsert->execute([
                ':observaciones'    => 'COPIA - ' . $orig['observaciones'],
                ':tipo_estudio'     => $orig['tipo_estudio'],
                ':numero_comunas'   => $orig['numero_comunas'],
                ':numero_zonas'     => $orig['numero_zonas'],
                ':numero_veredas'   => $orig['numero_veredas'],
                ':cantidad_poblacion' => $orig['cantidad_poblacion'],
                ':numero_votantes'  => $orig['numero_votantes'],
                ':dtcreate'         => Util::date(),
                ':tbl_usuario_id'   => $tbl_usuario_id,
            ]);
            $nuevo_id = $pdo->lastInsertId();

            // Copiar relaciones geográficas
            $tabla_rel = $db->getTable('tbl_espacio_geografico_x_departamentos_x_ciudades');
            $stmtRel = $pdo->prepare("SELECT codigo_departamento, codigo_ciudad FROM $tabla_rel WHERE tbl_espacio_geografico_id = :id");
            $stmtRel->execute([':id' => $id]);
            $rels = $stmtRel->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($rels)) {
                $dtcreate_rel = Util::date();
                $qInsertRel = "INSERT INTO $tabla_rel (tbl_espacio_geografico_id, codigo_departamento, codigo_ciudad, dtcreate) VALUES ";
                $values = [];
                foreach ($rels as $rel) {
                    $dep = $pdo->quote($rel['codigo_departamento']);
                    $mun = $pdo->quote($rel['codigo_ciudad']);
                    $values[] = "('$nuevo_id', $dep, $mun, '$dtcreate_rel')";
                }
                $pdo->query($qInsertRel . implode(', ', $values));
            }

            $pdo->commit();
            $arrjson = array('output' => array('valid' => true, 'response' => $nuevo_id));
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $arrjson = Util::error_general('Error al duplicar el espacio geográfico.');
        } finally {
            $db->closeConect();
        }
        return $arrjson;
    }

    public static function save($rqst)
    {
        $id = isset($rqst['id']) ? intval($rqst['id']) : 0;
        $observaciones = isset($rqst['observaciones']) ? trim($rqst['observaciones']) : '';
        $tipo_estudio = isset($rqst['tipo_estudio']) ? trim($rqst['tipo_estudio']) : '';
        $numero_comunas = isset($rqst['numero_comunas']) ? intval($rqst['numero_comunas']) : 0;
        $numero_zonas = isset($rqst['numero_zonas']) ? intval($rqst['numero_zonas']) : 0;
        $numero_veredas = isset($rqst['numero_veredas']) ? intval($rqst['numero_veredas']) : 0;
        $cantidad_poblacion = isset($rqst['cantidad_poblacion']) ? intval($rqst['cantidad_poblacion']) : 0;
        $numero_votantes = isset($rqst['numero_votantes']) ? intval($rqst['numero_votantes']) : 0;
        $tbl_usuario_id =  intval($_SESSION['session_user']['id']);

        $geografias = isset($rqst['geografias']) ? (array)$rqst['geografias'] : [];


        if (empty($observaciones)) {
            return Util::error_missing_data_description('El campo "Observaciones" es requerido.');
        }
        if (empty($tipo_estudio)) {
            return Util::error_missing_data_description('El campo "Tipo de estudio" es requerido.');
        }
        if (empty($cantidad_poblacion)) {
            return Util::error_missing_data_description('El campo "Cantidad de población" es requerido.');
        }
        if (empty($numero_votantes)) {
            return Util::error_missing_data_description('El campo "Número de votantes" es requerido.');
        }

        $db = new DbConection();
        $pdo = $db->openConect();
        $espacio_geografico_id = $id;

        try {
            $pdo->beginTransaction();

            if ($id > 0) {
                $table = $db->getTable('tbl_espacio_geografico');
                $arrfieldscomma = [
                    'observaciones' => $observaciones,
                    'tipo_estudio' => $tipo_estudio,
                    'numero_comunas' => $numero_comunas,
                    'numero_zonas' => $numero_zonas,
                    'numero_veredas' => $numero_veredas,
                    'cantidad_poblacion' => $cantidad_poblacion,
                    'numero_votantes' => $numero_votantes
                ];
                $arrfieldsnocomma = array();
                $q_update = Util::make_query_update($table, "id = '$id'", $arrfieldscomma, $arrfieldsnocomma);
                $pdo->query($q_update);

                $arrjson = array('output' => array('valid' => true, 'id' => $id));

                // 1. Eliminar las relaciones geográficas existentes (Limpieza)
                $q_delete_rel = "DELETE FROM " . $db->getTable('tbl_espacio_geografico_x_departamentos_x_ciudades') . " WHERE tbl_espacio_geografico_id = '$espacio_geografico_id'";
                $pdo->query($q_delete_rel);
            } else {

                $q = "INSERT INTO " . $db->getTable('tbl_espacio_geografico') . " (observaciones, tipo_estudio, numero_comunas, numero_zonas, numero_veredas, cantidad_poblacion, numero_votantes, dtcreate, tbl_usuario_id) VALUES (:observaciones, :tipo_estudio, :numero_comunas, :numero_zonas, :numero_veredas, :cantidad_poblacion, :numero_votantes, :dtcreate, :tbl_usuario_id)";
                $stmt = $pdo->prepare($q);
                $dtcreate = Util::date();

                $arrparam = [
                    ':observaciones' => $observaciones,
                    ':tipo_estudio' => $tipo_estudio,
                    ':numero_comunas' => $numero_comunas,
                    ':numero_zonas' => $numero_zonas,
                    ':numero_veredas' => $numero_veredas,
                    ':cantidad_poblacion' => $cantidad_poblacion,
                    ':numero_votantes' => $numero_votantes,
                    ':dtcreate' => $dtcreate,
                    ':tbl_usuario_id' => $tbl_usuario_id,
                ];

                $stmt->execute($arrparam);

                // 2. Obtenemos el ID del registro recién insertado
                $espacio_geografico_id = $pdo->lastInsertId();
                $arrjson = array('output' => array('valid' => true, 'response' => $espacio_geografico_id));
            }


            // --- Bloque de Guardado de Relaciones Geográficas (Común a Insert y Update) ---

            if ($espacio_geografico_id > 0 && !empty($geografias)) {
                $q_insert_rel = "INSERT INTO " . $db->getTable('tbl_espacio_geografico_x_departamentos_x_ciudades') . " (tbl_espacio_geografico_id, codigo_departamento, codigo_ciudad, dtcreate) VALUES ";
                $values = [];
                $dtcreate_rel = Util::date();

                // 3. Iteramos sobre el array $geografias (la nueva estructura)
                foreach ($geografias as $geo) {
                    // Aseguramos que las claves existan y sean válidas
                    $departamento_id = isset($geo['departamento']) ? trim($geo['departamento']) : ''; // El código de departamento es un string (ej: "05")
                    $ciudad_id = isset($geo['municipio']) ? trim($geo['municipio']) : ''; // El código de municipio/ciudad es un string (ej: "05001")

                    if (!empty($departamento_id) && !empty($ciudad_id)) {
                        // Usamos quote para sanitizar los valores antes de la inserción en la consulta masiva
                        $dep_id_q = $pdo->quote($departamento_id);
                        $ciudad_id_q = $pdo->quote($ciudad_id);

                        $values[] = "('$espacio_geografico_id', $dep_id_q, $ciudad_id_q, '$dtcreate_rel')";
                    }
                }

                if (!empty($values)) {
                    $q_insert_rel .= implode(', ', $values);
                    $pdo->query($q_insert_rel);
                }
            }


            $pdo->commit();
        } catch (PDOException $e) {
            print_r($e);
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $arrjson = Util::error_general('Guardando datos en Espacio Geografico');
        } finally {
            $db->closeConect();
        }
        return $arrjson;
    }
}