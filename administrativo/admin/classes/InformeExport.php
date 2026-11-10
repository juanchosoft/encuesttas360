<?php

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/InformeLaminas.php';
require_once __DIR__ . '/InformeGraficos.php';

use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\DocumentLayout;
use PhpOffice\PhpPresentation\IOFactory as PptIO;
use PhpOffice\PhpPresentation\Shape\RichText;
use PhpOffice\PhpPresentation\Shape\RichText\Paragraph;
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Fill;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory as XlsIO;
use PhpOffice\PhpSpreadsheet\Style\Alignment as XAlign;
use PhpOffice\PhpSpreadsheet\Style\Fill as XFill;

class InformeExport
{
    private const PT_MM = 0.352778;
    private const PX_MM = 3.7795;
    private const FAMILIA_PDF = ['regular' => 'poppins', 'medium' => 'poppinsmedium', 'semibold' => 'poppinssemib', 'bold' => 'poppinsb', 'italic' => 'poppinsi'];
    private const FAMILIA_PPT = ['regular' => ['Poppins', false, false], 'medium' => ['Poppins Medium', false, false], 'semibold' => ['Poppins SemiBold', false, false], 'bold' => ['Poppins', true, false], 'italic' => ['Poppins', false, true]];

    private static function rgb(string $hex): array
    {
        $h = ltrim($hex, '#');
        return [hexdec(substr($h, 0, 2)), hexdec(substr($h, 2, 2)), hexdec(substr($h, 4, 2))];
    }

    private static function bytesImagen(array $prim): string
    {
        return $prim['datos'] ?? (string) file_get_contents($prim['ruta']);
    }

    public static function pdf(array $laminas, array $spec): string
    {
        require_once __DIR__ . '/../include/TCPDF-main/tcpdf.php';
        $pdf = new TCPDF('L', 'mm', [InformeLaminas::W, InformeLaminas::H], true, 'UTF-8', false);
        $dir = __DIR__ . '/../include/TCPDF-main/fonts/';
        foreach (self::FAMILIA_PDF as $peso => $familia) {
            $pdf->AddFont($familia, '', $dir . $familia . '.php');
        }
        $pdf->SetCreator($spec['realizado_por']);
        $pdf->SetAuthor($spec['realizado_por']);
        $pdf->SetTitle($spec['titulo']);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetAutoPageBreak(false, 0);
        $pdf->setCellPaddings(0, 0, 0, 0);
        $pdf->setJPEGQuality(88);

        foreach ($laminas as $l) {
            $pdf->AddPage();
            if ($l['fondo']) {
                self::pdfRect($pdf, ['x' => 0, 'y' => 0, 'w' => InformeLaminas::W, 'h' => InformeLaminas::H, 'fill' => $l['fondo'], 'borde' => null]);
            }
            foreach ($l['prims'] as $p) {
                if ($p['t'] === 'rect') {
                    self::pdfRect($pdf, $p);
                } elseif ($p['t'] === 'img') {
                    $datos = $p['datos'] ?? null;
                    if ($datos !== null) {
                        $pdf->Image('@' . $datos, $p['x'], $p['y'], $p['w'], $p['h'], 'PNG', '', '', false, 300);
                    } else {
                        $pdf->Image($p['ruta'], $p['x'], $p['y'], $p['w'], $p['h'], '', '', '', false, 300);
                    }
                } else {
                    self::pdfTexto($pdf, $p);
                }
            }
        }
        return $pdf->Output('informe.pdf', 'S');
    }

    private static function pdfRect(TCPDF $pdf, array $p): void
    {
        [$r, $g, $b] = self::rgb($p['fill']);
        $pdf->SetFillColor($r, $g, $b);
        if (!empty($p['borde'])) {
            [$br, $bg, $bb] = self::rgb($p['borde']);
            $pdf->SetDrawColor($br, $bg, $bb);
            $pdf->SetLineWidth(0.2);
            $pdf->Rect($p['x'], $p['y'], $p['w'], $p['h'], 'DF');
        } else {
            $pdf->Rect($p['x'], $p['y'], $p['w'], $p['h'], 'F', ['all' => 0]);
        }
    }

