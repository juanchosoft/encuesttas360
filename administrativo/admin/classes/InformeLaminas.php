<?php

require_once __DIR__ . '/InformeGraficos.php';
require_once __DIR__ . '/IaRecursos.php';

class InformeLaminas
{
    public const W = 338.667;
    public const H = 190.5;
    private const M = 14;
    private const PT = 2.8346;
    private const LH = 1.3;
    private const PX_MM = 8;

    private static array $spec;
    private static array $t;
    private static int $numero = 0;

    public static function construir(array $spec): array
    {
        self::$spec = $spec;
        self::$t = $spec['tema'];
        self::$numero = 0;
        $laminas = [self::portada()];
        foreach ($spec['paginas'] as $p) {
            if ($p['tipo'] === 'resultado') {
                $laminas[] = self::resultado($p);
            } elseif ($p['tipo'] === 'ficha') {
                foreach (self::ficha($p) as $l) {
                    $laminas[] = $l;
                }
            } else {
                foreach (self::bloques($p) as $l) {
                    $laminas[] = $l;
                }
            }
        }
        $laminas[] = self::cierre();
        return $laminas;
    }

    private static function alturaTexto(array $paras, float $anchoMm, float $size, string $peso, float $psp = 0): float
    {
        $size = round($size);
        $lineas = 0;
        foreach ($paras as $p) {
            $txt = is_array($p) ? $p['t'] : $p;
            $pe = is_array($p) ? ($p['peso'] ?? $peso) : $peso;
            $lineas += count(InformeGraficos::envolver($txt, $size, $pe, $anchoMm * self::PT * 0.95));
        }
        return $lineas * $size * self::LH / self::PT + max(0, count($paras) - 1) * $psp;
    }

    private static function ajustar(array $paras, float $anchoMm, float $altoMm, float $max, float $min, string $peso, float $psp = 0): float
    {
        for ($s = round($max); $s > $min; $s -= 1) {
            if (self::alturaTexto($paras, $anchoMm, $s, $peso, $psp) <= $altoMm) {
                return $s;
            }
        }
        return $min;
    }

    private static function texto(array &$p, float $x, float $y, float $w, float $h, array $paras, float $size, string $peso, string $color, string $align = 'L', float $psp = 0, string $valign = 'T'): void
    {
        $p[] = ['t' => 'text', 'x' => $x, 'y' => $y, 'w' => $w, 'h' => $h, 'paras' => $paras, 'size' => (int) round($size), 'peso' => $peso, 'color' => $color, 'align' => $align, 'lh' => self::LH, 'psp' => $psp, 'valign' => $valign];
    }

    private static function rect(array &$p, float $x, float $y, float $w, float $h, string $fill, ?string $borde = null): void
    {
        $p[] = ['t' => 'rect', 'x' => $x, 'y' => $y, 'w' => $w, 'h' => $h, 'fill' => $fill, 'borde' => $borde];
    }

    private static function imagen(array &$p, float $x, float $y, float $w, float $h, ?string $ruta, ?string $datos = null): void
    {
        $p[] = ['t' => 'img', 'x' => $x, 'y' => $y, 'w' => $w, 'h' => $h, 'ruta' => $ruta, 'datos' => $datos];
    }

    private static function logo(array &$p, string $variante, float $xDer, float $y, float $altoMm, string $alinea = 'R'): float
    {
        $logo = self::$spec['logo'];
        if (!$logo) {
            return 0;
        }
        $ruta = IaRecursos::rutaLogo($logo, $variante);
        $dim = @getimagesize($ruta);
        $ancho = $dim ? $altoMm * $dim[0] / max(1, $dim[1]) : $altoMm;
        $ancho = min($ancho, 60);
        $x = $alinea === 'R' ? $xDer - $ancho : ($alinea === 'C' ? $xDer - $ancho / 2 : $xDer);
        self::imagen($p, $x, $y, $ancho, $altoMm, $ruta);
        return $ancho;
    }

