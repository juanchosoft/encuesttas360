<?php

class ResultadosCuestionarioAgregado
{
    private static function params(array $rqst): array
    {
        $estadoIn = isset($rqst['estado']) && in_array($rqst['estado'], ['completo', 'terminado_filtro', 'pendiente_audio', 'todas'], true) ? $rqst['estado'] : 'completo';
        $origen = isset($rqst['origen']) && in_array($rqst['origen'], ['interno', 'publico', 'contestar'], true) ? $rqst['origen'] : '';
        return [
            'ficha' => (int)($rqst['ficha_id'] ?? $rqst['ficha_tecnica_id'] ?? 0),
            'estado' => $estadoIn,
            'origen' => $origen,
            'municipio' => trim((string)($rqst['municipio'] ?? '')),
            'zona' => (int)($rqst['zona_id'] ?? 0),
            'desde' => trim((string)($rqst['fecha_desde'] ?? '')),
            'hasta' => trim((string)($rqst['fecha_hasta'] ?? '')),
            'cruce_pregunta' => (int)($rqst['cruce_pregunta_id'] ?? 0),
            'cruce_opcion' => (int)($rqst['cruce_opcion_id'] ?? 0),
        ];
    }

    private static function poblacion(PDO $pdo, DbConection $db, array $p): array
    {
        $where = ["i.tbl_ficha_tecnica_encuesta_id = :f", "(i.es_prueba = 0 OR i.es_prueba IS NULL)"];
        $par = [':f' => $p['ficha']];
        if ($p['estado'] !== 'todas') {
            $where[] = 'i.estado = :e';
            $par[':e'] = $p['estado'];
        }
        if ($p['origen'] !== '') {
            $where[] = 'i.origen = :o';
            $par[':o'] = $p['origen'];
        }
        if ($p['municipio'] !== '') {
            $where[] = 'i.codigo_municipio = :m';
            $par[':m'] = $p['municipio'];
        }
        if ($p['zona'] > 0) {
            $where[] = 'i.tbl_zona_muestreo_id = :z';
            $par[':z'] = $p['zona'];
        }
        if ($p['desde'] !== '') {
            $where[] = 'DATE(i.fecha_respuesta) >= :d1';
            $par[':d1'] = $p['desde'];
        }
        if ($p['hasta'] !== '') {
            $where[] = 'DATE(i.fecha_respuesta) <= :d2';
            $par[':d2'] = $p['hasta'];
        }
        if ($p['cruce_pregunta'] > 0 && $p['cruce_opcion'] > 0) {
            $where[] = "EXISTS (SELECT 1 FROM " . $db->getTable('tbl_cuestionario_respuestas') . " rc WHERE rc.tbl_intento_id = i.id AND rc.tbl_pregunta_id = :cp AND rc.tbl_opcion_respuesta_id = :co)";
            $par[':cp'] = $p['cruce_pregunta'];
            $par[':co'] = $p['cruce_opcion'];
        }
        $st = $pdo->prepare("SELECT i.id FROM " . $db->getTable('tbl_cuestionario_intentos') . " i WHERE " . implode(' AND ', $where));
        $st->execute($par);
        return array_map('intval', $st->fetchAll(PDO::FETCH_COLUMN));
    }

    private static function inList(array $ids): string
    {
        return $ids ? implode(',', $ids) : '0';
    }

    private static function pct(int $n, int $total): float
    {
        return $total > 0 ? round($n * 100 / $total, 1) : 0.0;
    }

