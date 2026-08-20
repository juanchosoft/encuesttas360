<?php

/**
 * Clase Formula
 *
 * Gestiona las operaciones CRUD para fórmulas e indicadores estadísticos
 *
 * @package EstadisticasGobierno
 * @subpackage Classes
 */
class Formula
{
    /**
     * Obtiene todas las fórmulas registradas
     *
     * @param array $rqst Request con filtros opcionales
     * @return array Array con valid y response
     */
    public static function getAll($rqst)
    {
        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $q = "SELECT
                    f.id,
                    f.indicador,
                    f.sigla,
                    f.tipo_indicador,
                    f.formula,
                    f.explicacion,
                    f.observaciones,
                    f.enunciado_antes,
                    f.enunciado_ahora,
                    f.notas_adicionales,
                    f.habilitado,
                    f.dtcreate,
                    f.dtupdate,
                    f.tbl_usuario_id,
                    CONCAT(u.nombre, ' ', u.apellido) AS usuario_nombre
                  FROM " . $db->getTable('tbl_formulas') . " f
                  LEFT JOIN " . $db->getTable('tbl_usuarios') . " u ON f.tbl_usuario_id = u.id
                  ORDER BY f.tipo_indicador ASC, f.indicador ASC";

            $stmt = $pdo->prepare($q);
            $stmt->execute();
            $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $db->closeConect();

            $arrjson = array(
                'output' => array(
                    'valid' => true,
                    'response' => $arr
                )
            );

            return $arrjson;

        } catch (PDOException $e) {
            $db->closeConect();
            return Util::error_general("Error al obtener fórmulas: " . $e->getMessage());
        }
    }

    /**
     * Obtiene una fórmula por ID
     *
     * @param array $rqst Request con el ID
     * @return array Array con valid y response
     */
    public static function getById($rqst)
    {
        if (empty($rqst['id'])) {
            return Util::error_missing_data();
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $q = "SELECT
                    f.*,
                    CONCAT(u.nombre, ' ', u.apellido) AS usuario_nombre
                  FROM " . $db->getTable('tbl_formulas') . " f
                  LEFT JOIN " . $db->getTable('tbl_usuarios') . " u ON f.tbl_usuario_id = u.id
                  WHERE f.id = :id";

            $stmt = $pdo->prepare($q);
            $stmt->bindParam(':id', $rqst['id'], PDO::PARAM_INT);
            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            $db->closeConect();

            if (!$data) {
                return Util::error_no_result();
            }

            $arrjson = array(
                'output' => array(
                    'valid' => true,
                    'response' => $data
                )
            );

            return $arrjson;

        } catch (PDOException $e) {
            $db->closeConect();
            return Util::error_general("Error al obtener fórmula: " . $e->getMessage());
        }
    }

    /**
     * Guarda o actualiza una fórmula
     *
     * @param array $rqst Request con los datos de la fórmula
     * @return array Array con valid y response
     */
    public static function save($rqst)
    {
        // Validaciones
        if (empty($rqst['indicador']) || empty($rqst['sigla']) || empty($rqst['formula']) || empty($rqst['tipo_indicador'])) {
            return Util::error_missing_data_description("Indicador, Sigla, Fórmula, Tipo indicador son obligatorios");
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $userId = SessionData::getUserId();
            $habilitado = isset($rqst['habilitado']) && $rqst['habilitado'] === 'si' ? 'si' : 'no';

            // Verificar si existe otra fórmula con la misma sigla
            $qCheck = "SELECT id FROM " . $db->getTable('tbl_formulas') . "
                       WHERE sigla = :sigla AND id != :id";
            $stmtCheck = $pdo->prepare($qCheck);
            $stmtCheck->bindParam(':sigla', $rqst['sigla']);
            $stmtCheck->bindParam(':id', $rqst['id'], PDO::PARAM_INT);
            $stmtCheck->execute();

            if ($stmtCheck->rowCount() > 0) {
                $db->closeConect();
                return Util::error_registroduplicado("Ya existe una fórmula con la sigla: " . $rqst['sigla']);
            }

            // UPDATE
            if (!empty($rqst['id']) && $rqst['id'] > 0) {
                $q = "UPDATE " . $db->getTable('tbl_formulas') . "
                      SET indicador = :indicador,
                          sigla = :sigla,
                          tipo_indicador = :tipo_indicador,
                          formula = :formula,
                          explicacion = :explicacion,
                          observaciones = :observaciones,
                          enunciado_antes = :enunciado_antes,
                          enunciado_ahora = :enunciado_ahora,
                          notas_adicionales = :notas_adicionales,
                          habilitado = :habilitado,
                          dtupdate = NOW()
                      WHERE id = :id";

                $stmt = $pdo->prepare($q);
                $stmt->bindParam(':id', $rqst['id'], PDO::PARAM_INT);

            } else {
                // INSERT
                $q = "INSERT INTO " . $db->getTable('tbl_formulas') . "
                      (indicador, sigla, tipo_indicador, formula, explicacion, observaciones,
                       enunciado_antes, enunciado_ahora, notas_adicionales,
                       habilitado, tbl_usuario_id, dtcreate)
                      VALUES
                      (:indicador, :sigla, :tipo_indicador, :formula, :explicacion, :observaciones,
                       :enunciado_antes, :enunciado_ahora, :notas_adicionales,
                       :habilitado, :tbl_usuario_id, NOW())";

                $stmt = $pdo->prepare($q);
                $stmt->bindParam(':tbl_usuario_id', $userId, PDO::PARAM_INT);
            }

            // Bind common parameters
            $stmt->bindParam(':indicador', $rqst['indicador']);
            $stmt->bindParam(':sigla', $rqst['sigla']);
            $stmt->bindParam(':tipo_indicador', $rqst['tipo_indicador']);
            $stmt->bindParam(':formula', $rqst['formula']);
            $stmt->bindParam(':explicacion', $rqst['explicacion']);
            $stmt->bindParam(':observaciones', $rqst['observaciones']);
            $stmt->bindParam(':enunciado_antes', $rqst['enunciado_antes']);
            $stmt->bindParam(':enunciado_ahora', $rqst['enunciado_ahora']);
            $stmt->bindParam(':notas_adicionales', $rqst['notas_adicionales']);
            $stmt->bindParam(':habilitado', $habilitado);

            $stmt->execute();

            $id = !empty($rqst['id']) ? $rqst['id'] : $pdo->lastInsertId();

            $db->closeConect();

            $arrjson = array(
                'output' => array(
                    'valid' => true,
                    'response' => array(
                        'id' => $id,
                        'message' => 'Fórmula guardada exitosamente',
                        'action' => !empty($rqst['id']) ? 'updated' : 'created'
                    )
                )
            );

            return $arrjson;

        } catch (PDOException $e) {
            $db->closeConect();
            return Util::error_general("Error al guardar fórmula: " . $e->getMessage());
        }
    }

    /**
     * Elimina una fórmula (soft delete - marca como deshabilitado)
     *
     * @param array $rqst Request con el ID
     * @return array Array con valid y response
     */
    public static function delete($rqst)
    {
        if (empty($rqst['id'])) {
            return Util::error_missing_data();
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $q = "UPDATE " . $db->getTable('tbl_formulas') . "
                  SET habilitado = 'no'
                  WHERE id = :id";

            $stmt = $pdo->prepare($q);
            $stmt->bindParam(':id', $rqst['id'], PDO::PARAM_INT);
            $stmt->execute();

            $db->closeConect();

            $arrjson = array(
                'output' => array(
                    'valid' => true,
                    'response' => array(
                        'message' => 'Fórmula deshabilitada exitosamente'
                    )
                )
            );

            return $arrjson;

        } catch (PDOException $e) {
            $db->closeConect();
            return Util::error_general("Error al eliminar fórmula: " . $e->getMessage());
        }
    }

    /**
     * Busca fórmulas por término
     *
     * @param array $rqst Request con el término de búsqueda
     * @return array Array con valid y response
     */
    public static function search($rqst)
    {
        if (empty($rqst['term'])) {
            return self::getAll($rqst);
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $term = '%' . $rqst['term'] . '%';

            $q = "SELECT
                    f.*,
                    CONCAT(u.nombre, ' ', u.apellido) AS usuario_nombre
                  FROM " . $db->getTable('tbl_formulas') . " f
                  LEFT JOIN " . $db->getTable('tbl_usuarios') . " u ON f.tbl_usuario_id = u.id
                  WHERE f.indicador LIKE :term
                     OR f.sigla LIKE :term
                     OR f.formula LIKE :term
                  ORDER BY f.indicador ASC";

            $stmt = $pdo->prepare($q);
            $stmt->bindParam(':term', $term);
            $stmt->execute();
            $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $db->closeConect();

            $arrjson = array(
                'output' => array(
                    'valid' => true,
                    'response' => $arr
                )
            );

            return $arrjson;

        } catch (PDOException $e) {
            $db->closeConect();
            return Util::error_general("Error en búsqueda: " . $e->getMessage());
        }
    }
}