    private static function pie(array &$p): void
    {
        self::$numero++;
        $W = self::W;
        $H = self::H;
        self::rect($p, self::M, $H - 11.5, $W - 2 * self::M, 0.25, '#D5D9E2');
        $izq = self::$spec['realizado_por'] . '  ·  ' . self::$spec['titulo'];
        $ancho = ($W - 2 * self::M - 30) * self::PT;
        while (mb_strlen($izq) > 10 && InformeGraficos::medir($izq, 9, 'regular') > $ancho) {
            $izq = rtrim(mb_substr($izq, 0, mb_strlen($izq) - 4)) . '…';
        }
        self::texto($p, self::M, $H - 9.5, $W - 2 * self::M - 30, 5, [$izq], 9, 'regular', self::$t['secundario']);
        self::texto($p, $W - self::M - 28, $H - 9.5, 28, 5, [(string) (self::$numero + 1)], 9, 'regular', self::$t['secundario'], 'R');
    }

    private static function cabecera(array &$p, string $titulo, string $logoVar = 'negro'): float
    {
        $W = self::W;
        $oscura = $logoVar === 'original';
        $anchoTit = $W - 2 * self::M - 36;
        $size = 26;
        while ($size > 17 && InformeGraficos::medir($titulo, $size, 'semibold') > $anchoTit * self::PT * 0.97) {
            $size -= 1;
        }
        if ($oscura) {
            self::rect($p, 0, 0, $W, 30, self::$t['primario']);
            self::rect($p, 0, 30, $W, 1.4, self::$t['acento']);
            self::texto($p, self::M, 5, $anchoTit, 20, [$titulo], $size, 'semibold', '#FFFFFF', 'L', 0, 'M');
            self::logo($p, 'original', $W - self::M, 3, 25);
            return 36;
        }
        self::texto($p, self::M, 7, $anchoTit, 14, [$titulo], $size, 'semibold', self::$t['primario'], 'L', 0, 'M');
        self::rect($p, self::M, 23.5, 22, 1.3, self::$t['acento']);
        self::logo($p, 'negro', $W - self::M, 5.5, 17);
        return 30;
    }

    private static function lamina(array $prims, ?string $fondo = '#FFFFFF'): array
    {
        return ['fondo' => $fondo, 'prims' => $prims];
    }

    private static function fondoImagen(array &$p, ?array $recurso): void
    {
        if ($recurso) {
            $ruta = IaRecursos::rutaFondo($recurso, 2000, (int) round(2000 * self::H / self::W), self::$spec['brillo']);
            self::imagen($p, 0, 0, self::W, self::H, $ruta);
        } else {
            self::rect($p, 0, 0, self::W, self::H, self::$t['primario']);
            self::rect($p, 0, 0, 10, self::H, self::$t['acento']);
        }
    }

    private static function portada(): array
    {
        $s = self::$spec;
        $p = [];
        self::fondoImagen($p, $s['portada']);
        $x = 28;
        $w = self::W * 0.66;
        $size = 34;
        $y = 40;
        $mayus = mb_strtoupper($s['titulo']);
        $size = self::ajustar([$mayus], $w, 66, 36, 22, 'bold');
        $h = self::alturaTexto([$mayus], $w, $size, 'bold');
        self::rect($p, $x, $y - 7, 26, 1.6, '#FFFFFF');
        self::texto($p, $x, $y, $w, $h + 2, [$mayus], $size, 'bold', '#FFFFFF');
        $y += $h + 8;
        foreach ($s['subtitulos'] as $i => $sub) {
            $sz = $i === 0 ? 22 : 18;
            $hh = self::alturaTexto([$sub], $w, $sz, 'semibold');
            self::texto($p, $x, $y, $w, $hh + 1, [$sub], $sz, 'semibold', '#F1F3F8');
            $y += $hh + 3;
        }
        if ($s['fecha_texto'] !== '') {
            self::texto($p, $x, $y, $w, 9, [$s['fecha_texto']], 18, 'semibold', '#F1F3F8');
        }
        self::realizadoPor($p, $x, self::H - 52, 'L');
        return self::lamina($p, null);
    }

    private static function realizadoPor(array &$p, float $x, float $y, string $alinea, float $altoLogo = 17): void
    {
        $s = self::$spec;
        $ancho = 150;
        $xTxt = $alinea === 'C' ? $x - $ancho / 2 : $x;
        self::texto($p, $xTxt, $y, $ancho, 7, ['Realizado por:'], 13, 'medium', '#E4E7EF', $alinea === 'C' ? 'C' : 'L');
        $yl = $y + 9;
        $lw = 0;
        if ($s['logo']) {
            $lw = self::logo($p, 'blanco', $x, $yl, $altoLogo, $alinea === 'C' ? 'C' : 'L');
        }
        if ($alinea === 'C') {
            self::texto($p, $xTxt, $yl + ($lw > 0 ? $altoLogo + 3 : 0), $ancho, 8, [$s['realizado_por']], 15, 'semibold', '#FFFFFF', 'C');
        } else {
            self::texto($p, $x + ($lw > 0 ? $lw + 6 : 0), $yl, 200, 17, [$s['realizado_por']], 17, 'semibold', '#FFFFFF', 'L', 0, 'M');
        }
    }