    private static function preguntasFicha(PDO $pdo, DbConection $db, int $ficha): array
    {
        $st = $pdo->prepare("SELECT id, numeral, capitulo, texto_pregunta, tipo_pregunta, es_filtro, rol_resultado, es_variable_cruce, etiqueta_cruce, tbl_grilla_plantilla_id
            FROM " . $db->getTable('tbl_preguntas') . " WHERE tbl_ficha_tecnica_encuesta_id = :f AND eliminado = 0 AND habilitado = 'si'
            ORDER BY COALESCE(orden, 999999), id");
        $st->execute([':f' => $ficha]);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    private static function etiqueta(array $p): string
    {
        return trim(($p['numeral'] !== null && $p['numeral'] !== '' ? $p['numeral'] . '. ' : '') . $p['texto_pregunta']);
    }

    public static function resumen($rqst)
    {
        $p = self::params($rqst);
        if ($p['ficha'] <= 0) {
            return Util::error_missing_data_description('Seleccione la encuesta.');
        }
        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $tI = $db->getTable('tbl_cuestionario_intentos');
            $base = "FROM {$tI} i WHERE i.tbl_ficha_tecnica_encuesta_id = :f AND (i.es_prueba = 0 OR i.es_prueba IS NULL)";
            $st = $pdo->prepare("SELECT i.estado, i.origen, COUNT(*) n {$base} GROUP BY i.estado, i.origen");
            $st->execute([':f' => $p['ficha']]);
            $porEstado = ['completo' => 0, 'terminado_filtro' => 0, 'pendiente_audio' => 0];
            $porOrigen = [];
            foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
                $porEstado[$r['estado']] = ($porEstado[$r['estado']] ?? 0) + (int)$r['n'];
                if ($r['estado'] === 'completo') {
                    $porOrigen[$r['origen']] = ($porOrigen[$r['origen']] ?? 0) + (int)$r['n'];
                }
            }

            $filtros = [];
            $todos = self::poblacion($pdo, $db, ['estado' => 'todas'] + $p);
            foreach (self::preguntasFicha($pdo, $db, $p['ficha']) as $q) {
                if ((int)$q['es_filtro'] !== 1) {
                    continue;
                }
                $stO = $pdo->prepare("SELECT o.id, o.texto_opcion, COUNT(DISTINCT r.tbl_intento_id) n
                    FROM " . $db->getTable('tbl_opciones_respuesta') . " o
                    LEFT JOIN " . $db->getTable('tbl_cuestionario_respuestas') . " r ON r.tbl_opcion_respuesta_id = o.id AND r.tbl_intento_id IN (" . self::inList($todos) . ")
                    WHERE o.tbl_pregunta_id = :p AND o.eliminado = 0 GROUP BY o.id, o.texto_opcion ORDER BY COALESCE(o.orden, 999999), o.id");
                $stO->execute([':p' => $q['id']]);
                $ops = [];
                $total = 0;
                $filas = $stO->fetchAll(PDO::FETCH_ASSOC);
                foreach ($filas as $f) { $total += (int)$f['n']; }
                foreach ($filas as $f) {
                    $ops[] = ['opcion' => $f['texto_opcion'], 'n' => (int)$f['n'], 'pct' => self::pct((int)$f['n'], $total)];
                }
                $filtros[] = ['pregunta' => self::etiqueta($q), 'total' => $total, 'opciones' => $ops];
            }

            $stZ = $pdo->prepare("SELECT z.id, z.codigo_municipio, c.municipio, z.nombre, z.cuota,
                    (SELECT COUNT(*) FROM {$tI} x WHERE x.tbl_zona_muestreo_id = z.id AND x.estado = 'completo' AND (x.es_prueba = 0 OR x.es_prueba IS NULL)) realizadas
                FROM " . $db->getTable('tbl_zonas_muestreo') . " z
                LEFT JOIN " . $db->getTable('tbl_ciudades_accion_unificada') . " c ON CONVERT(c.codigo_muncipio USING utf8mb4) COLLATE utf8mb4_unicode_ci = z.codigo_municipio
                WHERE z.tbl_ficha_tecnica_encuesta_id = :f AND z.eliminado = 0 ORDER BY c.municipio, z.orden, z.id");
            $stZ->execute([':f' => $p['ficha']]);
            $municipios = [];
            foreach ($stZ->fetchAll(PDO::FETCH_ASSOC) as $z) {
                $k = $z['codigo_municipio'];
                if (!isset($municipios[$k])) {
                    $municipios[$k] = ['codigo_municipio' => $k, 'municipio' => $z['municipio'] ?: $k, 'meta' => 0, 'realizadas' => 0, 'zonas' => []];
                }
                $municipios[$k]['meta'] += (int)$z['cuota'];
                $municipios[$k]['realizadas'] += (int)$z['realizadas'];
                $municipios[$k]['zonas'][] = ['id' => (int)$z['id'], 'nombre' => $z['nombre'], 'meta' => (int)$z['cuota'], 'realizadas' => (int)$z['realizadas'],
                    'avance' => self::pct((int)$z['realizadas'], (int)$z['cuota'])];
            }
            foreach ($municipios as &$m) {
                $m['avance'] = self::pct($m['realizadas'], $m['meta']);
            }
            unset($m);

            $db->closeConect();
            return ['output' => ['valid' => true, 'response' => [
                'por_estado' => $porEstado, 'completas_por_origen' => $porOrigen, 'filtros' => $filtros,
                'avance_municipios' => array_values($municipios),
                'meta_total' => array_sum(array_column($municipios, 'meta')),
            ]]];
        } catch (Throwable $e) {
            $db->closeConect();
            error_log('ResultadosCuestionarioAgregado::resumen error: ' . $e->getMessage());
            return Util::error_general('No se pudo calcular el resumen.');
        }
    }

    public static function preguntas($rqst)
    {
        $p = self::params($rqst);
        if ($p['ficha'] <= 0) {
            return Util::error_missing_data_description('Seleccione la encuesta.');
        }
        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $ids = self::poblacion($pdo, $db, $p);
            $in = self::inList($ids);
            $salida = [];
            foreach (self::preguntasFicha($pdo, $db, $p['ficha']) as $q) {
                if (in_array($q['tipo_pregunta'], ['Grilla', 'Lista_desde_grilla'], true)) {
                    continue;
                }
                $stO = $pdo->prepare("SELECT o.id, o.texto_opcion, o.valor_numerico, o.cuenta_como, COUNT(DISTINCT r.tbl_intento_id) n
                    FROM " . $db->getTable('tbl_opciones_respuesta') . " o
                    LEFT JOIN " . $db->getTable('tbl_cuestionario_respuestas') . " r ON r.tbl_opcion_respuesta_id = o.id AND r.tbl_pregunta_id = o.tbl_pregunta_id AND r.tbl_intento_id IN ({$in})
                    WHERE o.tbl_pregunta_id = :p AND o.eliminado = 0 GROUP BY o.id, o.texto_opcion, o.valor_numerico, o.cuenta_como ORDER BY COALESCE(o.orden, 999999), o.id");
                $stO->execute([':p' => $q['id']]);
                $filas = $stO->fetchAll(PDO::FETCH_ASSOC);
                if (!$filas) {
                    continue;
                }
                $stN = $pdo->prepare("SELECT COUNT(DISTINCT tbl_intento_id) FROM " . $db->getTable('tbl_cuestionario_respuestas') . " WHERE tbl_pregunta_id = :p AND tbl_intento_id IN ({$in})");
                $stN->execute([':p' => $q['id']]);
                $respondieron = (int)$stN->fetchColumn();
                $ops = [];
                $suma = 0.0;
                $nVal = 0;
                foreach ($filas as $f) {
                    $n = (int)$f['n'];
                    $ops[] = ['id' => (int)$f['id'], 'opcion' => $f['texto_opcion'], 'n' => $n, 'pct' => self::pct($n, $respondieron), 'cuenta_como' => $f['cuenta_como']];
                    if ($f['valor_numerico'] !== null) {
                        $suma += $n * (float)$f['valor_numerico'];
                        $nVal += $n;
                    }
                }
                $salida[] = [
                    'pregunta_id' => (int)$q['id'], 'numeral' => $q['numeral'], 'capitulo' => $q['capitulo'], 'pregunta' => self::etiqueta($q),
                    'rol_resultado' => $q['rol_resultado'], 'respondieron' => $respondieron, 'opciones' => $ops,
                    'nota' => $nVal > 0 ? round($suma / $nVal, 2) : null,
                    'variable_cruce' => (int)$q['es_variable_cruce'] === 1, 'etiqueta_cruce' => $q['etiqueta_cruce'],
                ];
            }
            $db->closeConect();
            return ['output' => ['valid' => true, 'response' => ['poblacion' => count($ids), 'preguntas' => $salida]]];
        } catch (Throwable $e) {
            $db->closeConect();
            error_log('ResultadosCuestionarioAgregado::preguntas error: ' . $e->getMessage());
            return Util::error_general('No se pudieron calcular los resultados por pregunta.');
        }
    }

    public static function grillas($rqst)
    {
        $p = self::params($rqst);
        if ($p['ficha'] <= 0) {
            return Util::error_missing_data_description('Seleccione la encuesta.');
        }
        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $ids = self::poblacion($pdo, $db, $p);
            $in = self::inList($ids);
            $tR = $db->getTable('tbl_cuestionario_respuestas');

            $st = $pdo->query("SELECT r.tbl_pregunta_id, r.tbl_grilla_fila_id, r.tbl_grilla_columna_id, r.tbl_grilla_columna_opcion_id, COUNT(*) n
                FROM {$tR} r WHERE r.tbl_intento_id IN ({$in}) AND r.tbl_grilla_fila_id IS NOT NULL
                GROUP BY r.tbl_pregunta_id, r.tbl_grilla_fila_id, r.tbl_grilla_columna_id, r.tbl_grilla_columna_opcion_id");
            $cel = [];
            foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
                $cel[(int)$r['tbl_pregunta_id']][(int)$r['tbl_grilla_fila_id']][(int)$r['tbl_grilla_columna_id']][(int)$r['tbl_grilla_columna_opcion_id']] = (int)$r['n'];
            }
            $st = $pdo->query("SELECT r.tbl_pregunta_id, r.tbl_grilla_fila_id, COUNT(DISTINCT r.tbl_intento_id) n
                FROM {$tR} r WHERE r.tbl_intento_id IN ({$in}) AND r.tbl_grilla_fila_id IS NOT NULL GROUP BY r.tbl_pregunta_id, r.tbl_grilla_fila_id");
            $respFila = [];
            foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
                $respFila[(int)$r['tbl_pregunta_id']][(int)$r['tbl_grilla_fila_id']] = (int)$r['n'];
            }

            $tFilas = $db->getTable('tbl_grilla_filas');
            $tCols = $db->getTable('tbl_grilla_columnas');
            $tOps = $db->getTable('tbl_grilla_columna_opciones');
            $stFilasPl = $pdo->prepare("SELECT id, etiqueta FROM {$tFilas} WHERE tbl_grilla_plantilla_id = :p AND eliminado = 0 ORDER BY orden, id");
            $stColsPl = $pdo->prepare("SELECT id, titulo, rol_resultado, tipo_respuesta FROM {$tCols} WHERE tbl_grilla_plantilla_id = :p AND eliminado = 0 ORDER BY orden, id");
            $stOpsCol = $pdo->prepare("SELECT id, texto, valor_numerico, cuenta_como FROM {$tOps} WHERE tbl_grilla_columna_id = :c AND eliminado = 0 ORDER BY orden, id");
            $stPlNombre = $pdo->prepare("SELECT nombre FROM " . $db->getTable('tbl_grilla_plantillas') . " WHERE id = :p");
            $stPlDe = $pdo->prepare("SELECT tbl_grilla_plantilla_id FROM {$tFilas} WHERE id = :f");

            $salida = [];
            foreach (self::preguntasFicha($pdo, $db, $p['ficha']) as $q) {
                if ($q['tipo_pregunta'] !== 'Grilla') {
                    continue;
                }
                $pid = (int)$q['id'];
                $plantillasUsadas = [];
                foreach (array_keys($cel[$pid] ?? []) as $fid) {
                    $stPlDe->execute([':f' => $fid]);
                    $plantillasUsadas[(int)$stPlDe->fetchColumn()] = true;
                }
                $tablas = [];
                foreach (array_keys($plantillasUsadas) as $plId) {
                    $stPlNombre->execute([':p' => $plId]);
                    $stColsPl->execute([':p' => $plId]);
                    $cols = $stColsPl->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($cols as &$c) {
                        $stOpsCol->execute([':c' => $c['id']]);
                        $c['opciones'] = $stOpsCol->fetchAll(PDO::FETCH_ASSOC);
                    }
                    unset($c);
                    $stFilasPl->execute([':p' => $plId]);
                    $filasOut = [];
                    foreach ($stFilasPl->fetchAll(PDO::FETCH_ASSOC) as $fl) {
                        $fid = (int)$fl['id'];
                        $n = $respFila[$pid][$fid] ?? 0;
                        $colsOut = [];
                        $ind = ['conocimiento' => null, 'favorabilidad' => null, 'intencion' => null, 'nota' => null];
                        foreach ($cols as $c) {
                            $cid = (int)$c['id'];
                            $opsOut = [];
                            $totalCol = 0;
                            foreach ($c['opciones'] as $o) {
                                $totalCol += $cel[$pid][$fid][$cid][(int)$o['id']] ?? 0;
                            }
                            $fav = 0; $des = 0; $si = 0; $sumaVal = 0.0; $nVal = 0;
                            foreach ($c['opciones'] as $o) {
                                $k = $cel[$pid][$fid][$cid][(int)$o['id']] ?? 0;
                                $opsOut[] = ['opcion' => $o['texto'], 'n' => $k, 'pct' => self::pct($k, $totalCol)];
                                if ($o['cuenta_como'] === 'favorable') { $fav += $k; }
                                if ($o['cuenta_como'] === 'desfavorable') { $des += $k; }
                                if ($o['cuenta_como'] === 'si') { $si += $k; }
                                if ($o['valor_numerico'] !== null) { $sumaVal += $k * (float)$o['valor_numerico']; $nVal += $k; }
                            }
                            $colsOut[] = ['columna' => $c['titulo'], 'rol' => $c['rol_resultado'], 'respondieron' => $totalCol, 'opciones' => $opsOut];
                            if ($c['rol_resultado'] === 'conocimiento') { $ind['conocimiento'] = self::pct($si, $totalCol); }
                            if ($c['rol_resultado'] === 'imagen') { $ind['favorabilidad'] = self::pct($fav, $fav + $des); }
                            if ($c['rol_resultado'] === 'intencion') { $ind['intencion'] = self::pct($si, $totalCol); }
                            if ($nVal > 0 && ($c['rol_resultado'] === 'escala' || $c['rol_resultado'] === 'ninguno') && $ind['nota'] === null) { $ind['nota'] = round($sumaVal / $nVal, 2); }
                        }
                        $filasOut[] = ['fila' => $fl['etiqueta'], 'respondieron' => $n, 'indicadores' => $ind, 'columnas' => $colsOut];
                    }
                    $tablas[] = ['plantilla' => (string)$stPlNombre->fetchColumn(), 'filas' => $filasOut];
                }
                $salida[] = ['pregunta_id' => $pid, 'numeral' => $q['numeral'], 'capitulo' => $q['capitulo'], 'pregunta' => self::etiqueta($q), 'tablas' => $tablas];
            }
            $db->closeConect();
            return ['output' => ['valid' => true, 'response' => ['poblacion' => count($ids), 'grillas' => $salida]]];
        } catch (Throwable $e) {
            $db->closeConect();
            error_log('ResultadosCuestionarioAgregado::grillas error: ' . $e->getMessage());
            return Util::error_general('No se pudieron calcular los resultados de las tablas.');
        }
    }

    public static function listas($rqst)
    {
        $p = self::params($rqst);
        if ($p['ficha'] <= 0) {
            return Util::error_missing_data_description('Seleccione la encuesta.');
        }
        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $ids = self::poblacion($pdo, $db, $p);
            $in = self::inList($ids);
            $tR = $db->getTable('tbl_cuestionario_respuestas');
            $salida = [];
            foreach (self::preguntasFicha($pdo, $db, $p['ficha']) as $q) {
                if ($q['tipo_pregunta'] !== 'Lista_desde_grilla') {
                    continue;
                }
                $pid = (int)$q['id'];
                $st = $pdo->prepare("SELECT COALESCE(gf.etiqueta, o.texto_opcion) etiqueta, (r.tbl_grilla_fila_id IS NULL) fija, COUNT(DISTINCT r.tbl_intento_id) n
                    FROM {$tR} r
                    LEFT JOIN " . $db->getTable('tbl_grilla_filas') . " gf ON gf.id = r.tbl_grilla_fila_id
                    LEFT JOIN " . $db->getTable('tbl_opciones_respuesta') . " o ON o.id = r.tbl_opcion_respuesta_id
                    WHERE r.tbl_pregunta_id = :p AND r.tbl_intento_id IN ({$in})
                    GROUP BY etiqueta, fija ORDER BY n DESC, etiqueta");
                $st->execute([':p' => $pid]);
                $filas = $st->fetchAll(PDO::FETCH_ASSOC);
                $total = 0;
                foreach ($filas as $f) { $total += (int)$f['n']; }
                $items = [];
                foreach ($filas as $f) {
                    $items[] = ['opcion' => $f['etiqueta'], 'opcion_fija' => (int)$f['fija'] === 1, 'n' => (int)$f['n'], 'pct' => self::pct((int)$f['n'], $total)];
                }
                $salida[] = ['pregunta_id' => $pid, 'numeral' => $q['numeral'], 'pregunta' => self::etiqueta($q), 'respondieron' => $total, 'items' => $items];
            }
            $db->closeConect();
            return ['output' => ['valid' => true, 'response' => ['poblacion' => count($ids), 'listas' => $salida ?? []]]];
        } catch (Throwable $e) {
            $db->closeConect();
            error_log('ResultadosCuestionarioAgregado::listas error: ' . $e->getMessage());
            return Util::error_general('No se pudieron calcular los resultados de voto.');
        }
    }

    public static function opcionesCruce($rqst)
    {
        $p = self::params($rqst);
        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $salida = [];
            foreach (self::preguntasFicha($pdo, $db, $p['ficha']) as $q) {
                if ((int)$q['es_variable_cruce'] !== 1) {
                    continue;
                }
                $st = $pdo->prepare("SELECT id, texto_opcion FROM " . $db->getTable('tbl_opciones_respuesta') . " WHERE tbl_pregunta_id = :p AND eliminado = 0 ORDER BY COALESCE(orden, 999999), id");
                $st->execute([':p' => $q['id']]);
                $salida[] = ['pregunta_id' => (int)$q['id'], 'nombre' => $q['etiqueta_cruce'] ?: self::etiqueta($q), 'opciones' => $st->fetchAll(PDO::FETCH_ASSOC)];
            }
            $stM = $pdo->prepare("SELECT DISTINCT i.codigo_municipio, c.municipio FROM " . $db->getTable('tbl_cuestionario_intentos') . " i
                LEFT JOIN " . $db->getTable('tbl_ciudades_accion_unificada') . " c ON CONVERT(c.codigo_muncipio USING utf8mb4) COLLATE utf8mb4_unicode_ci = i.codigo_municipio
                WHERE i.tbl_ficha_tecnica_encuesta_id = :f AND i.codigo_municipio IS NOT NULL ORDER BY c.municipio");
            $stM->execute([':f' => $p['ficha']]);
            $db->closeConect();
            return ['output' => ['valid' => true, 'response' => ['variables' => $salida, 'municipios' => $stM->fetchAll(PDO::FETCH_ASSOC)]]];
        } catch (Throwable $e) {
            $db->closeConect();
            error_log('ResultadosCuestionarioAgregado::opcionesCruce error: ' . $e->getMessage());
            return Util::error_general('No se pudieron cargar los filtros.');
        }
    }

    public static function cruce($rqst)
    {
        $p = self::params($rqst);
        $x = (int)($rqst['pregunta_x_id'] ?? 0);
        $y = (int)($rqst['pregunta_y_id'] ?? 0);
        if ($p['ficha'] <= 0 || $x <= 0 || $y <= 0) {
            return Util::error_missing_data_description('Escoja las dos preguntas a cruzar.');
        }
        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $ids = self::poblacion($pdo, $db, $p);
            $in = self::inList($ids);
            $tR = $db->getTable('tbl_cuestionario_respuestas');
            $tO = $db->getTable('tbl_opciones_respuesta');
            $st = $pdo->query("SELECT rx.tbl_opcion_respuesta_id ox, ry.tbl_opcion_respuesta_id oy, COUNT(DISTINCT rx.tbl_intento_id) n
                FROM {$tR} rx INNER JOIN {$tR} ry ON ry.tbl_intento_id = rx.tbl_intento_id AND ry.tbl_pregunta_id = {$y} AND ry.tbl_opcion_respuesta_id IS NOT NULL
                WHERE rx.tbl_pregunta_id = {$x} AND rx.tbl_opcion_respuesta_id IS NOT NULL AND rx.tbl_intento_id IN ({$in})
                GROUP BY rx.tbl_opcion_respuesta_id, ry.tbl_opcion_respuesta_id");
            $conteo = [];
            foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
                $conteo[(int)$r['ox']][(int)$r['oy']] = (int)$r['n'];
            }
            $opciones = function (int $pid) use ($pdo, $tO) {
                $s = $pdo->prepare("SELECT id, texto_opcion FROM {$tO} WHERE tbl_pregunta_id = :p AND eliminado = 0 ORDER BY COALESCE(orden, 999999), id");
                $s->execute([':p' => $pid]);
                return $s->fetchAll(PDO::FETCH_ASSOC);
            };
            $ox = $opciones($x);
            $oy = $opciones($y);
            $totalCol = [];
            foreach ($oy as $b) {
                $t = 0;
                foreach ($ox as $a) { $t += $conteo[(int)$a['id']][(int)$b['id']] ?? 0; }
                $totalCol[(int)$b['id']] = $t;
            }
            $filas = [];
            foreach ($ox as $a) {
                $celdas = [];
                foreach ($oy as $b) {
                    $n = $conteo[(int)$a['id']][(int)$b['id']] ?? 0;
                    $celdas[] = ['n' => $n, 'pct_columna' => self::pct($n, $totalCol[(int)$b['id']])];
                }
                $filas[] = ['opcion' => $a['texto_opcion'], 'celdas' => $celdas];
            }
            $db->closeConect();
            return ['output' => ['valid' => true, 'response' => [
                'columnas' => array_map(function ($b) use ($totalCol) { return ['opcion' => $b['texto_opcion'], 'total' => $totalCol[(int)$b['id']]]; }, $oy),
                'filas' => $filas,
            ]]];
        } catch (Throwable $e) {
            $db->closeConect();
            error_log('ResultadosCuestionarioAgregado::cruce error: ' . $e->getMessage());
            return Util::error_general('No se pudo calcular el cruce.');
        }
    }

