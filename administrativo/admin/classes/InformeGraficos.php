<?php

class InformeGraficos
{
    private const COLORES_NOMBRADOS = [
        'verde' => '#2E9E6B', 'rojo' => '#D64550', 'naranja' => '#F29E4C', 'amarillo' => '#E6B422',
        'morado' => '#7C5CFA', 'turquesa' => '#14B8C6', 'gris' => '#9AA3B2',
    ];

    private static array $cacheMedidas = [];

    public static function fuente(string $peso): string
    {
        $mapa = ['regular' => 'Regular', 'medium' => 'Medium', 'semibold' => 'SemiBold', 'bold' => 'Bold', 'italic' => 'Italic'];
        return realpath(__DIR__ . '/../include/fuentes/poppins/Poppins-' . ($mapa[$peso] ?? 'Regular') . '.ttf') ?: '';
    }

    public static function medir(string $texto, float $tam, string $peso = 'regular'): float
    {
        $clave = $peso . '|' . $tam . '|' . $texto;
        if (isset(self::$cacheMedidas[$clave])) {
            return self::$cacheMedidas[$clave];
        }
        $caja = imagettfbbox($tam, 0, self::fuente($peso), $texto);
        $ancho = $caja ? abs($caja[4] - $caja[0]) : 0;
        if (count(self::$cacheMedidas) > 4000) {
            self::$cacheMedidas = [];
        }
        return self::$cacheMedidas[$clave] = (float) $ancho;
    }

    public static function envolver(string $texto, float $tam, string $peso, float $anchoMax): array
    {
        $lineas = [];
        foreach (preg_split('/\R/u', $texto) as $parrafo) {
            $actual = '';
            foreach (preg_split('/\s+/u', trim($parrafo), -1, PREG_SPLIT_NO_EMPTY) ?: [''] as $palabra) {
                $prueba = $actual === '' ? $palabra : $actual . ' ' . $palabra;
                if ($actual !== '' && self::medir($prueba, $tam, $peso) > $anchoMax) {
                    $lineas[] = $actual;
                    $actual = $palabra;
                } else {
                    $actual = $prueba;
                }
            }
            $lineas[] = $actual;
        }
        return $lineas;
    }

    public static function colorDe(?string $nombre, int $indice, array $tema): string
    {
        $n = strtolower(trim((string) $nombre));
        if ($n === 'azul') {
            return $tema['acento'];
        }
        if ($n === 'azul-oscuro') {
            return $tema['primario'];
        }
        if (isset(self::COLORES_NOMBRADOS[$n])) {
            return self::COLORES_NOMBRADOS[$n];
        }
        if (preg_match('/^#[0-9a-f]{6}$/i', $n)) {
            return strtoupper($n);
        }
        $serie = $tema['serie'];
        return $serie[$indice % count($serie)];
    }

    public static function formato(float $v, string $unidad): string
    {
        $txt = number_format($v, $unidad === '%' || abs($v - round($v)) >= 0.05 ? 1 : 0, ',', '.');
        return $unidad === '' ? $txt : $txt . ($unidad === '%' ? ' %' : ' ' . $unidad);
    }

    private static function col($img, string $hex, int $alfa = 0)
    {
        $h = ltrim($hex, '#');
        return imagecolorallocatealpha($img, hexdec(substr($h, 0, 2)), hexdec(substr($h, 2, 2)), hexdec(substr($h, 4, 2)), $alfa);
    }

    private static function texto($img, float $tam, float $x, float $y, string $hex, string $txt, string $peso = 'regular', string $alinea = 'l'): void
    {
        $ancho = self::medir($txt, $tam, $peso);
        if ($alinea === 'c') {
            $x -= $ancho / 2;
        } elseif ($alinea === 'r') {
            $x -= $ancho;
        }
        imagettftext($img, $tam, 0, (int) round($x), (int) round($y), self::col($img, $hex), self::fuente($peso), $txt);
    }

    public static function png(array $g, array $tema, int $w = 1500, int $h = 900, string $fondo = '#FFFFFF'): string
    {
        $img = imagecreatetruecolor($w, $h);
        imagealphablending($img, true);
        imagefill($img, 0, 0, self::col($img, $fondo));
        imageantialias($img, true);

        $tipo = $g['tipo'];
        if ($tipo === 'torta' || $tipo === 'dona') {
            self::torta($img, $g, $tema, $w, $h, $tipo === 'dona', $fondo);
        } elseif ($tipo === 'apilada') {
            self::apilada($img, $g, $tema, $w, $h);
        } elseif ($tipo === 'barras_v') {
            self::barrasVerticales($img, $g, $tema, $w, $h);
        } else {
            self::barrasHorizontales($img, $g, $tema, $w, $h);
        }

        ob_start();
        imagepng($img, null, 6);
        $png = (string) ob_get_clean();
        imagedestroy($img);
        return $png;
    }