    private static function cierre(): array
    {
        $p = [];
        self::fondoImagen($p, self::$spec['cierre']);
        self::realizadoPor($p, self::W / 2, self::H / 2 - 30, 'C', 26);
        return self::lamina($p, null);
    }

    private static function bloquePregunta(array &$p, string $pregunta, float $y): float
    {
        if ($pregunta === '') {
            return $y;
        }
        $ancho = self::W - 2 * self::M;
        $size = self::ajustar([$pregunta], $ancho, 16, 16, 12, 'medium');
        $h = self::alturaTexto([$pregunta], $ancho, $size, 'medium');
        self::texto($p, self::M, $y, $ancho, $h + 1, [$pregunta], $size, 'medium', self::$t['texto']);
        return $y + $h + 4;
    }

    private static function resultado(array $pg): array
    {
        $p = [];
        $W = self::W;
        $H = self::H;
        $top = self::cabecera($p, $pg['titulo']);
        $y = self::bloquePregunta($p, $pg['pregunta'], $top + 2);
        $fondoCuerpo = $H - 15;
        if ($pg['nota'] !== '') {
            $hNota = self::alturaTexto([$pg['nota']], $W - 2 * self::M, 10, 'italic');
            self::texto($p, self::M, $fondoCuerpo - $hNota, $W - 2 * self::M, $hNota + 1, [$pg['nota']], 10, 'italic', self::$t['secundario']);
            $fondoCuerpo -= $hNota + 3;
        }
        $alto = $fondoCuerpo - $y;
        $total = $W - 2 * self::M;
        $conAnalisis = (bool) $pg['analisis'];
        $anchoGraf = $conAnalisis ? $total * 0.585 : $total;

        $tablas = array_values(array_filter($pg['bloques'], static fn ($b) => $b['t'] === 'tabla'));
        $nGraf = count($pg['graficos']);
        $altoGraficos = $tablas && $nGraf > 0 ? $alto * 0.55 : $alto;
        if ($nGraf > 0) {
            $hTit = 8;
            $hCada = ($altoGraficos - ($nGraf - 1) * 3) / $nGraf;
            $yy = $y;
            foreach ($pg['graficos'] as $g) {
                $tituloH = $g['titulo'] !== '' ? $hTit : 0;
                if ($tituloH) {
                    self::texto($p, self::M, $yy, $anchoGraf, $hTit, [$g['titulo']], 13, 'semibold', self::$t['primario']);
                }
                $hImg = $hCada - $tituloH;
                $wPx = (int) round($anchoGraf * self::PX_MM);
                $hPx = (int) round($hImg * self::PX_MM);
                $png = InformeGraficos::png($g, self::$t, $wPx, $hPx);
                self::imagen($p, self::M, $yy + $tituloH, $anchoGraf, $hImg, null, $png);
                $yy += $hCada + 3;
            }
            if ($tablas) {
                self::tabla($p, $tablas[0], self::M, $y + $altoGraficos + 3, $anchoGraf, $alto - $altoGraficos - 3);
            }
        } elseif ($tablas) {
            self::tabla($p, $tablas[0], self::M, $y, $anchoGraf, $alto);
        }

        if ($conAnalisis) {
            $xs = self::M + $anchoGraf + 7;
            self::rect($p, $xs, $y, 0.7, $alto, self::$t['acento']);
            $xa = $xs + 8;
            $wa = $W - self::M - $xa;
            self::texto($p, $xa, $y, $wa, 8, ['ANÁLISIS'], 13, 'semibold', self::$t['primario']);
            $paras = $pg['analisis'];
            $hDisp = $alto - 10;
            $size = self::ajustar($paras, $wa, $hDisp, 14, 10, 'regular', 3);
            $hh = self::alturaTexto($paras, $wa, $size, 'regular', 3);
            self::texto($p, $xa, $y + 10, $wa, $hh + 1, $paras, $size, 'regular', self::$t['texto'], 'L', 3);
        }
        self::pie($p);
        return self::lamina($p);
    }

