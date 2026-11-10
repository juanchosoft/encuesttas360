<?php

require_once __DIR__ . '/IaRecursos.php';
require_once __DIR__ . '/InformeIA.php';

class InformeSpec
{
    private const SERIE_BASE = ['#2A9E8F', '#E9A23B', '#C8567B', '#8D99AE', '#7C5CFA', '#8AA33B'];
    private const MAX_DATOS = 20;
    private const ANALISIS_DEFECTO = 900;
    private const ANALISIS_TOPE = 1700;

    public static function tema(?array $paleta, ?array $logoRecurso): array
    {
        $base = ['primario' => '#2B2F3A', 'secundario' => '#6B7280', 'acento' => '#4052BF', 'suave' => '#E9ECF7', 'texto' => '#2E3440'];
        if ($logoRecurso) {
            try {
                $base = array_merge($base, IaRecursos::paleta($logoRecurso));
            } catch (Throwable $e) {
            }
        }
        foreach (['primario', 'secundario', 'acento', 'suave', 'texto'] as $k) {
            if (isset($paleta[$k]) && preg_match('/^#[0-9a-f]{6}$/i', $paleta[$k])) {
                $base[$k] = strtoupper($paleta[$k]);
            }
        }
        $base['serie'] = array_merge([$base['acento'], $base['primario']], self::SERIE_BASE);
        return $base;
    }

    public static function compilar(array $informe, array $opciones, int $userId, bool $esAdmin): array
    {
        $marca = InformeIA::marca();
        $logo = !empty($opciones['logo_id']) ? IaRecursos::cargar((int) $opciones['logo_id'], $userId, $esAdmin) : null;
        $portada = !empty($opciones['portada_id']) ? IaRecursos::cargar((int) $opciones['portada_id'], $userId, $esAdmin) : null;
        $cierre = !empty($opciones['cierre_id']) ? IaRecursos::cargar((int) $opciones['cierre_id'], $userId, $esAdmin) : $portada;
        $tema = self::tema($opciones['paleta'] ?? null, $logo);

        $subtitulos = array_values(array_filter(array_map('trim', explode('|', (string) ($informe['subtitulo'] ?? ''))), 'strlen'));
        $fechaTexto = trim((string) ($opciones['fecha_texto'] ?? ''));
        if ($fechaTexto === '') {
            $tieneAnio = false;
            foreach ($subtitulos as $s) {
                if (preg_match('/\b20\d{2}\b/', $s)) {
                    $tieneAnio = true;
                }
            }
            $fechaTexto = $tieneAnio ? '' : ucfirst(InformeIA::mesAnio($informe['dt_create']));
        }

        $spec = [
            'titulo' => $informe['titulo'],
            'subtitulos' => $subtitulos,
            'fecha_texto' => $fechaTexto,
            'realizado_por' => trim((string) ($opciones['realizado_por'] ?? '')) !== '' ? trim((string) $opciones['realizado_por']) : $marca['nombre'],
            'logo' => $logo,
            'portada' => $portada,
            'cierre' => $cierre,
            'brillo' => max(10, min(90, (int) ($opciones['brillo'] ?? 35))),
            'tema' => $tema,
            'paginas' => [],
        ];

        $paginas = [];
        $fuente = (string) ($informe['fuente_dsl'] ?? '');
        $trozos = preg_split('#(<pagina\b[^>]*>.*?</pagina>)#isu', $fuente, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY) ?: [];
        foreach ($trozos as $trozo) {
            if (preg_match('#^<pagina\b([^>]*)>(.*)</pagina>$#isu', $trozo, $m)) {
                $p = self::paginaResultado($m[1], $m[2]);
                if ($p) {
                    $paginas[] = $p;
                }
            } else {
                foreach (self::paginasDeBloques(self::bloquesDeHtml($trozo)) as $p) {
                    $paginas[] = $p;
                }
            }
        }

        if (!empty($informe['ficha_tecnica_id'])) {
            $filas = InformeIA::filasFichaTecnica((int) $informe['ficha_tecnica_id']);
            if ($filas) {
                array_unshift($paginas, ['tipo' => 'ficha', 'titulo' => 'FICHA TÉCNICA', 'filas' => $filas]);
            }
        }
        $spec['paginas'] = $paginas;
        return $spec;
    }

    private static function atributos(string $txt): array
    {
        $a = [];
        if (preg_match_all('/([a-zA-Z0-9_-]+)\s*=\s*"([^"]*)"/u', $txt, $m, PREG_SET_ORDER)) {
            foreach ($m as $par) {
                $a[strtolower($par[1])] = html_entity_decode($par[2], ENT_QUOTES, 'UTF-8');
            }
        }
        return $a;
    }