    private static function leyendaSeries($img, array $g, array $tema, float $x, float $y, float $tam): float
    {
        $series = $g['series'] ?? [];
        if (count($series) < 2) {
            return 0;
        }
        $cursor = $x;
        foreach ($series as $i => $nombre) {
            imagefilledrectangle($img, (int) $cursor, (int) ($y - $tam * 0.8), (int) ($cursor + $tam * 0.9), (int) ($y + $tam * 0.1), self::col($img, self::colorDe('', $i, $tema)));
            self::texto($img, $tam, $cursor + $tam * 1.3, $y, $tema['texto'], $nombre, 'medium');
            $cursor += $tam * 1.3 + self::medir($nombre, $tam, 'medium') + $tam * 1.8;
        }
        return $tam * 2.2;
    }

    private static function barrasVerticales($img, array $g, array $tema, int $w, int $h): void
    {
        $datos = $g['datos'];
        $n = count($datos);
        $nSeries = max(1, count($datos[0]['valores']));
        $unidad = $g['unidad'];
        $tamEtiq = $n <= 5 ? 26 : ($n <= 8 ? 22 : ($n <= 12 ? 19 : 16));
        $tamVal = $n <= 8 ? 24 : ($n <= 12 ? 20 : 17);
        $izq = 30;
        $der = 30;
        $top = 30 + self::leyendaSeries($img, $g, $tema, $izq, 36, 22);
        $ancho = $w - $izq - $der;
        $slot = $ancho / $n;

        $palabraMax = 0.0;
        foreach ($datos as $d) {
            foreach (preg_split('/\s+/u', $d['etiqueta']) as $palabra) {
                $palabraMax = max($palabraMax, self::medir($palabra, $tamEtiq, 'medium'));
            }
        }
        while ($tamEtiq > 18 && $palabraMax > $slot - 10) {
            $tamEtiq--;
            $palabraMax = 0.0;
            foreach ($datos as $d) {
                foreach (preg_split('/\s+/u', $d['etiqueta']) as $palabra) {
                    $palabraMax = max($palabraMax, self::medir($palabra, $tamEtiq, 'medium'));
                }
            }
        }
        $maxLineas = 1;
        $lineasPor = [];
        foreach ($datos as $i => $d) {
            $lineasPor[$i] = self::envolver($d['etiqueta'], $tamEtiq, 'medium', $slot - 10);
            $maxLineas = max($maxLineas, count($lineasPor[$i]));
        }
        $maxLineas = min($maxLineas, 5);
        $rotar = $palabraMax > $slot - 10;
        $anchoRotado = 0.0;
        if ($rotar) {
            $tamEtiq = 20;
            foreach ($datos as $d) {
                $anchoRotado = max($anchoRotado, self::medir($d['etiqueta'], $tamEtiq, 'medium'));
            }
            $anchoRotado = min($anchoRotado, $h * 0.40);
            $altoEtiq = $anchoRotado * 0.72 + 34;
        } else {
            $altoEtiq = $maxLineas * $tamEtiq * 1.3 + 20;
        }
        $base = $h - $altoEtiq;
        $maximo = 0.0;
        foreach ($datos as $d) {
            $maximo = max($maximo, max($d['valores']));
        }
        $escala = $unidad === '%' ? max($maximo * 1.12, 10) : max($maximo * 1.12, 1);
        $altoPlot = $base - $top - $tamVal * 1.8;

        imagesetthickness($img, 2);
        imageline($img, $izq, (int) $base, $w - $der, (int) $base, self::col($img, '#C9CED8'));

        foreach ($datos as $i => $d) {
            $x0 = $izq + $slot * $i;
            $anchoGrupo = $slot * ($nSeries > 1 ? 0.8 : 0.66);
            $anchoBarra = $anchoGrupo / $nSeries;
            $ini = $x0 + ($slot - $anchoGrupo) / 2;
            foreach ($d['valores'] as $s => $v) {
                $alto = $altoPlot * ($v / $escala);
                $bx = $ini + $anchoBarra * $s;
                $color = $nSeries > 1 ? self::colorDe('', $s, $tema) : self::colorDe($d['color'], 0, $tema);
                imagefilledrectangle($img, (int) round($bx + 2), (int) round($base - $alto), (int) round($bx + $anchoBarra - 2), (int) $base, self::col($img, $color));
                self::texto($img, $tamVal, $bx + $anchoBarra / 2, $base - $alto - 10, $tema['texto'], self::formato($v, $unidad), 'bold', 'c');
            }
            if ($rotar) {
                $et = $d['etiqueta'];
                while (mb_strlen($et) > 4 && self::medir($et, $tamEtiq, 'medium') > $anchoRotado) {
                    $et = rtrim(mb_substr($et, 0, mb_strlen($et) - 2)) . '…';
                }
                $largo = self::medir($et, $tamEtiq, 'medium');
                $fin = $x0 + $slot / 2 + 8;
                imagettftext($img, $tamEtiq, 45, (int) round($fin - $largo * 0.7071), (int) round($base + 14 + $largo * 0.7071 + 4), self::col($img, $tema['texto']), self::fuente('medium'), $et);
                continue;
            }
            foreach (array_slice($lineasPor[$i], 0, 5) as $k => $linea) {
                self::texto($img, $tamEtiq, $x0 + $slot / 2, $base + 14 + $tamEtiq + $k * $tamEtiq * 1.3, $tema['texto'], $linea, 'medium', 'c');
            }
        }
    }

