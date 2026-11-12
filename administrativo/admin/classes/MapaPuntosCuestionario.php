<?php

require_once __DIR__ . '/ColorOpcion.php';

class MapaPuntosCuestionario
{
    private const TIPOS_CON_OPCIONES = ['radio', 'checkbox', 'Seleccion_Multiple_unica_respuesta', 'Seleccion_Multiple_multiple_respuesta', 'Dicotomica', 'Preguntas_Cardinales', 'Preguntas_Ordinales'];

    private static function municipio($valor): string
    {
        $d = preg_replace('/\D/', '', (string) $valor);
        return $d === '' ? '' : str_pad(substr($d, -5), 5, '0', STR_PAD_LEFT);
    }

    private static function filtros(array $rqst, DbConection $db, string &$joinUsuario, array &$params): string
    {
        $and = '';
        $desde = trim((string) ($rqst['fecha_desde'] ?? ''));
        $hasta = trim((string) ($rqst['fecha_hasta'] ?? ''));
        $tipo = trim((string) ($rqst['filtro_tipo'] ?? ''));
        $enc = trim((string) ($rqst['filtro_encuestador'] ?? ''));
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $desde)) {
            $and .= ' AND DATE(i.fecha_respuesta) >= :f_desde';
            $params[':f_desde'] = $desde;
        }
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $hasta)) {
            $and .= ' AND DATE(i.fecha_respuesta) <= :f_hasta';
            $params[':f_hasta'] = $hasta;
        }
        if ($tipo !== '' || $enc !== '') {
            $joinUsuario = ' LEFT JOIN ' . $db->getTable('tbl_usuarios') . ' u ON v.tbl_usuario_id = u.id ';
        }
        if ($tipo !== '') {
            $and .= " AND (CASE WHEN u.tipo = 'Encuestador' THEN 'Encuestador' WHEN v.tbl_usuario_id IS NULL OR v.tbl_usuario_id = 0 THEN 'Autoregistro' ELSE 'Registro interno' END) = :f_tipo";
            $params[':f_tipo'] = $tipo;
        }
        if ($enc !== '') {
            $nombre = "COALESCE(NULLIF(TRIM(CONCAT(COALESCE(u.nombre, ''), ' ', COALESCE(u.apellido, ''))), ''), NULLIF(TRIM(COALESCE(u.nickname, '')), ''), 'Sin asignar')";
            $and .= " AND (CASE WHEN u.tipo = 'Encuestador' THEN {$nombre} WHEN v.tbl_usuario_id IS NULL OR v.tbl_usuario_id = 0 THEN 'No aplica' ELSE {$nombre} END) = :f_enc";
            $params[':f_enc'] = $enc;
        }
        return $and;
    }

    public static function obtener(array $rqst): array
    {
        $fichaId = (int) ($rqst['ficha_tecnica_id'] ?? $rqst['id'] ?? 0);
        $preguntaId = (int) ($rqst['pregunta_id'] ?? 0);
        $municipio = self::municipio($rqst['municipio_click'] ?? $rqst['municipio'] ?? '');
        if ($fichaId <= 0 || $preguntaId <= 0 || $municipio === '') {
            return ['success' => false, 'message' => 'Faltan datos: encuesta, pregunta o municipio.'];
        }

        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $stmt = $pdo->prepare("SELECT id, texto_pregunta, tipo_pregunta FROM " . $db->getTable('tbl_preguntas') . " WHERE id = :p AND tbl_ficha_tecnica_encuesta_id = :f AND (eliminado = 0 OR eliminado IS NULL)");
            $stmt->execute([':p' => $preguntaId, ':f' => $fichaId]);
            $pregunta = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$pregunta) {
                return ['success' => false, 'message' => 'La pregunta no pertenece a la encuesta seleccionada.'];
            }
            if (!in_array($pregunta['tipo_pregunta'], self::TIPOS_CON_OPCIONES, true)) {
                return ['success' => false, 'sin_opciones' => true, 'message' => 'Esta pregunta no tiene opciones de respuesta para mostrar en el mapa.'];
            }

            $colores = ColorOpcion::mapaPregunta($pdo, $db, $preguntaId);
            $stmt = $pdo->prepare("SELECT id, texto_opcion FROM " . $db->getTable('tbl_opciones_respuesta') . " WHERE tbl_pregunta_id = :p AND (eliminado = 0 OR eliminado IS NULL) ORDER BY COALESCE(orden, 999999), id");
            $stmt->execute([':p' => $preguntaId]);
            $opciones = [];
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $o) {
                $opciones[(int) $o['id']] = [
                    'id' => (int) $o['id'],
                    'texto' => (string) $o['texto_opcion'],
                    'color' => $colores[(string) $o['id']] ?? ColorOpcion::SIN_DATO,
                    'total' => 0,
                ];
            }

            $joinUsuario = '';
            $params = [':p' => $preguntaId, ':mun' => $municipio];
            $and = self::filtros($rqst, $db, $joinUsuario, $params);

            $tCert = $db->getTable('tbl_certificacion_encuestador');
            $q = "SELECT i.id AS intento_id, r.tbl_opcion_respuesta_id AS opcion_id, i.fecha_respuesta,
                         c.latitud, c.longitud, c.precision_metros, i.zona_texto
                  FROM " . $db->getTable('tbl_cuestionario_respuestas') . " r
                  INNER JOIN " . $db->getTable('tbl_cuestionario_intentos') . " i ON r.tbl_intento_id = i.id AND (i.es_prueba = 0 OR i.es_prueba IS NULL)
                  INNER JOIN " . $db->getTable('tbl_votantes') . " v ON i.tbl_votante_id = v.id
                  {$joinUsuario}
                  INNER JOIN " . $db->getTable('tbl_opciones_respuesta') . " o ON r.tbl_opcion_respuesta_id = o.id AND (o.eliminado = 0 OR o.eliminado IS NULL)
                  LEFT JOIN {$tCert} c ON c.id = (
                      SELECT MIN(c2.id) FROM {$tCert} c2
                      WHERE c2.tbl_cuestionario_intento_id = i.id
                        AND c2.latitud IS NOT NULL AND c2.longitud IS NOT NULL
                        AND c2.latitud <> 0 AND c2.longitud <> 0
                  )
                  WHERE o.tbl_pregunta_id = :p
                    AND r.tbl_grilla_fila_id IS NULL AND r.tbl_grilla_columna_id IS NULL
                    AND LPAD(CAST(v.codigo_municipio AS UNSIGNED), 5, '0') = :mun
                    {$and}
                  ORDER BY i.fecha_respuesta, i.id, r.id";
            $stmt = $pdo->prepare($q);
            $stmt->execute($params);
            $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $porIntento = [];
            foreach ($filas as $f) {
                $oid = (int) $f['opcion_id'];
                $iid = (int) $f['intento_id'];
                if (!isset($porIntento[$iid])) {
                    $tiene = $f['latitud'] !== null && $f['longitud'] !== null
                        && (float) $f['latitud'] >= -4.5 && (float) $f['latitud'] <= 13.5
                        && (float) $f['longitud'] >= -79.5 && (float) $f['longitud'] <= -66.5;
                    $porIntento[$iid] = [
                        'lat' => $tiene ? round((float) $f['latitud'], 4) : null,
                        'lng' => $tiene ? round((float) $f['longitud'], 4) : null,
                        'precision' => $f['precision_metros'] !== null ? (int) round((float) $f['precision_metros']) : null,
                        'fecha' => $f['fecha_respuesta'] ? substr((string) $f['fecha_respuesta'], 0, 16) : null,
                        'zona' => trim((string) $f['zona_texto']) !== '' ? trim((string) $f['zona_texto']) : null,
                        'opciones' => [],
                    ];
                }
                if (!in_array($oid, $porIntento[$iid]['opciones'], true)) {
                    $porIntento[$iid]['opciones'][] = $oid;
                    if (isset($opciones[$oid])) {
                        $opciones[$oid]['total']++;
                    }
                }
            }

            $puntos = [];
            $sinUbicacion = 0;
            foreach ($porIntento as $p) {
                if ($p['lat'] === null) {
                    $sinUbicacion++;
                    continue;
                }
                $puntos[] = $p;
            }

            $stmt = $pdo->prepare("SELECT municipio FROM " . $db->getTable('tbl_ciudades_accion_unificada') . " WHERE LPAD(CAST(codigo_muncipio AS UNSIGNED), 5, '0') = :m LIMIT 1");
            $stmt->execute([':m' => $municipio]);
            $nombre = $stmt->fetchColumn();

            return [
                'success' => true,
                'municipio' => $municipio,
                'municipio_nombre' => $nombre ?: null,
                'pregunta' => ['id' => (int) $pregunta['id'], 'texto' => (string) $pregunta['texto_pregunta']],
                'opciones' => array_values($opciones),
                'puntos' => $puntos,
                'total_encuestas' => count($porIntento),
                'sin_ubicacion' => $sinUbicacion,
            ];
        } finally {
            $db->closeConect();
        }
    }
}
