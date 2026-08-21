<?php
class RespuestaSondeo
{
    public function __construct() {}

    /**
     * Filtro geo desde departamento_click / municipio_click (códigos DANE).
     * @return array{dep:string,muni:string}
     */
    private static function parseGeo($rqst)
    {
        $dep = '';
        $muni = '';
        if (!empty($rqst['municipio_click'])) {
            $muni = method_exists('Util', 'normalizeCodigoMunicipio')
                ? Util::normalizeCodigoMunicipio($rqst['municipio_click'])
                : str_pad((string)intval($rqst['municipio_click']), 5, '0', STR_PAD_LEFT);
        }
        if (!empty($rqst['departamento_click'])) {
            $dep = method_exists('Util', 'normalizeCodigoDepartamento')
                ? Util::normalizeCodigoDepartamento($rqst['departamento_click'])
                : str_pad((string)intval($rqst['departamento_click']), 2, '0', STR_PAD_LEFT);
        }
        return ['dep' => $dep, 'muni' => $muni];
    }

    /** Filtro sobre columnas de tbl_respuestas_sondeos */
    private static function geoSqlRespuesta($alias, $rqst, &$params)
    {
        $g = self::parseGeo($rqst);
        if ($g['muni'] !== '') {
            $params[':geo_muni'] = $g['muni'];
            return " AND LPAD(CAST({$alias}.codigo_municipio AS UNSIGNED), 5, '0') = :geo_muni ";
        }
        if ($g['dep'] !== '') {
            $params[':geo_dep'] = $g['dep'];
            return " AND LPAD(CAST({$alias}.codigo_departamento AS UNSIGNED), 2, '0') = :geo_dep ";
        }
        return '';
    }

    /** Filtro sobre tbl_votantes */
    private static function geoSqlVotante($alias, $rqst, &$params)
    {
        $g = self::parseGeo($rqst);
        if ($g['muni'] !== '') {
            $params[':geo_muni'] = $g['muni'];
            return " AND LPAD(CAST({$alias}.codigo_municipio AS UNSIGNED), 5, '0') = :geo_muni ";
        }
        if ($g['dep'] !== '') {
            $params[':geo_dep'] = $g['dep'];
            return " AND LPAD(CAST({$alias}.codigo_departamento AS UNSIGNED), 2, '0') = :geo_dep ";
        }
        return '';
    }

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

    public static function getEstadisticasGenerales($rqst)
    {
        $tbl_sondeo_id = isset($rqst['tbl_sondeo_id']) ? intval($rqst['tbl_sondeo_id']) : 0;

        if ($tbl_sondeo_id <= 0) {
            return Util::error_missing_data_description('El ID del sondeo es requerido.');
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $params = [':sondeo_id' => $tbl_sondeo_id];
            $geoRs = self::geoSqlRespuesta('rs', $rqst, $params);

            $qGeneral = "SELECT
                COUNT(DISTINCT rs.id) as total_respuestas,
                COUNT(DISTINCT rs.tbl_votante_id) as votantes_unicos,
                COUNT(DISTINCT DATE(rs.dtcreate)) as dias_activo
                FROM " . $db->getTable('tbl_respuestas_sondeos') . " rs
                WHERE rs.tbl_sondeo_id = :sondeo_id" . $geoRs;

            $stmt = $pdo->prepare($qGeneral);
            $stmt->execute($params);
            $general = $stmt->fetch(PDO::FETCH_ASSOC);

            $paramsJoin = [':sondeo_id' => $tbl_sondeo_id];
            $geoJoin = self::geoSqlRespuesta('rs', $rqst, $paramsJoin);
            $geoRs3 = self::geoSqlRespuesta('rs3', $rqst, $paramsJoin);

            $qOpciones = "SELECT
                so.opcion as respuesta_opcion,
                COUNT(rs.id) as cantidad,
                ROUND((COUNT(rs.id) * 100.0 / NULLIF((SELECT COUNT(*) FROM " . $db->getTable('tbl_respuestas_sondeos') . " rs3
                    WHERE rs3.tbl_sondeo_id = :sondeo_id AND rs3.tbl_sondeo_x_opciones_id IS NOT NULL{$geoRs3}), 0)), 2) as porcentaje
                FROM " . $db->getTable('tbl_sondeo_x_opciones') . " so
                LEFT JOIN " . $db->getTable('tbl_respuestas_sondeos') . " rs
                    ON so.id = rs.tbl_sondeo_x_opciones_id AND rs.tbl_sondeo_id = :sondeo_id" . $geoJoin . "
                WHERE so.tbl_sondeo_id = :sondeo_id
                GROUP BY so.id, so.opcion
                ORDER BY cantidad DESC";

            $stmt = $pdo->prepare($qOpciones);
            $stmt->execute($paramsJoin);
            $opciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $paramsCand = [':sondeo_id' => $tbl_sondeo_id];
            $geoCand = self::geoSqlRespuesta('rs', $rqst, $paramsCand);
            $geoCand3 = self::geoSqlRespuesta('rs3', $rqst, $paramsCand);

            $qCandidatos = "SELECT
                p.id, p.nombre_completo, p.foto,
                cp.nombre as cargo_publico,
                pp.nombre_partido,
                COUNT(rs.id) as votos,
                ROUND((COUNT(rs.id) * 100.0 / NULLIF((SELECT COUNT(*) FROM " . $db->getTable('tbl_respuestas_sondeos') . " rs3
                    WHERE rs3.tbl_sondeo_id = :sondeo_id AND rs3.tbl_candidato_id IS NOT NULL{$geoCand3}), 0)), 2) as porcentaje
                FROM " . $db->getTable('tbl_participantes') . " p
                INNER JOIN " . $db->getTable('tbl_sondeo_x_tbl_participantes') . " sxp ON p.id = sxp.tbl_participante_id
                LEFT JOIN " . $db->getTable('tbl_respuestas_sondeos') . " rs
                    ON p.id = rs.tbl_candidato_id AND rs.tbl_sondeo_id = :sondeo_id" . $geoCand . "
                LEFT JOIN " . $db->getTable('tbl_cargos_publicos') . " cp ON p.tbl_cargo_publico_id = cp.id
                LEFT JOIN " . $db->getTable('tbl_participantes_x_partidos_politicos') . " pxp ON p.id = pxp.tbl_participante_id
                LEFT JOIN " . $db->getTable('tbl_partidos_politicos') . " pp ON pxp.tbl_partido_politico_id = pp.id
                WHERE sxp.tbl_sondeo_id = :sondeo_id
                GROUP BY p.id
                ORDER BY votos DESC";

            $stmt = $pdo->prepare($qCandidatos);
            $stmt->execute($paramsCand);
            $candidatos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $arrjson = array('output' => array('valid' => true, 'response' => array(
                'general' => $general,
                'opciones' => $opciones,
                'candidatos' => $candidatos
            )));
        } catch (PDOException $e) {
            $arrjson = Util::error_general('Al obtener estadísticas generales.');
        } finally {
            $db->closeConect();
        }
        return $arrjson;
    }

