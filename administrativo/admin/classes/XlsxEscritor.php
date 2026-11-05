<?php

class XlsxEscritor
{
    public const ESTILO_NORMAL = 0;
    public const ESTILO_ENCABEZADO = 1;
    public const ESTILO_TEXTO = 2;
    public const ESTILO_NOTA = 3;
    public const ESTILO_TITULO = 4;
    public const ESTILO_SECCION = 5;
    public const ESTILO_ENCABEZADO_OPCIONAL = 6;

    private $hojas = [];

    public static function disponible(): bool
    {
        return class_exists('ZipArchive');
    }

    public function agregarHoja(string $nombre, array $filas, array $opciones = []): void
    {
        $nombre = preg_replace('/[\\\\\/\?\*\[\]:]/', ' ', $nombre);
        $this->hojas[] = ['nombre' => mb_substr($nombre, 0, 31), 'filas' => $filas, 'opciones' => $opciones];
    }

    public static function letraColumna(int $indice): string
    {
        $letra = '';
        $n = $indice + 1;
        while ($n > 0) {
            $m = ($n - 1) % 26;
            $letra = chr(65 + $m) . $letra;
            $n = intdiv($n - 1, 26);
        }
        return $letra;
    }

    private static function xml(string $texto): string
    {
        $texto = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $texto);
        return htmlspecialchars($texto, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    private function xmlHoja(array $hoja): string
    {
        $op = $hoja['opciones'];
        $x = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $x .= '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">';
        $x .= '<sheetViews><sheetView workbookViewId="0"' . (!empty($op['mostrar_cuadricula']) || !isset($op['mostrar_cuadricula']) ? '' : ' showGridLines="0"') . '>';
        if (!empty($op['congelar_fila'])) {
            $x .= '<pane ySplit="1" topLeftCell="A2" activePane="bottomLeft" state="frozen"/>';
        }
        $x .= '</sheetView></sheetViews><sheetFormatPr defaultRowHeight="15"/>';

        if (!empty($op['anchos'])) {
            $x .= '<cols>';
            foreach ($op['anchos'] as $i => $ancho) {
                $c = (int)$i + 1;
                $x .= '<col min="' . $c . '" max="' . $c . '" width="' . (float)$ancho . '" customWidth="1"/>';
            }
            $x .= '</cols>';
        }

        $x .= '<sheetData>';
        foreach ($hoja['filas'] as $r => $fila) {
            $nFila = $r + 1;
            $alto = isset($op['altos'][$r]) ? ' ht="' . (float)$op['altos'][$r] . '" customHeight="1"' : '';
            $x .= '<row r="' . $nFila . '"' . $alto . '>';
            foreach ($fila as $c => $celda) {
                $estilo = 0;
                $valor = $celda;
                if (is_array($celda)) {
                    $valor = $celda['v'] ?? '';
                    $estilo = (int)($celda['s'] ?? 0);
                }
                if ($valor === null || $valor === '') {
                    if ($estilo > 0) {
                        $x .= '<c r="' . self::letraColumna($c) . $nFila . '" s="' . $estilo . '"/>';
                    }
                    continue;
                }
                $ref = self::letraColumna($c) . $nFila;
                $s = $estilo > 0 ? ' s="' . $estilo . '"' : '';
                if (is_int($valor) || is_float($valor)) {
                    $x .= '<c r="' . $ref . '"' . $s . '><v>' . $valor . '</v></c>';
                } else {
                    $x .= '<c r="' . $ref . '"' . $s . ' t="inlineStr"><is><t xml:space="preserve">' . self::xml((string)$valor) . '</t></is></c>';
                }
            }
            $x .= '</row>';
        }
        $x .= '</sheetData>';

        if (!empty($op['combinar'])) {
            $x .= '<mergeCells count="' . count($op['combinar']) . '">';
            foreach ($op['combinar'] as $rango) {
                $x .= '<mergeCell ref="' . self::xml($rango) . '"/>';
            }
            $x .= '</mergeCells>';
        }

        if (!empty($op['validaciones'])) {
            $x .= '<dataValidations count="' . count($op['validaciones']) . '">';
            foreach ($op['validaciones'] as $v) {
                $formula = $v['formula'] ?? ('"' . str_replace('"', '', implode(',', $v['lista'])) . '"');
                $x .= '<dataValidation type="list" allowBlank="1" showInputMessage="1" showErrorMessage="1" errorStyle="warning"'
                    . ' errorTitle="Valor no reconocido" error="Elija un valor de la lista."'
                    . ' sqref="' . self::xml($v['rango']) . '"><formula1>' . self::xml($formula) . '</formula1></dataValidation>';
            }
            $x .= '</dataValidations>';
        }

        $x .= '<pageMargins left="0.7" right="0.7" top="0.75" bottom="0.75" header="0.3" footer="0.3"/>';
        $x .= '</worksheet>';
        return $x;
    }

    private function xmlEstilos(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<fonts count="6">'
            . '<font><sz val="11"/><name val="Calibri"/></font>'
            . '<font><b/><sz val="11"/><color rgb="FFFFFFFF"/><name val="Calibri"/></font>'
            . '<font><i/><sz val="10"/><color rgb="FF666666"/><name val="Calibri"/></font>'
            . '<font><b/><sz val="16"/><color rgb="FF20427F"/><name val="Calibri"/></font>'
            . '<font><b/><sz val="12"/><color rgb="FF20427F"/><name val="Calibri"/></font>'
            . '<font><sz val="11"/><color rgb="FF222222"/><name val="Calibri"/></font>'
            . '</fonts>'
            . '<fills count="4">'
            . '<fill><patternFill patternType="none"/></fill>'
            . '<fill><patternFill patternType="gray125"/></fill>'
            . '<fill><patternFill patternType="solid"><fgColor rgb="FF20427F"/><bgColor indexed="64"/></patternFill></fill>'
            . '<fill><patternFill patternType="solid"><fgColor rgb="FF7A8CA8"/><bgColor indexed="64"/></patternFill></fill>'
            . '</fills>'
            . '<borders count="2">'
            . '<border><left/><right/><top/><bottom/><diagonal/></border>'
            . '<border><left style="thin"><color rgb="FFBBBBBB"/></left><right style="thin"><color rgb="FFBBBBBB"/></right><top style="thin"><color rgb="FFBBBBBB"/></top><bottom style="thin"><color rgb="FFBBBBBB"/></bottom><diagonal/></border>'
            . '</borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="7">'
            . '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
            . '<xf numFmtId="0" fontId="1" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment vertical="center" wrapText="1"/></xf>'
            . '<xf numFmtId="49" fontId="5" fillId="0" borderId="0" xfId="0" applyNumberFormat="1" applyFont="1" applyAlignment="1"><alignment vertical="top" wrapText="1"/></xf>'
            . '<xf numFmtId="0" fontId="2" fillId="0" borderId="0" xfId="0" applyFont="1" applyAlignment="1"><alignment vertical="top" wrapText="1"/></xf>'
            . '<xf numFmtId="0" fontId="3" fillId="0" borderId="0" xfId="0" applyFont="1"/>'
            . '<xf numFmtId="0" fontId="4" fillId="0" borderId="0" xfId="0" applyFont="1"/>'
            . '<xf numFmtId="0" fontId="1" fillId="3" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment vertical="center" wrapText="1"/></xf>'
            . '</cellXfs>'
            . '<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
            . '</styleSheet>';
    }

    public function contenido(): string
    {
        if (!self::disponible()) {
            throw new RuntimeException('El servidor no tiene habilitada la extensión ZIP necesaria para generar archivos Excel.');
        }
        if (empty($this->hojas)) {
            throw new RuntimeException('No hay hojas para generar.');
        }

        $tmp = tempnam(sys_get_temp_dir(), 'xlsx');
        $zip = new ZipArchive();
        if ($zip->open($tmp, ZipArchive::OVERWRITE) !== true) {
            @unlink($tmp);
            throw new RuntimeException('No se pudo crear el archivo Excel.');
        }

        $tipos = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>';
        $libro = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets>';
        $rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">';

        foreach ($this->hojas as $i => $hoja) {
            $n = $i + 1;
            $tipos .= '<Override PartName="/xl/worksheets/sheet' . $n . '.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>';
            $libro .= '<sheet name="' . self::xml($hoja['nombre']) . '" sheetId="' . $n . '" r:id="rId' . $n . '"/>';
            $rels .= '<Relationship Id="rId' . $n . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet' . $n . '.xml"/>';
            $zip->addFromString('xl/worksheets/sheet' . $n . '.xml', $this->xmlHoja($hoja));
        }
        $rels .= '<Relationship Id="rId' . (count($this->hojas) + 1) . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/></Relationships>';
        $tipos .= '</Types>';
        $libro .= '</sheets></workbook>';

        $zip->addFromString('[Content_Types].xml', $tipos);
        $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>');
        $zip->addFromString('xl/workbook.xml', $libro);
        $zip->addFromString('xl/_rels/workbook.xml.rels', $rels);
        $zip->addFromString('xl/styles.xml', $this->xmlEstilos());
        $zip->close();

        $bytes = file_get_contents($tmp);
        @unlink($tmp);
        return $bytes;
    }
}
