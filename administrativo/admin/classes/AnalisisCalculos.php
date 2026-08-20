<?php
/**
 * Clase para manejar los cálculos aritméticos del investigador en Análisis de Estudio
 */
class AnalisisCalculos
{
    /**
     * Guardar los cálculos de un análisis
     * @param int $analisis_id - ID del análisis de estudio
     * @param array $calculos - Array de operaciones
     * @return bool
     */
    public static function guardarCalculos($analisis_id, $calculos)
    {
        if ($analisis_id <= 0 || empty($calculos)) {
            return false;
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            // Primero eliminar cálculos anteriores de este análisis
            $qDelete = "DELETE FROM " . $db->getTable('tbl_analisis_calculos') . "
                       WHERE tbl_analisis_estudio_id = :analisis_id";
            $stmtDelete = $pdo->prepare($qDelete);
            $stmtDelete->execute([':analisis_id' => $analisis_id]);

            // Insertar nuevos cálculos
            $qInsert = "INSERT INTO " . $db->getTable('tbl_analisis_calculos') . "
                       (tbl_analisis_estudio_id, orden, descripcion, valor1, operador, valor2, resultado)
                       VALUES (:analisis_id, :orden, :descripcion, :valor1, :operador, :valor2, :resultado)";

            $stmtInsert = $pdo->prepare($qInsert);

            foreach ($calculos as $calculo) {
                $stmtInsert->execute([
                    ':analisis_id' => $analisis_id,
                    ':orden' => $calculo['orden'],
                    ':descripcion' => $calculo['descripcion'],
                    ':valor1' => $calculo['valor1'],
                    ':operador' => $calculo['operador'],
                    ':valor2' => $calculo['valor2'],
                    ':resultado' => $calculo['resultado']
                ]);
            }

            $db->closeConect();
            return true;

        } catch (PDOException $e) {
            $db->closeConect();
            return false;
        }
    }

    /**
     * Obtener los cálculos de un análisis
     * @param int $analisis_id - ID del análisis de estudio
     * @return array
     */
    public static function obtenerCalculos($analisis_id)
    {
        if ($analisis_id <= 0) {
            return [];
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        $q = "SELECT id, orden, descripcion, valor1, operador, valor2, resultado
              FROM " . $db->getTable('tbl_analisis_calculos') . "
              WHERE tbl_analisis_estudio_id = :analisis_id
              ORDER BY orden ASC";

        $stmt = $pdo->prepare($q);
        $stmt->execute([':analisis_id' => $analisis_id]);
        $calculos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $db->closeConect();

        return $calculos;
    }

    /**
     * Eliminar los cálculos de un análisis
     * @param int $analisis_id - ID del análisis de estudio
     * @return bool
     */
    public static function eliminarCalculos($analisis_id)
    {
        if ($analisis_id <= 0) {
            return false;
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $qDelete = "DELETE FROM " . $db->getTable('tbl_analisis_calculos') . "
                       WHERE tbl_analisis_estudio_id = :analisis_id";
            $stmtDelete = $pdo->prepare($qDelete);
            $stmtDelete->execute([':analisis_id' => $analisis_id]);

            $db->closeConect();
            return true;

        } catch (PDOException $e) {
            $db->closeConect();
            return false;
        }
    }
}
