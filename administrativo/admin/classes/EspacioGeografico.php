<?php
class EspacioGeografico
{
    public function __construct() {}

    /**
     * Normaliza el tipo de estudio a valores canónicos.
     */
    public static function normalizeTipoEstudio($tipo)
    {
        $t = strtolower(trim((string)$tipo));
        if ($t === 'nacional') {
            return 'Nacional';
        }
        if ($t === 'departamental') {
            return 'Departamental';
        }
        if ($t === 'municipal') {
            return 'Municipal';
        }
        return trim((string)$tipo);
    }

    public static function getAll($rqst)
    {
        $id = isset($rqst['id']) ? intval($rqst['id']) : 0;
        $incluirInactivos = !empty($rqst['incluir_inactivos']);

        $db = new DbConection();
        $pdo = $db->openConect();

        $q = "SELECT * FROM " . $db->getTable('tbl_espacio_geografico');
        $params = [];
        $where = [];

        if ($id > 0) {
            $where[] = "id = :id";
            $params[':id'] = $id;
        } elseif (!$incluirInactivos) {
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

            if ($id > 0 && is_array($arr) && count($arr) > 0) {
                $tabla_rel = $db->getTable('tbl_espacio_geografico_x_departamentos_x_ciudades');
                $q_rel = "SELECT codigo_departamento, codigo_ciudad FROM " . $tabla_rel . " WHERE tbl_espacio_geografico_id = :id_rel";
                $stmt_rel = $pdo->prepare($q_rel);
                $stmt_rel->execute([':id_rel' => $id]);
                $rel_rows = $stmt_rel->fetchAll(PDO::FETCH_ASSOC);
                $arr[0]['geografias'] = $rel_rows ? $rel_rows : [];
            }

            $arrjson = ['output' => ['valid' => true, 'response' => $arr ? $arr : []]];
        } catch (PDOException $e) {
            $arrjson = Util::error_general('Al obtener los datos de EspacioGeografico.');
        } finally {
            $db->closeConect();
        }
        return $arrjson;
    }

