<?php

/**
 * Clase Dashboard
 * Obtiene estadísticas reales del sistema electoral
 */
class Dashboard
{
    /**
     * Obtiene la lista de grillas disponibles
     */
    public static function getGrillas($rqst)
    {
        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $q = "SELECT id, grilla as nombre, descripcion_grilla as descripcion
                  FROM " . $db->getTable('tbl_grilla') . "
                  WHERE habilitado = 'si'
                  ORDER BY grilla ASC";

            $stmt = $pdo->query($q);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $db->closeConect();

            return [
                'output' => [
                    'valid' => true,
                    'response' => $data
                ]
            ];
        } catch (Exception $e) {
            $db->closeConect();
            return Util::error_general('Error obteniendo grillas: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene las estadísticas principales del dashboard
     * Filtradas por grilla si se proporciona grilla_id
     */
    public static function getEstadisticasPrincipales($rqst)
    {
        $db = new DbConection();
        $pdo = $db->openConect();

        $grilla_id = isset($rqst['grilla_id']) && !empty($rqst['grilla_id']) ? $rqst['grilla_id'] : null;

        try {
            $stats = [];

            // 1. TOTAL DE VOTANTES (filtrados por grilla si aplica)
            if ($grilla_id) {
                $q = "SELECT COUNT(DISTINCT v.id) as total
                      FROM " . $db->getTable('tbl_votantes') . " v
                      INNER JOIN " . $db->getTable('tbl_grilla_sesion_votacion') . " gsv ON v.id = gsv.tbl_votante_id
                      WHERE v.estado = 'activo' AND gsv.tbl_grilla_id = :grilla_id";
                $stmt = $pdo->prepare($q);
                $stmt->execute([':grilla_id' => $grilla_id]);
            } else {
                $q = "SELECT COUNT(*) as total FROM " . $db->getTable('tbl_votantes') . " WHERE estado = 'activo'";
                $stmt = $pdo->query($q);
            }
            $stats['total_votantes'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            // 2. TOTAL DE GRILLAS ACTIVAS
            $q = "SELECT COUNT(*) as total FROM " . $db->getTable('tbl_grilla') . " WHERE habilitado = 'si'";
            $stmt = $pdo->query($q);
            $stats['total_grillas'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            // 3. TOTAL DE ANÁLISIS DE ESTUDIO
            $q = "SELECT COUNT(*) as total FROM " . $db->getTable('tbl_analisis_estudio');
            $stmt = $pdo->query($q);
            $stats['total_analisis'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            // 4. TOTAL DE PERSONAL POLÍTICO (CANDIDATOS) - filtrados por grilla si aplica
            if ($grilla_id) {
                $q = "SELECT COUNT(DISTINCT p.id) as total
                      FROM " . $db->getTable('tbl_participantes') . " p
                      INNER JOIN " . $db->getTable('tbl_grilla_x_tbl_participantes') . " gxp ON p.id = gxp.tbl_participante_id
                      WHERE p.habilitado = 'si' AND gxp.tbl_grilla_id = :grilla_id";
                $stmt = $pdo->prepare($q);
                $stmt->execute([':grilla_id' => $grilla_id]);
            } else {
                $q = "SELECT COUNT(*) as total FROM " . $db->getTable('tbl_participantes') . " WHERE habilitado = 'si'";
                $stmt = $pdo->query($q);
            }
            $stats['total_candidatos'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            // 5. TOTAL DE SONDEOS
            $q = "SELECT COUNT(*) as total FROM " . $db->getTable('tbl_sondeo') . " WHERE habilitado = 'si'";
            $stmt = $pdo->query($q);
            $stats['total_sondeos'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            // 6. TOTAL DE FÓRMULAS
            $q = "SELECT COUNT(*) as total FROM " . $db->getTable('tbl_formulas') . " WHERE habilitado = 'si'";
            $stmt = $pdo->query($q);
            $stats['total_formulas'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            $db->closeConect();

            return [
                'output' => [
                    'valid' => true,
                    'response' => $stats
                ]
            ];
        } catch (Exception $e) {
            $db->closeConect();
            return Util::error_general('Error obteniendo estadísticas principales: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene distribución de votantes por ideología
     * Filtrada por grilla si se proporciona grilla_id
     */
    public static function getVotantesPorIdeologia($rqst)
    {
        $db = new DbConection();
        $pdo = $db->openConect();

        $grilla_id = isset($rqst['grilla_id']) && !empty($rqst['grilla_id']) ? $rqst['grilla_id'] : null;

        try {
            if ($grilla_id) {
                $q = "SELECT
                        v.ideologia,
                        COUNT(DISTINCT v.id) as total
                      FROM " . $db->getTable('tbl_votantes') . " v
                      INNER JOIN " . $db->getTable('tbl_grilla_sesion_votacion') . " gsv ON v.id = gsv.tbl_votante_id
                      WHERE v.estado = 'activo' AND v.ideologia IS NOT NULL AND v.ideologia != ''
                        AND gsv.tbl_grilla_id = :grilla_id
                      GROUP BY v.ideologia
                      ORDER BY total DESC";
                $stmt = $pdo->prepare($q);
                $stmt->execute([':grilla_id' => $grilla_id]);
            } else {
                $q = "SELECT
                        ideologia,
                        COUNT(*) as total
                      FROM " . $db->getTable('tbl_votantes') . "
                      WHERE estado = 'activo' AND ideologia IS NOT NULL AND ideologia != ''
                      GROUP BY ideologia
                      ORDER BY total DESC";
                $stmt = $pdo->query($q);
            }

            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $db->closeConect();

            return [
                'output' => [
                    'valid' => true,
                    'response' => $data
                ]
            ];
        } catch (Exception $e) {
            $db->closeConect();
            return Util::error_general('Error obteniendo votantes por ideología: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene distribución de votantes por género
     * Filtrada por grilla si se proporciona grilla_id
     */
    public static function getVotantesPorGenero($rqst)
    {
        $db = new DbConection();
        $pdo = $db->openConect();

        $grilla_id = isset($rqst['grilla_id']) && !empty($rqst['grilla_id']) ? $rqst['grilla_id'] : null;

        try {
            if ($grilla_id) {
                $q = "SELECT
                        v.genero,
                        COUNT(DISTINCT v.id) as total
                      FROM " . $db->getTable('tbl_votantes') . " v
                      INNER JOIN " . $db->getTable('tbl_grilla_sesion_votacion') . " gsv ON v.id = gsv.tbl_votante_id
                      WHERE v.estado = 'activo' AND v.genero IS NOT NULL AND v.genero != ''
                        AND gsv.tbl_grilla_id = :grilla_id
                      GROUP BY v.genero
                      ORDER BY total DESC";
                $stmt = $pdo->prepare($q);
                $stmt->execute([':grilla_id' => $grilla_id]);
            } else {
                $q = "SELECT
                        genero,
                        COUNT(*) as total
                      FROM " . $db->getTable('tbl_votantes') . "
                      WHERE estado = 'activo' AND genero IS NOT NULL AND genero != ''
                      GROUP BY genero
                      ORDER BY total DESC";
                $stmt = $pdo->query($q);
            }

            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $db->closeConect();

            return [
                'output' => [
                    'valid' => true,
                    'response' => $data
                ]
            ];
        } catch (Exception $e) {
            $db->closeConect();
            return Util::error_general('Error obteniendo votantes por género: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene distribución de votantes por rango de edad
     * Filtrada por grilla si se proporciona grilla_id
     */
    public static function getVotantesPorEdad($rqst)
    {
        $db = new DbConection();
        $pdo = $db->openConect();

        $grilla_id = isset($rqst['grilla_id']) && !empty($rqst['grilla_id']) ? $rqst['grilla_id'] : null;

        try {
            if ($grilla_id) {
                $q = "SELECT
                        v.rango_edad,
                        COUNT(DISTINCT v.id) as total
                      FROM " . $db->getTable('tbl_votantes') . " v
                      INNER JOIN " . $db->getTable('tbl_grilla_sesion_votacion') . " gsv ON v.id = gsv.tbl_votante_id
                      WHERE v.estado = 'activo' AND v.rango_edad IS NOT NULL AND v.rango_edad != ''
                        AND gsv.tbl_grilla_id = :grilla_id
                      GROUP BY v.rango_edad
                      ORDER BY total DESC";
                $stmt = $pdo->prepare($q);
                $stmt->execute([':grilla_id' => $grilla_id]);
            } else {
                $q = "SELECT
                        rango_edad,
                        COUNT(*) as total
                      FROM " . $db->getTable('tbl_votantes') . "
                      WHERE estado = 'activo' AND rango_edad IS NOT NULL AND rango_edad != ''
                      GROUP BY rango_edad
                      ORDER BY total DESC";
                $stmt = $pdo->query($q);
            }

            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $db->closeConect();

            return [
                'output' => [
                    'valid' => true,
                    'response' => $data
                ]
            ];
        } catch (Exception $e) {
            $db->closeConect();
            return Util::error_general('Error obteniendo votantes por edad: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene análisis de estudio por mes (últimos 6 meses)
     */
    public static function getAnalisisPorMes($rqst)
    {
        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $q = "SELECT
                    DATE_FORMAT(dtcreate, '%Y-%m') as mes,
                    DATE_FORMAT(dtcreate, '%M %Y') as mes_nombre,
                    COUNT(*) as total
                  FROM " . $db->getTable('tbl_analisis_estudio') . "
                  WHERE dtcreate >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                  GROUP BY DATE_FORMAT(dtcreate, '%Y-%m')
                  ORDER BY mes ASC";

            $stmt = $pdo->query($q);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $db->closeConect();

            return [
                'output' => [
                    'valid' => true,
                    'response' => $data
                ]
            ];
        } catch (Exception $e) {
            $db->closeConect();
            return Util::error_general('Error obteniendo análisis por mes: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene top 5 candidatos con más análisis
     * Filtrado por grilla si se proporciona grilla_id
     */
    public static function getTopCandidatos($rqst)
    {
        $db = new DbConection();
        $pdo = $db->openConect();

        $grilla_id = isset($rqst['grilla_id']) && !empty($rqst['grilla_id']) ? $rqst['grilla_id'] : null;

        try {
            if ($grilla_id) {
                $q = "SELECT
                        p.nombre_completo,
                        p.foto,
                        COUNT(a.id) as total_analisis
                      FROM " . $db->getTable('tbl_analisis_estudio') . " a
                      INNER JOIN " . $db->getTable('tbl_participantes') . " p ON a.tbl_participante_id = p.id
                      INNER JOIN " . $db->getTable('tbl_grilla_x_tbl_participantes') . " gxp ON p.id = gxp.tbl_participante_id
                      WHERE p.habilitado = 'si' AND gxp.tbl_grilla_id = :grilla_id
                      GROUP BY p.id, p.nombre_completo, p.foto
                      ORDER BY total_analisis DESC
                      LIMIT 5";
                $stmt = $pdo->prepare($q);
                $stmt->execute([':grilla_id' => $grilla_id]);
            } else {
                $q = "SELECT
                        p.nombre_completo,
                        p.foto,
                        COUNT(a.id) as total_analisis
                      FROM " . $db->getTable('tbl_analisis_estudio') . " a
                      INNER JOIN " . $db->getTable('tbl_participantes') . " p ON a.tbl_participante_id = p.id
                      WHERE p.habilitado = 'si'
                      GROUP BY p.id, p.nombre_completo, p.foto
                      ORDER BY total_analisis DESC
                      LIMIT 5";
                $stmt = $pdo->query($q);
            }

            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $db->closeConect();

            return [
                'output' => [
                    'valid' => true,
                    'response' => $data
                ]
            ];
        } catch (Exception $e) {
            $db->closeConect();
            return Util::error_general('Error obteniendo top candidatos: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene distribución de votantes por nivel de ingresos
     * Filtrada por grilla si se proporciona grilla_id
     */
    public static function getVotantesPorIngresos($rqst)
    {
        $db = new DbConection();
        $pdo = $db->openConect();

        $grilla_id = isset($rqst['grilla_id']) && !empty($rqst['grilla_id']) ? $rqst['grilla_id'] : null;

        try {
            if ($grilla_id) {
                $q = "SELECT
                        v.nivel_ingresos,
                        COUNT(DISTINCT v.id) as total
                      FROM " . $db->getTable('tbl_votantes') . " v
                      INNER JOIN " . $db->getTable('tbl_grilla_sesion_votacion') . " gsv ON v.id = gsv.tbl_votante_id
                      WHERE v.estado = 'activo' AND v.nivel_ingresos IS NOT NULL AND v.nivel_ingresos != ''
                        AND gsv.tbl_grilla_id = :grilla_id
                      GROUP BY v.nivel_ingresos
                      ORDER BY total DESC";
                $stmt = $pdo->prepare($q);
                $stmt->execute([':grilla_id' => $grilla_id]);
            } else {
                $q = "SELECT
                        nivel_ingresos,
                        COUNT(*) as total
                      FROM " . $db->getTable('tbl_votantes') . "
                      WHERE estado = 'activo' AND nivel_ingresos IS NOT NULL AND nivel_ingresos != ''
                      GROUP BY nivel_ingresos
                      ORDER BY total DESC";
                $stmt = $pdo->query($q);
            }

            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $db->closeConect();

            return [
                'output' => [
                    'valid' => true,
                    'response' => $data
                ]
            ];
        } catch (Exception $e) {
            $db->closeConect();
            return Util::error_general('Error obteniendo votantes por ingresos: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene estadísticas de grillas por estado
     */
    public static function getGrillasPorEstado($rqst)
    {
        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $q = "SELECT
                    CASE
                        WHEN habilitado = 'si' THEN 'Activas'
                        ELSE 'Inactivas'
                    END as estado,
                    COUNT(*) as total
                  FROM " . $db->getTable('tbl_grilla') . "
                  GROUP BY habilitado";

            $stmt = $pdo->query($q);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $db->closeConect();

            return [
                'output' => [
                    'valid' => true,
                    'response' => $data
                ]
            ];
        } catch (Exception $e) {
            $db->closeConect();
            return Util::error_general('Error obteniendo grillas por estado: ' . $e->getMessage());
        }
    }
}