    public static function exportar($rqst)
    {
        $p = self::params($rqst);
        if ($p['ficha'] <= 0) {
            return Util::error_missing_data_description('Seleccione la encuesta.');
        }
        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $ids = self::poblacion($pdo, $db, $p);
            $in = self::inList($ids);
            $preguntas = self::preguntasFicha($pdo, $db, $p['ficha']);

            $columnas = [];
            $mapaCol = [];
            $stFilas = $pdo->prepare("SELECT f.id, f.etiqueta FROM " . $db->getTable('tbl_grilla_filas') . " f WHERE f.tbl_grilla_plantilla_id = :p AND f.eliminado = 0 ORDER BY f.orden, f.id");
            $stCols = $pdo->prepare("SELECT c.id, c.titulo FROM " . $db->getTable('tbl_grilla_columnas') . " c WHERE c.tbl_grilla_plantilla_id = :p AND c.eliminado = 0 ORDER BY c.orden, c.id");
            $stVar = $pdo->prepare("SELECT tbl_grilla_plantilla_id FROM " . $db->getTable('tbl_preguntas_grilla_variantes') . " WHERE tbl_pregunta_id = :p ORDER BY codigo_municipio");
            foreach ($preguntas as $q) {
                $pid = (int)$q['id'];
                $et = self::etiqueta($q);
                if ($q['tipo_pregunta'] === 'Grilla') {
                    $pls = [];
                    if ($q['tbl_grilla_plantilla_id']) { $pls[] = (int)$q['tbl_grilla_plantilla_id']; }
                    $stVar->execute([':p' => $pid]);
                    foreach ($stVar->fetchAll(PDO::FETCH_COLUMN) as $v) { $pls[] = (int)$v; }
                    foreach (array_unique($pls) as $plId) {
                        $stFilas->execute([':p' => $plId]);
                        $filas = $stFilas->fetchAll(PDO::FETCH_ASSOC);
                        $stCols->execute([':p' => $plId]);
                        $cols = $stCols->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($filas as $f) {
                            foreach ($cols as $c) {
                                $k = $pid . '|' . $f['id'] . '|' . $c['id'];
                                $columnas[$k] = $et . ' · ' . $f['etiqueta'] . ' · ' . $c['titulo'];
                                $mapaCol[$k] = true;
                            }
                        }
                    }
                } else {
                    $columnas[$pid . '|0|0'] = $et;
                }
            }

            $tR = $db->getTable('tbl_cuestionario_respuestas');
            $st = $pdo->query("SELECT r.tbl_intento_id, r.tbl_pregunta_id, r.tbl_grilla_fila_id, r.tbl_grilla_columna_id,
                    COALESCE(gco.texto, o.texto_opcion, gf.etiqueta, r.respuesta_texto) valor
                FROM {$tR} r
                LEFT JOIN " . $db->getTable('tbl_grilla_columna_opciones') . " gco ON gco.id = r.tbl_grilla_columna_opcion_id
                LEFT JOIN " . $db->getTable('tbl_opciones_respuesta') . " o ON o.id = r.tbl_opcion_respuesta_id
                LEFT JOIN " . $db->getTable('tbl_grilla_filas') . " gf ON gf.id = r.tbl_grilla_fila_id AND r.tbl_grilla_columna_id IS NULL
                WHERE r.tbl_intento_id IN ({$in}) ORDER BY r.id");
            $valores = [];
            foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
                $k = (int)$r['tbl_pregunta_id'] . '|' . (int)$r['tbl_grilla_fila_id'] . '|' . (int)$r['tbl_grilla_columna_id'];
                $kNormal = (int)$r['tbl_pregunta_id'] . '|0|0';
                $kk = isset($columnas[$k]) ? $k : $kNormal;
                if ($r['valor'] !== null && $r['valor'] !== '') {
                    $valores[(int)$r['tbl_intento_id']][$kk][] = $r['valor'];
                }
            }

            $stI = $pdo->query("SELECT i.id, i.fecha_respuesta, i.estado, i.origen, i.codigo_municipio, c.municipio, i.zona_texto, i.valido_para_pago
                FROM " . $db->getTable('tbl_cuestionario_intentos') . " i
                LEFT JOIN " . $db->getTable('tbl_ciudades_accion_unificada') . " c ON CONVERT(c.codigo_muncipio USING utf8mb4) COLLATE utf8mb4_unicode_ci = i.codigo_municipio
                WHERE i.id IN ({$in}) ORDER BY i.id");
            $intentos = $stI->fetchAll(PDO::FETCH_ASSOC);
            $db->closeConect();

            $estados = ['completo' => 'Completa', 'terminado_filtro' => 'Terminada por filtro', 'pendiente_audio' => 'Falta audio'];
            $encabezado = array_merge(['N°', 'Fecha', 'Estado', 'Origen', 'Municipio', 'Zona'], array_values($columnas));
            $fh = fopen('php://temp', 'r+');
            fprintf($fh, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($fh, $encabezado, ';');
            foreach ($intentos as $n => $i) {
                $fila = [$n + 1, $i['fecha_respuesta'], $estados[$i['estado']] ?? $i['estado'], $i['origen'], $i['municipio'] ?: $i['codigo_municipio'], $i['zona_texto']];
                foreach (array_keys($columnas) as $k) {
                    $fila[] = isset($valores[(int)$i['id']][$k]) ? implode(' | ', $valores[(int)$i['id']][$k]) : '';
                }
                fputcsv($fh, $fila, ';');
            }
            rewind($fh);
            $csv = stream_get_contents($fh);
            fclose($fh);
            return ['output' => ['valid' => true, 'response' => ['archivo' => 'resultados_encuesta_' . $p['ficha'] . '_' . date('Ymd_His') . '.csv', 'contenido_base64' => base64_encode($csv), 'filas' => count($intentos)]]];
        } catch (Throwable $e) {
            $db->closeConect();
            error_log('ResultadosCuestionarioAgregado::exportar error: ' . $e->getMessage());
            return Util::error_general('No se pudo preparar el archivo.');
        }
    }
}