    private static function tabla(array &$p, array $t, float $x, float $y, float $ancho, float $altoMax): float
    {
        $encab = $t['encabezado'];
        $filas = $t['filas'];
        $nCols = max(count($encab), $filas ? max(array_map('count', $filas)) : 0);
        if ($nCols === 0) {
            return 0;
        }
        $size = 11.5;
        do {
            $anchos = array_fill(0, $nCols, 0.0);
            foreach (array_merge($encab ? [$encab] : [], $filas) as $fila) {
                foreach ($fila as $c => $txt) {
                    $anchos[$c] = max($anchos[$c], min(InformeGraficos::medir((string) $txt, $size, 'regular') / self::PT + 7, $ancho * 0.45));
                }
            }
            $suma = array_sum($anchos);
            $anchos = array_map(static fn ($a) => max(14, $a / max($suma, 1) * $ancho), $anchos);
            $factor = $ancho / array_sum($anchos);
            $anchos = array_map(static fn ($a) => $a * $factor, $anchos);
            $minimos = array_fill(0, $nCols, 0.0);
            foreach (array_merge($encab ? [$encab] : [], $filas) as $r => $fila) {
                foreach ($fila as $c => $txt) {
                    foreach (preg_split('/\s+/u', (string) $txt) as $palabra) {
                        $minimos[$c] = max($minimos[$c], InformeGraficos::medir($palabra, $size, $encab && $r === 0 ? 'semibold' : 'regular') / self::PT + 7);
                    }
                }
            }
            $deficit = 0.0;
            foreach ($anchos as $c => $a) {
                if ($a < $minimos[$c]) {
                    $deficit += $minimos[$c] - $a;
                    $anchos[$c] = $minimos[$c];
                }
            }
            if ($deficit > 0) {
                $holgura = 0.0;
                foreach ($anchos as $c => $a) {
                    $holgura += max(0, $a - $minimos[$c]);
                }
                foreach ($anchos as $c => $a) {
                    if ($holgura > 0 && $a > $minimos[$c]) {
                        $anchos[$c] = $a - $deficit * (($a - $minimos[$c]) / $holgura);
                    }
                }
            }
            $alturas = [];
            $total = 0;
            foreach (array_merge($encab ? [$encab] : [], $filas) as $fila) {
                $hMax = 0;
                foreach ($fila as $c => $txt) {
                    $hMax = max($hMax, self::alturaTexto([(string) $txt], $anchos[$c] - 5, $size, 'regular'));
                }
                $alturas[] = $hMax + 4;
                $total += $hMax + 4;
            }
            $size -= 0.5;
        } while ($total > $altoMax && $size > 8.5);
        $size += 0.5;

        $yy = $y;
        $idx = 0;
        $todas = array_merge($encab ? [$encab] : [], $filas);
        foreach ($todas as $r => $fila) {
            $h = $alturas[$r];
            if ($yy + $h > $y + $altoMax + 0.5) {
                break;
            }
            $esEnc = $encab && $r === 0;
            $fill = $esEnc ? self::$t['primario'] : ($idx % 2 === 0 ? '#FFFFFF' : self::$t['suave']);
            self::rect($p, $x, $yy, $ancho, $h, $fill, $esEnc ? null : '#E3E6EE');
            $xx = $x;
            foreach ($fila as $c => $txt) {
                self::texto($p, $xx + 2.5, $yy + 2, $anchos[$c] - 5, $h - 3, [(string) $txt], $size, $esEnc ? 'semibold' : 'regular', $esEnc ? '#FFFFFF' : self::$t['texto'], $c === 0 ? 'L' : 'C');
                $xx += $anchos[$c];
            }
            $yy += $h;
            if (!$esEnc) {
                $idx++;
            }
        }
        return $yy - $y;
    }