    private static function barrasHorizontales($img, array $g, array $tema, int $w, int $h): void
    {
        $datos = $g['datos'];
        $n = count($datos);
        $nSeries = max(1, count($datos[0]['valores']));
        $unidad = $g['unidad'];
        $tamEtiq = $n <= 5 ? 28 : ($n <= 8 ? 25 : ($n <= 12 ? 22 : 19));
        $tamVal = $tamEtiq;
        $izq = 20;
        $top = 20 + self::leyendaSeries($img, $g, $tema, $izq, 36, 22);
        $anchoEtiq = 0;
        foreach ($datos as $d) {
            $anchoEtiq = max($anchoEtiq, self::medir($d['etiqueta'], $tamEtiq, 'medium'));
        }
        $anchoEtiq = min($anchoEtiq, $w * 0.38);
        $maxVal = 0.0;
        foreach ($datos as $d) {
            $maxVal = max($maxVal, max($d['valores']));
        }
        $anchoValor = self::medir(self::formato($maxVal, $unidad), $tamVal, 'bold') + 24;
        $xBarra = $izq + $anchoEtiq + 24;
        $anchoMax = $w - $xBarra - $anchoValor - 10;
        $escala = $unidad === '%' ? max($maxVal * 1.05, 10) : max($maxVal * 1.05, 1);
        $fila = ($h - $top - 10) / $n;
        $altoBarra = min(($fila * 0.62) / $nSeries, 70);

        foreach ($datos as $i => $d) {
            $yc = $top + $fila * $i + $fila / 2;
            $lineas = self::envolver($d['etiqueta'], $tamEtiq, 'medium', $anchoEtiq);
            $lineas = array_slice($lineas, 0, 2);
            $y0 = $yc - (count($lineas) - 1) * $tamEtiq * 0.65 + $tamEtiq * 0.35;
            foreach ($lineas as $k => $linea) {
                self::texto($img, $tamEtiq, $izq + $anchoEtiq, $y0 + $k * $tamEtiq * 1.25, $tema['texto'], $linea, 'medium', 'r');
            }
            foreach ($d['valores'] as $s => $v) {
                $by = $yc - ($altoBarra * $nSeries) / 2 + $altoBarra * $s;
                $largo = max(2, $anchoMax * ($v / $escala));
                $color = $nSeries > 1 ? self::colorDe('', $s, $tema) : self::colorDe($d['color'], 0, $tema);
                imagefilledrectangle($img, (int) $xBarra, (int) round($by + 2), (int) round($xBarra + $largo), (int) round($by + $altoBarra - 2), self::col($img, $color));
                self::texto($img, $tamVal, $xBarra + $largo + 12, $by + $altoBarra / 2 + $tamVal * 0.36, $tema['texto'], self::formato($v, $unidad), 'bold');
            }
        }
    }

