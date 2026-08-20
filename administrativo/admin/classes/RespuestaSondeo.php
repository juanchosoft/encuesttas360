<?php
class RespuestaSondeo
{
    public function __construct() {}

    /**
     * Obtiene todos los sondeos disponibles para mostrar en el selector
     */
    public static function getSondeosDisponibles($rqst)
    {
        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $q = "SELECT DISTINCT s.id, s.sondeo, s.descripcion_sondeo, s.tipo_sondeo, s.tipo_inferenciales,
                    s.fecha_inicio, s.fecha_fin, s.habilitado,
                    COUNT(DISTINCT rs.id) as total_respuestas
                FROM " . $db->getTable('tbl_sondeo') . " s
                LEFT JOIN " . $db->getTable('tbl_respuestas_sondeos') . " rs ON s.id = rs.tbl_sondeo_id
                WHERE s.habilitado = 'si'
                GROUP BY s.id
                ORDER BY s.dtcreate DESC";

            $stmt = $pdo->prepare($q);
            $stmt->execute();
            $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $arrjson = array('output' => array('valid' => true, 'response' => $arr));
        } catch (PDOException $e) {
            $arrjson = Util::error_general('Al obtener los sondeos disponibles.');
        } finally {
            $db->closeConect();
        }
        return $arrjson;
    }

    /**
     * Obtiene las estadísticas generales de un sondeo
     */
    public static function getEstadisticasGenerales($rqst)
    {
        $tbl_sondeo_id = isset($rqst['tbl_sondeo_id']) ? intval($rqst['tbl_sondeo_id']) : 0;

        if ($tbl_sondeo_id <= 0) {
            return Util::error_missing_data_description('El ID del sondeo es requerido.');
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            // Estadísticas generales
            $qGeneral = "SELECT
                COUNT(DISTINCT id) as total_respuestas,
                COUNT(DISTINCT tbl_votante_id) as votantes_unicos,
                COUNT(DISTINCT DATE(dtcreate)) as dias_activo
                FROM " . $db->getTable('tbl_respuestas_sondeos') . "
                WHERE tbl_sondeo_id = :sondeo_id";

            $stmt = $pdo->prepare($qGeneral);
            $stmt->execute([':sondeo_id' => $tbl_sondeo_id]);
            $general = $stmt->fetch(PDO::FETCH_ASSOC);

            // Respuestas por opción
            $qOpciones = "SELECT
                so.opcion as respuesta_opcion,
                COUNT(rs.id) as cantidad,
                ROUND((COUNT(rs.id) * 100.0 / (SELECT COUNT(*) FROM " . $db->getTable('tbl_respuestas_sondeos') . "
                    WHERE tbl_sondeo_id = :sondeo_id AND tbl_sondeo_x_opciones_id IS NOT NULL)), 2) as porcentaje
                FROM " . $db->getTable('tbl_sondeo_x_opciones') . " so
                LEFT JOIN " . $db->getTable('tbl_respuestas_sondeos') . " rs ON so.id = rs.tbl_sondeo_x_opciones_id AND rs.tbl_sondeo_id = :sondeo_id
                WHERE so.tbl_sondeo_id = :sondeo_id
                GROUP BY so.id, so.opcion
                ORDER BY cantidad DESC";

            $stmt = $pdo->prepare($qOpciones);
            $stmt->execute([':sondeo_id' => $tbl_sondeo_id]);
            $opciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Si el sondeo tiene candidatos, obtener resultados por candidato
            $qCandidatos = "SELECT
                p.id, p.nombre_completo, p.foto,
                cp.nombre as cargo_publico,
                pp.nombre_partido,
                COUNT(rs.id) as votos,
                ROUND((COUNT(rs.id) * 100.0 / (SELECT COUNT(*) FROM " . $db->getTable('tbl_respuestas_sondeos') . "
                    WHERE tbl_sondeo_id = :sondeo_id AND tbl_candidato_id IS NOT NULL)), 2) as porcentaje
                FROM " . $db->getTable('tbl_participantes') . " p
                INNER JOIN " . $db->getTable('tbl_sondeo_x_tbl_participantes') . " sxp ON p.id = sxp.tbl_participante_id
                LEFT JOIN " . $db->getTable('tbl_respuestas_sondeos') . " rs ON p.id = rs.tbl_candidato_id AND rs.tbl_sondeo_id = :sondeo_id
                LEFT JOIN " . $db->getTable('tbl_cargos_publicos') . " cp ON p.tbl_cargo_publico_id = cp.id
                LEFT JOIN " . $db->getTable('tbl_participantes_x_partidos_politicos') . " pxp ON p.id = pxp.tbl_participante_id
                LEFT JOIN " . $db->getTable('tbl_partidos_politicos') . " pp ON pxp.tbl_partido_politico_id = pp.id
                WHERE sxp.tbl_sondeo_id = :sondeo_id
                GROUP BY p.id
                ORDER BY votos DESC";

            $stmt = $pdo->prepare($qCandidatos);
            $stmt->execute([':sondeo_id' => $tbl_sondeo_id]);
            $candidatos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $arrjson = array('output' => array('valid' => true, 'response' => array(
                'general' => $general,
                'opciones' => $opciones,
                'candidatos' => $candidatos
            )));
        } catch (PDOException $e) {

            print_r($e);

            $arrjson = Util::error_general('Al obtener estadísticas generales.');
        } finally {
            $db->closeConect();
        }
        return $arrjson;
    }

    /**
     * Obtiene estadísticas por ideología política
     */
    public static function getEstadisticasPorIdeologia($rqst)
    {
        $tbl_sondeo_id = isset($rqst['tbl_sondeo_id']) ? intval($rqst['tbl_sondeo_id']) : 0;

        if ($tbl_sondeo_id <= 0) {
            return Util::error_missing_data_description('El ID del sondeo es requerido.');
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $q = "SELECT
                v.ideologia,
                COUNT(*) as cantidad,
                ROUND((COUNT(*) * 100.0 / (SELECT COUNT(*)
                    FROM " . $db->getTable('tbl_respuestas_sondeos') . " rs2
                    INNER JOIN " . $db->getTable('tbl_votantes') . " v2 ON rs2.tbl_votante_id = v2.id
                    WHERE rs2.tbl_sondeo_id = :sondeo_id AND v2.ideologia IS NOT NULL)), 2) as porcentaje
                FROM " . $db->getTable('tbl_respuestas_sondeos') . " rs
                INNER JOIN " . $db->getTable('tbl_votantes') . " v ON rs.tbl_votante_id = v.id
                WHERE rs.tbl_sondeo_id = :sondeo_id AND v.ideologia IS NOT NULL
                GROUP BY v.ideologia
                ORDER BY cantidad DESC";

            $stmt = $pdo->prepare($q);
            $stmt->execute([':sondeo_id' => $tbl_sondeo_id]);
            $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $arrjson = array('output' => array('valid' => true, 'response' => $arr));
        } catch (PDOException $e) {
            $arrjson = Util::error_general('Al obtener estadísticas por ideología.');
        } finally {
            $db->closeConect();
        }
        return $arrjson;
    }

    /**
     * Obtiene estadísticas por género
     */
    public static function getEstadisticasPorGenero($rqst)
    {
        $tbl_sondeo_id = isset($rqst['tbl_sondeo_id']) ? intval($rqst['tbl_sondeo_id']) : 0;

        if ($tbl_sondeo_id <= 0) {
            return Util::error_missing_data_description('El ID del sondeo es requerido.');
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $q = "SELECT
                v.genero,
                COUNT(*) as cantidad,
                ROUND((COUNT(*) * 100.0 / (SELECT COUNT(*)
                    FROM " . $db->getTable('tbl_respuestas_sondeos') . " rs2
                    INNER JOIN " . $db->getTable('tbl_votantes') . " v2 ON rs2.tbl_votante_id = v2.id
                    WHERE rs2.tbl_sondeo_id = :sondeo_id AND v2.genero IS NOT NULL)), 2) as porcentaje
                FROM " . $db->getTable('tbl_respuestas_sondeos') . " rs
                INNER JOIN " . $db->getTable('tbl_votantes') . " v ON rs.tbl_votante_id = v.id
                WHERE rs.tbl_sondeo_id = :sondeo_id AND v.genero IS NOT NULL
                GROUP BY v.genero
                ORDER BY cantidad DESC";

            $stmt = $pdo->prepare($q);
            $stmt->execute([':sondeo_id' => $tbl_sondeo_id]);
            $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $arrjson = array('output' => array('valid' => true, 'response' => $arr));
        } catch (PDOException $e) {
            $arrjson = Util::error_general('Al obtener estadísticas por género.');
        } finally {
            $db->closeConect();
        }
        return $arrjson;
    }

    /**
     * Obtiene estadísticas por rango de edad
     */
    public static function getEstadisticasPorEdad($rqst)
    {
        $tbl_sondeo_id = isset($rqst['tbl_sondeo_id']) ? intval($rqst['tbl_sondeo_id']) : 0;

        if ($tbl_sondeo_id <= 0) {
            return Util::error_missing_data_description('El ID del sondeo es requerido.');
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $q = "SELECT
                v.rango_edad,
                COUNT(*) as cantidad,
                ROUND((COUNT(*) * 100.0 / (SELECT COUNT(*)
                    FROM " . $db->getTable('tbl_respuestas_sondeos') . " rs2
                    INNER JOIN " . $db->getTable('tbl_votantes') . " v2 ON rs2.tbl_votante_id = v2.id
                    WHERE rs2.tbl_sondeo_id = :sondeo_id AND v2.rango_edad IS NOT NULL)), 2) as porcentaje
                FROM " . $db->getTable('tbl_respuestas_sondeos') . " rs
                INNER JOIN " . $db->getTable('tbl_votantes') . " v ON rs.tbl_votante_id = v.id
                WHERE rs.tbl_sondeo_id = :sondeo_id AND v.rango_edad IS NOT NULL
                GROUP BY v.rango_edad
                ORDER BY cantidad DESC";

            $stmt = $pdo->prepare($q);
            $stmt->execute([':sondeo_id' => $tbl_sondeo_id]);
            $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $arrjson = array('output' => array('valid' => true, 'response' => $arr));
        } catch (PDOException $e) {
            $arrjson = Util::error_general('Al obtener estadísticas por edad.');
        } finally {
            $db->closeConect();
        }
        return $arrjson;
    }

    /**
     * Obtiene estadísticas por nivel de ingresos
     */
    public static function getEstadisticasPorIngresos($rqst)
    {
        $tbl_sondeo_id = isset($rqst['tbl_sondeo_id']) ? intval($rqst['tbl_sondeo_id']) : 0;

        if ($tbl_sondeo_id <= 0) {
            return Util::error_missing_data_description('El ID del sondeo es requerido.');
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $q = "SELECT
                v.nivel_ingresos,
                COUNT(*) as cantidad,
                ROUND((COUNT(*) * 100.0 / (SELECT COUNT(*)
                    FROM " . $db->getTable('tbl_respuestas_sondeos') . " rs2
                    INNER JOIN " . $db->getTable('tbl_votantes') . " v2 ON rs2.tbl_votante_id = v2.id
                    WHERE rs2.tbl_sondeo_id = :sondeo_id AND v2.nivel_ingresos IS NOT NULL)), 2) as porcentaje
                FROM " . $db->getTable('tbl_respuestas_sondeos') . " rs
                INNER JOIN " . $db->getTable('tbl_votantes') . " v ON rs.tbl_votante_id = v.id
                WHERE rs.tbl_sondeo_id = :sondeo_id AND v.nivel_ingresos IS NOT NULL
                GROUP BY v.nivel_ingresos
                ORDER BY cantidad DESC";

            $stmt = $pdo->prepare($q);
            $stmt->execute([':sondeo_id' => $tbl_sondeo_id]);
            $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $arrjson = array('output' => array('valid' => true, 'response' => $arr));
        } catch (PDOException $e) {
            $arrjson = Util::error_general('Al obtener estadísticas por ingresos.');
        } finally {
            $db->closeConect();
        }
        return $arrjson;
    }

    /**
     * Obtiene estadísticas por nivel de educación
     */
    public static function getEstadisticasPorEducacion($rqst)
    {
        $tbl_sondeo_id = isset($rqst['tbl_sondeo_id']) ? intval($rqst['tbl_sondeo_id']) : 0;

        if ($tbl_sondeo_id <= 0) {
            return Util::error_missing_data_description('El ID del sondeo es requerido.');
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $q = "SELECT
                v.nivel_educacion,
                COUNT(*) as cantidad,
                ROUND((COUNT(*) * 100.0 / (SELECT COUNT(*)
                    FROM " . $db->getTable('tbl_respuestas_sondeos') . " rs2
                    INNER JOIN " . $db->getTable('tbl_votantes') . " v2 ON rs2.tbl_votante_id = v2.id
                    WHERE rs2.tbl_sondeo_id = :sondeo_id AND v2.nivel_educacion IS NOT NULL)), 2) as porcentaje
                FROM " . $db->getTable('tbl_respuestas_sondeos') . " rs
                INNER JOIN " . $db->getTable('tbl_votantes') . " v ON rs.tbl_votante_id = v.id
                WHERE rs.tbl_sondeo_id = :sondeo_id AND v.nivel_educacion IS NOT NULL
                GROUP BY v.nivel_educacion
                ORDER BY cantidad DESC";

            $stmt = $pdo->prepare($q);
            $stmt->execute([':sondeo_id' => $tbl_sondeo_id]);
            $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $arrjson = array('output' => array('valid' => true, 'response' => $arr));
        } catch (PDOException $e) {
            $arrjson = Util::error_general('Al obtener estadísticas por educación.');
        } finally {
            $db->closeConect();
        }
        return $arrjson;
    }

    /**
     * Obtiene estadísticas por departamento
     */
    public static function getEstadisticasPorDepartamento($rqst)
    {
        $tbl_sondeo_id = isset($rqst['tbl_sondeo_id']) ? intval($rqst['tbl_sondeo_id']) : 0;

        if ($tbl_sondeo_id <= 0) {
            return Util::error_missing_data_description('El ID del sondeo es requerido.');
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $q = "SELECT
                d.departamento,
                v.codigo_departamento,
                COUNT(*) as cantidad,
                ROUND((COUNT(*) * 100.0 / (SELECT COUNT(*)
                    FROM " . $db->getTable('tbl_respuestas_sondeos') . " rs2
                    INNER JOIN " . $db->getTable('tbl_votantes') . " v2 ON rs2.tbl_votante_id = v2.id
                    WHERE rs2.tbl_sondeo_id = :sondeo_id AND v2.codigo_departamento IS NOT NULL)), 2) as porcentaje
                FROM " . $db->getTable('tbl_respuestas_sondeos') . " rs
                INNER JOIN " . $db->getTable('tbl_votantes') . " v ON rs.tbl_votante_id = v.id
                LEFT JOIN " . $db->getTable('tbl_departamentos') . " d ON v.codigo_departamento = d.codigo_departamento
                WHERE rs.tbl_sondeo_id = :sondeo_id AND v.codigo_departamento IS NOT NULL
                GROUP BY v.codigo_departamento, d.departamento
                ORDER BY cantidad DESC";

            $stmt = $pdo->prepare($q);
            $stmt->execute([':sondeo_id' => $tbl_sondeo_id]);
            $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $arrjson = array('output' => array('valid' => true, 'response' => $arr));
        } catch (PDOException $e) {
            $arrjson = Util::error_general('Al obtener estadísticas por departamento.');
        } finally {
            $db->closeConect();
        }
        return $arrjson;
    }

    /**
     * Obtiene estadísticas por municipio
     */
    public static function getEstadisticasPorMunicipio($rqst)
    {
        $tbl_sondeo_id = isset($rqst['tbl_sondeo_id']) ? intval($rqst['tbl_sondeo_id']) : 0;

        if ($tbl_sondeo_id <= 0) {
            return Util::error_missing_data_description('El ID del sondeo es requerido.');
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $q = "SELECT
                c.municipio,
                d.departamento,
                v.codigo_municipio,
                v.codigo_departamento,
                COUNT(*) as cantidad,
                ROUND((COUNT(*) * 100.0 / (SELECT COUNT(*)
                    FROM " . $db->getTable('tbl_respuestas_sondeos') . " rs2
                    INNER JOIN " . $db->getTable('tbl_votantes') . " v2 ON rs2.tbl_votante_id = v2.id
                    WHERE rs2.tbl_sondeo_id = :sondeo_id AND v2.codigo_municipio IS NOT NULL)), 2) as porcentaje
                FROM " . $db->getTable('tbl_respuestas_sondeos') . " rs
                INNER JOIN " . $db->getTable('tbl_votantes') . " v ON rs.tbl_votante_id = v.id
                LEFT JOIN " . $db->getTable('tbl_ciudades_accion_unificada') . " c ON v.codigo_municipio = c.codigo_muncipio
                LEFT JOIN " . $db->getTable('tbl_departamentos') . " d ON v.codigo_departamento = d.codigo_departamento
                WHERE rs.tbl_sondeo_id = :sondeo_id AND v.codigo_municipio IS NOT NULL
                GROUP BY v.codigo_municipio, c.municipio, v.codigo_departamento, d.departamento
                ORDER BY cantidad DESC";

            $stmt = $pdo->prepare($q);
            $stmt->execute([':sondeo_id' => $tbl_sondeo_id]);
            $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $arrjson = array('output' => array('valid' => true, 'response' => $arr));
        } catch (PDOException $e) {
            $arrjson = Util::error_general('Al obtener estadísticas por municipio.');
        } finally {
            $db->closeConect();
        }
        return $arrjson;
    }

    /**
     * Obtiene todas las estadísticas de un sondeo en una sola llamada
     */
    public static function getEstadisticasCompletas($rqst)
    {
        $ideologia = RespuestaSondeo::getEstadisticasPorIdeologia($rqst);
        $genero = RespuestaSondeo::getEstadisticasPorGenero($rqst);
        $edad = RespuestaSondeo::getEstadisticasPorEdad($rqst);
        $ingresos = RespuestaSondeo::getEstadisticasPorIngresos($rqst);
        $educacion = RespuestaSondeo::getEstadisticasPorEducacion($rqst);
        $departamento = RespuestaSondeo::getEstadisticasPorDepartamento($rqst);
        $municipio = RespuestaSondeo::getEstadisticasPorMunicipio($rqst);
        $generales = RespuestaSondeo::getEstadisticasGenerales($rqst);

        return array('output' => array('valid' => true, 'response' => array(
            'generales' => $generales['output']['response'],
            'ideologia' => $ideologia['output']['response'],
            'genero' => $genero['output']['response'],
            'edad' => $edad['output']['response'],
            'ingresos' => $ingresos['output']['response'],
            'educacion' => $educacion['output']['response'],
            'departamento' => $departamento['output']['response'],
            'municipio' => $municipio['output']['response']
        )));
    }
}