    private static function ficha(array $pg): array
    {
        $laminas = [];
        $filas = $pg['filas'];
        $W = self::W;
        $H = self::H;
        $wLabel = 70;
        $wVal = $W - 2 * self::M - $wLabel;
        $size = 10.5;
        $cola = [];
        foreach ($filas as $et => $val) {
            $cola[] = [$et, $val, max(self::alturaTexto([$val], $wVal - 6, $size, 'regular'), self::alturaTexto([$et], $wLabel - 6, $size, 'semibold')) + 5];
        }
        $n = 0;
        while ($cola) {
            $p = [];
            $top = self::cabecera($p, $n === 0 ? $pg['titulo'] : $pg['titulo'] . ' (CONT.)');
            $y = $top + 3;
            $limite = $H - 15;
            $primera = true;
            while ($cola && ($primera || $y + $cola[0][2] <= $limite)) {
                [$et, $val, $h] = array_shift($cola);
                self::rect($p, self::M, $y, $wLabel, $h, self::$t['suave'], '#D5D9E2');
                self::rect($p, self::M + $wLabel, $y, $wVal, $h, '#FFFFFF', '#D5D9E2');
                self::texto($p, self::M + 3, $y + 2.5, $wLabel - 6, $h - 3, [$et], $size, 'semibold', self::$t['primario']);
                self::texto($p, self::M + $wLabel + 3, $y + 2.5, $wVal - 6, $h - 3, [$val], $size, 'regular', self::$t['texto']);
                $y += $h;
                $primera = false;
            }
            self::pie($p);
            $laminas[] = self::lamina($p);
            $n++;
        }
        return $laminas;
    }

    private static function alturaBloque(array $b, float $ancho, float $k): float
    {
        switch ($b['t']) {
            case 'h':
                return 11 * $k;
            case 'p':
                return self::alturaTexto([$b['texto']], $ancho, 14 * $k, 'regular') + 4 * $k;
            case 'lista':
                $h = 0;
                foreach ($b['items'] as $i => $it) {
                    $h += self::alturaTexto([$it], $ancho - 8, 14 * $k, 'regular') + 2.5 * $k;
                }
                return $h + 3;
            case 'kpis':
                return 40 * $k + 5;
            case 'tabla':
                return (count($b['filas']) + ($b['encabezado'] ? 1 : 0)) * 11 * $k + 5;
            case 'callout':
            case 'quote':
                return self::alturaTexto([$b['texto']], $ancho - 14, 13.5 * $k, 'medium') + 10 * $k + 4;
        }
        return 0;
    }

    private static function dibujarBloque(array &$p, array $b, float $x, float $y, float $ancho, float $k): float
    {
        $t = self::$t;
        switch ($b['t']) {
            case 'h':
                self::texto($p, $x, $y, $ancho, 9 * $k, [$b['texto']], 15 * $k, 'semibold', $t['acento']);
                return 11 * $k;
            case 'p':
                $h = self::alturaTexto([$b['texto']], $ancho, 14 * $k, 'regular');
                self::texto($p, $x, $y, $ancho, $h + 1, [$b['texto']], 14 * $k, 'regular', $t['texto']);
                return $h + 4 * $k;
            case 'lista':
                $yy = $y;
                foreach ($b['items'] as $i => $it) {
                    $h = self::alturaTexto([$it], $ancho - 8, 14 * $k, 'regular');
                    self::texto($p, $x, $yy, 7, 7, [$b['ordenada'] ? ($i + 1) . '.' : '•'], 14 * $k, 'semibold', $t['acento']);
                    self::texto($p, $x + 8, $yy, $ancho - 8, $h + 1, [$it], 14 * $k, 'regular', $t['texto']);
                    $yy += $h + 2.5 * $k;
                }
                return $yy - $y + 3;
            case 'kpis':
                $n = count($b['items']);
                $gap = 6;
                $wc = ($ancho - $gap * ($n - 1)) / $n;
                $hc = 40 * $k;
                foreach ($b['items'] as $i => $it) {
                    $xx = $x + $i * ($wc + $gap);
                    self::rect($p, $xx, $y, $wc, $hc, $t['suave'], '#D5D9E2');
                    self::rect($p, $xx, $y, $wc, 1.4, $t['acento']);
                    self::texto($p, $xx + 3, $y + 6 * $k, $wc - 6, 16 * $k, [$it['valor']], 28 * $k, 'bold', $t['primario'], 'C');
                    self::texto($p, $xx + 3, $y + 24 * $k, $wc - 6, 14 * $k, [$it['etiqueta']], 12 * $k, 'medium', $t['texto'], 'C');
                }
                return $hc + 5;
            case 'tabla':
                return self::tabla($p, $b, $x, $y, $ancho, 120) + 5;
            case 'callout':
            case 'quote':
                $h = self::alturaTexto([$b['texto']], $ancho - 14, 13.5 * $k, 'medium') + 10 * $k;
                $quote = $b['t'] === 'quote';
                $colores = ['info' => '#3B82F6', 'warning' => '#F5A623', 'danger' => '#E5484D', 'success' => '#12B981'];
                self::rect($p, $x, $y, $ancho, $h, $quote ? $t['primario'] : $t['suave']);
                self::rect($p, $x, $y, 2.2, $h, $quote ? $t['acento'] : ($colores[$b['nivel'] ?? 'info'] ?? $t['acento']));
                self::texto($p, $x + 8, $y + 5 * $k, $ancho - 14, $h - 4, [$b['texto']], 13.5 * $k, 'medium', $quote ? '#FFFFFF' : $t['texto']);
                return $h + 4;
        }
        return 0;
    }