    private static function pdfTexto(TCPDF $pdf, array $p): void
    {
        [$r, $g, $b] = self::rgb($p['color']);
        $pdf->SetTextColor($r, $g, $b);
        $pdf->setCellHeightRatio($p['lh']);
        $alinea = $p['align'];
        $y = $p['y'];
        $paras = $p['paras'];
        if ($p['valign'] === 'M') {
            $total = 0;
            foreach ($paras as $para) {
                $txt = is_array($para) ? $para['t'] : $para;
                $pdf->SetFont(self::FAMILIA_PDF[$p['peso']], '', $p['size']);
                $total += $pdf->getStringHeight($p['w'], $txt);
            }
            $y += max(0, ($p['h'] - $total) / 2);
        }
        foreach ($paras as $i => $para) {
            $txt = is_array($para) ? $para['t'] : $para;
            $peso = is_array($para) ? ($para['peso'] ?? $p['peso']) : $p['peso'];
            $pdf->SetFont(self::FAMILIA_PDF[$peso], '', $p['size']);
            $pdf->MultiCell($p['w'], 0, $txt, 0, $alinea, false, 1, $p['x'], $y, true, 0, false, true, 0, 'T', false);
            $y = $pdf->GetY() + ($i < count($paras) - 1 ? $p['psp'] : 0);
        }
    }

    public static function pptx(array $laminas, array $spec): string
    {
        $ppt = new PhpPresentation();
        $ppt->getLayout()->setCX(12192000)->setCY(6858000);
        $ppt->getDocumentProperties()->setTitle($spec['titulo'])->setCreator($spec['realizado_por']);
        $temporales = [];

        foreach ($laminas as $i => $l) {
            $slide = $i === 0 ? $ppt->getActiveSlide() : $ppt->createSlide();
            if ($l['fondo']) {
                self::pptRect($slide, ['x' => 0, 'y' => 0, 'w' => InformeLaminas::W, 'h' => InformeLaminas::H, 'fill' => $l['fondo'], 'borde' => null]);
            }
            foreach ($l['prims'] as $p) {
                if ($p['t'] === 'rect') {
                    self::pptRect($slide, $p);
                } elseif ($p['t'] === 'img') {
                    $ruta = $p['ruta'];
                    if (($p['datos'] ?? null) !== null) {
                        $ruta = tempnam(sys_get_temp_dir(), 's360') . '.png';
                        file_put_contents($ruta, $p['datos']);
                        $temporales[] = $ruta;
                    }
                    $d = $slide->createDrawingShape();
                    $d->setPath($ruta, false)->setResizeProportional(false)
                        ->setWidth((int) round($p['w'] * self::PX_MM))->setHeight((int) round($p['h'] * self::PX_MM))
                        ->setOffsetX((int) round($p['x'] * self::PX_MM))->setOffsetY((int) round($p['y'] * self::PX_MM));
                } else {
                    self::pptTexto($slide, $p);
                }
            }
        }
        $archivo = tempnam(sys_get_temp_dir(), 's360p');
        PptIO::createWriter($ppt, 'PowerPoint2007')->save($archivo);
        $bytes = (string) file_get_contents($archivo);
        @unlink($archivo);
        foreach ($temporales as $t) {
            @unlink($t);
        }
        return $bytes;
    }

    private static function pptColor(string $hex): Color
    {
        return new Color('FF' . strtoupper(ltrim($hex, '#')));
    }

    private static function pptRect($slide, array $p): void
    {
        $s = $slide->createAutoShape();
        $s->setType(\PhpOffice\PhpPresentation\Shape\AutoShape::TYPE_RECTANGLE)
            ->setWidth((int) round($p['w'] * self::PX_MM))->setHeight((int) round($p['h'] * self::PX_MM))
            ->setOffsetX((int) round($p['x'] * self::PX_MM))->setOffsetY((int) round($p['y'] * self::PX_MM));
        $s->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(self::pptColor($p['fill']));
        if (!empty($p['borde'])) {
            $s->getBorder()->setLineStyle(\PhpOffice\PhpPresentation\Style\Border::LINE_SINGLE)->setLineWidth(0.75)->setColor(self::pptColor($p['borde']));
        } else {
            $s->getBorder()->setLineStyle(\PhpOffice\PhpPresentation\Style\Border::LINE_NONE);
        }
    }