    /**
     * Valida si una ubicación (depto/municipio) está dentro del espacio geográfico.
     * Útil para geolocalización y visibilidad territorial.
     */
    public static function validarUbicacion($espacioId, $codigoDepto, $codigoMunicipio)
    {
        $espacioId = (int)$espacioId;
        if ($espacioId <= 0) {
            return true;
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $stmt = $pdo->prepare(
                "SELECT tipo_estudio FROM " . $db->getTable('tbl_espacio_geografico') . "
                 WHERE id = :id AND (habilitado = 'si' OR habilitado IS NULL) LIMIT 1"
            );
            $stmt->execute([':id' => $espacioId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) {
                $db->closeConect();
                return false;
            }

            $tipo = strtolower(trim((string)$row['tipo_estudio']));
            if ($tipo === 'nacional') {
                $db->closeConect();
                return true;
            }

            $tblRel = $db->getTable('tbl_espacio_geografico_x_departamentos_x_ciudades');
            $dep = Util::normalizeCodigoDepartamento($codigoDepto);

            if ($tipo === 'departamental') {
                $stmtDep = $pdo->prepare(
                    "SELECT COUNT(*) FROM $tblRel
                     WHERE tbl_espacio_geografico_id = :id
                     AND LPAD(CAST(codigo_departamento AS UNSIGNED), 2, '0') = :dep"
                );
                $stmtDep->execute([':id' => $espacioId, ':dep' => $dep]);
                $ok = (int)$stmtDep->fetchColumn() > 0;
                $db->closeConect();
                return $ok;
            }

            $muni = Util::normalizeCodigoMunicipio($codigoMunicipio);
            $stmtMuni = $pdo->prepare(
                "SELECT COUNT(*) FROM $tblRel
                 WHERE tbl_espacio_geografico_id = :id
                 AND LPAD(CAST(codigo_ciudad AS UNSIGNED), 5, '0') = :muni"
            );
            $stmtMuni->execute([':id' => $espacioId, ':muni' => $muni]);
            $ok = (int)$stmtMuni->fetchColumn() > 0;
            $db->closeConect();
            return $ok;
        } catch (Exception $e) {
            $db->closeConect();
            return false;
        }
    }

    /**
     * Indica si el espacio está asociado a alguna ficha técnica / encuesta activa.
     */
    public static function estaEnUso($espacioId)
    {
        $espacioId = (int)$espacioId;
        if ($espacioId <= 0) {
            return false;
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $q = "SELECT COUNT(*) FROM " . $db->getTable('tbl_ficha_tecnica_encuestas') . "
                  WHERE tbl_espacio_geografico_id = :id AND habilitado = 'si'";
            $stmt = $pdo->prepare($q);
            $stmt->execute([':id' => $espacioId]);
            return (int)$stmt->fetchColumn() > 0;
        } catch (Exception $e) {
            return true;
        } finally {
            $db->closeConect();
        }
    }

    public static function delete($rqst)
    {
        $id = isset($rqst['id']) ? (int)$rqst['id'] : 0;
        if ($id <= 0) {
            return Util::error_missing_data_description('ID inválido.');
        }

        if (self::estaEnUso($id)) {
            return Util::error_general(
                'No se puede eliminar: el espacio geográfico está asociado a una encuesta activa.'
            );
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $stmt = $pdo->prepare(
                "UPDATE " . $db->getTable('tbl_espacio_geografico') . "
                 SET habilitado = 'no' WHERE id = :id AND (habilitado = 'si' OR habilitado IS NULL)"
            );
            $stmt->execute([':id' => $id]);

            if ($stmt->rowCount() === 0) {
                return Util::error_no_result();
            }

            return ['output' => ['valid' => true, 'response' => $id]];
        } catch (PDOException $e) {
            return Util::error_general('Error al eliminar el espacio geográfico.');
        } finally {
            $db->closeConect();
        }
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

            $stmt = $pdo->prepare(
                "SELECT * FROM " . $db->getTable('tbl_espacio_geografico') . "
                 WHERE id = :id AND (habilitado = 'si' OR habilitado IS NULL)"
            );
            $stmt->execute([':id' => $id]);
            $orig = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$orig) {
                return Util::error_no_result();
            }

            $tbl_usuario_id = intval($_SESSION['session_user']['id']);
            $qInsert = "INSERT INTO " . $db->getTable('tbl_espacio_geografico') . "
                        (observaciones, tipo_estudio, numero_comunas, numero_zonas, numero_veredas,
                         cantidad_poblacion, numero_votantes, dtcreate, tbl_usuario_id, habilitado)
                        VALUES (:observaciones, :tipo_estudio, :numero_comunas, :numero_zonas, :numero_veredas,
                                :cantidad_poblacion, :numero_votantes, :dtcreate, :tbl_usuario_id, 'si')";
            $stmtInsert = $pdo->prepare($qInsert);
            $stmtInsert->execute([
                ':observaciones'      => 'COPIA - ' . $orig['observaciones'],
                ':tipo_estudio'       => $orig['tipo_estudio'],
                ':numero_comunas'     => $orig['numero_comunas'],
                ':numero_zonas'       => $orig['numero_zonas'],
                ':numero_veredas'     => $orig['numero_veredas'],
                ':cantidad_poblacion' => $orig['cantidad_poblacion'],
                ':numero_votantes'    => $orig['numero_votantes'],
                ':dtcreate'           => Util::date(),
                ':tbl_usuario_id'     => $tbl_usuario_id,
            ]);
            $nuevo_id = $pdo->lastInsertId();

            $tabla_rel = $db->getTable('tbl_espacio_geografico_x_departamentos_x_ciudades');
            $stmtRel = $pdo->prepare(
                "SELECT codigo_departamento, codigo_ciudad FROM $tabla_rel WHERE tbl_espacio_geografico_id = :id"
            );
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
            return ['output' => ['valid' => true, 'response' => $nuevo_id]];
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            return Util::error_general('Error al duplicar el espacio geográfico.');
        } finally {
            $db->closeConect();
        }
    }

    /**
     * Normaliza y valida relaciones geográficas según tipo de estudio.
     */
    private static function normalizarGeografias($tipoEstudio, array $geografias)
    {
        $tipo = strtolower(trim((string)$tipoEstudio));
        $out = [];

        if ($tipo === 'nacional') {
            return $out;
        }

        foreach ($geografias as $geo) {
            $dep = isset($geo['departamento']) ? trim((string)$geo['departamento']) : '';
            $mun = isset($geo['municipio']) ? trim((string)$geo['municipio']) : '';

            if ($dep === '' || $dep === '00') {
                continue;
            }

            if ($tipo === 'departamental') {
                $out[] = ['departamento' => $dep, 'municipio' => ''];
                continue;
            }

            if ($tipo === 'municipal' && $mun !== '') {
                $out[] = ['departamento' => $dep, 'municipio' => $mun];
            }
        }

        if ($tipo === 'departamental') {
            $deps = [];
            foreach ($out as $g) {
                $deps[$g['departamento']] = $g;
            }
            $out = array_values($deps);
            if (count($out) !== 1) {
                return ['error' => 'Para estudios departamentales debe seleccionar exactamente un departamento.'];
            }
        }

        if ($tipo === 'municipal') {
            $deps = [];
            foreach ($out as $g) {
                $deps[$g['departamento']] = true;
            }
            if (count($deps) !== 1) {
                return ['error' => 'Para estudios municipales debe seleccionar un solo departamento.'];
            }
            if (count($out) < 1) {
                return ['error' => 'Debe seleccionar al menos un municipio.'];
            }
        }

        return $out;
    }

    public static function save($rqst)
    {
        $id = isset($rqst['id']) ? intval($rqst['id']) : 0;
        $observaciones = isset($rqst['observaciones']) ? trim($rqst['observaciones']) : '';
        $tipo_estudio = self::normalizeTipoEstudio($rqst['tipo_estudio'] ?? '');
        $cantidad_poblacion = isset($rqst['cantidad_poblacion']) ? intval($rqst['cantidad_poblacion']) : 0;
        $numero_votantes = isset($rqst['numero_votantes']) ? intval($rqst['numero_votantes']) : 0;
        $tbl_usuario_id = intval($_SESSION['session_user']['id']);
        $geografias = isset($rqst['geografias']) ? (array)$rqst['geografias'] : [];

        if ($observaciones === '') {
            return Util::error_missing_data_description('El campo "Observaciones" es requerido.');
        }
        if ($tipo_estudio === '') {
            return Util::error_missing_data_description('El campo "Tipo de estudio" es requerido.');
        }
        if ($cantidad_poblacion <= 0) {
            return Util::error_missing_data_description('El campo "Cantidad de población" es requerido.');
        }
        if ($numero_votantes <= 0) {
            return Util::error_missing_data_description('El campo "Número de encuestados" es requerido.');
        }

        $geoNormalizadas = self::normalizarGeografias($tipo_estudio, $geografias);
        if (isset($geoNormalizadas['error'])) {
            return Util::error_missing_data_description($geoNormalizadas['error']);
        }

        $tipoLower = strtolower($tipo_estudio);
        if ($tipoLower === 'departamental' && empty($geoNormalizadas)) {
            return Util::error_missing_data_description('Debe seleccionar un departamento.');
        }
        if ($tipoLower === 'municipal' && empty($geoNormalizadas)) {
            return Util::error_missing_data_description('Debe seleccionar departamento y al menos un municipio.');
        }

        $db = new DbConection();
        $pdo = $db->openConect();
        $espacio_geografico_id = $id;

        try {
            $pdo->beginTransaction();

            $fields = [
                'observaciones'       => $observaciones,
                'tipo_estudio'        => $tipo_estudio,
                'numero_comunas'      => 0,
                'numero_zonas'        => 0,
                'numero_veredas'      => 0,
                'cantidad_poblacion'  => $cantidad_poblacion,
                'numero_votantes'     => $numero_votantes,
            ];

            if ($id > 0) {
                $table = $db->getTable('tbl_espacio_geografico');
                $q_update = Util::make_query_update($table, "id = '$id'", $fields, []);
                $pdo->query($q_update);
                $arrjson = ['output' => ['valid' => true, 'id' => $id]];

                $q_delete_rel = "DELETE FROM " . $db->getTable('tbl_espacio_geografico_x_departamentos_x_ciudades')
                    . " WHERE tbl_espacio_geografico_id = '$espacio_geografico_id'";
                $pdo->query($q_delete_rel);
            } else {
                $q = "INSERT INTO " . $db->getTable('tbl_espacio_geografico') . "
                      (observaciones, tipo_estudio, numero_comunas, numero_zonas, numero_veredas,
                       cantidad_poblacion, numero_votantes, dtcreate, tbl_usuario_id, habilitado)
                      VALUES (:observaciones, :tipo_estudio, 0, 0, 0, :cantidad_poblacion, :numero_votantes,
                              :dtcreate, :tbl_usuario_id, 'si')";
                $stmt = $pdo->prepare($q);
                $stmt->execute([
                    ':observaciones'      => $observaciones,
                    ':tipo_estudio'       => $tipo_estudio,
                    ':cantidad_poblacion' => $cantidad_poblacion,
                    ':numero_votantes'    => $numero_votantes,
                    ':dtcreate'           => Util::date(),
                    ':tbl_usuario_id'     => $tbl_usuario_id,
                ]);
                $espacio_geografico_id = $pdo->lastInsertId();
                $arrjson = ['output' => ['valid' => true, 'response' => $espacio_geografico_id]];
            }

            if ($espacio_geografico_id > 0 && !empty($geoNormalizadas)) {
                $values = [];
                $dtcreate_rel = Util::date();
                foreach ($geoNormalizadas as $geo) {
                    $dep_id_q = $pdo->quote($geo['departamento']);
                    $ciudad_id_q = $pdo->quote($geo['municipio']);
                    $values[] = "('$espacio_geografico_id', $dep_id_q, $ciudad_id_q, '$dtcreate_rel')";
                }
                if (!empty($values)) {
                    $q_insert_rel = "INSERT INTO " . $db->getTable('tbl_espacio_geografico_x_departamentos_x_ciudades')
                        . " (tbl_espacio_geografico_id, codigo_departamento, codigo_ciudad, dtcreate) VALUES "
                        . implode(', ', $values);
                    $pdo->query($q_insert_rel);
                }
            }

            $pdo->commit();
        } catch (PDOException $e) {
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