    private static function getEstadisticasPorCampo($rqst, $campo, $labelError)
    {
        $tbl_sondeo_id = isset($rqst['tbl_sondeo_id']) ? intval($rqst['tbl_sondeo_id']) : 0;
        if ($tbl_sondeo_id <= 0) {
            return Util::error_missing_data_description('El ID del sondeo es requerido.');
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $params = [':sondeo_id' => $tbl_sondeo_id];
            $geoV = self::geoSqlVotante('v', $rqst, $params);
            $geoV2 = self::geoSqlVotante('v2', $rqst, $params);

            $q = "SELECT
                v.{$campo},
                COUNT(*) as cantidad,
                ROUND((COUNT(*) * 100.0 / NULLIF((SELECT COUNT(*)
                    FROM " . $db->getTable('tbl_respuestas_sondeos') . " rs2
                    INNER JOIN " . $db->getTable('tbl_votantes') . " v2 ON rs2.tbl_votante_id = v2.id
                    WHERE rs2.tbl_sondeo_id = :sondeo_id AND v2.{$campo} IS NOT NULL{$geoV2}), 0)), 2) as porcentaje
                FROM " . $db->getTable('tbl_respuestas_sondeos') . " rs
                INNER JOIN " . $db->getTable('tbl_votantes') . " v ON rs.tbl_votante_id = v.id
                WHERE rs.tbl_sondeo_id = :sondeo_id AND v.{$campo} IS NOT NULL{$geoV}
                GROUP BY v.{$campo}
                ORDER BY cantidad DESC";

            $stmt = $pdo->prepare($q);
            $stmt->execute($params);
            $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $arrjson = array('output' => array('valid' => true, 'response' => $arr));
        } catch (PDOException $e) {
            $arrjson = Util::error_general($labelError);
        } finally {
            $db->closeConect();
        }
        return $arrjson;
    }

    public static function getEstadisticasPorIdeologia($rqst)
    {
        return self::getEstadisticasPorCampo($rqst, 'ideologia', 'Al obtener estadísticas por ideología.');
    }

    public static function getEstadisticasPorGenero($rqst)
    {
        return self::getEstadisticasPorCampo($rqst, 'genero', 'Al obtener estadísticas por género.');
    }

    public static function getEstadisticasPorEdad($rqst)
    {
        return self::getEstadisticasPorCampo($rqst, 'rango_edad', 'Al obtener estadísticas por edad.');
    }

