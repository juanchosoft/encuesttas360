<?php
session_start();

require_once __DIR__ . '/../classes/DbConection.php';
require_once __DIR__ . '/../classes/Util.php';
require_once __DIR__ . '/../classes/PermissionCatalog.php';
require_once __DIR__ . '/../classes/SessionData.php';
require_once __DIR__ . '/../classes/InformeIA.php';

if (!SessionData::hasPermission('ia.informes.view')) {
    http_response_code(403);
    exit('No autorizado.');
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$informe = $id > 0 ? InformeIA::cargar($id) : null;

if ($informe === null || !InformeIA::puedeAcceder($informe)) {
    http_response_code(404);
    exit('Informe no encontrado.');
}

if (InformeIA::esPresentacion($informe)) {
    header('Location: ia_informe_export.php?id=' . $id . '&formato=pdf' . (isset($_GET['descargar']) ? '&descargar=1' : ''));
    exit;
}

require_once __DIR__ . '/../include/TCPDF-main/tcpdf.php';

$marca = InformeIA::marca();

$pdf = new class('L', 'mm', 'A4', true, 'UTF-8', false) extends TCPDF {
    public string $tituloInforme = '';
    public string $nombreMarca = '';
    public string $logoMarca = '';

    public function Header()
    {
        if ($this->page === 1) {
            return;
        }
        $this->SetFillColor(0x0b, 0x1f, 0x43);
        $this->Rect(0, 0, $this->getPageWidth(), 3, 'F');
        $this->SetFillColor(0x2f, 0x67, 0xc4);
        $this->Rect(0, 3, 40, 1, 'F');
    }

    public function Footer()
    {
        if ($this->page === 1) {
            return;
        }
        $y = $this->getPageHeight() - 11;
        $this->SetDrawColor(0xd0, 0xd7, 0xe3);
        $this->SetLineWidth(0.2);
        $this->Line(12, $y - 1.5, $this->getPageWidth() - 12, $y - 1.5);
        $this->SetFont('aptos', '', 9);
        $this->SetTextColor(0x33, 0x3f, 0x4f);
        $this->SetXY(12, $y);
        $this->Cell(($this->getPageWidth() - 24) * 0.7, 5, $this->nombreMarca . '  ·  ' . $this->tituloInforme, 0, 0, 'L');
        $this->Cell(($this->getPageWidth() - 24) * 0.3, 5, 'Página ' . $this->getAliasNumPage() . ' de ' . $this->getAliasNbPages(), 0, 0, 'R');
    }
};

$dirFuentes = __DIR__ . '/../include/TCPDF-main/fonts/';
$pdf->AddFont('aptos', '', $dirFuentes . 'aptos.php');
$pdf->AddFont('aptos', 'B', $dirFuentes . 'aptosb.php');
$pdf->AddFont('aptos', 'I', $dirFuentes . 'aptosi.php');
$pdf->AddFont('aptos', 'BI', $dirFuentes . 'aptosbi.php');
foreach (['', 'B'] as $estiloFuente) {
    $pdf->AddFont('montserrat', $estiloFuente, $dirFuentes . 'montserratb.php');
    $pdf->AddFont('montserratsemib', $estiloFuente, $dirFuentes . 'montserratsemib.php');
}
$pdf->setCellHeightRatio(1.15);

$pdf->tituloInforme = $informe['titulo'];
$pdf->nombreMarca = $marca['nombre'];
$pdf->logoMarca = $marca['logo'];

$pdf->SetCreator($marca['nombre']);
$pdf->SetAuthor($marca['nombre']);
$pdf->SetTitle($informe['titulo']);
$pdf->SetMargins(12, 12, 12);
$pdf->SetHeaderMargin(0);
$pdf->SetFooterMargin(12);
$pdf->SetAutoPageBreak(false);

$pdf->setPrintHeader(true);
$pdf->setPrintFooter(true);
$pdf->AddPage();
$ancho = $pdf->getPageWidth();
$alto = $pdf->getPageHeight();
$pdf->SetFillColor(0x0b, 0x1f, 0x43);
$pdf->Rect(0, 0, $ancho, $alto, 'F');
$pdf->SetFillColor(0x2f, 0x67, 0xc4);
$pdf->Rect(0, 0, 9, $alto, 'F');
$pdf->SetFillColor(0x14, 0xb8, 0xc6);
$pdf->Rect(9, 0, 2, $alto, 'F');

$pdf->SetTextColor(0xa9, 0xc7, 0xff);
$pdf->SetFont('aptos', 'B', 11);
$pdf->SetXY(30, 38);
$pdf->Cell($ancho - 60, 6, 'ESTUDIO DE OPINIÓN', 0, 1, 'L');

$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('montserrat', 'B', 20);
$pdf->SetXY(30, 50);
$pdf->MultiCell($ancho - 70, 9, mb_strtoupper($informe['titulo']), 0, 'L', false, 1);

$ySub = $pdf->GetY() + 6;
if (!empty($informe['subtitulo'])) {
    $pdf->SetTextColor(0xdc, 0xe7, 0xfb);
    $pdf->SetFont('montserratsemib', '', 13);
    $pdf->SetXY(30, $ySub);
    $pdf->MultiCell($ancho - 70, 7, $informe['subtitulo'], 0, 'L', false, 1);
    $ySub = $pdf->GetY() + 3;
}
$pdf->SetTextColor(0x8f, 0xb2, 0xf0);
$pdf->SetFont('aptos', '', 11);
$pdf->SetXY(30, $ySub);
$pdf->Cell($ancho - 60, 7, ucfirst(InformeIA::mesAnio($informe['dt_create'])), 0, 1, 'L');

$pdf->SetFillColor(0x2f, 0x67, 0xc4);
$pdf->Rect(30, $alto - 62, 34, 0.9, 'F');
$pdf->SetTextColor(0xa9, 0xc7, 0xff);
$pdf->SetFont('aptos', '', 9);
$pdf->SetXY(30, $alto - 57);
$pdf->Cell(60, 5, 'Realizado por', 0, 1, 'L');
$xTexto = 30;
if ($marca['logo'] !== '') {
    $pdf->SetFillColor(255, 255, 255);
    $pdf->RoundedRect(30, $alto - 50, 26, 26, 3, '1111', 'F');
    $pdf->Image($marca['logo'], 31.5, $alto - 48.5, 23, 23, '', '', '', true, 300, '', false, false, 0, 'CM', false, false);
    $xTexto = 62;
}
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('montserratsemib', '', 13);
$pdf->SetXY($xTexto, $alto - 41);
$pdf->Cell(180, 8, $marca['nombre'], 0, 1, 'L');

$pdf->SetTextColor(0, 0, 0);
$pdf->SetAutoPageBreak(true, 18);

/*
 * CSS del PDF: solo selectores de etiqueta simples o de UNA sola clase
 * (".s360-x"), nunca "etiqueta.clase" ni combinadores descendientes — el
 * motor CSS de TCPDF no los resuelve de forma confiable y las reglas se
 * pierden en silencio.
 */
$estilo = '<style>'
    . 'table{border-collapse:collapse;width:100%;margin-bottom:4mm;border-top:none;border-right:none;border-bottom:none;border-left:none;font-family:aptos;}'
    . 'th{background-color:#0b1f43;color:#ffffff;padding:2mm;text-align:left;font-family:aptos;font-size:10pt;line-height:1.15;border-top:none;border-right:none;border-bottom:none;border-left:none;}'
    . 'td{padding:2mm;border-top:none;border-right:none;border-bottom:0.2mm solid #cccccc;border-left:none;font-family:aptos;font-size:10pt;line-height:1.15;}'
    . 'h1{color:#0b1f43;font-family:montserrat;font-size:20pt;line-height:1.15;margin-bottom:1mm;}'
    . 'h2{color:#0b1f43;font-family:montserratsemib;font-size:13pt;line-height:1.15;margin-top:5mm;border-top:none;border-right:none;border-left:none;border-bottom:0.3mm solid #d9e2ef;padding-bottom:1mm;}'
    . 'h3{color:#333f4f;font-family:montserratsemib;font-size:13pt;line-height:1.15;margin-top:4mm;}'
    . 'h4{color:#333f4f;font-family:aptos;font-size:11pt;line-height:1.15;}'
    . 'p{font-family:aptos;font-size:11pt;line-height:1.15;color:#333f4f;}'
    . 'div{font-family:aptos;}'
    . 'ul{font-family:aptos;font-size:11pt;line-height:1.15;color:#333f4f;}'
    . 'ol{font-family:aptos;font-size:11pt;line-height:1.15;color:#333f4f;}'
    . 'li{font-family:aptos;font-size:11pt;line-height:1.15;color:#333f4f;}'
    . 'small{color:#333f4f;font-family:aptos;font-size:9pt;}'
    . '.s360-table-highlight{border-top:0.4mm solid #20427f;border-right:0.4mm solid #20427f;border-bottom:0.4mm solid #20427f;border-left:0.4mm solid #20427f;}'
    . '.s360-kpis{border-top:none;border-right:none;border-bottom:none;border-left:none;}'
    . '.s360-kpi{background-color:#eaf1ff;border-top:0.3mm solid #b9d0f5;border-right:0.3mm solid #b9d0f5;border-bottom:0.3mm solid #b9d0f5;border-left:0.3mm solid #b9d0f5;text-align:center;padding:5mm 2mm;}'
    . '.s360-kpi-value{color:#0b1f43;font-family:montserrat;font-size:20pt;font-weight:bold;}'
    . '.s360-kpi-label{color:#333f4f;font-family:aptos;font-size:9pt;font-weight:bold;margin-top:1.5mm;}'
    . '.s360-callout-info{background-color:#eff6ff;border-top:none;border-right:none;border-bottom:none;border-left:1.2mm solid #3b82f6;color:#1e3a5f;padding:3mm 4mm;margin:3mm 0;font-size:10pt;line-height:1.15;}'
    . '.s360-callout-warning{background-color:#fff8e6;border-top:none;border-right:none;border-bottom:none;border-left:1.2mm solid #f5a623;color:#6b4a05;padding:3mm 4mm;margin:3mm 0;font-size:10pt;line-height:1.15;}'
    . '.s360-callout-danger{background-color:#fdeeee;border-top:none;border-right:none;border-bottom:none;border-left:1.2mm solid #e5484d;color:#7a1b1e;padding:3mm 4mm;margin:3mm 0;font-size:10pt;line-height:1.15;}'
    . '.s360-callout-success{background-color:#eafbf3;border-top:none;border-right:none;border-bottom:none;border-left:1.2mm solid #12b981;color:#0b5b3e;padding:3mm 4mm;margin:3mm 0;font-size:10pt;line-height:1.15;}'
    . '.s360-badge-dato{background-color:#e7f7ef;color:#0b7a45;font-size:9pt;padding:0.5mm 1.5mm;}'
    . '.s360-badge-interpretacion{background-color:#eef1ff;color:#3b49c7;font-size:9pt;padding:0.5mm 1.5mm;}'
    . '.s360-quote{background-color:#0b1f43;color:#eaf1ff;border-top:none;border-right:none;border-bottom:none;border-left:none;padding:4mm 5mm;margin-top:5mm;font-size:11pt;line-height:1.15;}'
    . '</style>';

$segmentos = [];
$ficha = !empty($informe['ficha_tecnica_id']) ? InformeIA::htmlFichaTecnica((int) $informe['ficha_tecnica_id']) : '';
$todo = $ficha . $informe['contenido_html'];
foreach (preg_split('/<!--S360(?:PAGINA|FIN)-->/', $todo) as $trozo) {
    if (trim(strip_tags($trozo, '<img><table>')) !== '') {
        $segmentos[] = $trozo;
    }
}

foreach ($segmentos as $i => $trozo) {
    $pdf->AddPage();
    $pdf->SetFont('aptos', '', 11);
    $pdf->SetTextColor(0x33, 0x3f, 0x4f);
    $trozo = preg_replace('/<table(?![^>]*cellpadding)/i', '<table cellpadding="4"', $trozo);
    $pdf->writeHTML($estilo . $trozo, true, false, true, false, '');
}

$nombreDescarga = 'informe-' . $informe['id'] . '.pdf';
$pdf->Output($nombreDescarga, 'I');
