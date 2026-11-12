<?php

class InformeIA
{
    private const TAGS_PERMITIDAS = '<h1><h2><h3><h4><p><div><span><table><thead><tbody><tr><th><td>'
        . '<ul><ol><li><b><strong><i><em><small><blockquote><br><hr>';
    private const MAX_CONTENIDO = 60000;

    /**
     * Vocabulario cerrado de clases visuales que el HTML generado puede usar.
     * El CSS real de cada clase vive en informes_ia.js (vista) y en
     * ia_informe_pdf.php (PDF, subconjunto compatible con TCPDF) — nunca en
     * el HTML que llega aquí. Cualquier otra clase, o cualquier otro
     * atributo (style, id, on*, data-*), se descarta.
     */
    private const CLASES_PERMITIDAS = [
        's360-kpis', 's360-kpi', 's360-kpi-value', 's360-kpi-label', 's360-kpi-trend',
        's360-callout', 's360-callout-info', 's360-callout-warning', 's360-callout-danger', 's360-callout-success',
        's360-badge', 's360-badge-dato', 's360-badge-interpretacion',
        's360-quote', 's360-section', 's360-lead', 's360-table-highlight', 's360-cite',
    ];

    private const PALETA_GRAFICOS = [
        'azul' => '#2F67C4', 'verde' => '#12B981', 'rojo' => '#E5484D', 'naranja' => '#F5A623',
        'morado' => '#7C5CFA', 'turquesa' => '#14B8C6', 'amarillo' => '#EAB308', 'gris' => '#98A2B3',
        'azul-oscuro' => '#20427F',
    ];
    private const CICLO_COLORES = ['azul', 'naranja', 'verde', 'morado', 'turquesa', 'rojo', 'amarillo', 'azul-oscuro'];
    private const MAX_DATOS_GRAFICO = 20;

    private static function atributosDeEtiqueta(string $texto): array
    {
        $attrs = [];
        if (preg_match_all('/([a-zA-Z0-9_-]+)\s*=\s*"([^"]*)"/u', $texto, $m, PREG_SET_ORDER)) {
            foreach ($m as $par) {
                $attrs[strtolower($par[1])] = html_entity_decode($par[2], ENT_QUOTES, 'UTF-8');
            }
        }
        return $attrs;
    }

    private static function numeroGrafico(string $txt): ?float
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

    private static function esc(string $t): string
    {
        return htmlspecialchars($t, ENT_QUOTES, 'UTF-8');
    }

    private static function colorGrafico(?string $nombre, int $indice): string
    {
        $nombre = strtolower(trim((string) $nombre));
        if (!isset(self::PALETA_GRAFICOS[$nombre])) {
            $nombre = self::CICLO_COLORES[$indice % count(self::CICLO_COLORES)];
        }
        return self::PALETA_GRAFICOS[$nombre];
    }

    private static function formatoValor(float $v, string $unidad): string
    {
        $txt = number_format($v, abs($v - round($v)) < 0.05 ? 0 : 1, ',', '.');
        return $unidad === '' ? $txt : $txt . ' ' . $unidad;
    }

    private const MESES = ['', 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
    private const MAX_ANALISIS = 640;
    private static bool $ampliado = false;
    public const MARCA_PAGINA = '<!--S360PAGINA-->';
    public const MARCA_FIN = '<!--S360FIN-->';

    private static function imagenTorta(array $porciones, bool $dona): string
    {
        $tam = 420;
        $esc = 3;
        $S = $tam * $esc;
        $img = imagecreatetruecolor($S, $S);
        $blanco = imagecolorallocate($img, 255, 255, 255);
        imagefill($img, 0, 0, $blanco);
        $cx = $cy = intdiv($S, 2);
        $diametro = (int) round($S * 0.94);

        $acumulado = 0.0;
        $inicio = -90;
        foreach ($porciones as $p) {
            $acumulado += $p['pct'];
            $fin = -90 + (int) round($acumulado * 3.6);
            if ($fin > $inicio) {
                $hex = ltrim($p['color'], '#');
                $col = imagecolorallocate($img, hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2)));
                imagefilledarc($img, $cx, $cy, $diametro, $diametro, $inicio, $fin, $col, IMG_ARC_PIE);
                imagesetthickness($img, 5 * $esc);
                $rad = deg2rad($inicio);
                imageline($img, $cx, $cy, (int) round($cx + cos($rad) * $diametro / 2), (int) round($cy + sin($rad) * $diametro / 2), $blanco);
            }
            $inicio = $fin;
        }
        if ($dona) {
            imagefilledellipse($img, $cx, $cy, (int) round($diametro * 0.52), (int) round($diametro * 0.52), $blanco);
        }