    private static function pptTexto($slide, array $p): void
    {
        $shape = $slide->createRichTextShape();
        $shape->setWidth((int) round($p['w'] * self::PX_MM))->setHeight((int) round($p['h'] * self::PX_MM))
            ->setOffsetX((int) round($p['x'] * self::PX_MM))->setOffsetY((int) round($p['y'] * self::PX_MM));
        $shape->setInsetLeft(0)->setInsetRight(0)->setInsetTop(0)->setInsetBottom(0);
        $shape->setWrap(RichText::WRAP_SQUARE);
        $h = ['L' => Alignment::HORIZONTAL_LEFT, 'C' => Alignment::HORIZONTAL_CENTER, 'R' => Alignment::HORIZONTAL_RIGHT][$p['align']] ?? Alignment::HORIZONTAL_LEFT;
        $v = $p['valign'] === 'M' ? Alignment::VERTICAL_CENTER : Alignment::VERTICAL_TOP;
        foreach ($p['paras'] as $i => $para) {
            $txt = is_array($para) ? $para['t'] : $para;
            $peso = is_array($para) ? ($para['peso'] ?? $p['peso']) : $p['peso'];
            $parrafo = $i === 0 ? $shape->getActiveParagraph() : $shape->createParagraph();
            $parrafo->getAlignment()->setHorizontal($h)->setVertical($v);
            $parrafo->setLineSpacingMode(Paragraph::LINE_SPACING_MODE_POINT)->setLineSpacing((int) round($p['size'] * $p['lh']));
            if ($i < count($p['paras']) - 1 && $p['psp'] > 0) {
                $parrafo->setSpacingAfter($p['psp'] * 2.8346);
            }
            [$nombre, $negrita, $cursiva] = self::FAMILIA_PPT[$peso];
            $run = $parrafo->createTextRun($txt);
            $run->getFont()->setName($nombre)->setSize($p['size'])->setBold($negrita)->setItalic($cursiva)->setColor(self::pptColor($p['color']));
        }
    }

    public static function xlsx(array $spec): string
    {
        $libro = new Spreadsheet();
        $libro->getDefaultStyle()->getFont()->setName('Poppins')->setSize(10);
        $t = $spec['tema'];
        $hoja = $libro->getActiveSheet();
        $hoja->setTitle('Resumen');
        $hoja->setCellValue('A1', $spec['titulo']);
        $hoja->getStyle('A1')->getFont()->setBold(true)->setSize(16)->getColor()->setRGB(ltrim($t['primario'], '#'));
        $fila = 2;
        foreach ($spec['subtitulos'] as $s) {
            $hoja->setCellValue('A' . $fila++, $s);
        }
        if ($spec['fecha_texto'] !== '') {
            $hoja->setCellValue('A' . $fila++, $spec['fecha_texto']);
        }
        $hoja->setCellValue('A' . $fila++, 'Realizado por: ' . $spec['realizado_por']);
        $fila++;
        foreach ($spec['paginas'] as $p) {
            if ($p['tipo'] !== 'ficha') {
                continue;
            }
            $hoja->setCellValue('A' . $fila, 'Ficha técnica');
            $hoja->getStyle('A' . $fila)->getFont()->setBold(true)->setSize(12);
            $fila++;
            foreach ($p['filas'] as $et => $val) {
                $hoja->setCellValue('A' . $fila, $et);
                $hoja->setCellValue('B' . $fila, $val);
                $hoja->getStyle('A' . $fila)->getFont()->setBold(true);
                $hoja->getStyle('B' . $fila)->getAlignment()->setWrapText(true)->setVertical(XAlign::VERTICAL_TOP);
                $hoja->getStyle('A' . $fila)->getAlignment()->setVertical(XAlign::VERTICAL_TOP);
                $fila++;
            }
        }
        $hoja->getColumnDimension('A')->setWidth(42);
        $hoja->getColumnDimension('B')->setWidth(90);

        $usados = ['Resumen' => true];
        $n = 0;
        foreach ($spec['paginas'] as $p) {
            if ($p['tipo'] === 'resultado') {
                foreach ($p['graficos'] as $gi => $g) {
                    $n++;
                    $nombre = self::nombreHoja($n . ' ' . ($g['titulo'] !== '' ? $g['titulo'] : $p['titulo']), $usados);
                    self::hojaGrafico($libro, $nombre, $p, $g, $t);
                }
                foreach ($p['bloques'] as $b) {
                    if ($b['t'] === 'tabla') {
                        $n++;
                        self::hojaTabla($libro, self::nombreHoja($n . ' ' . $p['titulo'], $usados), $b, $t, $p['pregunta']);
                    }
                }
            } elseif ($p['tipo'] === 'bloques') {
                foreach ($p['bloques'] as $b) {
                    if ($b['t'] === 'tabla') {
                        $n++;
                        self::hojaTabla($libro, self::nombreHoja($n . ' ' . $p['titulo'], $usados), $b, $t, '');
                    }
                }
            }
        }
        $libro->setActiveSheetIndex(0);
        $archivo = tempnam(sys_get_temp_dir(), 's360x');
        $writer = XlsIO::createWriter($libro, 'Xlsx');
        $writer->setIncludeCharts(true);
        $writer->save($archivo);
        $bytes = (string) file_get_contents($archivo);
        @unlink($archivo);
        return $bytes;
    }

