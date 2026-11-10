<?php
session_start();

require_once __DIR__ . '/../classes/DbConection.php';
require_once __DIR__ . '/../classes/Util.php';
require_once __DIR__ . '/../classes/PermissionCatalog.php';
require_once __DIR__ . '/../classes/SessionData.php';
require_once __DIR__ . '/../classes/ia/IaTrabajos.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

if (!SessionData::hasPermission('ia.asistente.use')) {
    http_response_code(403);
    echo json_encode(['output' => ['valid' => false, 'error' => 'permiso_denegado']]);
    exit;
}

$userId = (int) SessionData::getUserId();
session_write_close();

$trabajo = IaTrabajos::leer((string) ($_GET['trabajo'] ?? ''));
if ($trabajo === null || (int) ($trabajo['usuario'] ?? 0) !== $userId) {
    echo json_encode(['output' => ['valid' => false, 'response' => ['estado' => 'no_encontrado']]]);
    exit;
}

$salida = ['estado' => $trabajo['estado']];
if ($trabajo['estado'] !== 'ejecutando') {
    $salida['respuesta'] = $trabajo['respuesta'] ?? null;
}
echo json_encode(['output' => ['valid' => true, 'response' => $salida]], JSON_UNESCAPED_UNICODE);