        $final = imagecreatetruecolor($tam, $tam);
        imagefill($final, 0, 0, imagecolorallocate($final, 255, 255, 255));
        imagecopyresampled($final, $img, 0, 0, 0, 0, $tam, $tam, $S, $S);
        ob_start();
        imagepng($final, null, 9);
        $png = (string) ob_get_clean();
        imagedestroy($img);
        imagedestroy($final);
        return base64_encode($png);
    }

    private static function renderTorta(array $datos, string $unidad, string $html, string $tabla, string $celda, bool $dona, float $z): string
    {
        $suma = array_sum(array_map(static fn ($d) => $d['valores'][0], $datos));
        if ($suma <= 0) {
            return '';
        }
        $porciones = [];
        $leyenda = '';
        foreach ($datos as $i => $d) {
            $col = self::colorGrafico($d['color'], $i);
            $porciones[] = ['pct' => $d['valores'][0] / $suma * 100, 'color' => $col];
            $leyenda .= '<tr>'
                . '<td width="6%" bgcolor="' . $col . '" style="' . $celda . 'background-color:' . $col . ';font-size:6pt;">&nbsp;</td>'
                . '<td width="2%" style="' . $celda . '">&nbsp;</td>'
                . '<td width="62%" style="' . $celda . 'font-size:10pt;color:#101828;padding:0 1mm;">' . self::esc($d['etiqueta']) . '</td>'
                . '<td width="30%" align="right" style="' . $celda . 'font-size:10pt;color:#101828;padding:0 1mm;"><b>' . self::formatoValor($d['valores'][0], $unidad) . '</b></td>'
                . '</tr><tr><td colspan="4" style="' . $celda . 'font-size:' . round(3 * $z, 1) . 'pt;">&nbsp;</td></tr>';
        }
        $png = self::imagenTorta($porciones, $dona);
        $html .= '<table width="100%" cellpadding="0" cellspacing="0" border="0" style="' . $tabla . '"><tr>'
            . '<td width="46%" align="center" style="' . $celda . 'padding:1mm;"><img src="data:image/png;base64,' . $png . '" width="' . ($z > 1 ? 270 : 200) . '" height="' . ($z > 1 ? 270 : 200) . '" /></td>'
            . '<td width="54%" style="' . $celda . '"><table width="100%" cellpadding="0" cellspacing="0" border="0" style="' . $tabla . '">' . $leyenda . '</table></td>'
            . '</tr></table></div>';
        return $html;
    }

    private static function sanitizarFragmento(string $html): string
    {
        $graficos = [];
        $html = preg_replace_callback('#<grafico\b([^>]*)>(.*?)</grafico>#isu', function (array $m) use (&$graficos): string {
            $marca = '@@S360GRAFICO' . count($graficos) . '@@';
            $graficos[$marca] = self::renderGrafico($m[1], $m[2]);
            return $marca;
        }, $html) ?? $html;

        $html = strip_tags($html, self::TAGS_PERMITIDAS);
        $html = self::sanitizarAtributos($html);

        return $graficos ? strtr($html, $graficos) : $html;
    }

    private static function recortarTexto(string $texto, int $max): string
    {
        if (mb_strlen($texto) <= $max) {
            return $texto;
        }
        $corte = mb_substr($texto, 0, $max - 1);
        $espacio = mb_strrpos($corte, ' ');
        return rtrim($espacio !== false && $espacio > $max * 0.6 ? mb_substr($corte, 0, $espacio) : $corte, ' ,;:.') . '…';
    }

    private static function recortarAnalisis(string $html): string
    {
        if (mb_strlen(strip_tags($html)) <= self::MAX_ANALISIS) {
            return $html;
        }
        $piezas = preg_split('#(?<=</p>)|(?<=</li>)#', $html, -1, PREG_SPLIT_NO_EMPTY) ?: [$html];
        $salida = '';
        foreach ($piezas as $pieza) {
            $texto = trim(strip_tags($pieza));
            $acumulado = mb_strlen(strip_tags($salida));
            if ($salida !== '' && $acumulado + mb_strlen($texto) > self::MAX_ANALISIS) {
                break;
            }
            if ($salida === '' && mb_strlen($texto) > self::MAX_ANALISIS) {
                $corte = mb_substr($texto, 0, self::MAX_ANALISIS);
                $punto = mb_strrpos($corte, '. ');
                $corte = $punto !== false && $punto > 200 ? mb_substr($corte, 0, $punto + 1) : $corte;
                return '<p>' . htmlspecialchars($corte, ENT_QUOTES, 'UTF-8') . '</p>';
            }
            $salida .= $pieza;
        }
        foreach (['ul', 'ol'] as $lista) {
            $faltan = substr_count($salida, '<' . $lista . '>') - substr_count($salida, '</' . $lista . '>');
            $salida .= str_repeat('</' . $lista . '>', max(0, $faltan));
        }
        return $salida;
    }

    private static function renderPagina(string $atributosTxt, string $cuerpo): string
    {
        $attrs = self::atributosDeEtiqueta($atributosTxt);
        $titulo = mb_strtoupper(self::recortarTexto(trim($attrs['titulo'] ?? ''), 64));
        $pregunta = self::recortarTexto(trim($attrs['pregunta'] ?? ''), 170);

        $analisis = '';
        $cuerpo = preg_replace_callback('#<analisis\b[^>]*>(.*?)</analisis>#isu', function (array $m) use (&$analisis): string {
            $analisis .= $m[1];
            return '';
        }, $cuerpo) ?? $cuerpo;

        self::$ampliado = true;
        $graficos = trim(self::sanitizarFragmento($cuerpo));
        self::$ampliado = false;
        $analisis = self::recortarAnalisis(trim(self::sanitizarFragmento($analisis)));
        $analisis = str_replace(['<p>', '<li>'], ['<p style="font-size:11pt;line-height:1.15;color:#333F4F;text-align:justify;">', '<li style="font-size:11pt;line-height:1.15;color:#333F4F;">'], $analisis);
        if ($graficos === '' && $analisis === '') {
            return '';
        }

        $celda = 'border-top:none;border-right:none;border-bottom:none;border-left:none;';
        $html = self::MARCA_PAGINA . self::bandaTitulo($titulo, $pregunta);

        if ($graficos !== '' && $analisis !== '') {
            $html .= '<table nobr="true" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:0;margin-bottom:0;"><tr>'
                . '<td width="60%" valign="top" style="' . $celda . 'padding:3mm 2mm 0 0;"><div style="font-size:7pt;line-height:1;">&nbsp;</div>' . $graficos . '</td>'
                . '<td width="3%" style="' . $celda . '">&nbsp;</td>'
                . '<td width="0.6%" bgcolor="#20427F" style="' . $celda . 'background-color:#20427F;">&nbsp;</td>'
                . '<td width="3%" style="' . $celda . '">&nbsp;</td>'
                . '<td width="33.4%" valign="top" style="' . $celda . '"><div style="font-size:7pt;line-height:1;">&nbsp;</div>'
                . '<p style="font-family:montserratsemib,helvetica,sans-serif;font-size:13pt;color:#0B1F43;margin-bottom:2mm;"><b>ANÁLISIS</b></p>'
                . '<div style="font-size:11pt;color:#333F4F;line-height:1.15;text-align:justify;">' . $analisis . '</div>'
                . '</td></tr></table>';
        } else {
            $html .= '<div style="padding-top:3mm;">' . $graficos . $analisis . '</div>';
        }
        return $html . self::MARCA_FIN;
    }

    public static function bandaTitulo(string $titulo, string $pregunta = ''): string
    {
        $celda = 'border-top:none;border-right:none;border-bottom:none;border-left:none;';
        $html = '<table width="100%" cellpadding="12" cellspacing="0" border="0" style="margin-top:0;margin-bottom:0;">'
            . '<tr><td bgcolor="#0B1F43" style="' . $celda . 'background-color:#0B1F43;color:#ffffff;font-family:montserrat,helvetica,sans-serif;font-size:20pt;line-height:1.15;"><b>' . self::esc($titulo) . '</b></td></tr>';
        if ($pregunta !== '') {
            $html .= '<tr><td bgcolor="#2F67C4" style="' . $celda . 'background-color:#2F67C4;color:#ffffff;font-family:montserratsemib,helvetica,sans-serif;font-size:13pt;line-height:1.15;"><b>' . self::esc($pregunta) . '</b></td></tr>';
        }
        return $html . '</table>';
    }

    public static function mesAnio(string $fecha): string
    {
        $t = strtotime($fecha) ?: time();
        return self::MESES[(int) date('n', $t)] . ' de ' . date('Y', $t);
    }

    public static function marca(): array
    {
        $nombre = '';
        $logo = '';
        try {
            $db = new DbConection();
            $pdo = $db->openConect();
            $fila = $pdo->query("SELECT nombre_proyecto, logo FROM " . $db->getTable('tbl_configuracion') . " ORDER BY id LIMIT 1")->fetch(PDO::FETCH_ASSOC);
            $db->closeConect();
            $nombre = trim((string) ($fila['nombre_proyecto'] ?? ''));
            $ruta = realpath(__DIR__ . '/../../' . ltrim((string) ($fila['logo'] ?? ''), '/\\'));
            $raiz = realpath(__DIR__ . '/../../');
            if ($ruta !== false && $raiz !== false && strpos($ruta, $raiz) === 0 && is_file($ruta)) {
                $logo = $ruta;
            }
        } catch (Throwable $e) {
        }
        return ['nombre' => $nombre !== '' ? $nombre : 'Estadísticas 360', 'logo' => $logo];
    }

    private static function textoFicha($v): string
    {
        return trim(preg_replace('/\s+/u', ' ', strip_tags((string) $v)));
    }

    private static function numeroFicha($v): string
    {
        $n = (float) $v;
        return rtrim(rtrim(number_format($n, 2, ',', '.'), '0'), ',');
    }

    public static function filasFichaTecnica(int $fichaId): array
    {
        require_once __DIR__ . '/FichaTecnicaEncuesta.php';
        $filas = FichaTecnicaEncuesta::getAll(['id' => $fichaId])['output']['response'] ?? [];
        $f = is_array($filas) && $filas ? $filas[0] : null;
        if (!$f) {
            return [];
        }

        $universo = self::textoFicha($f['universo_representado'] ?? '');
        if ($universo !== '' && is_numeric($universo)) {
            $universo = number_format((float) $universo, 0, ',', '.') . ' ' . self::textoFicha($f['poblacion_objetivo'] ?? '');
        }
        $muestra = (int) ($f['tamano_muestra'] ?? 0);
        $diseno = trim(self::textoFicha($f['tipo_tamano_muestra_y_procedimiento_utilizado'] ?? '') . ' '
            . self::textoFicha($f['tipo_tamano_muestra_y_procedimiento_utilizado_descripcion'] ?? ''));
        $confianza = (float) ($f['nivel_confiabilidad_porcentaje'] ?? 0);
        $margen = (float) ($f['margen_error_porcentaje'] ?? 0);

        $mapa = [
            'Diseño y realización de la encuesta' => self::textoFicha($f['realizada_por_o_encomendada_por'] ?? ''),
            'Fuente de financiación' => self::textoFicha($f['fuente_financiacion'] ?? ''),
            'Universo en estudio' => trim($universo),
            'Tipo de estudio' => trim(self::textoFicha($f['tipo_encuesta'] ?? '') . ' ' . self::textoFicha($f['tipo_estudio_descripcion'] ?? '')),
            'Diseño de muestreo' => $diseno,
            'Tamaño de la muestra' => $muestra > 0 ? number_format($muestra, 0, ',', '.') . ' formularios' : '',
            'Procedimiento utilizado' => self::textoFicha($f['procedimiento_utilizado'] ?? ''),
            'Método de recolección' => self::textoFicha($f['metodo_recoleccion'] ?? ''),
            'Nivel de confianza' => $confianza > 0 ? self::numeroFicha($confianza) . ' %' : '',
            'Margen de error' => $margen > 0 ? '± ' . self::numeroFicha($margen) . ' %' : '',
            'Ámbito y periodo del trabajo de campo' => self::textoFicha($f['espacio_geografico_fecha_o_periodo_que_se_realizo'] ?? ''),
            'Propósito del estudio' => self::textoFicha($f['proposito_del_estudio'] ?? ''),
            'Temas tratados' => self::textoFicha($f['temas_concretos'] ?? ''),
            'Personas o instituciones indagadas' => self::textoFicha($f['candidatos_personas_instituciones_indagados'] ?? ''),
            'Supervisión, procesamiento e informe' => self::textoFicha($f['estadisticos_responsables'] ?? ''),
            'Declaración' => self::textoFicha($f['declaracion'] ?? ''),
            'Avisos' => self::textoFicha($f['avisos'] ?? ''),
        ];
        return array_filter($mapa, static fn ($v) => $v !== '');
    }

    public static function htmlFichaTecnica(int $fichaId): string
    {
        $filas = self::filasFichaTecnica($fichaId);
        if (!$filas) {
            return '';
        }
        $b = 'border-top:0.2mm solid #B8C4D9;border-right:0.2mm solid #B8C4D9;border-bottom:0.2mm solid #B8C4D9;border-left:0.2mm solid #B8C4D9;';
        $html = self::MARCA_PAGINA . self::bandaTitulo('FICHA TÉCNICA')
            . '<table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:0;margin-bottom:0;"><tr><td style="border-top:none;border-right:none;border-bottom:none;border-left:none;font-size:4pt;">&nbsp;</td></tr></table>'
            . '<table width="100%" cellpadding="8" cellspacing="0" border="0" style="margin-top:0;margin-bottom:0;">';
        foreach ($filas as $etiqueta => $valor) {
            $html .= '<tr>'
                . '<td width="30%" bgcolor="#EEF2F9" style="' . $b . 'background-color:#EEF2F9;font-size:10pt;line-height:1.15;color:#0B1F43;"><b>' . self::esc($etiqueta) . '</b></td>'
                . '<td width="70%" style="' . $b . 'font-size:10pt;line-height:1.15;color:#333F4F;">' . self::esc($valor) . '</td>'
                . '</tr>';
        }
        return $html . '</table>' . self::MARCA_FIN;
    }

    private static function renderGrafico(string $atributosTxt, string $cuerpo): string
    {
        $z = self::$ampliado ? 1.55 : 1.0;
        $cp = (int) round(($z - 1) * 6.5);
        $attrs = self::atributosDeEtiqueta($atributosTxt);
        $tipo = strtolower($attrs['tipo'] ?? 'barras');
        $titulo = mb_substr(trim($attrs['titulo'] ?? ''), 0, 140);
        $unidad = array_key_exists('unidad', $attrs) ? mb_substr(trim($attrs['unidad']), 0, 14) : '%';
        $series = array_values(array_filter(array_map('trim', explode('|', (string) ($attrs['series'] ?? ''))), 'strlen'));
        $series = array_slice($series, 0, 3);

        $datos = [];
        if (preg_match_all('/<dato\b([^>]*?)\/?>/iu', $cuerpo, $m)) {
            foreach ($m[1] as $a) {
                if (count($datos) >= self::MAX_DATOS_GRAFICO) {
                    break;
                }
                $d = self::atributosDeEtiqueta($a);
                $valores = [];
                foreach (explode('|', (string) ($d['valor'] ?? '')) as $v) {
                    $n = self::numeroGrafico($v);
                    if ($n !== null) {
                        $valores[] = $n;
                    }
                }
                $etiqueta = mb_substr(trim($d['etiqueta'] ?? ''), 0, 70);
                if ($etiqueta === '' || !$valores) {
                    continue;
                }
                $datos[] = ['etiqueta' => $etiqueta, 'valores' => $valores, 'color' => $d['color'] ?? ''];
            }
        }
        if (!$datos) {
            return '';
        }

        if (self::$ampliado) {
            $filas = 0;
            foreach ($datos as $d) {
                $filas += min(3, count($d['valores']));
            }
            $z = $filas <= 5 ? 2.0 : ($filas <= 8 ? 1.6 : ($filas <= 12 ? 1.25 : 1.0));
            $cp = (int) round(($z - 1) * 6.5);
        }

        $html = '<div style="margin:2mm 0 1mm 0;">';
        if ($titulo !== '') {
            $html .= '<p style="font-family:montserratsemib,helvetica,sans-serif;font-size:13pt;color:#0B1F43;margin-bottom:1mm;"><b>' . self::esc($titulo) . '</b></p>';
        }

        $celda = 'border-top:none;border-right:none;border-bottom:none;border-left:none;';
        $tabla = 'margin-top:0;margin-bottom:0;';

        if ($tipo === 'torta' || $tipo === 'dona') {
            return self::renderTorta($datos, $unidad, $html, $tabla, $celda, $tipo === 'dona', $z);
        }

        if ($tipo === 'apilada') {
            $suma = array_sum(array_map(static fn ($d) => $d['valores'][0], $datos));
            if ($suma <= 0) {
                return '';
            }
            $html .= '<table width="100%" cellpadding="0" cellspacing="0" border="0" style="' . $tabla . '"><tr>';
            foreach ($datos as $i => $d) {
                $pct = $d['valores'][0] / $suma * 100;
                if ($pct < 0.3) {
                    continue;
                }
                $col = self::colorGrafico($d['color'], $i);
                $html .= '<td width="' . round($pct, 2) . '%" bgcolor="' . $col . '" align="center" style="' . $celda
                    . 'background-color:' . $col . ';color:#ffffff;font-size:9pt;padding:2.2mm 0.5mm;">'
                    . ($pct >= 9 ? '<b>' . self::formatoValor($d['valores'][0], $unidad) . '</b>' : '&nbsp;') . '</td>';
            }
            $html .= '</tr></table>';
            $html .= '<table width="100%" cellpadding="1" cellspacing="0" border="0" style="margin-top:1mm;margin-bottom:0;">';
            foreach (array_chunk(array_keys($datos), 3) as $fila) {
                $html .= '<tr>';
                foreach ($fila as $i) {
                    $d = $datos[$i];
                    $col = self::colorGrafico($d['color'], $i);
                    $html .= '<td width="3%" bgcolor="' . $col . '" style="' . $celda . 'background-color:' . $col . ';font-size:6pt;">&nbsp;</td>'
                        . '<td width="30.3%" style="' . $celda . 'font-size:9pt;color:#344054;padding:0.5mm 2mm;">' . self::esc($d['etiqueta']) . ' <b>' . self::formatoValor($d['valores'][0], $unidad) . '</b></td>';
                }
                for ($k = count($fila); $k < 3; $k++) {
                    $html .= '<td width="3%" style="' . $celda . '">&nbsp;</td><td width="30.3%" style="' . $celda . '">&nbsp;</td>';
                }
                $html .= '</tr>';
            }
            $html .= '</table></div>';
            return $html;
        }

        $nSeries = max(1, min(3, max(array_map(static fn ($d) => count($d['valores']), $datos))));
        $maximo = 0.0;
        foreach ($datos as $d) {
            foreach ($d['valores'] as $v) {
                $maximo = max($maximo, $v);
            }
        }
        $escala = ($unidad === '%' && $maximo <= 100) ? 100.0 : max($maximo, 0.0001);

        if ($nSeries > 1 && $series) {
            $html .= '<table width="100%" cellpadding="1" cellspacing="0" border="0" style="' . $tabla . '"><tr><td width="26%" style="' . $celda . '">&nbsp;</td>';
            for ($s = 0; $s < $nSeries; $s++) {
                $col = self::colorGrafico('', $s);
                $html .= '<td width="3%" bgcolor="' . $col . '" style="' . $celda . 'background-color:' . $col . ';font-size:6pt;">&nbsp;</td><td style="' . $celda
                    . 'font-size:9pt;color:#344054;padding:0.5mm 2mm;">' . self::esc($series[$s] ?? ('Serie ' . ($s + 1))) . '</td>';
            }
            $html .= '</tr></table>';
        }

        $html .= '<table width="100%" cellpadding="' . $cp . '" cellspacing="0" border="0" style="' . $tabla . '">';
        foreach ($datos as $i => $d) {
            foreach (array_slice($d['valores'], 0, $nSeries) as $s => $v) {
                $color = $nSeries > 1 ? self::colorGrafico('', $s) : self::colorGrafico($d['color'], $i);
                $pct = min(100.0, $v / $escala * 100);
                if ($pct > 0 && $pct < 2) {
                    $pct = 2;
                }
                $barra = $pct >= 0.4
                    ? '<td width="' . round($pct, 2) . '%" bgcolor="' . $color . '" style="' . $celda . 'background-color:' . $color . ';font-size:' . round(8 * $z, 1) . 'pt;line-height:1.6;">&nbsp;</td>'
                    : '';
                $resto = 100 - $pct;
                $relleno = $resto > 0.1
                    ? '<td width="' . round($resto, 2) . '%" bgcolor="#EEF2F7" style="' . $celda . 'background-color:#EEF2F7;font-size:' . round(8 * $z, 1) . 'pt;line-height:1.6;">&nbsp;</td>'
                    : '';
                $html .= '<tr>'
                    . '<td width="27%" style="' . $celda . 'font-size:10pt;color:#101828;padding:' . round(0.8 * $z, 1) . 'mm 1.5mm;">' . ($s === 0 ? self::esc($d['etiqueta']) : '&nbsp;') . '</td>'
                    . '<td width="56%" style="' . $celda . 'padding:' . round(0.8 * $z, 1) . 'mm 0;"><table width="100%" cellpadding="0" cellspacing="0" border="0" style="' . $tabla . '"><tr>' . $barra . $relleno . '</tr></table></td>'
                    . '<td width="17%" align="right" style="' . $celda . 'font-size:10pt;color:#101828;padding:' . round(0.8 * $z, 1) . 'mm 1.5mm;"><b>' . self::formatoValor($v, $unidad) . '</b></td>'
                    . '</tr>';
            }
        }
        $html .= '</table></div>';
        return $html;
    }

    public static function sanitizarHtml(string $html): string
    {
        if (mb_strlen($html) > self::MAX_CONTENIDO) {
            $html = mb_substr($html, 0, self::MAX_CONTENIDO);
        }

        $paginas = [];
        $html = preg_replace_callback('#<pagina\b([^>]*)>(.*?)</pagina>#isu', function (array $m) use (&$paginas): string {
            $marca = '@@S360PAGINA' . count($paginas) . '@@';
            $paginas[$marca] = self::renderPagina($m[1], $m[2]);
            return $marca;
        }, $html) ?? $html;

        $html = self::sanitizarFragmento($html);

        return $paginas ? strtr($html, $paginas) : $html;
    }

    private static function sanitizarAtributos(string $html): string
    {
        $resultado = preg_replace_callback(
            '/<(\/?)([a-zA-Z0-9]+)((?:\s+[a-zA-Z-]+(?:\s*=\s*"[^"]*")?)*)\s*(\/?)>/',
            function (array $m): string {
                [, $cierre, $tag, $atributos, $autocierre] = $m;

                if ($cierre === '/' || trim($atributos) === '') {
                    return "<{$cierre}{$tag}{$autocierre}>";
                }

                if (preg_match('/class\s*=\s*"([^"]*)"/i', $atributos, $mc)) {
                    $clases = array_values(array_intersect(preg_split('/\s+/', trim($mc[1])), self::CLASES_PERMITIDAS));
                    if (!empty($clases)) {
                        return '<' . $tag . ' class="' . implode(' ', $clases) . '"' . $autocierre . '>';
                    }
                }

                return "<{$tag}{$autocierre}>";
            },
            $html
        );

        return $resultado ?? '';
    }

    public const MAX_FUENTE_DSL = 160000;

    public static function crear(int $userId, string $titulo, string $contenidoHtml, ?int $fichaTecnicaId = null, string $subtitulo = '', ?array $opciones = null): int
    {
        $titulo = mb_substr(trim($titulo), 0, 150);
        $subtitulo = mb_substr(trim(strip_tags($subtitulo)), 0, 200);
        $html = self::sanitizarHtml($contenidoHtml);
        $fuente = $opciones !== null ? mb_substr($contenidoHtml, 0, self::MAX_FUENTE_DSL) : null;

        $db = new DbConection();
        $pdo = $db->openConect();
        $q = "INSERT INTO " . $db->getTable('tbl_ia_informes') . " (tbl_usuario_id, titulo, subtitulo, ficha_tecnica_id, contenido_html, fuente_dsl, opciones_json, dt_create)
              VALUES (:usuario, :titulo, :subtitulo, :ficha, :html, :fuente, :opc, NOW())";
        $stmt = $pdo->prepare($q);
        $stmt->execute([':usuario' => $userId, ':titulo' => $titulo, ':subtitulo' => $subtitulo !== '' ? $subtitulo : null, ':ficha' => $fichaTecnicaId ?: null, ':html' => $html, ':fuente' => $fuente, ':opc' => $opciones !== null ? json_encode($opciones, JSON_UNESCAPED_UNICODE) : null]);
        $id = (int) $pdo->lastInsertId();
        $db->closeConect();
        return $id;
    }

    public static function actualizar(int $id, int $userId, array $cambios): bool
    {
        $actual = self::cargar($id);
        if ($actual === null) {
            return false;
        }
        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $pdo->beginTransaction();
            self::guardarVersion($pdo, $db, $id, $userId, $actual);
            $titulo = isset($cambios['titulo']) ? mb_substr(trim($cambios['titulo']), 0, 150) : $actual['titulo'];
            $subtitulo = array_key_exists('subtitulo', $cambios) ? mb_substr(trim(strip_tags((string) $cambios['subtitulo'])), 0, 200) : $actual['subtitulo'];
            $fuente = $actual['fuente_dsl'];
            $html = $actual['contenido_html'];
            if (isset($cambios['contenido'])) {
                if (self::esPresentacion($actual)) {
                    $fuente = mb_substr($cambios['contenido'], 0, self::MAX_FUENTE_DSL);
                }
                $html = self::sanitizarHtml($cambios['contenido']);
            }
            $opciones = isset($cambios['opciones']) ? json_encode($cambios['opciones'], JSON_UNESCAPED_UNICODE) : $actual['opciones_json'];
            $pdo->prepare("UPDATE " . $db->getTable('tbl_ia_informes') . " SET titulo = :t, subtitulo = :s, contenido_html = :h, fuente_dsl = :f, opciones_json = :o WHERE id = :id")
                ->execute([':t' => $titulo, ':s' => $subtitulo !== '' ? $subtitulo : null, ':h' => $html, ':f' => $fuente, ':o' => $opciones, ':id' => $id]);
            $pdo->commit();
            return true;
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            return false;
        } finally {
            $db->closeConect();
        }
    }

    private static function guardarVersion(PDO $pdo, DbConection $db, int $id, int $userId, array $actual): void
    {
        $pdo->prepare("INSERT INTO " . $db->getTable('tbl_ia_informe_versiones') . " (tbl_ia_informe_id, tbl_usuario_id, titulo, subtitulo, fuente_dsl, contenido_html, opciones_json, dt_create)
                       VALUES (:i, :u, :t, :s, :f, :h, :o, NOW())")
            ->execute([':i' => $id, ':u' => $userId, ':t' => $actual['titulo'], ':s' => $actual['subtitulo'], ':f' => $actual['fuente_dsl'], ':h' => $actual['contenido_html'], ':o' => $actual['opciones_json']]);
    }

    public static function versiones(int $informeId): array
    {
        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $stmt = $pdo->prepare("SELECT v.id, v.titulo, v.dt_create, LENGTH(COALESCE(NULLIF(v.fuente_dsl, ''), v.contenido_html)) AS caracteres
                                   FROM " . $db->getTable('tbl_ia_informe_versiones') . " v WHERE v.tbl_ia_informe_id = :i ORDER BY v.id DESC LIMIT 20");
            $stmt->execute([':i' => $informeId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } finally {
            $db->closeConect();
        }
    }

    public static function restaurarVersion(int $informeId, int $versionId, int $userId): bool
    {
        $actual = self::cargar($informeId);
        if ($actual === null) {
            return false;
        }
        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $stmt = $pdo->prepare("SELECT * FROM " . $db->getTable('tbl_ia_informe_versiones') . " WHERE id = :v AND tbl_ia_informe_id = :i");
            $stmt->execute([':v' => $versionId, ':i' => $informeId]);
            $v = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$v) {
                return false;
            }
            $pdo->beginTransaction();
            self::guardarVersion($pdo, $db, $informeId, $userId, $actual);
            $pdo->prepare("UPDATE " . $db->getTable('tbl_ia_informes') . " SET titulo = :t, subtitulo = :s, contenido_html = :h, fuente_dsl = :f, opciones_json = :o WHERE id = :id")
                ->execute([':t' => $v['titulo'], ':s' => $v['subtitulo'], ':h' => $v['contenido_html'], ':f' => $v['fuente_dsl'], ':o' => $v['opciones_json'], ':id' => $informeId]);
            $pdo->commit();
            return true;
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            return false;
        } finally {
            $db->closeConect();
        }
    }

    public static function listarParaAsistente(int $userId, bool $todos, string $texto = '', int $limite = 15): array
    {
        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $q = "SELECT i.id, i.titulo, i.subtitulo, i.ficha_tecnica_id, i.dt_create,
                         (i.fuente_dsl IS NOT NULL AND i.fuente_dsl <> '') AS es_presentacion,
                         ROUND((LENGTH(COALESCE(i.fuente_dsl, '')) - LENGTH(REPLACE(LOWER(COALESCE(i.fuente_dsl, '')), '<pagina', ''))) / 7) AS laminas,
                         (SELECT COUNT(*) FROM " . $db->getTable('tbl_ia_informe_versiones') . " v WHERE v.tbl_ia_informe_id = i.id) AS versiones
                  FROM " . $db->getTable('tbl_ia_informes') . " i WHERE 1 = 1";
            $params = [];
            if (!$todos) {
                $q .= " AND i.tbl_usuario_id = :u";
                $params[':u'] = $userId;
            }
            $n = 0;
            foreach (preg_split('/\s+/u', trim($texto), -1, PREG_SPLIT_NO_EMPTY) ?: [] as $palabra) {
                if (mb_strlen($palabra) < 3 || $n >= 6) {
                    continue;
                }
                $q .= " AND (i.titulo LIKE :p$n OR i.subtitulo LIKE :p$n)";
                $params[":p$n"] = '%' . addcslashes($palabra, '%_\\') . '%';
                $n++;
            }
            $q .= " ORDER BY i.dt_create DESC LIMIT " . max(1, min(30, $limite));
            $stmt = $pdo->prepare($q);
            $stmt->execute($params);
            $filas = [];
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $f) {
                $filas[] = [
                    'id' => (int) $f['id'],
                    'titulo' => $f['titulo'],
                    'subtitulo' => $f['subtitulo'],
                    'fecha' => $f['dt_create'],
                    'formato' => (int) $f['es_presentacion'] === 1 ? 'diapositivas' : 'documento',
                    'laminas' => (int) $f['es_presentacion'] === 1 ? (int) $f['laminas'] : null,
                    'ficha_tecnica_id' => $f['ficha_tecnica_id'] !== null ? (int) $f['ficha_tecnica_id'] : null,
                    'versiones_guardadas' => (int) $f['versiones'],
                ];
            }
            return $filas;
        } finally {
            $db->closeConect();
        }
    }

    public static function segmentosFuente(string $fuente): array
    {
        $trozos = preg_split('#(<pagina\b[^>]*>.*?</pagina>)#isu', $fuente, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY) ?: [];
        $segmentos = [];
        foreach ($trozos as $trozo) {
            $trozo = trim($trozo);
            if ($trozo === '') {
                continue;
            }
            $esPagina = preg_match('#^<pagina\b([^>]*)>#isu', $trozo, $m) === 1 && preg_match('#</pagina>\s*$#isu', $trozo) === 1;
            if ($esPagina && preg_match('#\btitulo\s*=\s*"([^"]*)"#iu', $m[1], $t)) {
                $titulo = html_entity_decode($t[1], ENT_QUOTES, 'UTF-8');
            } elseif (preg_match('#<h[1-4]\b[^>]*>(.*?)</h[1-4]>#isu', $trozo, $t)) {
                $titulo = trim(strip_tags($t[1]));
            } else {
                $titulo = mb_substr(trim(preg_replace('/\s+/u', ' ', strip_tags($trozo))), 0, 60);
            }
            $segmentos[] = ['n' => count($segmentos) + 1, 'tipo' => $esPagina ? 'pagina' : 'texto', 'titulo' => $titulo, 'contenido' => $trozo];
        }
        return $segmentos;
    }

    public static function indiceSegmentos(string $fuente): array
    {
        return array_map(static fn ($s) => ['n' => $s['n'], 'tipo' => $s['tipo'], 'titulo' => $s['titulo'], 'caracteres' => mb_strlen($s['contenido'])], self::segmentosFuente($fuente));
    }

    public static function aplicarOperaciones(string $fuente, array $operaciones): array
    {
        $segmentos = self::segmentosFuente($fuente);
        $total = count($segmentos);
        $reemplazos = [];
        $eliminar = [];
        $insertar = [];
        foreach ($operaciones as $i => $op) {
            $rotulo = 'Operación ' . ($i + 1);
            $accion = (string) ($op['accion'] ?? '');
            if (!isset($op['posicion']) || !is_numeric($op['posicion'])) {
                return ['ok' => false, 'mensaje' => $rotulo . ': falta posicion.'];
            }
            $pos = (int) $op['posicion'];
            $contenido = trim((string) ($op['contenido'] ?? ''));
            if (!in_array($accion, ['reemplazar', 'insertar_despues', 'eliminar'], true)) {
                return ['ok' => false, 'mensaje' => $rotulo . ': accion debe ser reemplazar, insertar_despues o eliminar.'];
            }
            $minimo = $accion === 'insertar_despues' ? 0 : 1;
            if ($pos < $minimo || $pos > $total) {
                return ['ok' => false, 'mensaje' => $rotulo . ': la posicion ' . $pos . ' no existe; el informe tiene ' . $total . ' secciones (usa el índice de leer_informe).'];
            }
            if ($accion !== 'eliminar') {
                if ($contenido === '') {
                    return ['ok' => false, 'mensaje' => $rotulo . ': falta el contenido.'];
                }
                if (preg_match_all('#<pagina\b#i', $contenido) !== preg_match_all('#</pagina>#i', $contenido)) {
                    return ['ok' => false, 'mensaje' => $rotulo . ': hay una etiqueta pagina sin cerrar.'];
                }
            }
            if ($accion === 'insertar_despues') {
                $insertar[$pos][] = $contenido;
                continue;
            }
            if (isset($reemplazos[$pos]) || isset($eliminar[$pos])) {
                return ['ok' => false, 'mensaje' => $rotulo . ': la posicion ' . $pos . ' ya tiene otra operación en esta llamada.'];
            }
            if ($accion === 'reemplazar') {
                $reemplazos[$pos] = $contenido;
            } else {
                $eliminar[$pos] = true;
            }
        }
        if (!$reemplazos && !$eliminar && !$insertar) {
            return ['ok' => false, 'mensaje' => 'No se indicó ninguna operación.'];
        }
        $salida = $insertar[0] ?? [];
        foreach ($segmentos as $s) {
            $n = $s['n'];
            if (!isset($eliminar[$n])) {
                $salida[] = $reemplazos[$n] ?? $s['contenido'];
            }
            foreach ($insertar[$n] ?? [] as $nuevo) {
                $salida[] = $nuevo;
            }
        }
        $resultado = implode("\n", $salida);
        if (mb_strlen($resultado) > self::MAX_FUENTE_DSL) {
            return ['ok' => false, 'mensaje' => 'El informe superaría el tamaño máximo permitido (' . self::MAX_FUENTE_DSL . ' caracteres).'];
        }
        return [
            'ok' => true,
            'fuente' => $resultado,
            'resumen' => ['reemplazadas' => count($reemplazos), 'eliminadas' => count($eliminar), 'insertadas' => array_sum(array_map('count', $insertar)), 'secciones_resultantes' => count(self::segmentosFuente($resultado))],
        ];
    }

    public static function esPresentacion(array $informe): bool
    {
        return trim((string) ($informe['fuente_dsl'] ?? '')) !== '';
    }

    public static function opciones(array $informe): array
    {
        $o = json_decode((string) ($informe['opciones_json'] ?? ''), true);
        return is_array($o) ? $o : [];
    }

    public static function formatos(array $informe): array
    {
        $f = self::opciones($informe)['formatos'] ?? null;
        $validos = is_array($f) ? array_values(array_intersect(['pdf', 'pptx', 'xlsx'], $f)) : [];
        return $validos ?: ['pdf', 'pptx', 'xlsx'];
    }

    public static function getAll($rqst)
    {
        $userId = SessionData::getUserId();
        $verTodos = SessionData::superAdministrador() || SessionData::hasPermission('ia.informes.manage');

        $db = new DbConection();
        $pdo = $db->openConect();

        $q = "SELECT i.id, i.titulo, i.tbl_usuario_id, i.dt_create, i.opciones_json,
                     CONCAT(u.nombre, ' ', u.apellido) AS autor,
                     (i.fuente_dsl IS NOT NULL AND i.fuente_dsl <> '') AS es_presentacion
              FROM " . $db->getTable('tbl_ia_informes') . " i
              LEFT JOIN " . $db->getTable('tbl_usuarios') . " u ON u.id = i.tbl_usuario_id";
        $params = [];

        if (!$verTodos) {
            $q .= " WHERE i.tbl_usuario_id = :usuario";
            $params[':usuario'] = $userId;
        }

        $q .= " ORDER BY i.dt_create DESC";

        try {
            $stmt = $pdo->prepare($q);
            $stmt->execute($params);
            $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $arrjson = ['output' => ['valid' => true, 'response' => $arr ?: []]];
        } catch (PDOException $e) {
            $arrjson = Util::error_general('Al obtener el listado de informes.');
        } finally {
            $db->closeConect();
        }
        return $arrjson;
    }

    public static function getById($rqst)
    {
        $id = isset($rqst['id']) ? intval($rqst['id']) : 0;
        if ($id <= 0) {
            return Util::error_missing_data();
        }

        $informe = self::cargar($id);
        if ($informe === null) {
            return Util::error_no_result();
        }
        if (!self::puedeAcceder($informe)) {
            return Util::error_general('No tienes permiso para ver este informe.');
        }

        $informe['ficha_html'] = !empty($informe['ficha_tecnica_id']) ? self::htmlFichaTecnica((int) $informe['ficha_tecnica_id']) : '';
        $informe['es_presentacion'] = self::esPresentacion($informe) ? 1 : 0;
        unset($informe['fuente_dsl']);

        return ['output' => ['valid' => true, 'response' => $informe]];
    }

    public static function delete($rqst)
    {
        $id = isset($rqst['id']) ? intval($rqst['id']) : 0;
        if ($id <= 0) {
            return Util::error_missing_data();
        }

        $informe = self::cargar($id);
        if ($informe === null) {
            return Util::error_no_result();
        }

        $puedeManage = SessionData::superAdministrador() || SessionData::hasPermission('ia.informes.manage');
        $esDueno = (int) $informe['tbl_usuario_id'] === (int) SessionData::getUserId();
        if (!$puedeManage && !($esDueno && SessionData::hasPermission('ia.informes.delete'))) {
            return Util::error_general('No tienes permiso para eliminar este informe.');
        }

        $db = new DbConection();
        $pdo = $db->openConect();
        $stmt = $pdo->prepare("DELETE FROM " . $db->getTable('tbl_ia_informes') . " WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $db->closeConect();

        return ['output' => ['valid' => true, 'response' => $id]];
    }

    public static function cargar(int $id): ?array
    {
        $db = new DbConection();
        $pdo = $db->openConect();
        $stmt = $pdo->prepare("SELECT * FROM " . $db->getTable('tbl_ia_informes') . " WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        $db->closeConect();
        return $fila ?: null;
    }

    public static function puedeAcceder(array $informe): bool
    {
        if (SessionData::superAdministrador() || SessionData::hasPermission('ia.informes.manage')) {
            return true;
        }
        return (int) $informe['tbl_usuario_id'] === (int) SessionData::getUserId();
    }
}
