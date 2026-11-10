<?php
session_start();
set_time_limit(180);
ini_set('memory_limit', '768M');

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
$formato = strtolower((string) ($_GET['formato'] ?? 'pdf'));
$informe = $id > 0 ? InformeIA::cargar($id) : null;

if ($informe === null || !InformeIA::puedeAcceder($informe) || !InformeIA::esPresentacion($informe) || !in_array($formato, ['pdf', 'pptx', 'xlsx'], true)) {
    http_response_code(404);
    exit('Informe no encontrado.');
}

require_once __DIR__ . '/../classes/InformeSpec.php';
require_once __DIR__ . '/../classes/InformeExport.php';

$spec = InformeSpec::compilar($informe, InformeIA::opciones($informe), (int) $informe['tbl_usuario_id'], false);

$nombre = 'informe-' . $informe['id'];
$limpio = trim(preg_replace('/[^a-z0-9]+/', '-', strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', (string) $informe['titulo']))), '-');
if ($limpio !== '') {
    $nombre = mb_substr($limpio, 0, 60);
}

if ($formato === 'xlsx') {
    $bytes = InformeExport::xlsx($spec);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $nombre . '.xlsx"');
} else {
    $laminas = InformeLaminas::construir($spec);
    if ($formato === 'pptx') {
        $bytes = InformeExport::pptx($laminas, $spec);
        header('Content-Type: application/vnd.openxmlformats-officedocument.presentationml.presentation');
        header('Content-Disposition: attachment; filename="' . $nombre . '.pptx"');
    } else {
        $bytes = InformeExport::pdf($laminas, $spec);
        header('Content-Type: application/pdf');
        header('Content-Disposition: ' . (isset($_GET['descargar']) ? 'attachment' : 'inline') . '; filename="' . $nombre . '.pdf"');
    }
}
header('Content-Length: ' . strlen($bytes));
header('Cache-Control: private, max-age=0, must-revalidate');
echo $bytes;
