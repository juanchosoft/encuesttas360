<?php
session_start();

require_once __DIR__ . '/../classes/DbConection.php';
require_once __DIR__ . '/../classes/Util.php';
require_once __DIR__ . '/../classes/PermissionCatalog.php';
require_once __DIR__ . '/../classes/SessionData.php';
require_once __DIR__ . '/../classes/MapaPuntosCuestionario.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

if (!SessionData::getPermission(74)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'No tienes permiso para ver este mapa.']);
    exit;
}
session_write_close();

try {
    $res = MapaPuntosCuestionario::obtener($_POST + $_GET);
} catch (Throwable $e) {
    $res = ['success' => false, 'message' => 'No se pudo cargar el mapa del municipio.'];
}
echo json_encode($res, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