    private static function limpiar(string $t): string
    {
        return trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($t), ENT_QUOTES, 'UTF-8')));
    }

    private static function numero(string $txt): ?float
    {
        $txt = trim(str_replace(['%', ' '], '', $txt));
        if ($txt === '') {
            return null;
        }
        if (substr_count($txt, ',') === 1 && strpos($txt, '.') === false) {
            $txt = str_replace(',', '.', $txt);
        }
        return is_numeric($txt) ? max(0.0, (float) $txt) : null;
    }

    private static function paginaResultado(string $attrTxt, string $cuerpo): ?array
    {
        $a = self::atributos($attrTxt);
        $titulo = mb_strtoupper(self::limpiar($a['titulo'] ?? ''));
        $pregunta = self::limpiar($a['pregunta'] ?? '');

        $analisis = [];
        $maxAnalisis = self::ANALISIS_DEFECTO;
        $cuerpo = preg_replace_callback('#<analisis\b([^>]*)>(.*?)</analisis>#isu', function (array $m) use (&$analisis, &$maxAnalisis): string {
            $aa = self::atributos($m[1]);
            if (isset($aa['max'])) {
                $maxAnalisis = max(300, min(self::ANALISIS_TOPE, (int) $aa['max']));
            }
            foreach (self::bloquesDeHtml($m[2]) as $b) {
                if ($b['t'] === 'p') {
                    $analisis[] = $b['texto'];
                } elseif ($b['t'] === 'lista') {
                    foreach ($b['items'] as $it) {
                        $analisis[] = '• ' . $it;
                    }
                }
            }
            return '';
        }, $cuerpo) ?? $cuerpo;

        $nota = '';
        $cuerpo = preg_replace_callback('#<nota\b[^>]*>(.*?)</nota>#isu', function (array $m) use (&$nota): string {
            $nota .= ($nota !== '' ? ' ' : '') . self::limpiar($m[1]);
            return '';
        }, $cuerpo) ?? $cuerpo;

        $graficos = [];
        $cuerpo = preg_replace_callback('#<grafico\b([^>]*)>(.*?)</grafico>#isu', function (array $m) use (&$graficos): string {
            $g = self::grafico($m[1], $m[2]);
            if ($g) {
                $graficos[] = $g;
            }
            return '';
        }, $cuerpo) ?? $cuerpo;

        $bloques = self::bloquesDeHtml($cuerpo);
        $analisis = self::recortarAnalisis($analisis, $maxAnalisis);

        if (!$graficos && !$analisis && !$bloques) {
            return null;
        }
        if (!$graficos && !$analisis) {
            return ['tipo' => 'bloques', 'titulo' => $titulo, 'subtitulo' => $pregunta, 'bloques' => $bloques, 'logo' => preg_match('/conclusi|recomendaci/iu', $titulo) ? 'original' : 'negro'];
        }
        return ['tipo' => 'resultado', 'titulo' => $titulo, 'pregunta' => $pregunta, 'graficos' => array_slice($graficos, 0, 3), 'analisis' => $analisis, 'nota' => $nota, 'bloques' => $bloques];
    }

    private static function recortarAnalisis(array $parrafos, int $max): array
    {
        $salida = [];
        $total = 0;
        foreach ($parrafos as $p) {
            $restante = $max - $total;
            if ($restante < 60) {
                break;
            }
            if (mb_strlen($p) > $restante) {
                $corte = mb_substr($p, 0, $restante);
                $punto = mb_strrpos($corte, '. ');
                $p = $punto !== false && $punto > $restante * 0.5 ? mb_substr($corte, 0, $punto + 1) : rtrim($corte, ' ,;:') . '…';
            }
            $salida[] = $p;
            $total += mb_strlen($p);
        }
        return $salida;
    }

    private static function grafico(string $attrTxt, string $cuerpo): ?array
    {
        $a = self::atributos($attrTxt);
        $tipo = strtolower($a['tipo'] ?? 'barras');
        if (!in_array($tipo, ['barras', 'barras_v', 'torta', 'dona', 'apilada'], true)) {
            $tipo = 'barras';
        }
        $unidad = array_key_exists('unidad', $a) ? mb_substr(trim($a['unidad']), 0, 14) : '%';
        $series = array_slice(array_values(array_filter(array_map('trim', explode('|', (string) ($a['series'] ?? ''))), 'strlen')), 0, 3);

        $datos = [];
        if (preg_match_all('/<dato\b([^>]*?)\/?>/iu', $cuerpo, $m)) {
            foreach ($m[1] as $attr) {
                if (count($datos) >= self::MAX_DATOS) {
                    break;
                }
                $d = self::atributos($attr);
                $valores = [];
                foreach (explode('|', (string) ($d['valor'] ?? '')) as $v) {
                    $n = self::numero($v);
                    if ($n !== null) {
                        $valores[] = $n;
                    }
                }
                $et = mb_substr(self::limpiar($d['etiqueta'] ?? ''), 0, 80);
                if ($et === '' || !$valores) {
                    continue;
                }
                $datos[] = ['etiqueta' => $et, 'valores' => array_slice($valores, 0, 3), 'color' => $d['color'] ?? '', 'n' => isset($d['n']) && is_numeric($d['n']) ? (int) $d['n'] : null];
            }
        }
        if (!$datos) {
            return null;
        }

        $orden = strtolower($a['orden'] ?? '');
        if ($orden === 'desc' || $orden === 'asc') {
            usort($datos, static fn ($x, $y) => $orden === 'desc' ? $y['valores'][0] <=> $x['valores'][0] : $x['valores'][0] <=> $y['valores'][0]);
        }
        $multiple = strtolower($a['multiple'] ?? '') === 'si';
        $forzar = strtolower($a['normalizar'] ?? '') === 'si';
        if ($unidad === '%' && !$multiple) {
            $datos = self::normalizar100($datos, $forzar);
        }
        $nSeries = max(array_map(static fn ($d) => count($d['valores']), $datos));
        foreach ($datos as &$d) {
            while (count($d['valores']) < $nSeries) {
                $d['valores'][] = 0.0;
            }
        }
        unset($d);

        return ['tipo' => $tipo, 'titulo' => mb_substr(self::limpiar($a['titulo'] ?? ''), 0, 140), 'unidad' => $unidad, 'series' => $series, 'datos' => $datos, 'multiple' => $multiple];
    }

    public static function normalizar100(array $datos, bool $forzar): array
    {
        $nSeries = max(array_map(static fn ($d) => count($d['valores']), $datos));
        for ($s = 0; $s < $nSeries; $s++) {
            $suma = 0.0;
            foreach ($datos as $d) {
                $suma += $d['valores'][$s] ?? 0;
            }
            if ($suma <= 0 || (!$forzar && ($suma < 97 || $suma > 103))) {
                foreach ($datos as $i => $d) {
                    if (isset($d['valores'][$s])) {
                        $datos[$i]['valores'][$s] = round($d['valores'][$s], 1);
                    }
                }
                continue;
            }
            $brutos = [];
            $pisos = [];
            $restos = [];
            foreach ($datos as $i => $d) {
                $v = ($d['valores'][$s] ?? 0) / $suma * 1000;
                $brutos[$i] = $v;
                $pisos[$i] = (int) floor($v);
                $restos[$i] = $v - $pisos[$i];
            }
            $faltan = 1000 - array_sum($pisos);
            arsort($restos);
            foreach (array_keys($restos) as $i) {
                if ($faltan <= 0) {
                    break;
                }
                $pisos[$i]++;
                $faltan--;
            }
            foreach ($datos as $i => $d) {
                $datos[$i]['valores'][$s] = (float) ($pisos[$i] / 10);
            }
        }
        return $datos;
    }

    public static function bloquesDeHtml(string $html): array
    {
        $html = trim(strip_tags($html, '<h1><h2><h3><h4><p><div><span><table><thead><tbody><tr><th><td><ul><ol><li><b><strong><i><em><small><blockquote><br><hr>'));
        if ($html === '') {
            return [];
        }
        $dom = new DOMDocument();
        $prev = libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8"?><div id="s360raiz">' . $html . '</div>', LIBXML_NOERROR | LIBXML_NOWARNING);
        libxml_clear_errors();
        libxml_use_internal_errors($prev);
        $raiz = $dom->getElementById('s360raiz');
        return $raiz ? self::bloquesDeNodo($raiz) : [];
    }

    private static function tieneClase(DOMElement $e, string $clase): bool
    {
        return in_array($clase, preg_split('/\s+/', trim($e->getAttribute('class'))), true);
    }

    private static function textoNodo(DOMNode $n): string
    {
        return trim(preg_replace('/\s+/u', ' ', $n->textContent));
    }

    private static function bloquesDeNodo(DOMElement $nodo): array
    {
        $bloques = [];
        foreach ($nodo->childNodes as $h) {
            if ($h instanceof DOMText) {
                $t = trim($h->textContent);
                if ($t !== '') {
                    $bloques[] = ['t' => 'p', 'texto' => $t];
                }
                continue;
            }
            if (!($h instanceof DOMElement)) {
                continue;
            }
            $tag = strtolower($h->tagName);
            if (in_array($tag, ['h1', 'h2', 'h3', 'h4'], true)) {
                $t = self::textoNodo($h);
                if ($t !== '') {
                    $bloques[] = ['t' => 'h', 'nivel' => (int) $tag[1], 'texto' => $t];
                }
            } elseif ($tag === 'p' || $tag === 'small') {
                $t = self::textoNodo($h);
                if ($t !== '') {
                    $bloques[] = ['t' => 'p', 'texto' => $t];
                }
            } elseif ($tag === 'ul' || $tag === 'ol') {
                $items = [];
                foreach ($h->getElementsByTagName('li') as $li) {
                    $t = self::textoNodo($li);
                    if ($t !== '') {
                        $items[] = $t;
                    }
                }
                if ($items) {
                    $bloques[] = ['t' => 'lista', 'ordenada' => $tag === 'ol', 'items' => $items];
                }
            } elseif ($tag === 'table') {
                $b = self::bloqueTabla($h);
                if ($b) {
                    $bloques[] = $b;
                }
            } elseif ($tag === 'blockquote' || $tag === 'div') {
                if (self::tieneClase($h, 's360-quote')) {
                    $bloques[] = ['t' => 'quote', 'texto' => self::textoNodo($h)];
                } elseif (self::tieneClase($h, 's360-callout')) {
                    $nivel = 'info';
                    foreach (['warning', 'danger', 'success'] as $n) {
                        if (self::tieneClase($h, 's360-callout-' . $n)) {
                            $nivel = $n;
                        }
                    }
                    $bloques[] = ['t' => 'callout', 'nivel' => $nivel, 'texto' => self::textoNodo($h)];
                } else {
                    foreach (self::bloquesDeNodo($h) as $b) {
                        $bloques[] = $b;
                    }
                }
            }
        }
        return $bloques;
    }

    private static function bloqueTabla(DOMElement $tabla): ?array
    {
        if (self::tieneClase($tabla, 's360-kpis')) {
            $items = [];
            foreach ($tabla->getElementsByTagName('td') as $td) {
                $valor = '';
                $etiqueta = '';
                foreach ($td->getElementsByTagName('div') as $d) {
                    if (self::tieneClase($d, 's360-kpi-value')) {
                        $valor = self::textoNodo($d);
                    } elseif (self::tieneClase($d, 's360-kpi-label')) {
                        $etiqueta = self::textoNodo($d);
                    }
                }
                if ($valor !== '') {
                    $items[] = ['valor' => $valor, 'etiqueta' => $etiqueta];
                }
            }
            return $items ? ['t' => 'kpis', 'items' => array_slice($items, 0, 5)] : null;
        }
        $encabezado = [];
        $filas = [];
        foreach ($tabla->getElementsByTagName('tr') as $tr) {
            $celdas = [];
            $esTh = false;
            foreach ($tr->childNodes as $c) {
                if ($c instanceof DOMElement && in_array(strtolower($c->tagName), ['td', 'th'], true)) {
                    $celdas[] = self::textoNodo($c);
                    $esTh = $esTh || strtolower($c->tagName) === 'th';
                }
            }
            if (!$celdas) {
                continue;
            }
            if ($esTh && !$encabezado && !$filas) {
                $encabezado = $celdas;
            } else {
                $filas[] = $celdas;
            }
        }
        return $filas || $encabezado ? ['t' => 'tabla', 'encabezado' => $encabezado, 'filas' => $filas, 'resaltada' => self::tieneClase($tabla, 's360-table-highlight')] : null;
    }

    private static function paginasDeBloques(array $bloques): array
    {
        $paginas = [];
        $actual = null;
        $cerrar = static function () use (&$paginas, &$actual): void {
            if ($actual && $actual['bloques']) {
                $paginas[] = $actual;
            }
            $actual = null;
        };
        foreach ($bloques as $b) {
            if ($b['t'] === 'h' && $b['nivel'] <= 2) {
                $cerrar();
                $actual = ['tipo' => 'bloques', 'titulo' => mb_strtoupper($b['texto']), 'subtitulo' => '', 'bloques' => [], 'logo' => preg_match('/conclusi|recomendaci/iu', $b['texto']) ? 'original' : 'negro'];
                continue;
            }
            if ($actual === null) {
                $actual = ['tipo' => 'bloques', 'titulo' => 'INTRODUCCIÓN', 'subtitulo' => '', 'bloques' => [], 'logo' => 'negro'];
            }
            $actual['bloques'][] = $b;
        }
        $cerrar();
        return $paginas;
    }
}
