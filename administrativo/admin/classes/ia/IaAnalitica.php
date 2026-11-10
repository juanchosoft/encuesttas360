<?php

require_once __DIR__ . '/../DbConection.php';

class IaAnalitica
{
    public static function preguntasFicha(int $fichaId, ?string $municipio = null): array
    {
        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $stmt = $pdo->prepare("SELECT p.id, p.texto_pregunta, p.tipo_pregunta, p.capitulo FROM " . $db->getTable('tbl_preguntas') . " p
                                    INNER JOIN " . $db->getTable('tbl_ficha_tecnica_encuestas') . " f ON f.id = p.tbl_ficha_tecnica_encuesta_id
                                    WHERE p.tbl_ficha_tecnica_encuesta_id = :id AND p.habilitado = 'si' AND (p.eliminado = 0 OR p.eliminado IS NULL)
                                      AND (f.eliminado = 'no' OR f.eliminado IS NULL)
                                    ORDER BY COALESCE(p.orden, 999999), p.id");
            $stmt->execute([':id' => $fichaId]);
            $preguntas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!$preguntas) {
                return [];
            }
            $stmt = $pdo->prepare("SELECT o.id, o.tbl_pregunta_id, o.texto_opcion FROM " . $db->getTable('tbl_opciones_respuesta') . " o
                                    INNER JOIN " . $db->getTable('tbl_preguntas') . " p ON p.id = o.tbl_pregunta_id
                                    WHERE p.tbl_ficha_tecnica_encuesta_id = :id AND (o.eliminado = 0 OR o.eliminado IS NULL)
                                    ORDER BY o.tbl_pregunta_id, COALESCE(o.orden, 999999), o.id");
            $stmt->execute([':id' => $fichaId]);
            $opciones = [];
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $o) {
                $opciones[$o['tbl_pregunta_id']][] = $o;
            }
            $params = [':id' => $fichaId];
            $filtroMun = '';
            if ($municipio !== null && $municipio !== '') {
                $filtroMun = ' AND i.codigo_municipio = :mun';
                $params[':mun'] = $municipio;
            }
            $stmt = $pdo->prepare("SELECT r.tbl_pregunta_id, r.tbl_opcion_respuesta_id, COUNT(*) AS n FROM " . $db->getTable('tbl_cuestionario_respuestas') . " r
                                    INNER JOIN " . $db->getTable('tbl_cuestionario_intentos') . " i ON i.id = r.tbl_intento_id
                                    INNER JOIN " . $db->getTable('tbl_preguntas') . " p ON p.id = r.tbl_pregunta_id
                                    WHERE p.tbl_ficha_tecnica_encuesta_id = :id AND i.estado = 'completo' AND (i.es_prueba = 0 OR i.es_prueba IS NULL)
                                      AND r.tbl_grilla_fila_id IS NULL AND r.tbl_grilla_columna_id IS NULL AND r.tbl_opcion_respuesta_id IS NOT NULL" . $filtroMun . "
                                    GROUP BY r.tbl_pregunta_id, r.tbl_opcion_respuesta_id");
            $stmt->execute($params);
            $conteos = [];
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $c) {
                $conteos[$c['tbl_pregunta_id']][$c['tbl_opcion_respuesta_id']] = (int) $c['n'];
            }
            $salida = [];
            foreach ($preguntas as $p) {
                $ops = [];
                $total = 0;
                foreach ($opciones[$p['id']] ?? [] as $o) {
                    $n = $conteos[$p['id']][$o['id']] ?? 0;
                    $total += $n;
                    $ops[] = ['id' => (int) $o['id'], 'texto' => (string) $o['texto_opcion'], 'n' => $n];
                }
                $salida[] = ['id' => (int) $p['id'], 'texto' => (string) $p['texto_pregunta'], 'tipo' => (string) $p['tipo_pregunta'], 'capitulo' => $p['capitulo'], 'respuestas' => $total, 'opciones' => $ops];
            }
            return $salida;
        } finally {
            $db->closeConect();
        }
    }

    public static function intentosCompletos(int $fichaId, ?string $municipio): int
    {
        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $q = "SELECT COUNT(*) FROM " . $db->getTable('tbl_cuestionario_intentos') . " WHERE tbl_ficha_tecnica_encuesta_id = :f AND estado = 'completo' AND (es_prueba = 0 OR es_prueba IS NULL)";
            $params = [':f' => $fichaId];
            if ($municipio !== null && $municipio !== '') {
                $q .= ' AND codigo_municipio = :m';
                $params[':m'] = $municipio;
            }
            $stmt = $pdo->prepare($q);
            $stmt->execute($params);
            return (int) $stmt->fetchColumn();
        } finally {
            $db->closeConect();
        }
    }

    private static function norm(string $t): string
    {
        $t = mb_strtolower(trim($t));
        return strtr($t, ['á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u', 'ñ' => 'n']);
    }

    public static function rankingImagen(int $fichaId, ?string $municipio, array $soloIds = []): array
    {
        $filas = [];
        foreach (self::preguntasFicha($fichaId, $municipio) as $p) {
            if ($soloIds && !in_array($p['id'], $soloIds, true)) {
                continue;
            }
            $pos = $neu = $neg = $no = null;
            foreach ($p['opciones'] as $o) {
                $t = self::norm($o['texto']);
                if (strpos($t, 'positiv') !== false) {
                    $pos = $o['n'];
                } elseif (strpos($t, 'neutr') !== false) {
                    $neu = $o['n'];
                } elseif (strpos($t, 'negativ') !== false) {
                    $neg = $o['n'];
                } elseif (preg_match('/\bno (lo |la )?(reconoce|conoce)/', $t)) {
                    $no = $o['n'];
                }
            }
            if ($pos === null || $neu === null || $neg === null) {
                continue;
            }
            $base = $pos + $neu + $neg + (int) $no;
            if ($base <= 0) {
                continue;
            }
            $rec = ($pos + $neu + $neg) / $base * 100;
            $nombre = trim(preg_replace('/^.*?(qu[eé] imagen tiene de|cu[aá]l es su imagen de)\s*/iu', '', $p['texto']), ' ¿?');
            if (preg_match('/^(él|ella|el|la)$/iu', $nombre) && preg_match('/(\p{Lu}\p{Ll}+(?:\s+\p{Lu}\p{Ll}+)+)/u', $p['texto'], $mn)) {
                $nombre = $mn[1];
            }
            $filas[] = [
                'pregunta_id' => $p['id'],
                'personaje' => $nombre !== '' ? $nombre : $p['texto'],
                'base' => $base,
                'reconocimiento_pct' => round($rec, 1),
                'positiva_pct' => round($pos / $base * 100, 1),
                'neutral_pct' => round($neu / $base * 100, 1),
                'negativa_pct' => round($neg / $base * 100, 1),
                'no_reconoce_pct' => round((int) $no / $base * 100, 1),
                'balance_pp' => round(($pos - $neg) / $base * 100, 1),
                'balance_entre_quienes_lo_reconocen_pp' => ($pos + $neu + $neg) > 0 ? round(($pos - $neg) / ($pos + $neu + $neg) * 100, 1) : 0.0,
            ];
        }
        usort($filas, static fn ($a, $b) => [$b['balance_pp'], $b['reconocimiento_pct']] <=> [$a['balance_pp'], $a['reconocimiento_pct']]);
        foreach ($filas as $i => &$f) {
            $f['ranking_balance'] = $i + 1;
        }
        unset($f);
        $porRec = $filas;
        usort($porRec, static fn ($a, $b) => $b['reconocimiento_pct'] <=> $a['reconocimiento_pct']);
        $posRec = [];
        foreach ($porRec as $i => $f) {
            $posRec[$f['pregunta_id']] = $i + 1;
        }
        foreach ($filas as &$f) {
            $f['ranking_reconocimiento'] = $posRec[$f['pregunta_id']];
        }
        unset($f);
        return $filas;
    }

    public static function cruce(int $fichaId, int $preguntaFila, int $preguntaColumna, ?string $municipio): array
    {
        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $params = [':f' => $fichaId, ':a' => $preguntaFila, ':b' => $preguntaColumna];
            $filtroMun = '';
            if ($municipio !== null && $municipio !== '') {
                $filtroMun = ' AND i.codigo_municipio = :mun';
                $params[':mun'] = $municipio;
            }
            $stmt = $pdo->prepare("SELECT ra.tbl_opcion_respuesta_id AS oa, rb.tbl_opcion_respuesta_id AS ob, COUNT(*) AS n
                                    FROM " . $db->getTable('tbl_cuestionario_intentos') . " i
                                    INNER JOIN " . $db->getTable('tbl_cuestionario_respuestas') . " ra ON ra.tbl_intento_id = i.id AND ra.tbl_pregunta_id = :a AND ra.tbl_opcion_respuesta_id IS NOT NULL
                                    INNER JOIN " . $db->getTable('tbl_cuestionario_respuestas') . " rb ON rb.tbl_intento_id = i.id AND rb.tbl_pregunta_id = :b AND rb.tbl_opcion_respuesta_id IS NOT NULL
                                    WHERE i.tbl_ficha_tecnica_encuesta_id = :f AND i.estado = 'completo' AND (i.es_prueba = 0 OR i.es_prueba IS NULL)" . $filtroMun . "
                                    GROUP BY ra.tbl_opcion_respuesta_id, rb.tbl_opcion_respuesta_id");
            $stmt->execute($params);
            $celdas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } finally {
            $db->closeConect();
        }
        $pregs = [];
        foreach (self::preguntasFicha($fichaId, $municipio) as $p) {
            $pregs[$p['id']] = $p;
        }
        if (!isset($pregs[$preguntaFila], $pregs[$preguntaColumna])) {
            return ['error' => 'no_encontrado', 'mensaje' => 'Alguna de las preguntas no pertenece a esa encuesta.'];
        }
        $filasOp = $pregs[$preguntaFila]['opciones'];
        $colsOp = $pregs[$preguntaColumna]['opciones'];
        $m = [];
        foreach ($celdas as $c) {
            $m[(int) $c['oa']][(int) $c['ob']] = (int) $c['n'];
        }
        $totalFila = [];
        $totalCol = [];
        $total = 0;
        foreach ($filasOp as $fo) {
            foreach ($colsOp as $co) {
                $n = $m[$fo['id']][$co['id']] ?? 0;
                $totalFila[$fo['id']] = ($totalFila[$fo['id']] ?? 0) + $n;
                $totalCol[$co['id']] = ($totalCol[$co['id']] ?? 0) + $n;
                $total += $n;
            }
        }
        if ($total === 0) {
            return ['error' => 'sin_datos', 'mensaje' => 'Ninguna persona respondió ambas preguntas.'];
        }
        $filas = [];
        $chi2 = 0.0;
        foreach ($filasOp as $fo) {
            if (($totalFila[$fo['id']] ?? 0) === 0) {
                continue;
            }
            $celdasFila = [];
            foreach ($colsOp as $co) {
                if (($totalCol[$co['id']] ?? 0) === 0) {
                    continue;
                }
                $n = $m[$fo['id']][$co['id']] ?? 0;
                $esperado = $totalFila[$fo['id']] * $totalCol[$co['id']] / $total;
                $chi2 += $esperado > 0 ? (($n - $esperado) ** 2) / $esperado : 0;
                $celdasFila[$co['texto']] = ['n' => $n, 'pct_de_la_fila' => round($n / $totalFila[$fo['id']] * 100, 1), 'pct_de_la_columna' => round($n / $totalCol[$co['id']] * 100, 1)];
            }
            $filas[] = ['opcion' => $fo['texto'], 'total' => $totalFila[$fo['id']], 'columnas' => $celdasFila];
        }
        $gl = (count($filas)) > 0 ? (count($filas) - 1) * (count(array_filter($totalCol)) - 1) : 0;
        $p = $gl > 0 ? self::pChiCuadrado($chi2, $gl) : null;
        return [
            'pregunta_filas' => $pregs[$preguntaFila]['texto'],
            'pregunta_columnas' => $pregs[$preguntaColumna]['texto'],
            'base_total' => $total,
            'filas' => $filas,
            'chi_cuadrado' => round($chi2, 2),
            'grados_libertad' => $gl,
            'valor_p' => $p !== null ? round($p, 4) : null,
            'asociacion_significativa_95' => $p !== null && $p < 0.05,
            'aviso' => 'Una asociación estadística no prueba causalidad. Con celdas de pocos casos (menos de 5 esperados) la prueba pierde fiabilidad.',
        ];
    }

    public static function pChiCuadrado(float $x, int $gl): float
    {
        if ($x <= 0) {
            return 1.0;
        }
        return 1.0 - self::gammaIncompletaRegularizada($gl / 2, $x / 2);
    }

    private static function gammaIncompletaRegularizada(float $a, float $x): float
    {
        $gln = self::logGamma($a);
        if ($x < $a + 1) {
            $ap = $a;
            $suma = $del = 1.0 / $a;
            for ($n = 0; $n < 300; $n++) {
                $ap++;
                $del *= $x / $ap;
                $suma += $del;
                if (abs($del) < abs($suma) * 1e-12) {
                    break;
                }
            }
            return $suma * exp(-$x + $a * log($x) - $gln);
        }
        $b = $x + 1 - $a;
        $c = 1.0 / 1e-300;
        $d = 1.0 / $b;
        $h = $d;
        for ($i = 1; $i < 300; $i++) {
            $an = -$i * ($i - $a);
            $b += 2;
            $d = $an * $d + $b;
            $d = abs($d) < 1e-300 ? 1e-300 : $d;
            $c = $b + $an / $c;
            $c = abs($c) < 1e-300 ? 1e-300 : $c;
            $d = 1.0 / $d;
            $del = $d * $c;
            $h *= $del;
            if (abs($del - 1.0) < 1e-12) {
                break;
            }
        }
        return 1.0 - exp(-$x + $a * log($x) - $gln) * $h;
    }

    private static function logGamma(float $x): float
    {
        $coef = [76.18009172947146, -86.50532032941677, 24.01409824083091, -1.231739572450155, 0.1208650973866179e-2, -0.5395239384953e-5];
        $y = $x;
        $tmp = $x + 5.5;
        $tmp -= ($x + 0.5) * log($tmp);
        $ser = 1.000000000190015;
        foreach ($coef as $c) {
            $ser += $c / ++$y;
        }
        return -$tmp + log(2.5066282746310005 * $ser / $x);
    }

    public static function estadistico(array $in): array
    {
        $tipo = (string) ($in['tipo'] ?? '');
        $confianza = (float) ($in['confianza'] ?? 95);
        $z = ['90' => 1.645, '95' => 1.96, '99' => 2.576][(string) (int) $confianza] ?? 1.96;
        if ($tipo === 'margen_error') {
            $n = (float) ($in['n'] ?? 0);
            $p = isset($in['proporcion_pct']) ? (float) $in['proporcion_pct'] / 100 : 0.5;
            if ($n <= 0) {
                return ['error' => 'parametro_faltante', 'mensaje' => 'n (tamaño de muestra) es obligatorio.'];
            }
            $N = (float) ($in['poblacion'] ?? 0);
            $fpc = $N > $n ? sqrt(($N - $n) / ($N - 1)) : 1.0;
            return ['margen_error_pp' => round($z * sqrt($p * (1 - $p) / $n) * $fpc * 100, 2), 'z' => $z, 'n' => $n, 'proporcion_usada_pct' => $p * 100, 'correccion_poblacion_finita' => $fpc < 1.0, 'valido_solo_para_muestra_probabilistica' => true];
        }
        if ($tipo === 'diferencia_proporciones') {
            $n1 = (float) ($in['n1'] ?? 0);
            $n2 = (float) ($in['n2'] ?? 0);
            $p1 = (float) ($in['p1_pct'] ?? 0) / 100;
            $p2 = (float) ($in['p2_pct'] ?? 0) / 100;
            if ($n1 <= 0 || $n2 <= 0) {
                return ['error' => 'parametro_faltante', 'mensaje' => 'n1, n2, p1_pct y p2_pct son obligatorios.'];
            }
            $pc = ($p1 * $n1 + $p2 * $n2) / ($n1 + $n2);
            $se = sqrt($pc * (1 - $pc) * (1 / $n1 + 1 / $n2));
            $zc = $se > 0 ? ($p1 - $p2) / $se : 0;
            $pv = 2 * (1 - self::normalCdf(abs($zc)));
            $seD = sqrt($p1 * (1 - $p1) / $n1 + $p2 * (1 - $p2) / $n2);
            return ['diferencia_pp' => round(($p1 - $p2) * 100, 2), 'z' => round($zc, 3), 'valor_p' => round($pv, 4), 'significativa' => $pv < (1 - $confianza / 100), 'intervalo_diferencia_pp' => [round(($p1 - $p2 - $z * $seD) * 100, 2), round(($p1 - $p2 + $z * $seD) * 100, 2)], 'confianza' => $confianza];
        }
        if ($tipo === 'tamano_muestra') {
            $e = (float) ($in['margen_pp'] ?? 0) / 100;
            if ($e <= 0) {
                return ['error' => 'parametro_faltante', 'mensaje' => 'margen_pp es obligatorio.'];
            }
            $p = isset($in['proporcion_pct']) ? (float) $in['proporcion_pct'] / 100 : 0.5;
            $n0 = $z * $z * $p * (1 - $p) / ($e * $e);
            $N = (float) ($in['poblacion'] ?? 0);
            $n = $N > 0 ? $n0 / (1 + ($n0 - 1) / $N) : $n0;
            return ['tamano_muestra' => (int) ceil($n), 'sin_correccion' => (int) ceil($n0), 'confianza' => $confianza];
        }
        return ['error' => 'parametro_invalido', 'mensaje' => 'tipo debe ser margen_error, diferencia_proporciones o tamano_muestra.'];
    }

    private static function normalCdf(float $x): float
    {
        return 0.5 * (1 + self::erf($x / M_SQRT2));
    }

    private static function erf(float $x): float
    {
        $t = 1 / (1 + 0.3275911 * abs($x));
        $y = 1 - ((((1.061405429 * $t - 1.453152027) * $t + 1.421413741) * $t - 0.284496736) * $t + 0.254829592) * $t * exp(-$x * $x);
        return $x >= 0 ? $y : -$y;
    }

    public static function compararEstudios(int $fichaA, int $fichaB, string $textoPregunta): array
    {
        $buscar = static function (array $preguntas, string $txt): ?array {
            $mejor = null;
            $mejorSim = 0.0;
            foreach ($preguntas as $p) {
                similar_text(self::norm($p['texto']), self::norm($txt), $pct);
                if ($pct > $mejorSim) {
                    $mejorSim = $pct;
                    $mejor = $p;
                }
            }
            return $mejorSim >= 55 ? $mejor : null;
        };
        $pa = $buscar(self::preguntasFicha($fichaA), $textoPregunta);
        $pb = $buscar(self::preguntasFicha($fichaB), $textoPregunta);
        if (!$pa || !$pb) {
            return ['error' => 'no_encontrado', 'mensaje' => 'No se encontró una pregunta equivalente en ambos estudios.'];
        }
        $dist = static function (array $p): array {
            $tot = array_sum(array_column($p['opciones'], 'n'));
            $r = [];
            foreach ($p['opciones'] as $o) {
                $r[self::norm($o['texto'])] = ['texto' => $o['texto'], 'n' => $o['n'], 'pct' => $tot > 0 ? round($o['n'] / $tot * 100, 1) : 0.0];
            }
            return [$r, $tot];
        };
        [$da, $na] = $dist($pa);
        [$db, $nb] = $dist($pb);
        $filas = [];
        foreach ($da as $k => $v) {
            if (isset($db[$k])) {
                $est = self::estadistico(['tipo' => 'diferencia_proporciones', 'n1' => $na, 'n2' => $nb, 'p1_pct' => $v['pct'], 'p2_pct' => $db[$k]['pct']]);
                $filas[] = ['opcion' => $v['texto'], 'estudio_a_pct' => $v['pct'], 'estudio_b_pct' => $db[$k]['pct'], 'diferencia_pp' => round($v['pct'] - $db[$k]['pct'], 1), 'diferencia_significativa_95' => $est['significativa'] ?? null];
            }
        }
        return [
            'pregunta_estudio_a' => $pa['texto'], 'pregunta_estudio_b' => $pb['texto'],
            'base_a' => $na, 'base_b' => $nb, 'opciones_comparables' => count($filas), 'opciones_solo_en_un_estudio' => abs(count($da) - count($db)),
            'comparacion' => $filas,
            'aviso' => 'Dos mediciones no son una tendencia: compara solo si población, método, fechas y redacción son comparables (revisa la ficha técnica de cada estudio).',
        ];
    }
}
