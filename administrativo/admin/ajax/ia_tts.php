<?php
session_start();
set_time_limit(60);

require_once __DIR__ . '/../classes/DbConection.php';
require_once __DIR__ . '/../classes/Util.php';
require_once __DIR__ . '/../classes/PermissionCatalog.php';
require_once __DIR__ . '/../classes/SessionData.php';
require_once __DIR__ . '/../classes/ia/ClaudeService.php';
require_once __DIR__ . '/../classes/ia/IaToolRegistry.php';
require_once __DIR__ . '/../classes/ia/AsistenteIA.php';
require_once __DIR__ . '/../classes/ia/ElevenLabsService.php';

if (!SessionData::hasPermission('ia.voz.use')) {
    http_response_code(403);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['output' => ['valid' => false, 'error' => 'permiso_denegado', 'message' => 'No tienes permiso para usar el modo de voz.']]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['output' => ['valid' => false, 'message' => 'Método no permitido.']]);
    exit;
}

$mensajeId = isset($_POST['mensaje_id']) ? (int) $_POST['mensaje_id'] : 0;
if ($mensajeId <= 0) {
    http_response_code(400);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['output' => ['valid' => false, 'message' => 'mensaje_id inválido.']]);
    exit;
}

$asistente = new AsistenteIA(
    (int) SessionData::getUserId(),
    SessionData::getPermissionKeys(),
    SessionData::superAdministrador()
);

$texto = $asistente->obtenerTextoMensajeAsistente($mensajeId, (int) SessionData::getUserId());
if ($texto === null) {
    http_response_code(404);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['output' => ['valid' => false, 'message' => 'Mensaje no encontrado.']]);
    exit;
}

try {
    $audioMp3 = ElevenLabsService::sintetizar(ElevenLabsService::limpiarMarkdown($texto));
} catch (RuntimeException $e) {
    http_response_code(502);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['output' => ['valid' => false, 'error' => 'error_tts', 'message' => $e->getMessage()]]);
    exit;
}

header('Content-Type: audio/mpeg');
header('Content-Length: ' . strlen($audioMp3));
echo $audioMp3;
