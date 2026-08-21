<?php
/**
 * Renderer SVG de municipios desde tbl_ciudades_accion_unificada.
 * Fase C: drill-down territorial nacional → municipios (todos los deptos con geometría en BD).
 */
require_once __DIR__ . '/mapa_muni_helpers.php';

class MapaMunicipiosRender
{
    public static function getGeometriaByDepartamento($codigoDepartamento)
    {
        $dep = Util::normalizeCodigoDepartamento($codigoDepartamento);
        if ($dep === '') {
            return [];
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $q = "SELECT
                    id, codigo_departamento, codigo_muncipio, municipio, nombre_mapa,
                    d, transform, class, class2, viewbox_svg
                  FROM " . $db->getTable('tbl_ciudades_accion_unificada') . "
                  WHERE LPAD(CAST(codigo_departamento AS UNSIGNED), 2, '0') = :dep
                    AND d IS NOT NULL AND d != ''
                  ORDER BY municipio ASC";
            $stmt = $pdo->prepare($q);
            $stmt->execute([':dep' => $dep]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        } finally {
            $db->closeConect();
        }
    }

    /**
     * @param array $municipios filas geometría
     * @param array $coloresPorCodigo [codigo_muni => ['fill'=>..., 'empate'=>bool]]
     */
    public static function renderSvgHtml(array $municipios, array $coloresPorCodigo = [], $viewBoxFallback = '0 0 1500 1580')
    {
        $viewBox = $viewBoxFallback;
        foreach ($municipios as $m) {
            if (!empty($m['viewbox_svg'])) {
                $viewBox = trim($m['viewbox_svg']);
                break;
            }
        }

        $html = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="' . htmlspecialchars($viewBox, ENT_QUOTES, 'UTF-8') . '" '
            . 'width="100%" height="auto" preserveAspectRatio="xMidYMid meet" '
            . 'style="max-width:100%;display:block;" data-nivel="departamento">';
        $html .= '<style type="text/css">'
            . '.municipio-text{font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;'
            . 'font-size:12.5px;font-weight:500;pointer-events:none;fill:#1f2937;}'
            . '.mapaClick{cursor:pointer;}.mapaClick:hover{opacity:.85;}'
            . '</style>';

        foreach ($municipios as $value) {
            if (empty($value['d'])) {
                continue;
            }
            $codigo = Util::normalizeCodigoMunicipio($value['codigo_muncipio'] ?? '');
            $dep = Util::normalizeCodigoDepartamento($value['codigo_departamento'] ?? '');
            $nombre = mapa_muni_name($value);
            $label = mapa_muni_label($value);
            $meta = $coloresPorCodigo[$codigo] ?? $coloresPorCodigo[(string)intval($codigo)] ?? null;
            $fill = '#d9d9d9';
            if ($meta) {
                if (!empty($meta['empate'])) {
                    $fill = 'url(#rayasAzules)';
                } elseif (!empty($meta['fill'])) {
                    $fill = $meta['fill'];
                }
            }
            $gid = strtoupper(preg_replace('/[^A-Za-z0-9_\-]/', '_', (string)($value['id'] ?? $codigo)));

            $html .= '<g id="' . htmlspecialchars($gid, ENT_QUOTES, 'UTF-8') . '">';
            $html .= '<path class="municipios mapaClick"'
                . ' d="' . htmlspecialchars($value['d'], ENT_QUOTES, 'UTF-8') . '"'
                . ' fill="' . htmlspecialchars($fill, ENT_QUOTES, 'UTF-8') . '"'
                . ' stroke="#000000" stroke-width="0.3px" stroke-miterlimit="10"'
                . ' data-codigo="' . htmlspecialchars($codigo, ENT_QUOTES, 'UTF-8') . '"'
                . ' data-nombre="' . htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') . '"'
                . ' data-departamento="' . htmlspecialchars($dep, ENT_QUOTES, 'UTF-8') . '"'
                . ' data-nivel="municipio"'
                . ' title="' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '"'
                . '></path>';
            if (!empty($value['transform'])) {
                $html .= '<text transform="matrix(' . htmlspecialchars($value['transform'], ENT_QUOTES, 'UTF-8') . ')"'
                    . ' class="' . htmlspecialchars((string)($value['class2'] ?? ''), ENT_QUOTES, 'UTF-8') . ' municipio-text">'
                    . htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8')
                    . '</text>';
            }
            $html .= '</g>';
        }

        $html .= '</svg>';
        return $html;
    }

    public static function obtenerMapaSvg($rqst)
    {
        $dep = Util::normalizeCodigoDepartamento($rqst['departamento_click'] ?? $rqst['codigo_departamento'] ?? '');
        if ($dep === '') {
            return ['success' => false, 'message' => 'Departamento requerido', 'habilitado' => false];
        }
        if (!Util::isMapaMunicipalHabilitado($dep)) {
            return [
                'success' => false,
                'habilitado' => false,
                'message' => Util::mensajeMapaMunicipalNoDisponible($dep),
                'departamento' => $dep,
            ];
        }

        $municipios = self::getGeometriaByDepartamento($dep);
        if (empty($municipios)) {
            return [
                'success' => false,
                'habilitado' => true,
                'message' => 'Sin geometría municipal para este departamento',
                'departamento' => $dep,
            ];
        }

        $colores = [];
        $modo = strtolower(trim((string)($rqst['modo'] ?? 'sondeo')));
        if ($modo === 'cuestionario') {
            require_once __DIR__ . '/../classes/RespuestaCuestionario.php';
            $colores = RespuestaCuestionario::obtenerColoresMapaMunicipios($rqst);
        } else {
            require_once __DIR__ . '/../classes/Sondeo.php';
            $colores = Sondeo::obtenerColoresMapaMunicipios($rqst);
        }

        $fillMap = [];
        if (!empty($colores['ganadores']) && is_array($colores['ganadores'])) {
            $paleta = $colores['colores'] ?? [];
            foreach ($colores['ganadores'] as $cod => $g) {
                $c = Util::normalizeCodigoMunicipio($cod);
                if (!empty($g['empate'])) {
                    $fillMap[$c] = ['empate' => true, 'fill' => null];
                } else {
                    $gid = isset($g['ganador']) ? (int)$g['ganador'] : 0;
                    $fillMap[$c] = [
                        'empate' => false,
                        'fill' => $paleta[$gid] ?? '#d9d9d9',
                    ];
                }
            }
        }

        $svg = self::renderSvgHtml($municipios, $fillMap);

        return [
            'success' => true,
            'habilitado' => true,
            'departamento' => $dep,
            'total_municipios' => count($municipios),
            'svg' => $svg,
            'colores' => $colores['colores'] ?? [],
        ];
    }
}