    private static function tarjetas(array $b, float $y, float $limite): array
    {
        $p = [];
        $n = count($b['items']);
        $W = self::W;
        $cols = $n === 1 ? 1 : 2;
        $filas = (int) ceil($n / $cols);
        $gap = 8;
        $wc = ($W - 2 * self::M - $gap * ($cols - 1)) / $cols;
        $hc = ($limite - $y - $gap * ($filas - 1)) / $filas;
        $hc = min($hc, 70);
        $sizeMin = 99;
        foreach ($b['items'] as $it) {
            $sizeMin = min($sizeMin, self::ajustar([$it], $wc - 24, $hc - 5, 18, 10, 'regular'));
        }
        foreach ($b['items'] as $i => $it) {
            $c = $i % $cols;
            $f = intdiv($i, $cols);
            $x = self::M + $c * ($wc + $gap);
            $yy = $y + $f * ($hc + $gap);
            self::rect($p, $x, $yy, $wc, $hc, self::$t['suave'], '#D5D9E2');
            self::rect($p, $x, $yy, 1.8, $hc, self::$t['acento']);
            self::rect($p, $x + 6, $yy + 5, 9, 9, self::$t['acento']);
            self::texto($p, $x + 6, $yy + 5, 9, 9, [$b['ordenada'] ? (string) ($i + 1) : '•'], 14, 'bold', '#FFFFFF', 'C', 0, 'M');
            $h = self::alturaTexto([$it], $wc - 24, $sizeMin, 'regular');
            self::texto($p, $x + 19, $yy + 5, $wc - 24, $hc - 6, [$it], $sizeMin, 'regular', self::$t['texto']);
        }
        return $p;
    }

    private static function bloques(array $pg): array
    {
        $laminas = [];
        $W = self::W;
        $H = self::H;
        $ancho = $W - 2 * self::M;
        $logoVar = $pg['logo'] ?? 'negro';
        $bloques = $pg['bloques'];
        $esConclusion = $logoVar === 'original';
        $n = 0;

        while ($bloques) {
            $p = [];
            $top = self::cabecera($p, $n === 0 ? $pg['titulo'] : $pg['titulo'] . ' (CONT.)', $logoVar);
            $y = self::bloquePregunta($p, $n === 0 ? ($pg['subtitulo'] ?? '') : '', $top + 3);
            $limite = $H - 15;
            $disp = $limite - $y;

            if ($esConclusion && count($bloques) === 1 && $bloques[0]['t'] === 'lista' && count($bloques[0]['items']) >= 2 && count($bloques[0]['items']) <= 6) {
                foreach (self::tarjetas($bloques[0], $y, $limite) as $prim) {
                    $p[] = $prim;
                }
                $bloques = [];
            } else {
                $k = 1.0;
                foreach ([1.0, 0.92, 0.84, 0.76] as $kk) {
                    $suma = 0;
                    foreach ($bloques as $b) {
                        $suma += self::alturaBloque($b, $ancho, $kk);
                    }
                    $k = $kk;
                    if ($suma <= $disp) {
                        break;
                    }
                }
                $usados = 0;
                $yy = $y;
                foreach ($bloques as $i => $b) {
                    $h = self::alturaBloque($b, $ancho, $k);
                    if ($usados > 0 && $yy + $h > $limite) {
                        break;
                    }
                    $yy += self::dibujarBloque($p, $b, self::M, $yy, $ancho, $k);
                    $usados++;
                }
                $bloques = array_slice($bloques, max(1, $usados));
            }
            self::pie($p);
            $laminas[] = self::lamina($p);
            $n++;
        }
        return $laminas;
    }
}
