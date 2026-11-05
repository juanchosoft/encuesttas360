<?php

class XlsxLector
{
    private const MAX_BYTES_DESCOMPRIMIDOS = 30 * 1024 * 1024;
    private const MAX_FILAS = 5000;
    private const MAX_COLUMNAS = 60;

    public static function disponible(): bool
    {
        return class_exists('ZipArchive') && function_exists('simplexml_load_string');
    }

    private static function indiceColumna(string $ref): int
    {
        preg_match('/^([A-Z]+)/', strtoupper($ref), $m);
        $letras = $m[1] ?? 'A';
        $n = 0;
        for ($i = 0, $l = strlen($letras); $i < $l; $i++) {
            $n = $n * 26 + (ord($letras[$i]) - 64);
        }
        return $n - 1;
    }

    private static function textoRico(SimpleXMLElement $nodo): string
    {
        if (isset($nodo->t)) {
            return (string)$nodo->t;
        }
        $txt = '';
        foreach ($nodo->r as $r) {
            $txt .= (string)$r->t;
        }
        return $txt;
    }

    private static function leerXml(ZipArchive $zip, string $ruta): ?SimpleXMLElement
    {
        $contenido = $zip->getFromName($ruta);
        if ($contenido === false) {
            return null;
        }
        $prev = libxml_use_internal_errors(true);
        $xml = simplexml_load_string($contenido, 'SimpleXMLElement', LIBXML_NONET | LIBXML_NOCDATA);
        libxml_clear_errors();
        libxml_use_internal_errors($prev);
        return $xml ?: null;
    }

    public static function leer(string $ruta): array
    {
        if (!self::disponible()) {
            throw new RuntimeException('El servidor no tiene habilitada la extensión ZIP necesaria para leer archivos Excel.');
        }
        $zip = new ZipArchive();
        if ($zip->open($ruta) !== true) {
            throw new RuntimeException('El archivo no es un Excel (.xlsx) válido.');
        }

        $total = 0;
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $total += (int)$zip->statIndex($i)['size'];
        }
        if ($total > self::MAX_BYTES_DESCOMPRIMIDOS) {
            $zip->close();
            throw new RuntimeException('El archivo Excel es demasiado grande.');
        }

        $libro = self::leerXml($zip, 'xl/workbook.xml');
        if (!$libro) {
            $zip->close();
            throw new RuntimeException('El archivo no parece un Excel (.xlsx). Si es un .xls antiguo, ábralo y guárdelo como .xlsx.');
        }

        $relsXml = self::leerXml($zip, 'xl/_rels/workbook.xml.rels');
        $destinos = [];
        if ($relsXml) {
            foreach ($relsXml->Relationship as $rel) {
                $destinos[(string)$rel['Id']] = (string)$rel['Target'];
            }
        }

        $compartidas = [];
        $ss = self::leerXml($zip, 'xl/sharedStrings.xml');
        if ($ss) {
            foreach ($ss->si as $si) {
                $compartidas[] = self::textoRico($si);
            }
        }

        $hojas = [];
        $rid = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships';
        foreach ($libro->sheets->sheet as $sheet) {
            $nombre = (string)$sheet['name'];
            $id = (string)$sheet->attributes($rid)['id'];
            $destino = $destinos[$id] ?? '';
            if ($destino === '') {
                continue;
            }
            $destino = ltrim($destino, '/');
            $ruta = strpos($destino, 'xl/') === 0 ? $destino : 'xl/' . $destino;
            $xml = self::leerXml($zip, $ruta);
            if (!$xml) {
                continue;
            }
            $filas = [];
            $nFila = 0;
            foreach ($xml->sheetData->row as $row) {
                $nFila = isset($row['r']) ? (int)$row['r'] : $nFila + 1;
                if ($nFila > self::MAX_FILAS) {
                    break;
                }
                $fila = [];
                foreach ($row->c as $c) {
                    $col = self::indiceColumna((string)$c['r']);
                    if ($col >= self::MAX_COLUMNAS) {
                        continue;
                    }
                    $tipo = (string)$c['t'];
                    if ($tipo === 's') {
                        $valor = $compartidas[(int)$c->v] ?? '';
                    } elseif ($tipo === 'inlineStr') {
                        $valor = self::textoRico($c->is);
                    } elseif ($tipo === 'b') {
                        $valor = ((string)$c->v) === '1' ? 'Sí' : 'No';
                    } else {
                        $valor = (string)$c->v;
                        if ($tipo === '' || $tipo === 'n') {
                            if ($valor !== '' && is_numeric($valor)) {
                                $f = (float)$valor;
                                $valor = ($f == floor($f) && abs($f) < 1e15) ? (string)(int)$f : rtrim(rtrim(sprintf('%.10F', $f), '0'), '.');
                            }
                        }
                    }
                    $fila[$col] = trim((string)$valor);
                }
                if (!empty($fila)) {
                    $max = max(array_keys($fila));
                    $completa = array_fill(0, $max + 1, '');
                    foreach ($fila as $k => $v) {
                        $completa[$k] = $v;
                    }
                    $filas[$nFila] = $completa;
                }
            }
            $hojas[$nombre] = $filas;
        }
        $zip->close();
        return $hojas;
    }
}