    private static function torta($img, array $g, array $tema, int $w, int $h, bool $dona, string $fondo): void
    {
        $datos = $g['datos'];
        $unidad = $g['unidad'];
        $suma = array_sum(array_map(static fn ($d) => $d['valores'][0], $datos));
        if ($suma <= 0) {
            return;
        }
        $diam = (int) min($h * 0.92, $w * 0.46);
        $cx = (int) ($diam / 2 + 20);
        $cy = (int) ($h / 2);
        $S = 3;
        $big = imagecreatetruecolor($diam * $S, $diam * $S);
        imagefill($big, 0, 0, self::col($big, $fondo));
        $acum = 0.0;
        $ini = -90;
        $R = $diam * $S;
        $centros = [];
        foreach ($datos as $i => $d) {
            $pct = $d['valores'][0] / $suma * 100;
            $acum += $pct;
            $fin = -90 + (int) round($acum * 3.6);
            if ($fin > $ini) {
                imagefilledarc($big, (int) ($R / 2), (int) ($R / 2), $R, $R, $ini, $fin, self::col($big, self::colorDe($d['color'], $i, $tema)), IMG_ARC_PIE);
                imagesetthickness($big, 4 * $S);
                $rad = deg2rad($ini);
                imageline($big, (int) ($R / 2), (int) ($R / 2), (int) round($R / 2 + cos($rad) * $R / 2), (int) round($R / 2 + sin($rad) * $R / 2), self::col($big, $fondo));
            }
            $centros[$i] = deg2rad(($ini + $fin) / 2);
            $ini = $fin;
        }
        if ($dona) {
            imagefilledellipse($big, (int) ($R / 2), (int) ($R / 2), (int) ($R * 0.5), (int) ($R * 0.5), self::col($big, $fondo));
        }
        imagecopyresampled($img, $big, $cx - (int) ($diam / 2), $cy - (int) ($diam / 2), 0, 0, $diam, $diam, $R, $R);
        imagedestroy($big);

        $tamPct = max(18, (int) ($diam * 0.052));
        $radioTxt = $dona ? $diam * 0.375 : $diam * 0.34;
        foreach ($datos as $i => $d) {
            $pct = $d['valores'][0] / $suma * 100;
            if ($pct >= 5) {
                $tx = $cx + cos($centros[$i]) * $radioTxt;
                $ty = $cy + sin($centros[$i]) * $radioTxt;
                self::texto($img, $tamPct, $tx, $ty + $tamPct * 0.36, '#FFFFFF', self::formato($pct, $unidad === '' ? '' : '%'), 'bold', 'c');
            }
        }

        $n = count($datos);
        $xLey = $diam + 70;
        $anchoLey = $w - $xLey - 10;
        $tam = $n <= 5 ? 30 : ($n <= 8 ? 26 : 22);
        $paso = min($tam * 2.0, ($h - 40) / $n);
        $y = $h / 2 - ($n * $paso) / 2 + $paso * 0.62;
        foreach ($datos as $i => $d) {
            $col = self::colorDe($d['color'], $i, $tema);
            imagefilledrectangle($img, $xLey, (int) ($y - $tam * 0.78), (int) ($xLey + $tam * 0.85), (int) ($y + $tam * 0.07), self::col($img, $col));
            $val = self::formato($d['valores'][0] / $suma * 100, $unidad === '' ? '' : '%');
            $anchoVal = self::medir($val, $tam, 'bold');
            $lineas = self::envolver($d['etiqueta'], $tam, 'medium', $anchoLey - $tam * 1.25 - $anchoVal - 20);
            self::texto($img, $tam, $xLey + $tam * 1.25, $y, $tema['texto'], $lineas[0] . (count($lineas) > 1 ? '…' : ''), 'medium');
            self::texto($img, $tam, $xLey + $anchoLey, $y, $tema['texto'], $val, 'bold', 'r');
            $y += $paso;
        }
    }

    private static function apilada($img, array $g, array $tema, int $w, int $h): void
    {
        $datos = $g['datos'];
        $unidad = $g['unidad'];
        $suma = array_sum(array_map(static fn ($d) => $d['valores'][0], $datos));
        if ($suma <= 0) {
            return;
        }
        $x = 20;
        $anchoTotal = $w - 40;
        $altoBarra = (int) ($h * 0.26);
        $y = (int) ($h * 0.18);
        foreach ($datos as $i => $d) {
            $pct = $d['valores'][0] / $suma * 100;
            $largo = $anchoTotal * $pct / 100;
            $col = self::colorDe($d['color'], $i, $tema);
            imagefilledrectangle($img, (int) round($x), $y, (int) round($x + $largo) - 2, $y + $altoBarra, self::col($img, $col));
            if ($pct >= 7) {
                self::texto($img, 28, $x + $largo / 2, $y + $altoBarra / 2 + 10, '#FFFFFF', self::formato($pct, $unidad === '' ? '' : '%'), 'bold', 'c');
            }
            $x += $largo;
        }
        $tam = 24;
        $colW = $anchoTotal / min(3, count($datos));
        $yl = $y + $altoBarra + 70;
        foreach ($datos as $i => $d) {
            $c = $i % 3;
            $r = intdiv($i, 3);
            $lx = 20 + $c * $colW;
            $ly = $yl + $r * $tam * 2.1;
            imagefilledrectangle($img, (int) $lx, (int) ($ly - $tam * 0.78), (int) ($lx + $tam * 0.85), (int) ($ly + $tam * 0.07), self::col($img, self::colorDe($d['color'], $i, $tema)));
            $txt = $d['etiqueta'] . '  ' . self::formato($d['valores'][0] / $suma * 100, $unidad === '' ? '' : '%');
            $lineas = self::envolver($txt, $tam, 'medium', $colW - $tam * 1.6);
            self::texto($img, $tam, $lx + $tam * 1.3, $ly, $tema['texto'], $lineas[0], 'medium');
        }
    }
}
