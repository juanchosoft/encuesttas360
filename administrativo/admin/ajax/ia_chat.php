<?php
session_start();
set_time_limit(120);

require_once __DIR__ . '/../classes/DbConection.php';
require_once __DIR__ . '/../classes/Util.php';
require_once __DIR__ . '/../classes/PermissionCatalog.php';
require_once __DIR__ . '/../classes/SessionData.php';
require_once __DIR__ . '/../classes/ia/ClaudeService.php';
require_once __DIR__ . '/../classes/ia/IaToolRegistry.php';
require_once __DIR__ . '/../classes/ia/AsistenteIA.php';

header('Content-Type: application/json; charset=utf-8');

if (!SessionData::hasPermission('ia.asistente.use')) {
    http_response_code(403);
    echo json_encode(['output' => ['valid' => false, 'error' => 'permiso_denegado', 'message' => 'No tienes permiso para usar el asistente IA.']]);
    exit;
}

$mensaje = trim((string) ($_POST['mensaje'] ?? ''));
$conversacionId = isset($_POST['conversacion_id']) && $_POST['conversacion_id'] !== '' ? (int) $_POST['conversacion_id'] : null;

$asistente = new AsistenteIA(
    (int) SessionData::getUserId(),
    SessionData::getPermissionKeys(),
    SessionData::superAdministrador()
);

$resultado = $asistente->enviarMensaje($conversacionId, $mensaje);

echo json_encode(['output' => ['valid' => $resultado['valid'], 'response' => $resultado]], JSON_UNESCAPED_UNICODE);