    public static function getEstadisticasPorIngresos($rqst)
    {
        return self::getEstadisticasPorCampo($rqst, 'nivel_ingresos', 'Al obtener estadísticas por ingresos.');
    }

    public static function getEstadisticasPorEducacion($rqst)
    {
        return self::getEstadisticasPorCampo($rqst, 'nivel_educacion', 'Al obtener estadísticas por educación.');
    }

    public static function getEstadisticasPorDepartamento($rqst)
    {
        $tbl_sondeo_id = isset($rqst['tbl_sondeo_id']) ? intval($rqst['tbl_sondeo_id']) : 0;
        if ($tbl_sondeo_id <= 0) {
            return Util::error_missing_data_description('El ID del sondeo es requerido.');
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $params = [':sondeo_id' => $tbl_sondeo_id];
            $geoV = self::geoSqlVotante('v', $rqst, $params);
            $geoV2 = self::geoSqlVotante('v2', $rqst, $params);

            $q = "SELECT
                d.departamento,
                v.codigo_departamento,
                COUNT(*) as cantidad,
                ROUND((COUNT(*) * 100.0 / NULLIF((SELECT COUNT(*)
                    FROM " . $db->getTable('tbl_respuestas_sondeos') . " rs2
                    INNER JOIN " . $db->getTable('tbl_votantes') . " v2 ON rs2.tbl_votante_id = v2.id
                    WHERE rs2.tbl_sondeo_id = :sondeo_id AND v2.codigo_departamento IS NOT NULL{$geoV2}), 0)), 2) as porcentaje
                FROM " . $db->getTable('tbl_respuestas_sondeos') . " rs
                INNER JOIN " . $db->getTable('tbl_votantes') . " v ON rs.tbl_votante_id = v.id
                LEFT JOIN " . $db->getTable('tbl_departamentos') . " d ON v.codigo_departamento = d.codigo_departamento
                WHERE rs.tbl_sondeo_id = :sondeo_id AND v.codigo_departamento IS NOT NULL{$geoV}
                GROUP BY v.codigo_departamento, d.departamento
                ORDER BY cantidad DESC";

            $stmt = $pdo->prepare($q);
            $stmt->execute($params);
            $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $arrjson = array('output' => array('valid' => true, 'response' => $arr));
        } catch (PDOException $e) {
            $arrjson = Util::error_general('Al obtener estadísticas por departamento.');
        } finally {
            $db->closeConect();
        }
        return $arrjson;
    }

    public static function getEstadisticasPorMunicipio($rqst)
    {
        $tbl_sondeo_id = isset($rqst['tbl_sondeo_id']) ? intval($rqst['tbl_sondeo_id']) : 0;
        if ($tbl_sondeo_id <= 0) {
            return Util::error_missing_data_description('El ID del sondeo es requerido.');
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $params = [':sondeo_id' => $tbl_sondeo_id];
            $geoV = self::geoSqlVotante('v', $rqst, $params);
            $geoV2 = self::geoSqlVotante('v2', $rqst, $params);

            $q = "SELECT
                c.municipio,
                d.departamento,
                v.codigo_municipio,
                v.codigo_departamento,
                COUNT(*) as cantidad,
                ROUND((COUNT(*) * 100.0 / NULLIF((SELECT COUNT(*)
                    FROM " . $db->getTable('tbl_respuestas_sondeos') . " rs2
                    INNER JOIN " . $db->getTable('tbl_votantes') . " v2 ON rs2.tbl_votante_id = v2.id
                    WHERE rs2.tbl_sondeo_id = :sondeo_id AND v2.codigo_municipio IS NOT NULL{$geoV2}), 0)), 2) as porcentaje
                FROM " . $db->getTable('tbl_respuestas_sondeos') . " rs
                INNER JOIN " . $db->getTable('tbl_votantes') . " v ON rs.tbl_votante_id = v.id
                LEFT JOIN " . $db->getTable('tbl_ciudades_accion_unificada') . " c ON v.codigo_municipio = c.codigo_muncipio
                LEFT JOIN " . $db->getTable('tbl_departamentos') . " d ON v.codigo_departamento = d.codigo_departamento
                WHERE rs.tbl_sondeo_id = :sondeo_id AND v.codigo_municipio IS NOT NULL{$geoV}
                GROUP BY v.codigo_municipio, c.municipio, v.codigo_departamento, d.departamento
                ORDER BY cantidad DESC";

            $stmt = $pdo->prepare($q);
            $stmt->execute($params);
            $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $arrjson = array('output' => array('valid' => true, 'response' => $arr));
        } catch (PDOException $e) {
            $arrjson = Util::error_general('Al obtener estadísticas por municipio.');
        } finally {
            $db->closeConect();
        }
        return $arrjson;
    }

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