    private static function nombreHoja(string $txt, array &$usados): string
    {
        $txt = trim(preg_replace('/[\[\]\:\*\?\/\\\\]+/u', ' ', $txt));
        $base = mb_substr($txt, 0, 28);
        $nombre = $base;
        $i = 2;
        while (isset($usados[mb_strtolower($nombre)])) {
            $nombre = mb_substr($base, 0, 26) . ' ' . $i++;
        }
        $usados[mb_strtolower($nombre)] = true;
        return $nombre;
    }

    private static function estiloEncabezado($hoja, string $rango, array $t): void
    {
        $hoja->getStyle($rango)->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $hoja->getStyle($rango)->getFill()->setFillType(XFill::FILL_SOLID)->getStartColor()->setRGB(ltrim($t['primario'], '#'));
    }

    private static function hojaGrafico(Spreadsheet $libro, string $nombre, array $p, array $g, array $t): void
    {
        $hoja = $libro->createSheet();
        $hoja->setTitle($nombre);
        $hoja->setCellValue('A1', $p['pregunta'] !== '' ? $p['pregunta'] : $p['titulo']);
        $hoja->getStyle('A1')->getFont()->setBold(true)->setSize(12);
        $nSeries = count($g['datos'][0]['valores']);
        $hoja->setCellValue('A3', 'Opción');
        for ($s = 0; $s < $nSeries; $s++) {
            $hoja->setCellValue([2 + $s, 3], $g['series'][$s] ?? ($nSeries > 1 ? 'Serie ' . ($s + 1) : ($g['unidad'] === '%' ? 'Porcentaje' : 'Valor')));
        }
        $fila = 4;
        foreach ($g['datos'] as $d) {
            $hoja->setCellValue('A' . $fila, $d['etiqueta']);
            foreach ($d['valores'] as $s => $v) {
                $hoja->setCellValue([2 + $s, $fila], $v);
                $hoja->getStyle([2 + $s, $fila])->getNumberFormat()->setFormatCode($g['unidad'] === '%' ? '0.0" %"' : '#,##0.0');
            }
            $fila++;
        }
        $ultima = $fila - 1;
        $conCasos = count(array_filter($g['datos'], static fn ($d) => $d['n'] !== null)) === count($g['datos']);
        if ($conCasos) {
            $hoja->setCellValue([2 + $nSeries, 3], 'Casos');
            foreach ($g['datos'] as $i => $d) {
                $hoja->setCellValue([2 + $nSeries, 4 + $i], $d['n']);
            }
        }
        self::estiloEncabezado($hoja, 'A3:' . chr(65 + $nSeries + ($conCasos ? 1 : 0)) . '3', $t);
        $hoja->getColumnDimension('A')->setWidth(48);
        for ($s = 0; $s < $nSeries; $s++) {
            $hoja->getColumnDimension(Coordinate::stringFromColumnIndex(2 + $s))->setWidth(16);
        }
        if ($p['analisis']) {
            $hoja->setCellValue('A' . ($ultima + 2), 'Análisis');
            $hoja->getStyle('A' . ($ultima + 2))->getFont()->setBold(true);
            $hoja->setCellValue('A' . ($ultima + 3), implode("\n\n", $p['analisis']));
            $hoja->mergeCells('A' . ($ultima + 3) . ':F' . ($ultima + 3));
            $hoja->getStyle('A' . ($ultima + 3))->getAlignment()->setWrapText(true)->setVertical(XAlign::VERTICAL_TOP);
            $hoja->getRowDimension($ultima + 3)->setRowHeight(150);
        }
        if ($p['nota'] !== '') {
            $hoja->setCellValue('A' . ($ultima + 5), $p['nota']);
            $hoja->getStyle('A' . ($ultima + 5))->getFont()->setItalic(true);
        }

        $q = "'" . str_replace("'", "''", $nombre) . "'";
        $etiquetas = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, $q . '!$A$4:$A$' . $ultima, null, $ultima - 3)];
        $valores = [];
        $nombres = [];
        for ($s = 0; $s < $nSeries; $s++) {
            $col = chr(66 + $s);
            $nombres[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, $q . '!$' . $col . '$3', null, 1);
            $valores[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, $q . '!$' . $col . '$4:$' . $col . '$' . $ultima, null, $ultima - 3);
        }
        $esTorta = in_array($g['tipo'], ['torta', 'dona'], true);
        if ($esTorta) {
            $serie = new DataSeries(DataSeries::TYPE_DONUTCHART, null, range(0, count($valores) - 1), $nombres, $etiquetas, $valores);
            if ($g['tipo'] === 'torta') {
                $serie = new DataSeries(DataSeries::TYPE_PIECHART, null, range(0, count($valores) - 1), $nombres, $etiquetas, $valores);
            }
        } else {
            $serie = new DataSeries(DataSeries::TYPE_BARCHART, DataSeries::GROUPING_CLUSTERED, range(0, count($valores) - 1), $nombres, $etiquetas, $valores);
            $serie->setPlotDirection($g['tipo'] === 'barras_v' ? DataSeries::DIRECTION_COL : DataSeries::DIRECTION_BAR);
        }
        $area = new PlotArea(null, [$serie]);
        $grafico = new Chart('g' . md5($nombre), new Title($g['titulo'] !== '' ? $g['titulo'] : $p['titulo']), new Legend(Legend::POSITION_BOTTOM, null, false), $area);
        $grafico->setTopLeftPosition('H3');
        $grafico->setBottomRightPosition('Q24');
        $hoja->addChart($grafico);
    }

    private static function hojaTabla(Spreadsheet $libro, string $nombre, array $b, array $t, string $titulo): void
    {
        $hoja = $libro->createSheet();
        $hoja->setTitle($nombre);
        $fila = 1;
        if ($titulo !== '') {
            $hoja->setCellValue('A1', $titulo);
            $hoja->getStyle('A1')->getFont()->setBold(true)->setSize(12);
            $fila = 3;
        }
        $nCols = max(count($b['encabezado']), $b['filas'] ? max(array_map('count', $b['filas'])) : 0);
        if ($b['encabezado']) {
            foreach ($b['encabezado'] as $c => $v) {
                $hoja->setCellValue([$c + 1, $fila], $v);
            }
            self::estiloEncabezado($hoja, 'A' . $fila . ':' . chr(64 + max(1, $nCols)) . $fila, $t);
            $fila++;
        }
        foreach ($b['filas'] as $f) {
            foreach ($f as $c => $v) {
                $limpio = str_replace([' %', '%', ','], ['', '', '.'], (string) $v);
                if (is_numeric($limpio) && preg_match('/^[\d.,\s%+-]+$/', (string) $v)) {
                    $hoja->setCellValue([$c + 1, $fila], (float) $limpio);
                } else {
                    $hoja->setCellValue([$c + 1, $fila], $v);
                }
            }
            $fila++;
        }
        for ($c = 1; $c <= $nCols; $c++) {
            $hoja->getColumnDimension(Coordinate::stringFromColumnIndex($c))->setAutoSize(true);
        }
    }
}
