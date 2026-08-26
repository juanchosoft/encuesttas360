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

require_once __DIR__ . '/../include/TCPDF-main/tcpdf.php';

$pdf = new class('P', 'mm', 'A4', true, 'UTF-8', false) extends TCPDF {
    public string $tituloInforme = '';
    public string $fechaInforme = '';

    public function Header()
    {
        $this->SetFillColor(0x1a, 0x3a, 0x5c);
        $this->Rect(0, 0, $this->getPageWidth(), 24, 'F');
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('helvetica', 'B', 13);
        $this->SetXY(12, 6);
        $this->Cell($this->getPageWidth() - 24, 6, 'Estadísticas 360 · Informe generado por Yamil', 0, 2, 'L');
        $this->SetFont('helvetica', '', 9);
        $this->Cell($this->getPageWidth() - 24, 5, $this->tituloInforme . ' — ' . $this->fechaInforme, 0, 0, 'L');
        $this->SetTextColor(0, 0, 0);
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('helvetica', '', 7);
        $this->SetTextColor(120, 120, 120);
        $this->Cell(0, 5, 'Generado automáticamente — uso interno', 0, 0, 'L');
        $this->Cell(0, 5, 'Página ' . $this->getAliasNumPage() . ' de ' . $this->getAliasNbPages(), 0, 0, 'R');
    }
};

$pdf->tituloInforme = $informe['titulo'];
$pdf->fechaInforme = date('d/m/Y H:i', strtotime($informe['dt_create']));

$pdf->SetCreator('Yamil');
$pdf->SetTitle($informe['titulo']);
$pdf->SetMargins(12, 32, 12);
$pdf->SetHeaderMargin(8);
$pdf->SetFooterMargin(12);
$pdf->SetAutoPageBreak(true, 20);
$pdf->setPrintHeader(true);
$pdf->setPrintFooter(true);
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 10);

/*
 * CSS del PDF: solo selectores de etiqueta simples o de UNA sola clase
 * (".s360-x"), nunca "etiqueta.clase" ni combinadores descendientes — el
 * motor CSS de TCPDF no los resuelve de forma confiable y las reglas se
 * pierden en silencio.
 */
$estilo = '<style>'
    . 'table{border-collapse:collapse;width:100%;margin-bottom:4mm;border-top:none;border-right:none;border-bottom:none;border-left:none;}'
    . 'th{background-color:#1a3a5c;color:#ffffff;padding:2mm;text-align:left;font-size:9.5pt;border-top:none;border-right:none;border-bottom:none;border-left:none;}'
    . 'td{padding:2mm;border-top:none;border-right:none;border-bottom:0.2mm solid #cccccc;border-left:none;font-size:9.5pt;}'
    . 'h1{color:#1a3a5c;font-size:17pt;margin-bottom:1mm;}'
    . 'h2{color:#1a3a5c;font-size:14pt;margin-top:5mm;border-top:none;border-right:none;border-left:none;border-bottom:0.3mm solid #d9e2ef;padding-bottom:1mm;}'
    . 'h3{color:#2f67c4;font-size:12pt;margin-top:4mm;}'
    . 'h4{color:#375c99;font-size:10.5pt;}'
    . 'p{font-size:10pt;line-height:1.5;}'
    . 'ul,ol{font-size:10pt;line-height:1.5;}'
    . 'small{color:#8a97ab;font-size:8pt;}'
    . '.s360-table-highlight{border-top:0.4mm solid #20427f;border-right:0.4mm solid #20427f;border-bottom:0.4mm solid #20427f;border-left:0.4mm solid #20427f;}'
    . '.s360-kpis{border-top:none;border-right:none;border-bottom:none;border-left:none;}'
    . '.s360-kpi{background-color:#eaf1ff;border-top:0.3mm solid #b9d0f5;border-right:0.3mm solid #b9d0f5;border-bottom:0.3mm solid #b9d0f5;border-left:0.3mm solid #b9d0f5;text-align:center;padding:5mm 2mm;}'
    . '.s360-kpi-value{color:#20427f;font-size:17pt;font-weight:bold;}'
    . '.s360-kpi-label{color:#3a4a68;font-size:8pt;font-weight:bold;margin-top:1.5mm;}'
    . '.s360-callout-info{background-color:#eff6ff;border-top:none;border-right:none;border-bottom:none;border-left:1.2mm solid #3b82f6;color:#1e3a5f;padding:3mm 4mm;margin:3mm 0;font-size:9.5pt;line-height:1.45;}'
    . '.s360-callout-warning{background-color:#fff8e6;border-top:none;border-right:none;border-bottom:none;border-left:1.2mm solid #f5a623;color:#6b4a05;padding:3mm 4mm;margin:3mm 0;font-size:9.5pt;line-height:1.45;}'
    . '.s360-callout-danger{background-color:#fdeeee;border-top:none;border-right:none;border-bottom:none;border-left:1.2mm solid #e5484d;color:#7a1b1e;padding:3mm 4mm;margin:3mm 0;font-size:9.5pt;line-height:1.45;}'
    . '.s360-callout-success{background-color:#eafbf3;border-top:none;border-right:none;border-bottom:none;border-left:1.2mm solid #12b981;color:#0b5b3e;padding:3mm 4mm;margin:3mm 0;font-size:9.5pt;line-height:1.45;}'
    . '.s360-badge-dato{background-color:#e7f7ef;color:#0b7a45;font-size:7pt;padding:0.5mm 1.5mm;}'
    . '.s360-badge-interpretacion{background-color:#eef1ff;color:#3b49c7;font-size:7pt;padding:0.5mm 1.5mm;}'
    . '.s360-quote{background-color:#0b1f43;color:#eaf1ff;border-top:none;border-right:none;border-bottom:none;border-left:none;padding:4mm 5mm;margin-top:5mm;font-size:10.5pt;line-height:1.5;}'
    . '</style>';

$pdf->writeHTML($estilo . '<h1>' . htmlspecialchars($informe['titulo'], ENT_QUOTES, 'UTF-8') . '</h1>' . $informe['contenido_html'], true, false, true, false, '');

$nombreDescarga = 'informe-' . $informe['id'] . '.pdf';
$pdf->Output($nombreDescarga, 'I');
